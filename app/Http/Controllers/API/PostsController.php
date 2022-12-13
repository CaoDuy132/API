<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatePostsM;
use App\Models\PostsM;
use App\Models\ProductM;
use App\Models\TagBlog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //  ====================== GET DATE CLIENT =================================
    public function allBlogClient()
    {
        $result = PostsM::with('cate_posts', 'tags')->where('statusPosts', '=', 1)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->take(12)->get();
        $count = PostsM::with('cate_posts', 'tags')->where('statusPosts', '=', 1)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->count();
        return response()->json(['result' => $result, 'count' => $count]);
    }

    public function viewMoreBlog($id)
    {
        $result = PostsM::with('cate_posts', 'tags')->where(function ($query) use ($id) {
            $query->where('id', '<', $id);
            $query->where('statusPosts', '=', 1);
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->take(12)->get();
        $count = PostsM::with('cate_posts', 'tags')->where(function ($query) use ($id) {
            $query->where('id', '<', $id);
            $query->where('statusPosts', '=', 1);
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->count();
        return response()->json(['result' => $result, 'count' => $count]);
    }

    public function viewMoreBlogNormal($id)
    {
        $result = [];
        $max = PostsM::with('cate_posts', 'tags')->where(function ($query) use ($id) {
            $query->where('id', '>=', $id);
            $query->where('statusPosts', '=', 1);
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();
        $countMax = PostsM::with('cate_posts', 'tags')->where(function ($query) use ($id) {
            $query->where('id', '>=', $id);
            $query->where('statusPosts', '=', 1);
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->count();


        $min = PostsM::with('cate_posts', 'tags')->where(function ($query) use ($id) {
            $query->where('id', '<', $id);
            $query->where('statusPosts', '=', 1);
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->take(12)->get();
        $countMin = PostsM::with('cate_posts', 'tags')->where(function ($query) use ($id) {
            $query->where('id', '<', $id);
            $query->where('statusPosts', '=', 1);
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->count();

        foreach ($max as $key => $value) {
            $result[] = $value;
        }
        foreach ($min as $key => $value) {
            $result[] = $value;
        }

        return response()->json(['result' => $result, 'count' => $countMin + $countMax, 'countMin' => $countMin]);
    }

    public function getDetail(Request $request)
    {
        $slugCate = $request->slug_cate;
        $slugTitle = $request->slug_title;
        $data = PostsM::with('cate_posts', 'tags')->whereHas('cate_posts', function ($query) use ($slugCate) {
            $query->where('slugCatePost', '=', $slugCate);
        })->where('slugPosts', '=', $slugTitle)->first();
        $viewCurrent = $data->viewPosts;
        $data->update([
            'viewPosts' => $viewCurrent + 1,
        ]);
        // #############################
        $prodRelated = PostsM::find($data->id)->products()->orderBy('discount', 'desc')->get();
        // #############################
        $relatedBlog = PostsM::with('cate_posts', 'tags')->whereHas('cate_posts', function ($query) use ($slugCate) {
            $query->where('slugCatePost', '=', $slugCate);
        })->where('id', '!=', $data->id)->where('statusPosts', '=', 1)->orderBy('created_at', 'desc')->take(4)->get();
        //  #############################
        if (!$data) {
            return response()->json(['status' => 404, 'msg' => null, 'related' => null, 'prods' => null]);
        } else {
            return response()->json(['status' => 200, 'msg' => $data, 'related' => $relatedBlog, 'prods' => $prodRelated]);
        }
    }

    public function allCateBlogClient()
    {
        $data = CatePostsM::with('posts')->orderBy('created_at', 'desc')->get();
        return response()->json($data);
    }

    public function getBlogByTag(Request $request)
    {
        $tag = $request->slug;
        $id = $request->id;

        $checkId = TagBlog::where('id', '=', $id)->count();
        $checkTag = TagBlog::Where('slugTagBlog', '=', $tag)->count();

        if ($checkId == 0 || $checkTag == 0) {
            return response()->json(['status' => 404]);
        }

        $dataTag = TagBlog::where(function ($query) use ($tag, $id) {
            $query->where('id', '=', $id);
            $query->where('slugTagBlog', '=', $tag);
        })->first();
        $nameTag  = $dataTag->nameTagBlog;
        $blogs = PostsM::with('cate_posts', 'tags')->whereHas('tags', function ($query) use ($nameTag) {
            $query->where('nameTagBlog', 'like', '%' . $nameTag . '%');
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->take(12)->get();
        $count = PostsM::with('cate_posts', 'tags')->whereHas('tags', function ($query) use ($nameTag) {
            $query->where('nameTagBlog', 'like', '%' . $nameTag . '%');
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->count();

        return response()->json(['status' => 200, 'tag' => $dataTag, 'blog' => $blogs, 'count' => $count]);
    }

    public function viewMoreBlogByTag($idTag, $id)
    {
        $dataTag = TagBlog::find($idTag);
        $nameTag = $dataTag->nameTagBlog;
        $blogs = PostsM::with('cate_posts', 'tags')->where('id', '<', $id)->whereHas('tags', function ($query) use ($nameTag) {
            $query->where('nameTagBlog', 'like', '%' . $nameTag . '%');
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->take(12)->get();
        $count = PostsM::with('cate_posts', 'tags')->where('id', '<', $id)->whereHas('tags', function ($query) use ($nameTag) {
            $query->where('nameTagBlog', 'like', '%' . $nameTag . '%');
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->count();

        return response()->json(['status' => 200, 'blog' => $blogs, 'count' => $count, 'tag' => $dataTag]);
    }


    public function viewMoreBlogByTagNormal($idTag, $id)
    {
        $result = [];
        $dataTag = TagBlog::find($idTag);
        $nameTag = $dataTag->nameTagBlog;

        $max = PostsM::with('cate_posts', 'tags')->where('id', '>=', $id)->whereHas('tags', function ($query) use ($nameTag) {
            $query->where('nameTagBlog', 'like', '%' . $nameTag . '%');
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();
        $countMax = PostsM::with('cate_posts', 'tags')->where('id', '>=', $id)->whereHas('tags', function ($query) use ($nameTag) {
            $query->where('nameTagBlog', 'like', '%' . $nameTag . '%');
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->count();

        $min = PostsM::with('cate_posts', 'tags')->where('id', '<', $id)->whereHas('tags', function ($query) use ($nameTag) {
            $query->where('nameTagBlog', 'like', '%' . $nameTag . '%');
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->take(12)->get();
        $countMin = PostsM::with('cate_posts', 'tags')->where('id', '<', $id)->whereHas('tags', function ($query) use ($nameTag) {
            $query->where('nameTagBlog', 'like', '%' . $nameTag . '%');
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->count();
        
        foreach ($max as $key => $value) {
            $result[] = $value;
        }
        foreach ($min as $key => $value) {
            $result[] = $value;
        }
        return response()->json(['status' => 200, 'result' => $result, 'count' => $countMin + $countMax, 'tag' => $dataTag , 'countMin' => $countMin]);
    }

    // ==========================================================================

    public function index()
    {
        $result = PostsM::with('cate_posts', 'tags')->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();
        return response()->json($result);
    }

    public function getAllProd()
    {
        $result = ProductM::orderBy('name', 'asc')->get();
        return response()->json($result);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'title' => 'required|min:5',
            'slug' => 'required',
            'summary' => 'required',
            'cate' => 'required',
            'content' => 'required',
        ], [
            'title.required' => 'Tiêu đề bắt buộc !',
            'title.min' => 'Tiêu đề ít nhất 5 ký tự !',
            'slug.required' => 'Vui lòng nhập slug !',
            'summary.required' => 'Vui lòng nhập tóm tắt !',
            'cate.required' => 'Vui lòng nhập chuyên mục !',
            'content.required' => 'Vui lòng nhập nội dung bài viết !',
        ]);
        if ($validation->fails()) {
            return response()->json(['status' => 202, 'msg' => $validation->errors()]);
        }
        $checkExist = PostsM::where('slugPosts', '=', $request->slug)->count();
        if ($checkExist > 0) {
            return response()->json(['status' => 204, 'msg' => 'Slug bài viết đã tồn tại vui lòng chọn tên khác !']);
        }
        if ($request->hasFile('file-image')) {
            $file = $request->file('file-image');
            $nameFile = uniqid() . time() . "-" . substr(md5($request->slug), 0, 10) . "." . $file->getClientOriginalExtension();
            $file->move('images/posts/', $nameFile);
            $insert = PostsM::create([
                'titlePosts' => trim(strip_tags($request->title)),
                'idcatePosts' => $request->cate,
                'slugPosts' => $request->slug,
                'summaryPosts' => trim(strip_tags($request->summary)),
                'imagePosts' => $nameFile,
                'contentPosts' => $request->content,
                'statusPosts' => $request->status,
                'author' => $request->author,
                'created_at' => now()
            ])->id;
            if (isset($request->tags)) {
                foreach (json_decode($request->tags) as $key => $value) {
                    TagBlog::create([
                        'id_blog' => $insert,
                        'nameTagBlog' => $value->value,
                        'slugTagBlog' => Str::slug($value->value),
                        'created_at' => now()
                    ]);
                }
            }
            if (count($request->prod) > 0) {
                foreach ($request->prod as $key => $value) {
                    DB::table('posts_prod')->insert([
                        'id_posts' => $insert,
                        'id_prod' => $value,
                        'created_at' => now()
                    ]);
                }
            }
            if ($insert) {
                return response()->json(['status' => 200, 'msg' => 'Thêm bài viết thành công !']);
            }
        } else {
            $insert = PostsM::create([
                'titlePosts' => trim(strip_tags($request->title)),
                'idcatePosts' => $request->cate,
                'slugPosts' => $request->slug,
                'summaryPosts' => trim(strip_tags($request->summary)),
                'contentPosts' => $request->content,
                'statusPosts' => $request->status,
                'author' => $request->author,
                'created_at' => now()
            ])->id;
            if (isset($request->tags)) {
                foreach (json_decode($request->tags) as $key => $value) {
                    TagBlog::create([
                        'id_blog' => $insert,
                        'nameTagBlog' => $value->value,
                        'slugTagBlog' => Str::slug($value->value),
                        'created_at' => now()
                    ]);
                }
            }
            if (count($request->prod) > 0) {
                foreach ($request->prod as $key => $value) {
                    DB::table('posts_prod')->insert([
                        'id_posts' => $insert,
                        'id_prod' => $value,
                        'created_at' => now()
                    ]);
                }
            }
            if ($insert) {
                return response()->json(['status' => 200, 'msg' => 'Thêm bài viết thành công !']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $slug_cate = $request->slug_cate;
        $slug_title = $request->slug_title;
        $result = PostsM::with('cate_posts', 'tags')->whereHas('cate_posts', function ($query) use ($slug_cate) {
            $query->where('slugCatePost', '=', $slug_cate);
        })->where('slugPosts', '=', $slug_title)->first();
        $prods = PostsM::find($result->id)->products;
        if ($result) {
            return response()->json(['status' => 200, 'msg' => $result, 'prods' => $prods]);
        } else {
            return response()->json(['status' => 404]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $update = PostsM::with('cate_posts', 'tags')->where('id', '=', $request->id)->first();
        $validation = Validator::make($request->all(), [
            'title' => 'required|min:5',
            'slug' => 'required',
            'summary' => 'required',
            'cate' => 'required',
            'content' => 'required',
        ], [
            'title.required' => 'Tiêu đề bắt buộc !',
            'title.min' => 'Tiêu đề ít nhất 5 ký tự !',
            'slug.required' => 'Vui lòng nhập slug !',
            'summary.required' => 'Vui lòng nhập tóm tắt !',
            'cate.required' => 'Vui lòng nhập chuyên mục !',
            'content.required' => 'Vui lòng nhập nội dung bài viết !',
        ]);
        if ($validation->fails()) {
            return response()->json(['status' => 202, 'msg' => $validation->errors()]);
        }
        $checkExist = PostsM::where('slugPosts', '=', $request->slug)->where('id', '!=', $request->id)->count();
        if ($checkExist > 0) {
            return response()->json(['status' => 204, 'msg' => 'Slug bài viết đã tồn tại vui lòng chọn tên khác !']);
        }



        if ($request->hasFile('file-image')) {
            // Bỏ hình củ
            if ($update->imagePosts != null) {
                $targetFile = public_path('images/posts/' . $update->imagePosts);
                if (file_exists($targetFile)) {
                    unlink($targetFile);
                }
            }
            // ==========
            $file = $request->file('file-image');
            $nameFile = uniqid() . time() . "-" . substr(md5($request->slug), 0, 10) . "." . $file->getClientOriginalExtension();
            $file->move('images/posts/', $nameFile);
            $updated =  $update->update([
                'titlePosts' => trim(strip_tags($request->title)),
                'idcatePosts' => $request->cate,
                'slugPosts' => $request->slug,
                'summaryPosts' => trim(strip_tags($request->summary)),
                'imagePosts' => $nameFile,
                'contentPosts' => $request->content,
                'statusPosts' => $request->status,
                'author' => $request->author,
                'updated_at' => now()
            ]);
            PostsM::find($request->id)->products()->sync($request->prod);
            if (isset($request->tags)) {
                foreach (json_decode($request->tags) as $key => $value) {
                    TagBlog::create([
                        'id_blog' => $request->id,
                        'nameTagBlog' => $value->value,
                        'slugTagBlog' => Str::slug($value->value),
                        'created_at' => now()
                    ]);
                }
            } else {
                $id = $request->id;
                TagBlog::with('posts')->whereHas('posts', function ($query) use ($id) {
                    $query->where('id', '=', $id);
                })->delete();
            }
            if ($updated) {
                return response()->json(['status' => 200, 'msg' => 'Cập nhật thành công !', 'url' => ['slug_cate' => CatePostsM::find($request->cate)->slugCatePost, 'slug_title' => $request->slug]]);
            }
        } else {
            $updated =  $update->update([
                'titlePosts' => trim(strip_tags($request->title)),
                'idcatePosts' => $request->cate,
                'slugPosts' => $request->slug,
                'summaryPosts' => trim(strip_tags($request->summary)),
                'contentPosts' => $request->content,
                'statusPosts' => $request->status,
                'author' => $request->author,
                'updated_at' => now()
            ]);
            PostsM::find($request->id)->products()->sync($request->prod);
            if (isset($request->tags)) {
                $id = $request->id;
                TagBlog::with('posts')->whereHas('posts', function ($query) use ($id) {
                    $query->where('id', '=', $id);
                })->delete();
                foreach (json_decode($request->tags) as $key => $value) {
                    TagBlog::create([
                        'id_blog' => $request->id,
                        'nameTagBlog' => $value->value,
                        'slugTagBlog' => Str::slug($value->value),
                        'created_at' => now()
                    ]);
                }
            } else {
                $id = $request->id;
                TagBlog::with('posts')->whereHas('posts', function ($query) use ($id) {
                    $query->where('id', '=', $id);
                })->delete();
            }
            if ($updated) {
                return response()->json(['status' => 200, 'msg' => 'Cập nhật thành công !', 'url' => ['slug_cate' => CatePostsM::find($request->cate)->slugCatePost, 'slug_title' => $request->slug]]);
            }
        }
    }


    public function deleteImageCover(Request $request)
    {
        $data = PostsM::find($request->id);
        if (file_exists(public_path('images/posts/' . $data->imagePosts))) {
            unlink(public_path('images/posts/' . $data->imagePosts));
        }
        $data->update([
            'imagePosts' => null
        ]);
        return response()->json(['status' => 200]);
    }
    public function deleteSoftManyItem(Request $request)
    {
        foreach ($request->arr as $key => $value) {
            PostsM::find($value)->delete();
        }
        return response()->json(['status' => 200]);
    }
    public function loadTrash()
    {
        $result = PostsM::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return response()->json($result);
    }
    public function restoreMany(Request $request)
    {
        foreach ($request->arr as $key => $value) {
            PostsM::withTrashed()->where('id', '=', $value)->restore();
        }

        return response()->json(['status' => 200]);
    }
    public function deleteSoftSingleItem(Request $request)
    {
        PostsM::find($request->id)->delete();
        return response()->json(['status' => 200]);
    }
    public function changeFastTitle(Request $request)
    {
        $checkExist = PostsM::where('slugPosts', '=', $request->slug)->where('id', '!=', $request->id)->count();
        if ($checkExist > 0) {
            return response()->json(['status' => 202, 'msg' => 'Bài viết đã tồn tại !']);
        }
        $data = PostsM::with('cate_posts')->where('id', '=', $request->id)->first();
        $update =  $data->update([
            'titlePosts' => trim($request->title),
            'slugPosts' => $request->slug
        ]);
        if ($update) {
            return response()->json(['status' => 200, 'id' => $request->id, 'title' => trim($data->titlePosts), 'slug_title' => $data->slugPosts, 'slug_cate' => $data->cate_posts->slugCatePost]);
        }
    }
    public function changeFastStatus(Request $request)
    {
        if ($request->act == 'hide') {
            PostsM::find($request->id)->update([
                'statusPosts' => 0
            ]);
        } elseif ($request->act == 'show') {
            PostsM::find($request->id)->update([
                'statusPosts' => 1
            ]);
        }
        return response()->json(['status' => 200]);
    }
    public function forceDeleteMany(Request $request)
    {
        foreach ($request->arr as $key => $value) {
            $data = PostsM::withTrashed()->where('id', '=', $value)->first();
            DB::table('posts_prod')->where('id_posts', '=', $value)->delete();
            DB::table('posts_tag')->where('id_blog', '=', $value)->delete();
            if ($data->imagePosts != null) {
                if (file_exists(public_path('images/posts/' . $data->imagePosts))) {
                    unlink(public_path('images/posts/' . $data->imagePosts));
                }
            }
            PostsM::withTrashed()->where('id', '=', $value)->forceDelete();
        }
        return response()->json(['status' => 200]);
    }

    public function restoreSingleItem(Request $request)
    {
        PostsM::withTrashed()->where('id', '=', $request->id)->restore();
        return response()->json(['status' => 200]);
    }


    public function forceDeleteSingleItem(Request $request)
    {
        $data = PostsM::withTrashed()->where('id', '=', $request->id)->first();
        DB::table('posts_prod')->where('id_posts', '=', $request->id)->delete();
        DB::table('posts_tag')->where('id_blog', '=', $request->id)->delete();
        if ($data->imagePosts != null) {
            if (file_exists(public_path('images/posts/' . $data->imagePosts))) {
                unlink(public_path('images/posts/' . $data->imagePosts));
            }
        }
        PostsM::withTrashed()->where('id', '=', $request->id)->forceDelete();
        return response()->json(['status' => 200]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

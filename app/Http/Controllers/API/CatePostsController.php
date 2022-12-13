<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatePostsM;
use App\Models\PostsM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\fileExists;

class CatePostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = CatePostsM::with('posts')->orderBy('created_at', 'desc')->get();
        return response()->json($result);
    }
    //  ============================ CATEGORIES BLOG CLIENT ===============================

    public function getBlogByCate(Request $request)
    {
        $slug_cate = $request->slug_cate;

        $detailCate = CatePostsM::where('slugCatePost', '=', $slug_cate)->first();

        $result = PostsM::with('cate_posts')->whereHas('cate_posts', function ($query) use ($slug_cate) {
            $query->where('slugCatePost', '=', $slug_cate);
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->take(12)->get();

        $count = PostsM::with('cate_posts')->whereHas('cate_posts', function ($query) use ($slug_cate) {
            $query->where('slugCatePost', '=', $slug_cate);
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->count();

        return response()->json(['count' => $count, 'result' => $result, 'detailC' => $detailCate]);
    }

    public function viewMoreBlogByCate($slug_cate,$id)
    {
        $result = PostsM::with('cate_posts')->where('id', '<', $id)->whereHas('cate_posts', function ($query) use ($slug_cate) {
            $query->where('slugCatePost', '=', $slug_cate);
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->take(12)->get();

        $count = PostsM::with('cate_posts')->where('id', '<', $id)->whereHas('cate_posts', function ($query) use ($slug_cate) {
            $query->where('slugCatePost', '=', $slug_cate);
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->count();

        return response()->json(['count' => $count, 'result' => $result]);
    }

    public function viewMoreBlogByCateNormal($slug_cate,$id)
    {
        $result = [];
        $max = PostsM::with('cate_posts')->where('id', '>=', $id)->whereHas('cate_posts', function ($query) use ($slug_cate) {
            $query->where('slugCatePost', '=', $slug_cate);
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();

        $countMax = PostsM::with('cate_posts')->where('id', '>=', $id)->whereHas('cate_posts', function ($query) use ($slug_cate) {
            $query->where('slugCatePost', '=', $slug_cate);
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->count();
        ##########################################
        $min = PostsM::with('cate_posts')->where('id', '<', $id)->whereHas('cate_posts', function ($query) use ($slug_cate) {
            $query->where('slugCatePost', '=', $slug_cate);
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->take(12)->get();

        $countMin = PostsM::with('cate_posts')->where('id', '<', $id)->whereHas('cate_posts', function ($query) use ($slug_cate) {
            $query->where('slugCatePost', '=', $slug_cate);
        })->orderBy('created_at', 'desc')->orderBy('id', 'desc')->count();
        ###########################################
        
        foreach ($max as $key => $value) {
            $result[] = $value;
        }
        foreach ($min as $key => $value) {
            $result[] = $value;
        }
        return response()->json(['count' => $countMax+$countMin, 'result' => $result,'countMin'=>$countMin]);
    }
    // ====================================================================================
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
            'nameCatePosts' => 'required',
            'slugCatePosts' => 'required',
        ], [
            'nameCatePosts.required' => 'Vui lòng nhập tên chuyên mục !',
            'slugCatePosts.required' => 'Vui lòng nhập slug chuyên mục !'
        ]);
        if ($validation->fails()) {
            return response()->json(['status' => 202, 'msg' => $validation->errors()]);
        } else {
            $checkExsit = CatePostsM::where('slugCatePost', '=', $request->slugCatePosts)->orWhere('nameCatePosts', '=', trim(strip_tags(ucfirst($request->nameCatePosts))))->count();
            if ($checkExsit > 0) {
                return response()->json(['status' => 204, 'msg' => 'Tên chuyên mục đã tồn tại vui lòng chọn tên khác !']);
            } else {
                if (isset($request->logo)) {
                    $folderPath = public_path('images/cate-posts/');
                    $image_parts = explode(";base64,", $request->logo);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    $nameFile = uniqid() . time() . "-" . substr(md5($request->slugCatePosts), 0, 20) . "." . $image_type;
                    $file = $folderPath . $nameFile;
                    file_put_contents($file, $image_base64);
                    CatePostsM::create([
                        'nameCatePosts' => trim(strip_tags(ucfirst($request->nameCatePosts))),
                        'slugCatePost' => $request->slugCatePosts,
                        'logo' => $nameFile,
                        'der' => trim(strip_tags($request->noteCatePosts)),
                        'created_at' => now()
                    ]);

                    return response()->json(['status' => 200, 'Thêm thành công !']);
                } else {
                    CatePostsM::create([
                        'nameCatePosts' => trim(strip_tags(ucfirst($request->nameCatePosts))),
                        'slugCatePost' => $request->slugCatePosts,
                        'der' => trim(strip_tags($request->noteCatePosts)),
                        'created_at' => now()
                    ]);
                    return response()->json(['status' => 200, 'Thêm thành công !']);
                }
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $validation = Validator::make($request->all(), [
            'nameCatePostsEdit' => 'required',
            'slugCatePostsEdit' => 'required',
        ], [
            'nameCatePostsEdit.required' => 'Vui lòng nhập tên chuyên mục !',
            'slugCatePostsEdit.required' => 'Vui lòng nhập slug chuyên mục !'
        ]);
        $name = trim(strip_tags(ucfirst($request->nameCatePostsEdit)));
        $slug = $request->slugCatePostsEdit;
        if ($validation->fails()) {
            return response()->json(['status' => 202, 'msg' => $validation->errors(), 'id' => $request->id]);
        } else {
            $checkExsits = CatePostsM::where('id', '!=', $request->id)->where(function ($query) use ($slug, $name) {
                $query->where('nameCatePosts', '=', $name);
                $query->orWhere('slugCatePost', '=', $slug);
            })->count();
            if ($checkExsits > 0) {
                return response()->json(['status' => 204, 'msg' => 'Tên chuyên mục đã tồn tại vui lòng chọn tên khác !', 'id' => $request->id]);
            } else {
                $data = CatePostsM::with('posts')->where('id', '=', $request->id)->first();
                if (isset($request->logo)) {
                    if ($data->logo != null) {
                        $target = public_path('images/cate-posts/' . $data->logo);
                        if (fileExists($target)) {
                            unlink($target);
                        }
                    }
                    $folderPath = public_path('images/cate-posts/');
                    $image_parts = explode(";base64,", $request->logo);
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1];
                    $image_base64 = base64_decode($image_parts[1]);
                    $nameFile = uniqid() . time() . "-" . substr(md5($request->slugCatePosts), 0, 20) . "." . $image_type;
                    $file = $folderPath . $nameFile;
                    file_put_contents($file, $image_base64);
                    CatePostsM::where('id', '=', $request->id)->update([
                        'nameCatePosts' => $name,
                        'slugCatePost' => $slug,
                        'logo' => $nameFile,
                        'der' => trim(strip_tags($request->noteCatePostsEdit)),
                        'updated_at' => now()
                    ]);
                    return response()->json(['status' => 200, 'msg' => 'Cập nhật thành công !', 'id' => $data->id]);
                } else {
                    CatePostsM::where('id', '=', $request->id)->update([
                        'nameCatePosts' => $name,
                        'slugCatePost' => $slug,
                        'der' => trim(strip_tags($request->noteCatePostsEdit)),
                        'updated_at' => now()
                    ]);
                    return response()->json(['status' => 200, 'msg' => 'Cập nhật thành công !', 'id' => $data->id]);
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $checkBeforeDelete = PostsM::where('idcatePosts', '=', $request->id)->count();
        if ($checkBeforeDelete > 0) {
            return response()->json(['status' => 202, 'msg' => 'Không thể xóa chuyên mục đang có bài viết !']);
        }

        $data = CatePostsM::find($request->id);
        if ($data->logo != null) {
            $target_file = public_path('images/cate-posts/' . $data->logo);
            if (file_exists($target_file)) {
                unlink($target_file);
            }
        }
        $data->delete();
        return response()->json(['status' => 200, 'msg' => 'Đã xóa chuyên mục thành công !']);
    }

    public function changeNameCatePosts(Request $request)
    {
        $newSlug = $request->slug;
        $newName = trim(strip_tags(ucfirst($request->value)));
        $checkSlugExsits = CatePostsM::where('id', '!=', $request->pk)->where(function ($query) use ($newSlug, $newName) {
            $query->where('nameCatePosts', '=', $newName);
            $query->orWhere('slugCatePost', '=', $newSlug);
        })->count();
        if ($checkSlugExsits > 0) {
            return response()->json(['status' => 202, 'msg' => 'Tên chuyên mục đã tồn tại vui lòng chọn tên khác !']);
        }
        CatePostsM::find($request->pk)->update([
            $request->name => $newName,
            'slugCatePost' => $newSlug,
        ]);

        return response()->json(['status' => 200, 'msg' => 'Đã cập nhật thành công !']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductM;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CateController extends Controller
{

      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $allSlider = Category::where('status', '=',1)->orderBy('id', 'desc')->get();
        return response()->json($allSlider);
    }
    public function all(){
        $allCate = Category::with('product')->orderBy('created_at', 'desc')->get();
        return response()->json($allCate);
    }
    public function addCate(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required',
            'fileCate'=>'required',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' =>false,'status'=> 500]);
        }else{
            $name = $request -> name;
            $slug = Str::slug($name);
            $checkNameExists = Category::where('slug', '=', $slug)->count();
            if ($checkNameExists > 0) {
                return response()->json(['check'=>false,'status' => 203, 'msg' => 'Tiêu đề đã tồn tại!']);
            }
            $status = $request -> status;
            $file = $request -> file('fileCate');
            $file_path = public_path('category/' . $file);
            $fileExt = $file -> extension();
            $fileName = time().'cate.'.$fileExt;
            $file->move('category/', $fileName);
            Category::create([
                'name' => $name,
                'slug' =>$slug,
                'image' => $fileName,   
                'status' => $status,
            ]);
            return response()->json([
                'check' => true,
                'msg' =>'Thêm thành công'
            ]);    
        }
    }
    public function deleteCate(Request $request){
        if($request->id){
            $id = $request->id;
            $check = count(ProductM::where('idCate','=',$id)->get());
            if($check>0){
                return response()->json(['check' => false, 'status' => 201]);
            }else{
                $cate=Category::where('id', $request->id)->first();
                $imageName = $cate->image;
                $file_path = public_path('category/'.$imageName);
                if(file_exists($file_path)) {
                    unlink($file_path);
                }
                $cate = Category::find($id)->delete();
                return response()->json(['check' => true]);
            }
         
        }else{
            return response()->json(['check'=>false, 'status' =>400]);
        }
    }

    public function editCate(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',
],
        [
            'name.required' => 'Vui lòng nhập tên',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' =>false,'status'=>203, 'msg' => $validation->errors() ]);
        }else{
            $idCate = $request -> id;
            $name = $request -> name;
            $slug = Str::slug($name);
            $checkNameExists = Category::where('slug', '=', $slug)->where('id', '!=', $idCate)->count();
            if ($checkNameExists > 0) {
            return response()->json(['check'=>false,'status' => 203, 'msg' => 'Tiêu đề đã tồn tại!']);
            }
            $file = $request -> file('fileupdate');
            $cate = Category::find($idCate);
            if($file){
                $fileExt = $file -> extension();
                $fileName = time().'cate.'.$fileExt;
                $file_path = public_path('category/' . $cate->image);
                if(file_exists($file_path)) {
                    unlink($file_path);
                }
                $file->move('category/', $fileName);
                $cate->update([
                    'name'=>$name,
                    'slug' => $slug,
                    'image'=>$fileName,
                ]);
            }else{
                $cate->update([
                    'name'=>$name,
                    'slug' => $slug,
                ]);
            }
            
        }
        return response()->json(['status' =>200]);   
    }
    public function changeStatus(Request $request){
        $id = $request->id;
        $status = Category::where('id','=',$id)->select('status')->first();
        if($status->status==1){
            $status = 0;
        }else{
            $status=1;
        }
        Category::find($id)->update([
            'status'=>$status
        ]);
        return response()->json(['status'=> 200,'msg'=>'Update status successfully']);
    }
    public function update(Request $request)
    {
        if($request->pk){
            $id = $request->pk;
            $name = $request->value;
            $slug = Str::slug($name);
            $checkNameExists = Category::where('slug', '=', $slug)->where('id', '!=', $id)->count();
            if ($checkNameExists > 0) {
                return response()->json(['check'=>false,'status' => 203, 'msg' => 'Tên đã tồn tại!','id'=>$id]);
            }
            if($name==""){
                return response()->json(['check'=>false, 'status'=>400,'msg'=>'Tên không được để trống']);
            }
            Category::find($id)->update([
                'name'=>$name,
                'slug'=>Str::slug($request->value)
            ]);
            return response()->json(['check' => true]);
        }else{
            $id = $request->id;
            $file = $request -> file('file');
            $fileExt = $file -> extension();
            $fileName = time().'cate.'.$fileExt;
            $cate = Category::find($id);
            $file_path = public_path('category/' . $cate->image);
            if(file_exists($file_path)) {
                unlink($file_path);
            }
            $file->move('category/', $fileName);
            $cate->update([
                'image'=>$fileName
            ]);
            return response()->json(['check' => true,'msg'=>'Cập nhật ảnh đại diện thành công']);
        }

    }
    public function allCateClient(){
        $allCateClient = Category::with(['product' =>
        function($query) {
            $query->where('status','=', 1); }])->where('status','=','1')->orderBy('id', 'desc')->get();;
        return response()->json($allCateClient);
    }
    public function allCateClientMen(){
        $allCateClientMen = Category::with(['product' =>
         function($query) {
             $query->where('status','=', 1)->where('gender','=', 1); }])->where('status', 1)->get();
        return response()->json($allCateClientMen);
    }
    public function allCateClientWomen(){
        $allCateClientWomen = Category::with(['product' =>
         function($query) {
             $query->where('status','=', 1)->where('gender','=', 0); }])->where('status', 1)->get();
        return response()->json($allCateClientWomen);
    }
    public function getCateHomeClientTop_1(){
        $cateHomeClientTop_1 = Category::with(['product' =>
        function($query) {
            $query->where('status','=', 1); }])->where('status', '=' , '1')->orderBy('id','desc')->limit(1)->get();
        return response()->json($cateHomeClientTop_1);
           
    }
    public function getCateHomeClientTop_4(){
        $cateHomeClientTop_4 = Category::with(['product' =>
        function($query) {
            $query->where('status','=', 1); }])->where('status', '=' , '1')->orderBy('id','desc')->skip(1)->take(4)->get();
        return response()->json($cateHomeClientTop_4);
    }
}
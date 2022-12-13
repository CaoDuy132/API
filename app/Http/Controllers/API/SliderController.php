<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\sliderM;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SliderController extends Controller
{

      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $allSlider = sliderM::where('status', '=',1)->orderBy('id', 'desc')->get();
        return response()->json($allSlider);
    }
    public function sliders(){
        $slider = sliderM::orderBy('id', 'desc')->get();
        return response()->json($slider);
    }
    public function addSlider(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required',
            'fileSlider'=>'required',
            'href'=>'required',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' =>false,'status'=> 500]);
        }else{
            $name = $request -> name;
            $slug = Str::slug($name);
            $checkNameExists = sliderM::where('slug', '=', $slug)->count();
            if ($checkNameExists > 0) {
                return response()->json(['check'=>false,'status' => 203, 'msg' => 'Tiêu đề đã tồn tại!']);
            }
            $status = $request -> status;
            $href = $request -> href;
            $file = $request -> file('fileSlider');
            $file_path = public_path('sliders/' . $file);
            $fileExt = $file -> extension();
            $fileName = time().'.'.$fileExt;
            $file->move('sliders/', $fileName);
            sliderM::create([
                'name' => $name,
                'slug' =>$slug,
                'image' => $fileName,   
                'status' => $status,
                'href' => $href,
            ]);
            return response()->json([
                'check' => true,
                'msg' =>'Thêm thành công'
            ]);    
        }
    }
    public function deleteSlider(Request $request){
        if($request->id){
            $id = $request->id;
            $slider = sliderM::find($id)->delete();
            return response()->json(['check' => true]);
        }else{
            return response()->json(['check'=>false, 'status' =>400]);
        }
    }
    public function trash(){
        $trash = sliderM::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return response()->json($trash);
    }
    public function restore(Request $request)
    {
        if($request->id){
            sliderM::withTrashed()->find($request->id)->restore();
            return response()->json(['check'=>true, 'status' =>200,'msg'=>'Khôi phục thành công']);
        }else{
            return response()->json(['check'=>false, 'status' =>400,'msg'=>'Khôi phục không thành công']);
        }
    }
    public function detroy(Request $request){
        if($request->id){
            $slider=sliderM::withTrashed()->where('id', $request->id)->first();
            $imageName = $slider->image;
            $file_path = public_path('sliders/'.$imageName);
            if(file_exists($file_path)) {
                unlink($file_path);
            }
            sliderM::withTrashed()->find($request->id)->forceDelete();
            return response()->json(['check'=>true, 'status' =>200,'msg'=>'Xóa thành công']);
        }else{
            return response()->json(['check'=>false, 'status' =>400,'msg'=>'Xóa không thành công']);
        }
    }
    public function editSlider(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',
        ],
        [
            'name.required' => 'Vui lòng nhập tên',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' =>false,'status'=>203, 'msg' => $validation->errors() ]);
        }else{
            $idSlider = $request -> id;
            $name = $request -> name;
            $slug = Str::slug($name);
            if($request->href){
                $href = $request -> href;
            }else{
                $href = '/';
            }
            $checkNameExists = sliderM::where('slug', '=', $slug)->where('id', '!=', $idSlider)->count();
            if ($checkNameExists > 0) {
            return response()->json(['check'=>false,'status' => 203, 'msg' => 'Tiêu đề đã tồn tại!']);
            }
            $file = $request -> file('fileupdate');
            $slider = sliderM::find($idSlider);
            if($file){
                $fileExt = $file -> extension();
                $fileName = time().'.'.$fileExt;
                $file_path = public_path('sliders/' . $slider->image);
                if(file_exists($file_path)) {
                    unlink($file_path);
                }
                $file->move('sliders/', $fileName);
                $slider->update([
                    'name'=>$name,
                    'href'=>$href,
                    'slug' => $slug,
                    'image'=>$fileName,
                ]);
            }else{
                $slider->update([
                    'name'=>$name,
                    'href'=>$href,
                    'slug' => $slug,
                ]);
            }
            
        }
        return response()->json(['status' =>200]);   
    }
    public function changeStatus(Request $request){
        $id = $request->id;
        $status = sliderM::where('id','=',$id)->select('status')->first();
        if($status->status==1){
            $status = 0;
        }else{
            $status=1;
        }
        sliderM::find($id)->update([
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
            $checkNameExists = sliderM::where('slug', '=', $slug)->where('id', '!=', $id)->count();
            if ($checkNameExists > 0) {
                return response()->json(['check'=>false,'status' => 203, 'msg' => 'Tiêu đề đã tồn tại!','id'=>$id]);
            }
            if($name==""){
                return response()->json(['check'=>false, 'status'=>400,'msg'=>'Tiêu đề không được để trống']);
            }
            sliderM::find($id)->update([
                $request->name=>$request->value,
                'slug'=>Str::slug($request->value)
            ]);
            return response()->json(['check' => true]);
        }else{
            $id = $request->id;
            $file = $request -> file('file');
            $fileExt = $file -> extension();
            $fileName = time().'.'.$fileExt;
            $slider = sliderM::find($id);
            $file_path = public_path('sliders/' . $slider->image);
            if(file_exists($file_path)) {
                unlink($file_path);
            }
            $file->move('sliders/', $fileName);
            $slider->update([
                'image'=>$fileName
            ]);
            return response()->json(['check' => true,'msg'=>'Cập nhật banner thành công']);
        }

    }
    public function checkAction(Request $request){
        switch($request->action){
            case 'restore':
                if($request->checkIDsTrash){
                    $slider =sliderM::whereIn('id',$request->checkIDsTrash)->withTrashed()->restore();
                    return response()->json(['check' => true,'msg'=>'Khôi phục thành công']);
                }else{
                    return response()->json(['check' => true,'msg'=>'Khôi phục không thành thành công']);
                }
            break;
            case 'delete':
                if($request->checkIDs){
                    $slider =sliderM::whereIn('id',$request->checkIDs);
                    $slider->delete();
                    return response()->json(['check' => true,'msg'=>'Chuyển vào thùng rác thành công']);
                }else{
                    return response()->json(['check' => false,'msg'=>'Xóa không thành công']);
                }
            break;
            case 'detroy':
                if($request->checkIDsTrash){
                    $slider=sliderM::withTrashed()->whereIn('id', $request->checkIDsTrash)->get();
                    foreach($slider as $key=> $value){
                        $imageName = $value->image;
                        $file_path = public_path('sliders/'.$imageName);
                        if(file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                    $slider =sliderM::whereIn('id',$request->checkIDsTrash)->withTrashed()->forceDelete();
                    return response()->json(['check' => true,'msg'=>'Xóa vĩnh viễn thành công']);
                }else{
                    return response()->json(['check' => true,'msg'=>'Xóa không thành công']);
                }

            break;

        }
    }
 
    
}
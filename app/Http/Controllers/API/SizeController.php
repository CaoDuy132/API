<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\sizeM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request,sizeM $sizeM)
    {
        $validation = Validator::make($request->all(), [
            'sizename' => 'required|min:1|max:6',
            'sizeinfo' => 'required|min:1|max:20',
           
        ], [
            'sizename.required' => 'Thiếu tên loại size',
            'sizeinfo.required' => 'Thiếu thông tin loại size',
            'sizename.min' => ' Tên loại size không hợp lệ',
            'sizename.max' => 'Tên loại size quá dài',
            'sizeinfo.required' => 'Thiếu thông tin loại size !',
            'sizeinfo.min' => 'Thông tin loại size quá ngắn !',
            'sizeinfo.max' => 'Thông tin loại size quá dài !',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $check = sizeM::where('sizename','=',$request->sizename)->count('id');
            if($check!=0){
                return response()->json(['check'=>false,'message'=>'Đã tồn tại size sản phẩm']);
            }else{
                sizeM::create(['sizename'=>$request->sizename,'sizeinfo'=>$request->sizeinfo,'created_at'=>now()]);
                return response()->json(['check'=>true]);
            }
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\sizeM  $sizeM
     * @return \Illuminate\Http\Response
     */
    public function all2(sizeM $sizeM)
    {
        $result = DB::Table('tbl_size')->where('status',1)->select('id','sizename','sizeinfo','status','created_at','updated_at')->get();
        return response()->json($result);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\sizeM  $sizeM
     * @return \Illuminate\Http\Response
     */
    public function all1(sizeM $sizeM)
    {
        $result = DB::Table('tbl_size')->select('id','sizename','sizeinfo','status','created_at','updated_at')->get();
        return response()->json($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\sizeM  $sizeM
     * @return \Illuminate\Http\Response
     */
    public function switch(Request $request,sizeM $sizeM)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ], [
            'id.required' => 'Thiếu mã loại size',
            'id.numeric' => 'Mã loại size không tồn tại',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $check = sizeM::where('id','=',$request->id)->count('id');
            if($check==0){
                return response()->json(['check' => false, 'message' =>'Loại size không tồn tại']);
            }else{
                $old = sizeM::where('id','=',$request->id)->value('status');
                if($old==0){
                    sizeM::where('id','=',$request->id)->update(['status'=>1,'updated_at'=>now()]);
                    return response()->json(['check'=>true,'status'=>1]);
                }else{
                    sizeM::where('id','=',$request->id)->update(['status'=>0,'updated_at'=>now()]);
                    return response()->json(['check'=>true,'status'=>0]);

                }
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\sizeM  $sizeM
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, sizeM $sizeM)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'name' => 'required',

        ], [
            'id.required' => 'Thiếu mã loại size',
            'id.numeric' => 'Mã loại size không tồn tại',
            'name.required'=>'Thiếu mã size mới'
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $id=$request->id;
            $name=$request->name;
            $check = sizeM::where('id','=',$id)->count();
            $check2= sizeM::where('sizename','=',$name)->where('id','!=',$id)->count();
            if($check==0){
                return response()->json(['check' => false, 'message' => 'Không tồn tại mã size']);
            }else if($check2){
                return response()->json(['check' => false, 'message' => 'Tên size đã tồn tại']);
            }else{
                sizeM::where('id','=',$id)->update(['sizename'=>$name,'updated_at'=>now()]);
                return response()->json(['check' => true]);
            }
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\sizeM  $sizeM
     * @return \Illuminate\Http\Response
     */
    public function update2(Request $request, sizeM $sizeM)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'info' => 'required',

        ], [
            'id.required' => 'Thiếu mã loại size',
            'id.numeric' => 'Mã loại size không tồn tại',
            'info.required'=>'Thiếu thông tin size mới'
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $id=$request->id;
            $info=$request->info;
            $check = sizeM::where('id','=',$id)->count();
            if($check==0){
                return response()->json(['check' => false, 'message' => 'Không tồn tại mã size']);
            }else{
                sizeM::where('id','=',$id)->update(['sizeinfo'=>$info,'updated_at'=>now()]);
                return response()->json(['check' => true]);
            }
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\sizeM  $sizeM
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request,sizeM $sizeM)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ], [
            'id.required' => 'Thiếu mã loại size',
            'id.numeric' => 'Mã loại size không tồn tại',
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false, 'message' => $validation->errors()]);
        } else {
            $id=$request->id;
            $check = sizeM::where('id','=',$id)->count();
            if($check==0){
                return response()->json(['check' => false, 'message' => 'Không tồn tại mã size']);
            }else{
                sizeM::where('id','=',$id)->delete();
                return response()->json(['check' => true]);
            }
        }
    }
}

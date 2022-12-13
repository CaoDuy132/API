<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserRole;
use App\Models\userM;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
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
    public function store(Request $request,UserRole $UserRole)
    {
        $validation = Validator::make($request->all(), [
            'userRole' => 'required|max:50|min:7',
        ],[
            'userRole.required'=>'Thiếu loại tài khoản',
            'userRole.max'=>'Loại tài khoản quá dài',
            'userRole.min'=>'Loại tài khoản ít nhất 7 ký tự',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            if(UserRole::where('name','=',$request->userRole)->count()!=0){
                return response()->json(['check' => false,'message'=>'Đã tồn tại loại tài khoản']);
            }else{
                UserRole::create(['name'=>$request->userRole]);
                return response()->json(['check' => true]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,UserRole $UserRole)
    {
        $result = DB::Table('userrole')->select('id')->get();
        $result = json_decode($result,true);
        $result2=[];
        foreach ($result as $key => $value) {
            array_push($result2,$value['id']);
        }
        $arr=[];
        foreach ($result2 as $value) {
            $result3=DB::Table('userrole')->where('id',$value)->get();
            $result3 = json_decode($result3,true);
            $count= userM::where('idRole','=',$value)->count('id');
            foreach ($result3 as $key => $value1) {
                $item=['id'=>$value1['id'],'name'=>$value1['name'],'count'=>$count,'status'=>$value1['status'],'created_at'=>$value1['created_at']];
                array_push($arr,$item);
            }
        }
        return response()->json($arr);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function switch(Request $request,userM $userM,UserRole $UserRole)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ],[
            'id.required'=>'Thiếu mã loại tài khoản',
            'id.numeric'=>'Mã loại tài khoản không hợp lệ',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            $old = UserRole::where('id','=',$request->id)->value('status');
            if($old==0){
                UserRole::where('id','=',$request->id)->update(['status'=>1]);
                return response()->json(['check'=>true]);
            }else{
                UserRole::where('id','=',$request->id)->update(['status'=>0]);
                userM::where('idRole','=',$request->id)->update(['status'=>0]);
                return response()->json(['check'=>true]);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,UserRole $UserRole)
    {
        $validation = Validator::make($request->all(), [
            'idRole' => 'required|numeric',
            'rolename'=>'required'
        ],[
            'idRole.required'=>'Thiếu mã loại tài khoản',
            'idRole.numeric'=>'Mã tài khoản không hợp lệ',
            'rolename.required'=>'Thiếu loại tài khoản mới',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            if(UserRole::where('name','=',$request->rolename)->count()!=0){
                return response()->json(['check' => false,'message'=>'Loại tài khoản mới đã tồn tại']);
            }else{
                UserRole::where('id','=',$request->idRole)->update(['name'=>$request->rolename,'updated_at'=>now()]);
                return response()->json(['check' => true]);
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
        $validation = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ],[
            'id.required'=>'Thiếu loại tài khoản',
            'id.numeric'=>'Sai loại dữ liệu',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{

            $idRole = $request->id;
            $check = count(userM::where('idRole','=',$idRole)->get());
            if($check!=0){
                return response()->json(['check'=>false,'message'=>'Còn tồn tại tài khoản trong loại']);
            }else{
                UserRole::where('id','=',$idRole)->delete();
                return response()->json(['check'=>true]);
            }
        }
    }
}

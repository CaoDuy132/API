<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\userM;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
class UserController extends Controller
{

          /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function editemail(Request $request,userM $userM)
    {
        $validation = Validator::make($request->all(), [
            'id'=>'required|numeric',
            'email' => 'required|email',
            
        ],[
            'email.required'=>'Thiếu email tài khoản',
            'email.email'=>'Không phải email',
            'id.required'=>'Thiếu mã tài khoản',
            'id.numeric'=>'Mã tài khoản không đúng định dạng',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            $check = count(userM::where('email','=',$request->email)->get());
            if($check!=0){
                return response()->json(['check'=>false,'message'=>'Tài khoản đã tồn tại']);
            }else{
                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                $password = substr(str_shuffle($permitted_chars), 0, 5);
                $password1= Hash::make($password);
                userM::where('id','=',$request->id)->update(['email'=>$request->email,'password'=>$password1]);
                $email = $request->email;
                $details = [
                    'title' => 'Email thông báo tài khoản',
                    'password'=> $password,
                    'time'=>'Tài khoản được tạo vào lúc: '.date('d/m/yy',time()),
                    'thongbao'=>'Tài khoản đã được thay đổi email . '
                ];
                Mail::to($email)->send(new \App\Mail\MailThongBao($details));
                return response()->json(['check'=>true]);
            }
        }
    }
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function single(Request $request,userM $userM){
        $validation = Validator::make($request->all(), [
            'idUser' => 'required|numeric',
        ],[
            'idUser.required'=>'Thiếu mã tài khoản',
            'idUser.numeric'=>'Mã tài khoản phải là số',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            $result = userM::where('id','=',$request->idUser)->select('id as idUser','idRole as idRole','email as email')->get();
            if(count($result)==0){
                return response()->json(['check' => false]);
            }else{
                return response()->json(['check' => true,'result'=>$result]);
            }
        }
    }
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
    public function switch(Request $request,userM $userM)
    {
        $validation = Validator::make($request->all(), [
            'idUser' => 'required|numeric',
        ],[
            'idUser.required'=>'Thiếu mã tài khoản',
            'idUser.numeric'=>'Mã tài khoản phải là số',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            $old = userM::where('id','=',$request->idUser)->value('status');
            if($old==0){
                userM::where('id','=',$request->idUser)->update(['status'=>1]);
                return response()->json(['check' => true]);
            }else{
                userM::where('id','=',$request->idUser)->update(['status'=>0]);
                return response()->json(['check' => true]);

            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $result = DB::Table('users')->join('userrole','users.idRole','=','userrole.id')->select('users.id as idUser','users.email as email','users.status as userstt','userrole.name as rolename','users.idRole as useridRole','users.created_at as usercreated')->get();
        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,userM $userM)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'idRole'=>'required|numeric'
        ],[
            'email.required'=>'Thiếu email tài khoản',
            'email.email'=>'Không phải email',
            'idRole.required'=>'Thiếu loại tài khoản',
            'idRole.numeric'=>'Loại tài khoản phải là số',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            $check = count(userM::where('email','=',$request->email)->get());
            if($check!=0){
                return response()->json(['check'=>false,'message'=>'Tài khoản đã tồn tại']);
            }else{
                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                $password = substr(str_shuffle($permitted_chars), 0, 5);
                $password1= Hash::make($password);
                userM::create(['email'=>$request->email,'idRole'=>$request->idRole,'password'=>$password1]);
                $email = $request->email;
                $details = [
                    'title' => 'Email thông báo tài khoản',
                    'password'=> $password,
                    'time'=>'Tài khoản được tạo vào lúc: '.date('d/m/yy',time()),
                    'thongbao'=>'Vui lòng đăng nhập và thay đổi mật khẩu . '
                ];
                Mail::to($email)->send(new \App\Mail\MailThongBao($details));
                return response()->json(['check'=>true]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\userM  $userM
     * @return \Illuminate\Http\Response
     */
    public function show(userM $userM)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\userM  $userM
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,userM $userM)
    {
        $validation = Validator::make($request->all(), [
            'idUser'=>'required|numeric',
            'email' => 'required|email',
            'idRole'=>'required|numeric'
        ],[
            'email.required'=>'Thiếu email tài khoản',
            'email.email'=>'Không phải email',
            'idRole.required'=>'Thiếu loại tài khoản',
            'idRole.numeric'=>'Loại tài khoản phải là số',
            'idUser.required'=>'Thiếu mã tài khoản',
            'idUser.numeric'=>'Mã tài khoản không đúng định dạng',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            $check=count(userM::where('email','=',$request->email)->get());
            if($check!=0){
                $idUser = userM::where('email','=',$request->email)->value('id');
                if($idUser==$request->idUser){
                $email = $request->email;
                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                $password = substr(str_shuffle($permitted_chars), 0, 5);
                $password1= Hash::make($password);
                userM::where('id','=',$request->idUser)->update(['email'=>$email,'idRole'=>$request->idRole,'password'=>$password1]);
                $details = [
                    'title' => 'Email thông báo tài khoản',
                    'password'=> $password,
                    'time'=>'Tài khoản được tạo vào lúc: '.date('d/m/yy',time()),
                    'thongbao'=>'Vui lòng đăng nhập và thay đổi mật khẩu . '
                ];
                Mail::to($email)->send(new \App\Mail\MailThongBao($details));
                return response()->json(['check'=>true]);
                }else{
                    return response()->json(['check' => false,'message'=>'Đã tồn tại email này ']);

                }
            }else{
                $email = $request->email;
                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                $password = substr(str_shuffle($permitted_chars), 0, 5);
                $password1= Hash::make($password);
                userM::where('id','=',$request->idUser)->update(['email'=>$email,'idRole'=>$request->idRole,'password'=>$password1]);
                $details = [
                    'title' => 'Email thông báo tài khoản',
                    'password'=> $password,
                    'time'=>'Tài khoản được tạo vào lúc: '.date('d/m/yy',time()),
                    'thongbao'=>'Vui lòng đăng nhập và thay đổi mật khẩu . '
                ];
                Mail::to($email)->send(new \App\Mail\MailThongBao($details));
                return response()->json(['check'=>true]);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\userM  $userM
     * @return \Illuminate\Http\Response
     */
    public function checkLogin(Request $request, userM $userM)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'image' => 'required',
            'name'=>'required',
        ],[
            'email.required'=>'Thiếu email',
            'email.email'=>'Email không đúng định dạng',
            'image.required'=>'Thiếu hình ảnh',
            'name.required'=>'Thiếu name',
            
        ]);
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            $check = count(userM::where('status','=',1)->where('email','=',$request->email)->get());
            if($check!=0){
                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                $token = substr(str_shuffle($permitted_chars), 0, 10);
                $token3=base64_encode($token);
                $token2=md5($token);
                userM::where('status','=',1)->where('email','=',$request->email)->update(['name'=>$request->name,'remember_token'=>$token2,'image'=>$request->image]);
                return response()->json(['check'=>true,'token'=>$token3]);
            }else{
                return response()->json(['check'=>false]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\userM  $userM
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,userM $userM)
    {
        $validation = Validator::make($request->all(), [
            'idUser'=>'required|numeric',
        ],[
            'idUser.required'=>'Thiếu mã tài khoản',
            'idUser.numeric'=>'Mã tài khoản không đúng định dạng',
        ]); 
        if ($validation->fails()) {
            return response()->json(['check' => false,'message'=>$validation->errors()]);
        }else{
            userM::where('id','=',$request->idUser)->delete();
            return response()->json(['check' => true]);
        }
    }
}

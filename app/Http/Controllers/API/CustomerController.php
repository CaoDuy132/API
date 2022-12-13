<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerM;
use App\Models\RatingP;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class CustomerController extends Controller
{
    public function _login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Vui lòng nhập Email !',
            'email.email' => 'Email sai định dạng !',
            'password.required' => 'Vui lòng nhập mật khẩu !'
        ]);
        if ($validation->fails()) {
            return response()->json(['status' => 202, 'msg' => $validation->errors()]);
        }

        if (auth()->guard('customer')->attempt(['email' => $request->email, 'password' => $request->password, 'active' => 1, 'provider_id' => null, 'provider' => null])) {
            $customer = CustomerM::where('email', '=', $request->email)->whereNull('provider')->whereNull('provider_id')->where('active', '=', 1)->first();
            return response()->json([
                'status' => 200,
                'msg' => 'Đăng nhập thành công !',
                'token' => $customer->createToken("API TOKEN")->plainTextToken
            ]);
        } else {
            return response()->json([
                'status' => 204,
                'msg' => 'Email hoặc mật khẩu không đúng !',
            ]);
        }
    }

    public function _register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/',
            'phone' => 'required|numeric',
            'confirm_password' => 'required|same:password'
        ], [
            'name.required' => 'Vui lòng nhập họ tên !',
            'email.required' => 'Vui lòng nhập Email !',
            'email.email' => 'Email sai định dạng !',
            'password.required' => 'Vui lòng nhập mật khẩu !',
            'password.min' => 'Mật khẩu yếu !',
            'password.regex' => 'Mật khẩu yếu !',
            'confirm_password.required' => 'Vui lòng nhập lại mật khẩu !',
            'confirm_password.same' => 'Nhập lại mật khẩu không đúng !',
            'phone.required' => 'Vui lòng nhập số điện thoại !',
            'phone.numeric' => 'Số điện thoại sai định dạng !'
        ]);
        if ($validation->fails()) {
            return response()->json(['status' => 202, 'msg' => $validation->errors()]);
        } else {
            $checkEmailExists = CustomerM::where('email', '=', trim($request->email))->whereNull('provider')->whereNull('provider_id')->count();
            if ($checkEmailExists > 0) {
                return response()->json(['status' => 204, 'msg' => 'Email đã tồn tại hãy chọn Email khác !']);
            } else {
                $insert  = CustomerM::create([
                    'name' => trim(strip_tags(ucwords($request->name))),
                    'email' => trim(strip_tags($request->email)),
                    'phone' => trim(strip_tags($request->phone)),
                    'password' => bcrypt($request->password),
                    'hash_email_active' => md5(trim($request->email)),
                    'created_at' => now()
                ]);
                if ($insert) {
                    $data = array(
                        'url' => 'http://127.0.0.1:8000/active/account/email-verify/' . md5(trim($request->email)) . '.html',
                    );
                    $send = Mail::to($request->email)->send(new \App\Mail\ActiveAccount($data));
                    if ($send) {
                        return response()->json(['status' => 200, 'msg' => 'Đã đăng ký thành công , vui lòng kiểm tra email để kích hoạt tài khoản !', 'hash_email' => md5(trim($request->email))]);
                    }
                }
            }
        }
    }

    public function active(Request $request)
    {
        $check = CustomerM::where('hash_email_active', '=', $request->hash_email)->whereNull('provider')->whereNull('provider_id')->first();
        if ($check) {
            $check->update([
                'active' => 1
            ]);
            auth()->guard('customer')->login($check);
            return response()->json([
                'status' => 200,
                'msg' => 'Đăng nhập thành công !',
                'token' => $check->createToken("API TOKEN")->plainTextToken
            ]);
        } else {
            return response()->json([
                'status' => 404,
            ]);
        }
    }

    public function logout()
    {
        auth()->guard('customer')->logout();
        return response()->json(['status' => 200]);
    }

    public function loadNotificationUser($id = null)
    {
        $result = [];
        $all = RatingP::with('customer', 'product')->where('idCustomer', '=', $id)->where('status', '!=', 1)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();
        $dateLimit = now()->addMonths(-2);
        foreach ($all as $key => $value) {
            if (Carbon::parse($value->created_at)->toDateString() >= $dateLimit->toDateString()) {
                $result[] = $value;
            }
        }
        return response()->json($result);
    }
}

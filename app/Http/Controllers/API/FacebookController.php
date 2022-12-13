<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerM;
class FacebookController extends Controller
{
    public function _login(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $provider = $request->provider;
        $provider_id = $request->provider_id;
        $login = CustomerM::where(function ($query) use ($name, $email, $provider, $provider_id) {
            $query->where('name', '=', $name);
            $query->where('email', '=', $email);
            $query->where('provider', '=', $provider);
            $query->where('provider_id', '=', $provider_id);
        })->first();
        if ($login) {
            $login->update([
                'name' => $name,
                'email' => $email,
                'provider' => $provider,
                'provider_id' => $provider_id,
                'created_at' => now()
            ]);
            auth()->guard('customer')->login($login);
            return response()->json([
                'status' => 200,
                'msg' => 'Đăng nhập thành công !',
                'token' => $login->createToken("API TOKEN")->plainTextToken
            ]);
        } else {
            $insert = CustomerM::create([
                'name' => $name,
                'email' => $email,
                'provider' => $provider,
                'provider_id' => $provider_id,
                'created_at' => now()
            ]);
            auth()->guard('customer')->login($insert);
            return response()->json([
                'status' => 200,
                'msg' => 'Đăng nhập thành công !',
                'token' => $insert->createToken("API TOKEN")->plainTextToken
            ]);
        }
    }
}

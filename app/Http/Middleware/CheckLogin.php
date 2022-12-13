<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\userM;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!isset($request->token)){
            return response()->json(['check'=>false,'message'=>'Yêu cầu đăng nhập']);
        }else{

            $token = md5(base64_decode($request->token));
            if (count(userM::where('remember_token','=',$token)->get())==0) {
                return response()->json(['check'=>false,'message'=>'Login']);
            }
        }
        return $next($request);
    }
}

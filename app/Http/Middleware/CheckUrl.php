<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUrl
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
        $host= '127.0.0.1:8000';
        $host2= '127.0.0.1:7000';
        if($host!=$request->host&&$host2!=$request->host){
            return response()->json(['check'=>false,'host'=>$request->host,'host1'=>$host]);
        }else{
            
        }
        return $next($request);
    }
}

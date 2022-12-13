<?php

namespace App\Http\Middleware;

use App\Models\PostsM;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AutoDeleteTrashPosts
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
      
        $trashs = PostsM::onlyTrashed()->get();

        foreach ($trashs as $key => $value) {
              if(now()->toDateString() >= Carbon::parse($value->deleted_at)->addDays(30)->toDateString()){
                              $data = PostsM::withTrashed()->where('id','=',$value->id)->first();
                              DB::table('posts_prod')->where('id_posts','=',$value)->delete();
                              DB::table('posts_tag')->where('id_blog','=',$value)->delete();
                              if($data->imagePosts != null){
                                    $target = public_path('images/posts/'.$data->imagePosts);
                                    if(file_exists($target)){
                                       unlink($target);
                                    }
                              }
                              PostsM::withTrashed()->where('id','=',$value->id)->forceDelete();
              }
        }

        return $next($request);
    }
}

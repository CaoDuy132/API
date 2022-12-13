<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PostsM extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'idcatePosts', 'titlePosts', 'slugPosts', 'summaryPosts','imagePosts','contentPosts','author','viewPosts','statusPosts','created_at','updated_at','deleted_at'];
    public function cate_posts()
    {
       return $this->belongsTo(CatePostsM::class,"idcatePosts","id");
    }

    public function products()
    {
        return $this->belongsToMany(ProductM::class,'posts_prod','id_posts','id_prod');
    }
    public function tags()
    {
        return $this->hasMany(TagBlog::class,'id_blog','id');
    }
}

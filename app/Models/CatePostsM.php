<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatePostsM extends Model
{
    use HasFactory;
    protected $table = 'cate_posts';
    protected $primaryKey = 'id';
    protected $fillable = ['id','nameCatePosts','slugCatePost','logo','der','created_at','updated_at'];
    public function posts()
    {
        return $this->hasMany(PostsM::class,"idcatePosts","id");
    }
}

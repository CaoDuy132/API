<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagBlog extends Model
{
    use HasFactory;
    protected $table = 'posts_tag';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'id_blog','nameTagBlog','slugTagBlog','updated_at','deleted_at'];
    public function posts()
    {
        return $this->belongsTo(PostsM::class,'id_blog','id');
    }
}

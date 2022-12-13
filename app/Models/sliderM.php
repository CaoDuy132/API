<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class sliderM extends Model
{
    protected $table= 'slider';
    protected $primary= 'id';
    protected $fillable=['name','slug','image','href','path','status','create_at','updated_at'];
    protected $dates = ['deleted_at'];
    use HasFactory,SoftDeletes;
}
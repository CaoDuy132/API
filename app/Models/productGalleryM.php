<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productGalleryM extends Model
{
    protected $table='productimage';
    protected $fillable=['idProd','id','image','choose','created_at','updated_at'];
    use HasFactory;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class storageM extends Model
{
    protected $table='storage';
    protected $fillable=['idProd','id','color','idSize','status','quantity','created_at','updated_at'];
    use HasFactory;
}

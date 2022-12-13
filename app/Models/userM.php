<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userM extends Model
{
    protected $table='users';
    protected $fillable=['id','email','remember_token','name','password','image','status','idRole','created_at','updated_at'];
    use HasFactory;
    
}

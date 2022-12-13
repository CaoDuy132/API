<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sizeM extends Model
{
    protected $table="tbl_size";
    protected $fillable=['id','sizename','sizeinfo','created_at','updated_at','status'];
    use HasFactory;
}

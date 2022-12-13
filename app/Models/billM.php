<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class billM extends Model
{
    protected $table="bills";
    protected $fillable=['id','idCustiomer','idBill','recieverName','address','total','recieverPhone','status','note','created_at','updated_at'];
    use HasFactory;
}

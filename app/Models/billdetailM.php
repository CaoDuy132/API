<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class billdetailM extends Model
{
    protected $table='billdetail';
    protected $fillale=['id','idBill','idStorage','quantity','created_at','updated_at'];
    use HasFactory;
}

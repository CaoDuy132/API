<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class infoM extends Model
{
    protected $table= 'infoshop';
    protected $primary= 'id';
    protected $fillable=['shopName','email','address','phoneNumber','logo','introShop','created_at','updated_at'];
    use HasFactory;
}
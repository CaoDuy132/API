<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
class CustomerM extends Authenticatable
{
    use HasFactory, HasApiTokens,Notifiable;
    protected $guard = 'customer';
    protected $table='customers';
    protected $fillable=['id','name','phone','email','password','active','hash_email_active','provider','provider_id','created_at','updated_at'];

    public function ratings()
    {
         return $this->hasMany(RatingP::class,'idCustomer','id');
    }
}

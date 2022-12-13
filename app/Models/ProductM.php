<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductM extends Model
{
    protected $table='products';
    protected $fillable=['id','name','gender','slug','image','price','discount','content','status','idCate','idBrand','week','seen','created_at','updated_at'];
    use HasFactory;
    
    public function reviews()
    {
        return $this->hasMany(RatingP::class,'idProd','id');
    }
}

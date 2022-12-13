<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class brandM extends Model
{
    protected $table='brands';
    protected $fillable=['id','name','status','created_at','updated_at'];
    use HasFactory;
    public function prods()
    {
        return $this->hasMany(ProductM::class,"idBrand","id");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table='categrories';
    protected $fillable=['id','name','image','slug','status','created_at','updated_at'];
    use HasFactory;
    public function product()
    {
        return $this->hasMany(ProductM::class,"idCate","id");
    }
}

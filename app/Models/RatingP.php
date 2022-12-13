<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingP extends Model
{
    use HasFactory;
    protected $table='rating_prod';
    protected $fillable=['id','idProd','idCustomer','num_star','content_review','status','created_at','updated_at'];

    public function customer()
    {
        return $this->belongsTo(CustomerM::class,'idCustomer','id');
    }

    public function product()
    {
        return $this->belongsTo(ProductM::class,'idProd','id');
    }
}

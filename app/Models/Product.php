<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'provider_id',
        'name',
        'stock',
        'image',
        'price',
        'status',
        'code'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function provider(){
        return $this->belongsTo(Provider::class);
    }

    public function purchaseDetails(){
        return $this->hasMany(PurchaseDetail::class);
    }
}

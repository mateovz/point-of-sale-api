<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'address',
        'phone',
        'ruc'
    ];

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function purchases(){
        return $this->hasMany(Purchase::class);
    }
}

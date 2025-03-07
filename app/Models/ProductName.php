<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductName extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'unit'];

    public function productIns()
    {
        return $this->hasMany(ProductIn::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

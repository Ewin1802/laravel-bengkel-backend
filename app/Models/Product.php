<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'product_in_id',
        'name',
        'description',
        'price',
        'image',
        'stock',
        'status',
        'is_favorite',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productIn()
    {
        return $this->belongsTo(ProductIn::class, 'product_in_id');
    }

    public function productName()
    {
        return $this->hasOneThrough(ProductName::class, ProductIn::class, 'id', 'id', 'product_in_id', 'product_name_id');
    }

    public function reduceStock($quantity)
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
        } else {
            throw new \Exception("Stok tidak cukup untuk produk: " . $this->name);
        }
    }

}


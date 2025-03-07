<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name_id', 'amount', 'quantity', 'supplier_id',
        'is_received', 'receiver', 'description', 'image', 'tanggal_masuk'
    ];

    // Relasi ke product_names
    public function productName()
    {
        return $this->belongsTo(ProductName::class, 'product_name_id');
    }

    // Relasi ke suppliers
    public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }
}


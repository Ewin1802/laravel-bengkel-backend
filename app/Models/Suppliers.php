<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'address',
    ];

    public function productIns()
    {
        return $this->hasMany(ProductIn::class);
    }
}

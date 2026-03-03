<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'avg_cost',
        'sale_price',
        'stock',
    ];

    protected $casts = [
        'avg_cost' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'integer',
    ];
}

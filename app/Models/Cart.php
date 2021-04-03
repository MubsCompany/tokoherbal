<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'cart_id',
        'quantity',
        'product_id',
        'is_checkout',
    ];
}

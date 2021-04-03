<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'status_order',
        'cart_id',
    ];
    public function cart()
    {
        return $this->hasMany(Cart::class, 'cart_id', 'cart_id');
    }
}

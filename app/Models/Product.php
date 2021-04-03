<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'manufacture',
        'price',
        'description',
        'quantity',
        'is_recomended',
    ];
    public function images()
    {
        return $this->hasMany(Media::class);
    }
}

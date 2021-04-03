<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    /**
     * Yang sekiranya bisa diisi oleh user
     */
    protected $fillable = [
        'user_id',
        'alamat',
        'provinsi',
        'kota',
        'kecamatan',
        'desa',
        'kodepos',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
    ];
}

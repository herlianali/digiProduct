<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    public $timestamps = false;

    protected $fillable = ['code', 'discount_pct', 'expires_at', 'is_active'];

    protected $casts = [
        'is_active'  => 'boolean',
        'expires_at' => 'datetime',
    ];
}

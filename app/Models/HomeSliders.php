<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSliders extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'link',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

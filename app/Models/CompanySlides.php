<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySlides extends Model
{
    protected $fillable = [
        'type',
        'title',
        'image',
        'link',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

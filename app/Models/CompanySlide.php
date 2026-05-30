<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySlide extends Model
{
    use HasFactory;

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

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeImage($query)
    {
        return $query->where('type', 'image');
    }

    public function scopeText($query)
    {
        return $query->where('type', 'text');
    }
}
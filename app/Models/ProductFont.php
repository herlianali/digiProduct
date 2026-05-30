<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFont extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'font_family',
        'font_styles',
        'font_formats',
        'glyph_count',
        'language_support',
        'variable_font',
    ];

    protected $casts = [
        'font_styles' => 'array',
        'font_formats' => 'array',
        'language_support' => 'array',
        'variable_font' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
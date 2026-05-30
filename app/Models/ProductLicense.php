<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'license_type',
        'license_text',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'file_path',
        'file_type',
        'file_size',
        'download_limit',
        'version',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
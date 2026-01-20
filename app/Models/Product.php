<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';
    protected $guarded = [];

    // Relationship dengan category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship dengan previews
    public function previews(): HasMany
    {
        return $this->hasMany(ProductPreview::class);
    }
}


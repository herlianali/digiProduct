<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';
    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function previews(): HasMany
    {
        return $this->hasMany(ProductPreview::class);
    }

    public function getThumbnailAttribute()
    {
        $primaryPreview = $this->previews()->where('sort_order', 0)->first();
        return $primaryPreview ? $primaryPreview->path : null;
    }
}


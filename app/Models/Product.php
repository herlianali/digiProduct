<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'category_id',
        'style_id',
        'price',
        'discount_price',
        'is_free',
        'thumbnail',
        'featured',
        'status',
        'views',
        'downloads',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'featured' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function style()
    {
        return $this->belongsTo(Style::class);
    }

    public function files()
    {
        return $this->hasMany(ProductFile::class);
    }

    public function previews()
    {
        return $this->hasMany(ProductPreview::class);
    }

    public function licenses()
    {
        return $this->hasMany(ProductLicense::class);
    }

    public function font()
    {
        return $this->hasOne(ProductFont::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function analytics()
    {
        return $this->hasMany(ProductAnalytic::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFinalPriceAttribute()
    {
        if ($this->is_free) {
            return 0;
        }
        
        if ($this->discount_price && $this->discount_price < $this->price) {
            return $this->discount_price;
        }
        
        return $this->price;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}
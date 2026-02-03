<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Orders extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'subtotal', 'discount', 'total',
        'payment_status', 'status', 'paid_at',
    ];

    protected $dates = ['paid_at'];

    // Relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke OrderItem
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'product_title', 'price', 'quantity',
    ];

    // Relasi ke Order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Orders::class);
    }

    // Relasi ke Product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}


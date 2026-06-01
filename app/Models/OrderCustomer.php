<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCustomer extends Model
{
    protected $fillable = [
        'order_id',
        'name',
        'email',
        'phone',
        'whatsapp',
        'company',
        'address',
        'city',
        'country',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'id');
    }

    public function getWhatsappNumberAttribute(): ?string
    {
        $number = $this->whatsapp ?? $this->phone;
        if (!$number) return null;

        $number = preg_replace('/\D/', '', $number);
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }

        return $number;
    }
}

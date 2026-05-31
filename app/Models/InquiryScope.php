<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryScope extends Model
{
    public $timestamps = false;

    protected $fillable = ['inquiry_id', 'scope'];

    public function inquiry()
    {
        return $this->belongsTo(ProjectInquiry::class, 'inquiry_id');
    }
}

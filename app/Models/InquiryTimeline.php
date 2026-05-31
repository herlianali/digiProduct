<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryTimeline extends Model
{
    public $timestamps = false;

    protected $fillable = ['inquiry_id', 'timeline'];

    public function inquiry()
    {
        return $this->belongsTo(ProjectInquiry::class, 'inquiry_id');
    }
}

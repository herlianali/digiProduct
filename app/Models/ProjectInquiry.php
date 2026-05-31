<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectInquiry extends Model
{
    protected $fillable = [
        'full_name',
        'company_name',
        'email',
        'budget',
        'voucher_code',
        'project_description',
        'status',
    ];

    public function scopes()
    {
        return $this->hasMany(InquiryScope::class, 'inquiry_id');
    }

    public function timelines()
    {
        return $this->hasMany(InquiryTimeline::class, 'inquiry_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_code', 'code');
    }
}

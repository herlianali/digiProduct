<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentSettings extends Model
{
    protected $table = 'content_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    protected $casts = [
        'value' => 'string',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Articles extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'thumbnail', 'author_id', 'status', 'published_at',
    ];

    protected $dates = ['published_at'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}


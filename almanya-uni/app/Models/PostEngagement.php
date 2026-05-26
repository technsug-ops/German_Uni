<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostEngagement extends Model
{
    protected $fillable = [
        'post_id', 'session_id', 'scroll_depth', 'seconds', 'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}

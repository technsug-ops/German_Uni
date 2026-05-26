<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HousingTip extends Model
{
    protected $fillable = [
        'user_id', 'city_id', 'city_name', 'title', 'category', 'content',
        'upvote_count', 'is_approved', 'approved_at',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function scopeApproved($q)
    {
        return $q->where('is_approved', true);
    }
}

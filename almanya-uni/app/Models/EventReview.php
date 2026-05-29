<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventReview extends Model
{
    protected $fillable = [
        'event_id', 'user_id',
        'rating',
        'attendee_name', 'attendee_email',
        'body', 'status', 'is_pinned', 'helpful_count',
        'ip_address', 'user_agent',
        'approved_at', 'approved_by',
    ];

    protected $casts = [
        'rating'        => 'integer',
        'is_pinned'     => 'boolean',
        'helpful_count' => 'integer',
        'approved_at'   => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved($q) { return $q->where('status', 'approved'); }
    public function scopePending($q)  { return $q->where('status', 'pending'); }

    public function getDisplayNameAttribute(): string
    {
        return $this->user?->name ?? ($this->attendee_name ?: 'Anonim');
    }

    public function getDisplayAvatarAttribute(): ?string
    {
        return $this->user?->avatar_url;
    }

    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRsvp extends Model
{
    protected $fillable = [
        'event_id', 'user_id',
        'attendee_name', 'attendee_email',
        'status', 'note',
        'ip_address', 'user_agent',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeGoing($q) { return $q->where('status', 'going'); }
    public function scopeMaybe($q) { return $q->where('status', 'maybe'); }

    public function getDisplayNameAttribute(): string
    {
        return $this->user?->name ?? ($this->attendee_name ?: 'Anonim');
    }

    public function getDisplayAvatarAttribute(): ?string
    {
        return $this->user?->avatar_url;
    }
}

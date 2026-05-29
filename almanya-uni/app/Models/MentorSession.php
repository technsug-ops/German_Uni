<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MentorSession extends Model
{
    protected $fillable = [
        'mentor_id', 'user_id',
        'scheduled_at', 'duration_minutes',
        'jitsi_room_id',
        'external_provider', 'external_booking_id',
        'topic', 'notes', 'preferred_language',
        'status', 'cancellation_reason',
        'rating', 'feedback',
    ];

    protected $casts = [
        'scheduled_at'     => 'datetime',
        'duration_minutes' => 'integer',
        'rating'           => 'integer',
    ];

    public const STATUSES = [
        'pending'   => '⏳ Onay bekliyor',
        'confirmed' => '✅ Onaylandı',
        'completed' => '🎓 Tamamlandı',
        'cancelled' => '❌ İptal',
        'no_show'   => '👻 Katılmadı',
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Public Jitsi meeting URL (e.g. https://meet.jit.si/AlmanyaUni-Mentor-abc123…) */
    public function jitsiUrl(): string
    {
        return 'https://meet.jit.si/' . $this->jitsi_room_id;
    }

    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->whereIn('status', ['pending', 'confirmed'])
                 ->where('scheduled_at', '>=', now());
    }

    public function scopePast(Builder $q): Builder
    {
        return $q->where(function ($qq) {
            $qq->where('scheduled_at', '<', now())
               ->orWhereIn('status', ['completed', 'cancelled', 'no_show']);
        });
    }

    protected static function booted(): void
    {
        static::creating(function (self $session) {
            if (empty($session->jitsi_room_id)) {
                // Brand-prefixed UUID — unguessable + identifies platform in URLs
                $session->jitsi_room_id = 'AlmanyaUni-' . Str::lower(Str::random(12)) . '-' . Str::lower(Str::random(8));
            }
        });
    }
}

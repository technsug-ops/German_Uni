<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contribution extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'content',
        'target_type', 'target_id', 'target_label',
        'status', 'upvote_count', 'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public const TYPES = [
        'experience' => '📝 Deneyim',
        'tip'        => '💡 İpucu',
        'correction' => '✏️ Düzeltme',
    ];

    public const TARGETS = [
        'general'    => 'Genel',
        'city'       => 'Şehir',
        'university' => 'Üniversite',
        'program'    => 'Program',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status', 'approved');
    }

    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', 'pending');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}

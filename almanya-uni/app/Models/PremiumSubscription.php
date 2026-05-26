<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PremiumSubscription extends Model
{
    protected $fillable = [
        'user_id', 'tier', 'status', 'started_at', 'ends_at',
        'payment_provider', 'payment_id', 'amount_eur', 'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at'    => 'datetime',
        'amount_eur' => 'decimal:2',
        'metadata'   => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && (! $this->ends_at || $this->ends_at->isFuture());
    }
}

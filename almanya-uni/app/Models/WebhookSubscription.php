<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebhookSubscription extends Model
{
    protected $fillable = [
        'api_client_id',
        'url',
        'events',
        'secret',
        'is_active',
        'failure_count',
        'last_success_at',
        'last_failure_at',
        'last_failure_reason',
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
        'last_success_at' => 'datetime',
        'last_failure_at' => 'datetime',
    ];

    public const AVAILABLE_EVENTS = [
        'university.created',
        'university.updated',
        'university.deleted',
        'program.created',
        'program.updated',
        'program.deleted',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(ApiClient::class, 'api_client_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(WebhookDelivery::class);
    }

    public function subscribesTo(string $event): bool
    {
        return in_array($event, $this->events ?? [], true)
            || in_array('*', $this->events ?? [], true);
    }
}

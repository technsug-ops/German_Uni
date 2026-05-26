<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookDelivery extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'webhook_subscription_id',
        'event',
        'payload',
        'status_code',
        'response_body',
        'duration_ms',
        'attempts',
        'succeeded',
        'delivered_at',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'succeeded' => 'boolean',
        'delivered_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(WebhookSubscription::class, 'webhook_subscription_id');
    }
}

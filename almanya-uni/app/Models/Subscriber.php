<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    protected $fillable = [
        'email',
        'name',
        'language',
        'source',
        'referrer_url',
        'confirm_token',
        'unsubscribe_token',
        'confirmed_at',
        'unsubscribed_at',
        'unsubscribe_reason',
        'ip_address',
        'user_agent',
        'bounce_count', 'bounced_at', 'complaint_at',
        'last_sent_at', 'last_open_at', 'last_click_at',
        'open_count', 'click_count', 'webhook_meta',
    ];

    protected $casts = [
        'confirmed_at'    => 'datetime',
        'unsubscribed_at' => 'datetime',
        'bounced_at'      => 'datetime',
        'complaint_at'    => 'datetime',
        'last_sent_at'    => 'datetime',
        'last_open_at'    => 'datetime',
        'last_click_at'   => 'datetime',
        'webhook_meta'    => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $sub) {
            if (empty($sub->confirm_token)) {
                $sub->confirm_token = Str::random(48);
            }
            if (empty($sub->unsubscribe_token)) {
                $sub->unsubscribe_token = Str::random(48);
            }
        });
    }

    public function scopeConfirmed(Builder $q): Builder
    {
        return $q->whereNotNull('confirmed_at')->whereNull('unsubscribed_at');
    }

    public function scopePending(Builder $q): Builder
    {
        return $q->whereNull('confirmed_at')->whereNull('unsubscribed_at');
    }

    public function scopeUnsubscribed(Builder $q): Builder
    {
        return $q->whereNotNull('unsubscribed_at');
    }

    /** Hard-bounced or complained — should never receive another mail. */
    public function scopeDeliverable(Builder $q): Builder
    {
        return $q->whereNull('bounced_at')->whereNull('complaint_at');
    }

    /** Mailable-ready: confirmed + not unsubscribed + deliverable. */
    public function scopeReachable(Builder $q): Builder
    {
        return $q->confirmed()->deliverable();
    }

    public function getIsConfirmedAttribute(): bool
    {
        return $this->confirmed_at !== null && $this->unsubscribed_at === null;
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->confirmed_at === null && $this->unsubscribed_at === null;
    }
}

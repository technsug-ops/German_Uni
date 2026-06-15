<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Şehir bazlı etkinlik bildirim aboneliği (double opt-in).
 * @see \App\Http\Controllers\Web\EventAlertController
 * @see \App\Console\Commands\NotifyEventSubscribers
 */
class EventCitySubscription extends Model
{
    protected $fillable = [
        'user_id', 'email', 'city_id', 'locale',
        'confirm_token', 'unsubscribe_token',
        'confirmed_at', 'unsubscribed_at', 'last_notified_at',
        'source', 'ip_address',
    ];

    protected $casts = [
        'confirmed_at'     => 'datetime',
        'unsubscribed_at'  => 'datetime',
        'last_notified_at' => 'datetime',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsConfirmedAttribute(): bool
    {
        return $this->confirmed_at !== null && $this->unsubscribed_at === null;
    }

    /** Onaylı + iptal edilmemiş aboneler (digest hedefi). */
    public function scopeActive(Builder $q): Builder
    {
        return $q->whereNotNull('confirmed_at')->whereNull('unsubscribed_at');
    }

    /** Yeni confirm + unsubscribe token üretir. */
    public function regenerateTokens(): void
    {
        $this->confirm_token = Str::random(48);
        $this->unsubscribe_token = Str::random(48);
    }
}

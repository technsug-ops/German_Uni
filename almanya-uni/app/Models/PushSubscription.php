<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tarayıcı web-push aboneliği (şehir bazlı). @see \App\Http\Controllers\Web\PushSubscriptionController
 */
class PushSubscription extends Model
{
    protected $fillable = [
        'user_id', 'city_id', 'endpoint', 'endpoint_hash', 'p256dh', 'auth',
        'locale', 'last_notified_at',
    ];

    protected $casts = [
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

    public static function hashEndpoint(string $endpoint): string
    {
        return hash('sha256', $endpoint);
    }
}

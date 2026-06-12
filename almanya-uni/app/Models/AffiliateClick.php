<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tek affiliate tıklaması (Sperrkonto/sigorta sağlayıcı dış linki).
 * AffiliateController@go yazar. updated_at yok — sadece created_at (DB useCurrent).
 */
class AffiliateClick extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'provider_type', 'provider_id', 'provider_slug',
        'context', 'locale', 'host', 'ip_hash', 'user_agent', 'referer',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}

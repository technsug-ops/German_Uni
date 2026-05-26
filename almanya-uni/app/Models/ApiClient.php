<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class ApiClient extends Model
{
    use HasFactory;
    use HasApiTokens;

    protected $fillable = [
        'name',
        'slug',
        'contact_email',
        'contact_name',
        'website',
        'plan',
        'rate_limit_per_minute',
        'allowed_endpoints',
        'is_active',
        'notes',
        'last_used_at',
    ];

    protected $casts = [
        'allowed_endpoints' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'rate_limit_per_minute' => 'integer',
    ];

    public const PLAN_LIMITS = [
        'free' => 60,
        'partner' => 1000,
        'enterprise' => 10000,
    ];

    public const PLAN_ABILITIES = [
        'free' => ['read:universities', 'read:programs', 'read:reference'],
        'partner' => ['read:universities', 'read:programs', 'read:reference', 'webhooks:manage'],
        'enterprise' => ['*'],
    ];

    public function effectiveRateLimit(): int
    {
        return $this->rate_limit_per_minute ?: (self::PLAN_LIMITS[$this->plan] ?? 60);
    }

    public function defaultAbilities(): array
    {
        return self::PLAN_ABILITIES[$this->plan] ?? ['read:universities', 'read:programs', 'read:reference'];
    }

    public function webhookSubscriptions(): HasMany
    {
        return $this->hasMany(WebhookSubscription::class, 'api_client_id');
    }
}

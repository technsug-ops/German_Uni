<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HousingProvider extends Model
{
    use \App\Models\Concerns\LocalizableContent;

    protected $fillable = [
        'slug', 'name', 'type', 'website', 'affiliate_url', 'email', 'phone',
        'logo_url', 'description_tr', 'description_en', 'description_de',
        'price_min', 'price_max',
        'cities', 'features',
        'total_capacity', 'waiting_period',
        'is_featured', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'cities'      => 'array',
        'features'    => 'array',
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public const TYPES = [
        'studierendenwerk' => ['label' => 'Devlet (Studierendenwerk)', 'emoji' => '🏛️', 'color' => 'emerald'],
        'private_chain'    => ['label' => 'Özel Şirket',                'emoji' => '🏢', 'color' => 'indigo'],
        'platform'         => ['label' => 'Portal / Platform',          'emoji' => '🌐', 'color' => 'amber'],
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeOfType(Builder $q, string $type): Builder
    {
        return $q->where('type', $type);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type]['label'] ?? $this->type;
    }

    public function getTypeEmojiAttribute(): string
    {
        return self::TYPES[$this->type]['emoji'] ?? '🏠';
    }

    public function getPriceRangeAttribute(): string
    {
        if (! $this->price_min && ! $this->price_max) return '—';
        if ($this->price_min === $this->price_max) return '€' . $this->price_min;
        return '€' . $this->price_min . '–€' . $this->price_max;
    }

    public function getCtaUrlAttribute(): ?string
    {
        return $this->affiliate_url ?: $this->website;
    }

    /** affiliate_clicks.provider_type + /go/{type}/ segmenti. */
    public const AFFILIATE_TYPE = 'housing';

    /** Takipli dış-link: /go/housing/{slug}?ctx=... → tıklama loglanır, sonra cta_url'e 302. */
    public function trackedUrl(?string $ctx = null): string
    {
        return route('affiliate.go', array_filter([
            'type' => self::AFFILIATE_TYPE,
            'slug' => $this->slug,
            'ctx'  => $ctx,
        ]));
    }
}

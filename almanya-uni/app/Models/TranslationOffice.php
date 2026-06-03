<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TranslationOffice extends Model
{
    use \App\Models\Concerns\LocalizableContent;

    protected $fillable = [
        'slug', 'name', 'type', 'website', 'affiliate_url', 'email', 'phone',
        'logo_url', 'image_path',
        'description_tr', 'description_en', 'description_de',
        'cities', 'languages', 'features', 'is_sworn',
        'is_featured', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'cities'      => 'array',
        'languages'   => 'array',
        'features'    => 'array',
        'is_sworn'    => 'boolean',
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public const TYPES = [
        'sworn_individual' => ['label' => 'Yeminli Tercüman (Birey)', 'emoji' => '👤', 'color' => 'indigo'],
        'agency'           => ['label' => 'Tercüme Bürosu',           'emoji' => '🏢', 'color' => 'emerald'],
        'online'           => ['label' => 'Online Tercüme',           'emoji' => '🌐', 'color' => 'amber'],
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
        return self::TYPES[$this->type]['emoji'] ?? '📜';
    }

    public function getOutboundUrlAttribute(): ?string
    {
        return $this->affiliate_url ?: $this->website;
    }
}

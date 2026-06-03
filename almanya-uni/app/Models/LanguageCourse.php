<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LanguageCourse extends Model
{
    use \App\Models\Concerns\LocalizableContent;

    protected $fillable = [
        'slug', 'name', 'type', 'website', 'affiliate_url', 'email', 'phone',
        'logo_url', 'image_path',
        'description_tr', 'description_en', 'description_de',
        'cities', 'levels', 'features',
        'price_min', 'price_max', 'price_note',
        'is_featured', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'cities'      => 'array',
        'levels'      => 'array',
        'features'    => 'array',
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public const TYPES = [
        'university' => ['label' => 'Üniversite Dil Kursu', 'emoji' => '🎓', 'color' => 'indigo'],
        'private'    => ['label' => 'Özel Dil Kursu',       'emoji' => '🏫', 'color' => 'emerald'],
        'online'     => ['label' => 'Online Dil Kursu',     'emoji' => '💻', 'color' => 'amber'],
    ];

    /** CEFR seviyeleri. */
    public const LEVELS = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];

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
        return self::TYPES[$this->type]['emoji'] ?? '🗣️';
    }

    /** Affiliate varsa onu, yoksa website'i kullan (tık yönlendirmesi için). */
    public function getOutboundUrlAttribute(): ?string
    {
        return $this->affiliate_url ?: $this->website;
    }

    public function getPriceRangeAttribute(): ?string
    {
        if ($this->price_min && $this->price_max) {
            return "€{$this->price_min}–{$this->price_max}";
        }
        return $this->price_min ? "€{$this->price_min}+" : ($this->price_note ?: null);
    }
}

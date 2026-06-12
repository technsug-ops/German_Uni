<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Profesyonel başvuru belgesi şablonu (Lebenslauf, Motivationsschreiben…).
 * title/description/name → LocalizableContent (otomatik fallback).
 * title/guide için açık accessor (trait sadece name+description veriyor).
 */
class DocumentTemplate extends Model
{
    use Concerns\LocalizableContent;

    protected $fillable = [
        'slug', 'category', 'doc_type',
        'title_tr', 'title_en', 'title_de',
        'description_tr', 'description_en', 'description_de',
        'body_de', 'body_en',
        'guide_tr', 'guide_en', 'guide_de',
        'placeholders', 'is_premium', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'placeholders' => 'array',
        'is_premium'   => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    /** Başlık — kimlik alanı, strict DEĞİL (çeviri yoksa TR'ye düşmek boş bırakmaktan iyi). */
    public function getTitleAttribute(): ?string
    {
        return $this->localized('title');
    }

    /** Nasıl doldurulur rehberi — prose, strict (EN/DE sayfada TR sızmaz). */
    public function getGuideAttribute(): ?string
    {
        return $this->localized('guide', strict: true);
    }

    /** Doldurulabilir gövde — DE ana, EN varsa locale'e göre tercih (İngilizce programlar). */
    public function bodyForLocale(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        if ($locale === 'en' && ! empty($this->body_en)) {
            return $this->body_en;
        }
        return $this->body_de ?: $this->body_en;
    }

    /** Kategori → ikon adı (svg-icon). */
    public function getIconAttribute(): string
    {
        return match ($this->doc_type) {
            'email'  => 'envelope',
            default  => 'document-text',
        };
    }
}

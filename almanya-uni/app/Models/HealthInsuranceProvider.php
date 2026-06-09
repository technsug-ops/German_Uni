<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HealthInsuranceProvider extends Model
{
    use \App\Models\Concerns\LocalizableContent;

    protected $fillable = [
        'slug', 'name', 'logo_url', 'website_url', 'affiliate_url',
        'type',
        'monthly_fee_eur', 'monthly_fee_max_eur', 'age_limit',
        'accepted_for_visa', 'accepted_for_enrollment',
        'covers_dental', 'covers_pregnancy', 'covers_mental_health', 'covers_repatriation',
        'digital_signup', 'english_support', 'supported_languages',
        'best_for',
        'description_tr', 'description_en', 'description_de', 'description_long',
        'pros', 'cons', 'features',
        'visa_recognition_note', 'turkish_students_note',
        'is_published', 'is_featured', 'sort_order', 'last_verified_at',
    ];

    protected $casts = [
        'monthly_fee_eur'       => 'decimal:2',
        'monthly_fee_max_eur'   => 'decimal:2',
        'accepted_for_visa'     => 'boolean',
        'accepted_for_enrollment' => 'boolean',
        'covers_dental'         => 'boolean',
        'covers_pregnancy'      => 'boolean',
        'covers_mental_health'  => 'boolean',
        'covers_repatriation'   => 'boolean',
        'digital_signup'        => 'boolean',
        'english_support'       => 'boolean',
        'is_published'          => 'boolean',
        'is_featured'           => 'boolean',
        'supported_languages'   => 'array',
        'pros'                  => 'array',
        'cons'                  => 'array',
        'features'              => 'array',
        'last_verified_at'      => 'datetime',
    ];

    /**
     * Sigorta tipleri. label çeviri için __() ile sarılır; emoji e_icon ile SVG'ye map'lenir.
     */
    public const TYPES = [
        'public'  => ['label' => 'Gesetzlich (GKV)', 'emoji' => '🏛️', 'color' => 'emerald'],
        'private' => ['label' => 'Privat (PKV)',     'emoji' => '⚡',  'color' => 'indigo'],
        'expat'   => ['label' => 'Expat / Incoming',  'emoji' => '🌍', 'color' => 'amber'],
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('is_featured', true);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type]['label'] ?? $this->type;
    }

    public function getTypeEmojiAttribute(): string
    {
        return self::TYPES[$this->type]['emoji'] ?? '🩺';
    }

    /**
     * Aylık fiyatı insanca: "€125" veya "€30–90" (aralık) ya da "—".
     */
    public function getMonthlyRangeAttribute(): string
    {
        $min = $this->monthly_fee_eur !== null ? (float) $this->monthly_fee_eur : null;
        $max = $this->monthly_fee_max_eur !== null ? (float) $this->monthly_fee_max_eur : null;

        if ($min === null && $max === null) {
            return '—';
        }
        if ($max !== null && $min !== null && $max > $min) {
            return '€' . number_format($min, 0) . '–' . number_format($max, 0);
        }
        return '€' . number_format($min ?? $max, 0);
    }

    /**
     * Yıllık tahmini maliyet (sıralama için) — aralık varsa alt sınırı baz alır.
     */
    public function getYearlyEstimateEurAttribute(): ?float
    {
        $min = $this->monthly_fee_eur !== null ? (float) $this->monthly_fee_eur : null;
        if ($min === null) {
            return null;
        }
        return round($min * 12, 2);
    }

    public function getCtaUrlAttribute(): ?string
    {
        return $this->affiliate_url ?: $this->website_url;
    }

    /**
     * best_for kontrollü anahtar → locale-aware etiket (serbest-metin TR sızıntısını önler).
     */
    public function getBestForLabelAttribute(): ?string
    {
        if (! $this->best_for) {
            return null;
        }

        return match ($this->best_for) {
            'public_standard' => __('Enrolled students under 30'),
            'over_30'         => __('Students over 30 / PhD / scholars'),
            'pre_enrollment'  => __('Language course / Studienkolleg phase'),
            'non_eu_incoming' => __('New arrivals / first months'),
            default           => $this->best_for,
        };
    }
}

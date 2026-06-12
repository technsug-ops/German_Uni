<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BlockedAccountProvider extends Model
{
    use \App\Models\Concerns\LocalizableContent;

    protected $fillable = [
        'slug', 'name', 'logo_url', 'website_url', 'affiliate_url',
        'type', 'backend_bank',
        'setup_fee_eur', 'monthly_fee_eur', 'yearly_fee_eur',
        'activation_days_min', 'activation_days_max',
        'combo_insurance', 'insurance_provider_name', 'insurance_monthly_eur',
        'monthly_withdrawal_limit_eur', 'required_yearly_deposit_eur',
        'has_mobile_app', 'bafin_licensed', 'supported_languages',
        'description_tr', 'description_en', 'description_de', 'description_long', 'pros', 'cons', 'features',
        'visa_recognition_note', 'turkish_students_note',
        'is_published', 'is_featured', 'sort_order', 'last_verified_at',
    ];

    protected $casts = [
        'setup_fee_eur'        => 'decimal:2',
        'monthly_fee_eur'      => 'decimal:2',
        'yearly_fee_eur'       => 'decimal:2',
        'insurance_monthly_eur'=> 'decimal:2',
        'combo_insurance'      => 'boolean',
        'has_mobile_app'       => 'boolean',
        'bafin_licensed'       => 'boolean',
        'is_published'         => 'boolean',
        'is_featured'          => 'boolean',
        'supported_languages'  => 'array',
        'pros'                 => 'array',
        'cons'                 => 'array',
        'features'             => 'array',
        'last_verified_at'     => 'datetime',
    ];

    public const TYPES = [
        'fintech'          => ['label' => 'FinTech', 'emoji' => '⚡', 'color' => 'indigo'],
        'traditional_bank' => ['label' => 'Geleneksel Banka', 'emoji' => '🏛️', 'color' => 'amber'],
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
        return self::TYPES[$this->type]['emoji'] ?? '🏦';
    }

    public function getFirstYearCostEurAttribute(): ?float
    {
        $setup = (float) ($this->setup_fee_eur ?? 0);
        $monthly = (float) ($this->monthly_fee_eur ?? 0);
        $yearly = (float) ($this->yearly_fee_eur ?? 0);

        if (! $setup && ! $monthly && ! $yearly) {
            return null;
        }

        return round($setup + ($monthly * 12) + $yearly, 2);
    }

    public function getActivationRangeAttribute(): string
    {
        // Birim locale-aware (gün / days / Tage) — EN/DE sayfalarda TR sızıntısını önle.
        $unit = __('days');
        if ($this->activation_days_min && $this->activation_days_max) {
            if ($this->activation_days_min === $this->activation_days_max) {
                return $this->activation_days_min . ' ' . $unit;
            }
            return "{$this->activation_days_min}–{$this->activation_days_max} {$unit}";
        }
        if ($this->activation_days_min) {
            return $this->activation_days_min . '+ ' . $unit;
        }
        return '—';
    }

    public function getCtaUrlAttribute(): ?string
    {
        return $this->affiliate_url ?: $this->website_url;
    }

    /** affiliate_clicks.provider_type değeri + /go/{type}/ segmenti. */
    public const AFFILIATE_TYPE = 'sperrkonto';

    /** Takipli dış-link: /go/sperrkonto/{slug}?ctx=... → tıklama loglanır, sonra cta_url'e 302. */
    public function trackedUrl(?string $ctx = null): string
    {
        return route('affiliate.go', array_filter([
            'type' => self::AFFILIATE_TYPE,
            'slug' => $this->slug,
            'ctx'  => $ctx,
        ]));
    }
}

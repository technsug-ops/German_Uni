<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Studienkolleg extends Model
{
    protected $fillable = [
        'name', 'slug', 'type', 'city_id', 'city_name_cache', 'state_id', 'university_id',
        'tracks', 'website_url', 'email', 'phone', 'address',
        'established_year', 'capacity_per_year', 'semester_fee_eur', 'entrance_exam',
        'description', 'admission_requirements', 'notes',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'tracks' => 'array',
        'description' => 'array',
        'admission_requirements' => 'array',
        'notes' => 'array',
        'is_active' => 'boolean',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    /**
     * JSON kolondan current locale'a göre lokalize değer döndürür.
     * Fallback: locale → en → de → tr → ilk dolu.
     */
    public function localized(string $field, ?string $locale = null): mixed
    {
        $data = $this->getAttribute($field);
        if (! is_array($data) || empty($data)) {
            return null;
        }
        $locale ??= app()->getLocale();
        $chain = config("locale.content_fallback.$locale", [$locale, 'en', 'de', 'tr']);
        foreach ($chain as $loc) {
            if (! empty($data[$loc])) {
                return $data[$loc];
            }
        }
        // Last resort: ilk dolu değer
        foreach ($data as $v) {
            if (! empty($v)) return $v;
        }
        return null;
    }

    public function getDescriptionLocalizedAttribute(): ?string
    {
        return $this->localized('description');
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'staatlich' ? __('Public (state-funded)') : __('Private');
    }

    public static function trackLabels(): array
    {
        return [
            'T' => __('T-Kurs (Engineering / Natural Sciences)'),
            'M' => __('M-Kurs (Medicine / Biology)'),
            'W' => __('W-Kurs (Economics / Social Sciences)'),
            'G' => __('G-Kurs (Humanities)'),
            'S' => __('S-Kurs (Languages / Philology)'),
        ];
    }
}

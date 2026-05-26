<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'wikidata_id', 'state_id',
        'name_tr', 'name_de', 'name_en', 'slug',
        'latitude', 'longitude', 'population',
        'is_active', 'image_url',
        'content_blocks', 'last_enriched_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
        'content_blocks' => 'array',
        'private_chain_slugs' => 'array',
        'last_enriched_at' => 'datetime',
    ];

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        $key = 'name_' . $locale; // name_tr, name_en, name_de
        $value = $this->attributes[$key] ?? null;
        if (empty($value)) {
            // fallback: en → de → tr
            foreach (['name_en', 'name_de', 'name_tr'] as $fb) {
                if (! empty($this->attributes[$fb] ?? null)) {
                    $value = $this->attributes[$fb];
                    break;
                }
            }
        }

        // TR locale'de Almanca disambiguation kuyruğunu gizle:
        //   "Frankfurt am Main" → "Frankfurt"
        //   "Neustadt an der Weinstraße" → "Neustadt"
        // "Frankfurt (Oder)" gibi parantezli ayrımlar etkilenmez (TR'de gerekli).
        if ($locale === 'tr' && $value) {
            $value = preg_replace('/\s+am\s+\S+$/u', '', $value);
            $value = preg_replace('/\s+an\s+der\s+\S+$/u', '', $value);
        }

        return $value ?: '';
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function universities(): HasMany
    {
        return $this->hasMany(University::class);
    }

    public function costData(): HasOne
    {
        return $this->hasOne(CityCostData::class);
    }

    // HTTPS-force accessor (mixed-content fix; Wikimedia URLs may be stored as http://)
    public function getImageUrlAttribute(?string $value): ?string
    {
        return $value ? preg_replace('#^http://#i', 'https://', $value) : null;
    }
}

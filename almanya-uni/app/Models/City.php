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
    use \App\Models\Concerns\FulltextSearch;

    protected $fillable = [
        'wikidata_id', 'state_id',
        'name_tr', 'name_de', 'name_en', 'slug',
        'latitude', 'longitude', 'population',
        'is_active', 'image_url',
        'content_blocks', 'content_blocks_en', 'content_blocks_de', 'last_enriched_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
        'content_blocks' => 'array',
        'content_blocks_en' => 'array',
        'content_blocks_de' => 'array',
        'private_chain_slugs' => 'array',
        'last_enriched_at' => 'datetime',
    ];

    /**
     * Locale-aware enrichment blocks. TR → source content_blocks; EN/DE →
     * translated content_blocks_{locale} (null until translated, so the blade
     * hides them instead of leaking Turkish). See doc/MULTILANG-PLAN (Enrichment-B).
     */
    public function localizedBlocks(?string $locale = null): ?array
    {
        $locale ??= app()->getLocale();
        if ($locale === 'tr') {
            return $this->content_blocks;
        }
        return $this->{'content_blocks_' . $locale} ?: null;
    }

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

    /**
     * Bu şehirde EK kampüsü olan üniler (birincil şehri başka ama burada da fakültesi var).
     * Çok-kampüslü üniler için (ör. Duisburg sayfasında Universität Duisburg-Essen).
     */
    public function campusUniversities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(University::class, 'university_campuses');
    }

    public function costData(): HasOne
    {
        return $this->hasOne(CityCostData::class);
    }

    // HTTPS-force + local WebP cache (Wikimedia rate-limit immunity) + thumbnail size accessor
    public function getImageUrlAttribute(?string $value): ?string
    {
        // Local WebP cache (populated by `php artisan images:cache-hot`)
        if ($this->slug) {
            $localFile = public_path("img/cache/cities/{$this->slug}.webp");
            if (file_exists($localFile)) {
                return asset("img/cache/cities/{$this->slug}.webp");
            }
        }

        if (! $value) return null;
        $value = preg_replace('#^http://#i', 'https://', $value);
        return wikimedia_thumb($value, 500); // Cards display 186-290px — 500px covers retina
    }
}

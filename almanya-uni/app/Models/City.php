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
        if (! empty($this->attributes[$key] ?? null)) {
            return $this->attributes[$key];
        }
        // fallback: en → de → tr
        foreach (['name_en', 'name_de', 'name_tr'] as $fb) {
            if (! empty($this->attributes[$fb] ?? null)) return $this->attributes[$fb];
        }
        return '';
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
}

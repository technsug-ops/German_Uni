<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'wikidata_id',
        'name_tr',
        'name_de',
        'name_en',
        'slug',
        'capital',
        'population',
        'image_url',
        'content_blocks',
        'last_enriched_at',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
        'content_blocks' => 'array',
        'last_enriched_at' => 'datetime',
    ];

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        $key = 'name_' . $locale;
        if (! empty($this->attributes[$key] ?? null)) {
            return $this->attributes[$key];
        }
        foreach (['name_en', 'name_de', 'name_tr'] as $fb) {
            if (! empty($this->attributes[$fb] ?? null)) return $this->attributes[$fb];
        }
        return '';
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function universities()
    {
        return $this->hasManyThrough(University::class, City::class);
    }
}

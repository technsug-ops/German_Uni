<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventCategory extends Model
{
    protected $fillable = [
        'slug', 'name_tr', 'name_en', 'name_de', 'icon', 'color', 'description', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Aktif dilde kategori adı: name_{locale} → name_en → name_tr (ilk dolu).
     * View'ler $cat->name kullanmalı; $cat->name_tr ham TR sızdırır.
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        foreach (["name_{$locale}", 'name_en', 'name_tr'] as $col) {
            if (! empty($this->attributes[$col] ?? null)) {
                return $this->attributes[$col];
            }
        }
        return '';
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

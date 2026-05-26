<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldOfStudy extends Model
{
    use HasFactory;

    protected $table = 'fields_of_study';

    protected $fillable = [
        'slug',
        'name_tr',
        'name_de',
        'name_en',
        'description_tr',
        'icon',
        'color',
        'image_url',
        'content_blocks',
        'last_enriched_at',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'content_blocks' => 'array',
        'last_enriched_at' => 'datetime',
    ];

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        $key = 'name_' . $locale;
        if (! empty($this->attributes[$key] ?? null)) return $this->attributes[$key];
        foreach (['name_en', 'name_de', 'name_tr'] as $fb) {
            if (! empty($this->attributes[$fb] ?? null)) return $this->attributes[$fb];
        }
        return '';
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function professions(): HasMany
    {
        return $this->hasMany(\App\Models\Profession::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}

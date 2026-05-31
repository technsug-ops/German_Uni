<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqTopic extends Model
{
    use HasFactory;
    use \App\Models\Concerns\LocalizableContent;

    protected $fillable = [
        'name',
        'name_tr',
        'name_en',
        'name_de',
        'slug',
        'icon',
        'description',
        'color',
        'pool_size',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'pool_size' => 'integer',
        'sort_order' => 'integer',
    ];

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mentor extends Model
{
    protected $fillable = [
        'user_id', 'name', 'slug', 'headline', 'avatar_url',
        'current_role', 'current_company', 'bio',
        'university', 'field_of_study', 'graduation_year', 'city',
        'linkedin_url', 'twitter_url', 'github_url', 'website_url', 'calendly_url', 'contact_email',
        'topics', 'languages', 'availability', 'rate_eur', 'session_duration',
        'is_featured', 'is_active', 'sessions_count', 'rating_avg', 'rating_count', 'sort_order',
    ];

    protected $casts = [
        'topics'      => 'array',
        'languages'   => 'array',
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
        'rate_eur'    => 'decimal:2',
        'rating_avg'  => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('is_featured', true);
    }

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', trim($this->name));
        return mb_strtoupper(mb_substr($parts[0] ?? '?', 0, 1) . mb_substr(end($parts), 0, 1));
    }

    public function getIsFreeAttribute(): bool
    {
        return (float) $this->rate_eur === 0.0;
    }

    /**
     * Mentor için en uygun "iletişim kur" URL'i:
     * 1. Calendly (varsa)
     * 2. Email mailto
     * 3. LinkedIn
     */
    public function getContactUrlAttribute(): ?string
    {
        if ($this->calendly_url) return $this->calendly_url;
        if ($this->contact_email) return 'mailto:' . $this->contact_email . '?subject=' . urlencode('Mentorluk Talebi · AlmanyaUni');
        return $this->linkedin_url;
    }
}

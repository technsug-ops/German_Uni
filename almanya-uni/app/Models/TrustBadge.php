<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TrustBadge extends Model
{
    protected $fillable = [
        'platform', 'display_name', 'logo_url', 'profile_url',
        'rating', 'review_count', 'badge_html',
        'slot', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'rating'       => 'decimal:1',
        'is_active'    => 'boolean',
    ];

    public const SLOTS = [
        'footer' => '📍 Footer (varsayılan)',
        'hero'   => '🎯 Hero / üst banner',
        'about'  => 'ℹ️ About page',
    ];

    public const PLATFORM_PRESETS = [
        'trustpilot'        => 'Trustpilot',
        'google_reviews'    => 'Google Reviews',
        'capterra'          => 'Capterra',
        'g2'                => 'G2',
        'facebook'          => 'Facebook',
        'youtube'           => 'YouTube',
        'instagram'         => 'Instagram',
        'linkedin'          => 'LinkedIn',
        'producthunt'       => 'Product Hunt',
        'featured_in_press' => '"Featured in" Basın',
    ];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeForSlot(Builder $q, string $slot): Builder
    {
        return $q->where('slot', $slot)->orderBy('sort_order');
    }
}

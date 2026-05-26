<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $fillable = [
        'platform', 'label', 'url', 'group', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const GROUPS = [
        'primary'   => 'Birincil (içerik)',
        'community' => 'Topluluk (mesajlaşma)',
    ];

    /** Footer'da gösterilecek: aktif + URL dolu, sıralı, gruplu. */
    public function scopeVisible(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->whereNotNull('url')
            ->where('url', '!=', '')
            ->orderBy('sort_order');
    }
}

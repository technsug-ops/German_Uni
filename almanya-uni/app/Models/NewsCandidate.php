<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Yayın öncesi haber adayı (gelen kutusu). 3 origin: auto | link | manual.
 * Onaylanınca NewsService::publish() ile type='news' Post grubuna dönüşür.
 */
class NewsCandidate extends Model
{
    public const STATUS_PENDING   = 'pending';
    public const STATUS_APPROVED  = 'approved';
    public const STATUS_REJECTED  = 'rejected';
    public const STATUS_PUBLISHED = 'published';

    public const ORIGIN_AUTO   = 'auto';
    public const ORIGIN_LINK   = 'link';
    public const ORIGIN_MANUAL = 'manual';

    protected $fillable = [
        'origin', 'status',
        'source_name', 'source_url', 'url_hash', 'orig_title', 'raw_excerpt',
        'fetched_content', 'image_url', 'event_date',
        'suggested_category_id', 'primary_locale', 'priority',
        'draft_title', 'draft_excerpt', 'draft_md',
        'meta', 'published_group_id', 'decided_at',
    ];

    protected $casts = [
        'meta'        => 'array',
        'event_date'  => 'date',
        'decided_at'  => 'datetime',
        'priority'    => 'integer',
    ];

    public function suggestedCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'suggested_category_id');
    }

    public function scopePending(Builder $q): Builder
    {
        return $q->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    /** Bir taslağı (AI veya manuel) hazır mı? (yayınlanabilir mi) */
    public function hasDraft(): bool
    {
        return ! empty($this->draft_title) && ! empty($this->draft_md);
    }

    public static function hashUrl(?string $url): ?string
    {
        return $url ? sha1(rtrim(strtolower(trim($url)), '/')) : null;
    }
}

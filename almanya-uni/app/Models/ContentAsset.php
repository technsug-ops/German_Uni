<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentAsset extends Model
{
    protected $fillable = [
        'content_brief_id', 'asset_type', 'spec',
        'body_md', 'body_html',
        'media', 'video_path', 'audio_path',
        'generated_by', 'prompt_used',
        'status', 'published_at', 'published_url', 'published_meta',
    ];

    protected $casts = [
        'spec' => 'array',
        'media' => 'array',
        'published_meta' => 'array',
        'published_at' => 'datetime',
    ];

    public const TYPES = [
        'blog'          => '📝 Blog (Markdown SEO)',
        'youtube_long'  => '🎬 YouTube Long (5-15 dk)',
        'youtube_short' => '⏱️ YouTube Shorts (60s)',
        'tiktok'        => '🎵 TikTok (30-60s)',
        'instagram'     => '📸 Instagram (Carousel + Reel)',
        'twitter'       => '🐦 Twitter/X Thread',
        'linkedin'      => '💼 LinkedIn Post',
        'pinterest'     => '📌 Pinterest Pin',
        'podcast'       => '🎙 Podcast Outline',
        'newsletter'    => '📧 Newsletter Section',
        'visual_brief'  => '🎨 Visual Brief (AI prompts)',
    ];

    public const STATUSES = [
        'draft'     => 'Taslak',
        'ready'     => 'Hazır',
        'scheduled' => 'Zamanlanmış',
        'published' => 'Yayında',
    ];

    public function brief(): BelongsTo
    {
        return $this->belongsTo(ContentBrief::class, 'content_brief_id');
    }
}

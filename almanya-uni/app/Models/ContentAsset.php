<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentAsset extends Model
{
    protected $fillable = [
        'content_brief_id', 'asset_type', 'language', 'source_asset_id', 'spec',
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
        'blog'             => '📝 Blog (Markdown SEO)',
        'youtube_long'     => '🎬 YouTube Long (5-15 dk)',
        'youtube_short'    => '⏱️ YouTube Shorts (60s)',
        'tiktok'           => '🎵 TikTok (30-60s)',
        'instagram'        => '📸 Instagram (Carousel + Reel)',
        'twitter'          => '🐦 Twitter/X Thread',
        'linkedin'         => '💼 LinkedIn Post',
        'pinterest'        => '📌 Pinterest Pin',
        'podcast'          => '🎙 Podcast Outline',
        'newsletter'       => '📧 Newsletter Section',
        'visual_brief'     => '🎨 Visual Brief (AI prompts)',
        // NEW formats (deutschland.de multi-format storytelling)
        'infographic_data' => '📊 Infographic JSON (data + callouts)',
        'faq_page'         => '❓ FAQ Page (Schema.org FAQPage)',
        'quiz'             => '🎯 Interactive Quiz (5 questions JSON)',
        'social_carousel'  => '🎠 Social Carousel (10-slide deck)',
        'email_sequence'   => '📩 Email Sequence (5-step drip)',
    ];

    public const LANGUAGES = [
        'tr' => '🇹🇷 Türkçe',
        'en' => '🇬🇧 English',
        'de' => '🇩🇪 Deutsch',
        'fr' => '🇫🇷 Français',
        'es' => '🇪🇸 Español',
        'it' => '🇮🇹 Italiano',
        'pl' => '🇵🇱 Polski',
        'ru' => '🇷🇺 Русский',
        'ar' => '🇸🇦 العربية',
        'fa' => '🇮🇷 فارسی',
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

    public function sourceAsset(): BelongsTo
    {
        return $this->belongsTo(ContentAsset::class, 'source_asset_id');
    }

    public function translations()
    {
        return $this->hasMany(ContentAsset::class, 'source_asset_id');
    }
}

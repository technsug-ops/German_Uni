<?php

namespace App\Models;

use App\Support\MarkdownRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'translation_group_id',
        'type',
        'source_url',
        'source_name',
        'event_date',
        'news_priority',
        'user_id',
        'co_author_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content_md',
        'content_html',
        'featured_image',
        'featured_image_caption',
        'audio_url',
        'audio_duration_seconds',
        'video_url',
        'gallery_images',
        'reading_minutes',
        'view_count',
        'meta_title',
        'meta_description',
        'published_at',
        'is_published',
    ];

    protected $casts = [
        'is_published'           => 'boolean',
        'published_at'           => 'datetime',
        'reading_minutes'        => 'integer',
        'view_count'             => 'integer',
        'audio_duration_seconds' => 'integer',
        'gallery_images'         => 'array',
        'event_date'             => 'date',
        'news_priority'          => 'integer',
    ];

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PostComment::class)->orderByDesc('is_pinned')->orderBy('created_at');
    }

    /** Same translation_group_id, different locale (TR/EN/DE/FR variants of this post). */
    public function siblings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'translation_group_id', 'translation_group_id');
    }

    public function approvedComments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PostComment::class)
            ->where('status', 'approved')
            ->whereNull('parent_id')
            ->with(['user:id,name,slug,avatar_url', 'replies.user:id,name,slug,avatar_url'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at');
    }

    public function getFormattedAudioDurationAttribute(): ?string
    {
        if (! $this->audio_duration_seconds) return null;
        $m = intdiv($this->audio_duration_seconds, 60);
        $s = $this->audio_duration_seconds % 60;
        return sprintf('%d:%02d', $m, $s);
    }

    protected static function booted(): void
    {
        static::saving(function (self $post) {
            if ($post->isDirty('content_md')) {
                $html = app(MarkdownRenderer::class)->render($post->content_md ?? '');
                // Otomatik iç-linkleme: glossary tooltip + şehir/üni entity linkleri
                $post->content_html = app(\App\Services\Content\BlogAutoLinker::class)->process($html, locale: $post->locale);
                $post->reading_minutes = self::computeReadingMinutes($post->content_md ?? '');
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coAuthor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'co_author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function engagements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PostEngagement::class);
    }

    /** #12 storytelling: yazının kaynak içerik brief'i (çok-formatlı asset'ler buradan). */
    public function contentBrief(): BelongsTo
    {
        return $this->belongsTo(ContentBrief::class);
    }

    /**
     * Bu yazının locale'ine uygun, ÜRETİLMİŞ infografik verisi (varsa). On-site
     * storytelling — blog sayfasında görsel infografik olarak render edilir.
     */
    public function infographicData(): ?array
    {
        if (! $this->content_brief_id) {
            return null;
        }
        $asset = ContentAsset::where('content_brief_id', $this->content_brief_id)
            ->where('asset_type', 'infographic_data')
            ->where('language', $this->locale)
            ->whereIn('status', ['ready', 'published'])
            ->latest('id')
            ->first();
        if (! $asset || empty($asset->body_md)) {
            return null;
        }
        // Gemini ```json fence'i kalmışsa temizle (savunmacı)
        $raw = preg_replace('/^```(?:json)?\s*|\s*```$/', '', trim((string) $asset->body_md));
        $json = json_decode((string) $raw, true);
        return is_array($json) && ! empty($json['title']) ? $json : null;
    }

    /**
     * #12 Faz-2: bu yazının "sesli makale" (podcast) mp3'ü varsa public URL'i.
     * Dosya public/audio/podcasts/{group}-{locale}.mp3 (deploy bundle ile gelir,
     * prod DB bağımlılığı yok). storytelling:podcasts komutu üretir.
     */
    public function podcastUrl(): ?string
    {
        if (! $this->translation_group_id) {
            return null;
        }
        $rel = 'audio/podcasts/' . $this->translation_group_id . '-' . $this->locale . '.mp3';
        return is_file(public_path($rel)) ? asset($rel) : null;
    }

    /** Ortalama okuma derinliği (% scroll) — okunmuşluk göstergesi. */
    public function getAvgScrollAttribute(): int
    {
        return (int) round($this->engagements()->avg('scroll_depth') ?? 0);
    }

    /** Ortalama sayfada kalış süresi (saniye). */
    public function getAvgSecondsAttribute(): int
    {
        return (int) round($this->engagements()->avg('seconds') ?? 0);
    }

    /** Tamamlama oranı (% — yazıyı sonuna kadar okuyanlar). */
    public function getCompletionRateAttribute(): int
    {
        $total = $this->engagements()->count();
        if ($total === 0) return 0;
        return (int) round($this->engagements()->where('completed', true)->count() / $total * 100);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('locale', app()->getLocale());
    }

    public function scopeForLocale(Builder $query, ?string $locale = null): Builder
    {
        return $query->where('locale', $locale ?? app()->getLocale());
    }

    /** Sadece blog yazıları (haber değil). type yoksa eski kayıtlar blog sayılır. */
    public function scopeBlogType(Builder $query): Builder
    {
        return $query->where(fn ($w) => $w->where('type', 'blog')->orWhereNull('type'));
    }

    /** Sadece haberler. */
    public function scopeNews(Builder $query): Builder
    {
        return $query->where('type', 'news');
    }

    /**
     * Haber sıralaması: admin önceliği (yüksek önde) → en yeni → id.
     * Öncelik verilmezse (priority=0, varsayılan) saf "en yeni en önde".
     */
    public function scopeNewsOrder(Builder $query): Builder
    {
        return $query->orderByDesc('news_priority')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    public function translations()
    {
        return $this->hasMany(self::class, 'translation_group_id', 'translation_group_id');
    }

    public function metaTitleResolved(): string
    {
        return $this->meta_title ?: ($this->title . ' - AlmanyaUni');
    }

    public function metaDescriptionResolved(): string
    {
        return $this->meta_description
            ?: ($this->excerpt ?: trim(mb_substr(strip_tags($this->content_html ?? ''), 0, 160)));
    }

    public static function computeReadingMinutes(string $markdown): int
    {
        $words = preg_match_all('/[\p{L}\p{N}]+/u', $markdown);
        return max(1, (int) ceil($words / 220));
    }
}

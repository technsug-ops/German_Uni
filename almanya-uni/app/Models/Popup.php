<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    protected $fillable = [
        'key', 'theme', 'position', 'media_type',
        'title_tr', 'title_en', 'title_de',
        'body_tr', 'body_en', 'body_de',
        'image_url', 'video_url', 'video_autoplay', 'video_muted',
        'emoji', 'accent_color',
        'cta_label_tr', 'cta_label_en', 'cta_label_de',
        'cta_url', 'cta_external',
        'secondary_label_tr', 'secondary_label_en', 'secondary_label_de',
        'target_pages', 'exclude_pages', 'locales',
        'trigger', 'delay_ms',
        'dismiss_days', 'show_dismiss_button',
        'is_active', 'starts_at', 'ends_at', 'priority',
        'view_count', 'click_count', 'dismiss_count',
    ];

    protected $casts = [
        'target_pages'         => 'array',
        'exclude_pages'        => 'array',
        'locales'              => 'array',
        'cta_external'         => 'boolean',
        'video_autoplay'       => 'boolean',
        'video_muted'          => 'boolean',
        'show_dismiss_button'  => 'boolean',
        'is_active'            => 'boolean',
        'starts_at'            => 'datetime',
        'ends_at'              => 'datetime',
    ];

    public const MEDIA_TYPES = [
        'text'  => '📝 Sadece yazı',
        'image' => '🖼️ Görsel',
        'video' => '🎬 Video (YouTube / Vimeo / MP4)',
    ];

    /** Detect embed style for video_url. */
    public function videoProvider(): ?string
    {
        $u = (string) $this->video_url;
        if ($u === '') return null;
        if (preg_match('#youtube\.com/watch\?v=|youtu\.be/#i', $u)) return 'youtube';
        if (preg_match('#vimeo\.com/#i', $u)) return 'vimeo';
        if (preg_match('#\.mp4(\?|$)#i', $u)) return 'mp4';
        return 'iframe'; // fallback
    }

    /** YouTube + Vimeo regular URL → embed URL. */
    public function videoEmbedUrl(): ?string
    {
        $u = (string) $this->video_url;
        if ($u === '') return null;
        $params = [];
        if ($this->video_autoplay) $params[] = 'autoplay=1';
        if ($this->video_muted)    $params[] = 'mute=1&muted=1';

        // YouTube watch?v=ID or youtu.be/ID
        if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/)([\w-]+)#i', $u, $m)) {
            $sep = '?';
            return 'https://www.youtube.com/embed/' . $m[1] . ($params ? $sep . implode('&', $params) : '');
        }
        // Vimeo vimeo.com/ID
        if (preg_match('#vimeo\.com/(\d+)#i', $u, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1] . ($params ? '?' . implode('&', $params) : '');
        }
        return $u;
    }

    public const THEMES = [
        'gradient'      => '🎨 Gradient (vibrant — indigo→pink)',
        'minimal'       => '⚪ Minimal (white card, subtle shadow)',
        'banner_top'    => '📢 Top Banner (full-width strip)',
        'banner_bottom' => '📌 Bottom Banner',
        'side_card'     => '📦 Side card (bottom-right corner)',
        'fullscreen'    => '🖼️ Fullscreen overlay',
    ];

    public const POSITIONS = [
        'center'       => 'Merkez',
        'top'          => 'Üst',
        'bottom'       => 'Alt',
        'bottom_right' => 'Sağ alt köşe',
        'bottom_left'  => 'Sol alt köşe',
    ];

    public const TRIGGERS = [
        'page_load'   => '⚡ Sayfa açılır açılmaz',
        'scroll_50'   => '📜 %50 scroll olunca',
        'time_5s'     => '⏱️ 5 saniye sonra',
        'time_15s'    => '⏱️ 15 saniye sonra',
        'exit_intent' => '🚪 Çıkış niyetinde',
    ];

    /** Active + within schedule window */
    public function scopeLive(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->where(function ($qq) {
                $qq->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($qq) {
                $qq->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    /** Returns the localized title for current app locale (falls back en→de→tr). */
    public function title(): ?string
    {
        return $this->localized('title');
    }

    public function body(): ?string
    {
        return $this->localized('body');
    }

    public function ctaLabel(): ?string
    {
        return $this->localized('cta_label');
    }

    public function secondaryLabel(): ?string
    {
        return $this->localized('secondary_label');
    }

    /** Locale chain: current → en → de → tr → null. */
    private function localized(string $field): ?string
    {
        $locale = app()->getLocale();
        foreach ([$locale, 'en', 'de', 'tr'] as $loc) {
            $value = $this->attributes[$field . '_' . $loc] ?? null;
            if (! empty($value)) return $value;
        }
        return null;
    }

    /** Does this popup apply on the given route name + path? */
    public function appliesToRoute(?string $routeName, string $path): bool
    {
        // Locale filter
        $locales = (array) ($this->locales ?? []);
        if (! empty($locales) && ! in_array(app()->getLocale(), $locales, true)) {
            return false;
        }

        // Exclude pages
        if ($this->matchesAny($routeName, $path, (array) ($this->exclude_pages ?? []))) {
            return false;
        }

        // Target pages (empty = all)
        $targets = (array) ($this->target_pages ?? []);
        if (empty($targets)) return true;

        return $this->matchesAny($routeName, $path, $targets);
    }

    private function matchesAny(?string $routeName, string $path, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            $pattern = trim((string) $pattern);
            if ($pattern === '') continue;
            // route name (e.g. "scholarships.index")
            if (str_contains($pattern, '.') && $routeName === $pattern) return true;
            // path glob (e.g. "/blog/*", "/tools/*")
            if ($pattern === $path) return true;
            if (str_contains($pattern, '*')) {
                $regex = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#i';
                if (preg_match($regex, $path)) return true;
            }
        }
        return false;
    }

    /** Resolve the first matching active popup for the current request. */
    public static function forCurrentRequest(?string $routeName, string $path): ?self
    {
        return static::live()
            ->orderBy('priority')
            ->get()
            ->first(fn ($p) => $p->appliesToRoute($routeName, $path));
    }

    /**
     * Resolve ALL matching active popups for the current request.
     * Order: priority ascending. Limit defaults to 3 to avoid UX overload.
     */
    public static function allForCurrentRequest(?string $routeName, string $path, int $limit = 3): \Illuminate\Support\Collection
    {
        return static::live()
            ->orderBy('priority')
            ->get()
            ->filter(fn ($p) => $p->appliesToRoute($routeName, $path))
            ->take($limit)
            ->values();
    }
}

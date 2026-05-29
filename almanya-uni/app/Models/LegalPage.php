<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LegalPage extends Model
{
    protected $fillable = [
        'key',
        'titles',
        'descriptions',
        'bodies',
        'effective_date',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'titles' => 'array',
        'descriptions' => 'array',
        'bodies' => 'array',
        'effective_date' => 'date',
        'is_published' => 'boolean',
    ];

    public static function findByKey(string $key): ?self
    {
        // Cache deliberately disabled: prod was flapping 500s on /tr/impressum and
        // /tr/cerez-politikasi (same controller, same view, others worked) — telltale
        // sign of a poisoned serialized cache value across shared workers. Legal
        // pages are visited rarely; a single indexed lookup costs <1ms, the cache
        // saved nothing meaningful but cost us availability. Re-enable later if
        // traffic justifies, with a cache key version (e.g. legal_page_v2_$key).
        return self::where('key', $key)->where('is_published', true)->first();
    }

    public function getTitle(?string $locale = null): string
    {
        return $this->localized('titles', $locale) ?? ucfirst($this->key);
    }

    public function getDescription(?string $locale = null): ?string
    {
        return $this->localized('descriptions', $locale);
    }

    public function getBody(?string $locale = null): string
    {
        return $this->localized('bodies', $locale) ?? '';
    }

    /**
     * Render markdown body to HTML.
     * Uses Laravel's built-in Str::markdown when available; falls back to raw HTML.
     */
    public function getRenderedBody(?string $locale = null): string
    {
        $body = $this->getBody($locale);
        if (! $body) return '';

        // If it already looks like HTML (starts with a tag), return as-is.
        if (preg_match('/^\s*</', $body)) {
            return $body;
        }

        // Otherwise render Markdown.
        return \Illuminate\Support\Str::markdown($body, [
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * Locale fallback chain: requested → app locale → en → tr → de → first available.
     */
    private function localized(string $jsonField, ?string $locale): ?string
    {
        $values = $this->$jsonField ?? [];
        if (! is_array($values)) return null;

        $locale = $locale ?: app()->getLocale();
        $fallbackChain = [$locale, app()->getLocale(), 'en', 'tr', 'de'];

        foreach (array_unique($fallbackChain) as $loc) {
            if (! empty($values[$loc])) {
                return $values[$loc];
            }
        }

        // Last resort: first non-empty value
        foreach ($values as $v) {
            if (! empty($v)) return $v;
        }
        return null;
    }

    protected static function booted(): void
    {
        // Clear cache when admin updates
        static::saved(fn ($p) => Cache::forget("legal_page_$p->key"));
        static::deleted(fn ($p) => Cache::forget("legal_page_$p->key"));
    }
}

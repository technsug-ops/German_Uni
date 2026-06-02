<?php

if (! function_exists('brand')) {
    /**
     * Domain-aware brand info.
     *
     * almanyauni.com   → AlmanyaUni brand (her locale'da bu marka)
     * applytogerman.com → ApplyToGerman brand (her locale'da bu marka)
     *
     * Domain ile brand sabit, locale ile içerik dili değişir. İki kavram bağımsız:
     *   almanyauni.com/en       → AlmanyaUni brand + EN content
     *   applytogerman.com/tr    → ApplyToGerman brand + TR content
     *
     *   brand()              → tüm brand array
     *   brand('name')        → "AlmanyaUni" | "ApplyToGerman"
     *   brand('tagline')     → mevcut locale'da tagline string'i (multi-locale field)
     *   brand('logo')        → SVG path
     *
     * @param string|null $forceBrandKey  CLI / kuyruk işleri için brand'i zorla
     *                                    ('almanyauni' veya 'applytogerman')
     */
    function brand(?string $key = null, ?string $forceBrandKey = null): mixed
    {
        $brandKey = $forceBrandKey ?? brand_key();
        $brands = config('brand.brands', []);
        $b = $brands[$brandKey] ?? $brands[config('brand.fallback', 'almanyauni')] ?? [];

        if ($key === null) return $b;
        $val = $b[$key] ?? null;

        // Locale-keyed multi-value field (örn. tagline) → mevcut locale ile çöz
        if (is_array($val)) {
            $locale = app()->getLocale();
            return $val[$locale] ?? ($val['en'] ?? ($val['tr'] ?? reset($val) ?: null));
        }
        return $val;
    }
}

if (! function_exists('brand_key')) {
    /**
     * Mevcut request'in host'undan brand key'i çözer (almanyauni | applytogerman).
     * HTTP request yoksa (CLI/queue) fallback'e düşer.
     */
    function brand_key(?string $forceHost = null): string
    {
        $host = $forceHost;
        if (! $host) {
            try {
                $host = request()?->getHost();
            } catch (\Throwable $e) {
                $host = null;
            }
        }
        if ($host) {
            $host = strtolower(preg_replace('/^www\./', '', $host));
            $domains = config('brand.domains', []);
            if (isset($domains[$host])) {
                return $domains[$host];
            }
        }
        return config('brand.fallback', 'almanyauni');
    }
}

if (! function_exists('lroute')) {
    /**
     * Locale-aware route helper.
     * Current locale TR (default) ise route name'i olduğu gibi çağırır.
     * Diğer dillerde locale prefix ekler.
     *
     *   lroute('programs.index')                 → /programs (tr) | /en/programs (en) | /de/programs (de)
     *   lroute('programs.show', ['slug'=>'x'])   → /programs/x | /en/programs/x | ...
     */
    function lroute(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        // Locale prefix'i artık SetLocale middleware'inde URL::defaults(['locale'=>...]) ile
        // otomatik enjekte ediliyor. Bu yüzden düz route() çağrısı locale-aware'dir.
        // (lroute geriye dönük uyumluluk için korunuyor; route() ile özdeş.)
        return route($name, $parameters, $absolute);
    }
}

if (! function_exists('hochschulkompass_url')) {
    /**
     * Hochschulkompass'ın gelişmiş arama sayfasına derin link üretir.
     * NC bilgisi bizim DB'de yok — kullanıcıyı resmi kaynağa yönlendirir.
     *
     * @param string|null $query    Studiengang adı (örn. "Informatik")
     * @param string|null $mode     'zulassungsfrei' | 'oertlich' | 'bundesweit' | null
     */
    function hochschulkompass_url(?string $query = null, ?string $mode = null): string
    {
        $base = 'https://www.hochschulkompass.de/studium/studiengangsuche/erweiterte-studiengangsuche.html';
        $params = [];

        if ($query) {
            // Hochschulkompass form alanı (TYPO3 plugin syntax)
            $params['tx_szhrksearch_pi1[search]'] = $query;
        }

        if ($mode) {
            $modeMap = [
                'zulassungsfrei' => '1',   // ohne NC
                'oertlich'       => '2',   // örtlich zulassungsbeschränkt
                'bundesweit'     => '3',   // bundesweit
            ];
            if (isset($modeMap[$mode])) {
                $params['tx_szhrksearch_pi1[zulassungsmodus][]'] = $modeMap[$mode];
            }
        }

        return $params ? $base . '?' . http_build_query($params) : $base;
    }
}

if (! function_exists('localized_url')) {
    /**
     * Mevcut URL'in başka bir locale versiyonunu üretir (locale switcher için).
     */
    function localized_url(string $targetLocale): string
    {
        // Slug-bazlı sayfalar (blog/haber) farklı locale'de FARKLI slug kullanır
        // (posts.slug global unique). Controller, dil-kardeşlerin GERÇEK URL'lerini
        // 'localeUrls' ile paylaşır → naif prefix-swap'ın 404'ünü önler.
        $override = view()->shared('localeUrls');
        if (is_array($override) && ! empty($override[$targetLocale])) {
            return $override[$targetLocale];
        }

        $path = trim(request()->path(), '/');

        // Mevcut path'ten varsa locale prefix'i çıkar
        $locales = array_keys(config('locale.locales', []));
        foreach ($locales as $l) {
            if ($path === $l || str_starts_with($path, "$l/")) {
                $path = ltrim(substr($path, strlen($l)), '/');
                break;
            }
        }

        // Route definition tüm dillerde /{locale} prefix bekler (default dahil).
        // Default için prefix atlamak EN'ye geçişi kırardı (URL hiçbir route'a eşleşmezdi).
        $newUrl = url('/' . $targetLocale . '/' . $path);
        $query  = request()->getQueryString();
        return $query ? $newUrl . '?' . $query : $newUrl;
    }
}

if (! function_exists('wikimedia_original')) {
    /**
     * Convert any Wikimedia URL (thumb or Special:FilePath) to the ORIGINAL file URL.
     * Wikipedia (2024+) restricts which thumbnail widths are accessible per file —
     * arbitrary widths often return 400. The ORIGINAL is always 200 OK and we resize
     * locally with GD. Pass-through for non-Wikimedia URLs.
     */
    function wikimedia_original(?string $url): ?string
    {
        if (! $url) return $url;

        // Pattern 1 (thumb): .../commons/X/YY/Name.ext/NNNpx-Name.ext → .../commons/X/YY/Name.ext
        if (preg_match('#^(https?://upload\.wikimedia\.org/wikipedia/commons)/thumb/([0-9a-f]/[0-9a-f]{2})/([^/]+)/\d+px-[^/]+$#i', $url, $m)) {
            return $m[1] . '/' . $m[2] . '/' . $m[3];
        }

        // Pattern 2 (Special:FilePath): strip width query param. Resolves via redirect to upload.wikimedia.org/full.
        if (str_contains($url, 'commons.wikimedia.org/wiki/Special:FilePath')) {
            $url = preg_replace('/[?&]width=\d+/', '', $url);
            return rtrim($url, '?&');
        }

        // Pattern 3 (already original): pass-through (it's already /commons/X/YY/Name.ext with no /thumb/)
        return $url;
    }
}

if (! function_exists('wikimedia_thumb')) {
    /**
     * Rewrite a Wikimedia image URL to request a smaller thumbnail.
     *
     * Wikimedia serves images at any width when requested correctly:
     *   - upload.wikimedia.org/.../thumb/X/YY/NAME/NNNpx-NAME  → swap NNN to $width
     *   - commons.wikimedia.org/wiki/Special:FilePath/NAME?width=NNN  → swap or add width param
     *
     * Non-Wikimedia URLs pass through unchanged.
     *
     * Use case: cards display 186-290px wide, but DB-stored URLs request 960px+ images.
     * Rewriting to ~500px saves ~80% of bytes per image (Lighthouse "image delivery").
     */
    function wikimedia_thumb(?string $url, int $width = 500): ?string
    {
        if (! $url) return $url;

        // Pattern 1: upload.wikimedia.org thumbnail
        // .../thumb/x/yy/filename.jpg/960px-filename.jpg → swap "960px-" to "{width}px-"
        if (preg_match('#^https?://upload\.wikimedia\.org/.+?/\d+px-[^/]+$#', $url)) {
            return preg_replace('#/\d+px-([^/]+)$#', '/' . $width . 'px-$1', $url);
        }

        // Pattern 2: commons.wikimedia.org/wiki/Special:FilePath/... ?width=NNN
        if (str_contains($url, 'commons.wikimedia.org/wiki/Special:FilePath')) {
            // Strip any existing width param then append fresh
            $url = preg_replace('/([?&])width=\d+&?/', '$1', $url);
            $url = rtrim($url, '?&');
            $sep = str_contains($url, '?') ? '&' : '?';
            return $url . $sep . 'width=' . $width;
        }

        // Pattern 3: upload.wikimedia.org original file (no /thumb/) — construct thumb URL
        // .../commons/X/YY/Filename.ext → .../commons/thumb/X/YY/Filename.ext/NNNpx-Filename.ext
        if (preg_match('#^(https?://upload\.wikimedia\.org/wikipedia/commons/)([0-9a-f]/[0-9a-f]{2})/([^/?]+)$#i', $url, $m)) {
            $base = $m[1];
            $hashPath = $m[2];
            $filename = $m[3];
            $thumbName = $width . 'px-' . $filename;
            // SVG thumbnails are rasterized to PNG (Wikipedia convention)
            if (preg_match('/\.svg$/i', $filename)) {
                $thumbName .= '.png';
            }
            return $base . 'thumb/' . $hashPath . '/' . $filename . '/' . $thumbName;
        }

        return $url;
    }
}

if (! function_exists('e_icon')) {
    /**
     * Map a DB-stored emoji to an inline Heroicons outline SVG so the chrome
     * matches the rest of the modernised UI. Returns the original emoji
     * unchanged if we don't have a mapping for it (admins can still pick
     * exotic emojis from Filament for one-off pages).
     *
     * Usage in Blade:
     *   {!! e_icon($item->icon, 'w-5 h-5') !!}
     *
     * The mapping mirrors resources/views/components/svg-icon.blade.php's
     * naming, but we inline the SVG here so callers don't have to think
     * about the component vs. string-fallback split.
     */
    function e_icon(?string $emoji, string $class = 'w-5 h-5'): string
    {
        if (! $emoji) return '';

        // DB icon → Heroicons name (covers the icons currently stored in
        // MenuPage, FieldOfStudy and a few content tables).
        static $map = [
            '🎓' => 'academic-cap',
            '📚' => 'book-open',
            '🏙️' => 'building-office',
            '🏘️' => 'building-office',
            '🏛️' => 'building-office',
            '🌆' => 'building-office',
            '🗺️' => 'map',
            '🎯' => 'target',
            '🛠️' => 'wrench-screwdriver',
            '🔧' => 'wrench-screwdriver',
            '🔍' => 'search',
            '🎁' => 'sparkles',
            '🎖️' => 'sparkles',
            '🏆' => 'trophy',
            '🥇' => 'trophy',
            '💰' => 'banknotes',
            '💳' => 'banknotes',
            '📅' => 'calendar',
            '📆' => 'calendar',
            '🗓️' => 'calendar',
            '✨' => 'sparkles',
            '⭐' => 'star',
            '💡' => 'light-bulb',
            '🚀' => 'rocket-launch',
            '📍' => 'map-pin',
            '🔗' => 'link',
            '✅' => 'check-circle',
            '❌' => 'x-circle',
            '⚠️' => 'exclamation-triangle',
            'ℹ️' => 'information-circle',
            '🤝' => 'users',
            '👥' => 'users',
            '👤' => 'user',
            '🌍' => 'globe',
            '🌐' => 'globe',
            '💼' => 'briefcase',
            '🏠' => 'home',
            '⚖️' => 'scale',
            '📊' => 'chart-bar',
            '📈' => 'chart-bar',
            '📉' => 'chart-bar',
            '⚙️' => 'cog',
            '💻' => 'computer',
            '❓' => 'question-mark-circle',
            '💬' => 'chat-bubble',
            '🏦' => 'building-library',
            '🧭' => 'compass',
            '💸' => 'banknotes',
            '💶' => 'currency-euro',
            '📋' => 'list-bullet',
            '✉️' => 'envelope',
            '📧' => 'envelope',
            '🔔' => 'bell',
            '🎫' => 'tag',
            '🎟️' => 'tag',
            '🛒' => 'shopping-bag',
            '✏️' => 'pencil',
            '🔒' => 'lock-closed',
            '🔓' => 'lock-closed',
            '🏆' => 'trophy',
            '🥇' => 'trophy',
            '🥈' => 'trophy',
            '🥉' => 'trophy',
            '⏰' => 'clock',
            '⏳' => 'clock',
            '⌛' => 'clock',
            '🎨' => 'paint-brush',
            '🌾' => 'leaf',
            '🐾' => 'paw',
            '🐛' => 'beaker',
            '🌱' => 'leaf',
            '✨' => 'sparkles',
            '❤️' => 'heart',
            '🩺' => 'heart',
            '🏥' => 'heart',
            '✏️' => 'pencil',
            '📝' => 'pencil',
            '🧪' => 'beaker',
            '🔬' => 'beaker',
            '🎬' => 'photo',
            '🎭' => 'photo',
            '🎵' => 'sparkles',
        ];

        $name = $map[$emoji] ?? null;
        if (! $name) {
            // Unknown emoji — keep it (admin's deliberate choice for niche pages).
            // Wrap with a span so callers get consistent inline-block layout.
            return '<span class="inline-block">' . e($emoji) . '</span>';
        }

        // Render via the same component the rest of the UI uses, so the icon
        // catalogue stays single-sourced. The component handles class merging.
        return view('components.svg-icon', ['name' => $name, 'class' => $class])->render();
    }
}

if (! function_exists('localized_pick')) {
    /**
     * Tek lokalizasyon politikası — array/stdClass satırlar için (toArray edilmiş
     * model'ler, raw DB rows). Model nesneleri için App\Models\Concerns\
     * LocalizableContent::localized() ile AYNI mantık.
     *
     * "{field}_{loc}" kolonlarını config/locale.php fallback zincirine göre dener,
     * ilk dolu değeri döndürür. $strict=true (serbest-metin/prose) ise aktif dil TR
     * değilken zincirden 'tr' ÇIKARILIR → EN/DE sayfada Türkçe sızmaz (çeviri yoksa
     * null → blade gizler). name/başlık gibi kimlik alanlarında $strict=false bırak.
     */
    function localized_pick(array|object $row, string $field, ?string $locale = null, bool $strict = false): ?string
    {
        $locale ??= app()->getLocale();
        $chain = config("locale.content_fallback.$locale", config('locale.content_fallback_default', ['en', 'de', 'tr']));

        if ($strict && $locale !== 'tr') {
            $chain = array_values(array_filter($chain, fn ($l) => $l !== 'tr'));
        }

        foreach ($chain as $loc) {
            $key = "{$field}_{$loc}";
            $val = is_array($row) ? ($row[$key] ?? null) : ($row->{$key} ?? null);
            if (! empty($val)) {
                return $val;
            }
        }

        return null;
    }
}

if (! function_exists('setting')) {
    /**
     * Global key-value ayar oku (cache'li, settings tablosu).
     *
     *   setting('google_analytics_id')        → değer | null
     *   setting('foo', 'varsayılan')          → değer | 'varsayılan'
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return \App\Models\Setting::get($key, $default);
    }
}

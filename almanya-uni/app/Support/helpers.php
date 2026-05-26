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
        $current = app()->getLocale();
        $default = config('locale.default', 'tr');
        $path    = trim(request()->path(), '/');

        // Mevcut path'ten varsa locale prefix'i çıkar
        $locales = array_keys(config('locale.locales', []));
        foreach ($locales as $l) {
            if ($path === $l || str_starts_with($path, "$l/")) {
                $path = ltrim(substr($path, strlen($l)), '/');
                break;
            }
        }

        $prefix = $targetLocale === $default ? '' : "/$targetLocale";
        $newUrl = url($prefix . '/' . $path);
        $query  = request()->getQueryString();
        return $query ? $newUrl . '?' . $query : $newUrl;
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

        return $url;
    }
}

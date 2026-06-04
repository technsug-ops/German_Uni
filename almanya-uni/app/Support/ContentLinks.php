<?php

namespace App\Support;

/**
 * İçerik (blog/sayfa) HTML'indeki İÇ linkleri RENDER ANINDA mevcut locale'e çevirir.
 *
 * KÖK SORUN: route'lar {locale}/... önekli, marka default locale = en. İçerikteki
 * önek-siz linkler (/faq, /universities) önek yokken default'a (en) düşüyor → /tr
 * makalede bile /en'e gidiyor. Bu helper her iç <a href>'i bulunduğun dile prefixler.
 *
 * Hafif (DB yok) + güvenli: statik/asset yolları (/storage, /img, uzantılı dosyalar),
 * dış linkler, anchor/mailto/tel DOKUNULMAZ.
 */
class ContentLinks
{
    private const LOCALES = ['tr', 'en', 'de', 'fr'];

    /** Locale prefix'lenmeyecek statik/asset kök segmentleri. */
    private const SKIP_PREFIXES = [
        'storage', 'img', 'images', 'css', 'js', 'build', 'assets',
        'fonts', 'vendor', 'favicon', 'forum', 'rss.xml', 'sitemap.xml',
    ];

    public static function localizeHtml(?string $html, string $locale): string
    {
        if (! $html) {
            return (string) $html;
        }
        if (! in_array($locale, self::LOCALES, true)) {
            $locale = 'tr';
        }

        return (string) preg_replace_callback(
            '/\bhref\s*=\s*(["\'])(\/[^"\'#?\s][^"\']*)\1/i',
            function ($m) use ($locale) {
                $quote = $m[1];
                $path = $m[2];
                $fixed = self::localizePath($path, $locale);
                return 'href=' . $quote . $fixed . $quote;
            },
            $html
        );
    }

    /** Tek bir root-relative path'i mevcut locale'e çevirir (uygun değilse aynen döner). */
    public static function localizePath(string $path, string $locale): string
    {
        $segments = explode('/', ltrim($path, '/'));
        $first = $segments[0] ?? '';

        // Asset / statik / özel kök → dokunma
        if ($first === '' || in_array(strtolower($first), self::SKIP_PREFIXES, true)) {
            return $path;
        }
        // Dosya uzantılı son segment (ör. /x/y.webp) → asset, dokunma
        $last = $segments[count($segments) - 1];
        if (str_contains($last, '.') && preg_match('/\.[a-z0-9]{2,5}$/i', $last)) {
            return $path;
        }

        // Zaten locale önekli → sadece locale'i değiştir
        if (in_array($first, self::LOCALES, true)) {
            $segments[0] = $locale;
            return '/' . implode('/', $segments);
        }

        // Önek yok → mevcut locale'i başa ekle
        return '/' . $locale . '/' . implode('/', $segments);
    }
}

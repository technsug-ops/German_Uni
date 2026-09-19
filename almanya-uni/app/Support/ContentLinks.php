<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

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

        // Zaten locale önekli
        if (in_array($first, self::LOCALES, true)) {
            $rest = array_slice($segments, 1);

            // BLOG/HABER: slug dile GÖRE değişir (tr=temel, en=-en, de=-de). Öneki körlemesine
            // değiştirmek /en/blog/<tr-slug> gibi 404 üretiyordu — yazının o dildeki GERÇEK
            // kardeşini bul; yoksa linki olduğu dilde bırak (var olan sayfa, 404'ten iyidir).
            if (($rest[0] ?? null) !== null && in_array($rest[0], ['blog', 'news'], true) && isset($rest[1])) {
                $resolved = self::resolvePostSlug((string) $rest[1], $locale);
                if ($resolved === null) {
                    return $path; // hedef bilinmiyor → dokunma
                }
                [$slug, $slugLocale] = $resolved;

                return '/' . $slugLocale . '/' . $rest[0] . '/' . $slug;
            }

            $segments[0] = $locale;

            return '/' . implode('/', $segments);
        }

        // Önek yok → mevcut locale'i başa ekle (blog/haber ise yine kardeş çözümlemesi)
        if ($first !== '' && in_array($first, ['blog', 'news'], true) && isset($segments[1])) {
            $resolved = self::resolvePostSlug((string) $segments[1], $locale);
            if ($resolved !== null) {
                [$slug, $slugLocale] = $resolved;

                return '/' . $slugLocale . '/' . $first . '/' . $slug;
            }
        }

        return '/' . $locale . '/' . implode('/', $segments);
    }

    /**
     * Bir yazı slug'ını hedef dile çevir: [yeniSlug, kullanılacakLocale] | null.
     * Kardeşi varsa hedef dile geçer; yoksa yazının KENDİ dilinde bırakır (404 üretme).
     */
    private static function resolvePostSlug(string $slug, string $locale): ?array
    {
        $idx = self::postIndex();
        $ownLocale = $idx['locale'][$slug] ?? null;
        if ($ownLocale === null) {
            return null; // yayında böyle bir yazı yok → path'e dokunma
        }
        if ($ownLocale === $locale) {
            return [$slug, $locale];
        }

        $group = $idx['group'][$slug] ?? null;
        $sibling = $group ? ($idx['byGroup'][$group][$locale] ?? null) : null;

        return $sibling ? [$sibling, $locale] : [$slug, $ownLocale];
    }

    /** Yayındaki yazıların slug/locale/çeviri-grubu haritası (düz dizi olarak cache'lenir). */
    private static function postIndex(): array
    {
        return cache()->remember('content_links_post_index_v1', now()->addMinutes(30), function () {
            $index = ['locale' => [], 'group' => [], 'byGroup' => []];

            $rows = DB::table('posts')
                ->where('is_published', 1)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->get(['slug', 'locale', 'translation_group_id']);

            foreach ($rows as $r) {
                $loc = $r->locale ?: 'tr';
                $index['locale'][$r->slug] = $loc;
                if ($r->translation_group_id) {
                    $index['group'][$r->slug] = $r->translation_group_id;
                    $index['byGroup'][$r->translation_group_id][$loc] = $r->slug;
                }
            }

            return $index;
        });
    }
}

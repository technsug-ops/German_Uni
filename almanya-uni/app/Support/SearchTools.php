<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Kavram/sinonim arama katmanı. Kullanıcı hangi dilde/varyantta yazarsa
 * (TR/EN/DE), bir kavramı (araç/sayfa) bulur. Entity cross-language araması
 * zaten çok-sütun FULLTEXT ile çözülür; bu katman İSİM olmayan kavramları
 * (sperrkonto = blocked account = bloke hesap) yakalar.
 *
 * Registry: config/search_tools.php (kod değişmeden genişletilebilir).
 */
class SearchTools
{
    /**
     * Sorguya uyan araçları döndürür (locale başlıklı, hazır sonuç formatında).
     *
     * @return array<int,array{type:string,type_label:string,title:string,subtitle:?string,url:string,icon:string}>
     */
    public static function match(string $q, ?string $locale = null): array
    {
        $q = self::normalize($q);
        if (mb_strlen($q) < 3) {
            return [];
        }

        $locale = $locale ?: app()->getLocale();
        $out = [];

        foreach (config('search_tools', []) as $tool) {
            if (! self::matchesKeywords($q, $tool['keywords'] ?? [])) {
                continue;
            }
            $title = $tool['title'][$locale] ?? $tool['title']['en'] ?? reset($tool['title']);
            $out[] = [
                'type' => 'tool',
                'type_label' => '🔧 ' . __('Tool'),
                'title' => $title,
                'subtitle' => null,
                'url' => self::url($tool['route']),
                'icon' => $tool['icon'] ?? '🔧',
            ];
        }

        return $out;
    }

    private static function matchesKeywords(string $q, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            $kw = self::normalize($kw);
            if ($kw === '') continue;
            // Tam, prefix ya da içerme (her iki yön) — aksan-folding'li
            if ($kw === $q || str_contains($kw, $q) || str_contains($q, $kw)) {
                return true;
            }
        }
        return false;
    }

    /** Küçük harf + aksan/diakritik sadeleştirme (ü→u, ç→c, ß→ss vb.) + trim. */
    private static function normalize(string $s): string
    {
        $s = mb_strtolower(trim($s));
        $map = [
            'ä' => 'a', 'ö' => 'o', 'ü' => 'u', 'ß' => 'ss',
            'ı' => 'i', 'İ' => 'i', 'ş' => 's', 'ğ' => 'g', 'ç' => 'c',
            'â' => 'a', 'î' => 'i', 'û' => 'u', 'é' => 'e', 'è' => 'e',
        ];
        return strtr($s, $map);
    }

    private static function url(string $routeName): string
    {
        try {
            return \Illuminate\Support\Facades\Route::has($routeName) ? route($routeName) : '/';
        } catch (\Throwable $e) {
            return '/';
        }
    }
}

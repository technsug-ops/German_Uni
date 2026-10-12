<?php

namespace App\Support;

/**
 * Eski (Türkçe / önceki tur) yol takma adlarını ve taşınmış slug'ları TEK adımda son yola çözer.
 *
 * Kullanım: Route::fallback ve locale'siz eski bağlantılar. Eskiden /sss/{konu}/{soru} → /faq/… (301) →
 * /{locale}/faq/… (302) → yeni slug (301) üç adım sürüyordu; burada hepsi tek hesaplamada birleşir.
 * Yalnız bilinen takma adlar ve slug_redirects kayıtları uygulanır; bilinmeyen yol null döner (gerçek 404).
 */
final class LegacyPaths
{
    /** Tam yol eşlemeleri (locale'siz, baştaki/sondaki '/' olmadan). */
    private const EXACT = [
        'arama' => 'search', 'biz-kimiz' => 'about', 'sss' => 'faq', 'karsilastir' => 'compare',
        'karsilastir/sonuc' => 'compare/result', 'siralama' => 'rankings', 'programlar' => 'programs',
        'meslekler' => 'professions', 'harita' => 'map', 'araclar' => 'tools',
        'araclar/yasam-maliyeti' => 'tools/cost-of-living', 'araclar/not-donusturucu' => 'tools/grade-converter',
        'araclar/uni-onerisi' => 'tools/recommendation', 'araclar/kariyer-pusulasi' => 'tools/career-compass',
        'araclar/sperrkonto' => 'tools/sperrkonto', 'tools/bloke-hesap' => 'tools/sperrkonto',
        'araclar/vize-randevu' => 'tools/visa-appointment', 'tools/vize-randevu' => 'tools/visa-appointment',
        'tools/dil-sertifikalari' => 'tools/language-certificates', 'tools/saglik-sigortasi' => 'tools/health-insurance',
        'araclar/saglik-sigortasi' => 'tools/health-insurance',
        'reklam' => 'advertise', 'tum-sayfalar' => 'all-pages', 'danisma-kurulu' => 'advisory-board', 'ekip' => 'team',
        'yazarlar' => 'team', 'almanyada-egitim' => 'study-in-germany', 'about-germany' => 'study-in-germany',
        'neden-almanya' => 'study-in-germany', 'gizlilik' => 'privacy', 'kosullar' => 'terms',
        'cerez-politikasi' => 'cookie-policy', 'yasal-uyari' => 'disclaimer', 'haberler' => 'news',
        'etkinlikler' => 'events', 'mentorlar' => 'mentors', 'deneyim-paylas' => 'contribute', 'iletisim' => 'contact',
        'housing/saglayicilar' => 'housing/providers',
    ];

    /** Önek eşlemeleri (uzun önek önce). */
    private const PREFIX = [
        'haberler/kategori' => 'news/category', 'blog/kategori' => 'blog/category', 'sss' => 'faq',
        'siralama' => 'rankings', 'programlar' => 'programs', 'meslekler' => 'professions', 'yazar' => 'author',
        'haberler' => 'news',
    ];

    /**
     * Yolu son haline çözer. Değişiklik yoksa null.
     *
     * @param string $path   locale öneki OLMADAN yol (ör. "sss/vize/soru")
     * @param string $locale slug çözümünde dil (FAQ slug'ları dile özgü)
     */
    public static function resolve(string $path, string $locale): ?string
    {
        $original = trim($path, '/');
        $path = $original;
        if (isset(self::EXACT[$path])) {
            $path = self::EXACT[$path];
        } else {
            foreach (self::PREFIX as $from => $to) {
                if (str_starts_with($path, $from . '/')) {
                    $path = $to . substr($path, strlen($from));
                    break;
                }
            }
        }
        $path = self::resolveSlugs($path, $locale);

        return $path !== null && $path !== $original ? $path : null;
    }

    /**
     * Locale'siz eski FAQ bağlantısında dil ipucu: slug "-en"/"-de" ile bitiyorsa o dil (TR eksiz).
     * Diğer yollarda null → çağıran marka varsayılanını kullanır.
     */
    public static function localeHint(string $path): ?string
    {
        $s = explode('/', trim($path, '/'));
        if (count($s) === 3 && in_array($s[0], ['sss', 'faq'], true)) {
            foreach (['en', 'de'] as $loc) {
                if (str_ends_with($s[2], '-' . $loc)) {
                    return $loc;
                }
            }

            return 'tr';
        }

        return null;
    }

    /**
     * Taşınmış slug'ları (slug_redirects) yol ailelerine göre çözer ve hedefin gerçekten var olduğunu doğrular;
     * bilinmeyen konu/alan/kategori/soru için null döner (gerçek 404, "yönlendirip 404" yok).
     * Başka dile ait eski FAQ slug'ı, çeviri grubundan bu dilin kardeşine çözülür (tek adım).
     */
    public static function resolveSlugs(string $path, string $locale): ?string
    {
        $s = explode('/', trim($path, '/'));
        $n = count($s);
        $map = fn (string $entity, string $slug) => SlugRedirects::to($entity, $slug) ?? $slug;
        $exists = fn (string $table, string $slug, array $extra = []) => \Illuminate\Support\Facades\Schema::hasTable($table)
            && \Illuminate\Support\Facades\DB::table($table)->where('slug', $slug)->where($extra)->exists();

        if ($n >= 2 && $s[0] === 'faq') {
            $s[1] = $map('faq_topic', $s[1]);
            if (! $exists('faq_topics', $s[1])) {
                return null;
            }
            if ($n >= 3) {
                $target = self::faqTarget($s[2], $locale);
                if ($target === null) {
                    return null;
                }
                [$s[1], $s[2]] = $target;
            }
        } elseif ($n === 2 && $s[0] === 'fields') {
            $s[1] = $map('field', $s[1]);
            if (! $exists('fields_of_study', $s[1])) {
                return null;
            }
        } elseif ($n === 3 && $s[0] === 'subjects' && $s[2] === 'nc-free') {
            $s[1] = $map('field', $s[1]);
            if (! $exists('fields_of_study', $s[1])) {
                return null;
            }
        } elseif ($n >= 3 && $s[0] === 'programs' && $s[1] === 'field') {
            $s[2] = $map('field', $s[2]);
            if (! $exists('fields_of_study', $s[2])) {
                return null;
            }
        } elseif ($n >= 5 && $s[0] === 'programs' && $s[1] === 'city' && $s[3] === 'field') {
            $s[4] = $map('field', $s[4]);
            if (! $exists('fields_of_study', $s[4])) {
                return null;
            }
        } elseif ($n === 3 && $s[0] === 'blog' && $s[1] === 'category') {
            $s[2] = $map('category', $s[2]);
            if (! $exists('categories', $s[2])) {
                return null;
            }
        }

        return implode('/', $s);
    }

    /**
     * Eski ya da güncel bir FAQ slug'ı için bu dildeki yayımlanmış kaydın [konu slug'ı, slug] çifti; yoksa null.
     * Sıra: dile özgü yönlendirme → güncel slug (bu dilde) → dil gözetmeyen yönlendirme/çapraz dil → kardeş.
     */
    public static function faqTarget(string $slug, string $locale): ?array
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('faqs')) {
            return null;
        }
        $find = fn (string $sl, ?string $loc) => \Illuminate\Support\Facades\DB::table('faqs')
            ->join('faq_topics', 'faq_topics.id', '=', 'faqs.faq_topic_id')
            ->where('faqs.is_published', true)->where('faqs.slug', $sl)
            ->when($loc, fn ($q) => $q->where('faqs.locale', $loc))
            ->first(['faqs.locale', 'faqs.slug', 'faqs.translation_group_id', 'faq_topics.slug as topic_slug']);

        $candidates = array_values(array_unique(array_filter([SlugRedirects::to('faq', $slug, $locale), $slug, SlugRedirects::toAny('faq', $slug)])));
        foreach ($candidates as $cand) {
            if ($row = $find($cand, $locale)) {
                return [$row->topic_slug, $row->slug];
            }
        }
        foreach ($candidates as $cand) {
            $other = $find($cand, null);
            if ($other && $other->translation_group_id) {
                $sibling = \Illuminate\Support\Facades\DB::table('faqs')->join('faq_topics', 'faq_topics.id', '=', 'faqs.faq_topic_id')
                    ->where('faqs.is_published', true)->where('faqs.translation_group_id', $other->translation_group_id)->where('faqs.locale', $locale)
                    ->first(['faqs.slug', 'faq_topics.slug as topic_slug']);
                if ($sibling) {
                    return [$sibling->topic_slug, $sibling->slug];
                }
            }
        }

        return null;
    }
}

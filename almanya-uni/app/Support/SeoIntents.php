<?php

namespace App\Support;

use App\Models\Faq;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

/**
 * SEO Recovery P1-1 — beş ana niyetin ana URL'leri ve iç bağlantı kuralları (config/seo_intents.php).
 *
 * - primaryUrl(): niyetin o dildeki ana URL'si. Yazı yalnız o dilde yayımlanmışsa döner; yoksa null (uydurma URL yok).
 * - forPost() / forRoute() / forCollection(): üye sayfa → niyet ("Ana rehber" kutusu). Ana URL'nin kendisi üye değildir.
 * - forFaq(): soru metni + konu + kategori + soru tipi üzerinden deterministik eşleştirme. Belirsizse null.
 */
class SeoIntents
{
    /** Yayımlanmış yazı haritası önbellek süresi (sn). Düz dizi saklanır (Eloquent Collection değil). */
    private const CACHE_TTL = 3600;

    /** @var array<string, array{type: string, locale: string}>|null */
    private static ?array $postIndex = null;

    public static function intents(): array
    {
        return (array) config('seo_intents.intents', []);
    }

    public static function exists(string $intent): bool
    {
        return isset(self::intents()[$intent]);
    }

    /** Niyetin ana URL'si (mutlak). Tanımsız, yayımlanmamış veya başka dilde olan yazı için null. */
    public static function primaryUrl(string $intent, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $ref = self::intents()[$intent]['primary'][$locale] ?? null;
        if (! $ref) {
            return null;
        }

        if (isset($ref['route'])) {
            return route($ref['route'], $ref['params'] ?? []);
        }

        if (isset($ref['post'])) {
            $p = self::posts()[$ref['post']] ?? null;
            if (! $p || $p['locale'] !== $locale) {
                return null;
            }

            return route($p['type'] === 'news' ? 'news.show' : 'blog.show', $ref['post']);
        }

        return null;
    }

    /** Ana sayfa bloğu: o dilde URL'si çözülen niyetler, config sırasıyla. */
    public static function cards(?string $locale = null): array
    {
        $out = [];
        foreach (self::intents() as $key => $cfg) {
            $url = self::primaryUrl($key, $locale);
            if ($url) {
                $out[] = ['key' => $key, 'url' => $url, 'label' => $cfg['label'], 'description' => $cfg['description'], 'icon' => $cfg['icon'] ?? 'book-open'];
            }
        }

        return $out;
    }

    public static function forPost(Post $post): ?string
    {
        foreach (self::intents() as $key => $cfg) {
            if (($cfg['primary'][$post->locale]['post'] ?? null) === $post->slug) {
                return null; // ana rehberin kendisi
            }
            if (in_array($post->slug, $cfg['members']['posts'] ?? [], true)) {
                return $key;
            }
        }

        return null;
    }

    public static function forRoute(string $routeName): ?string
    {
        foreach (self::intents() as $key => $cfg) {
            if (in_array($routeName, $cfg['members']['routes'] ?? [], true)) {
                return $key;
            }
        }

        return null;
    }

    public static function forCollection(string $slug): ?string
    {
        foreach (self::intents() as $key => $cfg) {
            if (in_array($slug, $cfg['members']['collections'] ?? [], true)) {
                return $key;
            }
        }

        return null;
    }

    // ---------------------------------------------------------------------------------------------
    // FAQ eşleştirmesi — deterministik, yalnız yerel kurallar (harici AI çağrısı yok).
    //
    // Her kural: `need` (hepsi eşleşmeli), `not` (biri eşleşirse elenir), `topics` (izinli FAQ konuları).
    // Kurallar sırayla denenir; ilk eşleşen kazanır. Hiçbiri eşleşmezse bağlantı gösterilmez.
    // Kategori "visa" ve vize/randevu kelimeleri vize sorularını üniversite niyetlerinden ayırır.
    // ---------------------------------------------------------------------------------------------

    /** Vize, randevu, oturum ve ilgisiz idari konular — üniversite niyetlerinin ortak dışlaması. */
    private const NOT_ADMIN = 'vize|visa\b|visum|randevu|appointment|termin|konsolosluk|consulate|konsulat|botschaft|embassy|büyükelçilik|idata|vfs|aufenthalt|oturum izni|residence permit|chancenkarte|fırsat kartı|blue card|mavi kart|blaue karte|anmeldung|ikamet kaydı|sperrkonto|bloke hesap|blocked account|sigorta|insurance|versicherung';

    private static function rules(): array
    {
        $uni = 'üniversite|universit|hochschul|\bfh\b|bachelor|lisans|studium|studien|study|program|bölüm|okul';

        return [
            'conditional_admission' => [
                'need'   => ['şartlı kabul|şartlı kayıt|koşullu kabul|conditional (admission|offer|acceptance)|bedingte[nr]? zulassung|bedingt zugelassen|zulassung unter vorbehalt'],
                'not'    => [],
                'topics' => ['language', 'master', 'uni-assist', 'recognition', 'studienkolleg', 'visa'],
            ],
            'application_deadlines' => [
                'need'   => [
                    'son başvuru|başvuru (tarih|takvim)|başvuru dönemi|başvuru\w* ne zaman|ne zaman başvur|deadline|application (period|window|date)|when (should|can|do|to) (i |you |we )?apply|bewerbungsfrist|bewerbungsschluss|bewerbungszeitraum|bewerbungsphase|wann (muss|sollte|kann) (ich|man) (mich )?bewerb|fristen|bfrist',
                    $uni . '|semester|dönem|uni-assist|winter|sommer|kış|yaz',
                ],
                // "sonuç ne zaman açıklanır" tarih değil, sonuç sorusu.
                'not'    => [self::NOT_ADMIN, 'burs|scholarship|stipendi|daad|wohn|yurt|yurd|housing|dorm|accommodation|steuer|vergi|\btax|bafög|\bjob|iş ilan|ausbildung|sonuç|result|ergebnis|açıklan'],
                'topics' => ['uni-assist', 'master', 'recognition', 'studienkolleg', 'language'],
            ],
            'masters_requirements' => [
                'need'   => [
                    'yüksek lisans|\bmaster|masterstudi|\bm\.?sc\b',
                    'şart|koşul|gerek|kabul|başvur|require|eligib|admission|apply|application|accept|voraussetz|zulass|bewerb|bewirb|gpa|not ortalama|notendurchschnitt|ects|kredi|credit|denklik|anerkenn|recogni|uni-assist|vpd|aps|farklı (bir )?(alan|bölüm)|different (field|subject|discipline)|fachfremd|ilgili bir lisans|related (bachelor|degree)',
                ],
                // "iş deneyimi" kabul ölçütüdür; yalnız çalışma izni / iş arama elenir. Ücret soruları maliyet kuralına düşer.
                'not'    => [self::NOT_ADMIN, 'çalışabil|çalışma (izni|saat)|work permit|part-time|werkstudent|nebenjob|job search|jobsuche|iş ara|iş bul|burs|scholarship|stipend|daad|online|uzaktan|fernstud|distance|\btez|thesis|masterarbeit|doktora|promotion|\bphd|wohn|yurt|housing|ücretsiz|tuition|gebühr|kostenlos'],
                'topics' => ['master', 'uni-assist', 'recognition', 'language'],
            ],
            'university_costs' => [
                'need'   => [
                    'öğrenim ücret|okul ücret|üniversite ücret|eğitim ücret|harç|tuition|studiengebühr|semesterbeitrag|semester (fee|contribution)|dönem (katkı|ücret)|katkı pay|ücretsiz mi|paralı mı|ücretsiz (master|yüksek lisans|lisans|üniversite|eğitim|okul)|free (master|university|bachelor|education)|free to study|study for free|(master|bachelor|degree|study|studies|studying)[^.?]{0,25} for free|is (university|studying|study|education) free|kostenlose[nrs]? (master|studium)|kostenlos studieren|studium (kostenlos|gebührenfrei)|gebührenfrei|langzeitstudiengebühr|zweitstudiengebühr|studienkosten|studying cost|cost of studying|cost to study|okumak (kaç|ne kadar)|kosten (des|eines|für das) studium',
                ],
                // Yalnız yaşam gideri / kira / market / ulaşım soran sorular ve ödeme/transfer soruları (SWIFT, komisyon)
                // ücret rehberine bağlanmaz; harç, Semesterbeitrag ve toplam öğrenim maliyeti soruları bağlanır.
                'not'    => [self::NOT_ADMIN, 'yaşam maliyet|yaşam gider|aylık (ortalama )?gider|geçim|cost of living|living (cost|expense)|lebenshaltung|monthly (cost|expense)|monatliche (kosten|ausgaben)|market|süpermarket|grocer|supermarkt|lebensmittel|ulaşım|toplu taşıma|transport|nahverkehr|öpnv|cover|finanzier|karşıla|swift|havale|transfer|überweisung|nasıl öden|ödenir|komisyon|\bpay (the |my |your )?(tuition|fee)|bezahl|zahle|uni-assist|vpd|çeviri|translation|übersetz|apostil|noter|notar|semesterticket|deutschlandticket|kira|\brent|miete|wohn|yurt|dorm'],
                'topics' => ['money', 'city-life', 'master', 'recognition'],
            ],
            'university_application' => [
                'need'   => [
                    'başvur|\bapply|application|bewerb|bewirb|kabul şart|admission requirement|zulassungsvoraussetzung|nereden başla|where (do i|should i|to) start|wo (fange|soll) ich',
                    'üniversite|universit|hochschul|\bfh\b|bachelor|lisans|studienplatz|uni-assist|vpd|hochschulstart|studienkolleg|zulassung|kabul',
                ],
                'not'    => [self::NOT_ADMIN, 'yüksek lisans|\bmaster|burs|scholarship|stipend|daad|wohn|yurt|yurd|housing|dorm|accommodation|\bjob|iş başvuru|stelle|ausbildung|werkstudent|praktikum|staj|internship|vergi|steuer|\btax|bafög|kindergeld|kredi kart|credit card'],
                'topics' => ['uni-assist', 'recognition', 'studienkolleg'],
            ],
        ];
    }

    /** FAQ → niyet anahtarı veya null. Eşleşme yalnız soru metnine, konuya, kategoriye ve soru tipine dayanır. */
    public static function matchFaq(string $question, ?string $topicSlug, ?string $category = null, ?string $faqIntent = null): ?string
    {
        $q = self::normalize($question);
        $visaCategory = $category === 'visa';

        foreach (self::rules() as $key => $rule) {
            if (! in_array($topicSlug, $rule['topics'], true)) {
                continue;
            }
            if ($visaCategory && $key !== 'conditional_admission') {
                continue;
            }
            $need = $rule['need'];
            // Soru tipi "ne zaman" ise tarih kuralında zaman kelimesi şartı yerine başvuru fiili yeterli.
            if ($key === 'application_deadlines' && $faqIntent === 'ne-zaman') {
                $need[0] .= '|başvur|\bapply|bewerb|bewirb';
            }
            foreach ($need as $re) {
                if (! preg_match('/(' . $re . ')/u', $q)) {
                    continue 2;
                }
            }
            foreach ($rule['not'] as $re) {
                if (preg_match('/(' . $re . ')/u', $q)) {
                    continue 2;
                }
            }

            return $key;
        }

        return null;
    }

    public static function forFaq(Faq $faq): ?string
    {
        return self::matchFaq((string) $faq->question, $faq->topic?->slug, $faq->category, $faq->intent);
    }

    private static function normalize(string $s): string
    {
        $s = str_replace('İ', 'i', $s); // mb_strtolower('İ') "i̇" (birleşik nokta) üretir
        $s = mb_strtolower($s, 'UTF-8');

        return preg_replace('/\s+/u', ' ', $s) ?? $s;
    }

    // ---------------------------------------------------------------------------------------------

    /** config'teki tüm yazı slug'ları → yayımlanmış kaydın tipi ve dili. */
    private static function posts(): array
    {
        if (self::$postIndex !== null) {
            return self::$postIndex;
        }

        $slugs = [];
        foreach (self::intents() as $cfg) {
            foreach ($cfg['primary'] ?? [] as $ref) {
                if (isset($ref['post'])) {
                    $slugs[] = $ref['post'];
                }
            }
        }
        $slugs = array_values(array_unique($slugs));
        if ($slugs === []) {
            return self::$postIndex = [];
        }

        $key = 'seo_intents_posts_' . md5(implode('|', $slugs));

        return self::$postIndex = Cache::remember($key, self::CACHE_TTL, function () use ($slugs) {
            $map = [];
            Post::query()
                ->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->whereIn('slug', $slugs)
                ->get(['slug', 'type', 'locale'])
                ->each(function ($p) use (&$map) {
                    $map[$p->slug] = ['type' => (string) ($p->type ?: 'blog'), 'locale' => (string) $p->locale];
                });

            return $map;
        });
    }

    /** Testler ve uzun süren süreçler için. */
    public static function flush(): void
    {
        self::$postIndex = null;
    }
}

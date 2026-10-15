<?php

namespace Tests\Feature;

use App\Support\OgCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Paylaşım kartı (Open Graph / Twitter) dil doğruluğu.
 *
 * Canlı bulgu (2026-10-14): EN yazının og:image kartında Türkçe alt metinler
 * ("Almanya başvuru rehberi", "Almanya'da Eğitim", "23 dk okuma"). /og/ route'u
 * locale'siz → kart her zaman uygulama varsayılan dilinde (tr) üretiliyordu.
 * Kural: TR sayfa → Türkçe, EN → İngilizce, DE → Almanca kart + meta.
 */
class SocialCardI18nTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = 'og-i18n-thesis-guide';

    private const CITY = 'og-i18n-testcity';

    /** Test sırasında üretilen kart dosyaları (gerçek storage'a yazılır) — tearDown'da silinir. */
    private array $generated = [];

    protected function tearDown(): void
    {
        foreach (['almanyauni', 'applytogerman'] as $bk) {
            foreach (['tr', 'en', 'de'] as $l) {
                foreach ([['post', self::BASE], ['post', self::BASE . '-en'], ['post', self::BASE . '-de'], ['city', self::CITY]] as [$t, $s]) {
                    @unlink(storage_path('app/public/' . OgCard::cachePath($bk, $l, $t, $s)));
                }
            }
        }
        parent::tearDown();
    }

    /* ---------------------------------------------------------------- fixtures */

    private function category(): int
    {
        return DB::table('categories')->insertGetId([
            'name'       => "Almanya'da Eğitim",
            'name_tr'    => "Almanya'da Eğitim",
            'name_en'    => 'Studying in Germany',
            'name_de'    => 'Studium in Deutschland',
            'slug'       => 'og-i18n-cat-' . Str::random(6),
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function postCluster(): void
    {
        $group = (string) Str::uuid();
        $cat = $this->category();
        $titles = [
            'tr' => "Almanya'da Tez Süreci",
            'en' => 'How the Thesis Works in Germany',
            'de' => 'Ablauf der Abschlussarbeit',
        ];
        foreach ($titles as $locale => $title) {
            DB::table('posts')->insert([
                'title'                => $title,
                'slug'                 => $locale === 'tr' ? self::BASE : self::BASE . '-' . $locale,
                'excerpt'              => "Excerpt {$locale}",
                'content_md'           => "## {$title}\n\nMetin.",
                'locale'               => $locale,
                'type'                 => 'blog',
                'category_id'          => $cat,
                'reading_minutes'      => 23,
                'is_published'         => true,
                'published_at'         => now()->subDay(),
                'translation_group_id' => $group,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }
        // reading_minutes saving-hook'u ile yeniden hesaplanabilir; sabitle
        DB::table('posts')->where('translation_group_id', $group)->update(['reading_minutes' => 23]);
    }

    private function city(): void
    {
        $state = DB::table('states')->insertGetId([
            'name_tr' => 'Bavyera', 'name_de' => 'Bayern', 'name_en' => 'Bavaria',
            'slug' => 'og-i18n-state-' . Str::random(5), 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('cities')->insert([
            'state_id' => $state, 'name_tr' => 'Münih', 'name_de' => 'München', 'name_en' => 'Munich',
            'slug' => self::CITY, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** @return array<string, list<string>> property/name => contents */
    private function metas(string $html): array
    {
        preg_match_all('/<meta (?:property|name)="((?:og|twitter):[^"]+)" content="([^"]*)"/', $html, $m, PREG_SET_ORDER);
        $out = [];
        foreach ($m as [, $k, $v]) {
            $out[$k][] = html_entity_decode($v, ENT_QUOTES);
        }

        return $out;
    }

    /* ---------------------------------------------------------------- meta tags */

    public function test_blog_post_meta_tags_follow_page_locale(): void
    {
        $this->postCluster();

        $cases = [
            'tr' => ['/tr/blog/' . self::BASE, 'tr_TR', ['en_US', 'de_DE'], self::BASE],
            'en' => ['/en/blog/' . self::BASE . '-en', 'en_US', ['tr_TR', 'de_DE'], self::BASE . '-en'],
            'de' => ['/de/blog/' . self::BASE . '-de', 'de_DE', ['tr_TR', 'en_US'], self::BASE . '-de'],
        ];

        foreach ($cases as $locale => [$url, $ogLocale, $alts, $slug]) {
            $res = $this->get($url);
            $res->assertOk();
            $meta = $this->metas($res->getContent());

            $this->assertSame([$ogLocale], $meta['og:locale'] ?? null, "{$locale}: og:locale");
            $this->assertEqualsCanonicalizing($alts, $meta['og:locale:alternate'] ?? [], "{$locale}: og:locale:alternate");
            $this->assertSame(['summary_large_image'], $meta['twitter:card'] ?? null);
            // Post kartı URL'si değişmedi (dil kayıttan gelir) — sosyal cache'ler için kararlı
            $this->assertStringEndsWith('/og/post/' . $slug . '.png', $meta['og:image'][0] ?? '');
            $this->assertSame($meta['og:image'], $meta['twitter:image'] ?? null);
            $this->assertSame(['image/png'], $meta['og:image:type'] ?? null);
            $this->assertSame($meta['og:title'], $meta['twitter:title'] ?? null);
        }
    }

    public function test_untranslated_post_lists_no_phantom_locale_alternates(): void
    {
        $cat = $this->category();
        DB::table('posts')->insert([
            'title' => 'Yalnız Türkçe', 'slug' => 'og-i18n-tr-only', 'content_md' => "## A\n\nB", 'locale' => 'tr',
            'type' => 'blog', 'category_id' => $cat, 'is_published' => true, 'published_at' => now()->subDay(),
            'translation_group_id' => (string) Str::uuid(), 'created_at' => now(), 'updated_at' => now(),
        ]);

        $meta = $this->metas($this->get('/tr/blog/og-i18n-tr-only')->assertOk()->getContent());

        $this->assertSame(['tr_TR'], $meta['og:locale']);
        $this->assertArrayNotHasKey('og:locale:alternate', $meta);
    }

    public function test_city_page_og_image_carries_page_locale(): void
    {
        $this->city();

        foreach (['tr' => 'tr_TR', 'en' => 'en_US', 'de' => 'de_DE'] as $locale => $ogLocale) {
            $res = $this->get("/{$locale}/cities/" . self::CITY);
            $res->assertOk();
            $meta = $this->metas($res->getContent());

            $this->assertSame([$ogLocale], $meta['og:locale'], "{$locale}: og:locale");
            $this->assertStringEndsWith('/og/city/' . self::CITY . '.png?lang=' . $locale, $meta['og:image'][0] ?? '');
            $this->assertSame($meta['og:image'], $meta['twitter:image']);
            $this->assertSame(['summary_large_image'], $meta['twitter:card']);
        }
    }

    /* ---------------------------------------------------------------- image endpoint */

    public function test_og_image_endpoint_returns_png_for_each_locale(): void
    {
        $this->postCluster();
        $this->city();

        foreach (['', '-en', '-de'] as $suffix) {
            $res = $this->get('/og/post/' . self::BASE . $suffix . '.png');
            $res->assertOk();
            $this->assertSame('image/png', $res->headers->get('Content-Type'));
            $this->assertStringStartsWith("\x89PNG", $res->getContent());
        }

        foreach (['tr', 'en', 'de'] as $l) {
            $res = $this->get('/og/city/' . self::CITY . '.png?lang=' . $l);
            $res->assertOk();
            $this->assertSame('image/png', $res->headers->get('Content-Type'));
            $this->assertFileExists(storage_path('app/public/' . OgCard::cachePath(brand_key(), $l, 'city', self::CITY)));
        }

        // Eski (lang'sız) URL ve geçersiz lang çalışmaya devam eder — 404 değil
        $this->get('/og/city/' . self::CITY . '.png')->assertOk();
        $this->get('/og/city/' . self::CITY . '.png?lang=xx')->assertOk();
        $this->get('/og/post/og-i18n-does-not-exist.png')->assertNotFound();
    }

    /* ---------------------------------------------------------------- text inputs */

    public function test_post_card_texts_are_localized_from_the_record(): void
    {
        $this->postCluster();
        app()->setLocale('tr');

        // Dil kayıttan: istek dili ne olursa olsun EN yazı → EN kart
        $this->assertSame('en', OgCard::localeFor('post', self::BASE . '-en', null));
        $this->assertSame('de', OgCard::localeFor('post', self::BASE . '-de', 'tr'));
        $this->assertSame('tr', OgCard::localeFor('post', self::BASE, null));

        $en = OgCard::data('post', self::BASE . '-en', 'en');
        $this->assertSame('Studying in Germany', $en['subtitle']);
        $this->assertSame('23 min read', $en['meta']);
        $this->assertSame('BLOG', $en['badge']);

        $de = OgCard::data('post', self::BASE . '-de', 'de');
        $this->assertSame('Studium in Deutschland', $de['subtitle']);
        $this->assertSame('23 Min. Lesezeit', $de['meta']);

        $tr = OgCard::data('post', self::BASE, 'tr');
        $this->assertSame("Almanya'da Eğitim", $tr['subtitle']);
        $this->assertSame('23 dk okuma', $tr['meta']);

        // Marka sloganı kart dilinde (marka config'inin o dildeki değeri)
        $taglines = config('brand.brands.' . brand_key() . '.tagline');
        $this->assertSame($taglines['en'], $en['tagline']);
        $this->assertSame($taglines['de'], $de['tagline']);
        $this->assertSame($taglines['tr'], $tr['tagline']);

        // Global locale kalıcı değişmedi
        $this->assertSame('tr', app()->getLocale());
    }

    public function test_city_card_texts_are_localized(): void
    {
        $this->city();
        app()->setLocale('en');

        $tr = OgCard::data('city', self::CITY, 'tr');
        $this->assertSame('ŞEHİR', $tr['badge']); // TR büyük harf: i → İ
        $this->assertSame('Münih', $tr['title']);
        $this->assertSame('Bavyera', $tr['subtitle']);
        $this->assertSame('0 üniversite', $tr['meta']);

        $de = OgCard::data('city', self::CITY, 'de');
        $this->assertSame('STADT', $de['badge']);
        $this->assertSame('München', $de['title']);
        $this->assertSame('Bayern', $de['subtitle']);

        $en = OgCard::data('city', self::CITY, 'en');
        $this->assertSame('CITY', $en['badge']);
        $this->assertSame('Munich', $en['title']);
        $this->assertSame('0 universities', $en['meta']);

        $this->assertSame('en', app()->getLocale());
    }

    public function test_localize_url_rules(): void
    {
        $host = 'http://' . request()->getHost();

        $this->assertSame("{$host}/og/city/berlin.png?lang=de", OgCard::localizeUrl("{$host}/og/city/berlin.png", 'de'));
        $this->assertSame('/og/university/tum.png?lang=tr', OgCard::localizeUrl('/og/university/tum.png', 'tr'));
        // post: URL değişmez
        $this->assertSame("{$host}/og/post/x-en.png", OgCard::localizeUrl("{$host}/og/post/x-en.png", 'en'));
        // zaten lang varsa dokunma; dış görsel / statik görsel dokunma; geçersiz dil dokunma
        $this->assertSame('/og/city/berlin.png?lang=en', OgCard::localizeUrl('/og/city/berlin.png?lang=en', 'de'));
        $this->assertSame('https://cdn.example.com/og/city/berlin.png', OgCard::localizeUrl('https://cdn.example.com/og/city/berlin.png', 'de'));
        $this->assertSame('/img/og/applytogerman.png', OgCard::localizeUrl('/img/og/applytogerman.png', 'de'));
        $this->assertSame('/og/city/berlin.png', OgCard::localizeUrl('/og/city/berlin.png', 'xx'));
    }
}

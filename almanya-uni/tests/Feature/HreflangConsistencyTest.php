<?php

namespace Tests\Feature;

use App\Support\Hreflang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * hreflang: sitemap ile sayfa <head>'i AYNI x-default'u vermeli; kopuk SSS grubu birleşmeli.
 *
 * Canlı denetim (2026-09-25): sitemap x-default = tr (activeLocales[0]), sayfa = en
 * (config default). Ayrıca "turk-lise-diplomasinin-..." SSS'sinin EN/DE kayıtlarının
 * translation_group_id'si NULL'dı → üç sayfa üç ayrı küme.
 */
class HreflangConsistencyTest extends TestCase
{
    use RefreshDatabase;

    private const FAQ_FIX = '2026_09_25_000100_link_orphaned_faq_translation_group.php';

    private const TR_SLUG = 'turk-lise-diplomasinin-almanyada-gecerliligi-var-mi';

    /* ---------------------------------------------------------------- yardımcı */

    /** @param  array<int, string>  $locales */
    private function postCluster(string $base, array $locales): string
    {
        $group = (string) Str::uuid();
        foreach ($locales as $locale) {
            DB::table('posts')->insert([
                'title'                => "{$base} {$locale}",
                'slug'                 => $locale === 'tr' ? $base : "{$base}-{$locale}",
                'content_md'           => "## {$base} {$locale}\n\nMetin.",
                'locale'               => $locale,
                'type'                 => 'blog',
                'is_published'         => true,
                'published_at'         => now()->subDay(),
                'translation_group_id' => $group,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }

        return $group;
    }

    private function topic(string $slug = 'denklik'): int
    {
        return DB::table('faq_topics')->where('slug', $slug)->value('id')
            ?? DB::table('faq_topics')->insertGetId(['name' => ucfirst($slug), 'slug' => $slug, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function faq(int $topic, string $locale, string $slug, ?string $group): int
    {
        return DB::table('faqs')->insertGetId([
            'faq_topic_id'         => $topic,
            'question'             => "Soru {$locale}",
            'answer_md'            => "Cevap {$locale}",
            'answer_html'          => "<p>Cevap {$locale}</p>",
            'has_answer'           => true,
            'slug'                 => $slug,
            'locale'               => $locale,
            'is_published'         => true,
            'translation_group_id' => $group,
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);
    }

    /** Canlıdaki kopuk durum: TR gruplu, EN/DE NULL. */
    private function orphanedFaq(): array
    {
        DB::table('faqs')->whereIn('slug', [self::TR_SLUG, self::TR_SLUG . '-en', self::TR_SLUG . '-de'])->delete();
        $t = $this->topic();

        return [
            'tr' => $this->faq($t, 'tr', self::TR_SLUG, (string) Str::uuid()),
            'en' => $this->faq($t, 'en', self::TR_SLUG . '-en', null),
            'de' => $this->faq($t, 'de', self::TR_SLUG . '-de', null),
        ];
    }

    private function runFaqFix(): void
    {
        ob_start();
        try {
            (require database_path('migrations/' . self::FAQ_FIX))->up();
        } finally {
            ob_end_clean();
        }
    }

    /** @return array{alts: array<string, string>, canonical: array<int, string>} */
    private function pageHead(string $url): array
    {
        $html = $this->get($url)->assertOk()->getContent();
        $head = explode('</head>', $html, 2)[0];
        preg_match_all('~<link rel="alternate" hreflang="([^"]+)" href="([^"]+)"~', $head, $m, PREG_SET_ORDER);
        preg_match_all('~<link rel="canonical" href="([^"]+)"~', $head, $c);

        $alts = [];
        foreach ($m as [, $hl, $href]) {
            $this->assertArrayNotHasKey($hl, $alts, "{$url}: '{$hl}' iki kez");
            $alts[$hl] = html_entity_decode($href);
        }

        return ['alts' => $alts, 'canonical' => $c[1]];
    }

    /** Sitemap'teki <url> kaydının hreflang haritası (dil dosyalarının hepsinde aranır). */
    private function sitemapAlts(string $loc): ?array
    {
        $xml = '';
        foreach (Hreflang::activeLocales() as $lang) {
            $xml .= $this->get("/sitemap-{$lang}.xml")->assertOk()->getContent();
        }
        foreach (explode('<url>', $xml) as $block) {
            if (! str_contains($block, '<loc>' . htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES) . '</loc>')) {
                continue;
            }
            preg_match_all('~hreflang="([^"]+)" href="([^"]+)"~', $block, $m, PREG_SET_ORDER);

            return collect($m)->mapWithKeys(fn ($x) => [$x[1] => html_entity_decode($x[2])])->all();
        }

        return null;
    }

    /** x-default hedefi yönlendirmesiz 200 dönmeli. */
    private function assertLive(string $url): void
    {
        $status = $this->get($url)->getStatusCode();
        $this->assertSame(200, $status, "{$url} → {$status} (404/redirect x-default olamaz)");
    }

    /* ---------------------------------------------------- merkezi mantık */

    public function test_helper_prefers_the_app_default_and_falls_back_in_locale_order(): void
    {
        $this->assertSame('en', config('locale.default'));
        $this->assertSame(['tr', 'en', 'de'], Hreflang::activeLocales());
        $this->assertSame('en', Hreflang::preferredDefault());

        $this->assertSame('E', Hreflang::xDefault(['tr' => 'T', 'en' => 'E', 'de' => 'D']));
        $this->assertSame('T', Hreflang::xDefault(['tr' => 'T', 'de' => 'D']));
        $this->assertSame('D', Hreflang::xDefault(['de' => 'D']));
        $this->assertNull(Hreflang::xDefault([]));
        $this->assertSame('T', Hreflang::xDefault(['tr' => 'T', 'en' => '']), 'boş URL alternatif sayılmaz');
    }

    /* ---------------------------------- A/B/D/E: üç dilli küme → EN */

    public function test_three_language_cluster_points_x_default_to_the_real_en_sibling_in_page_and_sitemap(): void
    {
        $this->postCluster('hreflang-three-lang-test', ['tr', 'en', 'de']);
        $en = url('en/blog/hreflang-three-lang-test-en');

        foreach (['tr' => 'hreflang-three-lang-test', 'en' => 'hreflang-three-lang-test-en', 'de' => 'hreflang-three-lang-test-de'] as $l => $slug) {
            $page = $this->pageHead(url("{$l}/blog/{$slug}"));
            $this->assertSame($en, $page['alts']['x-default'], "{$l} sayfası");
        }

        // Sitemap bu host'un dilindeki kaydı listeler (localhost → tr).
        $sm = $this->sitemapAlts(url('tr/blog/hreflang-three-lang-test'));
        $this->assertNotNull($sm, 'küme sitemap\'te yok');
        $this->assertSame($en, $sm['x-default']);
        $this->assertSame($sm, $this->pageHead(url('tr/blog/hreflang-three-lang-test'))['alts'], 'sitemap ve sayfa hreflang haritası aynı olmalı');

        $this->assertLive($en);
    }

    /* ------------------------------- C: EN yoksa gerçek mevcut kardeş */

    public function test_cluster_without_en_falls_back_to_an_existing_sibling_in_page_and_sitemap(): void
    {
        $this->postCluster('hreflang-no-en-test', ['tr', 'de']);
        $tr = url('tr/blog/hreflang-no-en-test');

        foreach ([url('tr/blog/hreflang-no-en-test'), url('de/blog/hreflang-no-en-test-de')] as $u) {
            $alts = $this->pageHead($u)['alts'];
            $this->assertSame($tr, $alts['x-default'], $u);
            $this->assertArrayNotHasKey('en', $alts, 'olmayan EN için hreflang üretildi');
        }

        $sm = $this->sitemapAlts($tr);
        $this->assertSame($tr, $sm['x-default']);
        $this->assertArrayNotHasKey('en', $sm);

        $this->assertLive($tr);
        $this->get(url('en/blog/hreflang-no-en-test-en'))->assertNotFound();
    }

    public function test_single_language_post_uses_itself_as_x_default(): void
    {
        $this->postCluster('hreflang-de-only-test', ['de']);
        $de = url('de/blog/hreflang-de-only-test-de');

        $this->assertSame(['de' => $de, 'x-default' => $de], $this->pageHead($de)['alts']);
    }

    public function test_template_pages_point_x_default_to_en_and_it_is_live(): void
    {
        $alts = $this->pageHead(url('tr/faq'))['alts'];

        // Host test ortamında isteğe göre değişir (APP_URL vs istek host'u); path karşılaştırılır.
        $this->assertSame('/en/faq', parse_url($alts['x-default'], PHP_URL_PATH));
        $this->assertLive($alts['x-default']);

        // Sitemap route() ile APP_URL host'unu üretir; canlıda ikisi aynı alan adı.
        $path = fn (string $u) => parse_url($u, PHP_URL_PATH);
        $sm = null;
        foreach (['http://localhost:8000', 'http://localhost'] as $root) {
            $sm ??= $this->sitemapAlts($root . '/tr/faq');
        }
        $this->assertNotNull($sm, 'SSS dizini sitemap içinde yok');
        $this->assertSame($path($alts['x-default']), $path($sm['x-default']));
    }

    /* ------------------------------------------- F/G: kopuk SSS grubu */

    public function test_orphaned_faq_siblings_are_merged_into_one_group(): void
    {
        $ids = $this->orphanedFaq();
        $trGroup = DB::table('faqs')->where('id', $ids['tr'])->value('translation_group_id');

        $this->runFaqFix();

        $groups = DB::table('faqs')->whereIn('id', $ids)->pluck('translation_group_id', 'locale')->all();
        $this->assertSame(['de' => $trGroup, 'en' => $trGroup, 'tr' => $trGroup], collect($groups)->sortKeys()->all(), 'TR grubu ortak grup olmalı');

        // Slug / yayın durumu değişmedi.
        $this->assertSame(
            [self::TR_SLUG, self::TR_SLUG . '-en', self::TR_SLUG . '-de'],
            [DB::table('faqs')->find($ids['tr'])->slug, DB::table('faqs')->find($ids['en'])->slug, DB::table('faqs')->find($ids['de'])->slug]
        );

        // Idempotent.
        $this->runFaqFix();
        $this->assertSame($groups, DB::table('faqs')->whereIn('id', $ids)->pluck('translation_group_id', 'locale')->all());
    }

    public function test_merged_faq_is_reciprocal_across_tr_en_de(): void
    {
        $this->orphanedFaq();
        $this->runFaqFix();

        $urls = [
            'tr' => url('tr/faq/denklik/' . self::TR_SLUG),
            'en' => url('en/faq/denklik/' . self::TR_SLUG . '-en'),
            'de' => url('de/faq/denklik/' . self::TR_SLUG . '-de'),
        ];
        $expected = $urls + ['x-default' => $urls['en']];

        foreach ($urls as $locale => $u) {
            $page = $this->pageHead($u);
            $this->assertSame($expected, $page['alts'], "{$locale} sayfası kümenin tamamını göstermeli");
            // H: canonical değişmedi — tek ve kendisi.
            $this->assertSame([$u], $page['canonical']);
        }
    }

    public function test_faq_fix_refuses_to_create_a_cluster_with_two_records_of_the_same_language(): void
    {
        $ids = $this->orphanedFaq();
        $trGroup = DB::table('faqs')->where('id', $ids['tr'])->value('translation_group_id');
        // Aynı gruba başka bir EN kaydı zaten bağlı.
        $this->faq($this->topic(), 'en', 'baska-bir-en-kayit', $trGroup);

        try {
            $this->runFaqFix();
            $this->fail('çakışmada migration sessizce geçti');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('başka kayıtlar var', $e->getMessage());
        }

        $this->assertNull(DB::table('faqs')->where('id', $ids['en'])->value('translation_group_id'), 'çakışmada yazıldı');
    }

    public function test_faq_fix_is_a_noop_on_an_empty_install(): void
    {
        DB::table('faqs')->whereIn('slug', [self::TR_SLUG, self::TR_SLUG . '-en', self::TR_SLUG . '-de'])->delete();

        $this->runFaqFix();

        $this->assertSame(0, DB::table('faqs')->where('slug', 'like', self::TR_SLUG . '%')->count());
    }

    /* ------------------------------------------------------- H: canonical */

    public function test_canonical_stays_self_referencing_on_blog_pages(): void
    {
        $this->postCluster('hreflang-canonical-test', ['tr', 'en', 'de']);

        foreach (['tr' => '', 'en' => '-en', 'de' => '-de'] as $l => $suffix) {
            $u = url("{$l}/blog/hreflang-canonical-test{$suffix}");
            $this->assertSame([$u], $this->pageHead($u)['canonical']);
        }
    }
}

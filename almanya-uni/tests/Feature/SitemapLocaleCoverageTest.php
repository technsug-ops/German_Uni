<?php

namespace Tests\Feature;

use App\Support\Hreflang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Dil bazlı sitemap: her dil sürümü kendi <url>/<loc> kaydını alır.
 *
 * 2026-09-25 kapsam denetimi: sitemap host dilini (en) kullanıyordu → 23.858 <loc>'un
 * tamamı EN, TR/DE yalnız hreflang alternatifi; EN'i olmayan TR yazı/SSS hiç yoktu.
 * Karşılaştırmalar path üzerinden (test ortamında host isteğe göre değişir).
 */
class SitemapLocaleCoverageTest extends TestCase
{
    use RefreshDatabase;

    private const LANGS = ['tr', 'en', 'de'];

    /* ---------------------------------------------------------------- yardımcı */

    private function path(string $url): string
    {
        return parse_url($url, PHP_URL_PATH) ?: '/';
    }

    /** @return array<string, array<string, string>> loc-path => [hreflang => path] */
    private function sitemap(string $lang): array
    {
        $xml = $this->get("/sitemap-{$lang}.xml")->assertOk()->assertHeader('Content-Type', 'application/xml; charset=utf-8')->getContent();
        $this->assertStringContainsString('xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"', $xml);
        $this->assertStringContainsString('xmlns:xhtml="http://www.w3.org/1999/xhtml"', $xml);

        preg_match_all('~<url>(.*?)</url>~s', $xml, $m);
        $out = [];
        foreach ($m[1] as $block) {
            preg_match('~<loc>([^<]+)</loc>~', $block, $l);
            preg_match_all('~hreflang="([^"]+)" href="([^"]+)"~', $block, $a, PREG_SET_ORDER);
            $loc = $this->path(html_entity_decode($l[1]));
            $this->assertArrayNotHasKey($loc, $out, "{$lang}: {$loc} iki kez <loc>");
            $out[$loc] = collect($a)->mapWithKeys(fn ($x) => [$x[1] => $this->path(html_entity_decode($x[2]))])->all();
        }

        return $out;
    }

    /** @return array<string, array<string, array<string, string>>> */
    private function all(): array
    {
        return collect(self::LANGS)->mapWithKeys(fn ($l) => [$l => $this->sitemap($l)])->all();
    }

    /** @param  array<string, string|null>  $slugs  locale => slug (null = o dilde yok) */
    private function blogPost(array $slugs, array $extra = []): void
    {
        $group = (string) Str::uuid();
        foreach ($slugs as $locale => $slug) {
            DB::table('posts')->insert(array_merge([
                'title' => $slug, 'slug' => $slug, 'content_md' => "## {$slug}\n\nMetin.", 'locale' => $locale,
                'type' => 'blog', 'is_published' => true, 'published_at' => now()->subDay(),
                'translation_group_id' => $group, 'created_at' => now(), 'updated_at' => now(),
            ], $extra));
        }
    }

    private function topic(): int
    {
        return DB::table('faq_topics')->where('slug', 'sitemap-test')->value('id')
            ?? DB::table('faq_topics')->insertGetId(['name' => 'Sitemap test', 'slug' => 'sitemap-test', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function faq(string $locale, string $slug, ?string $group, bool $published = true): void
    {
        DB::table('faqs')->insert([
            'faq_topic_id' => $this->topic(), 'question' => $slug, 'answer_md' => 'Cevap', 'answer_html' => '<p>Cevap</p>',
            'has_answer' => true, 'slug' => $slug, 'locale' => $locale, 'is_published' => $published,
            'translation_group_id' => $group, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** İndexable (açıklamalı) ve noindex (yalnız ad+derece) birer program. */
    private function programs(): void
    {
        $city = DB::table('cities')->insertGetId(['name_de' => 'Teststadt', 'name_tr' => 'Test şehri', 'slug' => 'sitemap-teststadt', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        $uni = DB::table('universities')->insertGetId(['name_de' => 'Test Uni', 'name_tr' => 'Test Üni', 'slug' => 'sitemap-test-uni', 'city_id' => $city, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        foreach (['sitemap-indexable-program' => 'Açıklama', 'sitemap-thin-program' => null] as $slug => $desc) {
            DB::table('programs')->insert(['name_de' => $slug, 'slug' => $slug, 'degree' => 'master', 'university_id' => $uni,
                'is_active' => true, 'description_tr' => $desc, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    /* ------------------------------------------------------------ A. index */

    public function test_sitemap_xml_is_an_index_of_one_file_per_language(): void
    {
        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $xml);
        preg_match_all('~<loc>([^<]+)</loc>~', $xml, $m);
        $this->assertSame(['/sitemap-tr.xml', '/sitemap-en.xml', '/sitemap-de.xml'], array_map(fn ($u) => $this->path($u), $m[1]));
    }

    public function test_legacy_child_urls_still_answer_with_the_index(): void
    {
        foreach (['/sitemap-content.xml', '/sitemap-landings.xml', '/sitemap-glossary.xml'] as $u) {
            $this->assertStringContainsString('<sitemapindex', $this->get($u)->assertOk()->getContent(), $u);
        }
        $this->get('/sitemap-xx.xml')->assertNotFound();
        $this->get('/sitemap-tr-2.xml')->assertNotFound();
    }

    /* ------------------------------------------- B/C/D + L + O: dil ve tekillik */

    public function test_every_loc_belongs_to_its_file_language_and_is_unique(): void
    {
        $this->blogPost(['tr' => 'sm-three', 'en' => 'sm-three-en', 'de' => 'sm-three-de']);
        $all = $this->all();

        $seen = [];
        foreach ($all as $lang => $entries) {
            $this->assertNotEmpty($entries, $lang);
            $this->assertLessThan(50000, count($entries), "{$lang}: 50.000 sınırı");
            foreach (array_keys($entries) as $loc) {
                $this->assertTrue($loc === "/{$lang}" || str_starts_with($loc, "/{$lang}/"), "{$lang} dosyasında yabancı <loc>: {$loc}");
                $this->assertArrayNotHasKey($loc, $seen, "{$loc} iki dosyada");
                $seen[$loc] = true;
            }
        }
    }

    public function test_a_language_over_the_file_limit_is_split_into_numbered_files(): void
    {
        // Dil dosyası üretilince kayıt sayısı önbelleğe yazılır; index dosya sayısını oradan okur.
        $full = $this->sitemap('tr');
        config(['sitemap.max_urls_per_file' => 40]);

        $pages = (int) ceil(count($full) / 40);
        $this->assertGreaterThan(1, $pages, 'test verisi en az iki dosya üretmeli');

        preg_match_all('~<loc>([^<]+)</loc>~', $this->get('/sitemap.xml')->assertOk()->getContent(), $m);
        $trFiles = array_values(array_filter(array_map(fn ($u) => $this->path($u), $m[1]), fn ($p) => str_starts_with($p, '/sitemap-tr')));
        $this->assertSame(array_merge(['/sitemap-tr.xml'], array_map(fn ($n) => "/sitemap-tr-{$n}.xml", range(2, $pages))), $trFiles);

        $union = [];
        foreach ($trFiles as $f) {
            preg_match_all('~<loc>([^<]+)</loc>~', $this->get($f)->assertOk()->getContent(), $u);
            $this->assertLessThanOrEqual(40, count($u[1]), $f);
            foreach ($u[1] as $loc) {
                $this->assertArrayNotHasKey($loc, $union, "{$loc} iki parçada");
                $union[$loc] = true;
            }
        }
        $this->assertSame(count($full), count($union), 'parçaların birleşimi tam dosyaya eşit olmalı');
        $this->get('/sitemap-tr-' . ($pages + 1) . '.xml')->assertNotFound();
    }

    /* --------------------------------------------- E. üç dilli küme → 3 <loc> */

    public function test_three_language_cluster_gets_three_locs_with_the_same_cluster(): void
    {
        $this->blogPost(['tr' => 'sm-cluster', 'en' => 'sm-cluster-en', 'de' => 'sm-cluster-de']);
        $all = $this->all();

        $expected = ['tr' => '/tr/blog/sm-cluster', 'en' => '/en/blog/sm-cluster-en', 'de' => '/de/blog/sm-cluster-de'];
        $cluster = $expected + ['x-default' => '/en/blog/sm-cluster-en'];

        foreach ($expected as $lang => $loc) {
            $this->assertArrayHasKey($loc, $all[$lang], "{$lang} <loc> yok");
            $this->assertSame($cluster, $all[$lang][$loc], "{$lang} kümesi");
        }

        // Şablon türleri (program vb.) de her dilde kendi <loc>'unu alır.
        foreach (self::LANGS as $lang) {
            $this->assertArrayHasKey("/{$lang}/faq", $all[$lang]);
            $this->assertSame('/en/faq', $all[$lang]["/{$lang}/faq"]['x-default']);
        }
    }

    /* ------------------------------------------------ F/G. TR-only içerik */

    public function test_tr_only_blog_is_in_the_tr_sitemap_only(): void
    {
        $this->blogPost(['tr' => 'sm-tr-only']);
        $all = $this->all();

        $this->assertSame(['tr' => '/tr/blog/sm-tr-only', 'x-default' => '/tr/blog/sm-tr-only'], $all['tr']['/tr/blog/sm-tr-only']);
        foreach (['en', 'de'] as $lang) {
            $this->assertEmpty(array_filter(array_keys($all[$lang]), fn ($l) => str_contains($l, 'sm-tr-only')), "{$lang} dosyasında TR-only yazı");
        }
    }

    public function test_tr_only_faq_is_in_the_tr_sitemap(): void
    {
        $this->faq('tr', 'sm-tr-only-faq', null);
        $all = $this->all();

        $loc = '/tr/faq/sitemap-test/sm-tr-only-faq';
        $this->assertSame(['tr' => $loc, 'x-default' => $loc], $all['tr'][$loc]);
        $this->assertArrayNotHasKey('/en/faq/sitemap-test/sm-tr-only-faq', $all['en']);
    }

    public function test_en_only_and_de_only_content_work_the_same_way(): void
    {
        $this->blogPost(['de' => 'sm-de-only-de']);
        $this->faq('en', 'sm-en-only-faq-en', (string) Str::uuid());
        $all = $this->all();

        $this->assertSame(['de' => '/de/blog/sm-de-only-de', 'x-default' => '/de/blog/sm-de-only-de'], $all['de']['/de/blog/sm-de-only-de']);
        $this->assertArrayHasKey('/en/faq/sitemap-test/sm-en-only-faq-en', $all['en']);
    }

    /* ------------------------------------------------- H. taslak/gelecek */

    public function test_draft_future_and_unpublished_content_never_leaks(): void
    {
        $this->blogPost(['tr' => 'sm-draft', 'en' => 'sm-draft-en'], ['is_published' => false]);
        $this->blogPost(['tr' => 'sm-future'], ['published_at' => now()->addDays(3)]);
        $this->blogPost(['tr' => 'sm-no-date'], ['published_at' => null]);
        $this->faq('tr', 'sm-unpublished-faq', null, false);

        $blob = json_encode($this->all());
        foreach (['sm-draft', 'sm-future', 'sm-no-date', 'sm-unpublished-faq'] as $slug) {
            $this->assertStringNotContainsString($slug, $blob, "{$slug} sitemap'e sızdı");
        }
    }

    /* ---------------------------------------------------- I. noindex program */

    public function test_noindex_programs_stay_out_and_indexable_ones_are_in_every_language(): void
    {
        $this->programs();
        $all = $this->all();

        foreach (self::LANGS as $lang) {
            $this->assertArrayHasKey("/{$lang}/programs/sitemap-indexable-program", $all[$lang]);
            $this->assertArrayNotHasKey("/{$lang}/programs/sitemap-thin-program", $all[$lang]);
        }
    }

    /* ------------------------------- superseded slug: dile duyarlı kural */

    public function test_a_slug_superseded_in_its_own_language_is_not_a_loc(): void
    {
        $this->blogPost(['tr' => 'sm-old-tr-slug']);
        $this->blogPost(['tr' => 'sm-new-slug', 'en' => 'sm-new-slug-en']);
        DB::table('blog_redirects')->insert(['from_slug' => 'sm-old-tr-slug', 'to_slug' => 'sm-new-slug', 'locale' => 'tr', 'created_at' => now(), 'updated_at' => now()]);

        // Başka dilde (en) aynı slug için kayıt: TR kardeşini DIŞARIDA BIRAKMAMALI.
        $this->blogPost(['tr' => 'sm-kept-tr', 'en' => 'sm-kept-tr-en']);
        DB::table('blog_redirects')->insert(['from_slug' => 'sm-kept-tr', 'to_slug' => 'sm-kept-tr-en', 'locale' => 'en', 'created_at' => now(), 'updated_at' => now()]);

        $tr = $this->sitemap('tr');
        $this->assertArrayNotHasKey('/tr/blog/sm-old-tr-slug', $tr);
        $this->assertArrayHasKey('/tr/blog/sm-new-slug', $tr);
        $this->assertArrayHasKey('/tr/blog/sm-kept-tr', $tr);
        $this->assertSame('/tr/blog/sm-kept-tr', $this->sitemap('en')['/en/blog/sm-kept-tr-en']['tr']);
    }

    /* ------------------- J/K/M/N + reciprocity: sayfa ile birebir, 200, self-canonical */

    public function test_locs_are_live_self_canonical_and_match_the_page_head(): void
    {
        $this->blogPost(['tr' => 'sm-live', 'en' => 'sm-live-en', 'de' => 'sm-live-de']);
        $this->blogPost(['tr' => 'sm-live-tr-only']);
        $this->faq('tr', 'sm-live-faq', null);
        $this->programs();
        $all = $this->all();

        $sample = ['/tr/blog/sm-live', '/en/blog/sm-live-en', '/de/blog/sm-live-de', '/tr/blog/sm-live-tr-only',
            '/tr/faq/sitemap-test/sm-live-faq', '/tr/programs/sitemap-indexable-program', '/de/programs/sitemap-indexable-program',
            '/tr/faq', '/en/faq', '/de/blog'];

        foreach ($sample as $loc) {
            $lang = substr($loc, 1, 2);
            $this->assertArrayHasKey($loc, $all[$lang], "{$loc} sitemap'te yok");

            // Test uygulaması istekler arasında yenilenmez: bir önceki sayfanın paylaştığı
            // localeUrls sonraki isteğe taşınır (canlıda her istek temiz süreç).
            view()->share('localeUrls', null);
            $response = $this->get($loc);
            $this->assertSame(200, $response->getStatusCode(), "{$loc} 200 değil (redirect/404 <loc> olamaz)");

            $head = explode('</head>', $response->getContent(), 2)[0];
            preg_match_all('~<link rel="canonical" href="([^"]+)"~', $head, $c);
            $this->assertSame([$loc], array_map(fn ($u) => $this->path($u), $c[1]), "{$loc} canonical");

            preg_match_all('~<link rel="alternate" hreflang="([^"]+)" href="([^"]+)"~', $head, $a, PREG_SET_ORDER);
            $page = collect($a)->mapWithKeys(fn ($x) => [$x[1] => $this->path(html_entity_decode($x[2]))])->all();
            $this->assertSame($all[$lang][$loc], $page, "{$loc}: sitemap kümesi sayfa <head>'iyle aynı olmalı (x-default dahil)");
        }
    }

    public function test_every_alternate_has_its_own_loc_and_identical_cluster(): void
    {
        $this->blogPost(['tr' => 'sm-recip', 'en' => 'sm-recip-en', 'de' => 'sm-recip-de']);
        $this->blogPost(['tr' => 'sm-recip-two', 'de' => 'sm-recip-two-de']);
        $flat = array_merge(...array_values($this->all()));

        foreach ($flat as $loc => $alts) {
            $this->assertContains($loc, $alts, "{$loc} kendini göstermiyor");
            $this->assertContains($alts['x-default'] ?? null, array_diff_key($alts, ['x-default' => 1]), "{$loc} x-default kümede değil");
            foreach ($alts as $hl => $u) {
                if ($hl === 'x-default' || $u === $loc) {
                    continue;
                }
                $this->assertArrayHasKey($u, $flat, "{$loc} → {$u}: alternatifin kendi <loc>'u yok");
                $this->assertSame($alts, $flat[$u], "{$loc} ↔ {$u}: küme farklı");
            }
        }
    }
}

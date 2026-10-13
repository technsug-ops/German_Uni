<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\UniversityWebController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * SEO Recovery Faz 1: üniversite sayfasındaki program listesi sayfalı ve filtreli.
 * - İlk HTML'de en fazla PROGRAMS_PER_PAGE program bağlantısı; sonraki sayfalara gerçek <a href> (rel=next/prev).
 * - Sayfa 1 = filtresiz üniversite URL'si (canonical değişmez); ?page=N (N≥2) self-canonical + aynı sayfanın hreflang'i.
 * - Filtreli URL'ler (degree/lang) canonical ve hreflang olarak filtresiz üniversite URL'sine işaret eder.
 * - ?page=1 → 301; geçersiz sayfa / filtre → 404. Hiçbir program kaybolmaz ya da tekrarlanmaz; noindex programlar gizlenmez.
 */
class UniversityProgramListingTest extends TestCase
{
    use RefreshDatabase;

    private const BIG = 'big-test-universitat';
    private const SMALL = 'small-test-hochschule';

    private function uni(string $slug): int
    {
        return DB::table('universities')->insertGetId(['name_de' => $slug, 'name_tr' => $slug, 'slug' => $slug, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function program(int $uniId, string $slug, string $degree, string $language, bool $rich, bool $active = true): void
    {
        DB::table('programs')->insert([
            'university_id' => $uniId, 'name_de' => $slug, 'slug' => $slug, 'degree' => $degree, 'language' => $language,
            'description_en' => $rich ? "A distinctive programme about {$slug} with seminars, laboratory projects and an internship semester." : null,
            'is_active' => $active, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** BIG: 30 bachelor (20 en, 10 de), 25 master (15 en), 5 phd = 60 aktif; 2 pasif. SMALL: 3 program. */
    private function seedFixture(): void
    {
        $big = $this->uni(self::BIG);
        for ($i = 1; $i <= 30; $i++) {
            $this->program($big, sprintf('ba-%02d', $i), 'bachelor', $i <= 20 ? 'en' : 'de', $i % 3 === 0);
        }
        for ($i = 1; $i <= 25; $i++) {
            $this->program($big, sprintf('ma-%02d', $i), 'master', $i <= 15 ? 'en' : 'de', $i % 2 === 0);
        }
        for ($i = 1; $i <= 5; $i++) {
            $this->program($big, sprintf('phd-%02d', $i), 'phd', 'de', false);
        }
        $this->program($big, 'inactive-01', 'bachelor', 'en', true, false);
        $this->program($big, 'inactive-02', 'master', 'en', true, false);

        $small = $this->uni(self::SMALL);
        foreach (['s-ba-1' => 'bachelor', 's-ba-2' => 'bachelor', 's-ma-1' => 'master'] as $slug => $degree) {
            $this->program($small, $slug, $degree, 'de', false);
        }
    }

    /** @return array<int,string> sayfadaki program slug'ları (sırayla) */
    private function programLinks(string $html): array
    {
        preg_match_all('#href="[^"]*/(?:tr|en|de)/programs/([a-z0-9-]+)"#', $html, $m);

        return $m[1];
    }

    private function canonical(string $html): ?string
    {
        return preg_match('#<link rel="canonical" href="([^"]+)"#', $html, $m) ? $m[1] : null;
    }

    /** @return array<string,string> */
    private function hreflangs(string $html): array
    {
        preg_match_all('#<link rel="alternate" hreflang="([\w-]+)" href="([^"]+)"#', $html, $m, PREG_SET_ORDER);

        return collect($m)->mapWithKeys(fn ($x) => [$x[1] => html_entity_decode($x[2])])->all();
    }

    public function test_small_university_lists_all_programs_without_pagination(): void
    {
        $this->seedFixture();
        $html = $this->get('/tr/universities/' . self::SMALL)->assertOk()->getContent();

        $this->assertEqualsCanonicalizing(['s-ba-1', 's-ba-2', 's-ma-1'], $this->programLinks($html));
        $this->assertStringNotContainsString('rel="next"', $html);
        $this->assertStringNotContainsString('aria-label="' . __('Programs pagination') . '"', $html);
        $this->assertSame(url('/tr/universities/' . self::SMALL), $this->canonical($html));
    }

    public function test_large_university_first_page_is_capped_and_links_to_next_pages(): void
    {
        $this->seedFixture();
        $html = $this->get('/tr/universities/' . self::BIG)->assertOk()->getContent();

        $links = $this->programLinks($html);
        $this->assertCount(UniversityWebController::PROGRAMS_PER_PAGE, $links, 'ilk HTML yanıtında en fazla 24 program bağlantısı');
        $this->assertCount(24, array_unique($links));
        $this->assertNotContains('inactive-01', $links);
        // Gerçek <a href> sayfalama bağlantıları (JS'siz)
        $this->assertMatchesRegularExpression('#<a href="[^"]*/tr/universities/' . self::BIG . '\?page=2\#programs" rel="next"#', $html);
        $this->assertStringContainsString('/tr/universities/' . self::BIG . '?page=3#programs', $html);
        $this->assertStringNotContainsString('?page=1', $html, 'sayfa 1 bağlantısı page parametresi taşımaz');
        // Sayaçlar tüm aktif programlardan
        $this->assertStringContainsString('(60)', $html);
        // Anlamlı metni olan programlar önce: ilk kart zengin bir program (ba-03, ba-06 …)
        $this->assertSame('ba-03', $links[0]);
        // Sayfa 1 canonical/hreflang değişmedi
        $this->assertSame(url('/tr/universities/' . self::BIG), $this->canonical($html));
        $this->assertSame(url('/en/universities/' . self::BIG), $this->hreflangs($html)['en']);
    }

    public function test_page_two_and_last_page_are_self_canonical_with_matching_hreflang(): void
    {
        $this->seedFixture();
        $p2 = $this->get('/tr/universities/' . self::BIG . '?page=2')->assertOk()->getContent();
        $this->assertCount(24, $this->programLinks($p2));
        $this->assertSame(url('/tr/universities/' . self::BIG) . '?page=2', $this->canonical($p2));
        $this->assertMatchesRegularExpression('#rel="prev"#', $p2);
        $this->assertMatchesRegularExpression('#rel="next"#', $p2);

        $last = $this->get('/de/universities/' . self::BIG . '?page=3')->assertOk()->getContent();
        $this->assertCount(12, $this->programLinks($last));
        $this->assertStringNotContainsString('rel="next"', $last);
        $this->assertSame(url('/de/universities/' . self::BIG) . '?page=3', $this->canonical($last));
        $alts = $this->hreflangs($last);
        foreach (['tr', 'en', 'de'] as $loc) {
            $this->assertSame(url("/{$loc}/universities/" . self::BIG) . '?page=3', $alts[$loc], "hreflang $loc");
        }
        $this->assertSame(url('/en/universities/' . self::BIG) . '?page=3', $alts['x-default']);
        // Programlar sekmesi açık başlar
        $this->assertStringContainsString("x-data=\"{ tab: 'prog' }\"", $last);
    }

    public function test_walking_all_pages_returns_every_active_program_exactly_once(): void
    {
        $this->seedFixture();
        $all = [];
        foreach ([1, 2, 3] as $n) {
            $all = array_merge($all, $this->programLinks($this->get('/en/universities/' . self::BIG . ($n > 1 ? "?page=$n" : ''))->getContent()));
        }
        $expected = DB::table('programs')->join('universities', 'universities.id', '=', 'programs.university_id')
            ->where('universities.slug', self::BIG)->where('programs.is_active', 1)->pluck('programs.slug')->all();
        $this->assertCount(60, $all);
        $this->assertSame([], array_values(array_diff_assoc($all, array_unique($all))), 'tekrar yok');
        $this->assertEqualsCanonicalizing($expected, $all, 'kayıp yok');
    }

    public function test_degree_and_language_filters_and_filter_with_pagination(): void
    {
        $this->seedFixture();
        $ba = $this->get('/tr/universities/' . self::BIG . '?degree=bachelor')->assertOk()->getContent();
        $baLinks = $this->programLinks($ba);
        $this->assertCount(24, $baLinks);
        $this->assertTrue(collect($baLinks)->every(fn ($s) => str_starts_with($s, 'ba-')));
        $this->assertStringContainsString('?degree=bachelor&amp;page=2#programs', $ba);
        // Filtreli URL → canonical ve hreflang filtresiz üniversite URL'si
        $this->assertSame(url('/tr/universities/' . self::BIG), $this->canonical($ba));
        $this->assertSame(url('/en/universities/' . self::BIG), $this->hreflangs($ba)['en']);

        $ba2 = $this->get('/tr/universities/' . self::BIG . '?degree=bachelor&page=2')->assertOk()->getContent();
        $this->assertCount(6, $this->programLinks($ba2));
        $this->assertSame(url('/tr/universities/' . self::BIG), $this->canonical($ba2));

        $ma = $this->programLinks($this->get('/tr/universities/' . self::BIG . '?degree=master')->assertOk()->getContent());
        $this->assertCount(24, $ma);
        $this->assertTrue(collect($ma)->every(fn ($s) => str_starts_with($s, 'ma-')));

        $en = $this->programLinks($this->get('/tr/universities/' . self::BIG . '?lang=en')->assertOk()->getContent());
        $en2 = $this->programLinks($this->get('/tr/universities/' . self::BIG . '?lang=en&page=2')->assertOk()->getContent());
        $this->assertCount(35, array_merge($en, $en2), '20 bachelor + 15 master İngilizce');
        $this->assertSame([], array_values(array_filter(array_merge($en, $en2), fn ($s) => preg_match('/^(ba-(2[1-9]|30)|ma-(1[6-9]|2\d)|phd)/', $s))));

        $maEn = $this->programLinks($this->get('/tr/universities/' . self::BIG . '?degree=master&lang=en')->assertOk()->getContent());
        $this->assertCount(15, $maEn);
    }

    public function test_invalid_pages_and_filters(): void
    {
        $this->seedFixture();
        $base = '/tr/universities/' . self::BIG;
        $this->get("$base?page=1")->assertStatus(301)->assertRedirect(url($base));
        $this->get("$base?degree=master&page=1")->assertStatus(301)->assertRedirect(url($base) . '?degree=master');
        foreach (['?page=4', '?page=0', '?page=-1', '?page=abc', '?page=2x', '?page[]=2', '?degree=xyz', '?degree=sprachkurs', '?lang=fr', '?degree=master&page=3'] as $q) {
            $this->get($base . $q)->assertNotFound();
        }
        // Bilinmeyen başka parametre sayfayı bozmaz (canonical zaten query'siz)
        $this->get("$base?utm_source=x")->assertOk();
    }

    public function test_empty_filter_combinations_are_not_linked_and_return_404(): void
    {
        $this->seedFixture();
        $base = '/tr/universities/' . self::BIG;
        // phd yalnız Almanca: phd + en kombinasyonu boş
        $this->get("$base?degree=phd&lang=en")->assertNotFound();
        $phd = $this->get("$base?degree=phd")->assertOk()->getContent();
        $this->assertStringNotContainsString('degree=phd&amp;lang=en', $phd, 'sonuç vermeyen dil çipi gösterilmez');
        $this->assertStringContainsString('degree=phd&amp;lang=de', $phd);
        $en = $this->get("$base?lang=en")->assertOk()->getContent();
        $this->assertStringNotContainsString('degree=phd&amp;lang=en', $en, 'sonuç vermeyen derece çipi gösterilmez');
    }

    public function test_filter_links_are_nofollow_while_unfiltered_pagination_stays_followable(): void
    {
        $this->seedFixture();
        $base = '/tr/universities/' . self::BIG;
        $html = $this->get($base)->assertOk()->getContent();
        // Filtre çipleri nofollow
        $this->assertMatchesRegularExpression('#href="[^"]*\?degree=master\#programs" rel="nofollow"#', $html);
        $this->assertMatchesRegularExpression('#href="[^"]*\?lang=en\#programs" rel="nofollow"#', $html);
        // Filtresiz sayfalama takip edilebilir
        $this->assertMatchesRegularExpression('#href="[^"]*\?page=2\#programs" rel="next"#', $html);
        $this->assertDoesNotMatchRegularExpression('#\?page=2\#programs" rel="next nofollow"#', $html);
        // Filtreli sayfalama nofollow
        $f = $this->get("$base?degree=bachelor")->assertOk()->getContent();
        $this->assertStringContainsString('rel="next nofollow"', $f);
        // Filtreli sayfa 2: canonical ve hreflang filtresiz sayfa 1
        $f2 = $this->get("$base?degree=bachelor&page=2")->assertOk()->getContent();
        $this->assertSame(url($base), $this->canonical($f2));
        $this->assertSame(url('/de/universities/' . self::BIG), $this->hreflangs($f2)['de']);
        $this->assertStringContainsString('rel="prev nofollow"', $f2);
    }

    public function test_pagination_and_filters_are_accessible_on_mobile(): void
    {
        $this->seedFixture();
        $html = $this->get('/tr/universities/' . self::BIG . '?page=2')->assertOk()->getContent();
        $this->assertStringContainsString('aria-label="' . __('Programs pagination') . '"', $html);
        $this->assertStringContainsString('aria-label="' . __('Filter programs') . '"', $html);
        $this->assertMatchesRegularExpression('#<span aria-current="page"[^>]*min-h-\[44px\][^>]*>2</span>#', $html);
        $this->assertStringContainsString('flex-wrap', $html);
        $this->assertStringContainsString('id="programs"', $html);
    }

    public function test_program_detail_links_still_resolve(): void
    {
        $this->seedFixture();
        $links = $this->programLinks($this->get('/tr/universities/' . self::BIG)->getContent());
        $this->get('/tr/programs/' . $links[0])->assertOk();
        $this->assertStringContainsString('/tr/programs/' . $links[0], route('programs.show', ['locale' => 'tr', 'slug' => $links[0]]));
    }
}

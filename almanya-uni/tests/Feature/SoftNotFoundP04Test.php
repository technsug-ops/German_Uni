<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\CityController;
use App\Http\Controllers\Web\UniversityWebController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * SEO Recovery P0-4 (soft 404) — docs/SEO_RECOVERY_P0_4_RESULT.md.
 *
 * C1 boş programatik landing → 404 (sitemap koşuluyla aynı), aralık dışı sayfa → 404, çipler boş hedefe bağlanmaz.
 * C2 sayısal ID → kanonik slug'a tek 301; 003 / 3-xyz / bilinmeyen → 404.
 * C3 pasif üniversite/şehir → eşlemeli 301, 225 → 410, eyalet-şehir → /states/{slug}.
 * C4 ölü blog_redirects → yayındaki eşdeğere tek 301 ya da doğrudan 404.
 */
class SoftNotFoundP04Test extends TestCase
{
    use RefreshDatabase;

    private const L = ['tr', 'en', 'de'];

    private int $stateId;

    private int $cityId;

    private int $emptyCityId;

    private int $fieldId;

    private int $otherFieldId;

    private int $uniId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stateId = DB::table('states')->insertGetId(['name_tr' => 'Bavyera', 'name_de' => 'Bayern', 'name_en' => 'Bavaria', 'slug' => 'bayern', 'created_at' => now(), 'updated_at' => now()]);
        $this->cityId = $this->city('testburg-q1', true);
        $this->emptyCityId = $this->city('leerstadt-q2', true);
        $this->fieldId = DB::table('fields_of_study')->insertGetId(['slug' => 'engineering', 'name_tr' => 'Mühendislik', 'name_de' => 'Ingenieurwesen', 'name_en' => 'Engineering', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $this->otherFieldId = DB::table('fields_of_study')->insertGetId(['slug' => 'arts-design', 'name_tr' => 'Sanat', 'name_de' => 'Kunst', 'name_en' => 'Arts', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $this->uniId = $this->uni('testburg-university', true, $this->cityId);
    }

    /* ---------------------------------------------------------------- yardımcılar */

    private function city(string $slug, bool $active, ?int $stateId = null, ?string $name = null): int
    {
        $name ??= Str::title(Str::before($slug, '-q'));

        return DB::table('cities')->insertGetId(['name_tr' => $name, 'name_de' => $name, 'name_en' => $name, 'slug' => $slug, 'state_id' => $stateId ?? $this->stateId, 'is_active' => $active, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function uni(string $slug, bool $active, ?int $cityId = null): int
    {
        return DB::table('universities')->insertGetId(['name_tr' => $slug, 'name_de' => $slug, 'name_en' => $slug, 'slug' => $slug, 'city_id' => $cityId, 'is_active' => $active, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function program(string $degree = 'bachelor', string $language = 'de', ?string $admission = null, ?int $uniId = null, ?int $fieldId = null): void
    {
        $name = 'Studiengang ' . Str::random(8);
        DB::table('programs')->insert([
            'university_id' => $uniId ?? $this->uniId, 'field_of_study_id' => $fieldId ?? $this->fieldId,
            'name_de' => $name, 'slug' => Str::slug($name) . '-' . Str::lower(Str::random(6)),
            'degree' => $degree, 'language' => $language, 'admission_mode' => $admission, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function path(?string $url): string
    {
        return (string) parse_url((string) $url, PHP_URL_PATH);
    }

    private function assertIndexable(string $url, string $canonicalPath): \Illuminate\Testing\TestResponse
    {
        view()->share('localeUrls', null);
        $r = $this->get($url)->assertOk();
        $html = $r->getContent();
        $this->assertMatchesRegularExpression('~<meta name="robots" content="index~', $html, "{$url} index değil");
        $this->assertMatchesRegularExpression('~<link rel="canonical" href="[^"]*' . preg_quote($canonicalPath, '~') . '"~', $html, "{$url} canonical");
        $bare = preg_replace('~^/(tr|en|de)~', '', $canonicalPath);
        // Not: hreflang href isteğin sorgu dizgisini taşıyor (bilinen, kapsam dışı P2; audit §6.4) → sorgu opsiyonel.
        foreach (self::L as $l) {
            $this->assertMatchesRegularExpression('~<link rel="alternate" hreflang="' . $l . '" href="[^"]*/' . $l . preg_quote($bare, '~') . '(\?[^"]*)?"~', $html, "{$url} hreflang {$l}");
        }

        return $r;
    }

    private function assertSingleHop(string $from, string $to): void
    {
        view()->share('localeUrls', null);
        $r = $this->get($from);
        $this->assertSame(301, $r->getStatusCode(), "{$from} 301 değil");
        $this->assertSame($to, $this->path($r->headers->get('Location')), "{$from} yanlış hedef");
        $this->assertNull(parse_url((string) $r->headers->get('Location'), PHP_URL_QUERY), "{$from} sorgu taşıdı");
        view()->share('localeUrls', null);
        $this->assertSame(200, $this->get($to)->getStatusCode(), "{$to} 200 değil (zincir/404)");
    }

    /* ======================================================== C1 boş landing'ler */

    public function test_empty_city_field_landing_is_404_and_filled_one_is_indexable(): void
    {
        $this->program('bachelor');

        foreach (self::L as $l) {
            $this->assertIndexable("/{$l}/programs/city/testburg-q1/field/engineering", "/{$l}/programs/city/testburg-q1/field/engineering");
            $this->get("/{$l}/programs/city/testburg-q1/field/arts-design")->assertNotFound();
            $this->get("/{$l}/programs/city/leerstadt-q2/field/engineering")->assertNotFound();
        }
    }

    public function test_degree_variant_404_when_empty_and_canonical_bare_when_filled(): void
    {
        $this->program('bachelor');

        $this->get('/en/programs/city/testburg-q1/field/engineering?degree=phd')->assertNotFound();
        $this->get('/tr/programs/city/testburg-q1/field/engineering?degree=master')->assertNotFound();
        $this->assertIndexable('/de/programs/city/testburg-q1/field/engineering?degree=bachelor', '/de/programs/city/testburg-q1/field/engineering');
    }

    public function test_city_language_city_nc_free_field_degree_and_field_language_empty_are_404(): void
    {
        $this->program('bachelor', 'de');

        $this->get('/en/programs/city/testburg-q1/language/en')->assertNotFound();
        $this->assertIndexable('/en/programs/city/testburg-q1/language/de', '/en/programs/city/testburg-q1/language/de');

        $this->get('/de/cities/testburg-q1/nc-free')->assertNotFound();
        $this->program('master', 'de', 'zulassungsfrei');
        $this->assertIndexable('/de/cities/testburg-q1/nc-free', '/de/cities/testburg-q1/nc-free');

        $this->get('/tr/programs/field/engineering/degree/phd')->assertNotFound();
        $this->assertIndexable('/tr/programs/field/engineering/degree/master', '/tr/programs/field/engineering/degree/master');

        $this->get('/en/programs/field/engineering/language/en')->assertNotFound();
        $this->get('/en/programs/field/engineering/language/de?degree=phd')->assertNotFound();
        $this->assertIndexable('/en/programs/field/engineering/language/de', '/en/programs/field/engineering/language/de');
    }

    public function test_page_beyond_last_page_and_invalid_page_are_404(): void
    {
        for ($i = 0; $i < 21; $i++) {
            $this->program('bachelor');
        }

        $page2 = $this->get('/en/programs/city/testburg-q1/field/engineering?page=2')->assertOk();
        // Eski hata: toplam paginate()'ten sonra sayıldığı için 2. sayfada "0" görünüyordu.
        $this->assertMatchesRegularExpression('~<strong class="text-white">21</strong>~', $page2->getContent());

        foreach (['3', '999', '0', 'abc', '-1', '2.5'] as $p) {
            $this->get("/en/programs/city/testburg-q1/field/engineering?page={$p}")->assertNotFound();
        }
        $this->get('/en/programs/field/engineering/degree/bachelor?page=99')->assertNotFound();
        $this->get('/en/programs/field/engineering/language/de?page=99')->assertNotFound();
        $this->get('/en/programs/city/testburg-q1/language/de?page=99')->assertNotFound();
    }

    public function test_the_normal_program_search_screen_is_not_turned_into_404(): void
    {
        $this->program('bachelor');

        // Kullanıcı arama/filtre ekranı (Faz 2A kuralları): geçerli ama sonuçsuz filtre 200 kalır.
        $this->get('/en/programs?field=engineering&degree=phd')->assertOk();
        $this->get('/en/programs?q=nonexistent-xyz')->assertOk();
    }

    public function test_city_field_chips_only_link_to_non_empty_targets(): void
    {
        $this->program('bachelor', 'de');

        $html = $this->get('/en/programs/city/testburg-q1/field/engineering')->assertOk()->getContent();
        $this->assertStringContainsString('/en/programs/city/testburg-q1/field/engineering?degree=bachelor', $html);
        $this->assertStringNotContainsString('?degree=master', $html);
        $this->assertStringNotContainsString('?degree=phd', $html);
        $this->assertStringNotContainsString('/programs/city/testburg-q1/language/en', $html, 'boş English programs çipi');

        // İngilizce program eklenince çip görünür; seçili derece bağlantı değil, aria-current ile işaretli.
        $this->program('master', 'en');
        $html = $this->get('/en/programs/city/testburg-q1/field/engineering?degree=master')->assertOk()->getContent();
        $this->assertStringContainsString('/en/programs/city/testburg-q1/language/en', $html);
        $this->assertMatchesRegularExpression('~<span[^>]*aria-current="page"[^>]*>\s*Master \(1\)~', $html);
        // Seçili derece çipi bağlantı değil (dil değiştirici hariç: hreflang="…" taşıyan <a>'lar).
        $this->assertDoesNotMatchRegularExpression('~<a [^>]*>Master \(\d+\)</a>~', $html);
        $this->assertMatchesRegularExpression('~<a href="[^"]*/en/programs/city/testburg-q1/field/engineering" class="[^"]*">All degrees</a>~', $html, '"All degrees" geri dönüş bağlantısı');

        // Her çip/kenar çubuğu landing hedefi gerçekten 200.
        preg_match_all('~href="https?://[^/"]+(/en/programs/(?:city|field)/[^"]+)"~', $html, $m);
        foreach (array_unique($m[1]) as $href) {
            $this->get(html_entity_decode($href))->assertOk();
        }
    }

    public function test_field_language_chips_only_link_to_non_empty_targets(): void
    {
        $this->program('bachelor', 'de');

        $html = $this->get('/tr/programs/field/engineering/language/de')->assertOk()->getContent();
        $this->assertStringContainsString('/tr/programs/field/engineering/language/de?degree=bachelor', $html);
        $this->assertStringNotContainsString('language/de?degree=phd', $html);
        $this->assertStringNotContainsString('/tr/programs/field/engineering/language/en', $html, 'boş diğer-dil çipi');

        $this->program('phd', 'both');
        $html = $this->get('/tr/programs/field/engineering/language/de')->assertOk()->getContent();
        $this->assertStringContainsString('/tr/programs/field/engineering/language/en', $html);
        $this->assertStringContainsString('language/de?degree=phd', $html);
    }

    public function test_sitemap_landing_urls_all_render_200_index(): void
    {
        $this->program('bachelor', 'en');
        $this->program('master', 'de', 'zulassungsfrei');
        $other = $this->uni('other-university', true, $this->emptyCityId);
        DB::table('cities')->where('id', $this->emptyCityId)->update(['is_active' => true]);
        $this->program('phd', 'both', null, $other, $this->otherFieldId);

        $checked = 0;
        foreach (self::L as $l) {
            $xml = $this->get("/sitemap-{$l}.xml")->assertOk()->getContent();
            preg_match_all('~<loc>([^<]+)</loc>~', $xml, $m);
            foreach ($m[1] as $loc) {
                $p = $this->path(html_entity_decode($loc));
                if (! preg_match('~^/(tr|en|de)/(programs/(city|field)/|cities/[^/]+/nc-free$)~', $p)) {
                    continue;
                }
                $this->assertIndexable($p, $p);
                $checked++;
            }
        }
        $this->assertGreaterThan(20, $checked);
    }

    /* ======================================================== C1 üniversite NC-free butonu */

    public function test_university_sidebar_nc_free_button_only_when_it_has_programs(): void
    {
        $this->program('bachelor');
        $needle = '~href="[^"]*/en/universities/testburg-university/nc-free"~';

        $this->assertDoesNotMatchRegularExpression($needle, $this->get('/en/universities/testburg-university')->assertOk()->getContent());
        // Boş NC-free sayfası bilinçli olarak 200 + noindex kalır (karar: rapor §C1).
        $this->get('/en/universities/testburg-university/nc-free')->assertOk()->assertSee('noindex', false);

        $this->program('master', 'de', 'zulassungsfrei');
        $this->assertMatchesRegularExpression($needle, $this->get('/en/universities/testburg-university')->assertOk()->getContent());
    }

    /* ======================================================== C2 sayısal ID */

    public function test_numeric_university_id_redirects_once_to_slug(): void
    {
        $id = $this->uniId;
        foreach (self::L as $l) {
            $this->assertSingleHop("/{$l}/universities/{$id}", "/{$l}/universities/testburg-university");
        }
        $this->assertSingleHop("/en/universities/{$id}?utm_source=x&page=3", '/en/universities/testburg-university');

        $this->get("/en/universities/00{$id}")->assertNotFound();
        $this->get("/en/universities/{$id}-xyz")->assertNotFound();
        $this->get('/en/universities/999999')->assertNotFound();
        $this->get('/en/universities/0')->assertNotFound();
        $this->get("/en/universities/{$id}/nc-free")->assertNotFound();
        $this->get('/en/universities/testburg-university')->assertOk();
    }

    public function test_numeric_city_id_redirects_once_to_slug(): void
    {
        $id = $this->cityId;
        foreach (self::L as $l) {
            $this->assertSingleHop("/{$l}/cities/{$id}", "/{$l}/cities/testburg-q1");
        }
        $this->get("/en/cities/00{$id}")->assertNotFound();
        $this->get("/en/cities/{$id}-xyz")->assertNotFound();
        $this->get('/en/cities/999999')->assertNotFound();
        $this->get('/en/cities/testburg-q1')->assertOk();
    }

    /* ======================================================== C3 pasif kayıtlar */

    public function test_passive_university_with_equivalent_redirects_and_its_id_too(): void
    {
        $shell = 'hochschule-kempten-partner-019ddbba';
        $target = UniversityWebController::MERGED_SLUGS[$shell];
        $shellId = $this->uni($shell, false);
        $this->uni($target, true, $this->cityId);

        $this->assertSingleHop("/de/universities/{$shell}", "/de/universities/{$target}");
        $this->assertSingleHop("/en/universities/{$shellId}", "/en/universities/{$target}");

        // Eski birleştirilmiş kabuk da ID ile tek adımda hedefe.
        $merged = 'fachhochschule-macromedia-q1346417';
        $mergedId = $this->uni($merged, false);
        $this->uni(UniversityWebController::MERGED_SLUGS[$merged], true, $this->cityId);
        $this->assertSingleHop("/tr/universities/{$mergedId}", '/tr/universities/' . UniversityWebController::MERGED_SLUGS[$merged]);
    }

    public function test_removed_university_is_410_and_review_records_are_unchanged(): void
    {
        $gone = UniversityWebController::GONE_SLUGS[0];
        $goneId = $this->uni($gone, false);
        foreach (self::L as $l) {
            $this->get("/{$l}/universities/{$gone}")->assertStatus(410)->assertSee('noindex', false);
        }
        $this->get("/en/universities/{$gone}")->assertSee('This university is no longer listed');
        $this->get("/en/universities/{$goneId}")->assertStatus(410);

        // NEEDS_REVIEW (araştırma enstitüsü, aktif programlı pasif kayıt): slug davranışı değişmedi, ID → 404.
        $review = $this->uni('german-cancer-research-center-dkfz-heidelberg-partner-019de9f1', false, $this->cityId);
        $this->get('/en/universities/german-cancer-research-center-dkfz-heidelberg-partner-019de9f1')->assertOk();
        $this->get("/en/universities/{$review}")->assertNotFound();
    }

    public function test_state_records_in_cities_redirect_to_state_page(): void
    {
        $stateCity = $this->city('bayern-q980', false, $this->stateId, 'Bayern');
        foreach (self::L as $l) {
            $this->assertSingleHop("/{$l}/cities/bayern-q980", "/{$l}/states/bayern");
        }
        $this->assertSingleHop("/en/cities/{$stateCity}", '/en/states/bayern');

        // Her eşleme hedefi gerçek ve aynı adlı eyalete gider (benzer ad değil).
        foreach (CityController::PASSIVE_REDIRECTS as $from => [$route, $to]) {
            if ($route === 'states.show') {
                $this->assertStringStartsWith($to . '-q', $from);
            }
        }
    }

    public function test_bonn_duplicate_redirects_and_unmapped_passive_city_is_unchanged(): void
    {
        $this->city('bonn-q892684', true, null, 'Bonn');
        $dupId = $this->city('bonn-q586', false, null, 'Bonn');
        $this->assertSingleHop('/en/cities/bonn-q586', '/en/cities/bonn-q892684');
        $this->assertSingleHop("/de/cities/{$dupId}", '/de/cities/bonn-q892684');

        $mitte = $this->city('mitte-q883803', false, null, 'Mitte');
        $this->get('/en/cities/mitte-q883803')->assertOk();
        $this->get("/en/cities/{$mitte}")->assertNotFound();
    }

    public function test_passive_city_redirect_is_ignored_once_the_record_is_reactivated(): void
    {
        $this->city('bonn-q586', true, null, 'Bonn');
        $this->get('/en/cities/bonn-q586')->assertOk();
    }

    /* ======================================================== C4 ölü blog yönlendirmeleri */

    private function blogPost(string $slug, string $locale, bool $published = true, string $type = 'blog'): void
    {
        // Bazı veri migration'ları test DB'sine aynı slug'lı yazı ekliyor → önce temizle.
        DB::table('posts')->where('slug', $slug)->where('locale', $locale)->delete();
        DB::table('posts')->insert([
            'title' => "{$slug} title", 'slug' => $slug, 'content_md' => "## {$slug}\n\nText.", 'locale' => $locale, 'type' => $type,
            'is_published' => $published, 'published_at' => $published ? now()->subDays(2) : null,
            'translation_group_id' => (string) Str::uuid(), 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function redirectRow(string $from, string $to, string $locale): void
    {
        DB::table('blog_redirects')->insert(['from_slug' => $from, 'to_slug' => $to, 'locale' => $locale, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function runRepointMigration(): void
    {
        (require database_path('migrations/2026_10_14_004000_repoint_dead_blog_redirects.php'))->up();
    }

    public function test_redirect_to_unpublished_post_is_a_direct_404_not_a_chain(): void
    {
        $this->blogPost('dead-target-en', 'en', false);
        $this->redirectRow('old-slug-en', 'dead-target-en', 'en');

        $this->get('/en/blog/old-slug-en')->assertNotFound();
    }

    public function test_redirect_to_published_news_goes_straight_to_news_url(): void
    {
        DB::table('menu_pages')->updateOrInsert(['key' => 'news.index'], ['label' => 'News', 'is_enabled' => true, 'created_at' => now(), 'updated_at' => now()]);
        \App\Models\MenuPage::flushCache();
        $this->blogPost('fresh-news-en', 'en', true, 'news');
        $this->redirectRow('old-news-en', 'fresh-news-en', 'en');

        $this->assertSingleHop('/en/blog/old-news-en', '/en/news/fresh-news-en');
    }

    public function test_repoint_migration_fixes_dead_rows_single_hop_and_is_idempotent_and_safe(): void
    {
        $map = collect((new \ReflectionClass(require database_path('migrations/2026_10_14_004000_repoint_dead_blog_redirects.php')))->getConstant('MAP'));
        $this->assertCount(12, $map);

        foreach ($map as [$locale, $from, $oldTo, $newTo]) {
            $this->blogPost($oldTo, $locale, false);
            $this->redirectRow($from, $oldTo, $locale);
            $this->blogPost($newTo, $locale, true);
        }
        // Güvenlik: yeni hedef yayında değilse dokunma; satır başka yere işaret ediyorsa dokunma.
        [$l1, $f1, $o1, $n1] = $map[0];
        DB::table('posts')->where('slug', $n1)->where('locale', $l1)->update(['is_published' => false]);
        [$l2, $f2] = $map[1];
        DB::table('blog_redirects')->where('from_slug', $f2)->update(['to_slug' => 'manually-changed']);

        $this->runRepointMigration();
        $this->runRepointMigration(); // idempotent

        $this->assertSame($o1, DB::table('blog_redirects')->where('from_slug', $f1)->value('to_slug'));
        $this->assertSame('manually-changed', DB::table('blog_redirects')->where('from_slug', $f2)->value('to_slug'));
        $this->get("/{$l1}/blog/{$f1}")->assertNotFound();

        foreach ($map->slice(2) as [$locale, $from, $oldTo, $newTo]) {
            $this->assertSame($newTo, DB::table('blog_redirects')->where('from_slug', $from)->where('locale', $locale)->value('to_slug'));
            $this->assertSingleHop("/{$locale}/blog/{$from}", "/{$locale}/blog/{$newTo}");
        }
    }

    public function test_repoint_migration_leaves_row_when_old_target_was_republished(): void
    {
        [$locale, $from, $oldTo, $newTo] = (new \ReflectionClass(require database_path('migrations/2026_10_14_004000_repoint_dead_blog_redirects.php')))->getConstant('MAP')[5];
        $this->blogPost($oldTo, $locale, true);
        $this->blogPost($newTo, $locale, true);
        $this->redirectRow($from, $oldTo, $locale);

        $this->runRepointMigration();

        $this->assertSame($oldTo, DB::table('blog_redirects')->where('from_slug', $from)->value('to_slug'));
    }
}

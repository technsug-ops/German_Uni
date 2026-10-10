<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Program;
use App\Models\ProgramVerification;
use App\Support\UniversityCollections;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

/**
 * SEO Growth Batch 3 (2026-10-10): İngilizce eğitim koleksiyonu, /english-taught, İngilizce yüksek lisans rehberi,
 * Almanca seviye/belge rehberi, dil belgeleri aracı ve iki destekleyici rehber. Testler migration güvenliğini
 * (eksik hedef, beklenmeyen durum, rollback, no-op, prod/lokal slug farkı), "tamamen İngilizce" ile "iki dilli"
 * ayrımını, dil çelişkisi olan kaydın listelenmemesini ve sayfa çıktılarını sabitler.
 */
class SeoGrowthBatch3Test extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_10_10_000500_apply_batch3_english_taught_and_language_guides.php';

    private function data(): array
    {
        return json_decode(file_get_contents(base_path('database/data/seo-batch3/posts.json')), true);
    }

    private function run500(): void
    {
        (require base_path(self::MIGRATION))->up();
    }

    /** Canlıdaki eski durum: ana yazılar eski başlıkla, destekleyici yazılar eski cümlelerle. $prod: prod slug + prod varyantı. */
    private function seedPosts(bool $prod = false, array $skip = []): void
    {
        $data = $this->data();
        $pick = fn (array $slugs) => $prod ? $slugs[0] : $slugs[count($slugs) - 1];
        foreach ($data['replace'] as $r) {
            $slug = $pick($r['slugs']);
            if (in_array($slug, $skip, true)) {
                continue;
            }
            $this->insertPost($r['locale'], $slug, $prod ? $r['old_titles'][0] : end($r['old_titles']),
                "## Eski bölüm\n\nAPS belgesi Türkiye'den başvuranlar için zorunludur.", 'Eski özet.', 'Eski açıklama.');
        }
        foreach ($data['fixes'] as $f) {
            $slug = $pick($f['slugs']);
            if (in_array($slug, $skip, true)) {
                continue;
            }
            $body = implode("\n\n", array_map(fn ($rule) => $prod ? end($rule[0]) : $rule[0][0], $f['rules']));
            $this->insertPost($f['locale'], $slug, 'Rehber ' . $slug, "## 1. Bölüm\n\n" . $body . "\n\n## 2. Bölüm\n\nMetin.", 'Özet.', 'Açıklama.');
        }
    }

    private function insertPost(string $locale, string $slug, string $title, string $md, string $excerpt, string $meta): void
    {
        DB::table('posts')->updateOrInsert(['locale' => $locale, 'slug' => $slug], [
            'title' => $title, 'content_md' => $md, 'content_html' => '<p>eski</p>', 'excerpt' => $excerpt, 'meta_description' => $meta,
            'is_published' => 1, 'published_at' => now()->subDay(), 'created_at' => now()->subDay(), 'updated_at' => now()->subDay(),
        ]);
    }

    private function rows(): array
    {
        return DB::table('posts')->orderBy('id')->get(['slug', 'title', 'meta_title', 'meta_description', 'excerpt', 'content_md', 'content_html', 'featured_image_caption', 'updated_at'])->toArray();
    }

    public static function slugVariants(): array
    {
        return ['lokal slug ve metin' => [false], 'prod slug ve metin' => [true]];
    }

    #[DataProvider('slugVariants')]
    public function test_applies_all_guides_and_second_run_changes_nothing(bool $prod): void
    {
        $this->seedPosts($prod);
        $this->run500();
        $after = $this->rows();
        $this->run500();
        $this->assertEquals($after, $this->rows(), 'ikinci çalıştırma no-op (updated_at dahil)');

        $data = $this->data();
        foreach ($data['replace'] as $r) {
            $p = Post::where('locale', $r['locale'])->whereIn('slug', $r['slugs'])->firstOrFail();
            $this->assertSame($r['title'], $p->title);
            $this->assertSame($r['meta_description'], $p->meta_description);
            $this->assertStringNotContainsString('APS belgesi', $p->content_md);
            $this->assertStringContainsString('<table>', $p->content_html, 'content_html md\'den yeniden üretilir');
            $this->assertDoesNotMatchRegularExpression($data['leftover'], $p->content_md . $p->excerpt . $p->meta_description);
            $this->assertStringNotContainsString('AlmanyaUni', $p->content_md);
            $this->assertLessThanOrEqual(160, mb_strlen($p->meta_description));
        }
        foreach ($data['fixes'] as $f) {
            $p = Post::where('locale', $f['locale'])->whereIn('slug', $f['slugs'])->firstOrFail();
            $this->assertDoesNotMatchRegularExpression($data['leftover'], $p->content_md);
            $this->assertSame(1, substr_count($p->content_html, "href=\"/{$f['locale']}/english-taught\""), $p->slug);
            $this->assertSame(1, substr_count($p->content_html, "href=\"/{$f['locale']}/universities/collections/english-taught-universities\""), $p->slug);
        }

        // Yüksek lisans rehberi ücret cümlesi ücret rehberiyle tutarlı: TUM istisnası ve Semesterbeitrag ayrı.
        $tr = Post::where('locale', 'tr')->where('slug', 'doing-a-masters-in-germany-2026-a-z-guide')->first();
        $this->assertStringContainsString('TUM gibi bazı üniversiteler AB/AEA dışından gelen öğrencilerden kendi ücretini alıyor', $tr->content_md);
        $this->assertStringContainsString('Semesterbeitrag) her yerde ayrıca ödenir', $tr->content_md);
    }

    public function test_unexpected_title_fails_and_writes_nothing(): void
    {
        $this->seedPosts();
        DB::table('posts')->where('locale', 'en')->where('slug', 'english-masters-in-germany-without-german-knowledge-finding-programs-and-application-en')
            ->update(['title' => 'Someone edited this guide']);
        $before = $this->rows();

        try {
            $this->run500();
            $this->fail('Beklenmeyen başlık sessizce geçmemeli');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('beklenmeyen başlık', $e->getMessage());
        }
        $this->assertEquals($before, $this->rows(), 'hiçbir yazıya yazılmamalı');
    }

    public function test_missing_old_sentence_fails_and_writes_nothing(): void
    {
        $this->seedPosts();
        DB::table('posts')->where('locale', 'de')->where('slug', 'doing-a-masters-in-germany-2026-a-z-guide-de')
            ->update(['content_md' => "## 1. Bölüm\n\nGanz anderer Text."]);
        $before = $this->rows();

        try {
            $this->run500();
            $this->fail('Eksik cümle sessizce geçmemeli');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('doing-a-masters-in-germany-2026-a-z-guide-de düzeltme 1', $e->getMessage());
        }
        $this->assertEquals($before, $this->rows());
    }

    public function test_missing_target_and_ambiguous_slug_fail(): void
    {
        $this->seedPosts(skip: ['goethe-telc-testdaf-dsh-differences-german-language-exam-comparison-for-turkish-de']);
        $this->insertPost('en', 'goethe-telc-testdaf-dsh-difference-german-language-exam-comparison-for-turkish-en', 'x', 'x', 'x', 'x');

        try {
            $this->run500();
            $this->fail('Eksik ya da çift aday sessizce geçmemeli');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('-de: bulunamadı', $e->getMessage());
            $this->assertStringContainsString('birden fazla aday', $e->getMessage());
        }
    }

    public function test_empty_database_is_a_noop_only_in_testing_environment(): void
    {
        $this->run500();
        $this->assertSame(0, Post::where('slug', 'like', 'english-masters-in-germany-without-german-knowledge%')->count());

        $env = $this->app['env'];
        $this->app['env'] = 'production';
        try {
            $this->run500();
            $this->fail('Production\'da hedeflerin olmaması sessizce geçmemeli');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('bulunamadı', $e->getMessage());
        } finally {
            $this->app['env'] = $env;
        }
    }

    private function uni(string $slug = 'b3-uni'): int
    {
        $city = DB::table('cities')->insertGetId(['name_de' => 'Teststadt', 'name_tr' => 'Test', 'slug' => $slug . '-city', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);

        return DB::table('universities')->insertGetId(['name_de' => 'Test Universität ' . $slug, 'name_tr' => 'Test', 'slug' => $slug, 'city_id' => $city,
            'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function program(int $uni, string $slug, string $degree, string $language): Program
    {
        DB::table('programs')->insert(['name_de' => 'B3 ' . $slug, 'slug' => $slug, 'degree' => $degree, 'language' => $language, 'university_id' => $uni,
            'is_active' => true, 'source' => 'partner', 'created_at' => now(), 'updated_at' => now()]);

        return Program::where('slug', $slug)->firstOrFail();
    }

    public function test_english_taught_keeps_fully_english_and_bilingual_apart_and_skips_language_conflicts(): void
    {
        $counts = fn () => $this->get('/tr/english-taught')->assertOk()->viewData('counts');
        $before = $counts();
        $uni = $this->uni();
        $this->program($uni, 'b3-en-master', 'master', 'en');
        $this->program($uni, 'b3-both-master', 'master', 'both');
        $conflict = $this->program($uni, 'b3-conflict-master', 'master', 'en');
        ProgramVerification::create(['program_id' => $conflict->id, 'field' => 'language', 'status' => ProgramVerification::CONFLICT, 'source_value' => 'Deutsch']);
        $after = $counts();

        $this->assertSame(($before['master.en'] ?? 0) + 1, $after['master.en'], 'çelişkili dil kaydı sayılmaz');
        $this->assertSame(($before['master.both'] ?? 0) + 1, $after['master.both'], 'iki dilli kayıt ayrı sayılır');

        $list = $this->get('/tr/programs?language=en&degree=master&q=B3')->assertOk()->getContent();
        $this->assertStringContainsString('b3-en-master', $list);
        $this->assertStringNotContainsString('b3-conflict-master', $list, 'dil filtresi çelişkili kaydı listelemez');
        $this->assertStringNotContainsString('b3-both-master', $list, 'tamamen İngilizce filtresine iki dilli kayıt girmez');
    }

    public static function locales(): array
    {
        return [['tr'], ['en'], ['de']];
    }

    #[DataProvider('locales')]
    public function test_english_taught_page_has_no_universal_claims(string $locale): void
    {
        $html = $this->get("/{$locale}/english-taught")->assertOk()->getContent();

        foreach (['degree=bachelor&amp;language=en', 'degree=bachelor&amp;language=both', 'degree=master&amp;language=en', 'degree=master&amp;language=both'] as $q) {
            $this->assertStringContainsString($q, $html);
        }
        $this->assertStringContainsString("/{$locale}/universities/collections/english-taught-universities", $html);
        $this->assertStringNotContainsString('IELTS/TOEFL) and no German', $html);
        $this->assertStringNotContainsString('Universities with the most English-taught programs', $html);
        $this->assertSame(1, preg_match_all('/"@type":\s*"FAQPage"/', $html));
    }

    #[DataProvider('locales')]
    public function test_collection_shows_checked_examples_only_for_listed_universities(string $locale): void
    {
        $c = UniversityCollections::find('english-taught-universities');
        $this->assertSame(array_keys($c['examples']), $c['uni_slugs'], 'koleksiyon yalnız örneği olan kurumları listeler');
        $this->assertSame(10, collect($c['examples'])->flatten(1)->count());
        foreach (collect($c['examples'])->flatten(1) as $p) {
            $this->assertStringStartsWith('https://', $p['url']);
            $this->assertSame('2026-10-10', $p['checked']);
            $this->assertContains($p['teaching'], ['english', 'bilingual']);
        }

        foreach ($c['uni_slugs'] as $i => $slug) {
            $this->uni($slug);
        }
        $html = $this->get("/{$locale}/universities/collections/english-taught-universities")->assertOk()->getContent();
        $this->assertStringContainsString('Sustainable Engineering of Products and Processes', $html);
        $this->assertStringContainsString('https://www.tum.de/en/studies/degree-programs/detail/aerospace-bachelor-of-science-bsc', $html);
        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $m);
        $itemList = collect($m[1])->map(fn ($j) => json_decode($j, true))->firstWhere('@type', 'ItemList');
        $this->assertCount(8, $itemList['itemListElement'], 'ItemList yalnız örneği olan 8 kurum');
        $this->assertStringNotContainsString('gisma-university-of-applied-sciences-hs518', $html);
    }

    #[DataProvider('locales')]
    public function test_language_certificate_tool_lists_goethe_c1_as_university_decision(string $locale): void
    {
        $html = $this->get("/{$locale}/tools/language-certificates")->assertOk()->getContent();

        $this->assertSame(7, substr_count($html, '<tr class="hover:bg-gray-50">'));
        $this->assertStringContainsString('Goethe-Zertifikat C1', $html);
        $this->assertStringNotContainsString('~€195', $html);
    }

    public static function reportBoxes(): array
    {
        // [locale, master sayısı, lisans sayısı, dönem, rapor adı, katalog sayısı kalıbı (kutuda olmamalı, kartlarda olmalı)]
        return [
            'tr' => ['tr', '1.928', '424', 'Temmuz 2025', 'ApplyToGerman Almanya Öğrenci İstatistikleri Raporu 2026', '/Katalogumuzda [\d.]+ program/u'],
            'en' => ['en', '1,928', '424', 'July 2025', 'ApplyToGerman Germany Student Statistics Report 2026', '/[\d,]+ programmes in our catalogue/u'],
            'de' => ['de', '1.928', '424', 'Juli 2025', 'ApplyToGerman Bericht zur Studierendenstatistik Deutschland 2026', '/[\d.]+ Studiengänge in unserem Katalog/u'],
        ];
    }

    #[DataProvider('reportBoxes')]
    public function test_english_taught_report_box_is_separate_from_catalogue_counts(string $locale, string $master, string $bachelor, string $period, string $report, string $catalogue): void
    {
        $html = $this->get("/{$locale}/english-taught")->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'data-report-box="english-programmes"'));
        preg_match('/<section data-report-box="english-programmes".*?<\/section>/s', $html, $m);
        $box = html_entity_decode($m[0], ENT_QUOTES);
        foreach ([$master, $bachelor, $period, $report, 'Wissenschaft weltoffen kompakt 2026', '36–37'] as $needle) {
            $this->assertStringContainsString($needle, $box, $locale);
        }
        $this->assertDoesNotMatchRegularExpression($catalogue, strip_tags($box), 'rapor kutusu katalog sayısı gibi sunulmaz');
        $this->assertMatchesRegularExpression($catalogue, html_entity_decode(strip_tags($html), ENT_QUOTES), 'katalog sayıları ayrı kartlarda');
    }

    public function test_english_master_guides_carry_the_master_share_box_once(): void
    {
        $this->seedPosts();
        $this->run500();

        foreach (['tr' => '%46,7', 'en' => '46.7%', 'de' => '46,7 %'] as $locale => $share) {
            $p = Post::where('locale', $locale)->where('slug', 'like', 'english-masters-in-germany-without-german-knowledge%')->firstOrFail();
            $text = html_entity_decode(strip_tags($p->content_html), ENT_QUOTES);
            $this->assertSame(1, substr_count($text, $share), $locale);
            $this->assertSame(1, substr_count($text, 'Wissenschaft weltoffen kompakt 2026'), $locale);
            $this->assertStringContainsString('2024/25', $text);
            $this->assertStringContainsString('ApplyToGerman', $text);
        }
        $guide = Post::where('locale', 'tr')->where('slug', 'like', 'goethe-telc-testdaf-dsh-%')->firstOrFail();
        $this->assertStringNotContainsString('%46,7', $guide->content_md, 'kutu yalnız İngilizce master rehberinde');
    }

    public function test_sitemap_lists_english_taught_collection_once(): void
    {
        $xml = $this->get('/sitemap-tr.xml')->assertOk()->getContent();

        $this->assertSame(1, substr_count($xml, '/tr/universities/collections/english-taught-universities<'));
        $this->assertStringNotContainsString('/tr/universities/collections/top-public-universities<', $xml);
    }
}

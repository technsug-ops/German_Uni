<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Support\StudentStatisticsReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

/**
 * Almanya Öğrenci İstatistikleri Raporu 2026 (Şule Demirel) — TR/EN/DE web yayını.
 * Taslak migration'ı (000200), yayın migration'ı (000300), admin/editör önizlemesi, Batch 3 atıflarının bağlanması,
 * dipnot/tablo/grafik/PDF çıktıları ve üç dil arasında sayı eşitliği.
 */
class StudentStatisticsReport2026Test extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_10_11_000200_add_student_statistics_report_2026_drafts.php';

    private const PUBLISH = 'database/migrations/2026_10_11_000300_publish_student_statistics_report_2026.php';

    private const PDF = '/reports/almanya-ogrenci-istatistikleri-2026/ApplyToGerman_Almanya_Ogrenci_Istatistikleri_Raporu_2026_TR.pdf';

    protected function setUp(): void
    {
        parent::setUp();
        StudentStatisticsReport::flush();
    }

    private function def(): array
    {
        return StudentStatisticsReport::definition();
    }

    private function reports(): \Illuminate\Support\Collection
    {
        return Post::where('translation_group_id', StudentStatisticsReport::GROUP)->get()->keyBy('locale');
    }

    private function seedGuides(bool $linked = false): void
    {
        foreach ($this->def()['guide_citations'] as $locale => $c) {
            DB::table('posts')->insert(['locale' => $locale, 'type' => 'blog', 'slug' => $c['slugs'][0], 'title' => 'Rehber ' . $locale,
                'content_md' => "## Bölüm\n\n> **Bağlam:** metin.\n>\n> " . ($linked ? $c['linked'] : $c['plain']) . " Wissenschaft weltoffen kompakt 2026, Figür 11.*\n",
                'content_html' => '<p>eski</p>', 'is_published' => 1, 'published_at' => now()->subDay(), 'created_at' => now()->subDay(), 'updated_at' => now()->subDay()]);
        }
    }

    private function publish(): void
    {
        (require base_path(self::PUBLISH))->up();
        StudentStatisticsReport::flush();
    }

    private function editor(): User
    {
        return User::factory()->create(['is_editor' => true]);
    }

    public static function locales(): array
    {
        return ['tr' => ['tr'], 'en' => ['en'], 'de' => ['de']];
    }

    /* ------------------------------------------------------------ taslak migration'ı */

    public function test_migration_adds_three_drafts_in_one_group_by_sule_demirel_and_rerun_is_a_noop(): void
    {
        $reports = $this->reports();
        $this->assertCount(3, $reports, 'RefreshDatabase migration\'ı çalıştırdı');
        $author = DB::table('users')->where('slug', 'sule-demirel')->first();
        foreach (['tr', 'en', 'de'] as $locale) {
            $p = $reports[$locale];
            $this->assertFalse((bool) $p->is_published, $locale);
            $this->assertNull($p->published_at, $locale);
            $this->assertSame('blog', $p->type);
            $this->assertSame((int) $author->id, (int) $p->user_id);
            $this->assertSame($this->def()['posts'][$locale]['slug'], $p->slug);
            $this->assertSame($this->def()['posts'][$locale]['title'], $p->title);
            $this->assertNotEmpty($p->content_html);
        }

        $before = DB::table('posts')->where('translation_group_id', StudentStatisticsReport::GROUP)->orderBy('id')->get()->toArray();
        (require base_path(self::MIGRATION))->up();
        $this->assertEquals($before, DB::table('posts')->where('translation_group_id', StudentStatisticsReport::GROUP)->orderBy('id')->get()->toArray(), 'ikinci çalıştırma hiçbir şey değiştirmez (updated_at dahil)');
    }

    public function test_migration_refuses_a_slug_owned_by_another_group_and_writes_nothing(): void
    {
        DB::table('posts')->where('translation_group_id', StudentStatisticsReport::GROUP)->delete();
        $slug = $this->def()['posts']['en']['slug'];
        DB::table('posts')->insert(['locale' => 'en', 'type' => 'blog', 'slug' => $slug, 'title' => 'Başka yazı', 'content_md' => 'x', 'content_html' => '<p>x</p>',
            'translation_group_id' => 'ffffffff-0000-0000-0000-000000000000', 'is_published' => 1, 'published_at' => now(), 'created_at' => now(), 'updated_at' => now()]);

        try {
            (require base_path(self::MIGRATION))->up();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('başka bir çeviri grubuna ait', $e->getMessage());
        }
        $this->assertSame(0, Post::where('translation_group_id', StudentStatisticsReport::GROUP)->count(), 'TR/DE de yazılmadı');
        $this->assertSame('Başka yazı', DB::table('posts')->where('slug', $slug)->value('title'));
    }

    /* ------------------------------------------------------------ taslak görünürlüğü */

    #[DataProvider('locales')]
    public function test_draft_is_invisible_to_visitors_lists_sitemap_and_author_page(string $locale): void
    {
        $slug = $this->def()['posts'][$locale]['slug'];
        $this->get("/{$locale}/blog/{$slug}")->assertNotFound();
        $this->assertStringNotContainsString($slug, $this->get("/{$locale}/blog")->assertOk()->getContent());
        $this->assertStringNotContainsString($slug, $this->get("/sitemap-{$locale}.xml")->assertOk()->getContent());
        $this->assertStringNotContainsString($slug, $this->get("/{$locale}/author/sule-demirel")->assertOk()->getContent());
        $this->assertStringNotContainsString($slug, $this->get("/{$locale}/english-taught")->assertOk()->getContent(), 'rapor kutusu taslağa bağlanmaz');
    }

    public function test_editor_and_admin_preview_the_draft_with_noindex_and_no_view_count(): void
    {
        $slug = $this->def()['posts']['tr']['slug'];
        $enSlug = $this->def()['posts']['en']['slug'];

        $this->actingAs(User::factory()->create())->get("/tr/blog/{$slug}")->assertNotFound();

        foreach ([$this->editor(), User::factory()->create(['is_admin' => true])] as $user) {
            $html = $this->actingAs($user)->get("/tr/blog/{$slug}")->assertOk()->getContent();
            $this->assertStringContainsString('data-draft-preview', $html);
            $this->assertStringContainsString('Taslak önizleme', $html);
            $this->assertStringContainsString('<meta name="robots" content="noindex, follow">', $html);
            $this->assertStringContainsString('hreflang="en" href="' . url("/en/blog/{$enSlug}") . '"', $html, 'önizlemede dil kardeşi taslaklara geçiş');
        }
        $this->assertSame(0, (int) DB::table('posts')->where('slug', $slug)->value('view_count'));
        $this->assertStringNotContainsString('data-draft-preview', $this->get('/tr/blog')->getContent());
    }

    /* ------------------------------------------------------------ içerik: tablolar, dipnotlar, grafikler, PDF, sayı eşitliği */

    #[DataProvider('locales')]
    public function test_rendered_content_has_tables_footnotes_charts_and_pdf(string $locale): void
    {
        $html = $this->reports()[$locale]->content_html;

        $this->assertSame(9, preg_match_all('/<div class="table-wrap">\s*<table>/', $html), 'dokuz tablo, kaydırılabilir sarmalayıcıda');
        $this->assertSame(0, substr_count($html, '<h1'), 'H1 yalnız şablondan basılır');
        $this->assertSame(3, preg_match_all('/<img [^>]*src="\/reports\/almanya-ogrenci-istatistikleri-2026\/[a-z-]+\.png"[^>]*alt="[^"]{20,}"/', $html), 'üç grafik, açıklayıcı alt metinle');
        $this->assertStringContainsString('href="' . self::PDF . '"', $html, 'PDF indirme bağlantısı');
        $this->assertSame(1, substr_count($html, '<div class="footnotes" role="doc-endnotes">'));
        $this->assertSame(29, preg_match_all('/<li class="footnote" id="fn-(\d+)" role="doc-endnote">/', $html, $m), '29 kaynak notu');
        $this->assertSame(range(1, 29), array_map('intval', $m[1]), 'sıra korunur');
        $this->assertStringContainsString('href="#fn-1" role="doc-noteref">1</a>', $html);
        $this->assertStringContainsString('href="' . self::PDF . '#page=31"', $html, 'kaynak notu PDF sayfasına gider');
        $this->assertStringContainsString('href="' . self::PDF . '#page=2"', $html);
        foreach (['filecite', 'sandbox:', 'C:\\', 'DATA_URI_REMOVED', 'data:image', 'data:application'] as $leak) {
            $this->assertStringNotContainsString($leak, $html, $leak);
        }
        if ($locale !== 'tr') {
            $this->assertMatchesRegularExpression('/(in Turkish|auf Türkisch)/', $html, 'PDF\'nin Türkçe olduğu belirtilir');
        }
    }

    public function test_all_three_languages_carry_the_same_numbers_sections_and_distinctions(): void
    {
        $reports = $this->reports();
        $numbers = [];
        $h2 = [];
        foreach (['tr', 'en', 'de'] as $locale) {
            $html = $reports[$locale]->content_html;
            $h2[$locale] = preg_match_all('/<h2[ >]/', $html);
            $dom = new \DOMDocument();
            @$dom->loadHTML('<?xml encoding="utf-8"?><div>' . $html . '</div>');
            $tokens = [];
            foreach ($dom->getElementsByTagName('td') as $cell) {
                $text = $cell->textContent;
                // TR "%14,0" · EN "14.0%" · DE "14,0 %" (boşluklu) → hepsi "14.0%"
                preg_match_all('/[+−-]?%?\d[\d.,]*(?:[ \x{a0}]?%)?/u', $text, $n);
                foreach ($n[0] as $t) {
                    $t = str_replace(['−', ' ', "\u{a0}"], ['-', '', ''], $t);
                    $pct = str_contains($t, '%');
                    $t = str_replace('%', '', $t);
                    $t = $locale === 'en' ? str_replace(',', '', $t) : str_replace(',', '.', str_replace('.', '', $t));
                    $tokens[] = $t . ($pct ? '%' : '');
                }
            }
            sort($tokens);
            $numbers[$locale] = $tokens;
            foreach (['Bildungsausländer', 'Bildungsinländer', 'HZB', 'Hochschulsemester', 'Fachsemester', '402.083|402,083', '24.171|24,171', '2024/25', '2025/26', '2023/24', 'Destatis', 'UNESCO', 'BAMF'] as $must) {
                $this->assertMatchesRegularExpression('~(' . $must . ')~', $html, "{$locale}: {$must}");
            }
        }
        $this->assertSame(13, $h2['tr']);
        $this->assertSame($h2['tr'], $h2['en']);
        $this->assertSame($h2['tr'], $h2['de']);
        $this->assertSame(87, count($numbers['tr']), 'dokuz tablodaki sayısal hücre değerleri');
        $this->assertSame($numbers['tr'], $numbers['en'], 'EN tablo sayıları TR ile aynı');
        $this->assertSame($numbers['tr'], $numbers['de'], 'DE tablo sayıları TR ile aynı');
    }

    public function test_media_files_are_real_and_pdf_is_the_original(): void
    {
        $def = $this->def();
        $this->assertFileExists(base_path($def['pdf']));
        $this->assertSame($def['pdf_md5'], md5_file(base_path($def['pdf'])), 'özgün rapor içerik bakımından değişmedi');
        foreach ($def['images'] as $img) {
            $this->assertFileExists(base_path($img));
            $this->assertGreaterThan(50_000, filesize(base_path($img)));
        }
        $this->assertSame(1, substr_count($this->reports()['tr']->content_md, self::PDF . ')'), 'tek PDF, tek indirme bağlantısı');
    }

    /* ------------------------------------------------------------ yayın adımı */

    public function test_publish_step_publishes_all_three_links_guides_and_is_idempotent(): void
    {
        $this->seedGuides();
        $this->publish();

        $reports = $this->reports();
        $dates = $reports->map(fn ($p) => $p->published_at?->toIso8601String())->unique();
        $this->assertCount(1, $dates, 'üç dil aynı anda, gerçek web yayın tarihiyle');
        $this->assertNotNull($dates->first());
        $this->assertTrue($reports->every(fn ($p) => (bool) $p->is_published));
        $this->assertGreaterThan(now()->subMinute(), $reports['tr']->published_at);

        foreach ($this->def()['guide_citations'] as $locale => $c) {
            $md = (string) Post::where('locale', $locale)->where('slug', $c['slugs'][0])->value('content_md');
            $this->assertSame(1, substr_count($md, $c['linked']), $locale);
            $this->assertSame(0, substr_count($md, $c['plain']), $locale);
            $this->assertStringContainsString('Wissenschaft weltoffen kompakt 2026, Figür 11.', $md, 'atıf cümlesinin kalanı değişmez');
        }

        $snapshot = DB::table('posts')->orderBy('id')->get(['id', 'content_md', 'is_published', 'published_at', 'updated_at'])->toArray();
        $this->publish();
        $this->assertEquals($snapshot, DB::table('posts')->orderBy('id')->get(['id', 'content_md', 'is_published', 'published_at', 'updated_at'])->toArray(), 'ikinci çalıştırma no-op');
    }

    public function test_publish_step_rolls_back_when_a_guide_citation_is_missing(): void
    {
        $this->seedGuides();
        DB::table('posts')->where('slug', $this->def()['guide_citations']['de']['slugs'][0])->update(['content_md' => '## Başka metin']);

        try {
            $this->publish();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('rehber de', $e->getMessage());
        }
        $this->assertTrue($this->reports()->every(fn ($p) => ! $p->is_published && $p->published_at === null), 'rapor taslak kaldı');
        $tr = $this->def()['guide_citations']['tr'];
        $this->assertSame(1, substr_count((string) Post::where('slug', $tr['slugs'][0])->value('content_md'), $tr['plain']), 'TR rehber değişmedi');
    }

    public function test_publish_step_refuses_a_report_whose_author_is_not_sule_demirel(): void
    {
        $this->seedGuides();
        $other = User::factory()->create(['is_author' => true]);
        DB::table('posts')->where('translation_group_id', StudentStatisticsReport::GROUP)->where('locale', 'de')->update(['user_id' => $other->id]);

        try {
            $this->publish();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('yazar sule-demirel değil', $e->getMessage());
        }
        $this->assertTrue($this->reports()->every(fn ($p) => ! $p->is_published), 'hiçbir dil yayımlanmadı');
    }

    public function test_publish_step_without_guides_is_a_noop_only_in_testing(): void
    {
        $this->publish();
        $this->assertTrue($this->reports()->every(fn ($p) => ! $p->is_published), 'hedef rehber yokken (CI) yayımlanmaz');
    }

    #[DataProvider('locales')]
    public function test_published_pages_answer_200_with_hreflang_schema_byline_and_links(string $locale): void
    {
        $this->seedGuides();
        $this->publish();
        $def = $this->def();
        $slug = $def['posts'][$locale]['slug'];

        $html = $this->get("/{$locale}/blog/{$slug}")->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, '<h1'), 'tek H1');
        $this->assertStringContainsString('<h1 class="text-3xl md:text-5xl font-bold leading-tight mb-4">' . e($def['posts'][$locale]['title']) . '</h1>', $html);
        $this->assertStringContainsString('<title>' . e($def['posts'][$locale]['meta_title']) . '</title>', $html);
        $this->assertStringContainsString('<meta name="robots" content="index, follow', $html);
        $this->assertStringNotContainsString('data-draft-preview', $html);
        foreach (['tr', 'en', 'de'] as $alt) {
            $this->assertStringContainsString('hreflang="' . $alt . '" href="' . url("/{$alt}/blog/" . $def['posts'][$alt]['slug']) . '"', $html, "hreflang {$alt}");
        }
        $this->assertStringNotContainsString('Şule Demirel', $this->reports()[$locale]->content_html, 'yazar satırı gövdede tekrar basılmaz (byline şablondan)');
        $this->assertStringContainsString('Şule Demirel', $html);
        $this->assertStringContainsString('ApplyToGerman', $this->reports()[$locale]->content_md, 'ekibin rapor hazırlama katkısı metinde');
        $this->assertStringContainsString('href="' . route('author.show', 'sule-demirel') . '"', $html);
        $this->assertStringContainsString('id="post-toc-ssr"', $html, 'içindekiler');
        $this->assertStringContainsString('href="#fn-1"', $html);
        $this->assertStringContainsString('href="' . self::PDF . '"', $html);
        $this->assertStringContainsString('/' . $locale . '/english-taught', $html, 'İngilizce program listesine bağlantı');
        $this->assertStringContainsString('/' . $locale . '/blog/' . $def['guide_citations'][$locale]['slugs'][0], $html, 'İngilizce master rehberine bağlantı');

        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $m);
        $schema = collect($m[1])->map(fn ($j) => json_decode($j, true))->firstWhere('@type', 'BlogPosting');
        $this->assertSame('Şule Demirel', $schema['author']['name']);
        $this->assertSame('Person', $schema['author']['@type']);
        $this->assertSame(route('author.show', 'sule-demirel'), $schema['author']['url']);
        $this->assertSame(['https://www.linkedin.com/in/%C5%9Fule-demirel-415a66116/'], $schema['author']['sameAs']);
        $this->assertSame('Organization', $schema['publisher']['@type']);
        $this->assertSame(brand('name'), $schema['publisher']['name']);
        $this->assertSame($locale, $schema['inLanguage']);
        $this->assertNotNull($schema['datePublished']);
        $this->assertStringStartsWith(now()->format('Y-m-d'), $schema['datePublished'], 'datePublished = gerçek web yayın tarihi (Eylül 2026 değil)');

        $this->assertStringContainsString($slug, $this->get("/sitemap-{$locale}.xml")->assertOk()->getContent(), 'yayın sonrası sitemap\'te');
        $this->assertStringContainsString($slug, $this->get("/{$locale}/author/sule-demirel")->assertOk()->getContent());
    }

    #[DataProvider('locales')]
    public function test_english_taught_report_box_links_the_report_only_after_publication(string $locale): void
    {
        $name = ['tr' => 'ApplyToGerman Almanya Öğrenci İstatistikleri Raporu 2026', 'en' => 'ApplyToGerman Germany Student Statistics Report 2026', 'de' => 'ApplyToGerman Bericht zur Studierendenstatistik Deutschland 2026'][$locale];
        $box = fn () => preg_replace('/.*(<section data-report-box="english-programmes".*?<\/section>).*/s', '$1', $this->get("/{$locale}/english-taught")->assertOk()->getContent());

        $before = $box();
        $this->assertStringContainsString($name, html_entity_decode($before, ENT_QUOTES));
        $this->assertStringNotContainsString('<a ', $before, 'taslakken bağlantı yok');

        $this->seedGuides();
        $this->publish();

        $after = html_entity_decode($box(), ENT_QUOTES);
        $this->assertStringContainsString('<a href="' . url("/{$locale}/blog/" . $this->def()['posts'][$locale]['slug']) . '"', $after);
        $this->assertSame(1, substr_count($after, $name));
        $this->assertStringContainsString('1' . ($locale === 'en' ? ',' : '.') . '928', $after, 'kutunun sayıları değişmez');
    }
}

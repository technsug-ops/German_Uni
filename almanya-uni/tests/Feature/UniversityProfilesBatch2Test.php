<?php

namespace Tests\Feature;

use App\Models\University;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

/**
 * SEO Growth Batch 2 (2026-10-10): TU Berlin, TUM, Heidelberg, Freiburg, TU Darmstadt profilleri + yüksek lisans ve
 * ücret rehberi bağlantıları. Batch 1'den farklı olarak beklenmeyen durum sessizce atlanmaz: migration hata verir ve
 * hiçbir kayda yazmaz (boş veritabanı yalnız testing ortamında no-op). Bu testler guard, atomiklik, no-op, canlıdaki
 * gerçek durumu (TU Berlin TR'de blok yok) ve ücret rehberinde çelişen genelleme kalmamasını sabitler.
 */
class UniversityProfilesBatch2Test extends TestCase
{
    use RefreshDatabase;

    private const PROFILES = 'database/migrations/2026_10_10_000300_apply_batch2_university_profiles.php';

    private const GUIDES = 'database/migrations/2026_10_10_000400_link_master_and_fee_guides_to_batch2_profiles.php';

    private function data(): array
    {
        return json_decode(file_get_contents(base_path('database/data/university-content/university-profiles-batch2-2026-10-10.json')), true);
    }

    private function blocksFor(?array $types): ?string
    {
        if ($types === null) {
            return null;
        }

        return json_encode(array_map(fn ($type) => match ($type) {
            'hero' => ['type' => 'hero', 'image_url' => 'https://example.org/hero.jpg', 'alt' => 'Kampüs'],
            'intro' => ['type' => 'intro', 'body_md' => 'Eski giriş.'],
            'quick_facts' => ['type' => 'quick_facts', 'h' => 'Hızlı Bakış', 'items' => [['label' => 'Şehir', 'value' => 'X'], ['label' => 'Program Sayısı', 'value' => '261'], ['label' => 'Uni-Assist Üyesi', 'value' => 'Hayır']]],
            'section' => ['type' => 'section', 'h' => 'Eski bölüm', 'body_md' => 'Genel bilgi. Bu üniversite genellikle şartlı kabul imkânı sunmaz. Son cümle.'],
            'faq' => ['type' => 'faq', 'h' => 'SSS', 'items' => [['q' => 'Eski?', 'a' => 'Eski.']]],
            'cta' => ['type' => 'cta', 'body_md' => 'Eski CTA'],
            'external_links' => ['type' => 'external_links', 'items' => []],
            'schema_jsonld' => ['type' => 'schema_jsonld', 'data' => ['@type' => 'CollegeOrUniversity', 'description' => 'eski']],
            default => ['type' => $type],
        }, $types), JSON_UNESCAPED_UNICODE);
    }

    /** Canlıdaki doğrulanmış başlangıç durumunu kurar; $override ile tek kurumun bir dil kolonunu bozabiliriz. */
    private function seedAll(array $override = []): void
    {
        foreach ($this->data()['universities'] as $slug => $spec) {
            $row = ['name_de' => $slug, 'name_tr' => $slug, 'slug' => $slug, 'is_active' => 1, 'type' => 'public', 'created_at' => now()->subDay(), 'updated_at' => now()->subDay()];
            foreach (['tr' => 'content_blocks', 'en' => 'content_blocks_en', 'de' => 'content_blocks_de'] as $loc => $col) {
                $row[$col] = $override[$slug][$col] ?? $this->blocksFor($spec['expected'][$loc]);
            }
            DB::table('universities')->insert($row);
        }
    }

    private function runProfiles(): void
    {
        (require base_path(self::PROFILES))->up();
    }

    public function test_applies_all_five_profiles_and_builds_missing_tr_blocks(): void
    {
        $this->seedAll();
        $this->runProfiles();

        foreach ($this->data()['universities'] as $slug => $spec) {
            $u = University::where('slug', $slug)->first();
            $this->assertTrue($u->hasEditorialLock(), $slug);
            foreach (['tr' => 'content_blocks', 'en' => 'content_blocks_en', 'de' => 'content_blocks_de'] as $loc => $col) {
                $blocks = collect($u->{$col});
                $this->assertSame($spec['locales'][$loc]['seo_title'], $blocks->firstWhere('type', 'intro')['seo_title']);
                $this->assertCount(4, $blocks->firstWhere('type', 'faq')['items']);
                $this->assertFalse($blocks->contains(fn ($b) => $b['type'] === 'programs_summary'));
                $this->assertStringNotContainsString('şartlı kabul imkânı sunmaz', json_encode($u->{$col}, JSON_UNESCAPED_UNICODE));
            }
        }

        // Canlıda TR bloğu olmayan TU Berlin: temiz yapı (giriş, hızlı bilgi, doğrulanmış bölümler, SSS, CTA, resmî link).
        $tub = University::where('slug', 'technische-universitat-berlin')->first();
        $types = collect($tub->content_blocks)->pluck('type')->all();
        $this->assertSame('intro', $types[0]);
        $this->assertSame(['faq', 'cta', 'external_links'], array_slice($types, -3));
        $this->assertSame($this->data()['universities']['technische-universitat-berlin']['official']['url'], collect($tub->content_blocks)->last()['items'][0]['url']);
    }

    public function test_second_run_changes_nothing_including_updated_at(): void
    {
        $this->seedAll();
        $this->runProfiles();
        $before = DB::table('universities')->orderBy('id')->get(['content_blocks', 'content_blocks_en', 'content_blocks_de', 'updated_at'])->toArray();

        $this->runProfiles();

        $this->assertEquals($before, DB::table('universities')->orderBy('id')->get(['content_blocks', 'content_blocks_en', 'content_blocks_de', 'updated_at'])->toArray());
    }

    public function test_unexpected_structure_fails_loudly_and_writes_nothing(): void
    {
        $this->seedAll(['universitat-heidelberg-partner-019ddbba' => ['content_blocks_de' => json_encode([['type' => 'intro', 'body_md' => 'x']])]]);
        $before = DB::table('universities')->orderBy('id')->pluck('content_blocks', 'slug')->all();

        try {
            $this->runProfiles();
            $this->fail('Beklenmeyen yapı sessizce atlanmamalı');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('universitat-heidelberg-partner-019ddbba content_blocks_de', $e->getMessage());
        }

        $this->assertSame($before, DB::table('universities')->orderBy('id')->pluck('content_blocks', 'slug')->all(), 'hiçbir kuruma yazılmamalı');
    }

    public function test_partially_applied_profile_fails_loudly(): void
    {
        $this->seedAll(['technische-universitat-darmstadt-q310695' => ['content_blocks_en' => json_encode([['type' => 'intro', 'editorial_lock' => true]])]]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('kısmen uygulanmış');
        $this->runProfiles();
    }

    public static function locales(): array
    {
        return [['tr'], ['en'], ['de']];
    }

    #[DataProvider('locales')]
    public function test_locked_tum_page_shows_verified_title_and_fee_facts(string $locale): void
    {
        $this->seedAll();
        $this->runProfiles();
        $spec = $this->data()['universities']['technische-universitat-munchen-partner-019ddbba']['locales'][$locale];

        $html = $this->get("/{$locale}/universities/technische-universitat-munchen-partner-019ddbba")->assertOk()->getContent();

        $this->assertStringContainsString('<title>' . e($spec['seo_title']) . ' — ', $html);
        $this->assertStringContainsString('content="' . e($spec['seo_description']) . '"', $html);
        $this->assertStringContainsString('TUMonline', $html);
    }

    /** Ücret rehberi düzeltme kuralları (migration'daki FIXES): locale → alan → [[eski varyantlar], yeni]. */
    private function fixes(): array
    {
        return (new \ReflectionClassConstant(require base_path(self::GUIDES), 'FIXES'))->getValue();
    }

    /** $variant: 0 = lokal kopyadaki eski metin, 1 = canlıdaki farklı metin (varsa). */
    private function seedGuides(bool $withAnchors = true, int $variant = 0): void
    {
        $guides = [
            ['tr', 'doing-a-masters-in-germany-2026-a-z-guide', '5. Önemli Deadline\'lar', '6. En İyi Master Programları'],
            ['en', 'doing-a-masters-in-germany-2026-a-z-guide-en', '5. Important Deadlines', '6. Best Master\'s Programs'],
            ['de', 'doing-a-masters-in-germany-2026-a-z-guide-de', '5. Wichtige Fristen', '6. Beste Masterprogramme'],
            ['tr', 'is-university-free-in-germany-2026-real-costs', '1. Devlet üniversitelerinde öğrenim ücreti', '2. Semesterbeitrag'],
            ['en', 'is-university-free-in-germany-2026-real-costs-en', 'Is university education in Germany really free?', null],
            ['de', 'is-university-free-in-germany-2026-real-costs-de', '1. Studiengebühren', '2. Semesterbeitrag'],
        ];
        $fixes = $this->fixes();
        foreach ($guides as [$locale, $slug, $h1, $h2]) {
            $h2 = $withAnchors ? $h2 : null;
            $old = fn (string $field) => array_map(fn ($rule) => $rule[0][$variant] ?? $rule[0][0], $fixes[$locale][$field] ?? []);
            $isFees = str_starts_with($slug, 'is-university-free');
            $body = $isFees && $old('content_md') ? implode("\n\n", $old('content_md')) : 'Metin.';
            $md = "Giriş.\n\n## {$h1}\n\n{$body}" . ($h2 ? "\n\n## {$h2}\n\nMetin." : '');
            DB::table('posts')->updateOrInsert(['locale' => $locale, 'slug' => $slug], [
                'title' => $slug, 'content_md' => $md, 'content_html' => '<p>eski</p>',
                'excerpt' => $isFees ? implode(' ', $old('excerpt')) . ' Bütçe tablosu.' : null,
                'meta_description' => $isFees ? implode(' ', $old('meta_description')) . ' Yıllık bütçe.' : null,
                'is_published' => 1, 'published_at' => now()->subDay(), 'created_at' => now()->subDay(), 'updated_at' => now()->subDay(),
            ]);
        }
    }

    private function runGuides(): void
    {
        (require base_path(self::GUIDES))->up();
    }

    private function guideRows(): array
    {
        return DB::table('posts')->orderBy('id')->get(['slug', 'content_md', 'content_html', 'excerpt', 'meta_description', 'updated_at'])->toArray();
    }

    public function test_guide_paragraphs_are_inserted_once_before_anchor_or_appended(): void
    {
        $this->seedGuides();

        $this->runGuides();
        $before = $this->guideRows();
        $this->runGuides();
        $this->assertEquals($before, $this->guideRows(), 'ikinci çalıştırma no-op (updated_at dahil)');

        $tum = 'technische-universitat-munchen-partner-019ddbba';
        $master = DB::table('posts')->where('slug', 'doing-a-masters-in-germany-2026-a-z-guide')->first();
        $this->assertSame(1, substr_count($master->content_html, "/tr/universities/{$tum}"), 'content_html md\'den yeniden üretilir');
        $this->assertLessThan(strpos($master->content_html, '6. En İyi'), strpos($master->content_html, "/tr/universities/{$tum}"));
        $this->assertGreaterThan(strpos($master->content_html, '5. Önemli'), strpos($master->content_html, "/tr/universities/{$tum}"));

        $enFees = DB::table('posts')->where('slug', 'is-university-free-in-germany-2026-real-costs-en')->first();
        $this->assertStringEndsWith('</p>', trim($enFees->content_html), 'tek H2\'li EN ücret rehberinde paragraf sona eklenir');
        $this->assertSame(1, substr_count($enFees->content_md, "/en/universities/{$tum}"));
    }

    public static function feeVariants(): array
    {
        return ['lokal metin' => [0], 'canlı metin' => [1]];
    }

    #[DataProvider('feeVariants')]
    public function test_fee_guide_keeps_no_contradicting_generalisation(int $variant): void
    {
        $this->seedGuides(variant: $variant);
        $this->runGuides();

        foreach (['tr' => '', 'en' => '-en', 'de' => '-de'] as $locale => $suffix) {
            $p = DB::table('posts')->where('slug', 'is-university-free-in-germany-2026-real-costs' . $suffix)->first();
            $all = $p->content_md . $p->content_html . $p->excerpt . $p->meta_description;
            $this->assertDoesNotMatchRegularExpression('/\b14\s+(eyalet|Bundesl)/u', $all, $locale);
            // TUM: yalnız bugünkü tutarlar; 2027/28 artışı rehberde bugünkü ücret gibi geçmez.
            $this->assertDoesNotMatchRegularExpression('/2027\/28|2\.300|2,300|4\.600|4,600|6\.800|6,800/u', $all, $locale);
            $this->assertStringContainsString('TUM', $p->excerpt, $locale);
        }

        $tr = DB::table('posts')->where('slug', 'is-university-free-in-germany-2026-real-costs')->first();
        $this->assertStringContainsString('<strong>genel bir eyalet öğrenim ücreti yok.</strong>', $tr->content_html);
        // TU Berlin özeti mutlak değil; öğrenim ücreti Semesterbeitrag'dan ayrı.
        $this->assertStringContainsString('sürekli eğitim (weiterbildende) yüksek lisansları dışında öğrenim ücreti almıyor', $tr->content_html);
        $this->assertStringContainsString('Semesterbeitrag) öğrenim ücretinden ayrıdır', $tr->content_html);
        $this->assertStringNotContainsString('tam ücretsiz', $tr->content_md);
        $de = DB::table('posts')->where('slug', 'is-university-free-in-germany-2026-real-costs-de')->first();
        $this->assertStringContainsString('außer für weiterbildende Master keine Studiengebühren', $de->content_html);
        $this->assertStringNotContainsString('kostenlos an staatlichen Universitäten', $de->content_md);
    }

    public function test_missing_old_fee_sentence_fails_and_writes_nothing(): void
    {
        $this->seedGuides();
        DB::table('posts')->where('slug', 'is-university-free-in-germany-2026-real-costs-de')
            ->update(['content_md' => "Giriş.\n\n## 1. Studiengebühren\n\nGanz anderer Text.\n\n## 2. Semesterbeitrag\n\nMetin."]);
        $before = $this->guideRows();

        try {
            $this->runGuides();
            $this->fail('Düzeltilecek cümle yoksa sessizce atlanmamalı');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('de/is-university-free-in-germany-2026-real-costs-de content_md düzeltme 1', $e->getMessage());
        }

        $this->assertEquals($before, $this->guideRows(), 'hiçbir rehbere yazılmamalı');
    }

    public function test_empty_database_is_a_noop_only_in_testing_environment(): void
    {
        // Test veritabanında hedef profiller ve altı rehber yok (diğer seed yazıları olabilir).
        $this->runProfiles();
        $this->runGuides();
        $this->assertSame(0, DB::table('posts')->where('slug', 'like', 'is-university-free-in-germany-2026%')->orWhere('slug', 'like', 'doing-a-masters-in-germany-2026%')->count());

        $env = $this->app['env'];
        $this->app['env'] = 'production';
        try {
            foreach ([[fn () => $this->runProfiles(), 'kayıt yok'], [fn () => $this->runGuides(), 'bulunamadı']] as [$run, $message]) {
                try {
                    $run();
                    $this->fail('Production\'da hedef kayıtların olmaması sessizce geçmemeli');
                } catch (RuntimeException $e) {
                    $this->assertStringContainsString($message, $e->getMessage());
                }
            }
        } finally {
            $this->app['env'] = $env;
        }
        $this->assertSame(0, DB::table('universities')->whereIn('slug', array_keys($this->data()['universities']))->count(), 'hiçbir şey yazılmadı');
    }

    public function test_guide_without_required_anchor_fails_and_writes_nothing(): void
    {
        $this->seedGuides(withAnchors: false);
        $before = DB::table('posts')->orderBy('id')->pluck('content_md', 'slug')->all();

        try {
            $this->runGuides();
            $this->fail('Çapa yoksa sessizce atlanmamalı');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('çapa bulunamadı', $e->getMessage());
        }

        $this->assertSame($before, DB::table('posts')->orderBy('id')->pluck('content_md', 'slug')->all());
    }
}

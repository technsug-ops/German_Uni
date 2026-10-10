<?php

namespace Tests\Feature;

use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

/**
 * Dil SSS uyum düzeltmesi (2026-10-10): iki SSS kümesinden (TR/EN/DE) kaynaksız kabul oranları ve mutlak kabul
 * ifadeleri kalkar; cevaplar Batch 3 dil rehberiyle tutarlıdır. Migration güvenliği (no-op, beklenmeyen durum,
 * rollback, çeviri grubu) ve sayfa çıktısı (görünür cevap, QAPage şeması, rehber linki) sabitlenir.
 */
class LanguageFaqAcceptanceRatesTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_10_10_000600_fix_language_faq_acceptance_rates.php';

    private const OLD = [
        'tr' => "**Kabul**\n\n| Sertifika | Uni kabul oranı |\n| --- | --- |\n| **Goethe C1** | %95+ |\n| **TestDaF** | %100 |\n\n> TestDaF al, her yere geçer.",
        'en' => "**Acceptability**\n\n| Certificate | Uni acceptance rate |\n| --- | --- |\n| **Goethe C1** | 95%+ |\n| **TestDaF** | 100% |",
        'de' => "**Akzeptanz**\n\n| Zertifikat | Akzeptanzrate |\n| --- | --- |\n| **Goethe C1** | 95 %+ |\n| **TestDaF** | 100 % |",
    ];

    private function targets(): array
    {
        return (new \ReflectionClassConstant(require base_path(self::MIGRATION), 'TARGETS'))->getValue();
    }

    private function run600(): void
    {
        (require base_path(self::MIGRATION))->up();
    }

    private function seedFaqs(array $override = []): void
    {
        $topic = DB::table('faq_topics')->where('slug', 'dil')->value('id')
            ?? DB::table('faq_topics')->insertGetId(['name' => 'Dil', 'name_tr' => 'Dil', 'name_en' => 'Language', 'name_de' => 'Sprache', 'slug' => 'dil', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        foreach ($this->targets() as $cluster => $locales) {
            $group = (string) Str::uuid();
            foreach ($locales as $locale => [$slug, $question]) {
                DB::table('faqs')->insert(array_merge([
                    'locale' => $locale, 'translation_group_id' => $group, 'faq_topic_id' => $topic, 'question' => $question, 'slug' => $slug,
                    'answer_md' => self::OLD[$locale], 'answer_html' => null, 'has_answer' => 1, 'is_published' => 1,
                    'created_at' => now()->subDay(), 'updated_at' => now()->subDay(),
                ], $override[$slug] ?? []));
            }
        }
    }

    private function rows(): array
    {
        return DB::table('faqs')->orderBy('id')->get(['slug', 'question', 'answer_md', 'answer_html', 'translation_group_id', 'is_published', 'updated_at'])->toArray();
    }

    public function test_updates_both_clusters_in_all_languages_and_second_run_is_a_noop(): void
    {
        $this->seedFaqs();
        $this->run600();
        $after = $this->rows();
        $this->run600();
        $this->assertEquals($after, $this->rows(), 'ikinci çalıştırma no-op (updated_at dahil)');

        $forbidden = (new \ReflectionClassConstant(require base_path(self::MIGRATION), 'FORBIDDEN'))->getValue();
        foreach ($this->targets() as $cluster => $locales) {
            foreach ($locales as $locale => [$slug, $question]) {
                $f = Faq::where('slug', $slug)->firstOrFail();
                $this->assertSame($question, $f->question, 'soru değişmez');
                $this->assertTrue((bool) $f->is_published);
                $this->assertDoesNotMatchRegularExpression($forbidden, $f->answer_md . ' ' . $f->answer_html, $slug);
                $this->assertNotEmpty($f->answer_html, 'answer_html md\'den üretilir');
                $this->assertStringContainsString("/{$locale}/blog/goethe-telc-testdaf-dsh-difference-german-language-exam-comparison-for-turkish", $f->answer_html);
                $this->assertStringContainsString('Goethe-Zertifikat C1', $f->answer_md);
                $this->assertStringContainsString('RWTH Aachen', $f->answer_md, 'koşullu açıklama: üniversiteye bağlı');
                $this->assertStringContainsString('telc Deutsch C1 Hochschule', $f->answer_md);
            }
        }
        $this->assertStringContainsString('TDN 4', Faq::where('slug', 'almanca-c1-hangi-sinav-ile-kanitlanir')->first()->answer_md);
        $this->assertStringContainsString('DSH-2', Faq::where('slug', 'almanca-c1-hangi-sinav-ile-kanitlanir-de')->first()->answer_md);
    }

    public function test_unexpected_question_fails_and_writes_nothing(): void
    {
        $this->seedFaqs(['almanca-c1-hangi-sinav-ile-kanitlanir-en' => ['question' => 'Edited question?']]);
        $before = $this->rows();

        try {
            $this->run600();
            $this->fail('Beklenmeyen soru sessizce geçmemeli');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('beklenmeyen soru metni', $e->getMessage());
        }
        $this->assertEquals($before, $this->rows());
    }

    public function test_answer_without_old_fingerprint_fails_and_writes_nothing(): void
    {
        $this->seedFaqs(['goethe-vs-telc-sertifikasi-uni-basvurusu-icin-fark-var-mi-de' => ['answer_md' => 'Ganz anderer Text.']]);
        $before = $this->rows();

        try {
            $this->run600();
            $this->fail('Farklı cevap sessizce ezilmemeli');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('parmak izi yok', $e->getMessage());
        }
        $this->assertEquals($before, $this->rows(), 'hiçbir kayda yazılmamalı');
    }

    public function test_records_from_different_translation_groups_fail(): void
    {
        $this->seedFaqs(['almanca-c1-hangi-sinav-ile-kanitlanir-de' => ['translation_group_id' => (string) Str::uuid()]]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('aynı çeviri grubunda değil');
        $this->run600();
    }

    public function test_empty_database_is_a_noop_only_in_testing_environment(): void
    {
        $this->run600();
        $this->assertSame(0, DB::table('faqs')->where('slug', 'like', 'goethe-vs-telc-sertifikasi%')->count());

        $env = $this->app['env'];
        $this->app['env'] = 'production';
        try {
            $this->run600();
            $this->fail('Production\'da hedef kayıtların olmaması sessizce geçmemeli');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('bulunamadı', $e->getMessage());
        } finally {
            $this->app['env'] = $env;
        }
    }

    public static function pages(): array
    {
        return [
            ['tr', 'goethe-vs-telc-sertifikasi-uni-basvurusu-icin-fark-var-mi'],
            ['en', 'goethe-vs-telc-sertifikasi-uni-basvurusu-icin-fark-var-mi-en'],
            ['de', 'almanca-c1-hangi-sinav-ile-kanitlanir-de'],
        ];
    }

    #[DataProvider('pages')]
    public function test_faq_page_shows_answer_and_matching_schema(string $locale, string $slug): void
    {
        $this->seedFaqs();
        $this->run600();

        $html = $this->get("/{$locale}/faq/dil/{$slug}")->assertOk()->getContent();
        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $m);
        $qa = collect($m[1])->map(fn ($j) => json_decode($j, true))->firstWhere('@type', 'QAPage');
        $answer = $qa['mainEntity']['acceptedAnswer']['text'] ?? '';

        $this->assertNotSame('', trim($answer), 'şemadaki cevap boş değil');
        $this->assertStringContainsString('Goethe-Zertifikat C1', $answer);
        $this->assertDoesNotMatchRegularExpression('/95\s?%|%\s?95/', $html);
        $this->assertStringContainsString('goethe-telc-testdaf-dsh-difference-german-language-exam-comparison-for-turkish', $html);
    }
}

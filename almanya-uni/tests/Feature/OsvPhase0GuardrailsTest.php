<?php

namespace Tests\Feature;

use App\Console\Commands\ChatEval;
use App\Console\Commands\SeedHowtoBriefs;
use App\Console\Commands\TranslatePosts;
use App\Models\ContentAsset;
use App\Models\ContentBrief;
use App\Models\Post;
use App\Services\Content\ContentPrompts;
use App\Services\Content\ContentTranslator;
use App\Services\GeminiTranslator;
use App\Services\Rag\ChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * OSV Faz 0 korkulukları (docs/OSV_PHASE0_GUARDRAILS_RESULT.md):
 *  1. Brief `notes` üretim promptuna girer (G8).
 *  2–4. Çeviri promptları rakam/tutar/tarih/§/URL korur; deterministik kontrol uyuşmayan çeviriyi kaydetmez (G9).
 *  5. x-source-note: kontrol tarihi ≠ kayıt güncelleme tarihi; doğrulama tarihi yoksa "verified" denmez.
 *  7. Chatbot system prompt'u kontrol tarihini yürürlük tarihi gibi vermez; chat:eval OSV dize kontrolleri.
 */
class OsvPhase0GuardrailsTest extends TestCase
{
    use RefreshDatabase;

    private static function gemini(string $text): array
    {
        return ['candidates' => [['content' => ['parts' => [['text' => $text]]]]], 'usageMetadata' => ['promptTokenCount' => 1, 'candidatesTokenCount' => 1]];
    }

    private static function promptOf($request): string
    {
        return (string) ($request->data()['contents'][0]['parts'][0]['text'] ?? '');
    }

    private function remonstrationBrief(): array
    {
        $briefs = (new \ReflectionClass(SeedHowtoBriefs::class))->getConstant('BRIEFS');
        foreach ($briefs as $b) {
            if ($b['slug'] === 'vize-reddi-remonstration-itiraz-2026') {
                return $b;
            }
        }
        $this->fail('remonstration brief tohumu bulunamadı');
    }

    // ── 1. Brief notes ────────────────────────────────────────────────────────────

    public function test_generation_prompt_contains_brief_notes_in_every_language_and_asset(): void
    {
        $def = $this->remonstrationBrief();
        $brief = new ContentBrief(array_intersect_key($def, array_flip((new ContentBrief)->getFillable())) + ['target_word_count' => 1500, 'brand_tone' => 'instructive']);

        foreach (['tr', 'en', 'de'] as $lang) {
            foreach (['blog', 'faq_page', 'newsletter'] as $asset) {
                $prompt = (new ContentPrompts)->build($brief, $asset, $lang);
                $this->assertStringContainsString('EDITORIAL CONSTRAINTS / VERIFIED FACTS — MUST FOLLOW', $prompt, "$lang/$asset");
                $this->assertStringContainsString('Remonstration 01.07.2025 itibarıyla kaldırıldı — itiraz mektubu önerme.', $prompt, "$lang/$asset");
                $this->assertStringContainsString($def['notes'], $prompt);
            }
        }

        $brief->notes = null;
        $this->assertStringNotContainsString('EDITORIAL CONSTRAINTS', (new ContentPrompts)->build($brief, 'blog', 'tr'), 'boş not → blok yok');
        $this->assertSame('', ContentPrompts::editorialNotes('   '));
    }

    // ── 2–4. Çeviri ───────────────────────────────────────────────────────────────

    private function seedFaqPair(string $answerTr): array
    {
        $topic = DB::table('faq_topics')->insertGetId(['name' => 'Vize', 'slug' => 'visa-osv0', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $group = '0e5c0000-0000-4000-8000-0000000000f0';
        $base = ['has_answer' => 1, 'is_published' => 1, 'intent' => 'community', 'translation_group_id' => $group, 'faq_topic_id' => $topic, 'created_at' => now(), 'updated_at' => now()];
        $tr = DB::table('faqs')->insertGetId($base + ['locale' => 'tr', 'slug' => 'ogrenci-calisma-osv0', 'question' => 'Öğrenci yılda kaç gün çalışabilir?', 'answer_md' => $answerTr, 'answer_html' => '<p>x</p>']);
        $en = DB::table('faqs')->insertGetId($base + ['locale' => 'en', 'slug' => 'student-work-osv0', 'question' => 'Old EN question?', 'answer_md' => 'Old EN answer.', 'answer_html' => '<p>x</p>']);

        return [$tr, $en];
    }

    public function test_faq_translate_skips_translation_that_changes_numbers_and_saves_faithful_one(): void
    {
        config(['services.gemini.key' => 'test-key']);
        $src = 'Yılda **140 tam gün** veya 280 yarım gün; haftada 20 saat. Vize harcı 75 € ([kaynak](https://www.make-it-in-germany.com/de/x), kontrol tarihi: 14.10.2026).';
        [, $en] = $this->seedFaqPair($src);

        Http::fake(['generativelanguage.googleapis.com/*' => Http::sequence()
            ->push(self::gemini(json_encode(['0' => [
                'question' => 'How many days can a student work per year?',
                'answer' => 'Students may work **120 full days** or 240 half days; 20 hours a week. Visa fee €75 ([source](https://www.make-it-in-germany.com/de/x), checked 14.10.2026).',
            ]])))
            ->push(self::gemini(json_encode(['0' => [
                'question' => 'How many days can a student work per year?',
                'answer' => 'Students may work **140 full days** or 280 half days; 20 hours a week. Visa fee €75 ([source](https://www.make-it-in-germany.com/de/x), checked 14.10.2026).',
            ]])))]);
        $this->artisan('faq:translate', ['--locale' => 'en', '--force' => true, '--sleep' => 0])
            ->expectsOutputToContain('atlandı')
            ->assertExitCode(0);
        $this->assertSame('Old EN answer.', DB::table('faqs')->where('id', $en)->value('answer_md'), 'rakamı değişen çeviri kaydedilmez');
        Http::assertSent(fn ($r) => str_contains(self::promptOf($r), 'DO NOT translate or change: numeric values') && str_contains(self::promptOf($r), '§ 16b Abs. 3'));

        $this->artisan('faq:translate', ['--locale' => 'en', '--force' => true, '--sleep' => 0])->assertExitCode(0);
        $this->assertStringContainsString('140 full days', DB::table('faqs')->where('id', $en)->value('answer_md'));
    }

    public function test_post_translation_invariant_diff_blocks_changed_body_and_invented_excerpt_figures(): void
    {
        $p = new Post(['title' => 'Blue Card 2026', 'excerpt' => 'Eşik 50.700 €.', 'content_md' => "2026 eşiği 50.700 €, darboğaz 45.934,20 € (§ 18g Abs. 1 AufenthG)."]);
        $good = ['title' => 'Blue Card 2026', 'excerpt' => 'Threshold €50,700.', 'content_md' => '2026 threshold €50,700, shortage €45,934.20 (§ 18g Abs. 1 AufenthG).'];
        $this->assertSame([], TranslatePosts::invariantDiff($p, $good));
        $this->assertSame([], TranslatePosts::invariantDiff($p, ['excerpt' => 'A short summary.'] + $good), 'özet rakam düşürebilir');
        $this->assertArrayHasKey('amounts', TranslatePosts::invariantDiff($p, ['excerpt' => 'Threshold €48,300.'] + $good), 'özette uydurma tutar');
        $this->assertArrayHasKey('amounts', TranslatePosts::invariantDiff($p, ['content_md' => '2026 threshold €48,300, shortage €45,934.20 (§ 18g Abs. 1 AufenthG).'] + $good));
    }

    public function test_translate_posts_command_saves_only_the_faithful_locale(): void
    {
        config(['services.gemini.key' => 'test-key']);
        $id = DB::table('posts')->insertGetId(['locale' => 'tr', 'slug' => 'blue-card-osv0', 'title' => 'Blue Card 2026', 'excerpt' => 'Eşik 50.700 €.',
            'content_md' => '2026 eşiği 50.700 €, darboğaz 45.934,20 €.', 'content_html' => '<p>x</p>', 'is_published' => 1, 'published_at' => now(),
            'translation_group_id' => '0e5c0000-0000-4000-8000-0000000000f1', 'created_at' => now(), 'updated_at' => now()]);
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(self::gemini(json_encode([
            'en' => ['title' => 'Blue Card 2026', 'slug' => 'blue-card-2026-osv0', 'excerpt' => 'Threshold €50,700.', 'content_md' => '2026 threshold €50,700, shortage €45,934.20.'],
            'de' => ['title' => 'Blue Card 2026', 'slug' => 'blue-card-2026-osv0-de', 'excerpt' => 'Schwelle 48.300 €.', 'content_md' => 'Schwelle 2026: 48.300 €, Engpass 43.759,20 €.'],
        ])))]);
        $this->artisan('content:translate-posts', ['--post' => $id, '--sleep' => 0])
            ->expectsOutputToContain('KAYDEDİLMEDİ')
            ->assertExitCode(0);
        $this->assertTrue(Post::where('translation_group_id', '0e5c0000-0000-4000-8000-0000000000f1')->where('locale', 'en')->exists());
        $this->assertFalse(Post::where('translation_group_id', '0e5c0000-0000-4000-8000-0000000000f1')->where('locale', 'de')->exists(), 'DE çevirisi tutar değiştirdi → kaydedilmedi');
        Http::assertSent(fn ($r) => str_contains(self::promptOf($r), 'DO NOT translate or change: numeric values'));
    }

    public function test_content_translator_drops_currency_adaptation_and_refuses_changed_amounts(): void
    {
        config(['services.gemini.key' => 'test-key']);
        $brief = ContentBrief::create(['title' => 'Sperrkonto 2026', 'slug' => 'sperrkonto-osv0', 'audience' => 'genel', 'topic' => 'para', 'primary_keyword' => 'sperrkonto',
            'pain_point' => 'x', 'target_word_count' => 800, 'brand_tone' => 'instructive', 'status' => 'ready']);
        $asset = ContentAsset::create(['content_brief_id' => $brief->id, 'asset_type' => 'blog', 'language' => 'tr', 'body_md' => 'Sperrkonto 2026: 11.904 € (aylık 992 €).', 'status' => 'ready']);

        Http::fake(['generativelanguage.googleapis.com/*' => Http::sequence()
            ->push(self::gemini('Blocked account 2026: about $12,900 (≈ $1,075 per month).'))
            ->push(self::gemini('Blocked account 2026: €11,904 (€992 per month).'))]);
        try {
            (new ContentTranslator('test-key'))->translate($asset, 'en');
            $this->fail('tutar değişen çeviri kaydedilmemeli');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Translation not saved', $e->getMessage());
        }
        $this->assertSame(0, ContentAsset::where('source_asset_id', $asset->id)->count());
        Http::assertSent(fn ($r) => ! str_contains(self::promptOf($r), 'Adapt cultural references (currency')
            && str_contains(self::promptOf($r), 'euro amounts stay in euros'));

        $new = (new ContentTranslator('test-key'))->translate($asset, 'en');
        $this->assertSame('draft', $new->status);
        $this->assertSame('Blocked account 2026: €11,904 (€992 per month).', $new->body_md);
    }

    public function test_gemini_translator_checked_variant_rejects_changed_figures(): void
    {
        config(['services.gemini.key' => 'test-key']);
        $this->assertStringContainsString('Keep numbers, amounts, dates, legal references', GeminiTranslator::SYSTEM_PROMPT_PROGRAM);
        $this->assertStringContainsString('Keep numbers, amounts, dates, legal references', GeminiTranslator::SYSTEM_PROMPT_UNIVERSITY);

        Http::fake(['generativelanguage.googleapis.com/*' => Http::sequence()
            ->push(self::gemini('Program 120 ECTS, harç 1.500 €.'))
            ->push(self::gemini('Program 90 ECTS, harç 1.500 €.'))]);
        $this->assertSame('Program 120 ECTS, harç 1.500 €.', app(GeminiTranslator::class)->translateChecked('Programme 120 ECTS, fee €1,500.')['translation']);

        $this->expectException(\RuntimeException::class);
        app(GeminiTranslator::class)->translateChecked('Programme 120 ECTS, fee €1,500.');
    }

    // ── 5. x-source-note ──────────────────────────────────────────────────────────

    public function test_source_note_distinguishes_check_date_from_update_date(): void
    {
        app()->setLocale('en');
        $checked = Blade::render('<x-source-note :sources="[[\'name\' => \'AA\', \'url\' => \'https://example.org\']]" checked="2026-10-14" />');
        $this->assertStringContainsString('Checked on <time datetime="2026-10-14">', $checked);
        $this->assertStringNotContainsString('verified', $checked);

        $updated = Blade::render('<x-source-note :sources="[\'DAAD\']" updated="2026-06-01" />');
        $this->assertStringContainsString('Last updated: <time datetime="2026-06-01">', $updated);
        $this->assertStringNotContainsString('verified', $updated, 'updated_at doğrulama iddiası değildir');
        $this->assertStringNotContainsString('Checked', $updated);

        $none = Blade::render('<x-source-note :sources="[\'DAAD\']" />');
        $this->assertStringContainsString('DAAD', $none);
        $this->assertStringNotContainsString('<time', $none);

        app()->setLocale('tr');
        $this->assertStringContainsString('Kontrol tarihi: <time', Blade::render('<x-source-note :sources="[\'AA\']" checked="2026-10-14" />'));
        app()->setLocale('de');
        $this->assertStringContainsString('Geprüft am <time', Blade::render('<x-source-note :sources="[\'AA\']" checked="2026-10-14" />'));
    }

    public function test_tool_pages_show_check_date_label(): void
    {
        $html = $this->get('/tr/tools/visa-appointment')->assertOk()->getContent();
        $this->assertStringContainsString('Kontrol tarihi: <time datetime="2026-10-14">', $html);
        $this->assertStringNotContainsString('Son doğrulama', $html);

        $en = $this->get('/en/tools/language-certificates')->assertOk()->getContent();
        $this->assertStringContainsString('Checked on <time datetime="2026-10-10">', $en);
        $this->assertStringNotContainsString('Last verified', $en);
        $de = $this->get('/de/tools/sperrkonto')->assertOk()->getContent();
        $this->assertStringContainsString('Geprüft am <time datetime="2026-06-04">', $de);
        $this->assertStringNotContainsString('Zuletzt geprüft', $de);
    }

    // ── 7. Chatbot ────────────────────────────────────────────────────────────────

    public function test_chat_system_prompt_forbids_check_date_as_effective_date(): void
    {
        foreach (['tr', 'en', 'de'] as $loc) {
            $system = app(ChatService::class)->buildPayload('Soru?', $loc, [['title' => 'T', 'url' => '/x', 'content' => 'Güncel durum (kontrol tarihi: 14.10.2026)']], [])['systemInstruction']['parts'][0]['text'];
            $this->assertStringContainsString('kuralın başladığı tarih değildir', $system, $loc);
            $this->assertStringContainsString('"since …"', $system);
            $this->assertStringContainsString('"seit …"', $system);
        }
        // Kural cevaba kopyalanırsa temizlenir
        $leak = "Başvurular portaldan yapılır [1].\n- Kaynakta \"kontrol tarihi\" ile verilen tarih, bilginin kontrol edildiği gündür; kuralın başladığı tarih değildir.";
        $this->assertSame('Başvurular portaldan yapılır [1].', app(ChatService::class)->stripInstructionLeak($leak));
    }

    public function test_chat_eval_string_checks_are_deterministic(): void
    {
        $item = ['q' => 'x', 'must_not_contain' => ['120 gün', '14.10.2026 tarihinden itibaren'], 'allow_context' => ['eskiden'], 'must_contain' => [['140'], ['portal', 'digital.diplo.de']]];
        $this->assertNull(ChatEval::stringChecks(['q' => 'x', 'expect' => 'answer'], 'anything'));
        $this->assertSame(['forbidden' => [], 'missing' => []], ChatEval::stringChecks($item, 'Sınır eskiden 120 gündü, şimdi 140 tam gün. Başvuru Portal üzerinden.'));
        $this->assertSame(['forbidden' => ['120 gün', '14.10.2026 tarihinden itibaren'], 'missing' => ['portal | digital.diplo.de']],
            ChatEval::stringChecks($item, '14.10.2026 tarihinden itibaren yılda 120 gün ve 140 yarım gün.'));
    }

    public function test_chat_eval_set_contains_osv_cases_in_existing_format(): void
    {
        $items = json_decode(file_get_contents(resource_path('data/chat_eval.json')), true)['items'];
        $osv = array_values(array_filter($items, fn ($i) => ($i['tag'] ?? null) === 'osv'));
        $this->assertGreaterThanOrEqual(15, count($osv));
        foreach ($items as $i) {
            $this->assertArrayHasKey('q', $i);
            $this->assertContains($i['lang'], ['tr', 'en', 'de']);
            $this->assertContains($i['expect'], ['answer', 'abstain']);
        }
        $claims = array_count_values(array_column($osv, 'claim'));
        foreach (['aps.turkey' => 3, 'visa.remonstration' => 2, 'visa.study.channel.tr' => 2, 'visa.fee' => 2, 'check-date' => 2, 'work.student.days' => 3, 'bluecard.2026' => 2, 'chancenkarte' => 2] as $c => $min) {
            $this->assertGreaterThanOrEqual($min, $claims[$c] ?? 0, $c);
        }
        $all = json_encode($osv, JSON_UNESCAPED_UNICODE);
        $this->assertStringContainsString('14.10.2026 tarihinden itibaren', $all);
        foreach ($osv as $i) {
            $this->assertNotEmpty($i['must_not_contain'] ?? $i['must_contain'] ?? null, $i['q']);
        }
    }
}

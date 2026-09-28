<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\FaqQualityAtlas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

/**
 * FAQ Parity Batch A migration: kapsam = atlas, eski durum parmak izi, slug çakışması, eksik hedef, kısmi durum,
 * yazım hatasında rollback, ikinci çalıştırma no-op, yeni EN/DE kardeşleri + hreflang, atlas anlık görüntüsü değişmez.
 * Motor küçük sentetik spec'le; gerçek spec dosyası ayrıca bütünlük + içerik güvenliği için taranır.
 */
class FaqParityBatchAMigrationTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_09_30_000100_faq_parity_batch_a.php';

    private int $topicId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->topicId = DB::table('faq_topics')->insertGetId(['name' => 'Vize', 'slug' => 'vize', 'created_at' => now(), 'updated_at' => now()]);
    }

    private function migration(): object
    {
        return require base_path(self::MIGRATION);
    }

    private function faq(string $locale, ?string $group, string $slug, string $question, ?string $html, ?string $md = null): int
    {
        return DB::table('faqs')->insertGetId([
            'locale' => $locale, 'translation_group_id' => $group, 'faq_topic_id' => $this->topicId,
            'question' => $question, 'slug' => $slug, 'answer_md' => $md ?? strip_tags((string) $html), 'answer_html' => $html,
            'has_answer' => (bool) $html, 'is_published' => true, 'intent' => 'bilgi', 'sort_order' => 7,
            'created_at' => '2026-09-01 10:00:00', 'updated_at' => '2026-09-01 10:00:00',
        ]);
    }

    private function atlas(string $trSlug, ?string $group, string $batch = 'A'): void
    {
        FaqQualityAtlas::create([
            'audit_label' => '2026-09-28', 'translation_group_id' => $group, 'tr_slug' => $trSlug,
            'tr_question_at_audit' => 'Soru', 'topic' => 'Visa', 'risk_level' => 'P0', 'tr_quality' => 'B',
            'tr_status' => 'STALE', 'en_status' => 'EMPTY', 'de_status' => 'MISSING', 'tr_issues' => [],
            'external_source_needed' => true, 'translation_strategy' => 'REWRITE_FROM_VERIFIED_FACTS', 'parity_score' => 1,
            'quality_ready_locales' => [], 'recommended_action' => 'Fix.', 'proposed_batch' => $batch, 'priority_score' => 300,
            'duplicate_of' => [], 'junk' => false, 'chatbot_risk' => true, 'audited_at' => '2026-09-28 20:00:00', 'resolved' => true,
        ]);
    }

    private const OLD_TR = 'Eski cevap: bloke hesapta 934 € olmalı ve Remonstration ile itiraz edebilirsin. Bu metin canlı sayfadaki eski gövdedir.';

    private const OLD_EN = 'Old answer text that is visible on the live English page and long enough to be fingerprinted by the migration.';

    private const KEEP_TR = 'Belgeyi gönderdikten sonra geri dönüş süresi üniversiteden üniversiteye değişir. Genellikle birkaç hafta içinde bilgi alırsın. Uzun süre dönüş yoksa öğrenci işlerine yaz.';

    /** 3 küme: A = TR rewrite + EN update (görünür) + DE update (gizli gövde); B = TR keep + EN/DE create; C = TR cümle silme + EN/DE create (TR grubu yok). */
    private function fixture(): array
    {
        $gA = (string) Str::uuid();
        $gB = (string) Str::uuid();
        $this->faq('tr', $gA, 'vize-soru', 'Vize sorusu?', '<p>'.self::OLD_TR.'</p>');
        $this->faq('en', $gA, 'vize-soru-en', 'Vize sorusu?', '<p>'.self::OLD_EN.'</p>');
        $this->faq('de', $gA, 'vize-soru-de', 'Vize sorusu?', null, 'Versteckter alter Text: 538 € Minijob.');
        $this->faq('tr', $gB, 'kalite-soru', 'Kaliteli soru?', '<p>'.self::OLD_TR.' (A1)</p>');
        $this->faq('tr', null, 'geri-donus', 'Geri dönüş süresi nedir?', '<p>'.self::KEEP_TR.'</p>', self::KEEP_TR."\n");
        $this->atlas('vize-soru', $gA);
        $this->atlas('kalite-soru', $gB);
        $this->atlas('geri-donus', null);
        $this->atlas('baska-batch', null, 'B');

        $old = fn (string $q, string $body) => ['question' => $q, 'visible' => 'text', 'head' => mb_substr($body, 0, 100)];
        $new = fn (string $q, string $md) => ['question' => $q, 'answer_md' => $md."\n"];

        return ['audit_label' => '2026-09-28', 'proposed_batch' => 'A', 'clusters' => [
            ['tr_slug' => 'vize-soru', 'chatbot_risk' => true, 'records' => [
                ['locale' => 'tr', 'slug' => 'vize-soru', 'mode' => 'rewrite', 'old' => $old('Vize sorusu?', self::OLD_TR), 'new' => $new('Vize sorusu?', 'Yeni TR cevap: 992 € (01.09.2024\'ten beri).')],
                ['locale' => 'en', 'slug' => 'vize-soru-en', 'mode' => 'update', 'old' => $old('Vize sorusu?', self::OLD_EN), 'new' => $new('What is the visa question?', 'New EN answer: 992 € since 1 September 2024.')],
                ['locale' => 'de', 'slug' => 'vize-soru-de', 'mode' => 'update', 'old' => ['question' => 'Vize sorusu?', 'visible' => 'empty'], 'new' => $new('Was ist die Visumfrage?', 'Neue DE-Antwort: 992 € pro Monat seit dem 01.09.2024. Der Betrag ist an den BAföG-Höchstsatz gekoppelt und gilt für den Nachweis der Finanzierung.')],
            ]],
            ['tr_slug' => 'kalite-soru', 'chatbot_risk' => false, 'group_if_missing' => 'b0000000-0000-5000-8000-000000000001', 'records' => [
                ['locale' => 'tr', 'slug' => 'kalite-soru', 'mode' => 'keep', 'old' => $old('Kaliteli soru?', self::OLD_TR)],
                ['locale' => 'en', 'slug' => 'quality-question', 'mode' => 'create', 'new' => $new('A quality question?', 'English answer created from the TR facts.')],
                ['locale' => 'de', 'slug' => 'quality-question-de', 'mode' => 'create', 'new' => $new('Eine Qualitätsfrage?', 'Deutsche Antwort aus den TR-Fakten.')],
            ]],
            ['tr_slug' => 'geri-donus', 'chatbot_risk' => false, 'group_if_missing' => 'b0000000-0000-5000-8000-000000000002', 'records' => [
                ['locale' => 'tr', 'slug' => 'geri-donus', 'mode' => 'remove', 'old' => $old('Geri dönüş süresi nedir?', self::KEEP_TR),
                    'remove' => ['Genellikle birkaç hafta içinde bilgi alırsın.'], 'anchor' => 'geri dönüş süresi üniversiteden üniversiteye değişir.'],
                ['locale' => 'en', 'slug' => 'response-time', 'mode' => 'create', 'new' => $new('How long until I hear back?', 'It varies from university to university.')],
                ['locale' => 'de', 'slug' => 'response-time-de', 'mode' => 'create', 'new' => $new('Wie lange dauert die Rückmeldung?', 'Das ist von Hochschule zu Hochschule verschieden.')],
            ]],
        ]];
    }

    private function snapshot(): array
    {
        return DB::table('faqs')->orderBy('id')->get()->map(fn ($f) => (array) $f)->all();
    }

    private function assertFailsWithoutWrites(array $spec, string $needle): void
    {
        $before = $this->snapshot();
        try {
            $this->migration()->run($spec);
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString($needle, $e->getMessage());
        }
        $this->assertSame($before, $this->snapshot());
    }

    public function test_applies_rewrites_updates_creates_and_removal(): void
    {
        $spec = $this->fixture();
        $count = Faq::count();

        $this->assertStringStartsWith('applied: TR 2, EN/DE güncellenen 2, EN/DE oluşturulan 4', $this->migration()->run($spec));
        $this->assertSame($count + 4, Faq::count());

        $a = Faq::where('slug', 'vize-soru')->first();
        $this->assertSame("Yeni TR cevap: 992 € (01.09.2024'ten beri).\n", $a->answer_md);
        $de = Faq::where('slug', 'vize-soru-de')->first();
        $this->assertSame('Was ist die Visumfrage?', $de->question);
        $this->assertStringContainsString('992 €', $de->answer_html);   // gizli gövde artık görünür ve güncel
        $this->assertStringNotContainsString('538', $de->answer_md);

        $keep = Faq::where('slug', 'kalite-soru')->first();
        $this->assertSame('2026-09-01 10:00:00', $keep->updated_at->toDateTimeString());   // A1 TR'ye dokunulmadı
        foreach (['quality-question' => 'en', 'quality-question-de' => 'de'] as $slug => $loc) {
            $n = Faq::where('slug', $slug)->first();
            $this->assertSame([$loc, $keep->translation_group_id, $this->topicId, 7, 'bilgi', true, true],
                [$n->locale, $n->translation_group_id, $n->faq_topic_id, $n->sort_order, $n->intent, $n->is_published, $n->has_answer]);
        }

        $c = Faq::where('slug', 'geri-donus')->first();
        $this->assertSame('b0000000-0000-5000-8000-000000000002', $c->translation_group_id);
        $this->assertStringNotContainsString('birkaç hafta', $c->answer_md);
        $this->assertStringContainsString('değişir. Uzun süre', $c->answer_md);
        $this->assertSame(['de', 'en', 'tr'], Faq::where('translation_group_id', $c->translation_group_id)->orderBy('locale')->pluck('locale')->all());
    }

    public function test_second_run_is_a_noop(): void
    {
        $spec = $this->fixture();
        $this->migration()->run($spec);
        $before = $this->snapshot();
        $this->travel(2)->days();

        $this->assertSame('noop', $this->migration()->run($spec));
        $this->assertSame($before, $this->snapshot());
    }

    public function test_new_locale_pages_render_with_reciprocal_hreflang_and_qa_schema(): void
    {
        $this->migration()->run($this->fixture());
        $g = Faq::where('slug', 'kalite-soru')->value('translation_group_id');
        $urls = ['tr' => url('tr/faq/vize/kalite-soru'), 'en' => url('en/faq/vize/quality-question'), 'de' => url('de/faq/vize/quality-question-de')];

        foreach ($urls as $loc => $u) {
            $html = $this->get($u)->assertOk()->getContent();
            $faq = Faq::where('translation_group_id', $g)->where('locale', $loc)->first();
            $this->assertStringContainsString(e($faq->question), $html);
            $this->assertMatchesRegularExpression('/"@type":\s*"QAPage"/', $html);
            foreach ($urls as $alt => $au) {
                $this->assertStringContainsString('hreflang="'.$alt.'" href="'.$au.'"', $html);
            }
        }
    }

    public function test_slug_collision_fails_without_writes(): void
    {
        $spec = $this->fixture();
        $this->faq('en', (string) Str::uuid(), 'quality-question', 'Another FAQ?', '<p>x</p>');
        $this->assertFailsWithoutWrites($spec, 'slug başka bir SSS');
    }

    public function test_slug_used_in_another_locale_fails(): void
    {
        $spec = $this->fixture();
        $this->faq('de', (string) Str::uuid(), 'response-time', 'Andere?', '<p>x</p>');
        $this->assertFailsWithoutWrites($spec, 'başka bir dilde');
    }

    public function test_missing_target_fails_without_writes(): void
    {
        $spec = $this->fixture();
        DB::table('faqs')->where('slug', 'vize-soru-en')->delete();
        $this->assertFailsWithoutWrites($spec, 'kayıt yok');
    }

    public function test_record_of_another_cluster_fails(): void
    {
        $spec = $this->fixture();
        DB::table('faqs')->where('slug', 'vize-soru-de')->update(['translation_group_id' => (string) Str::uuid()]);
        $this->assertFailsWithoutWrites($spec, 'aynı kümede değil');
    }

    public function test_unexpected_old_state_fails_without_writes(): void
    {
        $spec = $this->fixture();
        DB::table('faqs')->where('slug', 'vize-soru')->update(['answer_html' => '<p>Production’da farklı bir metin var, canlı taramadaki başlangıçla eşleşmiyor ve bu yüzden dokunulmamalı.</p>']);
        $this->assertFailsWithoutWrites($spec, 'beklenmeyen eski durum');
    }

    public function test_existing_sibling_for_a_create_fails(): void
    {
        $spec = $this->fixture();
        $g = Faq::where('slug', 'kalite-soru')->value('translation_group_id');
        $this->faq('en', $g, 'other-en-slug', 'Existing EN?', '<p>x</p>');
        $this->assertFailsWithoutWrites($spec, 'kardeşi var');
    }

    public function test_partially_applied_state_fails(): void
    {
        $spec = $this->fixture();
        $this->migration()->run($spec);
        DB::table('faqs')->where('slug', 'vize-soru-de')->update(['question' => 'Vize sorusu?', 'answer_html' => null]);
        $this->assertFailsWithoutWrites($spec, 'kısmen uygulanmış');
    }

    public function test_scope_must_equal_atlas_batch(): void
    {
        $spec = $this->fixture();
        array_pop($spec['clusters']);
        $this->assertFailsWithoutWrites($spec, 'kapsam atlas ile uyuşmuyor');
    }

    public function test_write_error_rolls_back_everything(): void
    {
        $spec = $this->fixture();
        $before = $this->snapshot();
        $n = 0;
        Faq::saving(function () use (&$n) {
            if (++$n === 4) {
                throw new RuntimeException('simulated write error');
            }
        });
        try {
            $this->migration()->run($spec);
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertSame('simulated write error', $e->getMessage());
        }
        $this->assertSame($before, $this->snapshot());
    }

    public function test_atlas_snapshot_is_untouched_and_review_needed_appears(): void
    {
        $spec = $this->fixture();
        $atlas = DB::table('faq_quality_atlas')->orderBy('id')->get()->all();
        Carbon::setTestNow('2026-09-29 10:00:00');
        $this->migration()->run($spec);

        $this->assertEquals($atlas, DB::table('faq_quality_atlas')->orderBy('id')->get()->all());
        $row = FaqQualityAtlas::query()->withLive()->where('tr_slug', 'vize-soru')->first();
        $this->assertSame(['B', 'P0', 'A', 'CONTENT'], [$row->tr_quality, $row->risk_level, $row->proposed_batch, $row->de_live]);
        $this->assertTrue((bool) $row->review_needed);
        Carbon::setTestNow();
    }

    /** Gerçek spec dosyası: bütünlük (SHA-256), küme yapısı, dil/slug/bağlantı ve güncel değer güvenliği. */
    public function test_real_spec_integrity_and_content_safety(): void
    {
        $m = $this->migration();
        $raw = str_replace("\r\n", "\n", file_get_contents(base_path($m::SPEC)));
        $this->assertSame($m::SPEC_SHA256, hash('sha256', $raw));
        $spec = json_decode($raw, true);

        $slugs = [];
        foreach ($spec['clusters'] as $c) {
            $this->assertSame(['tr', 'en', 'de'], array_column($c['records'], 'locale'), $c['tr_slug']);
            foreach ($c['records'] as $r) {
                if ($r['mode'] === 'create') {
                    $this->assertMatchesRegularExpression('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $r['slug']);
                    $this->assertNotContains($r['slug'], $slugs);
                    $slugs[] = $r['slug'];
                }
                if (! isset($r['new'])) {
                    continue;
                }
                $md = $r['new']['answer_md'];
                $this->assertNotSame('', trim($md));
                if ($r['locale'] !== 'tr') {
                    $this->assertFalse(Faq::looksTurkish($r['new']['question'].' '.$md), "{$c['tr_slug']}/{$r['locale']} Türkçe sızıntı");
                }
                preg_match_all('/\]\((\/[^)\s]+)\)/', $md, $links);
                foreach ($links[1] as $u) {
                    $this->assertStringStartsWith("/{$r['locale']}/", $u, "{$c['tr_slug']}: dil dışı iç bağlantı");
                }
                $this->assertDoesNotMatchRegularExpression('/(?<![\d.,])538\s*(?:€|Euro)|(?<![\d.,])934(?!\d)|11[.,]208|birkaç hafta/u', $md, $c['tr_slug']);
                $this->assertDoesNotMatchRegularExpression('/(?<![\d.,])120\s*(?:tam|full|volle)(?![^\n]{0,80}(?:sona erdi|ended|endete))/iu', $md, $c['tr_slug']);
            }
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\FaqQualityAtlas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

/**
 * FAQ Parity Batch B Wave 1 migration: motor Batch A ile aynı (orada ayrıntılı test edildi); burada Batch B'ye özgü
 * kapsam kuralı (spec = atlas Batch B'nin ALT kümesi), temel akış (güncelle + yeni kardeş + no-op + çakışma + rollback)
 * ve gerçek spec dosyasının bütünlüğü + içerik güvenliği sabitlenir.
 */
class FaqParityBatchBWave1MigrationTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_09_30_000300_faq_parity_batch_b_wave1.php';

    private int $topicId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->topicId = DB::table('faq_topics')->insertGetId(['name' => 'Uni-Assist', 'slug' => 'uni-assist', 'created_at' => now(), 'updated_at' => now()]);
    }

    private function migration(): object
    {
        return require base_path(self::MIGRATION);
    }

    private function faq(string $locale, ?string $group, string $slug, string $question, ?string $html): int
    {
        return DB::table('faqs')->insertGetId([
            'locale' => $locale, 'translation_group_id' => $group, 'faq_topic_id' => $this->topicId,
            'question' => $question, 'slug' => $slug, 'answer_md' => strip_tags((string) $html), 'answer_html' => $html,
            'has_answer' => (bool) $html, 'is_published' => true, 'intent' => 'bilgi', 'sort_order' => 3,
            'created_at' => '2026-09-01 10:00:00', 'updated_at' => '2026-09-01 10:00:00',
        ]);
    }

    private function atlas(string $trSlug, ?string $group, string $batch): void
    {
        FaqQualityAtlas::create([
            'audit_label' => '2026-09-28', 'translation_group_id' => $group, 'tr_slug' => $trSlug, 'tr_question_at_audit' => 'Soru',
            'topic' => 'Uni-Assist / VPD', 'risk_level' => 'P1', 'tr_quality' => 'D', 'tr_status' => 'STALE', 'en_status' => 'EMPTY',
            'de_status' => 'MISSING', 'tr_issues' => [], 'external_source_needed' => true, 'translation_strategy' => 'RESEARCH_FIRST',
            'parity_score' => 1, 'quality_ready_locales' => [], 'recommended_action' => 'Fix.', 'proposed_batch' => $batch,
            'priority_score' => 250, 'duplicate_of' => [], 'junk' => false, 'chatbot_risk' => false,
            'audited_at' => '2026-09-28 20:00:00', 'resolved' => true,
        ]);
    }

    private const OLD = 'Eski cevap: SWIFT/BIC HASPDEHHXXX ile öde, zahlung adresine yaz; VPD 2-3 yıl geçerlidir. Bu metin eski gövdedir.';

    /** 2 küme Batch B'de (biri EN güncelleme + DE yeni), 1 küme Batch B'de ama spec dışında (dokunulmamalı). */
    private function fixture(): array
    {
        $g1 = (string) Str::uuid();
        $g2 = (string) Str::uuid();
        $this->faq('tr', $g1, 'odeme-hatasi', 'Ödeme hatası?', '<p>'.self::OLD.'</p>');
        $this->faq('en', $g1, 'odeme-hatasi-en', 'Payment error?', null);
        $this->faq('tr', $g2, 'zulassung', 'Ön onay nedir?', '<p>'.self::OLD.' (2)</p>');
        $this->faq('tr', null, 'baska-b-kumesi', 'Başka?', '<p>'.self::OLD.' (3)</p>');
        $this->atlas('odeme-hatasi', $g1, 'B');
        $this->atlas('zulassung', $g2, 'B');
        $this->atlas('baska-b-kumesi', null, 'B');
        $this->atlas('a-kumesi', null, 'A');

        $old = fn (string $q, string $b) => ['question' => $q, 'visible' => 'text', 'head' => mb_substr($b, 0, 100)];
        $new = fn (string $q, string $md) => ['question' => $q, 'answer_md' => $md."\n"];

        return ['audit_label' => '2026-09-28', 'proposed_batch' => 'B', 'clusters' => [
            ['tr_slug' => 'odeme-hatasi', 'records' => [
                ['locale' => 'tr', 'slug' => 'odeme-hatasi', 'mode' => 'rewrite', 'old' => $old('Ödeme hatası?', self::OLD), 'new' => $new('Ödeme hatası?', 'Yalnız resmî My assist talimatlarını kullan.')],
                ['locale' => 'en', 'slug' => 'odeme-hatasi-en', 'mode' => 'update', 'old' => ['question' => 'Payment error?', 'visible' => 'empty'], 'new' => $new('What if my payment fails?', 'Use only the official My assist instructions.')],
                ['locale' => 'de', 'slug' => 'payment-error-de', 'mode' => 'create', 'new' => $new('Was tun bei Zahlungsfehlern?', 'Nutze nur die offiziellen Hinweise in My assist.')],
            ], 'group_if_missing' => 'c0000000-0000-5000-8000-000000000001'],
            ['tr_slug' => 'zulassung', 'records' => [
                ['locale' => 'tr', 'slug' => 'zulassung', 'mode' => 'rewrite', 'old' => $old('Ön onay nedir?', self::OLD.' (2)'), 'new' => $new('Zulassungsbescheid nedir?', 'Üniversitenin kabul kararıdır; VPD değildir.')],
                ['locale' => 'en', 'slug' => 'zulassungsbescheid-vs-vpd', 'mode' => 'create', 'new' => $new('What is a Zulassungsbescheid?', 'The university admission decision; not a VPD.')],
                ['locale' => 'de', 'slug' => 'zulassungsbescheid-vs-vpd-de', 'mode' => 'create', 'new' => $new('Was ist ein Zulassungsbescheid?', 'Die Zulassungsentscheidung der Hochschule; keine VPD.')],
            ], 'group_if_missing' => 'c0000000-0000-5000-8000-000000000002'],
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

    public function test_subset_of_batch_b_applies_and_leaves_rest_untouched(): void
    {
        $spec = $this->fixture();
        $count = Faq::count();
        $untouched = (array) DB::table('faqs')->where('slug', 'baska-b-kumesi')->first();

        $this->assertStringStartsWith('applied: TR 2, EN/DE güncellenen 1, EN/DE oluşturulan 3', $this->migration()->run($spec));
        $this->assertSame($count + 3, Faq::count());
        $this->assertSame($untouched, (array) DB::table('faqs')->where('slug', 'baska-b-kumesi')->first());

        $g = Faq::where('slug', 'zulassung')->value('translation_group_id');
        $this->assertSame(['de', 'en', 'tr'], Faq::where('translation_group_id', $g)->orderBy('locale')->pluck('locale')->all());
        $this->assertStringNotContainsString('HASPDE', (string) Faq::where('slug', 'odeme-hatasi')->value('answer_md'));
        $this->assertTrue((bool) Faq::where('slug', 'odeme-hatasi-en')->value('has_answer'));   // eski gizli/boş EN artık görünür
    }

    public function test_second_run_is_a_noop(): void
    {
        $spec = $this->fixture();
        $this->migration()->run($spec);
        $before = $this->snapshot();
        $this->travel(1)->days();
        $this->assertSame('noop', $this->migration()->run($spec));
        $this->assertSame($before, $this->snapshot());
    }

    public function test_cluster_outside_batch_b_fails(): void
    {
        $spec = $this->fixture();
        $extra = $spec['clusters'][0];
        $extra['tr_slug'] = 'a-kumesi';
        $spec['clusters'][] = $extra;
        $this->assertFailsWithoutWrites($spec, 'kapsam');
    }

    public function test_slug_collision_fails(): void
    {
        $spec = $this->fixture();
        $this->faq('en', (string) Str::uuid(), 'zulassungsbescheid-vs-vpd', 'Other?', '<p>x</p>');
        $this->assertFailsWithoutWrites($spec, 'slug başka bir SSS');
    }

    public function test_unexpected_old_state_fails(): void
    {
        $spec = $this->fixture();
        DB::table('faqs')->where('slug', 'odeme-hatasi')->update(['question' => 'Değişmiş?']);
        $this->assertFailsWithoutWrites($spec, 'beklenmeyen eski durum');
    }

    public function test_write_error_rolls_back(): void
    {
        $spec = $this->fixture();
        $before = $this->snapshot();
        $n = 0;
        Faq::saving(function () use (&$n) {
            if (++$n === 3) {
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

    /** Gerçek spec: SHA-256, yapı, dil, bağlantı ve Wave 1 içerik güvenliği. */
    public function test_real_spec_integrity_and_content_safety(): void
    {
        $m = $this->migration();
        $raw = str_replace("\r\n", "\n", file_get_contents(base_path($m::SPEC)));
        $this->assertSame($m::SPEC_SHA256, hash('sha256', $raw));
        $spec = json_decode($raw, true);
        $this->assertSame('B', $spec['proposed_batch']);
        $this->assertCount(6, $spec['clusters']);

        $allowed = ['/tr/blog/internship-in-germany-with-b1-b2-german', '/en/blog/internship-in-germany-with-b1-b2-german-en', '/de/blog/internship-in-germany-with-b1-b2-german-de',
            '/tr/blog/conditional-admission-visa-germany-which-merkblatt-and-language-level', '/en/blog/conditional-admission-visa-germany-which-merkblatt-and-language-level-en',
            '/de/blog/conditional-admission-visa-germany-which-merkblatt-and-language-level-de'];
        foreach ($spec['clusters'] as $c) {
            $this->assertSame(['tr', 'en', 'de'], array_column($c['records'], 'locale'));
            foreach ($c['records'] as $r) {
                $md = $r['new']['answer_md'];
                if ($r['locale'] !== 'tr') {
                    $this->assertFalse(Faq::looksTurkish($r['new']['question'].' '.$md), "{$c['tr_slug']}/{$r['locale']} Türkçe sızıntı");
                }
                preg_match_all('/\]\((\/[^)\s]+)\)/', $md, $links);
                foreach ($links[1] as $u) {
                    $this->assertContains($u, $allowed, "{$c['tr_slug']}: izinsiz iç bağlantı {$u}");
                    $this->assertStringStartsWith("/{$r['locale']}/", $u);
                }
                preg_match_all('/\]\((https?:\/\/[^)\s]+)\)/', $md, $ext);
                foreach ($ext[1] as $u) {
                    $this->assertStringStartsWith('https://www.uni-assist.de/', $u);
                }
                // Wave 1 content safety
                $this->assertDoesNotMatchRegularExpression('/HASPDE|SWIFT\/BIC:|zahlung@|@uni-assist|PayPal|Sofort|Wise|Revolut|Western Union/u', $md);
                $this->assertDoesNotMatchRegularExpression('/2\s?[-–]\s?3\s*(yıl|years|Jahre)|120\s*\/\s*240|(?<![\d.,])538(?!\d)|2\s?[-–]\s?4\s*(ay|months|Monate)/u', $md);
                $this->assertDoesNotMatchRegularExpression('/kesinlikle|absolutely|definitiv|H\+/u', $md);
            }
        }
    }
}

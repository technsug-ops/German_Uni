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
 * FAQ Parity Batch B Wave 2 migration: motor Wave 1 ile aynı; burada Wave 2'ye özgü kapsam kuralı ("batch" kümeleri
 * atlas Batch B'de, "extra" P0 kümeleri atlas'ta herhangi bir sınıfta — sınıfları değişmez), temel akış (güncelle +
 * yeni kardeş + no-op + kısmi durum + rollback) ve gerçek spec dosyasının bütünlüğü + içerik güvenliği + dil paritesi.
 */
class FaqParityBatchBWave2MigrationTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_09_30_000800_faq_parity_batch_b_wave2.php';

    private int $topicId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->topicId = DB::table('faq_topics')->insertGetId(['name' => 'Denklik', 'slug' => 'denklik', 'created_at' => now(), 'updated_at' => now()]);
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
            'topic' => 'Recognition / Anabin / ZAB', 'risk_level' => 'P0', 'tr_quality' => 'D', 'tr_status' => 'STALE', 'en_status' => 'EMPTY',
            'de_status' => 'MISSING', 'tr_issues' => [], 'external_source_needed' => true, 'translation_strategy' => 'RESEARCH_FIRST',
            'parity_score' => 1, 'quality_ready_locales' => [], 'recommended_action' => 'Fix.', 'proposed_batch' => $batch,
            'priority_score' => 300, 'duplicate_of' => [], 'junk' => false, 'chatbot_risk' => true,
            'audited_at' => '2026-09-28 20:00:00', 'resolved' => true,
        ]);
    }

    private const OLD = 'Eski cevap: H+ tam denklik ve direkt kabul demektir; YKS 200-250 genel programlar, 450+ tıp için yeterlidir.';

    /** 1 Batch-B kümesi (EN güncelle + DE yeni), 1 ek P0 kümesi (OUTSIDE_BATCH), 1 Batch-B kümesi spec dışında. */
    private function fixture(): array
    {
        $g1 = (string) Str::uuid();
        $g2 = (string) Str::uuid();
        $this->faq('tr', $g1, 'anabin-h', 'Anabin H+ ne demek?', '<p>'.self::OLD.'</p>');
        $this->faq('en', $g1, 'anabin-h-en', 'What is H+?', null);
        $this->faq('tr', $g2, 'tu9-kumesi', 'TU9 Studienkolleg ister mi?', '<p>'.self::OLD.' (2)</p>');
        $this->faq('en', $g2, 'tu9-kumesi-en', 'Does TU9 require it?', '<p>'.self::OLD.' (en)</p>');
        $this->faq('de', $g2, 'tu9-kumesi-de', 'Verlangt TU9 das?', '<p>'.self::OLD.' (de)</p>');
        $this->faq('tr', null, 'baska-b-kumesi', 'Başka?', '<p>'.self::OLD.' (3)</p>');
        $this->atlas('anabin-h', $g1, 'B');
        $this->atlas('tu9-kumesi', $g2, 'OUTSIDE_BATCH');
        $this->atlas('baska-b-kumesi', null, 'B');
        $this->atlas('a-kumesi', null, 'A');

        $old = fn (string $q, string $b) => ['question' => $q, 'visible' => 'text', 'head' => mb_substr($b, 0, 100)];
        $new = fn (string $q, string $md) => ['question' => $q, 'answer_md' => $md."\n"];

        return ['audit_label' => '2026-09-28', 'proposed_batch' => 'B', 'clusters' => [
            ['tr_slug' => 'anabin-h', 'scope' => 'batch', 'records' => [
                ['locale' => 'tr', 'slug' => 'anabin-h', 'mode' => 'rewrite', 'old' => $old('Anabin H+ ne demek?', self::OLD), 'new' => $new('Anabin H+ ne demek?', 'H+ kurum statüsüdür; kabul değildir.')],
                ['locale' => 'en', 'slug' => 'anabin-h-en', 'mode' => 'update', 'old' => ['question' => 'What is H+?', 'visible' => 'empty'], 'new' => $new('What does anabin H+ mean?', 'H+ is an institution status, not admission.')],
                ['locale' => 'de', 'slug' => 'what-does-anabin-h-mean-de', 'mode' => 'create', 'new' => $new('Was bedeutet H+ in anabin?', 'H+ ist ein Status der Hochschule, keine Zulassung.')],
            ], 'group_if_missing' => 'c0000000-0000-5000-8000-0000000000a1'],
            ['tr_slug' => 'tu9-kumesi', 'scope' => 'extra', 'records' => [
                ['locale' => 'tr', 'slug' => 'tu9-kumesi', 'mode' => 'rewrite', 'old' => $old('TU9 Studienkolleg ister mi?', self::OLD.' (2)'), 'new' => $new('TU9 Studienkolleg ister mi?', 'TU9 için ayrı bir kural yok.')],
                ['locale' => 'en', 'slug' => 'tu9-kumesi-en', 'mode' => 'update', 'old' => $old('Does TU9 require it?', self::OLD.' (en)'), 'new' => $new('Does TU9 require a Studienkolleg?', 'There is no TU9-specific rule.')],
                ['locale' => 'de', 'slug' => 'tu9-kumesi-de', 'mode' => 'update', 'old' => $old('Verlangt TU9 das?', self::OLD.' (de)'), 'new' => $new('Verlangt TU9 ein Studienkolleg?', 'Es gibt keine TU9-spezifische Regel.')],
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

    public function test_batch_and_extra_clusters_apply_and_rest_is_untouched(): void
    {
        $spec = $this->fixture();
        $count = Faq::count();
        $untouched = (array) DB::table('faqs')->where('slug', 'baska-b-kumesi')->first();
        $atlas = FaqQualityAtlas::orderBy('id')->get()->toArray();

        $this->assertStringStartsWith('applied: TR 2, EN/DE güncellenen 3, EN/DE oluşturulan 1', $this->migration()->run($spec));
        $this->assertSame($count + 1, Faq::count());
        $this->assertSame($untouched, (array) DB::table('faqs')->where('slug', 'baska-b-kumesi')->first());
        $this->assertSame($atlas, FaqQualityAtlas::orderBy('id')->get()->toArray());   // atlas'a yazılmaz; ek kümenin sınıfı değişmez

        foreach (['anabin-h', 'tu9-kumesi'] as $s) {
            $g = Faq::where('slug', $s)->value('translation_group_id');
            $this->assertSame(['de', 'en', 'tr'], Faq::where('translation_group_id', $g)->orderBy('locale')->pluck('locale')->all(), "{$s}: locale başına tek kayıt");
        }
        $this->assertTrue((bool) Faq::where('slug', 'anabin-h-en')->value('has_answer'));
        $this->assertStringNotContainsString('tam denklik', (string) Faq::where('slug', 'anabin-h')->value('answer_md'));
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

    public function test_scope_rules(): void
    {
        // "batch" kümesi atlas'ta Batch B değilse → FAIL
        $spec = $this->fixture();
        $x = $spec['clusters'][0];
        $x['tr_slug'] = 'a-kumesi';
        $spec['clusters'][] = $x;
        $this->assertFailsWithoutWrites($spec, 'kapsam');

        // "extra" kümesi atlas'ta hiç yoksa → FAIL
        $spec = $this->fixture2();
        $spec['clusters'][1]['tr_slug'] = 'atlasta-olmayan';
        $this->assertFailsWithoutWrites($spec, 'kapsam');
    }

    private function fixture2(): array
    {
        DB::table('faqs')->delete();
        FaqQualityAtlas::query()->delete();

        return $this->fixture();
    }

    public function test_partial_state_and_unexpected_old_state_fail_without_writes(): void
    {
        $spec = $this->fixture();
        $this->migration()->run($spec);
        DB::table('faqs')->where('slug', 'tu9-kumesi-de')->update(['question' => 'Verlangt TU9 das?', 'answer_html' => '<p>'.self::OLD.' (de)</p>']);
        $this->assertFailsWithoutWrites($spec, 'kısmen uygulanmış');

        $spec = $this->fixture2();
        DB::table('faqs')->where('slug', 'anabin-h')->update(['question' => 'Değişmiş?']);
        $this->assertFailsWithoutWrites($spec, 'beklenmeyen eski durum');
    }

    public function test_write_error_rolls_back(): void
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

    /** Gerçek spec: SHA-256, kapsam, dil, bağlantı, sayı paritesi, arama niyeti ve F1–F5 içerik güvenliği. */
    public function test_real_spec_integrity_parity_and_content_safety(): void
    {
        $m = $this->migration();
        $raw = str_replace("\r\n", "\n", file_get_contents(base_path($m::SPEC)));
        $this->assertSame($m::SPEC_SHA256, hash('sha256', $raw));
        $spec = json_decode($raw, true);
        $this->assertSame('B', $spec['proposed_batch']);
        $this->assertCount(19, $spec['clusters']);
        $this->assertSame(['batch' => 17, 'extra' => 2], array_count_values(array_column($spec['clusters'], 'scope')));

        $allowed = [];
        foreach (['what-is-anabin-h-h-h-how-is-a-turkish-diploma', 'studienkolleg-guide-2026-who-needs-it-which-course-which-school'] as $g) {
            $allowed[] = "/tr/blog/{$g}";
            $allowed[] = "/en/blog/{$g}-en";
            $allowed[] = "/de/blog/{$g}-de";
        }
        // Arama niyeti: her dildeki soru kümenin çekirdek terimini korur
        $intent = [
            'studienkolleg-nedir' => 'Studienkolleg', 'turk-lise' => '/lise|high school|Abitur/iu', 'lise-denklik' => '/not|grade|Note/iu',
            'almanyada-universiteye-kabul' => '/kabul|admission|Zulassung/iu', 'turkiyede-universite-1' => '/1\.|first year|erste/iu',
            'almanyada-universite-basvurularinda-yks' => 'YKS', 'on-lisans' => '/Ön ?lisans|Önlisans/iu', 'hazirlik-programini' => '/Bachelor/iu',
            'uni-assist-vpd-nedir' => 'VPD', 'uni-assist-vpd-belgesi' => 'VPD', 'vpd-aldiktan' => 'VPD', 'uni-assist-evraklarini' => '/post/iu',
            'uni-assist-hzb' => 'HZB', 'anabin-h-ve' => 'H+', 'turkiyedeki-universitemin-anabin' => '/anabin/iu', 'universite-diplomam' => '/denk|equivalent|gleichwertig/iu',
            'diploma-denkligi' => '/denklik/iu', 'almanyadaki-tu9' => 'TU9', 'studienkollege-gitmeden' => 'Studienkolleg',
        ];
        $hard = '/(?<![\d.,])450(?!\d)|200\s?[-–]\s?250|3[.,]20\s?(→|->)|150\s?[-–]\s?200\s?€|10\s?[-–]\s?16\s*(hafta|weeks|Wochen)|2\s?[-–]\s?3\s*(yıl|years|Jahre)|tam denklik|full equivalence|volle Gleichwertigkeit|(çoğu|most|die meisten)[^.\n]{0,40}Studienkolleg|HASPDE|%\s?70|70\s?%/iu';

        foreach ($spec['clusters'] as $c) {
            $this->assertSame(['tr', 'en', 'de'], array_column($c['records'], 'locale'));
            $key = collect($intent)->first(fn ($v, $k) => str_starts_with($c['tr_slug'], $k));
            $this->assertNotNull($key, "{$c['tr_slug']}: niyet anahtarı tanımlı değil");
            $nums = [];
            foreach ($c['records'] as $r) {
                $q = $r['new']['question'];
                $md = $r['new']['answer_md'];
                $plain = preg_replace('/\]\([^)]*\)/', ']', $md);
                $words = preg_match_all('/[\p{L}\p{N}]+/u', $plain);
                $this->assertGreaterThanOrEqual(100, $words, "{$c['tr_slug']}/{$r['locale']}: çok kısa");
                $this->assertLessThanOrEqual(320, $words, "{$c['tr_slug']}/{$r['locale']}: çok uzun");
                str_starts_with($key, '/') ? $this->assertMatchesRegularExpression($key, $q, "{$c['tr_slug']}/{$r['locale']}: soru niyeti") : $this->assertStringContainsString($key, $q);
                $this->assertDoesNotMatchRegularExpression($hard, $q.' '.$md, "{$c['tr_slug']}/{$r['locale']}: yasak iddia");
                if ($r['locale'] !== 'tr') {
                    $this->assertFalse(Faq::looksTurkish($q.' '.$md), "{$c['tr_slug']}/{$r['locale']} Türkçe sızıntı");
                }
                if ($r['mode'] === 'create') {
                    $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', $r['slug']);
                    $this->assertSame($r['locale'] === 'de', str_ends_with($r['slug'], '-de'));
                }
                preg_match_all('/\]\((\/[^)\s]+)\)/', $md, $links);
                foreach ($links[1] as $u) {
                    $this->assertContains($u, $allowed, "{$c['tr_slug']}: izinsiz iç bağlantı {$u}");
                    $this->assertStringStartsWith("/{$r['locale']}/", $u);
                }
                preg_match_all('/\]\((https?:\/\/[^)\s]+)\)/', $md, $ext);
                foreach ($ext[1] as $u) {
                    $this->assertMatchesRegularExpression('#^https://(anabin\.kmk\.org|zab\.kmk\.org|www\.uni-assist\.de|www\.kmk\.org|www\.daad\.de)/#', $u);
                }
                preg_match_all('/(?<![\d.,])(\d{2,4})(?!\d)/', $plain, $n);
                $nums[$r['locale']] = array_values(array_diff(array_unique($n[1]), ['2026']));
                sort($nums[$r['locale']]);
            }
            $this->assertSame($nums['tr'], $nums['en'], "{$c['tr_slug']}: TR/EN sayı paritesi");
            $this->assertSame($nums['tr'], $nums['de'], "{$c['tr_slug']}: TR/DE sayı paritesi");
        }

        // F1–F5 çekirdek olguları kümelerde doğru yerde
        $by = collect($spec['clusters'])->keyBy('tr_slug');
        $tr = fn ($s) => collect($by[$s]['records'])->firstWhere('locale', 'tr')['new']['answer_md'];
        $this->assertStringContainsString('180', $tr('turk-lise-diplomasinin-almanyada-gecerliligi-var-mi'));
        $this->assertStringContainsString('genel bir yol', $tr('studienkollege-gitmeden-veya-turkiyede-bir-yil-okumadan-almanyada-universiteye-kabul-almak-mumkun-mu'));
        $this->assertStringContainsString('208 €', $tr('diploma-denkligi-denklik-basvurusu-sureci-nasil-isler'));
        $this->assertStringContainsString('bir yıl', $tr('uni-assist-vpd-belgesi-her-universite-basvurusu-icin-ayri-mi-alinmalidir'));
        $this->assertStringContainsString('X = 1 + 3 × (Nmax − Nd) / (Nmax − Nmin)', $tr('uni-assist-hzb-notu-nasil-hesaplanir'));
        $this->assertStringContainsString('kurum', $tr('anabin-h-ve-h-ne-anlama-gelir'));
    }
}

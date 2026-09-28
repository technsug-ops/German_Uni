<?php

namespace Tests\Feature;

use App\Filament\Resources\FaqQualityAtlas\Pages\ListFaqQualityAtlas;
use App\Filament\Resources\FaqQualityAtlas\Widgets\AtlasOverview;
use App\Models\Faq;
use App\Models\FaqQualityAtlas;
use App\Models\User;
use App\Services\FaqAtlas\AtlasFilters;
use App\Services\FaqAtlas\FaqAtlasImporter;
use App\Services\FaqAtlas\LiveStatus;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

/**
 * SSS Kalite Atlası V1 (salt okunur admin panosu): yetki, içe aktarım (checksum/şema/idempotency/SSS'e dokunmama),
 * filtreler, canlı durum, drift, detay, CSV, sayfalama.
 */
class FaqQualityAtlasDashboardTest extends TestCase
{
    use RefreshDatabase;

    private const AUDIT = '2026-09-28';
    private const AUDITED_AT = '2026-09-28 20:00:00';
    private int $topicId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->topicId = DB::table('faq_topics')->insertGetId(['name' => 'İş', 'slug' => 'is', 'created_at' => now(), 'updated_at' => now()]);
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    /** Ham insert (model hook'ları devre dışı) — updated_at kontrollü. */
    private function faq(string $locale, string $group, string $slug, ?string $html, array $extra = []): int
    {
        return DB::table('faqs')->insertGetId(array_merge([
            'locale' => $locale, 'translation_group_id' => $group, 'faq_topic_id' => $this->topicId,
            'question' => "Soru {$slug}", 'slug' => $slug, 'answer_md' => strip_tags((string) $html), 'answer_html' => $html,
            'has_answer' => $html !== null && $html !== '', 'is_published' => true,
            'created_at' => '2026-09-01 10:00:00', 'updated_at' => '2026-09-01 10:00:00',
        ], $extra));
    }

    private function long(): string
    {
        return '<p>'.str_repeat('Almanya\'da öğrenci olarak çalışma kuralları ve Arbeitstagekonto. ', 3).'</p>';
    }

    /** Tam küme: TR+EN+DE içerikli (atlas COMPLETE). */
    private function cluster(string $slug, array $atlas = [], array $locales = ['tr', 'en', 'de']): FaqQualityAtlas
    {
        $g = (string) Str::uuid();
        foreach ($locales as $l) {
            $this->faq($l, $g, $l === 'tr' ? $slug : "{$slug}-{$l}", $this->long());
        }

        return FaqQualityAtlas::create(array_merge([
            'audit_label' => self::AUDIT, 'translation_group_id' => $g, 'tr_slug' => $slug,
            'en_slug' => "{$slug}-en", 'de_slug' => "{$slug}-de", 'tr_question_at_audit' => "Soru {$slug}",
            'topic' => 'Student Work', 'risk_level' => 'P1', 'tr_quality' => 'A',
            'tr_status' => 'COMPLETE', 'en_status' => 'COMPLETE', 'de_status' => 'COMPLETE',
            'tr_issues' => [], 'external_source_needed' => false, 'translation_strategy' => 'TRANSLATE_DIRECTLY',
            'parity_score' => 3, 'quality_ready_locales' => ['TR', 'EN', 'DE'], 'recommended_action' => 'No action needed.',
            'proposed_batch' => 'OUTSIDE_BATCH', 'priority_score' => 100, 'duplicate_of' => [], 'junk' => false,
            'chatbot_risk' => false, 'audited_at' => self::AUDITED_AT, 'resolved' => true,
        ], $atlas));
    }

    private function list(array $query = [])
    {
        $this->actingAs($this->admin());
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        return Livewire::withQueryParams($query)->test(ListFaqQualityAtlas::class);
    }

    // ───────────── AUTH ─────────────

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/ops/faq-atlas')->assertRedirect();
        $this->get('/admin/ops/faq-atlas/export')->assertRedirect();
    }

    public function test_normal_user_and_editor_are_forbidden(): void
    {
        $this->cluster('a-slug');
        foreach ([User::factory()->create(), User::factory()->create(['is_editor' => true])] as $u) {
            $this->actingAs($u)->get('/admin/ops/faq-atlas')->assertForbidden();
            $this->actingAs($u)->get('/admin/ops/faq-atlas/cluster/'.FaqQualityAtlas::first()->id)->assertForbidden();
            $this->actingAs($u)->get('/admin/ops/faq-atlas/export')->assertForbidden();
        }
    }

    public function test_admin_can_open_dashboard_and_detail(): void
    {
        $c = $this->cluster('open-me', ['tr_issues' => ['Eski tutar 538 €']]);
        $this->actingAs($this->admin())->get('/admin/ops/faq-atlas')->assertOk()->assertSee('Soru open-me')->assertSee('Toplam küme');
        $this->get('/admin/ops/faq-atlas/cluster/'.$c->id)->assertOk()->assertSee('Eski tutar 538 €');
    }

    // ───────────── IMPORT ─────────────

    private function dataset(array $clusters, string $label = self::AUDIT): string
    {
        $path = storage_path('framework/testing/atlas-'.$label.'-'.Str::random(6).'.json');
        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, json_encode(['schema_version' => 1, 'audit_label' => $label, 'audited_at' => self::AUDITED_AT, 'clusters' => $clusters], JSON_UNESCAPED_UNICODE));

        return $path;
    }

    private function dsRow(string $tr, array $over = []): array
    {
        return array_merge(['tr_slug' => $tr, 'en_slug' => "{$tr}-en", 'de_slug' => "{$tr}-de", 'tr_question' => "Soru {$tr}",
            'topic' => 'Visa', 'risk_level' => 'P0', 'tr_quality' => 'B', 'tr_status' => 'COMPLETE', 'en_status' => 'EMPTY', 'de_status' => 'MISSING',
            'tr_issues' => ['x'], 'authoritative_internal_source' => null, 'external_source_needed' => false,
            'translation_strategy' => 'REWRITE_FROM_VERIFIED_FACTS', 'parity_score' => 1, 'quality_ready_locales' => [],
            'recommended_action' => 'Fix', 'proposed_batch' => 'A', 'priority_score' => 350, 'duplicate_of' => [], 'junk' => false,
            'notes' => null, 'chatbot_risk' => true, 'stale_markers' => ['tr' => []]], $over);
    }

    public function test_valid_import_resolves_clusters_and_keeps_missing_locales_missing(): void
    {
        $g = (string) Str::uuid();
        $tr = $this->faq('tr', $g, 'vize-x', $this->long());
        $en = $this->faq('en', $g, 'vize-x-en', null);
        $path = $this->dataset([$this->dsRow('vize-x'), $this->dsRow('yok-boyle-bir-slug')]);

        $r = app(FaqAtlasImporter::class)->import($path, false, FaqAtlasImporter::checksum(file_get_contents($path)));

        $this->assertSame(2, $r['inserted']);
        $this->assertSame(1, $r['resolved']);
        $row = FaqQualityAtlas::where('tr_slug', 'vize-x')->first();
        $this->assertTrue($row->resolved);
        $this->assertSame($g, $row->translation_group_id);
        $this->assertSame($tr, $row->tr_faq_id);
        $this->assertSame($en, $row->en_faq_id);
        $this->assertNull($row->de_faq_id);                       // DE yok → MISSING kalır
        $this->assertSame('MISSING', $row->de_status);
        $bad = FaqQualityAtlas::where('tr_slug', 'yok-boyle-bir-slug')->first();
        $this->assertFalse($bad->resolved);                       // tahmin yok
        $this->assertStringContainsString('TR SSS bulunamadı', $bad->unresolved_reason);
        $this->assertNull($bad->translation_group_id);
    }

    public function test_import_is_idempotent_and_never_touches_faqs(): void
    {
        $g = (string) Str::uuid();
        $this->faq('tr', $g, 'idem', $this->long());
        $this->faq('en', $g, 'idem-en', $this->long());
        $before = DB::table('faqs')->orderBy('id')->get()->map(fn ($r) => (array) $r)->all();
        $path = $this->dataset([$this->dsRow('idem')]);
        $sha = FaqAtlasImporter::checksum(file_get_contents($path));

        app(FaqAtlasImporter::class)->import($path, false, $sha);
        $first = FaqQualityAtlas::firstOrFail();
        $this->travel(5)->minutes();
        $r2 = app(FaqAtlasImporter::class)->import($path, false, $sha);

        $this->assertSame(0, $r2['inserted']);
        $this->assertSame(1, $r2['existing']);
        $this->assertSame(1, FaqQualityAtlas::count());
        $this->assertEquals($first->updated_at, FaqQualityAtlas::firstOrFail()->updated_at);
        $this->assertSame($before, DB::table('faqs')->orderBy('id')->get()->map(fn ($r) => (array) $r)->all());
    }

    public function test_checksum_mismatch_fails_before_writing(): void
    {
        $path = $this->dataset([$this->dsRow('x')]);
        try {
            app(FaqAtlasImporter::class)->import($path, false, str_repeat('0', 64));
            $this->fail('checksum mismatch must throw');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('checksum', $e->getMessage());
        }
        $this->assertSame(0, FaqQualityAtlas::count());
    }

    public function test_unknown_snapshot_without_checksum_fails(): void
    {
        $this->expectException(RuntimeException::class);
        app(FaqAtlasImporter::class)->import($this->dataset([$this->dsRow('x')]));
    }

    public function test_malformed_json_and_invalid_schema_fail_before_writing(): void
    {
        $path = storage_path('framework/testing/atlas-broken.json');
        @mkdir(dirname($path), 0777, true);
        file_put_contents($path, '{"schema_version": 1, "clusters": [');
        try {
            app(FaqAtlasImporter::class)->import($path, false, FaqAtlasImporter::checksum(file_get_contents($path)));
            $this->fail('malformed JSON must throw');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('JSON', $e->getMessage());
        }
        $bad = $this->dataset([$this->dsRow('ok'), $this->dsRow('bad', ['risk_level' => 'P9', 'tr_quality' => 'Z'])]);
        try {
            app(FaqAtlasImporter::class)->import($bad, false, FaqAtlasImporter::checksum(file_get_contents($bad)));
            $this->fail('invalid schema must throw');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('şeması geçersiz', $e->getMessage());
        }
        $this->assertSame(0, FaqQualityAtlas::count());
    }

    public function test_shipped_snapshot_is_immutable_and_schema_valid(): void
    {
        $path = resource_path('data/faq-atlas/atlas-2026-09-28.json');
        $this->assertSame(FaqAtlasImporter::KNOWN_CHECKSUMS['atlas-2026-09-28.json'], FaqAtlasImporter::checksum(file_get_contents($path)));
        $r = app(FaqAtlasImporter::class)->import($path, dryRun: true);   // checksum + şema; dry-run yazmaz
        $this->assertSame(675, $r['total']);
        $this->assertSame(0, FaqQualityAtlas::count());
        $raw = file_get_contents($path);
        $this->assertStringNotContainsString('scratchpad', $raw);
        $this->assertStringNotContainsString('AppData', $raw);
    }

    // ───────────── DASHBOARD / FILTERS ─────────────

    private function overview(array $filters = [], ?string $search = null): AtlasOverview
    {
        $w = new AtlasOverview();
        $w->tableFilters = $filters;
        $w->tableSearch = $search;

        return $w;
    }

    public function test_summary_counts_are_dynamic_and_default_to_latest_audit(): void
    {
        $this->cluster('p0-b', ['risk_level' => 'P0', 'tr_quality' => 'B', 'en_status' => 'EMPTY', 'parity_score' => 1]);
        $this->cluster('p1-a');
        $this->cluster('p2-d', ['risk_level' => 'P2', 'tr_quality' => 'D', 'de_status' => 'MISSING', 'parity_score' => 2], ['tr', 'en']);
        $this->cluster('old-audit', ['audit_label' => '2026-01-01']);

        $k = $this->overview()->kpis();
        $this->assertSame(3, $k['total']);
        $this->assertSame([1, 1, 0, 1], [$k['q_A'], $k['q_B'], $k['q_C'], $k['q_D']]);
        $this->assertSame([1, 1, 1], [$k['r_P0'], $k['r_P1'], $k['r_P2']]);
        $this->assertSame([1, 1, 1, 0], [$k['p_3'], $k['p_2'], $k['p_1'], $k['p_0']]);
        $this->assertSame(1, $k['en_EMPTY']);
        $this->assertSame(1, $k['de_MISSING']);
        $this->assertSame(0, $k['unresolved']);

        $old = $this->overview(['audit' => ['value' => '2026-01-01']])->kpis();
        $this->assertSame(1, $old['total']);
    }

    public function test_table_filters_and_search(): void
    {
        $p0 = $this->cluster('visa-p0', ['risk_level' => 'P0', 'tr_quality' => 'B', 'topic' => 'Visa', 'en_status' => 'EMPTY', 'parity_score' => 1,
            'proposed_batch' => 'A', 'chatbot_risk' => true, 'authoritative_internal_source' => '/tr/blog/guide']);
        $a = $this->cluster('work-a');
        $d = $this->cluster('money-d', ['risk_level' => 'P2', 'tr_quality' => 'D', 'topic' => 'Banking / Money Transfer', 'de_status' => 'BROKEN',
            'parity_score' => 2, 'proposed_batch' => 'C', 'external_source_needed' => true]);

        $this->list()->assertCanSeeTableRecords([$p0, $a, $d])
            ->filterTable('risk', ['P0'])->assertCanSeeTableRecords([$p0])->assertCanNotSeeTableRecords([$a, $d]);
        $this->list()->filterTable('quality', ['D'])->assertCanSeeTableRecords([$d])->assertCanNotSeeTableRecords([$p0, $a]);
        $this->list()->filterTable('topic', 'Visa')->assertCanSeeTableRecords([$p0])->assertCanNotSeeTableRecords([$a, $d]);
        $this->list()->filterTable('en_status', ['EMPTY'])->assertCanSeeTableRecords([$p0])->assertCanNotSeeTableRecords([$a, $d]);
        $this->list()->filterTable('de_status', ['BROKEN'])->assertCanSeeTableRecords([$d])->assertCanNotSeeTableRecords([$p0, $a]);
        $this->list()->filterTable('parity', ['3'])->assertCanSeeTableRecords([$a])->assertCanNotSeeTableRecords([$p0, $d]);
        $this->list()->filterTable('batch', ['A'])->assertCanSeeTableRecords([$p0])->assertCanNotSeeTableRecords([$a, $d]);
        $this->list()->filterTable('chatbot', true)->assertCanSeeTableRecords([$p0])->assertCanNotSeeTableRecords([$a, $d]);
        $this->list()->filterTable('guide', true)->assertCanSeeTableRecords([$p0])->assertCanNotSeeTableRecords([$a, $d]);
        $this->list()->filterTable('research', true)->assertCanSeeTableRecords([$d])->assertCanNotSeeTableRecords([$p0, $a]);
        $this->list()->filterTable('preset', 'broken_locale')->assertCanSeeTableRecords([$d])->assertCanNotSeeTableRecords([$p0, $a]);
        $this->list()->searchTable('money-d')->assertCanSeeTableRecords([$d])->assertCanNotSeeTableRecords([$p0, $a]);
        $this->list()->searchTable('https://applytogerman.com/tr/faq/is/visa-p0')->assertCanSeeTableRecords([$p0])->assertCanNotSeeTableRecords([$a, $d]);
        $this->list()->searchTable('Soru work-a')->assertCanSeeTableRecords([$a])->assertCanNotSeeTableRecords([$p0, $d]);
        // ?batch=A kısayolu
        $this->list(['batch' => 'A'])->assertCanSeeTableRecords([$p0])->assertCanNotSeeTableRecords([$a, $d]);
    }

    public function test_audit_snapshot_filter_switches_snapshot(): void
    {
        $new = $this->cluster('same-slug');
        $old = FaqQualityAtlas::create(array_merge($new->only(['translation_group_id', 'tr_slug', 'topic', 'risk_level', 'tr_quality', 'tr_status',
            'en_status', 'de_status', 'parity_score', 'proposed_batch', 'resolved']), ['audit_label' => '2026-01-01', 'audited_at' => '2026-01-01 10:00:00']));

        $this->list()->assertCanSeeTableRecords([$new])->assertCanNotSeeTableRecords([$old]);
        $this->list()->filterTable('audit', '2026-01-01')->assertCanSeeTableRecords([$old])->assertCanNotSeeTableRecords([$new]);
    }

    public function test_pagination_defaults_to_50(): void
    {
        for ($i = 0; $i < 55; $i++) {
            $this->cluster("bulk-{$i}", ['priority_score' => 1000 - $i]);
        }
        $this->assertCount(50, $this->list()->instance()->getTableRecords());
    }

    public function test_list_has_no_n_plus_one(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $this->cluster("nq-{$i}");
        }
        DB::enableQueryLog();
        $rows = FaqQualityAtlas::query()->withLive()->with(['tr' => fn ($q) => $q->select(['faqs.id', 'faqs.translation_group_id', 'faqs.locale', 'faqs.question'])])->get();
        $rows->each(fn ($r) => [$r->tr?->question, $r->en_live, $r->review_needed]);
        $this->assertCount(20, $rows);
        $this->assertLessThanOrEqual(2, count(DB::getQueryLog()));
    }

    // ───────────── LIVE STATUS ─────────────

    public function test_live_status_php_and_sql_agree(): void
    {
        $g = (string) Str::uuid();
        $this->faq('tr', $g, 'ls-tr', $this->long());                                          // CONTENT
        $this->faq('en', $g, 'ls-en', '<p>Kısa cevap.</p>');                                    // THIN
        $this->faq('de', $g, 'ls-de', '<p>   </p>', ['has_answer' => true]);                   // EMPTY
        $row = FaqQualityAtlas::create(['audit_label' => self::AUDIT, 'translation_group_id' => $g, 'tr_slug' => 'ls-tr', 'topic' => 'X',
            'risk_level' => 'P2', 'tr_quality' => 'A', 'tr_status' => 'COMPLETE', 'en_status' => 'COMPLETE', 'de_status' => 'COMPLETE',
            'parity_score' => 3, 'proposed_batch' => 'OUTSIDE_BATCH', 'audited_at' => self::AUDITED_AT, 'resolved' => true]);
        $live = FaqQualityAtlas::query()->withLive()->findOrFail($row->id);

        $this->assertSame('CONTENT', $live->tr_live);
        $this->assertSame('THIN', $live->en_live);
        $this->assertSame('EMPTY', $live->de_live);
        foreach (['tr', 'en', 'de'] as $l) {
            $this->assertSame(LiveStatus::classify(Faq::where('translation_group_id', $g)->where('locale', $l)->first()), $live->{"{$l}_live"});
        }
        DB::table('faqs')->where('slug', 'ls-en')->update(['is_published' => false]);
        DB::table('faqs')->where('slug', 'ls-de')->delete();
        $live = FaqQualityAtlas::query()->withLive()->findOrFail($row->id);
        $this->assertSame('UNPUBLISHED', $live->en_live);
        $this->assertSame('MISSING', $live->de_live);
        $this->assertSame('MISSING', LiveStatus::classify(null));
        $this->assertSame('EMPTY', LiveStatus::classify(Faq::where('slug', 'ls-tr')->first()->forceFill(['has_answer' => false])));
    }

    // ───────────── DRIFT ─────────────

    public function test_drift_empty_to_content_updated_after_audit_and_missing_locale_added(): void
    {
        $clean = $this->cluster('clean');
        $emptyNowContent = $this->cluster('was-empty', ['en_status' => 'EMPTY']);                 // canlı EN içerik var
        $updated = $this->cluster('updated');
        DB::table('faqs')->where('slug', 'updated-de')->update(['updated_at' => '2026-09-29 09:00:00']);
        $missing = $this->cluster('was-missing', ['de_status' => 'MISSING'], ['tr', 'en']);

        $ids = fn () => FaqQualityAtlas::query()->reviewNeeded()->pluck('tr_slug')->sort()->values()->all();
        $this->assertSame(['updated', 'was-empty'], $ids());
        $this->faq('de', $missing->translation_group_id, 'was-missing-de', '<p>x</p>');           // eksik dil eklendi
        $this->assertSame(['updated', 'was-empty', 'was-missing'], $ids());
        $this->assertFalse((bool) FaqQualityAtlas::query()->withLive()->findOrFail($clean->id)->review_needed);
        $this->assertSame(3, $this->overview()->kpis()['review']);
        $this->list()->filterTable('review', true)->assertCanSeeTableRecords([$emptyNowContent, $updated])->assertCanNotSeeTableRecords([$clean]);
        // denetim kararları değişmez
        $this->assertSame('EMPTY', $emptyNowContent->fresh()->en_status);
    }

    // ───────────── DETAIL ─────────────

    public function test_detail_shows_three_locales_missing_locale_issues_guide_strategy_batch(): void
    {
        $c = $this->cluster('detail-x', ['tr_quality' => 'B', 'tr_issues' => ['Remonstration güncel gibi anlatılıyor'],
            'authoritative_internal_source' => '/tr/blog/internship-in-germany-with-b1-b2-german', 'translation_strategy' => 'REWRITE_FROM_VERIFIED_FACTS',
            'proposed_batch' => 'A', 'de_status' => 'MISSING'], ['tr', 'en']);

        $this->actingAs($this->admin())->get('/admin/ops/faq-atlas/cluster/'.$c->id)->assertOk()
            ->assertSee('Soru detail-x')->assertSee('Soru detail-x-en')
            ->assertSee('— kayıt yok —')
            ->assertSee('Remonstration güncel gibi anlatılıyor')
            ->assertSee('/tr/blog/internship-in-germany-with-b1-b2-german')
            ->assertSee('REWRITE FROM VERIFIED FACTS')
            ->assertSee('/tr/faq/is/detail-x', false)
            ->assertDontSee('scratchpad');
    }

    // ───────────── CSV ─────────────

    public function test_csv_export_respects_filters_and_is_admin_only(): void
    {
        $this->cluster('csv-p0', ['risk_level' => 'P0']);
        $this->cluster('csv-p1');
        $this->cluster('csv-old', ['audit_label' => '2026-01-01', 'risk_level' => 'P0']);

        $res = $this->actingAs($this->admin())->get('/admin/ops/faq-atlas/export?'.http_build_query(['filters' => ['risk' => ['values' => ['P0']]]]));
        $res->assertOk();
        $csv = $res->streamedContent();
        $this->assertStringContainsString('csv-p0', $csv);
        $this->assertStringNotContainsString('csv-p1', $csv);
        $this->assertStringNotContainsString('csv-old', $csv);            // en güncel anlık görüntü
        $this->assertStringNotContainsString('Almanya\'da öğrenci olarak çalışma kuralları', $csv);   // gövde yok

        $all = $this->get('/admin/ops/faq-atlas/export?search=csv-p1')->streamedContent();
        $this->assertStringContainsString('csv-p1', $all);
        $this->assertStringNotContainsString('csv-p0', $all);

        $this->actingAs(User::factory()->create(['is_editor' => true]))->get('/admin/ops/faq-atlas/export')->assertForbidden();
    }

    public function test_filters_service_compacts_and_ignores_blank_values(): void
    {
        $this->assertSame(['risk' => ['values' => ['P0']]], AtlasFilters::compact(['risk' => ['values' => ['P0']], 'topic' => ['value' => null], 'x' => 'y']));
    }
}

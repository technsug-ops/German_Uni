<?php

namespace Tests\Feature;

use App\Filament\Resources\Programs\Pages\EditProgram;
use App\Models\Program;
use App\Models\ProgramVerification;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Program Source & Verification Layer V1: import kaynağı ≠ resmî doğrulama; alan bazında durum; UNKNOWN ≠ hayır;
 * sync doğrulamayı ezmez, değişen doğrulanmış alan NEEDS_REVIEW olur; yalnız tam admin yazar; sayfada yalnız
 * doğrulanmış bilgi; Phase 2 (noindex/sitemap/locale/deadline) ve title/slug/canonical/hreflang korunur.
 */
class ProgramVerificationLayerTest extends TestCase
{
    use RefreshDatabase;

    private const DESC_EN = 'The programme trains engineers in wind energy, photovoltaics, grid integration and storage systems through lab projects and an industry semester.';

    private int $uni;

    protected function setUp(): void
    {
        parent::setUp();
        $city = DB::table('cities')->insertGetId(['name_de' => 'Prüfstadt', 'name_tr' => 'Kontrol şehri', 'slug' => 'pv-stadt', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        $this->uni = DB::table('universities')->insertGetId(['name_de' => 'Prüf Universität', 'name_tr' => 'Kontrol Üni', 'slug' => 'pv-uni', 'city_id' => $city,
            'is_active' => true, 'is_uni_assist_member' => true, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function program(string $slug = 'pv-prog', array $attrs = []): Program
    {
        DB::table('programs')->insert(array_merge([
            'name_de' => 'Erneuerbare Energien', 'slug' => $slug, 'degree' => 'master', 'language' => 'en', 'university_id' => $this->uni,
            'is_active' => true, 'source' => 'partner', 'description_en' => self::DESC_EN, 'application_deadline_winter' => now()->addMonths(5)->format('Y-m-d'),
            'created_at' => now(), 'updated_at' => now(),
        ], $attrs));

        return Program::where('slug', $slug)->firstOrFail();
    }

    private function verify(Program $p, string $field, array $attrs = []): ProgramVerification
    {
        return ProgramVerification::create(array_merge([
            'program_id' => $p->id, 'field' => $field, 'status' => ProgramVerification::VERIFIED,
            'source_url' => 'https://www.pruef-uni.de/master/erneuerbare-energien', 'evidence' => 'Quote from the official page', 'checked_at' => now(),
        ], $attrs))->fresh();
    }

    private function page(string $loc, string $slug = 'pv-prog'): string
    {
        return $this->get("/{$loc}/programs/{$slug}")->assertOk()->getContent();
    }

    /* ------------------------------------------------------------ model / status rules */

    public function test_legacy_program_without_new_fields_still_works(): void
    {
        $p = $this->program();
        $this->assertNull($p->official_program_url);
        $this->assertNull($p->uni_assist_required);
        $this->assertFalse($p->isThin());
        $this->assertSame(['verified' => 0, 'total' => 8, 'needs_review' => 0, 'conflict' => 0], $p->verificationSummary());
        foreach (['tr', 'en', 'de'] as $loc) {
            $this->assertStringNotContainsString('data-official-program-url', $this->page($loc));
        }
    }

    public function test_unknown_is_never_shown_as_no_and_university_flag_is_not_copied(): void
    {
        $this->program();
        $en = $this->page('en');
        $this->assertStringNotContainsString('Applications go through Uni-Assist', $en);
        $this->assertStringContainsString('University is a uni-assist member', $en);
        $this->assertStringContainsString('depends on the programme and your applicant group', $en);
        $this->assertStringNotContainsString('data-verified-routes', $en);
        $this->assertDoesNotMatchRegularExpression('/uni-assist[^<]{0,40}(:\s*)?(No|Hayır|Nein)\b/u', strip_tags($en));
    }

    public function test_adding_a_source_url_does_not_verify_anything(): void
    {
        $p = $this->program('pv-url', ['official_program_url' => 'https://www.pruef-uni.de/master/erneuerbare-energien']);
        $v = ProgramVerification::create(['program_id' => $p->id, 'field' => 'deadline', 'source_url' => 'https://www.pruef-uni.de/x']);

        $this->assertSame(ProgramVerification::UNVERIFIED, $v->fresh()->status);
        $this->assertNull($v->fresh()->verified_at);
        $this->assertFalse($p->fresh()->hasVerifiedOfficialUrl());
        $this->assertStringNotContainsString('data-official-program-url', $this->page('en', 'pv-url'));
    }

    public function test_verified_requires_source_and_evidence(): void
    {
        $p = $this->program();
        $this->expectException(\InvalidArgumentException::class);
        ProgramVerification::create(['program_id' => $p->id, 'field' => 'language', 'status' => ProgramVerification::VERIFIED, 'source_url' => 'https://x.de']);
    }

    public function test_verifying_one_field_does_not_spread_to_others(): void
    {
        $p = $this->program();
        $this->verify($p, 'language', ['source_value' => 'en']);
        $p->refresh();

        $this->assertSame(1, $p->verificationSummary()['verified']);
        $this->assertNull($p->verifiedRecord('deadline'));
        $this->assertNull($p->verifiedRecord('identity'));
        $this->assertSame(1, ProgramVerification::count());
    }

    public function test_scalar_source_value_different_from_program_becomes_conflict(): void
    {
        $p = $this->program();
        $v = $this->verify($p, 'language', ['source_value' => 'de']);
        $this->assertSame(ProgramVerification::CONFLICT, $v->status);
        $this->assertNull($v->verified_at);
    }

    public function test_applicant_group_and_term_context_is_kept_per_scope(): void
    {
        $p = $this->program();
        $this->verify($p, 'application_method', ['applicant_group' => 'non_eu', 'source_value' => 'vpd_then_portal', 'term' => 'WS 2027/28']);
        $this->verify($p, 'application_method', ['applicant_group' => 'german_degree', 'source_value' => 'direct_portal']);

        $rows = $p->verifications()->orderBy('applicant_group')->get();
        $this->assertSame(['german_degree', 'non_eu'], $rows->pluck('applicant_group')->all());
        $this->assertSame('WS 2027/28', $rows->firstWhere('applicant_group', 'non_eu')->term);
        $this->assertTrue($rows->every(fn ($r) => $r->status === ProgramVerification::VERIFIED), 'gruba özgü yol program özetiyle kıyaslanıp çelişki sayılmamalı');

        $this->expectException(\Illuminate\Database\QueryException::class);   // aynı kapsam ikinci kez → unique
        ProgramVerification::create(['program_id' => $p->id, 'field' => 'application_method', 'applicant_group' => 'non_eu', 'term' => 'WS 2027/28']);
    }

    /* ------------------------------------------------------------ sync safety */

    public function test_sync_of_unrelated_or_identical_values_keeps_verification_and_date(): void
    {
        $p = $this->program();
        $v = $this->verify($p, 'deadline');
        $verifiedAt = $v->verified_at->toDateTimeString();
        $this->travel(2)->days();

        // partner/DAAD importları Eloquent update kullanır: ilgisiz alan değişti + aynı deadline tekrar geldi
        $p->update(['description_en' => self::DESC_EN.' Updated.', 'application_deadline_winter' => $p->application_deadline_winter->format('Y-m-d'), 'last_synced_at' => now()]);
        $this->artisan('programs:verification-reconcile')->assertSuccessful();

        $v->refresh();
        $this->assertSame(ProgramVerification::VERIFIED, $v->status);
        $this->assertSame($verifiedAt, $v->verified_at->toDateTimeString(), 'sync verified_at yenilememeli');
    }

    public function test_changed_verified_value_becomes_needs_review_via_eloquent_and_query_builder(): void
    {
        $p = $this->program();
        $v = $this->verify($p, 'deadline');
        $at = $v->verified_at->toDateTimeString();

        $p->update(['application_deadline_winter' => now()->addMonths(7)->format('Y-m-d')]);   // Eloquent (import)
        $v->refresh();
        $this->assertSame(ProgramVerification::NEEDS_REVIEW, $v->status);
        $this->assertSame($at, $v->verified_at->toDateTimeString(), 'eski doğrulama tarihi korunur');
        $this->assertNotNull($v->review_reason);

        $q = $this->program('pv-qb');
        $w = $this->verify($q, 'tuition');
        DB::table('programs')->where('id', $q->id)->update(['tuition_fee_eur' => 1500]);       // query builder (rollover/parse komutları)
        $this->assertSame(ProgramVerification::VERIFIED, $w->fresh()->status);
        $this->artisan('programs:verification-reconcile')->assertSuccessful();
        $this->assertSame(ProgramVerification::NEEDS_REVIEW, $w->fresh()->status);
    }

    /* ------------------------------------------------------------ authorization */

    public function test_only_full_admin_can_write_verifications(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_editor' => false]);
        $editor = User::factory()->create(['is_admin' => false, 'is_editor' => true]);
        $p = $this->program();
        $v = ProgramVerification::create(['program_id' => $p->id, 'field' => 'language']);

        $this->assertTrue(Gate::forUser($admin)->allows('create', ProgramVerification::class));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $v));
        $this->assertFalse(Gate::forUser($editor)->allows('create', ProgramVerification::class));
        $this->assertFalse(Gate::forUser($editor)->allows('update', $v));
        $this->assertFalse(Gate::forUser($editor)->allows('delete', $v));
        $this->assertTrue(Gate::forUser($editor)->allows('viewAny', ProgramVerification::class));
    }

    public function test_editor_saving_the_program_form_cannot_write_verification_fields_and_viewing_writes_nothing(): void
    {
        $editor = User::factory()->create(['is_admin' => false, 'is_editor' => true]);
        $p = $this->program();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($editor);

        $before = DB::table('program_verifications')->count();
        Livewire::test(EditProgram::class, ['record' => $p->getRouteKey()])
            ->fillForm(['official_program_url' => 'https://evil.example/fake', 'uni_assist_required' => 'no'])
            ->call('save');

        $p->refresh();
        $this->assertNull($p->official_program_url);
        $this->assertNull($p->uni_assist_required);
        $this->assertSame($before, DB::table('program_verifications')->count(), 'sayfayı açmak/kaydetmek doğrulama kaydı yazmamalı');
    }

    /* ------------------------------------------------------------ public page */

    public function test_official_link_only_after_identity_verified_and_dates_are_field_specific(): void
    {
        $p = $this->program('pv-link', ['official_program_url' => 'https://www.pruef-uni.de/master/erneuerbare-energien']);
        $this->verify($p, 'deadline');
        $en = $this->page('en', 'pv-link');
        $this->assertStringNotContainsString('data-official-program-url', $en, 'kimlik doğrulanmadan bağlantı gösterilmemeli');
        $this->assertStringContainsString('data-verified-field="deadline"', $en);
        $this->assertStringNotContainsString('data-verified-field="tuition"', $en);

        $this->verify($p, 'identity');
        foreach (['tr' => 'Resmî program sayfası', 'en' => 'Official programme page', 'de' => 'Offizielle Studiengangsseite'] as $loc => $label) {
            $html = $this->page($loc, 'pv-link');
            $this->assertStringContainsString('data-official-program-url', $html);
            $this->assertStringContainsString($label, $html);
            $this->assertStringContainsString('href="https://www.pruef-uni.de/master/erneuerbare-energien"', $html);
        }
    }

    public function test_verified_routes_render_per_applicant_group_in_page_language(): void
    {
        $p = $this->program('pv-routes', ['application_method' => 'multiple']);
        $this->verify($p, 'application_method', ['applicant_group' => 'non_eu', 'source_value' => 'vpd_then_portal']);
        $this->verify($p, 'application_method', ['applicant_group' => 'eu', 'source_value' => 'direct_portal']);
        ProgramVerification::create(['program_id' => $p->id, 'field' => 'application_method', 'applicant_group' => 'international', 'source_value' => 'uni_assist', 'status' => ProgramVerification::NEEDS_REVIEW]);

        $de = $this->page('de', 'pv-routes');
        $this->assertStringContainsString('Bewerbende aus Nicht-EU-Staaten', $de);
        $this->assertStringContainsString('VPD von uni-assist, danach Bewerbungsportal der Hochschule', $de);
        $this->assertStringNotContainsString('Internationale Bewerbende', $de, 'NEEDS_REVIEW kayıt sayfada gösterilmemeli');
        $this->assertStringNotContainsString('uni-assist-Mitglied', $de, 'doğrulanmış yol varken üniversite bayrağı gösterilmez');
        $tr = $this->page('tr', 'pv-routes');
        $this->assertStringContainsString('AB dışı adaylar', $tr);
    }

    public function test_conflict_and_needs_review_values_are_never_shown_as_verified(): void
    {
        $p = $this->program('pv-conf', ['tuition_fee_eur' => 450, 'language' => 'both']);
        ProgramVerification::create(['program_id' => $p->id, 'field' => 'tuition', 'status' => ProgramVerification::CONFLICT, 'source_value' => 'tuition=0; semester_contribution=452.94',
            'source_url' => 'https://www.pruef-uni.de/fees', 'evidence' => 'keine Studiengebühren']);
        $this->verify($p, 'language', ['source_value' => 'de']);                                              // → modelce CONFLICT
        ProgramVerification::create(['program_id' => $p->id, 'field' => 'deadline', 'status' => ProgramVerification::NEEDS_REVIEW, 'source_value' => 'WS: 15.07.']);

        foreach (['tr' => 'resmî program sayfasıyla çelişiyor', 'en' => 'conflicts with the official programme page', 'de' => 'widerspricht der offiziellen Studiengangsseite'] as $loc => $warn) {
            $html = $this->page($loc, 'pv-conf');
            $this->assertStringContainsString('data-conflict-field="tuition"', $html);
            $this->assertStringContainsString('data-conflict-field="language"', $html);
            $this->assertStringContainsString($warn, $html);
            $this->assertStringNotContainsString('data-verified-field', $html, 'çelişkili/incelemedeki alan doğrulanmış gibi etiketlenmemeli');
        }
    }

    public function test_identity_conflict_suppresses_all_verified_labels_and_link(): void
    {
        $p = $this->program('pv-idc', ['official_program_url' => 'https://www.other-uni.de/master', 'application_method' => 'multiple']);
        ProgramVerification::create(['program_id' => $p->id, 'field' => 'identity', 'status' => ProgramVerification::CONFLICT, 'source_value' => 'Programm gehört zu einer anderen Hochschule',
            'source_url' => 'https://www.other-uni.de/master', 'evidence' => 'Other university']);
        $this->verify($p, 'deadline');
        $this->verify($p, 'tuition');
        $this->verify($p, 'application_method', ['applicant_group' => 'non_eu', 'source_value' => 'uni_assist']);

        $p->refresh();
        $this->assertNull($p->verifiedRecord('deadline'));
        $this->assertTrue($p->verifiedRecords('application_method')->isEmpty());
        $this->assertFalse($p->hasVerifiedOfficialUrl());
        foreach (['tr', 'en', 'de'] as $loc) {
            $html = $this->page($loc, 'pv-idc');
            foreach (['data-official-program-url', 'data-verified-field="deadline"', 'data-verified-field="tuition"', 'data-verified-routes'] as $marker) {
                $this->assertStringNotContainsString($marker, $html, "{$loc}: kimlik çelişkisinde {$marker} gösterilmemeli");
            }
        }
    }

    public function test_query_builder_change_hides_old_verified_label_even_before_reconcile(): void
    {
        $p = $this->program('pv-stale', ['tuition_fee_eur' => 0, 'official_program_url' => 'https://www.pruef-uni.de/m']);
        $this->verify($p, 'identity');
        $this->verify($p, 'tuition');
        $this->verify($p, 'deadline');
        $this->assertStringContainsString('data-verified-field="tuition"', $this->page('en', 'pv-stale'));

        DB::table('programs')->where('id', $p->id)->update(['tuition_fee_eur' => 1500, 'application_deadline_winter' => now()->addYear()->format('Y-m-d')]);
        // reconcile ÇALIŞMADI: kayıtlar hâlâ VERIFIED ama değer değişti
        $this->assertSame(3, ProgramVerification::where('status', ProgramVerification::VERIFIED)->count());

        $html = $this->page('en', 'pv-stale');
        $this->assertStringNotContainsString('data-verified-field="tuition"', $html);
        $this->assertStringNotContainsString('data-verified-field="deadline"', $html);
        $this->assertStringContainsString('data-official-program-url', $html, 'değişmeyen kimlik doğrulaması normal gösterilir');
    }

    public function test_seo_head_unchanged_and_phase2_rules_hold_with_verifications(): void
    {
        $p = $this->program('pv-seo', ['official_program_url' => 'https://www.pruef-uni.de/x', 'application_deadline_winter' => now()->subDays(20)->format('Y-m-d')]);
        $this->verify($p, 'identity');
        $thin = $this->program('pv-thin', ['description_en' => 'Erneuerbare Energien (M.Sc.)', 'official_program_url' => 'https://www.pruef-uni.de/y']);
        $this->verify($thin, 'identity');

        foreach (['tr', 'en', 'de'] as $loc) {
            $html = $this->page($loc, 'pv-seo');
            $this->assertMatchesRegularExpression('#<title>\s*Erneuerbare Energien — Master @ #u', $html);
            $this->assertMatchesRegularExpression('#<link rel="canonical" href="[^"]*/'.$loc.'/programs/pv-seo"#', $html);
            foreach (['tr', 'en', 'de'] as $l2) {
                $this->assertMatchesRegularExpression('#hreflang="'.$l2.'" href="[^"]*/'.$l2.'/programs/pv-seo"#', $html);
            }
            $this->assertStringNotContainsString('noindex', $html);
            $this->assertStringContainsString('data-deadline-state="past"', $html, 'geçmiş tarih "son bilinen" kalmalı');
            // doğrulama ince sayfayı indekslenebilir yapmaz
            $this->assertStringContainsString('content="noindex, follow"', $this->page($loc, 'pv-thin'));
            $xml = $this->get("/sitemap-{$loc}.xml")->assertOk()->getContent();
            $this->assertStringContainsString("/{$loc}/programs/pv-seo<", $xml);
            $this->assertStringNotContainsString("/{$loc}/programs/pv-thin<", $xml);
        }
        $this->assertSame('pv-seo', $p->fresh()->slug);
    }

    /* ------------------------------------------------------------ import command */

    private function match(Program $p, array $override = []): array
    {
        return array_merge(['source' => 'partner', 'external_id_field' => 'partner_id', 'external_id' => $p->partner_id,
            'name_de' => $p->name_de, 'degree' => $p->degree, 'university' => 'Prüf Universität'], $override);
    }

    private function importFile(array $programs): string
    {
        $file = 'storage/framework/testing-pv-import-'.uniqid().'.json';
        @mkdir(base_path('storage/framework'), 0777, true);
        file_put_contents(base_path($file), json_encode(['programs' => $programs]));

        return $file;
    }

    private function identityRow(): array
    {
        return ['field' => 'identity', 'applicant_group' => '', 'term' => '', 'status' => 'verified', 'source_value' => 'Erneuerbare Energien — M.Sc.',
            'source_url' => 'https://www.pruef-uni.de/m', 'evidence' => 'Master Erneuerbare Energien', 'checked_at' => '2026-10-02'];
    }

    public function test_import_matches_by_source_and_external_id_never_by_local_id_and_skips_ambiguous(): void
    {
        $ok = $this->program('pv-m-ok', ['partner_id' => 'ext-ok-1']);
        $other = $this->program('pv-m-other', ['partner_id' => 'ext-other-1']);
        $dupA = $this->program('pv-m-dup-a', ['partner_id' => 'ext-dup']);
        $this->program('pv-m-dup-b', ['partner_id' => 'ext-dup']);
        $renamed = $this->program('pv-m-renamed', ['partner_id' => 'ext-renamed', 'name_de' => 'Ganz anderer Studiengang']);

        $file = $this->importFile([
            // yanlış local_id + farklı slug verilse bile dış kimlik doğru programı bulur
            ['match' => $this->match($ok), 'local_id' => $other->id, 'slug' => 'prod-slug-differs', 'set' => [], 'verifications' => [$this->identityRow()]],
            // aynı dış kimlik iki kayıtta → belirsiz → yazılmaz
            ['match' => $this->match($dupA), 'local_id' => $dupA->id, 'slug' => 'x', 'set' => [], 'verifications' => [$this->identityRow()]],
            // dış kimlik eşleşiyor ama ad farklı → kimlik şüpheli → yazılmaz
            ['match' => $this->match($renamed, ['name_de' => 'Erneuerbare Energien']), 'local_id' => $renamed->id, 'slug' => 'x', 'set' => [], 'verifications' => [$this->identityRow()]],
            // eşleştirme anahtarı yok (yalnız id) → yazılmaz
            ['local_id' => $other->id, 'slug' => 'pv-m-other', 'set' => [], 'verifications' => [$this->identityRow()]],
        ]);
        $this->artisan('programs:verification-import', ['file' => $file, '--dry-run' => true])->assertSuccessful();
        $this->assertSame(0, ProgramVerification::count(), 'dry-run yazmaz');
        $this->artisan('programs:verification-import', ['file' => $file])->assertSuccessful();

        $this->assertSame([$ok->id], ProgramVerification::pluck('program_id')->all());
        @unlink(base_path($file));
    }

    public function test_import_is_idempotent_and_never_overwrites_existing_program_data(): void
    {
        $p = $this->program('pv-import', ['application_method' => 'direct_portal', 'tuition_fee_eur' => 450, 'partner_id' => 'ext-import-1']);
        $file = 'storage/framework/testing-pv-import.json';
        @mkdir(base_path('storage/framework'), 0777, true);
        file_put_contents(base_path($file), json_encode(['programs' => [[
            'match' => $this->match($p), 'local_id' => $p->id, 'slug' => 'pv-import',
            'set' => ['official_program_url' => 'https://www.pruef-uni.de/m', 'application_method' => 'uni_assist', 'tuition_fee_eur' => 0],
            'verifications' => [
                ['field' => 'identity', 'applicant_group' => '', 'term' => '', 'status' => 'verified', 'source_value' => 'Erneuerbare Energien — M.Sc.', 'source_url' => 'https://www.pruef-uni.de/m', 'evidence' => 'Master Erneuerbare Energien', 'checked_at' => '2026-10-02'],
                ['field' => 'tuition', 'applicant_group' => '', 'term' => '', 'status' => 'conflict', 'source_value' => 'tuition=0; semester_contribution=452', 'source_url' => 'https://www.pruef-uni.de/m', 'evidence' => 'keine Studiengebühren', 'checked_at' => '2026-10-02', 'review_reason' => 'katkı payı yanlış alanda'],
            ],
        ]]]));

        $this->artisan('programs:verification-import', ['file' => $file])->assertSuccessful();
        $this->artisan('programs:verification-import', ['file' => $file])->assertSuccessful();

        $p->refresh();
        $this->assertSame('https://www.pruef-uni.de/m', $p->official_program_url, 'boş yeni alan doldurulur');
        $this->assertSame('direct_portal', $p->application_method, 'dolu alan ezilmez');
        $this->assertEquals(450, $p->tuition_fee_eur, 'mevcut program verisi import ile değişmez');
        $this->assertSame(2, ProgramVerification::where('program_id', $p->id)->count(), 'tekrar çalıştırma duplicate üretmez');
        $this->assertSame(ProgramVerification::CONFLICT, ProgramVerification::where('program_id', $p->id)->where('field', 'tuition')->value('status'));
        @unlink(base_path($file));
    }
}

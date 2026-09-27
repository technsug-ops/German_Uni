<?php

namespace Tests\Feature;

use App\Console\Commands\DaadScholarshipsSync;
use App\Models\Scholarship;
use App\Services\DaadScholarshipsClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * Content Truth Batch 1B: BAföG kaydında (sap_objid 20000162) DAAD senkronu ülke listesini bağlamamalı ve
 * düzeltilmiş uygunluk metnini ezmemeli; diğer burslarda origin senkronu aynen çalışmalı.
 */
class DaadScholarshipsSyncCuratedEligibilityTest extends TestCase
{
    use RefreshDatabase;

    private const BAFOG = 20000162;
    private const OTHER = 50000001;

    private function feed(array $otherOrigins = [1, 2, 3], string $bafogName = 'Bundesministerium für Bildung und Forschung: Bundesausbildungsförderungsgesetz (BAföG)', bool $includeGone = true): array
    {
        $scholarships = [
            ['sapObjid' => self::BAFOG, 'id' => 164, 'nameDe' => $bafogName, 'nameEn' => str_replace('Bundesausbildungsförderungsgesetz', 'Federal Training Assistance Act', $bafogName),
                'introduction' => ['de' => 'Mit dem BAföG …', 'en' => 'BAföG …'], 'qDe' => 'bafög afghanistan albanien', 'qEn' => 'bafög afghanistan albania', 'origin' => [1, 2, 3]],
            ['sapObjid' => self::OTHER, 'id' => 999, 'nameDe' => 'Ein anderes Stipendium', 'nameEn' => 'Another scholarship',
                'introduction' => ['en' => 'Other …'], 'qDe' => 'anderes', 'qEn' => 'other', 'origin' => $otherOrigins],
        ];
        if ($includeGone) {
            $scholarships[] = ['sapObjid' => 50000002, 'id' => 998, 'nameEn' => 'Soon removed', 'origin' => [1]];
        }

        return [
            'origins' => [['id' => 1, 'nameEn' => 'Afghanistan'], ['id' => 2, 'nameEn' => 'Albania'], ['id' => 3, 'nameEn' => 'Türkiye']],
            'statuses' => [], 'subjectGroups' => [], 'intentions' => [], 'deadlines' => [],
            'scholarships' => $scholarships,
        ];
    }

    private function sync(array $feed): void
    {
        $client = \Mockery::mock(DaadScholarshipsClient::class);
        $client->shouldReceive('fetchAll')->andReturn($feed);
        $this->app->instance(DaadScholarshipsClient::class, $client);
        $this->artisan('daad:scholarships:sync', ['--no-scout' => true])->assertSuccessful();
    }

    private function origins(int $sap): array
    {
        return Scholarship::where('sap_objid', $sap)->first()->origins()->pluck('scholarship_origins_lookup.id')->sort()->values()->all();
    }

    public function test_identity_constant_targets_only_bafog(): void
    {
        $this->assertSame([self::BAFOG => 'BAföG'], DaadScholarshipsSync::CURATED_ELIGIBILITY);
    }

    public function test_bafog_origins_stay_empty_and_other_scholarship_syncs_normally(): void
    {
        $this->sync($this->feed());
        $this->assertSame([], $this->origins(self::BAFOG), 'A) BAföG: no country list attached');
        $this->assertNull(Scholarship::where('sap_objid', self::BAFOG)->first()->q_en_json, 'A) BAföG: DAAD keyword blob not written');
        $this->assertSame([1, 2, 3], $this->origins(self::OTHER), 'B) other scholarship: origins synced');
        $this->assertSame('other', Scholarship::where('sap_objid', self::OTHER)->first()->q_en_json, 'B) other scholarship: q fields synced');

        // B) origins change upstream -> other scholarship follows; BAföG still empty
        $this->sync($this->feed([2], includeGone: false));
        $this->assertSame([2], $this->origins(self::OTHER));
        $this->assertSame([], $this->origins(self::BAFOG));
        // removed_at behaviour unchanged: record missing from the feed is soft-removed, curated one is not
        $this->assertNotNull(Scholarship::where('sap_objid', 50000002)->first()->removed_at);
        $this->assertNull(Scholarship::where('sap_objid', self::BAFOG)->first()->removed_at);
    }

    public function test_cleanup_then_resync_keeps_origins_empty_and_curated_text(): void
    {
        $this->sync($this->feed());
        $s = Scholarship::where('sap_objid', self::BAFOG)->first();
        // C) simulate the current wrong production state
        $s->origins()->sync([1, 2, 3]);
        DB::table('scholarships')->where('id', $s->id)->update([
            'q_tr_json' => json_encode("Türkiye dahil, vatandaşlarının BAföG'den faydalanabileceği ülkeler listesi oldukça kapsamlıdır: Afganistan …", JSON_UNESCAPED_UNICODE),
            'q_en_json' => json_encode('federal training assistance act (bafög) afghanistan albania'),
            'q_de_json' => json_encode('bundesausbildungsförderungsgesetz (bafög) afghanistan albanien'),
        ]);

        $migration = require database_path('migrations/2026_09_27_000400_content_truth_batch1b_bafog_scholarship_eligibility.php');
        $migration->up();
        $s->refresh();
        $this->assertSame([], $this->origins(self::BAFOG), 'C) cleanup removes origins');
        $this->assertStringContainsString('§ 8 BAföG', $s->q_tr_json);
        $this->assertStringContainsString('Section 8', $s->q_en_json);
        $this->assertStringContainsString('§ 8 BAföG', $s->q_de_json);
        foreach ([$s->q_tr_json, $s->q_en_json, $s->q_de_json] as $txt) {
            $this->assertStringNotContainsStringIgnoringCase('afghanistan', $txt);
            $this->assertStringNotContainsString('ülkeler listesi', $txt);
        }
        $after = DB::table('scholarships')->where('id', $s->id)->first();

        // idempotent second run
        sleep(1);
        $migration->up();
        $this->assertEquals($after, DB::table('scholarships')->where('id', $s->id)->first(), 'migration second run is a no-op');

        // C) sync again -> origins do NOT return, curated text is NOT overwritten
        $this->sync($this->feed());
        $s->refresh();
        $this->assertSame([], $this->origins(self::BAFOG));
        $this->assertStringContainsString('Section 8', $s->q_en_json);
        $this->assertStringContainsString('§ 8 BAföG', $s->q_de_json);
        $this->assertStringContainsString('§ 8 BAföG', $s->q_tr_json);
    }

    public function test_unexpected_name_freezes_curated_record_instead_of_falling_back_to_normal_sync(): void
    {
        // curated state in place (origins empty, curated eligibility text)
        $this->sync($this->feed());
        $s = Scholarship::where('sap_objid', self::BAFOG)->first();
        DB::table('scholarships')->where('id', $s->id)->update([
            'q_en_json' => json_encode('<p>Section 8 curated text</p>'),
            'q_de_json' => json_encode('<p>§ 8 BAföG kuratierter Text</p>'),
        ]);

        Log::spy();
        // D) upstream renames the programme behind sap_objid 20000162; the other scholarship changes its origins
        $this->sync($this->feed([3], bafogName: 'Some renamed programme'));

        $s->refresh();
        $this->assertSame([], $this->origins(self::BAFOG), 'D) origins NOT re-attached (no fallback to normal sync)');
        $this->assertSame('<p>Section 8 curated text</p>', $s->q_en_json, 'D) curated q_en not overwritten');
        $this->assertSame('<p>§ 8 BAföG kuratierter Text</p>', $s->q_de_json, 'D) curated q_de not overwritten');
        $this->assertSame([3], $this->origins(self::OTHER), 'D) unrelated scholarship still syncs normally');
        Log::shouldHaveReceived('warning')->once()->withArgs(fn ($msg, $ctx) => str_contains($msg, (string) self::BAFOG)
            && ($ctx['sap_objid'] ?? null) === self::BAFOG && str_contains($ctx['incoming_name'] ?? '', 'Some renamed programme'));
    }

    public function test_unexpected_name_does_not_attach_origins_even_on_first_import(): void
    {
        Log::spy();
        $this->sync($this->feed(bafogName: 'Some renamed programme'));
        $this->assertSame([], $this->origins(self::BAFOG), 'no origins attached for a frozen curated identifier');
        $this->assertNull(Scholarship::where('sap_objid', self::BAFOG)->first()->q_en_json);
        $this->assertSame([1, 2, 3], $this->origins(self::OTHER));
        Log::shouldHaveReceived('warning')->once();
    }
}

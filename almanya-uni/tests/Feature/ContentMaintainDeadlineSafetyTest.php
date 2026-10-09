<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Program Verification V1 güvenlik kapısı: otomatik bakım (scheduler'daki `content:maintain --apply`) resmî kaynak
 * olmadan son başvuru tarihi üretemez/ileri taşıyamaz. fix-deadlines (yıl ekler), reparse-deadlines ve parse-deadlines
 * (metinde yıl yoksa yıl TAHMİN eder) bakımda yalnız rapor modunda çalışır. Diğer güvenli düzeltmeler uygulanmaya devam eder.
 */
class ContentMaintainDeadlineSafetyTest extends TestCase
{
    use RefreshDatabase;

    private function program(string $slug, array $attrs): int
    {
        $uni = DB::table('universities')->insertGetId(['name_de' => "Uni {$slug}", 'name_tr' => "Uni {$slug}", 'slug' => "uni-{$slug}", 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);

        return DB::table('programs')->insertGetId(array_merge([
            'name_de' => "Programm {$slug}", 'slug' => $slug, 'degree' => 'master', 'language' => 'en', 'university_id' => $uni,
            'is_active' => true, 'source' => 'partner', 'created_at' => now(), 'updated_at' => now(),
        ], $attrs));
    }

    public function test_scheduled_maintenance_cannot_move_or_invent_deadlines(): void
    {
        $past = now()->subDays(40)->format('Y-m-d');
        $rollover = $this->program('pv-rollover', ['application_deadline_winter' => $past]);                               // fix-deadlines adayı
        $reparse = $this->program('pv-reparse', ['application_deadline_winter' => $past, 'application_deadline_summer' => $past,
            'admission_summary' => 'Bewerbungsschluss Wintersemester: 15.07., Sommersemester: 15.01.']);                    // reparse adayı (yılsız metin)
        $parse = $this->program('pv-parse', ['admission_summary' => 'Application deadline: 31.05. (winter semester)']);    // parse adayı (boş tarih)
        $badFee = $this->program('pv-fee', ['application_fee_eur' => 9999]);                                                 // fix-data hâlâ uygulanmalı

        $this->artisan('content:maintain', ['--apply' => true])->assertSuccessful();

        $row = fn ($id) => DB::table('programs')->where('id', $id)->first();
        $this->assertSame($past, $row($rollover)->application_deadline_winter, 'fix-deadlines geçmiş tarihe yıl eklememeli');
        $this->assertSame($past, $row($reparse)->application_deadline_winter, 'reparse tahmini yılla yeniden yazmamalı');
        $this->assertSame($past, $row($reparse)->application_deadline_summer);
        $this->assertNull($row($parse)->application_deadline_winter, 'parse yılsız metinden tarih üretmemeli');
        $this->assertNull($row($parse)->application_deadline_summer);
        $this->assertNull($row($badFee)->application_fee_eur, 'diğer güvenli düzeltmeler (fix-data) uygulanmaya devam etmeli');
    }

    public function test_deadline_commands_still_work_when_run_manually_and_explicitly(): void
    {
        $past = now()->subDays(40)->format('Y-m-d');
        $id = $this->program('pv-manual', ['application_deadline_winter' => $past]);

        $this->artisan('programs:fix-deadlines')->assertSuccessful();                                 // rapor modu yazmaz
        $this->assertSame($past, DB::table('programs')->where('id', $id)->value('application_deadline_winter'));
    }
}

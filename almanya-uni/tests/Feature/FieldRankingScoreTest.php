<?php

namespace Tests\Feature;

use App\Models\FieldOfStudy;
use App\Models\Program;
use App\Models\University;
use App\Models\UniversitySubjectRank;
use App\Services\RankingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Alan sıralaması puanlaması (RankingService::buildForField).
 *
 * NEDEN BU TEST VAR: bu puanlama iki kez canlıda regresyon üretti —
 *   (1) program sayısı sıralamadan tamamen çıkarılınca alanda TEK programı olan genel
 *       üniversiteler gerçek mühendislik okullarının önüne geçti,
 *   (2) alt-birim kayıtları yüzünden asıl TU Berlin listeden düştü.
 * İkisi de ancak canlıda fark edildi çünkü yerelde proje veritabanı yok. Bu test,
 * doğrulamayı CI'ın gerçek MySQL'ine taşıyor.
 */
class FieldRankingScoreTest extends TestCase
{
    use RefreshDatabase;

    private FieldOfStudy $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = FieldOfStudy::create([
            'name_tr' => 'Mühendislik',
            'name_de' => 'Ingenieurwesen',
            'name_en' => 'Engineering',
            'slug' => 'test-muhendislik',
            'is_active' => true,
        ]);
    }

    private function uni(string $slug, array $attrs = []): University
    {
        $u = University::create(array_merge([
            'name_tr' => $slug,
            'name_de' => $slug,
            'slug' => $slug,
            'is_active' => true,
        ], $attrs));

        // buildForField whereHas('programs') istiyor → alanda en az bir aktif program şart.
        Program::create([
            'university_id' => $u->id,
            'field_of_study_id' => $this->field->id,
            'name_de' => 'Testprogramm',
            'slug' => $slug . '-prog',
            'degree' => 'bachelor',
            'is_active' => true,
        ]);

        return $u;
    }

    /** @return string[] slug'lar, sıralamadaki sırayla */
    private function rankedSlugs(): array
    {
        $svc = app(RankingService::class);
        $cfg = $svc->resolve('best-' . $this->field->slug . '-universities');
        $this->assertNotNull($cfg, 'alan sıralaması çözümlenemedi');

        return array_column($svc->fetchTop($cfg, 50), 'slug');
    }

    public function test_konu_sirasi_genel_dunya_sirasinin_yerine_gecer(): void
    {
        // Genel sırası ÇOK İYİ ama alanda konu sırası yok.
        $this->uni('genel-guclu', ['qs_world_rank' => 50, 'student_count' => 10000]);

        // Genel sırası zayıf, ama alanda güçlü konu sırası var (GRAS 51-75 gibi).
        $alanda = $this->uni('alanda-guclu', ['qs_world_rank' => 400, 'student_count' => 10000]);
        UniversitySubjectRank::create([
            'university_id' => $alanda->id,
            'field_of_study_id' => $this->field->id,
            'source' => UniversitySubjectRank::SOURCE_GRAS,
            'source_subject' => 'Mechanical Engineering',
            'rank_low' => 51,
            'rank_high' => 75,
            'year' => 2025,
        ]);

        $order = $this->rankedSlugs();

        $this->assertSame('alanda-guclu', $order[0], 'konu sırası genel sıranın önüne geçmeli');
        $this->assertSame('genel-guclu', $order[1]);
    }

    public function test_konu_sirasi_yoksa_genel_dunya_sirasina_dusulur(): void
    {
        $this->uni('dunya-100', ['qs_world_rank' => 100, 'student_count' => 10000]);
        $this->uni('dunya-900', ['qs_world_rank' => 900, 'student_count' => 10000]);
        $this->uni('siralamasiz', ['student_count' => 10000]);

        $order = $this->rankedSlugs();

        $this->assertSame(['dunya-100', 'dunya-900', 'siralamasiz'], $order);
    }

    public function test_alan_derinligi_tek_programli_kurumu_one_gecirmez(): void
    {
        // GEÇMİŞ REGRESYON: program sayısı skordan çıkarılınca, alanda tek programı olan
        // ama genel sırası iyi bir kurum (örn. bir tıp yüksekokulu) mühendislik listesinde
        // 7. sıraya çıkmıştı. Derinlik bileşeni bunu engellemeli.
        $tekProgram = $this->uni('tek-programli', ['qs_world_rank' => 130, 'student_count' => 3500]);

        $cokProgram = $this->uni('cok-programli', ['qs_world_rank' => 150, 'student_count' => 35000]);
        foreach (range(1, 29) as $i) {
            Program::create([
                'university_id' => $cokProgram->id,
                'field_of_study_id' => $this->field->id,
                'name_de' => 'Programm ' . $i,
                'slug' => 'cok-programli-' . $i,
                'degree' => 'bachelor',
                'is_active' => true,
            ]);
        }

        $order = $this->rankedSlugs();

        $this->assertSame('cok-programli', $order[0], 'alan derinliği tek programlı kurumu geçmeli');
        $this->assertContains('tek-programli', $order);
    }

    public function test_arastirma_kurumlari_siralamada_gorunmez(): void
    {
        $this->uni('normal-uni', ['qs_world_rank' => 200, 'student_count' => 10000]);
        $this->uni('arastirma-enstitusu', [
            'qs_world_rank' => 60,
            'student_count' => 10000,
            'exclude_from_rankings' => true,
        ]);

        $this->assertSame(['normal-uni'], $this->rankedSlugs());
    }
}

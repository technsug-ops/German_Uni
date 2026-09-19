<?php

namespace Tests\Feature;

use App\Models\FieldOfStudy;
use App\Models\Program;
use App\Models\Setting;
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

    /** Sıralanan alan DIŞINDA program açmak için — "alan odağı" bileşeni bunu ölçer. */
    private FieldOfStudy $other;

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

        $this->other = FieldOfStudy::create([
            'name_tr' => 'Diğer',
            'name_de' => 'Sonstige',
            'name_en' => 'Other',
            'slug' => 'test-diger',
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

    // ───────────────────────── v2 puanlaması ─────────────────────────
    //
    // v2, dünya sıralamalarında hiç yer almayan Fachhochschule'lerin listede
    // yükselebilmesi için kalite ağırlığını %60'tan %45'e indirip farkı alan odağı,
    // uluslararası erişim ve topluluk ilgisine dağıtır. Aşağıdaki testler bu kapının
    // AÇILDIĞINI ama fazla açılmadığını (güçlü teknik üniversite hâlâ önde) sabitler.

    private function useV2(): void
    {
        Setting::set(RankingService::FIELD_SCORE_SETTING, 'v2', 'rankings');
    }

    /** Alanda n adet program üret; $en tanesi İngilizce. */
    private function programs(University $u, int $n, int $en = 0, ?int $fieldId = null): void
    {
        foreach (range(1, $n) as $i) {
            Program::create([
                'university_id' => $u->id,
                'field_of_study_id' => $fieldId ?? $this->field->id,
                'name_de' => 'Programm ' . $i,
                'slug' => $u->slug . '-p' . $i . '-' . ($fieldId ?? $this->field->id),
                'degree' => 'bachelor',
                'language' => $i <= $en ? 'en' : 'de',
                'is_active' => true,
            ]);
        }
    }

    public function test_v2_odakli_fh_dunya_siralamasi_olmadan_da_yukselebilir(): void
    {
        // Genel üniversite: dünya sırası var ama alan, kataloğunun küçük bir parçası.
        $genel = $this->uni('genel-uni', ['qs_world_rank' => 200, 'student_count' => 40000]);
        $this->programs($genel, 5);                       // alanda 6 program (uni() 1 tane açtı)
        $this->programs($genel, 94, 0, $this->other->id); // başka alanlarda 94 → toplam 100

        // FH: hiçbir dünya sıralamasında yok, ama kataloğunun ağırlığı bu alanda.
        $fh = $this->uni('odakli-fh', ['type' => 'applied_sciences', 'student_count' => 6000, 'is_uni_assist_member' => true]);
        $this->programs($fh, 19, 5);                      // alanda 20, 5'i İngilizce
        $this->programs($fh, 5, 0, $this->other->id);     // toplam 25

        // v1'de dünya sırası %60 ağırlıkta → genel üniversite önde.
        $this->assertSame('genel-uni', $this->rankedSlugs()[0], 'v1 davranışı değişmemeli');

        // v2'de alan odağı + erişim bileşenleri FH'yi öne çıkarır.
        $this->useV2();
        $this->assertSame('odakli-fh', $this->rankedSlugs()[0], 'v2 odaklı FH\'yi öne almalı');
    }

    public function test_v2_kucuk_butik_kurum_ciddi_okulu_gecemez(): void
    {
        // Odak oranı yumuşatılmasa (bölende +10 olmasa) 3 programın 3'ü de alanda olan
        // bir butik okul odak bileşeninden tam puan alır ve listeyi başa yazardı.
        $butik = $this->uni('butik-okul', ['student_count' => 400]);
        $this->programs($butik, 2); // toplam 3, hepsi alanda

        $ciddi = $this->uni('ciddi-okul', ['qs_world_rank' => 600, 'student_count' => 20000]);
        $this->programs($ciddi, 14, 2);
        $this->programs($ciddi, 25, 0, $this->other->id); // alanda 15 / toplam 40

        $this->useV2();
        $order = $this->rankedSlugs();

        $this->assertSame('ciddi-okul', $order[0], 'yumuşatma butik kurumu başa taşımamalı');
    }

    public function test_v2_konu_sirasi_hala_en_agir_bilesen(): void
    {
        // v2'de kalite %45'e indi; yine de alanda kanıtlı bir kurum, kalitesiz ama
        // odaklı bir kurumun arkasına DÜŞMEMELİ.
        $konulu = $this->uni('konu-siralamali', ['qs_world_rank' => 400, 'student_count' => 30000, 'is_uni_assist_member' => true]);
        $this->programs($konulu, 24, 10);
        $this->programs($konulu, 95, 0, $this->other->id); // alanda 25 / toplam 120
        UniversitySubjectRank::create([
            'university_id' => $konulu->id,
            'field_of_study_id' => $this->field->id,
            'source' => UniversitySubjectRank::SOURCE_GRAS,
            'source_subject' => 'Mechanical Engineering',
            'rank_low' => 51,
            'rank_high' => 75,
            'year' => 2025,
        ]);

        $odakli = $this->uni('odakli-fh', ['type' => 'applied_sciences', 'student_count' => 6000, 'is_uni_assist_member' => true]);
        $this->programs($odakli, 19, 8);
        $this->programs($odakli, 5, 0, $this->other->id);

        $this->useV2();

        $this->assertSame('konu-siralamali', $this->rankedSlugs()[0]);
    }

    public function test_v2_ogrenci_sayisi_artik_puana_girmiyor(): void
    {
        // v1'de %10 büyüklük bileşeni vardı; v2'de kaldırıldı. Tek farkı öğrenci sayısı
        // olan iki kurum arasında v2 fark GÖRMEMELİ (sıra eşitlik bozucuya kalır).
        $buyuk = $this->uni('buyuk-kurum', ['qs_world_rank' => 300, 'student_count' => 50000]);
        $kucuk = $this->uni('kucuk-kurum', ['qs_world_rank' => 300, 'student_count' => 1000]);
        $this->programs($buyuk, 9);
        $this->programs($kucuk, 9);

        // v1: büyüklük bileşeni büyük kurumu öne alır.
        $this->assertSame('buyuk-kurum', $this->rankedSlugs()[0]);

        // v2: iki kurumun skoru eşit → sıralama öğrenci sayısına göre değişmez,
        // ikisi de listede kalır ve puan farkı sıfırdır.
        $this->useV2();
        $svc = app(RankingService::class);
        $rows = $svc->builderForField($this->field->id, 'v2')->limit(10)->get();
        $this->assertCount(2, $rows);
        $this->assertEqualsWithDelta(
            $rows[0]->field_programs_count,
            $rows[1]->field_programs_count,
            0.001,
            'iki kurum da aynı derinlikte olmalı — fark yalnızca öğrenci sayısıydı'
        );
    }

    public function test_yayinlanan_yontem_calisan_yontemle_ayni(): void
    {
        // Bu iki şey ayrılırsa sayfada yalan bir metodoloji yayınlarız.
        $v1 = RankingService::methodologyFor('program');
        $this->assertSame(60, $v1['indicators']['world_rank']['weight']);
        $this->assertSame(100, array_sum(array_column($v1['indicators'], 'weight')));

        $this->useV2();
        $v2 = RankingService::methodologyFor('program');
        $this->assertSame(45, $v2['indicators']['world_rank']['weight']);
        $this->assertSame(100, array_sum(array_column($v2['indicators'], 'weight')));
        $this->assertArrayHasKey('field_focus', $v2['indicators']);
    }

    public function test_gecersiz_surum_ayari_v1_e_duser(): void
    {
        Setting::set(RankingService::FIELD_SCORE_SETTING, 'v9-deneme', 'rankings');

        $this->assertSame('v1', RankingService::fieldScoreVersion());
    }

    public function test_ops_onizlemesi_iki_surumu_yan_yana_gosterir_ve_uygulayabilir(): void
    {
        // Yerelde proje veritabanı olmadığı için sürüm değişikliği ancak prod'da gerçek
        // veriyle görülebiliyor; önizleme sayfası o yüzden var — çalıştığı burada sabit.
        $genel = $this->uni('genel-uni', ['qs_world_rank' => 200, 'student_count' => 40000]);
        $this->programs($genel, 5);
        $this->programs($genel, 94, 0, $this->other->id);

        $fh = $this->uni('odakli-fh', ['type' => 'applied_sciences', 'is_uni_assist_member' => true]);
        $this->programs($fh, 19, 5);

        $admin = \App\Models\User::factory()->create(['is_admin' => true]);

        $res = $this->actingAs($admin)->get('/admin/ops/ranking-preview?field=' . $this->field->slug);
        $res->assertStatus(200);
        $res->assertSee('genel-uni');
        $res->assertSee('odakli-fh');
        $res->assertSee('Aktif puanlama sürümü: v1');

        // Uygulama adımı ayarı gerçekten değiştirmeli.
        $this->actingAs($admin)->get('/admin/ops/ranking-preview?apply=v2')->assertStatus(200);
        $this->assertSame('v2', RankingService::fieldScoreVersion());

        // Geri alınabilmeli.
        $this->actingAs($admin)->get('/admin/ops/ranking-preview?apply=v1')->assertStatus(200);
        $this->assertSame('v1', RankingService::fieldScoreVersion());
    }
}

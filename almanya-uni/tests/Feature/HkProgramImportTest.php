<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\University;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * HK importunun iki güvenlik kuralı.
 *
 * NEDEN BU TEST VAR: import 2.165 programı yalnızca ad + derece + NC ile ekliyor. Bu kayıtlar
 * listelerde/filtrelerde değerli ama indekslenirse ince-içerik yığını olur. Kural sessizce
 * bozulursa (yeni bir alan eklenir, isThin güncellenmez) fark etmek aylar sürer.
 */
class HkProgramImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_yalniz_ad_ve_derece_tasiyan_program_ince_sayilir(): void
    {
        $p = new Program(['name_de' => 'Volkswirtschaftslehre', 'degree' => 'bachelor', 'admission_mode' => 'zulassungsfrei']);

        $this->assertTrue($p->isThin());
    }

    /**
     * Program Data Quality Gate Phase 2 (02.10.2026): ince olmaktan çıkaran şey programa özgü ANLAMLI metin
     * (açıklama ya da başvuru/dil şartı, ≥ Program::MIN_MEANINGFUL_WORDS farklı kelime). Tek başına süre/ücret/tarih
     * ya da "DSH-2" gibi çıplak bir değer artık ince olmaktan çıkarmaz.
     */
    public function test_anlamli_metin_ince_olmaktan_cikarir_ciplak_alan_cikarmaz(): void
    {
        foreach ([
            ['description_tr' => 'Program, makroekonomi, ekonometri ve kamu maliyesi derslerini uygulamalı veri projeleriyle birleştirir.'],
            ['language_requirements_tr' => 'Başvuru için DSH-2 ya da TestDaF 4x4 belgesi ve ayrıca motivasyon mektubu, özgeçmiş gereklidir.'],
        ] as $field) {
            $p = new Program(array_merge(['name_de' => 'X', 'degree' => 'bachelor'], $field));
            $this->assertFalse($p->isThin(), 'anlamlı alan: ' . array_key_first($field));
        }
        foreach ([
            ['description_tr' => 'Uzun açıklama'],
            ['duration_semesters' => 6],
            ['tuition_fee_eur' => 1500],
            ['language_requirements_tr' => 'DSH-2'],
        ] as $field) {
            $p = new Program(array_merge(['name_de' => 'X', 'degree' => 'bachelor'], $field));
            $this->assertTrue($p->isThin(), 'çıplak alan ince kalmalı: ' . array_key_first($field));
        }
    }

    /** indexable() SQL ön filtresi, isThin() olmayan her programı kapsayan üst kümedir; sitemap kesin kararı isThin ile verir. */
    public function test_indexable_scope_isthin_olmayanlarin_ust_kumesidir(): void
    {
        $uni = University::create(['name_tr' => 'Test Üniversitesi', 'name_de' => 'Test Universitaet', 'slug' => 'test-universitaet-hk', 'is_active' => 1]);

        $thin = Program::create(['university_id' => $uni->id, 'name_de' => 'İnce Program', 'slug' => 'ince-program-test', 'degree' => 'bachelor', 'is_active' => 1]);
        $facts = Program::create(['university_id' => $uni->id, 'name_de' => 'Süreli Program', 'slug' => 'sureli-program-test', 'degree' => 'bachelor', 'is_active' => 1, 'duration_semesters' => 6]);
        $rich = Program::create(['university_id' => $uni->id, 'name_de' => 'Dolu Program', 'slug' => 'dolu-program-test', 'degree' => 'bachelor', 'is_active' => 1,
            'description_en' => 'The programme combines macroeconomics, econometrics and public finance with applied data projects.']);

        $indexable = Program::query()->indexable()->pluck('id');

        $this->assertFalse($indexable->contains($thin->id), 'ince program indexable olmamalı');
        $this->assertFalse($indexable->contains($facts->id), 'yalnız süre taşıyan program indexable olmamalı');
        $this->assertTrue($indexable->contains($rich->id), 'dolu program indexable olmalı');
        foreach (Program::all() as $p) {
            if (! $p->isThin()) {
                $this->assertTrue($indexable->contains($p->id), "isThin olmayan {$p->slug} ön filtrede olmalı");
            }
        }
    }
}

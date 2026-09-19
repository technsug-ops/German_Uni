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

    public function test_tek_bir_veri_alani_bile_ince_olmaktan_cikarir(): void
    {
        foreach ([
            ['description_tr' => 'Uzun açıklama'],
            ['duration_semesters' => 6],
            ['tuition_fee_eur' => 1500],
            ['language_requirements_tr' => 'DSH-2'],
        ] as $field) {
            $p = new Program(array_merge(['name_de' => 'X', 'degree' => 'bachelor'], $field));
            $this->assertFalse($p->isThin(), 'dolu alan: ' . array_key_first($field));
        }
    }

    public function test_indexable_scope_isthin_ile_ayni_sonucu_verir(): void
    {
        $uni = University::create(['name_tr' => 'Test Üniversitesi', 'name_de' => 'Test Universitaet', 'slug' => 'test-universitaet-hk', 'is_active' => 1]);

        $thin = Program::create(['university_id' => $uni->id, 'name_de' => 'İnce Program', 'slug' => 'ince-program-test', 'degree' => 'bachelor', 'is_active' => 1]);
        $rich = Program::create(['university_id' => $uni->id, 'name_de' => 'Dolu Program', 'slug' => 'dolu-program-test', 'degree' => 'bachelor', 'is_active' => 1, 'duration_semesters' => 6]);

        $indexable = Program::query()->indexable()->pluck('id');

        $this->assertFalse($indexable->contains($thin->id), 'ince program indexable olmamalı');
        $this->assertTrue($indexable->contains($rich->id), 'dolu program indexable olmalı');
    }
}

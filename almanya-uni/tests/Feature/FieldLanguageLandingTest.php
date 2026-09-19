<?php

namespace Tests\Feature;

use App\Models\FieldOfStudy;
use App\Models\Program;
use App\Models\University;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Alan × öğretim dili landing'i.
 *
 * NEDEN BU TEST VAR: bu sayfa "Almanya'da Almanca ekonomi nerede okunur" sorusunun karşılığı.
 * Dil filtresi sessizce bozulursa (ör. 'both' dışarıda kalırsa) sayfa doğru görünür ama
 * yarısı eksik liste gösterir — fark edilmesi zor, etkisi büyük.
 */
class FieldLanguageLandingTest extends TestCase
{
    use RefreshDatabase;

    private function makeProgram(FieldOfStudy $field, University $uni, string $name, ?string $lang): Program
    {
        return Program::create([
            'university_id' => $uni->id,
            'field_of_study_id' => $field->id,
            'name_de' => $name,
            'slug' => \Illuminate\Support\Str::slug($name) . '-' . uniqid(),
            'degree' => 'bachelor',
            'language' => $lang,
            'is_active' => 1,
        ]);
    }

    public function test_sayfa_dil_filtresini_dogru_uyguluyor(): void
    {
        $field = FieldOfStudy::create(['slug' => 'test-alan', 'name_tr' => 'Test Alan', 'name_de' => 'Testfach', 'is_active' => 1]);
        $uni = University::create(['name_tr' => 'Test Üni', 'name_de' => 'Test Universität', 'slug' => 'test-uni-landing', 'is_active' => 1]);

        $de = $this->makeProgram($field, $uni, 'Almanca Program', 'de');
        $both = $this->makeProgram($field, $uni, 'Iki Dilli Program', 'both');
        $en = $this->makeProgram($field, $uni, 'Ingilizce Program', 'en');

        $res = $this->get('/tr/programs/field/test-alan/language/de');

        $res->assertStatus(200)
            ->assertSee('Almanca Program')
            ->assertSee('Iki Dilli Program')   // 'both' Almanca listesine DE girmeli
            ->assertDontSee('Ingilizce Program');
    }

    public function test_gecersiz_dil_404(): void
    {
        FieldOfStudy::create(['slug' => 'test-alan-2', 'name_tr' => 'T2', 'name_de' => 'T2', 'is_active' => 1]);

        $this->get('/tr/programs/field/test-alan-2/language/fr')->assertStatus(404);
    }
}

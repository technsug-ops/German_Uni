<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * programs:export-hk kurum eşleştirmesi: normUni() "Universität Hamburg" ile "Technische Universität Hamburg"u aynı
 * anahtara indirir; eskiden SONUNCU kazanıyordu (2026-10-09: 1.029 program yanlış üniversitede). Birebir ad önce gelir,
 * çakışan anahtar hiçbir kuruma bağlanmaz.
 */
class ExportHkUniversityMatchTest extends TestCase
{
    use RefreshDatabase;

    private string $out = 'storage/framework/testing/hk-export-test.json';

    private function uni(string $name): void
    {
        DB::table('universities')->insert(['name_de' => $name, 'name_tr' => $name, 'slug' => \Illuminate\Support\Str::slug($name), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function catalog(string $hochschule, string $fach): void
    {
        DB::table('hk_catalog')->insert(['mode' => 'test', 'hochschule' => $hochschule, 'fach' => $fach, 'ort' => 'Hamburg', 'abschluss' => 'Master of Science', 'created_at' => now(), 'updated_at' => now()]);
    }

    private function export(): array
    {
        $this->artisan('programs:export-hk', ['--field' => 'hukuk-ekonomi', '--out' => $this->out])->assertSuccessful();

        return collect(json_decode(file_get_contents(base_path($this->out)), true)['programs'])->pluck('university_name', 'name_de')->all();
    }

    protected function tearDown(): void
    {
        @unlink(base_path($this->out));
        parent::tearDown();
    }

    public function test_exact_name_wins_and_colliding_key_is_not_guessed(): void
    {
        $this->uni('Universität Hamburg');
        $this->uni('Technische Universität Hamburg');
        $this->catalog('Universität Hamburg', 'Economics');
        $this->catalog('Technische Universität Hamburg', 'Logistics Management');
        $this->catalog('Uni Hamburg', 'Business Law'); // birebir değil, anahtar iki kuruma çakışıyor → atlanır

        $this->assertSame([
            'Economics' => 'Universität Hamburg',
            'Logistics Management' => 'Technische Universität Hamburg',
        ], $this->export());
    }
}

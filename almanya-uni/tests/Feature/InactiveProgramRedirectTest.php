<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Pasif program sayfası eski veriyle 200 + index sunmaz: aktif kopyası (aynı üni + derece, ad_de/ad_en çapraz) varsa
 * 301, yoksa 410. 2026-10-09'da 3.144 pasif sayfa index,follow + kendine canonical veriyordu.
 */
class InactiveProgramRedirectTest extends TestCase
{
    use RefreshDatabase;

    private int $uni;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uni = DB::table('universities')->insertGetId(['name_de' => 'Uni X', 'name_tr' => 'Uni X', 'slug' => 'uni-x', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function program(string $slug, array $attrs): void
    {
        DB::table('programs')->insert(array_merge([
            'slug' => $slug, 'university_id' => $this->uni, 'degree' => 'master', 'language' => 'en', 'is_active' => true,
            'source' => 'partner', 'created_at' => now(), 'updated_at' => now(),
        ], $attrs));
    }

    public function test_inactive_duplicate_redirects_to_active_counterpart(): void
    {
        $this->program('ms-physics-daad1', ['name_de' => 'MSc Physics', 'name_en' => 'Physics', 'source' => 'daad', 'is_active' => false]);
        $this->program('physik-hk1', ['name_de' => 'Physik', 'name_en' => 'Physics', 'source' => 'hochschulkompass']);

        $this->get('/en/programs/ms-physics-daad1')->assertStatus(301)->assertRedirect('/en/programs/physik-hk1');
        $this->get('/en/programs/physik-hk1')->assertOk();
    }

    public function test_inactive_without_counterpart_is_gone(): void
    {
        $this->program('mac-es-course', ['name_de' => 'Master’s College European Studies (MAC-ES)', 'name_en' => 'European Studies', 'is_active' => false]);
        $this->program('other-degree', ['name_de' => 'European Studies', 'degree' => 'bachelor']);

        $this->get('/en/programs/mac-es-course')->assertStatus(410)->assertSee('noindex', false);
    }
}

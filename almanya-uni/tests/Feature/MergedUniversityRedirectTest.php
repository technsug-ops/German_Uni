<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\UniversityWebController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Birleştirilen (merge) üniversite kaydının eski slug'ı kanonik sayfaya 301 yönlenir — kabuk kayıt DB'de (pasif)
 * dursa bile. Aksi halde eski URL'ler bölünmüş/eksik program listesiyle açılmaya devam ederdi.
 */
class MergedUniversityRedirectTest extends TestCase
{
    use RefreshDatabase;

    private function uni(string $slug, bool $active): void
    {
        DB::table('universities')->insert(['name_de' => $slug, 'name_tr' => $slug, 'slug' => $slug, 'is_active' => $active, 'created_at' => now(), 'updated_at' => now()]);
    }

    public function test_merged_slug_redirects_even_when_shell_record_still_exists(): void
    {
        $shell = 'paderborn-university-partner-019de9ee';
        $canonical = UniversityWebController::MERGED_SLUGS[$shell];
        $this->uni($shell, false);
        $this->uni($canonical, true);

        $this->get("/en/universities/{$shell}")->assertStatus(301)->assertRedirect("/en/universities/{$canonical}");
        $this->get("/en/universities/{$canonical}")->assertOk();
    }

    public function test_every_merge_target_is_not_itself_merged(): void
    {
        foreach (UniversityWebController::MERGED_SLUGS as $from => $to) {
            $this->assertArrayNotHasKey($to, UniversityWebController::MERGED_SLUGS, "{$from} → {$to} yönlendirme zinciri");
        }
    }
}

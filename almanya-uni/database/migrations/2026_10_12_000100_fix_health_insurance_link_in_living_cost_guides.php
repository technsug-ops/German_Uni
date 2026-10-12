<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Batch 4 yaşam maliyeti rehberleri (TR/EN/DE) sağlık sigortası karşılaştırma yazısına yerel kopyadaki slug'la
 * bağlanıyordu; prod'daki slug farklı (slug türetimi ortam başına ayrışmış). Üç rehberde yalnız bu URL değiştirilir.
 * Ön doğrulama: her rehberde eski URL tam bir kez. Zaten değiştirilmişse no-op. Tek transaction.
 */
return new class extends Migration
{
    private const GUIDES = [
        'tr' => ['slug' => 'real-cost-of-being-a-student-in-germany-budget-truth',
            'old' => '/tr/blog/health-insurance-comparison-2026-which-krankenkasse-is-best-for-students-in)',
            'new' => '/tr/blog/krankenkasse-comparison-2026-which-is-the-best-option-for-students)'],
        'en' => ['slug' => 'real-cost-student-germany-budget-reality-check',
            'old' => '/en/blog/health-insurance-comparison-2026-which-krankenkasse-is-best-for-students-in-en)',
            'new' => '/en/blog/krankenkasse-comparison-2026-which-is-the-best-option-for-students-en)'],
        'de' => ['slug' => 'wahre-kosten-studium-deutschland-budget-check',
            'old' => '/de/blog/health-insurance-comparison-2026-which-krankenkasse-is-best-for-students-in-de)',
            'new' => '/de/blog/krankenkasse-comparison-2026-which-is-the-best-option-for-students-de)'],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }
        // Test ortamında (taze DB) hiçbir rehberde eski bağlantı yoksa iş yok; eski 2026_06_17 migration'ından gelen TR rehber hedef değildir.
        $anyOld = collect(self::GUIDES)->contains(fn ($g, $loc) => str_contains((string) Post::where('locale', $loc)->where('slug', $g['slug'])->value('content_md'), $g['old']));
        if (app()->environment('testing') && ! $anyOld) {
            return;
        }
        $plan = [];
        $problems = [];
        foreach (self::GUIDES as $loc => $g) {
            $post = Post::where('locale', $loc)->where('slug', $g['slug'])->first();
            if (! $post) {
                if (app()->environment('testing')) {
                    continue; // CI fixture'ında rehber yoksa iş yok
                }
                $problems[] = "$loc/{$g['slug']}: bulunamadı";
                continue;
            }
            $md = (string) $post->content_md;
            if (str_contains($md, $g['new'])) {
                continue; // zaten düzeltilmiş → no-op
            }
            if (substr_count($md, $g['old']) !== 1) {
                $problems[] = "$loc/{$g['slug']}: eski sağlık sigortası bağlantısı tam bir kez bulunamadı";
                continue;
            }
            $post->content_md = str_replace($g['old'], $g['new'], $md);
            $plan[] = $post;
        }
        if ($problems) {
            throw new RuntimeException('Sağlık sigortası bağlantısı düzeltilmedi, hiçbir şey yazılmadı: ' . implode('; ', $problems));
        }
        if (! $plan) {
            return;
        }
        DB::transaction(function () use ($plan) {
            foreach ($plan as $post) {
                $post->save(); // content_html mutator ile yeniden üretilir
            }
        });
    }

    public function down(): void
    {
        // Geri alma yok — bağlantı düzeltmesi.
    }
};

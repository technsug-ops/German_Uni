<?php

namespace App\Console\Commands;

use App\Models\ContentAsset;
use App\Models\ContentBrief;
use App\Models\Post;
use App\Services\Content\ContentGenerationService;
use Illuminate\Console\Command;

/**
 * #12 storytelling Faz-1: blog'a bağlı brief'ler için infografik_data üretir
 * (Gemini). JSON fence'lerini temizler, geçerliyse language=tr + status=ready
 * yapar → blog sayfasında render edilir. İdempotent: hazır olanı atlar (--force
 * ile yeniden üretir). Gemini yoksa sessiz çıkar (deploy webhook'undan da çağrılır).
 */
class StorytellingInfographics extends Command
{
    protected $signature = 'storytelling:infographics {--force : Hazır olsa bile yeniden üret}';

    protected $description = 'Blog brief\'leri için infografik_data üret (TR, idempotent)';

    public function handle(ContentGenerationService $svc): int
    {
        if (! $svc->isConfigured()) {
            $this->warn('Gemini yapılandırılmamış — atlandı.');
            return self::SUCCESS;
        }

        // Hangi diller yayında — blog post'larının locale'lerinden çıkar
        $languages = Post::whereNotNull('content_brief_id')->distinct()->pluck('locale')
            ->filter()->values()->all() ?: ['tr'];

        $briefIds = Post::whereNotNull('content_brief_id')->distinct()->pluck('content_brief_id');

        foreach ($briefIds as $bid) {
            $brief = ContentBrief::find($bid);
            if (! $brief) {
                continue;
            }

            foreach ($languages as $lang) {
                $ready = ContentAsset::where('content_brief_id', $bid)
                    ->where('asset_type', 'infographic_data')
                    ->where('language', $lang)
                    ->where('status', 'ready')
                    ->exists();

                if ($ready && ! $this->option('force')) {
                    $this->line("atla (hazır): {$brief->slug} [{$lang}]");
                    continue;
                }

                $r = $svc->generateAsset($brief, 'infographic_data', $lang);
                if (empty($r['success']) || empty($r['asset'])) {
                    $this->warn("FAIL {$brief->slug} [{$lang}]: " . ($r['error'] ?? '?'));
                    continue;
                }

                // Gemini bazen ```json ... ``` fence ekler — temizle, JSON geçerliyse ready
                $raw = trim((string) $r['asset']->body_md);
                $raw = preg_replace('/^```(?:json)?\s*|\s*```$/', '', $raw);
                json_decode($raw);
                $valid = json_last_error() === JSON_ERROR_NONE;

                $r['asset']->update([
                    'body_md' => $raw,
                    'status'  => $valid ? 'ready' : 'draft',
                ]);

                $this->info(($valid ? '✅ OK' : '⚠️ GEÇERSİZ-JSON') . " {$brief->slug} [{$lang}]");
            }
        }

        return self::SUCCESS;
    }
}

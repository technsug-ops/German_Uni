<?php

namespace App\Console\Commands;

use App\Models\University;
use App\Services\GeminiTranslator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

#[Signature('rankings:sync-from-wikipedia {--limit=80} {--min-students=3000} {--force : Skip already-synced}')]
#[Description('Wikipedia EN sayfasındaki infobox + Rankings bölümünden QS/THE/ARWU world rank verisini çekip universities tablosuna yazar.')]
class RankingsSyncFromWikipedia extends Command
{
    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $minStudents = (int) $this->option('min-students');
        $force = (bool) $this->option('force');

        $translator = app(GeminiTranslator::class);
        if (! $translator->isConfigured()) {
            $this->error('Gemini API key not configured.');
            return self::FAILURE;
        }

        $query = University::where('is_active', 1)
            ->where('student_count', '>=', $minStudents)
            ->orderByDesc('student_count');

        if (! $force) {
            $query->where(function ($q) {
                $q->whereNull('rankings_synced_at')
                  ->orWhere('rankings_synced_at', '<', now()->subDays(90));
            });
        }

        $unis = $query->limit($limit)->get(['id', 'name_de', 'name_en', 'short_name', 'wikipedia_url_en', 'qs_world_rank']);

        $this->info("Processing {$unis->count()} universities (min {$minStudents} students)...");

        $bar = $this->output->createProgressBar($unis->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->start();

        $updated = 0;
        $noPage = 0;
        $noRanks = 0;

        foreach ($unis as $uni) {
            $bar->setMessage(\Illuminate\Support\Str::limit($uni->name_de, 50));

            // 1) Wikipedia EN sayfa başlığını tahmin et
            $title = $this->guessWikipediaTitle($uni);
            if (! $title) {
                $noPage++;
                $bar->advance();
                continue;
            }

            // 2) MediaWiki API ile wikitext çek
            $wikitext = $this->fetchWikitext($title);
            if (! $wikitext) {
                $noPage++;
                $bar->advance();
                continue;
            }

            // 3) Önce regex ile dene (hızlı, ücretsiz)
            $ranks = $this->parseInfoboxRanks($wikitext);

            // 4) Regex yetmediyse Gemini'ye yolla
            if (empty(array_filter($ranks))) {
                $ranks = $this->parseRanksWithAi($wikitext, $translator, $uni->name_de);
            }

            $update = ['rankings_synced_at' => now()];
            if (! empty($ranks['qs'])) $update['qs_world_rank'] = $ranks['qs'];
            if (! empty($ranks['the'])) $update['the_world_rank'] = $ranks['the'];
            if (! empty($ranks['arwu'])) $update['arwu_world_rank'] = $ranks['arwu'];

            if (count($update) > 1) {
                \DB::table('universities')->where('id', $uni->id)->update($update);
                $updated++;
            } else {
                $noRanks++;
            }

            $bar->advance();
            usleep(200000); // 200ms — rate limit
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✓ Updated: {$updated}");
        $this->line("  No Wikipedia page: {$noPage}");
        $this->line("  Page found but no ranks: {$noRanks}");
        $this->line("");
        $this->line("Total QS ranked: " . University::whereNotNull('qs_world_rank')->count());
        $this->line("Total THE ranked: " . University::whereNotNull('the_world_rank')->count());
        $this->line("Total ARWU ranked: " . University::whereNotNull('arwu_world_rank')->count());

        return self::SUCCESS;
    }

    private function guessWikipediaTitle(University $uni): ?string
    {
        // Önce kaydedilmiş URL varsa kullan
        if ($uni->wikipedia_url_en) {
            if (preg_match('~wikipedia\.org/wiki/([^/?#]+)~i', $uni->wikipedia_url_en, $m)) {
                return urldecode($m[1]);
            }
        }

        // Sıralı tahminler
        $candidates = array_filter([
            $uni->name_en,
            $uni->short_name,
            $uni->name_de,
            str_replace(' ', '_', $uni->name_de),
        ]);

        foreach ($candidates as $candidate) {
            $title = str_replace(' ', '_', trim($candidate));
            if ($this->wikipediaPageExists($title)) {
                return $title;
            }
        }

        return null;
    }

    private const USER_AGENT = 'AlmanyaUni/1.0 (https://almanyauni.com; technsug@gmail.com) Laravel/PHP';

    private function wikipediaPageExists(string $title): bool
    {
        try {
            $r = Http::withUserAgent(self::USER_AGENT)
                ->timeout(10)
                ->get('https://en.wikipedia.org/w/api.php', [
                    'action' => 'query',
                    'titles' => $title,
                    'format' => 'json',
                    'redirects' => 1,
                ]);
            $pages = $r->json()['query']['pages'] ?? [];
            $page = reset($pages);
            return $page && empty($page['missing']);
        } catch (\Throwable) {
            return false;
        }
    }

    private function fetchWikitext(string $title): ?string
    {
        try {
            $r = Http::withUserAgent(self::USER_AGENT)
                ->timeout(15)
                ->get('https://en.wikipedia.org/w/api.php', [
                    'action' => 'parse',
                    'page' => $title,
                    'prop' => 'wikitext',
                    'format' => 'json',
                    'redirects' => 1,
                ]);
            return $r->json()['parse']['wikitext']['*'] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Infobox university template'inden rank field'larını çek (regex).
     */
    private function parseInfoboxRanks(string $wikitext): array
    {
        $result = ['qs' => null, 'the' => null, 'arwu' => null];

        // QS World Rank: |ranking_QS = 22  veya  |QS_World = 22
        if (preg_match('/\|\s*(?:ranking_)?qs(?:_world)?\s*=\s*=?\s*(\d{1,4})/i', $wikitext, $m)) {
            $result['qs'] = (int) $m[1];
        }
        // THE
        if (preg_match('/\|\s*(?:ranking_)?the(?:_world)?\s*=\s*=?\s*(\d{1,4})/i', $wikitext, $m)) {
            $result['the'] = (int) $m[1];
        }
        // ARWU / Shanghai
        if (preg_match('/\|\s*(?:ranking_)?(?:arwu|shanghai)(?:_world)?\s*=\s*=?\s*(\d{1,4})/i', $wikitext, $m)) {
            $result['arwu'] = (int) $m[1];
        }

        return $result;
    }

    /**
     * AI fallback — wikitext'i Gemini'ye yolla, JSON döndür.
     */
    private function parseRanksWithAi(string $wikitext, GeminiTranslator $translator, string $uniName): array
    {
        // Wikitext'in sadece ilk 8000 char'ını yolla (infobox + intro genelde burada)
        $snippet = mb_substr($wikitext, 0, 8000);

        $systemPrompt = <<<TXT
You extract university world rankings from Wikipedia wikitext.

Task: Find the current/latest QS World University Rankings, THE (Times Higher Education) World University Rankings, and ARWU (Shanghai/Academic Ranking of World Universities) global positions.

Rules:
- Output ONLY a valid JSON object with keys "qs", "the", "arwu" and integer values (or null if not found).
- No markdown, no explanation, no preamble.
- Only WORLD rank (global). Ignore country rank, subject rank, regional rank.
- If multiple years are shown, use the most recent year.
- If a rank range is given (e.g. "201-250"), use the lower bound (201).
- If the text says "=22" (tied), use 22.
- If the field doesn't exist or is not clearly stated, use null.

Example output:
{"qs": 22, "the": 26, "arwu": 45}

University: {$uniName}

Wikitext:
TXT;

        try {
            $result = $translator->translate($snippet, $systemPrompt);
            if (! $result) return ['qs' => null, 'the' => null, 'arwu' => null];

            $clean = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', $result['translation']);
            $clean = trim($clean);
            $parsed = json_decode($clean, true);

            return [
                'qs' => is_int($parsed['qs'] ?? null) ? $parsed['qs'] : null,
                'the' => is_int($parsed['the'] ?? null) ? $parsed['the'] : null,
                'arwu' => is_int($parsed['arwu'] ?? null) ? $parsed['arwu'] : null,
            ];
        } catch (\Throwable) {
            return ['qs' => null, 'the' => null, 'arwu' => null];
        }
    }
}

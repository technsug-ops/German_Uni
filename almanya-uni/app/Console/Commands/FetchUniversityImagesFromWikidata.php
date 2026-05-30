<?php

namespace App\Console\Commands;

use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Auto-populate University.image_url from Wikidata P18 (image) for unis whose
 * slug embeds a Q-id (e.g. "universitat-hamburg-q156725"). The user has 446
 * unis with empty image_url and was being asked to fill them in by hand;
 * Wikidata already has hero images for most of them — this command harvests
 * them in one batch.
 *
 *   php artisan unis:fetch-wikidata-images           # dry-run, prints sample
 *   php artisan unis:fetch-wikidata-images --apply   # write to DB
 *   php artisan unis:fetch-wikidata-images --apply --limit=50
 *
 * Resolves to: https://commons.wikimedia.org/wiki/Special:FilePath/<file>?width=1280
 * which redirects to the actual upload.wikimedia.org URL. We store the
 * Special:FilePath form because it's resilient to file moves on Commons.
 */
class FetchUniversityImagesFromWikidata extends Command
{
    protected $signature = 'unis:fetch-wikidata-images
                            {--apply : Persist image_url to DB (default is dry-run)}
                            {--limit= : Stop after N successful fetches}
                            {--sleep=120 : Sleep ms between Wikidata calls (be polite)}';

    protected $description = 'Auto-populate image_url for universities that have a Q-id in their slug';

    public function handle(): int
    {
        $apply  = (bool) $this->option('apply');
        $limit  = (int) ($this->option('limit') ?: 0);
        $sleep  = (int) $this->option('sleep');

        // Slug pattern: "...-qNNNNN" where NNNNN is the Wikidata Q-id.
        $candidates = University::query()
            ->where('is_active', 1)
            ->where(fn ($q) => $q->whereNull('image_url')->orWhere('image_url', ''))
            ->where('slug', 'like', '%-q%')
            ->orderByDesc('student_count')
            ->orderBy('name_de')
            ->get(['id', 'slug', 'name_de']);

        $this->info('Candidates: ' . $candidates->count() . ($apply ? '  (APPLY mode)' : '  (DRY-RUN)'));

        $hit = 0; $miss = 0; $errored = 0;
        foreach ($candidates as $uni) {
            // Extract trailing -qNNN id
            if (! preg_match('/-q(\d+)$/i', $uni->slug, $m)) { $miss++; continue; }
            $qid = 'Q' . $m[1];

            $imageFile = $this->fetchP18($qid);
            usleep($sleep * 1000);

            if (! $imageFile) {
                $miss++;
                if ($miss < 8) $this->line('  miss  ' . $qid . '  ' . $uni->name_de);
                continue;
            }

            // Build Commons Special:FilePath URL (resilient to file renames)
            $url = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($imageFile) . '?width=1280';

            $hit++;
            $this->info(sprintf('  hit   %s  %-40s  →  %s', $qid, mb_strimwidth($uni->name_de, 0, 40, '…'), mb_strimwidth($imageFile, 0, 60, '…')));

            if ($apply) {
                $uni->image_url = $url;
                $uni->save();
            }

            if ($limit && $hit >= $limit) {
                $this->warn('Hit limit (' . $limit . ') — stopping.');
                break;
            }
        }

        $this->newLine();
        $this->info("Done. hit=$hit  miss=$miss  errored=$errored");
        if (! $apply) $this->warn('Re-run with --apply to persist.');
        return self::SUCCESS;
    }

    /**
     * Query Wikidata REST for the P18 (image) claim of an entity.
     * Returns the bare filename (e.g. "Hauptgebäude_UHH.jpg") or null.
     */
    private function fetchP18(string $qid): ?string
    {
        try {
            $resp = Http::timeout(8)
                ->retry(2, 250)
                ->withHeaders(['User-Agent' => 'AlmanyaUni/1.0 (image harvest; halil@almanyauni.com)'])
                ->get("https://www.wikidata.org/wiki/Special:EntityData/$qid.json");

            if (! $resp->ok()) return null;
            $entity = $resp->json("entities.$qid");
            if (! $entity) return null;

            // P18 = "image". Take the highest-rank value.
            $claims = $entity['claims']['P18'] ?? [];
            foreach ($claims as $c) {
                $name = $c['mainsnak']['datavalue']['value'] ?? null;
                if ($name && is_string($name)) {
                    return $name;
                }
            }
            // Fallback: P154 (logo image)
            $claims = $entity['claims']['P154'] ?? [];
            foreach ($claims as $c) {
                $name = $c['mainsnak']['datavalue']['value'] ?? null;
                if ($name && is_string($name)) {
                    return $name;
                }
            }
        } catch (\Throwable $e) {
            $this->warn('  err   ' . $qid . '  ' . $e->getMessage());
            return null;
        }
        return null;
    }
}

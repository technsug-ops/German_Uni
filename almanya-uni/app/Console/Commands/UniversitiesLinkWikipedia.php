<?php

namespace App\Console\Commands;

use App\Models\University;
use App\Services\Enrichment\WikipediaExtract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Wikidata_id'siz üniversiteleri Wikipedia REST summary API ile bağla.
 * Her üni için name_de ile arama → eşleşince wikipedia_url_de, description_de, image_url doldur.
 *
 * Bu AI değil — sadece Wikipedia content. Ücretsiz + hızlı.
 */
class UniversitiesLinkWikipedia extends Command
{
    protected $signature = 'universities:link-wikipedia
        {--limit=0 : Sadece N üni}
        {--force : description_de\'si dolu olanları da yeniden çek}
        {--dry-run : Sadece raporla, değişiklik yapma}';

    protected $description = 'wikidata_id\'siz üniler için Wikipedia summary ile temel veri doldur';

    public function handle(WikipediaExtract $wiki): int
    {
        if (method_exists(University::class, 'disableSearchSyncing')) {
            University::disableSearchSyncing();
        }

        $query = University::where('is_active', 1);

        if (!$this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('description_de')
                    ->orWhereNull('wikipedia_url_de');
            });
        }

        if ((int) $this->option('limit') > 0) {
            $query->limit((int) $this->option('limit'));
        }

        $unis = $query->orderByDesc('student_count')->get(['id', 'name_de', 'short_name', 'wikidata_id', 'description_de', 'wikipedia_url_de', 'image_url']);
        $total = $unis->count();

        if ($total === 0) {
            $this->info('İşlenecek üni yok.');
            return self::SUCCESS;
        }

        $this->info("Toplam {$total} üni Wikipedia'da aranıyor…");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $found = 0;
        $notFound = 0;
        $updated = 0;

        foreach ($unis as $uni) {
            // 1. Direkt title
            $r = $wiki->fetchByTitle($uni->name_de, 'de');

            // 2. EN
            if (!$r || empty($r['extract'])) {
                $r = $wiki->fetchByTitle($uni->name_de, 'en');
            }

            // 3. Opensearch fuzzy fallback (yazım/varyant farkları)
            if (!$r || empty($r['extract'])) {
                $foundTitle = $this->fuzzySearchTitle($uni->name_de, 'de');
                if ($foundTitle) {
                    $r = $wiki->fetchByTitle($foundTitle, 'de');
                }
            }

            // 4. short_name ile dene (örn. "TUM", "LMU")
            if ((!$r || empty($r['extract'])) && !empty($uni->short_name) && mb_strlen($uni->short_name) >= 3) {
                $r = $wiki->fetchByTitle($uni->short_name, 'de');
            }

            if (!$r || empty($r['extract'])) {
                $notFound++;
                $bar->advance();
                usleep(150_000);
                continue;
            }

            $found++;
            $update = [];

            if (empty($uni->description_de) || $this->option('force')) {
                $update['description_de'] = mb_substr($r['extract'], 0, 5000);
            }
            if (empty($uni->wikipedia_url_de) && !empty($r['source_url'])) {
                $update['wikipedia_url_de'] = $r['source_url'];
            }
            if (empty($uni->image_url) && !empty($r['thumbnail_url'])) {
                $update['image_url'] = $r['thumbnail_url'];
            }

            // Wikidata QID için ek istek — pageprops endpoint
            if (empty($uni->wikidata_id)) {
                $qid = $this->fetchWikidataQid($r['title'] ?? $uni->name_de);
                if ($qid) {
                    // Başka üniye atanmamış mı kontrol et (UNIQUE constraint)
                    $taken = University::where('wikidata_id', $qid)->where('id', '!=', $uni->id)->exists();
                    if (!$taken) {
                        $update['wikidata_id'] = $qid;
                    }
                }
            }

            if (!empty($update) && !$this->option('dry-run')) {
                try {
                    $uni->update($update);
                    $updated++;
                } catch (\Illuminate\Database\QueryException $e) {
                    // Duplicate ihtimaline karşı wikidata_id'yi düşürüp tekrar dene
                    unset($update['wikidata_id']);
                    if (!empty($update)) {
                        $uni->update($update);
                        $updated++;
                    }
                }
            }

            $bar->advance();
            usleep(200_000); // Wikipedia API'yi yormamak için
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ {$found} bulundu · {$updated} güncellendi · {$notFound} eşleşme yok");
        if ($this->option('dry-run')) {
            $this->warn('Dry-run modu — değişiklik yapılmadı');
        }
        return self::SUCCESS;
    }

    /**
     * Wikipedia opensearch ile fuzzy başlık ara. İlk eşleşeni döner.
     */
    private function fuzzySearchTitle(string $name, string $lang = 'de'): ?string
    {
        try {
            $resp = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'AlmanyaUniBot/1.0'])
                ->get("https://$lang.wikipedia.org/w/api.php", [
                    'action' => 'opensearch',
                    'search' => $name,
                    'limit' => 3,
                    'namespace' => 0,
                    'format' => 'json',
                ]);
            if (!$resp->ok()) return null;
            $data = $resp->json();
            // opensearch döndürüyor: [query, [titles], [descs], [urls]]
            $titles = $data[1] ?? [];
            if (empty($titles)) return null;

            // İlk başlığı al ama "Liste der" gibi disambiguation/liste sayfalarını atla
            foreach ($titles as $t) {
                if (preg_match('/^(Liste|Category|Wikipedia):/i', $t)) continue;
                return $t;
            }
            return $titles[0] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function fetchWikidataQid(string $title): ?string
    {
        try {
            $resp = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'AlmanyaUniBot/1.0 (https://almanyauni.de; technsug@gmail.com)'])
                ->get('https://de.wikipedia.org/w/api.php', [
                    'action' => 'query',
                    'prop' => 'pageprops',
                    'titles' => $title,
                    'format' => 'json',
                    'redirects' => 1,
                ]);
            if (!$resp->ok()) return null;
            $data = $resp->json();
            $pages = $data['query']['pages'] ?? [];
            foreach ($pages as $page) {
                if (!empty($page['pageprops']['wikibase_item'])) {
                    return $page['pageprops']['wikibase_item'];
                }
            }
        } catch (\Throwable) {}
        return null;
    }
}

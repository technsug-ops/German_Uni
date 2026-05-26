<?php

namespace App\Console\Commands;

use App\Models\University;
use App\Services\WikidataService;
use Illuminate\Console\Command;

class EnrichUniversities extends Command
{
    protected $signature = 'wikidata:enrich
        {--dry-run : DB\'ye yazmadan test et}
        {--only-missing : Sadece description veya wiki linki olmayanlar için}';

    protected $description = 'Wikidata\'dan üni description, Wikipedia URL ve detaylı tip bilgisini çek.';

    // Wikidata Q-id → bizim type değerimiz
    private array $typeMap = [
        'Q1664720' => 'private',           // Private university
        'Q1364732' => 'applied_sciences',  // Fachhochschule
        'Q4187951' => 'art',                // Kunsthochschule
        'Q875538' => 'public',              // Public university
        'Q3918' => 'public',                // Universität (genel)
        'Q1156895' => 'public',             // Technical university
        'Q38723' => 'public',               // Higher education institution
        'Q2385804' => 'religion',           // Theological seminary
        'Q1321960' => 'religion',           // Religious university
        'Q40207596' => 'public',            // Bundesuniversität
    ];

    public function __construct(private WikidataService $wikidata)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('🔬 Üniversite zenginleştirme başlıyor');
        $this->newLine();

        $dryRun = (bool) $this->option('dry-run');
        $onlyMissing = (bool) $this->option('only-missing');

        $query = University::whereNotNull('wikidata_id');
        if ($onlyMissing) {
            $query->where(function ($q) {
                $q->whereNull('description_de')
                    ->orWhereNull('wikipedia_url_de');
            });
        }

        $unis = $query->get(['id', 'wikidata_id', 'name_de', 'type']);
        $this->info("Hedef üni sayısı: {$unis->count()}");

        $qids = $unis->pluck('wikidata_id')->all();
        $this->info("Wikidata'dan veri çekiliyor (chunk'lar halinde)...");

        $enrichment = $this->wikidata->getUniversityEnrichment($qids);
        $this->info("Wikidata yanıt: " . count($enrichment) . " kayıt");
        $this->newLine();

        $updated = 0;
        $descAdded = 0;
        $wikiAdded = 0;
        $typeChanged = 0;

        $bar = $this->output->createProgressBar($unis->count());
        $bar->start();

        foreach ($unis as $uni) {
            $data = $enrichment[$uni->wikidata_id] ?? null;
            if (!$data) {
                $bar->advance();
                continue;
            }

            $update = [];

            if (!empty($data['description_de']) && empty($uni->description_de)) {
                $update['description_de'] = $data['description_de'];
                $descAdded++;
            }
            if (!empty($data['description_en']) && empty($uni->description_en)) {
                $update['description_en'] = $data['description_en'];
            }
            if (!empty($data['wiki_de']) && empty($uni->wikipedia_url_de)) {
                $update['wikipedia_url_de'] = $data['wiki_de'];
                $wikiAdded++;
            }
            if (!empty($data['wiki_en']) && empty($uni->wikipedia_url_en)) {
                $update['wikipedia_url_en'] = $data['wiki_en'];
            }

            // Type re-classification: önce daha spesifik tip varsa onu seç
            $newType = $this->detectBestType($data['types'] ?? [], $uni->type);
            if ($newType && $newType !== $uni->type) {
                $update['type'] = $newType;
                $typeChanged++;
            }

            if (!empty($update) && !$dryRun) {
                $update['last_synced_at'] = now();
                University::where('id', $uni->id)->update($update);
                $updated++;
            } elseif (!empty($update)) {
                $updated++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Güncellendi', 'Description eklendi', 'Wikipedia eklendi', 'Tür değişti'],
            [[$updated, $descAdded, $wikiAdded, $typeChanged]]
        );

        if ($dryRun) {
            $this->warn('⚠️  DRY-RUN modu: değişiklik yapılmadı.');
        }

        return self::SUCCESS;
    }

    /**
     * Multiple type Q-id geldiğinde en spesifik olanı seç.
     * Öncelik: art > applied_sciences > religion > private > public
     */
    private function detectBestType(array $typeQids, ?string $currentType): ?string
    {
        $priority = ['art', 'applied_sciences', 'religion', 'private', 'public'];
        $candidates = [];

        foreach ($typeQids as $qid) {
            if (isset($this->typeMap[$qid])) {
                $candidates[] = $this->typeMap[$qid];
            }
        }

        if (empty($candidates)) {
            return $currentType;
        }

        foreach ($priority as $p) {
            if (in_array($p, $candidates, true)) {
                return $p;
            }
        }

        return $currentType;
    }
}

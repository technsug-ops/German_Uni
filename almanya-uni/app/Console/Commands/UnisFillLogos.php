<?php

namespace App\Console\Commands;

use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UnisFillLogos extends Command
{
    protected $signature = 'unis:fill-logos
        {--dry-run : Sadece say, kaydetme}
        {--limit=0 : İlk N üni (0 = hepsi)}';

    protected $description = 'logo_url boş olan aktif üniler için Wikidata P154 logosunu doldur (kart rozeti senkronu)';

    private const UA = 'AlmanyaUni/1.0 (https://www.almanyauni.com; hello@almanyauni.com)';

    public function handle(): int
    {
        $query = University::query()->where('is_active', 1)
            ->whereNotNull('image_url')->where('image_url', '!=', '')
            ->where(fn ($q) => $q->whereNull('logo_url')->orWhere('logo_url', ''))
            ->whereNotNull('wikidata_id')
            ->orderByDesc('student_count');

        if ($this->option('limit') > 0) {
            $query->limit((int) $this->option('limit'));
        }

        $unis = $query->get(['id', 'name_de', 'wikidata_id']);
        $total = $unis->count();
        if ($total === 0) {
            $this->info('Logo eksiği olan üni yok.');
            return self::SUCCESS;
        }

        $dry = $this->option('dry-run');
        $this->info(($dry ? '[DRY-RUN] ' : '') . "{$total} üni için P154 logo aranıyor...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $set = 0;
        $miss = 0;
        foreach ($unis as $u) {
            $file = $this->wikidataLogo($u->wikidata_id);
            if ($file) {
                if (! $dry) {
                    $url = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($file) . '?width=300';
                    University::withoutSyncingToSearch(function () use ($u, $url) {
                        University::whereKey($u->id)->update(['logo_url' => $url]);
                    });
                }
                $set++;
            } else {
                $miss++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info($dry
            ? "[DRY-RUN] {$set} logo bulundu, {$miss} bulunamadı."
            : "✅ {$set} logo eklendi, {$miss} bulunamadı.");

        return self::SUCCESS;
    }

    private function wikidataLogo(string $qid): ?string
    {
        try {
            $r = Http::timeout(15)->withHeaders(['User-Agent' => self::UA])
                ->get('https://www.wikidata.org/w/api.php', [
                    'action' => 'wbgetclaims', 'format' => 'json',
                    'entity' => $qid, 'property' => 'P154',
                ]);
            $claims = $r->json('claims.P154');
            return $claims[0]['mainsnak']['datavalue']['value'] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}

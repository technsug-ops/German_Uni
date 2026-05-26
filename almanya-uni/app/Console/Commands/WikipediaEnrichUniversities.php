<?php

namespace App\Console\Commands;

use App\Models\University;
use App\Services\WikipediaService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('wikipedia:enrich-universities
    {--limit=0 : Maksimum kaç üni işlensin (0 = sınırsız)}
    {--from-id=0 : Bu ID\'den itibaren başla (resume için)}
    {--delay=700 : İstekler arası gecikme (ms)}
    {--dry-run : Veritabanına yazma, sadece simüle et}
    {--all : Sadece eksiği olanları değil, hepsini sor (mevcut veriyi karşılaştırma için faydalı)}')]
#[Description('Eksik üniversite alanlarını (öğrenci sayısı, logo, kuruluş yılı) Wikipedia infobox\'larından doldurur.')]
class WikipediaEnrichUniversities extends Command
{
    public function handle(WikipediaService $svc): int
    {
        $limit  = (int) $this->option('limit');
        $fromId = (int) $this->option('from-id');
        $delay  = (int) $this->option('delay');
        $dryRun = (bool) $this->option('dry-run');
        $all    = (bool) $this->option('all');

        $query = University::query()
            ->whereNotNull('wikipedia_url_de')
            ->orderBy('id');

        if (! $all) {
            $query->where(function ($q) {
                $q->whereNull('student_count')
                  ->orWhereNull('logo_url')
                  ->orWhereNull('founded_year');
            });
        }

        if ($fromId > 0) {
            $query->where('id', '>=', $fromId);
        }

        $totalAvailable = (clone $query)->count();
        $total = $limit > 0 ? min($limit, $totalAvailable) : $totalAvailable;

        if ($total === 0) {
            $this->info('Eksik veriye sahip üni bulunamadı.');
            return self::SUCCESS;
        }

        $this->info(sprintf(
            '%d üni işlenecek (eksiği olan toplam %d). Mod: %s, gecikme: %dms.',
            $total,
            $totalAvailable,
            $dryRun ? 'DRY-RUN' : 'LIVE',
            $delay
        ));

        if ($limit > 0) {
            $query->limit($limit);
        }
        $unis = $query->get();

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% — %message%');
        $bar->setMessage('başlıyor...');
        $bar->start();

        $stats = [
            'updated'      => 0,
            'no_changes'   => 0,
            'no_infobox'   => 0,
            'fetch_failed' => 0,
            'skip_no_url'  => 0,
            'skip_bad_url' => 0,
            'errors'       => 0,
        ];
        $fieldStats = ['student_count' => 0, 'founded_year' => 0, 'logo_url' => 0];

        // Meilisearch çalışmıyorsa save patlamasın — Scout sync'i bypass et.
        // Tam enrichment sonunda kullanıcı `scout:import` ile reindex edebilir.
        University::withoutSyncingToSearch(function () use ($unis, $svc, $dryRun, $delay, $bar, &$stats, &$fieldStats) {
            foreach ($unis as $uni) {
                $bar->setMessage('[' . $uni->id . '] ' . mb_substr($uni->name_de, 0, 40));

                try {
                    $result = $svc->enrich($uni, $dryRun);
                    $stats[$result['status']] = ($stats[$result['status']] ?? 0) + 1;

                    foreach (array_keys($result['changes']) as $field) {
                        $fieldStats[$field] = ($fieldStats[$field] ?? 0) + 1;
                    }
                } catch (\Throwable $e) {
                    $stats['errors']++;
                    $this->newLine();
                    $this->warn("Error on uni #{$uni->id} ({$uni->name_de}): {$e->getMessage()}");
                }

                $bar->advance();
                usleep($delay * 1000);
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info('=== SONUÇ ===');
        $this->table(
            ['Durum', 'Sayı'],
            collect($stats)->map(fn ($v, $k) => [$k, $v])->values()->all()
        );
        $this->info('=== Doldurulan Alanlar ===');
        $this->table(
            ['Alan', 'Adet'],
            collect($fieldStats)->map(fn ($v, $k) => [$k, $v])->values()->all()
        );

        if ($dryRun) {
            $this->warn('DRY-RUN — veritabanı değişmedi. Gerçek çalıştırma için --dry-run\'ı kaldır.');
        }

        return self::SUCCESS;
    }
}

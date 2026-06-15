<?php

namespace App\Console\Commands;

use App\Models\Program;
use Illuminate\Console\Command;

/**
 * Geçmiş başvuru deadline'larını YILLIK döngüye göre bugüne taşır (rollover).
 *
 * Alman üni başvuru tarihleri her yıl AYNI gün/ay tekrarlanır (ör. kış için 15 Temmuz,
 * yaz için 15 Ocak). Veride geçmiş yıllara ait stale tarihler kalmış; başvuru takip
 * takvimi + favori digest'i bunları yanlışlıkla "süresi doldu" gösteriyor. Bu komut
 * geçmiş tarihi, gün/ay'ı koruyarak >= bugün olana dek tam-yıl ekleyip günceller.
 *
 *   php artisan programs:fix-deadlines           → DRY-RUN (rapor)
 *   php artisan programs:fix-deadlines --apply    → uygula
 */
class ProgramsFixDeadlines extends Command
{
    protected $signature = 'programs:fix-deadlines {--apply : Değişiklikleri yaz (varsayılan dry-run)}';

    protected $description = 'Geçmiş başvuru deadline\'larını yıllık döngüye göre bugüne taşır (rollover).';

    public function handle(): int
    {
        $apply = $this->option('apply');
        $today = now()->startOfDay();

        $this->info($apply ? '🔥 APPLY — deadline\'lar güncellenecek' : '🔍 DRY-RUN — hiçbir şey yazılmayacak');

        $rolled = ['summer' => 0, 'winter' => 0];
        $examples = [];

        Program::where('is_active', 1)
            ->where(function ($q) use ($today) {
                $q->whereDate('application_deadline_summer', '<', $today->toDateString())
                  ->orWhereDate('application_deadline_winter', '<', $today->toDateString());
            })
            ->select('id', 'slug', 'application_deadline_summer', 'application_deadline_winter')
            ->chunkById(500, function ($programs) use ($today, $apply, &$rolled, &$examples) {
                foreach ($programs as $p) {
                    $changes = [];
                    foreach (['summer' => 'application_deadline_summer', 'winter' => 'application_deadline_winter'] as $sem => $col) {
                        $d = $p->{$col};
                        if ($d && $d->lt($today)) {
                            $new = $d->copy();
                            while ($new->lt($today)) {
                                $new->addYear();
                            }
                            $changes[$col] = $new;
                            $rolled[$sem]++;
                            if (count($examples) < 8) {
                                $examples[] = sprintf('#%d %s: %s → %s', $p->id, $sem, $d->toDateString(), $new->toDateString());
                            }
                        }
                    }
                    if ($apply && $changes) {
                        // updateQuietly: last_synced_at/observer tetikleme, sadece tarihleri yaz.
                        Program::whereKey($p->id)->update($changes);
                    }
                }
            });

        $this->newLine();
        foreach (array_slice($examples, 0, 8) as $ex) {
            $this->line('  ' . $ex);
        }
        $this->newLine();
        $this->line("Yaz deadline taşınan:  {$rolled['summer']}");
        $this->line("Kış deadline taşınan:  {$rolled['winter']}");

        if (! $apply && ($rolled['summer'] || $rolled['winter'])) {
            $this->warn('Uygulamak için --apply ile çalıştırın.');
        } elseif ($apply) {
            $this->info('✓ Tamamlandı.');
        }

        return self::SUCCESS;
    }
}

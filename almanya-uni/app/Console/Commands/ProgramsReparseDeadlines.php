<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Services\DeadlineParser;
use Illuminate\Console\Command;

/**
 * Yaz == kış (aynı tarih) olan programları admission_summary metninden YENİDEN parse eder.
 * Bu durum genelde partner'ın tek tarihi iki alana kopyalamasından veya hatalı parse'tan olur.
 * Metinde iki ayrı dönem tarihi varsa DeadlineParser bunları doğru ayırır.
 *
 * SADECE iyileştiğinde yazar: yeni parse summer != winter VE en az biri dolu olduğunda.
 * Aksi halde mevcut değeri korur (kötüleştirmez).
 *
 *   php artisan programs:reparse-deadlines           → DRY-RUN
 *   php artisan programs:reparse-deadlines --apply    → uygula
 */
class ProgramsReparseDeadlines extends Command
{
    protected $signature = 'programs:reparse-deadlines {--apply : Değişiklikleri yaz (varsayılan dry-run)}';

    protected $description = 'Yaz==kış deadline\'ları admission_summary\'den yeniden parse eder (doğru dönem ayrımı).';

    public function handle(DeadlineParser $parser): int
    {
        $apply = $this->option('apply');
        $this->info($apply ? '🔥 APPLY' : '🔍 DRY-RUN');

        $programs = Program::where('is_active', 1)
            ->whereColumn('application_deadline_summer', 'application_deadline_winter')
            ->whereNotNull('admission_summary')->where('admission_summary', '!=', '')
            ->select('id', 'slug', 'admission_summary', 'application_deadline_summer', 'application_deadline_winter')
            ->get();

        $this->line("Aday (yaz==kış + admission_summary dolu): {$programs->count()}");

        $fixed = 0; $unchanged = 0; $shown = 0;
        foreach ($programs as $p) {
            $r = $parser->parse($p->admission_summary);
            $s = $r['summer']; $w = $r['winter'];

            // Sadece iki dönem net ayrıştıysa (ikisi de dolu ve farklı) güncelle.
            if ($s && $w && $s !== $w) {
                if ($shown < 10) {
                    $this->line(sprintf('  ✓ #%d  yaz=%s · kış=%s', $p->id, $s, $w));
                    $shown++;
                }
                if ($apply) {
                    Program::whereKey($p->id)->update([
                        'application_deadline_summer' => $s,
                        'application_deadline_winter' => $w,
                    ]);
                }
                $fixed++;
            } else {
                $unchanged++;
            }
        }

        $this->newLine();
        $this->line("Düzeltilen: {$fixed}  ·  Değişmeyen (metinde ayrım yok): {$unchanged}");
        if (! $apply && $fixed) $this->warn('Uygulamak için --apply ile çalıştırın.');

        return self::SUCCESS;
    }
}

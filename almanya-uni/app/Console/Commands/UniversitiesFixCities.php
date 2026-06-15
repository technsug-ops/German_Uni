<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\University;
use Illuminate\Console\Command;

/**
 * Şehirsiz (city_id null) aktif üniversitelere şehir atar — üni adındaki şehir
 * çekirdek-ismini mevcut aktif şehirlerle eşleştirerek. SADECE tek-net eşleşmede
 * atar (>1 aday → atlar). Çekirdek isim: "Halle (Saale)"→"Halle", "Esslingen am
 * Neckar"→"Esslingen". Tablomuzda olmayan kasabalar atlanır (uydurma şehir yok).
 *
 *   php artisan universities:fix-cities          → DRY-RUN
 *   php artisan universities:fix-cities --apply   → uygula
 */
class UniversitiesFixCities extends Command
{
    protected $signature = 'universities:fix-cities {--apply : Değişiklikleri yaz (varsayılan dry-run)}';

    protected $description = 'Şehirsiz aktif ünilere ad-eşleşmesiyle şehir atar (sadece tek-net eşleşme).';

    public function handle(): int
    {
        $apply = $this->option('apply');
        $this->info($apply ? '🔥 APPLY' : '🔍 DRY-RUN');

        // Aktif şehirler + çekirdek isim; uzun isim önce (Frankfurt am Main, Frankfurt'tan önce).
        $cities = City::where('is_active', 1)->get(['id', 'name_de'])
            ->map(fn ($c) => ['id' => $c->id, 'core' => $this->coreName($c->name_de), 'full' => $c->name_de])
            ->filter(fn ($c) => mb_strlen($c['core']) >= 4)
            ->sortByDesc(fn ($c) => mb_strlen($c['core']))->values();

        $unis = University::where('is_active', 1)->whereNull('city_id')->get(['id', 'slug', 'name_de']);

        $assigned = 0; $ambiguous = 0; $unmatched = 0;
        foreach ($unis as $u) {
            $hay = ' ' . mb_strtolower($u->name_de) . ' ';
            $hits = [];
            foreach ($cities as $c) {
                if (preg_match('/\b' . preg_quote(mb_strtolower($c['core']), '/') . '\b/u', $hay)) {
                    $hits[$c['id']] = $c;
                }
            }
            if (count($hits) === 1) {
                $c = reset($hits);
                $this->line("  ✓ {$u->name_de}  →  {$c['full']}");
                if ($apply) {
                    University::whereKey($u->id)->update(['city_id' => $c['id']]);
                }
                $assigned++;
            } elseif (count($hits) > 1) {
                $this->line("  ? {$u->name_de}  →  belirsiz (" . implode(' | ', array_map(fn ($c) => $c['full'], $hits)) . ')');
                $ambiguous++;
            } else {
                $unmatched++;
            }
        }

        $this->newLine();
        $this->line("Atanan: {$assigned}  ·  Belirsiz: {$ambiguous}  ·  Eşleşmeyen (şehir veride yok): {$unmatched}");
        if (! $apply && $assigned) {
            $this->warn('Uygulamak için --apply ile çalıştırın.');
        }

        return self::SUCCESS;
    }

    private function coreName(string $n): string
    {
        $n = preg_replace('/\s*\([^)]*\)/u', '', $n);                 // "(Saale)", "(Oder)"
        $n = preg_replace('/\s+(am|an der|an|im)\s+\S+.*$/iu', '', $n); // "am Neckar", "an der ..."
        return trim($n);
    }
}

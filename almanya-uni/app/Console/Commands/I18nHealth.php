<?php

namespace App\Console\Commands;

use App\Services\I18n\LocalizationHealthService;
use Illuminate\Console\Command;

/**
 * Çapraz-katman lokalizasyon senkron raporu (CLI). Admin paneli "Dil Durumu"
 * ile aynı servisi kullanır. CI gate olarak da kullanılabilir.
 */
class I18nHealth extends Command
{
    protected $signature = 'i18n:health';
    protected $description = 'İçerik senkron durumu: hangi içerik tipi hangi dilde eksik (lang + taxonomy + enrichment + blog)';

    public function handle(LocalizationHealthService $svc): int
    {
        $this->newLine();
        $this->info('=== İÇERİK SENKRON DURUMU (TR kaynak → EN/DE) ===');
        $this->newLine();

        $rows = [];
        foreach ($svc->report() as $r) {
            $en = $r['locales']['en'];
            $de = $r['locales']['de'];
            $rows[] = [
                $r['type'],
                $r['total'],
                "{$en['pct']}%  (eksik {$en['missing']})",
                "{$de['pct']}%  (eksik {$de['missing']})",
            ];
        }

        $this->table(['İçerik', 'Toplam', 'EN', 'DE'], $rows);
        $this->newLine();
        $this->line('Not: "eksik" = o dilde çevrilmemiş kayıt (UI satırında: TR sızıntısı).');

        return self::SUCCESS;
    }
}

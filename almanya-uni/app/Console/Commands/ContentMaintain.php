<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Sistematik içerik bakımı — güvenli/deterministik düzeltmeleri çalıştırır, sonra
 * denetim raporu basar. Günlük scheduler ile çalışır (partner:sync importundan SONRA),
 * böylece her yeni import'un getirdiği stale deadline / geçersiz süre kendiliğinden onarılır.
 *
 *   php artisan content:maintain           → DRY-RUN (fix'ler raporlar, audit basar)
 *   php artisan content:maintain --apply    → düzeltmeleri yazar + audit
 *
 * Daima SUCCESS döner: kalan yargı-gerektiren hatalar (harç/dupe/şehir) bakımı
 * fail ettirmesin — onlar audit log'unda görünür, ayrı ele alınır.
 */
class ContentMaintain extends Command
{
    protected $signature = 'content:maintain {--apply : Düzeltmeleri yaz (varsayılan dry-run)}';

    protected $description = 'Sistematik içerik bakımı: güvenli veri düzeltmeleri + denetim raporu.';

    public function handle(): int
    {
        $apply = $this->option('apply');
        $fixOpts = $apply ? ['--apply' => true] : [];

        $this->line('▶ programs:fix-deadlines');
        $this->call('programs:fix-deadlines', $fixOpts);

        $this->line('▶ programs:fix-data');
        $this->call('programs:fix-data', $fixOpts);

        $this->line('▶ programs:reparse-deadlines');
        $this->call('programs:reparse-deadlines', $fixOpts);

        // parse-deadlines flag'i ters (--dry-run); apply'da boş bırak (execute), değilse dry-run.
        $this->line('▶ programs:parse-deadlines');
        $this->call('programs:parse-deadlines', $apply ? [] : ['--dry-run' => true]);

        $this->line('▶ universities:fix-cities');
        $this->call('universities:fix-cities', $fixOpts);

        $this->line('▶ universities:fix-quickfacts');
        $this->call('universities:fix-quickfacts', $fixOpts + ['--samples' => 0]);

        $this->line('▶ programs:dedupe');
        $this->call('programs:dedupe', $fixOpts);

        $this->newLine();
        $this->line('▶ content:audit');
        $this->call('content:audit', ['--samples' => 0]);

        return self::SUCCESS;
    }
}

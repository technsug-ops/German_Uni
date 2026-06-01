<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Self-hosted analytics SIFIRLA — page_views tablosunu boşaltır.
 * Demo/dummy seed verisini temizleyip gerçek trafikle baştan başlamak için.
 * Sonrasında TrackPageView middleware gerçek ziyaretleri loglamaya devam eder.
 *
 *   php artisan analytics:reset --force
 */
class AnalyticsReset extends Command
{
    protected $signature = 'analytics:reset {--force : Onay sormadan sil}';
    protected $description = 'page_views tablosunu boşaltır (dummy verileri sil, gerçek trafikle başla)';

    public function handle(): int
    {
        $count = DB::table('page_views')->count();

        if ($count === 0) {
            $this->info('page_views zaten boş.');
            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("{$count} satır SİLİNECEK ve geri alınamaz. Emin misin?")) {
            $this->warn('İptal edildi.');
            return self::SUCCESS;
        }

        try {
            DB::table('page_views')->truncate();
        } catch (\Throwable $e) {
            // truncate FK/izin sorununda delete'e düş
            DB::table('page_views')->delete();
        }

        // Dashboard cache'lerini temizle (3 dk beklemeden sıfır görünsün)
        foreach ([
            'analytics:overview',
            'analytics:trend:14', 'analytics:trend:7', 'analytics:trend:30',
            'analytics:top_pages:7:10', 'analytics:top_pages:7:8',
            'analytics:devices:7', 'analytics:hourly:14', 'analytics:hourly:7',
            'analytics:referrers:7:8',
        ] as $k) {
            Cache::forget($k);
        }

        $this->info("✅ {$count} satır silindi. Dashboard sıfırlandı; gerçek trafik artık baştan loglanıyor.");
        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Services\TicketmasterImporter;
use Illuminate\Console\Command;

class ImportTicketmasterEvents extends Command
{
    protected $signature = 'events:import-ticketmaster
        {--city=* : Şehir adları (boşsa büyük öğrenci şehirleri)}
        {--size=100 : Segment başına çekilecek etkinlik sayısı (max 200)}';

    protected $description = 'Ticketmaster Discovery API\'den kültürel etkinlikleri (konser/tiyatro) /events\'e aktarır';

    /** Varsayılan: uluslararası öğrenci yoğun büyük şehirler. */
    private const DEFAULT_CITIES = [
        'Berlin', 'München', 'Hamburg', 'Köln', 'Frankfurt',
        'Stuttgart', 'Düsseldorf', 'Leipzig', 'Dresden', 'Hannover',
    ];

    public function handle(TicketmasterImporter $importer): int
    {
        if (! $importer->isConfigured()) {
            $this->error('TICKETMASTER_API_KEY .env\'de tanımlı değil — developer.ticketmaster.com\'dan al.');

            return self::FAILURE;
        }

        $cities = $this->option('city') ?: self::DEFAULT_CITIES;
        $size   = (int) $this->option('size');
        $total  = ['imported' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($cities as $city) {
            $s = $importer->importCity($city, ['size' => $size]);
            $this->line(sprintf('  %-12s +%d yeni · %d güncel · %d atlandı', $city, $s['imported'], $s['updated'], $s['skipped']));
            foreach ($s as $k => $v) {
                $total[$k] += $v;
            }
        }

        $this->info(sprintf('Bitti: %d yeni · %d güncel · %d atlandı', $total['imported'], $total['updated'], $total['skipped']));

        return self::SUCCESS;
    }
}

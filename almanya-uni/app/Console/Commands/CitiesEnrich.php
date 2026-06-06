<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Services\Enrichment\CityEnrichmentService;
use Illuminate\Console\Command;

class CitiesEnrich extends Command
{
    protected $signature = 'cities:enrich
        {--limit=0 : Sadece N şehir (0 = hepsi)}
        {--force : Yakın zamanda enrich edilenleri de yeniden işle}
        {--only-without : Sadece content_blocks NULL olan şehirler}
        {--min-unis=1 : En az N üniversitesi olan şehirler}
        {--slug= : Sadece tek bir şehir (slug ile)}
        {--sleep=2 : Gemini API\'yi yormamak için her enrich arasında bekleme (saniye)}';

    protected $description = 'Birden fazla şehir için Wikipedia + AI ile zengin content_blocks üret';

    public function handle(CityEnrichmentService $svc): int
    {
        $query = City::query()->orderByDesc(
            \App\Models\University::selectRaw('count(*)')
                ->whereColumn('city_id', 'cities.id')
                ->where('is_active', 1)
        );

        if ($slug = $this->option('slug')) {
            $query->where('slug', $slug);
        } else {
            // Campus-only şehirler (ör. Duisburg → tek üni'si Duisburg-Essen'in EK kampüsü;
            // birincil city_id = Essen) de DAHİL edilmeli. Yoksa whereHas('universities')
            // birincil üni 0 olduğu için bu şehirleri SONSUZA DEK atlar → sayfa "İçerik
            // Henüz Hazırlanmadı" kalır (Duisburg + leer/mönchengladbach/bocholt/
            // recklinghausen/senftenberg). Bkz [[multicampus_and_city_enrichment]].
            $minUnis = (int) $this->option('min-unis');
            $query->where(function ($q) use ($minUnis) {
                $q->whereHas('universities', fn ($u) => $u->where('is_active', 1), '>=', $minUnis)
                  ->orWhereHas('campusUniversities', fn ($u) => $u->where('universities.is_active', 1));
            });
            if ($this->option('only-without')) {
                $query->whereNull('content_blocks');
            }
        }

        if ($this->option('limit') > 0) {
            $query->limit((int) $this->option('limit'));
        }

        $cities = $query->get();
        $total = $cities->count();

        if ($total === 0) {
            $this->warn('Kriterlere uyan şehir bulunamadı.');
            return self::SUCCESS;
        }

        $this->info("📍 {$total} şehir enrich edilecek (sleep: {$this->option('sleep')}s)");
        $this->newLine();

        $success = 0;
        $skipped = 0;
        $failed = 0;
        $start = now();

        foreach ($cities as $i => $city) {
            $label = sprintf('[%d/%d] %s', $i + 1, $total, $city->name_de);
            $this->line($label . ' …');

            try {
                $result = $svc->enrich($city, (bool) $this->option('force'));
                if ($result['success']) {
                    $this->info("  ✅ {$result['blocks_count']} blok · " . ($result['tokens']['output'] ?? 0) . ' token');
                    $success++;
                } else {
                    $this->warn('  ⏭️  ' . ($result['error'] ?? 'Bilinmeyen hata'));
                    if (str_contains((string) ($result['error'] ?? ''), 'Yakın zamanda')) {
                        $skipped++;
                    } else {
                        $failed++;
                    }
                }
            } catch (\Throwable $e) {
                $this->error('  ❌ Exception: ' . substr($e->getMessage(), 0, 200));
                $failed++;
            }

            if ($i < $total - 1) {
                sleep((int) $this->option('sleep'));
            }
        }

        $duration = $start->diffInSeconds(now());
        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("✅ Başarılı: {$success}");
        $this->line("⏭️  Atlandı (yeni): {$skipped}");
        $this->line("❌ Başarısız: {$failed}");
        $this->info("⏱️  Süre: {$duration}s");
        $this->newLine();

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}

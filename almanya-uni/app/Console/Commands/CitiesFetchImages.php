<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Services\Enrichment\WikipediaExtract;
use Illuminate\Console\Command;

class CitiesFetchImages extends Command
{
    protected $signature = 'cities:fetch-images
        {--force : Mevcut image_url\'leri de güncelle}
        {--limit=0 : Sadece N şehir (0 = hepsi)}
        {--only-with-unis : Sadece üniversitesi olan şehirler}';

    protected $description = 'Tüm şehirlere Wikipedia hero görseli çek ve image_url kolonuna yaz';

    public function handle(WikipediaExtract $wiki): int
    {
        $query = City::query();
        if ($this->option('only-with-unis')) {
            $query->whereHas('universities', fn ($q) => $q->where('is_active', 1));
        }
        if (!$this->option('force')) {
            $query->whereNull('image_url');
        }
        if ($this->option('limit') > 0) {
            $query->limit((int) $this->option('limit'));
        }

        $cities = $query->orderBy('name_de')->get();
        $total = $cities->count();

        if ($total === 0) {
            $this->info('İşlenecek şehir yok.');
            return self::SUCCESS;
        }

        $this->info("Toplam {$total} şehir için görsel çekiliyor...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $found = 0;
        $missing = 0;

        foreach ($cities as $city) {
            $image = null;

            // 1. Önce makaledeki gerçek fotoğrafları dene (bayrak/arma değil manzara)
            foreach (['de', 'en'] as $lang) {
                $images = $wiki->fetchImages($city->name_de, $lang, 30, 800);
                $curated = $wiki->curateGallery($images, 5);
                if (!empty($curated)) {
                    $image = $curated[0]['url'];
                    break;
                }
            }

            // 2. Fallback — summary API thumbnail (genelde bayrak ama olsun)
            if (!$image) {
                foreach (['de', 'en'] as $lang) {
                    $r = $wiki->fetchByTitle($city->name_de, $lang);
                    if ($r && !empty($r['thumbnail_url'])) {
                        $image = $r['thumbnail_url'];
                        break;
                    }
                }
            }

            if ($image) {
                $city->update(['image_url' => $image]);
                $found++;
            } else {
                $missing++;
            }

            $bar->advance();
            usleep(200_000);
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Tamamlandı: {$found} bulundu, {$missing} bulunamadı.");
        return self::SUCCESS;
    }
}

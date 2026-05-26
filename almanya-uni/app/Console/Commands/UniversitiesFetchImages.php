<?php

namespace App\Console\Commands;

use App\Models\University;
use App\Services\Enrichment\WikipediaExtract;
use Illuminate\Console\Command;

class UniversitiesFetchImages extends Command
{
    protected $signature = 'universities:fetch-images
        {--force : Mevcut image_url\'leri de güncelle}
        {--limit=0 : Sadece N üni (0 = hepsi)}
        {--top-by-students=0 : Sadece öğrenci sayısına göre en büyük N üni}
        {--only-active : Sadece is_active=1 olanlar}';

    protected $description = 'Tüm üniversitelere Wikipedia hero görseli çek (kampüs fotoğrafı tercihen)';

    public function handle(WikipediaExtract $wiki): int
    {
        if (method_exists(University::class, 'disableSearchSyncing')) {
            University::disableSearchSyncing();
        }

        $query = University::query();
        if ($this->option('only-active')) {
            $query->where('is_active', 1);
        }
        if (!$this->option('force')) {
            $query->whereNull('image_url');
        }
        if ((int) $this->option('top-by-students') > 0) {
            $query->whereNotNull('student_count')
                ->orderByDesc('student_count')
                ->limit((int) $this->option('top-by-students'));
        } else {
            $query->orderByDesc('student_count');
        }
        if ((int) $this->option('limit') > 0 && (int) $this->option('top-by-students') === 0) {
            $query->limit((int) $this->option('limit'));
        }

        $unis = $query->get(['id', 'name_de', 'slug', 'wikipedia_url_de', 'wikipedia_url_en']);
        $total = $unis->count();

        if ($total === 0) {
            $this->info('İşlenecek üniversite yok.');
            return self::SUCCESS;
        }

        $this->info("Toplam {$total} üniversite için görsel çekiliyor...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $found = 0;
        $missing = 0;

        foreach ($unis as $uni) {
            $image = null;

            // 1. Wikipedia URL'leri varsa onlardan
            foreach (['de', 'en'] as $lang) {
                $wikiUrl = $uni->{'wikipedia_url_' . $lang};
                $title = null;
                if ($wikiUrl && preg_match('#/wiki/(.+)$#', $wikiUrl, $m)) {
                    $title = urldecode($m[1]);
                }
                $title = $title ?: $uni->name_de;

                $images = $wiki->fetchImages($title, $lang, 25, 800);
                $curated = $wiki->curateGallery($images, 3);
                if (!empty($curated)) {
                    $image = $curated[0]['url'];
                    break;
                }
            }

            // 2. Fallback — summary API thumbnail
            if (!$image) {
                foreach (['de', 'en'] as $lang) {
                    $r = $wiki->fetchByTitle($uni->name_de, $lang);
                    if ($r && !empty($r['thumbnail_url'])) {
                        $image = $r['thumbnail_url'];
                        break;
                    }
                }
            }

            if ($image) {
                $uni->update(['image_url' => $image]);
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

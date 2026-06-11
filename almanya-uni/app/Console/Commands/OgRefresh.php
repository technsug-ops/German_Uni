<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\Post;
use App\Models\Profession;
use App\Models\Program;
use App\Models\Scholarship;
use App\Models\University;
use Illuminate\Console\Command;

/**
 * OG görselleri /og/{type}/{slug}.png ilk istekte üretilip
 * storage/app/public/og/{brand}/ altına SÜRESİZ cache'lenir. İçerik başlığı
 * değişince cache bayatlar. Bu komut son N günde güncellenen içeriğin OG
 * cache'ini siler → sonraki istekte (sosyal paylaşım crawl'ı) TAZE üretilir.
 * Üretim mantığını kopyalamaz; sadece geçersiz kılar. Her iki brand için.
 */
class OgRefresh extends Command
{
    protected $signature = 'og:refresh
        {--days=3 : Son N günde güncellenen içeriğin OG cache\'ini tazele}
        {--all : Tüm OG cache\'ini sil (tam yeniden üretim)}';

    protected $description = 'Güncellenen içeriğin bayat OG görsel cache\'ini siler → sonraki istekte taze üretilir (her brand).';

    /** og.image route type => Model */
    private const TYPES = [
        'post'        => Post::class,
        'university'  => University::class,
        'program'     => Program::class,
        'profession'  => Profession::class,
        'scholarship' => Scholarship::class,
        'city'        => City::class,
        'field'       => FieldOfStudy::class,
    ];

    public function handle(): int
    {
        $brandKeys = array_keys(config('brand.brands', [])) ?: ['almanyauni'];
        $deleted = 0;

        if ($this->option('all')) {
            foreach ($brandKeys as $bk) {
                foreach (glob(storage_path("app/public/og/{$bk}/*.png")) ?: [] as $f) {
                    if (@unlink($f)) $deleted++;
                }
            }
            $this->info("Tüm OG cache silindi: {$deleted} dosya.");
            return self::SUCCESS;
        }

        $since = now()->subDays((int) $this->option('days'));

        foreach (self::TYPES as $type => $model) {
            try {
                $slugs = $model::query()
                    ->where('updated_at', '>=', $since)
                    ->pluck('slug')
                    ->filter()
                    ->all();
            } catch (\Throwable $e) {
                $this->warn("{$type}: atlandı ({$e->getMessage()})");
                continue;
            }

            foreach ($slugs as $slug) {
                foreach ($brandKeys as $bk) {
                    $path = storage_path("app/public/og/{$bk}/{$type}-{$slug}.png");
                    if (is_file($path) && @unlink($path)) {
                        $deleted++;
                    }
                }
            }

            if ($slugs) {
                $this->line(sprintf('%-12s %d içerik tarandı', $type, count($slugs)));
            }
        }

        $this->info("Bayat OG cache silindi: {$deleted} dosya (son {$this->option('days')} gün).");

        return self::SUCCESS;
    }
}

<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

/**
 * Featured + gelecek tarihli etkinlikleri production DB'ye seed eder.
 *
 * Data source: database/seeders/data/featured-events.json — local'de Filament
 * veya tinker ile oluşturulan etkinliklerin export'u.
 *
 * Idempotent: slug çakışması → update; aksi halde create.
 *
 * Komut: php artisan db:seed --class=FeaturedEventsSeeder
 */
class FeaturedEventsSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/featured-events.json');

        if (! file_exists($path)) {
            $this->command->error('Veri dosyası bulunamadı: ' . $path);
            return;
        }

        $events = json_decode(file_get_contents($path), true);

        if (! is_array($events) || empty($events)) {
            $this->command->error('JSON parse hatası veya boş veri.');
            return;
        }

        $created = 0;
        $updated = 0;

        foreach ($events as $data) {
            $existing = Event::where('slug', $data['slug'])->first();

            if ($existing) {
                $existing->update($data);
                $this->command->info("🔄 Güncellendi: {$data['title_tr']}");
                $updated++;
            } else {
                Event::create($data);
                $this->command->info("✅ Eklendi: {$data['title_tr']}");
                $created++;
            }
        }

        $this->command->newLine();
        $this->command->info("━━━━━━━━━━━━━━━━━━━━━━");
        $this->command->info("Created: {$created}, Updated: {$updated}");
    }
}

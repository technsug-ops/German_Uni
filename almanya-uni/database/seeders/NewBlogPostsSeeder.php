<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

/**
 * 5 nasıl-yapılır blog yazısını production DB'ye seed eder.
 *
 * Data source: database/seeders/data/new-blog-posts.json (Content Factory
 * pipeline çıktısı, local'de üretilmiş + 2026-05-28 yayına alınmış).
 *
 * Idempotent: slug çakışması varsa post güncellenir (üzerine yazılır).
 *
 * Komut: php artisan db:seed --class=NewBlogPostsSeeder
 */
class NewBlogPostsSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/new-blog-posts.json');

        if (! file_exists($path)) {
            $this->command->error('Veri dosyası bulunamadı: ' . $path);
            return;
        }

        $posts = json_decode(file_get_contents($path), true);

        if (! is_array($posts) || empty($posts)) {
            $this->command->error('JSON parse hatası veya boş veri.');
            return;
        }

        $created = 0;
        $updated = 0;

        foreach ($posts as $data) {
            // user_id production'da bizim admin ile farklı olabilir → ilk admin'i bul
            $userId = $data['user_id'] ?? 1;

            $existing = Post::where('slug', $data['slug'])->first();

            if ($existing) {
                $existing->update($data);
                $this->command->info("🔄 Güncellendi: {$data['title']}");
                $updated++;
            } else {
                Post::create($data);
                $this->command->info("✅ Eklendi: {$data['title']}");
                $created++;
            }
        }

        $this->command->newLine();
        $this->command->info("━━━━━━━━━━━━━━━━━━━━━━");
        $this->command->info("Created: {$created}, Updated: {$updated}");
    }
}

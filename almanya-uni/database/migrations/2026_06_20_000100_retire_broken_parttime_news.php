<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;

/**
 * Otomatik üretilmiş bozuk haberi yayından kaldırır: scraper Google News
 * çerez-onay sayfasına takıldığı için içerik anlamsız bir meta-metne dönüşmüş
 * ("aranan haber linkine ulaşılamıyor..."). Yerine gerçek içerik yarı zamanlı
 * iş blogunda (part-time-jobs-germany-international-students-guide) yayınlandı.
 *
 * Slug-bazlı + idempotent. Silmek yerine yayından kaldırır (geri alınabilir).
 */
return new class extends Migration
{
    private string $slug = 'part-time-jobs-in-germany-the-news-youre-looking-for-isnt';

    public function up(): void
    {
        $post = Post::where('slug', $this->slug)->first();
        if (! $post) {
            return; // lokal DB'de yok; prod'da /admin/ops/migrate ile uygulanır
        }

        // Aynı çeviri grubundaki (TR/EN/DE) tüm sürümleri yayından kaldır.
        $query = $post->translation_group_id
            ? Post::where('translation_group_id', $post->translation_group_id)
            : Post::where('id', $post->id);

        $query->update(['is_published' => false, 'published_at' => null]);
    }

    public function down(): void
    {
        // Bilinçli no-op: bozuk içeriği geri yayınlamak istemiyoruz.
    }
};

<?php

use App\Models\Post;
use App\Support\StudentStatisticsReport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * YAYIN — Almanya Öğrenci İstatistikleri Raporu 2026 (TR/EN/DE) + Batch 3 atıflarının rapora bağlanması.
 * Yayın sahibi onayı 2026-10-11 (metin/yayın). Taslaklar 2026_10_11_000200 ile eklenir; bu migration deploy'da:
 *   1) üç dil kaydını tek transaction'da is_published = true, published_at = o anki zaman yapar (gerçek ilk web yayın
 *      tarihi; üçü aynı an; Eylül 2026 raporun hazırlanma dönemidir, içerikte öyle kalır),
 *   2) İngilizce yüksek lisans rehberinin TR/EN/DE sürümlerinde rapor adını aynı dildeki rapora bağlar
 *      (yalnız bağlantı; rehberlerin published_at'i ve cümleleri değişmez). /english-taught kutusu yayın durumuna göre
 *      kendiliğinden bağlanır (StudentStatisticsReport::nameHtml).
 *
 * Güvenlik: kayıtlar sayısal ID ile değil çeviri grubu + locale + slug ile bulunur. Üç rapor kaydından biri yoksa, slug
 * beklenenden farklıysa, yazarı users.slug = sule-demirel değilse, bir rehber bulunamazsa ya da rehberde ne düz ne
 * bağlantılı atıf tam olarak bir kez yoksa RuntimeException → hiçbir şey yazılmaz. Zaten yayında ve bağlı → no-op
 * (published_at / updated_at değişmez). Testing ortamında (CI) hedef rehberler yoksa "iş yok" sayılır; production'da hatadır.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }
        $def = StudentStatisticsReport::definition();
        $group = $def['translation_group_id'];
        $problems = [];

        $authorId = DB::table('users')->where('slug', $def['author_slug'])->where('is_author', true)->value('id');
        if (! $authorId) {
            throw new RuntimeException("Rapor yayımlanmadı: yazar '{$def['author_slug']}' bulunamadı.");
        }

        $reports = [];
        foreach ($def['posts'] as $locale => $p) {
            $found = Post::where('translation_group_id', $group)->where('locale', $locale)->get();
            if ($found->count() !== 1) {
                $problems[] = "rapor {$locale}: " . ($found->isEmpty() ? 'kayıt yok (önce taslak migration\'ı)' : 'birden fazla kayıt');
                continue;
            }
            $post = $found->first();
            if ($post->slug !== $p['slug'] || $post->title !== $p['title']) {
                $problems[] = "rapor {$locale}: slug/başlık beklenen taslakla uyuşmuyor ({$post->slug})";
            }
            if ((int) $post->user_id !== (int) $authorId) {
                $problems[] = "rapor {$locale}: yazar {$def['author_slug']} değil";
            }
            $reports[$locale] = $post;
        }
        if ($problems) {
            throw new RuntimeException('Rapor yayımlanmadı, hiçbir şey yazılmadı: ' . implode('; ', $problems));
        }
        if (app()->environment('testing') && ! $this->anyGuideExists($def['guide_citations'])) {
            return;
        }

        $guides = [];
        foreach ($def['guide_citations'] as $locale => $c) {
            $found = Post::where('locale', $locale)->whereIn('slug', $c['slugs'])->get();
            if ($found->count() !== 1) {
                $problems[] = "rehber {$locale}: " . ($found->isEmpty() ? 'bulunamadı' : 'birden fazla aday');
                continue;
            }
            $guide = $found->first();
            $md = (string) $guide->content_md;
            if (substr_count($md, $c['linked']) === 1) {
                continue; // zaten bağlı
            }
            if (substr_count($md, $c['plain']) !== 1) {
                $problems[] = "rehber {$locale}/{$guide->slug}: rapor atfı tam olarak bir kez bulunamadı";
                continue;
            }
            $guide->content_md = str_replace($c['plain'], $c['linked'], $md);
            $guides[] = $guide;
        }
        if ($problems) {
            throw new RuntimeException('Rapor yayımlanmadı, hiçbir şey yazılmadı: ' . implode('; ', $problems));
        }

        $toPublish = array_filter($reports, fn (Post $p) => ! ($p->is_published && $p->published_at));
        if (! $toPublish && ! $guides) {
            return; // no-op
        }

        $now = now();
        DB::transaction(function () use ($toPublish, $guides, $now) {
            foreach ($toPublish as $post) {
                $post->is_published = true;
                $post->published_at = $now;
                $post->save();
            }
            foreach ($guides as $guide) {
                $guide->save();
            }
        });
        StudentStatisticsReport::flush();
    }

    private function anyGuideExists(array $citations): bool
    {
        foreach ($citations as $locale => $c) {
            if (Post::where('locale', $locale)->whereIn('slug', $c['slugs'])->exists()) {
                return true;
            }
        }

        return false;
    }

    public function down(): void
    {
        // Geri alma yok — yayın kararı panelden geri alınabilir (is_published), bağlantılar içerikte kalır.
    }
};

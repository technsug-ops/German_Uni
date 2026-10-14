<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Yapay zekayla üretilmiş kapak görsellerini kaldırır (2026-10-14, docs/AI_VISUAL_AUDIT_2026-10-14.md §1).
 *
 * Kapak kaynakları:
 *  - blog/{slug}.jpg (kök dizin) → BlogSplit* komutları, Pollinations (FLUX) ile üretilip storage/app/public'e indirildi.
 *  - /images/news/{id}-{h}.png  → NewsService::illustration(), Gemini/Imagen illüstrasyonu.
 *  - doğrudan image.pollinations.ai URL'si (indirme başarısızsa yazılan yedek).
 * Bu yolların hepsi yapay zeka üretimi; gerçek fotoğraf bu yollara yazılmıyor.
 *
 * Kapak boşalınca yazı/haber sayfası görselsiz açılır, paylaşım görseli og.image (yazılı kart) olur.
 * Slug, URL ve diğer alanlar değişmez. Tekrar çalıştırma no-op. Geri alma yok (dosyalar silinir).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Yalnız blog/ kök dizini: admin yüklemeleri blog/featured/ altına gider ve dokunulmaz.
        $isAi = fn ($q) => $q->where(fn ($w) => $w->where('featured_image', 'like', 'blog/%')->where('featured_image', 'not like', 'blog/%/%'))
            ->orWhere('featured_image', 'like', '/images/news/%')
            ->orWhere('featured_image', 'like', '%pollinations.ai%');

        $paths = DB::table('posts')->where($isAi)->pluck('featured_image')->unique()->values();

        $posts = DB::table('posts')->where($isAi)->update(['featured_image' => null, 'featured_image_caption' => null]);

        $candidates = 0;
        if (DB::getSchemaBuilder()->hasColumn('news_candidates', 'image_url')) {
            $candidates = DB::table('news_candidates')
                ->where(fn ($q) => $q->where('image_url', 'like', '/images/news/%')->orWhere('image_url', 'like', '%pollinations.ai%'))
                ->update(['image_url' => null]);
        }

        $deleted = 0;
        foreach ($paths as $p) {
            if (str_starts_with($p, 'blog/') && Storage::disk('public')->exists($p)) {
                Storage::disk('public')->delete($p);
                $deleted++;
            } elseif (str_starts_with($p, '/images/news/') && is_file(public_path(ltrim($p, '/')))) {
                @unlink(public_path(ltrim($p, '/')));
                $deleted++;
            }
        }

        Log::info("AI kapakları kaldırıldı: posts={$posts}, news_candidates={$candidates}, dosya={$deleted}, yol=" . $paths->count());
    }

    public function down(): void
    {
        // Geri alma yok — dosyalar silindi.
    }
};

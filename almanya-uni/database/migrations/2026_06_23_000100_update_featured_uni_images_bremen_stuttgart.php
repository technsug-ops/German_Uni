<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Featured Universities görselleri: Universität Bremen + Universität Stuttgart
 * daha iyi/resmî görsellerle değiştirilir (kullanıcı talebi, 2026-06-23).
 *
 * ÖNEMLİ — getImageUrlAttribute CACHE-FIRST çalışır: public/img/cache/unis/{slug}.webp
 * varsa onu sunar ve DB image_url'i YOK SAYAR. Cache dizini gitignore'lu ve prod'da
 * images:cache-hot ile üretiliyor → eski cache prod'da yaşamaya devam eder.
 * Bu yüzden iki adım gerekir:
 *   (1) DB image_url'i yeni URL ile güncelle,
 *   (2) bayat WebP cache dosyasını SİL → accessor canlı olarak yeni DB URL'sine düşer;
 *       sonraki images:cache-hot turu yeni görseli yeniden cache'ler.
 *
 * URL uyumu: Bremen (uni-bremen.de, wikimedia-dışı) → wikimedia_thumb pass-through.
 * Stuttgart (upload.wikimedia .../1280px-Campus_Vaihingen.jpg) → thumb/original helper'ları destekler.
 */
return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'universitat-bremen-q500692'    => 'https://www.uni-bremen.de/fileadmin/_processed_/7/7/csm_16_9_DSC03759_2026_01_19_UFO_c_Leona_Hofmann_Universitaet_Bremen_df70ae5ff3.jpg',
            // Special:FilePath formatı: accessor 600px'e indirince upload-thumb 400 verir;
            // Special:FilePath?width=600 ise 200 döner (ve diğer üni kayıtlarıyla tutarlı).
            'universitat-stuttgart-q122453' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Campus%20Vaihingen.jpg?width=1280',
        ];

        foreach ($map as $slug => $url) {
            DB::table('universities')->where('slug', $slug)->update([
                'image_url'  => $url,
                'updated_at' => now(),
            ]);

            // Bayat WebP cache'i sil (cache-first accessor eski görseli sunmasın)
            $cache = public_path("img/cache/unis/{$slug}.webp");
            if (is_file($cache)) {
                @unlink($cache);
            }
        }
    }

    public function down(): void
    {
        // Görsel değişikliği bilinçli; geri alınmaz (no-op).
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Kırık görsel temizliği (geri bildirim): bazı news-import postlarının
 * featured_image / içerik <img>'leri storage/images/news/ altında ÜRETİLMEMİŞ
 * dosyaya işaret ediyor → kırık görsel (alt-text) görünüyor.
 *
 * Bu migration PROD'da çalışınca (file_exists prod dosya sistemini kontrol eder):
 *  - featured_image dosyası YOKSA → null (blog/show foto'yu sadece varsa basar).
 *  - content_html içindeki kırık /storage/images/news/ <img>'leri kaldırır.
 * SADECE images/news/ deseni + GERÇEKTEN eksik dosyalar hedeflenir (mevcut/geçerli
 * görsellere dokunmaz). DB::table → Scout-safe.
 */
return new class extends Migration
{
    public function up(): void
    {
        $tbl = 'posts';

        // 1) featured_image: images/news/ + dosya yok → null
        $rows = DB::table($tbl)
            ->whereNotNull('featured_image')->where('featured_image', '!=', '')
            ->where('featured_image', 'not like', 'http%')
            ->where('featured_image', 'like', '%images/news/%')
            ->get(['id', 'featured_image']);
        foreach ($rows as $r) {
            $path = public_path('storage/' . ltrim($r->featured_image, '/'));
            if (! is_file($path)) {
                DB::table($tbl)->where('id', $r->id)->update(['featured_image' => null, 'updated_at' => now()]);
            }
        }

        // 2) content_html: kırık /storage/images/news/ <img> → kaldır (dosya yoksa)
        $rows = DB::table($tbl)
            ->where('content_html', 'like', '%/storage/images/news/%')
            ->get(['id', 'content_html']);
        foreach ($rows as $r) {
            $html = (string) $r->content_html;
            $new = preg_replace_callback('~<img\b[^>]*\bsrc="[^"]*?/storage/images/news/([^"]+)"[^>]*>~i', function ($m) {
                $file = public_path('storage/images/news/' . $m[1]);
                return is_file($file) ? $m[0] : ''; // dosya yoksa img'i sil
            }, $html);
            if ($new !== $html) {
                DB::table($tbl)->where('id', $r->id)->update(['content_html' => $new, 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        // Geri alınamaz.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * YEDİNCİ GEÇİŞ — önceki geçişler prod'da GÖRÜNÜRDE hiç iş yapmadı: canlıda kalan ölü linkler
 * duruyordu, oysa aynı komut lokalde onarıyordu. En olası sebep: komut TÜM yazıları (content_md
 * + content_html) tek seferde belleğe alıyordu → deploy'un response-sonrası migrate adımında
 * bellek/süre sınırına takılıp sessizce ölüyordu (fatal error yakalanamaz, deploy yeşil kalır).
 *
 * Bu geçişte: komut chunkById(100) ile çalışıyor + burada limitler yükseltiliyor + çıktı
 * storage/logs/link-repair.log'a yazılıyor ki prod'da ne olduğu sonradan görülebilsin.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts') || DB::table('posts')->count() < 100) {
            return; // CI'ın boş test DB'sinde anlamsız
        }

        @ini_set('memory_limit', '512M');
        @set_time_limit(0);

        try {
            Artisan::call('content:repair-post-links', ['--demote' => true]);
            $out = Artisan::output();
        } catch (\Throwable $e) {
            $out = 'FAIL: ' . $e->getMessage();
            report($e);
        }

        // Prod'da SSH yok → izi dosyaya bırak (admin /admin/ops/link-audit ile de görebilir).
        try {
            @file_put_contents(
                storage_path('logs/link-repair.log'),
                '[' . now()->toDateTimeString() . '] ' . PHP_EOL . $out . PHP_EOL,
                FILE_APPEND
            );
        } catch (\Throwable $e) {
            // log yazılamıyorsa migration'ı düşürme
        }
    }

    public function down(): void
    {
        // İçerik yeniden yazımı geri alınamaz (eski linkler zaten 404'tü) — bilinçli no-op.
    }
};

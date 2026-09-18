<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Görünürlük ölçütü düzeltildikten sonraki onarım turu.
 *
 * TEŞHİS (prod migrate:status + /admin/ops/link-audit çıktısıyla kesinleşti): önceki geçişlerin
 * hepsi prod'da ÇALIŞTI ama "0 ölü link" raporladı — çünkü komut hedefi yalnızca `is_published`
 * ile ölçüyordu. Site ise `Post::scopePublished()` ile `published_at` dolu VE geçmiş olmasını da
 * şart koşuyor. Yayına girmemiş (published_at NULL / gelecek) yazılara giden linkler bu yüzden
 * "sağlam" sayılıyor, ziyaretçi 404 görüyordu. Komut artık siteyle aynı ölçütü kullanıyor.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts') || DB::table('posts')->count() < 100) {
            return; // CI'ın boş test DB'si
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

        try {
            @file_put_contents(
                storage_path('logs/link-repair.log'),
                '[' . now()->toDateTimeString() . '] görünürlük düzeltmesi sonrası' . PHP_EOL . $out . PHP_EOL,
                FILE_APPEND
            );
        } catch (\Throwable $e) {
            //
        }
    }

    public function down(): void
    {
        //
    }
};

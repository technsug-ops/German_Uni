<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * page_views: açık rıza zorunluluğundan (7cf259a, 2026-09-23 akşamı canlıda) ÖNCE toplanan satırları siler.
 *
 * Neden: 2026-06-01 → 2026-09-23 arası ~16,5 milyon satır / ~4,5 GB, rıza aranmadan kaydedildi (büyük kısmı bot)
 * ve gizlilik politikasının "rıza + 90 günde anonimleştirme" taahhüdüyle çelişiyordu. 24 Eylül'den beri günde
 * 0–85 satır geliyor. Karar: kullanıcı onayıyla tamamen silme (tam yedek: 2026-10-08 phpMyAdmin export).
 *
 * Yöntem (MySQL/MariaDB): 16 milyon satırlık DELETE yerine tutulacak birkaç yüz satır yeni tabloya kopyalanır
 * (idx_pv_created ile), tablolar RENAME ile atomik yer değiştirir, eski tablo DROP edilir → saniyeler, disk hemen
 * boşalır. Kopya ile RENAME arasında gelen tekil kayıt kaybolabilir (günde birkaç onaylı ziyaret, kabul edilebilir).
 * Idempotent: eski satır yoksa hiçbir şey yapmaz.
 */
return new class extends Migration
{
    private const CUTOFF = '2026-09-24 00:00:00';

    public function up(): void
    {
        if (! Schema::hasTable('page_views') || ! DB::table('page_views')->where('created_at', '<', self::CUTOFF)->exists()) {
            return;
        }

        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::table('page_views')->where('created_at', '<', self::CUTOFF)->delete();

            return;
        }

        DB::statement('DROP TABLE IF EXISTS page_views_keep');
        DB::statement('DROP TABLE IF EXISTS page_views_purged');
        DB::statement('CREATE TABLE page_views_keep LIKE page_views');
        DB::statement('INSERT INTO page_views_keep SELECT * FROM page_views WHERE created_at >= ?', [self::CUTOFF]);
        DB::statement('RENAME TABLE page_views TO page_views_purged, page_views_keep TO page_views');
        DB::statement('DROP TABLE page_views_purged');
    }

    public function down(): void
    {
        // Silinen veri geri getirilmez; gerekirse 2026-10-08 yedeğinden elle yüklenir.
    }
};

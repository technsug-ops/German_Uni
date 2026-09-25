<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Mükerrer temizliği — uni-assist rehberinin eski TR kopyasını yayından kaldırır.
 *
 * Canlı teşhis (2026-09-25): iki TR yazı %97,9 aynı, ikisi de 200/self-canonical.
 *   winner: uni-assist-application-vpd-common-rejection-reasons-solutions (TR/EN/DE küme, sitemap)
 *   loser : uniassist-vpd-reddedilme-cozumler (kümesiz, sitemap dışı)
 * blog_redirects'te tr satırı zaten loser → winner; yazı yayında olduğu için hiç tetiklenmiyordu.
 * Yayından kalkınca BlogController o satırla TEK 301 verir (aynı dil).
 *
 * Silme yok, slug değişmez, updated_at'e dokunulmaz. Doğrulama tutmazsa exception.
 */
return new class extends Migration
{
    private const WINNER = 'uni-assist-application-vpd-common-rejection-reasons-solutions';

    private const LOSER = 'uniassist-vpd-reddedilme-cozumler';

    public function up(): void
    {
        if (! Schema::hasTable('posts') || ! Schema::hasTable('blog_redirects')) {
            return;
        }

        $loser = DB::table('posts')->where('locale', 'tr')->where('slug', self::LOSER)->first();
        if (! $loser) {
            Log::info('uniassist retire: eski TR yazı yok, atlandı');

            return;
        }

        $winner = DB::table('posts')->where('locale', 'tr')->where('slug', self::WINNER)->first();
        if (! $winner || ! $winner->is_published || $winner->published_at === null || $winner->published_at > now()) {
            throw new RuntimeException('uniassist retire: kazanan TR yazı yok ya da yayında değil — HİÇBİR ŞEY YAZILMADI');
        }

        $rows = DB::table('blog_redirects')->where('from_slug', self::LOSER)->where('locale', 'tr')->get();
        if ($rows->count() > 1 || ($rows->count() === 1 && $rows->first()->to_slug !== self::WINNER)) {
            throw new RuntimeException('uniassist retire: tr yönlendirme kaydı kazanana gitmiyor — HİÇBİR ŞEY YAZILMADI');
        }

        DB::transaction(function () use ($loser, $rows) {
            if ($rows->isEmpty()) {
                DB::table('blog_redirects')->insert([
                    'from_slug' => self::LOSER, 'to_slug' => self::WINNER, 'locale' => 'tr',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            if ($loser->is_published) {
                // Sorgu oluşturucu updated_at'i kendiliğinden değiştirmez.
                DB::table('posts')->where('id', $loser->id)->update(['is_published' => false]);
            }
        });

        echo "uniassist retire: #{$loser->id} yayından kaldırıldı → /tr/blog/" . self::WINNER . "\n";
    }

    public function down(): void
    {
        // Bilinçli olarak boş: eski kopyayı geri yayına almak mükerrer içeriği geri getirir.
    }
};

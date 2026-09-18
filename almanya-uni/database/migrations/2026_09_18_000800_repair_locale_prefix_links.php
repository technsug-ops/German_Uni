<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ALTINCI GEÇİŞ: yanlış DİL ÖNEKİYLE verilmiş linkler (çeviri sırasında önek mekanik
 * değiştirilip slug aynı bırakılmış: /en/blog/<tr-slug>) canlıda 404 veriyor. Komut bunları
 * zaten düzeltiyordu ama sessizce — artık sayıp raporluyor, denetim çıktısı gerçeği gösteriyor.
 *
 * (Önceki) BEŞİNCİ GEÇİŞ: canlıda hâlâ 13 ölü link duruyordu. Sebep: bazı yazılarda content_html,
 * content_md'nin birebir çıktısı DEĞİL (html ayrı üretilmiş/düzenlenmiş) → md temiz olsa da
 * html'de ölü linkler kalıyordu. Komut artık content_html'i de bağımsız tarıyor (md'den
 * yeniden türetmeden) ve yanlış tip altında verilmiş entity linklerini doğru tipe çeviriyor
 * (ör. /cities/bayern-q980 → /states/bayern).
 *
 * (Önceki) DÖRDÜNCÜ GEÇİŞ: canlı doğrulamada, gövdesinde MARKDOWN yerine HAM <a href> bulunan
 * yazıların linkleri onarılmadan kalmıştı (markdown deseni eşleşmiyordu). Komut artık
 * ham anchor'ları ve content_md'si boş olup yalnız content_html'i olan yazıları da işliyor.
 *
 * (Önceki) ÜÇÜNCÜ GEÇİŞ: komut artık /faq/{konu} ve /faq/{konu}/{slug} yollarını da gerçek SSS
 * kayıtlarına karşı doğruluyor (ölü konu yolları canlıda 404 veriyordu). Önceki geçişler
 * bu kontrolden önce koştu → tekrar çalıştırılıyor. Komut idempotent.
 *
 * (Önceki) İKİNCİ GEÇİŞ: `content:repair-post-links` artık blog/şehir/üni/program dışındaki iç yolları da
 * (ör. /tools/…, /araclar/…) gerçek route'a karşı doğruluyor. İlk geçiş (000300) bu kontrolden
 * önce koştuğu için ölü araç linkleri yerinde kaldı; bu migration komutu yeni haliyle tekrar
 * çalıştırır. Komut idempotent — onarılacak bir şey yoksa hiçbir yazıya dokunmaz.
 *
 * Tüm yazılardaki ölü iç linkleri prod veritabanına karşı onarır (`content:repair-post-links`).
 *
 * NEDEN MIGRATION: prod'da SSH yok, artisan elle çalıştırılamıyor; deploy hattında
 * (`public/_deploy.php` → `migrate --force`) koşan tek yer migration'lar.
 *
 * NEDEN GEREKLİ: yazılar zaman içinde İngilizce slug'lara taşındı ama onlara link veren
 * gövdeler güncellenmedi → canlıda yüzlerce iç link 404. Mevcut `content:resolve-post-links`
 * yalnızca route şekline baktığı için bunları göremiyor (detay komutun başlığında).
 *
 * GÜVENLİK: komut yalnızca GERÇEKTEN ölü linklere dokunur; güvenli eşleşme bulamadığında
 * `--demote` ile linki düz metne indirir (404 bırakmaz). Küçük/boş bir veritabanında
 * (CI test DB'si) hiç çalışmaz — orada "ölü" görünen her link aslında sadece seed edilmemiştir.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }

        // Gerçek içerik veritabanı mı? CI'ın migrate:fresh test DB'sinde yazıların çoğu yok;
        // orada çalışmak anlamsız (ve yanlışlıkla link düşürebilir).
        if (DB::table('posts')->count() < 100) {
            return;
        }

        try {
            Artisan::call('content:repair-post-links', ['--demote' => true]);
        } catch (\Throwable $e) {
            // Deploy'u bu yüzden düşürme: içerik onarımı best-effort, migration'ın kendisi kritik değil.
            report($e);
        }
    }

    public function down(): void
    {
        // İçerik yeniden yazımı geri alınamaz (eski linkler zaten 404'tü). Geri almak gerekirse
        // yedekten dönülür; bu yüzden bilinçli olarak no-op.
    }
};

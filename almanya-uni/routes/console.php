<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Partner API Sync
|--------------------------------------------------------------------------
| Günde bir kez partner kuruluştan delta sync.
| .env: PARTNER_SYNC_SCHEDULE=daily|hourly|twice-daily
|       PARTNER_API_BASE_URL + PARTNER_API_KEY zorunlu, yoksa command FAIL döner.
*/
$partnerFreq = config('services.partner.sync_schedule', 'daily');

$partnerSync = Schedule::command('partner:sync')
    ->withoutOverlapping(60)
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/partner-sync.log'));

match ($partnerFreq) {
    'hourly'      => $partnerSync->hourly(),
    'twice-daily' => $partnerSync->twiceDaily(3, 15),
    'weekly'      => $partnerSync->weekly(),
    default       => $partnerSync->dailyAt('03:30'),
};

/*
|--------------------------------------------------------------------------
| İçerik Bakımı — sistematik veri self-heal (günlük)
|--------------------------------------------------------------------------
| 03:50 — partner:sync (03:30) importundan SONRA. Her import yeni stale deadline /
| geçersiz süre getirebilir; bu bakım onları yıllık-döngü rollover + null ile onarır
| ve denetim raporunu log'a basar. İdempotent, daima SUCCESS (kalan yargı-gerektiren
| hatalar bakımı fail ettirmez — content-maintain.log'da görünür).
*/
Schedule::command('content:maintain --apply')
    ->dailyAt('03:50')
    ->withoutOverlapping(60)
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/content-maintain.log'));

/*
|--------------------------------------------------------------------------
| Gemini Translate Daily Batch
|--------------------------------------------------------------------------
| Her gün gece 1500 program çevir (free tier limit). 17.074 program 11 günde tamamlanır.
| --yes maliyet onayını atlar (1500 × ortalama = ~$1/gün eşdeğeri, free tier).
| Paid plan'a geçilirse limit kaldırılabilir.
*/
/*
|--------------------------------------------------------------------------
| Newsletter Weekly Digest
|--------------------------------------------------------------------------
| Pazartesi 09:00 — son 7 günde enrich edilen şehir/üni/alan/eyalet özeti.
| İçerik yoksa erken çıkar, mail gönderilmez.
*/
Schedule::command('newsletter:digest', ['--days' => 7, '--send' => true])
    ->weeklyOn(1, '09:00')
    ->withoutOverlapping(30)
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/newsletter-digest.log'));

/*
|--------------------------------------------------------------------------
| Ticketmaster Cultural Events Import
|--------------------------------------------------------------------------
| Her gün 04:30 — büyük öğrenci şehirlerindeki konser/tiyatro etkinliklerini
| /events'e aktarır (idempotent, dedup). TICKETMASTER_API_KEY yoksa command
| FAIL döner (log'a yazar), zarar vermez.
*/
Schedule::command('events:import-ticketmaster')
    ->dailyAt('04:30')
    ->withoutOverlapping(60)
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/ticketmaster-import.log'));

/*
|--------------------------------------------------------------------------
| Event City Alerts — Weekly Digest
|--------------------------------------------------------------------------
| Perşembe 10:00 — şehir abonelerine yeni eklenen yaklaşan etkinlikler.
| Import (04:30) sonrası → taze veri. Boş içerikte mail göndermez.
*/
Schedule::command('events:notify-subscribers')
    ->weeklyOn(4, '10:00')
    ->withoutOverlapping(60)
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/event-digest.log'));

/*
|--------------------------------------------------------------------------
| Favorites Weekly Digest
|--------------------------------------------------------------------------
| Pazar 18:00 — kullanıcı favorileri için kişisel digest:
| yaklaşan deadline (30 gün) + yeni program (favori üni'lerde, 14 gün) + ilgili blog.
| Boş içerikli kullanıcıya mail göndermez (no spam).
*/
/*
|--------------------------------------------------------------------------
| Journey reminder — stalled application tracker users
|--------------------------------------------------------------------------
| Salı 10:00 — 14+ gündür hareketsiz olan kullanıcılara sıradaki adım hatırlatması.
| email_reminders=true + tamamlanmamış adımı olanlar.
*/
Schedule::command('journey:remind-inactive --send')
    ->weeklyOn(2, '10:00')
    ->withoutOverlapping(30)
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/journey-reminder.log'));

Schedule::command('favorites:digest')
    ->weeklyOn(0, '18:00')
    ->withoutOverlapping(30)
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/favorites-digest.log'));

Schedule::command('translate:programs', ['--limit' => 1400, '--delay' => 200, '--yes' => true])
    ->dailyAt('02:30')
    ->withoutOverlapping(180)
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/translate-daily.log'));

/*
|--------------------------------------------------------------------------
| DAAD Scholarships Sync (3 ayda bir)
|--------------------------------------------------------------------------
| DAAD scholarship database snapshot. 166 burs, statik JS dosyaları.
| 1 Ocak/Nisan/Temmuz/Ekim saat 03:00 UTC.
| NOT: daad:import (Solr API programları) ile karıştırma — bu AYRI veri seti.
*/
Schedule::command('daad:scholarships:sync')
    ->cron('0 3 1 */3 *')
    ->withoutOverlapping(60)
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/daad-scholarships.log'));

/*
|--------------------------------------------------------------------------
| Üniversite kapak görseli self-heal (haftalık)
|--------------------------------------------------------------------------
| Şüpheli/yanlış (logo/arma) veya eksik üni görsellerini Wikidata P18
| (ana bina) ile onarır → kartlarda default ikon kalmaz, kendini onarır.
| SADECE yüksek-güven (P18) — yanlış otomatik seçim riski yok. Agresif
| Commons fallback için manuel: php artisan unis:fix-images --low-confidence
| Çarşamba 04:00 — diğer cron'larla (02:30/03:00/03:30) çakışmaz.
*/
Schedule::command('unis:fix-images')
    ->weeklyOn(3, '04:00')
    ->withoutOverlapping(120)
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/unis-fix-images.log'));

/*
|--------------------------------------------------------------------------
| DAAD detay zenginleştirme (haftalık)
|--------------------------------------------------------------------------
| Salı 04:30 — DAAD International Programmes detay sayfalarından program-spesifik
| gereklilik (EN) + boş deadline çeker. Incremental (sadece _en BOŞ olanlar).
| İlk çalıştırma 678 programı doldurur; sonraki haftalar yeni daad import'ları.
*/
Schedule::command('daad:enrich-details --apply')
    ->weeklyOn(2, '04:30')
    ->withoutOverlapping(120)
    ->runInBackground()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/daad-enrich.log'));

/*
|--------------------------------------------------------------------------
| OG görsel cache tazeleme (günlük)
|--------------------------------------------------------------------------
| Son 3 günde güncellenen içeriğin bayat OG cache'ini siler → sonraki sosyal
| paylaşım crawl'ında taze başlıkla yeniden üretilir. Her iki brand için.
| 05:00 — diğer cron'larla çakışmaz.
*/
Schedule::command('og:refresh', ['--days' => 3])
    ->dailyAt('05:00')
    ->withoutOverlapping(30)
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/og-refresh.log'));

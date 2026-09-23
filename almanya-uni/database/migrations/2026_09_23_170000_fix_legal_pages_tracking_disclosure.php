<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Gizlilik ve Çerez Politikası metinleri fiilî durumla çelişiyordu — düzeltir.
 *
 * TESPİT (2026-09-23, canlı sayfadan ölçüldü):
 *   Sayfalar "Google Analytics yok", "Üçüncü Taraf: Yok", "Pazarlama çerezleri: Yok",
 *   "ABD'ye veri aktarımı yapılmaz" diyordu. Gerçekte ise <head> içinde:
 *     - Google Analytics 4 — G-D0VB1M1RKF (gtag.js, googletagmanager.com)
 *     - Microsoft Clarity — x8k5xytgs6 (ısı haritası + OTURUM KAYDI)
 *   İkisi de Consent Mode v2 arkasında ("denied" ile başlıyor, rıza ile açılıyor)
 *   ama var olduklarının beyan edilmesi gerekir. Meta ve TikTok piksellerinin
 *   altyapısı kodda mevcut, ID'leri BOŞ → şu an yüklenmiyorlar.
 *
 *   Ayrıca hukuki dayanak yanlıştı: çerez tabanlı analitik 6(1)(f) meşru menfaat
 *   değil, 6(1)(a) açık rıza gerektirir.
 *
 * Çerez banner'ı ise ters yönde hatalıydı (Meta/TikTok'tan söz ediyordu) —
 * o metin ayrı bir çeviri dosyasında, bu migration'ın kapsamı dışında.
 *
 * Yöntem: tüm gövdeyi yeniden yazmak yerine yalnızca yanlış bloklar hedefli
 * olarak değiştirilir; panelden yapılmış diğer düzenlemeler korunur.
 * Idempotent: ölçüm kimliği gövdede zaten varsa o locale atlanır.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('legal_pages')) {
            return;
        }

        $report = [];

        foreach (['cookies', 'privacy'] as $key) {
            $row = DB::table('legal_pages')->where('key', $key)->first();
            if (! $row) {
                $report[] = "{$key}: kayıt yok, atlandı";

                continue;
            }

            $bodies = json_decode($row->bodies ?? '{}', true) ?: [];
            $changed = false;

            foreach ($bodies as $locale => $html) {
                if (! is_string($html) || $html === '') {
                    continue;
                }
                // Idempotent işaret: düzeltilmiş metnin HER İKİ sayfada da geçtiği ifade.
                if (str_contains($html, 'Microsoft Clarity')) {
                    $report[] = "{$key}/{$locale}: zaten güncel";

                    continue;
                }

                $new = $key === 'cookies'
                    ? $this->fixCookies($html, $locale, $hits)
                    : $this->fixPrivacy($html, $locale, $hits);

                if ($new !== $html) {
                    $bodies[$locale] = $new;
                    $changed = true;
                    $report[] = "{$key}/{$locale}: " . implode(', ', $hits);
                } else {
                    $report[] = "{$key}/{$locale}: HİÇBİR KALIP EŞLEŞMEDİ — elle kontrol et";
                }
            }

            if ($changed) {
                DB::table('legal_pages')->where('key', $key)->update([
                    'bodies'     => json_encode($bodies, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
            }
        }

        echo "legal tracking disclosure:\n  " . implode("\n  ", $report) . "\n";
    }

    public function down(): void
    {
        // Geri alma yok: eski metin gerçeğe aykırıydı, geri yazmak hukuki risk doğurur.
    }

    /** Çerez Politikası: üçüncü taraf bölümü, pazarlama satırı ve çerez tablosu. */
    private function fixCookies(string $html, string $locale, ?array &$hits): string
    {
        $hits = [];
        $t = $this->text($locale);

        // 1) "Üçüncü Taraf: Yok" bölümü → gerçek sağlayıcı listesi
        $html = preg_replace_callback(
            '~<h2>[^<]*(?:Üçüncü Taraf|Third parties|Third-Party|Drittanbieter)[^<]*</h2>\s*<p><strong>(?:Yok|None|Keine)\.?</strong>.*?</p>~su',
            function () use ($t, &$hits) {
                $hits[] = 'üçüncü taraf bölümü';

                return $t['cookies_third_party'];
            },
            $html,
            1
        );

        // 2) "Pazarlama çerezleri: Yok" satırı
        $html = preg_replace_callback(
            '~<li><strong>(?:Pazarlama çerezleri|Marketing cookies|Marketing-Cookies):</strong>.*?</li>~su',
            function () use ($t, &$hits) {
                $hits[] = 'pazarlama satırı';

                return $t['cookies_marketing_line'];
            },
            $html,
            1
        );

        // 3) Çerez tablosuna üçüncü taraf çerezleri eklenir (tablo sonuna)
        if (str_contains($html, '</tbody>') && ! str_contains($html, '_clck')) {
            $html = preg_replace('~</tbody>~su', $t['cookies_table_rows'] . '</tbody>', $html, 1);
            $hits[] = 'çerez tablosu satırları';
        }

        return $html;
    }

    /** Gizlilik: analitik sağlayıcı, ABD aktarımı ve hukuki dayanak. */
    private function fixPrivacy(string $html, string $locale, ?array &$hits): string
    {
        $hits = [];
        $t = $this->text($locale);

        // 1) "Analitik: Self-hosted (Google Analytics yok)" → gerçek sağlayıcılar
        $html = preg_replace_callback(
            '~<li><strong>(?:Analitik|Analytics|Analytik):</strong>\s*Self-hosted.*?</li>~su',
            function () use ($t, &$hits) {
                $hits[] = 'analitik sağlayıcı satırı';

                return $t['privacy_analytics_line'];
            },
            $html,
            1
        );

        // 2) "ABD'ye veri aktarımı yapılmaz" → gerçek durum
        $html = preg_replace_callback(
            '~<p>[^<]*(?:ABD\'ye veri aktarımı|No data (?:is )?transferred to the US|Keine Daten(?:übermittlung)? in die USA)[^<]*(?:<strong>[^<]*</strong>[^<]*)?</p>~su',
            function () use ($t, &$hits) {
                $hits[] = 'ABD aktarımı cümlesi';

                return $t['privacy_us_transfer'];
            },
            $html,
            1
        );

        // ABD cümlesi bulunamadıysa (EN/DE'de farklı yazılmış olabilir) aktarım
        // listesinin hemen ardına ekle — beyanın hiç olmaması kabul edilemez.
        if (! in_array('ABD aktarımı cümlesi', $hits, true)
            && str_contains($html, $t['privacy_analytics_line'])) {
            $html = str_replace(
                $t['privacy_analytics_line'] . '</ul>',
                $t['privacy_analytics_line'] . '</ul>' . $t['privacy_us_transfer'],
                $html
            );
            $hits[] = 'ABD aktarımı cümlesi (eklendi)';
        }

        // 3) Hukuki dayanak: meşru menfaat → açık rıza
        $html = preg_replace_callback(
            '~<li><strong>(?:İstatistik \(analitik\)|İstatistik|Statistics|Analytics|Statistik|Analytik)</strong>\s*—\s*(?:GDPR|Art\.)\s*6\(1\)\(f\).*?</li>~su',
            function () use ($t, &$hits) {
                $hits[] = 'hukuki dayanak';

                return $t['privacy_legal_basis'];
            },
            $html,
            1
        );

        return $html;
    }

    /** Locale metinleri. Bilinmeyen locale için İngilizce kullanılır. */
    private function text(string $locale): array
    {
        $all = [
            'tr' => [
                'cookies_third_party' => '<h2>Üçüncü Taraf</h2>'
                    . '<p>Aşağıdaki üçüncü taraf araçları <strong>yalnızca açık rızanızla</strong> çalışır. '
                    . 'Çerez banner\'ında "Reddet" derseniz hiçbiri yüklenmez:</p><ul>'
                    . '<li><strong>Google Analytics 4</strong> (Google Ireland Ltd.) — ziyaret istatistikleri. Ölçüm kimliği: <code>G-D0VB1M1RKF</code></li>'
                    . '<li><strong>Microsoft Clarity</strong> (Microsoft Ireland Operations Ltd.) — ısı haritası ve <strong>oturum kaydı</strong> (fare hareketi, tıklama, kaydırma)</li>'
                    . '</ul><p>Her iki sağlayıcı da verileri ABD\'ye aktarabilir; aktarım AB–ABD Veri Gizliliği Çerçevesi ve standart sözleşme maddelerine dayanır. '
                    . 'Google Consent Mode v2 kullanıyoruz: siz onay verene kadar tüm ölçüm izinleri <code>denied</code> durumundadır.</p>'
                    . '<p>Meta (Facebook) ve TikTok piksel altyapısı sitede mevcuttur ancak <strong>şu anda etkin değildir</strong>. Etkinleştirilirse bu sayfa güncellenecektir.</p>',
                'cookies_marketing_line' => '<li><strong>Pazarlama çerezleri:</strong> Şu anda kullanılmıyor. Reklam ve yeniden pazarlama pikselleri etkin değildir.</li>',
                'cookies_table_rows' => '<tr><td><code>_ga</code></td><td>Google Analytics — ziyaretçi ayrımı</td><td>2 yıl</td><td>Analitik (onay sonrası)</td></tr>'
                    . '<tr><td><code>_ga_*</code></td><td>Google Analytics — oturum durumu</td><td>2 yıl</td><td>Analitik (onay sonrası)</td></tr>'
                    . '<tr><td><code>_clck</code></td><td>Microsoft Clarity — ziyaretçi kimliği</td><td>1 yıl</td><td>Analitik (onay sonrası)</td></tr>'
                    . '<tr><td><code>_clsk</code></td><td>Microsoft Clarity — oturum kaydı</td><td>1 gün</td><td>Analitik (onay sonrası)</td></tr>',
                'privacy_analytics_line' => '<li><strong>Analitik:</strong> Google Analytics 4 (Google Ireland Ltd.) ve Microsoft Clarity (Microsoft Ireland Operations Ltd.) — yalnızca açık rızanızla</li>',
                'privacy_us_transfer' => '<p>Analitik sağlayıcılar verileri <strong>ABD\'ye aktarabilir</strong>. Aktarım, AB–ABD Veri Gizliliği Çerçevesi ve standart sözleşme maddelerine dayanır. '
                    . 'Rıza vermezseniz bu araçlar hiç yüklenmez ve aktarım gerçekleşmez.</p>',
                'privacy_legal_basis' => '<li><strong>İstatistik (analitik)</strong> — GDPR 6(1)(a) açık rıza (Google Analytics 4, Microsoft Clarity; rıza verilmezse çalışmazlar)</li>',
            ],
            'en' => [
                'cookies_third_party' => '<h2>Third parties</h2>'
                    . '<p>The following third-party tools run <strong>only with your explicit consent</strong>. '
                    . 'If you choose "Reject" in the cookie banner, none of them load:</p><ul>'
                    . '<li><strong>Google Analytics 4</strong> (Google Ireland Ltd.) — visit statistics. Measurement ID: <code>G-D0VB1M1RKF</code></li>'
                    . '<li><strong>Microsoft Clarity</strong> (Microsoft Ireland Operations Ltd.) — heatmaps and <strong>session recording</strong> (mouse movement, clicks, scrolling)</li>'
                    . '</ul><p>Both providers may transfer data to the United States, on the basis of the EU–US Data Privacy Framework and standard contractual clauses. '
                    . 'We use Google Consent Mode v2: every measurement permission stays <code>denied</code> until you consent.</p>'
                    . '<p>Meta (Facebook) and TikTok pixel infrastructure exists on the site but is <strong>currently inactive</strong>. This page will be updated if that changes.</p>',
                'cookies_marketing_line' => '<li><strong>Marketing cookies:</strong> Not currently used. Advertising and remarketing pixels are inactive.</li>',
                'cookies_table_rows' => '<tr><td><code>_ga</code></td><td>Google Analytics — visitor distinction</td><td>2 years</td><td>Analytics (after consent)</td></tr>'
                    . '<tr><td><code>_ga_*</code></td><td>Google Analytics — session state</td><td>2 years</td><td>Analytics (after consent)</td></tr>'
                    . '<tr><td><code>_clck</code></td><td>Microsoft Clarity — visitor ID</td><td>1 year</td><td>Analytics (after consent)</td></tr>'
                    . '<tr><td><code>_clsk</code></td><td>Microsoft Clarity — session recording</td><td>1 day</td><td>Analytics (after consent)</td></tr>',
                'privacy_analytics_line' => '<li><strong>Analytics:</strong> Google Analytics 4 (Google Ireland Ltd.) and Microsoft Clarity (Microsoft Ireland Operations Ltd.) — only with your explicit consent</li>',
                'privacy_us_transfer' => '<p>Our analytics providers <strong>may transfer data to the United States</strong>, on the basis of the EU–US Data Privacy Framework and standard contractual clauses. '
                    . 'If you do not consent, these tools are never loaded and no transfer takes place.</p>',
                'privacy_legal_basis' => '<li><strong>Analytics</strong> — Art. 6(1)(a) GDPR, explicit consent (Google Analytics 4, Microsoft Clarity; they do not run without it)</li>',
            ],
            'de' => [
                'cookies_third_party' => '<h2>Drittanbieter</h2>'
                    . '<p>Die folgenden Drittanbieter-Dienste laufen <strong>ausschließlich mit Ihrer ausdrücklichen Einwilligung</strong>. '
                    . 'Wählen Sie im Cookie-Banner „Ablehnen", wird keiner davon geladen:</p><ul>'
                    . '<li><strong>Google Analytics 4</strong> (Google Ireland Ltd.) — Besuchsstatistik. Mess-ID: <code>G-D0VB1M1RKF</code></li>'
                    . '<li><strong>Microsoft Clarity</strong> (Microsoft Ireland Operations Ltd.) — Heatmaps und <strong>Sitzungsaufzeichnung</strong> (Mausbewegung, Klicks, Scrollen)</li>'
                    . '</ul><p>Beide Anbieter können Daten in die USA übermitteln; Grundlage sind das EU-US Data Privacy Framework und Standardvertragsklauseln. '
                    . 'Wir setzen Google Consent Mode v2 ein: Bis zur Einwilligung stehen alle Messberechtigungen auf <code>denied</code>.</p>'
                    . '<p>Die Pixel-Infrastruktur von Meta (Facebook) und TikTok ist vorhanden, aber <strong>derzeit nicht aktiv</strong>. Bei Aktivierung wird diese Seite aktualisiert.</p>',
                'cookies_marketing_line' => '<li><strong>Marketing-Cookies:</strong> Derzeit nicht im Einsatz. Werbe- und Remarketing-Pixel sind inaktiv.</li>',
                'cookies_table_rows' => '<tr><td><code>_ga</code></td><td>Google Analytics — Unterscheidung der Besucher</td><td>2 Jahre</td><td>Analytik (nach Einwilligung)</td></tr>'
                    . '<tr><td><code>_ga_*</code></td><td>Google Analytics — Sitzungsstatus</td><td>2 Jahre</td><td>Analytik (nach Einwilligung)</td></tr>'
                    . '<tr><td><code>_clck</code></td><td>Microsoft Clarity — Besucher-ID</td><td>1 Jahr</td><td>Analytik (nach Einwilligung)</td></tr>'
                    . '<tr><td><code>_clsk</code></td><td>Microsoft Clarity — Sitzungsaufzeichnung</td><td>1 Tag</td><td>Analytik (nach Einwilligung)</td></tr>',
                'privacy_analytics_line' => '<li><strong>Analytik:</strong> Google Analytics 4 (Google Ireland Ltd.) und Microsoft Clarity (Microsoft Ireland Operations Ltd.) — nur mit Ihrer ausdrücklichen Einwilligung</li>',
                'privacy_us_transfer' => '<p>Unsere Analyse-Anbieter können <strong>Daten in die USA übermitteln</strong>; Grundlage sind das EU-US Data Privacy Framework und Standardvertragsklauseln. '
                    . 'Ohne Ihre Einwilligung werden diese Dienste nicht geladen und es findet keine Übermittlung statt.</p>',
                'privacy_legal_basis' => '<li><strong>Statistik (Analytik)</strong> — Art. 6(1)(a) DSGVO, ausdrückliche Einwilligung (Google Analytics 4, Microsoft Clarity; ohne Einwilligung laufen sie nicht)</li>',
            ],
        ];

        return $all[$locale] ?? $all['en'];
    }
};

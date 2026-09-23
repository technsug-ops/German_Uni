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

        // ABD cümlesi bulunamadıysa (EN/DE'de farklı yazılmış) aktarım listesinin
        // ardına eklenir. Beyanın hiç olmaması kabul edilemez.
        //
        // Ekleme noktası hesaplanarak bulunur: analitik satırından SONRAKİ ilk </ul>.
        // Önceki sürüm "analitik satırı + </ul>" bitişikliğini varsayıyordu; EN/DE'de
        // araya başka satırlar girdiği için hiçbir şey eklenmiyor, üstelik başarılı
        // raporlanıyordu. Artık ekleme gerçekten olduysa rapor ediliyor.
        if (! in_array('ABD aktarımı cümlesi', $hits, true)) {
            $at = strpos($html, $t['privacy_analytics_line']);

            if ($at !== false) {
                $end = strpos($html, '</ul>', $at);
                $insertAt = $end !== false
                    ? $end + strlen('</ul>')
                    : $at + strlen($t['privacy_analytics_line']);

                $html = substr($html, 0, $insertAt) . $t['privacy_us_transfer'] . substr($html, $insertAt);
                $hits[] = 'ABD aktarımı cümlesi (eklendi)';
            } else {
                $hits[] = 'ABD AKTARIMI CÜMLESİ EKLENEMEDİ — elle kontrol et';
            }
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
                    . '<li><strong>Microsoft Clarity</strong> (Microsoft Ireland Operations Ltd.) — <strong>ısı haritaları</strong>, tıklama ve kaydırma analizi ve <strong>oturum kayıtları</strong> (sayfadaki hareketlerinizin sonradan yeniden izlenebildiği kayıtlar)</li>'
                    . '</ul><p><strong>Bu araçlar siz izin verene kadar sayfaya hiç eklenmez.</strong> Reddederseniz ya da hiçbir seçim yapmazsanız '
                    . 'script dosyaları yüklenmez, çerez yazmazlar ve sağlayıcılara istek gitmez.</p>'
                    . '<p>Sözleşme tarafımız AB\'deki kuruluşlardır (Google Ireland Ltd., Microsoft Ireland Operations Ltd.). Hizmetin işleyişinde veriler '
                    . 'ABD\'deki ana şirketlere (Google LLC, Microsoft Corporation) ulaşabilir. ABD aktarımı için başvurulan mekanizma alıcının '
                    . 'geçerli bir <strong>EU-U.S. Data Privacy Framework</strong> sertifikasyonu bulunması hâlinde ilgili yeterlilik kararına '
                    . 'dayanılabilir. DPF\'nin uygulanmadığı aktarımlarda, uygulanabilir olduğu ölçüde <strong>AB Standart Sözleşme Maddeleri</strong> '
                    . 'gibi uygun güvenceler kullanılabilir.</p>'
                    . '<p>Meta (Facebook) ve TikTok piksel altyapısı sitede mevcuttur ancak <strong>şu anda etkin değildir</strong>. Etkinleştirilirse bu sayfa güncellenecektir.</p>',
                'cookies_marketing_line' => '<li><strong>Pazarlama çerezleri:</strong> Şu anda kullanılmıyor. Reklam ve yeniden pazarlama pikselleri etkin değildir.</li>',
                'cookies_table_rows' => '<tr><td><code>_ga</code></td><td>Google Analytics — ziyaretçi ayrımı</td><td>2 yıl</td><td>Analitik (onay sonrası)</td></tr>'
                    . '<tr><td><code>_ga_*</code></td><td>Google Analytics — oturum durumu</td><td>2 yıl</td><td>Analitik (onay sonrası)</td></tr>'
                    . '<tr><td><code>_clck</code></td><td>Microsoft Clarity — ziyaretçi kimliği</td><td>1 yıl</td><td>Analitik (onay sonrası)</td></tr>'
                    . '<tr><td><code>_clsk</code></td><td>Microsoft Clarity — oturum kaydı</td><td>1 gün</td><td>Analitik (onay sonrası)</td></tr>',
                'privacy_analytics_line' => '<li><strong>Analitik:</strong> Google Analytics 4 (Google Ireland Ltd.) ve Microsoft Clarity (Microsoft Ireland Operations Ltd.) — yalnızca açık rızanızla</li>',
                'privacy_us_transfer' => '<p>Analitik araçlar yalnızca izin verdiğinizde sayfaya eklenir. Sözleşme tarafımız AB\'deki kuruluşlardır '
                    . '(Google Ireland Ltd., Microsoft Ireland Operations Ltd.); hizmetin işleyişinde veriler ABD\'deki ana şirketlere ulaşabilir. '
                    . 'ABD\'ye veri aktarımı söz konusu olduğunda, alıcının EU-U.S. Data Privacy Framework kapsamında geçerli bir sertifikasyonu '
                    . 'bulunması hâlinde ilgili yeterlilik kararına dayanılabilir. DPF\'nin uygulanmadığı veri aktarımlarında, uygulanabilir olduğu '
                    . 'ölçüde AB Standart Sözleşme Maddeleri gibi uygun güvenceler kullanılabilir. Ayrıntı için Çerez Politikası sayfamıza bakın.</p>',
                'privacy_legal_basis' => '<li><strong>İstatistik (analitik)</strong> — GDPR 6(1)(a) açık rıza (Google Analytics 4, Microsoft Clarity; rıza verilmezse çalışmazlar)</li>',
            ],
            'en' => [
                'cookies_third_party' => '<h2>Third parties</h2>'
                    . '<p>The following third-party tools run <strong>only with your explicit consent</strong>. '
                    . 'If you choose "Reject" in the cookie banner, none of them load:</p><ul>'
                    . '<li><strong>Google Analytics 4</strong> (Google Ireland Ltd.) — visit statistics. Measurement ID: <code>G-D0VB1M1RKF</code></li>'
                    . '<li><strong>Microsoft Clarity</strong> (Microsoft Ireland Operations Ltd.) — <strong>heatmaps</strong>, click and scroll analytics, and <strong>session recordings</strong> (replayable recordings of how you move through the page)</li>'
                    . '</ul><p><strong>These tools are not added to the page until you consent.</strong> If you reject, or make no choice at all, '
                    . 'their script files are never loaded, they set no cookies and no request reaches the providers.</p>'
                    . '<p>Our contracting parties are the EU entities (Google Ireland Ltd., Microsoft Ireland Operations Ltd.). In operating these '
                    . 'services, data may reach the US parent companies (Google LLC, Microsoft Corporation). The mechanism relied on for transfers to '
                    . 'the US, reliance may be placed on the relevant adequacy decision where the recipient holds a valid certification under the '
                    . '<strong>EU-U.S. Data Privacy Framework</strong>. For transfers not covered by the DPF, appropriate safeguards such as the '
                    . '<strong>EU Standard Contractual Clauses</strong> may be used to the extent applicable.</p>'
                    . '<p>Meta (Facebook) and TikTok pixel infrastructure exists on the site but is <strong>currently inactive</strong>. This page will be updated if that changes.</p>',
                'cookies_marketing_line' => '<li><strong>Marketing cookies:</strong> Not currently used. Advertising and remarketing pixels are inactive.</li>',
                'cookies_table_rows' => '<tr><td><code>_ga</code></td><td>Google Analytics — visitor distinction</td><td>2 years</td><td>Analytics (after consent)</td></tr>'
                    . '<tr><td><code>_ga_*</code></td><td>Google Analytics — session state</td><td>2 years</td><td>Analytics (after consent)</td></tr>'
                    . '<tr><td><code>_clck</code></td><td>Microsoft Clarity — visitor ID</td><td>1 year</td><td>Analytics (after consent)</td></tr>'
                    . '<tr><td><code>_clsk</code></td><td>Microsoft Clarity — session recording</td><td>1 day</td><td>Analytics (after consent)</td></tr>',
                'privacy_analytics_line' => '<li><strong>Analytics:</strong> Google Analytics 4 (Google Ireland Ltd.) and Microsoft Clarity (Microsoft Ireland Operations Ltd.) — only with your explicit consent</li>',
                'privacy_us_transfer' => '<p>Analytics tools are added to the page only once you consent. Our contracting parties are the EU entities '
                    . '(Google Ireland Ltd., Microsoft Ireland Operations Ltd.); in operating the services data may reach the US parent companies. '
                    . 'Where data is transferred to the United States, reliance may be placed on the relevant adequacy decision if the recipient holds '
                    . 'a valid certification under the EU-U.S. Data Privacy Framework. For transfers not covered by the DPF, appropriate safeguards '
                    . 'such as the EU Standard Contractual Clauses may be used to the extent applicable. See our Cookie Policy for detail.</p>',
                'privacy_legal_basis' => '<li><strong>Analytics</strong> — Art. 6(1)(a) GDPR, explicit consent (Google Analytics 4, Microsoft Clarity; they do not run without it)</li>',
            ],
            'de' => [
                'cookies_third_party' => '<h2>Drittanbieter</h2>'
                    . '<p>Die folgenden Drittanbieter-Dienste laufen <strong>ausschließlich mit Ihrer ausdrücklichen Einwilligung</strong>. '
                    . 'Wählen Sie im Cookie-Banner „Ablehnen", wird keiner davon geladen:</p><ul>'
                    . '<li><strong>Google Analytics 4</strong> (Google Ireland Ltd.) — Besuchsstatistik. Mess-ID: <code>G-D0VB1M1RKF</code></li>'
                    . '<li><strong>Microsoft Clarity</strong> (Microsoft Ireland Operations Ltd.) — <strong>Heatmaps</strong>, Klick- und Scroll-Analyse sowie <strong>Sitzungsaufzeichnungen</strong> (abspielbare Aufzeichnungen Ihrer Bewegung auf der Seite)</li>'
                    . '</ul><p><strong>Diese Dienste werden erst nach Ihrer Einwilligung in die Seite eingebunden.</strong> Bei Ablehnung oder ohne '
                    . 'Auswahl werden ihre Skriptdateien nicht geladen, es werden keine Cookies gesetzt und es geht keine Anfrage an die Anbieter.</p>'
                    . '<p>Unsere Vertragspartner sind die EU-Gesellschaften (Google Ireland Ltd., Microsoft Ireland Operations Ltd.). Im Betrieb dieser '
                    . 'Dienste können Daten die US-Muttergesellschaften (Google LLC, Microsoft Corporation) erreichen. Als Mechanismus für die '
                    . 'Übermittlung in die USA kann auf den entsprechenden Angemessenheitsbeschluss gestützt werden, sofern der Empfänger über eine '
                    . 'gültige Zertifizierung nach dem <strong>EU-U.S. Data Privacy Framework</strong> verfügt. Für nicht vom DPF erfasste '
                    . 'Übermittlungen können geeignete Garantien wie die <strong>EU-Standardvertragsklauseln</strong> herangezogen werden, '
                    . 'soweit anwendbar.</p>'
                    . '<p>Die Pixel-Infrastruktur von Meta (Facebook) und TikTok ist vorhanden, aber <strong>derzeit nicht aktiv</strong>. Bei Aktivierung wird diese Seite aktualisiert.</p>',
                'cookies_marketing_line' => '<li><strong>Marketing-Cookies:</strong> Derzeit nicht im Einsatz. Werbe- und Remarketing-Pixel sind inaktiv.</li>',
                'cookies_table_rows' => '<tr><td><code>_ga</code></td><td>Google Analytics — Unterscheidung der Besucher</td><td>2 Jahre</td><td>Analytik (nach Einwilligung)</td></tr>'
                    . '<tr><td><code>_ga_*</code></td><td>Google Analytics — Sitzungsstatus</td><td>2 Jahre</td><td>Analytik (nach Einwilligung)</td></tr>'
                    . '<tr><td><code>_clck</code></td><td>Microsoft Clarity — Besucher-ID</td><td>1 Jahr</td><td>Analytik (nach Einwilligung)</td></tr>'
                    . '<tr><td><code>_clsk</code></td><td>Microsoft Clarity — Sitzungsaufzeichnung</td><td>1 Tag</td><td>Analytik (nach Einwilligung)</td></tr>',
                'privacy_analytics_line' => '<li><strong>Analytik:</strong> Google Analytics 4 (Google Ireland Ltd.) und Microsoft Clarity (Microsoft Ireland Operations Ltd.) — nur mit Ihrer ausdrücklichen Einwilligung</li>',
                'privacy_us_transfer' => '<p>Analyse-Dienste werden erst nach Ihrer Einwilligung eingebunden. Vertragspartner sind die '
                    . 'EU-Gesellschaften (Google Ireland Ltd., Microsoft Ireland Operations Ltd.); im Betrieb können Daten die '
                    . 'US-Muttergesellschaften erreichen. Soweit Daten in die USA übermittelt werden, kann auf den entsprechenden '
                    . 'Angemessenheitsbeschluss gestützt werden, sofern der Empfänger über eine gültige Zertifizierung nach dem '
                    . 'EU-U.S. Data Privacy Framework verfügt. Für nicht vom DPF erfasste Übermittlungen können geeignete Garantien wie die '
                    . 'EU-Standardvertragsklauseln herangezogen werden, soweit anwendbar. Einzelheiten in unserer Cookie-Richtlinie.</p>',
                'privacy_legal_basis' => '<li><strong>Statistik (Analytik)</strong> — Art. 6(1)(a) DSGVO, ausdrückliche Einwilligung (Google Analytics 4, Microsoft Clarity; ohne Einwilligung laufen sie nicht)</li>',
            ],
        ];

        return $all[$locale] ?? $all['en'];
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Gizlilik ve Çerez Politikası — izleme beyanını fiilî duruma uyarlar (2. deneme).
 *
 * NEDEN YENİ BİR MIGRATION
 * 2026_09_23_170000 prod'da koştu ve "tamamlandı" olarak kaydedildi, ama canlı
 * metin değişmedi (sayfaların "son güncelleme" tarihi hâlâ 17.06.2026). Sebep:
 * o migration blok etiketlerinin BİTİŞİK yazıldığı bir gövde varsayıyordu
 * (<h2>…</h2><p>…), oysa prod'daki HTML bloklar arasında satır sonu içeriyor.
 * Kalıplar hiçbir yere oturmadı, hiçbir şey değişmedi.
 *
 * Kullanıcı talimatı gereği eski migration DEĞİŞTİRİLMEDİ, rollback YAPILMADI,
 * migrations tablosundan kayıt SİLİNMEDİ. Düzeltme bağımsız olarak buradadır ve
 * boşluk/satır-sonu toleranslı kalıplar kullanır.
 *
 * NE DÜZELTİLİYOR (canlı metin fiilî davranışla çelişiyordu)
 *   "Üçüncü Taraf: Yok. Google Analytics … kullanmıyoruz"  → GA4 + Clarity aktif
 *   "Analitik: Self-hosted (Google Analytics yok)"         → aynı
 *   "Pazarlama çerezleri: Yok. Üçüncü taraf takip …"       → EN/DE'de Türkçe sızıntı
 *   "ABD'ye veri aktarımı yapılmaz"                        → aktarım mümkün
 *   "İstatistik — 6(1)(f) meşru menfaat"                   → çerezli analitik 6(1)(a) ister
 *   Çerez tablosunda GA/Clarity çerezleri yok              → eksik beyan
 *   §2 tablosunda oturum kaydı hiç geçmiyor                → eksik beyan
 *
 * YÖNTEM
 *   - Sayfalar `key` ile hedeflenir ('cookies', 'privacy'); ID'ye güvenilmez.
 *   - Yalnızca bilinen hatalı bloklar değiştirilir; gövdenin geri kalanı ve
 *     panelden yapılmış diğer düzenlemeler olduğu gibi kalır.
 *   - Beklenen blok bulunamazsa o blok ATLANIR ve uyarı loglanır; yanlış yere
 *     içerik yazılmaz.
 *   - Idempotent: gövdede zaten "Microsoft Clarity" geçiyorsa o locale atlanır.
 *   - Her locale (tr/en/de) ayrı ayrı doğrulanır ve raporlanır.
 */
return new class extends Migration
{
    /** Gövde düzeltilmişse bu ifadeyi içerir; ikinci koşuda locale atlanır. */
    private const MARKER = 'Microsoft Clarity';

    public function up(): void
    {
        if (! Schema::hasTable('legal_pages')) {
            Log::warning('legal disclosure fix: legal_pages tablosu yok, atlandı');

            return;
        }

        $report = [];

        foreach (['cookies', 'privacy'] as $key) {
            $row = DB::table('legal_pages')->where('key', $key)->first();

            if (! $row) {
                $report[] = "{$key}: KAYIT YOK";
                Log::warning("legal disclosure fix: '{$key}' sayfası bulunamadı");

                continue;
            }

            $bodies = json_decode($row->bodies ?? '', true);

            if (! is_array($bodies) || $bodies === []) {
                $report[] = "{$key}: bodies boş/çözümlenemedi";
                Log::warning("legal disclosure fix: '{$key}' bodies çözümlenemedi");

                continue;
            }

            $changed = false;

            foreach (['tr', 'en', 'de'] as $locale) {
                $body = $bodies[$locale] ?? null;

                if (! is_string($body) || trim($body) === '') {
                    $report[] = "{$key}/{$locale}: gövde yok, atlandı";

                    continue;
                }

                if (str_contains($body, self::MARKER)) {
                    $report[] = "{$key}/{$locale}: zaten güncel, atlandı";

                    continue;
                }

                [$new, $hits, $misses] = $this->apply($body, $this->ops($key, $locale));

                if ($misses !== []) {
                    Log::warning("legal disclosure fix: {$key}/{$locale} eşleşmeyen blok(lar): " . implode(' | ', $misses));
                }

                if ($hits === []) {
                    $report[] = "{$key}/{$locale}: HİÇBİR BLOK EŞLEŞMEDİ — gövde elle kontrol edilmeli";

                    continue;
                }

                $bodies[$locale] = $new;
                $changed = true;
                $report[] = "{$key}/{$locale}: " . count($hits) . ' blok güncellendi (' . implode(', ', $hits) . ')'
                    . ($misses !== [] ? ' — EKSİK: ' . implode(', ', $misses) : '');
            }

            if ($changed) {
                DB::table('legal_pages')->where('key', $key)->update([
                    'bodies'     => json_encode($bodies, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
            }
        }

        echo "legal disclosure fix:\n  " . implode("\n  ", $report) . "\n";
    }

    public function down(): void
    {
        // Bilinçli olarak boş.
        //
        // Eski metin fiilî davranışla çelişiyordu: "Google Analytics kullanmıyoruz"
        // ve "ABD'ye veri aktarımı yapılmaz" derken GA4 ile Microsoft Clarity
        // (oturum kaydı dahil) çalışıyordu. Bu cümleleri geri yazmak ziyaretçiye
        // yanlış beyan sunmak ve GDPR açısından risk üretmek olurdu; bu yüzden
        // rollback bilerek uygulanmaz.
    }

    /**
     * Operasyonları sırayla uygular.
     *
     * @param  array<int, array{label: string, find: string, replace: string, mode?: string}>  $ops
     * @return array{0: string, 1: array<int, string>, 2: array<int, string>}
     */
    private function apply(string $body, array $ops): array
    {
        $hits = [];
        $misses = [];

        foreach ($ops as $op) {
            if (! preg_match($op['find'], $body, $m)) {
                $misses[] = $op['label'];

                continue;
            }

            // 'after' = eşleşeni koru, ardına ekle (tabloya satır eklemek için).
            // varsayılan = eşleşen bloğu değiştir.
            $new = ($op['mode'] ?? 'replace') === 'after'
                ? $m[0] . "\n" . $op['replace']
                : $op['replace'];

            $body = $this->replaceFirst($body, $m[0], $new);
            $hits[] = $op['label'];
        }

        return [$body, $hits, $misses];
    }

    /** preg_replace yerine düz string değişimi — $ ve \ kaçışı derdi olmasın. */
    private function replaceFirst(string $haystack, string $search, string $replace): string
    {
        $pos = strpos($haystack, $search);

        return $pos === false ? $haystack : substr_replace($haystack, $replace, $pos, strlen($search));
    }

    /**
     * @return array<int, array{label: string, find: string, replace: string, mode?: string}>
     */
    private function ops(string $key, string $locale): array
    {
        return $key === 'cookies'
            ? $this->cookieOps($locale)
            : $this->privacyOps($locale);
    }

    /** @return array<int, array{label: string, find: string, replace: string, mode?: string}> */
    private function cookieOps(string $locale): array
    {
        $labels = [
            'tr' => ['analytics' => 'Analitik çerezler', 'marketing' => 'Pazarlama çerezleri', 'heading' => 'Üçüncü Taraf'],
            'en' => ['analytics' => 'Analytics cookies', 'marketing' => 'Marketing cookies', 'heading' => 'Third parties'],
            'de' => ['analytics' => 'Statistik-Cookies', 'marketing' => 'Marketing-Cookies', 'heading' => 'Drittanbieter'],
        ][$locale];

        $t = $this->cookieTexts($locale);

        return [
            [
                'label'   => 'analitik çerez satırı',
                'find'    => '~<li>\s*<strong>\s*' . preg_quote($labels['analytics'], '~') . ':\s*</strong>.*?</li>~su',
                'replace' => $t['analytics_li'],
            ],
            [
                'label'   => 'pazarlama çerez satırı',
                'find'    => '~<li>\s*<strong>\s*' . preg_quote($labels['marketing'], '~') . ':\s*</strong>.*?</li>~su',
                'replace' => $t['marketing_li'],
            ],
            [
                // Çapa: tablonun son satırı olan almanyauni_consent; yeni çerez
                // satırları onun ARDINA eklenir (mode: after).
                'label'   => 'çerez tablosu satırları',
                'find'    => '~<tr>\s*<td><code>almanyauni_consent</code></td>.*?</tr>~su',
                'replace' => $t['table_rows'],
                'mode'    => 'after',
            ],
            [
                'label'   => 'üçüncü taraf bölümü',
                'find'    => '~<h2>\s*' . preg_quote($labels['heading'], '~') . '\s*</h2>\s*<p>.*?</p>~su',
                'replace' => $t['third_party'],
            ],
        ];
    }

    /** @return array<int, array{label: string, find: string, replace: string, mode?: string}> */
    private function privacyOps(string $locale): array
    {
        // §3 hukuki dayanak satırı iki nokta İÇERMEZ ("<strong>Analytics</strong> —"),
        // §5/§6 satırları içerir ("<strong>Analytics:</strong>") — kalıplar bu farkla ayrışır.
        $labels = [
            'tr' => ['basis' => 'İstatistik \(analitik\)', 'vendor' => 'Analitik', 'summary' => 'Analitik', 'dataRow' => 'Çerez verileri', 'law' => 'GDPR 6\(1\)\(f\)'],
            'en' => ['basis' => 'Analytics', 'vendor' => 'Analytics', 'summary' => 'Analytics', 'dataRow' => 'Cookies', 'law' => 'Art\. 6\(1\)\(f\) GDPR'],
            'de' => ['basis' => 'Statistik', 'vendor' => 'Analytik', 'summary' => 'Statistik', 'dataRow' => 'Cookies', 'law' => 'Art\. 6\(1\)\(f\) DSGVO'],
        ][$locale];

        $t = $this->privacyTexts($locale);

        return [
            [
                'label'   => '§3 hukuki dayanak (6(1)(f) → 6(1)(a))',
                'find'    => '~<li>\s*<strong>\s*' . $labels['basis'] . '\s*</strong>\s*—\s*' . $labels['law'] . '.*?</li>~su',
                'replace' => $t['basis_li'],
            ],
            [
                // Çapa: §2 tablosundaki çerez satırı; analitik veri kategorisi ardına eklenir.
                'label'   => '§2 toplanan veri tablosu',
                'find'    => '~<tr>\s*<td><strong>' . preg_quote($labels['dataRow'], '~') . '</strong></td>.*?</tr>~su',
                'replace' => $t['data_row'],
                'mode'    => 'after',
            ],
            [
                'label'   => '§5 analitik sağlayıcı satırı',
                'find'    => '~<li>\s*<strong>\s*' . preg_quote($labels['vendor'], '~') . ':\s*</strong>\s*Self-hosted.*?</li>~su',
                'replace' => $t['vendor_li'],
            ],
            [
                'label'   => '§5 ABD aktarımı beyanı',
                'find'    => $t['transfer_find'],
                'replace' => $t['transfer_p'],
            ],
            [
                'label'   => '§6 çerez özeti analitik satırı',
                'find'    => '~<li>\s*<strong>\s*' . preg_quote($labels['summary'], '~') . ':\s*</strong>\s*(?:Anonim|Anonymous|Anonyme).*?</li>~su',
                'replace' => $t['summary_li'],
            ],
        ];
    }

    /** @return array<string, string> */
    private function cookieTexts(string $locale): array
    {
        $all = [];

        $all['tr'] = [
            'analytics_li' => '<li><strong>Analitik çerezler:</strong> Google Analytics 4 ve Microsoft Clarity. <strong>Yalnızca açık onayınızla</strong> etkinleştirilir; onay vermezseniz yüklenmezler.</li>',
            'marketing_li' => '<li><strong>Pazarlama çerezleri:</strong> Şu anda kullanılmıyor. Reklam ve yeniden pazarlama pikselleri etkin değildir.</li>',
            'table_rows' => <<<'HTML'
                <tr>
                <td><code>almanyauni_consent_mkt</code></td>
                <td>Pazarlama onayı kaydı</td>
                <td>1 yıl</td>
                <td>Zorunlu</td>
                </tr>
                <tr>
                <td><code>_ga</code></td>
                <td>Google Analytics — ziyaretçi ayrımı</td>
                <td>2 yıl</td>
                <td>Analitik (onay sonrası)</td>
                </tr>
                <tr>
                <td><code>_ga_*</code></td>
                <td>Google Analytics — oturum durumu</td>
                <td>2 yıl</td>
                <td>Analitik (onay sonrası)</td>
                </tr>
                <tr>
                <td><code>_clck</code></td>
                <td>Microsoft Clarity — ziyaretçi kimliği</td>
                <td>1 yıl</td>
                <td>Analitik (onay sonrası)</td>
                </tr>
                <tr>
                <td><code>_clsk</code></td>
                <td>Microsoft Clarity — oturum kaydı</td>
                <td>1 gün</td>
                <td>Analitik (onay sonrası)</td>
                </tr>
                HTML,
            'third_party' => <<<'HTML'
                <h2>Üçüncü Taraf</h2>
                <p>Aşağıdaki üçüncü taraf araçları <strong>yalnızca açık rızanızla</strong> çalışır. Banner'da "Reddet" derseniz ya da hiçbir seçim yapmazsanız script dosyaları sayfaya hiç eklenmez, çerez yazmazlar ve sağlayıcılara istek gitmez.</p>
                <ul>
                <li><strong>Google Analytics 4</strong> (Google Ireland Ltd.) — ziyaret istatistikleri. Ölçüm kimliği: <code>G-D0VB1M1RKF</code>. Çerezler: <code>_ga</code>, <code>_ga_*</code>.</li>
                <li><strong>Microsoft Clarity</strong> (Microsoft Ireland Operations Ltd.) — <strong>ısı haritaları</strong>, tıklama ve kaydırma analizi ve <strong>oturum kayıtları</strong>: sayfadaki fare hareketleriniz, tıklamalarınız ve kaydırmalarınız kaydedilir, sonradan yeniden oynatılabilir. Çerezler: <code>_clck</code>, <code>_clsk</code>.</li>
                </ul>
                <p>Hukuki dayanak: <strong>GDPR 6(1)(a) açık rıza</strong>. Rızanızı istediğiniz zaman çerez ayarlarından geri çekebilirsiniz; geri çektiğinizde bu araçlar durdurulur ve ilgili çerezler silinir.</p>
                <p>Sözleşme tarafımız AB'deki kuruluşlardır (Google Ireland Ltd., Microsoft Ireland Operations Ltd.). Hizmetin işleyişi sırasında veriler ABD'deki ana şirketlere (Google LLC, Microsoft Corporation) ulaşabilir. ABD'ye aktarım söz konusu olduğunda, alıcının <strong>EU-U.S. Data Privacy Framework</strong> kapsamında geçerli bir sertifikasyonu bulunması hâlinde ilgili yeterlilik kararına dayanılabilir; DPF'nin kapsamadığı aktarımlarda, uygulanabilir olduğu ölçüde <strong>AB Standart Sözleşme Maddeleri</strong> gibi uygun güvenceler kullanılabilir.</p>
                <p>Meta (Facebook) ve TikTok piksel altyapısı sitede mevcuttur ancak <strong>şu anda etkin değildir</strong>. Etkinleştirilirse bu sayfa güncellenecektir.</p>
                HTML,
        ];

        $all['en'] = [
            'analytics_li' => '<li><strong>Analytics cookies:</strong> Google Analytics 4 and Microsoft Clarity. Enabled <strong>only with your explicit consent</strong>; without consent they are never loaded.</li>',
            'marketing_li' => '<li><strong>Marketing cookies:</strong> Not currently used. Advertising and remarketing pixels are inactive.</li>',
            'table_rows' => <<<'HTML'
                <tr>
                <td><code>almanyauni_consent_mkt</code></td>
                <td>Marketing consent record</td>
                <td>1 year</td>
                <td>Essential</td>
                </tr>
                <tr>
                <td><code>_ga</code></td>
                <td>Google Analytics — visitor distinction</td>
                <td>2 years</td>
                <td>Analytics (after consent)</td>
                </tr>
                <tr>
                <td><code>_ga_*</code></td>
                <td>Google Analytics — session state</td>
                <td>2 years</td>
                <td>Analytics (after consent)</td>
                </tr>
                <tr>
                <td><code>_clck</code></td>
                <td>Microsoft Clarity — visitor ID</td>
                <td>1 year</td>
                <td>Analytics (after consent)</td>
                </tr>
                <tr>
                <td><code>_clsk</code></td>
                <td>Microsoft Clarity — session recording</td>
                <td>1 day</td>
                <td>Analytics (after consent)</td>
                </tr>
                HTML,
            'third_party' => <<<'HTML'
                <h2>Third parties</h2>
                <p>The following third-party tools run <strong>only with your explicit consent</strong>. If you choose "Reject", or make no choice at all, their script files are never added to the page, they set no cookies and no request reaches the providers.</p>
                <ul>
                <li><strong>Google Analytics 4</strong> (Google Ireland Ltd.) — visit statistics. Measurement ID: <code>G-D0VB1M1RKF</code>. Cookies: <code>_ga</code>, <code>_ga_*</code>.</li>
                <li><strong>Microsoft Clarity</strong> (Microsoft Ireland Operations Ltd.) — <strong>heatmaps</strong>, click and scroll analytics, and <strong>session recordings</strong>: your mouse movements, clicks and scrolling on the page are recorded and can be replayed afterwards. Cookies: <code>_clck</code>, <code>_clsk</code>.</li>
                </ul>
                <p>Legal basis: <strong>Art. 6(1)(a) GDPR, explicit consent</strong>. You can withdraw it at any time from the cookie settings; on withdrawal these tools stop and the related cookies are deleted.</p>
                <p>Our contracting parties are the EU entities (Google Ireland Ltd., Microsoft Ireland Operations Ltd.). In operating the services, data may reach the US parent companies (Google LLC, Microsoft Corporation). Where data is transferred to the United States, reliance may be placed on the relevant adequacy decision if the recipient holds a valid certification under the <strong>EU-U.S. Data Privacy Framework</strong>; for transfers not covered by the DPF, appropriate safeguards such as the <strong>EU Standard Contractual Clauses</strong> may be used to the extent applicable.</p>
                <p>Meta (Facebook) and TikTok pixel infrastructure exists on the site but is <strong>currently inactive</strong>. This page will be updated if that changes.</p>
                HTML,
        ];

        $all['de'] = [
            'analytics_li' => '<li><strong>Statistik-Cookies:</strong> Google Analytics 4 und Microsoft Clarity. Nur <strong>mit Ihrer ausdrücklichen Einwilligung</strong> aktiv; ohne Einwilligung werden sie nicht geladen.</li>',
            'marketing_li' => '<li><strong>Marketing-Cookies:</strong> Derzeit nicht im Einsatz. Werbe- und Remarketing-Pixel sind inaktiv.</li>',
            'table_rows' => <<<'HTML'
                <tr>
                <td><code>almanyauni_consent_mkt</code></td>
                <td>Marketing-Einwilligungsstatus</td>
                <td>1 Jahr</td>
                <td>Notwendig</td>
                </tr>
                <tr>
                <td><code>_ga</code></td>
                <td>Google Analytics — Unterscheidung der Besucher</td>
                <td>2 Jahre</td>
                <td>Statistik (nach Einwilligung)</td>
                </tr>
                <tr>
                <td><code>_ga_*</code></td>
                <td>Google Analytics — Sitzungsstatus</td>
                <td>2 Jahre</td>
                <td>Statistik (nach Einwilligung)</td>
                </tr>
                <tr>
                <td><code>_clck</code></td>
                <td>Microsoft Clarity — Besucher-ID</td>
                <td>1 Jahr</td>
                <td>Statistik (nach Einwilligung)</td>
                </tr>
                <tr>
                <td><code>_clsk</code></td>
                <td>Microsoft Clarity — Sitzungsaufzeichnung</td>
                <td>1 Tag</td>
                <td>Statistik (nach Einwilligung)</td>
                </tr>
                HTML,
            'third_party' => <<<'HTML'
                <h2>Drittanbieter</h2>
                <p>Die folgenden Drittanbieter-Dienste laufen <strong>ausschließlich mit Ihrer ausdrücklichen Einwilligung</strong>. Wählen Sie „Ablehnen" oder treffen Sie gar keine Auswahl, werden ihre Skriptdateien nicht in die Seite eingebunden, es werden keine Cookies gesetzt und es geht keine Anfrage an die Anbieter.</p>
                <ul>
                <li><strong>Google Analytics 4</strong> (Google Ireland Ltd.) — Besuchsstatistik. Mess-ID: <code>G-D0VB1M1RKF</code>. Cookies: <code>_ga</code>, <code>_ga_*</code>.</li>
                <li><strong>Microsoft Clarity</strong> (Microsoft Ireland Operations Ltd.) — <strong>Heatmaps</strong>, Klick- und Scroll-Analyse sowie <strong>Sitzungsaufzeichnungen</strong>: Ihre Mausbewegungen, Klicks und Scrollvorgänge werden aufgezeichnet und können später abgespielt werden. Cookies: <code>_clck</code>, <code>_clsk</code>.</li>
                </ul>
                <p>Rechtsgrundlage: <strong>Art. 6(1)(a) DSGVO, ausdrückliche Einwilligung</strong>. Sie können sie jederzeit in den Cookie-Einstellungen widerrufen; beim Widerruf werden die Dienste gestoppt und die zugehörigen Cookies gelöscht.</p>
                <p>Unsere Vertragspartner sind die EU-Gesellschaften (Google Ireland Ltd., Microsoft Ireland Operations Ltd.). Im Betrieb der Dienste können Daten die US-Muttergesellschaften (Google LLC, Microsoft Corporation) erreichen. Soweit Daten in die USA übermittelt werden, kann auf den entsprechenden Angemessenheitsbeschluss gestützt werden, sofern der Empfänger über eine gültige Zertifizierung nach dem <strong>EU-U.S. Data Privacy Framework</strong> verfügt; für nicht vom DPF erfasste Übermittlungen können geeignete Garantien wie die <strong>EU-Standardvertragsklauseln</strong> herangezogen werden, soweit anwendbar.</p>
                <p>Die Pixel-Infrastruktur von Meta (Facebook) und TikTok ist vorhanden, aber <strong>derzeit nicht aktiv</strong>. Bei Aktivierung wird diese Seite aktualisiert.</p>
                HTML,
        ];

        return $all[$locale];
    }

    /** @return array<string, string> */
    private function privacyTexts(string $locale): array
    {
        $all = [];

        $all['tr'] = [
            'basis_li'   => '<li><strong>İstatistik (analitik)</strong> — GDPR 6(1)(a) açık rıza (Google Analytics 4, Microsoft Clarity; rıza verilmezse çalışmazlar)</li>',
            'vendor_li'  => '<li><strong>Analitik:</strong> Google Analytics 4 (Google Ireland Ltd.) ve Microsoft Clarity (Microsoft Ireland Operations Ltd.) — yalnızca açık rızanızla</li>',
            'summary_li' => '<li><strong>Analitik:</strong> Google Analytics 4 ve Microsoft Clarity çerezleri (<code>_ga</code>, <code>_ga_*</code>, <code>_clck</code>, <code>_clsk</code>) — yalnızca onay verildiyse</li>',
            'data_row' => <<<'HTML'
                <tr>
                <td><strong>Analitik verileri</strong></td>
                <td>Sayfa görüntüleme, tıklama ve kaydırma hareketleri, oturum kaydı (Microsoft Clarity), cihaz ve tarayıcı bilgisi</td>
                <td>Yalnızca analitik onayı verildiyse</td>
                </tr>
                HTML,
            'transfer_find' => "~<p>\s*ABD'ye veri aktarımı\s*<strong>\s*yapılmaz\s*</strong>\s*\.\s*</p>~su",
            'transfer_p' => <<<'HTML'
                <p>Analitik araçlar yalnızca siz izin verdiğinizde yüklenir. Sözleşme tarafımız AB'deki kuruluşlardır (Google Ireland Ltd., Microsoft Ireland Operations Ltd.); hizmetin işleyişi sırasında veriler ABD'deki ana şirketlere (Google LLC, Microsoft Corporation) ulaşabilir. ABD'ye aktarım söz konusu olduğunda, alıcının <strong>EU-U.S. Data Privacy Framework</strong> kapsamında geçerli bir sertifikasyonu bulunması hâlinde ilgili yeterlilik kararına dayanılabilir; DPF'nin kapsamadığı aktarımlarda, uygulanabilir olduğu ölçüde <strong>AB Standart Sözleşme Maddeleri</strong> gibi uygun güvenceler kullanılabilir. Rıza vermezseniz bu araçlar çalışmaz ve bu kapsamda bir aktarım gerçekleşmez.</p>
                HTML,
        ];

        $all['en'] = [
            'basis_li'   => '<li><strong>Analytics</strong> — Art. 6(1)(a) GDPR, explicit consent (Google Analytics 4, Microsoft Clarity; they do not run without it)</li>',
            'vendor_li'  => '<li><strong>Analytics:</strong> Google Analytics 4 (Google Ireland Ltd.) and Microsoft Clarity (Microsoft Ireland Operations Ltd.) — only with your explicit consent</li>',
            'summary_li' => '<li><strong>Analytics:</strong> Google Analytics 4 and Microsoft Clarity cookies (<code>_ga</code>, <code>_ga_*</code>, <code>_clck</code>, <code>_clsk</code>) — only if you consented</li>',
            'data_row' => <<<'HTML'
                <tr>
                <td><strong>Analytics data</strong></td>
                <td>Page views, click and scroll behaviour, session recording (Microsoft Clarity), device and browser information</td>
                <td>Only if you consented to analytics</td>
                </tr>
                HTML,
            'transfer_find' => '~<p>\s*<strong>\s*No transfers to the USA\.\s*</strong>\s*</p>~su',
            'transfer_p' => <<<'HTML'
                <p>Analytics tools are loaded only once you consent. Our contracting parties are the EU entities (Google Ireland Ltd., Microsoft Ireland Operations Ltd.); in operating the services, data may reach the US parent companies (Google LLC, Microsoft Corporation). Where data is transferred to the United States, reliance may be placed on the relevant adequacy decision if the recipient holds a valid certification under the <strong>EU-U.S. Data Privacy Framework</strong>; for transfers not covered by the DPF, appropriate safeguards such as the <strong>EU Standard Contractual Clauses</strong> may be used to the extent applicable. If you do not consent, these tools do not run and no such transfer takes place.</p>
                HTML,
        ];

        $all['de'] = [
            'basis_li'   => '<li><strong>Statistik</strong> — Art. 6(1)(a) DSGVO, ausdrückliche Einwilligung (Google Analytics 4, Microsoft Clarity; ohne Einwilligung laufen sie nicht)</li>',
            'vendor_li'  => '<li><strong>Analytik:</strong> Google Analytics 4 (Google Ireland Ltd.) und Microsoft Clarity (Microsoft Ireland Operations Ltd.) — nur mit Ihrer ausdrücklichen Einwilligung</li>',
            'summary_li' => '<li><strong>Statistik:</strong> Cookies von Google Analytics 4 und Microsoft Clarity (<code>_ga</code>, <code>_ga_*</code>, <code>_clck</code>, <code>_clsk</code>) — nur bei erteilter Einwilligung</li>',
            'data_row' => <<<'HTML'
                <tr>
                <td><strong>Analysedaten</strong></td>
                <td>Seitenaufrufe, Klick- und Scrollverhalten, Sitzungsaufzeichnung (Microsoft Clarity), Geräte- und Browserinformationen</td>
                <td>Nur bei erteilter Statistik-Einwilligung</td>
                </tr>
                HTML,
            'transfer_find' => '~<p>\s*<strong>\s*Keine Übermittlung in die USA\.\s*</strong>\s*</p>~su',
            'transfer_p' => <<<'HTML'
                <p>Analyse-Dienste werden erst nach Ihrer Einwilligung geladen. Unsere Vertragspartner sind die EU-Gesellschaften (Google Ireland Ltd., Microsoft Ireland Operations Ltd.); im Betrieb der Dienste können Daten die US-Muttergesellschaften (Google LLC, Microsoft Corporation) erreichen. Soweit Daten in die USA übermittelt werden, kann auf den entsprechenden Angemessenheitsbeschluss gestützt werden, sofern der Empfänger über eine gültige Zertifizierung nach dem <strong>EU-U.S. Data Privacy Framework</strong> verfügt; für nicht vom DPF erfasste Übermittlungen können geeignete Garantien wie die <strong>EU-Standardvertragsklauseln</strong> herangezogen werden, soweit anwendbar. Ohne Ihre Einwilligung laufen diese Dienste nicht und es findet insoweit keine Übermittlung statt.</p>
                HTML,
        ];

        return $all[$locale];
    }
};

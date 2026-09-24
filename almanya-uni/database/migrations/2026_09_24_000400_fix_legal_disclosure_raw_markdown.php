<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Gizlilik + Çerez Politikası izleme beyanı — HAM MARKDOWN üzerinde (3. deneme).
 *
 * NEDEN YENİ BİR MIGRATION
 * 2026_09_23_170000, 000100 ve 000150 prod'da koştu ama hiçbir şey değiştirmedi.
 * Üçü de `<li><strong>…</strong>` gibi HTML kalıpları arıyordu; oysa DB'deki gövde
 * seeder'dan gelen Markdown (`- **Analytics:** …`), HTML'e ancak görüntülemede
 * (LegalPage::getRenderedBody → Str::markdown) dönüşüyor. Testleri canlı sayfadan
 * kazınmış HTML ile beslendiği için yeşil geçtiler. Eski dosyalara DOKUNULMADI;
 * bu migration onların yerine forward-only düzeltmedir.
 *
 * DOĞRULANAN PROD GÖVDESİ (2026-09-24)
 * Seeder Markdown'u + iki fark (eski TR slug'lı iç linkler, zincir öncesi § 5 TMG)
 * render edildiğinde canlıdaki 15 sayfa/dil HTML'iyle birebir aynı çıkıyor.
 * Bu ham hâl tests/Fixtures/legal/prod-raw-snapshot.json'da.
 *
 * FİİLÎ DAVRANIŞ (7cf259a, BASIC Consent Mode)
 * GA4 (G-D0VB1M1RKF) ve Microsoft Clarity yalnızca analitik rızasıyla DOM'a girer;
 * karar vermemiş ziyaretçi reddetmiş sayılır, rıza öncesi Google'a/Microsoft'a
 * istek gitmez. Site içi sayaç (almanyauni_uid) da aynı rızaya bağlı. Pazarlama
 * araçlarının hiçbiri yapılandırılmamış. Metin bu davranışı anlatır — "rıza yoksa
 * aktarım yok" ifadesi Advanced Consent Mode'da yanlış olurdu, BASIC'te doğru.
 *
 * KURALLAR
 *   - Kalıplar satır bazlı Markdown; HTML kalıbı YOK. Madde imi (-, *, +), satır
 *     içi boşluk ve CRLF/LF farkına toleranslı.
 *   - Her locale yalnızca KENDİ metniyle güncellenir; başka dile yazılmaz.
 *   - Çeviri satırı ve aynı locale'in legacy JSON değeri AYNI sonuçla güncellenir
 *     (parity korunur). Kaynak, ziyaretçinin gördüğü çeviri satırıdır.
 *   - Idempotent: her adım ya eski bloğu bulur ya da yeni metnin zaten orada
 *     olduğunu görür. İkisi de değilse → exception. Sessiz geçiş yok.
 *   - Önce TÜM sayfa/diller hesaplanır ve doğrulanır, sonra tek transaction'da
 *     yazılır; bir dil tutmazsa hiçbir şey yazılmaz.
 *   - Yazımdan önce son kontrol: yanlış beyan / Türkçe sızıntı kalmamalı, zorunlu
 *     beyanlar bulunmalı. Tutmazsa exception.
 *   - Yalnızca privacy + cookies. § 7 / §§ 8-10 / §§ 7-10 TMG atıflarına
 *     dokunulmaz (hukuki inceleme bekliyor — LEGAL_REFERENCE_REVIEW_REQUIRED).
 */
return new class extends Migration
{
    private const LOCALES = ['tr', 'en', 'de'];

    /** Hiçbir dilde kalmaması gereken, fiilî davranışa aykırı ifadeler. */
    private const FORBIDDEN = [
        'Google Analytics yok', 'no Google Analytics', 'kein Google Analytics',
        'We do not use Google Analytics', 'Wir nutzen kein Google Analytics',
        'Google Analytics, Facebook Pixel veya benzer kullanmıyoruz',
        "ABD'ye veri aktarımı **yapılmaz**", 'No transfers to the USA', 'Keine Übermittlung in die USA',
        'Self-hosted analitik tercih', 'prefer self-hosted analytics', 'bevorzugen self-hosted',
        'Anonim ziyaretçi sayımı', 'Anonymous visitor counts', 'Anonyme Besucherzählung',
    ];

    /** EN/DE gövdelerinde kalmaması gereken Türkçe parçalar. */
    private const TURKISH_LEAKS = [
        'dil tercihi', 'Onay gerektirmez', 'yönetimi', 'Anonim ziyaretçi', 'onayınızla',
        'etkinleştirilir', 'Yok.', 'Üçüncü taraf', 'kullanmıyoruz',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('legal_pages')) {
            Log::warning('legal markdown fix: legal_pages tablosu yok, atlandı');

            return;
        }

        $hasTranslations = Schema::hasTable('legal_page_translations');
        $pages = DB::table('legal_pages')->whereIn('key', ['privacy', 'cookies'])->get()->keyBy('key');

        if ($pages->isEmpty()) {
            // Boş kurulum (CI/test DB): düzeltilecek içerik yok.
            Log::info('legal markdown fix: privacy/cookies kaydı yok, atlandı');

            return;
        }

        $report = [];
        $errors = [];
        $writes = [];   // [page_id => ['bodies' => [...], 'descriptions' => [...], 'rows' => [locale => [...]]]]

        foreach (['privacy', 'cookies'] as $key) {
            $page = $pages->get($key);

            if (! $page) {
                $report[] = "{$key}: KAYIT YOK";
                Log::warning("legal markdown fix: '{$key}' sayfası yok");

                continue;
            }

            $bodies = $this->decode($page->bodies);
            $descriptions = $this->decode($page->descriptions);
            $rows = $hasTranslations
                ? DB::table('legal_page_translations')->where('legal_page_id', $page->id)->get()->keyBy('locale')
                : collect();

            $pageWrite = ['bodies' => $bodies, 'descriptions' => $descriptions, 'rows' => [], 'changed' => false];

            foreach (self::LOCALES as $locale) {
                $row = $rows->get($locale);
                $body = $row->body ?? ($bodies[$locale] ?? null);

                if (! is_string($body) || trim($body) === '') {
                    // Bu dilde içerik yok → o dil 404 verir; başka dilden DOLDURULMAZ.
                    $report[] = "{$key}/{$locale}: içerik yok, atlandı";

                    continue;
                }

                $tag = "{$key}/{$locale}";
                $drift = $row && isset($bodies[$locale]) && $bodies[$locale] !== $row->body;

                [$newBody, $applied, $already, $missed] = $this->applyOps($body, $this->bodyOps($key, $locale));

                $description = $row->description ?? ($descriptions[$locale] ?? null);
                [$newDescription, $descNote, $descMissed] = $this->fixDescription($key, $locale, $description);

                foreach ($missed as $label) {
                    $errors[] = "{$tag}: '{$label}' bloğu bulunamadı ve yeni metin de yok";
                }
                if ($descMissed) {
                    $errors[] = "{$tag}: meta açıklama beklenen biçimde değil";
                }

                foreach ($this->postConditionErrors($key, $locale, $newBody, (string) $newDescription) as $e) {
                    $errors[] = "{$tag}: {$e}";
                }

                $bodyChanged = $newBody !== $body || $drift;
                $descChanged = $newDescription !== $description
                    || ($row && ($descriptions[$locale] ?? null) !== $row->description);

                if ($bodyChanged || $descChanged) {
                    $pageWrite['bodies'][$locale] = $newBody;
                    if ($newDescription !== null) {
                        $pageWrite['descriptions'][$locale] = $newDescription;
                    }
                    if ($row) {
                        $pageWrite['rows'][$locale] = ['body' => $newBody, 'description' => $newDescription];
                    }
                    $pageWrite['changed'] = true;
                }

                $report[] = "{$tag}: " . (count($applied) ? count($applied) . ' blok güncellendi' : 'gövde zaten güncel')
                    . (count($already) ? ' (' . count($already) . ' blok zaten yeni)' : '')
                    . ($descNote ? ", açıklama {$descNote}" : '')
                    . ($drift ? ' — UYARI: legacy JSON çeviri satırından farklıydı, eşitlendi' : '')
                    . ($row ? '' : ' — çeviri satırı yok, yalnız legacy JSON');
            }

            $writes[$page->id] = $pageWrite;
        }

        if ($errors !== []) {
            // Hiçbir şey yazılmadı. Sessizce geçmek yerine migrate'i durdur.
            throw new RuntimeException("legal markdown fix: prod gövdesi beklenen biçimde değil, HİÇBİR ŞEY YAZILMADI:\n  "
                . implode("\n  ", $errors));
        }

        DB::transaction(function () use ($writes) {
            foreach ($writes as $pageId => $w) {
                if (! $w['changed']) {
                    continue;
                }

                DB::table('legal_pages')->where('id', $pageId)->update([
                    'bodies'         => json_encode($w['bodies'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'descriptions'   => json_encode($w['descriptions'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    // Sayfada "Son güncelleme" olarak görünen alan budur.
                    'effective_date' => now()->toDateString(),
                    'updated_at'     => now(),
                ]);

                foreach ($w['rows'] as $locale => $fields) {
                    DB::table('legal_page_translations')
                        ->where('legal_page_id', $pageId)
                        ->where('locale', $locale)
                        ->update($fields + ['updated_at' => now()]);
                }
            }
        });

        echo "legal markdown fix:\n  " . implode("\n  ", $report) . "\n";
    }

    public function down(): void
    {
        // Bilinçli olarak boş: eski metin ("Google Analytics kullanmıyoruz", "ABD'ye
        // aktarım yapılmaz") GA4 + Clarity çalışırken yanlış beyandı. Geri yazmak
        // ziyaretçiye yanlış bilgi vermek olur.
    }

    /* ------------------------------------------------------------------ motor */

    /**
     * @param  array<int, array{label: string, find: string, replace: string, mode?: string}>  $ops
     * @return array{0: string, 1: array<int, string>, 2: array<int, string>, 3: array<int, string>}
     */
    private function applyOps(string $body, array $ops): array
    {
        $eol = str_contains($body, "\r\n") ? "\r\n" : "\n";
        $applied = $already = $missed = [];

        foreach ($ops as $op) {
            $replace = str_replace("\n", $eol, $op['replace']);
            // Yeni metnin ilk satırı idempotency işaretidir.
            $marker = trim(strtok($op['replace'], "\n"));

            // İşaret ÖNCE bakılır: 'after' adımlarının çapası (ör. almanyauni_consent
            // satırı) ikinci koşuda da yerinde durur; önce eşleştirseydik satırları çoğaltırdık.
            if ($marker !== '' && str_contains($body, $marker)) {
                $already[] = $op['label'];
            } elseif (preg_match($op['find'], $body, $m, PREG_OFFSET_CAPTURE)) {
                [$match, $offset] = $m[0];
                // Madde imini (-, *, +) ve girintiyi koru.
                $prefix = $m['prefix'][0] ?? '';
                $new = ($op['mode'] ?? 'replace') === 'after'
                    ? $match . $eol . $replace
                    : $prefix . $replace;

                $body = substr_replace($body, $new, $offset, strlen($match));
                $applied[] = $op['label'];
            } else {
                $missed[] = $op['label'];
            }
        }

        return [$body, $applied, $already, $missed];
    }

    /** Satır başı madde imi: "- ", "* ", "+ " (girintili olabilir). */
    private function bullet(string $rest): string
    {
        return '~^(?<prefix>\h*[-*+]\h+)' . $rest . '[^\r\n]*~mu';
    }

    /** Tek satırlık düz metin/tablo satırı. */
    private function line(string $rest): string
    {
        return '~^\h*' . $rest . '[^\r\n]*~mu';
    }

    private function q(string $s): string
    {
        // Metin içindeki boşlukları esnek eşleştir.
        return preg_replace('~\\\\ |\s+~', '\h+', preg_quote($s, '~'));
    }

    /** @return array<int, array{label: string, find: string, replace: string, mode?: string}> */
    private function bodyOps(string $key, string $locale): array
    {
        return $key === 'cookies' ? $this->cookieOps($locale) : $this->privacyOps($locale);
    }

    /**
     * @return array{0: ?string, 1: ?string, 2: bool} [yeni açıklama, not, beklenmeyen biçim mi]
     */
    private function fixDescription(string $key, string $locale, ?string $description): array
    {
        if ($key !== 'cookies' || $description === null) {
            return [$description, null, false];
        }

        $new = $this->cookieTexts($locale)['description'];

        if (str_contains($description, 'Microsoft Clarity')) {
            return [$description, 'zaten güncel', false];
        }

        $old = ['tr' => 'üçüncü taraf yok', 'en' => 'no third parties', 'de' => 'keine Drittanbieter'][$locale];

        if (! str_contains($description, $old)) {
            return [$description, null, true];
        }

        return [$new, 'güncellendi', false];
    }

    /** @return array<int, string> */
    private function postConditionErrors(string $key, string $locale, string $body, string $description): array
    {
        $errors = [];
        $haystack = $body . "\n" . $description;

        foreach (self::FORBIDDEN as $phrase) {
            if (str_contains($haystack, $phrase)) {
                $errors[] = "yanlış beyan kaldı: '{$phrase}'";
            }
        }

        if ($locale !== 'tr') {
            foreach (self::TURKISH_LEAKS as $leak) {
                if (str_contains($haystack, $leak)) {
                    $errors[] = "Türkçe sızıntı kaldı: '{$leak}'";
                }
            }
        }

        $basis = ['tr' => 'GDPR 6(1)(a) açık rıza', 'en' => 'Art. 6(1)(a) GDPR', 'de' => 'Art. 6(1)(a) DSGVO'][$locale];
        $required = ['Google Analytics 4', 'G-D0VB1M1RKF', 'Microsoft Clarity', 'EU-U.S. Data Privacy Framework', $basis, 'data-cookie-settings'];
        if ($key === 'cookies') {
            array_push($required, '`_ga`', '`_ga_*`', '`_clck`', '`_clsk`');
        }

        foreach ($required as $needle) {
            if (! str_contains($body, $needle)) {
                $errors[] = "zorunlu beyan eksik: '{$needle}'";
            }
        }

        return $errors;
    }

    /** @return array<string, string> */
    private function decode(?string $json): array
    {
        $value = json_decode($json ?? '', true);

        return is_array($value) ? $value : [];
    }

    /* ---------------------------------------------------------------- cookies */

    /** @return array<int, array{label: string, find: string, replace: string, mode?: string}> */
    private function cookieOps(string $locale): array
    {
        $t = $this->cookieTexts($locale);

        $old = [
            'tr' => [
                'essential' => null, // TR satırı doğru; yalnızca EN/DE'de Türkçe kalmış
                'session'   => null,
                'analytics' => '\*\*Analitik çerezler:\*\*\h*Anonim ziyaretçi sayımı',
                'marketing' => '\*\*Pazarlama çerezleri:\*\*\h*Yok\.',
                'control'   => $this->q("Sayfa açılışında çerez banner'ı görürsünüz."),
                'third'     => '\*\*Yok\.\*\*\h*Google Analytics',
            ],
            'en' => [
                'essential' => '\*\*Essential cookies:\*\*\h*Session,\h*CSRF protection,\h*dil tercihi',
                'session'   => '\|\h*`laravel_session`\h*\|\h*Session yönetimi\h*\|',
                'analytics' => '\*\*Analytics cookies:\*\*\h*Anonim ziyaretçi sayımı',
                'marketing' => '\*\*Marketing cookies:\*\*\h*Yok\.',
                'control'   => $this->q('On first visit you see a cookie banner.'),
                'third'     => '\*\*None\.\*\*\h*We do not use Google Analytics',
            ],
            'de' => [
                'essential' => '\*\*Notwendige Cookies:\*\*\h*Session,\h*CSRF-Schutz,\h*dil tercihi',
                'session'   => '\|\h*`laravel_session`\h*\|\h*Session yönetimi\h*\|',
                'analytics' => '\*\*Statistik-Cookies:\*\*\h*Anonim ziyaretçi sayımı',
                'marketing' => '\*\*Marketing-Cookies:\*\*\h*Yok\.',
                'control'   => $this->q('Beim ersten Besuch sehen Sie ein Cookie-Banner.'),
                'third'     => '\*\*Keine\.\*\*\h*Wir nutzen kein Google Analytics',
            ],
        ][$locale];

        $ops = [];

        if ($old['essential']) {
            $ops[] = ['label' => 'zorunlu çerez satırı', 'find' => $this->bullet($old['essential']), 'replace' => $t['essential']];
        }
        if ($old['session']) {
            $ops[] = ['label' => 'laravel_session satırı', 'find' => $this->line($old['session']), 'replace' => $t['session_row']];
        }

        return array_merge($ops, [
            ['label' => 'analitik çerez satırı', 'find' => $this->bullet($old['analytics']), 'replace' => $t['analytics']],
            ['label' => 'pazarlama çerez satırı', 'find' => $this->bullet($old['marketing']), 'replace' => $t['marketing']],
            ['label' => 'kontrol paragrafı', 'find' => $this->line($old['control']), 'replace' => $t['control']],
            [
                // Çapa: tablonun son satırı almanyauni_consent; yeni satırlar ardına.
                'label'   => 'çerez tablosu',
                'find'    => $this->line('\|\h*`almanyauni_consent`\h*\|'),
                'replace' => $t['table_rows'],
                'mode'    => 'after',
            ],
            ['label' => 'üçüncü taraf bölümü', 'find' => $this->line($old['third']), 'replace' => $t['third_party']],
        ]);
    }

    /** @return array<string, string> */
    private function cookieTexts(string $locale): array
    {
        $all = [];

        $all['tr'] = [
            'description' => 'AlmanyaUni / ApplyToGerman çerez kullanımı — zorunlu çerezler, yalnızca rızayla çalışan analitik (Google Analytics 4, Microsoft Clarity), çerez listesi ve rızayı geri çekme.',
            'analytics'   => '**Analitik çerezler:** Google Analytics 4, Microsoft Clarity ve site içi sayfa sayacı. **Yalnızca açık rızanızla** etkinleştirilir; rıza vermezseniz bu araçlar hiç yüklenmez.',
            'marketing'   => '**Pazarlama çerezleri:** Şu anda kullanılmıyor. Reklam ve yeniden pazarlama pikselleri etkin değildir.',
            'control'     => <<<'MD'
İlk ziyaretinizde bir çerez banner'ı görürsünüz: **Tümünü kabul et**, **Tümünü reddet** veya **Ayarlar**. Ayarlar'da analitik ve pazarlama kategorileri varsayılan olarak kapalıdır; hiçbir seçim yapmazsanız hiçbir analitik veya pazarlama aracı çalışmaz. Kararınızı istediğiniz zaman <a href="#" data-cookie-settings>çerez ayarlarından</a> değiştirebilir ya da rızanızı geri çekebilirsiniz; geri çektiğinizde ilgili çerezler silinir ve sayfa yeniden yüklenir.
MD,
            'table_rows'  => <<<'MD'
| `almanyauni_consent_mkt` | Pazarlama onayı kaydı | 1 yıl | Zorunlu |
| `_ga` | Google Analytics 4 — ziyaretçiyi ayırt etme | 2 yıl | Analitik (onay sonrası) |
| `_ga_*` | Google Analytics 4 — oturum durumu (`_ga_D0VB1M1RKF`) | 2 yıl | Analitik (onay sonrası) |
| `_clck` | Microsoft Clarity — ziyaretçi kimliği ve ayarları | 1 yıl | Analitik (onay sonrası) |
| `_clsk` | Microsoft Clarity — sayfa görüntülemelerini tek oturum kaydında birleştirme | 1 gün | Analitik (onay sonrası) |
| `CLID`, `MUID`, `ANONCHK`, `SM`, `MR` | Microsoft Clarity — clarity.ms alan adında ayarlanan çerezler | Microsoft tarafından belirlenir | Analitik (onay sonrası) |
MD,
            'third_party' => <<<'MD'
Aşağıdaki üçüncü taraf araçları **yalnızca açık rızanızla** çalışır. Banner'da "Tümünü reddet" derseniz ya da hiçbir seçim yapmazsanız bu araçların script dosyaları sayfaya hiç eklenmez, çerez yazmazlar ve sağlayıcılara istek gitmez.

- **Google Analytics 4** (Google Ireland Ltd.) — ziyaret istatistikleri. Ölçüm kimliği: `G-D0VB1M1RKF`. Çerezler: `_ga`, `_ga_*`.
- **Microsoft Clarity** (Microsoft Ireland Operations Ltd.) — **ısı haritaları**, **tıklama ve kaydırma analizi** ve **oturum kayıtları**: sayfadaki fare hareketleriniz, tıklamalarınız ve kaydırmalarınız kaydedilir ve sonradan yeniden oynatılabilir. Çerezler: `_clck`, `_clsk`.

Hukuki dayanak: **GDPR 6(1)(a) açık rıza**; cihazınızda bilgi saklanması için § 25 (1) TDDDG. Rızanızı istediğiniz zaman <a href="#" data-cookie-settings>çerez ayarlarından</a> geri çekebilirsiniz; geri çektiğinizde bu araçlar durdurulur ve ilgili çerezler silinir. Geri çekme, o zamana kadar yapılan işlemenin hukuka uygunluğunu etkilemez.

Sözleşme tarafımız AB'deki kuruluşlardır (Google Ireland Ltd., Microsoft Ireland Operations Ltd.). Hizmetin işleyişi sırasında veriler ABD'deki ana şirketlere (Google LLC, Microsoft Corporation) ulaşabilir. ABD'ye aktarım söz konusu olduğunda, alıcının **EU-U.S. Data Privacy Framework** kapsamında geçerli bir sertifikasyonu bulunması hâlinde ilgili yeterlilik kararına dayanılabilir; DPF'nin kapsamadığı aktarımlarda, uygulanabilir olduğu ölçüde **AB Standart Sözleşme Maddeleri** gibi uygun güvenceler kullanılabilir.

Meta (Facebook) ve TikTok piksel altyapısı sitede mevcuttur ancak **şu anda etkin değildir**. Etkinleştirilirse bu sayfa güncellenecektir.
MD,
        ];

        $all['en'] = [
            'description' => 'AlmanyaUni / ApplyToGerman cookie usage — essential cookies, consent-only analytics (Google Analytics 4, Microsoft Clarity), cookie list and withdrawing consent.',
            'essential'   => '**Essential cookies:** Session, CSRF protection, language preference. No consent required (Art. 6(1)(f) GDPR).',
            'session_row' => '| `laravel_session` | Session management | 2 hours | Essential |',
            'analytics'   => '**Analytics cookies:** Google Analytics 4, Microsoft Clarity and our own page-view counter. Enabled **only with your explicit consent**; without consent these tools are never loaded.',
            'marketing'   => '**Marketing cookies:** Not currently used. Advertising and remarketing pixels are inactive.',
            'control'     => <<<'MD'
On your first visit you see a cookie banner: **Accept all**, **Reject all** or **Settings**. In Settings, the analytics and marketing categories are off by default; if you make no choice, no analytics or marketing tool runs. You can change your decision or withdraw consent at any time in the <a href="#" data-cookie-settings>cookie settings</a>; on withdrawal the related cookies are deleted and the page reloads.
MD,
            'table_rows'  => <<<'MD'
| `almanyauni_consent_mkt` | Marketing consent record | 1 year | Essential |
| `_ga` | Google Analytics 4 — distinguishes visitors | 2 years | Analytics (after consent) |
| `_ga_*` | Google Analytics 4 — session state (`_ga_D0VB1M1RKF`) | 2 years | Analytics (after consent) |
| `_clck` | Microsoft Clarity — visitor ID and settings | 1 year | Analytics (after consent) |
| `_clsk` | Microsoft Clarity — joins page views into one session recording | 1 day | Analytics (after consent) |
| `CLID`, `MUID`, `ANONCHK`, `SM`, `MR` | Microsoft Clarity — cookies set on the clarity.ms domain | Set by Microsoft | Analytics (after consent) |
MD,
            'third_party' => <<<'MD'
The following third-party tools run **only with your explicit consent**. If you choose "Reject all", or make no choice at all, their script files are never added to the page, they set no cookies and no request reaches the providers.

- **Google Analytics 4** (Google Ireland Ltd.) — visit statistics. Measurement ID: `G-D0VB1M1RKF`. Cookies: `_ga`, `_ga_*`.
- **Microsoft Clarity** (Microsoft Ireland Operations Ltd.) — **heatmaps**, **click and scroll analytics** and **session recordings**: your mouse movements, clicks and scrolling on the page are recorded and can be replayed afterwards. Cookies: `_clck`, `_clsk`.

Legal basis: **Art. 6(1)(a) GDPR, explicit consent**; for storing information on your device, § 25(1) TDDDG. You can withdraw consent at any time in the <a href="#" data-cookie-settings>cookie settings</a>; on withdrawal these tools stop and the related cookies are deleted. Withdrawal does not affect the lawfulness of processing carried out before it.

Our contracting parties are the EU entities (Google Ireland Ltd., Microsoft Ireland Operations Ltd.). In operating the services, data may reach the US parent companies (Google LLC, Microsoft Corporation). Where data is transferred to the United States, reliance may be placed on the relevant adequacy decision if the recipient holds a valid certification under the **EU-U.S. Data Privacy Framework**; for transfers not covered by the DPF, appropriate safeguards such as the **EU Standard Contractual Clauses** may be used to the extent applicable.

Meta (Facebook) and TikTok pixel infrastructure exists on the site but is **currently inactive**. This page will be updated if that changes.
MD,
        ];

        $all['de'] = [
            'description' => 'AlmanyaUni / ApplyToGerman Cookie-Nutzung — notwendige Cookies, Statistik nur mit Einwilligung (Google Analytics 4, Microsoft Clarity), Cookie-Liste und Widerruf.',
            'essential'   => '**Notwendige Cookies:** Session, CSRF-Schutz, Sprachwahl. Keine Einwilligung erforderlich (Art. 6(1)(f) DSGVO).',
            'session_row' => '| `laravel_session` | Session-Verwaltung | 2 Stunden | Notwendig |',
            'analytics'   => '**Statistik-Cookies:** Google Analytics 4, Microsoft Clarity und unser eigener Seitenaufrufzähler. Nur **mit Ihrer ausdrücklichen Einwilligung** aktiv; ohne Einwilligung werden diese Dienste nicht geladen.',
            'marketing'   => '**Marketing-Cookies:** Derzeit nicht im Einsatz. Werbe- und Remarketing-Pixel sind inaktiv.',
            'control'     => <<<'MD'
Beim ersten Besuch sehen Sie ein Cookie-Banner: **Alle akzeptieren**, **Alle ablehnen** oder **Einstellungen**. In den Einstellungen sind Statistik und Marketing standardmäßig deaktiviert; treffen Sie keine Auswahl, läuft kein Statistik- oder Marketing-Dienst. Sie können Ihre Entscheidung jederzeit in den <a href="#" data-cookie-settings>Cookie-Einstellungen</a> ändern oder Ihre Einwilligung widerrufen; beim Widerruf werden die zugehörigen Cookies gelöscht und die Seite neu geladen.
MD,
            'table_rows'  => <<<'MD'
| `almanyauni_consent_mkt` | Marketing-Einwilligungsstatus | 1 Jahr | Notwendig |
| `_ga` | Google Analytics 4 — Unterscheidung der Besucher | 2 Jahre | Statistik (nach Einwilligung) |
| `_ga_*` | Google Analytics 4 — Sitzungsstatus (`_ga_D0VB1M1RKF`) | 2 Jahre | Statistik (nach Einwilligung) |
| `_clck` | Microsoft Clarity — Besucher-ID und Einstellungen | 1 Jahr | Statistik (nach Einwilligung) |
| `_clsk` | Microsoft Clarity — fasst Seitenaufrufe zu einer Sitzungsaufzeichnung zusammen | 1 Tag | Statistik (nach Einwilligung) |
| `CLID`, `MUID`, `ANONCHK`, `SM`, `MR` | Microsoft Clarity — auf der Domain clarity.ms gesetzte Cookies | Von Microsoft festgelegt | Statistik (nach Einwilligung) |
MD,
            'third_party' => <<<'MD'
Die folgenden Drittanbieter-Dienste laufen **ausschließlich mit Ihrer ausdrücklichen Einwilligung**. Wählen Sie „Alle ablehnen" oder treffen Sie gar keine Auswahl, werden ihre Skriptdateien nicht in die Seite eingebunden, es werden keine Cookies gesetzt und es geht keine Anfrage an die Anbieter.

- **Google Analytics 4** (Google Ireland Ltd.) — Besuchsstatistik. Mess-ID: `G-D0VB1M1RKF`. Cookies: `_ga`, `_ga_*`.
- **Microsoft Clarity** (Microsoft Ireland Operations Ltd.) — **Heatmaps**, **Klick- und Scroll-Analyse** sowie **Sitzungsaufzeichnungen**: Ihre Mausbewegungen, Klicks und Scrollvorgänge auf der Seite werden aufgezeichnet und können später abgespielt werden. Cookies: `_clck`, `_clsk`.

Rechtsgrundlage: **Art. 6(1)(a) DSGVO, ausdrückliche Einwilligung**; für die Speicherung von Informationen auf Ihrem Endgerät § 25 Abs. 1 TDDDG. Sie können Ihre Einwilligung jederzeit in den <a href="#" data-cookie-settings>Cookie-Einstellungen</a> widerrufen; beim Widerruf werden die Dienste gestoppt und die zugehörigen Cookies gelöscht. Der Widerruf berührt nicht die Rechtmäßigkeit der bis dahin erfolgten Verarbeitung.

Unsere Vertragspartner sind die EU-Gesellschaften (Google Ireland Ltd., Microsoft Ireland Operations Ltd.). Im Betrieb der Dienste können Daten die US-Muttergesellschaften (Google LLC, Microsoft Corporation) erreichen. Soweit Daten in die USA übermittelt werden, kann auf den entsprechenden Angemessenheitsbeschluss gestützt werden, sofern der Empfänger über eine gültige Zertifizierung nach dem **EU-U.S. Data Privacy Framework** verfügt; für nicht vom DPF erfasste Übermittlungen können geeignete Garantien wie die **EU-Standardvertragsklauseln** herangezogen werden, soweit anwendbar.

Die Pixel-Infrastruktur von Meta (Facebook) und TikTok ist vorhanden, aber **derzeit nicht aktiv**. Bei Aktivierung wird diese Seite aktualisiert.
MD,
        ];

        return $all[$locale];
    }

    /* ---------------------------------------------------------------- privacy */

    /** @return array<int, array{label: string, find: string, replace: string, mode?: string}> */
    private function privacyOps(string $locale): array
    {
        $t = $this->privacyTexts($locale);

        $old = [
            'tr' => [
                'dataRow'  => '\|\h*\*\*Çerez verileri\*\*\h*\|',
                'basis'    => '\*\*İstatistik \(analitik\)\*\*\h*—\h*GDPR 6\(1\)\(f\)',
                'vendor'   => '\*\*Analitik:\*\*\h*Self-hosted',
                'transfer' => "ABD'ye\h+veri\h+aktarımı\h+\*\*yapılmaz\*\*\.",
                'summary'  => '\*\*Analitik:\*\*\h*Anonim ziyaretçi sayımı',
            ],
            'en' => [
                'dataRow'  => '\|\h*\*\*Cookies\*\*\h*\|\h*Session,\h*language',
                'basis'    => '\*\*Analytics\*\*\h*—\h*Art\. 6\(1\)\(f\) GDPR',
                'vendor'   => '\*\*Analytics:\*\*\h*Self-hosted',
                'transfer' => '\*\*No transfers to the USA\.\*\*',
                'summary'  => '\*\*Analytics:\*\*\h*Anonymous visitor counts',
            ],
            'de' => [
                'dataRow'  => '\|\h*\*\*Cookies\*\*\h*\|\h*Session,\h*Sprache',
                'basis'    => '\*\*Statistik\*\*\h*—\h*Art\. 6\(1\)\(f\) DSGVO',
                'vendor'   => '\*\*Analytik:\*\*\h*Self-hosted',
                'transfer' => '\*\*Keine Übermittlung in die USA\.\*\*',
                'summary'  => '\*\*Statistik:\*\*\h*Anonyme Besucherzählung',
            ],
        ][$locale];

        return [
            [
                'label'   => '§2 veri tablosu (analitik satırı)',
                'find'    => $this->line($old['dataRow']),
                'replace' => $t['data_row'],
                'mode'    => 'after',
            ],
            ['label' => '§3 hukuki dayanak', 'find' => $this->bullet($old['basis']), 'replace' => $t['basis']],
            ['label' => '§5 analitik sağlayıcı', 'find' => $this->bullet($old['vendor']), 'replace' => $t['vendor']],
            ['label' => '§5 ABD aktarımı', 'find' => $this->line($old['transfer']), 'replace' => $t['transfer']],
            ['label' => '§6 çerez özeti', 'find' => $this->bullet($old['summary']), 'replace' => $t['summary']],
        ];
    }

    /** @return array<string, string> */
    private function privacyTexts(string $locale): array
    {
        $all = [];

        $all['tr'] = [
            'data_row' => '| **Analitik verileri** | Sayfa görüntüleme, tıklama ve kaydırma hareketleri, oturum kayıtları ve ısı haritası verileri (Microsoft Clarity), cihaz ve tarayıcı bilgisi | Yalnızca analitik onayı verildiyse |',
            'basis'    => '**İstatistik (analitik)** — GDPR 6(1)(a) açık rıza ve § 25 (1) TDDDG (Google Analytics 4, Microsoft Clarity ve site içi sayfa sayacı; rıza verilmezse çalışmazlar)',
            'vendor'   => '**Analitik:** Google Analytics 4 (Google Ireland Ltd., ölçüm kimliği `G-D0VB1M1RKF`) ve Microsoft Clarity (Microsoft Ireland Operations Ltd.; ısı haritaları, tıklama ve kaydırma analizi, oturum kayıtları) — yalnızca açık rızanızla',
            'transfer' => "Analitik araçlar yalnızca siz izin verdiğinizde yüklenir. Sözleşme tarafımız AB'deki kuruluşlardır (Google Ireland Ltd., Microsoft Ireland Operations Ltd.); hizmetin işleyişi sırasında veriler ABD'deki ana şirketlere (Google LLC, Microsoft Corporation) ulaşabilir. ABD'ye aktarım söz konusu olduğunda, alıcının **EU-U.S. Data Privacy Framework** kapsamında geçerli bir sertifikasyonu bulunması hâlinde ilgili yeterlilik kararına dayanılabilir; DPF'nin kapsamadığı aktarımlarda, uygulanabilir olduğu ölçüde **AB Standart Sözleşme Maddeleri** gibi uygun güvenceler kullanılabilir. Rıza vermezseniz bu araçlar çalışmaz ve bu kapsamda bir aktarım gerçekleşmez.",
            'summary'  => '**Analitik:** Google Analytics 4 ve Microsoft Clarity çerezleri (`_ga`, `_ga_*`, `_clck`, `_clsk`) ve site içi sayaç (`almanyauni_uid`) — yalnızca onay verildiyse. Rızanızı istediğiniz zaman <a href="#" data-cookie-settings>çerez ayarlarından</a> geri çekebilirsiniz.',
        ];

        $all['en'] = [
            'data_row' => '| **Analytics data** | Page views, click and scroll behaviour, session recordings and heatmap data (Microsoft Clarity), device and browser information | Only if you consented to analytics |',
            'basis'    => '**Analytics** — Art. 6(1)(a) GDPR, explicit consent, and § 25(1) TDDDG (Google Analytics 4, Microsoft Clarity and our own page-view counter; they do not run without consent)',
            'vendor'   => '**Analytics:** Google Analytics 4 (Google Ireland Ltd., measurement ID `G-D0VB1M1RKF`) and Microsoft Clarity (Microsoft Ireland Operations Ltd.; heatmaps, click and scroll analytics, session recordings) — only with your explicit consent',
            'transfer' => 'Analytics tools are loaded only once you consent. Our contracting parties are the EU entities (Google Ireland Ltd., Microsoft Ireland Operations Ltd.); in operating the services, data may reach the US parent companies (Google LLC, Microsoft Corporation). Where data is transferred to the United States, reliance may be placed on the relevant adequacy decision if the recipient holds a valid certification under the **EU-U.S. Data Privacy Framework**; for transfers not covered by the DPF, appropriate safeguards such as the **EU Standard Contractual Clauses** may be used to the extent applicable. If you do not consent, these tools do not run and no such transfer takes place.',
            'summary'  => '**Analytics:** Google Analytics 4 and Microsoft Clarity cookies (`_ga`, `_ga_*`, `_clck`, `_clsk`) and our own counter (`almanyauni_uid`) — only if you consented. You can withdraw consent at any time in the <a href="#" data-cookie-settings>cookie settings</a>.',
        ];

        $all['de'] = [
            'data_row' => '| **Analysedaten** | Seitenaufrufe, Klick- und Scrollverhalten, Sitzungsaufzeichnungen und Heatmap-Daten (Microsoft Clarity), Geräte- und Browserinformationen | Nur bei erteilter Statistik-Einwilligung |',
            'basis'    => '**Statistik** — Art. 6(1)(a) DSGVO, ausdrückliche Einwilligung, und § 25 Abs. 1 TDDDG (Google Analytics 4, Microsoft Clarity und unser eigener Seitenaufrufzähler; ohne Einwilligung laufen sie nicht)',
            'vendor'   => '**Analytik:** Google Analytics 4 (Google Ireland Ltd., Mess-ID `G-D0VB1M1RKF`) und Microsoft Clarity (Microsoft Ireland Operations Ltd.; Heatmaps, Klick- und Scroll-Analyse, Sitzungsaufzeichnungen) — nur mit Ihrer ausdrücklichen Einwilligung',
            'transfer' => 'Analyse-Dienste werden erst nach Ihrer Einwilligung geladen. Unsere Vertragspartner sind die EU-Gesellschaften (Google Ireland Ltd., Microsoft Ireland Operations Ltd.); im Betrieb der Dienste können Daten die US-Muttergesellschaften (Google LLC, Microsoft Corporation) erreichen. Soweit Daten in die USA übermittelt werden, kann auf den entsprechenden Angemessenheitsbeschluss gestützt werden, sofern der Empfänger über eine gültige Zertifizierung nach dem **EU-U.S. Data Privacy Framework** verfügt; für nicht vom DPF erfasste Übermittlungen können geeignete Garantien wie die **EU-Standardvertragsklauseln** herangezogen werden, soweit anwendbar. Ohne Ihre Einwilligung laufen diese Dienste nicht und es findet insoweit keine Übermittlung statt.',
            'summary'  => '**Statistik:** Cookies von Google Analytics 4 und Microsoft Clarity (`_ga`, `_ga_*`, `_clck`, `_clsk`) und unser eigener Zähler (`almanyauni_uid`) — nur bei erteilter Einwilligung. Sie können Ihre Einwilligung jederzeit in den <a href="#" data-cookie-settings>Cookie-Einstellungen</a> widerrufen.',
        ];

        return $all[$locale];
    }
};

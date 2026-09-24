<?php

namespace App\Console\Commands;

use App\Models\LegalPage;
use Illuminate\Console\Command;

/**
 * Hukuki sayfaların İÇERİĞİNİ denetler.
 *
 * Neden `legal:parity` yetmez: parity yalnızca "legacy JSON ile çeviri kaydı aynı
 * mı?" sorusunu yanıtlar. Yanlış bir metin birebir taşınırsa parity PASS verir.
 * Bu komut metnin KENDİSİNE bakar:
 *
 *   - EN/DE sayfalarında Türkçe sızıntı kalmış mı?
 *   - Yürürlükten kalkmış TMG atfı kalmış mı?
 *   - İzleme beyanları (GA4 / Clarity / 6(1)(a) / DPF) yerinde mi?
 *   - Eski yanlış beyanlar ("Google Analytics kullanmıyoruz" vb.) geri gelmiş mi?
 *
 * Okuma, ziyaretçinin gördüğü yoldan yapılır (LegalPage::getBody), yani çeviri
 * kaydı varsa ondan, rollout penceresinde legacy JSON'dan. Deploy sonrası
 * koşulacak kontrol budur.
 */
class LegalContentAudit extends Command
{
    protected $signature = 'legal:audit {--json : Bulguları JSON olarak döker (test/CI için)}';

    protected $description = 'Hukuki sayfa içeriklerinde dil sızıntısı, eski kanun atfı ve eksik/yanlış izleme beyanı arar';

    /** EN ve DE gövdelerinde bulunmaması gereken Türkçe parçalar. */
    private const TURKISH_LEAKS = [
        'dil tercihi',
        'Onay gerektirmez',
        'Session yönetimi',
        'Anonim ziyaretçi',
        'Yok. Üçüncü taraf',
        'kullanmıyoruz',
        'onayınızla',
    ];

    /** Hiçbir dilde bulunmaması gereken, fiilî duruma aykırı beyanlar. */
    private const FALSE_CLAIMS = [
        'We do not use Google Analytics',
        'Wir nutzen kein Google Analytics',
        'No transfers to the USA',
        'Keine Übermittlung in die USA',
        'Self-hosted (Google Analytics yok)',
        'Self-hosted (no Google Analytics)',
        'Self-hosted (kein Google Analytics)',
        '6(1)(f) meşru menfaat (self-hosted',
        '6(1)(f) GDPR (self-hosted',
        '6(1)(f) DSGVO (self-hosted',
    ];

    /**
     * TMG atfını tarihsel açıklama yapan işaretler.
     *
     * Yürürlükten kalkmış bir kanuna ATIF YAPMAK kendiliğinden hata değil; hata,
     * onu YÜRÜRLÜKTEKİ hukuk gibi sunmak. "eski § 8 TMG" ya da "§ 8 TMG a. F."
     * diyen bir cümle doğrudur ve FAIL sayılmaz.
     */
    private const HISTORICAL_MARKERS = [
        // tr
        'eski', 'mülga', 'yürürlükten', 'önceki', 'o dönemde',
        // en
        'former', 'formerly', 'repealed', 'no longer in force', 'previously', 'until 13 may 2024',
        // de
        'aufgehoben', 'a. f.', 'alte fassung', 'früher', 'ehemals', 'vormals', 'bis zum 13.05.2024',
        // ortak
        '13.05.2024', '14.05.2024',
    ];

    /** Bulunması gereken izleme beyanları: key → locale → ihtiyaç listesi. */
    private const REQUIRED = [
        'cookies' => [
            'tr' => ['Google Analytics 4', 'Microsoft Clarity', 'G-D0VB1M1RKF', '_clck', 'GDPR 6(1)(a) açık rıza', 'EU-U.S. Data Privacy Framework'],
            'en' => ['Google Analytics 4', 'Microsoft Clarity', 'G-D0VB1M1RKF', '_clck', 'Art. 6(1)(a) GDPR', 'EU-U.S. Data Privacy Framework'],
            'de' => ['Google Analytics 4', 'Microsoft Clarity', 'G-D0VB1M1RKF', '_clck', 'Art. 6(1)(a) DSGVO', 'EU-U.S. Data Privacy Framework'],
        ],
        'privacy' => [
            'tr' => ['Google Analytics 4', 'Microsoft Clarity', 'GDPR 6(1)(a) açık rıza', 'EU-U.S. Data Privacy Framework'],
            'en' => ['Google Analytics 4', 'Microsoft Clarity', 'Art. 6(1)(a) GDPR', 'EU-U.S. Data Privacy Framework'],
            'de' => ['Google Analytics 4', 'Microsoft Clarity', 'Art. 6(1)(a) DSGVO', 'EU-U.S. Data Privacy Framework'],
        ],
    ];

    public function handle(): int
    {
        $findings = [];
        $checked = 0;

        foreach (LegalPage::orderBy('id')->get() as $page) {
            foreach (LegalPage::LOCALES as $locale) {
                $body = $page->getBody($locale);

                if (trim($body) === '') {
                    // Eksik dil ayrı bir konu (404 davranışı); audit'in derdi metin.
                    $findings[] = [$page->key, $locale, 'eksik gövde', 'o dilde içerik yok'];

                    continue;
                }

                $checked++;
                $haystack = $body . ' ' . (string) $page->getDescription($locale) . ' ' . $page->getTitle($locale);

                if ($locale !== 'tr') {
                    foreach (self::TURKISH_LEAKS as $leak) {
                        if (str_contains($haystack, $leak)) {
                            $findings[] = [$page->key, $locale, 'dil sızıntısı', $leak];
                        }
                    }
                }

                foreach ($this->staleLawCitations($haystack) as $citation) {
                    $findings[] = [$page->key, $locale, 'eski kanun atfı', $citation];
                }

                foreach (self::FALSE_CLAIMS as $claim) {
                    if (str_contains($haystack, $claim)) {
                        $findings[] = [$page->key, $locale, 'yanlış beyan', $claim];
                    }
                }

                foreach (self::REQUIRED[$page->key][$locale] ?? [] as $needle) {
                    if (! str_contains($haystack, $needle)) {
                        $findings[] = [$page->key, $locale, 'eksik beyan', $needle];
                    }
                }
            }
        }

        if ($this->option('json')) {
            $this->line(json_encode([
                'checked'  => $checked,
                'findings' => array_map(
                    fn ($f) => ['key' => $f[0], 'locale' => $f[1], 'issue' => $f[2], 'detail' => $f[3]],
                    $findings
                ),
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            return $findings === [] ? self::SUCCESS : self::FAILURE;
        }

        if ($findings === []) {
            $this->info("PASS — {$checked} sayfa/dil denetlendi, bulgu yok.");

            return self::SUCCESS;
        }

        $this->table(['legal_page', 'locale', 'sorun', 'ayrıntı'], $findings);
        $this->error('FAIL — ' . count($findings) . " bulgu ({$checked} sayfa/dil denetlendi).");

        return self::FAILURE;
    }

    /**
     * Yürürlükteki hukuk gibi sunulan TMG atıflarını döndürür.
     *
     * Atfın çevresindeki pencerede tarihsel bir işaret varsa (bkz.
     * HISTORICAL_MARKERS) o atıf bilinçli bir tarihsel referanstır ve
     * raporlanmaz. Yalnızca normatif sesle sunulanlar bulgu olur.
     *
     * @return array<int, string>
     */
    private function staleLawCitations(string $haystack): array
    {
        // Çapa "TMG" kelimesinin kendisi. Atfı önden yakalamaya çalışmak
        // "§ 7 Abs. 1 TMG" gibi noktalı biçimleri kaçırıyordu — bir denetim
        // aracının atıf kaçırması, hiç denetlememekten daha kötü.
        if (! preg_match_all('~\bTMG\b~u', $haystack, $m, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $citations = [];

        foreach ($m[0] as [, $byteOffset]) {
            // Bayt ofsetini karakter ofsetine çevir; pencere mb ile kesilsin,
            // yoksa Türkçe/Almanca işaretler bozuk sınırda kaybolur.
            $charOffset = mb_strlen(substr($haystack, 0, $byteOffset));
            $window = mb_strtolower(mb_substr($haystack, max(0, $charOffset - 160), 320));

            foreach (self::HISTORICAL_MARKERS as $marker) {
                if (str_contains($window, $marker)) {
                    continue 2;
                }
            }

            $citations[] = $this->citationAt($haystack, $byteOffset);
        }

        return array_values(array_unique($citations));
    }

    /** "TMG" konumundan geriye doğru § işaretini bulup atfı bütün hâlde döndürür. */
    private function citationAt(string $haystack, int $byteOffset): string
    {
        $back = min(45, $byteOffset);
        $pre = substr($haystack, $byteOffset - $back, $back);
        $pos = strrpos($pre, '§');

        if ($pos === false) {
            return 'TMG';
        }

        // "§§" ise ikinciden değil birinciden başla.
        while ($pos >= 2 && substr($pre, $pos - 2, 2) === '§') {
            $pos -= 2;
        }

        return trim(preg_replace('~\s+~u', ' ', substr($pre, $pos) . 'TMG'));
    }
}

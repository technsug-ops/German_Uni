<?php

namespace App\Console\Commands;

use App\Models\LegalPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    /** Önem dereceleri: CRITICAL gerçek hata, REVIEW hukuki karar bekleyen atıf. */
    private const CRITICAL = 'CRITICAL';

    private const REVIEW = 'REVIEW REQUIRED';

    private const GROUP_TRACKING = 'Tracking disclosure';

    private const GROUP_LOCALE = 'Locale isolation';

    private const GROUP_DDG = '§5 DDG';

    private const GROUP_PARITY = 'Data parity';

    private const GROUP_LIABILITY = 'Legacy liability references';

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
                    // § 5 TMG'nin karşılığı doğrulandı (→ § 5 DDG): kalmışsa gerçek hata.
                    // Diğer TMG atıfları (sorumluluk) hukuki karar bekliyor → inceleme.
                    $group = preg_match('~^§\s*5\s+TMG$~u', $citation) ? self::GROUP_DDG : self::GROUP_LIABILITY;
                    $findings[] = [$page->key, $locale, 'eski kanun atfı', $citation, $group];
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

                if ($page->key === 'impressum' && ! preg_match('~§\s*5\s+DDG~u', $haystack)) {
                    $findings[] = [$page->key, $locale, 'eksik beyan', '§ 5 DDG', self::GROUP_DDG];
                }
            }
        }

        foreach ($this->parityFindings() as $f) {
            $findings[] = $f;
        }

        $findings = array_map(fn ($f) => $this->classify($f), $findings);
        $summary = $this->summary($findings);

        if ($this->option('json')) {
            $this->line(json_encode([
                'checked'  => $checked,
                'summary'  => $summary,
                'findings' => array_map(
                    fn ($f) => ['key' => $f[0], 'locale' => $f[1], 'issue' => $f[2], 'detail' => $f[3], 'group' => $f[4], 'severity' => $f[5]],
                    $findings
                ),
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            // Çıkış kodu bilinçli olarak DEĞİŞMEDİ: herhangi bir bulgu (inceleme dahil) → 1.
            return $findings === [] ? self::SUCCESS : self::FAILURE;
        }

        foreach ($summary as $label => $status) {
            $line = str_pad($label . ':', 32) . $status;
            match (true) {
                $status === 'PASS'                     => $this->info($line),
                str_starts_with($status, self::REVIEW) => $this->warn($line),
                default                                => $this->error($line),
            };
        }
        $this->newLine();

        if ($findings === []) {
            $this->info("PASS — {$checked} sayfa/dil denetlendi, bulgu yok.");

            return self::SUCCESS;
        }

        $critical = array_values(array_filter($findings, fn ($f) => $f[5] === self::CRITICAL));
        $review = array_values(array_filter($findings, fn ($f) => $f[5] === self::REVIEW));

        if ($critical !== []) {
            $this->error('CRITICAL FAIL (' . count($critical) . ')');
            $this->table(['legal_page', 'locale', 'sorun', 'ayrıntı'], array_map(fn ($f) => array_slice($f, 0, 4), $critical));
        }

        if ($review !== []) {
            $this->warn('REVIEW REQUIRED — LEGAL_REFERENCE_REVIEW_REQUIRED (' . count($review) . ')');
            $this->table(['legal_page', 'locale', 'sorun', 'ayrıntı'], array_map(fn ($f) => array_slice($f, 0, 4), $review));
        }

        $this->error('FAIL — ' . count($critical) . ' kritik, ' . count($review) . " inceleme bekleyen bulgu ({$checked} sayfa/dil denetlendi).");

        return self::FAILURE;
    }

    /**
     * Bulguya grup + önem derecesi ekler: [key, locale, issue, detail, group, severity].
     *
     * @param  array<int, string>  $f
     * @return array<int, string>
     */
    private function classify(array $f): array
    {
        $group = $f[4] ?? match ($f[2]) {
            'dil sızıntısı', 'eksik gövde' => self::GROUP_LOCALE,
            'parity'                       => self::GROUP_PARITY,
            default                        => self::GROUP_TRACKING, // yanlış / eksik beyan
        };

        return [$f[0], $f[1], $f[2], $f[3], $group, $group === self::GROUP_LIABILITY ? self::REVIEW : self::CRITICAL];
    }

    /** @return array<string, string> grup → PASS / FAIL / REVIEW REQUIRED */
    private function summary(array $findings): array
    {
        $out = [];

        foreach ([self::GROUP_TRACKING, self::GROUP_LOCALE, self::GROUP_DDG, self::GROUP_PARITY, self::GROUP_LIABILITY] as $group) {
            $hit = array_filter($findings, fn ($f) => $f[4] === $group);
            $out[$group] = $hit === [] ? 'PASS' : ($group === self::GROUP_LIABILITY ? self::REVIEW : 'FAIL') . ' (' . count($hit) . ')';
        }

        return $out;
    }

    /**
     * legal:parity ile aynı kural: legacy JSON gövdesi ↔ çeviri satırı birebir.
     *
     * @return array<int, array<int, string>>
     */
    private function parityFindings(): array
    {
        if (! Schema::hasTable('legal_page_translations')) {
            return [['*', '*', 'parity', 'legal_page_translations tablosu yok']];
        }

        $findings = [];

        foreach (DB::table('legal_pages')->orderBy('id')->get(['id', 'key', 'bodies']) as $page) {
            $bodies = json_decode($page->bodies ?? '', true);
            $bodies = is_array($bodies) ? array_filter($bodies, fn ($b) => is_string($b) && trim($b) !== '') : [];

            $rows = DB::table('legal_page_translations')
                ->where('legal_page_id', $page->id)->pluck('body', 'locale')->all();

            foreach (array_unique(array_merge(array_keys($bodies), array_keys($rows))) as $locale) {
                if (($bodies[$locale] ?? null) !== ($rows[$locale] ?? null)) {
                    $findings[] = [$page->key, $locale, 'parity', 'legacy JSON ile çeviri satırı farklı'];
                }
            }
        }

        return $findings;
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

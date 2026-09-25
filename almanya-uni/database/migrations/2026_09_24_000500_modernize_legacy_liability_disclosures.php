<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Künye + Yasal Uyarı: eski TMG sorumluluk kalıbını güncel, sade metinle değiştirir.
 *
 * NE DEĞİŞİYOR (yalnızca bu iki blok, 3 dilde)
 *   impressum  "İçerik Sorumluluğu" paragrafı: § 7 Abs. 1 TMG + §§ 8-10 TMG
 *   disclaimer "Harici Linkler" başlığı (§§ 7-10 TMG) + paragrafı
 *
 * NEDEN MEKANİK DÖNÜŞÜM YOK
 *   - § 7 Abs. 1 TMG kural koymuyordu, genel hukuku hatırlatıyordu; DSA'nın
 *     muafiyetleri kendi içeriğe uygulanmaz. Karşılığı yok → atıfsız cümle.
 *   - §§ 8-10 TMG'nin yerini DSA m. 4-6 (+ izleme yasağı m. 8) aldı. Bu site
 *     yalnızca hosting (m. 6) yapıyor: üniversite/etkinlik yorumları. §§ 8-10 DDG
 *     YANLIŞ olurdu (DDG § 8 yalnızca fikrî mülkiyette erişim engelleme talebi).
 *   - Link sorumluluğu TMG muafiyetlerine hiç girmiyordu (BGH I ZR 74/14);
 *     başlıktaki §§ 7-10 TMG baştan hatalıydı → atıf kaldırılır, DSA/DDG de eklenmez.
 *   - "Bağlantıyı eklerken kontrol ettik" iddiası doğrulanmış bir editoryal süreç
 *     olmadığı için yeni metinde YOK.
 *
 * KURALLAR (000400 ile aynı)
 *   - Ham Markdown; beklenen eski metin (yalnız boşluk toleransıyla) bulunmazsa ve
 *     yeni metin de yoksa exception — hiçbir şey yazılmaz.
 *   - Idempotent; TR/EN/DE ayrı ayrı; başka dile yazılmaz.
 *   - Çeviri satırı + aynı locale legacy JSON aynı sonuçla (parity).
 *   - Yazımdan önce son kontrol: gövdede TMG kalmamalı, m. 6 ve m. 8 bulunmalı,
 *     § 5 DDG yerinde olmalı; harici link bölümünde TMG/DDG/DSA olmamalı.
 */
return new class extends Migration
{
    private const LOCALES = ['tr', 'en', 'de'];

    public function up(): void
    {
        if (! Schema::hasTable('legal_pages')) {
            Log::warning('legal liability fix: legal_pages tablosu yok, atlandı');

            return;
        }

        $hasTranslations = Schema::hasTable('legal_page_translations');
        $pages = DB::table('legal_pages')->whereIn('key', ['impressum', 'disclaimer'])->get()->keyBy('key');

        if ($pages->isEmpty()) {
            Log::info('legal liability fix: impressum/disclaimer kaydı yok, atlandı');

            return;
        }

        $report = [];
        $errors = [];
        $writes = [];

        foreach (['impressum', 'disclaimer'] as $key) {
            $page = $pages->get($key);

            if (! $page) {
                $report[] = "{$key}: KAYIT YOK";
                Log::warning("legal liability fix: '{$key}' sayfası yok");

                continue;
            }

            $bodies = $this->decode($page->bodies);
            $rows = $hasTranslations
                ? DB::table('legal_page_translations')->where('legal_page_id', $page->id)->get()->keyBy('locale')
                : collect();

            $pageWrite = ['bodies' => $bodies, 'rows' => [], 'changed' => false];

            foreach (self::LOCALES as $locale) {
                $row = $rows->get($locale);
                $body = $row->body ?? ($bodies[$locale] ?? null);
                $tag = "{$key}/{$locale}";

                if (! is_string($body) || trim($body) === '') {
                    $report[] = "{$tag}: içerik yok, atlandı";

                    continue;
                }

                $op = $this->op($key, $locale);
                $eol = str_contains($body, "\r\n") ? "\r\n" : "\n";

                if (preg_match($op['done'], $body)) {
                    $newBody = $body;
                    $state = 'zaten güncel';
                } elseif (preg_match($op['find'], $body, $m, PREG_OFFSET_CAPTURE)) {
                    [$match, $offset] = $m[0];
                    $newBody = substr_replace($body, str_replace("\n", $eol, $op['replace']), $offset, strlen($match));
                    $state = 'güncellendi';
                } else {
                    $errors[] = "{$tag}: beklenen eski metin birebir bulunamadı ve yeni metin de yok";

                    continue;
                }

                foreach ($this->postConditionErrors($key, $locale, $newBody) as $e) {
                    $errors[] = "{$tag}: {$e}";
                }

                $drift = $row && isset($bodies[$locale]) && $bodies[$locale] !== $row->body;

                if ($newBody !== $body || $drift) {
                    $pageWrite['bodies'][$locale] = $newBody;
                    if ($row) {
                        $pageWrite['rows'][$locale] = $newBody;
                    }
                    $pageWrite['changed'] = true;
                }

                $report[] = "{$tag}: {$state}"
                    . ($drift ? ' — UYARI: legacy JSON çeviri satırından farklıydı, eşitlendi' : '')
                    . ($row ? '' : ' — çeviri satırı yok, yalnız legacy JSON');
            }

            $writes[$page->id] = $pageWrite;
        }

        if ($errors !== []) {
            throw new RuntimeException("legal liability fix: gövde beklenen biçimde değil, HİÇBİR ŞEY YAZILMADI:\n  "
                . implode("\n  ", $errors));
        }

        DB::transaction(function () use ($writes) {
            foreach ($writes as $pageId => $w) {
                if (! $w['changed']) {
                    continue;
                }

                DB::table('legal_pages')->where('id', $pageId)->update([
                    'bodies'         => json_encode($w['bodies'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'effective_date' => now()->toDateString(),
                    'updated_at'     => now(),
                ]);

                foreach ($w['rows'] as $locale => $body) {
                    DB::table('legal_page_translations')
                        ->where('legal_page_id', $pageId)
                        ->where('locale', $locale)
                        ->update(['body' => $body, 'updated_at' => now()]);
                }
            }
        });

        echo "legal liability fix:\n  " . implode("\n  ", $report) . "\n";
    }

    public function down(): void
    {
        // Bilinçli olarak boş: eski metin yürürlükten kalkmış TMG'yi güncel hukuk
        // gibi sunuyordu; geri yazmak yanlış hukuki dayanak göstermek olur.
    }

    /** Metni birebir, yalnızca boşluk farkına toleranslı kalıba çevirir (satır sonu dahil). */
    private function literal(string $text): string
    {
        $parts = preg_split('~\s+~u', trim($text));

        return implode('\s+', array_map(fn ($p) => preg_quote($p, '~'), $parts));
    }

    /** @return array{find: string, done: string, replace: string} */
    private function op(string $key, string $locale): array
    {
        $t = $this->texts($key, $locale);

        return [
            'find'    => '~^\h*' . $this->literal($t['old']) . '\h*(?=\r?$)~mu',
            'done'    => '~^\h*' . $this->literal($t['new']) . '\h*(?=\r?$)~mu',
            'replace' => $t['new'],
        ];
    }

    /** @return array<int, string> */
    private function postConditionErrors(string $key, string $locale, string $body): array
    {
        $errors = [];

        if (preg_match('~\bTMG\b~u', $body)) {
            $errors[] = 'TMG atfı kaldı';
        }

        if ($key === 'impressum') {
            $need = [
                'tr' => ['DSA m. 6', 'DSA m. 8', '§ 5 DDG'],
                'en' => ['Article 6 DSA', 'Article 8 DSA', '§ 5 DDG'],
                'de' => ['Art. 6 DSA', 'Art. 8 DSA', '§ 5 DDG'],
            ][$locale];

            foreach ($need as $needle) {
                if (! str_contains($body, $needle)) {
                    $errors[] = "zorunlu ifade eksik: '{$needle}'";
                }
            }
        }

        if ($key === 'disclaimer') {
            $heading = ['tr' => 'Harici Linkler', 'en' => 'External Links', 'de' => 'Externe Links'][$locale];

            if (preg_match('~^##\h+' . preg_quote($heading, '~') . '\h*\r?$(.*?)(?=^##\h|\z)~msu', $body, $m)) {
                if (preg_match('~\b(TMG|DDG|DSA)\b~u', $m[0])) {
                    $errors[] = 'harici link bölümünde kanun atfı kaldı';
                }
            } else {
                $errors[] = "'{$heading}' bölümü bulunamadı";
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

    /** @return array{old: string, new: string} */
    private function texts(string $key, string $locale): array
    {
        $impressum = [
            'tr' => [
                'old' => 'Hizmet sağlayıcı olarak, § 7 Abs. 1 TMG uyarınca bu sayfalardaki kendi içeriğimizden genel yasalara göre sorumluyuz. §§ 8-10 TMG uyarınca, iletilen veya saklanan üçüncü taraf bilgilerini izleme veya yasadışı faaliyete işaret eden koşulları araştırma yükümlülüğümüz yoktur.',
                'new' => <<<'MD'
Bu sayfalardaki kendi içeriğimizden genel hükümler uyarınca sorumluyuz.

Üçüncü kişilere ait içerikleri, örneğin üniversite veya etkinlik yorumlarını sakladığımız durumlarda DSA m. 6 hükümleri uygulanır. Bu içerikleri genel olarak izleme veya hukuka aykırılıkları aktif biçimde araştırma yükümlülüğümüz bulunmaz (DSA m. 8). Hukuka aykırı bir içerikten haberdar olduğumuzda bildirimi değerlendirir ve gerekli olduğu ölçüde ilgili içeriği gecikmeksizin kaldırır veya erişimi engelleriz.
MD,
            ],
            'en' => [
                'old' => 'As a service provider, we are responsible for our own content on these pages pursuant to § 7 (1) TMG. Pursuant to §§ 8-10 TMG, we are not obliged to monitor transmitted or stored third-party information or investigate circumstances indicating illegal activity.',
                'new' => <<<'MD'
We are responsible for our own content on these pages under the general laws.

Where we store third-party content, such as university or event reviews, the conditions of Article 6 DSA apply. There is no general obligation to monitor such content or actively seek illegal activity (Article 8 DSA). If we become aware of illegal content, we review the notice and remove or disable access to the content without undue delay where required.
MD,
            ],
            'de' => [
                'old' => 'Als Diensteanbieter sind wir gemäß § 7 Abs. 1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8-10 TMG sind wir jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen.',
                'new' => <<<'MD'
Für eigene Inhalte auf diesen Seiten sind wir nach den allgemeinen Gesetzen verantwortlich.

Soweit wir Inhalte Dritter speichern (z. B. Hochschul- oder Veranstaltungsbewertungen), gelten die Voraussetzungen des Art. 6 DSA. Eine allgemeine Pflicht, diese Inhalte zu überwachen oder aktiv nach rechtswidrigen Tätigkeiten zu forschen, besteht nicht (Art. 8 DSA). Erhalten wir Kenntnis von rechtswidrigen Inhalten, prüfen wir den Hinweis und entfernen oder sperren die betreffenden Inhalte unverzüglich, soweit dies erforderlich ist.
MD,
            ],
        ];

        // Başlık + paragraf tek blok: yeni başlık eski başlığın ön eki olduğundan
        // idempotency ancak bloğun TAMAMIYLA güvenilir biçimde anlaşılır.
        $disclaimer = [
            'tr' => [
                'old' => "## Harici Linkler (§§ 7-10 TMG)\n\nSitemizde üçüncü taraf sitelere bağlantılar bulunur. Bu sitelerin içeriğinden **biz sorumlu değiliz**. Link verdiğimiz anda içerikleri kontrol etmiş olmamıza rağmen, ilgili sitelerin sonradan yapacağı değişikliklerden sorumlu tutulamayız. Yasalara aykırı içerik fark ettiğinizde **lütfen bize bildirin**, kaldıracağız.",
                'new' => "## Harici Linkler\n\nHarici olarak bağlantı verilen sayfaların içeriğinden ilgili sayfanın sağlayıcısı sorumludur. Bağlantı verilen bir sayfada hukuka aykırı içerikten haberdar olmamız halinde ilgili bağlantıyı gecikmeksizin kaldırırız.",
            ],
            'en' => [
                'old' => "## External Links (§§ 7-10 TMG)\n\nOur site contains links to third-party websites. We are **not responsible** for their content. We check content at the time of linking, but cannot be held liable for subsequent changes by the linked site. **Please notify us** if you spot illegal content and we will remove the link.",
                'new' => "## External Links\n\nThe respective provider is responsible for the content of externally linked pages. If we become aware of illegal content on a linked page, we will remove the relevant link without undue delay.",
            ],
            'de' => [
                'old' => "## Externe Links (§§ 7-10 TMG)\n\nUnsere Seite enthält Links zu Websites Dritter. Für deren Inhalte sind wir **nicht verantwortlich**. Zum Zeitpunkt der Verlinkung haben wir Inhalte geprüft, können aber für nachträgliche Änderungen nicht haften. **Bitte teilen Sie uns mit**, wenn Sie rechtswidrige Inhalte bemerken — wir entfernen den Link.",
                'new' => "## Externe Links\n\nFür die Inhalte externer verlinkter Seiten ist der jeweilige Anbieter verantwortlich. Erlangen wir Kenntnis von rechtswidrigen Inhalten auf einer verlinkten Seite, entfernen wir den betreffenden Link unverzüglich.",
            ],
        ];

        return ($key === 'impressum' ? $impressum : $disclaimer)[$locale];
    }
};

<?php

namespace App\Services\Content;

use App\Models\City;
use App\Models\University;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Cache;

/**
 * İçeriği otomatik iç-linkler + glossary tooltip'leri ekler.
 *
 * İki tip işaretleme (her terim İLK geçişinde, bir kez):
 *   1. Glossary terimleri → <span class="glossary-term" data-tip="...">  (hover tooltip)
 *   2. DB entity'leri (şehir, üniversite) → <a href="/...">  (iç sayfa linki)
 *
 * HTML-safe: sadece metin düğümlerini işler; <a>, başlık (h1-h6), <code>, <pre>
 * içine GİRMEZ (mevcut link/başlık/kod bozulmaz). Post kaydı sırasında otomatik çalışır.
 */
class BlogAutoLinker
{
    /** İşlenmeyecek üst etiketler (zaten link / başlık / kod / infografik). */
    private const SKIP_ANCESTORS = ['a', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'code', 'pre', 'figcaption'];

    /** Aşırılık önleme — sayfa başına doygunluk sınırı (link tarlası YOK). */
    private const MAX_LINKS = 3;      // entity (şehir/üni) linki
    private const MAX_GLOSSARY = 5;   // glossary tooltip

    /** Yaygın Almanca/genel kelimelerle çakışan şehir adları — auto-link YANLIŞ olur
     *  (leer=boş, essen=yemek, hof=avlu, lage=durum, born=kuyu, horn=boynuz). Bu
     *  şehirlere otomatik link verilmez; case-insensitive eşleşme yanlış-link üretiyordu. */
    private const CITY_STOPLIST = ['leer', 'essen', 'hof', 'lage', 'born', 'horn', 'rust', 'brake'];

    /** Sayfa-genel sayaç (çok bloklu içerikte tüm bloklar paylaşır). */
    private int $gCount = 0;
    private int $lCount = 0;

    /**
     * @param string $html İşlenecek HTML
     * @param string|null $excludeUrl Bu URL'e link verme (self-link önleme — entity kendi sayfasındaysa)
     * @param bool $resetCounters Sayfa sayaçlarını sıfırla. Çok bloklu içerikte SADECE ilk blokta true ver
     *                            (tüm bloklar tek sayfa limitini paylaşsın).
     */
    public function process(string $html, ?string $excludeUrl = null, bool $resetCounters = true, ?string $locale = null): string
    {
        // Glossary tanımları Türkçe → SADECE TR içeriğe uygula (DE/EN sayfalarına sızmasın).
        $locale = $locale ?? app()->getLocale();
        if ($resetCounters) {
            $this->gCount = 0;
            $this->lCount = 0;
        }
        if (trim($html) === '') {
            return $html;
        }
        // Sayfa limiti zaten dolduysa hiç işleme
        if ($this->gCount >= self::MAX_GLOSSARY && $this->lCount >= self::MAX_LINKS) {
            return $html;
        }

        // Terim haritası: term(lower) => ['type'=>'glossary'|'link', 'tip'=>..., 'url'=>...]
        $terms = $this->buildTermMap($locale);
        if (empty($terms)) {
            return $html;
        }

        // Self-link önle: entity kendi sayfasındaysa kendine link vermesin
        if ($excludeUrl !== null) {
            $terms = array_filter($terms, fn ($m) => ($m['url'] ?? null) !== $excludeUrl);
        }

        // Uzun terimler önce (örn "uni-assist" < "uni" çakışmasını önler)
        uksort($terms, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));

        $used = [];                                  // her terim bir kez (bu çağrıda)

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        // UTF-8 koru; wrapper ile parça HTML yükle
        $dom->loadHTML(
            '<?xml encoding="UTF-8"><div id="__root__">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // Idempotent: önceki autolink işaretlerini temizle (birikmeyi önle).
        // Böylece her çalıştırmada limit sıfırdan + doğru uygulanır.
        foreach (iterator_to_array($xpath->query('//span[contains(@class,"glossary-term")] | //a[contains(@class,"auto-link")]')) as $old) {
            $old->parentNode->replaceChild($dom->createTextNode($old->textContent), $old);
        }
        $dom->getElementById('__root__')->normalize(); // bitişik text node'ları birleştir

        $textNodes = $xpath->query('//text()');

        foreach ($textNodes as $node) {
            // Her iki limit de dolduysa daha fazla işleme — link tarlası önleme
            if ($this->gCount >= self::MAX_GLOSSARY && $this->lCount >= self::MAX_LINKS) {
                break;
            }
            if ($this->hasSkipAncestor($node)) {
                continue;
            }
            $text = $node->nodeValue;
            if (trim($text) === '' || mb_strlen($text) < 3) {
                continue;
            }

            $replacement = $this->linkifyText($text, $terms, $used, $dom);
            if ($replacement !== null) {
                $node->parentNode->replaceChild($replacement, $node);
            }
        }

        // Sadece wrapper içeriğini geri ver
        $root = $dom->getElementById('__root__');
        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }
        return $out;
    }

    private function hasSkipAncestor(\DOMNode $node): bool
    {
        $p = $node->parentNode;
        while ($p && $p->nodeType === XML_ELEMENT_NODE) {
            if (in_array(strtolower($p->nodeName), self::SKIP_ANCESTORS, true)) {
                return true;
            }
            // Zaten işaretlenmiş terim/link içine girme (idempotent — çift-işleme yok)
            $cls = (string) $p->getAttribute('class');
            if (str_contains($cls, 'glossary-term') || str_contains($cls, 'auto-link')) {
                return true;
            }
            // infografik div'leri (inline style'lı) — dokunma
            if ($p->nodeName === 'div' && str_contains((string) $p->getAttribute('style'), 'border-radius')) {
                return true;
            }
            $p = $p->parentNode;
        }
        return false;
    }

    /**
     * Metinde ilk eşleşen (henüz kullanılmamış) terimi bulur, bir DocumentFragment döndürür.
     * Eşleşme yoksa null.
     */
    private function linkifyText(string $text, array $terms, array &$used, DOMDocument $dom): ?\DOMDocumentFragment
    {
        // Metindeki EN SOLDAKİ eşleşmeyi bul (çakışmada uzun terim öncelikli).
        $best = null;
        foreach ($terms as $term => $meta) {
            if (isset($used[$term])) {
                continue;
            }
            // Doygunluk: o tipin sayfa limiti dolduysa atla (aşırılık önleme)
            if ($meta['type'] === 'glossary' && $this->gCount >= self::MAX_GLOSSARY) {
                continue;
            }
            if ($meta['type'] === 'link' && $this->lCount >= self::MAX_LINKS) {
                continue;
            }
            $pattern = '/(?<![\p{L}\p{N}])(' . preg_quote($term, '/') . ')(?![\p{L}\p{N}])/iu';
            if (! preg_match($pattern, $text, $m, PREG_OFFSET_CAPTURE)) {
                continue;
            }
            $offset = $m[1][1];
            if ($best === null
                || $offset < $best['offset']
                || ($offset === $best['offset'] && mb_strlen($term) > mb_strlen($best['term']))) {
                $best = ['term' => $term, 'meta' => $meta, 'offset' => $offset, 'matched' => $m[1][0]];
            }
        }

        if ($best === null) {
            return null;
        }

        // ÖNEMLİ: recursion'dan ÖNCE işaretle (terim ikinci kez linklenmesin) + sayfa sayacını artır.
        $used[$best['term']] = true;
        if ($best['meta']['type'] === 'glossary') {
            $this->gCount++;
        } else {
            $this->lCount++;
        }

        $matched = $best['matched'];
        $before = substr($text, 0, $best['offset']);
        $after = substr($text, $best['offset'] + strlen($matched));

        $frag = $dom->createDocumentFragment();
        if ($before !== '') {
            $frag->appendChild($dom->createTextNode($before));
        }

        if ($best['meta']['type'] === 'glossary') {
            $span = $dom->createElement('span', htmlspecialchars($matched, ENT_QUOTES));
            $span->setAttribute('class', 'glossary-term');
            $span->setAttribute('data-tip', $best['meta']['tip']);
            $frag->appendChild($span);
        } else {
            $a = $dom->createElement('a', htmlspecialchars($matched, ENT_QUOTES));
            $a->setAttribute('href', $best['meta']['url']);
            $a->setAttribute('class', 'auto-link');
            $frag->appendChild($a);
        }

        if ($after !== '') {
            $rest = $this->linkifyText($after, $terms, $used, $dom);
            $frag->appendChild($rest ?? $dom->createTextNode($after));
        }

        return $frag;
    }

    /** Glossary + entity terimlerini tek haritada birleştir (cache'li). */
    private function buildTermMap(string $locale = 'tr'): array
    {
        return Cache::remember("blog_autolinker_terms_v2_{$locale}", 3600, function () use ($locale) {
            $map = [];

            // 1) Glossary (TÜRKÇE tanımlar) — SADECE TR içeriğe. DE/EN'e Türkçe tooltip sızmasın.
            if ($locale === 'tr') {
                foreach ($this->glossary() as $term => $tip) {
                    $map[mb_strtolower($term)] = ['type' => 'glossary', 'tip' => $tip];
                }
            }

            // 2) Şehirler (link) — yaygın/tek-harfli risk yok, name_de spesifik
            foreach (City::where('is_active', 1)->get(['name_de', 'slug']) as $c) {
                $key = mb_strtolower($c->name_de);
                if (mb_strlen($key) < 4) continue; // çok kısa şehir adı atla
                if (in_array($key, self::CITY_STOPLIST, true)) continue; // genel kelime çakışması (leer=boş)
                if (! isset($map[$key])) {
                    $map[$key] = ['type' => 'link', 'url' => '/cities/' . $c->slug];
                }
            }

            // 3) Üniversite kısa adları (RWTH, TUM, LMU, KIT…) + tam ad
            foreach (University::where('is_active', 1)->get(['name_de', 'short_name', 'slug']) as $u) {
                if ($u->short_name && mb_strlen($u->short_name) >= 3) {
                    $key = mb_strtolower($u->short_name);
                    if (! isset($map[$key])) {
                        $map[$key] = ['type' => 'link', 'url' => '/universities/' . $u->slug];
                    }
                }
            }

            return $map;
        });
    }

    /**
     * Almanya öğrenci terimleri sözlüğü — hover tooltip açıklamaları.
     * Tanımlar faktüel; spesifik rakamlar güncel (2025) ve "yaklaşık" olarak verilir.
     */
    private function glossary(): array
    {
        return [
            'Sperrkonto'        => 'Bloke hesap — Almanya öğrenci vizesi için ~11.904 €/yıl (2025) bloke edilen banka hesabı. Aylık ~992 € çekilebilir.',
            'Bloke hesap'       => 'Sperrkonto — vize için bloke edilen, aylık limitli para çekilen banka hesabı.',
            'APS'               => 'Akademische Prüfstelle — Türkiye\'den başvuranların akademik belgelerinin denkliğini kontrol eden kurum. Çoğu başvuru için zorunlu.',
            'VPD'               => 'Vorprüfungsdokumentation — uni-assist\'in not ortalamanı Alman sistemine çevirdiği ön-değerlendirme belgesi.',
            'Anmeldung'         => 'İkamet kaydı — yeni şehre taşınınca Bürgeramt\'ta yapılan resmi adres bildirimi (genelde 14 gün içinde).',
            'Uni-Assist'        => 'Birçok Alman üniversitesinin uluslararası başvurularını toplayan merkezi servis (uni-assist.de).',
            'Studienkolleg'     => 'Lise diploması denkliği yetmeyen öğrenciler için ~1 yıllık üniversite hazırlık programı.',
            'Semesterticket'    => 'Dönem boyunca bölgesel toplu taşımayı kapsayan, harç içinde gelen öğrenci ulaşım bileti.',
            'Numerus Clausus'   => 'NC — kontenjanı sınırlı bölümlere giriş için gereken minimum not barajı.',
            'Krankenversicherung' => 'Sağlık sigortası — Almanya\'da öğrenciler için yasal zorunlu (kayıt şartı).',
            'Studierendenwerk'  => 'Devlet öğrenci hizmetleri kurumu — yurt, yemekhane (Mensa) ve sosyal destek sağlar.',
            'Ausbildung'        => 'İkili mesleki eğitim — işbaşı + meslek okulu, eğitim boyunca maaş ödenir (genelde B2 Almanca şart).',
            'Zulassung'         => 'Kabul belgesi — üniversiteden alınan kesin kabul (Zulassungsbescheid).',
            'Immatrikulation'   => 'Üniversiteye kesin kayıt işlemi.',
            'Bürgeramt'         => 'Vatandaşlık dairesi — Anmeldung ve diğer ikamet işlemlerinin yapıldığı belediye birimi.',
            'Aufenthaltstitel'  => 'Oturma izni — Almanya\'da uzun süreli kalış için ikamet kartı.',
            'TestDaF'           => 'Yabancılar için standart Almanca dil yeterlilik sınavı (üniversite kabul için yaygın).',
            'DSH'               => 'Deutsche Sprachprüfung — üniversitelerin kendi düzenlediği Almanca dil yeterlilik sınavı.',
            'Fachhochschule'    => 'FH / HAW — uygulamalı bilimler üniversitesi; pratik ve sektör odaklı, staj ağırlıklı.',
            'Wartesemester'     => 'Bekleme dönemi — NC bölümlerde kontenjan beklerken geçen yarıyıllar.',
            'Mensa'             => 'Üniversite yemekhanesi — öğrencilere uygun fiyatlı yemek sunar (Studierendenwerk işletir).',
            'Exmatrikulation'   => 'Üniversiteden kayıt silme işlemi (mezuniyet veya ayrılma).',
            'Blocked account'   => 'Sperrkonto\'nun İngilizcesi — vize için bloke edilen banka hesabı.',
            'WG-Zimmer'         => 'Paylaşımlı dairede (Wohngemeinschaft) kiralanan tek oda — en yaygın öğrenci konaklaması.',
        ];
    }
}

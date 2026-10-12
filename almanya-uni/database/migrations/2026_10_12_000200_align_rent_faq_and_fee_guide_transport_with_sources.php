<?php

use App\Models\Faq;
use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Batch 4 sonrası iki kaynak grubunun kaynak uyumu (dar kapsam):
 *  1) FAQ "Berlin, Münih, Hamburg ortalama kira ne kadar?" (TR + EN/DE kardeşleri, aynı çeviri grubu): 2026 başı için
 *     "ortalama" diye sunulan kaynaksız yurt/WG/stüdyo bantları, Batch 4'ün doğrulanmış verileriyle değiştirilir
 *     (Studierendenwerk liste aralıkları dönemiyle; MLP Studentenwohnreport 2025 / Value AG WG medyanı ve 30 m² örnek daire).
 *  2) Rehber "Almanya'da üniversite ücretsiz mi?" (TR + DE; EN kardeşi kısa özet sürümüdür ve bu blokları içermez):
 *     yalnız "150 € Semesterticket / aylık 60 € toplu taşıma" paragrafı ve aylık gider tablosundaki ulaşım satırı + toplam.
 * Ön doğrulama: her hedefte eski metin tam bir kez. Zaten yeni metin varsa no-op. Tek transaction, rerun no-op.
 * Slug, çeviri grubu, yazar ve ilk yayın tarihi değişmez. Eksik kardeş yeni kayıt olarak oluşturulmaz.
 */
return new class extends Migration
{
    private const FAQ_TR_SLUG = 'berlin-munih-hamburg-ortalama-kira-ne-kadar';
    private const GUIDE_TR_SLUG = 'is-university-free-in-germany-2026-real-costs';

    /** Eski FAQ cevabının parmak izleri (üç dilde aynı rakamlar). */
    private const FAQ_OLD = ['240-340', '480-650', '290-380', '650-900', '260-360', '520-720'];

    private const FAQ_NEW = [
        'tr' => <<<'MD'
Tek bir "ortalama kira" yok: yurt odası, WG odası ve daire için fiyat türü ve dönem farklı. Aşağıdaki değerler kaynaklı; karşılaştırırken aynı türü ve aynı dönemi yan yana koy.

| Şehir | Studierendenwerk yurdu (resmî liste) | WG odası 20 m², ilan medyanı, sıcak kira (MLP 2025) | Örnek daire 30 m², sıcak kira (MLP 2025) |
|---|---|---|---|
| Berlin | 265–680 € (1 Kasım 2022 kiraları; Berlin Senatosu'nun Ocak 2023 cevabı) | 624 € | 664 € |
| Münih | 283–577 € tek kişilik WG odası (Eylül 2026 listesi) | 790 € | 837 € |
| Hamburg | 310 €'dan başlayan mobilyalı oda; yayımlanmış üst sınır yok (Ekim 2026) | 678 € | 626 € |

Nasıl okunur:

- Yurt sütunu ortalama değil, Studierendenwerk'in kendi listesindeki aralıktır; Berlin rakamı Kasım 2022'ye ait, güncel listeyi stw.berlin'de kontrol et. Yurt yeri garanti değildir, bekleme süresi şehre göre değişir.
- WG ve daire sütunları MLP Studentenwohnreport 2025 / Value AG ilan verisidir ve **Warmmiete**'dir (ısıtma ve yan giderler dahil). **Kaltmiete** yalnız odanın bedelidir; elektrik ve internet ayrıca eklenir.
- Depozito (Kaution) özel piyasada en fazla üç aylık Kaltmiete; yurtlarda Studierendenwerk sözleşmesinde yazar.

Ayrıntı ve kaynaklar: [Berlin](/tr/cities/berlin-q64), [Münih](/tr/cities/munchen-q1726), [Hamburg](/tr/cities/hamburg-q1055) şehir sayfaları; 44 şehir için [öğrenci kira haritası](/tr/student-rent-map); [yaşam maliyeti ve konaklama bütçesi rehberi](/tr/blog/real-cost-of-being-a-student-in-germany-budget-truth).
MD,
        'en' => <<<'MD'
There is no single "average rent": a dorm room, a shared-flat (WG) room and a flat have different price types and periods. The figures below are sourced; compare like with like, same type and same period.

| City | Studierendenwerk dorm (official list) | WG room 20 m², listing median, warm rent (MLP 2025) | Model flat 30 m², warm rent (MLP 2025) |
|---|---|---|---|
| Berlin | €265–680 (rents as of 1 November 2022; Berlin Senate's January 2023 answer) | €624 | €664 |
| Munich | €283–577 for a single WG room (September 2026 list) | €790 | €837 |
| Hamburg | furnished room from €310; no published upper limit (October 2026) | €678 | €626 |

How to read it:

- The dorm column is not an average but the range in the Studierendenwerk's own list; the Berlin figure dates from November 2022, check the current list on stw.berlin. A dorm place is not guaranteed and waiting times differ by city.
- The WG and flat columns are MLP Studentenwohnreport 2025 / Value AG listing data and are **Warmmiete** (heating and utilities included). **Kaltmiete** is the room alone; electricity and internet come on top.
- The deposit (Kaution) is at most three months' Kaltmiete on the private market; in dorms it is set in the Studierendenwerk contract.

Details and sources: the city pages for [Berlin](/en/cities/berlin-q64), [Munich](/en/cities/munchen-q1726) and [Hamburg](/en/cities/hamburg-q1055); the [student rent map](/en/student-rent-map) for 44 cities; the [cost of living and housing budget guide](/en/blog/real-cost-student-germany-budget-reality-check).
MD,
        'de' => <<<'MD'
Es gibt keine einzelne "Durchschnittsmiete": Wohnheimzimmer, WG-Zimmer und Wohnung haben unterschiedliche Preisarten und Zeiträume. Die Werte unten sind belegt; vergleiche nur gleiche Art und gleichen Zeitraum.

| Stadt | Studierendenwerk-Wohnheim (offizielle Liste) | WG-Zimmer 20 m², Angebotsmedian, Warmmiete (MLP 2025) | Musterwohnung 30 m², Warmmiete (MLP 2025) |
|---|---|---|---|
| Berlin | 265–680 € (Mieten zum 1. November 2022; Antwort des Berliner Senats vom Januar 2023) | 624 € | 664 € |
| München | 283–577 € für ein Einzelzimmer in der WG (Liste September 2026) | 790 € | 837 € |
| Hamburg | möbliertes Zimmer ab 310 €; keine veröffentlichte Obergrenze (Oktober 2026) | 678 € | 626 € |

So liest du die Tabelle:

- Die Wohnheimspalte ist kein Durchschnitt, sondern die Spanne aus der Liste des Studierendenwerks; der Berliner Wert stammt vom November 2022, die aktuelle Liste steht auf stw.berlin. Ein Wohnheimplatz ist nicht garantiert, Wartezeiten unterscheiden sich je Stadt.
- WG- und Wohnungsspalte sind Angebotsdaten des MLP Studentenwohnreports 2025 / Value AG und **Warmmiete** (Heizung und Nebenkosten enthalten). **Kaltmiete** ist nur das Zimmer; Strom und Internet kommen hinzu.
- Die Kaution beträgt auf dem privaten Markt höchstens drei Kaltmieten; im Wohnheim legt sie der Studierendenwerk-Vertrag fest.

Details und Quellen: die Stadtseiten [Berlin](/de/cities/berlin-q64), [München](/de/cities/munchen-q1726) und [Hamburg](/de/cities/hamburg-q1055); die [Mietkarte für Studierende](/de/student-rent-map) mit 44 Städten; der [Leitfaden zu Lebenshaltungskosten und Wohnbudget](/de/blog/wahre-kosten-studium-deutschland-budget-check).
MD,
    ];

    /**
     * Rehber: dil → slug + satır kuralları. Prod'daki Markdown yerel kopyadan küçük farklarla ayrışabildiği için
     * (ör. DE "Praktischer Tipp … gehen davon …"), eski satır tam metinle değil satır başı + zorunlu rakamlarla bulunur;
     * yine de tam bir satır eşleşmelidir, yoksa hiçbir şey yazılmaz. EN kardeşi kısa özet sürümüdür, bu blokları içermez.
     */
    private const GUIDE = [
        'tr' => ['slug' => 'is-university-free-in-germany-2026-real-costs', 'lines' => [
            ['starts' => '**Pratik bilgi', 'needs' => ['150 €', '60 €'],
                'new' => '**Pratik bilgi (Berlin, 2026/27 kış dönemi):** TU Berlin\'de 379,06 €, FU Berlin\'de 376,80 € olan Semesterbeitrag\'ın **226,80 €\'su Deutschlandsemesterticket**\'tir: altı ay boyunca Almanya genelinde yerel ve bölgesel toplu taşıma, aylık 37,80 €\'ya denk gelir (63 €\'luk Deutschlandticket\'in %60\'ı). Bilet Semesterbeitrag\'da olan şehirlerde (Berlin, Hamburg, Aachen) aylık bütçeye ulaşımı ikinci kez yazma. Münih\'te ise Semesterticket yok: öğrenciler Bavyera indirimli Deutschlandticket\'i ayda 43 € ödeyerek ayrıca alır. Kurum bazlı tutarlar: [Berlin](/tr/cities/berlin-q64), [Münih](/tr/cities/munchen-q1726), [Hamburg](/tr/cities/hamburg-q1055).'],
            ['starts' => '| Toplu taşıma', 'needs' => ['0 €'],
                'new' => '| Toplu taşıma | 0 € (Deutschlandsemesterticket Semesterbeitrag\'da) | Münih 43 € (Semesterticket yok, öğrenci Deutschlandticket\'i); Frankfurt: üniversitenin Semesterbeitrag tablosuna bak | Üniversiteye bağlı: bilet Semesterbeitrag\'daysa 0 €, değilse ayrı bilet |'],
            ['starts' => '| **Toplam aylık**', 'needs' => ['1.130–1.615'],
                'new' => '| **Toplam aylık** | **830–1.225 €** | **1.130–1.615 €** (Münih: +43 € bilet) | **780–1.095 €** (+ varsa ayrı bilet) |'],
        ]],
        'de' => ['slug' => 'is-university-free-in-germany-2026-real-costs-de', 'lines' => [
            ['starts' => '**Praktische', 'needs' => ['150 €', '60 €'],
                'new' => '**Praktische Info (Berlin, Wintersemester 2026/27):** Vom Semesterbeitrag der TU Berlin (379,06 €) und der FU Berlin (376,80 €) entfallen **226,80 € auf das Deutschlandsemesterticket**: sechs Monate Nah- und Regionalverkehr in ganz Deutschland, umgerechnet 37,80 € im Monat (60 % des Deutschlandtickets zu 63 €). Wo das Ticket im Semesterbeitrag steckt (Berlin, Hamburg, Aachen), rechne Mobilität im Monatsbudget nicht noch einmal. In München gibt es kein Semesterticket: Studierende kaufen das ermäßigte Bayern-Deutschlandticket für 43 € im Monat separat. Beträge je Hochschule: [Berlin](/de/cities/berlin-q64), [München](/de/cities/munchen-q1726), [Hamburg](/de/cities/hamburg-q1055).'],
            ['starts' => '| Öffentliche Verkehrsmittel', 'needs' => ['0 €'],
                'new' => '| Öffentliche Verkehrsmittel | 0 € (Deutschlandsemesterticket im Semesterbeitrag) | München 43 € (kein Semesterticket, ermäßigtes Deutschlandticket); Frankfurt: siehe Semesterbeitrag der Hochschule | je Hochschule: 0 € mit Ticket im Semesterbeitrag, sonst separates Ticket |'],
            ['starts' => '| **Gesamt monatlich**', 'needs' => ['1.130–1.615'],
                'new' => '| **Gesamt monatlich** | **830–1.225 €** | **1.130–1.615 €** (München: +43 € Ticket) | **780–1.095 €** (+ ggf. separates Ticket) |'],
        ]],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('faqs') || ! Schema::hasTable('posts')) {
            return;
        }
        $problems = [];
        $notes = [];
        $plan = [];
        // Kaynak metinlerin kendisi UTF-8 olmalı (deploy sırasında dosya bozulursa commonmark "Unexpected encoding" fırlatır).
        foreach (self::FAQ_NEW as $loc => $txt) {
            if (! mb_check_encoding($txt, 'UTF-8')) $problems[] = "migration kaynağı: FAQ_NEW[$loc] UTF-8 değil";
        }
        foreach (self::GUIDE as $loc => $spec) {
            foreach ($spec['lines'] as $i => $l) {
                if (! mb_check_encoding($l['new'], 'UTF-8')) $problems[] = "migration kaynağı: GUIDE[$loc][$i] UTF-8 değil";
            }
        }

        /* ---------------------------------------------------------------- FAQ kümesi */
        $faqTr = Faq::where('locale', 'tr')->where('slug', self::FAQ_TR_SLUG)->first();
        $guideTr = Post::where('locale', 'tr')->where('slug', self::GUIDE_TR_SLUG)->first();
        if (! $faqTr && ! $guideTr && app()->environment('testing')) {
            return; // taze test DB'sinde hedef yok
        }
        if (! $faqTr) {
            $problems[] = 'faq tr/' . self::FAQ_TR_SLUG . ': bulunamadı';
        } else {
            $group = $faqTr->translation_group_id;
            foreach (['tr', 'en', 'de'] as $loc) {
                $faq = $loc === 'tr' ? $faqTr : ($group ? Faq::where('locale', $loc)->where('translation_group_id', $group)->first() : null);
                if (! $faq) {
                    $notes[] = "faq $loc: kardeş yok (oluşturulmadı)";
                    continue;
                }
                $md = (string) $faq->answer_md;
                if (! mb_check_encoding($md, 'UTF-8')) {
                    $md = (string) iconv('UTF-8', 'UTF-8//IGNORE', $md); // parmak izi kontrolü için; cevap zaten bütünüyle değişiyor
                }
                if (trim($md) === trim(self::FAQ_NEW[$loc])) {
                    continue; // zaten uygulanmış
                }
                $old = collect(self::FAQ_OLD)->every(fn ($f) => str_contains($md, $f));
                if (! $old && trim($md) !== '') {
                    $problems[] = "faq $loc/{$faq->slug}: beklenen eski cevap parmak izi yok; elle değiştirilmiş içerik ezilmez";
                    continue;
                }
                $faq->answer_md = self::FAQ_NEW[$loc];
                $plan[] = $faq;
            }
        }

        /* ---------------------------------------------------------------- ücret rehberi */
        if (! $guideTr) {
            $problems[] = 'rehber tr/' . self::GUIDE_TR_SLUG . ': bulunamadı';
        } else {
            foreach (self::GUIDE as $loc => $spec) {
                $post = Post::where('locale', $loc)->where('slug', $spec['slug'])->first();
                if (! $post) {
                    $notes[] = "rehber $loc: kardeş yok";
                    continue;
                }
                if ($post->translation_group_id !== $guideTr->translation_group_id) {
                    $problems[] = "rehber $loc/{$spec['slug']}: TR rehberle aynı çeviri grubunda değil";
                    continue;
                }
                $md = (string) $post->content_md;
                if (! mb_check_encoding($md, 'UTF-8')) {
                    // Prod'daki kayıt geçersiz UTF-8 bayt içeriyor (Markdown işleyici kaydetmede bunu reddeder:
                    // "Unexpected encoding"). Yalnız geçersiz baytlar düşürülür; metin aynen kalır.
                    $md = (string) iconv('UTF-8', 'UTF-8//IGNORE', $md);
                }
                $lines = preg_split('/\R/', $md);
                if (collect($spec['lines'])->every(fn ($l) => in_array($l['new'], $lines, true))) {
                    continue; // zaten uygulanmış → no-op
                }
                $changed = $lines;
                foreach ($spec['lines'] as $l) {
                    $hits = array_keys(array_filter($changed, fn ($line) => str_starts_with($line, $l['starts'])
                        && collect($l['needs'])->every(fn ($n) => str_contains($line, $n)) && $line !== $l['new']));
                    if (count($hits) !== 1) {
                        $problems[] = "rehber $loc/{$spec['slug']}: '{$l['starts']}…' satırı tam bir kez bulunamadı (" . count($hits) . ')';
                        continue 2;
                    }
                    $changed[$hits[0]] = $l['new'];
                }
                $eol = str_contains($md, "\r\n") ? "\r\n" : "\n"; // satır sonu biçimini koru
                $post->content_md = implode($eol, $changed);
                $plan[] = $post;
            }
        }

        if ($problems) {
            throw new RuntimeException('Kira/ulaşım uyumu uygulanmadı, hiçbir şey yazılmadı: ' . implode('; ', $problems));
        }
        if (! $plan) {
            return;
        }
        DB::transaction(function () use ($plan) {
            foreach ($plan as $model) {
                try {
                    $model->save(); // answer_html / content_html model tarafında yeniden üretilir
                } catch (\Throwable $e) {
                    // Hangi kaydın hangi hatayla düştüğü görünsün; transaction geri alınır.
                    $label = ($model instanceof Faq ? 'faq ' : 'rehber ') . $model->locale . '/' . $model->slug . ' (id ' . $model->getKey() . ')';
                    throw new RuntimeException("Kira/ulaşım uyumu uygulanmadı, hiçbir şey yazılmadı: $label kaydedilemedi: " . $e->getMessage(), 0, $e);
                }
            }
        });
    }

    public function down(): void
    {
        // Geri alma yok — içerik düzeltmesi.
    }
};

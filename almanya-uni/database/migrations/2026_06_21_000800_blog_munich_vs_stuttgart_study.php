<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): München vs Stuttgart — okumak için hangi şehir? (karar çerçevesi).
 *
 * Veriler DB'den topraklandı: München (slug munchen-q1726, warm30=837€, TUM/LMU/HM,
 * mühendislik/otomotiv/sigorta-finans, Almanya'nın en pahalısı); Stuttgart (slug
 * stuttgart-q1022, warm30=640€, Uni Stuttgart/Hohenheim/DHBW, otomotiv-mühendislik
 * kalbi: Mercedes-Benz/Porsche/Bosch). Kazanan ilan ETMEZ; karar çerçevesi sunar.
 * İlke: önce BÖLÜM, sonra şehir ([[priority-university-programs]], city-vs-university blogu).
 *
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 * İç-linkler: şehir sayfaları çok-dilli (/cities/{slug}); city-vs-university & rent blogları
 * TR-only → DE/EN'de yalnızca şehir sayfalarına link verildi.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e67';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Münih'te mi okusam, Stuttgart'ta mı?" — Güney Almanya'ya gelmek isteyen öğrencilerin en sık sorduğu sorulardan biri. Kısa ve dürüst cevap: **mutlak bir "kazanan" yok; doğru olan sana uyan.** Bu yazı sana bir kazanan dayatmaz; **kendi kararını verebilmen için bir çerçeve** sunar.

## Önce en önemli kural: şehir değil, bölüm
Şehri seçmeden önce şunu netleştir: **hangi şehirde senin programın güçlü?** İki şehir de harika olabilir ama senin bölümün birinde belirgin biçimde daha iyiyse, karar zaten verilmiş demektir. Şehir, programın *üstüne* eklenen bir konfor katmanıdır — tersi değil. (Detaylı tartışma: [Şehir mi Üniversite mi?](/tr/blog/city-vs-university-which-matters-more-in-germany))

Bu yüzden ilk adımın: bölümünü **her iki şehirde de** arayıp kabul şartlarını, dilini (Almanca/İngilizce) ve içeriğini karşılaştırmak.

## Münih: prestijli, geniş yelpazeli güç merkezi
- **Güçlü alanlar:** Mühendislik, teknoloji, otomotiv, sigorta/finans, elit araştırma — ve geniş bir akademik yelpaze. **Technical University of Munich (TUM)** Almanya'nın zirvesinde; **Ludwig-Maximilians-Universität (LMU)** çok kapsamlı bir araştırma deviyse; **Munich UAS (HM)** uygulamalı.
- **İş piyasası:** BMW'nin merkezi, güçlü sigorta/finans sektörü, teknoloji ve mühendislik. Yüksek maaşlar ve geniş kariyer ağı.
- **Yaşam:** Müreffeh, tertemiz, Alpler'e yakın; canlı ama düzenli. Tek dezavantajı: çok pahalı.
- **Maliyet:** Almanya'nın **en pahalı şehri.** Öğrenci kirası ~**837 €** (warm, 30 m² referans). Bütçeni ciddi biçimde zorlar.

## Stuttgart: otomotiv ve mühendisliğin kalbi
- **Güçlü alanlar:** Otomotiv ve makine mühendisliğinin başkenti; yüksek-teknoloji sanayi. **Universität Stuttgart** mühendislik, havacılık ve otomotivde güçlü; **Universität Hohenheim** tarım, işletme ve gıda biliminde; **DHBW** ikili (dual) eğitimde lider.
- **İş piyasası:** Mercedes-Benz, Porsche ve Bosch'un merkezi → mühendis için Almanya'nın en yoğun sanayi havzalarından biri. İkili eğitim sayesinde okurken çalışma fırsatı yüksek.
- **Yaşam:** Müreffeh sanayi şehri (~633.000), vadi/tepelik coğrafya, Svabya kültürü; Münih'ten bir tık daha uygun.
- **Maliyet:** Münih'ten görece dengeli. Öğrenci kirası ~**640 €** (warm, 30 m²).

## Karşılaştırma tablosu
| Kriter | München | Stuttgart |
|---|---|---|
| Öne çıkan alanlar | Mühendislik/teknoloji, otomotiv, sigorta/finans, elit araştırma | Otomotiv & makine mühendisliği, yüksek-teknoloji, ikili eğitim |
| Büyük üniler | TUM, LMU, Munich UAS (HM) | Uni Stuttgart, Hohenheim, DHBW |
| İş piyasası | BMW + sigorta/finans + teknoloji | Mercedes-Benz, Porsche, Bosch (sanayi) |
| Öğrenci kirası (warm, 30 m²) | ~837 € (Almanya'nın en pahalısı) | ~640 € |
| Atmosfer | Müreffeh, temiz, Alpler, yüksek maaş | Müreffeh sanayi, vadi/tepe, Svabya |
| Nüfus | ~1.512.000 | ~633.000 |

## Peki sana hangisi uygun? (Hızlı karar rehberi)
- **Geniş akademik yelpaze / elit araştırma / sigorta-finans** istiyorsan → **München** (TUM/LMU + prestij).
- **Otomotiv / makine mühendisliği / ikili eğitim** ilgi alanınsa → **Stuttgart** (Mercedes/Porsche/Bosch + DHBW).
- **Bütçe önceliğinse** → Stuttgart kira açısından belirgin biçimde rahat; Münih Almanya'nın en pahalısı, WG (ev arkadaşlığı) neredeyse şart.
- **Mühendislik** her ikisinde de güçlü → bölümünün hangi üniversitede daha iyi olduğuna bak.
- **Müreffeh ama daha sakin / sanayi-odaklı şehir** istiyorsan → Stuttgart.

## Sonuç
München "prestijli, geniş ve pahalı metropol", Stuttgart "otomotiv-mühendislik kalbi ve görece uygun" demek — ama ikisi de yalnızca senin **programın** doğru olduğunda anlam taşır. Önce bölümünü iki şehirde de ara, sonra bu tabloyu kendi önceliklerinle doldur.

👉 İncele: [München şehir rehberi](/tr/cities/munchen-q1726) · [Stuttgart şehir rehberi](/tr/cities/stuttgart-q1022) · [Almanya'da şehir şehir kira](/tr/blog/germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent). Kararsızsan bölümünü yaz, birlikte iki şehirde de değerlendirelim.

---
*Kira rakamları ~30 m² warm referansına dayanır ve değişir; üniversite/şehir verileri 2026 itibarıyladır. Başvurudan önce resmî kaynaktan teyit et.*
MD;

        $deBody = <<<'MD'
„Soll ich in München oder in Stuttgart studieren?" — eine der häufigsten Fragen angehender Studierender in Süddeutschland. Die ehrliche Kurzantwort: **Es gibt keinen pauschalen „Sieger" — nur die Stadt, die zu dir passt.** Dieser Artikel drängt dir keinen Gewinner auf, sondern gibt dir einen **Rahmen für deine eigene Entscheidung.**

## Die wichtigste Regel zuerst: nicht die Stadt, sondern das Fach
Bevor du die Stadt wählst, kläre: **In welcher Stadt ist dein Studiengang stark?** Beide Städte können großartig sein — aber wenn dein Fach in einer davon deutlich besser ist, ist die Entscheidung praktisch gefallen. Die Stadt ist die Komfortebene *über* dem Studiengang, nicht umgekehrt.

Dein erster Schritt: Suche deinen Studiengang **in beiden Städten** und vergleiche Zulassung, Sprache (Deutsch/Englisch) und Inhalte.

## München: prestigeträchtiges, breit aufgestelltes Kraftzentrum
- **Starke Bereiche:** Ingenieurwesen, Technik, Automotive, Versicherungen/Finanzen, Spitzenforschung — und ein breites akademisches Spektrum. Die **Technical University of Munich (TUM)** zählt zur deutschen Spitze; die **Ludwig-Maximilians-Universität (LMU)** ist eine sehr breite Forschungsuniversität; die **Munich UAS (HM)** ist anwendungsnah.
- **Arbeitsmarkt:** Sitz von BMW, starke Versicherungs-/Finanzbranche, Technik und Ingenieurwesen. Hohe Gehälter und ein breites Karrierenetzwerk.
- **Leben:** Wohlhabend, sehr sauber, nah an den Alpen; lebendig und zugleich geordnet. Einziger Nachteil: sehr teuer.
- **Kosten:** **Teuerste Stadt Deutschlands.** Studentenmiete ca. **837 €** (warm, 30-m²-Referenz).

## Stuttgart: Herz von Automotive und Ingenieurwesen
- **Starke Bereiche:** Hauptstadt des Automobil- und Maschinenbaus; Hightech-Industrie. Die **Universität Stuttgart** ist stark in Ingenieurwesen, Luft- und Raumfahrt sowie Automotive; die **Universität Hohenheim** in Agrar-, Wirtschafts- und Lebensmittelwissenschaften; die **DHBW** führend im dualen Studium.
- **Arbeitsmarkt:** Sitz von Mercedes-Benz, Porsche und Bosch → für Ingenieure eine der dichtesten Industrieregionen Deutschlands. Dank dualem Studium hohe Chancen, parallel zu arbeiten.
- **Leben:** Wohlhabende Industriestadt (~633.000), Tal-/Hügellage, schwäbische Kultur; etwas günstiger als München.
- **Kosten:** Im Vergleich zu München ausgewogener. Studentenmiete ca. **640 €** (warm, 30 m²).

## Vergleichstabelle
| Kriterium | München | Stuttgart |
|---|---|---|
| Herausragende Bereiche | Ingenieurwesen/Technik, Automotive, Versicherungen/Finanzen, Spitzenforschung | Automobil- & Maschinenbau, Hightech, duales Studium |
| Große Hochschulen | TUM, LMU, Munich UAS (HM) | Uni Stuttgart, Hohenheim, DHBW |
| Arbeitsmarkt | BMW + Versicherungen/Finanzen + Technik | Mercedes-Benz, Porsche, Bosch (Industrie) |
| Studentenmiete (warm, 30 m²) | ca. 837 € (teuerste Deutschlands) | ca. 640 € |
| Atmosphäre | Wohlhabend, sauber, Alpen, hohe Gehälter | Wohlhabende Industrie, Tal/Hügel, schwäbisch |
| Einwohnerzahl | ~1.512.000 | ~633.000 |

## Welche Stadt passt zu dir? (Schnellentscheidung)
- **Breites akademisches Spektrum / Spitzenforschung / Versicherungen-Finanzen** → **München** (TUM/LMU + Prestige).
- **Automotive / Maschinenbau / duales Studium** → **Stuttgart** (Mercedes/Porsche/Bosch + DHBW).
- **Budget zuerst** → Stuttgart ist bei der Miete deutlich entspannter; München ist die teuerste Stadt Deutschlands, eine WG ist quasi Pflicht.
- **Ingenieurwesen** ist in beiden stark → schau, an welcher Hochschule dein Fach besser ist.
- **Wohlhabend, aber ruhiger / industriegeprägt** → Stuttgart.

## Fazit
München steht für „prestigeträchtige, breite und teure Metropole", Stuttgart für „Herz von Automotive und Ingenieurwesen, vergleichsweise günstig" — aber beide zählen nur, wenn dein **Studiengang** stimmt. Suche zuerst dein Fach in beiden Städten und fülle dann diese Tabelle mit deinen eigenen Prioritäten.

👉 Ansehen: [Stadtführer München](/de/cities/munchen-q1726) · [Stadtführer Stuttgart](/de/cities/stuttgart-q1022). Unentschlossen? Nenne deinen Studiengang — wir bewerten beide Städte gemeinsam.

---
*Mietangaben beziehen sich auf eine 30-m²-Warmmiete-Referenz und variieren; Hochschul-/Stadtdaten Stand 2026. Vor der Bewerbung bei offiziellen Quellen bestätigen.*
MD;

        $enBody = <<<'MD'
"Should I study in Munich or Stuttgart?" — one of the most common questions prospective students in southern Germany ask. The honest short answer: **there's no universal "winner" — only the city that fits you.** This article won't force a winner on you; it gives you a **framework to make your own decision.**

## The most important rule first: not the city, the subject
Before choosing the city, get clear on this: **in which city is your degree programme strong?** Both cities can be great — but if your subject is clearly better in one of them, the decision is essentially made. The city is the comfort layer *on top of* the programme, not the other way around.

So your first step: search for your programme **in both cities** and compare admission, language (German/English) and content.

## Munich: prestigious, broad powerhouse
- **Strong fields:** engineering, technology, automotive, insurance/finance, elite research — plus a broad academic range. The **Technical University of Munich (TUM)** is at Germany's very top; **Ludwig-Maximilians-Universität (LMU)** is a very broad research university; **Munich UAS (HM)** is applied.
- **Job market:** home of BMW, a strong insurance/finance sector, technology and engineering. High salaries and a wide career network.
- **Living:** prosperous, very clean, close to the Alps; lively yet orderly. The one drawback: very expensive.
- **Cost:** **the most expensive city in Germany.** Student rent ~**€837** (warm, 30 m² reference).

## Stuttgart: heart of automotive and engineering
- **Strong fields:** capital of automotive and mechanical engineering; high-tech industry. **Universität Stuttgart** is strong in engineering, aerospace and automotive; **Universität Hohenheim** in agriculture, business and food science; **DHBW** leads in dual studies.
- **Job market:** home of Mercedes-Benz, Porsche and Bosch → one of Germany's densest industrial regions for engineers. Thanks to dual study, strong chances to work while studying.
- **Living:** a prosperous industrial city (~633,000), valley/hilly terrain, Swabian culture; slightly cheaper than Munich.
- **Cost:** more balanced than Munich. Student rent ~**€640** (warm, 30 m²).

## Comparison table
| Criterion | Munich | Stuttgart |
|---|---|---|
| Standout fields | Engineering/tech, automotive, insurance/finance, elite research | Automotive & mechanical engineering, high-tech, dual study |
| Big universities | TUM, LMU, Munich UAS (HM) | Uni Stuttgart, Hohenheim, DHBW |
| Job market | BMW + insurance/finance + technology | Mercedes-Benz, Porsche, Bosch (industry) |
| Student rent (warm, 30 m²) | ~€837 (most expensive in Germany) | ~€640 |
| Atmosphere | Prosperous, clean, Alps, high salaries | Prosperous industry, valley/hills, Swabian |
| Population | ~1,512,000 | ~633,000 |

## So which fits you? (Quick decision guide)
- Want a **broad academic range / elite research / insurance-finance** → **Munich** (TUM/LMU + prestige).
- Interested in **automotive / mechanical engineering / dual study** → **Stuttgart** (Mercedes/Porsche/Bosch + DHBW).
- **Budget first** → Stuttgart is clearly easier on rent; Munich is Germany's most expensive city, so a shared flat (WG) is almost a must.
- **Engineering** is strong in both → check which university your subject is better at.
- **Prosperous but calmer / industry-focused** → Stuttgart.

## Conclusion
Munich means "a prestigious, broad and expensive metropolis"; Stuttgart means "the heart of automotive and engineering, comparatively affordable" — but both only matter once your **programme** is right. Search your subject in both cities first, then fill in this table with your own priorities.

👉 Explore: [Munich city guide](/en/cities/munchen-q1726) · [Stuttgart city guide](/en/cities/stuttgart-q1022). Undecided? Tell us your programme and we'll weigh both cities together.

---
*Rent figures refer to a 30 m² warm-rent reference and vary; university/city data as of 2026. Confirm with official sources before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'munich-vs-stuttgart-which-city-to-study-in-germany',
                'title' => 'München mi Stuttgart mı? Almanya\'da Okumak İçin Hangi Şehir? (2026)',
                'excerpt' => 'München mi Stuttgart mı? Kazanan ilan etmeyen, karar çerçevesi sunan rehber: önce bölüm sonra şehir ilkesi, München (mühendislik/TUM-LMU, ~837€ kira — Almanya\'nın en pahalısı) vs Stuttgart (otomotiv/Mercedes-Porsche-Bosch/DHBW, ~640€ kira), iş piyasası ve yaşam karşılaştırması + "sana hangisi uygun" hızlı karar rehberi.',
                'meta_title' => 'München mi Stuttgart mı? Okumak İçin Hangi Şehir? (2026)',
                'meta_description' => 'Almanya\'da München vs Stuttgart: bölüm, iş piyasası, kira (~837€ vs ~640€), yaşam karşılaştırması + hangi şehrin sana uygun olduğuna dair karar rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'munich-vs-stuttgart-which-city-to-study-in-germany-de',
                'title' => 'München oder Stuttgart? Welche Stadt zum Studium in Deutschland? (2026)',
                'excerpt' => 'München oder Stuttgart? Ein Leitfaden ohne pauschalen Sieger, mit Entscheidungsrahmen: erst das Fach, dann die Stadt, München (Ingenieurwesen/TUM-LMU, ~837€ Miete — teuerste Deutschlands) vs Stuttgart (Automotive/Mercedes-Porsche-Bosch/DHBW, ~640€ Miete), Arbeitsmarkt und Leben im Vergleich + Schnellentscheidung „welche passt zu dir".',
                'meta_title' => 'München oder Stuttgart? Welche Stadt zum Studium? (2026)',
                'meta_description' => 'München vs Stuttgart in Deutschland: Fach, Arbeitsmarkt, Miete (~837€ vs ~640€), Leben im Vergleich + Entscheidungshilfe, welche Stadt zu dir passt.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'munich-vs-stuttgart-which-city-to-study-in-germany-en',
                'title' => 'Munich or Stuttgart? Which City to Study in, in Germany? (2026)',
                'excerpt' => 'Munich or Stuttgart? A no-winner, decision-framework guide: subject before city, Munich (engineering/TUM-LMU, ~€837 rent — most expensive in Germany) vs Stuttgart (automotive/Mercedes-Porsche-Bosch/DHBW, ~€640 rent), job market and living compared + a quick "which fits you" decision guide.',
                'meta_title' => 'Munich or Stuttgart? Which City to Study in? (2026)',
                'meta_description' => 'Munich vs Stuttgart in Germany: subject, job market, rent (~€837 vs ~€640), living compared + a guide to which city fits you.',
                'body' => $enBody,
            ],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale'           => $locale,
                'translation_group_id' => $groupId,
                'user_id'          => $userId,
                'category_id'      => $categoryId,
                'title'            => $v['title'],
                'excerpt'          => Str::limit($v['excerpt'], 250, '…'),
                'content_md'       => $v['body'],
                'content_html'     => $html,
                'meta_title'       => $v['meta_title'],
                'meta_description' => Str::limit($v['meta_description'], 158, '…'),
                'reading_minutes'  => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
                'is_published'     => true,
                'published_at'     => now(),
            ];

            $existing = Post::where('slug', $v['slug'])->first();
            if ($existing) {
                $existing->update($payload);
            } else {
                Post::create($payload + ['slug' => $v['slug']]);
            }
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'munich-vs-stuttgart-which-city-to-study-in-germany',
            'munich-vs-stuttgart-which-city-to-study-in-germany-de',
            'munich-vs-stuttgart-which-city-to-study-in-germany-en',
        ])->delete();
    }
};

<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Berlin vs München — okumak için hangi şehir? (karar çerçevesi).
 *
 * Veriler DB'den topraklandı: Berlin (slug berlin-q64, ~3.755.000 nüfus, warm30=664€,
 * FU/HU/TU + Charité); München (kanonik slug munchen-q1726, ~1.512.000 nüfus, warm30=837€
 * — Almanya'nın EN PAHALI şehri, TUM/LMU/HM). Kazanan ilan ETMEZ; karar çerçevesi sunar.
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
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e65';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Berlin'de mi okusam, München'de mi?" — Almanya'ya gelmek isteyen öğrencilerin en sık sorduğu sorulardan biri. Kısa ve dürüst cevap: **mutlak bir "kazanan" yok; doğru olan sana uyan.** Bu yazı sana bir kazanan dayatmaz; **kendi kararını verebilmen için bir çerçeve** sunar. İki şehir de Almanya'nın amiral gemisi öğrenci şehirleri — biri başkent enerjisi ve görece uygun fiyat, diğeri prestij ve yüksek maliyet sunar.

## Önce en önemli kural: şehir değil, bölüm
Şehri seçmeden önce şunu netleştir: **hangi şehirde senin programın güçlü?** İki şehir de harika olabilir ama senin bölümün birinde belirgin biçimde daha iyiyse, karar zaten verilmiş demektir. Şehir, programın *üstüne* eklenen bir konfor katmanıdır — tersi değil. (Detaylı tartışma: [Şehir mi Üniversite mi?](/tr/blog/city-vs-university-which-matters-more-in-germany))

Bu yüzden ilk adımın: bölümünü **her iki şehirde de** arayıp kabul şartlarını, dilini (Almanca/İngilizce) ve içeriğini karşılaştırmak.

## Berlin: başkentin enerjisi, kültür ve startup'lar
- **Güçlü alanlar:** Startup/teknoloji sahnesi, sanat ve kültür, siyaset/sosyal bilimler, araştırma ve tıp. **Freie Universität Berlin**, **Humboldt-Universität zu Berlin** ve **Technische Universität Berlin** üçlüsü güçlü; tıpta **Charité** Avrupa'nın önde gelen kliniklerinden.
- **İş piyasası:** Avrupa'nın en canlı startup ekosistemlerinden biri; teknoloji, medya, sanat, kamu ve sivil toplum ağırlıklı. Genç ve uluslararası bir iş gücü.
- **Yaşam:** Devasa, çok kültürlü, son derece uluslararası ve yaratıcı. Bir dünya başkenti için görece uygun fiyatlı; her bütçeye ve her yaşam tarzına yer var.
- **Maliyet:** Bir başkent için makul. Öğrenci kirası ~**664 €** (warm, 30 m² referans).

## München: refah, mühendislik ve elit araştırma
- **Güçlü alanlar:** Mühendislik, teknoloji ve otomotiv (BMW, Siemens), sigorta/finans, elit araştırma. **Technical University of Munich (TUM)** ülkenin en üst sıradaki üniversitelerinden; **Ludwig-Maximilians-Universität (LMU)** çok kapsamlı; **Munich UAS (HM)** uygulamalı.
- **İş piyasası:** Yüksek maaşlar, güçlü sanayi (BMW, Siemens, Allianz), sigorta ve finans. Mühendislik/teknoloji mezunları için Almanya'nın en güçlü kariyer pazarlarından biri.
- **Yaşam:** Temiz, müreffeh, düzenli; Alpler hemen yanı başında. Çok yüksek yaşam kalitesi ama bunun bedeli pahalılık.
- **Maliyet:** Çok pahalı — **Almanya'nın en pahalı şehri.** Öğrenci kirası ~**837 €** (warm, 30 m²). Bütçeni ciddi biçimde zorlar.

## Karşılaştırma tablosu
| Kriter | Berlin | München |
|---|---|---|
| Öne çıkan alanlar | Startup/teknoloji, sanat/kültür, siyaset, araştırma, tıp | Mühendislik, otomotiv, sigorta/finans, elit araştırma |
| Büyük üniler | FU Berlin, HU Berlin, TU Berlin (+ Charité) | TUM, LMU, Munich UAS (HM) |
| İş piyasası | Startup/teknoloji + medya + kamu | Sanayi (BMW/Siemens) + sigorta/finans, yüksek maaş |
| Nüfus | ~3.755.000 | ~1.512.000 |
| Öğrenci kirası (warm, 30 m²) | ~664 € | ~837 € (en pahalı) |
| Atmosfer | Devasa, yaratıcı, uluslararası, görece uygun | Müreffeh, düzenli, pahalı, Alpler yanı başında |

## Peki sana hangisi uygun? (Hızlı karar rehberi)
- **Startup / teknoloji / sanat / siyaset / sosyal bilimler** okuyacaksan → **Berlin** (sahne + çeşitlilik).
- **Mühendislik / otomotiv / sigorta-finans** ilgi alanınsa → **München** (sanayi + TUM/LMU prestiji).
- **Bütçe önceliğinse** → Berlin belirgin biçimde daha uygun; München Almanya'nın en pahalı şehri.
- **En yüksek maaş ve sanayi ağı** istiyorsan → München.
- **Yaratıcı, özgür, çok kültürlü büyük şehir** istiyorsan → Berlin; **temiz, düzenli, doğaya yakın refah** istiyorsan → München.

## Sonuç
Berlin "başkent enerjisi ve erişilebilirlik", München "prestij ve yüksek maliyet" demek — ama ikisi de yalnızca senin **programın** doğru olduğunda anlam taşır. Önce bölümünü iki şehirde de ara, sonra bu tabloyu kendi önceliklerinle doldur.

👉 İncele: [Berlin şehir rehberi](/tr/cities/berlin-q64) · [München şehir rehberi](/tr/cities/munchen-q1726) · [Almanya'da şehir şehir kira](/tr/blog/germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent). Kararsızsan bölümünü yaz, birlikte iki şehirde de değerlendirelim.

---
*Kira rakamları ~30 m² warm referansına dayanır ve değişir; üniversite/şehir verileri 2026 itibarıyladır. Başvurudan önce resmî kaynaktan teyit et.*
MD;

        $deBody = <<<'MD'
„Soll ich in Berlin oder in München studieren?" — eine der häufigsten Fragen angehender Studierender. Die ehrliche Kurzantwort: **Es gibt keinen pauschalen „Sieger" — nur die Stadt, die zu dir passt.** Dieser Artikel drängt dir keinen Gewinner auf, sondern gibt dir einen **Rahmen für deine eigene Entscheidung.** Beide sind Deutschlands Flaggschiff-Studienstädte — die eine bietet Hauptstadtenergie und vergleichsweise bezahlbares Leben, die andere Prestige und hohe Kosten.

## Die wichtigste Regel zuerst: nicht die Stadt, sondern das Fach
Bevor du die Stadt wählst, kläre: **In welcher Stadt ist dein Studiengang stark?** Beide Städte können großartig sein — aber wenn dein Fach in einer davon deutlich besser ist, ist die Entscheidung praktisch gefallen. Die Stadt ist die Komfortebene *über* dem Studiengang, nicht umgekehrt.

Dein erster Schritt: Suche deinen Studiengang **in beiden Städten** und vergleiche Zulassung, Sprache (Deutsch/Englisch) und Inhalte.

## Berlin: Hauptstadtenergie, Kultur und Start-ups
- **Starke Bereiche:** Start-up-/Tech-Szene, Kunst und Kultur, Politik-/Sozialwissenschaften, Forschung und Medizin. Das Trio **Freie Universität Berlin**, **Humboldt-Universität zu Berlin** und **Technische Universität Berlin** ist stark; in der Medizin gehört die **Charité** zu Europas führenden Kliniken.
- **Arbeitsmarkt:** Eines der lebendigsten Start-up-Ökosysteme Europas; Schwerpunkt auf Tech, Medien, Kunst, öffentlichem Sektor und Zivilgesellschaft. Junge, internationale Belegschaft.
- **Leben:** Riesig, multikulturell, sehr international und kreativ. Für eine Weltstadt vergleichsweise bezahlbar; für jedes Budget und jeden Lebensstil ist Platz.
- **Kosten:** Für eine Hauptstadt moderat. Studentenmiete ca. **664 €** (warm, 30-m²-Referenz).

## München: Wohlstand, Ingenieurwesen und Spitzenforschung
- **Starke Bereiche:** Ingenieurwesen, Tech und Automobil (BMW, Siemens), Versicherung/Finanzen, Spitzenforschung. Die **Technical University of Munich (TUM)** zählt zu den bestplatzierten des Landes; die **Ludwig-Maximilians-Universität (LMU)** ist sehr breit aufgestellt; die **Munich UAS (HM)** anwendungsnah.
- **Arbeitsmarkt:** Hohe Gehälter, starke Industrie (BMW, Siemens, Allianz), Versicherung und Finanzen. Für Ingenieur-/Tech-Absolventen einer der stärksten Karrieremärkte Deutschlands.
- **Leben:** Sauber, wohlhabend, geordnet; die Alpen liegen direkt vor der Tür. Sehr hohe Lebensqualität, allerdings zu einem hohen Preis.
- **Kosten:** Sehr teuer — **die teuerste Stadt Deutschlands.** Studentenmiete ca. **837 €** (warm, 30 m²).

## Vergleichstabelle
| Kriterium | Berlin | München |
|---|---|---|
| Herausragende Bereiche | Start-ups/Tech, Kunst/Kultur, Politik, Forschung, Medizin | Ingenieurwesen, Automobil, Versicherung/Finanzen, Spitzenforschung |
| Große Hochschulen | FU Berlin, HU Berlin, TU Berlin (+ Charité) | TUM, LMU, Munich UAS (HM) |
| Arbeitsmarkt | Start-ups/Tech + Medien + öffentlicher Sektor | Industrie (BMW/Siemens) + Versicherung/Finanzen, hohe Gehälter |
| Einwohner | ~3.755.000 | ~1.512.000 |
| Studentenmiete (warm, 30 m²) | ca. 664 € | ca. 837 € (teuerste) |
| Atmosphäre | Riesig, kreativ, international, bezahlbarer | Wohlhabend, geordnet, teuer, Alpen vor der Tür |

## Welche Stadt passt zu dir? (Schnellentscheidung)
- **Start-ups / Tech / Kunst / Politik / Sozialwissenschaften** → **Berlin** (Szene + Vielfalt).
- **Ingenieurwesen / Automobil / Versicherung-Finanzen** → **München** (Industrie + Prestige von TUM/LMU).
- **Budget zuerst** → Berlin ist deutlich günstiger; München ist Deutschlands teuerste Stadt.
- **Höchste Gehälter und Industrienetzwerk** → München.
- **Kreative, freie, multikulturelle Großstadt** → Berlin; **saubere, geordnete, naturnahe Wohlstandsstadt** → München.

## Fazit
Berlin steht für „Hauptstadtenergie und Erreichbarkeit", München für „Prestige und hohe Kosten" — aber beide zählen nur, wenn dein **Studiengang** stimmt. Suche zuerst dein Fach in beiden Städten und fülle dann diese Tabelle mit deinen eigenen Prioritäten.

👉 Ansehen: [Stadtführer Berlin](/de/cities/berlin-q64) · [Stadtführer München](/de/cities/munchen-q1726). Unentschlossen? Nenne deinen Studiengang — wir bewerten beide Städte gemeinsam.

---
*Mietangaben beziehen sich auf eine 30-m²-Warmmiete-Referenz und variieren; Hochschul-/Stadtdaten Stand 2026. Vor der Bewerbung bei offiziellen Quellen bestätigen.*
MD;

        $enBody = <<<'MD'
"Should I study in Berlin or Munich?" — one of the most common questions prospective students ask. The honest short answer: **there's no universal "winner" — only the city that fits you.** This article won't force a winner on you; it gives you a **framework to make your own decision.** Both are Germany's flagship student cities — one offers capital-city energy and relative affordability, the other prestige and high costs.

## The most important rule first: not the city, the subject
Before choosing the city, get clear on this: **in which city is your degree programme strong?** Both cities can be great — but if your subject is clearly better in one of them, the decision is essentially made. The city is the comfort layer *on top of* the programme, not the other way around.

So your first step: search for your programme **in both cities** and compare admission, language (German/English) and content.

## Berlin: capital energy, culture and start-ups
- **Strong fields:** start-up/tech scene, arts and culture, politics/social sciences, research and medicine. The trio **Freie Universität Berlin**, **Humboldt-Universität zu Berlin** and **Technische Universität Berlin** is strong; in medicine, the **Charité** is among Europe's leading clinics.
- **Job market:** one of Europe's liveliest start-up ecosystems; focused on tech, media, arts, the public sector and civil society. A young, international workforce.
- **Living:** huge, multicultural, very international and creative. Relatively affordable for a world capital; there's room for every budget and lifestyle.
- **Cost:** moderate for a capital. Student rent ~**€664** (warm, 30 m² reference).

## Munich: prosperity, engineering and elite research
- **Strong fields:** engineering, tech and automotive (BMW, Siemens), insurance/finance, elite research. The **Technical University of Munich (TUM)** is among the country's top-ranked; **Ludwig-Maximilians-Universität (LMU)** is very broad; **Munich UAS (HM)** is applied.
- **Job market:** high salaries, strong industry (BMW, Siemens, Allianz), insurance and finance. For engineering/tech graduates, one of Germany's strongest career markets.
- **Living:** clean, prosperous, orderly; the Alps are right next door. Very high quality of life, but at a price.
- **Cost:** very expensive — **Germany's most expensive city.** Student rent ~**€837** (warm, 30 m²).

## Comparison table
| Criterion | Berlin | Munich |
|---|---|---|
| Standout fields | Start-ups/tech, arts/culture, politics, research, medicine | Engineering, automotive, insurance/finance, elite research |
| Big universities | FU Berlin, HU Berlin, TU Berlin (+ Charité) | TUM, LMU, Munich UAS (HM) |
| Job market | Start-ups/tech + media + public sector | Industry (BMW/Siemens) + insurance/finance, high salaries |
| Population | ~3,755,000 | ~1,512,000 |
| Student rent (warm, 30 m²) | ~€664 | ~€837 (most expensive) |
| Atmosphere | Huge, creative, international, more affordable | Prosperous, orderly, expensive, Alps next door |

## So which fits you? (Quick decision guide)
- Studying **start-ups / tech / arts / politics / social sciences** → **Berlin** (scene + diversity).
- Interested in **engineering / automotive / insurance-finance** → **Munich** (industry + the prestige of TUM/LMU).
- **Budget first** → Berlin is clearly cheaper; Munich is Germany's most expensive city.
- **Highest salaries and an industry network** → Munich.
- **A creative, free, multicultural metropolis** → Berlin; **a clean, orderly, nature-close prosperous city** → Munich.

## Conclusion
Berlin means "capital energy and accessibility"; Munich means "prestige and high cost" — but both only matter once your **programme** is right. Search your subject in both cities first, then fill in this table with your own priorities.

👉 Explore: [Berlin city guide](/en/cities/berlin-q64) · [Munich city guide](/en/cities/munchen-q1726). Undecided? Tell us your programme and we'll weigh both cities together.

---
*Rent figures refer to a 30 m² warm-rent reference and vary; university/city data as of 2026. Confirm with official sources before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'berlin-vs-munich-which-city-to-study-in-germany',
                'title' => 'Berlin mi München mi? Almanya\'da Okumak İçin Hangi Şehir? (2026)',
                'excerpt' => 'Berlin mi München mi? Kazanan ilan etmeyen, karar çerçevesi sunan rehber: önce bölüm sonra şehir ilkesi, Berlin (startup/sanat/FU-HU-TU, ~664€ kira) vs München (mühendislik/TUM-LMU, ~837€ kira — en pahalı şehir), iş piyasası ve yaşam karşılaştırması + "sana hangisi uygun" hızlı karar rehberi.',
                'meta_title' => 'Berlin mi München mi? Okumak İçin Hangi Şehir? (2026)',
                'meta_description' => 'Almanya\'da Berlin vs München: bölüm, iş piyasası, kira (~664€ vs ~837€), yaşam ve maliyet karşılaştırması + hangi şehrin sana uygun olduğuna dair karar rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'berlin-vs-munich-which-city-to-study-in-germany-de',
                'title' => 'Berlin oder München? Welche Stadt zum Studium in Deutschland? (2026)',
                'excerpt' => 'Berlin oder München? Ein Leitfaden ohne pauschalen Sieger, mit Entscheidungsrahmen: erst das Fach, dann die Stadt, Berlin (Start-ups/Kunst/FU-HU-TU, ~664€ Miete) vs München (Ingenieurwesen/TUM-LMU, ~837€ Miete — teuerste Stadt), Arbeitsmarkt und Leben im Vergleich + Schnellentscheidung „welche passt zu dir".',
                'meta_title' => 'Berlin oder München? Welche Stadt zum Studium? (2026)',
                'meta_description' => 'Berlin vs München in Deutschland: Fach, Arbeitsmarkt, Miete (~664€ vs ~837€), Leben und Kosten im Vergleich + Entscheidungshilfe, welche Stadt zu dir passt.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'berlin-vs-munich-which-city-to-study-in-germany-en',
                'title' => 'Berlin or Munich? Which City to Study in, in Germany? (2026)',
                'excerpt' => 'Berlin or Munich? A no-winner, decision-framework guide: subject before city, Berlin (start-ups/arts/FU-HU-TU, ~€664 rent) vs Munich (engineering/TUM-LMU, ~€837 rent — most expensive city), job market and living compared + a quick "which fits you" decision guide.',
                'meta_title' => 'Berlin or Munich? Which City to Study in? (2026)',
                'meta_description' => 'Berlin vs Munich in Germany: subject, job market, rent (~€664 vs ~€837), living and cost compared + a guide to which city fits you.',
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
            'berlin-vs-munich-which-city-to-study-in-germany',
            'berlin-vs-munich-which-city-to-study-in-germany-de',
            'berlin-vs-munich-which-city-to-study-in-germany-en',
        ])->delete();
    }
};

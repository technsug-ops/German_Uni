<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Aachen vs Karlsruhe — mühendislik için hangi şehir? (karar çerçevesi).
 *
 * Veriler DB'den topraklandı: Aachen (slug aachen-q1017, pop ~250.000, warm30=521€,
 * RWTH Aachen + FH Aachen); Karlsruhe (slug karlsruhe-q1040, pop ~308.000, warm30=570€,
 * KIT + Karlsruhe UAS/HKA + PH Karlsruhe). İkisi de elit mühendislik/CS tercihi → kazanan ilan
 * ETMEZ; karar çerçevesi sunar. İlke: önce BÖLÜM, sonra şehir ([[priority-university-programs]]).
 *
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 * İç-linkler: şehir sayfaları çok-dilli (/cities/{slug}); city-vs-university & rent blogları
 * TR-only → DE/EN'de yalnızca şehir sayfalarına link verildi.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e69';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Aachen'da mı okusam, Karlsruhe'de mi?" — özellikle mühendislik ve bilgisayar bilimleri okumak isteyen öğrencilerin en sık karşılaştığı ikilemlerden biri. Kısa ve dürüst cevap: **mutlak bir "kazanan" yok; ikisi de Almanya'nın elit mühendislik/teknoloji tercihleri.** Bu yazı sana bir kazanan dayatmaz; **kendi kararını verebilmen için bir çerçeve** sunar.

## Önce en önemli kural: şehir değil, bölüm
Şehri seçmeden önce şunu netleştir: **hangi şehirde senin programın güçlü?** Aachen ve Karlsruhe ikisi de TU9 üyesi ve mühendislikte zirvede — ama senin tam alanın (örn. makine, elektrik, otomotiv, enerji ya da yazılım/IT) birinde belirgin biçimde daha iyiyse, karar zaten verilmiş demektir. Şehir, programın *üstüne* eklenen bir konfor katmanıdır — tersi değil. (Detaylı tartışma: [Şehir mi Üniversite mi?](/tr/blog/city-vs-university-which-matters-more-in-germany))

Bu yüzden ilk adımın: bölümünü **her iki şehirde de** arayıp kabul şartlarını, dilini (Almanca/İngilizce) ve içeriğini karşılaştırmak.

## Aachen: elit mühendisliğin kompakt başkenti
- **Güçlü alanlar:** Makine mühendisliği, elektrik mühendisliği, bilgisayar bilimleri; otomotiv ve enerji araştırması. **RWTH Aachen University** Almanya'nın en iyi mühendislik/CS üniversitelerinden biri (TU9); **FH Aachen** uygulamalı (UAS).
- **İtibar:** RWTH, mühendislikte uluslararası tanınan elit bir markadır; sanayi ve araştırma bağlantıları çok güçlü.
- **Yaşam:** Kompakt bir öğrenci şehri (~250.000 nüfus), Hollanda ve Belçika sınırında (üç-ülke köşesi); son derece öğrenci ve mühendislik ağırlıklı, uygun maliyetli.
- **Maliyet:** Görece ucuz. Öğrenci kirası ~**521 €** (warm, 30 m² referans).

## Karlsruhe: teknoloji ve araştırmanın yeşil şehri
- **Güçlü alanlar:** Bilgisayar bilimleri / IT (Almanya'nın internet ve IT merkezlerinden biri), mühendislik, fizik, araştırma. **Karlsruhe Institute of Technology (KIT)** zirve teknoloji/araştırma (TU9); **Karlsruhe UAS (HKA)** uygulamalı; **PH Karlsruhe** öğretmenlik.
- **İtibar:** KIT, özellikle CS/IT ve mühendislik araştırmasında Almanya'nın en güçlü adreslerinden; teknoloji ekosistemiyle iç içe.
- **Yaşam:** Yeşil, "yelpaze biçimli" planlı şehir (~308.000 nüfus), öğrenci dostu, Baden-Württemberg'de; uygun maliyetli ve yüksek yaşam kalitesi.
- **Maliyet:** Hâlâ dengeli. Öğrenci kirası ~**570 €** (warm, 30 m²).

## Karşılaştırma tablosu
| Kriter | Aachen | Karlsruhe |
|---|---|---|
| Öne çıkan alanlar | Makine/elektrik müh., CS, otomotiv/enerji | CS/IT, mühendislik, fizik, araştırma |
| Büyük üniler | RWTH Aachen, FH Aachen | KIT, Karlsruhe UAS (HKA), PH Karlsruhe |
| İtibar | Elit mühendislik markası (TU9) | Zirve teknoloji/araştırma (TU9) |
| Öğrenci kirası (warm, 30 m²) | ~521 € | ~570 € |
| Nüfus | ~250.000 | ~308.000 |
| Atmosfer | Kompakt, sınır şehri, çok öğrenci/mühendis | Yeşil, planlı, öğrenci dostu, yüksek yaşam kalitesi |

## Peki sana hangisi uygun? (Hızlı karar rehberi)
- **Makine / elektrik mühendisliği, otomotiv veya enerji** odaklıysan → **Aachen** (RWTH'nin klasik güç alanı).
- **Bilgisayar bilimleri / IT, yazılım** odaklıysan → **Karlsruhe** (KIT + IT ekosistemi).
- **Bütçe önceliğinse** → Aachen kira açısından bir tık daha ucuz; ama her iki şehirde de WG (ev arkadaşlığı) mantıklı.
- **Komşu ülkelere (Hollanda/Belçika) yakınlık ve sınır havası** istiyorsan → Aachen.
- **Yeşil, planlı, yaşam kalitesi yüksek bir şehir** istiyorsan → Karlsruhe.

## Sonuç
İkisi de Almanya'da hırslı mühendis ve CS öğrencilerinin gözde adresleri — RWTH vs KIT. İkisi de elit ve uygun maliyetli olduğu için fark, **tam program gücü**, **konum** (sınır şehri mi, Baden-Württemberg mi) ve **şehir havasında** ortaya çıkıyor. Önce bölümünü iki şehirde de ara, sonra bu tabloyu kendi önceliklerinle doldur.

👉 İncele: [Aachen şehir rehberi](/tr/cities/aachen-q1017) · [Karlsruhe şehir rehberi](/tr/cities/karlsruhe-q1040) · [Almanya'da şehir şehir kira](/tr/blog/germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent). Kararsızsan bölümünü yaz, birlikte iki şehirde de değerlendirelim.

---
*Kira rakamları ~30 m² warm referansına dayanır ve değişir; üniversite/şehir verileri 2026 itibarıyladır. Başvurudan önce resmî kaynaktan teyit et.*
MD;

        $deBody = <<<'MD'
„Soll ich in Aachen oder in Karlsruhe studieren?" — eine der häufigsten Fragen angehender Ingenieur- und Informatikstudierender. Die ehrliche Kurzantwort: **Es gibt keinen pauschalen „Sieger" — beide gehören zu Deutschlands Elite-Adressen für Ingenieurwesen und Technik.** Dieser Artikel drängt dir keinen Gewinner auf, sondern gibt dir einen **Rahmen für deine eigene Entscheidung.**

## Die wichtigste Regel zuerst: nicht die Stadt, sondern das Fach
Bevor du die Stadt wählst, kläre: **In welcher Stadt ist dein Studiengang stark?** Aachen und Karlsruhe sind beide TU9-Mitglieder und an der Spitze des Ingenieurwesens — aber wenn dein genaues Gebiet (z. B. Maschinenbau, Elektrotechnik, Automotive, Energie oder Software/IT) in einer Stadt deutlich besser ist, ist die Entscheidung praktisch gefallen. Die Stadt ist die Komfortebene *über* dem Studiengang, nicht umgekehrt.

Dein erster Schritt: Suche deinen Studiengang **in beiden Städten** und vergleiche Zulassung, Sprache (Deutsch/Englisch) und Inhalte.

## Aachen: kompakte Hauptstadt des Elite-Ingenieurwesens
- **Starke Bereiche:** Maschinenbau, Elektrotechnik, Informatik; Automotive- und Energieforschung. Die **RWTH Aachen University** ist eine der besten Ingenieur-/Informatik-Universitäten Deutschlands (TU9); die **FH Aachen** ist anwendungsnah (UAS).
- **Reputation:** Die RWTH ist eine international anerkannte Elite-Marke im Ingenieurwesen; sehr starke Industrie- und Forschungsverbindungen.
- **Leben:** Eine kompakte Studierendenstadt (~250.000 Einwohner) im Dreiländereck mit den Niederlanden und Belgien; sehr studentisch und ingenieurgeprägt, kostengünstig.
- **Kosten:** Vergleichsweise günstig. Studentenmiete ca. **521 €** (warm, 30-m²-Referenz).

## Karlsruhe: grüne Stadt der Technik und Forschung
- **Starke Bereiche:** Informatik / IT (eine deutsche Internet- und IT-Hochburg), Ingenieurwesen, Physik, Forschung. Das **Karlsruhe Institute of Technology (KIT)** steht für Spitzen-Technik/Forschung (TU9); die **Karlsruhe UAS (HKA)** ist anwendungsnah; die **PH Karlsruhe** für das Lehramt.
- **Reputation:** Das KIT zählt besonders in CS/IT und Ingenieurforschung zu Deutschlands stärksten Adressen; eng mit dem Tech-Ökosystem verzahnt.
- **Leben:** Grüne, „fächerförmig" geplante Stadt (~308.000 Einwohner), studierendenfreundlich, in Baden-Württemberg; kostengünstig und mit hoher Lebensqualität.
- **Kosten:** Weiterhin ausgewogen. Studentenmiete ca. **570 €** (warm, 30 m²).

## Vergleichstabelle
| Kriterium | Aachen | Karlsruhe |
|---|---|---|
| Herausragende Bereiche | Maschinenbau/Elektrotechnik, Informatik, Automotive/Energie | Informatik/IT, Ingenieurwesen, Physik, Forschung |
| Große Hochschulen | RWTH Aachen, FH Aachen | KIT, Karlsruhe UAS (HKA), PH Karlsruhe |
| Reputation | Elite-Ingenieurmarke (TU9) | Spitzen-Technik/Forschung (TU9) |
| Studentenmiete (warm, 30 m²) | ca. 521 € | ca. 570 € |
| Einwohner | ~250.000 | ~308.000 |
| Atmosphäre | Kompakt, Grenzstadt, sehr studentisch/ingenieurgeprägt | Grün, geplant, studierendenfreundlich, hohe Lebensqualität |

## Welche Stadt passt zu dir? (Schnellentscheidung)
- **Maschinenbau / Elektrotechnik, Automotive oder Energie** → **Aachen** (klassische Stärke der RWTH).
- **Informatik / IT, Software** → **Karlsruhe** (KIT + IT-Ökosystem).
- **Budget zuerst** → Aachen ist bei der Miete einen Tick günstiger; eine WG ist in beiden Städten sinnvoll.
- **Nähe zu Nachbarländern (Niederlande/Belgien) und Grenzflair** → Aachen.
- **Grüne, geplante Stadt mit hoher Lebensqualität** → Karlsruhe.

## Fazit
Beide sind in Deutschland die Top-Adressen für ambitionierte Ingenieur- und Informatikstudierende — RWTH vs KIT. Da beide elitär und kostengünstig sind, entscheidet der Unterschied über die **genaue Programmstärke**, die **Lage** (Grenzstadt oder Baden-Württemberg) und das **Stadtflair**. Suche zuerst dein Fach in beiden Städten und fülle dann diese Tabelle mit deinen eigenen Prioritäten.

👉 Ansehen: [Stadtführer Aachen](/de/cities/aachen-q1017) · [Stadtführer Karlsruhe](/de/cities/karlsruhe-q1040). Unentschlossen? Nenne deinen Studiengang — wir bewerten beide Städte gemeinsam.

---
*Mietangaben beziehen sich auf eine 30-m²-Warmmiete-Referenz und variieren; Hochschul-/Stadtdaten Stand 2026. Vor der Bewerbung bei offiziellen Quellen bestätigen.*
MD;

        $enBody = <<<'MD'
"Should I study in Aachen or Karlsruhe?" — one of the most common dilemmas for prospective engineering and computer science students. The honest short answer: **there's no universal "winner" — both are among Germany's elite choices for engineering and tech.** This article won't force a winner on you; it gives you a **framework to make your own decision.**

## The most important rule first: not the city, the subject
Before choosing the city, get clear on this: **in which city is your degree programme strong?** Aachen and Karlsruhe are both TU9 members and at the top of engineering — but if your exact field (e.g. mechanical, electrical, automotive, energy or software/IT) is clearly better in one of them, the decision is essentially made. The city is the comfort layer *on top of* the programme, not the other way around.

So your first step: search for your programme **in both cities** and compare admission, language (German/English) and content.

## Aachen: compact capital of elite engineering
- **Strong fields:** mechanical engineering, electrical engineering, computer science; automotive and energy research. **RWTH Aachen University** is one of Germany's top engineering/CS universities (TU9); **FH Aachen** is applied (UAS).
- **Reputation:** RWTH is an internationally recognised elite brand in engineering, with very strong industry and research links.
- **Living:** a compact student city (~250,000 population) in the tri-border corner with the Netherlands and Belgium; very student- and engineering-dominated, and affordable.
- **Cost:** comparatively cheap. Student rent ~**€521** (warm, 30 m² reference).

## Karlsruhe: green city of tech and research
- **Strong fields:** computer science / IT (a German internet and IT hub), engineering, physics, research. **Karlsruhe Institute of Technology (KIT)** stands for top tech/research (TU9); **Karlsruhe UAS (HKA)** is applied; **PH Karlsruhe** is for teacher training.
- **Reputation:** KIT is among Germany's strongest addresses, especially in CS/IT and engineering research, and is closely tied to the tech ecosystem.
- **Living:** a green, "fan-shaped" planned city (~308,000 population), student-friendly, in Baden-Württemberg; affordable and with a high quality of life.
- **Cost:** still balanced. Student rent ~**€570** (warm, 30 m²).

## Comparison table
| Criterion | Aachen | Karlsruhe |
|---|---|---|
| Standout fields | Mechanical/electrical eng., CS, automotive/energy | CS/IT, engineering, physics, research |
| Big universities | RWTH Aachen, FH Aachen | KIT, Karlsruhe UAS (HKA), PH Karlsruhe |
| Reputation | Elite engineering brand (TU9) | Top tech/research (TU9) |
| Student rent (warm, 30 m²) | ~€521 | ~€570 |
| Population | ~250,000 | ~308,000 |
| Atmosphere | Compact, border city, very student/engineering | Green, planned, student-friendly, high quality of life |

## So which fits you? (Quick decision guide)
- Focused on **mechanical / electrical engineering, automotive or energy** → **Aachen** (RWTH's classic strength).
- Focused on **computer science / IT, software** → **Karlsruhe** (KIT + IT ecosystem).
- **Budget first** → Aachen is a touch cheaper on rent; a shared flat (WG) makes sense in both.
- **Proximity to neighbouring countries (Netherlands/Belgium) and a border vibe** → Aachen.
- **A green, planned city with high quality of life** → Karlsruhe.

## Conclusion
Both are the go-to addresses in Germany for ambitious engineering and CS students — RWTH vs KIT. Because both are elite and affordable, the difference comes down to **exact programme strength**, **location** (border city or Baden-Württemberg) and **city vibe**. Search your subject in both cities first, then fill in this table with your own priorities.

👉 Explore: [Aachen city guide](/en/cities/aachen-q1017) · [Karlsruhe city guide](/en/cities/karlsruhe-q1040). Undecided? Tell us your programme and we'll weigh both cities together.

---
*Rent figures refer to a 30 m² warm-rent reference and vary; university/city data as of 2026. Confirm with official sources before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'aachen-vs-karlsruhe-which-city-to-study-in-germany',
                'title' => 'Aachen mı Karlsruhe mi? Mühendislik İçin Almanya\'da Hangi Şehir? (2026)',
                'excerpt' => 'Aachen mı Karlsruhe mi? Kazanan ilan etmeyen, karar çerçevesi sunan rehber: önce bölüm sonra şehir ilkesi, Aachen (RWTH/makine-elektrik müh., ~521€ kira) vs Karlsruhe (KIT/CS-IT, ~570€ kira), itibar, yaşam ve konum karşılaştırması + "sana hangisi uygun" hızlı karar rehberi.',
                'meta_title' => 'Aachen mı Karlsruhe mi? Mühendislik İçin Hangi Şehir? (2026)',
                'meta_description' => 'Almanya\'da Aachen vs Karlsruhe: bölüm, itibar (RWTH vs KIT), kira (~521€ vs ~570€), yaşam ve konum karşılaştırması + hangi şehrin sana uygun olduğuna dair karar rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'aachen-vs-karlsruhe-which-city-to-study-in-germany-de',
                'title' => 'Aachen oder Karlsruhe? Welche Stadt zum (Ingenieur-)Studium? (2026)',
                'excerpt' => 'Aachen oder Karlsruhe? Ein Leitfaden ohne pauschalen Sieger, mit Entscheidungsrahmen: erst das Fach, dann die Stadt, Aachen (RWTH/Maschinenbau-Elektrotechnik, ~521€ Miete) vs Karlsruhe (KIT/CS-IT, ~570€ Miete), Reputation, Leben und Lage im Vergleich + Schnellentscheidung „welche passt zu dir".',
                'meta_title' => 'Aachen oder Karlsruhe? Welche Stadt zum Ingenieur-Studium? (2026)',
                'meta_description' => 'Aachen vs Karlsruhe in Deutschland: Fach, Reputation (RWTH vs KIT), Miete (~521€ vs ~570€), Leben und Lage im Vergleich + Entscheidungshilfe, welche Stadt zu dir passt.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'aachen-vs-karlsruhe-which-city-to-study-in-germany-en',
                'title' => 'Aachen or Karlsruhe? Which City to Study Engineering in Germany? (2026)',
                'excerpt' => 'Aachen or Karlsruhe? A no-winner, decision-framework guide: subject before city, Aachen (RWTH/mechanical-electrical eng., ~€521 rent) vs Karlsruhe (KIT/CS-IT, ~€570 rent), reputation, living and location compared + a quick "which fits you" decision guide.',
                'meta_title' => 'Aachen or Karlsruhe? Which City to Study Engineering in? (2026)',
                'meta_description' => 'Aachen vs Karlsruhe in Germany: subject, reputation (RWTH vs KIT), rent (~€521 vs ~€570), living and location compared + a guide to which city fits you.',
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
            'aachen-vs-karlsruhe-which-city-to-study-in-germany',
            'aachen-vs-karlsruhe-which-city-to-study-in-germany-de',
            'aachen-vs-karlsruhe-which-city-to-study-in-germany-en',
        ])->delete();
    }
};

<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Bremen vs Hannover — okumak için hangi şehir? (karar çerçevesi).
 *
 * Veriler DB'den topraklandı: Bremen (slug bremen-q24879, ~569.000 nüfus, warm30=504€,
 * Universität Bremen, Hochschule Bremen); Hannover (slug hannover-q1715, ~545.000 nüfus,
 * warm30=477€, Leibniz Universität Hannover, MHH, Hochschule Hannover). İki bütçe-dostu,
 * pratik kuzey şehri. Kazanan ilan ETMEZ; karar çerçevesi sunar.
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
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e6c';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Bremen'de mi okusam, Hannover'de mi?" — Almanya'nın kuzeyinde, üstelik bütçe dostu bir şehir arayan öğrenciler için çok yerinde bir soru. Kısa ve dürüst cevap: **mutlak bir "kazanan" yok; doğru olan sana uyan.** Bu yazı sana bir kazanan dayatmaz; **kendi kararını verebilmen için bir çerçeve** sunar. İyi haber: her iki şehir de kira açısından Almanya'nın en rahat şehirlerinden.

## Önce en önemli kural: şehir değil, bölüm
Şehri seçmeden önce şunu netleştir: **hangi şehirde senin programın güçlü?** İki şehir de harika olabilir ama senin bölümün birinde belirgin biçimde daha iyiyse, karar zaten verilmiş demektir. Şehir, programın *üstüne* eklenen bir konfor katmanıdır — tersi değil. (Detaylı tartışma: [Şehir mi Üniversite mi?](/tr/blog/city-vs-university-which-matters-more-in-germany))

Bu yüzden ilk adımın: bölümünü **her iki şehirde de** arayıp kabul şartlarını, dilini (Almanca/İngilizce) ve içeriğini karşılaştırmak.

## Bremen: liman, denizcilik ve havacılık
- **Güçlü alanlar:** Denizcilik ve çevre bilimleri, havacılık-uzay, mühendislik, lojistik (liman). **Universität Bremen** araştırma odaklı güçlü bir üni — deniz bilimleri, mühendislik ve üretim teknolojisinde öne çıkar; **Hochschule Bremen** (uygulamalı) havacılık ve gemi mühendisliğiyle bilinir.
- **İş piyasası:** Liman kenti olması ticaret/lojistiği besler; havacılık-uzay (Airbus/OHB) ve denizcilik kümeleri staj ve iş için cazip.
- **Yaşam:** Daha küçük, sakin bir kuzey liman şehri (~569.000). Denizci havası, rahat tempo, bütçe dostu.
- **Maliyet:** Çok uygun. Öğrenci kirası ~**504 €** (warm, 30 m² referans).

## Hannover: mühendislik, tıp ve fuar ekonomisi
- **Güçlü alanlar:** Mühendislik ve tıp güçlü. **Gottfried Wilhelm Leibniz Universität Hannover** mühendislikte köklü; **Hannover Tıp Fakültesi (MHH)** Almanya'nın en iyi tıp okullarından biri; **Hochschule Hannover** (uygulamalı) geniş yelpaze sunar.
- **İş piyasası:** Fuar ekonomisi (Messe / CeBIT mirası), mühendislik sanayii ve sağlık sektörü. Merkezi konumu iş ağı için avantaj.
- **Yaşam:** Almanya'nın tam ortasında, son derece iyi bağlantılı (~545.000). Pratik, mütevazı ama canlı bir fuar şehri.
- **Maliyet:** Çok uygun — Bremen'den de bir tık ucuz. Öğrenci kirası ~**477 €** (warm, 30 m²).

## Karşılaştırma tablosu
| Kriter | Bremen | Hannover |
|---|---|---|
| Öne çıkan alanlar | Deniz/çevre bilimleri, havacılık, mühendislik, lojistik | Mühendislik, tıp (MHH), fuar ekonomisi |
| Büyük üniler | Universität Bremen, Hochschule Bremen | Leibniz Uni Hannover, MHH, Hochschule Hannover |
| İş piyasası | Liman/lojistik + havacılık + denizcilik | Mühendislik + sağlık + fuar (Messe) |
| Öğrenci kirası (warm, 30 m²) | ~504 € | ~477 € |
| Atmosfer | Küçük, sakin, denizci, bütçe dostu | Merkezi, pratik, iyi bağlantılı, bütçe dostu |
| Ulaşım | İyi, ama merkez değil | Almanya'nın tam ortası, mükemmel bağlantı |

## Peki sana hangisi uygun? (Hızlı karar rehberi)
- **Deniz/çevre bilimleri / havacılık-uzay / lojistik** ilgi alanınsa → **Bremen** (küme + liman).
- **Mühendislik veya tıp** okuyacaksan → **Hannover** (Leibniz + MHH).
- **Bütçe önceliğinse** → ikisi de çok rahat; Hannover kira açısından bir tık daha ucuz ama fark küçük.
- **Merkezi konum / kolay seyahat** istiyorsan → Hannover (Almanya'nın tam ortası).
- **Sakin, denizci, daha küçük bir şehir** istiyorsan → Bremen.

## Sonuç
Bremen "denizcilik, havacılık ve sakin liman havası", Hannover "mühendislik, güçlü tıp (MHH) ve merkezi pratiklik" demek — ama ikisi de yalnızca senin **programın** doğru olduğunda anlam taşır. İkisi de bütçeye nazik; bu yüzden asıl ayrım bölümün ve yaşam tarzın olacak. Önce bölümünü iki şehirde de ara, sonra bu tabloyu kendi önceliklerinle doldur.

👉 İncele: [Bremen şehir rehberi](/tr/cities/bremen-q24879) · [Hannover şehir rehberi](/tr/cities/hannover-q1715) · [Almanya'da şehir şehir kira](/tr/blog/germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent). Kararsızsan bölümünü yaz, birlikte iki şehirde de değerlendirelim.

---
*Kira rakamları ~30 m² warm referansına dayanır ve değişir; üniversite/şehir verileri 2026 itibarıyladır. Başvurudan önce resmî kaynaktan teyit et.*
MD;

        $deBody = <<<'MD'
„Soll ich in Bremen oder in Hannover studieren?" — eine sehr passende Frage für alle, die im Norden Deutschlands eine zugleich budgetfreundliche Stadt suchen. Die ehrliche Kurzantwort: **Es gibt keinen pauschalen „Sieger" — nur die Stadt, die zu dir passt.** Dieser Artikel drängt dir keinen Gewinner auf, sondern gibt dir einen **Rahmen für deine eigene Entscheidung.** Die gute Nachricht: Beide Städte gehören bei der Miete zu den entspanntesten in Deutschland.

## Die wichtigste Regel zuerst: nicht die Stadt, sondern das Fach
Bevor du die Stadt wählst, kläre: **In welcher Stadt ist dein Studiengang stark?** Beide Städte können großartig sein — aber wenn dein Fach in einer davon deutlich besser ist, ist die Entscheidung praktisch gefallen. Die Stadt ist die Komfortebene *über* dem Studiengang, nicht umgekehrt.

Dein erster Schritt: Suche deinen Studiengang **in beiden Städten** und vergleiche Zulassung, Sprache (Deutsch/Englisch) und Inhalte.

## Bremen: Hafen, maritime Wirtschaft und Luft- und Raumfahrt
- **Starke Bereiche:** Meeres- und Umweltwissenschaften, Luft- und Raumfahrt, Ingenieurwesen, Logistik (Hafen). Die **Universität Bremen** ist forschungsstark — stark in Meereswissenschaften, Ingenieurwesen und Produktionstechnik; die **Hochschule Bremen** (anwendungsnah) ist für Luft- und Raumfahrt sowie Schiffbau bekannt.
- **Arbeitsmarkt:** Als Hafenstadt fördert Bremen Handel/Logistik; Luft- und Raumfahrt (Airbus/OHB) und maritime Cluster sind attraktiv für Praktika und Jobs.
- **Leben:** Eine kleinere, entspannte nördliche Hafenstadt (~569.000). Maritime Atmosphäre, gemächliches Tempo, budgetfreundlich.
- **Kosten:** Sehr günstig. Studentenmiete ca. **504 €** (warm, 30-m²-Referenz).

## Hannover: Ingenieurwesen, Medizin und Messewirtschaft
- **Starke Bereiche:** Stark in Ingenieurwesen und Medizin. Die **Gottfried Wilhelm Leibniz Universität Hannover** ist im Ingenieurwesen etabliert; die **Medizinische Hochschule Hannover (MHH)** zählt zu den besten medizinischen Hochschulen Deutschlands; die **Hochschule Hannover** (anwendungsnah) bietet ein breites Spektrum.
- **Arbeitsmarkt:** Messewirtschaft (Messe / CeBIT-Erbe), Ingenieurindustrie und Gesundheitssektor. Die zentrale Lage ist ein Vorteil fürs Netzwerk.
- **Leben:** Mitten in Deutschland, hervorragend angebunden (~545.000). Praktisch, bodenständig, aber lebendige Messestadt.
- **Kosten:** Sehr günstig — eine Spur günstiger als Bremen. Studentenmiete ca. **477 €** (warm, 30 m²).

## Vergleichstabelle
| Kriterium | Bremen | Hannover |
|---|---|---|
| Herausragende Bereiche | Meeres-/Umweltwissenschaften, Luftfahrt, Ingenieurwesen, Logistik | Ingenieurwesen, Medizin (MHH), Messewirtschaft |
| Große Hochschulen | Universität Bremen, Hochschule Bremen | Leibniz Uni Hannover, MHH, Hochschule Hannover |
| Arbeitsmarkt | Hafen/Logistik + Luftfahrt + maritim | Ingenieurwesen + Gesundheit + Messe |
| Studentenmiete (warm, 30 m²) | ca. 504 € | ca. 477 € |
| Atmosphäre | Klein, ruhig, maritim, budgetfreundlich | Zentral, praktisch, gut angebunden, budgetfreundlich |
| Anbindung | Gut, aber kein Zentrum | Mitten in Deutschland, hervorragend |

## Welche Stadt passt zu dir? (Schnellentscheidung)
- **Meeres-/Umweltwissenschaften / Luft- und Raumfahrt / Logistik** → **Bremen** (Cluster + Hafen).
- **Ingenieurwesen oder Medizin** → **Hannover** (Leibniz + MHH).
- **Budget zuerst** → beide sind sehr entspannt; Hannover ist bei der Miete eine Spur günstiger, der Unterschied ist klein.
- **Zentrale Lage / einfaches Reisen** → Hannover (mitten in Deutschland).
- **Ruhig, maritim, eine kleinere Stadt** → Bremen.

## Fazit
Bremen steht für „maritime Wirtschaft, Luftfahrt und ruhige Hafenatmosphäre", Hannover für „Ingenieurwesen, starke Medizin (MHH) und zentrale Praktikabilität" — aber beide zählen nur, wenn dein **Studiengang** stimmt. Beide sind sanft zum Budget; der eigentliche Unterschied liegt also in deinem Fach und deinem Lebensstil. Suche zuerst dein Fach in beiden Städten und fülle dann diese Tabelle mit deinen eigenen Prioritäten.

👉 Ansehen: [Stadtführer Bremen](/de/cities/bremen-q24879) · [Stadtführer Hannover](/de/cities/hannover-q1715). Unentschlossen? Nenne deinen Studiengang — wir bewerten beide Städte gemeinsam.

---
*Mietangaben beziehen sich auf eine 30-m²-Warmmiete-Referenz und variieren; Hochschul-/Stadtdaten Stand 2026. Vor der Bewerbung bei offiziellen Quellen bestätigen.*
MD;

        $enBody = <<<'MD'
"Should I study in Bremen or Hannover?" — a very fitting question for anyone looking for a northern German city that's also easy on the budget. The honest short answer: **there's no universal "winner" — only the city that fits you.** This article won't force a winner on you; it gives you a **framework to make your own decision.** The good news: both cities are among the most relaxed in Germany when it comes to rent.

## The most important rule first: not the city, the subject
Before choosing the city, get clear on this: **in which city is your degree programme strong?** Both cities can be great — but if your subject is clearly better in one of them, the decision is essentially made. The city is the comfort layer *on top of* the programme, not the other way around.

So your first step: search for your programme **in both cities** and compare admission, language (German/English) and content.

## Bremen: port, maritime industry and aerospace
- **Strong fields:** marine and environmental sciences, aerospace, engineering, logistics (port). **Universität Bremen** is research-strong — notable in marine sciences, engineering and production technology; **Hochschule Bremen** (applied) is known for aerospace and ship engineering.
- **Job market:** as a port city it feeds trade/logistics; aerospace (Airbus/OHB) and maritime clusters are attractive for internships and jobs.
- **Living:** a smaller, relaxed northern port city (~569,000). A maritime feel, an easy pace, budget-friendly.
- **Cost:** very affordable. Student rent ~**€504** (warm, 30 m² reference).

## Hannover: engineering, medicine and the trade-fair economy
- **Strong fields:** strong in engineering and medicine. **Gottfried Wilhelm Leibniz Universität Hannover** is well established in engineering; **Hannover Medical School (MHH)** is one of Germany's top medical schools; **Hochschule Hannover** (applied) offers a broad range.
- **Job market:** the trade-fair economy (Messe / CeBIT heritage), engineering industry and the health sector. Its central location is an advantage for networking.
- **Living:** right in the middle of Germany, superbly connected (~545,000). Practical, down-to-earth but a lively trade-fair city.
- **Cost:** very affordable — a touch cheaper than Bremen. Student rent ~**€477** (warm, 30 m²).

## Comparison table
| Criterion | Bremen | Hannover |
|---|---|---|
| Standout fields | Marine/environmental sciences, aerospace, engineering, logistics | Engineering, medicine (MHH), trade-fair economy |
| Big universities | Universität Bremen, Hochschule Bremen | Leibniz Uni Hannover, MHH, Hochschule Hannover |
| Job market | Port/logistics + aerospace + maritime | Engineering + health + trade fair (Messe) |
| Student rent (warm, 30 m²) | ~€504 | ~€477 |
| Atmosphere | Small, calm, maritime, budget-friendly | Central, practical, well-connected, budget-friendly |
| Connectivity | Good, but not a hub | Right in the middle of Germany, excellent |

## So which fits you? (Quick decision guide)
- Interested in **marine/environmental sciences / aerospace / logistics** → **Bremen** (cluster + port).
- Studying **engineering or medicine** → **Hannover** (Leibniz + MHH).
- **Budget first** → both are very relaxed; Hannover is a touch cheaper on rent, but the gap is small.
- **Central location / easy travel** → Hannover (right in the middle of Germany).
- **Calm, maritime, a smaller city** → Bremen.

## Conclusion
Bremen means "maritime industry, aerospace and a calm port feel"; Hannover means "engineering, strong medicine (MHH) and central practicality" — but both only matter once your **programme** is right. Both are gentle on the budget, so the real difference comes down to your subject and your lifestyle. Search your subject in both cities first, then fill in this table with your own priorities.

👉 Explore: [Bremen city guide](/en/cities/bremen-q24879) · [Hannover city guide](/en/cities/hannover-q1715). Undecided? Tell us your programme and we'll weigh both cities together.

---
*Rent figures refer to a 30 m² warm-rent reference and vary; university/city data as of 2026. Confirm with official sources before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'bremen-vs-hannover-which-city-to-study-in-germany',
                'title' => 'Bremen mi Hannover mı? Almanya\'da Okumak İçin Hangi Şehir? (2026)',
                'excerpt' => 'Bremen mi Hannover mı? Kazanan ilan etmeyen, karar çerçevesi sunan rehber: önce bölüm sonra şehir ilkesi, Bremen (denizcilik/havacılık + liman, ~504€ kira) vs Hannover (mühendislik + MHH tıp + merkezi ulaşım, ~477€ kira), iş piyasası ve yaşam karşılaştırması + "sana hangisi uygun" hızlı karar rehberi. İki bütçe dostu kuzey şehri.',
                'meta_title' => 'Bremen mi Hannover mı? Okumak İçin Hangi Şehir? (2026)',
                'meta_description' => 'Almanya\'da Bremen vs Hannover: bölüm, iş piyasası, kira (~504€ vs ~477€), yaşam ve ulaşım karşılaştırması + hangi şehrin sana uygun olduğuna dair karar rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'bremen-vs-hannover-which-city-to-study-in-germany-de',
                'title' => 'Bremen oder Hannover? Welche Stadt zum Studium in Deutschland? (2026)',
                'excerpt' => 'Bremen oder Hannover? Ein Leitfaden ohne pauschalen Sieger, mit Entscheidungsrahmen: erst das Fach, dann die Stadt, Bremen (maritim/Luftfahrt + Hafen, ~504€ Miete) vs Hannover (Ingenieurwesen + MHH-Medizin + zentrale Anbindung, ~477€ Miete), Arbeitsmarkt und Leben im Vergleich + Schnellentscheidung „welche passt zu dir". Zwei budgetfreundliche Nordstädte.',
                'meta_title' => 'Bremen oder Hannover? Welche Stadt zum Studium? (2026)',
                'meta_description' => 'Bremen vs Hannover in Deutschland: Fach, Arbeitsmarkt, Miete (~504€ vs ~477€), Leben und Anbindung im Vergleich + Entscheidungshilfe, welche Stadt zu dir passt.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'bremen-vs-hannover-which-city-to-study-in-germany-en',
                'title' => 'Bremen or Hannover? Which City to Study in, in Germany? (2026)',
                'excerpt' => 'Bremen or Hannover? A no-winner, decision-framework guide: subject before city, Bremen (maritime/aerospace + port, ~€504 rent) vs Hannover (engineering + MHH medicine + central transport, ~€477 rent), job market and living compared + a quick "which fits you" decision guide. Two budget-friendly northern cities.',
                'meta_title' => 'Bremen or Hannover? Which City to Study in? (2026)',
                'meta_description' => 'Bremen vs Hannover in Germany: subject, job market, rent (~€504 vs ~€477), living and connectivity compared + a guide to which city fits you.',
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
            'bremen-vs-hannover-which-city-to-study-in-germany',
            'bremen-vs-hannover-which-city-to-study-in-germany-de',
            'bremen-vs-hannover-which-city-to-study-in-germany-en',
        ])->delete();
    }
};

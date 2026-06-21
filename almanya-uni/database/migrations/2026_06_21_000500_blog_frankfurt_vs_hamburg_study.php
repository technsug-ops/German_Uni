<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Frankfurt vs Hamburg — okumak için hangi şehir? (karar çerçevesi).
 *
 * Veriler DB'den topraklandı: Frankfurt (slug frankfurt-am-main-q1794, warm30=734€,
 * Goethe ~41k, Frankfurt School, Frankfurt UAS); Hamburg (kanonik slug hamburg-q1055,
 * warm30=626€, Uni Hamburg ~42k, TUHH, HAW Hamburg). Kazanan ilan ETMEZ; karar çerçevesi sunar.
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
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e64';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Frankfurt'ta mı okusam, Hamburg'da mı?" — Almanya'ya gelmek isteyen öğrencilerin en sık sorduğu sorulardan biri. Kısa ve dürüst cevap: **mutlak bir "kazanan" yok; doğru olan sana uyan.** Bu yazı sana bir kazanan dayatmaz; **kendi kararını verebilmen için bir çerçeve** sunar.

## Önce en önemli kural: şehir değil, bölüm
Şehri seçmeden önce şunu netleştir: **hangi şehirde senin programın güçlü?** İki şehir de harika olabilir ama senin bölümün birinde belirgin biçimde daha iyiyse, karar zaten verilmiş demektir. Şehir, programın *üstüne* eklenen bir konfor katmanıdır — tersi değil. (Detaylı tartışma: [Şehir mi Üniversite mi?](/tr/blog/city-vs-university-which-matters-more-in-germany))

Bu yüzden ilk adımın: bölümünü **her iki şehirde de** arayıp kabul şartlarını, dilini (Almanca/İngilizce) ve içeriğini karşılaştırmak.

## Frankfurt: finansın ve iş dünyasının başkenti
- **Güçlü alanlar:** Ekonomi, finans, bankacılık, işletme, hukuk. **Goethe Üniversitesi** (~41.000 öğrenci) ekonomi/hukukta güçlü; **Frankfurt School of Finance & Management** finans odaklı (özel); **Frankfurt UAS** uygulamalı.
- **İş piyasası:** Avrupa Merkez Bankası, Alman borsası ve onlarca bankanın merkezi. Finans/danışmanlık staj ve iş ağı için Almanya'nın bir numarası.
- **Yaşam:** Kompakt, son derece uluslararası, "Mainhattan" gökdelenleri. **Avrupa'nın en büyük havaalanı** — Türkiye'ye uçmak çok kolay.
- **Maliyet:** Pahalı. Öğrenci kirası ~**734 €** (warm, 30 m² referans). Bütçeni zorlayabilir.

## Hamburg: liman, medya ve yaşam kalitesi
- **Güçlü alanlar:** Geniş yelpaze. **Universität Hamburg** (~42.000 öğrenci) çok kapsamlı; **TUHH** mühendislikte güçlü; **HAW Hamburg** uygulamalı. Medya, lojistik, denizcilik, havacılık (Airbus) güçlü.
- **İş piyasası:** Almanya'nın en büyük limanı → ticaret/lojistik; yayıncılık ve medya merkezi; Airbus ile havacılık. Sektör çeşitliliği yüksek.
- **Yaşam:** Almanya'nın 2. büyük şehri (~1,9 milyon), su kenarı (Alster & Elbe), kuzey havası; sürekli "yaşanacak en iyi şehirler" listelerinde. Daha sakin ama canlı.
- **Maliyet:** Frankfurt'tan görece dengeli. Öğrenci kirası ~**626 €** (warm, 30 m²).

## Karşılaştırma tablosu
| Kriter | Frankfurt | Hamburg |
|---|---|---|
| Öne çıkan alanlar | Finans, ekonomi, işletme, hukuk | Mühendislik, medya, lojistik, denizcilik, havacılık |
| Büyük üniler | Goethe, Frankfurt School, Frankfurt UAS | Uni Hamburg, TUHH, HAW Hamburg |
| İş piyasası | Finans/bankacılık merkezi (ECB, borsa) | Liman/ticaret + medya + Airbus |
| Öğrenci kirası (warm, 30 m²) | ~734 € | ~626 € |
| Atmosfer | Kompakt, uluslararası, iş odaklı | Büyük, su kenarı, yaşam kalitesi yüksek |
| Türkiye'ye ulaşım | Avrupa'nın en büyük havaalanı (avantaj) | İyi, ama mega-hub değil |

## Peki sana hangisi uygun? (Hızlı karar rehberi)
- **Finans / ekonomi / işletme / hukuk** okuyacaksan → **Frankfurt** (sektör + ağ).
- **Mühendislik / medya / lojistik / denizcilik** ilgi alanınsa → **Hamburg** (çeşitlilik + TUHH/medya).
- **Bütçe önceliğinse** → Hamburg kira açısından bir tık rahat; ama her iki şehirde de WG (ev arkadaşlığı) neredeyse şart.
- **Türkiye'ye sık uçacaksan** → Frankfurt havaalanı büyük avantaj.
- **Yaşam kalitesi / sakin ama canlı şehir** istiyorsan → Hamburg.

## Sonuç
Frankfurt "bugünün iş dünyası", Hamburg "dengeli çeşitlilik ve yaşam kalitesi" demek — ama ikisi de yalnızca senin **programın** doğru olduğunda anlam taşır. Önce bölümünü iki şehirde de ara, sonra bu tabloyu kendi önceliklerinle doldur.

👉 İncele: [Frankfurt şehir rehberi](/tr/cities/frankfurt-am-main-q1794) · [Hamburg şehir rehberi](/tr/cities/hamburg-q1055) · [Almanya'da şehir şehir kira](/tr/blog/germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent). Kararsızsan bölümünü yaz, birlikte iki şehirde de değerlendirelim.

---
*Kira rakamları ~30 m² warm referansına dayanır ve değişir; üniversite/şehir verileri 2026 itibarıyladır. Başvurudan önce resmî kaynaktan teyit et.*
MD;

        $deBody = <<<'MD'
„Soll ich in Frankfurt oder in Hamburg studieren?" — eine der häufigsten Fragen angehender Studierender. Die ehrliche Kurzantwort: **Es gibt keinen pauschalen „Sieger" — nur die Stadt, die zu dir passt.** Dieser Artikel drängt dir keinen Gewinner auf, sondern gibt dir einen **Rahmen für deine eigene Entscheidung.**

## Die wichtigste Regel zuerst: nicht die Stadt, sondern das Fach
Bevor du die Stadt wählst, kläre: **In welcher Stadt ist dein Studiengang stark?** Beide Städte können großartig sein — aber wenn dein Fach in einer davon deutlich besser ist, ist die Entscheidung praktisch gefallen. Die Stadt ist die Komfortebene *über* dem Studiengang, nicht umgekehrt.

Dein erster Schritt: Suche deinen Studiengang **in beiden Städten** und vergleiche Zulassung, Sprache (Deutsch/Englisch) und Inhalte.

## Frankfurt: Hauptstadt der Finanzen und der Wirtschaft
- **Starke Bereiche:** Wirtschaft, Finanzen, Banking, BWL, Jura. Die **Goethe-Universität** (~41.000 Studierende) ist stark in Wirtschaft/Jura; die **Frankfurt School of Finance & Management** finanzorientiert (privat); die **Frankfurt UAS** anwendungsnah.
- **Arbeitsmarkt:** Sitz der Europäischen Zentralbank, der Deutschen Börse und zahlreicher Banken. Für Finanz-/Beratungs-Praktika und Netzwerke die Nummer eins.
- **Leben:** Kompakt, sehr international, „Mainhattan"-Skyline. **Größter Flughafen Europas** — Flüge in die Türkei sind sehr einfach.
- **Kosten:** Teuer. Studentenmiete ca. **734 €** (warm, 30-m²-Referenz).

## Hamburg: Hafen, Medien und Lebensqualität
- **Starke Bereiche:** Breites Spektrum. Die **Universität Hamburg** (~42.000 Studierende) ist sehr breit aufgestellt; die **TUHH** stark im Ingenieurwesen; die **HAW Hamburg** anwendungsnah. Stark in Medien, Logistik, maritimer Wirtschaft und Luftfahrt (Airbus).
- **Arbeitsmarkt:** Deutschlands größter Hafen → Handel/Logistik; Verlags- und Medienzentrum; Luftfahrt mit Airbus. Hohe Branchenvielfalt.
- **Leben:** Zweitgrößte Stadt Deutschlands (~1,9 Mio.), am Wasser (Alster & Elbe), nordische Atmosphäre; regelmäßig in „lebenswerteste Städte"-Listen.
- **Kosten:** Im Vergleich zu Frankfurt ausgewogener. Studentenmiete ca. **626 €** (warm, 30 m²).

## Vergleichstabelle
| Kriterium | Frankfurt | Hamburg |
|---|---|---|
| Herausragende Bereiche | Finanzen, Wirtschaft, BWL, Jura | Ingenieurwesen, Medien, Logistik, maritim, Luftfahrt |
| Große Hochschulen | Goethe, Frankfurt School, Frankfurt UAS | Uni Hamburg, TUHH, HAW Hamburg |
| Arbeitsmarkt | Finanz-/Bankenzentrum (EZB, Börse) | Hafen/Handel + Medien + Airbus |
| Studentenmiete (warm, 30 m²) | ca. 734 € | ca. 626 € |
| Atmosphäre | Kompakt, international, businessorientiert | Groß, am Wasser, hohe Lebensqualität |
| Anbindung in die Türkei | Größter Flughafen Europas (Vorteil) | Gut, aber kein Mega-Hub |

## Welche Stadt passt zu dir? (Schnellentscheidung)
- **Finanzen / Wirtschaft / BWL / Jura** → **Frankfurt** (Branche + Netzwerk).
- **Ingenieurwesen / Medien / Logistik / maritim** → **Hamburg** (Vielfalt + TUHH/Medien).
- **Budget zuerst** → Hamburg ist bei der Miete etwas entspannter; eine WG ist in beiden Städten quasi Pflicht.
- **Häufige Flüge in die Türkei** → Frankfurts Flughafen ist ein großer Vorteil.
- **Lebensqualität / ruhig, aber lebendig** → Hamburg.

## Fazit
Frankfurt steht für „die Wirtschaftswelt von heute", Hamburg für „ausgewogene Vielfalt und Lebensqualität" — aber beide zählen nur, wenn dein **Studiengang** stimmt. Suche zuerst dein Fach in beiden Städten und fülle dann diese Tabelle mit deinen eigenen Prioritäten.

👉 Ansehen: [Stadtführer Frankfurt](/de/cities/frankfurt-am-main-q1794) · [Stadtführer Hamburg](/de/cities/hamburg-q1055). Unentschlossen? Nenne deinen Studiengang — wir bewerten beide Städte gemeinsam.

---
*Mietangaben beziehen sich auf eine 30-m²-Warmmiete-Referenz und variieren; Hochschul-/Stadtdaten Stand 2026. Vor der Bewerbung bei offiziellen Quellen bestätigen.*
MD;

        $enBody = <<<'MD'
"Should I study in Frankfurt or Hamburg?" — one of the most common questions prospective students ask. The honest short answer: **there's no universal "winner" — only the city that fits you.** This article won't force a winner on you; it gives you a **framework to make your own decision.**

## The most important rule first: not the city, the subject
Before choosing the city, get clear on this: **in which city is your degree programme strong?** Both cities can be great — but if your subject is clearly better in one of them, the decision is essentially made. The city is the comfort layer *on top of* the programme, not the other way around.

So your first step: search for your programme **in both cities** and compare admission, language (German/English) and content.

## Frankfurt: capital of finance and business
- **Strong fields:** economics, finance, banking, business, law. **Goethe University** (~41,000 students) is strong in economics/law; the **Frankfurt School of Finance & Management** is finance-focused (private); **Frankfurt UAS** is applied.
- **Job market:** home of the European Central Bank, the German stock exchange and dozens of banks. Number one for finance/consulting internships and networks.
- **Living:** compact, very international, the "Mainhattan" skyline. **Europe's largest airport** — flights to Turkey are very easy.
- **Cost:** expensive. Student rent ~**€734** (warm, 30 m² reference).

## Hamburg: port, media and quality of life
- **Strong fields:** broad range. **Universität Hamburg** (~42,000 students) is very broad; **TUHH** is strong in engineering; **HAW Hamburg** is applied. Strong in media, logistics, maritime industry and aviation (Airbus).
- **Job market:** Germany's largest port → trade/logistics; a publishing and media hub; aviation with Airbus. High sector diversity.
- **Living:** Germany's second-largest city (~1.9 million), on the water (Alster & Elbe), a northern vibe; regularly on "most liveable cities" lists.
- **Cost:** more balanced than Frankfurt. Student rent ~**€626** (warm, 30 m²).

## Comparison table
| Criterion | Frankfurt | Hamburg |
|---|---|---|
| Standout fields | Finance, economics, business, law | Engineering, media, logistics, maritime, aviation |
| Big universities | Goethe, Frankfurt School, Frankfurt UAS | Uni Hamburg, TUHH, HAW Hamburg |
| Job market | Finance/banking hub (ECB, stock exchange) | Port/trade + media + Airbus |
| Student rent (warm, 30 m²) | ~€734 | ~€626 |
| Atmosphere | Compact, international, business-focused | Large, on the water, high quality of life |
| Travel to Turkey | Europe's largest airport (advantage) | Good, but not a mega-hub |

## So which fits you? (Quick decision guide)
- Studying **finance / economics / business / law** → **Frankfurt** (industry + network).
- Interested in **engineering / media / logistics / maritime** → **Hamburg** (diversity + TUHH/media).
- **Budget first** → Hamburg is a bit easier on rent; a shared flat (WG) is almost a must in both.
- **Frequent flights to Turkey** → Frankfurt's airport is a big advantage.
- **Quality of life / calm but lively** → Hamburg.

## Conclusion
Frankfurt means "today's business world"; Hamburg means "balanced diversity and quality of life" — but both only matter once your **programme** is right. Search your subject in both cities first, then fill in this table with your own priorities.

👉 Explore: [Frankfurt city guide](/en/cities/frankfurt-am-main-q1794) · [Hamburg city guide](/en/cities/hamburg-q1055). Undecided? Tell us your programme and we'll weigh both cities together.

---
*Rent figures refer to a 30 m² warm-rent reference and vary; university/city data as of 2026. Confirm with official sources before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'frankfurt-vs-hamburg-which-city-to-study-in-germany',
                'title' => 'Frankfurt mı Hamburg mı? Almanya\'da Okumak İçin Hangi Şehir Sana Uygun? (2026)',
                'excerpt' => 'Frankfurt mı Hamburg mı? Kazanan ilan etmeyen, karar çerçevesi sunan rehber: önce bölüm sonra şehir ilkesi, Frankfurt (finans/Goethe, ~734€ kira) vs Hamburg (liman/medya/TUHH, ~626€ kira), iş piyasası, yaşam ve Türkiye ulaşımı karşılaştırması + "sana hangisi uygun" hızlı karar rehberi.',
                'meta_title' => 'Frankfurt mı Hamburg mı? Okumak İçin Hangi Şehir? (2026)',
                'meta_description' => 'Almanya\'da Frankfurt vs Hamburg: bölüm, iş piyasası, kira (~734€ vs ~626€), yaşam ve ulaşım karşılaştırması + hangi şehrin sana uygun olduğuna dair karar rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'frankfurt-vs-hamburg-which-city-to-study-in-germany-de',
                'title' => 'Frankfurt oder Hamburg? Welche Stadt passt zum Studium in Deutschland? (2026)',
                'excerpt' => 'Frankfurt oder Hamburg? Ein Leitfaden ohne pauschalen Sieger, mit Entscheidungsrahmen: erst das Fach, dann die Stadt, Frankfurt (Finanzen/Goethe, ~734€ Miete) vs Hamburg (Hafen/Medien/TUHH, ~626€ Miete), Arbeitsmarkt, Leben und Türkei-Anbindung im Vergleich + Schnellentscheidung „welche passt zu dir".',
                'meta_title' => 'Frankfurt oder Hamburg? Welche Stadt zum Studium? (2026)',
                'meta_description' => 'Frankfurt vs Hamburg in Deutschland: Fach, Arbeitsmarkt, Miete (~734€ vs ~626€), Leben und Anbindung im Vergleich + Entscheidungshilfe, welche Stadt zu dir passt.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'frankfurt-vs-hamburg-which-city-to-study-in-germany-en',
                'title' => 'Frankfurt or Hamburg? Which City Should You Study in, in Germany? (2026)',
                'excerpt' => 'Frankfurt or Hamburg? A no-winner, decision-framework guide: subject before city, Frankfurt (finance/Goethe, ~€734 rent) vs Hamburg (port/media/TUHH, ~€626 rent), job market, living and travel-to-Turkey compared + a quick "which fits you" decision guide.',
                'meta_title' => 'Frankfurt or Hamburg? Which City to Study in? (2026)',
                'meta_description' => 'Frankfurt vs Hamburg in Germany: subject, job market, rent (~€734 vs ~€626), living and travel compared + a guide to which city fits you.',
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
            'frankfurt-vs-hamburg-which-city-to-study-in-germany',
            'frankfurt-vs-hamburg-which-city-to-study-in-germany-de',
            'frankfurt-vs-hamburg-which-city-to-study-in-germany-en',
        ])->delete();
    }
};

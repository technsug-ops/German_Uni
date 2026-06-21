<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Berlin vs Hamburg — okumak için hangi şehir? (karar çerçevesi).
 *
 * Veriler DB'den topraklandı: Berlin (slug berlin-q64, ~3.755.000 nüfus, warm30=664€,
 * Freie Universität, Humboldt, TU Berlin + Charité); Hamburg (kanonik slug hamburg-q1055,
 * ~1.910.000 nüfus, warm30=626€, Uni Hamburg, TUHH, HAW Hamburg). Kazanan ilan ETMEZ; karar çerçevesi sunar.
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
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e68';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Berlin'de mi okusam, Hamburg'da mı?" — Almanya'ya gelmek isteyen öğrencilerin en sık sorduğu sorulardan biri. Kısa ve dürüst cevap: **mutlak bir "kazanan" yok; doğru olan sana uyan.** Bu yazı sana bir kazanan dayatmaz; **kendi kararını verebilmen için bir çerçeve** sunar. Üstelik bunlar Almanya'nın en büyük iki şehri — birbirine yakın kira, taban tabana zıt karakter.

## Önce en önemli kural: şehir değil, bölüm
Şehri seçmeden önce şunu netleştir: **hangi şehirde senin programın güçlü?** İki şehir de harika olabilir ama senin bölümün birinde belirgin biçimde daha iyiyse, karar zaten verilmiş demektir. Şehir, programın *üstüne* eklenen bir konfor katmanıdır — tersi değil. (Detaylı tartışma: [Şehir mi Üniversite mi?](/tr/blog/city-vs-university-which-matters-more-in-germany))

Bu yüzden ilk adımın: bölümünü **her iki şehirde de** arayıp kabul şartlarını, dilini (Almanca/İngilizce) ve içeriğini karşılaştırmak.

## Berlin: yaratıcı, uluslararası başkent
- **Güçlü alanlar:** Startup/teknoloji, sanat ve kültür, siyaset/sosyal bilimler, araştırma, tıp. **Freie Universität Berlin** ve **Humboldt-Universität zu Berlin** geniş ve araştırma odaklı; **Technische Universität Berlin** mühendislik/teknolojide güçlü; tıpta **Charité** Avrupa'nın en prestijlilerinden.
- **İş piyasası:** Almanya'nın startup başkenti; tech, medya, sanat ve kamu/siyaset ekosistemi çok canlı. Çok uluslu ortam, İngilizce iş imkânı görece bol.
- **Yaşam:** Devasa (~3.755.000 nüfus), çok kültürlü, son derece uluslararası, yaratıcı ve sürekli hareket halinde. Bir başkent için şaşırtıcı biçimde uygun fiyatlı.
- **Maliyet:** Öğrenci kirası ~**664 €** (warm, 30 m² referans). Başkent ölçeğine göre dengeli ama hızla artıyor.

## Hamburg: su kenarı, zarif tüccar şehri
- **Güçlü alanlar:** Geniş yelpaze. **Universität Hamburg** çok kapsamlı; **TUHH** mühendislikte güçlü; **HAW Hamburg** uygulamalı. Liman/lojistik/ticaret, medya ve yayıncılık, havacılık (Airbus) öne çıkıyor.
- **İş piyasası:** Almanya'nın en büyük limanı → ticaret/lojistik; yayıncılık ve medya merkezi; Airbus ile havacılık. Sektör çeşitliliği yüksek.
- **Yaşam:** Almanya'nın 2. büyük şehri (~1.910.000 nüfus), su kenarı (Alster & Elbe), kuzey havası, zarif; sürekli "yaşanacak en iyi şehirler" listelerinde. Daha sakin ama canlı.
- **Maliyet:** Berlin'le neredeyse aynı. Öğrenci kirası ~**626 €** (warm, 30 m²).

## Karşılaştırma tablosu
| Kriter | Berlin | Hamburg |
|---|---|---|
| Öne çıkan alanlar | Startup/tech, sanat, siyaset/sosyal bilimler, araştırma, tıp | Mühendislik, medya, lojistik, denizcilik, havacılık |
| Büyük üniler | FU Berlin, Humboldt, TU Berlin, Charité | Uni Hamburg, TUHH, HAW Hamburg |
| İş piyasası | Startup/tech + medya + kamu/siyaset | Liman/ticaret + medya + Airbus |
| Öğrenci kirası (warm, 30 m²) | ~664 € | ~626 € |
| Atmosfer | Devasa, uluslararası, yaratıcı, "edgy" | Büyük, su kenarı, zarif, yaşam kalitesi yüksek |
| Karakter | Başkent enerjisi, çeşitlilik | Sakin zarafet, kuzey huzuru |

## Peki sana hangisi uygun? (Hızlı karar rehberi)
- **Startup / tech / sanat / siyaset-sosyal bilimler / tıp** okuyacaksan → **Berlin** (ekosistem + Charité/araştırma).
- **Mühendislik / medya / lojistik / denizcilik** ilgi alanınsa → **Hamburg** (çeşitlilik + TUHH/medya).
- **Bütçe önceliğinse** → kira neredeyse aynı; her iki şehirde de WG (ev arkadaşlığı) neredeyse şart.
- **Uluslararası, çok kültürlü, hareketli ortam** istiyorsan → Berlin.
- **Yaşam kalitesi / sakin ama zarif şehir** istiyorsan → Hamburg.

## Sonuç
Berlin "yaratıcı, uluslararası başkent enerjisi", Hamburg "su kenarında zarif yaşam kalitesi" demek — ama ikisi de yalnızca senin **programın** doğru olduğunda anlam taşır. Önce bölümünü iki şehirde de ara, sonra bu tabloyu kendi önceliklerinle doldur.

👉 İncele: [Berlin şehir rehberi](/tr/cities/berlin-q64) · [Hamburg şehir rehberi](/tr/cities/hamburg-q1055) · [Almanya'da şehir şehir kira](/tr/blog/germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent). Kararsızsan bölümünü yaz, birlikte iki şehirde de değerlendirelim.

---
*Kira rakamları ~30 m² warm referansına dayanır ve değişir; üniversite/şehir verileri 2026 itibarıyladır. Başvurudan önce resmî kaynaktan teyit et.*
MD;

        $deBody = <<<'MD'
„Soll ich in Berlin oder in Hamburg studieren?" — eine der häufigsten Fragen angehender Studierender. Die ehrliche Kurzantwort: **Es gibt keinen pauschalen „Sieger" — nur die Stadt, die zu dir passt.** Dieser Artikel drängt dir keinen Gewinner auf, sondern gibt dir einen **Rahmen für deine eigene Entscheidung.** Es sind zudem Deutschlands zwei größte Städte — ähnliche Miete, völlig unterschiedlicher Charakter.

## Die wichtigste Regel zuerst: nicht die Stadt, sondern das Fach
Bevor du die Stadt wählst, kläre: **In welcher Stadt ist dein Studiengang stark?** Beide Städte können großartig sein — aber wenn dein Fach in einer davon deutlich besser ist, ist die Entscheidung praktisch gefallen. Die Stadt ist die Komfortebene *über* dem Studiengang, nicht umgekehrt.

Dein erster Schritt: Suche deinen Studiengang **in beiden Städten** und vergleiche Zulassung, Sprache (Deutsch/Englisch) und Inhalte.

## Berlin: kreative, internationale Hauptstadt
- **Starke Bereiche:** Startups/Tech, Kunst und Kultur, Politik/Sozialwissenschaften, Forschung, Medizin. Die **Freie Universität Berlin** und die **Humboldt-Universität zu Berlin** sind breit und forschungsstark; die **Technische Universität Berlin** ist stark in Ingenieurwesen/Technik; in der Medizin gehört die **Charité** zu Europas renommiertesten.
- **Arbeitsmarkt:** Deutschlands Startup-Hauptstadt; ein sehr lebendiges Ökosystem aus Tech, Medien, Kunst und Politik/Verwaltung. Internationales Umfeld, vergleichsweise viele englischsprachige Jobs.
- **Leben:** Riesig (~3.755.000 Einwohner), multikulturell, sehr international, kreativ und ständig in Bewegung. Für eine Hauptstadt überraschend bezahlbar.
- **Kosten:** Studentenmiete ca. **664 €** (warm, 30-m²-Referenz). Für eine Hauptstadt ausgewogen, steigt aber schnell.

## Hamburg: am Wasser, elegante Kaufmannsstadt
- **Starke Bereiche:** Breites Spektrum. Die **Universität Hamburg** ist sehr breit aufgestellt; die **TUHH** stark im Ingenieurwesen; die **HAW Hamburg** anwendungsnah. Stark in Hafen/Logistik/Handel, Medien und Verlagswesen sowie Luftfahrt (Airbus).
- **Arbeitsmarkt:** Deutschlands größter Hafen → Handel/Logistik; Verlags- und Medienzentrum; Luftfahrt mit Airbus. Hohe Branchenvielfalt.
- **Leben:** Zweitgrößte Stadt Deutschlands (~1.910.000 Einwohner), am Wasser (Alster & Elbe), nordische Atmosphäre, elegant; regelmäßig in „lebenswerteste Städte"-Listen.
- **Kosten:** Fast wie Berlin. Studentenmiete ca. **626 €** (warm, 30 m²).

## Vergleichstabelle
| Kriterium | Berlin | Hamburg |
|---|---|---|
| Herausragende Bereiche | Startups/Tech, Kunst, Politik/Sozialwissenschaften, Forschung, Medizin | Ingenieurwesen, Medien, Logistik, maritim, Luftfahrt |
| Große Hochschulen | FU Berlin, Humboldt, TU Berlin, Charité | Uni Hamburg, TUHH, HAW Hamburg |
| Arbeitsmarkt | Startups/Tech + Medien + Politik/Verwaltung | Hafen/Handel + Medien + Airbus |
| Studentenmiete (warm, 30 m²) | ca. 664 € | ca. 626 € |
| Atmosphäre | Riesig, international, kreativ, „edgy" | Groß, am Wasser, elegant, hohe Lebensqualität |
| Charakter | Hauptstadt-Energie, Vielfalt | Ruhige Eleganz, nordische Gelassenheit |

## Welche Stadt passt zu dir? (Schnellentscheidung)
- **Startups / Tech / Kunst / Politik-Sozialwissenschaften / Medizin** → **Berlin** (Ökosystem + Charité/Forschung).
- **Ingenieurwesen / Medien / Logistik / maritim** → **Hamburg** (Vielfalt + TUHH/Medien).
- **Budget zuerst** → Miete ist nahezu gleich; eine WG ist in beiden Städten quasi Pflicht.
- **Internationales, multikulturelles, lebhaftes Umfeld** → Berlin.
- **Lebensqualität / ruhig, aber elegant** → Hamburg.

## Fazit
Berlin steht für „kreative, internationale Hauptstadt-Energie", Hamburg für „elegante Lebensqualität am Wasser" — aber beide zählen nur, wenn dein **Studiengang** stimmt. Suche zuerst dein Fach in beiden Städten und fülle dann diese Tabelle mit deinen eigenen Prioritäten.

👉 Ansehen: [Stadtführer Berlin](/de/cities/berlin-q64) · [Stadtführer Hamburg](/de/cities/hamburg-q1055). Unentschlossen? Nenne deinen Studiengang — wir bewerten beide Städte gemeinsam.

---
*Mietangaben beziehen sich auf eine 30-m²-Warmmiete-Referenz und variieren; Hochschul-/Stadtdaten Stand 2026. Vor der Bewerbung bei offiziellen Quellen bestätigen.*
MD;

        $enBody = <<<'MD'
"Should I study in Berlin or Hamburg?" — one of the most common questions prospective students ask. The honest short answer: **there's no universal "winner" — only the city that fits you.** This article won't force a winner on you; it gives you a **framework to make your own decision.** These are also Germany's two largest cities — similar rent, very different character.

## The most important rule first: not the city, the subject
Before choosing the city, get clear on this: **in which city is your degree programme strong?** Both cities can be great — but if your subject is clearly better in one of them, the decision is essentially made. The city is the comfort layer *on top of* the programme, not the other way around.

So your first step: search for your programme **in both cities** and compare admission, language (German/English) and content.

## Berlin: creative, international capital
- **Strong fields:** startups/tech, arts and culture, politics/social sciences, research, medicine. **Freie Universität Berlin** and **Humboldt-Universität zu Berlin** are broad and research-strong; **Technische Universität Berlin** is strong in engineering/technology; in medicine, the **Charité** is among Europe's most prestigious.
- **Job market:** Germany's startup capital; a very lively ecosystem of tech, media, arts and public/political work. International environment, comparatively many English-language jobs.
- **Living:** huge (~3,755,000 population), multicultural, very international, creative and always in motion. Surprisingly affordable for a capital.
- **Cost:** student rent ~**€664** (warm, 30 m² reference). Balanced for a capital, but rising fast.

## Hamburg: on the water, elegant merchant city
- **Strong fields:** broad range. **Universität Hamburg** is very broad; **TUHH** is strong in engineering; **HAW Hamburg** is applied. Strong in port/logistics/trade, media and publishing, and aviation (Airbus).
- **Job market:** Germany's largest port → trade/logistics; a publishing and media hub; aviation with Airbus. High sector diversity.
- **Living:** Germany's second-largest city (~1,910,000 population), on the water (Alster & Elbe), a northern vibe, elegant; regularly on "most liveable cities" lists.
- **Cost:** almost the same as Berlin. Student rent ~**€626** (warm, 30 m²).

## Comparison table
| Criterion | Berlin | Hamburg |
|---|---|---|
| Standout fields | Startups/tech, arts, politics/social sciences, research, medicine | Engineering, media, logistics, maritime, aviation |
| Big universities | FU Berlin, Humboldt, TU Berlin, Charité | Uni Hamburg, TUHH, HAW Hamburg |
| Job market | Startups/tech + media + public/politics | Port/trade + media + Airbus |
| Student rent (warm, 30 m²) | ~€664 | ~€626 |
| Atmosphere | Huge, international, creative, "edgy" | Large, on the water, elegant, high quality of life |
| Character | Capital energy, diversity | Calm elegance, northern ease |

## So which fits you? (Quick decision guide)
- Studying **startups / tech / arts / politics-social sciences / medicine** → **Berlin** (ecosystem + Charité/research).
- Interested in **engineering / media / logistics / maritime** → **Hamburg** (diversity + TUHH/media).
- **Budget first** → rent is nearly the same; a shared flat (WG) is almost a must in both.
- **International, multicultural, lively environment** → Berlin.
- **Quality of life / calm but elegant** → Hamburg.

## Conclusion
Berlin means "creative, international capital energy"; Hamburg means "elegant quality of life on the water" — but both only matter once your **programme** is right. Search your subject in both cities first, then fill in this table with your own priorities.

👉 Explore: [Berlin city guide](/en/cities/berlin-q64) · [Hamburg city guide](/en/cities/hamburg-q1055). Undecided? Tell us your programme and we'll weigh both cities together.

---
*Rent figures refer to a 30 m² warm-rent reference and vary; university/city data as of 2026. Confirm with official sources before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'berlin-vs-hamburg-which-city-to-study-in-germany',
                'title' => 'Berlin mi Hamburg mı? Almanya\'da Okumak İçin Hangi Şehir? (2026)',
                'excerpt' => 'Berlin mi Hamburg mı? Kazanan ilan etmeyen, karar çerçevesi sunan rehber: önce bölüm sonra şehir ilkesi, Berlin (startup/tech, sanat, tıp/Charité, ~664€ kira) vs Hamburg (liman/medya/TUHH, ~626€ kira), iş piyasası, yaşam ve karakter karşılaştırması + "sana hangisi uygun" hızlı karar rehberi.',
                'meta_title' => 'Berlin mi Hamburg mı? Okumak İçin Hangi Şehir? (2026)',
                'meta_description' => 'Almanya\'da Berlin vs Hamburg: bölüm, iş piyasası, kira (~664€ vs ~626€), yaşam ve karakter karşılaştırması + hangi şehrin sana uygun olduğuna dair karar rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'berlin-vs-hamburg-which-city-to-study-in-germany-de',
                'title' => 'Berlin oder Hamburg? Welche Stadt zum Studium in Deutschland? (2026)',
                'excerpt' => 'Berlin oder Hamburg? Ein Leitfaden ohne pauschalen Sieger, mit Entscheidungsrahmen: erst das Fach, dann die Stadt, Berlin (Startups/Tech, Kunst, Medizin/Charité, ~664€ Miete) vs Hamburg (Hafen/Medien/TUHH, ~626€ Miete), Arbeitsmarkt, Leben und Charakter im Vergleich + Schnellentscheidung „welche passt zu dir".',
                'meta_title' => 'Berlin oder Hamburg? Welche Stadt zum Studium? (2026)',
                'meta_description' => 'Berlin vs Hamburg in Deutschland: Fach, Arbeitsmarkt, Miete (~664€ vs ~626€), Leben und Charakter im Vergleich + Entscheidungshilfe, welche Stadt zu dir passt.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'berlin-vs-hamburg-which-city-to-study-in-germany-en',
                'title' => 'Berlin or Hamburg? Which City to Study in, in Germany? (2026)',
                'excerpt' => 'Berlin or Hamburg? A no-winner, decision-framework guide: subject before city, Berlin (startups/tech, arts, medicine/Charité, ~€664 rent) vs Hamburg (port/media/TUHH, ~€626 rent), job market, living and character compared + a quick "which fits you" decision guide.',
                'meta_title' => 'Berlin or Hamburg? Which City to Study in? (2026)',
                'meta_description' => 'Berlin vs Hamburg in Germany: subject, job market, rent (~€664 vs ~€626), living and character compared + a guide to which city fits you.',
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
            'berlin-vs-hamburg-which-city-to-study-in-germany',
            'berlin-vs-hamburg-which-city-to-study-in-germany-de',
            'berlin-vs-hamburg-which-city-to-study-in-germany-en',
        ])->delete();
    }
};

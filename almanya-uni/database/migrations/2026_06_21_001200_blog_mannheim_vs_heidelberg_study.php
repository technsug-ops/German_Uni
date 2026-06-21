<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Mannheim vs Heidelberg — okumak için hangi şehir? (karar çerçevesi).
 *
 * Veriler DB'den topraklandı: Mannheim (slug mannheim-q2119, pop ~314.000, warm30=502€,
 * Universität Mannheim = işletme/ekonomide Almanya #1, Hochschule Mannheim UAS); Heidelberg
 * (slug heidelberg-q2966, pop ~159.000, warm30=670€, Heidelberg Üni 1386 en eski/elit araştırma,
 * SRH, PH Heidelberg). İki komşu Rhein-Neckar şehri ~20 km arayla ama dünyalar kadar farklı.
 * Kazanan ilan ETMEZ; karar çerçevesi sunar. İlke: önce BÖLÜM, sonra şehir
 * ([[priority-university-programs]], city-vs-university blogu).
 *
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 * İç-linkler: şehir sayfaları çok-dilli (/cities/{slug}); city-vs-university & rent blogları
 * TR-only → DE/EN'de yalnızca şehir sayfalarına link verildi.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e6b';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Mannheim'da mı okusam, Heidelberg'de mi?" — Rhein-Neckar bölgesine gelmek isteyen öğrencilerin sık sorduğu sorulardan biri. İlginç olan: iki şehir birbirine sadece ~20 km uzaklıkta, ama atmosfer olarak dünyalar kadar farklı. Kısa ve dürüst cevap: **mutlak bir "kazanan" yok; doğru olan sana uyan.** Bu yazı sana bir kazanan dayatmaz; **kendi kararını verebilmen için bir çerçeve** sunar.

## Önce en önemli kural: şehir değil, bölüm
Şehri seçmeden önce şunu netleştir: **hangi şehirde senin programın güçlü?** İki şehir de harika olabilir ama senin bölümün birinde belirgin biçimde daha iyiyse, karar zaten verilmiş demektir. Şehir, programın *üstüne* eklenen bir konfor katmanıdır — tersi değil. (Detaylı tartışma: [Şehir mi Üniversite mi?](/tr/blog/city-vs-university-which-matters-more-in-germany))

Bu yüzden ilk adımın: bölümünü **her iki şehirde de** arayıp kabul şartlarını, dilini (Almanca/İngilizce) ve içeriğini karşılaştırmak.

## Mannheim: işletme ve ekonominin numara biri
- **Güçlü alanlar:** İşletme, ekonomi, sosyal bilimler, veri bilimi. **Universität Mannheim**, işletme ve ekonomide **Almanya'nın bir numarası** olarak gösterilir; ABD tarzı kampüsü görkemli bir barok sarayda yer alır. **Hochschule Mannheim** (UAS) mühendislikte uygulamalı.
- **İş piyasası:** Sanayi ve hizmet sektörü güçlü; büyük bir demiryolu kavşağı sayesinde bölgeye ulaşım kolay. İşletme/ekonomi ağı için kuvvetli bir konum.
- **Yaşam:** Izgara planlı (kareler/"Quadrate"), pratik ve sanayi havası olan bir şehir (~314.000 nüfus). Görkemli değil ama işlevsel ve canlı.
- **Maliyet:** Görece **uygun**. Öğrenci kirası ~**502 €** (warm, 30 m² referans). Bütçe için belirgin avantaj.

## Heidelberg: tarih, prestij ve elit araştırma
- **Güçlü alanlar:** Tıp, yaşam bilimleri/biyoteknoloji, araştırma, beşeri bilimler. **Heidelberg Üniversitesi** Almanya'nın **en eski üniversitesi** (1386) ve elit bir araştırma merkezi. **SRH University Heidelberg** (özel) ve **PH Heidelberg** (eğitim) tamamlayıcı seçenekler.
- **İş piyasası:** Güçlü araştırma ve biyoteknoloji ekosistemi; akademik/bilimsel kariyer için cazip. Sanayi çeşitliliği Mannheim kadar geniş değil.
- **Yaşam:** Neckar kıyısında romantik, tarihi bir turist şehri (~159.000 nüfus). Küçük, prestijli, son derece "Almanya kartpostalı" havasında.
- **Maliyet:** Mannheim'dan **pahalı**. Öğrenci kirası ~**670 €** (warm, 30 m²). Küçük ve popüler olması fiyatları yukarı çeker.

## Karşılaştırma tablosu
| Kriter | Mannheim | Heidelberg |
|---|---|---|
| Öne çıkan alanlar | İşletme, ekonomi, sosyal bilimler, veri bilimi | Tıp, yaşam bilimleri/biyoteknoloji, araştırma, beşeri bilimler |
| Büyük üniler | Universität Mannheim (#1 işletme/ekonomi), Hochschule Mannheim | Heidelberg Üni (1386, en eski/elit), SRH, PH Heidelberg |
| İş piyasası | Sanayi/hizmet + demiryolu kavşağı | Araştırma + biyoteknoloji ekosistemi |
| Öğrenci kirası (warm, 30 m²) | ~502 € | ~670 € |
| Nüfus | ~314.000 | ~159.000 |
| Atmosfer | Izgara planlı, pratik, sanayi, uygun | Romantik, tarihi, turistik, prestijli, pahalı |

## Peki sana hangisi uygun? (Hızlı karar rehberi)
- **İşletme / ekonomi / sosyal bilimler / veri bilimi** okuyacaksan → **Mannheim** (Almanya #1 işletme okulu).
- **Tıp / yaşam bilimleri / araştırma / beşeri bilimler** ilgi alanınsa → **Heidelberg** (elit araştırma + en eski üni).
- **Bütçe önceliğinse** → Mannheim kira açısından belirgin biçimde daha rahat (~502 € vs ~670 €).
- **Tarihi, romantik, küçük bir şehir** istiyorsan → Heidelberg.
- **Pratik, canlı, ulaşımı kolay büyük şehir** istiyorsan → Mannheim. (İkisi ~20 km arayla; birinde okuyup diğerinde yaşamak bile mümkün.)

## Sonuç
Mannheim "modern, uygun ve işletmede numara bir", Heidelberg "tarihi, elit araştırma ama daha pahalı ve küçük" demek — ama ikisi de yalnızca senin **programın** doğru olduğunda anlam taşır. Önce bölümünü iki şehirde de ara, sonra bu tabloyu kendi önceliklerinle doldur.

👉 İncele: [Mannheim şehir rehberi](/tr/cities/mannheim-q2119) · [Heidelberg şehir rehberi](/tr/cities/heidelberg-q2966) · [Almanya'da şehir şehir kira](/tr/blog/germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent). Kararsızsan bölümünü yaz, birlikte iki şehirde de değerlendirelim.

---
*Kira rakamları ~30 m² warm referansına dayanır ve değişir; üniversite/şehir verileri 2026 itibarıyladır. Başvurudan önce resmî kaynaktan teyit et.*
MD;

        $deBody = <<<'MD'
„Soll ich in Mannheim oder in Heidelberg studieren?" — eine häufige Frage angehender Studierender in der Rhein-Neckar-Region. Das Spannende: Beide Städte liegen nur ~20 km auseinander, sind aber von der Atmosphäre her grundverschieden. Die ehrliche Kurzantwort: **Es gibt keinen pauschalen „Sieger" — nur die Stadt, die zu dir passt.** Dieser Artikel drängt dir keinen Gewinner auf, sondern gibt dir einen **Rahmen für deine eigene Entscheidung.**

## Die wichtigste Regel zuerst: nicht die Stadt, sondern das Fach
Bevor du die Stadt wählst, kläre: **In welcher Stadt ist dein Studiengang stark?** Beide Städte können großartig sein — aber wenn dein Fach in einer davon deutlich besser ist, ist die Entscheidung praktisch gefallen. Die Stadt ist die Komfortebene *über* dem Studiengang, nicht umgekehrt.

Dein erster Schritt: Suche deinen Studiengang **in beiden Städten** und vergleiche Zulassung, Sprache (Deutsch/Englisch) und Inhalte.

## Mannheim: die Nummer eins für BWL und Wirtschaft
- **Starke Bereiche:** BWL, Wirtschaft, Sozialwissenschaften, Data Science. Die **Universität Mannheim** gilt als **Deutschlands Nummer eins** in BWL und Wirtschaft; ihr Campus im US-Stil liegt in einem barocken Schloss. Die **Hochschule Mannheim** (UAS) ist anwendungsnah im Ingenieurwesen.
- **Arbeitsmarkt:** Starke Industrie und Dienstleistung; als großer Bahnknotenpunkt sehr gut angebunden. Eine kräftige Lage für BWL-/Wirtschaftsnetzwerke.
- **Leben:** Eine in Quadraten angelegte, praktische Stadt mit Industriecharakter (~314.000 Einwohner). Nicht prunkvoll, aber funktional und lebendig.
- **Kosten:** Vergleichsweise **günstig**. Studentenmiete ca. **502 €** (warm, 30-m²-Referenz). Ein klarer Budgetvorteil.

## Heidelberg: Geschichte, Prestige und Spitzenforschung
- **Starke Bereiche:** Medizin, Life Sciences/Biotechnologie, Forschung, Geisteswissenschaften. Die **Universität Heidelberg** ist die **älteste Universität Deutschlands** (1386) und ein Elite-Forschungsstandort. Die **SRH University Heidelberg** (privat) und die **PH Heidelberg** (Lehramt) ergänzen das Angebot.
- **Arbeitsmarkt:** Starkes Forschungs- und Biotechnologie-Ökosystem; attraktiv für akademische/wissenschaftliche Karrieren. Die Branchenvielfalt ist nicht so breit wie in Mannheim.
- **Leben:** Eine romantische, historische Touristenstadt am Neckar (~159.000 Einwohner). Klein, prestigeträchtig, sehr „Postkarten-Deutschland".
- **Kosten:** Teurer als Mannheim. Studentenmiete ca. **670 €** (warm, 30 m²). Klein und beliebt zu sein, treibt die Preise nach oben.

## Vergleichstabelle
| Kriterium | Mannheim | Heidelberg |
|---|---|---|
| Herausragende Bereiche | BWL, Wirtschaft, Sozialwissenschaften, Data Science | Medizin, Life Sciences/Biotech, Forschung, Geisteswissenschaften |
| Große Hochschulen | Universität Mannheim (#1 BWL/Wirtschaft), Hochschule Mannheim | Uni Heidelberg (1386, älteste/Elite), SRH, PH Heidelberg |
| Arbeitsmarkt | Industrie/Dienstleistung + Bahnknoten | Forschung + Biotech-Ökosystem |
| Studentenmiete (warm, 30 m²) | ca. 502 € | ca. 670 € |
| Einwohner | ~314.000 | ~159.000 |
| Atmosphäre | Quadrate, praktisch, industriell, günstig | Romantisch, historisch, touristisch, prestigeträchtig, teuer |

## Welche Stadt passt zu dir? (Schnellentscheidung)
- **BWL / Wirtschaft / Sozialwissenschaften / Data Science** → **Mannheim** (Deutschlands #1 Business School).
- **Medizin / Life Sciences / Forschung / Geisteswissenschaften** → **Heidelberg** (Spitzenforschung + älteste Uni).
- **Budget zuerst** → Mannheim ist bei der Miete deutlich entspannter (~502 € vs. ~670 €).
- **Historische, romantische, kleine Stadt** → Heidelberg.
- **Praktische, lebendige, gut angebundene Großstadt** → Mannheim. (Beide ~20 km voneinander entfernt; in der einen studieren und in der anderen wohnen ist sogar möglich.)

## Fazit
Mannheim steht für „modern, günstig und die Nummer eins in BWL", Heidelberg für „historisch, Elite-Forschung, aber teurer und kleiner" — aber beide zählen nur, wenn dein **Studiengang** stimmt. Suche zuerst dein Fach in beiden Städten und fülle dann diese Tabelle mit deinen eigenen Prioritäten.

👉 Ansehen: [Stadtführer Mannheim](/de/cities/mannheim-q2119) · [Stadtführer Heidelberg](/de/cities/heidelberg-q2966). Unentschlossen? Nenne deinen Studiengang — wir bewerten beide Städte gemeinsam.

---
*Mietangaben beziehen sich auf eine 30-m²-Warmmiete-Referenz und variieren; Hochschul-/Stadtdaten Stand 2026. Vor der Bewerbung bei offiziellen Quellen bestätigen.*
MD;

        $enBody = <<<'MD'
"Should I study in Mannheim or Heidelberg?" — a common question for prospective students in the Rhein-Neckar region. The interesting part: the two cities are only ~20 km apart, yet worlds apart in atmosphere. The honest short answer: **there's no universal "winner" — only the city that fits you.** This article won't force a winner on you; it gives you a **framework to make your own decision.**

## The most important rule first: not the city, the subject
Before choosing the city, get clear on this: **in which city is your degree programme strong?** Both cities can be great — but if your subject is clearly better in one of them, the decision is essentially made. The city is the comfort layer *on top of* the programme, not the other way around.

So your first step: search for your programme **in both cities** and compare admission, language (German/English) and content.

## Mannheim: the number one for business and economics
- **Strong fields:** business administration, economics, social sciences, data science. The **University of Mannheim** is regarded as **Germany's number one** for business administration and economics; its US-style campus sits in a baroque palace. **Hochschule Mannheim** (UAS) is applied in engineering.
- **Job market:** strong industry and services; as a major rail hub it is very well connected. A powerful base for business/economics networks.
- **Living:** a grid-layout city (the "Quadrate"), practical and with an industrial feel (~314,000 population). Not grand, but functional and lively.
- **Cost:** comparatively **affordable**. Student rent ~**€502** (warm, 30 m² reference). A clear budget advantage.

## Heidelberg: history, prestige and elite research
- **Strong fields:** medicine, life sciences/biotech, research, humanities. **Heidelberg University** is **Germany's oldest university** (1386) and an elite research hub. **SRH University Heidelberg** (private) and **PH Heidelberg** (teacher training) round out the options.
- **Job market:** a strong research and biotech ecosystem; attractive for academic/scientific careers. Sector diversity is not as broad as in Mannheim.
- **Living:** a romantic, historic tourist town on the Neckar (~159,000 population). Small, prestigious, very "postcard Germany".
- **Cost:** more expensive than Mannheim. Student rent ~**€670** (warm, 30 m²). Being small and popular pushes prices up.

## Comparison table
| Criterion | Mannheim | Heidelberg |
|---|---|---|
| Standout fields | Business, economics, social sciences, data science | Medicine, life sciences/biotech, research, humanities |
| Big universities | University of Mannheim (#1 business/economics), Hochschule Mannheim | Heidelberg Uni (1386, oldest/elite), SRH, PH Heidelberg |
| Job market | Industry/services + rail hub | Research + biotech ecosystem |
| Student rent (warm, 30 m²) | ~€502 | ~€670 |
| Population | ~314,000 | ~159,000 |
| Atmosphere | Grid-layout, practical, industrial, affordable | Romantic, historic, touristy, prestigious, pricier |

## So which fits you? (Quick decision guide)
- Studying **business / economics / social sciences / data science** → **Mannheim** (Germany's #1 business school).
- Interested in **medicine / life sciences / research / humanities** → **Heidelberg** (elite research + oldest university).
- **Budget first** → Mannheim is clearly easier on rent (~€502 vs ~€670).
- **A historic, romantic, small town** → Heidelberg.
- **A practical, lively, well-connected city** → Mannheim. (The two are ~20 km apart; you could even study in one and live in the other.)

## Conclusion
Mannheim means "modern, affordable and the number one in business"; Heidelberg means "historic, elite research, but pricier and smaller" — but both only matter once your **programme** is right. Search your subject in both cities first, then fill in this table with your own priorities.

👉 Explore: [Mannheim city guide](/en/cities/mannheim-q2119) · [Heidelberg city guide](/en/cities/heidelberg-q2966). Undecided? Tell us your programme and we'll weigh both cities together.

---
*Rent figures refer to a 30 m² warm-rent reference and vary; university/city data as of 2026. Confirm with official sources before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'mannheim-vs-heidelberg-which-city-to-study-in-germany',
                'title' => 'Mannheim mi Heidelberg mi? Almanya\'da Okumak İçin Hangi Şehir? (2026)',
                'excerpt' => 'Mannheim mi Heidelberg mi? Kazanan ilan etmeyen, karar çerçevesi sunan rehber: önce bölüm sonra şehir ilkesi, Mannheim (işletme/ekonomide Almanya #1, ~502€ kira) vs Heidelberg (en eski üni/tıp/araştırma, ~670€ kira), ~20 km arayla iki komşu şehir, iş piyasası ve yaşam karşılaştırması + "sana hangisi uygun" hızlı karar rehberi.',
                'meta_title' => 'Mannheim mi Heidelberg mi? Okumak İçin Hangi Şehir? (2026)',
                'meta_description' => 'Almanya\'da Mannheim vs Heidelberg: bölüm, iş piyasası, kira (~502€ vs ~670€), yaşam ve atmosfer karşılaştırması + hangi şehrin sana uygun olduğuna dair karar rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'mannheim-vs-heidelberg-which-city-to-study-in-germany-de',
                'title' => 'Mannheim oder Heidelberg? Welche Stadt zum Studium in Deutschland? (2026)',
                'excerpt' => 'Mannheim oder Heidelberg? Ein Leitfaden ohne pauschalen Sieger, mit Entscheidungsrahmen: erst das Fach, dann die Stadt, Mannheim (#1 in BWL/Wirtschaft, ~502€ Miete) vs Heidelberg (älteste Uni/Medizin/Forschung, ~670€ Miete), zwei Nachbarstädte ~20 km auseinander, Arbeitsmarkt und Leben im Vergleich + Schnellentscheidung „welche passt zu dir".',
                'meta_title' => 'Mannheim oder Heidelberg? Welche Stadt zum Studium? (2026)',
                'meta_description' => 'Mannheim vs Heidelberg in Deutschland: Fach, Arbeitsmarkt, Miete (~502€ vs ~670€), Leben und Atmosphäre im Vergleich + Entscheidungshilfe, welche Stadt zu dir passt.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'mannheim-vs-heidelberg-which-city-to-study-in-germany-en',
                'title' => 'Mannheim or Heidelberg? Which City to Study in, in Germany? (2026)',
                'excerpt' => 'Mannheim or Heidelberg? A no-winner, decision-framework guide: subject before city, Mannheim (#1 in business/economics, ~€502 rent) vs Heidelberg (oldest university/medicine/research, ~€670 rent), two neighbouring cities ~20 km apart, job market and living compared + a quick "which fits you" decision guide.',
                'meta_title' => 'Mannheim or Heidelberg? Which City to Study in? (2026)',
                'meta_description' => 'Mannheim vs Heidelberg in Germany: subject, job market, rent (~€502 vs ~€670), living and atmosphere compared + a guide to which city fits you.',
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
            'mannheim-vs-heidelberg-which-city-to-study-in-germany',
            'mannheim-vs-heidelberg-which-city-to-study-in-germany-de',
            'mannheim-vs-heidelberg-which-city-to-study-in-germany-en',
        ])->delete();
    }
};

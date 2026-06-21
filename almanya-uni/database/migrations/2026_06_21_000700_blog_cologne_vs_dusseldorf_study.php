<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Köln vs Düsseldorf — okumak için hangi şehir? (karar çerçevesi).
 *
 * Veriler DB'den topraklandı: Köln (slug koln-q365, ~1.085.000 nüfus, warm30=688€,
 * Uni zu Köln, TH Köln, Deutsche Sporthochschule); Düsseldorf (slug dusseldorf-q1718,
 * ~629.000 nüfus, warm30=557€, Heinrich-Heine-Uni, HS Düsseldorf). 40 km arayla iki
 * Ren komşusu. Kazanan ilan ETMEZ; karar çerçevesi sunar.
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
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e66';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Köln'de mi okusam, Düsseldorf'ta mı?" — Ren kıyısında, birbirine sadece 40 km uzaklıkta iki komşu şehir; öğrencilerin sürekli tarttığı bir ikilem. Kısa ve dürüst cevap: **mutlak bir "kazanan" yok; doğru olan sana uyan.** Bu yazı sana bir kazanan dayatmaz; **kendi kararını verebilmen için bir çerçeve** sunar.

## Önce en önemli kural: şehir değil, bölüm
Şehri seçmeden önce şunu netleştir: **hangi şehirde senin programın güçlü?** İki şehir de harika olabilir ama senin bölümün birinde belirgin biçimde daha iyiyse, karar zaten verilmiş demektir. Şehir, programın *üstüne* eklenen bir konfor katmanıdır — tersi değil. (Detaylı tartışma: [Şehir mi Üniversite mi?](/tr/blog/city-vs-university-which-matters-more-in-germany))

Bu yüzden ilk adımın: bölümünü **her iki şehirde de** arayıp kabul şartlarını, dilini (Almanca/İngilizce) ve içeriğini karşılaştırmak.

## Köln: canlı, açık ve yaratıcı büyük şehir
- **Güçlü alanlar:** İşletme, ekonomi, hukuk, medya ve yayıncılık, sigortacılık. **Universität zu Köln** ekonomi/işletme/hukukta güçlü; **TH Köln** uygulamalı (UAS); **Deutsche Sporthochschule Köln** spor biliminde Almanya'nın referansı.
- **İş piyasası:** RTL gibi büyük yayıncılarla **medya ve yayıncılığın** merkezi; yaratıcı sektörler, işletme ve sigortacılık güçlü. Staj ve ağ için geniş bir oyun alanı.
- **Yaşam:** Canlı, açık fikirli, karnavalıyla ünlü; Ren kıyısı, büyük şehir enerjisi, merkezi konum. ~**1.085.000** nüfusla bölgenin en büyük şehri.
- **Maliyet:** Öğrenci kirası ~**688 €** (warm, 30 m² referans). Düsseldorf'tan görece yüksek.

## Düsseldorf: şık, zarif ve iş odaklı
- **Güçlü alanlar:** Moda, reklam/pazarlama, finans, danışmanlık. **Heinrich-Heine-Universität Düsseldorf** tıp/hukuk/ekonomide güçlü; **Hochschule Düsseldorf** uygulamalı (UAS). Avrupa'nın önde gelen **Japon iş merkezlerinden** biri.
- **İş piyasası:** Moda ve reklamın başkenti; finans ve danışmanlık güçlü; büyük Japon iş topluluğu sayesinde uluslararası kariyer fırsatları.
- **Yaşam:** Şık, zarif, daha temiz ve daha küçük (~**629.000** nüfus). İş odaklı, derli toplu bir atmosfer; yine Ren kıyısı.
- **Maliyet:** Köln'e göre belirgin biçimde daha uygun. Öğrenci kirası ~**557 €** (warm, 30 m²).

## Karşılaştırma tablosu
| Kriter | Köln | Düsseldorf |
|---|---|---|
| Öne çıkan alanlar | Medya/yayıncılık, işletme, hukuk, sigorta, spor bilimi | Moda, reklam/pazarlama, finans, danışmanlık, tıp |
| Büyük üniler | Uni zu Köln, TH Köln, Sporthochschule | Heinrich-Heine-Uni, HS Düsseldorf |
| İş piyasası | Medya (RTL) + yaratıcı + sigorta | Moda/reklam + finans + Japon iş merkezi |
| Öğrenci kirası (warm, 30 m²) | ~688 € | ~557 € |
| Nüfus | ~1.085.000 | ~629.000 |
| Atmosfer | Canlı, açık, büyük şehir, karnaval | Şık, zarif, daha küçük, iş odaklı |

## Peki sana hangisi uygun? (Hızlı karar rehberi)
- **Medya / yayıncılık / yaratıcı sektör / spor bilimi** okuyacaksan → **Köln** (RTL + Sporthochschule).
- **Moda / reklam / finans / danışmanlık** ilgi alanınsa → **Düsseldorf** (sektör + Japon iş ağı).
- **Bütçe önceliğinse** → Düsseldorf kira açısından belirgin biçimde rahat; ama her iki şehirde de WG (ev arkadaşlığı) neredeyse şart.
- **Canlı, büyük şehir enerjisi** istiyorsan → Köln.
- **Şık, sakin ve derli toplu** bir ortam istiyorsan → Düsseldorf.

## Sonuç
Köln "canlı, açık, yaratıcı büyük şehir", Düsseldorf "şık, iş odaklı, daha sakin komşu" demek — ama ikisi de yalnızca senin **programın** doğru olduğunda anlam taşır. Üstelik 40 km arayla, birinde okurken diğerine günübirlik gitmek çok kolay. Önce bölümünü iki şehirde de ara, sonra bu tabloyu kendi önceliklerinle doldur.

👉 İncele: [Köln şehir rehberi](/tr/cities/koln-q365) · [Düsseldorf şehir rehberi](/tr/cities/dusseldorf-q1718) · [Almanya'da şehir şehir kira](/tr/blog/germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent). Kararsızsan bölümünü yaz, birlikte iki şehirde de değerlendirelim.

---
*Kira rakamları ~30 m² warm referansına dayanır ve değişir; üniversite/şehir verileri 2026 itibarıyladır. Başvurudan önce resmî kaynaktan teyit et.*
MD;

        $deBody = <<<'MD'
„Soll ich in Köln oder in Düsseldorf studieren?" — zwei Nachbarstädte am Rhein, nur 40 km voneinander entfernt, die Studierende ständig gegeneinander abwägen. Die ehrliche Kurzantwort: **Es gibt keinen pauschalen „Sieger" — nur die Stadt, die zu dir passt.** Dieser Artikel drängt dir keinen Gewinner auf, sondern gibt dir einen **Rahmen für deine eigene Entscheidung.**

## Die wichtigste Regel zuerst: nicht die Stadt, sondern das Fach
Bevor du die Stadt wählst, kläre: **In welcher Stadt ist dein Studiengang stark?** Beide Städte können großartig sein — aber wenn dein Fach in einer davon deutlich besser ist, ist die Entscheidung praktisch gefallen. Die Stadt ist die Komfortebene *über* dem Studiengang, nicht umgekehrt.

Dein erster Schritt: Suche deinen Studiengang **in beiden Städten** und vergleiche Zulassung, Sprache (Deutsch/Englisch) und Inhalte.

## Köln: lebendige, offene und kreative Großstadt
- **Starke Bereiche:** BWL, Wirtschaft, Jura, Medien und Rundfunk, Versicherungen. Die **Universität zu Köln** ist stark in Wirtschaft/BWL/Jura; die **TH Köln** anwendungsnah (UAS); die **Deutsche Sporthochschule Köln** ist die Referenz in der Sportwissenschaft.
- **Arbeitsmarkt:** Mit Sendern wie RTL ein Zentrum für **Medien und Rundfunk**; starke Kreativbranchen, BWL und Versicherungen. Ein weites Feld für Praktika und Netzwerke.
- **Leben:** Lebendig, weltoffen, berühmt für den Karneval; am Rhein, Großstadtenergie, zentrale Lage. Mit ca. **1.085.000** Einwohnern die größte Stadt der Region.
- **Kosten:** Studentenmiete ca. **688 €** (warm, 30-m²-Referenz). Im Vergleich zu Düsseldorf etwas höher.

## Düsseldorf: schick, elegant und businessorientiert
- **Starke Bereiche:** Mode, Werbung/Marketing, Finanzen, Beratung. Die **Heinrich-Heine-Universität Düsseldorf** ist stark in Medizin/Jura/Wirtschaft; die **Hochschule Düsseldorf** anwendungsnah (UAS). Einer der führenden **japanischen Wirtschaftsstandorte** Europas.
- **Arbeitsmarkt:** Hauptstadt der Mode und Werbung; starke Finanz- und Beratungsbranche; dank großer japanischer Geschäftswelt internationale Karrierechancen.
- **Leben:** Schick, elegant, sauberer und kleiner (~**629.000** Einwohner). Businessorientierte, aufgeräumte Atmosphäre; ebenfalls am Rhein.
- **Kosten:** Deutlich günstiger als Köln. Studentenmiete ca. **557 €** (warm, 30 m²).

## Vergleichstabelle
| Kriterium | Köln | Düsseldorf |
|---|---|---|
| Herausragende Bereiche | Medien/Rundfunk, BWL, Jura, Versicherung, Sportwissenschaft | Mode, Werbung/Marketing, Finanzen, Beratung, Medizin |
| Große Hochschulen | Uni zu Köln, TH Köln, Sporthochschule | Heinrich-Heine-Uni, HS Düsseldorf |
| Arbeitsmarkt | Medien (RTL) + Kreativ + Versicherung | Mode/Werbung + Finanzen + japanischer Standort |
| Studentenmiete (warm, 30 m²) | ca. 688 € | ca. 557 € |
| Einwohner | ~1.085.000 | ~629.000 |
| Atmosphäre | Lebendig, offen, Großstadt, Karneval | Schick, elegant, kleiner, businessorientiert |

## Welche Stadt passt zu dir? (Schnellentscheidung)
- **Medien / Rundfunk / Kreativbranche / Sportwissenschaft** → **Köln** (RTL + Sporthochschule).
- **Mode / Werbung / Finanzen / Beratung** → **Düsseldorf** (Branche + japanisches Netzwerk).
- **Budget zuerst** → Düsseldorf ist bei der Miete deutlich entspannter; eine WG ist in beiden Städten quasi Pflicht.
- **Lebendige Großstadtenergie** → Köln.
- **Schick, ruhig und aufgeräumt** → Düsseldorf.

## Fazit
Köln steht für „lebendige, offene, kreative Großstadt", Düsseldorf für „schicker, businessorientierter, ruhigerer Nachbar" — aber beide zählen nur, wenn dein **Studiengang** stimmt. Zudem sind es nur 40 km: Von der einen Stadt ist die andere ein leichter Tagesausflug. Suche zuerst dein Fach in beiden Städten und fülle dann diese Tabelle mit deinen eigenen Prioritäten.

👉 Ansehen: [Stadtführer Köln](/de/cities/koln-q365) · [Stadtführer Düsseldorf](/de/cities/dusseldorf-q1718). Unentschlossen? Nenne deinen Studiengang — wir bewerten beide Städte gemeinsam.

---
*Mietangaben beziehen sich auf eine 30-m²-Warmmiete-Referenz und variieren; Hochschul-/Stadtdaten Stand 2026. Vor der Bewerbung bei offiziellen Quellen bestätigen.*
MD;

        $enBody = <<<'MD'
"Should I study in Cologne or Düsseldorf?" — two neighbouring cities on the Rhine, just 40 km apart, that students constantly weigh against each other. The honest short answer: **there's no universal "winner" — only the city that fits you.** This article won't force a winner on you; it gives you a **framework to make your own decision.**

## The most important rule first: not the city, the subject
Before choosing the city, get clear on this: **in which city is your degree programme strong?** Both cities can be great — but if your subject is clearly better in one of them, the decision is essentially made. The city is the comfort layer *on top of* the programme, not the other way around.

So your first step: search for your programme **in both cities** and compare admission, language (German/English) and content.

## Cologne: lively, open-minded and creative big city
- **Strong fields:** business, economics, law, media and broadcasting, insurance. The **University of Cologne** is strong in economics/business/law; **TH Köln** is applied (UAS); the **German Sport University Cologne** is the national reference in sports science.
- **Job market:** with broadcasters like RTL, a hub for **media and broadcasting**; strong creative industries, business and insurance. A wide field for internships and networks.
- **Living:** lively, open-minded, famous for carnival; on the Rhine, big-city energy, a central location. With ~**1,085,000** inhabitants, the largest city in the region.
- **Cost:** student rent ~**€688** (warm, 30 m² reference). Somewhat higher than Düsseldorf.

## Düsseldorf: upscale, elegant and business-oriented
- **Strong fields:** fashion, advertising/marketing, finance, consulting. **Heinrich-Heine-Universität Düsseldorf** is strong in medicine/law/economics; **Hochschule Düsseldorf** is applied (UAS). One of Europe's leading **Japanese business hubs**.
- **Job market:** a capital of fashion and advertising; strong finance and consulting; international career opportunities thanks to a large Japanese business community.
- **Living:** upscale, elegant, cleaner and smaller (~**629,000** inhabitants). A business-oriented, tidy atmosphere; also on the Rhine.
- **Cost:** notably cheaper than Cologne. Student rent ~**€557** (warm, 30 m²).

## Comparison table
| Criterion | Cologne | Düsseldorf |
|---|---|---|
| Standout fields | Media/broadcasting, business, law, insurance, sports science | Fashion, advertising/marketing, finance, consulting, medicine |
| Big universities | Uni zu Köln, TH Köln, Sport University | Heinrich-Heine-Uni, HS Düsseldorf |
| Job market | Media (RTL) + creative + insurance | Fashion/advertising + finance + Japanese hub |
| Student rent (warm, 30 m²) | ~€688 | ~€557 |
| Population | ~1,085,000 | ~629,000 |
| Atmosphere | Lively, open, big-city, carnival | Upscale, elegant, smaller, business-focused |

## So which fits you? (Quick decision guide)
- Studying **media / broadcasting / creative industries / sports science** → **Cologne** (RTL + Sport University).
- Interested in **fashion / advertising / finance / consulting** → **Düsseldorf** (industry + Japanese network).
- **Budget first** → Düsseldorf is notably easier on rent; a shared flat (WG) is almost a must in both.
- **Lively big-city energy** → Cologne.
- **Upscale, calm and tidy** → Düsseldorf.

## Conclusion
Cologne means "lively, open, creative big city"; Düsseldorf means "an upscale, business-oriented, calmer neighbour" — but both only matter once your **programme** is right. And at just 40 km apart, the other city is an easy day trip from either one. Search your subject in both cities first, then fill in this table with your own priorities.

👉 Explore: [Cologne city guide](/en/cities/koln-q365) · [Düsseldorf city guide](/en/cities/dusseldorf-q1718). Undecided? Tell us your programme and we'll weigh both cities together.

---
*Rent figures refer to a 30 m² warm-rent reference and vary; university/city data as of 2026. Confirm with official sources before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'cologne-vs-dusseldorf-which-city-to-study-in-germany',
                'title' => 'Köln mü Düsseldorf mü? Almanya\'da Okumak İçin Hangi Şehir? (2026)',
                'excerpt' => 'Köln mü Düsseldorf mü? Kazanan ilan etmeyen, karar çerçevesi sunan rehber: önce bölüm sonra şehir ilkesi, Köln (medya/RTL/Sporthochschule, ~688€ kira) vs Düsseldorf (moda/Japon iş merkezi/finans, ~557€ kira), iş piyasası, yaşam ve atmosfer karşılaştırması + "sana hangisi uygun" hızlı karar rehberi.',
                'meta_title' => 'Köln mü Düsseldorf mü? Okumak İçin Hangi Şehir? (2026)',
                'meta_description' => 'Almanya\'da Köln vs Düsseldorf: bölüm, iş piyasası, kira (~688€ vs ~557€), yaşam ve atmosfer karşılaştırması + hangi şehrin sana uygun olduğuna dair karar rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'cologne-vs-dusseldorf-which-city-to-study-in-germany-de',
                'title' => 'Köln oder Düsseldorf? Welche Stadt zum Studium in Deutschland? (2026)',
                'excerpt' => 'Köln oder Düsseldorf? Ein Leitfaden ohne pauschalen Sieger, mit Entscheidungsrahmen: erst das Fach, dann die Stadt, Köln (Medien/RTL/Sporthochschule, ~688€ Miete) vs Düsseldorf (Mode/japanischer Standort/Finanzen, ~557€ Miete), Arbeitsmarkt, Leben und Atmosphäre im Vergleich + Schnellentscheidung „welche passt zu dir".',
                'meta_title' => 'Köln oder Düsseldorf? Welche Stadt zum Studium? (2026)',
                'meta_description' => 'Köln vs Düsseldorf in Deutschland: Fach, Arbeitsmarkt, Miete (~688€ vs ~557€), Leben und Atmosphäre im Vergleich + Entscheidungshilfe, welche Stadt zu dir passt.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'cologne-vs-dusseldorf-which-city-to-study-in-germany-en',
                'title' => 'Cologne or Düsseldorf? Which City to Study in, in Germany? (2026)',
                'excerpt' => 'Cologne or Düsseldorf? A no-winner, decision-framework guide: subject before city, Cologne (media/RTL/Sport University, ~€688 rent) vs Düsseldorf (fashion/Japanese hub/finance, ~€557 rent), job market, living and atmosphere compared + a quick "which fits you" decision guide.',
                'meta_title' => 'Cologne or Düsseldorf? Which City to Study in? (2026)',
                'meta_description' => 'Cologne vs Düsseldorf in Germany: subject, job market, rent (~€688 vs ~€557), living and atmosphere compared + a guide to which city fits you.',
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
            'cologne-vs-dusseldorf-which-city-to-study-in-germany',
            'cologne-vs-dusseldorf-which-city-to-study-in-germany-de',
            'cologne-vs-dusseldorf-which-city-to-study-in-germany-en',
        ])->delete();
    }
};

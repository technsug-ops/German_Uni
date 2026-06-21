<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Freiburg vs Tübingen — okumak için hangi şehir? (karar çerçevesi).
 *
 * Veriler DB'den topraklandı: Freiburg (slug freiburg-im-breisgau-q2833, ~236k nüfus,
 * warm30=644€, Uni Freiburg excellence/tıp/yaşam-çevre bilimleri, PH Freiburg); Tübingen
 * (slug tubingen-q3806, ~91k nüfus, warm30=526€, Uni Tübingen excellence/beşeri/tıp/sinir-
 * bilim/yapay zeka). Klasik Baden-Württemberg üniversite kasabaları. Kazanan ilan ETMEZ.
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
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e6d';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Freiburg'da mı okusam, Tübingen'de mi?" — klasik bir üniversite-kasabası deneyimi isteyen öğrencilerin sık sorduğu bir soru. İkisi de Baden-Württemberg'in efsanevi, araştırmada zirvedeki üniversite kentleri. Kısa ve dürüst cevap: **mutlak bir "kazanan" yok; doğru olan sana uyan.** Bu yazı sana bir kazanan dayatmaz; **kendi kararını verebilmen için bir çerçeve** sunar.

## Önce en önemli kural: şehir değil, bölüm
Şehri seçmeden önce şunu netleştir: **hangi şehirde senin programın güçlü?** İki şehir de mükemmel olabilir ama senin bölümün birinde belirgin biçimde daha iyiyse, karar zaten verilmiş demektir. Şehir, programın *üstüne* eklenen bir konfor katmanıdır — tersi değil. (Detaylı tartışma: [Şehir mi Üniversite mi?](/tr/blog/city-vs-university-which-matters-more-in-germany))

Bu yüzden ilk adımın: bölümünü **her iki şehirde de** arayıp kabul şartlarını, dilini (Almanca/İngilizce) ve içeriğini karşılaştırmak.

## Freiburg: Kara Orman'ın eşiğinde "yeşil" üniversite kenti
- **Güçlü alanlar:** Tıp, çevre ve sürdürülebilirlik bilimleri, yaşam bilimleri, beşeri bilimler. **Freiburg Üniversitesi** (Albert-Ludwigs, excellence düzeyinde araştırma) tıp, yaşam/çevre bilimleri ve beşeri bilimlerde güçlü; **PH Freiburg** öğretmenlik/eğitim odaklı.
- **Karakter:** Almanya'nın "Green City"si — çevre ve sürdürülebilirlikte öncü; güneş ışığı en bol Alman şehri olarak bilinir, Kara Orman'ın hemen kenarında.
- **Yaşam:** Canlı, öğrenci dolu ama Tübingen'den daha büyük (~236.000 nüfus); doğa, bisiklet ve açık hava kültürü güçlü.
- **Maliyet:** Bölge için görece pahalı. Öğrenci kirası ~**644 €** (warm, 30 m² referans).

## Tübingen: Neckar kıyısında minik, samimi üniversite kasabası
- **Güçlü alanlar:** Beşeri bilimler, tıp, sinirbilim ve yapay zeka / makine öğrenmesi araştırmaları. **Eberhard Karls Üniversitesi Tübingen** (excellence üniversitesi) beşeri bilimler ve tıpta köklü; sinirbilim ve YZ/ML'de Almanya'nın önemli araştırma merkezlerinden.
- **Karakter:** Klasik, masalsı bir üniversite kasabası — küçük, samimi, öğrenci ağırlıklı; nüfusun büyük kısmı öğrenci ve akademisyen.
- **Yaşam:** Çok küçük (~91.000 nüfus), Neckar kıyısında pitoresk eski şehir; her şeye yürüme mesafesi, yoğun "kampüs hayatı" hissi.
- **Maliyet:** Freiburg'a göre daha uygun. Öğrenci kirası ~**526 €** (warm, 30 m²).

## Karşılaştırma tablosu
| Kriter | Freiburg | Tübingen |
|---|---|---|
| Öne çıkan alanlar | Tıp, çevre/sürdürülebilirlik, yaşam bilimleri, beşeri bilimler | Beşeri bilimler, tıp, sinirbilim, YZ/ML |
| Büyük üniler | Uni Freiburg (Albert-Ludwigs), PH Freiburg | Uni Tübingen (Eberhard Karls) |
| Nüfus | ~236.000 | ~91.000 |
| Öğrenci kirası (warm, 30 m²) | ~644 € | ~526 € |
| Atmosfer | Yeşil, güneşli, canlı, Kara Orman kenarı | Minik, samimi, pitoresk, öğrenci ağırlıklı |
| Karakter | Daha büyük "yeşil kent" | Klasik küçük üniversite kasabası |

## Peki sana hangisi uygun? (Hızlı karar rehberi)
- **Tıp / çevre / sürdürülebilirlik / yaşam bilimleri** okuyacaksan → **Freiburg** (Green City + güçlü tıp ve çevre).
- **Beşeri bilimler / sinirbilim / yapay zeka / makine öğrenmesi** ilgi alanınsa → **Tübingen** (köklü beşeri bilimler + YZ araştırma merkezi).
- **Bütçe önceliğinse** → Tübingen kira açısından belirgin biçimde daha uygun; her iki kasabada da WG (ev arkadaşlığı) neredeyse şart.
- **Doğa, güneş, açık hava, biraz daha büyük şehir** istiyorsan → Freiburg.
- **Mümkün olan en samimi, yürünebilir "kampüs gibi şehir"** istiyorsan → Tübingen.

## Sonuç
Freiburg "yeşil, güneşli, biraz daha büyük üniversite kenti", Tübingen "minik, samimi, araştırma-yoğun klasik kasaba" demek — ama ikisi de yalnızca senin **programın** doğru olduğunda anlam taşır. Önce bölümünü iki şehirde de ara, sonra bu tabloyu kendi önceliklerinle doldur.

👉 İncele: [Freiburg şehir rehberi](/tr/cities/freiburg-im-breisgau-q2833) · [Tübingen şehir rehberi](/tr/cities/tubingen-q3806) · [Almanya'da şehir şehir kira](/tr/blog/germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent). Kararsızsan bölümünü yaz, birlikte iki kasabada da değerlendirelim.

---
*Kira rakamları ~30 m² warm referansına dayanır ve değişir; üniversite/şehir verileri 2026 itibarıyladır. Başvurudan önce resmî kaynaktan teyit et.*
MD;

        $deBody = <<<'MD'
„Soll ich in Freiburg oder in Tübingen studieren?" — eine häufige Frage von Studierenden, die das klassische Erlebnis einer Universitätsstadt suchen. Beide sind legendäre, forschungsstarke Universitätsstädte in Baden-Württemberg. Die ehrliche Kurzantwort: **Es gibt keinen pauschalen „Sieger" — nur die Stadt, die zu dir passt.** Dieser Artikel drängt dir keinen Gewinner auf, sondern gibt dir einen **Rahmen für deine eigene Entscheidung.**

## Die wichtigste Regel zuerst: nicht die Stadt, sondern das Fach
Bevor du die Stadt wählst, kläre: **In welcher Stadt ist dein Studiengang stark?** Beide Städte können großartig sein — aber wenn dein Fach in einer davon deutlich besser ist, ist die Entscheidung praktisch gefallen. Die Stadt ist die Komfortebene *über* dem Studiengang, nicht umgekehrt.

Dein erster Schritt: Suche deinen Studiengang **in beiden Städten** und vergleiche Zulassung, Sprache (Deutsch/Englisch) und Inhalte.

## Freiburg: „grüne" Universitätsstadt am Rand des Schwarzwalds
- **Starke Bereiche:** Medizin, Umwelt- und Nachhaltigkeitswissenschaften, Lebenswissenschaften, Geisteswissenschaften. Die **Universität Freiburg** (Albert-Ludwigs, Forschung auf Exzellenz-Niveau) ist stark in Medizin, Lebens-/Umweltwissenschaften und Geisteswissenschaften; die **PH Freiburg** ist auf Lehramt/Bildung ausgerichtet.
- **Charakter:** Deutschlands „Green City" — Vorreiterin in Umwelt und Nachhaltigkeit; gilt als sonnenreichste Stadt Deutschlands, direkt am Rand des Schwarzwalds.
- **Leben:** Lebendig, voller Studierender, aber größer als Tübingen (~236.000 Einwohner); starke Natur-, Fahrrad- und Outdoor-Kultur.
- **Kosten:** Für die Region vergleichsweise teuer. Studentenmiete ca. **644 €** (warm, 30-m²-Referenz).

## Tübingen: kleine, intime Universitätsstadt am Neckar
- **Starke Bereiche:** Geisteswissenschaften, Medizin, Neurowissenschaften und Forschung zu Künstlicher Intelligenz / maschinellem Lernen. Die **Eberhard Karls Universität Tübingen** (Exzellenzuniversität) ist traditionsreich in Geisteswissenschaften und Medizin; in Neurowissenschaften und KI/ML eines der wichtigsten Forschungszentren Deutschlands.
- **Charakter:** Eine klassische, malerische Universitätsstadt — klein, intim, von Studierenden geprägt; ein großer Teil der Bevölkerung studiert oder forscht.
- **Leben:** Sehr klein (~91.000 Einwohner), pittoreske Altstadt am Neckar; alles fußläufig, intensives „Campus-Leben"-Gefühl.
- **Kosten:** Günstiger als Freiburg. Studentenmiete ca. **526 €** (warm, 30 m²).

## Vergleichstabelle
| Kriterium | Freiburg | Tübingen |
|---|---|---|
| Herausragende Bereiche | Medizin, Umwelt/Nachhaltigkeit, Lebenswissenschaften, Geisteswissenschaften | Geisteswissenschaften, Medizin, Neurowissenschaften, KI/ML |
| Große Hochschulen | Uni Freiburg (Albert-Ludwigs), PH Freiburg | Uni Tübingen (Eberhard Karls) |
| Einwohner | ~236.000 | ~91.000 |
| Studentenmiete (warm, 30 m²) | ca. 644 € | ca. 526 € |
| Atmosphäre | Grün, sonnig, lebendig, am Schwarzwald | Klein, intim, malerisch, von Studierenden geprägt |
| Charakter | Größere „grüne Stadt" | Klassische kleine Universitätsstadt |

## Welche Stadt passt zu dir? (Schnellentscheidung)
- **Medizin / Umwelt / Nachhaltigkeit / Lebenswissenschaften** → **Freiburg** (Green City + starke Medizin und Umwelt).
- **Geisteswissenschaften / Neurowissenschaften / KI / maschinelles Lernen** → **Tübingen** (traditionsreiche Geisteswissenschaften + KI-Forschungszentrum).
- **Budget zuerst** → Tübingen ist bei der Miete deutlich günstiger; eine WG ist in beiden Städten quasi Pflicht.
- **Natur, Sonne, Outdoor, etwas größere Stadt** → Freiburg.
- **Möglichst intime, fußläufige „Stadt wie ein Campus"** → Tübingen.

## Fazit
Freiburg steht für „grüne, sonnige, etwas größere Universitätsstadt", Tübingen für „kleine, intime, forschungsintensive klassische Stadt" — aber beide zählen nur, wenn dein **Studiengang** stimmt. Suche zuerst dein Fach in beiden Städten und fülle dann diese Tabelle mit deinen eigenen Prioritäten.

👉 Ansehen: [Stadtführer Freiburg](/de/cities/freiburg-im-breisgau-q2833) · [Stadtführer Tübingen](/de/cities/tubingen-q3806). Unentschlossen? Nenne deinen Studiengang — wir bewerten beide Städte gemeinsam.

---
*Mietangaben beziehen sich auf eine 30-m²-Warmmiete-Referenz und variieren; Hochschul-/Stadtdaten Stand 2026. Vor der Bewerbung bei offiziellen Quellen bestätigen.*
MD;

        $enBody = <<<'MD'
"Should I study in Freiburg or Tübingen?" — a common question from students who want the classic university-town experience. Both are legendary, research-elite university towns in Baden-Württemberg. The honest short answer: **there's no universal "winner" — only the city that fits you.** This article won't force a winner on you; it gives you a **framework to make your own decision.**

## The most important rule first: not the city, the subject
Before choosing the city, get clear on this: **in which city is your degree programme strong?** Both towns can be great — but if your subject is clearly better in one of them, the decision is essentially made. The city is the comfort layer *on top of* the programme, not the other way around.

So your first step: search for your programme **in both towns** and compare admission, language (German/English) and content.

## Freiburg: a "green" university town at the edge of the Black Forest
- **Strong fields:** medicine, environmental and sustainability sciences, life sciences, humanities. The **University of Freiburg** (Albert-Ludwigs, excellence-level research) is strong in medicine, life/environmental sciences and humanities; **PH Freiburg** focuses on teacher training/education.
- **Character:** Germany's "Green City" — a pioneer in environment and sustainability; known as the sunniest city in Germany, right at the edge of the Black Forest.
- **Living:** lively, full of students, but bigger than Tübingen (~236,000 population); strong nature, cycling and outdoor culture.
- **Cost:** relatively pricey for the region. Student rent ~**€644** (warm, 30 m² reference).

## Tübingen: a tiny, intimate university town on the Neckar
- **Strong fields:** humanities, medicine, neuroscience and artificial intelligence / machine-learning research. The **Eberhard Karls Universität Tübingen** (excellence university) is rich in tradition in humanities and medicine; in neuroscience and AI/ML it is one of Germany's leading research hubs.
- **Character:** a classic, picturesque university town — small, intimate, student-dominated; a large share of the population studies or researches.
- **Living:** very small (~91,000 population), a picturesque old town on the Neckar; everything within walking distance, an intense "campus-life" feel.
- **Cost:** cheaper than Freiburg. Student rent ~**€526** (warm, 30 m²).

## Comparison table
| Criterion | Freiburg | Tübingen |
|---|---|---|
| Standout fields | Medicine, environment/sustainability, life sciences, humanities | Humanities, medicine, neuroscience, AI/ML |
| Big universities | Uni Freiburg (Albert-Ludwigs), PH Freiburg | Uni Tübingen (Eberhard Karls) |
| Population | ~236,000 | ~91,000 |
| Student rent (warm, 30 m²) | ~€644 | ~€526 |
| Atmosphere | Green, sunny, lively, edge of the Black Forest | Tiny, intimate, picturesque, student-dominated |
| Character | Larger "green city" | Classic small university town |

## So which fits you? (Quick decision guide)
- Studying **medicine / environment / sustainability / life sciences** → **Freiburg** (Green City + strong medicine and environment).
- Interested in **humanities / neuroscience / AI / machine learning** → **Tübingen** (deep-rooted humanities + an AI research hub).
- **Budget first** → Tübingen is clearly cheaper on rent; a shared flat (WG) is almost a must in both.
- **Nature, sun, outdoors, a slightly bigger city** → Freiburg.
- **The most intimate, walkable "city like a campus"** → Tübingen.

## Conclusion
Freiburg means "a green, sunny, slightly bigger university town"; Tübingen means "a tiny, intimate, research-intensive classic town" — but both only matter once your **programme** is right. Search your subject in both towns first, then fill in this table with your own priorities.

👉 Explore: [Freiburg city guide](/en/cities/freiburg-im-breisgau-q2833) · [Tübingen city guide](/en/cities/tubingen-q3806). Undecided? Tell us your programme and we'll weigh both towns together.

---
*Rent figures refer to a 30 m² warm-rent reference and vary; university/city data as of 2026. Confirm with official sources before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'freiburg-vs-tubingen-which-city-to-study-in-germany',
                'title' => 'Freiburg mu Tübingen mi? Almanya\'da Okumak İçin Hangi Şehir? (2026)',
                'excerpt' => 'Freiburg mu Tübingen mi? Kazanan ilan etmeyen, karar çerçevesi sunan rehber: önce bölüm sonra şehir ilkesi, Freiburg (yeşil/tıp/çevre, ~644€ kira) vs Tübingen (beşeri bilimler/sinirbilim/YZ, ~526€ kira), atmosfer, yaşam ve maliyet karşılaştırması + "sana hangisi uygun" hızlı karar rehberi.',
                'meta_title' => 'Freiburg mu Tübingen mi? Okumak İçin Hangi Şehir? (2026)',
                'meta_description' => 'Almanya\'da Freiburg vs Tübingen: bölüm, atmosfer, kira (~644€ vs ~526€), yaşam ve karakter karşılaştırması + hangi üniversite kasabasının sana uygun olduğuna dair karar rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'freiburg-vs-tubingen-which-city-to-study-in-germany-de',
                'title' => 'Freiburg oder Tübingen? Welche Stadt zum Studium in Deutschland? (2026)',
                'excerpt' => 'Freiburg oder Tübingen? Ein Leitfaden ohne pauschalen Sieger, mit Entscheidungsrahmen: erst das Fach, dann die Stadt, Freiburg (grün/Medizin/Umwelt, ~644€ Miete) vs Tübingen (Geisteswissenschaften/Neuro/KI, ~526€ Miete), Atmosphäre, Leben und Kosten im Vergleich + Schnellentscheidung „welche passt zu dir".',
                'meta_title' => 'Freiburg oder Tübingen? Welche Stadt zum Studium? (2026)',
                'meta_description' => 'Freiburg vs Tübingen in Deutschland: Fach, Atmosphäre, Miete (~644€ vs ~526€), Leben und Charakter im Vergleich + Entscheidungshilfe, welche Universitätsstadt zu dir passt.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'freiburg-vs-tubingen-which-city-to-study-in-germany-en',
                'title' => 'Freiburg or Tübingen? Which City to Study in, in Germany? (2026)',
                'excerpt' => 'Freiburg or Tübingen? A no-winner, decision-framework guide: subject before city, Freiburg (green/medicine/environment, ~€644 rent) vs Tübingen (humanities/neuroscience/AI, ~€526 rent), atmosphere, living and cost compared + a quick "which fits you" decision guide.',
                'meta_title' => 'Freiburg or Tübingen? Which City to Study in? (2026)',
                'meta_description' => 'Freiburg vs Tübingen in Germany: subject, atmosphere, rent (~€644 vs ~€526), living and character compared + a guide to which university town fits you.',
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
            'freiburg-vs-tubingen-which-city-to-study-in-germany',
            'freiburg-vs-tubingen-which-city-to-study-in-germany-de',
            'freiburg-vs-tubingen-which-city-to-study-in-germany-en',
        ])->delete();
    }
};

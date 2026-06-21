<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Dresden vs Leipzig — okumak için hangi şehir? (karar çerçevesi).
 *
 * Veriler DB'den topraklandı: Dresden (slug dresden-q1731, ~563.000 nüfus, warm30~499€,
 * TU Dresden mükemmeliyet/mikroelektronik, HTW Dresden); Leipzig (slug leipzig-q2079,
 * ~624.000 nüfus, warm30~442€ — büyük şehirler içinde en ucuzlardan, Uni Leipzig geniş,
 * HTWK Leipzig). Doğu Almanya, uygun fiyat teması. Kazanan ilan ETMEZ; karar çerçevesi sunar.
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
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e6a';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Dresden'de mi okusam, Leipzig'de mi?" — özellikle bütçesini düşünen, parasının karşılığını arayan uluslararası öğrencilerin sık sorduğu bir soru. Kısa ve dürüst cevap: **mutlak bir "kazanan" yok; doğru olan sana uyan.** Bu yazı sana bir kazanan dayatmaz; **kendi kararını verebilmen için bir çerçeve** sunar.

## Önce en önemli kural: şehir değil, bölüm
Şehri seçmeden önce şunu netleştir: **hangi şehirde senin programın güçlü?** İki şehir de uygun fiyatlı ve cazip olabilir ama senin bölümün birinde belirgin biçimde daha iyiyse, karar zaten verilmiş demektir. Şehir, programın *üstüne* eklenen bir konfor katmanıdır — tersi değil. (Detaylı tartışma: [Şehir mi Üniversite mi?](/tr/blog/city-vs-university-which-matters-more-in-germany))

Bu yüzden ilk adımın: bölümünü **her iki şehirde de** arayıp kabul şartlarını, dilini (Almanca/İngilizce) ve içeriğini karşılaştırmak.

## Dresden: mühendislik, mikroelektronik ve barok güzelliği
- **Güçlü alanlar:** Mikroelektronik & yarı iletkenler ("Silicon Saxony"), mühendislik, araştırma. **TU Dresden** bir mükemmeliyet üniversitesi (Exzellenzuniversität), TU9 seviyesinde mühendislik ve mikroelektronikte Almanya'nın önde gelenlerinden; **HTW Dresden** uygulamalı (UAS).
- **İş piyasası:** Avrupa'nın en büyük mikroçip kümesi burada; yarı iletken, çip ve teknoloji firmaları için staj ve iş ağı güçlü.
- **Yaşam:** Barok mimari, Elbe kıyısı, zengin kültür ve sanat sahnesi — son derece estetik bir şehir. Doğu Almanya, ~563.000 nüfus.
- **Maliyet:** Çok uygun. Öğrenci kirası ~**499 €** (warm, 30 m² referans) — batı metropollerine göre belirgin avantaj.

## Leipzig: geniş üniversite, genç ve yaratıcı sahne
- **Güçlü alanlar:** Sosyal/beşerî bilimler, tıp, hukuk, doğa bilimleri, medya. **Universität Leipzig** çok kapsamlı ve köklü; **HTWK Leipzig** uygulamalı (UAS). Yaratıcı/kültürel sektörler ve medya güçlü.
- **İş piyasası:** Hızla büyüyen start-up ve yaratıcı ekonomi sahnesi, medya ve kültür kurumları; canlı ve genç bir iş ortamı.
- **Yaşam:** Genç, yaratıcı ve enerjik — "Hypezig" lakaplı, sanat ve gece hayatıyla canlı. Doğu Almanya, ~624.000 nüfus.
- **Maliyet:** En güçlü kozu. Öğrenci kirası ~**442 €** (warm, 30 m²) — büyük şehirler içinde **en ucuzlardan**.

## Karşılaştırma tablosu
| Kriter | Dresden | Leipzig |
|---|---|---|
| Öne çıkan alanlar | Mikroelektronik, mühendislik, araştırma | Beşerî bilimler, tıp, hukuk, medya, sanat |
| Büyük üniler | TU Dresden, HTW Dresden | Uni Leipzig, HTWK Leipzig |
| İş piyasası | Yarı iletken / "Silicon Saxony" | Medya + yaratıcı ekonomi + start-up |
| Öğrenci kirası (warm, 30 m²) | ~499 € | ~442 € |
| Nüfus | ~563.000 | ~624.000 |
| Atmosfer | Barok, kültürel, estetik | Genç, yaratıcı, canlı ("Hypezig") |

## Peki sana hangisi uygun? (Hızlı karar rehberi)
- **Mühendislik / mikroelektronik / yarı iletken / teknik araştırma** okuyacaksan → **Dresden** (TU Dresden + Silicon Saxony).
- **Beşerî bilimler / tıp / hukuk / medya / yaratıcı alanlar** ilgi alanınsa → **Leipzig** (geniş üni + canlı sahne).
- **Bütçe en büyük önceliğinse** → her ikisi de çok uygun; Leipzig kirada bir tık daha ucuz.
- **Kültürel güzellik ve estetik şehir** istiyorsan → Dresden (barok mimari, Elbe).
- **Genç, hareketli, yaratıcı atmosfer** istiyorsan → Leipzig.

## Sonuç
Dresden "teknik mükemmeliyet ve barok güzellik", Leipzig "genç, yaratıcı ve daha da ucuz" demek — ama ikisi de yalnızca senin **programın** doğru olduğunda anlam taşır. Her ikisi de Doğu Almanya'nın paranın karşılığını veren cazip şehirleri; önce bölümünü iki şehirde de ara, sonra bu tabloyu kendi önceliklerinle doldur.

👉 İncele: [Dresden şehir rehberi](/tr/cities/dresden-q1731) · [Leipzig şehir rehberi](/tr/cities/leipzig-q2079) · [Almanya'da şehir şehir kira](/tr/blog/germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent). Kararsızsan bölümünü yaz, birlikte iki şehirde de değerlendirelim.

---
*Kira rakamları ~30 m² warm referansına dayanır ve değişir; üniversite/şehir verileri 2026 itibarıyladır. Başvurudan önce resmî kaynaktan teyit et.*
MD;

        $deBody = <<<'MD'
„Soll ich in Dresden oder in Leipzig studieren?" — eine häufige Frage besonders bei internationalen Studierenden, die aufs Budget achten und ihr Geld optimal einsetzen wollen. Die ehrliche Kurzantwort: **Es gibt keinen pauschalen „Sieger" — nur die Stadt, die zu dir passt.** Dieser Artikel drängt dir keinen Gewinner auf, sondern gibt dir einen **Rahmen für deine eigene Entscheidung.**

## Die wichtigste Regel zuerst: nicht die Stadt, sondern das Fach
Bevor du die Stadt wählst, kläre: **In welcher Stadt ist dein Studiengang stark?** Beide Städte können günstig und attraktiv sein — aber wenn dein Fach in einer davon deutlich besser ist, ist die Entscheidung praktisch gefallen. Die Stadt ist die Komfortebene *über* dem Studiengang, nicht umgekehrt.

Dein erster Schritt: Suche deinen Studiengang **in beiden Städten** und vergleiche Zulassung, Sprache (Deutsch/Englisch) und Inhalte.

## Dresden: Ingenieurwesen, Mikroelektronik und barocke Schönheit
- **Starke Bereiche:** Mikroelektronik & Halbleiter („Silicon Saxony"), Ingenieurwesen, Forschung. Die **TU Dresden** ist eine Exzellenzuniversität, auf TU9-Niveau im Ingenieurwesen und führend in der Mikroelektronik; die **HTW Dresden** ist anwendungsnah (UAS).
- **Arbeitsmarkt:** Hier liegt Europas größtes Mikrochip-Cluster; starkes Netzwerk für Praktika und Jobs bei Halbleiter-, Chip- und Technologiefirmen.
- **Leben:** Barocke Architektur, das Elbufer, eine reiche Kultur- und Kunstszene — eine sehr ästhetische Stadt. Ostdeutschland, ca. 563.000 Einwohner.
- **Kosten:** Sehr günstig. Studentenmiete ca. **499 €** (warm, 30-m²-Referenz) — ein klarer Vorteil gegenüber westdeutschen Metropolen.

## Leipzig: breite Universität, junge und kreative Szene
- **Starke Bereiche:** Geistes-/Sozialwissenschaften, Medizin, Jura, Naturwissenschaften, Medien. Die **Universität Leipzig** ist sehr breit aufgestellt und traditionsreich; die **HTWK Leipzig** ist anwendungsnah (UAS). Stark in Kreativ-/Kulturbranchen und Medien.
- **Arbeitsmarkt:** Schnell wachsende Start-up- und Kreativwirtschaft, Medien- und Kulturinstitutionen; ein lebendiges, junges Umfeld.
- **Leben:** Jung, kreativ und energiegeladen — als „Hypezig" bekannt, lebendig durch Kunst und Nachtleben. Ostdeutschland, ca. 624.000 Einwohner.
- **Kosten:** Sein größtes Plus. Studentenmiete ca. **442 €** (warm, 30 m²) — unter den **günstigsten** der großen Städte.

## Vergleichstabelle
| Kriterium | Dresden | Leipzig |
|---|---|---|
| Herausragende Bereiche | Mikroelektronik, Ingenieurwesen, Forschung | Geisteswissenschaften, Medizin, Jura, Medien, Kunst |
| Große Hochschulen | TU Dresden, HTW Dresden | Uni Leipzig, HTWK Leipzig |
| Arbeitsmarkt | Halbleiter / „Silicon Saxony" | Medien + Kreativwirtschaft + Start-ups |
| Studentenmiete (warm, 30 m²) | ca. 499 € | ca. 442 € |
| Einwohner | ca. 563.000 | ca. 624.000 |
| Atmosphäre | Barock, kulturell, ästhetisch | Jung, kreativ, lebendig („Hypezig") |

## Welche Stadt passt zu dir? (Schnellentscheidung)
- **Ingenieurwesen / Mikroelektronik / Halbleiter / technische Forschung** → **Dresden** (TU Dresden + Silicon Saxony).
- **Geisteswissenschaften / Medizin / Jura / Medien / kreative Fächer** → **Leipzig** (breite Uni + lebendige Szene).
- **Budget als oberste Priorität** → beide sehr günstig; Leipzig bei der Miete einen Tick billiger.
- **Kulturelle Schönheit und ästhetische Stadt** → Dresden (Barock, Elbe).
- **Junge, dynamische, kreative Atmosphäre** → Leipzig.

## Fazit
Dresden steht für „technische Exzellenz und barocke Schönheit", Leipzig für „jung, kreativ und noch günstiger" — aber beide zählen nur, wenn dein **Studiengang** stimmt. Beide sind attraktive ostdeutsche Städte mit hervorragendem Preis-Leistungs-Verhältnis; suche zuerst dein Fach in beiden Städten und fülle dann diese Tabelle mit deinen eigenen Prioritäten.

👉 Ansehen: [Stadtführer Dresden](/de/cities/dresden-q1731) · [Stadtführer Leipzig](/de/cities/leipzig-q2079). Unentschlossen? Nenne deinen Studiengang — wir bewerten beide Städte gemeinsam.

---
*Mietangaben beziehen sich auf eine 30-m²-Warmmiete-Referenz und variieren; Hochschul-/Stadtdaten Stand 2026. Vor der Bewerbung bei offiziellen Quellen bestätigen.*
MD;

        $enBody = <<<'MD'
"Should I study in Dresden or Leipzig?" — a common question, especially for budget-conscious international students looking for value for money. The honest short answer: **there's no universal "winner" — only the city that fits you.** This article won't force a winner on you; it gives you a **framework to make your own decision.**

## The most important rule first: not the city, the subject
Before choosing the city, get clear on this: **in which city is your degree programme strong?** Both cities can be affordable and appealing — but if your subject is clearly better in one of them, the decision is essentially made. The city is the comfort layer *on top of* the programme, not the other way around.

So your first step: search for your programme **in both cities** and compare admission, language (German/English) and content.

## Dresden: engineering, microelectronics and baroque beauty
- **Strong fields:** microelectronics & semiconductors ("Silicon Saxony"), engineering, research. **TU Dresden** is a University of Excellence, at TU9 level in engineering and a leader in microelectronics; **HTW Dresden** is applied (UAS).
- **Job market:** home to Europe's largest microchip cluster; a strong network for internships and jobs at semiconductor, chip and technology firms.
- **Living:** baroque architecture, the Elbe riverside, a rich culture and arts scene — a very aesthetic city. Eastern Germany, ~563,000 inhabitants.
- **Cost:** very affordable. Student rent ~**€499** (warm, 30 m² reference) — a clear advantage over western metropolises.

## Leipzig: broad university, young and creative scene
- **Strong fields:** humanities/social sciences, medicine, law, natural sciences, media. **Universität Leipzig** is very broad and long-established; **HTWK Leipzig** is applied (UAS). Strong in creative/cultural sectors and media.
- **Job market:** a fast-growing start-up and creative economy, media and cultural institutions; a lively, young environment.
- **Living:** young, creative and energetic — nicknamed "Hypezig", lively with art and nightlife. Eastern Germany, ~624,000 inhabitants.
- **Cost:** its biggest draw. Student rent ~**€442** (warm, 30 m²) — among the **cheapest** of the big cities.

## Comparison table
| Criterion | Dresden | Leipzig |
|---|---|---|
| Standout fields | Microelectronics, engineering, research | Humanities, medicine, law, media, arts |
| Big universities | TU Dresden, HTW Dresden | Uni Leipzig, HTWK Leipzig |
| Job market | Semiconductors / "Silicon Saxony" | Media + creative economy + start-ups |
| Student rent (warm, 30 m²) | ~€499 | ~€442 |
| Population | ~563,000 | ~624,000 |
| Atmosphere | Baroque, cultural, aesthetic | Young, creative, lively ("Hypezig") |

## So which fits you? (Quick decision guide)
- Studying **engineering / microelectronics / semiconductors / technical research** → **Dresden** (TU Dresden + Silicon Saxony).
- Interested in **humanities / medicine / law / media / creative fields** → **Leipzig** (broad university + lively scene).
- **Budget first** → both are very affordable; Leipzig is a touch cheaper on rent.
- **Cultural beauty and an aesthetic city** → Dresden (baroque architecture, the Elbe).
- **A young, dynamic, creative vibe** → Leipzig.

## Conclusion
Dresden means "technical excellence and baroque beauty"; Leipzig means "young, creative and even cheaper" — but both only matter once your **programme** is right. Both are appealing eastern German cities offering great value for money; search your subject in both cities first, then fill in this table with your own priorities.

👉 Explore: [Dresden city guide](/en/cities/dresden-q1731) · [Leipzig city guide](/en/cities/leipzig-q2079). Undecided? Tell us your programme and we'll weigh both cities together.

---
*Rent figures refer to a 30 m² warm-rent reference and vary; university/city data as of 2026. Confirm with official sources before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'dresden-vs-leipzig-which-city-to-study-in-germany',
                'title' => 'Dresden mi Leipzig mi? Almanya\'da Okumak İçin Hangi Şehir? (2026)',
                'excerpt' => 'Dresden mi Leipzig mi? Kazanan ilan etmeyen, karar çerçevesi sunan rehber: önce bölüm sonra şehir ilkesi, Dresden (mühendislik/mikroelektronik, TU Dresden, ~499€ kira) vs Leipzig (geniş üni, genç yaratıcı sahne, ~442€ kira), iş piyasası ve yaşam karşılaştırması + "sana hangisi uygun" hızlı karar rehberi.',
                'meta_title' => 'Dresden mi Leipzig mi? Okumak İçin Hangi Şehir? (2026)',
                'meta_description' => 'Almanya\'da Dresden vs Leipzig: bölüm, iş piyasası, kira (~499€ vs ~442€), yaşam ve atmosfer karşılaştırması + hangi şehrin sana uygun olduğuna dair karar rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'dresden-vs-leipzig-which-city-to-study-in-germany-de',
                'title' => 'Dresden oder Leipzig? Welche Stadt zum Studium in Deutschland? (2026)',
                'excerpt' => 'Dresden oder Leipzig? Ein Leitfaden ohne pauschalen Sieger, mit Entscheidungsrahmen: erst das Fach, dann die Stadt, Dresden (Ingenieurwesen/Mikroelektronik, TU Dresden, ~499€ Miete) vs Leipzig (breite Uni, junge kreative Szene, ~442€ Miete), Arbeitsmarkt und Leben im Vergleich + Schnellentscheidung „welche passt zu dir".',
                'meta_title' => 'Dresden oder Leipzig? Welche Stadt zum Studium? (2026)',
                'meta_description' => 'Dresden vs Leipzig in Deutschland: Fach, Arbeitsmarkt, Miete (~499€ vs ~442€), Leben und Atmosphäre im Vergleich + Entscheidungshilfe, welche Stadt zu dir passt.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'dresden-vs-leipzig-which-city-to-study-in-germany-en',
                'title' => 'Dresden or Leipzig? Which City to Study in, in Germany? (2026)',
                'excerpt' => 'Dresden or Leipzig? A no-winner, decision-framework guide: subject before city, Dresden (engineering/microelectronics, TU Dresden, ~€499 rent) vs Leipzig (broad university, young creative scene, ~€442 rent), job market and living compared + a quick "which fits you" decision guide.',
                'meta_title' => 'Dresden or Leipzig? Which City to Study in? (2026)',
                'meta_description' => 'Dresden vs Leipzig in Germany: subject, job market, rent (~€499 vs ~€442), living and atmosphere compared + a guide to which city fits you.',
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
            'dresden-vs-leipzig-which-city-to-study-in-germany',
            'dresden-vs-leipzig-which-city-to-study-in-germany-de',
            'dresden-vs-leipzig-which-city-to-study-in-germany-en',
        ])->delete();
    }
};

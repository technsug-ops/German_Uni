<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Bochum vs Dortmund — okumak için hangi şehir? (karar çerçevesi).
 *
 * Veriler DB'den topraklandı: Bochum (slug bochum-q2103, nüfus ~365.000, warm30 ~368€ —
 * Almanya'nın en uygunlarından, RUB ~büyük kampüs + IT-güvenlik/mühendislik, Hochschule Bochum);
 * Dortmund (slug dortmund-q1295, nüfus ~593.000, kira: kesin rakam YOK → Ruhr ortalaması/uygun,
 * TU Dortmund mühendislik/bilgisayar/istatistik/lojistik, FH Dortmund). Kazanan ilan ETMEZ;
 * karar çerçevesi sunar. İlke: önce BÖLÜM, sonra şehir ([[priority-university-programs]]).
 * Tema: Ruhr bölgesi (Ruhrgebiet) — Almanya'nın en uygun öğrenci kuşağı, yoğun toplu taşıma,
 * post-endüstriyel canlanma.
 *
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 * İç-linkler: şehir sayfaları çok-dilli (/cities/{slug}); city-vs-university & rent blogları
 * TR-only → DE/EN'de yalnızca şehir sayfalarına link verildi.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e6e';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Bochum'da mı okusam, Dortmund'da mı?" — Ruhr bölgesinde okumak isteyen, bütçesini önemseyen öğrencilerin sıkça sorduğu bir soru. Kısa ve dürüst cevap: **mutlak bir "kazanan" yok; doğru olan sana uyan.** Bu yazı sana bir kazanan dayatmaz; **kendi kararını verebilmen için bir çerçeve** sunar.

## Önce en önemli kural: şehir değil, bölüm
Şehri seçmeden önce şunu netleştir: **hangi şehirde senin programın güçlü?** İki şehir de Ruhr bölgesinde, birbirine trenle yaklaşık 20 dakika mesafede; bu yüzden fark çoğu zaman şehirde değil, **bölümün hangi üniversitede daha güçlü olduğunda** saklı. Şehir, programın *üstüne* eklenen bir konfor katmanıdır — tersi değil. (Detaylı tartışma: [Şehir mi Üniversite mi?](/tr/blog/city-vs-university-which-matters-more-in-germany))

Bu yüzden ilk adımın: bölümünü **her iki şehirde de** arayıp kabul şartlarını, dilini (Almanca/İngilizce) ve içeriğini karşılaştırmak.

## Bochum: uygun kira ve geniş üniversite yelpazesi
- **Güçlü alanlar:** IT güvenliği, mühendislik, geniş bölüm yelpazesi. **Ruhr-Universität Bochum (RUB)** büyük bir kampüs üniversitesi; IT güvenliği ve mühendislikte özellikle güçlü. **Hochschule Bochum** uygulamalı (UAS).
- **İş piyasası:** Ruhr bölgesinin endüstriden teknolojiye dönüşümünün parçası; siber güvenlik ekosistemi öne çıkıyor.
- **Yaşam:** Tam bir öğrenci şehri — sade, samimi, gösterişsiz. Ruhr bölgesinin yoğun toplu taşıma ağıyla komşu şehirlere ulaşım çok kolay.
- **Maliyet:** Çok uygun. Öğrenci kirası ~**368 €** (warm, 30 m² referans) — Almanya'nın **en ucuz** kiralarından. Bütçeni en az zorlayan seçeneklerden.

## Dortmund: mühendislik, teknoloji ve futbol şehri
- **Güçlü alanlar:** Mühendislik, bilgisayar bilimleri, istatistik, lojistik. **TU Dortmund** bu alanlarda güçlü; **FH Dortmund** uygulamalı (UAS). Teknoloji parkıyla (Technologiepark) sektör bağı kuvvetli.
- **İş piyasası:** Lojistik ve teknoloji odaklı post-endüstriyel canlanmanın merkezlerinden; teknoloji parkı staj/iş ağı için avantaj.
- **Yaşam:** Ruhr bölgesinin büyük şehirlerinden (~593.000), futbolla (Borussia Dortmund) ünlü, canlı ve enerjik. Post-endüstriyel dönüşümün yüzü.
- **Maliyet:** Ruhr bölgesi ortalamasında — Almanya'nın **en uygun kira bölgelerinden** biri. Bochum gibi bütçe dostu.

## Karşılaştırma tablosu
| Kriter | Bochum | Dortmund |
|---|---|---|
| Öne çıkan alanlar | IT güvenliği, mühendislik, geniş yelpaze | Mühendislik, bilgisayar, istatistik, lojistik |
| Büyük üniler | RUB, Hochschule Bochum | TU Dortmund, FH Dortmund |
| İş piyasası | Siber güvenlik + mühendislik | Lojistik + teknoloji (Technologiepark) |
| Öğrenci kirası (warm, 30 m²) | ~368 € | Ruhr ortalaması — uygun |
| Atmosfer | Öğrenci ağırlıklı, sade, samimi | Büyük, futbolla ünlü, enerjik |
| Bölge ulaşımı | Ruhr ağına mükemmel bağlı | Ruhr ağına mükemmel bağlı |

## Peki sana hangisi uygun? (Hızlı karar rehberi)
- **IT güvenliği / mühendislik / geniş bir bölüm arayışı** varsa → **Bochum** (RUB + IT-güvenlik gücü).
- **Mühendislik / bilgisayar / istatistik / lojistik** ilgi alanınsa → **Dortmund** (TU Dortmund + teknoloji parkı).
- **Bütçe önceliğinse** → ikisi de çok uygun; Bochum ~368 € ile Almanya'nın en ucuzlarından, Dortmund da Ruhr ortalamasında dengeli.
- **Canlı, enerjik şehir / futbol kültürü** istiyorsan → Dortmund.
- **Sade, öğrenci ağırlıklı, samimi atmosfer** istiyorsan → Bochum.

## Sonuç
İki şehir de Ruhr bölgesinin bütçe dostu kalbinde ve trenle yalnızca ~20 dakika arayla; bu yüzden ikisi arasında seçim çoğu zaman **programın** hangi üniversitede daha güçlü olduğuna iner. Önce bölümünü iki şehirde de ara, sonra bu tabloyu kendi önceliklerinle doldur.

👉 İncele: [Bochum şehir rehberi](/tr/cities/bochum-q2103) · [Dortmund şehir rehberi](/tr/cities/dortmund-q1295) · [Almanya'da şehir şehir kira](/tr/blog/germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent). Kararsızsan bölümünü yaz, birlikte iki şehirde de değerlendirelim.

---
*Kira rakamları ~30 m² warm referansına dayanır ve değişir; üniversite/şehir verileri 2026 itibarıyladır. Başvurudan önce resmî kaynaktan teyit et.*
MD;

        $deBody = <<<'MD'
„Soll ich in Bochum oder in Dortmund studieren?" — eine häufige Frage budgetbewusster Studierender, die ins Ruhrgebiet wollen. Die ehrliche Kurzantwort: **Es gibt keinen pauschalen „Sieger" — nur die Stadt, die zu dir passt.** Dieser Artikel drängt dir keinen Gewinner auf, sondern gibt dir einen **Rahmen für deine eigene Entscheidung.**

## Die wichtigste Regel zuerst: nicht die Stadt, sondern das Fach
Bevor du die Stadt wählst, kläre: **In welcher Stadt ist dein Studiengang stark?** Beide Städte liegen im Ruhrgebiet und sind mit dem Zug nur etwa 20 Minuten voneinander entfernt; der Unterschied liegt daher meist nicht in der Stadt, sondern darin, **an welcher Hochschule dein Fach stärker ist.** Die Stadt ist die Komfortebene *über* dem Studiengang, nicht umgekehrt.

Dein erster Schritt: Suche deinen Studiengang **in beiden Städten** und vergleiche Zulassung, Sprache (Deutsch/Englisch) und Inhalte.

## Bochum: günstige Miete und breites Studienangebot
- **Starke Bereiche:** IT-Sicherheit, Ingenieurwesen, breites Fächerspektrum. Die **Ruhr-Universität Bochum (RUB)** ist eine große Campus-Universität; besonders stark in IT-Sicherheit und Ingenieurwesen. Die **Hochschule Bochum** ist anwendungsnah (UAS).
- **Arbeitsmarkt:** Teil des Wandels des Ruhrgebiets von der Industrie zur Technologie; das Cybersicherheits-Ökosystem sticht hervor.
- **Leben:** Eine echte Studierendenstadt — schlicht, bodenständig, unprätentiös. Dank des dichten Ruhr-Nahverkehrs sind Nachbarstädte sehr leicht erreichbar.
- **Kosten:** Sehr günstig. Studentenmiete ca. **368 €** (warm, 30-m²-Referenz) — eine der **günstigsten** Mieten Deutschlands.

## Dortmund: Ingenieurwesen, Technologie und Fußball
- **Starke Bereiche:** Ingenieurwesen, Informatik, Statistik, Logistik. Die **TU Dortmund** ist in diesen Bereichen stark; die **FH Dortmund** ist anwendungsnah (UAS). Enge Branchenanbindung über den Technologiepark.
- **Arbeitsmarkt:** Ein Zentrum der post-industriellen Erneuerung mit Fokus auf Logistik und Technologie; der Technologiepark ist ein Vorteil für Praktika/Netzwerke.
- **Leben:** Eine der größeren Städte des Ruhrgebiets (~593.000), berühmt für Fußball (Borussia Dortmund), lebendig und energiegeladen. Das Gesicht des post-industriellen Wandels.
- **Kosten:** Im Ruhr-Durchschnitt — eine der **günstigsten Mietregionen** Deutschlands. Wie Bochum budgetfreundlich.

## Vergleichstabelle
| Kriterium | Bochum | Dortmund |
|---|---|---|
| Herausragende Bereiche | IT-Sicherheit, Ingenieurwesen, breites Spektrum | Ingenieurwesen, Informatik, Statistik, Logistik |
| Große Hochschulen | RUB, Hochschule Bochum | TU Dortmund, FH Dortmund |
| Arbeitsmarkt | Cybersicherheit + Ingenieurwesen | Logistik + Technologie (Technologiepark) |
| Studentenmiete (warm, 30 m²) | ca. 368 € | Ruhr-Durchschnitt — günstig |
| Atmosphäre | Studentisch, schlicht, bodenständig | Groß, fußballberühmt, energiegeladen |
| Regionale Anbindung | Hervorragend ans Ruhr-Netz angebunden | Hervorragend ans Ruhr-Netz angebunden |

## Welche Stadt passt zu dir? (Schnellentscheidung)
- **IT-Sicherheit / Ingenieurwesen / breites Fächerangebot** → **Bochum** (RUB + IT-Sicherheits-Stärke).
- **Ingenieurwesen / Informatik / Statistik / Logistik** → **Dortmund** (TU Dortmund + Technologiepark).
- **Budget zuerst** → beide sehr günstig; Bochum mit ~368 € unter den günstigsten Deutschlands, Dortmund im Ruhr-Durchschnitt ausgewogen.
- **Lebendige, energiegeladene Stadt / Fußballkultur** → Dortmund.
- **Schlichte, studentische, bodenständige Atmosphäre** → Bochum.

## Fazit
Beide Städte liegen im budgetfreundlichen Herzen des Ruhrgebiets und sind mit dem Zug nur ~20 Minuten voneinander entfernt; die Wahl läuft daher meist darauf hinaus, an welcher Hochschule dein **Studiengang** stärker ist. Suche zuerst dein Fach in beiden Städten und fülle dann diese Tabelle mit deinen eigenen Prioritäten.

👉 Ansehen: [Stadtführer Bochum](/de/cities/bochum-q2103) · [Stadtführer Dortmund](/de/cities/dortmund-q1295). Unentschlossen? Nenne deinen Studiengang — wir bewerten beide Städte gemeinsam.

---
*Mietangaben beziehen sich auf eine 30-m²-Warmmiete-Referenz und variieren; Hochschul-/Stadtdaten Stand 2026. Vor der Bewerbung bei offiziellen Quellen bestätigen.*
MD;

        $enBody = <<<'MD'
"Should I study in Bochum or Dortmund?" — a common question among budget-minded students heading to the Ruhr region. The honest short answer: **there's no universal "winner" — only the city that fits you.** This article won't force a winner on you; it gives you a **framework to make your own decision.**

## The most important rule first: not the city, the subject
Before choosing the city, get clear on this: **in which city is your degree programme strong?** Both cities sit in the Ruhr region and are only about 20 minutes apart by train; so the difference usually isn't the city but **which university your subject is stronger at.** The city is the comfort layer *on top of* the programme, not the other way around.

So your first step: search for your programme **in both cities** and compare admission, language (German/English) and content.

## Bochum: affordable rent and a broad subject range
- **Strong fields:** IT security, engineering, a broad subject range. **Ruhr-Universität Bochum (RUB)** is a large campus university; especially strong in IT security and engineering. **Hochschule Bochum** is applied (UAS).
- **Job market:** part of the Ruhr region's shift from industry to technology; the cybersecurity ecosystem stands out.
- **Living:** a genuine student city — down-to-earth, friendly, unpretentious. Thanks to the dense Ruhr public transport, neighbouring cities are very easy to reach.
- **Cost:** very affordable. Student rent ~**€368** (warm, 30 m² reference) — one of the **cheapest** rents in Germany.

## Dortmund: engineering, technology and football
- **Strong fields:** engineering, computer science, statistics, logistics. **TU Dortmund** is strong in these areas; **FH Dortmund** is applied (UAS). Close industry ties through the Technologiepark (tech park).
- **Job market:** a centre of post-industrial revival focused on logistics and technology; the tech park is an advantage for internships/networks.
- **Living:** one of the larger Ruhr cities (~593,000), famous for football (Borussia Dortmund), lively and energetic. The face of the post-industrial revival.
- **Cost:** in the Ruhr average — one of the **most affordable rent regions** in Germany. Budget-friendly like Bochum.

## Comparison table
| Criterion | Bochum | Dortmund |
|---|---|---|
| Standout fields | IT security, engineering, broad range | Engineering, computer science, statistics, logistics |
| Big universities | RUB, Hochschule Bochum | TU Dortmund, FH Dortmund |
| Job market | Cybersecurity + engineering | Logistics + technology (tech park) |
| Student rent (warm, 30 m²) | ~€368 | Ruhr average — affordable |
| Atmosphere | Student-heavy, down-to-earth, friendly | Large, football-famous, energetic |
| Regional transport | Excellently linked to the Ruhr network | Excellently linked to the Ruhr network |

## So which fits you? (Quick decision guide)
- Studying **IT security / engineering / a broad subject range** → **Bochum** (RUB + IT-security strength).
- Interested in **engineering / computer science / statistics / logistics** → **Dortmund** (TU Dortmund + tech park).
- **Budget first** → both very affordable; Bochum at ~€368 is among Germany's cheapest, Dortmund balanced at the Ruhr average.
- **Lively, energetic city / football culture** → Dortmund.
- **Down-to-earth, student-heavy, friendly atmosphere** → Bochum.

## Conclusion
Both cities sit in the budget-friendly heart of the Ruhr region and are only ~20 minutes apart by train; so the choice usually comes down to which university your **programme** is stronger at. Search your subject in both cities first, then fill in this table with your own priorities.

👉 Explore: [Bochum city guide](/en/cities/bochum-q2103) · [Dortmund city guide](/en/cities/dortmund-q1295). Undecided? Tell us your programme and we'll weigh both cities together.

---
*Rent figures refer to a 30 m² warm-rent reference and vary; university/city data as of 2026. Confirm with official sources before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'bochum-vs-dortmund-which-city-to-study-in-germany',
                'title' => 'Bochum mu Dortmund mu? Almanya\'da Okumak İçin Hangi Şehir? (2026)',
                'excerpt' => 'Bochum mu Dortmund mu? Kazanan ilan etmeyen, karar çerçevesi sunan rehber: önce bölüm sonra şehir ilkesi, Ruhr bölgesinde Bochum (IT-güvenlik/RUB, ~368€ kira) vs Dortmund (mühendislik/TU Dortmund/lojistik, Ruhr ortalaması uygun kira), iş piyasası, yaşam ve bölge ulaşımı karşılaştırması + "sana hangisi uygun" hızlı karar rehberi.',
                'meta_title' => 'Bochum mu Dortmund mu? Okumak İçin Hangi Şehir? (2026)',
                'meta_description' => 'Almanya\'da Bochum vs Dortmund: Ruhr bölgesi, bölüm, iş piyasası, kira (~368€ vs Ruhr ortalaması), yaşam ve ulaşım karşılaştırması + hangi şehrin sana uygun olduğuna dair karar rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'bochum-vs-dortmund-which-city-to-study-in-germany-de',
                'title' => 'Bochum oder Dortmund? Welche Stadt zum Studium in Deutschland? (2026)',
                'excerpt' => 'Bochum oder Dortmund? Ein Leitfaden ohne pauschalen Sieger, mit Entscheidungsrahmen: erst das Fach, dann die Stadt, im Ruhrgebiet Bochum (IT-Sicherheit/RUB, ~368€ Miete) vs Dortmund (Ingenieurwesen/TU Dortmund/Logistik, günstig im Ruhr-Durchschnitt), Arbeitsmarkt, Leben und regionale Anbindung im Vergleich + Schnellentscheidung „welche passt zu dir".',
                'meta_title' => 'Bochum oder Dortmund? Welche Stadt zum Studium? (2026)',
                'meta_description' => 'Bochum vs Dortmund in Deutschland: Ruhrgebiet, Fach, Arbeitsmarkt, Miete (~368€ vs Ruhr-Durchschnitt), Leben und Anbindung im Vergleich + Entscheidungshilfe, welche Stadt zu dir passt.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'bochum-vs-dortmund-which-city-to-study-in-germany-en',
                'title' => 'Bochum or Dortmund? Which City to Study in, in Germany? (2026)',
                'excerpt' => 'Bochum or Dortmund? A no-winner, decision-framework guide: subject before city, in the Ruhr region Bochum (IT security/RUB, ~€368 rent) vs Dortmund (engineering/TU Dortmund/logistics, affordable Ruhr average rent), job market, living and regional transport compared + a quick "which fits you" decision guide.',
                'meta_title' => 'Bochum or Dortmund? Which City to Study in? (2026)',
                'meta_description' => 'Bochum vs Dortmund in Germany: Ruhr region, subject, job market, rent (~€368 vs Ruhr average), living and transport compared + a guide to which city fits you.',
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
            'bochum-vs-dortmund-which-city-to-study-in-germany',
            'bochum-vs-dortmund-which-city-to-study-in-germany-de',
            'bochum-vs-dortmund-which-city-to-study-in-germany-en',
        ])->delete();
    }
};

<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da yabancı olarak Informatik (Computer Science) okumak (2026).
 *
 * Doğrulandı (genel kabul + üniversite şartları, 2026):
 *  - Informatik, Almanya'daki uluslararası öğrenciler arasında EN BÜYÜK alan.
 *  - NC tıp/psikolojiden çok daha yumuşak: birçok kamu üniversitesi ve çoğu FH NC-siz/ılımlı;
 *    ama TUM (Eignungsfeststellungsverfahren), RWTH, KIT, TU Berlin/Darmstadt, Saarland, Bonn rekabetçi.
 *  - #1 sürpriz: teorik Informatik ≠ coding bootcamp — matematik-ağırlıklı, yüksek bırakma oranı.
 *  - Dil: bachelor'ların çoğu Almanca (C1, DSH-2/TestDaF 4); İngilizce bachelor nadir/özel,
 *    İngilizce master bol ve kamu ünide ücretsiz (~150–350€ semester; BW non-EU ~1.500€).
 *  - TU/Uni = teori+araştırma; FH (HAW) = uygulamalı; her ikisi de güçlü IT kariyerine götürür.
 *  - Başvuru uni-assist; Türk diploması genelde doğrudan değil → Studienkolleg/1 yıl üni (anabin).
 *
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 * İç-link: anabin + studienkolleg + tıp (kontrast) + küme yazıları 2/3/4 → her dilde locale-doğru.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'b7d1e2a0-1111-4c8a-9f30-aa01bb02cc01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almanya'da bilgisayar mühendisliği okumak zor mu?" — kısa cevap: **tıp ya da psikolojiye kıyasla çok daha erişilebilir.** Informatik, Almanya'da uluslararası öğrenciler arasında **en büyük alan**, ve doğru beklentilerle gelirsen kapı geniş. Ama bir "yazılım kursu" sandığın için gelirsen sert bir gerçekle karşılaşırsın. İşin doğrusunu net koyalım.

## 1. İyi haber önce: NC çok daha yumuşak
Informatik, **Almanya'da uluslararası öğrenciler arasında EN BÜYÜK bölüm.** En güzeli: **NC, tıp ve psikolojiye göre çok daha ılımlı.** Birçok kamu üniversitesi ve **çoğu FH (HAW)** Informatik bachelor'ını **NC-siz (zulassungsfrei) veya ılımlı** kontenjanla açar. Yani notların orta düzeydeyse bile gerçekçi bir hedefin var — [yabancı olarak tıp okumaktan](/tr/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas) çok daha kolay.

**Ama tepe okullar rekabetçi:** **TUM** ayrı bir uygunluk değerlendirmesi (*Eignungsfeststellungsverfahren*) uygular; **RWTH Aachen, KIT, TU Berlin, TU Darmstadt, Saarland (CS devi), Uni Bonn** güçlü ve rekabetçidir. Strateji: hedefin yüksekse bunlara başvur, ama **güvenli bir FH veya NC-siz üniversiteyi** de listene koy.

## 2. #1 gerçeklik şoku: bu bir "coding bootcamp" değil
En çok hayal kırıklığı yaratan nokta: **teorik Informatik ≠ kod yazma kursu.** Alman Informatik müfredatı **çok matematik-ağırlıklıdır** — ayrık matematik (discrete math), lineer cebir, teorik bilgisayar bilimi / biçimsel mantık, algoritma analizi. İlk yıllar bol ispat ve teoriyle geçer.

Sonuç: **bırakma oranı yüksektir.** "Sadece kod yazmak istiyorum" diyerek gelenler en çok zorlanan grup. Eğer matematikle aran iyi değilse ve teoriden hoşlanmıyorsan, **uygulamalı bir FH programı** ya da pratik-odaklı bir bölüm (Wirtschaftsinformatik, Medieninformatik) seni daha mutlu edebilir.

## 3. Dil: bachelor'da Almanca, master'da İngilizce bol
Bachelor programlarının **büyük çoğunluğu Almanca** — asgari **C1 (DSH-2 / TestDaF 4).** **İngilizce bachelor nadirdir** ve çoğunlukla **özel üniversitelerdedir** (CODE University Berlin, Constructor University Bremen, IU, SRH, GISMA) — yani ücretli.

İyi haber: **İngilizce master bol ve kamu üniversitelerinde ücretsizdir** (sadece ~150–350€ dönem katkısı; Baden-Württemberg non-EU öğrencilerden ~1.500€/dönem alır). Yani plan şu olabilir: Almanca C1 ile bachelor, ya da [Almanca olmadan İngilizce CS okumanın yolları](/tr/blog/english-taught-computer-science-it-degrees-in-germany-without-german) ve [Studienkolleg gerçeği](/tr/blog/studienkolleg-is-not-a-language-school-what-it-really-is).

## 4. TU mu, FH mi? Teori vs uygulama
| | TU / Uni | FH (HAW) |
|---|---|---|
| Odak | teori + araştırma | uygulama + pratik |
| Matematik | çok yoğun | daha az, uygulamalı |
| Kariyer | Ar-Ge, akademi, derin teknik | endüstri, geliştirici rolleri |

**Her ikisi de güçlü IT kariyerine götürür.** İtibar kümesi: **TU9** (TUM, RWTH Aachen, KIT, TU Berlin, TU Darmstadt, TU Dresden, Uni Stuttgart, Leibniz Uni Hannover, TU Braunschweig) + **Saarland**, **Uni Bonn**. Ama Almanya'da işveren çoğunlukla **markaya değil yetkinliğe** bakar — FH mezunları da çok rahat iş bulur. (Detay: [prestij miti uni vs FH](/tr/blog/prestige-myth-german-universities-uni-vs-fh-practical-path) · [Hochschule vs Uni vs FH](/tr/blog/hochschule-vs-universitaet-vs-fh-differences-in-germany).)

## 5. Yabancı olarak başvuru
AB-dışı öğrenciler (ör. Türkiye) başvuruyu çoğunlukla **uni-assist** üzerinden yapar. **Türk lise diploması genelde doğrudan denk sayılmaz** → **Studienkolleg** ya da **Türkiye'de 1+ yıl üniversite** okuyarak Abitur denkliği gerekir. Hangi durumda olduğunu **anabin** veritabanından kontrol et. (Bkz. [Anabin & denklik](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma) ve [Studienkolleg](/tr/blog/studienkolleg-is-not-a-language-school-what-it-really-is).)

## 6. Karşılaştırma: Informatik vs Tıp vs Psikoloji
| Bölüm | NC | Matematik yükü | İngilizce seçeneği | İş talebi |
|---|---|---|---|---|
| **Informatik** | NC-siz–ılımlı (tepe okullar hariç) | yüksek | master bol, bachelor nadir | çok yüksek (#1 darboğaz meslek) |
| Tıp | ~1,0–1,2 (acımasız) | orta | yok denecek kadar az | yüksek |
| Psikoloji | ≈ 1,0 (çok rekabetçi) | istatistik-ağırlıklı | nadir/özel | orta, Master darboğazı |

Tablonun özeti: **Informatik, yüksek-talepli alanların içindeki en erişilebilir kapı.**

## Özet ve dürüst tavsiye
Informatik, Almanya'da yabancılar için **en mantıklı bahislerden biri**: NC çoğu yerde yumuşak, IT **#1 darboğaz meslek**, ve İngilizce master yolu açık. **Ama matematiğe ve teoriye hazır ol** — bu bir bootcamp değil. Tepe okulları (TUM, RWTH, KIT, Saarland) hedefle ama listene **NC-siz bir FH/üniversite** de ekle, ve diploma denkliğini **anabin'den** önceden netleştir.

Devamı bu kümede: [Almanca olmadan İngilizce CS/IT okumak](/tr/blog/english-taught-computer-science-it-degrees-in-germany-without-german) · [Almanya'da IT'de çalışmak — Mavi Kart & maaş](/tr/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary) · [CS diplomasıyla ne yapılır — iş piyasası](/tr/blog/what-to-do-with-a-computer-science-degree-in-germany-job-market-salary).

---
*2026 itibarıyla geçerli genel duruma dayanır; NC, dil ve kontenjan şartları üniversiteye göre değişir — başvurudan önce ilgili üniversitenin International Office'inden teyit et.*
MD;

        $deBody = <<<'MD'
„Ist Informatik in Deutschland schwer zu studieren?" — kurz: **viel zugänglicher als Medizin oder Psychologie.** Informatik ist das **größte Fach unter internationalen Studierenden** in Deutschland, und mit den richtigen Erwartungen steht dir die Tür weit offen. Wer aber einen „Programmierkurs" erwartet, erlebt einen harten Realitätsschock. Hier die Fakten.

## 1. Erst die gute Nachricht: der NC ist viel milder
Informatik ist das **größte Fach unter internationalen Studierenden in Deutschland.** Das Beste: Der **NC ist viel milder als bei Medizin und Psychologie.** Viele staatliche Unis und **die meisten FHs (HAW)** bieten den Informatik-Bachelor **NC-frei (zulassungsfrei) oder mit moderatem NC** an. Auch mit mittleren Noten hast du also ein realistisches Ziel — viel leichter als [Medizin als Ausländer](/de/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-de).

**Aber die Top-Schulen sind kompetitiv:** Die **TUM** nutzt ein *Eignungsfeststellungsverfahren*; **RWTH Aachen, KIT, TU Berlin, TU Darmstadt, Saarland (CS-Powerhouse), Uni Bonn** sind stark und kompetitiv. Strategie: Bewirb dich bei diesen, aber setz auch **eine sichere FH oder NC-freie Uni** auf deine Liste.

## 2. Realitäts-Schock Nr. 1: das ist kein „Coding-Bootcamp"
Der größte Frustpunkt: **theoretische Informatik ≠ Programmierkurs.** Das deutsche Informatik-Curriculum ist **sehr mathelastig** — diskrete Mathematik, lineare Algebra, theoretische Informatik / formale Logik, Algorithmenanalyse. Die ersten Jahre stecken voller Beweise und Theorie.

Folge: **Die Abbruchquote ist hoch.** Wer „ich will nur coden" sagt, tut sich am schwersten. Wenn Mathe nicht dein Ding ist und du Theorie nicht magst, machen dich ein **anwendungsorientiertes FH-Programm** oder ein praxisnahes Fach (Wirtschaftsinformatik, Medieninformatik) wahrscheinlich glücklicher.

## 3. Sprache: Bachelor auf Deutsch, Master oft auf Englisch
Die große Mehrheit der Bachelor ist **auf Deutsch** — Minimum **C1 (DSH-2 / TestDaF 4).** **Englischsprachige Bachelor sind selten** und meist an **privaten Unis** (CODE University Berlin, Constructor University Bremen, IU, SRH, GISMA) — also gebührenpflichtig.

Gute Nachricht: **Englischsprachige Master gibt es reichlich und sie sind an staatlichen Unis gebührenfrei** (nur ~150–350€ Semesterbeitrag; Baden-Württemberg verlangt von Nicht-EU ~1.500€/Semester). Möglicher Plan: Bachelor mit Deutsch C1, oder [Wege zum englischsprachigen CS-Studium ohne Deutsch](/de/blog/english-taught-computer-science-it-degrees-in-germany-without-german-de) und [was das Studienkolleg wirklich ist](/de/blog/studienkolleg-is-not-a-language-school-what-it-really-is-de).

## 4. TU oder FH? Theorie vs Anwendung
| | TU / Uni | FH (HAW) |
|---|---|---|
| Fokus | Theorie + Forschung | Anwendung + Praxis |
| Mathe | sehr intensiv | weniger, angewandt |
| Karriere | F&E, Wissenschaft, tiefe Technik | Industrie, Entwicklerrollen |

**Beide führen zu starken IT-Karrieren.** Reputations-Cluster: **TU9** (TUM, RWTH Aachen, KIT, TU Berlin, TU Darmstadt, TU Dresden, Uni Stuttgart, Leibniz Uni Hannover, TU Braunschweig) + **Saarland**, **Uni Bonn**. In Deutschland zählt für Arbeitgeber meist **Kompetenz, nicht die Marke** — FH-Absolventen finden problemlos Jobs. (Mehr: [Prestige-Mythos Uni vs FH](/de/blog/prestige-myth-german-universities-uni-vs-fh-practical-path-de) · [Hochschule vs Uni vs FH](/de/blog/hochschule-vs-universitaet-vs-fh-differences-in-germany-de).)

## 5. Bewerbung als Ausländer
Nicht-EU-Studierende (z. B. Türkei) bewerben sich meist über **uni-assist**. Das **türkische Abiturzeugnis wird meist nicht direkt anerkannt** → du brauchst ein **Studienkolleg** oder **1+ Jahr Universität in der Türkei** für die Abitur-Äquivalenz. Prüfe deinen Fall in der **anabin**-Datenbank. (Siehe [Was ist Anabin](/de/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-de) und [Studienkolleg](/de/blog/studienkolleg-is-not-a-language-school-what-it-really-is-de).)

## 6. Vergleich: Informatik vs Medizin vs Psychologie
| Fach | NC | Mathe-Last | Englisch-Option | Jobnachfrage |
|---|---|---|---|---|
| **Informatik** | NC-frei–moderat (außer Top-Unis) | hoch | Master reichlich, Bachelor selten | sehr hoch (Engpassberuf Nr. 1) |
| Medizin | ~1,0–1,2 (brutal) | mittel | praktisch keine | hoch |
| Psychologie | ≈ 1,0 (sehr kompetitiv) | statistiklastig | selten/privat | mittel, Master-Engpass |

Kurz: **Informatik ist der zugänglichste Einstieg unter den nachgefragten Fächern.**

## Fazit & ehrlicher Rat
Informatik ist für Ausländer in Deutschland **eine der vernünftigsten Wetten**: NC vielerorts mild, IT ist **Engpassberuf Nr. 1**, und der englischsprachige Master-Weg ist offen. **Aber sei bereit für Mathe und Theorie** — das ist kein Bootcamp. Ziel auf Top-Schulen (TUM, RWTH, KIT, Saarland), aber setz auch **eine NC-freie FH/Uni** auf die Liste und kläre die Anerkennung vorab über **anabin**.

Weiter in diesem Cluster: [Englischsprachiges CS/IT-Studium ohne Deutsch](/de/blog/english-taught-computer-science-it-degrees-in-germany-without-german-de) · [In der IT in Deutschland arbeiten — Blue Card & Gehalt](/de/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-de) · [Was tun mit einem CS-Abschluss — Arbeitsmarkt](/de/blog/what-to-do-with-a-computer-science-degree-in-germany-job-market-salary-de).

---
*Stand 2026; NC, Sprache und Quote variieren je nach Uni — vor der Bewerbung beim International Office der jeweiligen Uni bestätigen.*
MD;

        $enBody = <<<'MD'
"Is computer science hard to study in Germany?" — short answer: **far more accessible than medicine or psychology.** Informatik is the **single largest field among international students** in Germany, and with the right expectations the door is wide open. But come expecting a "coding course" and you'll hit a hard reality check. Here are the facts.

## 1. The good news first: the NC is much milder
Informatik is the **single largest field of study among international students in Germany.** Best of all: the **NC is far milder than for medicine and psychology.** Many public universities and **most FHs (HAW)** offer the Informatik bachelor **NC-free (zulassungsfrei) or with a moderate NC.** So even with middling grades you have a realistic target — much easier than [studying medicine as a foreigner](/en/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-en).

**But the top schools are competitive:** **TUM** uses an aptitude assessment (*Eignungsfeststellungsverfahren*); **RWTH Aachen, KIT, TU Berlin, TU Darmstadt, Saarland (a CS powerhouse), Uni Bonn** are strong and competitive. Strategy: apply to these if you aim high, but also put **a safe FH or an NC-free university** on your list.

## 2. Reality shock #1: this is not a "coding bootcamp"
The biggest source of disappointment: **theoretical Informatik ≠ a coding course.** The German Informatik curriculum is **very math-heavy** — discrete maths, linear algebra, theoretical CS / formal logic, algorithm analysis. The early years are full of proofs and theory.

Result: **the dropout rate is high.** Those who say "I just want to code" struggle the most. If maths isn't your thing and you dislike theory, an **applied FH programme** or a practical-leaning subject (business informatics, media informatics) will probably make you happier.

## 3. Language: German for bachelors, English plentiful for masters
The vast majority of bachelors are **in German** — minimum **C1 (DSH-2 / TestDaF 4).** **English-taught bachelors are rare** and mostly at **private universities** (CODE University Berlin, Constructor University Bremen, IU, SRH, GISMA) — so fee-paying.

Good news: **English-taught masters are abundant and tuition-free at public universities** (only a ~€150–350 semester fee; Baden-Württemberg charges non-EU students ~€1,500/semester). One possible plan: a bachelor with German C1, or read [routes into English-taught CS without German](/en/blog/english-taught-computer-science-it-degrees-in-germany-without-german-en) and [what the Studienkolleg really is](/en/blog/studienkolleg-is-not-a-language-school-what-it-really-is-en).

## 4. TU or FH? Theory vs application
| | TU / Uni | FH (HAW) |
|---|---|---|
| Focus | theory + research | application + practice |
| Maths | very intensive | less, applied |
| Career | R&D, academia, deep tech | industry, developer roles |

**Both lead to strong IT careers.** Reputation cluster: **TU9** (TUM, RWTH Aachen, KIT, TU Berlin, TU Darmstadt, TU Dresden, Uni Stuttgart, Leibniz Uni Hannover, TU Braunschweig) + **Saarland**, **Uni Bonn**. In Germany employers mostly weigh **competence, not the brand** — FH graduates land jobs easily too. (More: [the prestige myth, uni vs FH](/en/blog/prestige-myth-german-universities-uni-vs-fh-practical-path-en) · [Hochschule vs Uni vs FH](/en/blog/hochschule-vs-universitaet-vs-fh-differences-in-germany-en).)

## 5. Applying as a foreigner
Non-EU students (e.g. Turkey) usually apply via **uni-assist**. A **Turkish high-school diploma is usually not directly equivalent** → you'll need a **Studienkolleg** or **1+ year of university in Turkey** for Abitur equivalence. Check your case in the **anabin** database. (See [what Anabin is](/en/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-en) and [Studienkolleg](/en/blog/studienkolleg-is-not-a-language-school-what-it-really-is-en).)

## 6. Comparison: Informatik vs Medicine vs Psychology
| Subject | NC | Maths load | English option | Job demand |
|---|---|---|---|---|
| **Informatik** | NC-free–moderate (except top unis) | high | masters plentiful, bachelors rare | very high (#1 shortage occupation) |
| Medicine | ~1.0–1.2 (brutal) | medium | practically none | high |
| Psychology | ≈ 1.0 (very competitive) | statistics-heavy | rare/private | medium, Master's bottleneck |

In short: **Informatik is the most accessible entry among the high-demand fields.**

## Bottom line & honest advice
Informatik is **one of the most sensible bets** for foreigners in Germany: the NC is mild in most places, IT is the **#1 shortage occupation**, and the English-taught master's route is open. **But be ready for maths and theory** — this is not a bootcamp. Aim for the top schools (TUM, RWTH, KIT, Saarland), but also put **an NC-free FH/university** on your list, and sort out diploma equivalence in advance via **anabin**.

Continue in this cluster: [English-taught CS/IT in Germany without German](/en/blog/english-taught-computer-science-it-degrees-in-germany-without-german-en) · [working in IT in Germany — Blue Card & salary](/en/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-en) · [what to do with a CS degree — the job market](/en/blog/what-to-do-with-a-computer-science-degree-in-germany-job-market-salary-en).

---
*Based on the general situation as of 2026; NC, language and quota requirements vary by university — confirm with the relevant university's International Office before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'studying-computer-science-informatik-in-germany-as-a-foreigner',
                'title' => 'Almanya\'da Yabancı Olarak Informatik (Bilgisayar Bilimi) Okumak (2026)',
                'excerpt' => 'Almanya\'da yabancı olarak Informatik: uluslararası öğrenciler arasında EN BÜYÜK alan, NC tıp/psikolojiden çok daha yumuşak (çoğu FH NC-siz), ama TUM/RWTH/KIT/Saarland rekabetçi. #1 şok: matematik-ağırlıklı, coding bootcamp değil. Bachelor Almanca, master İngilizce ücretsiz. Dürüst rehber.',
                'meta_title' => 'Almanya\'da Yabancı Informatik Okumak — NC, Dil (2026)',
                'meta_description' => 'Almanya\'da Informatik: en büyük uluslararası alan, NC tıptan yumuşak (çoğu FH NC-siz), matematik-ağırlıklı (bootcamp değil), bachelor Almanca/master İngilizce ücretsiz — yabancılar için dürüst 2026 rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'studying-computer-science-informatik-in-germany-as-a-foreigner-de',
                'title' => 'Als Ausländer Informatik in Deutschland studieren (2026): NC, Sprache & Mathe-Realität',
                'excerpt' => 'Informatik als Ausländer in Deutschland: das größte Fach unter internationalen Studierenden, NC viel milder als Medizin/Psychologie (die meisten FHs NC-frei), aber TUM/RWTH/KIT/Saarland kompetitiv. Schock Nr. 1: mathelastig, kein Coding-Bootcamp. Bachelor auf Deutsch, Master auf Englisch gebührenfrei.',
                'meta_title' => 'Informatik in Deutschland als Ausländer — NC, Sprache (2026)',
                'meta_description' => 'Informatik in Deutschland: größtes internationales Fach, NC milder als Medizin (meiste FHs NC-frei), mathelastig (kein Bootcamp), Bachelor Deutsch/Master Englisch gebührenfrei — ehrlicher Leitfaden für Ausländer 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'studying-computer-science-informatik-in-germany-as-a-foreigner-en',
                'title' => 'Studying Computer Science (Informatik) in Germany as a Foreigner (2026)',
                'excerpt' => 'Computer science in Germany as a foreigner: the single largest field among international students, NC far milder than medicine/psychology (most FHs are NC-free), but TUM/RWTH/KIT/Saarland are competitive. Reality shock #1: it\'s math-heavy, not a coding bootcamp. Bachelors in German, English masters tuition-free.',
                'meta_title' => 'Study Computer Science in Germany as a Foreigner — NC (2026)',
                'meta_description' => 'CS (Informatik) in Germany: largest international field, NC milder than medicine (most FHs NC-free), math-heavy (not a bootcamp), bachelors in German/English masters tuition-free — an honest 2026 guide for foreigners.',
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
            'studying-computer-science-informatik-in-germany-as-a-foreigner',
            'studying-computer-science-informatik-in-germany-as-a-foreigner-de',
            'studying-computer-science-informatik-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};

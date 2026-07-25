<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): İngilizce Matematik/İstatistik master programları, Almancasız (2026).
 * Doğrulandı: İngilizce MSc Math/Applied/Financial-Actuarial/Statistics Almanya'da bol; kamu ücretsiz
 * (~150-350€/dönem, BW non-EU ~1.500€/dönem); güçlü matematik altyapı + İngilizce yeterlik, bazı program GRE ister.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'b2c20000-2222-4eae-9ff0-bb09cc0eee02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da lisans (bachelor) matematik çoğunlukla **Almanca (C1)** ister; İngilizce lisans nadirdir. Ama iyi haber şu: **master (MSc) seviyesinde İngilizce eğitim veren matematik programları BOL.** Almanca konuşmadan da güçlü bir matematik/istatistik yüksek lisansı yapıp Almanya'da kalabilirsin. Bu yazı hangi programların gerçekten İngilizce olduğunu, şartları, ücreti ve "Almancasız gerçeği"ni dürüstçe anlatır.

## İngilizce MSc bol: hangi alanlar?
Master seviyesinde İngilizce açılan matematik alanları geniş:

- **MSc Mathematics** (saf + uygulamalı karışık)
- **MSc Applied Mathematics** / **Mathematical Modelling**
- **MSc Financial Mathematics / Actuarial Science** (finans/aktüerya — en istihdam odaklı)
- **MSc Statistics / Data Science** (istatistik ağırlıklı)
- **MSc Scientific Computing / Computational Mathematics**

**Neden master İngilizce?** Çünkü matematik araştırmasının ortak dili İngilizcedir; makaleler, seminerler, doktora çalışmaları İngilizce döner. Üniversiteler uluslararası yüksek lisans öğrencisini bu yüzden Almanca şartı koymadan alabiliyor.

## Programlar ve tepe bölümler
Almanya'nın matematik ağırlık merkezleri belli. Aşağıdaki tablo yaklaşık bir yön verir (kesin program dilini her zaman bölümün kendi sayfasından doğrula):

| Üniversite / Merkez | Öne çıkan | İngilizce master | Not |
|---|---|---|---|
| **Bonn** (Hausdorff Center) | Saf matematik mükemmeliyeti | MSc Mathematics (İngilizce) | Araştırma ağırlıklı, seçici |
| **Münster** (Mathematics Münster) | Excellence kümesi | İngilizce master | Güçlü araştırma tabanı |
| **TU / LMU München** | Uygulamalı + finans | Financial/Applied yön | Yoğun kantitatif |
| **KIT (Karlsruhe)** | Uygulamalı + scientific computing | İngilizce yön | Sanayiyle bağ güçlü |
| **TU Berlin** | Uygulamalı/optimizasyon | İngilizce master | Büyük şehir, geniş ağ |
| **Göttingen** | Tarihi matematik merkezi | İngilizce yön | Klasik akademik prestij |
| **Heidelberg / Aachen / Bielefeld / Freiburg** | Çeşitli güçlü bölümler | Değişken | Program bazında kontrol et |

**Kalın gerçek:** İngilizce master çoktur ama her bölümde her yıl aynı program açılmaz. Alanına (finans mı, saf matematik mi, istatistik mi) göre 6-8 üniversitelik bir kısa liste yap.

## Şartlar: güçlü matematik + İngilizce + bazen GRE
İngilizce dil şartı burada asıl engel değil; **matematik altyapın** asıl belirleyici:

- **Güçlü lisans matematik**: analiz, lineer cebir, olasılık/istatistik, bazı programlarda ölçü teorisi, fonksiyonel analiz beklenir. Transkriptin ders-ders incelenir.
- **İngilizce yeterlik**: genelde **IELTS ~6.5** veya **TOEFL ~90**; İngilizce eğitim aldıysan muafiyet olabilir.
- **GRE**: bazı programlar, özellikle finans/aktüerya ve seçici saf matematik yönü, **GRE (General veya Subject)** ister. Şart olmayan çok program da vardır — ama isteyen varsa erken hazırlan.
- Motivasyon mektubu ve bazen mülakat.

Data science / analitik tarafına kaymayı düşünüyorsan, geçiş mantığını [Almanya'da veri bilimi / yapay zekaya nasıl girilir](/tr/blog/how-to-break-into-data-science-ai-in-germany) yazısıyla birlikte oku.

## Ücret: kamu ücretsiz, BW istisnası
- **Devlet üniversiteleri genelde ücretsiz**; sadece dönem başı **Semesterbeitrag ~150-350€** (ulaşım + idari).
- **Baden-Württemberg** eyaleti AB-dışı öğrencilerden **~1.500€/dönem** öğrenim ücreti alır (2025/2026 itibarıyla, yaklaşık; doğrula). KIT ve Freiburg bu eyalettedir.
- Özel üniversiteler ayrı fiyatlandırır; matematik master'ı için nadiren gerekir.

Yaşam masrafı (kira + geçim) genelde harçtan çok daha büyük kalemdir; bütçeni ona göre kur.

## Almancasız gerçeği: nerede yeter, nerede yetmez
Burada dürüst olalım — **İngilizce ile master bitirirsin, ama kariyerin tamamı Almancasız yürümez:**

- **Araştırma / akademi / doktora**: İngilizce büyük ölçüde yeterli. Matematik camiası uluslararasıdır.
- **Quant / finans / veri (tech şirketleri)**: İngilizce-dostu ortamlar çok; iş bulunabilir.
- **Sigorta / aktüerya (Aktuar) ve geniş yerel piyasa**: burada **Almanca ciddi avantaj, çoğu zaman şart.** Müşteri, regülasyon, ekip dili Almancadır.
- **Günlük hayat / bürokrasi / entegrasyon**: Almanca olmadan hayat zorlaşır.

Kısacası: **Almancasız gir, ama okurken B1-B2 Almanca öğren.** Bu, iş piyasasını ikiye katlar. Matematik diplomasının nereye götürdüğünü ve maaşları [matematik diplomasıyla Almanya'da çalışmak: quant, aktüer, veri, maaş](/tr/blog/working-with-a-mathematics-degree-in-germany-quant-actuary-data-salary) yazısında, geniş kariyer haritasını ise [Almanya'da matematik diplomasıyla ne yapılır](/tr/blog/what-to-do-with-a-mathematics-degree-in-germany-job-market) yazısında bulursun.

## Başvuru & DAAD
- Çoğu master başvurusu doğrudan üniversiteye ya da **uni-assist** üzerinden yapılır; son tarihler kışın (yaz başlangıcı için) ve yazın (kış başlangıcı için) değişir.
- **DAAD** burs veritabanı, İngilizce master için finansman aramanın en iyi başlangıcıdır; ayrıca program bulucu olarak da işe yarar.
- Master mı yoksa iş-arama vizesi mi diye kararsızsan, iki yolu karşılaştıran [Almanya master vs iş arama vizesi](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) yazısına göz at.
- Genel giriş için [yabancı olarak Almanya'da matematik / istatistik okumak](/tr/blog/studying-mathematics-statistics-in-germany-as-a-foreigner) rehberine dön.

## Sonuç & dürüst tavsiye
**İngilizce matematik master'ı Almanya'da gerçek ve erişilebilir bir yol.** Kamu üniversiteleri çoğunlukla ücretsiz, alan çeşidi geniş, araştırma dili İngilizce. Ama iki dürüst uyarı: (1) asıl kapı **matematik altyapın**dır — zayıfsa İngilizcen mükemmel olsa da eleme yersin; bazı programlar GRE de ister. (2) Diploma İngilizce olsa da **geniş yerel iş piyasası (özellikle sigorta/aktüerya) Almanca ister.** En akıllı hamle: İngilizce başvur, güçlü bir kantitatif yön (finans/aktüerya/veri) seç ve okurken Almancayı en az B2'ye çıkar. Böylece hem uluslararası hem yerel piyasa sana açık olur.

*Bu yazı 2025/2026 durumuna göre hazırlanmıştır; ücretler, program dilleri, GRE ve dil şartları değişebilir. Başvurmadan önce ilgili üniversite ve bölümün güncel sayfalarından doğrula.*
MD;
        $deBody = <<<'MD'
In Deutschland verlangt der Bachelor in Mathematik meist **Deutsch (C1)**; englischsprachige Bachelor sind selten. Die gute Nachricht: **Auf Master-Ebene (MSc) gibt es viele englischsprachige Mathematik-Programme.** Du kannst also ohne Deutschkenntnisse einen starken Mathematik-/Statistik-Master machen und in Deutschland bleiben. Dieser Beitrag zeigt dir ehrlich, welche Programme wirklich auf Englisch sind, welche Voraussetzungen gelten, was es kostet und wo Deutsch trotzdem nötig ist.

## Viele englische MSc: welche Bereiche?
Auf Master-Ebene wird ein breites Spektrum auf Englisch angeboten:

- **MSc Mathematics** (rein + angewandt gemischt)
- **MSc Applied Mathematics** / **Mathematical Modelling**
- **MSc Financial Mathematics / Actuarial Science** (Finanz-/Aktuarwesen — am arbeitsmarktnächsten)
- **MSc Statistics / Data Science**
- **MSc Scientific Computing / Computational Mathematics**

**Warum ist der Master englisch?** Weil die gemeinsame Sprache der Mathematikforschung Englisch ist: Paper, Seminare und Promotionen laufen auf Englisch. Deshalb nehmen Universitäten internationale Master-Studierende oft ohne Deutschnachweis auf.

## Programme und Spitzenstandorte
Die Schwerpunkte der deutschen Mathematik sind klar verteilt. Die Tabelle gibt eine grobe Orientierung (prüfe die genaue Unterrichtssprache immer auf der Seite des Fachbereichs):

| Universität / Zentrum | Stärke | Englischer Master | Hinweis |
|---|---|---|---|
| **Bonn** (Hausdorff Center) | Reine Mathematik, Exzellenz | MSc Mathematics (Englisch) | Forschungsstark, selektiv |
| **Münster** (Mathematics Münster) | Exzellenzcluster | Englischer Master | Starke Forschungsbasis |
| **TU / LMU München** | Angewandt + Finanz | Financial/Applied | Sehr quantitativ |
| **KIT (Karlsruhe)** | Angewandt + Scientific Computing | Englische Richtung | Enge Industrieanbindung |
| **TU Berlin** | Angewandt/Optimierung | Englischer Master | Großstadt, breites Netzwerk |
| **Göttingen** | Historisches Mathe-Zentrum | Englische Richtung | Klassisches Prestige |
| **Heidelberg / Aachen / Bielefeld / Freiburg** | Diverse starke Fachbereiche | Variabel | Pro Programm prüfen |

**Fett gesagt:** Englische Master sind zahlreich, aber nicht jeder Fachbereich öffnet jedes Jahr dasselbe Programm. Erstelle je nach Richtung (Finanz, reine Mathematik oder Statistik) eine Shortlist von 6-8 Universitäten.

## Voraussetzungen: starke Mathematik + Englisch + manchmal GRE
Die Englisch-Anforderung ist hier nicht die eigentliche Hürde; entscheidend ist dein **mathematisches Fundament:**

- **Starker Mathe-Bachelor**: Analysis, lineare Algebra, Wahrscheinlichkeit/Statistik, bei manchen Programmen Maßtheorie und Funktionalanalysis. Dein Transcript wird Kurs für Kurs geprüft.
- **Englischnachweis**: meist **IELTS ~6,5** oder **TOEFL ~90**; bei englischsprachigem Studium oft Befreiung.
- **GRE**: manche Programme, besonders Finanz-/Aktuarwesen und selektive reine Mathematik, verlangen den **GRE (General oder Subject)**. Viele Programme verlangen ihn nicht — aber wenn nötig, bereite dich früh vor.
- Motivationsschreiben und manchmal ein Interview.

Wenn du eher Richtung Data Science / Analytics denkst, lies ergänzend [Wie steigt man in Deutschland in Data Science / KI ein](/de/blog/how-to-break-into-data-science-ai-in-germany-de).

## Kosten: staatlich meist gebührenfrei, Ausnahme BW
- **Staatliche Universitäten sind meist gebührenfrei**; es fällt nur der **Semesterbeitrag ~150-350€** an (Verkehr + Verwaltung).
- **Baden-Württemberg** erhebt von Nicht-EU-Studierenden **~1.500€/Semester** Studiengebühr (Stand 2025/2026, ungefähr; bitte prüfen). KIT und Freiburg liegen in diesem Bundesland.
- Private Hochschulen kalkulieren anders; für einen Mathe-Master sind sie selten nötig.

Die Lebenshaltung (Miete + Alltag) ist meist ein deutlich größerer Posten als die Gebühren; plane dein Budget entsprechend.

## Realität ohne Deutsch: wo es reicht, wo nicht
Sei hier ehrlich zu dir: **den Master schaffst du auf Englisch, aber die ganze Karriere läuft nicht ohne Deutsch:**

- **Forschung / Wissenschaft / Promotion**: Englisch reicht weitgehend. Die Mathematik-Community ist international.
- **Quant / Finanz / Data (Tech-Firmen)**: viele englischfreundliche Umgebungen; Jobs sind machbar.
- **Versicherung / Aktuariat (Aktuar) und breiter lokaler Markt**: hier ist **Deutsch ein klarer Vorteil, oft Pflicht.** Kunden, Regulierung und Teamsprache sind deutsch.
- **Alltag / Bürokratie / Integration**: ohne Deutsch wird das Leben mühsam.

Kurz: **Steig auf Englisch ein, lern aber während des Studiums B1-B2 Deutsch.** Das verdoppelt deinen Arbeitsmarkt. Wohin ein Mathe-Abschluss führt und welche Gehälter realistisch sind, liest du in [Mit einem Mathematik-Abschluss in Deutschland arbeiten: Quant, Aktuar, Data, Gehalt](/de/blog/working-with-a-mathematics-degree-in-germany-quant-actuary-data-salary-de), die breite Karrierekarte in [Was macht man mit einem Mathematik-Abschluss in Deutschland](/de/blog/what-to-do-with-a-mathematics-degree-in-germany-job-market-de).

## Bewerbung & DAAD
- Die meisten Master-Bewerbungen laufen direkt über die Universität oder über **uni-assist**; die Fristen liegen im Winter (für den Sommerstart) und im Sommer (für den Winterstart).
- Die **DAAD**-Stipendiendatenbank ist der beste Startpunkt für die Finanzierung eines englischen Masters und funktioniert auch als Programmsuche.
- Wenn du zwischen Master und Job-Seeker-Visum schwankst, wirf einen Blick auf [Deutschland Master vs. Job-Seeker-Visum](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).
- Für den allgemeinen Einstieg kehre zum Leitfaden [Als Ausländer Mathematik / Statistik in Deutschland studieren](/de/blog/studying-mathematics-statistics-in-germany-as-a-foreigner-de) zurück.

## Fazit & ehrlicher Rat
**Ein englischsprachiger Mathematik-Master in Deutschland ist ein echter und erreichbarer Weg.** Staatliche Unis sind meist gebührenfrei, die Auswahl ist breit, die Forschungssprache ist Englisch. Aber zwei ehrliche Warnungen: (1) Die eigentliche Tür ist dein **mathematisches Fundament** — ist es schwach, hilft auch perfektes Englisch nicht; manche Programme verlangen zusätzlich den GRE. (2) Auch wenn das Diplom englisch ist, verlangt der **breite lokale Arbeitsmarkt (besonders Versicherung/Aktuariat) Deutsch.** Der klügste Zug: bewirb dich auf Englisch, wähle eine starke quantitative Richtung (Finanz/Aktuariat/Data) und bring dein Deutsch während des Studiums auf mindestens B2. So stehen dir der internationale und der lokale Markt offen.

*Dieser Beitrag beruht auf dem Stand 2025/2026; Gebühren, Unterrichtssprachen sowie GRE- und Sprachanforderungen können sich ändern. Prüfe vor der Bewerbung die aktuellen Seiten der jeweiligen Universität und des Fachbereichs.*
MD;
        $enBody = <<<'MD'
In Germany, a bachelor's in mathematics usually requires **German (C1)**; English-taught bachelor programs are rare. The good news: **at master's (MSc) level there are plenty of English-taught mathematics programs.** You can complete a strong mathematics/statistics master's without speaking German and stay in Germany. This post honestly explains which programs are really in English, the requirements, the cost, and where German is still needed.

## Plenty of English MSc: which fields?
A broad range is offered in English at master's level:

- **MSc Mathematics** (pure + applied mixed)
- **MSc Applied Mathematics** / **Mathematical Modelling**
- **MSc Financial Mathematics / Actuarial Science** (finance/actuarial — most job-oriented)
- **MSc Statistics / Data Science**
- **MSc Scientific Computing / Computational Mathematics**

**Why is the master's in English?** Because the shared language of mathematics research is English: papers, seminars, and doctoral work all run in English. That is why universities often admit international master's students without a German requirement.

## Programs and top departments
Germany's mathematics strongholds are clearly distributed. The table gives rough orientation (always confirm the exact language of instruction on the department's own page):

| University / Center | Strength | English master | Note |
|---|---|---|---|
| **Bonn** (Hausdorff Center) | Pure math excellence | MSc Mathematics (English) | Research-heavy, selective |
| **Münster** (Mathematics Münster) | Excellence cluster | English master | Strong research base |
| **TU / LMU München** | Applied + finance | Financial/Applied track | Very quantitative |
| **KIT (Karlsruhe)** | Applied + scientific computing | English track | Strong industry ties |
| **TU Berlin** | Applied/optimization | English master | Big city, wide network |
| **Göttingen** | Historic math center | English track | Classic academic prestige |
| **Heidelberg / Aachen / Bielefeld / Freiburg** | Various strong departments | Variable | Check per program |

**Bold fact:** English masters are numerous, but not every department opens the same program every year. Build a shortlist of 6-8 universities based on your track (finance, pure math, or statistics).

## Requirements: strong math + English + sometimes GRE
The English requirement is not the real hurdle here; your **mathematical foundation** is decisive:

- **Strong math bachelor**: analysis, linear algebra, probability/statistics, and in some programs measure theory and functional analysis. Your transcript is reviewed course by course.
- **English proficiency**: usually **IELTS ~6.5** or **TOEFL ~90**; often waived if you studied in English.
- **GRE**: some programs, especially finance/actuarial and selective pure-math tracks, require the **GRE (General or Subject)**. Many programs do not — but if one does, prepare early.
- Motivation letter and sometimes an interview.

If you are leaning toward data science / analytics, read this alongside [how to break into data science / AI in Germany](/en/blog/how-to-break-into-data-science-ai-in-germany-en).

## Cost: public mostly free, BW exception
- **Public universities are mostly tuition-free**; you only pay the **semester fee ~€150-350** (transport + admin).
- **Baden-Württemberg** charges non-EU students **~€1,500/semester** in tuition (as of 2025/2026, approximate; verify). KIT and Freiburg are in this state.
- Private universities price differently; they are rarely necessary for a math master's.

Living costs (rent + daily life) are usually a much bigger item than tuition; plan your budget accordingly.

## The no-German reality: where it's enough, where it isn't
Be honest with yourself here: **you can finish the master's in English, but your whole career won't run without German:**

- **Research / academia / PhD**: English is largely enough. The math community is international.
- **Quant / finance / data (tech firms)**: many English-friendly environments; jobs are attainable.
- **Insurance / actuarial (Aktuar) and the broad local market**: here **German is a clear advantage, often required.** Clients, regulation, and team language are German.
- **Daily life / bureaucracy / integration**: without German, life gets harder.

In short: **enter in English, but learn B1-B2 German while studying.** That doubles your job market. For where a math degree leads and realistic salaries, see [working with a mathematics degree in Germany: quant, actuary, data, salary](/en/blog/working-with-a-mathematics-degree-in-germany-quant-actuary-data-salary-en), and for the broad career map, [what to do with a mathematics degree in Germany](/en/blog/what-to-do-with-a-mathematics-degree-in-germany-job-market-en).

## Application & DAAD
- Most master's applications go directly to the university or through **uni-assist**; deadlines fall in winter (for a summer start) and summer (for a winter start).
- The **DAAD** scholarship database is the best starting point for funding an English master's and also works as a program finder.
- If you are torn between a master's and a job-seeker visa, take a look at [Germany master's vs. job-seeker visa](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).
- For the general entry point, return to the guide [studying mathematics / statistics in Germany as a foreigner](/en/blog/studying-mathematics-statistics-in-germany-as-a-foreigner-en).

## Conclusion & honest advice
**An English-taught mathematics master's in Germany is a real and reachable path.** Public universities are mostly free, the choice is broad, and the research language is English. But two honest warnings: (1) the real gate is your **mathematical foundation** — if it is weak, perfect English won't save you, and some programs also require the GRE. (2) Even if the degree is in English, the **broad local job market (especially insurance/actuarial) wants German.** The smartest move: apply in English, pick a strong quantitative track (finance/actuarial/data), and raise your German to at least B2 while studying. That way both the international and the local market stay open to you.

*This post reflects the situation as of 2025/2026; fees, languages of instruction, and GRE and language requirements can change. Verify the current pages of the relevant university and department before applying.*
MD;

        $variants = [
            'tr' => ['slug'=>'english-taught-mathematics-statistics-masters-in-germany-without-german',    'title'=>'Almancasız Almanya\'da Matematik: İngilizce Master Programları (2026)', 'excerpt'=>'Almanya\'da lisans matematik genelde Almanca ister ama İngilizce MSc Math, Applied, Financial/Actuarial ve Statistics programları boldur. Şartları, ücreti (kamu ücretsiz, BW ~1.500€), GRE ihtimalini ve "Almancasız gerçeği"ni dürüstçe anlatıyoruz.', 'meta_title'=>'İngilizce Matematik Master Almanya (Almancasız) 2026', 'meta_description'=>'Almanya\'da İngilizce matematik/istatistik master programları: Bonn, München, KIT, Berlin. Şartlar, GRE, ücret ve Almancasız kariyer gerçeği.', 'body'=>$trBody],
            'de' => ['slug'=>'english-taught-mathematics-statistics-masters-in-germany-without-german-de', 'title'=>'Mathematik in Deutschland ohne Deutsch: Englische Master (2026)',        'excerpt'=>'Der Mathe-Bachelor verlangt meist Deutsch, doch englische MSc in Mathematics, Applied, Financial/Actuarial und Statistics gibt es viele. Wir erklären ehrlich Voraussetzungen, Kosten (staatlich meist gebührenfrei, BW ~1.500€), GRE und die Realität ohne Deutsch.',   'meta_title'=>'Englische Mathe-Master in Deutschland (ohne Deutsch) 2026',  'meta_description'=>'Englischsprachige Mathematik-/Statistik-Master in Deutschland: Bonn, München, KIT, Berlin. Voraussetzungen, GRE, Kosten und Karriere-Realität ohne Deutsch.',   'body'=>$deBody],
            'en' => ['slug'=>'english-taught-mathematics-statistics-masters-in-germany-without-german-en', 'title'=>'Mathematics in Germany Without German: English Master\'s (2026)',        'excerpt'=>'A math bachelor usually needs German, but English-taught MSc in Mathematics, Applied, Financial/Actuarial and Statistics are plentiful. We honestly cover requirements, cost (public mostly free, BW ~€1,500), GRE, and the no-German career reality.',   'meta_title'=>'English-Taught Math Master\'s in Germany (No German) 2026',  'meta_description'=>'English-taught mathematics/statistics master\'s in Germany: Bonn, München, KIT, Berlin. Requirements, GRE, cost, and the no-German career reality.',   'body'=>$enBody],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale'=>$locale, 'translation_group_id'=>$groupId, 'user_id'=>$userId, 'category_id'=>$categoryId,
                'title'=>$v['title'], 'excerpt'=>Str::limit($v['excerpt'],250,'…'),
                'content_md'=>$v['body'], 'content_html'=>$html,
                'meta_title'=>$v['meta_title'], 'meta_description'=>Str::limit($v['meta_description'],158,'…'),
                'reading_minutes'=>max(1,(int)round(str_word_count(strip_tags($html))/200)),
                'is_published'=>true, 'published_at'=>now(),
            ];
            $existing = Post::where('slug', $v['slug'])->first();
            $existing ? $existing->update($payload) : Post::create($payload + ['slug'=>$v['slug']]);
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'english-taught-mathematics-statistics-masters-in-germany-without-german',
            'english-taught-mathematics-statistics-masters-in-germany-without-german-de',
            'english-taught-mathematics-statistics-masters-in-germany-without-german-en',
        ])->delete();
    }
};

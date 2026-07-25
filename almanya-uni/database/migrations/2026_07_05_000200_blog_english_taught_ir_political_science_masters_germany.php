<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almancasız — İngilizce Uluslararası İlişkiler / Public Policy master programları (2026).
 * Doğrulandı: Almanya'da IR/political science bachelor çoğunlukla Almanca (C1), ama İngilizce MASTER bol
 * (FU Berlin IR, Bremen BIGSSS, Hertie School, Willy Brandt Erfurt, TU Dresden, Leipzig Global Studies).
 * Kamu üniversiteleri ücretsiz (~150–350€/dönem, BW non-EU ~1.500€/dönem); Hertie özel ve pahalı.
 * "Almancasız tuzağı": staj/kamu iş için Almanca gerekir. Yazar: Halil Yaprakli.
 * Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c6d20000-2222-4f7b-9fa0-cc04dd09ff02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da **Uluslararası İlişkiler (IR / Internationale Beziehungen)** veya **Siyaset Bilimi (Politikwissenschaft)** okumak istiyorsun ama Almancan yok mu? İyi haber: yüksek lisans (master) seviyesinde **İngilizce ders veren program şaşırtıcı derecede bol** — özellikle IR, public policy ve global studies alanlarında. Kötü haber olmayan gerçek: İngilizce master seni sınıfa sokar, ama IR kariyerinin bir kısmı için Almanca yine gerekir. Bu yazı Almancasız IR/siyaset bilimi master yolunu dürüstçe anlatıyor.

## 1. İngilizce IR / public policy master programları gerçekten bol

Bachelor tarafında durum zor: Almanya'da siyaset bilimi/IR lisansı **çoğunlukla Almanca (C1)** ve İngilizce bachelor nadir. Ama **master seviyesinde** tablo tamamen değişir. Şu program tipleri İngilizce çok yaygın:

- **MA International Relations** (uluslararası ilişkiler)
- **Master of Public Policy (MPP)** (kamu politikası)
- **MA Political Science / Comparative Politics**
- **MA Global Studies / Global Governance**
- **MA Peace & Conflict Studies** (barış ve çatışma çalışmaları)
- **MA International Affairs / International Studies**

Bu programların çoğu tamamen İngilizce yürütülür; günlük hayat için biraz Almanca faydalı olsa da **derslere, tezine ve mezuniyete Almancasız** ulaşabilirsin.

## 2. Somut programlar: kamu vs özel

İşte İngilizce IR/public policy sahnesinde en tanınan programlar. **Kamu üniversiteleri neredeyse ücretsiz**; özel okullar (özellikle Hertie) pahalı ama prestijli ve network-güçlü.

| Program | Şehir | Tür | Not (yaklaşık, 2026 — doğrula) |
|---|---|---|---|
| **FU Berlin — MA International Relations** | Berlin | Kamu | Otto-Suhr-Institut geleneği; Humboldt + Potsdam ortak, prestijli, İngilizce |
| **BIGSSS Bremen — IR: Global Governance & Social Theory** | Bremen | Kamu | Araştırma-yoğun graduate school; teori + governance |
| **Hertie School — MIA / MPP** | Berlin | **Özel** | Prestijli, güçlü network; İngilizce; **öğrenim ücreti yüksek** |
| **Willy Brandt School — MPP** | Erfurt | Kamu | **Devlet** üniversitesi; tamamen İngilizce MPP, çeşitlilik odaklı |
| **TU Dresden — MA International Studies** | Dresden | Kamu | Disiplinlerarası, İngilizce |
| **Leipzig — MA Global Studies** | Leipzig | Kamu | Erasmus Mundus ağı; uluslararası hareketlilik |
| **Marburg — Peace & Conflict Studies** | Marburg | Kamu | Barış/çatışma odaklı, İngilizce |
| **Passau — International Governance** | Passau | Kamu | Governance + hukuk/ekonomi karışımı |

**Konum notu:** *Berlin* Almanya'nın IR kariyeri merkezidir — Auswärtiges Amt (Dışişleri), think-tank'ler (**SWP / DGAP / ECFR**), NGO'lar ve uluslararası kuruluşlar orada yoğunlaşır. Berlin'de okumak staj/network açısından ciddi avantaj.

## 3. Şartlar: lisans + İngilizce + bazen motivasyon/iş tecrübesi

Tipik başvuru şartları (*2025/2026, program başına değişir — mutlaka doğrula*):

- **İlgili lisans:** siyaset bilimi, IR, sosyoloji, tarih, hukuk, ekonomi veya ilgili sosyal bilim.
- **İngilizce yeterliği:** genelde **IELTS ~6.5–7.0** veya **TOEFL** dengi.
- **Motivasyon mektubu:** IR/public policy programlarında **çok önemli** — neden bu alan, neden bu okul, hangi konu ilgini çekiyor.
- **İş/staj tecrübesi:** bazı **MPP** programları (özellikle Hertie gibi) birkaç yıl **iş tecrübesi** tercih eder ya da şart koşar.
- Referanslar, CV, bazen kısa bir yazı örneği (writing sample).

**Dürüst beklenti:** IR/siyaset bilimi master'ı okuma-yazma yoğun, teori + analiz ağırlıklıdır. Bazı public policy programları kantitatif (istatistik/ekonomi) modüller de içerir.

## 4. Ücret: kamu ücretsiz, Hertie gibi özeller pahalı

Özet (*2025/2026, yaklaşık — doğrula*):

- **Kamu üniversitesi, çoğu eyalet:** öğrenim ücreti **yok**; sadece **~150–350€/dönem** katkı payı (Semesterbeitrag; toplu taşıma kartı dâhil olabilir).
- **Baden-Württemberg:** AB-dışı öğrenciler için ~**1.500€/dönem** (bu kümede daha çok Berlin/Bremen/Erfurt/Leipzig var, ama BW'deki programlarda dikkat).
- **Özel okullar (Hertie School):** **çok daha pahalı** — toplam öğrenim ücreti onbinlerce euro seviyesinde olabilir; ama burs/indirim imkânları da vardır. Rakamı **doğrudan okuldan doğrula**.
- **Geçim gideri:** öğrenim ücretinden bağımsız; şehre göre aylık ~**930€+** blokedeki hesaba yatırılması istenebilir (vize için, doğrula).

## 5. "Almancasız tuzağı": staj ve kamu iş için Almanca gerekir

İşte kimsenin yeterince söylemediği kısım. İngilizce master **eğitimi** çözer; IR **kariyerini** tam çözmez:

- **Uluslararası kuruluşlar (BM/AB/OECD), think-tank'lerin İngilizce projeleri, uluslararası NGO'lar:** İngilizce-dostu — İngilizceyle çok yol alırsın.
- **Alman kamu kurumları / bakanlıklar / Auswärtiges Amt:** neredeyse her zaman **Almanca (çoğu kez C1)** ister.
- **Staj/Praktikum (kariyerin anahtarı):** birçok Berlin merkezli think-tank ve NGO stajı **Almanca** ister; Almancasız staj havuzun ciddi daralır.
- **Diplomatlık ek uyarısı:** Alman dış hizmetine (Auswärtiges Amt diplomat) girmek **Alman vatandaşlığı** ister — Türk öğrenci için bu, vatandaşlık almadıkça geçerli dürüst bir sınır.

**Sonuç:** İngilizce master ile gel, ama **Almancayı ilk günden paralel öğren.** B1-B2 bile staj kapılarını açar; C1 kamu/politika yolunu açar. "Almancasız gelirim, hiç öğrenmem" planı IR kariyeri için tuzaktır — çünkü bu alanda **staj + network** her şeydir.

## 6. Başvuru & DAAD

- **Başvuru kanalı:** bazı programlar **uni-assist** üzerinden, bazıları **doğrudan üniversiteye/okula** (Hertie doğrudan). Her programın sayfasını tek tek kontrol et.
- **Takvim:** kış dönemi (Ekim) için başvurular genelde **kış öncesi ilkbahar-yaz** kapanır; iş tecrübesi/writing sample gerekiyorsa erken başla.
- **DAAD bursları:** yüksek lisans için DAAD çeşitli burslar sunar; ayrıca public policy alanında hedefli programlar olabilir. Rekabetçi; erken başvur. Ayrıntıları **daad.de** üzerinden *doğrula*.

## 7. Sonuç & dürüst tavsiye

Almancasız Almanya'da IR/siyaset bilimi master'ı **gerçekten mümkün** — FU Berlin IR, Bremen BIGSSS, Hertie School, Willy Brandt Erfurt, TU Dresden, Leipzig Global Studies gibi İngilizce programlar bol; kamuda **ücretsiz** (Hertie gibi özeller pahalı). Ama iki gerçeği unutma: (1) IR **generalist** bir alandır, kariyerde **staj + network + diller** belirleyicidir; (2) İngilizce eğitim seni sınıfa sokar ama **birçok staj/iş Almanca ister** — Almancayı paralel öğrenmek stratejik zorunluluk. Berlin gibi ekosistem-yoğun bir şehri düşün, motivasyon mektubunu ciddiye al ve Almancaya baştan başla.

İlgili: [Almanya'da Uluslararası İlişkiler / Siyaset Bilimi okumak — uluslararası öğrenci rehberi](/tr/blog/studying-international-relations-political-science-in-germany-as-a-foreigner) · [Almanya'da IR ve politikada çalışmak — kuruluşlar & gerçek](/tr/blog/working-in-international-relations-policy-organizations-in-germany) · [IR/siyaset bilimi diplomasıyla ne yapılır — iş piyasası](/tr/blog/what-to-do-with-a-political-science-ir-degree-in-germany-job-market) · [Almanya'da üniversite prestiji ve sıralamaları nasıl işler](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) · [Master mı, İş Arama Vizesi mi — iki kariyer anahtarı](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career).

*Bu yazı 2026 başı itibarıyla hazırlanmıştır. Ücretler (özellikle Hertie gibi özel okullar), başvuru şartları, İngilizce/Almanca yeterlik gerekleri ve vize/geçim kuralları değişebilir; başvurmadan önce üniversitelerin/okulların resmî sayfalarından ve daad.de'den doğrula.*
MD;

        $deBody = <<<'MD'
Du willst in Deutschland **Internationale Beziehungen (IR)** oder **Politikwissenschaft** studieren, sprichst aber kein Deutsch? Gute Nachricht: Auf **Master-Ebene gibt es überraschend viele englischsprachige Programme** — besonders in IR, Public Policy und Global Studies. Die ehrliche Nachricht: Ein englischer Master bringt dich in den Hörsaal, aber für einen Teil deiner IR-Karriere brauchst du trotzdem Deutsch. Dieser Beitrag erklärt den Weg zum IR-/Politik-Master ohne Deutsch offen.

## 1. Englische IR- / Public-Policy-Master gibt es reichlich

Beim Bachelor ist es schwierig: Politikwissenschaft/IR ist im Bachelor **meist auf Deutsch (C1)**, englische Bachelor sind selten. Auf **Master-Ebene** dreht sich das Bild komplett. Diese Programmtypen sind sehr häufig auf Englisch:

- **MA International Relations**
- **Master of Public Policy (MPP)**
- **MA Political Science / Comparative Politics**
- **MA Global Studies / Global Governance**
- **MA Peace & Conflict Studies**
- **MA International Affairs / International Studies**

Die meisten dieser Programme laufen komplett auf Englisch. Etwas Deutsch hilft im Alltag, aber **Lehre, Abschlussarbeit und Abschluss** erreichst du ohne Deutsch.

## 2. Konkrete Programme: staatlich vs privat

Hier die bekanntesten Programme der englischsprachigen IR-/Public-Policy-Szene. **Staatliche Unis sind fast gebührenfrei**; private Schulen (vor allem die Hertie School) sind teuer, aber prestigeträchtig und stark im Netzwerk.

| Programm | Stadt | Art | Hinweis (ungefähr, 2026 — prüfen) |
|---|---|---|---|
| **FU Berlin — MA International Relations** | Berlin | Staatlich | Tradition des Otto-Suhr-Instituts; gemeinsam mit Humboldt + Potsdam, prestigeträchtig, Englisch |
| **BIGSSS Bremen — IR: Global Governance & Social Theory** | Bremen | Staatlich | Forschungsstarke Graduate School; Theorie + Governance |
| **Hertie School — MIA / MPP** | Berlin | **Privat** | Prestigeträchtig, starkes Netzwerk; Englisch; **hohe Studiengebühren** |
| **Willy Brandt School — MPP** | Erfurt | Staatlich | **Staatliche** Uni; komplett englischer MPP, sehr divers |
| **TU Dresden — MA International Studies** | Dresden | Staatlich | Interdisziplinär, Englisch |
| **Leipzig — MA Global Studies** | Leipzig | Staatlich | Erasmus-Mundus-Netzwerk; internationale Mobilität |
| **Marburg — Peace & Conflict Studies** | Marburg | Staatlich | Fokus Frieden/Konflikt, Englisch |
| **Passau — International Governance** | Passau | Staatlich | Governance + Recht/Wirtschaft |

**Standort-Hinweis:** *Berlin* ist Deutschlands Zentrum für IR-Karrieren — das Auswärtige Amt, Think-Tanks (**SWP / DGAP / ECFR**), NGOs und internationale Organisationen ballen sich dort. In Berlin zu studieren ist ein echter Vorteil für Praktika/Netzwerk.

## 3. Voraussetzungen: Bachelor + Englisch + teils Motivation/Berufserfahrung

Typische Anforderungen (*2025/2026, je Programm unterschiedlich — unbedingt prüfen*):

- **Passender Bachelor:** Politikwissenschaft, IR, Soziologie, Geschichte, Jura, Wirtschaft oder verwandte Sozialwissenschaft.
- **Englischnachweis:** meist **IELTS ~6.5–7.0** oder gleichwertiges **TOEFL**.
- **Motivationsschreiben:** in IR-/Public-Policy-Programmen **sehr wichtig** — warum dieses Feld, warum diese Schule, welches Thema.
- **Berufs-/Praktikumserfahrung:** einige **MPP**-Programme (etwa die Hertie School) bevorzugen oder verlangen ein paar Jahre **Berufserfahrung**.
- Empfehlungen, Lebenslauf, teils ein Writing Sample.

**Ehrliche Erwartung:** Der IR-/Politik-Master ist lese- und schreibintensiv, theorie- und analyselastig. Manche Public-Policy-Programme enthalten auch quantitative Module (Statistik/Wirtschaft).

## 4. Gebühren: staatlich gebührenfrei, Private wie Hertie teuer

Zusammengefasst (*2025/2026, ungefähr — prüfen*):

- **Staatliche Uni, die meisten Länder:** **keine** Studiengebühren; nur **~150–350€/Semester** Beitrag (Semesterticket evtl. inkl.).
- **Baden-Württemberg:** für Nicht-EU-Studierende ca. **1.500€/Semester** (in diesem Cluster liegen v. a. Berlin/Bremen/Erfurt/Leipzig, aber bei BW-Programmen aufpassen).
- **Private Schulen (Hertie School):** **deutlich teurer** — die Gesamtgebühren können im fünfstelligen Bereich liegen; es gibt aber Stipendien/Nachlässe. Zahl **direkt bei der Schule prüfen**.
- **Lebenshaltung:** unabhängig von Gebühren; je nach Stadt monatlich ~**930€+**, evtl. auf einem Sperrkonto nachzuweisen (für das Visum, prüfen).

## 5. Die "ohne Deutsch"-Falle: für Praktikum und öffentlichen Dienst ist Deutsch nötig

Hier kommt der Teil, den kaum jemand deutlich sagt. Ein englischer Master löst das **Studium**, aber nicht die ganze IR-**Karriere**:

- **Internationale Organisationen (UN/EU/OECD), englische Think-Tank-Projekte, internationale NGOs:** englischfreundlich — auf Englisch kommst du weit.
- **Deutsche Behörden / Ministerien / Auswärtiges Amt:** verlangen fast immer **Deutsch (oft C1)**.
- **Praktikum (der Schlüssel zur Karriere):** viele Berliner Think-Tanks und NGOs verlangen **Deutsch** für Praktika; ohne Deutsch schrumpft dein Pool stark.
- **Diplomaten-Hinweis:** Für den deutschen Auswärtigen Dienst (Diplomat) ist die **deutsche Staatsbürgerschaft** nötig — eine ehrliche Grenze, solange du sie nicht hast.

**Fazit:** Komm mit einem englischen Master, aber **lerne Deutsch ab dem ersten Tag parallel.** Schon B1-B2 öffnet Praktikumstüren; C1 öffnet den Weg in Politik/öffentlichen Dienst. Der Plan "ohne Deutsch kommen, nie lernen" ist in der IR eine Falle — denn hier zählen **Praktikum + Netzwerk** alles.

## 6. Bewerbung & DAAD

- **Bewerbungsweg:** Manche Programme laufen über **uni-assist**, andere **direkt bei der Uni/Schule** (Hertie direkt). Prüfe jede Programmseite einzeln.
- **Zeitplan:** Für das Wintersemester (Oktober) schließen Bewerbungen meist im **Frühjahr/Sommer davor**; brauchst du Berufserfahrung/Writing Sample, fang früh an.
- **DAAD-Stipendien:** Für den Master bietet der DAAD verschiedene Stipendien, teils gezielt für Public Policy. Kompetitiv; bewirb dich früh. Details auf **daad.de** *prüfen*.

## 7. Fazit & ehrlicher Rat

Ein IR-/Politik-Master ohne Deutsch ist in Deutschland **wirklich machbar** — englische Programme wie FU Berlin IR, Bremen BIGSSS, Hertie School, Willy Brandt Erfurt, TU Dresden, Leipzig Global Studies gibt es reichlich; staatlich sind sie **gebührenfrei** (Private wie Hertie teuer). Vergiss aber zwei Dinge nicht: (1) IR ist ein **Generalisten-Feld**, entscheidend sind **Praktikum + Netzwerk + Sprachen**; (2) englische Lehre bringt dich in den Hörsaal, aber **viele Praktika/Jobs verlangen Deutsch** — Deutsch parallel zu lernen ist strategische Pflicht. Denk an eine ökosystemstarke Stadt wie Berlin, nimm dein Motivationsschreiben ernst und beginne von Anfang an mit Deutsch.

Verwandt: [Internationale Beziehungen / Politikwissenschaft in Deutschland studieren — Leitfaden für Internationale](/de/blog/studying-international-relations-political-science-in-germany-as-a-foreigner-de) · [In IR und Politik in Deutschland arbeiten — Organisationen & Realität](/de/blog/working-in-international-relations-policy-organizations-in-germany-de) · [Was mit einem Politik-/IR-Abschluss anfangen — Arbeitsmarkt](/de/blog/what-to-do-with-a-political-science-ir-degree-in-germany-job-market-de) · [Wie Uni-Prestige und Rankings in Deutschland funktionieren](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de) · [Master vs. Job-Seeker-Visum — zwei Karriereschlüssel](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).

*Dieser Beitrag ist Stand Anfang 2026. Gebühren (besonders private Schulen wie Hertie), Zulassungsvoraussetzungen, Englisch-/Deutschnachweise sowie Visa- und Lebenshaltungsregeln können sich ändern; prüfe vor der Bewerbung die offiziellen Seiten der Universitäten/Schulen und daad.de.*
MD;

        $enBody = <<<'MD'
You want to study **International Relations (IR)** or **Political Science** in Germany but don't speak German? Good news: at **master's level there are surprisingly many English-taught programmes** — especially in IR, public policy and global studies. The honest news: an English master's gets you into the lecture hall, but part of your IR career will still need German. This post explains the no-German route to an IR/political-science master's straight.

## 1. English IR / public policy master's are plentiful

At bachelor level it's hard: political science/IR is **mostly taught in German (C1)** at bachelor level, and English bachelors are rare. At **master's level** the picture flips completely. These programme types are very common in English:

- **MA International Relations**
- **Master of Public Policy (MPP)**
- **MA Political Science / Comparative Politics**
- **MA Global Studies / Global Governance**
- **MA Peace & Conflict Studies**
- **MA International Affairs / International Studies**

Most of these run fully in English. Some German helps in daily life, but you can reach **lectures, your thesis and graduation without German**.

## 2. Concrete programmes: public vs private

Here are the best-known programmes on the English-taught IR/public-policy scene. **Public universities are almost tuition-free**; private schools (above all the Hertie School) are expensive but prestigious and strong on network.

| Programme | City | Type | Note (approximate, 2026 — verify) |
|---|---|---|---|
| **FU Berlin — MA International Relations** | Berlin | Public | Otto-Suhr-Institut tradition; joint with Humboldt + Potsdam, prestigious, English |
| **BIGSSS Bremen — IR: Global Governance & Social Theory** | Bremen | Public | Research-intensive graduate school; theory + governance |
| **Hertie School — MIA / MPP** | Berlin | **Private** | Prestigious, strong network; English; **high tuition** |
| **Willy Brandt School — MPP** | Erfurt | Public | **State** university; fully English MPP, very diverse |
| **TU Dresden — MA International Studies** | Dresden | Public | Interdisciplinary, English |
| **Leipzig — MA Global Studies** | Leipzig | Public | Erasmus Mundus network; international mobility |
| **Marburg — Peace & Conflict Studies** | Marburg | Public | Peace/conflict focus, English |
| **Passau — International Governance** | Passau | Public | Governance + law/economics mix |

**Location note:** *Berlin* is Germany's hub for IR careers — the Federal Foreign Office (Auswärtiges Amt), think-tanks (**SWP / DGAP / ECFR**), NGOs and international organisations cluster there. Studying in Berlin is a real advantage for internships/network.

## 3. Requirements: bachelor + English + sometimes motivation/work experience

Typical requirements (*2025/2026, varies by programme — always verify*):

- **Relevant bachelor:** political science, IR, sociology, history, law, economics or a related social science.
- **English proficiency:** usually **IELTS ~6.5–7.0** or an equivalent **TOEFL**.
- **Motivation letter:** **very important** in IR/public-policy programmes — why this field, why this school, which topic draws you.
- **Work/internship experience:** some **MPP** programmes (such as the Hertie School) prefer or require a few years of **work experience**.
- References, CV, sometimes a writing sample.

**Honest expectation:** the IR/political-science master's is reading- and writing-heavy, theory- and analysis-focused. Some public-policy programmes also include quantitative modules (statistics/economics).

## 4. Fees: public is tuition-free, privates like Hertie are expensive

In short (*2025/2026, approximate — verify*):

- **Public university, most states:** **no** tuition; only a **~€150–350/semester** contribution (a transport ticket may be included).
- **Baden-Württemberg:** roughly **€1,500/semester** for non-EU students (this cluster is mostly Berlin/Bremen/Erfurt/Leipzig, but mind BW programmes).
- **Private schools (Hertie School):** **much more expensive** — total tuition can run into the tens of thousands of euros; scholarships/discounts exist, though. **Verify the figure directly with the school.**
- **Living costs:** independent of tuition; depending on the city around **€930+/month**, possibly to be shown in a blocked account (for the visa, verify).

## 5. The "no-German" trap: internships and public sector need German

Here's the part few people state clearly. An English master's solves the **study**, not the whole IR **career**:

- **International organisations (UN/EU/OECD), think-tanks' English projects, international NGOs:** English-friendly — you go far in English.
- **German public bodies / ministries / the Federal Foreign Office:** almost always require **German (often C1)**.
- **Internships/Praktikum (the key to a career):** many Berlin-based think-tank and NGO internships require **German**; without German your pool shrinks sharply.
- **Diplomat note:** joining the German foreign service (Auswärtiges Amt diplomat) requires **German citizenship** — an honest limit until you hold it.

**Bottom line:** come with an English master's, but **learn German in parallel from day one.** Even B1-B2 opens internship doors; C1 opens the policy/public-sector route. The "come without German, never learn it" plan is a trap in IR — because here **internships + network** are everything.

## 6. Application & DAAD

- **Application channel:** some programmes go through **uni-assist**, others **directly to the university/school** (Hertie direct). Check each programme page individually.
- **Timeline:** for the winter intake (October), applications usually close the **spring/summer before**; if you need work experience/a writing sample, start early.
- **DAAD scholarships:** for the master's, the DAAD offers various scholarships, some targeted at public policy. Competitive; apply early. Verify details on **daad.de**.

## 7. Conclusion & honest advice

An IR/political-science master's without German is **genuinely doable** in Germany — English programmes such as FU Berlin IR, Bremen BIGSSS, Hertie School, Willy Brandt Erfurt, TU Dresden and Leipzig Global Studies are plentiful; at public universities they are **tuition-free** (privates like Hertie are expensive). But don't forget two things: (1) IR is a **generalist** field where **internships + network + languages** decide your career; (2) English teaching gets you into the lecture hall, but **many internships/jobs require German** — learning German in parallel is a strategic must. Aim for an ecosystem-rich city like Berlin, take your motivation letter seriously, and start German from day one.

Related: [Studying International Relations / Political Science in Germany — international student guide](/en/blog/studying-international-relations-political-science-in-germany-as-a-foreigner-en) · [Working in IR and policy in Germany — organisations & reality](/en/blog/working-in-international-relations-policy-organizations-in-germany-en) · [What to do with a political science/IR degree — the job market](/en/blog/what-to-do-with-a-political-science-ir-degree-in-germany-job-market-en) · [How university prestige and rankings work in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en) · [Master's vs the Job-Seeker Visa — two career keys](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

*This post reflects the situation in early 2026. Fees (especially private schools like Hertie), admission requirements, English/German proficiency rules, and visa/living-cost rules can change; verify on the universities'/schools' official pages and daad.de before applying.*
MD;

        $variants = [
            'tr' => ['slug'=>'english-taught-international-relations-political-science-masters-in-germany',    'title'=>'Almancasız Almanya\'da Uluslararası İlişkiler: İngilizce Master Programları (2026)', 'excerpt'=>'Almancan yok mu? Almanya\'da İngilizce IR/public policy master bol: FU Berlin IR, Bremen BIGSSS, Hertie, Willy Brandt Erfurt, TU Dresden, Leipzig. Kamu ücretsiz, Hertie pahalı; staj/iş için Almanca gerçeği.', 'meta_title'=>'Almancasız İngilizce Uluslararası İlişkiler Master — Almanya (2026)', 'meta_description'=>'Almanya\'da İngilizce ders veren IR/public policy master programları (FU Berlin, Bremen BIGSSS, Hertie, Erfurt), kamu vs özel ücret, şartlar ve Almanca gerçeği (2026).', 'body'=>$trBody],
            'de' => ['slug'=>'english-taught-international-relations-political-science-masters-in-germany-de', 'title'=>'IR ohne Deutsch: Englische Master in Deutschland (2026)',        'excerpt'=>'Kein Deutsch? In Deutschland gibt es viele englische IR-/Public-Policy-Master: FU Berlin, Bremen BIGSSS, Hertie, Willy Brandt Erfurt, TU Dresden, Leipzig. Staatlich gebührenfrei, Hertie teuer; für Job oft Deutsch nötig.',   'meta_title'=>'Englische IR-/Politik-Master ohne Deutsch — Deutschland (2026)',  'meta_description'=>'Englischsprachige IR-/Public-Policy-Master in Deutschland (FU Berlin, Bremen BIGSSS, Hertie, Erfurt), staatlich vs privat, Voraussetzungen und die Deutsch-Realität (2026).',   'body'=>$deBody],
            'en' => ['slug'=>'english-taught-international-relations-political-science-masters-in-germany-en', 'title'=>'IR Without German: English Master\'s in Germany (2026)',        'excerpt'=>'No German? Germany has plenty of English IR/public policy master\'s: FU Berlin, Bremen BIGSSS, Hertie, Willy Brandt Erfurt, TU Dresden, Leipzig. Public is tuition-free, Hertie is pricey; jobs often need German.',   'meta_title'=>'English-Taught International Relations Master\'s — Germany (2026)',  'meta_description'=>'English-taught IR/public policy master\'s in Germany (FU Berlin, Bremen BIGSSS, Hertie, Erfurt), public vs private fees, requirements, and the German-language reality (2026).',   'body'=>$enBody],
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
            'english-taught-international-relations-political-science-masters-in-germany',
            'english-taught-international-relations-political-science-masters-in-germany-de',
            'english-taught-international-relations-political-science-masters-in-germany-en',
        ])->delete();
    }
};

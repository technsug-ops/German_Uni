<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): İngilizce Ekonomi (VWL) master programları — Almancasız (2026). Doğrulandı:
 * Almanya'da İngilizce MSc Economics/Quant/Econometrics bol; kamu üniversiteleri ücretsiz
 * (~150–350€/dönem, BW non-EU ~1.500€/dönem); bazı programlar (Bonn/Mannheim) GRE + matematik ister.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c2e20000-2222-4ec0-9f40-cc01dd03ff02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da **ekonomi (VWL — Volkswirtschaftslehre)** okumak istiyorsun ama Almancan yok mu? İyi haber: yüksek lisans (master) seviyesinde **İngilizce ders veren program bol**. Kötü haber olmayan gerçek: İngilizce master seni derse sokar, ama kariyerin bir kısmı için Almanca yine gerekebilir. Bu yazı Almancasız ekonomi master yolunu dürüstçe anlatıyor.

## 1. İngilizce MSc Economics/Quant/Econometrics gerçekten bol

Bachelor tarafında durum zor: Almanya'da ekonomi lisansı **çoğunlukla Almanca (C1)** ve İngilizce bachelor nadir. Ama **master seviyesinde** tablo değişir. Şu program tipleri İngilizce çok yaygın:

- **MSc Economics** (genel iktisat)
- **MSc Quantitative Economics** (kantitatif iktisat)
- **MSc Econometrics** (ekonometri)
- **MSc Economics & Finance**
- **MSc Public Policy / Development Economics** (politika, kalkınma iktisadı)

Bu programların çoğu tamamen İngilizce yürütülür; günlük hayat için biraz Almanca faydalı olsa da **derslere ve mezuniyete Almancasız** ulaşabilirsin.

## 2. Kamu üniversitelerinde ücretsiz İngilizce master programları

Almanya'nın en büyük avantajı: **kamu üniversitelerinde öğrenim ücreti yok.** Sadece dönemlik katkı payı (Semesterbeitrag) ödersin — *2025/2026 itibarıyla, yaklaşık, doğrula*: ~**150–350€/dönem** (toplu taşıma kartı dâhil olabilir). Tek istisna Baden-Württemberg: AB-dışı öğrenciler için ~**1.500€/dönem** (yine doğrula).

| Üniversite | Şehir | Öne çıkan İngilizce master | Not (yaklaşık, 2026 — doğrula) |
|---|---|---|---|
| **Bonn** | Bonn | MSc Economics | Avrupa'nın en güçlü iktisat bölümlerinden; araştırma yoğun, GRE isteyebilir |
| **Mannheim** | Mannheim | MSc Economics | Kantitatif, araştırma-güçlü; genelde GRE + matematik |
| **LMU München** | Münih | MSc Economics | Büyük fakülte, geniş seçmeli |
| **Frankfurt (Goethe)** | Frankfurt | MSc Economics / Money & Finance | ECB & finans merkezi yakınında |
| **Humboldt Berlin** | Berlin | MSc Economics / Statistics | Ekonometri/istatistik güçlü |
| **Köln** | Köln | MSc Economics | Büyük ekonomi fakültesi |
| **Tübingen** | Tübingen | MSc Economics | Baden-Württemberg → non-EU ücret uyarısı |

Not: **Bonn ve Mannheim** araştırma tarafında çok güçlüdür ama seçicidir. Ücretsiz olması "kolay girilir" demek değil.

## 3. Şartlar: lisans + İngilizce + bazen GRE ve matematik

Tipik başvuru şartları (*2025/2026, program başına değişir — mutlaka doğrula*):

- **İlgili lisans:** ekonomi veya kantitatif bir alan (istatistik, matematik, işletme+iktisat karışımı vb.).
- **İngilizce yeterliği:** genelde **IELTS ~6.5–7.0** veya **TOEFL** dengi.
- **Matematik altyapısı:** birçok İngilizce MSc Economics programı **kalkülüs, lineer cebir, olasılık/istatistik** transkriptinde arar. Bu bir formalite değil — VWL master'ı **matematik/ekonometri ağırlıklıdır**.
- **GRE:** özellikle **Bonn ve Mannheim** gibi araştırma-güçlü programlar **GRE (quant)** isteyebilir. Bu, dürüst bir engel; erken planla.
- Motivasyon mektubu, referanslar, bazen CV.

**Dürüst beklenti:** VWL master'ı ekonometri, optimizasyon ve olasılıkla dolu. Matematikten kaçmak isteyen için doğru rota değil.

## 4. Ücret: kamu ücretsiz, tek istisna Baden-Württemberg

Özet (*2025/2026, yaklaşık — doğrula*):

- **Kamu üniversitesi, çoğu eyalet:** öğrenim ücreti **yok**; sadece **~150–350€/dönem** katkı payı.
- **Baden-Württemberg (Stuttgart, Heidelberg, Tübingen, Freiburg, Konstanz...):** AB-dışı öğrenciler için ~**1.500€/dönem**.
- **Özel üniversiteler:** ayrı fiyatlandırma (bu yazı kamuya odaklı).
- **Geçim gideri:** öğrenim ücretinden bağımsız; şehre göre aylık ~**930€+** blokedeki hesaba yatırılması istenebilir (vize için, doğrula).

## 5. "Almancasız tuzağı": iş için Almanca çoğu zaman şart

İşte kimsenin yeterince söylemediği kısım. İngilizce master **eğitimi** çözer; **kariyeri** tam çözmez:

- **Araştırma / akademi / merkez bankası (kısmen):** İngilizce-dostu. Bonn, Mannheim, DIW, ZEW, ifo gibi ortamlarda İngilizceyle çok yol alırsın.
- **Politika / bakanlık / kamu kurumları:** neredeyse her zaman **Almanca (çoğu kez C1)** ister.
- **Danışmanlık / bankacılık / yerel şirketler:** müşteri ve ekip çoğunlukla Almanca; İngilizce master yeterli olmayabilir.
- **Veri bilimi / analitik:** en İngilizce-dostu çıkış yollarından biri (kantitatif gücün burada avantaj).

**Sonuç:** İngilizce master ile gel, ama **Almancayı paralel öğren.** B1-B2 seviyesi bile iş piyasasında kapı açar; C1 kamu/politika yolunu açar. "Almancasız gelirim, hiç öğrenmem" planı çoğu kariyer için tuzaktır.

## 6. Başvuru & DAAD

- **Başvuru kanalı:** bazı programlar **uni-assist** üzerinden, bazıları **doğrudan üniversiteye**. Her programın sayfasını tek tek kontrol et.
- **Takvim:** kış dönemi (Ekim) için başvurular genelde **kış öncesi ilkbahar-yaz** kapanır; GRE gerekiyorsa çok daha erken başla.
- **DAAD bursları:** yüksek lisans için DAAD çeşitli burslar sunar (geçim + bazen katkı). Rekabetçi; erken başvur. Ayrıntıları **daad.de** üzerinden *doğrula*.

## 7. Sonuç & dürüst tavsiye

Almancasız Almanya'da ekonomi master'ı **gerçekten mümkün** — İngilizce MSc Economics/Quant/Econometrics programları bol ve kamuda **ücretsiz** (BW hariç). Ama iki gerçeği unutma: (1) VWL master'ı **matematik/ekonometri ağırlıklı** ve bazı tepe programlar **GRE** ister; (2) İngilizce eğitim seni sınıfa sokar ama **birçok iş Almanca ister** — Almancayı paralel öğrenmek stratejik zorunluluk. Erken planla, matematik altyapını güçlendir, GRE'yi vaktinde ver ve Almancaya baştan başla.

İlgili: [Almanya'da Ekonomi (VWL) okumak — uluslararası öğrenci rehberi](/tr/blog/studying-economics-vwl-in-germany-as-a-foreigner) · [Almanya'da ekonomist olarak çalışmak — araştırma, politika, finans](/tr/blog/working-as-an-economist-in-germany-research-policy-finance) · [Ekonomi/VWL diplomasıyla ne yapılır — iş piyasası](/tr/blog/what-to-do-with-an-economics-vwl-degree-in-germany-job-market) · [Almancasız İngilizce İşletme (BWL) master programları](/tr/blog/english-taught-business-management-masters-in-germany-without-german) · [Master mı, İş Arama Vizesi mi — iki kariyer anahtarı](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career).

*Bu yazı 2026 başı itibarıyla hazırlanmıştır. Ücretler, başvuru şartları (GRE dâhil), İngilizce/Almanca yeterlik gerekleri ve vize/geçim kuralları değişebilir; başvurmadan önce üniversitelerin resmî sayfalarından ve daad.de'den doğrula.*
MD;

        $deBody = <<<'MD'
Du willst in Deutschland **Volkswirtschaftslehre (VWL, also Economics)** studieren, sprichst aber kein Deutsch? Gute Nachricht: Auf **Master-Ebene gibt es viele englischsprachige Programme**. Die ehrliche Nachricht: Ein englischer Master bringt dich ins Studium, aber für einen Teil deiner Karriere brauchst du trotzdem Deutsch. Dieser Beitrag erklärt den Weg zum Economics-Master ohne Deutsch offen.

## 1. Englische MSc Economics/Quant/Econometrics gibt es reichlich

Beim Bachelor ist es schwierig: Der Economics-Bachelor ist in Deutschland **meist auf Deutsch (C1)**, englische Bachelor sind selten. Auf **Master-Ebene** dreht sich das Bild. Diese Programmtypen sind sehr häufig auf Englisch:

- **MSc Economics** (allgemeine VWL)
- **MSc Quantitative Economics**
- **MSc Econometrics**
- **MSc Economics & Finance**
- **MSc Public Policy / Development Economics**

Die meisten dieser Programme laufen komplett auf Englisch. Etwas Deutsch hilft im Alltag, aber **Lehre und Abschluss** erreichst du ohne Deutsch.

## 2. Gebührenfreie englische Master an staatlichen Unis

Dein größter Vorteil: An **staatlichen Universitäten gibt es keine Studiengebühren.** Du zahlst nur den **Semesterbeitrag** — *Stand 2025/2026, ungefähr, bitte prüfen*: ca. **150–350€/Semester** (evtl. inkl. Semesterticket). Ausnahme Baden-Württemberg: für Nicht-EU-Studierende ca. **1.500€/Semester** (ebenfalls prüfen).

| Universität | Stadt | Bekannter englischer Master | Hinweis (ungefähr, 2026 — prüfen) |
|---|---|---|---|
| **Bonn** | Bonn | MSc Economics | Eine der stärksten VWL-Fakultäten Europas; forschungsstark, evtl. GRE nötig |
| **Mannheim** | Mannheim | MSc Economics | Quantitativ, forschungsstark; meist GRE + Mathematik |
| **LMU München** | München | MSc Economics | Große Fakultät, breites Wahlangebot |
| **Frankfurt (Goethe)** | Frankfurt | MSc Economics / Money & Finance | Nahe EZB & Finanzplatz |
| **Humboldt Berlin** | Berlin | MSc Economics / Statistics | Stark in Ökonometrie/Statistik |
| **Köln** | Köln | MSc Economics | Große VWL-Fakultät |
| **Tübingen** | Tübingen | MSc Economics | Baden-Württemberg → Nicht-EU-Gebühr beachten |

Hinweis: **Bonn und Mannheim** sind in der Forschung sehr stark, aber selektiv. Gebührenfrei heißt nicht "leicht reinzukommen".

## 3. Voraussetzungen: Bachelor + Englisch + oft GRE und Mathematik

Typische Anforderungen (*2025/2026, je Programm unterschiedlich — unbedingt prüfen*):

- **Passender Bachelor:** Economics oder ein quantitatives Fach (Statistik, Mathematik, BWL+VWL usw.).
- **Englischnachweis:** meist **IELTS ~6.5–7.0** oder gleichwertiges **TOEFL**.
- **Mathematik-Grundlage:** Viele englische MSc-Economics-Programme verlangen **Analysis, lineare Algebra, Wahrscheinlichkeit/Statistik** im Transcript. Das ist keine Formalität — der VWL-Master ist **mathe- und ökonometrielastig**.
- **GRE:** Besonders forschungsstarke Programme wie **Bonn und Mannheim** verlangen oft den **GRE (Quant)**. Das ist eine echte Hürde; plane früh.
- Motivationsschreiben, Empfehlungen, teils Lebenslauf.

**Ehrliche Erwartung:** Der VWL-Master steckt voller Ökonometrie, Optimierung und Wahrscheinlichkeit. Wer Mathematik meiden will, ist hier falsch.

## 4. Gebühren: staatlich gebührenfrei, Ausnahme Baden-Württemberg

Zusammengefasst (*2025/2026, ungefähr — prüfen*):

- **Staatliche Uni, die meisten Länder:** **keine** Studiengebühren; nur **~150–350€/Semester** Beitrag.
- **Baden-Württemberg (Stuttgart, Heidelberg, Tübingen, Freiburg, Konstanz...):** für Nicht-EU-Studierende ca. **1.500€/Semester**.
- **Private Unis:** eigene Preise (dieser Beitrag fokussiert auf staatliche).
- **Lebenshaltung:** unabhängig von Gebühren; je nach Stadt monatlich ~**930€+**, evtl. auf einem Sperrkonto nachzuweisen (für das Visum, prüfen).

## 5. Die "ohne Deutsch"-Falle: für den Job ist Deutsch oft Pflicht

Hier kommt der Teil, den kaum jemand deutlich sagt. Ein englischer Master löst das **Studium**, aber nicht die ganze **Karriere**:

- **Forschung / Wissenschaft / Zentralbank (teilweise):** englischfreundlich. Bei Bonn, Mannheim, DIW, ZEW, ifo kommst du auf Englisch weit.
- **Politik / Ministerien / öffentlicher Dienst:** verlangen fast immer **Deutsch (oft C1)**.
- **Beratung / Banking / lokale Unternehmen:** Kunden und Teams sprechen meist Deutsch; ein englischer Master reicht evtl. nicht.
- **Data Science / Analytics:** einer der englischfreundlichsten Wege (deine quantitative Stärke ist hier ein Vorteil).

**Fazit:** Komm mit einem englischen Master, aber **lerne parallel Deutsch.** Schon B1-B2 öffnet Türen im Arbeitsmarkt; C1 öffnet den Weg in Politik/öffentlichen Dienst. Der Plan "ohne Deutsch kommen, nie lernen" ist für die meisten Karrieren eine Falle.

## 6. Bewerbung & DAAD

- **Bewerbungsweg:** Manche Programme laufen über **uni-assist**, andere **direkt bei der Uni**. Prüfe jede Programmseite einzeln.
- **Zeitplan:** Für das Wintersemester (Oktober) schließen Bewerbungen meist im **Frühjahr/Sommer davor**; brauchst du den GRE, fang deutlich früher an.
- **DAAD-Stipendien:** Für den Master bietet der DAAD verschiedene Stipendien (Lebenshaltung + teils Beitrag). Kompetitiv; bewirb dich früh. Details auf **daad.de** *prüfen*.

## 7. Fazit & ehrlicher Rat

Ein Economics-Master ohne Deutsch ist in Deutschland **wirklich machbar** — englische MSc-Economics/Quant/Econometrics-Programme gibt es reichlich und staatlich sind sie **gebührenfrei** (außer BW). Vergiss aber zwei Dinge nicht: (1) Der VWL-Master ist **mathe-/ökonometrielastig**, und einige Top-Programme verlangen den **GRE**; (2) englische Lehre bringt dich in den Hörsaal, aber **viele Jobs verlangen Deutsch** — Deutsch parallel zu lernen ist strategische Pflicht. Plane früh, stärke deine Mathe-Grundlage, mach den GRE rechtzeitig und beginne von Anfang an mit Deutsch.

Verwandt: [VWL/Economics in Deutschland studieren — Leitfaden für Internationale](/de/blog/studying-economics-vwl-in-germany-as-a-foreigner-de) · [Als Ökonom in Deutschland arbeiten — Forschung, Politik, Finanzen](/de/blog/working-as-an-economist-in-germany-research-policy-finance-de) · [Was mit einem VWL-Abschluss anfangen — Arbeitsmarkt](/de/blog/what-to-do-with-an-economics-vwl-degree-in-germany-job-market-de) · [Englischsprachige BWL-Master ohne Deutsch](/de/blog/english-taught-business-management-masters-in-germany-without-german-de) · [Master vs. Job-Seeker-Visum — zwei Karriereschlüssel](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).

*Dieser Beitrag ist Stand Anfang 2026. Gebühren, Zulassungsvoraussetzungen (inkl. GRE), Englisch-/Deutschnachweise sowie Visa- und Lebenshaltungsregeln können sich ändern; prüfe vor der Bewerbung die offiziellen Seiten der Universitäten und daad.de.*
MD;

        $enBody = <<<'MD'
You want to study **economics (VWL — Volkswirtschaftslehre)** in Germany but don't speak German? Good news: at **master's level there are plenty of English-taught programmes**. The honest news: an English master's gets you into the classroom, but part of your career may still need German. This post explains the no-German route to an economics master's straight.

## 1. English MSc Economics/Quant/Econometrics are plentiful

At bachelor level it's hard: the economics bachelor in Germany is **mostly in German (C1)**, and English bachelors are rare. At **master's level** the picture flips. These programme types are very common in English:

- **MSc Economics** (general)
- **MSc Quantitative Economics**
- **MSc Econometrics**
- **MSc Economics & Finance**
- **MSc Public Policy / Development Economics**

Most of these run fully in English. Some German helps in daily life, but you can reach **lectures and graduation without German**.

## 2. Tuition-free English master's at public universities

Your biggest advantage: **public universities charge no tuition.** You only pay the **semester contribution** — *as of 2025/2026, approximate, verify*: about **€150–350/semester** (may include a transport ticket). The one exception is Baden-Württemberg: non-EU students pay roughly **€1,500/semester** (also verify).

| University | City | Notable English master's | Note (approximate, 2026 — verify) |
|---|---|---|---|
| **Bonn** | Bonn | MSc Economics | One of Europe's strongest economics faculties; research-heavy, may require GRE |
| **Mannheim** | Mannheim | MSc Economics | Quantitative, research-strong; usually GRE + mathematics |
| **LMU Munich** | Munich | MSc Economics | Large faculty, broad electives |
| **Frankfurt (Goethe)** | Frankfurt | MSc Economics / Money & Finance | Near the ECB & financial hub |
| **Humboldt Berlin** | Berlin | MSc Economics / Statistics | Strong in econometrics/statistics |
| **Cologne** | Cologne | MSc Economics | Large economics faculty |
| **Tübingen** | Tübingen | MSc Economics | Baden-Württemberg → mind the non-EU fee |

Note: **Bonn and Mannheim** are very strong in research but selective. Tuition-free does not mean "easy to get in".

## 3. Requirements: bachelor + English + sometimes GRE and maths

Typical requirements (*2025/2026, varies by programme — always verify*):

- **Relevant bachelor:** economics or a quantitative field (statistics, mathematics, a business+economics mix, etc.).
- **English proficiency:** usually **IELTS ~6.5–7.0** or an equivalent **TOEFL**.
- **Mathematics background:** many English MSc Economics programmes look for **calculus, linear algebra, probability/statistics** on your transcript. This is not a formality — the VWL master's is **maths- and econometrics-heavy**.
- **GRE:** research-strong programmes such as **Bonn and Mannheim** often require the **GRE (quant)**. It's a real hurdle; plan early.
- Motivation letter, references, sometimes a CV.

**Honest expectation:** the VWL master's is full of econometrics, optimisation and probability. If you want to avoid maths, this is the wrong route.

## 4. Fees: public is tuition-free, the one exception is Baden-Württemberg

In short (*2025/2026, approximate — verify*):

- **Public university, most states:** **no** tuition; only a **~€150–350/semester** contribution.
- **Baden-Württemberg (Stuttgart, Heidelberg, Tübingen, Freiburg, Konstanz...):** roughly **€1,500/semester** for non-EU students.
- **Private universities:** priced separately (this post focuses on public ones).
- **Living costs:** independent of tuition; depending on the city around **€930+/month**, possibly to be shown in a blocked account (for the visa, verify).

## 5. The "no-German" trap: for the job, German is often required

Here's the part few people state clearly. An English master's solves the **study**, not the whole **career**:

- **Research / academia / central bank (partly):** English-friendly. At Bonn, Mannheim, DIW, ZEW, ifo you go far in English.
- **Policy / ministries / public sector:** almost always require **German (often C1)**.
- **Consulting / banking / local firms:** clients and teams mostly speak German; an English master's may not be enough.
- **Data science / analytics:** one of the most English-friendly exits (your quantitative strength is an advantage here).

**Bottom line:** come with an English master's, but **learn German in parallel.** Even B1-B2 opens doors in the job market; C1 opens the policy/public-sector route. The "come without German, never learn it" plan is a trap for most careers.

## 6. Application & DAAD

- **Application channel:** some programmes go through **uni-assist**, others **directly to the university**. Check each programme page individually.
- **Timeline:** for the winter intake (October), applications usually close the **spring/summer before**; if you need the GRE, start much earlier.
- **DAAD scholarships:** for the master's, the DAAD offers various scholarships (living costs + sometimes the contribution). Competitive; apply early. Verify details on **daad.de**.

## 7. Conclusion & honest advice

An economics master's without German is **genuinely doable** in Germany — English MSc Economics/Quant/Econometrics programmes are plentiful and, at public universities, **tuition-free** (except BW). But don't forget two things: (1) the VWL master's is **maths-/econometrics-heavy**, and some top programmes require the **GRE**; (2) English teaching gets you into the lecture hall, but **many jobs require German** — learning German in parallel is a strategic must. Plan early, strengthen your maths foundation, take the GRE in time, and start German from day one.

Related: [Studying economics (VWL) in Germany — international student guide](/en/blog/studying-economics-vwl-in-germany-as-a-foreigner-en) · [Working as an economist in Germany — research, policy, finance](/en/blog/working-as-an-economist-in-germany-research-policy-finance-en) · [What to do with an economics/VWL degree — the job market](/en/blog/what-to-do-with-an-economics-vwl-degree-in-germany-job-market-en) · [English-taught business (BWL) master's without German](/en/blog/english-taught-business-management-masters-in-germany-without-german-en) · [Master's vs the Job-Seeker Visa — two career keys](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

*This post reflects the situation in early 2026. Fees, admission requirements (including the GRE), English/German proficiency rules, and visa/living-cost rules can change; verify on the universities' official pages and daad.de before applying.*
MD;

        $variants = [
            'tr' => ['slug'=>'english-taught-economics-masters-in-germany-without-german',    'title'=>'Almancasız Almanya\'da Ekonomi: İngilizce Master Programları (2026)', 'excerpt'=>'Almancan yok mu? Almanya\'da İngilizce MSc Economics/Quant/Econometrics bol ve kamuda ücretsiz. Bonn/Mannheim GRE ister; iş için Almanca gerçeği. Şartlar, ücret ve dürüst tavsiye.', 'meta_title'=>'Almancasız İngilizce Ekonomi Master Programları — Almanya (2026)', 'meta_description'=>'Almanya\'da İngilizce ders veren MSc Economics/Quantitative/Econometrics programları, ücretsiz kamu üniversiteleri, GRE/matematik şartı ve Almanca gerçeği (2026).', 'body'=>$trBody],
            'de' => ['slug'=>'english-taught-economics-masters-in-germany-without-german-de', 'title'=>'Economics ohne Deutsch: Englische Master in Deutschland (2026)',        'excerpt'=>'Kein Deutsch? In Deutschland gibt es viele englische MSc Economics/Quant/Econometrics — staatlich gebührenfrei. Bonn/Mannheim verlangen GRE; für Jobs oft Deutsch nötig.',   'meta_title'=>'Englische Economics-Master ohne Deutsch — Deutschland (2026)',  'meta_description'=>'Englischsprachige MSc Economics/Quantitative/Econometrics in Deutschland, gebührenfreie staatliche Unis, GRE/Mathe-Voraussetzung und die Deutsch-Realität (2026).',   'body'=>$deBody],
            'en' => ['slug'=>'english-taught-economics-masters-in-germany-without-german-en', 'title'=>'Economics Without German: English Master\'s in Germany (2026)',        'excerpt'=>'No German? Germany has plenty of English MSc Economics/Quant/Econometrics — tuition-free at public unis. Bonn/Mannheim want the GRE; jobs often need German.',   'meta_title'=>'English-Taught Economics Master\'s Without German — Germany (2026)',  'meta_description'=>'English-taught MSc Economics/Quantitative/Econometrics in Germany, tuition-free public universities, GRE/maths requirements, and the German-language reality (2026).',   'body'=>$enBody],
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
            'english-taught-economics-masters-in-germany-without-german',
            'english-taught-economics-masters-in-germany-without-german-de',
            'english-taught-economics-masters-in-germany-without-german-en',
        ])->delete();
    }
};

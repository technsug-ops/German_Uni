<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Studying Economics (VWL) in Germany as an international student (2026).
 * Doğrulandı: VWL = Volkswirtschaftslehre (iktisat), BWL'den farklı (makro/mikro/ekonometri/politika),
 * matematik/istatistik ağırlıklı; bachelor genelde Almanca (C1), İngilizce master bol; tepe: Bonn/Mannheim/LMU/Köln.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c1e10000-1111-4ec0-9f40-cc01dd03ff01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da **ekonomi** okumak istiyorsan, ilk öğrenmen gereken şey bir isim: **VWL — Volkswirtschaftslehre**. Bu, bizim "iktisat" dediğimiz alandır ve Almanya'da **işletme (BWL)**'den ayrı, kendi başına bir bölümdür. İkisini karıştırmak en sık yapılan hatadır — çünkü müfredat, kariyer ve hatta okunacak üniversiteler farklıdır. Bu rehber, uluslararası bir öğrenci olarak Almanya'da VWL okumanın ne demek olduğunu dürüstçe anlatıyor.

## VWL nedir, BWL'den farkı ne?
**VWL (Volkswirtschaftslehre)** ekonominin bütününü inceler: piyasalar, arz-talep, enflasyon, işsizlik, büyüme, para politikası, uluslararası ticaret. Yani **makroekonomi + mikroekonomi + ekonometri + iktisat politikası**. **BWL (Betriebswirtschaftslehre)** ise tek bir *firmanın* içine bakar: muhasebe, pazarlama, finans, yönetim.

Basit ayrım: **VWL "neden faiz artıyor?" sorusunu sorar; BWL "şirketim bu bilançoyu nasıl yönetir?" sorusunu sorar.** İkisi de değerli, ama VWL çok daha **analitik ve teorik**. Eğer amacın kurumsal bir şirkette hızlı iş bulmaksa BWL daha doğrudan olabilir; eğer merkez bankası, araştırma enstitüsü, politika veya veri/analitik ilgini çekiyorsa VWL senin alanın.

## Matematik & ekonometri ağırlığı (dürüst beklenti)
Burada dürüst olmam gerek: **Almanya'da VWL matematik ve istatistik ağırlıklıdır.** Popüler kültürdeki "ekonomi = tartışma dersi" imajını unut. İlk yıllardan itibaren şunlarla karşılaşırsın:

- **Analiz (calculus)** ve **lineer cebir**
- **Olasılık ve istatistik**
- **Ekonometri** (verilerle iktisat modeli tahmini — regresyon, hipotez testi)
- Giderek **kodlama/veri araçları** (R, Stata, Python)

Bonn, Mannheim gibi araştırma-güçlü bölümlerde matematik dozu daha da yüksektir. Eğer sayılardan kaçıyorsan VWL seni zorlar; ama bu **kantitatif güç** aynı zamanda VWL mezununu veri bilimi ve finansta çekici kılan şeydir. Lise/lisans matematiğini tazelemek, başlamadan önce yapabileceğin en iyi yatırımdır.

## Bachelor (Almanca) vs İngilizce master
Yapıyı net anla:

- **Bachelor (lisans):** Almanya'da VWL lisansı genelde **Almanca** verilir ve **C1 seviyesi** beklenir. İngilizce lisans nadirdir. Yani lisans için Almanca neredeyse şart.
- **Master (yüksek lisans):** Burada tablo değişir — **İngilizce yüksek lisans BOLDUR.** MSc Economics, Quantitative Economics, Econometrics, Economics & Finance, Public Policy, Development Economics gibi programların çoğu tamamen İngilizce.

Pratik strateji: Almancan yoksa, **lisansı ülkende/İngilizce bir yerde bitirip yüksek lisans için Almanya'ya gelmek** çok daha gerçekçi bir yoldur. Almancasız İngilizce master rotasını [ayrı bir yazıda](/tr/blog/english-taught-economics-masters-in-germany-without-german) detaylı anlattım.

## Tepe ekonomi üniversiteleri
Almanya'nın ekonomi bölümleri Avrupa'nın en güçlüleri arasında. Öne çıkanlar:

| Üniversite | Öne çıkan yön | Not |
|---|---|---|
| **Bonn** | Araştırma-güçlü, teorik derinlik | Almanya/Avrupa'nın en iyi iktisat bölümlerinden; BGSE doktora okulu |
| **Mannheim** | Kantitatif, ekonometri | ZEW enstitüsüne yakın; bazı programlar GRE ister |
| **LMU München** | Geniş yelpaze, güçlü fakülte | ifo enstitüsü Münih'te |
| **Köln** | Uygulamalı iktisat, ekonomi politikası | Büyük ve köklü |
| **Frankfurt (Goethe)** | Finans + iktisat, ECB yakınlığı | Finansa yönelenler için ideal konum |
| **Humboldt Berlin / Tübingen** | Araştırma & teori | DIW (Berlin) yakınlığı |

*Not: sıralamalar yıldan yıla değişir; başvurmadan önce güncel program sayfasını doğrula.* Bonn ve Mannheim özellikle **araştırma/doktora** hedefleyenler için en güçlü adreslerdir.

## Başvuru: uni-assist / doğrudan ve NC
Başvuru iki yoldan yürür:

- **uni-assist:** Birçok üniversite uluslararası başvuruları merkezi **uni-assist** platformu üzerinden toplar (belge ön-değerlendirme + VPD notu). Ücretlidir (ilk başvuru ~75€, sonrakiler daha ucuz — *2025/2026 itibarıyla, doğrula*).
- **Doğrudan:** Bazı üniversiteler kendi portalından başvuru alır.

**NC (Numerus Clausus):** Popüler bölümlerde kontenjan sınırlıdır ve **not ortalaması eşiği (NC)** uygulanır. VWL lisansı bazı üniversitelerde NC'li, bazılarında serbest olabilir; master başvurusunda ise lisans notun, ilgili matematik/istatistik dersleri ve bazen **GRE** belirleyicidir. Yüksek lisans mı yoksa iş arama vizesi mi sorusunu tartıyorsan [master vs job-seeker vizesi karşılaştırmasına](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) bak.

## Ücret & burs (DAAD)
İyi haber: **kamu (devlet) üniversitelerinde eğitim büyük ölçüde ücretsizdir.** Sadece **dönem katkı payı** ödersin: yaklaşık **150–350€/dönem** (semester ticket dahil olabilir). **İstisna: Baden-Württemberg**, AB-dışı öğrencilerden dönem başına yaklaşık **1.500€** alır (*2025/2026 itibarıyla, yaklaşık; doğrula*).

Burslar tarafında en bilinen adres **DAAD**'dir; ayrıca siyasi vakıf bursları (Konrad-Adenauer, Friedrich-Ebert vb.) ve üniversite kaynaklı destekler vardır. Geçim için ayrıca **Blocked Account (Sperrkonto)** göstermen gerekir (2026 için yıllık tutar güncellenir — doğrula).

## Sonuç & dürüst tavsiye
Almanya'da VWL, **düşünen ve sayılarla barışık** öğrenciler için mükemmel bir seçim. Ama üç şeyi baştan kabul et: (1) VWL, BWL değildir — **teorik ve matematiksel**; (2) lisans genelde **Almanca**, bu yüzden gerçekçi yol çoğu uluslararası öğrenci için **İngilizce master**; (3) kariyerin büyük kısmı (araştırma, merkez bankası, veri) kantitatif güç ister, politika/kurumsal roller ise **Almanca** ister.

Bir sonraki adım için kümedeki diğer yazılara göz at: [Almancasız İngilizce ekonomi master programları](/tr/blog/english-taught-economics-masters-in-germany-without-german), [Almanya'da ekonomist olarak çalışmak](/tr/blog/working-as-an-economist-in-germany-research-policy-finance) ve [VWL diplomasıyla iş piyasası](/tr/blog/what-to-do-with-an-economics-vwl-degree-in-germany-job-market). İşletme tarafını merak ediyorsan [BWL işletme diplomasıyla ne yapılır](/tr/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market) yazısı da faydalı.

*Bu yazı 2026 başı itibarıyla hazırlanmıştır. Harçlar, NC eşikleri, program dilleri ve vize/burs kuralları değişebilir; başvurudan önce üniversitenin ve resmi kurumların güncel bilgilerini mutlaka doğrula.*
MD;

        $deBody = <<<'MD'
Wenn du in Deutschland **Wirtschaft** studieren willst, musst du zuerst einen Namen kennen: **VWL — Volkswirtschaftslehre**. Das ist ein eigenständiges Studienfach und **unterscheidet sich klar von der BWL (Betriebswirtschaftslehre)**. Die beiden zu verwechseln ist der häufigste Fehler, denn Lehrplan, Karriere und sogar die passenden Universitäten sind unterschiedlich. Dieser Leitfaden erklärt dir ehrlich, was es bedeutet, als internationaler Student VWL in Deutschland zu studieren.

## Was ist VWL, und wie unterscheidet sie sich von BWL?
Die **VWL (Volkswirtschaftslehre)** untersucht die Wirtschaft als Ganzes: Märkte, Angebot und Nachfrage, Inflation, Arbeitslosigkeit, Wachstum, Geldpolitik, internationalen Handel. Also **Makroökonomie + Mikroökonomie + Ökonometrie + Wirtschaftspolitik**. Die **BWL (Betriebswirtschaftslehre)** schaut dagegen in das einzelne *Unternehmen*: Rechnungswesen, Marketing, Finanzen, Management.

Einfach gesagt: **Die VWL fragt „Warum steigen die Zinsen?", die BWL fragt „Wie steuert mein Unternehmen seine Bilanz?"** Beide sind wertvoll, aber die VWL ist deutlich **analytischer und theoretischer**. Wenn dein Ziel ein schneller Job in einem Unternehmen ist, kann BWL direkter sein. Wenn dich Zentralbank, Forschungsinstitute, Politik oder Daten/Analytik reizen, ist die VWL dein Fach.

## Mathematik & Ökonometrie (ehrliche Erwartung)
Hier muss ich ehrlich sein: **VWL ist in Deutschland stark mathematik- und statistiklastig.** Vergiss das Klischee „Wirtschaft = Diskussionsfach". Schon ab den ersten Semestern begegnen dir:

- **Analysis (Calculus)** und **lineare Algebra**
- **Wahrscheinlichkeitsrechnung und Statistik**
- **Ökonometrie** (Schätzung ökonomischer Modelle aus Daten — Regression, Hypothesentests)
- zunehmend **Programmierung/Datentools** (R, Stata, Python)

An forschungsstarken Fakultäten wie Bonn oder Mannheim ist die Mathe-Dosis noch höher. Wenn du Zahlen aus dem Weg gehst, wird dich die VWL fordern — aber genau diese **quantitative Stärke** macht VWL-Absolventen in Data Science und Finance attraktiv. Deine Schul- und Bachelor-Mathematik aufzufrischen ist die beste Investition, bevor du anfängst.

## Bachelor (Deutsch) vs. englischsprachiger Master
Verstehe die Struktur genau:

- **Bachelor:** Der VWL-Bachelor wird in Deutschland meist auf **Deutsch** unterrichtet, und **C1-Niveau** wird erwartet. Englischsprachige Bachelor sind selten. Für den Bachelor ist Deutsch also fast Pflicht.
- **Master:** Hier dreht sich das Bild — **englischsprachige Master gibt es reichlich.** MSc Economics, Quantitative Economics, Econometrics, Economics & Finance, Public Policy, Development Economics: Die meisten davon sind komplett auf Englisch.

Praktische Strategie: Wenn du kein Deutsch kannst, ist es viel realistischer, **den Bachelor in deinem Land (oder auf Englisch) abzuschließen und für den Master nach Deutschland zu kommen**. Die englischsprachige Master-Route ohne Deutsch habe ich in einem [eigenen Beitrag](/de/blog/english-taught-economics-masters-in-germany-without-german-de) ausführlich beschrieben.

## Top-Universitäten für Wirtschaft
Die deutschen VWL-Fakultäten gehören zu den stärksten Europas. Die wichtigsten:

| Universität | Schwerpunkt | Hinweis |
|---|---|---|
| **Bonn** | Forschungsstark, theoretische Tiefe | Eine der besten VWL-Fakultäten Deutschlands/Europas; BGSE-Promotionsschule |
| **Mannheim** | Quantitativ, Ökonometrie | Nahe am ZEW-Institut; einige Programme verlangen GRE |
| **LMU München** | Breites Spektrum, starke Fakultät | ifo-Institut in München |
| **Köln** | Angewandte VWL, Wirtschaftspolitik | Groß und traditionsreich |
| **Frankfurt (Goethe)** | Finance + VWL, Nähe zur EZB | Ideal für den Finanzweg |
| **Humboldt Berlin / Tübingen** | Forschung & Theorie | DIW (Berlin) in der Nähe |

*Hinweis: Rankings ändern sich jährlich; prüfe vor der Bewerbung die aktuelle Programmseite.* Bonn und Mannheim sind besonders für alle stark, die **Forschung/Promotion** anstreben.

## Bewerbung: uni-assist / direkt und NC
Die Bewerbung läuft auf zwei Wegen:

- **uni-assist:** Viele Universitäten bündeln internationale Bewerbungen über die zentrale Plattform **uni-assist** (Vorprüfung der Unterlagen + VPD-Note). Das ist kostenpflichtig (erste Bewerbung ca. 75€, weitere günstiger — *Stand 2025/2026, prüfe das*).
- **Direkt:** Manche Universitäten nehmen Bewerbungen über ihr eigenes Portal an.

**NC (Numerus Clausus):** In beliebten Fächern ist die Zahl der Plätze begrenzt, und es gilt eine **Notengrenze (NC)**. Der VWL-Bachelor ist an manchen Unis NC-beschränkt, an anderen zulassungsfrei; bei der Master-Bewerbung zählen deine Bachelor-Note, die relevanten Mathe-/Statistik-Kurse und manchmal der **GRE**. Wenn du zwischen Master und Job-Seeker-Visum abwägst, schau dir den [Vergleich Master vs. Job-Seeker-Visum](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de) an.

## Gebühren & Stipendien (DAAD)
Die gute Nachricht: **An staatlichen Universitäten ist das Studium weitgehend kostenlos.** Du zahlst nur den **Semesterbeitrag**: etwa **150–350€/Semester** (Semesterticket oft inklusive). **Ausnahme: Baden-Württemberg** verlangt von Nicht-EU-Studierenden rund **1.500€ pro Semester** (*Stand 2025/2026, ungefähr; prüfe das*).

Bei den Stipendien ist der bekannteste Anlaufpunkt der **DAAD**; dazu kommen Stipendien politischer Stiftungen (Konrad-Adenauer, Friedrich-Ebert usw.) und universitätseigene Förderungen. Für deinen Lebensunterhalt musst du außerdem ein **Sperrkonto** nachweisen (der Jahresbetrag für 2026 wird angepasst — prüfe das).

## Fazit & ehrlicher Rat
VWL in Deutschland ist eine hervorragende Wahl für Studierende, die **gerne denken und mit Zahlen umgehen können**. Aber akzeptiere von Anfang an drei Dinge: (1) VWL ist nicht BWL — sie ist **theoretisch und mathematisch**; (2) der Bachelor ist meist auf **Deutsch**, deshalb ist der realistische Weg für die meisten Internationalen der **englischsprachige Master**; (3) ein großer Teil der Karriere (Forschung, Zentralbank, Daten) verlangt quantitative Stärke, während Politik/Unternehmensrollen **Deutsch** verlangen.

Für den nächsten Schritt schau dir die anderen Beiträge dieser Reihe an: [Englischsprachige Economics-Master ohne Deutsch](/de/blog/english-taught-economics-masters-in-germany-without-german-de), [Als Ökonom in Deutschland arbeiten](/de/blog/working-as-an-economist-in-germany-research-policy-finance-de) und [Der Arbeitsmarkt mit einem VWL-Abschluss](/de/blog/what-to-do-with-an-economics-vwl-degree-in-germany-job-market-de). Wenn dich die betriebswirtschaftliche Seite interessiert, ist auch [Was tun mit einem BWL-Abschluss](/de/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market-de) hilfreich.

*Dieser Beitrag wurde Anfang 2026 erstellt. Gebühren, NC-Grenzen, Programmsprachen sowie Visa- und Stipendienregeln können sich ändern; prüfe vor der Bewerbung unbedingt die aktuellen Angaben der Universität und der offiziellen Stellen.*
MD;

        $enBody = <<<'MD'
If you want to study **economics** in Germany, the first thing to learn is a name: **VWL — Volkswirtschaftslehre**. This is what we call "economics," and in Germany it is a standalone degree, **distinct from business studies (BWL)**. Confusing the two is the most common mistake, because the curriculum, the careers, and even the best universities are different. This guide honestly explains what it means to study VWL in Germany as an international student.

## What is VWL, and how is it different from BWL?
**VWL (Volkswirtschaftslehre)** studies the economy as a whole: markets, supply and demand, inflation, unemployment, growth, monetary policy, international trade. In other words, **macroeconomics + microeconomics + econometrics + economic policy**. **BWL (Betriebswirtschaftslehre)**, by contrast, looks inside a single *firm*: accounting, marketing, finance, management.

Simple distinction: **VWL asks "why are interest rates rising?"; BWL asks "how does my company manage its balance sheet?"** Both are valuable, but VWL is far more **analytical and theoretical**. If your goal is a fast corporate job, BWL may be more direct; if central banking, research institutes, policy, or data/analytics excite you, VWL is your field.

## Math & econometrics load (an honest expectation)
Here I need to be honest: **VWL in Germany is heavy on mathematics and statistics.** Forget the pop-culture image of "economics = a discussion class." From the very first semesters you will meet:

- **Calculus** and **linear algebra**
- **Probability and statistics**
- **Econometrics** (estimating economic models from data — regression, hypothesis testing)
- increasingly **coding/data tools** (R, Stata, Python)

At research-strong faculties like Bonn or Mannheim the math dose is even higher. If you avoid numbers, VWL will challenge you — but that very **quantitative strength** is what makes VWL graduates attractive in data science and finance. Refreshing your high-school and bachelor-level math is the best investment you can make before you start.

## Bachelor (in German) vs. English-taught master
Understand the structure clearly:

- **Bachelor:** The VWL bachelor in Germany is usually taught **in German**, and **C1 level** is expected. English-taught bachelors are rare. So for the bachelor, German is almost mandatory.
- **Master:** Here the picture flips — **English-taught masters are plentiful.** MSc Economics, Quantitative Economics, Econometrics, Economics & Finance, Public Policy, Development Economics: most of these are fully in English.

Practical strategy: if you have no German, it is far more realistic to **finish the bachelor in your own country (or in English) and come to Germany for the master**. I describe the English-taught, no-German route in detail in a [separate post](/en/blog/english-taught-economics-masters-in-germany-without-german-en).

## Top universities for economics
Germany's economics faculties are among the strongest in Europe. The standouts:

| University | Focus | Note |
|---|---|---|
| **Bonn** | Research-strong, theoretical depth | One of the best economics faculties in Germany/Europe; BGSE doctoral school |
| **Mannheim** | Quantitative, econometrics | Close to the ZEW institute; some programs require GRE |
| **LMU Munich** | Broad spectrum, strong faculty | ifo institute in Munich |
| **Cologne (Köln)** | Applied economics, economic policy | Large and well established |
| **Frankfurt (Goethe)** | Finance + economics, near the ECB | Ideal location for the finance path |
| **Humboldt Berlin / Tübingen** | Research & theory | DIW (Berlin) nearby |

*Note: rankings shift year to year; verify the current program page before applying.* Bonn and Mannheim are especially strong for anyone aiming at **research/PhD**.

## Applying: uni-assist / direct, and the NC
Applications run through two channels:

- **uni-assist:** Many universities collect international applications through the central **uni-assist** platform (document pre-check + VPD grade). It charges a fee (first application ~€75, additional ones cheaper — *as of 2025/2026, verify*).
- **Direct:** Some universities take applications through their own portal.

**NC (Numerus Clausus):** In popular subjects the number of places is limited, and a **grade threshold (NC)** applies. The VWL bachelor is NC-restricted at some universities and open at others; for master applications, your bachelor grade, the relevant math/statistics courses, and sometimes the **GRE** are decisive. If you are weighing a master against a job-seeker visa, see the [master vs. job-seeker visa comparison](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

## Fees & scholarships (DAAD)
The good news: **at public universities, tuition is largely free.** You pay only the **semester contribution**: roughly **€150–350 per semester** (a semester transport ticket may be included). **Exception: Baden-Württemberg** charges non-EU students around **€1,500 per semester** (*as of 2025/2026, approximate; verify*).

On the scholarship side, the best-known address is the **DAAD**; there are also political-foundation scholarships (Konrad-Adenauer, Friedrich-Ebert, etc.) and university-run support. To cover living costs you will also need to show a **blocked account (Sperrkonto)** (the annual amount for 2026 is updated — verify).

## Conclusion & honest advice
VWL in Germany is an excellent choice for students who **enjoy thinking and are comfortable with numbers**. But accept three things from the start: (1) VWL is not BWL — it is **theoretical and mathematical**; (2) the bachelor is usually **in German**, so for most internationals the realistic route is an **English-taught master**; (3) a large part of the career (research, central banking, data) demands quantitative strength, while policy/corporate roles demand **German**.

For your next step, look at the other posts in this series: [English-taught economics masters without German](/en/blog/english-taught-economics-masters-in-germany-without-german-en), [working as an economist in Germany](/en/blog/working-as-an-economist-in-germany-research-policy-finance-en), and [the job market with a VWL degree](/en/blog/what-to-do-with-an-economics-vwl-degree-in-germany-job-market-en). If you are curious about the business side, [what to do with a BWL degree](/en/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market-en) is also useful.

*This post was prepared in early 2026. Fees, NC thresholds, program languages, and visa/scholarship rules can change; always verify the current information from the university and official authorities before applying.*
MD;

        $variants = [
            'tr' => ['slug'=>'studying-economics-vwl-in-germany-as-a-foreigner',    'title'=>'Almanya\'da Ekonomi (VWL) Okumak: Uluslararası Öğrenci Rehberi (2026)', 'excerpt'=>'Almanya\'da VWL (Volkswirtschaftslehre) okumak: BWL\'den farkı, matematik/ekonometri ağırlığı, Almanca lisans vs İngilizce master, Bonn/Mannheim/LMU gibi tepe üniversiteler, uni-assist başvurusu, NC, ücretler ve DAAD bursu — uluslararası öğrenciler için dürüst rehber.', 'meta_title'=>'Almanya\'da Ekonomi (VWL) Okumak: Öğrenci Rehberi 2026', 'meta_description'=>'Almanya\'da VWL/ekonomi okumak: BWL\'den farkı, matematik yükü, Almanca lisans vs İngilizce master, tepe üniversiteler, başvuru, ücret ve DAAD bursu (2026).', 'body'=>$trBody],
            'de' => ['slug'=>'studying-economics-vwl-in-germany-as-a-foreigner-de', 'title'=>'VWL in Deutschland studieren: Leitfaden für internationale Studierende (2026)', 'excerpt'=>'VWL (Volkswirtschaftslehre) in Deutschland studieren: Unterschied zur BWL, Mathe-/Ökonometrie-Last, deutscher Bachelor vs. englischsprachiger Master, Top-Unis wie Bonn/Mannheim/LMU, uni-assist, NC, Gebühren und DAAD-Stipendien — ein ehrlicher Leitfaden.', 'meta_title'=>'VWL in Deutschland studieren: Leitfaden 2026', 'meta_description'=>'VWL/Wirtschaft in Deutschland studieren: Unterschied zur BWL, Mathe-Last, deutscher Bachelor vs. englischer Master, Top-Unis, Bewerbung, Gebühren, DAAD (2026).', 'body'=>$deBody],
            'en' => ['slug'=>'studying-economics-vwl-in-germany-as-a-foreigner-en', 'title'=>'Studying Economics (VWL) in Germany: A Guide for International Students (2026)', 'excerpt'=>'Studying VWL (Volkswirtschaftslehre) in Germany: how it differs from BWL, the math/econometrics load, German-taught bachelor vs. English-taught master, top universities like Bonn/Mannheim/LMU, uni-assist applications, the NC, fees, and DAAD scholarships — an honest guide.', 'meta_title'=>'Studying Economics (VWL) in Germany: 2026 Guide', 'meta_description'=>'Study VWL/economics in Germany: difference from BWL, math load, German bachelor vs. English master, top universities, applications, fees, and DAAD scholarships (2026).', 'body'=>$enBody],
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
        Post::whereIn('slug', ['studying-economics-vwl-in-germany-as-a-foreigner', 'studying-economics-vwl-in-germany-as-a-foreigner-de', 'studying-economics-vwl-in-germany-as-a-foreigner-en'])->delete();
    }
};

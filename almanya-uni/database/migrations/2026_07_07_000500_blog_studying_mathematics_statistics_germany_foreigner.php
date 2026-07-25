<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Matematik / İstatistik okumak — uluslararası öğrenci rehberi (2026).
 * Doğrulandı: Bachelor genelde Almanca (C1), İngilizce master bol (Applied/Financial/Actuarial/Statistics);
 * kamu ücretsiz (~150–350€/dönem, BW non-EU ~1.500€); tepe: Bonn (Hausdorff), Münster, TU/LMU München, Göttingen;
 * matematik soyut/zor ama çok istihdam edilebilir. Sayılar yıl-hedge'li (2025/2026, doğrula).
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'b2c10000-1111-4eae-9ff0-bb09cc0eee01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Matematik zor bir bölüm, okumaya değer mi?" Almanya bağlamında dürüst yanıt şu: **matematik gerçekten soyut ve zorludur, ama aynı zamanda Almanya'da en çok istihdam edilebilir diplomalardan birini verir.** İşin sırrı bölümün kendisinde değil, onu nasıl bir kariyere bağladığınızda — quant, aktüerya (Aktuar), veri bilimi. Bu yazı Almanya'da yabancı bir öğrenci olarak matematik/istatistik okumanın gerçeklerini anlatır.

## 1. Alan kapsamı: saf matematik, uygulamalı matematik ve istatistik
Almanya'da "Mathematik" tek bir şey değildir. Kabaca üç yön vardır:

- **Saf matematik (reine Mathematik):** cebir, analiz, topoloji, sayı teorisi — soyut, ispat ağırlıklı, akademiye ve araştırmaya yakın.
- **Uygulamalı matematik (angewandte Mathematik):** sayısal analiz (Numerik), optimizasyon, diferansiyel denklemler, bilimsel hesaplama — mühendislik ve endüstriyle iç içe.
- **İstatistik & stokastik:** olasılık, matematiksel istatistik, veri modelleme — veri bilimi, sigorta ve finansın temeli.

Buna ek olarak birçok üniversite **finans/aktüerya matematiği (Finanz- und Versicherungsmathematik)** ve **Wirtschaftsmathematik (ekonomi matematiği)** gibi uygulamalı dallar sunar. Ayrıca istatistik bazı üniversitelerde ayrı bir bölüm (ör. TU Dortmund'da köklü bir Statistik bölümü vardır), bazılarında ise matematik bölümü içinde bir uzmanlık olarak bulunur. Yön seçiminiz ilerideki kariyerinizi büyük ölçüde belirler; örneğin saf matematik sizi akademiye, uygulamalı ve istatistik ise doğrudan endüstriye taşır. Bu ayrımı [Almanya'da matematik diplomasıyla iş piyasası](/tr/blog/what-to-do-with-a-mathematics-degree-in-germany-job-market) yazısında ayrıntısıyla ele alıyoruz.

## 2. Bachelor Almanca, master çoğu zaman İngilizce
Kritik bir dil gerçeği: **lisans (bachelor) programları neredeyse tamamen Almancadır ve genelde C1 Almanca ister.** İngilizce lisans matematik programı çok nadirdir. Buna karşılık **yüksek lisans (master) tarafında İngilizce bir dünya açılır** — MSc Mathematics, Applied Mathematics, **Financial/Actuarial Mathematics**, Statistics, Mathematical Modelling, Scientific Computing gibi programların önemli bir kısmı İngilizce yürütülür.

Bu, uluslararası öğrenciler için pratik bir yol haritası verir: Almancanız güçlü değilse **İngilizce master** en gerçekçi giriş kapısıdır. Almancasız master rotasını [Almancasız İngilizce Matematik/İstatistik master programları](/tr/blog/english-taught-mathematics-statistics-masters-in-germany-without-german) yazısında derledik.

## 3. Soyutluk ve zorluk konusunda dürüstlük — ama istihdam gücü yüksek
Şeker kaplamayalım: **Almanya'da matematik lisansı zorludur.** Özellikle ilk yıllarda Analiz I–II ve Lineer Cebir dersleri ispat kültürüne dayanır; lisede "işlem yapma" olarak öğrenilen matematikten çok farklıdır. Artık amaç bir sonucu hesaplamak değil, onun neden doğru olduğunu titizlikle kanıtlamaktır. Bırakma (dropout) oranları yüksektir; birçok öğrenci ilk iki dönemin ağır tempoya ve ispat mantığına uyum sağlayamadığı için ayrılır. Bu bir uyarı değil, bir gerçek: soyut düşünmeye ve ispata hazır olmadan gelmek hayal kırıklığı yaratır. İyi haber, bu beceriyi baştan çalışmaya niyetli olan herkesin geliştirebilmesidir.

> **Gerçeklik kontrolü:** Matematik "kolay iş garantisi" değildir; zorlu bir bölümdür. Ama bitirenler için tablo çok olumludur — çünkü matematikçiler analitik, modelleme ve problem çözme becerileriyle **finans (quant/risk), sigorta (aktüer), veri bilimi, IT, danışmanlık ve araştırma** gibi alanlarda yüksek talep görür.

Kariyer ve maaş tarafını [matematik diplomasıyla Almanya'da çalışmak: quant, aktüer, veri](/tr/blog/working-with-a-mathematics-degree-in-germany-quant-actuary-data-salary) yazısında rakamlarla anlatıyoruz. Özetle: bölüm soyut, ama çıktı çok somut.

## 4. Tepe bölümler: Bonn, Münster, München, Göttingen
Almanya matematik geleneğinin merkezidir (Gauss, Hilbert, Noether geleneği). Birkaç bölüm uluslararası düzeyde öne çıkar. Aşağıdaki tablo **2025/2026 itibarıyla genel bir yönelim sunar; program listesini ilgili üniversiteden doğrulayın.**

| Üniversite | Öne çıkan yön | Not |
|---|---|---|
| **Bonn** | Saf & uygulamalı matematik | **Hausdorff Center** — matematik mükemmeliyet merkezi |
| **Münster** | Saf matematik, geometri | **Mathematics Münster** — excellence kümesi |
| **TU München / LMU München** | Uygulamalı, finans, istatistik | Güçlü endüstri bağı |
| **Göttingen** | Tarihi matematik merkezi | Köklü gelenek |
| **Heidelberg / KIT / TU Berlin** | Uygulamalı, bilimsel hesaplama | Güçlü İngilizce master seçenekleri |
| Aachen / Bielefeld / Freiburg | Uygulamalı & istatistik | İyi araştırma altyapısı |

Not: "Hangi üniversite daha prestijli?" sorusu Almanya'da düşündüğünüzden farklı işler — devlet üniversiteleri arasındaki fark ABD/İngiltere'deki kadar keskin değildir. Bunu [Almanya'da üniversite prestiji ve sıralamalar nasıl işler](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) yazısında açıklıyoruz.

## 5. Başvuru: uni-assist ve NC gerçeği
AB-dışı (örneğin Türk) bir aday için tipik yol **uni-assist** üzerinden başvurudur. Matematik başvurusunda dikkat edilecekler:

1. **Denklik:** Lise diplomanız doğrudan üniversite girişi (Hochschulzugangsberechtigung) saymıyorsa, önce **Studienkolleg (T-Kurs — teknik/matematik odaklı)** gerekebilir; master için ise güçlü bir matematik lisansı esastır.
2. **NC:** Matematik bölümleri çoğu zaman **NC'siz (zulassungsfrei)** veya rahat kontenjanlıdır — tıp/hukukun aksine matematiğe yer bulmak genelde kolaydır. Asıl zorluk girmek değil, **bitirmektir.**
3. **Master şartları:** İngilizce programlarda güçlü matematik altyapısı, İngilizce belgesi (IELTS/TOEFL) ve **bazı programlarda GRE** istenir.

Başvuru son tarihleri kış dönemi için genelde **15 Temmuz**, yaz dönemi için **15 Ocak** civarındadır (üniversiteye göre değişir, doğrulayın). Master mı yoksa başka bir vize yolu mu size uygun — bu stratejik seçimi [Almanya'da master vs iş arama vizesi](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) yazısında karşılaştırdık.

## 6. Ücret ve burs
Devlet üniversitelerinde matematik/istatistik kural olarak **öğrenim ücreti almaz**; yalnızca dönemlik **Semesterbeitrag** ödenir. Baden-Württemberg eyaleti AB-dışı öğrencilerden dönemlik ücret alır. Aşağıdaki rakamlar **2025/2026 itibarıyla yaklaşıktır; başvurudan önce doğrulayın.**

| Kalem | Yaklaşık tutar (2025/2026, doğrula) |
|---|---|
| Devlet üniversitesi öğrenim ücreti | Yok (yalnızca Semesterbeitrag) |
| Semesterbeitrag | ~150–350 € / dönem |
| Baden-Württemberg (AB-dışı) | ~1.500 € / dönem |
| Yaşam masrafı (vize blokeli hesap) | ~11.900 € / yıl civarı, şehre göre değişir |

Burs tarafında **DAAD** ve **Deutschlandstipendium** başlıca seçeneklerdir; ayrıca bazı excellence kümeleri (Bonn, Münster) yetenekli master öğrencilerine burs/asistanlık sunar. Vize için genellikle bir yıllık yaşam masrafını kanıtlayan **bloke hesap (Sperrkonto)** ve geçerli **sağlık sigortası** gerekir.

## Sonuç & dürüst tavsiye
Dürüst tavsiye: **matematikten korkmayın ama hafife de almayın.** Soyut ve zorlu bir bölümdür; ispat kültürüne ve düzenli çalışmaya hazır gelmelisiniz. Buna karşılık, bitirdiğinizde Almanya'da **en istihdam edilebilir profillerden birine** sahip olursunuz — özellikle finans, aktüerya ve veri yönünde uzmanlaşırsanız.

Uluslararası öğrenci için en gerçekçi rota çoğu zaman şudur: Almancanız güçlüyse lisansa girin; değilse **İngilizce bir master** ile başlayın ve erkenden bir kariyer yönü (quant/aktüer/veri) seçin. Devamı için: [Almancasız İngilizce master programları](/tr/blog/english-taught-mathematics-statistics-masters-in-germany-without-german), [matematik diplomasıyla çalışmak](/tr/blog/working-with-a-mathematics-degree-in-germany-quant-actuary-data-salary) ve [matematik diplomasıyla iş piyasası](/tr/blog/what-to-do-with-a-mathematics-degree-in-germany-job-market).

---
*2026 itibarıyla geçerli genel duruma dayanır; program dili, NC, GRE şartı, ücretler ve burslar eyalet ve üniversiteye göre değişir — başvurudan önce ilgili üniversitenin International Office'i ve uni-assist üzerinden doğrulayın.*
MD;

        $deBody = <<<'MD'
„Mathematik ist ein schweres Fach — lohnt es sich?" Im deutschen Kontext lautet die ehrliche Antwort: **Mathematik ist wirklich abstrakt und anspruchsvoll, aber sie führt zu einem der beschäftigungsstärksten Abschlüsse in Deutschland.** Der Schlüssel liegt nicht im Fach selbst, sondern darin, wie du es mit einer Karriere verbindest — Quant, Aktuar, Data Science. Dieser Beitrag zeigt dir ehrlich, wie es ist, als internationaler Studierender in Deutschland Mathematik/Statistik zu studieren.

## 1. Das Feld: reine Mathematik, angewandte Mathematik und Statistik
„Mathematik" ist in Deutschland nicht eine einzige Sache. Grob gibt es drei Richtungen:

- **Reine Mathematik:** Algebra, Analysis, Topologie, Zahlentheorie — abstrakt, beweislastig, nah an Forschung und Wissenschaft.
- **Angewandte Mathematik:** Numerik, Optimierung, Differentialgleichungen, wissenschaftliches Rechnen — eng verzahnt mit Technik und Industrie.
- **Statistik & Stochastik:** Wahrscheinlichkeit, mathematische Statistik, Datenmodellierung — Grundlage von Data Science, Versicherung und Finanzen.

Dazu bieten viele Universitäten angewandte Zweige wie **Finanz- und Versicherungsmathematik** sowie **Wirtschaftsmathematik** an. Deine Richtungswahl bestimmt weitgehend deine spätere Karriere; das behandeln wir ausführlich in [Was man mit einem Mathe-Abschluss in Deutschland macht](/de/blog/what-to-do-with-a-mathematics-degree-in-germany-job-market-de).

## 2. Bachelor auf Deutsch, Master oft auf Englisch
Eine entscheidende Sprachrealität: **Bachelorprogramme sind fast durchgehend auf Deutsch und verlangen meist C1-Deutsch.** Englischsprachige Mathe-Bachelor sind sehr selten. Dagegen **öffnet sich auf Masterebene eine englische Welt** — ein großer Teil von MSc Mathematics, Applied Mathematics, **Financial/Actuarial Mathematics**, Statistics, Mathematical Modelling oder Scientific Computing wird auf Englisch angeboten.

Das ergibt für internationale Studierende eine praktische Roadmap: Ist dein Deutsch nicht stark, ist der **englischsprachige Master** der realistischste Einstieg. Die Route ohne Deutsch haben wir in [Englischsprachige Mathematik-/Statistik-Master ohne Deutsch](/de/blog/english-taught-mathematics-statistics-masters-in-germany-without-german-de) zusammengestellt.

## 3. Ehrlich zur Abstraktion und Schwierigkeit — aber starke Beschäftigung
Beschönigen wir nichts: **Ein Mathe-Bachelor in Deutschland ist hart.** Besonders in den ersten Jahren beruhen Analysis I–II und Lineare Algebra auf einer Beweiskultur; das unterscheidet sich stark von der „Rechen"-Mathematik der Schule. Die Abbruchquoten sind hoch. Das ist keine Warnung, sondern eine Tatsache: Wer ohne Bereitschaft zu abstraktem Denken und Beweisen kommt, wird enttäuscht.

> **Realitätscheck:** Mathematik ist keine „einfache Jobgarantie"; sie ist ein anspruchsvolles Fach. Aber für die, die es abschließen, ist das Bild sehr positiv — denn Mathematiker sind mit analytischem Denken, Modellierung und Problemlösung stark gefragt in **Finanzen (Quant/Risiko), Versicherung (Aktuar), Data Science, IT, Beratung und Forschung.**

Karriere und Gehalt behandeln wir mit Zahlen in [Mit einem Mathe-Abschluss in Deutschland arbeiten: Quant, Aktuar, Data](/de/blog/working-with-a-mathematics-degree-in-germany-quant-actuary-data-salary-de). Kurz: Das Fach ist abstrakt, das Ergebnis sehr konkret.

## 4. Top-Standorte: Bonn, Münster, München, Göttingen
Deutschland ist ein Zentrum der mathematischen Tradition (Gauß, Hilbert, Noether). Einige Fakultäten stechen international hervor. Die folgende Tabelle gibt **eine grobe Orientierung Stand 2025/2026; prüfe die Programmliste bei der jeweiligen Uni.**

| Universität | Schwerpunkt | Hinweis |
|---|---|---|
| **Bonn** | Reine & angewandte Mathematik | **Hausdorff Center** — Exzellenzzentrum |
| **Münster** | Reine Mathematik, Geometrie | **Mathematics Münster** — Exzellenzcluster |
| **TU München / LMU München** | Angewandt, Finanz, Statistik | Starke Industrieanbindung |
| **Göttingen** | Historisches Mathezentrum | Tiefe Tradition |
| **Heidelberg / KIT / TU Berlin** | Angewandt, wissenschaftliches Rechnen | Starke englische Master |
| Aachen / Bielefeld / Freiburg | Angewandt & Statistik | Gute Forschungsbasis |

Hinweis: Die Frage „Welche Uni ist prestigeträchtiger?" funktioniert in Deutschland anders als du denkst — der Unterschied zwischen staatlichen Universitäten ist nicht so scharf wie in den USA/UK. Das erklären wir in [Wie Prestige und Rankings in Deutschland funktionieren](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de).

## 5. Bewerbung: uni-assist und die NC-Realität
Für Bewerber aus Nicht-EU-Ländern (z. B. Türkei) läuft der typische Weg über **uni-assist**. Bei der Mathe-Bewerbung wichtig:

1. **Anerkennung:** Ergibt dein Schulabschluss keine direkte Hochschulzugangsberechtigung, kann zuerst das **Studienkolleg (T-Kurs — technisch/mathematisch)** nötig sein; für den Master ist ein starker Mathe-Bachelor entscheidend.
2. **NC:** Mathematikstudiengänge sind oft **zulassungsfrei (ohne NC)** oder haben entspannte Kapazitäten — anders als Medizin/Jura ist ein Platz meist leicht zu bekommen. Die eigentliche Hürde ist nicht der Einstieg, sondern der **Abschluss.**
3. **Master-Voraussetzungen:** Englische Programme verlangen eine starke mathematische Grundlage, einen Englischnachweis (IELTS/TOEFL) und **bei manchen Programmen den GRE.**

Die Bewerbungsfristen liegen fürs Wintersemester meist um den **15. Juli**, fürs Sommersemester um den **15. Januar** (variiert je nach Uni — prüfen). Ob Master oder ein anderer Visumsweg zu dir passt, vergleichen wir in [Master vs. Jobsuchvisum in Deutschland](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).

## 6. Gebühren und Stipendien
An staatlichen Universitäten ist Mathematik/Statistik in der Regel **studiengebührenfrei**; du zahlst nur den **Semesterbeitrag.** Baden-Württemberg erhebt von Nicht-EU-Studierenden eine Semestergebühr. Die folgenden Zahlen sind **Stand 2025/2026 ungefähr; vor der Bewerbung prüfen.**

| Posten | Ungefährer Betrag (Stand 2025/2026, prüfen) |
|---|---|
| Studiengebühr staatliche Uni | Keine (nur Semesterbeitrag) |
| Semesterbeitrag | ~150–350 € / Semester |
| Baden-Württemberg (Nicht-EU) | ~1.500 € / Semester |
| Lebenshaltung (Sperrkonto fürs Visum) | ~11.900 € / Jahr, je nach Stadt |

Bei Stipendien sind **DAAD** und das **Deutschlandstipendium** die Hauptoptionen; zudem bieten manche Exzellenzcluster (Bonn, Münster) begabten Masterstudierenden Stipendien/Hilfskraftstellen. Fürs Visum brauchst du in der Regel ein **Sperrkonto**, das die Lebenshaltung für ein Jahr nachweist, sowie eine gültige **Krankenversicherung.**

## Fazit & ehrlicher Rat
Ehrlicher Rat: **Hab keine Angst vor Mathematik, aber unterschätze sie auch nicht.** Es ist ein abstraktes, anspruchsvolles Fach; du solltest mit Bereitschaft zu Beweiskultur und diszipliniertem Arbeiten kommen. Dafür hast du nach dem Abschluss eines der **beschäftigungsstärksten Profile** in Deutschland — besonders, wenn du dich Richtung Finanzen, Aktuariat und Data spezialisierst.

Für internationale Studierende ist die realistischste Route oft: Ist dein Deutsch stark, steig in den Bachelor ein; wenn nicht, beginne mit einem **englischsprachigen Master** und wähle früh eine Karriererichtung (Quant/Aktuar/Data). Weiter geht es mit: [Englischsprachige Master ohne Deutsch](/de/blog/english-taught-mathematics-statistics-masters-in-germany-without-german-de), [mit einem Mathe-Abschluss arbeiten](/de/blog/working-with-a-mathematics-degree-in-germany-quant-actuary-data-salary-de) und [was man mit einem Mathe-Abschluss macht](/de/blog/what-to-do-with-a-mathematics-degree-in-germany-job-market-de).

---
*Stand 2026; Programmsprache, NC, GRE-Anforderung, Gebühren und Stipendien variieren je nach Bundesland und Universität — vor der Bewerbung beim International Office der Ziel-Uni und bei uni-assist bestätigen.*
MD;

        $enBody = <<<'MD'
"Maths is a hard subject — is it worth it?" In the German context, the honest answer is: **mathematics really is abstract and demanding, yet it leads to one of the most employable degrees in Germany.** The trick lies not in the subject itself but in how you connect it to a career — quant, actuary (Aktuar), data science. This post gives you the honest reality of studying mathematics/statistics in Germany as an international student.

## 1. The field: pure maths, applied maths and statistics
In Germany, "Mathematik" is not a single thing. Broadly, there are three directions:

- **Pure mathematics (reine Mathematik):** algebra, analysis, topology, number theory — abstract, proof-heavy, close to research and academia.
- **Applied mathematics (angewandte Mathematik):** numerics, optimisation, differential equations, scientific computing — closely tied to engineering and industry.
- **Statistics & stochastics:** probability, mathematical statistics, data modelling — the foundation of data science, insurance and finance.

On top of this, many universities offer applied tracks such as **financial and actuarial mathematics (Finanz- und Versicherungsmathematik)** and **Wirtschaftsmathematik (business mathematics)**. Your choice of direction largely determines your later career; we cover this in detail in [what to do with a maths degree in Germany](/en/blog/what-to-do-with-a-mathematics-degree-in-germany-job-market-en).

## 2. Bachelor in German, master often in English
A crucial language reality: **bachelor programmes are almost entirely in German and usually require C1 German.** English-taught maths bachelors are very rare. In contrast, **an English-speaking world opens up at master level** — a large share of MSc Mathematics, Applied Mathematics, **Financial/Actuarial Mathematics**, Statistics, Mathematical Modelling and Scientific Computing programmes are taught in English.

This gives international students a practical roadmap: if your German is not strong, the **English-taught master** is the most realistic entry point. We compiled the no-German route in [English-taught mathematics/statistics masters without German](/en/blog/english-taught-mathematics-statistics-masters-in-germany-without-german-en).

## 3. Honest about the abstraction and difficulty — but strong employability
Let's not sugar-coat it: **a maths bachelor in Germany is hard.** Especially in the first years, Analysis I–II and Linear Algebra rest on a proof culture very different from the "calculation" maths learned at school. Dropout rates are high. That is not a warning but a fact: arriving without readiness for abstract thinking and proofs leads to disappointment.

> **Reality check:** Mathematics is not an "easy job guarantee"; it is a demanding subject. But for those who finish, the picture is very positive — because mathematicians are in high demand, with their analytical, modelling and problem-solving skills, in **finance (quant/risk), insurance (actuary), data science, IT, consulting and research.**

We cover career and salary with figures in [working with a maths degree in Germany: quant, actuary, data](/en/blog/working-with-a-mathematics-degree-in-germany-quant-actuary-data-salary-en). In short: the subject is abstract, the outcome very concrete.

## 4. Top departments: Bonn, Münster, Munich, Göttingen
Germany is a heartland of the mathematical tradition (Gauss, Hilbert, Noether). A few departments stand out internationally. The table below gives **a rough orientation as of 2025/2026; verify the programme list with the relevant university.**

| University | Focus | Note |
|---|---|---|
| **Bonn** | Pure & applied mathematics | **Hausdorff Center** — centre of excellence |
| **Münster** | Pure mathematics, geometry | **Mathematics Münster** — excellence cluster |
| **TU Munich / LMU Munich** | Applied, finance, statistics | Strong industry links |
| **Göttingen** | Historic maths centre | Deep tradition |
| **Heidelberg / KIT / TU Berlin** | Applied, scientific computing | Strong English-taught masters |
| Aachen / Bielefeld / Freiburg | Applied & statistics | Good research base |

Note: the "which university is more prestigious?" question works differently in Germany than you might expect — the gap between public universities is not as sharp as in the US/UK. We explain this in [how prestige and rankings work in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en).

## 5. Applying: uni-assist and the NC reality
For a non-EU applicant (e.g. from Turkey), the typical route runs through **uni-assist**. Things to watch in a maths application:

1. **Recognition:** if your school diploma does not grant direct university entry (Hochschulzugangsberechtigung), you may first need the **Studienkolleg (T-Kurs — technical/maths track)**; for the master, a strong maths bachelor is essential.
2. **NC:** maths programmes are often **NC-free (zulassungsfrei)** or have relaxed capacities — unlike medicine/law, a place is usually easy to secure. The real hurdle is not getting in but **finishing.**
3. **Master requirements:** English-taught programmes expect a strong mathematical foundation, an English certificate (IELTS/TOEFL) and, **for some programmes, the GRE.**

Application deadlines are usually around **15 July** for the winter semester and **15 January** for the summer semester (it varies by university — verify). Whether a master or another visa route suits you is compared in [master vs job-seeker visa in Germany](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

## 6. Fees and scholarships
At public universities, mathematics/statistics is generally **tuition-free**; you only pay the **Semesterbeitrag.** Baden-Württemberg charges non-EU students a semester fee. The figures below are **approximate as of 2025/2026; confirm before applying.**

| Item | Approx. amount (as of 2025/2026, verify) |
|---|---|
| Public university tuition | None (only Semesterbeitrag) |
| Semesterbeitrag | ~€150–350 / semester |
| Baden-Württemberg (non-EU) | ~€1,500 / semester |
| Living costs (blocked account for visa) | ~€11,900 / year, varies by city |

For scholarships, **DAAD** and the **Deutschlandstipendium** are the main options; some excellence clusters (Bonn, Münster) also offer scholarships/assistantships to talented master students. For the visa you generally need a **blocked account (Sperrkonto)** proving one year of living costs plus valid **health insurance.**

## Bottom line & honest advice
Honest advice: **don't fear mathematics, but don't underestimate it either.** It is an abstract, demanding subject; come ready for a proof culture and disciplined work. In return, once you finish you hold one of the **most employable profiles** in Germany — especially if you specialise towards finance, actuarial work and data.

For an international student, the most realistic route is often: if your German is strong, enter the bachelor; if not, start with an **English-taught master** and pick a career direction (quant/actuary/data) early. Continue with: [English-taught masters without German](/en/blog/english-taught-mathematics-statistics-masters-in-germany-without-german-en), [working with a maths degree](/en/blog/working-with-a-mathematics-degree-in-germany-quant-actuary-data-salary-en) and [what to do with a maths degree](/en/blog/what-to-do-with-a-mathematics-degree-in-germany-job-market-en).

---
*Based on the general situation as of 2026; programme language, NC, GRE requirements, fees and scholarships vary by state and university — confirm with the target university's International Office and uni-assist before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'studying-mathematics-statistics-in-germany-as-a-foreigner',
                'title' => 'Almanya\'da Matematik / İstatistik Okumak: Uluslararası Öğrenci Rehberi (2026)',
                'excerpt' => 'Almanya\'da matematik/istatistik soyut ve zordur ama en istihdam edilebilir diplomalardan biridir: saf/uygulamalı/istatistik yönleri, Almanca bachelor vs İngilizce master, Bonn (Hausdorff) & Münster mükemmeliyeti, uni-assist & NC, ücret ve burslar — quant/aktüer/veri kariyerine dürüst rehber.',
                'meta_title' => 'Almanya\'da Matematik / İstatistik Okumak — Rehber (2026)',
                'meta_description' => 'Almanya\'da matematik/istatistik: saf/uygulamalı/istatistik, Almanca bachelor vs İngilizce master, Bonn & Münster, uni-assist & NC, ücret & burs — yabancılar için dürüst 2026 rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'studying-mathematics-statistics-in-germany-as-a-foreigner-de',
                'title' => 'Mathematik / Statistik in Deutschland studieren: Leitfaden für internationale Studierende (2026)',
                'excerpt' => 'Mathematik/Statistik in Deutschland ist abstrakt und hart, aber einer der beschäftigungsstärksten Abschlüsse: reine/angewandte/Statistik-Richtungen, Bachelor auf Deutsch vs Master auf Englisch, Bonn (Hausdorff) & Münster, uni-assist & NC, Gebühren und Stipendien — ehrlicher Leitfaden für Quant/Aktuar/Data.',
                'meta_title' => 'Mathematik / Statistik in Deutschland studieren — Leitfaden (2026)',
                'meta_description' => 'Mathematik/Statistik in Deutschland: rein/angewandt/Statistik, Bachelor auf Deutsch vs Master auf Englisch, Bonn & Münster, uni-assist & NC, Gebühren & Stipendien — ehrlicher Leitfaden 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'studying-mathematics-statistics-in-germany-as-a-foreigner-en',
                'title' => 'Studying Mathematics / Statistics in Germany: A Guide for International Students (2026)',
                'excerpt' => 'Mathematics/statistics in Germany is abstract and hard but one of the most employable degrees: pure/applied/statistics directions, German bachelor vs English-taught master, Bonn (Hausdorff) & Münster excellence, uni-assist & NC, fees and scholarships — an honest guide to quant/actuary/data careers.',
                'meta_title' => 'Studying Mathematics / Statistics in Germany — Guide (2026)',
                'meta_description' => 'Maths/statistics in Germany: pure/applied/statistics, German bachelor vs English-taught master, Bonn & Münster, uni-assist & NC, fees & scholarships — an honest 2026 guide for foreigners.',
                'body' => $enBody,
            ],
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
            'studying-mathematics-statistics-in-germany-as-a-foreigner',
            'studying-mathematics-statistics-in-germany-as-a-foreigner-de',
            'studying-mathematics-statistics-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};

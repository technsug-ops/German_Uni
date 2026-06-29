<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Studying engineering (Maschinenbau & Elektrotechnik) in Germany as a foreigner (2026).
 * Doğrulandı: Müh. NC tıptan çok yumuşak, çoğu FH/HAW Maschinenbau/Elektrotechnik'i NC-siz açar; tepe okullar (RWTH/TUM/KIT) rekabetçi.
 * #1 şok = ağır matematik+teori (Höhere Mathematik, Technische Mechanik) → yüksek bırakma. Bachelor çoğu Almanca C1; master İngilizce bol+ücretsiz.
 * TU=teori, FH=uygulama. Başvuru uni-assist; Türk diploması → Studienkolleg T-Kurs / anabin. Sayılar 2025/2026, hedge'li.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'e1a10000-1111-4eaa-9f30-aa01bb02cc01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da mühendislik okumayı düşünüyorsun ama kafanda bir sürü soru var: "Notlarım yeter mi? Almanca şart mı? Bırakma oranı gerçekten yüksek mi?" Bu rehber, **Maschinenbau (makine)** ve **Elektrotechnik (elektrik-elektronik)** başta olmak üzere, bir yabancı olarak Almanya'da mühendislik okumanın dürüst halini anlatır — pazarlama broşürü değil, gerçeklik.

## İyi haber: NC tıptan çok daha yumuşak

Almanya'da en çok korkulan kavram **NC (Numerus Clausus)** — kontenjan sınırlaması. Tıp ya da psikolojide NC acımasızdır (1,0–1,2 ortalama gerekebilir). Mühendislikte tablo bambaşka:

- **Çoğu FH (HAW — Fachhochschule / Hochschule für Angewandte Wissenschaften)** ve birçok kamu üniversitesi, Maschinenbau ve Elektrotechnik'i **NC-siz (zulassungsfrei)** ya da ılımlı kontenjanla açar. Yani şartları tutuyorsan büyük ihtimalle yer var.
- **Ama tepe okullar rekabetçi.** Mühendislik devi **RWTH Aachen**, ardından **TUM (Münih)**, **KIT (Karlsruhe)**, **TU Berlin / TU Darmstadt / TU Dresden** ve **Stuttgart** — bunlar prestijli, kalabalık ve bazı dönemlerde sınırlı kontenjanlı.

Yani strateji açık: Hedefin sadece RWTH değilse, **NC-siz bir FH ya da orta ölçek bir TU seçeneğin neredeyse her zaman var.** Tıpla kıyaslarsan ne kadar şanslı olduğunu görürsün — farkı [Almanya'da tıp okumak (NC, dil, TestAS)](/tr/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas) yazısında net görebilirsin.

## #1 gerçeklik şoku: matematik ve teori seni eler, atölye değil

Burası en çok yanlış bilinen nokta. Birçok öğrenci "mühendislik = makineyle uğraşmak, vida sıkmak" sanır. **Almanya'da mühendislik ilk yıllarda neredeyse saf matematik ve fizik teorisidir:**

- **Höhere Mathematik** (yüksek matematik — analiz, lineer cebir, diferansiyel denklemler)
- **Technische Mechanik** (statik, dinamik, mukavemet) — Maschinenbau'nun meşhur eleyicisi
- **Thermodynamik** (termodinamik)
- **Regelungstechnik** (kontrol sistemleri), elektrikte **Elektrotechnik Grundlagen** ve devre teorisi

**Bırakma oranı yüksektir** ve çoğu öğrenci ilk üç sömestrdeki Mathe ve Technische Mechanik sınavlarında elenir. Bu bir korkutma değil, plan yapman için uyarı: ilk yıl atölyeye değil, **ders çalışmaya, problem çözmeye ve Übung (alıştırma) gruplarına** hazırlıklı gel. Lise matematiğin sağlamsa büyük avantajın olur.

## Hangi dal? Maschinenbau, Elektrotechnik, Mechatronik, Wirtschaftsingenieur

Mühendislik tek şey değil. Almanya'daki ana dallar ve kime uyduğu:

| Dal | Odak | Kime uygun | İş alanı |
|---|---|---|---|
| **Maschinenbau** | Makine, üretim, mekanik, termodinamik | Fizik/mekanik sevenler | Otomotiv, makine sanayi, enerji |
| **Elektrotechnik** | Elektrik, elektronik, sinyal, güç | Devre/matematik sevenler | Otomasyon, enerji, yarı iletken |
| **Mechatronik** | Makine + elektrik + yazılım kesişimi | Disiplinler arası sevenler | Robotik, otomasyon, otomotiv |
| **Wirtschaftsingenieurwesen** | Mühendislik + işletme/ekonomi | Teknik + yönetim isteyenler | Proje yönetimi, teknik satış, danışmanlık |
| **Bauingenieurwesen** | İnşaat, yapı, altyapı | Yapı/saha sevenler | İnşaat, altyapı, kentsel planlama |

**Maschinenbau ve Elektrotechnik en büyük iki daldır** ve uluslararası öğrenciler arasında **Informatik (bilgisayar mühendisliği) ile birlikte en kalabalık STEM alanlarındandır.** Yazılıma ilgin varsa, kıyas için [Almanya'da bilgisayar mühendisliği / Informatik okumak](/tr/blog/studying-computer-science-informatik-in-germany-as-a-foreigner) yazısına da bak.

## Dil gerçeği: bachelor Almanca, master İngilizce

Bu, başvuru stratejini belirleyen en kritik konu:

- **Bachelor programlarının çoğu Almanca'dır.** Genelde **C1 seviyesi** ve **DSH-2 ya da TestDaF 4** dil sınavı şartı vardır. İngilizce bachelor mühendislik programı Almanya'da **nadirdir.**
- **Master tarafı bambaşka:** **İngilizce master programları çok boldur ve kamu üniversitelerinde ücretsizdir** (sadece **~150–350€ dönem katkı payı / Semesterbeitrag**). İstisna: **Baden-Württemberg eyaleti AB-dışı öğrencilerden ~1.500€/dönem** öğrenim ücreti alır (*2025/2026 itibarıyla, yaklaşık; yıllık değişir, başvurudan önce resmi kaynaktan doğrula*).

Yani Almancan zayıfsa pratik yol genelde: **önce ülkende ya da İngilizce bir bachelor, sonra Almanya'da İngilizce master.** Bunu detaylı ele aldığımız yazı: [Almancasız Almanya'da mühendislik: İngilizce master programları](/tr/blog/english-taught-engineering-masters-in-germany-without-german).

Ama dürüst ol kendine: **Almanca bilmeden gelirsen okul içi İngilizce olsa bile günlük hayat, staj (Praktikum) ve iş bulma için Almanca yine de şart olur.**

## TU/Uni mü, FH mı? İkisi de güçlü, ama farklı

Almanya'da mühendislik okurken iki tür kurum arasında seçim yaparsın:

| Kriter | TU / Universität | FH / HAW (Fachhochschule) |
|---|---|---|
| Odak | Teori + araştırma | Uygulama + pratik |
| Ders tarzı | Daha soyut, matematik ağır | Proje, laboratuvar, staj odaklı |
| Sanayi bağı | Araştırma ağırlıklı | Çok güçlü, zorunlu staj sömestri yaygın |
| Doktora | Doğrudan yapılabilir | Genelde TU ile ortak gerekir |
| İstihdam | Güçlü | Çoğu zaman doğrudan işe girişte avantajlı |

**Önemli gerçek: FH'ler "düşük seviye" değildir.** Almanya'da sanayi, pratik eğitimli FH mezunlarını çok değerli bulur ve mühendislikte FH diploması da **"Ingenieur" ünvanına** götürür. Araştırma/doktora istiyorsan TU; doğrudan sanayiye hızlı geçiş istiyorsan FH mantıklı.

## Başvuru: uni-assist, Studienkolleg ve diploma denkliği

Türkiye'den geliyorsan en kritik idari gerçek şu: **Türk lise diploması (düz lise) genelde doğrudan Almanya'da üniversiteye yetmez.** İki yol var:

1. **Studienkolleg (T-Kurs — teknik kol):** Bir yıllık hazırlık + FSP (Feststellungsprüfung) sınavı. Bu bir dil kursu DEĞİL — bir akademik hazırlık programıdır. Detayı: [Studienkolleg bir dil okulu değildir — gerçekte ne işe yarar](/tr/blog/studienkolleg-is-not-a-language-school-what-it-really-is).
2. **Ülkende 1 yıl üniversite okumak:** Türkiye'de bir üniversitede 1–2 yarıyıl okuyup geçer notla **doğrudan başvuru hakkı (HZB — Hochschulzugangsberechtigung)** kazanabilirsin. Denkliği **anabin** veritabanından kontrol et.

Başvuruların çoğu **uni-assist** üzerinden yapılır (belge ön-değerlendirme servisi). Başvuru takvimi: kış dönemi için genelde **15 Temmuz**, yaz dönemi için **15 Ocak** son tarihtir (üniye göre değişir; doğrula).

Önemli bir ek: **"Ingenieur" Almanya'da korumalı bir ünvandır** (geschützter Titel) — sadece tanınan bir mühendislik diplomasıyla kullanılır. Meslek örgütü **VDI'dir** (Verein Deutscher Ingenieure). Mezun olduktan sonra çalışma, maaş ve Blue Card tarafını [Almanya'da mühendis olarak çalışmak: Blue Card, maaş, ünvan](/tr/blog/working-as-an-engineer-in-germany-blue-card-salary) ve [mühendislik diplomasıyla iş piyasası](/tr/blog/what-to-do-with-an-engineering-degree-in-germany-job-market) yazılarında ele aldık.

## Sonuç & dürüst tavsiye

Almanya'da mühendislik okumak **mantıklı ve erişilebilir bir hedeftir** — tıp gibi NC duvarı yoktur, FH'lerle NC-siz yollar boldur, mezuniyet sonrası iş piyasası mühendis açığı (Fachkräftemangel) yüzünden güçlüdür. Ama iki şeyi baştan kabul et:

- **Matematik ve teori seni zorlayacak.** İlk yıl en kritik yıl; lise matematiğini sağlamlaştır, ilk sömestrde derslere asıl.
- **Almanca er ya da geç şart.** Bachelor için baştan, master için en azından günlük hayat ve staj için.

Akıllı plan: Almancan iyiyse Almanca bir bachelor + (gerekirse) Studienkolleg; Almancan zayıfsa İngilizce master rotası. Her durumda **anabin + uni-assist + dil sınavı** üçlüsünü erkenden çöz.

*Bu rehber 2026 başı içindir; NC, ücret, dil ve vize eşikleri yıllık değişir — başvurudan önce mutlaka resmi kaynaktan (üniversite, uni-assist, anabin) doğrula.*
MD;

        $deBody = <<<'MD'
Du überlegst, in Deutschland Ingenieurwissenschaften zu studieren, aber dein Kopf ist voller Fragen: "Reichen meine Noten? Brauche ich Deutsch? Ist die Abbruchquote wirklich so hoch?" Dieser Guide zeigt dir die ehrliche Version — vor allem für **Maschinenbau** und **Elektrotechnik** — wenn du als Ausländer in Deutschland Ingenieurwesen studieren willst. Keine Hochglanzbroschüre, sondern Realität.

## Die gute Nachricht: NC ist viel weicher als in der Medizin

Der meistgefürchtete Begriff in Deutschland ist der **NC (Numerus Clausus)** — die Zulassungsbeschränkung. In Medizin oder Psychologie ist der NC brutal. Im Ingenieurwesen sieht es ganz anders aus:

- **Die meisten FH (HAW — Hochschule für Angewandte Wissenschaften)** und viele staatliche Unis bieten Maschinenbau und Elektrotechnik **zulassungsfrei** oder mit moderater Beschränkung an. Wenn du die Voraussetzungen erfüllst, ist sehr wahrscheinlich ein Platz für dich da.
- **Aber die Top-Hochschulen sind kompetitiv.** Der Ingenieur-Gigant **RWTH Aachen**, dann **TUM (München)**, **KIT (Karlsruhe)**, **TU Berlin / TU Darmstadt / TU Dresden** und **Stuttgart** — prestigeträchtig, voll und in manchen Semestern begrenzt.

Die Strategie ist also klar: Wenn dein Ziel nicht ausschließlich die RWTH ist, gibt es fast immer **eine zulassungsfreie FH oder eine mittelgroße TU als Option.** Im Vergleich zur Medizin hast du es leicht — den Unterschied siehst du im Beitrag [Medizin in Deutschland studieren (NC, Sprache, TestAS)](/de/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-de).

## Der größte Realitäts-Schock: Mathe und Theorie sieben dich aus, nicht die Werkstatt

Das ist der am meisten missverstandene Punkt. Viele denken "Ingenieur = an Maschinen schrauben". **In Deutschland ist Ingenieurwesen in den ersten Jahren fast reine Mathematik und Physik-Theorie:**

- **Höhere Mathematik** (Analysis, lineare Algebra, Differentialgleichungen)
- **Technische Mechanik** (Statik, Dynamik, Festigkeitslehre) — das berüchtigte Sieb im Maschinenbau
- **Thermodynamik**
- **Regelungstechnik**, in der Elektrotechnik die **Grundlagen der Elektrotechnik** und Schaltungstheorie

**Die Abbruchquote ist hoch**, und viele scheitern an den Mathe- und Technische-Mechanik-Klausuren der ersten drei Semester. Das ist keine Abschreckung, sondern eine Warnung zur Planung: Komm im ersten Jahr nicht für die Werkstatt, sondern **zum Lernen, Rechnen und für die Übungsgruppen.** Wenn deine Schulmathematik solide ist, hast du einen großen Vorteil.

## Welche Fachrichtung? Maschinenbau, Elektrotechnik, Mechatronik, Wirtschaftsingenieur

Ingenieurwesen ist nicht eine Sache. Die Hauptrichtungen in Deutschland und für wen sie passen:

| Fachrichtung | Fokus | Für wen | Branche |
|---|---|---|---|
| **Maschinenbau** | Maschinen, Produktion, Mechanik, Thermodynamik | Physik/Mechanik-Fans | Automobil, Maschinenbau-Industrie, Energie |
| **Elektrotechnik** | Elektrik, Elektronik, Signal, Leistung | Schaltungs-/Mathe-Fans | Automatisierung, Energie, Halbleiter |
| **Mechatronik** | Maschine + Elektrik + Software | Interdisziplinär Denkende | Robotik, Automatisierung, Automobil |
| **Wirtschaftsingenieurwesen** | Technik + Betriebswirtschaft | Technik + Management | Projektmanagement, technischer Vertrieb, Beratung |
| **Bauingenieurwesen** | Bau, Tragwerk, Infrastruktur | Bau/Baustelle-Fans | Bauwesen, Infrastruktur, Stadtplanung |

**Maschinenbau und Elektrotechnik sind die zwei größten Richtungen** und gehören zusammen mit **Informatik** zu den beliebtesten MINT-Feldern unter internationalen Studierenden. Wenn dich Software interessiert, schau zum Vergleich in [Informatik in Deutschland studieren als Ausländer](/de/blog/studying-computer-science-informatik-in-germany-as-a-foreigner-de).

## Die Sprach-Realität: Bachelor auf Deutsch, Master auf Englisch

Das ist der kritischste Punkt für deine Bewerbungsstrategie:

- **Die meisten Bachelor-Programme sind auf Deutsch.** In der Regel **Niveau C1** und Nachweis über **DSH-2 oder TestDaF 4**. Englischsprachige Bachelor im Ingenieurwesen sind in Deutschland **selten.**
- **Beim Master ist es anders:** **Englischsprachige Master gibt es reichlich, und an staatlichen Unis sind sie kostenlos** (nur **~150–350€ Semesterbeitrag**). Ausnahme: **Baden-Württemberg verlangt von Nicht-EU-Studierenden ~1.500€/Semester** Studiengebühr (*Stand 2025/2026, ungefähr; ändert sich jährlich, vor der Bewerbung offiziell prüfen*).

Wenn dein Deutsch schwach ist, ist der praktische Weg oft: **erst ein Bachelor im Heimatland oder auf Englisch, dann ein englischsprachiger Master in Deutschland.** Mehr dazu in [Ingenieurwesen ohne Deutsch: englischsprachige Master in Deutschland](/de/blog/english-taught-engineering-masters-in-germany-without-german-de).

Aber sei ehrlich zu dir: **Auch wenn das Studium auf Englisch ist, brauchst du Deutsch für den Alltag, das Praktikum und die Jobsuche.**

## TU/Uni oder FH? Beide sind stark, aber unterschiedlich

Beim Ingenieurstudium wählst du zwischen zwei Hochschultypen:

| Kriterium | TU / Universität | FH / HAW |
|---|---|---|
| Fokus | Theorie + Forschung | Anwendung + Praxis |
| Lehrstil | Abstrakter, mathelastig | Projekt, Labor, Praxissemester |
| Industrie-Bezug | Forschungsorientiert | Sehr stark, Praxissemester üblich |
| Promotion | Direkt möglich | Meist nur kooperativ mit einer TU |
| Beschäftigung | Stark | Oft Vorteil beim direkten Berufseinstieg |

**Wichtige Wahrheit: FHs sind nicht "minderwertig".** Die deutsche Industrie schätzt praxisorientierte FH-Absolventen sehr, und auch ein FH-Abschluss führt zum geschützten Titel **"Ingenieur"**. Willst du Forschung/Promotion, nimm die TU; willst du schnell in die Industrie, ist die FH sinnvoll.

## Bewerbung: uni-assist, Studienkolleg und Anerkennung

Wenn du aus der Türkei kommst, ist die wichtigste administrative Realität: **Ein türkisches Abiturzeugnis reicht meist nicht direkt für ein Studium in Deutschland.** Es gibt zwei Wege:

1. **Studienkolleg (T-Kurs — technisch):** Ein Jahr Vorbereitung + Feststellungsprüfung (FSP). Das ist KEIN Sprachkurs — es ist ein akademisches Vorbereitungsprogramm. Mehr dazu: [Studienkolleg ist keine Sprachschule — was es wirklich ist](/de/blog/studienkolleg-is-not-a-language-school-what-it-really-is-de).
2. **Ein Jahr Studium im Heimatland:** Nach 1–2 Semestern an einer türkischen Uni mit Bestehensnoten kannst du die direkte **Hochschulzugangsberechtigung (HZB)** erlangen. Prüfe die Anerkennung in der **anabin**-Datenbank.

Die meisten Bewerbungen laufen über **uni-assist** (Vorprüfungsdokumentation). Fristen: fürs Wintersemester meist **15. Juli**, fürs Sommersemester **15. Januar** (variiert je Uni; prüfen).

Wichtiger Zusatz: **"Ingenieur" ist in Deutschland ein geschützter Titel** — nur mit anerkanntem Ingenieurabschluss nutzbar. Der Berufsverband ist der **VDI** (Verein Deutscher Ingenieure). Arbeit, Gehalt und Blue Card behandeln wir in [Als Ingenieur in Deutschland arbeiten: Blue Card, Gehalt, Titel](/de/blog/working-as-an-engineer-in-germany-blue-card-salary-de) und [Was tun mit einem Ingenieurabschluss in Deutschland](/de/blog/what-to-do-with-an-engineering-degree-in-germany-job-market-de).

## Fazit & ehrlicher Rat

In Deutschland Ingenieurwesen zu studieren ist **ein sinnvolles und erreichbares Ziel** — keine NC-Mauer wie in der Medizin, viele zulassungsfreie FH-Wege, und ein starker Arbeitsmarkt wegen des Fachkräftemangels. Aber akzeptiere zwei Dinge von Anfang an:

- **Mathe und Theorie werden dich fordern.** Das erste Jahr ist entscheidend; stärke deine Schulmathematik und gib im ersten Semester Vollgas.
- **Deutsch ist früher oder später Pflicht.** Für den Bachelor sofort, für den Master spätestens für Alltag und Praktikum.

Kluger Plan: Bei gutem Deutsch ein Bachelor auf Deutsch + (falls nötig) Studienkolleg; bei schwachem Deutsch die englischsprachige Master-Route. In jedem Fall löse früh das Trio **anabin + uni-assist + Sprachprüfung.**

*Dieser Guide gilt für Anfang 2026; NC, Gebühren, Sprache und Visa-Schwellen ändern sich jährlich — prüfe vor der Bewerbung unbedingt die offiziellen Quellen (Uni, uni-assist, anabin).*
MD;

        $enBody = <<<'MD'
You're thinking about studying engineering in Germany, but your head is full of questions: "Are my grades good enough? Do I need German? Is the dropout rate really that high?" This guide gives you the honest version — focused on **Maschinenbau (mechanical)** and **Elektrotechnik (electrical/electronic)** — of studying engineering in Germany as a foreigner. Not a glossy brochure, but reality.

## The good news: NC is far softer than in medicine

The most feared concept in Germany is the **NC (Numerus Clausus)** — the admission cap. In medicine or psychology the NC is brutal. In engineering it's a completely different picture:

- **Most FH (HAW — University of Applied Sciences)** and many public universities offer Maschinenbau and Elektrotechnik **without an NC (zulassungsfrei)** or with a moderate cap. If you meet the requirements, there's very likely a spot for you.
- **But the top schools are competitive.** The engineering giant **RWTH Aachen**, then **TUM (Munich)**, **KIT (Karlsruhe)**, **TU Berlin / TU Darmstadt / TU Dresden** and **Stuttgart** — prestigious, crowded and capped in some intakes.

So the strategy is clear: unless your only target is RWTH, there's almost always **a no-NC FH or a mid-sized TU as an option.** Compared to medicine, you have it easy — you can see the difference in [Studying medicine in Germany (NC, language, TestAS)](/en/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-en).

## The #1 reality shock: math and theory weed you out, not the workshop

This is the most misunderstood point. Many students think "engineering = working with machines, tightening bolts." **In Germany, engineering in the first years is almost pure mathematics and physics theory:**

- **Höhere Mathematik** (higher math — analysis, linear algebra, differential equations)
- **Technische Mechanik** (statics, dynamics, strength of materials) — the famous filter in Maschinenbau
- **Thermodynamik** (thermodynamics)
- **Regelungstechnik** (control engineering), and in electrical engineering the **fundamentals of Elektrotechnik** and circuit theory

**The dropout rate is high**, and many students fail the math and Technische Mechanik exams in the first three semesters. This isn't meant to scare you — it's a warning so you plan ahead: in the first year, come prepared **to study, solve problems and join the Übung (tutorial) groups**, not for the workshop. If your high-school math is solid, you have a big advantage.

## Which field? Maschinenbau, Elektrotechnik, Mechatronik, Wirtschaftsingenieur

Engineering isn't one thing. The main fields in Germany and who they fit:

| Field | Focus | Who it fits | Industry |
|---|---|---|---|
| **Maschinenbau** | Machines, production, mechanics, thermodynamics | Physics/mechanics lovers | Automotive, machinery industry, energy |
| **Elektrotechnik** | Electrical, electronics, signal, power | Circuit/math lovers | Automation, energy, semiconductors |
| **Mechatronik** | Machine + electrical + software | Interdisciplinary minds | Robotics, automation, automotive |
| **Wirtschaftsingenieurwesen** | Engineering + business/economics | Technical + management | Project management, technical sales, consulting |
| **Bauingenieurwesen** | Civil, structures, infrastructure | Construction/site lovers | Construction, infrastructure, urban planning |

**Maschinenbau and Elektrotechnik are the two biggest fields** and, together with **Informatik**, are among the most popular STEM fields for international students. If you're drawn to software, compare with [Studying computer science / Informatik in Germany as a foreigner](/en/blog/studying-computer-science-informatik-in-germany-as-a-foreigner-en).

## The language reality: bachelor in German, master in English

This is the most critical point for your application strategy:

- **Most bachelor programs are in German.** Usually **C1 level** and proof via **DSH-2 or TestDaF 4**. English-taught engineering bachelors are **rare** in Germany.
- **The master side is different:** **English-taught masters are plentiful, and at public universities they're free** (only **~150–350€ semester contribution / Semesterbeitrag**). Exception: **Baden-Württemberg charges non-EU students ~1,500€/semester** in tuition (*as of 2025/2026, approximate; changes yearly, verify with the official source before applying*).

So if your German is weak, the practical path is often: **first a bachelor at home or in English, then an English-taught master in Germany.** We cover this in detail in [Engineering without German: English-taught masters in Germany](/en/blog/english-taught-engineering-masters-in-germany-without-german-en).

But be honest with yourself: **even if the program is in English, you'll still need German for daily life, the internship (Praktikum) and the job search.**

## TU/Uni or FH? Both are strong, but different

When studying engineering you choose between two types of institution:

| Criterion | TU / Universität | FH / HAW |
|---|---|---|
| Focus | Theory + research | Application + practice |
| Teaching style | More abstract, math-heavy | Project, lab, internship semester |
| Industry link | Research-oriented | Very strong, mandatory practical semester common |
| PhD | Directly possible | Usually only jointly with a TU |
| Employment | Strong | Often an advantage for a direct job start |

**Important truth: FHs are not "lower tier."** German industry highly values practically-trained FH graduates, and an FH degree also leads to the protected title **"Ingenieur."** If you want research/PhD, take the TU; if you want a fast route into industry, the FH makes sense.

## Application: uni-assist, Studienkolleg and degree recognition

If you're coming from Turkey, the key administrative reality is: **a Turkish high-school diploma usually isn't enough on its own to study in Germany.** There are two routes:

1. **Studienkolleg (T-Kurs — technical track):** one year of preparation + the Feststellungsprüfung (FSP) exam. This is NOT a language course — it's an academic preparation program. More: [Studienkolleg is not a language school — what it really is](/en/blog/studienkolleg-is-not-a-language-school-what-it-really-is-en).
2. **One year of university at home:** after 1–2 semesters at a Turkish university with passing grades, you can earn the direct **university entrance qualification (HZB)**. Check recognition in the **anabin** database.

Most applications go through **uni-assist** (document pre-evaluation). Deadlines: for the winter semester usually **15 July**, for the summer semester **15 January** (varies by university; verify).

One important addition: **"Ingenieur" is a protected title in Germany** (geschützter Titel) — usable only with a recognized engineering degree. The professional body is the **VDI** (Verein Deutscher Ingenieure). We cover work, salary and the Blue Card in [Working as an engineer in Germany: Blue Card, salary, title](/en/blog/working-as-an-engineer-in-germany-blue-card-salary-en) and [What to do with an engineering degree in Germany](/en/blog/what-to-do-with-an-engineering-degree-in-germany-job-market-en).

## Conclusion & honest advice

Studying engineering in Germany is **a sensible and achievable goal** — no NC wall like in medicine, plenty of no-NC FH routes, and a strong post-graduation job market thanks to the engineer shortage (Fachkräftemangel). But accept two things from the start:

- **Math and theory will push you.** The first year is the most critical; strengthen your high-school math and go all-in in the first semester.
- **German is mandatory sooner or later.** For the bachelor immediately, for the master at least for daily life and the internship.

A smart plan: if your German is good, a German-taught bachelor + (if needed) Studienkolleg; if your German is weak, the English-taught master route. In any case, solve the trio **anabin + uni-assist + language exam** early.

*This guide is for early 2026; NC, fees, language and visa thresholds change yearly — always verify with the official sources (university, uni-assist, anabin) before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'studying-engineering-in-germany-as-a-foreigner',
                'title' => 'Almanya\'da Yabancı Olarak Mühendislik Okumak — Maschinenbau & Elektrotechnik (2026)',
                'excerpt' => 'NC tıptan yumuşak, çoğu FH NC-siz; ama gerçek şok ağır matematik+teori. Maschinenbau, Elektrotechnik, dil, TU vs FH ve uni-assist/Studienkolleg başvurusu — dürüst rehber.',
                'meta_title' => 'Almanya\'da Mühendislik Okumak (Yabancı Olarak) 2026',
                'meta_description' => 'Almanya\'da Maschinenbau & Elektrotechnik okumak: NC gerçeği, matematik+teori şoku, dil, TU vs FH, uni-assist ve Studienkolleg başvurusu. Yabancılar için dürüst 2026 rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'studying-engineering-in-germany-as-a-foreigner-de',
                'title' => 'In Deutschland Ingenieurwesen studieren als Ausländer — Maschinenbau & Elektrotechnik (2026)',
                'excerpt' => 'NC weicher als Medizin, viele FH zulassungsfrei; der echte Schock ist Mathe + Theorie. Maschinenbau, Elektrotechnik, Sprache, TU vs FH und Bewerbung über uni-assist/Studienkolleg.',
                'meta_title' => 'Ingenieurwesen in Deutschland studieren (Ausländer) 2026',
                'meta_description' => 'Maschinenbau & Elektrotechnik in Deutschland studieren: NC-Realität, Mathe-+Theorie-Schock, Sprache, TU vs FH, Bewerbung über uni-assist und Studienkolleg. Ehrlicher Guide 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'studying-engineering-in-germany-as-a-foreigner-en',
                'title' => 'Studying Engineering in Germany as a Foreigner — Maschinenbau & Elektrotechnik (2026)',
                'excerpt' => 'NC is softer than medicine, many FHs have no NC; the real shock is heavy math + theory. Maschinenbau, Elektrotechnik, language, TU vs FH and uni-assist/Studienkolleg applications.',
                'meta_title' => 'Studying Engineering in Germany as a Foreigner 2026',
                'meta_description' => 'Study Maschinenbau & Elektrotechnik in Germany: the NC reality, the math + theory shock, language, TU vs FH, and uni-assist/Studienkolleg applications. Honest 2026 guide for foreigners.',
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
            'studying-engineering-in-germany-as-a-foreigner',
            'studying-engineering-in-germany-as-a-foreigner-de',
            'studying-engineering-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};

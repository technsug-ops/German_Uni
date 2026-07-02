<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): İngilizce Mimarlık/Urban Design master — Almancasız Almanya (2026).
 * Doğrulandı: İngilizce bachelor nadir; İngilizce master sınırlı ama var (M.Sc. Architecture, Urban Design/Städtebau,
 * Integrated Design) — TU Berlin, TU München, Bauhaus Weimar, HafenCity Hamburg. Portfolyo (Mappe) + İngilizce şart.
 * "Almancasız tuzağı": stüdyo/kritik, büro ve lisans (Architektenkammer) çoğunlukla Almanca. Kamu ücretsiz, BW non-EU ~1.500€/dönem.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'd2a20000-2222-4a2c-9f50-dd01ee04aa02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almanya'da mimarlık okumak istiyorum ama Almancam yok" — bu cümleyi çok duyuyoruz. İyi haber: İngilizce yürüyen mimarlık master programları **var**. Kötü haber: sayısı sınırlı, çoğu **master** seviyesinde, ve "Almancasız hayat" göründüğü kadar pürüzsüz değil. Bu rehber, **İngilizce M.Sc. Architecture / Urban Design (Städtebau) / Integrated Design** programlarını, gerçek şartları ve kimsenin söylemediği "Almancasız tuzağı"nı dürüstçe anlatır.

## İngilizce mimarlık: bachelor nadir, master sınırlı ama var

Önce beklentiyi düzeltelim. Almanya'da mimarlık **bachelor** (B.Sc./B.A. Architektur) neredeyse tamamen **Almanca (genelde C1)** yürür — İngilizce bachelor çok nadirdir. Yani liseyi bitirip "İngilizce mimarlık lisansı okurum" planı büyük ölçüde çalışmaz.

**Master** tarafında ise nefes alma alanı var. İngilizce yürüyen (ya da ağırlıklı İngilizce) programlar özellikle şu alanlarda toplanır:

- **M.Sc. Architecture** — bazı teknik üniversitelerin uluslararası master kolları.
- **Urban Design / Städtebau** — kentsel tasarım, İngilizce master pazarının en güçlü olduğu alan.
- **Integrated Design** — disiplinlerarası, tasarım+araştırma odaklı programlar.

Bir ayrımı baştan netleştir: **Mimarlık (Architektur) inşaat mühendisliği (Bauingenieurwesen) değildir.** Architektur = tasarım, mekan, kompozisyon; Bauingenieurwesen = strüktür, statik, yapı hesabı. İngilizce master ararken ikisini karıştırma; ilgin strüktür/hesaba kayıyorsa mühendislik tarafına da bak — farkı görmek için [Almanya'da mühendislik okumak](/tr/blog/studying-engineering-in-germany-as-a-foreigner) yazısı iyi bir başlangıç.

## Hangi programlar? (İngilizce master haritası)

Aşağıdaki tablo, İngilizce (ya da ağırlıklı İngilizce) yürüyen tipik program tiplerini ve okulları gösterir. Program adları ve dil koşulları dönemden döneme değişebilir — **her zaman okulun güncel sayfasından doğrula.**

| Okul | Tipik program | Alan | Not |
|---|---|---|---|
| **TU Berlin** | Urban Design / Architecture (uluslararası kollar) | Kentsel tasarım / mimarlık | Güçlü uluslararası profil |
| **TU München (TUM)** | M.Sc. Architecture, Urbanism-Landscape | Mimarlık / kentsel | Tepe teknik üniversite, rekabetçi |
| **Bauhaus-Universität Weimar** | Integrated Urban Development, MediaArchitecture | Kentsel / tasarım | Bauhaus mirası, tasarım odaklı |
| **HafenCity Universität Hamburg** | Urban Design / REAP | Kentsel tasarım / planlama | Kentsel tasarımda köklü |
| **TU Darmstadt / Uni Stuttgart / KIT** | Seçili İngilizce master kolları | Mimarlık / hesaplamalı tasarım | Program bazında değişir |

*2025/2026 itibarıyla, yaklaşık; program listeleri sık değişir, doğrula.*

Gördüğün gibi denge **kentsel tasarıma (Urban Design/Städtebau)** doğru kayar. Saf "İngilizce M.Sc. Architecture" seçenekleri daha az; birçok öğrenci bu yüzden Urban Design'a yönelir.

## Şartlar: lisans + portfolyo + İngilizce

İngilizce bir mimarlık masterına girmenin üç ana kapısı var:

1. **İlgili bir lisans (bachelor):** Genelde mimarlık ya da yakın bir alanda tamamlanmış bir derece. Bazı Urban Design programları planlama/coğrafya/peyzaj kökenlilere de açıktır, ama mimarlık masterları çoğunlukla mimarlık lisansı bekler.
2. **Portfolyo (Mappe):** Bu neredeyse pazarlık dışıdır. Mimarlık ve tasarım programları **portfolyo** ve/veya **yetenek/uygunluk değerlendirmesi (Eignungsprüfung)** ister. Notların iyi olsa bile portfolyon zayıfsa elenirsin. NC-kısıtı da olabilir.
3. **İngilizce yeterlik:** Genelde **IELTS ~6.5 / TOEFL ~90** civarı (program bazında değişir; doğrula). "Ders İngilizce ama başvuru için İngilizce belgesi istemez" diye varsayma.

Kısacası: İngilizce master seni **Almanca dil bariyerinden** kurtarır ama **portfolyo bariyerinden** kurtarmaz. Erken başla.

## Ücret: kamu büyük ölçüde ücretsiz, ama BW istisnası

Para tarafı çoğu öğrenci için iyi haber:

- **Kamu üniversiteleri** genelde öğrenim ücreti almaz; ödediğin dönemlik **Semesterbeitrag (~150-350€)** idari katkı + çoğu zaman toplu taşıma bileti (Semesterticket) içerir.
- **Baden-Württemberg (BW) istisnası:** Bu eyalette **AB dışı (non-EU)** öğrencilerden genelde **~1.500€/dönem** öğrenim ücreti alınır. Stuttgart ve KIT bu eyalettedir — bütçeni buna göre planla.
- **Özel üniversiteler** çok daha pahalıdır.

*2025/2026 itibarıyla, yaklaşık; eyalet ve okul politikaları değişir, doğrula.* Genel tabloyu ve master-sonrası vize seçeneklerini [Almanya: Master mı, İş Arama Vizesi mi?](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) yazısında bulabilirsin.

## Almancasız tuzağı: stüdyo, büro ve lisans Almanca ister

İşte kimsenin broşürde yazmadığı dürüst kısım. Ders İngilizce olsa bile Almancasız hayatın üç büyük duvarı var:

- **Stüdyo ve kritik kültürü:** Mimarlık eğitiminin kalbi stüdyodur. Program resmi olarak İngilizce olsa bile ortam, misafir eleştirmenler ve gündelik iletişim sık sık Almancaya kayar. Almanca bilmeyen izole kalabilir.
- **Büro (staj ve iş):** Almanya'daki mimarlık bürolarının çoğu **Almanca** çalışır — müşteriyle, belediyeyle, inşaat mevzuatıyla (Bauordnung) uğraşırsın. Master bitince iş ararken Almanca neredeyse zorunlu hale gelir.
- **Lisans (Architektenkammer):** "Architekt" unvanını taşımak ve inşaat ruhsatı sunma yetkisi için bir eyaletin **Architektenkammer**'ine (mimarlar odası) kayıt gerekir. Tipik gerek: **akredite derece (genelde 5 yıl = bachelor + master, min 300 ECTS)** + **~2 yıl pratik deneyim.** Bu süreç ve mesleki hayat büyük ölçüde Almanca yürür — detay için kardeş yazımıza bak: [Almanya'da lisanslı mimar olmak (Architektenkammer)](/tr/blog/becoming-a-licensed-architect-in-germany-architektenkammer).

Yani plan şu olmalı: İngilizce masterla **başla**, ama **ilk günden Almanca öğrenmeye başla.** B1-B2 seviyesine ulaşmak, stüdyoda ve iş piyasasında oyunu değiştirir.

## Portfolyo (Mappe) nasıl hazırlanır?

Portfolyo başvurunun kalbidir. Birkaç dürüst ipucu:

- **Kalite > nicelik:** 8-12 güçlü proje, 30 vasat projeden iyidir. Her proje bir fikir anlatsın.
- **Süreci göster:** Sadece parlak render değil; eskiz, diyagram, maket fotoğrafı, konsept gelişimi. Jüri nasıl düşündüğünü görmek ister.
- **Çeşitlilik:** El çizimi, dijital, model — farklı becerileri göster. Urban Design başvurusu için ölçek/kentsel analiz projeleri ekle.
- **Sunum tutarlı olsun:** Tipografi, düzen ve dil (İngilizce) baştan sona tutarlı. Portfolyonun kendisi bir tasarım ürünüdür.
- **Erken başla:** İyi bir portfolyo haftalar sürer. Son haftaya bırakma.

## Sonuç & dürüst tavsiye

İngilizce mimarlık/Urban Design masterı Almanya'da **gerçek bir yol** — ama sihirli bir kaçış değil. Dürüst özet:

- **Bachelor'ı İngilizce okuma planı çoğunlukla çalışmaz;** İngilizce seçenek ağırlıkla masterdadır ve **Urban Design/Städtebau** en bol alandır.
- Seni **portfolyo (Mappe)** ve İngilizce yeterlik elemesi bekler — erken hazırlan.
- Ücret çoğu kamu okulunda düşük; **Baden-Württemberg'de non-EU ~1.500€/dönem** istisnasını unutma.
- **Almancasız tuzağına** düşme: ders İngilizce olsa da stüdyo, büro ve lisans (Architektenkammer) büyük ölçüde Almanca. İlk günden Almanca çalış.

Tam resmi görmek için küme kardeşlerimize göz at: [Almanya'da mimarlık okumak (kapsamlı rehber)](/tr/blog/studying-architecture-in-germany-as-a-foreigner) ve [Almanya'da mimar olarak çalışmak (maaş & iş piyasası)](/tr/blog/working-as-an-architect-in-germany-salary-job-market).

*Bu yazıdaki sayılar, program adları, ücretler ve şartlar 2025/2026 dönemine ait yaklaşık bilgilerdir ve sık değişir. Başvurmadan önce okulun ve ilgili Architektenkammer'in güncel resmi kaynaklarından doğrula.*
MD;
        $deBody = <<<'MD'
"Ich will in Deutschland Architektur studieren, aber ich spreche kein Deutsch" — diesen Satz hören wir oft. Die gute Nachricht: Es gibt englischsprachige Architektur-Master. Die schlechte: Ihre Zahl ist begrenzt, die meisten liegen auf **Master**-Ebene, und das "Leben ohne Deutsch" ist nicht so reibungslos, wie es klingt. Dieser Leitfaden erklärt dir ehrlich die **englischsprachigen M.Sc. Architecture / Urban Design (Städtebau) / Integrated Design**, die echten Voraussetzungen und die "Ohne-Deutsch-Falle", über die kaum jemand spricht.

## Englischsprachige Architektur: Bachelor selten, Master begrenzt, aber vorhanden

Zuerst die Erwartung geraderücken. Der Architektur-**Bachelor** (B.Sc./B.A.) läuft in Deutschland fast vollständig auf **Deutsch (meist C1)** — englische Bachelor sind sehr selten. Der Plan "Ich mache nach dem Abi einen englischen Architektur-Bachelor" funktioniert also meist nicht.

Auf **Master**-Ebene hast du Luft zum Atmen. Englischsprachige (oder überwiegend englische) Programme konzentrieren sich vor allem hier:

- **M.Sc. Architecture** — internationale Master-Tracks einiger Technischer Universitäten.
- **Urban Design / Städtebau** — hier ist das englischsprachige Angebot am stärksten.
- **Integrated Design** — interdisziplinäre, entwurfs- und forschungsorientierte Programme.

Kläre eine Unterscheidung von Anfang an: **Architektur ist nicht Bauingenieurwesen.** Architektur = Entwurf, Raum, Komposition; Bauingenieurwesen = Tragwerk, Statik, Baukonstruktion. Verwechsle beides bei der Suche nicht; wenn dich Tragwerk/Statik mehr reizt, schau auch auf die Ingenieurseite — als Einstieg eignet sich [Ingenieurwesen in Deutschland studieren](/de/blog/studying-engineering-in-germany-as-a-foreigner-de).

## Welche Programme? (Landkarte der englischen Master)

Die folgende Tabelle zeigt typische englischsprachige (oder überwiegend englische) Programmtypen und Hochschulen. Programmnamen und Sprachanforderungen ändern sich von Semester zu Semester — **prüfe immer die aktuelle Seite der Hochschule.**

| Hochschule | Typisches Programm | Bereich | Hinweis |
|---|---|---|---|
| **TU Berlin** | Urban Design / Architecture (internationale Tracks) | Städtebau / Architektur | Starkes internationales Profil |
| **TU München (TUM)** | M.Sc. Architecture, Urbanism-Landscape | Architektur / Städtebau | Top-TU, kompetitiv |
| **Bauhaus-Universität Weimar** | Integrated Urban Development, MediaArchitecture | Städtebau / Design | Bauhaus-Erbe, entwurfsorientiert |
| **HafenCity Universität Hamburg** | Urban Design / REAP | Städtebau / Planung | Etabliert im Städtebau |
| **TU Darmstadt / Uni Stuttgart / KIT** | ausgewählte englische Master-Tracks | Architektur / computational design | Je nach Programm |

*Stand ca. 2025/2026, ungefähr; Programmlisten ändern sich häufig, prüfe nach.*

Wie du siehst, verschiebt sich das Gleichgewicht Richtung **Urban Design/Städtebau**. Reine "M.Sc. Architecture" auf Englisch sind seltener; viele Studierende wenden sich deshalb dem Urban Design zu.

## Voraussetzungen: Bachelor + Mappe + Englisch

Es gibt drei Haupttore in einen englischsprachigen Architektur-Master:

1. **Ein einschlägiger Bachelor:** Meist ein abgeschlossener Abschluss in Architektur oder einem nahen Fach. Manche Urban-Design-Programme öffnen sich auch für Planung/Geografie/Landschaft, aber Architektur-Master erwarten überwiegend einen Architektur-Bachelor.
2. **Portfolio (Mappe):** Das ist praktisch nicht verhandelbar. Architektur- und Designprogramme verlangen ein **Portfolio** und/oder eine **Eignungsprüfung**. Selbst bei guten Noten fliegst du raus, wenn die Mappe schwach ist. Ein NC ist ebenfalls möglich.
3. **Englischnachweis:** Meist etwa **IELTS ~6.5 / TOEFL ~90** (je nach Programm; prüfe nach). Nimm nicht an, dass ein englischer Studiengang keinen Englischnachweis fordert.

Kurz gesagt: Ein englischer Master befreit dich von der **deutschen Sprachbarriere**, aber nicht von der **Mappen-Barriere**. Fang früh an.

## Kosten: öffentliche Hochschulen meist gebührenfrei, aber BW-Ausnahme

Beim Geld gibt es für die meisten gute Nachrichten:

- **Öffentliche Universitäten** erheben meist keine Studiengebühr; der **Semesterbeitrag (~150-350€)** deckt Verwaltung und oft ein Semesterticket ab.
- **Ausnahme Baden-Württemberg (BW):** In diesem Bundesland zahlen **Nicht-EU-Studierende** meist eine Studiengebühr von **~1.500€/Semester**. Stuttgart und das KIT liegen dort — plane dein Budget entsprechend.
- **Private Hochschulen** sind deutlich teurer.

*Stand ca. 2025/2026, ungefähr; Regelungen der Länder und Hochschulen ändern sich, prüfe nach.* Den Gesamtüberblick und die Visa-Optionen nach dem Master findest du in [Deutschland: Master oder Jobsuche-Visum?](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).

## Die Ohne-Deutsch-Falle: Studio, Büro und Zulassung wollen Deutsch

Jetzt der ehrliche Teil, der in keiner Broschüre steht. Selbst wenn der Unterricht auf Englisch ist, hat ein Leben ohne Deutsch drei große Mauern:

- **Studio- und Kritik-Kultur:** Das Herz des Architekturstudiums ist das Studio. Auch wenn das Programm offiziell englisch ist, driften Umfeld, Gastkritiker und Alltag oft ins Deutsche. Wer kein Deutsch kann, bleibt schnell isoliert.
- **Büro (Praktikum und Job):** Die meisten Architekturbüros in Deutschland arbeiten auf **Deutsch** — mit Bauherren, Behörden und der Bauordnung. Nach dem Master ist Deutsch bei der Jobsuche fast Pflicht.
- **Zulassung (Architektenkammer):** Um den Titel "Architekt" zu führen und Bauvorlagen einreichen zu dürfen, ist die Eintragung in die **Architektenkammer** eines Bundeslandes nötig. Typische Voraussetzung: **ein akkreditierter Abschluss (meist 5 Jahre = Bachelor + Master, min. 300 ECTS)** + **ca. 2 Jahre Berufspraxis.** Dieser Weg und der Berufsalltag laufen weitgehend auf Deutsch — Details in unserem Schwesterartikel: [Architekt werden in Deutschland (Architektenkammer)](/de/blog/becoming-a-licensed-architect-in-germany-architektenkammer-de).

Der Plan sollte also lauten: **Starte** mit dem englischen Master, aber **lerne ab dem ersten Tag Deutsch.** B1-B2 zu erreichen verändert das Spiel im Studio und auf dem Arbeitsmarkt.

## Wie bereitest du die Mappe vor?

Das Portfolio ist das Herz der Bewerbung. Ein paar ehrliche Tipps:

- **Qualität > Quantität:** 8-12 starke Projekte sind besser als 30 mittelmäßige. Jedes Projekt soll eine Idee erzählen.
- **Zeige den Prozess:** Nicht nur glänzende Renderings; Skizzen, Diagramme, Modellfotos, Konzeptentwicklung. Die Jury will sehen, wie du denkst.
- **Vielfalt:** Handzeichnung, digital, Modell — zeige verschiedene Fähigkeiten. Für Urban Design füge Projekte mit Maßstab/städtebaulicher Analyse hinzu.
- **Konsistente Präsentation:** Typografie, Layout und Sprache (Englisch) durchgehend einheitlich. Die Mappe selbst ist ein Designprodukt.
- **Fang früh an:** Eine gute Mappe braucht Wochen. Lass es nicht auf die letzte Woche ankommen.

## Fazit & ehrlicher Rat

Ein englischsprachiger Architektur-/Urban-Design-Master in Deutschland ist ein **echter Weg** — aber kein magischer Ausweg. Ehrliche Zusammenfassung:

- **Der Plan, den Bachelor auf Englisch zu machen, klappt meist nicht;** das englische Angebot liegt vor allem im Master, und **Urban Design/Städtebau** ist am reichsten.
- Dich erwartet eine Auswahl über **Portfolio (Mappe)** und Englischnachweis — bereite dich früh vor.
- Die Gebühren sind an den meisten öffentlichen Hochschulen niedrig; vergiss die Ausnahme **Baden-Württemberg (~1.500€/Semester für Nicht-EU)** nicht.
- Tappe nicht in die **Ohne-Deutsch-Falle:** Auch bei englischem Unterricht laufen Studio, Büro und Zulassung (Architektenkammer) weitgehend auf Deutsch. Lerne ab Tag eins Deutsch.

Für das ganze Bild schau zu unseren Schwesterartikeln: [Architektur in Deutschland studieren (umfassender Leitfaden)](/de/blog/studying-architecture-in-germany-as-a-foreigner-de) und [Als Architekt in Deutschland arbeiten (Gehalt & Arbeitsmarkt)](/de/blog/working-as-an-architect-in-germany-salary-job-market-de).

*Die Zahlen, Programmnamen, Gebühren und Voraussetzungen in diesem Artikel sind ungefähre Angaben für 2025/2026 und ändern sich häufig. Prüfe vor der Bewerbung die aktuellen offiziellen Quellen der Hochschule und der zuständigen Architektenkammer.*
MD;
        $enBody = <<<'MD'
"I want to study architecture in Germany, but I don't speak German" — we hear this a lot. The good news: English-taught architecture master's programmes do exist. The bad news: there aren't many, most are at **master's** level, and the "life without German" dream is not as smooth as it sounds. This guide honestly explains the **English-taught M.Sc. Architecture / Urban Design (Städtebau) / Integrated Design** programmes, the real requirements, and the "no-German trap" almost nobody mentions.

## English-taught architecture: bachelor's rare, master's limited but real

Let's set expectations first. In Germany the architecture **bachelor's** (B.Sc./B.A. Architektur) runs almost entirely in **German (usually C1)** — English bachelor's are very rare. So the plan "finish high school and do an English architecture bachelor's" mostly doesn't work.

At **master's** level, you have room to breathe. English-taught (or predominantly English) programmes cluster mainly in these areas:

- **M.Sc. Architecture** — the international master's tracks of some technical universities.
- **Urban Design / Städtebau** — this is where the English-taught offering is strongest.
- **Integrated Design** — interdisciplinary, design- and research-focused programmes.

Clear up one distinction from the start: **architecture (Architektur) is not civil engineering (Bauingenieurwesen).** Architektur = design, space, composition; Bauingenieurwesen = structure, statics, construction calculation. Don't mix them up when searching; if structure/statics appeal to you more, look at the engineering side too — a good starting point is [studying engineering in Germany](/en/blog/studying-engineering-in-germany-as-a-foreigner-en).

## Which programmes? (a map of English-taught master's)

The table below shows typical English-taught (or mostly English) programme types and schools. Programme names and language requirements change from term to term — **always verify on the school's current page.**

| School | Typical programme | Field | Note |
|---|---|---|---|
| **TU Berlin** | Urban Design / Architecture (international tracks) | Urban design / architecture | Strong international profile |
| **TU München (TUM)** | M.Sc. Architecture, Urbanism-Landscape | Architecture / urban | Top technical university, competitive |
| **Bauhaus-Universität Weimar** | Integrated Urban Development, MediaArchitecture | Urban / design | Bauhaus heritage, design-focused |
| **HafenCity Universität Hamburg** | Urban Design / REAP | Urban design / planning | Established in urban design |
| **TU Darmstadt / Uni Stuttgart / KIT** | selected English master's tracks | Architecture / computational design | Varies by programme |

*As of roughly 2025/2026, approximate; programme lists change often, verify.*

As you can see, the balance tips toward **urban design (Urban Design/Städtebau)**. Pure "M.Sc. Architecture" in English is scarcer; many students therefore pivot to urban design.

## Requirements: bachelor's + portfolio + English

There are three main gates into an English-taught architecture master's:

1. **A relevant bachelor's degree:** usually a completed degree in architecture or a close field. Some urban-design programmes also open to planning/geography/landscape backgrounds, but architecture master's mostly expect an architecture bachelor's.
2. **Portfolio (Mappe):** this is essentially non-negotiable. Architecture and design programmes require a **portfolio** and/or an **aptitude assessment (Eignungsprüfung)**. Even with good grades you're out if the portfolio is weak. An NC (numerus clausus) may also apply.
3. **English proficiency:** usually around **IELTS ~6.5 / TOEFL ~90** (varies by programme; verify). Don't assume an English-taught course won't ask for an English certificate.

In short: an English master's frees you from the **German language barrier** but not from the **portfolio barrier**. Start early.

## Fees: public schools mostly free, but the BW exception

On money, the news is good for most students:

- **Public universities** usually charge no tuition; the **semester fee (Semesterbeitrag, ~€150-350)** covers administration and often a public-transport ticket (Semesterticket).
- **Baden-Württemberg (BW) exception:** in this state, **non-EU students** usually pay tuition of about **€1,500/semester**. Stuttgart and KIT are located there — budget accordingly.
- **Private universities** are far more expensive.

*As of roughly 2025/2026, approximate; state and school policies change, verify.* For the big picture and post-master's visa options, see [Germany: master's vs job-seeker visa](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

## The no-German trap: studio, office and licensing want German

Here's the honest part no brochure prints. Even if classes are in English, a life without German has three big walls:

- **Studio and critique culture:** the heart of architecture education is the studio. Even when a programme is officially English, the environment, guest critics and everyday interaction often drift into German. Those without German can end up isolated.
- **Office (internship and job):** most architecture offices in Germany work in **German** — dealing with clients, authorities and building regulations (Bauordnung). After the master's, German is almost mandatory when job-hunting.
- **Licensing (Architektenkammer):** to use the title "Architekt" and be entitled to submit building applications, you must register with a state's **Architektenkammer** (chamber of architects). Typical requirement: **an accredited degree (usually 5 years = bachelor + master, min. 300 ECTS)** + **about 2 years of practical experience.** This path and professional life run largely in German — see our sister article for details: [becoming a licensed architect in Germany (Architektenkammer)](/en/blog/becoming-a-licensed-architect-in-germany-architektenkammer-en).

So the plan should be: **start** with the English master's, but **learn German from day one.** Reaching B1-B2 changes the game in the studio and on the job market.

## How to prepare the portfolio (Mappe)

The portfolio is the heart of the application. A few honest tips:

- **Quality > quantity:** 8-12 strong projects beat 30 mediocre ones. Each project should tell an idea.
- **Show the process:** not just glossy renders; sketches, diagrams, model photos, concept development. The jury wants to see how you think.
- **Variety:** hand drawing, digital, model — show different skills. For urban-design applications, add scale/urban-analysis projects.
- **Consistent presentation:** typography, layout and language (English) coherent throughout. The portfolio itself is a design product.
- **Start early:** a good portfolio takes weeks. Don't leave it to the last week.

## Conclusion & honest advice

An English-taught architecture/urban-design master's in Germany is a **real path** — but not a magic escape. Honest summary:

- **The plan to do the bachelor's in English mostly fails;** the English-taught option is mainly at master's level, and **Urban Design/Städtebau** is the richest field.
- Expect a filter of **portfolio (Mappe)** and English proficiency — prepare early.
- Fees are low at most public schools; don't forget the **Baden-Württemberg exception (~€1,500/semester for non-EU)**.
- Don't fall into the **no-German trap:** even with English classes, studio, office and licensing (Architektenkammer) run largely in German. Learn German from day one.

For the full picture, see our sister articles: [studying architecture in Germany (comprehensive guide)](/en/blog/studying-architecture-in-germany-as-a-foreigner-en) and [working as an architect in Germany (salary & job market)](/en/blog/working-as-an-architect-in-germany-salary-job-market-en).

*The figures, programme names, fees and requirements in this article are approximate for 2025/2026 and change frequently. Before applying, verify with the current official sources of the school and the relevant Architektenkammer.*
MD;

        $variants = [
            'tr' => ['slug'=>'english-taught-architecture-masters-in-germany-without-german',    'title'=>'Almancasız Almanya\'da Mimarlık: İngilizce Master ve Urban Design (2026)', 'excerpt'=>'Almanca bilmeden Almanya\'da mimarlık okunur mu? İngilizce M.Sc. Architecture, Urban Design (Städtebau) ve Integrated Design programları, portfolyo (Mappe) şartı, ücretler ve kimsenin söylemediği "Almancasız tuzağı" — stüdyo, büro ve lisans için Almanca gerçeği.', 'meta_title'=>'İngilizce Mimarlık Master Almanya: Urban Design (2026)', 'meta_description'=>'Almancasız Almanya\'da mimarlık: İngilizce M.Sc. Architecture ve Urban Design programları, portfolyo şartı, ücretler ve Almancasız tuzağı.', 'body'=>$trBody],
            'de' => ['slug'=>'english-taught-architecture-masters-in-germany-without-german-de', 'title'=>'Architektur in Deutschland ohne Deutsch: Englische Master & Urban Design (2026)',        'excerpt'=>'Kann man in Deutschland Architektur ohne Deutschkenntnisse studieren? Englischsprachige M.Sc. Architecture, Urban Design (Städtebau) und Integrated Design, die Mappen-Voraussetzung, Kosten und die "Ohne-Deutsch-Falle": Studio, Büro und Zulassung wollen Deutsch.',   'meta_title'=>'Englische Architektur-Master Deutschland: Urban Design (2026)',  'meta_description'=>'Architektur ohne Deutsch: englischsprachige M.Sc. Architecture und Urban Design, Mappe, Kosten und die Ohne-Deutsch-Falle.',   'body'=>$deBody],
            'en' => ['slug'=>'english-taught-architecture-masters-in-germany-without-german-en', 'title'=>'Architecture in Germany Without German: English-Taught Master\'s & Urban Design (2026)',        'excerpt'=>'Can you study architecture in Germany without German? English-taught M.Sc. Architecture, Urban Design (Städtebau) and Integrated Design, the portfolio (Mappe) requirement, fees, and the "no-German trap": studio, office and licensing want German.',   'meta_title'=>'English-Taught Architecture Master\'s Germany: Urban Design (2026)',  'meta_description'=>'Architecture in Germany without German: English-taught M.Sc. Architecture and Urban Design, portfolio, fees, and the no-German trap.',   'body'=>$enBody],
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
            'english-taught-architecture-masters-in-germany-without-german',
            'english-taught-architecture-masters-in-germany-without-german-de',
            'english-taught-architecture-masters-in-germany-without-german-en',
        ])->delete();
    }
};

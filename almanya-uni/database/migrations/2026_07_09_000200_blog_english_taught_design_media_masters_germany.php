<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almancasız Almanya'da İngilizce tasarım & medya master (2026).
 * Doğrulandı: İngilizce tasarım/medya/UX master'lar var ama sınırlı; portfolyo (Mappe) + lisans
 * + İngilizce yeterlik şart; kamu ~150-350€/dönem (BW non-EU ~1.500€), özel okullar pahalı;
 * ders İngilizce olsa da ajans/freelance/serbest çalışma için Almanca fiilen gerekli. 2025/2026 ~, doğrula.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'd4e20000-2222-4b0f-9f10-dd0bee11bb02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almanya'da tasarım okumak istiyorum ama Almancam yok" — bu cümleyi sık duyuyoruz. İyi haber: İngilizce verilen tasarım, medya ve UX master programları var. Kötü haber: sayıları sınırlı, çoğu **master** seviyesinde ve "Almancasız hayat" broşürde yazdığı kadar pürüzsüz değil. Bu rehber, Almanya'daki **İngilizce Design / Media / UX-UI / Interaction** master'larını, gerçek şartları ve kimsenin konuşmadığı "Almancasız gerçeği"ni dürüstçe anlatıyor.

## İngilizce tasarım/medya/UX master var — ama sınırlı

Önce beklentiyi düzeltelim. Tasarım **lisansı** (Grafik/İletişim/Ürün/Moda) Almanya'da neredeyse tamamen **Almanca (çoğunlukla C1)** yürür; İngilizce lisans çok nadirdir. "Liseden sonra İngilizce tasarım lisansı yaparım" planı genelde işlemez.

**Master** seviyesinde nefes alacak alan var. İngilizce (ya da ağırlıkla İngilizce) programlar özellikle şu alanlarda yoğunlaşır:

- **Mediendesign / Digital Media / Interaction Design** — dijital ve ekran odaklı tasarım.
- **UX/UI & Human-Computer Interaction (HCI)** — en istihdam edilebilir ve İngilizce açısından en zengin dal.
- **Integrated / Communication Design** — bazı programların uluslararası track'leri.
- **Media Art / New Media** — sanat-teknoloji kesişimi (bazı Kunsthochschule'lerde).

Baştan bir ayrımı netleştir: **saf "güzel sanat" (Freie Kunst) İngilizce seçeneği çok azdır** ve gelir tarafı güvencesizdir; İngilizce arz asıl olarak **dijital/medya/UX** tarafındadır. Almanya'da sanat & tasarımın genel manzarası, kabul mantığı (NC yok, portfolyo belirleyici) için kapsamlı kardeş rehberimize bak: [Almanya'da sanat & tasarım okumak (uluslararası öğrenci rehberi)](/tr/blog/studying-art-and-design-in-germany-as-a-foreigner).

## Hangi programlar? (kamu vs özel haritası)

Aşağıdaki tablo, tipik İngilizce (ya da ağırlıkla İngilizce) program türlerini ve kurum tiplerini gösterir. **Program adları ve dil şartları her dönem değişir — mutlaka okulun güncel sayfasını kontrol et.**

| Kurum tipi | Tipik program | Alan | Not |
|---|---|---|---|
| **Kamu FH/HAW** (ör. TH Köln/KISD, FH Potsdam, HTW Berlin) | Integrated / Interface / Media Design MA | İletişim/medya/UX | Ücretsiz sayılır, rekabetçi |
| **Kamu Kunsthochschule** (ör. Bauhaus Weimar, Offenbach HfG) | Media Art & Design, MediaArchitecture, HCI | Medya sanatı / etkileşim | Portfolyo çok belirleyici |
| **Teknik üniversite** | HCI / Human Factors (M.Sc.) | UX araştırması | Tasarım+bilgisayar melezi |
| **Özel tasarım okulu** (ör. UE, Berlin Int'l, Macromedia türü) | UX/UI, Motion, Game, Communication | Dijital tasarım | İngilizce bol ama **pahalı** |

*2025/2026 itibarıyla, yaklaşık; program listeleri sık değişir, doğrula.*

Görüldüğü gibi denge **dijital/medya/UX** tarafına kayıyor. Saf "İngilizce grafik tasarım masterı" kamu okullarında görece azdır; birçok öğrenci bu yüzden UX/medya tarafına yönelir ya da İngilizce arzın daha bol olduğu özel okullara bakar.

## Şartlar: portfolyo (Mappe) + lisans + İngilizce

İngilizce bir tasarım/medya masterına üç ana kapıdan girilir:

1. **İlgili bir lisans:** Genelde tasarım, medya, güzel sanat, mimarlık ya da yakın bir alanda tamamlanmış derece. Bazı UX/HCI programları bilgisayar/psikoloji kökenlilere de açılır; ama tasarım master'ları ağırlıkla tasarım temeli bekler.
2. **Portfolyo (Mappe):** Bu pratikte pazarlık konusu değildir. Tasarım ve medya programları **portfolyo** ve/veya bir **Eignungsprüfung** (yetenek sınavı) ister. Notların iyi olsa bile portfolyo zayıfsa elenirsin — çünkü Almanya'da sanat/tasarım kabulünde NC değil, **portfolyo belirleyicidir.** Nasıl hazırlanacağını adım adım anlatan kardeş yazımıza bak: [Portfolyo (Mappe) nasıl hazırlanır?](/tr/blog/how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools).
3. **İngilizce yeterlik:** Çoğunlukla **IELTS ~6.5 / TOEFL ~90** civarı (programa göre; doğrula). "İngilizce program İngilizce belgesi istemez" varsayımına düşme.

Kısacası: İngilizce master seni **Almanca dil bariyerinden** kurtarır ama **portfolyo bariyerinden** kurtarmaz. Erken başla.

## Ücret: kamu büyük ölçüde ücretsiz, özel okullar pahalı

Para tarafı çoğu öğrenci için iyi haber:

- **Kamu üniversiteleri ve sanat/tasarım okulları** genelde öğrenim ücreti almaz; dönemlik **Semesterbeitrag (~150-350€)** idari katkı + çoğu zaman toplu taşıma bileti (Semesterticket) içerir.
- **Baden-Württemberg (BW) istisnası:** Bu eyalette **AB dışı (non-EU)** öğrencilerden genelde **~1.500€/dönem** öğrenim ücreti alınır — bütçeni buna göre planla.
- **Özel tasarım okulları çok daha pahalıdır:** yıllık binlerce ila on binlerce euroya çıkabilir. İngilizce arzın bol olduğu yer çoğu zaman burasıdır; parayı ödemeden önce mezun istihdamını ve portfolyo çıktısını sorgula.

*2025/2026 itibarıyla, yaklaşık; eyalet ve okul politikaları değişir, doğrula.* Master sonrası vize seçeneklerini (18 ay iş arama) [Almanya: Master mı, İş Arama Vizesi mi?](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) yazısında bulabilirsin.

## Almancasız gerçeği: stüdyo, ajans ve freelance Almanca ister

İşte kimsenin broşürde yazmadığı dürüst kısım. Ders İngilizce olsa bile Almancasız hayatın duvarları var:

- **Stüdyo ve gündelik iletişim:** Program resmi olarak İngilizce olsa da ortam, misafir jüriler ve sınıf dışı hayat sık sık Almancaya kayar. Almanca bilmeyen izole kalabilir.
- **Ajans ve in-house iş:** Almanya'daki tasarım ajanslarının ve şirket içi ekiplerin çoğu **Almanca** çalışır — müşteri sunumu, brief, ekip iletişimi Almanca döner. Master bitince iş ararken Almanca çoğu pozisyonda beklenir. İstisna: bazı büyük şehir UX/dijital ürün ekipleri İngilizce yürür (özellikle uluslararası tech şirketleri).
- **Freelance ve serbest çalışma:** Serbest tasarımcıysan müşteri edinmek, sözleşme, fatura ve yerel network büyük ölçüde Almancadır. Almanca olmadan freelance pazarına girmek çok zordur.

Yani plan şu olmalı: İngilizce masterla **başla**, ama **ilk günden Almanca öğrenmeye başla.** B1-B2 seviyesine ulaşmak stüdyoda ve iş piyasasında oyunu değiştirir. UX/dijital tarafta İngilizce iş bulma şansın diğer tasarım dallarından yüksektir — bu manzarayı [Almanya'da IT/tech'te çalışmak (Mavi Kart & maaş)](/tr/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary) yazısıyla birlikte oku.

## Başvuru & DAAD

Pratik adımlar:

- **Başvuru kanalı:** Bazı okullar doğrudan başvuru alır, bazıları **uni-assist** üzerinden. Her programın kendi son tarihi ve portfolyo formatı (PDF boyutu, sayfa sayısı, teslim şekli) vardır — erken oku.
- **Zaman planı:** İyi bir portfolyo haftalar sürer; dil belgesi (IELTS/TOEFL) için de sınav tarihi ayarla. Başvuru döneminden **en az birkaç ay önce** hazırlığa başla.
- **DAAD & burslar:** Master için **DAAD** bursları ve program bazlı destekler olabilir; ayrıca yaşam giderini karşılamak için haftada ~20 saat çalışma izni öğrenci vizesinde vardır. DAAD'ın güncel burs veritabanını mutlaka kontrol et.
- **Denklik:** Lisans diplomanın Almanya'da tanınıp tanınmadığını (anabin/uni-assist ön değerlendirme) baştan doğrula.

Karşılaştırma için komşu bir alandaki İngilizce master manzarasına da göz atmak faydalı: [Almanya'da İngilizce mimarlık master'ları (Almancasız)](/tr/blog/english-taught-architecture-masters-in-germany-without-german).

## Sonuç & dürüst tavsiye

Almanya'da İngilizce tasarım/medya/UX masterı **gerçek bir yol** — ama sihirli bir kaçış değil. Dürüst özet:

- **Lisansı İngilizce okuma planı çoğunlukla çalışmaz;** İngilizce seçenek ağırlıkla masterda ve **dijital/medya/UX** en bol alan. Saf güzel sanat İngilizce seçeneği çok az.
- Seni **portfolyo (Mappe)** ve İngilizce yeterlik bekler — erken hazırlan; portfolyo belirleyicidir.
- Ücret çoğu kamu okulunda düşük; **BW'de non-EU ~1.500€/dönem** ve **özel okulların pahalı** olduğunu unutma.
- **Almancasız gerçeğine** hazır ol: ders İngilizce olsa da ajans, in-house ve freelance büyük ölçüde Almanca. İlk günden Almanca çalış; UX/dijitalde İngilizce iş şansın en yüksek.

Tam resmi görmek için küme kardeşlerimize göz at: [Almanya'da sanat & tasarım okumak](/tr/blog/studying-art-and-design-in-germany-as-a-foreigner), [Portfolyo (Mappe) nasıl hazırlanır?](/tr/blog/how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools) ve [Almanya'da tasarımcı olarak çalışmak (kariyer & maaş)](/tr/blog/working-as-a-designer-in-germany-careers-salary-and-reality).

*Bu yazıdaki sayılar, program adları, ücretler ve şartlar 2025/2026 dönemine ait yaklaşık bilgilerdir ve sık değişir. Başvurmadan önce okulun ve DAAD'ın güncel resmi kaynaklarından doğrula.*
MD;

        $deBody = <<<'MD'
"Ich will in Deutschland Design studieren, aber ich spreche kein Deutsch" — diesen Satz hören wir oft. Die gute Nachricht: Es gibt englischsprachige Master in Design, Medien und UX. Die schlechte: Ihre Zahl ist begrenzt, die meisten liegen auf **Master**-Ebene, und das "Leben ohne Deutsch" ist nicht so reibungslos, wie es in der Broschüre klingt. Dieser Leitfaden erklärt dir ehrlich die englischsprachigen **Design / Media / UX-UI / Interaction** Master, die echten Voraussetzungen und die "Ohne-Deutsch-Realität", über die kaum jemand spricht.

## Englischer Design-/Medien-/UX-Master: vorhanden, aber begrenzt

Zuerst die Erwartung geraderücken. Der Design-**Bachelor** (Grafik/Kommunikation/Produkt/Mode) läuft in Deutschland fast vollständig auf **Deutsch (meist C1)**; englische Bachelor sind sehr selten. Der Plan "Ich mache nach dem Abi einen englischen Design-Bachelor" funktioniert also meist nicht.

Auf **Master**-Ebene hast du Luft zum Atmen. Englischsprachige (oder überwiegend englische) Programme konzentrieren sich vor allem hier:

- **Mediendesign / Digital Media / Interaction Design** — digital und bildschirmorientiert.
- **UX/UI & Human-Computer Interaction (HCI)** — die beschäftigungsstärkste und englisch-reichste Richtung.
- **Integrated / Communication Design** — internationale Tracks einiger Hochschulen.
- **Media Art / New Media** — Schnittstelle Kunst-Technik (an einigen Kunsthochschulen).

Kläre eine Unterscheidung von Anfang an: **Reine "Freie Kunst" auf Englisch ist sehr rar** und die Einkommensseite ist unsicher; das englische Angebot liegt vor allem auf der **digitalen/Medien-/UX**-Seite. Den Gesamtüberblick über Kunst & Design in Deutschland und die Zulassungslogik (kein NC, Mappe entscheidet) findest du in unserem umfassenden Schwesterartikel: [Kunst & Design in Deutschland studieren (Leitfaden für Internationale)](/de/blog/studying-art-and-design-in-germany-as-a-foreigner-de).

## Welche Programme? (Landkarte öffentlich vs. privat)

Die folgende Tabelle zeigt typische englischsprachige (oder überwiegend englische) Programmtypen und Hochschularten. **Programmnamen und Sprachanforderungen ändern sich jedes Semester — prüfe immer die aktuelle Seite der Hochschule.**

| Hochschultyp | Typisches Programm | Bereich | Hinweis |
|---|---|---|---|
| **Öffentliche FH/HAW** (z. B. TH Köln/KISD, FH Potsdam, HTW Berlin) | Integrated / Interface / Media Design MA | Kommunikation/Medien/UX | Gebührenfrei, kompetitiv |
| **Öffentliche Kunsthochschule** (z. B. Bauhaus Weimar, HfG Offenbach) | Media Art & Design, MediaArchitecture, HCI | Medienkunst / Interaktion | Mappe sehr entscheidend |
| **Technische Universität** | HCI / Human Factors (M.Sc.) | UX-Forschung | Design+Informatik-Mix |
| **Private Designschule** (z. B. UE, Berlin Int'l, Macromedia-Typ) | UX/UI, Motion, Game, Communication | Digitales Design | Viel Englisch, aber **teuer** |

*Stand ca. 2025/2026, ungefähr; Programmlisten ändern sich häufig, prüfe nach.*

Wie du siehst, verschiebt sich das Gleichgewicht Richtung **digital/Medien/UX**. Reine "englische Grafikdesign-Master" sind an öffentlichen Häusern eher selten; viele Studierende wenden sich deshalb UX/Medien zu oder schauen auf private Schulen mit größerem englischem Angebot.

## Voraussetzungen: Mappe + Bachelor + Englisch

Es gibt drei Haupttore in einen englischsprachigen Design-/Medien-Master:

1. **Ein einschlägiger Bachelor:** Meist ein Abschluss in Design, Medien, Kunst, Architektur oder einem nahen Fach. Manche UX/HCI-Programme öffnen sich auch für Informatik/Psychologie; Design-Master erwarten aber überwiegend eine gestalterische Grundlage.
2. **Portfolio (Mappe):** Das ist praktisch nicht verhandelbar. Design- und Medienprogramme verlangen ein **Portfolio** und/oder eine **Eignungsprüfung**. Selbst bei guten Noten fliegst du raus, wenn die Mappe schwach ist — in Deutschland entscheidet nicht der NC, sondern die **Mappe.** Wie du sie aufbaust, zeigt unser Schwesterartikel Schritt für Schritt: [Wie bereitest du eine Mappe vor?](/de/blog/how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools-de).
3. **Englischnachweis:** Meist etwa **IELTS ~6.5 / TOEFL ~90** (je nach Programm; prüfe nach). Nimm nicht an, dass ein englischer Studiengang keinen Englischnachweis fordert.

Kurz gesagt: Ein englischer Master befreit dich von der **deutschen Sprachbarriere**, aber nicht von der **Mappen-Barriere**. Fang früh an.

## Kosten: öffentlich meist gebührenfrei, private Schulen teuer

Beim Geld gibt es für die meisten gute Nachrichten:

- **Öffentliche Universitäten und Kunst-/Designhochschulen** erheben meist keine Studiengebühr; der **Semesterbeitrag (~150-350€)** deckt Verwaltung und oft ein Semesterticket ab.
- **Ausnahme Baden-Württemberg (BW):** In diesem Bundesland zahlen **Nicht-EU-Studierende** meist eine Studiengebühr von **~1.500€/Semester** — plane dein Budget entsprechend.
- **Private Designschulen sind deutlich teurer:** oft mehrere Tausend bis Zehntausende Euro pro Jahr. Genau dort ist das englische Angebot oft am größten; prüfe vor der Zahlung die Absolventen-Beschäftigung und das Mappen-Ergebnis.

*Stand ca. 2025/2026, ungefähr; Regelungen der Länder und Hochschulen ändern sich, prüfe nach.* Die Visa-Optionen nach dem Master (18 Monate Jobsuche) findest du in [Deutschland: Master oder Jobsuche-Visum?](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).

## Die Ohne-Deutsch-Realität: Studio, Agentur und Freelance wollen Deutsch

Jetzt der ehrliche Teil, der in keiner Broschüre steht. Selbst wenn der Unterricht auf Englisch ist, hat ein Leben ohne Deutsch seine Mauern:

- **Studio und Alltag:** Auch wenn das Programm offiziell englisch ist, driften Umfeld, Gastjurys und das Leben außerhalb des Kurses oft ins Deutsche. Wer kein Deutsch kann, bleibt schnell isoliert.
- **Agentur und In-House-Job:** Die meisten Designagenturen und internen Teams in Deutschland arbeiten auf **Deutsch** — Kundenpräsentation, Briefing und Teamkommunikation laufen auf Deutsch. Nach dem Master wird Deutsch bei den meisten Stellen erwartet. Ausnahme: einige UX-/Digitalprodukt-Teams großer Städte arbeiten auf Englisch (vor allem internationale Tech-Firmen).
- **Freelance und Selbstständigkeit:** Als freie Designerin/freier Designer laufen Kundengewinnung, Vertrag, Rechnung und lokales Netzwerk weitgehend auf Deutsch. Ohne Deutsch ist der Einstieg in den Freelance-Markt sehr schwer.

Der Plan sollte also lauten: **Starte** mit dem englischen Master, aber **lerne ab dem ersten Tag Deutsch.** B1-B2 zu erreichen verändert das Spiel im Studio und auf dem Arbeitsmarkt. In UX/Digital sind deine Chancen auf einen englischen Job höher als in anderen Designrichtungen — lies das zusammen mit [In der IT/Tech in Deutschland arbeiten (Blue Card & Gehalt)](/de/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-de).

## Bewerbung & DAAD

Praktische Schritte:

- **Bewerbungsweg:** Manche Hochschulen nehmen Direktbewerbungen, andere über **uni-assist**. Jedes Programm hat eigene Fristen und Mappen-Formate (PDF-Größe, Seitenzahl, Abgabeform) — lies früh.
- **Zeitplan:** Eine gute Mappe braucht Wochen; für den Sprachnachweis (IELTS/TOEFL) brauchst du einen Prüfungstermin. Beginne **mindestens einige Monate vor** der Bewerbungsphase.
- **DAAD & Stipendien:** Für den Master gibt es evtl. **DAAD**-Stipendien und programmbezogene Förderungen; zudem erlaubt das Studierendenvisum Arbeit (~20 Std./Woche) für die Lebenshaltung. Prüfe die aktuelle DAAD-Stipendiendatenbank.
- **Anerkennung:** Kläre von Anfang an, ob dein Bachelor in Deutschland anerkannt wird (anabin/uni-assist Vorprüfung).

Zum Vergleich lohnt ein Blick auf das englische Master-Angebot in einem Nachbarfach: [Englischsprachige Architektur-Master in Deutschland (ohne Deutsch)](/de/blog/english-taught-architecture-masters-in-germany-without-german-de).

## Fazit & ehrlicher Rat

Ein englischsprachiger Design-/Medien-/UX-Master in Deutschland ist ein **echter Weg** — aber kein magischer Ausweg. Ehrliche Zusammenfassung:

- **Der Plan, den Bachelor auf Englisch zu machen, klappt meist nicht;** das englische Angebot liegt vor allem im Master, und **digital/Medien/UX** ist am reichsten. Reine Freie Kunst auf Englisch ist sehr selten.
- Dich erwartet eine **Mappe** und ein Englischnachweis — bereite dich früh vor; die Mappe entscheidet.
- Die Gebühren sind an den meisten öffentlichen Häusern niedrig; vergiss **BW: Nicht-EU ~1.500€/Semester** und die **teuren privaten Schulen** nicht.
- Sei auf die **Ohne-Deutsch-Realität** gefasst: Auch wenn der Unterricht englisch ist, laufen Agentur, In-House und Freelance weitgehend auf Deutsch. Lerne ab Tag eins Deutsch; in UX/Digital sind die englischen Jobchancen am höchsten.

Für das ganze Bild schau in unsere Cluster-Schwestern: [Kunst & Design in Deutschland studieren](/de/blog/studying-art-and-design-in-germany-as-a-foreigner-de), [Wie bereitest du eine Mappe vor?](/de/blog/how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools-de) und [Als Designer:in in Deutschland arbeiten (Karriere & Gehalt)](/de/blog/working-as-a-designer-in-germany-careers-salary-and-reality-de).

*Die Zahlen, Programmnamen, Gebühren und Voraussetzungen in diesem Artikel sind ungefähre Angaben für 2025/2026 und ändern sich häufig. Prüfe vor der Bewerbung die aktuellen offiziellen Quellen der Hochschule und des DAAD.*
MD;

        $enBody = <<<'MD'
"I want to study design in Germany, but I don't speak German" — we hear this a lot. The good news: there are English-taught master's programs in design, media and UX. The bad news: they are limited in number, most sit at the **master's** level, and life "without German" is not as smooth as the brochure suggests. This guide honestly walks through Germany's English-taught **Design / Media / UX-UI / Interaction** master's programs, the real requirements, and the "no-German reality" almost nobody talks about.

## English design/media/UX master's: real, but limited

First, let's reset expectations. The design **bachelor's** (graphic/communication/product/fashion) runs almost entirely in **German (usually C1)**; English-taught bachelor's are very rare. So the plan "I'll do an English design bachelor's after high school" usually doesn't work.

At the **master's** level you have room to breathe. English-taught (or mostly English) programs cluster mainly here:

- **Media Design / Digital Media / Interaction Design** — digital and screen-focused.
- **UX/UI & Human-Computer Interaction (HCI)** — the most employable and most English-rich track.
- **Integrated / Communication Design** — international tracks at some schools.
- **Media Art / New Media** — the art-technology intersection (at some Kunsthochschulen).

Clear up one distinction early: **pure "fine art" (Freie Kunst) in English is very scarce** and the income side is precarious; the English supply sits mainly on the **digital/media/UX** side. For the full landscape of art & design in Germany and the admissions logic (no NC, portfolio decides), see our comprehensive sibling guide: [Studying art & design in Germany (a guide for internationals)](/en/blog/studying-art-and-design-in-germany-as-a-foreigner-en).

## Which programs? (public vs private map)

The table below shows typical English-taught (or mostly English) program types and institution types. **Program names and language requirements change every semester — always check the school's current page.**

| Institution type | Typical program | Field | Note |
|---|---|---|---|
| **Public FH/HAW** (e.g. TH Köln/KISD, FH Potsdam, HTW Berlin) | Integrated / Interface / Media Design MA | Communication/media/UX | Fee-free, competitive |
| **Public Kunsthochschule** (e.g. Bauhaus Weimar, HfG Offenbach) | Media Art & Design, MediaArchitecture, HCI | Media art / interaction | Portfolio very decisive |
| **Technical university** | HCI / Human Factors (M.Sc.) | UX research | Design+CS hybrid |
| **Private design school** (e.g. UE, Berlin Int'l, Macromedia-type) | UX/UI, Motion, Game, Communication | Digital design | Lots of English, but **expensive** |

*As of 2025/2026, approximate; program lists change often, verify.*

As you can see, the balance tilts toward **digital/media/UX**. Pure "English graphic design master's" are relatively rare at public schools; many students therefore pivot to UX/media, or look at private schools with a larger English offering.

## Requirements: portfolio (Mappe) + bachelor's + English

There are three main gates into an English-taught design/media master's:

1. **A relevant bachelor's:** Usually a completed degree in design, media, art, architecture or a nearby field. Some UX/HCI programs also open to computer science/psychology backgrounds; but design master's mostly expect a design foundation.
2. **Portfolio (Mappe):** This is practically non-negotiable. Design and media programs require a **portfolio** and/or an **Eignungsprüfung** (aptitude assessment). Even with good grades you're out if the portfolio is weak — in Germany it isn't the NC that decides, it's the **portfolio.** Our sibling article shows step by step how to build it: [How to prepare a portfolio (Mappe)](/en/blog/how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools-en).
3. **English proof:** Usually around **IELTS ~6.5 / TOEFL ~90** (depends on the program; verify). Don't assume an English program waives the English certificate.

In short: an English master's frees you from the **German language barrier**, but not from the **portfolio barrier**. Start early.

## Fees: public largely free, private schools expensive

The money side is good news for most:

- **Public universities and art/design schools** usually charge no tuition; the per-semester **Semesterbeitrag (~€150-350)** covers administration and often a public-transport ticket (Semesterticket).
- **Baden-Württemberg (BW) exception:** in this state **non-EU students** usually pay tuition of **~€1,500/semester** — budget accordingly.
- **Private design schools are much pricier:** often several thousand to tens of thousands of euros per year. That's often exactly where the English supply is largest; before paying, scrutinize graduate employment and portfolio output.

*As of 2025/2026, approximate; state and school policies change, verify.* For post-master's visa options (18-month job search), see [Germany: Master's or Job-Seeker Visa?](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

## The no-German reality: studio, agency and freelance want German

Now the honest part that's in no brochure. Even if teaching is in English, life without German has walls:

- **Studio and daily life:** even if the program is officially English, the environment, guest juries and life outside class often drift into German. Those who don't speak German can get isolated fast.
- **Agency and in-house jobs:** most design agencies and internal teams in Germany work in **German** — client presentations, briefs and team communication run in German. After the master's, German is expected in most roles. Exception: some big-city UX/digital-product teams run in English (especially international tech firms).
- **Freelance and self-employment:** as a freelance designer, winning clients, contracts, invoicing and local networking are largely in German. Breaking into the freelance market without German is very hard.

So the plan should be: **start** with the English master's, but **begin learning German from day one.** Reaching B1-B2 changes the game in the studio and on the job market. In UX/digital your chance of an English-language job is higher than in other design tracks — read this alongside [Working in IT/tech in Germany (Blue Card & salary)](/en/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-en).

## Application & DAAD

Practical steps:

- **Application channel:** some schools take direct applications, others go through **uni-assist**. Each program has its own deadlines and portfolio format (PDF size, page count, submission method) — read early.
- **Timeline:** a good portfolio takes weeks; for the language proof (IELTS/TOEFL) you need an exam date. Begin **at least several months before** the application window.
- **DAAD & scholarships:** for the master's there may be **DAAD** scholarships and program-specific funding; the student visa also allows work (~20 hrs/week) to cover living costs. Check the current DAAD scholarship database.
- **Recognition:** confirm early whether your bachelor's is recognized in Germany (anabin/uni-assist pre-check).

For comparison, it helps to look at the English master's landscape in a neighboring field: [English-taught architecture master's in Germany (without German)](/en/blog/english-taught-architecture-masters-in-germany-without-german-en).

## Conclusion & honest advice

An English-taught design/media/UX master's in Germany is a **real path** — but not a magic escape. Honest summary:

- **The plan to do the bachelor's in English usually doesn't work;** the English offering sits mainly at the master's level, and **digital/media/UX** is the richest. Pure fine art in English is very scarce.
- A **portfolio (Mappe)** and an English proof await you — prepare early; the portfolio decides.
- Fees are low at most public schools; don't forget **BW: non-EU ~€1,500/semester** and the **expensive private schools.**
- Be ready for the **no-German reality:** even if teaching is English, agency, in-house and freelance run largely in German. Learn German from day one; in UX/digital your English job chances are highest.

For the full picture, see our cluster siblings: [Studying art & design in Germany](/en/blog/studying-art-and-design-in-germany-as-a-foreigner-en), [How to prepare a portfolio (Mappe)](/en/blog/how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools-en) and [Working as a designer in Germany (careers & salary)](/en/blog/working-as-a-designer-in-germany-careers-salary-and-reality-en).

*The figures, program names, fees and requirements in this article are approximate for 2025/2026 and change often. Before applying, verify against the school's and DAAD's current official sources.*
MD;

        $variants = [
            'tr' => ['slug'=>'english-taught-design-and-media-masters-in-germany-without-german',    'title'=>'Almancasız Almanya\'da Tasarım: İngilizce Tasarım & Medya Master Programları (2026)', 'excerpt'=>'Almanya\'da İngilizce tasarım, medya ve UX master programları var ama sınırlı. Kamu vs özel program haritası, portfolyo (Mappe) + İngilizce şartı, ücretler ve "Almancasız gerçeği" — dürüst rehber.', 'meta_title'=>'İngilizce Tasarım & Medya Master (Almanya, Almancasız) 2026', 'meta_description'=>'Almanya\'da İngilizce tasarım/medya/UX master: programlar, portfolyo + İngilizce şartı, ücretler ve Almancasız gerçeği. 2026 dürüst rehber.', 'body'=>$trBody],
            'de' => ['slug'=>'english-taught-design-and-media-masters-in-germany-without-german-de', 'title'=>'Design ohne Deutsch: Englischsprachige Design- & Medien-Master in Deutschland (2026)', 'excerpt'=>'In Deutschland gibt es englischsprachige Master in Design, Medien und UX — aber begrenzt. Landkarte öffentlich vs. privat, Mappe + Englisch, Kosten und die Ohne-Deutsch-Realität. Ein ehrlicher Leitfaden.', 'meta_title'=>'Englische Design- & Medien-Master in Deutschland (ohne Deutsch) 2026', 'meta_description'=>'Englischsprachige Design-/Medien-/UX-Master in Deutschland: Programme, Mappe + Englisch, Kosten und die Ohne-Deutsch-Realität. Ehrlicher Leitfaden 2026.', 'body'=>$deBody],
            'en' => ['slug'=>'english-taught-design-and-media-masters-in-germany-without-german-en', 'title'=>'Design Without German: English-Taught Design & Media Master\'s in Germany (2026)', 'excerpt'=>'Germany has English-taught master\'s in design, media and UX — but they are limited. Public vs private map, portfolio (Mappe) + English requirement, fees and the no-German reality. An honest guide.', 'meta_title'=>'English-Taught Design & Media Master\'s in Germany (no German) 2026', 'meta_description'=>'English-taught design/media/UX master\'s in Germany: programs, portfolio + English requirement, fees and the no-German reality. Honest 2026 guide.', 'body'=>$enBody],
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
            'english-taught-design-and-media-masters-in-germany-without-german',
            'english-taught-design-and-media-masters-in-germany-without-german-de',
            'english-taught-design-and-media-masters-in-germany-without-german-en',
        ])->delete();
    }
};

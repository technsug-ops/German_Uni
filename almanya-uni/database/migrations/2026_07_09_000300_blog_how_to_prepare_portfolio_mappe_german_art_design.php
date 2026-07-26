<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Portfolyo (Mappe) nasıl hazırlanır — Alman sanat & tasarım okulları (2026).
 * Doğrulandı: Kunsthochschule/HfG başvurularında NC yok; kabulü Mappe + Eignungsprüfung/
 * Aufnahmeprüfung belirler. Mappe genelde 15-30 iş + süreç/eskiz; Mappenkurs hazırlık yaygın.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'd4e30000-3333-4b0f-9f10-dd0bee11bb03';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da sanat ve tasarım kabulünde en çok yanlış anlaşılan gerçek şudur: **not ortalaması (NC) neredeyse hiç önemli değildir; her şeyi portfolyon — Almanca adıyla *Mappe* — belirler.** Tıp veya mühendislikte bir onda birlik puan farkı kabulü değiştirir; Kunsthochschule veya tasarım FH'sinde ise 20-30 sayfalık bir dosya senin sanatsal düşünceni, elinin gücünü ve potansiyelini gösterir. Bu yazı, güçlü bir Mappe'nin ne içerdiğini, Eignungsprüfung'ün nasıl işlediğini ve aylar öncesinden nasıl hazırlanacağını somut olarak anlatır.

## Portfolyo (Mappe) neden HER ŞEY?
Alman sanat akademilerinde (Kunstakademie Düsseldorf, UdK Berlin, HfBK Hamburg, Städelschule) ve güçlü tasarım okullarında (HfG Offenbach, Burg Giebichenstein Halle, Folkwang Essen) **kabul kararının %70-90'ı Mappe'ye dayanır.** Lise notların, hatta çoğu zaman lisans notların bile ikincil kalır. Jüri şuna bakar: özgün görüyor musun, bir fikri süreçle geliştirebiliyor musun, teknik olarak ne kadar yol katetmişsin ve **öğretilmeye açık bir potansiyelin var mı?**

Bunun sonucu net: **portfolyona kışın-ilkbaharın aylarını ayırman, kabulün tek gerçek yatırımıdır.** Tepe akademilerde kontenjan çok az ve rekabet serttir — bazı bölümlerde yüzlerce başvurudan onlarca kişi alınır. İyi haber: NC olmadığı için "notum tutmuyor" diye yolun kapanmaz; kötü haber: kimse senin yerine güçlü bir dosya yapamaz.

## Mappe ne içermeli: özgün iş, süreç, çeşitlilik
Her okulun kendi kuralları var ama ortak çekirdek benzerdir. Web sitelerinde **"Mappenrichtlinien" (portfolyo yönergeleri)** her zaman yayınlanır — başvurmadan önce mutlaka oku, çünkü sayı, format ve teslim şekli okuldan okula değişir.

| Öğe | Ne beklenir | Neden önemli |
|---|---|---|
| **İş sayısı** | Genelde **15-30 çalışma** (okula göre değişir) | Az = zayıf; çok + dolgu = odaksız |
| **Özgün iş** | Kopya/hayran çizimi değil, **kendi fikirlerin** | Jüri özgün görüşü arar |
| **Süreç & eskiz** | Eskiz defteri, taslaklar, deneme aşamaları | "Nasıl düşündüğünü" gösterir |
| **Çeşitlilik** | Farklı teknik/malzeme/konu | Esneklik ve merak kanıtı |
| **Gözlemsel çalışma** | Naturdan çizim (figür, obje, mekân) | Temel el becerisini kanıtlar |
| **Format** | PDF veya fiziksel; genelde **A3/A2** | Yönergeye birebir uy |

**En sık atlanan iki şey: süreç ve gözlemsel çizim.** Jüri bitmiş, cilalı sonuçlardan çok, **bir fikri nasıl geliştirdiğini** görmek ister. Eskiz defterin, denemelerin ve "başarısız" varyasyonların çoğu zaman en cazip parçalardır. Naturdan (gerçek modelden/objeden) gözlemsel çizim ise hâlâ birçok akademinin belkemiğidir.

## Alan-özel ipuçları
- **Grafik / iletişim tasarımı:** Fikir ve kavramsal güç, cilalı yazılım işinden önemli. Tipografi denemeleri, afiş/poster serileri, bir problemi görselle çözen projeler koy. Salt Instagram estetiği değil, **konsept** göster.
- **Endüstriyel / ürün tasarımı:** Üç boyutlu düşünme kanıtı ver — maketler, çizimler, işlev-form eskizleri. "Neden böyle çözdüm" anlatısı çok değerli.
- **Moda tasarımı:** İllüstrasyon + malzeme/kumaş denemeleri + varsa dikilmiş parçalar. Kişisel bir estetik dil ve **koleksiyon fikri** ara.
- **Freie Kunst (güzel sanatlar):** En serbest kategori. Kendi sanatsal pozisyonun, tekrar eden temaların ve deneysel cesaretin önemli. Teknik çeşitlilikten çok **tutarlı bir sanatçı sesi** aranır.
- **Mediendesign / UX-UI / dijital tasarım:** Süreç odaklı case study'ler (problem → araştırma → çözüm), ekran akışları ve varsa küçük prototipler. Burada dijital ürün mantığı sana ciddi avantaj sağlar.

## Eignungsprüfung / Aufnahmeprüfung nasıl işler?
Kabul genelde **iki aşamalıdır.** Önce Mappe teslim edilir ve ön elemeden geçer. Mappe'n beğenilirse **yetenek sınavına (Eignungsprüfung / Aufnahmeprüfung)** davet edilirsin. Bu aşama okula göre şunları içerebilir:
- **Yerinde uygulama sınavı:** Birkaç saat/gün süren, verilen konuda çizim/tasarım yapma.
- **Sözlü görüşme / mülakat:** İşlerini savunman, neden bu okulu/alanı seçtiğini anlatman istenir.
- **Ev ödevi (Hausaufgabe):** Bazı okullar önceden bir tema verir; onu Mappe'ye eklersin.

**Almanca burada devreye girer:** Mappe görsel olsa da mülakat ve çoğu programın dili Almancadır (**genelde C1**). Bazı İngilizce master seçenekleri istisnadır. Sınav günü kendi işlerini akıcı anlatabilmek büyük fark yaratır — jüri "bu kişi ne düşünüyor?" sorusunun cevabını orada arar.

## Mappenkurs ve hazırlık seçenekleri
Almanya'da yaygın ve son derece işe yarayan bir yol **Mappenkurs**'tur — portfolyo hazırlık kursu. Bunlar özel atölyeler, halk eğitim merkezleri (VHS), bazı akademilerin hazırlık programları veya "Vorstudium/Grundstudium" niteliğindeki yıllar olabilir. Seçenekler:
- **Mappenkurs (birkaç hafta-ay):** Bir eğitmen dosyanı kritik eder, eksiklerini kapatır. **Yurt dışından başvuranlar için bile online kritik hizmetleri var.**
- **Kolleg / hazırlık yılı:** Rekabetçi akademilere girmeden önce bir yıl temel atma.
- **Bireysel mentorluk / portföy kritiği:** Bir hoca veya mezunla dosya üzerine birebir çalışma.
- **Kendi kendine:** Yönergeleri oku, çok çiz, düzenli kritik al. Disiplinliysen mümkün ama en zoru budur.

**Dürüst not:** Tepe akademilere ilk denemede girememek çok yaygındır; birçok başarılı öğrenci **2-3 kez** başvurur. Bu bir başarısızlık değil, sürecin normal parçasıdır.

## Yaygın hatalar + zaman planı
**En sık hatalar:**
- Yönergeleri okumamak (yanlış sayı/format → doğrudan eleme).
- Sadece bitmiş, cilalı iş koyup **süreci gizlemek.**
- Kopya/hayran çizimi veya tutorial taklidi koymak — özgünlük yok sayılır.
- Dolgu iş ile sayıyı şişirmek; **kalite > nicelik.**
- Son haftaya sıkıştırmak — sanat dosyası aceleye gelmez.

**Kabaca zaman planı (başvuru genelde kış/ilkbahar):**

| Zaman | Yapılacak |
|---|---|
| **6-9 ay önce** | Okul seç, Mappenrichtlinien'i oku, çizmeye başla |
| **4-6 ay önce** | Projeleri geliştir, çeşitlilik + süreç biriktir |
| **2-3 ay önce** | Mappenkurs/kritik al, en zayıf işleri çıkar |
| **1 ay önce** | Dosyayı düzenle, format/teslim kontrolü |
| **Sınav sonrası** | Eignungsprüfung'e Almanca sunum hazırlığı |

## Sonuç & dürüst tavsiye
Alman sanat ve tasarım okullarında **kaderini notların değil, elin ve fikirlerin belirler.** Bu özgürleştirici ama aynı zamanda talepkâr: kimse senin yerine güçlü bir Mappe yapamaz. Yönergeleri kelimesi kelimesine oku, **süreç ve özgünlük** göster, en az bir kez dış kritik al ve reddedilirsen yılma — tekrar başvurmak normaldir. Alanını da dürüstçe seç: **UX/dijital ve ürün tasarımı istihdam ve maaş açısından güçlü**, güzel sanatlar ise tutku yoludur.

Devamı için: [Almanya'da sanat & tasarım okumak](/tr/blog/studying-art-and-design-in-germany-as-a-foreigner), [Almancasız İngilizce tasarım & medya master](/tr/blog/english-taught-design-and-media-masters-in-germany-without-german) ve [Almanya'da tasarımcı olarak çalışmak: maaş ve gerçek](/tr/blog/working-as-a-designer-in-germany-careers-salary-and-reality). Okul seçerken [Alman üniversitelerinde prestij ve sıralamalar nasıl işler](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) ve komşu bir disiplin olarak [Almanya'da mimarlık okumak](/tr/blog/studying-architecture-in-germany-as-a-foreigner) yazılarına da bakabilirsin.

*Bu içerik 2026 itibarıyla, yaklaşık ve genel bilgi amaçlıdır; portfolyo yönergeleri, sınav biçimleri ve tarihleri okuldan okula değişir ve güncellenir. Başvurmadan önce ilgili akademinin resmî Mappenrichtlinien sayfasını doğrulayın.*
MD;

        $deBody = <<<'MD'
Bei der Bewerbung an deutschen Kunst- und Designhochschulen wird eine Sache oft missverstanden: **Deine Abiturnote (NC) spielt fast keine Rolle — es zählt fast alles deine Mappe.** In Medizin oder Ingenieurwesen entscheidet ein Zehntelpunkt; an einer Kunstakademie oder Design-FH zeigt eine Mappe aus 20-30 Blättern dein künstlerisches Denken, deine handwerkliche Kraft und dein Potenzial. Dieser Beitrag erklärt konkret, was eine starke Mappe enthält, wie die Eignungsprüfung abläuft und wie du dich Monate im Voraus vorbereitest.

## Warum die Mappe ALLES ist
An deutschen Kunstakademien (Kunstakademie Düsseldorf, UdK Berlin, HfBK Hamburg, Städelschule) und starken Designhochschulen (HfG Offenbach, Burg Giebichenstein Halle, Folkwang Essen) beruht **70-90 % der Zulassungsentscheidung auf der Mappe.** Deine Schulnoten und oft sogar deine Bachelor-Noten sind zweitrangig. Die Jury fragt: Siehst du eigenständig? Kannst du eine Idee im Prozess entwickeln? Wie weit bist du handwerklich? Und: **Hast du ein Potenzial, das man weiterentwickeln kann?**

Die Folge ist klar: **Monate für deine Mappe zu investieren ist die einzige echte Investition in deine Zulassung.** An Spitzenakademien sind die Plätze rar und der Wettbewerb hart — in manchen Klassen werden aus Hunderten Bewerbungen nur wenige genommen. Die gute Nachricht: Ohne NC ist dir kein Weg wegen einer Note versperrt. Die schlechte: Niemand baut die starke Mappe für dich.

## Was in die Mappe gehört: eigene Arbeit, Prozess, Vielfalt
Jede Hochschule hat eigene Regeln, aber der Kern ähnelt sich. Die **Mappenrichtlinien** stehen immer auf der Website — lies sie unbedingt vor der Bewerbung, denn Anzahl, Format und Abgabe unterscheiden sich stark.

| Element | Was erwartet wird | Warum wichtig |
|---|---|---|
| **Anzahl der Arbeiten** | Meist **15-30 Arbeiten** (je nach Hochschule) | Zu wenig = schwach; zu viel + Füllmaterial = fokuslos |
| **Eigene Arbeit** | Keine Kopien/Fan-Art, sondern **eigene Ideen** | Die Jury sucht eigenständigen Blick |
| **Prozess & Skizzen** | Skizzenbuch, Entwürfe, Zwischenschritte | Zeigt, *wie* du denkst |
| **Vielfalt** | Verschiedene Techniken/Materialien/Themen | Beweis für Flexibilität und Neugier |
| **Beobachtungszeichnung** | Zeichnen nach der Natur (Figur, Objekt, Raum) | Belegt handwerkliche Grundlagen |
| **Format** | PDF oder physisch; oft **A3/A2** | Halte dich exakt an die Richtlinie |

**Die zwei am häufigsten vergessenen Dinge: Prozess und Beobachtungszeichnung.** Die Jury will weniger fertige, glatte Ergebnisse sehen, sondern **wie du eine Idee entwickelst.** Dein Skizzenbuch, deine Versuche und sogar „gescheiterte" Varianten sind oft die interessantesten Teile. Zeichnen nach der Natur bleibt an vielen Akademien das Rückgrat.

## Fachspezifische Tipps
- **Grafik / Kommunikationsdesign:** Idee und Konzept sind wichtiger als glatte Software-Arbeit. Zeig Typografie-Experimente, Plakatserien und Projekte, die ein Problem visuell lösen — **Konzept statt reiner Instagram-Ästhetik.**
- **Industrie- / Produktdesign:** Beweise dreidimensionales Denken — Modelle, Zeichnungen, Funktion-Form-Skizzen. Die Erzählung „warum ich es so gelöst habe" ist sehr wertvoll.
- **Modedesign:** Illustration + Material-/Stoffversuche + genähte Stücke, falls vorhanden. Gesucht sind eine persönliche Ästhetik und eine **Kollektionsidee.**
- **Freie Kunst:** Die freieste Kategorie. Deine künstlerische Position, wiederkehrende Themen und experimenteller Mut zählen. Weniger technische Vielfalt, mehr eine **konsistente künstlerische Stimme.**
- **Mediendesign / UX-UI / Digital Design:** Prozessorientierte Case Studies (Problem → Recherche → Lösung), Screen-Flows und kleine Prototypen. Hier verschafft dir Produktdenken einen echten Vorteil.

## Wie die Eignungsprüfung / Aufnahmeprüfung abläuft
Die Zulassung ist meist **zweistufig.** Zuerst reichst du die Mappe ein, die eine Vorauswahl durchläuft. Überzeugt sie, wirst du zur **Eignungsprüfung / Aufnahmeprüfung** eingeladen. Je nach Hochschule umfasst das:
- **Praktische Prüfung vor Ort:** Zeichnen/Gestalten zu einem gestellten Thema über Stunden oder Tage.
- **Gespräch / Interview:** Du verteidigst deine Arbeiten und erklärst deine Wahl der Hochschule und Fachrichtung.
- **Hausaufgabe:** Manche Hochschulen geben vorab ein Thema, das du in die Mappe integrierst.

**Hier kommt Deutsch ins Spiel:** Die Mappe ist visuell, aber Gespräch und Sprache der meisten Programme sind Deutsch (**meist C1**). Einige englischsprachige Master sind Ausnahmen. Am Prüfungstag deine Arbeiten flüssig zu erklären macht einen großen Unterschied — die Jury sucht dort die Antwort auf „Was denkt diese Person?".

## Mappenkurs und Vorbereitungsoptionen
Ein verbreiteter und sehr wirksamer Weg in Deutschland ist der **Mappenkurs.** Das können private Ateliers, Volkshochschulen (VHS), Vorbereitungsprogramme mancher Akademien oder ein „Vorstudium" sein. Deine Optionen:
- **Mappenkurs (Wochen-Monate):** Ein Dozent kritisiert deine Mappe und schließt Lücken. **Für Bewerber aus dem Ausland gibt es sogar Online-Kritik.**
- **Kolleg / Vorbereitungsjahr:** Ein Jahr Grundlagen vor der Bewerbung an kompetitiven Akademien.
- **Individuelles Mentoring / Mappenkritik:** Einzelarbeit mit einer Lehrperson oder Absolventin an der Mappe.
- **Selbststudium:** Richtlinien lesen, viel zeichnen, regelmäßig Kritik holen. Mit Disziplin möglich, aber am schwersten.

**Ehrlicher Hinweis:** Es ist sehr häufig, dass man bei Spitzenakademien nicht beim ersten Versuch angenommen wird; viele erfolgreiche Studierende bewerben sich **2-3 Mal.** Das ist kein Scheitern, sondern ein normaler Teil des Prozesses.

## Häufige Fehler + Zeitplan
**Die häufigsten Fehler:**
- Die Richtlinien nicht lesen (falsche Anzahl/Format → direkte Aussortierung).
- Nur fertige, glatte Arbeiten zeigen und den **Prozess verstecken.**
- Kopien, Fan-Art oder Tutorial-Imitationen einreichen — Eigenständigkeit wird ignoriert.
- Die Anzahl mit Füllmaterial aufblähen; **Qualität > Quantität.**
- Alles in die letzte Woche pressen — eine Mappe verträgt keine Hektik.

**Grober Zeitplan (Bewerbung meist Winter/Frühjahr):**

| Zeitpunkt | Zu tun |
|---|---|
| **6-9 Monate vorher** | Hochschule wählen, Mappenrichtlinien lesen, mit dem Zeichnen beginnen |
| **4-6 Monate vorher** | Projekte entwickeln, Vielfalt + Prozess sammeln |
| **2-3 Monate vorher** | Mappenkurs/Kritik holen, schwächste Arbeiten aussortieren |
| **1 Monat vorher** | Mappe ordnen, Format/Abgabe prüfen |
| **Nach der Auswahl** | Eignungsprüfung: Präsentation auf Deutsch vorbereiten |

## Fazit & ehrlicher Rat
An deutschen Kunst- und Designhochschulen entscheidet **nicht deine Note, sondern deine Hand und deine Ideen** über deinen Weg. Das ist befreiend, aber auch fordernd: Niemand baut die starke Mappe für dich. Lies die Richtlinien wortwörtlich, zeige **Prozess und Eigenständigkeit,** hol dir mindestens einmal externe Kritik und gib bei einer Absage nicht auf — sich erneut zu bewerben ist normal. Wähle auch dein Fach ehrlich: **UX/Digital und Produktdesign sind stark bei Anstellung und Gehalt,** die Freie Kunst ist der Weg der Leidenschaft.

Zum Weiterlesen: [Kunst & Design in Deutschland studieren](/de/blog/studying-art-and-design-in-germany-as-a-foreigner-de), [englischsprachige Design- & Media-Master ohne Deutsch](/de/blog/english-taught-design-and-media-masters-in-germany-without-german-de) und [als Designer:in in Deutschland arbeiten: Gehalt und Realität](/de/blog/working-as-a-designer-in-germany-careers-salary-and-reality-de). Bei der Hochschulwahl helfen [wie Prestige und Rankings in Deutschland funktionieren](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de) und als benachbarte Disziplin [Architektur in Deutschland studieren](/de/blog/studying-architecture-in-germany-as-a-foreigner-de).

*Dieser Beitrag dient der allgemeinen Orientierung, Stand 2026 und ungefähr; Mappenrichtlinien, Prüfungsformate und Termine unterscheiden sich je Hochschule und ändern sich. Prüfe vor der Bewerbung die offizielle Mappenrichtlinien-Seite der jeweiligen Akademie.*
MD;

        $enBody = <<<'MD'
When applying to German art and design schools, one thing is widely misunderstood: **your grades (the NC) barely matter — almost everything comes down to your portfolio, known in German as the *Mappe*.** In medicine or engineering a tenth of a grade point decides admission; at a Kunsthochschule or design university, a folder of 20-30 sheets reveals your artistic thinking, your hand skills and your potential. This article explains concretely what a strong Mappe contains, how the Eignungsprüfung works, and how to prepare months in advance.

## Why the Mappe is EVERYTHING
At German art academies (Kunstakademie Düsseldorf, UdK Berlin, HfBK Hamburg, Städelschule) and strong design schools (HfG Offenbach, Burg Giebichenstein Halle, Folkwang Essen), **70-90% of the admission decision rests on the Mappe.** Your school grades — and often even your bachelor's grades — are secondary. The jury asks: do you see originally? Can you develop an idea through a process? How far are your technical skills? And, crucially, **do you have a potential that can be developed further?**

The implication is clear: **spending months on your Mappe is the only real investment in your admission.** At top academies, places are scarce and competition is fierce — some classes admit only a handful from hundreds of applicants. The good news: without an NC, no path is closed to you over a grade. The bad news: nobody builds the strong Mappe for you.

## What the Mappe should contain: original work, process, variety
Every school has its own rules, but the core is similar. The **Mappenrichtlinien (portfolio guidelines)** are always on the website — read them before applying, because the number, format and submission method vary widely.

| Element | What's expected | Why it matters |
|---|---|---|
| **Number of works** | Usually **15-30 pieces** (varies by school) | Too few = weak; too many + filler = unfocused |
| **Original work** | No copies/fan art — **your own ideas** | The jury looks for an original eye |
| **Process & sketches** | Sketchbook, drafts, intermediate steps | Shows *how* you think |
| **Variety** | Different techniques/materials/themes | Proof of flexibility and curiosity |
| **Observational drawing** | Drawing from life (figure, object, space) | Proves core hand skills |
| **Format** | PDF or physical; often **A3/A2** | Follow the guideline exactly |

**The two most commonly forgotten things: process and observational drawing.** The jury wants to see less of the finished, polished result and more of **how you develop an idea.** Your sketchbook, experiments and even "failed" variations are often the most compelling parts. Drawing from life remains the backbone at many academies.

## Field-specific tips
- **Graphic / communication design:** Idea and concept matter more than polished software work. Show typography experiments, poster series and projects that solve a problem visually — **concept over pure Instagram aesthetics.**
- **Industrial / product design:** Prove three-dimensional thinking — models, drawings, function-form sketches. The narrative of "why I solved it this way" is very valuable.
- **Fashion design:** Illustration + material/fabric experiments + sewn pieces if you have them. Look for a personal aesthetic and a **collection idea.**
- **Freie Kunst (fine arts):** The freest category. Your artistic position, recurring themes and experimental courage count. Less technical variety, more a **consistent artistic voice.**
- **Media design / UX-UI / digital design:** Process-driven case studies (problem → research → solution), screen flows and small prototypes. Here, product thinking gives you a real advantage.

## How the Eignungsprüfung / Aufnahmeprüfung works
Admission is usually **two-stage.** First you submit the Mappe, which goes through a pre-selection. If it convinces the jury, you are invited to the **aptitude test (Eignungsprüfung / Aufnahmeprüfung).** Depending on the school, this can include:
- **On-site practical exam:** Drawing/designing on a set theme over hours or days.
- **Interview:** You defend your works and explain why you chose this school and field.
- **Home assignment (Hausaufgabe):** Some schools set a theme in advance to include in the Mappe.

**This is where German comes in:** the Mappe is visual, but the interview and the language of most programs are German (**usually C1**). Some English-taught master's programs are exceptions. On exam day, being able to explain your own work fluently makes a big difference — the jury is looking there for the answer to "what does this person think?".

## Mappenkurs and preparation options
A common and highly effective route in Germany is the **Mappenkurs** — a portfolio preparation course. These can be private studios, adult education centres (VHS), preparatory programs at some academies, or a "Vorstudium" foundation year. Your options:
- **Mappenkurs (weeks to months):** A tutor critiques your folder and closes the gaps. **There are even online critique services for applicants from abroad.**
- **Kolleg / foundation year:** A year of fundamentals before applying to competitive academies.
- **Individual mentoring / portfolio critique:** One-on-one work on the folder with a teacher or graduate.
- **Self-study:** Read the guidelines, draw a lot, get regular critique. Possible if disciplined, but the hardest path.

**Honest note:** it is very common not to get into top academies on the first try; many successful students apply **2-3 times.** That is not failure — it is a normal part of the process.

## Common mistakes + a timeline
**The most frequent mistakes:**
- Not reading the guidelines (wrong number/format → immediate rejection).
- Showing only finished, polished work and **hiding the process.**
- Submitting copies, fan art or tutorial imitations — originality is ignored.
- Padding the count with filler; **quality > quantity.**
- Cramming everything into the last week — an art portfolio can't be rushed.

**Rough timeline (applications are usually winter/spring):**

| Timing | To do |
|---|---|
| **6-9 months before** | Choose schools, read the Mappenrichtlinien, start drawing |
| **4-6 months before** | Develop projects, build variety + process |
| **2-3 months before** | Get a Mappenkurs/critique, cut the weakest works |
| **1 month before** | Order the folder, check format/submission |
| **After selection** | Prepare a German presentation for the Eignungsprüfung |

## Conclusion & honest advice
At German art and design schools, **your path is decided not by your grades but by your hand and your ideas.** That is liberating but also demanding: nobody builds the strong Mappe for you. Read the guidelines word for word, show **process and originality,** get external critique at least once, and if rejected, don't give up — reapplying is normal. Choose your field honestly too: **UX/digital and product design are strong for employment and salary,** while fine arts is the path of passion.

Read next: [studying art & design in Germany](/en/blog/studying-art-and-design-in-germany-as-a-foreigner-en), [English-taught design & media master's without German](/en/blog/english-taught-design-and-media-masters-in-germany-without-german-en) and [working as a designer in Germany: salary and reality](/en/blog/working-as-a-designer-in-germany-careers-salary-and-reality-en). When choosing a school, see [how prestige and rankings work in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en) and, as a neighbouring discipline, [studying architecture in Germany](/en/blog/studying-architecture-in-germany-as-a-foreigner-en).

*This content is general guidance, as of 2026 and approximate; portfolio guidelines, exam formats and dates vary by school and change over time. Verify the official Mappenrichtlinien page of the relevant academy before applying.*
MD;

        $variants = [
            'tr' => ['slug'=>'how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools',    'title'=>'Alman Sanat & Tasarım Okulları İçin Portfolyo (Mappe) Nasıl Hazırlanır? (2026)', 'excerpt'=>'Alman sanat & tasarım okullarında NC yok — kabulü portfolyon (Mappe) belirler. Mappe ne içermeli, alan-özel ipuçları, Eignungsprüfung, Mappenkurs, yaygın hatalar ve zaman planı.', 'meta_title'=>'Portfolyo (Mappe) Nasıl Hazırlanır — Alman Sanat & Tasarım (2026)', 'meta_description'=>'Alman sanat & tasarım okullarında portfolyo (Mappe) her şeydir. İçerik, alan-özel ipuçları, Eignungsprüfung, Mappenkurs, hatalar ve zaman planı (2026).', 'body'=>$trBody],
            'de' => ['slug'=>'how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools-de', 'title'=>'Mappe für deutsche Kunst- & Designhochschulen: So bereitest du sie vor (2026)', 'excerpt'=>'An deutschen Kunst- & Designhochschulen gibt es keinen NC — deine Mappe entscheidet. Inhalt, fachspezifische Tipps, Eignungsprüfung, Mappenkurs, häufige Fehler und Zeitplan.', 'meta_title'=>'Mappe vorbereiten — deutsche Kunst- & Designhochschulen (2026)', 'meta_description'=>'An deutschen Kunst- & Designhochschulen ist die Mappe alles. Inhalt, fachspezifische Tipps, Eignungsprüfung, Mappenkurs, Fehler und Zeitplan (2026).', 'body'=>$deBody],
            'en' => ['slug'=>'how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools-en', 'title'=>'How to Prepare a Portfolio (Mappe) for German Art & Design Schools (2026)', 'excerpt'=>'German art & design schools have no NC — your portfolio (Mappe) decides admission. What it should contain, field-specific tips, the Eignungsprüfung, Mappenkurs, common mistakes and a timeline.', 'meta_title'=>'How to Prepare a Portfolio (Mappe) — German Art & Design (2026)', 'meta_description'=>'At German art & design schools the portfolio (Mappe) is everything. Contents, field-specific tips, the Eignungsprüfung, Mappenkurs, mistakes and a timeline (2026).', 'body'=>$enBody],
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
            'how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools',
            'how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools-de',
            'how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools-en',
        ])->delete();
    }
};

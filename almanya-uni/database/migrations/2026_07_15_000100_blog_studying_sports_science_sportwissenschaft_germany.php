<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Spor Bilimi (Sportwissenschaft) okumak — study overview.
 * Doğrulandı: Sportwissenschaft disiplinlerarası (hareket/eğitim/tıp/yönetim); DSHS Köln = Almanya'nın TEK
 * spor üniversitesi, dünyanın en büyük & saygın spor bilimi kurumu (~6.000 öğrenci); diğer güçlü okullar
 * Freiburg/Tübingen/TUM/Potsdam/Leipzig/Stuttgart/Mainz/Bochum/Jena/Chemnitz; bachelor genelde NC'li VE
 * Sporteignungsprüfung (spor uygunluk sınavı) ister; bachelor çoğu Almanca C1, İngilizce master sınırlı;
 * kamu harçsız, dönemlik ~150-350€, Baden-Württemberg AB-dışı ~1.500€; Sperrkonto 2026 ~992€/ay =
 * ~11.904€/yıl; Blue Card ~50.700€ / darboğaz-yeni mezun ~45.934€. Dürüst gerçek: saf spor bilimi tek
 * başına rekabetçi/orta ücretli, getiri uzmanlaşmada. Hepsi hedge'li.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '5b5a0000-2222-4c7e-8a10-cc21bb30ee01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Spor sadece bir hobi değil; Almanya'da **koca bir bilim, sağlık ve endüstri alanı**. **Sportwissenschaft (spor bilimi)** okumak; hareket bilimi, spor tıbbı, rehabilitasyon, antrenman bilimi, spor psikolojisi ve spor yönetimini kapsayan disiplinlerarası bir yola girmek demek. Dürüst gerçek şu: alan heyecan verici ve Almanya bu işin dünya merkezlerinden birine — **Deutsche Sporthochschule Köln (DSHS Köln)** — ev sahipliği yapıyor; ama "saf" spor bilimi diploması tek başına rekabetçi ve orta ücretli olabiliyor. Asıl getiri **uzmanlaşmada**. Bu yazıda bir yabancı öğrenci olarak Almanya'da spor bilimi okumanın nasıl işlediğini, NC ve spor uygunluk sınavı dâhil, baştan sona anlatıyorum.

## Alan kapsamı: tek bir "spor bölümü" değil

Sportwissenschaft dar bir bölüm değil; birbirinden çok farklı yönelimleri olan geniş bir şemsiye. Kabaca şu alt dallar var:

- **Sports & Exercise Science (spor ve egzersiz bilimi):** performans, fizyoloji, biyomekanik — alanın "sert bilim" ucu.
- **Sports Medicine & Rehabilitation (spor tıbbı ve rehabilitasyon):** yaralanma, iyileşme, sağlık odaklı hareket; fizyoterapiyle komşu.
- **Training & Movement Science (antrenman ve hareket bilimi):** antrenman planlama, motor öğrenme, elit sporcu gelişimi.
- **Sports Management (spor yönetimi):** kulüp/tesis yönetimi, pazarlama, etkinlik, spor endüstrisi — istihdam açısından en güçlü uçlardan biri.
- **Sports Psychology (spor psikolojisi):** performans zihni, motivasyon, danışmanlık.
- **Lehramt Sport (beden eğitimi öğretmenliği):** okullarda öğretmenlik yolu (Staatsexamen gerektirir).
- **Performance / Data Analytics:** veri, izleme teknolojisi, performans analitiği — alanın en hızlı büyüyen ucu.

Kritik nokta: bu alan **hem fiziksel hem akademik**. Fizyoloji, anatomi, istatistik ve biyomekanik kadar pedagoji ve yönetim de var. Sağlık/rehabilitasyon tarafına yakın durmak istersen, komşu ve çok istihdam-dostu bir alan olarak fizyoterapiye bakmakta fayda var; ayrıntısını [Almanya'da yabancı olarak fizyoterapist olmak](/tr/blog/becoming-a-physiotherapist-in-germany-as-a-foreigner) yazısında ele aldım.

## Tanınan okullar: DSHS Köln başta

Almanya'nın en büyük özelliği: **spor bilimine adanmış ayrı bir üniversitesi var.**

| Okul | Öne çıkan |
|---|---|
| **Deutsche Sporthochschule Köln (DSHS Köln)** | Almanya'nın **TEK** spor üniversitesi; ~6.000 öğrenci; dünyanın en büyük ve en saygın spor bilimi kurumu. Rehabilitasyon, hareket bilimi, spor tıbbı, spor yönetimi, performans — her şey burada |
| **Uni Freiburg / Uni Tübingen** | Güçlü spor bilimi + spor tıbbı geleneği |
| **TU München (TUM)** | Spor & sağlık bilimleri, güçlü araştırma ve teknoloji bağı |
| **Uni Potsdam / Uni Leipzig** | Antrenman bilimi ve elit spor geleneği (Leipzig tarihsel olarak güçlü) |
| **Uni Stuttgart / Uni Mainz / Ruhr-Uni Bochum** | Geniş spor bilimi programları, öğretmenlik dâhil |
| **Uni Jena / TU Chemnitz** | Sağlam bölgesel seçenekler, spor bilimi + öğretmenlik |

İsim/marka peşinde koşmadan önce Almanya'da "prestij"in nasıl işlediğini anlamak önemli — DSHS Köln alanında dünya markası olsa da, çoğu kamu üniversitesi arasındaki fark abartıldığı kadar büyük değil. Bunu dürüstçe [Almanya'da üniversite prestiji ve sıralamalar nasıl işler](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) yazısında anlattım.

## Dürüst uyarı: NC + Sporteignungsprüfung

Burası çoğu yabancı öğrencinin bilmeden geldiği en kritik nokta. Spor bilimi bachelor'ı Almanya'da genellikle **iki filtreye** takılır:

1. **NC (Numerus Clausus):** popüler programlarda kontenjan sınırlı; not ortalaması (Abitur/denklik) belirleyici olabilir. Spor bilimi birçok okulda NC'lidir.
2. **Sporteignungsprüfung (spor uygunluk / yetenek sınavı):** DSHS Köln ve çoğu üniversite, kayıttan **önce** fiziksel bir uygunluk sınavı ister. Koşu, yüzme, jimnastik, takım sporları, atletizm gibi disiplinlerde belirli bir performans standardını geçmen beklenir. Bu sınav ciddidir; hazırlıksız gelen çok kişi elenir.

Dürüst tavsiye: hedeflediğin okulun **Sporteignungsprüfung tarihini ve gerekliliklerini erkenden** öğren ve aylarca fiziksel olarak hazırlan. Bazı okullarda üst yaş/geçerlilik kuralları da vardır. Bu sınav olmadan çoğu bachelor programına giremezsin — planını buna göre kur.

## Almanca vs İngilizce

- **Bachelor:** neredeyse tamamı **Almanca** yürür ve pratikte **Almanca C1** (TestDaF/DSH) beklenir. Spor bilimi bachelor'ı için Almancasız yol pratikte kapalıdır.
- **Master:** İngilizce seçenekler **var ama sınırlı** — özellikle Exercise Science, Sport Management, Human Movement gibi alanlarda. Almancan yoksa gerçekçi yol İngilizce master; hangi programların İngilizce olduğunu ve eşikleri [Almancasız Almanya'da İngilizce spor & egzersiz bilimi master programları](/tr/blog/english-taught-sports-science-and-exercise-science-masters-in-germany) yazısında topladım.

Not: İngilizce master'da bile, staj/Werkstudent ve iş bulma için Almanca hâlâ büyük fark yaratır. Almancayı "sonra öğrenirim" diye erteleme.

## Başvuru: uni-assist ve belgeler

- **uni-assist:** birçok kamu üniversitesi yabancı başvuruları **uni-assist** üzerinden toplar (diploma denkliği ve ön-değerlendirme). Bazı okullar doğrudan kendi portalından alır — okulun sayfasını mutlaka kontrol et.
- **Belgeler:** lise/lisans diploması ve transkript, dil kanıtı (Almanca C1 **veya** İngilizce programlar için IELTS/TOEFL), motivasyon mektubu, CV — ve spor bilimi için **Sporteignungsprüfung sonucu / sağlık raporu**.
- **Dönemler:** kış dönemi başvuruları çoğunlukla **15 Temmuz** civarında kapanır; uygunluk sınavı tarihleri genelde daha erkendir (ilkbahar/yaz). İki takvimi birden takip et.

## Ücret & yaşam & Sperrkonto & Blue Card

- **Harç:** kamu üniversitelerinde **öğrenim ücreti yok**; sadece dönemlik katkı ~**150–350€** (semester ticket dâhil olabilir). İstisna: **Baden-Württemberg**, AB dışı öğrencilerden ~**1.500€/dönem** alır. Özel okullar yılda **binlerce euro**. *2025/2026 itibarıyla, yaklaşık; doğrula.*
- **Sperrkonto (bloke hesap):** vize için genelde ~**992€/ay = ~11.904€/yıl** göstermen istenir. *2025/2026 itibarıyla, yaklaşık; resmî kaynaktan doğrula.*
- **Burs:** **DAAD** en bilinen kaynak; ayrıca Deutschlandstipendium ve vakıf bursları.
- **Mezuniyet sonrası & Blue Card:** iş bulunca Blue Card için 2026 genel maaş eşiği ~**50.700€/yıl**; darboğaz meslek/yeni mezun eşiği ~**45.934€/yıl**. *Yaklaşık; resmî kaynaktan doğrula.* Dürüst not: saf spor bilimi giriş rolleri bu eşiklerin altında kalabilir — bu yüzden uzmanlaşma (yönetim, veri, kurumsal sağlık) fark yaratır.

## Sonuç & dürüst tavsiye

Almanya'da spor bilimi okumak; tutkulu, disiplinlerarası ve kamu tarafında çok ekonomik bir yol — ve alan dünyanın en iyi kurumlarından birine (DSHS Köln) ev sahipliği yapıyor. Ama dürüst olmak gerekirse "spor bilimi okudum" tek başına iş piyasasında güçlü bir kart değil. Tavsiyem:

1. **Uygunluk sınavına ciddi hazırlan:** NC + Sporteignungsprüfung gerçek bir filtre; aylar öncesinden çalış.
2. **Baştan bir yönde uzmanlaş:** **spor yönetimi, performans/veri analitiği, kurumsal sağlık yönetimi (BGM) veya rehabilitasyon** — saf akademik spor biliminden çok daha istihdam-dostu.
3. **Fizyoterapi köprüsünü değerlendir:** sağlık/rehabilitasyon istiyorsan, fizyoterapi çok daha net bir istihdam yolu sunar; [Almanya'da uluslararası öğrenciler için fizyoterapi eğitimi ve okumak](/tr/blog/physiotherapy-training-and-study-in-germany-for-internationals) yazısı iyi bir başlangıç.
4. **Kariyeri baştan düşün:** hangi rolün seni istihdam edilebilir kıldığını [Almanya'da spor bilimiyle çalışmak: kariyer ve maaş](/tr/blog/working-in-sports-science-in-germany-careers-and-salary) ve diplomayla somut iş yollarını [spor bilimi diplomasıyla iş piyasası](/tr/blog/what-to-do-with-a-sports-science-degree-in-germany-job-market) yazılarında ele aldım.

Kararını "spora bayılıyorum"a değil, **hangi uzmanlaşmanın seni istihdam edilebilir ve iyi ücretli kılacağına** göre ver.

*Bu yazı 2026 başı itibarıyla hazırlanmıştır. Öğrenim ücretleri, NC ve Sporteignungsprüfung koşulları, başvuru tarihleri, Sperrkonto tutarı, Blue Card maaş eşikleri ve piyasa rakamları eyalete, okula ve yıla göre değişir. Başvurmadan önce ilgili okulun ve resmî kurumların güncel bilgilerini mutlaka doğrula.*
MD;

        $deBody = <<<'MD'
Sport ist nicht nur ein Hobby — in Deutschland ist es ein ganzes **Wissenschafts-, Gesundheits- und Industriefeld**. **Sportwissenschaft** zu studieren bedeutet, einen interdisziplinären Weg einzuschlagen, der Bewegungswissenschaft, Sportmedizin, Rehabilitation, Trainingswissenschaft, Sportpsychologie und Sportmanagement umfasst. Die ehrliche Wahrheit: Das Feld ist spannend, und Deutschland beherbergt eines der Weltzentren dafür — die **Deutsche Sporthochschule Köln (DSHS Köln)**. Aber ein „reiner" Sportwissenschaftsabschluss allein kann wettbewerbsintensiv und mittelmäßig bezahlt sein. Der eigentliche Ertrag liegt in der **Spezialisierung**. In diesem Artikel erkläre ich von Anfang bis Ende, wie ein Sportwissenschaftsstudium in Deutschland als internationale:r Studierende:r funktioniert — inklusive NC und Sporteignungsprüfung.

## Das Feld: nicht ein einzelnes „Sportfach"

Sportwissenschaft ist kein enges Fach, sondern ein breiter Schirm mit sehr unterschiedlichen Ausrichtungen. Grob gibt es diese Teilbereiche:

- **Sport- & Bewegungswissenschaft:** Leistung, Physiologie, Biomechanik — das „harte" Ende des Feldes.
- **Sportmedizin & Rehabilitation:** Verletzung, Heilung, gesundheitsorientierte Bewegung; benachbart zur Physiotherapie.
- **Trainings- & Bewegungswissenschaft:** Trainingsplanung, motorisches Lernen, Leistungssportentwicklung.
- **Sportmanagement:** Vereins-/Anlagenmanagement, Marketing, Events, Sportindustrie — eines der beschäftigungsstärksten Enden.
- **Sportpsychologie:** Leistungspsyche, Motivation, Beratung.
- **Lehramt Sport:** der Weg in den Schuldienst (Staatsexamen erforderlich).
- **Performance / Data Analytics:** Daten, Tracking-Technologie, Leistungsanalytik — das am schnellsten wachsende Ende.

Entscheidend: Dieses Feld ist **sowohl physisch als auch akademisch**. Physiologie, Anatomie, Statistik und Biomechanik gehören ebenso dazu wie Pädagogik und Management. Wenn du näher an der Gesundheits-/Reha-Seite bleiben willst, lohnt ein Blick auf die benachbarte, sehr beschäftigungsfreundliche Physiotherapie; die Details behandle ich in [Als Ausländer:in Physiotherapeut:in in Deutschland werden](/de/blog/becoming-a-physiotherapist-in-germany-as-a-foreigner-de).

## Anerkannte Hochschulen: DSHS Köln voran

Die Besonderheit Deutschlands: Es gibt eine **eigene, der Sportwissenschaft gewidmete Universität.**

| Hochschule | Besonderheit |
|---|---|
| **Deutsche Sporthochschule Köln (DSHS Köln)** | die **EINZIGE** Sportuniversität Deutschlands; ~6.000 Studierende; die größte und angesehenste sportwissenschaftliche Einrichtung der Welt. Rehabilitation, Bewegungswissenschaft, Sportmedizin, Sportmanagement, Performance — alles hier |
| **Uni Freiburg / Uni Tübingen** | starke Tradition in Sportwissenschaft + Sportmedizin |
| **TU München (TUM)** | Sport- & Gesundheitswissenschaften, starke Forschung und Technologiebezug |
| **Uni Potsdam / Uni Leipzig** | Trainingswissenschaft und Leistungssporttradition (Leipzig historisch stark) |
| **Uni Stuttgart / Uni Mainz / Ruhr-Uni Bochum** | breite Sportwissenschaftsprogramme, inkl. Lehramt |
| **Uni Jena / TU Chemnitz** | solide regionale Optionen, Sportwissenschaft + Lehramt |

Bevor du einem Namen/einer Marke hinterherläufst, ist es wichtig zu verstehen, wie „Prestige" in Deutschland funktioniert — die DSHS Köln ist zwar eine Weltmarke in ihrem Feld, aber der Unterschied zwischen den meisten staatlichen Unis ist nicht so groß wie oft angenommen. Das erkläre ich ehrlich in [Wie Uni-Prestige und Rankings in Deutschland funktionieren](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de).

## Ehrliche Warnung: NC + Sporteignungsprüfung

Das ist der kritischste Punkt, an den viele internationale Studierende ahnungslos herangehen. Der Sportwissenschafts-Bachelor stößt in Deutschland meist auf **zwei Filter**:

1. **NC (Numerus Clausus):** in beliebten Programmen ist die Platzzahl begrenzt; der Notenschnitt (Abitur/Anerkennung) kann entscheidend sein. Sportwissenschaft ist an vielen Hochschulen NC-beschränkt.
2. **Sporteignungsprüfung:** die DSHS Köln und die meisten Universitäten verlangen **vor** der Einschreibung eine körperliche Eignungsprüfung. In Disziplinen wie Laufen, Schwimmen, Turnen, Mannschaftssport und Leichtathletik musst du einen bestimmten Leistungsstandard erreichen. Diese Prüfung ist ernst; wer unvorbereitet kommt, fällt oft durch.

Ehrlicher Rat: Erkundige dich **früh** nach Termin und Anforderungen der Sporteignungsprüfung deiner Wunschhochschule und bereite dich monatelang körperlich vor. Ohne diese Prüfung kommst du in die meisten Bachelor-Programme nicht hinein — plane entsprechend.

## Deutsch vs. Englisch

- **Bachelor:** läuft fast vollständig auf **Deutsch** und erwartet praktisch **Deutsch C1** (TestDaF/DSH). Für den Sportwissenschafts-Bachelor ist der Weg ohne Deutsch praktisch geschlossen.
- **Master:** englischsprachige Optionen **gibt es, aber begrenzt** — besonders in Exercise Science, Sport Management, Human Movement. Ohne Deutsch ist der realistische Weg ein englischer Master; welche Programme englisch sind und welche Schwellen gelten, habe ich in [Englischsprachige Sport- & Bewegungswissenschaft-Master in Deutschland](/de/blog/english-taught-sports-science-and-exercise-science-masters-in-germany-de) gesammelt.

Hinweis: Selbst im englischen Master macht Deutsch für Praktikum/Werkstudentenjob und Jobsuche einen großen Unterschied. Verschiebe Deutsch nicht auf „später".

## Bewerbung: uni-assist und Unterlagen

- **uni-assist:** Viele staatliche Unis bündeln internationale Bewerbungen über **uni-assist** (Zeugnisbewertung und Vorprüfung). Manche nehmen direkt über ihr Portal an — prüfe unbedingt die Seite der Hochschule.
- **Unterlagen:** Schul-/Bachelorzeugnis und Transcript, Sprachnachweis (Deutsch C1 **oder** IELTS/TOEFL für englische Programme), Motivationsschreiben, CV — und für Sportwissenschaft das **Ergebnis der Sporteignungsprüfung / ein ärztliches Attest**.
- **Fristen:** Bewerbungen fürs Wintersemester schließen meist um den **15. Juli**; die Termine der Eignungsprüfung liegen meist früher (Frühjahr/Sommer). Verfolge beide Kalender.

## Kosten & Leben & Sperrkonto & Blue Card

- **Gebühren:** an staatlichen Unis gibt es **keine Studiengebühren**; nur ein Semesterbeitrag von ~**150–350€** (ggf. inkl. Semesterticket). Ausnahme: **Baden-Württemberg** verlangt von Nicht-EU-Studierenden ~**1.500€/Semester**. Private Hochschulen: mehrere **Tausend Euro** pro Jahr. *Stand 2025/2026, ungefähr; bitte prüfen.*
- **Sperrkonto:** fürs Visum musst du meist ~**992€/Monat = ~11.904€/Jahr** nachweisen. *Stand 2025/2026, ungefähr; aus offizieller Quelle prüfen.*
- **Stipendien:** **DAAD** ist die bekannteste Quelle; außerdem das Deutschlandstipendium und Stiftungsstipendien.
- **Nach dem Abschluss & Blue Card:** mit einem Job liegt die allgemeine Blue-Card-Gehaltsschwelle 2026 bei ~**50.700€/Jahr**; Engpassberufe/Berufseinsteiger:innen ~**45.934€/Jahr**. *Ungefähr; aus offizieller Quelle prüfen.* Ehrlicher Hinweis: Einstiegsrollen in reiner Sportwissenschaft können unter diesen Schwellen liegen — deshalb macht Spezialisierung (Management, Daten, betriebliches Gesundheitsmanagement) den Unterschied.

## Fazit & ehrlicher Rat

Sportwissenschaft in Deutschland zu studieren ist ein leidenschaftlicher, interdisziplinärer und auf der staatlichen Seite sehr günstiger Weg — und das Feld beherbergt eine der weltbesten Einrichtungen (DSHS Köln). Aber ehrlich gesagt ist „ich habe Sportwissenschaft studiert" allein am Arbeitsmarkt keine starke Karte. Mein Rat:

1. **Bereite dich ernsthaft auf die Eignungsprüfung vor:** NC + Sporteignungsprüfung sind ein echter Filter; übe Monate im Voraus.
2. **Spezialisiere dich früh:** **Sportmanagement, Performance-/Datenanalytik, betriebliches Gesundheitsmanagement (BGM) oder Rehabilitation** — viel beschäftigungsfreundlicher als reine akademische Sportwissenschaft.
3. **Prüfe die Physiotherapie-Brücke:** wenn du Gesundheit/Reha willst, bietet Physiotherapie einen viel klareren Beschäftigungsweg; [Physiotherapie-Ausbildung und -Studium in Deutschland für Internationale](/de/blog/physiotherapy-training-and-study-in-germany-for-internationals-de) ist ein guter Start.
4. **Denke die Karriere von Anfang an mit:** welche Rolle dich beschäftigungsfähig macht, behandle ich in [Mit Sportwissenschaft in Deutschland arbeiten: Karriere und Gehalt](/de/blog/working-in-sports-science-in-germany-careers-and-salary-de) und die konkreten Berufswege mit dem Abschluss in [Was tun mit einem Sportwissenschaft-Abschluss in Deutschland](/de/blog/what-to-do-with-a-sports-science-degree-in-germany-job-market-de).

Triff deine Entscheidung nicht nach „ich liebe Sport", sondern danach, **welche Spezialisierung dich beschäftigungsfähig und gut bezahlt macht**.

*Dieser Artikel wurde Anfang 2026 erstellt. Studiengebühren, NC- und Sporteignungsprüfungsbedingungen, Bewerbungsfristen, Sperrkonto-Betrag, Blue-Card-Gehaltsschwellen und Marktzahlen variieren je nach Bundesland, Hochschule und Jahr. Prüfe vor der Bewerbung unbedingt die aktuellen Angaben der jeweiligen Hochschule und offizieller Stellen.*
MD;

        $enBody = <<<'MD'
Sport isn't just a hobby — in Germany it's an entire **field of science, health and industry**. Studying **Sportwissenschaft (sports science)** means entering an interdisciplinary path spanning movement science, sports medicine, rehabilitation, training science, sports psychology and sports management. The honest truth: the field is exciting, and Germany is home to one of the world's centres for it — the **Deutsche Sporthochschule Köln (DSHS Köln)**. But a "pure" sports science degree on its own can be competitive and modestly paid. The real payoff is in **specialisation**. In this article I explain from start to finish how studying sports science in Germany works as an international student — including the NC and the Sporteignungsprüfung.

## The field: not a single "sports subject"

Sportwissenschaft isn't a narrow subject; it's a broad umbrella with very different directions. Broadly, the sub-fields are:

- **Sports & Exercise Science:** performance, physiology, biomechanics — the "hard science" end of the field.
- **Sports Medicine & Rehabilitation:** injury, recovery, health-oriented movement; neighbouring physiotherapy.
- **Training & Movement Science:** training planning, motor learning, elite-athlete development.
- **Sports Management:** club/facility management, marketing, events, the sports industry — one of the most employment-strong ends.
- **Sports Psychology:** performance mindset, motivation, counselling.
- **Lehramt Sport (PE teaching):** the route into school teaching (requires the Staatsexamen).
- **Performance / Data Analytics:** data, tracking technology, performance analytics — the fastest-growing end.

The key point: this field is **both physical and academic**. Physiology, anatomy, statistics and biomechanics matter as much as pedagogy and management. If you want to stay closer to the health/rehab side, it's worth looking at the neighbouring and very employment-friendly field of physiotherapy; I cover the details in [Becoming a physiotherapist in Germany as a foreigner](/en/blog/becoming-a-physiotherapist-in-germany-as-a-foreigner-en).

## Recognised schools: DSHS Köln first

Germany's standout feature: it has a **dedicated university just for sports science.**

| School | Highlight |
|---|---|
| **Deutsche Sporthochschule Köln (DSHS Köln)** | Germany's **ONLY** sports university; ~6,000 students; the largest and most respected sports science institution in the world. Rehabilitation, movement science, sports medicine, sports management, performance — all here |
| **Uni Freiburg / Uni Tübingen** | strong tradition in sports science + sports medicine |
| **TU München (TUM)** | sport & health sciences, strong research and technology ties |
| **Uni Potsdam / Uni Leipzig** | training science and elite-sport tradition (Leipzig historically strong) |
| **Uni Stuttgart / Uni Mainz / Ruhr-Uni Bochum** | broad sports science programs, including teaching |
| **Uni Jena / TU Chemnitz** | solid regional options, sports science + teaching |

Before chasing a name/brand, it's important to understand how "prestige" works in Germany — DSHS Köln is a world brand in its field, yet the gap between most public universities is smaller than people assume. I explain this honestly in [How university prestige and rankings work in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en).

## Honest warning: NC + Sporteignungsprüfung

This is the most critical point many international students walk into unaware. The sports science bachelor's in Germany usually hits **two filters**:

1. **NC (Numerus Clausus):** in popular programs the number of places is limited; your grade average (Abitur/recognised equivalent) can be decisive. Sports science is NC-restricted at many schools.
2. **Sporteignungsprüfung (sports aptitude/fitness test):** DSHS Köln and most universities require a physical aptitude test **before** enrolment. In disciplines like running, swimming, gymnastics, team sports and athletics, you're expected to meet a set performance standard. This test is serious; many who arrive unprepared are eliminated.

Honest advice: find out the **date and requirements of the Sporteignungsprüfung early** at your target school and train physically for months. Without this test you can't enter most bachelor's programs — plan accordingly.

## German vs English

- **Bachelor's:** almost entirely runs in **German**, and you're effectively expected to have **German C1** (TestDaF/DSH). For the sports science bachelor's, the path without German is practically closed.
- **Master's:** English options **exist but are limited** — especially in Exercise Science, Sport Management, Human Movement. Without German, the realistic route is an English master's; I've gathered which programs are English and what the thresholds are in [English-taught sports & exercise science master's in Germany](/en/blog/english-taught-sports-science-and-exercise-science-masters-in-germany-en).

Note: even in an English master's, German still makes a big difference for internships/Werkstudent jobs and the job search. Don't defer German to "later".

## Applying: uni-assist and documents

- **uni-assist:** many public universities bundle international applications through **uni-assist** (certificate evaluation and pre-checking). Some accept directly via their own portal — always check the school's page.
- **Documents:** school/bachelor's certificate and transcript, language proof (German C1 **or** IELTS/TOEFL for English programs), motivation letter, CV — and for sports science the **Sporteignungsprüfung result / a medical certificate**.
- **Deadlines:** winter-semester applications usually close around **15 July**; aptitude-test dates are usually earlier (spring/summer). Track both calendars.

## Fees & living & Sperrkonto & Blue Card

- **Fees:** public universities charge **no tuition**; only a semester contribution of ~**€150–350** (may include a semester ticket). Exception: **Baden-Württemberg** charges non-EU students ~**€1,500/semester**. Private schools: several **thousand euros** per year. *As of 2025/2026, approximate; verify.*
- **Sperrkonto (blocked account):** for the visa you're usually asked to show ~**€992/month = ~€11,904/year**. *As of 2025/2026, approximate; verify from official sources.*
- **Scholarships:** **DAAD** is the best-known source; also the Deutschlandstipendium and foundation scholarships.
- **After graduation & Blue Card:** with a job, the 2026 general Blue Card salary threshold is ~**€50,700/year**; the shortage-occupation/new-graduate threshold is ~**€45,934/year**. *Approximate; verify from official sources.* Honest note: entry-level pure-sports-science roles can fall below these thresholds — which is why specialisation (management, data, corporate health) makes the difference.

## Conclusion & honest advice

Studying sports science in Germany is a passionate, interdisciplinary and, on the public side, very affordable path — and the field is home to one of the world's best institutions (DSHS Köln). But honestly, "I studied sports science" alone isn't a strong card in the job market. My advice:

1. **Prepare seriously for the aptitude test:** NC + Sporteignungsprüfung are a real filter; train months ahead.
2. **Specialise early:** **sports management, performance/data analytics, corporate health management (BGM) or rehabilitation** — far more employment-friendly than pure academic sports science.
3. **Consider the physiotherapy bridge:** if you want health/rehab, physiotherapy offers a much clearer employment route; [Physiotherapy training and study in Germany for internationals](/en/blog/physiotherapy-training-and-study-in-germany-for-internationals-en) is a good start.
4. **Think about the career from the outset:** which role makes you employable I cover in [Working in sports science in Germany: careers and salary](/en/blog/working-in-sports-science-in-germany-careers-and-salary-en), and the concrete job paths with the degree in [What to do with a sports science degree in Germany](/en/blog/what-to-do-with-a-sports-science-degree-in-germany-job-market-en).

Make your decision not on "I love sport", but on **which specialisation will make you employable and well paid**.

*This article was prepared in early 2026. Tuition fees, NC and Sporteignungsprüfung conditions, application deadlines, the Sperrkonto amount, Blue Card salary thresholds and market figures vary by state, school and year. Always verify the current information from the relevant school and official bodies before applying.*
MD;

        $variants = [
            'tr' => ['slug'=>'studying-sports-science-sportwissenschaft-in-germany-as-a-foreigner',    'title'=>'Almanya\'da Spor Bilimi (Sportwissenschaft) Okumak: Rehber', 'excerpt'=>'Almanya\'da spor bilimi (Sportwissenschaft) okumak: disiplinlerarası alt dallar, DSHS Köln + diğer okullar (tablo), NC ve Sporteignungsprüfung (spor uygunluk sınavı) uyarısı, Almanca vs İngilizce, uni-assist başvurusu, ücret & yaşam & Sperrkonto & Blue Card ve dürüst tavsiye: uzmanlaş.', 'meta_title'=>'Almanya\'da Spor Bilimi (Sportwissenschaft) Okumak: Rehber', 'meta_description'=>'Almanya\'da spor bilimi okumak: DSHS Köln, NC + Sporteignungsprüfung, Almanca vs İngilizce, uni-assist, ücret, Sperrkonto ve Blue Card gerçeği. Dürüst rehber.', 'body'=>$trBody],
            'de' => ['slug'=>'studying-sports-science-sportwissenschaft-in-germany-as-a-foreigner-de', 'title'=>'Sportwissenschaft in Deutschland studieren: Leitfaden', 'excerpt'=>'Sportwissenschaft in Deutschland studieren: interdisziplinäre Teilbereiche, DSHS Köln + weitere Hochschulen (Tabelle), Warnung zu NC und Sporteignungsprüfung, Deutsch vs. Englisch, uni-assist-Bewerbung, Kosten & Leben & Sperrkonto & Blue Card und ein ehrlicher Rat: spezialisiere dich.', 'meta_title'=>'Sportwissenschaft in Deutschland studieren: Leitfaden', 'meta_description'=>'Sportwissenschaft in Deutschland studieren: DSHS Köln, NC + Sporteignungsprüfung, Deutsch vs. Englisch, uni-assist, Kosten, Sperrkonto und Blue-Card-Realität.', 'body'=>$deBody],
            'en' => ['slug'=>'studying-sports-science-sportwissenschaft-in-germany-as-a-foreigner-en', 'title'=>'Studying Sports Science (Sportwissenschaft) in Germany: A Guide', 'excerpt'=>'Studying sports science (Sportwissenschaft) in Germany: interdisciplinary sub-fields, DSHS Köln + other schools (table), the NC and Sporteignungsprüfung (sports aptitude test) warning, German vs English, uni-assist application, fees & living & Sperrkonto & Blue Card, and honest advice: specialise.', 'meta_title'=>'Studying Sports Science (Sportwissenschaft) in Germany: A Guide', 'meta_description'=>'Studying sports science in Germany: DSHS Köln, NC + Sporteignungsprüfung, German vs English, uni-assist, fees, Sperrkonto and the Blue Card reality. Honest guide.', 'body'=>$enBody],
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
            'studying-sports-science-sportwissenschaft-in-germany-as-a-foreigner',
            'studying-sports-science-sportwissenschaft-in-germany-as-a-foreigner-de',
            'studying-sports-science-sportwissenschaft-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};

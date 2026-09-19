<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN) — konu havuzu partisi 2/3, yazı 2/4: Urlaubssemester (izin dönemi).
 *
 * Doğrulanmış veriler (Eylül 2026):
 *   - Sınav: birçok üniversite izin döneminde sınavı tamamen kapatır, bazıları yalnızca
 *     bütünlemeye izin verir → kural üniversiteye/eyalete göre değişir, tek cevap yok.
 *   - Dönem katkı payı (Semesterbeitrag) yine ödenir; bazı kalemler (Semesterticket, AStA payı)
 *     üniversiteye göre kısmen düşebilir.
 *   - WERKSTUDENTENPRIVILEG İZİN DÖNEMİNDE KAYBEDİLİR → tam sosyal sigorta yükümlülüğü doğar.
 *   - Öğrenci sağlık sigortası: kayıt devam ettiği için normalde sürer; ancak izin döneminde
 *     düzenli çalışmaya başlanırsa öğrenci tarifesi biter, işçi olarak sigortalanılır.
 *   - BAföG: izin döneminde hak doğmaz (Auslands-BAföG istisnası).
 *   - Uluslararası öğrenci: gönüllü staj için izin dönemi talep edilmeden ÖNCE Ausländerbehörde
 *     onayı gerekir; 140 tam / 280 yarım gün sınırı işlemeye devam eder; sınırın üstü için ABH +
 *     iş ajansı izni şarttır (uni-heidelberg / uni-mainz uluslararası ofis bilgilendirmeleri).
 * Yazar: Halil Yaprakli. Kategori: basvuru.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'b62f18d4-7c05-4e93-a1f6-52d9c70b48e3';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'basvuru')->value('id')
            ?? DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Bazı dönemler yürümez: hastalık gelir, para biter, tükenirsin ya da hayatının fırsatı olan staj tam da sınav haftasına denk düşer. Alman üniversitelerinde bunun resmî bir karşılığı var: **Urlaubssemester** (izin dönemi, resmî adıyla *Beurlaubung*).

Mantığı basit: **kaydın devam eder, aktif öğrenciliğin durur.** Ama uluslararası öğrenci için bu kararın oturum ve sigorta tarafında iki tuzağı var — yazının asıl sebebi onlar.

## Hangi gerekçeler kabul edilir?

Üniversiteler arasında ayrıntı değişse de tipik liste şu:

| Gerekçe | Genelde istenen belge |
|---|---|
| Hastalık | Doktor raporu (Attest) |
| Hamilelik / ebeveynlik | Doğum belgesi, doktor raporu |
| Yakın bakımı | Bakım derecesi belgesi |
| Staj | Staj sözleşmesi (**uluslararası öğrencide ek şart var, aşağıda**) |
| Yurt dışında eğitim | Kabul/öğrenim belgesi |
| Askerlik / gönüllü hizmet | Çağrı belgesi |

Tipik sınır **iki dönemdir** (bazı gerekçelerde daha uzun) ve ilk dönemde genelde izin verilmez. Kendi üniversitenin yönetmeliğini oku; bu kurallar eyalet ve kuruma göre değişiyor.

## İzin dönemi neyi değiştirir?

| Konu | İzin döneminde ne olur |
|---|---|
| Ders ve sınav | **Çoğu üniversitede sınav yasak**; bazıları yalnızca bütünlemeye izin verir. Tek bir ulusal kural yok — yönetmeliğini oku. |
| Fachsemester sayacı | Alan dönemi olarak **sayılmaz** (Regelstudienzeit'i korur) |
| Dönem katkı payı | **Yine ödenir**; bazı kalemler (Semesterticket, AStA payı) üniversiteye göre kısmen düşebilir |
| BAföG | Hak **doğmaz** (yurt dışı BAföG istisnası) |
| Öğrenci sağlık sigortası | Kayıt sürdüğü için normalde devam eder — **ama çalışmaya başlarsan değişir** |
| Werkstudentenprivileg | **Kaybedilir** |
| Kütüphane, e-posta, kampüs erişimi | Genelde devam eder |

## Sigorta tuzağı: "izin aldım, tam zamanlı çalışayım"

En sık yapılan plan bu ve en pahalı hata da burada. İzin döneminde **Werkstudentenprivileg geçerli değildir**: normalde ödemediğin sağlık, bakım ve işsizlik primleri devreye girer, yani net kazancın düşer.

Üstüne, izin döneminde düzenli bir işe girersen **öğrenci sağlık sigortası tarifen sona erer** ve işçi olarak sigortalanırsın — primler öğrenci tarifesinin belirgin şekilde üstündedir. Tarifeler ve sağlayıcılar için [sigorta rehberimize](/tr/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs), çalışma biçimlerinin karşılaştırması için [HiWi–Werkstudent yazımıza](/tr/blog/hiwi-vs-werkstudent-student-jobs-in-germany) bak.

## Uluslararası öğrenci için kritik kısım: oturum izni

Oturum iznin (**§ 16b AufenthG**) öğrenim amacına bağlıdır. İzin dönemi, tanımı gereği o amacın duraklaması demektir — bu yüzden otomatik bir hak gibi davranma:

- **Ausländerbehörde'ye bildir.** Sessiz kalmak, uzatma başvurusunda "öğrenimini sürdürmüyor" değerlendirmesine yol açabilir.
- **Gönüllü staj için izin dönemi alacaksan, başvurudan ÖNCE ABH onayı gerekir.** Bu sıralama önemli: önce onay, sonra üniversiteye izin talebi.
- **140 tam / 280 yarım gün sınırı izin döneminde de işler.** İzin dönemi "sınırsız çalışma" anlamına gelmez; sınırın üstü için ABH ve iş ajansı izni şarttır.
- Hastalık ve ebeveynlik gerekçeleri genelde sorunsuz kabul edilir; "çalışmak için izin dönemi" en çok sorgulanan gerekçedir.

Oturum uzatman bu sırada gecikirse haklarının ne olduğunu [Ausländerbehörde gecikmesi yazımızda](/tr/blog/auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage) anlattık.

## Başvuru: zamanlama her şey

1. **Yönetmeliği oku** — kaç dönem, hangi gerekçe, hangi belge.
2. **Süreye dikkat et:** başvuru genelde dönem başında veya kayıt yenileme süresi içinde yapılır; dönem ortasında başvuru çoğu üniversitede kabul edilmez.
3. **Belgeyi hazırla:** rapor, sözleşme veya kabul yazısı.
4. **Uluslararası öğrenciysen ABH adımını atla** (yukarıdaki sıra).
5. **Kayıt yenilemeyi (Rückmeldung) yine yap** — izin dönemi kaydı canlı tutar, ama harç ödenmezse kayıt silinir. Bunun sonucunu [Exmatrikulation yazımızda](/tr/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back) anlattık.

## Ne zaman iyi fikir, ne zaman değil?

**İyi fikir:** ciddi hastalık veya ameliyat sürecindeysen; ebeveyn olduysan; yurt dışında bir dönem okuyacaksan; kariyerini gerçekten değiştirecek uzun bir staj bulduysan (ve ABH onayını aldıysan).

**Kötü fikir:** yalnızca "biraz nefes almak" için — çünkü sınav yasağı çoğu üniversitede geçerli, yani bir dönem tamamen kaybedilir; para kazanmak için — çünkü sigorta ayrıcalıkların düşer ve 140 gün sınırı yine işler; bir dersten kaçmak için — çünkü sınav hakkı sorunu ertelenir, çözülmez.

## Sıkça Sorulanlar

### İzin döneminde sınava girebilir miyim?
Üniversiteye göre değişir. Birçok kurumda hiç sınav yapılamaz; bazıları yalnızca bütünleme/tekrar sınavına izin verir. Karar vermeden önce Prüfungsordnung'u okuman şart — bu tek soru, izin döneminin sana kaç ay kaybettireceğini belirler.

### Harcı yine ödeyecek miyim?
Evet. Bazı üniversitelerde Semesterticket veya bazı kalemler düşer, ama katkı payı tamamen kalkmaz. Ödemezsen kaydın silinir.

### Vizem/oturumum iptal olur mu?
Otomatik olarak hayır. Ama amaç değişikliği olarak değerlendirilebileceği için ABH'ye bildirmek ve gerekçeyi belgelemek gerekir. Hastalık ve ebeveynlik en kolay kabul edilen gerekçelerdir.

### İzin döneminde tam zamanlı çalışabilir miyim?
Sosyal sigorta tarafında çalışabilirsin ama öğrenci ayrıcalıklarını kaybedersin. Uluslararası öğrenciysen **140 gün sınırı devam eder** — yani "tam zamanlı bir dönem" çoğu durumda sınırı aşar ve izin gerektirir.

### İzin dönemi mezuniyetimi geciktirir mi?
Fachsemester sayacı durduğu için Regelstudienzeit açısından korunursun; ama takvim olarak evet, mezuniyetin bir dönem ötelenir (sınav yasağı varsa kesin olarak).

### Kaç kez izin alabilirim?
Tipik üst sınır iki dönemdir; hastalık ve ebeveynlik gibi gerekçelerde bazı üniversiteler daha fazlasına izin verir. Yönetmelik belirleyicidir.

## Sonuç ve dürüst tavsiye

Urlaubssemester, doğru gerekçeyle kullanıldığında kariyerini kurtaran bir araç: hastalıkken sınav zorlamak yerine iyileşmeyi, tükenmişken bırakmak yerine ara vermeyi mümkün kılar.

Ama "boş bir dönem" değildir. Karar vermeden önce üç soruyu cevapla: **Sınav girebilecek miyim? Sigortam ne olacak? Ausländerbehörde ne diyecek?** Bu üçünü yazılı cevaplarıyla bilen öğrenci için izin dönemi güvenli bir moladır; bilmeyen için bir dönem ve birkaç yüz euro kaybıdır.

*Kurallar 2026 Eylül itibarıyla geçerli genel çerçevedir; izin dönemi koşulları üniversiteye, eyalete ve Prüfungsordnung'a göre değişir. Kendi üniversitenin yönetmeliği ve şehrinin Ausländerbehörde'si esas alınmalı.*
MD;

        $deBody = <<<'MD'
Manche Semester laufen einfach nicht: Krankheit kommt dazwischen, das Geld reicht nicht, die Kraft ist weg — oder das Praktikum, auf das du gewartet hast, fällt genau in die Prüfungsphase. Dafür gibt es an deutschen Hochschulen eine offizielle Lösung: das **Urlaubssemester** (formal die *Beurlaubung*).

Die Idee ist einfach: **Deine Immatrikulation bleibt bestehen, dein aktives Studium pausiert.** Für internationale Studierende hat diese Entscheidung aber zwei Fallen im Aufenthalts- und Versicherungsrecht — und genau darum geht es hier.

## Welche Gründe werden anerkannt?

Die Details unterscheiden sich, die typische Liste sieht so aus:

| Grund | Üblicher Nachweis |
|---|---|
| Krankheit | Ärztliches Attest |
| Schwangerschaft / Elternzeit | Geburtsurkunde, ärztliche Bescheinigung |
| Pflege von Angehörigen | Nachweis des Pflegegrads |
| Praktikum | Praktikumsvertrag (**für Internationale gilt eine Zusatzbedingung, siehe unten**) |
| Studium im Ausland | Zulassungs- oder Studienbescheinigung |
| Wehr- oder Freiwilligendienst | Einberufungsbescheid |

Üblich sind **zwei Semester** (bei manchen Gründen mehr), und im ersten Semester wird meist nicht beurlaubt. Lies die Ordnung deiner Hochschule — diese Regeln sind Länder- und Hochschulsache.

## Was ändert sich?

| Bereich | Im Urlaubssemester |
|---|---|
| Lehrveranstaltungen und Prüfungen | **An vielen Hochschulen sind Prüfungen ausgeschlossen**; manche lassen nur Wiederholungsprüfungen zu. Eine bundesweite Regel gibt es nicht. |
| Fachsemesterzähler | zählt **nicht** mit (schützt die Regelstudienzeit) |
| Semesterbeitrag | **fällt weiter an**; einzelne Bestandteile (Semesterticket, AStA-Beitrag) können je nach Hochschule entfallen |
| BAföG | **kein Anspruch** (Ausnahme: Auslands-BAföG) |
| Studentische Krankenversicherung | läuft normalerweise weiter — **ändert sich aber, sobald du arbeitest** |
| Werkstudentenprivileg | **entfällt** |
| Bibliothek, Hochschulkonto, Campuszugang | bleiben meist bestehen |

## Die Versicherungsfalle: „Ich beurlaube mich und arbeite Vollzeit"

Das ist der häufigste Plan — und der teuerste Irrtum. Im Urlaubssemester gilt das **Werkstudentenprivileg nicht**: Beiträge zur Kranken-, Pflege- und Arbeitslosenversicherung, die sonst entfallen, werden fällig. Vom Brutto bleibt spürbar weniger.

Zusätzlich endet mit einer regulären Beschäftigung im Urlaubssemester die **studentische Krankenversicherung**; du wirst als Arbeitnehmerin oder Arbeitnehmer versichert, und die Beiträge liegen deutlich über dem Studierendentarif. Tarife und Anbieter findest du im [Versicherungsratgeber](/de/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs-de), den Vergleich der Arbeitsformen im [HiWi-vs-Werkstudent-Artikel](/de/blog/hiwi-vs-werkstudent-student-jobs-in-germany-de).

## Der kritische Teil für Internationale: der Aufenthaltstitel

Dein Titel nach **§ 16b AufenthG** ist an den Studienzweck gebunden. Ein Urlaubssemester bedeutet definitionsgemäß eine Pause dieses Zwecks — behandle es also nicht als Selbstverständlichkeit:

- **Informiere die Ausländerbehörde.** Schweigen kann bei der Verlängerung zu der Einschätzung führen, dass du dein Studium nicht betreibst.
- **Für ein freiwilliges Praktikum brauchst du die Zustimmung der ABH, BEVOR du die Beurlaubung beantragst.** Diese Reihenfolge ist entscheidend: erst Zustimmung, dann Antrag an der Hochschule.
- **Die Grenze von 140 vollen bzw. 280 halben Tagen gilt auch im Urlaubssemester.** Beurlaubung heißt nicht unbegrenztes Arbeiten; darüber hinaus sind Zustimmung der ABH und der Arbeitsagentur nötig.
- Krankheit und Elternzeit werden in der Regel problemlos akzeptiert; „Beurlaubung zum Arbeiten" wird am kritischsten geprüft.

Verzögert sich in dieser Zeit deine Verlängerung, erklärt unser Artikel zu [Verzögerungen der Ausländerbehörde](/de/blog/auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage-de), welche Rechte du hast.

## Antrag: alles hängt am Zeitpunkt

1. **Ordnung lesen** — wie viele Semester, welche Gründe, welche Nachweise.
2. **Frist beachten:** der Antrag läuft meist zu Semesterbeginn oder innerhalb der Rückmeldefrist; mitten im Semester wird er selten angenommen.
3. **Nachweis vorbereiten:** Attest, Vertrag oder Zulassung.
4. **Als internationale Studierende zuerst die ABH einbinden** (Reihenfolge siehe oben).
5. **Rückmeldung trotzdem machen** — die Beurlaubung hält die Immatrikulation, aber ohne gezahlten Beitrag folgt die Exmatrikulation. Die Folgen stehen in unserem [Exmatrikulations-Artikel](/de/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-de).

## Wann sinnvoll, wann nicht?

**Sinnvoll:** bei ernsthafter Krankheit oder Operation; nach der Geburt eines Kindes; für ein Auslandssemester; für ein langes Praktikum, das deinen Berufsweg wirklich verändert (mit ABH-Zustimmung).

**Nicht sinnvoll:** nur um „durchzuatmen" — weil an vielen Hochschulen keine Prüfungen möglich sind und damit ein ganzes Semester verloren geht; um Geld zu verdienen — weil die Versicherungsvorteile wegfallen und die 140-Tage-Grenze weiterläuft; um einer Prüfung auszuweichen — das verschiebt das Problem, löst es nicht.

## Häufige Fragen

### Darf ich im Urlaubssemester Prüfungen schreiben?
Das hängt von der Hochschule ab. Viele schließen Prüfungen komplett aus, andere lassen nur Wiederholungen zu. Lies die Prüfungsordnung, bevor du entscheidest — diese eine Frage bestimmt, wie viele Monate dich die Beurlaubung kostet.

### Muss ich den Semesterbeitrag zahlen?
Ja. An manchen Hochschulen entfallen Semesterticket oder einzelne Posten, der Beitrag als Ganzes aber nicht. Ohne Zahlung folgt die Exmatrikulation.

### Verliere ich meinen Aufenthaltstitel?
Nicht automatisch. Da es aber als Unterbrechung des Studienzwecks gelesen werden kann, solltest du die ABH informieren und den Grund belegen. Krankheit und Elternzeit werden am ehesten akzeptiert.

### Kann ich im Urlaubssemester Vollzeit arbeiten?
Sozialversicherungsrechtlich ja, aber ohne studentische Vorteile. Für Internationale gilt die **140-Tage-Grenze weiter** — ein Vollzeitsemester überschreitet sie meist und braucht eine Zustimmung.

### Verzögert die Beurlaubung meinen Abschluss?
Der Fachsemesterzähler pausiert, die Regelstudienzeit bleibt also geschützt. Kalendarisch verschiebt sich dein Abschluss dennoch um ein Semester — bei Prüfungsverbot sicher.

### Wie oft ist eine Beurlaubung möglich?
Üblich sind maximal zwei Semester; bei Krankheit oder Elternzeit erlauben manche Hochschulen mehr. Maßgeblich ist die Ordnung.

## Fazit und ehrlicher Rat

Mit dem richtigen Grund ist das Urlaubssemester ein Instrument, das Studienverläufe rettet: Es macht Genesung möglich statt erzwungener Prüfungen und eine Pause statt eines Abbruchs.

Ein „freies Semester" ist es nicht. Beantworte vor der Entscheidung drei Fragen: **Darf ich Prüfungen schreiben? Was passiert mit meiner Versicherung? Was sagt die Ausländerbehörde?** Wer darauf schriftliche Antworten hat, macht eine sichere Pause; wer nicht, verliert ein Semester und einige hundert Euro.

*Die Darstellung gibt den allgemeinen Rahmen mit Stand September 2026 wieder; die Bedingungen unterscheiden sich nach Hochschule, Bundesland und Prüfungsordnung. Maßgeblich sind die Ordnung deiner Hochschule und deine Ausländerbehörde.*
MD;

        $enBody = <<<'MD'
Some semesters simply do not work: illness arrives, the money runs out, you burn out — or the internship you have been waiting for lands exactly in the exam period. German universities have an official answer for this: the **Urlaubssemester** (formally *Beurlaubung*), a semester on leave.

The logic is simple: **your enrolment continues, your active studies pause.** For international students, though, the decision carries two traps — in residence law and in insurance — and those are the reason for this article.

## Which grounds are accepted?

Details differ between universities, but the typical list looks like this:

| Ground | Evidence usually required |
|---|---|
| Illness | A doctor's certificate (Attest) |
| Pregnancy / parental leave | Birth certificate, medical confirmation |
| Caring for a relative | Proof of care level |
| Internship | Internship contract (**an extra condition applies to international students, below**) |
| Studying abroad | Admission or enrolment confirmation |
| Military or voluntary service | Call-up notice |

The usual cap is **two semesters** (more for some grounds), and leave is rarely granted in your first semester. Read your own university's regulations — these rules are set by state and institution.

## What changes during a leave semester?

| Area | During the leave semester |
|---|---|
| Classes and exams | **Many universities prohibit exams entirely**; some allow retakes only. There is no national rule. |
| Subject-semester counter | does **not** count (protecting your standard period of study) |
| Semester contribution | **still payable**; individual parts (semester ticket, student union fee) may drop depending on the university |
| BAföG | **no entitlement** (except BAföG for study abroad) |
| Student health insurance | normally continues while you are enrolled — **but changes as soon as you work** |
| Working-student privilege | **lost** |
| Library, university account, campus access | usually continue |

## The insurance trap: "I'll take leave and work full-time"

This is the most common plan, and the most expensive mistake. During a leave semester the **working-student privilege does not apply**: contributions to health, care and unemployment insurance that you would normally avoid become due, and noticeably less of your gross pay remains.

On top of that, taking regular employment during a leave semester **ends your student health insurance tariff**; you are insured as an employee instead, at premiums well above the student rate. For tariffs and providers see our [insurance guide](/en/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs-en), and for a comparison of the work formats our [HiWi vs Werkstudent article](/en/blog/hiwi-vs-werkstudent-student-jobs-in-germany-en).

## The critical part for international students: your residence permit

Your permit under **Section 16b AufenthG** is tied to the purpose of study. A leave semester is by definition a pause in that purpose, so do not treat it as automatic:

- **Inform the immigration office.** Silence can lead to an assessment at renewal that you are not pursuing your studies.
- **For a voluntary internship you need the authority's approval BEFORE you apply for leave.** The order matters: approval first, then the request to your university.
- **The 140 full / 280 half day limit still applies during a leave semester.** Leave does not mean unlimited work; going beyond requires approval from the immigration office and the employment agency.
- Illness and parental leave are generally accepted without difficulty; "leave in order to work" is the ground examined most critically.

If your permit extension stalls in the meantime, our article on [Ausländerbehörde delays](/en/blog/auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage-en) explains your rights.

## Applying: timing is everything

1. **Read the regulations** — how many semesters, which grounds, which documents.
2. **Watch the deadline:** applications usually run at the start of the semester or within the re-registration window; mid-semester requests are rarely accepted.
3. **Prepare the evidence:** certificate, contract or admission letter.
4. **If you are an international student, involve the immigration office first** (order as above).
5. **Still complete your re-registration** — leave keeps your enrolment alive, but an unpaid contribution ends it. The consequences are in our [de-registration guide](/en/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-en).

## When it is a good idea — and when it is not

**A good idea:** during serious illness or surgery; after the birth of a child; for a semester abroad; for a long internship that genuinely changes your career path (with the immigration office's approval).

**Not a good idea:** simply to "catch your breath" — because many universities bar exams, so a whole semester is lost; to earn money — because the insurance advantages fall away and the 140-day limit still runs; to dodge an exam — that postpones the problem rather than solving it.

## Frequently asked questions

### Can I sit exams during a leave semester?
It depends on the university. Many prohibit exams altogether, others allow retakes only. Read the examination regulations before deciding — this single question determines how many months the leave will cost you.

### Do I still pay the semester contribution?
Yes. Some universities drop the semester ticket or individual items, but not the contribution as a whole. Without payment, your enrolment ends.

### Will I lose my residence permit?
Not automatically. But because it can be read as an interruption of your study purpose, inform the immigration office and document the reason. Illness and parental leave are the most readily accepted grounds.

### Can I work full-time during the leave semester?
In social insurance terms yes, but without student advantages. For international students the **140-day limit still applies** — a full-time semester usually exceeds it and requires approval.

### Does leave delay my graduation?
Your subject-semester counter pauses, so your standard period of study is protected. In calendar terms, though, graduation moves back a semester — certainly so where exams are barred.

### How often can I take leave?
Two semesters is the usual maximum; some universities allow more for illness or parental leave. The regulations decide.

## Conclusion and honest advice

Used for the right reason, a leave semester rescues degrees: it makes recovery possible instead of forced exams, and a pause possible instead of dropping out.

It is not a free semester. Before deciding, answer three questions: **Can I sit exams? What happens to my insurance? What does the immigration office say?** A student who has those answers in writing takes a safe break; one who does not loses a semester and several hundred euros.

*This describes the general framework as of September 2026; conditions vary by university, state and examination regulations. Your own university's rules and your local immigration office are the authoritative sources.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'urlaubssemester-taking-a-semester-off-in-germany',
                'title' => 'Urlaubssemester: Almanya\'da Bir Döneme Ara Vermek',
                'excerpt' => 'Kaydın devam eder, aktif öğrenciliğin durur. Hangi gerekçeler kabul edilir, sınav girebilir misin, harç ne olur — ve uluslararası öğrenci için iki tuzak: Werkstudentenprivileg\'in kaybı ve gönüllü staj için Ausländerbehörde ön onayı.',
                'meta_title' => 'Urlaubssemester: İzin Dönemi, Sigorta ve Oturum Etkisi',
                'meta_description' => 'Almanya\'da izin dönemi (Urlaubssemester): kabul edilen gerekçeler, sınav yasağı, harç, Werkstudentenprivileg kaybı ve 140 gün kuralı (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug' => 'urlaubssemester-taking-a-semester-off-in-germany-de',
                'title' => 'Urlaubssemester: ein Semester pausieren — und was dabei zu beachten ist',
                'excerpt' => 'Die Immatrikulation bleibt, das Studium pausiert. Welche Gründe anerkannt werden, ob Prüfungen möglich sind, was mit dem Beitrag passiert — und die zwei Fallen für Internationale: Wegfall des Werkstudentenprivilegs und die vorherige Zustimmung der Ausländerbehörde.',
                'meta_title' => 'Urlaubssemester: Beurlaubung, Versicherung und Aufenthalt',
                'meta_description' => 'Urlaubssemester in Deutschland: anerkannte Gründe, Prüfungsverbot, Semesterbeitrag, Wegfall des Werkstudentenprivilegs und die 140-Tage-Regel (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug' => 'urlaubssemester-taking-a-semester-off-in-germany-en',
                'title' => 'Urlaubssemester: Taking a Semester Off at a German University',
                'excerpt' => 'Your enrolment continues, your studies pause. Which grounds are accepted, whether you can sit exams, what happens to the contribution — plus two traps for international students: losing the working-student privilege and needing prior approval for an internship.',
                'meta_title' => 'Urlaubssemester: Leave of Absence, Insurance and Residence',
                'meta_description' => 'Taking a semester off in Germany: accepted grounds, exam bans, the semester fee, losing the working-student privilege and the 140-day rule (2026).',
                'body' => $enBody,
            ],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale' => $locale, 'translation_group_id' => $groupId, 'user_id' => $userId, 'category_id' => $categoryId,
                'title' => $v['title'], 'excerpt' => Str::limit($v['excerpt'], 250, '…'),
                'content_md' => $v['body'], 'content_html' => $html,
                'meta_title' => $v['meta_title'], 'meta_description' => Str::limit($v['meta_description'], 158, '…'),
                'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
                'is_published' => true, 'published_at' => now(),
            ];
            $existing = Post::where('slug', $v['slug'])->first();
            $existing ? $existing->update($payload) : Post::create($payload + ['slug' => $v['slug']]);
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'urlaubssemester-taking-a-semester-off-in-germany',
            'urlaubssemester-taking-a-semester-off-in-germany-de',
            'urlaubssemester-taking-a-semester-off-in-germany-en',
        ])->delete();
    }
};

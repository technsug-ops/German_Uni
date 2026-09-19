<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN) — konu havuzu partisi 2/3, yazı 1/4: HiWi mi Werkstudent mi?
 *
 * Doğrulanmış veriler (Eylül 2026):
 *   - Asgari ücret 1 Ocak 2026'dan beri 13,90 €/saat (2027'de 14,60 €).
 *   - Minijob sınırı 603 €/ay (7.236 €/yıl); Midijob 603,01–2.000 € aralığı.
 *   - Gelir vergisi temel muafiyeti (Grundfreibetrag) 2026: 12.348 €.
 *   - Werkstudentenprivileg: dönem içinde ≤20 saat/hafta çalışılırsa sağlık, bakım ve
 *     işsizlik sigortası primi YOK; yalnızca emeklilik primi (işçi payı %9,3) ödenir.
 *   - Minijob'da KV/PV/AV primi yok; emeklilik priminden talep üzerine muafiyet alınabilir.
 *   - § 16b Abs. 3 AufenthG: uluslararası öğrenci yılda 140 tam / 280 yarım gün çalışabilir;
 *     üniversitenin kendi HiWi işleri kural olarak bu hesaba DAHİL DEĞİL.
 * Yazar: Halil Yaprakli. Kategori: yasam.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'a8d94c17-52e6-4f31-b7c9-0d3a15e8f462';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'yasam')->value('id')
            ?? DB::table('categories')->where('slug', 'kariyer')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da ilk işini ararken karşına iki kelime çıkar: **HiWi** ve **Werkstudent**. İkisi de "öğrenci işi" diye çevrilir, ikisi de yasaldır — ama vergisi, sigortası, saat sınırı ve kariyerine katkısı farklıdır. Yanlış seçim, oturum iznini riske atacak kadar da ciddileşebilir.

Bu yazı ikisini karşılaştırıyor ve uluslararası öğrenci için kritik olan noktayı ayrıca işaretliyor: **140 gün kuralı.**

## İki iş, iki mantık

| | **HiWi** (Hilfskraft) | **Werkstudent** |
|---|---|---|
| İşveren | Üniversitenin kendisi (enstitü, kürsü, kütüphane) | Özel şirket |
| Tipik iş | Ders asistanlığı, laboratuvar, araştırma desteği, sınav okuma | Sektörde yarı zamanlı uzman işi |
| Saat | Genelde 6–10 saat/hafta, sözleşmeye bağlı | Dönem içinde **20 saat/hafta** üst sınırı |
| Ücret | Eyalet ve üniversiteye göre değişir, çoğunlukla asgari ücretin biraz üstü | Piyasa ücreti — genelde belirgin şekilde yüksek |
| Sigorta | Öğrenci statüsüne bağlı | **Werkstudentenprivileg** (aşağıda) |
| Kariyer katkısı | Akademik: referans, doktora yolu, yayın teması | Sektör: CV, ağ, mezuniyet sonrası iş teklifi |
| **140 gün kuralına etkisi** | Kural olarak **sayılmaz** | **Sayılır** |

Son satır, uluslararası öğrenci için bu yazının en önemli bilgisi.

## 140 gün kuralı ve HiWi istisnası

Öğrenci oturum izni (**§ 16b AufenthG**) sana yılda **140 tam gün veya 280 yarım gün** çalışma hakkı verir. Kanun metninde açık bir istisna vardır: **üniversitenin kendi bünyesindeki öğrenci asistanlığı (HiWi) bu hesaba kural olarak dahil edilmez.**

Pratik sonucu şu: HiWi işi, 140 günlük bütçeni tüketmeden çalışmanı sağlar. Yani bir HiWi sözleşmesi + yaz döneminde şirket işi kombinasyonu, saf şirket işine göre sana daha fazla çalışma alanı bırakır.

İki uyarı:
- İstisna **kendi üniversitendeki** iş içindir; başka bir kurumun projesinde çalışıyorsan durumu Ausländerbehörde'ye teyit ettir.
- Sınırı aşmak oturum şartlarının ihlalidir ve uzatmada karşına çıkar. Kayıt tut: sözleşme, bordro ve çalışılan günlerin listesi.

## Werkstudentenprivileg: asıl para farkı burada

Werkstudent statüsünün cazibesi ücretten çok **kesintilerden** gelir. Dönem içinde haftada **20 saati aşmadığın** sürece:

- **Sağlık sigortası primi: yok**
- **Bakım sigortası primi: yok**
- **İşsizlik sigortası primi: yok**
- **Emeklilik sigortası: var** — işçi payı brüt maaşın **%9,3**'ü

Yani brütünün büyük kısmı cebinde kalır. Bu ayrıcalık öğrenci statüsüne bağlıdır; kaydın silinirse ([Exmatrikulation yazımız](/tr/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back)) ayrıcalık da biter ve işveren seni normal çalışan gibi sigortalamak zorunda kalır.

**20 saat kuralının istisnaları:** semester tatilinde ve akşam/gece ya da hafta sonu vardiyalarında sınır esneyebilir — ama bu **sosyal sigorta** tarafının kuralıdır. Uluslararası öğrencinin **140 gün** sınırı bundan bağımsız olarak işlemeye devam eder. İki kuralı karıştırmak en sık yapılan hata.

## Para: 2026 rakamlarıyla

| Kavram | 2026 değeri |
|---|---|
| Yasal asgari ücret | **13,90 €/saat** (2027'de 14,60 €) |
| Minijob üst sınırı | **603 €/ay** (yıllık 7.236 €) |
| Midijob aralığı | 603,01 € – 2.000 € |
| Gelir vergisi muafiyet sınırı | **12.348 €/yıl** |

Üç pratik sonuç:

1. **Minijob (603 €'ya kadar):** sağlık, bakım ve işsizlik primi yok; emeklilik priminden talep üzerine muaf olabilirsin. Küçük ama temiz gelir.
2. **12.348 €'nun altında kaldıysan** yıl içinde kesilen gelir vergisini **beyanname ile geri alırsın.** Çoğu öğrenci bunu bilmediği için parasını devlete bırakıyor.
3. **Ücret pazarlığı:** HiWi ücreti genelde üniversitenin cetveline bağlıdır ve pazarlığa kapalıdır; Werkstudent ücreti piyasa işidir, özellikle bilişim ve mühendislikte belirgin biçimde yüksektir.

Vergi numarası ve ilk maaş konusunu [Steuer-ID rehberimizde](/tr/blog/how-to-get-a-german-steuer-id-as-a-student-iban), sigorta tarafını [sigorta karşılaştırmamızda](/tr/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs) anlattık. Maaşın yatması için gereken hesabı ise [banka hesabı yazımız](/tr/blog/opening-a-bank-account-in-germany-the-real-obstacles) adım adım kuruyor.

## Hangisi senin için doğru?

**HiWi'yi seç** — akademik kariyer, doktora veya araştırma düşünüyorsan; hocalarla ilişki kurmak istiyorsan; ders programına uyumlu esnek saat arıyorsan; 140 günlük çalışma bütçeni korumak istiyorsan.

**Werkstudent'i seç** — mezuniyet sonrası sektöre geçmeyi hedefliyorsan; gelir senin için belirleyiciyse; Almanca iş ortamı deneyimi ve CV'de şirket adı istiyorsan; mezuniyette iş teklifi ihtimalini artırmak istiyorsan.

**En iyisi ikisini sırayla yapmak:** ilk yıl HiWi (dil ve akademik uyum kolay, saat esnek), sonraki yıllar Werkstudent (gelir ve iş ağı). Çok sayıda öğrencinin izlediği yol bu.

## Nasıl bulunur?

**HiWi:** enstitü panoları, bölüm web sayfaları, hocaya doğrudan e-posta. En etkili yöntem sonuncusu — dersini iyi geçtiğin hocaya kısa ve net bir mail. Dönem başlangıcından 4-6 hafta önce yaz.

**Werkstudent:** şirket kariyer sayfaları, StepStone/Indeed, üniversite kariyer merkezleri, LinkedIn. Başvuru Almanca isteniyorsa Almanca yaz. Bilişimde İngilizce ilanlar yaygındır.

## Sıkça Sorulanlar

### İkisini aynı anda yapabilir miyim?
Evet, ama toplam saat ve 140 gün bütçesi birlikte hesaplanır (HiWi istisnası saklı). Sosyal sigorta tarafında da toplam 20 saati aşarsan Werkstudent ayrıcalığını kaybedersin.

### Semester tatilinde tam zamanlı çalışabilir miyim?
Sosyal sigorta açısından genelde evet. Ama uluslararası öğrencinin **140 gün** sınırı tatilde de işler — tam zamanlı bir yaz, bütçenin büyük kısmını tüketir.

### HiWi sözleşmesi gerçekten 140 güne sayılmıyor mu?
Kanun kendi üniversitendeki öğrenci asistanlığı için istisna öngörüyor. Yine de sözleşmeyi imzalamadan önce Ausländerbehörde'ye veya üniversitenin International Office'ine yazılı olarak teyit ettir — şehirler arasında uygulama farkı görülebiliyor.

### Ne kadar vergi öderim?
Yıllık kazancın 12.348 €'nun altındaysa gelir vergisi yükün sıfıra iner; yıl içinde kesilenler beyannameyle geri gelir. Emeklilik primi (%9,3) Werkstudent'te yine de kesilir.

### Werkstudent olarak Almanca şart mı?
Sektöre bağlı. Bilişim ve araştırma tarafında İngilizce çalışan ekipler yaygın; müşteriyle temas eden işlerde (satış, İK, sağlık) Almanca fiilen zorunlu.

### Mezuniyet sonrası bu iş beni işe alır mı?
Werkstudent, Almanya'da en yaygın kalıcı işe geçiş yollarından biridir: şirket seni tanır, sen süreci bilirsin. Mezuniyet sonrası statü geçişini [çalışma iznine geçiş yazımızda](/tr/blog/changing-student-visa-to-work-permit-germany-zweckwechsel) anlattık.

## Sonuç ve dürüst tavsiye

Kararı ücrete bakarak verme. Sorulacak doğru soru şu: **mezuniyette elinde ne olsun istiyorsun?** Referans mektubu ve akademik yol istiyorsan HiWi; şirket deneyimi ve iş teklifi istiyorsan Werkstudent.

Ne seçersen seç iki şeyi kayıt altında tut: **çalıştığın günler** ve **sözleşmedeki haftalık saat.** Oturum uzatmasında sana sorulacak iki şey bunlar; sonradan hatırlamaya çalışmak yerine ilk günden bir tabloda tutmak, ileride saatlerce iş kurtarıyor.

*Bu yazıdaki tutarlar ve oranlar 2026 Eylül itibarıyla geçerlidir; asgari ücret, Minijob sınırı ve vergi muafiyeti yıllık güncellenir, çalışma izni uygulaması şehre göre değişebilir. Kendi sözleşmen ve şehrinin Ausländerbehörde sayfası esas alınmalı.*
MD;

        $deBody = <<<'MD'
Bei der ersten Jobsuche in Deutschland tauchen zwei Begriffe auf: **HiWi** und **Werkstudent**. Beides sind Studierendenjobs, beides ist legal — aber Steuern, Sozialversicherung, Stundengrenzen und der Nutzen für deinen Lebenslauf unterscheiden sich. Für internationale Studierende kann die falsche Wahl sogar den Aufenthaltstitel berühren.

Dieser Artikel vergleicht beide und markiert den Punkt, der international Studierende am meisten betrifft: die **140-Tage-Regel**.

## Zwei Jobs, zwei Logiken

| | **HiWi** (Hilfskraft) | **Werkstudent** |
|---|---|---|
| Arbeitgeber | Die Hochschule selbst (Institut, Lehrstuhl, Bibliothek) | Ein Unternehmen |
| Typische Aufgabe | Tutorien, Labor, Forschungsunterstützung, Korrekturen | Fachliche Teilzeitarbeit in der Branche |
| Stunden | meist 6–10 Std./Woche laut Vertrag | im Semester Obergrenze **20 Std./Woche** |
| Vergütung | nach Hochschul- und Landesregelung, meist knapp über Mindestlohn | Marktüblich — in der Regel deutlich höher |
| Versicherung | an den Studierendenstatus gebunden | **Werkstudentenprivileg** (siehe unten) |
| Nutzen | akademisch: Referenzen, Weg zur Promotion, Themennähe | beruflich: Lebenslauf, Netzwerk, Übernahmechance |
| **Wirkung auf die 140-Tage-Regel** | zählt in der Regel **nicht** mit | zählt **mit** |

Die letzte Zeile ist für internationale Studierende die wichtigste Information des Artikels.

## Die 140-Tage-Regel und die HiWi-Ausnahme

Die Aufenthaltserlaubnis zum Studium (**§ 16b AufenthG**) erlaubt dir **140 volle oder 280 halbe Arbeitstage pro Jahr**. Im Gesetz steht eine klare Ausnahme: **studentische Hilfskrafttätigkeiten an der eigenen Hochschule zählen in der Regel nicht mit.**

Praktisch heißt das: Eine HiWi-Stelle verbraucht dein 140-Tage-Budget nicht. Die Kombination aus HiWi-Vertrag und Ferienjob im Unternehmen lässt dir also mehr Spielraum als reine Unternehmensarbeit.

Zwei Hinweise:
- Die Ausnahme gilt für Tätigkeiten an **deiner eigenen** Hochschule; arbeitest du im Projekt einer anderen Einrichtung, lass dir den Fall von der Ausländerbehörde bestätigen.
- Ein Überschreiten verletzt die Bedingungen deines Aufenthalts und fällt spätestens bei der Verlängerung auf. Führe Buch: Vertrag, Abrechnungen und eine Liste der Arbeitstage.

## Werkstudentenprivileg: hier liegt der Geldunterschied

Der Reiz des Werkstudentenstatus liegt weniger im Lohn als in den **Abzügen**. Solange du im Semester **20 Stunden pro Woche** nicht überschreitest, gilt:

- **Krankenversicherung: kein Beitrag**
- **Pflegeversicherung: kein Beitrag**
- **Arbeitslosenversicherung: kein Beitrag**
- **Rentenversicherung: ja** — Arbeitnehmeranteil von **9,3 %** des Bruttolohns

Vom Brutto bleibt dir also viel mehr. Das Privileg hängt am Studierendenstatus: Fällt die Immatrikulation weg (siehe unseren Artikel zur [Exmatrikulation](/de/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-de)), endet es, und der Arbeitgeber muss dich regulär versichern.

**Ausnahmen von der 20-Stunden-Regel:** in den Semesterferien sowie bei Abend-, Nacht- und Wochenendarbeit kann die Grenze flexibler sein — das ist aber eine Regel des **Sozialversicherungsrechts**. Die **140-Tage-Grenze** internationaler Studierender läuft davon unabhängig weiter. Beides zu verwechseln, ist der häufigste Fehler.

## Geld: die Zahlen für 2026

| Größe | Wert 2026 |
|---|---|
| Gesetzlicher Mindestlohn | **13,90 €/Std.** (2027: 14,60 €) |
| Minijob-Grenze | **603 €/Monat** (7.236 € im Jahr) |
| Midijob-Bereich | 603,01 € – 2.000 € |
| Steuerlicher Grundfreibetrag | **12.348 €/Jahr** |

Drei praktische Folgen:

1. **Minijob (bis 603 €):** keine Beiträge zur Kranken-, Pflege- und Arbeitslosenversicherung; von der Rentenversicherung kannst du dich auf Antrag befreien lassen.
2. **Unter 12.348 €** bekommst du die im Jahr einbehaltene Lohnsteuer über die **Steuererklärung zurück.** Viele Studierende wissen das nicht und verschenken Geld.
3. **Verhandlung:** HiWi-Sätze folgen meist einer Hochschulregelung und sind kaum verhandelbar; Werkstudentenlöhne sind Marktsache und liegen besonders in IT und Technik deutlich höher.

Zur Steuernummer und zur ersten Abrechnung siehe unseren [Steuer-ID-Ratgeber](/de/blog/how-to-get-a-german-steuer-id-as-a-student-iban-de), zur Versicherung den [Kassenvergleich](/de/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs-de). Das Konto für die Gehaltszahlung richtest du mit unserem [Kontoeröffnungs-Artikel](/de/blog/opening-a-bank-account-in-germany-the-real-obstacles-de) ein.

## Was passt zu dir?

**HiWi passt**, wenn du an eine akademische Laufbahn, eine Promotion oder Forschung denkst; wenn dir der Kontakt zu Lehrenden wichtig ist; wenn du flexible, stundenplanfreundliche Zeiten brauchst; wenn du dein 140-Tage-Budget schonen willst.

**Werkstudent passt**, wenn du nach dem Abschluss in die Branche willst; wenn das Einkommen entscheidend ist; wenn du Erfahrung in einem deutschen Arbeitsumfeld und einen Firmennamen im Lebenslauf suchst; wenn du die Chance auf eine Übernahme erhöhen willst.

**Am besten nacheinander:** im ersten Jahr HiWi (sprachlich und organisatorisch leichter, flexible Zeiten), danach Werkstudent (Einkommen und Netzwerk). Diesen Weg gehen sehr viele.

## Wie man sie findet

**HiWi:** Aushänge am Institut, Seiten der Lehrstühle, direkte Mail an Lehrende. Das Letzte wirkt am besten — eine kurze, klare Mail an jemanden, bei dem du eine gute Note hattest. Schreib vier bis sechs Wochen vor Semesterbeginn.

**Werkstudent:** Karriereseiten der Unternehmen, StepStone/Indeed, Career Center der Hochschule, LinkedIn. Wird die Bewerbung auf Deutsch verlangt, schreib auf Deutsch. In der IT sind englischsprachige Ausschreibungen verbreitet.

## Häufige Fragen

### Kann ich beides gleichzeitig machen?
Ja, aber Stunden und 140-Tage-Budget werden zusammen betrachtet (die HiWi-Ausnahme bleibt). Überschreitest du insgesamt 20 Wochenstunden, verlierst du das Werkstudentenprivileg.

### Darf ich in den Semesterferien Vollzeit arbeiten?
Sozialversicherungsrechtlich meist ja. Die **140-Tage-Grenze** gilt für internationale Studierende aber auch in den Ferien — ein Vollzeitsommer verbraucht einen großen Teil des Budgets.

### Zählt der HiWi-Vertrag wirklich nicht mit?
Das Gesetz sieht die Ausnahme für studentische Hilfskrafttätigkeiten an der eigenen Hochschule vor. Lass dir den konkreten Fall dennoch vor Vertragsunterzeichnung schriftlich von der Ausländerbehörde oder dem International Office bestätigen; die Praxis unterscheidet sich zwischen Städten.

### Wie viel Steuern zahle ich?
Liegt dein Jahresverdienst unter 12.348 €, fällt im Ergebnis keine Einkommensteuer an; einbehaltene Beträge kommen über die Steuererklärung zurück. Der Rentenbeitrag von 9,3 % bleibt beim Werkstudentenjob bestehen.

### Brauche ich als Werkstudent Deutsch?
Kommt auf die Branche an. In IT und Forschung arbeiten viele Teams auf Englisch; in kundennahen Bereichen (Vertrieb, HR, Gesundheit) ist Deutsch faktisch Pflicht.

### Führt der Job nach dem Abschluss zur Anstellung?
Die Werkstudentenstelle ist einer der häufigsten Wege in eine Festanstellung: Das Unternehmen kennt dich, du kennst die Abläufe. Den Statuswechsel danach erklären wir im [Zweckwechsel-Artikel](/de/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-de).

## Fazit und ehrlicher Rat

Entscheide nicht nach dem Stundenlohn. Die richtige Frage lautet: **Was soll am Ende des Studiums in deiner Hand sein?** Referenzen und ein akademischer Weg sprechen für HiWi; Unternehmenserfahrung und eine Übernahme sprechen für Werkstudent.

Was du auch wählst: Dokumentiere zwei Dinge — **deine Arbeitstage** und die **vertraglichen Wochenstunden**. Genau danach wird bei der Verlängerung gefragt, und eine Tabelle vom ersten Tag an spart dir später Stunden.

*Beträge und Sätze gelten mit Stand September 2026; Mindestlohn, Minijob-Grenze und Freibeträge werden jährlich angepasst, die Praxis zum Arbeitsrecht unterscheidet sich je nach Stadt. Maßgeblich sind dein Vertrag und die Seite deiner Ausländerbehörde.*
MD;

        $enBody = <<<'MD'
Two words come up when you look for your first job in Germany: **HiWi** and **Werkstudent**. Both are student jobs, both are perfectly legal — but they differ in tax, social insurance, hour limits and what they do for your CV. For international students, the wrong choice can even touch your residence permit.

This article compares them and flags the point that matters most for international students: the **140-day rule**.

## Two jobs, two logics

| | **HiWi** (student assistant) | **Werkstudent** (working student) |
|---|---|---|
| Employer | The university itself (institute, chair, library) | A company |
| Typical work | Tutorials, lab work, research support, marking | Part-time professional work in your field |
| Hours | usually 6–10 per week by contract | capped at **20 per week** during term |
| Pay | set by university and state rules, usually a little above minimum wage | market rate — typically considerably higher |
| Insurance | tied to your student status | the **Werkstudentenprivileg** (below) |
| Career value | academic: references, a route to a doctorate, subject proximity | professional: CV, network, chance of being hired |
| **Effect on the 140-day rule** | as a rule, does **not** count | **counts** |

That last row is the single most important thing here for an international student.

## The 140-day rule and the HiWi exception

A student residence permit (**Section 16b AufenthG**) allows you **140 full or 280 half working days a year**. The law contains a clear exception: **student assistant work at your own university generally does not count towards it.**

In practice: a HiWi job does not consume your 140-day budget. So a HiWi contract plus holiday work at a company leaves you more room than company work alone.

Two cautions:
- The exception covers work at **your own** university; if you work on another institution's project, get your case confirmed by the immigration office.
- Exceeding the limit breaches the conditions of your stay and surfaces at renewal. Keep records: contract, payslips and a list of days worked.

## The Werkstudentenprivileg: this is where the money difference sits

The appeal of working-student status is less about the wage than about the **deductions**. As long as you stay within **20 hours a week** during term:

- **Health insurance: no contribution**
- **Long-term care insurance: no contribution**
- **Unemployment insurance: no contribution**
- **Pension insurance: yes** — the employee share of **9.3%** of gross pay

So far more of your gross stays with you. The privilege depends on student status: if your enrolment ends (see our guide to [de-registration](/en/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-en)) it stops, and your employer must insure you as a regular employee.

**Exceptions to the 20-hour rule:** during semester breaks and for evening, night or weekend shifts the limit can be more flexible — but that is a rule of **social insurance law**. The **140-day limit** for international students runs independently of it. Confusing the two is the most common mistake.

## Money: the 2026 figures

| Item | 2026 value |
|---|---|
| Statutory minimum wage | **€13.90/hour** (€14.60 in 2027) |
| Minijob ceiling | **€603/month** (€7,236 a year) |
| Midijob band | €603.01 – €2,000 |
| Basic tax-free allowance | **€12,348/year** |

Three practical consequences:

1. **A Minijob (up to €603):** no health, care or unemployment contributions; you can apply to be exempted from pension contributions too.
2. **Below €12,348** you get the income tax withheld during the year **back through a tax return.** Many students never file one and simply leave the money behind.
3. **Negotiation:** HiWi rates usually follow a university scale and are barely negotiable; Werkstudent pay is a market matter and runs markedly higher in IT and engineering.

For your tax number and first payslip see our [Steuer-ID guide](/en/blog/how-to-get-a-german-steuer-id-as-a-student-iban-en), and for cover our [insurance comparison](/en/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs-en). The account your salary lands in is covered step by step in our [bank account guide](/en/blog/opening-a-bank-account-in-germany-the-real-obstacles-en).

## Which one fits you?

**Choose HiWi** if you are considering an academic path, a doctorate or research; if contact with teaching staff matters to you; if you need flexible hours that bend around your timetable; if you want to protect your 140-day budget.

**Choose Werkstudent** if you intend to move into industry after graduating; if income is decisive for you; if you want experience of a German workplace and a company name on your CV; if you want to improve your chances of being kept on.

**Best of all, do them in sequence:** HiWi in your first year (easier linguistically and organisationally, flexible hours), then Werkstudent (income and network). A great many students take exactly this route.

## How to find them

**HiWi:** noticeboards at the institute, chair web pages, a direct e-mail to a lecturer. The last works best — a short, clear message to someone whose course you did well in. Write four to six weeks before term starts.

**Werkstudent:** company career pages, StepStone/Indeed, your university career centre, LinkedIn. If the application is requested in German, write in German. In IT, English-language postings are common.

## Frequently asked questions

### Can I do both at once?
Yes, but hours and the 140-day budget are assessed together (the HiWi exception aside). If your combined hours pass 20 per week, you lose the working-student privilege.

### Can I work full-time during semester breaks?
In social insurance terms, usually yes. But the **140-day limit** applies to international students during breaks too — a full-time summer eats much of the budget.

### Does a HiWi contract really not count?
The law provides the exception for student assistant work at your own university. Even so, get your specific case confirmed in writing by the immigration office or your International Office before signing; practice varies between cities.

### How much tax will I pay?
If your annual earnings stay below €12,348, you end up owing no income tax; anything withheld comes back via a tax return. The 9.3% pension contribution still applies to a working-student job.

### Do I need German as a Werkstudent?
It depends on the sector. IT and research teams often work in English; in customer-facing roles (sales, HR, healthcare) German is effectively mandatory.

### Will the job lead to employment after graduation?
A working-student role is one of the most common routes into a permanent job in Germany: the company knows you and you know how it runs. We cover the status change afterwards in our [Zweckwechsel article](/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en).

## Conclusion and honest advice

Do not decide on the hourly rate. The right question is: **what do you want in your hand at graduation?** References and an academic route point to HiWi; company experience and a job offer point to Werkstudent.

Whichever you choose, document two things: **the days you work** and the **weekly hours in your contract**. Those are exactly what you will be asked about at renewal, and keeping a simple table from day one saves hours later.

*The amounts and rates here are current as of September 2026; the minimum wage, Minijob ceiling and tax allowances are adjusted annually, and employment practice varies by city. Your contract and your local immigration office's page are the authoritative sources.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'hiwi-vs-werkstudent-student-jobs-in-germany',
                'title' => 'HiWi mi Werkstudent mi? Almanya\'da Öğrenci İşinin İki Yolu',
                'excerpt' => 'İkisi de öğrenci işi ama vergisi, sigortası ve oturum iznine etkisi farklı: HiWi 140 gün kuralına sayılmazken Werkstudent sayılıyor. Werkstudentenprivileg, 2026 asgari ücret ve Minijob sınırı, hangisi kime uygun ve nasıl bulunur.',
                'meta_title' => 'HiWi mi Werkstudent mi? 140 Gün Kuralı ve Gerçek Kazanç',
                'meta_description' => 'Almanya\'da öğrenci işi: HiWi vs Werkstudent farkı, 140 gün kuralı istisnası, Werkstudentenprivileg, 13,90 € asgari ücret ve 603 € Minijob sınırı (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug' => 'hiwi-vs-werkstudent-student-jobs-in-germany-de',
                'title' => 'HiWi oder Werkstudent? Die zwei Wege zum Studierendenjob',
                'excerpt' => 'Beides sind Studierendenjobs, doch Steuern, Sozialversicherung und Aufenthaltsrecht unterscheiden sich: HiWi-Stellen zählen in der Regel nicht zur 140-Tage-Regel, Werkstudentenjobs schon. Dazu Werkstudentenprivileg, die Zahlen für 2026 und was zu wem passt.',
                'meta_title' => 'HiWi oder Werkstudent? 140-Tage-Regel und echter Verdienst',
                'meta_description' => 'Studierendenjob in Deutschland: HiWi vs. Werkstudent, 140-Tage-Ausnahme, Werkstudentenprivileg, 13,90 € Mindestlohn und 603 € Minijob-Grenze (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug' => 'hiwi-vs-werkstudent-student-jobs-in-germany-en',
                'title' => 'HiWi or Werkstudent? The Two Routes to a Student Job in Germany',
                'excerpt' => 'Both are student jobs, but tax, social insurance and residence law treat them differently: HiWi work generally does not count towards the 140-day rule, while a Werkstudent job does. Plus the working-student privilege, the 2026 figures, and which suits whom.',
                'meta_title' => 'HiWi or Werkstudent? The 140-Day Rule and Real Earnings',
                'meta_description' => 'Student jobs in Germany: HiWi vs Werkstudent, the 140-day exception, the working-student privilege, €13.90 minimum wage and the €603 Minijob ceiling (2026).',
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
            'hiwi-vs-werkstudent-student-jobs-in-germany',
            'hiwi-vs-werkstudent-student-jobs-in-germany-de',
            'hiwi-vs-werkstudent-student-jobs-in-germany-en',
        ])->delete();
    }
};

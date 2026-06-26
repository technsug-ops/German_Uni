<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): What can you do with a CS (Informatik) degree in Germany — job market, salary & staying.
 * Verified: IT is Germany's #1 shortage occupation; 18-month §20 post-grad residence to find a qualified job;
 * Blue Card / §18b switch; Werkstudent→thesis→full-time pipeline; gross salary bands 2025 (junior ~€45–55k,
 * mid ~€60–75k, senior ~€80–100k+), ~35–42% tax. Salary/visa numbers labeled 2025 + hedged (rises yearly).
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'b7d1e2a0-4444-4c8a-9f30-aa01bb02cc04';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Diploma elinde. Peki şimdi ne olacak? Almanya'da bilgisayar mühendisliği (Informatik) okumanın en güçlü yanı tam da bu sorunun cevabında saklı: **IT, Almanya'nın 1 numaralı açık meslek alanı (Engpassberuf).** Yabancı bir öğrenci için hiçbir bölüm bu kadar net bir "mezun ol → iş bul → kal" yolu sunmaz. Bu yazıda iş piyasasını, mezuniyet sonrası vize geçişini, maaş gerçeğini ve Almanca'nın gerçekten ne kadar önemli olduğunu dürüstçe konuşalım.

> Bu yazıdaki tüm maaş ve vize rakamları **2025 itibarıyladır; eşikler her yıl güncellenir, başvurudan önce mutlaka güncel resmi rakamı doğrula.**

## 1. Yabancılar için en güçlü iş piyasası

Açık konuşalım: bir uluslararası öğrenci olarak Almanya'da iş bulma şansın hiçbir bölümde Informatik'teki kadar yüksek değil. Talep gerçek ve birden fazla alanda yayılmış:

- **Software development** (backend, frontend, full-stack) — en büyük havuz.
- **Data / ML / AI** — son yıllarda patlayan, en hızlı büyüyen alan.
- **Cybersecurity** — kronik personel açığı.
- **Cloud / DevOps / Platform** — neredeyse her şirketin aradığı.
- **Embedded / gömülü sistemler** — özellikle **otomotiv** (Bosch, Continental, Mercedes, VW yazılımı). Almanya'nın sanayi belkemiği burada.

IT, Almanya'da **1 numaralı Engpassberuf**. Bu sadece bir slogan değil; vize eşiklerinin IT için neden daha düşük tutulduğunun da sebebi. Detaylı sektör tablosu için → [Almanya'da yabancı olarak IT/tech çalışmak: Mavi Kart ve maaş](/tr/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary).

## 2. Mezuniyet sonrası: 18 ay iş arama izni (§20)

Alman üniversitesinden mezun olduğunda otomatik olarak sokağa atılmıyorsun. Yasa sana **18 aylık oturma izni (§20 AufenthG)** veriyor — bu süre içinde nitelikli (diplomana uygun) bir iş ararsın.

Önemli ayrıntı: bu 18 ay boyunca **geçinmek için herhangi bir işte çalışabilirsin** (garson, market, ne olursa). Kısıtlama yalnızca kalıcı geçişte: nitelikli işi bulduğunda **Mavi Kart (Blaue Karte EU)** veya **§18b nitelikli çalışma iznine** geçersin. Bu geçişin resmi adı **Zweckwechsel** (amaç değişikliği).

Adım adım süreç → [Öğrenci vizesinden çalışma iznine: Zweckwechsel](/tr/blog/changing-student-visa-to-work-permit-germany-zweckwechsel) ve mezuniyet sonrası piyasanın gerçeği → [Mezuniyet sonrası iş piyasası gerçeği](/tr/blog/germany-job-market-reality-after-graduation-for-international-students).

## 3. Werkstudent → şirkette tez → tam zamanlı: 1 numaralı kapı

Almanya'da yabancı bir öğrenci için işe girmenin **en kanıtlanmış yolu** budur ve mezuniyeti beklemez:

1. **Werkstudent ol.** Werkstudent = "çalışan öğrenci": dönem içinde **haftada en fazla 20 saat** bir şirkette çalışırsın (tatillerde tam zamanlı olabilir). Maaşlıdır, sigortalıdır ve CV'ne gerçek deneyim yazar.
2. **Bitirme tezini (Bachelor/Master Thesis) o şirkette yap.** Şirket seni 6 ay boyunca yakından tanır, sen de takımı.
3. **Tam zamanlı teklif** genelde tezin sonunda gelir. Şirket zaten seni biliyor; işe alım riski sıfıra yakın.

Bu pipeline neden bu kadar işe yarar? Çünkü Alman şirketleri "tanımadığı yabancıyı" işe almaktan çekinir; Werkstudent bu güveni önceden inşa eder. İlk Werkstudent pozisyonuna 2-3. sınıfta başvurmaya başla.

## 4. Maaş gerçeği (brüt, 2025)

Rakamlara bakalım — ama **brüt** olduklarını ve Almanya'da gelirin kabaca **%35–42 oranında vergi/sosyal kesintiye** uğradığını unutma. Eline geçen net, brütün yaklaşık %60'ıdır.

| Seviye | Brüt yıllık (2025, kaba) | Not |
|---|---|---|
| Junior (0–2 yıl) | ~**€45.000–55.000** | Genelde Mavi Kart açık-meslek eşiğini rahat geçer |
| Mid (3–5 yıl) | ~**€60.000–75.000** | |
| Senior (6+ yıl) | ~**€80.000–100.000+** | Büyük tech / lead rolleri daha yüksek |

**Şehir farkı önemli:** Münih ve Frankfurt maaşları yukarıda; ama **yaşam maliyeti de orada en yüksek.** Münih'te €70k, Leipzig'de €55k'dan daha rahat geçinmeyebilir. Maaşı her zaman o şehrin kira/yaşam maliyetiyle birlikte oku.

İyi haber: giriş seviyesi IT maaşı bile genelde **Mavi Kart açık-meslek eşiğini (~€43.759,80/yıl, 2025; yeni mezunlar için)** rahat aşar. Yani vize tarafında IT mezunu için işler kolaydır.

## 5. Almanca gerçekten gerekli mi?

Dürüst cevap: **kod yazarken bile Almanca işine yarar** — ama nerede çalışacağına bağlı.

- **Saf İngilizce roller** Berlin startup'larında ve büyük tech'te yoğunlaşır (SAP, Zalando, N26, Trade Republic, Delivery Hero). Buralarda günlük dil İngilizce olabilir.
- **Mittelstand (orta ölçekli Alman firmaları) ve otomotiv** çoğunlukla **Almanca ister** — toplantılar, dokümantasyon, müşteriler Almanca.
- B1–B2 Almanca senin için **kapıları katlar**: hem iş seçeneğini genişletir hem de Mavi Kart → daimi oturum süreni kısaltır (B1 ile 27 ay yerine **21 ayda** Niederlassungserlaubnis).

Dilin işe etkisinin dürüst dökümü → [İş için Almanca gerçeği](/tr/blog/german-language-reality-for-jobs-in-germany-the-honest-truth).

## 6. Nerede çalışmalı? Startup vs Mittelstand vs kurumsal

| Tip | Örnek | Artı | Eksi |
|---|---|---|---|
| **Berlin startup'ları** | N26, Trade Republic, Zalando | İngilizce çalışılır, modern stack, hızlı yükselme | Daha az iş güvencesi, kaotik olabilir |
| **Mittelstand** | Adı duyulmamış ama sağlam Alman firmaları | İş güvencesi, ciddi mühendislik | Genelde Almanca şart, daha yavaş |
| **Büyük kurumsal** | SAP, Siemens, Bosch, otomotiv | Stabil, iyi maaş/yan haklar, eğitim | Bürokrasi, yavaş süreç |

Tek "doğru" yok — yeni mezun olarak Berlin'in İngilizce startup'ları başlamak için en kolay kapıdır; ama uzun vadede Mittelstand/otomotivin güvencesi ve Almanca'yı öğrendikçe açılan kapılar da çok değerli.

## Sonuç: Dürüst tavsiye

Informatik, Almanya'da yabancı bir öğrenci için **kalmak isteyenlere en net yolu sunan bölümdür.** Talep gerçek, vize eşikleri IT lehine düşük, ve §20 ile 18 aylık güvenli bir iş arama penceresi var. Strateji nettir: **2-3. sınıfta Werkstudent ol → tezini bir şirkette yap → tam zamanlıya geç → Mavi Kart'a Zweckwechsel yap.** Almanca'yı (en az B1) yan yana öğren; hem iş havuzunu büyütür hem daimi oturumu hızlandırır.

Bütünü tamamlamak için: [yabancı olarak Informatik okumak](/tr/blog/studying-computer-science-informatik-in-germany-as-a-foreigner), [Almancasız İngilizce IT bölümleri](/tr/blog/english-taught-computer-science-it-degrees-in-germany-without-german), [IT'de çalışmak: Mavi Kart ve maaş](/tr/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary). Vize tarafı için: [iş teklifiyle çalışma vizesi](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) ve [yüksek lisans mı iş arama vizesi mi](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career).

*Bu içerik 2026 başı itibarıyla hazırlanmıştır. Maaş bantları, vergi oranları ve Mavi Kart / §20 vize eşikleri her yıl güncellenir; karar vermeden önce güncel resmi rakamları doğrula.*
MD;

        $deBody = <<<'MD'
Diplom in der Hand. Und jetzt? Genau hier liegt die größte Stärke eines Informatik-Studiums in Deutschland: **IT ist der Engpassberuf Nummer 1 in Deutschland.** Für internationale Studierende bietet kein anderes Fach einen so klaren Weg von „Abschluss → Job → Bleiben". In diesem Beitrag reden wir ehrlich über den Arbeitsmarkt, den Visumswechsel nach dem Abschluss, die Gehaltsrealität und darüber, wie wichtig Deutsch wirklich ist.

> Alle Gehalts- und Visumszahlen hier gelten **Stand 2025; die Schwellen werden jährlich angepasst — prüfe vor jeder Bewerbung die aktuelle offizielle Zahl.**

## 1. Der stärkste Arbeitsmarkt für Internationale

Klartext: Als internationale:r Studierende:r ist deine Jobchance in Deutschland in keinem Fach so hoch wie in der Informatik. Die Nachfrage ist real und verteilt sich über mehrere Felder:

- **Softwareentwicklung** (Backend, Frontend, Full-Stack) — der größte Pool.
- **Data / ML / AI** — das am schnellsten wachsende Feld der letzten Jahre.
- **Cybersecurity** — chronischer Personalmangel.
- **Cloud / DevOps / Platform** — fast jedes Unternehmen sucht hier.
- **Embedded Systems** — besonders **Automotive** (Bosch, Continental, Mercedes, VW-Software). Das industrielle Rückgrat Deutschlands.

IT ist der **Engpassberuf Nr. 1** in Deutschland. Das ist kein Slogan, sondern auch der Grund, warum die Visumsschwellen für IT niedriger angesetzt sind. Detaillierte Branchentabelle → [Als Ausländer:in in IT/Tech in Deutschland arbeiten: Blue Card & Gehalt](/de/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-de).

## 2. Nach dem Abschluss: 18 Monate Jobsuche (§20)

Mit dem Abschluss einer deutschen Hochschule wirst du nicht auf die Straße gesetzt. Das Gesetz gibt dir eine **18-monatige Aufenthaltserlaubnis (§20 AufenthG)** — in dieser Zeit suchst du einen qualifizierten (deiner Qualifikation entsprechenden) Job.

Wichtiges Detail: In diesen 18 Monaten darfst du **zum Lebensunterhalt jeden beliebigen Job** annehmen (Kellnern, Supermarkt, was auch immer). Die Einschränkung gilt nur beim dauerhaften Wechsel: Sobald du den qualifizierten Job hast, wechselst du zur **Blauen Karte EU** oder zur **§18b-Aufenthaltserlaubnis**. Der offizielle Name dieses Wechsels ist **Zweckwechsel**.

Schritt für Schritt → [Vom Studentenvisum zur Arbeitserlaubnis: Zweckwechsel](/de/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-de) und die Realität des Marktes → [Arbeitsmarkt-Realität nach dem Abschluss](/de/blog/germany-job-market-reality-after-graduation-for-international-students-de).

## 3. Werkstudent → Thesis im Unternehmen → Festanstellung

Das ist der **bewährteste Weg** für internationale Studierende in Deutschland — und er wartet nicht auf den Abschluss:

1. **Werde Werkstudent:in.** Werkstudent = „arbeitende:r Student:in": während des Semesters arbeitest du **höchstens 20 Stunden/Woche** in einem Unternehmen (in den Semesterferien Vollzeit). Bezahlt, versichert, echte Erfahrung im Lebenslauf.
2. **Schreibe deine Abschlussarbeit (Bachelor-/Masterthesis) in diesem Unternehmen.** Die Firma lernt dich über 6 Monate genau kennen, und du das Team.
3. **Das Festangebot** kommt meist am Ende der Thesis. Das Unternehmen kennt dich bereits; das Einstellungsrisiko ist nahezu null.

Warum funktioniert diese Pipeline so gut? Weil deutsche Firmen zögern, „unbekannte Ausländer" einzustellen; der Werkstudentenjob baut dieses Vertrauen im Voraus auf. Bewirb dich ab dem 3.–4. Semester auf deine erste Werkstudentenstelle.

## 4. Gehaltsrealität (brutto, 2025)

Schauen wir auf die Zahlen — aber denk daran, dass sie **brutto** sind und das Einkommen in Deutschland grob **35–42 % an Steuern/Sozialabgaben** verliert. Netto bleibt rund 60 % des Bruttos.

| Stufe | Brutto/Jahr (2025, grob) | Hinweis |
|---|---|---|
| Junior (0–2 Jahre) | ~**45.000–55.000 €** | Übersteigt meist locker die Blue-Card-Engpassschwelle |
| Mid (3–5 Jahre) | ~**60.000–75.000 €** | |
| Senior (6+ Jahre) | ~**80.000–100.000+ €** | Big Tech / Lead-Rollen höher |

**Der Stadtunterschied zählt:** München und Frankfurt zahlen höher — aber dort sind auch die **Lebenshaltungskosten am höchsten.** 70.000 € in München reichen evtl. nicht weiter als 55.000 € in Leipzig. Lies das Gehalt immer zusammen mit Miete und Lebenshaltungskosten der Stadt.

Gute Nachricht: Selbst ein Einstiegsgehalt in der IT übersteigt meist die **Blue-Card-Engpassschwelle (~43.759,80 €/Jahr, 2025; für Berufsanfänger)** locker. Visumstechnisch ist es für IT-Absolventen also einfach.

## 5. Braucht man wirklich Deutsch?

Ehrliche Antwort: **Selbst beim Coden hilft dir Deutsch** — aber es hängt davon ab, wo du arbeitest.

- **Reine Englisch-Rollen** konzentrieren sich auf Berliner Startups und Big Tech (SAP, Zalando, N26, Trade Republic, Delivery Hero). Dort kann die Alltagssprache Englisch sein.
- **Mittelstand und Automotive** verlangen meist **Deutsch** — Meetings, Doku, Kunden auf Deutsch.
- B1–B2 Deutsch **vervielfacht deine Türen**: erweitert die Jobauswahl und verkürzt deinen Weg von der Blue Card zur Niederlassungserlaubnis (mit B1 in **21 statt 27 Monaten**).

Ehrliche Aufschlüsselung zum Spracheinfluss → [Deutsch-Realität für Jobs](/de/blog/german-language-reality-for-jobs-in-germany-the-honest-truth-de).

## 6. Wo arbeiten? Startup vs Mittelstand vs Konzern

| Typ | Beispiel | Plus | Minus |
|---|---|---|---|
| **Berliner Startups** | N26, Trade Republic, Zalando | Englisch, moderner Stack, schneller Aufstieg | Weniger Sicherheit, kann chaotisch sein |
| **Mittelstand** | Unbekannte, aber solide deutsche Firmen | Jobsicherheit, ernsthaftes Engineering | Meist Deutsch nötig, langsamer |
| **Großkonzern** | SAP, Siemens, Bosch, Automotive | Stabil, gutes Gehalt/Benefits, Weiterbildung | Bürokratie, langsame Prozesse |

Es gibt kein einziges „Richtig" — als frische:r Absolvent:in sind Berlins englischsprachige Startups der leichteste Einstieg; langfristig sind aber die Sicherheit des Mittelstands/der Automotive und die Türen, die sich mit besserem Deutsch öffnen, sehr wertvoll.

## Fazit: Ehrlicher Rat

Informatik ist für internationale Studierende **das Fach mit dem klarsten Weg zum Bleiben** in Deutschland. Die Nachfrage ist real, die Visumsschwellen sind IT-freundlich, und §20 gibt dir ein sicheres 18-Monats-Fenster zur Jobsuche. Die Strategie ist klar: **Werde im 3.–4. Semester Werkstudent:in → schreibe deine Thesis in einem Unternehmen → wechsle in die Festanstellung → mache den Zweckwechsel zur Blue Card.** Lerne nebenbei Deutsch (mindestens B1); das vergrößert deinen Jobpool und beschleunigt die Niederlassungserlaubnis.

Zum Vervollständigen: [Informatik als Ausländer:in studieren](/de/blog/studying-computer-science-informatik-in-germany-as-a-foreigner-de), [Englischsprachige IT-Studiengänge ohne Deutsch](/de/blog/english-taught-computer-science-it-degrees-in-germany-without-german-de), [In IT arbeiten: Blue Card & Gehalt](/de/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-de). Zum Visum: [Arbeitsvisum mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de) und [Master vs. Jobsuche-Visum](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).

*Dieser Beitrag wurde Anfang 2026 erstellt. Gehaltsbänder, Steuersätze und Blue-Card-/§20-Schwellen werden jährlich aktualisiert; prüfe vor einer Entscheidung die aktuellen offiziellen Zahlen.*
MD;

        $enBody = <<<'MD'
Degree in hand. Now what? This is exactly where studying Computer Science (Informatik) in Germany shows its biggest strength: **IT is Germany's #1 shortage occupation (Engpassberuf).** For an international student, no other field offers such a clear path from "graduate → get a job → stay." In this post let's talk honestly about the job market, the post-graduation visa switch, the salary reality, and how much German actually matters.

> All salary and visa figures here are **as of 2025; thresholds are updated yearly — verify the current official number before any application.**

## 1. The strongest job market of any field for internationals

Straight talk: as an international student, your odds of landing a job in Germany are higher in Informatik than in any other field. The demand is real and spread across several areas:

- **Software development** (backend, frontend, full-stack) — the biggest pool.
- **Data / ML / AI** — the fastest-growing area of recent years.
- **Cybersecurity** — chronic staff shortage.
- **Cloud / DevOps / Platform** — almost every company is hiring here.
- **Embedded systems** — especially **automotive** (Bosch, Continental, Mercedes, VW software). Germany's industrial backbone lives here.

IT is the **#1 Engpassberuf** in Germany. That's not just a slogan; it's also why visa thresholds are set lower for IT. For a detailed sector breakdown → [Working in IT/tech in Germany as a foreigner: Blue Card & salary](/en/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-en).

## 2. After graduation: 18 months to find a job (§20)

Finishing a German degree doesn't put you on the next flight home. The law gives you an **18-month residence permit (§20 AufenthG)** — time to find a qualified job (one matching your degree).

Key detail: during those 18 months you may **take any job to support yourself** (waiting tables, supermarket, whatever). The restriction only applies to the permanent switch: once you land the qualified job, you move to the **EU Blue Card (Blaue Karte EU)** or the **§18b skilled-work permit**. The official name of this switch is **Zweckwechsel** (change of purpose).

Step by step → [Switching from a student visa to a work permit: Zweckwechsel](/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en) and the market reality → [Job-market reality after graduation](/en/blog/germany-job-market-reality-after-graduation-for-international-students-en).

## 3. Werkstudent → thesis at a company → full-time

This is the **most proven route** for international students in Germany — and it doesn't wait for graduation:

1. **Become a Werkstudent.** Werkstudent = "working student": during the semester you work **at most 20 hours/week** at a company (full-time during semester breaks). It's paid, insured, and puts real experience on your CV.
2. **Do your thesis (Bachelor/Master) at that company.** The company gets to know you closely over ~6 months, and you get to know the team.
3. **The full-time offer** usually comes at the end of the thesis. The company already knows you; the hiring risk is near zero.

Why does this pipeline work so well? Because German firms are wary of hiring an "unknown foreigner"; the Werkstudent role builds that trust in advance. Start applying for your first Werkstudent position in your 2nd–3rd year.

## 4. Salary reality (gross, 2025)

Let's look at the numbers — but remember they are **gross**, and income in Germany loses roughly **35–42% to taxes/social contributions**. Net take-home is about 60% of gross.

| Level | Gross/year (2025, rough) | Note |
|---|---|---|
| Junior (0–2 yrs) | ~**€45,000–55,000** | Usually clears the Blue Card shortage threshold comfortably |
| Mid (3–5 yrs) | ~**€60,000–75,000** | |
| Senior (6+ yrs) | ~**€80,000–100,000+** | Big tech / lead roles higher |

**The city matters:** Munich and Frankfurt pay more — but they also have the **highest cost of living.** €70k in Munich may not stretch further than €55k in Leipzig. Always read salary together with that city's rent and living costs.

Good news: even an entry-level IT salary usually clears the **Blue Card shortage threshold (~€43,759.80/yr, 2025; for recent grads)** with room to spare. So on the visa side, things are easy for IT graduates.

## 5. Does German actually matter for CS?

Honest answer: **even devs benefit from German** — but it depends on where you work.

- **Pure-English roles** concentrate in Berlin startups and big tech (SAP, Zalando, N26, Trade Republic, Delivery Hero). There the working language may be English.
- **Mittelstand (mid-sized German firms) and automotive** usually require **German** — meetings, documentation, clients in German.
- B1–B2 German **multiplies your doors**: it widens job options and shortens your path from Blue Card to permanent residence (with B1, **21 months instead of 27** for the Niederlassungserlaubnis).

The honest breakdown of language's effect on jobs → [German-language reality for jobs](/en/blog/german-language-reality-for-jobs-in-germany-the-honest-truth-en).

## 6. Where to work? Startup vs Mittelstand vs corporate

| Type | Example | Plus | Minus |
|---|---|---|---|
| **Berlin startups** | N26, Trade Republic, Zalando | English-speaking, modern stack, fast growth | Less security, can be chaotic |
| **Mittelstand** | Unknown but solid German firms | Job security, serious engineering | Usually needs German, slower |
| **Large corporates** | SAP, Siemens, Bosch, automotive | Stable, good pay/benefits, training | Bureaucracy, slow processes |

There's no single "right" — as a fresh grad, Berlin's English-speaking startups are the easiest door to get in; but long-term, the security of the Mittelstand/automotive and the doors that open as your German improves are very valuable.

## Bottom line: honest advice

Informatik is **the field with the clearest path to staying** in Germany for an international student. The demand is real, visa thresholds favor IT, and §20 gives you a safe 18-month window to find a job. The strategy is clear: **become a Werkstudent in your 2nd–3rd year → do your thesis at a company → convert to full-time → do the Zweckwechsel to a Blue Card.** Learn German (at least B1) alongside; it grows your job pool and speeds up permanent residence.

To complete the picture: [studying Informatik as a foreigner](/en/blog/studying-computer-science-informatik-in-germany-as-a-foreigner-en), [English-taught IT degrees without German](/en/blog/english-taught-computer-science-it-degrees-in-germany-without-german-en), [working in IT: Blue Card & salary](/en/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-en). For the visa side: [work visa with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en) and [master's vs job-seeker visa](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

*This content was prepared in early 2026. Salary bands, tax rates, and Blue Card / §20 visa thresholds are updated yearly; verify the current official figures before deciding.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'what-to-do-with-a-computer-science-degree-in-germany-job-market-salary',
                'title' => 'Almanya\'da bilgisayar mühendisliği diplomasıyla ne yapabilirsin: iş piyasası, maaş ve kalmak',
                'excerpt' => 'IT, Almanya\'nın 1 numaralı açık meslek alanı. Mezuniyet sonrası 18 aylık iş arama izni (§20), Werkstudent → tez → tam zamanlı yolu, 2025 maaş bantları ve Almanca\'nın gerçek etkisi.',
                'meta_title' => 'CS diplomasıyla Almanya\'da iş, maaş ve kalmak',
                'meta_description' => 'Almanya\'da Informatik mezunu için iş piyasası, §20 18 ay iş arama izni, Werkstudent pipeline\'ı, 2025 maaş bantları ve Almanca\'nın önemi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'what-to-do-with-a-computer-science-degree-in-germany-job-market-salary-de',
                'title' => 'Was du mit einem Informatik-Abschluss in Deutschland machen kannst: Arbeitsmarkt, Gehalt & Bleiben',
                'excerpt' => 'IT ist der Engpassberuf Nr. 1 in Deutschland. 18 Monate Jobsuche nach dem Abschluss (§20), der Werkstudent → Thesis → Festanstellung-Weg, Gehaltsbänder 2025 und wie wichtig Deutsch wirklich ist.',
                'meta_title' => 'Informatik-Abschluss in Deutschland: Job, Gehalt, Bleiben',
                'meta_description' => 'Arbeitsmarkt für Informatik-Absolventen in Deutschland, §20 18-Monate-Jobsuche, Werkstudent-Pipeline, Gehaltsbänder 2025 und die Rolle von Deutsch.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'what-to-do-with-a-computer-science-degree-in-germany-job-market-salary-en',
                'title' => 'What can you do with a CS degree in Germany: job market, salary & staying',
                'excerpt' => 'IT is Germany\'s #1 shortage occupation. The 18-month post-grad job-search residence (§20), the Werkstudent → thesis → full-time route, 2025 salary bands, and how much German really matters.',
                'meta_title' => 'CS degree in Germany: job market, salary & staying',
                'meta_description' => 'Job market for Informatik grads in Germany, §20 18-month job search, the Werkstudent pipeline, 2025 salary bands, and how much German matters.',
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
            'what-to-do-with-a-computer-science-degree-in-germany-job-market-salary',
            'what-to-do-with-a-computer-science-degree-in-germany-job-market-salary-de',
            'what-to-do-with-a-computer-science-degree-in-germany-job-market-salary-en',
        ])->delete();
    }
};

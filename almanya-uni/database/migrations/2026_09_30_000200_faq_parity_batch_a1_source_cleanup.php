<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * FAQ Parity Batch A1 — chatbot kaynak temizliği (Batch A dışındaki 3 eski kaynak, 8 kayıt):
 *  - blog student-work-permit-in-germany-2026-20-hour-rule-and-types EN/DE: Batch 2C'nin TR düzeltmesinin karşılığı
 *    (20 saat = Werkstudent sosyal sigorta kuralı, oturum sınırı değil; "20 saati aşmak yasal değil → Aufenthaltstitel
 *    iptali" kaldırıldı; 140 Arbeitstage, ≤4 saat = yarım gün, ders döneminde ≤20 saatlik hafta = 2,5 gün, lehe hesap);
 *  - SSS werkstudent-saat-siniri-nasil-kontrol-ediliyor TR/EN/DE: 131 €/ay, vize iptali, şirket cezası, vergi denetimi,
 *    "otomatik tarife" iddiaları kaldırıldı; öğrenci primi sabit tutar olmadan (baz tutar + Zusatzbeitrag + Pflege);
 *  - SSS mini-job-538eur-siniri-nedir TR/EN/DE: çalışan emeklilik payı %3,6 + muafiyet başvurusu; işverenin %13 sağlık
 *    toplu primi kişiye sigorta güvencesi sağlamaz; Grundfreibetrag 2026 = 12.348 €; 603 € (2026), 538 € yalnız geçmiş.
 *
 * Kaynaklar (28.09.2026): Minijob-Zentrale (Rentenversicherungspflicht; Befreiung; Versicherungen; Abgaben und Steuern),
 * TK (Werkstudent 20 saat / 26 hafta), § 16b Abs. 3 AufenthG, BMF (Grundfreibetrag 2026).
 *
 * Yöntem (her kayıt kararlı kimlikle: posts/faqs = slug+locale, varlıklar = slug):
 *  - md satırları: markdown'dan arındırılmış düz metni canlı sayfadaki blokla BİREBİR eşleşen satır değişir
 *    (liste/başlık/alıntı öneki korunur);
 *  - alanlar (title/question): eski değer birebir doğrulanır;
 *  - JSON content_blocks: eski alt dize içeren tüm metin değerlerinde birebir alt dize değişimi.
 * Ön kontrol yazmadan önce tüm kayıtlarda yapılır; kayıt başına durum "tamamı bekleyen" ya da "tamamı uygulanmış"
 * olmalı; eksik kayıt / eşleşmeyen blok / kısmi durum → RuntimeException, hiçbir şey yazılmaz. Tek transaction.
 * İkinci çalıştırma no-op. PHPUnit altında (boş test DB'si) sorun varsa sessizce çıkar.
 */
return new class extends Migration
{
    public function up(): void
    {
        $spec = json_decode(<<<'JSON'
{
 "records": [
  {
   "table": "posts",
   "slug": "student-work-permit-in-germany-2026-20-hour-rule-and-types-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "The work permit for international students in Germany is generally determined together with their Aufenthaltstitel. The most well-known and frequently asked about rule is the weekly 20-hour rule. This rule states that students can work a maximum of 20 hours per week during the semester (when university classes are in session). The purpose of this limit is to ensure that students' primary focus remains on their studies and education.",
      "replace": "The work permit for international students in Germany is generally determined together with their residence permit (Aufenthaltstitel). The most frequently asked-about topic is the weekly **20-hour rule** — but it is often confused with the residence-permit limit. Under residence law (§ 16b AufenthG), since 01.03.2024 the limit is not a weekly hour cap but an annual account of **140 working days** (Arbeitstagekonto). The weekly 20 hours are the **social-insurance condition for Werkstudent status** during the lecture period; in addition, a lecture-period week with no more than 20 hours can count as 2.5 working days in the 140-day account."
     },
     {
      "line": "Annual Work Limit: The weekly 20-hour rule covers work during the semester. Throughout the year, students have the right to work a total of up to 140 working days (Arbeitstagekonto; raised from 120 in 2024). A day of up to 4 hours counts as half a day. This means that students can work full-time (Vollzeit) for more than 20 hours a week during semesterferien. For example, you can use this annual allowance by working full-time for 2 months during the summer break.",
      "replace": "**Annual Work Limit:** The residence-law limit is an annual account of **140 working days** (Arbeitstagekonto; raised from 120 in 2024). A day of up to 4 hours counts as half a day. Alternatively, a weekly count can be used: during the lecture period, a week with no more than 20 hours counts as 2.5 working days, and outside the lecture period every week counts as 2.5 working days; for each week, the calculation that is more favourable to you applies. During semester breaks (Semesterferien) you can work full-time (Vollzeit); these weeks also count towards the annual account. Detailed counting examples: [the 140-day work account](/en/blog/internship-in-germany-with-b1-b2-german-en)."
     },
     {
      "line": "During Semester and Holiday Periods: Exceeding 20 hours per week during the semester is not legal and can lead to serious problems, including the cancellation of your Aufenthaltstitel. During holiday periods, this limit is lifted, and you can work more within your annual allowance.",
      "replace": "**During Semester and Holiday Periods:** Residence law has no separate weekly hour cap for the lecture period; what counts is the 140-working-day account. If you work more than 20 hours in a lecture-period week, the 2.5-day weekly count cannot be used for that week; every day you work is then deducted as a full or half day. If you are a Werkstudent, you may also lose the student social-insurance advantage. Exceeding the 140 working days can cause serious problems with your residence permit."
     },
     {
      "line": "Relationship with the 20-Hour Rule: A Minijob is subject to the weekly 20-hour rule. This means that even if you have a Minijob, your total working time during the semester should not exceed 20 hours per week. However, if you have more than one Minijob, the total of all your Minijob incomes must not exceed the monthly upper limit (603 Euro in 2026), and your total working time must not exceed 20 hours per week.",
      "replace": "**Relationship with the 20-Hour Rule:** There is no statutory weekly hour cap for a Minijob; what matters is the monthly earnings limit (603 Euro in 2026; if you have more than one Minijob, your total income must not exceed this limit). Under residence law, the working days in all your jobs, including a Minijob, count towards your annual 140-working-day account. If you combine a Minijob with a Werkstudent job, keep an eye on your total weekly working time during the lecture period for the Werkstudent social-insurance advantage."
     },
     {
      "line": "20-Hour Rule: The weekly 20-hour rule must also be observed in Werkstudent status during the semester. During holiday periods, this limit is lifted, and you can work full-time.",
      "replace": "**20-Hour Rule:** As a Werkstudent, working no more than 20 hours a week during the lecture period is the condition for keeping the student social-insurance advantage; it is not a residence-permit rule. During holiday periods this limit does not apply and you can work full-time; these weeks still count towards the 140-working-day account."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "student-work-permit-in-germany-2026-20-hour-rule-and-types-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Die Arbeitserlaubnis für internationale Studierende in Deutschland wird in der Regel zusammen mit ihrem Aufenthaltstitel festgelegt. Die bekannteste und am häufigsten nachgefragte Regel ist die wöchentliche 20-Stunden-Regel. Diese Regel besagt, dass Studierende während des Semesters (wenn die Universitätskurse stattfinden) maximal 20 Stunden pro Woche arbeiten dürfen. Ziel dieser Begrenzung ist es, sicherzustellen, dass sich die Studierenden hauptsächlich auf ihr Studium und ihre Ausbildung konzentrieren.",
      "replace": "Die Arbeitserlaubnis für internationale Studierende in Deutschland wird in der Regel zusammen mit ihrem Aufenthaltstitel festgelegt. Am häufigsten gefragt wird nach der wöchentlichen **20-Stunden-Regel** – sie wird aber oft mit der aufenthaltsrechtlichen Grenze verwechselt. Aufenthaltsrechtlich (§ 16b AufenthG) gilt seit dem 01.03.2024 keine wöchentliche Stundenobergrenze, sondern ein Jahreskonto von **140 Arbeitstagen** (Arbeitstagekonto). Die 20 Wochenstunden sind in der Vorlesungszeit die **sozialversicherungsrechtliche Voraussetzung für den Werkstudentenstatus**; außerdem kann eine Woche in der Vorlesungszeit mit höchstens 20 Stunden im 140-Tage-Konto als 2,5 Arbeitstage zählen."
     },
     {
      "line": "Jährliche Arbeitszeitbegrenzung: Die wöchentliche 20-Stunden-Regel gilt für die Arbeit während des Semesters. Über das Jahr verteilt haben Studierende das Recht, insgesamt 140 volle Tage oder 280 halbe Tage zu arbeiten (Änderung 2024 – zuvor 120/240). Ein Tag mit bis zu 4 Stunden zählt als halber Tag (Arbeitstagekonto). Dies bedeutet, dass Studierende in den Semesterferien mehr als 20 Stunden pro Woche, also Vollzeit, arbeiten können. Zum Beispiel kannst du diese jährliche Arbeitszeit nutzen, indem du während der Sommerferien 2 Monate lang Vollzeit arbeitest.",
      "replace": "**Jährliche Arbeitszeitbegrenzung:** Die aufenthaltsrechtliche Grenze ist ein Jahreskonto von **140 Arbeitstagen** (Arbeitstagekonto; 2024 von 120 auf 140 erhöht). Ein Tag mit bis zu 4 Stunden zählt als halber Tag. Alternativ ist eine wöchentliche Zählung möglich: In der Vorlesungszeit zählt eine Woche mit höchstens 20 Stunden als 2,5 Arbeitstage, außerhalb der Vorlesungszeit zählt jede Woche als 2,5 Arbeitstage; für jede Woche gilt die für dich günstigere Berechnung. In den Semesterferien kannst du Vollzeit arbeiten; auch diese Wochen zählen zum Jahreskonto. Ausführliche Rechenbeispiele: [das 140-Tage-Arbeitskonto](/de/blog/internship-in-germany-with-b1-b2-german-de)."
     },
     {
      "line": "Während des Semesters und in den Ferien: Das Überschreiten von 20 Stunden pro Woche während des Semesters ist nicht legal und kann zu ernsthaften Problemen führen, die bis zur Aufhebung deines Aufenthaltstitels reichen können. In den Ferienzeiten entfällt diese Begrenzung, und du kannst im Rahmen deiner jährlichen Arbeitszeit mehr arbeiten.",
      "replace": "**Während des Semesters und in den Ferien:** Aufenthaltsrechtlich gibt es für die Vorlesungszeit keine eigene wöchentliche Stundenobergrenze; maßgeblich ist das Konto von 140 Arbeitstagen. Arbeitest du in einer Woche der Vorlesungszeit mehr als 20 Stunden, kann für diese Woche die wöchentliche 2,5-Tage-Zählung nicht genutzt werden; jeder Arbeitstag wird dann als ganzer oder halber Tag abgezogen. Als Werkstudent kannst du außerdem den studentischen Vorteil in der Sozialversicherung verlieren. Wer die 140 Arbeitstage überschreitet, riskiert ernsthafte Probleme mit dem Aufenthaltstitel."
     },
     {
      "line": "Beziehung zur 20-Stunden-Regel: Ein Minijob unterliegt der wöchentlichen 20-Stunden-Regel. Das bedeutet, selbst wenn du einen Minijob hast, darf deine gesamte Arbeitszeit während des Semesters 20 Stunden pro Woche nicht überschreiten. Wenn du jedoch mehrere Minijobs hast, darf die Summe aller Minijob-Einkommen die monatliche Obergrenze (2026: 603 Euro) nicht überschreiten, und deine gesamte Arbeitszeit darf 20 Stunden pro Woche nicht überschreiten.",
      "replace": "**Beziehung zur 20-Stunden-Regel:** Für einen Minijob gibt es keine gesetzliche wöchentliche Stundenobergrenze; entscheidend ist die monatliche Verdienstgrenze (2026: 603 Euro; bei mehreren Minijobs darf das Gesamteinkommen diese Grenze nicht überschreiten). Aufenthaltsrechtlich zählen die Arbeitstage aus allen Jobs, auch aus einem Minijob, zu deinem Jahreskonto von 140 Arbeitstagen. Wenn du einen Minijob mit einem Werkstudentenjob kombinierst, achte in der Vorlesungszeit wegen des Werkstudentenvorteils in der Sozialversicherung auf deine gesamte Wochenarbeitszeit."
     },
     {
      "line": "20-Stunden-Regel: Auch im Werkstudentenstatus muss die wöchentliche 20-Stunden-Regel während des Semesters eingehalten werden. In den Ferienzeiten entfällt diese Begrenzung, und du kannst Vollzeit arbeiten.",
      "replace": "**20-Stunden-Regel:** Als Werkstudent ist es Voraussetzung für den studentischen Vorteil in der Sozialversicherung, in der Vorlesungszeit höchstens 20 Stunden pro Woche zu arbeiten; es ist keine aufenthaltsrechtliche Regel. In den Ferienzeiten gilt diese Grenze nicht, und du kannst Vollzeit arbeiten; diese Wochen zählen aber zum Konto von 140 Arbeitstagen."
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "werkstudent-saat-siniri-nasil-kontrol-ediliyor",
   "locale": "tr",
   "cluster": "werkstudent-saat-siniri-nasil-kontrol-ediliyor",
   "rewrite": {
    "answer_md": {
     "fingerprints": [
      "Direkt günlük takip sistemi yok ama dolaylı kanıtlar var",
      "131 €/ay",
      "Geriye dönük 1 yıl prim isteme hakkı var"
     ],
     "new": "Werkstudent'ın haftalık 20 saat sınırını günlük izleyen merkezi bir sistem yok. Bu sınır bir **sosyal sigorta kuralı**dır: ders döneminde düzenli olarak haftada en fazla 20 saat çalışırsan (birden fazla işverende çalışıyorsan saatler toplanır) sağlık, bakım ve işsizlik sigortasından muaf olursun; yalnızca emeklilik sigortası payın kesilir. Akşam, gece, hafta sonu ya da dönem tatilindeki çalışmalarla 20 saat yılda en fazla 26 hafta aşılabilir.\n\n### Kim neye bakar?\n- **İşveren:** Seni sosyal sigortaya bildirir ve Werkstudent statüsünü sözleşmene ve çalışma saatlerine göre değerlendirir. Saatler tüm işlerde toplandığı için işverenlerini diğer işlerinden haberdar et.\n- **Krankenkasse:** Sigorta durumunla ilgili sorularda başvuracağın yerdir. Werkstudent koşulları sağlanmıyorsa normal çalışan gibi sigortalanırsın.\n- **Ausländerbehörde:** Oturum hukukunda sınır haftalık saat değil, yılda **140 iş günü**dür (§ 16b AufenthG, Arbeitstagekonto). 4 saate kadar çalışılan gün yarım gün sayılır; ders döneminde en fazla 20 saat çalışılan bir hafta 2,5 iş günü sayılabilir ve her hafta için senin lehine olan hesap uygulanır. Uzatmada çalışma durumunla ilgili belge istenebilir; ayrıntıyı kendi Ausländerbehörde'nle netleştir.\n\n### 20 saati aşarsan ne olur?\nWerkstudent statüsünün sosyal sigorta avantajını kaybedebilirsin; o zaman sağlık, bakım ve işsizlik sigortası primleri normal çalışan gibi hesaplanır. Oturum açısından belirleyici olan ise 140 iş günlük hesaptır; haftalık 20 saat tek başına bir oturum sınırı değildir.\n\n### Öğrenci sağlık sigortası ne kadar?\nHerkes için geçerli tek bir sabit tutar yok. Öğrenci primi yasal bir baz tutar üzerinden hesaplanır; buna Krankenkasse'nin ek primi (Zusatzbeitrag) ve bakım sigortası primi eklenir. Tutar kasaya ve kişisel duruma (örneğin yaş, çocuk) göre değişir; güncel tutarı kendi Krankenkasse'nden öğren.\n\n### Pratik öneriler\n- Çalıştığın günleri ve saatleri kendin kaydet; bordrolarını (Lohnabrechnung) sakla.\n- Birden fazla işin varsa toplam haftalık saatini ve 140 günlük hesabını birlikte izle.\n- Emin değilsen işverenine ve Krankenkasse'ne sor.\n\nAyrıntılar: [HiWi mi Werkstudent mi?](/tr/blog/hiwi-vs-werkstudent-student-jobs-in-germany) · [140 günlük çalışma hesabı](/tr/blog/internship-in-germany-with-b1-b2-german)\n"
    }
   }
  },
  {
   "table": "faqs",
   "slug": "werkstudent-saat-siniri-nasil-kontrol-ediliyor-en",
   "locale": "en",
   "cluster": "werkstudent-saat-siniri-nasil-kontrol-ediliyor",
   "rewrite": {
    "answer_md": {
     "fingerprints": [
      "131 €/month",
      "risk of visa cancellation",
      "company penalty"
     ],
     "new": "There is no central system that tracks a Werkstudent's 20-hour limit day by day. The limit is a **social-insurance rule**: if you regularly work no more than 20 hours a week during the lecture period (hours at several employers are added together), you are exempt from health, long-term care and unemployment insurance in that job and only pay the employee share of pension insurance. You may exceed 20 hours for up to 26 weeks a year if the extra work is in the evening, at night, at weekends or during semester breaks.\n\n### Who looks at what?\n- **Employer:** registers you with social insurance and assesses your Werkstudent status based on your contract and working hours. Because hours are added up across jobs, tell your employers about your other jobs.\n- **Krankenkasse (health insurer):** the place to ask about your insurance status. If the Werkstudent conditions are not met, you are insured like a regular employee.\n- **Ausländerbehörde:** under residence law the limit is not weekly hours but **140 working days per year** (§ 16b AufenthG, Arbeitstagekonto). A day of up to 4 hours counts as half a day; a lecture-period week with no more than 20 hours can count as 2.5 working days, and for each week the calculation more favourable to you applies. You may be asked for documents about your work when you extend your permit; check the details with your own Ausländerbehörde.\n\n### What happens if you exceed 20 hours?\nYou can lose the Werkstudent social-insurance advantage; health, long-term care and unemployment insurance contributions are then calculated as for a regular employee. For your residence permit, what counts is the 140-working-day account; 20 hours a week on its own is not a residence-law limit.\n\n### How much is student health insurance?\nThere is no single fixed amount for everyone. The student contribution is calculated from a statutory base amount, plus your Krankenkasse's additional contribution (Zusatzbeitrag) and the long-term care contribution. The amount therefore varies by insurer and personal situation (for example age or children); ask your own Krankenkasse for the current figure.\n\n### Practical tips\n- Keep your own record of the days and hours you work, and keep your payslips (Lohnabrechnung).\n- If you have more than one job, track your total weekly hours and your 140-day account together.\n- If in doubt, ask your employer and your Krankenkasse.\n\nMore: [HiWi or Werkstudent?](/en/blog/hiwi-vs-werkstudent-student-jobs-in-germany-en) · [The 140-day work account](/en/blog/internship-in-germany-with-b1-b2-german-en)\n"
    }
   }
  },
  {
   "table": "faqs",
   "slug": "werkstudent-saat-siniri-nasil-kontrol-ediliyor-de",
   "locale": "de",
   "cluster": "werkstudent-saat-siniri-nasil-kontrol-ediliyor",
   "rewrite": {
    "answer_md": {
     "fingerprints": [
      "131 €/Monat",
      "Steuerprüfung",
      "DEÜV-Meldungen"
     ],
     "new": "Ein zentrales System, das die 20-Stunden-Grenze für Werkstudenten Tag für Tag überwacht, gibt es nicht. Die Grenze ist eine **Regel der Sozialversicherung**: Arbeitest du in der Vorlesungszeit regelmäßig höchstens 20 Stunden pro Woche (bei mehreren Arbeitgebern werden die Stunden zusammengezählt), bist du in diesem Job kranken-, pflege- und arbeitslosenversicherungsfrei und zahlst nur den Arbeitnehmeranteil zur Rentenversicherung. Mehr als 20 Stunden sind bis zu 26 Wochen im Jahr möglich, wenn die Arbeit abends, nachts, am Wochenende oder in den Semesterferien liegt.\n\n### Wer schaut worauf?\n- **Arbeitgeber:** meldet dich zur Sozialversicherung an und beurteilt deinen Werkstudentenstatus anhand von Vertrag und Arbeitszeit. Weil die Stunden aus allen Jobs zusammenzählen, informiere deine Arbeitgeber über weitere Jobs.\n- **Krankenkasse:** Ansprechpartnerin für Fragen zu deinem Versicherungsstatus. Sind die Werkstudenten-Voraussetzungen nicht erfüllt, bist du wie ein regulärer Arbeitnehmer versichert.\n- **Ausländerbehörde:** Aufenthaltsrechtlich gilt keine Wochenstundengrenze, sondern ein Konto von **140 Arbeitstagen pro Jahr** (§ 16b AufenthG, Arbeitstagekonto). Ein Tag mit bis zu 4 Stunden zählt als halber Tag; eine Woche in der Vorlesungszeit mit höchstens 20 Stunden kann als 2,5 Arbeitstage zählen, und pro Woche gilt die für dich günstigere Berechnung. Bei der Verlängerung können Nachweise zu deiner Beschäftigung verlangt werden; kläre die Einzelheiten mit deiner Ausländerbehörde.\n\n### Was passiert, wenn du mehr als 20 Stunden arbeitest?\nDu kannst den Vorteil des Werkstudentenstatus in der Sozialversicherung verlieren; Kranken-, Pflege- und Arbeitslosenversicherung werden dann wie bei regulären Arbeitnehmern berechnet. Aufenthaltsrechtlich zählt das Konto von 140 Arbeitstagen; 20 Wochenstunden allein sind keine aufenthaltsrechtliche Grenze.\n\n### Wie viel kostet die studentische Krankenversicherung?\nEinen festen Betrag für alle gibt es nicht. Der Studentenbeitrag wird aus einem gesetzlichen Bemessungsbetrag berechnet; dazu kommen der Zusatzbeitrag deiner Krankenkasse und der Pflegeversicherungsbeitrag. Der Betrag hängt deshalb von der Kasse und deiner persönlichen Situation ab (zum Beispiel Alter oder Kinder); den aktuellen Wert erfährst du bei deiner Krankenkasse.\n\n### Praktische Tipps\n- Halte deine Arbeitstage und -stunden selbst fest und bewahre deine Lohnabrechnungen auf.\n- Wenn du mehrere Jobs hast, behalte deine gesamte Wochenarbeitszeit und dein 140-Tage-Konto gemeinsam im Blick.\n- Im Zweifel frag deinen Arbeitgeber und deine Krankenkasse.\n\nMehr dazu: [HiWi oder Werkstudent?](/de/blog/hiwi-vs-werkstudent-student-jobs-in-germany-de) · [Das 140-Tage-Arbeitskonto](/de/blog/internship-in-germany-with-b1-b2-german-de)\n"
    }
   }
  },
  {
   "table": "faqs",
   "slug": "mini-job-538eur-siniri-nedir",
   "locale": "tr",
   "cluster": "mini-job-538eur-siniri-nedir",
   "rewrite": {
    "answer_md": {
     "fingerprints": [
      "Sen 0 prim ödüyorsun",
      "11,604 €",
      "131 €/ay"
     ],
     "new": "**Minijob**, 2026'da düzenli aylık kazancı **603 €'yu** (yılda 7.236 €) geçmeyen iştir (geringfügig entlohnte Beschäftigung). Sınır 2023'te 520 €, 2024'te 538 €, 2025'te 556 € idi.\n\n### Çalışan olarak ne ödersin?\n- **Emeklilik sigortası:** Minijob kural olarak emeklilik sigortasına tabidir. Toplam oran %18,6'dır; ticari bir Minijob'da işveren %15, sen **%3,6** ödersin. İşverenine yazılı ya da elektronik başvuruyla bu yükümlülükten **muafiyet** isteyebilirsin; o zaman senin payın düşer, işverenin toplu primi devam eder.\n- **Sağlık, bakım ve işsizlik sigortası:** Çalışan olarak Minijob'dan bu kollara prim ödemezsin.\n- **Vergi:** İşveren çoğunlukla %2 götürü vergi (Pauschsteuer) öder.\n\n### Sağlık sigortan Minijob'dan gelmez\nYasal sağlık sigortasındaysan işveren %13 toplu sağlık primi öder. Bu bir dayanışma katkısıdır; sana **kendi sağlık sigortası güvencesi sağlamaz**. Öğrenci sağlık sigortanı (ya da mevcut sigorta statünü) ayrıca sürdürmen gerekir; primi de kendin ödersin.\n\n### Saat sınırı var mı?\nMinijob için yasal bir haftalık saat sınırı yoktur; belirleyici olan aylık kazanç sınırıdır. 13,90 € asgari ücretle 603 € yaklaşık haftada 10 saate karşılık gelir; bu bir hesaplamadır, yasal bir sınır değildir. Birden fazla Minijob'ın varsa kazançların birlikte değerlendirilir.\n\n### Werkstudent ve diğer gelirlerle birlikte\n- Minijob'ı bir Werkstudent işiyle birlikte yapıyorsan, ders dönemindeki toplam çalışma süren Werkstudent sosyal sigorta statüsünü etkileyebilir.\n- Başka vergilendirilebilir gelirin varsa: 2026'da gelir vergisinde temel muafiyet tutarı (Grundfreibetrag) **12.348 €**'dur.\n\n### AB dışı öğrenciler (§ 16b AufenthG)\nMinijob'da çalıştığın günler de yılda **140 iş günlük** hesabına (Arbeitstagekonto) sayılır; 4 saate kadar çalışılan gün yarım gün sayılır.\n\nKaynak: [Minijob-Zentrale](https://www.minijob-zentrale.de/DE/die-minijobs/rentenversicherungspflicht) · Ayrıntılar: [HiWi mi Werkstudent mi?](/tr/blog/hiwi-vs-werkstudent-student-jobs-in-germany)\n"
    }
   }
  },
  {
   "table": "faqs",
   "slug": "mini-job-538eur-siniri-nedir-en",
   "locale": "en",
   "cluster": "mini-job-538eur-siniri-nedir",
   "rewrite": {
    "answer_md": {
     "fingerprints": [
      "You pay 0 contributions",
      "€11,604",
      "€131/month"
     ],
     "new": "A **Minijob** is a job whose regular monthly earnings do not exceed **€603 in 2026** (€7,236 a year) (geringfügig entlohnte Beschäftigung). The limit was €520 in 2023, €538 in 2024 and €556 in 2025.\n\n### What do you pay as the employee?\n- **Pension insurance:** a Minijob is generally subject to pension insurance. The total rate is 18.6%; in a commercial Minijob the employer pays 15% and you pay **3.6%**. You can ask your employer in writing or electronically to be **exempted**; your share then no longer applies, while the employer's flat-rate contribution continues.\n- **Health, long-term care and unemployment insurance:** as the employee, you pay no contributions to these from your Minijob.\n- **Tax:** the employer often pays a flat 2% tax (Pauschsteuer).\n\n### Your health insurance does not come from the Minijob\nIf you are in statutory health insurance, the employer pays a flat 13% health contribution. This is a solidarity contribution and does **not give you your own health cover**. You still need to keep your student health insurance (or your existing insurance status) separately and pay for it yourself.\n\n### Is there an hours limit?\nThere is no statutory weekly hours limit for a Minijob; what matters is the monthly earnings limit. At the €13.90 minimum wage, €603 corresponds to about 10 hours a week; this is a calculation, not a legal limit. If you have more than one Minijob, your earnings are assessed together.\n\n### Combined with a Werkstudent job or other income\n- If you combine a Minijob with a Werkstudent job, your total working time during the lecture period can affect your Werkstudent social-insurance status.\n- If you have other taxable income: the basic income-tax allowance (Grundfreibetrag) is **€12,348 in 2026**.\n\n### Non-EU students (§ 16b AufenthG)\nThe days you work in a Minijob also count towards your annual account of **140 working days** (Arbeitstagekonto); a day of up to 4 hours counts as half a day.\n\nSource: [Minijob-Zentrale](https://www.minijob-zentrale.de/DE/die-minijobs/rentenversicherungspflicht) · More: [HiWi or Werkstudent?](/en/blog/hiwi-vs-werkstudent-student-jobs-in-germany-en)\n"
    }
   }
  },
  {
   "table": "faqs",
   "slug": "mini-job-538eur-siniri-nedir-de",
   "locale": "de",
   "cluster": "mini-job-538eur-siniri-nedir",
   "rewrite": {
    "answer_md": {
     "fingerprints": [
      "Du zahlst 0 Beiträge",
      "11.604 €",
      "131 €/Monat"
     ],
     "new": "Ein **Minijob** ist eine Beschäftigung, bei der der regelmäßige Monatsverdienst **2026 höchstens 603 €** beträgt (7.236 € im Jahr; geringfügig entlohnte Beschäftigung). Die Grenze lag 2023 bei 520 €, 2024 bei 538 € und 2025 bei 556 €.\n\n### Was zahlst du als Arbeitnehmer?\n- **Rentenversicherung:** Ein Minijob ist grundsätzlich rentenversicherungspflichtig. Der volle Beitragssatz beträgt 18,6 %; im gewerblichen Minijob zahlt der Arbeitgeber 15 % und du **3,6 %**. Du kannst bei deinem Arbeitgeber schriftlich oder elektronisch die **Befreiung** beantragen; dann entfällt dein Anteil, der Pauschalbeitrag des Arbeitgebers bleibt.\n- **Kranken-, Pflege- und Arbeitslosenversicherung:** Als Arbeitnehmer zahlst du aus dem Minijob keine Beiträge dazu.\n- **Steuer:** Der Arbeitgeber zahlt häufig eine Pauschsteuer von 2 %.\n\n### Deine Krankenversicherung kommt nicht aus dem Minijob\nBist du gesetzlich krankenversichert, zahlt der Arbeitgeber einen Pauschalbeitrag von 13 % zur Krankenversicherung. Das ist ein Solidarbeitrag; daraus entsteht **kein eigener Krankenversicherungsschutz** für dich. Deine studentische Krankenversicherung (oder deinen bisherigen Versicherungsstatus) musst du weiterhin selbst haben und bezahlen.\n\n### Gibt es eine Stundengrenze?\nFür Minijobs gibt es keine gesetzliche wöchentliche Stundengrenze; entscheidend ist die monatliche Verdienstgrenze. Beim Mindestlohn von 13,90 € entsprechen 603 € etwa 10 Stunden pro Woche; das ist eine Rechnung, keine gesetzliche Grenze. Hast du mehrere Minijobs, werden die Verdienste zusammen betrachtet.\n\n### Zusammen mit Werkstudentenjob oder anderen Einkünften\n- Kombinierst du einen Minijob mit einem Werkstudentenjob, kann deine gesamte Arbeitszeit in der Vorlesungszeit deinen Werkstudentenstatus in der Sozialversicherung beeinflussen.\n- Hast du weitere steuerpflichtige Einkünfte: Der Grundfreibetrag bei der Einkommensteuer liegt **2026 bei 12.348 €**.\n\n### Studierende aus Nicht-EU-Staaten (§ 16b AufenthG)\nAuch die Tage im Minijob zählen zu deinem Jahreskonto von **140 Arbeitstagen** (Arbeitstagekonto); ein Tag mit bis zu 4 Stunden zählt als halber Tag.\n\nQuelle: [Minijob-Zentrale](https://www.minijob-zentrale.de/DE/die-minijobs/rentenversicherungspflicht) · Mehr dazu: [HiWi oder Werkstudent?](/de/blog/hiwi-vs-werkstudent-student-jobs-in-germany-de)\n"
    }
   }
  }
 ]
}
JSON, true, 512, JSON_THROW_ON_ERROR);

        $problems = [];
        $writes = [];
        $done = [];
        foreach ($spec['records'] as $r) {
            $label = "{$r['table']}:{$r['slug']}".(isset($r['locale']) ? "/{$r['locale']}" : '');
            $q = DB::table($r['table'])->where('slug', $r['slug']);
            if (isset($r['locale'])) {
                $q->where('locale', $r['locale']);
            }
            $rows = $q->get();
            if ($rows->count() !== 1) {
                $problems[] = "{$label}: kayıt sayısı {$rows->count()}";
                continue;
            }
            $row = (array) $rows->first();
            if (isset($r['cluster'])) {
                $group = DB::table('faqs')->where('slug', $r['cluster'])->where('locale', 'tr')->value('translation_group_id');
                if (! $group || $row['translation_group_id'] !== $group) {
                    $problems[] = "{$label}: TR kaydıyla aynı çeviri kümesinde değil";
                    continue;
                }
            }
            $pending = 0;
            $applied = 0;
            $upd = [];

            foreach ($r['md'] ?? [] as $col => $edits) {
                $lines = explode("\n", (string) $row[$col]);
                $changed = false;
                $units = null;
                foreach ($edits as $e) {
                    // $e = {line: canlı bloğun düz metni, subs: [{old,new}]} — blok düz metinle bulunur, değişiklik
                    // yalnız ham markdown'daki alt dizede yapılır (link/biçim korunur). Blok = tek satır, art arda
                    // satırlardan oluşan paragraf (yumuşak satır sonu) ya da tablo hücresi.
                    // {line, replace}: bütün blok yeni markdown ile değişir (önek korunur).
                    $whole = isset($e['replace']);
                    $after = $whole ? $e['replace'] : $e['line'];
                    foreach ($whole ? [] : $e['subs'] as $s) {
                        $after = str_replace($s['old'], $s['new'], $after);
                    }
                    $units ??= $this->units($lines);
                    $old = array_values(array_filter($units, fn ($u) => $u[3] === $this->norm($e['line'])));
                    $new = array_values(array_filter($units, fn ($u) => $u[3] === $this->norm($after)));
                    $rawOk = $old !== [] && array_reduce($old, fn ($ok, $u) => $ok && ($whole
                        ? $u[0] !== 'cell'
                        : array_reduce($e['subs'], fn ($o, $s) => $o && $this->unitHas($lines, $u, $s['old']), true)), true);
                    if (count($old) >= 1 && count($new) === 0 && $rawOk) {
                        $pending++;
                        foreach ($old as [$kind, $start, $len]) {
                            if ($whole) {
                                $cr = str_ends_with($lines[$start], "\r") ? "\r" : '';
                                // ^\s* (0–3 değil): girintili alt liste maddesinin ("    *   ") öneki de korunur
                                preg_match('/^\s*(?:#{1,6}\s+|(?:>\s?)+|[-*+]\s+|\d+[.)]\s+)?/u', $lines[$start], $m);
                                $lines[$start] = $m[0].$e['replace'].$cr;
                                for ($i = $start + 1; $i < $start + $len; $i++) {
                                    $lines[$i] = "\0";   // paragrafın diğer satırları (sonda silinir)
                                }
                            } else {
                                for ($i = $start; $i < $start + $len; $i++) {
                                    foreach ($e['subs'] as $s) {
                                        $lines[$i] = str_replace($s['old'], $s['new'], $lines[$i]);
                                    }
                                }
                            }
                        }
                        $changed = true;
                        $units = null;
                    } elseif (count($old) === 0 && count($new) >= 1) {
                        $applied++;
                    } else {
                        $problems[] = "{$label}: {$col} blok eşleşmedi (eski ".count($old).', yeni '.count($new).', ham alt dize '.($rawOk ? 'var' : 'yok').') «'.mb_substr($e['line'], 0, 70).'»';
                    }
                }
                if ($changed) {
                    $upd[$col] = implode("\n", array_values(array_filter($lines, fn ($l) => $l !== "\0")));
                }
            }

            foreach ($r['fieldsub'] ?? [] as $col => $subs) {
                $cur = (string) $row[$col];
                foreach ($subs as $s) {
                    if (substr_count($cur, $s['old']) === 1 && ! str_contains($cur, $s['new'])) {
                        $pending++;
                        $cur = str_replace($s['old'], $s['new'], $cur);
                        $upd[$col] = $cur;
                    } elseif (! str_contains($cur, $s['old']) && str_contains($cur, $s['new'])) {
                        $applied++;
                    } else {
                        $problems[] = "{$label}: {$col} alt dize eşleşmedi «".mb_substr($s['old'], 0, 60).'»';
                    }
                }
            }

            foreach ($r['rewrite'] ?? [] as $col => $rw) {
                // Tamamen eski kurala dayalı SSS cevabı: eski metin parmak izleriyle doğrulanır, yeni metin bütün yazılır.
                $cur = (string) $row[$col];
                $plain = $this->norm(str_replace("\n", ' ', $cur));
                $fp = array_reduce($rw['fingerprints'], fn ($ok, $f) => $ok && str_contains($plain, $this->norm($f)), true);
                if ($cur === $rw['new']) {
                    $applied++;
                } elseif ($fp) {
                    $pending++;
                    $upd[$col] = $rw['new'];
                } else {
                    $problems[] = "{$label}: {$col} eski metin parmak izleri bulunamadı";
                }
            }

            foreach ($r['fields'] ?? [] as $col => $v) {
                $cur = (string) $row[$col];
                if ($cur === $v['new']) {
                    $applied++;
                } elseif ($cur === $v['old']) {
                    $pending++;
                    $upd[$col] = $v['new'];
                } else {
                    $problems[] = "{$label}: {$col} beklenen eski değerde değil";
                }
            }

            foreach ($r['json'] ?? [] as $col => $subs) {
                $data = json_decode((string) $row[$col], true);
                if (! is_array($data)) {
                    $problems[] = "{$label}: {$col} JSON okunamadı";
                    continue;
                }
                $changed = false;
                $orig = $data;   // durum her alt dize için ORİJİNAL veriye göre belirlenir
                foreach ($subs as $s) {
                    $o = $this->countSub($orig, $s['old']);
                    $n = $this->countSub($orig, $s['new']);
                    if ($o >= 1 && $n === 0) {
                        $pending++;
                        $data = $this->replaceSub($data, $s['old'], $s['new']);
                        $changed = true;
                    } elseif ($o === 0 && $n >= 1) {
                        $applied++;
                    } else {
                        $problems[] = "{$label}: {$col} alt dize eşleşmedi (eski {$o}, yeni {$n}) «".mb_substr($s['old'], 0, 70).'»';
                    }
                }
                if ($changed) {
                    $upd[$col] = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }

            if ($pending > 0 && $applied > 0) {
                $problems[] = "{$label}: kısmen uygulanmış ({$applied} uygulanmış, {$pending} bekleyen)";
            } elseif ($pending > 0) {
                $writes[] = [$r, $row['id'], $upd];
            } else {
                $done[] = $label;
            }
        }
        if (! $problems && $writes && $done) {
            $problems[] = 'kayıtlar arası kısmi durum: '.count($done).' kayıt uygulanmış, '.count($writes).' bekleyen ('.implode(', ', array_slice($done, 0, 5)).')';
        }

        if ($problems) {
            if (app()->runningUnitTests()) {
                return; // Test DB'si sıfırdan kurulur; bu kayıtların çoğu orada yok. Hiçbir şey yazma.
            }
            throw new RuntimeException('FAQ Parity Batch A1 (kaynak temizliği): ön kontrol başarısız, hiçbir şey yazılmadı. '.implode(' | ', array_slice($problems, 0, 40)));
        }
        if (! $writes) {
            return; // zaten uygulanmış — no-op
        }

        DB::transaction(function () use ($writes) {
            foreach ($writes as [$r, $id, $upd]) {
                if ($r['table'] === 'posts') {
                    $m = \App\Models\Post::findOrFail($id);   // booted(): content_html + reading_minutes
                } elseif ($r['table'] === 'faqs') {
                    $m = \App\Models\Faq::findOrFail($id);    // booted(): answer_html
                } else {
                    DB::table($r['table'])->where('id', $id)->update($upd + ['updated_at' => now()]);
                    continue;
                }
                foreach ($upd as $col => $val) {
                    $m->{$col} = $val;
                }
                $m->save();
            }
        });
    }

    private function countSub($v, string $needle): int
    {
        if (is_array($v)) {
            return array_sum(array_map(fn ($x) => $this->countSub($x, $needle), $v));
        }

        return is_string($v) ? substr_count($v, $needle) : 0;
    }

    private function replaceSub($v, string $old, string $new)
    {
        if (is_array($v)) {
            return array_map(fn ($x) => $this->replaceSub($x, $old, $new), $v);
        }

        return is_string($v) ? str_replace($old, $new, $v) : $v;
    }

    /**
     * Markdown satırlarından sayfadaki bloklara karşılık gelen birimler: [tür, başlangıç, satır sayısı, düz metin].
     * tür: line (tek satır), win (boş satırla kesilmeyen art arda satırlar = yumuşak satır sonlu paragraf),
     * cell (tablo satırındaki hücre).
     */
    private function units(array $lines): array
    {
        $u = [];
        $n = count($lines);
        $blank = fn ($l) => $l === "\0" || trim($l) === '';
        for ($i = 0; $i < $n; $i++) {
            if ($blank($lines[$i])) {
                continue;
            }
            $u[] = ['line', $i, 1, $this->norm($lines[$i])];
            if (str_starts_with(ltrim($lines[$i]), '|')) {
                foreach (explode('|', trim(trim($lines[$i]), '|')) as $cell) {
                    $u[] = ['cell', $i, 1, $this->norm($cell)];
                }
            }
            $txt = $lines[$i];
            for ($j = $i + 1; $j < min($n, $i + 15) && ! $blank($lines[$j]); $j++) {
                $txt .= ' '.$lines[$j];
                $u[] = ['win', $i, $j - $i + 1, $this->norm($txt)];
            }
        }

        return $u;
    }

    private function unitHas(array $lines, array $u, string $needle): bool
    {
        for ($i = $u[1]; $i < $u[1] + $u[2]; $i++) {
            if (str_contains($lines[$i], $needle)) {
                return true;
            }
        }

        return false;
    }

    /** Markdown satırının görünen düz metni (canlı sayfadaki blok metniyle karşılaştırmak için). */
    private function norm(string $s): string
    {
        $s = preg_replace('/^\s*(?:#{1,6}\s+|(?:>\s?)+|[-*+]\s+|\d+[.)]\s+)?/u', '', $s);
        $s = preg_replace('/!\[([^\]]*)\]\([^)]*\)/u', '$1', $s);
        for ($i = 0; $i < 3; $i++) {
            $s = preg_replace('/\[([^\]]*)\]\([^)]*\)/u', '$1', $s);
        }
        $s = preg_replace('/<[^>]+>/u', '', $s);
        $s = str_replace(['**', '__', '*', '`'], '', $s);
        $s = preg_replace('/\\\\([\\\\`*_{}\[\]()#+\-.!>|"\'])/u', '$1', $s);
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $s));
    }

    public function down(): void
    {
        // Bilinçli olarak boş: forward-only içerik düzeltmesi.
    }
};

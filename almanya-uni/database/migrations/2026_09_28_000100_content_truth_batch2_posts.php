<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Content Truth Sprint — Batch 2 (A — blog/news yazıları): site genelinde eski çalışma kuralı ve tutar düzeltmeleri.
 *
 * Doğrulanmış gerçekler (27.09.2026): § 16b Abs. 3 AufenthG — 01.03.2024'ten beri 140 Arbeitstage
 * (Arbeitstagekonto; ≤4 saat = yarım gün; ders döneminde ≤20 saatlik hafta 2,5 gün sayılabilir; öğrenci lehine
 * hesap). Minijob sınırı 2026: 603 € (2025: 556 €, 2024: 538 €). Asgari ücret 2026: 13,90 €. Sperrkonto 2026:
 * 992 €/ay, 11.904 €/yıl (01.09.2024'ten beri). BAföG azami 992 € (WS 2024/25). DAAD: Master 992 €, doktora
 * 1.400 € (Şubat 2026'dan beri). Werkstudent 20 saat kuralı sosyal sigortaya ilişkindir; oturum hukukundaki
 * çalışma günü hesabından ayrıdır.
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
   "slug": "2026-guide-to-tax-and-health-insurance-for-students-in-germany-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Minijob: Bei einem Minijob bleibt deine studentische Krankenversicherung unberührt und dein günstiger Tarif besteht fort, solange dein monatliches Einkommen die Minijob-Grenze (z.B. 538 Euro) nicht überschreitet. Das Einkommen aus einem Minijob erhöht deine studentischen Krankenversicherungsbeiträge nicht.",
      "subs": [
       {
        "old": "z.B. 538 Euro",
        "new": "2026: 603 Euro"
       }
      ]
     },
     {
      "line": "Mindestlohn: In Deutschland gibt es einen gesetzlichen Mindestlohn, der in bestimmten Abständen aktualisiert wird. Arbeitgeber dürfen dir nicht weniger als diesen Mindestlohn zahlen. Ab 2024 beträgt der Mindestlohn einen bestimmten Eurobetrag pro Stunde (z.B. 12,82 Euro, diese Zahlen können sich ändern). Überprüfe den aktuellen Mindestlohn, bevor du anfängst zu arbeiten.",
      "subs": [
       {
        "old": "Ab 2024 beträgt der Mindestlohn einen bestimmten Eurobetrag pro Stunde (z.B. 12,82 Euro, ",
        "new": "Im Jahr 2026 beträgt der Mindestlohn 13,90 Euro pro Stunde ("
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "bafog-alternatives-scholarships-in-germany-for-turkish-students-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Stipendienarten: Master-Stipendien: Dauern in der Regel 10-24 Monate und bieten eine monatliche Lebenshaltungskostenpauschale von 934 Euro, Reisekosten und Unterstützung bei der Krankenversicherung. Ideal für Masterstudiengänge in Deutschland.",
      "subs": [
       {
        "old": "934 Euro",
        "new": "992 Euro"
       }
      ]
     },
     {
      "line": "Stipendienhöhe: Deckt monatliche Lebenshaltungskosten, Büchergeld, Krankenversicherung und in einigen Fällen Reisekosten ab. Ähnlich wie bei DAAD-Stipendien können sie für Masterstudierende etwa 934 Euro und für Doktoranden 1.300 Euro pro Monat an Unterstützung bieten.",
      "subs": [
       {
        "old": "934 Euro",
        "new": "992 Euro"
       },
       {
        "old": "1.300 Euro",
        "new": "1.400 Euro"
       }
      ]
     },
     {
      "line": "Teilzeitjobs (Werkstudent/Minijob): Internationale Studierende in Deutschland haben eine Arbeitserlaubnis für eine bestimmte Dauer pro Jahr (in der Regel 120 volle Tage oder 240 halbe Tage). Insbesondere die Arbeit als \"Werkstudent\" (working student) in einem Unternehmen deines Fachbereichs bietet sowohl finanzielle Unterstützung als auch wertvolle Berufserfahrung. Diese Einkommensquelle wird jedoch bei der Beantragung eines Visums in der Regel nicht als ausreichend angesehen, und es wird verlangt, ein Sperrkonto (blocked account) vorzuweisen.",
      "subs": [
       {
        "old": "in der Regel 120 volle Tage oder 240 halbe Tage",
        "new": "bis zu 140 Arbeitstage, Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag"
       }
      ]
     },
     {
      "line": "Bei der Beantragung eines Studentenvisums für Deutschland verlangt die deutsche Regierung einen Nachweis, dass du deine Studien- und Lebenshaltungskosten decken kannst. Hier kommt das Sperrkonto (blocked account) ins Spiel. Dies ist ein spezielles Bankkonto, das auf deinen Namen eröffnet wird, auf dem ein bestimmter Geldbetrag (Stand 2024: ca. 934 Euro pro Monat, ca. 11.208 Euro pro Jahr) blockiert ist und du jeden Monat auf einen bestimmten Teil davon zugreifen kannst.",
      "subs": [
       {
        "old": "Stand 2024: ca. 934 Euro pro Monat, ca. 11.208 Euro pro Jahr",
        "new": "seit 01.09.2024: 992 Euro pro Monat, 11.904 Euro pro Jahr"
       }
      ]
     },
     {
      "line": "Wichtiger Hinweis: Ein Sperrkonto ist keine Finanzierungsquelle; es ist lediglich eine Methode, deine finanzielle Leistungsfähigkeit nachzuweisen. Wenn du ein Stipendium erhältst und der Stipendienbetrag den monatlichen Sperrkontobetrag (934 Euro) abdeckt, kannst du bei deinem Visumantrag den Stipendienzulassungsbescheid als finanziellen Nachweis vorlegen, und es ist möglicherweise nicht erforderlich, ein Sperrkonto zu eröffnen. Liegt der Stipendienbetrag jedoch unter diesem Limit, musst du die Differenz möglicherweise mit einem Sperrkonto ausgleichen. Für aktuelle und genaue Informationen zu diesem Thema solltest du dich an das Konsulat oder die Visumantragsstelle wenden.",
      "subs": [
       {
        "old": "934 Euro",
        "new": "992 Euro"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "bringing-family-to-germany-with-a-student-visa-2026-turkish-student-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Ja — ohne Einschränkungen. Mit einer Aufenthaltserlaubnis für Familiennachzug/gemeinsamen Antrag kann der Ehepartner vollzeit + in jedem Sektor arbeiten. Die 120/240-Tage-Beschränkung Ihres Studentenvisums gilt nicht für den Ehepartner.",
      "subs": [
       {
        "old": "120/240-Tage-Beschränkung",
        "new": "140-Arbeitstage-Beschränkung"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "can-you-study-for-an-english-masters-in-germany-without-knowing-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Arbeitserlaubnis (20-Stunden-Regel): „Freunde, ich gehe für ein Masterstudium nach Deutschland, ich habe doch eine 20-Stunden-Arbeitserlaubnis, oder? Und kann ich die bei Liferando nutzen?“ Ja, als Masterstudierender in Deutschland hast du eine Arbeitserlaubnis für 120 volle Tage oder 240 halbe Tage pro Jahr (was 20 Stunden pro Woche entspricht). Du kannst diese Erlaubnis für Jobs wie Liferando (Essenslieferdienst), als Werkstudent an der Universität oder in anderen Tätigkeiten nutzen. Diese Arbeitserlaubnis sollte jedoch deine akademische Leistung nicht beeinträchtigen, und deine Ausbildung sollte Priorität haben.",
      "subs": [
       {
        "old": "120 volle Tage oder 240 halbe Tage pro Jahr (was 20 Stunden pro Woche entspricht)",
        "new": "bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag; in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "daad-scholarship-master-phd-2026-step-by-step-application-guide-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Stipendienhöhe: Master-Studierenden wird ein monatliches Stipendium von 861 Euro gezahlt. Zusätzlich werden Krankenversicherung, Reisekosten und in einigen Fällen auch Unterstützung bei den Kursgebühren übernommen.",
      "subs": [
       {
        "old": "861 Euro",
        "new": "992 Euro"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "finding-a-wg-in-germany-your-comprehensive-guide-to-dorm-wg-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Mit einem Studentenvisum in Deutschland gibt es eine Arbeitsgrenze von 20 Stunden pro Woche. Diese Grenze gilt während des Semesters. In den Semesterferien darfst du jedoch Vollzeit (bis zu 40 Stunden pro Woche) arbeiten. Pro Jahr hast du eine Arbeitserlaubnis von 120 vollen Tagen oder 240 halben Tagen (jeweils 4 Stunden oder weniger). Teilzeit- (Teilzeit) und Minijobs (Minijob, bis zu 538 Euro monatlich) können kombiniert werden, aber du musst darauf achten, dass deine gesamte Arbeitszeit und dein Verdienst diese gesetzlichen Grenzen nicht überschreiten. Eine Überschreitung der Grenzen kann zur Annullierung deines Visums oder zu rechtlichen Problemen führen. Es wird empfohlen, die aktuellsten und genauesten Informationen hierzu bei der Ausländerbehörde (Ausländerbehörde) oder dem International Office deiner Universität einzuholen.",
      "replace": "Mit einem Studentenvisum bzw. einer Aufenthaltserlaubnis zum Studium darfst du in Deutschland seit dem 01.03.2024 bis zu **140 Arbeitstage im Jahr** arbeiten. Gezählt wird über ein Arbeitstagekonto: Ein Tag mit bis zu 4 Stunden zählt als halber Tag; während der Vorlesungszeit kann alternativ eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden – es gilt jeweils die günstigere Zählung. In den Semesterferien kannst du auch Vollzeit arbeiten, die Tage werden aber auf dein Konto angerechnet. Die oft genannten **20 Stunden** pro Woche sind keine aufenthaltsrechtliche Grenze, sondern die sozialversicherungsrechtliche Bedingung für den Werkstudentenstatus; außerdem muss das Studium die Hauptsache bleiben. Teilzeit- und Minijobs (Minijob bis zu 603 Euro monatlich, Stand 2026) können kombiniert werden, aber alle Stunden und Tage zählen auf dein 140-Tage-Konto. Wird das Konto überschritten, kann das zu Problemen mit deiner Aufenthaltserlaubnis oder zu rechtlichen Problemen führen. Es wird empfohlen, die aktuellsten und genauesten Informationen hierzu bei der Ausländerbehörde oder dem International Office deiner Universität einzuholen."
     },
     {
      "line": "Kann ich mit einem Studentenvisum während des Semesters Teilzeit + Minijob machen? Gibt es Probleme, wenn ich die wöchentliche 20-Stunden-Grenze überschreite, solange ich die jährliche Arbeitserlaubnis von 240 halben Tagen nicht überschreite?",
      "subs": [
       {
        "old": "solange ich die jährliche Arbeitserlaubnis von 240 halben Tagen nicht überschreite?",
        "new": "solange ich das jährliche Arbeitstagekonto von 140 Tagen nicht überschreite?"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "germany-bachelor-application-2026-complete-flow-guide-for-turkish-high-school-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Sperrkonto: Internationale Studierende, die in Deutschland studieren möchten, müssen nachweisen, dass sie die von der deutschen Regierung festgelegten jährlichen Lebenshaltungskosten decken können. Dieser Betrag liegt ab 2024 bei etwa 11.208 Euro (934 Euro pro Monat). Dieses Geld musst du auf ein in Deutschland eröffnetes Sperrkonto einzahlen und monatlich einen bestimmten Teil davon abheben können. Dies ist eine der wichtigsten Voraussetzungen für deinen Visumsantrag.",
      "subs": [
       {
        "old": "liegt ab 2024 bei etwa 11.208 Euro (934 Euro pro Monat)",
        "new": "liegt seit 01.09.2024 bei 11.904 Euro (992 Euro pro Monat)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "germany-it-job-search-2026-a-guide-for-turkish-graduates-and-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Können Studierende neben dem Recht auf 20 Stunden Arbeit pro Woche auch einen Minijob ausüben? Im Allgemeinen nein. Die wöchentliche Arbeitszeitbegrenzung von 20 Stunden im Rahmen eines Studentenvisums umfasst alle einkommensschaffenden Tätigkeiten. Ein Minijob (Arbeiten bis zu 538 Euro pro Monat) fällt nicht außerhalb dieser Grenze, das heißt, Sie dürfen insgesamt 20 Stunden pro Woche nicht überschreiten. Für Antworten auf diese und ähnliche Fragen vergessen Sie nicht, unsere Seite ApplyToGerman (AlmanyaUni) Häufig gestellte Fragen zu besuchen.",
      "replace": "**Können Studierende neben einem Werkstudentenjob auch einen Minijob ausüben?** Grundsätzlich ja. Aufenthaltsrechtlich gilt für Studierende aus Nicht-EU-Staaten seit dem 01.03.2024 ein Konto von 140 Arbeitstagen im Jahr (ein Tag mit bis zu 4 Stunden zählt als halber Tag). Die bekannte Grenze von 20 Stunden pro Woche ist dagegen die sozialversicherungsrechtliche Bedingung für den Werkstudentenstatus. Ein Minijob (bis zu 603 Euro pro Monat, Stand 2026) lässt sich mit einem Werkstudentenjob kombinieren; alle Stunden und Tage zählen jedoch auf Ihr 140-Tage-Konto, und das Studium muss die Hauptsache bleiben. Für Antworten auf diese und ähnliche Fragen vergessen Sie nicht, unsere Seite [ApplyToGerman (AlmanyaUni) Häufig gestellte Fragen](/de/faq) zu besuchen."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "germany-student-visa-2026-application-steps-documents-rejection-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Dies ist das auf Bundesebene festgelegte Minimum – es ändert sich nicht. 2025 wurde es von 11.208 € auf 11.904 € erhöht. Obwohl einige versuchen, diesen Betrag über eine Verpflichtungserklärung zu reduzieren, sehen die Konsulate das Sperrkonto in der Regel als sicherer an.",
      "subs": [
       {
        "old": "2025 wurde es",
        "new": "Zum 01.09.2024 wurde es"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "is-university-free-in-germany-2026-real-costs-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Als Student:in in Deutschland darfst du 120 volle Tage oder 240 halbe Tage pro Jahr arbeiten. Werkstudent:innen-Positionen (Assistenzjobs, Forschung innerhalb der Uni) zahlen stündlich 13–18 €. 20 Stunden pro Monat = 260–360 €. Dies deckt 25–35 % deiner Lebenshaltungskosten.",
      "subs": [
       {
        "old": "120 volle Tage oder 240 halbe Tage pro Jahr",
        "new": "bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "student-career-guide-in-germany-work-permits-job-types-salary-deductions-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Grundregel: 120 volle oder 240 halbe Arbeitstage",
      "subs": [
       {
        "old": "120 volle oder 240 halbe Arbeitstage",
        "new": "bis zu 140 Arbeitstage im Jahr"
       }
      ]
     },
     {
      "line": "Als internationaler Studierender hast du pro Jahr eine Arbeitserlaubnis für 120 volle Arbeitstage oder 240 halbe Arbeitstage. Ein voller Arbeitstag bedeutet 8 Stunden oder mehr, ein halber Arbeitstag bis zu 4 Stunden. Es ist entscheidend, diese Gesamtdauer nicht zu überschreiten; andernfalls könntest du Probleme mit deiner Aufenthaltserlaubnis bekommen. Diese Regel gilt sowohl für Teilzeitjobs als auch für Minijobs.",
      "subs": [
       {
        "old": "120 volle Arbeitstage",
        "new": "140 volle Arbeitstage"
       },
       {
        "old": "240 halbe Arbeitstage",
        "new": "280 halbe Arbeitstage"
       },
       {
        "old": "Ein voller Arbeitstag bedeutet 8 Stunden oder mehr, ein halber Arbeitstag bis zu 4 Stunden.",
        "new": "Gezählt wird in einem Arbeitstagekonto: Ein Tag mit bis zu 4 Stunden zählt als halber Tag; alternativ kann in der Vorlesungszeit eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden."
       }
      ]
     },
     {
      "line": "Für Studierende enthält dieses Gesetz Regelungen, die insbesondere die Jobsuche nach dem Abschluss erleichtern werden. Zum Beispiel werden Anwendungen wie die Chancenkarte es flexibler machen, nach dem Studium in Deutschland zu bleiben und einen Job zu suchen. Allerdings gab es bisher keine direkten großen Änderungen bezüglich der Arbeitserlaubnis während des Studiums (die 120/240-Tage-Regel und die 20-Stunden-Wochenbegrenzung). Obwohl über eine Erhöhung dieser Grenzen in Zukunft diskutiert wird, gelten derzeit die oben genannten Regeln. Für die aktuellsten und genauesten Informationen empfehle ich dir, die offiziellen Mitteilungen der Bundesregierung und die DAAD-Webseite zu verfolgen.",
      "replace": "Für Studierende enthält dieses Gesetz Regelungen, die insbesondere die Jobsuche nach dem Abschluss erleichtern. Zum Beispiel macht die **Chancenkarte** es flexibler, nach dem Studium in Deutschland zu bleiben und einen Job zu suchen. Auch bei der **Arbeitserlaubnis während des Studiums** hat sich etwas geändert: Seit dem 01.03.2024 dürfen Studierende aus Nicht-EU-Staaten bis zu 140 Arbeitstage im Jahr arbeiten (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag). Die frühere 120/240-Tage-Regel ist am 29.02.2024 ausgelaufen. Die 20 Stunden pro Woche sind keine aufenthaltsrechtliche Grenze, sondern die sozialversicherungsrechtliche Bedingung für den Werkstudentenstatus. Für die aktuellsten und genauesten Informationen empfehle ich dir, die offiziellen Mitteilungen der Bundesregierung und die [DAAD-Webseite](https://www.daad.de/de/) zu verfolgen."
     },
     {
      "line": "Was ist ein Minijob? Ein Minijob ist eine Beschäftigungsart, bei der dein monatliches Einkommen eine bestimmte Grenze nicht überschreitet (Stand 2024: 538 Euro). Arbeitnehmer in solchen Jobs sind weitgehend von Sozialversicherungsbeiträgen befreit.",
      "subs": [
       {
        "old": "Stand 2024",
        "new": "Stand 2026"
       },
       {
        "old": "538 Euro",
        "new": "603 Euro"
       }
      ]
     },
     {
      "line": "Begrenztes Einkommen: Du darfst monatlich nicht mehr als 538 Euro verdienen. Auch wenn du das Recht hast, diese Grenze in einer bestimmten Anzahl unvorhergesehener und vorübergehender Situationen im Jahr kurzzeitig zu überschreiten (zweimal im Jahr, wobei die Summe für zwei Monate 1076 Euro nicht übersteigen darf), kannst du nicht dauerhaft über dieser Grenze verdienen.",
      "subs": [
       {
        "old": "538 Euro",
        "new": "603 Euro (Stand 2026)"
       },
       {
        "old": "(zweimal im Jahr, wobei die Summe für zwei Monate 1076 Euro nicht übersteigen darf)",
        "new": "(zweimal im Jahr)"
       }
      ]
     },
     {
      "line": "In den Semesterferien: In den Ferienzeiten kannst du die 20-Stunden-Grenze pro Woche überschreiten und sogar Vollzeit arbeiten. Wichtig ist, dass du die jährliche Grenze von 120 vollen oder 240 halben Arbeitstagen nicht überschreitest.",
      "subs": [
       {
        "old": "die jährliche Grenze von 120 vollen oder 240 halben Arbeitstagen nicht überschreitest.",
        "new": "die jährliche Grenze von 140 Arbeitstagen (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag) nicht überschreitest."
       }
      ]
     },
     {
      "line": "Wenn du einen Minijob jedoch in den Semesterferien machst oder deine Gesamtarbeitstage die jährliche Grenze von 120 vollen / 240 halben Arbeitstagen nicht überschreiten, könnte die Situation anders sein. Kurz gesagt, jede Kombination, die im Semester die 20 Stunden überschreitet, wird dich sozialversicherungsrechtlich benachteiligen. Für die genauesten Informationen hierzu empfehle ich dir dringend, dich an deine Krankenkasse oder das Finanzamt zu wenden.",
      "subs": [
       {
        "old": "die jährliche Grenze von 120 vollen / 240 halben Arbeitstagen nicht überschreiten",
        "new": "die jährliche Grenze von 140 Arbeitstagen (Arbeitstagekonto) nicht überschreiten"
       }
      ]
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
      "line": "Jährliche Arbeitszeitbegrenzung: Die wöchentliche 20-Stunden-Regel gilt für die Arbeit während des Semesters. Über das Jahr verteilt haben Studierende das Recht, insgesamt 140 volle Tage oder 280 halbe Tage zu arbeiten (Änderung 2024 – zuvor 120/240). Ein voller Tag wird als 8 Stunden, ein halber Tag als 4 Stunden betrachtet. Dies bedeutet, dass Studierende in den Semesterferien mehr als 20 Stunden pro Woche, also Vollzeit, arbeiten können. Zum Beispiel kannst du diese jährliche Arbeitszeit nutzen, indem du während der Sommerferien 2 Monate lang Vollzeit arbeitest.",
      "subs": [
       {
        "old": "Ein voller Tag wird als 8 Stunden, ein halber Tag als 4 Stunden betrachtet.",
        "new": "Ein Tag mit bis zu 4 Stunden zählt als halber Tag (Arbeitstagekonto)."
       }
      ]
     },
     {
      "line": "Ein Minijob sind Tätigkeiten, bei denen das monatliche Einkommen unter einer bestimmten Grenze liegt. Aktuell (Informationen können sich ändern, überprüfe immer die aktuellen Grenzen) liegt diese Grenze in der Regel bei etwa 538 Euro.",
      "subs": [
       {
        "old": "in der Regel bei etwa 538 Euro",
        "new": "2026 bei 603 Euro"
       }
      ]
     },
     {
      "line": "Beziehung zur 20-Stunden-Regel: Ein Minijob unterliegt der wöchentlichen 20-Stunden-Regel. Das bedeutet, selbst wenn du einen Minijob hast, darf deine gesamte Arbeitszeit während des Semesters 20 Stunden pro Woche nicht überschreiten. Wenn du jedoch mehrere Minijobs hast, darf die Summe aller Minijob-Einkommen die monatliche Obergrenze (z. B. 538 Euro) nicht überschreiten, und deine gesamte Arbeitszeit darf 20 Stunden pro Woche nicht überschreiten.",
      "subs": [
       {
        "old": "z. B. 538 Euro",
        "new": "2026: 603 Euro"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "tips-for-international-students-searching-for-jobs-in-germany-2026-faqs-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Ja, in Deutschland kannst du mit einem Studentenvisum während des Semesters sowohl Teilzeit- als auch Minijobs ausüben, aber die gesamte Arbeitszeit aus all diesen Tätigkeiten darf die wöchentliche Grenze von 20 Stunden nicht überschreiten. Diese Regel geht davon aus, dass deine Ausbildung während des Semesters deine Hauptpriorität ist. Solange du die jährliche Arbeitserlaubnis von 140 vollen Tagen oder 280 halben Tagen (Änderung 2024 – zuvor 120/240) nicht überschreitest, kannst du in den Semesterferien mehr als 20 Stunden pro Woche arbeiten. Das Überschreiten der 20-Stunden-Grenze während des Semesters kann jedoch zu ernsthaften Problemen führen, die bis zum Entzug deiner Aufenthaltserlaubnis reichen können. Daher ist es von entscheidender Bedeutung, dass du die wöchentliche 20-Stunden-Grenze während des Semesters strikt einhältst.",
      "replace": "Ja, in Deutschland kannst du mit einem Studentenvisum während des Semesters sowohl Teilzeit- als auch Minijobs ausüben. Aufenthaltsrechtlich zählt seit dem 01.03.2024 keine Wochengrenze, sondern dein Konto von 140 Arbeitstagen im Jahr: Ein Tag mit bis zu 4 Stunden zählt als halber Tag, und während der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden – es gilt die günstigere Zählung. Stunden und Tage aus allen Jobs werden auf dieses Konto angerechnet. Die 20 Stunden pro Woche während der Vorlesungszeit sind die sozialversicherungsrechtliche Bedingung für den Werkstudentenstatus; außerdem erwarten Hochschulen und Behörden, dass das Studium deine Hauptpriorität bleibt. In den Semesterferien kannst du mehr arbeiten, solange dein 140-Tage-Konto nicht aufgebraucht ist. Überschreitest du das Konto, kann das ernsthafte Probleme mit deiner Aufenthaltserlaubnis nach sich ziehen. Daher solltest du deine Arbeitstage genau im Blick behalten."
     },
     {
      "line": "Ja, internationale Studierende, die für ein Masterstudium nach Deutschland kommen, haben in der Regel eine Arbeitserlaubnis von 20 Stunden pro Woche während des Semesters. Diese Erlaubnis ist Teil deines jährlichen Arbeitsrechts von 140 vollen Tagen oder 280 halben Tagen (Änderung 2024 – zuvor 120/240). Du kannst diese Arbeitserlaubnis problemlos bei Lieferdiensten wie Lieferando nutzen. Lieferando ist bei Studierenden eine sehr beliebte Option, da es flexible Arbeitszeiten bietet und in der Regel weniger Deutschkenntnisse erfordert.",
      "subs": [
       {
        "old": "in der Regel eine Arbeitserlaubnis von 20 Stunden pro Woche während des Semesters. Diese Erlaubnis ist Teil deines jährlichen Arbeitsrechts von 140 vollen Tagen oder 280 halben Tagen (Änderung 2024 – zuvor 120/240).",
        "new": "eine Arbeitserlaubnis für bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag). In der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden alternativ als 2,5 Arbeitstage gezählt werden."
       }
      ]
     },
     {
      "line": "Ja, Studierende können zusätzlich zu ihrem Recht, 20 Stunden pro Woche zu arbeiten, auch einen Minijob ausüben. Der wichtige Punkt hierbei ist jedoch, dass die gesamte Arbeitszeit aus all deinen Tätigkeiten, einschließlich des Minijobs, während des Semesters die wöchentliche Grenze von 20 Stunden nicht überschreiten darf. Wenn du beispielsweise in einem Job 15 Stunden pro Woche arbeitest, kannst du zusätzlich in einem Minijob maximal 5 Stunden pro Woche arbeiten. Achte auch darauf, dass dein Einkommen aus dem Minijob die monatliche Minijob-Grenze (z.B. 538 Euro) nicht überschreitet, da du sonst deinen Minijob-Status verlieren und Sozialversicherungsbeiträge zahlen müsstest.",
      "replace": "Ja, Studierende können zusätzlich zu einem Werkstudentenjob auch einen Minijob ausüben. Wichtig ist: Die Grenze von 20 Stunden pro Woche ist die sozialversicherungsrechtliche Bedingung für den Werkstudentenstatus, keine eigene aufenthaltsrechtliche Wochengrenze. Aufenthaltsrechtlich zählen die Stunden und Tage aus allen Jobs, einschließlich des Minijobs, auf dein Konto von 140 Arbeitstagen im Jahr (ein Tag mit bis zu 4 Stunden zählt als halber Tag). Plane deine Arbeitszeit deshalb so, dass dein Konto reicht und das Studium die Hauptsache bleibt. Achte auch darauf, dass dein Einkommen aus dem Minijob die monatliche Minijob-Grenze (603 Euro im Jahr 2026) nicht überschreitet, da du sonst deinen Minijob-Status verlieren und Sozialversicherungsbeiträge zahlen müsstest."
     },
     {
      "line": "Kann man in Deutschland mit einem Studentenvisum während des Semesters Teilzeit- und Minijobs ausüben? Gibt es Probleme, wenn die wöchentliche 20-Stunden-Grenze überschritten wird, solange die jährliche Arbeitserlaubnis von 240 halben Tagen nicht überschritten wird?",
      "subs": [
       {
        "old": "solange die jährliche Arbeitserlaubnis von 240 halben Tagen nicht überschritten wird?",
        "new": "solange das jährliche Arbeitstagekonto von 140 Tagen nicht überschritten wird?"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "germanys-student-work-limits-increased-you-can-earn-more-now-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Deutschland hat eine wichtige Entscheidung getroffen, die internationalen Studierenden ein Lächeln ins Gesicht zaubern wird! Ab dem 1. März 2024 und vollständig wirksam ab dem akademischen Jahr 2026 wurde dein jährliches Arbeitslimit von 120 auf 140 volle Tage (oder von 240 auf 280 halbe Tage) erhöht. Diese Neuerung ist Teil der Reformen des Fachkräfteeinwanderungsgesetzes (Skilled Immigration Act) und bietet dir mehr Flexibilität sowie bessere Verdienstmöglichkeiten. Die Regel, dass du während des Semesters maximal 20 Stunden pro Woche arbeiten darfst, bleibt unverändert. Dein potenzielles durchschnittliches Jahreseinkommen könnte von rund 10.500 Euro auf über 12.000 Euro steigen. Beachte auch, dass der Mindestlohn bis 2026 voraussichtlich 13,90 Euro pro Stunde betragen wird.",
      "subs": [
       {
        "old": "Ab dem 1. März 2024 und vollständig wirksam ab dem akademischen Jahr 2026 wurde",
        "new": "Zum 1. März 2024 wurde"
       },
       {
        "old": "Die Regel, dass du während des Semesters maximal 20 Stunden pro Woche arbeiten darfst, bleibt unverändert.",
        "new": "Ein Tag mit bis zu 4 Stunden zählt als halber Tag; alternativ kann in der Vorlesungszeit eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden."
       },
       {
        "old": "bis 2026 voraussichtlich 13,90 Euro pro Stunde betragen wird",
        "new": "2026 13,90 Euro pro Stunde beträgt"
       }
      ]
     },
     {
      "line": "Zuerst solltest du dein Budget und deine Jobsuche-Strategie unter Berücksichtigung dieser neuen Limits und Verdienstmöglichkeiten neu bewerten. Konzentriere dich besonders auf Werkstudentenpositionen, um sowohl finanziell als auch karrieretechnisch das Beste herauszuholen. Du kannst auch Minijobs (bis zu 538 Euro monatlich, meist steuerfrei) oder Studentische Hilfskraft (HiWi)-Positionen an deiner Universität in Betracht ziehen. Informiere dich genau, welcher Job am besten zu dir passt und wie sich das auf deine Steuern auswirkt. Überprüfe aktuelle Informationen immer bei offiziellen Quellen.",
      "subs": [
       {
        "old": "bis zu 538 Euro monatlich",
        "new": "2026 bis zu 603 Euro monatlich"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "2026-guide-to-tax-and-health-insurance-for-students-in-germany-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "Minijob: When doing a Minijob, as long as your monthly income does not exceed the Minijob limit (e.g., 538 Euro), your student health insurance will not be affected, and your affordable tariff will continue. Income from a Minijob does not increase your student health insurance premiums.",
      "subs": [
       {
        "old": "e.g., 538 Euro",
        "new": "603 Euro in 2026"
       }
      ]
     },
     {
      "line": "Minimum Wage (Mindestlohn): Germany has a legal minimum wage, which is updated periodically. Employers cannot pay you below this minimum wage. As of 2024, the minimum wage is a specific Euro amount per hour (e.g., 12.82 Euro; these figures may change). Check the current minimum wage before you start working.",
      "subs": [
       {
        "old": "As of 2024, the minimum wage is a specific Euro amount per hour (e.g., 12.82 Euro; ",
        "new": "In 2026, the minimum wage is 13.90 Euro per hour ("
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "after-an-english-masters-german-internships-jobs-and-career-opportunities-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "Work Permit (20-Hour Rule): \"Friends, I'm going to Germany for master's education, I have a 20-hour work permit, right? And can I use it for Liferando?\" Yes, as a master's student in Germany, you have a work permit for 120 full days or 240 half days per year (equivalent to 20 hours per week). You can use this permit for Liferando (food delivery), university assistantships (Werkstudent), or other jobs. However, this work permit should not affect your academic performance, and your education should be your priority.",
      "subs": [
       {
        "old": "120 full days or 240 half days per year (equivalent to 20 hours per week)",
        "new": "up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day; during the lecture period a week with up to 20 hours can be counted as 2.5 working days)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "bafog-alternatives-scholarships-in-germany-for-turkish-students-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "Types of Scholarships: Master's Scholarships: Typically last 10-24 months, offering a monthly living allowance of 934 Euros, travel expenses, and health insurance support. Ideal for Master's programs in Germany.",
      "subs": [
       {
        "old": "934 Euros",
        "new": "992 Euros"
       }
      ]
     },
     {
      "line": "Scholarship Amount: Covers monthly living expenses, book allowance, health insurance, and in some cases, travel expenses. Similar to DAAD scholarships, they can provide support of around 934 Euros per month for Master's students and 1,300 Euros for PhD students.",
      "subs": [
       {
        "old": "934 Euros",
        "new": "992 Euros"
       },
       {
        "old": "1,300 Euros",
        "new": "1,400 Euros"
       }
      ]
     },
     {
      "line": "Part-time Work (Werkstudent/Minijob): International students in Germany have a work permit for a certain period per year (usually 120 full days or 240 half days). Working as a \"Werkstudent\" (working student) in a company in your field, in particular, provides both financial support and valuable work experience. However, this income source is usually not considered sufficient when applying for a visa, and you are asked to show a Sperrkonto (blocked account).",
      "subs": [
       {
        "old": "usually 120 full days or 240 half days",
        "new": "up to 140 working days, Arbeitstagekonto; a day of up to 4 hours counts as half a day"
       }
      ]
     },
     {
      "line": "When applying for a student visa to Germany, the German government requires proof that you can cover your education and living expenses. This is where the Sperrkonto (blocked account) comes into play. This is a special bank account opened in your name where a certain amount of money (as of 2024, approximately 934 Euros per month, approximately 11,208 Euros per year) is blocked, and you can access a specific portion of it each month.",
      "subs": [
       {
        "old": "as of 2024, approximately 934 Euros per month, approximately 11,208 Euros per year",
        "new": "since 1 September 2024: 992 Euros per month, 11,904 Euros per year"
       }
      ]
     },
     {
      "line": "Important Note: A Sperrkonto is not a source of funding; it is merely a method of proving your financial solvency. If you receive a scholarship and the scholarship amount covers the monthly blocked account amount (934 Euros), you can submit your scholarship acceptance letter as financial proof during your visa application, and you may not need to open a Sperrkonto. However, if the scholarship amount is below this limit, you may need to supplement the difference with a Sperrkonto. For current and accurate information on this matter, you should contact the consulate or visa application center.",
      "subs": [
       {
        "old": "934 Euros",
        "new": "992 Euros"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "bringing-family-to-germany-with-a-student-visa-2026-turkish-student-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "Yes — no restrictions. With a family reunification/joint application residence permit (Aufenthaltstitel), the spouse can work full-time + in any sector. The 120/240-day limitation of your student visa does not apply to the spouse.",
      "subs": [
       {
        "old": "120/240-day limitation",
        "new": "140-working-day limitation"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "can-you-study-for-an-english-masters-in-germany-without-knowing-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "Work Permit (20-Hour Rule): \"Friends, I'm going to Germany for master's studies, I have a 20-hour work permit, right? And can I use it for Liferando?\" Yes, as a master's student in Germany, you have a work permit for 120 full days or 240 half days per year (which translates to 20 hours per week). You can use this permit for jobs like Liferando (food delivery), as a student assistant (Werkstudent) at the university, or in other roles. However, this work permit should not affect your academic performance, and your education should be your priority.",
      "subs": [
       {
        "old": "120 full days or 240 half days per year (which translates to 20 hours per week)",
        "new": "up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day; during the lecture period a week with up to 20 hours can be counted as 2.5 working days)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "daad-scholarship-master-phd-2026-step-by-step-application-guide-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "Scholarship Amount: A monthly scholarship of 861 Euros is paid to Master's students. In addition, health insurance, travel expenses, and in some cases, course fee support are also provided.",
      "subs": [
       {
        "old": "861 Euros",
        "new": "992 Euros"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "finding-a-wg-in-germany-your-comprehensive-guide-to-dorm-wg-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "With a student visa in Germany, there is a weekly 20-hour work limit. This limit applies during the semester. During holidays, you can work full-time (up to 40 hours per week). Annually, you are allowed to work 120 full days or 240 half days (each 4 hours or less). Teilzeit (part-time) and Minijob (jobs up to 538 Euro per month) can be combined, but you must ensure that your total working hours and earnings do not exceed these legal limits. Exceeding the limits can lead to the cancellation of your visa or legal problems. It is advisable to obtain the most current and accurate information on this matter from the Ausländerbehörde (Foreigners' Office) or your university's international office.",
      "replace": "With a student visa or residence permit for study purposes in Germany, you have been allowed since 01.03.2024 to work up to **140 working days a year**. These are counted in a working-day account (Arbeitstagekonto): a day of up to 4 hours counts as half a day; alternatively, during the lecture period a week of up to 20 hours can be counted as 2.5 working days, and the more favourable counting applies. During holidays, you can also work full-time, but those days are deducted from your account. The often-quoted **20-hour** weekly limit is not a residence-law limit; it is the social-insurance condition for Werkstudent status, and your studies must remain your main purpose. Teilzeit (part-time) and Minijob (jobs up to €603 per month in 2026) can be combined, but all hours and days count toward your 140-day account. Exceeding the account can lead to problems with your residence permit or legal problems. It is advisable to obtain the most current and accurate information on this matter from the Ausländerbehörde (Foreigners' Office) or your university's international office."
     },
     {
      "line": "Can I do a Teilzeit + Minijob during the semester with a student visa in Germany? Will there be a problem if I exceed the weekly 20-hour limit, as long as I don't exceed the annual 240 half-days of work permit?",
      "subs": [
       {
        "old": "as long as I don't exceed the annual 240 half-days of work permit?",
        "new": "as long as I don't exceed the annual 140-working-day allowance?"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "germany-bachelor-application-2026-complete-flow-guide-for-turkish-high-school-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "Sperrkonto (Blocked Account): International students studying in Germany must prove that they can cover the annual living expenses determined by the German government. This amount is approximately 11,208 Euros as of 2024 (934 Euros per month). You must deposit this money into a blocked account opened in Germany and be able to withdraw a certain portion monthly. This is one of the most important conditions for your visa application.",
      "subs": [
       {
        "old": "approximately 11,208 Euros as of 2024 (934 Euros per month)",
        "new": "11,904 Euros since 1 September 2024 (992 Euros per month)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "germany-it-job-search-2026-a-guide-for-turkish-graduates-and-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "So, can students also do a Minijob in addition to their 20-hour weekly work allowance? Generally, no. The 20-hour weekly work limit under a student visa covers all income-generating activities. A Minijob (jobs up to 538 Euros per month) does not fall outside this limit, meaning you must not exceed a total of 20 hours per week. For answers to these and similar questions, don't forget to visit our ApplyToGerman (AlmanyaUni) Frequently Asked Questions page.",
      "replace": "**So, can students also do a Minijob in addition to a Werkstudent job?** Generally, yes. Since 01.03.2024, the residence-law limit for non-EU students is an account of 140 working days a year (a day of up to 4 hours counts as half a day). The well-known 20-hour weekly limit is the social-insurance condition for Werkstudent status, not a separate visa limit. A Minijob (up to €603 per month in 2026) can be combined with a Werkstudent job, but all hours and days count toward your 140-day account, and your studies must remain your main purpose. For answers to these and similar questions, don't forget to visit our [ApplyToGerman (AlmanyaUni) Frequently Asked Questions](/en/faq) page."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "student-career-guide-in-germany-work-permits-job-types-salary-deductions-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "Basic Rule: 120 Full Days or 240 Half Days",
      "subs": [
       {
        "old": "120 Full Days or 240 Half Days",
        "new": "Up to 140 Working Days a Year"
       }
      ]
     },
     {
      "line": "As an international student, you're allowed to work 120 full days (ganze Arbeitstage) or 240 half days (halbe Arbeitstage) per year. A full day means working 8 hours or more, while a half day means working up to 4 hours. It's crucial not to exceed this total limit; otherwise, you could face issues with your residence permit. This rule applies to both part-time jobs and Minijobs.",
      "subs": [
       {
        "old": "120 full days (ganze Arbeitstage)",
        "new": "140 full days (ganze Arbeitstage)"
       },
       {
        "old": "240 half days (halbe Arbeitstage)",
        "new": "280 half days (halbe Arbeitstage)"
       },
       {
        "old": "A full day means working 8 hours or more, while a half day means working up to 4 hours.",
        "new": "These are counted in a working-day account (Arbeitstagekonto): a day of up to 4 hours counts as half a day; alternatively, during the lecture period a week with up to 20 hours can be counted as 2.5 working days."
       }
      ]
     },
     {
      "line": "During Semester Breaks (Semesterferien): You can work more than 20 hours a week during holidays, even full-time. The key is to stay within the annual limit of 120 full days or 240 half days.",
      "subs": [
       {
        "old": "120 full days or 240 half days",
        "new": "140 working days (Arbeitstagekonto; a day of up to 4 hours counts as half a day)"
       }
      ]
     },
     {
      "line": "For students, this law includes provisions that will particularly ease the job search process after graduation. For example, initiatives like the Chancenkarte (Opportunity Card) will make it more flexible to stay in Germany and look for a job after you graduate. However, there haven't been major direct changes to work permits during your studies (the 120/240-day rule and the 20-hour weekly limit) yet. While there are discussions about increasing these limits in the future, the rules we mentioned above still apply for now. For the most current and accurate information, I recommend keeping an eye on official announcements from the German Federal Government and checking the DAAD website.",
      "replace": "For students, this law includes provisions that particularly ease the job search process after graduation. For example, the **Chancenkarte (Opportunity Card)** makes it more flexible to stay in Germany and look for a job after you graduate. The rules for **work permits during your studies** have also changed: since 01.03.2024, non-EU students may work up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day). The old 120/240-day rule ended on 29.02.2024. The 20-hour weekly limit is not a residence-law limit but the social-insurance condition for Werkstudent status. For the most current and accurate information, I recommend keeping an eye on official announcements from the German Federal Government and checking the [DAAD website](https://www.daad.de/en/)."
     },
     {
      "line": "What is a Minijob? Minijob, a type of employment where your monthly income doesn't exceed a certain limit (as of 2024, this is 538 Euros). Employees in these jobs are largely exempt from social security contributions.",
      "subs": [
       {
        "old": "as of 2024, this is ",
        "new": "in 2026, this is "
       },
       {
        "old": "538 Euros",
        "new": "603 Euros"
       }
      ]
     },
     {
      "line": "Limited Income: You can't earn more than 538 Euros per month. While you have the right to briefly exceed this limit for a specific number of unforeseen and temporary situations during the year (twice a year, with the total for two months not exceeding 1076 Euros), you can't consistently earn above this limit.",
      "subs": [
       {
        "old": "538 Euros",
        "new": "603 Euros (2026)"
       },
       {
        "old": "(twice a year, with the total for two months not exceeding 1076 Euros)",
        "new": "(twice a year)"
       }
      ]
     },
     {
      "line": "However, if you do a Minijob during semester breaks or if your total working days don't exceed the annual 120 full days / 240 half days limit, the situation might be different. In short, any combination that exceeds 20 hours during the semester will put you at a disadvantage regarding social security. For the most accurate information on this, I strongly recommend contacting your health insurance provider (Krankenkasse) or the tax office (Finanzamt).",
      "subs": [
       {
        "old": "120 full days / 240 half days limit",
        "new": "140-working-day limit (Arbeitstagekonto)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "student-work-permit-in-germany-2026-20-hour-rule-and-types-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "Annual Work Limit: The weekly 20-hour rule covers work during the semester. Throughout the year, students have the right to work a total of 120 full days or 240 half days (2024 change — old 120/240) (increased to 140/280 in 2024). A full day is considered 8 hours, and a half day is 4 hours. This means that students can work full-time (Vollzeit) for more than 20 hours a week during semesterferien. For example, you can use this annual allowance by working full-time for 2 months during the summer break.",
      "subs": [
       {
        "old": "120 full days or 240 half days (2024 change — old 120/240) (increased to 140/280 in 2024)",
        "new": "up to 140 working days (Arbeitstagekonto; raised from 120 in 2024)"
       },
       {
        "old": "A full day is considered 8 hours, and a half day is 4 hours.",
        "new": "A day of up to 4 hours counts as half a day."
       }
      ]
     },
     {
      "line": "A Minijob refers to jobs where the monthly income remains below a certain limit. Currently (information may change, always check current limits), this limit is generally around 538 Euro.",
      "subs": [
       {
        "old": "generally around 538 Euro",
        "new": "603 Euro in 2026"
       }
      ]
     },
     {
      "line": "Relationship with the 20-Hour Rule: A Minijob is subject to the weekly 20-hour rule. This means that even if you have a Minijob, your total working time during the semester should not exceed 20 hours per week. However, if you have more than one Minijob, the total of all your Minijob incomes must not exceed the monthly upper limit (e.g., 538 Euro), and your total working time must not exceed 20 hours per week.",
      "subs": [
       {
        "old": "e.g., 538 Euro",
        "new": "603 Euro in 2026"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "tips-for-international-students-searching-for-jobs-in-germany-2026-faqs-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "Yes, in Germany, with a student visa, you can do both Teilzeit (part-time) and a Minijob during the semester, but the total working hours from all these jobs must not exceed 20 hours per week. This rule assumes that your main priority during the semester is your education. As long as you do not exceed the annual work permit of 140 full days or 280 half days (2024 change — previously 120/240), you can work more than 20 hours per week during semester breaks (Semesterferien). However, exceeding the 20-hour limit during the semester can lead to serious problems, potentially even the cancellation of your residence permit. Therefore, strictly adhering to the weekly 20-hour limit during the semester is critically important.",
      "replace": "Yes, in Germany, with a student visa, you can do both Teilzeit (part-time) and a Minijob during the semester. Since 01.03.2024, the residence-law limit is not a weekly cap but your account of 140 working days a year: a day of up to 4 hours counts as half a day, and during the lecture period a week of up to 20 hours can be counted as 2.5 working days, whichever counting is more favourable. Hours and days from all your jobs count toward this account. The 20 hours per week during the lecture period is the social-insurance condition for Werkstudent status, and universities and authorities expect your education to remain your main priority. During semester breaks (Semesterferien), you can work more as long as your 140-day account is not used up. Exceeding the account can lead to serious problems with your residence permit, so it is important to keep careful track of your working days."
     },
     {
      "line": "Yes, international students going to Germany for Master's education generally have a weekly 20-hour work permit during the semester. This permit is part of your annual right to work 140 full days or 280 half days (2024 change — previously 120/240). You can easily use this work permit for delivery services like Liferando (Lieferando). Liferando is a very popular option among students due to its flexible working hours and generally requiring less German language knowledge.",
      "subs": [
       {
        "old": "generally have a weekly 20-hour work permit during the semester. This permit is part of your annual right to work 140 full days or 280 half days (2024 change — previously 120/240).",
        "new": "have a work permit for up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day). During the lecture period, a week with up to 20 hours can alternatively be counted as 2.5 working days."
       }
      ]
     },
     {
      "line": "Yes, students can do a Minijob in addition to their weekly 20-hour work right. However, the important point here is that the total working hours from all your jobs, including the Minijob, must not exceed 20 hours per week during the semester. For example, if you work 15 hours a week in one job, you can work a maximum of 5 additional hours per week in a Minijob. Furthermore, you should also ensure that the income you earn from the Minijob does not exceed the monthly Minijob limit (e.g., 538 Euro), otherwise you might lose your Minijob status and have to pay social security contributions.",
      "replace": "Yes, students can do a Minijob in addition to a Werkstudent job. The important point is that the 20-hour weekly limit is the social-insurance condition for Werkstudent status, not a separate residence-law weekly limit. Under residence law, the hours and days from all your jobs, including the Minijob, count toward your account of 140 working days a year (a day of up to 4 hours counts as half a day). So plan your working time so that your account lasts and your studies remain your main purpose. Furthermore, you should also ensure that the income you earn from the Minijob does not exceed the monthly Minijob limit (€603 in 2026), otherwise you might lose your Minijob status and have to pay social security contributions."
     },
     {
      "line": "Can I do a Teilzeit + Minijob during the semester with a student visa in Germany? Will there be a problem if I exceed the weekly 20-hour limit, as long as I don't exceed the 240 half-days of work permit per year?",
      "subs": [
       {
        "old": "as long as I don't exceed the 240 half-days of work permit per year?",
        "new": "as long as I don't exceed the 140 working days a year?"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "germanys-student-work-limits-increased-you-can-earn-more-now-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "Germany has made a significant decision that will benefit international students! Starting March 1, 2024, and fully effective by the 2026 academic year, your annual work limit has been raised from 120 full days to 140 full days (or from 240 half days to 280 half days). This is part of the Fachkräfteeinwanderungsgesetz (Skilled Immigration Act) reforms, offering you more flexibility and better earning opportunities. While the rule of working a maximum of 20 hours per week during the semester remains, your potential average annual earnings could increase from around 10,500 Euros to over 12,000 Euros. Keep in mind that by 2026, the minimum wage is projected to be 13.90 Euros per hour.",
      "subs": [
       {
        "old": "Starting March 1, 2024, and fully effective by the 2026 academic year, your",
        "new": "Since March 1, 2024, your"
       },
       {
        "old": "While the rule of working a maximum of 20 hours per week during the semester remains, your",
        "new": "A day of up to 4 hours counts as half a day, and during the lecture period a week with up to 20 hours can alternatively be counted as 2.5 working days. Your"
       },
       {
        "old": "by 2026, the minimum wage is projected to be 13.90 Euros per hour",
        "new": "in 2026, the minimum wage is 13.90 Euros per hour"
       }
      ]
     },
     {
      "line": "First, you should re-evaluate your budget and job search strategy, considering these new limits and earning potential. Focusing on Werkstudent positions can give you the best return, both financially and for your career development. You can also look into Minijob (a job with earnings up to 538 Euros per month, typically tax-free) or Studentische Hilfskraft (HiWi) (university assistant positions) within your university. Make sure to research which type of job suits you best and understand the tax implications. Always verify current information from official sources.",
      "subs": [
       {
        "old": "up to 538 Euros per month",
        "new": "up to 603 Euros per month in 2026"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "2026-guide-to-tax-and-health-insurance-for-students-in-germany",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Minijob: Minijob yaparken, aylık geliriniz Minijob sınırını (örn. 538 Euro) aşmadığı sürece öğrenci sağlık sigortanız etkilenmez ve uygun fiyatlı tarifeniz devam eder. Minijob'dan elde ettiğiniz gelir, öğrenci sağlık sigortası primlerinizi artırmaz.",
      "subs": [
       {
        "old": "örn. 538 Euro",
        "new": "2026'da 603 Euro"
       }
      ]
     },
     {
      "line": "Asgari Ücret (Mindestlohn): Almanya'da yasal bir asgari ücret uygulaması vardır ve bu ücret belirli aralıklarla güncellenir. İşverenler, size bu asgari ücretin altında ödeme yapamazlar. 2024 itibarıyla asgari ücret saatlik belirli bir Euro miktarındadır (örn. 12,82 Euro, bu rakamlar değişebilir). Çalışmaya başlamadan önce güncel asgari ücreti kontrol edin.",
      "subs": [
       {
        "old": "2024 itibarıyla asgari ücret saatlik belirli bir Euro miktarındadır (örn. 12,82 Euro, ",
        "new": "2026'da asgari ücret saatlik 13,90 Euro'dur ("
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "after-an-english-masters-german-internships-jobs-and-career-opportunities",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Çalışma İzni (20 Saat Kuralı): \"Arkadaşlar almanyaya master egitimi icin gidiyorum 20 saatlik calisma iznim var di mi simdi benim ? ve ben onu liferando da kullanabilir miyim ?\" Evet, Almanya'da master öğrencisi olarak yılda 120 tam gün veya 240 yarım gün (haftada 20 saate denk gelir) çalışma izniniz vardır. Bu izni Liferando (yemek dağıtım), üniversite içi asistanlık (Werkstudent) veya başka bir işte kullanabilirsiniz. Ancak bu çalışma izni, akademik performansınızı etkilememeli ve önceliğiniz eğitiminiz olmalıdır.",
      "subs": [
       {
        "old": "120 tam gün veya 240 yarım gün (haftada 20 saate denk gelir)",
        "new": "140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır; ders döneminde 20 saate kadar çalışılan bir hafta 2,5 iş günü sayılabilir)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "aps-certificate-turkey-2026-germany-university-guide",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Cevap: \"Denklik için hesapta para\" ifadesi genellikle karıştırılan bir konudur. APS sertifikası almak veya Anabin'de denklik kontrolü yapmak için hesabınızda belirli bir miktarda para bulunması gerekmez. Hesapta para bulundurma zorunluluğu, Almanya'ya öğrenci vizesi başvurusu yaparken finansal yeterliliğinizi kanıtlamak amacıyla istenen Sperrkonto (bloke hesap) ile ilgilidir. Bu hesapta, Almanya'da bir yıl boyunca geçim masraflarınızı karşılayacak miktarda paranın (2024 yılı için aylık yaklaşık 934 Euro, yıllık 11.208 Euro civarıydı, 2026 için güncel bilgiyi Almanya Konsolosluğu veya Büyükelçiliği'nin sitesinden kontrol edin) bloke edilmesi gerekir. APS süreci ve bloke hesap tamamen farklı iki konudur.",
      "subs": [
       {
        "old": "2024 yılı için aylık yaklaşık 934 Euro, yıllık 11.208 Euro civarıydı, 2026 için güncel bilgiyi",
        "new": "01.09.2024'ten beri aylık 992 Euro, yıllık 11.904 Euro; güncel bilgiyi"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "bafog-alternatives-scholarships-in-germany-for-turkish-students",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Burs Türleri: Yüksek Lisans Bursları: Genellikle 10-24 ay süreli, aylık 934 Euro yaşam gideri, seyahat masrafları ve sağlık sigortası desteği sunar. Almanya'daki yüksek lisans programları için idealdir.",
      "subs": [
       {
        "old": "934 Euro",
        "new": "992 Euro"
       }
      ]
     },
     {
      "line": "Burs Miktarı: Aylık yaşam gideri, kitap parası, sağlık sigortası ve bazı durumlarda seyahat masraflarını kapsar. DAAD burslarına benzer şekilde, yüksek lisans öğrencileri için aylık 934 Euro, doktora öğrencileri için 1.300 Euro civarında destek sağlayabilirler.",
      "subs": [
       {
        "old": "934 Euro",
        "new": "992 Euro"
       },
       {
        "old": "1.300 Euro",
        "new": "1.400 Euro"
       }
      ]
     },
     {
      "line": "Kısmi Zamanlı İş (Werkstudent/Minijob): Almanya'da uluslararası öğrencilerin yılda belirli bir süre (genellikle 120 tam gün veya 240 yarım gün) çalışma izni vardır. Özellikle \"Werkstudent\" olarak kendi alanında bir şirkette çalışmak, hem finansal destek sağlar hem de değerli iş deneyimi kazandırır. Ancak vize başvurusu yaparken bu gelir kaynağı genellikle yeterli kabul edilmez ve bir Sperrkonto (bloke hesap) göstermen istenir.",
      "subs": [
       {
        "old": "genellikle 120 tam gün veya 240 yarım gün",
        "new": "140 iş günü; Arbeitstagekonto, 4 saate kadar çalışılan gün yarım gün sayılır"
       }
      ]
     },
     {
      "line": "Almanya'ya öğrenci vizesi başvurusu yaparken, Alman hükümeti senden eğitim ve yaşam masraflarını karşılayabileceğini gösteren bir kanıt ister. İşte bu noktada Sperrkonto (bloke hesap) devreye girer. Bu, belirli bir miktar paranın (2024 itibarıyla aylık yaklaşık 934 Euro, yıllık yaklaşık 11.208 Euro) senin adına açılmış özel bir banka hesabında bloke edildiği ve her ay belirli bir kısmına erişebileceğin bir hesaptır.",
      "subs": [
       {
        "old": "2024 itibarıyla aylık yaklaşık 934 Euro, yıllık yaklaşık 11.208 Euro",
        "new": "01.09.2024'ten beri aylık 992 Euro, yıllık 11.904 Euro"
       }
      ]
     },
     {
      "line": "Önemli Not: Sperrkonto bir finansman kaynağı değildir, sadece finansal yeterliliğini kanıtlama yöntemidir. Eğer burs alıyorsan ve burs miktarı aylık bloke hesap miktarını (934 Euro) karşılıyorsa, vize başvurusu sırasında burs kabul mektubunu finansal kanıt olarak sunabilirsin ve Sperrkonto açmana gerek kalmayabilir. Ancak burs miktarı bu limitin altındaysa, aradaki farkı Sperrkonto ile tamamlaman gerekebilir. Bu konuda konsolosluk veya vize başvuru merkezi ile iletişime geçerek güncel ve doğru bilgiyi almalısın.",
      "subs": [
       {
        "old": "934 Euro",
        "new": "992 Euro"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "bringing-family-to-germany-with-a-student-visa-2026-turkish-student",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Evet — kısıtlama yok. Aile birleşimi/birlikte başvuru oturum izniyle eş tam zamanlı + her sektörde çalışabilir. Senin öğrenci vizenin 120/240 gün sınırlaması eşi bağlamaz.",
      "subs": [
       {
        "old": "120/240 gün sınırlaması",
        "new": "140 iş günü sınırlaması"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "daad-scholarship-master-phd-2026-step-by-step-application-guide",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Burs Miktarı: Master öğrencileri için aylık 861 Euro burs ödenir. Buna ek olarak sağlık sigortası, seyahat masrafları ve bazı durumlarda kurs ücreti desteği de sağlanır.",
      "subs": [
       {
        "old": "861 Euro",
        "new": "992 Euro"
       }
      ]
     },
     {
      "line": "Aylık Burs: Master öğrencileri için aylık €861, Doktora öğrencileri için aylık €1.200. Bu miktar, Almanya'daki temel yaşam giderlerinizi karşılamanıza yardımcı olur.",
      "subs": [
       {
        "old": "€861",
        "new": "€992"
       },
       {
        "old": "€1.200",
        "new": "€1.400"
       }
      ]
     },
     {
      "line": "Almanya'da Çalışma İzni: DAAD bursiyerleri, öğrenci vizesi kapsamında Almanya'da yasal olarak belirli saatlerde çalışma iznine sahiptir. \"Oradaki burs ve çalışma imkanlarıyla ilgili bilginiz var mı?\" sorusuna cevaben: Evet, bursunuzla birlikte çalışabilirsiniz, ancak akademik çalışmalarınızı aksatmayacak şekilde ve yasal sınırlara uyarak. Genellikle yıllık 120 tam gün veya 240 yarım gün çalışma hakkınız bulunur.",
      "subs": [
       {
        "old": "Genellikle yıllık 120 tam gün veya 240 yarım gün",
        "new": "Yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "finding-a-wg-in-germany-your-comprehensive-guide-to-dorm-wg",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Almanya'da öğrenci vizesi ile dönem içinde teilzeit + minijob yapılabilir mi? Haftalık 20 saat sınırı varya aşma durumunda problem olur mu yılda 240 yarım gün çalışma iznini geçmediği sürece sıkıntı yaşar mıyım?",
      "subs": [
       {
        "old": "yılda 240 yarım gün çalışma iznini",
        "new": "yılda 140 iş günlük çalışma iznini"
       }
      ]
     },
     {
      "line": "Almanya'da öğrenci vizesiyle haftalık 20 saatlik bir çalışma sınırı vardır. Bu sınır, ders dönemi boyunca geçerlidir. Tatillerde ise tam zamanlı (haftalık 40 saate kadar) çalışabilirsiniz. Yıllık olarak ise 120 tam gün veya 240 yarım gün (her biri 4 saat veya daha az) çalışma izniniz vardır. Teilzeit (yarı zamanlı) ve Minijob (aylık 538 Euro'ya kadar olan işler) bir arada yapılabilir, ancak toplam çalışma sürenizin ve kazancınızın bu yasal sınırları aşmamasına dikkat etmelisiniz. Sınırları aşmak, vizenizin iptaline veya yasal sorunlara yol açabilir. Bu konuda en güncel ve doğru bilgiyi Yabancılar Dairesi'nden (Ausländerbehörde) veya üniversitenizin uluslararası ofisinden almanız tavsiye edilir.",
      "replace": "Almanya'da öğrenci vizesi veya öğrenim amaçlı oturum izniyle 01.03.2024'ten bu yana **yılda 140 iş günü** çalışabilirsiniz. Bu süre bir iş günü hesabıyla (Arbeitstagekonto) takip edilir: 4 saate kadar çalışılan gün yarım gün sayılır; ders döneminde ise haftada 20 saate kadar çalışılan bir hafta alternatif olarak 2,5 iş günü sayılabilir ve sizin için daha avantajlı olan hesaplama uygulanır. Tatillerde tam zamanlı da çalışabilirsiniz, ancak bu günler hesabınızdan düşülür. Sık duyulan haftalık **20 saat** kuralı oturum hukukuna ait bir sınır değil, Werkstudent statüsünün sosyal sigorta koşuludur; ayrıca öğreniminizin asıl amaç olarak kalması gerekir. Teilzeit (yarı zamanlı) ve Minijob (2026'da aylık 603 Euro'ya kadar olan işler) bir arada yapılabilir, ancak tüm saat ve günler 140 günlük hesabınıza sayılır. Hesabı aşmak, oturum izninizle ilgili sorunlara veya yasal sorunlara yol açabilir. Bu konuda en güncel ve doğru bilgiyi Yabancılar Dairesi'nden (Ausländerbehörde) veya üniversitenizin uluslararası ofisinden almanız tavsiye edilir."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "germany-bachelor-application-2026-complete-flow-guide-for-turkish-high-school",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Sperrkonto (Bloke Hesap): Almanya'da öğrenim görecek uluslararası öğrencilerin, Alman hükümeti tarafından belirlenen yıllık yaşam masraflarını karşılayabileceklerini kanıtlamaları gerekir. Bu miktar 2024 itibarıyla yaklaşık 11.208 Euro'dur (aylık 934 Euro). Bu parayı Almanya'da açacağınız bloke bir hesaba yatırmanız ve aylık olarak belirli bir kısmını çekebilmeniz gerekir. Bu, vize başvurunuzun en önemli şartlarından biridir.",
      "subs": [
       {
        "old": "2024 itibarıyla yaklaşık 11.208 Euro'dur (aylık 934 Euro)",
        "new": "01.09.2024'ten beri 11.904 Euro'dur (aylık 992 Euro)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "germany-it-job-search-2026-a-guide-for-turkish-graduates-and",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Peki, öğrenciler haftalık 20 saat çalışma hakkının yanı sıra Minijob yapabiliyor mu? Genellikle hayır. Öğrenci vizesi kapsamında haftalık 20 saatlik çalışma sınırı, tüm gelir getirici faaliyetleri kapsar. Minijob (aylık 538 Euro'ya kadar olan işler) bu sınırın dışına çıkmaz, yani toplamda haftalık 20 saati aşmaman gerekir. Bu ve benzeri soruların yanıtları için ApplyToGerman (AlmanyaUni) Sıkça Sorulan Sorular sayfamızı ziyaret etmeyi unutma.",
      "replace": "**Peki, öğrenciler Werkstudent işinin yanı sıra Minijob yapabiliyor mu?** Genellikle evet. AB dışından gelen öğrenciler için 01.03.2024'ten bu yana oturum hukukundaki sınır, yılda 140 iş günlük bir hesaptır (4 saate kadar çalışılan gün yarım gün sayılır). Bilinen haftalık 20 saat sınırı ise ayrı bir vize sınırı değil, Werkstudent statüsünün sosyal sigorta koşuludur. Minijob (2026'da aylık 603 Euro'ya kadar olan işler) Werkstudent işiyle birleştirilebilir; ancak tüm saat ve günler 140 günlük hesabına sayılır ve öğreniminin asıl amaç olarak kalması gerekir. Bu ve benzeri soruların yanıtları için [ApplyToGerman (AlmanyaUni) Sıkça Sorulan Sorular](/tr/faq) sayfamızı ziyaret etmeyi unutma."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "germany-student-visa-2026-application-steps-documents-rejection",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Bu federal düzeyde belirlenmiş minimum — değişmiyor. 2025'te 11.208 €'dan 11.904 €'a yükseldi. Bazı kişiler Verpflichtungserklärung yoluyla bu rakamı düşürmeye çalışsa da konsolosluklar genellikle Sperrkonto'yu güvenli görür.",
      "subs": [
       {
        "old": "2025'te",
        "new": "01.09.2024'te"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "is-university-free-in-germany-2026-real-costs",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Almanya'da öğrenci olarak yılda 120 tam gün veya 240 yarım gün çalışabilirsin. Werkstudent pozisyonları (asistan iş, üni içinde araştırma) saatlik 13–18 €. Aylık 20 saat = 260–360 €. Bu, yaşam maliyetinin %25–35'ini karşılar.",
      "subs": [
       {
        "old": "120 tam gün veya 240 yarım gün",
        "new": "140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "part-time-jobs-germany-international-students-guide",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "AB-dışı (Türkiye dahil) öğrenciler için yıllık çalışma hakkı 2026'da 120 tam günden 140 tam güne çıkarıldı:",
      "subs": [
       {
        "old": "2026'da",
        "new": "1 Mart 2024'te"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "student-career-guide-in-germany-work-permits-job-types-salary-deductions",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Temel Kural: 120 Tam Gün veya 240 Yarım Gün",
      "subs": [
       {
        "old": "120 Tam Gün veya 240 Yarım Gün",
        "new": "Yılda 140 İş Günü"
       }
      ]
     },
     {
      "line": "Bir uluslararası öğrenci olarak yılda 120 tam gün (ganze Arbeitstage) veya 240 yarım gün (halbe Arbeitstage) çalışma iznin bulunur. Bir tam gün 8 saat ve üzeri çalışmayı, yarım gün ise 4 saate kadar çalışmayı ifade eder. Bu toplam süreyi aşmaman hayati önem taşır; aksi takdirde oturum izninle ilgili sorunlar yaşayabilirsin. Bu kural, hem yarı zamanlı işler (part-time) hem de minijob'lar için geçerlidir.",
      "subs": [
       {
        "old": "120 tam gün (ganze Arbeitstage)",
        "new": "140 tam gün (ganze Arbeitstage)"
       },
       {
        "old": "240 yarım gün (halbe Arbeitstage)",
        "new": "280 yarım gün (halbe Arbeitstage)"
       },
       {
        "old": "Bir tam gün 8 saat ve üzeri çalışmayı, yarım gün ise 4 saate kadar çalışmayı ifade eder.",
        "new": "Bu günler bir iş günü hesabında (Arbeitstagekonto) sayılır: 4 saate kadar çalışılan gün yarım gün sayılır; alternatif olarak ders döneminde 20 saate kadar çalışılan bir hafta 2,5 iş günü sayılabilir."
       }
      ]
     },
     {
      "line": "Sömestr Tatillerinde (Semesterferien): Tatil dönemlerinde haftalık 20 saat sınırını aşabilir, hatta tam zamanlı çalışabilirsin. Önemli olan, yıllık 120 tam gün veya 240 yarım gün limitini aşmamaktır.",
      "subs": [
       {
        "old": "120 tam gün veya 240 yarım gün limitini",
        "new": "140 iş günü limitini (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır)"
       }
      ]
     },
     {
      "line": "Öğrenciler için bu yasa özellikle mezuniyet sonrası iş arama süreçlerini kolaylaştıracak maddeler içeriyor. Örneğin, Chancenkarte (Fırsat Kartı) gibi uygulamalarla mezuniyet sonrası Almanya'da kalıp iş aramak daha esnek hale gelecek. Ancak, öğrencilik döneminde çalışma izni (120/240 gün kuralı ve haftalık 20 saat sınırı) konusunda doğrudan büyük bir değişiklik henüz gerçekleşmedi. Gelecekte bu sınırların artırılması yönünde tartışmalar olsa da, şu an için yukarıda bahsettiğimiz kurallar geçerliliğini koruyor. Güncel ve en doğru bilgi için Federal Almanya Hükümeti'nin resmi duyurularını ve DAAD web sitesini takip etmeni öneririm.",
      "replace": "Öğrenciler için bu yasa özellikle mezuniyet sonrası iş arama süreçlerini kolaylaştıran maddeler içeriyor. Örneğin, **Chancenkarte (Fırsat Kartı)** ile mezuniyet sonrası Almanya'da kalıp iş aramak daha esnek hale geliyor. **Öğrencilik döneminde çalışma izni** konusunda da değişiklik oldu: 01.03.2024'ten bu yana AB dışından gelen öğrenciler yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır) çalışabiliyor. Eski 120/240 gün kuralı 29.02.2024'te sona erdi. Haftalık 20 saat ise oturum hukukuna ait bir sınır değil, Werkstudent statüsünün sosyal sigorta koşuludur. Güncel ve en doğru bilgi için Federal Almanya Hükümeti'nin resmi duyurularını ve [DAAD web sitesini](https://www.daad.de/en/) takip etmeni öneririm."
     },
     {
      "line": "Minijob nedir? Minijob, aylık belirli bir gelir sınırını aşmayan (2024 itibarıyla 538 Euro) iş türüdür. Bu tür işlerde çalışanlar, sosyal güvenlik primlerinin büyük bir kısmından muaf tutulur.",
      "subs": [
       {
        "old": "2024 itibarıyla ",
        "new": "2026'da "
       },
       {
        "old": "538 Euro",
        "new": "603 Euro"
       }
      ]
     },
     {
      "line": "Sınırlı Gelir: Aylık 538 Euro'yu aşamazsın. Yıl içinde belirli bir sayıda, öngörülemeyen ve geçici durumlar için bu sınırı kısa süreli aşma hakkın olsa da (yılda iki kez, iki aylık maaş toplamı 1076 Euro'yu geçmeyecek şekilde), sürekli olarak bu sınırın üzerinde kazanamazsın.",
      "subs": [
       {
        "old": "Aylık 538 Euro'yu",
        "new": "2026'da aylık 603 Euro'yu"
       },
       {
        "old": "(yılda iki kez, iki aylık maaş toplamı 1076 Euro'yu geçmeyecek şekilde)",
        "new": "(yılda iki kez)"
       }
      ]
     },
     {
      "line": "Ancak, minijob'u semester tatillerinde yaparsan veya toplam çalışma gün sayın yıllık 120 tam gün / 240 yarım gün sınırını aşmıyorsa, durum farklı olabilir. Kısacası, ders döneminde 20 saati aşan herhangi bir kombinasyon, sosyal güvenlik açısından seni dezavantajlı duruma düşürür. Bu konuda en doğru bilgiyi almak için bağlı olduğun sağlık sigortası kurumu (Krankenkasse) veya vergi dairesi (Finanzamt) ile iletişime geçmeni şiddetle tavsiye ederim.",
      "subs": [
       {
        "old": "120 tam gün / 240 yarım gün sınırını",
        "new": "140 iş günü sınırını (Arbeitstagekonto)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "student-matters-in-germany-the-20-hour-rule-tax-and-health",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Yıllık Çalışma Sınırı: Haftalık 20 saat kuralı, dönem içindeki çalışmayı kapsar. Yıl boyunca ise öğrencilerin toplamda 120 tam gün veya 140 tam gün veya 280 yarım gün (2024 değişikliği — eski 120/240) (2024'te 140/280'e çıkarıldı) çalışma hakkı bulunur. Bir tam gün 8 saat, bir yarım gün ise 4 saat olarak kabul edilir. Bu, öğrencilerin dönem tatillerinde (semesterferien) haftada 20 saati aşan sürelerde tam zamanlı (Vollzeit) çalışabileceği anlamına gelir. Örneğin, yaz tatilinde 2 ay boyunca tam zamanlı çalışarak bu yıllık izninizi kullanabilirsiniz.",
      "subs": [
       {
        "old": "120 tam gün veya 140 tam gün veya 280 yarım gün (2024 değişikliği — eski 120/240) (2024'te 140/280'e çıkarıldı)",
        "new": "140 iş günü (Arbeitstagekonto; 2024'te 120'den 140'a çıkarıldı)"
       },
       {
        "old": "Bir tam gün 8 saat, bir yarım gün ise 4 saat olarak kabul edilir.",
        "new": "4 saate kadar çalışılan gün yarım gün sayılır."
       }
      ]
     },
     {
      "line": "Minijob, aylık belirli bir gelir sınırının altında kalan işlerdir. Güncel durumda (bilgiler değişebilir, her zaman güncel limitleri kontrol edin), bu sınır genellikle 538 Euro civarındadır.",
      "subs": [
       {
        "old": "genellikle 538 Euro civarındadır",
        "new": "2026'da 603 Euro'dur"
       }
      ]
     },
     {
      "line": "20 Saat Kuralı ile İlişkisi: Minijob, haftalık 20 saat kuralına tabidir. Yani, Minijob yapsanız bile, dönem içinde toplam çalışma süreniz haftalık 20 saati geçmemelidir. Ancak, birden fazla Minijob yapıyorsanız, tüm Minijob gelirlerinizin toplamı aylık üst sınırı (örn. 538 Euro) geçmemeli ve toplam çalışma süreniz haftalık 20 saati aşmamalıdır.",
      "subs": [
       {
        "old": "örn. 538 Euro",
        "new": "2026'da 603 Euro"
       }
      ]
     },
     {
      "line": "Minijob: Minijob yaparken, aylık geliriniz Minijob sınırını (örn. 538 Euro) aşmadığı sürece öğrenci sağlık sigortanız etkilenmez ve uygun fiyatlı tarifeniz devam eder. Minijob'dan elde ettiğiniz gelir, öğrenci sağlık sigortası primlerinizi artırmaz.",
      "subs": [
       {
        "old": "örn. 538 Euro",
        "new": "2026'da 603 Euro"
       }
      ]
     },
     {
      "line": "Asgari Ücret (Mindestlohn): Almanya'da yasal bir asgari ücret uygulaması vardır ve bu ücret belirli aralıklarla güncellenir. İşverenler, size bu asgari ücretin altında ödeme yapamazlar. 2024 itibarıyla asgari ücret saatlik belirli bir Euro miktarındadır (örn. 12,82 Euro, bu rakamlar değişebilir). Çalışmaya başlamadan önce güncel asgari ücreti kontrol edin.",
      "subs": [
       {
        "old": "2024 itibarıyla asgari ücret saatlik belirli bir Euro miktarındadır (örn. 12,82 Euro, ",
        "new": "2026'da asgari ücret saatlik 13,90 Euro'dur ("
       }
      ]
     },
     {
      "line": "Almanya'da öğrenci vizesi ile dönem içinde teilzeit + minijob yapılabilir mi? Haftalık 20 saat sınırı aşma durumunda problem olur mu yılda 240 yarım gün çalışma iznini geçmediği sürece sıkıntı yaşar mıyım?",
      "subs": [
       {
        "old": "yılda 240 yarım gün çalışma iznini",
        "new": "yılda 140 iş günlük çalışma iznini"
       }
      ]
     },
     {
      "line": "Evet, Almanya'da öğrenci vizesiyle dönem içinde hem Teilzeit (part-time) hem de Minijob yapabilirsiniz, ancak tüm bu işlerden elde ettiğiniz toplam çalışma süresi haftalık 20 saati geçmemelidir. Bu kural, dönem içinde asıl önceliğinizin eğitiminiz olduğunu varsayar. Yıllık 140 tam gün veya 280 yarım gün (2024 değişikliği — eski 120/240) çalışma iznini aşmadığınız sürece, dönem tatillerinde (Semesterferien) haftalık 20 saatin üzerinde çalışabilirsiniz. Ancak dönem içinde 20 saat sınırını aşmak, oturum izninizin iptaline kadar gidebilecek ciddi sorunlara yol açabilir. Bu nedenle, dönem içinde haftalık 20 saat sınırına harfiyen uymanız kritik öneme sahiptir.",
      "replace": "Evet, Almanya'da öğrenci vizesiyle dönem içinde hem Teilzeit (part-time) hem de Minijob yapabilirsiniz. 01.03.2024'ten bu yana oturum hukukundaki sınır haftalık bir tavan değil, yılda 140 iş günlük hesabınızdır: 4 saate kadar çalışılan gün yarım gün sayılır; ders döneminde haftada 20 saate kadar çalışılan bir hafta 2,5 iş günü olarak da sayılabilir ve daha avantajlı olan hesaplama uygulanır. Tüm işlerinizdeki saat ve günler bu hesaba sayılır. Ders döneminde haftalık 20 saat, Werkstudent statüsünün sosyal sigorta koşuludur; ayrıca üniversiteler ve makamlar öğreniminizin asıl önceliğiniz olarak kalmasını bekler. Dönem tatillerinde (Semesterferien), 140 günlük hesabınız dolmadığı sürece daha fazla çalışabilirsiniz. Hesabı aşmak, oturum izninizle ilgili ciddi sorunlara yol açabilir. Bu nedenle çalıştığınız günleri dikkatle takip etmeniz kritik öneme sahiptir."
     },
     {
      "line": "Evet, Almanya'da master eğitimi için giden uluslararası öğrencilerin genellikle dönem içinde haftalık 20 saat çalışma izni bulunur. Bu izin, yıllık 140 tam gün veya 280 yarım gün (2024 değişikliği — eski 120/240) çalışma hakkınızın bir parçasıdır. Bu çalışma iznini Liferando (Lieferando) gibi teslimat hizmetlerinde rahatlıkla kullanabilirsiniz. Liferando, esnek çalışma saatleri ve genellikle daha az Almanca bilgisi gerektirmesi nedeniyle öğrenciler arasında oldukça popüler bir seçenektir.",
      "subs": [
       {
        "old": "genellikle dönem içinde haftalık 20 saat çalışma izni bulunur. Bu izin, yıllık 140 tam gün veya 280 yarım gün (2024 değişikliği — eski 120/240) çalışma hakkınızın bir parçasıdır.",
        "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır) çalışma izni bulunur. Ders döneminde 20 saate kadar çalışılan bir hafta alternatif olarak 2,5 iş günü sayılabilir."
       }
      ]
     },
     {
      "line": "Evet, öğrenciler haftalık 20 saat çalışma hakkının yanı sıra Minijob yapabilirler. Ancak burada önemli olan nokta, Minijob dahil tüm işlerinizden elde ettiğiniz toplam çalışma süresinin dönem içinde haftalık 20 saati geçmemesidir. Örneğin, bir işte haftada 15 saat çalışıyorsanız, ek olarak bir Minijob'da haftada en fazla 5 saat daha çalışabilirsiniz. Ayrıca, Minijob'dan elde ettiğiniz gelirin aylık Minijob sınırını (örn. 538 Euro) aşmamasına da dikkat etmelisiniz, aksi takdirde Minijob statünüzü kaybedebilir ve sosyal güvenlik primleri ödemek zorunda kalabilirsiniz.",
      "replace": "Evet, öğrenciler Werkstudent işinin yanı sıra Minijob yapabilirler. Burada önemli olan nokta şudur: haftalık 20 saat sınırı, Werkstudent statüsünün sosyal sigorta koşuludur; oturum hukukunda ayrı bir haftalık sınır değildir. Oturum hukuku açısından Minijob dahil tüm işlerinizdeki saat ve günler, yılda 140 iş günlük hesabınıza sayılır (4 saate kadar çalışılan gün yarım gün sayılır). Bu yüzden çalışma sürenizi, hesabınız yetecek ve öğreniminiz asıl amaç olarak kalacak şekilde planlayın. Ayrıca, Minijob'dan elde ettiğiniz gelirin aylık Minijob sınırını (2026'da 603 Euro) aşmamasına da dikkat etmelisiniz, aksi takdirde Minijob statünüzü kaybedebilir ve sosyal güvenlik primleri ödemek zorunda kalabilirsiniz."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "student-work-permit-in-germany-2026-20-hour-rule-and-types",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Yıllık Çalışma Sınırı: Haftalık 20 saat kuralı, dönem içindeki çalışmayı kapsar. Yıl boyunca ise öğrencilerin toplamda 120 tam gün veya 140 tam gün veya 280 yarım gün (2024 değişikliği — eski 120/240) (2024'te 140/280'e çıkarıldı) çalışma hakkı bulunur. Bir tam gün 8 saat, bir yarım gün ise 4 saat olarak kabul edilir. Bu, öğrencilerin dönem tatillerinde (semesterferien) haftada 20 saati aşan sürelerde tam zamanlı (Vollzeit) çalışabileceği anlamına gelir. Örneğin, yaz tatilinde 2 ay boyunca tam zamanlı çalışarak bu yıllık izninizi kullanabilirsiniz.",
      "subs": [
       {
        "old": "120 tam gün veya 140 tam gün veya 280 yarım gün (2024 değişikliği — eski 120/240) (2024'te 140/280'e çıkarıldı)",
        "new": "140 iş günü (Arbeitstagekonto; 2024'te 120'den 140'a çıkarıldı)"
       },
       {
        "old": "Bir tam gün 8 saat, bir yarım gün ise 4 saat olarak kabul edilir.",
        "new": "4 saate kadar çalışılan gün yarım gün sayılır."
       }
      ]
     },
     {
      "line": "Minijob, aylık belirli bir gelir sınırının altında kalan işlerdir. Güncel durumda (bilgiler değişebilir, her zaman güncel limitleri kontrol edin), bu sınır genellikle 538 Euro civarındadır.",
      "subs": [
       {
        "old": "genellikle 538 Euro civarındadır",
        "new": "2026'da 603 Euro'dur"
       }
      ]
     },
     {
      "line": "20 Saat Kuralı ile İlişkisi: Minijob, haftalık 20 saat kuralına tabidir. Yani, Minijob yapsanız bile, dönem içinde toplam çalışma süreniz haftalık 20 saati geçmemelidir. Ancak, birden fazla Minijob yapıyorsanız, tüm Minijob gelirlerinizin toplamı aylık üst sınırı (örn. 538 Euro) geçmemeli ve toplam çalışma süreniz haftalık 20 saati aşmamalıdır.",
      "subs": [
       {
        "old": "örn. 538 Euro",
        "new": "2026'da 603 Euro"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "studienkolleg-center-list-2026-public-private-institutions",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Vize ve Finansal Kanıt (Sperrkonto): Almanya'da öğrenci vizesi almak için finansal yeterliliğinizi kanıtlamanız gerekir. Bunun en yaygın yolu, bir Sperrkonto (bloke hesap) açmaktır. Bu hesapta, Almanya'daki yaşam masraflarınızı karşılayabileceğinizi gösteren belirli bir miktar para (2024 yılı için aylık yaklaşık 934 Euro, yıllık yaklaşık 11.208 Euro) bloke edilir. Bu miktar her yıl güncellenir, bu yüzden güncel bilgiyi Alman Konsolosluğu'ndan veya DAAD'nin resmi sitesinden kontrol edin.",
      "subs": [
       {
        "old": "2024 yılı için aylık yaklaşık 934 Euro, yıllık yaklaşık 11.208 Euro",
        "new": "01.09.2024'ten beri aylık 992 Euro, yıllık 11.904 Euro"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "testas-guide-2026-is-it-necessary-for-turkish-students-how-to",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Soru 6: Denklik için hesapta para ne kadar olmalı? Cevap: Bu sorunuz TestAS ile ilgili değildir. Almanya'ya öğrenci vizesi başvurusu yaparken genellikle bir bloke hesapta (Sperrkonto) belirli bir miktarın (güncel olarak yıllık 11.208 Euro veya daha fazla olabilir) bulunması istenir. Bu miktar yaşam masraflarını karşılamak içindir ve Almanya Eğitim Maliyetleri sayfamızda güncel bilgileri bulabilirsin. Bu miktar denklik değil, vize şartıdır.",
      "subs": [
       {
        "old": "güncel olarak yıllık 11.208 Euro veya daha fazla olabilir",
        "new": "01.09.2024'ten beri yıllık 11.904 Euro"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "tips-for-international-students-searching-for-jobs-in-germany-2026-faqs",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Almanya'da öğrenci vizesi ile dönem içinde teilzeit + minijob yapılabilir mi? Haftalık 20 saat sınırı aşma durumunda problem olur mu yılda 240 yarım gün çalışma iznini geçmediği sürece sıkıntı yaşar mıyım?",
      "subs": [
       {
        "old": "yılda 240 yarım gün çalışma iznini",
        "new": "yılda 140 iş günlük çalışma iznini"
       }
      ]
     },
     {
      "line": "Evet, Almanya'da öğrenci vizesiyle dönem içinde hem Teilzeit (part-time) hem de Minijob yapabilirsiniz, ancak tüm bu işlerden elde ettiğiniz toplam çalışma süresi haftalık 20 saati geçmemelidir. Bu kural, dönem içinde asıl önceliğinizin eğitiminiz olduğunu varsayar. Yıllık 140 tam gün veya 280 yarım gün (2024 değişikliği — eski 120/240) çalışma iznini aşmadığınız sürece, dönem tatillerinde (Semesterferien) haftalık 20 saatin üzerinde çalışabilirsiniz. Ancak dönem içinde 20 saat sınırını aşmak, oturum izninizin iptaline kadar gidebilecek ciddi sorunlara yol açabilir. Bu nedenle, dönem içinde haftalık 20 saat sınırına harfiyen uymanız kritik öneme sahiptir.",
      "replace": "Evet, Almanya'da öğrenci vizesiyle dönem içinde hem Teilzeit (part-time) hem de Minijob yapabilirsiniz. 01.03.2024'ten bu yana oturum hukukundaki sınır haftalık bir tavan değil, yılda 140 iş günlük hesabınızdır: 4 saate kadar çalışılan gün yarım gün sayılır; ders döneminde haftada 20 saate kadar çalışılan bir hafta 2,5 iş günü olarak da sayılabilir ve daha avantajlı olan hesaplama uygulanır. Tüm işlerinizdeki saat ve günler bu hesaba sayılır. Ders döneminde haftalık 20 saat, Werkstudent statüsünün sosyal sigorta koşuludur; ayrıca üniversiteler ve makamlar öğreniminizin asıl önceliğiniz olarak kalmasını bekler. Dönem tatillerinde (Semesterferien), 140 günlük hesabınız dolmadığı sürece daha fazla çalışabilirsiniz. Hesabı aşmak, oturum izninizle ilgili ciddi sorunlara yol açabilir. Bu nedenle çalıştığınız günleri dikkatle takip etmeniz kritik öneme sahiptir."
     },
     {
      "line": "Evet, Almanya'da master eğitimi için giden uluslararası öğrencilerin genellikle dönem içinde haftalık 20 saat çalışma izni bulunur. Bu izin, yıllık 140 tam gün veya 280 yarım gün (2024 değişikliği — eski 120/240) çalışma hakkınızın bir parçasıdır. Bu çalışma iznini Liferando (Lieferando) gibi teslimat hizmetlerinde rahatlıkla kullanabilirsiniz. Liferando, esnek çalışma saatleri ve genellikle daha az Almanca bilgisi gerektirmesi nedeniyle öğrenciler arasında oldukça popüler bir seçenektir.",
      "subs": [
       {
        "old": "genellikle dönem içinde haftalık 20 saat çalışma izni bulunur. Bu izin, yıllık 140 tam gün veya 280 yarım gün (2024 değişikliği — eski 120/240) çalışma hakkınızın bir parçasıdır.",
        "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır) çalışma izni bulunur. Ders döneminde 20 saate kadar çalışılan bir hafta alternatif olarak 2,5 iş günü sayılabilir."
       }
      ]
     },
     {
      "line": "Evet, öğrenciler haftalık 20 saat çalışma hakkının yanı sıra Minijob yapabilirler. Ancak burada önemli olan nokta, Minijob dahil tüm işlerinizden elde ettiğiniz toplam çalışma süresinin dönem içinde haftalık 20 saati geçmemesidir. Örneğin, bir işte haftada 15 saat çalışıyorsanız, ek olarak bir Minijob'da haftada en fazla 5 saat daha çalışabilirsiniz. Ayrıca, Minijob'dan elde ettiğiniz gelirin aylık Minijob sınırını (örn. 538 Euro) aşmamasına da dikkat etmelisiniz, aksi takdirde Minijob statünüzü kaybedebilir ve sosyal güvenlik primleri ödemek zorunda kalabilirsiniz.",
      "replace": "Evet, öğrenciler Werkstudent işinin yanı sıra Minijob yapabilirler. Burada önemli olan nokta şudur: haftalık 20 saat sınırı, Werkstudent statüsünün sosyal sigorta koşuludur; oturum hukukunda ayrı bir haftalık sınır değildir. Oturum hukuku açısından Minijob dahil tüm işlerinizdeki saat ve günler, yılda 140 iş günlük hesabınıza sayılır (4 saate kadar çalışılan gün yarım gün sayılır). Bu yüzden çalışma sürenizi, hesabınız yetecek ve öğreniminiz asıl amaç olarak kalacak şekilde planlayın. Ayrıca, Minijob'dan elde ettiğiniz gelirin aylık Minijob sınırını (2026'da 603 Euro) aşmamasına da dikkat etmelisiniz, aksi takdirde Minijob statünüzü kaybedebilir ve sosyal güvenlik primleri ödemek zorunda kalabilirsiniz."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "what-is-anabin-h-h-h-how-is-a-turkish-diploma",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Soru 5: Denklik için hesapta para ne kadar olmalı? Cevap: Diploma denkliği ile hesapta olması gereken para miktarı arasında doğrudan bir ilişki yoktur. Hesapta olması gereken para, Almanya'da öğrenci vizesi başvurusu için ispatlamanız gereken finansal yeterliliği ifade eder. Bu miktar, genellikle \"Sperrkonto (bloke hesap)\" adı verilen bir hesapta bulunması gereken yıllık yaşam gideri miktarıdır. Bu miktar her yıl güncellenir ve 2024 yılı itibarıyla aylık yaklaşık 934 Euro, yıllık ise 11.208 Euro civarındadır. Ancak, güncel ve kesin miktar için Alman Dışişleri Bakanlığı veya Almanya'daki konsoloslukların resmi sayfalarından teyit almanız şarttır. (Bkz: /tools/cost-of-living sayfamız)",
      "subs": [
       {
        "old": "2024 yılı itibarıyla aylık yaklaşık 934 Euro, yıllık ise 11.208 Euro civarındadır",
        "new": "01.09.2024'ten beri aylık 992 Euro, yıllık ise 11.904 Euro'dur"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "zab-diploma-equivalency-official-recognition-guide-for-masterphd-in-germany",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Soru 5: Denklik için hesapta para ne kadar olmalı? Cevap: Bu sorudaki \"hesapta para\" kavramı genellikle denklik süreciyle değil, Almanya öğrenci vizesi başvurusu için gerekli olan \"Sperrkonto (bloke hesap)\" ile ilgilidir. ZAB diploma denkliği için herhangi bir bloke hesap gerekmez, sadece başvuru ücretini ödemeniz yeterlidir. Öğrenci vizesi için ise Almanya'da yaşam masraflarınızı karşılayabileceğinizi göstermek amacıyla bloke hesapta belirli bir miktar paranın (2024 itibarıyla yıllık yaklaşık 11.208 Euro) bulunması zorunludur. Bu, denklik sürecinden tamamen farklı bir konudur. Vize ve bloke hesap hakkında detaylı bilgi için ApplyToGerman (AlmanyaUni)'nin vize rehberine bakabilirsiniz.",
      "subs": [
       {
        "old": "2024 itibarıyla yıllık yaklaşık 11.208 Euro",
        "new": "01.09.2024'ten beri yıllık 11.904 Euro"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "germanys-student-work-limits-increased-you-can-earn-more-now",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Almanya, uluslararası öğrencilerin yüzünü güldürecek önemli bir karara imza attı! 1 Mart 2024'ten itibaren geçerli olan ve 2026 akademik yılı itibarıyla tam olarak yürürlüğe giren yeni düzenlemeyle, yıllık çalışma limitin 120 tam günden 140 tam güne (veya 240 yarım günden 280 yarım güne) yükseltildi. Bu, Fachkräfteeinwanderungsgesetz (Nitelikli Göç Yasası) reformlarının bir parçası ve sana hem daha fazla esneklik hem de daha iyi kazanç fırsatları sunuyor. Haftalık 20 saatlik dönem içi çalışma kuralı değişmezken, yıllık ortalama kazancın da 10.500 Euro civarından 12.000 Euro'nun üzerine çıkma potansiyeli taşıyor. 2026 itibarıyla asgari ücretin saatlik 13.90 Euro olduğunu da unutma.",
      "subs": [
       {
        "old": "geçerli olan ve 2026 akademik yılı itibarıyla tam olarak yürürlüğe giren yeni",
        "new": "geçerli olan yeni"
       },
       {
        "old": "Haftalık 20 saatlik dönem içi çalışma kuralı değişmezken, yıllık",
        "new": "4 saate kadar çalışılan gün yarım gün sayılır ve ders döneminde 20 saate kadar çalışılan bir hafta alternatif olarak 2,5 iş günü sayılabilir. Yıllık"
       }
      ]
     },
     {
      "line": "Öncelikle, yeni limitleri ve kazanç potansiyelini göz önünde bulundurarak bütçeni ve iş arama stratejini yeniden gözden geçirmelisin. Özellikle Werkstudent pozisyonlarına odaklanarak hem finansal hem de kariyer anlamında en iyi verimi alabilirsin. Minijob (aylık 538 Euro'ya kadar vergisiz iş) veya üniversite içindeki Studentische Hilfskraft (HiWi) (üniversite asistanı pozisyonu) gibi seçenekleri de değerlendirebilirsin. Hangi işin sana en uygun olduğunu ve vergi durumunu detaylıca araştırmayı unutma. Resmi kaynaklardan güncel bilgileri teyit etmen her zaman en doğrusu.",
      "subs": [
       {
        "old": "aylık 538 Euro'ya kadar",
        "new": "2026'da aylık 603 Euro'ya kadar"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "after-an-english-masters-german-internships-jobs-and-career-opportunities-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Arbeitserlaubnis (20-Stunden-Regel): „Ich gehe für ein Masterstudium nach Deutschland, habe ich jetzt eine 20-Stunden-Arbeitserlaubnis? Und kann ich diese bei Liferando nutzen?“ Ja, als Masterstudent in Deutschland hast du eine Arbeitserlaubnis von 120 vollen Tagen oder 240 halben Tagen pro Jahr (entspricht 20 Stunden pro Woche). Du kannst diese Erlaubnis bei Liferando (Essenslieferung), als Werkstudent an der Universität oder in einem anderen Job nutzen. Diese Arbeitserlaubnis sollte jedoch deine akademische Leistung nicht beeinträchtigen, und deine Ausbildung sollte Priorität haben.",
      "subs": [
       {
        "old": "Arbeitserlaubnis (20-Stunden-Regel):",
        "new": "Arbeitserlaubnis (140-Tage-Regel):"
       },
       {
        "old": "hast du eine Arbeitserlaubnis von 120 vollen Tagen oder 240 halben Tagen pro Jahr (entspricht 20 Stunden pro Woche).",
        "new": "darfst du bis zu 140 Arbeitstage im Jahr arbeiten (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag). Die 20 Stunden pro Woche sind keine aufenthaltsrechtliche Grenze, sondern die sozialversicherungsrechtliche Grenze für Werkstudierende in der Vorlesungszeit."
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "bachelor-oder-master-deutschland-schwierigkeit-sprache-job-3-versuche-regel",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Ein Minijob bringt bis zu ~556 €/Monat (2025). Wenn du 9–10 Stunden pro Woche zum Mindestlohn arbeitest und deine Ausgaben 710 € betragen, wirst du jeden Monat ein Defizit haben. Ein Kellnerjob mit Trinkgeld (steuerfrei) kann das Leben erleichtern – ist aber körperlich anstrengend und ermüdend.",
      "subs": [
       {
        "old": "bis zu ~556 €/Monat (2025)",
        "new": "bis zu 603 €/Monat (2026)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "bachelors-or-masters-germany-difficulty-language-jobs-3-attempt-rule",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "A Minijob pays up to ~€556 per month (2025). Working 9-10 hours a week at minimum wage will leave you in the red every month if your expenses are €710. A waiter job with tips (tax-free) can make life easier — but it's physically demanding and exhausting.",
      "subs": [
       {
        "old": "up to ~€556 per month (2025)",
        "new": "up to €603 per month (2026)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "bachelor-vs-master-in-germany-difficulty-language-work-for-internationals",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Minijob ~556 €/ay (2025). Haftada 9-10 saat asgari ücretle çalışmak, giderin 710 €'ysa her ay açık verdirir. Bahşişli (vergisiz) bir garsonluk işi hayatı kolaylaştırır — ama fiziksel ve yorucudur.",
      "subs": [
       {
        "old": "~556 €/ay (2025)",
        "new": "en fazla 603 €/ay (2026)"
       }
      ]
     }
    ]
   }
  }
 ]
}
JSON, true, 512, JSON_THROW_ON_ERROR);

        $problems = [];
        $writes = [];
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
                                preg_match('/^\s{0,3}(?:#{1,6}\s+|(?:>\s?)+|[-*+]\s+|\d+[.)]\s+)?/u', $lines[$start], $m);
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
            }
        }

        if ($problems) {
            if (app()->runningUnitTests()) {
                return; // Test DB'si sıfırdan kurulur; bu kayıtların çoğu orada yok. Hiçbir şey yazma.
            }
            throw new RuntimeException('Content Truth Batch 2 (A — blog/news yazıları): ön kontrol başarısız, hiçbir şey yazılmadı. '.implode(' | ', array_slice($problems, 0, 40)));
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
        $s = preg_replace('/^\s{0,3}(?:#{1,6}\s+|(?:>\s?)+|[-*+]\s+|\d+[.)]\s+)?/u', '', $s);
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

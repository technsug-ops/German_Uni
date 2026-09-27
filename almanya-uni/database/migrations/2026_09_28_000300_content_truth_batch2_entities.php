<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Content Truth Sprint — Batch 2 (C — şehir/eyalet/üniversite content_blocks): site genelinde eski çalışma kuralı ve tutar düzeltmeleri.
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
   "table": "cities",
   "slug": "freiburg-im-breisgau-q2833",
   "json": {
    "content_blocks_de": [
     {
      "old": "liegt ab 2024 bei etwa 934 Euro pro Monat, also rund 11.208 Euro für ein Jahr",
      "new": "liegt seit dem 01.09.2024 bei 992 Euro pro Monat, also 11.904 Euro für ein Jahr"
     },
     {
      "old": "hast du eine Arbeitserlaubnis von 120 vollen Tagen oder 240 halben Tagen pro Jahr.",
      "new": "darfst du bis zu 140 Arbeitstage im Jahr arbeiten (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag)."
     }
    ],
    "content_blocks_en": [
     {
      "old": "you have a work permit for 120 full days or 240 half days per year",
      "new": "you may work up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day)"
     },
     {
      "old": "as of 2024, it's approximately 934 Euros per month, totaling around 11,208 Euros for a year",
      "new": "since 1 September 2024, it has been 992 Euros per month, totaling 11,904 Euros for a year"
     }
    ],
    "content_blocks": [
     {
      "old": "(örn. 2024 itibarıyla aylık 934 EUR)",
      "new": "(01.09.2024'ten beri aylık 992 EUR, yıllık 11.904 EUR)"
     }
    ]
   }
  },
  {
   "table": "cities",
   "slug": "harburg-q1635",
   "json": {
    "content_blocks_de": [
     {
      "old": "(z.B. 120 volle oder 240 halbe Tage pro Jahr)",
      "new": "(bis zu 140 Arbeitstage im Jahr, geführt als Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(e.g., 120 full days or 240 half days per year)",
      "new": "(up to 140 working days a year, counted in an Arbeitstagekonto; a day of up to 4 hours counts as half a day)"
     }
    ],
    "content_blocks": [
     {
      "old": "(örn. yılda 120 tam gün veya 240 yarım gün)",
      "new": "(yılda 140 iş günü; Arbeitstagekonto, 4 saate kadar çalışılan gün yarım gün sayılır)"
     }
    ]
   }
  },
  {
   "table": "cities",
   "slug": "heilbronn-q715",
   "json": {
    "content_blocks_de": [
     {
      "old": "11.208 EUR pro Jahr (934 EUR pro Monat)",
      "new": "11.904 EUR pro Jahr (992 EUR pro Monat)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "11,208 EUR (934 EUR per month)",
      "new": "11,904 EUR (992 EUR per month)"
     }
    ],
    "content_blocks": [
     {
      "old": "yıllık 11.208 EUR (aylık 934 EUR)",
      "new": "yıllık 11.904 EUR (aylık 992 EUR)"
     }
    ]
   }
  },
  {
   "table": "cities",
   "slug": "neu-ulm-q4120",
   "json": {
    "content_blocks_de": [
     {
      "old": "für eine bestimmte Anzahl von Stunden (in der Regel 120 volle Tage oder 240 halbe Tage pro Jahr)",
      "new": "für eine begrenzte Anzahl von Arbeitstagen (bis zu 140 Arbeitstage im Jahr, geführt als Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "for a certain number of hours (typically 120 full days or 240 half days per year)",
      "new": "for a limited number of working days (up to 140 working days a year, counted in an Arbeitstagekonto; a day of up to 4 hours counts as half a day)"
     }
    ],
    "content_blocks": [
     {
      "old": "belirli saatlerde (genellikle yılda 120 tam gün veya 240 yarım gün) çalışma izniniz bulunur",
      "new": "sınırlı sayıda gün (yılda 140 iş günü; Arbeitstagekonto, 4 saate kadar çalışılan gün yarım gün sayılır) çalışma izniniz bulunur"
     }
    ]
   }
  },
  {
   "table": "states",
   "slug": "hessen",
   "json": {
    "content_blocks_de": [
     {
      "old": "wurde für 2024 auf etwa 934 Euro pro Monat festgelegt",
      "new": "liegt seit dem 01.09.2024 bei 992 Euro pro Monat"
     },
     {
      "old": "etwa 11.208 Euro (12 x 934 Euro)",
      "new": "11.904 Euro (12 x 992 Euro)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "as of 2024, is set at approximately 934 Euros per month",
      "new": "since 1 September 2024, has been set at 992 Euros per month"
     },
     {
      "old": "approximately 11,208 Euros (12 x 934 Euros)",
      "new": "11,904 Euros (12 x 992 Euros)"
     }
    ],
    "content_blocks": [
     {
      "old": "2024 itibarıyla aylık yaklaşık 934 Euro olarak belirlenmiştir",
      "new": "01.09.2024'ten beri aylık 992 Euro olarak belirlenmiştir"
     },
     {
      "old": "yaklaşık 11.208 Euro (12 x 934 Euro)",
      "new": "11.904 Euro (12 x 992 Euro)"
     }
    ]
   }
  },
  {
   "table": "states",
   "slug": "niedersachsen",
   "json": {
    "content_blocks_de": [
     {
      "old": "(bis 538 Euro monatlich)",
      "new": "(bis 603 Euro monatlich, Stand 2026)"
     },
     {
      "old": "Ein Einkommen von 603 Euro überschreitet die Minijob-Grenze",
      "new": "Ein Einkommen von mehr als 603 Euro überschreitet die Minijob-Grenze (2026)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(up to 538 Euros per month)",
      "new": "(up to 603 Euros per month in 2026)"
     },
     {
      "old": "Earning 603 Euros exceeds the Minijob limit",
      "new": "Earning more than 603 Euros exceeds the 2026 Minijob limit"
     }
    ],
    "content_blocks": [
     {
      "old": "Minijob (aylık 538 Euro'ya kadar)",
      "new": "Minijob (2026'da aylık 603 Euro'ya kadar)"
     },
     {
      "old": "603 Euro'luk bir gelir, Minijob sınırını aştığı için",
      "new": "603 Euro'yu aşan bir gelir, Minijob sınırını (2026) aştığı için"
     }
    ]
   }
  },
  {
   "table": "states",
   "slug": "rheinland-pfalz",
   "json": {
    "content_blocks_de": [
     {
      "old": "In der Regel ist eine Arbeitserlaubnis für 120 volle Tage oder 240 halbe Tage pro Jahr vorgesehen.",
      "new": "Erlaubt sind in der Regel bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag); Tätigkeiten als studentische Hilfskraft an der Hochschule werden nicht angerechnet."
     }
    ],
    "content_blocks_en": [
     {
      "old": "Generally, you're allowed to work 120 full days or 240 half days per year.",
      "new": "Generally, you're allowed to work up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day)."
     }
    ],
    "content_blocks": [
     {
      "old": "Genellikle yılda 120 tam gün veya 240 yarım gün çalışma izni verilir.",
      "new": "Genellikle yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır) çalışma izni verilir."
     }
    ]
   }
  },
  {
   "table": "cities",
   "slug": "kassel-q2865",
   "json": {
    "content_blocks": [
     {
      "old": "aylık 538 Euro'ya kadar",
      "new": "2026'da aylık 603 Euro'ya kadar"
     }
    ]
   }
  },
  {
   "table": "cities",
   "slug": "paderborn",
   "json": {
    "content_blocks": [
     {
      "old": "genellikle 538 Euro civarı",
      "new": "2026'da 603 Euro"
     }
    ]
   }
  },
  {
   "table": "cities",
   "slug": "rostock-q2861",
   "json": {
    "content_blocks": [
     {
      "old": "belirli saatlerde çalışma iznin var (genellikle yılda 120 tam gün veya 240 yarım gün)",
      "new": "sınırlı çalışma iznin var (yılda 140 iş günü; Arbeitstagekonto, 4 saate kadar çalışılan gün yarım gün sayılır)"
     }
    ]
   }
  },
  {
   "table": "cities",
   "slug": "wiesbaden",
   "json": {
    "content_blocks": [
     {
      "old": "saat limitlerini (genelde yılda 120 tam gün veya 240 yarım gün)",
      "new": "sınırlarını (yılda 140 iş günü; Arbeitstagekonto, 4 saate kadar çalışılan gün yarım gün sayılır)"
     },
     {
      "old": "genellikle yıllık 11.208 Euro civarı",
      "new": "01.09.2024'ten beri yıllık 11.904 Euro"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "akademie-fur-darstellende-kunst-baden-wurttemberg-q414258",
   "json": {
    "content_blocks_de": [
     {
      "old": "eine bestimmte Anzahl von Stunden arbeiten (120 volle oder 240 halbe Tage pro Jahr)",
      "new": "in begrenztem Umfang arbeiten (bis zu 140 Arbeitstage im Jahr; Arbeitstagekonto, ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "for a specific number of hours (120 full days or 240 half days per year)",
      "new": "within set limits (up to 140 working days a year; Arbeitstagekonto, a day of up to 4 hours counts as half a day)"
     }
    ],
    "content_blocks": [
     {
      "old": "belirli saatlerde çalışma iznine sahiptir (yılda 120 tam gün veya 240 yarım gün)",
      "new": "sınırlı ölçüde çalışma iznine sahiptir (yılda 140 iş günü; Arbeitstagekonto, 4 saate kadar çalışılan gün yarım gün sayılır)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "albert-ludwigs-universitat-freiburg-im-breisgau-partner-019ddbba",
   "json": {
    "content_blocks_de": [
     {
      "old": "für 120 volle oder 240 halbe Tage pro Jahr (entspricht 20 Stunden pro Woche)",
      "new": "für bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag; in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden)"
     },
     {
      "old": "(derzeit etwa 934 Euro pro Monat, also 11.208 Euro)",
      "new": "(derzeit 992 Euro pro Monat, also 11.904 Euro)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "for 120 full days or 240 half days per year (equivalent to 20 hours per week)",
      "new": "for up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day; during the lecture period, a week of up to 20 hours can instead be counted as 2.5 working days)"
     },
     {
      "old": "(currently around 934 Euros per month, totaling 11,208 Euros)",
      "new": "(currently 992 Euros per month, totaling 11,904 Euros)"
     }
    ],
    "content_blocks": [
     {
      "old": "yılda 120 tam gün veya 240 yarım gün (haftada 20 saate denk gelir) çalışma iznine",
      "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır; ders döneminde 20 saate kadar çalışılan bir hafta alternatif olarak 2,5 iş günü sayılabilir) çalışma iznine"
     },
     {
      "old": "(şu an için aylık yaklaşık 934 Euro, yani 11.208 Euro)",
      "new": "(şu an için aylık 992 Euro, yani yıllık 11.904 Euro)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "berlin-school-of-business-innovation-partner-019ddbba",
   "json": {
    "content_blocks_de": [
     {
      "old": "(normalerweise 120 volle oder 240 halbe Tage pro Jahr)",
      "new": "(bis zu 140 Arbeitstage im Jahr; ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(generally 120 full days or 240 half days per year)",
      "new": "(up to 140 working days a year; a day of up to 4 hours counts as half a day)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "deutsche-hochschule-fur-pravention-und-gesundheitsmanagement-gmbh-partner-019ddbbe",
   "json": {
    "content_blocks_de": [
     {
      "old": "eine bestimmte Anzahl von Stunden arbeiten (in der Regel 120 volle Tage oder 240 halbe Tage pro Jahr)",
      "new": "in begrenztem Umfang arbeiten (bis zu 140 Arbeitstage im Jahr; Arbeitstagekonto, ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     },
     {
      "old": "(derzeit ca. 11.208 Euro pro Jahr)",
      "new": "(derzeit 11.904 Euro pro Jahr)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "a certain number of hours (typically 120 full days or 240 half days per year)",
      "new": "within set limits (up to 140 working days a year; Arbeitstagekonto, a day of up to 4 hours counts as half a day)"
     },
     {
      "old": "(currently around 11,208 Euros annually)",
      "new": "(currently 11,904 Euros annually)"
     }
    ],
    "content_blocks": [
     {
      "old": "belirli saatlerde (genellikle yılda 120 tam gün veya 240 yarım gün) çalışma iznine",
      "new": "sınırlı ölçüde (yılda 140 iş günü; Arbeitstagekonto, 4 saate kadar çalışılan gün yarım gün sayılır) çalışma iznine"
     },
     {
      "old": "(güncel olarak yıllık yaklaşık 11.208 Euro)",
      "new": "(güncel olarak yıllık 11.904 Euro)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "ems-european-management-school-partner-019ddbbb",
   "json": {
    "content_blocks_de": [
     {
      "old": "innerhalb bestimmter Stundenbeschränkungen (in der Regel 120 volle oder 240 halbe Tage pro Jahr)",
      "new": "innerhalb bestimmter Grenzen (bis zu 140 Arbeitstage im Jahr; Arbeitstagekonto, ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     },
     {
      "old": "(üblicherweise 120 volle oder 240 halbe Tage pro Jahr)",
      "new": "(bis zu 140 Arbeitstage im Jahr; Arbeitstagekonto, ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "within certain hour restrictions (",
      "new": "within certain limits ("
     },
     {
      "old": "(typically 120 full days or 240 half days per year)",
      "new": "(up to 140 working days a year; Arbeitstagekonto, a day of up to 4 hours counts as half a day)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "europaische-hochschule-fur-innovation-und-perspektive-q118258797",
   "json": {
    "content_blocks_de": [
     {
      "old": "(z.B. 120 volle oder 240 halbe Tage pro Jahr)",
      "new": "(bis zu 140 Arbeitstage im Jahr; Arbeitstagekonto, ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(e.g., 120 full days or 240 half days per year)",
      "new": "(up to 140 working days a year; Arbeitstagekonto, a day of up to 4 hours counts as half a day)"
     }
    ],
    "content_blocks": [
     {
      "old": "(örn. yılda 120 tam gün veya 240 yarım gün)",
      "new": "(yılda 140 iş günü; Arbeitstagekonto, 4 saate kadar çalışılan gün yarım gün sayılır)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "europaische-medien-und-business-akademie-partner-019ddbba",
   "json": {
    "content_blocks_de": [
     {
      "old": "eine bestimmte Anzahl von Stunden arbeiten (120 volle oder 240 halbe Tage pro Jahr)",
      "new": "in begrenztem Umfang arbeiten (bis zu 140 Arbeitstage im Jahr; Arbeitstagekonto, ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "for a certain number of hours (120 full days or 240 half days per year)",
      "new": "within set limits (up to 140 working days a year; Arbeitstagekonto, a day of up to 4 hours counts as half a day)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "evangelische-hochschule-freiburg-partner-019ddbba",
   "json": {
    "content_blocks_de": [
     {
      "old": "eine Arbeitserlaubnis von insgesamt 120 vollen Tagen oder 240 halben Tagen (nicht mehr als 4 Stunden pro Tag) pro Jahr. Das entspricht durchschnittlich 20 Stunden pro Woche.",
      "new": "eine Arbeitserlaubnis für bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag). In der Vorlesungszeit kann alternativ eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden."
     }
    ],
    "content_blocks_en": [
     {
      "old": "a total of 120 full days or 240 half days (not exceeding 4 hours per day) per year. This averages out to about 20 hours per week.",
      "new": "up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day). During the lecture period, a week of up to 20 hours can instead be counted as 2.5 working days."
     }
    ],
    "content_blocks": [
     {
      "old": "yılda toplam 120 tam gün veya 240 yarım gün (günde 4 saatten fazla olmayan) çalışma izniniz bulunmaktadır. Bu, haftalık ortalama 20 saate denk gelir.",
      "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır) çalışma izniniz bulunmaktadır. Ders döneminde 20 saate kadar çalışılan bir hafta alternatif olarak 2,5 iş günü sayılabilir."
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "fau-erlangen-nurnberg-partner-019de9f1",
   "json": {
    "content_blocks_de": [
     {
      "old": "eine Arbeitserlaubnis von 120 vollen oder 240 halben Tagen pro Jahr gestattet",
      "new": "eine Arbeitserlaubnis für bis zu 140 Arbeitstage im Jahr gestattet (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "for a specific number of hours. Generally, you're allowed to work 120 full days or 240 half days per year.",
      "new": "within set limits. You're allowed to work up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day)."
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "fhdw-fachhochschule-fur-die-wirtschaft-hannover-partner-019ddbba",
   "json": {
    "content_blocks_de": [
     {
      "old": "jährlich 120 volle Tage oder 240 halbe Tage (4 Stunden pro Tag) zu arbeiten",
      "new": "bis zu 140 Arbeitstage im Jahr zu arbeiten (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     },
     {
      "old": "bis 538 Euro monatlich",
      "new": "bis 603 Euro monatlich (2026)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "120 full days or 240 half days (4 hours per day) per year",
      "new": "up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day)"
     },
     {
      "old": "up to 538 Euros per month",
      "new": "up to 603 Euros per month (2026)"
     }
    ],
    "content_blocks": [
     {
      "old": "yılda 120 tam gün veya 240 yarım gün (günde 4 saat) çalışma hakkına",
      "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır) çalışma hakkına"
     },
     {
      "old": "aylık 538 Euro'ya kadar",
      "new": "aylık 603 Euro'ya kadar (2026)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "health-and-medical-university-potsdam-partner-019ddbba",
   "json": {
    "content_blocks_de": [
     {
      "old": "insgesamt 120 volle Tage oder 240 halbe Tage pro Jahr arbeiten (entspricht durchschnittlich 20 Stunden pro Woche)",
      "new": "bis zu 140 Arbeitstage im Jahr arbeiten (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag; in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "a total of 120 full days or 240 half days per year (which averages out to about 20 hours per week)",
      "new": "up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day; during the lecture period, a week of up to 20 hours can instead be counted as 2.5 working days)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "hmu-health-and-medical-university-hs517",
   "json": {
    "content_blocks_de": [
     {
      "old": "von 120 vollen oder 240 halben Tagen pro Jahr (durchschnittlich 20 Stunden pro Woche)",
      "new": "für bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag; in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "120 full days or 240 half days per year (an average of 20 hours per week)",
      "new": "up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day; during the lecture period, a week of up to 20 hours can instead be counted as 2.5 working days)"
     }
    ],
    "content_blocks": [
     {
      "old": "yılda 120 tam gün veya 240 yarım gün (haftada ortalama 20 saat) çalışma izni",
      "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır; ders döneminde 20 saate kadar çalışılan bir hafta alternatif olarak 2,5 iş günü sayılabilir) çalışma izni"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "hochschule-des-bundes-fur-offentliche-verwaltung-q1391252",
   "json": {
    "content_blocks_de": [
     {
      "old": "(derzeit etwa 11.208 Euro pro Jahr)",
      "new": "(derzeit 11.904 Euro pro Jahr)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(currently around 11,208 Euros annually)",
      "new": "(currently 11,904 Euros annually)"
     }
    ],
    "content_blocks": [
     {
      "old": "(şu an için yıllık yaklaşık 11.208 Euro)",
      "new": "(şu an için yıllık 11.904 Euro)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "hochschule-fur-bildende-kunste-braunschweig-q879216",
   "json": {
    "content_blocks_de": [
     {
      "old": "eine bestimmte Anzahl von Stunden arbeiten (in der Regel 120 volle oder 240 halbe Tage pro Jahr)",
      "new": "in begrenztem Umfang arbeiten (bis zu 140 Arbeitstage im Jahr; Arbeitstagekonto, ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "a certain number of hours according to student visa regulations (typically 120 full days or 240 half days per year)",
      "new": "within the limits set by student visa regulations (up to 140 working days a year; Arbeitstagekonto, a day of up to 4 hours counts as half a day)"
     }
    ],
    "content_blocks": [
     {
      "old": "belirli saatlerde çalışma iznine sahiptir (genellikle yılda 120 tam gün veya 240 yarım gün)",
      "new": "sınırlı ölçüde çalışma iznine sahiptir (yılda 140 iş günü; Arbeitstagekonto, 4 saate kadar çalışılan gün yarım gün sayılır)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "hochschule-fur-den-offentlichen-dienst-in-bayern-partner-019ddbba",
   "json": {
    "content_blocks_de": [
     {
      "old": "liegt aber ab 2024 bei etwa 934 Euro pro Monat",
      "new": "liegt aber seit dem 01.09.2024 bei 992 Euro pro Monat"
     },
     {
      "old": "etwa 11.208 Euro",
      "new": "11.904 Euro"
     }
    ],
    "content_blocks_en": [
     {
      "old": "as of 2024, it's approximately 934 Euros per month",
      "new": "since 1 September 2024 it has been 992 Euros per month"
     },
     {
      "old": "around 11,208 Euros",
      "new": "11,904 Euros"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "hochschule-fur-finanzwirtschaft-management-hs352",
   "json": {
    "content_blocks_de": [
     {
      "old": "(120 volle oder 240 halbe Tage pro Jahr)",
      "new": "(bis zu 140 Arbeitstage im Jahr; ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(120 full days or 240 half days per year)",
      "new": "(up to 140 working days a year; a day of up to 4 hours counts as half a day)"
     }
    ],
    "content_blocks": [
     {
      "old": "(yılda 120 tam gün veya 240 yarım gün)",
      "new": "(yılda 140 iş günü; 4 saate kadar çalışılan gün yarım gün sayılır)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "hochschule-fur-katholische-kirchenmusik-und-musikpadagogik-partner-019ddbba",
   "json": {
    "content_blocks_de": [
     {
      "old": "(aktuell ca. 11.208 Euro)",
      "new": "(aktuell 11.904 Euro)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(currently around 11,208 Euros)",
      "new": "(currently 11,904 Euros)"
     }
    ],
    "content_blocks": [
     {
      "old": "(güncel olarak yaklaşık 11.208 Euro)",
      "new": "(güncel olarak 11.904 Euro)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "hochschule-fur-kunste-im-sozialen-partner-019ddbbc",
   "json": {
    "content_blocks_de": [
     {
      "old": "aktuell ca. 11.208 Euro",
      "new": "aktuell 11.904 Euro"
     },
     {
      "old": "eine Arbeitserlaubnis von 120 vollen oder 240 halben Tagen pro Jahr. Das entspricht etwa 20 Stunden pro Woche.",
      "new": "eine Arbeitserlaubnis für bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag). In der Vorlesungszeit kann alternativ eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden."
     }
    ],
    "content_blocks_en": [
     {
      "old": "currently around 11,208 Euros",
      "new": "currently 11,904 Euros"
     },
     {
      "old": "120 full days or 240 half days per year. This generally equates to 20 hours per week.",
      "new": "up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day). During the lecture period, a week of up to 20 hours can instead be counted as 2.5 working days."
     }
    ],
    "content_blocks": [
     {
      "old": "(güncel olarak yaklaşık 11.208 Euro)",
      "new": "(güncel olarak 11.904 Euro)"
     },
     {
      "old": "yılda 120 tam gün veya 240 yarım gün çalışma iznine sahiptir. Bu, haftalık 20 saate denk gelir.",
      "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır) çalışma iznine sahiptir. Ders döneminde 20 saate kadar çalışılan bir hafta alternatif olarak 2,5 iş günü sayılabilir."
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "hochschule-fur-musik-freiburg-q650345",
   "json": {
    "content_blocks_de": [
     {
      "old": "(aktuell etwa 11.208 Euro)",
      "new": "(aktuell 11.904 Euro)"
     },
     {
      "old": "eine Arbeitserlaubnis von bis zu 20 Stunden pro Woche (120 volle oder 240 halbe Tage pro Jahr)",
      "new": "eine Arbeitserlaubnis für bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag; in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(currently around 11,208 Euros)",
      "new": "(currently 11,904 Euros)"
     },
     {
      "old": "up to 20 hours per week (120 full days or 240 half days per year)",
      "new": "up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day; during the lecture period, a week of up to 20 hours can instead be counted as 2.5 working days)"
     }
    ],
    "content_blocks": [
     {
      "old": "(güncel olarak yaklaşık 11.208 Euro)",
      "new": "(güncel olarak 11.904 Euro)"
     },
     {
      "old": "haftada 20 saate kadar (yılda 120 tam gün veya 240 yarım gün) çalışma izniniz",
      "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır; ders döneminde 20 saate kadar çalışılan bir hafta alternatif olarak 2,5 iş günü sayılabilir) çalışma izniniz"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "hochschule-fur-technik-und-wirtschaft-des-saarlandes-q884111",
   "json": {
    "content_blocks_de": [
     {
      "old": "(derzeit etwa 934 Euro pro Monat)",
      "new": "(derzeit 992 Euro pro Monat, also 11.904 Euro pro Jahr)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(currently around 934 Euros per month)",
      "new": "(currently 992 Euros per month, i.e. 11,904 Euros a year)"
     }
    ],
    "content_blocks": [
     {
      "old": "(şu an için aylık yaklaşık 934 Euro)",
      "new": "(şu an için aylık 992 Euro, yani yıllık 11.904 Euro)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "hochschule-konstanz-technik-wirtschaft-und-gestaltung-q1622118",
   "json": {
    "content_blocks_de": [
     {
      "old": "(aktuell 11.208 EUR pro Jahr)",
      "new": "(aktuell 11.904 EUR pro Jahr)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(currently 11,208 EUR annually)",
      "new": "(currently 11,904 EUR annually)"
     }
    ],
    "content_blocks": [
     {
      "old": "(güncel olarak yıllık 11.208 EUR)",
      "new": "(güncel olarak yıllık 11.904 EUR)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "international-school-of-management-ism-partner-019de9f1",
   "json": {
    "content_blocks_de": [
     {
      "old": "von 120 vollen oder 240 halben Tagen pro Jahr (entspricht 20 Stunden pro Woche)",
      "new": "für bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag; in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "120 full days or 240 half days per year (equivalent to 20 hours per week)",
      "new": "up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day; during the lecture period, a week of up to 20 hours can instead be counted as 2.5 working days)"
     }
    ],
    "content_blocks": [
     {
      "old": "yılda 120 tam gün veya 240 yarım gün (haftada 20 saate denk gelir) çalışma iznine",
      "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır; ders döneminde 20 saate kadar çalışılan bir hafta alternatif olarak 2,5 iş günü sayılabilir) çalışma iznine"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "julius-maximilians-universitat-wurzburg-q161976",
   "json": {
    "content_blocks_de": [
     {
      "old": "eine bestimmte Anzahl von Stunden Teilzeit arbeiten (in der Regel 120 volle Tage oder 240 halbe Tage pro Jahr)",
      "new": "in begrenztem Umfang Teilzeit arbeiten (bis zu 140 Arbeitstage im Jahr; Arbeitstagekonto, ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "for a certain number of hours (typically 120 full days or 240 half days per year)",
      "new": "within set limits (up to 140 working days a year; Arbeitstagekonto, a day of up to 4 hours counts as half a day)"
     }
    ],
    "content_blocks": [
     {
      "old": "belirli saatlerde part-time çalışma izni vardır (genellikle yılda 120 tam gün veya 240 yarım gün)",
      "new": "sınırlı ölçüde part-time çalışma izni vardır (yılda 140 iş günü; Arbeitstagekonto, 4 saate kadar çalışılan gün yarım gün sayılır)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "katholische-hochschule-freiburg-partner-019ddbba",
   "json": {
    "content_blocks_de": [
     {
      "old": "(derzeit etwa 11.208 EUR)",
      "new": "(derzeit 11.904 EUR)"
     },
     {
      "old": "von 120 vollen oder 240 halben Tagen pro Jahr (durchschnittlich 20 Stunden pro Woche)",
      "new": "für bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag; in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(currently around 11,208 EUR)",
      "new": "(currently 11,904 EUR)"
     },
     {
      "old": "for 120 full days or 240 half days per year (averaging 20 hours per week)",
      "new": "for up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day; during the lecture period, a week of up to 20 hours can instead be counted as 2.5 working days)"
     }
    ],
    "content_blocks": [
     {
      "old": "(şu an yaklaşık 11.208 EUR)",
      "new": "(şu an 11.904 EUR)"
     },
     {
      "old": "yılda 120 tam gün veya 240 yarım gün (haftada ortalama 20 saat) çalışma izniniz",
      "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır; ders döneminde 20 saate kadar çalışılan bir hafta alternatif olarak 2,5 iş günü sayılabilir) çalışma izniniz"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "max-planck-institute-for-heart-and-lung-research-partner-019de9f1",
   "json": {
    "content_blocks_de": [
     {
      "old": "von 120 vollen oder 240 halben Tagen pro Jahr (entspricht 20 Stunden pro Woche)",
      "new": "für bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag; in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "for 120 full days or 240 half days per year (equivalent to 20 hours per week)",
      "new": "for up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day; during the lecture period, a week of up to 20 hours can instead be counted as 2.5 working days)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "max-planck-institute-for-human-cognitive-and-brain-sciences-partner-019de9f1",
   "json": {
    "content_blocks_de": [
     {
      "old": "von 120 vollen Tagen oder 240 halben Tagen pro Jahr (durchschnittlich 20 Stunden pro Woche)",
      "new": "für bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag; in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "for 120 full days or 240 half days per year (averaging 20 hours per week)",
      "new": "for up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day; during the lecture period, a week of up to 20 hours can instead be counted as 2.5 working days)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "max-planck-institute-for-marine-microbiology-partner-019de9f1",
   "json": {
    "content_blocks_de": [
     {
      "old": "20 Stunden pro Woche (oder 120 volle bzw. 240 halbe Tage im Jahr) arbeiten",
      "new": "bis zu 140 Arbeitstage im Jahr arbeiten (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag; in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "20 hours per week (120 full days or 240 half days per year)",
      "new": "up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day; during the lecture period, a week of up to 20 hours can instead be counted as 2.5 working days)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "max-planck-institute-for-mathematics-partner-019de9f1",
   "json": {
    "content_blocks_de": [
     {
      "old": "(derzeit ca. 934 Euro pro Monat, 11.208 Euro pro Jahr)",
      "new": "(derzeit 992 Euro pro Monat, 11.904 Euro pro Jahr)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(currently around 934 Euros per month, 11,208 Euros annually)",
      "new": "(currently 992 Euros per month, 11,904 Euros annually)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "mediadesign-hochschule-fur-design-und-informatik-partner-019ddbba",
   "json": {
    "content_blocks_de": [
     {
      "old": "(derzeit 11.208 Euro jährlich)",
      "new": "(derzeit 11.904 Euro jährlich)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(currently 11,208 Euros annually)",
      "new": "(currently 11,904 Euros annually)"
     }
    ],
    "content_blocks": [
     {
      "old": "(güncel olarak yıllık 11.208 Euro)",
      "new": "(güncel olarak yıllık 11.904 Euro)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "merz-akademie-hochschule-fur-gestaltung-kunst-und-medien-stuttgart-partner-019ddbba",
   "json": {
    "content_blocks_de": [
     {
      "old": "120 volle Tage oder 240 halbe Tage pro Jahr zu arbeiten",
      "new": "bis zu 140 Arbeitstage im Jahr zu arbeiten (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "120 full days or 240 half days per year",
      "new": "up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day)"
     }
    ],
    "content_blocks": [
     {
      "old": "yılda 120 tam gün veya 240 yarım gün çalışma hakkına",
      "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır) çalışma hakkına"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "nta-hochschule-isny-partner-019ddbba",
   "json": {
    "content_blocks_de": [
     {
      "old": "von 120 vollen oder 240 halben Tagen pro Jahr. Dies entspricht durchschnittlich 20 Stunden pro Woche.",
      "new": "für bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag). In der Vorlesungszeit kann alternativ eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden."
     },
     {
      "old": "(z.B. ab 2024 monatlich ca. 934 Euro)",
      "new": "(seit dem 01.09.2024: 992 Euro pro Monat, also 11.904 Euro pro Jahr)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "for 120 full days or 240 half days per year during their studies. This averages out to 20 hours per week.",
      "new": "for up to 140 working days a year during their studies (Arbeitstagekonto; a day of up to 4 hours counts as half a day). During the lecture period, a week of up to 20 hours can instead be counted as 2.5 working days."
     },
     {
      "old": "(e.g., approximately 934 Euros per month as of 2024)",
      "new": "(since 1 September 2024: 992 Euros per month, i.e. 11,904 Euros a year)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "otto-von-guericke-universitat-magdeburg-q655866",
   "json": {
    "content_blocks_de": [
     {
      "old": "120 volle Tage oder 240 halbe Tage pro Jahr zu arbeiten",
      "new": "bis zu 140 Arbeitstage im Jahr zu arbeiten (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "120 full days or 240 half days per year",
      "new": "up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day)"
     }
    ],
    "content_blocks": [
     {
      "old": "yılda 120 tam gün veya 240 yarım gün çalışma hakkınız",
      "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır) çalışma hakkınız"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "owl-university-of-applied-sciences-and-arts-partner-019de9f1",
   "json": {
    "content_blocks_de": [
     {
      "old": "von 120 vollen oder 240 halben Tagen pro Jahr (was durchschnittlich 20 Stunden pro Woche entspricht)",
      "new": "für bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag; in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "for 120 full days or 240 half days per year (which averages out to about 20 hours per week)",
      "new": "for up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day; during the lecture period, a week of up to 20 hours can instead be counted as 2.5 working days)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "padagogische-hochschule-freiburg-q1441203",
   "json": {
    "content_blocks_de": [
     {
      "old": "aktuell etwa 11.208 EUR",
      "new": "aktuell 11.904 EUR"
     },
     {
      "old": "von 20 Stunden pro Woche (während des Semesters) oder 120 vollen Tagen / 240 halben Tagen pro Jahr (in den Semesterferien)",
      "new": "für bis zu 140 Arbeitstage im Jahr (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag; in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "currently around 11,208 EUR",
      "new": "currently 11,904 EUR"
     },
     {
      "old": "20 hours per week during the semester or 120 full days / 240 half days per year during semester breaks",
      "new": "up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day; during the lecture period, a week of up to 20 hours can instead be counted as 2.5 working days)"
     }
    ],
    "content_blocks": [
     {
      "old": "(güncel olarak yaklaşık 11.208 EUR)",
      "new": "(güncel olarak 11.904 EUR)"
     },
     {
      "old": "haftada 20 saat (dönem içinde) veya yılda 120 tam gün / 240 yarım gün (dönem tatillerinde) çalışma izniniz",
      "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır; ders döneminde 20 saate kadar çalışılan bir hafta alternatif olarak 2,5 iş günü sayılabilir) çalışma izniniz"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "ruhr-universitat-bochum-q309948",
   "json": {
    "content_blocks_de": [
     {
      "old": "(in der Regel 120 volle oder 240 halbe Tage pro Jahr)",
      "new": "(bis zu 140 Arbeitstage im Jahr; Arbeitstagekonto, ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(generally 120 full days or 240 half days per year)",
      "new": "(up to 140 working days a year; Arbeitstagekonto, a day of up to 4 hours counts as half a day)"
     }
    ],
    "content_blocks": [
     {
      "old": "(genellikle yılda 120 tam gün veya 240 yarım gün)",
      "new": "(yılda 140 iş günü; Arbeitstagekonto, 4 saate kadar çalışılan gün yarım gün sayılır)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "universitat-greifswald-q165528",
   "json": {
    "content_blocks_de": [
     {
      "old": "(in der Regel 120 volle Tage oder 240 halbe Tage pro Jahr)",
      "new": "(bis zu 140 Arbeitstage im Jahr; Arbeitstagekonto, ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "for a certain number of hours (generally 120 full days or 240 half days per year)",
      "new": "within set limits (up to 140 working days a year; Arbeitstagekonto, a day of up to 4 hours counts as half a day)"
     }
    ],
    "content_blocks": [
     {
      "old": "belirli saatlerde part-time çalışma izni bulunmaktadır (genellikle yılda 120 tam gün veya 240 yarım gün)",
      "new": "sınırlı ölçüde part-time çalışma izni bulunmaktadır (yılda 140 iş günü; Arbeitstagekonto, 4 saate kadar çalışılan gün yarım gün sayılır)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "universitat-osnabruck-q702499",
   "json": {
    "content_blocks_de": [
     {
      "old": "(derzeit jährlich etwa 11.208 Euro)",
      "new": "(derzeit jährlich 11.904 Euro)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(currently around 11,208 Euros annually)",
      "new": "(currently 11,904 Euros annually)"
     }
    ],
    "content_blocks": [
     {
      "old": "(şu an için yıllık yaklaşık 11.208 Euro)",
      "new": "(şu an için yıllık 11.904 Euro)"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "universitat-passau-q704468",
   "json": {
    "content_blocks_de": [
     {
      "old": "Stand 2024: ca. 934 Euro pro Monat",
      "new": "seit dem 01.09.2024: 992 Euro pro Monat, also 11.904 Euro pro Jahr"
     }
    ],
    "content_blocks_en": [
     {
      "old": "as of 2024, it's approximately 934 Euros per month",
      "new": "since 1 September 2024, it has been 992 Euros per month, i.e. 11,904 Euros a year"
     }
    ],
    "content_blocks": [
     {
      "old": "2024 itibarıyla aylık yaklaşık 934 Euro",
      "new": "01.09.2024'ten beri aylık 992 Euro, yani yıllık 11.904 Euro"
     }
    ]
   }
  },
  {
   "table": "universities",
   "slug": "universitat-regensburg-q574571",
   "json": {
    "content_blocks_de": [
     {
      "old": "(in der Regel 120 volle oder 240 halbe Tage pro Jahr)",
      "new": "(bis zu 140 Arbeitstage im Jahr; Arbeitstagekonto, ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
     }
    ],
    "content_blocks_en": [
     {
      "old": "(generally 120 full days or 240 half days per year)",
      "new": "(up to 140 working days a year; Arbeitstagekonto, a day of up to 4 hours counts as half a day)"
     }
    ],
    "content_blocks": [
     {
      "old": "(genellikle yılda 120 tam gün veya 240 yarım gün)",
      "new": "(yılda 140 iş günü; Arbeitstagekonto, 4 saate kadar çalışılan gün yarım gün sayılır)"
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
            throw new RuntimeException('Content Truth Batch 2 (C — şehir/eyalet/üniversite content_blocks): ön kontrol başarısız, hiçbir şey yazılmadı. '.implode(' | ', array_slice($problems, 0, 40)));
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

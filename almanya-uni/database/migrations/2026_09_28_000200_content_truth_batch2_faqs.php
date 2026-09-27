<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Content Truth Sprint — Batch 2 (B — SSS cevapları): site genelinde eski çalışma kuralı ve tutar düzeltmeleri.
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
   "table": "faqs",
   "slug": "ilk-staj-veya-werkstudent-pozisyonu-nasil-bulunur-de",
   "locale": "de",
   "md": {
    "answer_md": [
     {
      "line": "✅ Maximal 538 € monatlich. ✅ Steuer- und versicherungsfrei. ✅ Geringer Druck.",
      "subs": [
       {
        "old": "538 €",
        "new": "603 € (2026)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "lisans-tezimi-yazarken-tam-zamanli-calisabilir-miyim-de",
   "locale": "de",
   "md": {
    "answer_md": [
     {
      "line": "Die Erstellung einer Bachelorarbeit ist ein intensiver Prozess. Es kann sehr herausfordernd sein, dies mit einer Vollzeitbeschäftigung zu verbinden. Als Studierender mit einem Visum in Deutschland sind deine Arbeitszeiten in der Regel begrenzt: Du darfst maximal 120 volle Tage oder 240 halbe Tage pro Jahr arbeiten. Beachte, dass dies deine akademische Leistung beeinflussen kann. Halte dich unbedingt an die Vorgaben deiner Universität und die Visabestimmungen.",
      "subs": [
       {
        "old": "120 volle Tage",
        "new": "140 Arbeitstage (Arbeitstagekonto)"
       },
       {
        "old": "240 halbe Tage",
        "new": "280 halbe Tage (ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "ogrenci-olarak-yillik-120-tam-gun-calisma-hakkimi-universite-icinde-haftada-20-saatten-fazla-calisarak-kullanabilir-miyim-de",
   "locale": "de",
   "fieldsub": {
    "question": [
     {
      "old": "120 volle Arbeitstage",
      "new": "140 Arbeitstage"
     }
    ]
   },
   "md": {
    "answer_md": [
     {
      "line": "Dein Arbeitsrecht mit einer studentischen Aufenthaltserlaubnis in Deutschland ist in der Regel auf 120 volle Tage oder 240 halbe Tage pro Jahr begrenzt. Wenn du während des Semesters mehr als 20 Stunden pro Woche arbeitest, kann das als voller Arbeitstag gezählt werden und wird von deinem Kontingent abgezogen. Beachte auch, dass bestimmte Jobs an der Universität, wie zum Beispiel als wissenschaftliche Hilfskraft (HiWi), unter Umständen anderen Regeln unterliegen können. Kläre deine individuelle Situation am besten direkt mit der Ausländerbehörde ab.",
      "subs": [
       {
        "old": "120 volle Tage",
        "new": "140 Arbeitstage (Arbeitstagekonto)"
       },
       {
        "old": "240 halbe Tage",
        "new": "280 halbe Tage (ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "studienkollegde-tatil-sureleri-ve-calisma-izinleri-nasil-duzenlenir-de",
   "locale": "de",
   "md": {
    "answer_md": [
     {
      "line": "Am Studienkolleg gibt es normalerweise in den Winter- und Sommermonaten insgesamt etwa 2 bis 4 Monate Ferien. Eine Arbeitserlaubnis bekommst du im ersten Jahr meistens nicht. Wenn dir eine Erlaubnis erteilt wird, hast du eventuell das Recht, 120 Tage Vollzeit oder 240 Tage Teilzeit zu arbeiten. Es ist wichtig, dass du die aktuellen Regeln immer bei den offiziellen Stellen bestätigst.",
      "subs": [
       {
        "old": "120 Tage Vollzeit oder 240 Tage Teilzeit zu arbeiten",
        "new": "bis zu 140 Arbeitstage im Jahr zu arbeiten (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden zählt als halber Tag)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "ilk-staj-veya-werkstudent-pozisyonu-nasil-bulunur-en",
   "locale": "en",
   "md": {
    "answer_md": [
     {
      "line": "✅ Max 538 € monthly ✅ Zero tax/insurance ✅ Low pressure",
      "subs": [
       {
        "old": "538 €",
        "new": "603 € (2026)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "lisans-tezimi-yazarken-tam-zamanli-calisabilir-miyim-en",
   "locale": "en",
   "md": {
    "answer_md": [
     {
      "line": "Writing your bachelor's thesis is an intensive process, and combining it with full-time work can be very challenging. In Germany, student visa regulations typically limit your working hours to 120 full days or 240 half days per year. Working beyond these limits or too much during your thesis period could negatively impact your academic performance. Always make sure you stay within the boundaries set by your university and your visa rules.",
      "subs": [
       {
        "old": "120 full days or 240 half days per year",
        "new": "140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "mini-job-538eur-siniri-nedir-en",
   "locale": "en",
   "fieldsub": {
    "question": [
     {
      "old": "538€",
      "new": "603€"
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "ogrenci-olarak-yillik-120-tam-gun-calisma-hakkimi-universite-icinde-haftada-20-saatten-fazla-calisarak-kullanabilir-miyim-en",
   "locale": "en",
   "fieldsub": {
    "question": [
     {
      "old": "120 full days of work",
      "new": "140 working days"
     }
    ]
   },
   "md": {
    "answer_md": [
     {
      "line": "As a student in Germany with a residence permit, your right to work is generally limited to 120 full days or 240 half days per year. If you work more than 20 hours per week during the semester, these hours might be counted as full days, which would reduce your overall quota quickly. Additionally, certain jobs, like student assistant positions (HiWi), might have different regulations. It's always best to confirm your specific situation with the local foreigners' office (Ausländerbehörde) to avoid any issues.",
      "subs": [
       {
        "old": "120 full days or 240 half days per year",
        "new": "140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "studienkollegde-tatil-sureleri-ve-calisma-izinleri-nasil-duzenlenir-en",
   "locale": "en",
   "md": {
    "answer_md": [
     {
      "line": "At a Studienkolleg, you typically get around 2-4 months of holidays in total, usually split between winter and summer breaks. Getting a work permit during your first year at a Studienkolleg can be challenging, and it might not be granted. If you are allowed to work, you might have the right to work 120 full days or 240 half days per year. It's crucial to verify the most current rules and regulations with official sources, as these can change.",
      "subs": [
       {
        "old": "120 full days",
        "new": "up to 140 working days (Arbeitstagekonto)"
       },
       {
        "old": "240 half days",
        "new": "280 half days (a day of up to 4 hours counts as half a day)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "almanya-ogrenci-vizesi-kac-yil-gecerli-oluyor-en",
   "locale": "en",
   "md": {
    "answer_md": [
     {
      "line": "You're allowed to work 120 full days or 240 half-days per year.",
      "subs": [
       {
        "old": "120 full days or 240 half-days per year",
        "new": "up to 140 working days a year (Arbeitstagekonto; a day of up to 4 hours counts as half a day)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "ilk-staj-veya-werkstudent-pozisyonu-nasil-bulunur",
   "locale": "tr",
   "md": {
    "answer_md": [
     {
      "line": "✅ Aylık 538 € max ✅ Vergi/sigorta sıfır ✅ Düşük baskı",
      "subs": [
       {
        "old": "538 €",
        "new": "603 € (2026)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "linkedin-de-uzerinden-is-aramak-ogrenciler-icin-etkili-mi",
   "locale": "tr",
   "md": {
    "answer_md": [
     {
      "line": "⚠️ LinkedIn DE'de Türkçe içerik az — Almanca + İngilizce ağırlık ver ⚠️ Yaz dönemi başvurular yoğun (Eylül başı master başlangıcı için) ⚠️ AB dışı öğrenci için izin durumu ilk soru olabilir (120 gün, Werkstudent vs.) ⚠️ Premium üyelik (LinkedIn Premium 29.99 €/ay) öğrenciler için %50 indirim — InMail için yararlı",
      "subs": [
       {
        "old": "120 gün",
        "new": "yılda 140 iş günü"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "lisans-tezimi-yazarken-tam-zamanli-calisabilir-miyim",
   "locale": "tr",
   "md": {
    "answer_md": [
     {
      "line": "Lisans tezi yazımı yoğun bir süreçtir ve tam zamanlı çalışma ile bir arada yürütmek zorlayıcı olabilir. Almanya'da öğrenci vizesiyle çalışma saatleri genellikle sınırlıdır (yılda 120 tam gün veya 240 yarım gün). Bu durum, akademik performansınızı etkileyebilir, bu nedenle üniversitenizin ve vize kurallarının izin verdiği sınırlar içinde kalmanız önemlidir.",
      "subs": [
       {
        "old": "yılda 120 tam gün veya 240 yarım gün",
        "new": "yılda 140 iş günü; Arbeitstagekonto, 4 saate kadar çalışılan gün yarım gün sayılır"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "master-sirasinda-part-time-calismak-mumkun-mu",
   "locale": "tr",
   "md": {
    "answer_md": [
     {
      "line": "✅ Aylık max 538 € ✅ Sigorta + vergi sıfır ✅ Yüksek akademik yük altındaysan ideal",
      "subs": [
       {
        "old": "538 €",
        "new": "603 € (2026)"
       }
      ]
     },
     {
      "line": "✅ Üniversite/bölüm bünyesinde ✅ Saatlik 12-18 € (TV-L tarifesi) ✅ Aylık 800-1,200 € (5-8 saat/hafta)",
      "subs": [
       {
        "old": "Saatlik 12-18 €",
        "new": "Saatlik 13,90-18 €"
       }
      ]
     },
     {
      "line": "⚠️ 20 saat/hafta sınırı katı — semestre içinde ⚠️ Yaz tatili 26 hafta full-time mümkün — bu süreçte 40 saat çalışabilirsin ⚠️ AB dışı öğrenci 120 tam gün/yıl sınırını dikkatli takip et ⚠️ Hilfskraft maaşı düşük ama akademik network için altın değerli",
      "subs": [
       {
        "old": "120 tam gün/yıl",
        "new": "yılda 140 iş günü (Arbeitstagekonto)"
       },
       {
        "old": " — semestre içinde",
        "new": " — Werkstudent sosyal sigorta kuralı, oturum izni sınırı değil (semestre içinde)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "mini-job-538eur-siniri-nedir",
   "locale": "tr",
   "fieldsub": {
    "question": [
     {
      "old": "538€",
      "new": "603€"
     }
    ]
   },
   "rewrite": {
    "answer_md": {
     "fingerprints": [
      "2024 başında 520 €'dan 538 €'ya yükseltildi.",
      "Yıllık 120 gün sınırı (AB dışı) dahil",
      "Mini-Job: 538 €/ay (sınır)",
      "sınırı dikkatli takip et"
     ],
     "new": "**Mini-Job**, Almanya'da aylık **603 €** (2026) sınırına kadar olan **sigorta primi sıfır + vergi sıfır** iş türü. Sınır 520 € (2023) → 538 € (2024) → 556 € (2025) → 603 € (2026) olarak yükseldi.\n\n## Mini-Job Tanımı\n\n✅ **Geringfügige Beschäftigung** — düşük ücretli iş\n✅ Aylık **maksimum 603 €** (yıllık 7,236 €)\n✅ Sigorta primi sıfır (sen 0 ödüyorsun)\n✅ Vergi sıfır (yıllık < 7,236 € ise)\n✅ İşveren **%30 toplu prim** öder (sigorta + vergi + emeklilik)\n\n## Mini-Job Türleri\n\n### 1. 603 € Mini-Job (Saatlik İş)\n✅ Aylık 603 € sınırı (2026)\n✅ Çalışan: 0 ödeme\n✅ İşveren: %30 toplu prim\n\n### 2. Kısa Süreli Mini-Job\n✅ **Maksimum 3 ay** veya **70 iş günü** (yıllık)\n✅ Aylık limit YOK (603 € aşılabilir)\n✅ Genelde mevsimsel iş (yaz tatili işleri)\n✅ Sigorta primi sıfır\n\n## 603 € Mini-Job Özellikleri\n\n### Avantajları\n✅ **Brutto = Net** (kesinti yok)\n✅ Çok esnek (haftalık saat sınırı az)\n✅ **2 Mini-Job + 1 ana iş** kombinasyonu mümkün\n✅ **Vergisiz** (yıllık < 7,236 €)\n\n### Dezavantajları\n❌ Aylık limit düşük (603 €)\n❌ Emeklilik birikimi sınırlı\n❌ Werkstudent statüsünden ucuz çıkış\n\n## Hangi İşler Mini-Job Olarak Yaygın?\n\n### Catering / Restoran\n- Garson, mutfak yardımcısı, kasiyer\n- Saatlik 13,90-15 € (2026 asgari ücret: 13,90 €)\n- Aylık 30-40 saat = 417-600 €\n\n### Lieferando / Wolt / Bolt Food\n- Kurye / sürücü\n- Saatlik 12-14 € (çalışan statüsünde en az 13,90 € asgari ücret; serbest kuryelikte asgari ücret uygulanmaz)\n- ~10 saat/hafta ≈ 600 €/ay\n\n### Mağaza / Süpermarket\n- Kasiyer, raf düzenleme\n- Saatlik 13,90-15 €\n- 8-10 saat/hafta\n\n### Spor Salonu / Otel\n- Resepsiyon, temizlik, fitness eğitmen\n- Saatlik 13,90-16 €\n\n### Dil Asistanı / Tutor\n- Üniversite tutorluğu, özel ders\n- Saatlik 15-25 € (yüksek nitelikli)\n\n### Online İşler\n- Çevirmen, içerik yazma, sosyal medya\n- Saatlik 15-30 €\n\n## Mini-Job vs Werkstudent\n\n| Özellik | Mini-Job | Werkstudent |\n| --- | --- | --- |\n| Aylık limit | 603 € | Sınırsız |\n| Saat sınırı | Yasal haftalık saat sınırı yok (603 €, 13,90 € asgari ücretle ~10 saat/haftaya karşılık gelir) | 20 saat/hafta (ders dönemi, sosyal sigorta kuralı) |\n| Sigorta | 0 | 131 € öğrenci tarifesi |\n| Vergi | 0 (genelde) | %9.3 emeklilik |\n| Mezuniyet sonrası | Devam etmez | Tam zamanlıya dönüşür |\n| Akademik etki | Düşük | Orta |\n\n## Hangi Senaryoda Hangisini Seç?\n\n### Mini-Job Seç:\n- **Düşük saat** çalışmak istiyorsan (~10 saat/hafta)\n- **Vergi/sigorta karışıklığı** istemiyorsan\n- **Akademik fokus** önemli (saat az)\n- Yaz tatilinde de zaman ayırmak istiyorsun\n\n### Werkstudent Seç:\n- **Yüksek maaş** istiyorsan (1,000+ €/ay)\n- **Profesyonel deneyim + CV** öncelikli\n- **20 saat/hafta** yönetebilir akademik yük\n- **Mezuniyet sonrası iş** garantisi istiyor\n\n## Mini-Job + Werkstudent Kombinasyonu\n\n✅ **Aynı anda** Mini-Job + Werkstudent mümkün\n✅ Toplam saat: 20 + 12 = 32 saat/hafta (semestre içi)\n⚠️ **20 saat Werkstudent + 5 saat Mini-Job daha güvenli** (saat sınırı dikkat)\n⚠️ AB dışı için iki iş birlikte yıllık 140 iş günü hesabına (Arbeitstagekonto) dahil\n\n### Gelir Hesabı (Kombinasyon)\n- Werkstudent: 20 saat × 4 hafta × 15 € = 1,200 €/ay brutto\n- Mini-Job: 603 €/ay (sınır)\n- **Toplam: ~1,803 €/ay brutto** (Mini-Job tam, Werkstudent ~%9.3 emeklilik kesintisi)\n- **Net: ~1,691 €/ay**\n\n## Vergi Durumu\n\n### Mini-Job Pauschalsteuer (Genel Vergi)\n- İşveren **%2 toplu vergi** öder (sen değil)\n- Yıllık 7,236 €'ya kadar **vergi sıfır**\n\n### Mini-Job + Diğer Gelir\n- Yıllık toplam **11,604 €** (Grundfreibetrag) altı vergi sıfır\n- Aşılırsa vergi başlar (ek geliri etkiler)\n\n## Sigorta Durumu\n\n### Mini-Job Sigorta Primi\n- **İşveren toplu primi:** %30 (sigorta + vergi + emeklilik)\n- **Sen 0 prim ödüyorsun**\n- Sağlık sigortan **işveren tarafından dahil** (öğrenci sigortası farklı)\n\n### Öğrenci Sigortası Durumu\n✅ Öğrenci sigortası **131 €/ay** sen ödüyorsun (Mini-Job dışı)\n✅ Mini-Job sigorta primi etkilemiyor (sen prim ödemiyorsun)\n✅ Sağlık sigortası işveren değil, kendi GKV'n üzerinden\n\n## Pratik Tavsiye\n\n### Yeni Öğrenci için\n1. **İlk Mini-Job** ile başla — düşük baskı + para kazanma\n2. Almanya kültürüne alış\n3. Sonraki dönem **Werkstudent** dene (profesyonel)\n\n### Yüksek Akademik Yük (Mühendislik, Tıp)\n✅ **Mini-Job** daha güvenli — akademik performans korunur\n\n### Profesyonel Kariyer Hedefli\n✅ **Werkstudent** öner — CV + para + deneyim\n\n## Önemli Notlar\n\n⚠️ **603 € sınırını aşarsan otomatik Werkstudent statüsüne geçilmez** — işverenle değişiklik gerek\n⚠️ **2 Mini-Job toplam 603 €'yu aşamaz** (her ayrı işveren, toplam sınır)\n⚠️ **Mini-Job + Werkstudent aynı işveren olmaz** — ayrı işverenler\n\n## Vize ve Çalışma İzni\n\n### AB Vatandaşı\n✅ Mini-Job sınırsız\n✅ Saat sınırı yok\n\n### Türk Vatandaşı + Öğrenci Vizesi\n⚠️ **Yılda 140 iş günü** sınırını (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır) dikkatli takip et\n⚠️ Mini-Job 5 saat/hafta = düşük gün sayısı, aşılmaz genelde\n\nİlgili: [Werkstudent statüsü](/sss/is/werkstudent-nedir-kim-basvurabilir) | [Haftalık saat sınırı](/sss/is/ogrenci-olarak-haftada-kac-saat-calisabilirim)."
    }
   }
  },
  {
   "table": "faqs",
   "slug": "muhendislik-ogrencisi-icin-is-arama-maas-ortalamasi-ne-kadar",
   "locale": "tr",
   "md": {
    "answer_md": [
     {
      "line": "2026: ~12.41 €/saat",
      "subs": [
       {
        "old": "~12.41 €/saat",
        "new": "13.90 €/saat"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "ogrenci-olarak-calisirken-sosyal-guvenlik-kesintisi-var-mi",
   "locale": "tr",
   "md": {
    "answer_md": [
     {
      "line": "538 € Mini-Job",
      "subs": [
       {
        "old": "538 €",
        "new": "603 €"
       }
      ]
     },
     {
      "line": "✅ Sıfır işçi prim (sen ödüyorsun: 0) ❌ İşveren toplu prim öder: %30 (sigorta + vergi + emeklilik) ✅ Brutto = Net (her ay 538 € banka hesabına)",
      "subs": [
       {
        "old": "538 €",
        "new": "603 €"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "ogrenci-olarak-yillik-120-tam-gun-calisma-hakkimi-universite-icinde-haftada-20-saatten-fazla-calisarak-kullanabilir-miyim",
   "locale": "tr",
   "fieldsub": {
    "question": [
     {
      "old": "120 tam gün",
      "new": "140 iş günü"
     }
    ]
   },
   "md": {
    "answer_md": [
     {
      "line": "Öğrenci ikamet izniyle Almanya'da çalışma hakkınız genellikle yılda 120 tam gün veya 240 yarım gün ile sınırlıdır. Dönem içinde haftada 20 saati aşan çalışma tam gün olarak sayılabilir ve bu kotadan düşer. Ayrıca üniversitedeki asistanlık (HiWi) gibi bazı işler farklı kurallara tabi olabilir; kendi durumunuz için yabancılar dairesiyle (Ausländerbehörde) teyit etmeniz önerilir.",
      "subs": [
       {
        "old": "yılda 120 tam gün veya 240 yarım gün",
        "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "werkstudent-basvurusu-ne-zaman-yapmali",
   "locale": "tr",
   "md": {
    "answer_md": [
     {
      "line": "✅ Werkstudent başvurusunda işveren Aufenthaltstitel isteyebilir ✅ Genelde 120 tam gün/yıl sınırı bilgilendirilir ✅ Bazı işverenler AB vatandaşı tercih ediyor (vize karışıklığı sebebi)",
      "subs": [
       {
        "old": "120 tam gün/yıl",
        "new": "yılda 140 iş günü"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "bilgisayar-muhendisligi-master-icin-deatch-karsilastirmasi",
   "locale": "tr",
   "md": {
    "answer_md": [
     {
      "line": "120 gün tam / 240 yarı",
      "subs": [
       {
        "old": "120 gün tam / 240 yarı",
        "new": "140 iş günü/yıl (Arbeitstagekonto)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "ogrenci-saglik-sigortasi-krankenversicherung-aylik-ne-kadar",
   "locale": "tr",
   "md": {
    "answer_md": [
     {
      "line": "538 € altı kazanç → Sigorta primi işveren ödüyor, sen 0 ödüyorsun",
      "subs": [
       {
        "old": "538 €",
        "new": "603 € (2026)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "studienkolleg-ogrencileri-icin-sigorta-zorunlulugu-nedir",
   "locale": "tr",
   "md": {
    "answer_md": [
     {
      "line": "✅ Studienkolleg öğrencisi Werkstudent statüsü kazanır — aynı haftalık 20 saat sınırı geçerli ✅ Mini-Job (538 € altı) → Sigorta primi sıfır ✅ Studienkolleg + Werkstudent kombinasyonu yaygın (özellikle T-Kurs)",
      "subs": [
       {
        "old": "538 €",
        "new": "603 €"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "werkstudent-icin-sigorta-zorunlu-mu",
   "locale": "tr",
   "md": {
    "answer_md": [
     {
      "line": "Mini-Job (538 € altı) Farklı",
      "subs": [
       {
        "old": "538 €",
        "new": "603 €"
       }
      ]
     },
     {
      "line": "⚠️ Mini-Job + Werkstudent kombinasyonu: 538 €/ay altı kazanç → Mini-Job tarifesi geçerli, üstü → Werkstudent tarifesi.",
      "subs": [
       {
        "old": "538 €/ay",
        "new": "603 €/ay (2026)"
       }
      ]
     },
     {
      "line": "Mini-Job (538 € altı) → sigorta dahil işveren ödüyor",
      "subs": [
       {
        "old": "538 €",
        "new": "603 €"
       }
      ]
     },
     {
      "line": "538 € üstü kazanç + Werkstudent değilsen → Normal çalışan sigortası kesilir (~%22 maaştan)",
      "subs": [
       {
        "old": "538 €",
        "new": "603 €"
       }
      ]
     },
     {
      "line": "İlgili: Werkstudent nedir | Mini-Job 538 € sınırı.",
      "subs": [
       {
        "old": "Mini-Job 538 €",
        "new": "Mini-Job 603 €"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "studienkolleg-icin-vize-basvurusu-ozel-mi",
   "locale": "tr",
   "md": {
    "answer_md": [
     {
      "line": "✅ 240 yarı zamanlı gün veya 120 tam gün/yıl",
      "subs": [
       {
        "old": "240 yarı zamanlı gün veya 120 tam gün/yıl",
        "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "studienkollegde-tatil-sureleri-ve-calisma-izinleri-nasil-duzenlenir",
   "locale": "tr",
   "md": {
    "answer_md": [
     {
      "line": "Studienkolleg'de genellikle kış ve yaz aylarında toplamda 2-4 ay civarı tatil dönemi bulunur. Çalışma izni, ilk yıl genellikle alınamayabilir. İzin verildiğinde, tam zamanlı 120 gün veya yarı zamanlı 240 gün çalışma hakkınız olabilir. Güncel kuralları resmi kaynaklardan doğrulamanız önemlidir.",
      "subs": [
       {
        "old": "tam zamanlı 120 gün veya yarı zamanlı 240 gün",
        "new": "yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "almanya-ogrenci-vizesi-kac-yil-gecerli-oluyor",
   "locale": "tr",
   "md": {
    "answer_md": [
     {
      "line": "Tam zamanlı 120 gün veya yarı zamanlı 240 gün/yıl çalışma izni",
      "subs": [
       {
        "old": "Tam zamanlı",
        "new": "Yılda"
       },
       {
        "old": "120 gün veya yarı zamanlı 240 gün/yıl",
        "new": "140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır)"
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "lieferandoda-ogrenci-olarak-calisinca-ne-kadar-kazanilir",
   "locale": "tr",
   "rewrite": {
    "answer_md": {
     "fingerprints": [
      "120 tam gün × 8 saat = 960 saat",
      "Aylık ortalama 538 € altında tut",
      "yıllık 520 saat (~65 tam gün)",
      "AB dışı için 120 gün sınırı"
     ],
     "new": "**Lieferando / Wolt / Bolt Food / Uber Eats** öğrenciler için en yaygın **yan iş seçenekleri**. Saatlik kazanç **12-18 €** civarında.\n\n## 2026 Almanya Gıda Teslimat Pazarı\n\n### Aktif Platformlar\n- **Lieferando** (en büyük, Just Eat Takeaway grubu)\n- **Wolt** (Helsinki menşeili, Almanya'da büyüyor)\n- **Bolt Food** (Estonya menşeili)\n- **Uber Eats** (San Francisco)\n- **Gorillas / Flink** (10-15 dakika market teslimat)\n\n## Lieferando Maaş Detayları\n\n### Saatlik Kazanç\n- **Saatlik temel ücret:** 13,90-14 € (2026 asgari ücret: 13,90 €)\n- **+ Tip / bahşiş:** ortalama 1-3 € saatlik\n- **+ Bonus (yağmurlu hava, yoğun saat):** 1-2 € ek\n- **Toplam ortalama:** **14-18 €/saat brutto**\n\n### Aylık Kazanç (Mini-Job — 603 €)\n- Saatlik 14 € × 10 saat/hafta = **140 €/hafta**\n- Aylık ~600 € brutto → 603 € sınıra **çok yakın**\n- Bazı haftalar 603 € sınırını aşabilirsin (haftalık ortalama izlemek)\n\n### Aylık Kazanç (Werkstudent — 20 saat/hafta)\n- 14 € × 20 saat × 4 hafta = **1,120 €/ay brutto**\n- Net: ~1,015 €/ay (Werkstudent kesintileri)\n\n### Aylık Kazanç (Full-time Yaz Tatili)\n- 14 € × 40 saat × 4 hafta = **2,240 €/ay brutto**\n- Net: ~2,030 €/ay\n- 26 hafta tatil dönemi mümkün\n\n## Wolt Maaş Detayları\n\n### Saatlik Kazanç\n- **Baz ücret:** Saatlik 11-14 €\n- **Bahşiş:** Lieferando'dan biraz daha yüksek (uluslararası uygulama)\n- **Toplam:** 13-17 €/saat\n\n### Wolt vs Lieferando\n| Özellik | Lieferando | Wolt |\n| --- | --- | --- |\n| Saatlik temel | 13,90-14 € | 11-14 € |\n| Tip kültürü | Düşük | Yüksek |\n| Aktif şehirler | Tüm DE | Büyük şehirler |\n| Sözleşme türü | Werkstudent + Mini-Job | Bağımsız çalışan (Freelance) |\n\n## Bolt Food\n\n- **Saatlik:** 12-15 €\n- **Yeni şehirlere açılıyor** (Berlin, Münih, Hamburg merkez)\n- Tip yüksek (uygulama promosyon)\n\n## Uber Eats\n\n- **Saatlik:** 11-14 €\n- **Almanya'da kayıt olmak zor** (uygulama Türk vatandaşı için sınırlı)\n- Şirket kayıt + GST gerek\n\n## Bisikletli mi, Scooter mı, Araba mı?\n\n### Bisiklet (En Yaygın)\n✅ **Çevre dostu + sağlıklı**\n✅ Hızlı manevra (şehir içi)\n✅ Yağmur/kar zor\n✅ Aylık bisiklet bakım: 20-30 €\n\n### E-Bike / Elektrikli Bisiklet\n✅ **Daha az yorucu** (uzun mesafe)\n✅ Lieferando bazı şehirlerde **e-bike sağlıyor** (kiralık)\n✅ Maliyet: 0 (şirket sağlarsa)\n\n### Scooter (Elektrikli)\n✅ Hızlı (büyük şehirde verimli)\n✅ Sürücü ehliyeti gerek (motorsiklet)\n✅ Aylık scooter kiralık: 50-100 €\n\n### Araba\n⚠️ **Lieferando'da nadir** (genelde bisiklet/scooter)\n⚠️ Yakıt + park ücreti masraflı\n⚠️ Şehir içi bisiklete göre yavaş\n\n## Çalışma Saat Sınırları\n\n### Mini-Job (603 €)\n- Saatlik 14 € × ~10 saat/hafta ≈ 600 €/ay\n- Aylık ortalama 603 € altında tut\n\n### Werkstudent (20 saat/hafta)\n- Saatlik 14 € × 20 saat = 1,120 € brutto/ay\n- Sigorta primi 131 € + emeklilik %9.3 → net ~1,015 €\n\n### Yıllık Sınır (Türk vatandaşı)\n- Yılda **140 iş günü** (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır)\n- Ders döneminde en fazla 20 saatlik bir hafta alternatif olarak 2,5 iş günü sayılabilir — senin lehine olan hesap geçerli\n- Eğer Mini-Job (10 saat/hafta, örn. 4 saatlik 2-3 vardiya) → haftada 1-1,5 iş günü (yıllık ~52-78 iş günü)\n- ⚠️ 140 gün sınırına yakın olma — başka iş varsa toplamı düşün\n\n## Lieferando Başvuru Süreci\n\n### Adım 1: Online Başvuru\n- *lieferando.de/karriere* → \"Fahrer (Kurye)\"\n- Konum seç + iletişim bilgileri\n\n### Adım 2: Belgeler\n✅ **Pasaport + Vize/Aufenthaltstitel**\n✅ **SteuerID**\n✅ **Sağlık sigortası kanıtı**\n✅ **IBAN** (maaş yatırma)\n✅ **Bisiklet/scooter** (kendi veya kiralık)\n\n### Adım 3: Görüşme + Eğitim\n- 30-60 dakika online görüşme\n- Almanca/İngilizce yeterli\n- Lieferando uygulaması + güvenlik eğitimi\n\n### Adım 4: İşe Başlama\n- **1-2 hafta** içinde işe başlayabilirsin\n- Lieferando üniforması + sırt çantası\n- İlk gün diğer kuryeyle birlikte yöneticilik\n\n## Sektör Avantajları\n\n### Esneklik\n✅ **Vardiya planı kendin yaparsın** — saat seçimi serbest\n✅ Hafta sonu çalışabilirsin (saatlik %15-20 daha yüksek)\n✅ Akşamları daha yüksek bahşiş\n\n### Ek Getiriler\n✅ **Yağmurlu / soğuk hava bonus** (saatlik +2-3 €)\n✅ **Pik saatler bonus** (12-14 + 18-21 arası)\n✅ **Yılbaşı, Sevgililer Günü vs.** ek bonuslar\n\n## Sektör Dezavantajları\n\n❌ **Fiziksel yorucu** — bisiklet/scooter ile şehir gezme\n❌ **Hava bağımlı** — yağmurda zor\n❌ **Düşük kariyer potansiyeli** — sektörde uzun vadeli artış sınırlı\n❌ **Akademik perspektifte değersiz** — CV'ye uygun değil (Werkstudent kategorisinde \"kurye\" deneyimi sınırlı katkı)\n\n## Pratik Tavsiye\n\n### Kısa Vadeli (Geçici Gelir)\n✅ **Mini-Job (603 €)** çok rahat — düşük baskı\n✅ Akademik fokusu korur\n✅ İlk dönem Almanca dil pratiği için iyi\n\n### Orta Vadeli (Ek Gelir)\n✅ **Werkstudent (20 saat)** + Sperrkonto kombinasyonu\n✅ Aylık ~1,000 € net + Sperrkonto 992 € = 2,000 € toplam\n✅ Türkiye'ye para gönderme rahat\n\n### Uzun Vadeli (Mezun + Almanya'da)\n⚠️ **Lieferando ile uzun vadeli kariyer önerilmez**\n✅ Geçici çözüm sonrası **profesyonel sektöre** geçiş\n\n## Lieferando vs Diğer Werkstudent İşleri\n\n| Faktör | Lieferando | İT Werkstudent | Akademik Hilfskraft |\n| --- | --- | --- | --- |\n| Saatlik | 14-18 € | 18-25 € | 13,90-18 € |\n| Esneklik | Yüksek | Düşük | Orta |\n| CV katkısı | Düşük | Yüksek | Orta-Yüksek |\n| Fiziksel yorgunluk | Yüksek | Düşük | Düşük |\n| Akademik etki | Düşük | Orta | Yüksek (akademik bağlantı) |\n\n## Önemli Notlar\n\n⚠️ **Lieferando çalışan sigortası** Werkstudent kategorisinde tüm hakları sağlar\n⚠️ **Sezonsal değişim** — kış aylarında daha az teslimat, yaz daha bol\n⚠️ **Tip kültürü Almanya'da düşük** (Türkiye + USA'dan az), tip beklemeden plan yap\n⚠️ **AB dışı için yılda 140 iş günü sınırı** (4 saate kadar = yarım gün) — Lieferando çalışma günlerini takip et\n\nİlgili: [Mini-Job](/sss/is/mini-job-538eur-siniri-nedir) | [Werkstudent](/sss/is/werkstudent-nedir-kim-basvurabilir)."
    }
   }
  },
  {
   "table": "faqs",
   "slug": "ogrenci-olarak-haftada-kac-saat-calisabilirim",
   "locale": "tr",
   "rewrite": {
    "answer_md": {
     "fingerprints": [
      "Kombinasyon mümkün: 60 tam gün + 120 yarım gün",
      "80 saat/ay = 10 tam gün",
      "120 tam gün / 240 yarım gün yıllık",
      "Tatil dönemi 30 saat (40'tan az tutarak) = 360 saat"
     ],
     "new": "Öğrenci olarak Almanya'da çalışma saatleri **vize statüsü** ve **iş türü**ne göre değişir.\n\n## AB Vatandaşı (EU/EEA Citizens)\n✅ **Sınırsız** çalışabilir\n✅ Almanya vatandaşları ile aynı haklar\n✅ Sadece Werkstudent statüsünü korumak için 20 saat/hafta sınırı (sosyal sigorta kuralı)\n\n## Türk Vatandaşı + AB Dışı (Standart Öğrenci Vizesi)\n\n### Yıllık Limit\n✅ **Yılda 140 iş günü** (Arbeitstagekonto, 01.03.2024'ten beri)\n✅ Hesap öğrenci lehine yapılır — her hafta için senin için daha avantajlı sayım geçerli\n\n### Tam vs Yarım Gün\n- **Tam gün:** 4 saatten fazla çalışılan gün\n- **Yarım gün:** 4 saate kadar\n- **20 saatlik hafta:** Ders döneminde en fazla 20 saat çalıştığın bir hafta alternatif olarak 2,5 iş günü sayılabilir\n- Kombinasyon mümkün: örn. 100 tam gün + 80 yarım gün = 140 iş günü\n\n### Werkstudent Hesabı\n- Ders döneminde haftalık 20 saat = **2,5 iş günü/hafta** (20 saatlik hafta kuralı)\n- Aynı 20 saati 5 güne yayarsan (4'er saat) 5 yarım gün = yine 2,5 iş günü\n- Werkstudent'ın 20 saat kuralı **sosyal sigorta** kuralıdır; oturum izni için ayrıca 140 iş günü hesabı geçerlidir\n\n### Pratik Yorum\n- Werkstudent statüsünde 20 saat/hafta = 80 saat/ay = haftada 2,5 iş günü\n- Yıl boyu her hafta en fazla 20 saat → 52 × 2,5 = ~130 iş günü\n- Tatilde tam zamanlı haftalar (haftada 5 iş günü) eklenince 140 gün hızla dolar — **sigortalı Werkstudent olmak 140 gün hesabından muaf tutmaz**\n\n## Haftalık Saat Senaryoları\n\n### Senaryo 1: Werkstudent (Önerilen)\n- **20 saat/hafta** semestre içi\n- 40 saat/hafta tatil dönemi (max 26 hafta)\n- Sigorta primi: 131 €/ay (öğrenci tarifesi)\n\n### Senaryo 2: Mini-Job (Düşük Saat)\n- **~10 saat/hafta** — 2026'da 603 € aylık Minijob sınırı, 13,90 € asgari ücretle yaklaşık 10 saat/haftaya karşılık gelir. Bu, yasal bir haftalık saat sınırı değildir.\n- Sigorta primi: İşveren öder (sen 0)\n- Vergisiz çalışma\n\n### Senaryo 3: Ders Döneminde 20+ Saat Çalışma\n⚠️ **Werkstudent statüsünü kaybedersin**\n⚠️ Çalışan sigortası başlar (~%22 maaştan)\n⚠️ AB dışı için: bu haftalar 2,5 gün sayılamaz — çalıştığın her gün tam/yarım gün olarak 140 iş günü hesabından düşer\n\n### Senaryo 4: Çoklu İş\n✅ Werkstudent (20 saat) + Mini-Job (5 saat) = toplam 25 saat\n⚠️ Toplam saat sınırına dikkat\n⚠️ Vize için 140 iş günü sınırına dikkat (ders döneminde 25 saatlik hafta 2,5 gün sayılamaz)\n\n## Vize ve Çalışma İzni\n\n### Öğrenci Vizesi (§16b AufenthG)\n✅ Yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır)\n✅ Werkstudent + Mini-Job kombinasyonu mümkün\n✅ İmmatrikulation devam ettiği sürece\n\n### Dil Kursu Vizesi (§16f AufenthG)\n⚠️ Çalışma **çoğunlukla yasak**\n⚠️ Sadece dil kursu yoğun ders dönemi içerisinde uygulanır\n⚠️ İstisnai durumlar: işveren özel izin\n\n### Üniversite Başvuru Vizesi (§17 AufenthG)\n⚠️ Çalışma **kısıtlı**\n⚠️ Genelde Werkstudent olarak çalışılamaz\n\n### Mavi Kart (Blue Card EU)\n✅ **Tam zamanlı** çalışma (40 saat/hafta)\n✅ Master/PhD mezunları için\n\n## Saat Sınırını Aşma Sonuçları\n\n### Werkstudent → Normal Çalışan Geçişi\n- 20 saat/hafta + üniversite ders saatleri = **fazla yük**\n- Maaş kesintileri **%14.6 + %18.6** sigorta (öğrenci tarifesi sona)\n- Net maaş **%15-20 azalır**\n\n### Vize İhlali (AB Dışı)\n⚠️ 140 iş günü aşıldığında **Ausländerbehörde uyarı veriyor**\n⚠️ Tekrar aşılırsa **vize iptal riski**\n⚠️ Çalışma kanıtları (Lohnabrechnung, Steuerbescheinigung) kontrol ediyor\n\n## Pratik İpuçları\n\n✅ **Yıllık çalışma günlerinin kaydını tut** — kendi yapma kayıt\n✅ **Lohnabrechnung'larını sakla** — vize uzatma için kanıt\n✅ **Werkstudent statüsünü kaybetme** — 20 saat sınırını çok dikkatli takip et\n✅ **Yaz tatilinde tam zamanlı çalışırken** Werkstudent statüsü devam (sayıyor)\n\n## Çalışma Süresi Hesabı (Örnek)\n\n### Senaryo: Yıl Boyu Werkstudent + Yaz Full-Time\n- Semestre içi (Ekim-Şubat + Nisan-Temmuz = 8 ay): 20 saat/hafta × 32 hafta → 32 × 2,5 = 80 iş günü\n- Semestre tatili (Mart-Nisan + Ağustos-Eylül = ~12 hafta): 40 saat/hafta (5 iş günü) × 12 = 60 iş günü\n- **Toplam yıllık:** 140 iş günü\n- ⚠️ **140 iş günü sınırı tamamen doluyor** — tek bir ek iş günü vize ihlali riski (AB dışı için)\n\n### Çözüm\n- Semestre içi 20 saat (Werkstudent statüsü)\n- Tatil dönemi 30 saat, 5 yerine 4 güne yayarak (haftada 4 iş günü) = 12 × 4 = 48 iş günü\n- **Toplam: 80 + 48 = 128 iş günü** — sınıra yakın\n- Daha güvenli: tatil dönemi de 20 saat tutmak (örn. 2 uzun gün + 1 gün en fazla 4 saat = haftada 2,5 iş günü)\n\n## Çoklu İşveren Durumu\n\n✅ **Birden fazla işveren** mümkün ama:\n- **Toplam haftalık saat 20'yi aşmamalı** (semestre içi)\n- Her işveren ile Werkstudent statüsü ayrı tutulur\n- **Bütün işverenlere üniversite öğrenci olduğunu bildir** (gerek vergi + sigorta için)\n\n## Önemli Notlar\n\n⚠️ **20 saat sınırı GROSS saat değildir** — net çalışma saati\n⚠️ **Mesai (Überstunden) sayıyor** — toplam aylık 80 saatte tutmak gerek\n⚠️ **Werkstudent statüsünü tam zamanlı işe çevirmek** sigorta nedeniyle pahalı\n⚠️ **Mezun olduğunda** statü değişir → tam zamanlı çalışan sigortası\n\nİlgili: [Werkstudent](/sss/is/werkstudent-nedir-kim-basvurabilir) | [Mini-Job](/sss/is/mini-job-538eur-siniri-nedir)."
    }
   }
  },
  {
   "table": "faqs",
   "slug": "werkstudent-nedir-kim-basvurabilir",
   "locale": "tr",
   "rewrite": {
    "answer_md": {
     "fingerprints": [
      "haftalık 20 saate kadar çalışma izni",
      "yıllık 120 tam gün veya 240 yarım gün sınırı (vize şartı)",
      "veya yarım günleri kombine etme",
      "Maaş: 12-18 €/saat (Tarif TV-L)"
     ],
     "new": "**Werkstudent**, Almanya'da öğrencilere özel **avantajlı iş statüsü** — sigorta kesintilerinden büyük oranda muaf; ders döneminde haftalık 20 saate kadar çalışırsan bu sigorta avantajı korunur (sosyal sigorta kuralı, vize sınırı değil).\n\n## Werkstudent Statüsü Genel Tanım\n\n✅ **Yarı zamanlı çalışan + tam zamanlı öğrenci** kombinasyonu\n✅ Aktif **immatrikulation** (üniversite kaydı) şart\n✅ Haftalık çalışma süresi **20 saati aşmaz** (semestre içi)\n✅ Semestre tatilinde **20 saat üstü mümkün** (en fazla 26 hafta/yıl)\n\n## Werkstudent Olabilme Şartları\n\n### 1. Aktif Öğrenci Kaydı\n✅ Almanca üniversitede **immatrikulation aktif**\n✅ Lisans, master, PhD öğrencisi olabilir\n✅ Studienkolleg öğrencisi de Werkstudent olabilir\n✅ Dil kursu öğrencisi → genelde **olamaz**\n\n### 2. Vize Durumu\n✅ AB vatandaşı → otomatik haklı\n✅ Türk vatandaşı + öğrenci vizesi (D vize) → **yılda 140 iş günü** çalışma izinli (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır)\n⚠️ Vize sınırı: **140 iş günü/yıl**; ders döneminde en fazla 20 saatlik bir hafta 2,5 gün sayılabilir — bu hesap Werkstudent'ın 20 saat kuralından (sosyal sigorta) ayrıdır\n\n### 3. Yaş ve Diploma\n- **30 yaş altı + öğrenci tarifesi** sigorta yararı tam\n- 30 yaş üstü + öğrenci → Werkstudent statüsü hala mümkün ama sigorta primi farklı\n\n## Werkstudent'ın Sigorta Avantajı\n\n### Normal Çalışan (Vollzeit) Kesintileri\n- **Krankenversicherung:** %14.6 (yarısı işveren öder, sen ~%7.3 ödüyorsun)\n- **Rentenversicherung:** %18.6 (yarısı işveren)\n- **Arbeitslosenversicherung:** %2.4 (yarısı işveren)\n- **Pflegeversicherung:** %3.4\n- **Toplam:** ~%22 maaştan kesinti\n\n### Werkstudent Kesintileri\n✅ **Krankenversicherung'tan muafsın** (öğrenci sigortası devam, ek prim yok)\n✅ **Arbeitslosenversicherung'tan muafsın**\n✅ **Sadece Rentenversicherung (%9.3)** kesilir\n- Net etki: 1,000 € brutto → ~907 € net\n\n### Yıllık Tasarruf Örneği\n\n| Maaş tipi | 1,500 €/ay brutto | Net |\n| --- | --- | --- |\n| Normal çalışan (Vollzeit) | 1,500 € | ~1,140 € |\n| Werkstudent | 1,500 € | ~1,360 € |\n| **Tasarruf** | | **+220 €/ay = 2,640 €/yıl** |\n\n## Haftalık 20 Saat Sınırı\n\n### Semestre İçi\n✅ **Maksimum 20 saat/hafta** çalışma\n✅ Sınırı aşarsan **Werkstudent statüsünü kaybedersin** → normal çalışan sigortası başlar\n✅ Esnek çalışma saatleri (sabah-akşam veya hafta sonları)\n\n### Semestre Tatilinde\n✅ **20 saat üstü çalışabilirsin** (40 saat full-time)\n✅ En fazla **26 hafta/yıl** full-time çalışma (kısıtlama burada)\n✅ Tatil dönemi = Ekim-Mart (Wintersemester arası) + Temmuz-Eylül (Sommersemester arası)\n\n## Hangi Sektörlerde Werkstudent Yaygın?\n\n### IT / Bilgisayar / Mühendislik\n- SAP, Bosch, Siemens, BMW, Daimler\n- Yazılım geliştirme, veri analizi, web tasarım\n- Maaş: 1,400-2,500 €/ay\n\n### Finans / Danışmanlık\n- Deutsche Bank, Commerzbank, McKinsey, BCG\n- Asistan, analist, raporlama\n- Maaş: 1,500-2,400 €/ay\n\n### Pazarlama / İletişim\n- Reklam ajansları, e-ticaret şirketleri\n- Sosyal medya, içerik üretimi\n- Maaş: 1,000-1,800 €/ay\n\n### Akademik Asistan\n- Üniversite kütüphanesi, araştırma\n- Hilfskraft, Tutor\n- Maaş: 13,90-18 €/saat (Tarif TV-L)\n\n### Endüstri / Üretim\n- Yarı zamanlı işçi (yetenekli işler için tercih)\n- Kalite kontrol, üretim asistanı\n\n## Werkstudent vs Mini-Job\n\n| Özellik | Werkstudent | Mini-Job (603 €) |\n| --- | --- | --- |\n| Aylık limit | Sınırsız (sigortayla) | 603 € (kesinti yok) |\n| Saat sınırı | 20 saat/hafta (semestre) | Yasal haftalık saat sınırı yok (603 €, 13,90 € asgari ücretle ~10 saat/haftaya karşılık gelir) |\n| Sigorta | Öğrenci tarifesi devam | İşveren tam öder |\n| Kesinti | %9.3 (emeklilik) | 0 |\n| Vergi | Düşük (yıllık < 11K €) | 0 (genelde) |\n| Hak ediş | Profesyonel deneyim | Genel iş tecrübe |\n\n## Werkstudent Olmanın Avantajları\n\n✅ **Yüksek maaş** (Mini-Job'dan 2-3 kat)\n✅ **Kariyer odaklı iş** — staj benzeri\n✅ **CV güçlendirme** — Alman şirket deneyimi\n✅ **Mezuniyet sonrası iş garantisi** — Werkstudent'lerin %30-50'si aynı şirkette tam zamanlı\n\n## Werkstudent Olmanın Dezavantajları\n\n❌ **Zaman yönetimi zor** — 20 saat + tam zamanlı öğrenci yorucu\n❌ **Akademik performansı etkileyebilir** — özellikle teknik bölümlerde\n❌ **Yaz tatilinde Türkiye'ye dönmek zor** olabilir (iş devam)\n\n## Werkstudent İçin Belgeler\n\n✅ **Immatrikulationsbescheinigung** (üni kayıt belgesi)\n✅ **Pasaport + Vize / Aufenthaltstitel**\n✅ **Anmeldebescheinigung**\n✅ **SteuerID**\n✅ **Sigorta kanıtı**\n✅ **IBAN** (banka hesap)\n✅ **Lebenslauf** (CV — Almanca/İngilizce)\n\n## Önemli Notlar\n\n⚠️ **20 saat sınırı katı** — üniversite haftalık raporu çekiyor (bazı yerlerde)\n⚠️ **AB dışı vatandaş** için yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır) sınırı (vize şartı) — Werkstudent 20 saat kuralından ayrı\n⚠️ **Yaz tatili 20+ saat çalışma** = Werkstudent sayılır, tatil dönemi muafiyet\n⚠️ **Werkstudent maaşı çok yüksekse** (5,000+ €/ay) işveren sigortası rejimi değişebilir\n\nİlgili: [Haftalık saat sınırı](/sss/is/ogrenci-olarak-haftada-kac-saat-calisabilirim) | [Mini-Job 603 €](/sss/is/mini-job-538eur-siniri-nedir)."
    }
   }
  },
  {
   "table": "faqs",
   "slug": "werkstudent-saat-siniri-nasil-kontrol-ediliyor",
   "locale": "tr",
   "rewrite": {
    "answer_md": {
     "fingerprints": [
      "Toplam çalışma süresi sigorta + vize kombinasyonu",
      "Toplam yıllık 1,000 saat altı kalsa",
      "Yıllık 20 × 52 = 1,040 saat = 130 tam gün",
      "120 tam gün/yıl aşıldığında Ausländerbehörde uyarı"
     ],
     "new": "Werkstudent 20 saat/hafta sınırı **kişisel sorumluluk + işveren + sigorta tarafından** kontrol ediliyor. Direkt günlük takip sistemi yok ama dolaylı kanıtlar var.\n\n## Kim Kontrol Ediyor?\n\n### 1. İşveren (Birinci Sorumlu)\n✅ İş sözleşmesi 20 saat/hafta yazılı\n✅ Lohnabrechnung (bordro) saat detayı göstermek\n✅ Aylık çalışma saat takibi\n✅ İhlal durumunda **şirket cezası** (sigorta vs. ekstra ödeme)\n\n### 2. Sigorta Şirketi (GKV)\n✅ Yıllık 26 hafta üstü full-time çalışırsan **otomatik tarife değişir**\n✅ GKV sigorta primi öğrenci tarifesinden **çalışan tarifesine** geçer\n✅ 131 €/ay → ~%14.6 maaştan\n\n### 3. Vergi Dairesi (Finanzamt)\n✅ Yıllık vergi beyannamesi saat detayı içeriyor\n✅ Lohnsteuerbescheinigung saat bazında raporluyor\n✅ İhlal durumunda **vergi denetimi** mümkün\n\n### 4. Ausländerbehörde (AB Dışı Öğrenci)\n✅ Vize uzatma sırasında **çalışma saatleri kanıtı**\n✅ Lohnabrechnung birikimi kontrol\n✅ Yılda 140 iş günü (Arbeitstagekonto) sınırı ihlal varsa **vize uzatma red**\n\n### 5. Üniversite (İmmatrikulation Kontrolü)\n⚠️ Çoğu üni doğrudan saat takip etmiyor\n⚠️ Ama akademik performans düşerse soru sorabilir\n\n## Saat Sınırının Belirlenmesi\n\n### Werkstudent Resmi Tanımı\n- **20 saat/hafta** semestre içi\n- 40 saat/hafta tatil dönemi (max 26 hafta/yıl)\n- 20 saat kuralı sosyal sigorta kuralıdır; vize/oturum için ayrıca yılda 140 iş günü hesabı geçerlidir (4 saate kadar = yarım gün)\n\n### Yıllık Saat Hesabı (Werkstudent)\n- Semestre içi (40 hafta) × 20 saat = 800 saat\n- Yaz tatili (12 hafta) × 40 saat = 480 saat\n- **Toplam: 1,280 saat/yıl**\n\n### Yıllık Saat Hesabı (Normal Çalışan)\n- 52 hafta × 40 saat = 2,080 saat\n\n## Saat Sınırını Aşmanın Sonuçları\n\n### Sonuç 1: Werkstudent Statüsü Kaybı\n- 20 saat/hafta aşılırsa sigorta otomatik **çalışan tarifesine geçer**\n- Krankenversicherung %14.6 (önceden 0 ödüyordun)\n- Pflegeversicherung %3.4 (önceden 0)\n- Arbeitslosenversicherung %2.4 (önceden 0)\n- **Net etki:** maaştan %20 ek kesinti\n\n### Sonuç 2: Vize İhlali (AB Dışı)\n- 140 iş günü/yıl aşıldığında Ausländerbehörde uyarı\n- Tekrar aşılırsa **vize iptal riski**\n- Yeni vize başvurusu zor\n\n### Sonuç 3: Şirket Cezası\n- İşveren **geri ödemek zorunda** olabilir (sigorta primi)\n- Şirket bunu seninle paylaşabilir veya tek yüklenir\n- Sözleşme şartlarına bakar\n\n### Sonuç 4: Vergi Etkisi\n- Yıllık gelir artar (40 saat × yüksek saatlik)\n- Vergi sınıfı değişiklik (artan)\n- Yıllık beyanname karışıklığı\n\n## Kontrol Mekanizmaları\n\n### Birinci Kontrol: İşveren Lohnabrechnung\n- Her ay verilen bordro\n- Saat sayısı + saatlik ücret\n- Aylık toplam çalışma süresi\n\n### İkinci Kontrol: Yıllık Lohnsteuerbescheinigung\n- Şubat-Mart'ta gelen yıllık özet\n- Yıllık toplam çalışma + gelir\n- Vergi dairesi bu belgeyi alır\n\n### Üçüncü Kontrol: Sigorta Şirketi (GKV)\n- Senin işverenden bilgi alıyor (Meldungen)\n- Saat sınırını aşarsan **otomatik tarife değiştirir**\n- Geriye dönük 1 yıl prim isteme hakkı var\n\n### Dördüncü Kontrol: Ausländerbehörde (Vize Uzatma)\n- Aufenthaltstitel yenileme sırasında **iş kanıtları**\n- Werkstudent statüsü + çalışma saatleri uygunluk\n- Maaş ihlal varsa **vize uzatma red**\n\n## Çoklu İşveren Durumu\n\n### 2+ İşveren ile Çalışma\n✅ **Toplam haftalık saat 20'yi aşmamalı**\n✅ Her işverene **diğer iş(ler) bilgisi** ver (yasal)\n✅ Sigorta tek panelde birleştirilir\n\n### Yaygın Sorun: İşveren Bilgilendirilmiyor\n⚠️ \"İkinci işverenden gizlemek\" yasal değil\n⚠️ Sigorta otomatik öğrenir (DEÜV bildirimleri)\n⚠️ Vergi sınıfı VI (yüksek kesinti) otomatik\n\n## Sınırlı Aşmak için Yasal Yöntemler\n\n### Yaz Tatili Full-Time\n✅ Semestre tatili **40 saat/hafta** mümkün\n✅ Maksimum **26 hafta/yıl**\n✅ Werkstudent statüsü devam ediyor\n\n### Sözleşme Esnek Saat\n✅ Bazı işverenler **toplam aylık 80 saat** istiyor (haftalık değişken)\n✅ Bazı haftalar 30 saat, bazıları 10 saat kabul\n✅ Vize açısından saat değil **iş günü** sayılır: ders döneminde 30 saatlik haftalar 2,5 gün sayılamaz, yılda 140 iş günü aşılmamalı\n\n### Mini-Job + Werkstudent\n✅ Werkstudent (20 saat) + Mini-Job (5 saat) = 25 saat\n✅ Toplam saat sınırı dikkat\n✅ İşverenler arası bilgilendirme şart\n\n## Pratik Tavsiye\n\n### Kayıt Tutma\n✅ **Kendi günlük çalışma kaydı** tut (Excel veya app)\n✅ Lohnabrechnung'larını **PDF olarak sakla**\n✅ Yıllık özet **Lohnsteuerbescheinigung** önemli\n\n### Şüpheli Durumda\n✅ İşveren HR'a sor — yasal şartlar net mi?\n✅ Sigorta şirketine doğrudan sor (TK, AOK)\n✅ DGB (Sendika) ücretsiz danışmanlık\n\n### Yıllık Beyanname\n✅ Mart-Temmuz arası **Steuererklärung** ver\n✅ Saat sınırı içinde kaldıysan **vergi iadesi** al\n✅ Werkstudent statüsü kanıtla\n\n## Yaygın Sorunlar ve Çözümleri\n\n### Sorun 1: İşveren 20 Saat Üstü Çalıştırıyor\n⚠️ Yasal değil — Werkstudent sözleşmesi 20 saat tanır\n✅ HR'a uyarı yaz\n✅ Çalışma saatlerini düzelt\n✅ Devam ederse iş değiştir\n\n### Sorun 2: Saat Sınırını Aştın (Farkında Olmadan)\n⚠️ Yıl sonu kontrolü yap\n⚠️ Sigorta tarife değişikliği gönder\n⚠️ Geriye dönük prim ödenmesi gerek\n\n### Sorun 3: Vize Uzatmada Saat Kanıtı Yok\n⚠️ Lohnabrechnung'lar eksikse → işverenden iste\n⚠️ Yıllık Lohnsteuerbescheinigung mevcut mu?\n⚠️ Ausländerbehörde'ye **yazılı açıklama** mektubu\n\n## Yıllık Sınır Hesaplama Örneği\n\n### Senaryo: Werkstudent Tek İş\n- Semestre içi (Ekim-Şubat + Nisan-Temmuz = 32 hafta): 20 saat/hafta → 32 × 2,5 = 80 iş günü\n- Yaz tatili (Ağustos-Eylül = 8 hafta): 40 saat/hafta (5 iş günü) × 8 = 40 iş günü\n- Sömestre tatili (Mart = 4 hafta): 30 saat/hafta (5 iş günü) × 4 = 20 iş günü\n- **Toplam yıllık: 140 iş günü**\n- ⚠️ AB dışı 140 iş günü sınırı **tamamen doluyor** — tek ek gün aşım demek\n- ✅ Çözüm: Yaz tatilinde haftada 5 yerine 4 gün çalış → 8 iş günü tasarruf (toplam 132); 4 saate kadar vardiyalar yarım gün sayılır\n\n### Senaryo: Werkstudent + Mini-Job\n- Werkstudent 15 saat/hafta + Mini-Job 5 saat/hafta = 20 saat/hafta\n- Her hafta toplam en fazla 20 saat → haftada 2,5 iş günü → 52 × 2,5 = 130 iş günü\n- ⚠️ Yine 140 iş günü sınırına yakın\n\n## Önemli Notlar\n\n⚠️ **20 saat sınırı kişisel sorumluluğun** — kayıt tut\n⚠️ **Yaz tatili 40 saat hala Werkstudent** (sigorta avantajı)\n⚠️ **140 iş günü vize sınırı AB dışı için** geçerli (Türk vatandaşı) — Werkstudent 20 saat kuralından ayrı\n⚠️ **Sigorta + vize bağımsız kontrol** ediyor — ikisini de gözden geçir\n\nİlgili: [Werkstudent](/sss/is/werkstudent-nedir-kim-basvurabilir) | [Haftalık saat sınırı](/sss/is/ogrenci-olarak-haftada-kac-saat-calisabilirim)."
    }
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
            throw new RuntimeException('Content Truth Batch 2 (B — SSS cevapları): ön kontrol başarısız, hiçbir şey yazılmadı. '.implode(' | ', array_slice($problems, 0, 40)));
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

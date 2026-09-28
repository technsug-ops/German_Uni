<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Content Truth Sprint — Batch 2C: TR iş kuralı kaynaklarına (student-work-permit, student-matters, part-time-jobs, 120-gün SSS)
 * Arbeitstagekonto nüansı (≤4 saat = yarım gün; ders döneminde ≤20 saatlik hafta = 2,5 gün; ders dönemi dışında her hafta
 * 2,5 gün; öğrenci lehine sayım) + Werkstudent 20 saat = sosyal sigorta kuralı ayrımı; DE Minijob SSS başlığı nötr
 * (slug değişmez, Batch 4 adayı); DE/EN Minijob SSS gövdesi (render edilmeyen eski 538 € metni) güncel 603 € metniyle;
 * TR/EN/DE Minijob SSS'sinde sabit toplam-saat örnekleri (20 + 12 = 32 vb.) kaldırıldı — Minijob'da yasal haftalık saat
 * sınırı yok, ~10 saat yalnız 13,90 € ile yaklaşık hesap; kombinasyon Werkstudent sigorta statüsünü etkileyebilir, § 16b ayrı.
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
   "slug": "student-work-permit-in-germany-2026-20-hour-rule-and-types",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Almanya'da uluslararası öğrencilerin çalışma izni, genellikle oturum izinleriyle (Aufenthaltstitel) birlikte belirlenir. En bilinen ve en çok merak edilen kural ise haftalık 20 saat kuralıdır. Bu kural, dönem içinde (üniversite derslerinin devam ettiği zamanlar) öğrencilerin haftada en fazla 20 saat çalışabileceğini belirtir. Bu sınırın amacı, öğrencilerin asıl odak noktalarının dersleri ve eğitimleri olmasını sağlamaktır.",
      "replace": "Almanya'da uluslararası öğrencilerin çalışma izni, genellikle oturum izinleriyle (Aufenthaltstitel) birlikte belirlenir. En çok merak edilen konu haftalık **20 saat kuralı**dır; ancak bu kural çoğu zaman oturum izni sınırıyla karıştırılır. Oturum hukukunda (§ 16b AufenthG) 01.03.2024'ten beri sınır haftalık bir saat tavanı değil, yılda **140 iş günlük** bir hesaptır (Arbeitstagekonto). Haftalık 20 saat ise ders döneminde **Werkstudent statüsünün sosyal sigorta koşuludur**; ayrıca ders döneminde en fazla 20 saat çalışılan bir hafta, 140 günlük hesapta 2,5 iş günü sayılabilir."
     },
     {
      "line": "Yıllık Çalışma Sınırı: Haftalık 20 saat kuralı, dönem içindeki çalışmayı kapsar. Yıl boyunca ise öğrencilerin toplamda 140 iş günü (Arbeitstagekonto; 2024'te 120'den 140'a çıkarıldı) çalışma hakkı bulunur. 4 saate kadar çalışılan gün yarım gün sayılır. Bu, öğrencilerin dönem tatillerinde (semesterferien) haftada 20 saati aşan sürelerde tam zamanlı (Vollzeit) çalışabileceği anlamına gelir. Örneğin, yaz tatilinde 2 ay boyunca tam zamanlı çalışarak bu yıllık izninizi kullanabilirsiniz.",
      "replace": "**Yıllık Çalışma Sınırı:** Oturum hukukundaki sınır, yılda **140 iş günlük** hesaptır (Arbeitstagekonto; 2024'te 120'den 140'a çıkarıldı). 4 saate kadar çalışılan gün yarım gün sayılır. Alternatif olarak haftalık sayım yapılabilir: ders döneminde en fazla 20 saat çalışılan bir hafta 2,5 iş günü, ders dönemi dışında ise her hafta 2,5 iş günü sayılır; her hafta için sizin lehinize olan hesap uygulanır. Dönem tatillerinde (Semesterferien) tam zamanlı (Vollzeit) çalışabilirsiniz; bu haftalar da yıllık hesaba sayılır. Ayrıntılı sayım örnekleri: [140 günlük çalışma hesabı](/tr/blog/internship-in-germany-with-b1-b2-german)."
     },
     {
      "line": "Dönem İçi ve Tatil Dönemi: Dönem içinde haftalık 20 saati aşmak yasal değildir ve oturum izninizin iptaline kadar gidebilecek ciddi sorunlara yol açabilir. Tatil dönemlerinde ise bu sınır kalkar ve yıllık izin dahilinde daha fazla çalışabilirsiniz.",
      "replace": "**Dönem İçi ve Tatil Dönemi:** Oturum hukukunda ders dönemi için ayrı bir haftalık saat tavanı yoktur; belirleyici olan 140 iş günlük hesaptır. Ders döneminde haftada 20 saati aşarsanız o hafta 2,5 günlük haftalık sayım kullanılamaz; çalıştığınız her gün tam veya yarım gün olarak hesaptan düşer. Ayrıca Werkstudent iseniz öğrenci sosyal sigorta avantajınızı kaybedebilirsiniz. 140 iş gününü aşmak ise oturum izninizde ciddi sorunlara yol açabilir."
     },
     {
      "line": "20 Saat Kuralı ile İlişkisi: Minijob, haftalık 20 saat kuralına tabidir. Yani, Minijob yapsanız bile, dönem içinde toplam çalışma süreniz haftalık 20 saati geçmemelidir. Ancak, birden fazla Minijob yapıyorsanız, tüm Minijob gelirlerinizin toplamı aylık üst sınırı (2026'da 603 Euro) geçmemeli ve toplam çalışma süreniz haftalık 20 saati aşmamalıdır.",
      "replace": "**20 Saat Kuralı ile İlişkisi:** Minijob için yasal bir haftalık saat tavanı yoktur; belirleyici olan aylık kazanç sınırıdır (2026'da 603 Euro; birden fazla Minijob yapıyorsanız toplam gelir bu sınırı geçmemelidir). Oturum hukuku açısından ise Minijob dahil tüm işlerinizdeki çalışma günleri yılda 140 iş günlük hesabınıza sayılır. Minijob'u bir Werkstudent işiyle birlikte yapıyorsanız, Werkstudent sosyal sigorta avantajı için ders dönemindeki toplam haftalık çalışma sürenize dikkat edin."
     },
     {
      "line": "20 Saat Kuralı: Werkstudent statüsünde de dönem içinde haftalık 20 saat kuralına uymak zorunludur. Tatil dönemlerinde ise bu sınır kalkar ve tam zamanlı çalışabilirsiniz.",
      "replace": "**20 Saat Kuralı:** Werkstudent olarak ders döneminde haftada en fazla 20 saat çalışmanız, öğrenci sosyal sigorta avantajını korumanın koşuludur; bu bir oturum izni kuralı değildir. Tatil dönemlerinde bu sınır uygulanmaz ve tam zamanlı çalışabilirsiniz; bu haftalar yine 140 iş günlük hesaba sayılır."
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
      "line": "Almanya'da uluslararası öğrencilerin çalışma izni, genellikle oturum izinleriyle (Aufenthaltstitel) birlikte belirlenir. En bilinen ve en çok merak edilen kural ise haftalık 20 saat kuralıdır. Bu kural, dönem içinde (üniversite derslerinin devam ettiği zamanlar) öğrencilerin haftada en fazla 20 saat çalışabileceğini belirtir. Bu sınırın amacı, öğrencilerin asıl odak noktalarının dersleri ve eğitimleri olmasını sağlamaktır.",
      "replace": "Almanya'da uluslararası öğrencilerin çalışma izni, genellikle oturum izinleriyle (Aufenthaltstitel) birlikte belirlenir. En çok merak edilen konu haftalık **20 saat kuralı**dır; ancak bu kural çoğu zaman oturum izni sınırıyla karıştırılır. Oturum hukukunda (§ 16b AufenthG) 01.03.2024'ten beri sınır haftalık bir saat tavanı değil, yılda **140 iş günlük** bir hesaptır (Arbeitstagekonto). Haftalık 20 saat ise ders döneminde **Werkstudent statüsünün sosyal sigorta koşuludur**; ayrıca ders döneminde en fazla 20 saat çalışılan bir hafta, 140 günlük hesapta 2,5 iş günü sayılabilir."
     },
     {
      "line": "Yıllık Çalışma Sınırı: Haftalık 20 saat kuralı, dönem içindeki çalışmayı kapsar. Yıl boyunca ise öğrencilerin toplamda 140 iş günü (Arbeitstagekonto; 2024'te 120'den 140'a çıkarıldı) çalışma hakkı bulunur. 4 saate kadar çalışılan gün yarım gün sayılır. Bu, öğrencilerin dönem tatillerinde (semesterferien) haftada 20 saati aşan sürelerde tam zamanlı (Vollzeit) çalışabileceği anlamına gelir. Örneğin, yaz tatilinde 2 ay boyunca tam zamanlı çalışarak bu yıllık izninizi kullanabilirsiniz.",
      "replace": "**Yıllık Çalışma Sınırı:** Oturum hukukundaki sınır, yılda **140 iş günlük** hesaptır (Arbeitstagekonto; 2024'te 120'den 140'a çıkarıldı). 4 saate kadar çalışılan gün yarım gün sayılır. Alternatif olarak haftalık sayım yapılabilir: ders döneminde en fazla 20 saat çalışılan bir hafta 2,5 iş günü, ders dönemi dışında ise her hafta 2,5 iş günü sayılır; her hafta için sizin lehinize olan hesap uygulanır. Dönem tatillerinde (Semesterferien) tam zamanlı (Vollzeit) çalışabilirsiniz; bu haftalar da yıllık hesaba sayılır. Ayrıntılı sayım örnekleri: [140 günlük çalışma hesabı](/tr/blog/internship-in-germany-with-b1-b2-german)."
     },
     {
      "line": "Dönem İçi ve Tatil Dönemi: Dönem içinde haftalık 20 saati aşmak yasal değildir ve oturum izninizin iptaline kadar gidebilecek ciddi sorunlara yol açabilir. Tatil dönemlerinde ise bu sınır kalkar ve yıllık izin dahilinde daha fazla çalışabilirsiniz.",
      "replace": "**Dönem İçi ve Tatil Dönemi:** Oturum hukukunda ders dönemi için ayrı bir haftalık saat tavanı yoktur; belirleyici olan 140 iş günlük hesaptır. Ders döneminde haftada 20 saati aşarsanız o hafta 2,5 günlük haftalık sayım kullanılamaz; çalıştığınız her gün tam veya yarım gün olarak hesaptan düşer. Ayrıca Werkstudent iseniz öğrenci sosyal sigorta avantajınızı kaybedebilirsiniz. 140 iş gününü aşmak ise oturum izninizde ciddi sorunlara yol açabilir."
     },
     {
      "line": "20 Saat Kuralı ile İlişkisi: Minijob, haftalık 20 saat kuralına tabidir. Yani, Minijob yapsanız bile, dönem içinde toplam çalışma süreniz haftalık 20 saati geçmemelidir. Ancak, birden fazla Minijob yapıyorsanız, tüm Minijob gelirlerinizin toplamı aylık üst sınırı (2026'da 603 Euro) geçmemeli ve toplam çalışma süreniz haftalık 20 saati aşmamalıdır.",
      "replace": "**20 Saat Kuralı ile İlişkisi:** Minijob için yasal bir haftalık saat tavanı yoktur; belirleyici olan aylık kazanç sınırıdır (2026'da 603 Euro; birden fazla Minijob yapıyorsanız toplam gelir bu sınırı geçmemelidir). Oturum hukuku açısından ise Minijob dahil tüm işlerinizdeki çalışma günleri yılda 140 iş günlük hesabınıza sayılır. Minijob'u bir Werkstudent işiyle birlikte yapıyorsanız, Werkstudent sosyal sigorta avantajı için ders dönemindeki toplam haftalık çalışma sürenize dikkat edin."
     },
     {
      "line": "20 Saat Kuralı: Werkstudent statüsünde de dönem içinde haftalık 20 saat kuralına uymak zorunludur. Tatil dönemlerinde ise bu sınır kalkar ve tam zamanlı çalışabilirsiniz.",
      "replace": "**20 Saat Kuralı:** Werkstudent olarak ders döneminde haftada en fazla 20 saat çalışmanız, öğrenci sosyal sigorta avantajını korumanın koşuludur; bu bir oturum izni kuralı değildir. Tatil dönemlerinde bu sınır uygulanmaz ve tam zamanlı çalışabilirsiniz; bu haftalar yine 140 iş günlük hesaba sayılır."
     },
     {
      "line": "Haftalık 20 Saat Kuralı ve Sigorta: Haftalık 20 saat kuralı, sadece çalışma izniyle ilgili değil, aynı zamanda öğrenci sağlık sigortası statünüzü korumak için de önemlidir. Dönem içinde haftada 20 saati aşan düzenli bir işte çalışmanız, sigorta şirketiniz tarafından \"tam zamanlı çalışan\" olarak değerlendirilmenize ve öğrenci sağlık sigortası avantajlarınızı kaybetmenize neden olabilir. Bu durum, daha yüksek primler ödemeniz gerektiği anlamına gelir. Tatil dönemlerinde haftalık 20 saati aşan çalışmalar, öğrenci statünüzü etkilemez, çünkü bu \"geçici\" bir durum olarak kabul edilir.",
      "subs": [
       {
        "old": "Haftalık 20 saat kuralı, sadece çalışma izniyle ilgili değil, aynı zamanda öğrenci sağlık sigortası statünüzü korumak için de önemlidir.",
        "new": "Haftalık 20 saat kuralı esasen öğrenci sosyal sigorta (sağlık sigortası) statünüzle ilgilidir; oturum izni açısından ayrıca yılda 140 iş günlük hesap geçerlidir."
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
      "line": "140 tam gün veya 280 yarım gün / yıl.",
      "replace": "**140 iş günü / yıl** (Arbeitstagekonto): 4 saate kadar çalışılan gün yarım gün sayılır — \"140 tam veya 280 yarım gün\" ifadesi buradan gelir."
     },
     {
      "line": "Alternatif olarak haftada 20 saate kadar.",
      "replace": "Alternatif haftalık sayım: ders döneminde **en fazla 20 saat** çalışılan bir hafta 2,5 iş günü, ders dönemi dışında her hafta 2,5 iş günü sayılır; her hafta için senin lehine olan hesap uygulanır ([ayrıntılar](/tr/blog/internship-in-germany-with-b1-b2-german))."
     },
     {
      "line": "Ders döneminde haftada 20 saati aşmamak önemli — aşarsan \"öğrenci\" statüsünün sigorta avantajını kaybedersin. Sömestr tatilinde tam zamanlı çalışabilirsin, ama bu günler yıllık 140/280 hesabına eklenir.",
      "replace": "Ders döneminde haftada **20 saati aşmamak** önemli — aşarsan \"öğrenci\" statüsünün sigorta avantajını kaybedersin (bu Werkstudent'ın sosyal sigorta kuralıdır; oturum hukukundaki 140 iş günü hesabından ayrıdır). **Sömestr tatilinde tam zamanlı** çalışabilirsin, ama bu haftalar da yıllık 140 iş günü hesabına eklenir."
     },
     {
      "line": "140 gün / 20 saat sınırını aşmak küçük bir idari mesele değildir; oturum izni incelemesine ve ağır durumlarda mezuniyet sonrası çalışma izni başvurunun reddine yol açabilir. Saatlerini kayıt altında tut.",
      "subs": [
       {
        "old": "140 gün / 20 saat sınırını aşmak",
        "new": "140 iş günlük hesabı aşmak"
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
   "md": {
    "answer_md": [
     {
      "line": "Öğrenci ikamet izniyle Almanya'da çalışma hakkınız genellikle yılda 140 iş günü (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır) ile sınırlıdır. Dönem içinde haftada 20 saati aşan çalışma tam gün olarak sayılabilir ve bu kotadan düşer. Ayrıca üniversitedeki asistanlık (HiWi) gibi bazı işler farklı kurallara tabi olabilir; kendi durumunuz için yabancılar dairesiyle (Ausländerbehörde) teyit etmeniz önerilir.",
      "subs": [
       {
        "old": "Dönem içinde haftada 20 saati aşan çalışma tam gün olarak sayılabilir ve bu kotadan düşer.",
        "new": "Ders döneminde en fazla 20 saat çalışılan bir hafta alternatif olarak 2,5 iş günü sayılabilir (ders dönemi dışında her hafta 2,5 iş günü); her hafta için sizin lehinize olan hesap uygulanır. Ders döneminde haftada 20 saati aşarsanız bu haftalık sayım kullanılamaz ve çalıştığınız her gün tam veya yarım gün olarak hesaptan düşer. Werkstudent'ın haftalık 20 saat kuralı ise bundan ayrı bir sosyal sigorta kuralıdır."
       }
      ]
     }
    ]
   }
  },
  {
   "table": "faqs",
   "slug": "mini-job-538eur-siniri-nedir-de",
   "locale": "de",
   "fields": {
    "question": {
     "old": "Was ist die 538-Euro-Grenze beim Minijob?",
     "new": "Wie hoch ist die Minijob-Grenze 2026?"
    }
   },
   "rewrite": {
    "answer_md": {
     "fingerprints": [
      "Anfang 2024 wurde das Limit von 520 € auf 538 € erhöht",
      "Monatliche Grenze von 538 €",
      "Die jährliche Grenze von 120 vollen Tagen"
     ],
     "new": "Ein **Minijob** ist in Deutschland eine Beschäftigung bis zu einer monatlichen Grenze von **603 €** (2026), bei der für dich **keine Sozialversicherungsbeiträge und keine Steuern** anfallen. Die Grenze ist von 520 € (2023) → 538 € (2024) → 556 € (2025) → 603 € (2026) gestiegen.\n\n## Was ist ein Minijob?\n\n✅ **Geringfügige Beschäftigung** — geringfügig entlohnte Arbeit\n✅ Monatlich **maximal 603 €** (jährlich 7.236 €)\n✅ Keine Sozialversicherungsbeiträge (du zahlst 0)\n✅ Keine Steuern (wenn jährlich < 7.236 €)\n✅ Der Arbeitgeber zahlt **pauschal 30 %** (Sozialversicherung + Steuern + Rente)\n\n## Arten von Minijobs\n\n### 1. 603-€-Minijob (Stundenjob)\n✅ Monatliche Grenze von 603 € (2026)\n✅ Arbeitnehmer: 0 Zahlung\n✅ Arbeitgeber: 30 % Pauschalbeitrag\n\n### 2. Kurzfristiger Minijob\n✅ **Maximal 3 Monate** oder **70 Arbeitstage** (pro Jahr)\n✅ KEINE monatliche Grenze (603 € dürfen überschritten werden)\n✅ Meist Saisonarbeit (z. B. Ferienjobs im Sommer)\n✅ Keine Sozialversicherungsbeiträge\n\n## Merkmale des 603-€-Minijobs\n\n### Vorteile\n✅ **Brutto = Netto** (keine Abzüge)\n✅ Sehr flexibel (keine gesetzliche Wochenstunden-Höchstgrenze)\n✅ Kombination aus **2 Minijobs + 1 Hauptjob** möglich\n✅ **Steuerfrei** (jährlich < 7.236 €)\n\n### Nachteile\n❌ Niedrige monatliche Grenze (603 €)\n❌ Begrenzte Rentenansprüche\n❌ Günstigere Alternative zum Werkstudentenstatus\n\n## Welche Jobs sind als Minijob verbreitet?\n\n### Gastronomie / Restaurant\n- Kellner, Küchenhilfe, Kassierer\n- 13,90-15 € pro Stunde (Mindestlohn 2026: 13,90 €)\n- 30-40 Stunden/Monat = 417-600 €\n\n### Lieferando / Wolt / Bolt Food\n- Kurier / Fahrer\n- 12-14 € pro Stunde (als Arbeitnehmer mindestens 13,90 € Mindestlohn; für selbstständige Kuriere gilt kein Mindestlohn)\n- ~10 Stunden/Woche ≈ 600 €/Monat\n\n### Geschäft / Supermarkt\n- Kasse, Regale einräumen\n- 13,90-15 € pro Stunde\n- 8-10 Stunden/Woche\n\n### Fitnessstudio / Hotel\n- Rezeption, Reinigung, Fitnesstrainer\n- 13,90-16 € pro Stunde\n\n### Sprachassistenz / Tutor\n- Tutorium an der Uni, Nachhilfe\n- 15-25 € pro Stunde (hochqualifiziert)\n\n### Online-Jobs\n- Übersetzen, Content-Erstellung, Social Media\n- 15-30 € pro Stunde\n\n## Minijob vs. Werkstudent\n\n| Merkmal | Minijob | Werkstudent |\n| --- | --- | --- |\n| Monatliche Grenze | 603 € | Unbegrenzt |\n| Stundenlimit | Keine gesetzliche Wochenstunden-Höchstgrenze (603 € entsprechen beim Mindestlohn von 13,90 € rechnerisch etwa 10 Stunden pro Woche) | 20 Stunden/Woche (Vorlesungszeit, Regel der Sozialversicherung) |\n| Versicherung | 0 | 131 € Studententarif |\n| Steuern | 0 (meist) | 9,3 % Rentenversicherung |\n| Nach dem Abschluss | Endet | Wird zur Vollzeitstelle |\n| Auswirkung aufs Studium | Gering | Mittel |\n\n## Was passt in welchem Szenario?\n\n### Wähle den Minijob, wenn:\n- du **wenige Stunden** arbeiten möchtest (~10 Stunden/Woche)\n- du **kein Steuer-/Versicherungs-Durcheinander** willst\n- dir der **Studienfokus** wichtig ist (wenige Stunden)\n- du auch in den Semesterferien Zeit haben möchtest\n\n### Wähle den Werkstudentenjob, wenn:\n- du ein **höheres Gehalt** möchtest (1.000+ €/Monat)\n- **Berufserfahrung + Lebenslauf** Priorität haben\n- du **20 Stunden/Woche** neben deinem Studium bewältigen kannst\n- du eine **Jobperspektive nach dem Abschluss** suchst\n\n## Kombination Minijob + Werkstudent\n\n✅ Minijob + Werkstudent **gleichzeitig** möglich\n✅ Für den Minijob gibt es keine gesetzliche Wochenstunden-Höchstgrenze; entscheidend ist, dass der regelmäßige Monatsverdienst die Minijobgrenze nicht überschreitet. Beim gesetzlichen Mindestlohn von 13,90 € entspricht die 603-€-Grenze 2026 rechnerisch ungefähr 10 Stunden pro Woche. Das ist eine ungefähre Rechnung, keine gesetzliche Wochenstunden-Höchstgrenze.\n⚠️ Wenn du einen Minijob neben einem anderen Studentenjob ausübst, kann die gesamte Arbeitszeit deinen Sozialversicherungsstatus als Werkstudent beeinflussen.\n⚠️ Das Arbeitstagekonto nach § 16b AufenthG (140 Arbeitstage im Jahr) wird davon getrennt betrachtet; für Nicht-EU-Studierende zählen die Arbeitstage aus beiden Jobs zusammen.\n\n## Steuern\n\n### Minijob-Pauschalsteuer\n- Der Arbeitgeber zahlt **2 % Pauschalsteuer** (nicht du)\n- Bis 7.236 € im Jahr **steuerfrei**\n\n### Minijob + sonstige Einkünfte\n- Bis zu einem Gesamteinkommen von **11.604 €** im Jahr (Grundfreibetrag) steuerfrei\n- Wird das überschritten, fallen Steuern an (betrifft das zusätzliche Einkommen)\n\n## Versicherung\n\n### Sozialversicherungsbeiträge im Minijob\n- **Pauschalbeitrag des Arbeitgebers:** 30 % (Versicherung + Steuern + Rente)\n- **Du zahlst 0 Beiträge**\n- Die Krankenversicherung ist **im Pauschalbeitrag des Arbeitgebers enthalten** (die studentische Versicherung ist davon getrennt)\n\n### Studentische Krankenversicherung\n✅ Die studentische Versicherung von **131 €/Monat** zahlst du selbst (unabhängig vom Minijob)\n✅ Der Minijob beeinflusst deinen Beitrag nicht (du zahlst dort keine Beiträge)\n✅ Krankenversichert bist du nicht über den Arbeitgeber, sondern über deine eigene GKV\n\n## Praktische Tipps\n\n### Für neue Studierende\n1. Starte mit einem **ersten Minijob** — wenig Druck + Geld verdienen\n2. Gewöhne dich an die deutsche Arbeitskultur\n3. Probiere im nächsten Semester einen **Werkstudentenjob** (professionell)\n\n### Hohe Studienbelastung (Ingenieurwesen, Medizin)\n✅ **Minijob** ist sicherer — deine Studienleistung bleibt geschützt\n\n### Fokus auf die berufliche Karriere\n✅ **Werkstudentenjob** empfohlen — Lebenslauf + Geld + Erfahrung\n\n## Wichtige Hinweise\n\n⚠️ **Wenn du die 603-€-Grenze überschreitest, wirst du nicht automatisch Werkstudent** — dafür ist eine Vertragsänderung mit dem Arbeitgeber nötig\n⚠️ **2 Minijobs dürfen zusammen 603 € nicht überschreiten** (auch bei verschiedenen Arbeitgebern gilt die Gesamtgrenze)\n⚠️ **Minijob + Werkstudent nicht beim selben Arbeitgeber** — es müssen verschiedene Arbeitgeber sein\n\n## Visum und Arbeitserlaubnis\n\n### EU-Bürger\n✅ Minijob unbegrenzt\n✅ Kein Stundenlimit\n\n### Türkische Staatsangehörige mit Studentenvisum\n⚠️ Behalte die Grenze von **140 Arbeitstagen pro Jahr** genau im Blick (Arbeitstagekonto; ein Tag mit bis zu 4 Stunden Arbeit zählt als halber Tag)\n⚠️ Auch deine Minijob-Arbeitstage zählen zum Konto von 140 Arbeitstagen — behalte die Gesamtzahl im Blick\n\nMehr zu den Arbeitsregeln (140-Tage-Konto): [Praktikum mit B1/B2 Deutsch](/de/blog/internship-in-germany-with-b1-b2-german-de)"
    }
   }
  },
  {
   "table": "faqs",
   "slug": "mini-job-538eur-siniri-nedir",
   "locale": "tr",
   "rewrite": {
    "answer_md": {
     "fingerprints": [
      "20 + 12 = 32 saat/hafta",
      "20 saat Werkstudent + 5 saat Mini-Job daha güvenli",
      "Gelir Hesabı (Kombinasyon)",
      "Mini-Job 5 saat/hafta = düşük gün sayısı"
     ],
     "new": "**Mini-Job**, Almanya'da aylık **603 €** (2026) sınırına kadar olan **sigorta primi sıfır + vergi sıfır** iş türü. Sınır 520 € (2023) → 538 € (2024) → 556 € (2025) → 603 € (2026) olarak yükseldi.\n\n## Mini-Job Tanımı\n\n✅ **Geringfügige Beschäftigung** — düşük ücretli iş\n✅ Aylık **maksimum 603 €** (yıllık 7,236 €)\n✅ Sigorta primi sıfır (sen 0 ödüyorsun)\n✅ Vergi sıfır (yıllık < 7,236 € ise)\n✅ İşveren **%30 toplu prim** öder (sigorta + vergi + emeklilik)\n\n## Mini-Job Türleri\n\n### 1. 603 € Mini-Job (Saatlik İş)\n✅ Aylık 603 € sınırı (2026)\n✅ Çalışan: 0 ödeme\n✅ İşveren: %30 toplu prim\n\n### 2. Kısa Süreli Mini-Job\n✅ **Maksimum 3 ay** veya **70 iş günü** (yıllık)\n✅ Aylık limit YOK (603 € aşılabilir)\n✅ Genelde mevsimsel iş (yaz tatili işleri)\n✅ Sigorta primi sıfır\n\n## 603 € Mini-Job Özellikleri\n\n### Avantajları\n✅ **Brutto = Net** (kesinti yok)\n✅ Çok esnek (yasal haftalık saat sınırı yok)\n✅ **2 Mini-Job + 1 ana iş** kombinasyonu mümkün\n✅ **Vergisiz** (yıllık < 7,236 €)\n\n### Dezavantajları\n❌ Aylık limit düşük (603 €)\n❌ Emeklilik birikimi sınırlı\n❌ Werkstudent statüsünden ucuz çıkış\n\n## Hangi İşler Mini-Job Olarak Yaygın?\n\n### Catering / Restoran\n- Garson, mutfak yardımcısı, kasiyer\n- Saatlik 13,90-15 € (2026 asgari ücret: 13,90 €)\n- Aylık 30-40 saat = 417-600 €\n\n### Lieferando / Wolt / Bolt Food\n- Kurye / sürücü\n- Saatlik 12-14 € (çalışan statüsünde en az 13,90 € asgari ücret; serbest kuryelikte asgari ücret uygulanmaz)\n- ~10 saat/hafta ≈ 600 €/ay\n\n### Mağaza / Süpermarket\n- Kasiyer, raf düzenleme\n- Saatlik 13,90-15 €\n- 8-10 saat/hafta\n\n### Spor Salonu / Otel\n- Resepsiyon, temizlik, fitness eğitmen\n- Saatlik 13,90-16 €\n\n### Dil Asistanı / Tutor\n- Üniversite tutorluğu, özel ders\n- Saatlik 15-25 € (yüksek nitelikli)\n\n### Online İşler\n- Çevirmen, içerik yazma, sosyal medya\n- Saatlik 15-30 €\n\n## Mini-Job vs Werkstudent\n\n| Özellik | Mini-Job | Werkstudent |\n| --- | --- | --- |\n| Aylık limit | 603 € | Sınırsız |\n| Saat sınırı | Yasal haftalık saat sınırı yok (603 €, 13,90 € asgari ücretle ~10 saat/haftaya karşılık gelir) | 20 saat/hafta (ders dönemi, sosyal sigorta kuralı) |\n| Sigorta | 0 | 131 € öğrenci tarifesi |\n| Vergi | 0 (genelde) | %9.3 emeklilik |\n| Mezuniyet sonrası | Devam etmez | Tam zamanlıya dönüşür |\n| Akademik etki | Düşük | Orta |\n\n## Hangi Senaryoda Hangisini Seç?\n\n### Mini-Job Seç:\n- **Düşük saat** çalışmak istiyorsan (~10 saat/hafta)\n- **Vergi/sigorta karışıklığı** istemiyorsan\n- **Akademik fokus** önemli (saat az)\n- Yaz tatilinde de zaman ayırmak istiyorsun\n\n### Werkstudent Seç:\n- **Yüksek maaş** istiyorsan (1,000+ €/ay)\n- **Profesyonel deneyim + CV** öncelikli\n- **20 saat/hafta** yönetebilir akademik yük\n- **Mezuniyet sonrası iş** garantisi istiyor\n\n## Mini-Job + Werkstudent Kombinasyonu\n\n✅ **Aynı anda** Mini-Job + Werkstudent mümkün\n✅ Minijob için yasal bir haftalık saat sınırı yoktur; belirleyici olan düzenli aylık kazancın Minijob sınırını aşmamasıdır. 2026'da 603 € sınırı, 13,90 € asgari ücretle yaklaşık 10 saat/haftaya karşılık gelir. Bu yaklaşık bir hesaplamadır, yasal bir haftalık üst sınır değildir.\n⚠️ Bir Minijob başka bir öğrenci işiyle birlikte yapılıyorsa, toplam çalışma süresi Werkstudent sosyal sigorta statüsünü etkileyebilir.\n⚠️ § 16b kapsamındaki Arbeitstagekonto (yılda 140 iş günü) ise ayrıca değerlendirilir; AB dışı öğrenciler için iki işteki çalışma günleri bu hesaba birlikte sayılır.\n\n## Vergi Durumu\n\n### Mini-Job Pauschalsteuer (Genel Vergi)\n- İşveren **%2 toplu vergi** öder (sen değil)\n- Yıllık 7,236 €'ya kadar **vergi sıfır**\n\n### Mini-Job + Diğer Gelir\n- Yıllık toplam **11,604 €** (Grundfreibetrag) altı vergi sıfır\n- Aşılırsa vergi başlar (ek geliri etkiler)\n\n## Sigorta Durumu\n\n### Mini-Job Sigorta Primi\n- **İşveren toplu primi:** %30 (sigorta + vergi + emeklilik)\n- **Sen 0 prim ödüyorsun**\n- Sağlık sigortan **işveren tarafından dahil** (öğrenci sigortası farklı)\n\n### Öğrenci Sigortası Durumu\n✅ Öğrenci sigortası **131 €/ay** sen ödüyorsun (Mini-Job dışı)\n✅ Mini-Job sigorta primi etkilemiyor (sen prim ödemiyorsun)\n✅ Sağlık sigortası işveren değil, kendi GKV'n üzerinden\n\n## Pratik Tavsiye\n\n### Yeni Öğrenci için\n1. **İlk Mini-Job** ile başla — düşük baskı + para kazanma\n2. Almanya kültürüne alış\n3. Sonraki dönem **Werkstudent** dene (profesyonel)\n\n### Yüksek Akademik Yük (Mühendislik, Tıp)\n✅ **Mini-Job** daha güvenli — akademik performans korunur\n\n### Profesyonel Kariyer Hedefli\n✅ **Werkstudent** öner — CV + para + deneyim\n\n## Önemli Notlar\n\n⚠️ **603 € sınırını aşarsan otomatik Werkstudent statüsüne geçilmez** — işverenle değişiklik gerek\n⚠️ **2 Mini-Job toplam 603 €'yu aşamaz** (her ayrı işveren, toplam sınır)\n⚠️ **Mini-Job + Werkstudent aynı işveren olmaz** — ayrı işverenler\n\n## Vize ve Çalışma İzni\n\n### AB Vatandaşı\n✅ Mini-Job sınırsız\n✅ Saat sınırı yok\n\n### Türk Vatandaşı + Öğrenci Vizesi\n⚠️ **Yılda 140 iş günü** sınırını (Arbeitstagekonto; 4 saate kadar çalışılan gün yarım gün sayılır) dikkatli takip et\n⚠️ Mini-Job'da çalıştığın günler de 140 iş günü hesabına sayılır — toplam gün sayını takip et\n\nİlgili: [Werkstudent statüsü](/sss/is/werkstudent-nedir-kim-basvurabilir) | [Haftalık saat sınırı](/sss/is/ogrenci-olarak-haftada-kac-saat-calisabilirim)."
    }
   }
  },
  {
   "table": "faqs",
   "slug": "mini-job-538eur-siniri-nedir-en",
   "locale": "en",
   "rewrite": {
    "answer_md": {
     "fingerprints": [
      "It was raised from 520 € to 538 € at the beginning of 2024",
      "Monthly limit of 538 €",
      "Carefully follow the 120 full days/year limit"
     ],
     "new": "A **Mini-Job** is a type of employment in Germany up to a monthly limit of **€603** (2026) with **zero social security contributions + zero tax** for you. The limit has risen from €520 (2023) → €538 (2024) → €556 (2025) → €603 (2026).\n\n## Mini-Job Definition\n\n✅ **Geringfügige Beschäftigung** — low-paid employment\n✅ **Maximum €603** per month (€7,236 annually)\n✅ Zero social security contributions (you pay 0)\n✅ Zero tax (if annual income < €7,236)\n✅ Employer pays a **30% lump-sum contribution** (social security + tax + pension)\n\n## Types of Mini-Job\n\n### 1. €603 Mini-Job (Hourly Work)\n✅ Monthly limit of €603 (2026)\n✅ Employee: pays 0\n✅ Employer: 30% lump-sum contribution\n\n### 2. Short-Term Mini-Job\n✅ **Maximum 3 months** or **70 working days** (per year)\n✅ NO monthly limit (€603 can be exceeded)\n✅ Usually seasonal work (summer holiday jobs)\n✅ Zero social security contributions\n\n## Features of the €603 Mini-Job\n\n### Advantages\n✅ **Gross = Net** (no deductions)\n✅ Very flexible (no statutory weekly-hours cap)\n✅ Combination of **2 Mini-Jobs + 1 main job** possible\n✅ **Tax-free** (annual income < €7,236)\n\n### Disadvantages\n❌ Low monthly limit (€603)\n❌ Limited pension accumulation\n❌ A cheaper alternative to Werkstudent status\n\n## Which Jobs Are Common as Mini-Jobs?\n\n### Catering / Restaurant\n- Waiter, kitchen assistant, cashier\n- €13.90-15 per hour (2026 minimum wage: €13.90)\n- 30-40 hours/month = €417-600\n\n### Lieferando / Wolt / Bolt Food\n- Courier / driver\n- €12-14 per hour (as an employee at least the €13.90 minimum wage; the minimum wage does not apply to freelance couriers)\n- ~10 hours/week ≈ €600/month\n\n### Store / Supermarket\n- Cashier, shelf stocking\n- €13.90-15 per hour\n- 8-10 hours/week\n\n### Gym / Hotel\n- Reception, cleaning, fitness instructor\n- €13.90-16 per hour\n\n### Language Assistant / Tutor\n- University tutoring, private lessons\n- €15-25 per hour (highly qualified)\n\n### Online Jobs\n- Translation, content writing, social media\n- €15-30 per hour\n\n## Mini-Job vs Werkstudent\n\n| Feature | Mini-Job | Werkstudent |\n| --- | --- | --- |\n| Monthly limit | €603 | Unlimited |\n| Hour limit | No statutory weekly-hours cap (at the €13.90 minimum wage, €603 corresponds to roughly 10 hours per week) | 20 hours/week (lecture period, social-insurance rule) |\n| Insurance | 0 | €131 student rate |\n| Tax | 0 (generally) | 9.3% pension |\n| After graduation | Does not continue | Converts to full-time |\n| Academic impact | Low | Medium |\n\n## Which One to Choose in Which Scenario?\n\n### Choose a Mini-Job if:\n- You want to work **few hours** (~10 hours/week)\n- You want to avoid **tax/insurance complications**\n- **Academic focus** is important (fewer hours)\n- You also want to keep time free during the summer break\n\n### Choose Werkstudent if:\n- You want a **higher salary** (€1,000+/month)\n- **Professional experience + CV** is a priority\n- You can manage **20 hours/week** alongside your academic load\n- You want a **job after graduation**\n\n## Mini-Job + Werkstudent Combination\n\n✅ Mini-Job + Werkstudent **at the same time** is possible\n✅ There is no statutory weekly-hours cap for a Minijob; what matters is that your regular monthly earnings stay within the Minijob limit. At the 2026 statutory minimum wage of €13.90, the €603 monthly limit corresponds to roughly 10 hours per week. This is an approximate calculation, not a statutory weekly-hours cap.\n⚠️ If you combine a Minijob with another student job, your total working time can affect your social-insurance status as a Werkstudent.\n⚠️ The residence-law working-day account under § 16b (Arbeitstagekonto, 140 working days a year) is assessed separately; for non-EU students, the working days from both jobs count together.\n\n## Tax Status\n\n### Mini-Job Pauschalsteuer (Lump-Sum Tax)\n- Employer pays a **2% lump-sum tax** (not you)\n- **Zero tax** up to €7,236 annually\n\n### Mini-Job + Other Income\n- Total annual income below **€11,604** (Grundfreibetrag) is tax-free\n- If exceeded, tax applies (affects the additional income)\n\n## Insurance Status\n\n### Mini-Job Contributions\n- **Employer's lump-sum contribution:** 30% (social security + tax + pension)\n- **You pay 0 contributions**\n- Health insurance is **included in the employer's lump sum** (student insurance is separate)\n\n### Student Insurance Status\n✅ You pay **€131/month** for student insurance yourself (separate from the Mini-Job)\n✅ The Mini-Job does not affect your contribution (you pay no contributions there)\n✅ You are health-insured through your own GKV, not through the employer\n\n## Practical Advice\n\n### For New Students\n1. Start with a **first Mini-Job** — low pressure + earning money\n2. Get used to German culture\n3. Try a **Werkstudent** job the next semester (professional)\n\n### High Academic Load (Engineering, Medicine)\n✅ **Mini-Job** is safer — your academic performance is protected\n\n### Career-Oriented\n✅ **Werkstudent** recommended — CV + money + experience\n\n## Important Notes\n\n⚠️ **Exceeding the €603 limit does not automatically turn you into a Werkstudent** — a change with the employer is required\n⚠️ **2 Mini-Jobs cannot exceed €603 in total** (even with separate employers, the combined limit applies)\n⚠️ **Mini-Job + Werkstudent cannot be with the same employer** — they must be separate employers\n\n## Visa and Work Permit\n\n### EU Citizens\n✅ Mini-Job unlimited\n✅ No hour limit\n\n### Turkish Citizens + Student Visa\n⚠️ Carefully track the **140 working days per year** limit (Arbeitstagekonto; a day with up to 4 hours of work counts as half a day)\n⚠️ Your Mini-Job working days also count toward the 140-working-day account — keep track of the total\n\nMore on the work rules (140-day account): [Internship in Germany with B1/B2 German](/en/blog/internship-in-germany-with-b1-b2-german-en)"
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
            throw new RuntimeException('Content Truth Batch 2 (C2 — iş kuralı kaynakları + DE Minijob SSS başlığı): ön kontrol başarısız, hiçbir şey yazılmadı. '.implode(' | ', array_slice($problems, 0, 40)));
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

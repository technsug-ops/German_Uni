# OSV-4 Paket 2b — Tarih, Ücret ve Tutarlılık Düzeltmeleri (Sonuç)

**Tarih:** 15.10.2026 · **Kapsam:** yalnız yerel (`almanyauni_baseline`). Commit, push, deploy, prod'a yazma ve KB gömme **yapılmadı**. Prod'a yalnız salt okunur GET atıldı (≤ 1 istek/sn).
**Paket:** `database/data/osv/2026-10-15-osv4-package2b.json` (63 düzenleme: 60 `replace` + 3 `insert_after`; `set` yok)
**Migration:** `database/migrations/2026_10_15_000300_osv4_package2b_dates_fees_consistency.php` (`OfficialInfoRepair::apply`)
**Test:** `tests/Feature/OsvPackage2bTest.php` (7 test)
**Çıkış noktası:** `OSV_PACKAGE2_CHATBOT_LIVE_VERIFICATION.md` §5'teki 7 "kısmen doğru" canlı cevap.

> Kural (mimari §6): **kontrol tarihi** (15.10.2026) yalnız bizim okuma tarihimizdir. Metinlerde yürürlük tarihi gibi kullanılmadı. Yürürlük tarihi yalnız resmî kaynakta yazıyorsa verildi (bu pakette yalnız 140 iş günü → 1 Mart 2024).

---

## 1. Özet

| Konu | Doğrulama sonucu | Metne etkisi |
|---|---|---|
| 992 €/ay "**01.09.2024'ten beri**" | **Doğrulanamadı.** BMI ilanları takvim yılı için: 2024 = **934 €** (BAnz AT 30.08.2023 B3), 2026 = **992 €** (BAnz AT 29.08.2025 B1). 01.09.2024 tarihi yalnız ikincil sitelerde var. AA'nın Maskat sayfası 11.904 € / 992 € diyor ama tarih vermiyor | Başlangıç tarihi kaldırıldı → "**2026 için** 992 €/ay (11.904 €/yıl)" + BMI ilanı. Tarihsel karşılaştırma "2024 için 934 €" kaynakla korundu |
| "Eylül 2026 itibarıyla / currently (September 2026) / Stand September 2026" (portal kanalı) | Kaynaksız durum tarihi | Resmî kaynak + "kontrol tarihi: 15.10.2026" + "başlangıç tarihi resmî sayfada yok" |
| 140 iş günü **01.03.2024** | **Doğrulandı:** BGBl. 2023 I Nr. 217, Art. 2 Nr. 6 b (§ 16b Abs. 3 yeni metni) + Art. 12 Abs. 1 ("am 1. März 2024 in Kraft"; Art. 2 Nr. 6 istisnalarda yok) | Tarih korunur. SSS başlıklarındaki "(01.03.2024'ten beri)" kaynaklı bir cümleye taşındı |
| Yarım gün kuralı | **Doğrulandı:** 2024'ten önce de vardı ("120 ganze oder 240 halbe Tage"). 2024'te yarım gün **ilk kez yasada** "en fazla 4 saat" diye tanımlandı ve haftalık 2,5 gün seçeneği eklendi (BT-Drs. 20/6500 S. 78) | "Yarım gün eski kuralda da vardı" cümlesi eklendi. "29.02.2024'te sona erdi" cümlesi doğru, korundu |
| Dil kursu **+%10** | **Doğrulandı:** AufenthG § 2 Abs. 3 **Satz 6**. Merkblatt 44F'de yöntem yok, yalnız "en az 1.091 €" var | Cümle kaldırılmadı. "Temsilcilik hesaplıyor" yanlış atfı → "yasa öngörüyor" + kaynak + 992 × 1,1 = 1.091,20 € |
| Blue Card 2026: 50.700 € / 45.934,20 € | **Yeniden teyit** (BAnz AT 18.12.2025 B3) | Değişiklik yok (paket 2 ile doğru) |
| Ulusal vize harcı 75 € | **Doğrulandı:** § 46 Abs. 2 Nr. 1 AufenthV. Muafiyet: **§ 52 Abs. 5** (kamu kaynaklı burs) | iDATA SSS'lerinin girişine "75 € harç ≠ iDATA 39,92 € ve/veya 12 € (TL)" + burs muafiyeti |

---

## 2. Resmî kaynaklar (okuma: 15.10.2026)

| Anahtar | URL | Birebir alıntı | Kapsam |
|---|---|---|---|
| `banz_subsistence_2026` | bundesanzeiger.de … `BAnz AT 29.08.2025 B1.pdf` | "… gibt das Bundesministerium des Innern den Mindestbetrag … **für das Jahr 2026** bekannt" / "… §§ 16b, 16c, 16e und 16f AufenthG, mit Ausnahme der Teilnahme an Sprachkursen, die nicht der Studienvorbereitung dienen, ergibt sich … ein Betrag für den monatlichen Bedarf in Höhe von **992 Euro**." | 2026 için 992 €/ay. "01.09.2024" yok |
| `banz_subsistence_2024` | bundesanzeiger.de … `BAnz AT 30.08.2023 B3.pdf` | "… für das Jahr 2024 bekannt" / "… §§ 16b, 16c, 16e und 16f AufenthG ergibt sich … in Höhe von **934 Euro**." | 2024 için 934 €/ay (= 11.208 €/yıl) |
| (yalnız okundu) | maskat.diplo.de/om-en/service/05-visaeinreise/2164012-2164012 | "… for students, the presumed annual requirement … has been increased to 11.904 Euros" / "(increased to 992 Euros for students)" | Tutarı doğruluyor; **tarih yok**. Makale tarihi "13.10.2020" |
| `bgbl_2023_217` | recht.bund.de/bgbl/1/2023/217/VO.html (Regelungstext PDF) | Art. 2 Nr. 6 b: "(3) … bis zu 140 Arbeitstage im Jahr … (Arbeitstagekonto) … bis zu vier Stunden … als halber Arbeitstag … zweieinhalb Arbeitstage"; Art. 12 Abs. 1: "Dieses Gesetz tritt vorbehaltlich der Absätze 2 bis 6 **am 1. März 2024** in Kraft." | 140 gün ve sayım kuralları 01.03.2024'ten beri |
| `bt_drs_20_6500` | dserver.bundestag.de/btd/20/065/2006500.pdf (S. 78) | "… bisherigen Regelung (Beschäftigungserlaubnis für **120 ganze oder 240 halbe Tage**) …" / "In Nummer 1 wird **erstmalig** der ‚halbe' Arbeitstag gesetzlich definiert …" | Yarım gün eskiden de vardı; 4 saat tanımı 2024'te yasaya girdi |
| `aufenthg_2` | gesetze-im-internet.de/aufenthg_2004/__2.html | § 2 Abs. 3 S. 6: "… §§ 16d, 16f Absatz 1 für Teilnehmer an Sprachkursen, die nicht der Studienvorbereitung dienen, … wenn Mittel entsprechend Satz 5 **zuzüglich eines Aufschlages um 10 Prozent** zur Verfügung stehen." | +%10 yasada |
| `tr_missions_language_course` | tuerkei.diplo.de/tr-de/service/05-visaeinreise/2725588-2725588 (Merkblatt 44F) | "es müssen monatlich mindestens **1091 EUR** zur Verfügung stehen. Die folgenden Optionen stehen gleichberechtigt nebeneinander: … Sperrkonto … Verpflichtungserklärung" / "Visumgebühr: i.d.R. 75,00 Euro" | Tutar + kanıt yolları; yöntem yazmıyor |
| `aufenthv_46` | gesetze-im-internet.de/aufenthv/__46.html | "1. für die Erteilung eines nationalen Visums (Kategorie „D") … **75 Euro**" | Ulusal vize harcı |
| `aufenthv_52` | gesetze-im-internet.de/aufenthv/__52.html | Abs. 5: "Ausländer, die für ihren Aufenthalt im Bundesgebiet ein **Stipendium aus öffentlichen Mitteln** erhalten, sind befreit von den Gebühren nach 1. § 46 Absatz 2 Nummer 1 …" | Metinde yalnız Abs. 5 anıldı. Abs. 6/7 takdirî, Abs. 8 Schengen bağlamı → yazılmadı |
| `tr_missions_national` | tuerkei.diplo.de/tr-tr/service/05-visaeinreise/2728742-2728742 | "iDATA **39,92 €** hizmet bedeli ve/veya **12 €** randevu rezervasyon ücreti almaktadır; her ikisi de Türk Lirası (TL) cinsinden ödenir." | iDATA bedelleri |
| `tr_missions_study` | tuerkei.diplo.de/tr-tr/service/05-visaeinreise/2728622-2728622 | "Eğitim ve öğrenim yeri arama alanındaki vize işlemleri artık yalnızca Konsolosluk Hizmet Portalı üzerinden gerçekleştirilmektedir." | Kanal; **başlangıç tarihi yok** |
| (yeniden teyit) | bundesanzeiger.de … `BAnz AT 18.12.2025 B3.pdf` | "Mindestbruttogehalt für das Jahr 2026 in Höhe von jährlich 50 700 Euro" / "… 45 934,20 Euro" | Blue Card 2026 |

Her kaynağın `quote` ve `scope` alanı paket JSON'unda da var.

---

## 3. Değişen kayıtlar (TR/EN/DE)

Slug, URL, id, çeviri grubu, canonical ve hreflang **değişmedi**. Yalnız ilgili cümle değişti.

### A. Geçim tutarı tarihi + portal kanalı (topic `funds-date` / `check-date`)

| Kayıt (TR / EN / DE id) | Önce | Sonra |
|---|---|---|
| SSS `how-do-i-get-a-german-student-visa` (1 / 270 / 271) | "01.09.2024'ten beri esas alınan tutar …" · "Since 01.09.2024 the reference amount is …" · "Seit dem 01.09.2024 gilt als Richtwert …" | "2026 için esas alınan tutar … ([BMI ilanı BAnz AT 29.08.2025 B1](…), kontrol tarihi: 15.10.2026)" · "For 2026 …" · "Für 2026 …" |
| aynı SSS (1 / 270 / 271), kanal | "(Eylül 2026 itibarıyla)" · "currently (September 2026)" · "derzeit (Stand September 2026)" | "… yalnızca online Auslandsportal üzerinden yürütüyor ([kaynak](…2728622), kontrol tarihi: 15.10.2026). Bu kuralın hangi tarihten beri uygulandığı resmî sayfada belirtilmiyor; 15.10.2026 yalnızca bizim kontrol tarihimiz." (EN/DE eşdeğeri, §6 şablonu) |
| SSS `what-documents-are-required-for-a-german-student-visa-application` (14 / 294 / 295) | "01.09.2024'ten beri ayda 992 €" + "(Eylül 2026 itibarıyla) yalnızca Auslandsportal" (EN/DE eşdeğeri) | "2026 için ayda 992 €" + "yalnızca Auslandsportal … ([kaynak](…), kontrol tarihi: 15.10.2026; başlangıç tarihi resmî sayfada belirtilmiyor)" |
| SSS `how-much-money-do-i-need-in-a-blocked-account-sperrkonto` (7 / 280 / 281) | "Bu tutar 01.09.2024'ten beri geçerli." | "Bu, 2026 için ilan edilen tutar ([BMI ilanı …], kontrol tarihi: 15.10.2026)." |
| SSS `how-much-money-do-i-need-for-a-blocked-account-sperrkonto` (106 / 463 / 464) | "… 01.09.2024'ten beri geçerli; öncesinde daha düşük bir tutar isteniyordu." | "… 2026 için ilan edilen tutar (…); 2024 için aylık 934 € idi ([BMI ilanı BAnz AT 30.08.2023 B3](…))." |
| SSS Studienkolleg Sperrkonto (82 / 416 / 417) | "(01.09.2024'ten beri, BAföG azami tutarına bağlı)" | "(BMI'nin 2026 ilanı, BAföG azami tutarına bağlı)" |
| SSS Studienkolleg kabul mektubu (89 / 430 / 431) | "(01.09.2024'ten beri ayda 992 €, …)" | "(2026 için ayda 992 €, …)" |
| SSS oturum izni uzatma (184 / 614 / 615) | "referans: 01.09.2024'ten beri aylık 992 €" | "referans: 2026 için aylık 992 €" |
| SSS DAAD master bursu (190 / 626 / 627) | "(01.09.2024'ten beri 992 €/ay = 11.904 €/yıl)" | "(2026 için 992 €/ay = 11.904 €/yıl)" |
| Yazı öğrenci vizesi rehberi (5 TR / 36 DE; EN 35'te ifade yok) | "01.09.2024'te 11.208 €'dan 11.904 €'a yükseldi." | "2024 için yıllık 11.208 € (aylık 934 €) idi (BAnz AT 30.08.2023 B3); 2026 için yıllık 11.904 € (aylık 992 €) (BAnz AT 29.08.2025 B1, kontrol tarihi: 15.10.2026)." |
| Yazı Sperrkonto 2026 güncellemesi (153 / 154 / 155): `meta_description`, `excerpt`, `content_md` | "(1 Eylül 2024'ten beri [geçerli])" · "in force since 1 September 2024" · "gültig seit dem 1. September 2024"; gövde "Bu tutar 1 Eylül 2024'ten beri geçerli (öncesinde … 934 …)" | meta/özet: "(2026 için, BMI ilanı)" · "for 2026 (BMI notice)" · "für 2026 (BMI-Bekanntmachung)"; gövde: "2026 için geçerli (…); 2024 için … 934 € idi (…)" |
| Yazı BAföG alternatifleri (114 / 113 / 112) | "(01.09.2024'ten beri …)" · "(since 1 September 2024: …)" · "(seit 01.09.2024: …)" | "(2026 için …)" · "(for 2026: …)" · "(für 2026: …)" |
| Yazı lisans başvuru akışı (186 / 185 / 184) | "… 01.09.2024'ten beri 11.904 Euro'dur" (EN/DE eşdeğeri) | "… 2026 için 11.904 Euro'dur" |
| Yazı yalnız TR: ZAB (87), TestAS (90), anabin (126), Studienkolleg listesi (192) | "(01.09.2024'ten beri …)" | "(2026 için …)" |

### B. iDATA SSS — 75 € harç ≠ iDATA bedeli (topic `visa-fee`)

| Kayıt | Önce | Sonra (girişteki iDATA paragrafının hemen arkasına, `insert_after`) |
|---|---|---|
| `how-do-you-apply-for-a-visa-through-idata` (166 / 581 / 582) | EN'de 75 € hiç yoktu. TR/DE'de 75 € yalnız aşağıdaki tabloda (chatbot'un gördüğü ilk 1.400 karakterin dışında) | "**Ücretler (kontrol tarihi: 15.10.2026):** Ulusal vize harcı 75 €'dur (AufenthV § 46 Abs. 2 Nr. 1) ve iDATA'nın kendi ücretlerinden ayrı ödenir: iDATA 39,92 € hizmet bedeli ve/veya 12 € randevu ücreti alır, ikisi de TL olarak (kaynak). Öğrenim ve öğrenim yeri arama vizeleri iDATA üzerinden yürümez; bu vizelerde iDATA ücreti doğmaz. Kamu kaynaklı burs alanlar ulusal vize harcından muaftır (AufenthV § 52 Abs. 5)." (EN/DE eşdeğeri) |

KB metninde (`stripMd`) "75" artık 822–886. karakterde (TR 839, EN 886, DE 822). Yani chatbot'un okuduğu 1.400 karakterlik bölümün içinde.

### C. Öğrenci çalışması (topic `student-work`)

| Kayıt | Önce | Sonra |
|---|---|---|
| `how-many-hours-a-week-can-i-work-as-a-student-in-germany` (134 / 517 / 518), `can-i-work-part-time-during-my-masters-in-germany` (137 / 523 / 524) | Başlık "### 140 iş günü nasıl sayılır? (01.03.2024'ten beri)" ve altında "4 saate kadar çalıştığın gün yarım gün …". Okuyan, yarım gün kuralının 2024'te başladığını sanabilir | Başlık tarihsiz. Altına: "140 iş günlük hesap 1 Mart 2024'ten beri geçerli ([BGBl. 2023 I Nr. 217](…), Madde 2 No. 6 ve Madde 12 Abs. 1; kontrol tarihi: 15.10.2026). Yarım gün eski kuralda da vardı (120 tam veya 240 yarım gün); o tarihten beri yarım gün (en fazla 4 saat) yasada açıkça tanımlanıyor ve haftalık 2,5 gün seçeneği eklendi ([Federal Meclis belgesi 20/6500, s. 78](…))." (EN/DE eşdeğeri) |

Dokunulmayanlar (doğrulandı, doğru): "Eski 120/240 günlük kural 29.02.2024'te sona erdi" ve yaklaşık 40 kayıtta (yazı + SSS) geçen "140 iş günü (Arbeitstagekonto, 01.03.2024'ten beri)". Bu yazılarda tarih 140 güne bağlı; yasal 4 saat tanımı da 01.03.2024'ten beri geçerli.

### D. Dil kursu (topic `language-course`)

| Kayıt | Önce | Sonra |
|---|---|---|
| `how-much-money-must-be-in-the-blocked-account-for-a-language-course-visa` (1949 / 1950 / 1951) | "Genel dil kursunda **temsilcilik**, geçim güvencesini BAföG azami tutarının %10 fazlası olarak hesaplıyor." | "Öğrenime hazırlık amacı taşımayan dil kursunda **Oturum Yasası**, öğrenci tutarına %10 ek öngörüyor ([AufenthG § 2 Abs. 3 cümle 6](…), kontrol tarihi: 15.10.2026): 2026 için 992 € × 1,1 = 1.091,20 €; Türkiye'deki temsilcilikler ayda en az 1.091 € istiyor ([Bilgi Notu 44F](…)). Hazırlık kursunda standart öğrenci tutarı (2026 için 992 €, BMI ilanı)." |

Dokunulmayanlar: 1937–1939 ve 1943–1945'teki "%10 fazlası" ifadeleri. Bunlar yasaya göre doğru, yalnız kaynak linki yok.

**Toplam:** 36 SSS kaydı (12 aile × 3 dil) + 15 yazı = **51 kayıt**, 63 düzenleme. Hepsi yayında.

---

## 4. Doğrulanamayan / yumuşatılan

| İddia | Durum | Ne yapıldı |
|---|---|---|
| "992 € 01.09.2024'ten beri" | UNVERIFIABLE (resmî). BMI'nin 2024 ilanı tüm 2024 yılı için 934 € diyor; 2025 yılı ilanı bulunamadı. "Eylül 2024" yalnız schengenvisainfo, studying-in-germany.org gibi ikincil sitelerde | Başlangıç tarihi tüm yayındaki içerikten kaldırıldı → "2026 için" + BMI ilanı |
| 2025 için tutar | Resmî ilan bulunamadı (Hamburg/üniversite formları 992 € diyor, resmî değil) | Metinde 2025 değeri verilmedi |
| "Eylül 2026 itibarıyla" kanal durumu | Kaynaksız | Kontrol tarihi + kaynak + "başlangıç tarihi resmî sayfada yok" |
| § 52 Abs. 8 AufenthV (öğrenci muafiyeti?) | Schengen/§ 46 Nr. 1–2 eski numaralandırma; ulusal D vizesi kapsamı belirsiz | **Yazılmadı.** Yalnız Abs. 5 (kamu kaynaklı burs) anıldı |
| Merkblatt 44F'de "+%10" | 44F sayfasında yöntem yok | Yönteme yasa (§ 2 Abs. 3 S. 6) kaynak gösterildi; 44F yalnız 1.091 € için |

---

## 5. Migration ve yerel sonuçlar

| Adım | Sonuç |
|---|---|
| Canlı GET tahmini (53 hedef sayfa, salt okuma) | 63 `find` metninin hepsi canlıda da var → prod'da **apply** beklenir (iDATA çapa metni HTML'de link biçimiyle var; 3 yeni belge SSS'si ayrıca doğrulandı) |
| `plan` (öncesi) | 60/60 `apply` |
| `php artisan migrate` | DONE |
| Ek tarama | Belge SSS'si 14/294/295'te "(Eylül 2026 itibarıyla)" kanal damgası bulundu → 3 düzenleme eklendi. Migration kaydı geri alınıp (`down` no-op) yeniden çalıştırıldı: 60 `already` + 3 `apply` |
| `plan` (sonrası) | **63/63 `already`** |
| Geri alınan transaction içinde yeniden çalıştırma | posts+faqs sağlama toplamı (md + html + updated_at) önce / içeride / sonra **aynı → NO-OP** |
| Son tarama (yayındaki içerik) | "01.09.2024", "1 September 2024", "1 Eylül 2024", "currently (September 2026)", "(Stand September 2026)", "(Eylül 2026 itibarıyla)": **0 isabet** |
| `answer_html` | Model kaydıyla yeniden üretildi (1, 270, 517, 581'de kontrol edildi) |

Kapsam dışı bırakılanlar: yazı sonlarındaki "*Bu rehberdeki bilgiler Eylül 2026 itibarıyla günceldir …*" / "Stand: September 2026" türü editoryal tazelik damgaları (yaklaşık 200 isabet). Bunlar bir kuralın başlangıç tarihi iddiası değil, yazının yazıldığı dönem notu. Ayrı bir "kontrol tarihi" dönüşümü gerekirse ayrı paket yapılmalı.

---

## 6. Chatbot değişikliği (`app/Services/Rag/ChatService.php`)

Sistem promptuna tek kural eklendi (mevcut "değişebilen bilgiyi kalıcı sunma" ve "kontrol tarihi ≠ başlangıç tarihi" kuralları korundu):

> "Resmî ilan veya yasayla belirli bir yıl ya da dönem için belirlenmiş tutar ve eşiği kaynaktaki tam rakamla, kaynaktaki yıl ve kapsamla ver (ör. "2026 için 50.700 €"); böyle bir değeri "yaklaşık", "approximately", "ungefähr" veya "ca." diye yumuşatma. Kaynakta bu bilgi yoksa bunu söyle; rakam veya yıl uydurma."

`stripInstructionLeak`'e check-date kuralındaki desenle aynı biçimde bir parmak izi eklendi (`…için belirlenmiş tutar ve eşiği kaynaktaki tam rakamla…` içeren satır silinir; doğal cümlelere dokunulmaz).

---

## 7. Eval eklemeleri (`resources/data/chat_eval.json`)

- 48 mevcut öğe korundu. Toplam **101 öğe**.
- **17 yeni `tag: "osv"` öğesi** (mevcut biçim: `q`, `lang`, `expect` + `must_contain` / `must_not_contain` / `allow_context`):
  - `visa.fee` TR/EN/DE: 75 € + 39,92 zorunlu. EN'de canlıdaki "do not specify the exact amount for the German national visa fee" yasak.
  - `bluecard.2026` TR/EN/DE: 50.700 ve 45.934 zorunlu. "yaklaşık" / "approximately", "approximate", "approx." / "ungefähr", "ca. 50", "ca. 45", "etwa 50" yasak.
  - `work.student.counting` TR/EN/DE: 140 ve 4 saat zorunlu. Kontrol tarihinden "itibaren/since/seit", "half days were introduced in 2024" vb. yasak. "Since March 1, 2024, a day with up to 4 hours…" **yasaklanmadı**: 4 saat tanımı gerçekten 01.03.2024'te yasaya girdi (BT-Drs. 20/6500).
  - `funds.date` TR/EN/DE: 992 zorunlu. "01.09.2024'ten beri", "since 1 September 2024", "seit dem 01.09.2024", "Stand September 2026", "yaklaşık 992" vb. yasak.
  - `check-date` TR/EN (kanal): "Eylül 2026 itibarıyla", "currently (September 2026)" yasak; portal zorunlu.
  - `langcourse.funds` TR/EN/DE: 1.091 + Verpflichtungserklärung zorunlu. 5.952 ve "× 992" yasak. "+%10" doğrulandığı için yasak **değil**.
- **15.10.2026 canlı kapanış testinin 44 sorusu** (`scratchpad/chat_probe2.py` SCEN: C01–C14, N01–N10), `tag: "osv-live-2026-10-15"`, `scenario` ve `topic` alanlarıyla. Aynı metinli 8 soru (N05, N07 ve N10'un bazıları) yeni OSV öğesiyle birleşti; onların `tag` alanı `["osv", "osv-live-2026-10-15"]`. `--tag=osv-live-2026-10-15` 44 sorunun tamamını, `--tag=osv` 37 öğeyi çalıştırır. ChatEval `(array) tag` ile her iki biçimi okur.
- `database/data/osv/known_wrong.json`: **13 yeni giriş** (`pkg2b-01…13`). Kaldırılan birebir tarih ifadeleri; `allow_context` ("doğrulanamadı", "not verified", "nicht belegt" …) ile tarihsel ve açıklayıcı anlatım serbest. Mevcut paket dosyalarına (package1, 1b, 2) dokunulmadı.

---

## 8. Testler

Yalıtılmış test DB: `phpunit.osv2b.local.xml` → `almanyauni_test_osv2b` (iş sonunda config silindi, DB düşürüldü).

| Test | Sonuç |
|---|---|
| `OsvPackage2bTest` (7 test): paket geçerli ve kaynaklı (her kaynakta `quote`); yeni metinlerde known_wrong / kaldırılan ifade yok; uydurma tarih yok (yalnız 15.10.2026 kontrol biçiminde + BAnz künyesi; "1 March 2024" yalnız öğrenci çalışmasında, BGBl + BT-Drs kaynağı ve "240 yarım gün" notuyla); "yaklaşık/approximately/ungefähr/ca." yok; önizleme yazmaz → uygula → ikinci plan `already` → ikinci çalıştırma no-op; içerik düzeldi, id/slug/grup aynı, yönlendirme yok; iDATA'da 75 € ilk 1.400 karakterde; beklenmeyen metin `mismatch` (ezilmez); chatbot kuralı + sızıntı temizliği; eval öğeleri + deterministik kontrol | **geçti** (`GEMINI_API_KEY=` boş ile de) |
| `OsvPackage1RepairTest`, `OsvPackage1bCheckDateTest`, `OsvPackage2Test`, `OsvPhase0GuardrailsTest`, `OfficialKnownWrongRegressionTest`, `ChatService*` | **geçti** — birlikte çalıştırma (OsvPackage2bTest dahil, `GEMINI_API_KEY=` boş): **60 test / 43.579 assertion, 0 hata** |
| `php artisan i18n:audit --leaky` | Lang dosyalarına dokunulmadı → gerekmedi |

---

## 9. KB planı (gömme yapılmadı)

KB yalnız FAQ `question + answer_md` ve yazı `title + content_md` okur. Meta/özet değişiklikleri KB'yi etkilemez. Sayım `KbEmbed::stripMd/chunkProse` (reflection) ile yapıldı; FAQ = 1 chunk/kayıt.

| Tür | Etkilenen yayındaki kayıt | Yeniden gömülecek chunk (≈) | Yereldeki mevcut chunk |
|---|---|---|---|
| FAQ | 36 | 36 | 36 |
| Yazı | 15 | 174 | 187 |
| **Toplam** | **51** | **≈ 210** | 223 |

Yerel KB bayat (bazı yazılarda 0 chunk). Canlıda `kb-embed?source=faq,post&dry=1` yaklaşık **200–230** göstermeli (başka değişiklik yoksa). 400'ü aşarsa durdurulmalı.

---

## 10. Kalan belirsizlikler

- **2025 yılı tutarı ve 992 €'ya geçiş tarihi:** resmî BMI ilanı bulunamadı. İçerik artık bu tarihi iddia etmiyor. İleride resmî bir kaynak (AA basın açıklaması veya BAnz 2024 ilanı) bulunursa tarih kaynakla geri eklenebilir.
- **Editoryal "Eylül 2026" damgaları** (yaklaşık 200 isabet): kural tarihi değil, dokunulmadı.
- 1937–1939 / 1943–1945 dil kursu SSS'lerinde "+%10" doğru ama kaynak linki yok.
- Chatbot kuralı yalnız prompt düzeyinde; modelin uyumu deploy + KB sonrası `chat:eval --tag=osv` ile ölçülmeli.
- `Since March 1, 2024, a day with up to 4 hours counts as half a day` cümlesi teknik olarak doğru (4 saat tanımı 2024'te yasaya girdi), bu yüzden eval'de yasaklanmadı. Metinler artık yarım günün eskiden de var olduğunu söylüyor.

## 11. Yayın riskleri ve kontrol listesi

Riskler:
- **Prod metin farkı:** canlı GET'te 63 `find`'ın hepsi bulundu. Panelde sonradan değişen kayıt olursa `mismatch` ile atlanır (ezilmez).
- **Meta/özet değişikliği** (153–155): başlık ve URL aynı; yalnız açıklama değişiyor → SERP snippet'i kısa süre değişebilir.
- **Paket 2 bağımlılığı:** iDATA çapası (`IOM-FAP … kontrol tarihi: 15.10.2026`) paket 2'nin eklediği metin. Paket 2 prod'da (canlıda doğrulandı); 000100 migration'ı 000300'den önce çalışır.
- **Eval kapısı:** yeni `must_not_contain` öğeleri KB yeniden gömülmeden çalıştırılırsa eski chunk'lar yüzünden kırmızı olabilir.

Kontrol listesi (onay sonrası):
1. Commit: paket JSON, migration, `OsvPackage2bTest`, `ChatService.php`, `chat_eval.json`, `known_wrong.json`, bu rapor.
2. Push → deploy (`_deploy.php` migration'ı çalıştırır).
3. `/admin/ops/osv-repair?file=2026-10-15-osv4-package2b` → 63/63 `already` (mismatch varsa listelenir).
4. Canlı GET: `/en/faq/visa/how-do-i-get-a-german-student-visa-en` ("For 2026", "checked 15.10.2026"), `/en/faq/appointments/how-do-you-apply-for-a-visa-through-idata-en` ("€75"), `/de/faq/work/how-many-hours-a-week-can-i-work-as-a-student-in-germany-de`, `/en/news/2026-germany-blocked-account-sperrkonto-update-how-much-money-do-you-en` (meta description). Canonical/hreflang değişmemeli.
5. KB: `/admin/ops/kb-embed?source=faq,post&dry=1` → ≈ 210 chunk; makulse gerçek çalıştırma (önce Gemini kredisi kontrol).
6. `php artisan chat:eval --tag=osv` (hedef: 0 yasak ifade) ve karşılaştırma için `--tag=osv-live-2026-10-15`.

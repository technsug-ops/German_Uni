# ApplyToGerman — Resmî Kaynak Boşluk Denetimi (OSV-0 / OSV-1)

**Tarih:** 14 Ekim 2026
**Kapsam:** 30 içerik (27 blog/haber yazısı grubu + 3 araç/landing sayfası) ve görüntülenme sayısına göre ilk 50 FAQ kaydı; ilgili TR/EN/DE kardeşleri ve yerel KB (`kb_chunks`) tutarlılığı.
**Yöntem:** Yerel anonim baseline (`almanyauni_baseline`, yalnız SELECT) + repo kodu/görünümleri + resmî kaynakların 14.10.2026 tarihli canlı okunması.
**Durum:** **Yalnız analiz — hiçbir değişiklik uygulanmadı.** Kod, DB, migration, KB, sitemap, robots, redirect ve içerikte değişiklik yok; commit/push/deploy yok.
**Bağlı tasarım:** `SEO_OFFICIAL_SOURCE_VERIFICATION_WORKSTREAM.md` (statüler, hiyerarşi, P0/P1/P2, kalite kapısı).

---

## 1. Özet

**Ana sayılar**

| Ölçüt | Değer |
|---|---|
| Matristeki iddia satırı | 50 (41’i P0) |
| SUPPORTED / CONFLICT / OUTDATED | 14 / 12 / 6 |
| UNVERIFIABLE / SCOPE_MISMATCH / NEEDS_REVIEW | 10 / 3 / 5 |
| P0 CONFLICT/OUTDATED taşıyan içerik grubu (blog + FAQ + araç) | 18 çeviri grubu, yaklaşık 45 kayıt (diller dahil) |
| Yayındaki 772 yazıdan `source_url` alanı dolu olan | 43; **hiçbiri resmî kaynak değil** (30× news.google.com, kalanı yesgermany/gradgermany/expatrio/visaflow/shiksha) |
| Gövdesinde en az bir resmî link olan yazı / FAQ | 169 / 772 yazı; 340 / 1.943 FAQ; ilk 50 FAQ’ta 22 |
| Yazı ve FAQ’larda `verified_at` / `last_checked` alanı | **Yok** (yalnız üniversite/şehir `content_blocks` içinde `editorial_lock` + `verified_at`, program için `program_verifications`) |
| Make it in Germany sayfaları | **4/4 alınamadı** (Radware bot doğrulaması); P0 iddialar bu yüzden birincil kaynaktan (kanun, BMI Bundesanzeiger ilanı, Türkiye temsilcilikleri) doğrulandı |

**Kesin olarak doğrulananlar (iyi haber):** 2026 geçim kanıtı tutarı **992 €/ay (11.904 €/yıl)** BMI’nin Bundesanzeiger ilanı ve BAföG §13/§13a ile birebir doğrulandı (475+380+102+35 = 992). Öğrenci çalışma kuralı (**140 iş günü, Arbeitstagekonto**, 4 saate kadar yarım gün, ders döneminde ≤20 saatlik hafta = 2,5 gün, öğrenci yan işleri sayılmaz) güncel § 16b Abs. 3 metniyle uyumlu; 13 Ekim’de düzeltilen ana çalışma yazıları ve FAQ 517/515 doğru. Mezuniyet sonrası **18 ay** (§ 20) ve Blue Card **2026 eşikleri 50.700 € / 45.934,20 €** (BAnz AT 18.12.2025 B3) doğru verilen yerler var. Remonstration’ın 01.07.2025’te kalkması (yazılar 5/35/36, 94–96; FAQ 15/296/297) doğrulandı.

**En büyük riskler (P0)**
1. **APS yanlışı:** "APS sertifikası Türk öğrenciler için zorunlu" (yazılar 97/98/99). APS yalnız Çin, Vietnam ve Hindistan için var (uni-assist, TUM). Yanlış yönlendirme başvuruyu doğrudan etkiliyor.
2. **İş arama vizesi yazısı geçersiz hukuku anlatıyor** (71/72/73: "6 ay, çalışma yasak"). Yurt dışı mezunlar için yürürlükteki yol § 20a Chancenkarte: 1 yıla kadar, haftada ortalama 20 saat çalışma ve deneme çalışması.
3. **Vize kanalı ve ücretleri çelişkili:** Türkiye’deki temsilcilikler öğrenim ve öğrenim yeri arama vizelerini **yalnız Auslandsportal** üzerinden yürütüyor. Buna karşın `/tools/visa-appointment` ve 3 yazı grubu "tek yetkili/tek resmî kanal iDATA" diyor. iDATA ücreti sitede üç farklı şekilde geçiyor: "ücretsiz", "32,81 €" ve "75 € online". Resmî ücret 39,92 € hizmet ücreti ve/veya 12 € randevu ücreti. TR/DE metinlerde ayrıca `vfsglobal.com` geçiyor.
4. **Kalkmış remonstration hakkı hâlâ öneriliyor:** 8 FAQ kaydı (3 grup) "30 gün içinde itiraz" diyor. Ayrıca FAQ 1159 (EN) ve DE kardeşi 1160 "temsilcilik genelde remonstration sunar" diyor. Bu kayıtlar Temmuz 2026'da oluşturuldu, ancak cümle **13 Ekim onarım paketleriyle yeniden yazıldı**: EN metni `2026-10-13-batch1.json`, DE metni `2026-10-13-batch2.json` içindeki onarımlardan geliyor (doğrulandı). İki paket de canlıda uygulandı; yani eskimiş bilgi canlıda.
5. **Çok okunan FAQ 652/16/298:** "Yılda en fazla 20 saat/hafta" ve "18 ay otomatik/koşulsuz (§ 20 Abs. 3)" yazıyor. DE kardeşi hâlâ "120/240 gün" diyor.
6. **Dil kursu Sperrkonto’su:** 20/55/56 yazıları "6 × 992 €" ve "Sperrkonto zorunlu" diyor. Türkiye temsilciliğinin Merkblatt 44F’sine göre en az **1.091 €/ay** gerekiyor ve Verpflichtungserklärung eşit seçenek.
7. **Yerel KB eski:** FAQ/yazı chunk’ları en son 29.06–01.07.2026’da gömülmüş. 50 kaydın KB metni hâlâ "120/240 gün", 15 kaydınki eski Blue Card rakamlarını taşıyor. (Prod KB durumu bilinmiyor; bkz. §10.)

## 2. Yöntem ve sınırlar

- **Envanter seçimi (30 içerik):** Yayındaki 772 yazıda P0 kalıpları (çalışma günü/saat, Sperrkonto tutarı, 18 ay, yerleşim izni süreleri, Blue Card maaşı, vize ücreti, iDATA/Auslandsportal) regex ile tarandı. Yazılar, P0 iddia yoğunluğu ve çeviri grubu toplam `view_count` değerine göre sıralandı. Bunlara SEO Recovery dokümanlarındaki P1 niyetleri (başvuru, yüksek lisans şartları, ücretler, şartlı kabul, başvuru tarihleri) ve GSC listesindeki Deutschlandticket rehberi eklendi. Statik P0 araç/landing sayfalarından 3’ü (`/tools/visa-appointment`, `/tools/sperrkonto`, `/study-in-germany`) da alındı. Her satır bir çeviri grubunu temsil ediyor; kardeşler listelendi.
- **50 FAQ:** Metrik `faqs.view_count` (anonim yerel baseline; 1.943 yayındaki kaydın hepsinde > 0). Liste dil kardeşlerini ayrı kayıt olarak sayıyor; örneğin 467/468/108 aynı gruptan. P0 iddiası olmayan FAQ’lar envanterde "P0 iddia yok / P2" olarak işaretlendi ve matrise alınmadı.
- **Doğrulama:** Her iddia için kaynak metin bizzat okundu. Kanunlarda gesetze-im-internet.de’deki güncel metin, tutarlarda BMI’nin Bundesanzeiger ilanının PDF metni kullanıldı. "Resmî URL mevcut" tek başına SUPPORTED sayılmadı.
- **Sınırlar:**
  - Make it in Germany (4 sayfa) bot korumasından dolayı okunamadı.
  - Auslandsportal JavaScript uygulaması olduğu için kategori kontrol listeleri okunamadı.
  - DAAD, Deutsche Rentenversicherung (Werkstudent), ZAB, anabin, Deutschlandticket ve BW Bilim Bakanlığı sayfaları bu turda alınmadı ya da 404 verdi.
  - Prod verisi yerel baseline’dan farklı olabilir (bilinen veri kayması). `view_count` anonim kopyadan geliyor.
  - `updated_at` değerleri 10–13 Ekim’deki toplu güncellemeleri yansıtıyor; içerik doğrulama tarihi olarak kullanılamaz.
  - Kaynak metinlerden yalnız kısa alıntı yapıldı.

## 3. Resmî kaynak haritası (hepsi 14.10.2026’da okundu)

| # | Kaynak (sahip, hiyerarşi) | URL | Okunan içerik (özet/kısa alıntı) | Kapsam |
|---|---|---|---|---|
| S1 | Make it in Germany – Voraussetzungen (Bundesregierung, H3) | https://www.make-it-in-germany.com/de/studium-ausbildung/studieren-in-deutschland/voraussetzungen | **ALINAMADI** – WebFetch ve düz GET ikisi de Radware "Verifying your browser" sayfası döndü | — |
| S2 | Make it in Germany – Arbeit (H3) | …/studieren-in-deutschland/arbeit | **ALINAMADI** (aynı neden) | — |
| S3 | Make it in Germany – Perspektiven danach (H3) | …/studieren-in-deutschland/perspektiven-danach | **ALINAMADI** | — |
| S4 | Make it in Germany – Studieren in Deutschland (H3) | …/studium-ausbildung/studieren-in-deutschland | **ALINAMADI** | — |
| L1 | AufenthG § 16b (H1) | https://www.gesetze-im-internet.de/aufenthg_2004/__16b.html | Abs. 3: "bis zu 140 Arbeitstage im Jahr … (Arbeitstagekonto)"; öğrenci yan işleri sayılmaz; ≤4 saat = yarım gün; ders döneminde ≤20 saat/hafta ve ders dışı her hafta = 2,5 gün. Abs. 2: oturum izni kural olarak 2 yıl, en az 1 yıl. Abs. 5: şartlı kabul/dil kursu türleri | Non-EU öğrenci (§ 16b), güncel metin |
| L2 | AufenthG § 20 (H1) | …/__20.html | Abs. 1 Nr. 1: Almanya’da başarıyla tamamlanan öğrenim sonrası iş arama izni; Abs. 2: "setzt die Lebensunterhaltssicherung voraus", "bis zu 18 Monaten", bu sürelerin ötesine uzatma hariç. **Abs. 3 yok** | Almanya mezunları |
| L3 | AufenthG § 20a (H1) | …/__20a.html | Chancenkarte: ilk etapta 1 yıla kadar; "durchschnittlich insgesamt höchstens 20 Stunden je Woche" çalışma + en fazla 2 haftalık deneme çalışması | Yurt dışı nitelikli kişiler |
| L4 | AufenthG § 2 Abs. 3 (H1) | …/__2.html | Öğrenci geçimi BAföG §§ 13, 13a Abs. 1 bedeline göre; "studienvorbereitend olmayan dil kursu" hariç; BMI tutarı her yıl 31 Ağustos’a kadar ilan eder | § 16a–16c, 16e, 16f |
| L5 | AufenthG § 4a, § 16f, § 18c, § 18g (H1) | …/__4a.html, __16f.html, __18c.html, __18g.html | § 4a(1): kanun yasaklamadıkça çalışma serbest. § 16f: dil kursu izni "bis zu 20 Stunden je Woche". § 18c: Almanya mezunlarına 2 yıl/24 ay katkı; Blue Card 27 ay, B1 ile 21 ay. § 18g: eşikler RV tavanının %50’si / %45,3’ü, BMI ilan eder | Federal |
| L6 | BAföG § 13, § 13a (H1) | https://www.gesetze-im-internet.de/baf_g/__13.html, __13a.html | Yükseköğretim 475 €, konut (aile yanında değil) 380 €, KV 102 €, PV 35 € → **992 €** | Güncel metin (fetch tarihi) |
| L7 | BMI Bekanntmachung § 2 Abs. 3 AufenthG, 13.08.2025 (H1) | https://www.bundesanzeiger.de/pub/publication/7ZVbxqtVVGzHzr35dka/content/7ZVbxqtVVGzHzr35dka/BAnz%20AT%2029.08.2025%20B1.pdf | 2026 için § 16b, 16c, 16e, 16f (studienvorbereitend olmayan dil kursu hariç) aylık bedel **992 Euro**; BAnz AT 29.08.2025 B1 | Takvim yılı 2026 |
| L8 | BMI Bekanntmachung § 18g, 02.12.2025 (H1) | https://www.bundesanzeiger.de/pub/publication/REViP4bN6jVdpGxPaiQ/content/REViP4bN6jVdpGxPaiQ/BAnz%20AT%2018.12.2025%20B3.pdf | Blue Card 2026: **50.700 €**; indirimli (§ 18g Abs. 1 S. 2, Abs. 2) **45.934,20 €** | Takvim yılı 2026 |
| M1 | Türkiye temsilcilikleri – Nationale Visa (AA, H1 işlem makamı) | https://tuerkei.diplo.de/tr-de/service/05-visaeinreise/2703120-2703120 | Temsilcilikler "ausschließlich mit den externen Dienstleistern iDATA und IOM-FAP" çalışıyor; iDATA randevuyu yapıyor veya Auslandsportal’a yönlendiriyor; ücret "39,92 €" hizmet ve/veya "12 €" randevu (TL ödenir); kayıt tek başına başvuru değil | Türk vatandaşları, ulusal vize |
| M2 | Türkiye – Studium und Studienplatzsuche (H1) | …/2720740-2720740 | "Das Visumverfahren im Bereich Studium und Studienplatzsuche wird ausschließlich über das Auslandsportal abgewickelt." | Türkiye, öğrenim + öğrenim yeri arama |
| M3 | Türkiye – Bildungsaufenthalte (H1) | …/2725836-2725836 | Geçim kanıtı: Sperrkonto **veya** Verpflichtungserklärung | Türkiye, eğitim amaçlı |
| M4 | Türkiye – Merkblatt 44F Sprachkurs (H1) | …/2725588-2725588 | "monatlich mindestens 1091 EUR"; Sperrkonto ve Verpflichtungserklärung "gleichberechtigt"; vize ücreti "i.d.R. 75,00 Euro" | Türkiye, § 16f dil kursu |
| M5 | Türkiye – Merkblatt 25 Sperrkonto (H1) | …/2621556-2621556 | Sağlayıcı seçimi serbest, hesap kapatma süreci; **tutar yok** | Türkiye |
| M6 | Türkiye – Was tun nach Ablehnung (H1) | …/2725832-2725832 | "Seit dem 01.07.2025 kann … nicht mehr … remonstriert werden"; seçenekler: yeni başvuru ya da 1 ay içinde VG Berlin’de dava | Türkiye, tüm vizeler |
| M7 | Auswärtiges Amt – Sperrkonto (H3) | https://www.auswaertiges-amt.de/de/sperrkonto-375488 | Tutar amaca göre değişir, "orientiert sich an den Förderhöchstsätzen des BAföG"; tutar için temsilciliğe/Auslandsportal’a yönlendiriyor (sayfa tarihi 21.10.2024) | Genel |
| M8 | AA FAQ – Finanzierung Studium (H3) | https://www.auswaertiges-amt.de/de/service/fragenkatalog-node/08-finanzierung-606220 | Kanıt yolları: ebeveyn geliri, Verpflichtungserklärung, Sperrkonto, banka teminatı, tanınan burslar | Genel |
| U1 | uni-assist – Handling fees (H4) | https://uni-assist.de/en/how-to-apply/pay-all-fees/handling-fees | Dönem başına ilk tercih "EUR 75.00", her ek tercih "EUR 30.00"; VPD için de aynı; bazı üniversiteler ücreti karşılıyor | uni-assist üyesi üniversiteler |
| U2 | uni-assist – APS sözlüğü (H4) | https://www.uni-assist.de/en/tools/glossary-of-terms/description/term/akademische-pruefstelle-aps/ | "It exists in China, Vietnam and India." | APS ülkeleri |
| U3 | TUM – APS (H2) | https://www.tum.de/studium/bewerbung/infoportal-bewerbung/aps | APS yalnız Çin, Vietnam ve Hindistan belgeleriyle başvuranlara; Türkiye geçmiyor | TUM |
| U4 | TUM – Tuition fees (H2) | https://www.tum.de/en/studies/fees/tuition | WS 2024/25’ten beri yeni kaydolan non-EU öğrenciler ücret ödüyor: Bachelor genelde 2.000/3.000 €, Master 4.000/6.000 €/dönem; WS 2027/28’de artış | TUM (Bavyera) |
| U5 | Heidelberg Üniv. – Tuition fees (H2) | https://www.uni-heidelberg.de/en/study/management-of-studies/semester-fees/tuition-fees-for-international-students | "EUR 1500 per semester" Bachelor ve **konsekütif Master** için; LHGebG, WS 2017/18 | BW, non-EU/EEA |
| U6 | Minijob-Zentrale (H1 yürütücü kurum) | https://www.minijob-zentrale.de/DE/die-minijobs/rentenversicherungspflicht | "Verdienstgrenze von 603 Euro" geçiyor; sayfada yıl yazmıyor | Minijob |
| U7 | BAMF – Blaue Karte EU (H3) | https://www.bamf.de/DE/Themen/MigrationAufenthalt/ZuwandererDrittstaaten/Migrathek/BlaueKarteEU/blauekarteeu-node.html | Yerleşim izni: 27 ay, B1 ile 21 ay; eşik rakamı yok (sayfa tarihi 18.11.2023) | Blue Card |
| X | Alınamayan diğer kaynaklar | bafoeg.de (bağlantı reddedildi), mwk.baden-wuerttemberg.de ve baden-wuerttemberg.de basın sayfaları (404), uni-assist eski "costs" URL’si (404), digital.diplo.de/studium (JS uygulaması) | — | — |

## 4. OSV-0 envanter

### 4.1 30 içerik

`görüntülenme` = çeviri grubundaki tüm dillerin `view_count` toplamı. `kaynak` = `source_url` alanı / gövdedeki resmî link sayısı (grubun tüm dilleri). Tarihler `published_at` / `updated_at`. Yazıların hiçbirinde doğrulama tarihi alanı yok.

| # | Temsilci (id, dil) – yol | Kardeşler | Konu | Kritik iddialar (siteden kısa alıntı) | Kaynak | Yayın / güncelleme | Matris |
|---|---|---|---|---|---|---|---|
| 1 | 154 en – `/en/news/2026-germany-blocked-account-sperrkonto-update-how-much-money-do-you-en` | tr:153, de:155 | Finansman | "€11,904 a year … €992 a month … since 1 September 2024 (before … €11,208 / €934)" | yesgermany.com / 0 | 02.02 / 13.10 | C01, C02 |
| 2 | 54 en – `/en/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-en` | tr:19, de:182 | Finansman | "As of 2024, and expected to remain valid for 2025 … **992 Euro**" | – / 0 | 23.02 / 13.10 | C01, C03 |
| 3 | 20 tr – `/tr/blog/sperrkonto-usage-different-visa-and-scholarship-situations-extension-2026` | en:55, de:56 | Finansman / dil kursu | "6 aylık bir dil kursu için 6 x 992 Euro = 5.952 Euro"; "dil kursu … Sperrkonto zorunludur" | – / 0 | 02.03 / 12.10 | C04, C05 |
| 4 | 6 tr – `/tr/blog/sperrkonto-2025-complete-guide-blocked-account-for-germany-visa` | yok (yalnız TR) | Finansman | Başlıkta "2025"; "2024 yılı itibarıyla ve 2025 için de geçerli olması beklenen … 992 Euro" | – / 0 | 02.05 / 10.10 | C01, C03 |
| 5 | 5 tr – `/tr/blog/germany-student-visa-2026-application-steps-documents-rejection` | en:35, de:36 | Vize | "Randevu online alınır, **ücretsizdir**"; "`vfsglobal.com` veya …"; "Vize ücreti 75 €"; "Remonstration 1 Temmuz 2025’te kaldırıldı"; bekleme 4–12 hafta | – / 0 | 20.05 / 13.10 | C32, C35, C36, C37, C40 |
| 6 | 148 en – `/en/news/germany-visa-application-goes-online-faster-easier-process-en` | tr:147, de:149 | Vize | "Consular Services Portal … 167 German visa offices … 4–6 weeks" | expatrio.com / 0 | 18.05 / 13.10 | C41 |
| 7 | 232 tr – `/tr/blog/germany-student-visa-consulate-process-before-you-go` | en:245, de:246 | Vize | "başvurular çoğunlukla iDATA … üzerinden"; "bazı konsolosluklarda (İstanbul, İzmir) başvuru [Auslandsportal’dan]" | – / 6 (diplo) | 19.04 / 13.10 | C31 (kısmen) |
| 8 | 7 tr – `/tr/blog/how-to-get-a-german-visa-appointment-the-idata-ordeal-and` | en:39, de:178 | Vize randevusu | "Almanya vize randevusu için **tek resmi platform** iDATA’nın web sitesidir"; "Türkiye’de … yetkili kurum iDATA’dır" | – / 0 | 15.01 / 13.10 | C31 |
| 9 | 93 tr – `/tr/blog/germany-consulate-visa-appointment-2026-waiting-times-and-city-strategy` | en:92, de:91 | Vize randevusu | "öğrenci vizesi için iDATA üzerinden randevu"; "İstanbul/Ankara/İzmir’de **iDATA zorunlu**"; EN: "Pay the €75 fee online" | – / 0 | 19.05 / 13.10 | C31, C34, C40 |
| 10 | 25 tr – `/tr/blog/student-work-permit-in-germany-2026-20-hour-rule-and-types` | en:65, de:66 | Çalışma | "01.03.2024’ten beri … yılda **140 iş günlük** hesap (Arbeitstagekonto)"; "20 saat … Werkstudent sosyal sigorta koşulu" | – / 0 | 11.06 / 13.10 | C10, C11, C16 |
| 11 | 151 en – `/en/news/germanys-student-work-limits-increased-you-can-earn-more-now-en` | tr:150, de:152 | Çalışma | "Since March 1, 2024 … 120 → 140 full days (240 → 280 half days)"; "average annual earnings … ~10,500 €" | gradgermany.com / 0 | 11.03 / 13.10 | C10, C11, C18 |
| 12 | 211 tr – `/tr/blog/student-career-guide-in-germany-work-permits-job-types-salary-deductions` | en:212, de:213 | Çalışma | "yılda 140 tam gün … veya 280 yarım gün"; "Tatil dönemlerinde … tam zamanlı çalışabilirsin" | – / 3 | 22.05 / 11.10 | C10 |
| 13 | 755 tr – `/tr/blog/hiwi-vs-werkstudent-student-jobs-in-germany` | de:756, en:757 | Çalışma | "HiWi … 140 gün hesabına kural olarak dahil edilmez" | – / 0 | 20.09 / 12.10 | C10, C14 |
| 14 | 72 en – `/en/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-en` | tr:71, de:73 | Mezuniyet sonrası (yurt dışı mezun) | "Job-Seeker Visa … up to **6 months**"; "This visa does **NOT** allow you to work"; Sperrkonto "6 × €992 = €5,952" | – / 3 | 23.04 / 13.10 | C20 |
| 15 | 310 en – `/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en` | de:309, tr:308 | Mezuniyet sonrası | "2026: general **€50,700**; reduced **€45,934.20**" | – / 0 | 22.06 / 12.10 | C24 |
| 16 | 316 en – `/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en` | tr:314, de:315 | Mezuniyet sonrası | "§ 20 … up to 18 months"; "graduates may work without restriction during these 18 months"; "cannot be extended beyond 18 months" | – / 0 | 16.07 / 13.10 | C21, C23 |
| 17 | 33 en – `/en/blog/doing-a-masters-in-germany-2026-a-z-guide-en` | tr:4, de:34 | P1 yüksek lisans şartları | "annual salary ≥ 45,300 € = automatic Blue Card"; uni-assist "75 € … then 30 € per additional"; TR’de ek olarak: "deadline’ları kış 15 Temmuz, yaz 15 Ocak" | – / 0 | 09.05 / 13.10 | C25, C51, C52 |
| 18 | 41 en – `/en/blog/uni-assist-application-guide-a-z-reach-your-germany-university-dream-en` | tr:9, de:42 | P1 başvuru | uni-assist süreç anlatımı (ücret rakamı kalıbı yakalanmadı) | – / 0 | 25.04 / 13.10 | C51 (bağlam) |
| 19 | 86 en – `/en/blog/zab-diploma-equivalency-official-recognition-guide-for-masterphd-in-germany-en` | de:85, tr:87 | Denklik | TR: "İlk başvuru için ücret **yaklaşık 200 Euro**"; "ZAB süreci 2–3 ay" | – / 1 | 07.05 / 12.10 | C55 |
| 20 | 98 en – `/en/blog/aps-certificate-turkey-2026-germany-university-guide-en` | de:97, tr:99 | Kabul | "for the vast majority of Turkish students … the **APS application** process is mandatory"; "Anabin H+ … not sufficient; an APS certificate is also mandatory" | – / 0 | 27.01 / 10.10 | C50 |
| 21 | 83 en – `/en/blog/studienkolleg-guide-2026-who-needs-it-which-course-which-school-en` | de:82, tr:84 | Kabul / Studienkolleg | "a Lise graduate … with the required YKS score can apply directly"; Studienkolleg ihtiyacı erişim yoluna bağlı | – / 0 | 15.01 / 13.10 | C64 |
| 22 | 241 en – `/en/blog/conditional-admission-germany-bachelor-master-2026-guide` | tr:230, de:242 | P1 şartlı kabul | "According to **§ 16b Abs. 5 AufenthG** … language preparation … generally one year and should not exceed two years"; "student applicant / language course visa (under § 16b)"; Sperrkonto "€11,904" | – / 0 | 25.02 / 13.10 | C01, C53, C54 |
| 23 | 166 en – `/en/news/is-tuition-free-university-in-germany-a-reality-your-application-guide-en` | tr:165, de:167 | P1 ücretler | "The vast majority of public universities in Germany do not charge tuition fees for anyone, including international students" | news.google.com / 0 | 31.05 / 13.10 | C58 |
| 24 | 200 en – `/en/blog/deutschlandticket-2026-price-coverage-and-the-student-semester-ticket-en` | tr:199, de:201 | Yaşam maliyeti (GSC) | "From January 2026 … **€63 per month**"; KVB Semesterticket 34,80 € / 37,80 € | – / 0 | 28.01 / 13.10 | C62 |
| 25 | 185 en – `/en/blog/germany-bachelor-application-2026-complete-flow-guide-for-turkish-high-school-en` | de:184, tr:186 | P1 başvuru (lise) | NC, Studienkolleg ve not dönüşümü akışı; resmî kaynak yok | – / 0 | 03.02 / 13.10 | C64 |
| 26 | 101 en – `/en/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs-en` | de:100, tr:102 | Sigorta (vize) | "GKV ~€120–130/ay"; "Private … OK for under 30 + first year" | – / 0 | 06.06 / 13.10 | C63 |
| 27 | 221 en – `/en/blog/student-visa-interview-common-questions-and-how-to-prepare-en` | tr:220, de:222 | Vize | "interview is short (5–15 min)"; finansman: "the current Sperrkonto amount, a scholarship, or a sponsorship declaration" | – / 0 | 14.06 / 11.10 | C40 |
| 28 | Araç – `/{tr,en,de}/tools/visa-appointment` (`resources/views/tools/visa-appointment.blade.php`) | 3 dil, tek şablon | Vize randevusu (TR çekirdek) | "iData is the **only** center authorized … to collect visa applications in Turkey"; "**€32.81** iData fee within 48 hours"; "Istanbul or Ankara iData office"; "verified 2026-06-04" | Şablonda iData ve AA linkleri | şablon | C31, C33 |
| 29 | Araç – `/{tr,en,de}/tools/sperrkonto` (`tools/blocked-account/*.blade.php`) | 3 dil + ülke sayfaları | Finansman | "€11,904 for 2026", "€992 monthly"; kaynak notu Merkblatt 25’e bağlanıyor (`updated="2026-06-04"`) | `x-source-note` | şablon | C01, C07 |
| 30 | Landing – `/{tr,en,de}/study-in-germany` (`about-germany.blade.php`) | 3 dil | Genel P1 giriş | "18 ay iş arama vizesi"; maliyet tablosunda "Blocked account €11,904", "Semester fee €300–700" | Kaynak notu yok | şablon | C01, C21 |

Ek gözlem (envanter dışı, aynı iddiayı taşıyan kod):
- 11.904/992 rakamı en az 10 yerde sabit yazılmış: `ToolsController.php` (271, 330, 521–522, 975), `ApplicationTracker.php:30`, `Support/PageFaq.php` (59, 130, 322–364), `config/glossary.php` (96–118), `config/ads.php`, `housing/index.blade.php`, `programs/show.blade.php`.
- `PageFaq.php:322` rakamı "DAAD estimates around €992/month" diye DAAD’a atfediyor (C08).
- `/tools/visa-cost` hâlâ "Up-to-date 2025 figures" / "average values for 2025" diyor.

### 4.2 En çok görüntülenen 50 FAQ

Metrik: `faqs.view_count` (yerel anonim baseline). Yol: `/{locale}/faq/{topic}/{slug}`. "resmî link" = cevapta resmî alan adına link olup olmadığı. FAQ’larda kaynak ve doğrulama tarihi alanı yok.

| # | id | dil | yol | konu | kritik iddia / matris | resmî link | güncelleme | kardeşler |
|---|---|---|---|---|---|---|---|---|
| 1 | 463 | en | `/en/faq/money/how-much-money-do-i-need-for-a-blocked-account-sperrkonto-en` | money | 992 €/ay, 11.904 €/yıl (2026); "01.09.2024’ten beri"; dil kursu 1.091 €/ay (44F) → C01/C02 | var | 2026-10-13 | de:464, tr:106 |
| 2 | 487 | en | `/en/faq/money/whats-the-best-way-to-send-money-from-germany-to-turkey-en` | money | Para transferi (ticari) — P0 iddia yok | yok | 2026-10-13 | de:488, tr:118 |
| 3 | 467 | en | `/en/faq/money/will-providing-swift-branch-details-cause-problems-for-my-fintiba-transfer-en` | money | Fintiba SWIFT (ticari) — P0 iddia yok | yok | 2026-10-13 | de:468, tr:108 |
| 4 | 525 | en | `/en/faq/work/is-job-searching-via-linkedin-de-effective-for-students-en` | work | "120 days" çalışma sınırı → C12 | yok | 2026-10-13 | de:526, tr:138 |
| 5 | 511 | en | `/en/faq/city-life/which-german-states-charge-tuition-fees-for-non-eu-students-en` | city-life | Harç yalnız BW; Bayern ücretsiz; BW Master ücretsiz; 1.500 €/dönem → C56/C57 | yok | 2026-10-13 | de:667, tr:130 |
| 6 | 658 | en | `/en/faq/master/how-do-you-find-phd-programs-in-germany-en` | master | Doktora arama yöntemi — P0 iddia yok | var | 2026-10-13 | de:344, tr:42 |
| 7 | 465 | en | `/en/faq/money/whats-the-most-cost-effective-way-to-transfer-money-from-turkey-to-germany-en` | money | 11.904 € transfer örneği → C01 | yok | 2026-10-13 | de:466, tr:107 |
| 8 | 491 | en | `/en/faq/city-life/which-are-the-cheapest-cities-for-students-in-germany-en` | city-life | Şehir maliyet aralıkları — resmî kaynak yok (P2) | yok | 2026-10-12 | de:492, tr:120 |
| 9 | 535 | en | `/en/faq/work/how-do-you-find-your-first-internship-or-working-student-position-en` | work | Werkstudent 20 saat; Minijob 603 € (2026) → C16/C17 | yok | 2026-10-13 | de:536, tr:143 |
| 10 | 468 | de | `/de/faq/money/will-providing-swift-branch-details-cause-problems-for-my-fintiba-transfer-de` | money | Fintiba SWIFT — P0 yok | yok | 2026-10-13 | en:467, tr:108 |
| 11 | 108 | tr | `/tr/faq/money/will-providing-swift-branch-details-cause-problems-for-my-fintiba-transfer` | money | Fintiba SWIFT — P0 yok | yok | 2026-10-13 | de:468, en:467 |
| 12 | 360 | en | `/en/faq/master/can-i-apply-for-a-masters-without-waiting-for-my-certificates-en` | master | Belgesiz Master başvurusu (üniye bağlı) — P2 | var | 2026-10-12 | de:361, tr:52 |
| 13 | 526 | de | `/de/faq/work/is-job-searching-via-linkedin-de-effective-for-students-de` | work | "120 Tage" → C12 | yok | 2026-10-13 | en:525, tr:138 |
| 14 | 636 | en | `/en/faq/scholarships/what-phd-scholarships-are-available-in-germany-en` | scholarships | DAAD PhD 992 €/ay → C61 | yok | 2026-10-12 | de:637, tr:195 |
| 15 | 670 | en | `/en/faq/anmeldung/what-is-the-difference-between-anmeldung-and-ummeldung-en` | anmeldung | Anmeldung/Ummeldung — P2 | yok | 2026-10-13 | de:671, tr:182 |
| 16 | 652 | en | `/en/faq/visa/how-long-is-a-german-student-visa-valid-en` | visa | "maks. 20 saat/hafta", "18 ay otomatik, koşulsuz (§20 Abs.3)" → C13/C22 | yok | 2026-10-13 | de:298, tr:16 |
| 17 | 650 | en | `/en/faq/visa/what-are-the-alternatives-to-fintiba-for-opening-a-blocked-account-sperrkonto-en` | visa | Sperrkonto sağlayıcıları (ticari) — P2 | var | 2026-10-12 | de:651, tr:6 |
| 18 | 654 | en | `/en/faq/language/what-is-the-difference-between-testdaf-and-dsh-en` | language | TestDaF/DSH farkı — kaynak yok (P2) | yok | 2026-10-13 | de:655, tr:20 |
| 19 | 352 | en | `/en/faq/master/how-do-masters-and-phd-programmes-in-dentistry-work-in-germany-en` | master | Diş hekimliği; ZHG linki — P2 | var | 2026-10-13 | de:353, tr:48 |
| 20 | 342 | en | `/en/faq/master/how-long-is-the-job-search-permit-valid-after-a-masters-in-germany-en` | master | 18 ay, uzatılamaz, geçim şartı (§20) → C21 | var | 2026-10-13 | de:343, tr:41 |
| 21 | 294 | en | `/en/faq/visa/what-documents-are-required-for-a-german-student-visa-application-en` | visa | Belgeler; Auslandsportal; 992/11.904 → C30/C01 | var | 2026-10-12 | de:295, tr:14 |
| 22 | 519 | en | `/en/faq/work/what-is-the-mini-job-603eur-limit-en` | work | Minijob 603 € (2026) → C17 | var | 2026-10-13 | de:520, tr:135 |
| 23 | 664 | en | `/en/faq/studienkolleg/whats-the-difference-between-studienkolleg-m-kurs-and-t-kurs-en` | studienkolleg | Studienkolleg M/T-Kurs; KMK linki → C64 | var | 2026-10-12 | de:665, tr:75 |
| 24 | 270 | en | `/en/faq/visa/how-do-i-get-a-german-student-visa-en` | visa | §16b/§16f 20 saat; Auslandsportal; 992 → C15/C30/C01 | var | 2026-10-13 | de:271, tr:1 |
| 25 | 515 | en | `/en/faq/work/what-is-a-werkstudent-and-who-can-become-one-en` | work | Werkstudent 20 saat/26 hafta; HiWi sayılmaz; 140 gün → C10/C14/C16 | yok | 2026-10-13 | de:516, tr:133 |
| 26 | 669 | en | `/en/faq/anmeldung/what-is-a-wohnungsgeberbestatigung-and-how-do-you-get-one-en` | anmeldung | Wohnungsgeberbestätigung — P2 | yok | 2026-10-12 | de:605, tr:178 |
| 27 | 308 | en | `/en/faq/language/are-there-any-recommendations-for-online-german-teachers-en` | language | Online öğretmen önerisi — P2 | yok | 2026-10-13 | de:309, tr:24 |
| 28 | 529 | en | `/en/faq/work/when-should-you-apply-for-a-werkstudent-position-en` | work | "120 full days/year" → C12 | yok | 2026-10-13 | de:530, tr:140 |
| 29 | 531 | en | `/en/faq/work/how-long-is-the-post-graduation-job-search-permit-20-valid-in-germany-en` | work | 18 ay §20 → C21 (KB: "Türk mezun 6 ay" bayat) | var | 2026-10-13 | de:532, tr:141 |
| 30 | 618 | en | `/en/faq/scholarships/how-can-you-get-a-daad-scholarship-and-what-is-the-application-process-en` | scholarships | DAAD Master 992 €/ay → C60 | var | 2026-10-13 | de:619, tr:186 |
| 31 | 517 | en | `/en/faq/work/how-many-hours-a-week-can-i-work-as-a-student-in-germany-en` | work | 140 iş günü; §16f 20 saat; Minijob 603 € → C10/C15/C17 | var | 2026-10-13 | de:518, tr:134 |
| 32 | 656 | en | `/en/faq/language/what-exams-prove-your-c1-german-level-en` | language | C1 sınavları — P2 | yok | 2026-10-12 | de:657, tr:22 |
| 33 | 338 | en | `/en/faq/master/what-is-nc-and-how-does-it-affect-your-masters-application-en` | master | NC/KMK not formülü — P2 | var | 2026-10-12 | de:339, tr:39 |
| 34 | 362 | en | `/en/faq/master/what-masters-scholarships-are-available-in-mainz-or-frankfurt-en` | master | Mainz/Frankfurt burs — P2 | yok | 2026-10-12 | de:363, tr:53 |
| 35 | 551 | en | `/en/faq/insurance/is-fintibas-insurance-package-accepted-for-a-german-visa-en` | insurance | Fintiba sigortası "Trabzon dahil tüm konsolosluklar kabul" → C39 | yok | 2026-10-13 | de:552, tr:151 |
| 36 | 356 | en | `/en/faq/master/should-you-apply-for-a-masters-in-germany-in-your-3rd-year-or-after-your-4th-en` | master | uni-assist/VPD zamanlaması — P2 | var | 2026-10-12 | de:357, tr:50 |
| 37 | 435 | en | `/en/faq/housing/what-is-the-average-rent-in-berlin-munchen-hamburg-en` | housing | Kira ortalamaları — P2 | yok | 2026-10-13 | de:436, tr:92 |
| 38 | 473 | en | `/en/faq/money/is-wise-or-revolut-suitable-for-transfers-from-turkey-to-germany-en` | money | Wise/Revolut — P2 | yok | 2026-10-12 | de:474, tr:111 |
| 39 | 280 | en | `/en/faq/visa/how-much-money-do-i-need-in-a-blocked-account-sperrkonto-en` | visa | 992/11.904; 2022–2026 tablo → C01/C02 | var | 2026-10-12 | de:281, tr:7 |
| 40 | 358 | en | `/en/faq/master/is-german-required-for-english-taught-masters-programmes-in-germany-en` | master | İngilizce Master dil şartı — P2 | var | 2026-10-12 | de:359, tr:51 |
| 41 | 541 | en | `/en/faq/work/what-happens-to-my-residence-permit-if-i-cant-find-a-job-after-my-masters-en` | work | 18 ay, uzatılamaz → C21 | var | 2026-10-13 | de:542, tr:146 |
| 42 | 1867 | en | `/en/faq/work/can-international-students-work-part-time-and-be-self-employed-in-germany-en` | work | 140 tam / 280 yarım gün; serbest meslek ayrı izin → C10 | yok | 2026-10-13 | de:1868, tr:1102 |
| 43 | 271 | de | `/de/faq/visa/how-do-i-get-a-german-student-visa-de` | visa | §16f 20 Std; Auslandsportal; 992 → C15/C30/C01 | var | 2026-10-13 | en:270, tr:1 |
| 44 | 296 | en | `/en/faq/visa/what-can-i-do-if-my-german-visa-application-is-refused-en` | visa | Remonstration 01.07.2025’te kalktı; VG Berlin → C37 | var | 2026-10-12 | de:297, tr:15 |
| 45 | 328 | en | `/en/faq/language/is-there-a-difference-between-goethe-vs-telc-certificates-for-university-en` | language | Goethe/telc — P2 | yok | 2026-10-13 | de:329, tr:34 |
| 46 | 278 | en | `/en/faq/visa/after-your-visa-application-how-long-does-it-take-on-average-to-receive-a-en` | visa | Karar 6–12 hafta; ABH 4–10 hafta → C40 | yok | 2026-10-12 | de:279, tr:5 |
| 47 | 288 | en | `/en/faq/visa/what-is-the-difference-between-fintiba-basic-and-plus-and-which-one-is-en` | visa | Fintiba Basic/Plus (ticari) — P2 | yok | 2026-10-13 | de:289, tr:11 |
| 48 | 481 | en | `/en/faq/money/if-the-sperrkonto-remains-closed-do-monthly-deductions-continue-en` | money | Kapalı Sperrkonto’da aylık 992 € aktarım → C01 (sağlayıcı kuralı: UNVERIFIABLE) | yok | 2026-10-12 | de:482, tr:115 |
| 49 | 585 | en | `/en/faq/appointments/what-are-the-alternative-ways-to-get-a-visa-appointment-if-you-cant-find-one-en` | appointments | iDATA randevu alternatifleri, "acil randevu mektubu" → C31/C40 | var | 2026-10-12 | de:586, tr:168 |
| 50 | 336 | en | `/en/faq/master/can-you-get-admitted-to-a-masters-or-bachelors-program-in-germany-with-a-low-gpa-en` | master | Düşük GPA kabul — P2 | var | 2026-10-12 | de:337, tr:38 |

## 5. OSV-1 kaynak–iddia matrisi

Kaynak kodları §3’teki tabloya atıf yapıyor (H1 = bağlayıcı/işlem makamı, H2 = kurum, H3 = genel federal rehber, H4 = tamamlayıcı). Güven: Y (yüksek) / O (orta) / D (düşük). "Yer" sütunu iddianın geçtiği kayıtları gösterir; y = yazı, f = FAQ.

### 5.1 Finansman kanıtı (P0)

| ID | İddia | Site ifadesi (kısa) / yer | Kaynak (sahip, H) | Kaynak ne diyor | Kapsam | Statü | Gerekçe | Güven | Öneri |
|---|---|---|---|---|---|---|---|---|---|
| C01 | 2026 öğrenci geçim tutarı | "992 €/ay, 11.904 €/yıl" – y153–155, 19/54/182, 20/55/56, 6, 5/35/36, 241; f463/464/106, 280/281/7, 294, 270/271; araç sperrkonto; study-in-germany | L7 (BMI, H1) + L4 + L6 | 2026 için § 16b vb. aylık bedel 992 €; BAföG 475+380+102+35 = 992 | Non-EU öğrenci, § 16b/16c/16e/16f (hazırlık amaçlı olmayan dil kursu hariç), takvim yılı 2026 | SUPPORTED | Bağlayıcı ilan ve kanun hesabı birebir aynı. Yıllık rakam 12 aylık kalış varsayımına dayanıyor | Y | Kaynak olarak L7 + L4 gösterilsin. 2027 ilanı kontrol edilsin (§10) |
| C02 | Yürürlük tarihi | "01.09.2024’ten beri (öncesi 11.208/934)" – y153–155; f463/106/280; FAQ 7/280/281’deki 2022–2026 tablosu | Alınan kaynaklarda yok | L7 yalnız 2026’yı ilan ediyor | — | UNVERIFIABLE | 2024 başlangıç tarihi ve eski rakamlar için 2024/2025 BAnz ilanları ya da BAföG değişiklik kanunu okunmadı | O | Eski yılların ilanları eklensin ya da cümle "2026 için" diye sadeleşsin |
| C03 | Zaman çerçevesi | "As of 2024, expected to remain valid for 2025" – y54/19/182; y6 başlığı "Sperrkonto 2025" | L7 | 2026 tutarı ilan edilmiş | 2026 | OUTDATED | Rakam doğru, ama 2026’da "2025 için beklenen" ifadesi eskimiş ve güven kaybettiriyor | Y | Metin "2026: 992 €/ay (BMI ilanı)" diye güncellensin. y6 için başlık/meta güncellemesi (slug değişmeden) |
| C04 | Dil kursu tutarı | "6 × 992 € = 5.952 €" – y20/55/56 | M4 (Türkiye temsilciliği, H1) + L4 | "monatlich mindestens 1091 EUR"; § 2 Abs. 3 hazırlık amaçlı olmayan dil kursunu BAföG bedelinin dışında tutuyor | Türkiye, § 16f dil kursu | CONFLICT | Site öğrenci bedelini dil kursuna uyguluyor; resmî tutar daha yüksek | Y | 1.091 €/ay + M4 linki; § 16b şartlı kabul + dil kursu ile § 16f ayrımı yapılsın. FAQ 463/106 bunu zaten doğru yazıyor; onunla hizalansın |
| C05 | Sperrkonto zorunluluğu | "Dil kursu için de Sperrkonto zorunludur" – y20/55/56 | M4, M3, M8 | Sperrkonto ve Verpflichtungserklärung "gleichberechtigt" | Türkiye | CONFLICT | Alternatif yol varken "zorunlu" deniyor | Y | "Sperrkonto veya Verpflichtungserklärung" |
| C07 | Kaynak gösterimi | Sperrkonto aracı rakam için "Auswärtiges Amt (Merkblatt)" linki veriyor (2621556) | M5 | Merkblatt 25’te tutar yok | — | SCOPE_MISMATCH | Rakam doğru, kanıt yanlış sayfa. "URL var" ≠ destek | Y | Kaynak notu L7 (+ M1/M2) olarak değişsin, `updated` tarihi yenilensin |
| C08 | Rakamın sahibi | "Officially, DAAD estimates around €992/month … Sperrkonto requires exactly this amount" – `PageFaq.php:322` | L4, L7 | Bedeli BMI ilan ediyor (BAföG bazlı) | — | NEEDS_REVIEW | Yanlış kurum atfı; DAAD burs miktarı ayrı bir konu | O | Atıf BMI/BAföG olarak düzeltilsin |

### 5.2 Öğrenci çalışma hakkı (P0)

| ID | İddia | Site ifadesi / yer | Kaynak | Kaynak ne diyor | Kapsam | Statü | Gerekçe | Güven | Öneri |
|---|---|---|---|---|---|---|---|---|---|
| C10 | Yıllık sınır ve sayım | "140 iş günü (Arbeitstagekonto), ≤4 saat yarım gün, ders döneminde ≤20 saat = 2,5 gün" – y25/65/66, 14, 211–213, 150–152, 755–757; f517/518, 515, 1867 ("140 full/280 half") | L1 § 16b(3) (H1) | Aynı kurallar; ders dışında her hafta 2,5 gün sayılıyor | § 16b oturum izni | SUPPORTED | Metinle birebir. "280 yarım gün" eşdeğer ifade | Y | Kaynak linki L1 olarak eklensin (gövdede yok) |
| C11 | Yürürlük | "01.03.2024’ten beri / 120’den 140’a" – y25/65, 150–152, f517 | L1 (yalnız güncel metin) | Değişiklik tarihi metinde yok | — | NEEDS_REVIEW | Arbeitstagekonto ve 20 saat/2,5 gün sayımının hangi değişiklikle geldiği doğrulanmadı | O | BGBl değişiklik kaydı (gesetze-im-internet "Stand" notu/BGBl) eklenip tarih teyit edilsin |
| C12 | Eski sınır güncel gibi | "120 days", "120 full days/year", "120 volle oder 240 halbe Tage" – f525/526 (+tr138), f529 (+530/140), f298 (DE) | L1 | 140 Arbeitstage | § 16b | OUTDATED | Güncel kanunla çelişen eski rakam | Y | 140 + Arbeitstagekonto; EN/TR kardeşleriyle hizalanmalı |
| C13 | Haftalık tavan | "Yıllık en fazla 20 saat/hafta (yoksa vize sorununa girer)" – f652/16/298 | L1 | Oturum hukukunda haftalık tavan yok; 20 saat yalnız ders döneminde sayım yöntemi | § 16b | CONFLICT | Aynı FAQ içinde 140 gün kuralıyla da çelişiyor | Y | FAQ 517 cümlesiyle değiştirilsin |
| C14 | Üniversitede yan iş | "HiWi 140 güne sayılmaz" – y755–757, f515 | L1 | "Studentische Nebentätigkeiten werden nicht angerechnet" | § 16b | SUPPORTED | Kanun "öğrenci yan işi" diyor; HiWi tipik örnek | Y | Kavram adı (studentische Nebentätigkeit) yanına eklenebilir |
| C15 | Dil kursu izni | "§ 16f … bis zu 20 Stunden pro Woche" – f517, 270/271 | L5 § 16f | "bis zu 20 Stunden je Woche" | § 16f dil kursu | SUPPORTED | Birebir | Y | — |
| C16 | Werkstudent sosyal sigorta | "20 saat; 26 hafta istisnası" – f515, 535, y25 | DRV/TK okunmadı | — | Sosyal sigorta | UNVERIFIABLE | Bu turda kaynak alınmadı | O | DRV / Minijob-Zentrale Werkstudent sayfası eklensin |
| C17 | Minijob sınırı | "603 € (2026)" – f519, 517, 535 | U6 (H1 kurum) | Sayfada "Verdienstgrenze von 603 Euro" | Minijob | SUPPORTED | Rakam güncel sayfada; yıl ibaresi sayfada yok | O | Minijob-Zentrale ana sayfası ve yürürlük tarihi kaynak notuna eklensin |
| C18 | Kazanç tahmini | "average annual earnings ~10,500 €" – y150–152 | Kaynak: gradgermany (ikincil) | — | — | UNVERIFIABLE | Resmî dayanak yok | D | Çıkarılsın ya da "örnek hesap" diye etiketlensin |

### 5.3 Mezuniyet sonrası oturum ve iş (P0)

| ID | İddia | Site ifadesi / yer | Kaynak | Kaynak ne diyor | Kapsam | Statü | Gerekçe | Güven | Öneri |
|---|---|---|---|---|---|---|---|---|---|
| C20 | Yurt dışı mezun iş arama | "Job-Seeker Visa … up to 6 months … does NOT allow you to work" – y71/72/73 | L2, L3 | § 20 artık yalnız Almanya’daki eğitim/araştırma/meslek sonrası; yurt dışından iş arama § 20a Chancenkarte: 1 yıla kadar, ortalama ≤20 saat/hafta + 2 haftalık deneme çalışması | Non-EU, yurt dışı mezunu | CONFLICT | Yazı yürürlükte olmayan bir düzeni anlatıyor | Y | Yeniden yazılsın: Chancenkarte (§ 20a) + puan/Fachkraft şartları; Almanya mezunları için § 20 (18 ay) ayrı. Lebensunterhalt tutarı ayrı doğrulanmalı (Chancenkarte için 992 € varsayılmamalı) |
| C21 | 18 ay | "up to 18 months, cannot be extended, secured livelihood" – f342, 531, 541; y316; study-in-germany "18 ay" | L2 (H1) | "bis zu 18 Monaten"; geçim şartı; bu sürelerin ötesine uzatma hariç | Almanya’da tamamlanan öğrenim | SUPPORTED | Birebir. study-in-germany başlığında kapsam (Almanya mezunu) belirtilmeli | Y | Landing’e kapsam notu |
| C22 | Koşulsuz/otomatik | "18 aylık iş arama vizesi (§ 20 Abs. 3) — koşulsuz", "Automatic right" – f652/16/298 | L2 | Abs. 2: geçim şartı var; Abs. 3 güncel metinde yok | Almanya mezunu | CONFLICT | Hem koşul hem paragraf yanlış | Y | "§ 20 Abs. 1 Nr. 1, geçim şartıyla, en fazla 18 ay" |
| C23 | Arama süresince çalışma | "graduates may work without restriction during these 18 months" – y316; y394 | L5 § 4a(1) + L2 | Oturum izni sahibi, kanun yasaklamadıkça çalışabilir; § 20’de kısıt yok | § 20 | SUPPORTED | Genel kuraldan çıkarım; Make it in Germany teyidi okunamadı | O | § 4a + § 20 linki eklensin |
| C24 | Blue Card 2026 eşikleri | "€50,700 / €45,934.20" – y308–310 | L8 (BMI, H1) | 50.700 € / 45.934,20 € | Takvim yılı 2026 | SUPPORTED | Birebir | Y | Kaynak linki L8 |
| C25 | Blue Card 45.300 € | "≥ 45,300 € = automatic Blue Card" – y4/33/34 (~1.400 görüntülenme); KB’de f41/44/342/343/660/661, y120/130/131, 323–328 | L8, L5 § 18g | 2026 genel eşik 50.700 €; 45.300 € 2024 rakamı (y391 bunu 2024 diye doğru veriyor) | 2026 | OUTDATED | Eski yıl rakamı. "Otomatik" ifadesi de yanlış (diploma, iş ve diğer şartlar gerekli) | Y | 50.700 / 45.934,20 + § 18g şartları |
| C26 | Yerleşim izni süreleri | "Blue Card 27 ay, B1 ile 21 ay" – y389/391, 392–394, 414–416 | L5 § 18c(2), U7 | 27 ay; yeterli Almanca ile 21 ay | Blue Card | SUPPORTED | Birebir | Y | — |

### 5.4 Vize süreci (P0)

| ID | İddia | Site ifadesi / yer | Kaynak | Kaynak ne diyor | Kapsam | Statü | Gerekçe | Güven | Öneri |
|---|---|---|---|---|---|---|---|---|---|
| C30 | Öğrenim vizesi kanalı | "Türkiye’de öğrenim ve öğrenim yeri arama vizeleri yalnız Auslandsportal üzerinden (Eylül 2026)" – f270/271, 294, 296 | M2 (H1) | "ausschließlich über das Auslandsportal" | Türkiye, Studium + Studienplatzsuche | SUPPORTED | Birebir | Y | Diğer içerikler bu cümleyle hizalansın |
| C31 | iDATA tek kanal | "iData is the only center authorized…"; "tek resmi platform iDATA"; "iDATA zorunlu" – araç visa-appointment; y7/39/178; y93/92/91; kısmen y232/245/246 | M1, M2 | Öğrenim vizesi Auslandsportal’dan; temsilcilikler iDATA **ve IOM-FAP** ile çalışıyor; iDATA bazen Auslandsportal’a yönlendiriyor | Türkiye | CONFLICT | Öğrenci kitlesi için yanlış kanal ve eksik hizmet sağlayıcı bilgisi | Y | Ayrım yapılsın: öğrenim = Auslandsportal; diğer ulusal vizeler = iDATA randevu sistemi / Auslandsportal |
| C32 | Randevu ücretsiz | "Randevu … ücretsizdir" / "kostenlos" / "free of charge" – y5/36/35 | M1 | iDATA hizmet ücreti 39,92 € ve/veya randevu ücreti 12 € | Türkiye | CONFLICT | Resmî ücretle doğrudan çelişiyor | Y | M1’deki ücret cümlesi (TL ödeme notu ile) |
| C33 | iDATA ücreti 32,81 € | "pay the €32.81 iData fee within 48 hours" – araç visa-appointment ("verified 2026-06-04") | M1 | 39,92 € (+ 12 €) | Türkiye | OUTDATED | Doğrulama tarihinden sonra değişmiş | Y | Rakam ve "verified" tarihi güncellensin |
| C34 | 75 € online ücret | "Pay the €75 fee online (visa fee is separate)" – y92 (EN; TR/DE kardeşleri kontrol edilmeli) | M1 | iDATA ücretleri 39,92 € / 12 € | Türkiye | CONFLICT | Ücret ve sahibi yanlış | O | M1 ile hizalansın |
| C35 | VFS Global | "`vfsglobal.com` veya…" (TR/DE); "iDATA for some missions, VFS Global for others" (EN) – y5/36/35 | M1 | "ausschließlich mit … iDATA und IOM-FAP" | Türkiye | CONFLICT | Türkiye’deki Alman temsilcilikleri VFS ile çalışmıyor. Üç dil kendi içinde de tutarsız | Y | VFS ifadesi kaldırılsın |
| C36 | Vize ücreti | "Vize ücreti 75 €" – y5/35/36; araç visa-appointment | M4 (+ Merkblatt 34) | "Visumgebühr: i.d.R. 75,00 Euro" | Türkiye ulusal vize (dil kursu/okul broşürlerinde) | SUPPORTED | Ücret ulusal vize için aynı; öğrenim kategorisinin portal listesi okunamadı | O | Kaynak + "kural olarak" ibaresi |
| C37 | Remonstration kalktı | "01.07.2025’te kaldırıldı; yeni başvuru veya VG Berlin" – y5/35/36, 94–96, 350–352, 732–734; f15/296/297 | M6 (H1) | Aynı; dava süresi 1 ay | Türkiye, tüm vizeler | SUPPORTED | Birebir | Y | — |
| C38 | Remonstration hakkı | "30 gün içinde itiraz (Remonstration) hakkı" – f163/575, 164/578, 170/589/590, 576; "the German mission usually offers a remonstration" – f1159/1160 (13.10 batch1) | M6 | 01.07.2025’ten beri remonstration hukuki etki doğurmuyor | Türkiye | OUTDATED | Kalkmış bir hak öneriliyor; 1159/1160 son pakette eklendi | Y | f296/15 cümlesiyle değiştirilsin; batch1 JSON’daki metin de düzeltilsin (ileride) |
| C39 | Temsilcilik listesi | "consulates in Istanbul, Ankara, Izmir, Antalya, **Trabzon**" – f551 | M1–M6 alt bilgisi | Temsilcilikler: Ankara Büyükelçiliği, İstanbul ve İzmir Başkonsoloslukları, Antalya Konsolosluğu | Türkiye | CONFLICT | Trabzon’da Alman temsilciliği yok. "Fintiba sigortası her yerde kabul" iddiası da kaynaksız | O | Trabzon çıkarılsın; kabul ifadesi temsilciliğin kontrol listesine yönlendirilsin |
| C40 | Süreler | "randevu 4–12 hafta, karar 6–12 hafta, ABH 4–10 hafta", "mülakat 5–15 dk" – y5/35/36, 93, 221; f278 | Resmî süre yayımı bulunamadı | — | — | UNVERIFIABLE | Topluluk deneyimi; resmî kaynak yok | O | "Topluluk deneyimi" diye etiketlensin, kesin ifade yumuşatılsın |
| C41 | Portal istatistiği | "167 visa offices … 4–6 weeks" – y147–149 | expatrio (ikincil) | — | — | UNVERIFIABLE | AA basın kaynağı okunmadı | D | AA basın açıklaması bulunsun ya da rakamlar çıkarılsın |

### 5.5 Kabul, tanınma, ücretler (P0/P1)

| ID | İddia | Site ifadesi / yer | Kaynak | Kaynak ne diyor | Kapsam | Statü | Gerekçe | Güven | Öneri |
|---|---|---|---|---|---|---|---|---|---|
| C50 | APS Türkiye | "for the vast majority of Turkish students … APS … mandatory" – y97/98/99 | U2 (uni-assist, H4), U3 (TUM, H2) | APS yalnız Çin, Vietnam, Hindistan; TUM: yalnız bu ülkelerin belgeleriyle başvuranlar | Türkiye diplomaları | CONFLICT | Başvuruyu doğrudan yanlış yönlendiriyor | Y | Yazı geri çekilsin ya da "Türkiye için APS yok" diye yeniden yazılsın; ZAB/anabin/uni-assist VPD yazılarına yönlendirilsin. Teyit için AA/DAAD APS sayfası eklensin |
| C51 | uni-assist ücreti | "İlk başvuruda 75 €, sonraki üni başına 30 €" – y4/33/34 | U1 (H4) | Dönem başına ilk **tercih** 75 €, her ek tercih 30 €; VPD dahil; bazı üniversiteler ödüyor | uni-assist | SUPPORTED | Rakamlar doğru; "üni başına" yerine "tercih (program) başına" daha doğru | Y | İfade inceltilsin + U1 linki |
| C52 | Genel son tarihler | "kış 15 Temmuz, yaz 15 Ocak" – yalnız y4 (TR); EN/DE’de yok | Üniversite sayfaları (alınmadı) | — | Üniversite/program bazlı | SCOPE_MISMATCH | Üniversiteye özel tarih genel kural gibi veriliyor. TR/EN/DE de tutarsız | O | "Sık görülen; programa göre değişir" + `/tools/deadlines` (Haziran’dan beri güncellenmedi, SEO denetimi) |
| C53 | Hazırlık süresi | "§ 16b Abs. 5 … generally one year and should not exceed two years" – y241/230/242 | L1 | Abs. 5’te böyle bir süre yok | § 16b | NEEDS_REVIEW | Atıf yanlış; kural büyük olasılıkla idari talimattan (AVwV) geliyor, okunmadı | O | Doğru kaynak bulunana kadar kesin süre ifadesi ve paragraf atfı kaldırılsın |
| C54 | Şartlı kabul vizesi | "student applicant / language course visa (under § 16b)" – y241 | L1 (§ 16b(1),(5)); § 17 okunmadı | Şartlı kabul + dil kursu § 16b(1)/(5); "Studienbewerber" § 17 | — | NEEDS_REVIEW | Kategoriler karışmış | O | § 16b(1) hazırlık önlemi / § 16b(5) / § 17 ayrımı |
| C55 | ZAB ücreti | "yaklaşık 200 Euro" (y87 TR) | FAQ batch2 (13.10, zab.kmk.org: 208 €, 3 ay); bu turda yeniden okunmadı | — | ZAB | NEEDS_REVIEW | Blog ile FAQ arasında rakam farkı (≈200 € / 208 €); TR/EN/DE de farklı | O | FAQ batch2 metniyle hizalansın (208 €, süre koşulu) |
| C56 | Harç | "Yalnız BW harç alıyor; Bayern harçsız; BW’de Master genelde ücretsiz" – f511 (+130/667) | U4 (TUM, H2), U5 (Heidelberg, H2) | TUM WS 2024/25’ten beri non-EU’dan 2.000–6.000 €/dönem; Heidelberg konsekütif Master’da da 1.500 € | Non-EU | CONFLICT | İki ayrı resmî kurum sayfasıyla çelişiyor | Y | Bavyera (kurum bazlı) eklensin; BW Master düzeltilsin; eyalet yasası kaynağı eklensin |
| C57 | BW tutarı | "1.500 €/dönem; ikinci öğrenim 650 €" – f511 | U5 | 1.500 € (650 € okunmadı) | BW | SUPPORTED | 1.500 € doğrulandı; 650 € kurum sayfasında görülmedi | O | MWK/LHGebG kaynağı eklensin |
| C58 | Harçsız genelleme | "do not charge tuition fees for anyone" – y166/165/167 | U4, U5 | BW ve bazı Bavyera kurumları non-EU’dan harç alıyor | Non-EU | SCOPE_MISMATCH | "Hiç kimseden" ifadesi istisnaları yok sayıyor; kaynak Google News | Y | İstisna cümlesi + resmî kaynak; `source_url` resmî olmayan haber adresi |

### 5.6 Diğer ve KB

| ID | İddia | Yer | Kaynak | Statü | Gerekçe | Güven | Öneri |
|---|---|---|---|---|---|---|---|
| C60 | DAAD Master bursu 992 €/ay | f618/619, 626/627; glossary | DAAD (okunmadı) | UNVERIFIABLE | Kaynak alınmadı | D | DAAD burs veritabanı sayfası eklensin |
| C61 | DAAD bi-nasyonel doktora 992 € | f636/637 | DAAD (okunmadı) | UNVERIFIABLE | Kaynak alınmadı | D | DAAD sayfası |
| C62 | Deutschlandticket 63 € (Ocak 2026) | y199/200/201 | Okunmadı (13.10’da KVB kaynağıyla düzeltilmiş, GSC dokümanı) | UNVERIFIABLE | Bu turda doğrulanmadı | O | Kaynak notu metne eklensin |
| C63 | Sigorta fiyatı / özel sigorta kabulü | y100/101/102 | GKV-Spitzenverband / temsilcilik listesi okunmadı | UNVERIFIABLE | — | D | Vize kabulü için temsilcilik listesi kaynak olsun |
| C64 | Studienkolleg / doğrudan giriş (YKS) | y83/82/84, 184–186 | anabin/KMK okunmadı | UNVERIFIABLE | Kabul kuralları bu turda doğrulanmadı | O | anabin + KMK Beschluss + uni-assist kaynakları |
| C70 | KB tutarlılığı | Yerel `kb_chunks`: FAQ/yazı chunk’ları 29.06–01.07.2026’da gömülmüş. 1.137 FAQ ve 285 yazı chunk’tan yeni; 806 FAQ ve 487 yazının chunk’ı yok. 50 kayıt KB’de "120/240 gün" taşıyor (DB metni düzeltilmiş). 15 kayıt eski Blue Card rakamı (45.300 / 41.041). 6 kayıt "6 ay" (ör. f531 KB: "Türk mezunları 6 ay"). Remonstration 30 gün 8 kayıtta hem DB hem KB’de | Kanun metinleri (L1, L2, L8) | OUTDATED | Chatbot düzeltilmiş içeriğe rağmen eski iddiayı verebilir | O (yalnız yerel) | Prod KB yaşı ölçülsün; onarım partilerinden sonra kaynak bazlı yeniden gömme (onaylı) |

**TR/EN/DE kardeş tutarsızlıkları:**
- FAQ 298 (DE) "120/240 Tage", FAQ 652 (EN) ve FAQ 16 (TR) "140 gün" diyor.
- Yazı 5 (TR) ve 36 (DE): iDATA + `vfsglobal.com` + ücretsiz randevu. Yazı 35 (EN): "iDATA bazı temsilciliklerde, VFS diğerlerinde".
- Yazı 4 (TR) 15 Temmuz/15 Ocak son tarihlerini veriyor; 33/34’te bu cümle yok.
- ZAB ücreti: blog ≈200 €, FAQ batch2 208 €.

**FAQ ile KB tutarsızlıkları:** Yukarıdaki C70 satırı. DB’de düzeltilmiş kayıtlar (16, 133, 134, 342, 517, 531 …) KB’de hâlâ eski haliyle duruyor.

## 6. Statü özeti

| Statü | Sayı | Satırlar |
|---|---|---|
| SUPPORTED | 14 | C01, C10, C14, C15, C17, C21, C23, C24, C26, C30, C36, C37, C51, C57 |
| CONFLICT | 12 | C04, C05, C13, C20, C22, C31, C32, C34, C35, C39, C50, C56 |
| OUTDATED | 6 | C03, C12, C25, C33, C38, C70 |
| UNVERIFIABLE | 10 | C02, C16, C18, C40, C41, C60, C61, C62, C63, C64 |
| SCOPE_MISMATCH | 3 | C07, C52, C58 |
| NEEDS_REVIEW | 5 | C08, C11, C53, C54, C55 |
| **Toplam** | **50** | |

Kapsam ölçümü (tasarım dokümanındaki KPI):
- Matristeki P0 satırlarından resmî kaynakla doğrulananlar: 13 / 41 (C56–C58 P1, C60–C64 ve C70 P1/P2 olarak sayıldı).
- İlk 50 FAQ’ta resmî link oranı 22/50. Ancak bunların bir kısmı iddiayı desteklemeyen sayfaya bağlanıyor (C07 türü).

## 7. Öncelikli 10 onarım adayı

| # | Öncelik | Aday (kayıtlar) | Kanıt | Önerilen düzeltme (uygulanmadı) |
|---|---|---|---|---|
| 1 | P0 – kabul | APS yazısı y97/98/99 (≈317 görüntülenme) | C50; U2, U3 | Türkiye için APS olmadığını söyleyen kısa bir düzeltme notu; asıl yolu (anabin, ZAB, uni-assist/VPD) anlatan yazılara 301 değil iç link. Slug korunur. Başlık/meta: "Türk öğrenciler için APS gerekmez". Üç dil birlikte |
| 2 | P0 – oturum | İş arama vizesi y71/72/73 (≈691) | C20; L2, L3 | Chancenkarte (§ 20a) anlatımı: 1 yıl, 20 saat/hafta, deneme çalışması, Fachkraft ya da puan şartı. Almanya mezunları için § 20 ayrı bölüm. "6 × 992" geçim hesabı kaldırılsın; Chancenkarte tutarı ayrıca doğrulanmalı |
| 3 | P0 – vize | Araç `/tools/visa-appointment` (TR çekirdek sayfa) | C31, C33; M1, M2 | "Öğrenim vizesi: yalnız Auslandsportal" kutusu; iDATA ücreti 39,92 € (+12 €), TL notu; "only iData" ifadesi kaldırılsın, IOM-FAP eklensin; İzmir/Antalya temsilcilikleri; `verified` tarihi + kaynak notu M1/M2 |
| 4 | P0 – vize | Randevu yazıları y5/35/36, y7/39/178, y93/92/91 (≈1.840) | C31, C32, C34, C35 | Ücretsiz/VFS/75 € online ifadeleri kaldırılsın; iDATA ve Auslandsportal rolleri M1/M2’ye göre; üç dilde aynı içerik |
| 5 | P0 – vize | Remonstration FAQ’ları 163/575/(TR-EN), 164/578, 170/589/590, 576; batch1 1159/1160 | C38; M6 | Ret sonrası bölümü FAQ 296/15 metniyle değiştirilsin (yeni başvuru veya 1 ay içinde VG Berlin). batch1 JSON metni de düzeltilsin, yoksa ileride yeniden uygulanınca hata geri gelir |
| 6 | P0 – çalışma/oturum | FAQ 652/16/298 "vize kaç yıl geçerli" (≈380) | C12, C13, C22; L1, L2 | Çalışma bölümü FAQ 517 ile hizalansın (140 gün, haftalık tavan yok); "§ 20 Abs. 1 Nr. 1, geçim şartı, en fazla 18 ay"; "otomatik/koşulsuz" çıkarılsın; DE’deki 120/240 düzeltilsin |
| 7 | P0 – finansman | Sperrkonto kullanım yazısı y20/55/56 (≈1.007) | C04, C05; M4, L4 | Dil kursu 1.091 €/ay; Sperrkonto veya Verpflichtungserklärung; § 16b şartlı kabul ile § 16f dil kursu ayrımı; kaynak M4 + L7 |
| 8 | P0 – çalışma | "120 gün" FAQ’ları 525/526/138 (LinkedIn, ≈1.100), 529/530/140; KB’de 50 kayıt | C12, C70; L1 | Cümle "140 iş günü (Arbeitstagekonto)" olsun. Onarım partisinden sonra ilgili kayıtlar KB’ye yeniden gömülsün (ayrı onay) |
| 9 | P0/P1 – oturum | Master A-Z y4/33/34 (≈1.400, P1 yüksek lisans şartları sayfası) | C25, C52, C51; L8, U1 | Blue Card "50.700 € / 45.934,20 € (2026), şartlar § 18g"; "otomatik" kaldırılsın; TR’deki genel son tarih "programa göre" diye yumuşatılsın; uni-assist "tercih başına" + U1 linki |
| 10 | P1 – ücretler | Harç FAQ 511/130/667 (≈260+) ve y165/166/167 (≈1.400) | C56, C58; U4, U5 | Bavyera’da kurum bazlı non-EU harcı (TUM örneği), BW’de konsekütif Master’da da 1.500 €; haber yazısına istisna cümlesi ve resmî kaynak |

Sonraki adaylar (P1/P2):
- Sperrkonto aracındaki kaynak gösterimi (C07).
- y54/19/182 ve y6’daki "2025" çerçevesi (C03).
- FAQ 551 Trabzon (C39).
- `/tools/visa-cost` "2025 figures".
- `PageFaq.php` DAAD atfı (C08).
- y241 § 16b Abs. 5 atfı (C53/C54).
- ZAB ücreti farkı (C55).

## 8. Gerekli birincil kaynaklar (konu bazında)

| Konu | Birincil (bağlayıcı / işlem makamı) | Kurum / tamamlayıcı | Durum |
|---|---|---|---|
| Geçim kanıtı tutarı | BMI Bekanntmachung § 2 Abs. 3 AufenthG (yıllık, BAnz; 2026: AT 29.08.2025 B1); AufenthG § 2 Abs. 3; BAföG §§ 13, 13a | Türkiye temsilcilikleri Merkblatt 44F (dil kursu), Auslandsportal kategori listesi | 2026 doğrulandı; **2027 ilanı bulunamadı** |
| Finansman yolları | AA FAQ Finanzierung; temsilcilik Bildungsaufenthalte sayfası | Sperrkonto sağlayıcıları (yalnız ürün bilgisi) | Doğrulandı |
| Öğrenci çalışması | AufenthG § 16b Abs. 3, § 16f | Minijob-Zentrale (603 €), DRV (Werkstudentenprivileg), Bundesagentur für Arbeit | § 16b/16f doğrulandı; DRV alınmadı |
| Mezuniyet sonrası | AufenthG § 20, § 20a, § 4a, § 18b, § 18c, § 18g; BMI Blue Card ilanı (BAnz AT 18.12.2025 B3) | BAMF Blue Card, Make it in Germany | Doğrulandı (MiiG hariç) |
| Vize kanalı ve ücret | tuerkei.diplo.de: Nationale Visa, Studium und Studienplatzsuche, Merkblätter; Auslandsportal | iDATA (yalnız ücret/randevu teyidi) | Doğrulandı; Auslandsportal içeriği okunamadı |
| Ret sonrası | tuerkei.diplo.de "Was tun nach Ablehnung" | VG Berlin | Doğrulandı |
| APS | AA / ilgili ülkelerdeki APS siteleri | uni-assist sözlüğü, üniversite sayfaları | uni-assist + TUM ile doğrulandı; AA APS sayfası eklenmeli |
| Tanınma/denklik | anabin, ZAB (zab.kmk.org), KMK kararları | uni-assist VPD | Bu turda alınmadı (FAQ batch2 13.10’da ZAB’ı kontrol etmiş) |
| Harç | Eyalet yükseköğretim harç yasaları (BW LHGebG, Bavyera BayHIG) | Üniversite ücret sayfaları (TUM, Heidelberg) | Kurum sayfaları doğrulandı; eyalet yasası alınmadı |
| Başvuru tarihleri | Program/üniversite sayfaları; Hochschulstart (NC/DoSV) | Hochschulkompass, DAAD veri tabanı | Alınmadı (program bazlı; `program_verifications.deadline`) |
| Burslar | DAAD burs veri tabanı/ilanları | — | Alınmadı |
| Ulaşım | VDV/Deutschlandticket resmî sayfası; Studierendenwerk/üniversite Semesterbeitrag | — | Alınmadı |
| Genel çerçeve | Make it in Germany (4 sayfa) | — | **Erişilemedi (bot koruması)** |

## 9. OSV-2 için veri modeli notları (migration yok)

- **`program_verifications` yeniden kullanılabilir ama program’a bağlı.** Mevcut alanlar: `field`, `applicant_group`, `term`, `status`, `source_value`, `source_url`, `evidence`, `checked_at`, `verified_at`, `verified_by`, `verified_via`, `review_reason`, `program_value_fingerprint`. Bunlar tasarımdaki mantıksal alanlarla örtüşüyor: `claim_key`, `claim_scope`, `effective_*`, `checked_at`, `verified_by`, `evidence_note`.
  - Statü kümesi dar: `unverified/verified/needs_review/conflict`. `OUTDATED`, `UNVERIFIABLE`, `SCOPE_MISMATCH` karşılığı yok.
  - `program_id` zorunlu olduğu için yazı/FAQ/araç iddiaları için doğrudan kullanılamaz.
  - Yerelde 124 kayıt var: verified 65, needs_review 53, conflict 6.
  - Fingerprint ile "kaynak doğrulandıktan sonra içerik değişti" tespiti iyi bir desen; iddia kaydına da taşınmalı.
- **Editoryal kilit:** `University` ve `City` `content_blocks` içinde blok bazlı `editorial_lock: true` ve `verified_at` taşıyor (`University.php:125–140`, `City.php:107–115`; 10.10 migration’ları). Yazı ve FAQ modellerinde bu alanların karşılığı yok.
- **Migration’sız ara çözüm önerisi:**
  1. **Merkezi iddia kaydı.** Yeni bir config ya da `database/data/claims/*.json`; mevcut `faq-en` ve `university-content` paketlerindeki gibi her öğe kaynak ve `checked` tarihi taşır. P0 rakamları burada tutulur: 992 / 11.904 / 1.091 / 140 / 18 ay / 50.700 / 45.934,20 / 603 / iDATA 39,92 + 12 / vize 75 / uni-assist 75 + 30. Her rakam için `source_url`, `source_owner`, `claim_scope`, `effective_from/to`, `checked_at`, `next_review_at` tutulur. Bugün 11.904 en az 10 yerde sabit yazılmış (§4.1 ek gözlem); şablonlar (`ToolsController`, `PageFaq`, `glossary`, `ApplicationTracker`, araç görünümleri) bu kayıttan okursa rakamlar birbirinden kopamaz.
  2. **Kaynak notu bileşeni.** Mevcut `x-source-note` (`sources`, `updated`) araç sayfalarında kullanılıyor. P1 rehberlerine ve FAQ şablonuna da eklenmeli. `updated` değeri sabit tarih yerine kayıttaki `checked_at` olmalı.
  3. **Yazı/FAQ iddia eşlemesi.** `content_md` içinde regex ile bulunan P0 kalıpları (bu raporda kullanılan kalıplar) bir denetim komutuyla iddia kaydına eşlenir. Fark çıkarsa review kuyruğuna düşer (OSV-4). Otomatik düzeltme yapılmaz.
  4. **KB senkronu.** `kb_chunks.content_hash` ve `embedded_at` mevcut. Kaynak kayıt `updated_at > embedded_at` ise "bayat" sayılsın; bu sayı izlensin (yerelde 1.137 FAQ / 285 yazı).
- **İleride migration gerekirse:** Polimorfik bir `content_claims` tablosu (`related_type/related_id` + tasarımdaki alanlar). Program dışı iddialar `program_verifications`’a zorlanmamalı. Statü enum’u tasarımdaki altı statüye genişletilmeli.
- **`posts.source_url`** bugün haber yazılarının dış kaynağı (Google News, ajans) için kullanılıyor. Resmî doğrulama kaynağı olarak yeniden kullanılmamalı, çünkü anlamı karışır.

## 10. Açık sorular ve doğrulanamayanlar

1. **Make it in Germany (S1–S4) okunamadı.** Radware doğrulaması WebFetch ve düz GET’i engelledi. Tasarımdaki eşleme hedefi (MiiG ↔ site) bu yüzden kanun ve temsilcilik kaynaklarıyla yapıldı. Tarayıcıdan elle okuma ya da `/print` sürümü denenebilir; kullanım şartları ayrıca incelenmeli.
2. **2027 geçim tutarı.** § 2 Abs. 3’e göre BMI 31.08.2026’ya kadar ilan etmeli; aramada 2027 ilanı bulunamadı. WS 2026/27 BAföG değişikliği olup olmadığı da doğrulanmadı (gesetze-im-internet 14.10.2026 itibarıyla 475/380 gösteriyor). Ocak 2027 öncesi yeniden kontrol gerekli.
3. **Auslandsportal öğrenim kontrol listesi** (sigorta, tutar, belge listesi) JS uygulaması olduğu için okunamadı. FAQ 463/106’daki "temsilciliklerin bilgi notunda 11.904 €" cümlesinin dayanağı bulunamadı.
4. **Arbeitstagekonto’nun yürürlük tarihi** (01.03.2024 mü, daha sonraki bir değişiklik mi): güncel metin doğrulandı, değişiklik tarihi doğrulanmadı.
5. **Chancenkarte geçim tutarı** ve § 20a(4) uygulaması (BMI ayrı ilan ediyor mu): okunmadı.
6. **DAAD burs miktarları, ZAB ücreti, Deutschlandticket fiyatı, DRV Werkstudent kuralları, BW LHGebG ikinci öğrenim ücreti** bu turda alınmadı (UNVERIFIABLE). İlgili kaynak sayfaları 404 verdi ya da kapsam dışı kaldı.
7. **Prod–yerel farkı.** Analiz anonim yerel baseline üzerinde yapıldı. Prod’da id’ler ve kardeş grupları farklı olabilir; onarım paketleri id yerine slug + mevcut metinle eşleşmeli (batch1/2 deseni). Prod KB’nin gömülme tarihi bilinmiyor.
8. **13.10 FAQ paketlerinin prod’daki durumu — yanıtlandı.** 14.10.2026'da canlı sayfalar okundu: `/en/faq/appointments/what-can-i-do-about-communication-problems-with-officials-at-visa-interviews-en` ve `-de` kardeşi remonstration cümlesini gösteriyor. Onarım paketi bu canlı metni hedeflemeli. Ayrıca batch1/batch2 JSON metinleri düzeltilmeli, yoksa ileride yeniden uygulanınca hata geri gelir.
9. **`view_count` güvenilirliği.** Anonim kopyadaki sayaçlar sıralama için kullanıldı. GSC gösterim verisi FAQ bazında birleştirilmedi.
10. **Kaynak hiyerarşisi kararı.** Üniversite sayfaları (TUM, Heidelberg) H2 olarak eyalet geneli iddiaları (C56/C57) destekliyor ama eyalet yasası okunmadı. Eyalet geneli ifadelerde H1 (LHGebG/BayHIG) şartı aranacak mı, editoryal karar gerekli.

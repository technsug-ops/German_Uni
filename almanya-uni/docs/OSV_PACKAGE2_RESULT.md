# OSV-4 Paket 2 — Resmî Kaynaklı İçerik Onarımı (Sonuç)

**Tarih:** 15.10.2026 · **Kapsam:** yalnız yerel (`almanyauni_baseline`). Commit / push / deploy / prod yazma / KB gömme **yok**.
**Paket:** `database/data/osv/2026-10-15-osv4-package2.json` (222 düzenleme + 1 kayıtlı-HTML düzenlemesi)
**Migration:** `database/migrations/2026_10_15_000100_osv4_package2_bluecard_chancenkarte_work_funds.php` (`OfficialInfoRepair::apply`, 000100 deseni)
**Test:** `tests/Feature/OsvPackage2Test.php`

> "Kontrol tarihi" (15.10.2026) yalnız bizim okuma tarihimiz; metinlerde yürürlük tarihi olarak kullanılmadı ("since/itibaren/seit 15.10.2026" yok, "artık/inzwischen" yok). Yürürlük tarihi verilen tek yer kaynakta yazan dönemdir ("2026 için").

---

## 1. Yeniden okunan kaynaklar (15.10.2026, salt okuma, ≤1 istek/sn)

| Anahtar | URL | Neyi destekliyor | Neyi desteklemiyor |
|---|---|---|---|
| `banz_bluecard_2026` | bundesanzeiger.de … `BAnz AT 18.12.2025 B3.pdf` (BMI, 02.12.2025) | 2026: **50.700 €** (§ 18g Abs. 1 S. 1), **45.934,20 €** (§ 18g Abs. 1 S. 2 + Abs. 2) | Meslek listesi, kapsam ayrıntısı |
| `aufenthg_18g` | gesetze-im-internet.de/aufenthg_2004/__18g.html | İndirimli eşik: ISCO 132/133/134/21/221/222/225/226/23/25 **veya** diploması başvurudan ≤ 3 yıl önce; **BA onayıyla**. Abs. 2: diplomasız BT (ISCO 133/25), son 7 yılda ≥ 3 yıl deneyim | "Mangelberufe/Engpass" terimi, "otomatik" verilme |
| `aufenthg_18c` | …/__18c.html | Yerleşim izni: 27 ay; yeterli Almancayla 21 ay | B1 eşlemesi metinde yok (§ 2 Abs. 11 tanımı okunmadı; metinlerde "yeterli Almanca (B1)" olarak kaldı) |
| `aufenthg_20a` | …/__20a.html | Chancenkarte ilk etapta ≤ 1 yıl; ortalama ≤ 20 saat/hafta; deneme çalışması her biri ≤ 2 hafta; Fachkraft **veya** puan yolu; puan yolunda devletçe tanınan yükseköğrenim/≥2 yıllık meslek eğitimi + basit Almanca veya İngilizce B2; geçim güvence şartı; Folge-Chancenkarte ≤ 2 yıl (sözleşme/teklif + BA) | **Geçim tutarı yok**; asgari puan sayısı (6) sayfada görülmedi |
| `aufenthg_20` | …/__20.html | Abs. 1 Nr. 1: Almanya'da başarılı öğrenim sonrası iş arama; Abs. 2: geçim şartı, ≤ 18 ay, uzatma yok. **Abs. 3 yok**; yurt dışı mezunu için hüküm yok | Bu izinde çalışma hakkı (metinde açık hüküm yok → yazılmadı) |
| `aufenthg_16b` | …/__16b.html | Abs. 3: 140 iş günü (Arbeitstagekonto); öğrenci yan işi sayılmaz; ≤ 4 saat = yarım gün; ders döneminde ≤ 20 saatlik hafta ve ders dışı her hafta = 2,5 gün, lehe olan | 01.03.2024 değişiklik tarihi (C11) — doğrulanmadı, mevcut tarihsel cümlelere dokunulmadı |
| `aufenthg_16f` | …/__16f.html | Abs. 3: dil kursu izni ≤ 20 saat/hafta çalışma | — |
| `banz_subsistence_2026` | bundesanzeiger.de … `BAnz AT 29.08.2025 B1.pdf` (BMI, 13.08.2025) | 2026: **992 €/ay** — §§ 16b, 16c, 16e, 16f (hazırlık amaçlı olmayan dil kursları **hariç**) | § 20a Chancenkarte'yi kapsamaz |
| `tr_missions_language_course` | tuerkei.diplo.de …/2725588-2725588 (Merkblatt 44F) | Dil kursu: ayda **en az 1.091 €**; Sperrkonto ve Verpflichtungserklärung **eşit**; vize harcı i.d.R. 75 € | Sayfada tarih/"Stand" yok; başvuru kanalı (iDATA/portal) yazmıyor |
| `tr_missions_education` | …/2725836-2725836 | Eğitim amaçlı vizede Sperrkonto **veya** Verpflichtungserklärung | Tutar yok; Chancenkarte yok |
| `aufenthv_46` | gesetze-im-internet.de/aufenthv/__46.html | Ulusal (D) vize 75 € | Muafiyetler |
| `tr_missions_study` | …/tr-tr/…/2728622-2728622 | Öğrenim + öğrenim yeri arama vizesi yalnız Konsolosluk Hizmet Portalı | Başlangıç tarihi; iDATA'dan söz etmiyor |
| `tr_missions_national` | …/tr-tr/…/2728742-2728742 | Ulusal vizeler (portal dışı kategoriler): yalnız iDATA ve IOM-FAP; 39,92 € ve/veya 12 € (TL); iDATA bazen portala yönlendirir | — |
| APS (kod) | aps-india.de/faqs, aps-india.de/about-us, uni-assist APS sözlüğü, aps.org.cn | Belirleyici olan belge ülkesi, uyruk değil; APS yalnız Çin (2001), Vietnam (2007), Hindistan (2022); istisnalar (IB, Alman/AB kamu bursu) | APS Vietnam'ın kendi sitesi okunamadı (aps.org.vn DNS yok, vietnam.diplo.de 404) → Vietnam yalnız uni-assist + APS India "about" ile destekli |

**Doğrulanamayan / yazılmayan:** Chancenkarte aylık geçim tutarı (yalnız ikincil sitelerde 1.091 €/13.092 € → **UNVERIFIABLE**, metne konmadı; "temsilcilikle teyit et" denildi). Make it in Germany yine bu turda kullanılmadı.

---

## 2. Konu bazında yapılanlar

Statüler: **fixed** = paket/kodla düzeltildi · **needs_review** = kaynak eksik, dokunulmadı · **skipped** = kapsam dışı / doğru.

### A. Blue Card 2026 (50.700 € / 45.934,20 €)

| Kayıt (TR/EN/DE) | Önce | Sonra | Statü |
|---|---|---|---|
| Master A-Z 4/33/34 | "≥ 45.300 € = Blue Card otomatik" | 2026: 50.700 €; § 18g grupları + ≤ 3 yıllık mezun 45.934,20 € (BA onayı); maaş tek başına yetmez + BAnz linki | fixed |
| Blue Card rehberi 108/107/106 | "2026: 45.300 € / Mangelberufe 41.041 €"; "ortalama maaşın yüzdesi" | 50.700 / 45.934,20; RV tavanının %50 / %45,3'ü; yeni mezun kapsamı eklendi | fixed |
| IT 389/391/390 | tablo "(2025)" + 43.759,80; "6 aylık iş arama vizesi" | "(2026)" + 45.934,20; not "2026 için (BMI ilanı)"; § 20 / § 20a; meta/özet "2026". "2024'te 45.300 / 41.041,80" tarihsel cümle **korundu** | fixed |
| CS 392/394/393 | "~43.759,80, 2025" | 2026 ~45.934,20 (BT + yeni mezun, BA onayı) | fixed |
| Mühendis 401/403/402 | meta "~43.760€ 2025"; tablo (2025); "~21-33 ay" | meta/tablo 2026; yerleşim 27 / 21 ay (§ 18c) | fixed |
| İşletme 426/428/427 | meta "~48.3k€ (2025)" | "~50,7k€ (2026)" | fixed |
| 404–406, 414–419, 426–431, 438–443, 459–467 (30 kayıt) | Doğru 2026 değeri (50.700 / 45.934) "2025" etiketli | Yalnız değere yakın yıl ifadesi → "2026" (otomatik, satır başına) | fixed |
| FAQ 142/534 (533 EN'de iddia yok) | "56.400 €/yıl genel" | 50.700 / 45.934,20 + diploma/iş şartı | fixed |
| FAQ 144/537/538 | "Standart 56.400, Mangelberufe 44.000 (2026)" | 50.700 / 45.934,20 (ISCO 21 mühendislik); "kendiliğinden verilmez" | fixed |
| FAQ 200/646/647 | "≥ 56.400 € (2026) veya 44.000 €+" | 50.700 / 45.934,20 | fixed |
| `config/glossary.php` blue-card | 48.300 / 43.759,20 "2026"; 33 ay; "Job-Seeker 6 ay"; alt maddeler ekranda hiç görünmüyordu | 50.700 / 45.934,20 + BAnz; 27/21 ay; § 20 / § 20a; liste ayrı paragraf; doğrulanmamış AB hareketlilik iddiaları çıkarıldı | fixed |
| `ToolsController.php:1602–1603` (IT meslek kartı) | "€45.300 şart"; "diplomasız 3+ yıl + €50.000" | 50.700 / 45.934,20; § 18g Abs. 2 (son 7 yılda 3 yıl BT deneyimi) | fixed |
| Brief tohumları (`SeedHowtoBriefs` Blue Card notları, `BlogGenerateStarter` "Blue Card 2025") | "33 ay"; başlık 2025 | 27/21 ay + § 18g; başlık 2026 + doğrulanmış `notes` | fixed |
| Rehber 108 meslek listesi (mimarlık vb.), "tam zamanlı şart" | — | Kaynakla karşılaştırılmadı | needs_review |

### B. APS (yalnız Çin, Vietnam, Hindistan; belge ülkesi)

| Yer | Önce | Sonra | Statü |
|---|---|---|---|
| `config/glossary.php` aps (short/body/FAQ/maliyet/official_url) | "Çin, Hindistan, Pakistan, Vietnam, Moğolistan **vatandaşları**"; 250 USD / 22-100 EUR; apsdeutschland.de | Çin/Vietnam/Hindistan, **belge ülkesi ≠ uyruk**, istisnalar; ücret "ülkeye göre"; uni-assist APS sözlüğü linki | fixed |
| `BlogAutoLinker.php` APS tooltip | (paket 1'de düzeltilmişti) | Sözlükle tutarlı; değişmedi | skipped (doğru) |
| `SeedHowtoBriefs` APS brief | "Türk öğrenci için OPSİYONEL ama bazı üniler ister" | Türkiye belgeleri için APS yok; uyruk değil belge ülkesi | fixed |
| DB taraması (Pakistan/Moğolistan/"Türk için zorunlu") | — | İsabet yok | — |

### C. Chancenkarte / § 20

| Kayıt | Önce | Sonra | Statü |
|---|---|---|---|
| İş arama rehberi 71/72/73 (title, meta_title, meta_description, excerpt, content_md) | "Jobseeker vizesi 6 ay, çalışma YASAK", 6 × 992 € | **`set` + expect_sha256** tam yeniden yazım: § 20a (≤ 1 yıl, ort. ≤ 20 saat/hafta, deneme çalışması ≤ 2 hafta, Fachkraft / puan, Folge ≤ 2 yıl, geçim şartı, tutar doğrulanamadı) + § 20 (≤ 18 ay, geçim, uzatma yok); kaynak listesi; slug/id/grup aynı | fixed |
| Sperrkonto yazıları 20/55/56, 6/37/38 "İş Arama Vizesi" bölümü | "yüksek vasıflılara Sperrkonto gerekli" | Chancenkarte + "992 € Chancenkarte'yi kapsamıyor, temsilcilikle teyit" | fixed (sağlık sigortası "30 yaş" iddiası: needs_review) |
| IT 389–391 "6 aylık iş arama vizesi" + "iş arama vizesiyle gel" | eski vize | § 20 (Almanya mezunu) / § 20a | fixed |
| Chancenkarte geçim tutarı | — | yazılmadı | **unverifiable** |

### D. Öğrenci çalışması (§ 16b Abs. 3) ve § 20

| Kayıt | Önce | Sonra | Statü |
|---|---|---|---|
| FAQ 16/652/298 | "yılda en fazla 20 saat/hafta"; DE "120 volle / 240 halbe"; "18 ay otomatik/koşulsuz (§ 20 Abs. 3)" | Haftalık tavan yok, 140 iş günü + sayım kuralları + öğrenci yan işi sayılmaz; § 20 Abs. 1 Nr. 1, ≤ 18 ay, geçim, otomatik değil | fixed |
| FAQ 525/526 | "(120 days / 120 Tage …)" | 140 iş günü | fixed (TR 138 zaten doğru) |
| FAQ 527/528 | "120 full days × 8 h = 960 h", "120-day limit" | TR 139 ile hizalı 140 iş günü bloğu | fixed |
| FAQ 529 | "120 full days/year" | 140 iş günü | fixed (530/140 zaten doğru) |
| FAQ 165/580 tablo | öğrenci "Hafta 20 saat" | § 16f ≤ 20 saat/hafta ↔ § 16b 140 iş günü | fixed |
| `lang/tr.json`, `lang/de.json` yetim TR anahtarları ("yılda 120 tam gün / 240 yarım gün") | kullanılmayan 4 satır | silindi | fixed |
| Yazı 150/151, 307 ("120'den 140'a çıktı") | doğru tarihsel | dokunulmadı | skipped (doğru) |

### E. Geçim kanıtı

| Kayıt | Önce | Sonra | Statü |
|---|---|---|---|
| 20/55/56 + 6/37/38 dil kursu bölümü ve SSS #6 | "Sperrkonto zorunlu", "6 × 992 = 5.952 €" | § 16f: Sperrkonto **veya** Verpflichtungserklärung; Türkiye en az 1.091 €/ay (44F); 992 € = öğrenim 2026 (BAnz) | fixed |
| 6/37/38, 19/54/182 "Vize süresi" maddesi, tutar başlığı/cümlesi, meta/özet/giriş | "2024 itibarıyla, 2025 için de geçerli olması beklenen 992"; "zorunlu"; "2025" | "2026 için 992 €/ay (BMI, BAnz AT 29.08.2025 B1)"; "en yaygın yol"; 2026 | fixed (37/38 yayında değil) |
| FAQ 2/272/273, 165/580, 27/314/315 | "kurs süresi × 992 €" | ≥ 1.091 €/ay, Sperrkonto veya Verpflichtungserklärung | fixed |
| `BlogAutoLinker.php:270` tooltip + 85 yazının kayıtlı `content_html`'i | "~11.904 €/yıl (2025)" | "Öğrenim için 2026 … 992 € … Verpflichtungserklärung" (paket `html`), önbellek anahtarı `v4→v5` | fixed |
| `ToolsController` yorumları (513/517/977), vize ücreti metni | "2025 güncel", "2025: 75€" | resmî tutarlar 15.10.2026 kontrol; "75 € (§ 46 AufenthV)" (yeni `__()` anahtarı, tr/de çevirisi) | fixed |
| `PageFaq.php:322/326` | "DAAD estimates €992" (yanlış kurum); "2025/26 için büyükelçilik DAAD oranı" | BMI/BAföG, "2026 için 992 €", dil kursu 1.091 € | fixed (anahtar + tr/de) |
| Araç metinleri: cost-of-living ("as of 2025" ×2), recommendation ("(2025)"), visa-cost ("Up-to-date 2025 figures", "average values for 2025") | 2025 etiketi | 2026 / "resmî tutarlar 15.10.2026'da kontrol edildi; diğerleri tahmin" | fixed |
| `config/glossary.php` sperrkonto | "zorunlu"; "Federal Hükümet kararı" | en yaygın yol + Verpflichtungserklärung + dil kursu 1.091 € + BMI ilanı | fixed |
| `BlogGenerateStarter` "Sperrkonto 2025" | başlık/pain | 2026 + doğrulanmış `notes` | fixed |
| 11.904'ün ~15 dosyada sabit yazılması (G2) | — | refactor yapılmadı | skipped (görev gereği) |

### iDATA ↔ Konsolosluk Hizmet Portalı

| Kayıt | Önce | Sonra | Statü |
|---|---|---|---|
| Yazı 7/39/178 | "yalnızca iDATA'nın resmî sitesi"; DE "Termine ausschließlich über iDATA" | resmî kanallar: öğrenim → portal, diğer ulusal vizeler → iDATA | fixed |
| FAQ 166/582 | "Tüm vize başvuruları IDATA üzerinden"; "tüm başvuruları kapsar" | öğrenim/öğrenim yeri arama hariç; iDATA + IOM-FAP | fixed (581 EN zaten doğru) |
| FAQ 2000 + `faq-en/2026-10-13-batch1.json` | "Only iDATA and the missions issue appointments" | portal dışı ulusal vizelerde temsilcilik + iDATA / IOM-FAP | fixed (JSON hizalandı → yeniden uygulanırsa geri gelmez) |
| FAQ 766/1195/1196 | fark sorusu portaldan söz etmiyor | "Güncel durum (kontrol tarihi: 15.10.2026)" notu (insert_after) | fixed |
| Dil kursu vizesi kanalı (FAQ 2/272/273 "IDATA randevusu al") | — | portal kapsamı belirsiz | needs_review (OSV3 §8 ile aynı) |

---

## 3. Migration ve yerel sonuçlar

| Adım | Sonuç |
|---|---|
| `plan` (uygulama öncesi) | 219/219 `apply` (+ html 85 kayıt `apply`) |
| 1. `migrate` denemesi | **Atomik geri alındı**: DE iş arama özeti 294 karakter > `excerpt` varchar(280). Özet kısaltıldı (265). Tüm başlık/meta alanlarının uzunluğu üretim betiğinde kontrol edildi |
| 2. `migrate` | DONE; plan: 217 `already` + 2 `apply` → **hata**: 272/273 "2. Open a Sperrkonto" `replace` metni `find`'ı içeriyordu (idempotent değil). Yerel 272/273 satırı özgün metne geri alındı, `replace` değiştirildi, migration kaydı geri alınıp yeniden çalıştırıldı. Teste "replace find'ı içeremez" kuralı eklendi |
| Ek kayıtlar | Uygulama sonrası taramada 27/314/315 ("kurs süresi × 992") bulundu → 3 düzenleme eklendi (toplam **222**) |
| Satır sonu | 71/72/73 `set` değerleri CRLF ile yazılmıştı (chunk sayısını bozuyordu). Paket değerleri LF'ye çevrildi, yerelde aynı kayıtlar LF'ye normalize edildi (model kaydı, yalnız bu 3 alan). Teste "yeni metinde \r yok" kuralı eklendi |
| Son `plan` | **222/222 `already`**, html `already` |
| Geri alınan transaction içinde yeniden çalıştırma | posts+faqs sağlama toplamları **aynı** (no-op) |

Yerel düzeltme notu: yukarıdaki iki yerel müdahale (272/273 geri alma, 71–73 LF normalizasyonu) yalnız bu paketin kendi yanlış ilk uygulamasını geri çevirmek içindir; sonuç, temiz baseline'a paketin uygulanmasıyla aynıdır. Prod'a ayrı bir işlem gerekmez.

---

## 4. Testler

Yalıtılmış test DB: `phpunit.osv2.local.xml` → `almanyauni_test_osv2` (iş sonunda silindi).

| Test | Sonuç |
|---|---|
| `OsvPackage2Test` (10 test) | geçti |
| `OsvPackage1RepairTest`, `OsvPackage1bCheckDateTest` | geçti |
| `BlogAutoLinkerAbbreviationTest`, `KbEmbedDryRunTest` | geçti |
| `OfficialKnownWrongRegressionTest` (diğer mühendis; "paket 2 kaynakları" testi dahil) | geçti |
| **Toplam** | **31 test / 29.644 assertion, 0 hata** |
| `php artisan i18n:audit --leaky` | Missing tr 0 / de 0; TR/DE leak 0 |

`OsvPackage2Test` kapsamı: paket geçerli ve her düzenleme kaynaklı (kaynakta `checked` = 2026-10-15); kontrol tarihi yürürlük tarihi gibi yazılmıyor, 15.10.2026 dışındaki tarihler yalnız BAnz künyesi; fixture → önizleme yazmaz → uygula → ikinci plan `already` → ikinci çalıştırma no-op; yanlışlar kalkar, tarihsel cümleler (2024 eşikleri, "120'den 140'a") korunur, id/slug/grup değişmez, yönlendirme yok; beklenmeyen metin ezilmez; `set` yalnız beklenen hash ile; Chancenkarte metni güncel kanunu ve "tutar doğrulanamadı"yı içeriyor, 1.091/5.952 yok; tooltip kayıtlı HTML'de ve yeni kayıtlarda düzeliyor; sözlük değerleri kaynaklarla aynı; `/tr/sozluk/blue-card` ve `/tr/tools/visa-cost` sayfaları düzeltilmiş rakamlarla render ediliyor; aktif kaynaklar (sözlük, ToolsController, PageFaq, BlogAutoLinker, lang, araç görünümleri, brief tohumları, faq-en ve OSV paketlerinin yeni metinleri) KNOWN_WRONG listesinden temiz.

---

## 5. KB planı (gömme yapılmadı)

KB yalnız FAQ `question+answer_md` ve yazı `title+content_md` metnini okur (meta/özet/HTML tooltip KB'yi etkilemez). Sayım `KbEmbed::stripMd/chunkProse` (reflection) ile yapıldı; FAQ = 1 chunk/kayıt.

| Tür | Etkilenen yayındaki kayıt | Yeniden gömülecek chunk (≈) | Yereldeki mevcut chunk |
|---|---|---|---|
| FAQ | 30 | 30 | 27 |
| Yazı | 58 | 439 | 178 |
| **Toplam** | **88** | **≈ 469** | 205 |

Yayında olmayan 2 yazı (37/38) KB'ye girmez. Yerel KB eski (çoğu kariyer yazısının yerelde chunk'ı yok); canlıda `kb-embed?dry=1` beklenen sayı ≈ 470 civarı olmalı (paket 1/1b sonrası başka değişiklik yoksa). Binlerce çıkarsa durdurulmalı.

---

## 6. Riskler

- **Prod metin farkı:** baseline 13.10 dökümü; sonradan panelde değişen kayıtlar `mismatch` ile atlanır (ezilmez). Deploy sonrası `/admin/ops/osv-repair?file=2026-10-15-osv4-package2` atlananları gösterir.
- **Tam yeniden yazım (71/72/73):** yalnız sha256 eşleşirse uygulanır; başlık/meta değişir (URL aynı) → kısa süreli SERP dalgalanması olabilir.
- **Otomatik yıl etiketi (30 kayıt):** yalnız 50.700 / 45.934 değerine ≤ 75 karakter yakın "2025" değişti; maaş bandı "2025" tahminleri korunur.
- **Yeni `__()` anahtarları:** tr/de eklendi; en.json kendinden anahtar. Eski TR anahtarlı yetim satırlar (ör. "2025: 75€") zararsız bırakıldı.
- **Sözlük görünümü:** `glossary/show.blade.php` artık `[metin](https://…)` linklerini işler (yalnız https, `e()` sonrası).
- **`faq-en/2026-10-13-batch1.json` değişti:** mevcut migration'ın veri dosyası; yalnız 2000 kaydındaki tek cümle (paket 1'in hizalama deseni).
- **Kaynak değişkenliği:** 2027 tutarları (BMI 31.08 / Aralık ilanları) ve Türkiye temsilcilik sayfaları değişebilir; yeni yıl için paket 3 gerekir.

## 7. Açık kalanlar (needs_review / kapsam dışı)

- Chancenkarte geçim tutarı (resmî kaynak bulunamadı).
- FAQ 527/528 EN/DE'de Minijob "538 €" (TR 603 €) — kapsam dışı, kaynak bu turda okunmadı.
- `PageFaq.php:362` Werkstudent vergi muafiyeti "12.096 € (2025)" — kapsam dışı.
- Sperrkonto yazılarındaki "30 yaş üstü özel sigorta zorunlu" iddiası — kaynak yok.
- Blue Card rehberi 106–108'deki meslek listesi ve "tam zamanlı iş şart" ifadesi.
- Dil kursu vizesinin başvuru kanalı (portal mı iDATA mı) — OSV3 §8 ile aynı.

## 8. Deploy kontrol listesi (onay sonrası)

1. Commit: paket JSON, migration, test, kod/görünüm/lang değişiklikleri, `faq-en/…batch1.json`, bu rapor.
2. Push → deploy; `_deploy.php` migration'ı çalıştırır.
3. `/admin/ops/osv-repair?file=2026-10-15-osv4-package2` → hepsi `already` (mismatch'ler listelenir).
4. Canlı GET (200 + içerik): `/tr/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates` (+ `-en`, `-de`), `/tr/sozluk/blue-card`, `/tr/sozluk/aps`, `/tr/tools/visa-cost`, FAQ `how-long-is-a-german-student-visa-valid` (3 dil). Canonical/hreflang değişmemeli.
5. Önbellek: BlogAutoLinker anahtarı v5 (otomatik); gerekirse `cache:clear`.
6. KB: `/admin/ops/kb-embed?source=faq,post&dry=1` → ≈ 470 chunk; makulse gerçek çalıştırma (Gemini kredisi kontrol).
7. `chat:eval` OSV senaryoları (Blue Card 2026, 140 gün, Chancenkarte, dil kursu 1.091 €).
8. Koordinatör: aşağıdaki KNOWN_WRONG eklerini `database/data/osv/known_wrong.json`'a birleştirir.

## 9. KNOWN_WRONG ekleri (koordinatör `known_wrong.json`a birleştirir)

Birebir ifadeler (büyük/küçük harf duyarsız). Tarihsel bağlamla ("2024te", "eskiden", "previously") geçen cümleler serbest kalmalı. Aynı liste `OsvPackage2Test::KNOWN_WRONG` içinde.

- `45.300 € maaş = Blue Card otomatik`
- `45,300 € = automatic Blue Card`
- `45.300 € = Blue Card automatisch`
- `= automatic Blue Card`
- `= Blue Card otomatik`
- `= Blue Card automatisch`
- `56,400 €/yıl`
- `56,400 €/year`
- `56.400 €/Jahr`
- `56,400 € (2026)`
- `56.400 € (2026)`
- `44,000 €/yıl`
- `44,000 €/year`
- `44.000 €/Jahr`
- `**44,000 €+**`
- `**44.000 €+**`
- `Yıllık brüt **€41.041**`
- `Annual gross **€41,041**`
- `Jährlich brutto **€41.041**`
- `~43.760€ 2025`
- `~€43,760 in 2025`
- `~48.3k€ (2025)`
- `~48,3k€ (2025)`
- `~€48.3k (2025)`
- `(~€43.759,80/yıl, 2025`
- `(~43.759,80 €/Jahr, 2025`
- `(~€43,759.80/yr, 2025`
- `~21-33 ay`
- `~21-33 Monate`
- `~21-33 months`
- `33 ayda B1 olmadan`
- `33 months without`
- `oturma süresi (33 ay)`
- `€45.300 yıllık maaş şart`
- `OPSIYONEL ama bazı üniler ister`
- `This visa does **NOT** allow you to work`
- `lets you stay in Germany for **up to 6 months**`
- `with the 6-month Jobseeker Visa`
- `6 aylık Jobseeker Vizesi`
- `6-monatigen Jobsuchvisum`
- `6 aylık iş arama vizesi (Job-seeker visa)`
- `6-month job-seeker visa:**`
- `6-monatiges Job-Seeker-Visum`
- `A Sperrkonto is also required for highly qualified professionals`
- `yüksek vasıflı profesyoneller için de Sperrkonto gereklidir`
- `Job-Seeker Visa allows 6 months job search`
- `Job-Seeker Visa) ile 6 ay arayabilirsin`
- `18 aylık iş arama vizesi (§ 20 Abs. 3)`
- `18-month job-seeker visa (§ 20 Abs. 3)`
- `Visum zur Arbeitsplatzsuche (§ 20 Abs. 3)`
- `Automatic right to an **18-month job-seeker visa**`
- `Automatischer Übergang zu einem **18-monatigen Visum`
- `18 aylık iş arama vizesi** otomatik geçiş hakkı`
- `Yıllık en fazla **20 saat/hafta**`
- `maximum of **20 hours per week** per year`
- `maximal **20 Stunden/Woche** pro Jahr`
- `120 volle oder 240 halbe Tage`
- `120-day limit`
- `120-Tage-Grenze`
- `dil kursuna katılmak isteyen öğrenciler için de Sperrkonto zorunludur`
- `mandatory for students who wish to attend an intensive language course`
- `ein Sperrkonto obligatorisch`
- `kurs süresi × 992`
- `course duration × 992`
- `Kursdauer × 992`
- `Kursmonate × 992`
- `= 5.952 Euro`
- `= 5,952 Euro`
- `= 5,952 €`
- `= 5.952 €`
- `6 ay = 5,952`
- `6 Monate = 5.952`
- `992 Euro x 6`
- `expected to remain valid for 2025`
- `expected to be valid for 2025`
- `geçerli olması beklenen`
- `voraussichtlich auch für 2025`
- `DAAD estimates around €992/month`
- `the German embassy requires the standard DAAD rate`
- `Up-to-date 2025 figures`
- `Numbers are average values for 2025`
- `required for visa as of 2025`
- `Tüm vize başvuruları IDATA üzerinden yapılır`
- `Alle Visumanträge werden über IDATA gestellt`
- `Only iDATA and the missions issue appointments`
- `yalnızca iDATA'nın resmi web sitesini kullanmanız`
- `you only use iDATA's official website`
- `ausschließlich die offizielle Website von iDATA zu nutzen`
- `Termine für deutsche Visa werden ausschließlich über die offizielle Website von iDATA`

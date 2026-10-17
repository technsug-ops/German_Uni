# SEO Recovery P1-1 / Paket A — Sonuç (yerel)

Tarih: 17 Ekim 2026. Durum: **yerelde uygulandı ve test edildi; yayın onaylandı (17.10). Canlı sonuç: `SEO_RECOVERY_P1_1_PACKAGE_A_PRODUCTION_RESULT.md`.**
Dayanak: `SEO_RECOVERY_P1_1_INTENT_URL_MATRIX.md`, `SEO_RECOVERY_P1_1_IMPLEMENTATION_PLAN.md` §2.

Onaylanan kararlar uygulandı:
- Başvuru niyetinin ana URL'si `/study-in-germany`.
- Şartlı kabulün ana URL'si genel blog rehberi; üniversite koleksiyonu alt niyet olarak kaldı.
- Yeni URL açılmadı.

## 1. Değişen dosyalar

| Dosya | Değişiklik |
|---|---|
| `config/seo_intents.php` (yeni) | 5 niyet × TR/EN/DE ana URL + üye sayfalar. Tek kaynak |
| `app/Support/SeoIntents.php` (yeni) | Ana URL çözümleme (yalnız o dilde yayımlanmış yazı), üye → niyet, FAQ eşleştirme kuralları |
| `resources/views/components/seo-intent-guide.blade.php` (yeni) | "Ana rehber" kutusu. Niyet yoksa, URL çözülmüyorsa veya sayfa ana URL'nin kendisiyse hiçbir şey basmaz |
| `resources/views/home.blade.php` | "Başvuru Rehberleri" bölümü (5 kart). Başvuru yol haritası tanıtımından hemen sonra |
| `resources/views/blog/show.blade.php`, `news/show.blade.php` | Gövdenin üstünde kutu (yalnız üye yazılarda) |
| `resources/views/universities/collection.blade.php` | Giriş metninin altında kutu (yalnız şartlı kabul koleksiyonunda) |
| `resources/views/discover/tuition-free.blade.php` | Açıklama bölümünün altında kutu |
| `resources/views/faqs/show.blade.php` | Cevabın altında kutu (eşleşme varsa) |
| `lang/tr.json`, `lang/de.json` | 13 yeni anahtar (etiket ve açıklamalar) |
| `tests/Feature/SeoIntentLinksTest.php` (yeni) | 41 test, 215 doğrulama |
| `docs/SEO_RECOVERY_P1_1_INTENT_URL_MATRIX.md` (commit dışı planlama belgesi) | Düzeltme: `/tools/deadlines` bağlantısı FAQ şablonundan değil, site menüsünden geliyor |

**Değişmeyenler:** slug, route, canonical, hreflang, robots/noindex, sitemap, yönlendirmeler, H1/title/meta, blog/FAQ verisi ve program landing kuralları. Migration yok.

## 2. Beş ana URL

| Niyet (`config` anahtarı) | TR | EN | DE |
|---|---|---|---|
| `university_application` | `/tr/study-in-germany` | `/en/study-in-germany` | `/de/study-in-germany` |
| `masters_requirements` | `/tr/blog/doing-a-masters-in-germany-2026-a-z-guide` | `/en/blog/doing-a-masters-in-germany-2026-a-z-guide-en` | `/de/blog/doing-a-masters-in-germany-2026-a-z-guide-de` |
| `university_costs` | `/tr/blog/is-university-free-in-germany-2026-real-costs` | `/en/blog/is-university-free-in-germany-2026-real-costs-en` | `/de/blog/is-university-free-in-germany-2026-real-costs-de` |
| `conditional_admission` | `/tr/blog/germany-conditional-admission-bedingte-zulassung-guide` | `/en/blog/conditional-admission-germany-bachelor-master-2026-guide` | `/de/blog/bedingte-zulassung-deutschland-bachelor-master-2026-leitfaden` |
| `application_deadlines` | `/tr/tools/deadlines` | `/en/tools/deadlines` | `/de/tools/deadlines` |

**Doğrulama:**
- Yazı slug'ları yerel DB'de aynı `translation_group_id` altında üç dilde yayımlanmış. 17.10 canlı taramasında üçü de 200 döndü.
- Route tabanlı olanlar (`study.germany`, `tools.deadlines`) mevcut route adları.
- Bir çeviri yayımlanmamışsa veya yoksa o dilde kart ve kutu çıkmaz. Test: `test_unpublished_or_missing_translation_gets_no_url`.

## 3. Tıklama derinliği (ana sayfadan)

| Niyet | Önce (canlı, 17.10) | Sonra (yerel render) |
|---|---|---|
| Üniversite başvurusu | 1 | **1** |
| Yüksek lisans şartları | 2 | **1** |
| Üniversite ücretleri | ≥3 | **1** |
| Şartlı kabul | 2 | **1** |
| Başvuru tarihleri | 1 | **1** |

Üç dilde ayrı ayrı ölçüldü: TR, EN ve DE ana sayfaları 200 dönüyor ve her birinde 5 kart var. Her kart düz bir `<a href>` (JS gerekmez) ve aynı dildeki ana URL'ye gidiyor.

## 4. Ana rehber başına ilgili FAQ bağlantısı

Ölçüm yerel DB'de (13.10 prod kopyası, yayımlanmış 1.943 FAQ). Eşleşen her FAQ sayfası render edildi: hepsi 200, `index, follow`, self-canonical; kutudaki bağlantı aynı dildeki ana URL'ye gidiyor (150/150). Yanlış dile veya 404'e giden bağlantı: **0**.

| Ana rehber | TR | EN | DE | Hedef ≥10 |
|---|---|---|---|---|
| Üniversite başvurusu | 27 | 20 | 18 | ✅ |
| Yüksek lisans şartları | 18 | 20 | 14 | ✅ |
| Şartlı kabul | 7 | 6 | 6 | ❌ (gerçek sayı) |
| Üniversite ücretleri | 2 | 2 | 2 | ❌ (gerçek sayı) |
| Başvuru tarihleri | 3 | 3 | 2 | ❌ (gerçek sayı) |
| **Toplam** | **57** | **51** | **42** | 150 sayfa |

Önce: FAQ gövdelerinden bu beş rehbere bağlantı 0 idi. Tarihler aracı yalnız site menüsünden bağlantı alıyordu.

**Hedefin altında kalan üç niyet zorlanmadı.**
- **Şartlı kabul:** Soru metninde açıkça "şartlı kabul / conditional admission / bedingte Zulassung" geçen FAQ sayısı bu kadar.
- **Ücretler:** Havuzda öğrenim harcı veya Semesterbeitrag soran soru çok az: üç dilde ikişer ("ücretsiz master mümkün mü", "hangi eyalette öğrenim ücreti var"). Yaşam gideri, kira, market, ulaşım, Sperrkonto, para transferi ve vize harcı soruları bilerek dışarıda bırakıldı (yayın öncesi düzeltme, §4a).
- **Tarihler:** Tarih soruları büyük ölçüde DAAD burs veya yurt tarihleri; bunlar da dışarıda.

Bu üç niyette sayı ancak yeni FAQ içeriğiyle artar (Plan, paket E/G veya FAQ üretim hattı).

### Eşleştirme yöntemi

Deterministik ve yerel; harici AI çağrısı yok (`SeoIntents::matchFaq`). Her niyet için dört katman var:
1. **Soru metni:** Gerekli ifadelerin hepsi geçmeli. Örneğin yüksek lisans niyeti için hem "yüksek lisans/master" hem bir şart veya başvuru kelimesi aranır.
2. **Dışlama listesi:** Vize, randevu, konsolosluk, oturum, Sperrkonto, sigorta; ayrıca niyete özel dışlamalar (SWIFT, transfer, komisyon, burs, yurt, iş arama, online, tez, sonuç açıklanması vb.).
3. **İzinli FAQ konuları:** Örneğin ücret niyeti yalnız `money`, `city-life`, `master`, `recognition` konularında aranır.
4. **Kategori ve soru tipi:** `visa` kategorisi yalnız şartlı kabul niyetine izin verir. "ne-zaman" tipi tarih kuralını genişletir.

Kurallar sırayla denenir: şartlı kabul → tarihler → yüksek lisans → ücretler → başvuru. İlk eşleşen kazanır; hiçbiri eşleşmezse kutu çıkmaz.

**Örnekler** (testte 31 vaka: 12 pozitif, 19 negatif):

| Soru | Sonuç |
|---|---|
| "Yüksek lisans programları için başvuru şartları (GPA, ECTS) değişir mi?" | Yüksek lisans rehberi |
| "Uni-Assist ile üniversite paralel başvurusu yapılabilir mi?" | Başvuru rehberi |
| "Which German states charge tuition fees for non-EU students?" | Ücret rehberi |
| "Şartlı kabul ile C1'e ulaşmadan kayıt olunur mu?" | Şartlı kabul rehberi |
| "Wann beginnen die Bewerbungsfristen für Universitäten?" | Tarihler aracı |
| "Fintiba'ya transferde SWIFT şube bilgisi sorun yaratır mı?" | **Yok** |
| "Üniversite ücretleri Türkiye'den nasıl ödenir, komisyon?" | **Yok** |
| "Ankara konsolosluk öğrenci vizesi randevu süreci?" | **Yok** |
| "Goethe vs Telc — uni başvurusu için fark var mı?" | **Yok** (bağlamsız dil sertifikası) |
| "Master kabul belgesi vize başvurusu için yeterli mi?" | **Yok** |
| "DAAD burs başvuru tarihleri ne zaman?" / "Yurt başvurusu deadline?" | **Yok** |
| "Online master diploması geçerli mi?" / "Jobsuche vizesi kaç ay?" | **Yok** |
| "Is it possible to do a master's degree for free in Germany?" | Ücret rehberi |
| "Wie hoch ist der Semesterbeitrag an deutschen Hochschulen?" | Ücret rehberi |
| "Öğrenci yıllık yaşam maliyeti kaç Euro?" / "monthly cost of living for a student" | **Yok** |
| "Toplu taşıma ücreti" / "Lebensmittel im Supermarkt" / "yurt kira ücreti" | **Yok** |

### 4a. Yayın öncesi düzeltme (onaylı brief §1)

- **Ücret rehberinden çıkarılanlar:** Yalnız yaşam gideri, kira, market veya ulaşım soran sorular. 10 FAQ (TR 4, EN 4, DE 2) ücret kutusunu kaybetti; artık bunlarda kutu çıkmıyor.
- **Ücret rehberinde kalanlar:** Harç, Semesterbeitrag, "ücretsiz okunur mu" ve toplam öğrenim maliyeti soruları. EN'deki "master's degree for free" sorusu yeni kalıpla eşleşmeye eklendi.
- **Korunan:** uni-assist VPD ücreti soruları başvuru rehberine bağlı kaldı.

Eşleşen 150 sorunun tamamı tek tek okundu. Bu sırada bulunan hatalı eşleşmeler (ödeme/komisyon, Studienkolleg yurdu, "sonuç ne zaman açıklanır", Studienkolleg ücreti) kurala dışlama olarak eklendi ve teste girdi.

## 5. Üye sayfalardan gelen bağlantılar

30 üye sayfa render edildi: hepsi 200 dönüyor, her birinde tam olarak 1 kutu var ve kutu aynı dildeki doğru ana URL'ye bağlanıyor.

| Ana rehber | Üye sayfalar (her biri × TR/EN/DE) | Sayfa |
|---|---|---|
| Üniversite başvurusu | Bachelor başvuru rehberi, uni-assist rehberi, Studienkolleg rehberi | 9 |
| Yüksek lisans şartları | Master haberi (159/160/161), İngilizce master rehberi, GPA/kabul şansı yazısı | 9 |
| Üniversite ücretleri | "Ücretsiz üniversite" haberi (165/166/167), `/tuition-free` kataloğu | 6 |
| Şartlı kabul | Şartlı kabul vizesi yazısı, şartlı kabul üniversite koleksiyonu | 6 |
| Başvuru tarihleri | — (rakip veya alt sayfa yok) | 0 |

Ana rehberlerin kendisinde kutu yok (15/15 kontrol edildi).

## 6. TR/EN/DE bütünlük kontrolü

| Kontrol | Sonuç |
|---|---|
| Ana sayfa 200 + 5 kart | TR ✅ · EN ✅ · DE ✅ |
| 15 ana URL: 200 | 15/15 |
| 15 ana URL: self-canonical | 15/15 |
| 15 ana URL: `index, follow` | 15/15 |
| 15 ana URL: hreflang tr/en/de/x-default | 15/15 (değişmedi) |
| Bağlantılar aynı dilde | Ana sayfa 15/15 · üye 30/30 · FAQ 150/150 |
| Bağlantı hedefi yayımlanmış içerik | Evet. Yayımlanmamış veya eksik çeviri → bağlantı yok (testli) |

## 7. Testler

| Test | Sonuç |
|---|---|
| `SeoIntentLinksTest` | **41/41**, 215 doğrulama |
| Tam paket (CI gibi, `GEMINI_API_KEY` boş) | **724/724**, 83.764 doğrulama (önceki 683 + yeni 41) |
| `i18n:audit --leaky` | TR 0, DE 0 |

`SeoIntentLinksTest` kapsamı:
- config yapısı (5 niyet × 3 dil; ana URL üye listesinde değil),
- yayımlanmamış/eksik çeviri,
- üç dilde ana sayfa → 5 ana URL (200 + self-canonical + kutusuz),
- üç dilde 6 üye sayfa türü → doğru dilde tek kutu,
- ilgisiz yazıda kutu yok,
- 31 FAQ eşleştirme vakası (yaşam gideri/kira/market/ulaşım negatifleri dahil),
- FAQ sayfası render (ilgili → kutu, SWIFT → yok).

## 8. Performans ve kullanıcı deneyimi riskleri

| Risk | Değerlendirme |
|---|---|
| Ek DB sorgusu | Config'teki 9 ana yazı slug'ı için tek sorgu, 1 saat önbellekte (düz dizi; Eloquent Collection değil). FAQ eşleştirme saf PHP (≈5 regex), sorgu yok |
| Yayından kaldırılan rehber | Bağlantı en geç 1 saat sonra kaybolur (önbellek süresi) |
| Ana sayfa uzunluğu | Bir bölüm eklendi (5 kart). Mobilde tek sütun; görsel yok, yalnız Heroicon. Gradyan, emoji veya AI görseli yok |
| **`/study-in-germany` beklenti farkı** | Kart "Üniversite başvurusu" diyor, ama sayfa bugün hâlâ "Neden Almanya" içeriğinde. Açıklama bu yüzden "Buradan başla: Almanya'da eğitim ve ilk adımlar" olarak tutuldu. Asıl uyum Paket H ile gelir. O zamana kadar başvuru FAQ'larından bu sayfaya gelen kullanıcı adım adım başvuru içeriği bulamaz |
| FAQ sayfası | Kutu cevap ile "işine yaradı mı?" arasında. Cevabın okunmasını engellemiyor |
| Blog/haber | Kutu gövdenin üstünde, içindekiler tablosundan sonra. İçeriği bir kutu boyu aşağı iter |
| Erişilebilirlik | Bölüm `aria-labelledby`, kutu `aria-label`. Bağlantı metni anlamlı (niyet adı); klavye odağı görünür |
| CSS | Yerel production build'de doğrulandı (§8a) |

## 8a. Frontend doğrulama (yayın öncesi)

**Production build:** `npm run build` (Vite + Tailwind 4) yerelde çalıştırıldı; `public/build` git'te izlenmiyor, CI kendi build'ini alıyor. Yeni bileşenlerin sınıfları `app-*.css` içinde: `border-l-4`, `border-l-primary-600`, `lg:grid-cols-5`, `sm:grid-cols-2`, `sm:p-5`, `sm:inline-flex`, `focus-visible:outline-2`, `focus-visible:outline-primary-600`, `hover:underline`, `underline-offset-2`, `hover:border-primary-500`, `group-hover:text-primary-700`, `hover:shadow-md`.

**Görsel kontrol:**
- **Yöntem:** Headless Chrome; sayfa HTML'i yerel sunucudan alındı, build edilmiş CSS ile render edildi. Gerçek telefon değil.
- **Mobil genişlik:** Chrome'un asgari pencere genişliği yüzünden 375 px, iframe içinde ölçüldü.

| Görünüm | Sonuç |
|---|---|
| Ana sayfa TR, masaüstü 1366 px | 5 kart tek satır, eşit yükseklik; "Başvuru Rehberleri" yol haritası bandının hemen altında. Diğer bölümler yerinde |
| Ana sayfa EN/DE, masaüstü | 5 kart tek satır. DE başlıklar ("Bewerbung an der Hochschule", "Voraussetzungen für den Master") iki satıra kırılıyor; taşma yok, kartlar eşit yükseklikte |
| Ana sayfa DE, tablet 820 px | 2 sütun; 5. kart son satırda tek başına |
| Ana sayfa DE, mobil 375 px | Tek sütun, tam genişlik kartlar; metin taşması veya yatay kaydırma yok. Kartın tamamı tıklanabilir |
| FAQ TR, mobil | "Ana rehber · Yüksek lisans şartları →" kutusu tam genişlik, sol vurgu çizgisi, okunaklı |
| Haber DE, mobil | "Hauptratgeber · Voraussetzungen für den Master →" kutusu; taşma yok |
| Şartlı kabul koleksiyonu TR, mobil | "Ana rehber · Şartlı kabul →" kutusu giriş metninin altında; karar kartlarını bozmuyor |

**Erişilebilirlik:**
- Bölümün `aria-labelledby` ile bir başlığı var; kartlar `<ul role="list">` içinde.
- Bağlantı metinleri niyet adını taşıyor ("Yüksek lisans şartları →"); "buraya tıkla" yok.
- İkonlar `aria-hidden`; kutu `aria-label` taşıyor.
- Kartlarda `focus-visible` çerçevesi var.
- Kontrastın ayrıca ölçülmesine gerek görülmedi: koyu gri metin beyaz zemin üzerinde, sitenin mevcut kart renkleriyle aynı.

**Gözlem (değiştirilmedi):** Mobilde çerez kutusu ilk açılışta ekranın üstünü kaplıyor. Bu sitenin mevcut davranışı, Paket A ile ilgisi yok.

## 9. Geri dönüş planı

- Tek commit revert yeterli. Veri, migration ve URL değişikliği yok.
- Kısmi kapatma: `config/seo_intents.php` içinde bir niyetin `primary` kaydı silinirse o niyetin kartı ve kutuları kaybolur. Kod değişikliği gerekmez; önbellek 1 saat içinde yenilenir.

## 10. Yayın için öneri ve ölçüm

1. Yayın onayı → yalnız bu paketin dosyaları commit edilir → push → CI + deploy.
2. Yayından sonra (salt okunur): canlı tarama `crawl.py depth` → beş ana URL d1; üye sayfalardan ve FAQ örnekleminden bağlantı kontrolü.
3. KB etkisi yok: içerik verisi değişmedi.
4. GSC: yayın tarihi not düşülür; 60 günlük ölçüt Matris §2'deki taban değerlere göre.

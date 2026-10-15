# SEO Recovery P0-4: Soft 404 düzeltmeleri — sonuç raporu

Tarih: 14–15.10.2026. Durum: **yalnız yerelde uygulandı ve test edildi. Commit, push ve deploy yok.** Canlıya hiçbir yazma yapılmadı; canlıda yalnız salt-okuma GET (≈40 istek, 1 istek/sn, UA `Mozilla/5.0 (soft404-fix; read-only)`, 429 alınmadı).
Dayanak: `docs/SEO_RECOVERY_SOFT404_AUDIT.md`. Ortam: `almanyauni_baseline` (13.10 prod kopyası). Lokal DB'ye yazma yok; tüm render'lar transaction + rollback ile yapıldı ("No side effects"). Yeni migration lokalde **çalıştırılmadı**, yalnız geri alınan transaction içinde denendi.

Kapsam dışı ve dokunulmadı: kök `/` 302'si, dil öneksiz URL fallback'i (P0-3), OG/seo bileşeni, visa-appointment, `database/data/osv/*`, mevcut migration'lar, scripts.

## Özet

| # | Konu | Önce (canlı/lokal) | Sonra (lokal) | Etkilenen |
|---|---|---|---|---|
| C1 | Boş programatik landing (şehir×alan, şehir×dil, şehir NC-free, `?degree=` varyantları) | 200 + index + "program yok" | **404** | 2.304 kombinasyon/dil ≈ **6.912 URL** |
| C1 | Landing'de `page` > son sayfa, geçersiz `page` | 200 + index, boş liste | **404** | sınırsız |
| C1 | Boş hedefe giden çipler / üni NC-free yan butonu | koşulsuz bağlantı | yalnız sonuç varsa | 1.108+2+13 çip hedefi; 164 üni × 3 dil buton |
| C1 | Boş üniversite NC-free sayfası | 200 + noindex | **değişmedi** (200 + noindex), bağlantı kaldırıldı | 164 × 3 |
| C2 | `/universities/{id}`, `/cities/{id}` | 200 + self-canonical (kopya) | **tek 301** → kanonik slug | 486 + 222 ID × 3 |
| C2 | `/003`, `/3-xyz`, bilinmeyen ID | 200 (tip zorlaması) | **404** | sınırsız |
| C3 | Pasif üniversite, net karşılığı var (3) | 200 + index | **301** (tek adım) | 9 URL (+ID) |
| C3 | Pasif üniversite 225 | 200 + index | **410** | 3 URL (+ID) |
| C3 | Eyalet olan şehir kayıtları (10) + Bonn kopyası | 200 (5'i index) | **301** → `/states/{slug}` / aktif Bonn | 33 URL (+ID) |
| C4 | Yayında olmayan hedefe giden 14 `blog_redirects` | 301 → 404 | 12'si **tek 301 → 200**, 2'si **doğrudan 404** | 14 URL |

## C1 — Boş programatik landing'ler

### Kod

| Dosya | Değişiklik |
|---|---|
| `app/Http/Controllers/Web/LandingController.php:283` `paginateOr404()` | Toplam ayrı sayılır; `0` → 404; `page` varsa `^[1-9][0-9]{0,5}$` olmalı ve `≤ ceil(total/20)` olmalı, değilse 404. Toplam `paginate()`'e verilir (çift sayım yok). |
| aynı dosya `:62, :107, :145, :199, :236` | `cityField`, `cityLanguage`, `fieldDegree`, `fieldLanguage`, `cityNcFree` bu yardımcıyı kullanıyor (`fieldDegree`/`cityNcFree` imzasına `Request` eklendi). |
| aynı dosya `:52-56, :188-194, :302-330` | Çip sayıları: `degreeCounts()` (bachelor/master/phd), şehrin İngilizce program sayısı, alanın diğer dil sayısı. |
| `resources/views/programs/landing.blade.php:102-176` | Çipler sayıya bağlı (aşağıda). |
| `resources/views/universities/show.blade.php:283-290` | Yan menü "NC-frei Programlar" butonu `@if ($progNcFree > 0)` (sayfadaki sayaç kartıyla aynı koşul). |

**Sayım mantığı sitemap ile aynı:** `programs.is_active=1` + üniversitenin `city_id`'si (şehir) / `field_of_study_id` / `language IN (x, both)` / `admission_mode='zulassungsfrei'` / `degree`. Sitemap (`SitemapController.php:158-224, :542-580`) yalnız ≥1 aktif programlı kombinasyonu yazıyor; dolayısıyla sitemap'teki her landing 200 kalır (aşağıda doğrulandı).
`/programs?...` arama/filtre ekranı **değiştirilmedi**: geçerli ama sonuçsuz filtre 200 kalıyor (test edildi).

**Yan bulgu (düzeltildi):** eski kod toplamı `paginate()`'ten sonra `$query->count()` ile alıyordu; `paginate()` builder'a offset eklediği için 2. ve sonraki sayfalarda başlıkta ve meta açıklamada "0 program" yazıyordu. Toplam artık ayrı sayılıyor (testte 2. sayfada "21" doğrulandı).

### Davranış (lokal render, `before.tsv` / `after_code.tsv`)

| Örnek URL | Önce | Sonra |
|---|---|---|
| `/en/programs/city/potsdam-q1711/field/agriculture-forestry-nutrition` | 200 index | 404 |
| `/tr/programs/city/deutschland-q183/language/en` | 200 index | 404 |
| `/de/cities/wiesbaden/nc-free` | 200 index | 404 |
| `/en/programs/city/potsdam-q1711/field/engineering?degree=phd` | 200 index | 404 |
| `/de/programs/field/agriculture-forestry-nutrition/language/de?degree=phd` | 200 index | 404 |
| `/en/programs/field/computer-science/degree/master?page=999` | 200 index | 404 |
| `/en/programs/city/berlin-q64/field/computer-science?page=abc` | 200 (sayfa 1 kopyası) | 404 |
| `/en/programs/city/gera-q3750/language/en` (eski çip hedefi) | 200 index | 404 |
| `/en/programs/city/potsdam-q1711/field/engineering` (dolu) | 200 index self | 200 index self |
| `/en/programs/city/berlin-q64/field/computer-science?degree=master` | 200, canonical sorgusuz | aynı |
| `/en/programs/city/berlin-q64/field/computer-science?page=2` | 200 | 200 |

**Tam envanter (audit'in `empty_landings.csv`, 2.468 satır, dil dönüşümlü, lokal):**

| Tür | Satır | Sonra |
|---|---|---|
| şehir×alan boş | 1.000 | 1.000 × 404 |
| şehir×alan `?degree=` boş | 1.108 | 1.108 × 404 |
| şehir×dil boş | 122 | 122 × 404 |
| şehir NC-free boş | 72 | 72 × 404 |
| alan×dil `?degree=` boş | 2 | 2 × 404 |
| üni NC-free boş | 164 | 164 × 200 **noindex** (bilinçli) |

### Çipler, erişilebilirlik ve kullanılabilirlik

- Derece çipleri yalnız sonucu olan dereceler için gösteriliyor ve sayıyı taşıyor: "Bachelor (12)". Hiç derece çipi kalmazsa "By degree:" etiketi de gizleniyor; ayırıcı "·" yalnız iki grup da varsa çıkıyor ve `aria-hidden`.
- Seçili derece artık kendine bağlantı değil: `aria-current="page"` taşıyan, görsel olarak vurgulu `<span>`. Yanında "All degrees" (mevcut çeviri) çipi sorgusuz sayfaya dönüş sağlıyor. Böylece seçimi kaldırma yolu kayboluyor değil, ilk kez ekleniyor.
- "English programs" (şehir×alan) ve diğer dil çipi (alan×dil) yalnız hedefte program varsa gösteriliyor. Gizlenen çip yerine "devre dışı" görünümlü kontrol bırakılmadı: tıklanamayan sahte buton klavye/ekran okuyucu için kafa karıştırıcı olurdu; kalan set kendi içinde anlamlı (her öğe gerçek bir sonuca gidiyor).
- Tüm çipler gerçek `<a href>` (klavye ile odaklanabilir); yeni JS yok.
- Üniversite yan menüsü: NC-frei programı yoksa buton hiç render edilmiyor; kalan butonlar (favori, karşılaştır, web sitesi) aynı. Sayfanın üstündeki NC-frei sayaç kartı zaten aynı koşulla çalışıyordu, artık ikisi tutarlı.
- **Doğrulama:** sitemap'ten seçilen 127 dolu sayfada (şehir×alan, alan×dil, şehir×dil, üniversite) render edilen tüm landing/NC-free bağlantıları (998 tekil hedef) tek tek render edildi: **0 tanesi 200 değil.**

### Üniversite NC-free boş sayfaları: karar

**404 yapılmadı; 200 + noindex korundu, tek değişiklik bağlantının kaldırılması (audit seçenek B).** Gerekçe:
1. Sayfa zaten noindex. Google bunu "noindex ile hariç" olarak sınıflar; soft 404 riski düşük.
2. `admission_mode` HK/NC pipeline'ı ile sürekli güncelleniyor; 404 yapılırsa veri gelince sayfa 404↔200 arasında gidip gelir.
3. Asıl tarama kaynağı her üniversite sayfasındaki koşulsuz buton idi (164 üni × 3 dil); o kalktı. Sitemap'te zaten yoklar.
4. İçerik sayfa "bu üniversitede NC-frei veri yok" mesajı ve genel bilgi taşıyor; kullanıcı doğrudan gelirse işe yarar.
GSC'de bu URL'ler 4–6 hafta sonra hâlâ "Soft 404" altında görünürse 404'e geçmek tek satırlık bir değişiklik (`AdmissionFreeController::byUniversity`).

## C2 — Sayısal ID URL'leri

| Dosya | Değişiklik |
|---|---|
| `app/Http/Controllers/Web/UniversityWebController.php:416-432` `redirectNumericId()` | `ctype_digit` ise: `^[1-9][0-9]{0,9}$` değilse 404; kayıt yoksa 404; `MERGED_SLUGS` hedefi varsa ona, aktifse kendi slug'ına **301**; `GONE_SLUGS` ise 410; eşlemesiz pasif kayıt 404. |
| aynı dosya `:441-452` | `orWhere('id', …)` kaldırıldı; slug araması katı (`where('slug')`). |
| `app/Http/Controllers/Web/CityController.php:130-141, :145-157` | Aynı kural şehirde; `orWhere('id')` kaldırıldı. |

- DB'de rakamla başlayan slug: üniversite 0, şehir 0, eyalet 0 → katı kuralın yan etkisi yok.
- Yönlendirme sorgu dizgisini taşımıyor (`?utm_source=x&page=3` testte düşüyor), dil aynı, tek adım.
- Dokunulmayanlar: `MERGED_SLUGS` slug yönlendirmeleri, `/universities/{slug}/nc-free` (zaten katı slug + aktif), `POST /universities/{uniSlug}/reviews`, API (`Api\UniversityController`, `Api\CityController` ayrı controller).

| URL | Önce | Sonra |
|---|---|---|
| `/en/universities/3`, `/tr/…/3`, `/de/…/3` | 200 self-canonical | 301 → `/{l}/universities/akademie-der-bildenden-kunste-nurnberg-q414041` → 200 |
| `/en/universities/3-xyz`, `/en/universities/003` | 200 (id 3'ün kopyası) | 404 |
| `/en/universities/999999` | 404 | 404 |
| `/en/universities/81` (birleştirilmiş Macromedia kabuğu) | 200 | 301 → `macromedia-university-of-applied-sciences-hs366` (tek adım) |
| `/en/universities/856` (DESY, NEEDS_REVIEW) | 200 | 404 (ID URL'i; slug sayfası değişmedi) |
| `/en/cities/8`, `/tr/cities/8` | 200 | 301 → `/{l}/cities/berlin-q64` |
| `/en/cities/8-xyz`, `/en/cities/008` | 200 (Berlin kopyası) | 404 |
| `/en/cities/62` (pasif Bayern kaydı) | 200 | 301 → `/en/states/bayern` |
| `/en/cities/48` (Mitte, REVIEW) | 200 | 404 (ID; slug sayfası değişmedi) |

## C3 — Pasif üniversiteler ve şehirler

### Üniversiteler (`inactive_universities.csv`, birleştirilmemiş 15 kayıt)

| ID | Slug | Karar | Gerekçe |
|---|---|---|---|
| 619 | `hochschule-kempten-partner-019ddbba` | **301** → `hochschule-fur-angewandte-wissenschaften-kempten-q1622115` | Aynı kurum (Hochschule Kempten = HAW Kempten); kabuk 0 program, hedef aktif 77 program. |
| 861 | `baden-wuerttemberg-cooperative-state-university-heidenheim-partner-019de9f1` | **301** → `duale-hochschule-baden-wurttemberg-heidenheim-q1262602` | Aynı kurum (DHBW Heidenheim), aynı şehir (Heidenheim an der Brenz); hedef aktif. |
| 575 | `hesse-state-university-…-q1481465` | **301** → `hessische-hochschule-fur-offentliches-management-und-sicherheit-partner-019ddbba` | Wikidata Q1481465 = Hessische Hochschule für Polizei und Verwaltung; kurum yeniden adlandırıldı (HöMS). Eski kaydın web sitesi zaten `hoems.hessen.de`; hedef Wiesbaden'de, aktif, 7 program. |
| 225 | `informationszentrum-fur-fremdsprachenforschung-q820409` | **410** | Yükseköğretim kurumu değil (Marburg Üniversitesi'ne bağlı bilgi merkezi), 0 program, sitede karşılığı yok. Marburg'a yönlendirmek "benzer isim" yönlendirmesi olurdu. |
| 844 | `baden-wuerttemberg-cooperative-state-university-partner-019de9f1` | **NEEDS_REVIEW** (değişmedi) | 2 aktif program (International Business, Computer Science; konum belirsiz); DHBW'nin tek bir ana kaydı yok, hangi kampüs olduğu doğrulanamadı. |
| 856, 864, 865, 867, 889, 898, 911, 922, 923, 942 | DESY, DIW, MPS Cognition, IOER, UFZ, HKI, MPI Animal Behavior, MPZPM, DKFZ, caesar | **NEEDS_REVIEW** (değişmedi) | 1–2 aktif doktora/master programı taşıyorlar (toplam 14 aktif program; DKFZ'de aynı program iki kez görünüyor). Karar: kurumu aktif say ya da program + kurumu birlikte kaldır. |

Uygulama: üç 301 mevcut `UniversityWebController::MERGED_SLUGS` haritasına eklendi (`:54-60`); 225 için `GONE_SLUGS` (`:62-70`) ve `abort(410, 'university')` (`:453-455`). 410 sayfası (`resources/views/errors/410.blade.php`) kurum metniyle gösteriliyor ("This university is no longer listed" + "All Universities" bağlantısı, noindex); program 410 metni değişmedi. Kayıt yeniden aktifleştirilirse 410 devre dışı kalır. Yeni çeviriler: `lang/tr.json:4660-4661`, `lang/de.json:4721-4722` (2 anahtar, satır düzeyinde ekleme).

**Neden 410 yalnız 225:** karşılığı olmayan, program taşımayan ve kurum olmayan tek kayıt bu. Diğer pasifler ya net bir aktif karşılığa sahip (301) ya da aktif program taşıyor (REVIEW). Landing'lerde 410 kullanılmadı: kombinasyon veri gelince dolabilir (404 doğru).

### Şehirler (13 pasif kayıt)

`CityController::PASSIVE_REDIRECTS` (`app/Http/Controllers/Web/CityController.php:23-35`), slug ve ID ile, tek adım. Eşleme, şehir kaydının `state_id`'si ile hedef eyaletin aynı olması ve adın eyalet adıyla birebir aynı olmasıyla doğrulandı (Q-id'ler Wikidata eyalet kimlikleri). Hedeflerin hepsi canlıda 200 + index.

| Slug | Hedef | Karar |
|---|---|---|
| `baden-wurttemberg-q985`, `bayern-q980`, `brandenburg-q1208`, `mecklenburg-vorpommern-q1196`, `niedersachsen-q1197`, `nordrhein-westfalen-q1198`, `rheinland-pfalz-q1200`, `sachsen-anhalt-q1206`, `schleswig-holstein-q1194`, `thuringen-q1205` | `/{l}/states/{aynı eyalet}` | 301 (5'i bugün index'li, eyalet sayfasının kopyası) |
| `bonn-q586` | `/{l}/cities/bonn-q892684` | 301 (aynı ad, eyalet, nüfus; hedef aktif) |
| `mitte-q883803` (eyalet: Niedersachsen), `mitte-q14214072` (eyalet: Sachsen) | — | **NEEDS_REVIEW**, değişmedi (Berlin-Mitte değiller; doğru hedef belirsiz). Bugün 200 + **noindex**. |

Kayıt yeniden aktif olursa yönlendirme kendiliğinden kapanır (test edildi).

Önce/sonra: `/en/cities/bayern-q980` 200 index → 301 `/en/states/bayern`; `/tr/cities/nordrhein-westfalen-q1198` → 301 `/tr/states/nordrhein-westfalen`; `/de/cities/thuringen-q1205` → 301 `/de/states/thuringen`; `/en/cities/bonn-q586` 200 noindex → 301 `/en/cities/bonn-q892684`.

## C4 — Ölü blog yönlendirmeleri (14 satır)

### Kod
- `app/Http/Controllers/Web/BlogController.php:150-157`: yönlendirme yalnız hedef bu dilde **yayındaysa** yapılıyor ve hedefin kanonik adresine (`Post::publicUrl()`) gidiyor. Hedef yayında değilse doğrudan 404 (301 → 404 zinciri yok). Ek kazanç: hedefi yayındaki bir **haber** olan 81 satır eskiden `/blog/eski` → `/blog/yeni` → `/news/yeni` (2 adım) idi; artık tek adımda `/news/yeni`.
- Veri: yeni migration `database/migrations/2026_10_14_004000_repoint_dead_blog_redirects.php`. `from_slug + locale` ile bulur; yalnız satır hâlâ beklenen eski hedefi gösteriyorsa, eski hedef hâlâ yayında değilse ve yeni hedef o dilde yayındaysa günceller; aksi halde dokunmaz ve sebebini yazar. İdempotent; `down()` yalnız kendi değiştirdiği satırları geri alır. Lokal DB'ye uygulanmadı; geri alınan transaction içinde denendi: 12/12 "updated", hepsi tek 301 → 200.

### Satır satır

İçerik karşılaştırması başlıklar (H2/H3) ve özet üzerinden yapıldı. Hedeflerin çoğu, taslağa alınan uzun yazının bölünmüş ve yayında kalan 1. parçası (aynı bölümler, aynı metin).

| Dil | from_slug | Eski hedef (taslak) | Yeni hedef (yayında, canlıda 200) | Dayanak |
|---|---|---|---|---|
| tr | `almanca-bilmeden-almanyada-ingilizce-master-yapmak-mumkun-mu-kapsamli-rehber` | `can-you-study-…-without-knowing` | `english-masters-in-germany-without-german-knowledge-finding-programs-and-application` | Aynı konu (Almanca bilmeden İngilizce master), aynı çeviri grubunun TR parçası, yakın zamanda doğrulanmış içerik |
| tr | `aile-birlesimi-vizesi-…-rehberi` | `family-reunion-visa-…-and` | `germany-family-reunion-visa-2026-who-can-apply` | Bölünmüş 1. parça, aynı H2'ler |
| en | `…-rehberi-en` | `…-and-en` | `germany-family-reunion-visa-2026-who-can-apply-en` | aynı |
| de | `…-rehberi-de` | `…-and-de` | `germany-family-reunion-visa-2026-who-can-apply-de` | aynı |
| tr | `part-time-jobs-in-germany-the-news-youre-looking-for-isnt-there-26060621524305b7` | bozuk haber | `part-time-jobs-germany-international-students-guide` | Haberi kaldıran migration (`2026_06_20_000100`) bu rehberi açıkça yerine koyuyor |
| en | `…-26060621524305b7-en` | bozuk haber | **yok → 404** | Kaynak içerik anlamsızdı (çerez sayfası meta-metni); EN'de part-time rehberi yok |
| de | `…-26060621524305b7-de` | bozuk haber | **yok → 404** | aynı |
| en | `sperrkonto-2025-tam-rehber-…-en` | `sperrkonto-2025-complete-guide-…-en` | `sperrkonto-for-a-german-visa-what-is-it-how-much-and-en` | Bölünmüş 1. parça, aynı H2'ler |
| de | `sperrkonto-2025-tam-rehber-…-de` | `sperrkonto-2025-complete-guide-…-de` | `sperrkonto-for-a-german-visa-what-is-it-how-much-and-de` | aynı |
| en | `almanyada-ogrenci-isleri-20-saat-…-en` | `student-matters-…-en` | `student-work-permit-in-germany-2026-20-hour-rule-and-types-en` | Bölünmüş 1. parça (20 saat kuralı, Minijob/Werkstudent) |
| de | `almanyada-ogrenci-isleri-20-saat-…-de` | `student-matters-…-de` | `student-work-permit-in-germany-2026-20-hour-rule-and-types-de` | aynı |
| tr | `testdaf-mi-dsh-mi-karsilastirmasi` | `testdaf-or-dsh-2026-…` | `testdaf-and-dsh-a-comprehensive-comparison-of-german-exams-for-2026` | Bölünmüş 1. parça (TestDaF/DSH nedir, karşılaştırma) |
| en | `…-en` | `…-en` | `…-for-2026-en` | aynı |
| de | `…-de` | `…-de` | `…-for-2026-de` | aynı |

Karşılığı olmayan 2 satır (EN/DE bozuk haber): blog dizinine ya da ana sayfaya yönlendirilmedi. **Öneri:** 410 daha doğru (içerik bilerek kaldırıldı), ancak `blog_redirects` satırı silinmeden 410 vermek ayrı bir mekanizma gerektirir; güvenli kısım olarak doğrudan 404 uygulandı. İstenirse EN/DE `student-work-permit-…` yazısına yönlendirme de savunulabilir, ama kaynak içerikte gerçek bir konu olmadığı için önerilmiyor.

Not: TR `can-you-study-…-without-knowing` (taslak) OSV-3 paketinde düzeltiliyor. Deploy anında yayına alınmışsa migration o satırı **atlıyor** (eski hedef yeniden yayında). Migration'dan sonra yayına alınırsa yönlendirme eşdeğer `english-masters-…` yazısına gitmeye devam eder.

## Canonical, hreflang, sitemap

**Canlı sitemap'teki tüm landing, üniversite ve şehir URL'leri lokalde render edildi** (`sitemap_urls.txt`, 14.10; 6.480 URL; 59 dk; yan etki yok):

| Tür | URL | 200 | index | self-canonical | hreflang tr/en/de + x-default |
|---|---|---|---|---|---|
| şehir×alan | 3.048 | 3.048 | 3.048 | 3.048 | 3.048 |
| şehir×dil | 1.110 | 1.110 | 1.110 | 1.110 | 1.110 |
| şehir NC-free | 411 | 411 | 411 | 411 | 411 |
| alan×derece | 90 | 90 | 90 | 90 | 90 |
| alan×dil | 60 | 60 | 60 | 60 | 60 |
| üniversite | 1.344 | 1.344 | 1.344 | 1.344 | 1.344 |
| şehir | 417 | 417 | **413** | 417 | 417 |

- **Landing'lerde sitemap ⊂ 200 + index: 4.719 / 4.719.** Veri kayması kaynaklı uyumsuzluk: **0**.
- 4 şehir URL'si (`/en|de/cities/wiesbaden`, `/en|de/cities/friedrichshafen`) noindex ama sitemap'te. Bu bu değişiklikten değil; audit §3d'de bilinen, önceden var olan sorun (`SitemapController.php:341`, kural yalnız TR bloğuna bakıyor). Ayrı düzeltme önerilir.
- Canonical kuralı değişmedi: `?degree=` varyantının canonical'ı sorgusuz URL. Bilinen kapsam dışı tutarsızlık (audit §6.4): hreflang `href` isteğin sorgu dizgisini taşıyor; testler bunu tolere ediyor, düzeltilmedi.
- Yönlendirmeler tek adım. Testte her 301'in hedefi doğrudan 200, sorgu taşınmıyor.

## Testler

Ayrı yapılandırma (`phpunit.soft404.local.xml`, DB `almanyauni_test_soft404`), iş sonunda silindi ve DB drop edildi.

| Test | Sonuç |
|---|---|
| Yeni `tests/Feature/SoftNotFoundP04Test.php` (20 test) | **20/20** |
| İlgili mevcut 25 sınıf: FieldLanguageLandingTest, MergedUniversityRedirectTest, UniversityProgramListingTest, UniversityProfilesBatch2Test, LegacyRedirectChainTest, SitemapLocaleCoverageTest, HreflangConsistencyTest, DuplicateContentCleanupTest, InactiveProgramRedirectTest, ProgramListingFacetTest, SeoGrowthBatch3/4Test, EnglishSlugsPhase1/2Test, BlogAutoLinkerAbbreviationTest, ConditionalAdmissionBatchTest, HkProgramImportTest, ProgramQualityGateTest, ProgramVerificationLayerTest, FaqParityBatchAMigrationTest, RentFaqAndFeeGuideTransportAlignmentTest, StudentStatisticsReport2026Test, OsvPackage1RepairTest, KfwStudienkreditRate202610MigrationTest, FaqEnContentBatch1Test | **236/236** |
| `php artisan i18n:audit --leaky` | TR 0, DE 0 eksik/sızıntı |
| Tam paket | Koşulmadı (koordinatör koşacak) |

Yeni testlerin kapsamı:
- Boş landing → 404; dolu → 200 + index + self-canonical + 3 dil hreflang (tr/en/de).
- `?degree=` boş → 404; dolu → canonical sorgusuz.
- Şehir×dil, şehir NC-free, alan×derece, alan×dil boş → 404.
- `page` = 3/999/0/abc/-1/2.5 → 404; 2. sayfada toplam doğru.
- `/programs?…` sonuçsuz arama 200 kalır.
- Çipler boş hedefe bağlanmıyor; seçili çip `aria-current`; "All degrees" var; render edilen tüm çip/kenar çubuğu hedefleri 200.
- Fixture sitemap'indeki tüm landing'ler 200 + index.
- Üni NC-free butonu yalnız >0 iken; boş sayfa 200 + noindex.
- `/universities/{id}` ve `/cities/{id}`: 3 dilde tek 301 → 200; `00{id}`, `{id}-xyz`, bilinmeyen, `0` → 404; sorgu düşer.
- Pasif üni 301 (slug ve ID), birleştirilmiş kabuk ID → hedef; 225 → 410 (slug, ID, 3 dil, kurum metni); REVIEW kaydı değişmedi.
- Eyalet-şehir → `/states/…` (slug + ID, 3 dil); her eşlemede hedefin aynı eyalet slug'ı olduğu kontrol ediliyor; Bonn → aktif Bonn; Mitte değişmedi; yeniden aktifleşen kayıtta yönlendirme yok.
- Blog: taslak hedef → doğrudan 404; haber hedefi → tek adım `/news/`; migration 12 satırı tek 301'e çeviriyor, idempotent, yeni hedef yayında değilse ya da satır elle değiştirilmişse dokunmuyor, eski hedef yeniden yayındaysa dokunmuyor.

## Değişen ve eklenen dosyalar

| Dosya | Tür |
|---|---|
| `app/Http/Controllers/Web/LandingController.php` | değişti |
| `app/Http/Controllers/Web/UniversityWebController.php` | değişti |
| `app/Http/Controllers/Web/CityController.php` | değişti |
| `app/Http/Controllers/Web/BlogController.php` | değişti |
| `resources/views/programs/landing.blade.php` | değişti |
| `resources/views/universities/show.blade.php` | değişti |
| `resources/views/errors/410.blade.php` | değişti (kurum varyantı) |
| `lang/tr.json`, `lang/de.json` | +2 anahtar (satır düzeyinde) |
| `database/migrations/2026_10_14_004000_repoint_dead_blog_redirects.php` | yeni |
| `tests/Feature/SoftNotFoundP04Test.php` | yeni |
| `docs/SEO_RECOVERY_P0_4_RESULT.md` | yeni (bu rapor) |

## Riskler

- **404 artışı:** GSC "Bulunamadı (404)" binlerle artabilir (≈6.900 landing + ID varyantları). Bu sayfalar boş ya da kopyaydı; beklenen ve zararsız. "Yönlendirmeli sayfa" ID ve pasif kayıt 301'leriyle artabilir.
- **Veri dalgalanması:** dolu bir landing programları pasifleşince 404'e düşer. Sitemap aynı koşulla üretildiği için tutarlı; Google bir süre eski sürümü görebilir.
- **Pasif üni 301'leri:** üçü elle doğrulandı (575 için Wikidata + web sitesi + şehir). 844 ve araştırma enstitüleri bilerek bırakıldı.
- **ID URL'leri:** dış bağlantı alan ID URL'leri korunuyor (301). Eşlemesiz pasif kayıtların ID'leri artık 404.
- **Dil öneksiz ID URL'i** (`/universities/3`): hâlâ 302 → 301 iki adım. P0-3 kapsamında.
- **Migration prod farkı:** prod ID'leri farklı olabilir; eşleme `from_slug + locale` ile. Prod'da durum farklıysa satır atlanır ve sebep deploy logunda yazar. O satır yine doğrudan 404 verir (zincir yok).
- **Performans:** şehir×alan ve alan×dil sayfalarında 2 küçük ek COUNT sorgusu (indeksli kolonlar).

## Canlıya alma öncesi kontrol listesi

- [ ] GSC başlangıç değerlerini kaydet: **Soft 404 = 450**, **Bulunamadı (404) = 64** (ayrıca "Yönlendirmeli sayfa" 163, "Tarandı – dizinde değil" 6.805).
- [ ] GSC Performans'ta `/programs/city/`, `/cities/*/nc-free`, `/universities/[0-9]` filtreleriyle 0 sonuçlu URL'lerin gösterim alıp almadığına son kez bak (beklenti: yok).
- [ ] Tam test paketi yeşil (koordinatör) + `i18n:audit --leaky` temiz.
- [ ] Deploy logunda migration çıktısı: 12 satır `updated` (ya da açıklamalı `skip`).
- [ ] Deploy sonrası canlı kontrol (yalnız GET, ≤1 istek/sn):
  - `/en/programs/city/potsdam-q1711/field/agriculture-forestry-nutrition` → 404
  - `/de/cities/wiesbaden/nc-free` → 404
  - `/en/programs/field/computer-science/degree/master?page=999` → 404
  - `/en/universities/3` → tek 301 → 200; `/en/universities/3-xyz` ve `/003` → 404
  - `/en/cities/bayern-q980` → 301 `/en/states/bayern`
  - `/en/universities/hochschule-kempten-partner-019ddbba` → 301 → 200
  - `/en/universities/informationszentrum-fur-fremdsprachenforschung-q820409` → 410
  - `/en/blog/sperrkonto-2025-tam-rehber-almanya-vizesi-icin-bloke-hesap-en` → tek 301 → 200
- [ ] Deploy sonrası canlı sitemap'teki tüm landing URL'lerinin 200 + index olduğunu betikle doğrula (lokalde 4.719/4.719).
- [ ] Geri alma: kod tek commit revert'ü. Veri için migration `down()` yalnız kendi değiştirdiği 12 satırı geri alır.
- [ ] Takip: 844 + 10 araştırma enstitüsü + 2 Mitte kaydı için karar (NEEDS_REVIEW); sitemap'teki 4 noindex şehir; hreflang'in sorgu dizgisi taşıması (P2); 4–6 hafta sonra üni NC-free boş sayfaları GSC'de soft 404 görünüyorsa 404'e geçiş.

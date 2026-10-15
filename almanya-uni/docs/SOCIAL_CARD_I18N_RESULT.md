# Paylaşım kartı (Open Graph / Twitter) dil düzeltmesi — sonuç

Tarih: 2026-10-14/15 · Kapsam: yalnız lokal (commit/push/deploy yok) · Canlıya sadece salt-okur GET.

Kural: TR sayfa → Türkçe, EN sayfa → İngilizce, DE sayfa → Almanca (hem meta etiketleri hem kart görseli).

## 1. Başlangıç durumu (canlı, 2026-10-14)

### Kök neden
`/og/{type}/{slug}.png` route'u `{locale}` grubunun **dışında** (yalnız `web` middleware). `SetLocale` çalışmadığı
için kart her zaman `APP_LOCALE=tr` ile üretiliyordu. Üstelik cache yolu `og/{brand}/{type}-{slug}.png` dil-bağımsızdı →
ortak slug'lı tiplerde (üni/şehir/program…) üç dil aynı (Türkçe) dosyayı alıyordu. Bazı dosyalar eski marka adıyla
("ApplyToGerman (AlmanyaUni)") süresiz cache'te kalmıştı.

### Kart görseli — tip bazında Türkçe/yanlış metinler

| Tip | Canlı örnek | Yanlış metinler |
|---|---|---|
| post (blog/haber) | `/og/post/bachelor-and-master-thesis-process-in-germany-en.png`, `…-de.png` | slogan "Almanya başvuru rehberi", kategori "Almanya'da Eğitim", "23 dk okuma" (EN ve DE kartta) |
| university | `/og/university/technische-universitat-munchen-partner-019ddbba.png` | rozet "ÜNIVERSITE" (ayrıca yanlış TR büyük harf), "Münih · Bavyera", alt sağ sabit Almanca "Hochschule" |
| program | `/og/program/business-management-master-9cf479c0.png` | "Almanca Eğitim Verilen", rozet ham derece "MASTER", eski marka adı |
| city | `/og/city/berlin-q64.png` | "ŞEHIR" (yanlış İ), "44 üniversite" |
| field | — | rozet "EĞİTİM ALANI", Türkçe alan adı |
| profession | `/og/profession/3-d-artist-27272.png` (DE sayfada kullanılıyor) | "MESLEK", başlık "3D Sanatçısı", eski marka adı |
| scholarship | `/og/scholarship/country-related-…-20000300.png` (EN sayfada) | sabit "DAAD BURSU"/"BURS", yedek "Almanya bursu", sabit Almanca "Stipendium", eski marka adı |

Her tipte üst sol slogan `brand('tagline')` → app locale (tr) → "Almanya başvuru rehberi".

### Meta etiketleri (`components/seo.blade.php`)

| Etiket | Durum |
|---|---|
| og:title / og:description / twitter:title / twitter:description | Doğru (sayfa dilinde) |
| og:locale | Doğru (tr_TR / en_US / de_DE) |
| og:locale:alternate | Var olmayan `config('locale.active')` → sabit `[tr,de,en]`; çevirisi olmayan yazıda da hayalet diller listeleniyordu |
| og:image / twitter:image | URL dil bilgisi taşımıyordu (ortak-slug tiplerde hep TR kart) |
| og:image:type | `.webp` görseller (şehir/üni fotoğrafları) `image/jpeg` olarak bildiriliyordu |
| twitter:card | `summary_large_image` (config) — doğruydu |

## 2. Değişiklikler

| Dosya | Değişiklik |
|---|---|
| `app/Support/OgCard.php` (yeni) | Dil çözümü, sürümlü cache yolu, URL'ye dil ekleme, tip bazında kart metinleri (tümü `__('…', [], $locale)`), TR-duyarlı büyük harf (i→İ) |
| `app/Http/Controllers/Web/OgImageController.php` | Metin üretimi OgCard'a taşındı; dil → cache yolu → üretim. Marka adı/slogan/domain kart dilinden. `Cache-Control: public, max-age=604800` (eski: 30 gün `immutable`) |
| `resources/views/components/seo.blade.php` | `OgCard::localizeUrl()` ile og:image'a sayfa dili; og:locale:alternate = aktif diller ∩ gerçek dil-kardeşleri (`localeUrls`, hreflang ile aynı kural); og:image:type uzantıdan (png/webp/gif/jpeg); twitter:card görsel varsa `summary_large_image` |
| `app/Console/Commands/OgRefresh.php` | Yeni yol yapısı (tüm diller) + eski sürümsüz dosyalar; `--all` ikisini de siler |
| `lang/tr.json`, `lang/de.json` | 7 yeni anahtar (satır-bazlı ekleme): `Public university`, `Private university`, `University of Applied Sciences`, `Art & music college`, `Church-affiliated university`, `Higher education institution`, `1 university` |
| `tests/Feature/SocialCardI18nTest.php` (yeni) | 7 test (aşağıda) |

### Dil kararı (kayıttan / URL'den, app varsayılanından değil)
- **post**: dil = kaydın `locale` alanı (slug dil-başına benzersiz). URL **değişmedi**.
- **diğer tipler** (aynı slug her dilde): `?lang=tr|en|de`. `<x-seo>` bunu otomatik ekler → yasak dosyalara
  (`universities/show`, `programs/landing`, controller'lar) dokunmadan tüm sayfalar kapsandı.
- `lang` yok (eski URL) → marka varsayılan dili (applytogerman → en). Geçersiz `lang` yok sayılır (sınırsız dosya üretilemez).
- Dış/statik görseller (fotoğraf, `brand('og_image')`) ve zaten `lang` taşıyan URL'lere dokunulmaz.

### Kart metinleri (yeni)

| Tip | Rozet | Alt başlık | Alt sağ |
|---|---|---|---|
| post | BLOG / `News` | kategorinin yerel adı (`name_tr/en/de`) | `:count min read` → "23 dk okuma" / "23 min read" / "23 Min. Lesezeit" |
| university | `University` | şehir · eyalet (yerel ad) | tür: `Public university` vb. |
| program | `degree_label()` (Yüksek Lisans/Master) | üniversite (resmi ad) | `Taught in English/German` |
| city | `City` | eyalet | `:count universities` / `1 university` |
| field | `Study Field` | Almanca ad (farklıysa) | `Germany` |
| profession | `Profession` | Almanca ad / alan | KldB kodu |
| scholarship | `DAAD Scholarship` / `Scholarship` | yerel programmname | — |

Başlıklar: üni/program/burs resmi adları bilinçli olarak korunur (DE resmi ad; burs DE sayfada Almanca, diğerlerinde İngilizce).
Slogan `config/brand.php` tagline'ının kart dilindeki değeri. Global locale yalnızca model accessor'ları için geçici
değiştirilir ve `finally` ile geri alınır (testle doğrulandı).

## 3. URL / cache kararları ve sosyal cache etkisi

- Disk cache: `storage/app/public/og/{brand}/v2/{locale}/{type}-{slug}.png`. Eski Türkçe dosyalar (`og/{brand}/{type}-{slug}.png`)
  artık okunmaz → deploy sonrası ilk istekte doğru dilde yeniden üretilir. Yetim eski dosyalar için deploy sonrası
  isteğe bağlı: `php artisan og:refresh --all` (eski + v2 siler; sonraki istekte yeniden üretilir).
- **post URL'leri aynı** → yeni paylaşımlar ve yeniden taranan sayfalar doğru kartı alır, ancak Facebook/LinkedIn/X
  daha önce aynı görsel URL'sini önbelleğe aldıysa eski Türkçe görseli yeniden taramaya kadar gösterebilir.
  Önemli yazılar için: Facebook Sharing Debugger "Scrape Again", LinkedIn Post Inspector; X ~7 günde tazeler.
  Tarayıcılarda eski yanıt `immutable, max-age=30 gün` idi → en fazla 30 gün eski kopya kalabilir (CDN yok).
- **Ortak-slug tipler** (üni/program/şehir/alan/meslek/burs): og:image URL'si `?lang=xx` ile **değişti** → platformlar
  sayfayı yeniden taradığında yeni URL'yi doğru dille çeker (sosyal cache sorunu yok). Eski lang'sız URL'ler 200 dönmeye
  devam eder (404 yok), marka varsayılan dilinde.

## 4. Önce / sonra

PNG'ler: `C:/Users/Yapra/AppData/Local/Temp/claude/c--Users-Yapra-uni-finder-Claude-almanya-uni/d8e6a9e3-adf4-4820-89ab-8f83c66c8161/scratchpad/social/`

| | Önce (canlı, `before/`) | Sonra (lokal, Host=applytogerman.com, `after/`) |
|---|---|---|
| TR post | `post_bachelor-and-master-thesis-process-in-germany.png` | `post_tr.png`: "Almanya başvuru rehberi · Almanya'da Eğitim · 23 dk okuma" |
| EN post | `post_…-en.png`: TR slogan/kategori/"23 dk okuma" | `post_en.png`: "Apply to study in Germany · Studying in Germany · 23 min read" |
| DE post | `post_…-de.png`: TR slogan/kategori/"23 dk okuma" | `post_de.png`: "Bewerbungsleitfaden für Deutschland · Studium in Deutschland · 23 Min. Lesezeit" |
| Üni | `university_…png`: ÜNIVERSITE / Münih · Bavyera / Hochschule | `uni_tr.png` ÜNİVERSİTE / Devlet üniversitesi; `uni_de.png` UNIVERSITÄT / München · Bayern / Staatliche Hochschule; `uni_en.png` |
| Şehir | `city_berlin-q64.png`: ŞEHIR / 44 üniversite | `city_tr.png` ŞEHİR; `city_de.png` STADT |
| Meslek (DE sayfa) | `profession_…png`: MESLEK / 3D Sanatçısı | `prof_de.png`: BERUF / 3-D-Artist / Ingenieurwissenschaften |
| Program | `program_…png`: MASTER / Almanca Eğitim Verilen | `prog_tr.png` YÜKSEK LİSANS; `prog_en.png` MASTER / Taught in German |
| Burs | `scholarship_…png`: DAAD BURSU / Stipendium (EN sayfada) | `sch_tr.png` DAAD BURSU + TR programmname; `sch_de.png` DAAD STIPENDIUM |

Meta etiketleri (lokal, sonra):

| Sayfa | og:locale | og:locale:alternate | og:image | og:image:type |
|---|---|---|---|---|
| /en/blog/…-en | en_US | tr_TR, de_DE (gerçek kardeşler) | `/og/post/…-en.png` (değişmedi) | image/png |
| /de/blog/…-de | de_DE | tr_TR, en_US | `/og/post/…-de.png` | image/png |
| /tr/programs/business-management-master-… | tr_TR | en_US, de_DE | `/og/program/….png?lang=tr` | image/png |
| /de/professions/3-d-artist-27272 | de_DE | tr_TR, en_US | `/og/profession/….png?lang=de` | image/png |
| /tr/universities/tum… , /en/cities/berlin-q64 | tr_TR / en_US | diğer ikisi | fotoğraf `.webp` (dokunulmadı) | image/webp (eskiden yanlışlıkla image/jpeg) |

twitter:card her yerde `summary_large_image`; twitter:image = og:image.

## 5. Testler

Ayrı DB (`almanyauni_test_social`, `phpunit.social.local.xml`; ikisi de sonunda silindi).

| Komut | Sonuç |
|---|---|
| `--filter=SocialCardI18nTest` (7 test: post meta tr/en/de, çevirisiz yazıda hayalet alternate yok, şehir sayfası og:image `?lang=`, endpoint 200 image/png her dil + eski/geçersiz lang 200 + yok 404, post/şehir metin girdileri 3 dil, global locale korunur, localizeUrl kuralları) | 7/7 geçti, 95 assertion |
| `BlogRenderHtmlTest, BrandSingleNameTest, DaadScholarshipsSyncCuratedEligibilityTest, HreflangConsistencyTest, SitemapLocaleCoverageTest, SeoGrowthBatch3Test, SeoGrowthBatch4Test` | 78/78 geçti |
| `php artisan i18n:audit --leaky` | TR/DE eksik 0, sızıntı 0 |
| `php artisan og:refresh --days=0` | hatasız |

Tam suite koşulmadı (koordinatör koşacak).

## 6. Kalan riskler / notlar

- Eski post görselleri sosyal platform cache'inde yeniden taramaya kadar Türkçe kalabilir (bkz. §3). İstenirse post
  URL'sine de sürüm parametresi (`?v=2`) eklenerek zorla tazelenebilir — bilinçli olarak yapılmadı (URL kararlılığı).
- SSS (faq) sayfalarında dinamik kart yok; marka varsayılan görseli (`/img/og/applytogerman.png`, dil-nötr logo) kullanılıyor.
- Şehir/üni fotoğrafları `.webp` og:image — LinkedIn gibi bazı platformlar WebP'yi kısıtlı destekler (bu işin kapsamı dışında).
- Haber rozeti mevcut `News` anahtarını kullanır: TR "HABERLER", DE "AKTUELLES".
- Yasak dosyalarda değişiklik gerekmedi: `universities/show` ve `programs/landing` `route('og.image')` URL'leri
  `<x-seo>` içinde otomatik `?lang=` alır.
- Deploy sonrası canlıda doğrulama önerisi: EN/DE yazı kartını ve bir `?lang=de` üni kartını GET ile açıp metinleri kontrol et.

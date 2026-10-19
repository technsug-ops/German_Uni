# `content_updated_at` — Faz 1 Sonucu (yerel)

Tarih: 18 Ekim 2026. Durum: **yerelde uygulandı ve test edildi. Commit, push ve deploy yok; yayın onayı bekleniyor.**
Dayanak: `SEO_CONTENT_UPDATED_AT_DESIGN.md`, `SEO_CONTENT_UPDATED_AT_PHASE0_RESULT.md` §3.

**Kapsam (Faz 1):** şema + yazma kuralı + `blog:render-html` düzeltmesi.
**Kapsam dışı:**
- Faz 2: okuma noktaları. Sitemap `lastmod`, JSON-LD `dateModified`, `article:modified_time`, "Güncellendi" etiketi ve FAQ sırası **hâlâ `updated_at`'ten okunuyor**. Bu fazda SEO çıktısı değişmez.
- Faz 3: geçmiş kayıtlar için kanıta dayalı ilk değerler. Bu fazda geçmiş tarihler yazılmaz; yeni sütunlar mevcut kayıtlarda boş kalır.

## 1. Değişiklikler

| Dosya | Değişiklik |
|---|---|
| `database/migrations/2026_10_18_000100_add_content_date_columns_to_posts_and_faqs.php` (yeni) | `posts` ve `faqs`'a nullable sütunlar: `content_updated_at` (indeksli), `content_updated_floor`, `content_date_source`, `content_hash`. Yalnız ekleme; `down()` sütunları düşürür. Sütun zaten varsa atlar |
| `app/Models/Concerns/TracksContentDate.php` (yeni) | `saving` kancası (sütun varlığı uygulama container'ında önbelleklenir; §4). İçerik alanlarının özeti eskisinden farklıysa (veya kayıt yeni ise) `content_updated_at = now()`, `content_date_source = 'recorded'`. Özet her kayıtta güncellenir. `maintenance()` bloğu tarihi ilerletmez. Sütun yoksa (deploy sırasında migration'dan önce) sessizce atlar |
| `app/Models/Post.php` | Trait + içerik alanları: `title`, `excerpt`, `content_md`, `meta_title`, `meta_description` |
| `app/Models/Faq.php` | Trait + içerik alanları: `question`, `answer_md` |
| `app/Console/Commands/BlogRenderHtml.php` | `updated_at => now()` kaldırıldı. Komut her deploy'da çalışıyor ve yalnız HTML'i yeniden ürettiği halde `updated_at`'i ilerletiyordu |
| `app/Console/Commands/RepairPostLinks.php` | Bağlantı onarımı `Post::maintenance()` içinde kaydediliyor; içerik tarihi ilerlemez |
| `tests/Feature/ContentUpdatedAtPhase1Test.php` (yeni) | 8 test |

**İçerik sayılmayanlar** (tarih ilerlemez):
- `featured_image`, `featured_image_caption`, `slug`, `is_published`, `is_featured`, `category_id`, `sort_order`,
- sayaçlar,
- `content_html` / `answer_html` yeniden üretimi,
- satır sonu veya kenar boşluğu farkı.

**Değişmeyenler:**
- `updated_at` davranışı (kayıt değişikliklerini izlemeye devam eder; yalnız `blog:render-html` artık yazmıyor).
- Mevcut veri.
- Sitemap, JSON-LD, meta ve sayfa görünümü.
- URL, canonical, hreflang.

## 2. Testler

`ContentUpdatedAtPhase1Test`: **8/8**.

| Davranış | Sonuç |
|---|---|
| Migration sütunları ekler; model olayı olmadan eklenen kayıtlarda boş kalır | ✓ |
| Yeni yazı ve FAQ oluşturma tarihi kaydeder (`recorded`, özet doğru) | ✓ |
| Her içerik alanının düzenlenmesi tarihi ilerletir (5 yazı alanı + FAQ cevabı) | ✓ |
| Görsel/başlık silme, slug, yayın durumu, sayaç, FAQ sıra/öne çıkarma tarihi ilerletmez; `updated_at` ilerlemeye devam eder | ✓ |
| Aynı metni yeniden kaydetme veya yalnız satır sonu değişikliği ilerletmez | ✓ |
| `maintenance()` içindeki kayıt ilerletmez; blok bitince normal davranış döner | ✓ |
| Doğrudan sorgu, `toBase()->increment` ve sayfa ziyareti ilerletmez | ✓ |
| `blog:render-html --apply` HTML'i üretir; `updated_at` ve `content_updated_at` değişmez | ✓ |

Tam paket ve i18n: §4.

**Yerel migration denemesi** (`almanyauni_baseline`, MySQL 8.4; prod MariaDB 10.11):

| Adım | posts / faqs | Yeni sütunlar | İçerik özeti |
|---|---|---|---|
| Önce | 790 / 2.005 | yok | `b207bb26…` |
| `migrate` | 790 / 2.005 | var, 0 dolu | aynı |
| `migrate:rollback --step=1` | 790 / 2.005 | yok | aynı |
| `migrate` (tekrar) | 790 / 2.005 | var, 0 dolu | aynı |

Bekleyen tek migration buydu. SQL yalnız `ALTER TABLE … ADD` ve `ADD INDEX`.

## 3. Yayın etkisi

- **Deploy:** `_deploy.php` dosyaları açtıktan sonra migration'ı çalıştırıyor. Aradaki kısa sürede bir kayıt kaydedilirse trait sütunun yokluğunu görüp atlar (hata vermez).
- **Görünür değişiklik:** Yok. Okuma Faz 2'de.
- **Yayından sonra:**
  - Yönetim panelinden yapılan her içerik düzenlemesi `content_updated_at`'e yazılır.
  - Gelecekteki OSV/içerik migration'ları da yazar, çünkü model `save()` kullanıyorlar.
  - Faz 3 ilk değerleri `content_updated_at IS NULL` kayıtlara yazacak. Faz 1'den sonra düzenlenen kayıtlar zaten kesin tarih taşıyacak; Faz 3 bunları ezmez.
- **`blog:render-html`:** Bundan sonraki deploy'larda `updated_at`'i ilerletmez. Bu, SB-1 19.10 takibindeki istisnalardan birini de ortadan kaldırır.

## 4. Tam paket

| Kontrol | Sonuç |
|---|---|
| Tam test paketi (`GEMINI_API_KEY` boş) | **759/759**, 83.922 doğrulama (önceki 751 + yeni 8) |
| `i18n:audit --leaky` | TR 0, DE 0 |

**İlk tam koşuda bulunan ve düzeltilen hata:** İlk denemede 759 testten 373'ü `Unknown column 'content_updated_at'` ile düştü.

- **Neden:** `LegalProductionDryRunTest` bir tabloyu siliyor. DDL test transaction'ını kapattığı için Laravel (`RefreshDatabase`) bir sonraki testte veritabanını **aynı süreç içinde** baştan kuruyor. Trait'in "sütun var" bilgisi **statik** önbellekteydi. Yeniden kurulumda eski veri migration'ları sütun henüz eklenmeden yazı eklerken önbellek "var" diyordu. İlk hata migration'ı yarıda bıraktı ve sonraki her test zincirleme düştü.
- **İlk düzeltme denemesi:** Önbelleği migration olaylarında sıfırlamak yetmedi. Olay dinleyicisi yalnız ilk uygulama örneğine bağlanıyor; testlerde her test yeni bir örnek kullanıyor.
- **Kalıcı düzeltme:** Bilgi artık statik değişkende değil, **uygulama container'ında** tutuluyor (her uygulama örneğinde sıfır). Yalnız "var" sonucu saklanıyor, "yok" her kayıtta yeniden soruluyor.
- **Prod'daki karşılığı:** Deploy'da migration ayrı bir süreçte çalışıyor, istekler kendi uygulama örneğinde. Aynı tür bayatlık orada da oluşamaz.

## 5. Riskler ve geri dönüş

- **Bilinçli dışarıda bırakılanlar:**
  - `OfficialInfoRepair` `html` bölümü doğrudan sorguyla yazıyor ve tarihi ilerletmiyor (Faz 0 §3.2: paket bazlı `counts_as_content` kararı ileride).
  - `FaqEnglishContentBatch` slug taşıma yolu `updated_at`'i elle yazıyor. İçerik tarihi etkilenmez; `updated_at` için ayrı küçük iş.
- **Performans:** Kayıt başına iki sha256 (eski/yeni içerik). Sütun var/yok kontrolü uygulama örneği (istek) başına en fazla bir sorgu; yalnız yazı/FAQ kaydedilirken.
- **Geri dönüş:**
  - Kod: commit revert.
  - Şema: `php artisan migrate:rollback --step=1` (yerelde denendi; veri kaybı yok, kaynak alanlar dokunulmaz).

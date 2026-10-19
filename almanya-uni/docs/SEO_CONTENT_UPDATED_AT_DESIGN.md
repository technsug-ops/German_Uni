# `content_updated_at` — Tasarım (uygulanmadı)

Tarih: 18 Ekim 2026. Durum: **yalnız tasarım.** Sütun, migration veya veri değişikliği yok.
Dayanak: `SEO_RECOVERY_SB1_RESULT.md` §6, `SEO_BACKLOG.md` SB-1.

## 1. Sorun

- **Gelecek:** SB-1 düzeltmesiyle ziyaret ve oy artık `updated_at`'i değiştirmiyor.
- **Geçmiş:** 26.05.2026'dan (ilk commit) bu yana prod'daki her yazı, FAQ ve iş ilanının `updated_at`'i "son ziyaret/oy anı" ile kirli. Prod SQL yedekleri de aynı şekilde kirli.
- **Kapsam dışı değişiklikler:** `updated_at` yalnız içerik değil, her tür kayıt değişikliğinde ilerler (ör. `is_featured`, kategori, kapak görseli, sayaç dışı yönetim işlemleri). SEO "son içerik güncellemesi" ile aynı şey değildir.

Hedef: sitemap `lastmod`, JSON-LD `dateModified`, `article:modified_time` ve "Güncellendi" etiketi yalnız **gerçek editoryal içerik değişikliğini** yansıtsın.

## 2. Gerçek içerik değişikliğinin tespiti

Yeni sütunlar (yalnız tasarım):

| Tablo | Sütun | Tür |
|---|---|---|
| `posts` | `content_updated_at` | `timestamp NULL` |
| `faqs` | `content_updated_at` | `timestamp NULL` |
| `job_postings` | (önerilmiyor) | İş ilanında `updated_at` artık yalnız gerçek düzenlemeyle ilerliyor; ayrı sütun gereksiz |

**İçerik alanları:**
- Yazı: `title`, `excerpt`, `content_md`, `meta_title`, `meta_description`, `featured_image_caption`.
- FAQ: `question`, `answer_md`.

`content_html`/`answer_html` türetilmiş alanlardır; Markdown'dan ya da doğrudan HTML düzeltmesinden gelir.

**Ne zaman güncellenir:**
1. **Model `saving` kancası:** içerik alanlarından biri `isDirty()` ise `content_updated_at = now()`. Mevcut kanca zaten `content_md` değişimini dinliyor; aynı yere eklenir.
2. **Doğrudan sorgu yolları** (model olayı çalışmaz), açıkça ele alınmalı:
   - `OfficialInfoRepair` `html` bölümü: `DB::table()->update([$field => …])`. Gerçek bir metin düzeltmesiyse aynı sorguya `content_updated_at` eklenir. Ölçüt: değişen metin görünür içerik mi, yoksa yalnız işaretleme/tooltip mi?
   - `blog:render-html`, auto-linker, slug/link düzeltme komutları: **içerik sayılmaz.** HTML yeniden üretilir ama editoryal metin değişmez; tarih ilerlemez.
3. **Özet karşılaştırma (isteğe bağlı güvenlik):** `content_hash` sütunu (içerik alanlarının sha256'sı). Kaydetmede özet değiştiyse `content_updated_at` ilerler. Böylece "aynı metni yeniden kaydetme" tarih oynatmaz. KB'deki `content_hash` mantığıyla aynı.

**İçerik sayılmayanlar:**
- `view_count`, `helpful_count`, `unhelpful_count`,
- `is_published`, `is_featured`, `category_id`, `featured_image`,
- `audio_url`, `translation_group_id`, `slug` (slug değişimi URL değişimidir, ayrı süreç).

## 3. İlk değer ataması (kanıta dayalı)

Kural: **yalnız kanıtla ileri; kanıt yoksa yayın tarihi.** Kirli `updated_at` hiçbir koşulda kaynak değildir.

Her kayıt için aday tarihler toplanır, kanıt sırasına göre seçilir:

| Sıra | Kaynak | Atanan değer | Kanıt gücü |
|---|---|---|---|
| 1 | **İçerik değiştiren veri migration'ları** (git): OSV paketleri (`database/data/osv/*.json`), truth/FAQ parity/çeviri düzeltmeleri. Her migration'ın etkilediği kayıtlar JSON'daki slug/kimlik listesinden veya migration kodundan çıkarılır | Kaydın etkilendiği **son migration'ın prod'a çıkış günü**. Kaynak: commit tarihi + aynı commit'in Actions deploy run'ı (`api.github.com/.../actions/runs`). Run yoksa commit günü | Yüksek |
| 2 | **Prod SQL dökümleri** (`Downloads/applytogerman-backup-2026-10-08.sql`, `d0471a0e*.sql`, 13.10 baseline) | Ardışık iki dökümde içerik özeti farklıysa değişiklik o aralıkta: **aralığın bitiş tarihi**. Özet aynıysa o aralıkta değişiklik yok | Yüksek (aralık) |
| 3 | **`published_at`** | Kanıt yoksa ilk değer `published_at` (yayından sonra değişmediği varsayımı, ama tarih ileri itilmez) | Alt sınır |
| — | KB `kb_chunks.embedded_at` + `content_hash` | Atama için **kullanılmaz**; yalnız çapraz kontrol (embed tarihi < atanan tarih ise uyarı) | Üst sınır |
| — | `updated_at` (prod) | **Kullanılmaz** | Kirli |

**Seçim:** `max(1, 2-değişti-kanıtı)` varsa o, yoksa `published_at`. `content_updated_at` hiçbir durumda `published_at`'ten önce olamaz.

**Belirsiz durumlar** ayrı listelenir, otomatik atanmaz. Örnek: migration bir kaydı etkilemiş ama prod'da "already/missing" dönmüş olabilir (`OfficialInfoRepair` raporu bunu ayırt eder).

## 4. SEO alanlarının kaynağı

| Alan | Bugün | Yeni |
|---|---|---|
| Sitemap `lastmod` (yazı, haber, FAQ) | `updated_at` | `content_updated_at ?? published_at` |
| JSON-LD `dateModified` (`Seo::article`) | `updated_at` | `content_updated_at ?? published_at` |
| `<meta article:modified_time>` (`<x-seo :updatedAt>`) | `updated_at` | aynı yeni alan |
| "Güncellendi …" etiketi (`blog/show.blade.php`) | `updated_at > published_at + 1 gün` | `content_updated_at > published_at + 1 gün` |
| FAQ ana sayfası "son güncellenenler" | `orderByDesc('updated_at')` | `orderByDesc('content_updated_at')` |
| İş ilanı sitemap `lastmod` | `updated_at` | `updated_at` (SB-1 sonrası temiz; değişiklik yok) |

## 5. OSV tarihlerinin ayrı tutulması

- **OSV kontrol tarihi** ("kontrol tarihi: 15.10.2026") bir bilginin resmî kaynakta en son **doğrulandığı** gündür; içeriğin **değiştiği** gün değildir. İçerik metninin parçası olarak kalır; `content_updated_at`'i ilerletmez, onunla birleştirilmez.
- **`ProgramVerification.verified_at`/`checked_at` ve sağlayıcı modellerindeki doğrulama tarihleri** ayrı sütunlar; bu tasarım onlara dokunmaz.
- **Örtüşen durum:** Bir OSV paketi metni de değiştiriyorsa (ör. 2b'deki tarih düzeltmeleri), `content_updated_at` paketin deploy günü olur (§3 sıra 1). Yalnız kontrol tarihi güncelleyen bir paket (metin aynı, yalnız "kontrol tarihi" değişti) ayrı işaretlenmeli. Öneri: bu durum içerik değişikliği **sayılır**, çünkü görünür metin değişiyor; ama raporda ayrı sınıf olarak gösterilir.

## 6. Migration ve geri dönüş (gelecek paket)

1. **Şema migration'ı:** `posts.content_updated_at`, `faqs.content_updated_at` (nullable, indeksli). Geri dönüş: `down()` sütunları düşürür. Veri kaybı yok, kaynak alanlar dokunulmaz.
2. **Kod:**
   - `saving` kancaları, okuma noktaları (§4) ve `OfficialInfoRepair` HTML yolu.
   - Okuma `content_updated_at ?? published_at` olduğu için sütun boşken de güvenli: tarih geri gider ama yalan söylemez.
3. **Başlangıç verisi:**
   - Ayrı bir artisan komutu, önce `--dry-run`: kayıt başına aday tarihler, seçilen kaynak, belirsizler. CSV/JSON rapor.
   - Onaydan sonra data-migration: kanıt tablosu repoya JSON olarak girer; `OfficialInfoRepair` gibi idempotent, `expect` kontrollü.
   - Geri dönüş: sütunu `NULL`'a çekmek (okuma `published_at`'e düşer).
4. **Yayın sırası:** şema + kod (sütun boş) → doğrulama → başlangıç verisi. Her adım ayrı commit ve deploy.

## 7. Riskler

| Risk | Önlem |
|---|---|
| `lastmod`'un birden geriye gitmesi (sütun boşken `published_at`) | Google için sorun değil; yanlış "yeni" sinyalinden daha doğru. Başlangıç verisi aynı hafta yüklenebilir |
| Migration → kayıt eşlemesi eksik (bazı düzeltmeler kodla, JSON'suz) | Kuru çalıştırmada "kanıt yok" listesi; elle inceleme |
| Döküm tarihlerinin belirsizliği (dosya adı ≠ alınış anı) | Döküm başlığındaki `-- Dump completed on …` satırı esas alınır |
| HTML yolu (`OfficialInfoRepair` `html`) atlanırsa tazelik eksik kalır | Test: HTML düzeltmesi sonrası `content_updated_at` ilerler |
| Yönetim panelinden içerik dışı düzenleme (kapak görseli) tarih oynatmaz | Bilinçli karar; gerekirse alan listesi genişletilir |

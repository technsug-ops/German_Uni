# SEO Recovery P1-1 / Paket B — Çift H1 Düzeltmesi (yerel)

Tarih: 17 Ekim 2026. Durum: **yerelde uygulandı ve test edildi; yayın onaylandı (17.10). Canlı sonuç: `SEO_RECOVERY_P1_1_PACKAGE_B_PRODUCTION_RESULT.md`.**
Dayanak: `SEO_RECOVERY_P1_1_IMPLEMENTATION_PLAN.md` §1 (B), `SEO_RECOVERY_P1_1_PACKAGE_A_RESULT.md`.

## 1. Analiz

| Konu | Bulgu |
|---|---|
| Sayfa H1'i | Blog (`blog/show.blade.php`) ve haber (`news/show.blade.php`) şablonu `{{ $post->title }}` ile tek H1 basıyor |
| Gövde H1'i | Kayıtlı `content_md` `# Başlık` ile başlıyor. `Post` modelinin `saving` kancası Markdown'ı `MarkdownRenderer` (CommonMark) ile `content_html`'e çeviriyor; gövdede `<h1>` olarak saklanıyor |
| Kalıcı bağlantılar | `heading_permalink` yalnız H2–H4'e uygulanıyor (`<a id="content-…" class="heading-anchor">#</a>`). Gövde H1'lerinde çapa yok |
| İçindekiler tablosu | `TocBuilder` yalnız H2/H3'e `id` veriyor (yoksa slug; slug boş veya 80 karakterden uzunsa sıra numaralı `toc-hN`). Liste yalnız H2'lerden kuruluyor. H1'ler tabloda yok |
| HTML temizliği | `html_input: strip`. Kod blokları `<pre><code>` içinde kaçışlı; `#` ile başlayan kod satırı başlık olmuyor |
| TR/EN/DE | Aynı şablon ve aynı işleyiş; dil farkı yok |
| Diğer render noktaları | `BlogPostResource` (API JSON) `content_html`'i ham veriyor. Sayfa değil, dokunulmadı |

### H1 ölçümü (güncel yerel veri)

Yerel DB: 13.10 prod kopyası + yerel migration'lar. Sayı değişmedi: **147**.

| Ölçü | Değer |
|---|---|
| Yayımlanmış yazı (blog + haber) | 772 |
| Gövdesinde H1 olan yazı | **147** (hepsi blog: TR 35, EN 53, DE 59; haberlerde yok) |
| Gövdedeki toplam H1 | 163 |
| H1'i gövdenin ilk elemanı olan yazı | 147/147 |
| Birden fazla gövde H1'i olan yazı | 16 (EN/DE çevirilerinde ikinci başlık varyantı) |
| Kod bloğu içinde H1 | 0 |

**İlk H1 ile sayfa başlığı** (yıl ve noktalama hariç kelime örtüşmesi):

| Örtüşme | Yazı |
|---|---|
| Birebir aynı | 106 |
| Biri diğerini kapsıyor | 15 |
| Kelime örtüşmesi ≥ 0,6 | 11 |
| Kelime örtüşmesi 0,4–0,6 | 12 |
| Kelime örtüşmesi < 0,4 | 3 |

En düşük üç örnek de başlığın yıllı/uzun varyantı (ör. "Rundfunkbeitrag (GEZ) Studentenleitfaden 2026: Gibt es eine Befreiung, …").

## 2. Uygulama — yalnız render katmanı

`App\Support\ContentHeadings::normalizeH1($html, $title)`:

1. **Öndeki H1 başlıkla aynı anlamdaysa kaldırılır.** Yalnız gövdenin en başında, önünde görünür metin olmayan ilk H1 kaldırılabilir. "Aynı anlam" ölçütleri:
   - normalize edilmiş kelime kümeleri eşit, ya da
   - biri diğerini kapsıyor, ya da
   - kelime örtüşmesi ≥ 0,3.

   Normalizasyon: küçük harf, aksan ve Türkçe I/İ/ı farkı yok, yıllar ve noktalama atılmış.
2. **Diğer her H1 H2'ye çevrilir.** Bu, farklı anlamlı öndeki H1'i ve sonradan gelen H1'leri kapsar.
   - İçerik ve öznitelikler korunur.
   - Kimliği yoksa `id="post-heading-N"` eklenir. Böylece `TocBuilder`'ın sıra numaralı kimlikleri (`toc-hN`) ve tekrar eden slug ekleri (`-2`) kaymaz.
3. **Kod blokları dokunulmaz.** `<pre>…</pre>` blokları işlemden önce yer tutucuya alınır, sonra geri konur.
4. **Diğer başlıklar değişmez.** H2/H3/H4 hiyerarşisi ve kalıcı bağlantılar aynen kalır.

Uygulandığı yerler:
- `blog/show.blade.php`: `TocBuilder::process(ContentHeadings::normalizeH1(...))`; içindekiler tablosu normalize edilmiş HTML'den kurulur.
- `news/show.blade.php`: gövde basılmadan önce.

**Değişmeyenler:** `content_md`, `content_html`, başlık/meta alanları, migration, URL/slug, canonical, hreflang, sitemap, noindex.

## 3. Önce / sonra (yerel, 772 yayımlanmış yazının tamamı)

Her yazı gerçek HTTP katmanından render edildi.

| Ölçü | Önce | Sonra |
|---|---|---|
| Sayfada tek H1 olan yazı | 625 / 772 | **772 / 772** |
| Sayfada 2+ H1 olan yazı | 147 | **0** |
| 200 dönmeyen yazı sayfası | — | 0 |
| Kaldırılan öndeki H1 (başlık tekrarı) | — | 147 |
| H2'ye çevrilen H1 | — | 16 |
| Kaybolan başlık kimliği/çapası | — | **0** |
| İçindekiler tablosu girdisi | 5.313 | 5.329 (+16, çevrilen H2'ler) |
| Kırık içindekiler bağlantısı (`#id` sayfada yok) | — | **0** |

- **"Önce" sütunu:** sayfa sayıları gövde H1 ölçümünden türetildi (şablon H1 + gövde H1'leri).
- **"Kaybolan kimlik":** değişiklikten önceki `TocBuilder` çıktısındaki her `id` sonrakinde de var mı, diye 772 yazıda karşılaştırıldı.

**Kayıtlı içerik:** Değişen 147 yazı render edildi; `title`, `slug`, `content_md`, `content_html`, `meta_*`, `excerpt`, `is_published` alanlarının özeti önce ve sonra aynı. Gövde H1'i veritabanında duruyor; yalnız sayfada ele alınıyor.

**Yan etki (yalnız yerel kopya):** Ölçüm için render edilen sayfalar yerel `posts.view_count` sayacını artırdı. Bu `updated_at`'i de güncelledi (772 kayıt). İçerik alanlarına dokunmadı. Production etkilenmedi.

**H2'ye çevrilen 16 başlık:** EN/DE çevirilerinde, giriş paragrafından sonra gelen ikinci bir başlık varyantı. Örnekler: "Studienkolleg Guide — T-Kurs/M-Kurs/W-Kurs Which One is For You?" ve "Germany Consulate Visa Appointment — 2026 Strategy". Bu başlıklar artık içindekiler tablosunda görünüyor. İçerik düzeltmesi olarak kaldırılmaları ileride ayrı bir veri işi olabilir; bu pakette içerik değiştirilmedi.

İlgili yazılar: 82/83, 91/92, 100/101, 109/110, 112/113, 142/143, 187/188, 190/191.

## 4. Değişen dosyalar

| Dosya | Değişiklik |
|---|---|
| `app/Support/ContentHeadings.php` (yeni) | Render katmanı H1 normalizasyonu |
| `resources/views/blog/show.blade.php` | `TocBuilder` girdisi normalize edilmiş HTML |
| `resources/views/news/show.blade.php` | Gövde normalize edilerek basılıyor |
| `tests/Feature/ContentHeadingsTest.php` (yeni) | 14 test |

## 5. Testler

`ContentHeadingsTest`: **14/14**.

| Senaryo | Test |
|---|---|
| Gövde H1 ile başlıyor, başlıkla aynı → kaldırılır | `test_leading_h1_same_as_title_is_removed` |
| Gövde H2 ile başlıyor → değişmez | `test_body_starting_with_h2_is_untouched` |
| İlk başlık farklı → H2 | `test_leading_h1_with_different_meaning_becomes_h2` |
| Sonraki H1 başlık tekrarı da olsa → H2 | `test_later_h1_becomes_h2_even_if_it_repeats_the_title` |
| Kod bloğunda `#` ve ham `<h1>` → dokunulmaz | `test_h1_inside_code_block_is_not_a_heading`, `test_markdown_pipeline_with_hash_in_code_fence` |
| H2/H3 korunur; mevcut kimlikler ve `toc-hN` kaymaz | `test_existing_heading_anchors_and_toc_ids_are_preserved` |
| Başlık benzerliği (yıllı varyant, büyük İ, farklı konu) | `test_same_title_judgement` ×4 |
| TR/EN/DE blog + haber sayfası: tek H1, içindekiler bağlantıları hedefli, kayıtlı içerik aynı | `test_blog_and_news_pages_have_single_h1_and_working_toc` ×3 |

Tam paket (CI gibi, `GEMINI_API_KEY` boş) ve `i18n:audit --leaky`: bkz. §6.

## 6. Tam paket

| Kontrol | Sonuç |
|---|---|
| Tam test paketi (`GEMINI_API_KEY` boş) | **738/738**, 83.828 doğrulama (önceki 724 + yeni 14) |
| `i18n:audit --leaky` | TR 0, DE 0 |

## 6a. Yayın öncesi içerik kaybı kontrolü (17.10)

Kaldırılan 147 öndeki H1'de başlıkta olmayan kelimeler incelendi:
- 117'sinde fazladan yalnız yıl var ya da hiç fazla kelime yok.
- 30'unda yeniden ifade veya alt başlık var.
- Başlıklarda bağlantı veya biçimlendirme yok (0).

Bu fazladan kelimelerin yalnız **5 yazıda** gövdenin geri kalanında geçmediği çıktı. Hepsi üslup kelimesi: "beantragt", "Yalnız Kalma!", "Kapsamlı", "hedefinize", "Antragsleitfaden". Bilgi kaybı yok. Örneğin "TK, AOK, Barmer, BKK" ve "T-Kurs/M-Kurs/W-Kurs" gövdede geçiyor. **Kritik içerik kaybı: 0.**

## 7. Riskler ve geri dönüş

- **Görünür değişiklik:** 147 yazıda gövdenin başındaki büyük başlık tekrarı kalkıyor. Sayfanın üstü kısalıyor; şablon başlığı yerinde.
- **Kaybolan metin:** Gövde H1'i başlıktan uzunsa ekli metin (ör. "2026", alt başlık) sayfadan düşüyor. Örtüşme eşiği 0,3 olduğundan farklı anlamlı bir başlık kaldırılmaz, H2 olur. Bugünkü verideki 147 öndeki H1'in hepsi başlık varyantı.
- **Performans:** Sayfa başına birkaç regex; sorgu yok.
- **Geri dönüş:** Tek commit revert. Veri ve migration değişikliği yok.

# `content_updated_at` — Faz 0: Kanıt ve Güvenlik Analizi

Tarih: 18 Ekim 2026. Durum: **yalnız analiz.** Kod, migration, şema ve veri değişikliği yok; production'a yazılmadı.
Dayanak: `SEO_CONTENT_UPDATED_AT_DESIGN.md`, `SEO_RECOVERY_SB1_RESULT.md`, `SEO_RECOVERY_SB1_PRODUCTION_RESULT.md`.

## 0. Özet

| | Kayıt |
|---|---|
| Kapsam: yayımlanmış yazı (blog + haber) + yayımlanmış FAQ (yerel baseline) | **2.715** (772 yazı, 1.943 FAQ) |
| **A** — son içerik değişikliğinin tarihi kanıtla biliniyor | **600** (%22) |
| **B** — son değişiklik yalnız bir aralıkta biliniyor | **2.115** (%78) |
| **C** — bilinmiyor | **0** |
| **D** — kaynaklar çelişiyor | **0** (analiz sırasında 9 aday çıktı; hepsi yöntem hatasıydı, §2.4) |

Kritik sınır: Bu sınıflandırma **13.10 prod dökümü + sonrasında deploy edilen migration'lar** görünümüne göredir. 13.10'dan sonra yönetim panelinden yapılmış elle düzenlemeler yerelde görünmez. Uygulama aşamasında taze bir prod dökümüyle yeniden koşulmalıdır (§6).

## 1. Mevcut mimari — içerik yazma yolları

**İçerik alanları** (tasarımda önerilen):
- Yazı: `title`, `excerpt`, `content_md`, `meta_title`, `meta_description`.
- FAQ: `question`, `answer_md`.

`featured_image_caption` bu analizle **içerik dışına alındı** (§2.4).

| Yol | Yazma biçimi | Ne değişiyor | Sınıf | `updated_at` bugün |
|---|---|---|---|---|
| Filament `Posts` / `Faqs` kaynakları (yönetim paneli düzenleme) | Eloquent `save()` → `saving` kancası Markdown'dan HTML üretir | Metin | **Editoryal** | İlerler |
| `OfficialInfoRepair` metin alanları (OSV paketleri) | `$model->save()` | Metin | **Editoryal** (kaynaklı düzeltme) | İlerler |
| `OfficialInfoRepair` `html` bölümü | `DB::table()->update([html])` | Kayıtlı HTML'de sabit metin | Editoryal **veya** biçim (pakete göre) | **İlerlemez** |
| Veri migration'ları (`Post/Faq` modeliyle) — ör. `remove_emoji_from_faq_and_post_content` | `save()` | Metin (emoji temizliği) | Editoryal temizlik | İlerler |
| Veri migration'ları (`DB::table` ile) — ör. `remove_ai_generated_featured_images` | Doğrudan sorgu | `featured_image`, `featured_image_caption` | **Görsel üst verisi** (içerik değil) | İlerlemez |
| `blog:render-html --apply` (**her deploy'da** `_deploy.php` / `post-deploy.php` çalıştırıyor) | `DB::table('posts')->update(['content_html', 'reading_minutes', 'updated_at' => now()])` | Yalnız türetilmiş HTML | **HTML yeniden üretim** | **Elle ilerletiyor** (ayrı bir sahte tazelik kaynağı) |
| `content:fix-blog-links`, `content:repair-post-links`, `ResolvePostLinks` | `->update()` / `save()` / `DB::table` karışık | İç bağlantı hedefleri | **Bakım** (metin değil) | Yola göre |
| `BlogSplitPilot` / `BlogSplitAuto` | `DB::table` / `->update()` | HTML veya içerik bölme | Bakım | Yola göre |
| `TranslateFaqs`, `ImportTranslations` | `save()` / `->update()` | Çeviri metni | Editoryal (yeni dil içeriği) | Yola göre |
| `FaqEnglishContentBatch` | `DB::table('faqs')->update(['slug', 'updated_at' => $now])`, `translation_group_id` | Slug, grup | Yönetimsel (URL) | **Elle ilerletiyor** |
| `NewsReslugEnglish`, `NewsBackfillImages` | `->update(['slug'])`, `->update(['featured_image'])` | Slug / görsel | Yönetimsel | İlerler |
| `StatsReset`, sayaçlar (SB-1 sonrası `toBase()`) | Doğrudan sorgu | Sayaçlar | Görünür içerik değil | İlerlemez |
| OSV kontrol tarihi ("kontrol tarihi: …") | Metnin parçası (OSV paketleriyle) | Görünür metin | Kaynak doğrulama | — (§3.6) |

**Sonuç:** `updated_at` yalnız sayaçlar yüzünden değil, `blog:render-html`'in her deploy'da elle `updated_at = now()` yazması ve slug taşımaları yüzünden de içerik tarihini temsil etmiyor. Ayrı bir `content_updated_at` gerekli.

## 2. Tarih kanıtı analizi

### 2.1 Kaynaklar

| Kaynak | Ne | Güven |
|---|---|---|
| **Prod anlık görüntüleri** (phpMyAdmin dökümleri, MariaDB) | S0 = 25.05.2026 22:55 (24.05 17:47 dökümüyle `posts`/`faqs` verisi birebir aynı); S1 = 09.10.2026 09:08 (5 GB tam döküm); S2 = 13.10.2026 15:36 (`almanyauni-production-20261013.sql.gz`, baseline'ın kaynağı). Yalnız `posts`/`faqs` ayıklanıp geçici analiz DB'lerine yüklendi | Yüksek |
| **Güncel yerel görünüm** (L) | `almanyauni_baseline` = S2 + 13.10 sonrası deploy edilen veri migration'ları | Yüksek (13.10 sonrası panel düzenlemeleri hariç) |
| **Veri migration'ları** | 297 migration yazı/FAQ'a dokunuyor; her biri eklendiği commit'e ve **o commit'i içeren ilk başarılı deploy run'ına** eşlendi (GitHub Actions API, 806 run) | Yüksek (deploy zamanı) |
| **Yeniden oynatma** | Toplu migration'lar (`EmojiCleaner`, AI görsel kaldırma) S2 içeriğine yeniden uygulanıp L ile birebir karşılaştırıldı | Yüksek |
| **KB `kb_chunks`** (yerel) | Güncel içerikten `KbEmbed` ile aynı chunk özetleri hesaplandı ve yerel KB ile karşılaştırıldı. Yerel KB prod'un değil, eski yerel DB'nin ürünü (Haziran–Temmuz) | Yalnız çapraz kontrol |
| `created_at` | Kaydın oluşturulma zamanı | **Sınırlı**: 573 kayıtta geriye tarihli (§2.3) |
| `updated_at` (prod) | — | **Kullanılmadı** (kirli) |

### 2.2 Yöntem

Her kayıt için içerik özeti (normalize: CRLF→LF, kırpma) S0, S1, S2 ve L'de hesaplandı. Son farkın olduğu aralık:

1. **S2 → L arasında değişmiş** (13.10 sonrası): değişikliği açıklayan olaylar toplandı. Olaylar:
   - kaydın slug'ını tırnak içinde tek başına anan migration'lar (link içindeki geçişler sayılmaz),
   - yeniden oynatmayla doğrulanan emoji temizliği ve görsel kaldırma.

   Son olayın deploy zamanı → **A**. Hiç olay yoksa → D (sonuç: 0).
2. **S1 → S2 veya S0 → S1 arasında değişmiş:** → **B**, aralık = iki anlık görüntü arası. Slug atfı aralığı **daraltmak için kullanılmadı**, yalnız ipucu olarak saklandı (§2.4).
3. **Hiç değişmemiş:** ilk görüldüğü anlık görüntü F'den beri aynı → **B**, aralık = [`created_at`, F]. `created_at` önceki anlık görüntüden de eskiyse (o görüntüde kayıt yoksa) alt sınır önceki görüntü zamanı olur (§2.3).
4. **KB çapraz kontrol:** güncel içerik yerel KB ile eşleşiyorsa ve gömülme tarihi aralığın başından önceyse → D. Sonuç: 0.

### 2.3 Sonuçlar

| Tür / dil | A | B | C | D |
|---|---|---|---|---|
| Yazı TR | 75 | 191 | 0 | 0 |
| Yazı EN | 65 | 188 | 0 | 0 |
| Yazı DE | 66 | 187 | 0 | 0 |
| FAQ TR | 131 | 544 | 0 | 0 |
| FAQ EN | 132 | 507 | 0 | 0 |
| FAQ DE | 131 | 498 | 0 | 0 |
| **Toplam** | **600** | **2.115** | **0** | **0** |

**A (600):** Tarihler deploy anları:

| Deploy anı (UTC) | Kayıt |
|---|---|
| 14.10 19:52 (emoji temizliği; yeniden oynatmayla doğrulandı) | 427 |
| 15.10 14:50 (OSV Paket 2) | 86 |
| 15.10 19:36 (OSV Paket 2b) | 51 |
| 15.10 09:43 (OSV 1b) | 28 |
| 14.10 13:04 (OSV Paket 1) | 8 |

535 kayıtta tek olay, 73 kayıtta birden çok olay var (en geç olan seçildi; ör. post 5: OSV 1 → emoji → OSV 2b, son = 15.10 19:36).

**B (2.115):**

| Aralık | Kayıt |
|---|---|
| [`created_at`, 09.10] veya [25.05, 09.10] (S1'den beri değişmemiş) | 1.988'in çoğu |
| 01.07–09.10 (Temmuz FAQ topluluk hattı) | 1.170 |
| 25.05–09.10 (`created_at` geriye tarihli veya Mayıs öncesi kayıt) | 645 |
| 09.10–13.10 içinde değişmiş | 55 |
| 25.05–09.10 içinde değişmiş | 72 |

**Aralık genişliği:**

| Genişlik | Kayıt |
|---|---|
| ≤ 7 gün | 68 |
| 8–30 gün | 116 |
| 31–100 gün | 1.196 |
| > 100 gün | 735 |

- **`created_at` çelişkisi:** 573 kayıtta `created_at`, kaydın henüz bulunmadığı bir anlık görüntüden eski. Örnekler:
  - post 28/29 `university-education-in-germany-your-2026-start-guide-en/-de`: `created_at` 12.01.2026, ama 25.05'te prod'da yok.
  - post 23: `created_at` 07.05.2026, 25.05'te yok.

  Bu kayıtlar sonradan geriye tarihli eklenmiş; `created_at` tek başına alt sınır olamaz.
- **KB ipucu:** 90 B kaydında yerel KB, aralığın üst sınırını daraltabilecek bir eşleşme gösteriyor. Ancak yerel KB prod görüntüsü olmadığı için yalnız ipucu.

### 2.4 Belirsiz ve yöntem dersleri (örnekler)

- **Slug atfı yanıltıcı olabilir.** `2026_09_30_000100_faq_parity_batch_a` 9 TR FAQ'ın (220, 223, 241, 247, 251, 257, 258, 260, 263) slug'ını anıyor. Aralık bununla 28.09'dan başlatılınca yerel KB'nin aynı metni 29.06'da gömmüş olmasıyla "çelişki" doğdu. Gerçekte migration bu FAQ'ların metnini değiştirmemiş (çeviri grubu bağlamış). **Ders:** slug atfı aralığı daraltmaz; yalnız ipucu.
- **Görsel başlığı içerik değil.** 8 yazıda (ör. 17, 50, 51 `visa-process-for-an-english-masters…`) tek değişiklik AI görselinin ve başlığının silinmesiydi. KB metni Haziran'dan beri aynı. `featured_image_caption` içerik alanlarından çıkarıldı; bu yazılar B oldu.
- **Toplu migration'lar slug anmaz.** İlk geçişte 428 kayıt "açıklanamayan" görünüyordu; emoji (420) ve görsel kaldırma (8) yeniden oynatmayla birebir doğrulandı.
- **Pencere içinde değişmiş B örnekleri** (09.10–13.10):
  - post 11/43 WG rehberi (`content_md`; ipucu: batch4 migration),
  - post 32 ücret rehberi EN (`excerpt`, `content_md`, `meta_description`; ipucu: 10.10 link migration'ı).

  İpuçları doğru olabilir, ama metin değişikliği o migration'la kanıtlanmadıkça aralık daraltılmadı.

## 3. Mimari güvenlik (tasarım kesinleştirme)

### 3.1 Şema

| Tablo | Sütun | Tür | Not |
|---|---|---|---|
| `posts`, `faqs` | `content_updated_at` | `TIMESTAMP NULL`, indeksli | Kesin tarih (A veya bundan sonraki editoryal değişiklik). Bilinmiyorsa `NULL` |
| `posts`, `faqs` | `content_updated_floor` | `TIMESTAMP NULL` | B için **kanıtlanmış alt sınır** (gerçek tarih ≥ bu). Kesin tarih değildir |
| `posts`, `faqs` | `content_date_source` | `VARCHAR(32) NULL` | `migration_deploy`, `editor`, `snapshot_floor`, `published_fallback` |
| `posts`, `faqs` | `content_hash` | `CHAR(64) NULL` | İçerik alanlarının sha256'sı; "aynı metni yeniden kaydetme" tarih oynatmaz |

Hepsi nullable; mevcut sorgular ve sayfalar sütunlar boşken değişmez. `down()` sütunları düşürür; kaynak alanlar dokunulmaz.

### 3.2 Yazma kuralları

- **`saving` kancası:** içerik alanlarının özeti değiştiyse `content_updated_at = now()`, `content_date_source = 'editor'`, `content_hash` güncellenir.
- **Model olayı dışındaki yollar:**
  - `OfficialInfoRepair` `html`: metin düzeltmesiyse aynı sorguya `content_updated_at` eklenir; paket JSON'ında `counts_as_content: true/false` alanı.
  - `blog:render-html`: **`updated_at = now()` kaldırılmalı** (ayrı küçük düzeltme önerisi; içerik değişmiyor).
  - Bağlantı onarım ve bölme komutları içerik tarihi **ilerletmez** (bakım).
  - Çeviri içe aktarma (yeni dil metni) ilerletir.
- **Sayaçlar:** SB-1 ile çözüldü (`toBase()`).

### 3.3 SEO alanları — okuma kuralı (fallback politikası)

`etkin_tarih = content_updated_at ?? max(published_at, content_updated_floor)`

| Alan | Kural |
|---|---|
| Sitemap `lastmod` (yazı, haber, FAQ) | `etkin_tarih` (gün) |
| JSON-LD `dateModified` | `etkin_tarih`; `content_updated_at` yoksa ve `etkin_tarih = published_at` ise `dateModified` = `datePublished` |
| `<meta article:modified_time>` | `dateModified` ile aynı |
| "Güncellendi …" etiketi | **Yalnız `content_updated_at` doluysa** ve `> published_at + 1 gün`. Alt sınır veya fallback ile asla "güncellendi" denmez |
| FAQ "son güncellenenler" | `orderByDesc(content_updated_at)` (boşlar sonda) |

**Fallback'in güvenli olduğu koşullar:**
- `published_at` fallback'i yalnız kesin tarih yoksa kullanılır. Hiçbir zaman "bu tarihte içerik değişti" anlamında etiketlenmez.
- `floor` yalnız kanıtla (anlık görüntü sınırı veya doğrulanmış `created_at`) atanır. B'deki **aralık sonu hiçbir zaman tarih olarak kullanılmaz**.
- "Taslak sonra yayımlandı" durumunda `published_at` gerçek son değişiklikten biraz sonra olabilir. Bu, sayfanın yayına girdiği gerçek bir olay olduğu için kabul edilir.

### 3.4 İlk veri aktarımı (idempotent)

1. **Komut:** `content:backfill-dates --dry-run`. Bu analizin mantığıyla, **taze prod dökümü** ve deploy run listesi üzerinden kayıt başına sınıf, kanıt ve önerilen değeri JSON/CSV olarak üretir.
2. **Onay:** Rapor onaylanınca kanıt dosyası (`database/data/content-dates/<tarih>.json`: id, sınıf, `content_updated_at` | `floor`, kaynak, kanıt) repoya girer.
3. **Data-migration:**
   - Yalnız `content_updated_at IS NULL AND content_updated_floor IS NULL` kayıtlara yazar (ikinci çalıştırma no-op).
   - `expect_hash` ile kaydın içerik özetini doğrular; farklıysa (arada düzenlenmiş) atlar ve raporlar.
4. **Geri dönüş:** Üç sütunu `NULL`'a çekmek; okuma `published_at`'e düşer. `updated_at` ve içerik dokunulmaz.

### 3.5 Yayın sırası (her adım ayrı onay, ayrı commit/deploy)

1. **Faz 1:** şema (nullable) + yazma kancası + `blog:render-html` düzeltmesi. Okuma henüz eski alanlardan. Testler: editoryal kayıt ilerletir; HTML yeniden üretim, bağlantı onarımı, sayaç, slug taşıma ilerletmez.
2. **Faz 2:** okuma noktaları (sitemap, JSON-LD, meta, etiket, FAQ sırası) yeni kurala geçer. Sütunlar boş olduğu için tarihler `published_at`'e düşer; bu yanlış değil, kısa süreli bir geri gidiş. Testler: fallback kuralları, "Güncellendi" yalnız kesin tarihte.
3. **Faz 3:** taze prod dökümüyle kuru çalıştırma → rapor → onaylı backfill data-migration.

### 3.6 OSV tarihlerinin ayrılığı

- "Kontrol tarihi" metni bir bilginin **doğrulandığı** gündür; içerik değişikliği değildir.
- Yalnız kontrol tarihi güncelleyen bir OSV düzenlemesi metni değiştirdiği için teknik olarak `content_updated_at`'i ilerletir. Paket JSON'ında `counts_as_content: false` ile bu bilinçli olarak dışarıda bırakılabilir. Öneri: kontrol tarihi yenilemesi tek başına "Güncellendi" göstermesin.
- `ProgramVerification.verified_at`/`checked_at` ve sağlayıcı doğrulama sütunları bu tasarımın dışında; dokunulmaz.

## 4. 19.10 takip planı (SB-1 sitemap `lastmod`)

**Yöntem:** Yalnız 3 sitemap isteği (`/sitemap-tr.xml`, `-en`, `-de`). Sayfa açılmadığı için sayaç ve `updated_at` etkilenmez. Sitemap önbelleğe alınmıyor; `lastmod` gün hassasiyetinde.

**Betik:** `scratchpad/sb1/sitemap_lastmod_check.py`. Kalıcılık istenirse `scripts/` altına alınır.

```
python -I -X utf8 sitemap_lastmod_check.py lastmod_2026-10-19.json lastmod_2026-10-18.json
```

**18.10 taban** (≈10:xx UTC; deploy 07:43 UTC):

| Tür | `lastmod` 2026-10-16 | 2026-10-17 | 2026-10-18 |
|---|---|---|---|
| FAQ | 341 | 687 | **812** |
| Blog | 96 | 324 | **214** |
| Haber | — | 25 | **16** |

9 örnek sayfanın hepsi 2026-10-18.

**Beklenen 19.10 sonucu:**
- `2026-10-18` sayıları **aynı kalmalı** (812 / 214 / 16). Deploy'dan önceki ziyaretlerle donmuş durumdalar; ziyaret artık ilerletmiyor.
- `2026-10-19` sayısı **≈ 0** olmalı. İstisnalar meşru: o gün yönetim panelinden düzenleme, içerik migration'ı veya deploy'da `blog:render-html`'in düzelttiği yazılar (bu komut hâlâ `updated_at = now()` yazıyor, §1).
- 9 örnek sayfa: `2026-10-18` kalmalı.

**Yorum kuralı:**
- 19.10'da yüzlerce `2026-10-19` görünürse → gerileme; dur ve incele.
- Birkaç tane ise → hangi kayıtlar olduğunu panelden veya deploy günlüğünden kontrol et.
- Kesinlik sınırı: Bu ölçüm, ziyaretin `lastmod`'u ilerletmediğini gösterir; tek tek kaydın `updated_at` değerini doğrudan kanıtlamaz (prod DB'ye salt okunur erişim yok).

**Kontrol otomatik zamanlanmadı.**

## 5. Disk analizi (salt okunur)

C: 476 GB, kullanılan ≈460 GB, boş ≈16 GB (%97). Ölçüm `du` ile; **`C:\Windows` ve `AppData` 10 dakikalık zaman aşımına takıldı, ölçülemedi.** Listelenen klasörler ≈230 GB tutuyor; farkın büyük kısmı büyük olasılıkla `Windows`, `AppData`, `pagefile`/`hiberfil` ve sistem dosyaları.

**En büyük klasörler:**

| Klasör | Boyut |
|---|---|
| `C:\Users\Yapra\Downloads` | **80,8 GB** |
| `C:\Users\Yapra\OneDrive` (yerel eşitleme) | 45,2 GB |
| `C:\Users\Yapra\Videos` | 27,9 GB |
| `C:\Program Files` | 22,9 GB |
| `C:\Program Files (x86)` | 22,3 GB |
| `C:\ProgramData` | 10,1 GB |
| `C:\Users\Yapra\uni finder_Claude` (repo + `vendor`/`node_modules`) | 5,3 GB |
| `C:\laragon\data` (MySQL verisi) | 3,5 GB |
| `C:\Users\Yapra\AppData\Local\Temp` | 3,3 GB |
| `C:\laragon\backup` | 2,7 GB |
| `C:\Users\Yapra\db-backups` | 1,7 GB |

**`Downloads` içinde:**
- `free kurs eng/`: 25,8 GB (2023 ders videoları).
- `OneDrive-2023-01-26.zip`: 5,2 GB.
- `applytogerman-backup-2026-10-08.sql`: 5,0 GB, prod dökümü, **kişisel veri içeriyor**.
- `MineBattlesv3.5.rar`: 3,8 GB.
- `_R_N G_RSELLER_` (zip + klasör): ≈4,5 GB.
- `CYBER FREE/`: 2,0 GB.
- `Adobe Photoshop CC 2019 … .rar`: 1,6 GB.
- Kurulum dosyaları (`.exe`): 1,8 GB.

**Güvenle temizlenebilecek aday dosyalar** (karar sizin; hiçbiri silinmedi veya taşınmadı):

| Aday | Boyut | Not |
|---|---|---|
| Eski binlog dosyaları (`C:\laragon\data\mysql-8.4\binlog.000085`, `.000086`, `binlog.index`) | 1,4 GB | Binlog kapalı; MySQL durdurulup yedek klasöre taşınabilir |
| `%LOCALAPPDATA%\Temp` | 3,3 GB | Geçici dosyalar; bu oturumun scratchpad'i (≈0,5 GB) dahil. Oturum kapanınca silinebilir |
| Geçici analiz DB'leri `zz_evidence_*` | <0,1 GB | Faz 0 onaylanınca `DROP DATABASE` (bu analizin ürünü) |
| `laragon\backup` | 2,7 GB | Laragon'un otomatik yedekleri; tarihleri kontrol edilip eskiler ayıklanabilir |
| `Downloads` arşiv ve kurulum dosyaları (2022–2024 `.rar`/`.zip`/`.exe`) | ≈12 GB | Kişisel; yalnız siz karar verirsiniz |
| `Downloads\free kurs eng` ve `Videos` | 25,8 + 27,9 GB | Harici diske veya buluta taşınabilir |
| OneDrive yerel kopyası | 45,2 GB | OneDrive "Yer aç / Files On-Demand" ile buluta bırakılabilir (veri silinmez) |
| Mayıs prod dökümlerinin 2 kopyası (`d0471a0e (1).sql`, `(2).sql`) | 2 × 121 MB | `posts`/`faqs` bakımından 24.05 dökümüyle aynı; diğer tablolarda fark var |

**Veri güvenliği notu:** `Downloads\applytogerman-backup-2026-10-08.sql` (5 GB) kişisel veri içeren bir prod dökümü. `docs/LOCAL_DB_BASELINE.md` bu tür dosyaların `~/db-backups` altında tutulmasını öngörüyor. Faz 3'e kadar kanıt olarak saklanmalı; `Downloads`'tan taşınması önerilir.

## 6. Riskler ve geri dönüş

| Risk | Önlem |
|---|---|
| 13.10 sonrası panel düzenlemeleri bu analizde görünmüyor | Faz 3'te taze prod dökümü; `expect_hash` ile arada değişen kayıt atlanır |
| Deploy anı ≠ migration'ın prod'da çalıştığı an (FTPS yeniden denemesi, webhook zaman aşımı) | Run `updated_at` deploy bitişi; dakika düzeyinde sapma kabul. Webhook zaman aşımı olan run'lar ayrıca işaretlenir |
| Slug atfının yanlış pozitifleri | Aralık daraltmada kullanılmaz; A yalnız 13.10 sonrası değişen kayıtlarda ve olaylar yeniden oynatma veya slug ile tutarlıysa |
| `created_at` geriye tarihli (573) | Alt sınır = önceki anlık görüntü |
| Toplu tarih geri gidişi (Faz 2'de `lastmod` çoğu yazıda `published_at`'e iner) | Bu doğru yöne bir düzeltmedir. GSC'de tarama sıklığı izlenir; Faz 3 kısa süre içinde |
| `blog:render-html` deploy'da `updated_at` yazıyor | Faz 1'de düzeltilir; o zamana kadar 19.10 ölçümünde istisna olarak okunur |
| **Geri dönüş** | Faz 1: sütunları düşür (`down()`), kodu revert. Faz 2: okuma kuralını revert. Faz 3: üç sütunu `NULL`'a çek. Hiçbir aşamada `updated_at` veya içerik değişmez |

## 7. Bu analizde kullanılan geçici veriler

- Geçici analiz veritabanları (yerel MySQL): `zz_evidence_20260525`, `zz_evidence_20261009`, `zz_evidence_20261013` (yalnız `posts` ve `faqs`). Mevcut veritabanlarına dokunulmadı.
- Ara dosyalar oturum scratchpad'inde (`cu0/`).
- Prod'a yalnız okuma: GitHub Actions API ve 3 sitemap isteği. Hiçbir sayfa açılmadı.

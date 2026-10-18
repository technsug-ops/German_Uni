# SEO Recovery SB-1 — Görüntüleme Sayacı İçerik Tarihini Değiştiriyor (yerel)

Tarih: 18 Ekim 2026. Durum: **yerelde uygulandı ve test edildi; yayın onaylandı (18.10). Canlı sonuç: `SEO_RECOVERY_SB1_PRODUCTION_RESULT.md`.**
Dayanak: `SEO_BACKLOG.md` SB-1, `SEO_RECOVERY_P1_1_PACKAGE_B_PRODUCTION_RESULT.md` §5.

## 1. Kök neden

Controller'lar sayaçları Eloquent sorgu oluşturucusuyla artırıyordu:
- `Model::where('id', …)->increment('view_count')`
- `$model->increment(...)`

Eloquent `increment()` modelde zaman damgası açıksa **`updated_at = now()`'u da** sorguya ekler. Yani her ziyaret ve her "faydalı mı" oyu içeriğin "son değişiklik" tarihini o ana çekiyordu.

- **Ne zamandan beri:** Hata ilk commit'ten beri var (`266dae5`, 2026-05-26).
- **Model olayları:** Bu çağrılar `saving`/`updating` model olaylarını tetiklemiyor. `Post` ve `Faq`'taki `saving` kancaları yalnız `content_md`/`answer_md` değişince HTML üretiyor; sayaç onları çalıştırmıyordu. Sorun yalnız zaman damgasıydı.

**Yerel kanıt** (yerel DB, transaction içinde, sonra geri alındı):

| Model | Eski çağrı: `where()->increment()` | Yeni çağrı: `where()->toBase()->increment()` |
|---|---|---|
| Post | view +1, `updated_at` 2026-10-17 09:15 → **2026-10-18 00:40 (değişti)** | view +1, `updated_at` **sabit** |
| Faq | view +1, `updated_at` 2026-06-29 → **bugün (değişti)** | view +1, **sabit** |
| JobPosting | view +1, `updated_at` 2026-06-26 → **bugün (değişti)** | view +1, **sabit** |

## 2. Etkilenen sayaçlar ve SEO alanları

| Çağrı | `updated_at` nereye gidiyor | Durum |
|---|---|---|
| `BlogController::show` — yazı görüntüleme | Sitemap `lastmod`, JSON-LD `dateModified` (`Seo::article`), `<meta article:modified_time>`, sayfadaki "Güncellendi" etiketi | **Düzeltildi** |
| `NewsController::show` — haber görüntüleme | Sitemap `lastmod`, `dateModified`, `article:modified_time` | **Düzeltildi** |
| `FaqController::show` — FAQ görüntüleme | FAQ sitemap `lastmod`; FAQ ana sayfasındaki "son güncellenenler" sıralaması (`orderByDesc('updated_at')`) | **Düzeltildi** |
| `BlogController::feedback`, `FaqController::feedback` — faydalı/faydasız oyu | Yukarıdakilerin aynısı | **Düzeltildi** |
| `JobController::show` — iş ilanı görüntüleme | İş ilanı sitemap `lastmod` | **Düzeltildi** |
| `PartnerController::click` (dil kursu / tercüme bürosu) | Sitemap'te ve sayfada kullanılmıyor | Dokunulmadı (SEO etkisi yok) |
| `PopupController`, `routes/web.php` popup izleme | Sitemap'te ve sayfada kullanılmıyor | Dokunulmadı |
| `UniversityReviewController` yorum oyu | Yorum `updated_at`'i sitemap'te ve şemada kullanılmıyor | Dokunulmadı |
| `ApiThrottleAndLog`, `ThrottleScrapers` | Önbellek sayacı, DB değil | İlgisiz |

**Etkilenmeyen tarih alanları:**
- **OSV doğrulama tarihleri:** `ProgramVerification` ve sağlayıcı modellerindeki `verified_at`/`checked_at` ayrı sütunlar. İçerikteki "kontrol tarihi" metinleri de gövdenin parçası; sayaçlar bunlara hiç dokunmuyordu.
- **Önbellek:** Sayfa önbelleği `updated_at`'e bağlı değil. `SeoIntents` yazı haritası ve sitemap'in kendi önbelleği içerik alanlarından bağımsız.

## 3. Kod değişikliği

Tek ilke: sayaç artışı sorgu oluşturucuya iner (`->toBase()->increment(...)`). Bu tek bir `UPDATE … SET view_count = view_count + 1` sorgusudur; atomik kalır, eşzamanlı isteklerde sayım kaybı olmaz. `updated_at` eklenmez, model olayı yoktur (önceki davranışla aynı).

| Dosya | Değişiklik |
|---|---|
| `app/Http/Controllers/Web/BlogController.php` | Görüntüleme ve oy: `Post::where(...)->toBase()->increment(...)` |
| `app/Http/Controllers/Web/NewsController.php` | Görüntüleme: aynı |
| `app/Http/Controllers/Web/FaqController.php` | Görüntüleme ve oy: `Faq::where(...)->toBase()->increment(...)` |
| `app/Http/Controllers/Web/JobController.php` | `$job->increment()` → `JobPosting::whereKey(...)->toBase()->increment(...)` (iş sayfası `view_count`'u göstermiyor; bellek içi değer gerekmiyor) |
| `tests/Feature/ViewCounterTimestampsTest.php` (yeni) | 13 test |

**Değişmeyenler:**
- Şema ve migration; veritabanı verisi.
- Analitik davranışı: her GET ve her oy +1. Önizleme modunda sayım yok.
- Model olayları; içerik düzenlendiğinde `updated_at`'in ilerlemesi.

## 4. Testler

`ViewCounterTimestampsTest`: **13/13** (yeni kodla).

Testlerin hatayı yakaladığı kanıtlandı: değişiklik geçici olarak geri alınıp eski kodla koşulduğunda **12/13 düştü**. Geçen tek test "gerçek düzenleme `updated_at`'i ilerletir"; beklenen bu.

| Davranış | Test |
|---|---|
| Blog GET ×2: `view_count` +2, `updated_at` sabit; `dateModified` = gerçek düzenleme tarihi; canonical, hreflang ve `dateModified` iki ziyarette aynı; sayfada "bugün" tarihi yok (TR/EN/DE) | `test_blog_view_counts_but_keeps_updated_at_and_seo_dates` ×3 |
| Haber GET: `view_count` +, `updated_at` ve `article:modified_time` sabit (TR/EN/DE) | `test_news_view_counts_but_keeps_updated_at_and_modified_time` ×3 |
| FAQ GET: `view_count` +1, `updated_at` sabit (TR/EN/DE) | `test_faq_view_counts_but_keeps_updated_at` ×3 |
| Yazı ve FAQ oyu: sayaç +1, `updated_at` sabit | `test_helpful_votes_keep_updated_at` |
| İş ilanı GET: `view_count` +1, `updated_at` sabit | `test_job_view_keeps_updated_at` |
| Sitemap `lastmod` görüntüleme sonrası aynı | `test_sitemap_lastmod_is_not_moved_by_views` |
| Gerçek içerik düzenlemesi `updated_at`'i ilerletir | `test_real_content_edit_still_moves_updated_at` |

Tam paket ve i18n: bkz. §8.

## 5. Önce / sonra (canlı davranış beklentisi)

| | Önce (canlı, 17.10'da doğrulandı) | Sonra |
|---|---|---|
| Bir ziyaret sonrası `dateModified` | Ziyaret anı (ör. 17.10 08:33) | Son gerçek düzenleme |
| Sitemap `lastmod` | Son ziyaret günü | Son gerçek düzenleme günü |
| "Güncellendi …" etiketi | Ziyaret günü | Son gerçek düzenleme |
| Canlı GET doğrulaması | `updated_at`'i değiştirir | Yalnız `view_count`'u artırır |

**Not:** Düzeltme yalnız bundan sonraki ziyaretleri durdurur. Mevcut `updated_at` değerleri hâlâ son ziyaret anını gösterir (§6).

## 6. Bozulmuş eski tarihler — güvenli onarım seçenekleri (uygulanmadı)

Durum: Hata ilk günden beri var. Bu yüzden prod'daki ve tüm yedeklerdeki `updated_at` değerleri "son ziyaret veya oy anı" ile kirli. Tahminle geriye almak yapılmadı ve önerilmiyor. Kanıta dayalı kaynaklar:

| Kaynak | Ne verir | Güven | Kapsam |
|---|---|---|---|
| **A. İçerik değiştiren veri migration'ları** (git'te, dosya adında tarih; OSV paketleri, içerik düzeltmeleri, FAQ parity vb. — ≈75 dosya) | Etkilediği her kaydın (slug/kimlik listesi JSON'da) **kesin değişiklik günü** (= deploy günü; git commit + Actions run zamanı) | Yüksek | Yalnız migration'la düzeltilen kayıtlar |
| **B. Prod SQL dökümleri** (`Downloads/applytogerman-backup-2026-10-08.sql`, `d0471a0e*.sql` ve 13.10 baseline) | İki döküm arasında `content_md`/`title`/`meta_*` özeti değiştiyse değişiklik **o aralıkta**; değişmediyse içerik o aralıkta sabit | Yüksek (aralık olarak) | Döküm tarihleri arası |
| **C. KB `kb_chunks.embedded_at` + `content_hash`** | Embed artımlı ve özet bazlı: şu anki içeriğin embed edildiği an ≥ son içerik değişikliği | Orta (üst sınır) | KB'ye giren yazı ve FAQ'lar (≈29.06'dan beri) |
| **D. `published_at` / `created_at`** | Alt sınır; hiç düzenlenmemiş kayıtlarda gerçek tarih | Yüksek (alt sınır) | Hepsi |
| ~~Prod `updated_at`~~ | Kirli | — | Kullanılmamalı |

**Önerilen yöntem (ayrı paket, onaylı):**
1. Şemaya `content_updated_at` sütunu ekle (migration gerekir; bu pakette yapılmadı).
   - `saving` kancasında yalnız içerik alanları (`title`, `content_md`, `excerpt`, `meta_*`; FAQ'ta `question`, `answer_md`) değişince güncellensin.
   - Sitemap `lastmod`, `dateModified`, `article:modified_time` ve "Güncellendi" bu alandan okunsun.
2. Başlangıç değerini kanıta göre doldur. Her kayıt için en yüksek kesin kanıt:
   - A'daki son migration günü;
   - B'de içeriğin değiştiği son döküm aralığının bitişi (yalnız "değişti" kanıtı varsa);
   - kanıt yoksa `published_at`.

   C yalnız çapraz kontrol olarak kullanılır.
3. Önce kuru çalıştırma raporu: kaç kayıt hangi kanıtla, örnekler. Sonra onaylı migration.

**Neden basit "updated_at = published_at" değil:** OSV ve içerik düzeltmeleriyle gerçekten güncellenmiş yüzlerce kaydın tazelik bilgisi kaybolur.

## 7. Yerel MySQL binlog riski (öneri; konfigürasyon değiştirilmedi)

**Mevcut durum:**
- `C:\laragon\bin\mysql\mysql-8.4.3-winx64\my.ini` içinde binlog ayarı **yok**; MySQL 8.4 varsayılanları geçerli:
  - `log_bin=ON`, `binlog_expire_logs_seconds=2592000` (30 gün),
  - `max_binlog_size=1 GB`, `binlog_format=ROW`, `sync_binlog=1`.
- Replikasyon yok: `SHOW REPLICAS` 0 satır, replica durumu boş. GTID kapalı.
- 17.10 temizliğinden bu yana yalnız test koşuları binlog'u yeniden **≈656 MB**'a çıkardı. Bir tam test paketi yüz MB'lar–GB'lar yazıyor; 30 günde onlarca GB birikebilir.

**İhtiyaç değerlendirmesi:**
- Bu makinede replikasyon yok.
- Zamanda nokta geri dönüşü (PITR) için düzenli bir taban yedeği ve binlog arşivleme süreci de görünmüyor; yerel veriler prod kopyası (prod'dan yeniden alınabilir).
- Yerel geliştirme için binlog'a ihtiyaç yok.

**Öneri (kullanıcı uygular veya onaylar):**
1. **Seçenek A — binlog'u kapat (önerilen):** `my.ini` `[mysqld]` bölümüne `disable_log_bin` (veya `skip-log-bin`) ekle → Laragon'dan MySQL'i yeniden başlat. Doğrulama: `SELECT @@log_bin;` → 0. Sonra kalan dosyalar `PURGE BINARY LOGS` ile değil, MySQL kapalıyken `binlog.*` + `binlog.index` taşınarak (silmeden önce yedek klasöre) temizlenebilir.
2. **Seçenek B — süreyi kısalt:** `binlog_expire_logs_seconds=86400` (1 gün) ve isteğe bağlı `max_binlog_size=268435456` (256 MB). Disk riski günlük yazımla sınırlı kalır; PITR kısa pencereyle mümkün kalır.
3. **Ek:** Önemli yerel veritabanları için `mysqldump` ile yedek. Binlog yedek yerine geçmez.

**Geri dönüş:** Eklenen satır `my.ini`'den silinir ve MySQL yeniden başlatılır; varsayılanlar (binlog açık, 30 gün) geri gelir. Veritabanı verisine etkisi yok.

## 8. Tam paket

| Kontrol | Sonuç |
|---|---|
| Tam test paketi (`GEMINI_API_KEY` boş) | **751/751**, 83.890 doğrulama (önceki 738 + yeni 13) |
| `i18n:audit --leaky` | TR 0, DE 0 |

## 9. Riskler ve geri dönüş

- **Sitemap davranışı:** Düzeltmeden sonra çoğu yazının `lastmod`'u değişmeyi bırakacak. Google sitemap'i daha az sıklıkta tarayabilir; bu doğru davranış. İçerik güncellemeleri (migration ile yapılanlar dahil, çünkü `save()`/`update` zaman damgası günceller) `lastmod`'u ilerletir.
- **OfficialInfoRepair (OSV migration'ları):** Metin alanı düzenlemeleri `$model->save()` ile yazılıyor, `updated_at` ilerlemeye devam eder. İstisna: yalnız kayıtlı HTML'deki sabit metni değiştiren `html` bölümü doğrudan `DB::table()->update()` ile yazıyor ve tarihi ilerletmiyor (önceden de böyleydi). Davranış değişmedi; `content_updated_at` paketinde bu yol da ele alınmalı.
- **Eski değerler:** Mevcut kirli `updated_at` değerleri olduğu gibi kalır (son ziyaret anı). Bu paket yalnız yeni bozulmayı durdurur; onarım §6'daki ayrı paket.
- **Geri dönüş:** Tek commit revert. Şema ve veri değişikliği yok.

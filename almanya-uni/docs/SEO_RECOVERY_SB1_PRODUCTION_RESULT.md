# SEO Recovery SB-1 — Canlıya Alma Sonucu + Yerel MySQL Binlog

Tarih: 18 Ekim 2026. Dayanak: `SEO_RECOVERY_SB1_RESULT.md`.

| Alan | Durum |
|---|---|
| Commit | ✅ `c5ddcb4` fix(seo): prevent view counters from changing content timestamps (6 dosya) |
| Push edilen commit'ler | ✅ Yalnız `c5ddcb4` (`6772dbf..c5ddcb4`) |
| CI | ✅ Run 39108480479: Safety-net tests ✓, i18n gate ✓ |
| FTPS | ✅ 1. denemede (07:43:01–07:43:13 UTC) |
| Server-side deploy | ✅ 07:43:13–07:43:17 UTC |
| Canlı davranış (blog/haber) | ✅ Ziyaret `dateModified`'ı artık değiştirmiyor (dolaylı kanıt, §3) |
| Canlı davranış (FAQ) | ⚠️ Sayfada tarih alanı yok; kanıt kod ve testlerden (§3) |
| Geçmiş tarihler | Değiştirilmedi |
| Yerel MySQL binlog | ✅ Kapatıldı (`log_bin=0`); veritabanları sağlam, yedekler doğrulandı |

## 1. Yayın öncesi

**Git:**
- `main` = `origin/main` (`6772dbf`), bekleyen commit yoktu.
- Diff yalnız 6 sayaç satırı. Commit'e giren 6 dosya:
  - `app/Http/Controllers/Web/BlogController.php`
  - `app/Http/Controllers/Web/NewsController.php`
  - `app/Http/Controllers/Web/FaqController.php`
  - `app/Http/Controllers/Web/JobController.php`
  - `tests/Feature/ViewCounterTimestampsTest.php`
  - `docs/SEO_RECOVERY_SB1_RESULT.md`
- Dışarıda kalanlar: forum önbelleği, `scripts/`, `.env`, SQL yedekleri, plan ve rapor belgeleri, `SEO_BACKLOG.md`, `SEO_CONTENT_UPDATED_AT_DESIGN.md`.

**Kod kontrolü:**
- Sayaç ve oy artışları `->toBase()->increment(...)`: tek atomik `UPDATE … = col + 1`, `updated_at` yok.
- Gerçek içerik düzenlemesi `updated_at`'i ilerletiyor (testli).
- Sayım davranışı aynı: her GET ve her oy +1; önizlemede sayım yok.

**Testler:**

| Kontrol | Sonuç |
|---|---|
| Hedefli: `ViewCounterTimestampsTest`, `ContentHeadingsTest`, `BlogRenderHtmlTest`, `SmokeTest`, `HreflangConsistencyTest`, `SitemapLocaleCoverageTest`, `SeoIntentLinksTest`, `SeoGrowthBatch3/4Test`, `SoftNotFoundP04Test` | 163/163 |
| Tam paket (`GEMINI_API_KEY` boş) | 751/751 |
| `i18n:audit --leaky` | TR 0, DE 0 |
| `npm run build` | ✓ |
| pre-push hook (i18n + tam test) | ✓ |

## 2. CI ve deploy

GitHub Actions run 39108480479:
- Safety-net tests ✓ (07:40:30–07:42:30 UTC), i18n leak gate ✓.
- Build & Deploy ✓. FTPS 1. denemede (12 sn). Server-side deploy 07:43:13–07:43:17 UTC. Deploy özeti ✓.

## 3. Canlı doğrulama

**Kanıt sınırı:** Production veritabanına salt okunur erişimim yok. Yönetim paneli ve DB sorgusu kullanılmadı. Aşağıdaki canlı gözlem dolaylı kanıttır; kesin kanıt §1'deki kod ve testler (eski kodla 13 testten 12'si düşüyor).

**Yöntem** (9 sayfa: TR/EN/DE × blog, haber, FAQ; yalnız GET, ≈1,5 sn aralık):
1. Sayfa, `updated_at`'i istek **işlenmeden önce** okuyup basıyor; sayaç sonra artıyor. Bu yüzden `dateModified`, bir önceki ziyaretin tarihi değiştirip değiştirmediğini gösterir.
2. **Ön kayıt** (07:25 UTC, eski kod): bu ziyaretler eski kodla `updated_at`'i 07:25'e çekti.
3. **1. tur** (07:44, yeni kod): `dateModified` = 07:25 (ön kaydın izi).
4. **2. tur** (07:46): 1. tur ziyaretleri eski kodla olsaydı 07:44 görünürdü.

| Sayfa | Ön kayıt | 1. tur | 2. tur | Sonuç |
|---|---|---|---|---|
| Blog TR / EN / DE | 23:50 / 07:42 / 23:50 (önceki ziyaretler) | 09:25:38 / :40 / :42 (yerel saat) | **aynı** | Ziyaret tarihi değiştirmedi |
| Haber TR / EN / DE | 23:50 / 08:32 / 08:50 | 09:25:43 / :45 / :47 | **aynı** | Ziyaret tarihi değiştirmedi |
| FAQ TR / EN / DE | — | — | — | Sayfada `dateModified` yok; ölçülemez |

**9/9 sayfa:**
- HTTP 200; title, canonical ve hreflang üç ölçümde de aynı.
- Tek H1 (Paket B korunuyor).
- Sitemap `lastmod` hepsinde 2026-10-18. Bugün zaten yayın öncesi ziyaretlerle 18.10'a çekilmişti; gün hassasiyetinde bugün ayırt edilemez. **Yarın (19.10)** aynı sayfalarda `lastmod` 2026-10-18'de kalmalı.

**Takip:** 19.10'da 9 sayfanın sitemap `lastmod`'u ve FAQ sitemap satırları tek GET ile kontrol edilecek. Beklenen: 2026-10-18 (yeni ziyaretler ilerletmiyor).

## 4. Geçmiş tarihler

Değiştirilmedi. Prod'daki `updated_at` değerleri son eski-kod ziyaretinin anında donmuş durumda. Bu örnekte bugün 07:25; genelde her sayfanın SB-1 öncesi son ziyareti. Kanıta dayalı onarım tasarımı: `SEO_CONTENT_UPDATED_AT_DESIGN.md` (uygulanmadı).

## 5. Yerel MySQL binlog

**Ön kontroller:**

| Kontrol | Sonuç |
|---|---|
| Çalışan sunucu | `mysqld` PID 2204 (3306'yı dinliyor) + Windows izleyici süreci PID 17012; Laragon başlatmış, `--defaults-file` yok |
| Kullanılan `my.ini` | `C:\laragon\bin\mysql\mysql-8.4.3-winx64\my.ini` (Windows/C:\ altında başka `my.ini`/`my.cnf` yok) |
| Replikasyon | Yok: `SHOW REPLICAS` 0, `SHOW REPLICA STATUS` 0 satır |
| PITR | Yok: binlog arşivi ve taban yedek süreci yok; binlog yalnız aktif dosyalardan ibaret |
| Veritabanı sağlığı | `mysqlcheck --check`: `almanyauni`, `almanyauni_baseline`, `almanyauni_forum` 271/271 OK; `almanya_uni` 16/16 OK |
| Yedek | `C:\Users\Yapra\db-backups\2026-10-18-pre-binlog-off\` — `almanyauni.sql` (485 MB), `almanyauni_baseline.sql` (483 MB), `almanyauni_forum.sql` (228 KB), `almanya_uni.sql` (24 KB), `my.ini.bak`. Hepsi çıkış kodu 0, hata çıktısı boş, "Dump completed" ile bitiyor; tablo sayıları DB ile birebir (101/101/69/16) |
| Yeniden başlatma yolu | SQL `RESTART` (MySQL 8 izleyici süreciyle aynı komut satırıyla yeniden başlatır) |

**Değişiklik:**
- `my.ini` `[mysqld]` bölümüne 3 satır eklendi (2 yorum + `disable_log_bin`).
- Dosyanın geri kalanı ve CRLF satır sonları aynı.
- Yedekler: `my.ini.bak-2026-10-18` (aynı klasörde) ve `db-backups/…/my.ini.bak`.

**Yeniden başlatma:** 07:48:14 UTC temiz kapanış ("Shutdown complete"), 07:48:32 UTC "ready for connections". Hata günlüğünde yalnız önceden de var olan uyarılar (`secure-file-priv`, ICU dizini, self-signed CA).

**Sonrası:**

| Kontrol | Sonuç |
|---|---|
| `SELECT @@log_bin` | **0** (önce 1) |
| `SHOW BINARY LOGS` | "You are not using binary logging" |
| Veritabanları | Dördü açılıyor; tablo sayıları aynı. `almanyauni` posts 687 / faqs 1.904 / programs 27.054; `almanyauni_baseline` posts 790 / faqs 2.005 / programs 27.037 |
| Laravel bağlantısı | ✓ (`almanyauni_baseline`, posts 790) |
| Kısa test | `ViewCounterTimestampsTest` 13/13 (DB'yi sıfırdan kurdu) |
| Yeni binlog yazımı | Yok: test sonrası `binlog.000086`'nın zaman damgası yeniden başlatma anında (09:48 yerel) kaldı |

**Disk:**

| An | Boş alan (C:) | Binlog |
|---|---|---|
| 17.10 yayın sırasında | 31 MB | ~20 GB (22 dosya) |
| 17.10 PURGE sonrası | 22 GB | 1 dosya |
| 18.10 binlog kapatmadan önce | 16 GB | 2 dosya, ~1,4 GB (≈1 günde test koşularından) |
| 18.10 sonrası | 16 GB | Aynı 2 dosya (yeni yazım yok; elle silinmedi) |

**Kalan dosyalar:** `binlog.000085` (1,0 GB), `binlog.000086` (0,4 GB), `binlog.index`. Talimat gereği elle silinmedi. Binlog kapalıyken `PURGE` çalışmaz. İleride temizlemek isterseniz: MySQL'i Laragon'dan durdurun → üç dosyayı yedek klasöre taşıyın (silmeyin) → başlatın.

**Geri dönüş:**
1. `my.ini`'den eklenen 3 satırı silin (veya `my.ini.bak-2026-10-18` ile değiştirin).
2. MySQL'i Laragon'dan veya `RESTART` ile yeniden başlatın.
3. `SELECT @@log_bin` → 1.

Veritabanlarına etkisi yok. Veri kaybı olursa: `mysql < db-backups/2026-10-18-pre-binlog-off/<db>.sql`.

## 6. Açık riskler

1. **FAQ canlı kanıtı yok** (sayfada tarih alanı yok). Kod/test kanıtına dayanıyor; 19.10 sitemap kontrolüyle tamamlanacak.
2. **Mevcut kirli tarihler:** Prod `updated_at`, sitemap `lastmod` ve `dateModified` hâlâ SB-1 öncesi son ziyaret anını gösteriyor. Onarım `content_updated_at` paketiyle (tasarım hazır, onay gerekli).
3. **Laragon güncellemesi:** Laragon MySQL sürümü değiştirilirse yeni sürüm klasöründeki `my.ini` binlog'u yeniden açar; `disable_log_bin` oraya da eklenmeli.
4. **Disk:** 16 GB boş (yüzde 97 dolu). Binlog kapalı, ama diskin geri kalanı başka nedenlerle dolu; ayrıca bakılması önerilir.

## 6a. 19.10 takip — ara sonuç

Ölçüm: 2026-10-18 23:51 UTC = **19.10 01:51 Berlin** (uygulama saat dilimi `Europe/Berlin`). Yalnız 3 sitemap GET'i; sayfa açılmadı.

| Tür | `lastmod` 2026-10-18 (taban → şimdi) | 2026-10-19 |
|---|---|---|
| FAQ | 812 → **812** | 0 |
| Blog | 214 → **214** | 0 |
| Haber | 16 → **16** | 0 |

9 örnek sayfanın hepsi hâlâ 2026-10-18. Dağılımda tabana göre hiçbir fark yok.

**Sınır:** Ölçüm 19.10'un yalnız ilk ~2 saatini (gece, düşük trafik) kapsıyor. Kesin sonuç için aynı komut 19.10 akşamı (Berlin 18:00 sonrası) tekrar çalıştırılmalı; beklenti yine `2026-10-19` ≈ 0. FAQ için açık risk 1 bu ikinci ölçümle kapanır.

## 7. Geri dönüş (SB-1 kodu)

`git revert c5ddcb4` → push → CI/deploy. Şema ve veri değişikliği yok. Revert sonrası ziyaretler yeniden `updated_at`'i değiştirmeye başlar.

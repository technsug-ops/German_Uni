# `content_updated_at` — Faz 1 Canlıya Alma Sonucu

Tarih: 19 Ekim 2026. Dayanak: `SEO_CONTENT_UPDATED_AT_PHASE1_RESULT.md`.

| Alan | Durum |
|---|---|
| Güncel prod yedeği | ✅ 19.10 08:19, doğrulandı ve geçici DB'ye geri yüklendi (§1) |
| Commit / push | (yayın sonrası doldurulacak) |
| CI / FTPS / server-side deploy | (yayın sonrası doldurulacak) |
| Production migration | Admin doğrulaması bekleniyor (§4) |
| Production DB'ye manuel işlem | Yok |

## 1. Production yedeği

**Dosya:** `C:\Users\Yapra\db-backups\almanyauni-production-20261019.sql.gz.sql`
- 612.593.451 bayt. Adında `.gz` geçse de **sıkıştırılmamış düz SQL** (ilk baytlar `-- phpMyAdmin SQL Dump`). Bu yüzden gzip bütünlük kontrolü uygulanamadı; bütünlük aşağıdaki kontrollerle ve geri yükleme testiyle doğrulandı.
- SHA-256: `fe0eeab07863959cfcdcb6aacdf35ef335ea05012c60e8f5450218bb80386bc7`
- Kişisel veri içerir; repoya ve OneDrive'a konmaz.

**Başlık ve kapsam:**

| Kontrol | Sonuç |
|---|---|
| `Erstellungszeit` | 19. Okt 2026 um 08:19 |
| Sunucu | MariaDB 10.11.14 (prod), veritabanı `d0471a0e` |
| Dosya sonu | `COMMIT;` + karakter seti geri yükleme satırları (yarım kalmamış) |
| `CREATE TABLE` | 101 (yerel tabanla aynı sayı; `kb_chunks`, `sessions`, `cache`, `jobs` dahil) |
| Veri içeren tablolar | 81 (diğerleri prod'da boş tablolar) |
| `migrations` | 561 kayıt. Repoda olup yedekte olmayan tek migration: `2026_10_18_000100_add_content_date_columns_to_posts_and_faqs` (beklenen). Son kayıt: `2026_10_15_000300_osv4_package2b_dates_fees_consistency` |

**Geri yükleme testi:** Yeni ve bağımsız geçici veritabanı `almanyauni_restoretest_20261019` (yerel MySQL 8.4.3). Mevcut yerel DB'lere dokunulmadı.

| Kontrol | Sonuç |
|---|---|
| İçe aktarma | Çıkış kodu 0, hata yok |
| Tablolar | 101 |
| posts / faqs / programs / universities | 790 / 2.005 / 27.037 / 486 |
| kb_chunks / migrations / users | 49.672 / 561 / 24 |
| `content_updated_at` sütunu | Yok (migration henüz çalışmadı; beklenen) |

Geçici veritabanı silinmedi; Faz 3 kanıt kaynağı olarak da kullanılabilir. Gerekmezse `DROP DATABASE almanyauni_restoretest_20261019` ile kaldırılabilir (yalnız bu ad).

**SB-1 için ek kanıt (yedekten):** SB-1 deploy'u 18.10 09:43:17 (Berlin) bitti. Yedekte bu andan sonra `updated_at`'i değişen kayıt **posts 0, faqs 0**. En yeni değerler posts 09:35:07, faqs 09:41:24, yani deploy öncesi ziyaretler. Yaklaşık 22,5 saatlik canlı trafik boyunca ziyaretler tarihi ilerletmedi. Bu, `SEO_RECOVERY_SB1_PRODUCTION_RESULT.md` §6'daki "FAQ canlı kanıtı yok" riskini doğrudan DB kanıtıyla kapatır.

## 2. Yayın kapsamı

**Kod:** `app/Console/Commands/BlogRenderHtml.php`, `app/Console/Commands/RepairPostLinks.php`, `app/Models/Faq.php`, `app/Models/Post.php`, `app/Models/Concerns/TracksContentDate.php`, `database/migrations/2026_10_18_000100_add_content_date_columns_to_posts_and_faqs.php`, `tests/Feature/ContentUpdatedAtPhase1Test.php`.

**Belgeler:** `SEO_CONTENT_UPDATED_AT_DESIGN.md`, `_PHASE0_RESULT.md`, `_PHASE1_RESULT.md`, bu rapor, `SEO_RECOVERY_SB1_PRODUCTION_RESULT.md`.

**Dışarıda:** forum önbelleği, `scripts/`, `resources/data/mlp_rents.json`, `.env`, SQL yedekleri, ilgisiz belgeler.

## 3. Testler (19.10)

| Kontrol | Sonuç |
|---|---|
| `ContentUpdatedAtPhase1Test` | 8/8 |
| Tam paket (`GEMINI_API_KEY` boş) | **759/759**, 83.922 doğrulama |
| `i18n:audit --leaky` | TR 0, DE 0 |
| `npm run build` | ✓ |

## 4. Migration güvenliği ve doğrulama

- Yalnız 4 nullable sütun + 1 indeks; tablo yeniden kurulmaz, veri yazılmaz, backfill yok; `updated_at`/`published_at` dokunulmaz.
- MariaDB 10.11: sona sütun ekleme INSTANT, indeks INPLACE/kilitsiz; tablolar küçük (790 / 2.005 satır).
- Sütun varsa tabloyu atlar; ikinci çalıştırma veri değiştirmez. Eski kod yeni sütunları yok sayar; yeni kod sütun yokken izlemeyi atlar.
- Deploy'da migration yanıttan **sonra** çalışır; CI yeşili migration'ı kanıtlamaz.

**Admin doğrulaması:** `/admin/ops/migrate` (**`?run=1` olmadan**, yalnız `migrate:status` gösterir):
- Başarı: `2026_10_18_000100_add_content_date_columns_to_posts_and_faqs` satırı `Ran` + batch numarası; `Pending` satırı yok.
- Başarısızlık: aynı satır `Pending`. `?run=1` kullanılmaz; çıktı incelenir. Site bu durumda da çalışır.

## 5. Geri dönüş

- Kod: `git revert <commit>` → push → CI/deploy. Yeni kod sütun yokken de çalıştığından revert şemadan bağımsızdır.
- Şema: gerekirse `migrate:rollback --step=1` (yalnız eklenen sütunları düşürür). Prod'da CLI yok; ayrı onayla yapılır.
- Veri kaybı senaryosu: §1'deki 19.10 yedeği.

## 6. Ayrı takip

19.10 Berlin 18:00 sonrası SB-1 sitemap kontrolü (`SEO_RECOVERY_SB1_PRODUCTION_RESULT.md` §6a).

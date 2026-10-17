# SEO Recovery P1-1 / Paket A — Canlıya Alma Sonucu

Tarih: 17 Ekim 2026. Dayanak: `SEO_RECOVERY_P1_1_PACKAGE_A_RESULT.md` (yerel sonuç).

| Alan | Durum |
|---|---|
| Commit | ✅ `0c30f4a` feat(seo): add primary intent guide internal links |
| Push edilen commit'ler | ✅ Yalnız `0c30f4a` (`72573fe..0c30f4a`) |
| CI ve deploy | ✅ Run 39009394625: testler ✓, Build & Deploy ✓ (FTPS 5. denemede; bkz. §2) |
| Canlıya çıkış | **17.10.2026 06:31:31 UTC** (server-side deploy tetiklendi); 06:32:53 UTC'de canlıda doğrulandı |
| 15 ana URL | ✅ 15/15: 200, index, self-canonical, hreflang tr/en/de/x-default |
| Ana sayfa → ana URL | ✅ 5/5 tek tık (TR/EN/DE) |
| FAQ bağlantıları | ✅ 150/150 ilgili FAQ doğru rehbere; 16/16 ilgisiz FAQ'ta kutu yok |
| Üye sayfalar | ✅ 30/30 tek kutu, doğru dil ve doğru hedef |
| Görünüm | ✅ Masaüstü ve mobil (headless Chrome) |
| Performans | ✅ Ana sayfa HTML +%1,8; yanıt süresinde gerileme yok |

## 1. Git ve dosya güvenliği

- **Yayın öncesi durum:** `main`, `origin/main`'in 1 commit önündeydi: `9ec3ec9`, içinde yalnız OSV Paket 2b sonuç raporu. Paket A değişiklikleri commit edilmemişti.
- **İzolasyon:** `9ec3ec9`'un push'a karışmaması için Paket A, doğrudan `origin/main` (`72573fe`) üzerinden açılan `release/p1-1-package-a` dalına commit edildi. Push `release/p1-1-package-a:main` olarak yapıldı.
- **Yerelde bekleyen:** `9ec3ec9` hâlâ push edilmedi. `backup/main-with-osv2b-result` dalında ve yerel `main`'de duruyor.
- **Commit içeriği:** 13 dosya, sonuç raporu §1 ile birebir.
  - Yeni: `config/seo_intents.php`, `app/Support/SeoIntents.php`, `resources/views/components/seo-intent-guide.blade.php`, `tests/Feature/SeoIntentLinksTest.php`, `docs/SEO_RECOVERY_P1_1_PACKAGE_A_RESULT.md`.
  - Değişen: `lang/tr.json`, `lang/de.json`, `resources/views/{home,blog/show,news/show,faqs/show,universities/collection,discover/tuition-free}.blade.php`.
- **Dışarıda kalanlar:** forum önbelleği, `.env`, `mlp_rents.json`, `scripts/`, `.claude/`, planlama ve denetim belgeleri (matris ve plan dahil), chatbot backlog'u.
- **Migration:** yok. Veritabanı içeriği değişmedi.

## 2. Testler, CI ve deploy

**Yayın öncesi (yerel, yayın dalında):**

| Kontrol | Sonuç |
|---|---|
| Hedefli testler: `SeoIntentLinksTest`, `HreflangConsistencyTest`, `SmokeTest`, `SeoGrowthBatch3/4Test`, `SitemapLocaleCoverageTest`, `SoftNotFoundP04Test`, `BlogRenderHtmlTest`, `FaqMissingAnswerHtmlRepairTest` | 137/137 |
| Tam paket (`GEMINI_API_KEY` boş) | 724/724 |
| `i18n:audit --leaky` | TR 0, DE 0 |
| `npm run build` | ✓ |
| pre-push hook (i18n + tam test) | ✓ (atlanmadı) |

**GitHub Actions run 39009394625:**
- Safety-net tests (Blade lint, MySQL testleri, i18n gate) ✓, 06:18–06:22 UTC.
- Build & Deploy ✓, 06:22–06:31 UTC. Vite build ✓.
- **FTPS:** 1–4. denemeler birkaç saniyede bitip yeniden denemeye düştü; 5. ve son deneme 37 saniyede aktarımı tamamladı (bilinen FTPS dalgalanması).
- Server-side deploy 06:31:31 UTC'de tetiklendi.

**Deploy'un gerçekten gerçekleştiğinin kanıtı:**
- Canlı ana sayfada 06:32:53 UTC'de 5 yeni kart görüldü.
- Canlı CSS (`app-B10AjT1n.css`) yeni sınıfları içeriyor.

## 3. 15 ana URL (canlı)

| Niyet | TR | EN | DE |
|---|---|---|---|
| Üniversite başvurusu | `/tr/study-in-germany` | `/en/study-in-germany` | `/de/study-in-germany` |
| Yüksek lisans şartları | `/tr/blog/doing-a-masters-in-germany-2026-a-z-guide` | `…-en` | `…-de` |
| Üniversite ücretleri | `/tr/blog/is-university-free-in-germany-2026-real-costs` | `…-en` | `…-de` |
| Şartlı kabul | `/tr/blog/germany-conditional-admission-bedingte-zulassung-guide` | `/en/blog/conditional-admission-germany-bachelor-master-2026-guide` | `/de/blog/bedingte-zulassung-deutschland-bachelor-master-2026-leitfaden` |
| Başvuru tarihleri | `/tr/tools/deadlines` | `/en/tools/deadlines` | `/de/tools/deadlines` |

Kontrol sonuçları:
- 15/15: HTTP 200, yönlendirme yok, `index, follow`, self-canonical.
- 15/15: hreflang tr/en/de/x-default; her dilin hreflang'i kendi URL'sini gösteriyor.
- 15/15: ana rehberin kendisinde "Ana rehber" kutusu yok.

## 4. Tıklama derinliği (ana sayfadan)

| Niyet | Önce (canlı, 17.10 sabah) | Sonra (canlı) |
|---|---|---|
| Üniversite başvurusu | 1 | 1 |
| Yüksek lisans şartları | 2 | **1** |
| Üniversite ücretleri | ≥3 | **1** |
| Şartlı kabul | 2 | **1** |
| Başvuru tarihleri | 1 | 1 |

TR, EN ve DE ana sayfalarında 5 kart var. Kartlar config'teki ana URL'lerle birebir eşleşiyor ve düz `<a href>` bağlantısı (JS gerekmez).

## 5. FAQ bağlantıları (canlı, gerçek sayı)

| Ana rehber | TR | EN | DE | ≥10 hedefi |
|---|---|---|---|---|
| Üniversite başvurusu | 27 | 20 | 18 | ✅ |
| Yüksek lisans şartları | 18 | 20 | 14 | ✅ |
| Şartlı kabul | 7 | 6 | 6 | ❌ (gerçek sayı, zorlanmadı) |
| Üniversite ücretleri | 2 | 2 | 2 | ❌ (gerçek sayı, zorlanmadı) |
| Başvuru tarihleri | 3 | 3 | 2 | ❌ (gerçek sayı, zorlanmadı) |
| **Toplam** | 57 | 51 | 42 | **150** |

- **150/150:** 200, yönlendirme yok, `index`, self-canonical. Tam olarak 1 kutu, aynı dildeki doğru ana URL'ye bağlı.
- **İlgisiz FAQ'lar (16/16 kutusuz):** yaşam maliyeti (TR/EN/DE), SWIFT transferi, Türkiye'den ücret ödeme/komisyon, konsolosluk ve iDATA vize randevusu, öğrenci vizesi randevusu, Goethe-Telc (TR/EN), DAAD burs tarihleri, yurt başvuru tarihi, Jobsuche vizesi, online master, Studienkolleg ücreti.
- **Kaynak:** FAQ listesi yerel DB'den (13.10 prod kopyası) üretildi. Canlıda 166 URL'nin hepsi 200 döndü; yerel ile canlı slug farkı çıkmadı.

## 6. Üye sayfalar (canlı)

30/30 üye sayfa: 200, tek kutu, aynı dildeki doğru ana rehber.

| Ana rehber | Üye sayfalar (×3 dil) |
|---|---|
| Üniversite başvurusu | Bachelor başvuru rehberi, uni-assist rehberi, Studienkolleg rehberi |
| Yüksek lisans şartları | Master haberi, İngilizce master rehberi, GPA/kabul şansı yazısı |
| Üniversite ücretleri | "Ücretsiz üniversite" haberi, `/tuition-free` |
| Şartlı kabul | Şartlı kabul vizesi yazısı, şartlı kabul üniversite koleksiyonu |

Yanlış dile, 404'e veya yönlendirmeye giden bağlantı: **0**.

## 7. Görünüm (canlı)

- **Yöntem:** Headless Chrome, canlı site.
- **Mobil:** 375 px genişlik iframe içinde (Chrome'un asgari pencere genişliği nedeniyle). Gerçek cihaz testi yapılmadı.

| Görünüm | Sonuç |
|---|---|
| Ana sayfa TR, masaüstü 1366 px | "Başvuru Rehberleri" yol haritası bandının altında; 5 kart tek satır, eşit yükseklik, taşma yok; diğer bölümler yerinde |
| Ana sayfa DE, mobil 375 px | 5 kart alt alta, tam genişlik; yatay kaydırma yok |
| FAQ TR, mobil | Kutu cevabın altında, "işine yaradı mı?"nın üstünde; okunaklı |
| Haber DE, mobil | "Hauptratgeber" kutusu özet ile gövde arasında; taşma yok |

**Erişilebilirlik:**
- Anlamlı bağlantı metinleri (niyet adı).
- Kartlarda `focus-visible` çerçevesi; sınıflar canlı CSS'te mevcut.
- İkonlar `aria-hidden`; bölüm `aria-labelledby` ile başlıklı.
- Klavyeyle gezinme elle denenmedi; yalnız işaretleme ve CSS düzeyinde doğrulandı.

**Ek gözlemler (Paket A ile ilgisiz, değiştirilmedi):**
- Haber gövdesinde başlık ikinci kez H1/H2 olarak tekrar ediyor (Paket B kapsamı).
- Mobilde çerez kutusu ilk açılışta ekranın üstünü kaplıyor.

## 8. Performans

Her sayfa 3 kez ölçüldü (önbellek kırıcı parametreyle; HTML sıkıştırılmamış boyut).

| Sayfa | HTML önce → sonra | İlk bayt (TTFB) önce | İlk bayt sonra |
|---|---|---|---|
| `/tr` | 411,4 → 419,0 KB (+7,6 KB, +%1,8) | 0,30–0,57 sn | 0,31–0,45 sn |
| `/en` | 404,6 → 412,2 KB | 0,25–0,32 sn | 0,27–0,30 sn |
| `/de` | 406,1 → 413,7 KB | 0,28–0,56 sn | 0,26–0,31 sn |
| FAQ (TR) | 244,2 → 245,7 KB | 0,29–0,43 sn | 0,24–0,30 sn |
| Haber (DE) | 234,6 → 236,1 KB | 0,28–0,34 sn | 0,21–0,29 sn |
| Blog (EN) | 278,7 → 280,1 KB | 0,24–0,63 sn | 0,26–1,08 sn (tek uç değer; diğer ikisi 0,26–0,29) |

- **Gerileme yok.** Yeni bölümde görsel veya ek istek yok; yalnız inline SVG ikonlar var.
- **Ek sunucu işi:** ana yazı slug'ları için 1 saat önbellekli tek sorgu.

## 9. Değişmeyenler (doğrulandı)

- `/study-in-germany` içeriği, URL ve slug'lar.
- Title, H1, meta.
- Canonical ve hreflang; robots, noindex ve sitemap koduna dokunulmadı.
- Blog, FAQ ve KB verisi; chatbot.
- Migration dosyaları (hiç eklenmedi).

## 10. Açık riskler

1. **`/study-in-germany` beklenti farkı:** Kart "Üniversite başvurusu" diyor ama sayfa hâlâ "Neden Almanya" içeriğinde. Başvuru FAQ'larından (65 sayfa) gelen kullanıcı adım adım başvuru içeriği bulamaz. Çözüm Paket H.
2. **Üç niyette FAQ bağlantısı 10'un altında:** şartlı kabul, ücretler ve tarihler. Havuzda ilgili soru az; sayı ancak yeni FAQ içeriğiyle artar.
3. **Önbellek:** Bir ana rehber yayından kaldırılırsa kart ve kutular en geç 1 saat sonra kaybolur.
4. **FTPS dalgalanması:** Bu yayında 5. denemede geçti. Sonraki yayınlarda yine yeniden deneme gerekebilir.

## 11. Geri dönüş planı

- `git revert 0c30f4a` → push → CI/deploy. Veri, migration ve URL değişikliği olmadığı için geri dönüş temiz.
- Kısmi kapatma: `config/seo_intents.php` içinde bir niyetin `primary` kaydını silmek o niyetin kartını ve kutularını kaldırır (deploy + en geç 1 saat önbellek).

## 12. GSC ölçüm başlangıcı

- **Başlangıç tarihi:** 17.10.2026 (06:31 UTC). Taban değerler: Matris §2 (GSC dışa aktarımı 12.07–11.10).
- **60. gün kontrolü (≈16.12.2026):** her niyette TR gösterim > 0 ve ortalama pozisyon < 50.
- **90. gün (≈15.01.2027):** toplam ≥300 gösterim ve ilk tıklamalar.
- Organik trafik artışı henüz ölçülmedi; bu raporda iddia edilmiyor.

## 13. Sonraki işler (başlanmadı)

- Paket B: 147 yazıda çift H1.
- Paket H: `/study-in-germany` başvuru rehberine dönüşüm.
- Resmî kaynaklarla içerik geliştirme (Paket E/F/G).

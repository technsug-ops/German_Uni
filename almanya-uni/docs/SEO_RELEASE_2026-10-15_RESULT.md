# Yayın Sonucu — 15 Ekim 2026 (P0-4 Soft 404, OSV-3 Paket 1b, Social Card i18n)

Durum: **Canlıda ve doğrulandı.** Yalnız KB adımı açık: admin oturumu gerekiyor (§6).
Dayanak: `docs/SEO_RELEASE_READINESS_2026-10-15.md`.

## 1. Yayın öncesi

| Kontrol | Sonuç |
|---|---|
| Git | Commit'e yalnız üç paketin dosyaları girdi. Forum önbelleği, `mlp_rents.json`, `scripts/`, `.claude/`, denetim ve plan belgeleri dışarıda kaldı |
| Önceden uygulanmış migration'lar | Değişmedi (`000100`–`000400` git'te aynı) |
| Bekleyen migration'lar | `2026_10_14_000500_osv3_package1b_check_date_vs_effective_date`, `2026_10_14_004000_repoint_dead_blog_redirects` |
| Idempotentlik (yerel) | İkisi de ikinci kez rollback'li transaction içinde çalıştırıldı. `faqs`, `posts` ve `blog_redirects` sağlama toplamları **değişmedi** |
| Tam test paketi | **601/601** + LegalProductionDryRunTest **11/11**; `i18n:audit --leaky` TR 0, DE 0 |
| GSC başlangıç (13.10 ölçümü) | Soft 404 **450**, 404 **64**, Yönlendirmeli **163**, Tarandı – dizinde değil **6.805**. **Daha yeni GSC ölçümüne erişim yok**; bu değerler geçmiş ölçümdür |

## 2. Commit, CI, deploy

| Commit | Paket |
|---|---|
| `4d7a9ff` | feat(seo): P0-4 Soft 404 |
| `a4e962d` | fix(content): OSV-3 Paket 1b |
| `201e749` | fix(seo): Social Card i18n |

- **Push:** `b3457c6..201e749 main -> main`. Pre-push hook'u (i18n + testler) temiz.
- **GitHub Actions run 38714563444:** Safety-net tests ✓, Build & Deploy ✓. FTPS ilk denemede geçti. "Trigger server-side deploy" 09:43:27 UTC'de tamamlandı.
- **Migration:** `_deploy.php` içinde cevaptan sonra çalıştı. Kanıt §3: Sperrkonto redirect'i yeni hedefe tek 301 veriyor (`004000`) ve 30 OSV kaydında yeni metin var (`000500`).

## 3. Canlı HTTP kontrolleri (15.10 09:44 UTC, yalnız GET, 1 istek/sn)

| URL | Önce | Sonra | Beklenen |
|---|---|---|---|
| `/en/programs/city/potsdam-q1711/field/agriculture-forestry-nutrition` | 200 index | **404** noindex | ✓ |
| `/tr/programs/city/genf-q71/field/arts-design` | 200 index | **404** | ✓ |
| `/en/programs/city/potsdam-q1711/field/engineering` | 200 index | 200 index | ✓ |
| `/en/programs/city/potsdam-q1711/field/engineering?degree=phd` | 200 index | **404** | ✓ |
| `/de/cities/wiesbaden/nc-free` | 200 index | **404** | ✓ |
| `/en/programs/field/computer-science/degree/master?page=999` | 200 index | **404** | ✓ |
| `/en/programs/field/computer-science/degree/master` | 200 | 200 index | ✓ |
| `/en/programs?q=zzzxxyy` (arama ekranı) | 200 | 200 | ✓ (404 olmadı) |
| `/en/universities/3` | 200 (kopya) | **301 → 200** `/en/universities/akademie-der-bildenden-kunste-nurnberg-q414041` | ✓ tek hop |
| `/en/universities/003`, `/en/universities/3-xyz` | 200 (kopya) | **404** | ✓ |
| `/en/cities/8` | 200 | **301 → 200** `/en/cities/berlin-q64` | ✓ |
| `/en/cities/bayern-q980` | 200 index | **301 → 200** `/en/states/bayern` | ✓ |
| `/de/cities/bonn-q586` | 200 noindex | **301 → 200** `/de/cities/bonn-q892684` | ✓ |
| `/en/universities/hochschule-kempten-partner-019ddbba` | 200 | **301 → 200** (HAW Kempten) | ✓ |
| `/en/universities/informationszentrum-fur-fremdsprachenforschung-q820409` | 200 | **410** | ✓ |
| `/en/blog/sperrkonto-2025-tam-rehber-almanya-vizesi-icin-bloke-hesap-en` | 301 → **404** | **301 → 200** (`sperrkonto-for-a-german-visa-…-en`) | ✓ |
| `/en/universities/technische-universitat-munchen-partner-019ddbba` | 200 | 200 index | ✓ |

**Sitemap betiği:** canlı `sitemap-{tr,en,de}.xml` dosyalarındaki tüm programatik landing URL'leri (şehir × alan, şehir × dil, şehir NC-free, alan × derece, alan × dil, konu NC-free), 15.10 09:47–11:20 UTC:

| Toplam | 200 + index + self-canonical | Uyumsuz |
|---|---|---|
| **4.749** | **4.749** | **0** |

Not: 4.719 sayısı yerel ölçümdü ve konu NC-free sayfalarını içermiyordu; canlıdaki 4.749 bunları da içeriyor.

**Canonical / hreflang** (örnek: EN yazı, DE üniversite, EN landing): self-canonical ✓; hreflang `tr`, `en`, `de`, `x-default` ✓.

**OSV-3 Paket 1b:**
- 30/30 kayıt canlı sayfada doğrulandı: yeni başlık ("Güncel durum (kontrol tarihi: 14.10.2026)" / "Current status (checked …)" / "Aktueller Stand (geprüft am …)") ve "başlangıç tarihi belirtilmiyor" cümlesi var; eski "Güncelleme / Update / Aktualisierung (14.10.2026)" yok.
- Beklenmeyen `mismatch` yok (30/30 uygulanmış görünüyor).
- `/admin/ops/osv-repair?file=2026-10-14-osv3-package1b-check-date` önizlemesi admin oturumu gerektirdiği için tarafımızdan görülmedi. İsteğe bağlı teyit: `already: 30` beklenir.

**Social Card i18n:**
- `/og/post/bachelor-and-master-thesis-process-in-germany-en.png`: "Apply to study in Germany", "Studying in Germany", "23 min read" ✓.
- DE üniversite kartı (`?lang=de`): "UNIVERSITÄT", "Bewerbungsleitfaden für Deutschland", "Staatliche Hochschule" ✓.
- `og:locale`: en_US / de_DE / tr_TR ✓; `twitter:card` summary_large_image ✓; `og:image:type` uzantıya göre ✓.

**Üretim verisi farkı:** gözlenmedi. Bütün beklentiler karşılandı; zorlanan bir kural yok.

## 4. Riskler ve izleme

- **GSC:** 404 sayısı binlerle artacak (≈6.900 boş landing + geçersiz ID varyantları). Bu beklenen bir artış. Soft 404 hedefi < 200 (4–8 hafta).
- **Sosyal önbellek:** yazı kartı URL'leri değişmedi. Facebook, LinkedIn ve X eski Türkçe kartı yeniden taramaya kadar gösterebilir. Önemli yazılar için "Scrape Again" ve Post Inspector kullanılabilir.
- **Eski OG dosyaları:** diskte kalıyor (okunmuyor). Canlıda CLI olmadığı için `og:refresh --all` çalıştırılamaz; zararsız.
- **İnceleme listesi (değişmedi):** DHBW 844, 10 araştırma enstitüsü, 2 Mitte kaydı, 2 haber redirect'i.

## 5. Geri dönüş

- Commit bazında revert mümkün.
- `004000` `down()` yalnız kendi satırlarını eski hedefe döndürür.
- OSV 1b için geri dönüş önerilmez (eski metin hatalı yorum üretiyordu).

## 6. KB — kullanıcı adımı bekliyor

Prod KB'ye admin oturumu olmadan erişilemiyor. Adımlar:

1. `https://applytogerman.com/admin/ops/kb-embed?source=faq,post&dry=1` → beklenen ≈ **102** chunk (OSV 1b'nin 30 kaydı). **200'ü aşarsa durdurun**; başka içerik de değişmiş demektir.
2. Sayı uygunsa: `https://applytogerman.com/admin/ops/kb-embed?source=faq,post`.
3. Ardından chatbot'ta 14 OSV senaryosu ve kontrol tarihi senaryosu yeniden koşulur. Beklenen: "14.10.2026 tarihinden itibaren" ifadesi yok, yanlış bilgi yok.

KB adımı tamamlanınca bu bölüm sonuçla güncellenecek.

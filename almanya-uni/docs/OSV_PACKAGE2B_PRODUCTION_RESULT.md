# OSV Paket 2b — Canlıya Alma Sonucu

Tarih: 15 Ekim 2026. Dayanak: `docs/OSV_PACKAGE2B_RESULT.md`.

| Alan | Durum |
|---|---|
| Commit | ✅ `72573fe` (yalnız Paket 2b'nin 8 dosyası) |
| CI ve deploy | ✅ Run 38794440462: testler ✓, Build & Deploy ✓; server deploy 19:36:05 UTC |
| Migration | ✅ Canlı içerikten doğrulandı (63/63 düzenleme canlıda). Admin önizlemesi görülmedi (§3) |
| Canlı içerik TR/EN/DE | ✅ 63/63; URL, canonical ve hreflang aynı |
| KB | ✅ Senkron: PLAN 210 → son kontrol 0 embed / 7235 değişmemiş; `kb_chunks` toplam 49.672 |
| Chatbot | ✅ 73 sorudan 70'i temiz; 1 eksik cevap, 1 denetleyici yanlış alarmı, 2 geçici kesilme (§6) |

## 1. Commit

- **Commit'e girenler:**
  - `database/data/osv/2026-10-15-osv4-package2b.json`
  - `database/migrations/2026_10_15_000300_osv4_package2b_dates_fees_consistency.php`
  - `tests/Feature/OsvPackage2bTest.php`
  - `app/Services/Rag/ChatService.php`
  - `resources/data/chat_eval.json`
  - `database/data/osv/known_wrong.json`
  - `docs/OSV_PACKAGE2B_RESULT.md`
  - `docs/OSV_PACKAGE2_CHATBOT_LIVE_VERIFICATION.md`
- **Dışarıda kalanlar:** forum önbelleği, `.env`, `mlp_rents.json`, `scripts/`, `.claude/`, plan ve denetim belgeleri.
- **Önceden uygulanmış migration'lar:** değişmedi (git'te takip edilen migration dosyalarında değişiklik yok).
- **Push:** `009f62b..72573fe` (önceki yerel docs commit'i `8d3d224` de gitti).
- **Yayın öncesi testler** (CI gibi, `GEMINI_API_KEY` boş): **672/672** + LegalProductionDryRunTest **11/11**; `i18n:audit --leaky` TR 0, DE 0.

## 2. CI ve deploy

GitHub Actions run 38794440462:
- Safety-net tests ✓
- Build & Deploy ✓ (FTPS ilk denemede geçti)
- "Trigger server-side deploy" 19:36:05 UTC

## 3. Migration kanıtı

- **Doğrudan kayıt okunamadı.** `migrate:status` için salt-okunur bir uç nokta yok; `/admin/ops/osv-repair?file=2026-10-15-osv4-package2b` önizlemesi admin oturumu istiyor. İsteğe bağlı teyit: önizlemede `already: 63` görülmeli; `mismatch` veya `missing` görülmemeli.
- **Bağımsız canlı kanıt:** paket içeriği yalnız bu migration'la prod veritabanına yazılabilir. Deploy'dan önce ve sonra aynı betikle (`verify_osv.py`) ölçüldü:

| Ölçüm | Önce (deploy öncesi) | Sonra |
|---|---|---|
| Eski metin canlıda | 51 düzenleme | **0** |
| Yeni metin gövdede birebir | 0 | **54** |
| Haber sayfaları 153–155 (9 düzenleme; haber şablonu farklı olduğu için tam sayfa metniyle ölçüldü) | "1 Eylül 2024 / 1 September 2024" var | "2026 için / for 2026 / für 2026" 6–7 kez; eski ifade 0; meta açıklamalar yeni |
| **Toplam** | — | **63/63 uygulanmış**, beklenmeyen fark yok |

**Kanıt sınırı:** Önizleme ekranındaki `mismatch` / `skipped` satırları doğrudan görülmedi. Canlı ölçümde 63 düzenlemenin hepsinin yeni metni bulunduğu için `mismatch` olmadığı sonucuna varılıyor.

## 4. Canlı içerik örnekleri

| URL | Kontrol |
|---|---|
| `/en/faq/visa/how-do-i-get-a-german-student-visa-en` | "For 2026" ✓; "currently (September 2026)" yok |
| `/en/faq/appointments/how-do-you-apply-for-a-visa-through-idata-en` | "€75 (…)" ✓ |
| `/de/faq/work/how-many-hours-a-week-can-i-work-as-a-student-in-germany-de` | Yeni kaynaklı 140 gün ve yarım gün cümlesi ✓ |
| `/tr|en|de/news/2026-germany-blocked-account-sperrkonto-update-…` | Meta + özet + gövde ✓ |

**URL, canonical, hreflang:** 4 örnek sayfa (EN SSS, DE SSS, TR SSS, EN haber) 200 dönüyor; self-canonical; hreflang tr/en/de/x-default deploy öncesiyle aynı.

## 5. KB

Admin `/admin/ops/kb-embed?source=faq,post` (kullanıcı çalıştırdı; gerçek çalıştırma otomatik başlatılmadı):

| Adım | Sonuç |
|---|---|
| Dry-run | PLAN **210 embed** / 7025 değişmemiş (beklenen 200–230 ✓, eşik 400'ün altında) |
| Son kontrol | TAMAM **0 embed** / 7235 değişmemiş; `kb_chunks` toplam **49.672** |

7235 = 210 + 7025 → paketteki 210 chunk embed edilmiş, KB senkron. **Kanıt sınırı:** 210 chunk'ı yazan gerçek çalıştırmanın çıktısı görülmedi; senkron durum son kontrolün 0 embed göstermesinden çıkarılıyor.

## 6. Chatbot (canlı, KB sonrası)

`POST /{locale}/chat`, deterministik kontrollerle (`must_contain` / `must_not_contain`). **73 benzersiz soru** (önceki 44 canlı senaryo + 29 yalnız-OSV soru; 17 yeni OSV senaryosu dahil), TR/EN/DE. Hata 0.

| Sonuç | Adet |
|---|---|
| Temiz | 70 |
| Eksik ama doğru | 1 |
| Denetleyici yanlış alarmı | 1 |
| Geçici kesilme | 1 (+1 odak incelemesinde) |

**İşaretlenenler:**
1. **TR `visa.remonstration`: kısmi.** Remonstrasyonun 01.07.2025'te kaldırıldığını doğru söylüyor; alternatifleri (yeni başvuru / VG Berlin'de dava) eksik. İçerik hatası değil, cevap eksik.
2. **TR `work.student.days`: yanlış alarm.** "120 gün" yasaklı ifadesine takıldı; cevap doğru tarihçe veriyor: "2024 yılında 120 günden 140 güne çıkarılmıştır". `PhraseGuard` `allow_context` yalnız eşleşmenin *öncesindeki* 40 karaktere bakıyor → sonrasını da kapsamalı.
3. **TR N10 dil kursu: geçici kesilme.** İlk cevap cümle ortasında kesildi ("…ayda en az 1.091 €"). 2 tekrar + alternatif soru tam cevap verdi (1087 / 1326 / 456 karakter): 1.091 € (Merkblatt 44F, kontrol tarihi 15.10.2026), 2026 için 992 € ayrımı, Verpflichtungserklärung. Tekrarlanmadı.
4. **EN `funds.date`: geçici kesilme.** Bir cevap "…was published" ile bitiyor; aynı konunun diğer EN/DE cevapları tam.

**Odak konular:**

| Konu | Sonuç |
|---|---|
| 75 € vize harcı | ✅ TR/EN/DE: 75 € (§ 46 Abs. 2 Nr. 1 AufenthV), iDATA ücretleri (39,92 € + 12 €, TL) ayrı; öğrenim vizesinde iDATA yok (Auslandsportal); burs muafiyeti |
| Blue Card 2026 | ✅ 50.700 € / 45.934,20 € birebir; "yaklaşık / approximately / ungefähr" yok |
| 140 gün + yarım gün | ✅ 140 tam / 280 yarım gün; 120 yalnız tarihçe olarak (bkz. 2) |
| Geçim tutarı tarihleri | ✅ "2026 için 992 €"; kontrol tarihi yürürlük tarihi gibi sunulmuyor; "1 Eylül 2024" yok |
| Dil kursu finansmanı | ✅ 1.091 € + Verpflichtungserklärung; 992 € ile ayrımı yapılıyor |

**Açık kalanlar (canlıya dokunmadan):**
- `PhraseGuard` için sonraki bağlam desteği (yanlış alarm 2).
- Remonstrasyon TR cevabına alternatiflerin girmesi (SSS içeriği veya prompt).
- Kesilmeler: 73'te 2, tekrar edilemedi. Sürerse `maxOutputTokens` ve yanıt bitiş nedeni (`finishReason`) loglanmalı.

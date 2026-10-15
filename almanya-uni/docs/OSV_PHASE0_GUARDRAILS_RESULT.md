# OSV Faz 0 — Yanlış resmî bilginin yeniden üretilmesini durduran korkuluklar

Tarih: 15 Ekim 2026. Durum: **yalnız yerelde uygulandı ve test edildi.** Commit, push, deploy, prod yazımı ve KB çalıştırması yok. Yeni tablo/migration yok; tam claims veritabanı (mimari §2) bilerek yapılmadı.

Referans: `docs/SEO_OFFICIAL_SOURCE_VERIFICATION_ARCHITECTURE.md` (G5, G8, G9, G10; §5, §6, §7.2, §7.3, §9 Faz 0).

## Özet

| # | Korkuluk | Önce | Sonra |
|---|---|---|---|
| 1 | Brief `notes` → üretim promptu (G8) | `notes` LLM'e hiç gitmiyordu | Etiketli, bağlayıcı blok olarak her asset/dilde prompta giriyor |
| 2 | Çeviri promptu kuralı (G9) | Kural yalnız 2 komutta vardı | 4 çeviri yolu + GeminiTranslator promptları rakam/tutar/tarih/§/URL korur |
| 3 | Deterministik çeviri kontrolü (G9) | Yok | Uyuşmayan çeviri **kaydedilmez**, komut çıktısında ve log'da raporlanır |
| 4 | "Para birimini uyarla" kuralı | `ContentTranslator` "Adapt cultural references (currency…)" diyordu | Kaldırıldı; tutar/para birimi aynen kalır |
| 5 | `x-source-note` | `updated` → "Last verified" (örnek `updated_at` veriyordu) | `checked` = kontrol tarihi ("Checked on / Kontrol tarihi / Geprüft am"); `updated` yalnız "Last updated"; tarih yoksa doğrulama iddiası yok |
| 6 | Bilinen yanlış ifade regresyonu | `KNOWN_WRONG` test içinde sabit, dar kapsam | `database/data/osv/known_wrong.json` (63 ifade) + veri güdümlü test, tarihsel bağlam allow-list'i |
| 7 | Chatbot check-date kuralı + eval | Kural yok; eval yalnız LLM-judge | System prompt kuralı + 20 OSV eval sorusu + deterministik dize kontrolü (kapıyı kaldırır) |

## 1. Brief notları üretim promptunda (G8)

| Dosya | Değişiklik |
|---|---|
| `app/Services/Content/ContentPrompts.php:54, :63, :74–83` | `baseContext()` artık `$brief->notes`'u `EDITORIAL CONSTRAINTS / VERIFIED FACTS — MUST FOLLOW (… çelişen bilgi, tutar, tarih veya prosedür yazma)` başlığıyla ekliyor. Boş not → blok yok. `editorialNotes()` statik yardımcı |
| `app/Console/Commands/BlogGenerateStarter.php:249–250, :256` | `TOPICS` öğesinde ileride `notes` olursa aynı blok prompta girer (bugün hiçbir konuda yok → davranış aynı) |

Diğer üreticiler: `ContentGenerationService` ve `StorytellingInfographics` zaten `ContentPrompts::build()` üzerinden geçiyor → otomatik kapsandı. `FaqGenerateAi` ve `SeoActivationService` brief kullanmıyor (havuz soruları / audit gap'leri) → değişiklik gerekmedi.

**Test:** `OsvPhase0GuardrailsTest::test_generation_prompt_contains_brief_notes_in_every_language_and_asset`. `SeedHowtoBriefs` içindeki gerçek remonstration brief'i (`:101`) TR/EN/DE × blog/faq_page/newsletter promptlarında birebir geçiyor; boş notta blok yok.

**Ek düzeltme:** `app/Console/Commands/SeedHowtoBriefs.php:121`. Blue Card brief'inin `pain_point`'i "2026 maaş eşiği €45.300 (…€41.041)" diyordu (2024 değeri). Yeni değer audit C24'teki doğrulanmış 2026 rakamı: "50.700 € (darboğaz meslekleri ve yeni mezunlarda 45.934,20 €)".

## 2–4. Çeviri: prompt kuralı + deterministik kontrol + para birimi kuralının kaldırılması

**Yeni sınıf:** `app/Support/TranslationInvariants.php`. Kaynak ve çeviriden şu çoklu kümeleri çıkarır ve karşılaştırır:

| Kategori | Normalleştirme |
|---|---|
| `urls` | http(s) bağlantısı; sondaki noktalama atılır |
| `dates` | `GG.AA.YYYY` ve ISO `YYYY-MM-DD` → `Y-m-d` |
| `paragraphs` | `§ 16b Abs. 3` = `§ 16b Absatz 3` = `§ 16b (3)` = `§16b(3)` |
| `amounts` | `45.934,20 €` = `€45,934.20` = `45934,20 EUR`; `992,00 €` = `€992` |
| `numbers` | `1.091` = `1,091` = `1 091`; `1,5` = `1.5` |

- Tek haneli çıplak tamsayılar (0–9) sayılmaz: "2 yıl" → "two years" serbesttir. Tutar, tarih, § içindeki rakamlar ve iki haneli sayılar her zaman sayılır.
- `compare()` tam çoklu küme farkını verir. `unexpected()` yalnız eklenen parçaları verir; özet/başlık gibi kısaltılan alanlar için kullanılır.
- `PROMPT_RULE` ortak prompt kuralıdır. `TranslateContentBlocks.php:117` / `I18nFillProse.php:180` ifadesinin genişletilmiş hâlidir ve şu satırı da içerir: "checked/kontrol tarihi bir since/ab/itibaren tarihine çevrilmez".

| Yol | Prompt | Kontrol → davranış |
|---|---|---|
| `faq:translate` (`TranslateFaqs.php:230, :243, :103, :183, :281–295`) | `PROMPT_RULE` | Soru + cevap `compare`. Fark varsa satır **kaydedilmez/yaratılmaz**; `⛔ … atlandı` + özet sayaç + `Log::warning` |
| `content:translate-posts` (`TranslatePosts.php:73, :117–131, :176–178, :189–198, :225–230`) | `PROMPT_RULE` | Gövde `compare`; başlık + özet `unexpected`. O dil **kaydedilmez**, diğer dil kaydedilebilir. Dry-run'da da raporlanır |
| `ContentTranslator` (`ContentTranslator.php:64, :82, :84, :113–120`) | `PROMPT_RULE`; **madde 8 "Adapt cultural references (currency…)" kaldırıldı** → "euro amounts stay in euros" | Fark varsa `RuntimeException`, asset yazılmaz. Filament aksiyonları ve `content:translate-assets` hatayı zaten raporluyor |
| `GeminiTranslator` (`GeminiTranslator.php:23, :37, :110–126`) | `SYSTEM_PROMPT_PROGRAM` / `_UNIVERSITY`'ye kural satırı | Yeni `translateChecked()`. `programs:translate` (`TranslatePrograms.php:105`), `TranslateUniversities.php:127`, `I18nFillProse.php:121` bunu kullanıyor; fark → mevcut `errors` sayacı + uyarı satırı, kayıt yok. Genel `translate()` değişmedi (sıralama ayrıştırma, dedupe gibi çeviri olmayan çağrılar etkilenmez) |

**Bilerek kapsam dışı:** `TranslateContentBlocks` (kural zaten var), `ScholarshipsLocalize`, `ProfessionsTranslateInfoFields`, `TranslateProgramNames`. Prompt kuralı veya kontrol eklenmedi; bir sonraki adımda `translateChecked` ile bağlanabilir.

**Ek düzeltme (önceden var olan hata):** `ContentTranslator.php:131`. `generated_by => 'gemini-translator'` enum'da yoktu (`manual|ai_gemini|ai_claude|ai_openai`). Strict MySQL'de **her çeviri kaydı düşüyordu**. Değer `ai_gemini` yapıldı. Test başarılı yolu bu düzeltmeyle doğruluyor.

**Testler:**
- `tests/Unit/TranslationInvariantsTest.php` (6 test, 31 assertion). TR/EN/DE örnekleri, binlik/ondalık ayırıcılar, tek haneli sayıların yazıyla yazılması (serbest), tutar/20 saat/tarih yazıya çevrilince yakalanma, çoklu küme, kategori çıkarımı.
- `OsvPhase0GuardrailsTest` (Http::fake ile, gerçek API çağrısı yok):
  - `faq:translate`: 140 → 120 değişen çeviri kaydedilmez; sadık çeviri kaydedilir; prompt kuralı gönderilir.
  - `content:translate-posts`: EN kaydedilir, tutarı değişen DE kaydedilmez.
  - `invariantDiff`: özette uydurma tutar yakalanır.
  - `ContentTranslator`: $'a çevrilen tutar reddedilir; prompt'ta "currency adapt" yok.
  - `GeminiTranslator::translateChecked`: 120 → 90 ECTS reddedilir.

## 5. `x-source-note`: kontrol tarihi ≠ güncelleme tarihi

| Dosya | Değişiklik |
|---|---|
| `resources/views/components/source-note.blade.php` | Yeni prop `checked` → `__('Checked on :date')` + `<time datetime>`. `updated` artık yalnız `__('Last updated: :date')`; "verified" demez. İkisi de yoksa yalnız kaynaklar gösterilir. Doc örneği `checked` kullanıyor; `updated_at` yalnız "Last updated" için |
| `resources/views/tools/blocked-account/index.blade.php:381` | `updated="2026-06-04"` → `checked="2026-06-04"` |
| `resources/views/tools/language-certificates.blade.php:136` | `updated="2026-10-10"` → `checked="2026-10-10"` |
| `resources/views/tools/visa-appointment.blade.php:176` | `updated="2026-10-14"` → `checked="2026-10-14"` |
| `lang/tr.json:4742`, `lang/de.json:4803` | `"Checked on :date"`: "Kontrol tarihi: :date" / "Geprüft am :date". Tek satır eklendi; dosyalar eklemeden hemen önce yeniden okundu |

Görsel çıktı aynı kutu: "Kaynak: … · Kontrol tarihi: 14 Eki 2026". `i18n:audit --leaky` sonucu: TR/DE eksik 0, sızıntı 0.

**Testler:**
- `test_source_note_distinguishes_check_date_from_update_date`: EN/TR/DE etiketleri; `updated` "verified/Checked" demez; tarihsiz not `<time>` içermez.
- `test_tool_pages_show_check_date_label`: `/tr/tools/visa-appointment`, `/en/tools/language-certificates`, `/de/tools/sperrkonto`.

## 6. Bilinen yanlış ifadeler regresyonu

**Veri:** `database/data/osv/known_wrong.json`, 63 ifade. Her girdide `id`, `phrase`, `locale`, `topic`, `context`, `origin` ve gerekirse `allow_context` var.

- `OsvPackage1RepairTest::KNOWN_WRONG`'un 17 ifadesinin tamamı (test reflection ile doğruluyor).
- Blue Card: `48.300` / `48,300` (allow `2025`), `43.759,20` / `43,759.20` / `43.759.20`, `€45.300` / `€45,300` / `45.300 €` (allow `2024`).
- APS: "Türk öğrenciler için APS zorunlu" ve EN/DE varyantları; "Çin, Hindistan, Pakistan, Vietnam ve Moğolistan" listesi (TR/EN/DE).
- Öğrenci çalışma sınırı: `120 tam gün`, `120 gün`, `120 Tage`, `120 ganze Tage`, `120 days`, `120 full days`. Allow: eskiden/önceden/früher/bisher/previously/formerly/until 2024…
- Diğerleri:
  - `6 × 992` (4 yazım)
  - `Sperrkonto (2025)`, `€992/month|Monat|ay (2025)`, `€/yıl|year|Jahr (2025)`
  - `iDATA is the only`, `32,81`, `€32.81`, `30 € IDATA`
  - Remonstration ek varyantları
  - Check-date: `14.10.2026 tarihinden itibaren`, `…'dan itibaren`, `since/from 14.10.2026`, `seit dem/ab dem 14.10.2026`

**Not:** Dosya `/admin/ops/osv-repair` önizlemesi tarafından da glob'lanıyor. Hata vermemesi için `package`, `checked_at` ve boş `edits` alanları var; önizlemede "0 düzenleme" görünür.

**Eşleştirme:** `app/Support/PhraseGuard.php`. Büyük/küçük harf duyarsız alt dizge araması yapar; boşluklar normalleştirilir. İsabetin önündeki 40 karakterde bir `allow_context` işareti varsa isabet tarihsel anlatım sayılır ve serbesttir. Aynı sınıf chat:eval'de de kullanılıyor.

**Test:** `tests/Feature/OfficialKnownWrongRegressionTest.php`. Kapsam: `config/*.php`, ToolsController, PageFaq, BlogAutoLinker, public Blade (filament hariç), `lang/*.json`, `database/data/osv/*.json` yeni metinleri (`replace`/`text`/`value`, `find` değil), `faq-en/*.json`, `Seed*Briefs.php`, `BlogGenerateStarter.php`. PHP ve Blade yorumları taranmaz; "eski sürümdeki X iddiası kaldırıldı" gibi düzeltme notları yanlış pozitif vermez.

| Test | Sonuç (şu anki çalışma ağacı) |
|---|---|
| liste geçerli + KNOWN_WRONG kapsandı | ✅ |
| tarihsel bağlam serbest, güncel iddia yakalanır | ✅ |
| paket 2 dışı aktif kaynaklar temiz | ✅ |
| paket 2 kaynakları temiz | ✅ (paket 2 düzeltmesi çalışma ağacında; **commit edilmemiş**) |

**Paket 2 öncesi (HEAD) sonuç.** Aynı deny-list ile `git show HEAD:` sürümleri tarandı. Paket 2 commit edilmeden önceki ağaçta bu test aşağıdakilerde **kırmızı** olurdu:

| Dosya (HEAD) | İsabet |
|---|---|
| `config/glossary.php` | Blue Card 48.300 / 48,300 (6), 43.759,20 / 43,759.20 / 43.759.20 (8), APS ülke listesi TR/EN/DE (9) |
| `app/Http/Controllers/Web/ToolsController.php` | `€45.300` (1) |
| `app/Services/Content/BlogAutoLinker.php` | `€/yıl (2025)` (1) |
| `lang/tr.json` | `120 tam gün` (4), `€992/month (2025)`, `€992/ay (2025)` |
| `lang/de.json` | `120 tam gün` (2; TR anahtar), `€992/month (2025)`, `€992/Monat (2025)` |
| `resources/views/tools/recommendation.blade.php` | `€992/month (2025)` (lang anahtarı) |
| `app/Console/Commands/SeedHowtoBriefs.php` | `€45.300` (bu pakette düzeltildi) |
| `app/Support/PageFaq.php`, `lang/en.json` | temiz |

Paket 2 commit'i bu dosyaları kapsamazsa test kırmızıya döner; beklenen davranış budur.

## 7. Chatbot: kontrol tarihi ≠ yürürlük tarihi + OSV eval

| Dosya | Değişiklik |
|---|---|
| `app/Services/Rag/ChatService.php:243` (system prompt, "Kurallar") | Yeni kural: "kontrol tarihi/checked/geprüft am" tarihi kontrol günüdür, kuralın başladığı tarih değildir; hiçbir dilde "… tarihinden itibaren / since … / seit … / ab dem …" diye verilmez; yürürlük tarihi kaynakta yoksa söylenmez. Prompt dili Türkçe (mevcut düzen) |
| `ChatService::stripInstructionLeak` (`:280`) | Kural cümlesi cevaba kopyalanırsa silinir (dar parmak izi) |
| `resources/data/chat_eval.json` | 20 OSV sorusu. Format korundu (`q`, `lang`, `expect`); ek alanlar `tag: "osv"`, `claim`, `must_not_contain`, `must_contain` (alternatif listesi), `allow_context`. Kapsam: APS Türkiye TR/EN/DE; remonstration TR/EN/DE; öğrenim vizesi kanalı TR/EN/DE; check-date TR/EN; iDATA bedeli ↔ vize harcı TR/EN; 140 gün TR/EN/DE; Blue Card 2026 TR/EN; Chancenkarte TR/EN |
| `app/Console/Commands/ChatEval.php` | `--tag=osv` seçeneği. `stringChecks()` deterministik kontrol yapar (LLM yok). Satır kararına `YASAK ✗ / EKSİK ✗ / dize ✓` eklenir, tabloya 2 metrik, JSON'a `osv_*` alanları. **must_not_contain ihlali kapıyı kaldırır** (hedef 0); must_contain eksiği yalnız raporlanır |

chat:eval prod'a karşı **çalıştırılmadı**.

**Testler:**
- `test_chat_system_prompt_forbids_check_date_as_effective_date`: TR/EN/DE payload ve sızıntı temizliği.
- `test_chat_eval_string_checks_are_deterministic`
- `test_chat_eval_set_contains_osv_cases_in_existing_format`

## Çalıştırılan testler (ayrı DB `almanyauni_test_guard`; config ve DB sonunda silindi)

| Paket | Sonuç |
|---|---|
| `OsvPhase0GuardrailsTest` | 11/11 |
| `TranslationInvariantsTest` (Unit) | 6/6 |
| `OfficialKnownWrongRegressionTest` | 4/4 |
| `OsvPackage1RepairTest`, `OsvPackage1bCheckDateTest`, `ChatServicePromptLeakTest`, `ChatServiceLanguageTest`, `ChatServiceAccessAlertTest`, `SeoGrowthBatch3Test` (yukarıdakilerle birlikte) | 59/59 |
| `SmokeTest`, `TrLocaleRedirectTest` | 41/41 |
| `php artisan i18n:audit --leaky` | TR/DE eksik 0, sızıntı 0 |

Tam suite çalıştırılmadı (koordinatör çalıştıracak).

## Bilerek yapılmayanlar

- Tam claims veritabanı (mimari §2: `official_sources`, `claims`, `claim_occurrences` …), migration ve registry JSON'u (`database/data/claims/registry.json`).
- Doğrulanmış claim'lerin üretim promptuna enjeksiyonu (Faz 2).
- Post/Faq `saving` lint'i ve Filament bulgu paneli (G10, Faz 1).
- Retriever'da wrong_variant cezası ve cevap sonrası lint (§7.2 alt satırları).
- Paket 2 dosyaları: `config/glossary.php`, ToolsController, PageFaq, BlogAutoLinker, OSV paket JSON'ları, migration'lar, `routes/web.php`. Bunlara dokunulmadı. `lang/*.json`'a yalnız iki yeni anahtar satırı eklendi.

## Riskler

1. **Notlar artık bağlayıcı.** Yanlış bir brief notu da "MUST FOLLOW" olarak modele gider. `SeedHowtoBriefs` içinde doğrulanmamış notlar var: Blue Card notu "33 ay / 21 ay" diyor (2024 reformu sonrası süre farklı olabilir), APS notu "OPSIYONEL ama bazı üniler ister" diyor. Prod DB'deki brief notları da denetlenmeli. Öneri: üretimden önce brief notlarını `known_wrong.json` ile tarayan küçük bir kontrol.
2. **Çeviri kontrolü yanlış pozitif verebilir.** Model tarihi "1 July 2025" biçimine çevirirse, iki haneli sayıyı yazıyla yazarsa ("twenty hours") veya URL'yi yerelleştirirse çeviri kaydedilmez. Bu bilinçli bir tercih (önce güvenlik). Reddedilen satırlar çıktıda ve log'da listelenir; tekrar çalıştırılabilir. Arapça vb. (coming_soon) dillerde farklı rakam sistemi reddedilir; bu diller şu an üretilmiyor.
3. **Boşlukla binlik ayırma** ("1 000") düz boşlukla da tanınıyor. Çok nadir durumda yan yana iki sayı birleşebilir.
4. **Deny-list alt dizge eşleşmesi.** `120 gün` gibi kısa ifadeler alakasız bağlamda da tutabilir. Çözüm: girdiye `allow_context` eklemek; test hangi dosya/bağlam olduğunu yazdırır.
5. **chat:eval kapısı sıkılaştı.** OSV sorularında tek bir yasak ifade kapıyı kaldırır. KB yenilenmeden çalıştırılırsa kırmızı beklenir (14.10 bazı: 14 senaryonun 7'si yanlış).
6. **x-source-note anlam değişikliği.** Başka (gelecekteki) çağrılar `updated` verirse artık "Last updated" görünür; doğrulama iddiası için `checked` gerekir. Mevcut 3 çağrı güncellendi.

## Koordinatör entegrasyon notu (15.10.2026)

- **`known_wrong.json` genişletildi:** OSV Paket 2'nin `OsvPackage2Test::KNOWN_WRONG` listesindeki 98 ifadenin 95'i eklendi (3'ü zaten vardı). Toplam **158 giriş** (`id: pkg2-NN`, `topic` ve `locale` sezgisel). `OfficialKnownWrongRegressionTest` + `OsvPackage2Test` + `OsvPhase0GuardrailsTest`: **25/25**.
- **Yayındaki içerikte deny-list taraması (yerel baseline, Paket 2 uygulandıktan sonra):** 4 isabet, hepsi **yanlış pozitif**:
  - 3 isabet yazı 150/151/307: "120 tam günden 140 tam güne çıkarıldı". Doğru tarihsel anlatım; `allow_context` yalnız isabetin **önündeki** 40 karaktere bakıyor, "→ 140" bağlamı arkada kalıyor.
  - 1 isabet yazı 101: "within 30 days", konu dışı bir iptal süresi.

  Öneri: `PhraseGuard` için arka bağlam desteği (`allow_after`, ör. "140") ya da "within 30 days" girişine bağlam kısıtı. Bu isabetler yalnız içerik taramasında; regresyon testi aktif kaynakları tarıyor ve yeşil.

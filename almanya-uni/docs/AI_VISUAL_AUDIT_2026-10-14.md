# "Yapay zeka standardı" görsel denetimi

Tarih: 14 Ekim 2026. Kapsam: **salt okuma**; hiçbir dosya, içerik ya da görsel değiştirilmedi.

Bu raporda "yapay zeka standardı görsel", ziyaretçiye site yapay zekayla üretilmiş izlenimi veren her görsel öğedir:
- ikon yerine kullanılan renkli emoji,
- emoji ile süslenmiş içerik listeleri,
- yapay zekayla üretilmiş fotoğraf ve illüstrasyonlar,
- "gradyan başlık + büyük emoji" kart kalıbı,
- klişe ikonlar (sparkles, rocket).

**Yöntem:**
1. **Canlı tarama.** Sitemap'teki 57 sayfa türünden 85 örnek URL'ye, sitemap dışı 12 sayfa eklendi; toplam 94 sayfa başarıyla tarandı. Görünür metinde ve `alt`, `title`, `aria-label` özniteliklerinde emoji arandı; `<script>` ve `<style>` hariç tutuldu. Her bulgu bölgesiyle birlikte kaydedildi (header, main, footer).
2. **Veritabanı taraması.** Yerel baseline (13.10 prod kopyası) üzerinde yayındaki yazı, SSS, üniversite, şehir, eyalet ve menü kayıtları tarandı.
3. **Kod taraması.** Blade şablonları ve PHP veri dizilerindeki emojiler kaynağa eşlendi.
4. **Görsel analiz.** Canlıdaki 15 öne çıkan görsel indirildi; EXIF ve boyutları incelendi, ikisine gözle bakıldı.

**Önemli ayrım:** `e_icon()` yardımcısı (`app/Support/helpers.php:263`) tanıdığı emojiyi Heroicons SVG'sine çeviriyor. Bu yoldan basılan emojiler (ör. menü sayfalarının DB ikonları) ziyaretçiye **emoji olarak görünmüyor**. Sorun, emojinin `{{ $x['icon'] }}` ile ham basıldığı yerlerde ve içerik metninin kendisinde.

**Düzeltildi (bugün, `02a2f91`, canlıda doğrulandı):** Ana sayfadaki "Your path to Germany in 8 steps" şeridi. 8 emoji Heroicons'a çevrildi.

## Özet

| # | Bulgu | Ölçek | Görünürlük | Öncelik |
|---|---|---|---|---|
| 1 | Yapay zekayla üretilmiş öne çıkan görseller | 15 görsel, **44 yazıda** (12 blog fotoğrafı + 3 haber illüstrasyonu) | Yazı ve haber sayfasının en üstü, OG/paylaşım görseli | **P0** |
| 2 | İçerik metninde emoji (✅ ⚠️ ❌ 💡 ⚖️ 👉) | **346 SSS (5.071 emoji)**, **135 yazı (580 emoji)** | Ham görünüyor (canlıda doğrulandı) | **P0** |
| 3 | Ana sayfa kartları: gradyan başlık + dev emoji | "Popular Programs" ve "Universities by Category" | Ana sayfa, 3 dil | **P0** |
| 4 | Üniversite koleksiyon ikonları (🏛️ 🌍 📝) | `/universities`, `/popular-universities`, koleksiyon sayfaları | Ham | **P0** |
| 5 | Blog infografikleri | 15 yazı, 250 emoji + gradyan başlık | Ham | **P1** |
| 6 | Her sayfadaki ortak öğeler: 🤖 bot kontrolü, 🍪 çerez bandı, 📲 PWA önerisi | 94/94 sayfa | Ham | **P1** |
| 7 | Sayfa şablonlarına gömülü emojiler | 84 public Blade dosyasında 199 satır | Ham | **P1/P2** |
| 8 | Klişe ikonlar ve gradyan kalıbı | `sparkles` 40, `rocket-launch` 10 kullanım; `bg-gradient-to` 114 dosyada 280 kez | SVG, ama "yapay zeka şablonu" hissi | **P2** |
| 9 | Bayraklar (🇩🇪 🇬🇧 🇹🇷) | Dil seçici, dil rozetleri, ülke seçiciler | Ham | **P2** (çoğu işlevsel) |
| 10 | E-postalar | Journey hatırlatma, newsletter karşılama | Ham | P2 |

**Sorunsuz bulunanlar:**
- Üniversite, şehir ve eyalet içeriklerinde **0 emoji** var.
- Menü sayfası ikonları (45 kaydın 44'ü DB'de emoji) `e_icon` ile SVG'ye çevriliyor; header'da emoji görünmüyor.
- Görseli olmayan blog kartlarında yer tutucu emoji yok.

## 1. Yapay zekayla üretilmiş öne çıkan görseller (P0)

44 yayındaki yazı ve haber yalnızca **15 benzersiz görsel** kullanıyor. Her görsel TR/EN/DE kardeşlerinde tekrarlanıyor, yani 15 görsel × 3 dil (bir grupta 2 dil).

| Grup | Dosya | Kanıt | Kullanıldığı yer |
|---|---|---|---|
| Blog, 12 görsel | `storage/blog/*.jpg` (ör. `almanca-bilmeden-ingilizce-master-basvuru-rehberi.jpg`, `almanya-vizesi-sperrkonto-nedir-ne-kadar-nasil-acilir-2026.jpg`) | 12'sinin de EXIF'inde `manufacturer=sana` (bir görüntü üretim modelinin imzası). Hepsi aynı 1059×556 boyutunda. Örnekte fotogerçekçi "kütüphanede öğrenciler" sahnesi; sandalye ve bacaklarda tipik bozulmalar var. | TestDaF/DSH, Sperrkonto, aile birleşimi, çalışma izni, İngilizce master, iş arama, vergi/sigorta yazıları |
| Haber, 3 görsel | `public/images/news/105-568140.png`, `113-d340fb.png`, `115-8e347e.png` | 1408×768 PNG. `113`'te **bozuk yazı** ("Univeriaal Sudents") ve yüzsüz figürler; tipik düz çizgi yapay zeka illüstrasyonu. | DAAD burs haberleri, "best student cities" haberi |

Üretim hattı repoda duruyor: `app/Services/Content/ImageGenerationService.php` (Pollinations.ai / FLUX) ve admin'deki `AssetsRelationManager`.

**Öneri:**
- Bozuk yazılı haber görseli (`113`) hemen kaldırılmalı.
- Diğerleri için seçenekler:
  - (a) Görselsiz yayın. Blog liste kartları zaten görsel kullanmıyor, kaybı az.
  - (b) Lisanslı ya da kendi fotoğraflarımız (Wikimedia Commons'taki gerçek kampüs/şehir fotoğrafları; üniversite ve şehir sayfalarında zaten bu kaynak kullanılıyor).
  - (c) Sitenin renkleriyle, yazısız, tipografik bir OG kartı (mevcut `og.image` route'u).
- `ImageGenerationService` yeni içerikte varsayılan olarak kapatılmalı.

## 2. İçerik metnindeki emojiler (P0)

| Tür | Emoji taşıyan kayıt | Toplam emoji | En sık | En yoğun örnekler |
|---|---|---|---|---|
| SSS (yayında) | **346 / 1.943** | **5.071** | ✅ 3.546 · ⚠️ 866 · ❌ 536 · 🎯 · 🎓 · 📋 · 🥇 · 🚨 | `can-you-find-a-student-job-in-germany-without-speaking-german` (TR ve DE'de 103'er), `how-do-you-find-your-first-internship-or-working-student-position` (TR/EN/DE 68'er) |
| Yazı (yayında) | **135 / 772** | **580** | 💡 157 · ⚖️ 126 · ✅ 73 · ⚠️ 59 · 👉 37 · 📅 · 🏦 · 💸 · ❌ | `bachelor-and-master-thesis-process-in-germany` (TR/EN/DE 36–40), `german-university-grading-system-and-exam-retakes` (27–32) |

**Canlı doğrulama:**
- `/tr/faq/work/can-you-find-a-student-job-in-germany-without-speaking-german` gövdesinde **103** ham emoji var ("✅ Uluslararası şirketler…", "❌ …").
- `/en/blog/bachelor-and-master-thesis-process-in-germany-en` gövdesinde **36** ham emoji var ("⚖️ Official rule", "💡 Practical advice").

**Kalıplar ve öneriler:**
- **✅ / ❌ / ⚠️ madde işaretleri** (SSS'lerin ana kaynağı): klasik "yapay zeka checklist" görünümü. Düz madde listesine, ya da gerçek bir "artı / eksi / dikkat" bileşenine (SVG ikonlu, Markdown'da işaretleyici) çevrilmeli.
- **💡 Practical advice / ⚖️ Official rule** etiketleri (yazılarda): bilgi türü işaretlemesi işlevsel ve değerli; sorun yalnızca emoji. Bunlar `> **Resmî kural:**` gibi bir callout bileşenine dönüştürülmeli.
- **👉, 🎯, 🥇, 🚨** gibi süs emojileri: anlam taşımıyor, silinmeli.

**Uygulama yolu:** Bu düzeltme `content_md` / `answer_md` üzerinde toplu bir data-migration gerektirir (OSV paketleriyle aynı desen: kayıt slug+dil ile bulunur, yalnız emoji dönüştürülür, KB yeniden gömülür). Hiçbir kural değişmez; yalnız biçim değişir.

## 3. Ana sayfa kartları (P0)

| Bölüm | Kaynak | Ne görünüyor |
|---|---|---|
| Popular Programs | `resources/views/home.blade.php:392` `{{ $program['icon'] ?? '🎓' }}` | Gradyan kart başlığında `text-4xl` emoji: ⚖️ 🧪 🎨 💻 (alana göre). |
| Universities by Category | `home.blade.php:505` `{{ $c['icon'] }}`, veri `app/Support/UniversityCollections.php:24/44/199` | Gradyan başlık + 🏛️ 🌍 📝 (kullanıcının ekran görüntüsündeki bölüm). |

**Öneri:** Gradyan + dev emoji kalıbı tamamen bırakılmalı. Yerine sade bir kart: ince Heroicon (ör. `building-library`, `language`, `document-text`) ve nötr arka plan. Ya da kart başlığı hiç olmadan, doğrudan başlık ve sayı.

## 4. Üniversite koleksiyonları (P0)

Aynı `UniversityCollections` ikon verisi, kartlara ek olarak şu yerlerde de ham basılıyor:
- `resources/views/universities/index.blade.php:142`
- `universities/collection.blade.php:61` (sayfa başlığının yanında), `:100` (grup başlıkları), `:151`
- `/en/popular-universities`: 🛠️ 🔬 🩺 🏆 🚀 (`universities/popular.blade.php`, 8 satır)

**Öneri:** Koleksiyon verisinde `icon` alanı emojiden Heroicon adına çevrilip `<x-svg-icon>` ile basılmalı. Journey düzeltmesiyle aynı desen.

## 5. Blog infografikleri (P1)

- `resources/views/components/content/infographic.blade.php`:
  - `:55` `{{ $it['icon'] ?? '📌' }}`
  - `:78`
  - `:88` `{{ $it['icon'] ?? '✅' }}`
  - `:14` gradyan başlık
- Veriyi yapay zeka üretmiş (`storytelling:infographics`).
- 15 yayındaki yazıda toplam 250 emoji. Örnek: `/tr/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh`.

**Öneri:** Bileşen ikonu `e_icon()` ile basmalı. Tanınmayan emoji için ikon gösterilmemeli; varsayılan 📌 / ✅ yerine boş bırakılmalı.

## 6. Her sayfadaki ortak öğeler (P1)

| Öğe | Kaynak | Not |
|---|---|---|
| 🤖 "9 + 4 =" bot kontrolü | `components/math-captcha.blade.php:20, :36` | Newsletter formunda (footer ve sayfa içi), 94/94 sayfada; sayfa başına 2–3 kez. |
| 🍪 çerez bandı | `layouts/app.blade.php:1152` | Her sayfada. |
| 📲 "uygulamayı yükle" önerisi | `components/pwa-install-prompt.blade.php:16` | Her sayfada. |

**Öneri:** Üçü de Heroicon'a çevrilmeli (`shield-check`, `cog` ya da ikonsuz, `computer`/`arrow-down-tray`). Toplam etki büyük, iş küçük.

## 7. Sayfa şablonlarına gömülü emojiler (P1/P2)

Canlıda görülen başlıcaları:

| Sayfa | Emoji | Kaynak |
|---|---|---|
| `/en/tools/studienkolleg` | 🎓 🏢 🏛️ ve **30× 📍** (şehir adlarının önünde) | `tools/studienkolleg.blade.php` |
| `/en/tools/visa-cost` | 📦 📨 🖋️ ✈️ | ToolsController veri dizisi |
| `/en/events` | 🎤 🎵 🎙️ 🎪 📺 🧠 🏔️ 🏭 (kategori ikonları) | `app/Models/Event.php` (64 satır), `events/index.blade.php` |
| `/en/programs`, `/en/fields/*` | 🟢 "NC-free programs", 💶 "Tuition-free" | `programs/index.blade.php`, `fields/show.blade.php` |
| Şehir sayfaları | 📩 🔔 "Notify me" | `events/_alert-subscribe.blade.php` |
| `/en/sozluk` | 📘 💬 🛠️ | `glossary/index.blade.php` |
| `/en/states/*` | ⛰️ "Southern Germany" | `StateController` |
| `/en/map`, `/en/cities` | 💶 "Student Rent Map", 🏙️ | `map/rents.blade.php`, `cities/_grid.blade.php` |
| Ana sayfa arama çipleri | ⭐ "Popular Universities" | `home.blade.php` |
| Diğer | 📭 boş durum, 👍 yorumlar, 💰 reklam alanı, 🎯 alanlar, 🏠 newsletter sonucu | `components/empty-state`, `university-reviews`, `ad-slot`, `fields/index`, `newsletter/result` |

Tam liste (dosya:satır): taramanın `files.json` çıktısında. Public şablonlarda emoji içeren **84 dosyada 199 satır** var; admin (Filament), e-posta ve konsol çıktıları hariç.

**Öneri:** `e_icon()` haritası zaten var. Bu satırlar ya `{!! e_icon('…') !!}`'a sarılmalı ya da doğrudan `<x-svg-icon>` kullanılmalı. Eksik ikonlar (ör. 📍 `map-pin` var; 🎤 / 🎪 yok) için haritaya ekleme yapılmalı.

## 8. Klişe ikonlar ve gradyan kalıbı (P2)

- **`sparkles` 40 kez** kullanılıyor. "Yapay zeka" çağrışımı en güçlü ikon; gerçek bir yapay zeka özelliği (chatbot) dışında kullanılmamalı.
- **`rocket-launch` 10 kez**, journey CTA'sı dahil ("Start your roadmap"). Yerine `arrow-right` ya da ikonsuz buton.
- `trophy` 31, `star` 25, `light-bulb` 24 kullanım: tek tek sorun değil, birlikte "şablon" hissi veriyor.
- `bg-gradient-to-*`: 114 dosyada 280 kullanım. Hero bölümlerinde marka rengi olarak kabul edilebilir. Kart başlıklarında ve rozetlerde gradyan + emoji birleşimi (§3, §5) asıl sorun.

## 9. Bayraklar (P2)

- **İşlevsel, kalabilir:** Dil seçici (`layouts/app.blade.php:609`), ülke seçiciler (Sperrkonto, eligibility checker).
- **Metne çevrilebilir:** Program kartlarındaki "🇬🇧 EN / 🇩🇪 DE / 🌐 EN/DE" dil rozetleri sayfa başına onlarca kez görünüyor. Sade "EN / DE" metin rozeti daha temiz. Ayrıca Windows'ta bayrak emojileri harf çifti ("GB") olarak görünür.

## 10. E-postalar (P2)

`emails/journey-reminder.blade.php:45` ve `emails/newsletter-welcome.blade.php:59` emoji kullanıyor. E-postada SVG güvenilir değil; ikonsuz metin ya da barındırılan PNG ikon tercih edilmeli.

## Önerilen uygulama sırası

| Paket | İçerik | Tür | Tahmini etki |
|---|---|---|---|
| 1 | Ana sayfa kartları + koleksiyon ikonları + ortak öğeler (🤖 🍪 📲) + infografik bileşeni | Yalnızca kod, küçük | Her sayfa ve ana sayfa temizlenir |
| 2 | 15 yapay zeka görseli (önce bozuk yazılı haber görseli) + `ImageGenerationService`'i kapatma | İçerik (`featured_image` alanı) + karar | Yazı başlarındaki yapay zeka fotoğrafları kalkar |
| 3 | SSS ve yazı içeriğindeki emojiler (✅ ❌ ⚠️ → liste/callout; 💡 ⚖️ → callout; süsler silinir) | Data-migration + KB yeniden gömme | 481 kayıt, ~5.650 emoji |
| 4 | Şablonlardaki kalan 199 satır, klişe ikonlar, dil rozetleri, e-postalar | Kod | Tutarlılık |

Her paket ayrı onay ister. Paket 3, içerik metnini değiştirdiği için OSV paketleriyle aynı güvenlik desenini (beklenen metin yoksa atla, tekrar çalıştırmada no-op) kullanmalı.

## Uygulama durumu (14.10.2026, onaylandı ve uygulandı)

Dört paketin hepsi uygulandı. Yerelde doğrulandı; canlıya deploy ve canlı kontrol ayrıca yapılacak.

| Paket | Ne yapıldı |
|---|---|
| 1 | **Ana sayfa:** program kartlarında gradyan + emoji yer tutucu gri zemin + çizgi ikona döndü; "Universities by Category" kartları ikon kutulu sade karta çevrildi; dil rozetleri metin (EN/DE). **Koleksiyon ikonları** Heroicon adı (`UniversityCollections`). **Ortak öğeler:** 🤖 kaldırıldı, 🍪 → `shield-check`, 📲 → `computer`, dil seçicideki bayraklar kaldırıldı. **Infografik** ikonları `e_icon()` ile; başlık düz renk. |
| 1b | **Hero görseli Gemini üretimiydi** (commit 980df31 mesajı: "Gemini ile üretilen … logo temizlendi"). Yerine repodaki gerçek fotoğraf kondu: "Brandenburger Tor abends", Thomas Wolf (www.foto-tw.de), CC BY-SA 3.0, Wikimedia Commons; atıf satırı hero'nun sağ altında. `hero-desktop.webp` ve `hero-mobile.webp` silindi. |
| 2 | Migration `2026_10_14_000200`: yapay zeka kapakları (`blog/` kökü, `/images/news/`, pollinations URL'leri) boşaltıldı, dosyalar silindi; admin yüklemeleri (`blog/featured/`) korunur. Paylaşım görseli artık yazılı OG kartı. Görsel üretimi `IMAGE_GENERATION_ENABLED` (varsayılan **kapalı**) anahtarına bağlandı: `ImageGenerationService`, `NewsService::illustration`, `BlogSplitAuto/Pilot`. |
| 3 | Migration `2026_10_14_000300` + `App\Support\EmojiCleaner` (birim testli): 488 alan (SSS + yazı). Yerelde sonrası: içerik ve üretilen HTML'de **0 emoji**. Yerelde 22 sn. |
| 4 | ~75 şablon, model/controller veri dizileri, e-posta konuları, `lang/*.json` (~60 giriş): emoji → Heroicon / metin. 21 yeni ikon eklendi. `sparkles`/`rocket-launch` süs kullanımları ve satır içi sparkles SVG'leri kaldırıldı. `e_icon()` artık bilinmeyen emojiyi hiç göstermiyor; `<x-svg-icon>` bilinmeyen adda `sparkles` yerine hiçbir şey çizmiyor. |

**Kalanlar (bilinçli):** Yalnız admin panelinde görünen etiketler (Filament seçenek listeleri), `e_icon` eşleme tablosunun kendisi, tipografik oklar (→ ↗ ▼) ve ✓ / ✗ işaretleri.

**Deploy sonrası yapılacak:** KB yeniden gömme (`/admin/ops/kb-embed?source=faq,post`, önce `&dry=1`): içerik hash'leri ~490 kayıtta değişti; OSV-3 KB işiyle aynı adımda yapılabilir.

**Yan bulgu (bu işte değiştirilmedi):** EN yazının OG kartında alt metinler Türkçe ("Almanya başvuru rehberi", "Almanya'da Eğitim", "23 dk okuma"); `OgImageController` kart metnini yazının dilinde üretmeli.

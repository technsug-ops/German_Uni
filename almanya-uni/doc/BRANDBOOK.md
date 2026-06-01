# 🎨 Brandbook — AlmanyaUni / ApplyToGerman

> **Kaynak:** Bu doküman tahminle değil, mevcut koddan **birebir** doğrulanarak çıkarıldı.
> Referans dosyalar:
> `resources/css/app.css` · `config/brand.php` · `app/Support/helpers.php` ·
> `public/img/logos/*.svg` · `resources/views/home.blade.php` ·
> `resources/views/components/brand-logo.blade.php`
>
> **Tarih:** 2026-05-30 · **Restore noktası:** git tag `pre-hero-redesign-2026-05-30`
> **Çalışma branch'i:** `redesign/hero-2026-05-30` (main'e dokunulmadı)

---

## 1. Marka Kimliği (Dual-Brand)

Tek kod tabanı, **iki marka**. Marka seçimi `brand()` helper'ı ile yapılır (`app/Support/helpers.php`). Kural:

> **Domain -> MARKA seçer** (logo, renk, ad). **URL locale öneki -> DİL seçer** (içerik). İkisi bağımsız: `almanyauni.com/en` = AlmanyaUni markası + İngilizce içerik.

Domain haritası (`config/brand.php`):

| Domain | Marka |
|---|---|
| almanyauni.com | almanyauni |
| applytogerman.com | applytogerman |
| germanyuni.com | applytogerman |
| localhost / 127.0.0.1 | almanyauni (varsayılan) |

Varsayılan (host eşleşmezse — CLI/test): `env('BRAND', 'almanyauni')`.

### Marka değerleri (`config/brand.php`)

| Anahtar | **AlmanyaUni** | **ApplyToGerman** |
|---|---|---|
| `name` | AlmanyaUni | ApplyToGerman |
| `domain` | almanyauni.com | applytogerman.com |
| `default_locale` | `tr` | `en` |
| `theme_color` (PWA) | `#1e40af` | `#0f172a` |
| `tagline.tr` | Almanya'da eğitim rehberin | Almanya başvuru rehberi |
| `tagline.en` | Your guide to studying in Germany | Apply to study in Germany |
| `tagline.de` | Dein Wegweiser fürs Studium in Deutschland | Bewerbungsleitfaden für Deutschland |
| `logo` | /img/logos/almanyauni.svg | /img/logos/applytogerman.svg |
| `logo_white` | /img/logos/almanyauni-white.svg | /img/logos/applytogerman-white.svg |
| `favicon` | /favicon.ico | /favicon-atg.ico |
| `og_image` | /img/og/almanyauni.png | /img/og/applytogerman.png |
| `twitter` | @almanyauni | @applytogerman |
| `mail_from` | merhaba@almanyauni.com | hello@applytogerman.com |
| `fallback` (host eşleşmezse) | **almanyauni** (config'de `'fallback' => 'almanyauni'`) | — |

> ⚠️ Marka adı / e-posta / logo'yu **asla hardcode etme** — `brand('name')`, `brand('logo_white')` vb. kullan.

---

## 2. Logo

Logolar **gerçek SVG dosyaları** (`public/img/logos/`), Blade bileşeni ile basılır:
`<x-brand-logo variant="white" />` (header'da beyaz varyant) — `resources/views/components/brand-logo.blade.php`.

Her marka için 2 varyant: normal + `-white`. Boyut prop varsayılanı `h-8 md:h-9 w-auto`.

### 2.1 AlmanyaUni logosu (`almanyauni.svg`, viewBox 0 0 220 48)
- **Rozet:** `rounded` kare (rx=8), altın gradyan **`#fbbf24` -> `#f59e0b`**
  - içinde lacivert üçgen `#1e3a8a` + kırmızı daire `#dc2626` (TR + DE çağrışımı)
- **Wordmark:** "Almanya" (`#1e3a8a` lacivert) + "Uni" (`#dc2626` kırmızı)
- Font: Inter, **800 (extrabold)**, `font-size 22`, `letter-spacing -0.5`

### 2.2 ApplyToGerman logosu (`applytogerman.svg`, viewBox 0 0 260 48)
- **Rozet:** Alman bayrağı 3 dikey şerit — siyah `#000000` · kırmızı `#dc2626` · altın `#fbbf24`
- **Wordmark:** "Apply" (`#0f172a`) + "To" (`#64748b`, **500/medium**) + "German" (`#dc2626`)
- Font: Inter, 800 (extrabold), "To" hariç; `letter-spacing -0.5`

### 2.3 Favicon / OG
- AlmanyaUni: `/favicon.ico` · OG `/img/og/almanyauni.png`
- ApplyToGerman: `/favicon-atg.ico` · OG `/img/og/applytogerman.png`
- OG görselleri ayrıca dinamik üretilebiliyor: `OgImageController`, cache key brand-izole (`og/{brandKey}/...png`)

---

## 3. Renk Paleti

> **Tek kaynak:** `resources/css/app.css` `@theme` bloğu (Tailwind v4 — ayrı `tailwind.config.js` paleti YOK). Sınıflar: `primary-*`, `accent-*`.

### 3.1 `primary` (lacivert) — birincil

| Ton | Hex | | Ton | Hex |
|---|---|---|---|---|
| 50 | `#f0f6fc` | | **500** | **`#1e40af`** (ana lacivert) |
| 100 | `#e0ecf9` | | 600 | `#1a368d` |
| 200 | `#c1d9f3` | | **700** | **`#162c6b`** (hero üst) |
| 300 | `#a2c6ed` | | **800** | **`#122249`** (hero alt) |
| 400 | `#83b3e7` | | 900 | `#0e1827` |

### 3.2 `accent` (turuncu) — vurgu

| Ton | Hex | | Ton | Hex |
|---|---|---|---|---|
| 50 | `#fff7ed` | | **500** | **`#f97316`** (ana turuncu / buton) |
| 100 | `#ffeddb` | | 600 | `#d95c0e` |
| 200 | `#ffdbb7` | | 700 | `#b9450c` |
| 300 | `#ffc993` | | 800 | `#952e0a` |
| 400 | `#ffb76f` (vurgu metni) | | 900 | `#712108` |

### 3.3 Nötrler (app.css `@layer base`)
- Sayfa zemini: `#f8fafc` (slate-50) · Metin: `#0f172a` (slate-900)
- Tema **sadece açık (light)** — dark mode kasıtlı kapalı (`app.css:42-45`).

### 3.4 Fiili hero gradyanı (`home.blade.php:14`)
```
bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800
= #162c6b -> #1a368d -> #122249  (135 derece)
```
Üstüne `opacity-10` beyaz nokta deseni (radial-gradient, 24px grid).

> 🎯 **Mockup notu:** Verdiğin mockup'ın mavisi koddakinden biraz **daha koyu**. **Karar: mevcut renk kodlarına sadık kal** — hero `primary-700/600/800` zinciri korunur, mockup'a göre renk koyulaştırma YAPILMAZ. Sadece layout/yerleşim mockup'tan ilham alınır.

---

## 4. Tipografi

> **Tek font ailesi: Inter** — self-hosted (`@fontsource-variable/inter`), Google Fonts **yok**. Ayrı başlık fontu yok (Lexend kullanılmıyor).

`app.css @theme`:
```css
--font-sans:    'Inter Variable', 'Inter', ui-sans-serif, system-ui, sans-serif;
--font-display: 'Inter Variable', 'Inter', ui-sans-serif, system-ui, sans-serif;
```

- Variable font -> 100–900 arası tüm ağırlıklar tek dosyada
- Kullanım deseni: H1 `font-extrabold` (800/900) · başlık `font-bold` (700) · kart başlığı `font-semibold` (600) · gövde 400
- Hero H1 ölçeği: `text-4xl md:text-5xl lg:text-6xl font-extrabold leading-[1.05]`

---

## 5. UI Bileşen Desenleri (hero referansı — `home.blade.php`)

| Bileşen | Sınıf deseni |
|---|---|
| **Hero zemin** | `bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white` |
| **Üst badge** | `bg-white/10 border border-white/20 backdrop-blur px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide` + `bg-accent-400 animate-pulse` nokta |
| **H1 vurgu** | `<span class="text-accent-400">…</span>` |
| **Arama kutusu** | `bg-white p-2 rounded-xl shadow-2xl` |
| **Ara butonu** | `bg-accent-500 hover:bg-accent-600 active:bg-accent-700 px-7 py-3 rounded-lg font-semibold shadow-md` |
| **Harita CTA** | `bg-white/10 hover:bg-white/20 border-2 border-white/30 backdrop-blur rounded-xl` |
| **Chip (popüler)** | `bg-white/10 hover:bg-white/20 border border-white/15 px-3 py-1 rounded-full` |
| **Chip (tool/accent)** | `bg-accent-500/20 hover:bg-accent-500/30 border border-accent-400/30` |
| **Stat kartı (sağ)** | `bg-white/10 backdrop-blur-sm border border-white/15 rounded-2xl p-6 shadow-2xl` |
| **Stat sayısı** | `text-4xl font-extrabold` (vurgulu olan `text-accent-400`) |

Konteyner: `max-w-[1400px] mx-auto px-4 py-16 md:py-24 grid lg:grid-cols-12 gap-10` (sol 7 / sağ 5 kolon).

İçerik kartları (alt bölümler) tematik gradyan kullanır: emerald / blue / purple `from-*-50 via-white to-*-50/40`.

---

## 6. Kurallar (do / don't)

✅ **Yap**
- Vurgu = **`accent-500` #f97316** (buton), **`accent-400` #ffb76f** (metin vurgusu)
- Lacivert zemin = **`primary-700/800`**; ana mavi = **`primary-500` #1e40af**
- Tüm metin **Inter** (`font-sans`/`font-display` ikisi de Inter)
- Marka adı/logo/e-posta -> **`brand()` helper**

🚫 **Yapma**
- Hero rengini **mockup'a uydurmak için koyulaştırma** — kod kazanır
- Marka adını/domaini/logoyu **hardcode** etmek
- Palet dışı yeni renk icat etmek
- Hardcoded TR metin (EN yaz + `__()` + `lang/{tr,de}.json`)
- Lexend / Google Fonts eklemek (font self-hosted Inter)

---

## 7. Geri dönüş (revert)

```bash
git checkout main
git reset --hard pre-hero-redesign-2026-05-30
```

Redesign ayrı branch'te: `redesign/hero-2026-05-30`. Frontend snapshot tag'i de mevcut: `v1-frontend-snapshot-2026-05-30`.

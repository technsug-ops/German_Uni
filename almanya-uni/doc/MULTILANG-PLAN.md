# Çok Dilli İçerik Planı & Sistemi

**Hedef:** AlmanyaUni alanında dünyanın en iyisi olacak — çok dilli, her dilde **native + SEO + az "AI-kokan"** içerik. Bu doküman, yeni dil eklemenin ve kaliteli içerik üretmenin **tek sistemini** tanımlar.

Güncellendi: 2026-05-31. İlgili: `doc/I18N-STYLE-GUIDE.md` (register/terminoloji), `config/locale.php` (registry), `app/Services/Content/ContentVoice.php`.

---

## 1. Tek Registry — `config/locale.php`

Tüm diller TEK yerde tanımlı. Artık dil listesi servislerde tekrarlanmıyor (drift bitti).

Her locale: `name`, `native_name`, `flag`, `active`, `coming_soon`, `direction` (ltr/**rtl**), `date_format`.

Mevcut (2026-05-31): **aktif** tr/en/de · **coming_soon** fr, es, it, pt, ru, pl, zh, ar(rtl), fa(rtl).

- `active=true` → UI'da görünür, root routing'e dahil.
- `coming_soon=true` → içerik üretimi/çevirisi yapılabilir, UI'da henüz yok.
- `content_fallback` + `content_fallback_default` (en→de→tr) → DB kolon fallback'i; yeni dil ektra giriş gerektirmez.

## 2. Voice & Kalite — `ContentVoice` (registry-driven, hardcode minimum)

`ContentVoice::for($lang)` → o dilin **register + native terminoloji + evrensel anti-AI-slop** direktifini döndürür.

- **Tailored profil** sadece doğrulanmış dillerde: **tr** (sen), **en** ("you", AmE), **de** (du, study-in-germany.de). 
- **Jenerik fallback** diğer TÜM diller için: registry'den `native_name`/`direction` okur → *"{native} dilinde native, informal, Almanca terimleri koru+açıkla, RTL ise dikkat"*. **Yeni dil KOD DEĞİŞİKLİĞİ olmadan native voice alır.**
- **HUMAN_QUALITY** (her dil): somut sayı/isim, AI-klişe yasağı, robotik geçiş yok, emoji/em-dash aşırılığı yok, topluluk diline dayan, uydurma yok. → "AI-kokusu" minimum.

Tüm üreticiler bunu kullanır: `ContentPrompts` (Content Factory, 16 format), `ContentTranslator` (10+ dil), `AiContentBlockGenerator` (enrichment).

## 3. Yeni Dil Ekleme — Checklist (dakikalar)

1. `config/locale.php` → locale girişi ekle (name, native_name, flag, direction, `coming_soon=true`).
2. `lang/<xx>.json` oluştur (EN'den kopya başlangıç; `php artisan i18n:audit` ile boşlukları gör).
3. İçerik: `php artisan content:translate-assets --lang=<xx>` ve/veya brief→asset üretimi (voice otomatik uygulanır).
4. DB içeriği: gerekiyorsa `*_<xx>` kolon + çeviri (uni/program açıklamaları); yoksa fallback en→de→tr devreye girer.
5. QA: `php artisan i18n:audit --leaky` + `/xx/...` smoke test.
6. Hazır olunca `active=true` + (RTL ise) layout `dir` kontrolü.

> Tailored voice profili (opsiyonel): dil olgunlaşınca `ContentVoice::TAILORED`'a ekle. Şart değil — jenerik zaten native üretir.

## 4. RTL (ar, fa)

Registry'de `direction=rtl`. Layout `<html dir="{{ config('locale.locales.'.app()->getLocale().'.direction') }}">` ile sarılmalı (kontrol/uygula). Voice fallback RTL notu ekliyor. Tailwind `rtl:` varyantları kritik bileşenlerde gözden geçirilmeli.

## 5. SEO (her dil)

- hreflang + self-canonical + sitemap = mevcut i18n mimarisi (route-aware) tüm registry dillerini otomatik kapsamalı (doğrula).
- Native arama terimleri: ContentVoice terminoloji haritası (Studiengänge, English-taught programs, vb.) → arama hacmi yakalanır.
- Meta title/description her dilde native (birebir çeviri değil) — translator artık register'ı zorluyor.

## 6. İki Katman (karıştırma)

1. **Biçim/voice tutarlılığı** [bu sistem]: register (du/you/sen + jenerik), native terim, az-AI. Otomatik, her dil.
2. **Topluluk-güdümlü içerik derinliği** [ayrı pass]: Forum 120K + Telegram 142K + visa/denklik 716K havuzundan gerçek sorular/pain-point → hangi içerik eksik, hangi FAQ gerekli. `[[feedback-community-insights-mandatory]]` — her ÜRETİMDE zorunlu girdi.

## 7. Rollout Önceliği (öneri)

tr (birincil) → en/de (aktif) → fr/es (büyük uluslararası kitle) → ar/fa (yüksek Almanya'ya öğrenci akışı, RTL) → ru/pl/it/pt → zh. Her dilde: UI → hero/landing → tool sayfaları → blog → kategori/şehir/üni intro.

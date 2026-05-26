<?php

namespace App\Services\Content;

use App\Models\ContentBrief;

/**
 * Asset türü başına AI prompt template'leri.
 * Brief'in tüm meta'sını alır, AI'a structured talimat döndürür.
 */
class ContentPrompts
{
    public function build(ContentBrief $brief, string $assetType): string
    {
        $base = $this->baseContext($brief);

        return match ($assetType) {
            'blog'          => $this->blog($brief, $base),
            'youtube_long'  => $this->youtubeLong($brief, $base),
            'youtube_short' => $this->youtubeShort($brief, $base),
            'tiktok'        => $this->tiktok($brief, $base),
            'instagram'     => $this->instagram($brief, $base),
            'twitter'       => $this->twitter($brief, $base),
            'linkedin'      => $this->linkedin($brief, $base),
            'pinterest'     => $this->pinterest($brief, $base),
            'podcast'       => $this->podcast($brief, $base),
            'newsletter'    => $this->newsletter($brief, $base),
            'visual_brief'  => $this->visualBrief($brief, $base),
            default => throw new \InvalidArgumentException("Unknown asset: $assetType"),
        };
    }

    private function baseContext(ContentBrief $brief): string
    {
        $audience = ContentBrief::AUDIENCES[$brief->audience] ?? $brief->audience;
        $tone = ContentBrief::TONES[$brief->brand_tone] ?? $brief->brand_tone;
        $sourceQ = $brief->source_questions ? "\n\nTopluluk'tan gerçek sorular (referans, doğal kullanıcı dilini yansıt):\n- " . implode("\n- ", array_slice($brief->source_questions, 0, 10)) : '';

        return <<<TXT
KONU: {$brief->title}
HEDEF KİTLE: $audience
ANA ANAHTAR KELİME: {$brief->primary_keyword}
İKİNCİL KELİMELER: {$this->kw($brief)}
PAIN POINT: {$brief->pain_point}
TONLAMA: $tone
PROJE: AlmanyaUni — Türk öğrencilerin Almanya'da eğitim alma rehberi (almanyauni.de){$sourceQ}

ÖNEMLİ İLKELER:
- Türkçe yaz, Almanca terimleri açıkla (örn: "Sperrkonto (bloke hesap)").
- Tahmin/yalan/hayali sayı kullanma. Bilmiyorsan "üniversitenin resmi sayfasından doğrulayın" de.
- Doğal Türk öğrenci dili (Telegram dilinden ipuçları yukarıda).
TXT;
    }

    private function kw(ContentBrief $brief): string
    {
        return $brief->secondary_keywords ? implode(', ', $brief->secondary_keywords) : '-';
    }

    private function blog(ContentBrief $brief, string $base): string
    {
        $words = $brief->target_word_count;
        return $base . "\n\n" . <<<TXT
GÖREV: SEO-optimize edilmiş Türkçe blog yazısı yaz.
HEDEF UZUNLUK: $words kelime (±%10).

ÇIKTI FORMATI (saf Markdown):
---
title: "60 char altı SEO başlık"
slug: "kebab-case-slug"
meta_description: "155 char altı açıklama, ana anahtar kelime ilk 100 char içinde"
focus_keyword: "{$brief->primary_keyword}"
schema_type: "Article" veya "FAQPage"
---

# H1 başlık (ana anahtar kelime dahil)

> **Hook (2-3 cümle)** — okuyucuyu yakala, pain point'i tanı.

## H2: Bölüm 1
İçerik...

## H2: Bölüm 2
İçerik...

## H2: SSS (Sıkça Sorulan Sorular)
**Soru 1:** ...
**Cevap:** ...

## H2: Sonuç + CTA
- Önerilen iç linkler: /universities, /faq, /tools/cost-of-living
- CTA: "Newsletter'a abone ol" veya "Üniversite araması yap"

EK GEREKSİNİMLER:
- Her H2'de 1-2 internal link önerisi (path notasyonu).
- Schema markup için JSON-LD blok ekle ARTIKEL sonunda (yorum içinde).
- 3-5 long-tail keyword'ü doğal şekilde yerleştir.
TXT;
    }

    private function youtubeLong(ContentBrief $brief, string $base): string
    {
        return $base . "\n\n" . <<<TXT
GÖREV: YouTube uzun video senaryosu (5-15 dk).

ÇIKTI FORMATI (Markdown):

# Video Başlığı (60 char, CTR-odaklı)

**Süre hedefi:** [N] dakika
**Tahmini Word Count:** [N×150 kelime]

## 📺 Açıklama (Description)
[2-3 paragraf, ilk 125 char önemli — feed preview]
- Timestamps:
- 00:00 Intro
- ...
- Hashtags + emoji
- Affiliate/CTA linkleri

## 🎬 SENARYO

### Hook (0-15 sn)
[Pattern interrupt, izleyici tutucu açılış]

### Giriş (15-45 sn)
[Konuyu çerçevele, neyi öğrenecekler]

### Ana içerik (1-2 dk segmentler)
**Segment 1: [başlık]**
- B-roll önerisi:
- Söylenecek:
- On-screen text:

**Segment 2: ...**
[devam et]

### CTA + Kapanış (son 30 sn)
- "Beğen, abone ol" prompt
- Bir sonraki video link önerisi
- Almanyauni.de linkı

## 🎨 Görsel İpuçları
- Thumbnail önerisi (3 varyasyon)
- Renk paleti: AlmanyaUni mavi #1E40AF + turuncu #F97316
TXT;
    }

    private function youtubeShort(ContentBrief $brief, string $base): string
    {
        return $base . "\n\n" . <<<TXT
GÖREV: YouTube Shorts senaryosu (60 saniye, dikey).

ÇIKTI:

# Shorts Başlığı (40 char altı)

## 🎬 60 saniyelik senaryo (saniye-saniye)
**0-3 sn HOOK:** [Şok edici tek cümle / soru]
**3-15 sn KONU:** [Pain point + hızlı tanım]
**15-45 sn ÇÖZÜM:** [3-4 hızlı bullet]
**45-55 sn ÖRNEK:** [Somut sayı/örnek]
**55-60 sn CTA:** [Takip et + linkbio]

## 🎨 On-screen text (her segmentte ne yazsın)
[Bold caption'lar]

## 🎵 Müzik/SFX önerisi
[Trending Almanca/TR sound]

## #️⃣ Hashtags (5-8 adet)
#almanyaunisi #almanyadayasam #...
TXT;
    }

    private function tiktok(ContentBrief $brief, string $base): string
    {
        return $base . "\n\n" . <<<TXT
GÖREV: TikTok 30-60 saniyelik video senaryosu (dikey, gen-z dili).

ÇIKTI:

## ⏱️ Süre: [30/45/60] saniye

## 🪝 HOOK (0-3 sn)
[Pattern interrupt — "Almanya'ya gidenler bilir" / "Kimse söylemiyor ama..." formatı]

## 📜 SENARYO (saniye saniye)
**0-3 sn:** [Hook on-screen text + söz]
**3-10 sn:** [Pain point itirafı]
**10-25 sn:** [3 hızlı tip / fact bomb]
**25-50 sn:** [Detay + örnek]
**50-60 sn:** [CTA: "Yorum bırak", "Kaydet", "Takip et"]

## ✏️ On-screen captions (her sahne)
[Her 2-3 saniyede 1 caption]

## 🎵 Trending sound önerisi
[TR / EU TikTok'unda popüler ses tipi]

## #️⃣ Hashtags (carousel için)
Trend: #almanyaunisi #almanyahayat #öğrencihayatı
Niche: #vize #sperrkonto #unistart
Geniş: #almanya #fyp #türkiye

## 💬 Caption (post altında)
[Soru + emoji + 5 hashtag]
TXT;
    }

    private function instagram(ContentBrief $brief, string $base): string
    {
        return $base . "\n\n" . <<<TXT
GÖREV: Instagram için İKİ format çıktısı: Carousel (8 slide) + Reel (30s).

## 📚 CAROUSEL — 8 SLIDE

**Slide 1 (Kapak):** [Pattern interrupt başlık, mavi-turuncu paletten]
**Slide 2:** Pain point tanıma
**Slide 3-6:** 4 ana nokta (her slide 1 konu, kısa)
**Slide 7:** Özet / kontrol listesi
**Slide 8:** CTA (Save + Share + linkbio)

Her slide için: Başlık + 2-3 cümle + ikon önerisi

## 🎬 REEL (30 saniye)

**0-3 sn HOOK:** ...
**3-15 sn İçerik:** ...
**15-25 sn Detay:** ...
**25-30 sn CTA:** ...

On-screen text:
Music:

## 📝 CAPTION (post altı)
[2-3 paragraf storytelling + soru + 8-12 hashtag]

## #️⃣ HASHTAGS
- Trending TR: ...
- Niche: ...
- Branded: #almanyaunisi
TXT;
    }

    private function twitter(ContentBrief $brief, string $base): string
    {
        return $base . "\n\n" . <<<TXT
GÖREV: Twitter/X thread (8-12 tweet).

## ÇIKTI:

**Tweet 1 (HOOK):** [240 char altı, sayı/şok/soru ile başla]

**Tweet 2:** [Pain point]

**Tweet 3-9:** [Ana noktalar — her tweet self-contained]

**Tweet 10 (CTA):** "Beğen+RT'le", profil linki

Kurallar:
- Her tweet 280 char altı (URL'ler hariç)
- Emoji minimum, sadece numbered list başında
- Önemli rakamları **bold yerine MAJUSCULE** (bold yok Twitter'da)
- Thread sonunda "1 dakika kalsın aşağı kaydır 👇" gibi engagement prompt
TXT;
    }

    private function linkedin(ContentBrief $brief, string $base): string
    {
        return $base . "\n\n" . <<<TXT
GÖREV: LinkedIn professional post (1.000-2.000 char).
HEDEF: Almanya'da mezun olmuş + iş arayan + veli + danışman kitlesi.

## ÇIKTI:

**HOOK (ilk 3 satır — feed preview kritik):**
[Storytelling açılış — kişisel deneyim / şok sayı]

**Ana içerik:**
[3-5 paragraf, her paragraf 2-3 cümle. Beyaz boşluk önemli LinkedIn'de.]

**Insight / Takeaway:**
[1-2 cümle ders / öğüt]

**CTA:**
[Soru sor — yorum etkileşimi için]

**Hashtag (3-5):**
#almanyadakariyer #studieren #türkmezunlar #...

**STİL:**
- Professional ama hikaye odaklı
- "Ben yaşadım", "Şunu fark ettim" tarzı 1. tekil şahıs
- Almanca terim varsa İngilizce çeviri parantez içinde (uluslararası okuyucu için)
TXT;
    }

    private function pinterest(ContentBrief $brief, string $base): string
    {
        return $base . "\n\n" . <<<TXT
GÖREV: Pinterest pin (görsel-yoğun, SEO-discoverable).

## ÇIKTI:

**Pin Title:** [100 char, anahtar kelime + sayı]
Örn: "Almanya'da Öğrenci Olmak: 2026 Sperrkonto Rehberi (12 Adımda)"

**Pin Description:** [500 char, paragraflı, hashtag dahil]
- İlk cümle hook
- Pin'in ne sunduğunu listele (numbered)
- Hashtag #almanya #öğrenci #studieren

**Visual Brief:**
- Boyut: 1000×1500px (2:3 ratio standart Pin)
- Font: Inter Bold başlık
- Renk: AlmanyaUni mavi #1E40AF arka plan, beyaz başlık, turuncu accent #F97316
- Stil: Infografik (numbered steps, ikon-yoğun) veya before/after / quote

**Board önerisi:**
- "Almanya Öğrenci Rehberi"
- "Avrupa'da Eğitim"
- "Vize Rehberleri"

**Tip:** Pinterest'te long-form text overlay çok iyi performans verir. Pin'de ana konuyu açıkça yaz.
TXT;
    }

    private function podcast(ContentBrief $brief, string $base): string
    {
        return $base . "\n\n" . <<<TXT
GÖREV: Podcast bölümü outline (10-25 dk).

## ÇIKTI:

# Bölüm Başlığı (60 char altı, iTunes/Spotify SEO)

**Süre hedefi:** [N] dk
**Format:** Solo monolog / İki konuşmacı / Röportaj

## 🎙 STRUCTURE

### 0:00 — Intro Theme + Hook (30 sn)
[Tek cümle hook + show intro music cue]

### 0:30 — Konu açılışı (1-2 dk)
[Bağlam, neden bu bölüm, kime hitap]

### 02:00 — Segment 1: [başlık] (3-5 dk)
[Talking points + örnek + alıntı]

### 07:00 — Segment 2 / Konuk röportajı (varsa)
[Sorular + cevap özetleri]

### 12:00 — Sponsor / Newsletter break (30 sn)
"AlmanyaUni newsletter'a abone ol"

### 13:00 — Segment 3 / Aksiyon listesi
[3-5 takeaway]

### 18:00 — Sonuç + CTA
- Bir sonraki bölüm preview
- "Yorum, abone, paylaş" prompt
- Twitter/Instagram hesabı

## 📝 SHOW NOTES
[Outline'ın 200 kelimelik özeti + zaman damgaları + bahsedilen tüm linkler]

## 🎵 Müzik cue önerisi
- Intro: enerjik
- Segment geçişi: minimal beat
- Outro: yumuşak fade
TXT;
    }

    private function newsletter(ContentBrief $brief, string $base): string
    {
        return $base . "\n\n" . <<<TXT
GÖREV: Newsletter section (e-posta için, mevcut AlmanyaUni newsletter sistemine eklenebilir).

## ÇIKTI:

**Subject Line A (test 1):** [40 char altı, açma oranı için]
**Subject Line B (test 2):** [alternatif test]
**Preheader:** [90 char, subject'i tamamlar]

## 📧 GÖVDE

**Açılış (30-50 kelime):**
[Kişisel "merhaba" + bu hafta neyi paylaşacaksın]

**Ana bölüm (200-400 kelime):**
[Blog özetinin kısa hali, en kritik 3 takeaway]

**Pro tip box:** 🎯
[1 cümlelik özel ipucu]

**CTA:**
"Detaylı yazıyı oku → [almanyauni.de/blog/slug]"
"Profilini güncelle / Üye ol"

**Kapanış (20-30 kelime):**
[İmza + bir sonraki sayı teaser]

## 📊 PERFORMANS NOTLARI
- Open rate hedefi: %30+
- CTR hedefi: %5+
- A/B test subject line öner

## 🎨 GÖRSEL
- Header banner: konu + AlmanyaUni logo
- Tek "feature image" önerisi (1200x600)
TXT;
    }

    private function visualBrief(ContentBrief $brief, string $base): string
    {
        return $base . "\n\n" . <<<TXT
GÖREV: AI image generation prompt'ları (Midjourney v6 / DALL-E 3 / Imagen 3 / Stable Diffusion).

## ÇIKTI: 4 adet farklı görsel prompt

### 1. Blog header image (1200x630, OG image)
**Prompt:** [İngilizce, detaylı, --ar 16:9 parametresi dahil]
**Negatif prompt:** (Stable Diffusion için)
**Style hints:** AlmanyaUni brand renkleri (#1E40AF, #F97316), modern flat illustration

### 2. Social media (1080x1080 square)
**Prompt:** ...

### 3. Pinterest (1000x1500 vertical)
**Prompt:** ...

### 4. YouTube thumbnail (1280x720 with text overlay area)
**Prompt:** ...

## 🎨 BRAND CONSTRAINTS (her promptta uy)
- Renk: deep blue (#1E40AF), warm orange (#F97316), white, soft gray
- Stil: modern flat illustration, friendly, professional but approachable
- Hedef kitle: 18-30 yaş Türk öğrenciler
- Almanya bağlamı: bayraklar, mimari (Brandenburg Tor / Neuschwanstein), öğrenci kıyafetleri
- KAÇIN: stereotyped, kitsch, eski Almanya görselleri

## 📝 ALT TEXT (her görsel için, SEO)
[60-125 char, primary keyword dahil]
TXT;
    }
}

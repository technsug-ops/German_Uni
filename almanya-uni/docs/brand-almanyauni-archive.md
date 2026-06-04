# AlmanyaUni — Marka Kimliği Arşivi (KORUNDU, şu an gizli)

> **Durum (2026-06-04):** Asıl marka **ApplyToGerman** oldu. AlmanyaUni'nin
> **ayrı görsel kimliği (logo) gizlendi**; isim yalnızca **"ApplyToGerman (AlmanyaUni)"**
> şeklinde parantezde geçiyor. Bu dosya, AlmanyaUni kimliğini **silmeden korur** —
> ileride gerekirse (ör. almanyauni.com'u ayrı marka olarak yeniden açmak) buradan geri dönülür.

## Korunan görsel varlıklar (repo'da duruyor, aktif kullanılmıyor)
- `public/img/logos/almanyauni.svg` — ana logo
- `public/img/logos/almanyauni-white.svg` — beyaz (koyu zemin) logo
- `public/img/og/almanyauni.png` — OG/sosyal paylaşım görseli
- `public/favicon.ico` — AlmanyaUni favicon

> Bu dosyalar SİLİNMEDİ. `config/brand.php`'de `almanyauni` markası artık bunlara
> değil ApplyToGerman görsellerine işaret ediyor (logo gizli). Geri açmak için
> aşağıdaki orijinal config değerlerini geri yaz.

## Orijinal `config/brand.php` → `almanyauni` değerleri (geri dönüş için)
```php
'almanyauni' => [
    'name'           => 'AlmanyaUni',
    'tagline'        => [
        'tr' => 'Almanya\'da eğitim rehberin',
        'en' => 'Your guide to studying in Germany',
        'de' => 'Dein Wegweiser fürs Studium in Deutschland',
    ],
    'domain'         => 'almanyauni.com',
    'logo'           => '/img/logos/almanyauni.svg',
    'logo_white'     => '/img/logos/almanyauni-white.svg',
    'favicon'        => '/favicon.ico',
    'og_image'       => '/img/og/almanyauni.png',
    'twitter'        => '@almanyauni',
    'mail_from'      => 'merhaba@almanyauni.com',
    'mail_from_name' => 'AlmanyaUni',
    'copyright'      => 'AlmanyaUni',
    'apple_title'    => 'AlmanyaUni',
    'default_locale' => 'tr',
    'theme_color'    => '#1e40af',
],
```

## Korunan diğer kimlik öğeleri
- **Birincil renk:** `#1e40af` (primary blue) — NOT: bu renk sitede zaten kullanılıyor; "renge dokunma" kuralı gereği değiştirilmedi.
- **Tagline (TR):** "Almanya'da eğitim rehberin"
- **Twitter/X:** @almanyauni
- **E-posta:** merhaba@almanyauni.com
- **Domain:** almanyauni.com (DNS henüz KAS'a bağlı değil — eski hosting'de)

## İlgili
İlgili karar geçmişi: [[dual_brand_setup]]. Marka değişimi 2026-06-04: ApplyToGerman birincil, AlmanyaUni parantez + arşiv.

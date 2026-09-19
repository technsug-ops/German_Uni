# Reklam Kurulumu (AdSense + affiliate + doğrudan satış)

Bu doküman sitedeki reklam katmanının nasıl çalıştığını ve **AdSense'i açmak için elle
yapılması gereken adımları** anlatır. Kod tarafı hazır; eksik olan tek şey hesap bilgileri.

---

## 1. Slot önceliği

`resources/views/components/ad-slot.blade.php` tek bir bileşen üzerinden üç katmanı yönetir:

| Sıra | Katman | Koşul |
|---|---|---|
| 1 | **Affiliate kartı** | `AFFILIATE_*_URL` env'i dolu (ortak aktif). Premium üyelerde de gösterilir — içerik-uyumlu sponsor sayılır. |
| 2 | **AdSense** | `ADSENSE_CLIENT_ID` + ilgili slot ID dolu **ve** ziyaretçi çerez onayı vermiş **ve** kullanıcı premium değil. |
| 3 | **"Reklam Ver" daveti** | Yukarıdakiler yoksa: boş kutu yerine `/{locale}/advertise` sayfasına götüren davet kartı. `ADS_HOUSE_ENABLED=false` ile kapatılır. |

Hiçbiri yoksa (ör. premium kullanıcı + davet kapalı) **hiçbir şey basılmaz** — boş kutu görünmez.

Kullanım:

```blade
<x-ad-slot type="banner" slot="banner_top" />
<x-ad-slot type="affiliate-card" :context="$context" />   {{-- visa | sperrkonto | insurance --}}
```

---

## 2. AdSense'i açmak için gerekenler

### a) env değişkenleri (prod `.env`)

```
ADSENSE_CLIENT_ID=ca-pub-XXXXXXXXXXXXXXXX
ADSENSE_SLOT_BANNER_TOP=1234567890
ADSENSE_SLOT_IN_CONTENT=1234567890
ADSENSE_SLOT_SIDEBAR=1234567890
ADSENSE_SLOT_BANNER_BOTTOM=1234567890
```

Sadece `ADSENSE_CLIENT_ID` girilirse hiçbir slot görünmez; **her yerleşim için ayrı slot ID** gerekir.
Slot ID'leri AdSense panelinde "Reklamlar → Reklam birimine göre" bölümünden üretilir.

### b) `ads.txt`

`/ads.txt` **otomatik** üretilir (rota: `routes/web.php`). `ADSENSE_CLIENT_ID` boşken bilinçli olarak
404 döner — boş/yanlış bir ads.txt, hiç olmamasından daha zararlıdır. Client ID girildiği an dosya
şu içerikle yayına girer:

```
google.com, pub-XXXXXXXXXXXXXXXX, DIRECT, f08c47fec0942fa0
```

Kontrol: `curl https://applytogerman.com/ads.txt`

### c) AEA/İngiltere trafiği için onay mekanizması (ZORUNLU)

Google, Avrupa Ekonomik Alanı ve İngiltere'deki kullanıcılara reklam gösterirken **Google
sertifikalı bir onay mekanizması (CMP)** şart koşar. Sitenin kendi çerez banner'ı Consent Mode v2
sinyallerini gönderiyor ama **sertifikalı CMP değildir**. Bu yüzden:

1. AdSense panelinde **Gizlilik ve mesajlaşma → Avrupa düzenlemeleri** mesajını aç (Google'ın kendi
   ücretsiz CMP'si).
2. Kod tarafında ek bir şey gerekmiyor: `ADSENSE_REQUIRE_CONSENT` varsayılan olarak `true` ve
   ziyaretçi "Kabul Et" demeden AdSense script'i sayfaya basılmaz (`almanyauni_consent` çerezi).

Onay şartını kapatmak istersen (yalnızca AEA dışı bir senaryoda anlamlı):

```
ADSENSE_REQUIRE_CONSENT=false
```

### d) Onay süreci notu

AdSense başvurusu içerik ve trafik ister. Başvurudan önce:
- `/ads.txt` erişilebilir olmalı (yani client ID girilmiş olmalı),
- gizlilik politikası ve çerez politikası sayfaları yayında (var: `/{locale}/cookie-policy`),
- reklam yerleşimleri içeriği boğmamalı.

---

## 3. Üç dilde reklam

Reklam katmanı dil-farkındadır:

- **Affiliate metinleri** `config/ads.php` içinde `text[tr|de|en]` olarak tutulur. Bileşen
  ziyaretçinin diline göre seçer; dil yoksa sırayla `en` → `tr`'ye düşer.
  **Yeni ortak eklerken üç dili de doldur** — yoksa Almanca sayfada Türkçe reklam çıkar.
- **"Reklam Ver" daveti** ve `/advertise` sayfası `__()` anahtarlarıyla çalışır; çeviriler
  `lang/tr.json` ve `lang/de.json` içinde.
- **AdSense** zaten sayfa diline göre reklam seçer; ek ayar gerekmez.

`/advertise` sayfası dil başına kitle tanımını ve yerleşim listesini gösterir; **trafik rakamı
bilinçli olarak sayfaya yazılmaz**, talep üzerine güncel veri paylaşılır (sayfada donmuş sayı
tutmamak için).

---

## 4. Doğrudan reklam satışı (şu an manuel)

Bugün doğrudan satılan bir banner için ayrı bir tablo yok. İki seçenek var:

1. **Affiliate gibi tanımla:** `config/ads.php → affiliates` altına yeni bir ortak ekle
   (üç dilde metin + URL). En hızlısı, kod deploy'u gerektirir.
2. **Kampanya motoru:** `popups` tablosu zaten çok dilli bir kampanya altyapısı içeriyor
   (`title_tr/de/en`, `locales`, `target_pages`, `starts_at/ends_at`, `priority`, gösterim/tıklama
   sayaçları) ama kullanılmıyor. Düzenli banner satışı başlarsa doğru yatırım, aynı deseni
   `ad_banners` tablosuna taşımaktır.

Tıklama takibi için mevcut `/go/{type}/{slug}` altyapısı ve `affiliate_clicks` tablosu (içinde
`locale` kolonu var) kullanılabilir — dil bazında raporlama böyle çıkar.

---

## 5. Kontrol listesi

- [ ] AdSense hesabı onaylandı
- [ ] `ADSENSE_CLIENT_ID` prod env'e girildi
- [ ] En az `banner_top` ve `in_content` slot ID'leri girildi
- [ ] `curl https://applytogerman.com/ads.txt` doğru pub ID'yi dönüyor
- [ ] AdSense panelinde Avrupa onay mesajı (CMP) açıldı
- [ ] Bir blog yazısında reklam göründü (çerez onayı verilmiş bir tarayıcıda)
- [ ] Premium kullanıcıda reklam **görünmüyor**

İlgili: `config/ads.php` · `resources/views/components/ad-slot.blade.php` ·
`resources/views/pages/advertise.blade.php` · `routes/web.php` (`/ads.txt`, `/{locale}/advertise`)

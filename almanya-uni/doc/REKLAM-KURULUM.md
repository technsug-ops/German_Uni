# Reklam Kurulumu (affiliate + doğrudan satış; AdSense ertelendi)

Bu doküman sitedeki reklam katmanının nasıl çalıştığını anlatır.

> **Karar (2026-09-19): AdSense bilinçli olarak ertelendi.** Gerekçe: mevcut trafikte getiri aylık
> birkaç euro seviyesinde kalırken maliyeti yüksek — sayfa hızı, AEA trafiği için sertifikalı CMP
> zorunluluğu ve en önemlisi **kontrol kaybı**: AdSense'te hangi reklamverenin çıkacağını sen
> seçemezsin, "garantili vize" vaat eden bir firmanın reklamı tam da o iddiayı çürüten yazının
> yanında görünebilir. Gelir odağı **affiliate + doğrudan satış**.
>
> Kod tarafı silinmedi: AdSense katmanı `ADSENSE_CLIENT_ID` boşken tamamen uykuda (script basılmaz,
> istek atılmaz, `/ads.txt` 404 döner). Fikir değişirse bölüm 4'teki adımlar yeterli.

---

## 1. Slot önceliği

`resources/views/components/ad-slot.blade.php` tek bileşen üzerinden üç katmanı yönetir:

| Sıra | Katman | Koşul |
|---|---|---|
| 1 | **Affiliate kartı** | `AFFILIATE_*_URL` env'i dolu. Premium üyelerde de gösterilir — içerik-uyumlu sponsor sayılır. |
| 2 | **AdSense** | *(şu an kapalı)* client ID + slot ID dolu **ve** çerez onayı verilmiş **ve** kullanıcı premium değil. |
| 3 | **"Reklam Ver" daveti** | Yukarıdakiler yoksa: `/{locale}/advertise` sayfasına götüren davet kartı. `ADS_HOUSE_ENABLED=false` ile kapatılır. |

Hiçbiri yoksa **hiçbir şey basılmaz** — boş kutu görünmez.

Kullanım:

```blade
<x-ad-slot type="banner" slot="banner_top" />
<x-ad-slot type="affiliate-card" :context="$context" />   {{-- visa | sperrkonto | insurance --}}
```

---

## 2. Affiliate — asıl gelir hattı (yapılacak iş burada)

Ortaklık programına başvur, takip linkini al, prod `.env`'e gir. Link girildiği an kart yayına
girer; onay süreci, hesap açma veya ek kod gerekmez.

```
AFFILIATE_EXPATRIO_URL=
AFFILIATE_FINTIBA_URL=
AFFILIATE_MAWISTA_URL=
AFFILIATE_CARECONCEPT_URL=
```

- Kart, ziyaretçinin dilinde gösterilir; metinler `config/ads.php → affiliates.*.text[tr|de|en]`.
- **Yeni ortak eklerken üç dili de doldur.** Eksik dil `en` → `tr` sırasıyla düşer; boş bırakırsan
  Almanca sayfada Türkçe reklam çıkar.
- Hangi bağlamda hangi ortağın çıkacağı `context_rules` ile belirlenir
  (`visa`, `sperrkonto`, `insurance`, `default`).
- Linkler `rel="sponsored noopener nofollow"` ile basılır ve kartta "Sponsor" etiketi + affiliate
  açıklaması görünür — SEO ve dürüstlük açısından ikisi de şart, kaldırma.

En yüksek dönüşüm beklenen konular: Sperrkonto ve sağlık sigortası (sitenin en çok okunan başlıkları).

---

## 3. Doğrudan satış

- `/{locale}/advertise` — üç dilde medya kiti sayfası: kitle tanımı, yerleşim listesi, kabul edilen
  ve edilmeyen reklam türleri, iletişim.
- Satılmamış her slot bu sayfaya davet kartı basar, yani envanter kendi kendini pazarlar.
- **Trafik rakamı sayfaya yazılmaz** — talep üzerine güncel veri paylaşılır ki sayfada donmuş bir
  sayı kalmasın.
- Yerleşim listesi `config/ads.php → inventory.placements`, iletişim adresi `inventory.contact_email`.

Satılan bir banner bugün iki yoldan verilebilir:

1. **Affiliate gibi tanımla** — `config/ads.php → affiliates` altına üç dilde metin + URL ekle.
   En hızlısı; kod deploy'u gerektirir.
2. **Kampanya motoru** — `popups` tablosu zaten çok dilli kampanya altyapısı içeriyor
   (`title_tr/de/en`, `locales`, `target_pages`, `starts_at/ends_at`, `priority`, gösterim/tıklama
   sayaçları) ama kullanılmıyor. Düzenli banner satışı başlarsa doğru yatırım, aynı deseni bir
   `ad_banners` tablosuna taşımaktır.

Tıklama takibi için `/go/{type}/{slug}` altyapısı ve `affiliate_clicks` tablosu var; içindeki
`locale` kolonu dil bazında raporlamayı mümkün kılıyor.

---

## 4. AdSense — ertelendi, ileride açılmak istenirse

Kod hazır ve uykuda. Açmak için sırayla:

1. AdSense hesabı aç ve onayı tamamla (alan adı, gizlilik + çerez politikası sayfaları ve özgün
   içerik gerekiyor — üçü de mevcut).
2. Panelde reklam birimi oluştur, prod `.env`'e gir:
   ```
   ADSENSE_CLIENT_ID=ca-pub-XXXXXXXXXXXXXXXX
   ADSENSE_SLOT_BANNER_TOP=...
   ADSENSE_SLOT_IN_CONTENT=...
   ```
   Yalnızca client ID girilirse hiçbir slot görünmez; her yerleşim için ayrı slot ID gerekir.
3. `/ads.txt` **otomatik** üretilir (rota `routes/web.php`). Client ID boşken bilinçli olarak 404
   döner — boş/yanlış ads.txt hiç olmamasından zararlıdır. Girildiği an içerik:
   ```
   google.com, pub-XXXXXXXXXXXXXXXX, DIRECT, f08c47fec0942fa0
   ```
4. **AEA/İngiltere için zorunlu:** AdSense panelinde *Gizlilik ve mesajlaşma → Avrupa düzenlemeleri*
   mesajını aç. Google sertifikalı CMP şartıdır; sitenin kendi çerez banner'ı Consent Mode v2
   sinyali gönderir ama sertifikalı CMP değildir. Kod tarafında `ADSENSE_REQUIRE_CONSENT` varsayılan
   `true`: onay verilmeden script basılmaz.

Kontrol listesi: `/ads.txt` doğru pub ID'yi dönüyor · onay verilmiş tarayıcıda reklam görünüyor ·
premium kullanıcıda **görünmüyor**.

---

İlgili dosyalar: `config/ads.php` · `resources/views/components/ad-slot.blade.php` ·
`resources/views/pages/advertise.blade.php` · `routes/web.php` (`/ads.txt`, `/{locale}/advertise`)

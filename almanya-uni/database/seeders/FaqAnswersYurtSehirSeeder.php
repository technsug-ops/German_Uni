<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

/**
 * Curated answers for YURT (16) + SEHIR (13) = 29 questions.
 * Focus: Konaklama, kira, şehir karşılaştırması, lokal hizmetler.
 */
class FaqAnswersYurtSehirSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->answers() as $slug => $md) {
            $faq = Faq::where('slug', $slug)->first();
            if (!$faq) {
                $this->command?->warn("FAQ not found: {$slug}");
                continue;
            }
            $faq->answer_md = trim($md);
            $faq->save();
        }
    }

    private function answers(): array
    {
        return [
            // ============ YURT ============
            'wg-nedir-nasil-aranir' => <<<'MD'
**WG** (Wohngemeinschaft), Almanya'da öğrencilerin en yaygın konaklama biçimi: bir daireyi 2-5 kişiyle paylaşmak. Herkesin kendi odası olur, mutfak/banyo ortak kullanılır.

## Nasıl Aranır

1. **WG-Gesucht.de** — en büyük platform (ücretsiz). Filtre: şehir, bütçe, oda tipi (möbliert/möbliert değil).
2. **Studenten-WG.de** — sadece öğrenci.
3. **eBay Kleinanzeigen** — yerel ilanlar, dolandırıcılık riski yüksek.
4. **Facebook grupları** — "WG Berlin", "WG München" gibi grupları takip et.
5. **Üniversite ilan panoları** — Mensa ve kütüphanelerde fiziksel ilanlar.

## Süreç

- İlan beğen → **Selbstbeschreibung** (kendini tanıtan mesaj) gönder, Almanca/İngilizce uygun.
- WG-Casting denilen tanışma görüşmesine davet ederler (online veya yüz yüze).
- Beğenirlerse sözleşme imzalanır.

## Önemli

⚠️ İlk WG'yi bulmak ortalama **6-8 hafta** sürer. Erkenden başla.
⚠️ Almanca seviyesi A2 altında çok zor — İngilizce konuşan WG'ler için "international" filtresi kullan (Berlin, Münih, Köln'de bol).
⚠️ Vize için adres lazımsa geçici çözüm: [kısa süreli konaklama](/sss/yurt/gecici-1-aylik-konaklama-icin-airbnbyurt-karsilastirmasi).

Detay için [WG dolandırıcılığını ayırt etme](/sss/yurt/wg-ilanlarinda-dolandiricilik-nasil-ayirt-edilir) yazımızı oku.
MD,

            'studierendenwerk-yurt-basvurusu-nasil-yapilir' => <<<'MD'
**Studierendenwerk** (öğrenci işleri), her şehirde devlet destekli yurtları işleten kurumdur. WG'den **%30-50 ucuz** ama bekleme süresi uzun.

## Başvuru Süreci

1. Şehrindeki Studierendenwerk'in web sitesine git (örn. *studierendenwerk-berlin.de*).
2. Online başvuru formunu doldur — genelde aşağıdaki alanlar:
   - Kişisel bilgiler (pasaport/kimlik no)
   - Üniversite ve program (Zulassung yoksa beklenen başvuru bilgisi)
   - Oda tercihi: tek kişilik / WG / aile yurdu
   - İstediğin başlangıç tarihi
3. Bazı şehirler **30-50 €** başvuru ücreti istiyor (Hamburg, Münih).
4. Onay e-postası ile **bekleme listesine** girersin.
5. Sıra geldiğinde teklif (Mietangebot) gelir — 2-3 gün içinde kabul etmen gerekir.

## Bekleme Süreleri (Ortalama)

| Şehir | Lisans | Master |
| --- | --- | --- |
| Berlin | 6-12 ay | 4-8 ay |
| Münih | 12-18 ay | 9-12 ay |
| Köln | 6-9 ay | 4-6 ay |
| Leipzig | 1-3 ay | 1-2 ay |
| Frankfurt | 6-9 ay | 4-6 ay |

## Strateji

- Kabul belgesini alır almaz başvur (yer geldiğinde reddetmek seni listeden silmiyor).
- **Internationale Studierende** kategorisinde öncelik var bazı yurtlarda (özellikle ilk dönem).
- Aynı anda 2-3 farklı yurda başvurabilirsin.

Kira: **220-380 €/ay**, dahil: oda + internet + ısıtma + bazı yurtlarda mutfak ekipmanı.
MD,

            'berlin-munih-hamburg-ortalama-kira-ne-kadar' => <<<'MD'
2026 başı itibarıyla öğrenci konaklaması için ortalama aylık kira (warm = ısıtma + su dahil):

## Berlin

| Tür | Aylık (warm) |
| --- | --- |
| Studierendenwerk yurdu | 240-340 € |
| WG odası (paylaşımlı daire) | 480-650 € |
| 1+1 stüdyo | 850-1,200 € |

## Münih

| Tür | Aylık (warm) |
| --- | --- |
| Studierendenwerk yurdu | 290-380 € |
| WG odası | 650-900 € |
| 1+1 stüdyo | 1,300-1,800 € |

## Hamburg

| Tür | Aylık (warm) |
| --- | --- |
| Studierendenwerk yurdu | 260-360 € |
| WG odası | 520-720 € |
| 1+1 stüdyo | 950-1,400 € |

## Önemli Notlar

- **Kaltmiete** (soğuk kira) = sadece oda; **Warmmiete** = ısıtma + su dahil. **Nebenkosten** ek olarak elektrik+internet, ayrıca **80-120 €**.
- Münih, Almanya'nın en pahalı şehri — bütçen kısıtlıysa Leipzig, Dresden, Halle veya Magdeburg'a bak (%40-50 daha ucuz).
- Kayyumla (Kaution) genelde **2-3 aylık** soğuk kira peşin alınır, çıkışta iade.

İlgili: [En ucuz şehirler](/sss/sehir/almanyada-ogrenci-icin-en-ucuz-sehirler-hangileri) | [Münih yurt bulmak](/sss/yurt/munihte-ogrenci-yurdu-bulmak-ne-kadar-zor).
MD,

            'kaution-genelde-kac-aylik-kiraya-denk-gelir' => <<<'MD'
**Kaution** (depozito), Alman yasası gereği maksimum **3 aylık soğuk kira** (Kaltmiete) olarak alınır — BGB §551.

## Pratik Örnekler

| Oda kirası (kalt) | Kaution (max) |
| --- | --- |
| 400 € | 1,200 € |
| 500 € | 1,500 € |
| 650 € | 1,950 € |

## Ödeme Şekli

- **Tek seferde:** Çoğu ev sahibi tercih eder.
- **3 taksitte:** Yasal hakkın; ev sahibi reddedemez. İlk taksit sözleşme imzasıyla, kalanları sonraki 2 ayda.
- **Mietkautionskonto:** Ayrı bir hesaba yatırılır, çıkışta iade. Sen değil ev sahibi açar.

## Çıkışta İade

- Daire teslim edildikten sonra **3-6 ay içinde** iade edilir.
- Ev sahibi bu süreyi **utility kesintilerini bekledikten sonra** kullanır.
- Hasar/temizlik varsa Kaution'dan düşülür.

## Studierendenwerk Yurdu

Devlet yurtları için Kaution **350-600 € sabit** (kira ne olursa olsun).

## Önemli

⚠️ **Schwarz** (nakit, kayıtsız) Kaution verme — iade garantin kalmaz, vergi denetimi de risk.
⚠️ **Bürgschaft** ile garantörlüğü Kaution yerine kabul eden ev sahipleri var (özellikle WG'de) — [garantör sorusu](/sss/yurt/wohnung-tutarken-garantor-burgschaft-zorunlu-mu).
MD,

            'wg-bulmak-icin-almanca-seviyesi-ne-olmali' => <<<'MD'
Genel kural: **B1** seviyesi pratik konuşmaya yetiyor, ama altında bile WG bulmak mümkün. Hedeflediğin şehre göre değişir.

## Şehir Bazlı Durum

| Şehir | Min seviye | Yorum |
| --- | --- | --- |
| Berlin | A1/A0 | İngilizce konuşan WG çok yaygın |
| Münih | A2/B1 | Almanca dominant, ama uluslararası WG var |
| Hamburg | A2 | Karışık, İngilizce WG'ler bulunabilir |
| Köln/Düsseldorf | B1 | Ortak alanlarda Almanca beklenir |
| Frankfurt | A2 | Finans sektörü → İngilizce yaygın |
| Leipzig/Dresden | B1+ | Almancasız zorlanırsın |
| Stuttgart | B1+ | Çoğunlukla Almanca |

## Pratik İpuçları

- **Selbstbeschreibung** (kendini tanıtan mesaj) İngilizce yaz, kapanışı Almanca dene (basit bir cümle bile sempati yaratır).
- **WG-Casting**'te (tanışma görüşmesi) basit Almanca + İngilizce karışım kabul edilir.
- "Internationale WG" / "English speaking" filtrelerini WG-Gesucht'ta kullan.
- Üniversitenin **Buddy programı** üzerinden Erasmus öğrencileriyle WG bulmak hızlı.

## Almanca Pratiği Eksiklerini Kapatma

A1-A2'yle WG bulduktan sonra ev arkadaşlarınla pratik en hızlı gelişim yöntemi. Mutfakta haftada 1-2 ortak yemek + günlük small talk + bir TV dizisini Almanca izleme rutini = 6 ayda B1.
MD,

            'yurt-basvurusu-icin-deadline-ne-zaman' => <<<'MD'
Studierendenwerk yurtlarının **resmi deadline'ı yok** — başvuru her zaman açık, bekleme listesi sürekli işliyor. Ama strateji için önemli tarihler var.

## Strateji Tarihleri

| Dönem | Ne zaman başvur | Sıra geleceği zaman |
| --- | --- | --- |
| **Wintersemester** (Ekim) | Şubat-Mart | Temmuz-Eylül |
| **Sommersemester** (Nisan) | Eylül-Ekim | Ocak-Mart |

## Öncelikli Tarihler

- **Mayıs sonu:** Yaz başında Erasmus dönüşleri çıkar, oda boşalır.
- **Eylül başı:** Yeni dönem yer dağıtımları tamamlanır (geç başvuranlar geri kalır).
- **Ekim ortası:** Kayıt yaptırmayanların yerleri açılır — son şans.

## Yerleştirme Önceliği

1. **Sosyal kriter** — düşük gelir kanıtı, yetim/refakatsiz
2. **Internationale Studierende** (ilk dönem)
3. **Erstsemester** (yeni başlayan)
4. **Engelli/kronik hastalık**
5. **Yaş** — 27 altı

## Önemli

- Kabul belgesini almadan da **niyet beyanı** ile bekleme listesine girebilirsin (bazı şehirler).
- Aynı anda **2-3 yurdu** önceliklendirebilirsin (Berlin, Bonn, Köln gibi büyük şehirlerde).
- Sıra geldiğinde teklif gelir, **2-3 gün içinde** kabul etmezsen bir sonrakine atlar.

İlgili: [Studierendenwerk başvuru süreci](/sss/yurt/studierendenwerk-yurt-basvurusu-nasil-yapilir).
MD,

            'wg-gesucht-gibi-platformlar-ucretsiz-mi' => <<<'MD'
Çoğu platform **temel arama ücretsiz**, premium üyelikle hızlı ilan görme ve mesajlaşma açılıyor.

## Platform Karşılaştırması

| Platform | Ücretsiz? | Premium |
| --- | --- | --- |
| **WG-Gesucht.de** | Arama + ilan görüntüleme bedava | 19.90 €/ay → öncelikli mesajlaşma |
| **Studenten-WG.de** | Tamamen ücretsiz | Yok |
| **WG-Suche.de** | Bedava | Yok |
| **ImmobilienScout24** | Görüntüleme bedava | 9.90 €/ay → ilan +24 saat erken görme |
| **eBay Kleinanzeigen** | Tamamen bedava | Yok (ilan boost ücretli) |
| **Immowelt** | Görüntüleme bedava | Yok |

## Premium Gerekli mi?

⚠️ **Berlin/Münih için PREMIUM faydalı** — yeni ilanlar 5 dakikada doluyor, premium 1 saat erken erişim verir. Aylık 20€ bütçen varsa değer.

⚠️ **Diğer şehirler için gereksiz** — bedava sürüm yeter.

## Dolandırıcılığa Dikkat

❌ "Anahtarı PayPal'den gönderirim" → **dolandırıcılık.**
❌ Daire görmeden ödeme isteyen → **dolandırıcılık.**
❌ Çok ucuz fiyat, "acil çıkıyorum" hikayesi → **dolandırıcılık.**

Detay: [WG dolandırıcılığı nasıl ayırt edilir](/sss/yurt/wg-ilanlarinda-dolandiricilik-nasil-ayirt-edilir).
MD,

            'wgyurt-sozlesmesi-olmadan-vize-gorusmesine-girilebilir-mi' => <<<'MD'
**Evet, girilebilir** — Almanya konsolosluğu öğrenci vizesi başvurusunda adres beyanı ister ama **bağlayıcı kira sözleşmesi şart değil**. Önemli olan "Almanya'da kalacağın yer var" iddiası.

## Kabul Edilen Belge Türleri

1. **Wohnungsgeberbestätigung** (ev sahibi taahhüdü) — en güçlü.
2. **Vorvertrag** (ön sözleşme) — WG sahibi imzaladıysa.
3. **Studierendenwerk yer onayı** — bekleme listesi onay yazısı.
4. **AirBnB / hostel rezervasyonu** — ilk 1-2 ay için.
5. **Akraba/tanıdık taahhüdü** — Almanya'da yakının varsa adres beyanı.

## Konsoloslukların Pratiği

- **İstanbul/Ankara:** Geçici konaklama belgesi (1-2 aylık AirBnB) kabul ediliyor.
- **Türkiye dışı bazı konsolosluklar:** Bağlayıcı kira sözleşmesi isteyebiliyor.
- **Risk:** Adres belirsiz görünürse memur "ülkeye dönüş niyetin sorgulanır" diye sorabilir — net cevap hazırla.

## Pratik Strateji

1. **İlk 2 ay için** AirBnB/hostel rezervasyonu yap (iptal edilebilir tipte).
2. Vize alıp Almanya'ya geldikten sonra WG ara.
3. WG sözleşmesi imzalanınca **Anmeldung** için Wohnungsgeberbestätigung alınır.

İlgili: [Anmeldung için yurt yeterli mi](/sss/yurt/anmeldung-icin-yurt-adresi-yeterli-mi) | [Geçici konaklama](/sss/yurt/gecici-1-aylik-konaklama-icin-airbnbyurt-karsilastirmasi).
MD,

            'aileler-icin-ogrenci-yurdu-secenegi-var-mi' => <<<'MD'
Evet, **Familienwohnung** olarak adlandırılan aile yurtları var ama sayıları sınırlı, bekleme süresi uzun.

## Aile Yurdu (Familienwohnung) Tipleri

| Tip | Kim için | Oda |
| --- | --- | --- |
| Paar-Wohnung | Çift (evli/partner) | 1+1, mutfak+banyo özel |
| Familienwohnung | Aile (çocuklu) | 2+1 veya 3+1 |
| Alleinerziehende | Tek ebeveyn + çocuk | 1+1 veya 2+1 |

## Başvuru

1. Şehrindeki Studierendenwerk'e başvur — başvuruda "Familienwohnung" kategorisini seç.
2. **Evlilik cüzdanı** ve varsa **çocuğun doğum belgesi** istenir (yeminli tercüme + Apostil).
3. Bekleme listesine girilir.

## Bekleme Süresi

- **Berlin, Münih:** 12-24 ay
- **Köln, Frankfurt:** 6-12 ay
- **Küçük şehirler (Bremen, Magdeburg, Halle):** 1-3 ay

## Maliyet

- **350-700 €/ay** (warm, mobilyalı genelde değil)
- Çocuk başına ek alan/oda olursa kira artıyor

## Alternatif

- **Sosyal konut (Sozialwohnung):** Düşük gelirli aileler için, *Wohnberechtigungsschein* (WBS) belgesi alınması gerekir.
- **Özel kiralık daire:** WG-Gesucht ve ImmobilienScout24'te "Wohnung" filtresi.
- **Studentenwohnheim** + yan oda — eşine ayrı oda alma stratejisi (her zaman izinli değil, yurdun politikasına bak).

⚠️ Eş Türkiye'de kalıyorsa Almanya'ya getirmek için **Aile Birleşimi (Familienzusammenführung)** vizesi gerekir — ayrı süreç.
MD,

            'gecici-1-aylik-konaklama-icin-airbnbyurt-karsilastirmasi' => <<<'MD'
İlk hafta/ay konaklaması için karşılaştırma:

## AirBnB

✅ **Avantajlar:**
- Kısa süreli (1 gece-1 ay) esnek.
- Anmeldung'a izin veren ev sahipleri var (filtre: "Long term").
- Mobilyalı, mutfak kullanımı.

❌ **Dezavantajlar:**
- Pahalı: Berlin 40-80 €/gece, Münih 60-100 €/gece.
- **Anmeldung yasak** ev sahibi izinsiz, vize uzatma sorun.
- 28 günden uzak konaklamada bazı şehirlerde yasal sorun (Berlin Zweckentfremdungsverbot).

## Hostel

✅ **Avantajlar:**
- Çok ucuz: 25-45 €/gece (dorm), 50-80 € (özel oda).
- Sosyal — diğer öğrencilerle tanışırsın.
- Esnek booking.

❌ **Dezavantajlar:**
- Anmeldung yapılmaz.
- Uzun dönemde pratik değil (dolap/eşya alanı yok).

## Studierendenwerk Geçici Yurt

✅ **Avantajlar:**
- Çok ucuz: 250-400 €/ay.
- Anmeldung ofiste yapılır (yurt adresinde).

❌ **Dezavantajlar:**
- Tüm üniversitelerde yok.
- Başvuru süreci uzun (2-4 hafta), garantili değil.

## Öneri Sıralaması

1. **İlk 7-14 gün:** Hostel (ucuz + sosyal).
2. **15-45 gün:** AirBnB long-term ($1,200-2,000 USD).
3. **45+ gün:** WG bulma sürecini bu sırada başlat.

⚠️ **Hostel'de Anmeldung yapamazsın** → vize uzatma için **kalıcı adres** lazım. İlk 2 ay içinde WG bulmaya odaklan.

İlgili: [WG sözleşmesi olmadan vize](/sss/yurt/wgyurt-sozlesmesi-olmadan-vize-gorusmesine-girilebilir-mi).
MD,

            'wohnung-tutarken-garantor-burgschaft-zorunlu-mu' => <<<'MD'
**Hayır, yasal zorunluluk değil** — ama bazı ev sahipleri özellikle yabancı öğrencilerden istiyor. Garantör (Bürge) yerine alternatifler var.

## Bürgschaft Ne Demek?

Garantör, kiracının kira ödememesi durumunda **kalan kira + Kaution'u** ödemeyi taahhüt eder. Bürgschaft sözleşmesi ile resmileşir.

## Kim Bürge Olabilir?

- Almanya'da ikamet eden, **kira tutarının 3 katı** net gelirli kişi.
- Genelde aile bireyleri veya yakın arkadaşlar.
- Türkiye'deki ailen genelde **kabul edilmez** — Almanya'da gelir kanıtı + Schufa istenir.

## Alternatifler (Bürge Yoksa)

| Yöntem | Maliyet | Kabul oranı |
| --- | --- | --- |
| **Mietkautionsversicherung** (sigorta) | Aylık 5-10 € | Yüksek, ev sahibinin onayı şart |
| **Mietkautionsbürgschaft** (banka) | 4-5% Kaution × yıl | Düşük (öğrencide gelir yok) |
| **Bloke hesap kanıtı** | 0 | Bazı ev sahipleri kabul (özellikle WG) |
| **Aileden taahhüt + Schufa** | 0 | Türk aile için zor |
| **Kaution'u peşin ver** | Yok | En kolay yol |

## Bürgschaft İstemeyen Yerler

- **Studierendenwerk yurdu** — sadece Kaution.
- **WG'ler** — Genelde Bürge istemiyor, Vorstellungsgespräch yeterli.
- **Sub-let (Untermiete):** Ana kiracı garantör görevi görür.

## Önemli

⚠️ "Bürge yok" = "Daire yok" demek değil. Doğru ilanı bulmak gerek.
⚠️ Studentenwohnheim, WG ve internatonale Studierende için özel daire kompleksleri (Stayery, The Fizz) Bürgschaft istemiyor.
MD,

            'anmeldung-icin-yurt-adresi-yeterli-mi' => <<<'MD'
**Evet, yurt adresi Anmeldung için tam geçerli** — Studierendenwerk yurtları için ev sahibi yurt yönetimidir, Wohnungsgeberbestätigung'u onlar verir.

## Süreç

1. Yurda taşındıktan sonra **2-3 gün içinde** yurt resepsiyonundan **Wohnungsgeberbestätigung** iste.
2. Bürgeramt'tan **randevu** al (online veya telefon).
3. Randevuda şunları götür:
   - Pasaport + vize
   - Wohnungsgeberbestätigung (yurttan alınan)
   - Anmeldeformular (önceden doldurulmuş)
4. Randevu 15 dakika sürer, **Meldebescheinigung** (kayıt belgesi) hemen verilir.
5. **SteuerID** 2-4 hafta içinde posta ile gelir.

## Yurt Adresi Yeterli Değil mi?

Tek istisna: **Subletting (Untermiete)** durumu — bir öğrenci kendi yurt odasını başkasına devrediyorsa. Bu durumda **Hauptmieter'in** (ana kiracı) yazılı izni gerekir, yurtların çoğu izin vermiyor.

## Önemli

⚠️ **14 gün kuralı:** Almanya'ya geldikten sonra 14 gün içinde Anmeldung yapmazsan **1,000 € kadar para cezası** riski var (uygulamada nadir, ama vize uzatmada sorun).

⚠️ **Geçici yurt** (1-2 haftalık emergency housing) Anmeldung'a uygun değil — yurt sözleşmesinde "Kurzzeitmiete" ibaresi varsa Bürgeramt kabul etmez.

İlgili: [Anmeldung evrakları](/sss/anmeldung/anmeldung-icin-gereken-evraklar-nelerdir) | [Wohnungsgeberbestätigung](/sss/anmeldung/wohnungsgeberbestatigung-nedir-nasil-alinir).
MD,

            'wg-ilanlarinda-dolandiricilik-nasil-ayirt-edilir' => <<<'MD'
WG ve daire ilanlarında dolandırıcılık (Wohnungsbetrug) yaygın — özellikle Berlin/Münih'te. Tipik kırmızı bayraklar:

## Klasik Dolandırıcı Senaryoları

### Senaryo 1: Anahtar Postayla
- İlan sahibi "ülke dışındayım, anahtarı **PayPal güvenli ödeme** ile gönderirim" der.
- Kaution'u ister, sonra ortadan kaybolur.
- ❌ **Asla daireyi görmeden para ödeme.**

### Senaryo 2: AirBnB Klon
- AirBnB'de gerçek ilanın fotoğraflarını kopyalar, WG-Gesucht'a koyar.
- Daha düşük fiyat, "acil" satış.
- ❌ Fotoğrafları Google reverse image search ile kontrol et.

### Senaryo 3: Sahte Kontrat
- Görüşme oluyor, daire gerçek görünüyor.
- Kontrat imzalanıyor, Kaution ödeniyor.
- Anahtar verilmiyor / kontrat sahte / ev sahibi başkası çıkıyor.
- ❌ Ev sahibinin **Grundbuch** (tapu) kaydını veya **kimlik kopyasını** iste.

## Kırmızı Bayraklar Kontrol Listesi

❌ Fiyat piyasanın çok altında (örn. Berlin'de 250 € WG)
❌ Acil satış hikayesi: "Tayin oldum/öldüm/hastayım"
❌ Daireyi görmeyi reddediyor
❌ Ödeme PayPal/Western Union ile isteniyor
❌ Mail/mesaj kötü Almanca veya yabancı dil ağırlıklı
❌ Ev sahibi yurt dışında, "anahtarı vekil verecek"
❌ "İlk olarak Kaution ödersen yer tutarım"

## Yeşil Bayraklar

✅ Daireyi yüz yüze görmek mümkün
✅ Ev sahibi kimliğini gösteriyor
✅ Ödeme **banka havalesi** ile (kayıtlı)
✅ **Mietvertrag** (resmi kira sözleşmesi) imzalanıyor
✅ **Übergabeprotokoll** (devir teslim tutanağı) tutuluyor

## Şüphe Durumunda

- Daire adresi gerçekten var mı? Google Maps'te kontrol.
- Ev sahibinin adı tapu kayıtlarında mı? Şehrin **Grundbuchamt**'ından sorgu.
- WG-Gesucht'un "Verify" rozetli ilanları (mavi tik) önceliklendir.

⚠️ Dolandırıldıysan: **Polizei** (online: *onlinewache.de*) + WG platformuna report.
MD,

            'frankfurt-staj-icin-kisa-sureli-konaklama-nasil-ayarlanir' => <<<'MD'
Frankfurt'ta 1-3 aylık staj/uygulama dönemi için en pratik konaklama seçenekleri:

## 1. The Social Hub (eski The Student Hotel) Frankfurt
- **750-1,400 €/ay**, tam mobilyalı, fitness + ortak çalışma alanı dahil.
- Anmeldung yapılabilir (long-term odalarda).
- Frankfurt-Niederrad'da konum (S-Bahn 15 dk merkez).
- *thesocialhub.co*

## 2. Brera Serviced Apartments / Adina Apartment Hotel
- **900-1,600 €/ay**, kısa süreli (1+ ay) servis dairesi.
- İş seyahatleri için pazarlanıyor, staj öğrencileri için ideal.

## 3. Studentenwohnheim — Boost / Stayery / The Fizz
- **600-900 €/ay**, modern öğrenci konaklaması.
- 3 aylık minimum süre genelde, başvuru 4-6 hafta önceden.

## 4. AirBnB Long Term
- "Long stays" filtre ile **30+ gün** rezervasyon.
- **700-1,400 €/ay** ortalama.
- ⚠️ Anmeldung yapılabilir mi? Listede "Anmeldung möglich" araması yap.

## 5. WG-Gesucht "Zwischenmiete"
- **400-700 €/ay**, başka öğrencinin Erasmus/staj döneminde odasını subletting.
- Anmeldung izni Hauptmieter'in yazılı izniyle.

## Strateji

1. **6-8 hafta önceden** ara — Frankfurt'ta finans staj dönemleri (Eylül-Mayıs) çok yoğun.
2. **Anmeldung gerekli mi?** kontrolünü ilk yap — vize/sigorta için kritik.
3. Bütçen tarsa **The Social Hub** veya **Boost** gibi yerler garantili.
4. **WG Zwischenmiete** en ucuz ama bulması zaman alıcı.

## Ulaşım

Frankfurt küçük şehir — staj yerin Höchst/Eschborn/Niederrad ise oraya yakın oturmak S-Bahn maliyetini düşürür (ay kart 95 €).
MD,

            'munihte-ogrenci-yurdu-bulmak-ne-kadar-zor' => <<<'MD'
Almanya'nın **en zor** yurt pazarı Münih. Talep çok, arz az.

## Sayılar

- Yaklaşık **130,000 öğrenci**, sadece **~12,000 yurt yatağı** (Studierendenwerk + üniversite + özel).
- **Karşılama oranı: %9**. Yani her 10 öğrenciden 9'u özel piyasada çözüm bulmak zorunda.
- Bekleme süresi: **12-18 ay** Studierendenwerk yurdunda.
- WG ortalaması **650-900 €/ay** (Berlin'in %35 üstü).

## Strateji

### 1. Mümkün Olduğunca Erken Başvur
- Kabul belgesini alır almaz **Studierendenwerk München**'e başvur.
- **Aynı anda** üniversitenin (TUM, LMU) kendi yurt başvurusunu da yap (ayrı kurum).
- Stayery, The Fizz, MyRoom24 gibi özel öğrenci yurtlarına da kayıt.

### 2. Sosyal Kriter Avantajı
- Düşük gelir kanıtı + yetim/refakatsiz öğrenci → **öncelik listesinde üst sıra**.
- *Soziale Kriterien* için Studierendenwerk formuna ek belgeler ekle.

### 3. Çevre Şehirler
- **Freising, Garching, Pasing** → S-Bahn ile 20-40 dk Münih merkez.
- Kira %20-30 daha ucuz.
- TUM Garching kampüsü zaten Münih dışında — orada okuyorsan büyük avantaj.

### 4. WG-Gesucht + Premium
- Münih için **Premium üyelik (19.90 €/ay)** gerçekten değer — yeni ilanları 1 saat erken görmek = kabul edilme şansı 3x.

### 5. Geçici Plan
- İlk ay AirBnB / hostel (300-1,200 €).
- Bu süreçte Anmeldung'a izin veren bir yer ara.

## Acil Durum

⚠️ Müracaat tarihi geçti, hala yer yok mu? **Münih Bahnhofsmission** veya **Caritas** geçici yatak sağlıyor. Üniversitenin **International Office** acil konaklama desteği veriyor (1-2 hafta).

İlgili: [Münih ulaşım kart](/sss/yurt/berlin-munih-hamburg-ortalama-kira-ne-kadar) | [Studierendenwerk başvuru](/sss/yurt/studierendenwerk-yurt-basvurusu-nasil-yapilir).
MD,

            'universite-yurdu-basvurusunda-kabul-orani-yuzde-kac' => <<<'MD'
Üniversite/Studierendenwerk yurdu kabul oranları şehre ve döneme göre çok değişir. **Genel ortalama: %15-30** Türkiye'den başvuran öğrenciler için.

## Şehir Bazlı Tahminler

| Şehir | İlk başvuru kabul oranı | Bekleme süresi |
| --- | --- | --- |
| Leipzig, Magdeburg, Halle | %60-80 | 1-3 ay |
| Bremen, Hannover | %40-50 | 3-6 ay |
| Berlin | %20-30 | 6-12 ay |
| Köln, Frankfurt, Stuttgart | %20-30 | 6-12 ay |
| Hamburg | %15-25 | 9-15 ay |
| Heidelberg, Tübingen | %15-25 | 9-15 ay |
| Münih | %9-15 | 12-18 ay |

## Oranı Etkileyen Faktörler

1. **Sosyal kriter** — düşük gelir, yetim, refakatsiz → öncelik (oran %50+).
2. **Erstsemester (1. dönem)** — bazı yurtlar yeni öğrencilere kontenjan ayırır.
3. **Internationale Studierende** programı — uluslararası öğrenciler için ayrı kontenjan (örn. Berlin'de %20).
4. **Başvuru zamanı** — Şubat-Mart başvuru → Ekim'de yer şansı yüksek.
5. **Esneklik** — "her odayı kabul ederim" diyenler önce yerleştirilir.

## Pratik Strateji

- **Hedef şehirlerin tümüne** aynı anda başvur (3-4 şehir).
- **2-3 yurt** içinde önceliklendirme (Erstwunsch, Zweitwunsch, Drittwunsch).
- **Sosyal kriter belgesi** ekle (vakıf yetim belgesi, gelir belgesi vs.).
- Kabul → 2-3 gün içinde **mutlaka cevapla**, geç kalırsan sıra düşer.

## Reddedildiysen

- Bekleme listesinde kal, yıl içinde 3-4 dalga yer açılır.
- Özel öğrenci yurtları (Stayery, The Fizz, MyRoom24) ücretli ama garantili.
- WG'ye dön — Studierendenwerk %70 öğrencinin gerçek seçeneği.

⚠️ **Yurt = WG'den ucuz** ama her şehirde kolay değil. Plana **WG alternatifi** dahil et, sürpriz olmasın.
MD,

            // ============ SEHIR ============
            'almanyada-ogrenci-icin-en-ucuz-sehirler-hangileri' => <<<'MD'
Almanya'da öğrenci hayatı için aylık toplam maliyet (kira + yemek + ulaşım + sigorta) en düşük şehirler:

## Top 10 Ucuz Şehir (2026)

| Sıra | Şehir | Aylık ortalama | WG kira |
| --- | --- | --- | --- |
| 1 | **Magdeburg** | 780-900 € | 280-380 € |
| 2 | **Halle (Saale)** | 800-920 € | 290-400 € |
| 3 | **Chemnitz** | 820-940 € | 300-410 € |
| 4 | **Leipzig** | 850-980 € | 320-450 € |
| 5 | **Cottbus** | 830-950 € | 290-380 € |
| 6 | **Dresden** | 880-1,000 € | 340-480 € |
| 7 | **Bremen** | 920-1,050 € | 380-520 € |
| 8 | **Bochum** | 880-1,000 € | 360-500 € |
| 9 | **Wuppertal** | 870-990 € | 350-490 € |
| 10 | **Bielefeld** | 900-1,020 € | 380-510 € |

## En Pahalı (Karşılaştırma)

- **Münih:** 1,400-1,800 €/ay
- **Frankfurt:** 1,200-1,500 €/ay
- **Hamburg:** 1,100-1,400 €/ay
- **Berlin:** 1,050-1,350 €/ay
- **Stuttgart:** 1,100-1,400 €/ay

## Avantaj-Dezavantaj

✅ **Doğu Almanya (Leipzig, Dresden, Halle):**
- Kültür/öğrenci hayatı canlı
- Kira düşük
- ❌ Türk topluluğu daha küçük
- ❌ Almanca dominant (İngilizce alanlar sınırlı)

✅ **NRW küçük şehirler (Bochum, Wuppertal, Bielefeld):**
- Almanya'nın iş merkezine yakın (Köln/Düsseldorf 30-60 dk)
- ❌ Şehir kendisi sönük olabilir

## Stratejik Karar

Bütçen sıkıysa **Leipzig veya Dresden** ideal — kültür + uygun maliyet + büyük üniversite kombinasyonu.

İlgili: [Berlin-Münih-Hamburg karşılaştırma](/sss/sehir/berlin-munih-hamburg-ogrenci-icin-hangisi-tercih-edilir) | [Doğu Almanya avantajları](/sss/sehir/ogrenci-icin-dogu-almanyanin-avantajlari-leipzig-dresden).
MD,

            'berlin-munih-hamburg-ogrenci-icin-hangisi-tercih-edilir' => <<<'MD'
Üç büyük şehrin öğrenci hayatı açısından kıyaslaması:

## Berlin

✅ **Avantajları:**
- En **uluslararası**, İngilizce her yerde
- Sanat/kültür/club sahnesi Avrupa'nın en zengini
- Kira hala uygun (Münih'in %60'ı)
- Geniş **Türk topluluğu** (Kreuzberg, Neukölln, Wedding)
- Üniversiteler güçlü: HU, TU, FU, Charité

❌ **Dezavantajları:**
- Bürokrasi en yavaş Almanya'da (Anmeldung 8-12 hafta)
- Anmeldung randevusu kabusu — [Berlin alternatif](/sss/anmeldung/berlinde-anmeldung-randevusu-cok-zor-alternatif-var-mi)
- Ulaşım stresli, S-Bahn sorunları sık

## Münih

✅ **Avantajları:**
- TUM ve LMU (Almanya top 2 üni)
- En yüksek maaşlar — staj/Werkstudent için ideal
- Güneye yakın (Alp Dağları, Avusturya)
- Düzenli, temiz, güvenli

❌ **Dezavantajları:**
- **En pahalı** — kira çok yüksek, yurt %9 kabul oranı
- Bavyera muhafazakar, Almanca dominant
- İngilizce alanlar Berlin/Hamburg'a göre az

## Hamburg

✅ **Avantajları:**
- Denizci kültürü, açık fikirli
- Kira Münih'ten ucuz, Berlin'le yakın
- Logistik/mühendislik sektörü güçlü
- TUHH, Universität Hamburg, HafenCity

❌ **Dezavantajları:**
- Hava — yılda 180 yağışlı gün
- Türk topluluğu küçük (Berlin'e göre)
- Şehir geniş, ulaşım pahalı (BVG değil, HVV)

## Hangi Öğrenci İçin?

| Profil | Tercih |
| --- | --- |
| Bütçe sıkı + sosyal sahne istiyor | **Berlin** |
| Mühendislik / iş hayatı odaklı | **Münih** |
| Logistik / denizcilik / hukuk | **Hamburg** |
| Almanca öğrenmek için saf ortam | **Münih > Hamburg > Berlin** |
| İngilizce konfor | **Berlin > Hamburg > Münih** |

İlgili: [Kira karşılaştırma](/sss/yurt/berlin-munih-hamburg-ortalama-kira-ne-kadar).
MD,

            'hamburgda-yuz-yuze-almanca-kursu-onerisi-var-mi' => <<<'MD'
Hamburg'da güvenilir Almanca kursu seçenekleri:

## Premium / Sertifikalı

### 1. Goethe-Institut Hamburg
- Holzdamm 7-9 (merkez)
- A1-C2 tüm seviyeler, **yoğunlaştırılmış (intensiv) 4 hafta** 1,350-1,650 €
- Sertifika **uluslararası geçerli** (Telc, ÖSD eş değer)
- Sınıf mevcudu küçük (10-14 kişi)
- *goethe.de/ins/de/de/sta/ham*

### 2. DeutschAkademie Hamburg
- Mönckebergstraße civarı
- A1-C1, intensiv 4 hafta **990-1,290 €**
- Sertifika Telc/ÖSD'ye paralel
- Esnek başlangıç tarihleri
- *deutschakademie.de*

### 3. Hartnackschule Hamburg
- Eppendorf
- Telc sınav merkezi
- Intensiv 4 hafta **800-1,100 €**
- Vize için sertifika geçerli

## Üniversite Bağlantılı

### 4. Universität Hamburg — Sprachenzentrum
- Kayıtlı öğrenciler için **ücretsiz**
- Dışarıdan kayıt: 200-400 €/dönem
- *uni-hamburg.de/sprachenzentrum*

### 5. Volkshochschule Hamburg (VHS)
- En ucuz: A1-B2 kursu **180-350 €** (40-60 saat)
- Belediye destekli, kalite değişken
- Yavaş tempolu (haftada 2-3 saat)
- *vhs-hamburg.de*

## Online + Hibrit

- **Babbel Live + Goethe online** kombosu — vize için Telc'e Hartnack veya DeutschAkademie'de gir.

## Vize için Hangisi Kabul?

✅ Goethe, DeutschAkademie, Hartnackschule — **tam kabul**
✅ Universität Hamburg Sprachenzentrum — **kabul**
⚠️ VHS — kabul edilir ama Telc/ÖSD sınav sonucu istenir (kurs sertifikası tek başına yetmez)

## Strateji

- **Vize aşamasında:** A1 → Hartnackschule (uygun + sertifikalı)
- **Almanya'ya geldikten sonra B1+:** Universität Hamburg veya Goethe (kaliteli)
- **Sınav hazırlık** (TestDaF, DSH): DeutschAkademie veya özel ders
MD,

            'berlinde-uygun-fiyatli-cevirmen-onerisi' => <<<'MD'
Berlin'de yeminli tercüman (vereidigter Übersetzer) hizmeti — vize/anmeldung/denklik için Türkçe-Almanca çeviri:

## Resmi Liste

✅ **Berlin Adliye Bakanlığı** resmi listesi: *berlin.de/sen/justv/justiz/dolmetscher*
- Filtre: *Sprache: Türkisch*, *Beeidigte Übersetzer*

## Bilinen Uygun Fiyatlı Tercümanlar

⚠️ **Not:** Fiyatlar 2026 başı, doğrulamak için arayın.

### Sayfa Başı 25-40 €
- **Türkisch-Übersetzungsbüro Kreuzberg** — Oranienstraße civarı
- **Çeviri Merkezi Berlin** — Kottbusser Tor yakını
- **Linguafix Berlin** — Online sipariş, Berlin yeminli onayı

### Sayfa Başı 40-65 € (premium)
- Adliye listesinde ön plana çıkanlar
- Apostil dahil paket teklifler

## Standart Sayfa Tanımı

- **1 standart sayfa = 55 satır × 50 karakter** (BDÜ tarifi)
- Diploma transkript = genelde 2-3 sayfa
- Pasaport kopyası = 1 sayfa
- Yeminli onay damgası: **5-15 € ek**

## Pratik İpuçları

✅ **Toplu indirim:** Tüm belgeleri tek seferde verirsen %10-20 indirim mümkün
✅ **Acil tarife:** Normal 5-7 iş günü; acil 2-3 gün → %50-100 zam
✅ **Online sipariş:** PDF + güvenli ödeme, çıktıyı posta ile gönderiyorlar
✅ **Apostil dahil mi?** Sor — bazı tercümanlar Bürgeramt apostili paket yapıyor (15-25 € extra)

## Türkiye'den mi Almanya'da mı?

- **Türkiye'de yeminli tercüme + Apostil:** Sayfa başı 8-15 € (DE'ye göre %40-60 ucuz)
- **DE'de Beeidigte Übersetzer:** Vize/anmeldung için daha güvenli kabul edilir
- **Strateji:** Diploma/sabıka belgesi Türkiye'de hazırla; DE'de gelen belgeleri buradan çevir.

İlgili: [Sayfa başı tercüme maliyeti](/sss/para/sayfa-basi-yeminli-tercume-ortalama-kac-euro) | [Stuttgart tercüman](/sss/sehir/stuttgartta-yeminli-cevirmen-onerisi).
MD,

            'frankfurt-civari-dil-kursu-secenekleri-nelerdir' => <<<'MD'
Frankfurt ve civarında (Offenbach, Wiesbaden, Mainz, Darmstadt) Almanca kursu seçenekleri:

## Frankfurt Merkez

### Goethe-Institut Frankfurt
- Diesterwegplatz 72
- Intensiv 4 hafta **1,350-1,800 €** (Premium)
- *goethe.de/ins/de/de/sta/fra*

### DeutschAkademie Frankfurt
- Kaiserstraße
- Intensiv 4 hafta **990-1,290 €**
- Telc sertifikası sağlar
- *deutschakademie.de*

### inlingua Frankfurt
- Hauptbahnhof yakını
- Intensiv 4 hafta **1,200-1,500 €**
- 1-1 dersler ek

### Sprachschule Aktiv
- Frankfurt + Online seçeneği
- Intensiv 4 hafta **800-1,050 €**
- Daha uygun fiyat

## Frankfurt Yakını (Banliyö)

### Wiesbaden — Volkshochschule Wiesbaden
- 4 hafta intensiv **350-550 €**
- VHS sertifikası — vize için Telc sınavı eklenmeli
- S-Bahn 40 dk Frankfurt

### Mainz — Internationale Sommerkurse (Mainz Uni)
- Yaz dönemi (Temmuz-Eylül) **1,500-2,200 €** (4 hafta yatılı dahil)
- Üniversite kampüsünde

### Darmstadt — TU Darmstadt Sprachenzentrum
- Kayıtlı öğrencilere ücretsiz/düşük fiyat
- Dışarıdan: 300-500 €/dönem

### Offenbach — VHS Offenbach
- 4 hafta intensiv **300-500 €**
- En ucuz seçenek, S-Bahn 10 dk Frankfurt

## Vize/Uni Başvurusu için Sertifika

✅ Goethe-Zertifikat
✅ Telc Deutsch
✅ ÖSD
✅ TestDaF (üni başvurusu için)
✅ DSH (Almanya'da uni sonrası)

⚠️ Sadece "Teilnahmebescheinigung" (katılım belgesi) **vize için yetmez** — sınav sertifikası şart.

## Strateji

- **Hızlı + uygun:** Sprachschule Aktiv veya DeutschAkademie
- **Premium sertifika:** Goethe-Institut
- **Ücretsiz/uni öğrencisi:** TU Darmstadt veya Frankfurt Uni Sprachenzentrum

İlgili: [Hamburg dil kursu](/sss/sehir/hamburgda-yuz-yuze-almanca-kursu-onerisi-var-mi).
MD,

            'bremen-hamburg-hannover-ogrenci-grup-linki-var-mi' => <<<'MD'
Kuzey Almanya'da Türk öğrenci toplulukları/grupları aktif:

## Bremen

- **Türkische Studentenvereinigung Bremen (TSVB)** — Universität Bremen bünyesinde, Instagram: @tsvbremen
- **Türk Topluluğu Bremen** Facebook grubu — 5,000+ üye
- **Bremen Türk Öğrenciler** WhatsApp grupları — Üni International Office aracılığıyla
- Kültür etkinliği: **Bremer Türkenkomitee** geleneksel kahve etkinlikleri

## Hamburg

- **Türk Öğrenciler Hamburg** Facebook grubu — 8,000+ üye, aktif
- **DİTİB Hamburg Gençlik** — sosyal etkinlik, dil değişimi
- **Hamburg Türk Toplumu (HTT)** — kültürel etkinlikler
- **Türkische Studentenvereinigung Hamburg** — Instagram: @tsvhh
- **Hamburg Türk Öğrenci Birliği (TÖB)** — bilgilendirme + danışmanlık

## Hannover

- **Türkische Studentengemeinde Hannover (TSGH)** — Leibniz Uni + TU Hannover
- **Hannover Türk Öğrenciler** Facebook + WhatsApp grupları
- **DİTİB Hannover** öğrenci komisyonu

## Genel Kuzey Almanya

- **AlmanyaUni Türk Öğrenciler Topluluğu** — Telegram grubu (kayıt için sitemizin alt kısmında link var)
- **Bizim Hannover/Hamburg** Instagram sayfaları — yerel haberler + duyurular

## Üniversiteyi Sorduğunda

✅ Her büyük üniversitenin **International Office**'i Türk öğrenci buddies sistemine sahip — kayıt sırasında "Türk buddy istiyorum" demek yeterli.

## Pratik Tavsiye

- İlk hafta üni oryantasyonunda **Türk öğrenci masaları** olur, oradan grupları al.
- Camileri (DİTİB, IGMG, ATIB) sosyal sorularda kullanma (genelde 18-40 yaş aralığı buluşma alanı).
- Üniversitenin Erasmus komisyonu Türk öğrenciler için **kültürel aktarım** programları yapar.

⚠️ Facebook/WhatsApp gruplarına dolandırıcı sızabiliyor (özellikle "kiralık daire" mesajları). Admin'lerden onay almadan ödeme yapma.
MD,

            'freiburgda-kisa-sureli-yurt-veya-daire' => <<<'MD'
Freiburg'da kısa süreli (1-3 ay) konaklama seçenekleri:

## Üniversite Bağlantılı

### Studierendenwerk Freiburg
- **300-450 €/ay**, mobilyalı yurt odaları
- **Sommer/Winterschool katılımcıları** için kısa süreli kontenjanlar açıyor
- Başvuru: *studierendenwerk-freiburg.de*

### Albert-Ludwigs-Universität Guest House
- Yurt dışı misafir öğrenci/araştırmacı için
- **450-650 €/ay**, 1+ ay rezervasyon
- Kayıtlı öğrenciler için indirimli

## Özel Öğrenci Konaklaması

### The Fizz Freiburg
- Vauban bölgesi
- **750-1,100 €/ay**, tam mobilyalı, modern
- 3+ ay minimum genelde, kısa kontrat müsait dönemler var
- *the-fizz.com*

### Stayery Freiburg (planlanan)
- Açılış 2026/2027 tahmini

## AirBnB / Apartments

- **Freiburg merkez:** 60-90 €/gece, aylık 1,400-2,200 € (1+1)
- **Vauban / Stühlinger:** %20-30 ucuz
- "Long term" filtreli rezervasyon → indirim

## WG-Gesucht Zwischenmiete

- **400-650 €/ay**, başka öğrencinin tatil/Erasmus döneminde odası
- En ekonomik ama esnek değil — boş günler için sözleşme

## Strateji

| Süre | Öneri |
| --- | --- |
| 1-7 gün | Hostel (Black Forest Hostel — 30-50 €/gece) |
| 1 ay | AirBnB + Anmeldung möglich filtre |
| 1-3 ay | The Fizz veya WG Zwischenmiete |
| 3+ ay | Studierendenwerk başvur + WG ara |

## Freiburg Özel Durumlar

⚠️ **Yaz festivalleri** (Haziran-Ağustos) konaklama fiyatlarını %50 kadar artırır — Freiburg küçük ama turistik.
⚠️ **Vauban** bölgesi car-free, bisikletle/tramvayla hareket. Bütçen sıkıysa bisiklet al (50-150 € ikinci el).

## Önemli

- Anmeldung süresi 14 gün → kısa konaklamada bile kayıt yaptır.
- Studierendenwerk'in kısa süreli oda ilanları: *studierendenwerk-freiburg.de/wohnen/zwischenmiete*
MD,

            'stuttgartta-yeminli-cevirmen-onerisi' => <<<'MD'
Stuttgart ve çevresinde (Esslingen, Ludwigsburg, Sindelfingen) yeminli tercüman (öffentlich bestellter und beeidigter Übersetzer) bulma:

## Resmi Liste

**Justiz Baden-Württemberg:** *justiz-bw.de/dolmetscherübersetzer*
- Türkçe-Almanca filtre, Stuttgart bölgesi seç.

## Stuttgart Merkez Tercümanlar

### Sayfa Başı 25-40 €
- **Türkisch Übersetzer Stuttgart (Bad Cannstatt civarı)**
- **Çeviri Bürosu Feuerbach**
- **Translingo Stuttgart** — online sipariş, posta ile teslim

### Sayfa Başı 40-60 € (Premium)
- Adliye listesinde Stuttgart-Mitte/Stadtmitte yeminli tercümanlar
- Apostil + yeminli ortak paketler

## Pratik Süreç

1. **Belgeleri tara** (300 dpi PDF) ve birkaç tercümana fiyat teklifi al.
2. **Tahmini fiyat** sayfa sayısı × birim fiyat + yeminli mühür ücreti (5-15 €).
3. PDF/orijinal posta ile gönder, **2-5 iş günü** içinde dönüş.
4. Yeminli çeviri **damgalı + imzalı** orijinal halini posta ile alırsın.

## Yakın Şehirler (Daha Uygun)

- **Heilbronn:** Sayfa başı 20-35 €
- **Reutlingen:** 25-40 €
- **Karlsruhe:** 25-40 €

⚠️ Yeminli onay tüm Almanya'da geçerli — Stuttgart'taki adliyeye Karlsruhe yeminli tercümanın çevirisi sunulur, kabul edilir.

## Türkiye'den Çeviri vs DE

| Yer | Sayfa başı | Apostil dahil mi |
| --- | --- | --- |
| Türkiye yeminli + Apostil | 10-20 € | Apostil ayrı (250-500 TL) |
| Stuttgart yeminli | 25-45 € | Apostil ayrı yapılır |

Türkiye'de hazırlanmış yeminli çeviri + Apostil **Almanya'da kabul edilir** (Lahey Sözleşmesi). Yine de bazı kurumlar (özellikle Stuttgart Standesamt) **lokal yeminli** isteyebilir — başvurudan önce sor.

## Çeviri Sonrası

✅ **Apostil** Türkiye'de Kaymakamlık veya Valilik'ten alınır
✅ **Almanya'da Apostil:** Land düzeyinde, Stuttgart için Regierungspräsidium Stuttgart

İlgili: [Berlin'de tercüman](/sss/sehir/berlinde-uygun-fiyatli-cevirmen-onerisi) | [Yeminli tercüme fiyat](/sss/para/sayfa-basi-yeminli-tercume-ortalama-kac-euro).
MD,

            'berlinde-denklik-icin-evrak-listesi-nelerdir' => <<<'MD'
Berlin'de **Senatsverwaltung für Bildung, Jugend und Familie** lise denklik (Hochschulzugangsberechtigung) işlemlerini yürütüyor.

## Lise Diploması Denkliği — İstenen Evraklar

1. **Diploma + transkript** (yeminli tercüme + Apostil ile)
2. **Pasaport kopyası**
3. **Anmeldung belgesi** (Meldebescheinigung)
4. **Başvuru formu** — *anabin.kmk.org* üzerinden çıktı alınır
5. **75 € başvuru ücreti**
6. (Varsa) **YKS puan kartı** + tercüme
7. (Varsa) **Üniversite kabul yazısı** (önceliklendirme için)

## Süreç

1. *anabin.kmk.org* sitesine git → kendi üniversiteni/lisenı ara.
   - **H+** durumdaysan denklik direkt verilir.
   - **H+/-** veya bilinmiyor → manuel inceleme (8-16 hafta).
2. **Senatsverwaltung'a postayla** veya **online (ServicePortal)** başvur.
3. Karar bekleme: **8-20 hafta**.
4. Karar olumluysa **Hochschulzugangsberechtigung (HZB)** belgesi gelir.
5. Bu belgeyle üniversite başvurusu yapılır.

## Üniversite Diploması (Bachelor/Master) Denkliği

- **Berlin'de ayrı kurum: ZAB (Zentralstelle für ausländisches Bildungswesen)**
- Başvuru: *kmk.org/zab/zeugnisbewertung*
- Ücret: **150-200 €**
- Bekleme: **10-16 hafta**
- Belge adı: **Zeugnisbewertung**

## Önemli

⚠️ **Anabin'de üniversiten H+ değilse** — diplomanın denkliği özel olarak değerlendirilir, ek belge istenebilir (ders içerikleri, AKTS).
⚠️ **Studienkolleg'e gidiyorsan** denkliği STK kabul ettiğinde almak gerekmez (Feststellungsprüfung sonrası denklik otomatik gelir).
⚠️ **Anmeldung olmadan başvuru yapamazsın** — Berlin'de oturum kaydı şart.

İlgili: [Lise diploması geçerli mi](/sss/denklik/turk-lise-diplomasinin-almanyada-gecerliligi-var-mi) | [Anabin nedir](/sss/denklik/anabin-nedir-universiteler-nasil-listeleniyor).
MD,

            'berlinin-nrwye-gore-ogrenci-avantajlari' => <<<'MD'
Berlin ve NRW (Nordrhein-Westfalen) Türk öğrenciler için Almanya'nın en popüler iki bölgesi. Avantajları farklı:

## Berlin'in Avantajları

✅ **Tek mega şehir:** Tek bir altyapı, kira/öğrenci hayatı koordineli.
✅ **En büyük Türk topluluğu:** Kreuzberg, Neukölln, Wedding — Türkçe yaşam mümkün.
✅ **Uluslararası:** İngilizce her yerde, sığınma/iş izni için sosyal destek geniş.
✅ **Kira NRW'den ucuz değil ama benzer:** Berlin'de WG 480-650 €, Köln'de 480-650 €.
✅ **Üniversite çeşitliliği:** HU, TU, FU, Charité, ESMT, Hertie School.
✅ **Kültür/sanat sahnesi:** Avrupa'nın #1 club + müzik + sanat şehri.
✅ **Tek eyalet vize işlemi:** Anmeldung, oturum tek noktada.

## NRW'nin Avantajları

✅ **17 milyon nüfus, 14 büyük üniversite şehri:** Köln, Düsseldorf, Bonn, Essen, Dortmund, Bochum, Münster, Aachen, Duisburg, Wuppertal, Bielefeld, Paderborn, Siegen, Hagen.
✅ **İş pazarı en büyük:** Almanya'nın endüstri kalbi (Bayer, Henkel, Thyssen-Krupp, Bertelsmann).
✅ **Şehirler arası mobilite:** Köln-Düsseldorf-Bonn üçgeni 30 dk ile birbirine bağlı; bir şehirde okuyup diğerinde staj yap.
✅ **Türk topluluğu büyük + dağılmış:** Köln, Duisburg, Essen, Dortmund — milyonu aşan Türk nüfusu.
✅ **Kira Berlin'le yakın ama küçük şehirlerde çok düşük:** Bochum/Wuppertal WG 350-500 €.
✅ **AlmanyaUni içerikleri NRW dominant:** Mühendislik (RWTH Aachen), tıp (Bonn), iş (WHU, Düsseldorf) çok güçlü.

## Hangi Profilde Hangisi?

| Senin durumun | Tavsiye |
| --- | --- |
| Yaratıcı sanat/medya | **Berlin** |
| Mühendislik (özellikle makine, otomotiv) | **NRW (RWTH Aachen, TU Dortmund)** |
| Tıp | **NRW (Bonn, Köln) > Berlin (Charité)** |
| İş / Finans | **NRW (WHU, Mannheim Business) > Berlin** |
| Sosyal bilim/kültür | **Berlin** |
| Türk topluluğu önemli | **NRW > Berlin** (toplam nüfus) |
| Tek mega şehir hayatı | **Berlin** |

## Maliyet Karşılaştırması

| Aylık (öğrenci) | Berlin | Köln | Bochum (NRW) |
| --- | --- | --- | --- |
| Kira (WG) | 520 € | 510 € | 380 € |
| Yemek | 220 € | 220 € | 180 € |
| Ulaşım | 65 € | 60 € (D-Ticket) | 60 € |
| Sigorta | 130 € | 130 € | 130 € |
| **Toplam** | ~1,050 € | ~1,030 € | ~830 € |

İlgili: [En ucuz şehirler](/sss/sehir/almanyada-ogrenci-icin-en-ucuz-sehirler-hangileri).
MD,

            'hangi-eyalette-ogrenim-ucreti-var-baden-wurttemberg-ab-disi' => <<<'MD'
Almanya'da **2026 itibarıyla** öğrenim ücreti (Studiengebühren) sadece **Baden-Württemberg** eyaletinde, **AB dışı** öğrencilere uygulanıyor.

## Baden-Württemberg Öğrenim Ücreti

- **AB dışı öğrenciler:** Dönem başı **1,500 €** (yılda **3,000 €**)
- **İkinci diploma alanlar:** Dönem başı **650 €**
- **AB öğrencileri:** Yok
- **Almanya'da mülteci statüsü:** Yok
- **Baden-Württemberg lise mezunu:** İlk diplomada yok

## Kapsama Giren Üniversiteler

- Universität Stuttgart, Heidelberg, Tübingen, Freiburg, Karlsruhe (KIT), Konstanz, Hohenheim, Mannheim, Ulm
- Pforzheim, Reutlingen, Esslingen, Aalen, Furtwangen, Albstadt-Sigmaringen + diğer Hochschule'ler

## Muafiyetler

- **DAAD bursu** sahipleri (genelde Baden-Württemberg'in ödediği muafiyet)
- **Mülteci/asylum** statüsündekiler
- Çift vatandaşlık (AB ikinci pasaportu varsa)
- Bazı **stipendium** alanlar (kuruma göre)

## Diğer Eyaletler

✅ **Öğrenim ücreti YOK:**
- Berlin, Brandenburg, Bayern, Hessen, Niedersachsen, Sachsen, Sachsen-Anhalt, Thüringen, NRW, Rheinland-Pfalz, Hamburg, Bremen, Schleswig-Holstein, Mecklenburg-Vorpommern, Saarland

⚠️ **Tüm eyaletlerde Semesterbeitrag (yarıyıl katkısı) var:** 150-450 €/dönem
- Öğrenci kartı + ulaşım (Semesterticket veya D-Ticket) + sosyal hizmet
- Bu **ücret değil**, hizmet karşılığı zorunlu katkı

## Master/PhD

- Master genelde **ücretsiz** (Baden-Württemberg dahil bazı master programlarında ücret var — kontrol et!)
- "Weiterbildender Master" (devam eden eğitim master) ücretli olabilir: 3,000-25,000 € (özel/MBA)
- PhD genelde ücretsiz, bazı **graduate school**'lar yıllık 500-2,000 € katkı

## Strateji

- Bütçen sıkıysa **Baden-Württemberg dışı** eyaletleri öncelikle değerlendir.
- Ya da Baden-Württemberg'de **DAAD veya KAAD bursu** ile öğrenim ücreti muafiyeti.
- Master için Baden-Württemberg'de **ücret genelde lisansla aynı** — kontrol şart.

İlgili: [DAAD bursu nasıl alınır](/sss/burs/daad-bursu-nasil-alinir-basvuru-sureci-nedir).
MD,

            'karlsruhe-veya-freiburg-ogrenci-hayati-nasil' => <<<'MD'
Baden-Württemberg'in iki popüler öğrenci şehri — küçük ama akademik olarak güçlü:

## Karlsruhe

**Nüfus:** ~310,000 | **Üni:** KIT (Karlsruhe Institute of Technology — Almanya top mühendislik)

✅ **Avantajlar:**
- KIT, **Excellence University** statüsünde — bilgisayar bilimi, makine müh, ekonomi top 3.
- **Bisiklet şehri** — düz arazi, neredeyse her yere 15 dk.
- Kira makul: WG 380-520 €.
- Fransa sınırı (Strasbourg) 1 saat — Avrupa gezme avantajı.
- Düzenli + güvenli.

❌ **Dezavantajlar:**
- Şehir **sönük gibi gelebilir** — Berlin/Münih hareketi yok.
- Sınırlı çeşitlilik (kafe, kültür, kulüp az).
- Yaz çok sıcak (Rhein vadisi).

## Freiburg im Breisgau

**Nüfus:** ~230,000 | **Üni:** Albert-Ludwigs-Universität Freiburg (Almanya'nın eski/saygın üniversitelerinden)

✅ **Avantajlar:**
- Almanya'nın **en güneşli şehri** — yılda 1,800 saat güneş.
- Schwarzwald (Kara Orman) kapıda, doğa kültürü güçlü.
- Bisiklet/yürüyüş şehri, araba neredeyse gereksiz.
- Kira makul: WG 450-600 € (Karlsruhe'den biraz pahalı, turistik).
- Güçlü tıp + ekoloji + felsefe bölümleri.
- Çevre dostu kültür (Vauban district dünya çapında ünlü).

❌ **Dezavantajlar:**
- Şehir küçük, **uluslararası mutfak/sahne sınırlı**.
- Almanca dominant — İngilizce konuşan ortamlar az.
- Yaz turist sezonu — fiyatlar artar.

## Karşılaştırma

| Kriter | Karlsruhe | Freiburg |
| --- | --- | --- |
| Üniversite gücü (mühendislik) | KIT > | Freiburg küçük müh. |
| Üniversite gücü (tıp/sosyal) | Az | Freiburg çok güçlü |
| Kira | 380-520 € | 450-600 € |
| İklim | Sıcak yaz | En güneşli |
| Yaşam tempo | Sakin akademik | Eko-bohem |
| Öğrenci sahnesi | Orta | Aktif (küçük ama yoğun) |
| Türk topluluğu | Orta (Karlsruhe Türk nüfusu 25K+) | Küçük |

## Hangisi Senin İçin?

- **Mühendislik/IT öğrencisi + sakin tempo** → **Karlsruhe (KIT)**
- **Tıp/biyoloji/çevre + doğa hayranı** → **Freiburg**
- **Almanca öğrenmek için saf ortam** → İkisi de uygun
- **Berlin/Münih cazibesi istiyorsan** → İkisi de küçük gelir

⚠️ **Baden-Württemberg = AB dışı öğrencilere 1,500 €/dönem öğrenim ücreti.** [Detay](/sss/sehir/hangi-eyalette-ogrenim-ucreti-var-baden-wurttemberg-ab-disi).
MD,

            'ogrenci-icin-dogu-almanyanin-avantajlari-leipzig-dresden' => <<<'MD'
Doğu Almanya (Berlin hariç) öğrenciler için **çok ucuz + akademik kalite yüksek** kombinasyonu sunar. Leipzig ve Dresden başı çekiyor.

## Leipzig

**Nüfus:** ~615,000 | **Üni:** Universität Leipzig (Avrupa'nın en eski üniversitelerinden, 1409)

✅ **Avantajlar:**
- **"Hipsterlerin Berlin'i"** — sanat, müzik, yaratıcı sahne güçlü.
- WG kira **320-450 €** (Berlin'in %35'i altı).
- Kahve/restoran/kulüp çeşitliliği büyük şehirlerle yarışır.
- Üni güçlü: tıp, müzik (Mendelssohn), iletişim, kimya.
- Treni 1 saatte Berlin'e gider.
- Yurt **Studierendenwerk** kabul oranı %60+ (1-3 ay bekleme).

❌ **Dezavantajlar:**
- AfD (sağ popülist parti) güçlü → izole vakalar (özellikle banliyö).
- Almanca dominant — İngilizce ortamlar Connewitz/Plagwitz dışında az.
- Türk topluluğu küçük (Berlin/NRW kıyaslandığında).

## Dresden

**Nüfus:** ~565,000 | **Üni:** TU Dresden (Excellence University, mühendislik top 5)

✅ **Avantajlar:**
- TU Dresden mühendislik + IT için **Almanya top 5**.
- Şehir merkezi turistik-güzel ("Florence on the Elbe").
- WG kira **340-480 €**.
- Çek Cumhuriyeti sınırı kapıda — Prag 2 saat.
- Müzik/sanat tarihi zengin (Semperoper, Zwinger).

❌ **Dezavantajlar:**
- Pegida hareketi başlangıcı Dresden — siyasi ortam zaman zaman gergin.
- Şehir Leipzig'den daha "klasik", genç sahne az.
- İngilizce alanlar TU Dresden + Neustadt'la sınırlı.

## Diğer Doğu Şehirleri

- **Halle (Saale):** Çok ucuz (WG 290-400 €), Martin-Luther-Universität güçlü tıp.
- **Magdeburg:** En ucuz (WG 280-380 €), Otto-von-Guericke teknik üni iyi.
- **Chemnitz:** Mühendislik fokus, çok düşük yaşam maliyeti.
- **Jena:** Optik/biyoloji top, küçük öğrenci şehri.
- **Cottbus:** BTU Cottbus, ucuz + sakin.

## Ortak Doğu Almanya Avantajları

✅ **Maaliyet:** Bavyera/NRW'nin **%50-60'ı** kadar
✅ **Yurt bulma kolay** — kabul oranı %60-80
✅ **Akademik kalite Excellence Universities seviyesinde** (TU Dresden, Uni Leipzig, FSU Jena)
✅ **Çek/Polonya yakın** — düşük maliyetli Avrupa gezisi
✅ **Almanca öğrenmek için saf ortam**

## Ortak Dezavantajlar

❌ Türk/uluslararası topluluk **küçük**
❌ Bazı bölgelerde **AfD/Pegida ortamı** rahatsız edici olabilir
❌ Almanca **şart** — İngilizce konfor sınırlı

## Tavsiye

- **Bütçen sıkı + akademik fokus istiyorsan → Doğu Almanya en akıllı tercih.**
- **Leipzig** sanat/sosyal/iletişim + güzel öğrenci hayatı için.
- **Dresden** mühendislik/IT + tarihi şehir için.
- **Halle/Magdeburg** en ucuz, tıp/teknik için ideal.

İlgili: [En ucuz şehirler](/sss/sehir/almanyada-ogrenci-icin-en-ucuz-sehirler-hangileri).
MD,
        ];
    }
}

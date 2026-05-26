<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

/**
 * Curated answers for the highest-demand topics: VİZE (1,537) + DİL (1,451) = ~57% of community demand.
 * All 35 questions answered with student-focused, 2026-current information.
 */
class FaqAnswersVizeDilSeeder extends Seeder
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
            'almanya-ogrenci-vizesi-nasil-alinir' => <<<'MD'
Almanya öğrenci vizesi (ulusal vize, kategori D — §16b) almak istiyorsan süreç aşağıdaki sırayla işliyor.

## Adım Adım Süreç

1. **Üniversite kabulü al** — Uni-Assist veya doğrudan üniversite başvurusu, ya da Studienkolleg kabulü.
2. **Sperrkonto (bloke hesap) açtır** — 2026 yılı için yıllık tutar **11,904 €** (aylık 992 €).
3. **Sağlık sigortası yaptır** — Vize için minimum 30,000 € teminat veya Alman yasal sigorta taahhüdü.
4. **Konsolosluk randevusu al** — IDATA üzerinden (Ankara/İstanbul/İzmir/Antalya).
5. **Evraklarla başvur** — Form, fotoğraf, pasaport, kabul belgesi, finansman kanıtı, sigorta, dil sertifikası.

## Önemli Süreler

- Randevu bekleme: **6-12 hafta** (yoğun dönemde 4-6 ay)
- Karar bekleme: Başvurudan sonra **6-12 hafta**
- Toplam (kabul belgesi → vize): ortalama **4-6 ay**

## Vize Türleri

| Tür | Ne için | §  |
| --- | --- | --- |
| Öğrenim vizesi | Lisans/master/PhD | §16b |
| Studienkolleg vizesi | Hazırlık | §16b (2. paragraf) |
| Dil kursu vizesi | Sadece Almanca kurs | §16f |
| Üni başvuru vizesi | DE'de yer aramak | §17 |

İlk iki yıl için verilir, üniversite kaydı süresince uzatılabilir. Detay için [vize başvurusunda istenen evraklar](/sss/vize/vize-basvurusunda-istenen-evraklar-nelerdir) yazımıza bak.
MD,

            'dil-kursu-vizesi-basvurusu-nasil-yapilir' => <<<'MD'
Dil kursu vizesi (§16f), Almanya'da yoğunlaştırılmış (haftalık 18+ saat) Almanca kursuna katılmak için verilir. **Üniversite okumak için yetmez** — sadece dil öğrenme amacı tanır.

## Şartlar

- En az **6 ay** kayıtlı, **haftalık 18+ saat** intensif kurs
- Genellikle **A1 veya A2** seviyesi yeterli (kurs alacaksın zaten)
- **Sperrkonto** — kurs süresi ay sayısı × 992 € (örn. 6 ay = 5,952 €)
- **Sağlık sigortası** (kurs süresi boyunca)
- Kurs ücretinin **ödendiğine dair makbuz**

## Süreç

1. Sertifikalı bir dil okuluna **6 ay+ kayıt yaptır** (ödeme dahil)
2. Sperrkonto açtır
3. Sigortayı ayarla
4. IDATA randevusu al
5. Başvur

## Önemli Riskler

⚠️ **Konsolosluk son dönemde dil kursu vizesini sıkılaştırdı.** Niyetin gerçekten dil öğrenmek olmalı, "uni öğrencisi olmak için kısayol" niyeti yakalanıyor — özellikle "üniversite başvurusu yaptım, ona kadar kurs alacağım" diyenler reddedilebiliyor.

✅ **Önerilen alternatif:** Eğer master/lisans hedefin varsa, doğrudan üniversite kabulü alıp **§16b** ile başvur. Sertifika eksikliğine göre Studienkolleg veya 1 dönemlik üniversite içi Almanca kursu daha güvenli yol.

Bağlantılı: [Üniversite kabul belgesi vize için yeterli mi?](/sss/vize/universite-kabul-belgesi-vize-basvurusu-icin-yeterli-mi)
MD,

            'universite-kabul-belgesi-vize-basvurusu-icin-yeterli-mi' => <<<'MD'
**Kısa cevap: Hayır, tek başına yetmez.** Kabul belgesi (Zulassung) zorunlu evraklar listesinde ilk sırada ama yanına başka belgeler de gerekiyor.

## Kabul Belgesi + Şunlar Şart

| Belge | Açıklama |
| --- | --- |
| ✅ Kabul belgesi | Üniversite veya Studienkolleg'den |
| ✅ Sperrkonto | 2026: **11,904 €/yıl** |
| ✅ Sağlık sigortası | DE yasal sigorta veya min 30K € teminatlı seyahat sigortası |
| ✅ Dil sertifikası | Çoğu programda zorunlu (TestDaF/DSH/IELTS) |
| ✅ Lise+lisans diploması | Apostilli + yeminli çeviri |
| ✅ Transcript | Apostilli + yeminli çeviri |
| ✅ Motivasyon mektubu | Programa neden başvurduğunu açıkla |
| ✅ Biyometrik fotoğraf | Son 6 ay içinde |
| ✅ Pasaport | Geçerlilik süresi vize bitiminden 3 ay+ sonrası olmalı |

## Şartlı Kabul (Bedingte Zulassung) Durumu

Eğer dil yeterliliği henüz tam değilse, **şartlı kabul** mektubu alabilirsin. Bu durumda Almanya'da **dil kursu + Studienkolleg veya direkt programa hazırlık** süresi için vize verilir. Şartlı kabul de §16b altında değerlendirilir, dil kursu vizesinden ayrıdır.

## Pratik Tavsiye

Tüm evrakları **2 kopya** halinde topla — biri orijinal/apostilli, biri konsolosluk için. Eksik evrakla başvurunca randevuda evrak kabul edilmiyor.

Bağlantılı: [Vize için istenen evraklar](/sss/vize/vize-basvurusunda-istenen-evraklar-nelerdir)
MD,

            'ulusal-vize-basvurusunda-dil-sertifikasi-zorunlu-mu' => <<<'MD'
**Çoğu durumda evet, ama hangi sertifika gerektiği başvuru amacına göre değişir.**

## Senaryoya Göre Dil Şartı

| Başvuru türü | Gereken sertifika |
| --- | --- |
| **Almanca lisans programı** | TestDaF (her bölüm 4+), DSH-2, telc C1 Hochschule veya Goethe C2 |
| **Almanca master programı** | TestDaF 4, DSH-2 veya programa göre B2 (bazı sosyal bilimler) |
| **İngilizce master** | IELTS 6.5 / TOEFL iBT 90+ ve **bazılarında ek olarak A1-B1 Almanca** |
| **Studienkolleg vizesi** | B1 minimum (Goethe/Telc/ÖSD), bazı eyaletler B2 ister |
| **Dil kursu vizesi (§16f)** | A1 yeterli (zaten kurs alacaksın) |
| **Üni başvuru vizesi (§17)** | Genelde **B1-B2** gerekli — programa hazır olduğun kanıtı |

## Önemli Kurallar

- Sertifika **son 2 yıl içinde** alınmış olmalı (Goethe ömür boyu geçerli ama konsolosluk genelde fresh tercih ediyor)
- **TestDaF + DSH** Almanya'da en yaygın kabul edilen iki sınav
- **IELTS Academic** zorunlu (General Training kabul edilmiyor)

## Şartlı Kabul Varsa

Eğer üniversite **şartlı kabul (Zulassung unter Vorbehalt)** verdiyse, kabul belgesinde "öğrenci geldikten sonra X seviyeye yükselmeli" yazar. Bu durumda vize başvurusunda gösterdiğin sertifika programa giriş için yetersiz olabilir ama vize verilir — sen DE'de eksikliği tamamlarsın.

Detay: [TestDaF vs DSH karşılaştırması](/sss/dil/testdaf-ile-dsh-arasindaki-fark-nedir)
MD,

            'vize-basvurusu-sonrasi-olumluolumsuz-donus-ortalama-kac-gun-surer' => <<<'MD'
Başvurudan sonra karar süresi **ortalama 6-12 hafta**, ama yoğun dönemde daha uzun sürebilir.

## Süreçler

| Aşama | Tipik süre |
| --- | --- |
| Randevu alma | 6-12 hafta (yaz: 4-6 ay) |
| Evrak teslimi → Almanya'ya gönderim | Aynı gün - 1 hafta |
| Almanya'da Ausländerbehörde inceleme | **4-10 hafta** |
| Sonuç → konsolosluk | 1 hafta |
| Pasaport çağrısı (vize basımı) | 1-2 gün |

## Hızlandıran Faktörler

- ✅ **Üniversite Almanya'daki "vorab" prosedürü tetiklerse** (önceden onay)
- ✅ Eksiksiz, organize evrak
- ✅ Sigorta ve Sperrkonto önceden hazır
- ✅ Ankara > İstanbul (yoğunluk farkı)
- ✅ Eylül/Mart dışı başvuru (semester başlangıçları çok yoğun)

## Geciktiren Faktörler

- ❌ Eksik veya çelişkili evrak
- ❌ Belirsiz finansman kaynağı (sponsor mektubu detaysızsa)
- ❌ Şüpheli motivasyon mektubu (kopyala-yapıştır olduğu anlaşılan)
- ❌ Önceki Schengen reddi

## Pratik Tavsiye

**Akademik takvime göre geri sayım:**
- Wintersemester (Ekim) için → **Mayıs sonu** başvur
- Sommersemester (Nisan) için → **Kasım** başvur

Geç başvuru = randevu bulamama riski. Beklerken pasaportun konsoloslukta kalmayacağı için seyahat edebilirsin.

Bağlantılı: [Ankara randevu süresi](/sss/vize/ankara-konsoloslugundan-vize-randevusu-ortalama-kac-haftada-aliniyor) · [Vize reddedilirse](/sss/vize/vize-basvurum-reddedilirse-ne-yapabilirim)
MD,

            'fintiba-disinda-bloke-hesap-sperrkonto-acabilecegim-alternatifler-nele' => <<<'MD'
Fintiba en popüler olsa da Almanya konsolosluğunun kabul ettiği başka Sperrkonto sağlayıcıları da var. Önemli olan **resmi tanınma** — listede olmalı.

## Kabul Edilen Sperrkonto Sağlayıcıları

| Sağlayıcı | Açılış ücreti | Aylık | Kapanış |
| --- | --- | --- | --- |
| **Fintiba** | ~89 € | 4.90-9.90 € | 25 € |
| **Expatrio** | 49 € | 5 € | Ücretsiz |
| **Coracle** | 99 € | ~4 € | Ücretsiz |
| **Deutsche Bank** | ~150 € | 5 € | Ücretsiz |
| **Sparkasse** (yerel) | ~50 € | ~5 € | Değişken |

## Karşılaştırma

**Fintiba:** En hızlı (24 saatte hesap), Türkiye'den dijital. Plus paket: özel sigortayı içeriyor.

**Expatrio:** Daha ucuz, "Value Package" sigorta + Sperrkonto bundle. Türkiye'den online açılır.

**Coracle:** Almanya'daki banka yerine 3. taraf. SwiftSEPA hızlı, dezavantaj: hesap kapanış süreci biraz uzun.

**Deutsche Bank:** Klasik banka. Pahalı ama uzun vadede güvenli — DE'ye gidince normal hesaba dönüştürebilirsin.

## Hangisi Sana Uygun?

- **Hızlı, online, dijital istiyorum** → Fintiba veya Expatrio
- **DE'de yerleşince banka hesabımı devam ettirmek istiyorum** → Deutsche Bank / Sparkasse
- **En düşük masraf** → Expatrio veya Coracle

Konsolosluk web sitesindeki [resmi liste](https://www.auswaertiges-amt.de) güncel kalır — açmadan kontrol et.

Bağlantılı: [Bloke hesapta ne kadar para](/sss/vize/bloke-hesapta-ne-kadar-para-olmasi-gerekiyor) · [Fintiba Basic vs Plus](/sss/vize/fintiba-basic-ve-plus-arasindaki-fark-nedir-ulusal-vize-icin-hangisi-y)
MD,

            'bloke-hesapta-ne-kadar-para-olmasi-gerekiyor' => <<<'MD'
**2026 yılı için yıllık tutar: 11,904 € (aylık 992 €).**

Bu miktar her yıl Alman federal hükümetinin belirlediği BAföG asgari geçim seviyesine göre güncellenir. Geçmişe bakış:

| Yıl | Aylık | Yıllık |
| --- | --- | --- |
| 2026 | **992 €** | **11,904 €** |
| 2025 | 992 € | 11,904 € |
| 2024 | 934 € | 11,208 € |
| 2023 | 934 € | 11,208 € |
| 2022 | 861 € | 10,332 € |

## Önemli Detaylar

- Vize süresi 1 yıldan kısa ise → **kalan ay × 992 €** yeterli
- Dil kursu vizesi → kurs ayı × 992 €
- Para **tek seferde** yatırılır, bankaca kilitlenir
- Geldikten sonra Almanya'da her ay 992 € çekme yetkin olur (otomatik düşmez, sen aktarırsın)

## Aile Durumu

- Eş/çocuk için **ayrı Sperrkonto açtırmak gerekmez**, ama bütçeyi gösterirsen vize kararını destekler
- BAföG'e göre eş için ek **~900 €/ay**, çocuk başına **~300 €/ay** önerilir

## Sponsorla Kanıtlama Alternatifi

Sperrkonto açmak istemiyorsan iki alternatif var:
1. **Verpflichtungserklärung** — Almanya'da yaşayan birinin "geçimini üstleneceğim" beyanı
2. **Burs belgesi** — DAAD gibi tanınmış bursun varsa tek başına yeterli

Bağlantılı: [Sperrkonto alternatifleri](/sss/vize/fintiba-disinda-bloke-hesap-sperrkonto-acabilecegim-alternatifler-nele)
MD,

            'dil-kursu-kaydi-en-az-kac-ay-olmak-zorunda' => <<<'MD'
Dil kursu vizesi (§16f) için kayıt **minimum 6 ay** olmak zorunda.

## Şartlar

- Kurs süresi: **6-12 ay** (12 aydan uzun olamaz)
- Yoğunluk: **Haftalık en az 18 saat** intensif kurs
- Format: **Yüz yüze sınıf** (online çoğunlukla kabul edilmez)
- Kurum: Resmi akredite dil okulu (DSV, Goethe, vb.)

## 3 Aylık Kurs Reddedilir Mi?

Konsolosluk kurs vizesini çok denetler. **3 aylık kurs ile ulusal vize verilmez** — onun yerine kısa süreli Schengen vizesi (Sprachferien turist vize) gerekir, ama Türk pasaportuyla onun da onayı zor.

## "Önce 3 ay sonra uzatım" Stratejisi

Çoğu öğrenci "3 ay yaparım, sonra DE'de uzatırım" diye düşünüyor. **Bu işlemiyor:**
- Konsolosluk başlangıç süresine göre karar verir
- DE'de uzatma için Ausländerbehörde'ye geçerli sebep göstermek gerekir
- Reddedilince sınır dışı edilme riski var

## Pratik Plan

Hedefin master/lisans okumaksa **dil kursu vizesi yerine üniversite/Studienkolleg vizesi (§16b)** daha güvenli yol. §16b'de kurs süresi serbest, ana amaç akademik eğitim.

Bağlantılı: [Dil kursu vizesi nasıl alınır](/sss/vize/dil-kursu-vizesi-basvurusu-nasil-yapilir) · [Sperrkonto açtırsam dil vizesi alır mıyım?](/sss/vize/sperrkonto-actirsam-bile-dil-kursu-vizesi-alabilir-miyim)
MD,

            'dil-kursu-vizesinde-cocuga-da-vize-cikarilir-mi' => <<<'MD'
**Dil kursu vizesinde (§16f) eş ve çocuk için aile birleşimi yolu çoğunlukla mümkün değil.** §16f bir "geçici amaçlı" vize türü.

## Neden Aile Vizesi Zor

- Dil kursu vizesi maksimum 12 ay
- Aile birleşimi vizesi **kalıcı amaç** ister (öğrenim, iş, yerleşik oturum)
- §16f sahibinin "ailesini destekleyecek finansal kaynak" göstermesi de gerek — Sperrkonto eş + çocuk için ayrı miktar şart

## İstisna: Bağımsız Vize ile Yan Yana

Eş ve çocuk **kendi başına** Schengen veya kısa süreli ziyaret vizesi ile gelebilir. Ama bu süre 90 gün/180 gün kuralına tabi — Almanya'da yerleşik kalamazlar.

## Akademik Vize ile Aile

Eğer üniversite vizen (§16b) varsa durum farklı:
- Lisans/master öğrencisi eş ve çocuğu için aile vizesi **mümkün**
- Aile vizesi başvurusu için ek belgeler: evlilik cüzdanı (apostilli + tercüme), doğum belgesi, ortak finansal kaynak

## Pratik Çözüm

Eğer aile birlikte göç planınız varsa:
1. **Akademik vize** (§16b) ile gelin — dil kursu vizesi değil
2. Vize başvurusunda eş ve çocuğu **aynı anda** belirtin (aile birleşimi paralel başvurusu)
3. Sperrkonto eş için **~900 €/ay × süre**, çocuk başına **~300 €/ay × süre** ek

Bağlantılı: [Dil kursu vizesi başvuru](/sss/vize/dil-kursu-vizesi-basvurusu-nasil-yapilir)
MD,

            'vize-icin-istenen-saglik-sigortasinin-kapsami-ne-olmali' => <<<'MD'
Vize için sağlık sigortası **iki tipte** olabilir — duruma göre seçim önemli.

## Tip 1: Seyahat Sigortası (Reisekrankenversicherung)

İlk geliş dönemi için, ilk 90 gün veya kayıt öncesi süre.

- **Minimum 30,000 € teminat** zorunlu
- Avrupa geneli kapsam
- Acil tedavi + hastane + ülkeye dönüş
- Süre: Genelde 3-12 ay
- Ortalama maliyet: **~30-100 €/ay**

Sağlayıcılar: Hanse Merkur, Mawista Visum, DR-WALTER, Care Concept.

## Tip 2: Alman Yasal Sağlık Sigortası (GKV)

Üniversiteye kayıt sonrası tam dönem için.

- Aylık ~**130 €** (öğrenci tarifesi, 30 yaş altı)
- 30 yaş üstü → **özel sigorta (PKV)** geçişi: aylık ~80-200 €
- Tüm tıbbi giderler dahil (acil, kronik, ilaç)

Sağlayıcılar: TK, AOK, Barmer, DAK, BKK.

## Hangisi Vize için Yeterli?

Vize başvurusunda iki kabul edilen senaryo:

1. **Direkt GKV taahhüt belgesi** (Almanya'daki üniversitenin onlinde işlem yaptığı bir sigorta var, "incoming statement" alıyorsun) → Tek başına yeterli ✅
2. **İlk 3 ay seyahat sigortası + GKV taahhüt** → Klasik kombinasyon ✅

## Fintiba/Expatrio Paketleri

- **Fintiba Plus:** Sperrkonto + seyahat sigortası + GKV geçiş yardımı (paket fiyat)
- **Expatrio Value:** Aynı bundle, biraz daha ucuz

Çoğu öğrenci Fintiba Plus veya Expatrio paketini tercih ediyor — tek seferde halloluyor.

Bağlantılı: [Vize evrakları](/sss/vize/vize-basvurusunda-istenen-evraklar-nelerdir) · [Sigorta seçimi (TK, AOK)](/sss/sigorta)
MD,

            'fintiba-basic-ve-plus-arasindaki-fark-nedir-ulusal-vize-icin-hangisi-y' => <<<'MD'
Fintiba'nın iki paketi var; vize için ikisi de kabul edilir ama **kapsam farkı** öğrenciye göre değişir.

## Karşılaştırma

| Özellik | Basic | Plus |
| --- | --- | --- |
| **Sperrkonto** | ✅ | ✅ |
| **Açılış ücreti** | 89 € | 89 € |
| **Aylık** | 4.90 € | 9.90 € |
| **Seyahat sigortası** | ❌ | ✅ (ilk 90 gün) |
| **GKV bağlantısı (TK)** | ❌ | ✅ |
| **Kayıt+vize danışmanlığı** | ❌ | ✅ |
| **24/7 destek** | Email | Telefon + chat |

## Ulusal Vize İçin?

Konsolosluk açısından **ikisi de kabul edilir**, çünkü vize için iki ana belge gerekli: Sperrkonto kanıtı + sigorta. Basic'te sigorta yok, dolayısıyla **ayrıca sigorta yaptırman gerekiyor**.

## Hangisi Sana Uygun?

✅ **Basic + ayrı sigorta** seç şu durumlarda:
- Hangi sigortayı istediğini biliyorsun (Care Concept, Mawista vs.)
- Önceden sigorta düşürme tecrübesi var
- Bütçen sıkı (Basic + ucuz sigorta = ~120 €/ilk yıl toplam)

✅ **Plus** seç şu durumlarda:
- "Her şeyi paketten halletsin" istiyorsun
- İlk kez Almanya'ya gidiyorsun, kaybolmamak istiyorsun
- TK ile öğrenci GKV geçişini hazır yapsınlar (sonra TK'ya geçişi sen düzenlersin)

## Pratik Sayı

12 aylık toplam:
- **Basic + ayrı seyahat sigortası:** ~89 + (4.9 × 12) + (50 × 12) = **~750 €**
- **Plus:** ~89 + (9.9 × 12) = **~210 €** (ilk 90 günlük sigorta dahil, sonra GKV)

Plus uzun vadede daha ekonomik.

Bağlantılı: [Sperrkonto alternatifleri](/sss/vize/fintiba-disinda-bloke-hesap-sperrkonto-acabilecegim-alternatifler-nele)
MD,

            'sperrkonto-actirsam-bile-dil-kursu-vizesi-alabilir-miyim' => <<<'MD'
**Sperrkonto açtırmak vizenin sadece bir şartı**, tek başına kabul anlamına gelmiyor. Son dönemde konsolosluk dil kursu vizesini (§16f) sıkı denetliyor.

## Sperrkonto + Kabul Şansı Şu Durumlarda Yüksek

- ✅ **B1+ Almanca sertifikan var** (Goethe, Telc, ÖSD)
- ✅ Kurs **6+ ay**, haftalık **18+ saat**, **yüz yüze**
- ✅ Akredite dil okulu (DSV listesinde)
- ✅ Mantıklı motivasyon mektubu — "neden Almanya'da, neden bu okul"
- ✅ Türkiye'de mevcut iş/eğitim bağlantısı (geri dönüş niyeti güveni)
- ✅ Kurs ücretinin yatırıldığı dekont

## Sperrkonto Olsa Bile Reddedilebilirsin Şu Durumlarda

- ❌ Dil kursunu "üniversiteye giriş için kısa yol" gibi sunuyorsan
- ❌ Eş zamanlı **şartlı üniversite kabul** belgen varsa (konsolosluk "neden direkt §16b ile değil?" sorar)
- ❌ A1 altı dil seviyen varsa ve Türkiye'de "neden başlamadın?" cevabın yoksa
- ❌ Önceki Schengen reddi geçmişin varsa

## Gerçekçi Olalım

Son 1-2 yıldır **§16f reddedilme oranı %30-40 civarı.** Bu yüzden topluluk önerisi:

> Eğer master/lisans hedefin varsa, dil kursu vizesi yerine **doğrudan akademik vize (§16b)** dene. Şartlı kabulle bile §16b §16f'ten daha kabul görüyor.

Bağlantılı: [Üniversite kabul belgesi yeterli mi?](/sss/vize/universite-kabul-belgesi-vize-basvurusu-icin-yeterli-mi)
MD,

            'ankara-konsoloslugundan-vize-randevusu-ortalama-kac-haftada-aliniyor' => <<<'MD'
Ankara konsolosluğu öğrenci vizesi randevu süreleri **mevsime göre çok değişiyor**.

## Tipik Bekleme Süreleri (2025-2026)

| Dönem | Randevu süresi |
| --- | --- |
| **Mart-Mayıs** (Wintersemester hazırlık) | **6-10 hafta** |
| **Haziran-Ağustos** (peak) | **3-5 ay** |
| **Eylül-Kasım** (Sommersemester hazırlık) | **4-8 hafta** |
| **Aralık-Şubat** (sakin dönem) | **2-4 hafta** |

## Randevu Türleri

- **Ulusal vize (Studium)** — Ankara konsolosluk doğrudan
- **IDATA üzerinden** — bazı işlemler için merkezleştirildi (İstanbul, İzmir, Antalya, Bursa)
- **Mavi kart / iş arama vizesi** — IDATA Ankara (farklı kategori)

## Hızlı Randevu Stratejileri

1. **VFS Global / IDATA web sayfasını sabah 09:00'da yenile** — yeni randevular o saatlerde açılıyor
2. **Hafta sonu/Pazartesi sabah** kontrol et — birikim açılır
3. **Cancellation çek** — günde 3-5 kez bak
4. **Konsolosluk doğrudan e-mail at:** vize@ankara.diplo.de — acil sebep belirt (semester start tarihi)
5. **Vorab-Zustimmung al** — Almanya'daki Ausländerbehörde'den önceden onay → randevu öncelik

## Önemli Tarih

> Wintersemester (Ekim) için hedefliyorsan, **en geç Mayıs başında** başvuru sürecini başlat. Yaz pikini kaçırırsın.

Bağlantılı: [Vize başvurusu kaç gün sürer?](/sss/vize/vize-basvurusu-sonrasi-olumluolumsuz-donus-ortalama-kac-gun-surer)
MD,

            'vize-basvurusunda-istenen-evraklar-nelerdir' => <<<'MD'
Vize başvurusunda her belgenin **2 kopya** (1 orijinal + 1 fotokopi) olması gerekli. Eksik evrak randevuda kabul edilmez.

## Zorunlu Evraklar

### Kişisel
- 🆔 **Pasaport** (vize bitiminden 3 ay+ sonrasına geçerli)
- 📷 **2 biyometrik fotoğraf** (son 6 ay)
- 📋 **Doldurulmuş başvuru formu** (VIDEX online doldur, çıktısını al)
- 📝 **Beyan formu** (bilgi doğruluğu)
- 💳 **Vize ücreti** (75 €, peşin nakit veya kartla)

### Akademik
- 🎓 **Kabul belgesi** (Zulassung) — uni veya Studienkolleg
- 🎓 **Lise diploması + transcript** (apostilli + yeminli tercüme)
- 🎓 **Lisans diploması + transcript** (varsa, master için)
- 🗣️ **Dil sertifikası** (TestDaF/DSH/IELTS, programa göre)
- ✍️ **Motivasyon mektubu** (1-2 sayfa, Almanca veya İngilizce)
- 📚 **CV / Özgeçmiş** (kronolojik, Almanca/İngilizce)

### Finansal
- 🏦 **Sperrkonto onayı** (Fintiba/Expatrio/banka)
- 🛡️ **Sağlık sigortası** (min 30K € teminat veya GKV taahhüt)
- 💰 **Burs belgesi** (varsa)
- 📊 **Aile finansal durum belgesi** (varsa, sponsor varsa)

## Apostil + Tercüme

**Apostil:** İlgili valiliğin İl Yazı İşleri Müdürlüğünden alınır (1-3 gün).
**Yeminli tercüme:** Konsolosluk Almanya'da yapılmasını tercih ediyor ama TR yeminli tercüme de kabul edilir — bazı eyaletlerin (BW, SH, Niedersachsen, Hamburg) DE yeminli tercüman istediği biliniyor.

## Pratik İpucu

Konsolosluk web sitesindeki **güncel checklist'i** mutlaka indir — yıl içinde madde değişebiliyor.
MD,

            'vize-basvurum-reddedilirse-ne-yapabilirim' => <<<'MD'
Vize reddi yaşadıysan **3 seçeneğin var**: itiraz, yeni başvuru, dava.

## Adım 1: Red Sebebini Anla

Red yazısında sebep listelenir. En sık 4 tür:

| Red sebebi | Düzeltilebilir mi? |
| --- | --- |
| Eksik/yetersiz evrak | ✅ Evet, yeni başvuruyla |
| Finansman kanıtı yetersiz | ✅ Sperrkonto/sponsor ile |
| Şüpheli motivasyon | ⚠️ Daha güçlü mektup, planlama gerekli |
| "Niyet" şüphesi (geri dönmeme) | ⚠️ TR'deki bağ kanıtı, ailesel/iş kanıtları |

## Adım 2: İtiraz (Remonstration)

- **Süre:** Red kararından **1 ay** içinde
- **Form:** Konsolosluğa yazılı dilekçe (Almanca veya Türkçe)
- **Ücret:** Yok (ücretsiz)
- **Süre:** Sonuç 4-12 hafta
- **İçerik:** Red sebebine somut delil + ek belge

## Adım 3: Yeni Başvuru

İtiraz yerine doğrudan **yeni başvuru** da yapabilirsin.

- ✅ Daha hızlı (yeni randevu süresi kadar)
- ✅ Yeni 75 € ücret
- ⚠️ Önceki ret kaydı dosyada kalır — eksiklikleri net düzelt
- ⚠️ Aynı ret sebebi sürerse 2. ret çok daha kalıcı zarar verir

## Adım 4: İdari Dava (Verwaltungsgericht Berlin)

Son çare:

- Avukat zorunlu, ücret yüksek (~1000-3000 €)
- Süre uzun (6-18 ay)
- Sadece açık hukuki hata varsa anlamlı

## Pratik Tavsiye

1. Reddi **soğukkanlı** oku, sebebini netleştir
2. **Yeni başvuru** çoğu durumda en hızlı yol
3. Eksiklikleri düzelt, motivasyon mektubunu **yeniden yaz**
4. Üniversiteden **rektörlük yazısı** al — "öğrencimizi bekliyoruz" tarzı destek
5. Vorab-Zustimmung al — Almanya'daki Ausländerbehörde önceden onaylasın

İtiraz kabul edilirse vize 1-2 hafta içinde basılır.
MD,

            'almanya-ogrenci-vizesi-kac-yil-gecerli-oluyor' => <<<'MD'
Almanya öğrenci vizesi ilk verildiğinde **maksimum 1 yıl** geçerli, ama Almanya'ya gidince oturum iznine (Aufenthaltstitel) çevrilir.

## Aşamalar

### Aşama 1: D Tipi Ulusal Vize (Konsolosluk)
- Süre: **3 ay - 1 yıl**
- Geçerlilik: Sadece Almanya'ya giriş için
- Yeniden giriş izni: Almanya'daki ilk Anmeldung'a kadar

### Aşama 2: Öğrenci Oturum İzni (Ausländerbehörde)
Almanya'ya gelince Bürgeramt'a kayıt + Ausländerbehörde randevusu:

- Süre: **1-2 yıl** (üniversite süresi boyunca uzatılır)
- En fazla **toplam öğrenim süresi** kadar (lisans 4 yıl, master 2 yıl, PhD belirsiz)
- Mezuniyet sonrası **18 aylık iş arama vizesi** otomatik geçiş hakkı

## Uzatma

- 3 ay öncesinden Ausländerbehörde'ye başvur
- Güncel **immatrikulationsbescheinigung** (kayıt belgesi)
- Sperrkonto bakiyesi (devam ediyor mu)
- Sigorta geçerlilik
- Ders ilerleme durumu (Leistungsnachweis) — 4. dönem sonrası önemli

## Önemli Süre Kuralı

- Yıllık en fazla **20 saat/hafta** çalışabilirsin (yoksa vize sorununa girer)
- Tam zamanlı **120 gün veya yarı zamanlı 240 gün/yıl** çalışma izni
- 3-4 dönemde "yeterli akademik ilerleme" bekleniyor — yoksa uzatma reddedilebilir

## Mezuniyet Sonrası

Diploma alır almaz **18 aylık iş arama vizesi (§ 20 Abs. 3)** — koşulsuz. İş bulunca **Mavi Kart** veya **çalışma izni**.

Bağlantılı: [Master sonrası iş arama vizesi](/sss/master/master-sonrasi-is-arama-vizesi-jobsuche-kac-ay-gecerli)
MD,

            'master-kabul-belgemle-vize-basvurusunu-almanyadan-yapabilir-miyim' => <<<'MD'
**Hayır — ulusal vize (Studium D tipi) Almanya'dan başvurulamaz.** Türkiye'deki konsoloslukta başvurman gerekiyor.

## Neden?

§ 71 AufenthG uyarınca ulusal vize başvurusu **yurt dışındaki Alman temsilciliğinde** yapılır. Almanya'da turist olarak bulunsan bile vize başvuru süreci için ülkeye dönmek zorundasın.

## İstisnalar (Çok Dar)

Almanya'dan başvurabileceğin durumlar:

| Durum | Almanya'dan? |
| --- | --- |
| Schengen vizesiyle gelmiş, sonradan kalmaya karar verdim | ❌ |
| AB vatandaşıyım, eşim Türk | ✅ Aile birleşimi |
| AB ülkesinde oturma iznim var | ✅ Bazı durumlarda |
| Üst düzey araştırmacı/uzman | ✅ (özel düzenleme) |
| Standart Türk öğrenci | ❌ TR'de başvuru zorunlu |

## "Schengen ile gel, sonra geç" Stratejisi

Bu yaygın bir yanlış anlama. Schengen vizesiyle Almanya'ya gelirsen:

- Maksimum **90 gün / 180 gün** kalma hakkın var
- Bu süre içinde ulusal vize başvurusu yapamazsın
- Üniversite Anmeldung'u **vize bekleyenler için** geçici öğrenci statüsü vermez
- Süre dolunca TR'ye dönmek zorundasın

## Pratik Yol

1. **Master kabul belgesini al**
2. TR'de yaşarken **konsolosluk randevusu** al
3. **Sperrkonto, sigorta, evrak** hazırla
4. Türkiye'de bekle, vize çıkınca uç
5. Geldiğin gün Anmeldung + üniversite kaydı

## "Bekleme süremde Almanya'yı tanıyayım" diyorsan

Schengen vizesiyle 2 hafta gez, hostel/Airbnb kal, üniversiteyi ziyaret et — sonra TR'ye dön ve vize bekle. Bu meşru bir yol.

Bağlantılı: [Vize evrakları](/sss/vize/vize-basvurusunda-istenen-evraklar-nelerdir)
MD,

            'yesil-pasaportla-ogrenci-vizesi-basvurusu-nasil-yapilir' => <<<'MD'
Yeşil pasaport (Hizmet Pasaportu) Schengen ülkelerine vize muafiyeti sağlar — **ama 90 gün/180 gün turist amacıyla**. Öğrenci olarak Almanya'da kalmak için yine de **ulusal vize (D tipi) gerekir.**

## Kafa Karışıklığı Sebebi

Yeşil pasaportla "vizesiz girebilirim" doğru — ama bu **turist statüsü**. Öğrenim için:

| Pasaport tipi | Schengen turist | Ulusal vize gerekli mi? |
| --- | --- | --- |
| Bordo (normal) | Vize gerekli | Evet (D tipi) |
| Yeşil (hizmet) | 90 günlük muaf | **Evet, D tipi gerekli** |
| Gri (özel) | 90 günlük muaf | Evet (D tipi gerekli) |
| Diplomatik | Vize muaf | Bazı durumlarda muaf |

## Yeşil Pasaportlu Öğrencinin İki Avantajı

1. **Schengen vizesiyle hızlı geliş**: Önce uçabilirsin, çok daha hızlı (vize gerekmez ki). Konsolosluğa "yerinde başvuru" yapabilir misin? Standart kuralda **hayır** — ama bazı durumlarda esneklik var (aşağıda).

2. **Randevu süreçi yine başvuru gerektirir**: D vize başvurusu için randevu alıp evraklarla başvuracaksın. Tek farkın: bu süreçte Almanya'ya kısa süreli giriş çıkış yapmakta daha rahatsın.

## Yeşil Pasaportla "Yerinde" D Vize Başvurusu

Almanya'daki Ausländerbehörde'nin bazıları yeşil pasaportlulara **DE'den başvuru yapma izni** veriyor — ama bu **iyi kalpli istisna**, talep edip reddedilebilirsin.

Strateji:
1. Yeşil pasaport ile turist olarak gel
2. Vize süreni dolmadan üniversitenin Studienberatung'una git
3. Ausländerbehörde'ye **Aufenthaltserlaubnis zum Studium** başvurusu yap
4. Kabul edilirse vize sürecini DE'de tamamlarsın

## Standart Yol (Daha Güvenli)

Yeşil pasaport olsa bile **TR'de konsolosluktan D vize başvurusu** yap. Süreç aynı:

- Kabul belgesi
- Sperrkonto
- Sigorta
- Evrak
- Randevu (yeşil pasaportlu fast track bazı konsolosluklarda mevcut)

Bağlantılı: [Almanya'dan vize başvurusu mümkün mü?](/sss/vize/master-kabul-belgemle-vize-basvurusunu-almanyadan-yapabilir-miyim)
MD,

            // ============ DİL ============

            'goethe-online-kursu-nasil-tavsiye-eder-misiniz' => <<<'MD'
Goethe Institut online kursları kalite-fiyat dengesinde **güvenilir** kabul ediliyor, ama herkese uygun olmayabilir.

## Goethe Online Kursu Türleri

| Kurs | Süre | Fiyat | Format |
| --- | --- | --- | --- |
| **Grup kursu** | 8-12 hafta | ~700-900 € | Canlı dersler + ödev |
| **Bireysel kurs** | Esnek | 65-95 €/saat | 1:1 öğretmen |
| **Self-learning** | Esnek | 100-200 €/ay | Platform, oto-değerlendirme |
| **Yoğun kurs** | 4 hafta | ~1200 € | Günlük 4-5 saat |

## Avantajları

✅ **Sertifika prestijli** — vize konsolosluğunda en güvenli
✅ Profesyonel materyaller, deneyimli öğretmenler
✅ Esnek saatler — Türkiye'den katılabilirsin
✅ A1'den C2'ye tüm seviyeler
✅ Doğrudan Goethe Institut sınavına hazırlık

## Dezavantajları

❌ **Pahalı** — özellikle bireysel ders
❌ Grup dersi sayısı sınırlı, kontenjan dolabiliyor
❌ Self-learning platformu yalnız kalıyorsan zor
❌ Sınav ayrı ücret (~150-180 €)

## Goethe Alternatifleri

| Kurum | Avantaj | Dezavantaj |
| --- | --- | --- |
| **DeutschAkademie** | Ücretsiz online dersler, video kaynak | Sertifika vermez |
| **Lingoda** | 6+ kez/hafta canlı | Aylık ~250-400 € |
| **Babbel/Duolingo** | Çok ucuz | Üst seviyelere yetmiyor |
| **Italki/Preply** | 8-25 €/saat hoca | Kalite değişken |
| **Yerel kurslar** | Yüz yüze, ucuz | Tempo standart |

## Tavsiye

Bütçe iyiyse Goethe + DeutschAkademie kombinasyonu. Bütçe sıkı ise → DeutschAkademie + ucuz italki hoca + Goethe sınavına direkt gir.

Bağlantılı: [Online vs yüz yüze kurs](/sss/dil/online-almanca-kursu-mu-yuz-yuze-mi-daha-verimli) · [Online ders hoca önerisi](/sss/dil/online-ders-veren-almanca-ogretmeni-onerisi-var-mi)
MD,

            'testdaf-ile-dsh-arasindaki-fark-nedir' => <<<'MD'
TestDaF ve DSH, Alman üniversitelerinde Almanca yeterliliği için kabul edilen iki ana sınav. Yapı, format ve geçerlilik açısından önemli farkları var.

## Kısa Karşılaştırma

| Kriter | TestDaF | DSH |
| --- | --- | --- |
| **Düzenleyici** | Merkezi sınav (TestDaF e.V.) | Her üniversite kendi |
| **Sıklık** | Yılda 6 kez | Üniversiteye göre değişir |
| **Geçerlilik** | Süresiz, her uni kabul eder | Sadece o uni için |
| **Türkiye'de girilir mi?** | ✅ Goethe Institut'larda | ❌ Hayır |
| **Çoklu uni başvuru** | ✅ Tek sertifika, her yer | ❌ Her uni için tekrar |
| **Yapı** | 4 bölüm: okuma, dinleme, yazma, konuşma | Yazılı + sözlü |
| **Süre** | ~3 saat | ~4-5 saat (yazılı) + 15-20 dk sözlü |
| **Ücret** | ~195 € | 50-150 € |
| **Notlama** | TDN 3, TDN 4, TDN 5 | DSH-1, DSH-2, DSH-3 |

## Kabul Seviyeleri

- **TestDaF:** Her bölümden **TDN 4** standart kabul, tıp/hukuk **TDN 5**
- **DSH:** **DSH-2** standart kabul, bazı programlar **DSH-3** ister

## Hangisini Seçmelisin?

✅ **TestDaF tercih et şunlar için:**
- Türkiye'den başvuruyorsan
- Birden fazla üniversiteye başvuracaksan
- Sertifikan ömür boyu kalsın
- Net format, hazırlık materyali bol

✅ **DSH tercih et şunlar için:**
- Zaten Almanya'dasın (Studienkolleg, dil kursu)
- Hedef üniversiten net, oraya gideceksin
- Daha "yazılı ağırlık", konuşmayı ayrı bölüm olmadığı

## Pratik Yol

Çoğu Türk öğrenci için: **önce TR'de TestDaF al → kabul mektubu → Almanya'ya git**. TestDaF yetmezse DE'de Studienkolleg'in DSH'sına gir.

## Alternatifler

- **Telc C1 Hochschule** — DSH-2 eşdeğeri kabul ediyor (her uni değil)
- **Goethe C2** — yüksek prestij, çoğu uni kabul ediyor
- **ÖSD C1** — Avusturya menşeli, Almanya'da kabul oranı düşük

Bağlantılı: [DSH-2 mi DSH-3 mü?](/sss/dil/dsh-hangi-seviyede-yeterli-kabul-edilir-dsh-2-mi-dsh-3-mu) · [Goethe vs Telc](/sss/dil/goethe-vs-telc-sertifikasi-uni-basvurusu-icin-fark-var-mi)
MD,

            'b2-sertifikasi-ile-master-basvurusu-yapabilir-miyim' => <<<'MD'
**Programa göre değişir** — B2 bazı master programları için yeterli, çoğu için ise C1 standart.

## Programa Göre Dil Şartı

| Program tipi | Tipik şart |
| --- | --- |
| **Almanca master (mühendislik, fen)** | C1 (TestDaF 4, DSH-2) |
| **Almanca master (sosyal bilim, hukuk)** | C1 (zorunlu, bazen C2) |
| **İngilizce master** | İngilizce IELTS 6.5 / TOEFL 90 + **B1 Almanca** (bazı uniler) |
| **MBA, executive program** | İngilizce + minimum A2 Almanca (genelde) |
| **Sanat, müzik (Kunst, Musik)** | B2 yeterli, ek olarak portfolio/sınav |
| **Mimar, tasarım programları** | B2-C1 + portfolio |

## B2 Master Başvurusu Yapabileceğin Durumlar

✅ **İngilizce master programı** (1000+ var) — Almanca B2/B1 destekleyici
✅ **Şartlı kabul** — uni "geldikten sonra C1 yap" diyor
✅ **Studienkolleg M-Kurs sonrası** — DSH yerine TestDaF B2 kabul edilebilir
✅ **Sanat ve uygulamalı programlar** — pratik beceri ağırlık

## Yetmediği Yaygın Durumlar

❌ Tıp (tabi ki PhD ile farklı, klinik branş)
❌ Hukuk masterı
❌ Almanca filolojisi
❌ Klasik DAAD bursu (genelde C1 ister)

## "B2'm var ama Master istiyorum" Stratejileri

1. **İngilizce master programlarına yön ver** — DAAD veritabanında 1000+ program: https://www.daad.de/en/studying-in-germany/courses/all-degrees/
2. **C1'e çıkarmaya odaklan** — TestDaF veya DSH'a 3-6 ayda hazırlanabilirsin
3. **Şartlı kabul** al, geldikten sonra Almanya'da hızla C1 yap
4. **Studienkolleg M-Kurs** dene — 1 yıl, hem dil hem akademik hazırlık

## Pratik Yan Not

İngilizce master programlarında bile Almanya'da yaşam, staj, ve mezuniyet sonrası iş için **Almanca C1** pratik olarak gerekli. B2 ile başla, paralel öğrenmeye devam et.

Bağlantılı: [Almanca C1 hangi sınavla kanıtlanır?](/sss/dil/almanca-c1-hangi-sinav-ile-kanitlanir) · [IELTS ile İngilizce master](/sss/dil/ielts-ile-almanyada-ingilizce-master-basvurusu-yapilabilir-mi)
MD,

            'almanca-c1-hangi-sinav-ile-kanitlanir' => <<<'MD'
Almanca C1 seviyesini kanıtlayan **5 ana sertifika** var. Hangisini seçtiğin başvuracağın üniversiteye göre değişir.

## Kabul Edilen C1 Sınavları

| Sertifika | Düzenleyici | Geçerlilik | Türkiye'de? |
| --- | --- | --- | --- |
| **TestDaF (TDN 4 her bölüm)** | TestDaF e.V. | Süresiz | ✅ Goethe Institut |
| **DSH-2** | Üniversiteler | Sadece o uni için | ❌ |
| **Goethe-Zertifikat C1** | Goethe Institut | Süresiz | ✅ |
| **telc Deutsch C1 Hochschule** | telc | Süresiz | ✅ |
| **ÖSD Zertifikat C1** | Österreichisches Sprachdiplom | Süresiz | Sınırlı |

## Üniversitelerin Kabul Tablosu

| Sınav | Üniversitelerin kabul oranı |
| --- | --- |
| TestDaF TDN 4 | %100 — herkes kabul |
| DSH-2 | %100 — herkes kabul |
| Goethe C1 | %95+ |
| telc C1 Hochschule | %85+ |
| ÖSD C1 | %70 (üniversiteye göre değişken) |

## Hangisi Sana Uygun?

✅ **TestDaF** — Türkiye'den başvuruyorsan ve birden fazla uniye yazacaksan en güvenli
✅ **Goethe C1** — Daha prestijli, dil okulundan sonra direkt sertifika almak istiyorsan
✅ **telc C1 Hochschule** — Yeni sınav, daha ucuz (~150 €), bazı kişiler "daha kolay" diyor
✅ **DSH-2** — Almanya'daki Studienkolleg/dil kursu sonunda al

## C1 Sınavı Karşılaştırma

| Açı | TestDaF | Goethe C1 | telc C1 Hochschule |
| --- | --- | --- | --- |
| **Ücret** | ~195 € | ~250 € | ~150 € |
| **Süre** | ~3 saat | ~4 saat | ~3.5 saat |
| **Konuşma** | Dijital kayıt | Yüz yüze interview | Yüz yüze interview |
| **Yazma** | 1 essay | 1 essay + 1 e-mail | 1 essay |
| **Tekrar girilebilir mi?** | ✅ İstediğin kadar | ✅ İstediğin kadar | ✅ İstediğin kadar |

## Pratik İpucu

Eğer TestDaF'tan sadece 1 bölüm 3 aldıysan, bütün sınava değil **sadece o bölüme yeniden gir** (TestDaF avantajı).

Bağlantılı: [TestDaF her bölümden kaç almak gerekli?](/sss/dil/testdaf-her-bolumden-en-az-kac-almak-gerekiyor)
MD,

            'ielts-ile-almanyada-ingilizce-master-basvurusu-yapilabilir-mi' => <<<'MD'
**Evet** — Almanya'daki İngilizce master programlarının çoğu IELTS'i kabul ediyor.

## Minimum Skor

| Program tipi | Tipik IELTS şartı |
| --- | --- |
| **STEM (mühendislik, fen)** | 6.5 (her bölüm 6+) |
| **Business, MBA** | 6.5-7.0 |
| **Sosyal bilimler** | 6.5-7.0 |
| **Hukuk LLM** | 7.0-7.5 |
| **TU9 (top teknik üniler)** | 7.0 |

⚠️ **IELTS Academic** zorunlu — General Training kabul edilmiyor.

## Almanya'da İngilizce Master Programları

DAAD veritabanında 1000+ İngilizce master programı: https://www2.daad.de/deutschland/studienangebote/international-programmes/

En çok program sunan üniversiteler:
- **TU München** — STEM
- **RWTH Aachen** — Mühendislik
- **Heidelberg** — Sosyal bilimler, fen
- **TU Berlin** — Mühendislik, computer science
- **LMU München** — Çeşitli alanlar
- **Hertie School** — Public policy, MPA

## IELTS Alternatifleri

| Sınav | Tipik şart |
| --- | --- |
| IELTS Academic | 6.5+ |
| TOEFL iBT | 90+ (top uni 100+) |
| Cambridge CAE/CPE | C1 / C2 |
| Duolingo English Test | 110+ (sınırlı kabul) |
| Anadil İngilizce | Bazı uniler İngiltere/ABD/Avustralya lisansını yeterli sayar |

## Almanca Şartı Var Mı?

- **Çoğu İngilizce master programında Almanca şartı YOK**
- Bazı uniler **A1-B1 Almanca** önerir (zorunlu değil)
- Mezuniyet sonrası Almanya'da iş için Almanca **pratik olarak gerekli**
- DAAD bursu için Almanca B1+ aranır

## Vize Başvurusu Etkisi

İngilizce master kabul belgesiyle vize başvurusunda:
- ✅ Dil sertifikası olarak IELTS yeterli
- ✅ Almanca sertifikası zorunlu değil
- ✅ Standart D tipi öğrenci vizesi (§16b)

## Pratik Tavsiye

1. IELTS 6.5'i hedefle (en yaygın geçerli)
2. Geldiğinde yan dal Almanca öğren — A2/B1 yeterli günlük yaşam için
3. DAAD bursu istiyorsan paralel Almanca öğren

Bağlantılı: [B2 ile master başvurusu](/sss/dil/b2-sertifikasi-ile-master-basvurusu-yapabilir-miyim)
MD,

            'online-ders-veren-almanca-ogretmeni-onerisi-var-mi' => <<<'MD'
Online Almanca dersleri için **3 ana kanal** var: marketplace platformları, kurum bazlı dersler, ve bağımsız öğretmenler. Hangisi sana uygun, bütçe ve hedefe bağlı.

## Marketplace Platformları (1:1 hoca)

| Platform | Saatlik ücret | Avantaj | Dezavantaj |
| --- | --- | --- | --- |
| **italki** | 8-30 € | Geniş seçim, deneme dersi 5-10 € | Kalite değişken |
| **Preply** | 10-30 € | Müsaitlik filtreleme iyi | Komisyon yüksek |
| **Lingoda** | ~12-15 €/grup ders | Sertifika programı, yapılandırılmış | Bireysel değil |
| **Cambly (DE)** | 15-25 € | Doğal konuşma, Anadolu öğretmen az | Çok native İngilizce odaklı |

## Kurum Bazlı Online Dersler

- **Goethe Institut Online** — Pahalı (~700-900 €/grup kursu) ama sertifikalı, prestijli
- **DeutschAkademie** — **Ücretsiz** online dersler (Viyana merkezli, kalite yüksek), sertifika vermez
- **Lingoda Sprint** — 1 ay 30 ders challenge'ı, başarınca para iadesi
- **Babbel Live** — Aylık abonelik, grup dersleri

## Türk Öğretmenler (Türkçe açıklamalı)

YouTube'da Türkçe Almanca öğretenler:
- **Doğukan Çakır** — Net anlatım, A1-B2 odaklı
- **Almancaclub** — Bol pratik içerik
- **Hocam Hocam (Almanca)** — Bayrak takipçi sayısı yüksek

italki/Preply'de Türk öğretmen ararken filtre: "Native German + Turkish speaker" ile bul.

## Seçim Kriteri

🎯 **A1-A2 yeni başlıyorum** → DeutschAkademie (ücretsiz) + ucuz italki hoca
🎯 **B1-B2 orta seviye, sınava hazırlanıyorum** → Lingoda + Goethe sınav prep
🎯 **C1 yoğun çalışma, TestDaF** → Goethe yoğun online + 1:1 hoca
🎯 **Konuşma pratiği ana sorunum** → italki bireysel native hoca

## Pratik İpucu

> Önce **3 farklı platformdan deneme dersi** al (5-10 € genelde). Sana en uygun öğretmeni bul, sonra haftalık 2-3 ders bağlan. Tek bir hocayla devam etmek tutarlılık sağlıyor.

Bağlantılı: [Goethe online kursu](/sss/dil/goethe-online-kursu-nasil-tavsiye-eder-misiniz) · [B2 için online hoca](/sss/dil/b2-icin-online-birebir-hoca-tavsiye-eder-misiniz)
MD,

            'goethe-sinav-tarihleri-ne-zaman-duyurulur' => <<<'MD'
Goethe sınavları **yıl boyunca düzenli olarak** yapılır. Tarihler **3-4 ay önceden** duyurulur.

## Goethe Sınav Sıklığı (Türkiye)

| Seviye | Yıllık tarih sayısı |
| --- | --- |
| **A1, A2** | ~10-12 (her ay) |
| **B1** | ~10-12 (her ay) |
| **B2** | ~8-10 |
| **C1** | ~6-8 |
| **C2** | ~3-4 |

## Resmi Takvim Nerede?

Goethe Institut Türkiye web sayfası: https://www.goethe.de/ins/tr/tr/spr/prf.html

Şehirlere göre takvim ayrıdır:
- İstanbul (en çok tarih)
- Ankara
- İzmir
- Online (Goethe Online Center)

## Tipik Duyuru Süreci

1. **Tarih açılır** → 3-4 ay önceden
2. **Kayıt açılır** → Sınavdan ~2-3 ay önce
3. **Kayıt kapanır** → Sınavdan 4-6 hafta önce
4. **Sınav** → Belirlenen tarih
5. **Sonuç** → Sınavdan 3-6 hafta sonra
6. **Sertifika** → Sonuçtan 2-4 hafta sonra

## Önemli Tarihler

⚠️ **Sınav randevuları hızlı doluyor.** Vize başvurun için B1/B2 sertifikasına ihtiyacın varsa:
- Hedef sınav tarihinden **4 ay önce** kayıt yap
- İstanbul yoğun → Ankara/İzmir alternatif düşün
- Online Goethe sınavları az ama mevcut

## Sınav Ücretleri (2026)

| Seviye | Ücret |
| --- | --- |
| A1 | ~110 € |
| A2 | ~140 € |
| B1 | ~180 € |
| B2 | ~210 € |
| C1 | ~250 € |
| C2 | ~280 € |

> Tek modül sınavlar (sadece okuma veya konuşma) genelde ~30-40 € daha ucuz.

## Goethe Yerine Telc

Goethe doluysa veya pahalıysa telc sınavları da kabul ediliyor — Türkiye'de Goethe kadar yaygın değil ama mevcut. telc fiyatları %20-30 daha ucuz.

Bağlantılı: [Almanca dil sınavı ücretleri](/sss/dil/almanca-dil-sinavi-ucretleri-ne-kadar) · [Goethe vs Telc](/sss/dil/goethe-vs-telc-sertifikasi-uni-basvurusu-icin-fark-var-mi)
MD,

            'testdaf-her-bolumden-en-az-kac-almak-gerekiyor' => <<<'MD'
TestDaF sertifikası 4 bölümden oluşur ve her bölüm **TDN 3, TDN 4 veya TDN 5** olarak puanlanır. Üniversite şartı **her bölümden** ne kadar gerektiğine bağlı.

## Genel Standart: TDN 4

Çoğu lisans ve master programı için **her 4 bölümden TDN 4** standart kabul.

| Bölüm | TDN 4 anlamı |
| --- | --- |
| Okuma anlama | %50-70 doğru |
| Dinleme anlama | %50-70 doğru |
| Yazılı anlatım | Yapılandırılmış, hatalı ama anlaşılır essay |
| Sözlü anlatım | Akıcı konuşma, terminoloji yeterli |

## Program Bazlı Şartlar

| Program | Şart |
| --- | --- |
| **Mühendislik (TU München, RWTH Aachen)** | 4 × TDN 4 |
| **Fen bilimleri** | 4 × TDN 4 |
| **Sosyal bilimler** | 4 × TDN 4, bazıları 4 × TDN 5 |
| **Tıp** | 4 × TDN 5 (zorunlu) |
| **Hukuk** | 4 × TDN 5 |
| **Edebiyat, Almancılık** | 4 × TDN 5 |
| **Sanat, tasarım** | 4 × TDN 4, bazılarında 4 × TDN 3 |

## Eksik Puanı Kapatma

TestDaF'ın güzel tarafı: **sadece eksik bölüme tekrar girebilirsin.**

Örnek: 3 bölüm TDN 4 aldın, sadece yazma TDN 3. Tekrar girip sadece yazma sınavına gir — diğerleri valid kalır.

⚠️ Tekrar girişler **aynı sınav döneminde** sayılır — yani yıl içinde 6 kez girebileceğin tarihler var.

## Toplam Puan Yetmiyorsa

- Bazı uniler **karma kabul** ediyor: 4 bölüm ortalaması 4+ ise kabul
- TestDaF + DSH karma kombinasyonu **kabul edilmiyor** — bir tane sertifika seç
- Eksik bölümü kapatmak için **3-6 ay daha çalışma** gerekiyor

## Pratik Strateji

1. **TestDaF Übungstest** çöz — mevcut seviyeni gör
2. En zayıf bölüme **odaklan** (yazma genelde Türk öğrencilerin en zayıfı)
3. Goethe veya Lingoda **TestDaF prep kursu** al
4. **3 ay öncesinden** simülasyon sınavları çöz

Bağlantılı: [TestDaF vs DSH](/sss/dil/testdaf-ile-dsh-arasindaki-fark-nedir) · [TR'de TestDaF hazırlık](/sss/dil/turkiyede-testdafa-hazirlanan-kurum-onerisi-var-mi)
MD,

            'b1-ile-dil-kursu-vizesi-basvurusu-mumkun-mu' => <<<'MD'
**Evet, B1 ile dil kursu vizesine (§16f) başvurabilirsin** — ama tek başına yetmez, başka şartlar da var.

## Dil Kursu Vizesi Dil Şartı

- **Minimum A1-A2** seviyesi yeterli (zaten dil öğrenmeye gidiyorsun)
- **B1+ varsa daha güçlü dosya** — "ciddiyim, devam edeceğim" mesajı verir
- **Hangi sertifika?** Goethe, Telc, ÖSD — son **2 yıl içinde** alınmış

## B1'le Başvurursan Avantajları

✅ Niyet ciddi görünür — "zaten Almanca biliyor, devam etmek istiyor"
✅ Konsolosluk daha rahat kabul eder (özellikle 2024+ sıkı dönemde)
✅ Almanya'ya geldiğinde **B2/C1 kursuna direkt** başlarsın
✅ 6-12 ay sonunda **TestDaF/DSH'a hazırlık tamam** — uni başvurusuna geçebilirsin

## B1 + Sperrkonto + Niyet Belgesi

Standart paket:
- B1 sertifikası
- Sperrkonto (kurs süresi × 992 €)
- Sigorta
- Yoğun (haftalık 18+ saat) **6 ay+ kurs kaydı**
- Akredite dil okulu (DSV listesinde)
- Mantıklı motivasyon mektubu

## "Üniversite okumak için niyet" Tuzakları

Konsolosluk dikkat ediyor:
- Eş zamanlı şartlı uni kabul belgen varsa → "neden direkt §16b ile değil?"
- Niyet mektubunda "sonra uni" yazıyorsan → §16b başvurusu öneriliyor
- Hesap yetersiz → finansman şüphesi

## §16f Yerine §16b Daha Mantıklı Olduğu Durumlar

Eğer master/lisans hedefin varsa:
- **Şartlı kabul mektubu** al → § 16b (akademik vize) başvurusu
- §16b daha kolay kabul ediliyor
- §16b süre olarak daha uzun (1-2 yıl, uzatılabilir)
- §16b'da Almanya'da DSH/TestDaF kursuna katılabilirsin

## Pratik Tavsiye

> B1+ varsa **akademik bir yön düşün**: ya uni başvurusu yap (kabul gelirse §16b), ya da Studienkolleg başvur. § 16f çoğu zaman gereksiz ek adım.

Bağlantılı: [Dil kursu vizesi nasıl alınır?](/sss/vize/dil-kursu-vizesi-basvurusu-nasil-yapilir) · [Sperrkonto + dil vizesi](/sss/vize/sperrkonto-actirsam-bile-dil-kursu-vizesi-alabilir-miyim)
MD,

            'almanca-ogrenmek-icin-en-hizli-yol-nedir' => <<<'MD'
Almanca'da **A1 → B2 hedefiniz**, yoğun çalışmayla **6-12 ayda** mümkün. C1 için **12-18 ay**. "En hızlı" demek **günlük zaman + kaliteli kaynak** demek.

## Realist Zaman Tahmini

| Hedef | Yoğun (4-6 saat/gün) | Standart (1-2 saat/gün) |
| --- | --- | --- |
| A1 → A2 | 1-2 ay | 3-4 ay |
| A2 → B1 | 1.5-2.5 ay | 4-6 ay |
| B1 → B2 | 2-3 ay | 6-8 ay |
| B2 → C1 | 3-5 ay | 9-12 ay |

## En Hızlı 4 Strateji

### 1. Yoğunlaştırılmış Dil Okulu (Türkiye veya Almanya)
- **Goethe/DeutschAkademie/Yerel** — günlük 4-5 saat
- A1 → B2: ~6-8 ay
- Maliyet: 200-500 €/ay TR; 800-1500 €/ay DE

### 2. Bireysel Hoca + Self-Study Kombinasyonu
- Haftalık 4-5 saat 1:1 hoca (italki, Preply)
- Günlük 1-2 saat self-study (kitap, app, podcast)
- A1 → B2: ~8-10 ay
- Daha esnek

### 3. Almanya'da Yaşayarak Öğrenme
- Studienkolleg veya yoğun dil kursu vizesi
- **Sürekli Almanca maruz kalma** → en hızlı pratik
- A1 → C1: ~12-15 ay (B2'ye hızla, C1'e dikkatli)

### 4. Tam Yoğunlaştırılmış Yaz Kampı (3 ay)
- Goethe Intensive (~1200 €/3 hafta)
- Bir seviye atlatır

## Günlük Pratik Plan (Orta Tempo)

| Aktivite | Süre |
| --- | --- |
| Kelime ezberi (Anki/Quizlet) | 20 dk |
| Dilbilgisi kitabı (Hueber, Klett) | 30 dk |
| Hoca dersi (haftalık 2-3 kez) | 60 dk |
| Almanca dizi/podcast (Easy German) | 45 dk |
| Yazma egzersizi (essay, mektup) | 30 dk |
| **Toplam** | **~3 saat/gün** |

## Kaynak Önerileri

📚 **Kitap:** "Studio d", "Schritte international", "Aspekte Neu"
🎧 **Podcast:** Slow German, Easy German, Deutsch lernen
📺 **Dizi:** Dark, Babylon Berlin, Tatort (orta seviye)
📱 **App:** Anki, DeutschAkademie, DW Learn German (ücretsiz)
🌐 **Site:** DW Deutsch Lernen, Lingolia, Mein Deutschbuch

## Hızı Düşüren Tuzaklar

❌ Sadece app'le öğrenmek (konuşma pratiği eksik)
❌ İngilizce-Almanca çeviri (TR'den direkt öğren)
❌ Sadece dilbilgisi (konuşma/dinleme paralel ilerlemeli)
❌ Tutarsız çalışma (haftada 1 gün 5 saat → 7 gün 45 dakikadan kötü)

> **Anahtar prensip:** Tutarlı günlük çalışma > arada yoğun spurts.

Bağlantılı: [Online vs yüz yüze](/sss/dil/online-almanca-kursu-mu-yuz-yuze-mi-daha-verimli)
MD,

            'goethe-b1-sertifikasi-buyukelcilik-tarafindan-kabul-edilir-mi' => <<<'MD'
**Evet** — Goethe-Zertifikat B1, Almanya konsolosluğu/büyükelçiliği tarafından kabul edilen geçerli bir dil belgesi.

## Hangi Vize Türünde Kabul Edilir?

| Vize tipi | Goethe B1 yeterli mi? |
| --- | --- |
| **Dil kursu vizesi (§16f)** | ✅ Yeterli, hatta güçlü dosya |
| **Studienkolleg vizesi** | ✅ Yeterli (bazı eyaletlerde B2 ister) |
| **Akademik vize §16b — şartlı kabul** | ✅ Vize için yeter, uni için ek şart olabilir |
| **Akademik vize §16b — kesin kabul** | ⚠️ Yetmez, **C1** gerekir |
| **Üniversite okumaya başlama** | ❌ B1 yetmez (DSH-2/TestDaF 4 gerekli) |
| **Aile birleşimi vizesi (eş)** | ✅ Yeterli (A1 zorunlu, B1 güçlü) |

## Goethe B1 Özelikleri

- **Süresiz geçerli** (Goethe sertifikaları "lifelong")
- 4 modül: okuma, dinleme, yazma, konuşma
- Her modülden **60% geçer not**
- **Modül modül girilebilir** — tüm sınavı tek seferde tamamlamak zorunda değilsin
- Ücret: ~180 € (full set), modül başı ~50 €

## "Hörende kaldım" Senaryosu

Yaygın bir durum: 3 modül geçtin, dinleme (Hörverstehen) kaldın.

Çözüm:
1. **Sadece dinleme modülüne tekrar gir** — ücret ~50 €
2. Diğer 3 modül **2 yıl geçerli** (Goethe kısmi sertifika kuralı)
3. Tekrar geçince **birleşik sertifika** verilir

> Konsolosluk **2 yıl içinde tüm modüllerin tamamlanmasını** ister — kısmi sertifika tek başına yetmez, sadece tam B1 sertifikası kabul edilir.

## Pratik Tavsiye

- Sınava girmeden önce **simülasyon sınavlarını** çöz (Goethe Institut web sayfasında ücretsiz)
- Dinleme zorlanıyorsan → **günlük 30 dk podcast** dinle (Slow German, Easy German)
- Sınav öncesi **son 2 hafta yoğun simulation**

## Goethe B1 vs Diğer B1

| Sertifika | Konsolosluk kabulü |
| --- | --- |
| **Goethe B1** | ✅ %100 kabul |
| **telc Deutsch B1** | ✅ %100 kabul |
| **ÖSD Zertifikat B1** | ✅ %100 kabul |
| **DSD B1 (lise diploması)** | ✅ Kabul (Türk PASCH okul mezunları için) |
| **TestDaF B1** | ❌ TestDaF B2+ verir, B1 yok |

Bağlantılı: [Almanca dil sınavı ücretleri](/sss/dil/almanca-dil-sinavi-ucretleri-ne-kadar) · [B1 dil kursu vizesi](/sss/dil/b1-ile-dil-kursu-vizesi-basvurusu-mumkun-mu)
MD,

            'dsh-hangi-seviyede-yeterli-kabul-edilir-dsh-2-mi-dsh-3-mu' => <<<'MD'
**DSH-2 standart kabuldür** — Alman üniversitelerinin büyük çoğunluğu için yeterli. DSH-3 sadece bazı programlarda istenir.

## DSH Seviyeleri Ne Anlama Geliyor?

| Seviye | Skor | CEFR eşdeğeri |
| --- | --- | --- |
| **DSH-1** | 57-66% | B2 (yetersiz) |
| **DSH-2** | 67-81% | C1 (standart) |
| **DSH-3** | 82-100% | C1+ / C2 |

## Hangi Programlar Hangi Seviyeyi İstiyor?

| Program | Şart |
| --- | --- |
| **Mühendislik (lisans+master)** | DSH-2 |
| **Fen bilimleri** | DSH-2 |
| **İşletme, ekonomi** | DSH-2 |
| **Sosyal bilimler** | DSH-2 (bazıları DSH-3) |
| **Tıp** | **DSH-3** (çoğu uni) |
| **Hukuk** | **DSH-3** (zorunlu) |
| **Almanca filolojisi, edebiyat** | DSH-3 |
| **Sanat, tasarım** | DSH-1 yeterli (uniye göre) |

## DSH-1 Yetmediği Halde Üniversite Şartına Vakti Olmayanlar

Bazı uniler **DSH-1 + ek dil kursu** kabul ediyor:
- DSH-1 ile kayıt
- İlk dönem üniversite içi Almanca dersi
- 2. dönem sonunda DSH-2 yeniden gir
- Geçemezsen kayıt iptal

Bu yol seçeneğini her uni vermiyor — başvuru öncesi sorgula.

## DSH-2 mi DSH-3 mü Hedeflemeli?

**Lisans/master için DSH-2 yeter** (mühendislik, fen, çoğu sosyal). **Yedekte DSH-3** olması:

- Hukuk veya tıp düşünüyorsan
- Yüksek prestijli uniye gidiyorsan (LMU, Heidelberg, TU München sosyal)
- Akademik kariyer planlıyorsan (PhD'ye geçiş için)

## DSH Türkiye'de Neden Yok?

DSH **üniversiteye özel** bir sınav — TestDaF gibi merkezi değil. Her uni kendi DSH'sını düzenler. Bu yüzden:

- Türkiye'den DSH'a giremiyorsun
- Sadece hedef üniversitenin Studienkolleg'i veya dil kursunda
- Önce TestDaF al → uni'ye git → orada gerekirse DSH'a gir

## TestDaF vs DSH Eşdeğerlik

Çoğu uni iki sınavı eşdeğer kabul eder:

| TestDaF | DSH |
| --- | --- |
| TDN 4 her bölüm | DSH-2 |
| TDN 5 her bölüm | DSH-3 |
| TDN 3 ortalama | DSH-1 |

Bağlantılı: [TestDaF vs DSH](/sss/dil/testdaf-ile-dsh-arasindaki-fark-nedir)
MD,

            'online-almanca-kursu-mu-yuz-yuze-mi-daha-verimli' => <<<'MD'
**Hibrit (karma) yaklaşım en verimli** — saf online ya da saf yüz yüzeye göre. Hangisi senin için daha iyi, hedefe ve disiplinine bağlı.

## Karşılaştırma Tablosu

| Açı | Online | Yüz Yüze |
| --- | --- | --- |
| **Maliyet** | Düşük (50-300 €/ay) | Yüksek (200-1200 €/ay) |
| **Esneklik** | Çok yüksek — saat seçimi | Sabit program |
| **Konuşma pratiği** | Sınırlı (Zoom) | Yüksek (gerçek diyalog) |
| **Disiplin gereksinimi** | Çok | Az (sınıf yapısı motive) |
| **Sosyal motivasyon** | Az | Yüksek (sınıf arkadaşları) |
| **Network/arkadaşlık** | Az | Yüksek |
| **Telafi imkanı** | Esnek | Sınırlı |
| **Almanya'ya gitmeden** | ✅ Türkiye'den | ❌ Almanya'da |

## Online Daha İyi: Şu Durumlarda

✅ **A1-A2 başlangıç** — dilbilgisi ve kelime, video ile etkili
✅ **Çalışıyorsun, esneklik şart**
✅ **Bütçe sıkı** — DeutschAkademie ücretsiz
✅ **Disiplinli öğrencisin** — kendin program yapabilirsin
✅ **Aralıklı çalışman gerekli** (sınavlar, iş)

## Yüz Yüze Daha İyi: Şu Durumlarda

✅ **B1-B2 ve sonrası** — konuşma pratiği kritik
✅ **TestDaF/DSH'a hazırlanıyorsun** — sınav simulasyonu yüz yüze daha gerçekçi
✅ **Disiplin sorunu yaşıyorsun** — sınıf zorunlu seni motive ediyor
✅ **Türk arkadaş çevresi istiyorsun** — Goethe Institut yüz yüze sınıflar
✅ **Almanya'dasın zaten** — yerel dil okuluna git

## Hibrit Plan (Önerilen)

| Faz | Yaklaşım |
| --- | --- |
| **A1-A2 (3-6 ay)** | Online ağırlıklı + haftalık 1 yüz yüze veya 1:1 italki |
| **B1 (3-4 ay)** | Online + 2-3 yüz yüze ders/hafta |
| **B2 (3-4 ay)** | Yoğun dönem — yüz yüze kurs + online destek |
| **C1 (4-6 ay)** | Yüz yüze yoğun + sınav prep (Goethe gibi) |

## Konuşma Pratiği Açığını Kapatma

Online ağırlıklı çalışıyorsan:
1. **italki tandem partner** bul — ücretsiz, karşılıklı dil değişimi
2. **Discord Deutsch grupları** — sürekli konuşma
3. **Meetup grupları** — TR'de Goethe Stammtisch akşamları
4. **Almanya yaz okulu** — DAAD Sommerkurse (3-4 hafta yoğun)

## Pratik Karar

> **Bütçe esas kriter mi?** Online + ücretsiz kaynaklarla başla, B1+ olunca yüz yüze geçiş yap.
> **Zaman ve disiplin sorun mu?** Yüz yüze kurs al, sıkıntıyı sınıf yapısı çözsün.

Bağlantılı: [Goethe online kursu](/sss/dil/goethe-online-kursu-nasil-tavsiye-eder-misiniz) · [Online hoca önerisi](/sss/dil/online-ders-veren-almanca-ogretmeni-onerisi-var-mi)
MD,

            'turkiyede-testdafa-hazirlanan-kurum-onerisi-var-mi' => <<<'MD'
Türkiye'de TestDaF hazırlık kurslarını **3 farklı tipten** alabilirsin: kurum, bireysel hoca veya kendin hazırlanma.

## Tanınmış Kurumlar

### 1. Goethe Institut (Türkiye)
- İstanbul, Ankara, İzmir
- TestDaF prep kursu: ~600-900 €/8 hafta
- Sertifikalı öğretmenler, orijinal materyal
- Goethe Institut sınav merkezi → simülasyon dahil

### 2. Yunus Emre Enstitüsü
- TestDaF'a değil ama C1 hazırlık var
- Ücretsiz veya düşük ücretli
- Yetersiz spesifik TestDaF prep

### 3. Özel Dil Okulları
| Okul | Şehir | TestDaF prep |
| --- | --- | --- |
| **Dilset** | İstanbul, Ankara | ~3000-5000 ₺/8 hafta |
| **Inlingua** | İstanbul, Ankara | Var |
| **Anadolu Üniversitesi (AKİS)** | Eskişehir | Ücretsiz uzaktan dil dersleri |
| **İstanbul Üni / Yıldız TÜ** | İstanbul | Bazı dönem TestDaF prep açar |

### 4. Online Hibrit
- **Lingoda C1 + TestDaF prep** modülleri
- **Italki / Preply hoca** + Lingoda hibrit
- **DeutschAkademie online** (ücretsiz, kendi tempon)

## Bireysel Hocalar

Türkiye'de TestDaF spesifik hoca bulmak için:
- italki.com — "TestDaF" filtre
- Sahibinden / sosyal medya — yerel TR Almanca hocalar
- Goethe Institut'taki öğretmenlerin bireysel ders verme imkanı
- Üniversite Almanca bölümü mezunları

Ortalama ücret: **150-400 TL/saat** (deneyime göre)

## Kendi Başına Hazırlık (Ücretsiz)

Resmi TestDaF materyalleri:
- **TestDaF Übungstest** — 5 deneme sınavı, https://www.testdaf.de
- **Modellsatz** — gerçek sınav formatları
- **Hueber Werkstatt B2/C1** kitapları
- **YouTube:** "TestDaF prep" kanalları (Easy German Deutsch, vb.)

## Önerilen Yol

Bütçe iyiyse:
1. **Goethe Institut TestDaF kursu** (~2 ay yoğun) +
2. **Haftalık 1-2 italki hoca** (konuşma pratiği) +
3. **Her hafta 1 simülasyon sınavı** (kendin)

Bütçe sıkı ise:
1. **DeutschAkademie ücretsiz online** +
2. **TestDaF Übungstest** öz değerlendirme +
3. **Sınav öncesi 2-3 italki ders** (zayıf bölüm için)

## Hazırlık Süresi

| Mevcut seviye | Hedef | Süre |
| --- | --- | --- |
| B1 | TDN 4 | 6-9 ay |
| B2 | TDN 4 | 3-5 ay |
| C1 | TDN 5 | 2-3 ay |

## Türkiye'de TestDaF Nerede Girilir?

İstanbul, Ankara ve İzmir'deki **Goethe Institut'ları** TestDaF merkezi. Online TestDaF girişi (TestDaF auf Wunsch) henüz Türkiye'de yaygın değil — ama bazı tarihlerde mevcut.

Bağlantılı: [TestDaF her bölümden kaç almak gerekli?](/sss/dil/testdaf-her-bolumden-en-az-kac-almak-gerekiyor)
MD,

            'almanca-dil-sinavi-ucretleri-ne-kadar' => <<<'MD'
Almanca dil sınavı ücretleri **sınav türüne, seviyeye ve düzenleyici kuruma** göre değişir. Türkiye'de 2026 itibariyle yaklaşık fiyatlar:

## Goethe Institut Sınavları

| Seviye | Ücret (TR) | Süre |
| --- | --- | --- |
| Goethe A1 | ~110 € | ~1.5 saat |
| Goethe A2 | ~140 € | ~2 saat |
| Goethe B1 | ~180 € | ~2.5 saat |
| Goethe B2 | ~210 € | ~3 saat |
| Goethe C1 | ~250 € | ~3.5 saat |
| Goethe C2 | ~280 € | ~4 saat |

**Modül başı sınav:** ~50-70 € (sadece yazma, sadece konuşma vs.)

## TestDaF

- Tüm sınav: ~**195 €**
- Sadece eksik bölüme tekrar: ~**195 €** (bütün set fiyatı, modül indirimi yok)

## DSH

- **Üniversiteye göre değişir**: 50-150 € arası
- Bazı uniler kendi başvuranları için **ücretsiz**
- Diğer uniler için ödeme yapmak gerekiyor

## Telc Sınavları

| Seviye | Ücret |
| --- | --- |
| telc B1 | ~120-150 € |
| telc B2 | ~140-180 € |
| telc C1 Hochschule | ~150-200 € |
| telc C2 | ~180-220 € |

## ÖSD (Avusturya Sertifikası)

| Seviye | Ücret |
| --- | --- |
| ÖSD A1-A2 | ~100-130 € |
| ÖSD B1-B2 | ~140-170 € |
| ÖSD C1 | ~180-220 € |

## Karşılaştırma: Hangi Sınav Ne Kadar Ucuz?

🥇 **En ucuz C1 sertifikası:** telc C1 Hochschule (~150 €)
🥇 **En ucuz B2:** Goethe B2 modül modül (~140 €)
🥇 **TestDaF tam sınav:** ~195 € (modül başı tekrar mümkün)

## Ekstra Masraflar

⚠️ Bunlara dikkat:
- **Sertifika postası** — ~10-20 € (TR adresine)
- **Tekrar girme** — tam ücret, modül indirimi sınırlı
- **Kayıt iptal** — genelde iade yok veya %50 iade
- **Hazırlık kursu** — sınav ücretinden ayrı (300-1500 €)

## Bütçe Önerisi

**Vize için B1+sertifika:**
- Goethe B1 modül modül: ~180 € + olası tekrar ~50 €
- **Toplam: ~230 €**

**Akademik için C1 sertifika:**
- TestDaF (3 ay hazırlık + sınav): ~195 € + olası tekrar ~195 €
- **Toplam: ~400 €**

**Toplam dil yatırımı (sıfırdan C1):**
- Kurs + sertifikalar + kitap + ortalama 2-3 sınav girişi
- **~2000-4000 €** (2-3 yıllık süreç)

Bağlantılı: [Goethe sınav tarihleri](/sss/dil/goethe-sinav-tarihleri-ne-zaman-duyurulur)
MD,

            'goethe-vs-telc-sertifikasi-uni-basvurusu-icin-fark-var-mi' => <<<'MD'
**Üniversite başvurusu açısından Goethe ve Telc sertifikaları büyük ölçüde eşdeğer kabul edilir** — ama bazı farklar var.

## Kabul Edilirlik

| Sertifika | Uni kabul oranı |
| --- | --- |
| **Goethe C2** | %100 |
| **Goethe C1** | %95+ |
| **TestDaF (TDN 4)** | %100 |
| **DSH-2/3** | %100 (zaten DE üniversite çıkışlı) |
| **telc Deutsch C1 Hochschule** | %85+ |
| **telc Deutsch C1** (standart) | %60-70 (uniye göre) |

## Önemli Detay: "Hochschule" Versiyonu

Telc'in **iki C1 sertifikası** var:
- **telc Deutsch C1** — genel amaçlı
- **telc Deutsch C1 Hochschule** — özellikle üniversite başvurusu için

⚠️ **"C1 Hochschule" sürümü olmalı!** Standart telc C1 bazı üniversitelerce kabul edilmiyor — özellikle TU9 ve büyük uniler.

## Fiyat ve Erişim

| Açı | Goethe | Telc |
| --- | --- | --- |
| Ücret (C1) | ~250 € | ~150-200 € |
| TR'de sınav merkezi | Çok yaygın | Daha az yaygın |
| Sınav tarihi sıklığı | Aylık | Daha az sık |
| Çıkış kurumu prestiji | Yüksek (Almanya devleti) | Orta |
| Modül modül girilebilir | ✅ | ✅ |
| Süresiz geçerli | ✅ | ✅ |

## Hangi Düzeyde Hangi Sınav?

| Hedef | Goethe | Telc | Alternatif |
| --- | --- | --- | --- |
| Vize için B1 | Goethe B1 | telc B1 | ÖSD B1 |
| Şartlı kabul B2 | Goethe B2 | telc B2 | ÖSD B2 |
| Üniversite C1 | Goethe C1 | telc C1 **Hochschule** | TestDaF |
| Hukuk/Tıp C1+ | Goethe C2 | — | TestDaF TDN 5 |

## Hangisini Seçmelisin?

✅ **Goethe** seç:
- Prestij ön planda
- Bütçe yeterli
- Her uniye başvurursam diye risk almak istemiyorum

✅ **Telc C1 Hochschule** seç:
- Bütçe sıkı
- Hedef uni telc'i kabul ediyor (önceden sorgula)
- Goethe sınav tarihi uymuyor

## Pratik İpucu

Başvuru yapacağın **uni'nin web sitesinde** (Studienberatung/Bewerbung sayfası) hangi sertifikaları kabul ettiği listelenir. **Önce kontrol et, sonra sınava gir** — yanlış sertifikaya 200 € yatırma.

> "TestDaF en güvenli yoldur" — eğer hangi uniye gideceğini henüz bilmiyorsan TestDaF al, her yere geçer.

Bağlantılı: [Almanca C1 hangi sınavla?](/sss/dil/almanca-c1-hangi-sinav-ile-kanitlanir) · [TestDaF vs DSH](/sss/dil/testdaf-ile-dsh-arasindaki-fark-nedir)
MD,

            'b2-icin-online-birebir-hoca-tavsiye-eder-misiniz' => <<<'MD'
**Evet, B2 seviyesinde online 1:1 hoca çok verimli olabilir** — özellikle konuşma ve yazma pratiği için.

## Neden B2'de 1:1 Hoca Etkili?

B2 seviyesi **kendi kendine ilerlemenin zorlaştığı** seviye. Grup dersi yetersiz kalır çünkü:
- Konuşma süren sınırlı
- Hata düzeltimi geç ve yüzeysel
- Senin spesifik zayıf konularına odaklanılmaz

1:1 hoca:
- Tam senin tempon
- Anlık hata düzeltme
- Senin için **özel müfredat**
- Sınava odaklı çalışma mümkün

## Platform Seçimi

| Platform | B2 hoca saatlik | Avantaj |
| --- | --- | --- |
| **italki** | 12-25 € | En geniş seçim, deneme dersi 5-10 € |
| **Preply** | 15-30 € | Iyi müsaitlik filtreleme |
| **Lingoda Private** | ~25 €/saat | Standart müfredat, sertifika |
| **Goethe Institut Bireysel** | 60-90 € | En kaliteli ama pahalı |

## "İyi Hoca" Nasıl Bulunur?

✅ **Native German speaker** (Muttersprachler)
✅ Almanya'da yaşıyor veya yaşamış
✅ B2-C1 sınavlarına aşina (TestDaF, telc, Goethe)
✅ Akademik backround (üni mezunu, öğretmen sertifikalı)
✅ Türkçe **anlıyor** olması bonus (zayıf noktaları Türkçe açıklayabilir)

⚠️ Profil görünce dikkat et:
- ❌ Çok düşük fiyat (kalite düşük olabilir)
- ❌ Sadece "Konuşma pratiği" tanıtımı (yapılandırılmış öğretim yok)
- ❌ Hiç değerlendirme yok (yeni hoca, risk)

## Önerilen Plan (B2 → C1)

**Haftalık 2-3 ders, 45-60 dk:**

| Gün | İçerik |
| --- | --- |
| Pazartesi | Yazma egzersizi + düzeltme |
| Çarşamba | Konuşma pratiği (free + structured) |
| Cumartesi | Dilbilgisi gözden geçirme + sınav formatı |

**Ders arası kendi çalışma (haftalık ~5-6 saat):**
- Anki kelime
- Almanca dizi/podcast
- Yazma egzersizi (essay)

**3-4 ay sonunda B2 → C1 mümkün.**

## "Sadece Konuşma" Pratiği Yetersiz

B2'de sadece konuşmaya odaklanmak zayıf bir stratejidir. Çünkü:
- Konuşma pratiği var ama hata düzeltme/yapılandırma yoksa **fosil hatalar** yerleşir
- Yazma pratiği şart (sınavlarda yazma kritik)
- Dilbilgisi B2'de derinleşiyor (Konjunktiv II, Passiv, vs.)

## Türkçe Konuşan Native German Hocalar

italki/Preply'de filtre kullan:
- "Native German" + "Speaks Turkish"
- "Almanya'da yaşayan Türk hocalar" — anadili Türkçe ama Almancası C2

İkinci grup orta seviyede çok yardımcı olabilir — zayıf noktaları **Türkçe açıklayabilir**.

## Bütçe Planı

| Düzey | Aylık masraf |
| --- | --- |
| 1 ders/hafta | ~80-120 € |
| 2 ders/hafta | ~150-220 € |
| 3 ders/hafta | ~230-330 € |
| **Önerilen (B2→C1 hızlı):** 2-3 ders/hafta | **200-300 €/ay × 4 ay** |

> Bu yatırım ucuz kursa kıyasla daha yüksek ama **hız 2-3 katı**.

Bağlantılı: [Online hoca önerisi](/sss/dil/online-ders-veren-almanca-ogretmeni-onerisi-var-mi) · [Goethe online kursu](/sss/dil/goethe-online-kursu-nasil-tavsiye-eder-misiniz)
MD,
        ];
    }
}

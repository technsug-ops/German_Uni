<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

/**
 * Curated answers for PARA (14) + SIGORTA (15) + BURS (15) = 44 questions.
 * Focus: Sperrkonto, transferler, yaşam maliyeti, sağlık sigortası, burs başvurusu.
 */
class FaqAnswersParaSigortaBursSeeder extends Seeder
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
        return array_merge(
            $this->paraAnswers(),
            $this->sigortaAnswers(),
            $this->bursAnswers(),
        );
    }

    private function paraAnswers(): array
    {
        return [
            'sperrkonto-bloke-hesap-icin-ne-kadar-para-gerekli' => <<<'MD'
2026 için Almanya öğrenci vizesi gerekli **Sperrkonto** (bloke hesap) tutarı: **yıllık 11,904 €** (aylık 992 €). Bu rakam her yıl güncelleniyor.

## Yıllık Karşılaştırma

| Yıl | Yıllık tutar | Aylık |
| --- | --- | --- |
| 2024 | 11,208 € | 934 € |
| 2025 | 11,904 € | 992 € |
| 2026 | 11,904 € | 992 € (yıl ortasında güncellenebilir) |

## Süre Bazlı Hesaplama

- **12 aylık vize:** 11,904 €
- **6 aylık dil kursu vizesi:** 5,952 €
- **2 yıllık vize (uzun süreli):** Genelde ilk yıl tutarı yeter, sonra yenileme.

## Tutarı Düşürme Yöntemleri

1. **Burs alıyorsan:** Burs tutarı aylık 992 €'yu karşılıyorsa Sperrkonto gerekmez (burs taahhüt belgesi gönder).
2. **Aile garantörü (Verpflichtungserklärung):** Almanya'da yaşayan akraban 6 aylık ortalama net 2,200 €+ geliri ile imza atarsa muafiyet mümkün.
3. **Burslu + kısmi Sperrkonto:** Burs aylık tutarı düşükse fark kadar Sperrkonto yatırılır.

## Hesap Nereye Açılır?

- **Fintiba** (en yaygın) — *fintiba.com*, online açma, 89 € yıllık.
- **Expatrio** — *expatrio.com*, 49 €, sigorta paketi dahil.
- **Coracle**, **Sutor Bank**, **Deutsche Bank** alternatif.

## Para Çekme

- Almanya'ya geldikten sonra Sperrkonto aktive olur.
- **Aylık 992 €** çekebilirsin, fazlasını çekemezsin (ay kayıp ederse birikir).

İlgili: [Fintiba'ya transfer](/sss/para/turkiyeden-almanyaya-para-transferinde-en-uygun-banka-hangisi).
MD,

            'turkiyeden-almanyaya-para-transferinde-en-uygun-banka-hangisi' => <<<'MD'
Sperrkonto'ya 11,904 € yatırmak için en az komisyonlu yöntemler:

## Karşılaştırma (2026)

| Yöntem | Komisyon | Hız | Notlar |
| --- | --- | --- | --- |
| **Wise (TransferWise)** | %0.4-0.7 + 5-15 € | 1-2 gün | En şeffaf kur, kredi kartı/IBAN |
| **Revolut Premium** | %0 (Premium üyelik 7.99 €/ay) | Aynı gün | Limit 100K €/ay |
| **Garanti BBVA Havale** | ~%1.5-2.5 + SWIFT 35 € | 1-3 gün | Kur şube oranı |
| **Ziraat Havale** | %1.5-2 + 30 € SWIFT | 2-4 gün | Devlet bankası güveni |
| **İş Bankası Havale** | %1.5-2 + 35 € SWIFT | 2-4 gün | İyi destek |
| **Western Union** | %3-5 | Aynı gün | Kötü kur, pahalı |
| **Currency exchange + döviz** | %1-2 | Aynı gün | Bizzat şube |

## En Uygun Yöntem: Wise

✅ **11,904 € transferinde:**
- Wise: ~50-80 € toplam komisyon
- Banka SWIFT: ~250-400 € toplam (kur farkı dahil)

✅ **Avantajları:**
- Anlık kur (Google Finance kuru)
- IBAN'a direkt yatırır
- Şeffaf — hangi komisyonu ödediğini görüyorsun

⚠️ **Wise dezavantaj:** İlk büyük transfer (10K+ €) için kimlik doğrulama 2-3 gün sürebiliyor.

## Strateji

1. **Wise'a kayıt ol** (önce küçük tutarla test transferi yap).
2. Türkiye'deki bankan üzerinden Wise hesabına IBAN ile gönder.
3. Wise → Sperrkonto IBAN'a gönder.
4. SWIFT bilgileri: Wise'ın verdiği BIC ve IBAN.

## Önemli Notlar

⚠️ Türkiye'de **döviz çıkış vergisi yok** (kişisel transfer < 50K USD/yıl).
⚠️ Wise/Revolut'ta **TL → EUR konversion** spread'i sabit (~%0.4).
⚠️ Sperrkonto'ya yatırılan paranın **kaynağı belli olmalı** — banka ekstresi/maaş bordrosu hazırla, vergi denetimi durumunda.

İlgili: [Wise/Revolut karşılaştırma](/sss/para/wise-veya-revolut-turkiye-almanya-transferi-icin-uygun-mu).
MD,

            'fintibaya-para-transferinde-swift-sube-bilgisi-sorun-yaratir-mi' => <<<'MD'
**Hayır, doğru SWIFT bilgisi verirsen sorun olmaz.** Yanlış SWIFT/BIC kodu yazmak para geri gönderme veya 4-7 gün gecikmeye sebep olur.

## Fintiba SWIFT/IBAN Bilgileri

Fintiba'nın 2 partner bankası var, hesap açtığında bana hangi olduğu yazılı gelir:

### Solarisbank (en yaygın)
- **BIC:** SOBKDEBBXXX
- **IBAN:** DE... ile başlar (sana özel)
- Banka adresi: Cuvrystraße 53, 10997 Berlin

### Sutor Bank
- **BIC:** SUTODEH1XXX
- **IBAN:** DE... ile başlar
- Banka adresi: Hermannstraße 46, 20095 Hamburg

## Türkiye'den Gönderirken

Bankaya verilecek bilgi:
1. **Alıcı adı (Begünstigter):** Senin adın soyadın (Fintiba hesap sahibi)
2. **IBAN:** Fintiba'nın sana verdiği IBAN
3. **BIC/SWIFT:** Yukarıda
4. **Açıklama (Verwendungszweck):** "Sperrkonto" yazılır (zorunlu değil, ama önerilir)
5. **Banka adresi:** Yukarıda
6. **Şehir:** Berlin veya Hamburg (BIC'e göre)

## "Şube bilgisi" Sorulduğunda

Türk bankaları formda **şube kodu/adresi** ister. Almanya'da banka şubesi (Filiale) kavramı SWIFT için kullanılmaz — sadece **banka adı + BIC + IBAN + banka adresi** yeter.

✅ **Şube kodu boş bırak** (veya "Hauptsitz" / "Headquarters" yaz)
✅ **Banka adresi:** Yukarıdaki adresi yaz
✅ **Şehir:** BIC'in 5-6. karakteri (DE = Deutschland, BB = Berlin, H1 = Hamburg)

## Yaygın Hatalar

❌ BIC'e tek harf eksik yazmak → para 7-10 gün dolaşır, geri gelir
❌ IBAN'da harf yerine sayı yazmak → otomatik red
❌ Alıcı adı pasaport adından farklı → uyumsuzluk, transfer iptal
❌ "Açıklama" boş bırakmak → Fintiba transferi geç işleyebilir (önerilir: "Sperrkonto YOUR_NAME")

## Yardım

- Şüphen varsa Fintiba'ya **chat support** üzerinden hesabın SWIFT bilgilerini istetip ekran görüntüsü al.
- Türk bankan SWIFT formatını anlamıyorsa **HSBC, Garanti, İş Bankası şubelerine** git — daha tecrübeli.
MD,

            'bloke-hesaptan-para-cekmek-icin-turkiyede-hesap-actirmak-gerekiyor-mu' => <<<'MD'
**Hayır, gerekmez.** Sperrkonto'dan para çekmek için sadece **Almanya'da bir cari hesap (Girokonto)** yeterli. Türkiye hesabı gerekmiyor.

## Süreç

1. Almanya'ya gel, **Anmeldung** yap.
2. Almanya'da **Girokonto** aç (N26, Commerzbank, Deutsche Bank, Postbank, Sparkasse, ING).
3. Fintiba/Expatrio panelinden **"Auszahlung"** (ödeme) talep et.
4. Hangi Girokonto'ya gönderileceğini gir (IBAN).
5. Aylık **992 €** otomatik çıkıp Girokonto'na geçer.

## Girokonto Seçimi (Öğrenciler İçin)

| Banka | Aylık ücret | Avantaj |
| --- | --- | --- |
| **N26** | 0 € | Online, hızlı, İngilizce |
| **Commerzbank Studierende** | 0 € | Geleneksel, ATM bol |
| **ING Direkt** | 0 € | Online, faiz veriyor |
| **Postbank Giro Plus** | 0 € (öğrenci) | Posta + banka, esnek |
| **Deutsche Bank Studierende** | 0 € (öğrenci) | Prestij + uluslararası transfer kolay |
| **Sparkasse** | 2-5 € | Yerel, her köşede şube |

## Önemli Notlar

⚠️ **Sperrkonto kendisi cari hesap DEĞİL** — para çekemezsin, sadece transfer alabilirsin.
⚠️ Para çıkışı **otomatik** (aylık 992 €) — eğer otomatik değilse panelinden ayarlanır.
⚠️ Türkiye'den gönderilen paranın **döviz çıkış kanıtı** banka ekstresi olarak korunmalı (Almanya'da vergi denetimi durumunda).

## Türkiye'den Ek Para Gönderme

Sperrkonto'daki 992 €/ay yetmiyorsa, ailenden Türkiye'den **direkt Girokonto'na** transfer alabilirsin. Wise/Revolut/banka SWIFT — Sperrkonto'ya değil.

⚠️ **Sperrkonto'ya yıl boyunca ek para yatırmak gerekmez** — vize için sadece başlangıçta 11,904 € yeter.

İlgili: [Bloke hesap kapanırsa](/sss/para/bloke-hesap-kapali-kalirsa-aylik-kesinti-devam-eder-mi) | [N26 vs banka](/sss/para/postbank-deutsche-bank-n26-ogrenci-icin-en-uygun-banka).
MD,

            'bloke-hesap-aile-bireyleri-icin-ayri-miktar-mi-isteniyor' => <<<'MD'
**Evet, her aile bireyi için ayrı Sperrkonto açılır.** Tek bir hesap aile bütününü kapsamaz.

## Senaryolar

### 1. Eş ile beraber gidiyorsanız
- **Sen (öğrenci):** 11,904 € Sperrkonto (öğrenci vizesi için)
- **Eş (refakatçi):** Aile birleşimi vizesi gerekiyor → eş için ayrı Sperrkonto **8,388 €/yıl** (700 €/ay yaklaşık) veya alternatif gelir kanıtı
- Eğer eş çalışıyorsa Almanya'da → maaş/iş sözleşmesi Sperrkonto yerine geçer

### 2. Çocuk ile beraber
- **Çocuk başına ek tutar:** ~6,000-8,000 €/yıl (yaşa göre değişir, 14 yaş altı/üstü ayrımı)
- Sperrkonto'da çocuk için **ayrı hesap** veya **Verpflichtungserklärung** (Almanya'da yaşayan akraba garantörü)
- Çocuğa **Kindergeld** başvurusu Almanya'ya geldikten sonra yapılır (aylık 250 €)

### 3. Tek başına gidiyorsun, aileyi sonra getireceksin
- Sadece **kendi Sperrkonto'n** (11,904 €) yeter
- Aile birleşimi sonra başvurulduğunda **o tarihteki tutarlarla** ek Sperrkonto açılır

## Toplam Maliyet Örneği (Aile)

| Kişi | Yıllık tutar |
| --- | --- |
| Öğrenci (sen) | 11,904 € |
| Eş (çalışmıyorsa) | 8,388 € |
| Çocuk (14 yaş altı) | 6,036 € |
| **Toplam** | **26,328 €** |

⚠️ Bu yüksek tutar nedeniyle aile birleşimi çoğu öğrenci için **vize geldikten + iş bulunduktan sonra** yapılır.

## Alternatifler

1. **Verpflichtungserklärung:** Almanya'da yaşayan akraban (Schwager, Tante, vs.) finansal garanti imzalar.
2. **Burs:** DAAD/KAAD aile içeren burs paketleri (nadir, çok rekabetçi).
3. **Aile şirketinden iş sözleşmesi:** Eş Almanya'da Werkstudent veya remote işçi olabiliyorsa Sperrkonto azaltılır.

İlgili: [Sperrkonto tutarı](/sss/para/sperrkonto-bloke-hesap-icin-ne-kadar-para-gerekli).
MD,

            'wise-veya-revolut-turkiye-almanya-transferi-icin-uygun-mu' => <<<'MD'
**Evet, ikisi de uygun** — Wise daha yaygın, Revolut daha hızlı. Sperrkonto açma için ikisi de çalışıyor.

## Wise (TransferWise)

✅ **Avantajlar:**
- Şeffaf kur (Google Finance kuru, spread %0.4-0.7)
- Komisyon önceden gösteriliyor
- IBAN üzerinden Sperrkonto'ya direkt
- Yüksek transfer limiti yok (KYC sonrası 1M €/transfer)
- Türk bankalarından TL → EUR alınabiliyor

❌ **Dezavantajlar:**
- İlk büyük transferde **kimlik doğrulama 2-3 gün** (KYC)
- Türkiye TL göndermek için banka transferi gerekiyor (anlık değil)

## Revolut

✅ **Avantajlar:**
- Anlık transfer (uygulamadan uygulamaya)
- Premium üyelikle aylık 100K € limitli ücretsiz
- TL hesabı + EUR hesabı tek uygulamada
- Sanal kart hızlı kullanım

❌ **Dezavantajlar:**
- Standart üyelikte aylık 1,250 € limit (üstü %0.5 komisyon)
- **Premium ücretli** (7.99 €/ay, Sperrkonto için 1 ay yeter)
- Sperrkonto bazı bankaları "neo-bank" havalesini geç işleyebilir

## Sperrkonto İçin Strateji

### Senaryo A: 11,904 € tek seferde
- **Wise Premium yok gerek**, normal Wise hesabı yeter
- Türkiye banka → Wise IBAN → Sperrkonto IBAN
- Toplam komisyon: ~50-80 €

### Senaryo B: Aylık taksitli (hassas)
- Aylık 1,000 € transferi → Revolut Standart yeterli
- 1,250 € limit altında kalır, ücretsiz

## Önemli Notlar

⚠️ **Wise/Revolut → Sperrkonto direkt çalışıyor mu?** — Fintiba ve Expatrio her ikisini de kabul ediyor.
⚠️ **Açıklama (Reference):** "Sperrkonto YOUR_NAME" yaz, Fintiba transferi hızlı eşleştirir.
⚠️ **Türkiye'den döviz çıkış sınırı:** Şahıs hesabı için 50K USD/yıl serbest (vergi yok, beyan yok).

## Pratik Tavsiye

- **İlk büyük transferi Wise'la yap** (şeffaf, hesap doğrulanmış)
- Almanya'ya geldikten sonra **Revolut Premium 1 ay al** → Türk bankasından TL göndermek için.
- Ödediğin komisyonu **xe.com gibi siteler** ile kontrol et (gizli kur farkı varsa görürsün).

İlgili: [Türkiye'den para transferi](/sss/para/turkiyeden-almanyaya-para-transferinde-en-uygun-banka-hangisi).
MD,

            'almanya-ogrenci-yillik-yasam-maliyeti-ortalama-kac-euro' => <<<'MD'
Almanya'da öğrenci olarak yıllık ortalama yaşam maliyeti **11,000-15,000 €**, şehre ve yaşam tarzına göre değişir.

## Aylık Detay (2026, ortalama öğrenci)

| Kalem | Düşük şehir (Leipzig) | Orta (Berlin) | Yüksek (Münih) |
| --- | --- | --- | --- |
| **Kira (warm)** | 350 € | 520 € | 750 € |
| **Yemek** | 180 € | 220 € | 260 € |
| **Sağlık sigortası** | 130 € | 130 € | 130 € |
| **Ulaşım** | 60 € (D-Ticket) | 60 € | 60 € |
| **Telefon/Internet** | 25 € | 25 € | 25 € |
| **Kitap/eğitim** | 25 € | 30 € | 30 € |
| **Kişisel/eğlence** | 100 € | 150 € | 180 € |
| **Giyim** | 30 € | 40 € | 50 € |
| **Acil/diğer** | 50 € | 60 € | 80 € |
| **Toplam aylık** | **~950 €** | **~1,235 €** | **~1,565 €** |
| **Toplam yıllık** | **11,400 €** | **14,820 €** | **18,780 €** |

## Vize için Resmi Tutar

- 2026 Sperrkonto: **11,904 €/yıl** (aylık 992 €)
- Bu resmi minimum kabul ediliyor, ama Berlin/Münih için yetmiyor — ek finansman gerek.

## Bütçeyi Düşürme Yolları

✅ **Studierendenwerk yurdu:** Aylık 200-300 € tasarruf
✅ **Mensa'da yemek:** Aylık 80-120 € tasarruf
✅ **D-Ticket (49 €):** Aylık 30-50 € tasarruf (ulaşım+seyahat)
✅ **WG-Hesap (Wohngemeinschaft):** Tek kira yerine paylaşımlı
✅ **Werkstudent (haftalık 20 saat):** Aylık 500-900 € ek gelir
✅ **Burs:** DAAD 992 €/ay → Sperrkonto'ya gerek kalmaz

## Maliyeti Artıran Faktörler

❌ Şehir dışında uçak/tren gezme (€100-300/ay)
❌ Spor + fitness üyeliği (€30-50/ay)
❌ Premium club üyelikleri (€20-40/ay)
❌ Sigara/alkol (€80-200/ay)
❌ Restoranlara sık çıkma (€100-200/ay)

## Strateji

- **İlk yıl Sperrkonto + Werkstudent (5-9 ay sonra)** kombinasyonu en yaygın.
- **Düşük maliyet için:** Leipzig, Magdeburg, Halle, Chemnitz hedefle.
- **Yüksek kalite + iş imkanı:** Berlin, Stuttgart (NRW küçük şehirler).

İlgili: [En ucuz şehirler](/sss/sehir/almanyada-ogrenci-icin-en-ucuz-sehirler-hangileri).
MD,

            'goethe-yogunlastirilmis-kurs-1200eurya-deger-mi' => <<<'MD'
Goethe yoğunlaştırılmış (intensiv) Almanca kursu **1,200-1,800 €/4 hafta** arası. Bu fiyatın **değerini** kişisel hedef ve alternatiflere bakarak değerlendirmek gerek.

## Goethe-Kurs Avantajları

✅ **Uluslararası tanınmış sertifika** — DSH/TestDaF için en sağlam ön hazırlık
✅ **Üst düzey öğretmenler** (Almanca anadil + pedagoji doktorası)
✅ **Sınıf 8-14 kişi** (küçük, kişisel ilgi)
✅ **Materyaller dahil**
✅ **Kültürel etkinlik** (kursun yarısı kültür)
✅ **Vize için kabul** %100

## Alternatifler (Daha Ucuz)

| Kurs | 4 hafta intensiv | Sertifika |
| --- | --- | --- |
| Goethe-Institut | 1,200-1,800 € | Goethe-Zertifikat (uluslararası altın standart) |
| DeutschAkademie | 990-1,290 € | Telc (uluslararası, vize kabul) |
| Hartnackschule | 800-1,100 € | Telc (vize kabul) |
| Sprachschule Aktiv | 800-1,050 € | Telc |
| VHS (Volkshochschule) | 250-500 € | VHS sertifikası + ayrıca Telc sınavı |
| Online (DW, Babbel) | 50-200 € | Yok — ayrıca Telc sınavına gir |

## Hangisini Seçmeli?

### Goethe Seç:
- Hedefin **C1+ profesyonel kullanım**
- **Çocuk eğitimi/akademik kariyer** Almancada
- **CV'de "Goethe" prestiji** önemli
- Sınıf ortamında **küçük grup + premium** istiyorsan

### Alternatif Seç:
- **Vize için A1/A2** lazımsa Hartnack veya DeutschAkademie yeter
- **B1/B2 öğrenci hayatı** için orta segment yeterli
- **Sınav stratejisi:** ucuz kurs + ayrı sınav merkezi

## Pratik Tavsiye

- **A1 → B1 yolculuğu:** Hartnack veya DeutschAkademie (toplam **3,000-4,500 €**)
- **B2 → C1 hedef + akademik:** Goethe (1 kurs **1,500 €**)
- **C1 üzeri + profesyonel:** Goethe veya özel ders

## Para/Zaman Hesabı

- Goethe kursunun **1.5x maliyeti** ile DeutschAkademie + Telc sınav ücreti (~120 €) kapsanır.
- Çok hızlı öğreniciyseniz **Goethe'nin sınıf temposu** sizi yormaz; yavaş öğreniciyseniz **VHS uzun süreli + ucuz** daha mantıklı.

İlgili: [Dil kursu seçenekleri](/sss/sehir/frankfurt-civari-dil-kursu-secenekleri-nelerdir).
MD,

            'vize-basvurusu-icin-75eur-ucret-yeterli-mi-ek-harcama-var-mi' => <<<'MD'
**Hayır, 75 € sadece konsolosluk vize ücreti.** Toplam vize başvurusu maliyeti **300-700 €** civarındadır.

## Vize Başvurusu Toplam Maliyet

| Kalem | Tutar |
| --- | --- |
| **Vize ücreti (konsolosluk)** | 75 € |
| **IDATA hizmet ücreti** | 30 € |
| **Pasaport (yeniyse)** | 432 TL + harç (~25 €) |
| **Biyometrik fotoğraf (4 adet)** | 50-80 TL (~3 €) |
| **Yeminli tercüme (5-10 belge)** | 80-200 € (Türkiye'de) |
| **Apostil + tasdik** | 5-15 € (kaymakamlık veya valilik) |
| **Sağlık sigortası (1 yıl)** | 240-720 € (Hanse Merkur, Care Concept) |
| **Sperrkonto açma ücreti** | 89 € (Fintiba) veya 49 € (Expatrio) |
| **Anabin/denklik (gerekirse)** | 75 € |
| **Posta + kargo + ulaşım** | 30-100 € |
| **Toplam ortalama** | **~600-800 €** |

## Ek Maliyetler (Eğer Gerekirse)

- **TestDaF sınavı:** 195 € (Türkiye'de)
- **DSH sınavı:** 100-200 € (Almanya'da, gelmeden önce yapılmaz)
- **DAAD başvuru:** Ücretsiz, ama referans mektubu noter onayı 10-15 €
- **Uni-Assist:** 75 € ilk başvuru, sonraki başvurular 30 € her biri

## Vize Ücreti İade Edilir mi?

❌ **Hayır.** Vize reddedilse bile **75 € geri alınmaz.**
✅ Tek istisna: Konsoloslu işlemi başlamadan vazgeçersen IDATA üzerinden kısmi iade mümkün (genelde 30 € hizmet ücreti).

## Tasarruf Stratejisi

✅ **Türkiye'de yeminli tercüme** — Almanya'dan %50 ucuz
✅ **Hanse Merkur tek paket** — yıllık 720 €, vize için kabul (Fintiba paketinde dahil 84 €/ay)
✅ **Expatrio kombinasyonu** — Sperrkonto + sigorta paketi → tekil hizmetlerden 80-120 € ucuz
✅ **Pasaportu erken al** — randevu sıkıntısı, son haftaya bırakma

⚠️ **Vize başvurusu ÖNCESİ harcanan paranın çoğu vize redine eşit kaybedilir.** Belgelerini iyi hazırla.

İlgili: [Vize evrakları](/sss/vize/vize-basvurusunda-istenen-evraklar-nelerdir).
MD,

            'bloke-hesap-kapali-kalirsa-aylik-kesinti-devam-eder-mi' => <<<'MD'
**Evet, Sperrkonto kapalı (Türkiye'deyken) olsa bile aylık 992 € otomatik aktarılır** — ama bu para "kayıp" değil, ya birikir ya da iade edilir.

## Senaryo 1: Vize Almadın, Sperrkonto Açık
- Sperrkonto açıldıktan sonra **vize başvurun reddedildi** veya **iptal ettin**
- Para birikiyor, çıkış yapamıyorsun
- **Çözüm:** Fintiba/Expatrio'ya iptal başvurusu — 14 gün içinde **tam iade** (vize ücreti hariç)
- 14 günden sonra hesap kapatma ücreti: **30-50 €**

## Senaryo 2: Vize Aldın, Almanya'ya Gelmedin (Erteleme)
- Vize geldikten sonra 3-6 ay gelmedin
- Hesap "açık" kalır, **ay başında otomatik para çıkmaz** (sen aktivasyon yapmadığın için)
- Para hesapta kalmaya devam eder
- **Çözüm:** Fintiba'ya bilgi ver, hesap "Wartestatus" (bekleme modu)na alınır

## Senaryo 3: Almanya'ya Geldin, Aktivasyon Yaptın
- İlk ay **992 €** Girokonto'na aktarılır
- Sonraki aylar otomatik (sen iptal etmedikçe)
- Türkiye'ye geri dönsen bile **otomatik aktarım devam eder** — Almanya'da hesabın açıksa para alabilirsin

## Senaryo 4: Eğitim Bitti, Türkiye'ye Döndün
- Bakiyenin kalanı **Türkiye banka hesabına** veya **Almanya'da Girokonto'na** iade edilir
- İade ücreti: **30-50 €** (uluslararası SWIFT komisyonu)

## Önemli Notlar

⚠️ **Sperrkonto yıllık 89 € yönetim ücreti** (Fintiba) — vize alınmasa bile 1 yıl boyunca kesilir
⚠️ **Faiz var mı?** — Solarisbank'ta düşük faiz (% 1-2 düzeyinde), Sutor Bank'ta yok
⚠️ Hesap **2 yıl hareketsiz** kalırsa "dormant account" olur, ek ücret kesilir (5-10 €/ay)

## Pratik Tavsiye

- **Vize başvurusu geç veya reddedilirse** → 14 gün içinde Fintiba'ya iptal mesajı (tam iade)
- **Vize alındıysa ama erteleme** → Fintiba "bekleme" moduna al, ücretsiz tut
- **Almanya'ya gelmeden hesap aktive olmaz** — endişelenme

İlgili: [Sperrkonto açma](/sss/para/sperrkonto-bloke-hesap-icin-ne-kadar-para-gerekli).
MD,

            'berline-euro-yatirma-en-az-komisyonlu-yontem-hangisi' => <<<'MD'
Berlin'deki Sperrkonto veya Girokonto'na Türkiye'den Euro yatırmanın en uygun yöntemleri:

## En Uygun Yöntemler (2026)

### 1. Wise (TransferWise) — En Şeffaf
- Komisyon: ~%0.4-0.7
- 10,000 € transferinde: **45-75 € toplam**
- Süre: 1-2 iş günü
- Şeffaf kur (Google Finance kuru)

### 2. Revolut Premium — En Hızlı
- Premium üyelik: 7.99 €/ay (1 ay yetimli)
- Komisyon: %0 (limit içinde 100K €/ay)
- Süre: Aynı gün (uygulamadan)
- Türk bankası → Revolut TL → Revolut EUR → Berlin IBAN

### 3. Türk Bankası Direkt SWIFT
- Garanti BBVA: %1.5-2 + 35 € SWIFT
- 10,000 € transferinde: **180-235 € toplam**
- Süre: 2-3 iş günü
- En "klasik" yöntem, banka destek varsa kolay

### 4. Currency Exchange + Nakit
- Şube döviz ofislerinde EUR al, **el ile taşı** Almanya'ya
- 10,000 € **gümrük sınırı** — üstü beyan zorunlu
- Risk: Hırsızlık, kayıp
- Maliyet: %1-2 (banka spread)
- Önerilmez 5K €'dan fazla için

## En Az Komisyonlu Senaryo

**10,000 € transferi için tasarruf:**

| Yöntem | Toplam maliyet | Tasarruf (Wise'a göre) |
| --- | --- | --- |
| Wise | 45-75 € | — |
| Revolut Premium (1 ay) | 40-60 € | 5-15 € |
| Türk bankası SWIFT | 180-235 € | -135 € |
| Western Union | 350-450 € | -300 € |

✅ **Net kazanan: Wise + Revolut Premium kombinasyonu**

## Strateji

### Senaryo: Sperrkonto açıyorsun (11,904 €)
1. **Wise'a kayıt ol** (KYC tamamla — 2-3 gün)
2. Türkiye'deki bankan üzerinden **Wise hesabına IBAN ile gönder**
3. Wise'da TL → EUR çevir
4. Wise EUR → Sperrkonto IBAN'a gönder
5. Toplam komisyon: **~70 €**

### Senaryo: Aylık 1,500 € aile desteği
1. **Revolut Premium 1 ay al** (7.99 €)
2. Aylık 100K € limit içinde **komisyonsuz**
3. Premium 1 ay sonra iptal et, hesap kalır (Standart'a düşer)

## Kritik İpucu

⚠️ **Türkiye'den döviz çıkışı:** Şahıs hesabı 50K USD/yıl serbest, beyan zorunlu değil.
⚠️ **Almanya'ya nakit girişi:** 10K € üstü gümrük beyanı zorunlu (yoksa el konur + ceza).
⚠️ Banka transferi ile **kayıt** kalır, vergi/kira teyit gerektiğinde işine yarar.

İlgili: [Wise/Revolut karşılaştırma](/sss/para/wise-veya-revolut-turkiye-almanya-transferi-icin-uygun-mu).
MD,

            'postbank-deutsche-bank-n26-ogrenci-icin-en-uygun-banka' => <<<'MD'
Almanya'da öğrenci için en yaygın 3 banka seçeneği — özellikleri farklı:

## N26 — Modern + Hızlı

✅ **Avantajlar:**
- **Tamamen online** — uygulamayla hesap açma 8 dakikada
- **Aylık ücret 0 €** (Standart)
- **İngilizce arayüz** (Almanca-İngilizce karışım)
- **Anlık bildirim, Apple/Google Pay**
- **Avrupa'da ücretsiz** kart kullanımı
- **Maestro/Mastercard** ücretsiz çıkarma 3 kez/ay

❌ **Dezavantajlar:**
- **Şubesiz** — yüz yüze hizmet yok
- **Telefon destek yok** (sadece chat/mail)
- **Sperrkonto açamaz** (öğrenci vizesi için ayrı Fintiba/Expatrio lazım)
- Müşteri hizmetleri yavaş (problem çıkarsa zor)

✅ **Kim için:** Teknolojiyle anlaşan, hızlı çözüm isteyen, az nakit kullanan öğrenci

## Postbank — Klasik + Geniş

✅ **Avantajlar:**
- **Posta + banka kombinasyonu** — Deutsche Post ofislerinde bankacılık
- **Aylık ücret 0 €** (öğrenci tarifesi)
- **Geleneksel banka güveni**
- **Postanede nakit yatırma** (her köşede)
- Şube + ATM yaygın

❌ **Dezavantajlar:**
- **Online sistemi modası geçmiş** (uygulama bazen yavaş)
- **Sperrkonto açamaz**
- Uluslararası transfer **ücretli ve yavaş**

✅ **Kim için:** Nakit çoğunluklu kullanan, güvenilirlik isteyen klasik tip

## Deutsche Bank — Premium + Profesyonel

✅ **Avantajlar:**
- **Aylık 0 €** (öğrenci pakti)
- **Uluslararası prestij** (Türkiye'de de var)
- **Sperrkonto açabilir** — vize için ideal (290 € yıllık ücret)
- **Maestro + Visa** kart ücretsiz
- **Şube + ATM bol**
- **Uluslararası transfer** kolay, makul komisyon

❌ **Dezavantajlar:**
- **Öğrenci paketi sonrası ücretli** (4.90 €/ay, 30 yaş üstü)
- Yeni hesap açma **şubede yapılır** (online sınırlı)
- ATM dışındaki bankalarda **ücretli** çekim

✅ **Kim için:** Uluslararası bankacılık, Sperrkonto + Girokonto tek banka isteyen, premium müşteri

## Sonuç: Öneri

### Çoğu Öğrenci için: **N26 + Fintiba (Sperrkonto)** kombo
- Fintiba: Sperrkonto (89 €/yıl)
- N26: Günlük kullanım (ücretsiz)
- Para çekme: ayda 3x ücretsiz, ATM Almanya'da yaygın

### Klasik tipler için: **Postbank + Fintiba**
- Postbank: Günlük + nakit yatırma kolay
- Fintiba: Sperrkonto

### Premium isteyenler için: **Deutsche Bank tek paket**
- Sperrkonto + Girokonto aynı bankada
- Yıllık 290 €'ya yakın ek maliyet ama uluslararası transfer kolay

## Diğer Alternatifler

- **ING** — N26 alternatifi, online, faiz veriyor
- **Commerzbank Studierende** — orta yol, ücretsiz
- **Sparkasse** — yerel bağlantı isteyenler için (2-5 €/ay)
- **DKB** — komisyonsuz uluslararası ATM
MD,

            'almanyadan-turkiyeye-para-gondermek-icin-en-uygun-uygulama-hangisi' => <<<'MD'
Almanya'dan Türkiye'ye TL veya EUR göndermek için en uygun yöntemler:

## 2026 Karşılaştırması

| Uygulama/Yöntem | Komisyon (1,000 €) | Hız | Şeffaflık |
| --- | --- | --- | --- |
| **Wise** | ~6-12 € | 1-2 gün | %100 şeffaf |
| **Revolut** | ~5-10 € (Premium üyelikle 0 €) | Aynı gün | Şeffaf |
| **N26 SWIFT** | 30-50 € | 2-4 gün | Banka kuru |
| **Deutsche Bank SWIFT** | 35-60 € | 2-3 gün | Banka kuru |
| **Sparkasse SWIFT** | 25-40 € | 2-3 gün | Banka kuru |
| **Western Union (uygulama)** | 30-80 € | 15 dk - 1 gün | Düşük kur |
| **Hawala/elden** | Risk | Risk | Yasal değil |

## En Uygun: Wise

✅ **Wise avantajları:**
- TL/EUR şeffaf kur (Google Finance)
- Türk IBAN'a direkt transfer (Garanti, İş Bankası, Akbank vs. tüm bankalar)
- Aileye ulaşma süresi 1-2 iş günü
- Mobil uygulama Türkçe destekli

## Revolut'un Üstünlüğü

✅ **Revolut Premium (7.99 €/ay):**
- Aylık 100K € limitli ücretsiz transfer
- Anlık (aynı uygulama içinde TL-EUR)
- Türkiye'de bankaya gönderme: Wise'la aynı hızda

## Önemli Notlar

⚠️ **Türkiye'ye TL gönderirken kambiyo:**
- Wise/Revolut TL hesabı **var** (Türk vatandaşı için)
- Almanya'dan EUR → TL Türk bankasına direkt mümkün
- Bazı bankalar **döviz girişinde EUR olarak** kayıt yapar (TL otomatik çevirmez)

⚠️ **Vergi:** Türkiye'ye giden para kişisel transfer < 50K USD/yıl beyan etmek gerekmez.

⚠️ **Türk bankalarının komisyonu:** Bazı bankalar uluslararası gelen havalede **20-50 TL** komisyon alabilir (alıcı tarafta).

## Strateji

### Senaryo: Aile/akrabaya 200-500 € gönderiyorsun
- **Wise** en uygun, **5-8 € komisyon** civarı

### Senaryo: Yıllık tasarrufunu Türkiye'ye gönderiyorsun (10K+ €)
- **Revolut Premium 1 ay** al → komisyonsuz
- Toplam: **8 € (Premium ücreti)** vs Wise'da **45 €**

### Senaryo: Büyük tek seferde transfer (50K+ €)
- **DKB veya Deutsche Bank SWIFT** profesyonel destek + kayıt
- Komisyon 100-200 € ama vergi/audit için resmi belge

## Kritik İpucu

⚠️ Türkiye'de **enflasyon yüksek** — yatırım amaçlı para gönderirken **döviz olarak tutmak** çoğu zaman daha mantıklı (Türkiye Wise EUR hesabı veya altın/döviz hesabı).
MD,

            'sayfa-basi-yeminli-tercume-ortalama-kac-euro' => <<<'MD'
Yeminli tercüme (beeidigte Übersetzung) sayfa başı ücretler 2026'da:

## Almanya'da Yeminli Tercüme

| Bölge | Sayfa başı |
| --- | --- |
| Berlin (uygun) | 25-40 € |
| Berlin (premium) | 40-60 € |
| Münih | 35-55 € |
| Hamburg | 30-50 € |
| Stuttgart | 25-45 € |
| Köln/Düsseldorf | 28-45 € |
| Küçük şehirler (Heilbronn, Reutlingen) | 20-35 € |

**+ Yeminli mühür ücreti:** 5-15 € sayfa/belge başı
**+ Apostil ek hizmeti:** 15-25 € (kurum ücretleri ayrı)

## Türkiye'de Yeminli Tercüme

- **Sayfa başı:** 200-400 TL (8-15 €)
- Mühür + tasdik dahil **sayfa fiyatı**
- **Apostil ayrı:** Kaymakamlık 200-300 TL (5-10 €), Valilik 300-500 TL

**Türkiye'de tercüme + Apostil toplam: 10-25 € sayfa başı** (Almanya'nın yarısı)

## Standart Sayfa Tanımı

- **1 standart sayfa = 55 satır × 50 karakter** (BDÜ tarifi Almanya)
- **1 standart sayfa = 1,000 karakter** (Türkiye tercüme dernekleri)
- Diploma transkript: 2-4 sayfa
- Pasaport ana sayfa: 1 sayfa
- Sabıka kaydı: 1 sayfa
- Lise diploması: 1-2 sayfa

## Tipik Belge Maliyetleri (Türkiye'de hazırlanırsa)

| Belge | Sayfa | TR maliyet | DE maliyet |
| --- | --- | --- | --- |
| Lise diploması | 1 | 300 TL (~10 €) | 30 € |
| Transkript (4 yıllık lise) | 2-3 | 600-900 TL (~25 €) | 75 € |
| Üni diploması + transkript | 3-4 | 1,000-1,500 TL (~40 €) | 130 € |
| Pasaport sayfası | 1 | 250 TL (~8 €) | 25 € |
| Sabıka kaydı | 1 | 250 TL (~8 €) | 25 € |
| Doğum belgesi | 1 | 250 TL (~8 €) | 25 € |
| **Toplam tipik belge paketi** | ~10 sayfa | **~3,000 TL (100 €)** | **~300 €** |

## Strateji

✅ **Türkiye'de hazırla:** Diploma, transkript, sabıka, doğum belgesi (Apostil ile)
✅ **DE'de hazırla:** Yalnızca Almanya'da gelen yeni belgeler (örn. Anmeldung sonrası)
✅ **Toplu indirim:** Tek tercümana 5+ belge → %10-20 indirim
✅ **Online sipariş:** PDF tara, tercüman e-mail ile çalışsın, **acil tarife** (5-7 gün)

⚠️ **Hangi belgeler tercüme zorunlu:**
- Pasaport (sadece pasaport sayfası — bazen kabul ediliyor, bazen istenmiyor)
- Lise diploması + transkript
- Üni diploması + transkript (varsa)
- Yabancı dil sertifikası (genelde original kabul, tercüme isteğe bağlı)
- Sabıka kaydı (vize için, 6 ay geçerli)

⚠️ **Apostil zorunlu:** Almanya'da yeminli olmayan tercümeler **Apostil** ile geçerli olur. Türkiye'de Kaymakamlık → Valilik → Hâkimler ve Savcılar Kurulu zinciri.

İlgili: [Berlin tercüman](/sss/sehir/berlinde-uygun-fiyatli-cevirmen-onerisi) | [Stuttgart tercüman](/sss/sehir/stuttgartta-yeminli-cevirmen-onerisi).
MD,
        ];
    }

    private function sigortaAnswers(): array
    {
        return [
            'tk-aok-barmer-arasindaki-fark-ogrenci-icin-hangisi' => <<<'MD'
Almanya'da öğrencilerin %95'i **gesetzliche Krankenversicherung (GKV — yasal sağlık sigortası)** kullanıyor. En yaygın 3 GKV şirketi: **TK, AOK, Barmer**.

## Karşılaştırma (2026)

| Özellik | TK (Techniker) | AOK | Barmer |
| --- | --- | --- | --- |
| **Aylık prim (öğrenci)** | ~131 € | ~131 € | ~131 € |
| **Yaş 30 altı öğrenci tarifi** | Evet | Evet | Evet |
| **Yaş 30 üstü zorunlu** | Var (özel veya gönüllü GKV) | Aynı | Aynı |
| **Diş kapsamı (Standart)** | Temel + filling + temizlik | Temel + filling | Temel + 1 yıl x 1 temizlik |
| **Diş premium** | +10 €/ay | +12 €/ay | +9 €/ay |
| **Online portal** | En modern, app ile randevu | Klasik | Orta seviye |
| **İngilizce destek** | Evet (web + telefon) | Sınırlı | Sınırlı |
| **Şube ağı** | Tüm Almanya | En geniş (eyalet bazlı) | Geniş |
| **Türkçe destek** | Sınırlı | AOK Türkische Service (Berlin, NRW) | Sınırlı |
| **Mobil app puanı** | ★★★★★ | ★★★ | ★★★★ |

## Diğer GKV Seçenekleri

- **DAK-Gesundheit** — orta yol, aile odaklı
- **BKK** — şirket sağlık fonları, bazıları ucuz (BKK VBU vs.)
- **IKK** — küçük zanaatkâr fonları

## Öğrenci için Hangisi?

### TK (Techniker)
✅ **En çok öğrenci tercih ediyor**
✅ Mobil app modern, online belge yükleme
✅ Avrupa'da seyahat ek paketi cüzi
❌ Şube fiziksel az (online ağırlık)

### AOK
✅ **Eyaletten eyalete farklı** (AOK Bayern, AOK NRW, AOK Berlin)
✅ Şube + telefon desteği güçlü
✅ AOK Türkische Service Berlin/NRW'de **Türkçe destek**
❌ Online sistemi daha geleneksel

### Barmer
✅ **Akademisyen + öğrenci dostu** (kütüphane indirimi, etkinlikler)
✅ Çocuk koruyucu hekimlik geniş
❌ App ortalama

## Pratik Tavsiye

- **Tek başına gidip dil sınırlıysan:** TK (İngilizce hizmet + app)
- **Türkçe destek istiyorsan:** AOK NRW veya AOK Berlin (Türkische Service)
- **Aile/çocuklu:** Barmer (çocuk hizmetleri geniş)
- **Hızlı çözüm + dijital:** TK

## Geçiş / Değiştirme

✅ **18 ay kuralı:** Hangi sigortaya kayıt olursan ol, 18 ay sonra başka sigortaya geçebilirsin (kanıtsız).
✅ **Ücret aynı** GKV'ler arasında — fark sadece hizmet/destek/diş paketleri.
✅ Üniversite kaydında **GKV kart** veya **muafiyet belgesi** zorunlu.

İlgili: [Sigorta aylık maliyet](/sss/sigorta/ogrenci-saglik-sigortasi-krankenversicherung-aylik-ne-kadar) | [AOK Bayern başvuru](/sss/sigorta/aok-bayerna-ogrenci-olarak-nasil-basvurulur).
MD,

            'ogrenci-saglik-sigortasi-krankenversicherung-aylik-ne-kadar' => <<<'MD'
2026 Almanya öğrenci sağlık sigortası aylık primi:

## Yasal Öğrenci Sigortası (GKV — 30 yaş altı)

| Sigorta tipi | Aylık prim |
| --- | --- |
| **GKV Standart (TK, AOK, Barmer vs.)** | **130-135 €** |
| **GKV + Pflege (uzun süreli bakım sigortası)** | +25-30 € (toplam ~131 €) |
| **GKV + Premium diş paketi** | +9-15 € |

**TOPLAM ortalama: 130-160 €/ay**

## 30 Yaş Üstü (Zorunlu Özel)

| Sigorta tipi | Aylık prim |
| --- | --- |
| **Privat Krankenversicherung (PKV) öğrenci tarifi** | **180-280 €** |
| **Mawista Student, Care Concept** (yıllık önödemeli) | 60-90 €/ay (kapsamı sınırlı) |
| **Hanse Merkur Student** | 88-130 €/ay |

## Vize için Geçici Sigorta (İlk 3 Ay)

- **Hanse Merkur Visa Protect:** 24-30 €/ay (uzaktan açma)
- **Care Concept Visum Plus:** 27-32 €/ay
- **Mawista Visum:** 22-28 €/ay
- **Allianz Travel:** 35-45 €/ay (premium)

⚠️ Bu **geçici sigortalar vize için kabul** ediliyor (30K € teminat) ama Almanya'da uzun süreli kullanılamaz. İlk 3 ay sonra **GKV'ye geçiş** zorunlu.

## Studienkolleg Öğrencileri

- 30 yaş altı + Studienkolleg kayıtlı → GKV %50 indirim tarifesi geçerli **bazı kantonlarda**
- Genelde 130 € normal tarife, ama bazı eyaletler özel kontenjan

## Werkstudent / Mini-Job Çalışıyorsan

- 538 € altı kazanç → Sigorta primi **işveren ödüyor**, sen 0 ödüyorsun
- Werkstudent (haftalık 20 saat altı) → Sigorta primi öğrenci tarifesi devam, sen 131 € ödüyorsun
- Werkstudent (haftalık 20 saat üstü, semestre dışında) → Çalışan sigortası, ücret %14.6'sı

## Aile Sigortası (Familienversicherung)

- 30 yaş altı + aylık gelir 535 € altı → **Almanya'daki ebeveynine kayıtlı olarak ücretsiz** sigortalanabilirsin
- ⚠️ Bu sadece ebeveynin Almanya GKV'de kayıtlıysa geçerli
- Eş veya çocuk için de mümkün

## Sigorta + Pflege Ayrımı

- **Krankenversicherung:** Sağlık + ilaç + hastane
- **Pflegeversicherung:** Uzun süreli bakım (yaşlı, engelli) — öğrenci de ödüyor (~25 €/ay)
- İkisi birlikte zorunlu

## Yıllık Toplam (Tahmini)

| Yaş + Durum | Aylık | Yıllık |
| --- | --- | --- |
| 30 altı + standart GKV | 131 € | 1,572 € |
| 30 altı + premium diş | 145 € | 1,740 € |
| 30 üstü + PKV | 230 € | 2,760 € |
| Werkstudent (haftalık 20+) | %14.6 maaş | Değişken |

İlgili: [TK/AOK karşılaştırma](/sss/sigorta/tk-aok-barmer-arasindaki-fark-ogrenci-icin-hangisi) | [Yaş 30 üstü](/sss/sigorta/bachelor-baslarken-30-yas-ustuysem-sigortam-degisir-mi).
MD,

            'vize-icin-30k-euro-teminatli-sigorta-yeterli-mi' => <<<'MD'
**Evet, 30K € teminatlı sigorta vize için kabul ediliyor** — Schengen + ulusal vize gerekliliği.

## Vize Sigorta Kriterleri (2026)

✅ **Minimum teminat:** 30,000 € (sağlık, kaza, hastane, geri dönüş)
✅ **Süre:** Vize geçerlilik süresini kapsamalı (genelde 3-12 ay)
✅ **Almanya'da geçerli** olmalı
✅ **Schengen bölgesi** dahil

## Vize Onaylı Sigorta Şirketleri

| Şirket | Yıllık Premium | Avantaj |
| --- | --- | --- |
| **Hanse Merkur Visa Protect** | 240-320 € | En yaygın, ucuz |
| **Care Concept Visum Plus** | 270-360 € | Türkiye'de yaygın |
| **Mawista Visum** | 240-300 € | Online açma kolay |
| **Allianz Travel** | 380-480 € | Premium, kapsamlı |
| **Fintiba Plus paketi** | 84 €/ay dahil | Sperrkonto + sigorta |
| **Expatrio paketi** | 95 €/ay dahil | Sperrkonto + sigorta |

⚠️ **Tüm bu sigortalar vize ücreti olarak geçici (vize sigortası)** — vize alındıktan ve Almanya'ya gelindikten sonra **GKV'ye geçiş zorunlu**.

## Vize Sigortası vs GKV Karşılaştırma

| Özellik | Vize Sigortası (geçici) | GKV (Almanya'da) |
| --- | --- | --- |
| Teminat | 30K € | Sınırsız |
| Diş tedavisi | Acil sadece | Standart bakım dahil |
| Doğum/aile planlama | Yok | Tam kapsam |
| Kronik hastalık | İlk hastalık önce başlamış olmamalı | Tam kapsam |
| Aylık prim | 22-30 € | 131 € |
| Süre | Max 12 ay | Sınırsız |

## Süreç

1. **Vize başvurusu öncesi:** Hanse Merkur veya Mawista'dan **3-6 ay sigorta** al (24-30 €/ay).
2. Vize evraklarına **sigorta sertifikası** ekle.
3. Vize alındıktan sonra Almanya'ya gel.
4. **Üniversite kaydı için GKV (TK/AOK/Barmer)** seç.
5. GKV başvurusu yaptıktan sonra vize sigortasını **iptal et** (kullanılmayan ay iadesi).

## Önemli Notlar

⚠️ **GKV başvurusu için Anmeldung şart olmayabilir** ama üniversite kaydı için GKV onay belgesi gerekli.
⚠️ **Vize sigortası TR kaynaklı olabilir mi?** — Türkiye'deki şirketler **Almanya konsolosluğunda kabul edilmez** (Hanse Merkur Türkiye temsilcisi farklı sözleşme yapıyor).
⚠️ **30K €'nun üstü teminat** vize için zorunlu değil ama premium öneriliyor (özellikle 30+ yaş için).

## Strateji

✅ **Fintiba Plus paketi** (Sperrkonto + Sigorta tek pakette) - en pratik
✅ **Mawista Visum** ucuz + esnek (vize çıktığında iptal kolay)
✅ Yıllık ödemek **aylık öğrenciye** göre %15-20 ucuz

İlgili: [Fintiba sigorta](/sss/sigorta/fintibanin-sigorta-paketi-vize-icin-kabul-ediliyor-mu) | [Türkiye'den sigorta](/sss/sigorta/almanyaya-gelmeden-turkiyeden-sigorta-yaptirabilir-miyim).
MD,

            'fintibanin-sigorta-paketi-vize-icin-kabul-ediliyor-mu' => <<<'MD'
**Evet, Fintiba'nın sigorta paketi Almanya konsolosluğunda vize için kabul ediliyor.** Almanya'nın tüm konsoloslukları (İstanbul, Ankara, İzmir, Antalya) onaylıyor.

## Fintiba Plus Paketi Detayları

**Aylık 84 €** (vize sigortası + Sperrkonto yönetim ücreti)

### İçerik
✅ **Hanse Merkur Visa Protect** vize sigortası (30K € teminat)
✅ **DR-WALTER Krankenversicherung** Almanya'ya geldikten sonra otomatik geçiş
✅ **Sperrkonto hesap yönetimi** (89 €/yıl ücreti bu pakette dahil)
✅ **Sigorta + bloke hesap tek panel**

### Vize için Sağlanan Belgeler
- **Sigorta sertifikası (Versicherungsbescheinigung)** — vize başvurusunda konsolosluğa sunmak için
- 30K € teminat, Schengen geçerli
- Almanya'da geçerli, hastane dahil

## Vize Onaylı Statüsü

✅ Almanya İstanbul, Ankara, İzmir, Antalya, Trabzon konsolosluklarında **direkt kabul**
✅ IDATA hizmet ortağı listede Fintiba referansı var
✅ Diğer Almanya konsoloslukları (Avrupa, Asya) da kabul ediyor

## Avantajları

✅ **Sperrkonto + sigorta tek paket** — ayrı ayrı ücret yok
✅ **Online açma** — Türkiye'den 2-3 günde başvuru tamam
✅ **Türkçe destek** chat üzerinden
✅ Vize geldikten sonra Almanya'ya geldiğinde otomatik **GKV'ye geçiş asistanı**

## Dezavantajları

❌ **Aylık 84 € biraz pahalı** — vize sigortası ayrı + Sperrkonto ayrı alınırsa ~60-70 €/ay yapılabilir
❌ **GKV'ye geçiş zorunlu** — Almanya'da 30 yaş altı + üniversite öğrencisiysen TK/AOK/Barmer'e geçmen lazım (Fintiba sigortası geçici)
❌ **İptal süreci 14 günü aşarsa** ay sonuna kadar prim devam eder

## Karşılaştırma: Fintiba Plus vs Ayrı Paket

| Hizmet | Fintiba Plus | Ayrı Paket |
| --- | --- | --- |
| Sperrkonto yönetim | 89 €/yıl | 89 €/yıl (Fintiba Basic) |
| Vize sigortası 1 yıl | Pakette dahil | 240-300 € (Hanse Merkur) |
| **Toplam yıllık** | **~1,008 €** | **~329-389 €** |

⚠️ **Plus paket biraz pahalı** — ayrı alırsan tasarruf ediyorsun. Ama "tek panel + tek sorumlu" kolaylığı için Plus tercih edenler çok.

## Pratik Strateji

### Bütçe Hassasiyse:
- Fintiba Basic (Sperrkonto, 89 €/yıl)
- + Hanse Merkur Visa Protect ayrı (240-300 €/yıl)
- Toplam: **~330 €/yıl**

### Kolaylık İstiyorsan:
- Fintiba Plus (84 €/ay × 6-12 ay) **~1,000 €**
- Tek başvuru, tek e-mail, tek panel

## Önemli

⚠️ Almanya'ya geldikten sonra **GKV'ye kayıt zorunlu** — Fintiba/Hanse Merkur sigortası Almanya'da öğrenci olarak uzun süre kullanılamaz.
⚠️ Üniversite **GKV onay belgesi** istiyor (TK, AOK vs.). Fintiba sertifikası geçici, üniversite için yetmez.

İlgili: [Vize sigortası](/sss/sigorta/vize-icin-30k-euro-teminatli-sigorta-yeterli-mi) | [Sperrkonto](/sss/para/sperrkonto-bloke-hesap-icin-ne-kadar-para-gerekli).
MD,

            'studienkolleg-ogrencileri-icin-sigorta-zorunlulugu-nedir' => <<<'MD'
Studienkolleg öğrencileri **üniversite öğrencisi statüsünde** sayılır, dolayısıyla aynı sigorta kuralları geçerli:

## Sigorta Zorunluluğu

✅ **GKV (yasal sigorta) kayıt zorunlu** — Studienkolleg kayıt sırasında istenen
✅ **30 yaş altı:** Öğrenci tarifesi geçerli (~131 €/ay)
✅ **30 yaş üstü:** PKV (özel sigorta) zorunlu (~230-280 €/ay)
✅ **Vize sigortası** sadece Almanya'ya geliş süresince (vize başvurusu için)

## Studienkolleg Spesifik Durumlar

### 1. T-Kurs / G-Kurs / W-Kurs Öğrencileri
- Aynı sigorta, aynı prim
- Sertifika almak için Studienkolleg → GKV başvurusu

### 2. Studienkolleg Süresi (1-2 Dönem)
- 1 dönem (Kurzkurs): 6 ay sigorta gerekiyor
- 2 dönem (Standardkurs): 12 ay sigorta
- Süre boyunca aralıksız ödeme

### 3. Studienkolleg Sonrası Üniversiteye Geçiş
- Sigorta aynen devam eder (GKV otomatik)
- TK'dan AOK'a geçiş isteğe bağlı (18 ay sonra)

## Werkstudent / Mini-Job Çalışma

✅ Studienkolleg öğrencisi **Werkstudent statüsü kazanır** — aynı haftalık 20 saat sınırı geçerli
✅ Mini-Job (538 € altı) → Sigorta primi sıfır
✅ Studienkolleg + Werkstudent kombinasyonu yaygın (özellikle T-Kurs)

## Sınav Dönemi (Feststellungsprüfung)

- Sınav 4-6 hafta sürer
- Sınav döneminde sigorta devam ediyor (aralık yapılmaz)
- Sınav sonrası üniversiteye geçiş döneminde **boş geçen aylar olmamalı**

## Özel Durumlar

### Studienkolleg Tamamlanmadan Türkiye'ye Dönmek Zorunda Kaldıysan
- Sigortayı **iptal et** (önceden bildirim 1 ay)
- Kullanılmayan aylar iade edilir (genelde)

### Studienkolleg Reddinden Sonra Almanya'da Kaldıysan
- Sigorta zorunlu — vize uzatma için kanıt lazım
- "Sprachkurs vizesi"ne dönerken sigorta da değişebilir

## Hangi GKV?

- **TK:** Modern + İngilizce destek + uygulama
- **AOK Bayern/Berlin/NRW:** Eyalet bazlı, fiziksel şube
- **Barmer:** Aile + akademisyen dostu

### Öğrenci Tarifesi (özellikle Studienkolleg için)
- TK + Barmer: Genel öğrenci tarifesi
- AOK: Eyalete göre küçük varyasyon (örn. AOK Bayern Studierende 130.50 €)

## Önemli Notlar

⚠️ Studienkolleg başvurusunda **vize sigortası**, kayıt sonrası **GKV** lazım — geçişi zamanında yap
⚠️ Üniversite **GKV onay belgesi** zorunlu kayıt için — vize sigortası yetmez
⚠️ Aile Almanya'da kayıtlıysa **Familienversicherung** mümkün (35 yaş altı + gelir 535 € altı)

İlgili: [Öğrenci sigortası aylık](/sss/sigorta/ogrenci-saglik-sigortasi-krankenversicherung-aylik-ne-kadar) | [Yaş 30 üstü](/sss/sigorta/bachelor-baslarken-30-yas-ustuysem-sigortam-degisir-mi).
MD,

            'bachelor-baslarken-30-yas-ustuysem-sigortam-degisir-mi' => <<<'MD'
**Evet, 30 yaş ve üstü olarak Bachelor'a başlıyorsan sigorta sistemi değişiyor.** 30 yaş **GKV öğrenci tarifesinden** çıkış sınırı.

## 30 Yaş Sınırı Kuralı

✅ **30 yaşına kadar (30 dahil):** GKV öğrenci tarifesi (~131 €/ay)
❌ **30 yaş 1 gün sonrası:** Öğrenci tarifesi sona erer → 2 seçenek:
   1. **Privat Krankenversicherung (PKV)** — özel sigorta
   2. **Freiwillige GKV** — gönüllü yasal sigorta

## Seçenek 1: PKV (Özel Sigorta)

### Öğrenci PKV Tarifeleri
| Şirket | Aylık prim |
| --- | --- |
| **DA Direkt** | 180-220 € |
| **Hallesche Studenten** | 195-240 € |
| **Mawista Premium** | 175-210 € |
| **Hanse Merkur Premium** | 200-250 € |
| **Allianz Studenten** | 230-290 € |

### PKV Avantajları
✅ Premium hastane (özel oda, doktor seçimi)
✅ Bekleme süresi az
✅ Diş + alternatif tıp paketi
✅ **30 yaş öncesi GKV'den ucuz olabilir**

### PKV Dezavantajları
❌ **Tek yön kapı** — PKV'den GKV'ye dönmek 55 yaş altı imkansız (özellikle çalışıyorsan)
❌ Yaş ilerledikçe prim artıyor
❌ Aile sigortası yok (her birey ayrı prim ödüyor)
❌ Türkçe destek yok genelde

## Seçenek 2: Freiwillige GKV (Gönüllü Yasal)

### Tarife
- Genelde **160-200 €/ay** (TK öğrenci için 30 yaş sonrası)
- Pflegeversicherung dahil

### Avantajları
✅ Standart GKV ile aynı hizmet
✅ Aile sigortası kullanabiliyorsun
✅ Geçiş sonrası **kalıcı PKV'ye geçmek mümkün**
✅ Almanya'da gelir kazanır kazanmaz iş sigortasına dönüş kolay

### Dezavantajları
❌ Öğrenci tarifesinden pahalı
❌ Bazı sigorta şirketleri "freiwillig" tarifesini açmıyor — başvuru reddedilebilir

## Hangisi Daha Mantıklı?

### PKV Seç:
- Bachelor sonrası **Almanya'da çalışmayı düşünüyorsan** (yüksek gelir + premium istiyorsan)
- Sağlığa para harcamayı sorun etmiyorsan
- Hızlı/kaliteli sağlık hizmeti istiyorsan
- Almanya'da uzun vadeli kalış planlıyorsan

### Freiwillige GKV Seç:
- **Türkiye'ye geri dönme ihtimali yüksek** ise (PKV'den çıkmak zor)
- Sağlık masrafı az olan genç + sağlıklı isen
- Aile sigortası ileride lazım olabilirse
- Almanya'da çalışan eş veya ebeveynin GKV'de ise (Familienversicherung şansı)

## Önemli Notlar

⚠️ **Yaş 30 üstü işsiz** → ihtiyaç halinde başvuru çok zor olabilir, önce bir GKV/PKV başvur, sonra üniversiteye kayıt yap
⚠️ **Master öğrencisi 30 yaş üstü** → Aynı kurallar geçerli, master öğrenci de bu durumla karşılaşıyor
⚠️ **Yaş 35 öncesi PKV** geçişi 14 günde mümkün ama 35 sonrası kontrol süreci uzun

## Vize Başvurusunda

Yaş 30 üstüysen **vize sigortası geçicidir**, Almanya'ya geldikten sonra PKV'ye kayıt için kanıt belgesi ister.

İlgili: [Sigorta aylık ücret](/sss/sigorta/ogrenci-saglik-sigortasi-krankenversicherung-aylik-ne-kadar) | [Werkstudent sigorta](/sss/sigorta/werkstudent-icin-sigorta-zorunlu-mu).
MD,

            'almanyaya-gelmeden-turkiyeden-sigorta-yaptirabilir-miyim' => <<<'MD'
**Evet, Türkiye'den vize sigortası yaptırabilirsin** — hatta vize başvurusu için **zorunlu**. Ama Almanya'ya geldikten sonra GKV/PKV'ye **geçiş yapmak zorundasın**.

## Vize Sigortası (Türkiye'den)

### Almanya Konsolosluğu Onaylı Şirketler
✅ **Hanse Merkur Visa Protect** (Almanya merkezli, dünya çapında)
✅ **Care Concept Visum Plus** (Almanya)
✅ **Mawista Visum** (Almanya)
✅ **Allianz Travel** (Almanya/uluslararası)
✅ **Fintiba Plus paketi** (Almanya + paket halinde Sperrkonto ile)

### Türkiye Kaynaklı Şirketler (DİKKAT!)
⚠️ Anadolu Sigorta, Aksigorta, Allianz Türkiye **Almanya konsolosluğunda kabul EDİLMİYOR**
⚠️ Sadece **Almanya/AB merkezli sigortalar geçerli**

### Online Açma
- Hanse Merkur, Mawista, Care Concept — **online başvuru + ödeme** ile sertifika 24 saatte gelir
- Kredi kartı ile öder, e-mail ile sertifika alırsın
- Konsolosluğa **PDF olarak sunabilirsin**

## Vize Sigortası Detayları

- **Süre:** Genelde 3-6 ay (vize başlangıcı kadar)
- **Teminat:** 30K € minimum (konsolosluk şartı)
- **Kapsam:** Acil sağlık, hastane, geri dönüş (Türkiye'ye nakil)
- **Aylık fiyat:** 22-30 € (Hanse Merkur), 27-32 € (Care Concept)

## Vize Sigortası NEDEN Türkiye'den Alınamaz?

⚠️ Türkiye sigortacılığı **Türk hukukuna tâbi** — Almanya'da geçerli değil
⚠️ Konsoloslukla **anlaşmalı sigorta şirketleri listesi** var (yukarıdakiler)
⚠️ Türk sigortasının "Almanya'da geçerlidir" notu olsa bile **Almanya'da hastane kabul etmez**

## Vize Sigortasından GKV'ye Geçiş

Vize → Almanya'ya geliş → Üniversite kaydı süreci:
1. **Vize sigortası** ilk 1-3 ay yeterli
2. Üniversite kaydı sırasında **GKV başvurusu zorunlu** (TK, AOK, Barmer vs.)
3. GKV başvurusu yapılınca **vize sigortasını iptal et** (kullanılmayan ay iadesi var)
4. GKV onay belgesi al, üniversiteye sun

⚠️ **30 yaş üstüysen PKV (özel sigorta)** seçeneği — Bachelor öğrenciler dahil.

## Önemli Vize Stratejileri

### Hangi Sigortayı Seç?

| Bütçe + Kolaylık | Öneri |
| --- | --- |
| Ucuz + yıllık | **Mawista** (~240 €/yıl) |
| Premium + güvenli | **Hanse Merkur** (~280 €/yıl) |
| Sperrkonto ile paket | **Fintiba Plus** (84 €/ay, sigorta + Sperrkonto bir arada) |
| Konsolosluk en sık kabul | **Hanse Merkur** veya **Fintiba** |

### Tasarruf İpuçları

✅ **Yıllık ödeme** (aylıktan %15-20 ucuz)
✅ **Fintiba/Expatrio paketi** Sperrkonto + sigorta birlikte alınca komisyon kazancı
✅ Almanya'ya geldikten sonra **iptal et** — kullanılmayan ay iadesi

## Sonuç

✅ Türkiye'den **Almanya/AB merkezli vize sigortası** al (Hanse Merkur, Mawista, Care Concept, Allianz, Fintiba)
❌ Türk sigorta şirketleri vize için kabul edilmez
✅ Almanya'ya geldiğinde **GKV'ye geçiş zorunlu**

İlgili: [Vize sigortası](/sss/sigorta/vize-icin-30k-euro-teminatli-sigorta-yeterli-mi) | [Fintiba paket](/sss/sigorta/fintibanin-sigorta-paketi-vize-icin-kabul-ediliyor-mu).
MD,

            'dis-tedavisi-ogrenci-sigortasina-dahil-mi' => <<<'MD'
**Kısmen.** GKV (yasal sigorta) öğrenci tarifesi temel diş bakımını kapsıyor ama **premium işlemler kullanıcının ek ödemesi** gerekiyor.

## GKV Standart Diş Kapsamı

✅ **Tam Kapsanan:**
- Yılda 1 kontrol (Kontrolluntersuchung)
- Yılda 1 diş taşı temizleme (PZR — değişebilir, bazı sigortalarda yıllık 60 € geri ödeme)
- Acil ağrı tedavisi
- Basit dolgu (amalgam veya beyaz dolgu **arka dişlerde standart**)
- Kanal tedavisi (basit, ön diş ve yapılabilir bölgelerde)
- Ön diş için **resin kompozit** (beyaz) dolgu

❌ **Kısmen Kapsanan / Ek Ödeme:**
- Beyaz dolgu **arka dişlerde** (sigorta amalgamı kapsıyor, beyaz isterseniz 30-100 € fark öder)
- Implant — **Hiç kapsanmıyor** (1,500-3,000 € cep)
- Kron/köprü — **kısmi katkı (Bonus)** %50-80 (sigortaya göre)
- Ortodonti yetişkin — **kapsanmıyor** (sadece 18 yaş altında)

❌ **Hiç Kapsanmıyor:**
- Beyazlatma (Bleaching)
- Profesyonel temizlik (PZR) — bazı GKV'ler 60 €/yıl katkı veriyor
- Cosmetic/estetik işlemler

## Bonus-Programm (Önemli!)

✅ Yılda 1 kontrole gidersen **"Bonusheft"** kaşelenir
✅ 5 yıl üst üste kontrol → Kron/köprü gerektiğinde **+%15 katkı**
✅ 10 yıl üst üste → **+%30 katkı**
✅ Bonusheft'i kaybetme — pratik tasarruf

## Premium Diş Paketi (Zahnzusatzversicherung)

| Sigorta | Aylık ek prim | Kapsam |
| --- | --- | --- |
| **TK Premium Zahn** | 9-15 € | Beyaz dolgu + PZR yılda 2x + kron/köprü %90 + implant 1,000 €/yıl |
| **AOK Plus** | 12-18 € | Premium kapsam + implant |
| **Barmer Zahn-Extra** | 9-14 € | Aile dostu paket |
| **DA Direkt Zahn-Premium** | 15-25 € | En kapsamlı |

✅ **Öneri:** Öğrenci için **TK Premium Zahn (10 €/ay) çok mantıklı** — yılda 120 €, ama 1 implant 1,500 € kapsama girer.

## Pratik Örnekler

### Senaryo 1: Sade Kontrol + Temizlik
- TK Standart: **0 € cebinden ödeme**
- Yılda 1 kontrol + temizlik (PZR isteğe bağlı, 80-120 € — TK 60 € geri öder)

### Senaryo 2: Dolgu Lazım
- Amalgam (gümüş gri): **0 € cebinden**
- Beyaz dolgu arka dişte: **30-100 € cebinden** (fark ödemesi)

### Senaryo 3: Kanal Tedavisi
- Basit kanal: **0 €**
- Karmaşık kanal (3+ kanal, mikroskop): **100-300 € cebinden**

### Senaryo 4: Akıl Dişi Çekimi
- Standart çekim: **0 €**
- Cerrahi çekim + sedasyon: **50-200 € fark**

### Senaryo 5: Implant
- GKV: **Hiç kapsamıyor**
- Cep: 1,500-3,000 € / implant
- Premium paket varsa: 1,000 € geri ödeme/yıl

## Türkiye'de Diş Tedavisi Stratejisi

⚠️ **Bazı öğrenciler tatil dönemlerinde Türkiye'ye gelip diş yaptırıyor** — Türkiye'de aynı işlem **%50-70 ucuz**
✅ Implant: TR'de 350-700 € | DE'de 1,500-3,000 €
✅ Kanal tedavisi: TR'de 50-100 € | DE'de 150-400 €
✅ Beyazlatma: TR'de 80-150 € | DE'de 250-500 €

⚠️ Acil durumlarda Almanya'da yaptır, planlı işlemler için Türkiye seçeneği değerlendir.

İlgili: [Sigorta aylık ücret](/sss/sigorta/ogrenci-saglik-sigortasi-krankenversicherung-aylik-ne-kadar) | [TK/AOK karşılaştırma](/sss/sigorta/tk-aok-barmer-arasindaki-fark-ogrenci-icin-hangisi).
MD,

            'ogrenci-sigortasi-iptal-etme-sureci-nasil' => <<<'MD'
Öğrenci sigortası iptali için belirli kurallar var — yanlış zamanlama prim ödemeye devam etmeye yol açar.

## İptal Süreci

### 1. Yazılı Bildirim Şart
- E-mail veya posta ile **Kündigung** (iptal) bildirimi gönder
- Hem TK, AOK, Barmer **online portal** üzerinden de iptal kabul ediyor (TK app uygun)

### 2. İptal Süreleri (Kündigungsfrist)
- **18 ay sigortalı kalma zorunluluğu** — yeni bir GKV'ye geçmek için (kanunen)
- 18 ay sonrası iptal süresi: **2 ay önceden bildirim**
- **Sondergrund** (özel sebep) varsa anında iptal:
  - Almanya'dan ayrılıyorsan (taşınma kanıtı)
  - Aile sigortasına geçiyorsan (Familienversicherung kanıtı)
  - Eş Almanya'da çalışıyor + onun sigortasına geçiyorsan

### 3. Belge Gereksinimleri

| Sebep | Gereken belge |
| --- | --- |
| Almanya'dan ayrılış | **Abmeldebescheinigung** (Bürgeramt'tan) |
| Aile sigortasına geçiş | Yeni Familienversicherung kanıtı |
| Yeni GKV'ye geçiş | Yeni sigortanın onay belgesi |
| 18 ay sonra ücretsiz iptal | Sebep göstermek zorunlu değil, ama önceden 2 ay bildirim |

## Senaryolar

### Senaryo 1: Mezun oldun, Türkiye'ye dönüyorsun
1. **Bürgeramt'a git** → Abmeldung yap (Almanya'dan ayrılış kaydı)
2. Abmeldebescheinigung sigorta şirketine gönder (e-mail/posta)
3. Sigorta **Abmeldung tarihinde sona erer**
4. Son ay tam ücret kesilir (gün hesabı yok)

### Senaryo 2: Üniversite değiştiriyorsun, başka şehre taşınıyorsun
- Sigorta otomatik değişmez, **yeni adres bildir**
- Aynı sigortaya devam edebilirsin
- AOK kullanıyorsan **yeni eyalette AOK'a otomatik geçiş** (örn. AOK Bayern → AOK Berlin)

### Senaryo 3: 18 ay sonra başka GKV'ye geçmek
1. Yeni GKV'ye **kayıt yap** (TK, AOK, Barmer vs.)
2. Yeni GKV eski GKV'ye iptal mesajını **otomatik gönderir**
3. Yeni sigortaya geçiş **ayın 1'inde** olur (örn. Ocak 1'de geçersen Aralık sonunda eski iptal)

### Senaryo 4: Mezun + İş bulundu (Werkstudent → tam zamanlı)
- Öğrenci sigortası otomatik **çalışan sigortasına dönüşür**
- İş sözleşmesi başlangıcında otomatik geçiş
- Aynı sigorta şirketinde devam edebilirsin (TK, AOK genelde aynı kişiyi tutuyor)

## Önemli Notlar

⚠️ **İptal süresi yanlış hesaplanırsa** → 1-2 ay ek prim ödenmesi gerek (Kündigungsfrist içinde)
⚠️ **Almanya'dan ayrılırken Abmeldung yapmazsan** → sigorta otomatik kesilmez, prim birikir
⚠️ Vize sigortası (Hanse Merkur, Mawista) → online iptal **daha esnek**, kullanılmayan aylar iade ediliyor

## Pratik Tavsiye

1. **Mezuniyet öncesi 2 ay** sigortayı bilgilendir
2. **Abmeldung yapmadan iptal kabul edilmez** (GKV için)
3. **Online portal** üzerinden başvuru hızlı (TK app, AOK web)
4. **İptal sertifikası iste** — yeni sigorta için kanıt olarak gerekebilir

## Bir Sonraki Adım: Türkiye'ye Dönüş

- Türkiye'de **SGK aktive et** (Almanya'da çalıştıysan emeklilik yıllarını birleştir)
- **Almanca/uluslararası sağlık sigortası** ihtiyacı varsa Türkiye'de **özel sigorta** araştır
- Almanya'ya tekrar dönme ihtimali varsa **sigorta dosyasını sakla** (yeniden kayıt için)

İlgili: [Sigorta aylık ücret](/sss/sigorta/ogrenci-saglik-sigortasi-krankenversicherung-aylik-ne-kadar).
MD,

            'aok-bayerna-ogrenci-olarak-nasil-basvurulur' => <<<'MD'
AOK Bayern, Münih + Bavyera bölgesi öğrencileri için en popüler yasal sağlık sigortası.

## Başvuru Süreci

### Adım 1: Online Başvuru
- **aok.de/bayern** → "Studierende" sekmesi
- Online form doldur:
  - Kişisel bilgiler (pasaport/kimlik)
  - Üniversite bilgileri (TUM, LMU, FAU vs.)
  - Vize/oturum durumu
  - Almanya adresi (Anmeldung yapılmışsa)
- Form gönder → Onay e-mail'i 1-2 iş gününde gelir

### Adım 2: Doğrulama
- AOK Bayern, üniversite kaydını doğrulamak için **immatrikulation belgesi** ister
- Belgeleri e-mail veya **online portal** üzerinden yükle

### Adım 3: Versicherungsbescheinigung
- 3-7 iş günü içinde **sigorta onay belgesi** PDF olarak gelir
- Bu belgeyi üniversiteye sun → kayıt tamamlanır

### Adım 4: Sigorta Kartı (Gesundheitskarte)
- 2-3 hafta içinde **fiziksel kart** postayla gelir
- Üzerinde **versicherte Person** ve **Krankenversichertennummer** yazılı
- Doktor ziyaretinde kart gösterilir (chip okuyucu)

## Gerekli Belgeler

✅ Pasaport kopyası
✅ Vize/oturum belgesi
✅ Üniversite kayıt onayı (immatrikulation)
✅ Anmeldebescheinigung (Bürgeramt'tan)
✅ SteuerID (Anmeldung sonrası gelir, başvuruda olmazsa sonra ekle)
✅ Banka hesap bilgisi (IBAN — aylık prim çekimi için)

## Aylık Prim

- **Genel öğrenci tarifesi:** 131-135 €/ay
- AOK Bayern'in 30 yaş altı öğrenci tarifesi: **130.50 €/ay** (2026)
- **Pflegeversicherung dahil**
- 30 yaş üstü: PKV'ye geçiş zorunlu

## AOK Bayern Avantajları

✅ **Bavyera'da en geniş şube ağı** (Münih, Nürnberg, Würzburg, Augsburg)
✅ **Bavyera Türkische Service** — Türkçe danışmanlık (Münih ve Nürnberg)
✅ **Bonus programı** — yılda 1 kontrol → 1 yıl sonunda 100 € ek katkı
✅ **AOK FitFood** — beslenme danışmanlığı ücretsiz
✅ **Mobil app + online portal**

## Yaygın Sorunlar ve Çözümleri

### Sorun: SteuerID yok
- AOK önce **vorläufige Versicherung** (geçici sigorta) yapar
- SteuerID gelince sigortaya bildir, güncelleme yapılır

### Sorun: Anmeldung yapılmadı
- AOK Bayern **bazen geçici Anmeldung** ile başvuruyu kabul ediyor (özel durum)
- Tam onay için Anmeldung şart

### Sorun: Üniversite henüz kayıt yapmadı
- **Zulassungsbescheid** (kabul mektubu) ile **geçici sigorta** mümkün
- Asıl kayıt sonrası belgeleri günceller

## Diğer Bavyera Sigortaları

- **TK** — modern, dijital odaklı, uluslararası
- **Barmer** — aile + akademisyen dostu
- **DAK-Gesundheit** — alternatif
- **BKK VBU** — Volkswagen şirket fonu (öğrenciler de kayıt olabiliyor, biraz ucuz)

## Sigorta Değiştirme

- 18 ay AOK Bayern'de kal, sonra istediğin sigortaya geç (TK, Barmer vs.)
- AOK Bayern → AOK Berlin/NRW geçiş otomatik (eyalet değişikliği)

## Türkçe Destek

✅ **AOK Türkische Service Bayern:**
- Telefon: AOK Bayern Service-Center → "Türkçe destek" iste
- Adres: AOK Hauptverwaltung Bayern, Carl-Wery-Str. 28, München
- E-mail iletişim Türkçe mümkün

İlgili: [TK/AOK/Barmer karşılaştırma](/sss/sigorta/tk-aok-barmer-arasindaki-fark-ogrenci-icin-hangisi) | [Münih yurt](/sss/yurt/munihte-ogrenci-yurdu-bulmak-ne-kadar-zor).
MD,

            'werkstudent-icin-sigorta-zorunlu-mu' => <<<'MD'
**Evet, her halükarda Almanya'da sigorta zorunlu** — Werkstudent statüsünde olsan da, olmasan da. Ama Werkstudent statüsü **sigorta primi maliyetini değiştiriyor.**

## Werkstudent Statüsü Genel Şartlar

✅ Aktif öğrenci kaydı (immatrikuliert)
✅ Haftalık çalışma süresi **20 saati aşmaz** (semester içi)
✅ Semestre tatilinde **20 saat üstü** mümkün (en fazla 26 hafta/yıl)

## Werkstudent Sigorta Avantajı (En Önemli!)

### Standart Çalışan Sigortası (Normal İşçi)
- Krankenversicherung: %14.6 (yarısı işçi, yarısı işveren)
- Rentenversicherung: %18.6 (emeklilik)
- Arbeitslosenversicherung: %2.4 (işsizlik)
- Pflegeversicherung: %3.4
- **Toplam: yaklaşık %22 maaştan kesinti**

### Werkstudent Sigorta İndirim
✅ **Krankenversicherung'tan muafsın** — öğrenci sigortası devam, ek prim yok
✅ **Arbeitslosenversicherung'tan muafsın**
❌ **Sadece Rentenversicherung (%9.3)** kesilir maaştan
- Net etki: 1,000 € brutto maaş → ~907 € net (sadece emeklilik kesintisi)

## Yıllık Tasarruf Örneği

| Maaş tipi | 1,500 €/ay brutto | Net |
| --- | --- | --- |
| Normal çalışan (Vollzeit) | 1,500 € | ~1,140 € |
| Werkstudent | 1,500 € | ~1,360 € |
| **Tasarruf** | | **+220 €/ay = 2,640 €/yıl** |

## Sigorta Primi Yine Ayrı

⚠️ **Werkstudent statüsü çalışan sigortası kesintisinden muaf tutar** ama:
- **GKV öğrenci tarifesi (131 €/ay)** ödemen gerekiyor
- Bu **öğrencilik şartı** — çalışan olduğun için değil

## Mini-Job (538 € altı) Farklı

✅ **Mini-Job'da sigorta primi sıfır** — işveren yatırır
- Sen sıfır ödüyorsun
- Brut = Net (maaş tam alınır)
- Yıllık 6,456 € kadar Mini-Job ile çalışabilirsin (vergisiz)

⚠️ Mini-Job + Werkstudent kombinasyonu: 538 €/ay altı kazanç → Mini-Job tarifesi geçerli, üstü → Werkstudent tarifesi.

## Yaş 30 Üstü Werkstudent

⚠️ 30 yaş üstü öğrenci → öğrenci tarifesi **bitmiş** olur
- Werkstudent statüsünde de PKV veya Freiwillige GKV ödüyorsun (180-280 €/ay)
- Çalışan sigortası muafiyeti **30 yaş üstü için de geçerli** — ama sigorta primi yine ödenmeli

## Werkstudent Olmazsam (Normal Öğrenci İşi)

- Mini-Job (538 € altı) → sigorta dahil işveren ödüyor
- 538 € üstü kazanç + Werkstudent değilsen → **Normal çalışan sigortası kesilir** (~%22 maaştan)

## Pratik Örnekler

### Örnek 1: BWL Master öğrencisi, 1,200 €/ay Werkstudent
- Sigorta primi: GKV öğrenci tarifesi 131 €
- Maaştan kesinti: %9.3 (emeklilik) → 1,089 € net
- Toplam net + sigorta: 1,089 € - 131 € = **958 € net**

### Örnek 2: Bachelor öğrencisi, 480 €/ay Mini-Job
- Sigorta primi: GKV öğrenci tarifesi 131 € (kendi ödüyor, üniversiteden)
- Maaştan kesinti: 0 € (Mini-Job)
- Toplam net + sigorta: 480 € - 131 € = **349 € net**

### Örnek 3: Yarı zamanlı çalışan (Werkstudent değil), 1,500 €/ay
- Sigorta primi: GKV (çalışan tarifesi) - %7.3 maaştan
- Diğer kesintiler: emeklilik %9.3, işsizlik %1.2, Pflege %1.7
- Toplam ~%22 kesinti → 1,170 € net
- **Sigortayı işveren ödediği için ek prim yok**

## Sonuç

✅ Werkstudent statüsü **emeklilik dışı kesintilerden muaf** → 131 € öğrenci sigortası primi devam
✅ Mini-Job tarafında **sigorta zaten dahil**, ek prim yok
✅ Normal çalışan olarak (Werkstudent değil) çalışırsan **işçi sigortası tarifesi** geçerli

İlgili: [Werkstudent nedir](/sss/is/werkstudent-nedir-kim-basvurabilir) | [Mini-Job 538 € sınırı](/sss/is/mini-job-538eur-siniri-nedir).
MD,

            'sigorta-belgesi-anmeldung-icin-gerekli-mi' => <<<'MD'
**Çoğu Bürgeramt'ta gerekli değil**, ama bazı belediyeler ister. Anmeldung'a giderken hazırlamak güvenli.

## Anmeldung Resmi Gereksinim Listesi

✅ **Pasaport + vize**
✅ **Wohnungsgeberbestätigung** (ev sahibi onayı)
✅ **Anmeldeformular** (önceden doldurulmuş)

❌ Sigorta belgesi → **kanunen Anmeldung için zorunlu değil**

## Pratikte Ne Olur?

### Sigorta sorulmazsa
- Çoğu Bürgeramt direkt 3 belge ister → işlem 15 dakikada biter
- Berlin, Hamburg, Köln, Münih çoğu şubede sigorta sorulmuyor

### Sigorta sorulursa (Ekstra Kontrol Edenler)
Bazı küçük şehirler ve Bavyera bölgesinde memur **"Krankenversicherung yapıldı mı?"** diye sorabilir:
- Vize sigortası (Hanse Merkur, Fintiba) yeterli → PDF veya kart göster
- GKV başvurusu sonrası **Versicherungsbescheinigung** yeterli

⚠️ Soru sorulduğunda **"Henüz yapmadım, ilk hafta yapacağım"** demek yetiyor — memur bunu kabul ediyor genelde, çünkü Almanya'ya yeni gelen herkes için bu durum normal.

## Sigorta Ne Zaman Şart?

✅ **Üniversite kaydı için ZORUNLU** — GKV onay belgesi olmadan immatrikulation yapamazsın
✅ **Vize uzatmada ZORUNLU** — Ausländerbehörde sigorta kanıtı ister
✅ **İş başvurusunda (Werkstudent)** — işveren bazen sorar
✅ **Doktor ziyaretinde** — sigorta kartı yoksa "Privatpatient" sayılırsın (cep ödemesi)

## Strateji

### Almanya'ya Yeni Geldiysen
1. **Hafta 1:** Anmeldung yap (3 belgeyle yeter)
2. **Hafta 2:** GKV başvurusu (TK, AOK, Barmer)
3. **Hafta 3-4:** GKV onay belgesi gelince üniversiteye kayıt

### Vize Sigortası ile Anmeldung
- Vize sigortası (Hanse Merkur, Fintiba, Mawista) Anmeldung'da yeterli — memur sorduğunda göster
- Vize sigortasının **kapsamı** 30K € + Almanya'da geçerli olmalı

### Sigortasız Anmeldung Riski
⚠️ Resmi olarak Anmeldung sigortasız mümkün ama:
- Bürgeramt memuru **özel durum** kabul edebilir veya etmeyebilir
- Memurun ruh haline + bürgeramt'ın kuralına bağlı

## Bazı Belediyelerin Özel Uygulaması

- **Berlin-Pankow Bürgeramt:** Sigorta sorulmaz (genelde)
- **München-Mitte Bürgeramt:** Sigorta sertifikası kontrol edilir (bazen)
- **Köln-Innenstadt:** Sigorta sorulmaz
- **Frankfurt-Mitte:** Sigorta sorulmaz
- **Hamburg-Altona:** Vize sigortası yeterli

⚠️ Önceden **bürgeramt'ın web sitesi** kontrol et — bazı şubeler kendi listelerini yayınlıyor.

## Pratik Tavsiye

✅ Anmeldung randevusuna **vize sigortası PDF'ini telefonla** götür — sorulursa hızlı erişim
✅ GKV'ye Anmeldung **öncesinde** başvurabilirsin — Versicherungsbescheinigung PDF olarak gelir
✅ Bürgeramt'ta soru olursa: **"Kayıt sürecindeyim, GKV başvurum yapıldı, onay bekliyorum"** demek yeterli

İlgili: [Anmeldung evrakları](/sss/anmeldung/anmeldung-icin-gereken-evraklar-nelerdir) | [Vize sigortası](/sss/sigorta/vize-icin-30k-euro-teminatli-sigorta-yeterli-mi).
MD,

            'aile-bireyleri-ogrenci-sigortasina-dahil-edilebilir-mi' => <<<'MD'
**Hayır, öğrenci sigortası tek kişiliktir** — eş ve çocuk **ayrı sigortalanmalı**. Ancak **Familienversicherung (aile sigortası)** ile bazı koşullarda **ücretsiz** kayıt mümkün.

## Familienversicherung Şartları

### Eş için
✅ Almanya'da ikamet
✅ **Aylık kişisel geliri 535 € altı** (2026 sınırı)
✅ Almanya'da yasal sigortalı olmamak (henüz)
✅ Yaş sınırı yok eş için

### Çocuk için
✅ Almanya'da ikamet
✅ Yaş sınırı:
   - **25 yaş altı + öğrenci** → ücretsiz
   - **23 yaş altı + çalışmayan** → ücretsiz
   - **18 yaş altı** → ücretsiz (her durumda)
✅ Aylık kişisel geliri 535 € altı

## Ücretsiz Aile Sigortası Nasıl Açılır?

### Senin GKV'ne kayıt
- Eşin / çocuğun, **senin sigortana ekleniyor** (familienversichert)
- Ek prim ödemen gerekmez — aile sigortası tamamen ücretsiz
- Eş veya çocuk, aynı kart numarasını alır (Sigorta kartı kendisine özel)

### Başvuru Süreci
1. GKV (TK, AOK, Barmer) müşteri hizmetlerini ara
2. **Familienversicherung başvuru formu** iste
3. Şu belgeleri sun:
   - Evlilik cüzdanı (yeminli tercüme + Apostil)
   - Eş/çocuk pasaportu + vize/oturum
   - Anmeldung'u eşin/çocuğun (Almanya'da kayıtlı)
   - Gelir beyanı (eşin maaşı yoksa "0 € beyanı")

⚠️ **Eş çalışıyorsa** (Werkstudent + 535 € üstü gelir varsa) → Aile sigortası **iptal**, eş kendi sigortasına geçmek zorunda.

## Ücretli Alternatifler

### Eş için kendi GKV'si
- Eş çalışıyor + 535 € üstü kazanç → kendi GKV ücreti ödüyor
- Aylık prim: 131-180 € (öğrenci tarifesi varsa) veya çalışan tarifesi (maaşa göre)

### Eş için PKV
- 30 yaş üstü + Almanya'da çalışmıyor → PKV mümkün ama pahalı (200-300 €/ay)
- Aile birleşimi vizesi için PKV bazen istenir

### Çocuk için Özel Sigorta
- PKV altında çocuk sigortası 50-150 €/ay (tarifeye göre)

## Aile Birleşimi Sigorta Stratejisi

### Senin Almanya'da yaşaman → Sen GKV'desin
1. **Eşi/çocuğu davet et** (Familienzusammenführung vizesi)
2. Vize alındıktan sonra Almanya'ya gelirler
3. Anmeldung yapıldıktan sonra → **GKV'ne aile sigortası başvurusu**
4. 1-2 hafta içinde Familienversicherung açılır → ücretsiz

### Eş Werkstudent Çalışacaksa
- 535 € altı (Mini-Job) → aile sigortası kalır, ücretsiz
- 535 € üstü → aile sigortası kapanır, eş kendi sigortasını alır

## Önemli Notlar

⚠️ **Eş çalışan ise (Vollzeit, Mini-Job dışı):** Eş otomatik kendi sigortasına dahil olur
⚠️ **Çocuk üniversite öğrencisi (Almanya'da):** Kendi sigortasını açar (131 €/ay)
⚠️ **Boşanma:** Familienversicherung iptal edilir, eş kendi sigortasına geçer

## Aile Üyesi Geçmişte GKV Yokken

Eş Türkiye'den geliyorsa **vize sigortası** ilk önce yapılmalı:
1. **Hanse Merkur Visa Protect** (eş için ayrı poliçe) → vize için
2. Almanya'ya geldikten sonra **aile sigortasına geçiş** (senin GKV altında)
3. Vize sigortasını iptal et (kullanılmayan ay iadesi)

## Aile Yurdunda Yaşamak

⚠️ Aile sigortası, **aynı adresi paylaşma şartı** yoktur ama ekonomik bağ aranır. Eş başka şehirde yaşıyorsa (örn. öğrenci sen Münih'tesin, eş Berlin'de çalışıyor) — aile sigortası kapanır.

İlgili: [Sigorta aylık ücret](/sss/sigorta/ogrenci-saglik-sigortasi-krankenversicherung-aylik-ne-kadar) | [Yaş 30 üstü](/sss/sigorta/bachelor-baslarken-30-yas-ustuysem-sigortam-degisir-mi).
MD,

            'acil-saglik-durumu-icin-seyahat-sigortasi-ayri-mi' => <<<'MD'
**Almanya içinde:** GKV/PKV acil sağlık durumunu **tam karşılar** → seyahat sigortası gerekmez.
**Almanya dışında:** GKV sınırlı koruma sağlıyor → seyahat sigortası şart.

## Almanya İçinde Acil Durum

✅ **GKV kart yeterli** — Notaufnahme (acil servis) ücretsiz
✅ Ambulans çağrısı (112) ücretsiz
✅ Hastane yatışı: 28 gün/yıl 10 €/gün kullanım payı (sonra ücretsiz)
✅ İlaç: Reçeteli ilaç 5-10 € fark ödemesi

## Avrupa Birliği İçinde (EHIC)

✅ **GKV kartı arkası EHIC** (European Health Insurance Card) — AB ülkelerinde acil sağlık koruması
✅ İspanya, İtalya, Fransa vs. tatildeyken hastane → EHIC ile ödeme
❌ **Yatış + dönüş** maliyetleri tam kapsanmıyor (sadece acil hizmet)

## Almanya/AB Dışında

⚠️ **GKV kapsamı çok sınırlı** veya **hiç yok**
- Türkiye seyahatinde hastalanırsan → GKV kısmi geri ödüyor
- USA, Japonya, Türkiye vs. → ek seyahat sigortası **şart**

## Seyahat Sigortası (Auslandskrankenversicherung)

### Yıllık Paket (Tek Kişi)
| Sigorta | Yıllık prim | Kapsam |
| --- | --- | --- |
| **HanseMerkur Auslandsreisekrankenversicherung** | 18-25 €/yıl | Yılda 56 gün toplam |
| **ADAC Auslandskrankenschutz** | 28-35 €/yıl | Yılda 56 gün, ADAC üyelik dahil |
| **DKV / Allianz** | 35-50 €/yıl | Premium, 90 gün |
| **AOK Auslandsreise** | 30-40 €/yıl | AOK üyelerine özel paket |

### Tek Seyahat (Kısa Dönem)
- 1-2 hafta tatil için: **5-15 €** poliçe
- Online açma kolay (Check24, Verivox karşılaştırma)

## Ne Zaman Gerekli?

✅ **Yıllık paket öner:**
- Türkiye'ye 2x/yıl gidiyorsan
- Avrupa dışı seyahat yapıyorsan (USA, Asia)
- Genç + sık seyahatçi öğrenci

✅ **Tek seyahat sigortası yeter:**
- Yılda 1 kez Türkiye'ye gidiyorsan
- Avrupa içi tatildeyse EHIC yetiyor

## Türkiye Spesifik Durum

⚠️ **Türk vatandaşıysan + Türkiye'ye gidiyorsan:**
- Türkiye'de **SGK** hâlâ kayıtlı olabilirsin (çıkış yapmadıysan)
- SGK + GKV kombinasyonu ile Türkiye'de hastane bedava
- ⚠️ SGK kapatılmışsa veya gecikmişse → ek seyahat sigortası şart

✅ **Önerilen kombinasyon:**
- GKV (Almanya'da)
- HanseMerkur Auslandsreise (18 €/yıl, yıllık 56 gün dünya kapsam)
- Toplam ek maliyet: ~18 €/yıl

## Acil Durum Numaraları (Avrupa'da)

🚨 **112** — Tüm AB'de acil hizmet (yangın, ambulans, polis)
🚨 **116 117** — Almanya hekim acil dışı destek
🚨 **+49 30 11 22 33 44** — Türk Konsolosluk acil hattı (Almanya'da Türk vatandaşları için)

## Hangi Senaryoda Hangi Sigorta?

| Senaryo | Sigorta yeterli mi? |
| --- | --- |
| Almanya'da hastalandın | GKV/PKV ✅ |
| AB ülkesinde tatildeyken | EHIC ✅ (kısıtlı) |
| Türkiye'ye gittin, hastalandın | GKV kısmen + seyahat sigortası ✅ |
| USA seyahatinde acil | Seyahat sigortası şart ✅ |
| Tropikal bölgede (Asya, Afrika) | Premium seyahat sigortası ✅ |

## Önemli

⚠️ **Sigorta poliçesini telefon kapağına yapıştır** — Acil durumda hızlı erişim
⚠️ **Sigorta numarası + alarm hattı** bilinmesi gerekir (genellikle poliçede yazılı)
⚠️ **Çek formu (Schadenformular)** doldurması için belgeler 30 gün içinde sigortaya gönder

İlgili: [Sigorta aylık ücret](/sss/sigorta/ogrenci-saglik-sigortasi-krankenversicherung-aylik-ne-kadar).
MD,

            'master-ogrencisinin-sigortasi-lisanstan-farkli-mi' => <<<'MD'
**Hayır, master öğrencisi de lisans öğrencisi gibi GKV öğrenci tarifesinden faydalanır** — aynı kurallar geçerli. Sadece yaş sınırı (30 yaş üstü) farklı sigorta tarifelerine geçişe sebep olabilir.

## Master Öğrencisi Sigorta Kuralları

### Aynı Olan Şeyler
✅ **30 yaş altı:** Öğrenci tarifesi (~131 €/ay) — Lisansla aynı
✅ **30 yaş üstü:** PKV (özel) zorunlu (~230-280 €/ay)
✅ **Pflegeversicherung dahil**
✅ Sigorta şirketi seçimi aynı (TK, AOK, Barmer vs.)

### Farklı Olabilen Şeyler
⚠️ **Yaş 30 üstü ihtimali yüksek** — master genelde 23-32 yaş arası başlatılır
⚠️ **İş gelirinin Werkstudent tarifesine etkisi** — master öğrencisi daha sık çalışıyor (Werkstudent + 1,000+ €/ay)
⚠️ **Aile sigortası (Familienversicherung)** — master öğrencisinin eşi/çocuğu için aynı kurallar geçerli

## 30 Yaş Üstü Master Öğrencisi

### Senaryolar

#### Senaryo 1: 30+ Yaşında Master Başlat
- **Öğrenci tarifesi bitmiş** — PKV (özel sigorta) veya Freiwillige GKV
- Aylık prim: 180-280 €

#### Senaryo 2: 28 Yaşında Bachelor Bitti, 30 Yaşında Master
- Bachelor süresince öğrenci tarifesinde
- Master başlangıcında **29 yaş** → hala öğrenci tarifesi
- 30 doğum gününden sonra **PKV geçişi**

#### Senaryo 3: Yaş 30 Öncesi Master Bittikten Sonra İşe Geçiş
- Bachelor + Master tamamlandı, mezun olduktan sonra **Werkstudent değil tam zamanlı işçi** oldun
- Çalışan sigortasına otomatik geçiş

## Master Öğrencisinin Avantajları

### Werkstudent Statüsü Daha Etkin
- Master öğrencisi **Werkstudent olarak haftalık 20 saate kadar çalışabilir**
- Bu sürede **çalışan sigortası muafiyeti** geçerli (sadece emeklilik kesilir)
- Lisans öğrencisi de aynı haktan faydalanıyor, ama master öğrenci genelde **daha yüksek maaş** bekliyor

### Aylık Aile Bütçesi
- Master öğrencisi **eşli / çocuklu** olabilir
- Familienversicherung ile aileyi ücretsiz sigortalayabilir (gelir 535 € altı şartı)

## Master İçin Sigorta Stratejisi

### 30 Yaş Altıysan:
- **TK Studierende** veya **AOK Bayern/Berlin Studierende** — öğrenci tarifesi (~131 €/ay)
- **Bonus programı** kullan (yılda 1 kontrol → ileride kron/köprü için %15+ ek katkı)

### 30 Yaş Üstüysen:
✅ **Freiwillige GKV** seç:
- Geri dönüşü mümkün (Türkiye'ye dönersen Almanya'ya tekrar dönüşte GKV açılabilir)
- Aile sigortası kullanılabilir
- Maliyet PKV'den ucuz (~160-200 €/ay)

❌ **PKV'ye dikkat:**
- 55 yaş üstüne kadar GKV'ye geri dönmek **imkansız**
- Türkiye'ye dönersen Almanya'ya tekrar dönüşte PKV başvurusu açılabilir, GKV yeniden mümkün değil
- Master sonrası Almanya'da çalışmayı düşünüyorsan PKV uzun vadeli pahalı

## Master Sonrası Sigorta Geçişi

### Mezun + İş Bulundu (Tam Zamanlı)
- Öğrenci sigortası → çalışan sigortası **otomatik geçiş**
- Maaştan %14.6 GKV (yarısı işveren ödüyor → %7.3 net kesinti)
- Aynı sigorta şirketinde devam edebilirsin

### Mezun + İş Aramaya Devam (Job Search Visa)
- 18 ay süresince Almanya'da kalabilirsin
- Sigorta zorunlu: Öğrenci tarifesi sona erer
- **Freiwillige GKV** veya kısa süreli **vize sigortası** (Hanse Merkur)
- Aylık ~160-180 €

### Mezun + PhD Başlat
- PhD öğrencisi statüsü → GKV öğrenci tarifesi devam (yaş 30 altı)
- 30 yaş üstü PhD → Freiwillige GKV veya PKV

## Master Süresince Sık Sorulan Sorular

### "Master 1.5 yıl uzadı, sigortam değişir mi?"
- Master süresi uzasa bile **öğrenci kaydın aktif olduğu sürece** sigorta devam
- 14. dönemden sonra (max 7 yıl öğrencilik) sigorta şirketi sorgulayabilir

### "Master + Werkstudent paralel — sigorta kim öder?"
- Sen ödüyorsun (GKV öğrenci tarifesi 131 €/ay)
- İşverenin sigorta katkısı yok (Werkstudent için)

İlgili: [Werkstudent sigorta](/sss/sigorta/werkstudent-icin-sigorta-zorunlu-mu) | [Yaş 30 üstü](/sss/sigorta/bachelor-baslarken-30-yas-ustuysem-sigortam-degisir-mi).
MD,
        ];
    }

    private function bursAnswers(): array
    {
        return [
            'daad-bursu-nasil-alinir-basvuru-sureci-nedir' => <<<'MD'
**DAAD** (Deutscher Akademischer Austauschdienst), Almanya'nın resmi akademik değişim örgütüdür. Türkiye'den her yıl yüzlerce öğrenciye burs veriyor.

## Başvuru Süreci (Adım Adım)

### 1. Burs Kategorisini Seç
- **Master Studies in Germany** (Master öğrenimi)
- **PhD in Germany** (Doktora)
- **Research Grants** (Araştırma)
- **Re-Invitation for Former Scholarship Holders** (Eski bursiyer geri davet)
- **Summer Schools** (Yaz okulları)

### 2. DAAD Portal'a Kayıt
- *daad.de/scholarship-database* üzerinden burs ara
- Kişisel hesap aç (e-mail + şifre)
- Profil tamamla (eğitim geçmişi, sertifikalar, fotoğraf)

### 3. Üniversite Kabulü
✅ **Master/PhD için:** Önce Alman üniversitesine kabul al
✅ **Araştırma bursu için:** Profesörle e-mail iletişimi (Forschungsexposé hazırla)

### 4. Online Başvuru
- *daad.de/online-application* (DAAD Portal)
- Şu belgeleri yükle:
  - Motivasyon mektubu (3-5 sayfa)
  - CV (Europass formatı önerilir)
  - Diploma + transkript (yeminli tercüme)
  - 2-3 referans mektubu (üniversite profesörlerinden)
  - Almanca/İngilizce dil sertifikası
  - Üniversite kabul mektubu (Master/PhD için)
  - Forschungsexposé (PhD için)

### 5. Başvuru Süresi
- **Master Studies:** Genelde Eylül-Ekim arası (sonraki ekim dönemine)
- **PhD:** Mart, Mayıs, Eylül - farklı kategoriler
- Geç başvuru kabul edilmez

### 6. Seçim Süreci
- **1. Aşama:** Belge incelemesi (3-4 ay)
- **2. Aşama:** Mülakat (Skype/Zoom üzerinden, Türkiye'de DAAD ofisi)
- **3. Aşama:** Final değerlendirme (1-2 ay)
- **Toplam süre:** Başvurudan kabul mektubuna **6-8 ay**

## Burs Kapsamı (Master için 2026)

✅ **Aylık 992 €** stipendium (Sperrkonto'yu karşılıyor)
✅ **Sağlık sigortası primi**
✅ **Tek seferlik 2,400 € gemi taşıma ücreti**
✅ **Almanca kursu** (gerekirse, Almanya'da 6 aya kadar)
✅ **Çalışma + araştırma harçlığı** 460 €/yıl

## Başarı Stratejisi

### Yüksek Başarı Şansı için:
✅ **Not ortalaması:** Lisans GPA 3.0/4.0+ (Türkiye notu ~85+)
✅ **Almanca seviyesi:** B2 minimum (master için), C1+ önerilen
✅ **İngilizce:** IELTS 6.5 / TOEFL 90
✅ **Motivasyon mektubu** çok detaylı — neden Almanya, neden bu üniversite, kariyer planları
✅ **Referans mektupları** akademik (rektör/dekan/profesörden)
✅ **Yayın/proje deneyimi** (master için bile)
✅ **Stage geçmişi** (özellikle araştırma bursu için)

### Yaygın Başarısızlık Sebepleri
❌ Motivasyon mektubu genel/kişiliksiz
❌ Referans mektubu profesörel olmayan (komşu/aile dostu)
❌ Almanca/İngilizce sertifikası eksik
❌ Geçmişte iş tecrübesi belirtilmemiş

## Türkiye'de DAAD Ofisi

**DAAD Information Center İstanbul:**
- Adres: Levent / İstanbul
- Tel: +90 212 248 09 88
- E-mail: ic.istanbul@daad.de
- Web: ic.daad.de/istanbul

**DAAD Lektörlüğü (Üniversitelerde):**
- Boğaziçi, ODTÜ, Bilkent, İstanbul Teknik, Hacettepe vs.
- Lektörler başvuru sürecinde destek veriyor

İlgili: [DAAD belgeler](/sss/burs/daad-basvurusu-icin-hangi-belgeler-gerekli) | [DAAD tarihleri](/sss/burs/daad-burs-basvuru-tarihleri-ne-zaman) | [DAAD aylık tutar](/sss/burs/master-icin-daad-bursu-aylik-ne-kadar).
MD,

            'daad-basvurusu-icin-hangi-belgeler-gerekli' => <<<'MD'
DAAD bursu başvurusu için belgeler **kategoriden kategoriye değişir**, ama temel belge paketi genelde aynı.

## Genel Zorunlu Belgeler

### Kişisel
✅ **Pasaport kopyası** (vize bilgileri)
✅ **Biyometrik fotoğraf** (taranmış, 4 adet)
✅ **CV** (Europass formatı önerilir, 2 sayfa)
✅ **Doğum belgesi** (yeminli tercüme + Apostil)

### Eğitim
✅ **Lise diploması + transkript** (yeminli tercüme + Apostil)
✅ **Üniversite diploması + transkript** (Bachelor için - varsa)
✅ **Anabin denklik belgesi** (gerekirse)

### Dil Sertifikaları
✅ **Almanca:** TestDaF, Goethe-Zertifikat C1, DSH, telc B2+ (master için B2 yeter)
✅ **İngilizce:** IELTS 6.5+, TOEFL iBT 90+ (eğer programın İngilizce ise)
✅ **Türkiye sınavları:** YDS 80+, e-YDS, ÖSYM YÖKDİL — DAAD'da kabul edilir bazı kategoriler

### Akademik Materyaller
✅ **Motivasyon mektubu** (3-5 sayfa, Almanca/İngilizce)
✅ **Referans mektupları (2-3 adet)** — üniversite profesörlerinden, mühürlü
✅ **Üniversite kabul mektubu** (Master/PhD için — Zulassungsbescheid)
✅ **Çalışma planı** (Master) veya **Forschungsexposé** (PhD)

## Kategoriye Göre Ek Belgeler

### Master Studies in Germany (1 Yıllık Burs)
- Bachelor diplomasının yeminli tercümesi
- Bachelor son sınıfta isen mezuniyet beyanı (transcript)
- Almanya'da master programının kabul mektubu

### PhD in Germany (3-4 Yıllık Burs)
- Master diplomasının yeminli tercümesi
- **Forschungsexposé** (5-15 sayfa, Almanca/İngilizce):
  - Araştırma konusu
  - Soru-cevap planı
  - Yöntem (methodology)
  - Zaman planı
  - Beklenen sonuçlar
- Alman profesör desteği (Betreuungszusage)

### Research Grants
- Araştırma planı (10-20 sayfa)
- Alman üniversitesinde araştırmacı kabul mektubu
- Önceki yayın listesi (varsa)

## Tercüme + Apostil Gereksinimleri

⚠️ **Tüm belgeler Almanca veya İngilizce olmalı**

| Belge | Tercüme şart mı? | Apostil şart mı? |
| --- | --- | --- |
| Pasaport | Hayır | Hayır |
| Doğum belgesi | Evet (yeminli) | Evet |
| Diploma | Evet (yeminli) | Evet |
| Transkript | Evet (yeminli) | Evet |
| Sabıka kaydı | Evet | Evet |
| Dil sertifikası | Hayır (zaten İngilizce/Almanca) | Hayır |

## Türkiye'de Belge Hazırlama Süreci

1. **Diploma/Transkript:**
   - Üniversitenden orijinal alınır
   - Yeminli tercüman → Türkçe → Almanca/İngilizce
   - Kaymakamlık/Valilik Apostil
   - Toplam süre: 1-2 hafta
   - Maliyet: ~150-300 €

2. **Sabıka Kaydı:**
   - E-Devlet üzerinden (5 dakika)
   - Yeminli tercüman → Almanca
   - Geçerlilik: 6 ay
   - Maliyet: ~25-40 €

## Belge Yükleme

✅ **DAAD Portal** üzerinden PDF olarak yükle
✅ Dosya boyutu max **2 MB** her belge (kontrol et)
✅ Belge isimlendirme: "Soyad_Belge_Türü.pdf" (örn. "Yilmaz_Diploma.pdf")
✅ **Belge eksik gönderirsen başvuru reddedilir** — çift kontrol şart

## Sık Yapılan Hatalar

❌ Referans mektubu **profesörel olmayan kişiden** (komşu, aile dostu)
❌ Motivasyon mektubu **DAAD'a özel değil** (kalıp metin)
❌ Dil sertifikası **expire olmuş** (TestDaF 2 yıl, Goethe 1 yıl geçerli)
❌ Apostil yok → belgeler **Almanya'da geçersiz**

İlgili: [DAAD başvuru süreci](/sss/burs/daad-bursu-nasil-alinir-basvuru-sureci-nedir) | [Referans mektubu](/sss/burs/burs-icin-referans-mektubu-nasil-yazilir).
MD,

            'burs-icin-yas-siniri-var-mi' => <<<'MD'
**Genelde evet** — çoğu Alman bursu için yaş sınırı var ama esnek uygulamalar mümkün.

## DAAD Yaş Sınırları (2026)

| Burs türü | Maksimum yaş (başvuruda) |
| --- | --- |
| **Master Studies in Germany** | Genelde 32 (Master bittiğinde 33-34) |
| **PhD in Germany** | Genelde 36 |
| **Research Grants for Faculty** | 45 (esnek) |
| **Research Grants for Researchers** | 32 (Master sonrası 5 yıl içinde) |

✅ DAAD'da yaş sınırı **kesin red sebebi değil** — geçerli sebep varsa (kariyer kesintisi, sağlık vs.) esnek uygulanabilir.

## Diğer Almanya Bursları Yaş Sınırları

### Politik Vakıf Bursları
| Vakıf | Master | PhD |
| --- | --- | --- |
| **Konrad Adenauer Stiftung (KAS)** | 30 | 35 |
| **Friedrich Ebert Stiftung (FES)** | 32 | 36 |
| **Heinrich Böll Stiftung** | 35 | 40 |
| **Hanns Seidel Stiftung** | 30 | 35 |
| **Studienstiftung des Deutschen Volkes** | 35 | 40 |
| **Friedrich Naumann Stiftung** | 30 | 35 |

### Dini Vakıf Bursları
- **KAAD (Katolik):** 32 yaş — Master | 35 yaş — PhD
- **Evangelisches Studienwerk:** 30 yaş — Master
- **Cusanuswerk (Katolik):** 28 yaş — Master

### Excellence Initiative Bursları
- **DAAD Helmut-Schmidt-Programme:** 35 yaş
- **Erasmus+:** Genelde yaş sınırı yok ama uygun yaşta öğrenci seçilir

### Üniversite Bursları
- Her üniversite kendi kuralı koyar
- TUM, RWTH, Heidelberg, LMU — genelde 32-35 yaş üst sınır
- Bazı master programları **yaş sınırsız** (özellikle profesyonel master)

## Yaş Sınırı Esnekliği

✅ **Kariyer kesintisi mazeret olarak kabul:**
- Aile sorumluluğu (çocuk bakımı, hasta bakımı)
- Askerlik (Türkiye'de zorunlu - 32-36 yaş arası mümkün)
- Sağlık sorunları
- Uluslararası iş tecrübesi

✅ **Önceki diploma sonrası 5 yıllık kural:**
- Lisans bitiminden sonra **5 yıl içinde master başvurusu** uygundur
- Bu kural ile 27 yaşında lisans biten kişi 32 yaşına kadar master başvurabilir

## Yaş Yüksek (35+) İçin Strateji

### Önerilen Bursar Kategorileri
✅ **DAAD Re-invitation** — eski DAAD bursiyer iken Almanya'ya tekrar dönen (yaş esnek)
✅ **Postdoctoral Fellowships** — PhD sonrası araştırma (yaş 50'ye kadar)
✅ **Visiting Researcher** — kısa süreli araştırma değişimi
✅ **Sandwich Programme** — Türkiye'de PhD + Almanya'da 6-12 ay (yaş esnek)
✅ **EU Erasmus+ Mundus** — kapsamlı master programları (yaş sınırı yok)

### Yaş Sınırı Olmayan Burslar
✅ **Bazı şirket bursları** (Bayer, SAP, Bosch) — yaş sınırı yok
✅ **TÜBİTAK 2214-A (Türkiye)** — yaş sınırsız PhD bursu
✅ **TEV bursları** — yaş esnek
✅ **Erasmus+ Staff Exchange** — akademisyenler için yaş sınırsız

## Pratik Tavsiye

### 28+ Yaşındaysan:
1. **DAAD Master Studies'e başvur** — yaş sınırı esnek, kariyer kesintisi mazeret olabilir
2. **Politik vakıf** karşılaştır — KAS 30, FES 32, Böll 35
3. **TÜBİTAK 2214-A** (Türkiye'den) — yaş sınırsız, Almanya araştırma için

### 35+ Yaşındaysan:
1. **Postdoctoral** kategoriler hedef al
2. **Re-invitation** (önceki DAAD bursiyer iken Almanya'ya tekrar dönen)
3. **Sandwich Programme** (Türkiye'de PhD + Almanya'da kısmi)

⚠️ **Yaş sınırı katı uygulanmıyor** — başvuru her durumda yapılır, sebep gösterirsen değerlendirilir.

İlgili: [PhD için burslar](/sss/burs/phd-icin-almanya-burslari-nelerdir) | [Konrad Adenauer](/sss/burs/konrad-adenauer-vakfi-bursu-nasil-alinir).
MD,

            'daad-kisa-sureli-3-6-aylik-burs-imkanlari-neler' => <<<'MD'
DAAD'ın **kısa süreli (3-6 ay) burs** seçenekleri özellikle araştırma, yaz okulu ve değişim programları için uygun.

## Kısa Süreli Burs Kategorileri

### 1. Research Stays for University Academics and Scientists
- **Süre:** 1-3 ay
- **Hedef:** Doktora sahibi araştırmacılar
- **Aylık tutar:** 2,070-2,400 € (akademik seviyeye göre)
- **Başvuru:** Tüm yıl açık

### 2. Short-Term Research Grants for Doctoral Candidates
- **Süre:** 1-6 ay
- **Hedef:** Türkiye'de PhD yapan, Almanya'da kısmi araştırma yapmak isteyenler
- **Aylık tutar:** 992 € + sağlık sigortası
- **Başvuru:** Mart, Mayıs, Eylül - 3 dönem

### 3. Sandwich Programme
- **Süre:** 6 ay - 1 yıl
- **Hedef:** Türkiye'de PhD öğrencisi + Almanya'da araştırma dönemi
- **Aylık tutar:** 992 € + sağlık + gemi ücreti
- **Başvuru:** Yıllık 2 kez

### 4. International Summer Course Programme (Yaz Okulu)
- **Süre:** 3-4 hafta (yoğun)
- **Hedef:** Almanca öğrenmek, kültürel etkinlik
- **Aylık tutar:** 1,000-1,500 € (tek seferlik paket)
- **Başvuru:** Aralık-Ocak (sonraki yaz için)

### 5. University Summer Course Grant
- **Süre:** 3-4 hafta yaz okulu
- **Hedef:** Lisans/master öğrencisi, dil + alan dersi
- **Aylık tutar:** 1,061 € (paket)
- **Başvuru:** Aralık-Ocak

### 6. Re-invitation for Former Scholarship Holders
- **Süre:** 1-3 ay
- **Hedef:** Daha önce DAAD bursu almış kişiler
- **Aylık tutar:** 2,070 €
- **Başvuru:** Tüm yıl açık

### 7. Working Internships in Science and Engineering (WISE)
- **Süre:** 2-3 ay yaz dönemi
- **Hedef:** Doğa bilimleri, mühendislik lisans öğrencileri
- **Aylık tutar:** 1,061 € + ulaşım
- **Başvuru:** Ekim-Aralık

## Kısa Süreli Burs Avantajları

✅ **Rekabet daha az** — uzun süreli burslara göre
✅ **Almanya'da deneyim kazanma** — sonra uzun süreli için daha avantajlı
✅ **CV güçlendirme** — uluslararası deneyim
✅ **Hızlı karar** — başvurudan kabul'a 2-4 ay
✅ **Vize sürecinde kolaylık** — kısa süreli vize (Schengen)

## Başvuru Süreci

1. *daad.de* → Scholarship Database
2. "Short-term" veya "Summer course" filtresi
3. Online başvuru + belgeler
4. Mülakat (genelde Skype, Türkiye'de DAAD ofisi)
5. Karar: **2-4 ay**

## Önemli İpucu

⚠️ **Kısa süreli burs uzun süreli bursa geçit olabilir:**
- WISE bursunda iyi referans aldıysan → sonraki yıl DAAD Master Studies başvurusunda avantaj
- Sandwich Programme'ı tamamladıysan → DAAD PhD başvurusunda öncelik
- Yaz okulu deneyimi motivasyon mektubunda kullanılabilir

## Kombinasyon Stratejisi

✅ **3-4 hafta yaz okulu** (lisans 1-2. yıl)
→ **6 ay sandwich** (master sırasında)
→ **2 yıl DAAD Master Studies** (master için)
→ **3-4 yıl DAAD PhD**

Her aşamada DAAD ağına katılarak referans kazanırsın.

İlgili: [DAAD Master burs](/sss/burs/master-icin-daad-bursu-aylik-ne-kadar) | [DAAD başvuru](/sss/burs/daad-bursu-nasil-alinir-basvuru-sureci-nedir).
MD,

            'master-icin-daad-bursu-aylik-ne-kadar' => <<<'MD'
DAAD Master Studies in Germany bursu (2026 sonbahar dönemi itibarıyla):

## Aylık Tutar

### Standart Master Studies Burs (Türkiye'den başvuru)
✅ **Aylık 992 €** stipendium
- Sperrkonto gereksinimini tam karşılıyor (aylık 992 € hedef)
- Yıllık: 11,904 €

### Burs Paketinin Tamamı

| Kalem | Tutar | Yıllık |
| --- | --- | --- |
| **Aylık stipendium** | 992 € | 11,904 € |
| **Sağlık sigortası** | ~131 € (DAAD ödüyor) | ~1,572 € |
| **Pflegeversicherung** | Dahil | Dahil |
| **Tek seferlik gemi ücreti** | 2,400 € | 2,400 € (1x) |
| **Almanca kursu desteği** (ihtiyaç halinde) | 6 ay'a kadar | Maksimum 2,000 € |
| **Araştırma + materyal** | 460 €/yıl | 460 € |
| **Eş + çocuk için aile yardımı** | 276 €/ay eş + 184 €/ay çocuk | (varsa) |
| **Toplam tahmini yıllık değer** | | **~16,500 € (eş/çocuk yoksa)** |

## Hangi Kalemler Net?

✅ **992 €/ay** — banka hesabına direkt yatar
✅ **Sağlık sigortası** — DAAD direkt sigorta şirketine öder, senden kesilmez
✅ **Gemi ücreti** — Almanya'ya gelişte ödeme (Türkiye'den 2,400 €)

## DAAD Bursu vs Sperrkonto Karşılaştırma

| | Sperrkonto | DAAD Bursu |
| --- | --- | --- |
| Yıllık tutar | 11,904 € | 11,904 € (aynı) |
| Aylık | 992 € | 992 € |
| Sağlık sigortası | Ek 1,572 €/yıl | Dahil |
| Gemi ücreti | Sen ödüyorsun | 2,400 € DAAD |
| Vize | Sperrkonto kanıt | Burs kanıt |
| **Cep maliyeti** | ~13,500 €/yıl + gemi | **0 €** |

✅ **DAAD bursu ile yıllık 16,500 € tasarruf** etkin olarak.

## Aile İçin Ek Yardım

### Eş için (Türkiye'de veya Almanya'da)
- Eş başvuranla Almanya'da yaşıyorsa: **276 €/ay**
- Eş Türkiye'de kalıyorsa: yardım yok

### Çocuk için
- 1 çocuk: **184 €/ay**
- 2 çocuk: **368 €/ay** (toplam)
- 3+ çocuk: artan tarife

⚠️ Aile birleşimi vizesi DAAD bursiyer için kolaylaştırılmış — DAAD sponsor olarak kabul edilir.

## Almanca Kursu Desteği

✅ **Burs öncesi 2 ay Almanya'da yoğun kurs** (genellikle Goethe-Institut)
✅ Kurs ücretini DAAD ödüyor (tipik 1,500-2,000 €)
✅ Konaklama + yemek dahil paketler var

⚠️ Almanca seviyeniz B2 altıysa kurs **zorunlu**, üstündeyse **isteğe bağlı**.

## Net Karşılaştırma — DAAD vs Diğer Burslar

| Burs | Aylık | Yıllık | Avantaj |
| --- | --- | --- | --- |
| **DAAD** | 992 € | 11,904 € | En kapsamlı, prestij |
| **KAS (Konrad Adenauer)** | 957 € | 11,484 € | Politik network |
| **FES (Friedrich Ebert)** | 957 € | 11,484 € | SPD network |
| **KAAD (Katolik)** | 957 € | 11,484 € | Dini içerik |
| **Heinrich Böll** | 1,100 € | 13,200 € | Yeşil/feminist |
| **Erasmus+ Master** | 1,000-1,400 € | 12,000-16,800 € | AB'de değişim |

## DAAD Bursunun Süresi

✅ **Master programı süresince** (1-2 yıl)
✅ **6 ay önceden Almanca kursu** dahil (toplam 1.5-2.5 yıl)
✅ Master uzayan sürelerde 6 ay ek uzatma mümkün (akademik sebep)

## Önemli Notlar

⚠️ **DAAD bursu vergiden muaf** (Türkiye'de de Almanya'da da)
⚠️ Burs sırasında **ek iş yapmak yasak değil**, ama haftada 20 saat sınırı geçerli
⚠️ Burs süresince **akademik performans şartı** — derslere %80 katılım + 1. dönem geç kalmadan başarı

İlgili: [DAAD başvuru](/sss/burs/daad-bursu-nasil-alinir-basvuru-sureci-nedir) | [Yıllık yaşam maliyeti](/sss/para/almanya-ogrenci-yillik-yasam-maliyeti-ortalama-kac-euro).
MD,

            'burs-ile-dil-kursunu-birlikte-alabilir-miyim' => <<<'MD'
**Evet, çoğu Alman bursu Almanca/İngilizce kursu desteği içerir** — özellikle DAAD ve büyük politik vakıflar.

## DAAD Kurs Desteği

### Master/PhD Bursiyeler İçin
✅ **Burs öncesi 2-6 ay Almanya'da yoğun Almanca kursu**
✅ DAAD ödüyor (Goethe-Institut, Carl-Duisberg veya benzer kurum)
✅ Kurs ücreti: 1,500-3,000 € (tamamen DAAD'da)
✅ Konaklama + yemek desteği dahil
✅ A1'den C1'e kadar kurs çeşidi mevcut

### Süre Sınırları
- **Master için:** 6 aya kadar Almanca kurs
- **PhD için:** 2 ay yeterli (çoğu PhD İngilizce)
- **Araştırma bursu için:** Genelde 1-2 ay
- **Re-invitation:** Ek kurs yok (önceki bursiyer için)

### Kabul Edilen Kurumlar
✅ **Goethe-Institut** (Berlin, München, Frankfurt, Hamburg)
✅ **Carl-Duisberg Centren** (Bonn, Berlin)
✅ **IIK (Internationales Institut Köln)**
✅ **Eurasia Institute**

## Diğer Bursların Kurs Desteği

### KAAD (Katolik)
✅ Burs öncesi 2-4 ay Almanca kursu
✅ Tam ödenir + konaklama

### KAS (Konrad Adenauer)
✅ 6 aya kadar dil kursu desteği
✅ İngilizce + Almanca kombinasyonu mümkün

### Heinrich Böll
✅ 6 aya kadar Almanca kursu
✅ Esneklik yüksek

### Friedrich Ebert (FES)
✅ 6 aya kadar dil kursu
✅ Konaklama + yemek desteği

### Cusanuswerk (Katolik akademik vakıf)
✅ Almanca kursu + akademik destek
✅ Mentorluk programı

## Süreç

1. **Burs kabul mektubu** geldiğinde Almanca seviyeni belirt
2. DAAD/vakıf seni uygun kursa yerleştirir
3. Almanya'ya **kurs için vize** ile gel (öğrenci vizesi öncesi)
4. Kurs tamamlandıktan sonra üniversite kaydı + Aufenthaltstitel

## Yetersiz Almanca ile DAAD Başvurusu

⚠️ DAAD'a başvuru için **minimum B1 (master) veya B2 (PhD)** istenir
⚠️ Başvuru sırasında **C1 sertifikası yoksa**, kurs desteği zaten dahil
⚠️ "Almancam yetersiz" diye reddedilmezsin — burs kursu içerir

## Strateji

### Senaryo: Lisans bitti, Almancam A2
1. **DAAD Master Studies başvurusu yap** (A2 yeterli kabul için)
2. Kabul gelirse → **6 ay yoğun Almanca kurs DAAD ile**
3. Kurs sonunda B2+ → master derslere başla
4. Toplam süreç: Başvuru + 6 ay kurs + 2 yıl master = ~3 yıl

### Senaryo: Lisans bitti, Almancam C1 hazır
1. DAAD başvurusu yap, **kurs desteği gerek yok**
2. Direkt master programa başla
3. Burs süresince mevcut C1 seviyesi geliştirilir

## Önemli Notlar

⚠️ **Türkiye'de Almanca öğrenmek + DAAD kabul** alternatifi mümkün:
- TR'de Goethe / DeutschAkademie / TR ÖSYM YDS hazırlığı
- DAAD başvurusunda **bu sertifikalar gösterilir**
- Yine de DAAD'da kurs desteği teklif edilebilir (B2 seviyesini güçlendirmek için)

⚠️ **Erasmus+'ta dil kursu daha sınırlı** — Erasmus exchange programları kurs içermez, ama üniversite kendi kursları sunar (ücretsiz)

İlgili: [DAAD başvuru](/sss/burs/daad-bursu-nasil-alinir-basvuru-sureci-nedir) | [Goethe kurs](/sss/para/goethe-yogunlastirilmis-kurs-1200eurya-deger-mi).
MD,

            'erasmus-ile-daad-bursu-karsilastirmasi' => <<<'MD'
Erasmus+ ve DAAD bursu farklı amaçlara hizmet ediyor — **birbirinin alternatifi değil, farklı kategori**.

## Hızlı Karşılaştırma

| Özellik | Erasmus+ | DAAD |
| --- | --- | --- |
| **Tip** | Değişim programı | Tam burs |
| **Süre** | 3-12 ay | 1-4 yıl |
| **Aylık tutar** | 350-700 € | 992 € (master), 1,200 € (PhD) |
| **Hedef** | Sürekli değişim deneyimi | Tam diploma alımı |
| **Uygulanan kurum** | AB ülkeleri arası | Almanya'ya özel |
| **Başvuru kaynağı** | Türk üniversitesi | DAAD direkt |
| **Geri dönüş zorunlu** | Evet (genelde) | Hayır |
| **Yaş sınırı** | Yok (ama genç) | 32-36 |

## Erasmus+ Özellikleri

### Erasmus+ Studies (En Yaygın)
- **Süre:** 3-12 ay
- **Amaç:** Türk üniversitende kayıtlıyken kısa süreli Almanya'da öğrenim
- **Tutar:** Aylık 350-540 € (program ücretsiz, sadece yaşam desteği)
- **Avantaj:** Türk üni diploması alıyorsun, Almanya kısa deneyim katar
- **Dezavantaj:** Almanya'da tam yaşam masrafı için yetersiz

### Erasmus Mundus Joint Master Degrees
- **Süre:** 2 yıl
- **Amaç:** 2-3 farklı AB ülkesinde master yapma
- **Tutar:** Aylık 1,400 € + ulaşım + öğrenim + sigorta
- **Avantaj:** DAAD'a yakın (hatta üstün) destek
- **Dezavantaj:** Çok rekabetçi (yıllık 50-100 öğrenci/program)

### Erasmus+ Mobility for Trainees (Staj)
- **Süre:** 2-12 ay
- **Amaç:** Almanya'da staj
- **Tutar:** Aylık 400-650 €
- **Avantaj:** Profesyonel deneyim
- **Dezavantaj:** Sadece kayıtlı öğrencilere

## DAAD Özellikleri

### DAAD Master Studies in Germany
- **Süre:** 1-2 yıl (master tamamı)
- **Tutar:** 992 €/ay + sigorta + gemi
- **Avantaj:** Tam master alımı + kapsamlı destek
- **Dezavantaj:** Çok rekabetçi (yıllık ~50 öğrenci Türkiye'den)

### DAAD PhD in Germany
- **Süre:** 3-4 yıl
- **Tutar:** 1,200 €/ay + sigorta + ek katkılar
- **Avantaj:** Yüksek akademik özgürlük
- **Dezavantaj:** Çok teknik başvuru (Forschungsexposé)

### DAAD Research Grants
- **Süre:** 1-12 ay (kategoriye göre)
- **Tutar:** 992-2,400 €/ay
- **Avantaj:** Sandwich programmes ile Türkiye'de PhD + Almanya'da araştırma

## Hangi Senaryoda Hangi Burs?

### Senin Profilin → Önerilen Burs

| Senin Profilin | Önerilen Burs |
| --- | --- |
| Lisans 3-4. yıl, kısa Almanya deneyimi | **Erasmus+ Studies** (3-6 ay) |
| Master için Almanya'ya tam taşınmak | **DAAD Master Studies** |
| Master için 2 ülkede yapmak | **Erasmus Mundus** |
| PhD için Almanya'ya tam taşınmak | **DAAD PhD in Germany** |
| Türkiye'de PhD + Almanya'da araştırma | **DAAD Sandwich Programme** |
| Lisans/master staj Almanya'da | **Erasmus+ Trainees** |

## Yan Yana Almak Mümkün mü?

⚠️ **Aynı anda iki AB burs alamazsın** — Erasmus + DAAD birlikte yasaklanmıştır
⚠️ **Sequential mümkün** — Erasmus 6 ay sonra → DAAD master başvurusu sonraki yıl
⚠️ **DAAD Türkiye'deki diplomayı destekliyor** — Erasmus sonrası DAAD'a başvuru avantaj

## Maliyet Açısından Karşılaştırma

### 1 Yıllık Master Almanya'da Erasmus ile (Yarı Yıl)
- Erasmus: 6 ay × 400 € = 2,400 €
- Eksik finansman: 6 ay × 600 € = 3,600 € (cep)
- Toplam cep maliyeti: ~6,000 €

### 1 Yıllık Master Almanya'da DAAD ile
- DAAD: 992 € × 12 = 11,904 € + sigorta + gemi
- Cep maliyeti: **0 € (tamamen kapsıyor)**

✅ **DAAD finansal olarak çok daha avantajlı** uzun süreli için.

## Erasmus'un DAAD Üzerinde Avantajları

✅ **Daha kolay** kabul (rekabet az)
✅ **Türkiye diploması** korunur (Türk üni'nde geri dönüş)
✅ **6 ay gibi kısa süre** — yapamayacağın 2 yıllık taahhüt yok
✅ **CV'de "Almanya'da değişim" deneyimi** — sonra DAAD master için avantaj

## Pratik Strateji

✅ **Lisans 3. yılda** Erasmus+ Studies (6 ay Almanya)
✅ Türkiye'de diploma + İngilizce/Almanca pratiği
✅ Lisans son yılında **DAAD Master Studies başvurusu**
✅ Master için tam burs alıp Almanya'ya gel

İlgili: [DAAD bursu](/sss/burs/daad-bursu-nasil-alinir-basvuru-sureci-nedir) | [DAAD aylık](/sss/burs/master-icin-daad-bursu-aylik-ne-kadar).
MD,

            'burs-basvurusu-icin-kac-universiteye-yazilmali' => <<<'MD'
DAAD ve diğer Alman bursları için **3-6 üniversite** önerilir. Çok az olursa şansın azalır, çok fazla olursa motivasyon mektupların yetersiz olur.

## Optimum Sayı: 3-5 Üniversite

### DAAD Master Studies'e Özel
✅ **Maximum 4 üniversite** seçilebilir başvuruda
✅ **Öncelik sırası** belirtilir (Erstwunsch, Zweitwunsch, Drittwunsch, Viertwunsch)
✅ DAAD önceliğini koruyarak değerlendirir

### Strateji: Üniversite Çeşitliliği

| Strateji | Önerilen üniversite sayısı |
| --- | --- |
| **Hedefli** (1 üniversite, 1 program) | 1-2 |
| **Kapsamlı** (3 farklı şehir/üniversite) | 3-4 |
| **Genişletilmiş** (5+ alternatif) | Önerilmez |

## Üniversite Seçim Kriterleri

### 1. Program Uyumu
✅ Master programının kabul kriterleri sana uygun mu?
✅ Bachelor'ın kabul edilen alanlarda mı?
✅ Almanca/İngilizce yeterliliğin yeterli mi?

### 2. Şehir/Konum Tercihi
✅ Büyük şehir (Berlin, Münih) vs küçük şehir (Heidelberg, Tübingen)
✅ Yaşam maliyeti farkı
✅ Uluslararası öğrenci topluluğu büyüklüğü

### 3. DAAD Öncelik Listesi
✅ DAAD bazı üniversitelerle özel anlaşmalı (Excellence Universities)
✅ TU, Charité, Bayreuth, Konstanz vs. genelde tercih ediliyor
✅ "DAAD partnerships" araması ile öğrenebilirsin

### 4. Profesör/Bölüm Önerileri
✅ Spesifik bir profesör altında master yapmak istiyorsan → onun bulunduğu uni öncelikli
✅ Sandwich Programme için Türkiye'deki danışmanın Almanya bağlantısı önemli

## Üniversite Sayısı Stratejisi

### Az Sayıda (1-2 Üniversite)
**Avantajlar:**
- Motivasyon mektubu **çok detaylı + spesifik**
- Profesörel iletişim güçlü (öncesi başlanmış)
- Yüksek başarı şansı (eğer kabul edilirse)

**Dezavantajlar:**
- Reddedilirse alternatifin yok
- Bekleme süresinde yeniden başvuru gerekebilir

### Orta Sayıda (3-4 Üniversite)
**Avantajlar:**
- Çeşitlilik (farklı şehir, farklı program)
- Reddedildiğinde alternatif var
- DAAD önceliği koruyabiliyor

**Dezavantajlar:**
- Motivasyon mektubu her uni için ayrı yazmak zaman alır
- Belge tekrarı (her uni için yeni evrak setleri)

### Çok Sayıda (5+ Üniversite)
**Avantajlar:**
- Çok geniş bir alanda alternatif
- Genel kapsam

**Dezavantajlar:**
- Motivasyon mektubu **şablon** olabilir (kişiliksiz)
- DAAD değerlendirme komitesi farkı görür (motivasyon zayıf)
- Hangi uni'ye gerçekten gitmek istiyorsan o belirsiz olur

## Pratik Strateji (Önerilen)

### Adım 1: 6-8 Üniversite Aday Listesi
- DAAD database'i ile filtreleme
- Programın seninle uyumu
- Şehir/program çeşitliliği

### Adım 2: 3-5 Üniversiteye Düşür
- En çok uyumlu programlar
- DAAD partnerships avantajı
- Önceki Türk bursiyer iyi referans veren uni'ler

### Adım 3: Önceliklendir
- Erstwunsch (1. tercih): En istediğin
- Zweitwunsch (2. tercih): Yedek + iyi seçenek
- Drittwunsch (3. tercih): Alternatif
- Viertwunsch (4. tercih): Son şans

## Sık Yapılan Hatalar

❌ **Tek üniversiteye başvuru** — reddedilirse o yıl burs şansı kaybolur
❌ **6+ üniversite başvurusu** — motivasyon mektupları cansız, generic olur
❌ **Aynı uni'nin 2 master programı** — bölüm seçimi karışık görünür
❌ **Yüksek prestij ama düşük uyum** — TUM/RWTH'e bachelor uygun olmayan programdan başvuru

## Diğer Burslar İçin

### KAAD, KAS, FES için
✅ **2-3 üniversite yeter** — bunlar daha küçük programlar
✅ Politik/dini vakıf ağı bağlantısı önemli

### Erasmus+ için
✅ **1-2 üniversite** — Türk üniversitenin partnerlikleri sınırlı
✅ Türk üni'n hangi Almanya uni'leriyle Erasmus anlaşması var?

## Önemli

⚠️ Belge eksik gönderirsen başvuru reddedilir — çift kontrol şart
⚠️ Üniversite başvurusu + DAAD başvurusu **paralel** yapılır — Zulassungsbescheid DAAD'a sunulur
⚠️ Üniversitenin **uygulama deadline'ı** (genelde 15 Temmuz) DAAD deadline'ından önce — geç kalma!

İlgili: [DAAD belgeler](/sss/burs/daad-basvurusu-icin-hangi-belgeler-gerekli) | [DAAD tarihleri](/sss/burs/daad-burs-basvuru-tarihleri-ne-zaman).
MD,

            'burs-icin-not-ortalamasi-ve-dil-sarti-nedir' => <<<'MD'
Burs başvuruları için **not ortalaması ve dil yeterlilik** kritik kriterler. Burs türüne göre değişir.

## Not Ortalaması Şartları

### DAAD Master Studies
✅ **Minimum:** Lisans GPA **2.5/4.0** (Türkiye notu **80+ / 100**)
✅ **Önerilen:** GPA **3.0/4.0+** (Türkiye notu **85-90+**)
✅ **Yüksek başarı için:** GPA **3.5/4.0+** (Türkiye notu **90+**)
✅ Üniversite başvurusunda bazı programlar GPA 3.0+ ister

### DAAD PhD
✅ **Master GPA 2.5/4.0+** minimum (Türkiye notu 80+)
✅ Master tezindeki başarı önemli
✅ Yayın/proje deneyimi GPA'yı kompanse edebilir

### Politik Vakıf Bursları
| Vakıf | Master GPA min | PhD GPA min |
| --- | --- | --- |
| **KAS** (Konrad Adenauer) | 2.5 | 2.5 |
| **FES** (Friedrich Ebert) | 2.5 | 2.5 |
| **Heinrich Böll** | 2.5 | 2.5 |
| **Cusanuswerk** | 1.8 (Türkiye 90+) | 1.8 |
| **Studienstiftung** | 1.8 | 1.8 |

## Türkiye Notu → Alman GPA Çevirme

| TR notu (4 yıl ortalama) | Alman GPA |
| --- | --- |
| 100-95 | 1.0 (en yüksek) |
| 94-90 | 1.3-1.5 |
| 89-85 | 1.7-2.0 |
| 84-80 | 2.3-2.7 |
| 79-75 | 2.8-3.0 |
| 74-70 | 3.0-3.3 |
| 69-60 | 3.4-3.7 |
| 60 altı | 4.0+ (geçer dahi yok) |

⚠️ **Alman sistemde 1.0 en iyi, 4.0 en kötü** — Türk sisteminin tam tersi!

## Dil Şartları

### Almanca

#### Master Programları
| Burs | Minimum | Önerilen |
| --- | --- | --- |
| **DAAD Master Studies** | B2 (TestDaF 4) | C1 |
| **KAS, FES, Böll** | B2 | C1 |
| **KAAD** | B1 (kursla B2'ye çıkar) | B2 |
| **Üniversite programları** | C1 (TestDaF 4 veya DSH-2) | C1 |

#### PhD Programları
| Burs | Minimum |
| --- | --- |
| **DAAD PhD** | B2 (Almanca konuşan programlarda C1) |
| **Üniversite programları** | Programa göre değişir (Almanca veya İngilizce) |

### İngilizce

#### Master/PhD için (İngilizce program)
| Burs | Minimum |
| --- | --- |
| **DAAD Master Studies** | IELTS 6.5 / TOEFL 90+ |
| **DAAD PhD** | IELTS 7.0 / TOEFL 95+ |
| **Üniversite programları** | IELTS 6.0-7.0 (programa göre) |

### Türkiye'den Kabul Edilen Sınavlar
✅ **YDS / e-YDS / YÖKDİL:** DAAD bazı kategoriler kabul (80+ puan = İngilizce yeterlilik)
✅ **TOEFL ITP:** DAAD kabul ediyor
✅ **TestDaF / Goethe / DSH:** Almanca yeterliliği için
✅ **IELTS / TOEFL iBT / Cambridge:** İngilizce yeterliliği için

## Dil Sertifikası Geçerlilik

| Sertifika | Geçerlilik süresi |
| --- | --- |
| TestDaF | 5 yıl |
| Goethe-Zertifikat C1 | Yaşam boyu |
| DSH | Yaşam boyu |
| IELTS | 2 yıl |
| TOEFL iBT | 2 yıl |
| YDS | 5 yıl |

## DAAD'ın Spesifik Beklentileri

### Not Konusunda
- "Sehr gut" Almanya'da nota karşılık = **1.0-1.5** Alman GPA
- DAAD her zaman **birinci-ikinci dilimden** (üst %20) öğrenci seçiyor
- "Sınıf birinciliği" veya "fakülte birinciliği" DAAD için çok değerli

### Dil Konusunda
- **Akademik motivasyon mektubu** Almanca/İngilizce yazılmalı
- Sertifika **başvuru anında geçerli** olmalı
- DAAD kabul süreci sırasında **dil kursu desteği** sunabilir (6 aya kadar)

## Diğer Kriterler

✅ **Akademik performans** (GPA + sınıf sırası)
✅ **Dil yeterlilik** (Almanca + İngilizce)
✅ **Motivasyon mektubu kalitesi**
✅ **Referans mektupları** (akademik)
✅ **Yayın/proje deneyimi** (bonus, ama önemli)
✅ **Pratik deneyim** (staj, gönüllü çalışma)
✅ **Kariyer planı netliği**

## Pratik Tavsiye

### GPA Düşükse (75-80)
- **Yüksek dil sertifikası** ile dengele (TestDaF 5, C1)
- **Güçlü referans mektupları** topla
- **Yayın/proje** ekle (sınıf birincisi değilsen başka bir yerde öne çık)
- **KAAD veya küçük vakıflar** GPA 2.5+ kabul ediyor

### Dil Sertifikası Yoksa
- **TestDaF + IELTS** çift sertifika ile başvur
- DAAD'da bazı kategoriler **dil kursu dahil** (B1 ile başvurabilirsin)
- **Türkiye'de Goethe-Zertifikat** B2 → DAAD için minimum

### Hem GPA Hem Dil Düşükse
- **TÜBİTAK bursları + Türkiye'de PhD + Almanya kısmi araştırma** alternatif
- **Erasmus+ Studies** (kabul kriteri daha esnek)
- **Üniversite kabul + Sperrkonto** (burs olmadan da gidebilirsin)

İlgili: [DAAD başvuru](/sss/burs/daad-bursu-nasil-alinir-basvuru-sureci-nedir) | [Konrad Adenauer](/sss/burs/konrad-adenauer-vakfi-bursu-nasil-alinir).
MD,

            'phd-icin-almanya-burslari-nelerdir' => <<<'MD'
PhD (Doktora) için Almanya'da burs seçenekleri çeşitli — DAAD en yaygın, ama Excellence ve özel programlar da var.

## 1. DAAD PhD Programmes

### DAAD Research Grants - Doctoral Programmes in Germany
- **Süre:** 3-4 yıl
- **Aylık:** **1,200 €** (Master bursundan yüksek)
- **Kapsam:** Sağlık sigortası + ulaşım + araştırma desteği
- **Hedef:** Türkiye'de Bachelor + Master, Almanya'da PhD
- **Başvuru:** Mart, Mayıs, Eylül

### DAAD Bi-nationally Supervised PhD
- **Süre:** 1-3 yıl
- **Aylık:** 992 €
- **Hedef:** Türkiye'de Master + ortak PhD (TR ve DE profesör)
- **Sandwich modeli**

### DAAD Re-invitation Doctoral
- **Süre:** 1-3 ay
- **Aylık:** 2,070 €
- **Hedef:** Önceki DAAD bursiyer + Almanya'da kısa süreli araştırma

## 2. Excellence Initiative Bursları

### Max Planck Society PhD Programmes
- **Süre:** 3-4 yıl
- **Aylık:** **1,365-1,615 €** (TVöD scaled)
- **Kapsam:** Sağlık + ulaşım + araştırma materyali
- **Avantaj:** Dünya çapında prestijli enstitü
- **Başvuru:** Direkt enstitüye, yıllık 2 kez

### Helmholtz Association PhD
- **Süre:** 3-4 yıl
- **Aylık:** **1,400-1,800 €**
- **Kapsam:** TVöD-13 maaş seviyesinde
- **Hedef:** Doğa bilimleri + mühendislik

### Leibniz Association PhD
- **Süre:** 3-4 yıl
- **Aylık:** **1,200-1,600 €**
- **Hedef:** Tüm disiplinler

### Fraunhofer Society PhD
- **Süre:** 3-4 yıl
- **Aylık:** **1,500-2,000 €**
- **Hedef:** Uygulamalı araştırma (mühendislik)
- **Avantaj:** Endüstri bağlantısı + iş garantisi sonrası

## 3. Politik Vakıf PhD Bursları

| Vakıf | Aylık | Yaş üst sınırı |
| --- | --- | --- |
| **Konrad Adenauer Stiftung (KAS)** | 1,500 € | 35 |
| **Friedrich Ebert Stiftung (FES)** | 1,365 € | 36 |
| **Heinrich Böll Stiftung** | 1,500 € | 40 |
| **Hanns Seidel Stiftung** | 1,300 € | 35 |
| **Studienstiftung des Deutschen Volkes** | 1,365 € | 40 |
| **Friedrich Naumann Stiftung** | 1,365 € | 35 |

## 4. Dini Vakıf PhD Bursları

### KAAD (Katolik Akademik Değişim)
- **Aylık:** 1,500 €
- **Hedef:** Müslüman + Hristiyan öğrenciler (TR için açık)
- **Avantaj:** Türkçe destek, Türk öğrenci ağı

### Evangelisches Studienwerk
- **Aylık:** 1,365 €
- **Hedef:** Protestan öğrenciler

### Cusanuswerk
- **Aylık:** 1,800 € (en yüksek)
- **Hedef:** Akademik elite (GPA 1.8/4.0+ veya TR 90+)
- **Çok rekabetçi**

## 5. Üniversite + Bölüm PhD Pozisyonları

### Asistan Pozisyonları (Wissenschaftliche Mitarbeiter)
- **Aylık:** **1,800-3,200 €** (TVöD-13, %50-100 zaman)
- **Süre:** 3-6 yıl
- **Avantaj:** Tam maaş + sigorta + emeklilik
- **Şart:** Doğrudan profesörle anlaşma, bölümde ders ve araştırma

### Stipendium-Position (Burs Pozisyonu)
- **Aylık:** **1,200-1,800 €**
- **Süre:** 3-4 yıl
- **Avantaj:** Daha az sorumluluk, sadece araştırmaya yoğunlaşma

## 6. Endüstri Bursları

### Bayer, BASF, BMW, Daimler, Siemens
- **Aylık:** **1,800-2,500 €**
- **Süre:** 3-4 yıl
- **Hedef:** Şirketle ilgili PhD konuları
- **Avantaj:** İş garantisi + yüksek maaş

### Bosch PhD Programme
- **Aylık:** 2,000-2,400 €
- **Hedef:** Mühendislik, IT, malzeme bilimi

## 7. TÜBİTAK Türkiye'den

### TÜBİTAK 2214-A Yurt Dışı PhD Bursu
- **Süre:** 12 ay Almanya'da
- **Aylık:** **2,500 USD** (~2,300 €)
- **Hedef:** Türkiye'de PhD öğrencisi, Almanya'da araştırma
- **Avantaj:** Yaş sınırsız, ek burs gerek yok

### TÜBİTAK 2213
- **Süre:** Tüm PhD
- **Aylık:** **2,500 USD**
- **Hedef:** Yurtdışı tam PhD (Almanya dahil)

## Hangi Burs En Avantajlı?

### Maaş Açısından
✅ Üniversite Asistan Pozisyonu (TVöD-13): **2,500-3,200 € net**
✅ Endüstri PhD (Bosch, Bayer): **2,000-2,500 €**
✅ Fraunhofer PhD: **1,500-2,000 €**

### Akademik Özgürlük Açısından
✅ DAAD PhD: Tek başına araştırma, tam kontrol
✅ Max Planck: Prestij + araştırma odaklı
✅ Helmholtz: Doğa bilimi + teknoloji

### Süresince Yan İş Yapma Açısından
✅ DAAD: Yan iş %50 mümkün
✅ Politik vakıf: Yan iş %50 mümkün
✅ Üniversite asistan: Maaş zaten yüksek, yan iş gereksiz

## PhD Başvuru Stratejisi

### Adım 1: Almanya'da Profesör Bul
- Konuna uygun **2-3 profesör** belirle
- E-mail ile iletişim: araştırma planı + CV
- "Betreuungszusage" (danışman onayı) iste

### Adım 2: PhD Pozisyon vs Burs Seç
✅ **Profesörel pozisyon** (Wissenschaftliche Mitarbeiter) → tam maaş, ders verme
✅ **Burs** (DAAD, vakıf) → tam araştırma odaklı

### Adım 3: Forschungsexposé Hazırla
- 5-15 sayfa (Almanca veya İngilizce)
- Araştırma konusu + soru + yöntem + zaman planı + beklenen sonuçlar

### Adım 4: Burs Başvurusu
- DAAD + 2-3 politik vakıf paralel başvuru
- Her başvuruda farklı motivasyon mektubu

İlgili: [DAAD başvuru](/sss/burs/daad-bursu-nasil-alinir-basvuru-sureci-nedir) | [Konrad Adenauer](/sss/burs/konrad-adenauer-vakfi-bursu-nasil-alinir).
MD,

            'konrad-adenauer-vakfi-bursu-nasil-alinir' => <<<'MD'
**Konrad Adenauer Stiftung (KAS)**, CDU partisine yakın politik vakıf — Almanya'nın en büyük PhD/Master burs programlarından biri. Türkiye'den her yıl 20-30 öğrenci kabul ediliyor.

## KAS Burs Kategorileri

### 1. KAS Master Studies (Türkiye'den)
- **Süre:** 1-2 yıl
- **Aylık:** **957 €** (DAAD'a yakın)
- **Kapsam:** Sağlık sigortası + ulaşım + araştırma + dil kursu
- **Hedef:** Master için Almanya'ya tam taşınmak

### 2. KAS PhD in Germany
- **Süre:** 3-4 yıl
- **Aylık:** **1,500 €**
- **Kapsam:** Tam burs + araştırma desteği

### 3. KAS Researcher Programmes
- **Süre:** 1-12 ay
- **Aylık:** 1,200-2,000 €

## Başvuru Şartları

### Akademik
✅ **GPA minimum:** 2.5/4.0 (Türkiye 80+)
✅ **Lisans bitirme veya bitirme aşaması**
✅ **Master için lisans, PhD için master tamamlanmış**
✅ **Yaş üst sınırı:** Master 30 yaş, PhD 35 yaş

### Dil
✅ **Almanca minimum B2** (TestDaF 4 veya Goethe B2)
✅ **İngilizce gerekmez** (Alman bazlı program)
✅ **Almanca master/PhD programları** önceliklendirilir

### Kişisel / Politik
✅ **CDU/Hristiyan-demokrat değerlere yakınlık** — vakıf önceliği
✅ **Toplumsal aktivite** (kulüp, dernek, gönüllü çalışma)
✅ **Liderlik özelliği** kanıtla
✅ **Türkiye'de geri dönüş sonrası katkı sağlama** niyeti

## Başvuru Süreci

### Adım 1: Online Başvuru
- *kas.de/scholarship-programmes*
- Türkiye için: **DAAD Information Center İstanbul** üzerinden de bilgi alınabilir
- Online form doldur

### Adım 2: Belgeler
✅ **CV** (Almanca + İngilizce versiyon)
✅ **Motivasyon mektubu** (5 sayfa, Almanca tercih)
✅ **3 referans mektubu** (akademik + politik)
✅ **Diploma + transkript** (yeminli tercüme)
✅ **Almanca/İngilizce sertifika**
✅ **Üniversite kabul mektubu** (Master/PhD için)
✅ **Forschungsexposé** (PhD için)
✅ **KAS'a uygun olduğunu kanıtlayan yazı** (politik aktivite)

### Adım 3: Mülakat
- Online (Skype/Zoom) veya Türkiye'de DAAD ofisinde
- **Süre:** 30-45 dakika
- **Konular:**
  - Akademik plan
  - Politik tutum (CDU değerlerine yakınlık)
  - Toplumsal katkı niyeti
  - Almanya'ya geliş sebebin

### Adım 4: Karar
- Mülakat sonrası **2-3 ay**
- Kabul mektubu gelir
- Bursun başlangıç tarihi belirlenir

## Başvuru Tarihleri (2026)

| Kategori | Başvuru deadline |
| --- | --- |
| **Master Studies (Wintersemester)** | 15 Şubat |
| **Master Studies (Sommersemester)** | 15 Ağustos |
| **PhD in Germany** | 15 Şubat veya 15 Ağustos |
| **Research Grants** | Tüm yıl açık |

## KAS Burs Özellikleri

### Ek Hizmetler (KAS Üyelik)
✅ **KAS Seminer + Workshop'lar** — politik eğitim
✅ **Akademik mentor** — danışman atanır
✅ **Network etkinlikleri** — diğer bursiyerle tanışma
✅ **Konferans + seyahat desteği** ek finansman
✅ **KAS Alumni ağı** — mezun sonrası kariyer desteği

### Şartları
⚠️ **Politik eğitim seminerlerine katılım** zorunlu (yılda 2-3 etkinlik)
⚠️ **CDU değerlerine bağlı kalma** beklenir
⚠️ **Burs sonrası KAS'a haber verme** zorunlu (Türkiye'de katkı sağlama)

## KAS vs DAAD Karşılaştırma

| Özellik | KAS | DAAD |
| --- | --- | --- |
| **Aylık tutar (Master)** | 957 € | 992 € |
| **Aylık tutar (PhD)** | 1,500 € | 1,200 € |
| **Politik bağlılık** | CDU yakın | Bağımsız |
| **Network/Etkinlik** | Çok aktif | Az |
| **Mülakat zorluğu** | Orta | Yüksek |
| **Geri dönüş şartı** | Esnek | Esnek |
| **Yaş üst sınırı** | Master 30, PhD 35 | Master 32, PhD 36 |

## Hangi Senaryoda KAS Tercih Edilir?

### KAS Seç:
- **CDU/Hristiyan-demokrat değerlere yakınsan**
- **Politik aktiviteden hoşlanıyorsan** (seminer, workshop, network)
- **Türkiye'de geri dönüş + topluluk katkısı** yapma niyetin varsa
- **35 yaş altıysan + iyi akademik performans**

### KAS Seçme:
- **DAAD'ın daha esnek programları varsa** (kısa süreli, sandwich vs.)
- **Politik etkinliklere zaman ayırmak istemiyorsan**
- **35 yaş üstüysen** (KAS kabul etmez)

## Türkiye Bağlantısı

✅ **KAS Türkiye Ofisi (Ankara):**
- Adres: Çankaya, Ankara
- Tel: +90 312 466 22 76
- Web: kas.de/tuerkei

KAS Türkiye **CDU politikalarını destekleyen Türk akademisyenler** ile ağ oluşturuyor. Geri dönüş sonrası mevcut Türk akademisyenler ile bağlantı kurabilirsin.

İlgili: [DAAD başvuru](/sss/burs/daad-bursu-nasil-alinir-basvuru-sureci-nedir) | [PhD burs](/sss/burs/phd-icin-almanya-burslari-nelerdir).
MD,

            'burs-icin-referans-mektubu-nasil-yazilir' => <<<'MD'
Burs başvurularında referans mektupları (Empfehlungsschreiben) ağırlık taşıyor — DAAD ve diğer vakıflar mutlaka **2-3 akademik referans** ister.

## Kimden Referans Almak?

### En İyi Referans Kaynakları
✅ **Tez/proje danışmanı** (bachelor veya master)
✅ **Anabilim dalı başkanı / dekan**
✅ **Birden fazla ders aldığın profesör** (özellikle yüksek başarılı olduğun)
✅ **Staj/araştırma deneyiminden mentor**
✅ **Yayın yaptığın profesör** (varsa)

### Kabul Edilmeyen Referans Kaynakları
❌ Aile dostu / akraba
❌ İş yerinden komşu / yönetici (akademik dışı)
❌ Lise öğretmeni (üniversite düzeyinde olmalı)
❌ Akademik olmayan profesyonel referans

## Referans Mektubu Nasıl Olmalı?

### Bölümler
1. **Profesörel başlık + iletişim** (üst kısımda)
2. **Tanıyıcı paragraf** (öğrenciyi nasıl tanıyor, hangi derslerden)
3. **Akademik performans değerlendirmesi** (notlar + spesifik dersler)
4. **Kişilik özellikleri** (motivasyon, çalışkanlık, eleştirel düşünme)
5. **Spesifik örnekler** (proje, ödev, sınıf içi katkı)
6. **Burs için neden uygun?** (DAAD/KAS/FES vs. spesifik)
7. **Önerme ifadesi** (kuvvetli)
8. **İmza + mühür + tarih**

### Uzunluk
✅ **1 sayfa minimum**, **2-3 sayfa optimum**
✅ Az: Profesör tembel görünür, etki zayıf
✅ Çok: Profesör seni iyi tanımıyor görünür

### Dil
✅ **Almanca veya İngilizce** (DAAD için)
✅ Profesör Türkçe yazarsa → **yeminli tercüme** ekle

## Mektubun İçeriğinde Olması Gereken

✅ **Spesifik notlar** ("XYZ dersinden 95 aldı")
✅ **Sınıf sıralaması** ("İlk %5'te")
✅ **Yıllık değerlendirme** ("3 yıl boyunca tanıyorum")
✅ **Karşılaştırma** ("Çok az öğrencide gördüğüm motivasyon")
✅ **Spesifik proje/yayın** ("X konusu hakkında yaptığı sunum mükemmeldi")
✅ **Liderlik özelliği** ("Sınıf temsilcisi olarak gönüllü çalıştı")
✅ **Geri dönüş niyeti** ("Türkiye'ye katkı sağlayacak")
✅ **Burs üzerinde değerlendirme** ("DAAD bursunu en uygun adaylardan biri")

## Mektup İsteme Süreci

### Adım 1: Profesörle Konuş
- En az **2 ay önceden** ricada bulun
- **Buluşma talebinde bulun** (15-20 dakikalık görüşme)
- Burs türü ve hedefini açıkla

### Adım 2: Bilgilendirici Paket Hazırla
✅ **CV** (akademik + kişisel)
✅ **Burs ilanı** (kısa özet)
✅ **Motivasyon mektubu taslağı**
✅ **Transcript** kopya
✅ **Spesifik referans noktaları** (ne tür şeylere odaklanmasını istiyorsan)

### Adım 3: Yazma Süresi
- **1-2 hafta minimum** ver profesöre
- DAAD deadline'ından önce **3-4 hafta önce** iste

### Adım 4: Teslimat
- Genelde profesör mektubu **PDF olarak** size gönderir
- Bazı vakıflar **direkt profesörden** ister (mühürlü zarf)
- DAAD: Online portal'a yükle, profesör de ayrıca onaylar

## Sık Yapılan Hatalar

### Öğrenci Tarafında
❌ **Çok geç istemek** (1-2 hafta önce)
❌ **Bilgisiz isteme** ("Burs için ne yazmam gerekir bilmiyorum")
❌ **Bir profesörden çok talep** (3+ referans aynı profesörden)
❌ **Düşük seviye profesörden referans** (asistan veya yüksek lisans öğrencisi)

### Profesör Tarafında
❌ **Genel cümleler** ("İyi bir öğrencidir")
❌ **Spesifik örnek yok**
❌ **Önerme kuvvetsiz** ("Uygun olabilir")
❌ **Yanlış burs adı veya kuruluş**
❌ **Türkçe yazıp tercüme etmemek**

## Örnek Referans Mektubu Formatı

```
[Profesörel başlık + ünvan]
[Adres + iletişim]
[Tarih]

To Whom It May Concern,
(Veya: Sehr geehrte Damen und Herren / Lieber Frau/Herr [İsim])

It is with great pleasure that I write this letter
in support of [Öğrenci Adı]'s application for
the [Burs Adı, örn. DAAD Master Studies in Germany].

I have known [Öğrenci Adı] for [3 yıl] in my role as
[ders adı / tez danışmanı / vs.] at [Üniversite].

In my course on [Konu], they demonstrated...
[Spesifik örnekler, notlar, sınıf sıralaması]

[Kişilik değerlendirmesi: motivasyon, çalışkanlık]

I particularly want to note...
[Spesifik bir başarı veya özellik]

Based on these observations, I strongly recommend
[Öğrenci Adı] for the [Burs Adı]. I am confident
that they will...

Please do not hesitate to contact me for any
further information.

Sincerely,
[Profesör İmza]
[Profesör Adı, Ünvan, Üniversite]
[E-mail + Telefon]
```

## Profesör İstemiyorsa Ne Yapmalı?

### Reddedilirsen Stratejisi
1. **Başka profesör dene** (en az 5 profesör listesi hazırla)
2. **Anabilim dalı başkanına başvur**
3. **Tez danışmanına başvur**
4. **Endüstri bağlantın varsa profesyonel referans** (akademik dışı ama destekleyici)

## Önemli Notlar

⚠️ **Referans mektubu öğrenci tarafından okunmaz** — DAAD bunu beklemiyor, profesör direkt portal'a yükler
⚠️ Bazı profesörler **template imza** olarak gönderiyor → sen düzeltme isteyebilirsin
⚠️ **Eski mezunlar** zor referans alır → şu anda kayıtlı olduğun profesörler daha pratik

İlgili: [DAAD belgeler](/sss/burs/daad-basvurusu-icin-hangi-belgeler-gerekli) | [Motivasyon mektubu](/sss/burs/daad-bursu-nasil-alinir-basvuru-sureci-nedir).
MD,

            'turkiyeden-almanya-bursu-icin-tev-gibi-yerel-kaynaklar-var-mi' => <<<'MD'
**Evet, Türkiye'de Almanya eğitimini destekleyen yerel vakıf ve kurumlar var.** TEV en bilinen, ama başka seçenekler de mevcut.

## 1. Türk Eğitim Vakfı (TEV)

### TEV Yurt Dışı Burs Programı
- **Hedef:** Türkiye'deki en başarılı öğrenciler, yurt dışında master/PhD
- **Aylık tutar:** **1,000-1,500 € (Almanya'da)**
- **Süre:** Master için 2 yıl, PhD için 3-4 yıl
- **Kapsam:** Tüm öğrenim ücreti + yaşam + sigorta + ulaşım

### Başvuru Şartları
✅ **GPA:** Türkiye notu **3.5/4.0+ (90+/100)**
✅ **Yaş:** Lisans bitirmiş, 30 altı tercih
✅ **Dil:** İngilizce/Almanca yüksek (TOEFL 100+, TestDaF 5)
✅ **Toplumsal aktivite:** Spor, sanat, gönüllü çalışma
✅ **Liderlik özelliği:** Kulüp başkanlığı, dernek aktivitesi
✅ **Geri dönüş taahhüdü:** Türkiye'de en az 5 yıl çalışma

### Başvuru Süreci
- *tev.org.tr* → Yurt Dışı Burs Programı
- **Başvuru deadline:** Ocak (her yıl)
- Başvuru → ön eleme → mülakat → final değerlendirme
- Karar: **3-4 ay**

### TEV Avantajları
✅ **Tüm masraflar dahil** (öğrenim + yaşam + sağlık + ulaşım)
✅ **Türk öğrenci ağı** Almanya'da
✅ **Mentor desteği**
✅ **Geri dönüş sonrası iş bulma desteği**

### Dezavantajlar
❌ **Çok rekabetçi** (yıllık 10-20 öğrenci kabul)
❌ **Geri dönüş zorunluluğu** (en az 5 yıl)
❌ **Politik bağlılık beklenmez** (TEV bağımsız) ama Türkiye'ye katkı sağlama

## 2. Türkiye Bilimsel ve Teknolojik Araştırma Kurumu (TÜBİTAK)

### TÜBİTAK 2214-A — Yurt Dışı Doktora Sırası Araştırma Bursu
- **Hedef:** Türkiye'de PhD öğrencisi, 6-12 ay Almanya'da araştırma
- **Aylık:** **2,500 USD** (~2,300 €)
- **Kapsam:** Yaşam + sigorta + araştırma + ulaşım
- **Şart:** Türkiye'de aktif PhD öğrencisi olmak

### TÜBİTAK 2213 — Yurt Dışı Lisansüstü Burs Programı
- **Hedef:** Türkiye'de master tamamlanmış, Almanya'da PhD
- **Aylık:** **2,500 USD**
- **Süre:** Tüm PhD (3-5 yıl)
- **Şart:** Geri dönüş + Türkiye'de akademik kariyer

### TÜBİTAK 2228 — Yurt Dışı Master Bursu
- **Hedef:** Master için Almanya'ya gitmek
- **Aylık:** **1,000-1,500 USD**
- **Süre:** 1-2 yıl

## 3. Vehbi Koç Vakfı

### Vehbi Koç Vakfı Yurt Dışı Burs Programı
- **Hedef:** Yurt dışı master/PhD
- **Aylık:** **1,000-1,500 €**
- **Şart:** Türkiye'de okumuş, geri dönüş niyeti

## 4. Sabancı Vakfı

### Sabancı Vakfı Yurt Dışı Burs Programı
- **Hedef:** Yurt dışı master, çeşitli alanlar
- **Aylık:** **1,000-1,500 €**
- **Şart:** Sabancı Üniversitesi mezun tercih ediliyor, ama herkes başvurabilir

## 5. Diğer Türk Burs Vakıfları

### Eczacıbaşı Vakfı
- Master/PhD bursu, çevre + sağlık alanları

### Kafkas Vakfı
- Yurt dışı eğitim destek programı

### KKTC + Türkiye Bursları (YÖK Kontenjanı)
- YÖK'ten "Yurt Dışı Eğitim Bursları" — sınırlı

### Şişli Sosyal Yardımlaşma ve Dayanışma Vakfı
- Yerel düzeyde yardım

## TEV + DAAD Kombinasyonu Mümkün mü?

### Kural
⚠️ **Tek yön kural:** Sadece bir burs alabilirsin (TEV veya DAAD veya KAS vs.)
⚠️ **Çift burs yasaktır** — vakıflar birbirine bildirim yapıyor

### Strateji
✅ **TEV'i öncelikli başvur** (Türkiye'de kabul + Almanya'ya gönderme)
✅ TEV reddedilirse → **DAAD'a sıradakine başvur**
✅ TEV alırsan → DAAD başvurusunu **geri çek**

## Bursları Karşılaştırma

| Burs | Aylık | Süre | Geri dönüş şartı |
| --- | --- | --- | --- |
| **TEV** | 1,000-1,500 € | 2-4 yıl | 5 yıl Türkiye'de çalışma |
| **TÜBİTAK 2214-A** | 2,300 € | 6-12 ay | Türkiye'de PhD tamamla |
| **TÜBİTAK 2213** | 2,300 € | 3-5 yıl | Türkiye'de akademik |
| **DAAD Master** | 992 € | 2 yıl | Esnek |
| **DAAD PhD** | 1,200 € | 3-4 yıl | Esnek |
| **KAS** | 957-1,500 € | 2-4 yıl | Esnek |

## Pratik Strateji

### Lisans Sonrası Master için
1. **TEV başvur** (Türkiye'de yüksek başarın varsa)
2. **DAAD başvur** (parallel — TEV reddedilirse)
3. **KAS başvur** (politik aktivite varsa)

### Türkiye'de PhD + Almanya Araştırma için
1. **TÜBİTAK 2214-A** en kapsamlı (2,300 €/ay 6-12 ay)
2. **DAAD Sandwich Programme** alternatif

### PhD için Almanya'da
1. **TÜBİTAK 2213** (Türkiye akademik kariyeri hedefliyorsan)
2. **DAAD PhD** (esneklik istiyorsan)
3. **Max Planck / Helmholtz pozisyon** (en yüksek maaş)

## Türkiye'den Almanya İçin Yararlı Bağlantılar

✅ **DAAD Bilgi Merkezi İstanbul** — *ic.daad.de/istanbul*
✅ **TEV Yurt Dışı** — *tev.org.tr/yurt-disi*
✅ **TÜBİTAK BİDEB** — *tubitak.gov.tr/bideb*
✅ **YÖK Bursları** — *yokegitim.gov.tr*

İlgili: [DAAD başvuru](/sss/burs/daad-bursu-nasil-alinir-basvuru-sureci-nedir) | [PhD burslar](/sss/burs/phd-icin-almanya-burslari-nelerdir).
MD,

            'daad-burs-basvuru-tarihleri-ne-zaman' => <<<'MD'
DAAD burs başvuru tarihleri kategoriden kategoriye değişir — **doğru deadline'ı kaçırmak başvurunun reddine sebep olur.**

## Master Studies in Germany (En Popüler)

### Wintersemester (Ekim 2026 Başlangıç) İçin
- **Başvuru deadline:** **30 Eylül 2025** (önceki yılın sonu)
- **Mülakat:** Ekim-Kasım 2025
- **Karar:** Aralık 2025
- **Burs başlangıcı:** Ekim 2026
- **Almanca kurs (gerekirse):** Nisan-Ekim 2026

### Sommersemester (Nisan 2026 Başlangıç) İçin
- **Başvuru deadline:** **15 Haziran 2025**
- **Mülakat:** Temmuz-Eylül 2025
- **Karar:** Ekim 2025
- **Burs başlangıcı:** Nisan 2026

⚠️ **Wintersemester (Ekim) en yaygın başlangıç** — kategorilerin çoğu Wintersemester için tasarlandı.

## PhD in Germany

### 3 Farklı Başvuru Dönemi
- **Mart başvurusu:** Bahar dönemi başlangıç → Eylül 2026
- **Mayıs başvurusu:** Sonbahar başlangıç → Şubat 2026
- **Eylül başvurusu:** Bahar başlangıç → Mayıs 2026

### Karar Süreleri
- Başvurudan kabul mektubuna: **6-8 ay**
- Mülakat genelde başvurudan 2-3 ay sonra

## Diğer Kategori Tarihleri

### Re-invitation for Former Scholarship Holders
- **Başvuru:** Tüm yıl açık (rolling basis)
- **Karar:** 3-4 ay

### Research Stays for Doctoral Candidates (Short-term)
- **Mart başvurusu:** Eylül 2025 başlangıç → Mart 2026 deadline
- **Mayıs başvurusu:** Aralık 2025 → Mayıs 2026
- **Eylül başvurusu:** Mart 2026 → Eylül 2026

### Working Internships in Science and Engineering (WISE)
- **Başvuru deadline:** **15 Aralık 2025** (Yaz 2026 staj için)
- **Karar:** Şubat-Mart 2026
- **Staj:** Haziran-Eylül 2026

### Summer Course Programme
- **Başvuru deadline:** **1 Aralık 2025** (Yaz 2026 kursu için)
- **Karar:** Şubat 2026
- **Kurs:** Temmuz-Ağustos 2026

### Bi-nationally Supervised PhD (Sandwich)
- **Başvuru deadline:** 15 Şubat, 15 Mayıs, 15 Eylül
- **Karar:** 3-4 ay

### Faculty Research Stays
- **Başvuru:** Tüm yıl açık
- **Karar:** 3-4 ay

## 2026 İçin Kritik Deadlinelar

| Tarih | Burs | Hedef başlangıç |
| --- | --- | --- |
| **1 Aralık 2025** | DAAD Summer Course | Yaz 2026 |
| **15 Aralık 2025** | WISE Programme | Yaz 2026 |
| **15 Şubat 2026** | KAS/FES Master | Sonbahar 2026 |
| **15 Mart 2026** | DAAD PhD (1. dönem) | Sonbahar 2026 |
| **15 Mayıs 2026** | DAAD PhD (2. dönem) | Şubat 2027 |
| **15 Haziran 2026** | DAAD Master Sommersemester | Nisan 2027 |
| **30 Eylül 2026** | DAAD Master Wintersemester | Ekim 2027 |

## Başvuru Hazırlık Süresi (Önerilen)

### Master Studies için (Wintersemester)
- **Başvuru deadline:** 30 Eylül 2025 (Ekim 2026 başlangıç)
- **Önceki yıl Şubat'ında başla:**
  - Şubat-Mart: Belgeleri topla (diploma, transkript, tercüme)
  - Nisan-Mayıs: Üniversite kabul başvurusu (Anabin denkliği için)
  - Haziran-Ağustos: DAAD belgelerini tamamla (motivasyon mektubu, referans)
  - Eylül başı: Online başvuru gönder

⚠️ **Toplam hazırlık süresi: 6-8 ay**

### PhD için
- **Başvuru deadline:** 15 Mayıs 2025 (Şubat 2026 başlangıç)
- **Önceki yıl Eylül'de başla:**
  - Eylül-Aralık: Forschungsexposé hazırla, profesör arama
  - Ocak-Şubat: Profesörel iletişim, Betreuungszusage
  - Mart-Nisan: Belgeleri tamamla
  - Mayıs başı: Online başvuru

## Sık Yapılan Hata

❌ **Son 1 ay'da başvuru hazırlamak** — yetmez, belgeleri eksik kalır
❌ **Üniversite kabulü deadline'ı es geçmek** — DAAD başvurusu üni kabuli istiyor
❌ **Dil sertifikası geç almak** — TestDaF/IELTS hazırlığı 3-4 ay sürer
❌ **Apostil sürecini hesap etmemek** — kaymakamlık + valilik 2-3 hafta

## Önemli İpucu

✅ **Başvurudan 1 yıl önce hazırlık başlat**
✅ **Üniversite kabulü** DAAD başvurusundan önce gelmeli (Mayıs'ta üni başvurusu yap, Temmuz-Eylül'de cevap, Ekim'de DAAD)
✅ **Belge eksikliği** başvurunun otomatik reddine yol açar — çift kontrol

## Akademik Yıl Yapısı (Almanya)

### Wintersemester
- Başlangıç: **Ekim 1**
- Bitim: **Mart 31**
- En yaygın master başlangıç dönemi

### Sommersemester
- Başlangıç: **Nisan 1**
- Bitim: **Eylül 30**
- Geç başvuru veya değişim için

⚠️ **Çoğu master programı sadece Wintersemester** kabul ediyor — bu yüzden Eylül deadline'ı kritik.

İlgili: [DAAD başvuru süreci](/sss/burs/daad-bursu-nasil-alinir-basvuru-sureci-nedir) | [DAAD belgeler](/sss/burs/daad-basvurusu-icin-hangi-belgeler-gerekli).
MD,

            'burs-sonrasinda-turkiyeye-donus-zorunlulugu-var-mi' => <<<'MD'
**DAAD ve çoğu Alman bursunda geri dönüş zorunlu DEĞİL**, ama bazı Türk burslarında (TEV, TÜBİTAK) **5 yıl Türkiye'de çalışma şartı** var.

## DAAD'da Geri Dönüş Durumu

### Resmi Politika
✅ **DAAD geri dönüş zorunluluğu YOK** — mezun sonrası Almanya'da kalabilirsin
✅ **Job Search Visa** (18 ay) ile iş arama
✅ **Almanya'da iş bulup oturum** alabilirsin
✅ **DAAD Alumni** ağı geri dönüş + Almanya'da kalış için her iki yönde destek

### Pratik Beklenti
⚠️ DAAD'ın **örtük beklentisi**: Türkiye'ye dönerek **Türk-Alman akademik/profesyonel köprü** kur
⚠️ DAAD Alumni etkinliklerinde **Türkiye katkısı** olarak değerlendirilen yayın/proje yapma teşvik edilir
⚠️ Türkiye'de Türk-Alman ilişkilerinde katkı sağlayan eski DAAD bursiyerleri **prestijli**

## Politik Vakıf Burslarında

### KAS, FES, Böll, Naumann
✅ **Resmi geri dönüş zorunluluğu YOK**
✅ Pratik beklenti: **Türkiye'de demokratik değerlere katkı**
✅ Politik network + bilgi paylaşma

### KAAD (Katolik)
✅ **Geri dönüş zorunlu DEĞİL**
✅ Beklenti: Türk-Alman dinler arası diyaloğa katkı
✅ Türk Hristiyan/Müslüman topluluklarda etkinlik

## Türk Burslarında (TEV, TÜBİTAK, Vehbi Koç)

### TEV Yurt Dışı Burs Programı
⚠️ **5 yıl geri dönüş + Türkiye'de çalışma şart**
⚠️ Sözleşmede yazılı — geri ödeme yapmadan ihlal mümkün değil
⚠️ Geri dönmezsen → tüm burs tutarını **iade etme zorunluluğu**

### TÜBİTAK 2213
⚠️ **PhD sonrası Türkiye'de en az 5 yıl akademik çalışma** zorunlu
⚠️ Üniversiteye geri dönüş zorunlu (TÜBİTAK + üniversite anlaşması)
⚠️ Geri dönmezsen → burs iadesi + ek ceza

### TÜBİTAK 2214-A (6-12 ay araştırma)
✅ **Türkiye'de PhD tamamlama** zorunlu (Almanya'da değil)
✅ Sonrası serbest

### Vehbi Koç Vakfı
⚠️ **5 yıl Türkiye'de çalışma** beklenir (kontrata göre)
⚠️ İhlal durumunda burs iadesi

## Geri Dönüş Zorunluluğunu Aşma Yolları

### Türk Bursunda Geri Dönmek İstemiyorsan
1. **Bursu iade et** — sözleşmede belirtilen tutarı geri öde
2. **Türkiye'de kısa süreli çalışma** (2-3 yıl) sonra Almanya'ya dönüş
3. **Hukuki danışmanlık** al — sözleşmenin esnek noktaları olabilir

### Alman Bursunda Almanya'da Kalmak İstiyorsan
1. **Job Search Visa** (mezun olduktan sonra 18 ay)
2. **Werkstudent → tam zamanlı iş** geçişi
3. **Mavi Kart** (Blue Card EU) — yüksek vasıflı çalışan vizesi
4. **Almanya'da PhD bursu** (Master sonrası) → kalıcı kalış

## Almanya'da Kalmak İçin Mavi Kart (Blue Card EU)

### Şartlar
✅ **Bachelor/Master/PhD diploması** Almanya'dan veya tanınan üniversiteden
✅ **İş sözleşmesi** veya **iş teklifi** (Almanya'da)
✅ **Yıllık brüt maaş ≥ 56,400 € (2026)** veya
✅ **Yüksek talepli meslek** (BT, mühendislik, sağlık) için **44,000 €+**

### Süre
- **4 yıl** Mavi Kart
- Sonrasında **Niederlassungserlaubnis** (kalıcı oturum) başvurusu mümkün
- 21 ay Almanya'da yaşama + Almanca A1 → kalıcı oturum
- 33 ay Almanya'da yaşama → kalıcı oturum (dil yeterliliği daha düşük)

## Çifte Burs Almak İçin Etik Tavsiye

### Yasak Durumlar
❌ **DAAD + TEV aynı anda alma** — ikisi de bildiğin için ihlal sayılır
❌ **DAAD + KAS aynı anda** — vakıflar bildirim yapıyor
❌ **TÜBİTAK 2213 + DAAD PhD aynı anda** — iki kurum birbirine bildiriyor

### İzin Verilen Durumlar
✅ **TEV bursu + üniversiteden ek araştırma fonu** (akademik dışı)
✅ **DAAD + üniversiteden TVöD asistan pozisyonu** (DAAD %50, asistan %50 maaş)
✅ **TÜBİTAK 2214-A + Alman üniversite kısmi destek** (TÜBİTAK ana, üni katkı)

## Pratik Strateji

### Almanya'da Kalmak İstiyorsan
1. **Alman bursu** (DAAD, KAS, FES) seç → geri dönüş zorunluluğu yok
2. **Türk bursunu reddedin** (TEV almazsan sonraki burs için yedek)
3. Mezuniyet sonrası **Mavi Kart + iş** yolu

### Türkiye'de Devam Etmek İstiyorsan
1. **TÜBİTAK 2214-A + DAAD Sandwich** (Türkiye'de PhD + 6-12 ay Almanya araştırma)
2. **TEV bursu** ile master + zorunlu 5 yıl Türkiye akademik kariyer
3. Mezun sonrası Türkiye'de iş bulup geliştir

### Esneklik İstiyorsan
1. **DAAD Master** (geri dönüş esnek)
2. Master sonrası **karar ver:**
   - PhD Almanya'da → DAAD PhD veya üni pozisyonu
   - İş Almanya'da → Mavi Kart
   - Türkiye'ye dönüş → akademik veya iş kariyer

## Türkiye'ye Dönüş İçin Destekler

### Mezun Sonrası Türk Bursiyerlere
✅ **DAAD Alumni Türkiye** — etkinlik + network
✅ **TÜBİTAK Akademik İstihdam Programı** — yurt dışı PhD sonrası
✅ **Türk Akademik Mezunlar Ağı** (TÜABA) — destek
✅ **Türk-Alman Üniversitesi (TAÜ)** — Almanya mezunlarına öncelik

## Önemli Notlar

⚠️ **Sözleşmeyi dikkatlice oku** — TEV/TÜBİTAK'ta 5 yıl zorunluluk açıkça yazılı
⚠️ **Yasal danışman al** — sözleşmenin nüansları için
⚠️ **DAAD geri dönüş zorunlu değil** ama **alumni katkısı beklenir** — Türkiye'de etkinliklere katıl

İlgili: [TEV bursu](/sss/burs/turkiyeden-almanya-bursu-icin-tev-gibi-yerel-kaynaklar-var-mi) | [DAAD başvuru](/sss/burs/daad-bursu-nasil-alinir-basvuru-sureci-nedir).
MD,
        ];
    }
}

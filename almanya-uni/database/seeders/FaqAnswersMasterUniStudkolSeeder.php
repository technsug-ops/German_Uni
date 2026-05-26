<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

/**
 * Curated answers for Master/PhD + Uni-Assist + Studienkolleg topics.
 * Combined with VİZE+DİL = ~82% of total community demand.
 */
class FaqAnswersMasterUniStudkolSeeder extends Seeder
{
    public function run(): void
    {
        $answers = array_merge(
            $this->masterAnswers(),
            $this->uniAssistAnswers(),
            $this->studienkollegAnswers(),
        );

        foreach ($answers as $slug => $md) {
            $faq = Faq::where('slug', $slug)->first();
            if (!$faq) {
                $this->command?->warn("FAQ not found: {$slug}");
                continue;
            }
            $faq->answer_md = trim($md);
            $faq->save();
        }
    }

    private function masterAnswers(): array
    {
        return [
            'almanyada-ucretsiz-master-mumkun-mu' => <<<'MD'
**Evet — Almanya'da master öğrenimi büyük ölçüde ücretsiz.** Devlet üniversitelerinin neredeyse tamamı master programlarını öğrenim ücreti almadan sunar.

## Ücret Yapısı

| Üniversite tipi | Yıllık öğrenim ücreti |
| --- | --- |
| **Devlet üniversitesi** | 0 € (sadece semester ücreti ~150-350 €) |
| **Baden-Württemberg AB dışı öğrenci** | 3,000 €/yıl |
| **Özel üniversite** | 10,000-25,000 €/yıl |
| **Executive MBA, sertifika programları** | 15,000-50,000 € (toplam) |

## Semester Beitrag (Yarıyıl Katkı Payı)

"Ücretsiz" derken aslında **150-350 € arası bir yarıyıl katkı payı** ödüyorsun. Bu para:
- Toplu taşıma kartı (Semesterticket) — şehir içi sınırsız
- Öğrenci servisleri (Studentenwerk)
- Mensa indirimi, kütüphane

## Yaşam Maliyeti Ayrı

Eğitim ücretsiz olsa bile yaşam pahalı:
- Kira: 350-700 €/ay
- Yemek: 200-300 €/ay
- Sigorta: ~130 €/ay
- **Toplam aylık: ~900-1200 €**

Bu yüzden vize için **Sperrkonto 11,904 €** istiyorlar.

## Ücretsiz Master Erişimi

Türk öğrenci olarak ücretsiz master için:
1. **TestDaF/IELTS sertifikası** al
2. **Uni-Assist VPD** çıkar
3. Devlet üniversitelerine başvur (BW dışı)
4. Kabul gelince vize

⚠️ **Baden-Württemberg** istisna — 3,000 €/yıl AB dışı öğrenciler için (TR vatandaşı dahil).

Bağlantılı: [Master için Almanca şartı](/sss/dil/b2-sertifikasi-ile-master-basvurusu-yapabilir-miyim) · [Bloke hesapta ne kadar?](/sss/vize/bloke-hesapta-ne-kadar-para-olmasi-gerekiyor)
MD,

            'goethe-b2-ile-sartsiz-master-kabulu-alabilir-miyim' => <<<'MD'
**Çoğu Almanca master programı için B2 yetmez** — standart şart C1. Ama bazı istisnalar var.

## B2 Yeterli Programlar

✅ **İngilizce master programları** — Almanca B2 yan kriter
✅ **Bazı sosyal bilim/sanat programları** — uniye göre değişir
✅ **Şartlı kabul (Bedingte Zulassung)** — geldikten sonra C1'e çıkarman koşuluyla
✅ **Sanat akademileri (Kunst, Musik)** — portfolio ağırlıklı

## B2 Yetmeyen Programlar

❌ Mühendislik (TU München, RWTH, KIT)
❌ Tıp ve sağlık bilimleri
❌ Hukuk masterı (LLM)
❌ Almanca filolojisi, edebiyat
❌ Tarih, felsefe, sosyoloji (klasik üniversitelerde)

## Şartlı Kabul Stratejisi

Bazı uniler "C1 olmadan kabul, ilk dönem içinde tamamla" diyor:
1. Uni başvurusunda B2 ile başvur
2. Şartlı kabul (Vorbehalt) alırsın
3. Vize başvurusunda bu kabul belgesi yeterli
4. Almanya'da ilk 1-2 dönem C1 hazırlığı + DSH-2 sınavı
5. C1 sertifikası al → tam kayıt

⚠️ Riski: C1'i çıkaramazsan kayıt iptal, vize sorunu.

## B2 ile Başvurabileceğin Yaygın Programlar

- **Public Policy** (Hertie School)
- **International Business** (bazı uygulamalı üniler)
- **Tasarım, mimari** (portfolio ağırlık)
- **Sanat & müzik akademileri**
- **Almancılık-dışı bölümler** + İngilizce program

## Pratik Yol

1. **DAAD veritabanında** İngilizce master ara: https://www2.daad.de
2. B2 ile bunlara başvur — Almanca pratik avantaj olur
3. Paralel C1'e hazırlan — gelmeden veya geldiğinde

Bağlantılı: [İngilizce master Almanca seviyesi](/sss/master/ingilizce-master-programlarinda-almanca-seviyesi-gerekli-mi) · [IELTS ile master](/sss/dil/ielts-ile-almanyada-ingilizce-master-basvurusu-yapilabilir-mi)
MD,

            'dusuk-gpa-ile-almanyada-masterlisans-kabulu-alinir-mi' => <<<'MD'
Almanya'da kabul **sadece GPA'ya bağlı değil** — düşük not ortalamasıyla da yol var.

## GPA'nın Önemi

| GPA (TR 4'lük) | Almanya bakış |
| --- | --- |
| **3.5+** | Çok iyi (Sehr gut, 1.0-1.5) |
| **3.0-3.5** | İyi (Gut, 1.5-2.5) |
| **2.5-3.0** | Yeterli (Befriedigend, 2.5-3.5) |
| **2.0-2.5** | Geçer (Ausreichend, 3.5-4.0) |
| **2.0 altı** | Düşük — strateji gerekli |

## Düşük GPA ile Stratejiler

### 1. NC'siz programlara odaklan
Numerus Clausus (kontenjan sınırı) olmayan programlar:
- Pek çok mühendislik (TU dışı)
- Bazı doğa bilimleri
- Daha az popüler şehirler

### 2. Daha az talepkar üniversitelere yönel
- TU München, LMU, Heidelberg → yüksek GPA istiyor
- Bazı doğu bölgesi üniler (Magdeburg, Cottbus, Greifswald) → daha esnek
- Fachhochschule'ler → genelde daha esnek

### 3. Motivasyon mektubu güçlü olsun
1.000-1.500 kelime, somut deneyim + program araştırması + neden bu uni

### 4. Profesyonel deneyim göster
İş tecrübesi, staj, proje yayın → GPA dezavantajını kapatır

### 5. Almanca seviyeni yükselt
C1+ Almanca düşük GPA'ya rağmen kabul avantajı

### 6. Studienkolleg alternatifi (lisans için)
Düşük lise GPA ile direkt Bachelor zor → Studienkolleg yıl içinde notları sıfırlar.

## Master için Spesifik Yollar

✅ **İngilizce master programları** — İngilizce sınav yüksek + motivasyon mektubu kritik
✅ **Yapay zekâ/data science gibi yeni alanlar** — endüstri ilişkili, esnek
✅ **Özel üniversiteler** (parası varsa) — daha esnek kabul kriteri

⚠️ Tıp, hukuk, prestijli programlar düşük GPA'ya esneklik göstermiyor.

## Pratik İpucu

**Ortalama hesaplama:** Almanya 1.0 en iyi (4.0 değil). TR 4.0'lık sisteminde 3.0 → Almanya'da yaklaşık 2.0-2.5 (orta-iyi).

Bağlantılı: [NC nedir](/sss/master/nc-nedir-master-basvurusunu-nasil-etkiler)
MD,

            'nc-nedir-master-basvurusunu-nasil-etkiler' => <<<'MD'
**NC (Numerus Clausus)**, talebin kontenjandan fazla olduğu programlarda **giriş notu sınırı** anlamına gelir.

## NC Nasıl Hesaplanır?

NC sabit değil — **her dönem aday GPA'larına göre belirlenir.** Bir programa örnek:
- 100 kontenjan
- 500 başvuru
- En yüksek 100 GPA'lı kabul edilir
- 100. sıradaki adayın notu o dönemin **NC'si** olur

| Örnek program | Tipik NC (2025 WiSe) |
| --- | --- |
| Tıp (Medizin) | 1.0-1.2 (en yüksek!) |
| Hukuk (Jura) | 1.5-2.5 |
| Psikoloji | 1.2-1.8 |
| Mühendislik | 2.0-3.0 veya **NC frei** (kontenjan dolmaz) |
| İşletme | 2.0-2.8 |

## NC ile Aday Olarak Neyle Karşılaşırsın?

1. **NC frei** = kontenjan sınırlı değil → tam başvuru kabul edilir
2. **Örtlich zulassungsbeschränkt** = uni kendi belirler, GPA + bekleme süresi karması
3. **Bundesweit zulassungsbeschränkt** = merkezi sistem (DoSV) yönetir — sadece tıp, diş, eczacılık, veterinerlik

## Uluslararası Öğrenciler için NC

Türk vatandaşı olarak **AB-dışı kontenjan** çoğunlukla %5-10 ayrı sayılır. Kontenjan dolmamışsa **NC etkilenmez** — sen başvurursun, GPA'na bakılır.

## NC'siz Master Bulma

NC olmayan programlar bol:
- Daha az popüler bölümler
- Yeni başlayan programlar
- Bazı uygulamalı bilimler (FH)
- DAAD veritabanında "Open admission" filtresi

## NC Yüksek ise Stratejiler

1. **Bekleme dönemi (Wartesemester)** — TR mezunları için sınırlı geçerli
2. **Test puanı (TestAS)** — yüksek puan GPA'yı dengeler
3. **Mesleki deneyim** — bazı programlarda not avantajı verir
4. **Alt sıradaki ünilere başvur** — aynı programı daha az talep gören uniden al

## Master Başvurusunda NC'nin Yeri

Master için NC daha az yaygın — çoğu master "uygunluk değerlendirmesi" yapar (motivasyon + transcript + sınav). Sadece çok talep edilen master programları NC uygular.

Bağlantılı: [Düşük GPA ile kabul](/sss/master/dusuk-gpa-ile-almanyada-masterlisans-kabulu-alinir-mi)
MD,

            'bachelor-ve-master-farkli-bolumlerde-olabilir-mi' => <<<'MD'
**Evet, mümkün** — ama master programının kabul kriterleri **bachelor temelin uygun olmasını** ister. Tamamen ilgisiz geçiş zor.

## Yaygın Geçiş Türleri

| Bachelor | Mümkün Master | Şart |
| --- | --- | --- |
| Endüstri Müh. | Industrial Eng., Operations Research, MBA | ✅ Direkt |
| Bilgisayar Müh. | Data Science, AI, Cybersecurity | ✅ Direkt |
| Elektrik Müh. | Bilgisayar Müh. masterı | ✅ Genelde |
| Matematik/Fizik | Bilgisayar Müh., Data Science | ✅ Bazı uniler |
| İşletme | Finance, Marketing, MBA | ✅ Direkt |
| Psikoloji | Psikoloji masterı | ✅ Direkt |
| Mühendislik | Public Health, Sosyal Politika | ⚠️ İlgili dersler |
| İşletme | Mühendislik masterı | ❌ Zor |

## Kontrol Edilen Kriter: "Vorbildung" (Ön Eğitim)

Master kabul komitesi transcriptini okur:
- Müfredatta hangi temel dersler var?
- Kredi sayısı yeterli mi (mühendislik master için 30+ kredi temel ders)?
- Hangi notlar?

Eksik varsa:
- **Auflagen** (ek dersler) → master içinde +20-30 kredi tamamlaman istenir
- **Bedingte Zulassung** → master öncesi kapatman gerekir

## Düşük Uyumlulukta Geçiş Yolları

✅ **Bridge program** (Brückenkurs) — bazı uniler düzenler
✅ **Bachelor 2** — yeni bir lisansa daha kısa süreli giriş
✅ **MBA** — geniş kabul, profesyonel deneyim ağırlığı
✅ **Public Health, Data Science gibi interdisciplinary alanlar**

## Pratik Plan

1. Hedef master programının **Modulhandbuch'una** bak (müfredat detay)
2. Senin bachelor transcript'inle karşılaştır — hangi dersler eksik?
3. Uni'nin **Studienberatung'una** mail at — "transcript'imi gönderiyorum, uygunluk var mı?"
4. Eksik dersleri Türkiye'de online MOOC ile telafi et (Coursera certificate)

## Vize için Önemi

Vize başvurusunda master kabul belgesi gözükünce — bachelor uyumsuzluğu kontrol edilmez. Master kabulü tek başına yeterli.

Bağlantılı: [Master için Almanca şartı](/sss/master/sosyal-bilimler-master-programlari-icin-almanca-sarti-nedir)
MD,

            'master-sonrasi-is-arama-vizesi-jobsuche-kac-ay-gecerli' => <<<'MD'
Master mezuniyetinden sonra **18 ay (1.5 yıl)** geçerli iş arama vizesi (§ 20 AufenthG) alma hakkın var. Bu süre içinde iş bulabilirsen Mavi Karta veya çalışma iznine geçersin.

## Şartlar

✅ Almanya'da tamamlanmış lisans veya master diploması
✅ Yeterli yaşam masrafını kanıtlama (Sperrkonto veya gelir)
✅ Sağlık sigortası (öğrenci tarifesinden çıkıyorsun)
✅ Mezuniyet anından **6 ay içinde** başvuru

## Süreç

1. Diploma alır almaz Ausländerbehörde randevu al
2. Mevcut öğrenci oturum izninin **iş arama vizesine geçişi** talep et
3. Yeni kart 18 ay geçerli
4. **Çalışma izninde sınırsız haftada saat** çalışabilirsin (öğrenci dönemden farklı)

## İş Bulduktan Sonra

İş bulduğunda:
- **Mavi Kart (Blaue Karte EU):** Yıllık brüt ~45,300 € + üzeri (2026), STEM alanları için ~41,041 €
- **Standart çalışma izni (§ 18a):** Mavi Kart altı maaşlarda
- **Profesör/öğretmen kadrosu:** Özel düzenleme

## 18 Ay'da İş Bulamazsam?

❌ Vize uzatılmaz (özel durum hariç)
❌ Türkiye'ye dönmen gerekir
❌ Sonra Almanya'dan yeniden iş arama vizesi başvurusu mümkün ama yeni başvuru

## Pratik Tavsiye

1. **Mezuniyetten 6 ay önce iş aramaya başla** — staj/Werkstudent network'ü kullan
2. **LinkedIn DE'da pasif aday** ol → Almanca + İngilizce profil
3. **Master tezini şirket ortaklığında** yap → mezuniyet → direkt teklif (yaygın yol)
4. **Almanca C1+** kritik — İngilizce master mezunları için bile

## Kalıcı Oturum (Niederlassungserlaubnis)

Master mezunu olarak 2 yıl Mavi Kartla çalıştıktan sonra **kalıcı oturum** alabilirsin (B1 Almanca ile yeter). Standart çalışma izninde 5 yıl gerekir.

Bağlantılı: [Almanya öğrenci vizesi kaç yıl?](/sss/vize/almanya-ogrenci-vizesi-kac-yil-gecerli-oluyor)
MD,

            'almanyada-phd-programlari-nasil-bulunur' => <<<'MD'
Almanya'da PhD (Doktorat) **iki temel yolla** alınır: yapılandırılmış doktora programları (Structured) veya geleneksel hoca-öğrenci modeli (Individual).

## İki Tip Doktora

### 1. Strukturierte Promotion (Yapılandırılmış)
- 3 yıl program, müfredat var
- DAAD veya üniversite kontenjanı
- Stipendium (burs) genelde dahil
- İngilizce yürütülen programlar çok
- Daha kolay başvurmak ama daha rekabetçi

### 2. Individuelle Promotion (Bireysel)
- 3-5 yıl, hocayla bire bir
- Önce **Doktorvater/mutter** bulmak gerekiyor
- WiMi (Wissenschaftlicher Mitarbeiter) kadrosu — maaşlı (~50-65k €/yıl brüt)
- Akademik dünyaya hızlı entegrasyon
- Daha esnek ama kendi kapını çalman lazım

## PhD Arama Kaynakları

🔍 **Programlı PhD için:**
- **DAAD**: https://www2.daad.de/deutschland/studienangebote/phd
- **Helmholtz**: Hellholtz araştırma merkezleri
- **Max Planck**: International Max Planck Research School
- **Leibniz**: Leibniz Graduate Schools
- **PhDGermany**: https://www.daad.de/phd-germany

🔍 **Bireysel PhD için:**
- **Academics.de**: https://www.academics.de — kadro ilanları
- **Stellenwerk** (uni iş ilanı portal)
- Üniversite kendi siteleri — Lehrstuhl sayfaları
- DAAD Stellenanzeige (akademik iş)
- ResearchGate — profesör profilleri

## Başvuru Süreci

### Adım 1: Konu/Hoca Eşleştirmesi
- Master tezi konuna yakın hoca bul
- Hoca'nın son 5 yıl yayınlarını oku
- Senin proje önerinle ne kadar uyumlu?

### Adım 2: İlk Temas (Cold Email)
- Kişisel mail (bcc liste değil)
- 2-3 paragraf: kim olduğun, neden bu hoca, kısa proje fikri
- CV + transcript ekle
- Almanca veya İngilizce, hocanın dil tercihi

### Adım 3: Görüşme
- Online veya Almanya'da yüz yüze
- Proje önerini detaylandır (3-5 sayfa)

### Adım 4: Resmi Başvuru
- Hocayla anlaşırsan üniversite formal başvuru
- Promotionsausschuss (komite) onayı
- Doktora çalışması başlar

## Finansman

| Tip | Aylık |
| --- | --- |
| **DAAD bursu** | ~1,300-1,500 € |
| **WiMi kadrosu (TV-L 13)** | ~2,800-3,400 € net |
| **Helmholtz/Max Planck stipendium** | ~1,500-2,500 € |
| **Endüstri-uni ortak PhD** | ~3,000-5,000 € |

## Şartlar

- Master diploması (genelde 2.0 veya üstü Alman notu)
- İngilizce yeterliliği veya Almanca C1
- TestAS bazı programlarda
- Yayın varsa büyük artı

Bağlantılı: [Master sonrası iş arama vizesi](/sss/master/master-sonrasi-is-arama-vizesi-jobsuche-kac-ay-gecerli) · [DAAD bursu](/sss/burs)
MD,

            'public-health-master-icin-almanyadaki-secenekler-nelerdir' => <<<'MD'
Public Health (Halk Sağlığı) Almanya'da gelişmekte olan bir alan — **MPH** veya **MSc Public Health** olarak 30+ program mevcut.

## Önde Gelen Programlar

### Charité Berlin — MSc Public Health
- 4 dönem, **İngilizce**
- Klinik araştırma + epidemiyoloji ağırlık
- Yıllık ücret: 0 € (devlet uni)
- DAAD bursu için TOP tercih

### Heidelberg — MSc International Health
- 2 dönem yoğun + tez
- WHO ile bağlantılı
- Tropik hastalık + global health odaklı
- İngilizce, ücretsiz

### LMU München — MSc Public Health
- 4 dönem, İngilizce
- Bavyera Yenilik Lab'ı ile çalışma
- Yüksek talep, NC yüksek

### Hannover MHH — MPH
- 4 dönem, **Almanca** (bazı dersler İngilizce)
- 30+ yıl köklü program
- Sağlık politikası odaklı

### TU Dresden — MSc Public Health
- Yenilikçi dijital sağlık modülleri
- İngilizce

### Bremen — MPH (Master of Public Health)
- 4 dönem, Almanca
- Bremen Üniversitesi + sağlık enstitüleri ortak

## Başvuru Şartları

✅ **Lisans diploması** — tıp, biyoloji, hemşirelik, psikoloji, mühendislik, sosyal bilim (geniş yelpaze)
✅ **Dil**: İngilizce TOEFL 90+/IELTS 6.5+ veya Almanca C1
✅ **Mesleki deneyim** — bazı programlar 1-2 yıl klinik/saha çalışması ister (Charité, Hannover)
✅ **Motivasyon mektubu** — somut araştırma ilgi alanı

## Public Health Alt Alanlar

- **Epidemiyoloji** → veri analizi, salgın yönetimi
- **Sağlık ekonomisi** → politika, sigorta sistemleri
- **Küresel sağlık (Global Health)** → WHO, tropik hastalık
- **Sağlık iletişimi** → kamu kampanyaları, davranış değişimi
- **Çevre sağlığı** → iklim, hava kirliliği

## Mezuniyet Sonrası Kariyer

🏥 Halk sağlığı uzmanı (RKI — Robert Koch Institute)
🌍 WHO, GIZ, uluslararası NGO
📊 Sigorta şirketi sağlık analisti
🏛️ Sağlık bakanlığı, eyalet daireleri
🎓 PhD'ye geçiş — akademik kariyer

## Maaş Beklentisi (Mezun)

| Kıdem | Yıllık brüt |
| --- | --- |
| Junior (0-2 yıl) | 40-50k € |
| Mid (3-7 yıl) | 55-70k € |
| Senior | 70-95k € |
| Direktör/uzman | 95k+ € |

Bağlantılı: [İngilizce master Almanca seviyesi](/sss/master/ingilizce-master-programlarinda-almanca-seviyesi-gerekli-mi)
MD,

            'bilgisayar-muhendisligi-master-icin-deatch-karsilastirmasi' => <<<'MD'
**DE/AT/CH** üçlüsü Türk öğrenciler için en popüler Almanca konuşan ülkeler. Master için karşılaştırma:

## Karşılaştırma Tablosu

| Açı | 🇩🇪 Almanya (DE) | 🇦🇹 Avusturya (AT) | 🇨🇭 İsviçre (CH) |
| --- | --- | --- | --- |
| **Öğrenim ücreti** | 0 € (BW 3K €) | ~1500 €/dönem | 1-2K CHF/dönem |
| **Yaşam maliyeti** | ~1000 €/ay | ~1100 €/ay | **~2500 CHF/ay** |
| **Sperrkonto** | 11,904 € | ~6,000-7,500 € | 21,000+ CHF |
| **Dil şartı** | Almanca C1 veya İngilizce | Almanca C1 veya İngilizce | İngilizce ETH/EPFL |
| **Top uniler (CS)** | TUM, RWTH, KIT, TU Berlin | TU Wien, JKU Linz | **ETH Zürich, EPFL Lausanne** |
| **Çalışma izni** | 120 gün tam / 240 yarı | 20 saat/hafta | 15 saat/hafta |
| **Mez. sonrası iş arama** | 18 ay | 12 ay | 6 ay |
| **Mavi Kart eşiği** | 45,300 €/yıl (STEM 41,041 €) | 49,000 €/yıl | 80-100K CHF |

## En Avantajlı?

### Maliyet/Kalite → 🇩🇪 Almanya
- En düşük masraf, en geniş program seçeneği
- TU München, RWTH, TU Berlin → küresel TOP 100
- Mezuniyet sonrası 18 ay iş arama

### Akademik Prestij → 🇨🇭 İsviçre
- ETH Zürich → küresel TOP 10
- EPFL Lausanne → TOP 20
- Maaş yüksek (junior 80-100k CHF)
- Ama: pahalı, kontenjan dar, AB-dışı için zor

### Esneklik → 🇦🇹 Avusturya
- Almanca eğitim + ucuz
- TU Wien dünya çapında iyi
- Vize biraz daha kolay (DE'ye göre evrak az)

## Top Bilgisayar Müh. Master Programları

### Almanya
- **TUM Informatics** (Garching)
- **RWTH Aachen Computer Science**
- **TU Berlin Computer Engineering**
- **KIT Karlsruhe Informatics**
- **Saarland University CS** (Max Planck bağlantılı)

### Avusturya
- **TU Wien Informatics**
- **JKU Linz Computer Science**
- **TU Graz Computer Science**

### İsviçre
- **ETH Zürich CS**
- **EPFL Computer Science**
- **University of Zurich Data Science**

## Vize Kıyası

| Ülke | Vize zorluğu | Sperrkonto sıkıntısı |
| --- | --- | --- |
| Almanya | Orta | Düzenli |
| Avusturya | Orta-kolay | Düzenli |
| İsviçre | Zor (AB-dışı kota) | Çok yüksek |

## Tavsiye

✅ **DE'ye git** — bütçe makul, fırsat çok, mezuniyet sonrası iş ekosistemi büyük (Bosch, SAP, BMW, Siemens, start-up'lar).
✅ **CH'a sadece TOP 1-2 uni** ile gid — fırsat yoksa DE daha mantıklı.
✅ **AT'yi yedek** olarak tut — kabul gelirse DE'ye paralel düşün.

Bağlantılı: [Master sonrası iş arama vizesi](/sss/master/master-sonrasi-is-arama-vizesi-jobsuche-kac-ay-gecerli)
MD,

            'online-master-diplomasi-almanyada-gecerli-mi' => <<<'MD'
**Genelde hayır** — Almanya'da online (uzaktan) master diploması iş piyasası ve akademik dünyada **klasik diplomadan zayıf** algılanır. Ama istisnalar var.

## Almanya'nın Online Master Bakış Açısı

Almanya akademisi/işvereni **kontaktstudium** (yüz yüze) ağırlık verir:
- Klasik master diploması daha prestijli
- Online master "düşük standartlı" algılanma riski
- Mezuniyet sonrası akademik kariyer için (PhD) ciddi engel

## Online Master Kabul Edilen Durumlar

✅ **Açılış akredite** — Alman akreditasyon ajansı (AQAS, ACQUIN) tanıdığı program
✅ **Klasik uni + online module** karma — örnek: Hagen (Fernuniversität) — Almanya'nın resmi uzaktan eğitim devleti
✅ **Mevcut işle paralel** — çalışan profesyonel kariyer geliştirme
✅ **MBA executive programlar** — Mannheim, ESMT Berlin online ağırlık karma

## Tanınmış Online Master Programları (DE)

### Fernuniversität Hagen
- Devlet üniversitesi — tamamen uzaktan
- Düşük ücret (~1500 €/dönem)
- Çok yaygın kabul

### IUBH Internationale Hochschule
- Özel uni, online + yüz yüze
- 100% İngilizce programlar
- ~12k €/yıl

### IU International University
- Tamamen online MBA + master
- 200+ alan
- ~15k €/program

### WBH Wilhelm Büchner Hochschule
- Mühendislik online master
- Endüstri ortaklı projeler

## "Yarı online + yarı yüz yüze" Modelleri

Pek çok uni hibrit programlar açıyor:
- Ders online (canlı + kayıtlı)
- Sınavlar yüz yüze (uni'de veya merkezde)
- Yıl içinde 1-2 hafta yoğun yüz yüze
- Bu modelin **klasik diploma** verir

## Türkiye'den Online Master Yapan Türk Öğrencileri Almanya'da

Sıklıkla soruluyor: "TR'den online master yaptım, Almanya'da kabul mu?"

| Durum | Kabul |
| --- | --- |
| TR online master + yüksek not + iş tecrübesi | ✅ Çoğu işveren kabul |
| TR online master tek başına | ⚠️ Bazı firmalarda dezavantaj |
| AB-dışı online master + Almanya'da PhD başvurusu | ❌ Çoğu zor |

## Pratik Tavsiye

1. **Akademik kariyer (PhD) düşünüyorsan** → yüz yüze klasik master tercih et
2. **İş piyasası için** → online + iş tecrübesi karması iyi
3. **Hibrit programlar** orta yol — klasik diploma + esneklik

## Diploma Tanınması

Almanya'da diploma tanınması için **Anabin** veritabanı kontrol:
- https://anabin.kmk.org
- Üniversiten "H+" işaretliyse master diploması direkt geçerli
- "H-" veya listede yoksa → ek değerlendirme gerek

Bağlantılı: [Diploma denkliği](/sss/denklik)
MD,

            'master-tezi-surecinde-universite-degistirebilir-miyim' => <<<'MD'
**Teknik olarak mümkün ama pratik olarak zor** — master tezi sürecinde uni değiştirmek nadir bir adım.

## Olası Senaryolar

### Senaryo 1: Aynı Programdan Aynı Programa Geçiş
- Master 1. veya 2. dönem
- Hedef uni'de eşdeğer programa kabul
- Mevcut dersler tanınır (Anrechnung)
- ⚠️ Genelde 1 dönem kayıp olabilir

### Senaryo 2: Tez Aşamasında Geçiş
- 3-4. dönem, tez başlamış
- **Çok zor** — yeni uni'nin senin tezini "tanıması" gerek
- Pratikte: yeni hocayla anlaşıp tezi yeniden yapılandırma
- 6-12 ay ek süre

### Senaryo 3: Çift Diplomalı Program (Joint Degree)
- Erasmus+ veya Joint Master programı
- Tez 2 uni'de paralel yürür
- **Resmi düzenleme** — uni değiştirme değil, çift uni

## Hangi Durumlar Uni Değişimi Gerektirir?

✅ Mevcut hoca emekli/ayrıldı
✅ Şehir değişikliği (aile/iş)
✅ Daha iyi program fırsatı (LMU → MIT gibi)
✅ Şartlı kabul → C1 yetersiz, yeni uni B2 kabul ediyor

❌ Sadece "şehir hoşuma gitmedi" → genelde kabul edilmez
❌ Mali sebepler → BAföG/scholarship değişimi daha kolay

## Süreç

1. **Hedef uni Studienberatung** ile görüş — kabul ihtimali var mı?
2. **Mevcut uni dekanlığına** dilekçe — uni'den ayrılış belgesi (Exmatrikulationsbescheinigung)
3. **Anrechnung başvurusu** — geçmiş derslerini yeni uni'ye tanıt
4. **Yeni uni'ye başvur** — Uni-Assist + dönem ortası başvuru
5. **Vize durumu** — Ausländerbehörde'ye değişiklik bildir

## Vize Etkisi

⚠️ Vize/oturum izni uni değişiminde **iptal olmaz** ama:
- 3 ay içinde Ausländerbehörde'ye bildir
- Yeni Immatrikulation belgesi → kart üzerinde belirtilir
- Eski uni kayıt iptal → yeni uni kayıt onayı şart

## Pratik Öneri

> **Uni seçiminde sabırlı ol.** Master başlamadan önce 2-3 uniye paralel başvur, kabul gelenleri kıyasla. Değişiklik master ortasında çok zor, başta düzeltmek kolay.

Bağlantılı: [Master kabul belgesi vize için yeterli mi?](/sss/master/master-kabul-belgesi-vize-basvurusu-icin-yeterli-mi)
MD,

            'master-kabul-belgesi-vize-basvurusu-icin-yeterli-mi' => <<<'MD'
**Master kabul belgesi vize başvurusu için temel belge** — ama tek başına yetmez, başka belgeler de gerekiyor.

## Kabul Belgesi Türleri

| Tip | Açıklama | Vize için yeter mi? |
| --- | --- | --- |
| **Kesin kabul** (Zulassung) | Tüm şartlar tamam | ✅ Direkt |
| **Şartlı kabul** (Vorbehalt) | Eksik dil/sınav var | ✅ Kabul edilir |
| **Conditional Offer** | İngilizce program | ✅ Kabul edilir |
| **Provisional Acceptance** | Belge bekleyen | ⚠️ Bazı konsolosluk reddeder |
| **Studienkolleg kabulü** | Hazırlık | ✅ Akademik vize |

## Master Kabul + Vize Paketi

Kabul belgesi yanında:

### Akademik Belgeler
- Lisans diploması (apostilli + yeminli tercüme)
- Lisans transcript (apostilli + yeminli tercüme)
- Dil sertifikası (TestDaF/DSH veya IELTS)
- Motivasyon mektubu
- CV

### Finansal
- **Sperrkonto onayı** — 2026: 11,904 €
- Sağlık sigortası
- (Burs/sponsor varsa) belge

### Kişisel
- Pasaport (geçerlilik vize bitiminden 3+ ay)
- Biyometrik fotoğraf
- Doldurulmuş başvuru formu (VIDEX)
- Beyan formu

## Kabul Belgesi Üzerinde Olması Gerekenler

✅ Üniversite başlığı + logo
✅ Senin tam adın + doğum tarihi
✅ Kabul edilen program tam adı
✅ Başlama tarihi (Wintersemester veya Sommersemester)
✅ Süre (genelde 4 dönem)
✅ Üniversite yetkilisi imzası

## Belge Eksik veya Geçici ise

Eğer "Provisional Acceptance" aldıysan ve resmi Zulassung'u bekliyorsan:
1. Üniversiteden **dil sertifikası tamamladığını gönder**
2. Resmi Zulassung'u bekle (genelde 2-4 hafta)
3. **Resmi belgeyle başvur** — geçici belgeyle riske girme

## Pratik İpucu

> Master kabul belgesi geldikten **3-4 ay öncesinden** vize randevusu al. Konsolosluk randevu süresi yoğun dönemde 4-6 ay olabilir.

Bağlantılı: [Vize başvurusunda istenen evraklar](/sss/vize/vize-basvurusunda-istenen-evraklar-nelerdir)
MD,

            'dis-hekimligi-masterphd-programlari-nasil' => <<<'MD'
Almanya'da diş hekimliği (Zahnmedizin) **klasik master programı değil** — Türkiye'deki diş hekimliği 5 yıllık entegre programa benzer şekilde. Master/PhD seçenekleri sınırlı.

## Diş Hekimliği Almanya Yapısı

Almanya'da diş hekimliği:
- **10 yarıyıl** (5 yıl) eğitim
- Devlet sınavı (**Staatsexamen**) ile bitiyor — diploma değil
- Sonra **Approbation** (resmi diş hekimi olma izni)
- Tüm Almanya'da geçerli

Bu yüzden "diş hekimliği master" arayışı kafa karıştırıcı — DE'de master geçişi yok.

## Türk Diş Hekimi için Yollar

### 1. Approbation Süreci (Diploma Tanıma)
- Türkiye'de mezunsan → Anabin kontrolü
- **Gleichwertigkeitsprüfung (denklik sınavı)** veya gutachten
- 12-18 ay süreç
- Sonunda Almanya'da diş hekimi olarak çalışabilirsin

### 2. PhD (Dr. med. dent.)
- Approbation aldıktan sonra
- 2-3 yıllık doktora tezi
- Klinik veya araştırma
- Hocayla bireysel anlaşma

### 3. Spezialisierung (Uzmanlık)
- Approbation sonrası
- Endodonti, periodontoloji, ortodonti, oral cerrahi, çocuk diş hekimliği
- 3-5 yıl
- KZV (Kassenzahnärztliche Vereinigung) tarafından düzenlenir

### 4. MSc Programları (Akademik Master)
Klinik diş hekimliğinden farklı, **araştırma master** olarak:
- **MSc Dental Sciences** (Berlin Charité, Münster)
- **MSc Implantology** (Frankfurt, Steinbeis)
- **MSc Orthodontics** (Charité, Düsseldorf)
- Çoğu 1-2 yıl, çoğunlukla yüksek ücretli (~15-30k €)
- Klinik uygulama içerebilir veya araştırma odaklı

## Türk Diş Hekimi için Pratik Plan

> 🦷 **Önce Approbation, sonra PhD/uzmanlık.**

1. TR diş hekimliği diploması + Almanca B2/C1
2. Eyalet sağlık bakanlığına Approbation başvurusu
3. Onaylanırsa **resmi izin** + iş başvurusu
4. Reddedilirse → Gleichwertigkeitsprüfung sınavına gir
5. Approbation sonrası → PhD (Dr. med. dent.) veya uzmanlık

⚠️ **Bu süreç bizim "öğrenci odaklı" rehberin dışına çıkıyor.** Detay için sağlık profesyonel göçü kaynaklarına yön ver.

## Diş Hekimliği Lisans Başvurusu

Eğer **lisans seviyesinde** TR'de okumamış öğrenciysen:
- Almanya'da diş hekimliği **NC ~1.0-1.2** (en yüksek NC)
- AB-dışı kontenjan çok kısıtlı (uniye ~%5)
- TestAS + APS + dil sertifikası şart
- DoSV merkezi sistem
- Çok rekabetçi — alternatif ülkelere (Litvanya, Macaristan) düşmek yaygın

## Kaynaklar

- **Bundeszahnärztekammer (BZÄK)**: https://www.bzaek.de
- **Anabin** diploma tanıma
- **Charité Berlin Diş Fakültesi**: araştırma programları için
MD,

            'sosyal-bilimler-master-programlari-icin-almanca-sarti-nedir' => <<<'MD'
Sosyal bilimler master programlarında Almanca şartı **çoğunlukla C1** — mühendislikten daha yüksek hassasiyet.

## Sosyal Bilim Dallarına Göre

| Alan | Dil Şartı |
| --- | --- |
| **Sosyoloji, Politika Bilimi** | C1 zorunlu (DSH-2/TestDaF 4) |
| **Psikoloji** | C1 zorunlu |
| **Hukuk (LLM)** | **C1 veya C2** — yazılı ifade kritik |
| **Tarih, Felsefe** | C1 (bazı uni C2 ister) |
| **Eğitim Bilimleri** | C1 |
| **Almancılık (Germanistik)** | C2 |
| **Antropoloji** | C1 |
| **Edebiyat** | C2 |
| **İlahiyat (Teologie)** | C1 + Latince/Yunanca |

## Neden Sosyal Bilimler Daha Yüksek Dil Ister?

- Yazılı işin önemli (essay, tez, seminar paper)
- Tartışma derslerinde aktif katılım
- Akademik metinlerle yoğun okuma
- Sözlü sınavlar (mündliche Prüfung) yaygın
- Almanca-dilli kaynaklar ağırlıklı

## İngilizce Sosyal Bilim Masterları

Bazı program istisnaları:
- **International Relations** (Hertie School, Bremen, Erfurt)
- **Public Policy** (Hertie School Berlin)
- **Global Studies** (Freiburg, Leipzig, Wroclaw)
- **European Studies** (Bonn, Maastricht)
- **Development Studies** (Marburg, Berlin)
- **Migration Studies** (Osnabrück)

Bu programlarda:
- İngilizce IELTS 6.5+/TOEFL 90+
- Almanca **B1-B2** önerilir (zorunlu değil ama günlük yaşam için)

## Şartlı Kabul Stratejisi

B2 ile başvurabileceğin programlar:
- "Wir akzeptieren auch B2 mit Verpflichtung zur C1-Steigerung" cümlesini ara
- Şartlı kabul al → DE'de C1'e çıkar → tam kayıt
- ~25 program bu esnekliği gösteriyor

## Pratik Öneri

1. **Almanca C1'i hedefle** — sosyal bilim için pratik gereklilik
2. C1 yoksa **İngilizce program** veya **şartlı kabul** dene
3. **DAAD veritabanı** filtre: Almanca/İngilizce ve seviye
4. Başvuru evraklarında **yazma örneği** (Schreibprobe) bekle — bazı uni ister

## Yazma Sınavı Hazırlığı

Sosyal bilim master için yazılı sınav yaygın:
- Essay (5 sayfa) - belirli bir konuda argumentation
- Lit-review (10 sayfa) - bilimsel makalelerden özet
- Hazırlık: TestDaF yazma + akademik almanca kursu

Bağlantılı: [B2 ile master kabulü](/sss/dil/b2-sertifikasi-ile-master-basvurusu-yapabilir-miyim)
MD,

            'master-3-sinifta-mi-4-sinif-sonrasi-mi-basvurmali' => <<<'MD'
**Genel kural: Lisans 4. sınıf güz dönemi (Ekim-Kasım)** master başvurusu için doğru zaman. Wintersemester (Ekim) kabul almak için.

## Zaman Çizgisi

### Lisans 3. Sınıf
- **GPA'na odaklan** — son 2 dönem kritik
- Almanca/İngilizce yatırımı: B1 → B2
- Kısa staj veya proje katıl

### Lisans 4. Sınıf Yaz (Mayıs-Eylül)
- **Dil sertifikasını al** (TestDaF, IELTS)
- **Uni-Assist VPD** çıkar (yıl boyunca)
- Hedef üniversite + programları belirle
- Motivasyon mektubu yaz

### Lisans 4. Sınıf Güz (Eylül-Kasım)
- **WiSe için başvur** (deadline genelde **15 Temmuz** ama özel programlar farklı)
- **SoSe için başvur** (Deadline **15 Ocak**)
- 2-4 uniye paralel başvur

### Lisans Bitirme (Haziran)
- **Mezuniyet öncesi şartlı kabul** alabilirsin
- Transcript "in progress" gönder, mezuniyet sertifikası geldikçe güncelle

## Erken Başvuru Avantajları

✅ **NC durumu** daha az belirsiz
✅ Burs başvuruları (DAAD: Ekim-Kasım)
✅ Yurt başvurusu erken (kontenjan dolmadan)
✅ Vize randevusu yoğun dönemde değil

## Mezuniyet Öncesi Başvuru: Nasıl Mümkün?

Çoğu Alman uni "**transcript on progress + final ders listesi**" kabul ediyor. Mezuniyet henüz olmamışsa:
- Geçici transcript gönder
- Mezuniyet beklenen tarihi belirt
- Mezuniyet belgesi geldikçe e-mail ile gönder

## "Mezun olmadan başvurursam reddedilir miyim?" Endişesi

❌ Hayır — Almanya bunu standart kabul ediyor. TR mezunlarının çoğu **son dönem başvuru yapar** ve sertifikayı sonradan teslim eder.

## Pratik Zaman Çizgisi

| Hedef başlangıç | Başvuru tarihi | Cevap |
| --- | --- | --- |
| **WiSe 2027 (Ekim 2027)** | Mart-Temmuz 2027 | Ağustos-Eylül |
| **SoSe 2027 (Nisan 2027)** | Ekim 2026-Ocak 2027 | Şubat-Mart |

## Geç Başvurursam?

- 4. sınıf sonu/Haziran mezuniyeti sonrası başvuru:
- WiSe 2027 yerine **WiSe 2028 hedefin** ol
- Aradaki 1 yılda: dil ilerlet, staj yap, GPA notunu lokalize et
- "Boşluk" CV'de açıklanmalı → araştırma asistanlığı, dil okulu

## Risk: Bekleme Yılı

> Eğer 4. sınıf bitirdin ve master başvurusunu kaçırdıysan, **bir yıl bekle** ve hazırlık yap. Aceleyle hazırlıksız başvuru genelde reddedilir.

Bağlantılı: [Wintersemester Uni-Assist deadline](/sss/uni-assist/wintersemester-icin-uni-assist-deadline-ne-zaman)
MD,

            'ingilizce-master-programlarinda-almanca-seviyesi-gerekli-mi' => <<<'MD'
**Çoğu İngilizce master programında Almanca zorunlu değil** — ama bazı programlar Almanca seviye **önerir** veya **şartlı** kılar.

## Almanca Şartına Göre Kategoriler

### Kategori 1: Almanca Şartı Yok (En Yaygın)
- STEM (mühendislik, fen, computer science)
- MBA, İşletme
- Bilim odaklı master programları
- Sadece İngilizce IELTS/TOEFL yeterli

Örnek programlar:
- TUM Informatics
- RWTH Computer Science
- Hertie Public Policy
- Heidelberg Bioscience

### Kategori 2: Almanca Önerilir (Tavsiye)
- "We recommend basic German skills (A1-B1) for everyday life"
- Zorunlu değil — kabul etkilenmez
- Pratik faydası var (günlük hayat, network)

### Kategori 3: Şartlı Almanca
- Kabul "B1 Almanca tamamlama koşuluyla"
- Bazı sosyal bilim İngilizce programları
- Örnek: Bremen International Studies

### Kategori 4: Almanca Zorunlu
- **DAAD bursu için** B1+ Almanca
- Bazı uygulamalı bilimler "İngilizce ama Almanca staj"

## Pratik Faydası: Neden Yine de Öğrenmeli?

✅ **Günlük yaşam** — market, doktor, kira sözleşmesi
✅ **Yurt başvuruları** — bazı yurtlar Almanca önerir
✅ **İş başvuruları** — Almanya'da iş için **C1 standart**
✅ **Network** — Alman öğrencilerle arkadaşlık
✅ **Bürokrasi** — Bürgeramt, Ausländerbehörde
✅ **Mezuniyet sonrası kalmak istiyorsan kritik**

## Önerilen Almanca Plan

| Faz | Hedef |
| --- | --- |
| Master başvuru öncesi | A1 (3 ay) |
| Almanya'ya geliş | A2 (DE'de 2-3 ay yoğun) |
| Master 1. dönem | B1 |
| Master 2. dönem | B2 |
| Mezuniyet | C1 (iş için hazır) |

## Almanca Öğrenme Maliyeti

- Türkiye'de A2'ye kadar: ~500-1000 €
- DE'de B1-C1: Uni içi dersler **ücretsiz veya çok ucuz** (semester fee dahil)
- Toplam yatırım: 1500-3000 € (orta tempolu)

## "Hiç Almanca öğrenmeden gidebilir miyim?" Sorusu

Evet ama:
- ❌ Mezuniyet sonrası %80 işsizlik (yabancı dilli iş çok az)
- ❌ Türk-only arkadaş çevresi
- ❌ Bürokrasi zorluğu
- ❌ Maaş %30-40 daha düşük (Almanca konuşan İngilizce konuşana göre)

## Pratik Tavsiye

> İngilizce master kabul belgesi varsa Almanca **zorunlu değil**, ama **B2'yi hedefle**. Geldiğinden 1.5 yıl içinde C1'e çıkmak ulaşılabilir. İş için Türk öğrencileri yatırım yapanlar 2-3 kat hızlı yerleşiyor.

Bağlantılı: [Almanca öğrenmek için en hızlı yol](/sss/dil/almanca-ogrenmek-icin-en-hizli-yol-nedir)
MD,

            'sertifika-beklemeden-master-basvurusu-yapilir-mi' => <<<'MD'
**Evet — çoğu Alman üniversitesi "bekleyen sertifika" ile başvuruya izin veriyor.** Şartlı kabul mekanizması bunun için var.

## Hangi Sertifikalar "Bekleyebilir"

| Sertifika tipi | Bekleyerek başvuru? |
| --- | --- |
| **Lisans diploması** | ✅ Evet — "transcript on progress" |
| **Dil sertifikası (TestDaF, IELTS)** | ✅ Çoğu uni şartlı kabul verir |
| **GMAT/GRE** | ⚠️ Programa göre — bazıları zorunlu |
| **APS belgesi** | ❌ Genelde önceden gerek |

## "Şartlı Kabul" Mekanizması

### 1. Başvuru Yaparsın
- Eksik belgeleri belirt: "TestDaF sınavım 15 Mart'ta", "Mezuniyet Haziran'da beklenen"
- Mevcut belgelerin (lisans transcript son 2 yıl, dil ön sertifika, vb.)
- Motivasyon mektubu + CV

### 2. Şartlı Kabul (Vorbehalt)
Üniversite "Sen X'e kadar eksiklikleri tamamlarsan kabulun geçerli" diyor.

Yaygın şart tipleri:
- "Bachelor mezuniyet belgesini X tarihine kadar gönder"
- "TestDaF TDN 4 sertifikasını X tarihine kadar gönder"
- "Almanca B2 → C1 ilerleme kanıtı"

### 3. Şart Tamamla
- Sertifika veya belge gelir
- Resmi olarak üniversiteye gönderirsin (online portal veya posta)
- **Resmi Zulassung** alırsın

### 4. Vize Başvurusu
- Hem **şartlı** hem **kesin** kabul belgesi konsolosluğa kabul edilir
- Şartlı kabulle vize başvurusu **daha sık denetlenir** ama çıkar

## Hangi Sertifikaları Beklemek Akıllıdır?

✅ **Dil sertifikası** — sınav tarihiyle çakışsa bile başvurulabilir
✅ **Mezuniyet belgesi** — son dönem öğrencisi için standart
✅ **Stage/staj belgesi** — varsa, sonradan ekle

❌ **APS belgesi** — bazı uniler önceden ister (özellikle Çinli/Vietnamlı öğrenci kotaları için var, Türk için genelde gerekmiyor)
❌ **GRE/GMAT** — pahalı master programlarında sıkı şart

## Riskler

⚠️ **Şart tamamlanmazsa kabul iptal** → vize sürecinde değil ama kayıt aşamasında
⚠️ **TestDaF düşük çıkarsa** → kabul iptal olabilir
⚠️ **Mezuniyet ertelenirse** → uni dönem değişikliği isteyebilir (WiSe'den SoSe'ye atla)

## Pratik Plan

1. **Şubat-Mart**: Sınav tarihlerini netleştir (TestDaF/IELTS)
2. **Mart-Mayıs**: Sertifikayı al
3. **Mayıs-Temmuz**: Master başvurusu (Wintersemester deadline)
4. **Temmuz-Eylül**: Şartlı kabul → kesin kabul geçişi
5. **Eylül-Ekim**: Vize başvurusu
6. **Ekim**: Wintersemester başlangıç

## Önemli Tarih

> Wintersemester için **15 Temmuz** standart deadline. Bu tarihe kadar mezuniyet belgesi ve dil sertifikası beklenmiyorsa, "şartlı kabul" mekanizması işe yarar.

Bağlantılı: [Master 3. sınıfta mı başvurmalı?](/sss/master/master-3-sinifta-mi-4-sinif-sonrasi-mi-basvurmali)
MD,

            'mainz-veya-frankfurtta-master-bursu-olanaklari-nelerdir' => <<<'MD'
Mainz ve Frankfurt çevresi (Rhein-Main bölgesi) — birçok burs imkanına ev sahipliği yapar.

## Mainz Bölgesi Bursları

### Johannes Gutenberg-Universität Mainz
- **JGU Stipendium** — uni içi excellence bursu (~1,500 €/dönem)
- **Mainzer Stipendium** — yabancı master öğrencileri için (~800 €/ay, 12 ay)
- **Forschungsstipendium** — araştırma odaklı projelerde

### Mainz Şehri ve Eyalet
- **Rheinland-Pfalz Landesstipendium** — eyaletten master/PhD bursu
- **Stipendium für junge Wissenschaftler** — genç araştırmacı

### Religious-affiliated Burslar
- **Cusanuswerk** (Katolik)
- **Evangelisches Studienwerk Villigst** (Protestan)
- Aktif olmayanlar için de kabul ediliyor

## Frankfurt Bölgesi Bursları

### Goethe-Universität Frankfurt
- **Deutschland-Stipendium** — eyalet + uni + sponsor (300 €/ay, 12 ay)
- **Goethe Goes Global** — yabancı öğrenci bursu (~12,000 € toplam)
- **Stiftung Polytechnische Gesellschaft** — Frankfurt yerel vakıf

### Frankfurt School of Finance
- **FSFM Excellence Scholarship** — %50 ücret indirimi
- **DAAD Master Studies Scholarship** — finance/business

### Frankfurt Bankacılık + Finans
- **Deutsche Bank Foundation** — finance master
- **Commerzbank Stiftung** — uluslararası öğrenci finansı
- **Allianz Scholarship** — Frankfurt çevresi

## Genel Bursları Mainz/Frankfurt için

### DAAD Programları
- **Study Scholarships** — master için aylık 1,200-1,500 €
- **Re-Invitation Programme** — eski Erasmus alumni için
- **EPOS** — gelişen ülke vatandaşları için (sosyal bilim odaklı)

### Konrad Adenauer Stiftung
- Politik aktif öğrenciler için
- 1,000-1,400 €/ay + dönem ücreti

### Heinrich Böll Stiftung
- Yeşil/sol-ekolojist eğilim
- ~1,200 €/ay

### Friedrich Ebert Stiftung
- Sosyal demokrat eğilim
- ~1,200 €/ay

## Türk Vakıfları + Almanya

Türkiye'den de bazı vakıflar Almanya master için fonluyor:
- **TEV (Türk Eğitim Vakfı)** — DE master için fonlama
- **Vehbi Koç Vakfı** — fellowship programları
- **MEB Yurt Dışı Eğitim Bursu** — devlet yatırımcılığı

## Başvuru Stratejisi

### Mainz/Frankfurt Avantajları
- Bankacılık/finans merkezi → endüstri-uni ortaklı burslar
- Goethe ve JGU iki köklü uni
- Şehir merkezi yaşam imkanı
- Hub konumu (Frankfurt havalimanı, hızlı tren)

### Tipik Başvuru Takvimi
- **Erken** burs deadline: Mart-Mayıs (DAAD)
- **Geç** deadline: Eylül-Ekim (uni içi)
- **Master kabul + burs paralel** başvur

## Aylık Maliyet (Mainz/Frankfurt)

| Kalem | €/ay |
| --- | --- |
| Kira (WG/yurt) | 400-650 |
| Yemek | 200-300 |
| Sigorta | 130 |
| Toplam | 730-1080 |

Burs yeterli mi? Genelde **kısmen** yeterli, paralel Werkstudent gerekli olabilir.

Bağlantılı: [DAAD bursu başvurusu](/sss/burs)
MD,
        ];
    }

    private function uniAssistAnswers(): array
    {
        return [
            'uni-assist-vpd-nedir-ne-ise-yarar' => <<<'MD'
**VPD = Vorprüfungsdokumentation** — Uni-Assist'in senin yabancı diploma ve transkriptini Alman not sistemine çevirdiği resmi belgedir.

## VPD Ne İşe Yarar?

VPD, Türk üniversite diplomanın Almanya'da nasıl değerlendirileceğini gösteriyor:

- **HZB notu** (Hochschulzugangsberechtigung) — Alman not sistemine çevirilmiş GPA
- **Diploma denkliği** — TR lisansı = DE Bachelor karşılaştırması
- **Kredi sistemi karşılığı** — TR kredilerinin ECTS olarak değeri
- **Uygunluk değerlendirmesi** — başvurduğun program için yeterli mi

## Neden Önemli?

Çoğu Alman üniversitesi yabancı öğrenci başvurularını **doğrudan değil, Uni-Assist üzerinden** alır. VPD olmadan başvuru yapamazsın.

## Süreç

1. Uni-Assist hesabı aç (https://www.uni-assist.de)
2. Üniversite seç (en az 1 tane)
3. Online başvuru formu doldur
4. Evrakları yükle (lisans diploma + transcript, lise diploması, pasaport)
5. Ücret öde (75 € ilk uni + 30 € her ek uni)
6. Evrakları posta ile gönder (orijinal/onaylı kopya)
7. **4-8 hafta** içinde VPD hazır → online olarak indirilebilir

## VPD Görünümü

VPD aşağıdaki bilgileri içerir:
- Senin adın + doğum tarihi
- TR diploma türü ve kurumu
- TR notunun Alman karşılığı (örnek: 3.20 TR → 1.7 DE)
- "Uygundur" veya "uygun değildir" kararı
- Ek not: kontenjan dolar dolmaz değerlendirir

## VPD vs APS

⚠️ **APS** ile karıştırma:
- **APS** (Akademische Prüfstelle): Çin, Vietnam, Hindistan vatandaşları için zorunlu
- **VPD** (Uni-Assist): Türk dahil çoğu uluslararası öğrenci için

Türk öğrenciler **sadece VPD** ister, APS'ye gerek yok.

## Pratik Tavsiye

> VPD aldıktan sonra **2-3 üniversiteye paralel başvur** — ek başvuru sadece 30 € ve değerlendirme süresini hızlandırır.

Bağlantılı: [VPD ücreti](/sss/uni-assist/vpd-basvuru-ucreti-ve-ek-basvuru-ucreti-ne-kadar) · [VPD geçerlilik süresi](/sss/uni-assist/vpdnin-gecerlilik-suresi-var-mi)
MD,

            'vpd-basvuru-ucreti-ve-ek-basvuru-ucreti-ne-kadar' => <<<'MD'
Uni-Assist ücretleri **ilk uni 75 €**, **ek her uni 30 €**. Vize ücretiyle birlikte master başvuru sürecinin önemli bir parçası.

## Detaylı Ücret Yapısı (2026)

| Hizmet | Ücret |
| --- | --- |
| **1. üniversite + VPD** | 75 € |
| **Her ek üniversite (aynı dönem)** | 30 € |
| **Sonraki dönem başvuru** | 30 € (varolan VPD'yle) |
| **Belge dönüş postası (TR)** | ~15-25 € (DHL/UPS) |

## Örnek Hesaplama

5 üniversiteye başvurman:
- 1. uni: 75 €
- 4 ek uni: 4 × 30 € = 120 €
- Posta ücreti: ~25 €
- **Toplam: ~220 €**

Bu master başvuru bütçesi içinde kabul edilebilir.

## Ödeme Yöntemleri

✅ **Kredi kartı** (Visa, Mastercard) — en hızlı
✅ **Banka havalesi** (SEPA) — Türkiye'den uluslararası SWIFT
✅ **Sofortüberweisung** (Almanya'dan)
✅ **PayPal** — bazı durumlarda
❌ Western Union, Wise, Revolut → Uni-Assist genelde kabul etmiyor

## Ödeme Sonrası

- Ödeme onayı **otomatik 1-2 gün** içinde gelir
- Onay sonrası **evrak değerlendirmesi başlar**
- Ödeme yapmadan evrak postalama → işleme alınmaz

## Geri Ödeme

⚠️ **Ücret iade edilmez** — başvuru reddedilse bile.

İstisnalar:
- Çift ödeme (banka hatası) → iade
- Sistem hatası → kanıtlamak gerekiyor

## Birden Fazla Dönem Başvurusu

Aynı VPD ile farklı dönemlerde uniye başvuru:
- VPD valid (genellikle 2-3 yıl) → 30 €/yeni başvuru
- VPD süresi geçmiş → yeniden tam ücret

## "Express" İşlem Var mı?

Uni-Assist resmen express işlem teklif etmiyor — herkes aynı kuyrukta. Ama:
- Erken başvuru (Mart-Nisan) → daha hızlı
- Eksiksiz evrak → re-submission gerekmez

## Pratik İpucu

**Strateji:** İlk başvuruyu **en güvenli uniden** (kabul ihtimali yüksek) yap. Sonra ek 30 €'larla "stretch" hedeflerine başvur.

Bağlantılı: [VPD nedir?](/sss/uni-assist/uni-assist-vpd-nedir-ne-ise-yarar)
MD,

            'uni-assist-hzb-notu-nasil-hesaplanir' => <<<'MD'
HZB (Hochschulzugangsberechtigung) notu, **TR diplomandaki not ortalamasının Alman not sistemine çevirilmiş hali**. Almanya'da 1.0 en iyi, 4.0 en kötü.

## Modifiye Bayerische Formül

Uni-Assist çoğu yabancı diploma için **modifiye Bayerische** formülünü kullanır:

```
N = 1 + 3 × (Nmax - Nd) / (Nmax - Nmin)
```

Burada:
- **N** = Alman notu
- **Nmax** = TR sistemindeki maksimum not (Türkiye'de 4.0)
- **Nmin** = TR sistemindeki minimum geçer not (Türkiye'de 2.0)
- **Nd** = senin TR notun

## Örnek Hesaplamalar

### Örnek 1: TR 4.0'lık sistem
| TR notu | Alman notu (yaklaşık) |
| --- | --- |
| 4.00 | 1.0 (en iyi) |
| 3.50 | 1.75 |
| 3.20 | 2.20 |
| 3.00 | 2.50 |
| 2.50 | 3.25 |
| 2.00 | 4.0 (asgari) |

### Örnek 2: TR 100'lük sistem
| TR notu | Alman notu |
| --- | --- |
| 90+ | 1.0-1.5 |
| 80-89 | 1.5-2.0 |
| 70-79 | 2.0-3.0 |
| 60-69 | 3.0-3.5 |
| 50-59 | 3.5-4.0 |

## Önemli Kurallar

✅ **Üniversite minimum geçer notu** önemli — bazı TR uniler 1.5, bazıları 2.0 geçer not
✅ Diploma üstündeki **resmi not sistemi** kullanılır (transcript)
✅ Bazı uniler **özel hesaplama** yapar — Uni-Assist'in standart hesabını kabul etmeyebilir
✅ **Birinci sınıf dersleri** dahildir, ortalama tüm 4 yıl

## Alman Not Sistemi

| Not | Anlamı | Sıkça kullanılan tanım |
| --- | --- | --- |
| 1.0-1.5 | Sehr gut (Çok iyi) | Üst seviye |
| 1.6-2.5 | Gut (İyi) | Standart "iyi" |
| 2.6-3.5 | Befriedigend (Yeterli) | Geçer |
| 3.6-4.0 | Ausreichend (Yetersizdir ama geçer) | Asgari |
| 4.1+ | Nicht ausreichend (Geçmez) | Kayıp |

## NC ile İlişki

Senin HZB notun, NC (Numerus Clausus) olan programlarda kontenjan kararını belirler:
- **TUM, RWTH yüksek programlar** → 1.5'in altında not (TR ~3.5+)
- **Mid-tier uniler** → 2.0-2.5 (TR ~3.0+)
- **NC frei programlar** → not ne olursa kabul

## Pratik Tavsiye

VPD aldığında HZB notun yazılır. Düşükse:
1. **Daha az talep gören üniler** veya **NC frei programlar** ara
2. **Şartlı kabul + GMAT/GRE** alternatifi (master için)
3. **Mesleki deneyim** vurgu motivasyon mektubunda

Bağlantılı: [Düşük GPA ile master kabulü](/sss/master/dusuk-gpa-ile-almanyada-masterlisans-kabulu-alinir-mi) · [NC nedir?](/sss/master/nc-nedir-master-basvurusunu-nasil-etkiler)
MD,

            'uni-assist-uzerinden-basvuru-reddedilirse-ne-yapmaliyim' => <<<'MD'
Uni-Assist başvurusunun reddedilmesi (rejected/abgelehnt) **kalıcı değil** — sebebine göre düzeltebilirsin.

## Red Tipleri ve Çözümleri

### 1. Eksik Evrak (Most Common)
**Mesaj:** "Ihre Bewerbung ist unvollständig"

**Çözüm:**
- Eksik belgeyi tamamla
- 6 hafta içinde gönder
- Yeni ücret yok — varolan başvuru sürecini devam ettirir

### 2. Yanlış Evrak Formatı
**Mesaj:** "Beglaubigung fehlt" / "Übersetzung nicht akzeptiert"

**Çözüm:**
- Onaylı kopya (Beglaubigung) eksikse — notere git
- Yeminli tercüme yanlışsa — Almanya'da onaylı tercüman bul
- Resubmission

### 3. Diploma Tanınmıyor
**Mesaj:** "Vorbildung nicht ausreichend"

**Çözüm:**
- Senin TR uni'n Anabin'de "H+" mı? Kontrol et
- Anabin "H-" ise → o uni Almanya'da tanınmıyor
- Alternatif: TR'de tanınan uni'ye geçiş veya başka ülke düşünme

### 4. Not Yetersiz
**Mesaj:** "GPA unter Mindestnote"

**Çözüm:**
- Notu değiştiremezsin → daha az talep gören uni/program
- Motivasyon mektubu + iş tecrübesi yıkın
- Şartlı kabul olabilir mi sor

### 5. Program Uygun Değil
**Mesaj:** "Studiengang nicht passend"

**Çözüm:**
- Bachelor müfredatın master ile uyumsuz
- Auflagen (ek dersler) önerilmiş mi?
- Bridge program veya farklı program seç

## "Resubmission" Süreci

Çoğu red **6 hafta içinde düzeltme** ile çözülebilir:

1. **Uni-Assist mailını dikkatli oku** — "missing documents" listesi
2. **Eksiği tamamla** (orijinal/onaylı kopya, yeminli tercüme)
3. **Online portal'dan yükle** + posta ile orijinal gönder
4. **2-4 hafta** içinde yeniden değerlendirme

## Yeni Başvuru Gerekli mi?

| Durum | Yeni başvuru? |
| --- | --- |
| Sadece evrak eksik | ❌ Mevcut başvuru devam |
| Yanlış uni seçimi | ✅ Yeni 30 € ek başvuru |
| Tamamen iptal istiyorum | ✅ Yeni VPD süreç |

## "Berufung" (İtiraz) Hakkı

Uni-Assist kararına itiraz ediyorsan:
- **30 gün içinde** yazılı itiraz (Almanca/İngilizce)
- "Begründung der Ablehnung" iste — sebebini ayrıntılı talep et
- **Üniversitenin Bewerbungsstelle'ı** ile doğrudan iletişim al

## Pratik İpucu

> Red mailını **30 dakika içinde paniğe kapılmadan** oku. Çoğu red basit evrak eksikliği — çözülebilir. Sebep belirsizse Uni-Assist'e mail at "Welche Dokumente fehlen genau?" diye.

Bağlantılı: [Red sebepleri](/sss/uni-assist/uni-assist-red-sebepleri-en-cok-hangileridir)
MD,

            'aps-ile-uni-assist-arasindaki-iliski-ve-siralama-nedir' => <<<'MD'
**Türk öğrenci olarak APS'ye ihtiyacın YOK.** APS (Akademische Prüfstelle), sadece **Çin, Vietnam, Hindistan, Mongolistan** gibi belirli ülke vatandaşları için zorunlu.

## APS vs Uni-Assist Farkı

| Açı | APS | Uni-Assist (VPD) |
| --- | --- | --- |
| **Kimler için?** | Çin, Vietnam, Hindistan vatandaşları | Çoğu uluslararası öğrenci (TR dahil) |
| **Amaç** | Diploma doğrulaması + giriş sınavı | Diploma → Alman notu çevirisi |
| **Ücret** | ~250-500 € | 75 € + 30 €/ek uni |
| **Süre** | 3-6 ay | 4-8 hafta |
| **Mülakat** | Var (bazı versiyonlarda) | Yok |
| **Türk öğrenci için?** | ❌ Gerekmiyor | ✅ Zorunlu |

## Türk Öğrenci Süreci

Sadece Uni-Assist:
1. Uni-Assist hesap aç
2. Üniversite seç
3. Evrak gönder
4. VPD al (~4-8 hafta)
5. Uni başvurusu yap (VPD ile)

APS adımı **yok**. Eğer kafan karıştıysa veya birisi "APS gerekli" dediyse — bu yanlış bilgi.

## Sıralama (Doğru Süreç)

```
Lise/lisans diploması (apostilli + tercüme)
        ↓
Uni-Assist VPD başvurusu
        ↓
VPD belgesi
        ↓
Üniversite başvurusu (Uni-Assist veya doğrudan)
        ↓
Kabul belgesi
        ↓
Vize başvurusu
        ↓
Almanya'ya yerleşim
```

## "APS Türkçe versiyon var mı?" Sorusu

❌ Hayır — APS belirli ülkeler için tasarlandı (Çin Şanghay, Hindistan Yeni Delhi, vb.). Türkiye'de Alman konsolosluğu zaten doğrudan Uni-Assist süreciyle çalışır.

## İstisnai Durum: Çin'de Lisans Yapmış Türk

Eğer **Türk vatandaşıysın ama Çin/Vietnam'da lisans yaptıysan** → o ülke için APS gerekebilir. Bu çok dar bir durum.

## Pratik İpucu

> Birisi "APS al" derse Türk öğrenciye, danışmanlık firmasının yanlış yönlendirmesi olabilir. Doğrudan Uni-Assist web sitesinden ücretsiz danışmanlık al.

Bağlantılı: [Uni-Assist VPD nedir?](/sss/uni-assist/uni-assist-vpd-nedir-ne-ise-yarar)
MD,

            'dosv-programlar-icin-uni-assist-uzerinden-basvuru-yapilir-mi' => <<<'MD'
**DoSV programları için Uni-Assist + Hochschulstart.de paralel başvuru gerekir.** İki sistem birlikte çalışır.

## DoSV Nedir?

**DoSV = Dialogorientiertes Serviceverfahren** — Almanya'nın merkezi öğrenci kabul sistemi.

NC olan programlar için (özellikle popüler bölümler) kullanılır:
- Tıp, Diş, Eczacılık (Bundesweit zulassungsbeschränkt)
- Bazı uniler kendi programlarını da DoSV'ye dahil eder

Yönetici platform: **Hochschulstart.de** (HoS)

## Türk Öğrenci için DoSV

Eğer DoSV programına başvuruyorsan:

1. **Uni-Assist'e başvur** → VPD al
2. **Hochschulstart.de'da kayıt aç**
3. **DoSV bid (öncelik sırası)** gir — hangi uni öncelikli
4. Sistem otomatik eşleştirme yapar (Almanca: "Vergabeverfahren")

## İki Sistem Akışı

```
Uni-Assist (Diploma çevirisi/VPD)
        ↓
Hochschulstart.de (Merkezi yerleştirme)
        ↓
Eşleşme + kabul
```

Pratikte:
- VPD önce hazır olmalı (~4-8 hafta)
- VPD'yle Hochschulstart.de'ye başvur
- Hochschulstart.de bu bilgiyi alıp uniler arasında dağıtım yapar

## Türk Vatandaşı NC Programları için Kontenjan

Tıp gibi programlarda **AB-dışı öğrenci kontenjanı %5-10** civarı. Türk vatandaşı olarak:
- Çok rekabetçi (yıllık ~500 başvuru, ~30 kabul)
- TestAS skoru kritik
- Almanca C1+ standart

## DoSV İçin Önemli Tarihler

| Aşama | Wintersemester | Sommersemester |
| --- | --- | --- |
| Uni-Assist başvuru | Mart-Mayıs | Eylül-Kasım |
| Hochschulstart.de açılış | Mart | Kasım |
| Hochschulstart.de deadline | **15 Temmuz** | **15 Ocak** |
| Eşleşme + kabul | Eylül | Mart |
| Yedek koltuk dağıtımı | Eylül-Ekim | Mart-Nisan |

## Pratik Tavsiye

> DoSV programları **rekabetçi ve kontenjan dar** — Türk öğrenci olarak tıp gibi alanlara hem DoSV hem de **alternatif programlara** paralel başvuru yap. Sadece tıba odaklanma riskli.

## NC olmayan programlar?

NC frei programlar **DoSV değil** — direkt Uni-Assist üzerinden başvurursun.

Bağlantılı: [NC nedir?](/sss/master/nc-nedir-master-basvurusunu-nasil-etkiler)
MD,

            'vpd-aldiktan-sonra-universitelere-dogrudan-mi-basvurmaliyim' => <<<'MD'
**Üniversiteye göre değişir** — bazı uniler tüm başvuruları Uni-Assist üzerinden alır, bazıları kendi portallarını kullanır.

## İki Yol

### 1. Uni-Assist Üzerinden Tam Başvuru
Çoğu üniversite bu sistemi kullanır. Bunda:
- Uni-Assist hem VPD üretir hem **başvuruyu uniye iletir**
- Tek başvuruyla 2-3 uniye paralel başvurabilirsin
- 75 € + 30 €/ek uni

### 2. VPD + Direkt Uni Başvurusu
Bazı uniler kendi sistemine başvuru istiyor:
- Önce Uni-Assist'ten VPD al
- Sonra **uni'nin kendi portalında** (campus, Hochschulportal) başvuru yap
- VPD'yi belge olarak yükle
- Uni'ye özel ücret olabilir (~50-100 €)

## Hangi Sistem Hangi Uni İçin?

### Sadece Uni-Assist (%70 uni)
- TU Berlin, RWTH, KIT, Heidelberg, çoğu BW eyaleti uni

### Uni-Assist + Kendi Portalı (%20 uni)
- TUM (Technische Universität München)
- LMU
- Charité (özel program)
- Frankfurt Goethe-Universität (bazı master)

### Sadece Direkt (Uni-Assist gerekmez)
- Bazı özel uniler (Hertie School, ESMT, Frankfurt School)
- Bazı uygulamalı bilimler (FH)

## Nasıl Anlarsın?

Hedef uninin **Bewerbung** veya **Application** sayfasına git. Aramalar:

✅ "Bewerbung über Uni-Assist" → Sadece Uni-Assist
✅ "Direktbewerbung + VPD" → Uni-Assist + kendi portalı
✅ "Online application directly" → Direkt başvuru

## Süreç Karşılaştırması

| Adım | Uni-Assist tek | Uni-Assist + Direkt |
| --- | --- | --- |
| 1 | Uni-Assist hesap aç | Uni-Assist hesap aç |
| 2 | Evrak yükle | Evrak yükle |
| 3 | Ödeme | Ödeme |
| 4 | VPD'yi bekle | VPD'yi bekle |
| 5 | Uni-Assist üzerinden başvuru tamamlanır | VPD ile uni portalına git |
| 6 | Uni karar verir | Uni'ye direkt başvur |
| 7 | Kabul/red | Uni karar verir |

## Avantajları

**Sadece Uni-Assist:**
- Tek sistem, basit
- Paralel uni başvurusu kolay
- Tek ücret yapısı

**Uni-Assist + Direkt:**
- Üniversite ek belgeler isteyebilir (motivasyon, portfolio)
- Kabul kararı daha hızlı bazen
- Üniversite ile direkt iletişim

## Pratik İpucu

> Her hedef uninin **Bewerbungsablauf** (başvuru süreci) sayfasını dikkatli oku — yanlış sistem kullanırsan başvurun değerlendirmeye alınmaz.

Bağlantılı: [Uni-Assist + paralel başvuru](/sss/uni-assist/uni-assist-ile-universite-paralel-basvurusu-yapilabilir-mi)
MD,

            'apostilli-evrak-uni-assist-basvurusu-icin-gerekli-mi' => <<<'MD'
**Evet — apostil zorunlu.** Türkiye'de düzenlenmiş resmi evrakların Almanya'da geçerli olması için Apostil (Hague Apostille) gerekli.

## Hangi Evraklar Apostilli Olmalı?

### Zorunlu Apostilli
- 🎓 **Lise diploması** (orijinal)
- 🎓 **Lisans diploması** (orijinal)
- 📋 **Lise transcript** (mezuniyet öncesi senin notların)
- 📋 **Lisans transcript** (tüm dönem notları)
- 🎓 **Studienkolleg diploması** (varsa)

### Apostil Gerekmeyen (Sadece Tercüme)
- 📷 Pasaport kopyası
- 🆔 Kimlik
- 📝 Motivasyon mektubu, CV

## Apostil Nereden Alınır?

**İlgili evrakın türüne göre:**

| Evrak | Apostil yeri |
| --- | --- |
| Lise diplomast, transcript | **İl Milli Eğitim Müdürlüğü** + **Valilik İl Yazı İşleri Müdürlüğü** |
| Lisans diploma, transcript | **Üniversite** onayı + **Valilik İl Yazı İşleri** |
| Adli sicil belgesi | **Adliye** + **Valilik** |
| Doğum belgesi | **Nüfus Müdürlüğü** + **Valilik** |

## Süreç

1. Belgeyi al (orijinal veya onaylı kopya — uniye göre farklı)
2. İlgili kurumdan resmi mührünü al
3. Valilik İl Yazı İşleri Müdürlüğü'ne git
4. **Apostil mührü** yapışır (1-3 gün)

## Süre + Maliyet

| Tip | Süre | Ücret |
| --- | --- | --- |
| Valilik apostil | 1-3 gün | ~50-100 ₺ |
| **Online randevu sistemi** | Aktif (e-Devlet) | Aynı |
| Acele/exprime servis | Bazı şehirlerde aynı gün | ~200-300 ₺ |

## Apostil Sonrası Tercüme

Apostil mührü Türkçe — Almanya'da kullanmak için:
1. **Yeminli tercüme** (Almanca veya İngilizce)
2. **Apostil mührünün kendisi de tercüme edilir**
3. Tercümeden sonra **noter onayı** istenebilir (uniye göre)

## Türkiye Apostil Mührü vs Diğer Onaylar

✅ **Apostil** — Hague Sözleşmesi (Almanya da imzacı) — yeterli
❌ Konsolosluk onayı — apostil yerine geçmez
❌ Noter tasdiki — apostil yerine geçmez
✅ **Sadece apostil yeterlidir** Almanya'ya gönderilen evrak için

## Pratik İpucu

> Apostil dosyaları **uzun süreli geçerli** — bir kez al, master başvurusu + vize başvurusu + Almanya'da kayıt aşamasında defalarca kullanırsın. **2-3 kopya** çıkar, vize randevusunda yedek bulundur.

Bağlantılı: [Uni-Assist evrak postası](/sss/uni-assist/uni-assist-evraklarini-postayla-mi-gondermem-gerekiyor)
MD,

            'uni-assist-icin-diploma-cevirisi-yeminli-mi-olmali' => <<<'MD'
**Evet, yeminli tercüme zorunlu.** Uni-Assist ve Alman üniversiteleri **resmi yeminli tercüman** tarafından yapılmış tercüme kabul ediyor.

## "Yeminli Tercüman" Nedir?

Yeminli tercüman = mahkeme veya devlet kurumlarına onaylı tercüman:
- TR'de **Noter** veya **Sulh Hukuk Mahkemesi** tarafından yeminli
- DE'de **Eyalet mahkemesi** (Landesgericht) tarafından yeminli — "öffentlich bestellt und beeidigt"

## Hangi Tercümanlar Kabul Edilir?

### En Güvenli: Almanya'daki Yeminli Tercüman
- Alman mahkemesince yeminli
- "Beglaubigte Übersetzung" damgalı
- **%100 kabul** her uni ve konsolosluk
- Ücret: 1-2 €/satır, ~80-150 € diploma+transcript için

### TR'deki Yeminli Tercüman
- Noterde yemin etmiş tercüman
- **Kabul ama bazı uniler tartışıyor**
- Özellikle **BW, SH, Niedersachsen, Hamburg** — DE tercümanı tercih
- Ücret: 50-150 ₺/sayfa, ~500-1500 ₺ diploma+transcript

### Online Tercüme + Noter Tasdiki
- ❌ Genelde kabul edilmiyor (Google Translate + noter)

## TR Yeminli Tercüme Kullanırken Riskler

⚠️ Bazı uniler **TR tercümesi reddediyor** → resubmission gerekiyor:
- "Vereidigter Übersetzer in Deutschland erforderlich"
- Bu durumda DE'de yeniden tercüme + ek ücret

## En Güvenli Yol

1. **Önce TR yeminli tercüme** yaptır (ucuz, hızlı)
2. Uni-Assist'e bu tercümeyle başvur
3. **Eğer reddedilirse** DE'de yeniden tercüme yaptır
4. Ya da: en başından DE tercümanı tercih (daha pahalı, hızlı sonuç)

## Yeminli Tercüman Bulma

**Türkiye:**
- Noter sayfalarında yeminli tercüman listesi
- Sahibinden, yeminli-tercume.com
- Almanca için **Goethe Institut** önerileri

**Almanya:**
- https://www.justiz-dolmetscher.de (resmi liste)
- "Almanca-Türkçe vereidigter Übersetzer + şehir adı" araması
- Şehirlerde Türkçe konuşan tercüman: Berlin, Münih, Köln, Hamburg

## Tercümenin İçeriği

Tercüme şunları kapsamalı:
- ✅ Diploma + transcript tam metin
- ✅ Apostil mührü tercümesi
- ✅ Yeminli tercüman damgası + imza
- ✅ Tercüme tarihi
- ✅ "İdentisch mit dem Original" beyanı

## Pratik İpucu

> Tercüme + apostil pahalı bir adım — diplomadan sadece 1 kopya tercüme et, sonra orjinal Almanca tercümeyi **fotokopi + noter** ile çoğalt. Ucuz olur.

Bağlantılı: [Apostil gerekli mi?](/sss/uni-assist/apostilli-evrak-uni-assist-basvurusu-icin-gerekli-mi)
MD,

            'vpdnin-gecerlilik-suresi-var-mi' => <<<'MD'
**VPD'nin resmi sona erme tarihi yok ama pratikte 2-3 yıl içinde tekrar başvuru tavsiye edilir.**

## VPD Süresi: Resmi Durum

- Uni-Assist VPD'yi **belirsiz süreli** verir
- Üzerinde "Geçerlilik Tarihi" yazmaz
- **Yeniden hesaplama olmasa** geçerli kalır

## Ama: Pratik Durum

Aşağıdaki durumlarda VPD yenileme gerekiyor:

### 1. Diploma Yeni Çıkar
- VPD aldıktan sonra **bachelor mezuniyet belgeni** aldıysan
- Eski VPD'de "in progress" notu var → güncelle

### 2. Not Sistemi Değişiklik
- TR uni'sinde 4'lük sistem → 100'lük sistem geçtin
- Veya tam tersi
- HZB notun yeniden hesaplanmalı

### 3. Anabin Durumu Değişti
- TR uninin Anabin'de "H+" → "H-" geçti (nadir)
- Uni'nin durumu değiştiyse

### 4. Yeni Bachelor/Master Diploması
- Master tamamladın, PhD başvurusu yapıyorsun → master için yeni VPD

## VPD Yenileme Maliyeti

✅ **Aynı diploma için yenileme**: Genelde ücretsiz veya çok az
✅ **Yeni diploma için yeni VPD**: 30 € (varolan hesap)
✅ **Yeni öğrenci tipi (lisans → master)**: 75 € (tam başvuru)

## Pratik Süre Tavsiyesi

> **Mezuniyet sonrası 2-3 yıl içinde** VPD ile başvuru yap. 5 yıl üstü beklenmiş VPD bazı uniler tarafından **yeniden inceleme** istenebilir.

## Master Başvurusu için VPD Yenileme

Eğer 4. sınıfta VPD aldıysan ama master başvurusu **1 yıl sonra** yapacaksan:
- VPD geçerli — yeniden ücret yok
- Mezuniyet belgesini eklemek için Uni-Assist'e mail yeterli

## "Vize Bekleyen" Süresi

Eğer:
1. VPD aldın → Master başvurusu yaptın → Kabul aldın
2. Vize 1 yıl bekledi
3. Hâlâ aynı master için kabul belgesi geçerli mi?

**Çoğu durumda evet**, ama uni'ye **deferred admission** (ertelenmiş kabul) talebi göndermek gerekiyor. Bazı uniler 1 dönem (6 ay) erteleme verir, bazıları yıllık.

## VPD Tekrarı için Ne Gerekir?

VPD'ni yenilemek isterken:
1. Uni-Assist hesabına gir
2. "Mein Konto" → "Bewerbung verwalten"
3. "Neue Bewerbung" başlat
4. Mevcut belgeleri çek (otomatik)
5. Yeni dönem ve programları seç
6. **30 €** ek başvuru ücreti öde
7. 4-8 hafta sonra yeni VPD

Bağlantılı: [Mein Konto güncellemesi](/sss/uni-assist/uni-assist-mein-konto-guncellemesi-nasil-yapilir)
MD,

            'uni-assist-uzerinden-basvuruda-transcript-cevirisi-zorunlu-mu' => <<<'MD'
**Evet — transcript çevirisi zorunlu.** Üniversite + Uni-Assist diplomayla birlikte **tüm dönem notlarının** çevirisini ister.

## Hangi Transcript Çevrilmeli?

### Tam Lisans Dönemi
- 4 yıllık (genelde 8 dönem) tüm dersler
- Her dersin **adı + kredi + notu**
- Cumulative GPA (toplam ortalama)
- Mezuniyet tarihi (mezunsan)

### Henüz Mezun Değilsen
- O ana kadar tamamlanan tüm dönemler
- "In progress" derslerin listesi (varsa)
- Beklenen mezuniyet tarihi

### Lise Transcript
- 4 yıllık not bilgisi
- AGNO/HZB notu görüntüsü (üst sınıfa geçiş notu)
- Diploma puanı (TYT/AYT puanları varsa)

## Çeviri Şartları

✅ **Yeminli tercüman** tarafından yapılmalı (TR veya DE)
✅ Her sayfa **damga + imza** olmalı
✅ Orijinal transcripti aynı sayıda sayfa
✅ Apostil tercümesi de dahil
✅ Ders adlarının **Almanca veya İngilizce** karşılığı

## Ders Adı Tercümeleri

Bazı dersler için karşılık zor:
- TR: "Türk Dili I" → DE: "Türkische Sprache I"
- TR: "Atatürk İlkeleri" → DE: "Atatürk'sche Prinzipien" (bazen "Politische Bildung")
- TR: "Bilgisayar Programlama" → DE: "Programmierung" / EN: "Computer Programming"

Yeminli tercümanın bu kararları vermesi normal. Ama master programıyla ilgili **teknik dersler** için **kesin tercüme** zorunlu.

## Online vs Posta

| Aşama | Format |
| --- | --- |
| **Uni-Assist online portal'a yükleme** | Renkli scan (PDF) |
| **Posta ile gönderme** | Orijinal/onaylı kopya + tercüme |
| **Üniversite ek istek** | Bazıları sadece scan, bazıları posta |

## Maliyet

Lisans transcript tercüme:
- TR yeminli: 10-30 ₺/sayfa × 8-10 sayfa = **100-300 ₺**
- DE yeminli: 1.50-2.50 €/satır × ~80 satır = **100-200 €**
- + Apostil tercümesi = **+30-50 ₺**

Lise transcript tercüme: **50-150 ₺**

## "Sadece Diploma Yetmez" Sebepleri

- Diploma sadece "lisans verilmiştir" gösterir
- Hangi dersleri aldığını, müfredatın master ile uyumunu **transcript** gösterir
- HZB notu hesaplaması için tüm dönem notları gerekli
- Bachelor uyumluluk değerlendirmesi (Vorbildung) **transcript ağırlıklı**

## Pratik İpucu

> Tercüme yaptırırken **2-3 kopya** alın — Uni-Assist + Üniversite + Vize başvurusu için her birinde ihtiyaç olur. Aynı tercümeden çoğaltmak ucuz.

Bağlantılı: [Yeminli tercüme gerekli mi?](/sss/uni-assist/uni-assist-icin-diploma-cevirisi-yeminli-mi-olmali)
MD,

            'uni-assist-red-sebepleri-en-cok-hangileridir' => <<<'MD'
Uni-Assist başvurusunda **%30-40 red oranı** var — sebepler büyük çoğunlukla evrak eksikliği. Yaygın red sebepleri ve çözümleri.

## En Yaygın 7 Red Sebebi

### 1. Eksik Evrak (En Yaygın)
**Mesaj:** "Bewerbung unvollständig — folgende Dokumente fehlen..."

- Apostilli kopya eksik
- Tercüme eksik veya kabul edilmeyen format
- Tek dönem transcripti eksik

**Çözüm:** 6 hafta içinde gönder

### 2. Yanlış Tercüme Formatı
**Mesaj:** "Beglaubigte Übersetzung fehlt"

- Google Translate veya online tercüme
- TR yeminli kabul edilmedi (bazı uniler)
- Tercüman damgası olmayan tercüme

**Çözüm:** Yeminli tercümana git, yeniden yaptır

### 3. Diploma Anabin'de Tanınmıyor
**Mesaj:** "Vorbildung nicht ausreichend"

- Türk lisansın Anabin'de "H-" görünüyor
- Uni'n özel bir adla "H+/-" karışık

**Çözüm:** Anabin kontrol → alternatif uni veya bridge program

### 4. Not Yetersiz
**Mesaj:** "Notenniveau zu niedrig"

- Programın HZB-Mindestnote'un altında
- Genelde 2.5+ (Alman not) altı reddedilir

**Çözüm:** Daha az talep gören uni, NC frei program

### 5. Müfredat Uyumsuz
**Mesaj:** "Curriculum nicht passend"

- Bachelor master için **temel ders eksik** (mühendislik master için en az 30 kredi temel ders)
- Bachelor "İşletme" master "Bilgisayar Müh." → uyumsuz

**Çözüm:** Bridge program, MOOC sertifikası, farklı program

### 6. Dil Sertifikası Eksik
**Mesaj:** "Sprachnachweis fehlt"

- TestDaF/DSH/IELTS gönderilmemiş
- Geçerlilik süresi geçmiş (2 yıl üstü Goethe hariç)

**Çözüm:** Sertifika al ve gönder, şartlı kabul de mümkün

### 7. Ödeme Eksik
**Mesaj:** "Zahlung nicht eingegangen"

- Bank transferi 5-7 gün sürdü, deadline kaçtı
- Yanlış referans numarası

**Çözüm:** Banka dekontu ile Uni-Assist'e mail at, durumu açıkla

## Red Sebebini Anlamak

Red mailı genelde **kategorize edilmiş listede** geliyor:
- "Anlage 1: Fehlende Dokumente"
- "Anlage 2: Formelle Mängel"
- "Anlage 3: Inhaltliche Mängel"

Her birinin altında somut eksiklikler.

## "Sebep Belirsiz" Red

Bazen sadece "Bewerbung wurde abgelehnt" yazıyor — sebep belirsiz. Bu durumda:
1. **Begründung iste** — "Bitte um detaillierte Begründung"
2. **Uni'nin Studienberatung'ına** mail at
3. **Berufung (itiraz)** seçeneği var

## Red Önleme Checklist

✅ Diploma + transcript + apostil + tercüme
✅ Dil sertifikası (TestDaF, IELTS)
✅ Motivasyon mektubu Almanca veya İngilizce, 1-2 sayfa
✅ Pasaport kopyası
✅ CV
✅ Ödeme dekontu
✅ Anabin kontrolü önceden (TR uni "H+" mı?)

Bağlantılı: [Reddedilirse ne yapmalıyım?](/sss/uni-assist/uni-assist-uzerinden-basvuru-reddedilirse-ne-yapmaliyim)
MD,

            'lisans-bitirmeden-uni-assist-vpd-basvurusu-mumkun-mu' => <<<'MD'
**Evet, lisans son sınıfta okurken Uni-Assist VPD başvurusu yapabilirsin.** Uni-Assist "in progress" (henüz tamamlanmamış) lisans için VPD üretiyor.

## Süreç: Mezun Olmadan Önce

### 1. Lisans 4. Sınıf Başı
- Mevcut tüm dönem notları + transcript
- Beklenen mezuniyet tarihi
- VPD için Uni-Assist hesabı aç

### 2. VPD İçin Evraklar
- Lise diploması + apostil + tercüme
- **Mevcut transcript** (örnek: 6 dönem tamamlanmış)
- "In progress" belgesi — uni'nden alacaksın
- Mezuniyet beklenen tarih beyanı

### 3. Geçici VPD Çıkar
- ~4-8 hafta sonra VPD hazır
- VPD üzerinde notu: "Bachelor degree expected by [DATE]"
- HZB notu mevcut notların ortalamasıyla hesaplanır
- "Vorläufig" (geçici) damgalı olabilir

### 4. Master Başvurusu Yap
- Geçici VPD ile uniye başvur
- Mezuniyet belgesini sonradan gönder
- Çoğu uni "şartlı kabul" verir

### 5. Mezuniyet
- Diploma + final transcript hazır olduğunda Uni-Assist'e gönder
- VPD güncelleme (genelde ücretsiz)
- Final HZB notu hesaplanır

## "Final GPA" Etkisi

⚠️ **Önemli:** Mevcut transcript ile beklenen final GPA arasında **anlamlı fark** olursa:
- Düşüş → şartlı kabul iptal olabilir
- Yükseliş → durumu uniye bildirebilirsin

## Zaman Çizgisi

### Hedef WiSe 2027 (Ekim 2027)
| Tarih | Adım |
| --- | --- |
| Eylül 2026 | 4. sınıf başı + Uni-Assist VPD başvuru |
| Ekim-Kasım 2026 | VPD hazır (geçici) |
| Aralık 2026-Mart 2027 | Master başvuruları, dil sertifikası |
| Mart-Mayıs 2027 | Şartlı kabul + dil sınavı sonuçları |
| Haziran 2027 | Mezuniyet |
| Temmuz-Ağustos 2027 | Final transcript + vize başvurusu |
| Ekim 2027 | Almanya'ya geliş |

## VPD'nin "Geçici" Damgası

Uni'ye gösterirken bu damga sorun:
- ✅ **Çoğu uni** şartlı kabul verir (mezuniyet bekleniyor)
- ⚠️ Bazı uniler (özellikle TUM, RWTH) **final transcript** ister

## Erken VPD Avantajları

✅ Burs deadline'larına yetiş (DAAD Ekim-Kasım)
✅ Yurt başvurusu erken (kontenjan dolmadan)
✅ Vize randevusu yoğun dönemde değil
✅ Daha fazla program araştırması için zaman

## Risk

⚠️ Mezuniyet ertelenirse:
- Geçici VPD'deki "beklenen tarih" geçer
- Kabul belgesi iptal olabilir
- Vize başvurusu reddedilebilir

**Tedbir:** Master başvurusunda **mezuniyet için 1-2 ay buffer** koy. Wintersemester (Ekim) hedefliyorsan mezuniyetin **Haziran** olmalı, Eylül değil.

Bağlantılı: [Master 3. sınıfta mı başvurmalı?](/sss/master/master-3-sinifta-mi-4-sinif-sonrasi-mi-basvurmali)
MD,

            'uni-assist-odeme-hatasi-durumunda-ne-yapilir' => <<<'MD'
Uni-Assist ödeme hataları yaygın — banka transferi, kart reddetme, çift çekme. Çoğu **2-3 gün içinde çözülebilir**.

## Yaygın Ödeme Hataları

### 1. Ödeme Yapıldı Ama Onay Gelmedi
**Sebep:** Banka transferi 5-7 gün sürüyor

**Çözüm:**
- 7-10 gün bekle
- Banka dekontu (Überweisungsbeleg) hazırla
- Uni-Assist'e mail at: `zahlung@uni-assist.de`
- "Anbei der Überweisungsbeleg" konusuyla dekont gönder

### 2. Kredi Kartı Reddetti
**Sebep:** Yurt dışı blok, sınır, Türk bankasının yurt dışı kart kullanım kısıtı

**Çözüm:**
- Banka müşteri hizmetlerine ara → yurt dışı ödeme aktivasyonu
- Farklı kart dene (Visa/Mastercard varyans)
- SEPA banka transferi ile dene (alternatif)

### 3. Çift Çekildi
**Sebep:** Onay görmedin, tekrar tıkladın

**Çözüm:**
- Uni-Assist'e iki kanıtla mail at:
  - Banka ekstresi (her iki çekim)
  - İşlem onay numaraları
- "Doppelzahlung — bitte um Rückerstattung"
- ~2-4 hafta içinde iade

### 4. Yanlış Referans Numarası
**Sebep:** Transfer'inde Verwendungszweck yanlış

**Çözüm:**
- Doğru referans numarasını al (Uni-Assist hesap sayfasında)
- Banka aracılığıyla "Referenz korrigieren" talebi

### 5. SWIFT Şube Bilgisi Sorunu
**Sebep:** Türk bankaları SWIFT ile uluslararası bağlantı kurarken sorun

**Çözüm:**
- Uni-Assist'in tam IBAN bilgisi hesap sayfasında
- SWIFT/BIC: HASPDEHHXXX (Hamburg)
- Banka şubesinde yardım iste — "international transfer to Germany"

## Pratik Önlemler

✅ **Erken öde** (deadline'dan 3+ hafta önce) — banka transferi yavaş olsa bile yetişir
✅ **Onay mailini dosyala** — sonradan referans için
✅ **Banka dekontunu sakla** (PDF/JPG)

## Ödeme Onayı Süresi

| Yöntem | Onay süresi |
| --- | --- |
| Kredi kartı (Visa/MC) | Aynı gün - 24 saat |
| Sofortüberweisung | Aynı gün |
| SEPA Türkiye'den | 3-7 iş günü |
| SEPA Almanya/Avrupa'dan | 1-2 iş günü |

Bağlantılı: [VPD ücreti](/sss/uni-assist/vpd-basvuru-ucreti-ve-ek-basvuru-ucreti-ne-kadar)
MD,

            'uni-assist-mein-konto-guncellemesi-nasil-yapilir' => <<<'MD'
Uni-Assist **Mein Konto** sayfası, başvurularını yönetebileceğin ana panel.

## Mein Konto'da Yapılabilecekler

- ✏️ Kişisel bilgileri düzenleme
- 📄 Yeni evrak yükleme
- ➕ Yeni dönem başvurusu
- 💳 Ödeme yapma + onay görme
- 📨 Mesajlaşma (Uni-Assist ile)
- 📥 VPD ve diğer belgeleri indirme

## Kişisel Bilgi Güncelleme

1. https://www.uni-assist.de → "Mein Konto" → giriş
2. Sol menü: **"Persönliche Daten"**
3. Düzenlemek istediğin alanları aç (ad, doğum tarihi, pasaport, adres, telefon)
4. **"Speichern"** → onay mailını kontrol et

⚠️ **Ad/Soyad değişikliği** → ek belge (evlilik, pasaport değişikliği) talep edilir.

## Yeni Evrak Yükleme

1. Sol menü: **"Bewerbung verwalten"**
2. İlgili başvuruyu seç
3. **"Dokumente hochladen"** → PDF yükle
4. Format: PDF veya JPEG, max 5 MB, renkli scan
5. 2-4 hafta içinde Uni-Assist değerlendirir

## Yeni Başvuru Ekleme

Aynı dönem için ek uniler:
1. **"Neue Bewerbung erstellen"**
2. Hedef uni + program seç
3. Mevcut evraklar **otomatik dahil**
4. Sadece **30 €** ek ücret

## Mesajlaşma

Uni-Assist mesajları → **"Postfach"** (gelen kutusu)

Cevap için "Antworten" → kısa mesaj veya belge ekle.

## VPD İndirme

1. **"Bewerbung verwalten"** → ilgili başvuru
2. **"VPD/Vorprüfungsdokumentation"** → PDF download
3. Resmi mühür + Uni-Assist imzası mevcut

## Şifre Unuttuysan

Giriş sayfası → **"Passwort vergessen"** → e-mail reset linki → 24 saat içinde değiştir.

## "Bewerbungsstatus" Anlamları

| Status | Anlamı |
| --- | --- |
| **Eingereicht** | Başvurun alındı |
| **In Bearbeitung** | İnceleme (~4-8 hafta) |
| **Zur Klärung** | Eksik belge — mesaj geldi |
| **An Hochschule weitergeleitet** | Uni'ye iletildi |
| **Abgeschlossen — Zugelassen** | Kabul edildi |
| **Abgeschlossen — Abgelehnt** | Reddedildi |

Bağlantılı: [Red sebepleri](/sss/uni-assist/uni-assist-red-sebepleri-en-cok-hangileridir)
MD,

            'uni-assist-evraklarini-postayla-mi-gondermem-gerekiyor' => <<<'MD'
**Çoğu Uni-Assist başvurusunda online yükleme yeterli — posta gerekmez.** Ama bazı uniler **orijinal/onaylı kopya posta** istiyor.

## Online vs Posta — Kim Ne İstiyor?

### Online Yeterli (Çoğunluk)
- TU Berlin, RWTH Aachen, KIT, Heidelberg, çoğu BW eyaleti
- Yüksek çözünürlüklü renkli scan (PDF)
- Apostil + tercüme de scan

### Posta Zorunlu (Az Sayıda)
- Bazı medikal programlar
- Bazı sanat akademileri (orjinal portfolyo)
- LMU, TUM bazı eski programlar

### Karma (Önce Online, Kabul Sonrası Posta)
- Online ile başvur, ön kabul al
- Kabul edilirsen orijinal evraklar posta ile
- Pek çok uni bu modeli kullanıyor

## Posta Süreci

### Türkiye'den DE'ye Belge Postası

**Önerilen kuryeler:**
- **DHL Express** — 2-4 iş günü, ~25-40 €
- **UPS** — benzer
- **PTT EMS** — 5-10 iş günü, ~15-25 €

**Önerilmeyen:**
- ❌ Standart PTT posta — takip yok, kayıp riski

### Uni-Assist Posta Adresi

```
uni-assist e.V.
Geneststraße 5
10829 Berlin
Deutschland
```

⚠️ **Bewerber-ID** (Uni-Assist hesap numarası) eklemen zorunlu.

## Hangi Belgeler Posta?

✅ Orijinal apostilli diploma (mezunsan)
✅ Onaylı kopya transcript (apostilli)
✅ Yeminli tercüme orijinal
❌ Pasaport kopyası — scan yeter
❌ Motivasyon mektubu — scan yeter

## Belgeler Geri İade Edilir mi?

Uni-Assist orijinal belgeleri **geri göndermez**. Bu yüzden:
- Diploma orijinalini gönderme — onaylı kopya gönder
- 2-3 set hazırla (Uni-Assist + uni + vize)

## Pratik Tavsiye

> Mümkünse **online başvuruyu** tercih et — hızlı, ucuz, kayıp riski yok. Uni posta isterse, kabul belgesi geldikten sonra gönder.

Bağlantılı: [Apostil gerekli mi?](/sss/uni-assist/apostilli-evrak-uni-assist-basvurusu-icin-gerekli-mi)
MD,

            'uni-assist-ile-universite-paralel-basvurusu-yapilabilir-mi' => <<<'MD'
**Evet — Uni-Assist'in en büyük avantajı paralel başvuru.** Aynı evraklarla **birden fazla üniversiteye** ek 30 €'larla başvurabilirsin.

## Avantajları

✅ **Tek evrak seti** — diploma, transcript, dil sertifikası 1 kez gönderilir
✅ **Ucuz ek ücret** — sonraki uni başvurusu 30 € (ilk 75 € yerine)
✅ **Tek VPD** — tüm uniler aynı VPD'i değerlendirir
✅ **Risk azaltma** — bir uniye reddediliyorsan diğerleri devam
✅ **Kabul gelmesi yüksek** — her uni farklı kriter

## Önerilen Strateji: 3-5 Uni Karması

| Tip | Açıklama | Sayı |
| --- | --- | --- |
| **Safe** | GPA'na göre kabul güvenli | 1-2 |
| **Match** | Senin seviyeye uygun | 2-3 |
| **Reach** | Hayalin uni, zor ama denenir | 1-2 |
| **Toplam** | | 5-7 başvuru |

### Örnek 5 Uni Başvurusu (Bilgisayar Müh.)

| Uni | Kategori | NC (yaklaşık) |
| --- | --- | --- |
| TUM Informatics | Reach | 1.5 |
| RWTH CS | Match | 2.0 |
| TU Berlin | Match | 2.0 |
| KIT | Match | 2.0-2.5 |
| TU Dresden | Safe | 2.5-3.0 |

Toplam Uni-Assist: 75 + 4×30 = **195 €**

## Birden Fazla Kabul Geldiğinde

1. **Süre ver** — uniler genelde 2-4 hafta cevap bekler
2. Hangisini kabul ettiğini **bir uniye onayla** — diğerlerine "Absage"
3. Yurt başvurusu **kabul ettiğin uni** üzerinden
4. Vize başvurusunda **tek kabul belgesi** gerekli

## Pratik İpucu

> **Tek uniye değil, en az 3-5 uniye başvur.** "Tek uniden red, diğer 4'ten kabul" yaygın senaryo. Ek 30 €'lar başvuru bütçesinin %1'i bile değil.

Bağlantılı: [Wintersemester Uni-Assist deadline](/sss/uni-assist/wintersemester-icin-uni-assist-deadline-ne-zaman)
MD,

            'uni-assist-vpd-10-ve-40-not-farki-nedir' => <<<'MD'
VPD'de "1.0" ve "4.0" Alman not sistemindeki **en iyi ve en düşük geçer notu** ifade eder. Bu **TR'deki 4.0/2.0 ile ters** çalışır.

## Alman Not Sistemi

| Not | Anlamı | Açıklama |
| --- | --- | --- |
| **1.0** | Sehr gut | %95-100 başarı |
| **1.3** | Sehr gut (eksi) | %90-94 |
| **1.7** | Gut (artı) | %85-89 |
| **2.0** | Gut | %80-84 |
| **2.3** | Gut (eksi) | %75-79 |
| **2.7** | Befriedigend (artı) | %70-74 |
| **3.0** | Befriedigend | %65-69 |
| **3.3** | Befriedigend (eksi) | %60-64 |
| **3.7** | Ausreichend (artı) | %55-59 |
| **4.0** | Ausreichend | %50-54 (Asgari geçer) |
| **4.1+** | Nicht ausreichend | Geçmez |

## Türk Sistemine Karşılık

### TR 4'lük Sistem

| TR not | Yaklaşık Alman not |
| --- | --- |
| 4.00 | 1.0 |
| 3.50 | 1.7 |
| 3.20 | 2.2 |
| 3.00 | 2.5 |
| 2.50 | 3.2 |
| 2.00 | 4.0 |

### TR 100'lük Sistem

| TR not | Yaklaşık Alman not |
| --- | --- |
| 90+ | 1.0-1.5 |
| 80-89 | 1.5-2.0 |
| 70-79 | 2.0-3.0 |
| 60-69 | 3.0-3.5 |
| 50-59 | 3.5-4.0 |

## Önemli Konular

### 1. Alman Sistemi "Ters" Çalışır
- **Düşük not = İyi** (1.0 mükemmel)
- **Yüksek not = Kötü** (4.0 zayıf)

"VPD'm 2.0 geldi" demek **iyi bir not**.

### 2. Modifiye Bayerische Formül
TR diploması Alman not sistemine **modifiye Bayerische formül** ile çevrilir:
- TR maksimum not → DE 1.0
- TR asgari geçer not → DE 4.0
- Aradakiler interpolasyon

### 3. Master Başvurusu Hedef Not

| Hedef program | Yaklaşık VPD not |
| --- | --- |
| TUM, RWTH, LMU prestij | 1.0-2.0 |
| Orta-üst düzey uniler | 2.0-2.5 |
| Ortalama uniler | 2.5-3.0 |
| NC frei programlar | 3.0+ kabul |

## Pratik İpucu

> VPD notunu **karşılaştırırken doğru sistemle bak.** Çoğu Türk öğrenci "TR'de 3.5 vardı, neden VPD'de 1.7 geldi?" diye soruyor — 1.7 aslında çok iyi bir Alman notu.

Bağlantılı: [HZB notu hesaplanması](/sss/uni-assist/uni-assist-hzb-notu-nasil-hesaplanir)
MD,

            'wintersemester-icin-uni-assist-deadline-ne-zaman' => <<<'MD'
Wintersemester (Ekim başlangıç) için **15 Temmuz standart deadline** — ama uniye göre değişir.

## Standart Deadline'lar

### Wintersemester (Ekim başlangıç)
| Tip | Uni-Assist'e ulaşma deadline |
| --- | --- |
| **Lisans (Bachelor)** | 15 Temmuz |
| **Master** | 15 Temmuz (çoğunluk), bazıları 31 Mayıs |
| **PhD** | Rolling (yıl boyu, hocaya bağlı) |
| **Studienkolleg** | 15 Temmuz |

### Sommersemester (Nisan başlangıç)
| Tip | Uni-Assist'e ulaşma deadline |
| --- | --- |
| **Lisans** | 15 Ocak |
| **Master** | 15 Ocak (çoğunluk) |
| **Studienkolleg** | 15 Ocak |

## Önemli Detay: "Posteingang" Tarihi

Deadline **Uni-Assist'in evraklara ulaşma tarihi** — başvuruyu gönderdiğin tarih değil:
- Online yükleme: Aynı gün
- Posta ile: **Deadline'dan 5-7 iş günü önce gönder**

⚠️ Türkiye'den posta ile gönderiyorsan **Temmuz başında** kuryele.

## Erken Deadline'lı Üniversiteler

| Uni | Wintersemester deadline |
| --- | --- |
| **Heidelberg** (bazı programlar) | 31 Mayıs |
| **TU München** (Informatics) | 31 Mayıs |
| **Frankfurt School of Finance** | 15 Mayıs |
| **Charité Berlin** (tıp/farmakoloji) | 1 Haziran |
| **Hertie School** | 1 Mart-15 Mayıs |

## "Erken Başvur" Avantajları

✅ Daha hızlı değerlendirme (kontenjan dolmadan)
✅ Burs deadline'larına yetiş (DAAD: Ekim-Kasım)
✅ Yurt başvurusu erken
✅ Vize randevusu yoğun dönemde değil

## Zaman Çizgisi (WiSe 2027 için)

| Tarih | Adım |
| --- | --- |
| **Ekim 2026** | Diploma + transcript + apostil |
| **Kasım 2026** | Yeminli tercüme |
| **Aralık 2026** | Uni-Assist hesabı + VPD başvurusu |
| **Ocak-Şubat 2027** | TestDaF veya IELTS sınavı |
| **Mart 2027** | VPD hazır + master başvuruları |
| **15 Temmuz 2027** | Uni-Assist son tarih |
| **Ağustos 2027** | Kabul belgeleri |
| **Eylül 2027** | Vize başvurusu |
| **Ekim 2027** | Almanya'ya gel |

## Pratik İpucu

> Başvuruyu **deadline'dan en az 4 hafta önce** tamamla. Uni-Assist evrak eksikliği bildirebilir — düzeltme süresi olmalı.

Bağlantılı: [VPD ücreti](/sss/uni-assist/vpd-basvuru-ucreti-ve-ek-basvuru-ucreti-ne-kadar) · [Master 3. sınıfta mı başvurmalı?](/sss/master/master-3-sinifta-mi-4-sinif-sonrasi-mi-basvurmali)
MD,
        ];
    }

    private function studienkollegAnswers(): array
    {
        return [
            'studienkolleg-nedir-kimler-icin-zorunlu' => <<<'MD'
**Studienkolleg**, Türk lise mezunlarının Almanya'da doğrudan lisans (Bachelor) okuyamadığı durumda gittiği **1 yıllık hazırlık programıdır**. Türkiye'de lise diploması direkt yeterli değil — Studienkolleg bu boşluğu kapatır.

## Kimler İçin Zorunlu?

Türk vatandaşı lise mezunları için **direkt lisansa kabul yok** — Studienkolleg veya 1 yıl üniversite okumuş olmak şart.

### Durum 1: TR Lise Mezunu (Üniversite Okumamış)
- **Studienkolleg zorunlu**
- 1 yıl hazırlık → Feststellungsprüfung sınavı
- Sonra Bachelor başvurusu

### Durum 2: TR'de Üniversite 1. Sınıf Tamamlanmış
- Bazı programlar için **Studienkolleg gerekmez** — doğrudan Bachelor başvurusu
- Bazı programlar için yine de Studienkolleg ister
- Uni'ye sor

### Durum 3: TR'de Lisans Tamamlanmış
- Direkt master başvurusu — Studienkolleg gerekmez
- Bachelor varsa hazır geçer

### Durum 4: TR'de İki Yıllık MYO/Yüksekokul
- Studienkolleg gerekli olabilir
- Anabin "H+" şartına bağlı

## Studienkolleg Sonrası

1. **Feststellungsprüfung** sınavı (1 yıl sonunda)
2. Geçersen → Bachelor başvurusu
3. **Birden çok uniye** başvurabilirsin

## Süreç

```
TR lise mezuniyeti
    ↓
TestAS veya Aufnahmeprüfung
    ↓
Studienkolleg giriş sınavı
    ↓
1 yıl Studienkolleg (M-Kurs, T-Kurs, W-Kurs, S-Kurs)
    ↓
Feststellungsprüfung (final sınav)
    ↓
Bachelor başvurusu (Uni-Assist veya direkt)
```

## Studienkolleg Tipleri

| Kurs | Hangi alana? |
| --- | --- |
| **M-Kurs** | Tıp + sağlık bilimleri |
| **T-Kurs** | Teknik (mühendislik, fen) |
| **W-Kurs** | İktisat, işletme |
| **G-Kurs** | Beşeri bilimler, hukuk |
| **S-Kurs** | Dil, sanat |

## Vize ile İlişki

Studienkolleg için **akademik vize (§16b)** gerekiyor — dil kursu vizesi DEĞİL. Kabul belgesi Studienkolleg'den geliyor, vize Almanya'ya hazırlık + Bachelor için olarak veriliyor.

Bağlantılı: [Studienkolleg M-Kurs T-Kurs farkı](/sss/studienkolleg/studienkolleg-m-kurs-ile-t-kurs-arasindaki-fark-nedir)
MD,

            'studienkolleg-feststellungsprufung-sureci-nasil-isler' => <<<'MD'
**Feststellungsprüfung (FSP)**, Studienkolleg'in 1 yıl sonunda yapılan **final sınavı** — Bachelor kapısını açar.

## FSP Yapısı

Sınav 5 ders + Almanca'dan oluşur. Detay kursa göre:

### T-Kurs (Teknik)
- Almanca (yazılı + sözlü)
- Matematik
- Fizik
- Kimya
- (Bilgisayar veya İngilizce)

### M-Kurs (Tıp)
- Almanca
- Biyoloji
- Kimya
- Fizik
- Matematik

### W-Kurs (İktisat)
- Almanca
- Matematik
- Ekonomi
- BWL/VWL
- İngilizce

### G-Kurs (Beşeri)
- Almanca
- Tarih veya Edebiyat
- İngilizce
- Politika veya Felsefe
- (Sosyoloji)

## Sınav Türleri

| Bölüm | Format | Süre |
| --- | --- | --- |
| **Yazılı sınav** | Her ders için 2-4 saat | 1-2 hafta |
| **Sözlü sınav** | Almanca + 2 ders | 15-20 dk |
| **Yazılı Almanca** | Essay + dilbilgisi | 3-4 saat |

## Notlar ve Geçer

- Her ders **1.0-4.0** arası (DSH-2 standart)
- **Almanca DSH-2 zorunlu** (yazılı + sözlü)
- Diğer dersler **4.0 üstü** olamaz
- Genel ortalama **2.5+** önerilir (master/lisans için)

## Geçemezsem?

❌ **1 kez tekrar girme hakkı** var (genelde 1 dönem sonra)
❌ 2. kez de geçemezsen → Studienkolleg iptal
❌ Studienkolleg sonrası 6-12 ay içinde Bachelor başvurusu yoksa kabul iptali

✅ **Almanca'da kalan ders var** ama matematik geçtin → bazı uniler "şartlı kabul" verebilir

## FSP ile Direkt Almanya Üniversitesine Giriş

FSP başarılı:
- **Tüm Alman üniversitelerinde geçerli** (federal)
- HZB (Hochschulzugangsberechtigung) statüsü verilir
- Bachelor başvurusu için Anabin kontrolü gerekmez

## TestAS ile Karşılaştırma

| FSP vs | Avantajı |
| --- | --- |
| **TestAS** | Tek sınav, Türkiye'den girilir |
| **FSP** | 1 yıl emek, ama Almanca seviyesi de güçleniyor |

TestAS Türkiye'den girilebilirken FSP **sadece Studienkolleg'in son aşaması**.

## Vize Etkisi

FSP sonucunu beklerken vize devam ediyor (öğrenci oturum izni Studienkolleg üzerinden). Geçtikten sonra:
- Bachelor kabulü 4-8 hafta
- Vize Bachelor için **devam eder** (yeni başvuru gerekmez)
- Yeni Immatrikulation belgesi ile Ausländerbehörde'ye bildirim

Bağlantılı: [Studienkolleg sonrası uni kabulü garanti mi?](/sss/studienkolleg/studienkolleg-sonrasi-universite-kabulu-garanti-mi)
MD,

            'studienkolleg-m-kurs-ile-t-kurs-arasindaki-fark-nedir' => <<<'MD'
M-Kurs ve T-Kurs, Studienkolleg'in **farklı akademik alanlara hazırlayan iki ayrı dalı**.

## Farklılaşma

| Kurs | Sınıflar | Hedef Bachelor |
| --- | --- | --- |
| **M-Kurs** | Medizinisch | Tıp, Diş, Biyoloji, Eczacılık, Beslenme |
| **T-Kurs** | Technisch | Mühendislik, Fen, Bilgisayar, Mimarlık |

## M-Kurs Müfredatı

- **Almanca** (DSH-2 hedefli)
- **Biyoloji** (hücre, genetik, anatomi)
- **Kimya** (organik + anorganik)
- **Fizik** (mekanik, termodinamik)
- **Matematik** (analiz, lineer cebir)

## T-Kurs Müfredatı

- **Almanca** (DSH-2 hedefli)
- **Matematik** (yoğun)
- **Fizik** (yoğun)
- **Kimya** (orta düzey)
- **(Opsiyonel:** Bilgisayar veya İngilizce)

## Hangi Kursa Gideceğine Karar

✅ **M-Kurs** seç şu Bachelor programları için:
- Tıp, Diş hekimliği, Eczacılık
- Veterinerlik, Beslenme
- Biyokimya, Biyomühendislik
- Pharma
- Hemşirelik, Sağlık Bilimleri

✅ **T-Kurs** seç şu Bachelor programları için:
- Bilgisayar Mühendisliği
- Elektrik-Elektronik Mühendisliği
- Makine Mühendisliği
- İnşaat Mühendisliği
- Endüstri Mühendisliği
- Mimarlık
- Matematik, Fizik (saf)
- Kimya Mühendisliği (T-Kurs veya M-Kurs)

## Geçiş Yapabilir miyim?

⚠️ M-Kurs → T-Kurs **dönemler arası** geçiş **çok zor** — müfredat farklı, hangi yöne gittiğine karar vererek başla.

## TestAS Etkisi

Studienkolleg giriş sınavı yerine **TestAS** alabilirsin (yüksek puan):
- M-Kurs için → TestAS Naturwissenschaften modülü
- T-Kurs için → TestAS Ingenieurwissenschaften modülü

Bu durumda Studienkolleg gerekmez veya 6 aylık dil kursu yeter.

## Bachelor Hedefin Belirsiz

Eğer "henüz emin değilim, biyoloji de mühendislik de düşünüyorum":
- **M-Kurs** seç → her iki yöne açık (özellikle biyoteknoloji, biyomühendislik)
- T-Kurs sadece teknik yönünü destekler

## Vize için Etkisi

M-Kurs veya T-Kurs vize başvurusunda **fark yapmaz**:
- Aynı Studienkolleg vize prosedürü
- Aynı Sperrkonto miktarı
- Aynı süre

Bağlantılı: [Studienkolleg nedir](/sss/studienkolleg/studienkolleg-nedir-kimler-icin-zorunlu) · [Hazırlık atlanabilir mi?](/sss/studienkolleg/hazirlik-programini-atlayip-dogrudan-bachelor-basvurusu-mumkun-mu)
MD,

            'devlet-studienkolleg-ucretsiz-mi-ozel-okul-ne-kadar' => <<<'MD'
**Devlet Studienkolleg ücretsiz** (sadece semester ücreti ~150-350 €). **Özel Studienkolleg** ücreti **3,000-12,000 €/yıl** arasında değişir.

## Karşılaştırma

| Özellik | Devlet Studienkolleg | Özel Studienkolleg |
| --- | --- | --- |
| **Yıllık ücret** | 0 € (sadece semester fee) | 3,000-12,000 € |
| **Kontenjan** | Sınırlı (~30-40/eyalet) | Daha geniş |
| **Giriş zorluğu** | Aufnahmeprüfung sınavı | Test + ödeme |
| **Süre** | 1 yıl (2 dönem) | 1 yıl (genelde) |
| **Sonu sınav** | Feststellungsprüfung resmi | Feststellungsprüfung **özel okul + uni** |
| **Diploma denkliği** | Tüm DE uniler kabul | Tüm DE uniler kabul (varsa) |

## Devlet Studienkolleg'ler

Almanya'da **devlet Studienkolleg'leri**:

### Berlin
- Studienkolleg an der TU Berlin
- Studienkolleg an der FU Berlin

### Hamburg
- Studienkolleg Hamburg

### NRW
- Studienkolleg an der Bochum, Köln, Aachen

### Bayern
- Studienkolleg München (DSU)

### Baden-Württemberg
- Studienkolleg Konstanz, Heidelberg, Karlsruhe

### Niedersachsen
- Studienkolleg Hannover, Göttingen

### Saxony, Saxony-Anhalt, Thüringen
- Studienkolleg Halle, Leipzig

## Özel Studienkolleg'ler

Resmi kabul gören özel okullar:

- **Studienkolleg Hochschule Anhalt** (ortak tip)
- **Studienkolleg Coburg**
- **AKADEMIE+ INTERNATIONAL** (Berlin)
- **Studienkolleg Mittelhessen** (Marburg)

⚠️ Bazı özel okullar **resmi kabul görmüyor** — diploma Almanya'da geçerli olmuyor. Başvurmadan önce uninin tanıdığını doğrula.

## Kabul İçin Sürecek

### Devlet Studienkolleg
- **Aufnahmeprüfung** (giriş sınavı)
- Yazılı: Matematik + Almanca
- Yıllık **2 kez** sınav (Wintersemester + Sommersemester)
- Sınavda **geçer not** + kontenjan → kabul

### Özel Studienkolleg
- Tipik giriş sınavı + ödeme
- Daha az rekabetçi
- Daha hızlı kayıt
- Ama: kalite + ücret değişken

## Hangisi Sana Uygun?

✅ **Devlet Studienkolleg** tercih et şu durumlarda:
- Bütçe sıkı
- Yüksek motivasyon (giriş sınavına hazırlanmak için)
- Prestij ve diploma kalitesi önemli

✅ **Özel Studienkolleg** tercih et şu durumlarda:
- Bütçe yeterli
- Devlet kontenjanını alamamışsın
- Daha küçük sınıf + bireysel destek istiyorsun
- Bazı işlerin daha hızlı tamamlanmasını istiyorsun

## Vize Etkisi

Devlet ve özel Studienkolleg arasında **vize başvurusu açısından fark yok** — her ikisi de akademik vize §16b altında.

Bağlantılı: [Studienkolleg giriş sınavı](/sss/studienkolleg/studienkolleg-giris-sinavinda-neler-soruluyor)
MD,

            'studienkollege-kabul-icin-almanca-c1-sart-mi' => <<<'MD'
**Çoğu Studienkolleg için B2 yeterli** — C1 zorunlu değil. Ama bazı eyaletler B2'nin üstü ister.

## Eyaletlere Göre Studienkolleg Almanca Şartı

| Eyalet | Minimum Almanca |
| --- | --- |
| **Berlin** | B2 (bazı kurs B2+ ister) |
| **NRW** | B2 |
| **Bavyera** | B2-C1 (uniye göre) |
| **Baden-Württemberg** | B2 minimum |
| **Hamburg** | B2 |
| **Sachsen** | B2 |
| **Hessen** | B2-B2+ |

## Studienkolleg Almanca Mantığı

Mantık:
- **Studienkolleg'in amacı** Almanca'nı C1'e çıkarmak
- B2 ile başla, 1 yıl içinde C1 (DSH-2)'a çık
- Bu yüzden giriş için B2 yeterli

⚠️ Eğer hâlâ A2 seviyesindeysen → önce Türkiye'de B1-B2'ye çık → sonra Studienkolleg

## Giriş Sınavı: Almanca Bölümü

Studienkolleg Aufnahmeprüfung'unda Almanca:
- **B2 seviyesinde** olmalısın
- Yazılı (dilbilgisi + okuma): 90 dk
- Mülakat (sözlü): 10-15 dk

## Sertifika Kabulü

Aşağıdaki Almanca sertifikalar Studienkolleg giriş şartını sağlar:
- ✅ **Goethe B2** veya **B2+** (en yaygın)
- ✅ **Telc B2**
- ✅ **TestDaF B2** (TDN 3 her bölüm)
- ✅ **ÖSD B2**
- ⚠️ DSH yok (DSH zaten Studienkolleg sonu)

## Studienkolleg İçinde Almanca Dersi

1 yıl boyunca Almanca dersleri **yoğun**:
- Haftalık 10-15 saat Almanca
- Dilbilgisi + okuma + yazma + konuşma
- Akademik Almanca + alan terminolojisi (T-Kurs için teknik, M-Kurs için biyolojik vb.)
- 1 yıl sonunda **DSH-2** veya **TestDaF TDN 4** seviyesi

## "Daha Yüksek Seviye Daha İyi" Avantajı

C1 ile geliyorsan:
- Studienkolleg Almanca dersini **kolay** geçersin
- Diğer derslere daha fazla zaman ayırırsın
- Final FSP'de DSH-2 yerine **DSH-3** alabilirsin (prestijli uniler için ek avantaj)

## Vize Etkisi

Studienkolleg vizesi için **B1** yeterli olabilir (özellikle giriş sınavı hazırlık için). Ama:
- Giriş sınavına gireceksen B2 lazım
- Vize başvurusunda B2 sertifikası **güçlü dosya** olur

Bağlantılı: [Studienkolleg giriş sınavı](/sss/studienkolleg/studienkolleg-giris-sinavinda-neler-soruluyor) · [Almanca B2 master](/sss/dil/b2-sertifikasi-ile-master-basvurusu-yapabilir-miyim)
MD,

            'studienkolleg-egitimi-1-yil-mi-2-yil-mi-surer' => <<<'MD'
**Standart Studienkolleg 1 yıl (2 dönem)** — 1 Wintersemester + 1 Sommersemester. **2 yıl olanlar istisnai durum**.

## 1 Yıl (Standart)

Normal Studienkolleg programı:
- **2 dönem (Wintersemester + Sommersemester)**
- Toplam **40 hafta** yoğun eğitim
- Haftalık ~30 saat ders
- 1 yılın sonunda **Feststellungsprüfung**

## 2 Yıl Olabilir mi?

Bazı durumlarda 2 yıl gerekir:

### 1. Almanca Seviyesi Yetersiz
- A2 veya altıyla başlıyorsan
- 1. yıl **sadece dil kursu** + ön hazırlık
- 2. yıl asıl Studienkolleg
- "Vorlaufkurs" + "Hauptkurs" toplam **18 ay-2 yıl**

### 2. Tıbbi Sebepler
- Hastalık veya aile durumu nedeniyle dönem tekrarı
- Resmi belgeyle kayıt değişikliği

### 3. Sınav Tekrarı
- Feststellungsprüfung'da kaldıysan
- 1 dönem daha ekleyebilirsin (Wiederholung)
- Toplam ~18 ay

## Yarı Yıl Yapı (Sömestir)

Her dönem ~5 ay:

| Dönem | Aylar | Ders türleri |
| --- | --- | --- |
| **Wintersemester (Wise)** | Ekim-Şubat | Temel dersler, dilbilgisi yoğun |
| **Şubat-Mart** | Yarıyıl ara | Sınav + tatil |
| **Sommersemester (Sose)** | Nisan-Temmuz | İleri konular, sınav hazırlığı |
| **Temmuz-Eylül** | FSP | Final sınav + sonuçlar |

## "Hangi Dönemden Başlamalı?"

İdeal: **Wintersemester'da başla** (Ekim)
- Tam 1 yıl döngüsü → Temmuz'da FSP → Ekim Bachelor başlangıcı
- Senkron geçiş

Sommersemester başlangıcı (Nisan):
- Tam yıl yapılır ama bitiş Mart → Bachelor başlangıcı Ekim'i bekliyorsun
- 6 ay boşluk → bu süre değerlendirilebilir (dil ilerletme, staj)

## Vize Süresi

Studienkolleg vizesi genelde:
- İlk veriliş: **1-2 yıl**
- Studienkolleg sonu yeniden değerlendirme
- Geçersen → Bachelor için uzatma
- Geçemezsen → 6 aylık ek süre var

## Pratik Plan

| Hedef | Yapı |
| --- | --- |
| 1 yılda Bachelor başlangıcı | Wise 2026 Studienkolleg → Wise 2027 Bachelor |
| 18 aylık plan | Sose 2026 Studienkolleg → Wise 2027 Bachelor |
| 2 yıllık plan | Vorlaufkurs + Studienkolleg → 2028 Bachelor |

## Fast-Track Programlar

Bazı özel Studienkolleg'lerde **6 ay yoğun program** mevcut:
- Çok pahalı (~8,000-12,000 €)
- Sadece T-Kurs/W-Kurs
- Sıkı program (haftalık 40 saat)
- Devlet kontenjanından farklı

Bağlantılı: [Studienkolleg yıl içinde 2 dönem mi](/sss/studienkolleg/studienkolleg-yil-icinde-2-donem-mi-aliyor)
MD,

            'studienkolleg-giris-sinavinda-neler-soruluyor' => <<<'MD'
Studienkolleg Aufnahmeprüfung (giriş sınavı), **2 ana bölümden** oluşur: **Almanca + Matematik**. Bazı uniler ek olarak alan dersleri sorabilir.

## Sınav Yapısı

### Bölüm 1: Almanca (90 dk)
- **Okuma anlama**: Akademik metin + sorular
- **Dilbilgisi**: Çoktan seçmeli + boşluk doldurma
- **Yazma**: Kısa essay (200-300 kelime)
- **Bazı uniler**: Dinleme (45 dk ek)

### Bölüm 2: Matematik (90 dk)
- **Cebir**: Denklemler, fonksiyonlar
- **Geometri**: Trigonometri, alan/hacim
- **Analiz**: Türev, integral (T-Kurs için)
- Hesap makinesi izinli (uniye göre değişir)

### Bölüm 3: Mülakat (Sözlü, 10-15 dk)
- Almanca konuşma değerlendirmesi
- Motivasyon: "Neden Almanya? Neden Studienkolleg?"
- Bachelor hedefin nedir?

## Kurs Türüne Göre Ek

### T-Kurs Giriş Sınavı
- Yukarıdakilere ek: **Fizik** (60 dk)
- Mekanik, optik, termodinamik temelleri

### M-Kurs Giriş Sınavı
- Almanca + Matematik + **Biyoloji** (60 dk)
- Hücre, anatomi, basit fizyoloji

### W-Kurs Giriş Sınavı
- Almanca + Matematik + **Ekonomi** (60 dk, opsiyonel)
- Veya İngilizce

## Seviye Beklentisi

| Bölüm | Beklenen seviye |
| --- | --- |
| **Almanca** | B2 (giriş seviyesi) |
| **Matematik** | TR lise 11-12 müfredatı |
| **Fizik** | TR lise 11-12 |
| **Biyoloji** | TR lise 12 |

⚠️ TR müfredatı genelde yeterli — endişelenmene gerek yok eğer lise mezunusan.

## Sınav Tarihleri

| Hedef Studienkolleg | Sınav tarihi |
| --- | --- |
| **Wintersemester (Ekim 2026)** | Mayıs-Haziran 2026 |
| **Sommersemester (Nisan 2026)** | Kasım-Aralık 2025 |

## Sınav Yeri

Devlet Studienkolleg'in yerinde:
- Berlin, München, Hamburg, Heidelberg vb.
- **Almanya'ya gitmen gerekiyor** sınav için
- Ya da: **Vorab-Test** (önce-test) — Türkiye'den online sınav, bazı uniler kabul ediyor

## Tekrar Hakkı

Geçemezsen:
- **1 sonraki sınav dönemi** girebilirsin (genelde 6 ay sonra)
- Aynı uniye tekrar başvuru
- Veya farklı bir Studienkolleg'e başvur

## TestAS Alternatif

Yüksek TestAS skoru ile (genelde 100+):
- Studienkolleg gerekmez
- Direkt Bachelor başvurusu (uniye göre)
- Sadece Almanca dersi alman gerekebilir

Bağlantılı: [Studienkolleg nedir](/sss/studienkolleg/studienkolleg-nedir-kimler-icin-zorunlu)
MD,

            'studienkolleg-sonrasi-universite-kabulu-garanti-mi' => <<<'MD'
**Hayır — Feststellungsprüfung geçmek Bachelor kabulü garantilemiyor.** FSP **HZB statüsü** verir, ama uniye başvuru hâlâ rekabetçi.

## Studienkolleg → Bachelor Süreci

```
Studienkolleg Feststellungsprüfung başarılı
    ↓
HZB (Hochschulzugangsberechtigung) statüsü
    ↓
Bachelor başvurusu (Uni-Assist)
    ↓
Uni karar verir → kabul/red
```

## Neden Garanti Değil?

- **NC olan programlar**: Yine FSP notuna göre sıralama
- **Kontenjan kısıtı**: Yıllık başvuru > kontenjan
- **AB-dışı kontenjan**: %5-10 (TR vatandaşı bu kotaya tabi)
- **Hedef uni seçimi**: Senin Studienkolleg'in mi başka uni mi?

## FSP Notu ve Kabul İlişkisi

FSP'den çıkan **Endnote** (final not), Bachelor başvurusunda kritik:

| FSP not | Kabul ihtimali |
| --- | --- |
| **1.0-1.5** | TUM, RWTH, LMU dahil her uni |
| **1.5-2.0** | Çoğu uni, prestijliler dahil |
| **2.0-2.5** | Standart uniler |
| **2.5-3.0** | Daha az talep gören uniler |
| **3.0+** | NC frei programlar veya FH'ler |

## Aynı Uni'ye Doğrudan Geçiş

Çoğu Studienkolleg **bir üniversiteye bağlı** (örnek: Studienkolleg an der TU Berlin):
- ✅ **Bu uniye** kabul daha kolay (öncelikli sırada değerlendirme)
- ⚠️ Başka uniye başvururken yine rekabet
- ✅ Doğrudan FSP not + Almanca seviyesiyle değerlendirilir

## Studienkolleg Sonrası Birden Çok Uniye Başvuru

FSP geçer geçmez:
1. **Studienkolleg sertifikası** + transcript al
2. **Uni-Assist VPD** (FSP HZB statüsü ile yeni VPD)
3. **3-5 uniye paralel başvur**
4. Wintersemester (Ekim) için 15 Temmuz deadline

## Kabul Şartları (FSP Sonrası)

Studienkolleg + FSP geçtin:
- ✅ **HZB statüsü** — uni başvurusu için yeterli
- ✅ **DSH-2 Almanca** — uni dil şartı karşılanmış
- ✅ **TestAS** veya **APS** gerekmez

Yine ihtiyaç:
- Motivasyon mektubu
- Uni-Assist başvurusu
- Bazı NC programlarda yüksek not

## Pratik İpucu

> Studienkolleg'de **FSP notunu yükseltmek** kritik. 1 yıl yoğun çalışma sonunda 2.5'in altına düşersen Bachelor seçimi sınırlanır.

## Bekleme Dönemi (Wartesemester)

Düşük FSP notuyla:
- "Bekleme dönemi" Türk öğrenciler için sınırlı uygulanır
- Master'a geçiş için Bachelor şart
- 6 ay-1 yıl bekleyip yeniden başvuru mümkün

Bağlantılı: [Feststellungsprüfung süreci](/sss/studienkolleg/studienkolleg-feststellungsprufung-sureci-nasil-isler) · [Direkt Bachelor karşılaştırması](/sss/studienkolleg/studienkolleg-ile-direkt-bachelor-karsilastirmasi-hangisi-avantajli)
MD,

            'studienkolleg-icin-vize-basvurusu-ozel-mi' => <<<'MD'
Studienkolleg vizesi **akademik vize (§16b 2. fıkra)** kapsamında — dil kursu vizesinden farklı. Süreç standart öğrenci vizesine çok benziyor.

## Vize Türü

- **§16b AufenthG (akademik vize)** — Studienkolleg öğrencileri için
- **Maximum 2 yıl** veriliyor (1+1)
- Bachelor başvurusu için **otomatik uzatma**

## Dil Kursu Vizesinden Farkı

| Özellik | Studienkolleg (§16b) | Dil Kursu (§16f) |
| --- | --- | --- |
| **Süre** | 1-2 yıl | Max 1 yıl |
| **Sonraki adım** | Bachelor başvurusu kolay | Bachelor geçiş zor |
| **Çalışma hakkı** | Var (20 saat/hafta) | Genelde yok |
| **Aile birleşim** | Mümkün | Çok zor |
| **Vize ret riski** | Düşük | Yüksek |

## Studienkolleg Vize Şartları

1. **Studienkolleg kabul belgesi** (Zulassung)
2. **Almanca B2** sertifika (genelde Goethe B2 yeterli)
3. **Sperrkonto**: 1 yıl × 992 € = **11,904 €** (2026)
4. **Sağlık sigortası**: 30K € teminat veya GKV taahhüt
5. **Lise diploması** + apostil + tercüme
6. **Pasaport** + biyometrik foto
7. **Motivasyon mektubu** + CV

## Başvuru Süreci

```
Studienkolleg kabul
    ↓
IDATA / konsolosluk randevu (6-12 hafta)
    ↓
Evrak teslim
    ↓
Karar (6-12 hafta)
    ↓
Vize basımı (1-2 hafta)
```

Toplam: **3-6 ay** kabul belgesi → vize

## Studienkolleg Vizesinde Sperrkonto Yıllık Miktar

| Süre | Sperrkonto |
| --- | --- |
| **12 ay** | 11,904 € |
| **18 ay** (Vorlaufkurs + Hauptkurs) | ~17,856 € |
| **24 ay** | ~23,808 € |

⚠️ Studienkolleg uzun süreliyse hesap miktarı **buna göre artar**.

## Çalışma Hakkı

Studienkolleg öğrencisi:
- ✅ **20 saat/hafta** çalışabilirsin
- ✅ **240 yarı zamanlı gün veya 120 tam gün/yıl**
- ⚠️ Çoğu öğrenci ilk yıl çalışmaz — yoğun ders programı

## Vize Uzatma

Studienkolleg → Bachelor geçişinde:
1. Bachelor kabul belgesi al
2. Ausländerbehörde'ye **30 gün öncesinden** başvur
3. Vize **3-4 yıl** Bachelor süresince uzatılır
4. Yeni Aufenthaltstitel (oturum izni kartı)

## Vize Reddi Riskleri

Studienkolleg vizesi diğer vizelere göre az reddedilir:
- ✅ Net akademik amaç
- ✅ Resmi kabul belgesi
- ✅ Tüm evraklar standart

Yine de reddedilir:
- ❌ Sahte/eksik belgeler
- ❌ Önceki Schengen reddi
- ❌ Çelişkili finansman (Sperrkonto + sponsor karışıklığı)

Bağlantılı: [Studienkolleg Sperrkonto gerekli mi?](/sss/studienkolleg/studienkolleg-icin-sperrkonto-gerekli-mi) · [Vize başvurusu nasıl yapılır?](/sss/vize/almanya-ogrenci-vizesi-nasil-alinir)
MD,

            'studienkolleg-icin-sperrkonto-gerekli-mi' => <<<'MD'
**Evet — Studienkolleg vizesi için Sperrkonto zorunlu**, tıpkı diğer öğrenci vizeleri gibi.

## Sperrkonto Miktarı (2026)

| Vize süresi | Sperrkonto |
| --- | --- |
| **1 yıl Studienkolleg** | 11,904 € |
| **1 yıl + Vorlaufkurs (18 ay)** | ~17,856 € |
| **2 yıl** | ~23,808 € |

Bu rakam **aylık 992 €** üzerinden hesaplanır — Almanya'nın BAföG asgari geçim seviyesi.

## Hangi Bankada Açtırabilirim?

Studienkolleg vizesi için **standart Sperrkonto sağlayıcıları** kabul ediliyor:

| Sağlayıcı | Süre + Maliyet |
| --- | --- |
| **Fintiba** | Açılış 89 €, aylık 5-10 € |
| **Expatrio** | 49 € açılış, 5 €/ay |
| **Coracle** | 99 €, ~4 €/ay |
| **Deutsche Bank** | ~150 € + 5 €/ay |
| **Sparkasse** (yerel) | Eyalete göre |

## Studienkolleg ile Sperrkonto Süreci

1. Studienkolleg kabul al
2. Sperrkonto açtır (en hızlı: Fintiba 24-48 saat)
3. Yıllık tutarı tek seferde yatır (TR bankasından SWIFT veya Wise)
4. **Sperrkonto Bestätigung** belgesini al
5. Vize başvurusunda göster

## "Daha Az Para Yatırabilir miyim?"

❌ **Hayır** — Sperrkonto miktarı kesin. Düşük yatırılırsa vize reddedilir.

⚠️ İstisna: Sponsor + Verpflichtungserklärung birleştirme → kısmi Sperrkonto + sponsor mektubu

## Alternatif: Sponsor (Verpflichtungserklärung)

Sperrkonto yerine:
- Almanya'da yaşayan biri sana sponsor olabilir
- **Verpflichtungserklärung** — "geçimini üstleniyorum" beyanı
- Almanya'da Ausländerbehörde'den çıkarılır
- Sponsor'un mali durumu yeterli olmalı (gelir + servet kanıtı)

## Vize Aldıktan Sonra Para

Geldiğinde:
- Sperrkonto Almanya bankasında kilitli
- **Her ay 992 €** çekebilirsin (otomatik değil — sen aktarman gerekir)
- Yıl sonu kalan miktar serbest kalır
- Hesap kapatma (DE'den çıkış sonrası): ~25 € ücret

## Aile Durumu

Eş ve çocuk için ek Sperrkonto:
- Eş: ek ~900 €/ay
- Çocuk: ~300 €/ay
- Aile birleşimi için ek miktar gerekli

## Pratik İpucu

> Sperrkonto'yu **vize randevusu yaklaşırken** açtır (1-2 hafta öncesi). Erken açtırırsan aylık ücret birikir. Fintiba 24-48 saat → vize randevusundan **3-4 gün önce** açtırmak yeterli.

Bağlantılı: [Sperrkonto alternatifleri](/sss/vize/fintiba-disinda-bloke-hesap-sperrkonto-acabilecegim-alternatifler-nele) · [Bloke hesapta ne kadar para](/sss/vize/bloke-hesapta-ne-kadar-para-olmasi-gerekiyor)
MD,

            'hazirlik-programini-atlayip-dogrudan-bachelor-basvurusu-mumkun-mu' => <<<'MD'
**Bazı durumlarda evet — Studienkolleg'i atlayabilirsin**. Ama bunun için **belirli şartları** sağlaman gerekiyor.

## Studienkolleg'i Atlama Yolları

### 1. TestAS Yüksek Puan
**TestAS = Test für Ausländische Studierende** — Türkiye'de girilen uluslararası öğrenci testi.

- Yıllık 4 kez yapılır
- Almanca veya İngilizce
- 4 modül: Genel + alanına göre (Matematik, Mühendislik, Doğa Bilim, Sosyal Bilim, vb.)

| TestAS puanı | Studienkolleg etkisi |
| --- | --- |
| **100+ (yüksek)** | Bazı uniler Studienkolleg muafiyeti veriyor |
| **80-99** | Studienkolleg gerekli |
| **80 altı** | Genelde Studienkolleg |

### 2. TR'de Üniversite 1+ Yıl Tamamladıysan
- **Tamamen geçer programlar** için Studienkolleg gerekmez
- Anabin'de TR uni'n "H+" işaretliyse Bachelor direkt başvuru
- **2 dönem tamamla** → genelde yeterli

### 3. Anadolu Üniversitesi gibi DSD Lisesi Mezunu
- DSD (Deutsches Sprachdiplom) B1/B2 sertifikası olan öğrenciler
- TR'de Alman PASCH okul mezunları
- **Studienkolleg yok**

### 4. DAAD Programı veya Burs
- DAAD bursu kabul edersen Studienkolleg gerekmez
- Uni'nin DAAD ortaklığı varsa direkt başvuru

### 5. AB Vatandaşlığı (Hayır, Türk vatandaşı değilsen)
- Türk vatandaşları için bu yol yok

## Studienkolleg'i Atlamanın Avantajları

✅ 1 yıl kazanırsın
✅ ~12,000 € Sperrkonto + yaşam masrafı tasarruf
✅ Bachelor doğrudan başlar
✅ Vize tek aşamalı

## Dezavantajları

❌ Almanca seviyen yetersizse zorlanırsın
❌ TR lise müfredatın eksikleri kapatılmaz
❌ Kabul daha rekabetçi (FSP geçer notlu Studienkolleg mezunlarına kıyasla)

## Direkt Bachelor Başvurusu Şartları

Studienkolleg'siz Bachelor başvurusu:
- **TestAS** veya **APS** veya **DSD** belgesi
- **Almanca C1** (DSH-2/TestDaF TDN 4)
- TR lise diploması + **yüksek not**
- Bazı programlarda TR'de 1+ yıl uni okumuş

## Pratik Plan

Kararını şu kriterlere göre ver:

| Kriter | Studienkolleg | Direkt Bachelor |
| --- | --- | --- |
| **Almanca seviyem** | B2 yeter | C1 zorunlu |
| **TR notum** | Standart | Yüksek olmalı |
| **TestAS puanım** | Gerekmez | Yüksek olmalı |
| **Bütçem** | 1 yıl ek | Hemen Bachelor |
| **Hız** | Yavaş | Hızlı |

## Pratik İpucu

> Şüpheliysen Studienkolleg tercih et. Almanca'nı sıfırdan **DSH-2'ye çıkarmak** Studienkolleg'de daha sistematik. Direkt Bachelor başlayanlar genelde ilk yıl Almanca seviyesi nedeniyle yarış kaybediyor.

Bağlantılı: [Direkt Bachelor karşılaştırma](/sss/studienkolleg/studienkolleg-ile-direkt-bachelor-karsilastirmasi-hangisi-avantajli)
MD,

            'studienkollegde-basarisiz-olursam-ne-olur' => <<<'MD'
Studienkolleg'de başarısızlık birden fazla türde olabilir — sebebine göre **çözüm yolları** mevcut.

## Başarısızlık Türleri

### 1. Aufnahmeprüfung (Giriş Sınavı) Reddedildi
**Ne oldu:** Giriş sınavında geçemedin

**Çözüm:**
- ✅ **1 sonraki sınav dönemi** dene (6 ay sonra)
- ✅ Farklı şehir/eyalet Studienkolleg'i dene
- ✅ Hazırlık kursu al (Almanca + matematik)
- ✅ Türkiye'de bekleme süresinde dil ilerlet

### 2. Dönem İçi Geçemedin
**Ne oldu:** 1. veya 2. dönem ortalama altında

**Çözüm:**
- ✅ **Dönem tekrarı** mümkün (1 kez)
- ✅ Süreç 1.5-2 yıl uzayabilir
- ⚠️ Vize uzatma için kanıt gerekli ("akademik ilerleme")

### 3. Feststellungsprüfung (FSP) Reddedildi
**Ne oldu:** Final sınavda geçer not alamadın

**Çözüm:**
- ✅ **1 kez tekrar girme hakkı** (6 ay sonra)
- ✅ Bu dönemde dersi tekrar al + Almanca ilerlet
- ✅ İkinci kez geçemezsen: Studienkolleg kapanır

### 4. FSP Geçtin Ama Notu Düşük
**Ne oldu:** FSP'den 3.0+ aldın, hedef üniler kabul vermiyor

**Çözüm:**
- ✅ NC frei programlara başvur
- ✅ Daha az talep gören uniler
- ✅ FH (Fachhochschule, uygulamalı bilimler)
- ✅ Farklı program — Bachelor seçimini değiştir

## Vize Etkisi

### Studienkolleg Vizen İptal Olursa

Vize iptal sebepleri:
- Studienkolleg'den kayıt iptal
- Akademik ilerleme yok (uzun süreli)
- Dönem tekrarı limiti aşıldı

Sonuçlar:
- ⚠️ Türkiye'ye dönüş gerekli
- ⚠️ Yeni başvuru sürecine bel
- ✅ Bazı durumlarda **dil kursu vizesine geçiş** (§16f) — kısa süre daha kalabilirsin

## "Vize Çalıştığım Süre Boşa Mı Gitti?"

❌ Hayır — yatırımının bir kısmı geri kazanılabilir:
- ✅ **Almanca'n ilerledi** (B2-C1 düzeyi)
- ✅ **Türkiye'ye dönüp** ortak programla başvuru
- ✅ **AT, NL, vb.** alternatif ülkelere geçiş
- ✅ **TR'deki uni** kabulü (öğrenim ücretleri DE'ye göre uygun)

## İkinci Şans: Studienkolleg Tekrarı

Bazı koşullar altında ikinci kez Studienkolleg'e kayıt:
- Farklı eyalete başvuru
- 1-2 yıl boşluk sonrası
- Yeni dosya (motivasyon mektubu, gelişim göstergesi)

## Pratik Tavsiye

> Studienkolleg'de bocaladığını fark edersen **erkenden hocayla konuş** — Tutorial saatleri, Nachhilfe (özel ders), arkadaş çalışması. Dönem ortasında düzeltmek dönem sonunda düzeltmekten kolay.

## Plan B Düşüncesi

Studienkolleg başlangıcında **alternatif planın** olsun:
- TR'de mezun olmadıysan TR uni'ne dönüş
- AT, NL, Polonya alternatifleri
- Bachelor değil → Ausbildung veya başka yön
- 1 yıl çalışma + yeniden başvuru

Bağlantılı: [Studienkolleg sonrası uni kabulü](/sss/studienkolleg/studienkolleg-sonrasi-universite-kabulu-garanti-mi)
MD,

            'studienkolleg-yil-icinde-2-donem-mi-aliyor' => <<<'MD'
**Evet — Studienkolleg yılda 2 dönem başlangıç sunar:** Wintersemester (Ekim) ve Sommersemester (Nisan).

## Dönem Yapısı

| Dönem | Başlangıç | Bitiş |
| --- | --- | --- |
| **Wintersemester (WiSe)** | Ekim | Şubat |
| **Yarıyıl ara** | Şubat-Mart | (2-3 hafta) |
| **Sommersemester (SoSe)** | Nisan | Temmuz/Ağustos |
| **Yaz tatili** | Ağustos-Eylül | (~6 hafta) |

## Hangi Dönemden Başlamalı?

### Wintersemester (Önerilen)
✅ Tam **1 yıl döngüsü** — Ekim → Temmuz FSP → Ekim Bachelor
✅ Sınavlar yarıyıl sonunda — daha sistematik
✅ Aufnahmeprüfung Mayıs-Haziran
✅ Daha geniş kontenjan (devlet Studienkolleg'lerinde)

### Sommersemester
⚠️ Nisan başlangıç → Mart FSP → 6 ay boşluk → Ekim Bachelor
⚠️ Boşlukta vize uzatma sıkıntısı
⚠️ Daha az kontenjan
✅ Eğer Wintersemester'ı kaçırdıysan alternatif

## Sınav Dönemleri

### Aufnahmeprüfung
- **WiSe** için: Mayıs-Haziran
- **SoSe** için: Kasım-Aralık

### Feststellungsprüfung
- **WiSe başlayan**: Sonraki yaz (Haziran-Temmuz)
- **SoSe başlayan**: Sonraki kış (Şubat-Mart)

## Vize ile Senkronizasyon

Vize başvurusu Studienkolleg dönem başlangıcına denk getirilmeli:
- **WiSe Studienkolleg** → Vize Eylül-Ekim
- **SoSe Studienkolleg** → Vize Mart-Nisan

Vize randevuları yoğun dönemlerde (yaz) 4-6 ay sürebilir.

## Pratik Tavsiye

> **Wintersemester'a hedef koy.** Tam 1 yıl + ardışık Bachelor → her aşama optimize. SoSe başlayıcılar 1.5 yıl planlamalı.

Bağlantılı: [Studienkolleg eğitimi 1 yıl mı 2 yıl mı?](/sss/studienkolleg/studienkolleg-egitimi-1-yil-mi-2-yil-mi-surer)
MD,

            'studienkolleg-ile-direkt-bachelor-karsilastirmasi-hangisi-avantajli' => <<<'MD'
İki yol da geçerli ama **profiline göre seçim** kritik. Kıyaslayalım:

## Karşılaştırma Tablosu

| Açı | Studienkolleg | Direkt Bachelor |
| --- | --- | --- |
| **Süre** | 1 yıl ek (toplam 4 yıl) | 3 yıl (Bachelor süresi) |
| **Ek maliyet** | ~15K € (Sperrkonto + yaşam) | Yok |
| **Almanca gereği** | B2 başla → C1 çık | C1 (DSH-2) baştan |
| **Bachelor kabul oranı** | Yüksek (FSP HZB statüsü) | Daha rekabetçi |
| **Akademik hazırlık** | Sistematik | Direkt başlar |
| **Bachelor seçimi** | Sınırlı (M/T/W/G-Kurs) | Geniş |
| **Risk** | FSP'de kalma | Bachelor'da kalma |

## Studienkolleg Avantajları

✅ **Almanca'yı sıfırdan C1'e** sistematik götürür
✅ **TR lise eksiklerini** kapatır
✅ **HZB statüsü** sonunda Bachelor kapısı açık
✅ **Sınıf arkadaşları** — Türk öğrenci ağı
✅ **Akademik kültür adaptasyonu**

## Direkt Bachelor Avantajları

✅ **1 yıl kazanırsın**
✅ **~15,000 € tasarruf** (Sperrkonto + yaşam masrafı)
✅ **Doğrudan branş** — vakit kaybı yok
✅ **Daha geniş program seçimi**

## Şu Profilde Studienkolleg Tercih Edilir

- 🇹🇷 TR'de lise mezunu (üni okumamış)
- 🌐 Almanca seviyem **B2 altı**
- 📚 Akademik **özgüven düşük** — sistematik hazırlık istiyor
- 🎯 Bachelor branşım **kesin değil**
- 💰 Aile maddi olarak destekleyebiliyor

## Şu Profilde Direkt Bachelor Tercih Edilir

- 🎓 TR'de **üni 1+ yıl tamamlanmış**
- 🗣️ Almanca **C1 düzeyinde**
- 📊 TestAS yüksek puan
- 🇩🇪 DSD veya PASCH okul mezunu
- 💰 Bütçe sıkı, hız önemli
- 🎯 Branş kesin (mühendislik, bilgisayar)

## Pratik Karar Matrisi

| Senin durumun | Önerilen yol |
| --- | --- |
| TR lise + A2 Almanca + Mühendislik | **Studienkolleg T-Kurs** |
| TR uni 2. sınıf + B2 + Bilgisayar | **Direkt Bachelor** |
| TR lise + DSD-2 + Tıp | **TestAS + Direkt Bachelor** |
| TR lise + A1 + branş belirsiz | **TR'de B1 + Studienkolleg** |
| TR uni 1. sınıf + B1 + Sosyal Bilim | **Studienkolleg G-Kurs** |

## "Studienkolleg Süresinde Ne Kazanırım?"

Bir yılın sonunda:
- ✅ DSH-2 (Almanca C1) — ücretsiz olarak kazanılır
- ✅ Akademik Almanca terminolojisi
- ✅ Alman üni kültürüne tam adaptasyon
- ✅ Sınav formatı + öğrenme stili deneyimi

## Pratik İpucu

> Şüpheliysen **Studienkolleg tercih et**. Riski az, kazanımı çok. Direkt Bachelor seçenlerin %30-40'ı ilk yıl Almanca seviyesi sorunundan yarış kaybediyor.

Bağlantılı: [Hazırlık atlanabilir mi?](/sss/studienkolleg/hazirlik-programini-atlayip-dogrudan-bachelor-basvurusu-mumkun-mu)
MD,

            'studienkolleg-yurdu-var-mi-basvuru-sureci-nasil' => <<<'MD'
**Studienkolleg öğrencileri yurt başvurusu yapabilir** — uni öğrencileriyle aynı haklara sahipler.

## Yurt Tipleri

### 1. Studentenwerk Yurdu
- Resmi öğrenci yurdu
- **300-450 €/ay** (şehre göre)
- Kontenjan dolu — erken başvuru kritik
- Aylık ücrete elektrik+su+internet dahil
- Genelde tek kişilik oda + ortak banyo+mutfak

### 2. WG (Wohngemeinschaft)
- 2-5 kişilik daire paylaşımı
- **350-700 €/ay** (oda)
- WG-Gesucht, ImmoScout, Facebook
- Almanca seviyen B2+ olunca kolay
- Garantör (Bürgschaft) bazen istenir

### 3. Privatzimmer (Özel Oda)
- Aile/ev sahibi yanında oda
- **250-500 €/ay**
- En ucuz seçenek
- Almanca konuşma pratiği ağırlık

## Studentenwerk Yurt Başvurusu

1. **Studienkolleg'in bağlı olduğu Studentenwerk'i bul** (örn: studierendenwerk-berlin.de)
2. Online başvuru formu doldur
3. **Studienkolleg kabul belgesi** + pasaport scan'i yükle
4. Tercih sırasıyla 3-5 yurt seç
5. **3-12 ay bekleme** süresi olabilir
6. Kontenjan açıldığında kabul mailı

## WG Başvurusu

1. **WG-Gesucht.de** veya **ImmoScout24** hesap aç
2. Profil oluştur: kim olduğunu, ne yaptığını yaz (Almanca + İngilizce)
3. İlanları gez, **mesaj yaz** (Almanca tercih)
4. Görüşme (online veya yüz yüze) → **WG-Casting**
5. Seçilirsen sözleşme + Kaution (2-3 aylık kira)

## Önemli Tarih

> **Studienkolleg başlangıcından 3-6 ay önce** yurt başvurusunu yapmaya başla. WiSe Studienkolleg için → Nisan-Mayıs başvuru.

## Vize için Konaklama

Vize başvurusunda **konaklama kanıtı** zorunlu:

| Belge | Açıklama |
| --- | --- |
| **Yurt kabul mektubu** | Resmi yurt onayı |
| **WG sözleşmesi** | Imza atılmış kira sözleşmesi |
| **Otel/Airbnb rezervasyonu** | İlk 1-2 ay için (geçici) |
| **Ev sahibi mektubu** | "Kalıyor" beyanı |

⚠️ Vize randevusunda **yurt henüz yoksa** geçici konaklama yeterli — ilk gün uçtuktan sonra resmi yurt aranır.

## "WG Bulamıyorum" Endişesi

Yaygın endişe ama gerçekçi olarak:
- Studienkolleg'in olduğu şehirde 1-2 ay içinde mutlaka bir yer bulunur
- İlk hafta hostel/Airbnb → bu süreçte yerinde arama
- Türk öğrenci Facebook grupları yardımcı

## Pratik İpucu

> Yurt başvurusunda **Kaution (depozito)** için ek 700-1500 € hazır bulundur. Genelde 2-3 aylık kira tutarı, taşınma anında ödenir.

Bağlantılı: [WG nasıl bulunur?](/sss/yurt) · [Studienkolleg için Sperrkonto](/sss/studienkolleg/studienkolleg-icin-sperrkonto-gerekli-mi)
MD,

            'studienkolleg-giris-icin-lise-diplomasi-cevirisi-yeminli-mi-olmali' => <<<'MD'
**Evet — Studienkolleg başvurusunda lise diploması yeminli tercüme zorunlu**, üniversite başvurularıyla aynı kurallar.

## Zorunlu Belgeler

### Apostil + Tercüme
- 🎓 **Lise diploması** (orijinal/onaylı kopya)
- 📋 **Lise transcript** (4 yıllık not bilgisi)
- 📋 **Mezuniyet sınav puanları** (TYT/AYT)

### Sadece Orijinal (Tercüme/Apostil Yok)
- 🆔 **Pasaport** (vize bitiminden 3+ ay sonrasına geçerli)
- 📷 **Biyometrik fotoğraf**
- 🗣️ **B2 Almanca sertifikası** (zaten Almanca)

## Geçerli Tercüme Tipleri

✅ **Almanya'da yeminli tercüman** — "vereidigter Übersetzer"
- En güvenli, %100 kabul
- Ücret: ~2 €/satır (~80-150 € diploma+transcript)

✅ **Türkiye'de yeminli tercüman** — Noter veya mahkeme yeminli
- Çoğu Studienkolleg kabul eder
- Bazıları DE tercümanı talep eder (özellikle BW, SH)
- Ücret: 50-150 ₺/sayfa

❌ **Geçersiz**:
- Google Translate
- Online çeviri + noter tasdiki
- Apostille olmayan yeminli tercüme

## Apostil Süreci

Lise diploma + transcript için:

1. **İl Milli Eğitim Müdürlüğü** onayı
2. **Valilik İl Yazı İşleri Müdürlüğü** → **Apostil mührü** (1-3 iş günü)
3. **Yeminli tercüman** Apostil mührünü de tercüme eder

⚠️ Önce apostil, sonra tercüme.

## Maliyet Hesabı

| Adım | TR'de | DE'de |
| --- | --- | --- |
| Apostil (lise diploma + transcript) | ~100 ₺ | — |
| Yeminli tercüme (diploma) | ~50-150 ₺ | ~50 € |
| Yeminli tercüme (transcript) | ~200-500 ₺ | ~80-150 € |
| Yeminli tercüme (Apostil mührü) | ~30 ₺ | ~10 € |
| **Toplam** | ~400-700 ₺ | ~150-220 € |

## Çoğaltma Stratejisi

Bir kez apostilli + tercüme edilmiş belgeleri **defalarca kullanırsın**:
- Studienkolleg başvurusu
- Uni-Assist (sonradan Bachelor için)
- Vize başvurusu
- Almanya'da Anmeldung

**2-3 set hazırla** — kopya çoğaltmak ucuz, kayıp riski yüksek.

## Hangi Dilde Tercüme?

✅ **Almanca** — en güvenli, her Studienkolleg kabul
✅ **İngilizce** — bazı uluslararası programlar için yeterli, ama Studienkolleg çoğunlukla Almanca tercih

## Pratik İpucu

> Tercüme yaptırmadan önce **hedef Studienkolleg'in web sayfasını** kontrol et — "Beglaubigte Übersetzung" şartı + hangi dilde olduğu yazılır. Zaman ve para kazanır.

Bağlantılı: [Apostil gerekli mi?](/sss/uni-assist/apostilli-evrak-uni-assist-basvurusu-icin-gerekli-mi)
MD,

            'studienkolleg-kabul-belgesi-vize-icin-yeterli-mi' => <<<'MD'
**Hayır — Studienkolleg kabul belgesi vize için temel belge** ama tek başına yetmez. Yanına başka evraklar gerekiyor.

## Vize Paketi

### Akademik Belgeler
- ✅ **Studienkolleg kabul belgesi** (Zulassung)
- ✅ **Lise diploması** + apostil + yeminli tercüme
- ✅ **Lise transcript** + apostil + yeminli tercüme
- ✅ **B2 Almanca sertifikası** (Goethe/Telc/ÖSD)
- ✅ **Motivasyon mektubu** + CV

### Finansal
- ✅ **Sperrkonto onayı** — 1 yıl × 992 € = **11,904 €**
- ✅ **Sağlık sigortası** — 30K € teminat veya GKV taahhüt

### Kişisel
- ✅ **Pasaport** (geçerlilik vize bitiminden 3+ ay)
- ✅ **2 biyometrik fotoğraf**
- ✅ **Doldurulmuş başvuru formu** (VIDEX)
- ✅ **Vize ücreti** (75 €)

## "Kabul Belgesi Var, Vize Garanti mi?"

❌ Hayır — kabul belgesi bir **zorunlu evrak**, vize kararını **konsolosluk** verir.

Vize değerlendirmesinde:
- Tüm evrak eksiksiz mi?
- Mali durum yeterli mi?
- Motivasyon mantıklı mı?
- Türkiye'ye dönüş niyeti var mı?
- Önceki Schengen sorunu var mı?

## Vize Türü

Studienkolleg vizesi:
- **§16b AufenthG (akademik vize)** — Bachelor hazırlığı dahil
- **1-2 yıl** geçerli (ilk veriliş)
- Bachelor için **otomatik uzatma** (FSP geçince)

## Şartlı Studienkolleg Kabulü Durumu

Bazı uniler **"vorläufige Zulassung"** verir:
- Almanca sertifikan tam yetmedi (B1 ile başvurdun, B2 isteniyor)
- TR diploma değerlendirmesi sürüyor

Bu şartlı kabulle vize başvurusu:
- ✅ Konsolosluk **kabul eder** çoğunlukla
- ⚠️ Süreci hızlandırmak için **kesin kabul** önerilir

## Kabul Belgesi Kontrol Listesi

Belgenin üzerinde:
- ✅ Studienkolleg adı + yetkili imzası
- ✅ Senin tam adın + doğum tarihi
- ✅ Kabul edilen kurs (M-Kurs, T-Kurs vb.)
- ✅ Dönem (WiSe veya SoSe)
- ✅ Başlama tarihi
- ✅ Süre (1 yıl)

⚠️ Eksik bilgi varsa Studienkolleg'e başvur ve düzeltilmiş belge iste.

## Konaklama Kanıtı

Vize başvurusunda konaklama da sorulabilir:
- **Studentenwerk yurt** kabul mektubu
- **WG sözleşmesi**
- **Otel/Airbnb rezervasyonu** ilk hafta için
- **Tanıdık beyan mektubu**

## Pratik Tavsiye

> Studienkolleg kabul belgesini aldıktan sonra **3-4 hafta içinde** tüm vize evrakını hazırla — yoğun dönemde randevu kuyruğu uzun (3-4 ay), zamanı en iyi kullan.

Bağlantılı: [Studienkolleg vize başvurusu özel mi?](/sss/studienkolleg/studienkolleg-icin-vize-basvurusu-ozel-mi) · [Vize başvurusu kaç gün sürer?](/sss/vize/vize-basvurusu-sonrasi-olumluolumsuz-donus-ortalama-kac-gun-surer)
MD,
        ];
    }
}

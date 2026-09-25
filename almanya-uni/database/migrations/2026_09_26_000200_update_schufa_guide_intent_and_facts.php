<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * Mevcut SCHUFA rehberi (TR/EN/DE) — niyet netleştirme + düzeltme. Slug, başlık, yayın tarihi ve
 * çeviri grubu DEĞİŞMİYOR; redirect/silme yok. Yalnızca gövde, excerpt ve meta description.
 *
 * Niyet ayrımı: bu yazı "SCHUFA nedir, nasıl oluşur, 2026 skoru"; "SCHUFA'sız nasıl ev kiralarım"
 * yeni yazıda (2026_09_26_000100). Eski "alternatifler" bölümü kısaltılıp oraya yönlendiriliyor.
 *
 * Düzeltilen iddialar (kaynak: SCHUFA, DSK V2.0 Ocak 2026):
 *   - BonitätsCheck ev sahibine skor göstermiyor; yalnızca olumsuz ödeme bilgisi olup olmadığını.
 *   - Kira gecikmesi ev sahibince "doğrudan" SCHUFA'ya bildirilmez; olumsuz kayıt için iki ihtar,
 *     ilkinden ≥ 4 hafta, itirazsız alacak ve önceden uyarı şartı var.
 *   - Datenkopie (Art. 15 DSGVO) ücretsiz — "yılda bir kez" anlatımı kaldırıldı; ev sahibine verilmez.
 *   - Yeni SCHUFA skoru (17.03.2026, 100–999, 12 kriter); Basisscore artık yok.
 *
 * Panelden sonradan düzenlenmiş olabileceği için gövde tümden değiştiriliyor: canlı metin
 * 25.09.2026'da okunup bu sürümün temeli yapıldı.
 */
return new class extends Migration
{
    public function up(): void
    {
        $trBody = <<<'MD'
Almanya'ya yeni gelmiş bir Türk öğrenci olarak en büyük dertlerinden biri "SCHUFA" mı? "Daha bir ay oldu, SCHUFA kaydım nereden olacak?" diye mi düşünüyorsun? Ev sahipleri SCHUFA belgesi istiyor, telefon kontratı başvuruları takılıyor, bir de 2026'da SCHUFA skoru tamamen değişti. Bu rehberde SCHUFA'nın ne olduğunu, kayıtların nasıl oluştuğunu, sıfırdan nasıl geçmiş oluşturacağını, yeni skorun nasıl çalıştığını ve ücretsiz Datenkopie ile ücretli BonitätsCheck arasındaki farkı adım adım anlatıyoruz.

> **Son güncelleme: Eylül 2026** · Resmî kaynaklarla doğrulandı: SCHUFA, Alman veri koruma otoritelerinin ortak rehberi (DSK, Ocak 2026).

## Schufa nedir ve öğrenci için neden kritik

Almanya'da "SCHUFA" kelimesini duymamak neredeyse imkânsız. **SCHUFA Holding AG**, Almanya'nın en büyük özel kredi bilgi kuruluşudur (adı tarihsel olarak *Schutzgemeinschaft für allgemeine Kreditsicherung*'dan gelir). Basitçe söylemek gerekirse, şirketlerin kendisine bildirdiği verileri saklar ve bunlardan bir skor hesaplar. Bu bir tür "finansal karne"dir, ama önemli bir fark var: SCHUFA senin maaşını, bakiyeni ya da harcamalarını bilmez; yalnızca kendisine bildirilen verileri bilir.

SCHUFA'da tipik olarak şu bilgiler bulunur:

- Vadesiz hesaplar (Girokonto) ve kredi kartları
- Krediler, leasing ve kefaletler (Bürgschaft)
- Partner şirketler üzerinden bildirilen mobil hat ve benzeri kontratlar
- Şirketlerin senin hakkında yaptığı sorgular (Anfragen)
- Varsa ödeme sorunları (olumsuz kayıtlar)

**Bir öğrenci olarak neden bu kadar önemli?**

- **Ev kiralama:** Özellikle büyük şehirlerde ev sahipleri, kısa listeye aldıkları adaylardan sıklıkla bir SCHUFA belgesi ister. Yasal bir zorunluluk olmasa da, sıkışık piyasada SCHUFA belgesi olan adaylar çoğu zaman tercih edilir.
- **Telefon ve internet kontratları:** Uzun vadeli kontratlarda sağlayıcılar genellikle SCHUFA'ya bakar. Kaydın henüz boşsa ön ödemeli (Prepaid) bir hatla başlamak çoğu zaman en kolay yoldur.
- **Kredi kartı ve krediler:** İleride kredi kartı veya kredi almak istersen SCHUFA verilerin belirleyici olur.
- **Diğer sözleşmeler:** Bazı şirketler başka sözleşmelerde de SCHUFA sorgusu yapabilir.

Kısacası SCHUFA, Almanya'daki finansal güvenilirliğinin bir göstergesidir. Yeni gelen bir öğrenci için onu anlamak, ilk aylardaki pek çok bürokratik sürprizin önüne geçer.

## Almanya'ya yeni geldim, SCHUFA kaydım yok: ilk adımlar

İşte asıl can alıcı nokta! Almanya'ya yeni geldin, belki daha bir ay bile olmadı. Türkiye'deki kredi notun (örneğin Findeks) buraya taşınmaz; SCHUFA yalnızca Almanya'da kendisine bildirilen verileri bilir. Panik yapma, bu durumdan geçen tek kişi sen değilsin: SCHUFA'ya göre insanların yaklaşık %1,5'i hakkında çok az veri var ya da hiç veri yok ve SCHUFA bu grubun içinde açıkça daha önce Almanya'da ekonomik olarak aktif olmamış kişileri, örneğin göçmenleri sayıyor.

En önemli nokta şu: **Boş bir kayıt, olumsuz bir kayıt değildir.** Yeni gelen biri olarak kaydın büyük ihtimalle boştur, kötü değil.

**"Tavuk mu yumurtadan" ikilemini kırmak için ilk adımlar:**

- **Alman banka hesabı aç:** Almanya'ya gelir gelmez yapman gereken ilk işlerden biri bir Girokonto açmaktır. SCHUFA'nın açıkladığı mantığa göre herhangi bir banka ürünü (vadesiz hesap, kredi kartı, leasing, kefalet, kredi) tam bir skor oluşması için yeterli. Adımlar ve pratik engeller için [Almanya'da banka hesabı açma rehberimize](/tr/blog/opening-a-bank-account-in-germany-the-real-obstacles) göz at. Adres kaydını henüz yaptırmadıysan [Anmeldung rehberimiz](/tr/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt) işine yarar.
- **Sperrkonto'yu doğru konumlandır:** Bloke hesap vize başvurusu için bir finansman kanıtıdır. SCHUFA geçmişi oluşturmanın ana yolu ise günlük kullandığın banka ürünleridir. Sperrkonto hakkında ayrıntılar [Sperrkonto rehberimizde](/tr/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and) ve [Sperrkonto aracımızda](/tr/tools/sperrkonto).
- **Küçük ve yönetilebilir kontratlarla başla:** Mobil hat gibi kontratlar partner şirketler üzerinden SCHUFA'ya bildirilebilir. Ödeyemeyeceğin kontratlara girme; önce bütçeni [yaşam maliyeti aracımızla](/tr/tools/cost-of-living) planla.
- **Banka kartını kullan:** Başlangıçta klasik bir kredi kartı almak zor olabilir; hesabına bağlı banka kartı (Debitkarte) günlük hayat için çoğu zaman yeter. "SCHUFA'sız kredi" ya da "SCHUFA'sız kredi kartı" gibi reklamlara karşı temkinli ol; koşullarını ve maliyetlerini dikkatle incele.

**Kira ödemeleri hakkında önemli bir düzeltme:** Kirayı zamanında ödemek elbette çok önemlidir, ama kira ödemeleri genellikle SCHUFA geçmişi oluşturan şey değildir. SCHUFA geçmişini banka ürünleri oluşturur. Ev sahibi de gecikmiş kirayı "doğrudan SCHUFA'ya bildiremez"; olumsuz bir kaydın hangi koşullarda oluşabileceğini aşağıda anlatıyoruz.

Unutma, SCHUFA geçmişi bir anda oluşmaz. Sabırlı ol; doğru ilk adımlarla ilk aylarda sağlam bir temel atabilirsin.

## 2026'dan itibaren yeni SCHUFA skoru

**17 Mart 2026'dan beri** tüketiciler için yeni bir SCHUFA skoru yayında. Eski sistemle ilgili internette dolaşan pek çok bilgi (yüzdelik ölçekler, sektör skorları, "Basisscore") artık güncel değil. SCHUFA'nın açıklamasına göre yeni skorun temel özellikleri şunlar:

| Özellik | Yeni SCHUFA skoru |
|---|---|
| Ölçek | 100–999 |
| Kriter sayısı | 12 kriter |
| Kimin için aynı | Tüketiciler ve şirketler aynı skoru görür |
| Nereden görülür | Ücretsiz SCHUFA hesabı / uygulaması |
| Güncelleme | Üç ayda bir |
| Eski skorlar | Eski sektör skorları ve Basisscore'un yerini aldı |
| Şirketler için geçiş | 2028 sonuna kadar geçiş süresi var |

Ücretsiz SCHUFA hesabına kayıt olurken kimliğin doğrulanır, örneğin elektronik kimlik fonksiyonu (eID) ya da posta ile gelen bir PIN mektubu ile. Şirketlerin geçiş süresi 2028 sonuna kadar sürdüğü için bazı şirketlerin bir süre daha eski skorlarla çalışması mümkün.

### Yeni gelenler için: veri yoksa skor yok

SCHUFA, hakkında veri olmayan kişiler için açık bir mantık açıklıyor:

- **Hiç veri yok:** Skor hesaplanmaz; şirketler "veri yok" bilgisi alır.
- **6 aydan kısa süredir biliniyorsun ve hiçbir banka sözleşmen yok:** Skor yok.
- **6 aydan uzun süredir biliniyorsun ama banka sözleşmen yok:** "Az bilgi" uyarısıyla bir skor.
- **Herhangi bir banka sözleşmen var** (vadesiz hesap, kredi kartı, leasing, kefalet, kredi): tam skor.

Yani Almanya'daki ilk adımın olan Girokonto, SCHUFA açısından da en önemli adımdır. SCHUFA puanını zamanla nasıl güçlendirebileceğine dair kısa cevaplar için [SCHUFA puanımı nasıl yükseltebilirim](/tr/faq/para/almanyada-schufa-puanimi-nasil-yukseltebilirim) sorumuza da bakabilirsin.

## Olumsuz bir SCHUFA kaydı nasıl oluşur

Birçok öğrenci "bir faturayı bir gün geç ödersem SCHUFA'm biter" diye korkar. Gerçek biraz farklı. SCHUFA'ya göre ödeme sorunları yalnızca belirli koşullarda bildirilebilir:

- En az **iki yazılı ihtar (Mahnung)** gönderilmiş olmalı,
- ilk ihtar en az **4 hafta önce** gönderilmiş olmalı,
- alacak **itiraz edilmemiş** olmalı,
- ve müşteri SCHUFA'ya bildirim yapılacağı konusunda **önceden uyarılmış** olmalı.

Kira için de durum budur: Ev sahibi gecikmiş kirayı öylece SCHUFA'ya bildirmez. Ödenmemiş ve itiraz edilmemiş bir alacak, ancak bu resmî süreç üzerinden, örneğin SCHUFA partneri olan bir alacaklı ya da tahsilat şirketi aracılığıyla olumsuz kayda dönüşebilir. Bu yüzden bir ihtar aldığında onu görmezden gelme; alacak haksızsa yazılı olarak itiraz et, haklıysa hemen öde.

**Kayıtlar ne kadar kalır?** SCHUFA'ya göre sorgular 12 ay sonra silinir. Ödenmiş ödeme sorunları genellikle 3 yıl sonra, belirli koşullarda 18 ay sonra silinir.

## Ücretsiz Datenkopie ve ücretli BonitätsCheck: hangisi ne işe yarar

SCHUFA ile ilgili en çok sorulan sorulardan biri: "SCHUFA belgemi nasıl alırım, ücretsiz mi?" Cevap, hangi belgeye ihtiyacın olduğuna bağlı. İki farklı ürün var ve karıştırılmamaları gerekiyor.

### Datenkopie (GDPR Madde 15 kopyası): kendin için, ücretsiz

- **Ne içerir:** SCHUFA'nın senin hakkında sakladığı tüm veriler, son 12 aydaki sorgular ve şirketlere iletilmiş skorlar. SCHUFA'ya göre kişisel bilgin içindir.
- **Ücret:** SCHUFA'dan **ücretsiz**. Bu kopyayı yaklaşık 30 € karşılığında satan aracı siteler var; onlara ihtiyacın yok.
- **Nasıl alınır:** Online olarak ücretsiz SCHUFA hesabın ya da uygulaman üzerinden veya SCHUFA'ya başvurarak talep edebilirsin. Hesap için kimliğin doğrulanır, örneğin eID ya da PIN mektubu ile.
- **Ne işe yarar:** Kayıtlarını kontrol etmek ve hatalı bilgileri fark edip SCHUFA'dan düzeltilmesini istemek için idealdir.
- **Ev sahibine verme:** Datenkopie çok fazla veri içerir. SCHUFA ve DSK, bunu ev sahiplerine vermemeni tavsiye ediyor. Alman veri koruma otoritelerinin ortak rehberine göre (DSK, Ocak 2026) ev sahipleri Datenkopie'yi **talep edemez**. Kendi isteğinle göstermen yasak değil, ama önerilmez.

### BonitätsCheck: ev sahibi için, yaklaşık 30 €

- **Ne içerir:** Ev sahipleri için hazırlanmıştır. Ev sahibine verilen bölüm yalnızca **olumsuz ödeme bilgisi olup olmadığını** gösterir. **Ev sahibine skor gösterilmez.**
- **Formatlar:** BonitätsCheck, ev sahibinin doğrulayabileceği bir kontrol kodu olan dijital bir PDF'tir. Kâğıt versiyonu BonitätsAuskunft ise 2–4 iş gününde postayla gelir; bundan ev sahibine yalnızca ilk sayfayı vermelisin.
- **Ücret:** Yaklaşık 30 €.
- **Yeni gelenler için:** Kaydın boşsa belge genellikle olumsuz kayıt olmadığını gösterir. Yani "kötü" görünmez.
- **Ne zaman verilir:** SCHUFA'ya göre ev sahibinin kredi bilgisini görme hakkı, sen kısa listeye girdiğinde doğar. DSK rehberinde de kredi bilgisi, ev sahibinin seni seçtiği son aşamaya (sözleşmeden kısa süre önce) aittir. İlk ev gezmesinde istenmesi uygun değil.

### Öğrenci olarak hangisini kullanmalısın

| | Datenkopie | BonitätsCheck |
|---|---|---|
| Kimin için | Senin için | Ev sahibi için |
| Ücret | Ücretsiz | Yaklaşık 30 € |
| Skor görünür mü | Evet, iletilen skorlar dahil | Ev sahibinin bölümünde hayır |
| Ev sahibine ver | Hayır | Evet, son aşamada |

**İpucu:** Kendi kayıtlarını düzenli olarak ücretsiz Datenkopie ile kontrol et. Ev başvurusunda belge gerekiyorsa, kısa listeye girdiğinde BonitätsCheck ya da BonitätsAuskunft'un ilk sayfasını ver.

## Schufa olmadan ev bulmak: kısa özet

Almanya'da SCHUFA geçmişi olmadan ev bulmak, özellikle [Berlin](/tr/cities/berlin-q64) ya da [Münih](/tr/cities/munchen-q1726) gibi şehirlerde zorlayıcı olabilir, ama hiçbir yasa kiralama için SCHUFA belgesini zorunlu kılmaz. Öğrenci yurtları genellikle SCHUFA yerine öğrenci olduğuna dair belge ister; WG'ler ve ev sahibinin izniyle yapılan ara kiralamalar da iyi bir başlangıç olabilir. Depozito ve kefalet toplamı için yasal üst sınır, yan giderler hariç 3 aylık kiradır (§ 551 BGB). Henüz SCHUFA geçmişin yoksa ev başvurusu için hangi belgeleri hazırlayabileceğini [burada anlattık](/tr/blog/renting-a-flat-in-germany-without-schufa).

## Yabancı öğrenci olarak SCHUFA kaydını sağlıklı tutmak

SCHUFA kaydın oluşmaya başladığında onu iyi yönetmek önemli. Aşağıdakiler pratik tavsiyelerdir; yeni skorun 12 kriterinin ayrıntıları için ücretsiz SCHUFA hesabındaki açıklamalara bakabilirsin.

- **Ödemelerini zamanında yap ve ihtarları ciddiye al:** Olumsuz kayıt ancak ihtar sürecinden sonra oluşabilir; bu süreci hiç başlatmamak en iyisi. Otomatik ödeme talimatı (Lastschrift) ödemeleri unutma riskini azaltır. İhtar alırsan ya hemen öde ya da alacak haksızsa yazılı olarak itiraz et.
- **Gerçekten ihtiyacın olmayan ürünlere başvurma:** Her kredi veya kredi kartı başvurusu bir sorgu olarak kaydedilir; sorgular 12 ay saklanır. Sadece gerçekten ihtiyacın olduğunda başvur.
- **Kredi kartını akıllıca kullan (varsa):** Limitini zorlama ve borcunu düzenli öde.
- **Adres değişikliklerini bildir:** Taşındığında hem [Anmeldung](/tr/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt) işlemini yap hem de bankana ve sözleşme ortaklarına yeni adresini bildir, böylece yazışmalar ve olası ihtarlar sana ulaşır.
- **Kaydını düzenli kontrol et:** Ücretsiz Datenkopie ile ya da SCHUFA hesabın üzerinden verilerini kontrol et. Hatalı veya güncel olmayan bir bilgi görürsen SCHUFA'dan düzeltilmesini iste.
- **Sabırlı ol:** Almanya'da ne kadar uzun süre düzgün bir banka ilişkin olursa, SCHUFA'nın senin hakkındaki tablosu da o kadar netleşir.

## Sık sorulan sorular

### SCHUFA'yı öğrenci için kısaca nasıl açıklarsınız?

SCHUFA, şirketlerin bildirdiği verileri (banka hesapları, kredi kartları, krediler, bazı kontratlar, sorgular ve varsa ödeme sorunları) saklayan ve bunlardan bir skor hesaplayan Almanya'nın en büyük özel kredi bilgi kuruluşudur. Öğrenci için özellikle ev kiralama, telefon kontratı ve kredi kartı başvurularında önem taşır. Yeni gelen biri olarak kaydının boş olması normaldir ve olumsuz bir kayıt anlamına gelmez.

### Türkiye'deki kredi notum SCHUFA'ya aktarılır mı?

Hayır. SCHUFA yalnızca Almanya'da kendisine bildirilen verileri bilir; Türkiye'deki kredi geçmişin (örneğin Findeks) aktarılmaz. Bu yüzden SCHUFA geçmişini Almanya'da, en başta bir Girokonto açarak oluşturman gerekir. SCHUFA'ya göre herhangi bir banka sözleşmesi tam skor için yeterlidir.

### Datenkopie gerçekten ücretsiz mi?

Evet. GDPR Madde 15 kapsamındaki Datenkopie, SCHUFA'dan ücretsizdir ve online olarak SCHUFA hesabın ya da uygulaman üzerinden veya başvuru yoluyla alınabilir. Bunu yaklaşık 30 € karşılığında satan aracı sitelere ihtiyacın yok. Datenkopie'yi ev sahibine verme; ev sahibi için BonitätsCheck hazırlanmıştır.

### BonitätsCheck ev sahibine SCHUFA skorumu gösterir mi?

Hayır. BonitätsCheck'in ev sahibine verilen bölümü yalnızca olumsuz ödeme bilgisi olup olmadığını gösterir, skor göstermez. Yaklaşık 30 € tutar ve kaydı boş olan yeni gelenlerde genellikle olumsuz kayıt olmadığını gösterir. Ev sahibine bunu kısa listeye girdiğinde vermen yeterli.

### Kirayı zamanında ödemek SCHUFA'mı yükseltir mi?

Genellikle hayır; kira ödemeleri SCHUFA geçmişini oluşturan şey değildir, banka ürünleri oluşturur. Ama kirayı ödememek sorun yaratabilir: İtiraz edilmemiş bir alacak, iki ihtar ve uyarı gibi resmî koşullar yerine geldiğinde bir alacaklı ya da tahsilat şirketi üzerinden olumsuz kayda dönüşebilir.

## Sonuç

SCHUFA ilk başta gözünü korkutabilir, ama mantığı sanıldığından basit: Almanya'da bir banka hesabı açınca geçmişin başlar, ödemelerini düzenli yaparsan olumsuz kayıt oluşmaz, kendi verilerini ücretsiz Datenkopie ile kontrol edersin, ev sahibine ise son aşamada yalnızca BonitätsCheck verirsin. 2026'dan itibaren geçerli yeni skoru da ücretsiz SCHUFA hesabından takip edebilirsin.

Henüz SCHUFA geçmişin yoksa ev başvurusu için hangi belgeleri hazırlayabileceğini [burada anlattık](/tr/blog/renting-a-flat-in-germany-without-schufa). Genel ev arama ipuçları için [konut sayfamıza](/tr/housing) da göz atabilirsin.

## Kaynaklar

1. SCHUFA: Yeni SCHUFA skoru basın bülteni (17.03.2026) — https://www.schufa.de/en/newsroom/press-release/der-neue-schufa-score-transparent-und-leicht-nachzurechnen/index.jsp
2. SCHUFA: Verisi eksik kişiler SCHUFA skoru almaz — https://www.schufa.de/en/newsroom/creditworthiness/fehlende-daten-personen-erhalten-keinen-schufa-score/index.jsp
3. SCHUFA: Veriler ve silme süreleri — https://www.schufa.de/ueber-uns/daten-scoring/daten-schufa/
4. SCHUFA: Datenkopie ve BonitätsCheck farkı — https://www.schufa.de/themenportal/datenkopie-bonitaetscheck/index.jsp
5. SCHUFA: Ev sahipleri için SCHUFA belgesi — https://www.schufa.de/newsroom/bonitaet/schufa-auskunft-vermieter-schnell-einfach/
6. Datenschutzkonferenz (DSK): Kiracı adaylarından bilgi alınmasına ilişkin rehber, Versiyon 2.0 (Ocak 2026) — https://www.datenschutzkonferenz-online.de/media/oh/OH_Einholung_von_Selbstauskuenften_Mietinteressenten_V2.pdf

Bilgiler Eylül 2026'da yukarıdaki kaynaklarla doğrulandı. Hukuki tavsiye değildir.
MD;

        $enBody = <<<'MD'
Are you a new Turkish student in Germany, and "SCHUFA" is one of your biggest worries? Are you thinking, "It's only been a month, how can I have a SCHUFA record?" Landlords ask for SCHUFA documents, phone contract applications get stuck, and on top of that the SCHUFA score was completely overhauled in 2026. This guide explains step by step what SCHUFA is, how entries arise, how to build a history from zero, how the new score works, and the difference between the free Datenkopie and the paid BonitätsCheck.

> **Last updated: September 2026** · Verified against official sources: SCHUFA, joint guidance of Germany's data protection authorities (DSK, January 2026).

## What SCHUFA is and why it matters for students

In Germany, it's almost impossible not to hear the word "SCHUFA". **SCHUFA Holding AG** is Germany's largest private credit agency (the name historically comes from *Schutzgemeinschaft für allgemeine Kreditsicherung*). Simply put, it stores data that companies report to it and calculates a score from that data. Unlike a "financial report card", SCHUFA does not know your salary, your balance or your spending. It only knows what is reported to it.

A SCHUFA file typically contains:

- Current accounts (Girokonto) and credit cards
- Loans, leasing and guarantees (Bürgschaft)
- Mobile and similar contracts reported via partner companies
- Enquiries that companies have made about you (Anfragen)
- Payment problems, if any (negative entries)

**So why does it matter so much for a student?**

- **Renting a flat:** Especially in big cities, landlords often ask shortlisted applicants for a SCHUFA document. It is not a legal requirement, but in a tight market applicants who can show one are often preferred.
- **Phone and internet contracts:** For long-term contracts, providers usually check SCHUFA. If your file is still empty, starting with a prepaid SIM is often the easiest route.
- **Credit cards and loans:** If you want a credit card or a loan later on, your SCHUFA data will be decisive.

Understanding SCHUFA early saves you many surprises in your first months.

## Just arrived in Germany with no SCHUFA record: first steps

You've just arrived, maybe not even a month ago. Your Turkish credit history (for example Findeks) does not transfer; SCHUFA only knows data reported in Germany. Don't panic, you're not the only one: according to SCHUFA, about 1.5% of people have little or no SCHUFA data, and SCHUFA explicitly includes "people who have not previously been economically active in Germany, such as immigrants" in that group.

The key point: **an empty file is not a negative file.** As a newcomer, your record is most likely empty, not bad.

**First steps to break the "chicken or the egg" dilemma:**

- **Open a German bank account:** One of the first things to do after arriving is to open a Girokonto. According to the logic SCHUFA has published, any bank product (current account, credit card, leasing, guarantee, loan) is enough for a full score. For the steps and the practical hurdles, see our [guide to opening a bank account in Germany](/en/blog/opening-a-bank-account-in-germany-the-real-obstacles-en). If you haven't registered your address yet, our [Anmeldung guide](/en/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt-en) will help.
- **Put the Sperrkonto in the right place:** The blocked account is proof of financing for your visa. The main way to build a SCHUFA history is the bank products you use day to day. For details, see our [Sperrkonto guide](/en/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-en) and our [Sperrkonto tool](/en/tools/sperrkonto).
- **Start with small, manageable contracts:** Contracts such as a mobile plan can be reported to SCHUFA via partner companies. Don't sign contracts you can't afford; plan your budget first with our [cost of living tool](/en/tools/cost-of-living).
- **Use your debit card:** A classic credit card can be hard to get at first; the debit card linked to your account (Debitkarte) is usually enough for everyday life. Be careful with ads for "SCHUFA-free loans" or "credit cards without SCHUFA" and check their terms and costs carefully.

**An important correction about rent:** Paying rent on time is of course essential, but rent payments are generally not what builds a SCHUFA history. Bank products are. And a landlord cannot simply "report late rent to SCHUFA"; we explain below under which conditions a negative entry can arise.

## The new SCHUFA score from 2026

**Since 17 March 2026**, a new SCHUFA score has been live for consumers. Much of the information still circulating online about the old system (percentage scales, industry scores, the "Basisscore") is no longer current. According to SCHUFA, the key features of the new score are:

| Feature | New SCHUFA score |
|---|---|
| Scale | 100–999 |
| Criteria | 12 criteria |
| Who sees the same score | Consumers and companies see the same score |
| Where to see it | Free SCHUFA account / app |
| Updates | Quarterly |
| Old scores | Replaces the old industry scores and the Basisscore |
| Transition for companies | Until the end of 2028 |

Registration for the free account verifies your identity, e.g. via the online ID function (eID) or a PIN letter. Because of the transition period, some companies may keep using old scores for a while.

### For newcomers: no data, no score

SCHUFA has published a clear logic for people it has no data on:

- **No data at all:** no score is calculated; companies receive "no data".
- **Known for less than 6 months and no bank contract:** no score.
- **Known for more than 6 months but no bank contract:** a score with a "little information" warning.
- **Any bank contract** (current account, credit card, leasing, guarantee, loan): a full score.

So the Girokonto, your first step in Germany, is also your most important step for SCHUFA. For short answers on strengthening your file over time, see our FAQ [how can I improve my SCHUFA score](/en/faq/para/almanyada-schufa-puanimi-nasil-yukseltebilirim-en).

## How a negative SCHUFA entry arises

Many students fear that one late bill ruins their SCHUFA. According to SCHUFA, payment problems may only be reported under certain conditions:

- at least **two written reminders (Mahnungen)** have been sent,
- the first reminder was sent at least **4 weeks earlier**,
- the claim is **undisputed**,
- and the customer was **warned in advance** that it would be reported to SCHUFA.

The same applies to rent: a landlord does not simply report late rent to SCHUFA. An unpaid, undisputed claim can only end up as a negative entry through this formal process, for example via a creditor or collection agency that is a SCHUFA partner.

**How long do entries stay?** According to SCHUFA, enquiries are deleted after 12 months. Settled payment problems are generally deleted after 3 years, and under certain conditions after 18 months.

## Free Datenkopie vs paid BonitätsCheck: what each is for

"How do I get my SCHUFA report, and is it free?" It depends on which document you need: there are two different products.

### Datenkopie (GDPR Art. 15 copy): for you, free

- **What it contains:** all data SCHUFA stores about you, the enquiries of the last 12 months and the scores that were transmitted to companies. In SCHUFA's words, it is "for your personal information".
- **Cost:** **free** from SCHUFA. Some middlemen sell this copy for around €30; you don't need them.
- **How to get it:** online via your free SCHUFA account or app, or by request to SCHUFA. For the account, your identity is verified, for example via eID or a PIN letter.
- **What it's for:** ideal for checking your file and asking SCHUFA to correct wrong data.
- **Don't give it to landlords:** the Datenkopie contains far too much data. SCHUFA and the DSK advise against handing it to landlords. According to the joint guidance of Germany's data protection authorities (DSK, January 2026), landlords may not **demand** the Datenkopie. It isn't illegal for you to show it voluntarily, but it is not recommended.

### BonitätsCheck: for the landlord, around €30

- **What it contains:** it is made for landlords. The landlord's part only shows **whether negative payment information exists**. **No score is shown to the landlord.**
- **Formats:** the BonitätsCheck is a digital PDF with a verification code the landlord can check. The paper version, the BonitätsAuskunft, arrives by post within 2–4 working days; pass on only page 1 to the landlord.
- **Cost:** around €30.
- **For newcomers:** if your file is empty, it will typically show no negative entries. In other words, it does not look "bad".
- **When to hand it over:** according to SCHUFA, a landlord only has a claim to see credit information once you are shortlisted. In the DSK guidance, too, credit information belongs to the final stage, when the landlord has chosen you (shortly before signing). It is not appropriate to demand it at a first viewing.

### Which one should you use as a student

| | Datenkopie | BonitätsCheck |
|---|---|---|
| For whom | For you | For the landlord |
| Cost | Free | Around €30 |
| Shows a score | Yes, incl. transmitted scores | Not in the landlord's part |
| Give to landlord | No | Yes, at the final stage |

## Finding an apartment without SCHUFA: the short version

Finding a flat without a SCHUFA history can be challenging, especially in cities like [Berlin](/en/cities/berlin-q64) or [Munich](/en/cities/munchen-q1726), but no law makes a SCHUFA report mandatory for renting. Student halls usually ask for proof of study rather than SCHUFA, and shared flats (WGs) or sublets with the main landlord's permission can be a good start. For deposits and guarantees combined, the legal cap is three months' rent excluding operating costs (§ 551 BGB). If you don't have a SCHUFA history yet, we explain [which documents you can prepare for a rental application here](/en/blog/rent-an-apartment-in-germany-without-schufa-en).

## Keeping your SCHUFA record healthy as a foreign student

The points below are practical advice; for details on the 12 criteria of the new score, see your free SCHUFA account.

- **Pay on time and take reminders seriously:** a negative entry can only arise after the reminder process, so the best strategy is never to trigger it. Direct debits (Lastschrift) reduce the risk of forgetting a payment. If you get a reminder, either pay promptly or, if the claim is unjustified, dispute it in writing.
- **Don't apply for products you don't really need:** every loan or credit card application is stored as an enquiry, and enquiries are kept for 12 months. Apply only when you really need something.
- **Report address changes:** when you move, do your [Anmeldung](/en/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt-en) and tell your bank and contract partners your new address, so that letters and any reminders actually reach you.
- **Check your file regularly:** use the free Datenkopie or your SCHUFA account. If you spot wrong or outdated information, ask SCHUFA to correct it.

## Frequently asked questions

### How would you explain SCHUFA to a student in one paragraph?

SCHUFA is Germany's largest private credit agency. It stores data reported by companies (bank accounts, credit cards, loans, some contracts, enquiries and any payment problems) and calculates a score from it. As a newcomer it is normal for your file to be empty, and that does not mean it is negative.

### Does my Turkish credit score transfer to SCHUFA?

No. SCHUFA only knows data reported in Germany; your Turkish credit history (for example Findeks) is not transferred. That's why you need to build a SCHUFA history in Germany, first of all by opening a Girokonto. According to SCHUFA, any bank contract is enough for a full score.

### Is the Datenkopie really free?

Yes. The Datenkopie under Art. 15 GDPR is free from SCHUFA and can be obtained online via your SCHUFA account or app, or by request. Don't give the Datenkopie to landlords; the BonitätsCheck is the document made for them.

### Does the BonitätsCheck show my SCHUFA score to the landlord?

No. The landlord's part of the BonitätsCheck only shows whether negative payment information exists; it shows no score. For newcomers with an empty file it typically shows no negative entries.

### Does paying rent on time improve my SCHUFA?

Generally not: rent payments are not what builds a SCHUFA history; bank products are. But not paying rent can cause trouble: an undisputed claim can become a negative entry via a creditor or collection agency once the formal conditions are met, such as two reminders and a prior warning.

## Conclusion

SCHUFA may look intimidating at first, but the logic is simpler than it seems: your history starts when you open a German bank account, no negative entry arises as long as you pay reliably, you check your own data with the free Datenkopie, and you give a landlord only the BonitätsCheck at the final stage. You can follow the new score, valid since 2026, in your free SCHUFA account.

If you don't have a SCHUFA history yet, we explain [which documents you can prepare for a rental application here](/en/blog/rent-an-apartment-in-germany-without-schufa-en). For general flat-hunting tips, have a look at our [housing page](/en/housing).

## Sources

1. SCHUFA: press release on the new SCHUFA score (17.03.2026) — https://www.schufa.de/en/newsroom/press-release/der-neue-schufa-score-transparent-und-leicht-nachzurechnen/index.jsp
2. SCHUFA: people with missing data receive no SCHUFA score — https://www.schufa.de/en/newsroom/creditworthiness/fehlende-daten-personen-erhalten-keinen-schufa-score/index.jsp
3. SCHUFA: data and deletion periods — https://www.schufa.de/ueber-uns/daten-scoring/daten-schufa/
4. SCHUFA: Datenkopie vs BonitätsCheck — https://www.schufa.de/themenportal/datenkopie-bonitaetscheck/index.jsp
5. SCHUFA: SCHUFA information for landlords — https://www.schufa.de/newsroom/bonitaet/schufa-auskunft-vermieter-schnell-einfach/
6. Datenschutzkonferenz (DSK): guidance on obtaining self-disclosures from prospective tenants, version 2.0 (January 2026) — https://www.datenschutzkonferenz-online.de/media/oh/OH_Einholung_von_Selbstauskuenften_Mietinteressenten_V2.pdf

Information verified against the sources above in September 2026. This is not legal advice.
MD;

        $deBody = <<<'MD'
Sind Sie als neuer türkischer Student in Deutschland, und "SCHUFA" ist eine Ihrer größten Sorgen? Denken Sie: "Es ist erst einen Monat her, woher soll ich einen SCHUFA-Eintrag haben?" Vermieter verlangen SCHUFA-Unterlagen, Handyverträge hängen fest, und obendrein wurde der SCHUFA-Score 2026 grundlegend umgestellt. Dieser Leitfaden erklärt Schritt für Schritt, was die SCHUFA ist, wie Einträge entstehen, wie Sie bei null eine Historie aufbauen, wie der neue Score funktioniert und worin sich die kostenlose Datenkopie vom kostenpflichtigen BonitätsCheck unterscheidet.

> **Zuletzt aktualisiert: September 2026** · Mit offiziellen Quellen geprüft: SCHUFA, Orientierungshilfe der Datenschutzkonferenz (DSK, Januar 2026).

## Was die SCHUFA ist und warum sie für Studierende wichtig ist

In Deutschland ist es fast unmöglich, das Wort "SCHUFA" nicht zu hören. Die **SCHUFA Holding AG** ist die größte private Auskunftei Deutschlands (der Name geht historisch auf die *Schutzgemeinschaft für allgemeine Kreditsicherung* zurück). Einfach ausgedrückt speichert sie Daten, die Unternehmen ihr melden, und berechnet daraus einen Score. Man kann sie sich als eine Art "finanzielles Zeugnis" vorstellen, mit einem wichtigen Unterschied: Die SCHUFA kennt weder Ihr Gehalt noch Ihren Kontostand oder Ihre Ausgaben. Sie kennt nur, was ihr gemeldet wird.

Typischerweise enthält eine SCHUFA-Akte:

- Girokonten und Kreditkarten
- Kredite, Leasing und Bürgschaften
- Handy- und ähnliche Verträge, die über Partnerunternehmen gemeldet werden
- Anfragen, die Unternehmen zu Ihrer Person gestellt haben
- gegebenenfalls Zahlungsstörungen (Negativeinträge)

**Warum ist sie für Studierende so wichtig?**

- **Wohnungssuche:** Gerade in Großstädten verlangen Vermieter von Bewerbern in der engeren Auswahl häufig eine SCHUFA-Unterlage. Gesetzlich vorgeschrieben ist das nicht, aber auf einem angespannten Markt werden Bewerber mit einer solchen Unterlage oft bevorzugt.
- **Telefon- und Internetverträge:** Bei längerfristigen Verträgen prüfen Anbieter in der Regel die SCHUFA. Wenn Ihre Akte noch leer ist, ist eine Prepaid-Karte für den Anfang oft der einfachste Weg.
- **Kreditkarten und Kredite:** Wenn Sie später eine Kreditkarte oder einen Kredit beantragen möchten, sind Ihre SCHUFA-Daten entscheidend.

Wer die SCHUFA früh versteht, erspart sich in den ersten Monaten viele Überraschungen.

## Neu in Deutschland und noch ohne SCHUFA-Eintrag: die ersten Schritte

Hier liegt der entscheidende Punkt! Sie sind gerade erst angekommen, vielleicht ist noch nicht einmal ein Monat vergangen. Ihre türkische Kredithistorie (zum Beispiel Findeks) wird nicht übertragen; die SCHUFA kennt nur Daten, die in Deutschland gemeldet werden. Keine Panik, Sie sind nicht allein: Laut SCHUFA liegen über rund 1,5 % der Menschen kaum oder gar keine Daten vor, und die SCHUFA zählt dazu ausdrücklich Menschen, die in Deutschland bisher nicht wirtschaftlich aktiv waren, etwa Zugewanderte.

Das Wichtigste vorweg: **Eine leere Akte ist keine negative Akte.** Als Neuankömmling ist Ihr Eintrag höchstwahrscheinlich leer, nicht schlecht.

**Erste Schritte, um das "Henne-Ei-Dilemma" zu durchbrechen:**

- **Ein deutsches Girokonto eröffnen:** Eines der ersten Dinge nach Ihrer Ankunft sollte die Eröffnung eines Girokontos sein. Nach der von der SCHUFA veröffentlichten Logik genügt jedes Bankprodukt (Girokonto, Kreditkarte, Leasing, Bürgschaft, Kredit) für einen vollständigen Score. Die Schritte und die praktischen Hürden finden Sie in unserem [Leitfaden zur Kontoeröffnung in Deutschland](/de/blog/opening-a-bank-account-in-germany-the-real-obstacles-de). Falls Sie Ihre Adresse noch nicht angemeldet haben, hilft Ihnen unser [Leitfaden zur Anmeldung](/de/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt-de).
- **Das Sperrkonto richtig einordnen:** Das Sperrkonto ist ein Finanzierungsnachweis für Ihr Visum. Eine SCHUFA-Historie bauen Sie vor allem über die Bankprodukte auf, die Sie im Alltag nutzen. Details finden Sie in unserem [Sperrkonto-Leitfaden](/de/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-de) und im [Sperrkonto-Rechner](/de/tools/sperrkonto).
- **Mit kleinen, überschaubaren Verträgen beginnen:** Verträge wie ein Handytarif können über Partnerunternehmen an die SCHUFA gemeldet werden. Schließen Sie keine Verträge ab, die Sie sich nicht leisten können; planen Sie Ihr Budget vorher mit unserem [Lebenshaltungskosten-Rechner](/de/tools/cost-of-living).
- **Die Debitkarte nutzen:** Eine klassische Kreditkarte ist anfangs oft schwer zu bekommen; die Debitkarte zu Ihrem Konto reicht im Alltag meist aus. Seien Sie vorsichtig bei Werbung für "Kredite ohne SCHUFA" oder "Kreditkarten ohne SCHUFA" und prüfen Sie Bedingungen und Kosten genau.

**Eine wichtige Korrektur zur Miete:** Pünktliche Mietzahlungen sind selbstverständlich wichtig, aber sie sind in der Regel nicht das, was eine SCHUFA-Historie aufbaut. Das tun Bankprodukte. Und ein Vermieter kann verspätete Miete nicht einfach "an die SCHUFA melden"; unter welchen Voraussetzungen ein Negativeintrag entstehen kann, erklären wir weiter unten.

## Der neue SCHUFA-Score ab 2026

**Seit dem 17. März 2026** ist für Verbraucher ein neuer SCHUFA-Score online. Viele Informationen zum alten System, die noch im Netz kursieren (Prozentskalen, Branchenscores, der "Basisscore"), sind nicht mehr aktuell. Laut SCHUFA hat der neue Score folgende Eckpunkte:

| Merkmal | Neuer SCHUFA-Score |
|---|---|
| Skala | 100–999 |
| Kriterien | 12 Kriterien |
| Wer denselben Score sieht | Verbraucher und Unternehmen sehen denselben Score |
| Wo einsehbar | Kostenloser SCHUFA-Account / App |
| Aktualisierung | Vierteljährlich |
| Alte Scores | Ersetzt die bisherigen Branchenscores und den Basisscore |
| Übergang für Unternehmen | Bis Ende 2028 |

Bei der Registrierung für den kostenlosen SCHUFA-Account wird Ihre Identität geprüft, zum Beispiel über die Online-Ausweisfunktion (eID) oder einen per Post versandten PIN-Brief. Da Unternehmen eine Übergangsfrist bis Ende 2028 haben, arbeiten manche von ihnen möglicherweise noch eine Weile mit den alten Scores.

### Für Neuankömmlinge: keine Daten, kein Score

Die SCHUFA hat für Personen, über die ihr keine Daten vorliegen, eine klare Logik veröffentlicht:

- **Gar keine Daten:** Es wird kein Score berechnet; Unternehmen erhalten die Information "keine Daten".
- **Seit weniger als 6 Monaten bekannt und kein Bankvertrag:** kein Score.
- **Seit mehr als 6 Monaten bekannt, aber kein Bankvertrag:** ein Score mit dem Hinweis "wenig Informationen".
- **Irgendein Bankvertrag** (Girokonto, Kreditkarte, Leasing, Bürgschaft, Kredit): ein vollständiger Score.

Das Girokonto, Ihr erster Schritt in Deutschland, ist also auch Ihr wichtigster Schritt für die SCHUFA. Kurze Antworten dazu, wie Sie Ihre Akte mit der Zeit stärken, finden Sie in unserer FAQ [Wie kann ich meinen SCHUFA-Score verbessern](/de/faq/para/almanyada-schufa-puanimi-nasil-yukseltebilirim-de).

## Wie ein negativer SCHUFA-Eintrag entsteht

Viele Studierende fürchten: "Wenn ich eine Rechnung einen Tag zu spät bezahle, ist meine SCHUFA ruiniert." Die Realität sieht anders aus. Laut SCHUFA dürfen Zahlungsstörungen nur unter bestimmten Voraussetzungen gemeldet werden:

- Es wurden mindestens **zwei schriftliche Mahnungen** verschickt,
- die erste Mahnung liegt mindestens **4 Wochen** zurück,
- die Forderung ist **unbestritten**,
- und der Kunde wurde **vorher gewarnt**, dass eine Meldung an die SCHUFA erfolgt.

Das gilt auch für die Miete: Ein Vermieter meldet verspätete Miete nicht einfach an die SCHUFA. Eine unbezahlte, unbestrittene Forderung kann nur über dieses formale Verfahren zu einem Negativeintrag werden, etwa über einen Gläubiger oder ein Inkassounternehmen, das SCHUFA-Vertragspartner ist. Ignorieren Sie daher nie eine Mahnung: Ist die Forderung unberechtigt, widersprechen Sie schriftlich; ist sie berechtigt, zahlen Sie zügig.

**Wie lange bleiben Einträge gespeichert?** Laut SCHUFA werden Anfragen nach 12 Monaten gelöscht. Erledigte Zahlungsstörungen werden in der Regel nach 3 Jahren gelöscht, unter bestimmten Voraussetzungen bereits nach 18 Monaten.

## Kostenlose Datenkopie und kostenpflichtiger BonitätsCheck: wofür was gedacht ist

Eine der häufigsten Fragen lautet: "Wie bekomme ich meine SCHUFA-Auskunft, und ist sie kostenlos?" Die Antwort hängt davon ab, welches Dokument Sie brauchen. Es gibt zwei verschiedene Produkte, die man nicht verwechseln sollte.

### Datenkopie (nach Art. 15 DSGVO): für Sie, kostenlos

- **Inhalt:** alle Daten, die die SCHUFA über Sie speichert, die Anfragen der letzten 12 Monate und die an Unternehmen übermittelten Scores. Laut SCHUFA ist sie für Ihre persönliche Information gedacht.
- **Kosten:** bei der SCHUFA **kostenlos**. Manche Vermittler verkaufen diese Kopie für rund 30 €; die brauchen Sie nicht.
- **So erhalten Sie sie:** online über Ihren kostenlosen SCHUFA-Account oder die App oder per Antrag bei der SCHUFA. Für den Account wird Ihre Identität geprüft, etwa per eID oder PIN-Brief.
- **Wofür:** ideal, um Ihre Daten zu prüfen und falsche Angaben von der SCHUFA berichtigen zu lassen.
- **Nicht an Vermieter geben:** Die Datenkopie enthält viel zu viele Daten. SCHUFA und DSK raten davon ab, sie Vermietern auszuhändigen. Laut Orientierungshilfe der Datenschutzkonferenz (DSK, Januar 2026) dürfen Vermieter die Datenkopie **nicht verlangen**. Verboten ist es nicht, sie freiwillig zu zeigen, empfehlenswert aber auch nicht.

### BonitätsCheck: für den Vermieter, rund 30 €

- **Inhalt:** Er ist für Vermieter gemacht. Der Vermieterteil zeigt nur, **ob Negativmerkmale zu Zahlungen vorliegen**. **Dem Vermieter wird kein Score angezeigt.**
- **Formate:** Der BonitätsCheck ist ein digitales PDF mit einem Verifizierungscode, den der Vermieter prüfen kann. Die Papierversion, die BonitätsAuskunft, kommt innerhalb von 2–4 Werktagen per Post; geben Sie davon nur Seite 1 an den Vermieter weiter.
- **Kosten:** rund 30 €.
- **Für Neuankömmlinge:** Bei einer leeren Akte zeigt er typischerweise keine Negativeinträge. Er sieht also nicht "schlecht" aus.
- **Wann übergeben:** Laut SCHUFA hat ein Vermieter erst dann Anspruch auf Bonitätsinformationen, wenn Sie in der engeren Auswahl sind. Auch nach der DSK-Orientierungshilfe gehören Bonitätsauskünfte in die letzte Phase, wenn sich der Vermieter für Sie entschieden hat (kurz vor Vertragsschluss). Bei einer ersten Besichtigung ist eine solche Forderung nicht angebracht.

### Welche Unterlage Sie als Studierender nutzen sollten

| | Datenkopie | BonitätsCheck |
|---|---|---|
| Für wen | Für Sie | Für den Vermieter |
| Kosten | Kostenlos | Rund 30 € |
| Score sichtbar | Ja, inkl. übermittelter Scores | Nicht im Vermieterteil |
| An Vermieter geben | Nein | Ja, in der letzten Phase |

**Tipp:** Prüfen Sie Ihre eigenen Daten regelmäßig mit der kostenlosen Datenkopie. Wenn eine Wohnungsbewerbung eine Unterlage erfordert, geben Sie den BonitätsCheck (oder Seite 1 der BonitätsAuskunft) ab, sobald Sie in der engeren Auswahl sind.

## Wohnung ohne SCHUFA: kurz zusammengefasst

Eine Wohnung ohne SCHUFA-Historie zu finden, kann besonders in Städten wie [Berlin](/de/cities/berlin-q64) oder [München](/de/cities/munchen-q1726) herausfordernd sein, doch kein Gesetz schreibt eine SCHUFA-Auskunft für die Anmietung vor. Studierendenwohnheime fragen in der Regel eher nach einem Studiennachweis als nach der SCHUFA, und WGs oder Untermieten mit Erlaubnis des Hauptvermieters können ein guter Einstieg sein. Für Kaution und Bürgschaft zusammen gilt die gesetzliche Obergrenze von drei Nettokaltmieten (§ 551 BGB). Wenn Sie noch keine SCHUFA-Historie haben, erklären wir [hier, welche Unterlagen Sie für die Wohnungsbewerbung vorbereiten können](/de/blog/apartment-search-in-germany-without-schufa-history-de).

## Als internationaler Studierender den SCHUFA-Eintrag gesund halten

Sobald sich Ihre SCHUFA-Akte zu füllen beginnt, lohnt es sich, sie gut zu pflegen. Die folgenden Punkte sind praktische Ratschläge; Einzelheiten zu den 12 Kriterien des neuen Scores finden Sie in den Erläuterungen in Ihrem kostenlosen SCHUFA-Account.

- **Pünktlich zahlen und Mahnungen ernst nehmen:** Ein Negativeintrag kann erst nach dem Mahnverfahren entstehen, die beste Strategie ist also, es gar nicht erst so weit kommen zu lassen. Lastschriften verringern das Risiko, eine Zahlung zu vergessen. Wenn Sie eine Mahnung erhalten, zahlen Sie zügig oder widersprechen Sie schriftlich, falls die Forderung unberechtigt ist.
- **Keine Produkte beantragen, die Sie nicht wirklich brauchen:** Jeder Kredit- oder Kreditkartenantrag wird als Anfrage gespeichert, und Anfragen bleiben 12 Monate gespeichert. Stellen Sie Anträge nur, wenn Sie etwas wirklich benötigen.
- **Kreditkarte klug nutzen (falls vorhanden):** Schöpfen Sie Ihr Limit nicht aus und begleichen Sie offene Beträge regelmäßig.
- **Adressänderungen mitteilen:** Wenn Sie umziehen, erledigen Sie die [Anmeldung](/de/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt-de) und teilen Sie Ihrer Bank und Ihren Vertragspartnern die neue Adresse mit, damit Post und eventuelle Mahnungen Sie auch erreichen.
- **Die eigenen Daten regelmäßig prüfen:** Nutzen Sie die kostenlose Datenkopie oder Ihren SCHUFA-Account. Wenn Sie falsche oder veraltete Angaben entdecken, lassen Sie sie von der SCHUFA berichtigen.

## Häufige Fragen

### Wie lässt sich die SCHUFA für Studierende kurz erklären?

Die SCHUFA ist die größte private Auskunftei Deutschlands. Sie speichert Daten, die Unternehmen melden (Bankkonten, Kreditkarten, Kredite, bestimmte Verträge, Anfragen und gegebenenfalls Zahlungsstörungen), und berechnet daraus einen Score. Für Studierende ist sie vor allem bei der Wohnungssuche, bei Handyverträgen und bei Kreditkartenanträgen wichtig. Als Neuankömmling ist eine leere Akte normal und bedeutet keinen Negativeintrag.

### Wird meine türkische Bonität an die SCHUFA übertragen?

Nein. Die SCHUFA kennt nur Daten, die in Deutschland gemeldet werden; Ihre türkische Kredithistorie (zum Beispiel Findeks) wird nicht übertragen. Deshalb müssen Sie Ihre SCHUFA-Historie in Deutschland aufbauen, zuallererst mit einem Girokonto. Laut SCHUFA genügt irgendein Bankvertrag für einen vollständigen Score.

### Ist die Datenkopie wirklich kostenlos?

Ja. Die Datenkopie nach Art. 15 DSGVO ist bei der SCHUFA kostenlos und online über Ihren SCHUFA-Account oder die App oder per Antrag erhältlich. Geben Sie die Datenkopie nicht an Vermieter weiter; für diesen Zweck gibt es den BonitätsCheck.

### Sieht der Vermieter im BonitätsCheck meinen SCHUFA-Score?

Nein. Der Vermieterteil des BonitätsCheck zeigt nur, ob Negativmerkmale zu Zahlungen vorliegen; ein Score wird nicht angezeigt. Bei Neuankömmlingen mit leerer Akte zeigt er typischerweise keine Negativeinträge.

### Verbessert pünktliche Mietzahlung meine SCHUFA?

In der Regel nicht: Mietzahlungen bauen keine SCHUFA-Historie auf, Bankprodukte schon. Nicht gezahlte Miete kann jedoch Probleme verursachen: Eine unbestrittene Forderung kann über einen Gläubiger oder ein Inkassounternehmen zu einem Negativeintrag werden, wenn die formalen Voraussetzungen erfüllt sind, etwa zwei Mahnungen und eine vorherige Warnung.

## Fazit

Die SCHUFA wirkt anfangs einschüchternd, doch die Logik ist einfacher als gedacht: Ihre Historie beginnt mit einem deutschen Girokonto, solange Sie zuverlässig zahlen, entsteht kein Negativeintrag, Ihre eigenen Daten prüfen Sie mit der kostenlosen Datenkopie, und dem Vermieter geben Sie in der letzten Phase nur den BonitätsCheck. Den seit 2026 geltenden neuen Score können Sie in Ihrem kostenlosen SCHUFA-Account verfolgen.

Wenn Sie noch keine SCHUFA-Historie haben, erklären wir [hier, welche Unterlagen Sie für die Wohnungsbewerbung vorbereiten können](/de/blog/apartment-search-in-germany-without-schufa-history-de). Allgemeine Tipps zur Wohnungssuche finden Sie auf unserer [Wohnen-Seite](/de/housing).

## Quellen

1. SCHUFA: Pressemitteilung zum neuen SCHUFA-Score (17.03.2026) — https://www.schufa.de/en/newsroom/press-release/der-neue-schufa-score-transparent-und-leicht-nachzurechnen/index.jsp
2. SCHUFA: Personen mit fehlenden Daten erhalten keinen SCHUFA-Score — https://www.schufa.de/en/newsroom/creditworthiness/fehlende-daten-personen-erhalten-keinen-schufa-score/index.jsp
3. SCHUFA: Daten und Löschfristen — https://www.schufa.de/ueber-uns/daten-scoring/daten-schufa/
4. SCHUFA: Datenkopie oder BonitätsCheck — https://www.schufa.de/themenportal/datenkopie-bonitaetscheck/index.jsp
5. SCHUFA: SCHUFA-Auskunft für Vermieter — https://www.schufa.de/newsroom/bonitaet/schufa-auskunft-vermieter-schnell-einfach/
6. Datenschutzkonferenz (DSK): Orientierungshilfe zur Einholung von Selbstauskünften bei Mietinteressent:innen, Version 2.0 (Januar 2026) — https://www.datenschutzkonferenz-online.de/media/oh/OH_Einholung_von_Selbstauskuenften_Mietinteressenten_V2.pdf

Die Angaben wurden im September 2026 anhand der oben genannten Quellen geprüft. Keine Rechtsberatung.
MD;

        $updates = [
            'schufa-guide-2026-why-is-credit-score-important-for-turkish-students' => [
                'locale' => 'tr',
                'excerpt' => 'SCHUFA\'yı sıfırdan anlatıyoruz: kayıtlar nasıl oluşur, boş kayıt neden olumsuz değildir, Mart 2026\'dan beri geçerli 100–999 skoru nasıl çalışır ve ücretsiz Datenkopie ile BonitätsCheck arasındaki fark.',
                'meta_description' => 'SCHUFA nedir, kayıt nasıl oluşur, Almanya\'ya yeni gelen öğrenci nasıl geçmiş oluşturur? 2026 yeni skoru, ücretsiz Datenkopie ve BonitätsCheck farkı.',
                'body' => $trBody,
            ],
            'schufa-guide-2026-why-is-credit-score-important-for-turkish-students-en' => [
                'locale' => 'en',
                'excerpt' => 'SCHUFA explained from zero: how entries arise, why an empty file is not a negative one, how the new 100–999 score (live since March 2026) works, and the difference between the free Datenkopie and the BonitätsCheck.',
                'meta_description' => 'What SCHUFA is, how entries arise and how newcomers build a history in Germany. The new 2026 score (100–999) and free Datenkopie vs BonitätsCheck.',
                'body' => $enBody,
            ],
            'schufa-guide-2026-why-is-credit-score-important-for-turkish-students-de' => [
                'locale' => 'de',
                'excerpt' => 'Die SCHUFA von Grund auf erklärt: wie Einträge entstehen, warum eine leere Akte nicht negativ ist, wie der neue Score (100–999, seit März 2026) funktioniert und was Datenkopie und BonitätsCheck unterscheidet.',
                'meta_description' => 'Was die SCHUFA ist, wie Einträge entstehen und wie Neuankömmlinge eine Historie aufbauen. Neuer Score 2026 (100–999), Datenkopie vs. BonitätsCheck.',
                'body' => $deBody,
            ],
        ];

        foreach ($updates as $slug => $u) {
            $post = Post::where('slug', $slug)->where('locale', $u['locale'])->first();
            if (! $post) {
                Log::warning("SCHUFA rehberi güncellemesi: {$slug} bulunamadı, atlandı.");

                continue;
            }
            if ($post->content_md === $u['body']) {
                continue; // zaten güncel — updated_at'a dokunma
            }
            // content_md değişince Post::booted() content_html'i yeniden üretir.
            $post->update([
                'content_md' => $u['body'],
                'excerpt' => $u['excerpt'],
                'meta_description' => $u['meta_description'],
            ]);
        }
    }

    public function down(): void
    {
        // Bilinçli olarak boş: eski metin yanlış olgular içeriyordu.
    }
};

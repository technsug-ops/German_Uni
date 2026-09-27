<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+EN+DE): Almanya'da öğrenci kredisi ve eğitim finansmanı (Studienkredit).
 *
 * Ana mesaj: Almanya'da tek bir "öğrenci kredisi" yok; KfW-Studienkredit, Bildungskredit, BAföG,
 * Studierendenwerk/Darlehenskasse, banka ürünleri, gelir payı modelleri, burs/iş/aile ayrı anlatılır.
 * Erişim ürüne ve oturum statüsüne bağlı: § 16b tek başına KfW/Bildungskredit/BAföG listelerinde ayrı
 * uygun kategori değil; Bildungsinländer ve diğer statüler ayrıca değerlendirilir. Kredi Sperrkonto'nun
 * yerine otomatik geçmez; kredi onayı vize garantisi değildir; Hamburg (Stand 07/2024) yerel örnektir.
 *
 * Faizler resmi kaynaktan yeniden doğrulandı (27.09.2026, KfW Konditionen-Anzeiger "Stand 28.09.2026",
 * KfW Q&A, BVA): KfW 174 değişken 6,34 % (eff. 6,53 %) gültig ab 01.04.2026 — 01.10.2026 değişken oran
 * henüz yayımlanmamıştı; Bildungskredit 173: 3,57 % (eff. 3,53 %) gültig ab 01.04.2026.
 * 2026 Finanzierungsnachweis: 992 €/ay, 11.904 €/yıl; 2027 rakamı verilmedi.
 * Eski/yanlış değerli sayfalara (BAföG yazısı, bafog-alternatives, scholarships 934 €, budget-planner,
 * 120/240 sayfaları, boş SSS'ler) bilinçli olarak link verilmedi.
 * [n] işaretleri Kaynaklar/Sources/Quellen başlığının HeadingPermalink id'sine bağlanır.
 * Yazar: Halil Yaprakli. Kategori: finans.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '054874dc-fc54-481b-ba30-d060d837e5a1';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'finans')->value('id')
            ?? DB::table('categories')->where('slug', 'funding')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Kısa cevap: Almanya'da "öğrenci kredisi" tek bir ürün değildir. KfW Studienkredit, Bildungskredit, BAföG, Studierendenwerk kredileri, banka ürünleri ve gelir payı modelleri farklı kurallarla işler. Hangisine erişebileceğiniz; ürüne, oturum statünüze ve kişisel durumunuza bağlıdır. Öğrenci oturum izniyle (§ 16b AufenthG) Almanya'ya gelen tipik uluslararası öğrenci, KfW'nin ve Bildungskredit'in uygunluk listelerinde ayrı bir kategori olarak sayılmaz. Buna karşılık Bildungsinländer, uzun süredir Almanya'da yaşayan AB vatandaşları ve bazı başka statüler ayrıca değerlendirilir. Bu rehberde her seçeneği, vize ve oturum izni açısından ne anlama geldiğini ve kredi almadan önce neleri kontrol etmeniz gerektiğini anlatıyoruz.

> **Stand: 26.09.2026** · Faiz oranları, uygunluk koşulları ve ürünlerin durumu değişebilir. · Bu yazı genel bilgi amaçlıdır; finansal veya hukuki danışmanlık değildir. · Başvurmadan önce resmi sağlayıcının güncel koşullarını kontrol edin.

## Studienkredit nedir

Studienkredit (öğrenim kredisi), öğrenim süresince yaşam masraflarınızı karşılamak için aldığınız ve **faiziyle birlikte geri ödediğiniz** bir kredidir. Almanya'da bu adla anılan ürünlerin en bilineni devlete ait kalkınma bankası KfW'nin "KfW-Studienkredit" ürünüdür [\[1\]](#content-kaynaklar). Ancak günlük dilde "öğrenci kredisi" denince çoğu zaman birbirinden çok farklı şeyler kastedilir:

- **KfW Studienkredit:** Değişken faizli, gelirden bağımsız, teminatsız bir devlet bankası kredisi [\[1\]](#content-kaynaklar)[\[2\]](#content-kaynaklar).
- **Bildungskredit:** Federal hükümetin, öğreniminin ileri aşamasındaki öğrencilere verdiği, süresi ve tutarı sınırlı kredi [\[5\]](#content-kaynaklar)[\[6\]](#content-kaynaklar).
- **BAföG:** Yükseköğretimde genellikle yarısı hibe, yarısı faizsiz kredi olan devlet desteği [\[9\]](#content-kaynaklar).
- **Studierendenwerk / Darlehenskasse kredileri:** Öğrenci hizmetleri kurumlarının acil durum, köprü veya bitirme kredileri [\[12\]](#content-kaynaklar).
- **Banka ve Sparkasse ürünleri:** Bazı yerel bankaların kendi eğitim kredileri; çoğu banka ise yalnızca KfW kredisine aracılık eder [\[21\]](#content-kaynaklar)[\[37\]](#content-kaynaklar).
- **Gelir payı modelleri (Bildungsfonds):** Mezuniyetten sonra gelirinizin bir yüzdesini belirli bir süre ödediğiniz modeller [\[24\]](#content-kaynaklar).
- **Borç doğurmayan kaynaklar:** Burslar, çalışma, aile desteği ve Verpflichtungserklärung (garantörlük beyanı) [\[34\]](#content-kaynaklar)[\[35\]](#content-kaynaklar)[\[26\]](#content-kaynaklar).

Bu ayrım önemlidir, çünkü her ürünün kime açık olduğu, ne kadar ödediği ve vize/oturum süreçlerinde nasıl değerlendirildiği farklıdır.

## Studienkredit ile BAföG arasındaki fark

Studienkredit ile BAföG aynı şey değildir. BAföG bir yasa (Bundesausbildungsförderungsgesetz) ile düzenlenen devlet desteğidir; yükseköğretimde genellikle yarısı geri ödenmeyen hibe, yarısı faizsiz devlet kredisi olarak verilir [\[9\]](#content-kaynaklar). Studienkredit ise bir kredi sözleşmesidir: Aldığınız tutarı faiziyle birlikte geri ödersiniz [\[1\]](#content-kaynaklar).

| Özellik | BAföG | KfW Studienkredit |
|---|---|---|
| Hukuki dayanak | Yasa (BAföG) | Kredi sözleşmesi |
| Geri ödeme | Genellikle yarısı; kredi kısmı faizsiz | Tamamı, faiziyle |
| Gelir kontrolü | Öğrenci ve aile geliri dikkate alınır | Gelirden bağımsız |
| Yasal hak | Koşulları sağlayana yasal hak | Yasal hak yok, kredi kararı KfW'de |

BAföG'ün kredi kısmının geri ödemesi için yasada ayrıca bir üst sınır ve borç silme kuralı vardır [\[9\]](#content-kaynaklar)[\[10\]](#content-kaynaklar). Studienkredit'te böyle bir sınır yoktur; ödeyeceğiniz toplam tutar faiz oranına ve geri ödeme süresine göre değişir.

## Uluslararası öğrenciler hangi seçeneklere erişebilir

Uluslararası öğrencinin erişimi tek bir kurala değil, **ürüne ve oturum statüsüne** bağlıdır:

- **KfW Studienkredit:** Resmi uygunluk listesinde Alman vatandaşları, belirli koşullarla AB vatandaşları, aile üyeleri ve Bildungsinländer sayılır. Yalnızca § 16b öğrenci oturumuna sahip tipik uluslararası öğrenci ayrı bir uygun kategori olarak sayılmaz [\[1\]](#content-kaynaklar)[\[2\]](#content-kaynaklar).
- **Bildungskredit:** Yabancı uyruklu öğrenciler için § 8 BAföG kuralları uygulanır; bu kurallar daha çok kalıcı veya uzun süreli oturum statülerini kapsar [\[7\]](#content-kaynaklar)[\[8\]](#content-kaynaklar).
- **BAföG:** § 8 BAföG'deki statülere bağlıdır; olağan § 16b statüsü bu listede yeterli bir statü olarak sayılmaz [\[8\]](#content-kaynaklar).
- **Studierendenwerk kredileri:** Kurumdan kuruma değişir. Bazıları tüm uyrukları kabul eder, bazıları AB dışı öğrencilerden kalıcı oturum ister. Pratikteki en büyük engel çoğu zaman Almanya'da yaşayan bir kefildir [\[12\]](#content-kaynaklar)[\[14\]](#content-kaynaklar)[\[15\]](#content-kaynaklar)[\[16\]](#content-kaynaklar).
- **Burslar, çalışma ve aile desteği:** Uyruğa bağlı genel bir yasak yoktur; her programın kendi koşulları vardır [\[26\]](#content-kaynaklar)[\[34\]](#content-kaynaklar)[\[35\]](#content-kaynaklar).

Bu nedenle uluslararası öğrenciler için genel bir "evet" veya "hayır" cevabı yoktur. Önce kendi oturum statünüzü, sonra ürünün uygunluk listesini kontrol edin.

## KfW Studienkredit

KfW Studienkredit (program numarası 174), Almanya'daki devlet veya devletçe tanınmış bir yükseköğretim kurumunda okuyan öğrencilere aylık ödeme yapan bir kredidir [\[1\]](#content-kaynaklar). Temel özellikleri:

- **Teminat istenmez** ve kredi **gelirden bağımsızdır** [\[1\]](#content-kaynaklar)[\[2\]](#content-kaynaklar).
- Buna rağmen KfW'nin bir **kredi değerlendirmesi ve kredi kararı** vardır; krediye **yasal hak yoktur** [\[2\]](#content-kaynaklar).
- Başvuru doğrudan KfW'ye değil, KfW'nin anlaştığı dağıtım ortakları (banka, Sparkasse veya çevrimiçi ortaklar) üzerinden yapılır [\[1\]](#content-kaynaklar).
- Alman olmayan başvuru sahiplerinden ek bir form ve Almanya'daki adres kaydı gibi belgeler istenir [\[2\]](#content-kaynaklar).

## KfW Studienkredit'i kimler alabilir

KfW'nin resmi uygunluk listesinde şu gruplar sayılır [\[1\]](#content-kaynaklar)[\[2\]](#content-kaynaklar):

1. Almanya'da adresi olan **Alman vatandaşları** ve aile üyeleri,
2. Almanya'da en az **üç yıldır** yasal olarak ikamet eden ve kayıtlı olan **AB vatandaşları** ve aile üyeleri,
3. **Bildungsinländer:** Yükseköğretime giriş yeterliliğini (örneğin Abitur) Almanya'da veya yurtdışındaki bir Alman okulunda almış ve Almanya'da kayıtlı adresi olan kişiler.

Bunun yanında bir yaş sınırı vardır: Finansmanın başlamasından önceki 1 Nisan veya 1 Ekim tarihinde en fazla **44 yaşında** olmanız gerekir [\[2\]](#content-kaynaklar).

**AB dışı öğrenciler için not:** Resmi listede yalnızca § 16b öğrenci oturum iznine sahip olmak, ayrı bir uygun kategori olarak geçmez. Bu, "AB dışı öğrenciler KfW'den tamamen dışlanmıştır" anlamına gelmez: Örneğin Almanya'da Abitur almış bir Bildungsinländer veya bir AB vatandaşının aile üyesi olarak uygun olan kişi farklı değerlendirilir. Kendi durumunuzu KfW'nin güncel koşullarıyla ve başvuru yapacağınız ortakla birlikte kontrol edin.

## KfW ödemesi ve süresi

KfW Studienkredit aylık **100 € ile 650 €** arasında ödeme yapar; tutarı kendiniz seçersiniz [\[1\]](#content-kaynaklar)[\[2\]](#content-kaynaklar). Ödeme süresi yaşınıza göre sınırlıdır [\[2\]](#content-kaynaklar):

| Finansman başlangıcındaki yaş | En fazla süre | En fazla toplam ödeme |
|---|---|---|
| 24 yaşa kadar | 14 dönem | 54.600 € |
| 25–34 yaş | 10 dönem | 39.000 € |
| 35–44 yaş | 6 dönem | 23.400 € |

Master ve lisansüstü programlar için ödeme en fazla **6 dönem ve 23.400 €** ile sınırlıdır. İlk başvuru en geç 10. dönemde (Fachsemester) yapılabilir [\[2\]](#content-kaynaklar).

## KfW faizi

KfW Studienkredit'in faizi **değişkendir**. Faiz, 6 aylık EURIBOR ile sabit bir marjdan oluşur ve normalde her yıl **1 Nisan ve 1 Ekim** tarihlerinde yeniden belirlenir [\[2\]](#content-kaynaklar)[\[3\]](#content-kaynaklar).

> **KfW Studienkredit faizi — Stand: 26.09.2026, geçerlilik başlangıcı: 01.04.2026**
> Nominal: **%6,34** · Efektif: **%6,53** [\[3\]](#content-kaynaklar)[\[4\]](#content-kaynaklar)
> 1 Ekim 2026'dan itibaren geçerli olacak değişken faiz, bu yazının hazırlandığı tarihte henüz doğrulanamadı. Güncel oranı mutlaka KfW'nin resmi sayfasından kontrol edin.

Karşılaştırma için: 1 Ekim 2025'ten itibaren geçerli efektif faiz %6,04 idi [\[3\]](#content-kaynaklar). Yani oran altı ay içinde yarım puana yakın değişti. KfW ürün sayfasında gördüğünüz örnek hesaplamalar, güncel ürün faizini değil, bir hesap örneğini gösterir; faizi her zaman "geçerlilik tarihi" ile birlikte okuyun.

Ödeme döneminde birikmiş faiz, **aylık ödemenizden kesilir**. Bu yüzden seçtiğiniz tutarın tamamı hesabınıza geçmeyebilir. Ödeme döneminin ilerleyen aşamasında, KfW koşullarına göre faizin ertelenmesi (Zinsstundung) mümkün olabilir; ertelenen faiz ortadan kalkmaz, daha sonra ödenir [\[2\]](#content-kaynaklar).

## KfW geri ödemesi

- **Karenz dönemi:** Son ödemeden sonra **18–23 ay** süren ödemesiz bir dönem vardır. Bu dönem talep üzerine **6 aya** kadar kısaltılabilir [\[2\]](#content-kaynaklar).
- **Süre:** Geri ödeme en fazla **25 yıl** sürebilir ve en geç 67 yaşında tamamlanmalıdır [\[2\]](#content-kaynaklar).
- **Taksit:** En düşük aylık taksit **20 €**'dur; varsayılan plan **10 yıllık** geri ödemedir [\[2\]](#content-kaynaklar).
- **Sabit faiz:** Geri ödeme döneminde 10 yıla kadar sabit faiz seçeneği vardır [\[2\]](#content-kaynaklar)[\[3\]](#content-kaynaklar).
- **Ek ödeme:** Her aşamada en az **100 €** tutarında ek ödeme yapılabilir [\[2\]](#content-kaynaklar).

Geri ödeme planınızı KfW'nin koşullarına ve kendi gelir beklentinize göre gerçekçi biçimde kurun; ileride geliriniz düşük kalırsa ne yapabileceğinizi sözleşme koşullarından önceden öğrenin.

## Bildungskredit

Bildungskredit, KfW Studienkredit ile karıştırılmamalıdır. Bu, federal hükümetin öğreniminin **ileri aşamasındaki** öğrencilere kısa süreli destek olarak sunduğu ayrı bir programdır (program numarası 173). Başvuruyu **Bundesverwaltungsamt (BVA)** onaylar, ödemeyi **KfW** yapar [\[5\]](#content-kaynaklar)[\[6\]](#content-kaynaklar).

**Kimler başvurabilir:** 18 yaşından itibaren ve **36. yaş gününün olduğu ayın sonuna** kadar; öğreniminin ileri aşamasında olan ve en fazla **12. dönemde** bulunan öğrenciler [\[6\]](#content-kaynaklar). Yabancı uyruklu öğrenciler için **§ 8 BAföG** kuralları uygulanır; bu kurallar kalıcı oturum ve benzeri statüleri kapsar [\[7\]](#content-kaynaklar)[\[8\]](#content-kaynaklar). Yalnızca olağan § 16b öğrenci oturumu, bu listede yeterli bir statü olarak geçmez.

**Tutar:** Aylık **100 €, 200 € veya 300 €**, en fazla **24 ay**; toplam **1.000–7.200 €**. Buna ek olarak tek seferlik **3.600 €**'ya kadar ödeme mümkündür [\[6\]](#content-kaynaklar).

**Faiz:** EURIBOR'a bağlı değişken faiz.
> **Bildungskredit faizi — Stand: 26.09.2026, geçerlilik başlangıcı: 01.04.2026**
> Nominal: **%3,57** · Efektif: **%3,53** [\[5\]](#content-kaynaklar)

**Geri ödeme:** İlk ödemeden **4 yıl sonra**, aylık **120 €** taksitle başlar. Erken geri ödeme ücretsizdir; teminat istenmez [\[5\]](#content-kaynaklar)[\[6\]](#content-kaynaklar).

## BAföG ile kısa karşılaştırma

Bu yazı bir BAföG rehberi değildir; yalnızca karşılaştırma için temel noktaları özetliyoruz:

- Yükseköğretimde BAföG genellikle **yüzde 50 hibe, yüzde 50 faizsiz devlet kredisi** olarak verilir [\[9\]](#content-kaynaklar).
- Kredi kısmının geri ödemesi, azami destek süresinin bitiminden yaklaşık beş yıl sonra başlar; yasa asgari aylık taksiti ve belirli sayıda taksitten sonra kalan borcun silinmesini düzenler [\[10\]](#content-kaynaklar). Kendi durumunuza düşen tutarı her zaman resmi bildirimden (Bescheid) kontrol edin.
- Uluslararası öğrenciler için uygunluk **§ 8 BAföG**'deki statülere bağlıdır. Olağan § 16b öğrenci oturumu bu listede yeterli bir statü olarak sayılmaz; aile, kalıcı oturum veya diğer statüler ayrı değerlendirilir [\[8\]](#content-kaynaklar).
- 12 Ağustos 2026'da kabul edilen bir **kabine taslağı**, 1 Nisan 2027'den itibaren BAföG tutarlarının artırılmasını **planlamaktadır**. Bu değişiklik henüz yürürlüğe girmemiştir [\[11\]](#content-kaynaklar).

## Studierendenwerk kredileri

Studierendenwerk'ler (öğrenci hizmetleri kurumları) ve bunlara bağlı Darlehenskasse'ler (kredi fonları) ayrı bir kategori oluşturur. Tek bir ulusal ürün yoktur. Almanya'daki 58 Studierendenwerk'ten 56'sı, kendi kusuru olmadan maddi sıkıntıya düşen öğrencilere Darlehenskasse üzerinden köprü krediler verir; ancak krediye **yasal hak yoktur** ve genellikle **kefil** istenir [\[12\]](#content-kaynaklar).

Farklı ürün türlerini ayırmak gerekir:

- **Acil durum / tek seferlik kredi:** Beklenmedik, kısa süreli sıkıntılar için küçük tutarlar.
- **Köprü kredisi:** Örneğin BAföG kararı beklenirken aradaki dönemi kapatmak için.
- **Bitirme kredisi (Studienabschlussdarlehen):** Öğreniminin son aşamasındaki öğrenciler için.

İncelediğimiz örnekler (26.09.2026 itibarıyla kurumların kendi sayfalarında):

- **Berlin:** studierendenWERK BERLIN'in kendisi şu anda **kredi vermediğini** belirtir; yalnızca tek seferlik acil destek sunar [\[13\]](#content-kaynaklar). Bundan ayrı, bağımsız bir dernek olan **Studentische Darlehnskasse Berlin e.V.** faizli (%3,95'ten başlayan kademeli faiz) bir öğrenim kredisi sunar; AB dışı öğrencilerden kalıcı oturum ister ve kefil gerekir [\[14\]](#content-kaynaklar). Bu iki kurum birbirine karıştırılmamalıdır.
- **Daka (NRW):** Kuzey Ren-Vestfalya'daki 12 Studierendenwerk'in ortak kredi fonu. **Tüm uyrukları** kabul ettiğini belirtir; faizsizdir ancak **%5 işlem ücreti** alır; toplam en fazla 12.000 €. Kefil gerekir; AB dışı bir kefilin süresiz oturum izni olması gerekir [\[15\]](#content-kaynaklar).
- **Bayern (Darlehenskasse der Bayerischen Studierendenwerke):** Bitirme kredisi; ilk 5 yıl faizsiz, sonrasında yıllık %2; toplam en fazla 18.000 €. AB dışı öğrencilerden süresiz oturum veya yerleşim izni ister (zor durumlar için istisna olabilir); kefil gerekir [\[16\]](#content-kaynaklar).
- **Hamburg:** Tek seferlik kredi (en fazla 1.000 €), bitirme kredisi ve BAföG köprü kredisi; faizsiz, **%1 ücret**; genellikle kefil istenir [\[17\]](#content-kaynaklar).
- **Frankfurt (MainSWerk-Studiendarlehen):** Faizsiz, **%5 işlem ücreti**; toplam en fazla 10.000 €; kefil gerekir [\[18\]](#content-kaynaklar).
- **Köln (KStW):** Beklenmedik ve kusursuz sıkıntılar için faizsiz yardım fonu kredisi (kefil gerekir) ve kefil gerektirmeyen 350 €'luk kısa köprü kredisi; önce diğer finansman kaynaklarının kullanılmış olması beklenir [\[19\]](#content-kaynaklar).
- **Heidelberg:** Öğrenimin bitirme aşaması için, genellikle aylık BAföG tutarının altı katına kadar faizsiz kredi; **%1 ücret**; 500 €'nun altındaki kredilerde kefil istenmez [\[20\]](#content-kaynaklar).

Görüldüğü gibi bazı krediler faizsizdir ama ücret alır, bazıları faizlidir; bazıları uyruk koşulu koyar, bazıları koymaz. Kendi şehrinizdeki Studierendenwerk'in sosyal danışmanlık birimiyle görüşmeden karar vermeyin; birçok kurum başvurudan önce bir danışmanlık görüşmesi şart koşar [\[13\]](#content-kaynaklar)[\[17\]](#content-kaynaklar)[\[19\]](#content-kaynaklar).

## Özel banka kredileri

2026 itibarıyla büyük bankalarda klasik, kendine ait bir öğrenci kredisi bulmak zordur:

- **DKB**, kendi öğrenci kredisini artık sunmadığını açıkça belirtir ve KfW'ye yönlendirir [\[21\]](#content-kaynaklar).
- **Deutsche Bank** ve **Commerzbank**'ın resmi sayfalarında, 26.09.2026 itibarıyla kendilerine ait klasik bir öğrenci kredisi tespit edilemedi. Deutsche Bank'ın bireysel kredi sayfası, düzenli net gelir şartı arar [\[22\]](#content-kaynaklar).
- Pek çok **Sparkasse** ve **Volksbank**, ağırlıklı olarak KfW Studienkredit'e aracılık eder [\[37\]](#content-kaynaklar).
- Bazı yerel bankaların kendi ürünleri vardır. Örneğin Sparkasse Pforzheim Calw'ın S-Bildungskredit ürünü değişken faizlidir ve kredi değerliliği (Bonität) şartı arar [\[23\]](#content-kaynaklar).

Bu yüzden bir Alman bankasının kendine ait bir öğrenci kredisi sunduğunu varsaymayın. Bir banka ürünü bulursanız faiz türünü, toplam maliyeti, teminat/kefil şartını ve uluslararası öğrencilere açık olup olmadığını yazılı olarak sorun.

**Gelir payı modelleri:** Bu modellerde faiz yerine, mezuniyetten sonra belirli bir süre gelirinizin bir yüzdesini ödersiniz. 26.09.2026 itibarıyla **CHANCEN eG** sayfasında "şu anda finansman mümkün değil" ibaresi yer alıyordu; **Deutsche Bildung** sayfasında başvuru açık görünüyor ancak Alman Abitur'u beklendiği belirtiliyor; **Brain Capital** ise AB vatandaşlığı veya kalıcı oturum şartı arıyor [\[24\]](#content-kaynaklar). Bu modellerin durumu sık değişir; başvurmadan önce sağlayıcıya doğrudan sorun.

## SCHUFA ve kredi değerlendirmesi

SCHUFA, Almanya'daki en bilinen kredi kayıt kuruluşudur. Öğrenci kredilerinde SCHUFA'nın rolü ürüne göre değişir:

- KfW'nin incelediğimiz Merkblatt'ında SCHUFA'dan söz edilmez. Ancak bu, kredi kontrolü olmadığı anlamına gelmez: KfW'nin bir **kredi değerlendirmesi ve kredi kararı** vardır [\[2\]](#content-kaynaklar).
- Banka ürünlerinde kredi değerliliği (Bonität) genellikle kontrol edilir [\[23\]](#content-kaynaklar).
- Bazı gelir payı modelleri SCHUFA kaydı oluşturmadığını belirtir [\[24\]](#content-kaynaklar).

Almanya'da henüz SCHUFA geçmişinizin olmaması tek başına otomatik bir ret anlamına gelmez; her sağlayıcı kendi değerlendirmesini yapar. SCHUFA'nın nasıl çalıştığını ve nasıl kayıt oluştuğunu [SCHUFA rehberimizde](/tr/blog/schufa-guide-2026-why-is-credit-score-important-for-turkish-students) anlatıyoruz; kira tarafında bu konunun nasıl yönetildiğini ise [SCHUFA'sız ev kiralama rehberimizde](/tr/blog/renting-a-flat-in-germany-without-schufa) bulabilirsiniz.

## Kefil ve teminat

- **KfW Studienkredit** ve **Bildungskredit** için teminat istenmez [\[2\]](#content-kaynaklar)[\[6\]](#content-kaynaklar).
- **Studierendenwerk kredilerinde** kefil (Bürge) çoğu zaman şarttır [\[12\]](#content-kaynaklar). Örneğin Daka, kefilin 18–70 yaş aralığında olmasını, kalıcı olarak Almanya'da yaşamasını ve haczedilemeyen gelir sınırının üzerinde kazanmasını ister [\[15\]](#content-kaynaklar). Bayern'de kefilin Almanya'da yaşaması ve belirli bir aylık gelire sahip olması gerekir [\[16\]](#content-kaynaklar).
- **Studentische Darlehnskasse Berlin e.V.** belirli tutarların üzerinde iki kefil ister [\[14\]](#content-kaynaklar).

Almanya'da yeni olan uluslararası öğrenciler için, Almanya'da yaşayan ve düzenli geliri olan bir kefil bulmak çoğu zaman uyruk kuralından daha büyük bir engeldir.

## Vize ve oturum izni için finansman kanıtı

Bu bölüm en çok karıştırılan konudur. Ana mesaj: **Öğrenci kredisi, bloke hesabın (Sperrkonto) yerine otomatik olarak geçmez.**

**Yasal çerçeve:** Oturum Yasası'na göre öğrenim amaçlı oturumda geçim, BAföG'ün aylık ihtiyaç tutarına bağlı bir tutarla güvence altında sayılır [\[25\]](#content-kaynaklar). 2026 için bu tutar **ayda 992 €, yılda 11.904 €**'dur [\[28\]](#content-kaynaklar)[\[31\]](#content-kaynaklar). Yetkili makamlar, kabul edilen kanıtları yönetmelikteki "özellikle" (insbesondere) diye başlayan, yani kapalı olmayan bir listeye göre değerlendirir: ebeveyn gelir ve varlık belgeleri, Verpflichtungserklärung, bloke hesap, her yıl yenilenen banka teminatı ve burslar bu listede sayılır [\[27\]](#content-kaynaklar).

**İlk vize başvurusu (Almanya dışından):**
- İncelediğimiz Alman temsilciliği sayfaları (örneğin Fransa'daki Alman temsilcilikleri), olağan bir öğrenci kredisini standart kabul edilen kanıtlar arasında **saymıyor** [\[28\]](#content-kaynaklar).
- Bu, kredinin "yasak" olduğu anlamına gelmez; yalnızca standart listede yer almadığı anlamına gelir.
- Başvurduğunuz **Alman temsilciliğine** (büyükelçilik veya konsolosluk) hangi kanıtı kabul ettiğini mutlaka sorun. DAAD da kanıt biçimini temsilcilikle netleştirmeyi önerir [\[29\]](#content-kaynaklar).

**Almanya'da, § 16b oturum izni verilirken veya uzatılırken:**
- **Hamburg**, yabancılar dairesinin öğrenci bilgilendirme belgesinde, aylık yeterli tutar ödeyen bir banka öğrenci kredisi sözleşmesini kabul edilebilir kanıtlar arasında sayar (Stand: Temmuz 2024) [\[30\]](#content-kaynaklar). Hamburg ayrıca farklı kanıtların **birleştirilebileceğini** belirtir.
- Bu bir **örnektir, ülke genelinde geçerli bir kural değildir.** Berlin'in hizmet sayfası uzatmada son altı ayın hesap dökümlerini ve diğer gelir belgelerini sayar, ancak kredilerden söz etmez [\[31\]](#content-kaynaklar). Başka şehirlerin yabancılar daireleri farklı belgeler isteyebilir.
- Uzatmada finansman kanıtı yeniden istenir; öğrenimde makul bir ilerleme de değerlendirilir [\[26\]](#content-kaynaklar)[\[36\]](#content-kaynaklar).

**Kesinlikle bilmeniz gereken:** Kredi onayı vize veya oturum izni **garantisi değildir.** Karar her zaman yetkili temsilcilik veya yabancılar dairesine aittir; bazı değerlendirmeler takdir yetkisine bağlıdır.

Vize süreci için [2026 öğrenci vizesi rehberimize](/tr/blog/germany-student-visa-2026-application-steps-documents-rejection), vize masraflarını hesaplamak için [vize maliyeti aracımıza](/tr/tools/visa-cost), aile veya tanıdık desteği için [Verpflichtungserklärung rehberimize](/tr/blog/germany-guarantor-declaration-verpflichtungserklarung-guide) bakabilirsiniz.

## Sperrkonto ile öğrenci kredisi arasındaki fark

- **Sperrkonto (bloke hesap)** bir **finansman kanıtı mekanizmasıdır.** Paranın sizde olduğunu gösterir ve ayda yalnızca belirli bir tutarın çekilmesine izin verir. 2026 için gereken tutar yılda 11.904 € (ayda 992 €)'dur [\[28\]](#content-kaynaklar)[\[31\]](#content-kaynaklar).
- **Studienkredit** bir **finansman ürünüdür.** Size borç verir ve bu borcu faiziyle geri ödersiniz.

İkisi aynı şey değildir. Bir kredi, Sperrkonto'ya yatıracağınız parayı sağlamanın bir yolu olabilir, ancak bu, kredinin kendisinin finansman kanıtı olarak kabul edileceği anlamına gelmez. Sperrkonto'nun nasıl işlediğini [Sperrkonto rehberimizde](/tr/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and), uzatma ve burs gibi özel durumları [Sperrkonto kullanım rehberimizde](/tr/blog/sperrkonto-usage-different-visa-and-scholarship-situations-extension-2026) anlatıyoruz. Sağlayıcıları [Sperrkonto karşılaştırma aracımızla](/tr/tools/sperrkonto) inceleyebilirsiniz.

2027 tutarı için henüz doğrulanmış bir resmi rakam yoktur; planlanan BAföG değişiklikleri bu tutarı etkileyebilir.

## Borçlanmanın gerçek maliyeti

Basit bir mekanizma örneği (bir hesaplama değil, yalnızca mantığı göstermek için):

> Bir öğrenci **24 ay boyunca ayda 500 €** almayı planlıyor. Planlanan toplam ödeme: **500 € × 24 = 12.000 €**.

Bu 12.000 €, **geri ödeyeceğiniz toplam tutar değildir.** Çünkü:

- **Faiz işler:** Ödeme döneminde her ay aldığınız toplam borç üzerinden faiz işler.
- **Faiz değişkendir:** KfW faizi yılda iki kez yeniden belirlenir; gelecekteki oranları bugünden bilemezsiniz [\[2\]](#content-kaynaklar)[\[3\]](#content-kaynaklar).
- **Faiz ödemeden kesilebilir:** KfW'de ödeme döneminde faiz aylık ödemenizden düşülür; yani her ay 500 €'nun tamamı hesabınıza geçmeyebilir [\[2\]](#content-kaynaklar).
- **Süre önemlidir:** Geri ödeme ne kadar uzun sürerse, toplam faiz o kadar artar.

Bu yüzden bu yazıda kesin bir "toplam geri ödeme" rakamı vermiyoruz. KfW'nin kendi hesaplama araçlarını ve güncel koşullarını kullanın; ödeme ve geri ödeme planını sözleşme öncesinde yazılı olarak isteyin. Aylık bütçenizi planlamak için [yaşam maliyeti aracımız](/tr/tools/cost-of-living) ve [Almanya'da öğrenci olmanın gerçek maliyeti](/tr/blog/real-cost-of-being-a-student-in-germany-budget-truth) yazımız yardımcı olabilir.

## Değişken faiz riski

Değişken faiz, oranın hem düşebileceği hem de yükselebileceği anlamına gelir. KfW Studienkredit'in efektif faizi 1 Ekim 2025'ten itibaren %6,04 iken, 1 Nisan 2026'dan itibaren %6,53 oldu [\[3\]](#content-kaynaklar). Verbraucherzentrale (tüketici merkezi) da faizin ödeme döneminde her yıl 1 Nisan ve 1 Ekim'de değişebileceğine, geri ödeme döneminde ise belirli koşullarla sabit faiz seçilebileceğine dikkat çeker [\[32\]](#content-kaynaklar).

Bu riski yönetmek için:
- Faiz değişikliklerini düzenli takip edin.
- Geri ödeme döneminde sabit faiz seçeneğinin koşullarını ve maliyetini öğrenin [\[2\]](#content-kaynaklar).
- Ek ödeme imkânlarını ve koşullarını önceden kontrol edin [\[2\]](#content-kaynaklar).

## Avantajlar

- **Gelirden bağımsız ve teminatsız** ürünler vardır (KfW, Bildungskredit) [\[1\]](#content-kaynaklar)[\[6\]](#content-kaynaklar).
- Aylık ödeme tutarı ihtiyacınıza göre ayarlanabilir [\[1\]](#content-kaynaklar).
- Studierendenwerk kredileri çoğu zaman **faizsizdir** (ücret alınabilir) [\[15\]](#content-kaynaklar)[\[17\]](#content-kaynaklar).
- Geri ödeme, mezuniyetten sonra bir karenz dönemiyle başlar [\[2\]](#content-kaynaklar).
- Çalışma saatlerini azaltarak öğrenime odaklanmayı kolaylaştırabilir.

## Riskler

- Faizli kredilerde **geri ödenecek toplam tutar, aldığınız tutardan fazladır.**
- Değişken faiz, toplam maliyeti öngörmeyi zorlaştırır [\[3\]](#content-kaynaklar)[\[32\]](#content-kaynaklar).
- Öğrenim uzarsa veya yarıda kalırsa geri ödeme planı zorlaşabilir; bazı kredi verenler hemen taksit isteyebilir [\[32\]](#content-kaynaklar).
- Kredi, vize veya oturum izni için otomatik olarak finansman kanıtı sayılmaz [\[28\]](#content-kaynaklar)[\[30\]](#content-kaynaklar).
- Kefil gerektiren kredilerde kefilin de sorumluluğu vardır.

Geri ödemede zorlanırsanız, Verbraucherzentrale ve belediyelerin **ücretsiz borç danışmanlık** birimlerine başvurabilirsiniz; yerel danışmanlık merkezlerini meine-schulden.de üzerinden bulabilirsiniz [\[33\]](#content-kaynaklar).

## Alternatifler

- **Deutschlandstipendium:** Aylık **300 €**, geri ödenmez, **tüm uyruklara açıktır** ve gelirden bağımsızdır; başarıya dayalıdır. Başvuru kendi üniversitenize yapılır. Tek başına 992 €'luk finansman kanıtını karşılamaz [\[34\]](#content-kaynaklar).
- **DAAD bursları ve STIBET:** DAAD burs veri tabanında programlar listelenir. STIBET kapsamındaki burslara öğrenciler doğrudan değil, üniversitelerinin International Office'i üzerinden erişir [\[35\]](#content-kaynaklar).
- **Çalışma:** § 16b oturum izniyle yılda **140 tam gün veya 280 yarım gün** çalışabilirsiniz; üniversitedeki öğrenci işleri bu sınıra sayılmaz [\[26\]](#content-kaynaklar). Seçenekleri [HiWi ve Werkstudent karşılaştırmamızda](/tr/blog/hiwi-vs-werkstudent-student-jobs-in-germany), kariyer açısından değerini [Werkstudent rehberimizde](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market) ve B1/B2 Almanca ile staj imkânlarını [staj rehberimizde](/tr/blog/internship-in-germany-with-b1-b2-german) anlatıyoruz.
- **Aile desteği / Verpflichtungserklärung:** Ebeveyn gelir belgeleri veya Almanya'da yaşayan birinin garantörlük beyanı, kabul edilen finansman kanıtları arasındadır [\[27\]](#content-kaynaklar).
- **Studierendenwerk acil yardımları:** Bazı Studierendenwerk'lerin uluslararası öğrenciler için acil durum fonları vardır [\[36\]](#content-kaynaklar).

Almanya'da banka hesabı açma sürecindeki pratik engeller için [banka hesabı rehberimize](/tr/blog/opening-a-bank-account-in-germany-the-real-obstacles), şehir bazlı masraflar için [en ucuz öğrenci şehirleri](/tr/blog/cheapest-student-cities-germany-real-monthly-cost) yazımıza bakabilirsiniz.

## Beş öğrenci senaryosu

Aşağıdaki senaryolar bir ürün önerisi değildir; yalnızca hangi kategorilerin ve kontrollerin gündeme gelebileceğini gösterir.

### Senaryo 1: Türkiye'den ilk öğrenci vizesine başvuran AB dışı öğrenci

- **Olası kategoriler:** Sperrkonto, Verpflichtungserklärung, burs, ebeveyn gelir belgeleri.
- **Uygunluk kontrolü:** Almanya dışından ilk başvuruda KfW ve Bildungskredit'in uygunluk listelerindeki statüler genellikle söz konusu değildir [\[1\]](#content-kaynaklar)[\[7\]](#content-kaynaklar).
- **Kontrol edilecek belgeler:** Temsilciliğin güncel kanıt listesi, 2026 tutarı (yılda 11.904 €) [\[28\]](#content-kaynaklar)[\[31\]](#content-kaynaklar).
- **Risk:** Standart listede olmayan bir kanıtla başvurmak.
- **Kime sorulmalı:** Başvuru yapacağınız Alman temsilciliğine.

### Senaryo 2: Almanya'da § 16b oturumuyla okuyan ve finansman açığı olan öğrenci

- **Olası kategoriler:** Studierendenwerk acil veya köprü kredisi, çalışma, burs, aile desteği, bazı şehirlerde kredi sözleşmesi.
- **Uygunluk kontrolü:** Studierendenwerk'in uyruk ve kefil koşulları; KfW listesinde bu statü ayrı bir kategori olarak geçmez [\[1\]](#content-kaynaklar)[\[12\]](#content-kaynaklar).
- **Kontrol edilecek belgeler:** Uzatmada yabancılar dairenizin istediği finansman kanıtları, öğrenim ilerleme belgeleri [\[30\]](#content-kaynaklar)[\[31\]](#content-kaynaklar).
- **Risk:** Uzatma tarihine yakın bir dönemde kanıtın yetersiz kalması.
- **Kime sorulmalı:** Yerel yabancılar dairesi (Ausländerbehörde) ve Studierendenwerk sosyal danışmanlığı.

### Senaryo 3: En az üç yıldır Almanya'da yaşayan AB vatandaşı

- **Olası kategoriler:** KfW Studienkredit, koşullar sağlanırsa BAföG ve Bildungskredit, burslar.
- **Uygunluk kontrolü:** Yasal ikamet süresi ve adres kaydı, yaş sınırı [\[1\]](#content-kaynaklar)[\[2\]](#content-kaynaklar).
- **Kontrol edilecek belgeler:** Kayıt belgesi (Meldebescheinigung), öğrenci belgesi.
- **Risk:** Değişken faiz ve toplam maliyet.
- **Kime sorulmalı:** KfW dağıtım ortağı; BAföG için yerel BAföG ofisi.

### Senaryo 4: Almanya'da Abitur almış Bildungsinländer

- **Olası kategoriler:** KfW Studienkredit, oturum statüsüne göre BAföG ve Bildungskredit, burslar.
- **Uygunluk kontrolü:** KfW'nin Bildungsinländer tanımı ve Almanya'daki adres kaydı [\[1\]](#content-kaynaklar)[\[2\]](#content-kaynaklar); BAföG için § 8'deki statü [\[8\]](#content-kaynaklar).
- **Kontrol edilecek belgeler:** Abitur belgesi, oturum izni, adres kaydı.
- **Risk:** Oturum statüsü değişirse uygunluğun da değişmesi.
- **Kime sorulmalı:** KfW dağıtım ortağı ve BAföG ofisi.

### Senaryo 5: Mezuniyete yakın, kısa süreli finansman sıkıntısı yaşayan öğrenci

- **Olası kategoriler:** Studierendenwerk bitirme kredisi, Bildungskredit (uygunsa), çalışma, burs.
- **Uygunluk kontrolü:** Kalan dönem sayısı, bitirme kredisinin koşulları, kefil [\[16\]](#content-kaynaklar)[\[17\]](#content-kaynaklar)[\[20\]](#content-kaynaklar).
- **Kontrol edilecek belgeler:** Not dökümü, sınav ve tez takvimi.
- **Risk:** Mezuniyetin gecikmesi ve geri ödemenin iş aramayla çakışması.
- **Kime sorulmalı:** Studierendenwerk sosyal danışmanlığı; not ve sınav kuralları için [not sistemi rehberimiz](/tr/blog/german-university-grading-system-and-exam-retakes), kaydınız sona ererse ne olacağı için [Exmatrikulation rehberimiz](/tr/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back).

## Karşılaştırma tablosu

> **Uygunluk ve koşullar sağlayıcıya, oturum statüsüne ve kişisel duruma göre değişir. Başvurmadan önce resmi sağlayıcıyı kontrol edin.**

| Finansman seçeneği | Sağlayıcı | Geri ödeme gerekir mi | Faiz | Tipik uygunluk | Uluslararası öğrenci erişimi | Ödeme | Geri ödeme başlangıcı | Temel sınırlama |
|---|---|---|---|---|---|---|---|---|
| KfW Studienkredit | KfW (ortaklar üzerinden) | Evet | Değişken; %6,34 nominal / %6,53 efektif (Stand 26.09.2026, 01.04.2026'dan beri) | 44 yaşa kadar; listelenen statüler | Bildungsinländer ve belirli AB/aile statüleri; § 16b ayrı kategori değil | Aylık 100–650 € | 18–23 ay karenz sonrası | Değişken faiz, yasal hak yok |
| Bildungskredit | BVA onayı, KfW ödemesi | Evet | Değişken; %3,57 nominal / %3,53 efektif (Stand 26.09.2026, 01.04.2026'dan beri) | 18–36 yaş, ileri aşama, en fazla 12. dönem | § 8 BAföG'e göre | Aylık 100/200/300 €, en fazla 24 ay | İlk ödemeden 4 yıl sonra | Kısa süreli, sınırlı tutar |
| BAföG | Devlet (BAföG ofisleri) | Genellikle yarısı | Kredi kısmı faizsiz | Gelir ve statü koşulları | § 8 BAföG'e göre | Aylık, ihtiyaca göre | Destek süresinden yaklaşık 5 yıl sonra | Statü ve gelir koşulları |
| Studierendenwerk / Daka | Studierendenwerk'ler | Evet | Çoğu faizsiz; %1–5 ücret olabilir | Kuruma göre | Kuruma göre değişir | Tek seferlik veya aylık | Kuruma göre | Kefil, yasal hak yok |
| Özel banka / Sparkasse | Yerel bankalar | Evet | Genellikle değişken | Bonität, müşteri bölgesi | Ürüne göre, çoğu belirtmez | Ürüne göre | Ürüne göre | Sınırlı sayıda ürün |
| Gelir payı modeli | Bildungsfonds | Evet, gelirden pay | Faiz yerine gelir payı | Programa göre | Çoğu AB veya kalıcı statü arar | Ürüne göre | Belirli gelir eşiğinden sonra | Durumu sık değişir |
| Burs | DAAD, vakıflar, üniversiteler | Hayır | Yok | Başarı, program koşulları | Programa göre, çoğu açık | Programa göre | – | Rekabetçi, sınırlı |
| Çalışma | İşveren | Hayır | Yok | Oturum izni koşulları | Yılda 140 tam / 280 yarım gün | Maaş | – | Zaman ve çalışma sınırı |
| Verpflichtungserklärung | Almanya'daki garantör | Hayır (garantör için yükümlülük) | Yok | Garantörün gelir durumu | Evet | Garantör karşılar | – | Garantörün yasal sorumluluğu |

## Kontrol listesi

1. Oturum statünüzü belirleyin (§ 16b, kalıcı oturum, AB vatandaşlığı, Bildungsinländer).
2. İhtiyacınızı ay ay yazın; ne kadar süre için finansman gerektiğini hesaplayın.
3. Önce borç doğurmayan kaynakları kontrol edin: burslar, iş, aile desteği.
4. KfW, Bildungskredit ve BAföG'ün uygunluk listelerini kendi statünüzle karşılaştırın.
5. Şehrinizdeki Studierendenwerk'in kredi türlerini, uyruk ve kefil koşullarını öğrenin.
6. Faizi her zaman "geçerlilik tarihi" ile birlikte okuyun; değişken mi sabit mi olduğunu kontrol edin.
7. Ödeme döneminde faizin ödemeden kesilip kesilmediğini öğrenin.
8. Geri ödemenin ne zaman başladığını ve en düşük taksiti yazılı olarak isteyin.
9. Vize veya uzatma için kullanmayı düşünüyorsanız, yetkili temsilciliğe veya yabancılar dairesine önceden sorun.
10. Geri ödemede zorlanırsanız ücretsiz borç danışmanlığına başvurun [\[33\]](#content-kaynaklar).

## Sıkça sorulan sorular

### Yabancı öğrenciler Almanya'da KfW öğrenci kredisi alabilir mi?

Duruma bağlıdır. KfW'nin resmi uygunluk listesinde Alman vatandaşları, en az üç yıldır Almanya'da yaşayan AB vatandaşları, aile üyeleri ve Bildungsinländer sayılır. Yalnızca § 16b öğrenci oturum iznine sahip olmak, listede ayrı bir uygun kategori olarak geçmez. Almanya'da Abitur almış veya uygun bir aile statüsüne sahip olanlar ayrıca değerlendirilir.

### Öğrenci kredisi bloke hesabın yerine geçer mi?

Otomatik olarak geçmez. İncelediğimiz Alman temsilciliği sayfaları, ilk vize için öğrenci kredisini standart kabul edilen kanıtlar arasında saymıyor; temsilciliğe sormanız gerekir. Almanya'da uzatmada Hamburg gibi bazı yabancılar daireleri, aylık yeterli ödeme yapan bir kredi sözleşmesini kabul edebilir; bu ülke genelinde geçerli bir kural değildir.

### KfW Studienkredit faizi şu an ne kadar?

26.09.2026 itibarıyla, 01.04.2026'dan beri geçerli oran nominal %6,34, efektif %6,53'tür. Faiz değişkendir ve normalde her yıl 1 Nisan ve 1 Ekim'de yeniden belirlenir. 1 Ekim 2026'dan itibaren geçerli oranı KfW'nin resmi sayfasından kontrol edin.

### Studienkredit ile BAföG arasındaki fark nedir?

BAföG, yasayla düzenlenen ve yükseköğretimde genellikle yarısı hibe, yarısı faizsiz kredi olan bir devlet desteğidir. Studienkredit ise faiziyle birlikte tamamen geri ödenen bir kredi sözleşmesidir. Uygunluk koşulları da farklıdır.

### SCHUFA kaydım yoksa öğrenci kredisi alamaz mıyım?

SCHUFA geçmişinin olmaması tek başına otomatik bir ret anlamına gelmez. KfW'nin incelediğimiz Merkblatt'ında SCHUFA'dan söz edilmez, ancak KfW'nin bir kredi değerlendirmesi vardır. Banka ürünleri genellikle kredi değerliliğini kontrol eder. Her sağlayıcı kendi değerlendirmesini yapar.

### Studierendenwerk kredisi için kefil gerekir mi?

Çoğu zaman evet. Studierendenwerk kredilerinde genellikle Almanya'da yaşayan ve düzenli geliri olan bir kefil istenir. Bazı küçük veya kısa süreli köprü kredilerinde kefil istenmeyebilir. Koşullar kurumdan kuruma değişir ve krediye yasal hak yoktur.

## Kaynaklar

1. KfW, KfW-Studienkredit (174) ürün sayfası — https://www.kfw.de/inlandsfoerderung/Privatpersonen/Studieren-Qualifizieren/F%C3%B6rderprodukte/KfW-Studienkredit-(174)/
2. KfW, Merkblatt KfW-Studienkredit 174 (Stand 11/2023) — https://www.kfw.de/PDF/Download-Center/F%C3%B6rderprogramme-(Inlandsf%C3%B6rderung)/PDF-Dokumente/6000002590_M_174_Studienkredit.PDF
3. KfW, Fragen und Antworten zum KfW-Studienkredit (faiz 01.04.2026) — https://www.kfw.de/%C3%9Cber-die-KfW/Newsroom/Aktuelles/Q-As-Studienkredit.html
4. KfW, Konditionenanzeiger (26.09.2026'da kontrol edildi) — https://www.kfw-formularsammlung.de/KonditionenanzeigerINet/KonditionenAnzeiger
5. KfW, Bildungskredit (173) — https://www.kfw.de/inlandsfoerderung/Privatpersonen/Studieren-Qualifizieren/F%C3%B6rderprodukte/Bildungskredit-(173)/
6. Bundesverwaltungsamt, Was bietet der Bildungskredit — https://www.bva.bund.de/DE/Services/Buerger/Schule-Ausbildung-Studium/Bildungskredit/Antrag/Bildungskredit-Hintergrund/bildungskredit-was-bietet_node.html
7. Bundesverwaltungsamt, Bildungskredit: Staatsangehörigkeit — https://www.bva.bund.de/DE/Services/Buerger/Schule-Ausbildung-Studium/Bildungskredit/Antrag/Studierende/Voraussetzungen/Besondere/Besondere_6.html
8. § 8 BAföG — https://www.gesetze-im-internet.de/baf_g/__8.html
9. § 17 BAföG — https://www.gesetze-im-internet.de/baf_g/__17.html
10. § 18 BAföG — https://www.gesetze-im-internet.de/baf_g/__18.html
11. BMFTR, BAföG-Kabinettsbeschluss (12.08.2026) — https://www.bmftr.bund.de/SharedDocs/Kurzmeldungen/DE/2026/08/120826-bafoeg-kabinettsbeschluss.html
12. Deutsches Studierendenwerk, Darlehenskassen — https://www.studierendenwerke.de/en/topics/student-finance/funding-options/darlehenskassen
13. studierendenWERK BERLIN, Finanzielle Unterstützung in Notlagen — https://www.stw.berlin/beratung/beratung-finanzierung-und-soziales/studienfinanzierung-im-%C3%BCberblick/einmalige-unterst%C3%BCtzungsm%C3%B6glichkeiten-des-studierendenwerks-berlin/finanzielle-unterst%C3%BCtzung-in-notlagen.html
14. Studentische Darlehnskasse Berlin e.V. — https://dakaberlin.de/
15. Daka – Darlehenskasse der Studierendenwerke e.V. — https://daka-darlehensantrag.de/ ; Kölner Studierendenwerk, Daka-Darlehen — https://www.kstw.de/finanzen/weitere-finanzierungsmoeglichkeiten/daka-darlehen/
16. Darlehenskasse der Bayerischen Studierendenwerke, Richtlinien — https://www.darlehenskasse-bayern.de/richtlinien/
17. Studierendenwerk Hamburg, Darlehen für Studierende in finanziellen Notlagen — https://www.stwhh.de/studienfinanzierung/darlehen-fuer-studierende-in-finanziellen-notlagen ; Darlehenskasse — https://www.stwhh.de/studienfinanzierung/darlehenskasse-des-studierendenwerks-hamburg
18. Studierendenwerk Frankfurt am Main, MainSWerk-Studiendarlehen — https://www.swffm.de/beratung-finanzierung/studienfinanzierung/mainswerk-studiendarlehen
19. Kölner Studierendenwerk, Darlehen in Notlagen — https://www.kstw.de/finanzen/darlehen-in-notlagen/
20. Studierendenwerk Heidelberg, Stipendien und Kredite — https://www.stw.uni-heidelberg.de/de/stipendien_und_kredite
21. DKB, Bietet die DKB einen Studienkredit an — https://www.dkb.de/fragen-antworten/bietet-die-dkb-einen-studienkredit-an
22. Deutsche Bank, Privatkredit FAQ — https://www.deutsche-bank.de/pk/service-und-kontakt/services/fragen-antworten/kredit-und-immobilien/werden-auch-privatkredite-an-arbeitslose-auszubildende_-hausfrauen-studenten-und-schueler-vergeben.html ; Commerzbank, KfW-Studienkredit — https://www.commerzbank.de/kredit-finanzierung/produkte/ratenkredite/kfw-studienkredit/
23. Sparkasse Pforzheim Calw, S-Bildungskredit — https://www.sparkasse-pforzheim-calw.de/de/home/privatkunden/kredite-und-finanzierungen/bildungskredit.html
24. CHANCEN eG — https://chancen-eg.de/ ; Deutsche Bildung, FAQ — https://www.deutsche-bildung.de/faq/ ; Brain Capital, FAQ — https://braincapital.de/faq.html
25. § 2 AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__2.html
26. § 16b AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__16b.html
27. Allgemeine Verwaltungsvorschrift zum Aufenthaltsgesetz, Nr. 16.0.8 — https://www.verwaltungsvorschriften-im-internet.de/bsvwvbund_26102009_MI31284060.htm
28. Auswärtiges Amt / Deutsche Vertretungen in Frankreich, Studium — https://allemagneenfrance.diplo.de/fr-de/service/visa/2521298-2521298
29. DAAD / Study in Germany, Finanzierungsnachweis — https://www.study-in-germany.com/de/studium-planen/voraussetzungen/finanzierungsnachweis/
30. Freie und Hansestadt Hamburg, Amt für Migration: Informationen für ausländische Studenten (Stand Juli 2024) — https://www.hamburg.de/resource/blob/91946/1dc75bb6a18abac0cb3dd85da155c1bc/amt-m-m31-informationen-fuer-auslaendische-studenten-2024-deutsch-pdf-data.pdf
31. Service Berlin, Aufenthaltserlaubnis zum Studium — https://service.berlin.de/dienstleistung/305244/
32. Verbraucherzentrale, Studium mit Studienkredit (Stand 18.02.2025) — https://www.verbraucherzentrale.de/wissen/geld-versicherungen/kredit-schulden-insolvenz/studium-mit-studienkredit-82766
33. meine-schulden.de, Schuldnerberatungsstelle finden — https://www.meine-schulden.de/en/finding-help/debt-advice-centers/find-a-debt-counseling-center
34. Deutschlandstipendium, Häufig gestellte Fragen — https://www.deutschlandstipendium.de/deutschlandstipendium/de/studierende/haeufig-gestellte-fragen/haeufig-gestellte-fragen.html
35. DAAD, STIBET I — https://www.daad.de/de/infos-services-fuer-hochschulen/weiterfuehrende-infos-zu-daad-foerderprogrammen/stibet-i/ ; DAAD-Stipendien — https://www.daad.de/de/in-deutschland-studieren/stipendien/daad-stipendien/
36. Deutsches Studierendenwerk, internationale-studierende.de: Finanzierung und Aufenthalt — https://www.internationale-studierende.de/waehrend-des-studiums/finanzierung-und-aufenthalt
37. Sparkasse Oberhessen, KfW-Studienkredit — https://www.sparkasse-oberhessen.de/de/home/privatkunden/kredite-und-finanzierungen/kfw-studienkredit.html ; Volksbanken Raiffeisenbanken, Studienkredit — https://www.vr.de/privatkunden/produkte/kredite/studienkredit.html

*Koşullar 26.09.2026 tarihinde resmi sağlayıcı ve kurum sayfalarında kontrol edildi; faiz oranları ve ürünler değişebilir. Bu yazı genel bilgi amaçlıdır; finansal, hukuki veya vergi danışmanlığı değildir.*
MD;

        $enBody = <<<'MD'
Short answer: in Germany, "student loan" is not one product. The KfW Studienkredit, the Bildungskredit, BAföG, Studierendenwerk loans, bank products and income-share models each follow their own rules. Which of them you can use depends on the product, your residence status and your personal situation. A typical international student who came to Germany on a student residence permit (§ 16b AufenthG) is not listed as a separate eligible category for the KfW Studienkredit or the Bildungskredit. Bildungsinländer, EU citizens who have lived in Germany for some time, and certain other statuses are assessed differently. This guide explains each option, what it means for your visa and residence permit, and what to check before you borrow.

> **Stand (as of): 26.09.2026** · Interest rates, eligibility rules and product availability can change. · General information only; this is not financial or legal advice. · Check the official provider's current conditions before applying.

## What a Studienkredit is

A Studienkredit (study loan) is a loan you take out to cover living costs while you study and that you **repay with interest**. The best-known product under this name is the "KfW-Studienkredit" from KfW, Germany's state-owned development bank [\[1\]](#content-sources). In everyday language, though, "student loan" can mean very different things:

- **KfW Studienkredit:** a variable-rate state-bank loan, independent of income, with no collateral [\[1\]](#content-sources)[\[2\]](#content-sources).
- **Bildungskredit:** a federal loan for students in an advanced stage of study, limited in time and amount [\[5\]](#content-sources)[\[6\]](#content-sources).
- **BAföG:** state support that, in higher education, is generally half grant and half interest-free loan [\[9\]](#content-sources).
- **Studierendenwerk / Darlehenskasse loans:** emergency, bridge or final-phase loans from student services organisations [\[12\]](#content-sources).
- **Bank and Sparkasse products:** a few local banks have their own education loans; many banks only broker the KfW loan [\[21\]](#content-sources)[\[37\]](#content-sources).
- **Income-share models (Bildungsfonds):** after graduating you pay a percentage of your income for a set period [\[24\]](#content-sources).
- **Non-debt sources:** scholarships, work, family support and a Verpflichtungserklärung (formal sponsorship declaration) [\[34\]](#content-sources)[\[35\]](#content-sources)[\[26\]](#content-sources).

The distinction matters because each product differs in who can apply, how much it pays and how it is treated in visa and residence procedures.

## Studienkredit vs BAföG

A Studienkredit and BAföG are not the same thing. BAföG is state support governed by a federal law (the Bundesausbildungsförderungsgesetz); in higher education it is generally paid half as a non-repayable grant and half as an interest-free state loan [\[9\]](#content-sources). A Studienkredit is a loan contract: you repay the amount you received plus interest [\[1\]](#content-sources).

| Feature | BAföG | KfW Studienkredit |
|---|---|---|
| Legal basis | Federal law (BAföG) | Loan contract |
| Repayment | Generally half; loan part interest-free | All of it, with interest |
| Income check | Student's and family's income considered | Independent of income |
| Legal entitlement | Yes, if you meet the conditions | No; KfW makes the credit decision |

The law also sets a repayment cap and a debt-cancellation rule for the BAföG loan part [\[9\]](#content-sources)[\[10\]](#content-sources). A Studienkredit has no such cap: what you repay in total depends on the interest rate and the repayment period.

## Which options international students can access

Access does not follow one rule. It depends on **the product and your residence status**:

- **KfW Studienkredit:** the official eligibility list covers German citizens, EU citizens under certain conditions, family members and Bildungsinländer. A typical international student holding only a § 16b student residence permit is not listed as a separate eligible category [\[1\]](#content-sources)[\[2\]](#content-sources).
- **Bildungskredit:** for non-German students, the rules of § 8 BAföG apply; these mainly cover permanent or long-term residence statuses [\[7\]](#content-sources)[\[8\]](#content-sources).
- **BAföG:** depends on the statuses listed in § 8 BAföG; ordinary § 16b status alone is not listed as sufficient [\[8\]](#content-sources).
- **Studierendenwerk loans:** vary by organisation. Some accept all nationalities, some require non-EU students to hold a permanent residence permit. In practice the biggest hurdle is often a guarantor living in Germany [\[12\]](#content-sources)[\[14\]](#content-sources)[\[15\]](#content-sources)[\[16\]](#content-sources).
- **Scholarships, work and family support:** there is no general nationality ban; each programme has its own conditions [\[26\]](#content-sources)[\[34\]](#content-sources)[\[35\]](#content-sources).

So there is no general yes or no answer for international students. Check your own residence status first, then the product's eligibility list.

## KfW Studienkredit

The KfW Studienkredit (programme number 174) pays a monthly amount to students at state or state-recognised higher education institutions in Germany [\[1\]](#content-sources). Key features:

- **No collateral** is required and the loan is **independent of income** [\[1\]](#content-sources)[\[2\]](#content-sources).
- Even so, KfW carries out a **credit assessment and makes the credit decision**, and there is **no legal entitlement** to the loan [\[2\]](#content-sources).
- You don't apply to KfW directly but through KfW's distribution partners (banks, Sparkassen or online partners) [\[1\]](#content-sources).
- Non-German applicants have to submit an additional form and documents such as proof of registration at a German address [\[2\]](#content-sources).

## Who can get a KfW Studienkredit

KfW's official eligibility list covers [\[1\]](#content-sources)[\[2\]](#content-sources):

1. **German citizens** with an address in Germany, and their family members;
2. **EU citizens** who have lived lawfully in Germany and been registered there for at least **three years**, and their family members;
3. **Bildungsinländer:** people who obtained their university entrance qualification (for example the Abitur) in Germany or at a German school abroad and have a registered address in Germany.

There is also an age limit: you must be no older than **44** on the 1 April or 1 October before your funding starts [\[2\]](#content-sources).

**Note for non-EU students:** holding only a § 16b student residence permit is not listed as a separate eligible category. That doesn't mean non-EU students are excluded across the board. Someone who earned an Abitur in Germany (a Bildungsinländer), or who qualifies as a family member of an EU citizen, is assessed differently. Check your situation against KfW's current conditions and with the partner you apply through.

## KfW payout and duration

The KfW Studienkredit pays between **€100 and €650 a month**; you choose the amount [\[1\]](#content-sources)[\[2\]](#content-sources). How long you can receive payments depends on your age [\[2\]](#content-sources):

| Age when funding starts | Maximum duration | Maximum total payout |
|---|---|---|
| Up to 24 | 14 semesters | €54,600 |
| 25–34 | 10 semesters | €39,000 |
| 35–44 | 6 semesters | €23,400 |

For master's and postgraduate programmes, payouts are limited to **6 semesters and €23,400**. The first application can be made up to the 10th subject semester (Fachsemester) [\[2\]](#content-sources).

## KfW interest

The KfW Studienkredit has a **variable interest rate**. It is made up of the 6-month EURIBOR plus a fixed margin and is normally reset every year on **1 April and 1 October** [\[2\]](#content-sources)[\[3\]](#content-sources).

> **KfW Studienkredit interest rate: Stand 26.09.2026, valid from 01.04.2026**
> Nominal: **6.34%** · Effective: **6.53%** [\[3\]](#content-sources)[\[4\]](#content-sources)
> The variable rate applying from 1 October 2026 had not been verified when this guide was written. Always check the current rate on KfW's official website.

For comparison: the effective rate from 1 October 2025 was 6.04% [\[3\]](#content-sources). So the rate moved by about half a percentage point within six months. Sample calculations on the KfW product page illustrate the maths; they are not the current product rate. Always read a rate together with the date it applies from.

During the payout phase, the interest that builds up is **deducted from your monthly payout**. That means you may not receive the full amount you chose. Later in the payout phase, KfW's conditions may allow you to defer interest (Zinsstundung); deferred interest doesn't disappear, it is paid later [\[2\]](#content-sources).

## KfW repayment

- **Grace period (Karenzphase):** after the last payout there is a repayment-free period of **18–23 months**. You can ask to shorten it to **6 months** [\[2\]](#content-sources).
- **Duration:** repayment can take up to **25 years** and must be completed by age 67 [\[2\]](#content-sources).
- **Instalments:** the minimum monthly instalment is **€20**; the default plan is a **10-year** repayment [\[2\]](#content-sources).
- **Fixed rate:** in the repayment phase you can choose a fixed rate for up to 10 years [\[2\]](#content-sources)[\[3\]](#content-sources).
- **Special repayments:** you can make extra repayments of at least **€100** in every phase [\[2\]](#content-sources).

Build a realistic repayment plan based on KfW's conditions and your expected income. Find out from the contract terms beforehand what you can do if your income turns out lower than planned.

## Bildungskredit

Don't confuse the Bildungskredit with the KfW Studienkredit. It is a separate federal programme (number 173) offering short-term support to students in an **advanced stage** of their studies. The **Bundesverwaltungsamt (BVA)** approves the application and **KfW** pays it out [\[5\]](#content-sources)[\[6\]](#content-sources).

**Who can apply:** students aged 18 up to **the end of the month of their 36th birthday**, in an advanced stage of study and no later than their **12th semester** [\[6\]](#content-sources). For non-German students, the rules of **§ 8 BAföG** apply; these cover permanent residence and similar statuses [\[7\]](#content-sources)[\[8\]](#content-sources). An ordinary § 16b student permit alone is not listed as sufficient.

**Amount:** **€100, €200 or €300 a month** for up to **24 months**, so **€1,000–€7,200** in total. A one-off payment of up to **€3,600** is also possible [\[6\]](#content-sources).

**Interest:** variable, linked to EURIBOR.
> **Bildungskredit interest rate: Stand 26.09.2026, valid from 01.04.2026**
> Nominal: **3.57%** · Effective: **3.53%** [\[5\]](#content-sources)

**Repayment:** starts **4 years after the first payout**, at **€120 a month**. Early repayment is free of charge and no collateral is required [\[5\]](#content-sources)[\[6\]](#content-sources).

## How BAföG compares

This isn't a BAföG guide, so here are only the key points for comparison:

- In higher education, BAföG is generally paid as **50% grant and 50% interest-free state loan** [\[9\]](#content-sources).
- Repayment of the loan part starts about five years after the end of the maximum funding period; the law sets a minimum monthly instalment and cancels the remaining debt after a certain number of instalments [\[10\]](#content-sources). Always check your own figures in your official notice (Bescheid).
- For international students, eligibility depends on the statuses in **§ 8 BAföG**. An ordinary § 16b student permit is not listed as sufficient; family, permanent-residence and other statuses are assessed separately [\[8\]](#content-sources).
- A **cabinet draft** adopted on 12 August 2026 **plans** higher BAföG rates from 1 April 2027. This change has not taken effect [\[11\]](#content-sources).

## Studierendenwerk loans

Studierendenwerke (student services organisations) and their Darlehenskassen (loan funds) are a separate category. There is no single national product. Of the 58 Studierendenwerke in Germany, 56 give bridging loans through a Darlehenskasse to students who face financial hardship through no fault of their own. However, there is **no legal entitlement** and a **guarantor** is usually required [\[12\]](#content-sources).

It helps to tell the product types apart:

- **Emergency / one-off loans:** small amounts for unexpected, short-term hardship.
- **Bridging loans:** for example to cover the gap while a BAföG decision is pending.
- **Final-phase loans (Studienabschlussdarlehen):** for students in the last stage of their studies.

Examples we checked (on the organisations' own pages, as of 26.09.2026):

- **Berlin:** studierendenWERK BERLIN itself states that it **does not currently give loans**; it offers only one-off emergency support [\[13\]](#content-sources). Separately, **Studentische Darlehnskasse Berlin e.V.**, an independent association, offers an interest-bearing study loan (tiered interest starting at 3.95%); it requires non-EU students to hold a permanent residence permit, and a guarantor is needed [\[14\]](#content-sources). Don't mix up these two organisations.
- **Daka (NRW):** the shared loan fund of 12 Studierendenwerke in North Rhine-Westphalia. It says it funds **all nationalities**; the loan is interest-free but carries a **5% administration fee**, up to €12,000 in total. A guarantor is required; a non-EU guarantor needs a permanent residence permit [\[15\]](#content-sources).
- **Bavaria (Darlehenskasse der Bayerischen Studierendenwerke):** a final-phase loan, interest-free for the first 5 years and then 2% a year, up to €18,000 in total. It requires non-EU students to hold an unlimited residence or settlement permit (exceptions possible in hardship cases); a guarantor is required [\[16\]](#content-sources).
- **Hamburg:** a one-off loan (up to €1,000), a final-phase loan and a BAföG bridging loan; interest-free with a **1% fee**; a guarantor is usually required [\[17\]](#content-sources).
- **Frankfurt (MainSWerk-Studiendarlehen):** interest-free with a **5% administration fee**, up to €10,000 in total; a guarantor is required [\[18\]](#content-sources).
- **Cologne (KStW):** an interest-free relief-fund loan for unforeseen hardship through no fault of your own (guarantor required) and a short €350 bridging loan without a guarantor; you are expected to have used other financing sources first [\[19\]](#content-sources).
- **Heidelberg:** an interest-free loan for the final phase of study, usually up to six times the monthly BAföG rate, with a **1% fee**; no guarantor is needed for loans under €500 [\[20\]](#content-sources).

So some loans are interest-free but charge a fee, others charge interest; some set nationality conditions and others don't. Talk to the social counselling service of your local Studierendenwerk before deciding; many require a counselling session before you apply [\[13\]](#content-sources)[\[17\]](#content-sources)[\[19\]](#content-sources).

## Private bank loans

As of 2026, it is hard to find a classic, bank-owned student loan at the big banks:

- **DKB** states clearly that it no longer offers its own student loan and refers students to KfW [\[21\]](#content-sources).
- On the official websites of **Deutsche Bank** and **Commerzbank**, no bank-owned classic student loan could be identified as of 26.09.2026. Deutsche Bank's personal-loan page requires a regular net income [\[22\]](#content-sources).
- Many **Sparkassen** and **Volksbanken** mainly broker the KfW Studienkredit [\[37\]](#content-sources).
- Some local banks have their own products. For example, Sparkasse Pforzheim Calw's S-Bildungskredit has a variable rate and requires creditworthiness (Bonität) [\[23\]](#content-sources).

So don't assume that a German bank will have its own student loan. If you find a bank product, ask in writing about the type of interest, the total cost, any collateral or guarantor requirement, and whether it's open to international students.

**Income-share models:** instead of interest, you pay a percentage of your income for a set period after graduating. As of 26.09.2026, **CHANCEN eG**'s website said that funding is currently not possible; **Deutsche Bildung**'s site appears open to applications but says a German Abitur is expected; **Brain Capital** requires EU citizenship or a permanent residence permit [\[24\]](#content-sources). The status of these models changes often, so ask the provider directly before applying.

## SCHUFA and credit checks

SCHUFA is Germany's best-known credit bureau. Its role in student loans depends on the product:

- KfW does not mention SCHUFA in the checked Merkblatt. KfW still assesses you: it carries out a **credit assessment and makes a credit decision** [\[2\]](#content-sources).
- Bank products usually check creditworthiness (Bonität) [\[23\]](#content-sources).
- Some income-share providers state that they don't create a SCHUFA entry [\[24\]](#content-sources).

Having no SCHUFA history in Germany yet does not in itself mean automatic rejection; each provider makes its own assessment. Our [SCHUFA guide](/en/blog/schufa-guide-2026-why-is-credit-score-important-for-turkish-students-en) explains how SCHUFA works and how a record is built up, and our [guide to renting without SCHUFA](/en/blog/rent-an-apartment-in-germany-without-schufa-en) covers the rental side.

## Guarantors and collateral

- The **KfW Studienkredit** and the **Bildungskredit** require no collateral [\[2\]](#content-sources)[\[6\]](#content-sources).
- **Studierendenwerk loans** often require a guarantor (Bürge) [\[12\]](#content-sources). Daka, for example, requires a guarantor aged 18–70 who lives permanently in Germany and earns above the attachment-free income limit [\[15\]](#content-sources). In Bavaria, the guarantor must live in Germany and have a minimum monthly income [\[16\]](#content-sources).
- **Studentische Darlehnskasse Berlin e.V.** requires two guarantors above a certain loan amount [\[14\]](#content-sources).

For international students who are new to Germany, finding a guarantor who lives in Germany and has a regular income is often a bigger hurdle than any nationality rule.

## Visa and residence permit: proof of financial resources

This is the most commonly misunderstood part. The key point: **a student loan does not automatically replace a blocked account (Sperrkonto).**

**Legal framework:** under the Residence Act, living costs for study-related residence count as secured by an amount linked to the monthly BAföG requirement [\[25\]](#content-sources). For 2026, that amount is **€992 a month, or €11,904 a year** [\[28\]](#content-sources)[\[31\]](#content-sources). Authorities assess acceptable proof against a list in the administrative regulations that begins with "insbesondere" (in particular), so it isn't closed. It names parents' income and assets, a Verpflichtungserklärung, a blocked account, an annually renewed bank guarantee and scholarships [\[27\]](#content-sources).

**First visa (applying from outside Germany):**
- The German mission pages we checked (for example the German missions in France) do **not** list an ordinary student loan among the standard accepted forms of proof [\[28\]](#content-sources).
- That doesn't mean a loan is "forbidden"; it means it isn't on the standard list.
- Ask the **German mission** (embassy or consulate) responsible for you which proof it accepts. DAAD also advises confirming the form of proof with the mission [\[29\]](#content-sources).

**In Germany, when a § 16b permit is issued or extended:**
- **Hamburg**'s immigration office lists, in its information sheet for students, a bank student-loan contract that pays out a sufficient monthly amount as acceptable proof (Stand: July 2024) [\[30\]](#content-sources). Hamburg also says different kinds of proof **can be combined**.
- This is an **example, not a nationwide rule.** Berlin's service page lists bank statements for the last six months and other income for extensions but doesn't mention loans [\[31\]](#content-sources). Immigration offices in other cities may ask for different evidence.
- Proof of funds is requested again at extension, and reasonable academic progress is also taken into account [\[26\]](#content-sources)[\[36\]](#content-sources).

**What you must know:** a loan approval does **not guarantee** a visa or residence permit. The decision always lies with the responsible mission or immigration office, and some assessments are discretionary.

For the visa process, see our [2026 student visa guide](/en/blog/germany-student-visa-2026-application-steps-documents-rejection-en). To estimate visa costs, use our [visa cost tool](/en/tools/visa-cost). For support from family or friends, see our [Verpflichtungserklärung guide](/en/blog/german-sponsorship-letter-verpflichtungserklarung-2026-guide).

## Blocked account vs student loan

- A **Sperrkonto (blocked account)** is a **proof-of-funds mechanism.** It shows the money is available and only lets you withdraw a set amount each month. The amount required for 2026 is €11,904 a year (€992 a month) [\[28\]](#content-sources)[\[31\]](#content-sources).
- A **Studienkredit** is a **financing product.** It lends you money that you repay with interest.

They are not the same thing. A loan might be one way of getting the money you pay into a blocked account, but that doesn't mean the loan itself will be accepted as proof of funds. Our [blocked account guide](/en/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-en) explains how a Sperrkonto works, and our [guide to blocked-account use in different situations](/en/blog/sperrkonto-usage-different-visa-and-scholarship-situations-extension-2026-en) covers extensions and scholarships. You can compare providers with our [blocked account tool](/en/tools/sperrkonto).

No verified official figure exists yet for 2027; the planned BAföG changes may affect it.

## The real cost of borrowing

A simple example of the mechanism (not a calculation, just to show the logic):

> A student plans to receive **€500 a month for 24 months**. Scheduled total payout: **€500 × 24 = €12,000**.

That €12,000 is **not the total amount you will repay.** Here's why:

- **Interest accrues:** during the payout phase, interest is charged each month on the total you've borrowed so far.
- **KfW interest is variable:** the rate is reset twice a year, so you can't know future rates today [\[2\]](#content-sources)[\[3\]](#content-sources).
- **Interest may be deducted from the payout:** with KfW, interest is taken out of your monthly payout during the payout phase, so you may not receive the full €500 each month [\[2\]](#content-sources).
- **The repayment period matters:** the longer you take to repay, the more interest you pay in total.

That's why this guide gives no exact "total repayment" figure. Use KfW's own calculators and current conditions, and ask for the payout and repayment plan in writing before you sign. To plan your monthly budget, our [cost of living tool](/en/tools/cost-of-living) and our article on [the real cost of being a student in Germany](/en/blog/real-cost-student-germany-budget-reality-check) can help.

## Variable-rate risk

A variable rate can go down as well as up. The effective rate for the KfW Studienkredit was 6.04% from 1 October 2025 and 6.53% from 1 April 2026 [\[3\]](#content-sources). The Verbraucherzentrale (consumer advice centre) also points out that the rate can change every 1 April and 1 October during the payout phase, and that a fixed rate can be agreed under certain conditions in the repayment phase [\[32\]](#content-sources).

To manage this risk:
- Keep track of rate changes.
- Find out the terms and cost of the fixed-rate option for the repayment phase [\[2\]](#content-sources).
- Check the options and conditions for extra repayments in advance [\[2\]](#content-sources).

## Advantages

- Some products are **independent of income and need no collateral** (KfW, Bildungskredit) [\[1\]](#content-sources)[\[6\]](#content-sources).
- You can adjust the monthly payout to your needs [\[1\]](#content-sources).
- Studierendenwerk loans are often **interest-free** (fees may apply) [\[15\]](#content-sources)[\[17\]](#content-sources).
- Repayment starts after graduation, following a grace period [\[2\]](#content-sources).
- A loan can make it easier to cut working hours and focus on your studies.

## Risks

- With an interest-bearing loan, **you repay more than you received.**
- A variable rate makes the total cost hard to predict [\[3\]](#content-sources)[\[32\]](#content-sources).
- If your studies take longer or you drop out, repayment can become harder; some lenders may ask for instalments straight away [\[32\]](#content-sources).
- A loan does not automatically count as proof of funds for a visa or residence permit [\[28\]](#content-sources)[\[30\]](#content-sources).
- With loans that need a guarantor, the guarantor also takes on liability.

If you struggle with repayment, you can turn to the **free debt counselling** services run by the Verbraucherzentralen and local authorities; you can find a local advice centre through meine-schulden.de [\[33\]](#content-sources).

## Alternatives

- **Deutschlandstipendium:** **€300 a month**, non-repayable, **open to all nationalities**, independent of income and merit-based. You apply through your own university. On its own it doesn't cover the €992 proof-of-funds requirement [\[34\]](#content-sources).
- **DAAD scholarships and STIBET:** the DAAD scholarship database lists programmes. STIBET scholarships aren't applied for directly; students access them through their university's International Office [\[35\]](#content-sources).
- **Work:** with a § 16b permit you can work **140 full days or 280 half days a year**; student jobs at your university don't count towards this limit [\[26\]](#content-sources). We compare the options in [HiWi vs Werkstudent](/en/blog/hiwi-vs-werkstudent-student-jobs-in-germany-en), explain the career value in our [Werkstudent guide](/en/blog/werkstudent-germany-job-market-experience-grades) and cover internships in our [guide to internships with B1/B2 German](/en/blog/internship-in-germany-with-b1-b2-german-en).
- **Family support / Verpflichtungserklärung:** parents' income documents or a sponsorship declaration from someone living in Germany are among the accepted forms of proof [\[27\]](#content-sources).
- **Studierendenwerk emergency help:** some Studierendenwerke run emergency funds for international students [\[36\]](#content-sources).

For the practical hurdles of opening an account, see our [bank account guide](/en/blog/opening-a-bank-account-in-germany-the-real-obstacles-en); for city-level costs, see [the cheapest student cities in Germany](/en/blog/cheapest-student-cities-germany-real-monthly-cost-en).

## Five student scenarios

These scenarios are not product recommendations. They only show which categories and checks may come up.

### Scenario 1: a non-EU student applying from Türkiye for a first student visa

- **Possible categories:** blocked account, Verpflichtungserklärung, scholarship, parents' income documents.
- **Eligibility check:** for a first application from outside Germany, the statuses on the KfW and Bildungskredit eligibility lists usually don't apply [\[1\]](#content-sources)[\[7\]](#content-sources).
- **Documents to verify:** the mission's current list of accepted proof and the 2026 amount (€11,904 a year) [\[28\]](#content-sources)[\[31\]](#content-sources).
- **Risk:** applying with proof that isn't on the standard list.
- **Confirm with:** the German mission you apply to.

### Scenario 2: a § 16b student already in Germany with a financing gap

- **Possible categories:** Studierendenwerk emergency or bridging loan, work, scholarship, family support, and in some cities a loan contract.
- **Eligibility check:** the Studierendenwerk's nationality and guarantor conditions; this status isn't listed as a separate category for KfW [\[1\]](#content-sources)[\[12\]](#content-sources).
- **Documents to verify:** the proof of funds your immigration office asks for at extension, plus evidence of academic progress [\[30\]](#content-sources)[\[31\]](#content-sources).
- **Risk:** not having enough proof close to your extension date.
- **Confirm with:** your local immigration office (Ausländerbehörde) and the Studierendenwerk's social counselling service.

### Scenario 3: an EU citizen who has lived in Germany for at least three years

- **Possible categories:** KfW Studienkredit, BAföG and Bildungskredit if the conditions are met, scholarships.
- **Eligibility check:** length of lawful residence and registration, age limit [\[1\]](#content-sources)[\[2\]](#content-sources).
- **Documents to verify:** registration certificate (Meldebescheinigung), enrolment certificate.
- **Risk:** variable interest and total cost.
- **Confirm with:** a KfW distribution partner; for BAföG, your local BAföG office.

### Scenario 4: a Bildungsinländer with a German Abitur

- **Possible categories:** KfW Studienkredit, BAföG and Bildungskredit depending on residence status, scholarships.
- **Eligibility check:** KfW's Bildungsinländer definition and a registered German address [\[1\]](#content-sources)[\[2\]](#content-sources); for BAföG, the status under § 8 [\[8\]](#content-sources).
- **Documents to verify:** Abitur certificate, residence permit, registration.
- **Risk:** eligibility can change if your residence status changes.
- **Confirm with:** a KfW distribution partner and your BAföG office.

### Scenario 5: a student close to graduation with a short-term funding gap

- **Possible categories:** Studierendenwerk final-phase loan, Bildungskredit (if eligible), work, scholarship.
- **Eligibility check:** remaining semesters, the final-phase loan's conditions, guarantor [\[16\]](#content-sources)[\[17\]](#content-sources)[\[20\]](#content-sources).
- **Documents to verify:** transcript, exam and thesis timetable.
- **Risk:** graduation is delayed and repayment overlaps with your job search.
- **Confirm with:** the Studierendenwerk's social counselling service; for exam rules see our [grading and exam retakes guide](/en/blog/german-university-grading-system-and-exam-retakes-en), and for what happens if your enrolment ends see our [Exmatrikulation guide](/en/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-en).

## Comparison table

> **Eligibility and conditions vary by provider, residence status and individual circumstances. Check the official provider before applying.**

| Financing option | Provider | Repayment required | Interest | Typical eligibility | International student access | Payout | Repayment start | Key limitation |
|---|---|---|---|---|---|---|---|---|
| KfW Studienkredit | KfW (via partners) | Yes | Variable; 6.34% nominal / 6.53% effective (Stand 26.09.2026, valid from 01.04.2026) | Up to age 44; listed statuses | Bildungsinländer and certain EU/family statuses; § 16b not a separate category | €100–650 a month | After 18–23-month grace period | Variable rate, no legal entitlement |
| Bildungskredit | BVA approves, KfW pays | Yes | Variable; 3.57% nominal / 3.53% effective (Stand 26.09.2026, valid from 01.04.2026) | Age 18–36, advanced stage, up to 12th semester | Under § 8 BAföG | €100/200/300 a month, up to 24 months | 4 years after first payout | Short-term, limited amount |
| BAföG | State (BAföG offices) | Generally half | Loan part interest-free | Income and status conditions | Under § 8 BAföG | Monthly, needs-based | About 5 years after funding period | Status and income conditions |
| Studierendenwerk / Daka | Studierendenwerke | Yes | Mostly interest-free; 1–5% fee possible | Varies by organisation | Varies by organisation | One-off or monthly | Varies by organisation | Guarantor, no legal entitlement |
| Private bank / Sparkasse | Local banks | Yes | Usually variable | Creditworthiness, local area | Varies; often not stated | Varies by product | Varies by product | Few products available |
| Income-share model | Bildungsfonds | Yes, as income share | Income share instead of interest | Varies by programme | Most require EU or permanent status | Varies by product | Above an income threshold | Availability changes often |
| Scholarship | DAAD, foundations, universities | No | None | Merit, programme conditions | Varies; many open | Varies by programme | – | Competitive, limited |
| Employment | Employer | No | None | Residence permit conditions | 140 full / 280 half days a year | Salary | – | Time and work limit |
| Verpflichtungserklärung | Sponsor in Germany | No (sponsor is liable) | None | Sponsor's income | Yes | Sponsor covers costs | – | Sponsor's legal liability |

## Checklist

1. Identify your residence status (§ 16b, permanent residence, EU citizenship, Bildungsinländer).
2. Write down your needs month by month and work out how long you need funding.
3. Check non-debt sources first: scholarships, work, family support.
4. Compare the KfW, Bildungskredit and BAföG eligibility lists with your status.
5. Find out your local Studierendenwerk's loan types and its nationality and guarantor conditions.
6. Always read an interest rate together with its "valid from" date, and check whether it is variable or fixed.
7. Ask whether interest is deducted from the payout during the payout phase.
8. Get the repayment start date and minimum instalment in writing.
9. If you plan to use the loan for a visa or extension, ask the responsible mission or immigration office in advance.
10. If repayment becomes difficult, contact a free debt counselling service [\[33\]](#content-sources).

## FAQ

### Can international students get a KfW student loan in Germany?

It depends. KfW's official eligibility list covers German citizens, EU citizens who have lived in Germany for at least three years, family members and Bildungsinländer. Holding only a § 16b student residence permit is not listed as a separate eligible category. People with a German Abitur or an eligible family status are assessed separately.

### Does a student loan replace a blocked account?

Not automatically. The German mission pages we checked don't list student loans among the standard accepted forms of proof for a first visa, so ask your mission. For extensions in Germany, some immigration offices, such as Hamburg's, may accept a loan contract that pays out a sufficient monthly amount; this isn't a nationwide rule.

### What is the current KfW Studienkredit interest rate?

As of 26.09.2026, the rate valid from 01.04.2026 is 6.34% nominal and 6.53% effective. The rate is variable and is normally reset every 1 April and 1 October. Check KfW's official website for the rate applying from 1 October 2026.

### What is the difference between a Studienkredit and BAföG?

BAföG is state support set out in law; in higher education it is generally half grant and half interest-free loan. A Studienkredit is a loan contract that you repay in full with interest. The eligibility rules also differ.

### Can I get a student loan without a SCHUFA history?

Having no SCHUFA history doesn't in itself mean automatic rejection. KfW does not mention SCHUFA in the checked Merkblatt, but it does carry out a credit assessment. Banks usually check creditworthiness. Each provider makes its own assessment.

### Do I need a guarantor for a Studierendenwerk loan?

Often, yes. Studierendenwerk loans usually require a guarantor who lives in Germany and has a regular income. Some small or short bridging loans may not need one. Conditions vary by organisation, and there's no legal entitlement to a loan.

## Sources

1. KfW, KfW-Studienkredit (174) product page — https://www.kfw.de/inlandsfoerderung/Privatpersonen/Studieren-Qualifizieren/F%C3%B6rderprodukte/KfW-Studienkredit-(174)/
2. KfW, Merkblatt KfW-Studienkredit 174 (Stand 11/2023) — https://www.kfw.de/PDF/Download-Center/F%C3%B6rderprogramme-(Inlandsf%C3%B6rderung)/PDF-Dokumente/6000002590_M_174_Studienkredit.PDF
3. KfW, Fragen und Antworten zum KfW-Studienkredit (rate from 01.04.2026) — https://www.kfw.de/%C3%9Cber-die-KfW/Newsroom/Aktuelles/Q-As-Studienkredit.html
4. KfW, Konditionenanzeiger (checked 26.09.2026) — https://www.kfw-formularsammlung.de/KonditionenanzeigerINet/KonditionenAnzeiger
5. KfW, Bildungskredit (173) — https://www.kfw.de/inlandsfoerderung/Privatpersonen/Studieren-Qualifizieren/F%C3%B6rderprodukte/Bildungskredit-(173)/
6. Bundesverwaltungsamt, Was bietet der Bildungskredit — https://www.bva.bund.de/DE/Services/Buerger/Schule-Ausbildung-Studium/Bildungskredit/Antrag/Bildungskredit-Hintergrund/bildungskredit-was-bietet_node.html
7. Bundesverwaltungsamt, Bildungskredit: nationality — https://www.bva.bund.de/DE/Services/Buerger/Schule-Ausbildung-Studium/Bildungskredit/Antrag/Studierende/Voraussetzungen/Besondere/Besondere_6.html
8. § 8 BAföG — https://www.gesetze-im-internet.de/baf_g/__8.html
9. § 17 BAföG — https://www.gesetze-im-internet.de/baf_g/__17.html
10. § 18 BAföG — https://www.gesetze-im-internet.de/baf_g/__18.html
11. BMFTR, BAföG cabinet decision (12.08.2026) — https://www.bmftr.bund.de/SharedDocs/Kurzmeldungen/DE/2026/08/120826-bafoeg-kabinettsbeschluss.html
12. Deutsches Studierendenwerk, Darlehenskassen — https://www.studierendenwerke.de/en/topics/student-finance/funding-options/darlehenskassen
13. studierendenWERK BERLIN, Finanzielle Unterstützung in Notlagen — https://www.stw.berlin/beratung/beratung-finanzierung-und-soziales/studienfinanzierung-im-%C3%BCberblick/einmalige-unterst%C3%BCtzungsm%C3%B6glichkeiten-des-studierendenwerks-berlin/finanzielle-unterst%C3%BCtzung-in-notlagen.html
14. Studentische Darlehnskasse Berlin e.V. — https://dakaberlin.de/
15. Daka – Darlehenskasse der Studierendenwerke e.V. — https://daka-darlehensantrag.de/ ; Kölner Studierendenwerk, Daka-Darlehen — https://www.kstw.de/finanzen/weitere-finanzierungsmoeglichkeiten/daka-darlehen/
16. Darlehenskasse der Bayerischen Studierendenwerke, Richtlinien — https://www.darlehenskasse-bayern.de/richtlinien/
17. Studierendenwerk Hamburg, Darlehen für Studierende in finanziellen Notlagen — https://www.stwhh.de/studienfinanzierung/darlehen-fuer-studierende-in-finanziellen-notlagen ; Darlehenskasse — https://www.stwhh.de/studienfinanzierung/darlehenskasse-des-studierendenwerks-hamburg
18. Studierendenwerk Frankfurt am Main, MainSWerk-Studiendarlehen — https://www.swffm.de/beratung-finanzierung/studienfinanzierung/mainswerk-studiendarlehen
19. Kölner Studierendenwerk, Darlehen in Notlagen — https://www.kstw.de/finanzen/darlehen-in-notlagen/
20. Studierendenwerk Heidelberg, Stipendien und Kredite — https://www.stw.uni-heidelberg.de/de/stipendien_und_kredite
21. DKB, Bietet die DKB einen Studienkredit an — https://www.dkb.de/fragen-antworten/bietet-die-dkb-einen-studienkredit-an
22. Deutsche Bank, personal loan FAQ — https://www.deutsche-bank.de/pk/service-und-kontakt/services/fragen-antworten/kredit-und-immobilien/werden-auch-privatkredite-an-arbeitslose-auszubildende_-hausfrauen-studenten-und-schueler-vergeben.html ; Commerzbank, KfW-Studienkredit — https://www.commerzbank.de/kredit-finanzierung/produkte/ratenkredite/kfw-studienkredit/
23. Sparkasse Pforzheim Calw, S-Bildungskredit — https://www.sparkasse-pforzheim-calw.de/de/home/privatkunden/kredite-und-finanzierungen/bildungskredit.html
24. CHANCEN eG — https://chancen-eg.de/ ; Deutsche Bildung, FAQ — https://www.deutsche-bildung.de/faq/ ; Brain Capital, FAQ — https://braincapital.de/faq.html
25. § 2 AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__2.html
26. § 16b AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__16b.html
27. General administrative regulation on the Residence Act (AVwV-AufenthG), no. 16.0.8 — https://www.verwaltungsvorschriften-im-internet.de/bsvwvbund_26102009_MI31284060.htm
28. German missions in France, study visa — https://allemagneenfrance.diplo.de/fr-de/service/visa/2521298-2521298
29. DAAD / Study in Germany, Finanzierungsnachweis — https://www.study-in-germany.com/de/studium-planen/voraussetzungen/finanzierungsnachweis/
30. Free and Hanseatic City of Hamburg, Amt für Migration: Informationen für ausländische Studenten (Stand July 2024) — https://www.hamburg.de/resource/blob/91946/1dc75bb6a18abac0cb3dd85da155c1bc/amt-m-m31-informationen-fuer-auslaendische-studenten-2024-deutsch-pdf-data.pdf
31. Service Berlin, residence permit for study — https://service.berlin.de/dienstleistung/305244/
32. Verbraucherzentrale, Studium mit Studienkredit (Stand 18.02.2025) — https://www.verbraucherzentrale.de/wissen/geld-versicherungen/kredit-schulden-insolvenz/studium-mit-studienkredit-82766
33. meine-schulden.de, find a debt counselling centre — https://www.meine-schulden.de/en/finding-help/debt-advice-centers/find-a-debt-counseling-center
34. Deutschlandstipendium, FAQ — https://www.deutschlandstipendium.de/deutschlandstipendium/de/studierende/haeufig-gestellte-fragen/haeufig-gestellte-fragen.html
35. DAAD, STIBET I — https://www.daad.de/de/infos-services-fuer-hochschulen/weiterfuehrende-infos-zu-daad-foerderprogrammen/stibet-i/ ; DAAD scholarships — https://www.daad.de/de/in-deutschland-studieren/stipendien/daad-stipendien/
36. Deutsches Studierendenwerk, internationale-studierende.de: Finanzierung und Aufenthalt — https://www.internationale-studierende.de/waehrend-des-studiums/finanzierung-und-aufenthalt
37. Sparkasse Oberhessen, KfW-Studienkredit — https://www.sparkasse-oberhessen.de/de/home/privatkunden/kredite-und-finanzierungen/kfw-studienkredit.html ; Volksbanken Raiffeisenbanken, Studienkredit — https://www.vr.de/privatkunden/produkte/kredite/studienkredit.html

*Conditions checked on the official pages of providers and authorities on 26.09.2026; interest rates and products can change. General information only; not financial, legal or tax advice.*
MD;

        $deBody = <<<'MD'
Kurz gesagt: Einen einheitlichen „Studienkredit" gibt es in Deutschland nicht. KfW-Studienkredit, Bildungskredit, BAföG, Darlehen der Studierendenwerke, Bankprodukte und einkommensabhängige Modelle folgen jeweils eigenen Regeln. Welche davon für Sie infrage kommen, hängt vom Produkt, Ihrem Aufenthaltsstatus und Ihrer persönlichen Situation ab. Typische internationale Studierende, die mit einer Aufenthaltserlaubnis zum Studium (§ 16b AufenthG) nach Deutschland gekommen sind, werden beim KfW-Studienkredit und beim Bildungskredit nicht als eigene förderfähige Gruppe genannt. Bildungsinländer, EU-Bürger mit längerem Aufenthalt in Deutschland und bestimmte andere Statusgruppen werden dagegen gesondert geprüft. Dieser Leitfaden erklärt die einzelnen Möglichkeiten, was sie für Visum und Aufenthaltserlaubnis bedeuten und was Sie vor einer Kreditaufnahme prüfen sollten.

> **Stand: 26.09.2026** · Zinssätze, Voraussetzungen und Angebote können sich ändern. · Allgemeine Informationen, keine Finanz- oder Rechtsberatung. · Prüfen Sie vor der Antragstellung die aktuellen Konditionen beim offiziellen Anbieter.

## Was ist ein Studienkredit

Ein Studienkredit ist ein Darlehen, mit dem Sie Ihre Lebenshaltungskosten während des Studiums finanzieren und das Sie **mit Zinsen zurückzahlen**. Das bekannteste Produkt dieser Art ist der „KfW-Studienkredit" der staatlichen Förderbank KfW [\[1\]](#content-quellen). Im Alltag meint „Studienkredit" aber oft sehr unterschiedliche Dinge:

- **KfW-Studienkredit:** ein einkommensunabhängiger Kredit der staatlichen Förderbank mit variablem Zins und ohne Sicherheiten [\[1\]](#content-quellen)[\[2\]](#content-quellen).
- **Bildungskredit:** ein zeitlich und betraglich begrenztes Darlehen des Bundes für Studierende in fortgeschrittenen Ausbildungsphasen [\[5\]](#content-quellen)[\[6\]](#content-quellen).
- **BAföG:** staatliche Förderung, im Studium in der Regel zur Hälfte Zuschuss und zur Hälfte zinsloses Darlehen [\[9\]](#content-quellen).
- **Darlehen der Studierendenwerke / Darlehenskassen:** Not-, Überbrückungs- oder Studienabschlussdarlehen [\[12\]](#content-quellen).
- **Bank- und Sparkassenprodukte:** einzelne regionale Institute haben eigene Bildungskredite; viele Banken vermitteln nur den KfW-Studienkredit [\[21\]](#content-quellen)[\[37\]](#content-quellen).
- **Einkommensabhängige Modelle (Bildungsfonds):** Nach dem Abschluss zahlen Sie für eine bestimmte Zeit einen Anteil Ihres Einkommens [\[24\]](#content-quellen).
- **Finanzierung ohne Schulden:** Stipendien, Jobben, Unterstützung durch die Familie und eine Verpflichtungserklärung [\[34\]](#content-quellen)[\[35\]](#content-quellen)[\[26\]](#content-quellen).

Diese Unterscheidung ist wichtig, denn die Produkte unterscheiden sich darin, wer sie beantragen kann, wie viel sie auszahlen und wie sie im Visums- und Aufenthaltsverfahren bewertet werden.

## Studienkredit und BAföG im Vergleich

Studienkredit und BAföG sind nicht dasselbe. BAföG ist eine gesetzlich geregelte staatliche Förderung nach dem Bundesausbildungsförderungsgesetz; im Studium wird sie in der Regel zur Hälfte als Zuschuss und zur Hälfte als zinsloses Staatsdarlehen gezahlt [\[9\]](#content-quellen). Ein Studienkredit ist dagegen ein Kreditvertrag: Sie zahlen den erhaltenen Betrag plus Zinsen zurück [\[1\]](#content-quellen).

| Merkmal | BAföG | KfW-Studienkredit |
|---|---|---|
| Rechtsgrundlage | Gesetz (BAföG) | Kreditvertrag |
| Rückzahlung | In der Regel die Hälfte; Darlehensteil zinslos | Vollständig, mit Zinsen |
| Einkommensprüfung | Einkommen von Studierenden und Eltern zählt | Einkommensunabhängig |
| Rechtsanspruch | Ja, wenn die Voraussetzungen erfüllt sind | Nein; die KfW entscheidet über den Kredit |

Für den BAföG-Darlehensteil regelt das Gesetz außerdem eine Obergrenze und einen Erlass der Restschuld [\[9\]](#content-quellen)[\[10\]](#content-quellen). Beim Studienkredit gibt es keine solche Grenze: Was Sie insgesamt zurückzahlen, hängt vom Zinssatz und von der Rückzahlungsdauer ab.

## Welche Möglichkeiten internationale Studierende haben

Der Zugang folgt keiner einheitlichen Regel, sondern hängt **vom Produkt und vom Aufenthaltsstatus** ab:

- **KfW-Studienkredit:** Die offizielle Liste der Antragsberechtigten nennt deutsche Staatsangehörige, EU-Bürger unter bestimmten Voraussetzungen, Familienangehörige und Bildungsinländer. Typische internationale Studierende, die nur eine Aufenthaltserlaubnis nach § 16b haben, werden dort nicht als eigene förderfähige Gruppe genannt [\[1\]](#content-quellen)[\[2\]](#content-quellen).
- **Bildungskredit:** Für ausländische Studierende gelten die Regeln des § 8 BAföG; diese erfassen vor allem dauerhafte oder langfristige Aufenthaltstitel [\[7\]](#content-quellen)[\[8\]](#content-quellen).
- **BAföG:** hängt von den in § 8 BAföG genannten Statusgruppen ab; ein gewöhnlicher Aufenthalt nach § 16b allein wird dort nicht als ausreichend genannt [\[8\]](#content-quellen).
- **Darlehen der Studierendenwerke:** unterschiedlich je nach Einrichtung. Manche fördern alle Staatsangehörigkeiten, andere verlangen von Nicht-EU-Studierenden einen unbefristeten Aufenthaltstitel. In der Praxis ist die größte Hürde oft eine bürgende Person mit Wohnsitz in Deutschland [\[12\]](#content-quellen)[\[14\]](#content-quellen)[\[15\]](#content-quellen)[\[16\]](#content-quellen).
- **Stipendien, Jobben und Familienunterstützung:** Es gibt keinen allgemeinen Ausschluss nach Staatsangehörigkeit; jedes Programm hat eigene Bedingungen [\[26\]](#content-quellen)[\[34\]](#content-quellen)[\[35\]](#content-quellen).

Eine pauschale Antwort mit Ja oder Nein gibt es für internationale Studierende also nicht. Prüfen Sie zuerst Ihren Aufenthaltsstatus und dann die Voraussetzungen des Produkts.

## KfW-Studienkredit

Der KfW-Studienkredit (Programmnummer 174) zahlt Studierenden an staatlichen oder staatlich anerkannten Hochschulen in Deutschland einen monatlichen Betrag aus [\[1\]](#content-quellen). Die wichtigsten Merkmale:

- Es sind **keine Sicherheiten** nötig, und der Kredit ist **einkommensunabhängig** [\[1\]](#content-quellen)[\[2\]](#content-quellen).
- Trotzdem gibt es eine **Kreditprüfung und Kreditentscheidung durch die KfW**, und es besteht **kein Rechtsanspruch** [\[2\]](#content-quellen).
- Sie beantragen den Kredit nicht direkt bei der KfW, sondern über ihre Vertriebspartner (Banken, Sparkassen oder Online-Partner) [\[1\]](#content-quellen).
- Antragstellende ohne deutsche Staatsangehörigkeit reichen ein zusätzliches Formblatt und Nachweise wie die Meldebescheinigung ein [\[2\]](#content-quellen).

## Wer den KfW-Studienkredit bekommen kann

Die offizielle Liste der KfW nennt [\[1\]](#content-quellen)[\[2\]](#content-quellen):

1. **deutsche Staatsangehörige** mit Wohnsitz in Deutschland und ihre Familienangehörigen,
2. **EU-Bürger**, die seit mindestens **drei Jahren** rechtmäßig in Deutschland wohnen und gemeldet sind, und ihre Familienangehörigen,
3. **Bildungsinländer:** Personen, die ihre Hochschulzugangsberechtigung (etwa das Abitur) in Deutschland oder an einer deutschen Schule im Ausland erworben haben und in Deutschland gemeldet sind.

Hinzu kommt eine Altersgrenze: Am 1. April bzw. 1. Oktober vor Beginn der Auszahlung dürfen Sie höchstens **44 Jahre** alt sein [\[2\]](#content-quellen).

**Hinweis für Nicht-EU-Studierende:** Eine Aufenthaltserlaubnis nach § 16b allein wird in der Liste nicht als eigene förderfähige Gruppe genannt. Das heißt aber nicht, dass Nicht-EU-Studierende pauschal ausgeschlossen sind: Wer zum Beispiel in Deutschland das Abitur gemacht hat (Bildungsinländer) oder als Familienangehöriger einer EU-Bürgerin oder eines EU-Bürgers berechtigt ist, wird anders beurteilt. Prüfen Sie Ihre Situation anhand der aktuellen KfW-Konditionen und mit dem Vertriebspartner, über den Sie den Antrag stellen.

## Auszahlung und Förderdauer beim KfW-Studienkredit

Der KfW-Studienkredit zahlt **100 bis 650 Euro im Monat** aus; die Höhe wählen Sie selbst [\[1\]](#content-quellen)[\[2\]](#content-quellen). Wie lange Sie Geld erhalten, hängt von Ihrem Alter ab [\[2\]](#content-quellen):

| Alter bei Förderbeginn | Höchstdauer | Höchstbetrag insgesamt |
|---|---|---|
| bis 24 Jahre | 14 Semester | 54.600 € |
| 25–34 Jahre | 10 Semester | 39.000 € |
| 35–44 Jahre | 6 Semester | 23.400 € |

Für Master- und Aufbaustudiengänge ist die Auszahlung auf **6 Semester und 23.400 Euro** begrenzt. Der Erstantrag ist bis zum 10. Fachsemester möglich [\[2\]](#content-quellen).

## Zinsen beim KfW-Studienkredit

Der KfW-Studienkredit hat einen **variablen Zinssatz**. Er setzt sich aus dem 6-Monats-EURIBOR und einer festen Marge zusammen und wird in der Regel jeweils zum **1. April und 1. Oktober** angepasst [\[2\]](#content-quellen)[\[3\]](#content-quellen).

> **Zinssatz KfW-Studienkredit – Stand: 26.09.2026, gültig ab 01.04.2026**
> Sollzins: **6,34 %** · Effektivzins: **6,53 %** [\[3\]](#content-quellen)[\[4\]](#content-quellen)
> Der ab 1. Oktober 2026 geltende variable Zinssatz war bei Redaktionsschluss noch nicht bestätigt. Prüfen Sie den aktuellen Zinssatz immer auf der offiziellen Website der KfW.

Zum Vergleich: Ab dem 1. Oktober 2025 lag der Effektivzins bei 6,04 % [\[3\]](#content-quellen). Innerhalb von sechs Monaten hat sich der Zins also um rund einen halben Prozentpunkt verändert. Rechenbeispiele auf der KfW-Produktseite veranschaulichen die Berechnung; sie zeigen nicht den aktuellen Produktzins. Lesen Sie einen Zinssatz deshalb immer zusammen mit dem Datum, ab dem er gilt.

In der Auszahlungsphase werden die anfallenden Zinsen **von der monatlichen Auszahlung abgezogen**. Es kommt also unter Umständen nicht der volle gewählte Betrag bei Ihnen an. Im weiteren Verlauf der Auszahlungsphase kann nach den KfW-Bedingungen eine Zinsstundung möglich sein; gestundete Zinsen entfallen nicht, sondern werden später gezahlt [\[2\]](#content-quellen).

## Rückzahlung beim KfW-Studienkredit

- **Karenzphase:** Nach der letzten Auszahlung folgt eine tilgungsfreie Zeit von **18 bis 23 Monaten**. Sie kann auf Wunsch auf **6 Monate** verkürzt werden [\[2\]](#content-quellen).
- **Laufzeit:** Die Rückzahlung kann bis zu **25 Jahre** dauern und muss spätestens mit 67 Jahren abgeschlossen sein [\[2\]](#content-quellen).
- **Raten:** Die Mindestrate beträgt **20 Euro** im Monat; der Standardplan sieht eine Rückzahlung über **10 Jahre** vor [\[2\]](#content-quellen).
- **Festzins:** In der Rückzahlungsphase ist ein Festzins für bis zu 10 Jahre möglich [\[2\]](#content-quellen)[\[3\]](#content-quellen).
- **Sondertilgungen:** In jeder Phase sind Sondertilgungen ab **100 Euro** möglich [\[2\]](#content-quellen).

Planen Sie die Rückzahlung realistisch anhand der KfW-Bedingungen und Ihres erwarteten Einkommens. Klären Sie vorab in den Vertragsbedingungen, welche Möglichkeiten Sie haben, wenn Ihr Einkommen später niedriger ausfällt als geplant.

## Bildungskredit

Der Bildungskredit ist nicht mit dem KfW-Studienkredit zu verwechseln. Er ist ein eigenes Programm des Bundes (Nummer 173), das Studierenden in **fortgeschrittenen Ausbildungsphasen** eine befristete Unterstützung bietet. Über den Antrag entscheidet das **Bundesverwaltungsamt (BVA)**, die Auszahlung übernimmt die **KfW** [\[5\]](#content-quellen)[\[6\]](#content-quellen).

**Wer kann ihn beantragen:** Studierende ab 18 Jahren bis zum **Ende des Monats, in dem sie 36 Jahre alt werden**, in einer fortgeschrittenen Ausbildungsphase und höchstens bis zum **12. Semester** [\[6\]](#content-quellen). Für ausländische Studierende gelten die Regeln des **§ 8 BAföG**, die dauerhafte Aufenthaltstitel und vergleichbare Statusgruppen erfassen [\[7\]](#content-quellen)[\[8\]](#content-quellen). Eine gewöhnliche Aufenthaltserlaubnis nach § 16b allein wird dort nicht als ausreichend genannt.

**Höhe:** **100, 200 oder 300 Euro im Monat** für höchstens **24 Monate**, insgesamt also **1.000 bis 7.200 Euro**. Zusätzlich ist ein Abschlag von bis zu **3.600 Euro** möglich [\[6\]](#content-quellen).

**Zinsen:** variabel, an den EURIBOR gekoppelt.
> **Zinssatz Bildungskredit – Stand: 26.09.2026, gültig ab 01.04.2026**
> Sollzins: **3,57 %** · Effektivzins: **3,53 %** [\[5\]](#content-quellen)

**Rückzahlung:** beginnt **4 Jahre nach der ersten Auszahlung** mit **120 Euro im Monat**. Vorzeitige Rückzahlung ist kostenfrei, Sicherheiten sind nicht nötig [\[5\]](#content-quellen)[\[6\]](#content-quellen).

## BAföG im Kurzvergleich

Dies ist kein BAföG-Ratgeber; hier nur die wichtigsten Punkte zum Vergleich:

- Im Studium wird BAföG in der Regel **zu 50 % als Zuschuss und zu 50 % als zinsloses Staatsdarlehen** gezahlt [\[9\]](#content-quellen).
- Die Rückzahlung des Darlehensteils beginnt etwa fünf Jahre nach dem Ende der Förderungshöchstdauer; das Gesetz legt eine monatliche Mindestrate fest und erlässt die Restschuld nach einer bestimmten Zahl von Raten [\[10\]](#content-quellen). Prüfen Sie die für Sie geltenden Beträge immer in Ihrem Bescheid.
- Für internationale Studierende hängt die Förderfähigkeit von den Statusgruppen in **§ 8 BAföG** ab. Eine gewöhnliche Aufenthaltserlaubnis nach § 16b wird dort nicht als ausreichend genannt; familiäre, dauerhafte und andere Aufenthaltstitel werden gesondert geprüft [\[8\]](#content-quellen).
- Ein am 12. August 2026 beschlossener **Kabinettsentwurf** **plant** höhere BAföG-Sätze ab dem 1. April 2027. Diese Änderung ist noch nicht in Kraft [\[11\]](#content-quellen).

## Darlehen der Studierendenwerke

Studierendenwerke und ihre Darlehenskassen bilden eine eigene Kategorie. Ein einheitliches bundesweites Angebot gibt es nicht. 56 der 58 Studierendenwerke in Deutschland unterstützen Studierende, die unverschuldet in finanzielle Not geraten sind, mit Überbrückungsdarlehen aus einer Darlehenskasse. Es besteht jedoch **kein Rechtsanspruch**, und in der Regel ist eine **Bürgschaft** nötig [\[12\]](#content-quellen).

Dabei sollten Sie die Darlehensarten unterscheiden:

- **Not- oder Einzeldarlehen:** kleine Beträge für unerwartete, kurzfristige Notlagen.
- **Überbrückungsdarlehen:** etwa für die Zeit, bis über einen BAföG-Antrag entschieden ist.
- **Studienabschlussdarlehen:** für Studierende in der letzten Phase des Studiums.

Beispiele, die wir geprüft haben (auf den Websites der Einrichtungen, Stand 26.09.2026):

- **Berlin:** Das studierendenWERK BERLIN weist selbst darauf hin, dass es **derzeit keine Darlehen vergibt**; es bietet nur einmalige Nothilfen an [\[13\]](#content-quellen). Davon zu unterscheiden ist die **Studentische Darlehnskasse Berlin e.V.**, ein unabhängiger Verein, der einen verzinsten Studienkredit anbietet (gestaffelter Zins ab 3,95 %); Nicht-EU-Studierende brauchen einen unbefristeten Aufenthaltstitel, und eine Bürgschaft ist erforderlich [\[14\]](#content-quellen). Die beiden Einrichtungen sollten nicht verwechselt werden.
- **Daka (NRW):** die gemeinsame Darlehenskasse von 12 Studierendenwerken in Nordrhein-Westfalen. Sie fördert nach eigenen Angaben **alle Staatsangehörigkeiten**; das Darlehen ist zinslos, es fällt aber eine **Verwaltungsgebühr von 5 %** an, höchstens 12.000 Euro insgesamt. Eine Bürgschaft ist nötig; bürgende Personen aus Nicht-EU-Staaten brauchen einen unbefristeten Aufenthaltstitel [\[15\]](#content-quellen).
- **Bayern (Darlehenskasse der Bayerischen Studierendenwerke):** Studienabschlussdarlehen, in den ersten 5 Jahren zinslos, danach 2 % pro Jahr, höchstens 18.000 Euro. Nicht-EU-Studierende müssen eine unbefristete Aufenthalts- oder Niederlassungserlaubnis nachweisen (Ausnahmen in Härtefällen möglich); eine Bürgschaft ist nötig [\[16\]](#content-quellen).
- **Hamburg:** Einzeldarlehen (bis 1.000 Euro), Studienabschlussdarlehen und BAföG-Überbrückungsdarlehen; zinslos mit **1 % Gebühr**; in der Regel ist eine Bürgschaft nötig [\[17\]](#content-quellen).
- **Frankfurt (MainSWerk-Studiendarlehen):** zinslos mit **5 % Verwaltungsgebühr**, höchstens 10.000 Euro; eine Bürgschaft ist erforderlich [\[18\]](#content-quellen).
- **Köln (KStW):** ein zinsloses Darlehen aus dem Hilfsfonds für unvorhersehbare und unverschuldete Notlagen (Bürge erforderlich) und ein kurzes Überbrückungsdarlehen von 350 Euro ohne Bürgen; andere Finanzierungsquellen sollen vorher ausgeschöpft sein [\[19\]](#content-quellen).
- **Heidelberg:** ein zinsloses Darlehen für die Studienabschlussphase, in der Regel bis zum Sechsfachen des monatlichen BAföG-Satzes, mit **1 % Gebühr**; unter 500 Euro ist keine Bürgschaft nötig [\[20\]](#content-quellen).

Manche Darlehen sind also zinslos, kosten aber eine Gebühr, andere sind verzinst; manche knüpfen an die Staatsangehörigkeit an, andere nicht. Sprechen Sie vor einer Entscheidung mit der Sozialberatung Ihres Studierendenwerks; viele Einrichtungen setzen ein Beratungsgespräch vor dem Antrag voraus [\[13\]](#content-quellen)[\[17\]](#content-quellen)[\[19\]](#content-quellen).

## Private Bankkredite

Einen klassischen, bankeigenen Studienkredit bei den großen Banken zu finden, ist 2026 schwierig:

- Die **DKB** schreibt ausdrücklich, dass sie keinen eigenen Studienkredit mehr anbietet, und verweist auf die KfW [\[21\]](#content-quellen).
- Auf den offiziellen Websites der **Deutschen Bank** und der **Commerzbank** war am 26.09.2026 kein bankeigener klassischer Studienkredit feststellbar. Die Privatkredit-Seite der Deutschen Bank setzt ein regelmäßiges Nettoeinkommen voraus [\[22\]](#content-quellen).
- Viele **Sparkassen** und **Volksbanken** vermitteln vor allem den KfW-Studienkredit [\[37\]](#content-quellen).
- Einzelne regionale Institute haben eigene Produkte. So hat etwa der S-Bildungskredit der Sparkasse Pforzheim Calw einen variablen Zins und setzt Bonität voraus [\[23\]](#content-quellen).

Gehen Sie also nicht davon aus, dass eine deutsche Bank einen eigenen Studienkredit anbietet. Wenn Sie ein Bankprodukt finden, fragen Sie schriftlich nach Zinsart, Gesamtkosten, Sicherheiten bzw. Bürgschaft und danach, ob es internationalen Studierenden offensteht.

**Einkommensabhängige Modelle:** Statt Zinsen zahlen Sie nach dem Abschluss für eine bestimmte Zeit einen Anteil Ihres Einkommens. Am 26.09.2026 hieß es auf der Website der **CHANCEN eG**, dass derzeit keine Finanzierung möglich ist; die Website der **Deutschen Bildung** wirkt für Bewerbungen geöffnet, nennt aber ein deutsches Abitur als Erwartung; **Brain Capital** setzt eine EU-Staatsangehörigkeit oder einen dauerhaften Aufenthaltstitel voraus [\[24\]](#content-quellen). Der Stand dieser Modelle ändert sich häufig; fragen Sie vor einer Bewerbung direkt beim Anbieter nach.

## SCHUFA und Bonitätsprüfung

Die SCHUFA ist die bekannteste Auskunftei in Deutschland. Welche Rolle sie beim Studienkredit spielt, hängt vom Produkt ab:

- Im geprüften Merkblatt der KfW wird die SCHUFA nicht erwähnt. Geprüft wird trotzdem: Die KfW nimmt eine **Kreditprüfung vor und trifft eine Kreditentscheidung** [\[2\]](#content-quellen).
- Bei Bankprodukten wird in der Regel die Bonität geprüft [\[23\]](#content-quellen).
- Einige einkommensabhängige Modelle geben an, keinen SCHUFA-Eintrag zu erzeugen [\[24\]](#content-quellen).

Eine noch fehlende SCHUFA-Historie in Deutschland bedeutet für sich genommen keine automatische Ablehnung; jeder Anbieter prüft nach eigenen Kriterien. Wie die SCHUFA funktioniert und wie ein Eintrag entsteht, erklärt unser [SCHUFA-Ratgeber](/de/blog/schufa-guide-2026-why-is-credit-score-important-for-turkish-students-de); wie das Thema bei der Wohnungssuche gelöst werden kann, zeigt unser [Ratgeber zur Wohnungssuche ohne SCHUFA](/de/blog/apartment-search-in-germany-without-schufa-history-de).

## Bürgschaft und Sicherheiten

- **KfW-Studienkredit** und **Bildungskredit** verlangen keine Sicherheiten [\[2\]](#content-quellen)[\[6\]](#content-quellen).
- **Darlehen der Studierendenwerke** setzen häufig eine Bürgschaft voraus [\[12\]](#content-quellen). Die Daka verlangt zum Beispiel eine bürgende Person zwischen 18 und 70 Jahren, die dauerhaft in Deutschland lebt und über der Pfändungsfreigrenze verdient [\[15\]](#content-quellen). In Bayern muss die bürgende Person in Deutschland wohnen und ein Mindesteinkommen haben [\[16\]](#content-quellen).
- Die **Studentische Darlehnskasse Berlin e.V.** verlangt ab einer bestimmten Darlehenshöhe zwei Bürgen [\[14\]](#content-quellen).

Für internationale Studierende, die neu in Deutschland sind, ist eine bürgende Person mit Wohnsitz in Deutschland und regelmäßigem Einkommen oft eine größere Hürde als jede Staatsangehörigkeitsregel.

## Visum und Aufenthaltserlaubnis: Finanzierungsnachweis

Dieser Punkt wird am häufigsten missverstanden. Die Kernaussage: **Ein Studienkredit ersetzt ein Sperrkonto nicht automatisch.**

**Rechtlicher Rahmen:** Nach dem Aufenthaltsgesetz gilt der Lebensunterhalt bei Aufenthalten zum Studium mit einem Betrag als gesichert, der sich am monatlichen BAföG-Bedarf orientiert [\[25\]](#content-quellen). Für 2026 sind das **992 Euro im Monat bzw. 11.904 Euro im Jahr** [\[28\]](#content-quellen)[\[31\]](#content-quellen). Welche Nachweise anerkannt werden, richtet sich nach einer Aufzählung in der Verwaltungsvorschrift, die mit „insbesondere" beginnt, also nicht abschließend ist. Genannt werden Einkommens- und Vermögensnachweise der Eltern, eine Verpflichtungserklärung, ein Sperrkonto, eine jährlich zu erneuernde Bankbürgschaft und Stipendien [\[27\]](#content-quellen).

**Erstes Visum (Antrag aus dem Ausland):**
- Die von uns geprüften Seiten deutscher Auslandsvertretungen (zum Beispiel der deutschen Vertretungen in Frankreich) nennen einen gewöhnlichen Studienkredit **nicht** als Standardnachweis [\[28\]](#content-quellen).
- Das heißt nicht, dass ein Kredit „verboten" ist, sondern nur, dass er nicht auf der Standardliste steht.
- Fragen Sie die für Sie zuständige **deutsche Auslandsvertretung** (Botschaft oder Generalkonsulat), welchen Nachweis sie akzeptiert. Auch der DAAD empfiehlt, die Form des Nachweises mit der Vertretung abzuklären [\[29\]](#content-quellen).

**In Deutschland, bei Erteilung oder Verlängerung der Aufenthaltserlaubnis nach § 16b:**
- Die Hamburger Ausländerbehörde nennt in ihrem Informationsblatt für Studierende einen Kreditvertrag mit einer Bank über einen Studienkredit, aus dem monatlich eine ausreichende Summe ausgezahlt wird, als möglichen Nachweis (Stand: Juli 2024) [\[30\]](#content-quellen). Hamburg weist außerdem darauf hin, dass sich verschiedene Nachweise **kombinieren** lassen.
- Das ist ein **Beispiel, keine bundesweite Regel.** Die Berliner Serviceseite nennt für die Verlängerung Kontoauszüge der letzten sechs Monate und sonstige Einkommensnachweise, erwähnt aber keine Kredite [\[31\]](#content-quellen). Ausländerbehörden in anderen Städten können andere Nachweise verlangen.
- Bei der Verlängerung wird die Finanzierung erneut geprüft, außerdem ein angemessener Studienfortschritt [\[26\]](#content-quellen)[\[36\]](#content-quellen).

**Wichtig:** Eine Kreditzusage ist **keine Garantie** für ein Visum oder eine Aufenthaltserlaubnis. Die Entscheidung trifft immer die zuständige Auslandsvertretung oder Ausländerbehörde, teils nach Ermessen.

Zum Visumsverfahren lesen Sie unseren [Leitfaden zum Studentenvisum 2026](/de/blog/germany-student-visa-2026-application-steps-documents-rejection-de); die Visumkosten können Sie mit unserem [Visumkosten-Rechner](/de/tools/visa-cost) schätzen. Zur Unterstützung durch Familie oder Bekannte finden Sie Informationen in unserem [Leitfaden zur Verpflichtungserklärung](/de/blog/verpflichtungserklarung-deutschland-leitfaden-2026).

## Sperrkonto und Studienkredit im Vergleich

- Ein **Sperrkonto** ist ein **Mechanismus für den Finanzierungsnachweis.** Es zeigt, dass das Geld vorhanden ist, und erlaubt nur eine bestimmte monatliche Auszahlung. Für 2026 sind 11.904 Euro im Jahr (992 Euro im Monat) nötig [\[28\]](#content-quellen)[\[31\]](#content-quellen).
- Ein **Studienkredit** ist ein **Finanzierungsprodukt.** Er leiht Ihnen Geld, das Sie mit Zinsen zurückzahlen.

Beides ist nicht dasselbe. Ein Kredit kann ein Weg sein, das Geld für ein Sperrkonto aufzubringen; das bedeutet aber nicht, dass der Kredit selbst als Finanzierungsnachweis anerkannt wird. Wie ein Sperrkonto funktioniert, erklärt unser [Sperrkonto-Ratgeber](/de/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-de); Sonderfälle wie Verlängerung und Stipendium behandelt unser [Ratgeber zur Sperrkonto-Nutzung](/de/blog/sperrkonto-usage-different-visa-and-scholarship-situations-extension-2026-de). Anbieter vergleichen Sie mit unserem [Sperrkonto-Vergleich](/de/tools/sperrkonto).

Für 2027 gibt es noch keinen bestätigten amtlichen Betrag; die geplanten BAföG-Änderungen können ihn beeinflussen.

## Was ein Kredit wirklich kostet

Ein einfaches Beispiel für den Mechanismus (keine Berechnung, nur zur Veranschaulichung):

> Eine Studentin plant, **24 Monate lang 500 Euro im Monat** zu erhalten. Geplante Auszahlung insgesamt: **500 € × 24 = 12.000 €**.

Diese 12.000 Euro sind **nicht der Betrag, den sie am Ende zurückzahlt.** Denn:

- **Es fallen Zinsen an:** In der Auszahlungsphase werden jeden Monat Zinsen auf die bisher ausgezahlte Summe berechnet.
- **Der KfW-Zins ist variabel:** Er wird zweimal im Jahr angepasst; künftige Zinssätze kennt heute niemand [\[2\]](#content-quellen)[\[3\]](#content-quellen).
- **Zinsen können von der Auszahlung abgezogen werden:** Beim KfW-Studienkredit werden die Zinsen in der Auszahlungsphase von der monatlichen Auszahlung abgezogen, sodass nicht jeden Monat die vollen 500 Euro ankommen [\[2\]](#content-quellen).
- **Die Laufzeit zählt:** Je länger die Rückzahlung dauert, desto mehr Zinsen zahlen Sie insgesamt.

Deshalb nennen wir in diesem Leitfaden keine exakte Gesamtrückzahlungssumme. Nutzen Sie die Rechner und aktuellen Konditionen der KfW und lassen Sie sich Auszahlungs- und Rückzahlungsplan vor Vertragsabschluss schriftlich geben. Für Ihre Monatsplanung helfen unser [Lebenshaltungskosten-Rechner](/de/tools/cost-of-living) und unser Artikel über die [wahren Kosten eines Studiums in Deutschland](/de/blog/wahre-kosten-studium-deutschland-budget-check).

## Risiko variabler Zinsen

Ein variabler Zins kann sinken, aber auch steigen. Der Effektivzins des KfW-Studienkredits lag ab 1. Oktober 2025 bei 6,04 % und ab 1. April 2026 bei 6,53 % [\[3\]](#content-quellen). Auch die Verbraucherzentrale weist darauf hin, dass sich der Zins in der Auszahlungsphase jedes Jahr zum 1. April und 1. Oktober ändern kann und dass in der Rückzahlungsphase unter bestimmten Voraussetzungen ein fester Zinssatz vereinbart werden kann [\[32\]](#content-quellen).

So können Sie mit diesem Risiko umgehen:
- Verfolgen Sie die Zinsänderungen regelmäßig.
- Informieren Sie sich über Bedingungen und Kosten des Festzinses in der Rückzahlungsphase [\[2\]](#content-quellen).
- Prüfen Sie vorab, welche Möglichkeiten und Bedingungen für Sondertilgungen gelten [\[2\]](#content-quellen).

## Vorteile

- Einige Produkte sind **einkommensunabhängig und ohne Sicherheiten** (KfW, Bildungskredit) [\[1\]](#content-quellen)[\[6\]](#content-quellen).
- Die monatliche Auszahlung lässt sich an Ihren Bedarf anpassen [\[1\]](#content-quellen).
- Darlehen der Studierendenwerke sind oft **zinslos** (Gebühren möglich) [\[15\]](#content-quellen)[\[17\]](#content-quellen).
- Die Rückzahlung beginnt erst nach einer Karenzphase [\[2\]](#content-quellen).
- Ein Kredit kann es erleichtern, weniger zu jobben und sich auf das Studium zu konzentrieren.

## Risiken

- Bei einem verzinsten Kredit **zahlen Sie mehr zurück, als Sie erhalten haben.**
- Ein variabler Zins macht die Gesamtkosten schwer vorhersehbar [\[3\]](#content-quellen)[\[32\]](#content-quellen).
- Dauert das Studium länger oder wird es abgebrochen, kann die Rückzahlung schwieriger werden; manche Kreditgeber verlangen dann sofort Raten [\[32\]](#content-quellen).
- Ein Kredit gilt nicht automatisch als Finanzierungsnachweis für Visum oder Aufenthaltserlaubnis [\[28\]](#content-quellen)[\[30\]](#content-quellen).
- Bei Darlehen mit Bürgschaft haftet auch die bürgende Person.

Wenn Sie Probleme bei der Rückzahlung haben, können Sie sich an die **kostenlose Schuldnerberatung** der Verbraucherzentralen und Kommunen wenden; Beratungsstellen in Ihrer Nähe finden Sie über meine-schulden.de [\[33\]](#content-quellen).

## Alternativen

- **Deutschlandstipendium:** **300 Euro im Monat**, muss nicht zurückgezahlt werden, **steht allen Staatsangehörigkeiten offen**, ist einkommensunabhängig und leistungsbezogen. Beworben wird sich bei der eigenen Hochschule. Allein deckt es den Finanzierungsnachweis von 992 Euro nicht ab [\[34\]](#content-quellen).
- **DAAD-Stipendien und STIBET:** Die DAAD-Stipendiendatenbank listet Programme auf. Stipendien aus STIBET beantragen Studierende nicht direkt, sondern über das International Office ihrer Hochschule [\[35\]](#content-quellen).
- **Jobben:** Mit einer Aufenthaltserlaubnis nach § 16b dürfen Sie **140 ganze oder 280 halbe Tage im Jahr** arbeiten; studentische Nebentätigkeiten an der Hochschule werden nicht angerechnet [\[26\]](#content-quellen). Die Möglichkeiten vergleichen wir in [HiWi vs. Werkstudent](/de/blog/hiwi-vs-werkstudent-student-jobs-in-germany-de), den Nutzen für die Karriere erklärt unser [Werkstudent-Ratgeber](/de/blog/werkstudent-deutschland-jobmarkt-erfahrung-noten), und zu Praktika mit B1/B2-Deutsch finden Sie unseren [Praktikums-Ratgeber](/de/blog/internship-in-germany-with-b1-b2-german-de).
- **Familie / Verpflichtungserklärung:** Einkommensnachweise der Eltern oder die Verpflichtungserklärung einer in Deutschland lebenden Person gehören zu den anerkannten Nachweisen [\[27\]](#content-quellen).
- **Nothilfe der Studierendenwerke:** Einige Studierendenwerke haben Notfallfonds für internationale Studierende [\[36\]](#content-quellen).

Zu den praktischen Hürden bei der Kontoeröffnung lesen Sie unseren [Ratgeber zum Bankkonto](/de/blog/opening-a-bank-account-in-germany-the-real-obstacles-de); zu den Kosten in einzelnen Städten unseren Artikel über [die günstigsten Studentenstädte](/de/blog/cheapest-student-cities-germany-real-monthly-cost-de).

## Fünf Fallbeispiele

Die folgenden Beispiele sind keine Produktempfehlung. Sie zeigen nur, welche Kategorien und Prüfungen infrage kommen können.

### Fall 1: Nicht-EU-Studierende, die aus der Türkei ihr erstes Studienvisum beantragen

- **Mögliche Kategorien:** Sperrkonto, Verpflichtungserklärung, Stipendium, Einkommensnachweise der Eltern.
- **Voraussetzungen prüfen:** Bei einem Erstantrag aus dem Ausland liegen die Statusgruppen aus den Listen von KfW und Bildungskredit in der Regel nicht vor [\[1\]](#content-quellen)[\[7\]](#content-quellen).
- **Unterlagen / Status prüfen:** aktuelle Nachweisliste der Auslandsvertretung und der Betrag für 2026 (11.904 Euro im Jahr) [\[28\]](#content-quellen)[\[31\]](#content-quellen).
- **Risiko:** ein Antrag mit einem Nachweis, der nicht auf der Standardliste steht.
- **Wo nachfragen:** bei der zuständigen deutschen Auslandsvertretung.

### Fall 2: Studierende mit Aufenthaltserlaubnis nach § 16b in Deutschland mit Finanzierungslücke

- **Mögliche Kategorien:** Not- oder Überbrückungsdarlehen des Studierendenwerks, Jobben, Stipendium, Familienunterstützung, in manchen Städten ein Kreditvertrag.
- **Voraussetzungen prüfen:** Staatsangehörigkeits- und Bürgschaftsbedingungen des Studierendenwerks; bei der KfW wird dieser Status nicht als eigene Gruppe genannt [\[1\]](#content-quellen)[\[12\]](#content-quellen).
- **Unterlagen / Status prüfen:** die Nachweise, die Ihre Ausländerbehörde bei der Verlängerung verlangt, und Nachweise über den Studienfortschritt [\[30\]](#content-quellen)[\[31\]](#content-quellen).
- **Risiko:** kurz vor dem Verlängerungstermin keinen ausreichenden Nachweis zu haben.
- **Wo nachfragen:** bei der örtlichen Ausländerbehörde und der Sozialberatung des Studierendenwerks.

### Fall 3: EU-Bürger, die seit mindestens drei Jahren in Deutschland leben

- **Mögliche Kategorien:** KfW-Studienkredit, bei erfüllten Voraussetzungen BAföG und Bildungskredit, Stipendien.
- **Voraussetzungen prüfen:** Dauer des rechtmäßigen Aufenthalts und der Meldung, Altersgrenze [\[1\]](#content-quellen)[\[2\]](#content-quellen).
- **Unterlagen / Status prüfen:** Meldebescheinigung, Immatrikulationsbescheinigung.
- **Risiko:** variabler Zins und Gesamtkosten.
- **Wo nachfragen:** bei einem KfW-Vertriebspartner; zum BAföG beim zuständigen BAföG-Amt.

### Fall 4: Bildungsinländer mit deutschem Abitur

- **Mögliche Kategorien:** KfW-Studienkredit, je nach Aufenthaltsstatus BAföG und Bildungskredit, Stipendien.
- **Voraussetzungen prüfen:** die KfW-Definition für Bildungsinländer und eine Meldeadresse in Deutschland [\[1\]](#content-quellen)[\[2\]](#content-quellen); beim BAföG der Status nach § 8 [\[8\]](#content-quellen).
- **Unterlagen / Status prüfen:** Abiturzeugnis, Aufenthaltstitel, Meldebescheinigung.
- **Risiko:** Ändert sich der Aufenthaltsstatus, kann sich auch die Förderfähigkeit ändern.
- **Wo nachfragen:** bei einem KfW-Vertriebspartner und beim BAföG-Amt.

### Fall 5: Studierende kurz vor dem Abschluss mit kurzfristigem Finanzierungsengpass

- **Mögliche Kategorien:** Studienabschlussdarlehen des Studierendenwerks, Bildungskredit (falls förderfähig), Jobben, Stipendium.
- **Voraussetzungen prüfen:** verbleibende Semester, Bedingungen des Abschlussdarlehens, Bürgschaft [\[16\]](#content-quellen)[\[17\]](#content-quellen)[\[20\]](#content-quellen).
- **Unterlagen / Status prüfen:** Leistungsübersicht, Prüfungs- und Abschlussarbeitsplan.
- **Risiko:** Der Abschluss verzögert sich, und die Rückzahlung fällt in die Jobsuche.
- **Wo nachfragen:** bei der Sozialberatung des Studierendenwerks; zu Prüfungsregeln in unserem [Ratgeber zu Notensystem und Wiederholungsprüfungen](/de/blog/german-university-grading-system-and-exam-retakes-de), zu den Folgen einer beendeten Einschreibung in unserem [Exmatrikulations-Ratgeber](/de/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-de).

## Vergleichstabelle

> **Voraussetzungen und Konditionen unterscheiden sich je nach Anbieter, Aufenthaltsstatus und persönlicher Situation. Prüfen Sie vor der Antragstellung die Angaben des offiziellen Anbieters.**

| Finanzierungsmöglichkeit | Anbieter | Rückzahlung nötig | Zinsen | Typische Voraussetzungen | Zugang für internationale Studierende | Auszahlung | Beginn der Rückzahlung | Wichtigste Einschränkung |
|---|---|---|---|---|---|---|---|---|
| KfW-Studienkredit | KfW (über Vertriebspartner) | Ja | Variabel; 6,34 % Sollzins / 6,53 % effektiv (Stand 26.09.2026, gültig ab 01.04.2026) | Bis 44 Jahre; genannte Statusgruppen | Bildungsinländer und bestimmte EU-/Familienstatus; § 16b keine eigene Gruppe | 100–650 € pro Monat | Nach 18–23 Monaten Karenz | Variabler Zins, kein Rechtsanspruch |
| Bildungskredit | BVA bewilligt, KfW zahlt aus | Ja | Variabel; 3,57 % Sollzins / 3,53 % effektiv (Stand 26.09.2026, gültig ab 01.04.2026) | 18–36 Jahre, fortgeschrittene Phase, bis 12. Semester | Nach § 8 BAföG | 100/200/300 € pro Monat, max. 24 Monate | 4 Jahre nach erster Auszahlung | Kurzfristig, begrenzter Betrag |
| BAföG | Staat (BAföG-Ämter) | In der Regel zur Hälfte | Darlehensteil zinslos | Einkommens- und Statusvoraussetzungen | Nach § 8 BAföG | Monatlich, bedarfsabhängig | Etwa 5 Jahre nach Förderungshöchstdauer | Status- und Einkommensvoraussetzungen |
| Studierendenwerk / Daka | Studierendenwerke | Ja | Meist zinslos; 1–5 % Gebühr möglich | Je nach Einrichtung | Je nach Einrichtung | Einmalig oder monatlich | Je nach Einrichtung | Bürgschaft, kein Rechtsanspruch |
| Private Bank / Sparkasse | Regionale Institute | Ja | Meist variabel | Bonität, Geschäftsgebiet | Unterschiedlich; oft nicht angegeben | Je nach Produkt | Je nach Produkt | Wenige Angebote |
| Einkommensabhängiges Modell | Bildungsfonds | Ja, als Einkommensanteil | Einkommensanteil statt Zins | Je nach Programm | Meist EU- oder dauerhafter Status nötig | Je nach Produkt | Ab einer Einkommensschwelle | Angebot ändert sich häufig |
| Stipendium | DAAD, Stiftungen, Hochschulen | Nein | Keine | Leistung, Programmbedingungen | Je nach Programm; viele offen | Je nach Programm | – | Wettbewerb, begrenzt |
| Jobben | Arbeitgeber | Nein | Keine | Bedingungen des Aufenthaltstitels | 140 ganze / 280 halbe Tage im Jahr | Lohn | – | Zeit- und Arbeitsgrenze |
| Verpflichtungserklärung | Verpflichtungsgeber in Deutschland | Nein (Haftung des Verpflichtungsgebers) | Keine | Einkommen des Verpflichtungsgebers | Ja | Verpflichtungsgeber trägt Kosten | – | Rechtliche Haftung des Verpflichtungsgebers |

## Checkliste

1. Klären Sie Ihren Aufenthaltsstatus (§ 16b, dauerhafter Aufenthaltstitel, EU-Staatsangehörigkeit, Bildungsinländer).
2. Schreiben Sie Ihren Bedarf Monat für Monat auf und rechnen Sie aus, wie lange Sie eine Finanzierung brauchen.
3. Prüfen Sie zuerst Quellen ohne Schulden: Stipendien, Jobben, Unterstützung durch die Familie.
4. Gleichen Sie die Voraussetzungen von KfW, Bildungskredit und BAföG mit Ihrem Status ab.
5. Informieren Sie sich über Darlehensarten, Staatsangehörigkeits- und Bürgschaftsbedingungen Ihres Studierendenwerks.
6. Lesen Sie einen Zinssatz immer zusammen mit dem Datum, ab dem er gilt, und prüfen Sie, ob er variabel oder fest ist.
7. Fragen Sie, ob Zinsen in der Auszahlungsphase von der Auszahlung abgezogen werden.
8. Lassen Sie sich Rückzahlungsbeginn und Mindestrate schriftlich geben.
9. Wenn Sie den Kredit für Visum oder Verlängerung nutzen wollen, fragen Sie vorher bei der zuständigen Auslandsvertretung oder Ausländerbehörde nach.
10. Wenn die Rückzahlung schwierig wird, wenden Sie sich an eine kostenlose Schuldnerberatung [\[33\]](#content-quellen).

## Häufige Fragen (FAQ)

### Können internationale Studierende in Deutschland einen KfW-Studienkredit bekommen?

Das hängt vom Status ab. Die offizielle Liste der KfW nennt deutsche Staatsangehörige, EU-Bürger mit mindestens drei Jahren Aufenthalt in Deutschland, Familienangehörige und Bildungsinländer. Eine Aufenthaltserlaubnis nach § 16b allein wird nicht als eigene förderfähige Gruppe genannt. Wer ein deutsches Abitur oder einen passenden Familienstatus hat, wird gesondert geprüft.

### Ersetzt ein Studienkredit das Sperrkonto?

Nicht automatisch. Die geprüften Seiten deutscher Auslandsvertretungen nennen Studienkredite beim ersten Visum nicht als Standardnachweis; fragen Sie deshalb bei Ihrer Vertretung nach. Bei der Verlängerung in Deutschland akzeptieren manche Ausländerbehörden, etwa in Hamburg, einen Kreditvertrag mit ausreichender monatlicher Auszahlung; eine bundesweite Regel ist das nicht.

### Wie hoch ist der Zins beim KfW-Studienkredit aktuell?

Stand 26.09.2026 beträgt der seit 01.04.2026 gültige Zins 6,34 % Sollzins und 6,53 % effektiv. Der Zins ist variabel und wird in der Regel zum 1. April und 1. Oktober angepasst. Den ab 1. Oktober 2026 geltenden Zinssatz finden Sie auf der offiziellen Website der KfW.

### Was ist der Unterschied zwischen Studienkredit und BAföG?

BAföG ist eine gesetzlich geregelte staatliche Förderung und im Studium in der Regel zur Hälfte Zuschuss und zur Hälfte zinsloses Darlehen. Ein Studienkredit ist ein Kreditvertrag, den Sie vollständig mit Zinsen zurückzahlen. Auch die Voraussetzungen unterscheiden sich.

### Bekomme ich ohne SCHUFA-Historie einen Studienkredit?

Eine fehlende SCHUFA-Historie bedeutet für sich genommen keine automatische Ablehnung. Im geprüften Merkblatt der KfW wird die SCHUFA nicht erwähnt, die KfW nimmt aber eine Kreditprüfung vor. Banken prüfen in der Regel die Bonität. Jeder Anbieter entscheidet nach eigenen Kriterien.

### Brauche ich für ein Darlehen des Studierendenwerks einen Bürgen?

Häufig ja. Darlehen der Studierendenwerke setzen meist eine bürgende Person mit Wohnsitz in Deutschland und regelmäßigem Einkommen voraus. Bei manchen kleinen oder kurzen Überbrückungsdarlehen ist keine Bürgschaft nötig. Die Bedingungen unterscheiden sich je nach Einrichtung, und es besteht kein Rechtsanspruch.

## Quellen

1. KfW, KfW-Studienkredit (174), Produktseite — https://www.kfw.de/inlandsfoerderung/Privatpersonen/Studieren-Qualifizieren/F%C3%B6rderprodukte/KfW-Studienkredit-(174)/
2. KfW, Merkblatt KfW-Studienkredit 174 (Stand 11/2023) — https://www.kfw.de/PDF/Download-Center/F%C3%B6rderprogramme-(Inlandsf%C3%B6rderung)/PDF-Dokumente/6000002590_M_174_Studienkredit.PDF
3. KfW, Fragen und Antworten zum KfW-Studienkredit (Zins ab 01.04.2026) — https://www.kfw.de/%C3%9Cber-die-KfW/Newsroom/Aktuelles/Q-As-Studienkredit.html
4. KfW, Konditionenanzeiger (abgerufen am 26.09.2026) — https://www.kfw-formularsammlung.de/KonditionenanzeigerINet/KonditionenAnzeiger
5. KfW, Bildungskredit (173) — https://www.kfw.de/inlandsfoerderung/Privatpersonen/Studieren-Qualifizieren/F%C3%B6rderprodukte/Bildungskredit-(173)/
6. Bundesverwaltungsamt, Was bietet der Bildungskredit — https://www.bva.bund.de/DE/Services/Buerger/Schule-Ausbildung-Studium/Bildungskredit/Antrag/Bildungskredit-Hintergrund/bildungskredit-was-bietet_node.html
7. Bundesverwaltungsamt, Bildungskredit: Staatsangehörigkeit — https://www.bva.bund.de/DE/Services/Buerger/Schule-Ausbildung-Studium/Bildungskredit/Antrag/Studierende/Voraussetzungen/Besondere/Besondere_6.html
8. § 8 BAföG — https://www.gesetze-im-internet.de/baf_g/__8.html
9. § 17 BAföG — https://www.gesetze-im-internet.de/baf_g/__17.html
10. § 18 BAföG — https://www.gesetze-im-internet.de/baf_g/__18.html
11. BMFTR, BAföG-Kabinettsbeschluss (12.08.2026) — https://www.bmftr.bund.de/SharedDocs/Kurzmeldungen/DE/2026/08/120826-bafoeg-kabinettsbeschluss.html
12. Deutsches Studierendenwerk, Darlehenskassen — https://www.studierendenwerke.de/en/topics/student-finance/funding-options/darlehenskassen
13. studierendenWERK BERLIN, Finanzielle Unterstützung in Notlagen — https://www.stw.berlin/beratung/beratung-finanzierung-und-soziales/studienfinanzierung-im-%C3%BCberblick/einmalige-unterst%C3%BCtzungsm%C3%B6glichkeiten-des-studierendenwerks-berlin/finanzielle-unterst%C3%BCtzung-in-notlagen.html
14. Studentische Darlehnskasse Berlin e.V. — https://dakaberlin.de/
15. Daka – Darlehenskasse der Studierendenwerke e.V. — https://daka-darlehensantrag.de/ ; Kölner Studierendenwerk, Daka-Darlehen — https://www.kstw.de/finanzen/weitere-finanzierungsmoeglichkeiten/daka-darlehen/
16. Darlehenskasse der Bayerischen Studierendenwerke, Richtlinien — https://www.darlehenskasse-bayern.de/richtlinien/
17. Studierendenwerk Hamburg, Darlehen für Studierende in finanziellen Notlagen — https://www.stwhh.de/studienfinanzierung/darlehen-fuer-studierende-in-finanziellen-notlagen ; Darlehenskasse — https://www.stwhh.de/studienfinanzierung/darlehenskasse-des-studierendenwerks-hamburg
18. Studierendenwerk Frankfurt am Main, MainSWerk-Studiendarlehen — https://www.swffm.de/beratung-finanzierung/studienfinanzierung/mainswerk-studiendarlehen
19. Kölner Studierendenwerk, Darlehen in Notlagen — https://www.kstw.de/finanzen/darlehen-in-notlagen/
20. Studierendenwerk Heidelberg, Stipendien und Kredite — https://www.stw.uni-heidelberg.de/de/stipendien_und_kredite
21. DKB, Bietet die DKB einen Studienkredit an — https://www.dkb.de/fragen-antworten/bietet-die-dkb-einen-studienkredit-an
22. Deutsche Bank, FAQ Privatkredit — https://www.deutsche-bank.de/pk/service-und-kontakt/services/fragen-antworten/kredit-und-immobilien/werden-auch-privatkredite-an-arbeitslose-auszubildende_-hausfrauen-studenten-und-schueler-vergeben.html ; Commerzbank, KfW-Studienkredit — https://www.commerzbank.de/kredit-finanzierung/produkte/ratenkredite/kfw-studienkredit/
23. Sparkasse Pforzheim Calw, S-Bildungskredit — https://www.sparkasse-pforzheim-calw.de/de/home/privatkunden/kredite-und-finanzierungen/bildungskredit.html
24. CHANCEN eG — https://chancen-eg.de/ ; Deutsche Bildung, FAQ — https://www.deutsche-bildung.de/faq/ ; Brain Capital, FAQ — https://braincapital.de/faq.html
25. § 2 AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__2.html
26. § 16b AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__16b.html
27. Allgemeine Verwaltungsvorschrift zum Aufenthaltsgesetz, Nr. 16.0.8 — https://www.verwaltungsvorschriften-im-internet.de/bsvwvbund_26102009_MI31284060.htm
28. Deutsche Vertretungen in Frankreich, Studium — https://allemagneenfrance.diplo.de/fr-de/service/visa/2521298-2521298
29. DAAD / Study in Germany, Finanzierungsnachweis — https://www.study-in-germany.com/de/studium-planen/voraussetzungen/finanzierungsnachweis/
30. Freie und Hansestadt Hamburg, Amt für Migration: Informationen für ausländische Studenten (Stand Juli 2024) — https://www.hamburg.de/resource/blob/91946/1dc75bb6a18abac0cb3dd85da155c1bc/amt-m-m31-informationen-fuer-auslaendische-studenten-2024-deutsch-pdf-data.pdf
31. Service Berlin, Aufenthaltserlaubnis zum Studium — https://service.berlin.de/dienstleistung/305244/
32. Verbraucherzentrale, Studium mit Studienkredit (Stand 18.02.2025) — https://www.verbraucherzentrale.de/wissen/geld-versicherungen/kredit-schulden-insolvenz/studium-mit-studienkredit-82766
33. meine-schulden.de, Schuldnerberatungsstelle finden — https://www.meine-schulden.de/en/finding-help/debt-advice-centers/find-a-debt-counseling-center
34. Deutschlandstipendium, Häufig gestellte Fragen — https://www.deutschlandstipendium.de/deutschlandstipendium/de/studierende/haeufig-gestellte-fragen/haeufig-gestellte-fragen.html
35. DAAD, STIBET I — https://www.daad.de/de/infos-services-fuer-hochschulen/weiterfuehrende-infos-zu-daad-foerderprogrammen/stibet-i/ ; DAAD-Stipendien — https://www.daad.de/de/in-deutschland-studieren/stipendien/daad-stipendien/
36. Deutsches Studierendenwerk, internationale-studierende.de: Finanzierung und Aufenthalt — https://www.internationale-studierende.de/waehrend-des-studiums/finanzierung-und-aufenthalt
37. Sparkasse Oberhessen, KfW-Studienkredit — https://www.sparkasse-oberhessen.de/de/home/privatkunden/kredite-und-finanzierungen/kfw-studienkredit.html ; Volksbanken Raiffeisenbanken, Studienkredit — https://www.vr.de/privatkunden/produkte/kredite/studienkredit.html

*Konditionen am 26.09.2026 auf den offiziellen Seiten der Anbieter und Behörden geprüft; Zinssätze und Angebote können sich ändern. Allgemeine Informationen, keine Finanz-, Rechts- oder Steuerberatung.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'student-loans-and-study-financing-in-germany',
                'title' => 'Almanya\'da Öğrenci Kredisi ve Eğitim Finansmanı',
                'excerpt' => 'Almanya\'da tek bir öğrenci kredisi yok. KfW Studienkredit, Bildungskredit, BAföG, Studierendenwerk ve banka kredileri kime açık, faiz ve geri ödeme nasıl işler, bloke hesap ve vize ile ilişkisi ne; 5 senaryo ve karşılaştırma tablosu.',
                'meta_title' => 'Almanya\'da Öğrenci Kredisi: KfW, Bildungskredit ve Diğerleri',
                'meta_description' => 'Almanya\'da öğrenci kredisi: KfW Studienkredit, Bildungskredit, BAföG ve Studierendenwerk kredileri kime açık, faizi ne (Stand 26.09.2026), vizede geçer mi?',
                'body' => $trBody,
            ],
            'en' => [
                'slug' => 'student-loans-and-study-financing-in-germany-en',
                'title' => 'Student Loans and Study Financing in Germany for International Students',
                'excerpt' => 'Germany has no single student loan. Who can use KfW, Bildungskredit, BAföG, Studierendenwerk and bank loans, how interest and repayment work, and how loans relate to the blocked account and visa, with 5 scenarios and a comparison table.',
                'meta_title' => 'Student Loans in Germany for International Students (2026)',
                'meta_description' => 'Student loans in Germany: who can get KfW, Bildungskredit, BAföG or Studierendenwerk loans, rates as of 26.09.2026, and what a loan means for your visa.',
                'body' => $enBody,
            ],
            'de' => [
                'slug' => 'student-loans-and-study-financing-in-germany-de',
                'title' => 'Studienkredit und Studienfinanzierung für internationale Studierende',
                'excerpt' => 'Den einen Studienkredit gibt es nicht: Wer KfW-Studienkredit, Bildungskredit, BAföG oder Darlehen der Studierendenwerke bekommt, wie Zinsen und Rückzahlung laufen und was das für Sperrkonto und Visum heißt – mit 5 Fallbeispielen.',
                'meta_title' => 'Studienkredit für internationale Studierende (2026)',
                'meta_description' => 'Studienkredit für internationale Studierende: KfW, Bildungskredit, BAföG und Darlehen der Studierendenwerke, Zinsen (Stand 26.09.2026) und Folgen fürs Visum.',
                'body' => $deBody,
            ],
        ];

        // Slug başka bir yazıya aitse ezme: hiçbir şey yazmadan önce üç slug'ı da kontrol et.
        foreach ($variants as $v) {
            $foreign = Post::where('slug', $v['slug'])->where(fn ($q) => $q->whereNull('translation_group_id')->orWhere('translation_group_id', '!=', $groupId))->first();
            if ($foreign) {
                throw new RuntimeException("Öğrenci kredisi yazısı: '{$v['slug']}' slug'ı başka bir çeviri grubuna ait (#{$foreign->id}), hiçbir şey yazılmadı.");
            }
        }

        DB::transaction(function () use ($variants, $groupId, $userId, $categoryId) {
        foreach ($variants as $locale => $v) {
            // content_html + reading_minutes: Post::booted() content_md'den üretir (MarkdownRenderer + BlogAutoLinker).
            $payload = [
                'locale' => $locale, 'translation_group_id' => $groupId, 'user_id' => $userId, 'category_id' => $categoryId,
                'title' => $v['title'], 'excerpt' => Str::limit($v['excerpt'], 250, '…'),
                'content_md' => $v['body'],
                'meta_title' => $v['meta_title'], 'meta_description' => Str::limit($v['meta_description'], 158, '…'),
                'is_published' => true,
            ];
            // Tekrar koşarsa yayın tarihi korunur; değişmeyen alanlar updated_at'a dokunmaz.
            $existing = Post::where('slug', $v['slug'])->first();
            $existing ? $existing->update($payload) : Post::create($payload + ['slug' => $v['slug'], 'published_at' => now()]);
        }
        });
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'student-loans-and-study-financing-in-germany',
            'student-loans-and-study-financing-in-germany-en',
            'student-loans-and-study-financing-in-germany-de',
        ])->delete();
    }
};

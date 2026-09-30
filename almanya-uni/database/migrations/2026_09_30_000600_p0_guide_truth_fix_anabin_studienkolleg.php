<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * P0 guide truth fix — anabin / Studienkolleg (TR/EN/DE, 9 yazı): what-is-anabin-h-h-h-how-is-a-turkish-diploma,
 * studienkolleg-center-list-2026-public-private-institutions, studienkolleg-guide-2026-who-needs-it-which-course-which-school.
 * Yanlış: H+/H+-/H- kodlarının lise diplomasına uygulanması ("H+ = HZB", "H- = Studienkolleg zorunlu"), "çoğu Türk lise
 * mezunu Studienkolleg'e gider", genel lise → otomatik Studienkolleg karar matrisi. Doğru (anabin/KMK, DAAD kabul
 * veritabanı, KMK Rahmenordnung; 30.09.2026): H kodları kurum statüsüdür (H+ = derecelerin denklik incelemesine tabi
 * tutulabilmesi; HZB / otomatik tanıma / otomatik kabul değil); okul diploması Türkiye kurallarıyla değerlendirilir
 * (12 yıllık lise + YKS SAY/SÖZ/EA/DİL > 180 + fakültede ≥4 yıllık programa yerleşme → alana bağlı doğrudan giriş;
 * yüksekokul → yalnız FH; meslek lisesi → alana yönelik Studienkolleg; önlisans + TYT > 150 → doğrudan; MYO 1 yıl →
 * Studienkolleg yalnız FH; açıköğretim 2 yıl → doğrudan; açık öğretim lisesi → bireysel; lisans → tüm alanlar;
 * yerleşmesiz/eşik altı → resmî genel kural yok). 180 = KMK 30.06.2022'den beri "bis auf Weiteres"; 170 yalnız 2020.
 * Motor: blok eşleştirme (canlı metin → md satırı). Bu migration'da ek: {delete} (blok tam bir kez eşleşmeli, satırlar
 * kaldırılır), {probe} (çok paragraflı yeni metnin "uygulandı" tespiti), alıntı devam satırlarında ">" öneki
 * eşleştirmede yok sayılır, excerpt için fieldsub. Başlık, slug, diğer bölümler değişmez.
 *
 * Yöntem (her kayıt kararlı kimlikle: posts/faqs = slug+locale, varlıklar = slug):
 *  - md satırları: markdown'dan arındırılmış düz metni canlı sayfadaki blokla BİREBİR eşleşen satır değişir
 *    (liste/başlık/alıntı öneki korunur);
 *  - alanlar (title/question): eski değer birebir doğrulanır;
 *  - JSON content_blocks: eski alt dize içeren tüm metin değerlerinde birebir alt dize değişimi.
 * Ön kontrol yazmadan önce tüm kayıtlarda yapılır; kayıt başına durum "tamamı bekleyen" ya da "tamamı uygulanmış"
 * olmalı; eksik kayıt / eşleşmeyen blok / kısmi durum → RuntimeException, hiçbir şey yazılmaz. Tek transaction.
 * İkinci çalıştırma no-op. PHPUnit altında (boş test DB'si) sorun varsa sessizce çıkar.
 */
return new class extends Migration
{
    public function up(): void
    {
        $spec = json_decode(<<<'JSON'
{
 "records": [
  {
   "table": "posts",
   "slug": "what-is-anabin-h-h-h-how-is-a-turkish-diploma",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Anabin H+, H+-, H- Nedir? Türk Lise Diploması Almanya İçin Nasıl Sınıflandırılır?",
      "replace": "Anabin H+, H+-, H- Nedir? Türk Diplomaları Almanya'da Nasıl Değerlendirilir?"
     },
     {
      "line": "Almanya'da üniversite hayalleri kuran bir Türk öğrenci misin? Başvuru sürecinin en kritik adımlarından biri, lise veya üniversite diplomanızın Almanya'daki denklik durumunu anlamaktır. İşte tam bu noktada anabin h+ h+- h- nedir sorusu devreye giriyor ve kafa karışıklığına yol açabiliyor. Merak etme, bu rehberimizde Anabin veri tabanının ne işe yaradığını, Türk diplomalarının bu sistemde nasıl sınıflandırıldığını ve senin için hangi yolun doğru olduğunu adım adım açıklayacağız.",
      "replace": "Almanya'da üniversite hayalleri kuran bir Türk öğrenci misin? Başvurunun en kritik adımlarından biri, lise ya da üniversite diplomanın Almanya'da nasıl değerlendirildiğini anlamaktır. Burada sık yapılan bir hata var: anabin'deki H+, H+- ve H- kodları lise diplomalarını değil, **yükseköğretim kurumlarını** gösterir. Bu rehberde H kodlarının gerçekte ne anlama geldiğini, Türk lise diplomasının Almanya'da hangi kurallarla değerlendirildiğini ve kendi durumunu nasıl kontrol edeceğini adım adım anlatıyoruz."
     },
     {
      "line": "Anabin sistemi, Türk lise ve üniversite diplomalarını temelde üç ana kategoriye ayırır: H+, H+-, ve H-. Bu sınıflandırma, senin Almanya'daki eğitim yolculuğunun nasıl başlayacağını doğrudan etkiler. Hadi bu kategorileri yakından inceleyelim.",
      "replace": "## H+, H+/- ve H- aslında neyi gösterir?\n\nanabin'deki **H+, H+/- ve H-** kısaltmaları **yükseköğretim kurumlarının** statüsünü gösterir; lise diplomalarını sınıflandırmaz.\n\n- **H+:** Kurum, kendi ülkesinde yükseköğretim kurumu olarak tanınır ve Almanya'da da yükseköğretim kurumu sayılır.\n- **H+/-:** Kurum türü düzeyinde tek bir statü belirlenemez; kurum ayrıca değerlendirilir.\n- **H-:** Kurum geçici ya da kalıcı olarak yükseköğretim kurumu sayılmaz.\n\nanabin'e göre H+ yalnızca, o kurumdan alınan derecelerin bir **denklik incelemesine** tabi tutulabileceği anlamına gelir; sonuç hakkında ön karar içermez. Yani H+:\n\n- tek başına bir **üniversiteye giriş hakkı (HZB)** değildir,\n- dereceni **otomatik olarak tanıtmaz**,\n- **otomatik kabul** anlamına gelmez; kabul kararını başvurduğun üniversite verir.\n\nDerecenin kendisi anabin'de ayrıca **derece türü** üzerinden değerlendirilir (\"entspricht\", \"gleichwertig\" ya da \"bedingt vergleichbar\" gibi). Bu değerlendirme de bir tanıma kararı değildir.\n\n## Türk lise diploması Almanya'da nasıl değerlendirilir?\n\nLise diplomanın Almanya'da üniversiteye giriş hakkı verip vermediği H kodlarıyla değil, anabin'deki **Türkiye okul diploması kurallarıyla** değerlendirilir (anabin → \"Schulabschlüsse mit Hochschulzugang\" → Türkei). Lise diploması **tek başına** giriş hakkı vermez; kurallar diplomayı ÖSYM sonucu ve yerleşme belgesiyle birlikte ele alır. anabin'in güncel değerlendirmesine göre (30.09.2026 itibarıyla):\n\n- **12 yıllık lise + YKS'de SAY, SÖZ, EA ya da DİL puan türünde 180 puanın üzerinde + bir fakültede en az 4 yıllık lisans programına yerleşme:** yerleştiğin alanda ve yakın alanlarda **Studienkolleg olmadan** doğrudan başvuru hakkı (alana bağlı giriş). Türkiye'de kayıt yaptırmış olman gerekmez.\n- **Üniversiteye bağlı bir yüksekokulda 4 yıllık programa yerleşme:** alana bağlı doğrudan giriş, yalnızca **Fachhochschule**'lere.\n- **Meslek lisesi + 180 puanın üzerinde:** alana yönelik **Studienkolleg**; Türkiye'de başarıyla tamamlanmış bir yıllık lisans öğrenimiyle alana bağlı doğrudan giriş.\n- **Tamamlanmış önlisans + TYT'de 150 puanın üzerinde:** alana bağlı doğrudan giriş.\n- **Meslek yüksekokulunda (MYO) yalnızca bir yıl:** Studienkolleg yolu, yalnızca Fachhochschule'ler için.\n- **Açıköğretim fakültesinde başarıyla tamamlanmış iki yıl:** alana bağlı doğrudan giriş.\n- **Açık öğretim lisesi:** her başvuru ayrıca değerlendirilir; sınav sonucu ve yerleşme de kanıtlanmalıdır.\n- **Tamamlanmış en az 4 yıllık lisans:** tüm alanlarda doğrudan giriş.\n- **İmam hatip ve Anadolu teknik gibi okul türleri:** sonucu bireysel olarak kontrol et.\n\n**Yerleşmen yoksa ya da eşiğin altındaysan:** Resmî kaynaklar bu durum için genel bir yol tanımlamıyor. DAAD'ın kabul veritabanına göre YKS'ye girmemiş ya da yalnızca TYT sonucu olan lise mezunu için kabul mümkün değildir. Kendi durumunu başvuracağın üniversiteye ya da uni-assist'e sor; karar üniversitenin ya da eyaletin yetkili makamının.\n\n**Alana bağlı giriş** ne demek? Yalnızca yerleştiğin ya da okuduğun alanla ilgili ve ona yakın programlara başvurabilirsin. Hangi programların \"yakın\" sayıldığına dair resmî sabit bir liste yok; bunu başvurduğun üniversite değerlendirir.\n\n**Puan eşikleri hakkında:** 180 puan, anabin/KMK'nın güncel değerlendirmesidir (son değişiklik: KMK kararı 30.06.2022, \"şimdilik\" devam ediyor). 170 puan yalnızca 2020 sınav yılı için geçerli bir istisnaydı. Bu eşikler Türk yükseköğretim kurallarının değil, Almanya'daki değerlendirmenin parçasıdır ve değişebilir; başvurmadan önce anabin'i ve üniversitenin şartlarını kontrol et.",
      "probe": "H+, H+/- ve H- aslında neyi gösterir?"
     },
     {
      "line": "H+ Nedir: Direkt Başvuru Hakkı (Hochschulzugangsberechtigung – HZB)",
      "delete": true
     },
     {
      "line": "H+ sınıflandırması, diplomanızın Almanya'da üniversiteye direkt başvuru hakkı (Hochschulzugangsberechtigung - HZB) verdiğini gösterir. Bu, en avantajlı durumdur! Eğer diplomanız H+ olarak sınıflandırılıyorsa, belirli bir bölüm ve üniversite için gerekli diğer şartları (dil yeterliliği, not ortalaması vb.) karşıladığınız takdirde, tıpkı bir Alman lise mezunu gibi doğrudan üniversiteye başvurabilirsiniz.",
      "delete": true
     },
     {
      "line": "Hangi Türk Liseleri Genellikle H+ Olarak Sınıflandırılır?",
      "delete": true
     },
     {
      "line": "Genel olarak, Türkiye'deki belirli lise türleri ve başarı koşulları sağlandığında H+ sınıflandırması alabilir:",
      "delete": true
     },
     {
      "line": "Anadolu Liseleri ve Fen Liseleri: Bu lise türlerinden mezun olan öğrenciler, genellikle çok iyi bir lise not ortalamasına ve Yükseköğretim Kurumları Sınavı (YKS) ile Türkiye'de bir üniversiteye yerleşme hakkına sahipse H+ olarak değerlendirilebilir. Özellikle YKS'de iyi bir dereceyle 4 yıllık bir lisans programına yerleşmiş olmak bu durumu güçlendirir.",
      "delete": true
     },
     {
      "line": "Uluslararası Bakalorya (IB) Diploması: Uluslararası geçerliliği olan bir IB diplomasına sahipseniz, genellikle doğrudan H+ sınıflandırması alırsınız ve Almanya'daki birçok üniversiteye direkt başvuru yapabilirsiniz.",
      "delete": true
     },
     {
      "line": "Bazı Özel Liseler: Almanya'daki bazı Alman okulları veya belirli uluslararası müfredat uygulayan özel liseler de doğrudan H+ denkliği sağlayabilir.",
      "delete": true
     },
     {
      "line": "Önemli Not: Sadece lise diploması ve YKS başarısı tek başına yeterli olmayabilir. Başvurduğunuz bölümle ilgili alan derslerinde belirli bir başarı seviyesi veya üniversitenin ek şartları da olabilir. Bu nedenle, her zaman başvurmak istediğiniz üniversitenin kabul şartlarını dikkatlice kontrol etmelisiniz. (Bkz: /universities sayfamızdaki üniversite profilleri)",
      "delete": true
     },
     {
      "line": "H+- Nedir: Kısıtlı Başvuru Hakkı (Fachgebundene HZB)",
      "delete": true
     },
     {
      "line": "H+- sınıflandırması, diplomanızın Almanya'da üniversiteye başvuru hakkı olduğunu, ancak belirli kısıtlamalarla birlikte geldiğini gösterir. Bu kısıtlamalar genellikle başvurabileceğiniz bölüm alanıyla sınırlı olabilir veya ek sınavlar/şartlar gerektirebilir.",
      "delete": true
     },
     {
      "line": "Hangi Türk Liseleri Genellikle H+- Olarak Sınıflandırılır ve Kısıtlamalar Nelerdir?",
      "delete": true
     },
     {
      "line": "H+- sınıflandırması genellikle şu durumlarda karşımıza çıkar:",
      "delete": true
     },
     {
      "line": "Bazı Anadolu/Fen Liseleri Mezunları: Lise not ortalaması çok yüksek olmayan veya YKS'de belirli bir başarı eşiğini tam olarak karşılayamayan ancak yine de Türkiye'de bir üniversiteye yerleşme hakkı kazanmış öğrenciler bu kategoriye girebilir. Kısıtlama, genellikle YKS'de yerleştiği bölümle aynı alanda Almanya'da eğitim alma zorunluluğu olabilir. Örneğin, Türkiye'de sayısal bir bölüme yerleştiyseniz, Almanya'da da sayısal ağırlıklı bir bölüme başvurmanız gerekebilir.",
      "delete": true
     },
     {
      "line": "İmam Hatip Liseleri ve Bazı Meslek Liseleri: Bu lise türlerinden mezun olan öğrenciler, genellikle kendi alanlarıyla ilgili bölümlere (meslek lisesi mezunları kendi mesleki alanlarına) başvurabilirler. Alan dışı bir bölüme başvurmak isterlerse, genellikle ek sınavlara girmeleri veya Studienkolleg'e gitmeleri gerekebilir. Örneğin, bir İmam Hatip Lisesi mezunu Almanya'da İlahiyat okumak isterse H+- ile direkt başvurabilirken, mühendislik okumak isterse H- durumuna düşebilir.",
      "delete": true
     },
     {
      "line": "Ön Lisans Mezunları: Türkiye'de 2 yıllık bir ön lisans (Meslek Yüksekokulu) programından mezun olan öğrenciler için durum biraz daha karmaşıktır. Genellikle doğrudan lisans tamamlama hakkı vermez. Bazı durumlarda, ön lisans eğitimini takip eden YKS başarısı ve belirli not ortalaması ile kendi alanlarında H+- olarak değerlendirilebilirler, ancak çoğu zaman Studienkolleg veya lisans programına baştan başlama seçeneği ile karşılaşırlar. Bu konuda her üniversitenin farklı politikaları olabilir, bu yüzden detaylı araştırma çok önemlidir. (Bkz: /faq sayfamızdaki ön lisans soruları)",
      "delete": true
     },
     {
      "line": "Çözüm Yolu: Eğer diplomanız H+- ise, öncelikle başvurmak istediğiniz bölümün kısıtlamalarla uyumlu olup olmadığını kontrol edin. Gerekirse, üniversiteye direkt olarak danışın veya ek sınavlar için hazırlık yapın.",
      "delete": true
     },
     {
      "line": "H- Nedir: Studienkolleg Zorunluluğu (Keine HZB)",
      "delete": true
     },
     {
      "line": "H- sınıflandırması, diplomanızın Almanya'da üniversiteye direkt başvuru hakkı vermediği anlamına gelir. Bu durumda, Almanya'da üniversiteye başlayabilmek için genellikle bir hazırlık koleji olan Studienkolleg'e gitmeniz zorunludur.",
      "delete": true
     },
     {
      "line": "Hangi Türk Liseleri Genellikle H- Olarak Sınıflandırılır?",
      "delete": true
     },
     {
      "line": "H- sınıflandırması genellikle şu durumlarda karşımıza çıkar:",
      "delete": true
     },
     {
      "line": "Açık Öğretim Liseleri: Türkiye'deki açık öğretim liselerinden mezun olan öğrenciler, genellikle Almanya'da direkt üniversiteye başvuramazlar ve Studienkolleg'e gitmek zorundadırlar.",
      "delete": true
     },
     {
      "line": "Meslek Liseleri (Genel Kural): Meslek lisesi mezunlarının büyük çoğunluğu H- olarak sınıflandırılır. Kendi alanları dışında bir bölüme başvurmak isteyenler veya belirli başarı şartlarını karşılamayanlar kesinlikle Studienkolleg'e gitmelidir. Çok nadir durumlarda, 4 yıllık meslek lisesi mezunları, kendi alanlarında YKS'de iyi bir başarı gösterip Türkiye'de bir lisans programına yerleşmişlerse ve çok iyi bir not ortalamasına sahiplerse H+- olarak değerlendirilebilirler, ancak bu istisnai bir durumdur ve her üniversite kabul etmeyebilir.",
      "delete": true
     },
     {
      "line": "İmam Hatip Liseleri (Alan Dışı Başvuru): İmam Hatip Lisesi mezunları, kendi alanları dışındaki bölümlere başvurmak istediklerinde genellikle H- olarak değerlendirilirler ve Studienkolleg'e yönlendirilirler.",
      "delete": true
     },
     {
      "line": "Genel Lise Diploması (YKS Başarısı Olmayanlar): Eğer Türkiye'deki bir liseden mezun olmuş ancak YKS'ye girmemiş veya Türkiye'de bir üniversiteye yerleşme hakkı kazanamamışsanız, diplomanız genellikle H- olarak sınıflandırılır.",
      "delete": true
     },
     {
      "line": "Studienkolleg Nedir? Studienkolleg, yabancı öğrencileri Alman üniversite sistemine ve diline hazırlayan bir yıllık bir programdır. Program sonunda FSP (Feststellungsprüfung) adı verilen bir yeterlilik sınavına girersiniz. Bu sınavı başarıyla geçerseniz, Almanya'da üniversiteye başvuru hakkı kazanırsınız. Studienkolleg'ler genellikle belirli alanlara (T-Kurs, M-Kurs, W-Kurs, G-Kurs, S-Kurs) ayrılır ve başvurmak istediğiniz bölüme göre uygun kursu seçmeniz gerekir. (Bkz: /studienkolleg rehberimiz)",
      "delete": true
     },
     {
      "line": "ÖSYM/YKS ve Anabin Sınıflandırmasındaki Rolü",
      "delete": true
     },
     {
      "line": "Anabin sisteminde Türk diplomalarının değerlendirilmesinde ÖSYM (Öğrenci Seçme ve Yerleştirme Merkezi) ve YKS (Yükseköğretim Kurumları Sınavı) sonuçları hayati bir rol oynar. Almanya, Türkiye'deki üniversiteye giriş sınavı başarısını, lise diplomasının Almanya'daki denkliğini belirlemede önemli bir kriter olarak kabul eder.",
      "delete": true
     },
     {
      "line": "Genellikle, H+ veya H+- sınıflandırması alabilmek için lise diplomanızın yanı sıra Türkiye'de 4 yıllık bir lisans programına yerleşme hakkı kazanmış olmanız beklenir. Bu durum, eğitiminizin belirli bir akademik standardı karşıladığının bir göstergesi olarak kabul edilir. YKS'de yerleştiğiniz bölüm, Almanya'da başvurabileceğiniz bölüm alanını da etkileyebilir.",
      "delete": true
     },
     {
      "line": "Detayları İncele: Lise türünüzün üzerine tıkladığınızda, o diploma için geçerli olan H+, H+-, H- sınıflandırmalarını ve ilgili açıklamaları göreceksiniz. Genellikle \"Bewertung\" (Değerlendirme) kısmında detaylı bilgi bulunur.",
      "replace": "Detayları İncele: Kendi diplomanı seçtiğinde, o diploma için geçerli kuralları (örneğin ÖSYM sonucu ve yerleşmeyle birlikte hangi erişim yolunun açıldığını) görürsün. H+/H- kodları okul diplomaları için kullanılmaz; kurumlara ilişkindir."
     },
     {
      "line": "Soru 3: Merhaba hukuk mezunu birinin anabinde denkliği çıkmıyorsa almanyada master yapmaya uygun olmuyor mu acaba denklik almak için neler yapmak lazım? Cevap: Hukuk gibi bazı meslekler, Almanya'da denklik süreçleri açısından oldukça özel ve karmaşıktır. Eğer Anabin'de hukuk diplomanız için doğrudan bir H+ veya H+- sınıflandırması göremiyorsanız veya \"keine Aussage\" (bilgi yok) gibi bir ifade varsa, bu genellikle Almanya'da hukuk masterı yapmanın zor olacağı anlamına gelir. Almanya'da hukuk mesleği, eyaletten eyalete değişen uzun bir eğitim ve staj sürecini (Staatsexamen) gerektirir. Yabancı hukuk diplomaları, genellikle Alman hukuk sistemine entegrasyon için ek sınavlar, dersler veya hatta lisans eğitimine baştan başlama gibi şartlar getirebilir. Denklik almak için öncelikle başvurmak istediğiniz eyaletin Adalet Bakanlığı veya ilgili denklik ofisleriyle iletişime geçmeniz, durumunuzu detaylıca anlatmanız ve hangi ek şartları yerine getirmeniz gerektiğini öğrenmeniz gerekir. Bazı durumlarda \"LL.M.\" (Master of Laws) programları yabancı hukuk mezunları için tasarlanmış olabilir, ancak bunlar da genellikle belirli ön şartlara tabidir.",
      "replace": "Soru 3: Merhaba hukuk mezunu birinin anabinde denkliği çıkmıyorsa almanyada master yapmaya uygun olmuyor mu acaba denklik almak için neler yapmak lazım? Cevap: anabin'de iki ayrı bilgi var: üniversitenin **kurum statüsü** (H+, H+/-, H-) ve **derece türünün** değerlendirmesi. Kurumun H+ olması tek başına denklik ya da kabul anlamına gelmez. Master başvurusunda kabul kararını üniversite verir ve derecenin programa uygunluğunu kendisi inceler. Almanya'da hukuk mesleği ise düzenlenmiş bir meslektir ve Staatsexamen yoluna bağlıdır; yabancı hukuk diploması bunun yerine geçmez. Hedefin master ise programın kabul şartlarını (bazı LL.M. programları yabancı hukukçular için tasarlanmıştır) üniversiteye sor; meslek için ise eyaletin yetkili makamına danış."
     },
     {
      "line": "Soru 4: Selam arkadaşlar. Ben ön lisans mezunuyum oradan denklik alabilir miyim veya lisans kabul mü almalıyım ne dersiniz sizce? Cevap: Ön lisans mezunları için Almanya'da durum genellikle H- veya H+- (kısıtlı) kategorisine girer. Direkt lisans tamamlama (dikey geçiş) hakkı elde etmek oldukça zordur. Çoğu zaman, ön lisans mezunlarının Almanya'da bir lisans programına kabul edilebilmesi için Studienkolleg'e gitmeleri ve FSP sınavını geçmeleri gerekir. Nadiren, çok yüksek not ortalaması ve Türkiye'de bir lisans programına yerleşme hakkı ile kendi alanlarında H+- olarak değerlendirilip bazı üniversitelerden kabul alabilirler, ancak bu istisnai bir durumdur. Genellikle, Almanya'da lisans eğitimi almak istiyorsanız, Studienkolleg ile başlayıp sıfırdan lisansa başvurmanız en garanti yoldur.",
      "replace": "Soru 4: Selam arkadaşlar. Ben ön lisans mezunuyum oradan denklik alabilir miyim veya lisans kabul mü almalıyım ne dersiniz sizce? Cevap: anabin'in güncel değerlendirmesine göre **tamamlanmış bir önlisans diploması**, lise diploması ve TYT'de 150 puanın üzerinde bir sonuçla birlikte, önlisans alanında ve yakın alanlarda **alana bağlı doğrudan giriş** hakkı verir; bu yolda Studienkolleg gerekmez. MYO'da yalnızca bir yıl okuduysan Studienkolleg yolu vardır ve yalnızca Fachhochschule'ler için geçerlidir. Hangi programların \"yakın alan\" sayıldığını başvurduğun üniversite değerlendirir."
     },
     {
      "line": "Soru 6: Merhabalar, ben meslek lisesi mezunuyum. Denklik yapsam orada kendi bölümüm ile alakalı, orada okuyabilir miyim studienkolleg yapmadan? Cevap: Maalesef, meslek lisesi mezunlarının büyük çoğunluğu için durum H- kategorisindedir ve Studienkolleg zorunludur. Kendi bölümünüzle alakalı olsa bile, doğrudan üniversiteye kabul edilmeniz çok nadirdir. İstisnai durumlarda (4 yıllık meslek lisesi, çok yüksek not ortalaması, YKS ile Türkiye'de 4 yıllık bir lisans programına yerleşme ve Almanya'da başvurulan bölümün mesleki alanınızla birebir uyumu) H+- olarak değerlendirilme ihtimali olsa da, bu her üniversite tarafından kabul edilmeyebilir. En güvenli ve yaygın yol, Studienkolleg'e gitmektir.",
      "replace": "Soru 6: Merhabalar, ben meslek lisesi mezunuyum. Denklik yapsam orada kendi bölümüm ile alakalı, orada okuyabilir miyim studienkolleg yapmadan? Cevap: anabin'in güncel değerlendirmesine göre meslek lisesi diploması, YKS'de SAY, SÖZ, EA ya da DİL puan türünde 180 puanın üzerinde bir sonuçla **alana yönelik Studienkolleg** yoluna açılır. Türkiye'de bir yıllık lisans öğrenimini başarıyla tamamladıysan, okuduğun alanda ve yakın alanlarda Studienkolleg olmadan **doğrudan** başvurabilirsin. \"H-\" gibi bir kod okul diplomaları için kullanılmaz. Kesin kararı başvurduğun üniversite verir."
     },
     {
      "line": "Soru 7: Bu soru daha önce sorulmuş, bu konu hakkında merak ettiğim bir soru var. Acaba lisansımızı denklik yaptırmamız gerekiyor mu yoksa anabin’de h+ sonucu yeterli olur mu? Cevap: Eğer lisans diplomanız Anabin'de H+ olarak sınıflandırılıyorsa, bu genellikle Almanya'da yüksek lisans (Master) programlarına doğrudan başvurmak için yeterlidir. \"Denklik yaptırmak\" terimi, genellikle bir diplomanın belirli bir mesleği icra etmek için resmi olarak tanınması anlamına gelir (örneğin doktorluk, öğretmenlik). Yüksek lisans başvurularında ise Anabin'deki H+ sonucu, temel olarak akademik denkliğinizi gösterir ve master programına kabul için yeterli bir ön şarttır. Ancak, yine de başvuracağınız üniversitenin program özelindeki ek şartlarını (dil yeterliliği, belirli bir not ortalaması, referans mektupları vb.) karşılamanız gerekir.",
      "replace": "Soru 7: Bu soru daha önce sorulmuş, bu konu hakkında merak ettiğim bir soru var. Acaba lisansımızı denklik yaptırmamız gerekiyor mu yoksa anabin’de h+ sonucu yeterli olur mu? Cevap: Üniversitenin anabin'de **H+** olması yalnızca kurumun yükseköğretim kurumu sayıldığını ve derecelerinin denklik incelemesine tabi tutulabileceğini gösterir; dereceni otomatik olarak tanıtmaz ve kabul garantisi değildir. Master başvurusunda kabul kararını ve derecenin programa uygunluğunu üniversite değerlendirir; ayrıca anabin'de derece türünün değerlendirmesine de bakabilirsin. \"Denklik\" (tanıma) çoğunlukla düzenlenmiş meslekler (örneğin hekimlik, öğretmenlik) için ayrı bir süreçtir. ZAB'ın Zeugnisbewertung belgesi üniversiteye giriş hakkı vermez; bazı üniversiteler isteyebilir."
     },
     {
      "line": "Almanya'da üniversite eğitimi hayalinizi gerçeğe dönüştürmek için anabin h+ h+- h- nedir sorusunun cevabını bilmek, atacağınız ilk ve en önemli adımlardan biridir. Diplomanızın hangi kategoriye girdiğini anlamak, size doğru yolu gösterecek ve zaman kaybetmenizi önleyecektir.",
      "replace": "Almanya'da üniversite hayalini gerçeğe dönüştürmek için H+, H+- ve H- kodlarının **kurumların** statüsünü gösterdiğini, lise diplomanın ise Türkiye'ye özgü okul diploması kurallarıyla (ÖSYM sonucu ve yerleşmeyle birlikte) değerlendirildiğini bilmek önemli bir adımdır. Kendi yolunu anlamak zaman kaybını önler; kesin kararı ise başvurduğun üniversite verir."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "what-is-anabin-h-h-h-how-is-a-turkish-diploma-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "What is Anabin H+, H+-, H-? How is a Turkish Diploma Classified for Germany?",
      "replace": "What Do Anabin H+, H+/- and H- Mean? How Are Turkish Qualifications Assessed for Germany?"
     },
     {
      "line": "Are you a Turkish student dreaming of university in Germany? One of the most critical steps in the application process is understanding the recognition status of your high school or university diploma in Germany. This is exactly where the question what is anabin h+ h+- h- comes into play and can lead to confusion. Don't worry, in this guide, we will explain step-by-step what the Anabin database is for, how Turkish diplomas are classified in this system, and which path is right for you.",
      "replace": "Are you a Turkish student dreaming of university in Germany? One of the most important steps is understanding how your school or university qualification is assessed in Germany. A common mistake: anabin's H+, H+- and H- codes describe **higher-education institutions**, not school-leaving certificates. This guide explains what the H codes really mean, which rules apply to a Turkish school-leaving certificate, and how to check your own case."
     },
     {
      "line": "The Anabin system fundamentally divides Turkish high school and university diplomas into three main categories: H+, H+-, and H-. This classification directly affects how your educational journey in Germany will begin. Let's take a closer look at these categories.",
      "replace": "## What do H+, H+/- and H- actually mean?\n\nIn anabin, **H+, H+/- and H-** describe the status of **higher-education institutions**; they do not classify school-leaving certificates.\n\n- **H+:** the institution is recognised as a higher-education institution in its home country and is regarded as one in Germany.\n- **H+/-:** no single status can be set for the type of institution; the institution is assessed separately.\n- **H-:** the institution is, temporarily or permanently, not regarded as a higher-education institution.\n\nAccording to anabin, H+ only means that degrees from that institution can undergo an **equivalence assessment**; it does not anticipate the result. So H+:\n\n- is **not** in itself a **university entrance qualification (HZB)**,\n- does **not automatically recognise** your degree,\n- does **not** mean **automatic admission**; the university you apply to decides.\n\nThe degree itself is assessed separately in anabin by **degree type** (for example \"entspricht\", \"gleichwertig\" or \"bedingt vergleichbar\"). That assessment is not a recognition decision either.\n\n## How is a Turkish school-leaving certificate assessed in Germany?\n\nWhether your Turkish school certificate gives access to German higher education is decided not by H codes but by anabin's **country rules for Turkish school qualifications** (anabin → \"Schulabschlüsse mit Hochschulzugang\" → Türkei). The Lise diploma **on its own** does not give access; the rules assess it together with the ÖSYM result and placement document. According to anabin's current assessment (as of 30 September 2026):\n\n- **12-year Lise + more than 180 points in YKS in the score type SAY, SÖZ, EA or DIL + placement in a bachelor's programme of at least 4 years at a faculty:** direct, subject-restricted access **without a Studienkolleg** for the placed subject and related subjects. You do not need to have enrolled in Türkiye.\n- **Placement in a 4-year programme at a university-affiliated Yüksekokul:** direct, subject-restricted access to **Fachhochschulen** only.\n- **Vocational high school (Meslek Lisesi) + more than 180 points:** subject-oriented **Studienkolleg**; after one successfully completed year of bachelor's study in Türkiye, direct subject-restricted access.\n- **Completed Önlisans + more than 150 points in TYT:** direct, subject-restricted access.\n- **Only one year at a Meslek Yüksekokulu (MYO):** Studienkolleg route, for Fachhochschulen only.\n- **Two successfully completed years at an open-education faculty (Açıköğretim):** direct, subject-restricted access.\n- **Open high school (Açık Öğretim Lisesi):** assessed case by case; the exam result and a study place must also be proven.\n- **Completed bachelor's degree of at least 4 years:** direct access for all subjects.\n- **School types such as İmam Hatip or Anadolu Teknik:** check the outcome individually.\n\n**No placement, or below the threshold:** official sources do not define a general route for this case. According to the DAAD admission database, admission is not possible for a Lise graduate who did not take YKS or only has a TYT result. Ask the university you apply to or uni-assist about your case; the decision lies with the university or the competent authority of the federal state.\n\n**What does subject-restricted access mean?** You may only apply for programmes in, or related to, the field you were placed in or studied. There is no official fixed list of \"related\" subjects; the university you apply to assesses this.\n\n**About the score thresholds:** 180 points is anabin/KMK's current assessment (last change: KMK decision of 30 June 2022, continued \"until further notice\"). 170 points was an exception for the 2020 exam year only. These thresholds are part of the German assessment, not Turkish higher-education rules, and can change; check anabin and the university's requirements before you apply.",
      "probe": "What do H+, H+/- and H- actually mean?"
     },
     {
      "line": "What is H+: Direct Admission Right (Hochschulzugangsberechtigung – HZB)",
      "delete": true
     },
     {
      "line": "H+ classification indicates that your diploma grants direct university admission (Hochschulzugangsberechtigung - HZB – higher education entrance qualification) in Germany. This is the most advantageous situation! If your diploma is classified as H+, you can apply directly to a university, just like a German high school graduate, provided you meet the other requirements for a specific program and university (language proficiency, GPA, etc.).",
      "delete": true
     },
     {
      "line": "Which Turkish High Schools are Generally Classified as H+?",
      "delete": true
     },
     {
      "line": "Generally, certain types of high schools in Turkey can receive H+ classification if specific academic conditions are met:",
      "delete": true
     },
     {
      "line": "Anadolu Liseleri (Anatolian High Schools) and Fen Liseleri (Science High Schools): Students graduating from these types of high schools can generally be evaluated as H+ if they have a very good high school GPA and have gained admission to a university in Turkey through the Higher Education Institutions Examination (Yükseköğretim Kurumları Sınavı – YKS). Especially having been placed in a 4-year undergraduate program with a good score in YKS strengthens this status.",
      "delete": true
     },
     {
      "line": "International Baccalaureate (IB) Diploma: If you have an internationally recognized IB diploma, you generally receive direct H+ classification and can apply directly to many universities in Germany.",
      "delete": true
     },
     {
      "line": "Some Private High Schools: Some German schools in Turkey or private high schools that implement certain international curricula may also provide direct H+ equivalence.",
      "delete": true
     },
     {
      "line": "Important Note: A high school diploma and YKS success alone may not be sufficient. There may also be a certain level of success required in subject-specific courses related to the program you are applying for, or additional university requirements. Therefore, you should always carefully check the admission requirements of the university you wish to apply to. (See: /universities page for our university profiles)",
      "delete": true
     },
     {
      "line": "What is H+-: Restricted Admission Right (Fachgebundene HZB)",
      "delete": true
     },
     {
      "line": "H+- classification indicates that your diploma grants university admission in Germany, but with certain restrictions. These restrictions may generally be limited to the field of study you can apply for or may require additional exams/conditions.",
      "delete": true
     },
     {
      "line": "Which Turkish High Schools are Generally Classified as H+- and What are the Restrictions?",
      "delete": true
     },
     {
      "line": "H+- classification usually occurs in the following situations:",
      "delete": true
     },
     {
      "line": "Graduates of Some Anadolu/Fen Liseleri: Students who do not have a very high high school GPA or who do not fully meet a certain success threshold in YKS but have still gained admission to a university in Turkey may fall into this category. The restriction is usually the obligation to study in Germany in the same field as the program they were placed in through YKS. For example, if you were placed in a science-oriented program in Turkey, you may need to apply for a science-oriented program in Germany.",
      "delete": true
     },
     {
      "line": "Imam Hatip Liseleri (Religious Vocational High Schools) and Some Vocational High Schools: Graduates from these types of high schools can generally apply to programs related to their own fields (vocational high school graduates to their own vocational fields). If they wish to apply to a program outside their field, they usually need to take additional exams or attend a Studienkolleg (preparatory college). For example, an Imam Hatip High School graduate who wants to study Theology in Germany can apply directly with H+-, but if they want to study engineering, they may fall into the H- category.",
      "delete": true
     },
     {
      "line": "Associate Degree Graduates: The situation is a bit more complex for students who have graduated from a 2-year associate degree (Meslek Yüksekokulu – Vocational School of Higher Education) program in Turkey. It generally does not grant direct right to complete a bachelor's degree. In some cases, with YKS success following associate degree education and a certain GPA, they may be evaluated as H+- in their own fields, but most often, they face the option of a Studienkolleg (preparatory college) or starting a bachelor's program from scratch. Each university may have different policies on this, so detailed research is very important. (See: /faq page for our associate degree questions)",
      "delete": true
     },
     {
      "line": "Solution: If your diploma is H+-, first check if the program you wish to apply for is compatible with the restrictions. If necessary, consult the university directly or prepare for additional exams.",
      "delete": true
     },
     {
      "line": "What is H-: Studienkolleg Requirement (Keine HZB)",
      "delete": true
     },
     {
      "line": "H- classification means that your diploma does not grant direct university admission in Germany. In this case, attending a Studienkolleg (preparatory college) is generally mandatory to be able to start university in Germany.",
      "delete": true
     },
     {
      "line": "Which Turkish High Schools are Generally Classified as H-?",
      "delete": true
     },
     {
      "line": "H- classification usually occurs in the following situations:",
      "delete": true
     },
     {
      "line": "Açık Öğretim Liseleri (Open Education High Schools): Students graduating from open education high schools in Turkey generally cannot apply directly to universities in Germany and are required to attend a Studienkolleg (preparatory college).",
      "delete": true
     },
     {
      "line": "Meslek Liseleri (Vocational High Schools) (General Rule): The vast majority of vocational high school graduates are classified as H-. Those who wish to apply to a program outside their field or who do not meet specific academic requirements must definitely attend a Studienkolleg (preparatory college). In very rare cases, 4-year vocational high school graduates may be evaluated as H+- in their own fields if they have shown good success in YKS and have been placed in an undergraduate program in Turkey, and have a very good GPA, but this is an exceptional situation and not every university may accept it.",
      "delete": true
     },
     {
      "line": "Imam Hatip Liseleri (Religious Vocational High Schools) (Out-of-Field Application): Imam Hatip High School graduates are generally classified as H- and directed to a Studienkolleg (preparatory college) when they wish to apply to programs outside their own field.",
      "delete": true
     },
     {
      "line": "General High School Diploma (Without YKS Success): If you have graduated from a high school in Turkey but have not taken the YKS or have not gained admission to a university in Turkey, your diploma is generally classified as H-.",
      "delete": true
     },
     {
      "line": "What is Studienkolleg? Studienkolleg (preparatory college) is a one-year program that prepares foreign students for the German university system and language. At the end of the program, you take a qualification exam called FSP (Feststellungsprüfung – assessment test). If you pass this exam successfully, you gain the right to apply to universities in Germany. Studienkollegs are usually divided into specific fields (T-Kurs, M-Kurs, W-Kurs, G-Kurs, S-Kurs), and you need to choose the appropriate course according to the program you wish to apply for. (See: Our /studienkolleg guide)",
      "delete": true
     },
     {
      "line": "The Role of ÖSYM/YKS in Anabin Classification",
      "delete": true
     },
     {
      "line": "In the Anabin system, ÖSYM (Öğrenci Seçme ve Yerleştirme Merkezi – Student Selection and Placement Center) and YKS (Yükseköğretim Kurumları Sınavı – Higher Education Institutions Examination) results play a vital role in the evaluation of Turkish diplomas. Germany considers success in Turkey's university entrance examination as an important criterion in determining the equivalence of a high school diploma in Germany.",
      "delete": true
     },
     {
      "line": "Generally, to receive H+ or H+- classification, in addition to your high school diploma, you are expected to have gained admission to a 4-year undergraduate program in Turkey. This situation is considered an indicator that your education meets a certain academic standard. The program you were placed in through YKS can also affect the field of study you can apply for in Germany.",
      "delete": true
     },
     {
      "line": "Review Details: When you click on your high school type, you will see the H+, H+-, H- classifications applicable to that diploma and related explanations. Detailed information is usually found in the \"Bewertung\" (Evaluation) section.",
      "replace": "Review the details: when you open your certificate, you will see the rules that apply to it (for example, which access route opens together with the ÖSYM result and placement). H+/H- codes are not used for school certificates; they refer to institutions."
     },
     {
      "line": "Question 3: Hello, if a law graduate's diploma is not recognized in Anabin, does it mean they are not suitable for a master's degree in Germany? What needs to be done to get recognition? Answer: Some professions, such as law, have very specific and complex recognition processes in Germany. If you do not see a direct H+ or H+- classification for your law diploma in Anabin, or if there is an expression like \"keine Aussage\" (no statement/information), this usually means that pursuing a master's in law in Germany will be difficult. The legal profession in Germany requires a long education and internship process (Staatsexamen – state examination) that varies from state to state. Foreign law diplomas may generally impose additional exams, courses, or even requirements to start a bachelor's degree from scratch for integration into the German legal system. To get recognition, you first need to contact the Ministry of Justice or the relevant recognition offices of the state you wish to apply to, explain your situation in detail, and find out what additional requirements you need to fulfill. In some cases, an \"LL.M.\" (Master of Laws)",
      "replace": "Question 3: Hello, if a law graduate's diploma is not recognized in Anabin, does it mean they are not suitable for a master's degree in Germany? What needs to be done to get recognition? Answer: anabin contains two separate pieces of information: the **institution status** of the university (H+, H+/-, H-) and the assessment of the **degree type**. An H+ institution alone does not mean equivalence or admission. For a master's application, the university decides on admission and checks whether your degree fits the programme. The legal profession in Germany is regulated and tied to the Staatsexamen; a foreign law degree does not replace it. If your goal is a master's, ask the university about the programme's requirements (some LL.M. programmes are designed for foreign lawyers); for the profession, contact the competent authority of the federal state."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "what-is-anabin-h-h-h-how-is-a-turkish-diploma-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Was ist Anabin H+, H+-, H-? Wie wird ein türkisches Diplom für Deutschland klassifiziert?",
      "replace": "Was bedeuten Anabin H+, H+/- und H-? Wie werden türkische Abschlüsse für Deutschland bewertet?"
     },
     {
      "line": "Träumst du als türkischer Student von einem Studium in Deutschland? Einer der wichtigsten Schritte im Bewerbungsprozess ist es, den Anerkennungsstatus deines Abitur- oder Hochschulabschlusses in Deutschland zu verstehen. Genau hier kommt die Frage was ist anabin h+ h+- h- ins Spiel und kann zu Verwirrung führen. Keine Sorge, in diesem Leitfaden erklären wir dir Schritt für Schritt, wozu die Anabin-Datenbank dient, wie türkische Diplome in diesem System klassifiziert werden und welcher Weg für dich der richtige ist.",
      "replace": "Träumst du als türkischer Student von einem Studium in Deutschland? Einer der wichtigsten Schritte ist zu verstehen, wie dein Schul- oder Hochschulabschluss in Deutschland bewertet wird. Ein häufiger Irrtum: Die anabin-Kürzel H+, H+- und H- beschreiben **Hochschulen**, nicht Schulabschlüsse. Dieser Leitfaden erklärt, was die H-Kürzel wirklich bedeuten, nach welchen Regeln ein türkischer Schulabschluss bewertet wird und wie du deinen eigenen Fall prüfst."
     },
     {
      "line": "Das Anabin-System unterteilt türkische Abitur- und Hochschulabschlüsse grundsätzlich in drei Hauptkategorien: H+, H+-, und H-. Diese Klassifizierung beeinflusst direkt, wie deine Bildungsreise in Deutschland beginnen wird. Schauen wir uns diese Kategorien genauer an.",
      "replace": "## Was bedeuten H+, H+/- und H- eigentlich?\n\nIn anabin beschreiben **H+, H+/- und H-** den Status von **Hochschulen**; sie ordnen keine Schulabschlüsse ein.\n\n- **H+:** Die Institution ist im Herkunftsland als Hochschule anerkannt und gilt in Deutschland als Hochschule.\n- **H+/-:** Für den Institutionstyp ist keine einheitliche Statusfestlegung möglich; die Institution wird gesondert bewertet.\n- **H-:** Die Institution gilt vorläufig oder auf Dauer nicht als Hochschule.\n\nLaut anabin bedeutet H+ lediglich, dass Abschlüsse dieser Institution einer **Gleichwertigkeitsprüfung** unterzogen werden können; eine Vorentscheidung ist damit nicht verbunden. H+ ist also:\n\n- **keine** Hochschulzugangsberechtigung (HZB),\n- **keine automatische Anerkennung** deines Abschlusses,\n- **keine automatische Zulassung**; darüber entscheidet die Hochschule, bei der du dich bewirbst.\n\nDer Abschluss selbst wird in anabin gesondert über den **Abschlusstyp** bewertet (etwa „entspricht“, „gleichwertig“ oder „bedingt vergleichbar“). Auch das ist keine Anerkennungsentscheidung.\n\n## Wie wird ein türkisches Schulabschlusszeugnis in Deutschland bewertet?\n\nOb dein türkischer Schulabschluss einen Hochschulzugang in Deutschland eröffnet, entscheiden nicht die H-Kürzel, sondern die **Länderregeln für türkische Schulabschlüsse** in anabin (anabin → „Schulabschlüsse mit Hochschulzugang“ → Türkei). Das Lise Diplomasi **allein** eröffnet keinen Zugang; die Regeln bewerten es zusammen mit dem ÖSYM-Ergebnis und dem Platzierungsnachweis. Nach der aktuellen Bewertung in anabin (Stand: 30.09.2026):\n\n- **12-jähriges Lise + mehr als 180 Punkte in der YKS in der Punkteart SAY, SÖZ, EA oder DIL + Zuweisung in einen mindestens 4-jährigen Bachelorstudiengang an einer Fakultät:** fachgebundener direkter Hochschulzugang **ohne Studienkolleg** für das zugewiesene Fach und benachbarte Fächer. Eine Einschreibung in der Türkei ist nicht nötig.\n- **Zuweisung in einen 4-jährigen Studiengang an einer universitätsangehörigen Yüksekokul:** fachgebundener direkter Zugang, nur zu **Fachhochschulen**.\n- **Meslek Lisesi (Berufsgymnasium) + mehr als 180 Punkte:** fachbezogenes **Studienkolleg**; nach einem erfolgreich abgeschlossenen Studienjahr in einem Bachelorstudiengang in der Türkei fachgebundener direkter Zugang.\n- **Abgeschlossenes Önlisans + mehr als 150 Punkte im TYT:** fachgebundener direkter Zugang.\n- **Nur ein Jahr an einer Meslek Yüksekokulu (MYO):** Studienkolleg-Weg, nur für Fachhochschulen.\n- **Zwei erfolgreich abgeschlossene Jahre an einer Fernstudienfakultät (Açıköğretim):** fachgebundener direkter Zugang.\n- **Offenes Gymnasium (Açık Öğretim Lisesi):** Einzelfallentscheidung; Prüfungsergebnis und Studienplatz sind zusätzlich nachzuweisen.\n- **Abgeschlossener mindestens 4-jähriger Bachelor:** direkter Zugang für alle Fächer.\n- **Schultypen wie İmam Hatip oder Anadolu Teknik:** das Ergebnis individuell prüfen lassen.\n\n**Keine Zuweisung oder unter der Schwelle:** Offizielle Quellen legen dafür keinen allgemeinen Weg fest. Laut der DAAD-Zulassungsdatenbank ist eine Zulassung für Lise-Absolventen ohne YKS oder nur mit TYT-Ergebnis nicht möglich. Frag die Hochschule, bei der du dich bewirbst, oder uni-assist; entscheiden die Hochschule bzw. die zuständige Stelle des Landes.\n\n**Was heißt fachgebunden?** Du kannst dich nur für Studiengänge im zugewiesenen bzw. studierten Fach und in benachbarten Fächern bewerben. Eine offizielle feste Liste „benachbarter“ Fächer gibt es nicht; das prüft die Hochschule.\n\n**Zu den Punktgrenzen:** 180 Punkte sind die aktuelle Bewertung von anabin/KMK (letzte Änderung: KMK-Beschluss vom 30.06.2022, „bis auf Weiteres“ fortgeführt). 170 Punkte waren eine Ausnahme nur für den Prüfungsjahrgang 2020. Diese Schwellen gehören zur deutschen Bewertung, nicht zu türkischen Hochschulregeln, und können sich ändern; prüfe vor der Bewerbung anabin und die Anforderungen der Hochschule.",
      "probe": "Was bedeuten H+, H+/- und H- eigentlich?"
     },
     {
      "line": "Was ist H+: Direkte Hochschulzugangsberechtigung (HZB)",
      "delete": true
     },
     {
      "line": "Die H+-Klassifizierung zeigt an, dass dein Diplom in Deutschland eine direkte Hochschulzugangsberechtigung (HZB) verleiht. Dies ist die vorteilhafteste Situation! Wenn dein Diplom als H+ klassifiziert ist, kannst du dich, genau wie ein deutscher Abiturient, direkt an einer Universität bewerben, sofern du die anderen Voraussetzungen für einen bestimmten Studiengang und eine bestimmte Universität (Sprachkenntnisse, Notendurchschnitt usw.) erfüllst.",
      "delete": true
     },
     {
      "line": "Welche türkischen Gymnasien werden im Allgemeinen als H+ klassifiziert?",
      "delete": true
     },
     {
      "line": "Im Allgemeinen können bestimmte Gymnasialtypen in der Türkei die H+-Klassifizierung erhalten, wenn bestimmte akademische Bedingungen erfüllt sind:",
      "delete": true
     },
     {
      "line": "Anadolu Liseleri und Fen Liseleri: Absolventen dieser Gymnasialtypen können in der Regel als H+ bewertet werden, wenn sie einen sehr guten Abiturnotendurchschnitt haben und durch die Hochschulzugangsprüfung (Yükseköğretim Kurumları Sınavı – YKS) einen Studienplatz an einer türkischen Universität erhalten haben. Insbesondere die Zulassung zu einem 4-jährigen Bachelorstudiengang mit einem guten Ergebnis in der YKS stärkt diesen Status.",
      "delete": true
     },
     {
      "line": "International Baccalaureate (IB) Diplom: Wenn du ein international anerkanntes IB-Diplom besitzt, erhältst du in der Regel eine direkte H+-Klassifizierung und kannst dich direkt an vielen Universitäten in Deutschland bewerben.",
      "delete": true
     },
     {
      "line": "Einige Privatschulen: Einige deutsche Schulen in der Türkei oder Privatschulen, die bestimmte internationale Lehrpläne anwenden, können ebenfalls eine direkte H+-Gleichwertigkeit ermöglichen.",
      "delete": true
     },
     {
      "line": "Wichtiger Hinweis: Das Abiturzeugnis und der YKS-Erfolg allein reichen möglicherweise nicht aus. Es können auch ein bestimmtes Leistungsniveau in fachbezogenen Kursen für den Studiengang, für den du dich bewirbst, oder zusätzliche Universitätsanforderungen bestehen. Daher solltest du immer die Zulassungsvoraussetzungen der Universität, an der du dich bewerben möchtest, sorgfältig prüfen. (Siehe: Universitätsprofile auf unserer Seite /universities)",
      "delete": true
     },
     {
      "line": "Was ist H+-: Eingeschränkte Hochschulzugangsberechtigung (Fachgebundene HZB)",
      "delete": true
     },
     {
      "line": "Die H+-Klassifizierung zeigt an, dass dein Diplom in Deutschland eine Hochschulzugangsberechtigung verleiht, jedoch mit bestimmten Einschränkungen. Diese Einschränkungen können im Allgemeinen auf den Bereich des Studiengangs beschränkt sein, für den du dich bewerben kannst, oder zusätzliche Prüfungen/Bedingungen erfordern.",
      "delete": true
     },
     {
      "line": "Welche türkischen Gymnasien werden im Allgemeinen als H+- klassifiziert und welche Einschränkungen gibt es?",
      "delete": true
     },
     {
      "line": "H+-Klassifizierung tritt in der Regel in folgenden Situationen auf:",
      "delete": true
     },
     {
      "line": "Absolventen einiger Anadolu/Fen Liseleri: Studierende, die keinen sehr hohen Abiturnotendurchschnitt haben oder eine bestimmte Erfolgsschwelle in der YKS nicht vollständig erreichen, aber dennoch einen Studienplatz an einer türkischen Universität erhalten haben, können in diese Kategorie fallen. Die Einschränkung besteht in der Regel in der Verpflichtung, in Deutschland im selben Fachbereich zu studieren, in dem sie durch die YKS einen Studienplatz erhalten haben. Wenn du beispielsweise in der Türkei einen naturwissenschaftlichen Studiengang belegt hast, musst du dich möglicherweise in Deutschland für einen naturwissenschaftlich orientierten Studiengang bewerben.",
      "delete": true
     },
     {
      "line": "Imam Hatip Liseleri und einige Berufsschulen: Absolventen dieser Schultypen können sich in der Regel für Studiengänge bewerben, die mit ihren eigenen Fachgebieten zusammenhängen (Berufsschulabsolventen für ihre eigenen Berufsfelder). Wenn sie sich für einen fachfremden Studiengang bewerben möchten, müssen sie in der Regel zusätzliche Prüfungen ablegen oder ein Studienkolleg besuchen. Zum Beispiel kann ein Absolvent einer Imam Hatip Lisesi, der in Deutschland Theologie studieren möchte, sich direkt mit H+- bewerben, aber wenn er Ingenieurwesen studieren möchte, kann er in die Kategorie H- fallen.",
      "delete": true
     },
     {
      "line": "Absolventen von zweijährigen Studiengängen (Ön Lisans): Die Situation ist etwas komplexer für Studierende, die einen zweijährigen Studiengang (Meslek Yüksekokulu – Berufsfachschule) in der Türkei abgeschlossen haben. Dies berechtigt in der Regel nicht direkt zum Abschluss eines Bachelorstudiums. In einigen Fällen können sie mit YKS-Erfolg nach dem zweijährigen Studium und einem bestimmten Notendurchschnitt in ihren eigenen Fachgebieten als H+- bewertet werden, aber meistens stehen sie vor der Option eines Studienkollegs oder müssen ein Bachelorstudium von Grund auf neu beginnen. Jede Universität kann hier unterschiedliche Richtlinien haben, daher ist eine detaillierte Recherche sehr wichtig. (Siehe: Fragen zu zweijährigen Studiengängen auf unserer Seite /faq)",
      "delete": true
     },
     {
      "line": "Lösungsweg: Wenn dein Diplom H+- ist, überprüfe zunächst, ob der Studiengang, für den du dich bewerben möchtest, mit den Einschränkungen vereinbar ist. Konsultiere bei Bedarf direkt die Universität oder bereite dich auf zusätzliche Prüfungen vor.",
      "delete": true
     },
     {
      "line": "Was ist H-: Studienkolleg-Pflicht (Keine HZB)",
      "delete": true
     },
     {
      "line": "Die H--Klassifizierung bedeutet, dass dein Diplom in Deutschland keine direkte Hochschulzugangsberechtigung verleiht. In diesem Fall ist der Besuch eines Studienkollegs in der Regel zwingend erforderlich, um ein Studium in Deutschland aufnehmen zu können.",
      "delete": true
     },
     {
      "line": "Welche türkischen Gymnasien werden im Allgemeinen als H- klassifiziert?",
      "delete": true
     },
     {
      "line": "Die H--Klassifizierung tritt in der Regel in folgenden Situationen auf:",
      "delete": true
     },
     {
      "line": "Açık Öğretim Liseleri (Offene Gymnasien): Absolventen von offenen Gymnasien in der Türkei können sich in der Regel nicht direkt an Universitäten in Deutschland bewerben und müssen ein Studienkolleg besuchen.",
      "delete": true
     },
     {
      "line": "Berufsschulen (Allgemeine Regel): Die überwiegende Mehrheit der Berufsschulabsolventen wird als H- klassifiziert. Wer sich für einen fachfremden Studiengang bewerben möchte oder bestimmte Leistungsvoraussetzungen nicht erfüllt, muss unbedingt ein Studienkolleg besuchen. In sehr seltenen Fällen können Absolventen von 4-jährigen Berufsschulen, die in der YKS gute Leistungen erbracht und einen Studienplatz in einem Bachelorstudiengang in der Türkei erhalten haben und einen sehr guten Notendurchschnitt aufweisen, als H+- bewertet werden, dies ist jedoch eine Ausnahme und wird nicht von jeder Universität akzeptiert.",
      "delete": true
     },
     {
      "line": "Imam Hatip Liseleri (Fachfremde Bewerbung): Absolventen von Imam Hatip Liseleri werden in der Regel als H- eingestuft und an ein Studienkolleg verwiesen, wenn sie sich für fachfremde Studiengänge bewerben möchten.",
      "delete": true
     },
     {
      "line": "Allgemeines Abiturzeugnis (Ohne YKS-Erfolg): Wenn du ein türkisches Gymnasium abgeschlossen, aber nicht an der YKS teilgenommen oder keinen Studienplatz an einer türkischen Universität erhalten hast, wird dein Diplom in der Regel als H- klassifiziert.",
      "delete": true
     },
     {
      "line": "Was ist ein Studienkolleg? Ein Studienkolleg ist ein einjähriges Programm, das ausländische Studierende auf das deutsche Hochschulsystem und die deutsche Sprache vorbereitet. Am Ende des Programms legst du eine Qualifikationsprüfung namens FSP (Feststellungsprüfung) ab. Wenn du diese Prüfung erfolgreich bestehst, erhältst du die Berechtigung, dich an Universitäten in Deutschland zu bewerben. Studienkollegs sind in der Regel in bestimmte Fachbereiche (T-Kurs, M-Kurs, W-Kurs, G-Kurs, S-Kurs) unterteilt, und du musst den geeigneten Kurs entsprechend dem Studiengang wählen, für den du dich bewerben möchtest. (Siehe: Unser Leitfaden /studienkolleg)",
      "delete": true
     },
     {
      "line": "Die Rolle von ÖSYM/YKS bei der Anabin-Klassifizierung",
      "delete": true
     },
     {
      "line": "Im Anabin-System spielen die Ergebnisse von ÖSYM (Öğrenci Seçme ve Yerleştirme Merkezi – Zentrum für Studienplatzvergabe und -auswahl) und YKS (Yükseköğretim Kurumları Sınavı – Hochschulzugangsprüfung) eine entscheidende Rolle bei der Bewertung türkischer Diplome. Deutschland betrachtet den Erfolg bei der türkischen Hochschulzugangsprüfung als wichtiges Kriterium für die Feststellung der Gleichwertigkeit eines Abiturzeugnisses in Deutschland.",
      "delete": true
     },
     {
      "line": "Im Allgemeinen wird erwartet, dass du für eine H+- oder H+-Klassifizierung zusätzlich zu deinem Abiturzeugnis einen Studienplatz in einem 4-jährigen Bachelorstudiengang in der Türkei erhalten hast. Diese Situation gilt als Indikator dafür, dass deine Ausbildung einem bestimmten akademischen Standard entspricht. Der Studiengang, in den du durch die YKS aufgenommen wurdest, kann auch den Fachbereich beeinflussen, für den du dich in Deutschland bewerben kannst.",
      "delete": true
     },
     {
      "line": "Details prüfen: Wenn du auf deinen Schultyp klickst, siehst du die für dieses Diplom geltenden H+, H+-, H--Klassifizierungen und die entsprechenden Erläuterungen. Detaillierte Informationen findest du in der Regel im Abschnitt \"Bewertung\".",
      "replace": "Details prüfen: Wenn du deinen Abschluss öffnest, siehst du die geltenden Regeln (zum Beispiel, welcher Zugangsweg sich zusammen mit dem ÖSYM-Ergebnis und der Zuweisung ergibt). H+/H- werden für Schulabschlüsse nicht verwendet; sie beziehen sich auf Hochschulen."
     },
     {
      "line": "Frage 3: Hallo, wenn das Diplom eines Juristen in Anabin nicht anerkannt wird, bedeutet das, dass er in Deutschland keinen Master machen kann? Was muss man tun, um eine Anerkennung zu erhalten? Antwort: Einige Berufe, wie zum Beispiel Jura, haben in Deutschland sehr spezifische und komplexe Anerkennungsprozesse. Wenn du in Anabin keine direkte H+- oder H+-Klassifizierung für dein Jura-Diplom siehst oder ein Ausdruck wie \"keine Aussage\" vorhanden ist, bedeutet dies in der Regel, dass ein Jura-Master in Deutschland schwierig sein wird. Der juristische Beruf in Deutschland erfordert einen langen Ausbildungs- und Praktikumsprozess (Staatsexamen), der von Bundesland zu Bundesland variiert. Ausländische Jura-Diplome können in der Regel zusätzliche Prüfungen, Kurse oder sogar die Auflage, ein Bachelorstudium von Grund auf neu zu beginnen, für die Integration in das deutsche Rechtssystem mit sich bringen. Um eine Anerkennung zu erhalten, musst du dich zunächst an das Justizministerium oder die zuständigen Anerkennungsstellen des Bundeslandes wenden, in dem du dich bewerben möchtest, deine Situation detailliert erläutern und herausfinden, welche zusätzlichen Anforderungen du erfüllen musst. In einigen Fällen kann ein \"LL.M.\" (Master of Laws)",
      "replace": "Frage 3: Hallo, wenn das Diplom eines Juristen in Anabin nicht anerkannt wird, bedeutet das, dass er in Deutschland keinen Master machen kann? Was muss man tun, um eine Anerkennung zu erhalten? Antwort: anabin enthält zwei getrennte Angaben: den **Institutionsstatus** der Hochschule (H+, H+/-, H-) und die Bewertung des **Abschlusstyps**. Ein H+ der Institution bedeutet allein weder Gleichwertigkeit noch Zulassung. Bei einer Masterbewerbung entscheidet die Hochschule über die Zulassung und prüft, ob dein Abschluss zum Studiengang passt. Der juristische Beruf ist in Deutschland reglementiert und an das Staatsexamen gebunden; ein ausländischer Jura-Abschluss ersetzt es nicht. Geht es dir um einen Master, frag die Hochschule nach den Zulassungsvoraussetzungen (manche LL.M.-Programme richten sich an ausländische Juristen); für den Beruf wende dich an die zuständige Stelle des Bundeslandes."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "studienkolleg-center-list-2026-public-private-institutions",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "Almanya'da üniversite okuma hayali kuran bir Türk öğrenci misin? Lise diplomanın Almanya'da doğrudan üniversiteye giriş için yeterli olmadığını öğrendiysen veya \"H-\" ya da \"H+\" durumun kafanı karıştırıyorsa, yalnız değilsin. Çoğu Türk öğrencisi için Almanya'ya açılan kapı Studienkolleg'den geçiyor. Peki, hangi Studienkolleg sana uygun? Devlet mi, özel mi? Berlin mi, Münih mi? 2026'da seni bekleyen seçenekleri, kabul şartlarını ve tüm merak ettiklerini bu kapsamlı rehberde bulacaksın. ApplyToGerman (AlmanyaUni) ile doğru kararı ver!",
      "replace": "Almanya'da üniversite okuma hayali kuran bir Türk öğrenci misin? Studienkolleg'e ihtiyacın olup olmadığı lise türünden değil, **giriş hakkı yolundan** belirlenir; bazı Türk adaylar doğrudan başvurabilir, bazıları için Studienkolleg gerekir. Eğer senin yolun Studienkolleg'den geçiyorsa hangi kurum sana uygun? Devlet mi, özel mi? Berlin mi, Münih mi? Bu kapsamlı rehberde 2026'da seni bekleyen seçenekleri, kabul şartlarını ve merak ettiğin her şeyi bulacaksın. ApplyToGerman (AlmanyaUni) ile doğru kararı ver!"
     },
     {
      "line": "Peki, kimin Studienkolleg yapması gerekiyor? Alman yükseköğretim sistemi, lise diplomanızın Alman Abitur'una ne kadar denk olduğuna göre sizi H- veya H+ olarak sınıflandırır.",
      "replace": "### Studienkolleg'e ihtiyacım var mı?\n\nBu, lise türünden değil, **giriş hakkı yolundan** belirlenir. anabin'in güncel değerlendirmesine göre özetle:\n\n- **12 yıllık lise + YKS'de 180 puanın üzerinde (SAY/SÖZ/EA/DİL) + fakültede 4 yıllık lisansa yerleşme:** alana bağlı doğrudan giriş; Studienkolleg gerekmez.\n- **Meslek lisesi + 180 puanın üzerinde:** alana yönelik Studienkolleg; Türkiye'de başarıyla tamamlanmış bir yıllık lisans öğrenimiyle doğrudan giriş.\n- **MYO'da yalnızca bir yıl:** Studienkolleg (yalnızca Fachhochschule'ler için).\n- **Tamamlanmış önlisans ya da açıköğretimde iki başarılı yıl:** alana bağlı doğrudan giriş. **Tamamlanmış 4 yıllık lisans:** tüm alanlarda doğrudan giriş.\n- **Yerleşme yoksa ya da eşiğin altındaysan:** resmî kaynaklar genel bir yol tanımlamıyor; üniversiteye ya da uni-assist'e sor.\n\nH+/H- kodları okul diplomasına değil, kurumlara ilişkindir. Kesin kararı başvurduğun üniversite (ya da eyaletin yetkili makamı) verir. Ayrıntılar ve kaynaklar: [anabin rehberi](/tr/blog/what-is-anabin-h-h-h-how-is-a-turkish-diploma)",
      "probe": "Studienkolleg'e ihtiyacım var mı?"
     },
     {
      "line": "H- (Hochschulzugangsberechtigung nicht direkt): Lise diplomanız Alman Abitur'una doğrudan denk değilse Studienkolleg yapmanız zorunludur. Çoğu Türk lise mezunu bu kategoriye girer.",
      "delete": true
     },
     {
      "line": "H+ (Hochschulzugangsberechtigung direkt): Lise diplomanız Alman Abitur'una denk veya Türkiye'de 4 yıllık bir lisans programını tamamladıysanız, genellikle Studienkolleg yapmanıza gerek kalmaz ve doğrudan üniversiteye başvurabilirsiniz.",
      "delete": true
     },
     {
      "line": "\"Studienkolleg olmadan kabul eden üniversiteler var mı?\" Topluluktan gelen bu soruya cevabımız: Nadiren ve belirli şartlar altında. Eğer lise diplomanız Anabin veri tabanında H+ olarak listeleniyorsa veya Türkiye'de bir lisans programını tamamladıysanız, bazı üniversiteler sizi doğrudan kabul edebilir. Ancak, bu durum bölümden bölüme ve üniversiteden üniversiteye değişir. En güncel bilgiyi her zaman başvurmak istediğiniz üniversitenin resmi web sitesinden veya Anabin veri tabanından kontrol etmelisiniz.",
      "replace": "\"Studienkolleg olmadan kabul eden üniversiteler var mı?\" Topluluktan gelen bu soruya cevabımız: Bu, üniversiteden çok **senin giriş hakkı yoluna** bağlıdır. Örneğin 12 yıllık lise ve YKS'de gerekli puanla bir fakültede 4 yıllık lisansa yerleştiysen, yerleştiğin alanda ve yakın alanlarda Studienkolleg olmadan başvurabilirsin; Türkiye'de lisansı tamamladıysan tüm alanlara başvurabilirsin. Programın kendi şartlarını her zaman üniversitenin resmî sayfasından kontrol et."
     },
     {
      "line": "\"Vpde'de (Vorprüfungsdokumentation) belirtilir mi?\" Evet, genellikle Vpde belgesinde (ön inceleme belgesi) veya Anabin veri tabanı çıktısında lise diplomanızın Almanya'da doğrudan üniversiteye giriş için yeterli olup olmadığı (H+ veya H-) ve dolayısıyla Studienkolleg'e ihtiyacınız olup olmadığı açıkça belirtilir. Bu belge, Uni-Assist gibi kurumlar aracılığıyla yaptığınız başvurular sonucunda size gönderilir.",
      "replace": "\"Vpde'de (Vorprüfungsdokumentation) belirtilir mi?\" uni-assist üzerinden başvurduğunda ön inceleme sonucu (örneğin VPD), doğrudan giriş mi yoksa Studienkolleg mi gerektiğine dair bir değerlendirme içerebilir. Bu değerlendirme H+/H- kodlarına değil, okul diplomana ilişkin kurallara dayanır; kabul kararını ise üniversite verir."
     },
     {
      "line": "Meslek Lisesi Mezunları İçin Not: \"Meslek liseliyim, studienkolleg yapmam gerekliymiş.\" veya \"Denklik yapsam orada kendi bölümüm ile alakalı, orada okuyabilir miyim studienkolleg yapmadan?\" gibi sorular da sıklıkla geliyor. Meslek lisesi mezunlarının durumu genellikle H- olarak değerlendirilir ve Studienkolleg yapmaları zorunludur. Hatta bazı durumlarda, meslek lisesi mezunları sadece kendi alanlarıyla ilgili bir Studienkolleg kursuna (örneğin teknik alanlar için T-Kurs) başvurabilirler. Denklik işlemi, diplomanızın Almanya'daki karşılığını belirler ve eğer H- ise, Studienkolleg şartı genellikle kalkmaz.",
      "replace": "Meslek Lisesi Mezunları İçin Not: \"Meslek liseliyim, studienkolleg yapmam gerekliymiş\" gibi sorular sıkça geliyor. anabin'in güncel değerlendirmesine göre meslek lisesi diploması, YKS'de 180 puanın üzerinde bir sonuçla **alana yönelik Studienkolleg** yoluna açılır; bu yüzden genellikle kendi alanınla ilgili bir kursa (örneğin teknik alanlar için T-Kurs) yönlendirilirsin. Türkiye'de bir yıllık lisans öğrenimini başarıyla tamamladıysan, okuduğun alanda ve yakın alanlarda doğrudan başvurabilirsin. \"H-\" gibi bir kod okul diplomaları için kullanılmaz."
     },
     {
      "line": "Soru 1: Merhabalar, eğer bir öğrenci studienkolleg yapmak zorunda ise bu Vpde de belirtilir mi yoksa buna okul mu karar verir? Cevap: Evet, Studienkolleg yapmanız gerekip gerekmediği genellikle Uni-Assist'ten alacağınız Vpde (Vorprüfungsdokumentation) belgesinde veya Anabin veri tabanında lise diplomanızın Alman Abitur'u ile denkliği (H- veya H+) belirtilerek açıkça ifade edilir. Üniversiteler bu belgeye göre karar verir. Yani, Vpde belgesi bu konuda belirleyicidir.",
      "replace": "Soru 1: Merhabalar, eğer bir öğrenci studienkolleg yapmak zorunda ise bu Vpde de belirtilir mi yoksa buna okul mu karar verir? Cevap: uni-assist'in ön incelemesi (örneğin VPD) doğrudan giriş mi yoksa Studienkolleg mi gerektiğine dair bir değerlendirme içerebilir; bu değerlendirme okul diploman için geçerli kurallara dayanır, H+/H- kodlarına değil. Kabul kararını ve Studienkolleg'e yönlendirmeyi başvurduğun üniversite (ya da ilgili Studienkolleg) verir."
     },
     {
      "line": "Soru 2: Hangi üniversitelerin studienkolleg olmadan kabul ettiğini nereden öğrenebiliriz? Cevap: Studienkolleg olmadan kabul, genellikle lise diplomanızın Anabin veri tabanında H+ olarak listelenmesi veya Türkiye'de 4 yıllık bir lisans programını tamamlamış olmanız durumunda mümkündür. Bu bilgiyi Anabin veri tabanından kontrol edebilir, ardından başvuru yapmak istediğiniz üniversitenin uluslararası öğrenciler için kabul şartları sayfasını inceleyebilirsiniz. Her üniversite ve bölümün kendi özel şartları olabileceği için doğrudan üniversitenin web sitesi en güvenilir kaynaktır.",
      "replace": "Soru 2: Hangi üniversitelerin studienkolleg olmadan kabul ettiğini nereden öğrenebiliriz? Cevap: Studienkolleg'siz başvuru, üniversiteden çok **senin giriş hakkı yoluna** bağlıdır: örneğin 12 yıllık lise ve YKS'de gerekli puanla bir fakültede 4 yıllık lisansa yerleşme (yerleşilen alan ve yakın alanlar için) ya da Türkiye'de tamamlanmış bir lisans. Kendi yolunu anabin'in Türkiye okul diploması kurallarından kontrol et, ardından başvurmak istediğin programın uluslararası öğrenci kabul şartlarını üniversitenin sayfasından incele."
     },
     {
      "line": "Soru 3: Merhaba, esenlikler dilerim. Ben meslek liseliyim, studienkolleg yapmam gerekliymiş. Eylül için de galiba bu studienkolleg kursu varmış bilgisi olan yazabilir mi? Cevap: Evet, meslek lisesi mezunlarının genellikle Studienkolleg yapması zorunludur (H- durumu). Studienkolleg kursları genellikle yılda iki kez, Kış Dönemi (Eylül/Ekim başlangıçlı) ve Yaz Dönemi (Ocak/Şubat başlangıçlı) için öğrenci kabul eder. Eylül için olan kurslar mevcuttur. Baş",
      "replace": "Soru 3: Merhaba, esenlikler dilerim. Ben meslek liseliyim, studienkolleg yapmam gerekliymiş. Eylül için de galiba bu studienkolleg kursu varmış bilgisi olan yazabilir mi? Cevap: Meslek lisesi mezunları için anabin'in güncel değerlendirmesi YKS'de 180 puanın üzerinde bir sonuçla alana yönelik Studienkolleg yolunu öngörüyor. Studienkolleg'ler genellikle yılda iki kez, Kış Dönemi (Eylül/Ekim başlangıçlı) ve Yaz Dönemi (Ocak/Şubat başlangıçlı) için öğrenci alır; hangi dönemde hangi kursun açıldığını ve başvuru tarihlerini ilgili Studienkolleg'in resmî sayfasından kontrol et."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "studienkolleg-center-list-2026-public-private-institutions-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "Are you a Turkish student dreaming of studying at a university in Germany? If you've learned that your high school diploma isn't directly sufficient for university admission in Germany, or if your \"H-\" or \"H+\" status confuses you, you're not alone. For most Turkish students, the door to Germany opens through Studienkolleg (preparatory college). So, which Studienkolleg is right for you? Public or private? Berlin or Munich? In this comprehensive guide, you'll find the options awaiting you in 2026, admission requirements, and everything else you're curious about. Make the right decision with ApplyToGerman (AlmanyaUni)!",
      "replace": "Are you a Turkish student dreaming of studying at a university in Germany? Whether you need a Studienkolleg depends on your **access route**, not on your type of high school: some Turkish applicants can apply directly, others need a Studienkolleg first. If your route goes through a Studienkolleg, which one is right for you? Public or private? Berlin or Munich? In this guide you'll find the options for 2026, admission requirements and everything else you're curious about. Make the right decision with ApplyToGerman (AlmanyaUni)!"
     },
     {
      "line": "So, who needs to attend a Studienkolleg? The German higher education system classifies you as H- or H+ based on how equivalent your high school diploma is to the German Abitur.",
      "replace": "### Do I need a Studienkolleg?\n\nThat depends on your **access route**, not on your type of high school. Based on anabin's current assessment, in short:\n\n- **12-year Lise + more than 180 points in YKS (SAY/SÖZ/EA/DIL) + placement in a 4-year bachelor's at a faculty:** direct, subject-restricted access; no Studienkolleg needed.\n- **Vocational high school + more than 180 points:** subject-oriented Studienkolleg; after one successful year of bachelor's study in Türkiye, direct access.\n- **Only one year at an MYO:** Studienkolleg (Fachhochschulen only).\n- **Completed Önlisans, or two successful years in open education:** direct, subject-restricted access. **Completed 4-year bachelor's:** direct access for all subjects.\n- **No placement, or below the threshold:** official sources define no general route; ask the university or uni-assist.\n\nH+/H- codes refer to institutions, not to school certificates. The final decision lies with the university you apply to (or the competent state authority). Details and sources: [anabin guide](/en/blog/what-is-anabin-h-h-h-how-is-a-turkish-diploma-en)",
      "probe": "Do I need a Studienkolleg?"
     },
     {
      "line": "H- (Hochschulzugangsberechtigung nicht direkt) (direct university entrance qualification not given): If your high school diploma is not directly equivalent to the German Abitur, attending a Studienkolleg is mandatory. Most Turkish high school graduates fall into this category.",
      "delete": true
     },
     {
      "line": "H+ (Hochschulzugangsberechtigung direkt) (direct university entrance qualification given): If your high school diploma is equivalent to the German Abitur, or if you have completed a 4-year bachelor's program in Turkey, you generally do not need to attend a Studienkolleg and can apply directly to universities.",
      "delete": true
     },
     {
      "line": "\"Are there universities that accept students without a Studienkolleg?\" Our answer to this question from the community is: Rarely and under specific conditions. If your high school diploma is listed as H+ in the Anabin (database for evaluating foreign educational qualifications) database or if you have completed a bachelor's program in Turkey, some universities may accept you directly. However, this varies from program to program and from university to university. You should always check the most up-to-date information on the official website of the university you wish to apply to or in the Anabin database.",
      "replace": "\"Are there universities that accept students without a Studienkolleg?\" Our answer to this community question: it depends less on the university than on **your access route**. For example, with a 12-year Lise and a placement in a 4-year bachelor's at a faculty with the required YKS score, you can apply without a Studienkolleg for the placed subject and related subjects; with a completed bachelor's from Türkiye, you can apply for all subjects. Always check the programme's own requirements on the university's official website."
     },
     {
      "line": "\"Is it stated in the Vpde (Vorprüfungsdokumentation)?\" Yes, it is usually clearly stated in the Vpde (Vorprüfungsdokumentation) (pre-examination documentation) document or the Anabin database printout whether your high school diploma is sufficient for direct university admission in Germany (H+ or H-) and therefore whether you need a Studienkolleg. This document is sent to you as a result of applications you make through institutions like Uni-Assist (service institution for international student applications).",
      "replace": "\"Is it stated in the Vpde (Vorprüfungsdokumentation)?\" If you apply via uni-assist, the preliminary review (for example the VPD) can include an assessment of whether you have direct access or need a Studienkolleg. This assessment is based on the rules for your school certificate, not on H+/H- codes; the admission decision is made by the university."
     },
     {
      "line": "Note for Vocational High School Graduates: Questions like \"I'm a vocational high school graduate, do I need to attend a Studienkolleg?\" or \"If I get my diploma recognized, can I study in my field there without attending a Studienkolleg?\" are also frequently asked. The situation for vocational high school graduates is generally classified as H-, and attending a Studienkolleg is mandatory for them. In some cases, vocational high school graduates can only apply for a Studienkolleg course related to their field (e.g., a T-Kurs (technical course) for technical fields). The recognition process determines the equivalent of your diploma in Germany, and if it's H-, the Studienkolleg requirement usually does not disappear.",
      "replace": "Note for Vocational High School Graduates: Questions like \"I'm a vocational high school graduate, do I need a Studienkolleg?\" come up often. According to anabin's current assessment, a vocational high school diploma with more than 180 points in YKS opens the **subject-oriented Studienkolleg** route, so you are usually directed to a course related to your field (e.g., a T-Kurs for technical fields). If you have successfully completed one year of bachelor's study in Türkiye, you can apply directly for that field and related fields. Codes like \"H-\" are not used for school certificates."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "studienkolleg-center-list-2026-public-private-institutions-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Sind Sie ein türkischer Student, der davon träumt, an einer Universität in Deutschland zu studieren? Wenn Sie erfahren haben, dass Ihr Abitur in Deutschland nicht direkt für die Hochschulzulassung ausreicht, oder wenn Ihr \"H-\" oder \"H+\" Status Sie verwirrt, sind Sie nicht allein. Für die meisten türkischen Studenten führt der Weg nach Deutschland über das Studienkolleg. Welches Studienkolleg ist also das Richtige für Sie? Staatlich oder privat? Berlin oder München? In diesem umfassenden Leitfaden finden Sie die Optionen, die Sie 2026 erwarten, die Zulassungsvoraussetzungen und alles, was Sie sonst noch wissen möchten. Treffen Sie die richtige Entscheidung mit ApplyToGerman (AlmanyaUni)!",
      "replace": "Träumen Sie als türkischer Student von einem Studium an einer deutschen Universität? Ob Sie ein Studienkolleg brauchen, hängt von Ihrem **Zugangsweg** ab, nicht von der Art Ihres Gymnasiums: Manche türkischen Bewerber können sich direkt bewerben, andere brauchen zuerst ein Studienkolleg. Wenn Ihr Weg über ein Studienkolleg führt: Welches passt zu Ihnen? Staatlich oder privat? Berlin oder München? In diesem Leitfaden finden Sie die Optionen für 2026, die Zulassungsvoraussetzungen und alles Weitere. Treffen Sie mit ApplyToGerman (AlmanyaUni) die richtige Entscheidung!"
     },
     {
      "line": "Wer muss ein Studienkolleg besuchen? Das deutsche Hochschulsystem klassifiziert Sie als H- oder H+, je nachdem, wie Ihr Abitur dem deutschen Abitur entspricht.",
      "replace": "### Brauche ich ein Studienkolleg?\n\nDas hängt von deinem **Zugangsweg** ab, nicht von der Art deines Gymnasiums. Nach der aktuellen Bewertung in anabin kurz zusammengefasst:\n\n- **12-jähriges Lise + mehr als 180 Punkte in der YKS (SAY/SÖZ/EA/DIL) + Zuweisung in einen 4-jährigen Bachelor an einer Fakultät:** fachgebundener direkter Zugang; kein Studienkolleg nötig.\n- **Meslek Lisesi + mehr als 180 Punkte:** fachbezogenes Studienkolleg; nach einem erfolgreichen Bachelor-Studienjahr in der Türkei direkter Zugang.\n- **Nur ein Jahr an einer MYO:** Studienkolleg (nur für Fachhochschulen).\n- **Abgeschlossenes Önlisans oder zwei erfolgreiche Jahre im Fernstudium:** fachgebundener direkter Zugang. **Abgeschlossener 4-jähriger Bachelor:** direkter Zugang für alle Fächer.\n- **Keine Zuweisung oder unter der Schwelle:** Offizielle Quellen legen keinen allgemeinen Weg fest; frag die Hochschule oder uni-assist.\n\nH+/H- beziehen sich auf Hochschulen, nicht auf Schulabschlüsse. Die endgültige Entscheidung trifft die Hochschule, bei der du dich bewirbst (bzw. die zuständige Stelle des Landes). Details und Quellen: [anabin-Leitfaden](/de/blog/what-is-anabin-h-h-h-how-is-a-turkish-diploma-de)",
      "probe": "Brauche ich ein Studienkolleg?"
     },
     {
      "line": "H- (Hochschulzugangsberechtigung nicht direkt): Wenn Ihr Abiturzeugnis dem deutschen Abitur nicht direkt gleichwertig ist, ist der Besuch eines Studienkollegs obligatorisch. Die meisten türkischen Abiturienten fallen in diese Kategorie.",
      "delete": true
     },
     {
      "line": "H+ (Hochschulzugangsberechtigung direkt): Wenn Ihr Abiturzeugnis dem deutschen Abitur gleichwertig ist oder Sie ein 4-jähriges Bachelorstudium in der Türkei abgeschlossen haben, müssen Sie in der Regel kein Studienkolleg besuchen und können sich direkt an Universitäten bewerben.",
      "delete": true
     },
     {
      "line": "\"Gibt es Universitäten, die ohne Studienkolleg zulassen?\" Unsere Antwort auf diese Frage aus der Community lautet: Selten und unter bestimmten Bedingungen. Wenn Ihr Abiturzeugnis in der Anabin-Datenbank als H+ aufgeführt ist oder Sie ein Bachelorstudium in der Türkei abgeschlossen haben, können einige Universitäten Sie direkt zulassen. Dies variiert jedoch von Studiengang zu Studiengang und von Universität zu Universität. Die aktuellsten Informationen sollten Sie immer auf der offiziellen Website der Universität, an der Sie sich bewerben möchten, oder in der Anabin-Datenbank überprüfen.",
      "replace": "„Gibt es Universitäten, die ohne Studienkolleg zulassen?“ Unsere Antwort auf diese Frage aus der Community: Das hängt weniger von der Universität als von **Ihrem Zugangsweg** ab. Mit einem 12-jährigen Lise und einer Zuweisung in einen 4-jährigen Bachelor an einer Fakultät mit der nötigen YKS-Punktzahl können Sie sich zum Beispiel ohne Studienkolleg für das zugewiesene und benachbarte Fächer bewerben; mit einem abgeschlossenen Bachelor aus der Türkei für alle Fächer. Prüfen Sie die Anforderungen des Studiengangs immer auf der offiziellen Website der Hochschule."
     },
     {
      "line": "\"Wird es in der Vpde (Vorprüfungsdokumentation) angegeben?\" Ja, in der Regel wird im Vpde (Vorprüfungsdokumentation)-Dokument oder im Anabin-Datenbankauszug klar angegeben, ob Ihr Abiturzeugnis für die direkte Hochschulzulassung in Deutschland ausreicht (H+ oder H-) und ob Sie somit ein Studienkolleg benötigen. Dieses Dokument wird Ihnen infolge von Bewerbungen zugesandt, die Sie über Institutionen wie Uni-Assist einreichen.",
      "replace": "„Wird es in der Vpde (Vorprüfungsdokumentation) angegeben?“ Bei einer Bewerbung über uni-assist kann die Vorprüfung (zum Beispiel die VPD) eine Einschätzung enthalten, ob Sie direkten Zugang haben oder ein Studienkolleg brauchen. Diese Einschätzung beruht auf den Regeln für Ihren Schulabschluss, nicht auf H+/H--Kürzeln; über die Zulassung entscheidet die Hochschule."
     },
     {
      "line": "Hinweis für Absolventen von Berufsfachschulen: Fragen wie \"Ich bin Absolvent einer Berufsfachschule, muss ich ein Studienkolleg besuchen?\" oder \"Wenn ich mein Zeugnis anerkennen lasse, kann ich dort in meinem Fachbereich studieren, ohne ein Studienkolleg zu besuchen?\" werden ebenfalls häufig gestellt. Die Situation für Absolventen von Berufsfachschulen wird in der Regel als H- eingestuft, und der Besuch eines Studienkollegs ist für sie obligatorisch. In einigen Fällen können Absolventen von Berufsfachschulen nur einen Studienkolleg-Kurs besuchen, der ihrem Fachbereich entspricht (z.B. ein T-Kurs für technische Bereiche). Der Anerkennungsprozess bestimmt die Entsprechung Ihres Diploms in Deutschland, und wenn es H- ist, entfällt die Studienkolleg-Pflicht in der Regel nicht.",
      "replace": "Hinweis für Absolventen berufsbildender Schulen: Fragen wie „Ich habe eine Meslek Lisesi abgeschlossen, brauche ich ein Studienkolleg?“ kommen häufig. Nach der aktuellen Bewertung in anabin eröffnet das Diplom einer Meslek Lisesi mit mehr als 180 YKS-Punkten den Weg über ein **fachbezogenes Studienkolleg**; Sie werden daher meist einem Kurs Ihres Fachgebiets zugeordnet (z. B. T-Kurs für technische Fächer). Nach einem erfolgreich abgeschlossenen Bachelor-Studienjahr in der Türkei können Sie sich direkt für dieses und benachbarte Fächer bewerben. Kürzel wie „H-“ werden für Schulabschlüsse nicht verwendet."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "studienkolleg-guide-2026-who-needs-it-which-course-which-school",
   "locale": "tr",
   "md": {
    "content_md": [
     {
      "line": "30 saniye özet: Türk lise mezunlarının çoğu doğrudan Alman üniversitesine başvuramaz. Bir yıllık Studienkolleg (ön hazırlık) zorunlu. Sonunda Feststellungsprüfung (FSP) sınavı geçilirse Alman lise diploması eşdeğeri (HZB) elde edilir. 5 kurs tipi var, hangi üniversite bölümüne gideceksen ona göre seçilir: T-Kurs (teknik), M-Kurs (tıp/biyo), W-Kurs (ekonomi), G-Kurs (sosyal), S-Kurs (filoloji).",
      "replace": "**30 saniye özet:** Studienkolleg'e ihtiyacın olup olmadığı lise türüne değil, **giriş hakkı yoluna** bağlıdır: örneğin YKS'de gerekli puanla bir fakültede 4 yıllık lisansa yerleşen lise mezunu yerleştiği alanda doğrudan başvurabilir; meslek lisesi mezunları ve MYO'da yalnızca bir yıl okuyanlar için Studienkolleg yolu vardır. Studienkolleg sonunda **Feststellungsprüfung (FSP)** geçilirse Almanya'da üniversiteye giriş hakkı elde edilir. 5 kurs tipi var, hangi bölüme gideceksen ona göre seçilir: T-Kurs (teknik), M-Kurs (tıp/biyo), W-Kurs (ekonomi), G-Kurs (beşerî bilimler), S-Kurs (dil)."
     },
     {
      "line": "Türk eğitim sistemindeki herkes değil! Karar matrisi:",
      "replace": "Bu, lise türünden değil, **giriş hakkı yolundan** belirlenir. anabin'in güncel değerlendirmesine göre özetle:\n\n- **12 yıllık lise + YKS'de 180 puanın üzerinde (SAY/SÖZ/EA/DİL) + fakültede 4 yıllık lisansa yerleşme:** alana bağlı doğrudan giriş; Studienkolleg gerekmez.\n- **Meslek lisesi + 180 puanın üzerinde:** alana yönelik Studienkolleg; Türkiye'de başarıyla tamamlanmış bir yıllık lisans öğrenimiyle doğrudan giriş.\n- **MYO'da yalnızca bir yıl:** Studienkolleg (yalnızca Fachhochschule'ler için).\n- **Tamamlanmış önlisans ya da açıköğretimde iki başarılı yıl:** alana bağlı doğrudan giriş. **Tamamlanmış 4 yıllık lisans:** tüm alanlarda doğrudan giriş.\n- **Yerleşme yoksa ya da eşiğin altındaysan:** resmî kaynaklar genel bir yol tanımlamıyor; üniversiteye ya da uni-assist'e sor.\n\nH+/H- kodları okul diplomasına değil, kurumlara ilişkindir. Kesin kararı başvurduğun üniversite (ya da eyaletin yetkili makamı) verir. Ayrıntılar ve kaynaklar: [anabin rehberi](/tr/blog/what-is-anabin-h-h-h-how-is-a-turkish-diploma)",
      "probe": "Bu, lise türünden değil, **giriş hakkı yolundan** belirlenir. anabin'in güncel değerlendirmesine göre özetle:"
     },
     {
      "line": "| Türk eğitim durumun | Studienkolleg gerekli mi? |",
      "delete": true
     },
     {
      "line": "|---|---|",
      "delete": true
     },
     {
      "line": "| Lise (genel/Anadolu/fen) → Almanya lisans | Evet — 1 yıl SK + FSP |",
      "delete": true
     },
     {
      "line": "| Lise + Türkiye'de 4 yıllık lisansa devam ediyorsun, 1. sınıfı bitirdin | Hayır — direkt lisansa başvurabilirsin |",
      "delete": true
     },
     {
      "line": "| Lise + Türkiye'de lisans mezunu | Hayır — direkt master'a |",
      "delete": true
     },
     {
      "line": "| Meslek lisesi mezunu | Genelde evet — bölüm denkliği zayıf |",
      "delete": true
     },
     {
      "line": "| Açık lise mezunu | Genelde evet — bazı eyaletler kabul etmiyor |",
      "delete": true
     },
     {
      "line": "| IB diploması | Hayır — direkt lisans (uluslararası tanınır) |",
      "delete": true
     },
     {
      "line": "Topluluktan: \"Ben meslek lisesi mezunuyum. Denklik yapsam orada kendi bölümümle alakalı, Studienkolleg yapmadan okuyabilir miyim?\" Pratik: Genelde hayır. Meslek lisesi Anabin denklik puanı düşük. Bazı uygulamalı bilimler üniversiteleri (FH/HAW) doğrudan kabul edebilir — okul bazlı kontrol et. Ama klasik üniversite (RWTH, LMU, TU) için SK gerekir.",
      "replace": "Topluluktan: \"Ben meslek lisesi mezunuyum. Denklik yapsam orada kendi bölümümle alakalı, Studienkolleg yapmadan okuyabilir miyim?\" Cevap: anabin'in güncel değerlendirmesine göre meslek lisesi diploması, YKS'de 180 puanın üzerinde bir sonuçla **alana yönelik Studienkolleg** yoluna açılır. Türkiye'de bir yıllık lisans öğrenimini başarıyla tamamladıysan, okuduğun alanda ve yakın alanlarda Studienkolleg olmadan doğrudan başvurabilirsin. Kesin kararı başvurduğun üniversite verir."
     }
    ]
   },
   "fieldsub": {
    "excerpt": [
     {
      "old": "Türk lise mezunlarının çoğunun Almanya'ya başlamadan önce geçmesi gereken bir yıllık ön hazırlık programı: Studienkolleg.",
      "new": "Doğrudan giriş hakkı olmayan adayların Almanya'da üniversiteye hazırlandığı bir yıllık program: Studienkolleg; gerekip gerekmediği giriş hakkı yoluna bağlı."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "studienkolleg-guide-2026-who-needs-it-which-course-which-school-en",
   "locale": "en",
   "md": {
    "content_md": [
     {
      "line": "A one-year preparatory program that most Turkish high school graduates must complete before starting their studies in Germany: Studienkolleg. T-Kurs or M-Kurs, how to apply, which cities are free — a complete, section-based roadmap.",
      "replace": "A one-year preparatory programme for applicants without direct access to German higher education: Studienkolleg. Whether you need it depends on your access route. T-Kurs or M-Kurs, how to apply, which cities are free — a complete, section-based roadmap."
     },
     {
      "line": "30-second summary: Most Turkish high school graduates cannot apply directly to a German university. A one-year Studienkolleg (preparatory course) is mandatory. If the Feststellungsprüfung (FSP) (assessment test) is passed at the end, an equivalent of a German high school diploma (HZB - Hochschulzugangsberechtigung) is obtained. There are 5 course types, chosen according to the university major you want to pursue: T-Kurs (technical), M-Kurs (medicine/biology), W-Kurs (economics), G-Kurs (social sciences), S-Kurs (philology).",
      "replace": "**30-second summary:** Whether you need a Studienkolleg depends on your **access route**, not on your type of high school: for example, a Lise graduate placed in a 4-year bachelor's at a faculty with the required YKS score can apply directly in that field; for vocational high school graduates and those with only one year at an MYO, the route goes through a Studienkolleg. Passing the **Feststellungsprüfung (FSP)** at the end gives you access to German higher education. There are 5 course types, chosen by the degree you aim for: T-Kurs (technical), M-Kurs (medicine/biology), W-Kurs (economics), G-Kurs (humanities), S-Kurs (languages)."
     },
     {
      "line": "Not everyone in the Turkish education system! Decision matrix:",
      "replace": "That depends on your **access route**, not on your type of high school. Based on anabin's current assessment, in short:\n\n- **12-year Lise + more than 180 points in YKS (SAY/SÖZ/EA/DIL) + placement in a 4-year bachelor's at a faculty:** direct, subject-restricted access; no Studienkolleg needed.\n- **Vocational high school + more than 180 points:** subject-oriented Studienkolleg; after one successful year of bachelor's study in Türkiye, direct access.\n- **Only one year at an MYO:** Studienkolleg (Fachhochschulen only).\n- **Completed Önlisans, or two successful years in open education:** direct, subject-restricted access. **Completed 4-year bachelor's:** direct access for all subjects.\n- **No placement, or below the threshold:** official sources define no general route; ask the university or uni-assist.\n\nH+/H- codes refer to institutions, not to school certificates. The final decision lies with the university you apply to (or the competent state authority). Details and sources: [anabin guide](/en/blog/what-is-anabin-h-h-h-how-is-a-turkish-diploma-en)",
      "probe": "That depends on your **access route**, not on your type of high school. Based on anabin's current assessment, in short:"
     },
     {
      "line": "| Your Turkish education status | Is Studienkolleg required? |",
      "delete": true
     },
     {
      "line": "|---|---|",
      "delete": true
     },
     {
      "line": "| High school (general/Anatolian/science) → Germany bachelor's | Yes — 1 year SK + FSP |",
      "delete": true
     },
     {
      "line": "| High school + studying for a 4-year bachelor's in Turkey, completed 1st year | No — you can apply directly for a bachelor's |",
      "delete": true
     },
     {
      "line": "| High school + bachelor's degree holder in Turkey | No — directly for a master's |",
      "delete": true
     },
     {
      "line": "| Vocational high school graduate | Generally yes — weak subject equivalence |",
      "delete": true
     },
     {
      "line": "| Open high school graduate | Generally yes — some states do not accept |",
      "delete": true
     },
     {
      "line": "| IB diploma | No — direct bachelor's (internationally recognized) |",
      "delete": true
     },
     {
      "line": "From the community: \"I am a vocational high school graduate. If I get an equivalence for my field there, can I study without attending Studienkolleg?\" Practical: Generally no. Vocational high school Anabin equivalence score is low. Some universities of applied sciences (FH/HAW) might accept directly — check on a school-by-school basis. But for classic universities (RWTH, LMU, TU), SK is required.",
      "replace": "From the community: \"I am a vocational high school graduate. If I get an equivalence for my field there, can I study without attending Studienkolleg?\" Answer: according to anabin's current assessment, a vocational high school diploma with more than 180 points in YKS opens the **subject-oriented Studienkolleg** route. If you have successfully completed one year of bachelor's study in Türkiye, you can apply directly, without a Studienkolleg, in that field and related fields. The final decision lies with the university you apply to."
     }
    ]
   },
   "fieldsub": {
    "excerpt": [
     {
      "old": "A one-year preparatory program that most Turkish high school graduates must complete before starting their studies in Germany: Studienkolleg.",
      "new": "A one-year preparatory programme for applicants without direct access to German higher education: Studienkolleg — whether you need it depends on your access route."
     }
    ]
   }
  },
  {
   "table": "posts",
   "slug": "studienkolleg-guide-2026-who-needs-it-which-course-which-school-de",
   "locale": "de",
   "md": {
    "content_md": [
     {
      "line": "Ein einjähriges Vorbereitungsprogramm, das die meisten türkischen Abiturienten absolvieren müssen, bevor sie ihr Studium in Deutschland beginnen: das Studienkolleg. T-Kurs oder M-Kurs, wie man sich bewirbt, welche Städte kostenlos sind – eine vollständige, fachbezogene Roadmap.",
      "replace": "Ein einjähriges Vorbereitungsprogramm für Bewerber ohne direkten Hochschulzugang in Deutschland: das Studienkolleg. Ob du es brauchst, hängt von deinem Zugangsweg ab. T-Kurs oder M-Kurs, wie man sich bewirbt, welche Städte kostenlos sind – eine vollständige, fachbezogene Roadmap."
     },
     {
      "line": "30-Sekunden-Zusammenfassung: Die meisten türkischen Abiturienten können sich nicht direkt an einer deutschen Universität bewerben. Ein einjähriges Studienkolleg (Vorbereitungskurs) ist verpflichtend. Wird die abschließende Feststellungsprüfung (FSP) bestanden, erhält man die Gleichwertigkeit eines deutschen Abiturs (HZB - Hochschulzugangsberechtigung). Es gibt 5 Kurstypen, die je nach angestrebtem Studienfach gewählt werden: T-Kurs (technisch), M-Kurs (Medizin/Biologie), W-Kurs (Wirtschaft), G-Kurs (Geistes-/Sozialwissenschaften), S-Kurs (Sprachwissenschaften).",
      "replace": "**30-Sekunden-Zusammenfassung:** Ob du ein Studienkolleg brauchst, hängt von deinem **Zugangsweg** ab, nicht von der Art deines Gymnasiums: Wer zum Beispiel mit der nötigen YKS-Punktzahl einer Fakultät in einem 4-jährigen Bachelor zugewiesen wurde, kann sich in diesem Fach direkt bewerben; für Absolventen einer Meslek Lisesi und nach nur einem Jahr an einer MYO führt der Weg über ein Studienkolleg. Mit bestandener **Feststellungsprüfung (FSP)** erhältst du den Hochschulzugang in Deutschland. Es gibt 5 Kurstypen, je nach angestrebtem Studium: T-Kurs (technisch), M-Kurs (Medizin/Biologie), W-Kurs (Wirtschaft), G-Kurs (Geisteswissenschaften), S-Kurs (Sprachen)."
     },
     {
      "line": "Nicht jeder aus dem türkischen Bildungssystem! Die Entscheidungsmatrix:",
      "replace": "Das hängt von deinem **Zugangsweg** ab, nicht von der Art deines Gymnasiums. Nach der aktuellen Bewertung in anabin kurz zusammengefasst:\n\n- **12-jähriges Lise + mehr als 180 Punkte in der YKS (SAY/SÖZ/EA/DIL) + Zuweisung in einen 4-jährigen Bachelor an einer Fakultät:** fachgebundener direkter Zugang; kein Studienkolleg nötig.\n- **Meslek Lisesi + mehr als 180 Punkte:** fachbezogenes Studienkolleg; nach einem erfolgreichen Bachelor-Studienjahr in der Türkei direkter Zugang.\n- **Nur ein Jahr an einer MYO:** Studienkolleg (nur für Fachhochschulen).\n- **Abgeschlossenes Önlisans oder zwei erfolgreiche Jahre im Fernstudium:** fachgebundener direkter Zugang. **Abgeschlossener 4-jähriger Bachelor:** direkter Zugang für alle Fächer.\n- **Keine Zuweisung oder unter der Schwelle:** Offizielle Quellen legen keinen allgemeinen Weg fest; frag die Hochschule oder uni-assist.\n\nH+/H- beziehen sich auf Hochschulen, nicht auf Schulabschlüsse. Die endgültige Entscheidung trifft die Hochschule, bei der du dich bewirbst (bzw. die zuständige Stelle des Landes). Details und Quellen: [anabin-Leitfaden](/de/blog/what-is-anabin-h-h-h-how-is-a-turkish-diploma-de)",
      "probe": "Das hängt von deinem **Zugangsweg** ab, nicht von der Art deines Gymnasiums. Nach der aktuellen Bewertung in anabin kurz zusammengefasst:"
     },
     {
      "line": "| Dein türkischer Bildungsstatus | Ist ein Studienkolleg erforderlich? |",
      "delete": true
     },
     {
      "line": "|---|---|",
      "delete": true
     },
     {
      "line": "| Gymnasium (allgemein/Anatolisch/naturwissenschaftlich) → Deutschland Bachelor | Ja — 1 Jahr SK + FSP |",
      "delete": true
     },
     {
      "line": "| Gymnasium + Studium eines 4-jährigen Bachelors in der Türkei, 1. Studienjahr abgeschlossen | Nein — Du kannst dich direkt für einen Bachelor bewerben |",
      "delete": true
     },
     {
      "line": "| Gymnasium + Bachelor-Abschluss in der Türkei | Nein — direkt für einen Master |",
      "delete": true
     },
     {
      "line": "| Absolvent einer Berufsoberschule | Meistens ja — geringe Fächergleichwertigkeit |",
      "delete": true
     },
     {
      "line": "| Absolvent einer offenen Oberschule | Meistens ja — einige Bundesländer akzeptieren nicht |",
      "delete": true
     },
     {
      "line": "| IB-Diplom | Nein — direkter Bachelor (international anerkannt) |",
      "delete": true
     },
     {
      "line": "Aus der Community: \"Ich bin Absolvent einer Berufsoberschule. Wenn ich dort eine Anerkennung für mein Fach erhalte, kann ich ohne Studienkolleg studieren?\" Praktisch: Meistens nein. Der Anabin-Gleichwertigkeitspunkt für Berufsoberschulen ist niedrig. Einige Fachhochschulen (FH/HAW) können direkt akzeptieren — schulintern prüfen. Aber für klassische Universitäten (RWTH, LMU, TU) ist ein SK erforderlich.",
      "replace": "Aus der Community: „Ich bin Absolvent einer Berufsoberschule. Wenn ich dort eine Anerkennung für mein Fach erhalte, kann ich ohne Studienkolleg studieren?“ Antwort: Nach der aktuellen Bewertung in anabin eröffnet das Diplom einer Meslek Lisesi mit mehr als 180 YKS-Punkten den Weg über ein **fachbezogenes Studienkolleg**. Hast du in der Türkei ein Bachelor-Studienjahr erfolgreich abgeschlossen, kannst du dich ohne Studienkolleg direkt für dieses und benachbarte Fächer bewerben. Die endgültige Entscheidung trifft die Hochschule, bei der du dich bewirbst."
     }
    ]
   },
   "fieldsub": {
    "excerpt": [
     {
      "old": "Ein einjähriges Vorbereitungsprogramm, das die meisten türkischen Abiturienten vor dem Studium in Deutschland absolvieren müssen: das Studienkolleg.",
      "new": "Ein einjähriges Vorbereitungsprogramm für Bewerber ohne direkten Hochschulzugang in Deutschland: das Studienkolleg – ob du es brauchst, hängt von deinem Zugangsweg ab."
     }
    ]
   }
  }
 ]
}
JSON, true, 512, JSON_THROW_ON_ERROR);

        $problems = [];
        $writes = [];
        $done = [];
        foreach ($spec['records'] as $r) {
            $label = "{$r['table']}:{$r['slug']}".(isset($r['locale']) ? "/{$r['locale']}" : '');
            $q = DB::table($r['table'])->where('slug', $r['slug']);
            if (isset($r['locale'])) {
                $q->where('locale', $r['locale']);
            }
            $rows = $q->get();
            if ($rows->count() !== 1) {
                $problems[] = "{$label}: kayıt sayısı {$rows->count()}";
                continue;
            }
            $row = (array) $rows->first();
            if (isset($r['cluster'])) {
                $group = DB::table('faqs')->where('slug', $r['cluster'])->where('locale', 'tr')->value('translation_group_id');
                if (! $group || $row['translation_group_id'] !== $group) {
                    $problems[] = "{$label}: TR kaydıyla aynı çeviri kümesinde değil";
                    continue;
                }
            }
            $pending = 0;
            $applied = 0;
            $upd = [];

            foreach ($r['md'] ?? [] as $col => $edits) {
                $lines = explode("\n", (string) $row[$col]);
                $changed = false;
                $units = null;
                foreach ($edits as $e) {
                    // $e = {line: canlı bloğun düz metni, subs: [{old,new}]} — blok düz metinle bulunur, değişiklik
                    // yalnız ham markdown'daki alt dizede yapılır (link/biçim korunur). Blok = tek satır, art arda
                    // satırlardan oluşan paragraf (yumuşak satır sonu) ya da tablo hücresi.
                    // {line, replace}: bütün blok yeni markdown ile değişir (önek korunur).
                    // {line, delete}: blok tamamen kaldırılır (tam bir kez eşleşmeli). {probe}: çok paragraflı yeni metnin
                    // uygulanmış olduğunu tanımak için yeni metnin ilk satırı.
                    $del = ! empty($e['delete']);
                    $whole = isset($e['replace']) || $del;
                    $after = $del ? null : ($whole ? $e['replace'] : $e['line']);
                    foreach ($whole ? [] : $e['subs'] as $s) {
                        $after = str_replace($s['old'], $s['new'], $after);
                    }
                    $units ??= $this->units($lines);
                    $old = array_values(array_filter($units, fn ($u) => $u[3] === $this->norm($e['line'])));
                    $new = $del ? [] : array_values(array_filter($units, fn ($u) => $u[3] === $this->norm($e['probe'] ?? $after)));
                    $rawOk = $old !== [] && array_reduce($old, fn ($ok, $u) => $ok && ($whole
                        ? $u[0] !== 'cell'
                        : array_reduce($e['subs'], fn ($o, $s) => $o && $this->unitHas($lines, $u, $s['old']), true)), true);
                    if (count($old) >= 1 && count($new) === 0 && $rawOk && (! $del || count($old) === 1)) {
                        $pending++;
                        foreach ($old as [$kind, $start, $len]) {
                            if ($whole) {
                                $cr = str_ends_with($lines[$start], "\r") ? "\r" : '';
                                // ^\s* (0–3 değil): girintili alt liste maddesinin ("    *   ") öneki de korunur
                                preg_match('/^\s*(?:#{1,6}\s+|(?:>\s?)+|[-*+]\s+|\d+[.)]\s+)?/u', $lines[$start], $m);
                                $lines[$start] = $del ? "\0" : $m[0].$e['replace'].$cr;
                                for ($i = $start + 1; $i < $start + $len; $i++) {
                                    $lines[$i] = "\0";   // paragrafın diğer satırları (sonda silinir)
                                }
                            } else {
                                for ($i = $start; $i < $start + $len; $i++) {
                                    foreach ($e['subs'] as $s) {
                                        $lines[$i] = str_replace($s['old'], $s['new'], $lines[$i]);
                                    }
                                }
                            }
                        }
                        $changed = true;
                        $units = null;
                    } elseif (count($old) === 0 && ($del || count($new) >= 1)) {
                        $applied++;
                    } else {
                        $problems[] = "{$label}: {$col} blok eşleşmedi (eski ".count($old).', yeni '.count($new).', ham alt dize '.($rawOk ? 'var' : 'yok').') «'.mb_substr($e['line'], 0, 70).'»';
                    }
                }
                if ($changed) {
                    $upd[$col] = implode("\n", array_values(array_filter($lines, fn ($l) => $l !== "\0")));
                }
            }

            foreach ($r['fieldsub'] ?? [] as $col => $subs) {
                $cur = (string) $row[$col];
                foreach ($subs as $s) {
                    if (substr_count($cur, $s['old']) === 1 && ! str_contains($cur, $s['new'])) {
                        $pending++;
                        $cur = str_replace($s['old'], $s['new'], $cur);
                        $upd[$col] = $cur;
                    } elseif (! str_contains($cur, $s['old']) && str_contains($cur, $s['new'])) {
                        $applied++;
                    } else {
                        $problems[] = "{$label}: {$col} alt dize eşleşmedi «".mb_substr($s['old'], 0, 60).'»';
                    }
                }
            }

            foreach ($r['rewrite'] ?? [] as $col => $rw) {
                // Tamamen eski kurala dayalı SSS cevabı: eski metin parmak izleriyle doğrulanır, yeni metin bütün yazılır.
                $cur = (string) $row[$col];
                $plain = $this->norm(str_replace("\n", ' ', $cur));
                $fp = array_reduce($rw['fingerprints'], fn ($ok, $f) => $ok && str_contains($plain, $this->norm($f)), true);
                if ($cur === $rw['new']) {
                    $applied++;
                } elseif ($fp) {
                    $pending++;
                    $upd[$col] = $rw['new'];
                } else {
                    $problems[] = "{$label}: {$col} eski metin parmak izleri bulunamadı";
                }
            }

            foreach ($r['fields'] ?? [] as $col => $v) {
                $cur = (string) $row[$col];
                if ($cur === $v['new']) {
                    $applied++;
                } elseif ($cur === $v['old']) {
                    $pending++;
                    $upd[$col] = $v['new'];
                } else {
                    $problems[] = "{$label}: {$col} beklenen eski değerde değil";
                }
            }

            foreach ($r['json'] ?? [] as $col => $subs) {
                $data = json_decode((string) $row[$col], true);
                if (! is_array($data)) {
                    $problems[] = "{$label}: {$col} JSON okunamadı";
                    continue;
                }
                $changed = false;
                $orig = $data;   // durum her alt dize için ORİJİNAL veriye göre belirlenir
                foreach ($subs as $s) {
                    $o = $this->countSub($orig, $s['old']);
                    $n = $this->countSub($orig, $s['new']);
                    if ($o >= 1 && $n === 0) {
                        $pending++;
                        $data = $this->replaceSub($data, $s['old'], $s['new']);
                        $changed = true;
                    } elseif ($o === 0 && $n >= 1) {
                        $applied++;
                    } else {
                        $problems[] = "{$label}: {$col} alt dize eşleşmedi (eski {$o}, yeni {$n}) «".mb_substr($s['old'], 0, 70).'»';
                    }
                }
                if ($changed) {
                    $upd[$col] = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }

            if ($pending > 0 && $applied > 0) {
                $problems[] = "{$label}: kısmen uygulanmış ({$applied} uygulanmış, {$pending} bekleyen)";
            } elseif ($pending > 0) {
                $writes[] = [$r, $row['id'], $upd];
            } else {
                $done[] = $label;
            }
        }
        if (! $problems && $writes && $done) {
            $problems[] = 'kayıtlar arası kısmi durum: '.count($done).' kayıt uygulanmış, '.count($writes).' bekleyen ('.implode(', ', array_slice($done, 0, 5)).')';
        }

        if ($problems) {
            if (app()->runningUnitTests()) {
                return; // Test DB'si sıfırdan kurulur; bu kayıtların çoğu orada yok. Hiçbir şey yazma.
            }
            throw new RuntimeException('P0 guide truth fix: ön kontrol başarısız, hiçbir şey yazılmadı. '.implode(' | ', array_slice($problems, 0, 40)));
        }
        if (! $writes) {
            return; // zaten uygulanmış — no-op
        }

        DB::transaction(function () use ($writes) {
            foreach ($writes as [$r, $id, $upd]) {
                if ($r['table'] === 'posts') {
                    $m = \App\Models\Post::findOrFail($id);   // booted(): content_html + reading_minutes
                } elseif ($r['table'] === 'faqs') {
                    $m = \App\Models\Faq::findOrFail($id);    // booted(): answer_html
                } else {
                    DB::table($r['table'])->where('id', $id)->update($upd + ['updated_at' => now()]);
                    continue;
                }
                foreach ($upd as $col => $val) {
                    $m->{$col} = $val;
                }
                $m->save();
            }
        });
    }

    private function countSub($v, string $needle): int
    {
        if (is_array($v)) {
            return array_sum(array_map(fn ($x) => $this->countSub($x, $needle), $v));
        }

        return is_string($v) ? substr_count($v, $needle) : 0;
    }

    private function replaceSub($v, string $old, string $new)
    {
        if (is_array($v)) {
            return array_map(fn ($x) => $this->replaceSub($x, $old, $new), $v);
        }

        return is_string($v) ? str_replace($old, $new, $v) : $v;
    }

    /**
     * Markdown satırlarından sayfadaki bloklara karşılık gelen birimler: [tür, başlangıç, satır sayısı, düz metin].
     * tür: line (tek satır), win (boş satırla kesilmeyen art arda satırlar = yumuşak satır sonlu paragraf),
     * cell (tablo satırındaki hücre).
     */
    private function units(array $lines): array
    {
        $u = [];
        $n = count($lines);
        $blank = fn ($l) => $l === "\0" || trim($l) === '';
        for ($i = 0; $i < $n; $i++) {
            if ($blank($lines[$i])) {
                continue;
            }
            $u[] = ['line', $i, 1, $this->norm($lines[$i])];
            if (str_starts_with(ltrim($lines[$i]), '|')) {
                foreach (explode('|', trim(trim($lines[$i]), '|')) as $cell) {
                    $u[] = ['cell', $i, 1, $this->norm($cell)];
                }
            }
            $txt = $lines[$i];
            $quote = str_starts_with(ltrim($lines[$i]), '>');
            for ($j = $i + 1; $j < min($n, $i + 15) && ! $blank($lines[$j]); $j++) {
                // alıntı devamı ("> …", ">"): eşleştirmede öneki yok say (sayfada tek blockquote bloğu)
                $txt .= ' '.($quote ? preg_replace('/^\s{0,3}(?:>\s?)+/u', '', $lines[$j]) : $lines[$j]);
                $u[] = ['win', $i, $j - $i + 1, $this->norm($txt)];
            }
        }

        return $u;
    }

    private function unitHas(array $lines, array $u, string $needle): bool
    {
        for ($i = $u[1]; $i < $u[1] + $u[2]; $i++) {
            if (str_contains($lines[$i], $needle)) {
                return true;
            }
        }

        return false;
    }

    /** Markdown satırının görünen düz metni (canlı sayfadaki blok metniyle karşılaştırmak için). */
    private function norm(string $s): string
    {
        $s = preg_replace('/^\s*(?:#{1,6}\s+|(?:>\s?)+|[-*+]\s+|\d+[.)]\s+)?/u', '', $s);
        $s = preg_replace('/!\[([^\]]*)\]\([^)]*\)/u', '$1', $s);
        for ($i = 0; $i < 3; $i++) {
            $s = preg_replace('/\[([^\]]*)\]\([^)]*\)/u', '$1', $s);
        }
        $s = preg_replace('/<[^>]+>/u', '', $s);
        $s = str_replace(['**', '__', '*', '`'], '', $s);
        $s = preg_replace('/\\\\([\\\\`*_{}\[\]()#+\-.!>|"\'])/u', '$1', $s);
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $s));
    }

    public function down(): void
    {
        // Bilinçli olarak boş: forward-only içerik düzeltmesi.
    }
};

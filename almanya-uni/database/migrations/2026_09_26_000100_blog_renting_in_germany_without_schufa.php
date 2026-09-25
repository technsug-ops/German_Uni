<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+EN+DE): Almanya'da SCHUFA geçmişi olmadan ev kiralama — başvuru dosyası odaklı.
 *
 * Arama niyeti: "SCHUFA geçmişim yok, yine de nasıl ev kiralarım?". Genel SCHUFA konusu
 * (nedir, nasıl oluşur, 2026 skoru) eski rehberde kalıyor; iki yazı karşılıklı linkli
 * (bkz. 2026_09_26_000200_update_schufa_guide_intent_and_facts).
 *
 * Doğrulanmış kaynaklar (25.09.2026):
 *   - DSK Orientierungshilfe Selbstauskünfte Mietinteressent:innen V2.0 (Ocak 2026) + Musterfragebogen:
 *     A/B/C aşamaları, kimlik fotokopisi yok, uyruk sorulamaz, Datenkopie ve Mietschuldenfreiheits-
 *     bescheinigung talep edilemez. Yasa değil, otoritelerin yorumu — metinde de öyle anlatılıyor.
 *   - § 551 BGB (3 Nettokaltmiete, 3 taksit); BGH VIII ZR 243/03 (Kumulationsverbot, gönüllü kefalet
 *     istisnası BGHZ 111, 361); BGH VIII ZR 238/08; § 540 BGB; § 19 BMG; § 2 / § 4a WoVermRG; § 19 / § 21 AGG.
 *   - SCHUFA: yeni skor 17.03.2026 (100–999); veri yoksa skor yok; BonitätsCheck'te ev sahibine skor yok.
 *   - Verbraucherzentrale (07.08.2026), Polizei BW (04/2025) ve NRW, Studierendenwerk Berlin/München.
 * Ticari kefil/depozito firmalarının adı bilinçli olarak verilmedi; Sperrkonto tutarı yazılmadı.
 * Yazar: Halil Yaprakli. Kategori: student-life.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'ca79c30f-fc60-4a39-b4ff-286c2c745828';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'student-life')->value('id')
            ?? DB::table('categories')->where('slug', 'yasam')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Kısa cevap: Evet, Almanya'da SCHUFA kaydınız olmadan da ev kiralayabilirsiniz. Hiçbir yasa kiracıdan SCHUFA raporu şartı koşmaz; ancak ev sahibi kiracısını serbestçe seçer. Bu yüzden asıl işiniz, SCHUFA'nın yerini tutan belgelerle ikna edici bir başvuru dosyası hazırlamak ve bu belgeleri doğru zamanda göstermektir.

> **Son güncelleme: Eylül 2026** · Resmî kaynaklarla doğrulandı: Alman veri koruma otoritelerinin ortak rehberi (DSK, Ocak 2026), Alman Medeni Kanunu (BGB), SCHUFA, Verbraucherzentrale (tüketici merkezi). Bu yazı hukuki danışmanlık değildir.

## SCHUFA nedir ve ev sahipleri neden ister

SCHUFA Holding AG, Almanya'nın en büyük özel kredi bilgi kuruluşudur. Bankaların, kredi kartı şirketlerinin, leasing firmalarının ve bazı telefon/abonelik sağlayıcılarının bildirdiği verileri saklar ve bunlardan bir puan hesaplar. 17 Mart 2026'dan beri tüketiciler için yeni SCHUFA skoru geçerli: 100 ile 999 arasında bir ölçek, 12 kriter; eski sektör skorlarının yerini aldı ([SCHUFA basın bülteni, 17.03.2026](https://www.schufa.de/en/newsroom/press-release/der-neue-schufa-score-transparent-und-leicht-nachzurechnen/index.jsp)). Skorunuzu ücretsiz SCHUFA hesabı veya uygulaması üzerinden görebilirsiniz.

Ev sahibi için SCHUFA tek bir soruya cevap arar: "Bu kişinin ödeme sorunu olmuş mu?" Skorun nasıl oluştuğunu ve nasıl yükseltileceğini [SCHUFA Rehberi 2026](/tr/blog/schufa-guide-2026-why-is-credit-score-important-for-turkish-students) yazımızda anlattık. Bu yazı ise tek bir soruya odaklanıyor: SCHUFA kaydınız yokken evi nasıl alırsınız?

## Almanya'ya yeni gelenlerde SCHUFA kaydı neden genelde boştur

SCHUFA yalnızca **Almanya'da** bildirilen verileri bilir. Türkiye'deki kredi notunuz, Findeks raporunuz ya da yıllardır düzenli ödediğiniz kredi kartı borçlarınız SCHUFA'ya aktarılmaz. Almanya'ya ilk geldiğiniz gün, finansal açıdan "tanınmayan" biri olursunuz.

SCHUFA'nın kendi açıklamasına göre ([SCHUFA](https://www.schufa.de/en/newsroom/creditworthiness/fehlende-daten-personen-erhalten-keinen-schufa-score/index.jsp)):

- Nüfusun yaklaşık %1,5'i hakkında çok az veri vardır veya hiç veri yoktur; buna açıkça "daha önce Almanya'da ekonomik olarak aktif olmamış kişiler, örneğin göçmenler" dahildir.
- Hiç veri yoksa skor da yoktur; sorgulayan şirket "veri yok" bilgisi alır.
- 6 aydan kısa süredir biliniyorsanız ve banka sözleşmeniz yoksa skor hesaplanmaz. 6 aydan uzun süredir biliniyor ama banka sözleşmeniz yoksa, skor "az bilgi" uyarısıyla çıkar.
- Herhangi bir banka sözleşmesi (vadesiz hesap, kredi kartı, kredi, leasing, kefalet) olduğunda tam skor hesaplanır.

En önemli nokta şu: **Boş kayıt, olumsuz kayıt değildir.** Yeni gelen biri olarak sizin sorununuz "kötü SCHUFA" değil, "henüz SCHUFA geçmişi olmaması"dır. Ev sahibine bunu açıkça bu şekilde anlatmanız, dosyanızın algısını ciddi şekilde değiştirir.

Bu arada, kirayı zamanında ödemek tek başına SCHUFA geçmişi oluşturmaz; geciken bir kira da SCHUFA'ya "doğrudan" işlenmez. Olumsuz kayıt ancak belirli şartlarla oluşabilir: en az iki yazılı ihtar, ilki en az 4 hafta önce gönderilmiş olmalı, alacak tartışmasız olmalı ve borçlu bildirim konusunda uyarılmış olmalı ([SCHUFA, veriler ve silme süreleri](https://www.schufa.de/ueber-uns/daten-scoring/daten-schufa/)).

## SCHUFA olmadan kiralamak: mümkün ama bir şartla

Hukuki durum net: Almanya'da ev kiralamak için SCHUFA raporunu zorunlu kılan bir yasa yok. Ev sahibi, veri koruma ve ayrımcılık yasağı sınırları içinde kiracısını serbestçe seçer. Yani sizden SCHUFA istemesi yasak değil, ama SCHUFA'nız olmadığı için başvurunuzu kabul etmek zorunda da değil.

Pratikte bunun anlamı şudur: Berlin, Münih, Hamburg ya da Frankfurt gibi konut piyasasının sıkışık olduğu şehirlerde tek bir ilana onlarca başvuru gelir ve ev sahipleri çoğu zaman SCHUFA raporu olan adayları tercih eder. Hangi şehirde piyasanın ne kadar sıkışık olduğunu [öğrenci kira haritası](/tr/student-rent-map) üzerinden karşılaştırabilirsiniz. Daha sakin piyasalarda düzenli bir alternatif dosyayla şansınız genellikle daha yüksektir.

## Ev sahibi neyi, ne zaman sorabilir: DSK aşamaları

Alman veri koruma otoritelerinin ortak rehberine göre (DSK Orientierungshilfe, Sürüm 2.0, Ocak 2026), ev sahibinin kiracı adayından bilgi toplaması üç aşamada olmalıdır. Bu rehber bir yasa değildir, ancak denetim makamlarının uygulamada neyi kabul ettiğini gösterir. Kredi bilgisi (SCHUFA dahil) yalnızca son aşamaya aittir.

| Aşama | Sorulabilecekler | Sorulmaması / istenmemesi gerekenler |
|---|---|---|
| **A – Ev gezisi (Besichtigung)** | Ad, soyad, adres; kimliğe yüz yüze bakıp kontrol edildiğini not etmek; sosyal konutta WBS (sosyal konut belgesi) olup olmadığı | Kimlik fotokopisi (DSK: "gerekli değil, bu nedenle izinli değil"), gelir belgesi, SCHUFA |
| **B – Bu daireyi istediğinizi bildirdiğinizde** | Taşınacak kişi sayısı, işveren ve meslek, net gelir (belirli bir eşiğin üzerinde olduğunu belirtmek yeterli), evcil hayvan, devam eden tüketici iflası, son 5 yılda kira borcu nedeniyle tahliye kararı | Ne kadar süredir çalıştığınız, harcamalarınızın nedenleri, eski ev sahibinizin iletişim bilgileri |
| **C – Ev sahibi sizi seçtiğinde (imzadan kısa süre önce)** | Ödeme gücü kanıtı (maaş bordrosu, banka dökümü veya vergi tahakkuku; gereksiz yerleri karartılmış **kopya** olarak), kredi bilgisi (ör. kiraya özel kredi raporu), gerekiyorsa Jobcenter kira ödeme belgesi | Tam banka geçmişi, SCHUFA Datenkopie (tam veri kopyası), Mietschuldenfreiheitsbescheinigung (kira borcu olmadığına dair belge) talebi |

SCHUFA'nın kendi ev sahibi sayfası da aynı çizgide: Ev sahibinin kredi bilgisini görme talebi ancak aday kısa listeye girdiğinde doğar; somut bir taahhüt olmadan ilk gezide SCHUFA istemek uygun değildir ([SCHUFA](https://www.schufa.de/newsroom/bonitaet/schufa-auskunft-vermieter-schnell-einfach/)). DSK ayrıca kiralamayı kredi sorgusuna rıza göstermeye bağlamanın geçerli bir rıza olmadığını belirtir.

## SCHUFA'nın yerini pratikte tutabilecek belgeler

Aşağıdaki belgelerin hiçbiri yasal olarak SCHUFA'nın "resmî yerine geçen" belgesi değildir. Ancak pratikte ödeme gücünüzü destekleyebilir. Kabul edip etmemek ev sahibine kalmıştır.

| Belge | Kimler için uygun | Ne gösterir | Ne kadar güçlü |
|---|---|---|---|
| Arbeitsvertrag (iş sözleşmesi) | Yeni çalışanlar, Blue Card sahipleri | Düzenli gelir ve maaş miktarı | Güçlü |
| Gehaltsabrechnungen (maaş bordroları) | En az bir ay maaş almış olanlar | Gerçekten ödenen net gelir | Güçlü |
| Bürgschaft (kefalet) | Öğrenciler, geliri düşük olanlar | Ödeyemezseniz üçüncü bir kişinin ödeyeceği | Güçlü (kefilin gelirine bağlı) |
| Sperrkonto (bloke hesap) onayı / banka dökümü (karartılmış) | Vizeli öğrenciler | Aylık düzenli ödeme ve bakiye | Orta |
| Immatrikulationsbescheinigung (öğrenci belgesi) | Kayıtlı öğrenciler | Öğrenci statüsü | Destekleyici |
| Zulassungsbescheid (kabul mektubu) | Henüz kaydolmamış yeni öğrenciler | Neden şehirde olduğunuz | Destekleyici |
| Burs mektubu | DAAD, Erasmus vb. bursiyerler | Aylık sabit gelir | Orta |
| SCHUFA BonitätsCheck ("olumsuz kayıt yok") | Alman adresi ve hesabı olan herkes | Olumsuz ödeme bilgisi olmadığı | Orta, SCHUFA isteyen ev sahipleri için önemli |
| Mietschuldenfreiheitsbescheinigung (kira borcu yok belgesi) | Daha önce Almanya'da kiracı olanlar | Önceki kiranın ödendiği | Yeni gelenlerde genelde yok; talep edilemez |
| Kimlik / oturum izni | Herkes | Kimlik ve yasal kalış | Yalnızca gösterilir, kopya verilmez |

Birkaç önemli not:

- **Sperrkonto ve burs mektubu**, Alman Dışişleri Bakanlığı'nın vize için istediği finansman kanıtlarıdır; kira için yasal bir belge değildir. Yine de bazı ev sahipleri bunları pratikte ödeme gücünü destekleyen bir kanıt olarak kabul edebilir. Güncel Sperrkonto tutarını [Sperrkonto hesaplayıcısında](/tr/tools/sperrkonto) bulabilirsiniz.
- **Mietschuldenfreiheitsbescheinigung**: Federal Yüksek Mahkeme'ye (BGH, VIII ZR 238/08) göre kiracının eski ev sahibinden böyle bir belge talep etme hakkı yoktur; DSK de bu yüzden yeni ev sahibinin bunu isteyemeyeceğini söyler. Türkiye'deki eski ev sahibinizden gönüllü bir referans yazısı almak isterseniz bu tamamen isteğe bağlı bir pratik ektir.

### Datenkopie mi, BonitätsCheck mi

Almanya'da bir adresiniz ve banka hesabınız olduktan sonra SCHUFA'dan iki farklı belge alabilirsiniz:

- **Datenkopie (GDPR Madde 15 veri kopyası)**: SCHUFA'dan **ücretsizdir**. Saklanan tüm verileri, son 12 aydaki sorguları ve iletilen skorları içerir; "kişisel bilginiz için" hazırlanır. Bazı aracı siteler bunu yaklaşık 30 € karşılığında satar; bunlara ihtiyacınız yok. SCHUFA ve DSK, bu belgeyi ev sahibine vermemenizi önerir: içinde gereğinden fazla veri vardır ve ev sahibi bunu talep edemez.
- **BonitätsCheck** (dijital PDF, ev sahibi için doğrulama kodlu) veya **BonitätsAuskunft** (kâğıt, 2–4 iş gününde postayla gelir; yalnızca 1. sayfayı verin): Tam da ev sahipleri için tasarlanmıştır. Ev sahibine ayrılan kısım yalnızca olumsuz ödeme bilgisi olup olmadığını gösterir; **skor gösterilmez**. Ücreti yaklaşık 30 €'dur ([SCHUFA](https://www.schufa.de/themenportal/datenkopie-bonitaetscheck/index.jsp)).

Yeni gelen biri için BonitätsCheck genelde "olumsuz kayıt yok" sonucunu gösterir. Yani adresinizi kaydettirip ([Anmeldung rehberi](/tr/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt)) hesabınızı açtıktan ([Almanya'da banka hesabı açma](/tr/blog/opening-a-bank-account-in-germany-the-real-obstacles)) sonra bu belgeyi almak, "SCHUFA istiyoruz" diyen ev sahibine gösterebileceğiniz somut bir kanıt olur.

## Dört gerçekçi başvuru senaryosu

Aşağıdaki kişiler örnek amaçlıdır.

### Yeni gelmiş yüksek lisans öğrencisi (Sperrkonto + kabul mektubu)

Elif, Ankara'dan TU München'de yüksek lisansa kabul aldı ve Ekim başında Münih'e geldi. Sperrkonto'su var, Alman banka hesabı yeni açıldı, SCHUFA kaydı henüz boş. [Münih](/tr/cities/munchen-q1726) konut piyasası çok sıkışık olduğu için Studierendenwerk başvurusuna ek olarak WG odalarına da başvuruyor.

**Dosyasına koyması gerekenler:**

- Zulassungsbescheid (kabul mektubu) ve kayıt tamamlandıysa Immatrikulationsbescheinigung
- Sperrkonto onayı ve aylık ödemeyi gösteren, gereksiz yerleri karartılmış banka dökümü
- Varsa Türkiye'deki ailesinden kefalet (Bürgschaft) beyanı ve kefilin gelir kanıtı
- Kısa bir tanıtım metni (kim olduğu, ne okuduğu, ne kadar kalacağı)
- Adres kaydından sonra: BonitätsCheck

### Yeni çalışan: sözleşme imzalanmış, henüz maaş bordrosu yok

Murat, İstanbul'dan bir yazılım şirketinde çalışmak için Berlin'e geldi. İş sözleşmesi imzalı, ilk maaşı ay sonunda yatacak. SCHUFA kaydı yok, bordrosu da henüz yok.

**Dosyasına koyması gerekenler:**

- Arbeitsvertrag (iş sözleşmesi), maaşın görüldüğü sayfa; diğer ayrıntılar karartılabilir
- İsteğe bağlı olarak işverenden kısa bir yazı (işe başlama tarihi, deneme süresi durumu)
- İlk bordro gelir gelmez: Gehaltsabrechnung
- Türkiye'den getirdiği birikimi gösteren banka dökümü (yalnızca bakiye görünür şekilde)
- Adres ve hesap tamamlanınca: BonitätsCheck

### Alman banka geçmişi hiç olmayan kişi

Zeynep dil kursu için geldi; kalıcı adresi olmadığı için hesap açamıyor, SCHUFA'sı tamamen boş. Bu durumda ilk hedef kalıcı bir ev değil, adres kaydı yapabileceği geçici bir çözümdür (öğrenci yurdu, resmî Zwischenmiete ya da WG).

**Dosyasına koyması gerekenler:**

- Dil kursu kayıt belgesi ve vize/oturum izni (yalnızca gösterilir)
- Finansman kanıtı: Sperrkonto veya burs mektubu
- Türkiye'deki hesabından gelen düzenli transferleri gösteren döküm (karartılmış)
- Kefil (Bürgschaft), mümkünse Almanya'da yaşayan bir tanıdık
- Taşındığı ilk yerden Wohnungsgeberbestätigung (ev sahibinin taşınma onayı); bununla adres kaydı ve hesap açma yolu açılır

### Aile veya başka bir kefille başvuru

Can, Frankfurt'ta lisansa başlıyor; ailesi Türkiye'de ve kira ödemelerini üstlenecek. Kefalet onun dosyasının en güçlü parçası olacak. Kefaletin kurallarına aşağıda ayrıca bakıyoruz.

**Dosyasına koyması gerekenler:**

- Kefilin imzalı Bürgschaft beyanı (hangi daire, hangi kira)
- Kefilin gelir kanıtı ve kimlik bilgisi; yabancı dildeki belgeler için Almanca veya İngilizce çeviri pratikte işleri kolaylaştırır
- Öğrenci belgesi / kabul mektubu
- Kendi banka dökümü (karartılmış)

## Yeni öğrenciler için konut seçenekleri: yurt, WG, Zwischenmiete, özel ev sahibi

Tüm seçenekleri tek sayfada görmek için [konut rehberimize](/tr/housing) göz atabilirsiniz. SCHUFA açısından özetle:

### Studierendenwerk yurtları

Öğrenci yurtları genellikle SCHUFA değil, öğrenim kanıtı ister; sorun SCHUFA değil bekleme listesidir, bu yüzden erken başvurun.

- **Berlin (studierendenWERK BERLIN):** Kabul mektubu veya öğrenci belgesi, dönem harcı ödeme kanıtı ve kimlik/pasaportla başvurulur. Bekleme listesinde kalmak için düzenli olarak (her 30 günde bir) e-postayla onay vermeniz ve 5 gün içinde cevap vermeniz gerekir; aksi halde başvuru düşer ([stw.berlin](https://www.stw.berlin/wohnen/faq-wohnen/wie-bewerbe-ich-mich-um-einen-wohnplatz.html)). Şehir hakkında daha fazlası: [Berlin](/tr/cities/berlin-q64).
- **Münih (Studierendenwerk München Oberbayern):** Başvuru online; kış dönemi için başvurular 15 Mayıs'ta açılır, sıralama başvuru tarihine göre yapılır ve teklifler genelde dönem başlangıcından yaklaşık 3–8 hafta önce gelir ([Studierendenwerk München](https://www.studierendenwerk-muenchen-oberbayern.de/en/accommodation/application/)).
- Yurtlarda da depozito en fazla 3 aylık kiradır.

### WG (paylaşımlı ev)

WG'lerde çoğu zaman karar ev arkadaşlarının elindedir; kişisel izlenim SCHUFA'dan daha önemli olabilir. İyi bir WG başvurusunun nasıl yazıldığını [WG rehberimizde](/tr/blog/finding-a-wg-in-germany-your-comprehensive-guide-to-dorm-wg) ve [WG nasıl bulunur SSS](/tr/faq/yurt/almanyada-paylasimli-ogrenci-evi-wg-nasil-bulunur) sayfasında bulabilirsiniz.

### Zwischenmiete / Untermiete (geçici kiralama, alt kiralama)

Alt kiralama için asıl ev sahibinin izni gerekir (§ 540 BGB). İzinsiz alt kiralama asıl kiracı için risklidir ve sizin konaklamanızı da belirsizleştirebilir. Pratik öneri: Ev sahibinin onayını gösteren bir belge isteyin.

### Özel ev sahibi

Özel ev sahipleri ve konut şirketleri sıklıkla SCHUFA ister. Burada eksiksiz, düzenli ve doğru aşamada sunulan bir dosya fark yaratır. Almanca başvuru için [ev başvuru şablonu](/tr/templates/wohnungsbewerbung) işinizi kolaylaştırır. Kira kalemlerini (Kaltmiete, Warmmiete, Nebenkosten) önceden anlamak için [kira maliyetleri yazımıza](/tr/blog/germany-rental-costs-explained-kaltmiete-warmmiete-nebenkosten-kaution) bakın.

## Kefalet (Bürgschaft): kurallar

Kefalet, SCHUFA'sı olmayan öğrenciler için en güçlü kozlardan biridir. Ama sınırları vardır:

- **Üst sınır (§ 551 BGB):** Kira güvencesi toplamda en fazla 3 aylık net soğuk kiradır (Nettokaltmiete, yan giderler hariç). Nakit depozito 3 eşit aylık taksitle ödenebilir; ilki kira ilişkisinin **başlangıcında**, diğerleri sonraki iki kira ödemesiyle birlikte. Kiracı aleyhine farklı anlaşmalar geçersizdir.
- **Birikim yasağı:** BGH'nin kararına (VIII ZR 243/03, 30.06.2004) göre birden fazla güvence birlikte bu sınırı aşamaz. Yargı genel olarak, tam 3 aylık depozitonun üzerine ayrıca talep edilen bir aile kefaletini geçersiz sayar.
- **Gönüllülük istisnası:** Kefalet üçüncü bir kişi tarafından istenmeden, gönüllü olarak ve sözleşmenin yapılması şartıyla teklif edilmişse § 551'i ihlal etmez (BGHZ 111, 361). Uygulamada bunun "gönüllü" olduğunu kanıtlamak zor olabilir.
- **Ticari kefalet hizmetleri:** Kefil bulamayanlar için ücret karşılığı kefalet veya depozito sigortası sunan ticari hizmetler de vardır. Burada marka önermiyoruz; maliyetleri, iade koşullarını ve sözleşme şartlarını dikkatle okuyun.

## Ev sahibine örnek mesaj

Ev sahipleri Alman olduğu için mesaj Almancadır. Mesaj; yeni geldiğinizi, SCHUFA kaydınızın henüz boş (olumsuz değil) olduğunu, uygun aşamada hangi belgeleri sunabileceğinizi ve BonitätsCheck'i alır almaz göstermeye hazır olduğunuzu anlatır. Hukuki iddia veya vaat içermez.

> Sehr geehrte/r Frau/Herr [Name],
>
> vielen Dank für die Besichtigung. Ich interessiere mich sehr für die Wohnung in der [Straße]. Ich bin vor Kurzem für mein Masterstudium an der [Hochschule] nach Deutschland gezogen. Da ich neu in Deutschland bin, habe ich noch keine SCHUFA-Historie – mein Eintrag ist leer, nicht negativ.
>
> Gern stelle ich Ihnen zum passenden Zeitpunkt folgende Unterlagen zur Verfügung: Zulassungsbescheid, Nachweis meines Sperrkontos und eine Bürgschaftserklärung meiner Eltern. Sobald meine Anmeldung abgeschlossen ist, kann ich Ihnen außerdem einen SCHUFA-BonitätsCheck vorlegen.
>
> Über eine Rückmeldung freue ich mich.
>
> Mit freundlichen Grüßen
> [Vorname Nachname]

Türkçesi özetle: "Gezi için teşekkürler, daireyle çok ilgileniyorum; yeni geldiğim için SCHUFA geçmişim henüz yok, kaydım boş ama olumsuz değil; uygun aşamada kabul mektubu, Sperrkonto kanıtı ve ailemin kefaletini sunabilirim, adres kaydım tamamlanınca BonitätsCheck'i de gösterebilirim."

## Gereksiz yere vermemeniz gerekenler

"Ne kadar çok belge, o kadar iyi" düşüncesi yanıltıcıdır; veri minimizasyonu sizi dolandırıcılara karşı da korur. Alman veri koruma otoritelerinin ortak rehberine göre:

- **Ev gezisinde kimlik fotokopisi vermeyin.** Kimliğinizi yüz yüze göstermeniz yeterli. Alman kimlik kartı yalnızca sahibinin rızasıyla kopyalanabilir ve kopya açıkça "kopya" olarak işaretlenmelidir (§ 20 PAuswG). Pasaport veya oturum kartı için de aynı dikkati gösterin: Kopya gerçekten gerekiyorsa gereksiz alanları karartın ve üzerine "yalnızca X dairesi başvurusu için kopya" yazın.
- **Tam banka geçmişi vermeyin.** C aşamasında bile yalnızca gereken bilgi görünmeli; diğer işlemleri karartın.
- **SCHUFA Datenkopie'yi vermeyin;** bunun yerine BonitätsCheck kullanın.
- **Uyrukluk, din, etnik köken, evlilik planı, hamilelik, çocuk isteği, parti veya kiracı derneği üyeliği** gibi sorular DSK'ya göre gerekli değildir ve bu nedenle izinli değildir. Medeni durum da genel olarak gerekli değildir. (Bir eyalet otoritesi özel kategoriler konusunda kısmen farklı görüştedir.)
- **Eski ev sahibinizin iletişim bilgilerini** vermek zorunda değilsiniz; bu da DSK'ya göre sorulmamalıdır.
- Başvuru reddedilirse ev sahibinin verilerinizi silmesi gerekir; AGG talepleri söz konusu olabildiğinden kural olarak en geç 6 ay içinde.

**Ayrımcılık:** Genel Eşit Muamele Yasası (AGG), ev sahibinin büyüklüğünden bağımsız olarak konutta etnik köken nedeniyle ayrımcılığı yasaklar (§ 19(2) AGG). Taleplerin şu anda **2 ay** içinde ileri sürülmesi gerekir; bu süreyi 4 aya uzatacak bir reform planlanıyor ama henüz yasalaşmadı. Federal Ayrımcılıkla Mücadele Ajansı ([Antidiskriminierungsstelle des Bundes](https://www.antidiskriminierungsstelle.de)) ilk danışmanlık sunar ve yalnızca "Almanca konuşan" adayları hedefleyen ilanların dolaylı ayrımcılık olabileceğini belirtir.

## Kira dolandırıcılıklarını nasıl fark edersiniz

SCHUFA'sı olmayan ve acilen ev arayan yeni gelenler, dolandırıcıların favori hedefidir. Verbraucherzentrale (07.08.2026) ve polisin uyarılarına göre tipik işaretler:

- Ev sahibi "yurt dışında", anahtarlar depozito ödendikten sonra postayla gönderilecekmiş
- Gezi öncesinde depozito veya "rezervasyon ücreti" isteniyor
- Gerçek bir geziden sonra ama sözleşme imzalanmadan depozito isteniyor
- Ön seçim listesine girmek için ücret isteniyor
- Daha ilk yazışmada e-postayla kimlik kopyası veya SCHUFA raporu isteniyor
- Emlak portalları adına gelen oltalama (phishing) e-postaları
- Uzun süreli kiralamaları yönetmeyen Airbnb, booking.com veya eBay üzerinden ödeme linkleri
- Yabancı IBAN ve piyasanın çok altında kira

Polis NRW ayrıca kimlik hırsızlığı ve cinsel istismar amaçlı sahte ilanlara karşı uyarıyor; gezilere yalnız gitmeyin.

> ⚠️ **Temel kurallar:** Görmediğiniz bir daire için asla para ödemeyin. Depozito, sözleşme imzalandıktan sonra ve kira ilişkisinin başlangıcında ödenir (§ 551 BGB taksitleriyle). Banka havalesini kendiniz geri alamazsınız; bankanızdan geri çağırma isteyebilirsiniz ama bunun bir garantisi yoktur. Yalnızca SEPA otomatik ödemeleri 8 hafta içinde iade edilebilir. Dolandırıcılığı polise bildirin.

Ayrıca: Ev sahibinin görevlendirdiği emlakçıya komisyon ödemezsiniz (Bestellerprinzip, § 2 WoVermRG) ve emlakçıya ön ödeme yapılmaz. Eski kiracıya yalnızca taşınması için ödeme yapmak geçersizdir (§ 4a WoVermRG); eşya satın alma anlaşması kira sözleşmesine bağlıdır. Daha fazlası için [dolandırıcılıktan korunma SSS](/tr/faq/yurt/almanyada-kiralik-ev-ararken-dolandiriciliklardan-nasil-korunabilirim) sayfasına bakın.

## Kontrol listesi: SCHUFA'sız kira başvuru dosyanız

**Her zaman**

- Kısa, kişisel tanıtım metni (Almanca)
- Kimlik / pasaport / oturum izni (yalnızca gösterilir)
- Finansmanı gösteren özet (gelir veya aylık bütçe)
- "SCHUFA kaydım boş, olumsuz değil" açıklaması

**Öğrenciler**

- Zulassungsbescheid veya Immatrikulationsbescheinigung
- Sperrkonto onayı veya burs mektubu
- Karartılmış banka dökümü (düzenli aylık ödeme)

**Çalışanlar**

- Arbeitsvertrag (maaş sayfası)
- İsteğe bağlı işveren yazısı
- Varsa son maaş bordroları

**Kefille**

- İmzalı Bürgschaft beyanı
- Kefilin gelir kanıtı
- Gerekirse çeviriler

**Yalnızca son aşamada (C)**

- Karartılmış maaş bordrosu / banka dökümü kopyası
- SCHUFA BonitätsCheck (adres ve hesaptan sonra)
- Gerekirse Jobcenter belgesi

Aylık bütçenizin kirayı kaldırıp kaldırmadığını [yaşam maliyeti hesaplayıcısı](/tr/tools/cost-of-living) ile kontrol edebilirsiniz.

## Sık sorulan sorular

### Türkiye'deki Findeks notum Almanya'da işe yarar mı?

Hayır. SCHUFA yalnızca Almanya'da kendisine bildirilen verileri bilir; Findeks raporu veya Türkiye'deki kredi geçmişiniz otomatik olarak aktarılmaz. Yine de Türkiye'deki düzenli gelirinizi veya birikiminizi gösteren belgeler, ev sahibi kabul ederse ödeme gücünüzü destekleyen ek bir kanıt olabilir. Asıl hedefiniz, Almanya'da adres kaydı ve banka hesabıyla kendi SCHUFA geçmişinizi başlatmak olmalı.

### SCHUFA skorum ne kadar sürede oluşur?

SCHUFA'ya göre 6 aydan kısa süredir biliniyorsanız ve banka sözleşmeniz yoksa skor hesaplanmaz. Bir banka sözleşmesi (örneğin vadesiz hesap) olduğunda tam skor hesaplanabilir. Bu yüzden hesap açmak en hızlı adımdır. Skor üç ayda bir güncellenir. Skorunuzu yükseltme yollarını [SCHUFA puanı SSS](/tr/faq/para/almanyada-schufa-puanimi-nasil-yukseltebilirim) sayfasında bulabilirsiniz.

### Ev sahibi ilk gezide SCHUFA isteyebilir mi?

Alman veri koruma otoritelerinin ortak rehberine göre kredi bilgisi yalnızca son aşamaya, yani ev sahibi sizi seçtikten sonrasına aittir. SCHUFA da somut bir taahhüt olmadan ilk gezide rapor istemenin uygun olmadığını belirtir. Pratikte kibarca "Beni seçerseniz BonitätsCheck'i hemen iletirim" demek en iyi yoldur.

### Depozitoyu sözleşmeden önce ödemeli miyim?

Hayır. Depozito kira ilişkisinin başlangıcında, sözleşme imzalandıktan sonra ödenir; nakit depozito üç taksite bölünebilir (§ 551 BGB). Görmediğiniz bir daire için veya sözleşmesiz para istenmesi, tipik dolandırıcılık işaretidir.

### Ailem hem kefil olsun hem de 3 aylık depozito mu ödemeliyim?

Yargı genel olarak, tam 3 aylık depozitonun üzerine ayrıca talep edilen kefaleti § 551'deki sınırı aştığı için geçersiz sayar (BGH VIII ZR 243/03). İstisna, kefaletin istenmeden ve gönüllü olarak teklif edilmesidir. Bu gönüllülüğü kanıtlamak zor olabilir; o yüzden yazışmalarınızı saklayın.

### Yabancı öğrenci olarak kiralama süreci genel olarak nasıl işler?

İlan bulma, gezi, başvuru dosyası, sözleşme, depozito ve adres kaydı adımlarını [yabancı öğrenci olarak ev kiralama SSS](/tr/faq/yurt/almanyada-yabanci-bir-ogrenci-olarak-ev-veya-daire-kiralama-sureci-nasil-isler) sayfasında adım adım anlattık. Taşındıktan sonra ev sahibi Wohnungsgeberbestätigung vermek zorundadır (§ 19 BMG) ve 2 hafta içinde adres kaydı yaptırmanız gerekir; ev sahibi vermezse kayıt ofisine bildirin.

## Kaynaklar

1. Orientierungshilfe zur Einholung von Selbstauskünften bei Mietinteressent:innen, V2.0 — Datenschutzkonferenz (DSK) (Ocak 2026) — https://www.datenschutzkonferenz-online.de/media/oh/OH_Einholung_von_Selbstauskuenften_Mietinteressenten_V2.pdf
2. Musterfragebogen (örnek form) — Datenschutzkonferenz (DSK) (Ocak 2026) — https://www.datenschutzkonferenz-online.de/media/oh/OH_Einholung_von_Selbstauskuenften_Mietinteressenten_V2_Anhang.pdf
3. § 551 BGB (kira güvencesi) — gesetze-im-internet.de — https://www.gesetze-im-internet.de/bgb/__551.html
4. § 540 BGB (alt kiralama) — gesetze-im-internet.de — https://www.gesetze-im-internet.de/bgb/__540.html
5. § 19 BMG (ev sahibi onayı) — gesetze-im-internet.de — https://www.gesetze-im-internet.de/bmg/__19.html
6. § 2 ve § 4a WoVermRG (emlakçı komisyonu, eski kiracıya ödemeler) — gesetze-im-internet.de — https://www.gesetze-im-internet.de/wovermrg/__2.html ; https://www.gesetze-im-internet.de/wovermrg/__4a.html
7. § 19 AGG — gesetze-im-internet.de — https://www.gesetze-im-internet.de/agg/__19.html
8. § 20 PAuswG — gesetze-im-internet.de — https://www.gesetze-im-internet.de/pauswg/__20.html
9. Basın bülteni 199/2009 (VIII ZR 238/08) — Bundesgerichtshof (30.09.2009) — https://www.bundesgerichtshof.de/SharedDocs/Pressemitteilungen/DE/2009/2009199.html
10. VIII ZR 243/03 — Bundesgerichtshof (30.06.2004)
11. SCHUFA-Auskunft für Vermieter — SCHUFA — https://www.schufa.de/newsroom/bonitaet/schufa-auskunft-vermieter-schnell-einfach/
12. Datenkopie ve BonitätsCheck — SCHUFA — https://www.schufa.de/themenportal/datenkopie-bonitaetscheck/index.jsp
13. Yeni SCHUFA skoru basın bülteni — SCHUFA (17.03.2026) — https://www.schufa.de/en/newsroom/press-release/der-neue-schufa-score-transparent-und-leicht-nachzurechnen/index.jsp
14. Verisi olmayan kişiler skor almaz — SCHUFA — https://www.schufa.de/en/newsroom/creditworthiness/fehlende-daten-personen-erhalten-keinen-schufa-score/index.jsp
15. Veriler ve silme süreleri — SCHUFA — https://www.schufa.de/ueber-uns/daten-scoring/daten-schufa/
16. Fake-Wohnungen im Internet — Verbraucherzentrale (07.08.2026) — https://www.verbraucherzentrale.de/wissen/vertraege-reklamation/abzocke/fakewohnungen-im-internet-so-erkennen-sie-falsche-immobilienanzeigen-27576
17. Infoblatt Betrug bei der Wohnungssuche — Polizei Baden-Württemberg (04/2025) — https://praevention.polizei-bw.de/wp-content/uploads/sites/20/2025/04/INFOBLATT-Betrug-Wohnungssuche.pdf
18. Betrug bei Wohnungsangeboten — Polizei NRW (2026) — https://polizei.nrw/artikel/betrug-bei-wohnungsangeboten
19. Wohnplatz başvurusu — studierendenWERK BERLIN — https://www.stw.berlin/wohnen/faq-wohnen/wie-bewerbe-ich-mich-um-einen-wohnplatz.html
20. Konaklama başvurusu — Studierendenwerk München Oberbayern — https://www.studierendenwerk-muenchen-oberbayern.de/en/accommodation/application/
21. Antidiskriminierungsstelle des Bundes — https://www.antidiskriminierungsstelle.de

Bilgiler Eylül 2026'da yukarıdaki kaynaklarla doğrulanmıştır. Bu yazı genel bilgilendirme amaçlıdır, hukuki danışmanlık değildir.

*Durum: Eylül 2026. Kurallar ve ücretler değişebilir; önemli kararlardan önce ilgili resmî kaynağı kontrol edin.*
MD;

        $enBody = <<<'MD'
Yes, you can rent an apartment in Germany without a SCHUFA history. No law requires a SCHUFA report, and a newcomer's record is usually empty, not negative. What gets you the keys is a clear application file that proves you can pay the rent in other ways, handed over at the right moment.

> **Last updated: September 2026** · Verified against official sources: guidance from Germany's data protection authorities (DSK, Jan 2026), the German Civil Code (BGB), SCHUFA and the Verbraucherzentrale (consumer advice center). General information, not legal advice.

## What SCHUFA is and why landlords ask for it

SCHUFA Holding AG is Germany's largest private credit agency. Banks, card issuers, leasing companies, phone providers and other partners report accounts, loans, guarantees, contracts and credit enquiries to it, and SCHUFA calculates a score from that data. Since 17 March 2026, consumers see a new score on a scale of 100 to 999, based on 12 criteria, which you can check for free in the SCHUFA account/app (SCHUFA press release, 17 Mar 2026).

Landlords ask for it for one reason: reassurance that you will pay the rent. For how SCHUFA works and how to build a record over time, read our [SCHUFA guide 2026](/en/blog/schufa-guide-2026-why-is-credit-score-important-for-turkish-students-en). This article covers what to do *before* you have a record at all.

## Why newcomers often have no SCHUFA history

SCHUFA only knows data reported in Germany, so your credit history from home does not carry over. According to SCHUFA, around 1.5% of people have little or no data on file, explicitly including "people who have not previously been economically active in Germany, such as immigrants".

How SCHUFA handles thin files:

- **No data at all:** no score; a company asking receives "no data".
- **Known for less than 6 months, no bank contract:** still no score.
- **Known for more than 6 months, no bank contract:** a score with a "little information" warning.
- **At least one bank contract** (current account, credit card, loan, leasing, guarantee): a full score.

The key point: **an empty record is not a negative one.** Negative entries only arise through a formal process, with at least two written reminders (the first at least four weeks earlier), an undisputed claim and a prior warning about reporting (SCHUFA). If you just arrived, you are "unknown", not "bad", and it helps to tell landlords exactly that.

## Renting without SCHUFA: possible, with nuance

No German statute says you need a SCHUFA report to sign a lease. But landlords are free to choose tenants within data protection and anti-discrimination law, and in tight markets many simply prefer applicants who can show one. No law forces them to accept alternatives.

In practice, student halls, WGs (shared flats) and sublets are usually the easiest start; with private landlords, a strong alternative file improves your chances but guarantees nothing. Your goal is to remove the landlord's uncertainty another way.

## What a landlord may ask for, and when: the DSK stages

In January 2026 Germany's data protection authorities published joint guidance on tenant self-disclosure (DSK *Orientierungshilfe*, version 2.0, with a model questionnaire). It is guidance, not a law, but it shows what the authorities consider acceptable at each step.

| Stage | What may be asked | What should not be asked |
|---|---|---|
| **A – Viewing** | Name, first name, address; the landlord may look at your ID in person and note it was checked; for social housing, whether you have a WBS (housing entitlement certificate) | A copy of your ID ("not necessary and therefore not permitted", DSK); credit reports; payslips |
| **B – You say you want this flat** | Number of people moving in; employer and occupation; net income (saying it is above a threshold is enough); pets (except small caged animals); open consumer insolvency; eviction titles for rent arrears in the last 5 years | Length of employment; reasons for outgoings; religion, ethnic origin, nationality; your former landlord's contact details |
| **C – Landlord has chosen you (shortly before signing)** | Proof of ability to pay (payslip, bank statement or tax assessment) as a copy with unneeded details blacked out; credit information such as a rental-specific credit report; where relevant, proof that a public body pays the rent | Your full SCHUFA Datenkopie; unredacted bank histories; a Mietschuldenfreiheitsbescheinigung (rent-arrears clearance certificate) |

Two more points from the DSK: making the tenancy conditional on your "consent" to a credit check is not freely given consent, and if you already provided a rental-specific report, the landlord should not run an extra query.

## Documents that can replace SCHUFA in practice

No official source lists "SCHUFA substitutes". The documents below can, in practice, support your ability to pay; the landlord decides what is enough.

| Document | Who it suits | What it shows | How strong |
|---|---|---|---|
| Arbeitsvertrag (employment contract) | New employees | Salary and income ahead | Strong |
| Gehaltsabrechnungen (payslips) | Anyone already paid in Germany | Actual income received | Strong |
| Bürgschaft (guarantee) | Students, early-career newcomers | Someone else stands behind the rent | Strong with a guarantor earning in Germany; medium otherwise |
| Sperrkonto (blocked account) confirmation / bank statement, blacked out | Students | Funds for living costs | Medium |
| Immatrikulationsbescheinigung (enrolment certificate) | Enrolled students | Registered student status | Supporting |
| Zulassungsbescheid (admission letter) | Students before enrolment | Why and how long you'll be in the city | Supporting |
| Scholarship letter | Scholarship holders | Regular funding and its duration | Medium to strong |
| SCHUFA BonitätsCheck with no negative entries | Anyone with a German address | No payment problems on record | Medium |
| Mietschuldenfreiheitsbescheinigung | People who rented in Germany before | No rent arrears with a previous landlord | Newcomers rarely have one; it cannot be demanded |
| ID / residence permit | Everyone | Identity and right to stay | Show in person; no early copies |

A Sperrkonto confirmation or scholarship letter is a financing proof for your visa, not a rental document by law, but some landlords may accept it as practical evidence of your ability to pay. To check the amount you need, use our [Sperrkonto calculator](/en/tools/sperrkonto).

Since tenants have no claim to a Mietschuldenfreiheitsbescheinigung from a former landlord (BGH, VIII ZR 238/08, 2009), the DSK concludes a new landlord cannot demand one.

### Datenkopie vs BonitätsCheck: which one to show

- **Datenkopie** (data copy under Art. 15 GDPR): free from SCHUFA, "for your personal information". It lists everything stored about you. SCHUFA and the DSK advise **not** to give it to landlords, and you don't need paid middlemen to get it.
- **BonitätsCheck** (digital PDF with a verification code) or **BonitätsAuskunft** (paper, by post in 2–4 working days; pass on only page 1): made for landlords. It shows whether negative payment information exists; **no score is shown to the landlord.** It costs around €30.

For a newcomer, a BonitätsCheck typically shows no negative entries. Once you have a German address, request one and keep it for stage C.

## Four real application scenarios

These are illustrative, not real cases.

### Master's student who just arrived

Priya from India starts a Master's in Munich in October. She has an admission letter and a funded blocked account, but no German income or SCHUFA record.

What to put in your file:

- Admission letter or enrolment certificate
- Sperrkonto confirmation (balance and monthly payout visible, account numbers partly blacked out)
- A guarantee from a parent, if possible
- A cover letter explaining that your SCHUFA file is empty, not negative
- BonitätsCheck as soon as you are registered at an address

### New employee: contract signed, no payslip yet

Lucas from Brazil signed a developer contract in Berlin that starts next month, so there is no German payslip yet.

What to put in your file:

- Employment contract with salary, start date and employer visible
- An employer confirmation letter, if your employer provides one
- Payslips from your previous job abroad, to show continuity
- Bank statement showing savings for the deposit and first rents (blacked out)

### No German bank history at all

Amira from Egypt is still waiting for a German bank account, and without an address many banks are slow to open one. Our guide on [opening a bank account in Germany](/en/blog/opening-a-bank-account-in-germany-the-real-obstacles-en) explains how to break that loop.

What to put in your file:

- Statement from your home bank showing your balance (redacted)
- Proof of income or funding (contract, scholarship, Sperrkonto)
- An offer to pay rent by standing order once your German account is open
- Plan B: a dorm, WG or sublet first, so you can register, open an account and then apply for a long-term flat

### Applying with a parent or other guarantor

Daniel from the US starts a Bachelor's in Berlin, and his mother is willing to sign a Bürgschaft, a written promise to pay if he doesn't.

What to put in your file:

- Signed guarantee declaration
- Guarantor's proof of income and ID (shown at stage C, redacted)
- Your own student documents

In practice, a guarantee from abroad can be harder to enforce, so some landlords may value it less. The legal limits are explained in the guarantor section below.

## Housing options for new students: dorm, WG, Zwischenmiete, private landlord

Your first home doesn't have to be your long-term one. Our [student housing overview](/en/housing) compares the routes.

**Student halls (Studierendenwerk).** Halls usually ask for proof of study rather than SCHUFA, but waiting lists are long, so apply early.

- In [Berlin](/en/cities/berlin-q64), studierendenWERK BERLIN asks for your admission letter or enrolment certificate, proof of semester fee payment and your ID or passport. You must re-confirm by email every 30 days (replying within 5 days), or your application lapses.
- In [Munich](/en/cities/munchen-q1726), the Studierendenwerk München Oberbayern takes applications online; winter-semester applications open on 15 May, places are allocated by application date, and offers typically come about 3–8 weeks before the start.

**WG (shared flat).** Flatmates usually care more about fit than SCHUFA. See our [guide to finding a WG](/en/blog/finding-a-wg-in-germany-your-comprehensive-guide-to-dorm-wg-en) and the FAQ on [how to find a WG](/en/faq/yurt/almanyada-paylasimli-ogrenci-evi-wg-nasil-bulunur-en).

**Zwischenmiete / Untermiete (sublet).** Subletting needs the main landlord's permission (§ 540 BGB). Ask to see that consent in writing.

**Private landlord.** Expect the stage-by-stage process above. A tidy file and an honest cover letter make the biggest difference; our [apartment application template](/en/templates/wohnungsbewerbung) helps you structure it.

To see realistic rents, check the [student rent map](/en/student-rent-map), and plan your costs with the [budget planner](/en/tools/budget-planner).

## Guarantor (Bürgschaft) explained

A guarantee is the classic way to compensate for a missing SCHUFA record, within firm legal limits:

- **Cap:** Under § 551 BGB, total rental security is at most three months' rent excluding operating costs. A cash deposit may be paid in three monthly instalments; the first is due at the start of the tenancy, the others with the next two rent payments. Terms worse for the tenant are void.
- **No stacking:** The BGH (VIII ZR 243/03, 30 June 2004) held that several securities together may not exceed that cap; a parental guarantee demanded on top of a full three-month deposit was invalid.
- **Voluntary exception:** Case law generally treats a guarantee differently if a third party offers it voluntarily, unrequested, on condition that the lease is signed. That can be hard to prove, so keep the correspondence.

Commercial guarantor and deposit-insurance services also exist. They cost money and terms vary, so check the conditions and total costs carefully.

## Sample message to a landlord

Landlords usually expect German. Adapt this short message (more options in our [apartment enquiry template](/en/housing/templates/wohnungsanfrage)):

> Sehr geehrte Damen und Herren,
>
> ich interessiere mich sehr für Ihre Wohnung in der [Straße] und würde sie gerne besichtigen. Ich bin vor Kurzem nach Deutschland gezogen und studiere/arbeite ab [Monat] an/bei [Hochschule/Arbeitgeber]. Da ich neu in Deutschland bin, habe ich noch keine SCHUFA-Historie – mein Eintrag ist leer, nicht negativ.
>
> Zum passenden Zeitpunkt lege ich Ihnen gerne Nachweise über meine Zahlungsfähigkeit vor, z. B. [Arbeitsvertrag / Sperrkonto-Bestätigung / Bürgschaft meiner Eltern]. Eine SCHUFA-BonitätsAuskunft reiche ich nach, sobald sie verfügbar ist.
>
> Mit freundlichen Grüßen
> [Vorname Nachname] · [Telefon]

In English: you'd like to view the flat; you're new in Germany, so your SCHUFA file is empty, not negative; you can show proof of ability to pay at the appropriate stage; and you'll provide a SCHUFA report once available.

## What you should not hand over unnecessarily

Data minimization protects you from identity theft. Based on the DSK guidance:

- **ID copies at the viewing:** showing your ID is enough. German ID cards may only be copied by or with the consent of the holder, clearly marked as a copy (§ 20 PAuswG). Treat your passport the same way: if a copy is really needed at the final stage, black out unneeded fields and write "Copy for rental application [address] only" across it.
- **Full bank history:** show a balance or recent income, other transactions blacked out.
- **Your SCHUFA Datenkopie:** show a BonitätsCheck instead.
- **Nationality, religion, ethnic origin, pregnancy, plans to marry or have children, party membership:** the DSK considers these questions not permitted; marital status is generally not necessary.
- **Your former landlord's contact details:** a landlord should not ask for them.

According to the DSK, landlords must delete unsuccessful applicants' data; where discrimination claims are possible, as a rule within six months at most.

If you believe you were rejected because of your origin: the General Equal Treatment Act (AGG) prohibits discrimination on grounds of ethnic origin in housing, regardless of landlord size (§ 19(2) AGG). Claims currently must be made within **two months**; a reform extending this to four months is pending but not yet law. The [Federal Anti-Discrimination Agency](https://www.antidiskriminierungsstelle.de) offers initial advice and notes that ads aimed only at "German-speaking" applicants can be indirect discrimination.

## How to spot rental scams

Newcomers under time pressure are a favorite target. Red flags named by the Verbraucherzentrale and police:

- The owner is "abroad" and will post the keys after you pay.
- A deposit or "reservation fee" before a viewing, or a deposit after a viewing but before a contract.
- A fee to get on a "pre-selection list".
- Requests to email an ID copy or SCHUFA report at first contact.
- Fake emails in the name of property portals.
- Payment "via Airbnb, booking.com or eBay", although these don't manage long-term rentals.
- A foreign IBAN or a rent far below market level.

Police in North Rhine-Westphalia also warn about identity theft and sexual exploitation disguised as flat offers: don't go to viewings alone.

> ⚠️ **Rules that protect you:** Never pay for a flat you have not seen. The deposit is due only at the agreed start of the tenancy, after signing, and may be paid in instalments (§ 551 BGB). You generally cannot reverse a bank transfer yourself; your bank may attempt a recall, but you have no right to one. Report scams to the police.

Also: you pay no broker fee if the landlord hired the broker (§ 2 WoVermRG), and payments to a previous tenant just for moving out are void (§ 4a WoVermRG). More in our FAQ on [avoiding rental scams](/en/faq/yurt/almanyada-kiralik-ev-ararken-dolandiriciliklardan-nasil-korunabilirim-en).

## Checklist: your rental application file without SCHUFA

Always:
- Short cover letter (new in Germany, SCHUFA file empty, not negative)
- Passport or residence permit to show in person
- Proof of funds for deposit and first rents (balance only)
- Contact details and desired move-in date

Students:
- Admission letter or enrolment certificate
- Sperrkonto confirmation
- Scholarship letter, if any

Employees:
- Employment contract
- Employer confirmation letter, if available
- Latest payslips (German or previous job)

With guarantor:
- Signed guarantee declaration
- Guarantor's proof of income (redacted)

Only at the final stage (C):
- Redacted copies of payslips or bank statements
- SCHUFA BonitätsCheck (not the Datenkopie)
- Guarantor's ID, if requested
- Copies marked "for rental application [address] only"

After moving in, your landlord must confirm your move-in (Wohnungsgeberbestätigung, § 19 BMG), and you must register within two weeks. Our [Anmeldung guide](/en/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt-en) walks you through it.

## Frequently asked questions

### Can a landlord turn me down just because I have no SCHUFA report?

Yes, in most cases. No law obliges a landlord to accept alternatives, and landlords may choose among applicants as long as they respect data protection and anti-discrimination rules. They may not reject you because of your ethnic origin. The practical answer is a file so clear that the missing SCHUFA record stops looking like a risk, plus applying widely, including to dorms, WGs and sublets.

### Will paying rent on time build my SCHUFA score?

Not by itself. Landlords don't normally report rent payments to SCHUFA; what builds your record is German bank contracts such as a current account or credit card. Conversely, a landlord cannot simply "report late rent": a negative entry requires a formal process with reminders and an undisputed claim. Our FAQ on [improving your SCHUFA score](/en/faq/para/almanyada-schufa-puanimi-nasil-yukseltebilirim-en) has more.

### How much does a SCHUFA report for landlords cost?

The BonitätsCheck or BonitätsAuskunft costs around €30. The GDPR Datenkopie is free directly from SCHUFA, but it contains too much data to give to a landlord, and you should avoid sites that charge for it. For renting, the BonitätsCheck is the right document because it only shows whether negative entries exist, not your score.

### Does my credit history from home count for anything?

Not in SCHUFA, which only knows data reported in Germany. You can still use it as supporting evidence: a bank statement or a letter from a previous landlord abroad can show reliability. The documents that usually carry the most weight are an employment contract, payslips, a Sperrkonto confirmation or a guarantee.

### Can the landlord ask for three months' deposit plus a guarantee?

Generally not. Under § 551 BGB, all rental securities together may not exceed three months' rent excluding operating costs, and the BGH ruled that a parental guarantee demanded on top of a full deposit was invalid. The exception is a guarantee offered voluntarily and unrequested. If you're unsure about a specific request, get it checked by a tenants' association or legal advice service.

### What if I need a place immediately?

Start short-term: a sublet with the main landlord's written consent, a WG room or a furnished temporary room. That gives you an address for your Anmeldung, which makes opening a bank account and requesting a BonitätsCheck easier. Then apply for long-term flats with a stronger file. See also our FAQ on [renting as a foreign student](/en/faq/yurt/almanyada-yabanci-bir-ogrenci-olarak-ev-veya-daire-kiralama-sureci-nasil-isler-en).

## Sources

1. Orientierungshilfe zur Einholung von Selbstauskünften bei Mietinteressent:innen, V2.0 — Datenschutzkonferenz (DSK) (January 2026) — https://www.datenschutzkonferenz-online.de/media/oh/OH_Einholung_von_Selbstauskuenften_Mietinteressenten_V2.pdf
2. Musterfragebogen (model questionnaire) — Datenschutzkonferenz (DSK) (January 2026) — https://www.datenschutzkonferenz-online.de/media/oh/OH_Einholung_von_Selbstauskuenften_Mietinteressenten_V2_Anhang.pdf
3. § 551 BGB (rental security) — German Civil Code — https://www.gesetze-im-internet.de/bgb/__551.html
4. § 540 BGB (subletting) — German Civil Code — https://www.gesetze-im-internet.de/bgb/__540.html
5. § 19 BMG (landlord confirmation) — Federal Registration Act — https://www.gesetze-im-internet.de/bmg/__19.html
6. § 2 and § 4a WoVermRG — Housing Brokerage Act — https://www.gesetze-im-internet.de/wovermrg/__2.html ; https://www.gesetze-im-internet.de/wovermrg/__4a.html
7. § 19 AGG — General Equal Treatment Act — https://www.gesetze-im-internet.de/agg/__19.html
8. § 20 PAuswG — Identity Card Act — https://www.gesetze-im-internet.de/pauswg/__20.html
9. Press release 199/2009 (VIII ZR 238/08) — Federal Court of Justice (BGH) (30 September 2009) — https://www.bundesgerichtshof.de/SharedDocs/Pressemitteilungen/DE/2009/2009199.html
10. Judgment VIII ZR 243/03 — Federal Court of Justice (BGH) (30 June 2004)
11. SCHUFA-Auskunft für Vermieter — SCHUFA — https://www.schufa.de/newsroom/bonitaet/schufa-auskunft-vermieter-schnell-einfach/
12. Datenkopie vs BonitätsCheck — SCHUFA — https://www.schufa.de/themenportal/datenkopie-bonitaetscheck/index.jsp
13. The new SCHUFA score — SCHUFA (17 March 2026) — https://www.schufa.de/en/newsroom/press-release/der-neue-schufa-score-transparent-und-leicht-nachzurechnen/index.jsp
14. People with missing data receive no SCHUFA score — SCHUFA — https://www.schufa.de/en/newsroom/creditworthiness/fehlende-daten-personen-erhalten-keinen-schufa-score/index.jsp
15. Data at SCHUFA and deletion periods — SCHUFA — https://www.schufa.de/ueber-uns/daten-scoring/daten-schufa/
16. Fake-Wohnungen im Internet — Verbraucherzentrale (as of 7 August 2026) — https://www.verbraucherzentrale.de/wissen/vertraege-reklamation/abzocke/fakewohnungen-im-internet-so-erkennen-sie-falsche-immobilienanzeigen-27576
17. Infoblatt Betrug bei der Wohnungssuche — Polizei Baden-Württemberg (April 2025) — https://praevention.polizei-bw.de/wp-content/uploads/sites/20/2025/04/INFOBLATT-Betrug-Wohnungssuche.pdf
18. Betrug bei Wohnungsangeboten — Polizei NRW (2026) — https://polizei.nrw/artikel/betrug-bei-wohnungsangeboten
19. How to apply for a room — studierendenWERK BERLIN — https://www.stw.berlin/wohnen/faq-wohnen/wie-bewerbe-ich-mich-um-einen-wohnplatz.html
20. Accommodation application — Studierendenwerk München Oberbayern — https://www.studierendenwerk-muenchen-oberbayern.de/en/accommodation/application/
21. Antidiskriminierungsstelle des Bundes (Federal Anti-Discrimination Agency) — https://www.antidiskriminierungsstelle.de

Information verified against the sources above in September 2026. This article is general information, not legal advice.

*Status: September 2026. Rules and prices can change; check the linked official sources before you sign anything.*
MD;

        $deBody = <<<'MD'
Ja, Sie können in Deutschland eine Wohnung mieten, auch wenn Sie noch keine SCHUFA-Historie haben: Kein Gesetz schreibt eine SCHUFA-Auskunft für einen Mietvertrag vor, und manche Vermieter akzeptieren in der Praxis andere Nachweise wie Arbeitsvertrag, Kontoauszug, Sperrkonto-Bestätigung oder eine Bürgschaft. Entscheidend ist, dass Ihre Bewerbungsmappe zur richtigen Phase die richtigen Unterlagen enthält und Sie offen erklären, warum Ihre SCHUFA-Akte (noch) leer ist.

Wichtig vorab: Dieser Artikel richtet sich an Menschen, die neu in Deutschland sind und **noch keine SCHUFA-Historie** haben, also eine leere Akte. Wer **negative SCHUFA-Einträge** hat (etwa wegen offener Forderungen), ist in einer anderen Situation, die wir hier nicht behandeln.

> **Stand: September 2026** · Geprüft anhand offizieller Quellen: Orientierungshilfe der Datenschutzkonferenz (DSK, Januar 2026), Bürgerliches Gesetzbuch (BGB), SCHUFA, Verbraucherzentrale. Keine Rechtsberatung.

## Was die SCHUFA ist und warum Vermieter danach fragen

Die SCHUFA Holding AG ist die größte private Auskunftei in Deutschland. Sie speichert Daten, die Unternehmen an sie melden, zum Beispiel Girokonten, Kreditkarten, Kredite, Leasing, Bürgschaften, bestimmte Handy- und andere Verträge sowie Anfragen, und berechnet daraus einen Score. Seit dem 17. März 2026 gibt es für Verbraucher den neuen SCHUFA-Score auf einer Skala von 100 bis 999, berechnet aus 12 Kriterien. Er ersetzt die früheren Branchenscores, und Sie können ihn kostenlos im SCHUFA-Account bzw. in der App einsehen (SCHUFA, Pressemitteilung 17.03.2026).

Vermieter fragen danach, weil sie wissen wollen, ob es bereits Zahlungsprobleme gab. Wie der Score funktioniert und wie Sie ihn aufbauen, erklärt unser [SCHUFA-Leitfaden 2026](/de/blog/schufa-guide-2026-why-is-credit-score-important-for-turkish-students-de).

## Warum Neuankömmlinge oft keine SCHUFA-Historie haben

Die SCHUFA kennt nur Daten, die in Deutschland gemeldet wurden. Ihre Kreditgeschichte aus dem Herkunftsland, etwa aus der Türkei, Indien oder Brasilien, wird nicht übernommen. Laut SCHUFA haben rund 1,5 % der Menschen wenig oder gar keine SCHUFA-Daten, ausdrücklich auch Personen, die bisher in Deutschland nicht wirtschaftlich aktiv waren, zum Beispiel Zugewanderte.

So funktioniert es nach Angaben der SCHUFA:

- **Keine Daten:** Es wird kein Score berechnet. Anfragende Unternehmen erhalten den Hinweis, dass keine Daten vorliegen.
- **Seit weniger als 6 Monaten bekannt, kein Bankvertrag:** kein Score.
- **Seit mehr als 6 Monaten bekannt, kein Bankvertrag:** ein Score mit dem Hinweis auf „wenig Informationen“.
- **Mindestens ein Bankvertrag** (Girokonto, Kreditkarte, Leasing, Bürgschaft oder Kredit): ein vollständiger Score.

Der wichtigste Punkt für Ihre Wohnungssuche: **Eine leere Akte ist nicht dasselbe wie eine negative Akte.** Sie sind kein „schlechter“ Mieter, Sie sind einfach neu. Genau das sollten Sie Vermietern auch so erklären. Der erste Schritt zu einer Historie ist meist ein deutsches Girokonto; welche Hürden es dabei gibt, lesen Sie in unserem Beitrag zur [Kontoeröffnung in Deutschland](/de/blog/opening-a-bank-account-in-germany-the-real-obstacles-de).

## Ohne SCHUFA mieten: möglich, aber mit Einschränkungen

Es gibt kein Gesetz, das eine SCHUFA-Auskunft für den Abschluss eines Mietvertrags verlangt. Vermieter dürfen ihre Mieter allerdings frei auswählen, solange sie Datenschutz- und Antidiskriminierungsrecht beachten. Das bedeutet auch: Niemand zwingt einen Vermieter, Alternativen zur SCHUFA zu akzeptieren.

In angespannten Märkten wie Berlin oder München bevorzugen viele Vermieter in der Praxis Bewerber mit SCHUFA-Auskunft. Ihre Chancen steigen, wenn Sie Ihre Zahlungsfähigkeit auf anderem Weg glaubhaft machen. Einen Überblick über den Mietmarkt und Anbieter finden Sie auf unserer Seite [Wohnen in Deutschland](/de/housing).

## Was Vermieter wann fragen dürfen: die Phasen laut DSK

Laut Orientierungshilfe der Datenschutzkonferenz (DSK, Januar 2026) dürfen Vermieter nicht alles auf einmal verlangen. Die Datenschutzbehörden unterscheiden drei Phasen. Sie ist kein Gesetz, zeigt aber, wie die Aufsichtsbehörden das Datenschutzrecht auslegen.

| Phase | Was gefragt werden darf | Was nicht |
|---|---|---|
| **A – Besichtigung** | Name, Vorname, Anschrift; Ausweis darf vorgezeigt und die Prüfung notiert werden; bei Sozialwohnungen: ob ein WBS vorliegt | Kopie des Ausweises („nicht erforderlich und daher nicht zulässig“), Bonitätsauskunft, Gehaltsnachweise |
| **B – Sie erklären, dass Sie genau diese Wohnung möchten** | Anzahl der einziehenden Personen, Arbeitgeber und Beruf, Nettoeinkommen (Angabe „über einer bestimmten Grenze“ genügt), Haustiere (außer Kleintiere im Käfig), laufende Verbraucherinsolvenz, Räumungstitel wegen Mietrückständen in den letzten 5 Jahren | Dauer der Beschäftigung, Gründe für Ausgaben |
| **C – Der Vermieter hat sich für Sie entschieden (kurz vor Vertragsschluss)** | Nachweis der Zahlungsfähigkeit, z. B. Gehaltsabrechnung, Kontoauszug oder Steuerbescheid als geschwärzte Kopie; Bonitätsauskunft (z. B. mietspezifische Auskunft); ggf. Nachweis über Mietzahlung durch öffentliche Stellen | Vollständige Kontohistorie, ungeschwärzte Unterlagen, SCHUFA-Datenkopie |

Nicht zulässig sind laut DSK außerdem Fragen nach Religion, ethnischer Herkunft, Staatsangehörigkeit, Vorstrafen, Heiratsplänen, Schwangerschaft, Kinderwunsch, Partei- oder Mietervereinsmitgliedschaft und nach dem Vorvermieter; der Familienstand ist in der Regel nicht erforderlich. Eine Landesbehörde sieht einzelne Punkte teilweise anders.

Auch die SCHUFA schreibt: Anspruch auf Bonitätsinformationen hat ein Vermieter erst, wenn Sie in der engeren Auswahl sind, nicht schon bei der ersten Besichtigung. Und laut DSK ist eine Einwilligung in eine Bonitätsprüfung nicht freiwillig, wenn die Vermietung davon abhängig gemacht wird. Haben Sie bereits eine mietspezifische Auskunft vorgelegt, ist eine zusätzliche Abfrage durch den Vermieter laut DSK nicht zulässig.

## Dokumente, die die SCHUFA in der Praxis ersetzen können

Keine amtliche Stelle legt fest, welche Unterlagen eine SCHUFA-Auskunft „ersetzen“. Die folgenden Dokumente können Ihre Zahlungsfähigkeit in der Praxis aber gut belegen. Ob sie genügen, entscheidet der Vermieter.

| Dokument | Für wen geeignet | Was es zeigt | Aussagekraft |
|---|---|---|---|
| Arbeitsvertrag | Neue Beschäftigte | Künftiges Einkommen, Arbeitgeber, Befristung | stark |
| Gehaltsabrechnungen | Beschäftigte ab dem ersten Monat | Tatsächlich gezahltes Nettogehalt | stark |
| Bürgschaft (Eltern oder andere Person) | Studierende, Berufseinsteiger | Eine zweite Person haftet für die Miete | stark, wenn der Bürge solvent ist |
| Sperrkonto-Bestätigung / Kontoauszug (geschwärzt) | Studierende, alle ohne deutsches Einkommen | Verfügbares Guthaben bzw. monatliche Auszahlung | mittel bis stark |
| Stipendienbescheid | Stipendiaten | Laufende monatliche Förderung | mittel bis stark |
| Zulassungsbescheid | Studierende vor der Einschreibung | Grund und Dauer Ihres Aufenthalts | unterstützend |
| Immatrikulationsbescheinigung | Eingeschriebene Studierende | Aktiver Studierendenstatus | unterstützend |
| SCHUFA-BonitätsCheck ohne Negativeinträge | Alle mit deutscher Adresse (idealerweise mit Konto) | Dass keine negativen Zahlungsinformationen vorliegen | mittel |
| Mietschuldenfreiheitsbescheinigung | Nur wer schon in Deutschland gemietet hat | Keine Mietrückstände beim Vorvermieter | Neuankömmlinge haben sie meist nicht; darf nicht verlangt werden |
| Ausweis / Aufenthaltstitel | Alle | Identität und Aufenthaltsstatus | Vorzeigen genügt, keine Kopie abgeben |

Zur Mietschuldenfreiheitsbescheinigung: Der Bundesgerichtshof hat entschieden, dass Mieter gegenüber dem früheren Vermieter keinen Anspruch auf eine Bescheinigung haben, die über Quittungen für die gezahlte Miete hinausgeht (BGH, VIII ZR 238/08, 30.09.2009). Laut DSK kann ein neuer Vermieter sie deshalb nicht verlangen. Ein freiwilliges Schreiben eines Vermieters aus Ihrem Herkunftsland ist optional.

Sperrkonto-Bestätigung und Stipendienbescheid sind Finanzierungsnachweise für das Visum, keine gesetzlich vorgesehenen Mietunterlagen; Vermieter können sie aber als praktischen Nachweis akzeptieren. Wie das Sperrkonto funktioniert, zeigt unser [Sperrkonto-Rechner](/de/tools/sperrkonto).

### Datenkopie oder BonitätsCheck: welches Dokument Sie zeigen

- **Datenkopie (nach Art. 15 DSGVO):** kostenlos direkt bei der SCHUFA. Sie enthält alle gespeicherten Daten, die Anfragen der letzten 12 Monate und übermittelte Scores, und ist für Ihre persönliche Information gedacht. Zwischenhändler verlangen dafür Geld, das brauchen Sie nicht. SCHUFA und DSK raten davon ab, die Datenkopie an Vermieter weiterzugeben, weil sie zu viele Daten enthält; verlangen darf der Vermieter sie nicht.
- **BonitätsCheck (digitales PDF mit Verifizierungscode) bzw. BonitätsAuskunft (Papier, per Post in 2 bis 4 Werktagen; nur Seite 1 weitergeben):** speziell für Vermieter gemacht. Der Vermieterteil zeigt nur, ob negative Zahlungsinformationen vorliegen. Einen Score sieht der Vermieter nicht. Kosten: rund 30 €.

Bei einer leeren Akte zeigt der BonitätsCheck in der Regel schlicht, dass keine Negativeinträge vorliegen. Das ist kein schlechtes Ergebnis. Sobald Sie eine deutsche Adresse haben, können Sie ihn beantragen und in Phase C vorlegen.

## Vier typische Bewerbungssituationen

Die folgenden Beispiele sind zur Veranschaulichung gedacht. Passen Sie Ihre Mappe immer an Ihre eigene Situation an.

### Masterstudentin, gerade angekommen

Priya kommt aus Indien, beginnt ihr Masterstudium in Berlin und wohnt die ersten Wochen in einem möblierten Zimmer. Ihr Visum wurde mit einem Sperrkonto finanziert, eine SCHUFA-Akte hat sie noch nicht.

**Was in die Mappe gehört:**

- Zulassungsbescheid oder, sobald vorhanden, Immatrikulationsbescheinigung
- Sperrkonto-Bestätigung mit monatlichem Auszahlungsbetrag (geschwärzt, was nicht nötig ist)
- Optional: Bürgschaft der Eltern (siehe unten)
- BonitätsCheck, sobald sie gemeldet ist

### Neuer Job: Vertrag unterschrieben, noch keine Gehaltsabrechnung

Marco ist aus Italien nach Frankfurt gezogen und hat einen unbefristeten Arbeitsvertrag. Das erste Gehalt kommt erst Ende des Monats, eine deutsche Gehaltsabrechnung gibt es also noch nicht.

**Was in die Mappe gehört:**

- Arbeitsvertrag (Gehaltsangabe sichtbar, unnötige Details geschwärzt)
- In Phase B: Angabe, dass das Nettoeinkommen über einer bestimmten Grenze liegt (das genügt laut DSK)
- Erste Gehaltsabrechnung nachreichen, sobald sie da ist
- BonitätsCheck nach der Anmeldung

### Noch gar keine deutsche Bankhistorie

Ahmet ist gerade in Köln angekommen, hat noch kein deutsches Konto und nutzt vorerst seine Karte aus der Türkei. Ohne Bankvertrag gibt es auch keinen vollständigen Score.

**Was in die Mappe gehört:**

- Kontoauszug seines Heimatkontos oder Sperrkonto-Bestätigung (geschwärzt, nur Guthaben und Name sichtbar)
- Zulassungsbescheid oder Arbeitsvertrag, je nach Situation
- Bürgschaft, falls möglich
- Hinweis, dass ein deutsches Konto gerade eröffnet wird

Tipp: Für viele Konten brauchen Sie eine Meldeadresse, für die Anmeldung eine Wohnungsgeberbestätigung; mehr in unserer [Anleitung zur Anmeldung](/de/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt-de). Der Vermieter muss Ihnen den Einzug bestätigen (§ 19 BMG), und Sie müssen sich innerhalb von zwei Wochen anmelden (§ 17 BMG). Verweigert der Vermieter die Bestätigung, informieren Sie die Meldebehörde.

### Bewerbung mit Eltern oder anderer Person als Bürge

Lena aus der Ukraine studiert in Leipzig. Ihre Tante lebt seit Jahren in Deutschland, hat ein festes Einkommen und bietet an, für sie zu bürgen.

**Was in die Mappe gehört:**

- Eigene Unterlagen (Immatrikulation, Kontoauszug)
- Bürgschaftserklärung der Tante
- Einkommensnachweis der Bürgin in Phase C (geschwärzt)
- Hinweis, dass die Bürgschaft freiwillig angeboten wird (Regeln im nächsten Abschnitt)

## Bürgschaft: die wichtigsten Regeln

Eine Bürgschaft bedeutet, dass eine andere Person für Ihre Mietschulden einsteht, falls Sie nicht zahlen. Für Neuankömmlinge ohne SCHUFA-Historie ist sie oft das stärkste Argument. Diese Regeln sollten Sie kennen:

- **Obergrenze nach § 551 BGB:** Die Mietsicherheit darf höchstens drei Monatsmieten ohne Betriebskosten (drei Nettokaltmieten) betragen. Eine Barkaution dürfen Sie in drei gleichen Monatsraten zahlen; die erste ist zu Beginn des Mietverhältnisses fällig, die anderen mit den nächsten beiden Mieten. Abweichende Vereinbarungen zu Ihrem Nachteil sind unwirksam.
- **Kein Addieren von Sicherheiten:** Die Rechtsprechung geht grundsätzlich davon aus, dass mehrere Sicherheiten zusammen diese Grenze nicht überschreiten dürfen. Im Fall BGH VIII ZR 243/03 (30.06.2004) war eine Elternbürgschaft, die zusätzlich zu einer vollen Kaution von drei Monatsmieten verlangt wurde, unwirksam.
- **Ausnahme bei Freiwilligkeit:** Bietet ein Dritter die Bürgschaft von sich aus, ohne Aufforderung und unter der Bedingung an, dass der Mietvertrag zustande kommt, verstößt sie nach der Rechtsprechung nicht gegen § 551 BGB. In der Praxis kann es allerdings schwer sein, diese Freiwilligkeit später zu belegen.
- **Kommerzielle Anbieter:** Es gibt gewerbliche Bürgschafts- und Mietkautionsdienste. Wir empfehlen keinen bestimmten Anbieter. Prüfen Sie Kosten und Bedingungen genau, bevor Sie einen solchen Vertrag abschließen.

## Wohnoptionen für neue Studierende: Wohnheim, WG, Zwischenmiete, privater Vermieter

### Studierendenwohnheim

Wohnheime der Studierendenwerke verlangen in der Regel einen Studiennachweis statt einer SCHUFA-Auskunft. Die Wartelisten sind aber lang, bewerben Sie sich also früh.

- **Berlin (studierendenWERK BERLIN):** Bewerbung mit Zulassungsbescheid oder Immatrikulationsbescheinigung, Nachweis über den gezahlten Semesterbeitrag und Ausweis. Sie stehen auf einer Warteliste und müssen Ihr Interesse regelmäßig per E-Mail bestätigen (alle 30 Tage, Antwort innerhalb von 5 Tagen), sonst verfällt die Bewerbung. Mehr zur Stadt: [Berlin](/de/cities/berlin-q64).
- **München (Studierendenwerk München Oberbayern):** Online-Bewerbung; für das Wintersemester ab dem 15. Mai. Vergeben wird nach Bewerbungsdatum, Angebote kommen typischerweise etwa 3 bis 8 Wochen vor Semesterbeginn. Mehr zur Stadt: [München](/de/cities/munchen-q1726).

Auch im Wohnheim gilt die Kautionsgrenze von drei Monatsmieten; Wohnheimträger müssen die Kaution allerdings nicht verzinsen (§ 551 Abs. 3 BGB).

### WG

In einer Wohngemeinschaft entscheiden oft die Mitbewohner mit, und persönliche Sympathie zählt häufig mehr als Unterlagen. Wie Sie überzeugend auftreten, zeigt unser [WG-Leitfaden](/de/blog/finding-a-wg-in-germany-your-comprehensive-guide-to-dorm-wg-de); kurze Antworten finden Sie auch in der FAQ [Wie finde ich eine WG?](/de/faq/yurt/almanyada-paylasimli-ogrenci-evi-wg-nasil-bulunur-de).

### Zwischenmiete und Untermiete

Eine Zwischenmiete ist oft der schnellste Einstieg, aber Untervermietung braucht die Erlaubnis des Hauptvermieters (§ 540 BGB). Lassen Sie sich diese Zustimmung zeigen, sonst riskieren Sie, plötzlich ohne Zimmer dazustehen.

### Privater Vermieter

Private Vermieter erwarten meist eine vollständige Mappe und eine klare Anfrage. Vorlagen dafür finden Sie unter [Wohnungsanfrage](/de/housing/templates/wohnungsanfrage) und [Wohnungsbewerbung](/de/templates/wohnungsbewerbung). Welche Mieten realistisch sind, zeigt unsere [Mietkarte für Studierende](/de/student-rent-map); Ihr Gesamtbudget können Sie mit dem [Lebenshaltungskosten-Rechner](/de/tools/cost-of-living) planen.

## Beispielnachricht an einen Vermieter

Diese Nachricht eignet sich für die erste Kontaktaufnahme oder als Ergänzung in Phase B. Passen Sie die Angaben an:

> Sehr geehrte Frau Müller,
>
> vielen Dank für die Besichtigung. Ich interessiere mich sehr für die Wohnung in der Musterstraße und möchte mich hiermit gern um die Wohnung bewerben.
>
> Ich bin vor Kurzem nach Deutschland gezogen und beginne im Oktober mein Masterstudium an der TU Berlin. Da ich neu in Deutschland bin, habe ich noch keine SCHUFA-Historie. Meine Akte ist also leer, es gibt keine negativen Einträge.
>
> Wenn Sie sich für mich entscheiden, kann ich Ihnen gerne eine Sperrkonto-Bestätigung, meinen Zulassungsbescheid sowie die Bürgschaftserklärung meiner Eltern vorlegen. Einen SCHUFA-BonitätsCheck reiche ich nach, sobald ich angemeldet bin.
>
> Für Rückfragen stehe ich Ihnen jederzeit zur Verfügung.
>
> Mit freundlichen Grüßen
> Priya Sharma

Die Nachricht verspricht nichts und bietet Unterlagen erst für die passende Phase an.

## Was Sie nicht unnötig herausgeben sollten

Datensparsamkeit schützt Sie vor Missbrauch:

- **Keine Ausweiskopie bei der Besichtigung.** Laut DSK darf der Vermieter den Ausweis ansehen, eine Kopie ist nicht zulässig. Einen deutschen Personalausweis dürfen nach § 20 PAuswG nur Sie selbst oder andere mit Ihrer Zustimmung kopieren, und die Kopie muss deutlich als Kopie erkennbar sein. Beim ausländischen Pass empfiehlt sich dieselbe Vorsicht: vorzeigen statt abgeben; falls doch eine Kopie nötig ist, nicht benötigte Felder schwärzen und „Kopie nur für Mietbewerbung [Adresse]“ daraufschreiben.
- **Keine vollständige Kontohistorie.** Ein Kontoauszug mit geschwärzten Einzelposten reicht.
- **Keine SCHUFA-Datenkopie**, sondern den BonitätsCheck.
- **Keine Angaben zu Staatsangehörigkeit, Religion** oder Familienplanung.
- **Keine Kontaktdaten des Vorvermieters**, die darf der Vermieter laut DSK nicht verlangen.
- **Keine unaufgeforderten Unterlagen** in einer frühen Phase; laut DSK soll der Vermieter solche Dokumente nicht verarbeiten. Die Daten abgelehnter Bewerber muss der Vermieter laut DSK löschen; soweit AGG-Ansprüche möglich sind, in der Regel spätestens nach sechs Monaten.

Diskriminierung wegen der ethnischen Herkunft ist bei der Wohnungsvermietung verboten, unabhängig davon, wie viele Wohnungen der Vermieter besitzt (§ 19 Abs. 2 in Verbindung mit § 2 Abs. 1 Nr. 8 AGG). Ansprüche müssen derzeit innerhalb von zwei Monaten geltend gemacht werden (§ 21 Abs. 5 AGG); eine Reform, die die Frist auf vier Monate verlängern soll, ist geplant, aber noch nicht Gesetz. Die [Antidiskriminierungsstelle des Bundes](https://www.antidiskriminierungsstelle.de) bietet eine Erstberatung an und weist darauf hin, dass Anzeigen, die sich nur an „deutschsprachige“ Bewerber richten, eine mittelbare Diskriminierung sein können.

## Mietbetrug erkennen

Wer neu ist und unter Zeitdruck steht, ist ein typisches Ziel für Betrüger. Verbraucherzentrale (Stand 07.08.2026) und Polizei beschreiben immer wieder dieselben Muster.

> ⚠️ **Warnsignale:** Der Eigentümer ist angeblich im Ausland und schickt die Schlüssel per Post, nachdem Sie die Kaution überwiesen haben. Eine Kaution oder „Reservierungsgebühr“ wird vor der Besichtigung verlangt. Sie sollen Geld zahlen, um auf eine Vorauswahlliste zu kommen. Ausweiskopie oder SCHUFA-Auskunft werden sehr früh per E-Mail angefordert. Die Zahlung soll über Airbnb-, booking.com- oder eBay-Links laufen, obwohl diese Plattformen keine Langzeitmieten verwalten. Die IBAN ist ausländisch, der Preis liegt weit unter dem Marktniveau.

Die Polizei NRW warnt zusätzlich vor Identitätsdiebstahl und vor Maschen mit sexueller Ausbeutung; gehen Sie deshalb nicht allein zu Besichtigungen. Diese Regeln helfen:

- Zahlen Sie nie für eine Wohnung, die Sie nicht gesehen haben (Verbraucherzentrale).
- Die Kaution ist erst zum vereinbarten Mietbeginn nach Vertragsunterschrift fällig, auf Wunsch in drei Raten (§ 551 BGB).
- Eine Überweisung können Sie in der Regel nicht selbst zurückholen; Ihre Bank kann einen Rückruf versuchen, einen Anspruch darauf haben Sie nicht. Eine SEPA-Lastschrift können Sie dagegen innerhalb von acht Wochen zurückgeben.
- Hat der Vermieter den Makler beauftragt, zahlen Sie keine Provision; Vorschüsse an Makler sind unzulässig (§ 2 WoVermRG). Zahlungen an den Vormieter nur für den Auszug sind unwirksam (§ 4a WoVermRG).
- Melden Sie Betrugsversuche der Polizei.

Mehr dazu in unserer FAQ [Wie schütze ich mich bei der Wohnungssuche vor Betrug?](/de/faq/yurt/almanyada-kiralik-ev-ararken-dolandiriciliklardan-nasil-korunabilirim-de).

## Checkliste: Ihre Bewerbungsmappe ohne SCHUFA

**Immer**

- Kurzes Anschreiben: neu in Deutschland, leere SCHUFA-Akte, keine Negativeinträge
- Ausweis oder Pass und Aufenthaltstitel zum Vorzeigen (keine Kopie abgeben)
- Ausgefüllte Mieterselbstauskunft, nur mit zulässigen Angaben

**Studierende**

- Zulassungsbescheid oder Immatrikulationsbescheinigung
- Sperrkonto-Bestätigung oder Stipendienbescheid
- Ggf. Nachweis über den gezahlten Semesterbeitrag (für das Wohnheim)

**Beschäftigte**

- Arbeitsvertrag
- Gehaltsabrechnungen, sobald vorhanden
- Optional: Bestätigung des Arbeitgebers

**Mit Bürgschaft**

- Bürgschaftserklärung (freiwillig angeboten)
- Einkommensnachweis des Bürgen
- Ausweis des Bürgen zum Vorzeigen

**Erst in Phase C (der Vermieter hat sich für Sie entschieden)**

- SCHUFA-BonitätsCheck, sobald Sie gemeldet sind
- Geschwärzte Kontoauszüge oder Gehaltsnachweise
- Kopien deutlich als Kopie für diese Bewerbung kennzeichnen

## Häufige Fragen

### Kann mir ein Vermieter die Wohnung verweigern, nur weil ich keine SCHUFA habe?

Ja, das kann er. Vermieter dürfen ihre Mieter frei auswählen, und kein Gesetz verpflichtet sie, Alternativen zur SCHUFA zu akzeptieren. Verboten ist aber eine Ablehnung wegen Ihrer ethnischen Herkunft (AGG). In der Praxis lohnt es sich, von Anfang an zu erklären, dass Ihre Akte leer und nicht negativ ist, und starke Ersatznachweise wie Arbeitsvertrag, Sperrkonto-Bestätigung oder eine Bürgschaft anzubieten.

### Wie lange dauert es, bis ich einen SCHUFA-Score habe?

Das hängt vor allem von Ihrem ersten Bankvertrag ab. Ohne Daten, oder wenn Sie weniger als sechs Monate bekannt sind und keinen Bankvertrag haben, gibt es laut SCHUFA keinen Score. Sobald Sie ein Girokonto, eine Kreditkarte oder einen anderen Bankvertrag haben, wird ein vollständiger Score berechnet. Wie Sie ihn danach verbessern, lesen Sie in der FAQ [Wie verbessere ich meinen SCHUFA-Score?](/de/faq/para/almanyada-schufa-puanimi-nasil-yukseltebilirim-de).

### Baut pünktliche Mietzahlung meine SCHUFA auf?

Nein, nicht direkt. Pünktlich gezahlte Miete wird normalerweise nicht an die SCHUFA gemeldet. Umgekehrt kann ein Vermieter verspätete Miete auch nicht einfach „melden“. Ein Negativeintrag setzt ein formales Verfahren voraus: mindestens zwei schriftliche Mahnungen, die erste mindestens vier Wochen vorher, eine unbestrittene Forderung und einen Hinweis auf die mögliche Meldung, etwa über ein Inkassounternehmen, das SCHUFA-Vertragspartner ist.

### Darf der Vermieter nach meiner Staatsangehörigkeit fragen?

Laut Orientierungshilfe der Datenschutzkonferenz (DSK, Januar 2026) ist die Frage nach der Staatsangehörigkeit für die Mieterauswahl nicht erforderlich und daher nicht zulässig. Den Ausweis darf der Vermieter bei der Besichtigung ansehen, eine Kopie braucht er nicht. Wenn Sie den Eindruck haben, wegen Ihrer Herkunft abgelehnt worden zu sein, können Sie sich an die Antidiskriminierungsstelle des Bundes wenden; beachten Sie die derzeit geltende Frist von zwei Monaten.

### Was kostet ein BonitätsCheck, und brauche ich ihn sofort?

Der BonitätsCheck kostet rund 30 €. Sie brauchen ihn nicht bei der Besichtigung, sondern erst, wenn der Vermieter sich für Sie entschieden hat (Phase C). Als Neuankömmling zeigt er meist, dass keine Negativeinträge vorliegen. Die kostenlose Datenkopie ist dagegen nur für Sie selbst gedacht und sollte nicht an Vermieter gehen.

### Wie hoch darf die Kaution sein, wenn ich keine SCHUFA habe?

Auch ohne SCHUFA gilt die gesetzliche Obergrenze: höchstens drei Nettokaltmieten (§ 551 BGB), zahlbar in drei Monatsraten ab Mietbeginn. Ein Vermieter kann diese Grenze nicht umgehen, indem er zusätzlich zur vollen Kaution eine Elternbürgschaft verlangt; die Rechtsprechung behandelt solche Kombinationen grundsätzlich als unzulässig (Ausnahme: eine von sich aus, freiwillig angebotene Bürgschaft). Weitere Antworten zum Mietprozess finden Sie in der FAQ [Wie läuft die Wohnungsmiete als ausländischer Studierender ab?](/de/faq/yurt/almanyada-yabanci-bir-ogrenci-olarak-ev-veya-daire-kiralama-sureci-nasil-isler-de).

## Quellen

1. Orientierungshilfe zur Einholung von Selbstauskünften bei Mietinteressent:innen, Version 2.0 — Datenschutzkonferenz (DSK) (Januar 2026) — https://www.datenschutzkonferenz-online.de/media/oh/OH_Einholung_von_Selbstauskuenften_Mietinteressenten_V2.pdf
2. Musterfragebogen zur Orientierungshilfe — Datenschutzkonferenz (DSK) (Januar 2026) — https://www.datenschutzkonferenz-online.de/media/oh/OH_Einholung_von_Selbstauskuenften_Mietinteressenten_V2_Anhang.pdf
3. § 551 BGB Begrenzung und Anlage von Mietsicherheiten — Bundesministerium der Justiz — https://www.gesetze-im-internet.de/bgb/__551.html
4. § 540 BGB Gebrauchsüberlassung an Dritte — Bundesministerium der Justiz — https://www.gesetze-im-internet.de/bgb/__540.html
5. § 19 BMG Mitwirkung des Wohnungsgebers — Bundesministerium der Justiz — https://www.gesetze-im-internet.de/bmg/__19.html
6. § 2 und § 4a WoVermRG — Bundesministerium der Justiz — https://www.gesetze-im-internet.de/wovermrg/__2.html ; https://www.gesetze-im-internet.de/wovermrg/__4a.html
7. § 19 AGG — Bundesministerium der Justiz — https://www.gesetze-im-internet.de/agg/__19.html
8. § 20 PAuswG — Bundesministerium der Justiz — https://www.gesetze-im-internet.de/pauswg/__20.html
9. Pressemitteilung 199/2009 zu VIII ZR 238/08 — Bundesgerichtshof (30.09.2009) — https://www.bundesgerichtshof.de/SharedDocs/Pressemitteilungen/DE/2009/2009199.html
10. Urteil VIII ZR 243/03 — Bundesgerichtshof (30.06.2004)
11. SCHUFA-Auskunft für Vermieter — SCHUFA Holding AG — https://www.schufa.de/newsroom/bonitaet/schufa-auskunft-vermieter-schnell-einfach/
12. Datenkopie und BonitätsCheck — SCHUFA Holding AG — https://www.schufa.de/themenportal/datenkopie-bonitaetscheck/index.jsp
13. Der neue SCHUFA-Score — SCHUFA Holding AG (17.03.2026) — https://www.schufa.de/en/newsroom/press-release/der-neue-schufa-score-transparent-und-leicht-nachzurechnen/index.jsp
14. Fehlende Daten: Personen erhalten keinen SCHUFA-Score — SCHUFA Holding AG — https://www.schufa.de/en/newsroom/creditworthiness/fehlende-daten-personen-erhalten-keinen-schufa-score/index.jsp
15. Daten bei der SCHUFA — SCHUFA Holding AG — https://www.schufa.de/ueber-uns/daten-scoring/daten-schufa/
16. Fake-Wohnungen im Internet — Verbraucherzentrale (Stand 07.08.2026) — https://www.verbraucherzentrale.de/wissen/vertraege-reklamation/abzocke/fakewohnungen-im-internet-so-erkennen-sie-falsche-immobilienanzeigen-27576
17. Infoblatt Betrug bei der Wohnungssuche — Polizei Baden-Württemberg (04/2025) — https://praevention.polizei-bw.de/wp-content/uploads/sites/20/2025/04/INFOBLATT-Betrug-Wohnungssuche.pdf
18. Betrug bei Wohnungsangeboten — Polizei NRW (2026) — https://polizei.nrw/artikel/betrug-bei-wohnungsangeboten
19. Wie bewerbe ich mich um einen Wohnplatz? — studierendenWERK BERLIN — https://www.stw.berlin/wohnen/faq-wohnen/wie-bewerbe-ich-mich-um-einen-wohnplatz.html
20. Application — Studierendenwerk München Oberbayern — https://www.studierendenwerk-muenchen-oberbayern.de/en/accommodation/application/
21. Antidiskriminierungsstelle des Bundes — https://www.antidiskriminierungsstelle.de

Die Informationen wurden im September 2026 anhand der oben genannten Quellen geprüft. Dieser Artikel ist eine allgemeine Information und keine Rechtsberatung.

*Stand: September 2026. Gesetze, SCHUFA-Verfahren und die Praxis der Studierendenwerke können sich ändern; prüfen Sie wichtige Punkte vor Ihrer Entscheidung bei der jeweiligen Stelle.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'renting-a-flat-in-germany-without-schufa',
                'title' => 'Almanya\'da SCHUFA Olmadan Ev Nasıl Bulunur?',
                'excerpt' => 'SCHUFA kaydınız yoksa da Almanya\'da ev kiralayabilirsiniz. Hangi belgelerin SCHUFA\'nın yerini tuttuğunu, ev sahibinin neyi ne zaman isteyebileceğini ve kefaletin sınırlarını öğrenin.',
                'meta_title' => 'SCHUFA Olmadan Almanya\'da Ev Kiralama (2026 Rehberi)',
                'meta_description' => 'Almanya\'ya yeni geldiniz, SCHUFA kaydınız yok mu? SCHUFA yerine geçen belgeler, kefalet kuralları, örnek mesaj ve dolandırıcılık uyarılarıyla ev bulun.',
                'body' => $trBody,
            ],
            'en' => [
                'slug' => 'rent-an-apartment-in-germany-without-schufa-en',
                'title' => 'How to Rent an Apartment in Germany Without SCHUFA',
                'excerpt' => 'New in Germany and no SCHUFA record? An empty file isn\'t a bad one. Here\'s what landlords may ask for and when, which documents can replace SCHUFA, how guarantors work, and how to avoid rental scams.',
                'meta_title' => 'Rent an Apartment in Germany Without SCHUFA (2026)',
                'meta_description' => 'No SCHUFA history yet? Learn which documents replace it, what landlords may ask at each stage, guarantor rules, a sample message and scam warnings.',
                'body' => $enBody,
            ],
            'de' => [
                'slug' => 'apartment-search-in-germany-without-schufa-history-de',
                'title' => 'Wohnung ohne SCHUFA finden: So klappt es als Neuankömmling',
                'excerpt' => 'Keine SCHUFA-Historie ist kein Negativeintrag. So stellen Sie als Neuankömmling eine überzeugende Bewerbungsmappe zusammen: Ersatznachweise, DSK-Phasen, Bürgschaftsregeln, Musteranschreiben und Checkliste.',
                'meta_title' => 'Wohnung ohne SCHUFA mieten: Tipps für Neuankömmlinge',
                'meta_description' => 'Neu in Deutschland und noch keine SCHUFA? So mieten Sie trotzdem: welche Unterlagen helfen, was Vermieter laut DSK wann fragen dürfen und wie Bürgschaft geht.',
                'body' => $deBody,
            ],
        ];

        foreach ($variants as $locale => $v) {
            // content_html + reading_minutes: Post::booted() content_md'den üretir (MarkdownRenderer + BlogAutoLinker).
            $payload = [
                'locale' => $locale, 'translation_group_id' => $groupId, 'user_id' => $userId, 'category_id' => $categoryId,
                'title' => $v['title'], 'excerpt' => Str::limit($v['excerpt'], 250, '…'),
                'content_md' => $v['body'],
                'meta_title' => $v['meta_title'], 'meta_description' => Str::limit($v['meta_description'], 158, '…'),
                'is_published' => true,
            ];
            // Tekrar koşarsa yayın tarihi korunur.
            $existing = Post::where('slug', $v['slug'])->first();
            $existing ? $existing->update($payload) : Post::create($payload + ['slug' => $v['slug'], 'published_at' => now()]);
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'renting-a-flat-in-germany-without-schufa',
            'rent-an-apartment-in-germany-without-schufa-en',
            'apartment-search-in-germany-without-schufa-history-de',
        ])->delete();
    }
};

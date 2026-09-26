<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+EN+DE): Almanya'da Bachelor/Master tez süreci — genel çerçeve + 10 kurumdan gerçek örnek.
 *
 * Ana mesaj: Almanya'da tek bir tez süreci yok; bağlayıcı olan öğrencinin kendi Prüfungsordnung'u,
 * fakülte kuralları ve danışman / Prüfungsamt talimatları.
 *
 * Doğrulanmış kaynaklar (26.09.2026):
 *   - KMK Musterrechtsverordnung 21.11.2024 (§ 4(3) tez zorunlu; § 8(3) 6–12 / 15–30 ECTS).
 *   - DFG Kodex v1.2 (2024) + DFG GenAI açıklaması (2023); § 63(5) HG NRW, § 156 StGB (eyalete bağlı).
 *   - 10 kurum örneği (program adıyla): TUM, LMU, RWTH, TU Berlin, KIT (Informatik); Uni Hamburg WiSo,
 *     Goethe Frankfurt WiWi, Uni Köln WiSo, TH Köln, FU Berlin BWL. TU Berlin yalnız Prüfungsamt'ın
 *     resmî 03/2026 notuna dayanır (resmî olmayan AllgStuPO okunur sürümünden § verilmedi).
 *   - Şirket tezi: HS Emden/Leer (NDA), Uni Potsdam (140 gün — federal kural değil, Ausländerbehörde
 *     teyidi öneriliyor); § 20 AufenthG / BAMF.
 * [n] kaynak işaretleri, mevcut HeadingPermalink'in Kaynaklar/Sources/Quellen başlığına ürettiği id'ye
 * bağlanır; yeni bir citation sistemi yok.
 * Eski 120/240 kuralını içeren sayfalara bilinçli olarak link verilmedi.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '3300c665-4eb7-4eb1-b5e5-d20872b62056';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'yasam')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Kısa cevap: Almanya'da tek tip bir tez süreci yoktur. Bachelor ya da Master tezinizin ne zaman başlayacağını, kaç hafta süreceğini, nasıl teslim edileceğini ve nasıl notlanacağını kendi sınav yönetmeliğiniz (Prüfungsordnung), fakültenizin kuralları ve danışmanınızla sınav ofisinin (Prüfungsamt) talimatları belirler. Bu rehberde Almanya'da tez süreci için genel çerçeveyi, 10 üniversite/fakülteden gerçek örnekleri ve adım adım uygulanabilir bir süreci Türkiye'den gelen öğrenciler için anlatıyoruz.

> **Son güncelleme:** Eylül 2026 · Tez kuralları üniversiteye, fakülteye ve sınav yönetmeliğine göre değişir. · **⚖️ Resmî kural** = yönetmelik veya resmî düzenleme, **💡 Pratik öneri** = deneyime dayalı tavsiye · Örnekler Eylül 2026'da 10 Alman üniversitesi/fakültesinde kontrol edildi; bu evrensel bir kural değildir.

## Bachelorarbeit ve Masterarbeit nedir

Bachelorarbeit nedir sorusunun kısa cevabı "lisans bitirme tezi", Masterarbeit ise "yüksek lisans tezi"dir; ikisi de diplomanın zorunlu parçasıdır.

**⚖️ Resmî kural:** Eyaletlerin ortak çerçevesini belirleyen KMK Model Yönetmeliği'ne (Musterrechtsverordnung, 21 Kasım 2024) göre her Bachelor ve Master programı bir tez içerir. Bu tez, öğrencinin belirli bir süre içinde, kendi alanındaki bir problemi bilimsel yöntemlerle bağımsız olarak işleyebildiğini göstermelidir (§ 4(3)). Kapsam da aynı yönetmelikte çerçevelenir: Bachelor tezi 6–12 ECTS, Master tezi 15–30 ECTS (güzel sanatlar için istisnalar vardır, § 8(3)) [\[1\]](#content-kaynaklar).

Tam kredi ve süreyi programınız belirler.

### Mini sözlük: tezle ilgili Almanca terimler

- **Bachelorarbeit:** Lisans bitirme tezi.
- **Masterarbeit:** Yüksek lisans tezi.
- **Betreuer / Betreuerin:** Tez danışmanı.
- **Prüfer / Erstprüfer / Zweitprüfer:** Değerlendirici; birinci ve ikinci değerlendirici.
- **Prüfungsamt:** Sınav ofisi (idari birim).
- **Prüfungsausschuss:** Sınav kurulu.
- **Bearbeitungszeit:** Resmî çalışma süresi.
- **Abgabe:** Teslim.
- **Kolloquium / Disputation:** Tez savunması veya tez sunumu.
- **Sperrvermerk:** Gizlilik şerhi (özellikle şirket tezlerinde).
- **Eidesstattliche Versicherung / Eigenständigkeitserklärung:** Yeminli beyan / bağımsız çalışma beyanı.
- **Exposé:** Tez önerisi (soru, yöntem, zaman planı).

## İlk okumanız gereken belge: Prüfungsordnung

Almanya'da tezle ilgili neredeyse her sorunun cevabı tek bir belgededir: programınızın sınav yönetmeliği, yani Prüfungsordnung.

**⚖️ Resmî kural:** Tezle ilgili tek bir federal kural yoktur. Kurallar katman katman oluşur [\[1\]](#content-kaynaklar):

1. Eyalet yükseköğretim yasası (Landeshochschulgesetz),
2. KMK model yönetmeliğine dayanan eyalet akreditasyon yönetmeliği,
3. Üniversitenin genel sınav yönetmeliği (ör. TUM'da APSO, RWTH'de Übergreifende Prüfungsordnung, FU Berlin'de RSPO),
4. Programınızın sınav yönetmeliği (ör. Fachprüfungsordnung).

Genel yönetmelik çerçeveyi çizer, program yönetmeliği ayrıntıyı doldurur; örneğin TH Köln'ün 2023 tarihli çerçeve yönetmeliği (Rahmenprüfungsordnung) bir şablondur; kredi ve süre gibi değerleri her program kendisi belirler [\[15\]](#content-kaynaklar).

**💡 Pratik öneri:** Yönetmeliği genellikle "Amtliche Bekanntmachungen" (resmî duyurular) sayfasında bulursunuz; en güncel sürümü ("in der Fassung vom…") indirin ve PDF'te "Abschlussarbeit", "Bachelorarbeit" veya "Masterarbeit" kelimesini aratın. Prüfungsamt'ın "Merkblatt" veya "Hinweise" adlı bilgi notlarını da okuyun.

## Teze ne zaman başlayabilirsiniz

Teze başlamak için genellikle belirli bir kredi veya bazı zorunlu dersler şarttır. İncelediğimiz örneklerde bu şart, RWTH Informatik (B.Sc.) ve TU Berlin Informatik (B.Sc.) için 120 kredi, Köln WiSo B.Sc. BWL için 100 LP, FU Berlin BWL için 90 LP; LMU Informatik'te (B.Sc.) ise P 16.1 modülüdür. RWTH'de her Master tezi için ayrıca "Wissenschaftliche Integrität" (bilimsel dürüstlük) modülü gerekir [\[9\]](#content-kaynaklar). Birçok programda tez son sınav olarak planlanır; örneğin TUM M.Sc. Informatik'te tez normalde son sınavdır [\[7\]](#content-kaynaklar).

**💡 Pratik öneri:** Kredi şartını kayıttan birkaç hafta önce transkriptinizden kontrol edin. Notu sisteme girilmemiş tek bir sınav, kaydınızı geciktirebilir. Türkiye'den veya Erasmus'tan gelen derslerin tanınması (Anerkennung) sürüyorsa bunu önceden bitirin.

## Tez konusu nasıl bulunur

Almanya'da konu bulmanın üç yaygın yolu vardır:

1. **Kendi önerinizle gitmek:** Bir dersten veya stajdan çıkan bir soruyu kısa bir öneriyle bir profesöre götürürsünüz.
2. **Konu ilanlarından seçmek:** Kürsüler (Lehrstuhl) ve araştırma grupları açık konuları web sayfalarında listeler; TUM'daki "Themenbörse" (konu borsası) gibi merkezi platformlar da vardır.
3. **Şirkette konu:** Çalıştığınız şirketin somut bir problemi teze dönüşebilir (aşağıda ayrıca ele alıyoruz).

Araştırma grubunda yazılan bir Master tezi, doktoraya da kapı açabilir; bunun için [Almanya'da doktora rehberimize](/tr/blog/doing-a-phd-and-research-career-in-germany-as-a-foreigner) bakın.

**⚖️ Resmî kural (örnekler):** İncelediğimiz kurumların çoğunda konu, erken bir süre içinde bir kez iade edilebiliyor. Ama bu hak her programda yok; örneğin Köln WiSo örneğinde böyle serbest bir iade hakkını doğrulayamadık. Örnekler: LMU Informatik'te ilk 2 hafta içinde [\[8\]](#content-kaynaklar), KIT Informatik'te ilk ay içinde [\[11\]](#content-kaynaklar), TH Köln'de 2 hafta içinde gerekçesiz [\[15\]](#content-kaynaklar).

## Tez danışmanı nasıl bulunur

Tez danışmanı nasıl bulunur sorusunun cevabı çoğu zaman "doğru zamanda, hazırlıklı ve kısa bir e-postayla"dır.

**💡 Pratik öneri:**

- Kürsü sayfasında "Abschlussarbeiten" veya "Theses" başlığına bakın; başvuru şekli çoğu zaman orada yazar.
- Dersini aldığınız ve iyi not aldığınız hocalardan başlayın.
- Kayıttan 2–3 ay önce iletişime geçin; popüler kürsülerde yerler erken dolabilir.
- Günlük danışmanlığı çoğu zaman bir doktora öğrencisi (wissenschaftliche Mitarbeiter) yapar; resmî değerlendirici profesördür.
- Akademik CV ve not dökümü ekleyin; hazır yapı için [akademik CV şablonumuzu](/tr/templates/lebenslauf-akademisch) kullanabilirsiniz.

**Örnek e-posta:** Hocalara Türkçe değil, Almanca veya İngilizce yazın. İngilizce bir örnek:

> Subject: Master thesis inquiry – [topic area] – Summer semester 2027
>
> Dear Professor [Name],
>
> I am a Master's student in [programme] and took your course [course name]. I would like to write my Master thesis in the area of [topic], with this question: [one sentence]. I plan to register the thesis in [month/year]. Would there be an opportunity to write a thesis on this or a similar topic at your chair? I would be glad to discuss it briefly. My CV and transcript are attached.
>
> Kind regards,
> [Name], Matrikelnummer [student ID]

Türkçesi özetle: "Programınızı ve aldığınız dersi belirtin, konu alanınızı ve tek cümlelik sorunuzu yazın, planladığınız kayıt tarihini söyleyin, kısa bir görüşme isteyin, CV ve not dökümünü ekleyin."

Cevap gelmezse bir hafta sonra kısa bir hatırlatma yazmak normaldir.

## Betreuer, Prüfer ve Prüfungsamt: kim ne yapar

Türkiye'deki "danışman + jüri" yapısına benzer, ama roller daha resmî ayrılır:

- **Betreuer (danışman):** Konuyu birlikte belirler, yönlendirir, geri bildirim verir; çoğu zaman birinci değerlendiricidir (Erstprüfer).
- **Prüfer (değerlendirici):** Tezi notlar.
- **Prüfungsamt (sınav ofisi):** Kaydı alır, teslim tarihini hesaplar, dilekçeleri işler, notu sisteme girer.
- **Prüfungsausschuss (sınav kurulu):** İstisnalara, uzatmalara ve itirazlara karar verir.

**⚖️ Resmî kural (örnekler):** TUM'da tezi konuyu veren kişi notluyor [\[7\]](#content-kaynaklar), LMU Informatik'te danışman [\[8\]](#content-kaynaklar); iki örnekte de ikinci değerlendirici yalnızca tez başarısız sayılacaksa devreye giriyor. İncelediğimiz diğer sekiz örnekte iki değerlendirici var.

## Tez kaydı nasıl yapılır

Tez, resmî olarak "kaydedilmeden" (Anmeldung) başlamaz. Kayıt yöntemi kurumdan kuruma değişir:

- **Online portal:** Örneğin TUM'da ilgili okulun portalı üzerinden [\[7\]](#content-kaynaklar).
- **Form + imza:** Birçok fakültede danışmanın imzaladığı bir form Prüfungsamt'a verilir.
- **Ek onaylar:** Şirkette yazılan tezlerde kurul veya kürsü onayı gerekebilir (aşağıya bakın).

**💡 Pratik öneri:** Konu başlığı resmî kayda geçer; kayıttan önce danışmanınızla son hâline getirin.

## Bearbeitungszeit ne zaman başlar

**⚖️ Resmî kural:** İncelediğimiz kurumlarda resmî çalışma süresi (Bearbeitungszeit), konunun resmî olarak verildiği veya kaydedildiği tarihten (Ausgabedatum) itibaren işler.

Süreler çok farklıdır: Uni Hamburg WiSo'da Bachelor için 9 hafta [\[12\]](#content-kaynaklar), TU Berlin'de M.Sc. Computer Science için 26 hafta [\[10\]](#content-kaynaklar), TUM'da Master için 6 ay [\[7\]](#content-kaynaklar).

Kayıttan önce çalışmaya gelince: İncelediğimiz kurumlar arasında yalnızca Goethe-Universität Frankfurt WiWi, konu resmî olarak kayda geçmeden tez üzerinde çalışmayı açıkça yasaklıyor [\[13\]](#content-kaynaklar). Diğer örneklerde bu konu düzenlenmemiş; ama resmî süre her durumda kayıt tarihinden işliyor.

**💡 Pratik öneri:** Kayıttan önce ne kadar ön çalışma (literatür, Exposé) yapabileceğinizi danışmanınızla netleştirin.

## Danışmanınızla çalışmak

Danışmanlık ilişkisi genellikle öğrencinin inisiyatifine dayanır: Toplantı istemek ve ilerlemeyi göstermek sizin işinizdir.

**💡 Pratik öneri:**

- **Exposé ile başlayın:** Araştırma sorusu, yöntem, kaynaklar ve haftalık plan içeren 2–5 sayfalık bir metin, beklentileri baştan netleştirir.
- **Toplantı ritmi belirleyin:** Örneğin iki ya da üç haftada bir kısa görüşme. Her toplantıdan sonra kararları kısa bir e-postayla özetleyin.
- **Taslak paylaşın:** Tüm tezi sona bırakmayın; bir bölümü erkenden gösterip üslup ve derinlik konusunda geri bildirim alın.

**⚖️ Resmî kural (örnek):** Universität zu Köln WiSo'da 1 Ekim 2025'ten itibaren başlayan tezler için ilerleme belgelemesi (Fortschrittsdokumentation) zorunludur [\[14\]](#content-kaynaklar).

Tez dönemi yalnızlaştırıcı olabilir; zorlanırsanız [yalnızlık ve ruh sağlığı yazımıza](/tr/blog/loneliness-and-mental-health-as-an-international-student-in-germany) göz atın.

## Kaynak kullanımı, atıf ve intihal

**⚖️ Resmî kural:** DFG (Alman Araştırma Kurumu) kodeksine göre asıl kaynaklara atıf yapılmalıdır (Yönerge 7); intihal ve veri uydurma bilimsel suistimaldir (Yönerge 19); yazarlık gerçek katkı gerektirir (Yönerge 14) [\[2\]](#content-kaynaklar).

Teslimde bir beyan imzalarsınız; adı kuruma göre değişir:

- **Eidesstattliche Versicherung / Versicherung an Eides statt** (yeminli beyan): ör. Uni Hamburg WiSo, Uni Köln WiSo, RWTH.
- **Eigenständigkeitserklärung / Selbstständigkeitserklärung** (bağımsız çalışma beyanı): ör. Goethe Frankfurt, TU Berlin, FU Berlin.

Bu beyanın hukuki ağırlığı eyalete göre değişir. Örneğin NRW'de üniversiteler yeminli beyan alabilir; gerçeğe aykırı bir beyan § 156 StGB (Ceza Kanunu) kapsamına girebilir. NRW ayrıca kasıtlı kopya için 50.000 euroya kadar para cezası ve tekrarlanan veya ağır durumlarda kayıt silmeyi öngörür [\[4\]](#content-kaynaklar)[5].

**⚖️ Resmî kural (tipik sonuçlar):** İntihal veya kopya tespit edilirse tez genellikle "nicht bestanden" (başarısız, 5,0) sayılır. Ağır veya tekrarlanan durumlarda kayıt silinmesi (Exmatrikulation) mümkündür. Uni Hamburg'un 2025 tarihli rehberine göre başkasına tez yazdırmak (ghostwriting) genellikle özellikle ağır bir aldatmadır; izinsiz YZ kullanımı ise tek başına otomatik olarak özellikle ağır bir vaka sayılmaz [\[17\]](#content-kaynaklar). Kopya sonradan ortaya çıkarsa derece geri alınabilir (süre sınırlarını yönetmelik belirler).

## Tezde yapay zekâ ve ChatGPT

"ChatGPT kullanabilir miyim?" Dürüst cevap: Almanya genelinde ne tamamen yasak ne de serbest; karar yönetmeliğinize ve değerlendiricinize bağlıdır.

> **Your Prüfungsordnung, faculty guidance and supervisor instructions prevail.**
> Türkçesi: Sınav yönetmeliğiniz, fakültenizin yönergeleri ve danışmanınızın talimatları her zaman önceliklidir.

İncelediğimiz kurumlardaki ortak eğilim, kullanılan YZ araçlarının şeffaf biçimde beyan edilmesi ve içeriğin tüm sorumluluğunun öğrencide olmasıdır. Ancak bağlayıcı kural üniversiteye, fakülteye, Prüfungsordnung'a ve danışmana göre değişir. DFG'nin 2023 tarihli açıklaması da (öğrenci sınavları için değil, araştırmacılar için yazılmıştır) YZ kullanımının yazarı sorumluluktan kurtarmadığını, hangi modelin hangi amaçla kullanıldığının belirtilmesi gerektiğini ve yalnızca gerçek kişilerin yazar olabileceğini vurgular [\[3\]](#content-kaynaklar).

**⚖️ Resmî kural (örnekler):**

- **TU Berlin:** Bağımsızlık beyanı; ürün adı, üretici, sürüm ve kullanım türünü soruyor. Prompt ve sohbetlerin belgelenmesi öneriliyor [\[10\]](#content-kaynaklar).
- **Goethe Frankfurt WiWi:** Beyan YZ'yi açıkça kapsıyor. Kendi katkınız ağır basmalı, YZ destekli kısımlar işaretlenmeli, fikir üretiminde kullanım da beyan edilmeli; araç, sürüm, kullanım ve bölümleri gösteren bir YZ dizini isteniyor. Yazım denetleyicisi, kaynak yönetim ve istatistik yazılımlarının belirtilmesi gerekmiyor [\[13\]](#content-kaynaklar).
- **KIT Informatik:** Yazım, dilbilgisi ve çeviri araçları beyan gerektirmiyor; diğer üretken YZ kullanımları (amaç, bölümler, modeller) beyan edilmeli ve danışmanla önceden konuşulmalı [\[11\]](#content-kaynaklar).
- **RWTH:** Tek tip bir işaretleme kuralı yok; ama yeminli beyan "dil, metin ve medya üretimi için yazılım ve hizmetleri" açıkça kapsıyor [\[9\]](#content-kaynaklar).
- **TH Köln:** Beyan "ilke olarak evet"; metnin tamamını veya önemli kısımlarını ürettirmek yasak; ayrıntıyı hocalarınızla netleştirin [\[15\]](#content-kaynaklar)[20].
- **Uni Hamburg:** Çerçeve belge, gözetimsiz çalışmalarda üretken YZ'ye ilke olarak izin veriyor; ama önemli YZ kısımları işaretsiz bırakılamaz ve araçlar listelenmelidir [\[12\]](#content-kaynaklar).

Dil düzeltme birçok yerde üretilmiş içerikten farklı değerlendiriliyor (KIT'te beyansız, Leipzig'de genellikle sorunsuz [\[21\]](#content-kaynaklar)); ama bu da değişir. Beyan edilmeden ya da izinsiz kullanılan YZ, kopya (Täuschung) sayılabilir. Tespit yazılımları ise yalnızca destekleyici kanıttır; Goethe Frankfurt'ta şüphe bir dinlemeye veya sözlü kontrole yol açabiliyor.

**💡 Pratik öneri:**

- Hangi aracı, hangi sürümle, hangi bölüm için ve ne amaçla kullandığınızı gösteren bir kayıt (log) tutun; önemli prompt'ları saklayın.
- YZ'nin önerdiği her kaynağı tek tek kontrol edin. Uni Leipzig, var olmayan kaynaklar ve gizli intihal riskine dikkat çekiyor [\[21\]](#content-kaynaklar).
- Kişisel veri, şirket verisi, gizli veya telifli içerikleri herkese açık YZ araçlarına girmeyin.
- Tezin başında danışmanınıza e-postayla sorun: "Hangi araçlar serbest, nasıl beyan etmeliyim?" Cevabı saklayın.

## Şirkette tez yazmak

Almanya'da şirketle tez yazmak iş hayatına güçlü bir giriş olabilir, ama akademik süreç üniversitede kalır.

**⚖️ Resmî kural (örnekler):** Akademik yetki üniversitededir. Konuyu bir üniversite değerlendiricisi belirler veya onaylar ve notu o verir; şirketteki danışman fikir verebilir, öneride bulunabilir ama not vermez (Goethe Frankfurt, LMU Informatik, TUM, RWTH) [\[7\]](#content-kaynaklar)[8][9][13]. TUM'da şirket tezi yalnızca bir TUM değerlendiricisiyle mümkündür; RWTH'de dış tez ancak istisnai olarak ve RWTH değerlendiricisinin gözetiminde yazılabilir. Frankfurt, TH Köln ve KIT'te kurul veya kürsü onayı gerekir; FU Berlin'de ise program yönetmeliği izin veriyorsa mümkündür.

**Ücret ve sosyal sigorta:** Yalnızca tezini şirkette yazan öğrenci genellikle çalışan sayılmaz (Federal Sosyal Mahkeme 1993 kararı, sağlık sigortası SBK aktarımıyla). Tezin yanında şirket için üretken iş de yapıyorsanız normal istihdam kuralları geçerlidir. Ayrı bir iş sözleşmesi için [Werkstudent rehberimize](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market) ve [HiWi ile Werkstudent karşılaştırmamıza](/tr/blog/hiwi-vs-werkstudent-student-jobs-in-germany) bakabilirsiniz.

**140 gün kuralı (AB dışı öğrenciler):** Bu konuda federal düzeyde genel bir kural bulamadık. Bazı üniversitelerin uluslararası ofisleri (ör. Uni Potsdam), şirkette yazılan tezin öğrenimin parçası sayıldığını ve 140 günlük çalışma hakkından düşülmediğini belirtiyor [\[19\]](#content-kaynaklar). Tez dışında ek üretken iş yaparsanız bu istihdamdır. **💡 Pratik öneri:** Kendi durumunuzu mutlaka yabancılar dairenize (Ausländerbehörde) yazılı olarak teyit ettirin. Ayrıca [tez yazarken çalışma SSS'imize](/tr/faq/is/almanyada-lisans-tezi-yazarken-tam-zamanli-calismak-mumkun-mudur) bakın.

## NDA, Sperrvermerk ve fikrî mülkiyet

Şirketler çoğu zaman gizlilik sözleşmesi (NDA) ister. **⚖️ Resmî kural (örnekler):** NDA, öğrenci ile şirket arasında özel bir sözleşmedir ve değerlendiricilerin (itiraz süreçleri dâhil) tezi okuyup doğrulamasını engelleyemez; profesörler zaten sır saklama yükümlülüğü altındadır (§ 203 StGB) [\[18\]](#content-kaynaklar). Köln WiSo'da NDA, konu belirlenmeden önce konuyu verene bildirilmelidir; Sperrvermerk (gizlilik şerhi) varsa yayın için onay gerekir [\[14\]](#content-kaynaklar).

Sperrvermerk kuralları programa göre değişir. Köln WiSo gibi yerlerde koşullarla mümkünken, TH Köln'de bir fakülte şerhin her nüshada işaretlenmesini istiyor. TU Berlin Informatik/Computer Science ise Sperrvermerk'e ve olağan gizliliğin ötesinde bir gizlilik yükümlülüğüne izin vermiyor [\[10\]](#content-kaynaklar). LMU Informatik'te şirketler, değerlendiriciler tezi aldığı sürece gizlilik isteyebiliyor [\[8\]](#content-kaynaklar).

**Telif hakkı ve buluşlar:** Tezin yazarı sizsinizdir; şirket kullanım haklarını ancak sözleşmeyle elde eder. Üniversitede istihdam edilmeyen öğrenciler "serbest mucit" sayılır [\[22\]](#content-kaynaklar). Şirkette ayrıca çalışıyorsanız (ör. Werkstudent) çalışan buluşları hukuku devreye girebilir; sözleşmenizi kontrol edin.

**💡 Pratik öneri:** NDA'yı imzalamadan önce danışmanınıza gösterin. Şirket verilerini asla herkese açık YZ araçlarına yüklemeyin.

## Tez dili

**⚖️ Resmî kural (örnekler):** Almanca veya İngilizce yaygındır. Başka diller genellikle yalnızca onayla ve önceden talep edilerek mümkündür. Bazı yerlerde Almanca özet istenir: Goethe Frankfurt WiWi İngilizce teze Almanca özetle izin veriyor [\[13\]](#content-kaynaklar); TU Berlin'de tez öğretim dilinden farklı bir dilde yazılırsa Almanca özet gerekiyor [\[10\]](#content-kaynaklar).

**💡 Pratik öneri:** İngilizce bir programda okuyorsanız İngilizce yazmak çoğu zaman en mantıklısıdır. Almanca yazmak iş piyasasında avantaj olabilir, ama kısa sürede ciddi yük getirir.

## Tez teslimi (Abgabe)

Teslim şekli kurumdan kuruma farklıdır. TUM, RWTH, Uni Hamburg WiSo ve Köln WiSo örneklerinde teslim yalnızca dijitaldir. Goethe Frankfurt WiWi, FU Berlin BWL, TH Köln ve LMU ise ciltli nüsha istiyor; TU Berlin'de iki ciltli nüsha + dijital sürüm verilir, iki değerlendirici onaylarsa yalnızca elektronik teslim mümkündür (ayrıntı aşağıdaki tabloda). Teslimle birlikte imzalı beyan eklenir.

**⚖️ Resmî kural:** İncelediğimiz kurumlarda kabul edilmiş bir gerekçe olmadan teslim tarihinin kaçırılması, tezin başarısız (5,0) sayılmasıyla sonuçlanır.

**💡 Pratik öneri:** Ciltleme (Bindung) dükkânları teslim dönemlerinde yoğundur; önceden randevu alın. Dosya formatını ve adlandırma kuralını kontrol edin, bir gün önce yükleyin ve onay e-postasını saklayın.

## Kolokyum ve savunma

**⚖️ Resmî kural (örnekler):** Kolokyum (savunma) bazı kurumlarda zorunlu, bazılarında programa bağlıdır. RWTH'de zorunlu sözlü bölüm [\[9\]](#content-kaynaklar), KIT'te zorunlu sunum [\[11\]](#content-kaynaklar), LMU Informatik'te zorunlu Disputation [\[8\]](#content-kaynaklar) var. TH Köln'de tezden en az 4,0 aldıktan sonra yaklaşık 30 dakikalık, 3 LP'lik ve notlu bir kolokyum yapılır [\[15\]](#content-kaynaklar). Uni Hamburg WiSo'da ise gerekmiyor [\[12\]](#content-kaynaklar).

**💡 Pratik öneri:** Çoğu zaman kısa bir sunumun ardından sorular gelir. Yöntem seçiminizi ve sınırlılıkları savunmaya hazırlanın.

## Notlandırma

Alman üniversitelerinde not ölçeği genellikle 1,0 (en iyi) ile 5,0 (başarısız) arasındadır; 4,0 genellikle geçer nottur. Türk sistemindeki karşılığı için [not dönüştürücümüzü](/tr/tools/grade-converter) kullanın.

**⚖️ Resmî kural (örnekler):** İki değerlendiricinin notu belirgin biçimde farklıysa üçüncü bir değerlendirici devreye girebilir. RWTH'de notlar 2,0'dan fazla farklıysa veya biri geçer, diğeri kalır not verirse [\[9\]](#content-kaynaklar); Köln WiSo'da fark 1,0'dan fazlaysa veya bir not 5,0 ise [\[14\]](#content-kaynaklar); TH Köln'de fark 2,0 veya daha fazlaysa [\[15\]](#content-kaynaklar). Değerlendirme süreleri de örnek olarak şöyle: Uni Hamburg WiSo'da Bachelor için 6 hafta, Master için 3 ay [\[12\]](#content-kaynaklar); Goethe Frankfurt WiWi'de 6 hafta [\[13\]](#content-kaynaklar).

**💡 Pratik öneri:** Değerlendirme süresi, mezuniyet ve vize planınızı doğrudan etkiler; takviminize ekleyin.

## Tez başarısız olursa

**⚖️ Resmî kural (örnekler):** İncelediğimiz kurumların çoğunda başarısız bir tez bir kez tekrarlanabilir; çoğu zaman yeni bir konuyla (ör. TUM, Goethe Frankfurt WiWi) [\[7\]](#content-kaynaklar)[13]. TU Berlin'in kuralları başarısız tezin iki kez tekrarlanmasına izin veriyor [\[10\]](#content-kaynaklar). RWTH'de tekrar için 3 dönem içinde kayıt yaptırmanız gerekiyor [\[9\]](#content-kaynaklar). Uni Hamburg WiSo'da ikinci tekrar yalnızca istisnai durumlarda mümkün [\[12\]](#content-kaynaklar); Köln WiSo'da bir tekrardan sonra kesin başarısızlık gelir [\[14\]](#content-kaynaklar).

Kesin başarısızlık (endgültig nicht bestanden) genellikle kaydınızın silinmesi demektir ve oturum izninizi etkileyebilir; seçenekleri [Exmatrikulation rehberimizde](/tr/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back) ayrıntılı anlattık.

**💡 Pratik öneri:** Hemen Prüfungsamt'tan yazılı bilgi isteyin: tekrar için son tarih ne, itiraz (Widerspruch) mümkün mü?

## Hastalık ve süre uzatma

**⚖️ Resmî kural (örnekler):** Uzatma koşulları çok farklıdır:

- **TUM:** Elde olmayan sebeplerle sürenin yarısına kadar; hekim raporu süreyi durdurur [\[7\]](#content-kaynaklar).
- **TU Berlin:** Engel süresince, programın üst sınırıyla; hastalık raporu 5 gün içinde verilmeli [\[10\]](#content-kaynaklar).
- **TH Köln:** En fazla +2 hafta; uzun süreli hastalıkta tezden çekilip deneme hakkını kaybetmeden yeni konuyla yeniden başlanabilir [\[15\]](#content-kaynaklar).
- **FU Berlin BWL:** Kanıtlanan hastalık süresi kadar; resmî sağlık memurundan rapor (amtsärztliches Attest) gerekir [\[16\]](#content-kaynaklar).
- **LMU Informatik:** PStO'da açıkça düzenlenmemiş; Prüfungsamt yürütür [\[8\]](#content-kaynaklar).

**💡 Pratik öneri:** Hasta olduğunuz gün hekime gidin ve raporu gecikmeden Prüfungsamt'a iletin. Uzatma dilekçesini son haftaya bırakmayın. Ağır bir durumda tüm dönemi dondurmak mantıklıysa [izin dönemi (Urlaubssemester) rehberimize](/tr/blog/urlaubssemester-taking-a-semester-off-in-germany) bakın.

## Mezuniyet ve oturum izni

**⚖️ Resmî kural:** İncelediğimiz kurumların çoğunda diploma tarihi, son sınava veya son değerlendirmeye bağlıdır; ayrıntılar üniversiteye ve programa göre değişir. Örneğin TUM'da tüm sınavların tamamlandığı gün, kolokyum sonuncuysa kolokyum tarihidir; RWTH, LMU, KIT ve TH Köln'de son sınav tarihi; Frankfurt ve Köln'de tez son sınavsa teslim tarihi esas alınır [\[7\]](#content-kaynaklar)[13][14].

Kayıt (Immatrikulation) da önemlidir: TUM'da teslime kadar [\[7\]](#content-kaynaklar), Frankfurt'ta süreç boyunca kayıtlı olmalısınız; Uni Köln'de son sınavı mevcut dönemde veriyorsanız sonraki dönem için kayıt yenilemeniz gerekmez.

**⚖️ Resmî kural:** § 20 AufenthG'ye göre öğrenimini başarıyla tamamlayan kişi, iş aramak için 18 aya kadar oturum izni alabilir; bu sürede her türlü işte çalışabilir, izin uzatılamaz [\[6\]](#content-kaynaklar). Mezuniyet belgesi gerekir ve başvuruyu mevcut izniniz bitmeden yapmalısınız.

**💡 Pratik öneri:** Oturum izninizin bitiş tarihini, teslim + değerlendirme süresiyle karşılaştırın. Bkz. [iş arama vizesi rehberimiz](/tr/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates) ve [çalışma iznine geçiş yazımız](/tr/blog/changing-student-visa-to-work-permit-germany-zweckwechsel).

## 10 üniversite karşılaştırması

| Üniversite / program örneği | Ön şart | Çalışma süresi | Uzatma | Teslim | Kolokyum | Değerlendirici | Tekrar | YZ kuralı |
|---|---|---|---|---|---|---|---|---|
| [TUM](/tr/universities/technische-universitat-munchen-partner-019ddbba), Informatik | Programa göre (M.Sc.: tez normalde son sınav) | B: 4 ay, M: 6 ay | Sürenin yarısına kadar; rapor süreyi durdurur | Elektronik (CIT portalı) | Programa göre (M.Sc.: sunum, notsuz) | Konuyu veren; 2. yalnızca başarısızlıkta | 1, yeni konu | Şeffaf kullanım; öğretim üyesi karar verir |
| [LMU](/tr/universities/ludwig-maximilians-universitat-munchen-q55044), Informatik | P 16.1 modülü (B.Sc.) | B: 14 hafta, M: 19 hafta | PStO'da açıkça düzenlenmemiş | B.Sc. 2 nüsha, M.Sc. 1 nüsha | Zorunlu Disputation | Danışman; 2. yalnızca başarısızlıkta | 1 | Fakülte düzeyinde |
| [RWTH Aachen](/tr/universities/rwth-aachen-university-partner-019de9ee), Informatik | 120 CP + zorunlu modüller (B.Sc.) | Varsayılan en fazla 3/6 ay; B.Sc. 4 ay | +4 hf (B) / +6 hf (M), gerekçeli | Elektronik (RWTHonline) | Zorunlu sözlü bölüm | 2; 3. fark >2,0 veya geçti/kaldı | 1 (3 dönem içinde) | Tek tip kural yok; beyan YZ'yi kapsar |
| TU Berlin, Informatik | 120 LP (B.Sc.) | B.Sc. 20 hf, M.Sc. 26 hf | Engel süresince, üst sınırlı | 2 ciltli + dijital veya yalnızca elektronik | Informatik'te yok | 2 | 2 tekrar | Ürün, üretici, sürüm, kullanım türü |
| [KIT](/tr/universities/karlsruher-institut-fur-technologie-q309988), Informatik | 120 LP (B.Sc.) / 60 LP (M.Sc.) | 4 / 6 ay | +1 ay (B) / +3 ay (M) | Danışmanla; dijital mümkün | Zorunlu sunum | 2 | 1 | Dil araçları beyansız; diğer YZ beyan edilir |
| Uni Hamburg, WiSo | BWL: 120 ECTS + seminer ödevi | B: 9 hf; M: programa göre | +2 hf (B) / +3 hf (M) | Yalnızca dijital | Gerekmiyor | 2 | 1 (2. istisnaen) | İlke olarak izinli; araçlar listelenir |
| Goethe Frankfurt, WiWi | Oryantasyon evresi + 18 CP + seminer | B: 9 hf; M: 3–6 ay | En fazla %50; WiWi en fazla 32 gün | 1 ciltli + USB | Programa göre | 2 | 1, yeni konu | Beyan YZ'yi kapsar; YZ dizini |
| Uni Köln, WiSo | B.Sc. BWL 100 LP; çoğu Master 60 LP | B: en fazla 12 hf, M: en fazla 6 ay | +4 hf (B) / +2 ay (M) | PDF (WiSo gelen kutusu) | Bazı programlarda | 2; 3. fark >1,0 veya 5,0 | 1, sonra kesin başarısızlık | Beyan YZ içeriğini kapsar |
| TH Köln | Programa göre LP | 4 hafta–4 ay | En fazla +2 hf; uzun hastalıkta yeniden başlama | 1 ciltli + dijital | ~30 dk, 3 LP, notlu | 2; 3. fark ≥2,0 | 1 | Beyan ilke olarak evet; metin ürettirmek yasak |
| FU Berlin, BWL | 90 LP | 12 hf | Kanıtlanan hastalık süresi kadar | 2 ciltli + dosya | Programa göre | 2 | 1 | Fakülte düzeyinde (kullanım tablosu) |

> Bu örnekler belirli programlara veya sınav yönetmeliklerine aittir. Kendi Prüfungsordnung'unuz farklı olabilir. (These examples refer to specific programmes or examination regulations. Your own Prüfungsordnung may differ.)

*B = Bachelor, M = Master, hf = hafta. Belgeler Eylül 2026'da kontrol edildi; TU Berlin verileri Prüfungsamt'ın 03/2026 tarihli bilgi notuna dayanır. Diğer programları [üniversite listemizde](/tr/universities) ve [program aramamızda](/tr/programs) inceleyebilirsiniz.*

## Örnek zaman çizelgesi

Aşağıdaki liste bir **örnek süreçtir**; resmî bir sıra değildir. Göreli zamanlamalar pratik öneridir; kendi programınızın takvimi farklı olabilir.

1. **Kayıttan 3–4 ay önce:** Prüfungsordnung'u okuyun.
2. **Kayıttan 3 ay önce:** Kredi ön şartlarını kontrol edin.
3. **Kayıttan 2–3 ay önce:** Kürsüleri ve konu ilanlarını tarayın.
4. **Kayıttan 2–3 ay önce:** Danışman adaylarına e-posta gönderin.
5. **Kayıttan 1–2 ay önce:** İlk görüşmeyi yapın, konuyu daraltın.
6. **Kayıttan 1 ay önce:** Exposé yazın ve danışmanla onaylayın.
7. **Şirket tezi ise, kayıttan önce:** Onay, NDA ve fikrî mülkiyeti netleştirin.
8. **Kayıttan önce:** Tez dilini netleştirin.
9. **Kayıt günü:** Tezi resmî olarak kaydedin; teslim tarihini not alın.
10. **İlk hafta:** Toplantı ritmini ve YZ kurallarını yazılı teyit edin.
11. **Sürenin ilk yarısı:** Literatür, yöntem ve veri toplama.
12. **Sürenin ortası:** Bir bölüm taslağını danışmanınıza gösterin.
13. **Son 3–4 hafta:** Yazımı bitirin, kaynakları kontrol edin, ciltleme randevusu alın.
14. **Teslim:** Beyanla birlikte zamanında teslim edin.
15. **Teslimden sonra:** Kolokyuma hazırlanın, oturum izni adımlarını planlayın.

## Dört öğrenci senaryosu

### Bachelor öğrencisi, akademik tez

- **Önce kontrol edin:** Kredi ön şartı, çalışma süresi (bazı yerlerde yalnızca 9 hafta), kolokyum.
- **Konuşun:** Dersini aldığınız bir hocayla veya onun doktora öğrencisiyle.
- **Hazırlayın:** Kısa bir Exposé ve kaynak listesi.
- **En büyük risk:** Kısa süreyi hafife almak ve konuyu fazla geniş tutmak.

### Araştırma grubunda Master tezi

- **Önce kontrol edin:** Grubun açık konuları, veri ve laboratuvar erişimi, tez dili.
- **Konuşun:** Grup lideri profesör ve günlük danışmanlığı yapacak doktora öğrencisi.
- **Hazırlayın:** Akademik CV, not dökümü, ilgili derslerdeki projeleriniz.
- **En büyük risk:** Veri erişiminin gecikmesi; resmî süre beklemez.

### Şirkette Master tezi

- **Önce kontrol edin:** Dış teze izin, gerekli onaylar, Sperrvermerk imkânı.
- **Konuşun:** Üniversitedeki değerlendirici (önce), şirketteki danışman, gerekirse Ausländerbehörde.
- **Hazırlayın:** Konu tanımı, NDA taslağı, ücret ve sözleşme bilgileri.
- **En büyük risk:** Şirket beklentisiyle akademik beklentinin çatışması.

### Son dönem, sıkışık mezuniyet tarihi

- **Önce kontrol edin:** Değerlendirme süresi, diploma tarihinin nasıl belirlendiği, oturum izninizin bitiş tarihi.
- **Konuşun:** Prüfungsamt, danışmanınız ve gerekirse yabancılar dairesi.
- **Hazırlayın:** Gerçekçi bir takvim ve belge listesi.
- **En büyük risk:** Notun geç gelmesi yüzünden izin süresinin dolması.

## Kontrol listesi

**Kayıttan önce**

- Prüfungsordnung'u ve Prüfungsamt bilgi notlarını okudum.
- Kredi ön şartlarını sağladığımı kontrol ettim.
- Danışmanımı buldum ve onayını aldım.
- Konuyu ve başlığı netleştirdim.
- Tez dilini belirledim, gerekirse onay aldım.
- Dış veya şirket tezi için gereken onayları aldım.
- NDA ve fikrî mülkiyet konularını kontrol ettim.

**Kayıttan sonra**

- Resmî teslim tarihini not aldım.
- Danışmanımla toplantı ritmini belirledim.
- Literatür taramasını başlattım.
- Atıf stilini belirledim ve bir kaynak yönetim aracı kurdum.
- Düzenli yedek alıyorum (bulut + harici disk).
- YZ kullanımımı belgeliyorum.
- Biçim şartlarını (şablon, sayfa düzeni) kontrol ettim.
- Teslim şartlarını (dijital/ciltli, nüsha sayısı) öğrendim.

**Teslimden önce**

- Beyanı (Eidesstattliche Versicherung / Eigenständigkeitserklärung) imzaladım.
- Tüm kaynakları ve atıfları intihale karşı kontrol ettim.
- Dosya formatını ve adlandırmayı kontrol ettim.
- Gerekirse ciltli nüshaları hazırladım.
- Yükleme portalını test ettim.
- Teslim tarihini ve saatini yeniden kontrol ettim.
- Kolokyum tarihini ve şartlarını öğrendim.

## Sık sorulan sorular

### Almanya'da tez yazmak ne kadar sürer?

Tek bir cevap yoktur. Resmî çalışma süresi programa göre değişir: İncelediğimiz örneklerde Uni Hamburg WiSo'da Bachelor için 9 hafta, LMU Informatik'te Master için 19 hafta, TU Berlin'de M.Sc. Computer Science için 26 hafta, TUM'da Master için 6 ay. Buna hazırlık, değerlendirme ve kolokyum eklenir; danışman aramaya kayıttan 2–3 ay önce başlayın.

### Tez danışmanı bulamazsam ne olur?

Önce bölümünüzün öğrenci danışmanlığına (Studienberatung) veya Prüfungsamt'a başvurun ve durumunuzu anlatın. Böyle bir durumda ne yapılacağına dair bir düzenleme olup olmadığını kendi Prüfungsordnung'unuzdan kontrol edin. Pratikte daha iyi yol, erken başlamak, birden fazla kürsüye kısa ve hazırlıklı e-postalar yazmak ve konu ilanlarını takip etmektir.

### Masterarbeit nasıl yazılır, nereden başlamalıyım?

Masterarbeit nasıl yazılır sorusunun cevabı yönetmelikle başlar: süreyi, dili, biçim ve teslim şartlarını öğrenin. Sonra net bir araştırma sorusu belirleyin, 2–5 sayfalık bir Exposé yazın ve danışmanınızla onaylayın. Kayıttan sonra düzenli toplantı ritmi kurun, literatür ve yöntem bölümünü erken bitirin, bir bölüm taslağını ortada paylaşın. Kaynak ve YZ kaydını baştan tutun.

### Tezimi İngilizce yazabilir miyim?

İncelediğimiz kurumlarda Almanca veya İngilizce yaygındır. İngilizce bir programdaysanız genellikle sorun yoktur. Almanca bir programda İngilizce yazmak için önceden onay gerekebilir ve bazı yerlerde Almanca özet istenir; örneğin Goethe Frankfurt WiWi ve TU Berlin'de. Başka diller genellikle yalnızca onayla mümkündür. Kararı kayıttan önce danışmanınızla verin ve gerekiyorsa dilekçeyi zamanında verin.

### Tez yazarken çalışabilir miyim?

Evet, ama sürenin ve yükün farkında olun. Bir öğrenci işinde (ör. Werkstudent) çalışıyor olmanız, resmî teslim tarihinizi kendiliğinden değiştirmez; iş yükünüzü planınıza baştan katın. AB dışı öğrenciler için 140 günlük çalışma hakkı geçerlidir. Yalnızca şirkette tez yazmak bazı uluslararası ofislere göre öğrenimin parçası sayılır; ama tez dışında ek iş yaparsanız bu istihdamdır. Durumunuzu yabancılar dairesine teyit ettirin.

### Tez teslim tarihini kaçırırsam ne olur?

İncelediğimiz kurumlarda kabul edilmiş bir gerekçe olmadan teslim tarihini kaçırmak, tezin başarısız (5,0) sayılmasıyla sonuçlanır. Hastalık gibi bir engel varsa hekim raporunu hemen Prüfungsamt'a iletin; bazı yerlerde süre sınırı vardır (ör. TU Berlin'de 5 gün). Uzatma dilekçesini son güne bırakmayın. Başarısız sayılırsanız genellikle bir tekrar hakkınız olur; kuralları kendi yönetmeliğinizden kontrol edin.

## Kaynaklar

1. KMK, Musterrechtsverordnung (21.11.2024) — https://www.kmk.org/fileadmin/Dateien/veroeffentlichungen_beschluesse/2024/2024_11_21-Musterrechtsverordnung.pdf
2. DFG, Leitlinien zur Sicherung guter wissenschaftlicher Praxis (Kodex v1.2, 2024) — https://www.dfg.de/de/grundlagen-themen/grundlagen-und-prinzipien-der-foerderung/gwp/kodex
3. DFG, Stellungnahme zu generativen Modellen (2023) — https://www.dfg.de/resource/blob/289674/ff57cf46c5ca109cb18533b21fba49bd/230921-stellungnahme-praesidium-ki-ai-data.pdf
4. § 63 HG NRW — https://lexmea.de/de/gesetz/hg-nrw/63
5. § 156 StGB — https://www.gesetze-im-internet.de/stgb/__156.html
6. § 20 AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__20.html ; BAMF, Hochschulabsolventen — https://www.bamf.de/EN/Themen/MigrationAufenthalt/ZuwandererDrittstaaten/Arbeit/Hochschulabsolvent/hochschulabsolvent-node.html
7. TUM APSO (2011, 2024 değişiklikleriyle) — https://www.edu.sot.tum.de/fileadmin/w00bed/edu/Documents/Studium/APSO/Lesb.F._APSO_vom_18.03.2011_mit_11._AES_vom_17.12.2024.pdf ; TUM tez formaliteleri — https://www.tum.de/en/studies/graduation/theses/formalities
8. LMU, PStO B.Sc. Informatik (2022) — https://cms-cdn.lmu.de/media/contenthub/amtliche-veroeffentlichungen/1569-16in-ba-nf60-2022-ps00.pdf
9. RWTH, Übergreifende Prüfungsordnung (2025 değişiklikleriyle) — https://www.rwth-aachen.de/global/show_document.asp?id=aaaaaaaaddqknak ; RWTH YZ ve sınav SSS (2024) — https://cls.rwth-aachen.de/global/show_document.asp?id=aaaaaaaacocblkv
10. TU Berlin Prüfungsamt, Hinweise zur Erstellung von Bachelor- und Masterarbeiten (03/2026) — https://www.static.tu.berlin/fileadmin/www/10002461/Pruefungsamt/Formulare_Bescheide/Abschlussarbeit_Hinweise_03.26.pdf
11. KIT, SPO B.Sc. Informatik (2022) — https://www.sle.kit.edu/downloads/AmtlicheBekanntmachungen/2022_AB_034.pdf ; KIT Informatik Leitfaden Generative KI — https://www.informatik.kit.edu/downloads/studium/Leitfaden_Generative_KI_Informatik.pdf
12. Uni Hamburg, PO WiSo B.Sc. (2024) — https://www.uni-hamburg.de/campuscenter/studienorganisation/ordnungen-satzungen/pruefungsordnungen/wirtschafts-und-sozialwissenschaften/20240508-po-wiso-bsc-82.pdf ; Orientierungsrahmen gKI (2025) — https://www.uni-hamburg.de/lehre-navi/lehrende/orientierungsrahmen-gki/orientierungsrahmen-gki.pdf
13. Goethe-Universität Frankfurt, B.Sc. Wirtschaftswissenschaften PO 2022 — https://www.wiwi.uni-frankfurt.de/fileadmin/studium/pruefungsorganisation/dateien/bsc_wirtschaftswissenschaften_po2022.pdf
14. Universität zu Köln, WiSo Gemeinsame Prüfungsordnung (2025) — https://wiso.uni-koeln.de/sites/fakultaet/dokumente/PA/po/GPO_2025.pdf
15. TH Köln, Rahmenprüfungsordnungen (2023) — https://www.th-koeln.de/mam/downloads/deutsch/hochschule/amtlichemitteilungen/inkraftsetzungssatzung_rpoen_2023_final.pdf ; TH Köln Abschlussarbeiten — https://www.th-koeln.de/studium/abschlussarbeiten_5336.php
16. FU Berlin, Rahmenstudien- und -prüfungsordnung (2013) — https://www.fu-berlin.de/service/zuvdocs/amtsblatt/2013/ab322013.pdf
17. Universität Hamburg, Handreichung Nr. 15 Täuschung in der Prüfung (2025) — https://www.uni-hamburg.de/uhh/organisation/praesidialverwaltung/studium-und-lehre/qualitaet-und-recht/handreichungen/dateien/handreichung-15-taeuschung-in-der-pruefung.pdf
18. HS Emden/Leer, Merkblatt externe Abschlussarbeiten (2024) — https://www.hs-emden-leer.de/fileadmin/user_upload/sta/Dokumente/Allgemein/Merkblatt_externe_Abschlussarbeiten_V11.pdf
19. Universität Potsdam, Arbeiten für internationale Studierende (2026) — https://www.uni-potsdam.de/de/international/incoming/students/arbeiten
20. TH Köln, KI-Tools handout (Schreibzentrum) — https://www.th-koeln.de/mam/downloads/deutsch/studium/rundumsstudium/handout_ki-tools.pdf
21. Universität Leipzig, Kennzeichnung KI-generierter Inhalte — https://kb.el.uni-leipzig.de/books/generative-ki-in-der-hochschullehre/page/kennzeichnung-von-ki-generierten-inhalten
22. FU Berlin Technologietransfer, FAQ Arbeitnehmererfindungen — https://www.fu-berlin.de/forschung/technologietransfer/patente-und-lizenzen-01/faq/arbeitnehmer.html

*Örnekler Eylül 2026'da 10 Alman üniversitesi/fakültesinde kontrol edildi; belgeler değişir, kendi yönetmeliğinizin güncel sürümünü mutlaka kontrol edin. Bu yazı genel bilgi amaçlıdır, hukuki danışmanlık değildir.*
MD;

        $enBody = <<<'MD'
There is no single thesis process in Germany. Your Bachelor or Master thesis follows the rules of your own Prüfungsordnung (examination regulation), your faculty, your supervisor and your Prüfungsamt (examination office), and these differ between universities. Below: the general framework, real examples from 10 German universities and faculties, and a practical process.

> **Last updated:** September 2026 · Thesis rules vary by university, faculty and examination regulations. · ⚖️ Official rule = taken from a law or an official regulation; 💡 Practical advice = our experience-based recommendation · Examples checked across 10 German universities/faculties (September 2026); this is not a universal rule.

## What a Bachelorarbeit and a Masterarbeit are

**⚖️ Official rule:** The KMK (Standing Conference of Education Ministers) model ordinance of November 2024 sets the frame: every Bachelor and Master programme includes a thesis in which you show that you can work on a problem in your subject independently, with scientific methods, within a set period. The Bachelor thesis is worth 6–12 ECTS, the Master thesis 15–30 ECTS (with exceptions for fine arts) [\[1\]](#content-sources).

Key terms:

- **Bachelorarbeit / Masterarbeit:** the Bachelor's / Master's thesis.
- **Betreuer/in:** your supervisor.
- **Prüfer/in, Erstprüfer/in, Zweitprüfer/in:** examiner, first examiner, second examiner.
- **Prüfungsamt:** the examination office that registers the thesis, records deadlines and receives the submission.
- **Prüfungsausschuss:** the examination board that often decides on applications such as extensions or external theses.
- **Bearbeitungszeit:** the official working time between issue of the topic and the deadline.
- **Abgabe:** submission.
- **Kolloquium / Disputation:** an oral presentation or defence of your thesis.
- **Sperrvermerk:** a blocking notice that restricts access to a confidential thesis.
- **Eidesstattliche Versicherung / Eigenständigkeitserklärung:** a sworn declaration or a declaration of independent work that you sign.
- **Exposé:** a short research proposal outlining question, method and plan.

## The first document to read: your Prüfungsordnung

**⚖️ Official rule:** There is no federal thesis rule. The rules come in layers: the state higher-education law (Landeshochschulgesetz), then the state accreditation ordinance based on the KMK model, then your university's general examination regulation, and finally your programme's own Prüfungsordnung [\[1\]](#content-sources). The general regulation (names vary, for example APSO at TUM, ÜPO at RWTH or RSPO at FU Berlin) sets the frame; the programme regulation fills in details such as credit prerequisites and working time.

**💡 Practical advice:** Find both documents under "Amtliche Bekanntmachungen" (official announcements) or on your programme page, and check which version applies to you. Then read the faculty's thesis guidelines and Prüfungsamt leaflets. **Your Prüfungsordnung, faculty guidance and supervisor instructions prevail.** This article gives examples, not a substitute.

## When you can start the thesis

**⚖️ Official rule:** Many programmes require credits before you can register. Examples:

- RWTH Aachen, B.Sc. Informatik: 120 CP plus compulsory modules; for any RWTH Master thesis, the module "Wissenschaftliche Integrität" (scientific integrity) [\[9\]](#content-sources).
- TU Berlin, B.Sc. Informatik: 120 LP [\[10\]](#content-sources).
- KIT Informatik: 120 LP (B.Sc.) or 60 LP (M.Sc.) [\[11\]](#content-sources).
- Goethe Frankfurt, B.Sc. Wirtschaftswissenschaften: orientation phase plus 18 CP plus a seminar [\[13\]](#content-sources).
- Universität zu Köln, WiSo: 100 LP for the B.Sc. BWL, 60 LP for most WiSo Masters [\[14\]](#content-sources).
- LMU, B.Sc. Informatik: the module P 16.1 [\[8\]](#content-sources).

At TUM, the prerequisite is set by the programme; in the Informatics M.Sc., the thesis is normally the last exam [\[7\]](#content-sources). In other programmes, you may still take courses in parallel; check your regulation.

## How to find a topic

Topics usually come from three directions:

- **Your own proposal** that fits a chair's research area.
- **Advertised topics** on chair websites or topic exchanges, for example TUM's "Themenbörse".
- **Research groups and companies**, for example via a HiWi (student assistant) job or a company project.

**💡 Practical advice:** Start looking one or two semesters before registering, and read recent theses from your target chair to understand the expected scope. Overly broad topics are a common reason theses run late.

**⚖️ Official rule:** In most of the institutions we examined, the topic can be returned once within an early window. But this right does not exist in every programme; for example, in the Köln WiSo example we could not verify such a free return right. Examples: LMU Informatik and Uni Hamburg WiSo (within 2 weeks, at Hamburg with a reason), TH Köln (within 2 weeks, without a reason), KIT (in the first month).

## How to find a supervisor

**💡 Practical advice:** Approach professors whose courses you took or whose research matches your interest, after checking the chair's website for its thesis process. Contact them two to three months before registering. Keep the email short:

> Subject: Master thesis inquiry – [topic area]
>
> Dear Professor [Name],
>
> I am a Master's student in [programme] at [university] and have completed [relevant course]. I am interested in writing my Master thesis in the area of [topic] and would like to ask whether you supervise theses in this area in [semester]. I have attached a one-page outline and my transcript.
>
> Kind regards,
> [Name], [student ID]

No answer after one to two weeks? Follow up once, or ask a research assistant at the chair.

## Betreuer, Prüfer and Prüfungsamt: who does what

Your **Betreuer** guides the work, your **Prüfer** grade it, and the **Prüfungsamt** handles the administration.

**⚖️ Official rule:** The number of graders differs. At TUM, the thesis is graded by the person who set the topic, and a second examiner is involved only if it would fail [\[7\]](#content-sources). LMU Informatik works the same way [\[8\]](#content-sources). RWTH, TU Berlin, KIT, Uni Hamburg WiSo, Goethe Frankfurt WiWi, Köln WiSo, TH Köln and FU Berlin BWL use two examiners.

## Registering your thesis

**⚖️ Official rule:** Registration makes your thesis official. Depending on the institution, it runs through an online portal or a form signed by you and your supervisor. The Prüfungsamt then records the topic and the issue date, which sets your deadline.

**💡 Practical advice:** Before registering, confirm the exact title, language, examiners, and whether you need an approval for an external or company thesis. Keep the confirmation showing your deadline.

## When the Bearbeitungszeit starts

**⚖️ Official rule:** In the institutions we examined, the official working time runs from the recorded issue date of the topic, not from the day you started reading. Goethe Frankfurt WiWi explicitly forbids working on the thesis before the topic is officially recorded [\[13\]](#content-sources). The other institutions we examined do not regulate this point explicitly.

**💡 Practical advice:** Reading and drafting an exposé before registration is common, but clarify with your supervisor what is acceptable in your programme.

## Working with your supervisor

**💡 Practical advice:** Agree early on an exposé, a meeting rhythm (for example every two to four weeks), how you share drafts and what feedback to expect. Summarize each meeting in a short email.

**⚖️ Official rule:** Some faculties formalize this: at Universität zu Köln WiSo, theses starting from 1 October 2025 require mandatory progress documentation [\[14\]](#content-sources).

## Sources, citation and plagiarism

**⚖️ Official rule:** The German Research Foundation (DFG) Code of good research practice requires citing original sources, treats plagiarism and fabrication as scientific misconduct, and links authorship to a genuine contribution [\[2\]](#content-sources).

You will sign a declaration, named differently by institution: an "Eidesstattliche Versicherung / Versicherung an Eides statt" (sworn declaration), for example at Uni Hamburg WiSo, Köln WiSo and RWTH, or an "Eigenständigkeitserklärung / Selbstständigkeitserklärung" (declaration of independent work), for example at Goethe Frankfurt, TU Berlin and FU Berlin. Whether it is a formal affidavit depends on the federal state. In North Rhine-Westphalia, universities may take affidavits, so a false one can be a criminal offense under § 156 StGB; NRW also allows fines of up to €50,000 for deliberate cheating and exmatriculation for repeated or serious cases [\[4\]](#content-sources)[5].

Plagiarism or cheating typically means "nicht bestanden" (failed, 5.0); serious or repeated cases can lead to exmatriculation. Uni Hamburg's 2025 guidance treats ghostwriting as usually a particularly serious deception, while unauthorised AI use alone is not automatically a particularly serious case [\[17\]](#content-sources). A degree can be revoked later if cheating is discovered, within time limits set by the regulation.

**💡 Practical advice:** Use a reference manager from day one and agree on one citation style.

## AI and ChatGPT in your thesis

AI is neither banned nor freely allowed across Germany. **Your Prüfungsordnung, faculty guidance and supervisor instructions prevail.** The common trend in the institutions we examined is transparent disclosure of the AI tools used and full responsibility of the student for the content. But the binding rule varies by university, faculty, Prüfungsordnung and supervisor.

**⚖️ Official rule:** Concrete examples:

- **TU Berlin:** the declaration asks for product name, manufacturer, version and type of use; documenting prompts or chats is recommended [\[10\]](#content-sources).
- **Goethe Frankfurt WiWi:** your own contribution must predominate, AI-assisted parts are marked, idea generation is disclosed, and you list tools, version, use and affected parts in an AI directory. Spell-checkers, reference managers and statistics software need no mention [\[13\]](#content-sources).
- **KIT Informatik:** spelling, grammar and translation tools need no disclosure; other generative AI use must be disclosed (purpose, parts, models) and agreed with the supervisor in advance [\[11\]](#content-sources).
- **RWTH:** no uniform marking rule, but the affidavit explicitly covers "software and services for language, text and media production" [\[9\]](#content-sources).
- **TH Köln:** disclosure "in principle yes"; generating whole texts or substantial parts is not permitted; clarify with your lecturers [\[20\]](#content-sources).
- **Uni Hamburg:** the framework allows generative AI in unsupervised work in principle, but unmarked non-trivial AI parts are not allowed and tools must be listed [\[12\]](#content-sources).

The DFG statement on generative models, aimed at researchers rather than student exams, makes the same core point: using AI does not release authors from responsibility, and only natural persons can be authors [\[3\]](#content-sources). Undisclosed or unauthorised use is treated as cheating in several institutions. AI tools can also produce non-existent sources and hidden plagiarism [\[21\]](#content-sources). Detector software is only supporting evidence; at Goethe Frankfurt, a suspicion leads to a hearing or oral check.

Proofreading and language correction are often treated differently from generated content (KIT and Uni Leipzig are examples), but this varies. Uni Leipzig suggests adding the prompts you used as an appendix [\[21\]](#content-sources).

**💡 Practical advice:**

- Keep a log of the tools, versions and prompts you use and what you did with the output.
- Check every reference yourself in the original source.
- Never enter personal, confidential, company or copyrighted data into public AI tools.
- Ask your supervisor in writing what is allowed before you start, and save the answer.

## Writing your thesis in a company

**⚖️ Official rule:** In the institutions we examined, academic authority stays with the university: a university examiner sets or approves the topic and grades the thesis, while the company supervisor may advise or suggest but does not set the grade (for example Goethe Frankfurt, LMU Informatik, TUM, RWTH). It usually needs approval of the board or chair (Frankfurt, TH Köln, KIT; at RWTH only exceptionally). At TUM, a company thesis is possible only with a TUM examiner [\[7\]](#content-sources); at FU Berlin, only if the programme regulation allows it.

**Pay and social insurance:** a student who only writes the thesis in a company is generally not an employee. If you also do productive work for the company, normal employment rules apply. See our guide on [HiWi vs. Werkstudent jobs](/en/blog/hiwi-vs-werkstudent-student-jobs-in-germany-en) and on [how Werkstudent experience shapes your job market chances](/en/blog/werkstudent-germany-job-market-experience-grades).

**The 140-day question (non-EU students):** there is no federal general rule. International offices such as Uni Potsdam's state that a company thesis counts as part of your studies and does not use your 140-day account [\[19\]](#content-sources). **💡 Practical advice:** confirm with your Ausländerbehörde (immigration office) before you start, and remember that extra productive work counts as employment. If you are combining a job with the thesis, see our FAQ on [working full time while writing a thesis](/en/faq/is/almanyada-lisans-tezi-yazarken-tam-zamanli-calismak-mumkun-mudur-en).

## NDA, Sperrvermerk and intellectual property

**⚖️ Official rule:** A non-disclosure agreement (NDA) is a private contract between you and the company. It must not prevent examiners, including in appeal procedures, from reading and verifying the thesis; professors are bound to confidentiality anyway under § 203 StGB [\[18\]](#content-sources). At Köln WiSo, you must disclose an NDA to the topic setter before the topic is set [\[14\]](#content-sources).

Sperrvermerk rules vary. Köln WiSo allows one with conditions; publication then needs consent. At TH Köln, one faculty requires the marking on every copy. At TU Berlin, Informatik and Computer Science do not allow a Sperrvermerk or confidentiality beyond usual discretion [\[10\]](#content-sources).

**Copyright:** you are the author; a company gets usage rights only by contract. **Inventions:** students not employed by the university are "free inventors" [\[22\]](#content-sources). If you are employed by the company, for example as a Werkstudent, employee-invention law may apply, so check your contract. Never put company data into public AI tools.

## Thesis language

**⚖️ Official rule:** German or English is common. Other languages usually require approval, requested in advance. Some institutions require a German summary, for example Goethe Frankfurt WiWi for English theses and TU Berlin when the thesis is not in the programme's teaching language.

**💡 Practical advice:** Settle the language with your supervisor before registration.

## Submission

**⚖️ Official rule:** Submission formats vary. It is digital-only in some institutions we examined (TUM, RWTH, Uni Hamburg WiSo, Köln WiSo) and requires bound copies in others (Goethe Frankfurt WiWi, FU Berlin BWL, TH Köln, LMU, and TU Berlin unless both examiners agree to electronic submission). You attach or upload the signed declaration. If you miss the deadline without an accepted reason, the thesis is typically graded as failed (5.0).

**💡 Practical advice:** Plan printing and binding time, keep two backups, submit a few days early and keep a receipt.

## Colloquium and defence

**⚖️ Official rule:** An oral part is mandatory in some institutions we examined, for example RWTH, KIT, LMU (Disputation worth 3/5 ECTS) and TH Köln (about 30 minutes, 3 LP, graded, held once the thesis is rated at least 4.0, the pass mark). Elsewhere it depends on the programme: at TUM, the Informatics M.Sc. has an ungraded presentation; at Köln WiSo, only some programmes have one; at Uni Hamburg WiSo, it is not required.

**💡 Practical advice:** Fit your talk to the set time, anticipate questions on method and limitations, and rehearse.

## Grading

**⚖️ Official rule:** With two examiners, several institutions add a third if the grades differ strongly. Examples: RWTH, if they are more than 2.0 apart or one says pass and the other fail; Köln WiSo, if they are more than 1.0 apart or one gives a 5.0; TH Köln, if they are 2.0 or more apart. Grading periods are set by some regulations, for example 6 weeks for a Bachelor and 3 months for a Master at Uni Hamburg WiSo, and 6 weeks at Goethe Frankfurt WiWi. Use our [grade converter](/en/tools/grade-converter) to see how German grades translate.

## If the thesis fails

**⚖️ Official rule:** Most institutions we examined allow one repeat; at TUM and Goethe Frankfurt, with a new topic. At Uni Hamburg WiSo, a second repeat is possible only in exceptional cases. TU Berlin's rules allow two repeats. At Köln WiSo, a failed repeat means final failure. Final failure usually leads to exmatriculation, which can affect your residence permit. Read our guide on [Exmatrikulation, residence permit and coming back](/en/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-en), and see our [Exmatrikulation template](/en/templates/exmatrikulation).

**💡 Practical advice:** If your thesis is at risk, talk to your supervisor and student counseling early; our article on [loneliness and mental health](/en/blog/loneliness-mental-health-international-students-germany) lists support options.

## Illness and extensions

**⚖️ Official rule:** Examples:

- TUM: up to half the working time for reasons beyond your control; a medical certificate pauses the clock [\[7\]](#content-sources).
- RWTH: +4 weeks (Bachelor) / +6 weeks (Master) on reasoned application [\[9\]](#content-sources).
- KIT: +1 month / +3 months [\[11\]](#content-sources).
- Uni Hamburg WiSo: +2 weeks / +3 weeks, more only in exceptional hardship, with a qualified medical certificate [\[12\]](#content-sources).
- Goethe Frankfurt: max. +50%, in WiWi max. 32 days [\[13\]](#content-sources).
- TU Berlin: for the duration of the obstacle, capped by the programme; illness certificate within 5 days [\[10\]](#content-sources).
- FU Berlin BWL: by the period of proven illness, with a certificate from a public health officer ("amtsärztliches Attest") [\[16\]](#content-sources).
- TH Köln: max. +2 weeks; with longer illness you can withdraw and restart with a new topic without losing an attempt [\[15\]](#content-sources).
- LMU Informatik: not regulated explicitly in the PStO; ask the exam office.

**💡 Practical advice:** Apply before the deadline, not after. For a longer break, consider a leave semester; see our guide to the [Urlaubssemester](/en/blog/urlaubssemester-taking-a-semester-off-in-germany-en).

## Graduation and your residence permit

**⚖️ Official rule:** In most of the institutions we examined, the certificate date is tied to the last exam or last assessment; details vary by university and programme. Stay enrolled as your university requires: at TUM until submission, at Goethe Frankfurt throughout the process; at Uni Köln, if you take your last exam in the current semester, you don't need to re-register for the next.

After successful completion, § 20 AufenthG allows a residence permit for job search of up to 18 months, during which any work is allowed; it cannot be extended [\[6\]](#content-sources). You need proof of completion; apply before your current permit expires. See our [job seeker visa guide](/en/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-en) and the [Zweckwechsel guide](/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en). See also [the German job market after graduation](/en/blog/germany-job-market-after-graduation-degree-not-job).

## 10 universities compared

| University / programme example | Prerequisite | Working time | Extension | Submission | Colloquium | Graders | Repeats | AI rule |
|---|---|---|---|---|---|---|---|---|
| [TUM](/en/universities/technische-universitat-munchen-partner-019ddbba), Informatik | Set by programme (M.Sc.: normally last exam) | B 4 / M 6 months | Up to half, reasons beyond control | Electronic (portal) | Per programme (M.Sc.: ungraded presentation) | 1 (2nd only if failing) | 1, new topic | AI Strategy 2025; instructors decide |
| [LMU](/en/universities/ludwig-maximilians-universitat-munchen-q55044), Informatik | Module P 16.1 (B.Sc.) | B 14 / M 19 weeks | Not explicit in PStO | B 2 copies / M 1 copy | Mandatory Disputation | 1 (2nd only if failing) | 1 | Faculty-level guidance |
| [RWTH Aachen](/en/universities/rwth-aachen-university-partner-019de9ee), Informatik | B.Sc. 120 CP + modules; M: "Wiss. Integrität" | Max 3/6 months (B.Sc. Informatik 4) | +4 weeks B / +6 weeks M | Electronic (RWTHonline) | Mandatory | 2; 3rd if >2.0 apart or pass/fail | 1 | Affidavit covers AI tools |
| TU Berlin, Informatik | 120 LP (B.Sc.) | B 20 / M 26 weeks | Length of obstacle, capped | 2 bound + digital, or electronic if both agree | Per programme (not Informatik) | 2 | 2 | Tool, maker, version, use declared |
| [KIT](/en/universities/karlsruher-institut-fur-technologie-q309988), Informatik | 120 LP B / 60 LP M | 4 / 6 months | +1 month B / +3 months M | Agreed with supervisor | Mandatory presentation | 2 | 1 | Disclose beyond spelling/translation |
| Uni Hamburg, WiSo | BWL: 120 ECTS + seminar paper | B 9 weeks; M per programme | +2 weeks B / +3 weeks M | Digital only | Not required | 2 | 1 (2nd exceptionally) | Mark non-trivial AI parts; list tools |
| Goethe Frankfurt, WiWi | Orientation phase + 18 CP + seminar | B 9 weeks; M 3–6 months | Max +50% (WiWi 32 days) | 1 bound copy + USB | Per programme | 2 | 1, new topic | AI directory; parts marked |
| Uni Köln, WiSo | B.Sc. BWL 100 LP; most M 60 LP | B max 12 weeks; M max 6 months | +4 weeks B / +2 months M | PDF via WiSo inbox | Some programmes | 2; 3rd if >1.0 apart or 5.0 | 1, then final failure | Declaration covers AI content |
| TH Köln | Programme-specific LP | 4 weeks–4 months | Max +2 weeks | 1 bound + digital | ~30 min, graded | 2; 3rd if ≥2.0 apart | 1 | Disclose; no whole texts |
| FU Berlin, BWL | 90 LP | 12 weeks | Proven illness period | 2 bound + file | Per programme | 2 | 1 | Faculty-level (usage table) |

> These examples refer to specific programmes or examination regulations. Your own Prüfungsordnung may differ.

*Documents checked September 2026; TU Berlin based on the Prüfungsamt guidance 03/2026; Frankfurt based on the Prüfungsamt leaflet 03/2026.*

Institution types differ too; see [Hochschule vs. Universität vs. FH](/en/blog/hochschule-vs-universitaet-vs-fh-differences-in-germany-en).

## Example timeline

**💡 Practical advice:** An example process with rough timing; your deadlines come from your regulation.

1. Read your Prüfungsordnung and faculty guidelines (two semesters before).
2. Check your credit prerequisites (one to two semesters before).
3. Choose a topic area.
4. Contact potential supervisors (2–3 months before registration).
5. Write a short exposé and agree on it (1–2 months before).
6. Clarify company approval, NDA and IP if relevant.
7. Agree on language, examiners and AI rules.
8. Register the thesis and note the official deadline.
9. Set a meeting rhythm and start (week 1).
10. Build your literature base (first weeks).
11. Log AI tools, sources and progress (throughout).
12. Share a full draft for feedback (about two-thirds in).
13. Revise, format, sign the declaration (last 2–3 weeks).
14. Submit early and keep the receipt.
15. Prepare the colloquium, then graduation and residence steps.

## Four student scenarios

### Bachelor thesis in an academic setting

- **Check first:** credit prerequisite, working time, topic-return window.
- **Talk to:** the chair whose course you liked most.
- **Prepare:** a one-page idea and your transcript.
- **Biggest risk:** a topic too broad for the working time.

### Master thesis with a research group

- **Check first:** whether a colloquium is mandatory and how grading works.
- **Talk to:** the professor and the research assistant supervising day to day.
- **Prepare:** an exposé and relevant course or HiWi experience.
- **Biggest risk:** unclear expectations; agree on scope in writing. Considering research? Read about [doing a PhD in Germany](/en/blog/doing-a-phd-and-research-career-in-germany-as-a-foreigner-en).

### Master thesis in a company

- **Check first:** whether company theses are allowed and need approval.
- **Talk to:** a university examiner first, then the company, and your Ausländerbehörde about the 140 days.
- **Prepare:** NDA and Sperrvermerk rules, IP and pay questions.
- **Biggest risk:** company goals overriding academic requirements. See also [internships in Germany](/en/blog/internship-in-germany-with-b1-b2-german-en).

### Final semester with a tight graduation date

- **Check first:** how your certificate date is set and enrolment rules.
- **Talk to:** the Prüfungsamt about grading times and the colloquium date.
- **Prepare:** a buffer for grading and your residence permit expiry date.
- **Biggest risk:** your permit expiring before you have proof of completion.

## Checklist

Before registration:

- Read your Prüfungsordnung and faculty guidelines
- Confirm eligibility (credits, modules)
- Find a supervisor and examiners
- Agree on the topic and exposé
- Settle the thesis language
- Get external/company approval if needed
- Check NDA and IP

After registration:

- Note the official deadline
- Agree on a meeting rhythm
- Build your literature base
- Use one citation style consistently
- Back up your files daily
- Document AI use
- Check formatting requirements
- Check submission requirements

Before submission:

- Sign the declaration
- Run a plagiarism check if offered
- Check the file format
- Print and bind copies if required
- Upload to the correct portal
- Submit before the deadline
- Prepare for the colloquium

## FAQ

### How long does a Master thesis take in Germany?

It depends on your programme. In the institutions we examined, Master working times include 19 weeks (LMU Informatik), 26 weeks (TU Berlin Computer Science), 3–6 months (Goethe Frankfurt) and 6 months (TUM, KIT, Köln WiSo). Bachelor examples range from 9 weeks (Uni Hamburg WiSo, Goethe Frankfurt WiWi) to 20 weeks (TU Berlin). Your Prüfungsordnung sets your working time, which starts with the official issue date.

### Can I start writing before I register my thesis?

Reading and planning are common, but the official working time starts with the recorded issue date. Goethe Frankfurt WiWi explicitly forbids working on the thesis before the topic is officially recorded. The other institutions we examined do not regulate this explicitly. Clarify with your supervisor what is acceptable in your programme.

### Can I use ChatGPT for my thesis?

It depends on your university, faculty, Prüfungsordnung and supervisor. The common trend in the institutions we examined is transparent disclosure and full student responsibility for the content. KIT does not require disclosure of spelling or translation tools, while Frankfurt WiWi asks for an AI directory. Ask your supervisor in writing and keep a log.

### Do I get paid for a thesis in a company?

Pay depends on your agreement with the company. A student who only writes the thesis in a company is generally not an employee. If you also do productive work for the company, normal employment rules apply. Non-EU students should confirm with their Ausländerbehörde how the work is counted against the 140 days.

### What happens if I miss the thesis deadline?

If you miss the deadline without an accepted reason, the thesis is typically graded as failed (5.0). Most institutions we examined allow one repeat, and TU Berlin's rules allow two. If illness delays you, apply for an extension before the deadline with the certificate your regulation requires.

### Can I write my thesis in English?

In many programmes, yes. German and English are both common thesis languages. Some institutions require a German summary, for example Goethe Frankfurt WiWi and TU Berlin when the thesis is not in the teaching language. Other languages usually need approval requested in advance. Settle the language before registration.

## Sources

1. KMK, Musterrechtsverordnung (21 Nov 2024) — https://www.kmk.org/fileadmin/Dateien/veroeffentlichungen_beschluesse/2024/2024_11_21-Musterrechtsverordnung.pdf
2. DFG, Guidelines for Safeguarding Good Research Practice (Code v1.2, 2024) — https://www.dfg.de/de/grundlagen-themen/grundlagen-und-prinzipien-der-foerderung/gwp/kodex
3. DFG, Statement on generative models (2023) — https://www.dfg.de/resource/blob/289674/ff57cf46c5ca109cb18533b21fba49bd/230921-stellungnahme-praesidium-ki-ai-data.pdf
4. § 63 HG NRW — https://lexmea.de/de/gesetz/hg-nrw/63
5. § 156 StGB — https://www.gesetze-im-internet.de/stgb/__156.html
6. § 20 AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__20.html ; BAMF, graduates — https://www.bamf.de/EN/Themen/MigrationAufenthalt/ZuwandererDrittstaaten/Arbeit/Hochschulabsolvent/hochschulabsolvent-node.html
7. TUM APSO (2011, as amended 2024) — https://www.edu.sot.tum.de/fileadmin/w00bed/edu/Documents/Studium/APSO/Lesb.F._APSO_vom_18.03.2011_mit_11._AES_vom_17.12.2024.pdf ; TUM thesis formalities — https://www.tum.de/en/studies/graduation/theses/formalities
8. LMU, PStO B.Sc. Informatik (2022) — https://cms-cdn.lmu.de/media/contenthub/amtliche-veroeffentlichungen/1569-16in-ba-nf60-2022-ps00.pdf
9. RWTH, Übergreifende Prüfungsordnung (as amended 2025) — https://www.rwth-aachen.de/global/show_document.asp?id=aaaaaaaaddqknak ; RWTH AI exam FAQ (2024) — https://cls.rwth-aachen.de/global/show_document.asp?id=aaaaaaaacocblkv
10. TU Berlin Prüfungsamt, Hinweise zur Erstellung von Bachelor- und Masterarbeiten (03/2026) — https://www.static.tu.berlin/fileadmin/www/10002461/Pruefungsamt/Formulare_Bescheide/Abschlussarbeit_Hinweise_03.26.pdf
11. KIT, SPO B.Sc. Informatik (2022) — https://www.sle.kit.edu/downloads/AmtlicheBekanntmachungen/2022_AB_034.pdf ; KIT Informatik, Leitfaden Generative KI — https://www.informatik.kit.edu/downloads/studium/Leitfaden_Generative_KI_Informatik.pdf
12. Uni Hamburg, PO WiSo B.Sc. (2024) — https://www.uni-hamburg.de/campuscenter/studienorganisation/ordnungen-satzungen/pruefungsordnungen/wirtschafts-und-sozialwissenschaften/20240508-po-wiso-bsc-82.pdf ; Orientierungsrahmen gKI (2025) — https://www.uni-hamburg.de/lehre-navi/lehrende/orientierungsrahmen-gki/orientierungsrahmen-gki.pdf
13. Goethe-Universität Frankfurt, B.Sc. Wirtschaftswissenschaften PO 2022 — https://www.wiwi.uni-frankfurt.de/fileadmin/studium/pruefungsorganisation/dateien/bsc_wirtschaftswissenschaften_po2022.pdf
14. Universität zu Köln, WiSo Gemeinsame Prüfungsordnung (2025) — https://wiso.uni-koeln.de/sites/fakultaet/dokumente/PA/po/GPO_2025.pdf
15. TH Köln, Rahmenprüfungsordnungen (2023) — https://www.th-koeln.de/mam/downloads/deutsch/hochschule/amtlichemitteilungen/inkraftsetzungssatzung_rpoen_2023_final.pdf ; TH Köln, Abschlussarbeiten — https://www.th-koeln.de/studium/abschlussarbeiten_5336.php
16. FU Berlin, Rahmenstudien- und -prüfungsordnung (2013) — https://www.fu-berlin.de/service/zuvdocs/amtsblatt/2013/ab322013.pdf
17. Universität Hamburg, Handreichung Nr. 15 Täuschung in der Prüfung (2025) — https://www.uni-hamburg.de/uhh/organisation/praesidialverwaltung/studium-und-lehre/qualitaet-und-recht/handreichungen/dateien/handreichung-15-taeuschung-in-der-pruefung.pdf
18. HS Emden/Leer, Merkblatt externe Abschlussarbeiten (2024) — https://www.hs-emden-leer.de/fileadmin/user_upload/sta/Dokumente/Allgemein/Merkblatt_externe_Abschlussarbeiten_V11.pdf
19. Universität Potsdam, Arbeiten für internationale Studierende (2026) — https://www.uni-potsdam.de/de/international/incoming/students/arbeiten
20. TH Köln, KI-Tools handout (Schreibzentrum) — https://www.th-koeln.de/mam/downloads/deutsch/studium/rundumsstudium/handout_ki-tools.pdf
21. Universität Leipzig, Kennzeichnung KI-generierter Inhalte — https://kb.el.uni-leipzig.de/books/generative-ki-in-der-hochschullehre/page/kennzeichnung-von-ki-generierten-inhalten
22. FU Berlin Technologietransfer, FAQ Arbeitnehmererfindungen — https://www.fu-berlin.de/forschung/technologietransfer/patente-und-lizenzen-01/faq/arbeitnehmer.html

Examples checked in September 2026 across 10 German universities/faculties; documents change — always check the current version of your own regulation. General information, not legal advice.
MD;

        $deBody = <<<'MD'
Einen einheitlichen Abschlussarbeit-Ablauf gibt es in Deutschland nicht: Wann Sie Ihre Bachelorarbeit oder Masterarbeit anmelden dürfen, wie lange die Bearbeitungszeit läuft, wie Sie abgeben und wer benotet, legt Ihre eigene Prüfungsordnung fest. Dieser Leitfaden erklärt den allgemeinen Rahmen, zeigt echte Beispiele aus 10 Hochschulen und Fakultäten und führt Sie praktisch von der Themensuche bis zur Note.

> **Stand: September 2026** (zuletzt aktualisiert) · Die Regeln für Abschlussarbeiten unterscheiden sich je nach Hochschule, Fakultät und Prüfungsordnung. · **⚖️ Regelung** = Vorgabe aus einer Prüfungsordnung, einem Gesetz oder einem offiziellen Merkblatt · **💡 Praxistipp** = unsere praktische Empfehlung · Beispiele geprüft an 10 deutschen Hochschulen bzw. Fakultäten (September 2026); das ist keine allgemeingültige Regel.

## Was eine Bachelorarbeit und eine Masterarbeit sind

**⚖️ Regelung:** Nach der Musterrechtsverordnung der Kultusministerkonferenz (KMK) vom 21. November 2024 enthält jeder Bachelor- und Masterstudiengang eine Abschlussarbeit. Sie zeigt, dass Sie ein fachliches Problem in einer festgelegten Frist selbstständig wissenschaftlich bearbeiten können (§ 4 Abs. 3). Der Umfang beträgt 6 bis 12 ECTS für die Bachelorarbeit und 15 bis 30 ECTS für die Masterarbeit, mit Ausnahmen in künstlerischen Studiengängen (§ 8 Abs. 3) [\[1\]](#content-quellen).

Kurzes Glossar für internationale Studierende:

- **Betreuer bzw. Betreuerin:** begleitet Sie fachlich, meist eine Professorin, ein Professor oder eine wissenschaftliche Mitarbeiterin bzw. ein Mitarbeiter.
- **Erst- und Zweitprüfer:** bewerten die Arbeit offiziell; der Erstprüfer ist oft zugleich Ihr Betreuer.
- **Prüfungsamt:** organisiert Anmeldung, Fristen, Abgabe und Noten.
- **Prüfungsausschuss:** entscheidet über Anträge, etwa Verlängerungen oder externe Arbeiten.
- **Bearbeitungszeit:** die offizielle Frist zwischen Themenausgabe und Abgabe.
- **Kolloquium / Disputation:** mündliche Präsentation und Verteidigung der Arbeit.
- **Sperrvermerk:** schränkt die Veröffentlichung einer Arbeit mit vertraulichen Unternehmensdaten ein.
- **Eidesstattliche Versicherung / Eigenständigkeitserklärung:** Ihre Erklärung, die Arbeit selbst verfasst und alle Hilfsmittel angegeben zu haben.
- **Exposé:** kurzer Projektplan mit Forschungsfrage, Methode und Zeitplan.

## Das erste Dokument: Ihre Prüfungsordnung

**⚖️ Regelung:** Eine bundesweit einheitliche Vorschrift gibt es nicht. Die Regeln entstehen in Ebenen: Landeshochschulgesetz, Studienakkreditierungsverordnung des Landes (auf Grundlage der KMK-Musterrechtsverordnung), allgemeine Prüfungsordnung der Hochschule und Prüfungsordnung Ihres Studiengangs.

Den Kernsatz dieses Artikels sollten Sie sich merken: **"Your Prüfungsordnung, faculty guidance and supervisor instructions prevail."** Auf Deutsch: Maßgeblich sind Ihre Prüfungsordnung, die Vorgaben Ihrer Fakultät und die Anweisungen Ihres Betreuers.

Oft ist die Ordnung zweigeteilt, etwa an der TUM in APSO und die FPSO des Studiengangs [\[7\]](#content-quellen) oder an der FU Berlin in Rahmen- und Fachordnung [\[16\]](#content-quellen). Die TH Köln nutzt Rahmenprüfungsordnungen als Vorlage, in die jeder Studiengang seine Werte einträgt [\[15\]](#content-quellen).

**💡 Praxistipp:** Sie finden die Ordnungen in den amtlichen Mitteilungen Ihrer Hochschule oder beim Prüfungsamt. Laden Sie die gültige Fassung samt Änderungssatzungen herunter und suchen Sie nach „Abschlussarbeit", „Bachelorarbeit" oder „Masterarbeit". Lesen Sie auch die Merkblätter Ihrer Fakultät.

## Wann Sie mit der Abschlussarbeit beginnen können

**⚖️ Regelung:** Zugelassen werden Sie erst, wenn Sie bestimmte Voraussetzungen erfüllen. Beispiele: Im B.Sc. Informatik der RWTH Aachen sind es 120 CP und Pflichtmodule, für jede Masterarbeit an der RWTH das Modul „Wissenschaftliche Integrität" [\[9\]](#content-quellen). Die LMU Informatik verlangt das Modul P 16.1 [\[8\]](#content-quellen), die FU Berlin im B.Sc. BWL 90 LP [\[16\]](#content-quellen), die Universität zu Köln im B.Sc. BWL 100 LP [\[14\]](#content-quellen), Frankfurt in den Wirtschaftswissenschaften Orientierungsphase, 18 CP und ein Seminar [\[13\]](#content-quellen). In vielen Studiengängen ist die Abschlussarbeit die letzte Prüfung, etwa im M.Sc. Informatik der TUM [\[7\]](#content-quellen), aber nicht in jedem Studiengang.

**💡 Praxistipp:** Prüfen Sie Ihren Notenspiegel etwa ein Semester vor dem geplanten Start. Fehlt ein Pflichtmodul, verschiebt sich die Anmeldung sonst oft um ein Semester.

## Wie Sie ein Thema finden

- **Eigener Vorschlag:** Sie entwickeln eine Forschungsfrage, oft aus einem Seminar, und suchen dafür eine Betreuung.
- **Ausgeschriebene Themen:** Viele Lehrstühle veröffentlichen offene Themen, teils in Themenbörsen wie an der TUM.
- **Forschungsgruppen:** Im Master oft Mitarbeit an einem laufenden Projekt, mit Daten, aber auch festen Erwartungen.
- **Unternehmen:** möglich, in den untersuchten Beispielen aber nur mit Betreuung durch die Hochschule (siehe unten).

**💡 Praxistipp:** Ein gutes Thema ist eng genug für die Bearbeitungszeit und passt zu Ihrem Berufsziel. Wer promovieren möchte, wählt eher ein forschungsnahes Thema; mehr dazu in unserem Beitrag zur [Promotion und Forschungskarriere](/de/blog/doing-a-phd-and-research-career-in-germany-as-a-foreigner-de).

## Betreuer für die Bachelorarbeit finden

**💡 Praxistipp:**

- Beginnen Sie zwei bis drei Monate vor der geplanten Anmeldung.
- Fragen Sie zuerst Lehrende, bei denen Sie schon ein Seminar belegt haben.
- Beachten Sie die Hinweise auf der Lehrstuhlseite: Viele Lehrstühle haben feste Bewerbungswege.
- Schreiben Sie eine kurze, konkrete E-Mail mit einer ersten Themenidee.

**Beispiel-E-Mail:**

> Betreff: Anfrage Betreuung Bachelorarbeit – [Themenidee]
>
> Sehr geehrte Frau Professorin [Name],
>
> ich studiere im 6. Semester [Studiengang] und habe Ihr Seminar [Titel] besucht. Ich würde meine Bachelorarbeit gern an Ihrem Lehrstuhl schreiben. Meine erste Idee: [Forschungsfrage in einem Satz]. Die Zulassungsvoraussetzungen erfülle ich voraussichtlich bis [Datum]. Ein einseitiges Exposé und meinen Notenspiegel habe ich angehängt.
>
> Hätten Sie in den nächsten Wochen Zeit für ein kurzes Gespräch?
>
> Mit freundlichen Grüßen
> [Vorname Nachname], Matrikelnummer [Nummer]

Verlangt der Lehrstuhl eine kurze Bewerbung, hilft ein [akademischer Lebenslauf](/de/templates/lebenslauf-akademisch).

## Betreuer, Prüfer und Prüfungsamt: wer was macht

**⚖️ Regelung:** An der [TUM](/de/universities/technische-universitat-munchen-partner-019ddbba) bewertet im Beispiel Informatik die Person, die das Thema gestellt hat; ein Zweitprüfer kommt nur hinzu, wenn die Arbeit nicht bestanden würde [\[7\]](#content-quellen). Ähnlich ist es an der [LMU](/de/universities/ludwig-maximilians-universitat-munchen-q55044) Informatik [\[8\]](#content-quellen). In den meisten anderen untersuchten Beispielen, etwa an der [RWTH Aachen](/de/universities/rwth-aachen-university-partner-019de9ee), am [KIT](/de/universities/karlsruher-institut-fur-technologie-q309988), an der TU Berlin, in Frankfurt, Köln und Hamburg, bewerten zwei Prüfer.

**💡 Praxistipp:** Klären Sie früh, wer Zweitprüfer wird.

## Die Abschlussarbeit anmelden

Wie Sie die Abschlussarbeit anmelden, ist unterschiedlich: An der TUM läuft die Anmeldung etwa über das Portal der jeweiligen School [\[7\]](#content-quellen); anderswo füllen Sie ein Formular aus, das Betreuer und teils der Prüfungsausschuss unterschreiben, bevor das Prüfungsamt das Thema offiziell ausgibt.

**⚖️ Regelung:** Die Anmeldung legt meist Thema, Prüfer und Sprache fest. Für externe Arbeiten verlangen viele Ordnungen eine zusätzliche Genehmigung, etwa in Frankfurt, an der TH Köln, am KIT und an der RWTH (dort nur ausnahmsweise) [\[9\]](#content-quellen)[11][13][15].

**💡 Praxistipp:** Bewahren Sie die Bestätigung mit dem Ausgabedatum auf. Dieses Datum bestimmt Ihre Abgabefrist.

## Wann die Bearbeitungszeit beginnt

**⚖️ Regelung:** In den untersuchten Hochschulen beginnt die Bearbeitungszeit mit dem offiziell festgehaltenen Ausgabe- bzw. Anmeldedatum. Die Dauer variiert stark: neun Wochen für die Bachelorarbeit in den Wirtschaftswissenschaften in Frankfurt und Hamburg, 20 Wochen im B.Sc. Informatik der TU Berlin. Auch die Bearbeitungszeit der Masterarbeit unterscheidet sich: 19 Wochen an der LMU Informatik, 26 Wochen im M.Sc. Computer Science der TU Berlin, sechs Monate in der Informatik an TUM und KIT.

Nur die Goethe-Universität Frankfurt (WiWi) **verbietet ausdrücklich**, vor der offiziellen Themenausgabe an der Arbeit zu arbeiten [\[13\]](#content-quellen). Die anderen untersuchten Ordnungen regeln das nicht ausdrücklich; die offizielle Frist läuft aber immer ab dem festgehaltenen Ausgabedatum.

**💡 Praxistipp:** Klären Sie mit Ihrem Betreuer, welche Vorarbeiten vor der Anmeldung sinnvoll und zulässig sind, etwa Literaturrecherche oder Exposé.

**⚖️ Regelung:** In den meisten der von uns untersuchten Hochschulen kann das Thema einmal innerhalb einer frühen Frist zurückgegeben werden (Rückgabe des Themas), etwa an der LMU, in Hamburg und an der TH Köln innerhalb von zwei Wochen, am KIT im ersten Monat [\[8\]](#content-quellen)[11][12][15]. Dieses Recht besteht aber nicht in jedem Studiengang; im Beispiel Köln WiSo konnten wir ein solches freies Rückgaberecht nicht verifizieren.

## Zusammenarbeit mit dem Betreuer

**💡 Praxistipp:** Vereinbaren Sie zu Beginn, wie oft Sie sich treffen, wann Sie Zwischenstände schicken und ob der Betreuer Entwürfe liest. Ein Treffen alle zwei bis vier Wochen ist für viele ein sinnvoller Rhythmus; eine kurze Zusammenfassung per E-Mail danach dokumentiert Ihren Fortschritt.

**⚖️ Regelung:** An der Universität zu Köln (WiSo) ist für Abschlussarbeiten, die ab dem 1. Oktober 2025 beginnen, eine Fortschrittsdokumentation verpflichtend [\[14\]](#content-quellen).

Wenn Sie sich in dieser Phase isolieren, helfen die Anlaufstellen in unserem Beitrag zu [Einsamkeit und mentaler Gesundheit](/de/blog/einsamkeit-mentale-gesundheit-internationale-studierende-deutschland).

## Quellen, Zitieren und Plagiat

**⚖️ Regelung:** Die DFG-Leitlinien zur Sicherung guter wissenschaftlicher Praxis (Kodex, Version 1.2, 2024) verlangen, Originalquellen zu zitieren (Leitlinie 7). Plagiat und Datenfälschung sind wissenschaftliches Fehlverhalten (Leitlinie 19), Autorschaft setzt einen echten eigenen Beitrag voraus (Leitlinie 14) [\[2\]](#content-quellen).

Die Erklärung, die Sie unterschreiben, heißt unterschiedlich: Die Universität Hamburg (WiSo), die Universität zu Köln (WiSo) und die RWTH verlangen eine **Eidesstattliche Versicherung** („Versicherung an Eides statt"), Frankfurt, die TU Berlin und die FU Berlin eine **Eigenständigkeitserklärung** bzw. Selbstständigkeitserklärung.

Ob es rechtlich eine echte eidesstattliche Versicherung ist, hängt vom Bundesland ab. In Nordrhein-Westfalen dürfen Hochschulen solche Versicherungen abnehmen (§ 63 Abs. 5 HG NRW) [\[4\]](#content-quellen); eine falsche Versicherung kann dann nach § 156 StGB strafbar sein [\[5\]](#content-quellen). NRW erlaubt zudem Geldbußen bis zu 50.000 € bei vorsätzlicher Täuschung und die Exmatrikulation bei wiederholter oder schwerwiegender Täuschung [\[4\]](#content-quellen).

**⚖️ Regelung:** Typische Folge von Plagiat oder Täuschung ist „nicht bestanden" (5,0), in schweren oder wiederholten Fällen ist eine Exmatrikulation möglich. Die Universität Hamburg wertet Ghostwriting in der Regel als besonders schwere Täuschung; unerlaubte KI-Nutzung allein ist dort nicht automatisch ein besonders schwerer Fall [\[17\]](#content-quellen). Wird eine Täuschung später entdeckt, kann der Abschluss innerhalb der in der Ordnung festgelegten Fristen aberkannt werden.

## KI und ChatGPT in der Abschlussarbeit

**"Your Prüfungsordnung, faculty guidance and supervisor instructions prevail."** Auf Deutsch: Ihre Prüfungsordnung, die Vorgaben Ihrer Fakultät und die Anweisungen Ihres Betreuers sind maßgeblich.

KI ist in Deutschland weder allgemein verboten noch frei erlaubt. Der gemeinsame Trend in den von uns untersuchten Hochschulen ist die transparente Offenlegung der verwendeten KI-Tools und die volle Verantwortung der Studierenden für den Inhalt. Die verbindliche Regel unterscheidet sich aber je nach Hochschule, Fakultät, Prüfungsordnung und Betreuer. Die DFG betont in ihrer Stellungnahme von 2023 (für Forschende, nicht für studentische Prüfungen): KI entbindet nicht von Verantwortung, Modelle und Zweck sind offenzulegen, Autoren können nur natürliche Personen sein [\[3\]](#content-quellen).

**⚖️ Regelung – Beispiele:**

- **TU Berlin:** Die Eigenständigkeitserklärung fragt nach Produktname, Hersteller, Version und Art der Nutzung; empfohlen wird, Prompts und Chats zu dokumentieren [\[10\]](#content-quellen).
- **Goethe-Universität Frankfurt (WiWi):** Ihr eigener Anteil muss überwiegen, KI-gestützte Stellen sind zu kennzeichnen, auch Ideengenerierung ist offenzulegen; verlangt wird eine KI-Erklärung als Verzeichnis (Tool, Version, Nutzung, Stellen). Rechtschreibprüfung, Literaturverwaltung und Statistiksoftware müssen nicht genannt werden [\[13\]](#content-quellen).
- **KIT (Informatik):** Tools für Rechtschreibung, Grammatik und Übersetzung ohne Angabe; andere generative KI ist offenzulegen (Zweck, Teile, Modelle) und vorab mit dem Betreuer abzustimmen [\[11\]](#content-quellen).
- **RWTH Aachen:** keine einheitliche Kennzeichnungsregel; die Eidesstattliche Versicherung erfasst ausdrücklich „Software und Dienste zur Sprach-, Text- und Medienproduktion" [\[9\]](#content-quellen).
- **TH Köln:** Offenlegung „grundsätzlich ja"; ganze Texte oder wesentliche Teile generieren zu lassen, ist nicht zulässig; Details klären Sie mit den Lehrenden [\[20\]](#content-quellen).
- **Universität Hamburg:** Generative KI ist bei unbeaufsichtigten Arbeiten grundsätzlich erlaubt; nicht gekennzeichnete, nicht triviale KI-Anteile sind unzulässig, die Tools sind aufzulisten [\[12\]](#content-quellen).

Nicht offengelegte oder unerlaubte Nutzung wird etwa an der TH Köln als Täuschung behandelt [\[20\]](#content-quellen). Die Universität Leipzig warnt vor erfundenen Quellen und verdecktem Plagiat [\[21\]](#content-quellen). In Frankfurt gilt Detektor-Software nur als unterstützendes Indiz; ein Verdacht führt zu einer Anhörung oder mündlichen Überprüfung.

**💡 Praxistipp:**

- Führen Sie ab dem ersten Tag ein KI-Protokoll: Tool, Version, Datum, Zweck, Prompt und Verwendung des Ergebnisses.
- Prüfen Sie jede Quelle selbst im Original.
- Geben Sie keine personenbezogenen, vertraulichen, urheberrechtlich geschützten oder Unternehmensdaten in öffentliche KI-Tools ein [\[10\]](#content-quellen)[20].
- Sprechen Sie die KI-Nutzung im ersten Betreuungsgespräch an und halten Sie die Absprache schriftlich fest.

## Masterarbeit im Unternehmen

Eine Masterarbeit (oder Bachelorarbeit) im Unternehmen ist in vielen Studiengängen möglich; die akademische Verantwortung bleibt aber bei der Hochschule.

**⚖️ Regelung:** In den untersuchten Beispielen stellt oder genehmigt ein Prüfer der Hochschule das Thema und vergibt die Note. Der Betreuer im Unternehmen kann beraten und Vorschläge machen, legt aber die Note nicht fest (etwa Frankfurt, LMU Informatik, TUM, RWTH) [\[7\]](#content-quellen)[8][9][13]. An der TUM geht das nur mit einem TUM-Prüfer, an der RWTH nur ausnahmsweise mit Betreuung durch die RWTH. Frankfurt und die TH Köln verlangen eine Genehmigung des Prüfungsausschusses; an der FU Berlin muss die Studiengangsordnung externe Arbeiten zulassen.

**Vergütung und Sozialversicherung:** Wer im Unternehmen nur seine Abschlussarbeit schreibt, ist nach einem Urteil des Bundessozialgerichts von 1993 in der Regel kein Arbeitnehmer (so die Krankenkasse SBK). Leisten Sie zusätzlich produktive Arbeit, gelten die normalen Regeln für Beschäftigung. Mehr dazu: [HiWi oder Werkstudent](/de/blog/hiwi-vs-werkstudent-student-jobs-in-germany-de).

**140 Tage für Studierende aus Nicht-EU-Staaten:** Eine bundesweite Regelung haben wir nicht gefunden. Internationale Büros, etwa der Universität Potsdam, erklären, dass eine im Unternehmen geschriebene Abschlussarbeit Teil des Studiums ist und nicht auf das 140-Tage-Konto angerechnet wird [\[19\]](#content-quellen). **💡 Praxistipp:** Lassen Sie sich das von Ihrer Ausländerbehörde bestätigen, bevor Sie unterschreiben; zusätzliche produktive Arbeit zählt als Beschäftigung. Ob Sie parallel Vollzeit arbeiten können, beantwortet unsere [FAQ zum Arbeiten während der Abschlussarbeit](/de/faq/is/almanyada-lisans-tezi-yazarken-tam-zamanli-calismak-mumkun-mudur-de).

## NDA, Sperrvermerk und geistiges Eigentum

**⚖️ Regelung:** Eine Geheimhaltungsvereinbarung (NDA) ist ein privater Vertrag zwischen Ihnen und dem Unternehmen. Sie darf nicht verhindern, dass die Prüfer, auch in einem Widerspruchsverfahren, Ihre Arbeit lesen und überprüfen [\[18\]](#content-quellen); Professorinnen und Professoren sind ohnehin zur Verschwiegenheit verpflichtet (§ 203 StGB). An der Universität zu Köln (WiSo) legen Sie eine NDA dem Themensteller offen, bevor das Thema festgelegt wird; mit Sperrvermerk ist eine Veröffentlichung nur mit Zustimmung möglich [\[14\]](#content-quellen).

Die Regeln zum Sperrvermerk unterscheiden sich je nach Studiengang. Die TH Köln (Fakultät 10) verlangt den Vermerk auf jedem Exemplar. In Informatik und Computer Science der TU Berlin ist ein Sperrvermerk dagegen **nicht** zulässig; über die übliche Verschwiegenheit hinaus gibt es dort keine Vertraulichkeit [\[10\]](#content-quellen).

**Urheberrecht und Erfindungen:** Sie sind Urheber Ihrer Arbeit; ein Unternehmen erhält Nutzungsrechte nur per Vertrag. Studierende, die nicht an der Hochschule angestellt sind, gelten als „freie Erfinder" [\[22\]](#content-quellen). Sind Sie beim Unternehmen angestellt, etwa als Werkstudent, kann das Arbeitnehmererfindungsrecht greifen. Prüfen Sie Ihren Vertrag.

**💡 Praxistipp:** Zeigen Sie NDA und Vertrag vor der Anmeldung Ihrem Betreuer, und geben Sie Unternehmensdaten nie in öffentliche KI-Tools ein.

## Sprache der Abschlussarbeit

**⚖️ Regelung:** Deutsch oder Englisch ist in vielen Studiengängen üblich; andere Sprachen meist nur mit vorab beantragter Genehmigung. Manche verlangen eine deutsche Zusammenfassung, etwa Frankfurt (WiWi) bei englischen Arbeiten und die TU Berlin, wenn die Arbeit nicht in der Unterrichtssprache verfasst ist [\[10\]](#content-quellen)[13].

**💡 Praxistipp:** Wählen Sie die Sprache, in der Sie am präzisesten argumentieren.

## Abgabe der Abschlussarbeit

**⚖️ Regelung:** Einige untersuchte Hochschulen verlangen nur die digitale Abgabe (TUM über das CIT-Portal, RWTH, Uni Hamburg, Köln WiSo per PDF). Andere wollen gebundene Exemplare, etwa Frankfurt, die FU Berlin (BWL), die TH Köln und die LMU; an der TU Berlin ist rein elektronische Abgabe möglich, wenn beide Prüfer zustimmen [\[10\]](#content-quellen). Dazu gehört die unterschriebene Erklärung, oft inklusive KI-Erklärung. Wer die Frist ohne anerkannten Grund versäumt, erhält in den untersuchten Beispielen in der Regel „nicht bestanden" (5,0).

**💡 Praxistipp:** Planen Sie für Druck und Bindung zwei bis drei Werktage ein und reichen Sie nicht in der letzten Stunde ein.

## Kolloquium und Disputation

**⚖️ Regelung:** Ein mündlicher Teil ist in einigen untersuchten Studiengängen Pflicht, etwa an RWTH, KIT, LMU Informatik (Disputation) und TH Köln (ca. 30 Minuten, benotet) [\[8\]](#content-quellen)[9][11][15]. In Hamburg (WiSo) ist keiner vorgeschrieben, in Köln (WiSo) nur in einigen Studiengängen.

**💡 Praxistipp:** Bereiten Sie eine kurze Präsentation vor und üben Sie Antworten zu Methode, Grenzen und offenen Fragen Ihrer Arbeit.

## Bewertung und Note

**⚖️ Regelung:** Bei zwei Prüfern ergibt sich die Note meist aus beiden Gutachten. Weichen sie stark ab, kommt ein dritter Prüfer hinzu: an der RWTH bei mehr als 2,0 Abstand oder bestanden gegen nicht bestanden, in Köln (WiSo) bei mehr als 1,0 Abstand oder einer 5,0, an der TH Köln ab 2,0 Abstand [\[9\]](#content-quellen)[14][15]. Die Korrekturzeit beträgt zum Beispiel in Hamburg (WiSo) sechs Wochen (Bachelor) bzw. drei Monate (Master), in Frankfurt (WiWi) sechs Wochen [\[12\]](#content-quellen)[13].

**💡 Praxistipp:** Rechnen Sie die Korrekturzeit in Ihren Zeitplan ein. Wie Ihre Note international einzuordnen ist, zeigt unser [Notenrechner](/de/tools/grade-converter).

## Wenn die Abschlussarbeit nicht bestanden ist

**⚖️ Regelung:** In den meisten untersuchten Hochschulen können Sie eine nicht bestandene Abschlussarbeit einmal wiederholen, teils mit neuem Thema (TUM, Frankfurt). Die Regeln der TU Berlin erlauben zwei Wiederholungen. In Hamburg ist eine zweite Wiederholung nur ausnahmsweise möglich; an der RWTH melden Sie die Wiederholung innerhalb von drei Semestern an; in Köln (WiSo) folgt danach das endgültige Nichtbestehen [\[9\]](#content-quellen)[12][14].

Endgültiges Nichtbestehen führt in der Regel zur Exmatrikulation, was für Nicht-EU-Studierende auch den Aufenthaltstitel betrifft. Details erklärt unser Beitrag zur [Exmatrikulation](/de/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-de).

## Krankheit und Verlängerung

**⚖️ Regelung:** Verlängerungen sind möglich, aber begrenzt und an Gründe gebunden. Einige Beispiele (weitere in der Tabelle unten):

- **TUM:** bis zur Hälfte der Bearbeitungszeit bei Gründen, die Sie nicht zu vertreten haben; ein ärztliches Attest hemmt die Frist [\[7\]](#content-quellen).
- **Uni Hamburg (WiSo):** plus zwei bzw. drei Wochen, mehr nur bei besonderer Härte; verlangt wird ein qualifiziertes ärztliches Attest [\[12\]](#content-quellen).
- **TU Berlin:** für die Dauer des Hindernisses, begrenzt durch den Studiengang; Attest binnen fünf Tagen [\[10\]](#content-quellen).
- **FU Berlin (BWL):** um die Dauer der Krankheit, mit amtsärztlichem Attest [\[16\]](#content-quellen).
- **TH Köln:** höchstens zwei Wochen; bei längerer Krankheit Rücktritt und Neustart mit neuem Thema, ohne einen Versuch zu verlieren [\[15\]](#content-quellen).
- **LMU Informatik:** in der PStO nicht ausdrücklich geregelt [\[8\]](#content-quellen).

Bei Behinderung oder chronischer Erkrankung fragen Sie beim Prüfungsamt nach einem **Nachteilsausgleich**; die Einzelheiten regelt Ihre Hochschule.

**💡 Praxistipp:** Beantragen Sie Verlängerungen sofort und schriftlich, und klären Sie, welches Attest verlangt wird. Bei längerem Ausfall kann ein [Urlaubssemester](/de/blog/urlaubssemester-taking-a-semester-off-in-germany-de) sinnvoll sein; dafür gibt es unseren [Musterantrag](/de/templates/urlaubssemester-antrag).

## Abschluss und Aufenthaltstitel

**⚖️ Regelung:** In den meisten der von uns untersuchten Hochschulen ist das Zeugnisdatum an die letzte Prüfung bzw. letzte Prüfungsleistung gekoppelt; die Details unterscheiden sich je nach Hochschule und Studiengang. An der TUM ist es der Tag, an dem alle Prüfungen abgeschlossen sind (bzw. der Kolloquiumstermin, wenn dieser zuletzt liegt); in Frankfurt und Köln kann es das Abgabedatum sein, wenn die Arbeit die letzte Prüfung ist.

Zur Einschreibung: An der TUM bleiben Sie bis zur Abgabe eingeschrieben, in Frankfurt während der gesamten Bearbeitung. In Köln müssen Sie sich nicht zurückmelden, wenn Sie die letzte Prüfung im laufenden Semester ablegen.

**⚖️ Regelung:** Nach erfolgreichem Abschluss ist nach § 20 AufenthG eine Aufenthaltserlaubnis zur Arbeitsplatzsuche für bis zu 18 Monate möglich; jede Erwerbstätigkeit ist erlaubt, eine Verlängerung nicht (BAMF) [\[6\]](#content-quellen). Beantragen Sie sie mit Abschlussnachweis, bevor Ihr aktueller Titel abläuft.

**💡 Praxistipp:** Fragen Sie das Prüfungsamt früh nach einer vorläufigen Abschlussbescheinigung. Mehr im [Leitfaden zum Job-Seeker-Visum](/de/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-de), in der [FAQ zur Dauer der Jobsuche](/de/faq/is/mezuniyet-sonrasi-is-arama-vizesi-jobsuche-suresi-nedir-de) und zum [Zweckwechsel in einen Arbeitstitel](/de/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-de).

## 10 Hochschulen im Vergleich

| Hochschule / Studiengangsbeispiel | Voraussetzung | Bearbeitungszeit | Verlängerung | Abgabe | Kolloquium | Prüfer | Wiederholung | KI-Regel |
|---|---|---|---|---|---|---|---|---|
| TUM, Informatik | je Studiengang (M.Sc.: meist letzte Prüfung) | B: 4 Mon., M: 6 Mon. | bis zur Hälfte; Attest hemmt Frist | elektronisch (CIT-Portal) | je Studiengang (M.Sc.: Präsentation, unbenotet) | Themensteller; 2. nur bei Nichtbestehen | 1, neues Thema | transparent; Lehrende entscheiden |
| LMU, Informatik | Modul P 16.1 (B.Sc.) | B: 14 Wo., M: 19 Wo. | in PStO nicht ausdrücklich geregelt | B: 2 Exemplare, M: 1 | Disputation Pflicht (3/5 ECTS) | Betreuer; 2. nur bei Nichtbestehen | 1 | fakultätsbezogen |
| RWTH Aachen, Informatik | B.Sc.: 120 CP + Pflichtmodule | max. 3/6 Mon.; B.Sc. Informatik 4 Mon. | +4 Wo. (B) / +6 Wo. (M) | elektronisch (RWTHonline) | mündlicher Teil Pflicht | 2; 3. bei > 2,0 Abstand | 1 (binnen 3 Semestern) | keine einheitliche Kennzeichnung |
| TU Berlin, Informatik | 120 LP (B.Sc.) | B: 20 Wo., M (CS): 26 Wo. | Dauer des Hindernisses, gedeckelt | 2 gebunden + digital, oder rein elektronisch bei Zustimmung beider Prüfer | nicht in Informatik | 2 | 2 | Produkt, Version, Nutzung angeben |
| KIT, Informatik | 120 LP (B) / 60 LP (M) | 4 / 6 Mon. | +1 Mon. (B) / +3 Mon. (M) | nach Absprache | Präsentation Pflicht | 2 | 1 | Sprachtools frei; sonst offenlegen |
| Uni Hamburg, WiSo | BWL: 120 ECTS + Seminararbeit | B: 9 Wo.; M: je Studiengang | +2 Wo. (B) / +3 Wo. (M) | nur digital | nicht erforderlich | 2 | 1 (2. ausnahmsweise) | erlaubt, Tools auflisten |
| Goethe-Uni Frankfurt, WiWi | Orientierungsphase + 18 CP + Seminar | B: 9 Wo.; M: 3–6 Mon. | max. +50 %, WiWi max. 32 Tage | 1 gebunden + USB | optional | 2 | 1, neues Thema | KI-Verzeichnis |
| Uni Köln, WiSo | B.Sc. BWL: 100 LP | B: max. 12 Wo.; M: max. 6 Mon. | +4 Wo. (B) / +2 Mon. (M) | PDF (WiSo-Postfach) | nur einige Studiengänge | 2; 3. bei > 1,0 Abstand | 1, dann endgültig | Erklärung umfasst KI-Inhalte |
| TH Köln | LP je Studiengang | 4 Wo.–4 Mon. | max. +2 Wo. | 1 gebunden + digital | ca. 30 Min., benotet | 2; 3. ab 2,0 Abstand | 1 | Offenlegung grundsätzlich ja |
| FU Berlin, BWL | 90 LP | 12 Wo. | Dauer der Krankheit | 2 gebunden + Datei | optional | 2 | 1 | fakultätsbezogen |

> Diese Beispiele beziehen sich auf bestimmte Studiengänge oder Prüfungsordnungen. Ihre eigene Prüfungsordnung kann abweichen. *(These examples refer to specific programmes or examination regulations. Your own Prüfungsordnung may differ.)*

*Dokumente geprüft im September 2026; TU Berlin auf Grundlage der Hinweise des Prüfungsamts (03/2026), TH Köln auf Grundlage der Rahmenprüfungsordnung 2023.*

## Beispielhafter Ablauf

Dieser **Beispielablauf** ist eine praktische Orientierung, keine offizielle Vorgabe; die Zeitangaben sind grobe Richtwerte.

1. **Prüfungsordnung lesen** (etwa ein Semester vorher).
2. **Zulassung prüfen:** Leistungspunkte und Pflichtmodule abgleichen.
3. **Themenideen sammeln** (3–4 Monate vor der Anmeldung).
4. **Betreuer kontaktieren** (2–3 Monate vor der Anmeldung).
5. **Exposé schreiben** und abstimmen.
6. **Bei externer Arbeit:** Genehmigung, NDA und Sperrvermerk klären.
7. **Zweitprüfer und Sprache festlegen.**
8. **Abschlussarbeit anmelden:** Ab dem Ausgabedatum läuft die Bearbeitungszeit.
9. **Literaturrecherche und Gliederung** (erste Wochen).
10. **Regelmäßige Treffen** und Fortschritt dokumentieren.
11. **Schreiben**, KI-Nutzung protokollieren, Quellen prüfen.
12. **Überarbeiten und Formalia** (letzte 2–3 Wochen).
13. **Erklärung unterschreiben und abgeben** (einige Tage vor Fristende).
14. **Kolloquium bzw. Disputation**, falls vorgesehen.
15. **Note, Abschlussbescheinigung und Aufenthalt** klären.

## Vier typische Situationen

### Bachelorarbeit mit akademischem Thema

- **Zuerst prüfen:** Zulassung, Bearbeitungszeit, Abgabeform.
- **Sprechen mit:** einer Lehrperson, deren Seminar Sie besucht haben.
- **Vorbereiten:** Themenidee, einseitiges Exposé, Notenspiegel.
- **Größtes Risiko:** ein zu breites Thema für eine kurze Frist.

### Masterarbeit in einer Forschungsgruppe

- **Zuerst prüfen:** Bearbeitungszeit der Masterarbeit, Pflicht zu Kolloquium oder Präsentation.
- **Sprechen mit:** Professor und betreuendem Mitarbeiter über Erwartungen und Daten.
- **Vorbereiten:** Methodenkenntnisse, Exposé, Zeitplan mit Meilensteinen.
- **Größtes Risiko:** Daten oder Experimente verzögern sich. Wer danach promovieren will, kann ein [Motivationsschreiben für die Promotion](/de/templates/motivationsschreiben-phd) vorbereiten.

### Masterarbeit im Unternehmen

- **Zuerst prüfen:** ob externe Arbeiten zulässig sind und welche Genehmigung nötig ist.
- **Sprechen mit:** einem Prüfer der Hochschule vor der Zusage an das Unternehmen; bei Nicht-EU-Staatsangehörigkeit mit der Ausländerbehörde.
- **Vorbereiten:** Vertrag, NDA, Sperrvermerk, Nutzungsrechte, Vergütung.
- **Größtes Risiko:** Unternehmensinteressen kollidieren mit wissenschaftlichen Anforderungen.

### Letztes Semester mit knappem Abschlussdatum

- **Zuerst prüfen:** Zeugnisdatum, Korrekturzeit, Einschreibung, Ablauf des Aufenthaltstitels.
- **Sprechen mit:** Prüfungsamt und Betreuer über realistische Termine.
- **Vorbereiten:** Pufferzeit, früher Antrag auf eine vorläufige Abschlussbescheinigung.
- **Größtes Risiko:** Korrektur und Kolloquium verschieben den Abschluss über ein wichtiges Datum hinaus. Zum Übergang in den Beruf lesen Sie unseren Beitrag zum [Arbeitsmarkt nach dem Studium](/de/blog/deutscher-arbeitsmarkt-nach-studium-diplom-kein-job).

## Checkliste

**Vor der Anmeldung**

- Prüfungsordnung in aktueller Fassung gelesen
- Zulassungsvoraussetzungen geprüft
- Betreuer gefunden
- Thema abgestimmt
- Sprache festgelegt
- bei externer Arbeit bzw. im Unternehmen: Genehmigung eingeholt
- NDA und geistiges Eigentum geprüft

**Nach der Anmeldung**

- offizielle Abgabefrist notiert
- Treffrhythmus vereinbart
- Literaturrecherche gestartet
- Zitierweise festgelegt
- Datensicherung eingerichtet
- KI-Nutzung dokumentiert

**Vor der Abgabe**

- Formatierung geprüft
- Abgabeanforderungen erfüllt
- Erklärung unterschrieben
- eigene Plagiatsprüfung durchgeführt
- Dateiformat geprüft
- gedruckte Exemplare gebunden
- im Upload-Portal eingereicht
- Frist eingehalten
- Kolloquium vorbereitet

## FAQ

### Wie lange dauert die Bearbeitungszeit einer Masterarbeit?

Das hängt von Ihrer Prüfungsordnung ab. In den untersuchten Beispielen reicht die Spanne von 19 Wochen (LMU Informatik) und 26 Wochen (TU Berlin, M.Sc. Computer Science) bis zu sechs Monaten, etwa an TUM und KIT in der Informatik oder als Höchstgrenze in Köln (WiSo). In Frankfurt (WiWi) liegt der Rahmen bei drei bis sechs Monaten. Die Frist läuft ab dem offiziellen Ausgabedatum.

### Darf ich vor der offiziellen Anmeldung schon an der Arbeit schreiben?

Das ist unterschiedlich geregelt. Die Goethe-Universität Frankfurt (WiWi) verbietet es ausdrücklich. Die anderen untersuchten Ordnungen regeln das nicht ausdrücklich; die offizielle Bearbeitungszeit beginnt aber immer mit dem festgehaltenen Ausgabedatum. Klären Sie mit Ihrem Betreuer, welche Vorarbeiten wie Literaturrecherche oder Exposé zulässig sind.

### Darf ich ChatGPT für meine Abschlussarbeit nutzen?

Das entscheiden Prüfungsordnung, Fakultät und Betreuer. In den untersuchten Hochschulen ist KI weder allgemein verboten noch frei erlaubt. Der gemeinsame Trend ist die transparente Offenlegung der Tools und Ihre volle Verantwortung für den Inhalt; manche verlangen ein Verzeichnis mit Tool, Version und Zweck. Nicht offengelegte Nutzung kann als Täuschung gelten. Sprechen Sie das Thema früh an.

### Was passiert, wenn ich die Abgabefrist verpasse?

In den untersuchten Beispielen gilt eine ohne anerkannten Grund verspätete Arbeit in der Regel als „nicht bestanden" (5,0); dann greift die Wiederholungsregel Ihrer Ordnung. Sind Sie krank, beantragen Sie die Verlängerung sofort mit dem verlangten Attest. Manche Hochschulen fordern ein qualifiziertes oder amtsärztliches Attest oder setzen kurze Fristen, an der TU Berlin etwa fünf Tage.

### Wer bewertet meine Masterarbeit im Unternehmen?

Die Note vergibt ein Prüfer der Hochschule, nicht das Unternehmen. Der Betreuer im Unternehmen kann beraten und Vorschläge machen, legt in den untersuchten Beispielen aber nicht die Note fest. Das Thema stellt oder genehmigt ein Prüfer der Hochschule, oft mit Zustimmung des Prüfungsausschusses. Eine NDA darf nicht verhindern, dass die Prüfer Ihre Arbeit lesen.

### Kann ich nach der Abschlussarbeit in Deutschland bleiben?

Ja. Nach erfolgreichem Abschluss ist nach § 20 AufenthG eine Aufenthaltserlaubnis zur Arbeitsplatzsuche für bis zu 18 Monate möglich. In dieser Zeit dürfen Sie jede Tätigkeit ausüben, verlängern lässt sich der Titel nicht. Sie brauchen einen Abschlussnachweis. Stellen Sie den Antrag, bevor Ihr aktueller Titel abläuft, und fragen Sie früh nach einer Bescheinigung.

## Quellen

1. KMK, Musterrechtsverordnung (21.11.2024) — https://www.kmk.org/fileadmin/Dateien/veroeffentlichungen_beschluesse/2024/2024_11_21-Musterrechtsverordnung.pdf
2. DFG, Leitlinien zur Sicherung guter wissenschaftlicher Praxis (Kodex v1.2, 2024) — https://www.dfg.de/de/grundlagen-themen/grundlagen-und-prinzipien-der-foerderung/gwp/kodex
3. DFG, Stellungnahme zu generativen Modellen (2023) — https://www.dfg.de/resource/blob/289674/ff57cf46c5ca109cb18533b21fba49bd/230921-stellungnahme-praesidium-ki-ai-data.pdf
4. § 63 HG NRW — https://lexmea.de/de/gesetz/hg-nrw/63
5. § 156 StGB — https://www.gesetze-im-internet.de/stgb/__156.html
6. § 20 AufenthG — https://www.gesetze-im-internet.de/aufenthg_2004/__20.html ; BAMF, Hochschulabsolventen — https://www.bamf.de/EN/Themen/MigrationAufenthalt/ZuwandererDrittstaaten/Arbeit/Hochschulabsolvent/hochschulabsolvent-node.html
7. TUM, APSO (2011, in der Fassung von 2024) — https://www.edu.sot.tum.de/fileadmin/w00bed/edu/Documents/Studium/APSO/Lesb.F._APSO_vom_18.03.2011_mit_11._AES_vom_17.12.2024.pdf ; TUM, Formalitäten Abschlussarbeiten — https://www.tum.de/en/studies/graduation/theses/formalities
8. LMU, PStO B.Sc. Informatik (2022) — https://cms-cdn.lmu.de/media/contenthub/amtliche-veroeffentlichungen/1569-16in-ba-nf60-2022-ps00.pdf
9. RWTH, Übergreifende Prüfungsordnung (in der Fassung von 2025) — https://www.rwth-aachen.de/global/show_document.asp?id=aaaaaaaaddqknak ; RWTH, FAQ KI in Prüfungen (2024) — https://cls.rwth-aachen.de/global/show_document.asp?id=aaaaaaaacocblkv
10. TU Berlin Prüfungsamt, Hinweise zur Erstellung von Bachelor- und Masterarbeiten (03/2026) — https://www.static.tu.berlin/fileadmin/www/10002461/Pruefungsamt/Formulare_Bescheide/Abschlussarbeit_Hinweise_03.26.pdf
11. KIT, SPO B.Sc. Informatik (2022) — https://www.sle.kit.edu/downloads/AmtlicheBekanntmachungen/2022_AB_034.pdf ; KIT Informatik, Leitfaden Generative KI — https://www.informatik.kit.edu/downloads/studium/Leitfaden_Generative_KI_Informatik.pdf
12. Universität Hamburg, PO WiSo B.Sc. (2024) — https://www.uni-hamburg.de/campuscenter/studienorganisation/ordnungen-satzungen/pruefungsordnungen/wirtschafts-und-sozialwissenschaften/20240508-po-wiso-bsc-82.pdf ; Orientierungsrahmen gKI (2025) — https://www.uni-hamburg.de/lehre-navi/lehrende/orientierungsrahmen-gki/orientierungsrahmen-gki.pdf
13. Goethe-Universität Frankfurt, PO B.Sc. Wirtschaftswissenschaften 2022 — https://www.wiwi.uni-frankfurt.de/fileadmin/studium/pruefungsorganisation/dateien/bsc_wirtschaftswissenschaften_po2022.pdf
14. Universität zu Köln, WiSo, Gemeinsame Prüfungsordnung (2025) — https://wiso.uni-koeln.de/sites/fakultaet/dokumente/PA/po/GPO_2025.pdf
15. TH Köln, Rahmenprüfungsordnungen (2023) — https://www.th-koeln.de/mam/downloads/deutsch/hochschule/amtlichemitteilungen/inkraftsetzungssatzung_rpoen_2023_final.pdf ; TH Köln, Abschlussarbeiten — https://www.th-koeln.de/studium/abschlussarbeiten_5336.php
16. FU Berlin, Rahmenstudien- und -prüfungsordnung (2013) — https://www.fu-berlin.de/service/zuvdocs/amtsblatt/2013/ab322013.pdf
17. Universität Hamburg, Handreichung Nr. 15 „Täuschung in der Prüfung" (2025) — https://www.uni-hamburg.de/uhh/organisation/praesidialverwaltung/studium-und-lehre/qualitaet-und-recht/handreichungen/dateien/handreichung-15-taeuschung-in-der-pruefung.pdf
18. HS Emden/Leer, Merkblatt externe Abschlussarbeiten (2024) — https://www.hs-emden-leer.de/fileadmin/user_upload/sta/Dokumente/Allgemein/Merkblatt_externe_Abschlussarbeiten_V11.pdf
19. Universität Potsdam, Arbeiten für internationale Studierende (2026) — https://www.uni-potsdam.de/de/international/incoming/students/arbeiten
20. TH Köln, Handout KI-Tools (Schreibzentrum) — https://www.th-koeln.de/mam/downloads/deutsch/studium/rundumsstudium/handout_ki-tools.pdf
21. Universität Leipzig, Kennzeichnung KI-generierter Inhalte — https://kb.el.uni-leipzig.de/books/generative-ki-in-der-hochschullehre/page/kennzeichnung-von-ki-generierten-inhalten
22. FU Berlin Technologietransfer, FAQ Arbeitnehmererfindungen — https://www.fu-berlin.de/forschung/technologietransfer/patente-und-lizenzen-01/faq/arbeitnehmer.html

Beispiele geprüft im September 2026 an 10 deutschen Hochschulen bzw. Fakultäten; Dokumente ändern sich, prüfen Sie immer die aktuelle Fassung Ihrer eigenen Ordnung. Allgemeine Informationen, keine Rechtsberatung.
MD;

        $variants = [
            'tr' => [
                'slug' => 'bachelor-and-master-thesis-process-in-germany',
                'title' => 'Almanya\'da Bachelor ve Master Tez Süreci Nasıl İşler?',
                'excerpt' => 'Almanya\'da tek tip bir tez süreci yok: Prüfungsordnung\'unuz belirler. Bachelorarbeit ve Masterarbeit için danışman bulma, kayıt, süre, YZ, şirkette tez ve teslim adımları; 10 üniversiteden örneklerle.',
                'meta_title' => 'Almanya\'da Tez Süreci: Bachelor ve Master Rehberi',
                'meta_description' => 'Almanya\'da tez süreci: danışman bulma, kayıt, Bearbeitungszeit, şirkette tez, YZ kuralları, teslim ve kolokyum. 10 üniversiteden gerçek örneklerle rehber.',
                'body' => $trBody,
            ],
            'en' => [
                'slug' => 'bachelor-and-master-thesis-process-in-germany-en',
                'title' => 'How the Bachelor\'s and Master\'s Thesis Works in Germany',
                'excerpt' => 'There is no single thesis process in Germany. Learn how Bachelor and Master theses work, from finding a supervisor and registering to AI rules, company theses and grading, with real examples from 10 German universities.',
                'meta_title' => 'Thesis Process in Germany: Bachelor & Master Guide',
                'meta_description' => 'Thesis process in Germany: find a supervisor, register, AI rules, company theses, submission, grading and extensions, with examples from 10 universities.',
                'body' => $enBody,
            ],
            'de' => [
                'slug' => 'bachelor-and-master-thesis-process-in-germany-de',
                'title' => 'Bachelorarbeit und Masterarbeit: Ablauf von der Anmeldung bis zur Note',
                'excerpt' => 'Einen einheitlichen Ablauf gibt es nicht: Ihre Prüfungsordnung entscheidet. Anmeldung, Bearbeitungszeit, Betreuer, KI, Unternehmen, Abgabe und Note, mit Vergleichstabelle aus 10 Hochschulen und Checkliste.',
                'meta_title' => 'Abschlussarbeit Ablauf: Bachelor- und Masterarbeit',
                'meta_description' => 'Abschlussarbeit Ablauf: Anmeldung, Bearbeitungszeit, Betreuer, KI-Regeln, Abgabe, Kolloquium und Note, mit Beispielen aus 10 deutschen Hochschulen.',
                'body' => $deBody,
            ],
        ];

        // Slug başka bir yazıya aitse ezme: hiçbir şey yazmadan önce üç slug'ı da kontrol et.
        foreach ($variants as $v) {
            $foreign = Post::where('slug', $v['slug'])->where(fn ($q) => $q->whereNull('translation_group_id')->orWhere('translation_group_id', '!=', $groupId))->first();
            if ($foreign) {
                throw new RuntimeException("Tez süreci yazısı: '{$v['slug']}' slug'ı başka bir çeviri grubuna ait (#{$foreign->id}), hiçbir şey yazılmadı.");
            }
        }

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
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'bachelor-and-master-thesis-process-in-germany',
            'bachelor-and-master-thesis-process-in-germany-en',
            'bachelor-and-master-thesis-process-in-germany-de',
        ])->delete();
    }
};

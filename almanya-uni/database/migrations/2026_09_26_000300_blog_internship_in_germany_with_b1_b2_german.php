<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+EN+DE): B1/B2 Almanca ile Almanya'da staj — dürüst, sektöre bağlı cevap.
 *
 * Arama niyeti: "Almancam staj için yeter mi?". Karar matrisi, ilan dili çözücü, Pflicht/freiwillig,
 * 140 günlük Arbeitstagekonto, Mindestlohn, 30 günlük plan.
 *
 * Doğrulanmış kaynaklar (26.09.2026):
 *   - § 16b Abs. 1 S. 2 + Abs. 3 AufenthG (140 Arbeitstage; 20 saat yalnızca SAYMA yöntemi, ayrı sınır
 *     değil; Pflichtpraktikum sayılmaz), § 15 Nr. 2 BeschV, § 20 AufenthG; BA Fachliche Weisungen 12/2024;
 *     Stadt München KVR + LMU (freiwilliges Praktikum 140 güne sayılır; yerel uygulama farklı olabilir).
 *   - § 22 MiLoG + BMAS (13,90 €/saat 2026; 3 ay kuralı; mezuniyet sonrası istisna yok), § 2 Abs. 1a NachwG.
 *   - CEFR Companion Volume 2020 (B1/B2 resmî tanım; pratik yorum ayrıca işaretli).
 *   - Kendi pazar örneklemimiz: 45 staj ilanı, 26.09.2026 — temsilî değil, metinde uyarısı var.
 * Eski 120/240 kuralını içeren sayfalara bilinçli olarak link verilmedi (ayrı cleanup işi).
 * Yazar: Halil Yaprakli. Kategori: yasam.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '6d174f57-9f79-4a66-bd2a-80b86aa11585';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'yasam')->value('id')
            ?? DB::table('categories')->where('slug', 'kariyer')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Kısa cevap: Evet, Almanya'da B1 veya B2 Almanca ile staj bulabilirsiniz, ama her stajı değil. Sonucu belirleyen şey tek başına dil seviyeniz değil; sektör, yapacağınız işler, müşteriyle temas olup olmadığı, şirketin çalışma dili, stajın zorunlu mu gönüllü mü olduğu ve İngilizcenizin gücü. Bu rehberde "Almancam yeter mi?" sorusunu bu değişkenlere göre dürüstçe cevaplıyoruz ve B1 ile B2 seviyesi için ayrı bir başvuru stratejisi sunuyoruz.

> **Son güncelleme:** Eylül 2026 · Yasal bilgiler resmî kaynaklarla karşılaştırıldı (AufenthG, MiLoG, BMAS, Bundesagentur für Arbeit, Avrupa Konseyi) · ⚖️ = resmî/yasal bilgi, 💡 = pratik öneri · Pazar örneklemi: 26 Eylül 2026'da incelenen 45 staj ilanı (temsilî değildir).

## İş hayatında B1 ile B2 arasındaki gerçek fark

Dil kursunda B1 ile B2 arasındaki fark birkaç ünite gibi görünür. Ofiste ise bu fark, "toplantıyı takip edebiliyorum" ile "toplantıda fikrimi savunabiliyorum" arasındaki farktır.

### Resmî CEFR tanımı

**⚖️ Resmî bilgi:** Avrupa Konseyi'nin Avrupa Dilleri Ortak Çerçeve Programı (CEFR, Companion Volume 2020, genel ölçek) seviyeleri şöyle tanımlar. Aşağıdakiler resmî İngilizce metnin Türkçe çevirisidir:

> **B1:** "İş, okul, boş zaman vb. alanlarda düzenli olarak karşılaşılan tanıdık konulardaki açık, standart girdilerin ana noktalarını anlayabilir. Dilin konuşulduğu bir bölgede seyahat ederken ortaya çıkabilecek çoğu durumla başa çıkabilir. Tanıdık veya kişisel ilgi alanına giren konularda basit, bağlantılı metinler üretebilir. Deneyimleri ve olayları, hayalleri, umutları ve hedefleri anlatabilir; görüş ve planları için kısaca gerekçe ve açıklama sunabilir."

> **B2:** "Kendi uzmanlık alanındaki teknik tartışmalar da dahil olmak üzere, hem somut hem soyut konulardaki karmaşık metinlerin ana fikirlerini anlayabilir. Hedef dili kullananlarla, iki taraf için de zorlanma yaratmadan düzenli etkileşimi oldukça mümkün kılan bir akıcılık ve doğallıkla iletişim kurabilir. Geniş bir konu yelpazesinde açık, ayrıntılı metinler üretebilir ve güncel bir konudaki bakış açısını, farklı seçeneklerin avantaj ve dezavantajlarını ortaya koyarak açıklayabilir."

Karşılaştırma için: A2, basit ve rutin görevlerde, tanıdık konularda doğrudan bilgi alışverişi demektir. C1 ise uzun ve zorlu metinleri anlamak, akıcı ve kendiliğinden ifade etmek ve dili sosyal, akademik ve **mesleki** amaçlarla esnek kullanmak anlamına gelir.

### Pratik yorum: ofiste ne anlama geliyor

**💡 Pratik öneri:** Aşağıdaki tablo resmî bir tanım değil, bizim iş hayatına dönük yorumumuzdur.

| Görev | B1 ile | B2 ile |
|---|---|---|
| Ekip toplantıları | Tanıdık konuları çabayla takip edersiniz, kısa katkı verirsiniz | Çoğu toplantıya aktif katılırsınız |
| E-posta | Basit, kısa iç yazışmalar | Net, düzgün yapılandırılmış e-postalar |
| Telefon | Zor; hızlı konuşma ve lehçeler sorun olur | Çoğunlukla yürür, ama hızlı ve beklenmedik görüşmeler yorucudur |
| Teknik ekip içi iletişim | İş İngilizce yürüyorsa sorun yok; Almancada basit günlük konular | Kendi alanınızdaki teknik tartışmalara katılabilirsiniz |
| Müşteri teması | Genellikle gerçekçi değil | Mümkün, ama ikna ve pazarlık çoğu zaman C1 ister |
| Rapor yazma | Kısa notlar, şablonlu metinler | Kısa raporlar ve özetler |

## Almanca staj için yasal bir şart değil: kararı işveren verir

**⚖️ Resmî bilgi:** Oturum Yasası (AufenthG), İstihdam Yönetmeliği (BeschV) ve Asgari Ücret Yasası (MiLoG), üniversite öğrencilerinin stajları için belirli bir Almanca seviyesi öngörmez. Hangi seviyenin gerektiğine, pozisyona göre işveren karar verir.

Federal İş Ajansı (Bundesagentur für Arbeit, BA) bunu açıkça yazar: Birçok şirket en azından temel düzeyde Almanca bekler ve tek başına İngilizce çoğu zaman yeterli değildir. BA'nın başvuru rehberi de genel olarak, gerekli dil seviyesine (B1/B2) ulaştığınızda başvurmanızı önerir.

Doktorluk, hemşirelik veya eğitimcilik gibi bazı düzenlenmiş mesleklerde Almanca yasal olarak gereklidir; ancak bu kural meslek tanınması ve çalışma izniyle ilgilidir, öğrenci stajlarıyla değil. Yine de sağlık, hukuk ve kamu sektörü uygulamada dile çok yoğun alanlardır; buralarda B1 ile staj bulmak zordur.

## Karar matrisi: A1/A2, B1, B2, C1+

**💡 Pratik öneri:** Bu matris, incelediğimiz ilanlar ve genel piyasa deneyimine dayanan bir yönlendirmedir; kesin bir kural değildir.

| Seviye | Gerçekçi olarak neler yapabilirsiniz | Güçlü olduğunuz sektörler | Zorlanacağınız sektörler | Başvuru stratejisi |
|---|---|---|---|---|
| A1/A2 | İş tamamen İngilizce yürüyorsa staj; ofiste günlük selamlaşma ve basit konuşmalar | Yazılım, veri/yapay zekâ, araştırma laboratuvarları | Satış, danışmanlık, İK, pazarlama, lojistik koordinasyon | Yalnızca İngilizce ilanlar; Almanca kursunu paralel sürdürün |
| B1 | İngilizce ağırlıklı ekipte çalışma, Almanca iç iletişimi takip etme, basit e-postalar | IT, Data/AI, mühendislik, araştırma enstitüleri, uluslararası start-up'lar | Müşteriyle temas eden her rol, "fließend" isteyen ilanlar | İngilizce ilanlar + "Deutsch von Vorteil" ilanları + Pflichtpraktikum pozisyonları |
| B2 | Almanca toplantılara katılma, teknik tartışma, kısa rapor | Yukarıdakilere ek olarak mühendislik, üretim, bazı işletme ve İK pozisyonları | Satış, danışmanlık, müşteriye dönük pazarlama | "gute Deutschkenntnisse" ilanlarına da başvurun, mülakatta Almanca konuşun |
| C1+ | Müşteri görüşmesi, sunum, pazarlık, uzun raporlar | Neredeyse tüm sektörler | Çok az | Tüm ilanlar; dil avantajınızı öne çıkarın |

## B1'in yetebileceği alanlar

B1 ile en güçlü olduğunuz yer, çalışma dilinin fiilen İngilizce olduğu ekiplerdir. İncelediğimiz 45 ilanda IT, Data/AI, mühendislik ve araştırma ilanları, Almancayı daha sık hiç istemiyor ya da sadece "artı" olarak sayıyordu. Büyük bir araştırma kuruluşunun 5 ilanından 3'ünde Almanca hiç geçmiyordu.

B1'in yetebileceği tipik durumlar:

- **Yazılım ve veri projeleri:** Kod, dokümantasyon ve toplantılar çoğu zaman İngilizcedir.
- **Araştırma enstitüleri ve üniversite laboratuvarları:** Uluslararası ekiplerde İngilizce ortak dildir.
- **Uluslararası start-up'lar:** "Working language is English" ifadesi olan ilanlar.
- **Pflichtpraktikum (zorunlu staj) pozisyonları:** İncelediğimiz 45 ilanın 10'u yalnızca zorunlu staj kabul ediyordu; zorunlu stajınız varsa bu ilanlar size açık ayrı bir segmenttir.

**💡 Pratik öneri:** B1 sizin için bir eksi değil, bir artıdır. Tamamen İngilizce yürüyen bir ekipte bile kahve molasında Almanca konuşabilmek, uyumunuzu gösterir. Hangi mesleklerin sizin profilinize uyduğunu görmek için [Kariyer Pusulası](/tr/tools/career-compass) aracını kullanabilirsiniz.

## B2'nin gerçekçi alt sınır olduğu alanlar

Ekip içi iş akışı Almanca yürüyorsa (örneğin birçok üretim ve saha mühendisliği, işletme, finans, İK ve lojistik ekibinde) B2 gerçekçi bir alt sınırdır: toplantılara katılmanız, e-posta yazmanız ve iş arkadaşlarınızla teknik konuları konuşmanız beklenir.

Bazı alanlarda ise B2 bile genellikle yetmez. İncelediğimiz ilanlarda satış, danışmanlık, pazarlama, lojistik koordinasyon ve İK'nın önemli bir kısmı "sehr gut", "fließend", "verhandlungssicher" veya açıkça C1 ve üzeri Almanca istiyordu. Üç danışmanlık ilanının üçü de bu kategorideydi. Müşteriyle doğrudan konuşacağınız, ikna edeceğiniz veya pazarlık yapacağınız rollerde beklenti pratikte C1 düzeyindedir.

**💡 Pratik öneri:** "İngilizce yeter, Almanca şart değil" düşüncesinin iş hayatında neden sık sık çöktüğünü [Almanya'da iş için Almanca gerçeği](/tr/blog/german-language-reality-for-jobs-in-germany-the-honest-truth) yazımızda ayrıntılı anlattık.

## İngilizce stajlar: nerede bulunur

Almanya'da İngilizce staj arıyorsanız, iyi haber şu: İncelediğimiz 45 ilanın 10'u İngilizceydi ve bunların 7'sinde hiçbir Almanca şartı yoktu. İki ilanda Almanca "artı" veya alternatif olarak geçiyordu. Akıcı Almanca isteyen tek İngilizce ilan, müşteriye dönük bir pazarlama pozisyonuydu.

Dikkat edilmesi gereken bir nokta: **Sessizlik "Almanca gerekmez" demek değildir.** Almanca yazılmış ve hiçbir dil şartı belirtmeyen 6 ilan vardı; bu durumda çalışma dili büyük ihtimalle Almancadır.

İngilizce staj için pratik kanallar:

- **Araştırma enstitüleri:** Kamu araştırma kuruluşlarının kariyer portalları, özellikle teknik ve bilimsel pozisyonlar.
- **Uluslararası şirketler:** Büyük şirketlerin İngilizce ilanları, özellikle IT, Data/AI ve mühendislik.
- **İngilizce çalışan start-up'lar:** Berlin, Münih ve Hamburg start-up ekosistemleri.
- **Üniversite kürsüleri ve laboratuvarları (Lehrstuhl):** Profesörlere ve araştırma gruplarına doğrudan yazın.
- **İş portallarında dil filtresi:** İlan dilini İngilizce olarak filtreleyin, "English" ve "internship" anahtar kelimelerini birlikte kullanın.

> **Pazar örneklemi hakkında:** Pazar örneklemi 26 Eylül 2026'da incelenmiştir. Bu küçük ve rastgele olmayan bir örneklemdir; Almanya iş piyasasına dair temsilî bir istatistik değildir.

## Pflichtpraktikum ve freiwilliges Praktikum

**⚖️ Resmî bilgi:** *Pflichtpraktikum* (zorunlu staj), çalışma veya sınav yönetmeliğinizin (Studien-/Prüfungsordnung, Praktikumsordnung) öngördüğü stajdır (§ 22 Abs. 1 Nr. 1 MiLoG). *Freiwilliges Praktikum* (gönüllü staj) bunun dışındaki her stajdır; yasa özellikle eğitim/öğrenim öncesi "yönlendirme" stajlarını ve "öğrenime eşlik eden" stajları sayar.

Bu ayrım neden bu kadar önemli:

- **140 gün hesabı:** Zorunlu staj, çalışma günü hesabınızdan düşmez; gönüllü staj genellikle düşer (aşağıda anlatıyoruz).
- **Asgari ücret:** Zorunlu stajda asgari ücret zorunluluğu yoktur; gönüllü stajda süreye bağlıdır.
- **İşverenin tercihi:** İncelediğimiz 45 ilanın 10'u yalnızca Pflichtpraktikum kabul ediyordu, 10'u her ikisini, 1'i yalnızca gönüllü stajı; 24 ilanda bu konu belirtilmemişti. Bazı işverenler zorunlu ve gönüllü stajyerlere farklı ücret ödüyor.

**💡 Pratik öneri:** Bölümünüzde zorunlu staj varsa bunu CV'nizde ve ön yazınızda açıkça belirtin. Bu, özellikle B1 seviyesindeyseniz, işveren için başvurunuzu daha cazip hale getirebilir.

## Uluslararası öğrenciler için çalışma kuralları: 140 günlük hesap

**⚖️ Resmî bilgi:** AB/AEA ve İsviçre vatandaşı öğrencilerin iş piyasasına erişimi sınırsızdır (DAAD). Türkiye'den gelen öğrenciler ise AB dışı öğrenci statüsündedir ve § 16b AufenthG geçerlidir (1 Mart 2024'ten beri değişmedi):

- Öğrenci oturum izniyle yılda **140 iş gününe** kadar çalışabilirsiniz. Buna *Arbeitstagekonto* (çalışma günü hesabı) denir.
- Üniversitedeki öğrenci asistanlığı işleri (*studentische Nebentätigkeit*, örneğin HiWi) bu hesaba sayılmaz.
- **Sayım yöntemi:** En fazla 4 saat çalışılan bir gün yarım gün sayılır; "140 tam veya 280 yarım gün" ifadesi buradan gelir. Alternatif olarak haftalık sayım da yapılabilir: Ders döneminde en fazla 20 saatlik bir hafta 2,5 gün sayılır; ders dönemi dışında her hafta 2,5 gün sayılır. Her takvim haftası için sizin lehinize olan yöntem uygulanır. Yani **20 saat, ayrı bir yasal üst sınır değil, günleri sayma yöntemidir.**

Stajlar açısından:

- **Pflichtpraktikum**, öğrenim amacınızın bir parçasıdır (§ 16b Abs. 1 S. 2 AufenthG). Bu nedenle 140 güne sayılmaz ve Federal İş Ajansı'nın onayını gerektirmez (§ 15 Nr. 2 BeschV).
- **Freiwilliges Praktikum** genellikle 140 günlük hesaba sayılır (Münih KVR, LMU). Ancak yerel yabancılar dairelerinin uygulaması farklı olabilir; örneğin Leipzig, üniversitenin önerdiği alanla ilgili stajları farklı değerlendirir.
- Hesabı aşmak için *Ausländerbehörde*'nin (yabancılar dairesi) izni ve BA onayı gerekir. İzinsiz çalışmak para cezasına yol açabilir.

**💡 Pratik öneri:** Oturum izninizin ek sayfasındaki (*Zusatzblatt/Nebenbestimmung*) çalışma koşullarını okuyun; orada genellikle "Beschäftigung bis zu 140 Tage … sowie Ausübung studentischer Nebentätigkeit erlaubt" gibi bir ifade yer alır. Gönüllü bir staj sözleşmesi imzalamadan önce Ausländerbehörde'nize danışın. HiWi, Werkstudent ve 140 gün ilişkisini [HiWi mi Werkstudent mi](/tr/blog/hiwi-vs-werkstudent-student-jobs-in-germany) yazımızda karşılaştırdık.

## Asgari ücret ve staj maaşı

**⚖️ Resmî bilgi:** 2026'da asgari ücret saatlik brüt **13,90 €**'dur; 1 Ocak 2027'den itibaren 14,60 € olacaktır (BMAS). Stajlarda şu istisnalar geçerlidir (§ 22 MiLoG):

- **Pflichtpraktikum:** Süresi ne olursa olsun (örneğin 6 ay), yönetmelik öngörüyorsa asgari ücret zorunlu değildir.
- **Gönüllü yönlendirme stajı:** En fazla 3 aya kadar asgari ücret zorunlu değildir.
- **Öğrenime eşlik eden gönüllü staj:** Aynı işverenle daha önce böyle bir staj yapılmamışsa, en fazla 3 aya kadar muaftır.
- 3 aydan uzun gönüllü stajda asgari ücret **ilk günden itibaren** ödenir.
- Aynı işverende birden fazla gönüllü staj yapılırsa asgari ücret ödenmelidir.
- Bu kurallar Alman ve uluslararası öğrenciler için aynıdır (BMAS).

İşveren, stajın öğrenme hedefleri, süresi, çalışma saatleri, ücreti ve izni gibi temel koşulları başlamadan önce yazılı olarak belgelemek zorundadır (§ 2 Abs. 1a NachwG). Zorunlu staj için işverene ilgili çalışma yönetmeliğini göstermeniz beklenir.

Mezuniyetten sonra Pflichtpraktikum istisnası artık geçerli değildir; mezun stajları genellikle en az asgari ücretle ödenir. Lisans sonrası "öğrenime eşlik eden" istisnası yalnızca yüksek lisansa kayıtlıysanız geçerlidir. Mezuniyet sonrası iş arama için 18 aya kadar verilen oturum izni (§ 20 AufenthG) her türlü istihdama izin verir; ayrıntılar [iş arama vizesi rehberimizde](/tr/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates).

**💡 Pratik öneri:** Stajyerlerin gerçekte ne kadar kazandığına dair [stajyer maaşları SSS](/tr/faq/is/almanyada-stajyerler-ne-kadar-maas-alir) sayfamıza bakın. Maaşlı stajdan önce [öğrenci olarak Steuer-ID almayı](/tr/blog/how-to-get-a-german-steuer-id-as-a-student-iban) ve [öğrenciler için vergi ve sağlık sigortası rehberini](/tr/blog/2026-guide-to-tax-and-health-insurance-for-students-in-germany) okuyun.

## İlan çözücü: ilan dili ne anlatıyor

**💡 Pratik öneri:** Aşağıdaki eşleştirmeler bizim yorumumuzdur; ifadelerin anlamı şirketten şirkete değişir. İncelediğimiz 45 ilanın hiçbiri Almanca için B1 veya B2 yazmıyordu; açıkça yazılan seviyeler C1 veya C2 idi.

| İfade | Ne anlama gelebilir | B1 ile başvuru | B2 ile başvuru |
|---|---|---|---|
| "Deutschkenntnisse von Vorteil" / "German is a plus" | Çalışma dili muhtemelen İngilizce, Almanca avantaj | Evet | Evet |
| "German preferred" / "would be an advantage" | İngilizce çalışma dili, Almanca artı | Evet | Evet |
| "gute Deutschkenntnisse" | Günlük iş Almanca yürüyor | Riskli; ancak diğer profiliniz çok güçlüyse | Evet |
| "sichere Deutschkenntnisse" | Almancayı rahat kullanmanız bekleniyor | Genellikle hayır | Mümkün |
| "sehr gute Deutschkenntnisse" | C1'e yakın; yazılı rapor beklentisi | Hayır | Sadece güçlü teknik uyumda; mülakatta test edilmeyi bekleyin |
| "in Wort und Schrift" | Yazılı Almanca da önemli (e-posta, rapor) | Hayır | Duruma göre |
| "fließend" / "fluent German" | Pratikte C1 ve üzeri | Hayır | Nadiren |
| "verhandlungssicher" / "business fluent" | C1–C2; pazarlık ve müşteri teması | Hayır | Hayır |
| "German required" (İngilizce ilanda) | Müşteriye dönük mü kontrol edin; çoğu zaman B2/C1 ve üzeri | Hayır | Duruma göre |
| Açıkça "C1" / "C2" | Ciddiye alın; "C2" bazen sadece "akıcı" demek | Hayır | Genellikle hayır |
| Almanca ilanda dil hiç geçmiyor | Çalışma dili muhtemelen Almanca | Dikkatli | Evet |
| İngilizce ilanda Almanca hiç geçmiyor | Çalışma dili muhtemelen İngilizce | Evet | Evet |

## B1 seviyesinde başvuru stratejisi

- **Hedefinizi daraltın:** İngilizce ilanlar, "von Vorteil" ilanları, teknik ve araştırma pozisyonları ile Pflichtpraktikum pozisyonlarına odaklanın.
- **Seviyenizi dürüst yazın:** CV'de "Deutsch: B1 (Goethe-Zertifikat)" gibi net bir ifade kullanın ve varsa sertifikanızı ekleyin. Hangi sertifikanın işe yaradığını [dil sertifikaları aracımızda](/tr/tools/language-certificates) karşılaştırabilirsiniz.
- **İlerlemenizi gösterin:** "Derzeit B2-Kurs, Abschluss voraussichtlich März" gibi bir not, işverene gelişim sinyali verir. Uygun kursları [dil kursları](/tr/language-courses) sayfamızda bulabilirsiniz.
- **İngilizcenizi öne çıkarın:** B1 Almanca + güçlü İngilizce, İngilizce çalışan teknik ekipler için yeterli olabilir.
- **Mülakata hazırlanın:** Kısa bir Almanca tanıtım (30–60 saniye) ezberleyin; bu tek başına iyi bir izlenim bırakır.
- **Doğrudan başvuru yapın:** Laboratuvarlara ve kürsülere ilan beklemeden kısa, net bir e-posta yazın.

## B2 seviyesinde başvuru stratejisi

- **Hedef havuzunu genişletin:** "gute Deutschkenntnisse" ve bazı "sichere Deutschkenntnisse" ilanlarına da başvurun.
- **Almanca başvuru yapın:** Almanca ilana Almanca ön yazı gönderin; yazıyı mutlaka bir anadili konuşana okutun.
- **Teknik kelime dağarcığınızı güçlendirin:** Kendi alanınızın 100–200 temel terimini Almanca bilmek, mülakatta B2'nizi daha güçlü gösterir.
- **Mülakatta Almanca konuşmayı teklif edin:** Bu, "sehr gut" isteyen ilanlarda bile şansınızı artırabilir.
- **Müşteri rolleri için plan yapın:** Satış veya danışmanlık hedefliyorsanız C1'e giden yolu şimdiden planlayın; B2 ile iç rollerden başlamak daha gerçekçidir.

## Başvuru belgeleri

**⚖️ Resmî bilgi (BA/ZAV başvuru rehberi):** Almanya'da standart bir başvuru dosyası şunlardan oluşur:

- **Lebenslauf (tablo halinde CV):** En fazla 2 sayfa. Fotoğraf zorunlu değildir, ancak çoğu işveren hâlâ bekler.
- **Anschreiben (ön yazı):** İlanda aksi belirtilmedikçe beklenir ve her ilana özel yazılmalıdır.
- **Zeugnisse:** İlgili diploma, transkript ve belgeler.
- **Immatrikulationsbescheinigung:** Öğrenci belgeniz.
- **Pflichtpraktikum için:** Praktikumsordnung (staj yönetmeliği) veya fakülte onayı.
- **Dil sertifikası:** Almanca ve İngilizce seviyenizi gösteren belgeler.

**💡 Pratik öneri:** Başvuruyu ilanın dilinde yapın; bu resmî bir kural değil, yaygın bir pratik tavsiyedir. Hazır şablonlarımızdan yararlanabilirsiniz: [staj ön yazısı şablonu](/tr/templates/praktikum-anschreiben), [Almanca CV şablonu](/tr/templates/lebenslauf) ve İngilizce ilanlar için [İngilizce CV şablonu](/tr/templates/lebenslauf-englisch).

## Üniversitenizin Career Service'ini kullanın

Çoğu öğrenci üniversitesinin kariyer merkezini ancak son dakikada hatırlar. Oysa staj aramasında en az kullanılan ücretsiz kaynaklardan biridir. Örnekler:

- **TUM Career Service:** Kişisel geri bildirimli CV kontrolü, koçluk, TUM iş portalı, uluslararası öğrenciler için "Engineers for Germany" programı ve atölyeler sunar.
- **LMU:** Werkstudent, staj ve yan iş ilanlarının yer aldığı bir iş portalı ile çalışma kurallarını anlatan bir International Student Guide sunar.
- **RWTH (Makine Mühendisliği Praktikantenamt):** Şirketler için Pflichtpraktikum onay belgesi düzenler; stajın tanınıp tanınmayacağına fakülte yönergeleri karar verir.

**💡 Pratik öneri:** Fakültenizin *Praktikantenamt*'ı (staj ofisi) veya staj sorumlusu, hangi stajın Pflichtpraktikum sayılacağını onaylar. Başvurudan önce onlarla konuşun; aksi halde yaptığınız staj tanınmayabilir.

## Dört öğrenci senaryosu

### Emre: Informatik öğrencisi, B1 Almanca + güçlü İngilizce

Emre, Darmstadt'ta bilgisayar bilimleri okuyor. İngilizcesi C1, Almancası B1. Yazılım stajı arıyor.

- İngilizce ilanlara ve "German is a plus" ilanlarına odaklanmalı.
- Araştırma enstitülerine ve üniversite laboratuvarlarına doğrudan yazmalı.
- GitHub profilini ve projelerini CV'de öne çıkarmalı.
- Sektörün genel tablosu için [Almanya'da IT iş arama rehberimizi](/tr/blog/germany-it-job-search-2026-a-guide-for-turkish-graduates-and) okumalı.

### Zeynep: Maschinenbau öğrencisi, B2, Pflichtpraktikum

Zeynep'in bölümü 12 haftalık zorunlu staj istiyor. Almancası B2.

- Praktikantenamt'tan Pflichtpraktikum onay belgesini almalı.
- "Pflichtpraktikum" arayan ilanları özellikle hedeflemeli; bu stajın 140 güne sayılmadığını bilmeli.
- Almanca ilanlara Almanca başvurmalı, teknik terimlere hazırlanmalı.
- Orta ölçekli üretim şirketlerini (Mittelstand) listeye eklemeli.

### Burak: Pazarlama öğrencisi, B1

Burak'ın hedefi pazarlama, ama bu alanda ilanların çoğu akıcı Almanca istiyor.

- Almanca metin yazma gerektirmeyen rollere odaklanmalı: performans pazarlaması, veri analizi, sosyal medya analitiği, uluslararası kampanyalar.
- İngilizce çalışan start-up'ları hedeflemeli.
- Türkçe ve İngilizce bilgisini Türkiye pazarına dönük projelerde avantaja çevirmeli.
- Almanca çalışmasını hızlandırmalı; pazarlamada uzun vadede C1 hedefi gerçekçi.

### Selin: Satış/danışmanlık hedefi, B2, C1 yolunda

Selin, işletme okuyor ve danışmanlığa girmek istiyor. Almancası B2, C1 kursuna yazılmış.

- Müşteriye dönük stajlarda B2'nin çoğu zaman yetmediğini kabul etmeli.
- Önce iç rollerde (analiz, araştırma, back-office) Werkstudent veya staj deneyimi edinmeli; bunun neden değerli olduğunu [Werkstudent'in iş piyasasındaki rolü](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market) yazımızda anlattık.
- CV'ye C1 kursunu ve tahmini bitiş tarihini yazmalı.
- C1 sertifikasını aldıktan sonra danışmanlık ilanlarına yeniden başvurmalı.

## 30 günlük staj arama planı

**💡 Pratik öneri:** Bu pratik bir plandır; sayılar garanti değil, hedeftir.

**1. hafta: Temel hazırlık**
- CV'nizi Almanca ve İngilizce olarak hazırlayın; LinkedIn ve Xing profillerinizi güncelleyin.
- Dil belgelerinizi toplayın (sertifika, kurs belgesi).
- 30–40 şirketlik bir hedef liste oluşturun.

**2. hafta: Başvuru dalgası**
- Yaklaşık 20 özelleştirilmiş başvuru gönderin.
- Career Service'ten CV kontrolü için randevu alın.
- Profesörlere ve araştırma gruplarına doğrudan yazın.

**3. hafta: Takip ve mülakat**
- Cevap gelmeyen başvurular için kibar bir takip e-postası gönderin.
- Mülakat için temel Almanca kalıpları çalışın: kendinizi tanıtma, güçlü yönler, "Könnten Sie das bitte wiederholen?" gibi ifadeler.
- Gelen geri bildirimlere göre belgelerinizi iyileştirin.

**4. hafta: Genişletme**
- Coğrafyanızı genişletin: [Berlin](/tr/cities/berlin-q64) gibi start-up yoğun şehirleri ya da başka bölgeleri de düşünün.
- İngilizce rollere, KOBİ'lere, start-up'lara ve araştırma enstitülerine yönelin.
- Sonuç henüz gelmediyse bu normaldir; süreci aynı ritimle sürdürün.

## Sık sorulan sorular

### B1 Almanca ile Almanya'da staj bulmak gerçekten mümkün mü?

Evet, ama hedefli olursanız. B1 ile en gerçekçi seçenekler, çalışma dilinin İngilizce olduğu IT, veri, mühendislik ve araştırma stajlarıdır. İncelediğimiz ilanlarda İngilizce ilanların çoğu hiçbir Almanca şartı koymuyordu. Buna karşılık müşteriyle temas eden satış, danışmanlık ve pazarlama rolleri genellikle çok daha yüksek bir seviye bekliyor. B1'inizi dürüstçe belirtip İngilizcenizi öne çıkarmanız şansınızı artırabilir.

### B2 Almanca ile staj için hangi ilanlara başvurabilirim?

B2 ile İngilizce ilanlara, "Deutschkenntnisse von Vorteil" yazan ilanlara ve çoğu "gute Deutschkenntnisse" ilanına başvurabilirsiniz. "Sichere Deutschkenntnisse" ilanları da mümkün, özellikle teknik rollerde. "Fließend" veya "verhandlungssicher" isteyen ilanlarda şansınız düşüktür, çünkü bunlar pratikte C1 ve üzeri anlamına gelir. Almanca başvuru yapmak ve mülakatta Almanca konuşmak B2'nizi daha görünür kılar.

### Zorunlu staj 140 günlük çalışma hakkımdan düşer mi?

Hayır. Pflichtpraktikum, § 16b AufenthG'ye göre öğrenim amacınızın bir parçasıdır; bu nedenle 140 günlük hesaba sayılmaz ve Federal İş Ajansı'nın onayını gerektirmez. Gönüllü staj ise genellikle bu hesaba sayılır. Yerel yabancılar dairelerinin uygulaması farklı olabileceği için, gönüllü bir staja başlamadan önce Ausländerbehörde'nize danışmanızı ve oturum izninizin ek sayfasındaki çalışma koşullarını okumanızı öneririz.

### Almanya'da staj yaparken asgari ücret alır mıyım?

Duruma bağlı. Pflichtpraktikum'da asgari ücret zorunluluğu yoktur. Gönüllü stajlarda 3 aya kadar istisna vardır; 3 aydan uzun bir gönüllü stajda ise asgari ücret ilk günden itibaren ödenir. 2026'da asgari ücret saatlik brüt 13,90 €'dur. Bu kurallar uluslararası öğrenciler için de aynıdır. Uygulamada bazı işverenler zorunlu stajlara da ücret öder; incelediğimiz ilanlarda bazı şirketler zorunlu ve gönüllü stajyerlere farklı ücret öngörüyordu.

### Almanya'da öğrenci olarak staj nasıl bulunur?

En etkili yollar şunlardır: üniversitenizin Career Service portalı, iş portallarında dil ve staj türü filtreleri, şirketlerin kariyer sayfaları, araştırma enstitüleri ve profesörlere doğrudan başvuru. İlk deneyiminiz için [ilk staj veya Werkstudent pozisyonu nasıl bulunur](/tr/faq/is/ilk-staj-veya-werkstudent-pozisyonu-nasil-bulunur) SSS sayfamız adım adım bir rehber sunuyor. Düzenli ve özelleştirilmiş başvurular, toplu gönderilen genel başvurulardan genellikle daha iyi sonuç verir.

### İlanda Almanca şartı yoksa Almanca gerekmez mi?

Her zaman değil. İlanın hangi dilde yazıldığına bakın. İngilizce yazılmış ve Almancadan hiç söz etmeyen bir ilanda çalışma dili büyük ihtimalle İngilizcedir. Almanca yazılmış ve dil şartı belirtmeyen bir ilanda ise Almancanın zaten çalışma dili olduğu varsayılıyor olabilir. Emin değilseniz başvurudan önce kısa bir e-postayla ekibin çalışma dilini sormak tamamen normaldir.

## Kaynaklar

1. § 16b AufenthG — Bundesministerium der Justiz (Eylül 2026'da kontrol edildi) — https://www.gesetze-im-internet.de/aufenthg_2004/__16b.html
2. § 20 AufenthG — Bundesministerium der Justiz (Eylül 2026'da kontrol edildi) — https://www.gesetze-im-internet.de/aufenthg_2004/__20.html
3. § 15 BeschV — Bundesministerium der Justiz (Eylül 2026'da kontrol edildi) — https://www.gesetze-im-internet.de/beschv_2013/__15.html
4. § 22 MiLoG — Bundesministerium der Justiz (Eylül 2026'da kontrol edildi) — https://www.gesetze-im-internet.de/milog/__22.html
5. § 2 NachwG — Bundesministerium der Justiz (Eylül 2026'da kontrol edildi) — https://www.gesetze-im-internet.de/nachwg/__2.html
6. Fünfte Mindestlohnanpassungsverordnung (13,90 € / 14,60 €) — BMAS — https://www.bmas.de/DE/Service/Gesetze-und-Gesetzesvorhaben/fuenfte-mindestlohnanpassungsverordnung-milov5.html
7. Der Mindestlohn für Studierende — BMAS (Ocak 2026) — https://www.bmas.de/SharedDocs/Downloads/DE/Publikationen/a765-mindestlohn-fuer-studierende.pdf
8. Fachliche Weisungen AufenthG/BeschV — Bundesagentur für Arbeit (12/2024) — https://www.arbeitsagentur.de/datei/dok_ba146473.pdf
9. Beschäftigung internationale Studierende — Landeshauptstadt München, KVR — https://stadt.muenchen.de/infos/studium-arbeiten-aufenthaltsrecht.html
10. Arbeiten in Deutschland — LMU München — https://www.lmu.de/de/workspace-fuer-studierende/international-student-guide/arbeiten-in-deutschland/index.html
11. Side jobs — DAAD — https://www.daad.de/en/studying-in-germany/work-career/side-jobs/
12. CEFR Companion Volume — Council of Europe (2020) — https://rm.coe.int/common-european-framework-of-reference-for-languages-learning-teaching/16809ea0d4
13. How well do I need to know German? — Bundesagentur für Arbeit — https://www.arbeitsagentur.de/en/working-in-germany/german-language-skills
14. Application series: CV — BA/ZAV (Issue 01/2026) — https://www.arbeitsagentur.de/vor-ort/zav/working-and-living-in-germany/iss-en/issue-01-2026/application-cv
15. Career Service — Technische Universität München — https://www.community.tum.de/en/career-service/
16. Pflichtpraktikumsbescheinigung — RWTH Aachen, Maschinenbau Praktikantenamt — https://www.maschinenbau.rwth-aachen.de/cms/maschinenbau/studium/studierende/praktikantenamt/how-to-/~skwti/pflichtpraktikumsbescheinigung/?lidx=1
17. ApplyToGerman pazar örneklemi: 45 staj ilanı — kendi analizimiz (26 Eylül 2026, temsilî değildir)

Yasal bilgiler Eylül 2026'da yukarıdaki kaynaklarla karşılaştırılarak kontrol edilmiştir. Bu yazı genel bilgi niteliğindedir, hukuki danışmanlık değildir. Kendi oturum izninizdeki çalışma koşullarını mutlaka kontrol edin.

Pazar örneklemi 26 Eylül 2026'da incelenmiştir. Bu küçük ve rastgele olmayan bir örneklemdir; Almanya iş piyasasına dair temsilî bir istatistik değildir.
MD;

        $enBody = <<<'MD'
Yes, you can get an internship in Germany with B1 or B2 German, but not every internship. It depends on the sector, customer contact, the company's working language, whether the internship is mandatory or voluntary, the language of the ad, and your English. B1 works best for English-first technical and research roles. B2 opens many more doors. Customer-facing jobs usually expect C1-level German.

> **Last updated:** September 2026 · Legal facts checked against official sources (AufenthG, MiLoG, BMAS, Bundesagentur für Arbeit, Council of Europe) · ⚖️ = legal or official fact, 💡 = practical advice · Market sample: 45 internship ads, 26 Sept 2026 (not representative).

## The real difference between B1 and B2 at work

On a CV, B1 and B2 look close. At work, the gap is large.

### Official CEFR description

**⚖️ Official definition:** The levels come from the Council of Europe's Common European Framework of Reference (CEFR Companion Volume 2020, global scale). The official descriptors read:

> **B1:** "Can understand the main points of clear standard input on familiar matters regularly encountered in work, school, leisure, etc. Can deal with most situations likely to arise while travelling in an area where the language is spoken. Can produce simple connected text on topics which are familiar or of personal interest. Can describe experiences and events, dreams, hopes & ambitions and briefly give reasons and explanations for opinions and plans."
>
> **B2:** "Can understand the main ideas of complex text on both concrete and abstract topics, including technical discussions in their field of specialisation. Can interact with a degree of fluency and spontaneity that makes regular interaction with users of the target language quite possible without imposing strain on either party. Can produce clear, detailed text on a wide range of subjects and explain a viewpoint on a topical issue giving the advantages and disadvantages of various options."

For comparison: A2 covers simple, routine exchanges; C1 means using the language fluently and flexibly, including for professional purposes.

### Practical interpretation

**💡 Practical advice:** Our reading of the descriptors for office work, not an official standard.

| Workplace task | With B1 | With B2 |
|---|---|---|
| Team meetings | Follow familiar topics with effort; hard to jump in | Take part in most meetings |
| Email | Short, simple internal emails | Clear emails, including to other departments |
| Phone calls | Difficult, especially fast or unplanned calls | Manageable, but fast calls are still demanding |
| Technical team work | Workable if the team switches to English when needed | Can join technical discussions in your field |
| Customer contact | Usually not realistic | Possible for simple cases; persuasive or negotiating conversations often need C1 |
| Reports and documentation | Short notes; longer texts need heavy editing | Clear short reports |

## German for internships: no legal requirement

**⚖️ Legal fact:** No. Nothing in the Residence Act (AufenthG), the Employment Ordinance (BeschV) or the Minimum Wage Act (MiLoG) sets a German level for university students' internships. The employer decides what the role needs.

The Federal Employment Agency (Bundesagentur für Arbeit, BA) is candid about the reality: "Many companies expect at least basic German language skills. English alone is often not enough." German is legally required for certain regulated professions, such as doctors, nurses and educators. That concerns licensing, not student internships. Healthcare, law and the public sector are simply language-heavy in practice.

## Decision matrix: A1/A2, B1, B2, C1+

**💡 Practical advice:** Use this as a starting point, not a verdict.

| Level | What you can realistically do | Stronger sectors | Difficult sectors | Application strategy |
|---|---|---|---|---|
| A1/A2 | Routine phrases; work happens in English | English-only IT, Data/AI, research labs | Almost everything German-language | Target ads written in English with no German requirement; keep studying |
| B1 | Internal everyday communication; simple emails; follow familiar meetings | IT, Data/AI, engineering, research, English-first start-ups | Sales, consulting, HR, marketing with customer contact | Apply to English ads and "German is a plus" ads; show your learning path |
| B2 | Most team meetings, technical talk in your field, clear emails | All of the above plus many engineering and business-support roles | Negotiation-heavy and customer-facing roles | Add "gute Deutschkenntnisse" ads where your technical fit is strong |
| C1+ | Fluent professional use, including customers and reports | Nearly all sectors | Very few | Apply broadly, including German-first and client-facing roles |

Unsure which direction fits? Try the [career compass](/en/tools/career-compass) or browse our [profession profiles](/en/professions).

## Where B1 can be enough

B1 is realistic where English is the working language. In our market sample, IT, Data/AI, engineering and research ads were more likely to mention no German, or to treat it as optional. Three of the five ads we looked at from one large research organization did not mention German at all.

Good places for a B1 candidate:

- **Software, data and AI teams** at international companies and start-ups whose working language is English.
- **Research institutes and university labs**.
- **Engineering roles in international R&D teams**, especially for a *Pflichtpraktikum* (mandatory internship).
- **Ads that say "Deutschkenntnisse von Vorteil"** ("German skills are an advantage").

**💡 Practical advice:** At B1, English and technical skills carry the application; your German shows motivation.

## Where B2 is the realistic minimum

B2 is often the realistic minimum where German is used every day inside the team, which can be the case in engineering at mid-sized manufacturers, logistics coordination, business support, controlling and in-house project work. Such ads may ask for "gute Deutschkenntnisse" ("good German").

### Where even B2 is usually not enough

In our sample, sales, consulting, marketing, logistics coordination and much of HR mostly asked for very good, fluent or business-fluent German, or for C1 and above. All three consulting ads did. The only English-language ad that required fluent German was a customer-facing marketing role.

**💡 Practical advice:** If the job means talking to German customers, negotiating, or writing texts people outside the company will read, plan for C1-level German. More on this in our [job-language myth check](/en/blog/english-enough-german-not-required-job-myth-germany).

## English-speaking internships: where to find them

Our market sample of 45 ads contained 10 English-language ads. Seven of those had no German requirement. Two said German was a plus or listed it as one option. One, a customer-facing marketing role, required fluent German. None of the 45 ads named B1 or B2 for German; explicit levels were C1 or C2.

Careful: silence does not mean "no German needed." Six German-language ads mentioned no language at all, and a German-language ad usually means a German-speaking workplace.

**💡 Practical advice:** Where English-speaking internships tend to appear:

- **Research institutes** (large public research organizations and their institutes) and **university chairs and labs**.
- **International companies** with global R&D, IT or data teams.
- **Start-ups with English as their working language.** The ad often says "working language is English."
- **Job-board language filters.** Filter for English-language ads, or search "working language English" or "German is a plus."
- **Your university's career portal**.

> **Market sample disclaimer:** Market sample checked on 26 September 2026. This is a small, non-random sample and is not a representative German labor-market statistic.

## Pflichtpraktikum vs freiwilliges Praktikum

**⚖️ Legal fact:** A *Pflichtpraktikum* (mandatory internship) is one your study or examination regulations, or your faculty's internship regulations (*Praktikumsordnung*), require (§ 22 (1) No. 1 MiLoG). A *freiwilliges Praktikum* (voluntary internship) is any other internship. The law names *orientation* internships (before studies) and internships *accompanying studies*.

The difference matters for three reasons:

1. **The 140-day account.** For non-EU students, a mandatory internship does not count toward it. A voluntary one generally does (next section).
2. **Minimum wage.** Mandatory internships are exempt; voluntary ones are exempt only under certain conditions (see below).
3. **What employers prefer.** In our sample, 10 of 45 ads accepted only a Pflichtpraktikum, 10 accepted both, 1 wanted only voluntary interns, and 24 did not say. Some employers also pay mandatory and voluntary interns differently.

**💡 Practical advice:** If your program requires an internship, say so in the first lines of your cover letter.

## Work rules for international students: the 140-day account

**⚖️ Legal fact:** Students from the EU, EEA and Switzerland have unrestricted access to the German labor market. Students from outside the EU with a student residence permit may work up to **140 working days per year** (§ 16b AufenthG), tracked in a working-day account (*Arbeitstagekonto*). Student assistant jobs at the university (*studentische Nebentätigkeiten*, such as HiWi jobs) are not counted.

**How days are counted.** Each calendar week is counted in whichever way is more favorable to you:

- A day with up to 4 hours counts as a half day (hence "140 full or 280 half days").
- **Or**, per week: during the lecture period, a week with up to 20 hours counts as 2.5 days. Outside the lecture period, any week counts as 2.5 days.

The 20 hours are a way of **counting** days in the account. They are not a second, separate limit.

**⚖️ Legal fact:** A **Pflichtpraktikum** belongs to the purpose of your studies (§ 16b (1) sentence 2 AufenthG). It does **not** count toward the 140 days and needs no approval from the Federal Employment Agency (§ 15 No. 2 BeschV). A **voluntary internship** generally **does** count, according to the Munich immigration office (KVR) and LMU. Local practice can differ (Leipzig, for example, handles university-recommended subject-related internships differently). Working beyond your account requires a permit from the immigration office (*Ausländerbehörde*) and BA approval, and unauthorized work can lead to a fine.

**💡 Practical advice:** Read the extra sheet in your residence permit (*Zusatzblatt*). It states your work conditions, for example "Beschäftigung bis zu 140 Tage … sowie Ausübung studentischer Nebentätigkeit erlaubt" ("employment up to 140 days … and student assistant work permitted"). Before you sign a voluntary internship, check with your Ausländerbehörde. See also [HiWi vs Werkstudent](/en/blog/hiwi-vs-werkstudent-student-jobs-in-germany-en).

## Minimum wage and pay

**⚖️ Legal fact:** The statutory minimum wage in 2026 is **€13.90 gross per hour** (€14.60 from January 1, 2027). The exemptions under § 22 MiLoG:

- **Pflichtpraktikum:** exempt, whatever the length (for example 6 months) if your regulations prescribe it.
- **Voluntary orientation internship:** exempt for up to 3 months.
- **Voluntary internship accompanying studies:** exempt for up to 3 months, if you have not done such an internship with the same employer before.

If a voluntary internship accompanying studies runs **longer than 3 months**, minimum wage is owed from the **first day**. Several voluntary internships with the same employer also trigger minimum wage. According to the Federal Ministry of Labor (BMAS), these rules are **the same for German and international students**. For a mandatory internship, show the employer your study regulation. Employers must also set out the key terms (learning goals, duration, hours, pay, vacation) in writing before you start (§ 2 NachwG).

**After graduation**, the Pflichtpraktikum exception no longer applies; graduate internships are generally paid at least minimum wage. After a Bachelor's, the "accompanying studies" exception applies only if you are enrolled in a Master's. Graduates can use the job-seeker residence permit (§ 20 AufenthG, up to 18 months), which allows any employment; see our [job-seeker visa guide](/en/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-en).

**💡 Practical advice:** For a paid internship you will need a tax ID. Here is [how to get a Steuer-ID as a student](/en/blog/how-to-get-a-german-steuer-id-as-a-student-iban-en). Deductions are covered in our [student tax and health insurance guide](/en/blog/2026-guide-to-tax-and-health-insurance-for-students-in-germany-en). For typical pay levels, see [how much interns earn in Germany](/en/faq/is/almanyada-stajyerler-ne-kadar-maas-alir-en).

## Job-ad decoder

**💡 Practical advice:** This is our interpretation. Wording varies by company, so one employer's "gut" can be another's "sehr gut." In our sample, the most frequent phrases were "in Wort und Schrift" (10 ads), "gute Englischkenntnisse" (8) and "sehr gute Deutschkenntnisse" (6).

| Phrase (English gloss) | What it may mean | Apply with B1 | Apply with B2 |
|---|---|---|---|
| "Deutschkenntnisse von Vorteil" (German is an advantage) | Working language likely English; German helps | Yes | Yes |
| "German is a plus" / "German preferred" | English working language, German valued | Yes | Yes |
| "gute Deutschkenntnisse" (good German) | Everyday work partly in German | Risky; only with a strong profile | Yes |
| "sichere Deutschkenntnisse" (confident German) | You use German confidently at work | Usually no | Possible |
| "sehr gute Deutschkenntnisse" (very good German) | Close to C1 | No | Only with a strong technical fit; expect a test in the interview |
| "in Wort und Schrift" (spoken and written) | Written German matters too: emails, reports | Rarely | Depends on the level named with it |
| "fließend" / "fluent German" | C1+ in practice | No | Rarely |
| "verhandlungssicher" (business fluent) | C1–C2; negotiation, customer contact | No | No |
| "German required" (in an English ad) | Check whether it is customer-facing; often B2/C1 or more | Usually no | Sometimes |
| Explicit "C1" or "C2" | Take it seriously; "C2" sometimes just means "fluent" | No | Rarely |
| No language mentioned, **German-language** ad | German is probably the working language | Rarely | Possible |
| No language mentioned, **English-language** ad | English working language likely | Yes | Yes |

## Application strategy at B1

- **Lead with English and skills.** Put your English level and technical skills near the top of your CV.
- **State your German honestly** as "German: B1 (certificate, year)" and add "currently preparing for B2" if true. You can compare exams with our [language certificate tool](/en/tools/language-certificates).
- **Target the right ads:** English-language ads, "von Vorteil" ads, research labs and Pflichtpraktikum slots.
- **Show momentum.** An ongoing course signals that your German will improve during the internship. Browse [language courses](/en/language-courses) if you need one.
- **Prepare a short German self-introduction** for the interview, even for an English role.
- **Skip customer-facing ads for now.**

## Application strategy at B2

- **Widen your net** to "gute Deutschkenntnisse" ads where your technical fit is strong.
- **Consider writing some applications in German**, especially for German-language ads.
- **Expect a language check.** Practice explaining a past project in German.
- **Name the gap openly:** "B2, working toward C1" is honest and reads as ambitious.
- **Stay careful with "fließend" and "verhandlungssicher" ads** unless the rest of your profile is outstanding.

## Application documents

Per the BA's application series, a typical application includes:

- **CV (Lebenslauf):** tabular, max. 2 pages. A photo is not mandatory, but most employers still expect one.
- **Cover letter (Anschreiben):** expected unless the ad says otherwise, and tailored to each job.
- **Certificates (Zeugnisse):** transcripts and relevant certificates.
- **Enrollment certificate (Immatrikulationsbescheinigung).**
- **For a Pflichtpraktikum:** your *Praktikumsordnung* or a faculty confirmation.
- **Language certificate**, if you have one.

**💡 Practical advice:** Apply in the language of the ad. This is common advice, not an official rule. Start from our [internship cover-letter template](/en/templates/praktikum-anschreiben) and our [English CV template](/en/templates/lebenslauf-englisch).

## How to use your university's Career Service

Career services know which employers hire international students. Examples:

- **TUM Career Service** offers CV checks with personal feedback, coaching, a job portal, workshops and the "Engineers for Germany" program for internationals.
- **LMU** runs a job portal for Werkstudent jobs, internships and side jobs, plus an International Student Guide on work rules.
- **RWTH Aachen's Mechanical Engineering *Praktikantenamt*** (internship office) issues a Pflichtpraktikum confirmation for companies. Faculty guidelines decide recognition.

**💡 Practical advice:** Ask your faculty's Praktikantenamt or internship coordinator (*Praktikumsbeauftragte*) *before* signing whether a placement counts as a Pflichtpraktikum. That decision affects your 140 days and your pay.

## Four student scenarios

These are illustrative personas, not real cases.

### 1. Computer science student, B1 German, strong English

**Situation:** Priya from India studies computer science (*Informatik*) in Berlin, with B1 German and C1 English.

**What to do:**
- Filter for English-language IT and Data/AI ads and "German is a plus" roles.
- Apply to research labs and university chairs in her field.
- If her voluntary internship counts toward the 140 days, plan her other jobs around it.
- Read our [IT job search guide](/en/blog/germany-it-job-search-2026-a-guide-for-turkish-graduates-and-en).

### 2. Mechanical engineering student, B2, mandatory internship

**Situation:** Mateo from Colombia studies mechanical engineering (*Maschinenbau*), has B2 German, and his program requires an internship.

**What to do:**
- Get the Pflichtpraktikum confirmation from his Praktikantenamt first.
- Apply to engineering ads that ask for "gute Deutschkenntnisse" and mention "Pflichtpraktikum" in the first paragraph.
- Include mid-sized manufacturers, where German is often the everyday working language.

### 3. Marketing student, B1

**Situation:** Aisha from Nigeria studies marketing. Her German is B1.

**What to do:**
- Be realistic: in our sample, most marketing ads asked for very good or fluent German.
- Focus on English-first start-ups and international brands, especially analytics-heavy roles.
- Push toward B2 while applying, and consider a Werkstudent job later. See our [Werkstudent reality check](/en/blog/werkstudent-germany-job-market-experience-grades).

### 4. Customer-facing role (sales/consulting), B2 aiming for C1

**Situation:** Daniel from the US wants a consulting internship. His German is B2.

**What to do:**
- Consulting and sales ads in our sample expected C1-level German or more.
- Build toward C1 now; meanwhile target English-speaking international teams or internal roles.
- Reapply to client-facing roles once he reaches C1.

## 30-day internship search plan

**💡 Practical advice:** A practical plan; the numbers are targets, not guarantees.

**Week 1: Foundation**
- Update your CV, LinkedIn and Xing profiles.
- Get proof of your language level ready: a certificate or course confirmation.
- Build a target list of 30–40 employers matched to your level using the decision matrix.

**Week 2: Applications**
- Send about 20 tailored applications.
- Book a Career Service CV check.
- Email professors and research groups about student roles.

**Week 3: Follow-up and interviews**
- Follow up politely on applications older than about 10 days.
- Practice German interview phrases: "Ich studiere … im … Semester" ("I'm studying … in my … semester"), "Mein Pflichtpraktikum dauert … Monate" ("My mandatory internship lasts … months").

**Week 4: Widen**
- Expand your geography. Large hubs like [Berlin](/en/cities/berlin-q64) and [Munich](/en/cities/munchen-q1726) have many international employers.
- Add more English-language roles, SMEs, start-ups and research institutes.

## FAQ

### Can I get an internship in Germany with only B1 German?

Yes, in the right sectors. B1 works best for English-first roles in IT, Data/AI, engineering and research, and for ads that call German "von Vorteil" (an advantage). Customer-facing roles in sales, marketing or consulting are unrealistic at B1. Lead with your English and technical skills, and show that you are actively improving your German.

### Is B2 German enough for an internship in Germany?

For many technical and business-support roles, yes. B2 lets you take part in most meetings and technical discussions and write clear emails, so ads asking for "gute Deutschkenntnisse" become realistic. It is usually not enough for roles that require "fließend" or "verhandlungssicher" German, such as consulting, sales and client-facing marketing, where employers typically expect C1-level German.

### Is there a legal German requirement for internships?

No. Neither the Residence Act, the Employment Ordinance nor the Minimum Wage Act sets a German level for university students' internships. The employer decides what the role needs. Legal language requirements exist for regulated professions like medicine or nursing, but those concern professional licensing, not student internships.

### Does an internship count toward my 140 days?

A mandatory internship (Pflichtpraktikum) required by your study regulations does not count toward the 140-day account for non-EU students. A voluntary internship generally does, though local immigration offices' practice can differ. Check your residence permit's work conditions and confirm with your Ausländerbehörde before signing. EU/EEA/Swiss students have unrestricted labor-market access.

### Do international students get the minimum wage during an internship?

The same rules apply to German and international students. Mandatory internships are exempt from minimum wage. Voluntary orientation internships and voluntary internships accompanying studies are exempt for up to 3 months. If a voluntary internship accompanying studies lasts longer than 3 months, the €13.90 hourly minimum wage (2026) is owed from day one.

### Should I apply in English or German?

As a practical rule, apply in the language of the ad. An English-language ad usually signals an English working environment, so an English CV and cover letter fit. A German application to a German-language ad shows you can work in German, but only if you can back it up in the interview. Have a native speaker proofread it.

## Sources

1. § 16b AufenthG (Residence Act, studies) — Federal Ministry of Justice, gesetze-im-internet.de (accessed Sept 2026) — https://www.gesetze-im-internet.de/aufenthg_2004/__16b.html
2. § 20 AufenthG (job-seeker residence permit) — Federal Ministry of Justice, gesetze-im-internet.de (accessed Sept 2026) — https://www.gesetze-im-internet.de/aufenthg_2004/__20.html
3. § 15 BeschV (Employment Ordinance, internships) — Federal Ministry of Justice, gesetze-im-internet.de (accessed Sept 2026) — https://www.gesetze-im-internet.de/beschv_2013/__15.html
4. § 22 MiLoG (Minimum Wage Act, scope) — Federal Ministry of Justice, gesetze-im-internet.de (accessed Sept 2026) — https://www.gesetze-im-internet.de/milog/__22.html
5. § 2 NachwG (Documentation of Employment Conditions Act) — Federal Ministry of Justice, gesetze-im-internet.de (accessed Sept 2026) — https://www.gesetze-im-internet.de/nachwg/__2.html
6. Fünfte Mindestlohnanpassungsverordnung (€13.90 / €14.60) — BMAS (accessed Sept 2026) — https://www.bmas.de/DE/Service/Gesetze-und-Gesetzesvorhaben/fuenfte-mindestlohnanpassungsverordnung-milov5.html
7. Der Mindestlohn für Studierende — BMAS (Jan 2026) — https://www.bmas.de/SharedDocs/Downloads/DE/Publikationen/a765-mindestlohn-fuer-studierende.pdf
8. Fachliche Weisungen AufenthG/BeschV — Bundesagentur für Arbeit (12/2024) — https://www.arbeitsagentur.de/datei/dok_ba146473.pdf
9. Beschäftigung internationale Studierende — Landeshauptstadt München, KVR (accessed Sept 2026) — https://stadt.muenchen.de/infos/studium-arbeiten-aufenthaltsrecht.html
10. Arbeiten in Deutschland (International Student Guide) — LMU Munich (accessed Sept 2026) — https://www.lmu.de/de/workspace-fuer-studierende/international-student-guide/arbeiten-in-deutschland/index.html
11. Side jobs — DAAD (accessed Sept 2026) — https://www.daad.de/en/studying-in-germany/work-career/side-jobs/
12. CEFR Companion Volume — Council of Europe (2020) — https://rm.coe.int/common-european-framework-of-reference-for-languages-learning-teaching/16809ea0d4
13. How well do I need to know German? — Bundesagentur für Arbeit (accessed Sept 2026) — https://www.arbeitsagentur.de/en/working-in-germany/german-language-skills
14. Application series: CV — BA/ZAV (Issue 01/2026) — https://www.arbeitsagentur.de/vor-ort/zav/working-and-living-in-germany/iss-en/issue-01-2026/application-cv
15. Career Service — Technical University of Munich (accessed Sept 2026) — https://www.community.tum.de/en/career-service/
16. Pflichtpraktikumsbescheinigung — RWTH Aachen University, Mechanical Engineering (accessed Sept 2026) — https://www.maschinenbau.rwth-aachen.de/cms/maschinenbau/studium/studierende/praktikantenamt/how-to-/~skwti/pflichtpraktikumsbescheinigung/?lidx=1
17. Market sample: 45 internship ads — ApplyToGerman, own analysis (26 Sept 2026, not representative)

Legal information checked in September 2026 against the sources above. General information, not legal advice. Check the work conditions in your own residence permit.

Market sample checked on 26 September 2026. This is a small, non-random sample and is not a representative German labor-market statistic.
MD;

        $deBody = <<<'MD'
Ein Praktikum mit B1 Deutsch ist möglich, aber nicht in jeder Branche und nicht in jeder Rolle. Ob B1 oder B2 reicht, entscheidet nicht ein Gesetz, sondern das Unternehmen: Es kommt auf Branche, Aufgaben, Kundenkontakt, Arbeitssprache im Team, die Art des Praktikums (Pflichtpraktikum oder freiwilliges Praktikum) und nicht zuletzt auf Ihr Englisch an. Dieser Leitfaden zeigt Ihnen, wo Ihre Chancen realistisch sind und wie Sie Ihre Bewerbung darauf ausrichten.

> **Stand: September 2026** · Rechtliche Angaben geprüft anhand offizieller Quellen (AufenthG, MiLoG, BMAS, Bundesagentur für Arbeit, Europarat) · ⚖️ = Rechtslage, 💡 = Praxistipp · Marktstichprobe: 45 Praktikumsanzeigen, 26. September 2026 (nicht repräsentativ).

## Der echte Unterschied zwischen B1 und B2 im Berufsalltag

### Offizielle GER-Beschreibung

Der Gemeinsame Europäische Referenzrahmen (GER) des Europarats beschreibt die Niveaus in seiner globalen Skala (Companion Volume 2020, Tabelle 1). Die folgenden Zitate sind **unsere eigene, möglichst wortgetreue Übersetzung des offiziellen englischen Texts**:

> **B1:** „Kann die Hauptpunkte verstehen, wenn klare Standardsprache verwendet wird und es um vertraute Dinge geht, denen man regelmäßig bei der Arbeit, in der Schule, in der Freizeit usw. begegnet. Kann die meisten Situationen bewältigen, die sich auf Reisen im Sprachgebiet ergeben. Kann einfache, zusammenhängende Texte zu vertrauten Themen oder persönlichen Interessengebieten verfassen. Kann über Erfahrungen und Ereignisse berichten, Träume, Hoffnungen und Ziele beschreiben und zu Meinungen und Plänen kurze Begründungen oder Erklärungen geben."

> **B2:** „Kann die Hauptinhalte komplexer Texte zu konkreten und abstrakten Themen verstehen, einschließlich Fachdiskussionen im eigenen Spezialgebiet. Kann sich so fließend und spontan verständigen, dass eine regelmäßige Interaktion mit Nutzerinnen und Nutzern der Zielsprache ohne größere Anstrengung auf beiden Seiten gut möglich ist. Kann klare, detaillierte Texte zu einem breiten Themenspektrum verfassen, einen Standpunkt zu einer aktuellen Frage erläutern und die Vor- und Nachteile verschiedener Möglichkeiten angeben."

Zum Vergleich: A2 steht für einfache, routinemäßige Aufgaben und den direkten Austausch von Informationen über vertraute Dinge. C1 bedeutet, anspruchsvolle, längere Texte zu verstehen, sich fließend und spontan auszudrücken und die Sprache flexibel für gesellschaftliche, akademische und **berufliche** Zwecke zu nutzen.

### Praktische Einordnung

**💡 Praxistipp:** Die folgende Tabelle ist unsere Interpretation für den Praktikumsalltag, keine offizielle Definition.

| Aufgabe | Mit B1 | Mit B2 |
|---|---|---|
| Team-Meetings | Themen aus dem eigenen Bereich mit Mühe folgen; selten spontan beitragen | Den meisten Meetings folgen und sich aktiv beteiligen |
| E-Mails | Kurze, einfache interne E-Mails schreiben | Klare, sachliche E-Mails auch an externe Stellen |
| Telefonate | Schwierig, vor allem bei schnellem Tempo oder Dialekt | Machbar, schnelle oder unerwartete Gespräche bleiben anspruchsvoll |
| Fachliche Teamarbeit | Gut möglich, wenn Fachbegriffe bekannt sind und Englisch als Brücke dient | Fachdiskussionen im eigenen Gebiet gut möglich |
| Kundenkontakt | Meist zu früh | Einfacher Kontakt möglich; Verhandeln und Überzeugen erfordert oft C1 |
| Berichte, Protokolle | Nur kurze Notizen mit Korrektur | Kurze Berichte und Protokolle in klarer Sprache |

## Deutsch im Praktikum: keine gesetzliche Pflicht

**⚖️ Rechtslage:** Weder das Aufenthaltsgesetz (AufenthG) noch die Beschäftigungsverordnung (BeschV) noch das Mindestlohngesetz (MiLoG) legen für Praktika von Studierenden ein bestimmtes Deutschniveau fest. Welche Deutschkenntnisse ein Praktikum verlangt, entscheidet das Unternehmen für die jeweilige Stelle.

Die Bundesagentur für Arbeit (BA) formuliert es auf ihrer Seite zu Deutschkenntnissen im Beruf nüchtern (sinngemäß übersetzt): Viele Unternehmen erwarten mindestens grundlegende Deutschkenntnisse, Englisch allein reicht oft nicht aus. In ihrer Bewerbungsreihe (Ausgabe 01/2026) empfiehlt die BA, sich zu bewerben, sobald man das geforderte Sprachniveau (B1/B2) erreicht hat.

Für einige **reglementierte Berufe** wie Ärztinnen und Ärzte, Pflegefachkräfte oder Erzieherinnen und Erzieher ist Deutsch rechtlich vorgeschrieben. Das betrifft jedoch die Berufsanerkennung und Zulassung, nicht Ihr Praktikum im Studium. Gesundheit, Recht und öffentlicher Dienst sind in der Praxis trotzdem sehr sprachintensive Felder.

## Entscheidungsmatrix: A1/A2, B1, B2, C1+

**💡 Praxistipp:** Die Matrix beruht auf unserer Marktstichprobe und praktischer Erfahrung. Einzelne Unternehmen können deutlich abweichen.

| Niveau | Was realistisch ist | Stärkere Branchen | Schwierige Branchen | Bewerbungsstrategie |
|---|---|---|---|---|
| A1/A2 | Praktika mit rein englischer Arbeitssprache | IT, Data/AI, Forschung, internationale Start-ups | Fast alle Rollen mit deutscher Arbeitssprache | Nur englischsprachige Anzeigen; parallel Deutsch intensiv lernen |
| B1 | Englisch als Arbeitssprache, Deutsch im Alltag des Teams | IT, Data/AI, Engineering, Forschungsinstitute, Lehrstühle | Vertrieb, Beratung, Marketing, HR | Englische Anzeigen und „Deutschkenntnisse von Vorteil"; Pflichtpraktikum als Argument |
| B2 | Technische und analytische Rollen, teils mit „guten Deutschkenntnissen" | Engineering, IT, Forschung, Controlling, Logistik (technisch) | Kundenkontakt, Beratung, Vertrieb | Auch deutsche Anzeigen mit „gute Deutschkenntnisse"; Deutsch im Interview aktiv zeigen |
| C1+ | Die meisten Praktika, auch mit Kundenkontakt | Alle, inkl. Beratung, Vertrieb, Marketing | Wenige Einschränkungen | Anzeigen mit „fließend" und „verhandlungssicher" gezielt angehen |

## Wo B1 reichen kann

Mit B1 sind Sie dort realistisch, wo Englisch die Arbeitssprache ist und Deutsch vor allem im Kaffeeküchen-Gespräch, bei kurzen internen Abstimmungen oder beim Lesen einfacher Dokumente gebraucht wird. In unserer Stichprobe hatten Anzeigen aus **IT, Data/AI, Engineering und Forschung** häufiger keine oder nur optionale Deutschanforderungen. Drei von fünf Anzeigen einer großen Forschungsorganisation erwähnten Deutsch gar nicht.

Typische Rollen für B1:

- Softwareentwicklung, Data Science oder Machine Learning in internationalen Teams
- Praktika in Forschungsinstituten und an Lehrstühlen, wo in Englisch publiziert und diskutiert wird
- Technische Rollen in Start-ups, die „Our working language is English" schreiben
- Anzeigen mit „Deutschkenntnisse von Vorteil" oder „German is a plus"

**💡 Praxistipp:** Ein Pflichtpraktikum kann ein Argument sein. In unserer Stichprobe akzeptierten 10 von 45 Anzeigen ausschließlich Pflichtpraktika. Wer ein Pflichtpraktikum sucht, hat damit Zugang zu einem eigenen Segment von Stellen.

## Wo B2 das realistische Minimum ist

Sobald ein Team intern auf Deutsch arbeitet, Protokolle auf Deutsch geschrieben werden oder Sie mit anderen Abteilungen abstimmen, wird B2 zur realistischen Untergrenze. Das gilt etwa für technische Rollen im Mittelstand, Controlling, Einkauf oder Logistik-Koordination.

**Wo selbst B2 meist nicht reicht:** In unserer Stichprobe verlangten Anzeigen aus **Vertrieb, Beratung, Marketing, Logistik-Koordination** und großen Teilen des HR-Bereichs überwiegend sehr gute, fließende oder verhandlungssichere Deutschkenntnisse oder ausdrücklich C1 und höher. Alle drei Beratungsanzeigen gehörten dazu. Wer Kundinnen und Kunden überzeugen, verhandeln oder schnell am Telefon reagieren soll, braucht in der Praxis meist C1.

Warum „Englisch reicht" auf dem deutschen Arbeitsmarkt oft ein Trugschluss ist, beschreibt unser Beitrag zum [Mythos „Englisch reicht"](/de/blog/mythos-englisch-reicht-deutsch-job-realitaet) ausführlich.

## Englischsprachige Praktika: Wo Sie sie finden

Von den 45 Anzeigen, die wir untersucht haben, waren 34 auf Deutsch, 10 auf Englisch und eine zweisprachig. Von den 10 englischsprachigen Anzeigen verlangten 7 überhaupt kein Deutsch, 2 nannten Deutsch als Plus oder Alternative. Die einzige Ausnahme mit gefordertem fließendem Deutsch war eine Marketingrolle mit Kundenkontakt. Keine der 45 Anzeigen nannte B1 oder B2 für Deutsch; wo ein Niveau genannt wurde, war es C1 oder C2.

**Wichtig:** Schweigen heißt nicht „kein Deutsch". Sechs deutschsprachige Anzeigen erwähnten Sprache gar nicht. Hier ist Deutsch sehr wahrscheinlich die selbstverständliche Arbeitssprache.

**💡 Praxistipp – gute Kanäle für englischsprachige Praktika:**

- **Forschungsinstitute** und außeruniversitäre Forschungseinrichtungen, deren Teams international besetzt sind
- **Lehrstühle und Labore** an Ihrer eigenen Hochschule (fragen Sie direkt bei Professorinnen und Professoren oder Forschungsgruppen an)
- **Internationale Konzerne**, deren Konzernsprache Englisch ist, vor allem in IT und Data
- **Start-ups** mit englischer Arbeitssprache, besonders in Berlin, München und Hamburg
- **Jobportale** mit Sprachfilter oder englischen Suchbegriffen wie „internship", „working language English", „no German required"

> Marktstichprobe vom 26. September 2026. Es handelt sich um eine kleine, nicht zufällige Stichprobe und keine repräsentative Statistik des deutschen Arbeitsmarkts.

## Pflichtpraktikum und freiwilliges Praktikum

**⚖️ Rechtslage:** Ein **Pflichtpraktikum** ist ein Praktikum, das Ihre Studien- oder Prüfungsordnung bzw. die Praktikumsordnung Ihrer Hochschule vorschreibt (§ 22 Abs. 1 Nr. 1 MiLoG). Jedes andere Praktikum ist ein **freiwilliges Praktikum**. Das MiLoG nennt dabei insbesondere das Orientierungspraktikum (vor Ausbildung oder Studium) und das studienbegleitende Praktikum.

Der Unterschied hat drei praktische Folgen:

1. **Arbeitstagekonto:** Ein Pflichtpraktikum zählt für Studierende aus Nicht-EU-Staaten nicht zu den 140 Arbeitstagen, ein freiwilliges in der Regel schon.
2. **Mindestlohn:** Für Pflichtpraktika gilt der Mindestlohn nicht, für freiwillige Praktika nur unter bestimmten Bedingungen nicht.
3. **Präferenz der Arbeitgeber:** In unserer Stichprobe akzeptierten 10 der 45 Anzeigen **ausschließlich** Pflichtpraktika, 10 beide Formen, eine nur freiwillige und 24 machten keine Angabe. Manche Unternehmen vergüten Pflicht- und freiwillige Praktika unterschiedlich.

## Arbeitsregeln für internationale Studierende: das 140-Tage-Konto

**⚖️ Rechtslage:** Studierende aus der **EU, dem EWR und der Schweiz** haben freien Zugang zum Arbeitsmarkt. Für sie ist nur die sozialversicherungsrechtliche 20-Stunden-Regel relevant (DAAD).

Studierende aus **Nicht-EU-Staaten** mit Aufenthaltserlaubnis zum Studium dürfen nach § 16b AufenthG bis zu **140 Arbeitstage im Jahr** arbeiten (Arbeitstagekonto). Studentische Nebentätigkeiten an der Hochschule, zum Beispiel als HiWi, werden nicht angerechnet. Den Unterschied zwischen HiWi und Werkstudent erklärt unser Beitrag [HiWi oder Werkstudent](/de/blog/hiwi-vs-werkstudent-student-jobs-in-germany-de).

**So wird gezählt** (je Kalenderwoche gilt die für Sie günstigere Methode):

- Ein Tag mit bis zu 4 Stunden zählt als halber Tag, daher die bekannte Formel „140 ganze oder 280 halbe Tage".
- Alternativ wochenweise: In der Vorlesungszeit zählt eine Woche mit bis zu 20 Stunden als 2,5 Tage; in der vorlesungsfreien Zeit zählt jede Woche als 2,5 Tage.

Die 20 Stunden sind also eine **Zählmethode** innerhalb des Kontos, keine zusätzliche, eigene Obergrenze.

**⚖️ Rechtslage zum Praktikum:**

- **Pflichtpraktikum:** gehört zum Aufenthaltszweck Studium (§ 16b Abs. 1 S. 2 AufenthG), zählt **nicht** zu den 140 Tagen und braucht keine Zustimmung der Bundesagentur für Arbeit (§ 15 Nr. 2 BeschV).
- **Freiwilliges Praktikum:** wird in der Regel auf das 140-Tage-Konto angerechnet (KVR München, LMU). Die Praxis der Ausländerbehörden kann abweichen; einzelne Städte behandeln von der Hochschule empfohlene, fachbezogene Praktika anders.
- Wer das Konto überschreiten will, braucht eine Erlaubnis der Ausländerbehörde mit Zustimmung der BA. Unerlaubte Beschäftigung kann mit einem Bußgeld geahndet werden.

**💡 Praxistipp:** Lesen Sie die **Nebenbestimmung** bzw. das Zusatzblatt Ihres Aufenthaltstitels. Dort steht zum Beispiel: „Beschäftigung bis zu 140 Tage … sowie Ausübung studentischer Nebentätigkeit erlaubt". Klären Sie vor Vertragsunterschrift bei einem freiwilligen Praktikum mit Ihrer Ausländerbehörde, wie es angerechnet wird.

## Mindestlohn und Vergütung

**⚖️ Rechtslage:** Der gesetzliche Mindestlohn beträgt 2026 **13,90 € brutto pro Stunde**, ab dem 1. Januar 2027 **14,60 €**. Für Praktika gelten nach § 22 MiLoG Ausnahmen:

- **Pflichtpraktikum:** kein Mindestlohnanspruch, unabhängig von der Dauer (auch z. B. sechs Monate, wenn die Ordnung es vorschreibt). Zeigen Sie dem Arbeitgeber dafür die maßgebliche Studien- bzw. Praktikumsordnung.
- **Freiwilliges Orientierungspraktikum:** bis zu drei Monate ohne Mindestlohn.
- **Freiwilliges studienbegleitendes Praktikum:** bis zu drei Monate ohne Mindestlohn, wenn vorher kein solches Praktikum beim selben Arbeitgeber bestand.
- Dauert ein freiwilliges studienbegleitendes Praktikum **länger als drei Monate**, gilt der Mindestlohn **ab dem ersten Tag** (BMAS).
- Mehrere freiwillige Praktika beim selben Arbeitgeber: Mindestlohn ist zu zahlen (BMAS).
- Diese Regeln gelten für deutsche und internationale Studierende gleichermaßen (BMAS).

Arbeitgeber müssen die wesentlichen Bedingungen des Praktikums vor Beginn schriftlich festhalten, etwa Lernziele, Dauer, Arbeitszeit, Vergütung und Urlaub (§ 2 Abs. 1a NachwG). Was Praktikantinnen und Praktikanten tatsächlich verdienen, fasst unsere [FAQ zur Praktikumsvergütung](/de/faq/is/almanyada-stajyerler-ne-kadar-maas-alir-de) zusammen. Für die erste Gehaltsabrechnung brauchen Sie eine [Steuer-ID](/de/blog/how-to-get-a-german-steuer-id-as-a-student-iban-de); was bei Steuern und Krankenversicherung gilt, erklärt unser [Leitfaden zu Steuern und Krankenversicherung für Studierende](/de/blog/2026-guide-to-tax-and-health-insurance-for-students-in-germany-de). Ein vorgeschriebenes Praktikum ist übrigens rentenversicherungsfrei, während Werkstudentinnen und Werkstudenten Rentenbeiträge zahlen (DRV).

**Nach dem Abschluss** ändert sich die Lage: Die Ausnahme für Pflichtpraktika entfällt, und ein Praktikum nach dem Studium wird in der Regel mindestens mit Mindestlohn vergütet. Nach einem Bachelor greift die Ausnahme für studienbegleitende Praktika nur, wenn Sie im Master eingeschrieben sind. Mit der Aufenthaltserlaubnis zur Arbeitsplatzsuche nach § 20 AufenthG (bis zu 18 Monate) dürfen Sie jede Beschäftigung ausüben. Details dazu finden Sie in unserem [Leitfaden zum Job-Seeker-Visum](/de/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-de).

## Stellenanzeigen entschlüsseln: Was hinter den Formulierungen steckt

**💡 Praxistipp:** Die Zuordnung ist unsere Interpretation. Unternehmen verwenden dieselben Begriffe unterschiedlich; im Zweifel fragen Sie nach.

| Formulierung | Was es bedeuten kann | Bewerbung mit B1 | Bewerbung mit B2 |
|---|---|---|---|
| „Deutschkenntnisse von Vorteil" | Arbeitssprache wahrscheinlich Englisch; Deutsch hilft | Ja | Ja |
| „German is a plus" / „would be an advantage" | Englisch im Team, Deutsch als Bonus | Ja | Ja |
| „German preferred" | Englisch als Arbeitssprache, Deutsch erwünscht | Ja | Ja |
| „gute Deutschkenntnisse" | Arbeitsalltag teilweise auf Deutsch | Riskant, nur mit sehr starkem Profil | Ja |
| „sichere Deutschkenntnisse" | Sicherer Umgang im Arbeitsalltag | Meist nein | Möglich |
| „sehr gute Deutschkenntnisse in Wort und Schrift" | Nahe C1, auch Berichte auf Deutsch | Nein | Nur bei starker fachlicher Passung; Test im Interview erwarten |
| „in Wort und Schrift" | Schriftliches Deutsch zählt ebenfalls (E-Mails, Berichte) | Eher nein | Je nach Rolle |
| „fließend" / „fluent German" | In der Praxis C1+ | Nein | Selten |
| „verhandlungssicher" / „business fluent" | C1–C2, Verhandlungen und Kundenkontakt | Nein | Nein |
| „German required" (englische Anzeige) | Prüfen, ob Kundenkontakt; oft B2/C1 oder mehr | Meist nein | Prüfen |
| Ausdrücklich „C1" oder „C2" | Ernst nehmen; „C2" steht manchmal für „fließend" | Nein | Selten |
| „Our working language is English" | Englisch im Team | Ja | Ja |
| Keine Angabe, Anzeige auf Deutsch | Deutsch ist vermutlich Arbeitssprache | Eher nein | Möglich |
| Keine Angabe, Anzeige auf Englisch | Englische Arbeitssprache wahrscheinlich | Ja | Ja |

## Bewerbungsstrategie mit B1

- **Fokus auf englischsprachige Anzeigen** und solche mit „Deutschkenntnisse von Vorteil".
- **Pflichtpraktikum hervorheben:** Nennen Sie in der Betreffzeile oder im ersten Satz, dass es sich um ein Pflichtpraktikum handelt, und legen Sie die Bestätigung Ihrer Fakultät bei.
- **Sprachniveau ehrlich und konkret angeben:** „Deutsch: B1 (GER), aktuell Kurs auf B2-Niveau" wirkt besser als eine vage Selbsteinschätzung. Welche Nachweise anerkannt sind, zeigt unsere [Übersicht zu Sprachzertifikaten](/de/tools/language-certificates).
- **Lernfortschritt belegen:** Ein laufender [Deutschkurs](/de/language-courses) signalisiert, dass Sie Ihr Deutsch aktiv weiterentwickeln.
- **Fachliche Stärken nach vorn stellen:** Projekte, GitHub-Profil, Laborerfahrung, Tools.
- **Initiativbewerbungen an Lehrstühle und Forschungsgruppen**, wo Englisch Alltag ist.

## Bewerbungsstrategie mit B2

- **Deutsche Anzeigen mit „gute Deutschkenntnisse" einbeziehen**, besonders in technischen und analytischen Rollen.
- **Anschreiben auf Deutsch**, wenn die Anzeige deutsch ist, und von einer muttersprachlichen Person gegenlesen lassen.
- **Im Interview Deutsch aktiv anbieten:** Beginnen Sie auf Deutsch und wechseln Sie nur bei Fachdetails ins Englische, wenn es das Gespräch erleichtert.
- **„Sehr gute Deutschkenntnisse" gezielt testen:** Bei starker fachlicher Passung lohnt sich die Bewerbung; bereiten Sie sich auf einen Sprachtest im Gespräch vor.
- **Zielberufe prüfen:** Der [Karriere-Kompass](/de/tools/career-compass) hilft, Rollen zu finden, in denen Ihr Profil und Ihr Sprachniveau zusammenpassen.

## Bewerbungsunterlagen

Nach der Bewerbungsreihe der BA/ZAV (Ausgaben 01/2026 und 02/2026) gehören dazu:

- **Lebenslauf:** tabellarisch, höchstens zwei Seiten. Ein Foto ist nicht verpflichtend, wird aber von den meisten Arbeitgebern noch erwartet. Vorlage: [Lebenslauf nach deutschem Standard](/de/templates/lebenslauf).
- **Anschreiben:** erwartet, sofern die Anzeige nichts anderes sagt, und für jede Stelle individuell. Vorlage: [Anschreiben für ein Praktikum](/de/templates/praktikum-anschreiben).
- **Zeugnisse:** relevante Nachweise, etwa Notenübersicht und Abschlusszeugnisse.
- **Immatrikulationsbescheinigung**
- **Für ein Pflichtpraktikum:** Auszug aus der Praktikumsordnung oder Bestätigung der Fakultät bzw. des Praktikantenamts.
- **Sprachnachweis**, falls vorhanden.

**💡 Praxistipp:** Bewerben Sie sich in der Sprache der Anzeige. Diese Faustregel ist verbreitet, aber nicht offiziell geregelt. Wer eine englische Anzeige mit deutschen Unterlagen beantwortet, ist nicht automatisch im Nachteil, doch die passende Sprache wirkt aufmerksamer.

## So nutzen Sie den Career Service Ihrer Hochschule

Career Services sind für Praktikumssuchende oft unterschätzt. Einige Beispiele:

- **TUM Career Service:** Lebenslauf-Check mit persönlichem Feedback, Coaching, TUM-Jobportal, Workshops und das Programm „Engineers for Germany" für internationale Studierende.
- **LMU:** Jobportal mit Werkstudentenjobs, Praktika und Nebenjobs sowie der International Student Guide mit den Arbeitsregeln.
- **RWTH Aachen (Maschinenbau):** Das Praktikantenamt stellt Unternehmen eine Bestätigung über das Pflichtpraktikum aus; über die Anerkennung entscheiden die Richtlinien der Fakultät.

**💡 Praxistipp:** Klären Sie mit dem Praktikantenamt oder den Praktikumsbeauftragten Ihrer Fakultät **vor** der Bewerbung, was als Pflichtpraktikum anerkannt wird. Tipps für den ersten Einstieg finden Sie auch in unserer FAQ [erstes Praktikum oder Werkstudentenstelle finden](/de/faq/is/ilk-staj-veya-werkstudent-pozisyonu-nasil-bulunur-de).

## Vier Beispielszenarien

### Informatik, B1 und sehr gutes Englisch

Sie studieren Informatik im Master, sprechen B1 Deutsch und arbeiten fließend auf Englisch.

- Konzentrieren Sie sich auf Software-, Data- und AI-Praktika mit englischer Arbeitssprache.
- Sprechen Sie Forschungsgruppen und Lehrstühle direkt an.
- Nutzen Sie die Strategien aus unserem [Leitfaden zur IT-Jobsuche](/de/blog/germany-it-job-search-2026-a-guide-for-turkish-graduates-and-de).
- Nennen Sie Ihr B1 offen und zeigen Sie, dass Sie weiterlernen.

### Maschinenbau, B2, Pflichtpraktikum

Ihre Prüfungsordnung schreibt ein Industriepraktikum vor; Sie haben B2.

- Holen Sie sich die Bestätigung des Praktikantenamts und legen Sie sie jeder Bewerbung bei.
- Bewerben Sie sich auch auf deutsche Anzeigen mit „gute Deutschkenntnisse", besonders im Mittelstand.
- Kommen Sie aus einem Nicht-EU-Staat, können Sie erwähnen, dass ein Pflichtpraktikum nicht auf Ihr 140-Tage-Konto angerechnet wird.

### Marketing, B1

Sie studieren Marketing und haben B1. Hier ist es am schwierigsten, weil Marketing oft Texte und Kundenkommunikation auf Deutsch verlangt.

- Suchen Sie gezielt nach Rollen mit internationalem Fokus: Social Media auf Englisch, Performance-Marketing, Marktanalyse, internationale Märkte.
- Start-ups mit englischer Arbeitssprache sind realistischer als klassische Agenturen.
- Planen Sie einen intensiven Sprachkurs parallel; mit B2 bis C1 wächst Ihr Markt deutlich.

### Kundenkontakt (Vertrieb, Beratung), B2 mit Ziel C1

Sie möchten in den Vertrieb oder die Beratung und haben B2.

- Seien Sie realistisch: Solche Rollen verlangen meist C1 oder „verhandlungssicher".
- Überbrücken Sie mit Backoffice-, Analyse- oder Research-Rollen im selben Unternehmen.
- Eine Werkstudentenstelle kann ein Sprungbrett sein; warum, erklärt unser Beitrag zur [Werkstudenten-Realität](/de/blog/werkstudent-deutschland-jobmarkt-erfahrung-noten).
- Setzen Sie sich ein konkretes Datum für die C1-Prüfung.

## 30-Tage-Plan für die Praktikumssuche

**💡 Praxistipp:** Dies ist ein praktischer Plan. Die Zahlen sind Zielwerte, keine Erfolgsgarantie.

**Woche 1: Grundlagen**

- Lebenslauf aktualisieren, LinkedIn- und Xing-Profil auf Deutsch und Englisch pflegen
- Sprachnachweis bereitlegen oder Prüfungstermin buchen
- Zielliste mit 30 bis 40 Unternehmen, Instituten und Lehrstühlen erstellen

**Woche 2: Bewerbungen**

- Rund 20 individuell angepasste Bewerbungen versenden
- Termin beim Career Service vereinbaren
- Professorinnen, Professoren und Forschungsgruppen direkt anschreiben

**Woche 3: Nachfassen und vorbereiten**

- Nach etwa 10 Tagen freundlich nachfragen
- Deutsche Standardsätze für Interviews üben (Vorstellung, Stärken, Motivation, Rückfragen)
- Unterlagen anhand erster Rückmeldungen verbessern

**Woche 4: Suche ausweiten**

- Geografisch breiter suchen, etwa in [Berlin](/de/cities/berlin-q64) oder [München](/de/cities/munchen-q1726)
- Mehr englischsprachige Rollen einbeziehen
- Mittelstand, Start-ups und Forschungsinstitute gezielt ansprechen

## FAQ

### Kann ich mit B1 Deutsch ein Praktikum in Deutschland bekommen?

Ja, aber vor allem in Rollen mit englischer Arbeitssprache. In unserer Stichprobe von 45 Anzeigen fanden sich solche Chancen vor allem in IT, Data/AI, Engineering und Forschung sowie in Anzeigen mit „Deutschkenntnisse von Vorteil". Vertrieb, Beratung und Marketing verlangen dagegen meist deutlich mehr. Ein Pflichtpraktikum, ein starkes fachliches Profil und sehr gutes Englisch erhöhen Ihre Chancen spürbar.

### Reicht B2 Deutsch für ein Praktikum in Deutschland?

Für viele technische und analytische Praktika ja, besonders wenn die Anzeige „gute Deutschkenntnisse" verlangt. Bei „sehr gute Deutschkenntnisse in Wort und Schrift" lohnt sich eine Bewerbung mit starker fachlicher Passung. Für Rollen mit viel Kundenkontakt, Verhandlungen oder Beratung erwarten Unternehmen in der Praxis meist C1-Niveau.

### Gibt es eine gesetzliche Sprachanforderung für Praktika?

Nein. Weder AufenthG noch BeschV noch MiLoG schreiben für Praktika von Studierenden ein Deutschniveau vor. Das Unternehmen legt die Anforderungen für die jeweilige Stelle fest. Gesetzliche Sprachanforderungen gibt es für einige reglementierte Berufe, etwa in Medizin oder Pflege, sie betreffen aber die Berufsanerkennung, nicht Ihr studentisches Praktikum.

### Zählt ein Pflichtpraktikum zu den 140 Arbeitstagen?

Nein. Ein Pflichtpraktikum gehört zum Aufenthaltszweck Studium und wird nicht auf das 140-Tage-Konto angerechnet; eine Zustimmung der Bundesagentur für Arbeit ist nicht nötig. Ein freiwilliges Praktikum wird dagegen in der Regel angerechnet. Weil die Praxis der Ausländerbehörden abweichen kann, sollten Sie bei freiwilligen Praktika vor der Unterschrift nachfragen.

### Muss ich für ein Praktikum ein Sprachzertifikat vorlegen?

Gesetzlich nicht. Viele Unternehmen fragen aber nach einem Nachweis oder testen Ihr Deutsch im Gespräch. Ein anerkanntes Zertifikat macht Ihre Angabe „B1" oder „B2" glaubwürdiger als eine Selbsteinschätzung. Haben Sie noch keines, nennen Sie Ihr Niveau nach GER ehrlich und geben Sie an, welchen Kurs oder welche Prüfung Sie gerade absolvieren.

### Haben internationale Studierende Anspruch auf Mindestlohn im Praktikum?

Es gelten dieselben Regeln wie für deutsche Studierende. Pflichtpraktika sind vom Mindestlohn ausgenommen, ebenso freiwillige Orientierungs- oder studienbegleitende Praktika bis zu drei Monaten. Dauert ein freiwilliges studienbegleitendes Praktikum länger als drei Monate, gilt der Mindestlohn von 13,90 € pro Stunde (2026) ab dem ersten Tag.

## Quellen

1. § 16b AufenthG — Bundesministerium der Justiz, gesetze-im-internet.de (abgerufen September 2026) — https://www.gesetze-im-internet.de/aufenthg_2004/__16b.html
2. § 20 AufenthG — Bundesministerium der Justiz, gesetze-im-internet.de (abgerufen September 2026) — https://www.gesetze-im-internet.de/aufenthg_2004/__20.html
3. § 15 BeschV — Bundesministerium der Justiz, gesetze-im-internet.de (abgerufen September 2026) — https://www.gesetze-im-internet.de/beschv_2013/__15.html
4. § 22 MiLoG — Bundesministerium der Justiz, gesetze-im-internet.de (abgerufen September 2026) — https://www.gesetze-im-internet.de/milog/__22.html
5. § 2 NachwG — Bundesministerium der Justiz, gesetze-im-internet.de (abgerufen September 2026) — https://www.gesetze-im-internet.de/nachwg/__2.html
6. Fünfte Mindestlohnanpassungsverordnung — Bundesministerium für Arbeit und Soziales (BMAS) — https://www.bmas.de/DE/Service/Gesetze-und-Gesetzesvorhaben/fuenfte-mindestlohnanpassungsverordnung-milov5.html
7. Der Mindestlohn für Studierende — BMAS (Januar 2026) — https://www.bmas.de/SharedDocs/Downloads/DE/Publikationen/a765-mindestlohn-fuer-studierende.pdf
8. Fachliche Weisungen AufenthG/BeschV — Bundesagentur für Arbeit (12/2024) — https://www.arbeitsagentur.de/datei/dok_ba146473.pdf
9. Beschäftigung internationaler Studierender — Landeshauptstadt München, KVR — https://stadt.muenchen.de/infos/studium-arbeiten-aufenthaltsrecht.html
10. Arbeiten in Deutschland — LMU München, International Student Guide — https://www.lmu.de/de/workspace-fuer-studierende/international-student-guide/arbeiten-in-deutschland/index.html
11. Side jobs — DAAD — https://www.daad.de/en/studying-in-germany/work-career/side-jobs/
12. CEFR Companion Volume — Europarat (2020) — https://rm.coe.int/common-european-framework-of-reference-for-languages-learning-teaching/16809ea0d4
13. How well do I need to know German? — Bundesagentur für Arbeit — https://www.arbeitsagentur.de/en/working-in-germany/german-language-skills
14. Application series: CV — BA/ZAV (Ausgabe 01/2026) — https://www.arbeitsagentur.de/vor-ort/zav/working-and-living-in-germany/iss-en/issue-01-2026/application-cv
15. Career Service — Technische Universität München — https://www.community.tum.de/en/career-service/
16. Pflichtpraktikumsbescheinigung — RWTH Aachen, Fakultät für Maschinenwesen, Praktikantenamt — https://www.maschinenbau.rwth-aachen.de/cms/maschinenbau/studium/studierende/praktikantenamt/how-to-/~skwti/pflichtpraktikumsbescheinigung/?lidx=1
17. Werkstudentenprivileg — Deutsche Rentenversicherung — https://www.deutsche-rentenversicherung.de/DRV/DE/Experten/Arbeitgeber-und-Steuerberater/summa-summarum/Lexikon/W/werkstudentenprivileg
18. ApplyToGerman-Marktstichprobe: 45 Praktikumsanzeigen, geprüft am 26. September 2026 (eigene Auswertung, nicht repräsentativ)

Rechtliche Angaben im September 2026 anhand der oben genannten Quellen geprüft. Allgemeine Informationen, keine Rechtsberatung. Prüfen Sie die Arbeitsbedingungen in Ihrem eigenen Aufenthaltstitel.

> Marktstichprobe vom 26. September 2026. Es handelt sich um eine kleine, nicht zufällige Stichprobe und keine repräsentative Statistik des deutschen Arbeitsmarkts.
MD;

        $variants = [
            'tr' => [
                'slug' => 'internship-in-germany-with-b1-b2-german',
                'title' => 'B1/B2 Almanca ile Almanya\'da Staj Bulabilir misiniz?',
                'excerpt' => 'B1 veya B2 Almanca ile Almanya\'da staj bulmak mümkün, ama her stajı değil. Sektör, çalışma dili ve staj türüne göre dürüst bir karar matrisi, ilan çözücü ve 30 günlük plan.',
                'meta_title' => 'B1/B2 Almanca ile Almanya\'da Staj: Gerçekçi Rehber',
                'meta_description' => 'Almanya\'da B1 Almanca ile staj mümkün mü? B1/B2 farkı, İngilizce stajlar, Pflichtpraktikum, 140 gün kuralı, asgari ücret ve 45 ilanlık pazar analizi.',
                'body' => $trBody,
            ],
            'en' => [
                'slug' => 'internship-in-germany-with-b1-b2-german-en',
                'title' => 'Can You Get an Internship in Germany with B1 or B2 German?',
                'excerpt' => 'B1 or B2 German and looking for an internship in Germany? The honest answer depends on sector, customer contact and working language. Here\'s where each level works, how to read job ads, and the rules on 140 days and pay.',
                'meta_title' => 'Internship in Germany with B1 or B2 German (2026)',
                'meta_description' => 'Internship in Germany with B1 or B2 German? Where each level works, English-speaking internships, a job-ad decoder, the 140-day rule and minimum wage.',
                'body' => $enBody,
            ],
            'de' => [
                'slug' => 'internship-in-germany-with-b1-b2-german-de',
                'title' => 'Praktikum mit B1 oder B2 Deutsch: Was realistisch ist',
                'excerpt' => 'Ob B1 oder B2 für ein Praktikum reicht, entscheidet das Unternehmen, nicht das Gesetz. Mit Entscheidungsmatrix, Anzeigen-Decoder aus 45 Stellenanzeigen, 140-Tage-Regel, Mindestlohn und 30-Tage-Plan.',
                'meta_title' => 'Praktikum mit B1 Deutsch: Chancen und Strategie',
                'meta_description' => 'Praktikum mit B1 oder B2 Deutsch: Wo Ihre Chancen realistisch sind, was Stellenanzeigen wirklich meinen und was für 140 Tage und Mindestlohn gilt.',
                'body' => $deBody,
            ],
        ];

        // Slug başka bir yazıya aitse ezme: hiçbir şey yazmadan önce üç slug'ı da kontrol et.
        foreach ($variants as $v) {
            $foreign = Post::where('slug', $v['slug'])->where(fn ($q) => $q->whereNull('translation_group_id')->orWhere('translation_group_id', '!=', $groupId))->first();
            if ($foreign) {
                throw new RuntimeException("B1/B2 staj yazısı: '{$v['slug']}' slug'ı başka bir çeviri grubuna ait (#{$foreign->id}), hiçbir şey yazılmadı.");
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
            // Tekrar koşarsa yayın tarihi korunur.
            $existing = Post::where('slug', $v['slug'])->first();
            $existing ?$existing->update($payload) : Post::create($payload + ['slug' => $v['slug'], 'published_at' => now()]);
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'internship-in-germany-with-b1-b2-german',
            'internship-in-germany-with-b1-b2-german-en',
            'internship-in-germany-with-b1-b2-german-de',
        ])->delete();
    }
};

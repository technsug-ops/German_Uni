<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Tarım & Gıda Bilimleri okumak (2026).
 * Doğrulandı: Agrarwissenschaften/Lebensmittelwissenschaft/Ernährungswissenschaft/Agribusiness disiplinlerarası;
 * bachelor genelde Almanca (C1), İngilizce master bol; tepe okullar Hohenheim/TUM Weihenstephan/Göttingen/Bonn/Kiel/Kassel-Witzenhausen;
 * kamu ~150-350€/dönem (BW non-EU ~1.500€); başvuru uni-assist; gıda sanayi + agtech/sürdürülebilirlik büyüyor;
 * Sperrkonto ~992€/ay = 11.904€/yıl; Blue Card 2026 ~50.700€ / darboğaz ~45.934€.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'd1e10000-1111-4b6f-9f70-dd11ee17bb01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Gıda, tarım ve beslenme; nüfus, iklim ve sürdürülebilirlik baskısı altında giderek stratejik hale gelen alanlar. Almanya, güçlü bir gıda sanayisi, köklü tarım üniversiteleri ve büyüyen bir agtech ekosistemiyle bu alanda Avrupa'nın en sağlam adreslerinden biri. Üstelik kamu üniversiteleri neredeyse **ücretsiz**. Bu yazıda bir yabancı öğrenci olarak Almanya'da **Agrarwissenschaften** (tarım bilimleri), **Lebensmittelwissenschaft** (gıda bilimi) ve komşu alanları okumanın nasıl işlediğini dürüstçe anlatıyorum.

## Alan kapsamı: tarım, gıda, beslenme, agribusiness

Bu alan tek bir bölüm değil; birbirine yakın birçok disiplinden oluşan geniş bir şemsiye:

- **Agrarwissenschaften (tarım bilimleri):** bitki üretimi, hayvancılık, toprak, tarımsal ekoloji — biyoloji ve tarım tekniğinin buluştuğu çekirdek alan.
- **Lebensmittelwissenschaft / -technologie (gıda bilimi/teknolojisi):** gıda işleme, gıda kimyası, mikrobiyoloji, kalite ve güvenlik — sanayiye en yakın kol.
- **Ernährungswissenschaft (beslenme bilimi):** insan beslenmesi, sağlık, diyetetik ve gıda ile sağlık ilişkisi.
- **Agribusiness / Agrarökonomie (tarım ekonomisi):** tarım-gıda zincirinin ekonomisi, yönetimi, pazarlama ve politika.
- **Gartenbau (bahçecilik):** meyve/sebze/süs bitkileri üretimi ve teknolojisi.

Kritik nokta: bu alan **disiplinlerarası**. Doğa bilimi (biyoloji, kimya), teknoloji (proses, mühendislik) ve ekonomi (yönetim, politika) bir arada gelir. Bu sana büyük bir esneklik verir; ama aynı zamanda bir risktir — çünkü çok genel kalırsan mezuniyette **uzmanlaşma** sorunuyla karşılaşabilirsin. Seçtiğin program ve modüller (Modulhandbuch), mezuniyette hangi işe yakın duracağını büyük ölçüde belirler; başvurudan önce ders kataloğunu baştan sona oku ve programın seni laboratuvar/teknoloji, saha/üretim yoksa ekonomi/yönetim yönüne mi ittiğini gör.

## Bachelor (Almanca) vs İngilizce master

Çoğu uluslararası öğrencinin yanıldığı ayrım burada:

- **Bachelor genelde Almanca yürür.** *2025/2026 itibarıyla, yaklaşık; doğrula* — tarım/gıda alanında İngilizce lisans **sınırlıdır**. Lisans için pratikte **Almanca C1** (TestDaF / DSH) beklenir.
- **İngilizce master ise çok bol.** Özellikle **Agricultural Sciences, Food Science, Agribusiness, Sustainable Agriculture, Nutrition ve Agricultural Economics** alanlarında İngilizce yüksek lisans (MSc) programları uluslararası öğrenci için büyük bir çekim noktası.

Yani Almancan yoksa en gerçekçi yol **master seviyesinde** İngilizce başlamak. Bu rotanın tüm detayını ayrı bir yazıda ele alıyorum: [Almancasız İngilizce tarım/gıda/agribusiness master programları](/tr/blog/english-taught-agriculture-food-and-agribusiness-masters-in-germany).

## Tepe & tanınan okullar

Alanda öne çıkan üniversiteler (güçlü oldukları yöne göre, kabaca):

| Üniversite | Öne çıkan alan | Not |
|---|---|---|
| **Hohenheim (Stuttgart)** | Tarım bilimleri, gıda, agribusiness | Almanya'nın önde gelen tarım üniversitesi |
| **TUM — Weihenstephan kampüsü** | Gıda/tarım/bira teknolojisi | Ünlü yaşam bilimleri kampüsü |
| **Göttingen** | Tarım bilimleri, agribusiness | Güçlü araştırma geleneği |
| **Bonn / Kiel** | Tarım, gıda ekonomisi | Güçlü araştırma altyapısı |
| **Halle / Gießen** | Tarım & besin bilimleri | Köklü programlar |
| **Kassel-Witzenhausen** | Organik/ekolojik tarım | Sürdürülebilir tarımda öncü |

*2025/2026 itibarıyla, yaklaşık; sıralamalar alt-alana göre değişir, kendi programını doğrula.* Sıralama ve prestij Almanya'da nasıl işler, "kötü okul" var mı — bunu [Almanya'da üniversite prestiji ve sıralamalar nasıl işler](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) yazısında dürüstçe anlattım.

## Başvuru: uni-assist ve süreç

Başvuru süreci kabaca şöyle:

- **uni-assist:** birçok üniversite yabancı başvurularını buradan toplar (belge/diploma denkliği ön-kontrolü). Erken hesap aç, belgeleri hazırla.
- **Dil kanıtı:** bachelor için Almanca C1; İngilizce master için IELTS/TOEFL. Bazı master'lar ilgili bir lisans altyapısı (biyoloji, kimya, tarım, gıda, ekonomi) da ister.
- **Motivasyon & CV:** disiplinlerarası programlar, neden bu alanı istediğini net anlatan bir niyet mektubuna değer verir; staj/saha deneyimi artı puandır.
- **Dönemler:** kış dönemi başvuruları genelde yaz aylarında kapanır. Erken başla.

## Sektör neden sağlam ve büyüyor

Bu alanın en güçlü kozu, mezun olduğun sektörün hem **sağlam** hem de **büyüyor** olması:

- **Gıda sanayi (büyük ve istikrarlı):** Nestlé, Dr. Oetker gibi devler ve sayısız orta ölçekli üretici sürekli Ar-Ge, kalite ve üretim uzmanı arar. İnsan her koşulda yemek yer; sektör krize dayanıklıdır.
- **Agtech & dijital tarım:** sensörler, hassas tarım, veri, otomasyon — en hızlı büyüyen kollardan.
- **Sürdürülebilirlik & bitki-bazlı:** iklim baskısı, organik üretim ve alternatif proteinler talebi artırıyor.
- **Tohum & ıslah:** KWS, BASF/Bayer Crop Science gibi şirketler bitki ıslahı ve biyoteknolojide uzman arar.
- **Araştırma & kamu:** araştırma enstitüleri, gıda güvenliği kurumları, tarım politikası ve danışmanlık.

Kısacası talep gerçek; ama alt-alanlara eşit dağılmıyor. Gıda teknolojisi ve agribusiness tarafı, araştırma/kamuya göre genelde daha çok ve daha iyi ücretli pozisyon açar. "Tarımı/gıdayı seviyorum" hissini, işverenin gerçekten aradığı somut bir beceriye (gıda proses, kalite/HACCP, veri/agtech, tarım ekonomisi) bağlaman istihdam şansını ciddi biçimde artırır. Bu diplomanın sanayiye bakan yüzünü, daha genel olarak [Almanya'da bir doğa bilimi diplomasıyla sanayide kariyer](/tr/blog/what-to-do-with-a-science-degree-in-germany-industry-careers) yazısında ele aldım; sektörün maaş ve kariyer detayları için [Almanya'da tarım, gıda sanayi ve agtech'te çalışmak](/tr/blog/working-in-agriculture-food-industry-and-agtech-in-germany-careers-salary) yazısına bak.

## Ücret, burs ve mezuniyet sonrası

- **Harç:** kamu üniversitelerinde **öğrenim ücreti yok**; sadece dönemlik katkı ~**150–350€** (semester ticket dâhil olabilir). İstisna: **Baden-Württemberg**, AB dışı öğrencilerden ~**1.500€/dönem** alır — Hohenheim de bu eyalette. *2025/2026 itibarıyla, yaklaşık; doğrula.*
- **Sperrkonto (bloke hesap):** vize için genelde ~**992€/ay = ~11.904€/yıl** göstermen istenir. *2025/2026 itibarıyla, yaklaşık; resmî kaynaktan doğrula.*
- **Burs:** **DAAD** en bilinen kaynak; ayrıca Deutschlandstipendium ve vakıf bursları.
- **Blue Card (mezuniyet sonrası iş):** 2026 için genel maaş eşiği ~**50.700€/yıl**; darboğaz meslek / yeni mezun eşiği ~**45.934€/yıl**. *Yaklaşık; resmî kaynaktan doğrula.* Tarım/gıda alanının MINT ile örtüşen teknik rolleri (gıda-tek, agtech, veri) bu eşikleri yakalamana yardımcı olabilir.

Doğa bilimlerinin genel çerçevesini merak ediyorsan [Almanya'da doğa bilimleri (fizik/kimya/biyoloji) okumak](/tr/blog/studying-natural-sciences-physics-chemistry-biology-in-germany) yazısı da tamamlayıcı bir okuma. Bu diplomayla iş piyasasında somut olarak ne yapabileceğini ise [tarım/gıda diplomasıyla iş piyasası](/tr/blog/what-to-do-with-an-agriculture-food-science-degree-in-germany-job-market) yazısında ele alıyorum.

## Sonuç & dürüst tavsiye

Almanya'da tarım ve gıda bilimleri okumak **maliyet açısından çok cazip**, sektör sağlam ve büyüyor, İngilizce master seçeneği bol. Dürüst tavsiyem:

1. **Almancan yoksa master hedefle.** İngilizce lisans sınırlı; İngilizce MSc bol.
2. **Baştan bir yönde uzmanlaş:** gıda teknolojisi mi, agribusiness mi, sürdürülebilirlik/agtech mi, yoksa araştırma mı? Çok genel bir diploma iş aramada dezavantaj olabilir.
3. **Almancayı ihmal etme:** araştırma/uluslararası tarafta İngilizce yeterli olsa da, Alman gıda sanayisi ve domestik piyasa için Almanca büyük fark yaratır.
4. **Erken planla:** uni-assist, dil kanıtı, Sperrkonto ve başvuru dönemleri seni bir yıl kaybettirebilir.

Kararını verirken sadece "tarım/gıda ilgimi çekiyor" duygusuna değil, **hangi alt-alanın seni istihdam edilebilir kılacağına** bak.

*Bu yazı 2026 başı itibarıyla hazırlanmıştır. Öğrenim ücretleri, başvuru koşulları, Sperrkonto tutarı, Blue Card maaş eşikleri ve piyasa rakamları eyalete, üniversiteye ve yıla göre değişir. Başvurmadan önce ilgili üniversitenin ve resmî kurumların güncel bilgilerini mutlaka doğrula.*
MD;

        $deBody = <<<'MD'
Ernährung, Landwirtschaft und Lebensmittel werden unter dem Druck von Bevölkerung, Klima und Nachhaltigkeit immer strategischer. Mit einer starken Lebensmittelindustrie, traditionsreichen Agraruniversitäten und einem wachsenden Agtech-Ökosystem ist Deutschland eine der solidesten Adressen Europas in diesem Feld. Und die staatlichen Universitäten sind praktisch **kostenlos**. In diesem Artikel erkläre ich dir ehrlich, wie ein Studium der **Agrarwissenschaften**, der **Lebensmittelwissenschaft** und verwandter Felder in Deutschland als internationale:r Studierende:r funktioniert.

## Das Feld: Landwirtschaft, Lebensmittel, Ernährung, Agribusiness

Dieses Feld ist kein einzelnes Fach, sondern ein breites Dach aus vielen verwandten Disziplinen:

- **Agrarwissenschaften:** Pflanzenproduktion, Tierhaltung, Boden, Agrarökologie — der Kern, in dem Biologie und Agrartechnik zusammenkommen.
- **Lebensmittelwissenschaft / -technologie:** Lebensmittelverarbeitung, Lebensmittelchemie, Mikrobiologie, Qualität und Sicherheit — der industrienächste Zweig.
- **Ernährungswissenschaft:** menschliche Ernährung, Gesundheit, Diätetik und der Zusammenhang von Lebensmitteln und Gesundheit.
- **Agribusiness / Agrarökonomie:** Ökonomie, Management, Marketing und Politik der Agrar- und Lebensmittelkette.
- **Gartenbau:** Produktion und Technologie von Obst, Gemüse und Zierpflanzen.

Entscheidend: Dieses Feld ist **interdisziplinär**. Naturwissenschaft (Biologie, Chemie), Technologie (Prozess, Ingenieurwesen) und Ökonomie (Management, Politik) kommen zusammen. Das gibt dir große Flexibilität, ist aber auch ein Risiko — denn wenn du zu allgemein bleibst, stößt du beim Abschluss auf das Problem der **Spezialisierung**. Das gewählte Programm und deine Module (Modulhandbuch) bestimmen weitgehend, welcher Job dir nach dem Abschluss nahe liegt; lies daher vor der Bewerbung den Modulkatalog gründlich und erkenne, ob dich das Programm eher in Richtung Labor/Technologie, Feld/Produktion oder Ökonomie/Management drängt.

## Bachelor (auf Deutsch) vs. englischsprachiger Master

Hier täuschen sich viele internationale Studierende:

- **Der Bachelor läuft meist auf Deutsch.** *Stand 2025/2026, ungefähr; bitte prüfen* — englischsprachige Bachelorprogramme in Landwirtschaft/Lebensmittel sind **begrenzt**. Für den Bachelor wird praktisch **Deutsch C1** (TestDaF / DSH) erwartet.
- **Englischsprachige Master gibt es dagegen reichlich.** Besonders in **Agricultural Sciences, Food Science, Agribusiness, Sustainable Agriculture, Nutrition und Agricultural Economics** sind englischsprachige Master (MSc) ein großer Anziehungspunkt für Internationale.

Ohne Deutsch ist der realistischste Weg also ein Einstieg auf **Masterebene** auf Englisch. Alle Details dazu im Artikel [Englischsprachige Agrar-, Lebensmittel- & Agribusiness-Master in Deutschland](/de/blog/english-taught-agriculture-food-and-agribusiness-masters-in-germany-de).

## Top- und anerkannte Universitäten

Herausragende Universitäten im Feld (grob nach Stärken):

| Universität | Stärke | Hinweis |
|---|---|---|
| **Hohenheim (Stuttgart)** | Agrarwissenschaften, Lebensmittel, Agribusiness | führende Agraruniversität Deutschlands |
| **TUM — Campus Weihenstephan** | Lebensmittel-/Agrar-/Brautechnologie | berühmter Life-Science-Campus |
| **Göttingen** | Agrarwissenschaften, Agribusiness | starke Forschungstradition |
| **Bonn / Kiel** | Landwirtschaft, Agrarökonomie | starke Forschungsinfrastruktur |
| **Halle / Gießen** | Agrar- & Ernährungswissenschaften | traditionsreiche Programme |
| **Kassel-Witzenhausen** | ökologischer Landbau | Vorreiter der nachhaltigen Landwirtschaft |

*Stand 2025/2026, ungefähr; Rankings variieren je nach Teilgebiet, prüfe dein konkretes Programm.* Wie Prestige und Rankings in Deutschland funktionieren und ob es "schlechte Unis" gibt, erkläre ich ehrlich in [Wie Uni-Prestige und Rankings in Deutschland funktionieren](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de).

## Bewerbung: uni-assist und Ablauf

Der Ablauf grob:

- **uni-assist:** viele Universitäten bündeln hier internationale Bewerbungen (Vorprüfung von Zeugnissen/Abschlüssen). Lege früh ein Konto an und bereite die Unterlagen vor.
- **Sprachnachweis:** Deutsch C1 für den Bachelor; IELTS/TOEFL für den englischen Master. Manche Master verlangen einen fachlichen Hintergrund (Biologie, Chemie, Agrar, Lebensmittel, Wirtschaft).
- **Motivation & CV:** interdisziplinäre Programme legen Wert auf ein Motivationsschreiben, das klar zeigt, warum du dieses Feld willst; Praktikums-/Felderfahrung ist ein Pluspunkt.
- **Fristen:** Bewerbungen fürs Wintersemester schließen oft im Sommer. Fang früh an.

## Warum der Sektor solide ist und wächst

Der größte Trumpf dieses Feldes: Der Sektor, in den du hineinwächst, ist zugleich **solide** und **wachsend**:

- **Lebensmittelindustrie (groß und stabil):** Konzerne wie Nestlé und Dr. Oetker sowie unzählige mittelständische Hersteller suchen ständig Fachleute für F&E, Qualität und Produktion. Gegessen wird immer; der Sektor ist krisenfest.
- **Agtech & digitale Landwirtschaft:** Sensorik, Precision Farming, Daten, Automatisierung — einer der am schnellsten wachsenden Zweige.
- **Nachhaltigkeit & pflanzenbasiert:** Klimadruck, Bio-Produktion und alternative Proteine steigern die Nachfrage.
- **Saatgut & Züchtung:** Unternehmen wie KWS und BASF/Bayer Crop Science suchen Fachleute in Pflanzenzüchtung und Biotechnologie.
- **Forschung & öffentlicher Sektor:** Forschungsinstitute, Lebensmittelsicherheitsbehörden, Agrarpolitik und Beratung.

Kurz: Die Nachfrage ist real; aber sie verteilt sich nicht gleichmäßig auf die Teilgebiete. Lebensmitteltechnologie und Agribusiness schaffen in der Regel mehr und besser bezahlte Stellen als Forschung/öffentlicher Dienst. Wenn du das Gefühl "Ich mag Landwirtschaft/Lebensmittel" an eine konkrete Fähigkeit koppelst, die Arbeitgeber wirklich suchen (Lebensmittelprozess, Qualität/HACCP, Daten/Agtech, Agrarökonomie), steigen deine Beschäftigungschancen deutlich. Die industrielle Seite dieses Abschlusses behandle ich allgemeiner in [Was tun mit einem naturwissenschaftlichen Abschluss in der Industrie in Deutschland](/de/blog/what-to-do-with-a-science-degree-in-germany-industry-careers-de); Gehälter und Karrieredetails des Sektors findest du in [In Landwirtschaft, Lebensmittelindustrie und Agtech in Deutschland arbeiten](/de/blog/working-in-agriculture-food-industry-and-agtech-in-germany-careers-salary-de).

## Kosten, Stipendien und nach dem Abschluss

- **Gebühren:** an staatlichen Universitäten gibt es **keine Studiengebühren**; nur ein Semesterbeitrag von ~**150–350€** (ggf. inkl. Semesterticket). Ausnahme: **Baden-Württemberg** verlangt von Nicht-EU-Studierenden ~**1.500€/Semester** — auch Hohenheim liegt in diesem Bundesland. *Stand 2025/2026, ungefähr; bitte prüfen.*
- **Sperrkonto:** fürs Visum musst du meist ~**992€/Monat = ~11.904€/Jahr** nachweisen. *Stand 2025/2026, ungefähr; aus offizieller Quelle prüfen.*
- **Stipendien:** **DAAD** ist die bekannteste Quelle; außerdem das Deutschlandstipendium und Stiftungsstipendien.
- **Blue Card (Job nach dem Abschluss):** allgemeine Gehaltsschwelle 2026 ~**50.700€/Jahr**; Engpassberufe / Berufseinsteiger:innen ~**45.934€/Jahr**. *Ungefähr; aus offizieller Quelle prüfen.* Die technischen, MINT-nahen Rollen des Agrar-/Lebensmittelfeldes (Food-Tech, Agtech, Daten) können dir helfen, diese Schwellen zu erreichen.

Wenn dich der allgemeine Rahmen der Naturwissenschaften interessiert, ist [Naturwissenschaften (Physik/Chemie/Biologie) in Deutschland studieren](/de/blog/studying-natural-sciences-physics-chemistry-biology-in-germany-de) eine ergänzende Lektüre. Was du mit diesem Abschluss konkret auf dem Arbeitsmarkt machen kannst, behandle ich in [Was tun mit einem Agrar-/Lebensmittelabschluss in Deutschland](/de/blog/what-to-do-with-an-agriculture-food-science-degree-in-germany-job-market-de).

## Fazit & ehrlicher Rat

Agrar- und Lebensmittelwissenschaften in Deutschland zu studieren ist **kostenmäßig sehr attraktiv**, der Sektor ist solide und wächst, und englische Master gibt es reichlich. Mein ehrlicher Rat:

1. **Ohne Deutsch: ziele auf den Master.** Englische Bachelor sind begrenzt, englische MSc reichlich.
2. **Spezialisiere dich früh:** Lebensmitteltechnologie, Agribusiness, Nachhaltigkeit/Agtech oder Forschung? Ein zu allgemeiner Abschluss kann bei der Jobsuche ein Nachteil sein.
3. **Vernachlässige Deutsch nicht:** in Forschung/international reicht Englisch, aber für die deutsche Lebensmittelindustrie und den heimischen Markt macht Deutsch einen großen Unterschied.
4. **Plane früh:** uni-assist, Sprachnachweis, Sperrkonto und Fristen können dich ein Jahr kosten.

Schau bei deiner Entscheidung nicht nur auf das Gefühl "Landwirtschaft/Lebensmittel interessieren mich", sondern darauf, **welches Teilgebiet dich beschäftigungsfähig macht**.

*Dieser Artikel wurde Anfang 2026 erstellt. Studiengebühren, Bewerbungsbedingungen, Sperrkonto-Betrag, Blue-Card-Gehaltsschwellen und Marktzahlen variieren je nach Bundesland, Universität und Jahr. Prüfe vor der Bewerbung unbedingt die aktuellen Angaben der jeweiligen Universität und offizieller Stellen.*
MD;

        $enBody = <<<'MD'
Food, agriculture and nutrition are becoming increasingly strategic under the pressure of population, climate and sustainability. With a strong food industry, long-established agricultural universities and a growing agtech ecosystem, Germany is one of Europe's most solid destinations in this field. And public universities are practically **free**. In this article I explain honestly how studying **Agrarwissenschaften** (agricultural sciences), **Lebensmittelwissenschaft** (food science) and neighbouring fields in Germany works as an international student.

## The field: agriculture, food, nutrition, agribusiness

This field isn't a single subject; it's a broad umbrella of many related disciplines:

- **Agrarwissenschaften (agricultural sciences):** crop production, animal husbandry, soil, agroecology — the core where biology and agricultural technology meet.
- **Lebensmittelwissenschaft / -technologie (food science/technology):** food processing, food chemistry, microbiology, quality and safety — the branch closest to industry.
- **Ernährungswissenschaft (nutrition science):** human nutrition, health, dietetics and the link between food and health.
- **Agribusiness / Agrarökonomie (agricultural economics):** the economics, management, marketing and policy of the agri-food chain.
- **Gartenbau (horticulture):** production and technology of fruit, vegetables and ornamental plants.

The key point: this field is **interdisciplinary**. Natural science (biology, chemistry), technology (process, engineering) and economics (management, policy) come together. That gives you great flexibility, but it's also a risk — because if you stay too general, you'll hit the problem of **specialisation** at graduation. The program you choose and your modules (Modulhandbuch) largely determine which job you'll be close to at graduation; so before applying, read the module catalogue carefully and see whether the program pushes you toward the lab/technology, the field/production, or the economics/management direction.

## Bachelor's (in German) vs English-taught master's

Here's where many international students get it wrong:

- **The bachelor's is mostly taught in German.** *As of 2025/2026, approximate; verify* — English-taught bachelor's in agriculture/food are **limited**. For a bachelor's you're effectively expected to have **German C1** (TestDaF / DSH).
- **English-taught master's, on the other hand, are plentiful.** Especially in **Agricultural Sciences, Food Science, Agribusiness, Sustainable Agriculture, Nutrition and Agricultural Economics**, English-taught master's (MSc) are a major draw for international students.

So without German, the most realistic path is starting at **master's level** in English. I cover this route in full in a separate article: [English-taught agriculture, food & agribusiness master's in Germany](/en/blog/english-taught-agriculture-food-and-agribusiness-masters-in-germany-en).

## Top & recognised universities

Standout universities in the field (roughly by their strengths):

| University | Strength | Note |
|---|---|---|
| **Hohenheim (Stuttgart)** | Agricultural sciences, food, agribusiness | Germany's leading agricultural university |
| **TUM — Weihenstephan campus** | Food/agri/brewing technology | famous life-sciences campus |
| **Göttingen** | Agricultural sciences, agribusiness | strong research tradition |
| **Bonn / Kiel** | Agriculture, agricultural economics | strong research infrastructure |
| **Halle / Gießen** | Agricultural & nutrition sciences | long-established programs |
| **Kassel-Witzenhausen** | Organic/ecological farming | pioneer in sustainable agriculture |

*As of 2025/2026, approximate; rankings vary by sub-field, verify your specific program.* How prestige and rankings actually work in Germany, and whether "bad universities" exist, I explain honestly in [How university prestige and rankings work in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en).

## Applying: uni-assist and the process

The process, roughly:

- **uni-assist:** many universities collect international applications here (pre-checking certificates/degrees). Open an account early and prepare your documents.
- **Language proof:** German C1 for the bachelor's; IELTS/TOEFL for the English master's. Some master's also require a relevant background (biology, chemistry, agriculture, food, economics).
- **Motivation & CV:** interdisciplinary programs value a statement that clearly shows why you want this field; internship/field experience is a plus.
- **Deadlines:** winter-semester applications often close in summer. Start early.

## Why the sector is solid and growing

The biggest advantage of this field is that the sector you graduate into is both **solid** and **growing**:

- **Food industry (large and stable):** giants like Nestlé and Dr. Oetker, and countless mid-sized producers, constantly need specialists for R&D, quality and production. People always eat; the sector is crisis-resistant.
- **Agtech & digital agriculture:** sensors, precision farming, data, automation — one of the fastest-growing branches.
- **Sustainability & plant-based:** climate pressure, organic production and alternative proteins are raising demand.
- **Seed & breeding:** companies like KWS and BASF/Bayer Crop Science seek specialists in plant breeding and biotechnology.
- **Research & public sector:** research institutes, food-safety agencies, agricultural policy and consulting.

In short: demand is real, but it isn't spread evenly across the sub-fields. Food technology and agribusiness generally create more and better-paid roles than research/public service. If you couple the feeling of "I like agriculture/food" with a concrete skill employers actually seek (food process, quality/HACCP, data/agtech, agricultural economics), your employability rises significantly. I cover the industry-facing side of this degree more generally in [What to do with a science degree in Germany: industry careers](/en/blog/what-to-do-with-a-science-degree-in-germany-industry-careers-en); for the sector's salaries and career details, see [Working in agriculture, the food industry and agtech in Germany](/en/blog/working-in-agriculture-food-industry-and-agtech-in-germany-careers-salary-en).

## Fees, scholarships and after graduation

- **Fees:** public universities charge **no tuition**; only a semester contribution of ~**€150–350** (may include a semester ticket). Exception: **Baden-Württemberg** charges non-EU students ~**€1,500/semester** — Hohenheim is in this state too. *As of 2025/2026, approximate; verify.*
- **Sperrkonto (blocked account):** for the visa you're usually asked to show ~**€992/month = ~€11,904/year**. *As of 2025/2026, approximate; verify from official sources.*
- **Scholarships:** **DAAD** is the best-known source; also the Deutschlandstipendium and foundation scholarships.
- **Blue Card (job after graduation):** the 2026 general salary threshold is ~**€50,700/year**; the shortage-occupation / new-graduate threshold is ~**€45,934/year**. *Approximate; verify from official sources.* The technical, STEM-adjacent roles of the agri/food field (food-tech, agtech, data) can help you reach these thresholds.

If you're curious about the broader natural-sciences framing, [Studying natural sciences (physics/chemistry/biology) in Germany](/en/blog/studying-natural-sciences-physics-chemistry-biology-in-germany-en) is complementary reading. What you can concretely do with this degree on the job market I cover in [What to do with an agriculture/food science degree in Germany](/en/blog/what-to-do-with-an-agriculture-food-science-degree-in-germany-job-market-en).

## Conclusion & honest advice

Studying agriculture and food sciences in Germany is **very attractive cost-wise**, the sector is solid and growing, and English-taught master's are plentiful. My honest advice:

1. **Without German, target the master's.** English bachelor's are limited; English MSc are plentiful.
2. **Specialise early:** food technology, agribusiness, sustainability/agtech, or research? An overly general degree can be a disadvantage in the job hunt.
3. **Don't neglect German:** research/international work may run in English, but for the German food industry and the domestic market, German makes a big difference.
4. **Plan early:** uni-assist, language proof, Sperrkonto and deadlines can cost you a year.

When you decide, look not just at the feeling of "agriculture/food interests me," but at **which sub-field will make you employable**.

*This article was prepared in early 2026. Tuition fees, application conditions, the Sperrkonto amount, Blue Card salary thresholds and market figures vary by state, university and year. Always verify the current information from the relevant university and official bodies before applying.*
MD;

        $variants = [
            'tr' => ['slug'=>'studying-agriculture-and-food-science-in-germany-as-a-foreigner',    'title'=>'Almanya\'da Tarım & Gıda Bilimleri Okumak: Rehber (2026)', 'excerpt'=>'Almanya\'da tarım ve gıda bilimleri okumak: Agrarwissenschaften/Lebensmittelwissenschaft kapsamı, Almanca bachelor vs İngilizce master, tepe okullar (Hohenheim/TUM Weihenstephan/Göttingen), uni-assist başvurusu, sağlam ve büyüyen sektör (gıda sanayi/agtech), ücret & burs ve Blue Card (2026).', 'meta_title'=>'Almanya\'da Tarım & Gıda Bilimleri Okumak (2026)', 'meta_description'=>'Almanya\'da tarım & gıda bilimleri okumak: Almanca bachelor vs İngilizce master, Hohenheim/TUM Weihenstephan/Göttingen, başvuru, ücret ve büyüyen sektör (2026).', 'body'=>$trBody],
            'de' => ['slug'=>'studying-agriculture-and-food-science-in-germany-as-a-foreigner-de', 'title'=>'Agrar- & Lebensmittelwissenschaften in Deutschland studieren: Leitfaden (2026)', 'excerpt'=>'Agrar- und Lebensmittelwissenschaften in Deutschland studieren: Agrarwissenschaften/Lebensmittelwissenschaft, deutscher Bachelor vs. englischer Master, Top-Unis (Hohenheim/TUM Weihenstephan/Göttingen), uni-assist-Bewerbung, solider und wachsender Sektor (Lebensmittelindustrie/Agtech), Kosten & Stipendien und Blue Card (2026).', 'meta_title'=>'Agrar- & Lebensmittelwissenschaften in Deutschland studieren (2026)', 'meta_description'=>'Agrar & Lebensmittel in Deutschland studieren: Bachelor vs. Master, Hohenheim/TUM Weihenstephan/Göttingen, Bewerbung, Kosten und wachsender Sektor (2026).', 'body'=>$deBody],
            'en' => ['slug'=>'studying-agriculture-and-food-science-in-germany-as-a-foreigner-en', 'title'=>'Studying Agriculture & Food Science in Germany: A Guide (2026)', 'excerpt'=>'Studying agriculture and food sciences in Germany: Agrarwissenschaften/Lebensmittelwissenschaft scope, German bachelor vs English master, top universities (Hohenheim/TUM Weihenstephan/Göttingen), uni-assist application, a solid and growing sector (food industry/agtech), fees & scholarships and the Blue Card (2026).', 'meta_title'=>'Studying Agriculture & Food Science in Germany (2026)', 'meta_description'=>'Studying agriculture & food science in Germany: bachelor vs master, Hohenheim/TUM Weihenstephan/Göttingen, applying, fees and a growing sector (2026).', 'body'=>$enBody],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale'=>$locale, 'translation_group_id'=>$groupId, 'user_id'=>$userId, 'category_id'=>$categoryId,
                'title'=>$v['title'], 'excerpt'=>Str::limit($v['excerpt'],250,'…'),
                'content_md'=>$v['body'], 'content_html'=>$html,
                'meta_title'=>$v['meta_title'], 'meta_description'=>Str::limit($v['meta_description'],158,'…'),
                'reading_minutes'=>max(1,(int)round(str_word_count(strip_tags($html))/200)),
                'is_published'=>true, 'published_at'=>now(),
            ];
            $existing = Post::where('slug', $v['slug'])->first();
            $existing ? $existing->update($payload) : Post::create($payload + ['slug'=>$v['slug']]);
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'studying-agriculture-and-food-science-in-germany-as-a-foreigner',
            'studying-agriculture-and-food-science-in-germany-as-a-foreigner-de',
            'studying-agriculture-and-food-science-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};

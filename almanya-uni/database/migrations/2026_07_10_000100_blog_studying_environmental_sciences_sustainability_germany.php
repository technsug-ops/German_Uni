<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Çevre Bilimleri & Sürdürülebilirlik okumak (2026).
 * Doğrulandı: Umweltwissenschaften/Nachhaltigkeit disiplinlerarası; bachelor genelde Almanca (C1),
 * İngilizce master bol; kamu ~150-350€/dönem (BW non-EU ~1.500€); tepe okullar Leuphana/Freiburg/Oldenburg (PPRE);
 * başvuru uni-assist; Energiewende + ESG ile yeşil sektör büyüyor; Blue Card 2026 ~50.700€ / darboğaz ~45.934€.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'e5f10000-1111-4c1f-9f20-ee0cff12cc01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Çevre ve iklim, 21. yüzyılın en büyük meselesi; Almanya da bu dönüşümün Avrupa'daki merkezlerinden biri. **Umweltwissenschaften** (çevre bilimleri) ve **Nachhaltigkeit** (sürdürülebilirlik) alanları, doğa bilimini politika ve yönetimle birleştiren disiplinlerarası bir yapı sunuyor. Üstelik kamu üniversiteleri neredeyse **ücretsiz**. Bu yazıda bir yabancı öğrenci olarak Almanya'da çevre bilimleri ve sürdürülebilirlik okumanın nasıl işlediğini dürüstçe anlatıyorum.

## Alan kapsamı: disiplinlerarası bir şemsiye

Çevre & sürdürülebilirlik, tek bir bölüm değil; birbirine yakın birçok programın oluşturduğu geniş bir şemsiye:

- **Umweltwissenschaften (çevre bilimleri):** ekoloji, iklim, su/toprak, kirlilik — doğa bilimi ağırlıklı.
- **Nachhaltigkeit (sürdürülebilirlik):** çevre + toplum + ekonomiyi birlikte ele alan bütüncül yaklaşım.
- **Environmental Management & Governance:** çevre yönetimi, politika, hukuk ve karar süreçleri.
- **Renewable Energy (yenilenebilir enerji):** rüzgar, güneş, enerji sistemleri — **Energiewende**'nin teknik kalbi.
- **Sustainable Resource Management / döngüsel ekonomi:** kaynak, atık, malzeme döngüleri.

Kritik nokta: bu alan **disiplinlerarası**. Doğa bilimi (ölçüm, ekoloji), sosyal bilim (politika, ekonomi) ve yönetim (proje, danışmanlık) bir arada gelir. Bu hem büyük bir esneklik hem de bir risk — çünkü çok genel kalırsan mezuniyette **uzmanlaşma** sorunuyla karşılaşırsın (buna aşağıda döneceğim).

Pratikte bu şu demek: aynı "sürdürülebilirlik" başlığı altında biri iklim verisi analiz ederken, bir diğeri şirketlerin karbon raporlarını hazırlıyor, bir başkası da rüzgar santrali projesi yönetiyor olabilir. Seçtiğin program ve aldığın modüller, mezuniyette hangi işe yakın duracağını büyük ölçüde belirler; bu yüzden başvurudan önce ders kataloğunu (Modulhandbuch) baştan sona dikkatle oku ve programın seni teknik mi, yönetsel mi yoksa politika yönüne mi ittiğini gör.

## Bachelor (Almanca) vs İngilizce master

Çoğu uluslararası öğrencinin yanıldığı ayrım burada:

- **Bachelor genelde Almanca yürür.** *2025/2026 itibarıyla, yaklaşık; doğrula* — çevre/sürdürülebilirlik alanında İngilizce lisans **sınırlıdır**. Lisans için pratikte **Almanca C1** (TestDaF / DSH) beklenir.
- **İngilizce master ise çok bol.** Özellikle **sürdürülebilirlik, yenilenebilir enerji, çevre yönetimi ve iklim** alanlarında İngilizce yüksek lisans (MSc) programları uluslararası öğrenci için büyük bir çekim noktası.

Yani Almancan yoksa, en gerçekçi yol **master seviyesinde** İngilizce başlamak. Bu rotanın tüm detayını ayrı bir yazıda ele alıyorum: [Almancasız İngilizce çevre & sürdürülebilirlik master programları](/tr/blog/english-taught-environmental-and-sustainability-masters-in-germany).

## Tepe & tanınan okullar

Alanda öne çıkan üniversiteler (güçlü oldukları yöne göre, kabaca):

| Üniversite | Öne çıkan alan | Not |
|---|---|---|
| **Leuphana Lüneburg** | Sürdürülebilirlik | Almanya'da sürdürülebilirlik eğitiminin öncüsü |
| **Freiburg** | Çevre & ormancılık | Çevre bilimleri geleneği güçlü |
| **Oldenburg** | Yenilenebilir enerji | **PPRE** — ünlü İngilizce Renewable Energy master'ı |
| **Kassel / Göttingen** | Sürdürülebilir tarım, kaynak | Uygulamalı, disiplinlerarası |
| **TU München / Bonn** | Çevre & yaşam bilimleri | Güçlü araştırma altyapısı |
| **Potsdam / Kiel / Leipzig** | İklim, deniz, çevre | İklim/araştırma odaklı |

*2025/2026 itibarıyla, yaklaşık; sıralamalar alt-alana göre değişir, kendi programını doğrula.* Sıralama ve prestij Almanya'da nasıl işler, "kötü okul" var mı — bunu [Almanya'da üniversite prestiji ve sıralamalar nasıl işler](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) yazısında dürüstçe anlattım.

## Başvuru: uni-assist ve süreç

Başvuru süreci kabaca şöyle:

- **uni-assist:** birçok üniversite yabancı başvurularını buradan toplar (belge/diploma denkliği ön-kontrolü). Erken hesap aç, belgeleri hazırla.
- **Dil kanıtı:** bachelor için Almanca C1; İngilizce master için IELTS/TOEFL. Bazı master'lar ilgili bir lisans altyapısı (doğa bilimi, mühendislik, ekonomi) da ister.
- **Motivasyon & CV:** disiplinlerarası programlar, neden bu alanı istediğini net anlatan bir niyet mektubuna değer verir.
- **Dönemler:** kış dönemi başvuruları genelde yaz aylarında kapanır. Erken başla.

## Yeşil sektör neden büyüyor

Bu alanın en güçlü kozu, mezun olduğun sektörün **büyüyor** olması:

- **Energiewende:** Almanya'nın enerji dönüşümü — rüzgar ve güneşe büyük yatırım. Yenilenebilir enerji, en hızlı büyüyen istihdam alanlarından.
- **Kurumsal sürdürülebilirlik / ESG-CSR:** şirketler raporlama ve sürdürülebilirlik hedefleri için giderek daha çok uzman arıyor — talep artıyor.
- **Kamu & NGO:** Umweltamt (çevre daireleri), belediyeler, sivil toplum; politika ve iklim alanında istikrarlı ama rekabetçi.
- **Danışmanlık & araştırma:** çevre danışmanlığı, iklim modelleme, döngüsel ekonomi projeleri.

Kısacası talep gerçek; ancak bu talep alt-alanlara eşit dağılmıyor. Teknik yenilenebilir enerji ve kurumsal ESG tarafı, NGO ve politikaya göre genelde daha çok ve daha iyi ücretli pozisyon açar. Bu yüzden "çevre için çalışmak istiyorum" hissini, işverenin gerçekten aradığı somut bir beceriye (enerji sistemleri, karbon muhasebesi, çevre mevzuatı, GIS/veri) bağlaman kariyer şansını ciddi biçimde artırır.

Yenilenebilir enerji tarafı mühendisliğe komşu olduğundan, teknik profildeysen [Almanya'da mühendis olarak çalışmak ve Blue Card maaşı](/tr/blog/working-as-an-engineer-in-germany-blue-card-salary) yazısı da işine yarar. Yeşil kariyerin tüm sektörlerini ve maaşlarını ayrı bir yazıda derinlemesine ele alıyorum: [Almanya'da yeşil kariyer: sürdürülebilirlik ve yenilenebilir enerjide çalışmak](/tr/blog/green-careers-working-in-sustainability-and-renewable-energy-in-germany).

## Ücret, burs ve mezuniyet sonrası

- **Harç:** kamu üniversitelerinde **öğrenim ücreti yok**; sadece dönemlik katkı ~**150–350€** (semester ticket dâhil olabilir). İstisna: **Baden-Württemberg**, AB dışı öğrencilerden ~**1.500€/dönem** alır. *2025/2026 itibarıyla, yaklaşık; doğrula.*
- **Sperrkonto (bloke hesap):** vize için genelde ~**992€/ay = ~11.904€/yıl** göstermen istenir. *2025/2026 itibarıyla, yaklaşık; resmî kaynaktan doğrula.*
- **Burs:** **DAAD** en bilinen kaynak; ayrıca Deutschlandstipendium ve vakıf bursları.
- **Blue Card (mezuniyet sonrası iş):** 2026 için genel maaş eşiği ~**50.700€/yıl**; darboğaz meslek / yeni mezun eşiği ~**45.934€/yıl**. *Yaklaşık; resmî kaynaktan doğrula.* Master mı, iş-arama vizesi mi ikilemindeysen [Almanya'da master vs iş-arama vizesi](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) yazısı kariyer planını netleştirir.

Bu diplomayla iş piyasasında somut olarak ne yapabileceğini ise [çevre/sürdürülebilirlik diplomasıyla iş piyasası](/tr/blog/what-to-do-with-an-environmental-sustainability-degree-in-germany-job-market) yazısında ele alıyorum.

## Sonuç & dürüst tavsiye

Almanya'da çevre bilimleri ve sürdürülebilirlik okumak **maliyet açısından çok cazip**, sektör büyüyor ve İngilizce master seçeneği bol. Dürüst tavsiyem:

1. **Almancan yoksa master hedefle.** İngilizce lisans sınırlı; İngilizce MSc bol.
2. **Baştan bir yönde uzmanlaş:** yenilenebilir enerji mi, ESG/kurumsal mı, politika mı, danışmanlık mı? Çok genel bir diploma iş aramada dezavantaj olabilir.
3. **Almancayı ihmal etme:** araştırma/uluslararası tarafta İngilizce yeterli olsa da, kurumsal ve kamu işleri için Almanca büyük fark yaratır.
4. **Erken planla:** uni-assist, dil kanıtı, Sperrkonto ve başvuru dönemleri seni yıl kaybettirebilir.

Kararını verirken sadece "çevreye faydalı olmak" duygusuna değil, **hangi alt-alanın seni istihdam edilebilir kılacağına** bak.

*Bu yazı 2026 başı itibarıyla hazırlanmıştır. Öğrenim ücretleri, başvuru koşulları, Sperrkonto tutarı, Blue Card maaş eşikleri ve piyasa rakamları eyalete, üniversiteye ve yıla göre değişir. Başvurmadan önce ilgili üniversitenin ve resmî kurumların güncel bilgilerini mutlaka doğrula.*
MD;

        $deBody = <<<'MD'
Umwelt und Klima sind das große Thema des 21. Jahrhunderts — und Deutschland ist eines der europäischen Zentren dieses Wandels. Die Bereiche **Umweltwissenschaften** und **Nachhaltigkeit** verbinden Naturwissenschaft mit Politik und Management zu einem interdisziplinären Feld. Und die staatlichen Universitäten sind praktisch **kostenlos**. In diesem Artikel erkläre ich dir ehrlich, wie ein Studium der Umweltwissenschaften und Nachhaltigkeit in Deutschland als internationale:r Studierende:r funktioniert.

## Das Feld: ein interdisziplinäres Dach

Umwelt & Nachhaltigkeit ist kein einzelnes Fach, sondern ein breites Dach aus vielen verwandten Programmen:

- **Umweltwissenschaften:** Ökologie, Klima, Wasser/Boden, Verschmutzung — naturwissenschaftlich geprägt.
- **Nachhaltigkeit:** ganzheitlicher Ansatz aus Umwelt, Gesellschaft und Wirtschaft.
- **Environmental Management & Governance:** Umweltmanagement, Politik, Recht und Entscheidungsprozesse.
- **Renewable Energy (erneuerbare Energien):** Wind, Solar, Energiesysteme — das technische Herz der **Energiewende**.
- **Sustainable Resource Management / Kreislaufwirtschaft:** Ressourcen, Abfall, Materialkreisläufe.

Entscheidend: Dieses Feld ist **interdisziplinär**. Naturwissenschaft (Messung, Ökologie), Sozialwissenschaft (Politik, Wirtschaft) und Management (Projekte, Beratung) kommen zusammen. Das ist große Flexibilität, aber auch ein Risiko — denn wenn du zu allgemein bleibst, stößt du beim Abschluss auf das Problem der **Spezialisierung** (dazu unten mehr).

Praktisch heißt das: Unter demselben Titel "Nachhaltigkeit" analysiert die eine Person Klimadaten, die andere erstellt CO2-Berichte für Unternehmen, und eine dritte leitet ein Windpark-Projekt. Das gewählte Programm und deine Module bestimmen weitgehend, welcher Job dir nach dem Abschluss nahe liegt; lies daher vor der Bewerbung das Modulhandbuch gründlich und erkenne, ob dich das Programm eher in die technische, die managementbezogene oder die politische Richtung drängt.

## Bachelor (auf Deutsch) vs. englischsprachiger Master

Hier täuschen sich viele internationale Studierende:

- **Der Bachelor läuft meist auf Deutsch.** *Stand 2025/2026, ungefähr; bitte prüfen* — englischsprachige Bachelorprogramme in Umwelt/Nachhaltigkeit sind **begrenzt**. Für den Bachelor wird praktisch **Deutsch C1** (TestDaF / DSH) erwartet.
- **Englischsprachige Master gibt es dagegen reichlich.** Besonders in **Nachhaltigkeit, erneuerbaren Energien, Umweltmanagement und Klima** sind englischsprachige Master (MSc) ein großer Anziehungspunkt für Internationale.

Ohne Deutsch ist der realistischste Weg also ein Einstieg auf **Masterebene** auf Englisch. Alle Details dazu im Artikel [Englischsprachige Umwelt- & Nachhaltigkeits-Master in Deutschland](/de/blog/english-taught-environmental-and-sustainability-masters-in-germany-de).

## Top- und anerkannte Universitäten

Herausragende Universitäten im Feld (grob nach Stärken):

| Universität | Stärke | Hinweis |
|---|---|---|
| **Leuphana Lüneburg** | Nachhaltigkeit | Vorreiter der Nachhaltigkeitslehre in Deutschland |
| **Freiburg** | Umwelt & Forst | starke umweltwissenschaftliche Tradition |
| **Oldenburg** | erneuerbare Energien | **PPRE** — berühmter englischsprachiger Renewable-Energy-Master |
| **Kassel / Göttingen** | nachhaltige Landwirtschaft, Ressourcen | angewandt, interdisziplinär |
| **TU München / Bonn** | Umwelt- & Lebenswissenschaften | starke Forschungsinfrastruktur |
| **Potsdam / Kiel / Leipzig** | Klima, Meer, Umwelt | klima-/forschungsorientiert |

*Stand 2025/2026, ungefähr; Rankings variieren je nach Teilgebiet, prüfe dein konkretes Programm.* Wie Prestige und Rankings in Deutschland funktionieren und ob es "schlechte Unis" gibt, erkläre ich ehrlich in [Wie Uni-Prestige und Rankings in Deutschland funktionieren](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de).

## Bewerbung: uni-assist und Ablauf

Der Ablauf grob:

- **uni-assist:** viele Universitäten bündeln hier internationale Bewerbungen (Vorprüfung von Zeugnissen/Abschlüssen). Lege früh ein Konto an und bereite die Unterlagen vor.
- **Sprachnachweis:** Deutsch C1 für den Bachelor; IELTS/TOEFL für den englischen Master. Manche Master verlangen einen fachlichen Hintergrund (Naturwissenschaft, Ingenieurwesen, Wirtschaft).
- **Motivation & CV:** interdisziplinäre Programme legen Wert auf ein Motivationsschreiben, das klar zeigt, warum du dieses Feld willst.
- **Fristen:** Bewerbungen fürs Wintersemester schließen oft im Sommer. Fang früh an.

## Warum der grüne Sektor wächst

Der größte Trumpf dieses Feldes: Der Sektor, in den du hineinwächst, **wächst**:

- **Energiewende:** Deutschlands Energiewandel — große Investitionen in Wind und Solar. Erneuerbare Energien gehören zu den am schnellsten wachsenden Beschäftigungsfeldern.
- **Unternehmensnachhaltigkeit / ESG-CSR:** Unternehmen suchen für Reporting und Nachhaltigkeitsziele immer mehr Fachleute — die Nachfrage steigt.
- **Öffentlicher Sektor & NGO:** Umweltämter, Kommunen, Zivilgesellschaft; im Bereich Politik und Klima stabil, aber kompetitiv.
- **Beratung & Forschung:** Umweltberatung, Klimamodellierung, Kreislaufwirtschaftsprojekte.

Kurz: Die Nachfrage ist real; aber sie verteilt sich nicht gleichmäßig auf die Teilgebiete. Technische erneuerbare Energien und Unternehmens-ESG schaffen in der Regel mehr und besser bezahlte Stellen als NGOs und Politik. Wenn du das Gefühl "Ich will für die Umwelt arbeiten" an eine konkrete Fähigkeit koppelst, die Arbeitgeber wirklich suchen (Energiesysteme, CO2-Bilanzierung, Umweltrecht, GIS/Daten), steigen deine Karrierechancen deutlich.

Da die erneuerbaren Energien nah am Ingenieurwesen liegen, hilft dir bei technischem Profil auch [Als Ingenieur:in in Deutschland arbeiten und Blue-Card-Gehalt](/de/blog/working-as-an-engineer-in-germany-blue-card-salary-de). Alle Branchen und Gehälter grüner Karrieren behandle ich ausführlich in [Grüne Karriere in Deutschland: in Nachhaltigkeit und erneuerbaren Energien arbeiten](/de/blog/green-careers-working-in-sustainability-and-renewable-energy-in-germany-de).

## Kosten, Stipendien und nach dem Abschluss

- **Gebühren:** an staatlichen Universitäten gibt es **keine Studiengebühren**; nur ein Semesterbeitrag von ~**150–350€** (ggf. inkl. Semesterticket). Ausnahme: **Baden-Württemberg** verlangt von Nicht-EU-Studierenden ~**1.500€/Semester**. *Stand 2025/2026, ungefähr; bitte prüfen.*
- **Sperrkonto:** fürs Visum musst du meist ~**992€/Monat = ~11.904€/Jahr** nachweisen. *Stand 2025/2026, ungefähr; aus offizieller Quelle prüfen.*
- **Stipendien:** **DAAD** ist die bekannteste Quelle; außerdem das Deutschlandstipendium und Stiftungsstipendien.
- **Blue Card (Job nach dem Abschluss):** allgemeine Gehaltsschwelle 2026 ~**50.700€/Jahr**; Engpassberufe / Berufseinsteiger:innen ~**45.934€/Jahr**. *Ungefähr; aus offizieller Quelle prüfen.* Wenn du zwischen Master und Jobsuchevisum schwankst, klärt [Master vs. Jobsuchevisum in Deutschland](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de) deine Karriereplanung.

Was du mit diesem Abschluss konkret auf dem Arbeitsmarkt machen kannst, behandle ich in [Was tun mit einem Umwelt-/Nachhaltigkeitsabschluss in Deutschland](/de/blog/what-to-do-with-an-environmental-sustainability-degree-in-germany-job-market-de).

## Fazit & ehrlicher Rat

Umweltwissenschaften und Nachhaltigkeit in Deutschland zu studieren ist **kostenmäßig sehr attraktiv**, der Sektor wächst und englische Master gibt es reichlich. Mein ehrlicher Rat:

1. **Ohne Deutsch: ziele auf den Master.** Englische Bachelor sind begrenzt, englische MSc reichlich.
2. **Spezialisiere dich früh:** erneuerbare Energien, ESG/Unternehmen, Politik oder Beratung? Ein zu allgemeiner Abschluss kann bei der Jobsuche ein Nachteil sein.
3. **Vernachlässige Deutsch nicht:** in Forschung/international reicht Englisch, aber für Unternehmen und öffentlichen Dienst macht Deutsch einen großen Unterschied.
4. **Plane früh:** uni-assist, Sprachnachweis, Sperrkonto und Fristen können dich ein Jahr kosten.

Schau bei deiner Entscheidung nicht nur auf das Gefühl, "der Umwelt zu helfen", sondern darauf, **welches Teilgebiet dich beschäftigungsfähig macht**.

*Dieser Artikel wurde Anfang 2026 erstellt. Studiengebühren, Bewerbungsbedingungen, Sperrkonto-Betrag, Blue-Card-Gehaltsschwellen und Marktzahlen variieren je nach Bundesland, Universität und Jahr. Prüfe vor der Bewerbung unbedingt die aktuellen Angaben der jeweiligen Universität und offizieller Stellen.*
MD;

        $enBody = <<<'MD'
Environment and climate are the defining issue of the 21st century — and Germany is one of Europe's centres of that transition. The fields of **Umweltwissenschaften** (environmental sciences) and **Nachhaltigkeit** (sustainability) combine natural science with policy and management into an interdisciplinary whole. And public universities are practically **free**. In this article I explain honestly how studying environmental sciences and sustainability in Germany works as an international student.

## The field: an interdisciplinary umbrella

Environment & sustainability isn't a single subject; it's a broad umbrella of many related programs:

- **Umweltwissenschaften (environmental sciences):** ecology, climate, water/soil, pollution — natural-science heavy.
- **Nachhaltigkeit (sustainability):** a holistic approach spanning environment, society and economy.
- **Environmental Management & Governance:** environmental management, policy, law and decision-making.
- **Renewable Energy:** wind, solar, energy systems — the technical heart of the **Energiewende**.
- **Sustainable Resource Management / circular economy:** resources, waste, material cycles.

The key point: this field is **interdisciplinary**. Natural science (measurement, ecology), social science (policy, economics) and management (projects, consulting) come together. That's great flexibility, but also a risk — because if you stay too general, you'll hit the problem of **specialisation** at graduation (more on that below).

In practice this means: under the same "sustainability" label, one person analyses climate data, another prepares companies' carbon reports, and a third manages a wind-farm project. The program you choose and the modules you take largely determine which job you'll be close to at graduation; so before applying, read the module catalogue (Modulhandbuch) carefully and see whether the program pushes you toward the technical, the managerial or the policy direction.

## Bachelor's (in German) vs English-taught master's

Here's where many international students get it wrong:

- **The bachelor's is mostly taught in German.** *As of 2025/2026, approximate; verify* — English-taught bachelor's in environment/sustainability are **limited**. For a bachelor's you're effectively expected to have **German C1** (TestDaF / DSH).
- **English-taught master's, on the other hand, are plentiful.** Especially in **sustainability, renewable energy, environmental management and climate**, English-taught master's (MSc) are a major draw for international students.

So without German, the most realistic path is starting at **master's level** in English. I cover this route in full in a separate article: [English-taught environmental & sustainability master's in Germany](/en/blog/english-taught-environmental-and-sustainability-masters-in-germany-en).

## Top & recognised universities

Standout universities in the field (roughly by their strengths):

| University | Strength | Note |
|---|---|---|
| **Leuphana Lüneburg** | Sustainability | pioneer of sustainability education in Germany |
| **Freiburg** | Environment & forestry | strong environmental-science tradition |
| **Oldenburg** | Renewable energy | **PPRE** — famous English-taught Renewable Energy master's |
| **Kassel / Göttingen** | Sustainable agriculture, resources | applied, interdisciplinary |
| **TU Munich / Bonn** | Environmental & life sciences | strong research infrastructure |
| **Potsdam / Kiel / Leipzig** | Climate, marine, environment | climate/research-oriented |

*As of 2025/2026, approximate; rankings vary by sub-field, verify your specific program.* How prestige and rankings actually work in Germany, and whether "bad universities" exist, I explain honestly in [How university prestige and rankings work in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en).

## Applying: uni-assist and the process

The process, roughly:

- **uni-assist:** many universities collect international applications here (pre-checking certificates/degrees). Open an account early and prepare your documents.
- **Language proof:** German C1 for the bachelor's; IELTS/TOEFL for the English master's. Some master's also require a relevant background (natural science, engineering, economics).
- **Motivation & CV:** interdisciplinary programs value a statement that clearly shows why you want this field.
- **Deadlines:** winter-semester applications often close in summer. Start early.

## Why the green sector is growing

The biggest advantage of this field is that the sector you graduate into is **growing**:

- **Energiewende:** Germany's energy transition — major investment in wind and solar. Renewable energy is among the fastest-growing employment areas.
- **Corporate sustainability / ESG-CSR:** companies increasingly need specialists for reporting and sustainability targets — demand is rising.
- **Public sector & NGOs:** environmental agencies (Umweltamt), municipalities, civil society; stable but competitive in policy and climate.
- **Consulting & research:** environmental consulting, climate modelling, circular-economy projects.

Since the renewable-energy side sits close to engineering, if you have a technical profile [Working as an engineer in Germany and the Blue Card salary](/en/blog/working-as-an-engineer-in-germany-blue-card-salary-en) is useful too. I cover all the sectors and salaries of green careers in depth in [Green careers in Germany: working in sustainability and renewable energy](/en/blog/green-careers-working-in-sustainability-and-renewable-energy-in-germany-en).

## Fees, scholarships and after graduation

- **Fees:** public universities charge **no tuition**; only a semester contribution of ~**€150–350** (may include a semester ticket). Exception: **Baden-Württemberg** charges non-EU students ~**€1,500/semester**. *As of 2025/2026, approximate; verify.*
- **Sperrkonto (blocked account):** for the visa you're usually asked to show ~**€992/month = ~€11,904/year**. *As of 2025/2026, approximate; verify from official sources.*
- **Scholarships:** **DAAD** is the best-known source; also the Deutschlandstipendium and foundation scholarships.
- **Blue Card (job after graduation):** the 2026 general salary threshold is ~**€50,700/year**; the shortage-occupation / new-graduate threshold is ~**€45,934/year**. *Approximate; verify from official sources.* If you're torn between a master's and a job-seeker visa, [Germany master's vs job-seeker visa](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en) will clarify your career plan.

What you can concretely do with this degree on the job market I cover in [What to do with an environmental/sustainability degree in Germany](/en/blog/what-to-do-with-an-environmental-sustainability-degree-in-germany-job-market-en).

## Conclusion & honest advice

Studying environmental sciences and sustainability in Germany is **very attractive cost-wise**, the sector is growing, and English-taught master's are plentiful. My honest advice:

1. **Without German, target the master's.** English bachelor's are limited; English MSc are plentiful.
2. **Specialise early:** renewable energy, ESG/corporate, policy or consulting? An overly general degree can be a disadvantage in the job hunt.
3. **Don't neglect German:** research/international work may run in English, but for corporate and public-sector jobs German makes a big difference.
4. **Plan early:** uni-assist, language proof, Sperrkonto and deadlines can cost you a year.

When you decide, look not just at the feeling of "helping the environment," but at **which sub-field will make you employable**.

*This article was prepared in early 2026. Tuition fees, application conditions, the Sperrkonto amount, Blue Card salary thresholds and market figures vary by state, university and year. Always verify the current information from the relevant university and official bodies before applying.*
MD;

        $variants = [
            'tr' => ['slug'=>'studying-environmental-sciences-and-sustainability-in-germany-as-a-foreigner',    'title'=>'Almanya\'da Çevre Bilimleri & Sürdürülebilirlik Okumak: Rehber (2026)', 'excerpt'=>'Almanya\'da çevre bilimleri ve sürdürülebilirlik okumak: disiplinlerarası yapı, Almanca bachelor vs İngilizce master, tepe okullar (Leuphana/Freiburg/Oldenburg PPRE), uni-assist başvurusu, büyüyen yeşil sektör, ücret & burs ve Blue Card (2026).', 'meta_title'=>'Almanya\'da Çevre Bilimleri & Sürdürülebilirlik Okumak (2026)', 'meta_description'=>'Almanya\'da çevre & sürdürülebilirlik okumak: Almanca bachelor vs İngilizce master, Leuphana/Freiburg/Oldenburg, başvuru, ücret ve büyüyen yeşil sektör (2026).', 'body'=>$trBody],
            'de' => ['slug'=>'studying-environmental-sciences-and-sustainability-in-germany-as-a-foreigner-de', 'title'=>'Umweltwissenschaften & Nachhaltigkeit in Deutschland studieren: Leitfaden (2026)', 'excerpt'=>'Umweltwissenschaften und Nachhaltigkeit in Deutschland studieren: interdisziplinärer Aufbau, deutscher Bachelor vs. englischer Master, Top-Unis (Leuphana/Freiburg/Oldenburg PPRE), uni-assist-Bewerbung, wachsender grüner Sektor, Kosten & Stipendien und Blue Card (2026).', 'meta_title'=>'Umweltwissenschaften & Nachhaltigkeit in Deutschland studieren (2026)', 'meta_description'=>'Umwelt & Nachhaltigkeit in Deutschland studieren: Bachelor vs. Master, Leuphana/Freiburg/Oldenburg, Bewerbung, Kosten und wachsender grüner Sektor (2026).', 'body'=>$deBody],
            'en' => ['slug'=>'studying-environmental-sciences-and-sustainability-in-germany-as-a-foreigner-en', 'title'=>'Studying Environmental Sciences & Sustainability in Germany: A Guide (2026)', 'excerpt'=>'Studying environmental sciences and sustainability in Germany: interdisciplinary structure, German bachelor vs English master, top universities (Leuphana/Freiburg/Oldenburg PPRE), uni-assist application, the growing green sector, fees & scholarships and the Blue Card (2026).', 'meta_title'=>'Studying Environmental Sciences & Sustainability in Germany (2026)', 'meta_description'=>'Studying environment & sustainability in Germany: bachelor vs master, Leuphana/Freiburg/Oldenburg, applying, fees and the growing green sector (2026).', 'body'=>$enBody],
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
            'studying-environmental-sciences-and-sustainability-in-germany-as-a-foreigner',
            'studying-environmental-sciences-and-sustainability-in-germany-as-a-foreigner-de',
            'studying-environmental-sciences-and-sustainability-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};

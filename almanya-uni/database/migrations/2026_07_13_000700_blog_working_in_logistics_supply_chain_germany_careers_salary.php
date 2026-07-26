<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da lojistik & tedarik zincirinde çalışmak — kariyer & maaş (2026).
 * Doğrulandı: Almanya Avrupa'nın #1 lojistik merkezi (DHL/Kühne+Nagel/DB Schenker/DACHSER); SCM/lojistik
 * giriş maaşı ~45-55k€ (yaklaşık, doğrula); Blue Card 2026 genel ~50.700€, darboğaz/yeni-mezun ~45.934€.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'f3a30000-3333-4d8f-9f90-ff13aa19dd03';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya, Avrupa'nın **1 numaralı lojistik merkezi**: merkezi coğrafi konumu, dev limanları, otoban ağı ve **Deutsche Post DHL, Kühne+Nagel, DB Schenker, DACHSER** gibi küresel devleriyle tedarik zinciri profesyonelleri için belki de kıtanın en canlı iş piyasasını sunar. Peki bir uluslararası öğrenci olarak lojistik veya **Supply Chain Management (SCM)** diplomasıyla burada nasıl çalışırsın, hangi sektörler seni ister ve gerçekçi maaş ne kadar? Dürüstçe anlatalım.

## Hangi sektörler lojistik & SCM mezunu arıyor?

Bu alanın en büyük avantajı istihdam çeşitliliği. Diploma seni tek bir kutuya sıkıştırmaz:

- **Lojistik hizmet şirketleri (3PL/4PL):** DHL, Kühne+Nagel, DB Schenker, DACHSER, Hermes — nakliye, depo yönetimi, freight forwarding.
- **Üretimde SCM:** Otomotiv (VW, BMW, Bosch, Continental), perakende ve endüstriyel üreticiler için planlama, üretim lojistiği, malzeme akışı.
- **Satın alma / procurement:** Tedarikçi yönetimi, sözleşme, sourcing — hemen her büyük şirkette.
- **E-ticaret & fulfillment:** Amazon, Zalando ve büyüyen online perakende için depo/dağıtım operasyonları.
- **Danışmanlık:** Tedarik zinciri optimizasyonu (büyük ve niş danışmanlıklar).
- **Dijital SCM / lojistik teknolojisi:** Yazılım, veri, otomasyon odaklı, hızla büyüyen segment.

## Dijital tedarik zinciri büyüyor

Sektörün en heyecanlı kısmı burası. Talep tahmini, rota optimizasyonu, depo otomasyonu, sürdürülebilirlik (yeşil lojistik) ve tedarik zinciri görünürlüğü — hepsi **veri ve yazılımla** yürüyor. SAP, analitik ve otomasyon becerileri olan mezunlar öne çıkıyor. Bu nedenle SCM ile [veri bilimi ve yapay zekâ dünyasına giriş](/tr/blog/how-to-break-into-data-science-ai-in-germany) arasındaki köprü giderek değerleniyor; teknik tarafa yakın duran adaylar daha yüksek maaş potansiyeline sahip.

## Tipik roller

| Rol | Ne yapar | Not |
|---|---|---|
| Supply Chain Analyst | Veri analizi, talep planlama, KPI | Analitik/dijital tarafın kapısı |
| Operations / Logistics Manager | Depo, nakliye, saha operasyonu | Talepkar, hızlı tempolu |
| Procurement / Purchasing Specialist | Tedarikçi & sourcing yönetimi | Almanca çoğu zaman avantaj |
| Demand / Supply Planner | Envanter & tahmin | SAP/Excel/analitik ağırlıklı |
| SCM Consultant | Süreç optimizasyonu | Danışmanlık, seyahat yoğun |

## Maaş: gerçekçi rakamlar (yaklaşık)

Rakamlar şirkete, şehre, sektöre ve tecrübeye göre değişir — bunları **kaba aralık** olarak al ve mutlaka güncel ilanlarla doğrula.

| Seviye | Yıllık brüt (yaklaşık) |
|---|---|
| Giriş (yeni mezun) | ~45.000 – 55.000 € |
| 3-5 yıl tecrübe | ~55.000 – 70.000 € |
| Kıdemli / yönetici | ~70.000 € ve üzeri |

Otomotiv ve ilaç gibi güçlü sektörler ile danışmanlık üst bandı zorlar; saf depo/operasyon rolleri giriş bandının altında kalabilir. Mühendislik ağırlıklı SCM (Wirtschaftsingenieur) rollerinde maaşlar genelde [Almanya'da mühendis olarak çalışmak ve Blue Card maaş eşikleri](/tr/blog/working-as-an-engineer-in-germany-blue-card-salary) yazısındaki mühendis seviyelerine yaklaşır.

Şehir de fark yaratır: Münih, Stuttgart ve Frankfurt gibi merkezlerde maaşlar daha yüksek ama yaşam maliyeti de ağır; Ruhr bölgesi veya doğu Almanya'da rakam düşse de alım gücü genelde dengelenir. Yıllık ikramiye (13. maaş), şirket aracı ve prim gibi ek kalemler de toplam paketi ciddi etkiler — brüt rakama takılmadan **net + yan haklar** bütününe bak.

## Almanca & Blue Card (2026)

Uluslararası lojistik ve global SCM İngilizce-dostudur; birçok merkez ofis ve danışmanlık İngilizce yürür. Ancak **domestik operasyon, saha ve procurement** rollerinde Almanca ciddi avantaj — hatta çoğu zaman şart. B1-B2 seviyesi iş bulma şansını belirgin artırır.

Vize tarafında 2026 için **Blue Card** eşikleri (yaklaşık, resmî kaynaktan doğrula):

- Genel eşik: **~50.700 € / yıl** brüt.
- Darboğaz meslekler & yeni mezunlar: **~45.934 € / yıl** brüt.

SCM/lojistik yönetimi rollerinin çoğu bu eşikleri karşılayabilir; giriş maaşı düşük kalırsa yeni-mezun/darboğaz indirimi devreye girebilir.

## İş arama & strateji

- Xing ve LinkedIn Almanya'da lojistik/SCM ilanlarının kalbi; şirketlerin kariyer sayfaları ve StepStone da güçlü.
- **Werkstudent / staj** ile daha okurken bir DHL veya üretici içine girmek en etkili yol.
- SAP (özellikle SAP MM/SD/APO), Excel ileri, bir analitik aracı (SQL/Python) ve İngilizce+Almanca kombinasyonu CV'ni öne çıkarır.
- İş teklifi aldıysan süreç [iş teklifiyle Almanya çalışma vizesi: süreç ve hızlı takip](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) yazısındaki adımları izler.

Kariyer resmini tamamlamak için: [Almanya'da lojistik & SCM okumak](/tr/blog/studying-logistics-and-supply-chain-management-in-germany-as-a-foreigner), [İngilizce lojistik/SCM master programları](/tr/blog/english-taught-logistics-and-supply-chain-management-masters-in-germany) ve [lojistik/SCM diplomasıyla iş piyasası](/tr/blog/what-to-do-with-a-logistics-supply-chain-degree-in-germany-job-market).

## Sonuç & dürüst tavsiye

Lojistik & SCM, Almanya'da istihdam-dostu, sağlam ve büyüyen bir alan. Ekosistem dev, dijital SCM yükseliyor ve giriş maaşları makul. Dürüst gerçek: saf operasyon rolleri talepkar ve temposu yüksek olabilir; en iyi getiriyi **uzmanlaşma** (analitik/dijital SCM veya procurement) + **Almanca** + Blue Card eşiğini karşılayan bir rol kombinasyonu verir. Werkstudent'le erken başla, SAP/analitik becerini büyüt, iki dilli ol — kapılar hızla açılır.

*Bu yazıdaki maaş ve Blue Card rakamları 2026 için yaklaşıktır ve değişebilir; başvurudan önce resmî kaynaklardan (Make it in Germany, Bundesagentur für Arbeit) ve güncel iş ilanlarından doğrula.*
MD;
        $deBody = <<<'MD'
Deutschland ist Europas **Logistik-Drehscheibe Nummer eins**: zentrale Lage, große Häfen, dichtes Autobahnnetz und globale Player wie **Deutsche Post DHL, Kühne+Nagel, DB Schenker und DACHSER** machen den Arbeitsmarkt für Supply-Chain-Profis vielleicht zum lebendigsten des Kontinents. Aber wie arbeitest du als internationale:r Absolvent:in mit einem Abschluss in Logistik oder **Supply Chain Management (SCM)** hier — welche Branchen suchen dich und was verdienst du realistisch? Reden wir ehrlich.

## Welche Branchen suchen Logistik- & SCM-Absolvent:innen?

Der größte Vorteil dieses Felds ist die Vielfalt. Dein Abschluss steckt dich nicht in eine einzige Schublade:

- **Logistikdienstleister (3PL/4PL):** DHL, Kühne+Nagel, DB Schenker, DACHSER, Hermes — Transport, Lagerhaltung, Spedition.
- **SCM in der Produktion:** Automobil (VW, BMW, Bosch, Continental), Handel und Industrie — Planung, Produktionslogistik, Materialfluss.
- **Einkauf / Procurement:** Lieferantenmanagement, Verträge, Sourcing — in fast jedem großen Unternehmen.
- **E-Commerce & Fulfillment:** Amazon, Zalando und der wachsende Online-Handel für Lager- und Distributionsbetrieb.
- **Beratung:** Optimierung von Lieferketten (große und spezialisierte Beratungen).
- **Digitales SCM / Logistiktechnologie:** Software, Daten, Automatisierung — ein schnell wachsendes Segment.

## Digitale Lieferketten wachsen

Das ist der spannendste Teil der Branche. Bedarfsprognose, Routenoptimierung, Lagerautomatisierung, Nachhaltigkeit (grüne Logistik) und Supply-Chain-Transparenz laufen alle über **Daten und Software**. Absolvent:innen mit SAP-, Analytics- und Automatisierungskenntnissen stechen heraus. Deshalb wird die Brücke zwischen SCM und dem [Einstieg in Data Science und KI in Deutschland](/de/blog/how-to-break-into-data-science-ai-in-germany-de) immer wertvoller; wer nah an der technischen Seite bleibt, hat mehr Gehaltspotenzial.

## Typische Rollen

| Rolle | Aufgabe | Hinweis |
|---|---|---|
| Supply Chain Analyst | Datenanalyse, Bedarfsplanung, KPI | Tür zur analytischen/digitalen Seite |
| Operations / Logistics Manager | Lager, Transport, Feldbetrieb | Anspruchsvoll, hohes Tempo |
| Procurement / Einkaufsspezialist:in | Lieferanten- & Sourcing-Management | Deutsch oft ein Vorteil |
| Demand / Supply Planner | Bestand & Prognose | SAP/Excel/Analytics-lastig |
| SCM-Berater:in | Prozessoptimierung | Beratung, viel Reisen |

## Gehalt: realistische Zahlen (ungefähr)

Die Zahlen hängen von Unternehmen, Stadt, Branche und Erfahrung ab — nimm sie als **grobe Spanne** und prüfe sie unbedingt an aktuellen Stellenanzeigen.

| Level | Brutto pro Jahr (ungefähr) |
|---|---|
| Einstieg (Absolvent:in) | ~45.000 – 55.000 € |
| 3-5 Jahre Erfahrung | ~55.000 – 70.000 € |
| Senior / Führung | ~70.000 € und mehr |

Starke Branchen wie Automobil und Pharma sowie Beratung reizen das obere Band aus; reine Lager-/Operations-Rollen können unter dem Einstiegsband liegen. In ingenieurnahen SCM-Rollen (Wirtschaftsingenieur) nähern sich die Gehälter oft den Ingenieurstufen aus dem Beitrag [Als Ingenieur:in in Deutschland arbeiten und Blue-Card-Gehalt](/de/blog/working-as-an-engineer-in-germany-blue-card-salary-de).

Auch die Stadt macht einen Unterschied: In Zentren wie München, Stuttgart und Frankfurt sind die Gehälter höher, aber die Lebenshaltungskosten ebenfalls; im Ruhrgebiet oder in Ostdeutschland fällt die Zahl niedriger aus, doch die Kaufkraft gleicht das meist aus. Zusatzleistungen wie Jahresbonus (13. Gehalt), Firmenwagen und Prämien beeinflussen das Gesamtpaket erheblich — schau nicht nur auf brutto, sondern auf **netto + Zusatzleistungen** als Ganzes.

## Deutsch & Blue Card (2026)

Internationale Logistik und globales SCM sind englischfreundlich; viele Zentralen und Beratungen arbeiten auf Englisch. Doch bei **inländischem Betrieb, Feld- und Procurement-Rollen** ist Deutsch ein klarer Vorteil — oft sogar Voraussetzung. Ein Niveau von B1-B2 erhöht deine Chancen deutlich.

Bei der Visafrage gelten für 2026 folgende **Blue-Card**-Schwellen (ungefähr, offiziell prüfen):

- Allgemeine Schwelle: **~50.700 € / Jahr** brutto.
- Engpassberufe & Berufseinsteiger:innen: **~45.934 € / Jahr** brutto.

Die meisten SCM-/Logistik-Management-Rollen können diese Schwellen erreichen; bleibt das Einstiegsgehalt niedrig, greift die Ermäßigung für Einsteiger:innen/Engpassberufe.

## Jobsuche & Strategie

- Xing und LinkedIn sind das Herz der Logistik-/SCM-Anzeigen in Deutschland; auch Karriereseiten der Unternehmen und StepStone sind stark.
- Über **Werkstudent / Praktikum** schon während des Studiums bei DHL oder einem Hersteller einzusteigen, ist der wirksamste Weg.
- SAP (besonders SAP MM/SD/APO), fortgeschrittenes Excel, ein Analysetool (SQL/Python) sowie die Kombination Englisch+Deutsch heben deinen Lebenslauf hervor.
- Hast du ein Jobangebot, folgt der Prozess den Schritten aus [Arbeitsvisum für Deutschland mit Jobangebot: Ablauf und Fast Track](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).

Um das Karrierebild zu vervollständigen: [Logistik & SCM in Deutschland studieren](/de/blog/studying-logistics-and-supply-chain-management-in-germany-as-a-foreigner-de), [englischsprachige Logistik-/SCM-Master](/de/blog/english-taught-logistics-and-supply-chain-management-masters-in-germany-de) und [was du mit einem Logistik-/SCM-Abschluss machst](/de/blog/what-to-do-with-a-logistics-supply-chain-degree-in-germany-job-market-de).

## Fazit & ehrlicher Rat

Logistik & SCM ist in Deutschland ein beschäftigungsfreundliches, solides und wachsendes Feld. Das Ökosystem ist riesig, digitales SCM steigt und die Einstiegsgehälter sind ordentlich. Die ehrliche Wahrheit: Reine Operations-Rollen können anspruchsvoll und schnell getaktet sein; die beste Rendite bringt die Kombination aus **Spezialisierung** (Analytics/digitales SCM oder Procurement) + **Deutsch** + einer Rolle, die die Blue-Card-Schwelle erreicht. Starte früh als Werkstudent, baue SAP-/Analytics-Skills aus, sei zweisprachig — dann öffnen sich die Türen schnell.

*Die Gehalts- und Blue-Card-Zahlen in diesem Beitrag sind für 2026 ungefähr und können sich ändern; prüfe sie vor der Bewerbung an offiziellen Quellen (Make it in Germany, Bundesagentur für Arbeit) und an aktuellen Stellenanzeigen.*
MD;
        $enBody = <<<'MD'
Germany is Europe's **number-one logistics hub**: a central location, major ports, a dense motorway network and global players like **Deutsche Post DHL, Kühne+Nagel, DB Schenker and DACHSER** make it perhaps the continent's liveliest job market for supply chain professionals. But how do you actually work here as an international graduate with a degree in logistics or **Supply Chain Management (SCM)** — which sectors want you, and what can you realistically earn? Let's be honest about it.

## Which sectors hire logistics & SCM graduates?

The biggest advantage of this field is employability across many industries. Your degree does not box you into a single role:

- **Logistics service providers (3PL/4PL):** DHL, Kühne+Nagel, DB Schenker, DACHSER, Hermes — transport, warehousing, freight forwarding.
- **SCM in manufacturing:** Automotive (VW, BMW, Bosch, Continental), retail and industrial makers — planning, production logistics, material flow.
- **Procurement / purchasing:** Supplier management, contracts, sourcing — in nearly every large company.
- **E-commerce & fulfilment:** Amazon, Zalando and the growing online retail sector for warehouse and distribution operations.
- **Consulting:** Supply chain optimisation (both large and niche consultancies).
- **Digital SCM / logistics technology:** Software, data and automation — a fast-growing segment.

## Digital supply chains are growing

This is the most exciting part of the industry. Demand forecasting, route optimisation, warehouse automation, sustainability (green logistics) and supply chain visibility all run on **data and software**. Graduates with SAP, analytics and automation skills stand out. That is why the bridge between SCM and [breaking into data science and AI in Germany](/en/blog/how-to-break-into-data-science-ai-in-germany-en) keeps gaining value; candidates close to the technical side have higher salary potential.

## Typical roles

| Role | What it does | Note |
|---|---|---|
| Supply Chain Analyst | Data analysis, demand planning, KPIs | Gateway to the analytical/digital side |
| Operations / Logistics Manager | Warehouse, transport, field operations | Demanding, fast-paced |
| Procurement / Purchasing Specialist | Supplier & sourcing management | German often an advantage |
| Demand / Supply Planner | Inventory & forecasting | SAP/Excel/analytics-heavy |
| SCM Consultant | Process optimisation | Consulting, travel-heavy |

## Salary: realistic numbers (approximate)

Figures vary by company, city, sector and experience — treat these as a **rough range** and always verify against current job listings.

| Level | Gross per year (approximate) |
|---|---|
| Entry (fresh graduate) | ~€45,000 – €55,000 |
| 3-5 years' experience | ~€55,000 – €70,000 |
| Senior / management | ~€70,000 and above |

Strong sectors such as automotive and pharma, plus consulting, push the upper band; pure warehouse/operations roles can sit below the entry band. In engineering-heavy SCM roles (Wirtschaftsingenieur), salaries often approach the engineer levels covered in [working as an engineer in Germany and Blue Card salary](/en/blog/working-as-an-engineer-in-germany-blue-card-salary-en).

City matters too: in hubs like Munich, Stuttgart and Frankfurt salaries are higher, but so is the cost of living; in the Ruhr region or eastern Germany the figure is lower, yet purchasing power usually balances it out. Extras such as an annual bonus (13th salary), a company car and performance premiums also shape the total package significantly — look at **net pay plus benefits** as a whole, not just the gross figure.

## German & the Blue Card (2026)

International logistics and global SCM are English-friendly; many headquarters and consultancies run in English. But for **domestic operations, field and procurement** roles, German is a clear advantage — often a requirement. A B1-B2 level noticeably improves your chances.

On the visa side, the 2026 **Blue Card** thresholds are (approximate — verify officially):

- General threshold: **~€50,700 / year** gross.
- Shortage occupations & new graduates: **~€45,934 / year** gross.

Most SCM/logistics management roles can meet these thresholds; if your entry salary stays low, the new-graduate/shortage reduction may apply.

## Job search & strategy

- Xing and LinkedIn are the heart of logistics/SCM listings in Germany; company career pages and StepStone are strong too.
- Getting inside a DHL or a manufacturer as a **Werkstudent / intern** while still studying is the most effective route.
- SAP (especially SAP MM/SD/APO), advanced Excel, an analytics tool (SQL/Python) and an English+German combination make your CV stand out.
- Once you have a job offer, the process follows the steps in [Germany work visa with a job offer: process and fast track](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).

To complete the career picture: [studying logistics & SCM in Germany](/en/blog/studying-logistics-and-supply-chain-management-in-germany-as-a-foreigner-en), [English-taught logistics/SCM master's programmes](/en/blog/english-taught-logistics-and-supply-chain-management-masters-in-germany-en) and [what to do with a logistics/SCM degree](/en/blog/what-to-do-with-a-logistics-supply-chain-degree-in-germany-job-market-en).

## Conclusion & honest advice

Logistics & SCM is an employment-friendly, solid and growing field in Germany. The ecosystem is huge, digital SCM is rising and entry salaries are decent. The honest truth: pure operations roles can be demanding and fast-paced; the best return comes from combining **specialisation** (analytics/digital SCM or procurement) + **German** + a role that meets the Blue Card threshold. Start early as a Werkstudent, build SAP/analytics skills, be bilingual — and doors open quickly.

*The salary and Blue Card figures in this article are approximate for 2026 and may change; verify them before applying via official sources (Make it in Germany, Bundesagentur für Arbeit) and current job listings.*
MD;

        $variants = [
            'tr' => ['slug'=>'working-in-logistics-and-supply-chain-management-in-germany-careers-salary',    'title'=>'Almanya\'da Lojistik & Tedarik Zincirinde Çalışmak: Kariyer ve Maaş (2026)', 'excerpt'=>'Almanya lojistik & SCM iş piyasası: hangi sektörler işe alır, tipik roller, gerçekçi giriş maaşı (~45-55k€), Almanca, 2026 Blue Card eşikleri ve iş arama stratejisi.', 'meta_title'=>'Almanya\'da Lojistik & SCM: Kariyer ve Maaş (2026)', 'meta_description'=>'Almanya\'da lojistik & tedarik zincirinde çalışmak: sektörler, roller, gerçekçi maaş (~45-55k€), Almanca ve 2026 Blue Card. Dürüst kariyer rehberi.', 'body'=>$trBody],
            'de' => ['slug'=>'working-in-logistics-and-supply-chain-management-in-germany-careers-salary-de', 'title'=>'In Logistik & Supply Chain in Deutschland arbeiten: Karriere und Gehalt (2026)', 'excerpt'=>'Logistik- & SCM-Arbeitsmarkt in Deutschland: welche Branchen einstellen, typische Rollen, realistisches Einstiegsgehalt (~45-55k€), Deutsch und die Blue-Card-Schwellen 2026.', 'meta_title'=>'Logistik & SCM in Deutschland: Karriere & Gehalt (2026)', 'meta_description'=>'In Logistik & Supply Chain in Deutschland arbeiten: Branchen, Rollen, realistisches Gehalt (~45-55k€), Deutsch und Blue Card 2026. Ehrlicher Karriere-Guide.', 'body'=>$deBody],
            'en' => ['slug'=>'working-in-logistics-and-supply-chain-management-in-germany-careers-salary-en', 'title'=>'Working in Logistics & Supply Chain in Germany: Careers and Salary (2026)', 'excerpt'=>'Germany\'s logistics & SCM job market: which sectors hire, typical roles, realistic entry salary (~€45-55k), German, the 2026 Blue Card thresholds and a job-search strategy.', 'meta_title'=>'Logistics & SCM in Germany: Careers & Salary (2026)', 'meta_description'=>'Working in logistics & supply chain in Germany: sectors, roles, realistic salary (~€45-55k), German and the 2026 Blue Card. An honest career guide.', 'body'=>$enBody],
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
            'working-in-logistics-and-supply-chain-management-in-germany-careers-salary',
            'working-in-logistics-and-supply-chain-management-in-germany-careers-salary-de',
            'working-in-logistics-and-supply-chain-management-in-germany-careers-salary-en',
        ])->delete();
    }
};

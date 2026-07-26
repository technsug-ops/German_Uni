<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da yeşil kariyer — sürdürülebilirlik & yenilenebilir enerji (2026).
 * Doğrulandı: Energiewende sektörü büyüyor (rüzgar/güneş); kurumsal ESG/CSR talebi artıyor.
 * ESG/sürdürülebilirlik maaşı ~45-55k€ (yaklaşık, doğrula). Blue Card 2026: genel ~50.700€,
 * darboğaz/yeni-mezun ~45.934€ (yaklaşık; resmi kaynaktan doğrula).
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'e5f30000-3333-4c1f-9f20-ee0cff12cc03';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da "yeşil kariyer" artık niş bir hayal değil; Energiewende (enerji dönüşümü), kurumsal iklim hedefleri ve AB düzenlemeleri sayesinde büyüyen gerçek bir iş piyasası. Ama disiplinlerarası bir sürdürülebilirlik diploması tek başına yeterli değil — nereye uzmanlaşacağını bilmen gerekiyor. Bu yazı sektörleri, maaşları ve 2026 Blue Card gerçeğini dürüstçe anlatıyor.

## Yeşil sektörler: haritanı çıkar

Almanya'da sürdürülebilirlik kariyeri tek bir şey değil; en az beş farklı dünya:

- **Yenilenebilir enerji (Energiewende):** Rüzgar (kara/deniz üstü), güneş, şebeke, enerji depolama, hidrojen. Teknik ağırlıklı, en çok istihdam yaratan alan.
- **Kurumsal sürdürülebilirlik / ESG-CSR:** Büyük şirketlerin (otomotiv, kimya, sigorta) iklim raporlaması, karbon muhasebesi, tedarik zinciri sürdürülebilirliği.
- **Çevre danışmanlığı:** Firmalara çevresel etki değerlendirmesi, sertifikasyon (ISO 14001), enerji verimliliği danışmanlığı.
- **NGO / sivil toplum:** WWF, BUND, NABU, iklim vakıfları. Anlamlı ama rekabetçi ve düşük maaşlı.
- **Kamu:** Umweltamt (çevre dairesi), belediyeler, federal kurumlar (UBA). İstikrarlı, Almanca şart.

## Energiewende neden bu kadar büyük bir fırsat

Almanya 2045 iklim-nötrlük hedefi koydu ve bunun bel kemiği yenilenebilir enerji. Rüzgar ve güneş kapasitesi her yıl agresif artıyor; deniz üstü rüzgar (offshore) ve yeşil hidrojen ise yeni büyük dalga. Bu, mühendis, proje yöneticisi, veri analisti ve saha teknisyeni talebini sürekli yukarı çekiyor. Teknik altyapın (mühendislik, fizik, enerji sistemleri) varsa bu alan en yüksek maaş ve en net Blue Card yolunu sunar. Sırf çevre bilimi diploman varsa da proje geliştirme, ruhsatlandırma (Genehmigung) ve sürdürülebilirlik analizi tarafında yer bulabilirsin — ama teknik bir beceri katmanı eklemek şart.

## ESG / sürdürülebilirlik yöneticiliğinin yükselişi

AB'nin kurumsal sürdürülebilirlik raporlama düzenlemeleri (CSRD gibi) yüzünden şirketler artık iklim ve ESG raporu vermek zorunda. Bu, "Sustainability Manager", "ESG Analyst", "CSR Specialist" gibi yeni pozisyonlar yarattı. İş; karbon ayak izi hesaplama, sürdürülebilirlik stratejisi, paydaş raporlaması ve düzenleme uyumu. Disiplinlerarası sürdürülebilirlik mezunları için en erişilebilir kurumsal kapı burası — özellikle biraz veri/finans/raporlama becerisi eklersen. Talep büyüyor ama pozisyonlar hâlâ rekabetçi.

## Maaş: dürüst bir tablo

Aşağıdaki rakamlar 2025/2026 için yaklaşık brüt yıllık değerlerdir; şehir, sektör ve deneyime göre ciddi değişir. Kendi teklifini mutlaka doğrula.

| Alan / rol | Yıllık brüt (yaklaşık) | Not |
|---|---|---|
| ESG / Sürdürülebilirlik uzmanı (giriş) | ~45.000–55.000 € | Kurumsal; büyüyen talep |
| Yenilenebilir enerji mühendisi | ~50.000–65.000 € | Teknik; en yüksek, Blue Card dostu |
| Çevre danışmanı | ~42.000–52.000 € | Değişken; proje bazlı |
| NGO / sivil toplum | ~35.000–45.000 € | Anlamlı ama düşük |
| Kamu (Umweltamt, TVöD) | ~42.000–55.000 € | İstikrarlı; Almanca şart |

Genel örüntü: teknik/yenilenebilir üstte, NGO/başlangıç kamu altta, ESG ortada ve yükseliyor.

## Almanca gerçeği + 2026 Blue Card

Araştırma, uluslararası danışmanlık ve bazı yenilenebilir enerji şirketleri İngilizce-dostu olabilir. Ama kurumsal ESG'nin çoğu, tüm kamu ve NGO'ların büyük kısmı **Almanca (en az B2/C1)** ister. Almanca, düşük maaşlı ilk işten iyi maaşlı role geçişinin de anahtarı.

Blue Card (yüksek vasıflı çalışma izni) için 2026'da yaklaşık eşikler:

- **Genel maaş eşiği: ~50.700 €/yıl (yaklaşık; resmi kaynaktan doğrula).**
- **Darboğaz meslek / yeni mezun eşiği: ~45.934 €/yıl (yaklaşık; doğrula).** MINT-ilişkili yenilenebilir enerji ve mühendislik rolleri genelde darboğaz kategorisine girer, bu da eşiği düşürür.
- Öğrenci olarak kalıyorsan **Sperrkonto ~992 €/ay = ~11.904 €/yıl (2025/2026)** güncel bloke hesap tutarıdır.

Yani teknik bir yenilenebilir enerji rolü, hem maaş hem Blue Card açısından en net yol; saf ESG/NGO rolleri eşiği zorlayabilir.

## İş arama + strateji

- **Uzmanlaş:** "Sürdürülebilirlik" çok geniş. Karbon muhasebesi, rüzgar proje geliştirme, ESG raporlaması gibi tek bir dikeyde derinleş.
- **Beceri katmanı ekle:** Veri analizi (Excel/Python), GIS, enerji modellemesi veya finans/raporlama seni öne çıkarır.
- **Staj/Werkstudent:** Almanya'da işe girişin klasik kapısı; öğrenciyken başla.
- **Ağlar:** LinkedIn, Greenjobs.de, StepStone, şirketlerin kariyer sayfaları, sektör fuarları (WindEnergy Hamburg gibi).
- **Almancanı yükselt:** B2+ çoğu kurumsal/kamu kapısını açar.

Mühendislik tarafına yakınsan [Almanya'da mühendis olarak çalışmak ve Blue Card maaşı](/tr/blog/working-as-an-engineer-in-germany-blue-card-salary) yazısı yenilenebilir enerji için doğrudan yararlı. İş teklifiyle çalışma vizesi sürecini [iş teklifiyle Almanya çalışma vizesi: süreç ve zaman çizelgesi](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) yazısında adım adım bulabilirsin.

## Sonuç & dürüst tavsiye

Almanya'daki yeşil sektör gerçekten büyüyor ve uluslararası yetenek arıyor — ama "sürdürülebilirlik seviyorum" bir kariyer planı değil. En güvenli yol: teknik bir dikeye (tercihen yenilenebilir enerji) uzmanlaş, veri/raporlama becerisi ekle, Almancanı B2+'a çıkar ve Werkstudent/staj ile erken ayağını sok. Böyle yaparsan hem maaş hem Blue Card yolun net olur; sadece geniş diplomayla NGO/politika hayali kurarsan piyasa rekabetçi ve düşük maaşlı olabilir.

Devamı için: [yabancı olarak Almanya'da çevre bilimleri & sürdürülebilirlik okumak](/tr/blog/studying-environmental-sciences-and-sustainability-in-germany-as-a-foreigner), [Almanya'da İngilizce çevre & sürdürülebilirlik master programları](/tr/blog/english-taught-environmental-and-sustainability-masters-in-germany) ve [çevre/sürdürülebilirlik diplomasıyla iş piyasası](/tr/blog/what-to-do-with-an-environmental-sustainability-degree-in-germany-job-market).

*Bu yazı 2026 başı itibarıyla geneldir ve hukuki/göç danışmanlığı değildir. Maaş aralıkları, Blue Card eşikleri ve Sperrkonto tutarları yaklaşıktır ve değişir; başvurudan önce resmi kaynaklardan (Bundesagentur für Arbeit, ilgili yabancılar dairesi) doğrula.*
MD;
        $deBody = <<<'MD'
Eine "grüne Karriere" in Deutschland ist kein Nischentraum mehr. Dank der Energiewende, unternehmerischer Klimaziele und EU-Regulierung ist ein echter, wachsender Arbeitsmarkt entstanden. Aber ein breiter, interdisziplinärer Nachhaltigkeits-Abschluss allein reicht nicht — du musst wissen, wohin du dich spezialisierst. Dieser Artikel erklärt ehrlich die Sektoren, Gehälter und die Blue-Card-Realität 2026.

## Grüne Sektoren: erstell deine Landkarte

Nachhaltigkeit in Deutschland ist nicht eine Sache, sondern mindestens fünf Welten:

- **Erneuerbare Energien (Energiewende):** Wind (onshore/offshore), Solar, Netze, Speicher, Wasserstoff. Technisch geprägt, mit dem größten Beschäftigungswachstum.
- **Unternehmerische Nachhaltigkeit / ESG-CSR:** Klimaberichterstattung, CO2-Bilanzierung und Lieferketten-Nachhaltigkeit in großen Firmen (Automobil, Chemie, Versicherung).
- **Umweltberatung:** Umweltverträglichkeitsprüfungen, Zertifizierung (ISO 14001), Energieeffizienz-Beratung.
- **NGO / Zivilgesellschaft:** WWF, BUND, NABU, Klimastiftungen. Sinnvoll, aber wettbewerbsintensiv und schlecht bezahlt.
- **Öffentlicher Dienst:** Umweltamt, Kommunen, Bundesbehörden (UBA). Stabil, Deutsch ist Pflicht.

## Warum die Energiewende so eine große Chance ist

Deutschland hat sich Klimaneutralität bis 2045 zum Ziel gesetzt, und das Rückgrat dafür sind erneuerbare Energien. Wind- und Solarkapazitäten wachsen jedes Jahr stark; Offshore-Wind und grüner Wasserstoff sind die nächste große Welle. Das treibt die Nachfrage nach Ingenieuren, Projektmanagern, Datenanalysten und Techniker:innen dauerhaft nach oben. Wenn du eine technische Basis hast (Ingenieurwesen, Physik, Energiesysteme), bietet dieser Bereich das höchste Gehalt und den klarsten Blue-Card-Weg. Auch mit einem reinen Umweltwissenschafts-Abschluss findest du Platz in Projektentwicklung, Genehmigung und Nachhaltigkeitsanalyse — aber eine technische Zusatzkompetenz ist Pflicht.

## Der Aufstieg des Nachhaltigkeitsmanagements (ESG)

Wegen der EU-Regulierung zur unternehmerischen Nachhaltigkeitsberichterstattung (etwa CSRD) müssen Firmen jetzt Klima- und ESG-Berichte liefern. Das hat neue Positionen geschaffen: "Sustainability Manager", "ESG Analyst", "CSR Specialist". Die Arbeit umfasst CO2-Fußabdruck-Berechnung, Nachhaltigkeitsstrategie, Stakeholder-Reporting und Regulierungs-Compliance. Für interdisziplinäre Nachhaltigkeits-Absolventen ist das die zugänglichste Unternehmenstür — besonders wenn du Daten-/Finanz-/Reporting-Kompetenz mitbringst. Die Nachfrage wächst, aber die Stellen sind weiterhin umkämpft.

## Gehalt: eine ehrliche Tabelle

Die folgenden Zahlen sind ungefähre Brutto-Jahreswerte für 2025/2026; sie variieren stark nach Stadt, Sektor und Erfahrung. Prüfe dein eigenes Angebot immer.

| Bereich / Rolle | Brutto/Jahr (ungefähr) | Hinweis |
|---|---|---|
| ESG / Nachhaltigkeitsspezialist (Einstieg) | ~45.000–55.000 € | Unternehmen; wachsende Nachfrage |
| Ingenieur erneuerbare Energien | ~50.000–65.000 € | Technisch; am höchsten, Blue-Card-freundlich |
| Umweltberater | ~42.000–52.000 € | Variabel; projektbasiert |
| NGO / Zivilgesellschaft | ~35.000–45.000 € | Sinnvoll, aber niedrig |
| Öffentlicher Dienst (Umweltamt, TVöD) | ~42.000–55.000 € | Stabil; Deutsch Pflicht |

Das Muster: Technik/Erneuerbare oben, NGO/Einstieg-Öffentlicher-Dienst unten, ESG in der Mitte und steigend.

## Deutsch-Realität + Blue Card 2026

Forschung, internationale Beratung und manche Erneuerbare-Firmen können englischsprachig sein. Aber das meiste Corporate-ESG, der gesamte öffentliche Dienst und ein Großteil der NGOs verlangen **Deutsch (mindestens B2/C1)**. Deutsch ist auch der Schlüssel, um vom schlecht bezahlten ersten Job in eine gut bezahlte Rolle zu wechseln.

Für die Blue Card (Aufenthaltstitel für Hochqualifizierte) gelten 2026 ungefähr folgende Schwellen:

- **Allgemeine Gehaltsschwelle: ~50.700 €/Jahr (ungefähr; aus offizieller Quelle prüfen).**
- **Engpassberuf / Berufseinsteiger-Schwelle: ~45.934 €/Jahr (ungefähr; prüfen).** MINT-nahe Erneuerbare-Energie- und Ingenieurrollen fallen meist in die Engpasskategorie, was die Schwelle senkt.
- Wenn du als Student bleibst: **Sperrkonto ~992 €/Monat = ~11.904 €/Jahr (2025/2026)** ist der aktuelle Betrag.

Eine technische Erneuerbare-Energie-Rolle ist also sowohl beim Gehalt als auch bei der Blue Card der klarste Weg; reine ESG-/NGO-Rollen können die Schwelle strapazieren.

## Jobsuche + Strategie

- **Spezialisiere dich:** "Nachhaltigkeit" ist sehr breit. Geh in einer Vertikale in die Tiefe — CO2-Bilanzierung, Wind-Projektentwicklung oder ESG-Reporting.
- **Zusatzkompetenz:** Datenanalyse (Excel/Python), GIS, Energiemodellierung oder Finanz-/Reporting-Wissen heben dich hervor.
- **Praktikum/Werkstudent:** Die klassische Eintrittstür in Deutschland — fang schon im Studium an.
- **Netzwerke:** LinkedIn, Greenjobs.de, StepStone, Karriereseiten der Firmen, Branchenmessen (etwa WindEnergy Hamburg).
- **Verbessere dein Deutsch:** B2+ öffnet die meisten Unternehmens-/Behördentüren.

Wenn du nahe am Ingenieurwesen bist, ist der Artikel [Als Ingenieur in Deutschland arbeiten und Blue-Card-Gehalt](/de/blog/working-as-an-engineer-in-germany-blue-card-salary-de) direkt nützlich für erneuerbare Energien. Den Arbeitsvisum-Prozess mit Jobangebot findest du Schritt für Schritt unter [Deutsches Arbeitsvisum mit Jobangebot: Prozess und Zeitplan](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).

## Fazit & ehrlicher Rat

Der grüne Sektor in Deutschland wächst wirklich und sucht internationale Talente — aber "ich mag Nachhaltigkeit" ist kein Karriereplan. Der sicherste Weg: Spezialisiere dich auf eine technische Vertikale (idealerweise erneuerbare Energien), füge Daten-/Reporting-Kompetenz hinzu, bring dein Deutsch auf B2+ und steig früh über Werkstudent/Praktikum ein. So wird sowohl dein Gehalt als auch dein Blue-Card-Weg klar; mit nur einem breiten Abschluss und einem NGO-/Politik-Traum kann der Markt umkämpft und schlecht bezahlt sein.

Weiterlesen: [Umweltwissenschaften & Nachhaltigkeit in Deutschland als Ausländer studieren](/de/blog/studying-environmental-sciences-and-sustainability-in-germany-as-a-foreigner-de), [englischsprachige Umwelt- & Nachhaltigkeits-Master in Deutschland](/de/blog/english-taught-environmental-and-sustainability-masters-in-germany-de) und [was man mit einem Umwelt-/Nachhaltigkeitsabschluss macht: Arbeitsmarkt](/de/blog/what-to-do-with-an-environmental-sustainability-degree-in-germany-job-market-de).

*Dieser Artikel ist Anfang 2026 allgemein gehalten und keine Rechts- oder Migrationsberatung. Gehaltsspannen, Blue-Card-Schwellen und Sperrkonto-Beträge sind ungefähr und ändern sich; prüfe vor der Bewerbung offizielle Quellen (Bundesagentur für Arbeit, zuständige Ausländerbehörde).*
MD;
        $enBody = <<<'MD'
A "green career" in Germany is no longer a niche dream. Thanks to the Energiewende (energy transition), corporate climate targets and EU regulation, a real and growing job market has emerged. But a broad, interdisciplinary sustainability degree alone is not enough — you need to know where to specialise. This article honestly explains the sectors, salaries and the 2026 Blue Card reality.

## Green sectors: draw your map

Sustainability in Germany is not one thing; it is at least five different worlds:

- **Renewable energy (Energiewende):** Wind (onshore/offshore), solar, grids, storage, hydrogen. Technically driven, with the largest employment growth.
- **Corporate sustainability / ESG-CSR:** Climate reporting, carbon accounting and supply-chain sustainability inside large firms (automotive, chemicals, insurance).
- **Environmental consulting:** Environmental impact assessments, certification (ISO 14001), energy-efficiency advisory.
- **NGO / civil society:** WWF, BUND, NABU, climate foundations. Meaningful, but competitive and low-paid.
- **Public sector:** Umweltamt (environmental office), municipalities, federal agencies (UBA). Stable, German is mandatory.

## Why the Energiewende is such a big opportunity

Germany has set a 2045 climate-neutrality target, and its backbone is renewable energy. Wind and solar capacity grow aggressively every year; offshore wind and green hydrogen are the next big wave. This keeps pushing up demand for engineers, project managers, data analysts and field technicians. If you have a technical base (engineering, physics, energy systems), this field offers the highest salary and the clearest Blue Card path. Even with a pure environmental-science degree you can find a place in project development, permitting (Genehmigung) and sustainability analysis — but adding a technical skill layer is essential.

## The rise of sustainability (ESG) management

Because of EU corporate sustainability reporting rules (such as CSRD), firms must now produce climate and ESG reports. This has created new roles: "Sustainability Manager", "ESG Analyst", "CSR Specialist". The work covers carbon footprinting, sustainability strategy, stakeholder reporting and regulatory compliance. For interdisciplinary sustainability graduates, this is the most accessible corporate door — especially if you add some data/finance/reporting skill. Demand is growing, but positions remain competitive.

## Salary: an honest table

The figures below are approximate gross annual values for 2025/2026; they vary heavily by city, sector and experience. Always verify your own offer.

| Field / role | Gross per year (approx.) | Note |
|---|---|---|
| ESG / sustainability specialist (entry) | ~€45,000–55,000 | Corporate; growing demand |
| Renewable energy engineer | ~€50,000–65,000 | Technical; highest, Blue-Card-friendly |
| Environmental consultant | ~€42,000–52,000 | Variable; project-based |
| NGO / civil society | ~€35,000–45,000 | Meaningful but low |
| Public sector (Umweltamt, TVöD) | ~€42,000–55,000 | Stable; German mandatory |

The pattern: technical/renewables on top, NGO/entry public sector at the bottom, ESG in the middle and rising.

## The German-language reality + 2026 Blue Card

Research, international consulting and some renewable-energy firms can be English-friendly. But most corporate ESG, the entire public sector and much of the NGO world require **German (at least B2/C1)**. German is also the key to moving from a low-paid first job into a well-paid role.

For the Blue Card (residence permit for the highly qualified), approximate 2026 thresholds are:

- **General salary threshold: ~€50,700/year (approximate; verify from an official source).**
- **Shortage occupation / new-graduate threshold: ~€45,934/year (approximate; verify).** STEM-related renewable-energy and engineering roles usually fall into the shortage category, which lowers the threshold.
- If you stay as a student: **Sperrkonto ~€992/month = ~€11,904/year (2025/2026)** is the current blocked-account amount.

So a technical renewable-energy role is the clearest path on both salary and Blue Card; pure ESG/NGO roles may strain the threshold.

## Job search + strategy

- **Specialise:** "Sustainability" is very broad. Go deep in one vertical — carbon accounting, wind project development or ESG reporting.
- **Add a skill layer:** Data analysis (Excel/Python), GIS, energy modelling or finance/reporting knowledge makes you stand out.
- **Internship/Werkstudent:** The classic entry door in Germany — start while you are still a student.
- **Networks:** LinkedIn, Greenjobs.de, StepStone, company career pages, industry fairs (such as WindEnergy Hamburg).
- **Raise your German:** B2+ opens most corporate and public-sector doors.

If you are close to engineering, the article on [working as an engineer in Germany and the Blue Card salary](/en/blog/working-as-an-engineer-in-germany-blue-card-salary-en) is directly useful for renewables. You can find the work-visa-with-job-offer process step by step in [Germany work visa with a job offer: process and timeline](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).

## Conclusion & honest advice

Germany's green sector really is growing and looking for international talent — but "I like sustainability" is not a career plan. The safest path: specialise in a technical vertical (ideally renewable energy), add data/reporting skills, raise your German to B2+, and get in early via a Werkstudent role or internship. Do that and both your salary and your Blue Card path become clear; rely on a broad degree plus an NGO/policy dream alone, and the market can be competitive and low-paid.

Read on: [studying environmental sciences & sustainability in Germany as a foreigner](/en/blog/studying-environmental-sciences-and-sustainability-in-germany-as-a-foreigner-en), [English-taught environmental & sustainability master's in Germany](/en/blog/english-taught-environmental-and-sustainability-masters-in-germany-en), and [what to do with an environmental/sustainability degree: the job market](/en/blog/what-to-do-with-an-environmental-sustainability-degree-in-germany-job-market-en).

*This article is general as of early 2026 and is not legal or immigration advice. Salary ranges, Blue Card thresholds and Sperrkonto amounts are approximate and change; verify with official sources (Bundesagentur für Arbeit, the relevant immigration office) before applying.*
MD;

        $variants = [
            'tr' => ['slug'=>'green-careers-working-in-sustainability-and-renewable-energy-in-germany',    'title'=>'Almanya\'da Yeşil Kariyer: Sürdürülebilirlik ve Yenilenebilir Enerjide Çalışmak (2026)', 'excerpt'=>'Almanya\'da yeşil kariyer gerçek ve büyüyor: Energiewende (yenilenebilir enerji), kurumsal ESG/CSR, danışmanlık, NGO ve kamu. Sektörler, maaşlar (ESG ~45-55k€), Almanca gerçeği ve güncel 2026 Blue Card eşikleriyle dürüst bir rehber.', 'meta_title'=>'Almanya\'da Yeşil Kariyer: Sürdürülebilirlik & Enerji (2026)', 'meta_description'=>'Almanya\'da sürdürülebilirlik ve yenilenebilir enerji kariyeri: sektörler, maaş tablosu (ESG ~45-55k€), Almanca ve 2026 Blue Card gerçeği. Dürüst rehber.', 'body'=>$trBody],
            'de' => ['slug'=>'green-careers-working-in-sustainability-and-renewable-energy-in-germany-de', 'title'=>'Grüne Karriere in Deutschland: Arbeiten in Nachhaltigkeit und erneuerbaren Energien (2026)', 'excerpt'=>'Grüne Karriere in Deutschland ist real und wächst: Energiewende (erneuerbare Energien), Corporate ESG/CSR, Beratung, NGO und öffentlicher Dienst. Ein ehrlicher Leitfaden zu Sektoren, Gehältern (ESG ~45-55k€), Deutsch und den aktuellen Blue-Card-Schwellen 2026.', 'meta_title'=>'Grüne Karriere in Deutschland: Nachhaltigkeit & Energie (2026)', 'meta_description'=>'Karriere in Nachhaltigkeit und erneuerbaren Energien in Deutschland: Sektoren, Gehaltstabelle (ESG ~45-55k€), Deutsch und Blue-Card-Realität 2026. Ehrlicher Leitfaden.', 'body'=>$deBody],
            'en' => ['slug'=>'green-careers-working-in-sustainability-and-renewable-energy-in-germany-en', 'title'=>'Green Careers in Germany: Working in Sustainability and Renewable Energy (2026)', 'excerpt'=>'A green career in Germany is real and growing: the Energiewende (renewable energy), corporate ESG/CSR, consulting, NGOs and the public sector. An honest guide to the sectors, salaries (ESG ~€45-55k), the German-language reality and the current 2026 Blue Card thresholds.', 'meta_title'=>'Green Careers in Germany: Sustainability & Energy (2026)', 'meta_description'=>'Careers in sustainability and renewable energy in Germany: sectors, salary table (ESG ~€45-55k), German-language and 2026 Blue Card reality. An honest guide.', 'body'=>$enBody],
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
            'green-careers-working-in-sustainability-and-renewable-energy-in-germany',
            'green-careers-working-in-sustainability-and-renewable-energy-in-germany-de',
            'green-careers-working-in-sustainability-and-renewable-energy-in-germany-en',
        ])->delete();
    }
};

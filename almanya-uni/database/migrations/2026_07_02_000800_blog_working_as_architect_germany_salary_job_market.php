<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da mimar olarak çalışmak — maaş & iş piyasası gerçeği (2026).
 * Doğrulandı: Architektur ≠ Bauingenieurwesen; mimarlıkta giriş maaşı (~35-45k) mühendislikten
 * düşük (hedge'li); mezuniyet sonrası 18 ay iş-arama oturumu (Studienabsolvent) → Zweckwechsel;
 * büro/pratik Almanca ister, Architektenkammer lisansı kariyeri açar. Yazar: Halil Yaprakli.
 * Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'd4a40000-4444-4a2c-9f50-dd01ee04aa04';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da mimarlık okumak bir tutku işidir. Ama tutku faturaları ödemez. Bu yazı, romantizmi bir kenara bırakıp **Almanya'da mimar olarak çalışmanın parasal ve piyasa gerçeğini** dürüstçe anlatır: hangi sektörler var, giriş maaşı gerçekte ne, neden mühendislikten düşük, ve uluslararası bir öğrenci olarak bu piyasada nasıl ayakta kalırsın.

Önemli bir ayrımla başlayalım: **Architektur (mimarlık) ile Bauingenieurwesen (inşaat mühendisliği) aynı şey değildir.** Mimarlık tasarım, mekan ve konsept odaklıdır; inşaat mühendisliği strüktür, statik ve yapımdır. İş piyasası ve maaş açısından bu iki dünya ciddi ayrışır — ve genelde inşaat mühendisliği tarafı daha çok kazanır. Bunu baştan bilmen gerekir.

## Sektörler: Mimar nerede çalışır?

Almanya'da mimarlık diplomasıyla çalışabileceğin başlıca alanlar:

- **Architekturbüro (mimarlık bürosu):** Klasik yol. Küçük butik bürolardan büyük uluslararası ofislere kadar. Mezunların çoğu buradan başlar. Maaşlar burada en değişken.
- **Kentsel planlama / Stadtplanung / Städtebau:** Belediyeler, planlama ofisleri, kamu kurumları. Daha kurumsal, daha istikrarlı, çoğu zaman kamu ücret cetveline (TVöD) bağlı.
- **Bau sektörü / şantiye tarafı:** Proje yönetimi, Bauleitung (yapı denetimi/şantiye şefliği), inşaat şirketleri. Burada mimarlar mühendislerle rekabet eder ve genelde daha teknik-yönetsel roller alır.
- **Kamu (öffentlicher Dienst):** Bauamt (yapı dairesi), kamu kurumları. İstikrarlı ama ücret tavanı belli; TVöD/TV-L cetveline göre ilerler.
- **Yan alanlar:** BIM koordinasyonu, iç mimarlık, peyzaj, görselleştirme, ürün/mobilya tasarımı, akademi.

## Maaş gerçeği: Neden düşük ve ne kadar?

Burada dürüst olacağım çünkü çoğu kaynak süslüyor. **Mimarlıkta giriş maaşı, mühendisliğe kıyasla belirgin şekilde DÜŞÜKTÜR.** Sebebi arz-talep: mimarlık popüler bir bölüm, mezun bol, açık pozisyon görece az; buna karşı inşaat/makine/elektrik mühendisliğinde açık çok, aday az. Piyasa fiyatı bu farkı yansıtır.

| Rol / aşama | Yaklaşık brüt yıllık (€) | Not |
|---|---|---|
| Mimarlık bürosunda giriş (mezun) | ~35.000 – 45.000 | Küçük bürolarda alt banda yakın |
| 3-5 yıl deneyimli mimar | ~45.000 – 55.000 | Lisans (Kammer) + sorumluluk artışıyla |
| Kammer kayıtlı, proje yöneten | ~55.000 – 70.000+ | Bauleitung/proje yönetimiyle üst banda |
| Kamu (TVöD, Bauamt) | ~48.000 – 62.000 | Cetvele bağlı, istikrarlı |
| Kıyas: giriş mühendisi | ~45.000 – 55.000 | Aynı deneyimde genelde daha yüksek |

**Kalın gerçek: mimarlıkta ~35-45k ile başlarsın; aynı aşamadaki bir mühendis genelde ~45-55k alır.** Yani tercihini yaparken bunu bilerek yap.

*2025/2026 itibarıyla, yaklaşık; bölgeye (Güney = Bayern/Baden-Württemberg yüksek ama kira da yüksek), büro büyüklüğüne ve ekonomiye göre ciddi değişir, yıllık güncellenir. Bir teklif aldığında o şehir için **net** rakamı (vergi, sağlık sigortası, kira) ayrıca hesapla ve **doğrula.***

## Roller: Entwurf, Bauleitung, BIM

Mimarlık kariyeri tek tip değil. Nereye yöneldiğin maaşını ve iş güvenliğini etkiler:

- **Entwurf (tasarım):** En "mimar" görünen ama en rekabetçi ve genelde en düşük ücretli alan. Konsept, tasarım, yarışma. Tutku burada, para daha az.
- **Ausführungsplanung / uygulama projesi:** Detay çizim, teknik dokümantasyon. İstikrarlı talep var.
- **Bauleitung (şantiye/yapı denetimi):** Sahada koordinasyon, taşeron yönetimi. Daha iyi ücretli, sorumluluğu yüksek; Almanca burada **şart**.
- **BIM koordinasyonu:** Yükselen alan. Revit/ArchiCAD/BIM becerisi olan mimar piyasada avantajlı — bu beceriye yatırım yap.

## Mezuniyet sonrası: 18 aylık iş-arama oturumu

Uluslararası bir öğrenciysen ve **Almanya'da mezun olduysan** en avantajlı yoldasın: mezuniyet sonrası **18 aylık iş-arama oturumu** (Studienabsolvent) hakkın var. Bu süre içinde nitelikli bir iş bulunca, öğrenci izninden çalışma iznine geçersin (**Zweckwechsel**). Mimarlık işi genelde diploma-nitelikli sayıldığından bu geçiş yapılabilir.

Yollar özetle:

1. **Almanya'da okudun** → 18 ay iş-arama oturumu → iş bulunca Zweckwechsel. En kolay yol.
2. **Yurt dışından teklifle** → çalışma vizesi / Blue Card (diploma tanınması + eşik). Süreç: [iş teklifiyle çalışma vizesi](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track).
3. **Diplomalısın, teklifin yok** → iş-arama vizesi ile gelip yerinde ara. Master vs job-seeker stratejisi: [master vs iş-arama vizesi](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career).

**Dikkat:** Blue Card için maaş eşiği var. Mimarlık maaşları düşük banda yakın olduğundan, girişte Blue Card eşiğini karşılamayabilirsin; o durumda normal çalışma izniyle başlarsın. Bunu baştan hesaba kat.

## Almanca + lisans gerçeği: kariyeri açan iki anahtar

İki şey senin tavanını belirler:

**Almanca.** Stüdyo kültürü İngilizce olabilir ama **büro günlük işi, müşteri, yetkililer, şantiye ve sözleşmeler Almancadır.** Almancasız bir mimar, işe alım havuzunun çok küçük bir kısmına erişir ve genelde arka planda (görselleştirme, salt çizim) kalır. **Kalın gerçek: pratik mimarlık için en az B2-C1 Almanca fiilen zorunludur.**

**Lisans (Architektenkammer).** "Architekt" korumalı bir unvandır. İnşaat ruhsatı sunma (Bauvorlageberechtigung) yetkisi ve gerçek proje sorumluluğu için bir eyaletin **Architektenkammer**'ine kayıt gerekir. Tipik gerek: akredite derece (genelde 5 yıl = Bachelor + Master, min ~300 ECTS) **+ ~2 yıl pratik deneyim.** Kammer kaydı olmadan da çalışırsın ama tavana çarparsın — kendi imzanla proje sunamaz, "Architekt" diyemezsin. **Lisans, maaşını ve kariyerini açan anahtardır.** Detay: [Almanya'da lisanslı mimar olmak](/tr/blog/becoming-a-licensed-architect-in-germany-architektenkammer).

## Uluslararası öğrenci stratejisi

Bu piyasada ayakta kalmak için somut plan:

- **Praktikum (staj) yap:** Okurken büro stajı, mezuniyette işe dönüşen en güçlü kanaldır. Almanya'da çoğu işe alım stajdan/tavsiyeden gelir.
- **Portfolyo (Mappe):** Sürekli güncel tut; Alman büro estetiğine (net, teknik, konsept-odaklı) uygun sun.
- **Almancaya yatırım yap:** B2-C1 seviyesi görünmez bir kapıyı açar. Bunu erken başlat.
- **BIM / yazılım becerisi:** Revit/ArchiCAD/Rhino+Grasshopper piyasa değerini yükseltir.
- **Network:** BDA (Bund Deutscher Architekten) etkinlikleri, üniversite ağı, LinkedIn. İşlerin çoğu ilan olmadan el değiştirir.
- **Lisans planı:** Master + 2 yıl pratik → Kammer kaydını hedefle. Bu, "diploma sahibi" ile "gerçek mimar" arasındaki farktır.

Aynı kümedeki diğer yazılar: [yabancı olarak mimarlık okumak](/tr/blog/studying-architecture-in-germany-as-a-foreigner) · [Almancasız İngilizce mimarlık master](/tr/blog/english-taught-architecture-masters-in-germany-without-german) · [lisanslı mimar olmak (Architektenkammer)](/tr/blog/becoming-a-licensed-architect-in-germany-architektenkammer). Kıyas için mühendislik tarafı: [Almanya'da mühendis olarak çalışmak](/tr/blog/working-as-an-engineer-in-germany-blue-card-salary).

## Sonuç & dürüst tavsiye

Almanya'da mimarlık kariyeri gerçek ve mümkün, ama gözünü açık tut. **Giriş maaşı mühendislikten düşük** (~35-45k) — bunu bilerek sev bu mesleği. Piyasada gerçekten yükselmek istiyorsan iki şey pazarlık edilemez: **Almanca (B2-C1)** ve **Architektenkammer lisansı (master + ~2 yıl pratik).** Bu ikisi olmadan tavana çarparsın; bu ikisiyle 55-70k+ bandı ve gerçek proje sorumluluğu açılır. Uluslararası öğrenci olarak stratejin net olsun: staj yap, portfolyonu güncel tut, Almancaya erken yatır, ve mezuniyet sonrası 18 aylık pencereyi verimli kullan. Tutkuyla gel — ama planla kal.

---

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır; maaşlar, vize kuralları, Kammer gerekleri ve eyalet uygulamaları zamanla ve duruma göre değişir. Karar vermeden önce ilgili Architektenkammer'in, işverenin ve resmi göçmenlik makamının güncel bilgisini doğrula.*
MD;
        $deBody = <<<'MD'
In Deutschland Architektur zu studieren ist eine Leidenschaft. Aber Leidenschaft zahlt keine Rechnungen. Dieser Beitrag lässt die Romantik beiseite und erklärt ehrlich die **finanzielle und marktbezogene Realität, als Architekt in Deutschland zu arbeiten**: welche Branchen es gibt, wie hoch das Einstiegsgehalt wirklich ist, warum es niedriger als im Ingenieurwesen liegt, und wie du als internationale:r Studierende:r in diesem Markt bestehst.

Beginnen wir mit einer wichtigen Unterscheidung: **Architektur und Bauingenieurwesen sind nicht dasselbe.** Architektur dreht sich um Entwurf, Raum und Konzept; Bauingenieurwesen um Statik, Struktur und Ausführung. Beim Arbeitsmarkt und Gehalt trennen sich diese Welten deutlich — und meist verdient die Ingenieurseite mehr. Das solltest du von Anfang an wissen.

## Branchen: Wo arbeitet ein Architekt?

Die wichtigsten Felder mit einem Architekturabschluss in Deutschland:

- **Architekturbüro:** Der klassische Weg. Von kleinen Boutique-Büros bis zu großen internationalen Offices. Die meisten Absolventen starten hier. Die Gehälter sind hier am variabelsten.
- **Stadtplanung / Städtebau:** Kommunen, Planungsbüros, öffentliche Stellen. Institutioneller, stabiler, oft an den öffentlichen Tarif (TVöD) gebunden.
- **Bausektor / Baustellenseite:** Projektmanagement, Bauleitung, Bauunternehmen. Hier konkurrieren Architekten mit Ingenieuren und übernehmen meist eher technisch-organisatorische Rollen.
- **Öffentlicher Dienst:** Bauamt, öffentliche Einrichtungen. Stabil, aber mit klarer Gehaltsobergrenze; Entwicklung nach TVöD/TV-L.
- **Nebenfelder:** BIM-Koordination, Innenarchitektur, Landschaftsarchitektur, Visualisierung, Produkt-/Möbeldesign, Wissenschaft.

## Die Gehaltsrealität: Warum niedrig und wie viel?

Hier bin ich ehrlich, denn die meisten Quellen beschönigen. **Das Einstiegsgehalt in der Architektur ist im Vergleich zum Ingenieurwesen deutlich NIEDRIGER.** Der Grund ist Angebot und Nachfrage: Architektur ist ein beliebtes Fach, es gibt viele Absolventen und vergleichsweise wenige offene Stellen; im Gegensatz dazu gibt es im Bau-/Maschinen-/Elektroingenieurwesen viele Stellen und wenige Bewerber. Der Marktpreis spiegelt diesen Unterschied wider.

| Rolle / Stufe | Ungefähres Bruttojahresgehalt (€) | Anmerkung |
|---|---|---|
| Einstieg im Architekturbüro (Absolvent) | ~35.000 – 45.000 | In kleinen Büros nahe dem unteren Band |
| Architekt mit 3-5 Jahren Erfahrung | ~45.000 – 55.000 | Mit Kammer-Eintrag + mehr Verantwortung |
| Kammer-eingetragen, projektleitend | ~55.000 – 70.000+ | Mit Bauleitung/Projektleitung oberes Band |
| Öffentlicher Dienst (TVöD, Bauamt) | ~48.000 – 62.000 | Tarifgebunden, stabil |
| Vergleich: Einstieg Ingenieur | ~45.000 – 55.000 | Bei gleicher Erfahrung meist höher |

**Fette Wahrheit: In der Architektur startest du mit ~35-45k; ein Ingenieur auf gleicher Stufe bekommt meist ~45-55k.** Triff deine Wahl also mit diesem Wissen.

*Stand 2025/2026, ungefähr; variiert stark nach Region (Süden = Bayern/Baden-Württemberg hoch, aber auch die Mieten), Bürogröße und Konjunktur, ändert sich jährlich. Wenn du ein Angebot bekommst, rechne die **Netto**-Zahl (Steuern, Krankenversicherung, Miete) für die jeweilige Stadt aus und **prüfe** sie.*

## Rollen: Entwurf, Bauleitung, BIM

Eine Architekturkarriere ist nicht einheitlich. Wohin du dich orientierst, beeinflusst Gehalt und Jobsicherheit:

- **Entwurf:** Das "architektonischste", aber wettbewerbsintensivste und meist am schlechtesten bezahlte Feld. Konzept, Design, Wettbewerbe. Die Leidenschaft ist hier, das Geld weniger.
- **Ausführungsplanung:** Detailzeichnung, technische Dokumentation. Stabile Nachfrage.
- **Bauleitung:** Koordination vor Ort, Nachunternehmer-Management. Besser bezahlt, hohe Verantwortung; Deutsch ist hier **Pflicht**.
- **BIM-Koordination:** Ein wachsendes Feld. Architekten mit Revit/ArchiCAD/BIM-Kompetenz sind im Vorteil — investiere in diese Fähigkeit.

## Nach dem Abschluss: 18 Monate zur Jobsuche

Wenn du internationale:r Studierende:r bist und **in Deutschland deinen Abschluss gemacht hast**, bist du im vorteilhaftesten Weg: Nach dem Abschluss hast du **18 Monate Aufenthalt zur Jobsuche** (Studienabsolvent). Findest du in dieser Zeit einen qualifizierten Job, wechselst du von der Studien- zur Arbeitserlaubnis (**Zweckwechsel**). Da Architekturarbeit meist als abschlussqualifiziert gilt, ist dieser Wechsel möglich.

Die Wege kurz:

1. **Du hast in Deutschland studiert** → 18 Monate Jobsuche → mit Job Zweckwechsel. Der einfachste Weg.
2. **Aus dem Ausland mit Angebot** → Arbeitsvisum / Blue Card (Anerkennung + Schwelle). Ablauf: [Arbeitsvisum mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).
3. **Du hast einen Abschluss, aber kein Angebot** → mit dem Job-Seeker-Visum herkommen und vor Ort suchen. Master vs. Job-Seeker-Strategie: [Master vs. Job-Seeker-Visum](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).

**Achtung:** Für die Blue Card gibt es eine Gehaltsschwelle. Da Architekturgehälter nahe dem unteren Band liegen, erfüllst du sie beim Einstieg vielleicht nicht; dann startest du mit der regulären Arbeitserlaubnis. Kalkuliere das von Anfang an ein.

## Deutsch + Lizenz: die zwei Schlüssel zur Karriere

Zwei Dinge bestimmen deine Decke:

**Deutsch.** Die Studiokultur mag Englisch sein, aber **der Büroalltag, Kunden, Behörden, Baustelle und Verträge laufen auf Deutsch.** Ein Architekt ohne Deutsch erreicht nur einen winzigen Teil des Einstellungspools und bleibt meist im Hintergrund (Visualisierung, reines Zeichnen). **Fette Wahrheit: Für die praktische Architektur ist mindestens B2-C1 Deutsch faktisch zwingend.**

**Lizenz (Architektenkammer).** "Architekt" ist ein geschützter Titel. Für die Bauvorlageberechtigung und echte Projektverantwortung ist der Eintrag in die **Architektenkammer** eines Bundeslandes nötig. Typische Voraussetzung: akkreditierter Abschluss (meist 5 Jahre = Bachelor + Master, min. ~300 ECTS) **+ ~2 Jahre Praxiserfahrung.** Ohne Kammer-Eintrag arbeitest du zwar, stößt aber an die Decke — du kannst keine Bauvorlagen mit eigener Unterschrift einreichen und dich nicht "Architekt" nennen. **Die Lizenz ist der Schlüssel, der Gehalt und Karriere öffnet.** Details: [lizenzierter Architekt in Deutschland werden](/de/blog/becoming-a-licensed-architect-in-germany-architektenkammer-de).

## Strategie für internationale Studierende

Konkreter Plan, um in diesem Markt zu bestehen:

- **Mach ein Praktikum:** Ein Bürupraktikum während des Studiums ist der stärkste Kanal, der beim Abschluss zu einem Job wird. Die meisten Einstellungen in Deutschland kommen über Praktikum/Empfehlung.
- **Portfolio (Mappe):** Halte es stets aktuell; präsentiere es passend zur deutschen Büroästhetik (klar, technisch, konzeptorientiert).
- **Investiere in Deutsch:** B2-C1 öffnet eine unsichtbare Tür. Fang früh an.
- **BIM-/Software-Kompetenz:** Revit/ArchiCAD/Rhino+Grasshopper steigern deinen Marktwert.
- **Netzwerk:** BDA-Veranstaltungen (Bund Deutscher Architekten), Uni-Netzwerk, LinkedIn. Die meisten Jobs wechseln ohne Ausschreibung den Besitzer.
- **Lizenz-Plan:** Master + 2 Jahre Praxis → ziele auf den Kammer-Eintrag. Das ist der Unterschied zwischen "Abschlussinhaber" und "echter Architekt".

Weitere Beiträge in derselben Reihe: [als Ausländer Architektur studieren](/de/blog/studying-architecture-in-germany-as-a-foreigner-de) · [englischsprachiger Architektur-Master ohne Deutsch](/de/blog/english-taught-architecture-masters-in-germany-without-german-de) · [lizenzierter Architekt werden (Architektenkammer)](/de/blog/becoming-a-licensed-architect-in-germany-architektenkammer-de). Zum Vergleich die Ingenieurseite: [als Ingenieur in Deutschland arbeiten](/de/blog/working-as-an-engineer-in-germany-blue-card-salary-de).

## Fazit & ehrlicher Rat

Eine Architekturkarriere in Deutschland ist real und möglich, aber halte die Augen offen. **Das Einstiegsgehalt ist niedriger als im Ingenieurwesen** (~35-45k) — liebe diesen Beruf im Wissen darum. Willst du im Markt wirklich aufsteigen, sind zwei Dinge nicht verhandelbar: **Deutsch (B2-C1)** und die **Architektenkammer-Lizenz (Master + ~2 Jahre Praxis).** Ohne beides stößt du an die Decke; mit beiden öffnet sich das Band von 55-70k+ und echte Projektverantwortung. Als internationale:r Studierende:r sei strategisch: mach Praktika, halte dein Portfolio aktuell, investiere früh in Deutsch und nutze das 18-Monate-Fenster nach dem Abschluss effizient. Komm mit Leidenschaft — aber bleib mit Plan.

---

*Dieser Beitrag dient der allgemeinen Information mit Stand Anfang 2026; Gehälter, Visaregeln, Kammer-Voraussetzungen und die Praxis der Bundesländer ändern sich mit der Zeit und je nach Fall. Prüfe vor einer Entscheidung die aktuellen Angaben der jeweiligen Architektenkammer, des Arbeitgebers und der zuständigen Ausländerbehörde.*
MD;
        $enBody = <<<'MD'
Studying architecture in Germany is a labour of love. But passion doesn't pay the bills. This article sets the romance aside and honestly lays out the **financial and market reality of working as an architect in Germany**: which sectors exist, what the entry salary actually is, why it's lower than engineering, and how you survive in this market as an international student.

Let's start with an important distinction: **Architektur (architecture) and Bauingenieurwesen (civil engineering) are not the same thing.** Architecture is about design, space and concept; civil engineering is about structure, statics and construction. In terms of the job market and salary, these two worlds diverge sharply — and the engineering side usually earns more. You need to know this from the outset.

## Sectors: where does an architect work?

The main fields open to you with an architecture degree in Germany:

- **Architekturbüro (architecture office):** The classic path. From small boutique practices to large international offices. Most graduates start here. Salaries are most variable here.
- **Urban planning / Stadtplanung / Städtebau:** Municipalities, planning offices, public bodies. More institutional, more stable, often tied to the public pay scale (TVöD).
- **Construction sector / site side:** Project management, Bauleitung (site supervision), construction firms. Here architects compete with engineers and usually take more technical-managerial roles.
- **Public sector (öffentlicher Dienst):** Bauamt (building authority), public institutions. Stable, but with a clear salary ceiling; progression follows TVöD/TV-L.
- **Adjacent fields:** BIM coordination, interior design, landscape architecture, visualisation, product/furniture design, academia.

## The salary reality: why low, and how much?

I'll be honest here because most sources sugar-coat it. **Entry salary in architecture is noticeably LOWER than in engineering.** The reason is supply and demand: architecture is a popular field with many graduates and relatively few open positions; by contrast, civil/mechanical/electrical engineering has many openings and few candidates. The market price reflects that gap.

| Role / stage | Approx. gross annual (€) | Note |
|---|---|---|
| Entry at an architecture office (graduate) | ~35,000 – 45,000 | Near the lower band in small offices |
| Architect with 3-5 years' experience | ~45,000 – 55,000 | With Kammer registration + more responsibility |
| Kammer-registered, project-leading | ~55,000 – 70,000+ | Upper band with Bauleitung/project management |
| Public sector (TVöD, Bauamt) | ~48,000 – 62,000 | Pay-scale bound, stable |
| For comparison: entry engineer | ~45,000 – 55,000 | Usually higher at the same experience |

**Bold fact: in architecture you start at ~€35-45k; an engineer at the same stage usually earns ~€45-55k.** So make your choice with that in mind.

*As of 2025/2026, approximate; it varies a lot by region (the south = Bavaria/Baden-Württemberg pays high but rents are high too), office size and the economy, and changes yearly. When you get an offer, calculate the **net** figure (tax, health insurance, rent) for that specific city and **verify** it.*

## Roles: Entwurf, Bauleitung, BIM

An architecture career isn't uniform. Where you aim affects your salary and job security:

- **Entwurf (design):** The most "architect-like" but most competitive and usually lowest-paid field. Concept, design, competitions. The passion is here, the money less so.
- **Ausführungsplanung (execution planning):** Detail drawing, technical documentation. Stable demand.
- **Bauleitung (site supervision):** On-site coordination, subcontractor management. Better paid, high responsibility; German is **mandatory** here.
- **BIM coordination:** A growing field. Architects with Revit/ArchiCAD/BIM skills have an edge — invest in this skill.

## After graduation: the 18-month job-search residence

If you're an international student and you **graduated in Germany**, you're on the most advantageous path: after graduating you get an **18-month residence to look for work** (Studienabsolvent). If you land a qualified job within that window, you switch from the student to the work permit (**Zweckwechsel**). Since architecture work usually counts as degree-qualified, this switch is feasible.

The routes in brief:

1. **You studied in Germany** → 18-month job search → Zweckwechsel once you land a job. The easiest route.
2. **From abroad with an offer** → work visa / Blue Card (recognition + threshold). Process: [work visa with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).
3. **You have a degree but no offer** → come on the Job Seeker Visa and search on the ground. Master's vs. job-seeker strategy: [master's vs. job-seeker visa](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

**Careful:** the Blue Card has a salary threshold. Because architecture salaries sit near the lower band, you may not meet it at entry; in that case you start on the regular work permit. Factor this in from the start.

## German + licence: the two keys that open a career

Two things set your ceiling:

**German.** The studio culture may be English, but **office day-to-day, clients, authorities, the building site and contracts all run in German.** An architect without German reaches only a tiny slice of the hiring pool and usually stays in the background (visualisation, pure drafting). **Bold fact: for practising architecture, at least B2-C1 German is effectively mandatory.**

**Licence (Architektenkammer).** "Architekt" is a protected title. For the right to submit building applications (Bauvorlageberechtigung) and real project responsibility, you need to register with a state **Architektenkammer** (chamber of architects). Typical requirement: an accredited degree (usually 5 years = Bachelor + Master, min. ~300 ECTS) **+ ~2 years of practical experience.** Without Kammer registration you can still work, but you hit a ceiling — you can't submit building documents under your own signature or call yourself "Architekt". **The licence is the key that opens salary and career.** Details: [becoming a licensed architect in Germany](/en/blog/becoming-a-licensed-architect-in-germany-architektenkammer-en).

## Strategy for international students

A concrete plan to survive in this market:

- **Do a Praktikum (internship):** An office internship during your studies is the strongest channel that turns into a job at graduation. Most hires in Germany come via internship/referral.
- **Portfolio (Mappe):** Keep it constantly up to date; present it in line with German office aesthetics (clean, technical, concept-driven).
- **Invest in German:** B2-C1 opens an invisible door. Start early.
- **BIM / software skills:** Revit/ArchiCAD/Rhino+Grasshopper raise your market value.
- **Network:** BDA events (Bund Deutscher Architekten), your university network, LinkedIn. Most jobs change hands without ever being advertised.
- **Licence plan:** Master's + 2 years of practice → aim for Kammer registration. That's the difference between "degree holder" and "real architect".

Other articles in the same cluster: [studying architecture as a foreigner](/en/blog/studying-architecture-in-germany-as-a-foreigner-en) · [English-taught architecture master's without German](/en/blog/english-taught-architecture-masters-in-germany-without-german-en) · [becoming a licensed architect (Architektenkammer)](/en/blog/becoming-a-licensed-architect-in-germany-architektenkammer-en). For comparison, the engineering side: [working as an engineer in Germany](/en/blog/working-as-an-engineer-in-germany-blue-card-salary-en).

## Conclusion & honest advice

An architecture career in Germany is real and possible, but keep your eyes open. **The entry salary is lower than in engineering** (~€35-45k) — love this profession knowing that. If you genuinely want to rise in this market, two things are non-negotiable: **German (B2-C1)** and the **Architektenkammer licence (master's + ~2 years of practice).** Without both you hit a ceiling; with both, the €55-70k+ band and real project responsibility open up. As an international student, be strategic: do internships, keep your portfolio current, invest in German early, and use the 18-month post-graduation window efficiently. Come with passion — but stay with a plan.

---

*This article is general information as of early 2026; salaries, visa rules, Kammer requirements and state-level practice change over time and by case. Before deciding, verify the current information from the relevant Architektenkammer, the employer and the responsible immigration authority.*
MD;

        $variants = [
            'tr' => ['slug'=>'working-as-an-architect-in-germany-salary-job-market',    'title'=>'Almanya\'da Mimar Olarak Çalışmak: Maaş ve İş Piyasası Gerçeği (2026)', 'excerpt'=>'Almanya\'da mimar olarak çalışmanın dürüst gerçeği: sektörler, giriş maaşı (~35-45k, mühendislikten düşük), 18 aylık iş-arama oturumu, Almanca ve Architektenkammer lisansının kariyeri nasıl açtığı.', 'meta_title'=>'Almanya\'da Mimar Olarak Çalışmak: Maaş Gerçeği (2026)', 'meta_description'=>'Almanya\'da mimar maaşı (~35-45k giriş, mühendislikten düşük), sektörler, roller, 18 ay iş-arama oturumu ve Kammer lisansının önemi — dürüst rehber.', 'body'=>$trBody],
            'de' => ['slug'=>'working-as-an-architect-in-germany-salary-job-market-de', 'title'=>'Als Architekt in Deutschland arbeiten: Gehalt & Arbeitsmarkt-Realität (2026)', 'excerpt'=>'Die ehrliche Realität, als Architekt in Deutschland zu arbeiten: Branchen, Einstiegsgehalt (~35-45k, niedriger als Ingenieurwesen), 18 Monate Jobsuche, und wie Deutsch und die Architektenkammer-Lizenz die Karriere öffnen.', 'meta_title'=>'Als Architekt in Deutschland arbeiten: Gehalt (2026)', 'meta_description'=>'Architektengehalt in Deutschland (~35-45k Einstieg, niedriger als Ingenieurwesen), Branchen, Rollen, 18 Monate Jobsuche und die Kammer-Lizenz — ehrlicher Guide.', 'body'=>$deBody],
            'en' => ['slug'=>'working-as-an-architect-in-germany-salary-job-market-en', 'title'=>'Working as an Architect in Germany: Salary & Job-Market Reality (2026)', 'excerpt'=>'The honest reality of working as an architect in Germany: sectors, entry salary (~35-45k, lower than engineering), the 18-month job-search residence, and how German and the Architektenkammer licence open a career.', 'meta_title'=>'Working as an Architect in Germany: Salary Reality (2026)', 'meta_description'=>'Architect salary in Germany (~€35-45k entry, lower than engineering), sectors, roles, the 18-month job-search residence and the Kammer licence — an honest guide.', 'body'=>$enBody],
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
            'working-as-an-architect-in-germany-salary-job-market',
            'working-as-an-architect-in-germany-salary-job-market-de',
            'working-as-an-architect-in-germany-salary-job-market-en',
        ])->delete();
    }
};

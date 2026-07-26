<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Sosyoloji diplomasıyla Almanya'da çalışmak — araştırma, veri, kariyer (2026).
 * Doğrulandı: generalist sosyal bilim diploması, uzmanlaşma + kantitatif/veri becerisi (R/Python/anket)
 * istihdamı belirler; maaş araştırma/veri/İK ~40-50k (hedge); Blue Card 2026 genel ~50.700€.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'b8c30000-3333-4f4f-9f50-bb0fcc15ff03';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Sosyoloji ya da sosyal bilimler diploman var ve Almanya'da bununla gerçekten geçineceğin bir iş bulunup bulunmayacağını merak ediyorsun. Dürüst cevap: evet, bulunur — ama diplomanın kendisi seni işe almaz. Seni işe alan şey, o diplomanın üstüne koyduğun somut beceridir; özellikle **veri ve araştırma metodu** becerisi. Bu yazı hangi sektörlerin gerçekten kapı açtığını, neyin maaşı belirlediğini ve uluslararası biri olarak nasıl bir strateji kuracağını anlatıyor.

## Sosyologlar Almanya'da hangi sektörlerde çalışır?

Sosyoloji "generalist" bir diplomadır: tek bir meslek kapısına değil, çok sayıda alana açılır. Almanya'da en çok iş çıkan alanlar şunlar:

- **Akademik ve kurumsal araştırma:** üniversiteler, WZB, Max Planck, DIW, GESIS gibi enstitüler; sosyal araştırma, anket tasarımı, değerlendirme (Evaluation).
- **Piyasa ve pazar araştırması (Marktforschung):** GfK, Ipsos, Nielsen tarzı şirketler — anket, segmentasyon, tüketici davranışı.
- **İnsan kaynakları (HR / Personal):** işe alım, örgüt gelişimi, çeşitlilik/kapsayıcılık, çalışan anketleri.
- **Danışmanlık:** kamu politikası, örgüt danışmanlığı, sosyal etki değerlendirmesi.
- **Veri ve sosyal analitik (büyüyen alan):** People Analytics, kamu veri birimleri, sosyal veri bilimi.
- **NGO ve kamu/idare:** göç, sosyal hizmet, kentsel planlama, vakıflar, belediyeler, bakanlıklar.
- **UX / kullanıcı araştırması:** tech şirketlerinde niteliksel görüşme + niceliksel test — sosyologlar için hızlı büyüyen bir niş.

Görülüyor ki tek bir "sosyolog kadrosu" yok; iş ilanları çoğu zaman "Research Analyst", "HR Specialist", "UX Researcher", "Referent:in" gibi başlıklar altında.

## Kantitatif ve veri becerisi neden belirleyici?

Burası yazının en kritik yeri. Almanya'da sosyoloji mezunları arasında **işi alanla alamayanı ayıran ana faktör veri becerisidir.** İşverenler "insanları anlıyorum" diyenle "insanları veriyle ölçüp raporluyorum" diyeni aynı görmez.

Somut olarak aranan beceriler:

- **İstatistik:** regresyon, hipotez testi, örnekleme, anket metodolojisi.
- **R veya Python:** veri temizleme, analiz, görselleştirme (özellikle R sosyal bilimde çok yaygın).
- **SPSS / Stata:** hâlâ birçok enstitü ve piyasa araştırması şirketinde standart.
- **Anket tasarımı ve saha yönetimi**, **SQL** temeli, veri görselleştirme (Tableau/Power BI).

Bir cümlede: **niteliksel eğitimin sana konuyu, niceliksel becerin sana işi kazandırır.** Master'da metot derslerini ciddiye al, bir dönem projesi R/Python ile analiz yap, GitHub'a koy. Bu, "how to break into data science" tarafındaki mantığın sosyal bilim versiyonudur — ayrıntı için [Almanya'da Data Science & AI'ye nasıl girilir](/tr/blog/how-to-break-into-data-science-ai-in-germany) yazısına bak.

## Uzmanlaşma yolları: generalisti nasıl "işe alınabilir" yaparsın?

Generalist diploma bir zayıflık değil, ham maddedir — ama işlenmesi gerekir. En işe yarar uzmanlaşma eksenleri:

1. **Metot/veri ekseni:** anket + istatistik + R/Python → araştırma, analitik, UX research.
2. **İK ekseni:** örgüt sosyolojisi + HR yazılımları + iş hukuku temeli → People & Culture rolleri.
3. **Politika/idare ekseni:** göç, kent, sosyal politika + Almanca → kamu, NGO, vakıf.
4. **Danışmanlık ekseni:** proje yönetimi + iletişim + veri → consulting.

Staj (Praktikum) ve Werkstudent işleri burada altın değerindedir; Almanya'da ilk tam zamanlı iş çoğu zaman staj yaptığın yerden gelir. Hangi eksenin sana uygun olduğunu iş piyasası tarafından okumak için [Almanya'da sosyoloji diplomasıyla ne yapılır](/tr/blog/what-to-do-with-a-sociology-degree-in-germany-job-market) yazısı işine yarar.

## Maaş: gerçekçi rakamlar

Aşağıdaki rakamlar **kabaca 2025/2026 brüt yıllık** değerlerdir ve şehre, sektöre, deneyime göre ciddi değişir. Kesin sayı değil, büyüklük mertebesi olarak oku.

| Rol / alan | Giriş (brüt/yıl, yaklaşık) | Birkaç yıl sonra |
|---|---|---|
| Piyasa/sosyal araştırma analisti | ~38.000–46.000 € | ~50.000–58.000 € |
| İK (HR) uzmanı | ~40.000–48.000 € | ~55.000–65.000 € |
| Veri / People Analytics | ~45.000–52.000 € | ~60.000–70.000 € |
| Danışmanlık | ~45.000–55.000 € | ~60.000–75.000 € |
| UX araştırmacı | ~45.000–55.000 € | ~60.000–72.000 € |
| NGO / kamu (giriş) | ~35.000–42.000 € | ~45.000–52.000 € (TVöD skalası) |

**Kalın gerçek:** Veri/analitik becerisi olan sosyolog, saf niteliksel profilden tipik olarak belirgin daha yüksek maaş alır. NGO ve kamu anlamlı iş sunar ama ücret tavanı özel sektörün altındadır.

## Almanca gerçeği ve Blue Card (2026)

- **Araştırma ve uluslararası ekipler** çoğunlukla İngilizce çalışır — Almancasız da giriş mümkün.
- **Alman kamu, NGO, İK ve danışmanlığın** büyük kısmı için **Almanca (çoğu zaman C1) fiilen şarttır.** Uzun vadeli kariyer için Almanca en yüksek getirili yatırımın.
- **Blue Card (2026, yaklaşık; doğrula):** genel maaş eşiği **~50.700 €/yıl**, darboğaz meslekler ve yeni mezunlar için **~45.934 €/yıl**. Sosyal bilim rolleri bu eşiğin bazen altında kalır; o durumda normal nitelikli çalışma vizesi devreye girer. Vize sürecinin adımları için [iş teklifiyle Almanya çalışma vizesi](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) yazısına bak.

## İş arama ve strateji

- **Mezuniyet sonrası 18 aya kadar iş-arama izni** (nitelikli mezun için) — bu süreyi staj/Werkstudent ile köprüle, boş bırakma.
- Platformlar: **StepStone, Indeed, LinkedIn, Xing**, üniversite kariyer portalları, enstitü kariyer sayfaları, **interamt.de** (kamu).
- CV'ni **rol bazında** yaz: "sosyolog" değil, "survey analyst / people analytics / UX researcher" diye pozisyonla.
- Bir **portfolyo** kur: R/Python ile yaptığın bir analiz, temiz bir rapor, görselleştirme. Sosyal bilimde bu hâlâ nadir ve seni öne çıkarır.
- Almancasız başlayacaksan araştırma/UX/uluslararası şirket ekseninden gir, paralelde C1'e yürü.

## Sonuç & dürüst tavsiye

Sosyoloji Almanya'da işsiz bırakan bir diploma değil — ama **kendini pazarlamayan** sosyoloğu işsiz bırakabilir. Tek net meslek yolu yok; işi getiren şey uzmanlaşma ve özellikle **kantitatif/veri becerisidir.** R/Python + istatistik + anket öğren, staj yap, portfolyo kur, uzun vadede Almanca'ya yatır. Bunu yaparsan araştırma, İK, veri, danışmanlık ve UX kapıları gerçekten açılır. Almancasız İngilizce master rotasını düşünüyorsan [İngilizce sosyoloji/sosyal bilim master programları](/tr/blog/english-taught-sociology-and-social-science-masters-in-germany), en baştan alanı tanımak istiyorsan [Almanya'da sosyoloji & sosyal bilimler okumak](/tr/blog/studying-sociology-and-social-sciences-in-germany-as-a-foreigner) yazısı iyi başlangıç.

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır; maaşlar, Blue Card eşikleri ve vize kuralları değişebilir. Karar vermeden önce resmi kaynaklardan (Make it in Germany, ilgili üniversite/enstitü, yerel Ausländerbehörde) güncel bilgiyi doğrula.*
MD;

        $deBody = <<<'MD'
Du hast einen Abschluss in Soziologie oder Sozialwissenschaften und fragst dich, ob du damit in Deutschland wirklich einen Job findest, von dem du leben kannst. Ehrliche Antwort: ja — aber der Abschluss allein stellt dich nicht ein. Eingestellt wirst du wegen der konkreten Fähigkeiten, die du daraufsetzt, vor allem **Daten- und Methodenkompetenz**. Dieser Text zeigt dir, welche Branchen wirklich Türen öffnen, was das Gehalt bestimmt und welche Strategie für dich als internationale:r Absolvent:in sinnvoll ist.

## In welchen Branchen arbeiten Soziolog:innen in Deutschland?

Soziologie ist ein „Generalisten"-Abschluss: er öffnet nicht eine einzige Berufstür, sondern viele Felder. Die beschäftigungsstärksten Bereiche:

- **Akademische und institutionelle Forschung:** Universitäten, WZB, Max Planck, DIW, GESIS; Sozialforschung, Fragebogendesign, Evaluation.
- **Marktforschung:** Firmen wie GfK, Ipsos, Nielsen — Befragung, Segmentierung, Konsumverhalten.
- **Personal (HR):** Recruiting, Organisationsentwicklung, Diversity, Mitarbeiterbefragungen.
- **Beratung:** Politikberatung, Organisationsberatung, Wirkungsanalyse.
- **Daten und Social Analytics (wachsend):** People Analytics, Datenstellen der öffentlichen Hand, Social Data Science.
- **NGO und öffentlicher Dienst:** Migration, Soziales, Stadtplanung, Stiftungen, Kommunen, Ministerien.
- **UX / User Research:** in Tech-Unternehmen qualitative Interviews + quantitatives Testing — eine schnell wachsende Nische für Soziolog:innen.

Es gibt also keine einzige „Soziolog:innen-Stelle"; Ausschreibungen laufen meist unter Titeln wie „Research Analyst", „HR Specialist", „UX Researcher" oder „Referent:in".

## Warum ist quantitative und Datenkompetenz entscheidend?

Das ist der wichtigste Punkt. In Deutschland ist **Datenkompetenz der Hauptfaktor, der unter Soziologie-Absolvent:innen entscheidet, wer den Job bekommt.** Arbeitgeber sehen einen Unterschied zwischen „ich verstehe Menschen" und „ich messe und berichte Menschen mit Daten".

Konkret gefragte Fähigkeiten:

- **Statistik:** Regression, Hypothesentests, Stichproben, Befragungsmethodik.
- **R oder Python:** Datenaufbereitung, Analyse, Visualisierung (R ist in der Sozialwissenschaft sehr verbreitet).
- **SPSS / Stata:** in vielen Instituten und Marktforschungsfirmen weiterhin Standard.
- **Fragebogendesign und Feldsteuerung**, **SQL**-Grundlagen, Datenvisualisierung (Tableau/Power BI).

In einem Satz: **deine qualitative Ausbildung verschafft dir das Thema, deine quantitativen Fähigkeiten verschaffen dir den Job.** Nimm die Methodenkurse im Master ernst, mach ein Projekt in R/Python, stell es auf GitHub. Das ist die sozialwissenschaftliche Version der Logik aus [Einstieg in Data Science & KI in Deutschland](/de/blog/how-to-break-into-data-science-ai-in-germany-de).

## Spezialisierungswege: wie machst du den Generalisten einstellbar?

Ein Generalisten-Abschluss ist keine Schwäche, sondern Rohmaterial — er muss aber bearbeitet werden. Die nützlichsten Spezialisierungsachsen:

1. **Methoden/Daten:** Befragung + Statistik + R/Python → Forschung, Analytics, UX Research.
2. **HR:** Organisationssoziologie + HR-Tools + arbeitsrechtliche Grundlagen → People-&-Culture-Rollen.
3. **Politik/Verwaltung:** Migration, Stadt, Sozialpolitik + Deutsch → öffentlicher Dienst, NGO, Stiftung.
4. **Beratung:** Projektmanagement + Kommunikation + Daten → Consulting.

Praktika und Werkstudentenstellen sind hier Gold wert; der erste Vollzeitjob in Deutschland kommt oft aus dem Praktikumsbetrieb. Um zu lesen, welche Achse zu dir passt, hilft [Was macht man mit einem Soziologie-Abschluss in Deutschland](/de/blog/what-to-do-with-a-sociology-degree-in-germany-job-market-de).

## Gehalt: realistische Zahlen

Die folgenden Zahlen sind **grobe Bruttojahresgehälter für 2025/2026** und variieren stark nach Stadt, Branche und Erfahrung. Lies sie als Größenordnung, nicht als exakten Wert.

| Rolle / Bereich | Einstieg (brutto/Jahr, ca.) | Nach einigen Jahren |
|---|---|---|
| Markt-/Sozialforschungsanalyst:in | ~38.000–46.000 € | ~50.000–58.000 € |
| HR-Spezialist:in | ~40.000–48.000 € | ~55.000–65.000 € |
| Daten / People Analytics | ~45.000–52.000 € | ~60.000–70.000 € |
| Beratung | ~45.000–55.000 € | ~60.000–75.000 € |
| UX Researcher | ~45.000–55.000 € | ~60.000–72.000 € |
| NGO / öffentlicher Dienst (Einstieg) | ~35.000–42.000 € | ~45.000–52.000 € (TVöD-Skala) |

**Klare Wahrheit:** Ein:e Soziolog:in mit Daten-/Analytics-Kompetenz verdient typischerweise deutlich mehr als ein rein qualitatives Profil. NGO und öffentlicher Dienst bieten sinnvolle Arbeit, aber die Gehaltsobergrenze liegt unter der Privatwirtschaft.

## Deutsch-Realität und Blue Card (2026)

- **Forschung und internationale Teams** arbeiten oft auf Englisch — ein Einstieg ohne Deutsch ist möglich.
- Für einen Großteil von **öffentlichem Dienst, NGO, HR und Beratung** ist **Deutsch (oft C1) faktisch Voraussetzung.** Für die langfristige Karriere ist Deutsch deine renditestärkste Investition.
- **Blue Card (2026, ca.; bitte prüfen):** allgemeine Gehaltsschwelle **~50.700 €/Jahr**, für Engpassberufe und Berufseinsteiger:innen **~45.934 €/Jahr**. Sozialwissenschaftliche Rollen liegen manchmal darunter; dann greift die reguläre Fachkräfte-Arbeitserlaubnis. Zu den Schritten des Visumsprozesses siehe [Arbeitsvisum für Deutschland mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).

## Jobsuche und Strategie

- **Bis zu 18 Monate Jobsuche-Erlaubnis nach dem Abschluss** (für qualifizierte Absolvent:innen) — überbrücke diese Zeit mit Praktikum/Werkstudent, lass sie nicht leer.
- Plattformen: **StepStone, Indeed, LinkedIn, Xing**, Uni-Karriereportale, Institutsseiten, **interamt.de** (öffentlicher Dienst).
- Schreib deinen Lebenslauf **rollenbasiert**: nicht „Soziologe", sondern „Survey Analyst / People Analytics / UX Researcher".
- Bau ein **Portfolio**: eine Analyse in R/Python, ein sauberer Bericht, eine Visualisierung. In der Sozialwissenschaft ist das selten und hebt dich hervor.
- Ohne Deutsch: steig über Forschung/UX/internationale Firmen ein und arbeite parallel auf C1 hin.

## Fazit & ehrlicher Rat

Soziologie ist in Deutschland kein Abschluss, der dich arbeitslos macht — aber er kann Soziolog:innen arbeitslos lassen, **die sich nicht vermarkten.** Es gibt keinen einzelnen klaren Berufsweg; was den Job bringt, ist Spezialisierung und vor allem **quantitative/Datenkompetenz.** Lerne R/Python + Statistik + Befragung, mach Praktika, bau ein Portfolio, investiere langfristig in Deutsch. Dann öffnen sich Forschung, HR, Daten, Beratung und UX wirklich. Wenn du die englischsprachige Master-Route ohne Deutsch überlegst, ist [englischsprachige Soziologie-/Sozialwissenschafts-Master](/de/blog/english-taught-sociology-and-social-science-masters-in-germany-de) hilfreich; für den Einstieg ins Fach [Soziologie & Sozialwissenschaften in Deutschland studieren](/de/blog/studying-sociology-and-social-sciences-in-germany-as-a-foreigner-de).

*Dieser Text dient der allgemeinen Information mit Stand Anfang 2026; Gehälter, Blue-Card-Schwellen und Visaregeln können sich ändern. Prüfe vor einer Entscheidung aktuelle Angaben aus offiziellen Quellen (Make it in Germany, jeweilige Universität/Institut, örtliche Ausländerbehörde).*
MD;

        $enBody = <<<'MD'
You have a degree in sociology or the social sciences, and you're wondering whether you can actually find a job in Germany that pays the bills. Honest answer: yes — but the degree alone won't get you hired. What gets you hired is the concrete skill you stack on top of it, above all **data and research-methods skill**. This post explains which sectors really open doors, what determines pay, and what strategy makes sense for you as an international graduate.

## Which sectors do sociologists work in in Germany?

Sociology is a "generalist" degree: it opens not one career door but many fields. The areas with the most jobs in Germany:

- **Academic and institutional research:** universities, and institutes like WZB, Max Planck, DIW, GESIS; social research, survey design, evaluation.
- **Market research (Marktforschung):** firms such as GfK, Ipsos, Nielsen — surveys, segmentation, consumer behaviour.
- **Human resources (HR / Personal):** recruiting, organisational development, diversity, employee surveys.
- **Consulting:** policy advice, organisational consulting, social-impact assessment.
- **Data and social analytics (growing):** People Analytics, public-sector data units, social data science.
- **NGOs and public administration:** migration, social services, urban planning, foundations, municipalities, ministries.
- **UX / user research:** in tech companies, qualitative interviews plus quantitative testing — a fast-growing niche for sociologists.

So there is no single "sociologist position"; postings usually run under titles like "Research Analyst", "HR Specialist", "UX Researcher" or "Referent:in".

## Why is quantitative and data skill decisive?

This is the most important point. In Germany, **data skill is the main factor that decides which sociology graduate gets the job.** Employers see a difference between "I understand people" and "I measure and report on people with data".

Concretely sought-after skills:

- **Statistics:** regression, hypothesis testing, sampling, survey methodology.
- **R or Python:** data cleaning, analysis, visualisation (R is very common in the social sciences).
- **SPSS / Stata:** still standard in many institutes and market-research firms.
- **Survey design and fieldwork management**, **SQL** basics, data visualisation (Tableau/Power BI).

In one sentence: **your qualitative training gives you the topic; your quantitative skill gives you the job.** Take the methods courses in your master's seriously, do a term project in R/Python, put it on GitHub. This is the social-science version of the logic in [how to break into data science & AI in Germany](/en/blog/how-to-break-into-data-science-ai-in-germany-en).

## Specialisation paths: how do you make a generalist hireable?

A generalist degree isn't a weakness, it's raw material — but it needs to be worked. The most useful specialisation axes:

1. **Methods/data:** surveys + statistics + R/Python → research, analytics, UX research.
2. **HR:** organisational sociology + HR tools + basic labour law → People & Culture roles.
3. **Policy/administration:** migration, urban, social policy + German → public sector, NGO, foundation.
4. **Consulting:** project management + communication + data → consulting.

Internships (Praktikum) and Werkstudent jobs are worth gold here; the first full-time job in Germany often comes from the place you interned. To read which axis suits you from the job-market side, see [what to do with a sociology degree in Germany](/en/blog/what-to-do-with-a-sociology-degree-in-germany-job-market-en).

## Salary: realistic numbers

The figures below are **rough gross annual salaries for 2025/2026** and vary a lot by city, sector and experience. Read them as an order of magnitude, not an exact value.

| Role / field | Entry (gross/year, approx.) | After a few years |
|---|---|---|
| Market/social research analyst | ~€38,000–46,000 | ~€50,000–58,000 |
| HR specialist | ~€40,000–48,000 | ~€55,000–65,000 |
| Data / People Analytics | ~€45,000–52,000 | ~€60,000–70,000 |
| Consulting | ~€45,000–55,000 | ~€60,000–75,000 |
| UX researcher | ~€45,000–55,000 | ~€60,000–72,000 |
| NGO / public sector (entry) | ~€35,000–42,000 | ~€45,000–52,000 (TVöD scale) |

**Bold truth:** a sociologist with data/analytics skill typically earns noticeably more than a purely qualitative profile. NGOs and the public sector offer meaningful work, but the salary ceiling sits below the private sector.

## The German-language reality and the Blue Card (2026)

- **Research and international teams** often work in English — entry without German is possible.
- For much of the **public sector, NGOs, HR and consulting**, **German (often C1) is effectively required.** For a long-term career, German is your highest-return investment.
- **Blue Card (2026, approximate; verify):** the general salary threshold is **~€50,700/year**, and for shortage occupations and new graduates **~€45,934/year**. Social-science roles sometimes fall below that; then the regular skilled-worker permit applies. For the steps of the visa process, see [Germany work visa with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).

## Job search and strategy

- **Up to 18 months of job-search permit after graduation** (for qualified graduates) — bridge that time with internships/Werkstudent, don't leave it empty.
- Platforms: **StepStone, Indeed, LinkedIn, Xing**, university career portals, institute career pages, **interamt.de** (public sector).
- Write your CV **role-based**: not "sociologist", but "survey analyst / people analytics / UX researcher".
- Build a **portfolio**: an analysis in R/Python, a clean report, a visualisation. In social science this is still rare and makes you stand out.
- If you start without German, enter via the research/UX/international-company axis and work toward C1 in parallel.

## Conclusion & honest advice

Sociology is not a degree that leaves you unemployed in Germany — but it can leave unemployed the sociologist who **doesn't market themselves.** There is no single clear career path; what brings the job is specialisation and above all **quantitative/data skill.** Learn R/Python + statistics + surveys, do internships, build a portfolio, invest in German for the long run. Do that, and research, HR, data, consulting and UX doors genuinely open. If you're considering the English-taught master's route without German, [English-taught sociology/social-science master's programmes](/en/blog/english-taught-sociology-and-social-science-masters-in-germany-en) helps; to get to know the field from the start, see [studying sociology & social sciences in Germany](/en/blog/studying-sociology-and-social-sciences-in-germany-as-a-foreigner-en).

*This post is general information as of early 2026; salaries, Blue Card thresholds and visa rules can change. Before deciding, verify current details from official sources (Make it in Germany, the relevant university/institute, your local Ausländerbehörde).*
MD;

        $variants = [
            'tr' => ['slug'=>'working-with-a-sociology-degree-in-germany-research-data-and-careers',    'title'=>'Sosyoloji Diplomasıyla Almanya\'da Çalışmak: Araştırma, Veri ve Kariyer (2026)', 'excerpt'=>'Sosyoloji/sosyal bilimler diplomasıyla Almanya\'da çalışmak: hangi sektörler kapı açar (araştırma, piyasa araştırması, İK, danışmanlık, veri/sosyal analitik, NGO, kamu, UX), kantitatif/veri becerisi (R/Python/anket) neden istihdamı belirler, maaş aralıkları ve Blue Card 2026 gerçeği — dürüst bir rehber.', 'meta_title'=>'Sosyoloji Diplomasıyla Almanya\'da Çalışmak (2026)', 'meta_description'=>'Almanya\'da sosyoloji kariyeri: araştırma, İK, danışmanlık, veri/UX; R/Python/anket becerisi neden şart, maaş ~40-50k ve Blue Card 2026 (~50.700€, doğrula).', 'body'=>$trBody],
            'de' => ['slug'=>'working-with-a-sociology-degree-in-germany-research-data-and-careers-de', 'title'=>'Mit einem Soziologie-Abschluss in Deutschland arbeiten: Forschung, Daten und Karriere (2026)', 'excerpt'=>'Mit einem Soziologie-/Sozialwissenschafts-Abschluss in Deutschland arbeiten: welche Branchen Türen öffnen (Forschung, Marktforschung, HR, Beratung, Daten/Analytics, NGO, öffentlicher Dienst, UX), warum quantitative/Datenkompetenz (R/Python/Befragung) über die Einstellung entscheidet, Gehaltsspannen und die Blue-Card-Realität 2026.', 'meta_title'=>'Mit Soziologie-Abschluss in Deutschland arbeiten (2026)', 'meta_description'=>'Soziologie-Karriere in Deutschland: Forschung, HR, Beratung, Daten/UX; warum R/Python/Befragung entscheidend sind, Gehalt ~40-50k und Blue Card 2026 (~50.700€, prüfen).', 'body'=>$deBody],
            'en' => ['slug'=>'working-with-a-sociology-degree-in-germany-research-data-and-careers-en', 'title'=>'Working With a Sociology Degree in Germany: Research, Data and Careers (2026)', 'excerpt'=>'Working with a sociology/social-science degree in Germany: which sectors open doors (research, market research, HR, consulting, data/analytics, NGOs, public sector, UX), why quantitative/data skill (R/Python/surveys) decides who gets hired, salary ranges and the 2026 Blue Card reality — an honest guide.', 'meta_title'=>'Working With a Sociology Degree in Germany (2026)', 'meta_description'=>'Sociology careers in Germany: research, HR, consulting, data/UX; why R/Python/surveys are decisive, salary ~€40-50k and the 2026 Blue Card (~€50,700, verify).', 'body'=>$enBody],
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
            'working-with-a-sociology-degree-in-germany-research-data-and-careers',
            'working-with-a-sociology-degree-in-germany-research-data-and-careers-de',
            'working-with-a-sociology-degree-in-germany-research-data-and-careers-en',
        ])->delete();
    }
};

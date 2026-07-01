<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): İşletme/BWL diplomasıyla Almanya iş piyasası & kariyer (2026).
 * Doğrulandı: BWL çok yönlü; danışmanlık/finans/Big 4/kurumsal DAX + Trainee-Programme yolları;
 * mezuniyet sonrası ~18 ay iş-arama izni → çalışma izni; Almanca+network kurumsal işte kritik.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'b4a40000-4444-4baa-9f30-aa01bb02ee04';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Elinde bir İşletme (BWL — Betriebswirtschaftslehre) diploması var ya da yakında olacak. Peki Almanya'da bununla **gerçekten ne yapabilirsin?** İyi haber: BWL, mühendislik gibi tek bir mesleğe kilitlemez — **çok yönlüdür.** Kötü haber (ve dürüst gerçek): tam da bu yüzden rekabet yüksektir ve çoğu kurumsal işte **Almanca ile network belirleyicidir.** Bu yazı iş piyasasını ve gerçekçi kariyer yollarını açıklıyor.

## BWL = çok yönlü: seni nereye götürür?
İşletme "genelci" bir diplomadır; bir uzmanlık dayatmaz, **kapı açar.** Bir BWL/business mezunu Almanya'da tipik olarak şu alanlara dağılır:

- **Danışmanlık (Consulting):** Strateji ve yönetim danışmanlığı — en prestijli ve en yüksek maaşlı giriş yollarından biri.
- **Finans & bankacılık:** Frankfurt Almanya'nın finans merkezi; yatırım bankacılığı, corporate finance, risk.
- **Kurumsal (DAX şirketleri):** Siemens, SAP, BMW, Bosch, Allianz gibi devlerde controlling, strateji, satın alma.
- **Big 4 (denetim/vergi/danışmanlık):** PwC, Deloitte, EY, KPMG — uluslararası mezunlar için **büyük ve düzenli** giriş kapısı.
- **Pazarlama & satış, İK, lojistik/tedarik zinciri, girişimcilik (Start-up).**

**Önemli:** BWL avantajını ancak **bir odak** seçersen kazanır. "Her şeyi biraz bilen" profil, net bir uzmanlığı (finans, pazarlama, veri analitiği…) olan adaya karşı zayıf kalır.

## Kariyer yolları: bir bakışta
| Kariyer yolu | Tipik işverenler | Giriş için ne gerekir | Almanca gerçeği |
|---|---|---|---|
| Danışmanlık | McKinsey, BCG, Roland Berger | Güçlü not + case, staj | Çok işte C1 avantaj/şart; bazı İngilizce projeler |
| Finans / bankacılık | Frankfurt bankaları, DAX | Finans odağı, staj | Genelde Almanca; İngilizce roller var |
| Big 4 | PwC, Deloitte, EY, KPMG | Muhasebe/vergi ilgisi | Denetimde Almanca çoğu zaman şart |
| Kurumsal (DAX) | Siemens, SAP, Bosch, Allianz | Trainee/staj, odak alan | Çoğu kurumsal işte Almanca |
| Pazarlama / dijital | Ajanslar, e-ticaret, start-up | Portföy, staj, araç bilgisi | Start-up'ta İngilizce daha yaygın |

*(2025/2026 itibarıyla tipik tablo; işverene ve role göre değişir — doğrula.)*

## Roller & giriş pozisyonları
Yeni mezun olarak tipik giriş noktaların:

- **Trainee-Programme:** Almanya'nın klasik kariyer başlangıcı. DAX şirketleri ve bankalar **12–24 aylık** yapılandırılmış rotasyon programları sunar; farklı departmanları dolaşır, kalıcı role geçersin. Uluslararası mezun için **çok değerli** bir kapı.
- **Big 4 giriş (Associate/Consultant):** PwC/Deloitte/EY/KPMG düzenli, çok sayıda giriş pozisyonu açar; öğrenme eğrisi dik, ağ kurma imkânı yüksek.
- **Junior Controller / Analyst / Consultant:** Kurumsal ve danışmanlıkta klasik ilk unvanlar.
- **Werkstudent → tam zaman:** Okurken bir şirkette Werkstudent olmak, mezuniyette **doğrudan işe** dönüşen en güçlü yollardan biridir.

**Kalın gerçek:** Almanya'da **staj (Praktikum) ve Werkstudent tecrübesi**, notlardan sonra en çok bakılan şeydir. Tecrübesiz "sadece diploma" ile başlamak zordur.

## Mezuniyet sonrası: 18 ay iş-arama izni → çalışma izni
Alman üniversitesinden mezun olan **AB-dışı uluslararası öğrenciler**, iş aramak için **18 aya kadar** oturma izni alabilir *(2025/2026 itibarıyla; doğrula).* Bu süre boyunca **kısıtsız çalışabilir** ve alanına uygun bir iş bulunca **çalışma iznine / Blue Card'a** geçersin.

- İşletme her zaman "darboğaz meslek" sayılmaz; bu yüzden Blue Card için genelde **genel maaş eşiği** geçerli olabilir *(2025'te ~48.300 €/yıl; role ve yıla göre değişir — doğrula).*
- Bu 18 ay bir **hediye değil, sayaç:** erken başla, mezun olmadan başvur.

Vize/izin mekaniği için: [Almanya'da Master mı yoksa iş-arama vizesi mi?](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) ve [iş teklifiyle çalışma vizesi süreci](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track).

## Almanca + network gerçeği (BWL'de kritik)
Bu, teknoloji/IT'den en büyük farktır. Yazılımda İngilizce çoğu zaman yeterken, **kurumsal/işletme dünyası günlük işi Almanca yürütür.**

- **Danışmanlık ve finansta** bazı İngilizce roller vardır; ama iç iletişim, müşteri ve terfi çoğu zaman **Almanca** ister.
- Hedef: mezuniyete kadar **B2, tercihen C1.** İngilizce başladıysan bile Almanca'yı paralel yürüt.
- **Network BWL'de belki en kritik faktör:** işlerin önemli kısmı ilan bile edilmeden, staj/Werkstudent, üniversite kariyer fuarları ve LinkedIn üzerinden dolar. Almanca LinkedIn profili + Alumni ağı fark yaratır.

## Uluslararası öğrenci için zorluklar + strateji
**Zorluklar (dürüstçe):** Almanca eksikliği en büyük engel; BWL çok popüler → rekabet yoğun; tecrübesiz mezunun "genelci" profili öne çıkmaz; bazı işverenlerde vize/dil tereddüdü.

**Strateji:**
1. **Erken bir odak seç** (finans, controlling, pazarlama, veri analitiği) — genelci kalma.
2. **Almanca'yı kariyerin merkezine koy** — B2/C1.
3. **Okurken çalış:** Werkstudent + en az bir güçlü Praktikum. Tecrübe, diplomadan sonra en önemli kart.
4. **Network kur:** kariyer fuarları, LinkedIn (DE), Alumni, hocalar.
5. **Trainee-Programme ve Big 4'e geniş başvur** — bunlar uluslararası mezun için en açık kapılar.
6. **18 aylık iş-arama iznini erken kullan** — mezun olmadan başvurmaya başla.

## Sonuç & dürüst tavsiye
Almanya'da BWL diploması **güçlü ve esnek** bir başlangıçtır — ama diploma tek başına iş getirmez. Kazanan formül net: **bir odak + Almanca (B2/C1) + gerçek tecrübe (Werkstudent/Praktikum) + network.** Trainee-Programme ve Big 4 en güvenilir giriş kapıların; 18 aylık iş-arama izni de rahat bir tampon. Almanca'yı ertelersen kurumsal kapıların çoğu kapalı kalır — bu, tavsiye değil, piyasa gerçeğidir.

Küme yazıları: [Almancasız İngilizce işletme master'ları](/tr/blog/english-taught-business-management-masters-in-germany-without-german) · [Kamu mu özel mi: Mannheim, WHU, Frankfurt School](/tr/blog/public-vs-private-business-schools-in-germany-mannheim-whu-frankfurt) · [Danışmanlık & finansta çalışmak: maaş, Blue Card](/tr/blog/working-in-business-consulting-finance-in-germany-blue-card-salary). İlgili: [Almanya'da İşletme (BWL) okumak rehberi](/tr/blog/studying-business-administration-bwl-in-germany-international-student-guide).

---
*Bu içerik genel bir rehberdir ve 2026 başı bilgisine dayanır. Maaşlar, vize eşikleri, iş-arama izni süreleri ve işveren koşulları yıla ve role göre değişir — başvurmadan önce resmî kaynaklardan (üniversite, Ausländerbehörde, işveren) teyit et.*
MD;

        $deBody = <<<'MD'
Du hast einen BWL-Abschluss (Betriebswirtschaftslehre) – oder bald. Aber was kannst du damit in Deutschland **wirklich anfangen?** Die gute Nachricht: BWL legt dich nicht auf einen einzigen Beruf fest wie das Ingenieurwesen – BWL ist **vielseitig.** Die ehrliche Nachricht: genau deshalb ist die Konkurrenz groß, und in den meisten Unternehmensjobs entscheiden **Deutsch und dein Netzwerk.** Dieser Artikel erklärt den Arbeitsmarkt und realistische Karrierewege.

## BWL = vielseitig: wohin führt dich das?
BWL ist ein „Generalisten"-Abschluss; er zwingt dir keine Spezialisierung auf, sondern **öffnet Türen.** Absolventinnen und Absolventen verteilen sich in Deutschland typischerweise auf:

- **Consulting (Unternehmensberatung):** Strategie- und Managementberatung – einer der prestigeträchtigsten und bestbezahlten Einstiege.
- **Finanzen & Banken:** Frankfurt ist Deutschlands Finanzzentrum; Investment Banking, Corporate Finance, Risk.
- **Konzerne (DAX-Unternehmen):** Controlling, Strategie, Einkauf bei Riesen wie Siemens, SAP, BMW, Bosch, Allianz.
- **Big 4 (Wirtschaftsprüfung/Steuern/Beratung):** PwC, Deloitte, EY, KPMG – ein **großes, regelmäßiges** Einstiegstor für internationale Absolventen.
- **Marketing & Vertrieb, HR, Logistik/Supply Chain, Gründung (Start-up).**

**Wichtig:** Den BWL-Vorteil bekommst du erst, wenn du **einen Fokus** wählst. Ein Profil, das „von allem ein bisschen" kann, verliert gegen jemanden mit klarer Spezialisierung (Finance, Marketing, Data Analytics …).

## Karrierewege: auf einen Blick
| Karriereweg | Typische Arbeitgeber | Was du für den Einstieg brauchst | Deutsch-Realität |
|---|---|---|---|
| Consulting | McKinsey, BCG, Roland Berger | Starke Noten + Case, Praktikum | Oft C1 von Vorteil/Pflicht; einige englische Projekte |
| Finanzen / Banken | Frankfurter Banken, DAX | Finance-Fokus, Praktikum | Meist Deutsch; englische Rollen existieren |
| Big 4 | PwC, Deloitte, EY, KPMG | Interesse an Rechnungswesen/Steuern | In der Prüfung meist Deutsch Pflicht |
| Konzern (DAX) | Siemens, SAP, Bosch, Allianz | Trainee/Praktikum, Fokusgebiet | In den meisten Konzernjobs Deutsch |
| Marketing / Digital | Agenturen, E-Commerce, Start-up | Portfolio, Praktikum, Tool-Kenntnisse | Im Start-up ist Englisch verbreiteter |

*(Typisches Bild Stand 2025/2026; variiert je nach Arbeitgeber und Rolle – bitte prüfen.)*

## Rollen & Einstiegspositionen
Als frische Absolventin oder frischer Absolvent sind das deine typischen Einstiegspunkte:

- **Trainee-Programme:** Der klassische deutsche Karrierestart. DAX-Konzerne und Banken bieten strukturierte **12- bis 24-monatige** Rotationsprogramme; du durchläufst mehrere Abteilungen und wechselst danach in eine feste Rolle. Für internationale Absolventen eine **sehr wertvolle** Tür.
- **Big-4-Einstieg (Associate/Consultant):** PwC/Deloitte/EY/KPMG öffnen regelmäßig viele Einstiegsstellen; steile Lernkurve, viel Netzwerk.
- **Junior Controller / Analyst / Consultant:** klassische erste Titel in Konzern und Beratung.
- **Werkstudent → Festanstellung:** Als Werkstudent in einem Unternehmen zu arbeiten, ist einer der stärksten Wege, der beim Abschluss **direkt in einen Job** mündet.

**Fette Wahrheit:** In Deutschland zählt **Praktikums- und Werkstudentenerfahrung** nach den Noten am meisten. Nur mit dem Diplom und ohne Erfahrung zu starten, ist schwer.

## Nach dem Abschluss: 18 Monate Jobsuche → Arbeitserlaubnis
**Nicht-EU-Absolventen** einer deutschen Hochschule können eine Aufenthaltserlaubnis von **bis zu 18 Monaten** zur Jobsuche erhalten *(Stand 2025/2026; bitte prüfen)*. In dieser Zeit darfst du **uneingeschränkt arbeiten**, und sobald du einen passenden Job findest, wechselst du in eine **Arbeitserlaubnis / Blaue Karte.**

- BWL gilt nicht immer als „Mangelberuf"; deshalb kann für die Blaue Karte oft die **allgemeine Gehaltsschwelle** gelten *(2025 ca. 48.300 €/Jahr; variiert je nach Rolle und Jahr – bitte prüfen)*.
- Diese 18 Monate sind **kein Geschenk, sondern ein Countdown:** fang früh an, bewirb dich schon vor dem Abschluss.

Zur Visums-/Aufenthaltsmechanik: [Master oder Job-Seeker-Visum in Deutschland?](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de) und [Arbeitsvisum mit Jobangebot – Prozess](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).

## Deutsch + Netzwerk – die Realität (in BWL entscheidend)
Das ist der größte Unterschied zu Tech/IT. Während in der Softwareentwicklung Englisch oft reicht, läuft die **Unternehmens-/Businesswelt im Alltag auf Deutsch.**

- In **Consulting und Finance** gibt es einige englische Rollen; interne Kommunikation, Kunden und Beförderung verlangen aber meist **Deutsch.**
- Ziel: bis zum Abschluss **B2, besser C1.** Auch wenn du auf Englisch startest – zieh Deutsch parallel durch.
- **Netzwerk ist in BWL vielleicht der kritischste Faktor:** Ein großer Teil der Jobs wird über Praktikum/Werkstudent, Karrieremessen und LinkedIn vergeben, oft ohne öffentliche Ausschreibung. Ein deutsches LinkedIn-Profil und ein Alumni-Netzwerk machen den Unterschied.

## Herausforderungen für internationale Studierende + Strategie
**Herausforderungen (ehrlich):** Fehlendes Deutsch ist die größte Hürde; BWL ist sehr beliebt → starke Konkurrenz; das „generalistische" Profil ohne Erfahrung sticht nicht heraus; bei manchen Arbeitgebern Zögern bei Visum/Sprache.

**Strategie:**
1. **Wähle früh einen Fokus** (Finance, Controlling, Marketing, Data Analytics) – bleib kein Generalist.
2. **Mach Deutsch zum Zentrum deiner Karriere** – B2/C1.
3. **Arbeite während des Studiums:** Werkstudent + mindestens ein starkes Praktikum. Erfahrung ist nach dem Diplom die wichtigste Karte.
4. **Bau ein Netzwerk auf:** Karrieremessen, LinkedIn (DE), Alumni, Professoren.
5. **Bewirb dich breit auf Trainee-Programme und Big 4** – das sind die offensten Türen für internationale Absolventen.
6. **Nutze die 18 Monate Jobsuche früh** – bewirb dich schon vor dem Abschluss.

## Fazit & ehrlicher Rat
Ein BWL-Abschluss ist in Deutschland ein **starker, flexibler** Start – aber das Diplom allein bringt keinen Job. Die Gewinnerformel ist klar: **ein Fokus + Deutsch (B2/C1) + echte Erfahrung (Werkstudent/Praktikum) + Netzwerk.** Trainee-Programme und Big 4 sind deine zuverlässigsten Einstiegstüren; die 18 Monate Jobsuche sind ein bequemer Puffer. Schiebst du Deutsch auf, bleiben die meisten Unternehmenstüren zu – das ist kein Rat, sondern Marktrealität.

Cluster-Artikel: [Englischsprachige Business-Master ohne Deutsch](/de/blog/english-taught-business-management-masters-in-germany-without-german-de) · [Öffentlich oder privat: Mannheim, WHU, Frankfurt School](/de/blog/public-vs-private-business-schools-in-germany-mannheim-whu-frankfurt-de) · [Arbeiten in Consulting & Finance: Gehalt, Blaue Karte](/de/blog/working-in-business-consulting-finance-in-germany-blue-card-salary-de).

---
*Dieser Inhalt ist ein allgemeiner Leitfaden und basiert auf dem Stand Anfang 2026. Gehälter, Visa-Schwellen, Fristen zur Jobsuche und Arbeitgeberbedingungen ändern sich je nach Jahr und Rolle – prüfe vor der Bewerbung offizielle Quellen (Hochschule, Ausländerbehörde, Arbeitgeber).*
MD;

        $enBody = <<<'MD'
You have a business/BWL degree (Betriebswirtschaftslehre) – or you soon will. So what can you **actually do** with it in Germany? The good news: unlike engineering, BWL doesn't lock you into a single profession – it is **versatile.** The honest news: that's exactly why competition is fierce, and in most corporate jobs **German and your network** decide the outcome. This article explains the job market and realistic career paths.

## BWL = versatile: where does it take you?
Business is a "generalist" degree; it doesn't force a specialisation on you – it **opens doors.** A business/BWL graduate in Germany typically spreads across:

- **Consulting:** strategy and management consulting – one of the most prestigious and best-paid entry routes.
- **Finance & banking:** Frankfurt is Germany's financial hub; investment banking, corporate finance, risk.
- **Corporates (DAX companies):** controlling, strategy, procurement at giants like Siemens, SAP, BMW, Bosch, Allianz.
- **Big 4 (audit/tax/advisory):** PwC, Deloitte, EY, KPMG – a **large, regular** entry gate for international graduates.
- **Marketing & sales, HR, logistics/supply chain, entrepreneurship (start-ups).**

**Important:** you only unlock the BWL advantage if you pick **a focus.** A "knows a bit of everything" profile loses to a candidate with a clear specialisation (finance, marketing, data analytics …).

## Career paths at a glance
| Career path | Typical employers | What you need to get in | The German reality |
|---|---|---|---|
| Consulting | McKinsey, BCG, Roland Berger | Strong grades + case, internship | C1 often an edge/requirement; some English projects |
| Finance / banking | Frankfurt banks, DAX firms | Finance focus, internship | Mostly German; English roles exist |
| Big 4 | PwC, Deloitte, EY, KPMG | Interest in accounting/tax | In audit, German usually required |
| Corporate (DAX) | Siemens, SAP, Bosch, Allianz | Trainee/internship, focus area | German in most corporate jobs |
| Marketing / digital | Agencies, e-commerce, start-ups | Portfolio, internship, tool skills | English more common in start-ups |

*(Typical picture as of 2025/2026; varies by employer and role – verify.)*

## Roles & entry positions
As a fresh graduate, these are your typical entry points:

- **Trainee programmes (Trainee-Programme):** the classic German career start. DAX corporates and banks offer structured **12–24-month** rotation programmes; you cycle through several departments and then move into a permanent role. A **very valuable** door for international graduates.
- **Big 4 entry (Associate/Consultant):** PwC/Deloitte/EY/KPMG open many entry roles regularly; steep learning curve, lots of networking.
- **Junior Controller / Analyst / Consultant:** classic first titles in corporates and consulting.
- **Werkstudent → full-time:** working as a Werkstudent (working student) at a company is one of the strongest routes that converts **directly into a job** at graduation.

**Bold truth:** in Germany, **internship (Praktikum) and Werkstudent experience** matters most after grades. Starting with "just a degree" and no experience is hard.

## After graduation: 18 months to find a job → work permit
**Non-EU graduates** of a German university can obtain a residence permit of **up to 18 months** to look for a job *(as of 2025/2026; verify)*. During this time you may **work without restriction**, and once you find a suitable job you switch to a **work permit / EU Blue Card.**

- Business isn't always classed as a "shortage occupation," so the **general salary threshold** often applies for the Blue Card *(around €48,300/year in 2025; varies by role and year – verify)*.
- These 18 months are **not a gift but a countdown:** start early, apply before you graduate.

For the visa/permit mechanics: [Master's vs job-seeker visa in Germany](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en) and [work visa with a job offer – the process](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).

## German + network – the reality (critical in BWL)
This is the biggest difference from tech/IT. While English often suffices in software, the **corporate/business world runs on German day to day.**

- **Consulting and finance** have some English roles; but internal communication, clients and promotion usually demand **German.**
- Target: **B2, ideally C1** by graduation. Even if you start in English, push German in parallel.
- **Networking may be the single most critical factor in BWL:** a large share of jobs is filled through internships/Werkstudent roles, career fairs and LinkedIn, often without a public posting. A German LinkedIn profile and an alumni network make the difference.

## Challenges for international students + strategy
**Challenges (honestly):** lack of German is the biggest hurdle; business is very popular → intense competition; the "generalist" profile without experience doesn't stand out; some employers hesitate over visa/language.

**Strategy:**
1. **Pick a focus early** (finance, controlling, marketing, data analytics) – don't stay a generalist.
2. **Put German at the centre of your career** – B2/C1.
3. **Work while you study:** Werkstudent + at least one strong Praktikum. Experience is the most important card after your degree.
4. **Build a network:** career fairs, LinkedIn (DE), alumni, professors.
5. **Apply broadly to trainee programmes and Big 4** – these are the most open doors for international graduates.
6. **Use the 18-month job-search window early** – start applying before you graduate.

## Conclusion & honest advice
A business/BWL degree is a **strong, flexible** start in Germany – but the degree alone won't land a job. The winning formula is clear: **a focus + German (B2/C1) + real experience (Werkstudent/Praktikum) + a network.** Trainee programmes and Big 4 are your most reliable entry doors; the 18-month job search is a comfortable buffer. Delay German and most corporate doors stay shut – that isn't advice, it's market reality.

Cluster articles: [English-taught business master's without German](/en/blog/english-taught-business-management-masters-in-germany-without-german-en) · [Public vs private: Mannheim, WHU, Frankfurt School](/en/blog/public-vs-private-business-schools-in-germany-mannheim-whu-frankfurt-en) · [Working in consulting & finance: salary, Blue Card](/en/blog/working-in-business-consulting-finance-in-germany-blue-card-salary-en).

---
*This content is a general guide based on information from early 2026. Salaries, visa thresholds, job-search permit durations and employer conditions change by year and role – verify with official sources (your university, the Ausländerbehörde, employers) before applying.*
MD;

        $variants = [
            'tr' => ['slug'=>'what-to-do-with-a-business-bwl-degree-in-germany-job-market',    'title'=>'Almanya\'da İşletme/BWL Diplomasıyla Ne Yapılır? İş Piyasası & Kariyer (2026)', 'excerpt'=>'İşletme/BWL diploması Almanya\'da çok yönlüdür: danışmanlık, finans (Frankfurt), Big 4, kurumsal DAX, pazarlama, girişimcilik. Trainee-Programme ve Big 4 en açık giriş kapıları; mezuniyet sonrası ~18 ay iş-arama izni → çalışma izni/Blue Card. Ama dürüst gerçek: kurumsal işte Almanca (B2/C1) ile network belirleyici. Odak + tecrübe (Werkstudent/Praktikum) şart.', 'meta_title'=>'İşletme/BWL Diplomasıyla Almanya\'da Ne Yapılır? (2026)', 'meta_description'=>'BWL diplomasıyla Almanya iş piyasası: danışmanlık, finans, Big 4, kurumsal, Trainee-Programme, 18 ay iş-arama izni. Almanca+network kritik. Dürüst rehber.', 'body'=>$trBody],
            'de' => ['slug'=>'what-to-do-with-a-business-bwl-degree-in-germany-job-market-de', 'title'=>'Was macht man mit einem BWL-Abschluss in Deutschland? Arbeitsmarkt & Karriere (2026)', 'excerpt'=>'Ein BWL-Abschluss ist in Deutschland vielseitig: Consulting, Finanzen (Frankfurt), Big 4, DAX-Konzerne, Marketing, Gründung. Trainee-Programme und Big 4 sind die offensten Einstiegstüren; nach dem Abschluss bis zu 18 Monate Jobsuche → Arbeitserlaubnis/Blaue Karte. Die ehrliche Wahrheit: im Unternehmen entscheiden Deutsch (B2/C1) und Netzwerk. Fokus + Erfahrung (Werkstudent/Praktikum) sind Pflicht.', 'meta_title'=>'Was macht man mit einem BWL-Abschluss in Deutschland? (2026)', 'meta_description'=>'BWL-Arbeitsmarkt in Deutschland: Consulting, Finanzen, Big 4, Konzerne, Trainee-Programme, 18 Monate Jobsuche. Deutsch + Netzwerk entscheiden. Ehrlicher Leitfaden.', 'body'=>$deBody],
            'en' => ['slug'=>'what-to-do-with-a-business-bwl-degree-in-germany-job-market-en', 'title'=>'What Can You Do With a Business/BWL Degree in Germany? Job Market & Careers (2026)', 'excerpt'=>'A business/BWL degree is versatile in Germany: consulting, finance (Frankfurt), Big 4, DAX corporates, marketing, entrepreneurship. Trainee programmes and Big 4 are the most open entry doors; after graduation up to 18 months to find a job → work permit/Blue Card. The honest truth: in corporate jobs, German (B2/C1) and your network decide. A focus + experience (Werkstudent/Praktikum) are essential.', 'meta_title'=>'What to Do With a Business/BWL Degree in Germany? (2026)', 'meta_description'=>'BWL job market in Germany: consulting, finance, Big 4, corporates, trainee programmes, 18-month job search. German + network are decisive. An honest guide.', 'body'=>$enBody],
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
            'what-to-do-with-a-business-bwl-degree-in-germany-job-market',
            'what-to-do-with-a-business-bwl-degree-in-germany-job-market-de',
            'what-to-do-with-a-business-bwl-degree-in-germany-job-market-en',
        ])->delete();
    }
};

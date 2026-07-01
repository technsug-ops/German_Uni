<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da işletme/danışmanlık/finans'ta çalışmak — maaş, Blue Card, pazar (2026).
 * Doğrulandı: BWL mezunları için sektörler (danışmanlık/finans/Big 4/kurumsal DAX), giriş maaşı ~45-55k€
 * (danışmanlık/finans üstü), Blue Card genel eşiği ~48.300€ 2025 (yıllık değişir), Almanca kurumsal işlerde şart/avantaj.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'b3a30000-3333-4baa-9f30-aa01bb02ee03';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da işletme (BWL) okumanın asıl sınavı diploma değil; mezuniyet sonrası iş piyasasına girebilmek. Danışmanlık, finans ve kurumsal dünya uluslararası yetenek arıyor — ama oyunun kuralları tech'ten farklı. Bu yazıda sektörleri, rolleri, **gerçekçi maaşları** ve en çok göz ardı edilen gerçeği (Almanca'nın rolü) dürüstçe anlatıyorum.

## Sektörler: danışmanlık, finans, Big 4, kurumsal DAX

İşletme mezunu için Almanya'da dört büyük kapı var:

- **Danışmanlık (Consulting):** McKinsey, BCG, Roland Berger, Bain gibi strateji firmaları + teknoloji/operasyon danışmanlığı. Yüksek maaş, yüksek tempo, güçlü kariyer ivmesi.
- **Finans & bankacılık:** **Frankfurt** Almanya'nın finans merkezi (Avrupa Merkez Bankası, Deutsche Bank, Commerzbank burada). Yatırım bankacılığı, varlık yönetimi, kurumsal finans.
- **Big 4 (denetim/vergi/danışmanlık):** PwC, Deloitte, EY, KPMG. Uluslararası mezunlar için **en erişilebilir giriş kapılarından biri** — çok sayıda giriş pozisyonu ve yapılandırılmış programlar.
- **Kurumsal (DAX şirketleri):** Siemens, SAP, BMW, Volkswagen, Bosch gibi büyük şirketlerde controlling, pazarlama, satın alma, İK, strateji.

## Roller: hangi işi yaparsın?

BWL diploması tek bir mesleğe değil, bir yelpazeye açılır:

| Rol | Ne yapar | Nerede |
|---|---|---|
| Consultant | Strateji/operasyon problemleri çözer | McKinsey, BCG, Big 4 |
| Controlling / Finance | Bütçe, raporlama, analiz | DAX kurumsal, orta ölçek |
| Marketing / Brand | Kampanya, ürün, pazar | Kurumsal, ajans, e-ticaret |
| Investment / Corporate Finance | Yatırım, M&A, sermaye | Frankfurt bankaları |
| HR / People | İşe alım, organizasyon | Her sektör |
| Trainee-Programme | 12-24 ay rotasyonlu giriş | Büyük şirketler |

**Trainee-Programme'ler** özellikle önemli: büyük Alman şirketleri mezunları yapılandırılmış rotasyon programlarıyla işe alır — uluslararası öğrenci için harika bir giriş yolu.

## Maaş: gerçekçi rakamlar

Rakamları abartmayalım. **2025/2026 itibarıyla, yaklaşık; bölgeye/şirkete/role göre değişir, doğrula:**

| Alan | Giriş (brüt/yıl) | Not |
|---|---|---|
| Kurumsal giriş / controlling | **~45.000–55.000€** | Standart giriş bandı |
| Big 4 (denetim/danışmanlık) | ~48.000–58.000€ | Yapılandırılmış artış |
| Strateji danışmanlığı (McKinsey/BCG) | ~70.000–90.000€+ | En yüksek, ama en rekabetçi |
| Finans / yatırım bankacılığı | ~55.000–75.000€+ | Frankfurt, bonus dahil değişken |

**Danışmanlık ve finans üst banttadır**, ama giriş de en zorudur. Ortalama kurumsal giriş **~45–55k€** civarındadır. Münih/Frankfurt gibi pahalı şehirlerde maaş yüksek ama yaşam maliyeti de yüksektir — net rakama bak.

## Almanca gerçeği: tech'in aksine, çoğu zaman şart

Bu yazının en dürüst bölümü. Bilişim/tech'te İngilizce çoğu zaman yeter; **işletme/kurumsal dünyada durum farklı.** Controlling, İK, satış, pazarlama, danışmanlığın büyük kısmı **Alman müşteriyle ve Alman ekiple** yürür — yani **Almanca ciddi bir avantaj, çoğu rolde fiilen şart.**

- Danışmanlık ve finansta **bazı İngilizce roller** vardır (uluslararası projeler, yatırım bankacılığı), ama bunlar rekabetçi ve azınlıktadır.
- **Staj (Praktikum) ve Werkstudent** pozisyonları çoğunlukla Almanca ister.
- Gerçekçi hedef: mezuniyete kadar **en az B2, ideal C1**. Almanca'sız işletme yolu teoride mümkün, pratikte çok dar.

Bunu erken kabul et: Almanca'ya yatırım, işletme kariyerinde diplomadan sonra en yüksek getirili adımdır.

## Blue Card ve çalışma vizesi

AB dışından geliyorsan **Blue Card** ana yoldur. İşletme her zaman MINT/darboğaz meslek sayılmaz — bu yüzden çoğu işletme rolünde **genel maaş eşiği** geçerli olur.

- **Genel Blue Card eşiği: ~48.300€/yıl (2025 itibarıyla, yaklaşık; yıllık güncellenir, doğrula).** Danışmanlık/finans giriş maaşları bu eşiği genelde geçer.
- Darboğaz mesleklerde daha düşük eşik olabilir, ama tipik işletme rolleri buna girmez — role göre değişir, doğrula.
- İş teklifi + eşiği geçen maaş + tanınan diploma → Blue Card. Süreç ve zaman çizelgesi için: [İş teklifiyle çalışma vizesi süreci](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track).

Kıyas için tech tarafı çoğunlukla darboğaz/MINT avantajından yararlanır: [Almanya'da IT/tech'te yabancı olarak çalışmak — Blue Card & maaş](/tr/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary).

## İş arama: LinkedIn, network, Praktikum

Almanya'da işletme işi bulmak **ilan başvurusundan çok network işidir.**

- **LinkedIn (DE) ve Xing:** Alman piyasasının ilan ve ağ merkezi.
- **Praktikum / Werkstudent:** Okurken staj yapmak, mezuniyette tam zamana geçmenin en güçlü yoludur — çoğu giriş pozisyonu buradan çıkar.
- **Üniversite kariyer fuarları** (özellikle Mannheim, Frankfurt School gibi güçlü sanayi bağlı okullar) doğrudan işverenle buluşturur.
- **Trainee-Programme başvuruları** genelde mezuniyetten aylar önce açılır — takvimi kaçırma.
- Yüksek lisans mı, iş arama vizesi mi rotası daha mantıklı: [Master mı, iş arama vizesi mi?](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career).

Devamı için küme yazıları: [Almancasız İngilizce işletme masterları](/tr/blog/english-taught-business-management-masters-in-germany-without-german), [kamu mu özel mi işletme okulu?](/tr/blog/public-vs-private-business-schools-in-germany-mannheim-whu-frankfurt) ve [BWL diplomasıyla ne yapılır?](/tr/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market).

## Sonuç & dürüst tavsiye

Almanya işletme mezunu için gerçek bir fırsat pazarı: danışmanlık, finans, Big 4 ve DAX kurumsal dünyası uluslararası yetenek alıyor, maaşlar makul, Blue Card yolu açık. Ama dürüst ol: **giriş maaşları ~45–55k€ bandında** (danışmanlık/finans üstü), en yüksek bantlar en rekabetçi, ve en önemlisi — **tech'in aksine Almanca çoğu rolde şart.** Erken staj yap, network kur, Almanca'yı C1'e taşı. Bu üçünü yaparsan işletme diploması Almanya'da güçlü bir kariyere dönüşür; yapmazsan İngilizce-only bir cepte sıkışırsın.

*Not: Bu yazı 2026 başı itibarıyla hazırlanmıştır. Maaş bantları, Blue Card eşikleri ve vize kuralları düzenli değişir — başvurmadan önce resmi kaynaklardan (Make it in Germany, ilgili şirket, yabancılar dairesi) güncel bilgiyi doğrula.*
MD;

        $deBody = <<<'MD'
Die eigentliche Prüfung eines BWL-Studiums in Deutschland ist nicht der Abschluss, sondern der Einstieg in den Arbeitsmarkt danach. Beratung, Finanzen und die Konzernwelt suchen internationale Talente — aber die Spielregeln sind andere als in der Tech-Branche. In diesem Artikel erkläre ich dir die Branchen, die Rollen, **realistische Gehälter** und die am häufigsten übersehene Wahrheit (die Rolle der deutschen Sprache) ehrlich.

## Branchen: Beratung, Finanzen, Big 4, DAX-Konzerne

Für BWL-Absolvent:innen gibt es in Deutschland vier große Türen:

- **Beratung (Consulting):** Strategiefirmen wie McKinsey, BCG, Roland Berger, Bain plus Technologie-/Operations-Beratung. Hohes Gehalt, hohes Tempo, starke Karrieredynamik.
- **Finanzen & Banking:** **Frankfurt** ist Deutschlands Finanzzentrum (Europäische Zentralbank, Deutsche Bank, Commerzbank). Investment Banking, Asset Management, Corporate Finance.
- **Big 4 (Wirtschaftsprüfung/Steuer/Beratung):** PwC, Deloitte, EY, KPMG. Für internationale Absolvent:innen **eine der zugänglichsten Einstiegstüren** — viele Einstiegsstellen und strukturierte Programme.
- **Konzerne (DAX-Unternehmen):** Controlling, Marketing, Einkauf, HR, Strategie bei Siemens, SAP, BMW, Volkswagen, Bosch.

## Rollen: welchen Job machst du?

Ein BWL-Abschluss öffnet nicht einen Beruf, sondern ein ganzes Spektrum:

| Rolle | Aufgabe | Wo |
|---|---|---|
| Consultant | Löst Strategie-/Operations-Probleme | McKinsey, BCG, Big 4 |
| Controlling / Finance | Budget, Reporting, Analyse | DAX-Konzern, Mittelstand |
| Marketing / Brand | Kampagne, Produkt, Markt | Konzern, Agentur, E-Commerce |
| Investment / Corporate Finance | Investment, M&A, Kapital | Frankfurter Banken |
| HR / People | Recruiting, Organisation | Jede Branche |
| Trainee-Programm | 12-24 Monate Rotationseinstieg | Große Unternehmen |

**Trainee-Programme** sind besonders wichtig: Große deutsche Unternehmen stellen Absolvent:innen über strukturierte Rotationsprogramme ein — ein hervorragender Einstiegsweg für internationale Studierende.

## Gehalt: realistische Zahlen

Übertreiben wir die Zahlen nicht. **Stand 2025/2026, ungefähr; variiert nach Region/Unternehmen/Rolle, bitte prüfen:**

| Bereich | Einstieg (brutto/Jahr) | Hinweis |
|---|---|---|
| Konzern-Einstieg / Controlling | **~45.000–55.000€** | Standard-Einstiegsband |
| Big 4 (Prüfung/Beratung) | ~48.000–58.000€ | Strukturierte Steigerung |
| Strategieberatung (McKinsey/BCG) | ~70.000–90.000€+ | Am höchsten, aber am kompetitivsten |
| Finanzen / Investment Banking | ~55.000–75.000€+ | Frankfurt, Bonus variabel |

**Beratung und Finanzen liegen im oberen Band**, aber der Einstieg dort ist auch am schwersten. Der durchschnittliche Konzern-Einstieg liegt bei **~45–55k€**. In teuren Städten wie München/Frankfurt ist das Gehalt höher, aber die Lebenshaltungskosten auch — schau auf die Netto-Zahl.

## Die Deutsch-Wahrheit: anders als in Tech oft ein Muss

Der ehrlichste Abschnitt dieses Artikels. In der Tech-Branche reicht oft Englisch; **in der BWL-/Konzernwelt ist das anders.** Controlling, HR, Vertrieb, Marketing und ein großer Teil der Beratung laufen **mit deutschen Kund:innen und deutschen Teams** — also ist **Deutsch ein starker Vorteil, in den meisten Rollen faktisch ein Muss.**

- In Beratung und Finanzen gibt es **einige englischsprachige Rollen** (internationale Projekte, Investment Banking), aber die sind kompetitiv und in der Minderheit.
- **Praktikum und Werkstellen** verlangen meist Deutsch.
- Realistisches Ziel: bis zum Abschluss **mindestens B2, idealerweise C1**. Der BWL-Weg ohne Deutsch ist theoretisch möglich, praktisch sehr eng.

Akzeptiere das früh: Die Investition ins Deutsche ist nach dem Abschluss der renditestärkste Schritt in einer BWL-Karriere.

## Blue Card und Arbeitsvisum

Wenn du von außerhalb der EU kommst, ist die **Blue Card** der Hauptweg. BWL gilt nicht immer als MINT-/Engpassberuf — deshalb greift für die meisten BWL-Rollen die **allgemeine Gehaltsschwelle**.

- **Allgemeine Blue-Card-Schwelle: ~48.300€/Jahr (Stand 2025, ungefähr; wird jährlich aktualisiert, bitte prüfen).** Einstiegsgehälter in Beratung/Finanzen überschreiten diese Schwelle in der Regel.
- Für Engpassberufe kann die Schwelle niedriger sein, aber typische BWL-Rollen fallen nicht darunter — variiert nach Rolle, bitte prüfen.
- Jobangebot + Gehalt über der Schwelle + anerkannter Abschluss → Blue Card. Zum Prozess und Zeitplan: [Arbeitsvisum mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).

Zum Vergleich profitiert die Tech-Seite meist vom Engpass-/MINT-Vorteil: [In IT/Tech in Deutschland als Ausländer:in arbeiten — Blue Card & Gehalt](/de/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-de).

## Jobsuche: LinkedIn, Netzwerk, Praktikum

Einen BWL-Job in Deutschland zu finden ist **mehr Netzwerkarbeit als Bewerbung auf Ausschreibungen.**

- **LinkedIn (DE) und Xing:** Zentrale für Ausschreibungen und Netzwerk im deutschen Markt.
- **Praktikum / Werkstudent:** Ein Praktikum während des Studiums ist der stärkste Weg zur Vollzeitstelle nach dem Abschluss — die meisten Einstiegsstellen entstehen so.
- **Karrieremessen der Universitäten** (besonders bei industrienahen Schulen wie Mannheim, Frankfurt School) bringen dich direkt zu Arbeitgebern.
- **Bewerbungen für Trainee-Programme** öffnen meist Monate vor dem Abschluss — verpasse den Zeitplan nicht.
- Welche Route sinnvoller ist — Master oder Job-Seeker-Visum: [Master oder Job-Seeker-Visum?](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).

Weiterlesen in der Serie: [Englischsprachige BWL-Master ohne Deutsch](/de/blog/english-taught-business-management-masters-in-germany-without-german-de), [öffentliche oder private Business School?](/de/blog/public-vs-private-business-schools-in-germany-mannheim-whu-frankfurt-de) und [Was mache ich mit einem BWL-Abschluss?](/de/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market-de).

## Fazit & ehrlicher Rat

Für BWL-Absolvent:innen ist Deutschland ein echter Chancenmarkt: Beratung, Finanzen, Big 4 und die DAX-Konzernwelt nehmen internationale Talente auf, die Gehälter sind solide, der Blue-Card-Weg ist offen. Aber sei ehrlich: **Einstiegsgehälter liegen bei ~45–55k€** (Beratung/Finanzen darüber), die höchsten Bänder sind am kompetitivsten, und am wichtigsten — **anders als in Tech ist Deutsch in den meisten Rollen ein Muss.** Mach früh ein Praktikum, baue ein Netzwerk auf, bring dein Deutsch auf C1. Wenn du diese drei Dinge tust, wird der BWL-Abschluss in Deutschland zu einer starken Karriere; wenn nicht, steckst du in einer reinen Englisch-Nische fest.

*Hinweis: Dieser Artikel entstand Anfang 2026. Gehaltsbänder, Blue-Card-Schwellen und Visaregeln ändern sich regelmäßig — prüfe vor der Bewerbung aktuelle Informationen aus offiziellen Quellen (Make it in Germany, das jeweilige Unternehmen, die Ausländerbehörde).*
MD;

        $enBody = <<<'MD'
The real test of a business (BWL) degree in Germany is not the diploma — it's breaking into the job market afterward. Consulting, finance and the corporate world want international talent, but the rules of the game differ from tech. In this article I honestly walk you through the sectors, the roles, **realistic salaries**, and the most overlooked truth: the role of the German language.

## Sectors: consulting, finance, Big 4, DAX corporates

For a business graduate, Germany has four big doors:

- **Consulting:** strategy firms like McKinsey, BCG, Roland Berger, Bain, plus technology/operations consulting. High pay, high pace, strong career momentum.
- **Finance & banking:** **Frankfurt** is Germany's financial center (European Central Bank, Deutsche Bank, Commerzbank). Investment banking, asset management, corporate finance.
- **Big 4 (audit/tax/advisory):** PwC, Deloitte, EY, KPMG. For international graduates, **one of the most accessible entry doors** — plenty of entry-level roles and structured programs.
- **Corporate (DAX companies):** controlling, marketing, procurement, HR, strategy at Siemens, SAP, BMW, Volkswagen, Bosch.

## Roles: which job will you do?

A BWL degree opens not one profession but a whole spectrum:

| Role | What it does | Where |
|---|---|---|
| Consultant | Solves strategy/operations problems | McKinsey, BCG, Big 4 |
| Controlling / Finance | Budget, reporting, analysis | DAX corporate, mid-size |
| Marketing / Brand | Campaign, product, market | Corporate, agency, e-commerce |
| Investment / Corporate Finance | Investment, M&A, capital | Frankfurt banks |
| HR / People | Recruiting, organization | Every sector |
| Trainee program | 12-24 month rotational entry | Large companies |

**Trainee programs** matter especially: large German companies hire graduates through structured rotation programs — an excellent entry route for international students.

## Salary: realistic numbers

Let's not inflate the figures. **As of 2025/2026, approximate; varies by region/company/role, verify:**

| Field | Entry (gross/year) | Note |
|---|---|---|
| Corporate entry / controlling | **~€45,000–55,000** | Standard entry band |
| Big 4 (audit/advisory) | ~€48,000–58,000 | Structured progression |
| Strategy consulting (McKinsey/BCG) | ~€70,000–90,000+ | Highest, but most competitive |
| Finance / investment banking | ~€55,000–75,000+ | Frankfurt, bonus variable |

**Consulting and finance sit at the top band**, but entry there is also the hardest. The average corporate entry is around **~€45–55k**. In expensive cities like Munich/Frankfurt salaries are higher but so is the cost of living — look at the net figure.

## The German-language truth: unlike tech, often a must

The most honest section of this article. In tech, English is often enough; **in the business/corporate world it's different.** Controlling, HR, sales, marketing and much of consulting run **with German clients and German teams** — so **German is a strong advantage, in most roles effectively a must.**

- Consulting and finance have **some English-language roles** (international projects, investment banking), but they are competitive and in the minority.
- **Internships (Praktikum) and Werkstudent** positions mostly require German.
- Realistic target: **at least B2, ideally C1** by graduation. The business path without German is theoretically possible but practically very narrow.

Accept this early: investing in German is the highest-return step in a business career after the degree itself.

## Blue Card and work visa

If you come from outside the EU, the **Blue Card** is the main route. Business isn't always classified as a STEM/shortage occupation — so for most business roles the **general salary threshold** applies.

- **General Blue Card threshold: ~€48,300/year (as of 2025, approximate; updated annually, verify).** Entry salaries in consulting/finance usually clear this threshold.
- Shortage occupations may have a lower threshold, but typical business roles don't fall under it — varies by role, verify.
- Job offer + salary above the threshold + recognized degree → Blue Card. For the process and timeline: [Work visa with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).

By comparison, the tech side usually benefits from the shortage/STEM advantage: [Working in IT/tech in Germany as a foreigner — Blue Card & salary](/en/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-en).

## Job search: LinkedIn, network, Praktikum

Finding a business job in Germany is **more networking than applying to listings.**

- **LinkedIn (DE) and Xing:** the hub for listings and networking in the German market.
- **Praktikum / Werkstudent:** an internship during your studies is the strongest path to a full-time offer at graduation — most entry roles come from here.
- **University career fairs** (especially at industry-connected schools like Mannheim, Frankfurt School) put you directly in front of employers.
- **Trainee program applications** usually open months before graduation — don't miss the timeline.
- Which route makes more sense — master's or job-seeker visa: [Master's or job-seeker visa?](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

Read on in the series: [English-taught business master's without German](/en/blog/english-taught-business-management-masters-in-germany-without-german-en), [public or private business school?](/en/blog/public-vs-private-business-schools-in-germany-mannheim-whu-frankfurt-en) and [What to do with a business/BWL degree?](/en/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market-en).

## Conclusion & honest advice

For a business graduate, Germany is a real opportunity market: consulting, finance, Big 4 and the DAX corporate world take on international talent, salaries are solid, the Blue Card route is open. But be honest: **entry salaries sit around ~€45–55k** (consulting/finance above), the top bands are the most competitive, and most importantly — **unlike tech, German is a must in most roles.** Do an internship early, build a network, get your German to C1. Do these three things and a business degree turns into a strong career in Germany; skip them and you get stuck in an English-only niche.

*Note: This article was prepared in early 2026. Salary bands, Blue Card thresholds and visa rules change regularly — before applying, verify current information from official sources (Make it in Germany, the relevant company, the immigration office).*
MD;

        $variants = [
            'tr' => ['slug'=>'working-in-business-consulting-finance-in-germany-blue-card-salary',    'title'=>"Almanya'da İşletme, Danışmanlık ve Finans'ta Çalışmak: Maaş, Blue Card, Pazar (2026)", 'excerpt'=>"Almanya'da işletme/BWL mezunu olarak danışmanlık, finans, Big 4 ve kurumsal DAX dünyasında çalışmak: sektörler, roller, gerçekçi giriş maaşları (~45-55k€), Blue Card eşiği ve Almanca'nın çoğu rolde neden şart olduğu — dürüst bir rehber.", 'meta_title'=>"Almanya'da İşletme & Finans'ta Çalışmak: Maaş & Blue Card 2026", 'meta_description'=>"Almanya işletme/danışmanlık/finans kariyeri: sektörler, roller, giriş maaşı ~45-55k€, Blue Card eşiği ~48.3k€ (2025), Almanca gerçeği. Dürüst rehber.", 'body'=>$trBody],
            'de' => ['slug'=>'working-in-business-consulting-finance-in-germany-blue-card-salary-de', 'title'=>"In BWL, Beratung und Finanzen in Deutschland arbeiten: Gehalt, Blue Card, Markt (2026)", 'excerpt'=>"Als BWL-Absolvent:in in Deutschland in Beratung, Finanzen, Big 4 und der DAX-Konzernwelt arbeiten: Branchen, Rollen, realistische Einstiegsgehälter (~45-55k€), Blue-Card-Schwelle und warum Deutsch in den meisten Rollen ein Muss ist — ein ehrlicher Leitfaden.", 'meta_title'=>"In BWL & Finanzen in Deutschland arbeiten: Gehalt & Blue Card 2026", 'meta_description'=>"Karriere in BWL/Beratung/Finanzen in Deutschland: Branchen, Rollen, Einstiegsgehalt ~45-55k€, Blue-Card-Schwelle ~48,3k€ (2025), Deutsch-Wahrheit. Ehrlicher Leitfaden.", 'body'=>$deBody],
            'en' => ['slug'=>'working-in-business-consulting-finance-in-germany-blue-card-salary-en', 'title'=>"Working in Business, Consulting and Finance in Germany: Salary, Blue Card, Market (2026)", 'excerpt'=>"Working in consulting, finance, Big 4 and the DAX corporate world as a business/BWL graduate in Germany: sectors, roles, realistic entry salaries (~€45-55k), the Blue Card threshold and why German is a must in most roles — an honest guide.", 'meta_title'=>"Working in Business & Finance in Germany: Salary & Blue Card 2026", 'meta_description'=>"A business/consulting/finance career in Germany: sectors, roles, entry salary ~€45-55k, Blue Card threshold ~€48.3k (2025), the German-language truth. Honest guide.", 'body'=>$enBody],
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
            'working-in-business-consulting-finance-in-germany-blue-card-salary',
            'working-in-business-consulting-finance-in-germany-blue-card-salary-de',
            'working-in-business-consulting-finance-in-germany-blue-card-salary-en',
        ])->delete();
    }
};

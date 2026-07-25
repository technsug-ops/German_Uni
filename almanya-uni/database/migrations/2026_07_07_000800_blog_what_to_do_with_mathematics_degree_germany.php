<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da matematik diplomasıyla ne yapılır? İş piyasası & kariyer (2026).
 * Doğrulandı: Matematik güçlü generalist diploma; istihdam uzmanlaşmayla (finans/aktüerya/veri) gelir.
 * Mezuniyet sonrası 18 ay iş-arama oturumu. Sayılar 2025/2026 itibarıyla yaklaşık, doğrulanmalı.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'b2c40000-4444-4eae-9ff0-bb09cc0eee04';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Matematik diploması aldın (ya da almak üzeresin) ve klasik soruyla karşı karşıyasın: "Peki şimdi ne yapacağım?" İyi haber şu ki, Almanya'da matematik mezunlarının iş piyasası düşündüğünden çok daha geniş. Kötü haber ise şu: diploma tek başına seni bir işe koymaz — seni işe koyan şey, matematiği bir **alana** bağlaman. Bu yazıda matematik diplomasının nereye götürdüğünü, hangi kariyer yollarının açık olduğunu ve uluslararası bir öğrenci olarak Almanya'da gerçekçi rotanı dürüstçe ele alıyoruz.

## Generalist güç: matematik diploması seni nereye götürür?

Matematik, üniversitedeki en soyut ve zorlu bölümlerden biri. Ama tam da bu yüzden işverenler onu bir **düşünme sertifikası** gibi görür: karmaşık problemi parçalara ayırabilen, kanıt/mantık disiplinine sahip, sayısal modelleri anlayan biri. Bu yüzden matematik mezunu tek bir mesleğe hapsolmaz — finanstan sigortaya, yazılımdan danışmanlığa çok geniş bir yelpazeye açılabilir.

Ama burada tuzak var: "her yere gidebilirsin" cümlesi, "hiçbir yere hazır değilsin" anlamına da gelebilir. İşverenler matematiksel zekâna saygı duyar, fakat seni işe alırken somut bir **rol** için alır: quant, aktüer, veri analisti, yazılımcı. Yani generalist güç bir başlangıç sermayesidir; onu bir uzmanlığa çevirmek senin işin.

## Kariyer yolları: matematik nereye açılır?

Aşağıdaki tablo, Almanya'da matematik/istatistik mezunlarının en sık girdiği alanları ve pratik durumlarını özetliyor (2025/2026 itibarıyla, yaklaşık; kendi araştırmanla doğrula):

| Kariyer yolu | Ne yapar | Talep | Not |
|---|---|---|---|
| Finans / quant / risk | Fiyatlama, risk modelleme, portföy | Yüksek | Frankfurt merkezli; İngilizce-dostu |
| Sigorta — **Aktüer (Aktuar)** | Risk/fiyatlandırma, DAV sertifikası | Çok yüksek | Net, iyi ödeyen kariyer; Almanca yardımcı |
| Veri bilimi / analitik | Model, tahmin, ETL, ML | Yüksek | İstatistik altyapısı büyük avantaj |
| IT / yazılım | Backend, scientific computing | Yüksek | Programlama pratiği şart |
| Danışmanlık | Kantitatif analiz, optimizasyon | Orta-yüksek | İletişim + Almanca önemli |
| Araştırma / akademi | Doktora, üniversite/enstitü | Rekabetçi | Başlangıç maaşı düşük |
| Öğretmenlik (Lehramt) | Okulda matematik | İstikrarlı | Almanca C1+ ve pedagojik formasyon şart |

En pratik ve yüksek getirili yolların **kantitatif** olanlar olduğunu göreceksin: quant, aktüerya ve veri. Bu üçü, matematik diplomasının Almanya'daki iş garantisini en somut hâle getiren rotalardır. Aktüer yolu özellikle distinkt: **DAV (Deutsche Aktuarvereinigung)** sertifikasıyla ilerleyen, talebi yüksek ve iyi ödeyen bir meslektir.

## Uzmanlaşma neden şart? Matematik → alan

Burası yazının kalbi. Almanya iş piyasasında "matematikçiyim" demek yeterli değil; "**şu alanın** matematikçisiyim" demek gerekir. İşe alım ilanları rol üzerinden yazılır: *Risk Analyst*, *Aktuar (DAV)*, *Data Scientist*, *Quantitative Developer*. Saf matematik bilgisi bu rollere kapı açar ama içeri girmek için alan bilgisi ve araçlar lazımdır.

Pratik olarak uzmanlaşma şöyle görünür:

- **Finans/quant** istiyorsan: stokastik, finansal matematik, Python/C++, türev fiyatlama.
- **Aktüerya** istiyorsan: sigorta matematiği, olasılık, DAV modülleri, Almanca sektör dili.
- **Veri** istiyorsan: istatistik, makine öğrenmesi, SQL/Python, portföy projeleri.

**Kalın gerçek:** Almanya'da matematik mezunlarının işsiz kalması nadirdir — ama işsiz kalanlar genelde hiçbir alana bağlanmamış, "saf matematik dışında bir şey yapmam" diyenlerdir. Uzmanlaşma bir ihanet değil; matematiğinin karşılığını aldığın yerdir.

Bu geçişleri daha derin planlamak istersen [matematik diplomasıyla Almanya'da çalışmak: quant, aktüer, veri, maaş](/tr/blog/working-with-a-mathematics-degree-in-germany-quant-actuary-data-salary) yazısı sektör sektör yol gösteriyor.

## Mezuniyet sonrası 18 ay: iş-arama oturumu

Uluslararası bir öğrenciysen bu bölüm senin için kritik. Almanya'da bir yüksek lisans (ya da lisans) diplomasını tamamladıktan sonra, **iş aramak için 18 aylık bir oturum izni** alabilirsin. Bu süre boyunca ülkede kalıp nitelikli bir iş arayabilir, bulduğunda çalışma iznine/Blue Card'a geçebilirsin.

Bu 18 ay altın değerinde ama sınırlı. Boşa harcamamak için:

- Mezun olmadan **önce** staj/Werkstudent deneyimi biriktir.
- CV'ni ve LinkedIn'i Almanya standardına göre hazırla; bir role odaklan.
- Aktüerya/quant/veri gibi net bir hedef belirle — dağınık başvuru zaman kaybettirir.
- Almanca'nı geliştir; birçok iş İngilizce olsa da Almanca kapı açar.

Oturum ve vize stratejisinin bütününü [Almanya'da yüksek lisans mı, iş arama vizesi mi: kariyerin iki anahtarı](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) yazısında ayrıntılı bulabilirsin.

## Almanca + strateji: ikisi birden

Dil konusunda dürüst olalım: araştırma, veri ve quant rollerinin önemli bölümü İngilizce yürür — özellikle uluslararası şirketlerde ve Frankfurt finans çevresinde. Ama sigorta (aktüerya), danışmanlık, öğretmenlik ve geniş yerel piyasa büyük ölçüde **Almanca** ister. Almanca'n B2–C1 seviyesine çıktığında iş yelpazen katlanır.

Strateji şu: dil öğrenmeyi kariyer hedefinden ayrı düşünme. Aktüer olmak istiyorsan Almanca'yı önceliklendir; uluslararası bir data/quant rolü hedefliyorsan İngilizce yeterli olabilir ama Almanca yine de avantaj. Okul seçiminde de programın gücü ve konumu önemli — [Almanya'da üniversite prestiji ve sıralamalar nasıl çalışır](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) yazısı burada işine yarar.

## Uluslararası öğrenci için gerçekçi yol

Hepsini birleştirince, Almanya'da matematik diplomasıyla sağlam bir rota şöyle çizilir:

1. Bir matematik/istatistik programına gir (giriş için [Almanya'da matematik/istatistik okumak](/tr/blog/studying-mathematics-statistics-in-germany-as-a-foreigner) yazısına bak).
2. Almancan zayıfsa İngilizce master seçeneklerini değerlendir ([Almancasız İngilizce matematik/istatistik master programları](/tr/blog/english-taught-mathematics-statistics-masters-in-germany-without-german)).
3. Bir alan seç (finans/aktüerya/veri) ve o alanın araçlarında uzmanlaş.
4. Staj/Werkstudent ile deneyim biriktir.
5. Mezuniyet sonrası 18 aylık oturumu odaklı iş aramasıyla kullan.
6. Almanca'nı paralel geliştir.

Bu adımları takip eden matematik mezunlarının Almanya'da iş bulması istisna değil, kuraldır.

## Sonuç & dürüst tavsiye

Matematik diploması, Almanya'da güçlü bir generalist temeldir — ama seni işe koyan şey diploma değil, o diplomayı bir **alana** bağlamandır. Kantitatif yollar (quant, aktüer, veri) matematik mezunları için en somut, en yüksek getirili rotalardır; saf akademi ise rekabetçi ve maaşça düşük başlar. Dürüst tavsiyem: mezun olmadan bir alan seç, o alanın araçlarında uzmanlaş, staj/Werkstudent ile CV'ni doldur ve mezuniyet sonrası 18 ay oturumunu odaklı kullan. Uzmanlaşmayı geciktirme; matematiğin gücünü bir yöne kanalize ettiğin an, iş piyasası sana açılır.

*Bu yazıdaki tüm sayılar, oturum süreleri, maaş aralıkları ve vize kuralları 2025/2026 itibarıyla yaklaşık değerlerdir ve değişebilir. Başvuru öncesi resmi kaynaklardan (üniversite, DAV, Bundesagentur für Arbeit, yabancılar dairesi) güncel bilgiyi mutlaka doğrula.*
MD;

        $deBody = <<<'MD'
Du hast einen Mathematik-Abschluss (oder bist kurz davor) und stehst vor der klassischen Frage: „Und was mache ich jetzt damit?" Die gute Nachricht: Der Arbeitsmarkt für Mathematik-Absolventen in Deutschland ist breiter, als du denkst. Die weniger gute Nachricht: Der Abschluss allein bringt dich nicht in einen Job — was dich in einen Job bringt, ist die Verbindung deiner Mathematik mit einem konkreten **Fachgebiet**. In diesem Artikel schauen wir ehrlich darauf, wohin dich ein Mathe-Abschluss führt und wie dein realistischer Weg als internationaler Studierender aussieht.

## Generalistische Stärke: Wohin führt dich der Abschluss?

Mathematik gehört zu den abstraktesten und anspruchsvollsten Studiengängen. Genau deshalb sehen Arbeitgeber ihn als eine Art **Denk-Zertifikat**: jemand, der komplexe Probleme zerlegt, logisch und beweisdiszipliniert arbeitet und quantitative Modelle versteht. Deshalb bist du als Mathe-Absolvent nicht auf einen einzigen Beruf festgelegt — von der Finanzwelt über die Versicherung bis hin zu Software und Beratung steht dir ein breites Feld offen.

Doch hier lauert eine Falle: „Du kannst überallhin" kann auch bedeuten „Du bist nirgends wirklich vorbereitet". Arbeitgeber respektieren deine mathematische Intelligenz, aber sie stellen dich für eine konkrete **Rolle** ein: Quant, Aktuar, Data Analyst, Softwareentwickler. Die generalistische Stärke ist ein Startkapital — sie in eine Spezialisierung zu verwandeln, ist deine Aufgabe.

## Karrierewege: Wohin öffnet sich Mathematik?

Die folgende Tabelle fasst die häufigsten Felder für Mathematik-/Statistik-Absolventen in Deutschland zusammen (Stand 2025/2026, ungefähr; bitte selbst prüfen):

| Karriereweg | Was man tut | Nachfrage | Hinweis |
|---|---|---|---|
| Finanzen / Quant / Risiko | Pricing, Risikomodellierung, Portfolio | Hoch | Frankfurt-lastig; englischfreundlich |
| Versicherung — **Aktuar** | Risiko/Pricing, DAV-Zertifizierung | Sehr hoch | Klarer, gut bezahlter Weg; Deutsch hilft |
| Data Science / Analytics | Modelle, Prognosen, ETL, ML | Hoch | Statistik-Basis ist ein großer Vorteil |
| IT / Software | Backend, Scientific Computing | Hoch | Programmierpraxis ist Pflicht |
| Beratung | Quantitative Analyse, Optimierung | Mittel-hoch | Kommunikation + Deutsch wichtig |
| Forschung / Wissenschaft | Promotion, Uni/Institut | Kompetitiv | Einstiegsgehalt niedrig |
| Lehramt | Mathematik an der Schule | Stabil | Deutsch C1+ und pädagogische Ausbildung nötig |

Du wirst merken: Die praktischsten und lukrativsten Wege sind die **quantitativen** — Quant, Aktuariat und Data. Diese drei machen die Beschäftigungsstärke eines Mathe-Abschlusses in Deutschland am konkretesten. Der Aktuar-Weg ist besonders eigenständig: Er läuft über die **DAV-Zertifizierung (Deutsche Aktuarvereinigung)**, ist stark nachgefragt und gut bezahlt.

## Warum Spezialisierung Pflicht ist: Mathematik → Fachgebiet

Das ist der Kern des Artikels. Auf dem deutschen Arbeitsmarkt reicht „Ich bin Mathematiker" nicht; du musst sagen „Ich bin Mathematiker **für dieses Gebiet**". Stellenausschreibungen sind über Rollen definiert: *Risk Analyst*, *Aktuar (DAV)*, *Data Scientist*, *Quantitative Developer*. Reines Mathe-Wissen öffnet die Tür zu diesen Rollen, aber hineinzukommen erfordert Fachwissen und Werkzeuge.

Praktisch sieht Spezialisierung so aus:

- **Finanzen/Quant**: Stochastik, Finanzmathematik, Python/C++, Derivate-Pricing.
- **Aktuariat**: Versicherungsmathematik, Wahrscheinlichkeit, DAV-Module, deutsche Fachsprache.
- **Data**: Statistik, Machine Learning, SQL/Python, Portfolio-Projekte.

**Harte Wahrheit:** Arbeitslose Mathematik-Absolventen sind in Deutschland selten — aber die, die es sind, haben sich meist an kein Gebiet gebunden und sagen „Ich mache nichts außer reiner Mathematik". Spezialisierung ist kein Verrat; sie ist der Ort, an dem sich deine Mathematik auszahlt.

Wenn du diese Übergänge tiefer planen willst, führt dich der Artikel [Mit einem Mathematik-Abschluss in Deutschland arbeiten: Quant, Aktuar, Data, Gehalt](/de/blog/working-with-a-mathematics-degree-in-germany-quant-actuary-data-salary-de) Branche für Branche.

## Nach dem Abschluss: 18 Monate Job-Suche

Als internationaler Studierender ist dieser Abschnitt für dich entscheidend. Nach dem Abschluss eines Studiums in Deutschland kannst du eine **Aufenthaltserlaubnis von 18 Monaten zur Arbeitssuche** erhalten. In dieser Zeit darfst du im Land bleiben und einen qualifizierten Job suchen; findest du einen, wechselst du in eine Arbeitserlaubnis / Blaue Karte.

Diese 18 Monate sind Gold wert, aber begrenzt. Um sie nicht zu verschwenden:

- Sammle **vor** dem Abschluss Praktikums-/Werkstudenten-Erfahrung.
- Bereite Lebenslauf und LinkedIn nach deutschem Standard vor; fokussiere dich auf eine Rolle.
- Setze ein klares Ziel (Aktuariat/Quant/Data) — Bewerbungen mit der Gießkanne kosten Zeit.
- Verbessere dein Deutsch; viele Jobs sind auf Englisch, aber Deutsch öffnet Türen.

Die gesamte Aufenthalts- und Visumsstrategie findest du im Artikel [Master oder Job-Seeker-Visum in Deutschland: die zwei Schlüssel deiner Karriere](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).

## Deutsch + Strategie: beides zusammen

Sei ehrlich zur Sprache: Ein großer Teil der Forschungs-, Data- und Quant-Rollen läuft auf Englisch — besonders in internationalen Firmen und im Frankfurter Finanzumfeld. Aber Versicherung (Aktuariat), Beratung, Lehramt und der breite lokale Markt verlangen weitgehend **Deutsch**. Sobald dein Deutsch auf B2–C1 steigt, vervielfacht sich dein Job-Spektrum.

Die Strategie: Denke Sprachenlernen nicht getrennt vom Karriereziel. Willst du Aktuar werden, priorisiere Deutsch; zielst du auf eine internationale Data-/Quant-Rolle, kann Englisch reichen — aber Deutsch bleibt ein Vorteil. Auch bei der Hochschulwahl zählen Stärke und Standort des Programms — der Artikel [Wie Hochschul-Prestige und Rankings in Deutschland funktionieren](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de) hilft dir hier weiter.

## Realistischer Weg für internationale Studierende

Alles zusammengenommen sieht ein solider Weg mit Mathe-Abschluss in Deutschland so aus:

1. Steige in ein Mathematik-/Statistik-Programm ein (zum Einstieg siehe [Mathematik/Statistik in Deutschland studieren](/de/blog/studying-mathematics-statistics-in-germany-as-a-foreigner-de)).
2. Ist dein Deutsch schwach, prüfe englischsprachige Master ([Englischsprachige Mathematik-/Statistik-Master ohne Deutsch](/de/blog/english-taught-mathematics-statistics-masters-in-germany-without-german-de)).
3. Wähle ein Gebiet (Finanzen/Aktuariat/Data) und spezialisiere dich in dessen Werkzeugen.
4. Sammle Erfahrung über Praktikum/Werkstudent.
5. Nutze die 18-monatige Aufenthaltserlaubnis nach dem Abschluss für eine fokussierte Job-Suche.
6. Verbessere parallel dein Deutsch.

Für Mathe-Absolventen, die diese Schritte gehen, ist ein Job in Deutschland nicht die Ausnahme, sondern die Regel.

## Fazit & ehrlicher Rat

Ein Mathematik-Abschluss ist in Deutschland ein starkes generalistisches Fundament — aber was dich in einen Job bringt, ist nicht der Abschluss, sondern seine Verbindung mit einem **Fachgebiet**. Quantitative Wege (Quant, Aktuar, Data) sind für Mathe-Absolventen die konkretesten und lukrativsten; reine Wissenschaft ist kompetitiv und startet gehaltlich niedrig. Mein ehrlicher Rat: Wähle ein Gebiet vor dem Abschluss, spezialisiere dich in dessen Werkzeugen, fülle deinen Lebenslauf mit Praktikum/Werkstudent und nutze die 18 Monate danach fokussiert. Verzögere die Spezialisierung nicht; sobald du die Kraft deiner Mathematik in eine Richtung kanalisierst, öffnet sich der Arbeitsmarkt.

*Alle Zahlen, Aufenthaltsfristen, Gehaltsspannen und Visaregeln in diesem Artikel sind ungefähre Werte mit Stand 2025/2026 und können sich ändern. Prüfe vor der Bewerbung unbedingt die aktuellen Informationen aus offiziellen Quellen (Hochschule, DAV, Bundesagentur für Arbeit, Ausländerbehörde).*
MD;

        $enBody = <<<'MD'
You have a mathematics degree (or you are about to get one) and you are facing the classic question: "So what do I actually do with it?" The good news: the job market for mathematics graduates in Germany is wider than you think. The less good news: the degree alone does not put you in a job — what puts you in a job is connecting your maths to a concrete **field**. In this article we look honestly at where a maths degree leads and what your realistic path looks like as an international student in Germany.

## Generalist strength: where does the degree take you?

Mathematics is one of the most abstract and demanding subjects at university. Precisely for that reason, employers treat it as a kind of **thinking certificate**: someone who can break down complex problems, works with logical and proof discipline, and understands quantitative models. That is why, as a maths graduate, you are not locked into a single profession — from finance and insurance to software and consulting, a wide field opens up to you.

But there is a trap here: "you can go anywhere" can also mean "you are not really prepared for anywhere". Employers respect your mathematical intelligence, but they hire you for a concrete **role**: quant, actuary, data analyst, software developer. Generalist strength is starting capital — turning it into a specialisation is your job.

## Career paths: where does mathematics open up?

The table below summarises the most common fields for mathematics/statistics graduates in Germany (as of 2025/2026, approximate; verify for yourself):

| Career path | What you do | Demand | Note |
|---|---|---|---|
| Finance / quant / risk | Pricing, risk modelling, portfolio | High | Frankfurt-heavy; English-friendly |
| Insurance — **Actuary (Aktuar)** | Risk/pricing, DAV certification | Very high | Clear, well-paid path; German helps |
| Data science / analytics | Models, forecasting, ETL, ML | High | Statistics background is a big advantage |
| IT / software | Backend, scientific computing | High | Programming practice is a must |
| Consulting | Quantitative analysis, optimisation | Medium-high | Communication + German matter |
| Research / academia | PhD, university/institute | Competitive | Low starting salary |
| Teaching (Lehramt) | Maths at school | Stable | German C1+ and pedagogical training required |

You will notice that the most practical and highest-return paths are the **quantitative** ones — quant, actuarial and data. These three make the employability of a maths degree in Germany most concrete. The actuary path is especially distinct: it runs through **DAV certification (Deutsche Aktuarvereinigung)**, is in strong demand and well paid.

## Why specialisation is a must: maths → field

This is the heart of the article. In the German job market, saying "I am a mathematician" is not enough; you have to say "I am a mathematician **for this field**". Job ads are defined by roles: *Risk Analyst*, *Aktuar (DAV)*, *Data Scientist*, *Quantitative Developer*. Pure maths knowledge opens the door to these roles, but getting inside requires domain knowledge and tools.

In practice, specialisation looks like this:

- **Finance/quant**: stochastics, financial mathematics, Python/C++, derivatives pricing.
- **Actuarial**: insurance mathematics, probability, DAV modules, German industry language.
- **Data**: statistics, machine learning, SQL/Python, portfolio projects.

**Hard truth:** unemployed maths graduates are rare in Germany — but those who are usually never tied themselves to a field and insist "I do nothing except pure mathematics". Specialisation is not a betrayal; it is the place where your maths pays off.

If you want to plan these transitions in more depth, the article [Working with a mathematics degree in Germany: quant, actuary, data, salary](/en/blog/working-with-a-mathematics-degree-in-germany-quant-actuary-data-salary-en) guides you sector by sector.

## After graduation: 18 months of job search

As an international student, this section is crucial for you. After completing a degree in Germany, you can obtain an **18-month residence permit to look for work**. During this time you may stay in the country and search for a qualified job; once you find one, you switch to a work permit / EU Blue Card.

These 18 months are worth gold, but limited. To avoid wasting them:

- Build internship / Werkstudent experience **before** you graduate.
- Prepare your CV and LinkedIn to German standards; focus on one role.
- Set a clear target (actuarial/quant/data) — scattergun applications waste time.
- Improve your German; many jobs are in English, but German opens doors.

You can find the whole residence and visa strategy in the article [Master's vs job-seeker visa in Germany: the two keys to your career](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

## German + strategy: both together

Let's be honest about language: a large part of research, data and quant roles run in English — especially in international companies and Frankfurt's finance scene. But insurance (actuarial), consulting, teaching and the broad local market largely require **German**. Once your German rises to B2–C1, your job spectrum multiplies.

The strategy: do not think of language learning separately from your career goal. If you want to become an actuary, prioritise German; if you target an international data/quant role, English may be enough — but German remains an advantage. University choice matters too, through the programme's strength and location — the article [How university prestige and rankings work in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en) helps you here.

## A realistic path for international students

Putting it all together, a solid route with a maths degree in Germany looks like this:

1. Enter a mathematics/statistics programme (for the entry point see [Studying mathematics/statistics in Germany](/en/blog/studying-mathematics-statistics-in-germany-as-a-foreigner-en)).
2. If your German is weak, consider English-taught master's options ([English-taught mathematics/statistics master's without German](/en/blog/english-taught-mathematics-statistics-masters-in-germany-without-german-en)).
3. Choose a field (finance/actuarial/data) and specialise in its tools.
4. Build experience through internships/Werkstudent.
5. Use the 18-month post-graduation permit for a focused job search.
6. Improve your German in parallel.

For maths graduates who follow these steps, landing a job in Germany is the rule, not the exception.

## Conclusion & honest advice

A mathematics degree is a strong generalist foundation in Germany — but what puts you in a job is not the degree, it is connecting it to a **field**. Quantitative paths (quant, actuary, data) are the most concrete and highest-return options for maths graduates; pure academia is competitive and starts low on pay. My honest advice: choose a field before you graduate, specialise in its tools, fill your CV with internships/Werkstudent, and use the 18 months afterwards in a focused way. Do not delay specialising; the moment you channel the power of your mathematics in one direction, the job market opens up to you.

*All figures, residence periods, salary ranges and visa rules in this article are approximate as of 2025/2026 and may change. Before applying, always verify current information from official sources (the university, DAV, the Bundesagentur für Arbeit, the immigration office).*
MD;

        $variants = [
            'tr' => ['slug'=>'what-to-do-with-a-mathematics-degree-in-germany-job-market',    'title'=>'Almanya\'da Matematik Diplomasıyla Ne Yapılır? İş Piyasası & Kariyer (2026)', 'excerpt'=>'Almanya\'da matematik diploması güçlü bir generalist temeldir ama seni işe koyan şey uzmanlaşmadır. Kariyer yolları, aktüerya/quant/veri, 18 aylık iş-arama oturumu ve gerçekçi rota.', 'meta_title'=>'Matematik Diplomasıyla Almanya\'da Ne Yapılır? (2026)', 'meta_description'=>'Almanya\'da matematik diplomasıyla kariyer: finans/quant, aktüer, veri, IT, akademi. Uzmanlaşma neden şart, 18 aylık iş-arama oturumu ve uluslararası öğrenci için gerçekçi yol.', 'body'=>$trBody],
            'de' => ['slug'=>'what-to-do-with-a-mathematics-degree-in-germany-job-market-de', 'title'=>'Was macht man mit einem Mathematik-Abschluss in Deutschland? Arbeitsmarkt & Karriere (2026)', 'excerpt'=>'Ein Mathe-Abschluss ist in Deutschland ein starkes generalistisches Fundament, doch was dich in einen Job bringt, ist die Spezialisierung. Karrierewege, Aktuariat/Quant/Data und die 18-monatige Job-Suche.', 'meta_title'=>'Mathematik-Abschluss in Deutschland: Was nun? (2026)', 'meta_description'=>'Karriere mit Mathe-Abschluss in Deutschland: Finanzen/Quant, Aktuar, Data, IT, Wissenschaft. Warum Spezialisierung Pflicht ist, 18 Monate Job-Suche und ein realistischer Weg.', 'body'=>$deBody],
            'en' => ['slug'=>'what-to-do-with-a-mathematics-degree-in-germany-job-market-en', 'title'=>'What to Do With a Mathematics Degree in Germany? Job Market & Career (2026)', 'excerpt'=>'A maths degree is a strong generalist foundation in Germany, but what puts you in a job is specialisation. Career paths, actuarial/quant/data and the 18-month job search.', 'meta_title'=>'Maths Degree in Germany: What Can You Do? (2026)', 'meta_description'=>'Careers with a maths degree in Germany: finance/quant, actuary, data, IT, academia. Why specialisation is a must, the 18-month job search and a realistic path for students.', 'body'=>$enBody],
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
            'what-to-do-with-a-mathematics-degree-in-germany-job-market',
            'what-to-do-with-a-mathematics-degree-in-germany-job-market-de',
            'what-to-do-with-a-mathematics-degree-in-germany-job-market-en',
        ])->delete();
    }
};

<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Ekonomi/VWL diplomasıyla iş piyasası & kariyer (2026). Doğrulandı: VWL BWL'den farklı yollar
 * açar (araştırma/politika/veri/uluslararası kuruluş); mezuniyet sonrası 18 ay iş-arama izni; giriş ~45-55k€ hedge.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c4e40000-4444-4ec0-9f40-cc01dd03ff04';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Ekonomi (VWL — Volkswirtschaftslehre) diploması elinde ama "şimdi ne olacak?" diye soruyorsan yalnız değilsin. VWL, işletme (BWL) gibi seni doğrudan tek bir mesleğe hazırlamaz; onun yerine analiz, veri ve politika düşünme becerisi verir. Bu yazı, Almanya'da bir ekonomi diplomasının kapısını açtığı **gerçek iş yollarını**, giriş pozisyonlarını ve uluslararası öğrenci olarak stratejiyi dürüstçe anlatır.

## VWL Çok Yönlüdür: Seni Nereye Götürür (BWL'den Farklı Yollar)
Önce net bir ayrım: **BWL** (işletme) muhasebe, pazarlama, insan kaynakları, satış gibi şirket-içi fonksiyonlara götürür. **VWL** ise makro/mikro iktisat, ekonometri ve politika analizi üzerine kuruludur. Bu yüzden VWL mezununun tipik yolları BWL'den ayrılır:

- **Araştırma & akademi** (üniversite, enstitü)
- **Merkez bankası & para politikası** (Bundesbank, ECB)
- **Politika & bakanlık** (ekonomi/maliye bakanlıkları, kamu kurumları)
- **Veri bilimi & analitik** (kantitatif matematik gücü sayesinde çok güçlü bir yol)
- **Bankacılık, finans, sigorta, danışmanlık**
- **Uluslararası kuruluşlar** (IMF, OECD, Dünya Bankası)

Yani VWL "dar" değil; sadece BWL'den **farklı bir kapı seti** açar. Dürüst gerçek: VWL diploması tek başına bir unvan garantisi değildir — beceri profilin (ekonometri mi, politika mı, veri mi) yönünü belirler.

## Kariyer Yolları: Sektör, Tipik Rol ve Almanca İhtiyacı
Aşağıdaki tablo (**2025/2026 itibarıyla, yaklaşık; doğrula**) VWL diplomasının başlıca hedef sektörlerini özetler.

| Sektör | Tipik giriş rolü | Almanca gerçeği | Doktora gerekir mi? |
|---|---|---|---|
| Araştırma enstitüsü (ifo, DIW, ZEW) | Research assistant / associate | İngilizce çoğu zaman yeterli | Kıdem için yaygın |
| Merkez bankası (Bundesbank, ECB) | Economist / analyst | ECB İngilizce, Bundesbank Almanca | Sık istenir |
| Politika / bakanlık | Referent / policy analyst | **Almanca genelde şart** | Genelde hayır |
| Veri / analitik | Data / business analyst | Role göre değişir | Hayır |
| Banka / finans / sigorta | Risk / research / actuarial | Çoğu kurumsal iş Almanca | Hayır |
| Danışmanlık | Consultant / analyst | Müşteriye göre; sık Almanca | Hayır |

**Kalın gerçek:** Veri/analitik yolu, VWL'nin ekonometri ve istatistik gücü sayesinde uluslararası öğrenci için genelde en erişilebilir kapıdır — çünkü teknik beceri, dil eksiğini kısmen telafi edebilir.

## Giriş Pozisyonları & Trainee Programları
Almanya'da yeni mezun ekonomistler genelde şu yollardan girer:
- **Trainee programları:** Büyük bankalar, sigorta şirketleri ve danışmanlıklar 12-24 aylık yapılandırılmış giriş programları sunar.
- **Werkstudent → dönüşüm:** Öğrenciyken part-time çalıştığın şirkette tam zamanlı geçiş.
- **Research assistant (WiMi):** Üniversite/enstitüde doktoraya giden ilk basamak.
- **Junior analyst / consultant:** Veri, risk veya piyasa analizi rolleri.

**Maaş (2025/2026 itibarıyla, yaklaşık; doğrula):** giriş seviyesi kabaca **45.000–55.000€/yıl**; finans ve danışmanlık üstünü öder; araştırma/doktora başlangıcı (WiMi pozisyonları) daha düşük başlar. Kesin rakam sektöre, şehre ve dil seviyene göre çok oynar.

## Mezuniyet Sonrası 18 Aylık İş-Arama İzni → Çalışma İzni
Uluslararası öğrenci için en kritik köprü budur: Alman üniversitesinden mezun olan **non-EU öğrenciler, iş aramak için mezuniyet sonrası izin süresine** hak kazanır. **2025/2026 itibarıyla, yaklaşık; doğrula:** bu süre yaygın olarak **18 aya** kadardır ve bu sürede tam zamanlı çalışıp iş arayabilirsin.

Mantık şu:
1. Mezun ol → iş-arama izni için başvur (18 aya kadar).
2. Alanına uygun bir iş teklifi al.
3. Teklifle birlikte **çalışma izni / Blue Card**'a geç.

**Blue Card gerçeği:** Ekonomi her zaman "darboğaz meslek" (Engpassberuf) listesinde olmaz, yani genel maaş eşiği geçerli olabilir (**~48.300€, 2025; hedge**). Yani iyi maaşlı bir teklif, kalıcı bir izin için önemli bir kaldıraçtır. Bu süreci kardeş yazımızda derinlemesine ele aldık: [Master mı iş-arama vizesi mi?](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career).

## Almanca + Kantitatif Beceri: İki Anahtar
İki dürüst gerçeği aynı anda kabul et:
1. **Kantitatif beceri seni ayırır:** Ekonometri, Python/R/STATA, veri modelleme — bunlar VWL mezununun süper gücüdür ve İngilizce-dostu rollerde kapı açar.
2. **Almanca seni derinleştirir:** Politika, bakanlık, çoğu kurumsal ve müşteri-yüzlü rol **Almanca ister**. B2-C1 seviyesi, açık olan iş havuzunu kat kat büyütür.

Yani strateji nettir: teknik beceriyi keskin tut, Almancayı ihmal etme. İkisi bir arada, uluslararası öğrenciyi en rekabetçi konuma getirir.

## Uluslararası Öğrenci Stratejisi: Staj, Network, Doktora Kararı
- **Staj/Werkstudent erken başlat:** Alman iş piyasası deneyime ve yerel referansa değer verir. İlk günden ağ kurmaya başla.
- **Network:** Üniversite career center, LinkedIn, sektör etkinlikleri, enstitü seminerleri. Almanya'da "kim tanıyor" ciddi rol oynar.
- **Doktora kararını bilinçli ver:** Araştırma ve merkez bankası yolu için doktora (Bonn BGSE, Mannheim CDSE gibi programlar) çoğu zaman gereklidir; ama endüstri/veri/danışmanlık için **şart değildir** — hatta zaman maliyeti olabilir.
- **Beceri profilini seç:** Ekonometri/veri mi, politika mı, finans mı? Erken netleşen profil, doğru staj ve doğru ilk işi getirir.

Derinlemesine yollar için: [Ekonomist olarak çalışmak](/tr/blog/working-as-an-economist-in-germany-research-policy-finance), [VWL okumak](/tr/blog/studying-economics-vwl-in-germany-as-a-foreigner) ve [Almancasız İngilizce master](/tr/blog/english-taught-economics-masters-in-germany-without-german). BWL tarafını merak ediyorsan: [İşletme (BWL) diplomasıyla iş piyasası](/tr/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market).

## Sonuç & Dürüst Tavsiye
VWL diploması sana tek bir meslek değil, **bir düşünme ve analiz araç kutusu** verir. Almanya'da bu araç kutusu araştırmadan merkez bankasına, veri biliminden politikaya kadar geniş bir yelpazeye açılır. Ama dürüst ol: diploma tek başına iş getirmez. Kazandıran formül şudur — **kantitatif beceri + Almanca + erken staj + net bir beceri profili**, mezuniyet sonrası 18 aylık iş-arama izniyle birleştiğinde. Rakam ve süre hedeflemesi yerine, bugünden Python/R öğren, Almancanı B2'ye taşı ve ilk Werkstudent'ini bul.

*Bu yazı 2026 başı itibarıyla hazırlanmıştır. Vize süreleri, maaş eşikleri ve Blue Card kuralları değişebilir; başvurudan önce resmi kaynaklardan (ilgili üniversite, Ausländerbehörde, Make it in Germany) güncel bilgiyi doğrula.*
MD;
        $deBody = <<<'MD'
Du hast deinen VWL-Abschluss (Volkswirtschaftslehre) in der Tasche und fragst dich: "Und jetzt?" Damit bist du nicht allein. Anders als BWL bereitet dich VWL nicht direkt auf einen einzigen Beruf vor — sie gibt dir Analyse-, Daten- und politisches Denkvermögen. Dieser Artikel zeigt dir ehrlich die **echten Karrierewege**, die dir ein Wirtschaftsabschluss in Deutschland eröffnet.

## VWL ist vielseitig: Wohin sie dich führt (andere Wege als BWL)
Zuerst eine klare Abgrenzung: **BWL** führt zu unternehmensinternen Funktionen wie Rechnungswesen, Marketing, HR oder Vertrieb. **VWL** dagegen baut auf Makro-/Mikroökonomie, Ökonometrie und Politikanalyse auf. Deshalb unterscheiden sich die typischen Wege deutlich:

- **Forschung & Wissenschaft** (Universität, Institut)
- **Zentralbank & Geldpolitik** (Bundesbank, EZB)
- **Politik & Ministerien** (Wirtschafts-/Finanzministerien, öffentliche Stellen)
- **Data Science & Analytik** (dank quantitativer Stärke ein sehr starker Weg)
- **Banken, Finanzen, Versicherung, Beratung**
- **Internationale Organisationen** (IWF, OECD, Weltbank)

VWL ist also nicht "eng" — sie öffnet nur ein **anderes Türset** als BWL. Ehrliche Wahrheit: Der Abschluss allein ist keine Jobgarantie — dein Skill-Profil (Ökonometrie, Politik oder Daten) bestimmt die Richtung.

## Karrierewege: Branche, typische Rolle und Deutsch-Bedarf
Die Tabelle unten (**Stand 2025/2026, ungefähr; bitte prüfen**) fasst die wichtigsten Zielbranchen zusammen.

| Branche | Typische Einstiegsrolle | Deutsch-Realität | Promotion nötig? |
|---|---|---|---|
| Forschungsinstitut (ifo, DIW, ZEW) | Research Assistant / Associate | Englisch oft ausreichend | Für Seniorität üblich |
| Zentralbank (Bundesbank, EZB) | Economist / Analyst | EZB englisch, Bundesbank deutsch | Häufig gefragt |
| Politik / Ministerium | Referent / Policy Analyst | **Deutsch meist Pflicht** | Meist nein |
| Data / Analytik | Data / Business Analyst | Je nach Rolle | Nein |
| Bank / Finanzen / Versicherung | Risk / Research / Aktuariat | Meist deutsch | Nein |
| Beratung | Consultant / Analyst | Je nach Mandant; oft deutsch | Nein |

**Fettgedruckte Wahrheit:** Der Daten-/Analytik-Weg ist dank der ökonometrischen und statistischen Stärke der VWL für internationale Studierende meist die zugänglichste Tür — denn technisches Können gleicht fehlendes Deutsch teilweise aus.

## Einstiegspositionen & Trainee-Programme
Frisch abgeschlossene Volkswirt:innen steigen in Deutschland meist so ein:
- **Trainee-Programme:** Große Banken, Versicherungen und Beratungen bieten strukturierte 12-24-monatige Einstiegsprogramme.
- **Werkstudent → Übernahme:** Übergang in Vollzeit im Unternehmen, in dem du als Student:in gearbeitet hast.
- **Wissenschaftliche:r Mitarbeiter:in (WiMi):** Erster Schritt zur Promotion an Uni/Institut.
- **Junior Analyst / Consultant:** Rollen in Daten-, Risiko- oder Marktanalyse.

**Gehalt (Stand 2025/2026, ungefähr; bitte prüfen):** Einstieg grob **45.000-55.000€/Jahr**; Finanzen und Beratung zahlen darüber; Forschung/Promotion (WiMi-Stellen) starten niedriger. Die genaue Zahl schwankt stark nach Branche, Stadt und Sprachniveau.

## 18 Monate Jobsuche nach dem Abschluss → Arbeitserlaubnis
Für internationale Studierende ist dies die entscheidende Brücke: Non-EU-Absolvent:innen einer deutschen Hochschule erhalten **nach dem Abschluss eine Aufenthaltserlaubnis zur Jobsuche**. **Stand 2025/2026, ungefähr; bitte prüfen:** Dieser Zeitraum beträgt üblicherweise bis zu **18 Monate**, und in dieser Zeit darfst du Vollzeit arbeiten und suchen.

Die Logik:
1. Abschluss → Aufenthalt zur Jobsuche beantragen (bis zu 18 Monate).
2. Ein fachlich passendes Jobangebot erhalten.
3. Mit dem Angebot in **Arbeitserlaubnis / Blaue Karte** wechseln.

**Blaue-Karte-Realität:** Wirtschaft steht nicht immer auf der Engpassberuf-Liste, also kann die allgemeine Gehaltsschwelle gelten (**~48.300€, 2025; Richtwert**). Ein gut bezahltes Angebot ist daher ein wichtiger Hebel für einen dauerhaften Aufenthalt. Diesen Prozess haben wir im Schwesterartikel vertieft: [Master oder Jobsuche-Visum?](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).

## Deutsch + quantitative Skills: zwei Schlüssel
Akzeptiere zwei ehrliche Wahrheiten gleichzeitig:
1. **Quantitative Skills heben dich hervor:** Ökonometrie, Python/R/STATA, Datenmodellierung — das ist die Superkraft der Volkswirt:innen und öffnet Türen in englischsprachigen Rollen.
2. **Deutsch vertieft dich:** Politik, Ministerien, die meisten Unternehmens- und kundennahen Rollen **verlangen Deutsch**. B2-C1 vergrößert deinen offenen Jobpool enorm.

Die Strategie ist also klar: Halte die technischen Skills scharf und vernachlässige Deutsch nicht. Beides zusammen bringt internationale Studierende in die wettbewerbsfähigste Position.

## Strategie für internationale Studierende: Praktikum, Netzwerk, Promotionsentscheidung
- **Praktikum/Werkstudent früh starten:** Der deutsche Arbeitsmarkt schätzt Erfahrung und lokale Referenzen. Netzwerke ab Tag eins.
- **Netzwerk:** Career Center der Uni, LinkedIn, Branchenevents, Institutsseminare. In Deutschland zählt "Wen kennst du" ernsthaft.
- **Promotionsentscheidung bewusst treffen:** Für Forschung und Zentralbank ist eine Promotion (z. B. Bonn BGSE, Mannheim CDSE) oft nötig; für Industrie/Daten/Beratung dagegen **kein Muss** — sie kann sogar Zeitkosten bedeuten.
- **Skill-Profil wählen:** Ökonometrie/Daten, Politik oder Finanzen? Ein früh geklärtes Profil bringt das richtige Praktikum und den richtigen ersten Job.

Für vertiefte Wege: [Als Ökonom:in arbeiten](/de/blog/working-as-an-economist-in-germany-research-policy-finance-de), [VWL studieren](/de/blog/studying-economics-vwl-in-germany-as-a-foreigner-de) und [Englischsprachiger Master ohne Deutsch](/de/blog/english-taught-economics-masters-in-germany-without-german-de). Interessiert dich die BWL-Seite: [Arbeitsmarkt mit BWL-Abschluss](/de/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market-de).

## Fazit & ehrlicher Rat
Ein VWL-Abschluss gibt dir keinen einzelnen Beruf, sondern einen **Werkzeugkasten zum Denken und Analysieren**. In Deutschland öffnet sich dieser Werkzeugkasten von der Forschung über die Zentralbank bis zu Data Science und Politik. Aber sei ehrlich: Der Abschluss allein bringt keinen Job. Die Gewinnerformel lautet — **quantitative Skills + Deutsch + frühes Praktikum + klares Skill-Profil**, kombiniert mit der 18-monatigen Aufenthaltserlaubnis zur Jobsuche. Statt auf Zahlen zu starren: Lerne heute Python/R, bring dein Deutsch auf B2 und finde deinen ersten Werkstudentenjob.

*Dieser Artikel ist auf dem Stand von Anfang 2026. Visadauern, Gehaltsschwellen und Regeln der Blauen Karte können sich ändern; prüfe vor der Bewerbung aktuelle Informationen aus offiziellen Quellen (jeweilige Hochschule, Ausländerbehörde, Make it in Germany).*
MD;
        $enBody = <<<'MD'
You have your economics degree (VWL — Volkswirtschaftslehre) in hand and you are asking "now what?" You are not alone. Unlike business administration (BWL), economics does not train you directly for a single job — instead it gives you analysis, data and policy-thinking skills. This article honestly walks through the **real career paths** an economics degree opens in Germany, the entry roles, and the strategy for international students.

## Economics Is Versatile: Where It Takes You (Paths Different from BWL)
First, a clear distinction: **BWL** (business administration) leads to in-company functions such as accounting, marketing, HR or sales. **VWL** (economics) is built on macro/micro economics, econometrics and policy analysis. So the typical paths differ noticeably:

- **Research & academia** (universities, institutes)
- **Central banking & monetary policy** (Bundesbank, ECB)
- **Policy & ministries** (economic/finance ministries, public bodies)
- **Data science & analytics** (a very strong path thanks to quantitative strength)
- **Banking, finance, insurance, consulting**
- **International organisations** (IMF, OECD, World Bank)

So economics is not "narrow" — it simply opens a **different set of doors** than BWL. Honest truth: the degree alone is not a job guarantee — your skill profile (econometrics, policy or data) sets the direction.

## Career Paths: Sector, Typical Role and German Reality
The table below (**as of 2025/2026, approximate; verify**) summarises the main target sectors.

| Sector | Typical entry role | German reality | PhD needed? |
|---|---|---|---|
| Research institute (ifo, DIW, ZEW) | Research assistant / associate | English often enough | Common for seniority |
| Central bank (Bundesbank, ECB) | Economist / analyst | ECB English, Bundesbank German | Frequently expected |
| Policy / ministry | Referent / policy analyst | **German usually required** | Usually no |
| Data / analytics | Data / business analyst | Depends on the role | No |
| Bank / finance / insurance | Risk / research / actuarial | Mostly German | No |
| Consulting | Consultant / analyst | Depends on client; often German | No |

**Bold fact:** The data/analytics path is usually the most accessible door for international students thanks to economics' econometric and statistical strength — because technical skill partly offsets weaker German.

## Entry Positions & Trainee Programmes
Fresh economics graduates in Germany typically enter through:
- **Trainee programmes:** Large banks, insurers and consultancies offer structured 12-24 month entry programmes.
- **Werkstudent → conversion:** Moving to full-time at the company where you worked part-time as a student.
- **Research assistant (WiMi):** The first rung toward a PhD at a university/institute.
- **Junior analyst / consultant:** Roles in data, risk or market analysis.

**Salary (as of 2025/2026, approximate; verify):** entry level roughly **€45,000-55,000/year**; finance and consulting pay above that; research/PhD starts (WiMi positions) begin lower. The exact figure varies a lot by sector, city and language level.

## The 18-Month Post-Graduation Job Search → Work Permit
For international students this is the crucial bridge: non-EU graduates of a German university are entitled to a **post-graduation residence permit to look for a job**. **As of 2025/2026, approximate; verify:** this period is commonly up to **18 months**, and during it you may work full-time while searching.

The logic:
1. Graduate → apply for the job-search residence permit (up to 18 months).
2. Receive a job offer relevant to your field.
3. Switch to a **work permit / EU Blue Card** with the offer.

**Blue Card reality:** Economics is not always on the shortage-occupation (Engpassberuf) list, so the general salary threshold may apply (**~€48,300, 2025; hedge**). A well-paid offer is therefore a major lever for a lasting permit. We cover this process in depth in a sister article: [Master's vs job-seeker visa](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

## German + Quantitative Skills: Two Keys
Accept two honest truths at once:
1. **Quantitative skills set you apart:** econometrics, Python/R/STATA, data modelling — this is the economist's superpower and opens doors in English-friendly roles.
2. **German deepens you:** policy, ministries, most corporate and client-facing roles **require German**. B2-C1 hugely expands your open job pool.

So the strategy is clear: keep technical skills sharp and do not neglect German. The two together put international students in the most competitive position.

## International Student Strategy: Internship, Network, PhD Decision
- **Start internships/Werkstudent early:** the German labour market values experience and local references. Start networking from day one.
- **Network:** university career centre, LinkedIn, industry events, institute seminars. In Germany "who you know" genuinely matters.
- **Make the PhD decision deliberately:** for research and central banking a PhD (e.g. Bonn BGSE, Mannheim CDSE) is often required; for industry/data/consulting it is **not a must** — it can even be a time cost.
- **Choose your skill profile:** econometrics/data, policy or finance? A profile clarified early brings the right internship and the right first job.

For deeper paths: [Working as an economist](/en/blog/working-as-an-economist-in-germany-research-policy-finance-en), [Studying economics (VWL)](/en/blog/studying-economics-vwl-in-germany-as-a-foreigner-en) and [English-taught master's without German](/en/blog/english-taught-economics-masters-in-germany-without-german-en). If the BWL side interests you: [Job market with a business (BWL) degree](/en/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market-en).

## Conclusion & Honest Advice
An economics degree does not hand you a single profession but a **toolkit for thinking and analysing**. In Germany this toolkit opens onto everything from research and central banking to data science and policy. But be honest: the degree alone brings no job. The winning formula is — **quantitative skills + German + early internship + a clear skill profile**, combined with the 18-month post-graduation job-search permit. Instead of staring at numbers: learn Python/R today, push your German to B2, and find your first Werkstudent role.

*This article reflects the situation as of early 2026. Visa durations, salary thresholds and Blue Card rules can change; before applying, verify current information from official sources (the relevant university, the Ausländerbehörde, Make it in Germany).*
MD;

        $variants = [
            'tr' => ['slug'=>'what-to-do-with-an-economics-vwl-degree-in-germany-job-market',    'title'=>'Almanya\'da Ekonomi/VWL Diplomasıyla Ne Yapılır? İş Piyasası & Kariyer (2026)', 'excerpt'=>'Almanya\'da VWL (ekonomi) diploması hangi kapıları açar? Araştırma, merkez bankası, politika, veri bilimi ve finans yolları; giriş maaşları ve mezuniyet sonrası 18 aylık iş-arama izni — uluslararası öğrenci için dürüst rehber.', 'meta_title'=>'Almanya\'da Ekonomi/VWL Diplomasıyla İş Piyasası (2026)', 'meta_description'=>'VWL diplomasıyla Almanya\'da kariyer: araştırma, merkez bankası, politika, veri, finans. Giriş maaşı, 18 ay iş-arama izni ve uluslararası öğrenci stratejisi.', 'body'=>$trBody],
            'de' => ['slug'=>'what-to-do-with-an-economics-vwl-degree-in-germany-job-market-de', 'title'=>'Was tun mit einem VWL-Abschluss in Deutschland? Arbeitsmarkt & Karriere (2026)', 'excerpt'=>'Welche Türen öffnet ein VWL-Abschluss in Deutschland? Forschung, Zentralbank, Politik, Data Science und Finanzen; Einstiegsgehälter und die 18-monatige Aufenthaltserlaubnis zur Jobsuche — ein ehrlicher Leitfaden.', 'meta_title'=>'VWL-Abschluss in Deutschland: Arbeitsmarkt (2026)', 'meta_description'=>'Karriere mit VWL-Abschluss in Deutschland: Forschung, Zentralbank, Politik, Daten, Finanzen. Einstiegsgehalt, 18 Monate Jobsuche und Strategie für Internationale.', 'body'=>$deBody],
            'en' => ['slug'=>'what-to-do-with-an-economics-vwl-degree-in-germany-job-market-en', 'title'=>'What to Do with an Economics (VWL) Degree in Germany: Job Market & Careers (2026)', 'excerpt'=>'What doors does an economics (VWL) degree open in Germany? Research, central banking, policy, data science and finance; entry salaries and the 18-month post-graduation job-search permit — an honest guide for international students.', 'meta_title'=>'Economics (VWL) Degree in Germany: Job Market (2026)', 'meta_description'=>'Careers with an economics (VWL) degree in Germany: research, central banking, policy, data, finance. Entry salary, 18-month job search and student strategy.', 'body'=>$enBody],
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
            'what-to-do-with-an-economics-vwl-degree-in-germany-job-market',
            'what-to-do-with-an-economics-vwl-degree-in-germany-job-market-de',
            'what-to-do-with-an-economics-vwl-degree-in-germany-job-market-en',
        ])->delete();
    }
};

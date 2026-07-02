<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da ekonomist olarak çalışmak — araştırma/politika/finans (2026).
 * Doğrulandı: VWL kariyer yolları (ifo/DIW/ZEW, Bundesbank/ECB, bakanlık, finans, danışmanlık, veri).
 * Maaş ve Blue Card eşiği 2025 itibarıyla yaklaşık ve hedge'li; doktora araştırma/merkez bankası için yaygın.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c3e30000-3333-4ec0-9f40-cc01dd03ff03';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da ekonomi (VWL — Volkswirtschaftslehre) diploman var ya da yakında olacak ve asıl soru şu: **ekonomist olarak nerede, nasıl çalışılır?** Bu yazı işletme (BWL) değil, iktisatçının dünyasına odaklanıyor: araştırma enstitüleri, merkez bankaları, bakanlıklar, finans ve danışmanlık. Kantitatif gücünü nereye koyacağını, maaş beklentisini, doktoranın ne zaman gerektiğini ve Almanca gerçeğini dürüstçe anlatıyoruz.

## Sektörler: ekonomist nerede çalışır?

VWL diploması seni "tek bir meslek"e kilitlemez; iktisatçılar birbirinden çok farklı kurumlarda çalışır. Ana damarlar:

- **Araştırma enstitüleri:** **ifo** (München), **DIW** (Berlin), **ZEW** (Mannheim), IW, RWI. Makro tahmin, politika değerlendirmesi, ekonometrik analiz.
- **Merkez bankaları:** **Bundesbank** ve Frankfurt'taki **ECB** (Avrupa Merkez Bankası). Para politikası, finansal istikrar, araştırma.
- **Bakanlık & politika:** Ekonomi/Maliye bakanlıkları, düzenleyici kurumlar, rekabet otoritesi (Bundeskartellamt).
- **Finans:** bankalar, varlık yönetimi, sigorta (aktüerya-komşusu roller), risk ve makro strateji.
- **Danışmanlık:** ekonomik danışmanlık, rekabet/regülasyon ekonomisi, "economic consulting" firmaları.
- **Uluslararası kuruluşlar:** IMF, OECD, Dünya Bankası (genelde doktora + deneyim).

**Kalın gerçek:** VWL'nin en güçlü olduğu yerler, işletmeciden ziyade *analiz ve model* isteyen rollerdir — piyasa/politika etkisini sayılarla ölçmen beklenir.

## Veri & analitik yolu: kantitatif gücünü paraya çevir

İktisat eğitimi sana ekonometri, olasılık, nedensellik (causal inference) ve veri işleme verir. Bu, doğrudan **veri bilimi / analitik** rollerine köprü kurar: A/B test, tahmin modelleri, politika/iş etkisi ölçümü. Python/R + SQL öğrenen bir VWL mezunu, saf ekonomi rolleri kadar veri odaklı roller için de rekabetçidir. Almanya'da tech ve veri talebi yüksek; bu yol genellikle ekonomistin en hızlı ve en yüksek ücretli çıkış kapılarından biridir.

## Maaş: gerçekçi beklenti

Sayılar rol, sektör, şehir ve yıla göre çok oynar. Kaba bir çerçeve:

| Rol / sektör | Yaklaşık giriş (yıllık, brüt) | Not |
|---|---|---|
| Genel giriş (ekonomist/analist) | **~45–55k €** | Sektöre göre değişir |
| Finans / danışmanlık | üstü, sık sık **55k+ €** | Bonus/prim eklenebilir |
| Araştırma enstitüsü / doktora (WiMi) | **daha düşük** başlar (~kısmi TV-L) | Akademik ölçek |
| Bundesbank / ECB | rekabetçi, kurumsal ölçek | Kademeli ilerleme |

**Kalın gerçek:** araştırma ve doktora yolları başlangıçta *finans/danışmanlıktan düşük* öder ama uzun vadeli uzmanlık ve merkez bankası/uluslararası kuruluş kapısı açar. *2025 itibarıyla, yaklaşık; yıllık değişir, kendi hedef rolün için doğrula.*

## Doktora gerçeği: araştırma & merkez bankası için yaygın

Eğer hedefin **araştırma enstitüsü, merkez bankası araştırma bölümü veya akademi** ise, **doktora çoğu zaman fiilen şarttır**. Almanya'nın güçlü doktora programları structured (yapılandırılmış) modeldedir: **Bonn (BGSE)** ve **Mannheim (CDSE)** en bilinenleri; genelde İngilizce, uluslararası ve araştırma-yoğundur.

- **Doktora GEREKİR:** üst düzey araştırma, merkez bankası araştırma, akademik kadro, üst uluslararası kuruluş rolleri.
- **Doktora GEREKMEZ (genelde):** finans, danışmanlık, veri/analitik, birçok bakanlık/politika giriş rolü, kurumsal ekonomist pozisyonları.

**Kalın gerçek:** doktora bir "prestij süsü" değil, belirli kariyer kapılarının anahtarıdır — hedefin araştırma değilse, 4-5 yılı doktoraya harcamadan da güçlü bir kariyer kurabilirsin.

## Almanca gerçeği: araştırma İngilizce, politika Almanca

Bu ayrımı içselleştir, çünkü kariyer stratejini belirler:

- **İngilizce-dostu:** akademik/araştırma çıktısı, merkez bankası araştırması, uluslararası enstitü ekipleri, uluslararası finans/danışmanlık takımları. Ekonomi araştırmasının *ortak dili İngilizce*dir.
- **Almanca gerektirir:** **bakanlıklar, kamu politikası, düzenleyici kurumlar** ve çoğu yerel kurumsal rol. Kamu sektöründe genelde **C1 Almanca** beklenir; birçok ilanda zorunludur.

**Kalın gerçek:** kariyerini politika/kamu tarafında kurmak istiyorsan, Almanca'yı "sonra hallederim" diyeceğin bir şey değil, plana koyacağın bir yatırım olarak gör.

## Blue Card & vize (hedge'li)

Non-EU isen çalışma iznin genelde iş teklifi + nitelik üzerinden yürür. **AB Mavi Kart (Blue Card)**, üniversite diploması + belirli bir maaş eşiği tutan işlerde geçerlidir.

- **Genel maaş eşiği:** *2025 itibarıyla ~48.300 € brüt/yıl (yaklaşık; yıllık güncellenir, doğrula).*
- Ekonomi her zaman "darboğaz/kıtlık meslek" (Engpassberuf) listesinde olmayabilir → genel (indirimsiz) eşik geçerli olabilir; rolüne göre değişir.
- Giriş maaşları (~45–55k) bazı rollerde eşiği yakalar, bazılarında yakalamaz; **teklif mektubundaki brüt rakamı** eşikle karşılaştır.

**Kalın gerçek:** vize planını maaş teklifin netleşince yap; eşik yıllık değişir, resmî kaynaktan (Make it in Germany / yabancılar dairesi) teyit et.

## Sonuç & dürüst tavsiye

Almanya'da ekonomist kariyeri tek bir yol değil, bir yelpazedir: araştırma enstitüsü ve merkez bankası (doktora + İngilizce), politika/bakanlık (Almanca), finans/danışmanlık (yüksek ücret, hızlı giriş) ve veri/analitik (kantitatif güç). Dürüst tavsiye: **önce hedef sektörü seç**, sonra doktora ve Almanca kararlarını ona göre ver. Araştırma/merkez bankası istiyorsan doktora ve İngilizce yeter; politika/kamu istiyorsan Almanca'ya erken yatır; hız ve ücret istiyorsan finans/danışmanlık/veri yoluna yönel ve staj + network'e ağırlık ver.

Devamı için küme kardeşleri: [Almanya'da Ekonomi (VWL) okumak](/tr/blog/studying-economics-vwl-in-germany-as-a-foreigner), [Almancasız İngilizce ekonomi master'ları](/tr/blog/english-taught-economics-masters-in-germany-without-german) ve [Ekonomi/VWL diplomasıyla iş piyasası](/tr/blog/what-to-do-with-an-economics-vwl-degree-in-germany-job-market). Vize/kariyer için: [İş teklifiyle çalışma vizesi süreci](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track), [Almanya'da IT/tech'te çalışmak & Blue Card](/tr/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary) ve [Veri bilimi/AI'a nasıl girilir](/tr/blog/how-to-break-into-data-science-ai-in-germany).

*Bu yazı 2026 başı itibarıyla hazırlanmıştır. Maaşlar, Blue Card eşiği, vize kuralları ve kurum politikaları yıllık değişir — başvurudan önce resmî kaynaklardan (Make it in Germany, ilgili kurum, yabancılar dairesi) mutlaka doğrula.*
MD;

        $deBody = <<<'MD'
Du hast einen Abschluss in Volkswirtschaftslehre (VWL) — oder bald — und die eigentliche Frage lautet: **Wo und wie arbeitet man in Deutschland als Ökonom:in?** Dieser Beitrag dreht sich nicht um BWL, sondern um die Welt der Volkswirt:innen: Forschungsinstitute, Zentralbanken, Ministerien, Finanzsektor und Beratung. Wir sagen dir ehrlich, wo deine quantitative Stärke hingehört, was du beim Gehalt erwarten kannst, wann eine Promotion nötig ist und wie es mit dem Deutsch aussieht.

## Branchen: Wo arbeiten Ökonom:innen?

Ein VWL-Abschluss legt dich nicht auf einen einzigen Beruf fest. Die Hauptwege sind:

- **Forschungsinstitute:** **ifo** (München), **DIW** (Berlin), **ZEW** (Mannheim), IW, RWI. Makroprognosen, Politikevaluation, ökonometrische Analyse.
- **Zentralbanken:** **Bundesbank** und die **EZB** (Europäische Zentralbank) in Frankfurt. Geldpolitik, Finanzstabilität, Forschung.
- **Ministerien & Politik:** Wirtschafts-/Finanzministerien, Regulierungsbehörden, Bundeskartellamt.
- **Finanzsektor:** Banken, Asset Management, Versicherungen, Risiko- und Makrostrategie.
- **Beratung:** ökonomische Beratung, Wettbewerbs-/Regulierungsökonomie, "Economic Consulting".
- **Internationale Organisationen:** IWF, OECD, Weltbank (meist Promotion + Erfahrung).

**Fett gedruckt:** VWL ist dort am stärksten, wo *Analyse und Modelle* gefragt sind — man erwartet von dir, Markt- und Politikeffekte in Zahlen zu messen.

## Der Daten- & Analytik-Weg: quantitative Stärke nutzen

Ein VWL-Studium gibt dir Ökonometrie, Wahrscheinlichkeitsrechnung, Kausalanalyse (causal inference) und Datenkompetenz. Das ist eine direkte Brücke zu **Data Science / Analytics**: A/B-Tests, Prognosemodelle, Wirkungsmessung. Mit Python/R + SQL bist du als VWL-Absolvent:in nicht nur für klassische Ökonom:innen-Rollen konkurrenzfähig, sondern auch für datengetriebene Jobs. Die Nachfrage nach Daten- und Tech-Kompetenz in Deutschland ist hoch — oft der schnellste und bestbezahlte Ausstieg für Ökonom:innen.

## Gehalt: realistische Erwartung

Die Zahlen schwanken stark je nach Rolle, Branche, Stadt und Jahr. Ein grober Rahmen:

| Rolle / Branche | Ungefährer Einstieg (jährlich, brutto) | Hinweis |
|---|---|---|
| Allgemeiner Einstieg (Ökonom:in/Analyst:in) | **~45–55k €** | branchenabhängig |
| Finanzsektor / Beratung | darüber, oft **55k+ €** | Bonus möglich |
| Forschungsinstitut / Promotion (WiMi) | startet **niedriger** (teils Teil-TV-L) | akademische Skala |
| Bundesbank / EZB | wettbewerbsfähig, institutionell | stufenweise |

**Fett gedruckt:** Forschung und Promotion zahlen anfangs *weniger als Finanz/Beratung*, öffnen aber langfristig Expertise und Türen zu Zentralbank/internationalen Organisationen. *Stand 2025, ungefähr; jährlich veränderlich, für deine Zielrolle prüfen.*

## Die Promotions-Realität: üblich für Forschung & Zentralbank

Wenn dein Ziel ein **Forschungsinstitut, die Forschungsabteilung einer Zentralbank oder die Wissenschaft** ist, dann ist eine **Promotion faktisch meist Pflicht**. Deutschlands starke Doktorandenprogramme sind strukturiert: **Bonn (BGSE)** und **Mannheim (CDSE)** sind die bekanntesten — meist englischsprachig, international und forschungsintensiv.

- **Promotion NÖTIG:** Spitzenforschung, Zentralbankforschung, akademische Laufbahn, höhere Rollen bei internationalen Organisationen.
- **Promotion NICHT nötig (meist):** Finanz, Beratung, Data/Analytics, viele Ministeriums-/Politik-Einstiegsrollen, Ökonom:innen-Positionen in Unternehmen.

**Fett gedruckt:** Eine Promotion ist kein "Prestige-Schmuck", sondern der Schlüssel zu bestimmten Türen — wenn dein Ziel nicht die Forschung ist, kannst du auch ohne 4-5 Promotionsjahre eine starke Karriere aufbauen.

## Deutsch-Realität: Forschung Englisch, Politik Deutsch

Verinnerliche diese Unterscheidung, denn sie bestimmt deine Strategie:

- **Englischfreundlich:** akademischer/Forschungs-Output, Zentralbankforschung, internationale Institutsteams, internationale Finanz-/Beratungsteams. Die *gemeinsame Sprache der Ökonomieforschung ist Englisch*.
- **Deutsch erforderlich:** **Ministerien, öffentliche Politik, Regulierungsbehörden** und die meisten lokalen Unternehmensrollen. Im öffentlichen Dienst wird meist **C1-Deutsch** erwartet und in vielen Ausschreibungen vorausgesetzt.

**Fett gedruckt:** Wenn du deine Karriere auf der Politik-/Öffentlichkeitsseite aufbauen willst, ist Deutsch keine Sache für "später", sondern eine geplante Investition.

## Blue Card & Visum (mit Vorbehalt)

Wenn du Nicht-EU-Bürger:in bist, läuft deine Arbeitserlaubnis meist über Jobangebot + Qualifikation. Die **EU Blue Card** gilt bei einem Hochschulabschluss + einem bestimmten Gehaltsschwellenwert.

- **Allgemeine Gehaltsschwelle:** *Stand 2025 ca. 48.300 € brutto/Jahr (ungefähr; jährlich aktualisiert, bitte prüfen).*
- VWL steht nicht immer auf der Engpassberuf-Liste → es kann die allgemeine (höhere) Schwelle gelten; hängt von der Rolle ab.
- Einstiegsgehälter (~45–55k) erreichen die Schwelle in manchen Rollen, in anderen nicht; vergleiche den **Bruttobetrag im Angebot** mit der Schwelle.

**Fett gedruckt:** Plane das Visum, sobald dein Gehaltsangebot konkret ist; die Schwelle ändert sich jährlich — bestätige sie über eine offizielle Quelle (Make it in Germany / Ausländerbehörde).

## Fazit & ehrlicher Rat

Eine Ökonom:innen-Karriere in Deutschland ist kein einzelner Weg, sondern ein Spektrum: Forschungsinstitut und Zentralbank (Promotion + Englisch), Politik/Ministerium (Deutsch), Finanz/Beratung (hohes Gehalt, schneller Einstieg) und Data/Analytics (quantitative Stärke). Ehrlicher Rat: **wähle zuerst die Zielbranche**, entscheide dann über Promotion und Deutsch. Für Forschung/Zentralbank reichen Promotion + Englisch; für Politik/öffentlichen Dienst investiere früh ins Deutsch; für Tempo und Gehalt gehe Richtung Finanz/Beratung/Data und setze auf Praktika + Netzwerk.

Weiter mit den Cluster-Geschwistern: [VWL in Deutschland studieren](/de/blog/studying-economics-vwl-in-germany-as-a-foreigner-de), [Englischsprachige Economics-Master ohne Deutsch](/de/blog/english-taught-economics-masters-in-germany-without-german-de) und [Was tun mit einem VWL-Abschluss — Arbeitsmarkt](/de/blog/what-to-do-with-an-economics-vwl-degree-in-germany-job-market-de). Für Visum/Karriere: [Arbeitsvisum mit Jobangebot — Prozess](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de), [In IT/Tech in Deutschland arbeiten & Blue Card](/de/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-de) und [Einstieg in Data Science/KI](/de/blog/how-to-break-into-data-science-ai-in-germany-de).

*Dieser Beitrag entstand Anfang 2026. Gehälter, Blue-Card-Schwelle, Visaregeln und Institutionsrichtlinien ändern sich jährlich — prüfe vor der Bewerbung immer offizielle Quellen (Make it in Germany, die jeweilige Institution, Ausländerbehörde).*
MD;

        $enBody = <<<'MD'
You have (or soon will have) a degree in economics (VWL — Volkswirtschaftslehre) in Germany, and the real question is: **where and how do you actually work as an economist?** This article is about the economist's world, not business (BWL): research institutes, central banks, ministries, finance and consulting. We'll tell you honestly where your quantitative strength belongs, what to expect on salary, when a PhD is required, and the truth about German.

## Sectors: where do economists work?

An economics degree doesn't lock you into one job. The main lanes are:

- **Research institutes:** **ifo** (Munich), **DIW** (Berlin), **ZEW** (Mannheim), IW, RWI. Macro forecasting, policy evaluation, econometric analysis.
- **Central banks:** the **Bundesbank** and the **ECB** (European Central Bank) in Frankfurt. Monetary policy, financial stability, research.
- **Ministries & policy:** economics/finance ministries, regulators, the Bundeskartellamt (competition authority).
- **Finance:** banks, asset management, insurance (actuary-adjacent roles), risk and macro strategy.
- **Consulting:** economic consulting, competition/regulatory economics.
- **International organisations:** IMF, OECD, World Bank (usually PhD + experience).

**Bold fact:** VWL is strongest in roles that demand *analysis and models* rather than management — you're expected to measure market and policy effects in numbers.

## The data & analytics path: turn quantitative strength into a job

An economics degree gives you econometrics, probability, causal inference and data skills. That is a direct bridge to **data science / analytics**: A/B testing, forecasting models, impact measurement. With Python/R + SQL, an economics graduate is competitive not only for classic economist roles but for data-driven jobs too. Demand for data and tech skills in Germany is high — often the fastest and best-paid exit for an economist.

## Salary: a realistic expectation

Numbers vary a lot by role, sector, city and year. A rough frame:

| Role / sector | Approx. entry (annual, gross) | Note |
|---|---|---|
| General entry (economist/analyst) | **~€45–55k** | sector-dependent |
| Finance / consulting | above, often **€55k+** | bonus possible |
| Research institute / PhD (WiMi) | starts **lower** (partial TV-L) | academic scale |
| Bundesbank / ECB | competitive, institutional | step-based |

**Bold fact:** research and PhD paths pay *less than finance/consulting at first* but open long-term expertise and doors to central banks and international organisations. *As of 2025, approximate; changes yearly, verify for your target role.*

## The PhD reality: common for research & central banks

If your goal is a **research institute, a central bank research department, or academia**, a **PhD is effectively required** most of the time. Germany's strong doctoral programmes are structured: **Bonn (BGSE)** and **Mannheim (CDSE)** are the best known — usually English-taught, international and research-intensive.

- **PhD NEEDED:** top-tier research, central bank research, academic careers, senior international-organisation roles.
- **PhD NOT needed (usually):** finance, consulting, data/analytics, many ministry/policy entry roles, corporate economist positions.

**Bold fact:** a PhD is not a "prestige ornament" — it's the key to specific doors. If research isn't your goal, you can build a strong career without spending 4-5 years on a doctorate.

## The German reality: research in English, policy in German

Internalise this split, because it shapes your strategy:

- **English-friendly:** academic/research output, central bank research, international institute teams, international finance/consulting teams. The *common language of economics research is English*.
- **German required:** **ministries, public policy, regulators** and most local corporate roles. The public sector usually expects **C1 German**, and many postings make it mandatory.

**Bold fact:** if you want to build your career on the policy/public side, German is not a "later" thing — treat it as a planned investment.

## Blue Card & visa (hedged)

If you're non-EU, your work authorisation typically runs on a job offer + qualification. The **EU Blue Card** applies to jobs with a university degree + a certain salary threshold.

- **General salary threshold:** *as of 2025 about €48,300 gross/year (approximate; updated yearly, verify).*
- Economics isn't always on the shortage-occupation (Engpassberuf) list → the general (higher) threshold may apply; it depends on the role.
- Entry salaries (~€45–55k) meet the threshold in some roles and not others; compare the **gross figure in the offer** against the threshold.

**Bold fact:** plan the visa once your salary offer is concrete; the threshold changes yearly — confirm it via an official source (Make it in Germany / the immigration office).

## Conclusion & honest advice

An economist's career in Germany is not one path but a spectrum: research institutes and central banks (PhD + English), policy/ministries (German), finance/consulting (high pay, fast entry) and data/analytics (quantitative strength). Honest advice: **pick the target sector first**, then decide on the PhD and German accordingly. For research/central banks a PhD plus English is enough; for policy/public sector invest in German early; for speed and pay, head toward finance/consulting/data and lean on internships + networking.

Continue with the cluster siblings: [Studying economics (VWL) in Germany](/en/blog/studying-economics-vwl-in-germany-as-a-foreigner-en), [English-taught economics master's without German](/en/blog/english-taught-economics-masters-in-germany-without-german-en) and [What to do with an economics/VWL degree — the job market](/en/blog/what-to-do-with-an-economics-vwl-degree-in-germany-job-market-en). For visa/career: [Work visa with a job offer — the process](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en), [Working in IT/tech in Germany & the Blue Card](/en/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-en) and [How to break into data science/AI](/en/blog/how-to-break-into-data-science-ai-in-germany-en).

*This article was prepared in early 2026. Salaries, the Blue Card threshold, visa rules and institutional policies change yearly — always verify with official sources (Make it in Germany, the relevant institution, the immigration office) before applying.*
MD;

        $variants = [
            'tr' => ['slug'=>'working-as-an-economist-in-germany-research-policy-finance',    'title'=>'Almanya\'da Ekonomist Olarak Çalışmak: Araştırma, Politika, Finans (2026)', 'excerpt'=>'Almanya\'da ekonomist (VWL) kariyeri: araştırma enstitüleri (ifo/DIW/ZEW), merkez bankaları (Bundesbank/ECB), bakanlık/politika, finans, danışmanlık ve veri. Maaş, doktora ve Almanca gerçeği dürüstçe.', 'meta_title'=>'Almanya\'da Ekonomist Olarak Çalışmak (2026)', 'meta_description'=>'Almanya\'da ekonomist kariyeri: araştırma, merkez bankası, politika, finans, veri. Maaş, doktora ve Almanca gerçeği, Blue Card eşiği (hedge\'li).', 'body'=>$trBody],
            'de' => ['slug'=>'working-as-an-economist-in-germany-research-policy-finance-de', 'title'=>'Als Ökonom:in in Deutschland arbeiten: Forschung, Politik, Finanzen (2026)', 'excerpt'=>'Ökonom:innen-Karriere in Deutschland (VWL): Forschungsinstitute (ifo/DIW/ZEW), Zentralbanken (Bundesbank/EZB), Ministerien, Finanzsektor, Beratung und Daten. Ehrlich zu Gehalt, Promotion und Deutsch.', 'meta_title'=>'Als Ökonom:in in Deutschland arbeiten (2026)', 'meta_description'=>'Ökonom:innen-Karriere in Deutschland: Forschung, Zentralbank, Politik, Finanz, Daten. Gehalt, Promotion, Deutsch-Realität und Blue-Card-Schwelle.', 'body'=>$deBody],
            'en' => ['slug'=>'working-as-an-economist-in-germany-research-policy-finance-en', 'title'=>'Working as an Economist in Germany: Research, Policy, Finance (2026)', 'excerpt'=>'An economist\'s (VWL) career in Germany: research institutes (ifo/DIW/ZEW), central banks (Bundesbank/ECB), ministries, finance, consulting and data. Honest on salary, the PhD and German.', 'meta_title'=>'Working as an Economist in Germany (2026)', 'meta_description'=>'Economist careers in Germany: research, central banks, policy, finance, data. Salary, PhD reality, the German-language truth and the Blue Card threshold.', 'body'=>$enBody],
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
            'working-as-an-economist-in-germany-research-policy-finance',
            'working-as-an-economist-in-germany-research-policy-finance-de',
            'working-as-an-economist-in-germany-research-policy-finance-en',
        ])->delete();
    }
};

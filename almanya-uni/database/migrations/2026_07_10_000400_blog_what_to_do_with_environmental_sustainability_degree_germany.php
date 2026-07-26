<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Çevre/Sürdürülebilirlik diplomasıyla iş piyasası (2026). Doğrulandı:
 * disiplinlerarası diploma uzmanlaşma ister (yenilenebilir/ESG/politika/danışmanlık); mezuniyet
 * sonrası 18 ay iş-arama oturumu; Blue Card 2026 genel ~50.700€, darboğaz ~45.934€ (hedge).
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'e5f40000-4444-4c1f-9f20-ee0cff12cc04';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Elinde bir çevre bilimleri veya sürdürülebilirlik diploması var ve soru basit: **Almanya'da bununla ne iş yapılır?** Cevap hem umut verici hem de dürüstlük gerektiriyor. Yeşil sektör büyüyor, ama diploman disiplinlerarası olduğu için tek başına bir "meslek" tanımlamıyor. Bu yazı, diplomanın seni nereye götürdüğünü ve iş piyasasında gerçekçi yolu anlatıyor.

## Disiplinlerarası diploma seni nereye götürür?
Çevre & sürdürülebilirlik diploması doğa bilimi + politika + yönetimi birleştirir. Güçlü yanı: **birçok kapı açar** — enerji, danışmanlık, kurumsal ESG, NGO, kamu, araştırma. Zayıf yanı: hiçbirine "hazır uzman" olarak girmezsin. İşverenler "çevreci" değil, **belirli bir problemi çözen kişi** arar: rüzgar projesini hesaplayan, karbon raporu yazan, atık akışını yöneten biri.

Yani diploma bir **temel**; üzerine ne inşa ettiğin kariyerini belirler. Alanın genel manzarası için [çevre & sürdürülebilirlik okumak rehberimize](/tr/blog/studying-environmental-sciences-and-sustainability-in-germany-as-a-foreigner) göz at.

## Kariyer yolları (tablo)
Aşağıdaki tablo tipik yolları, ortalama giriş maaşını (yaklaşık 2026; brüt/yıl, **doğrula**) ve dil gereksinimini özetler.

| Yol | Örnek roller | Giriş maaşı (yaklaşık) | Dil |
|---|---|---|---|
| Yenilenebilir enerji (teknik) | Proje geliştirici, rüzgar/güneş mühendisliği destek | ~48–58k | Almanca + İngilizce |
| Kurumsal ESG / sürdürülebilirlik | ESG uzmanı, CSR yöneticisi, raporlama | ~45–55k | Almanca artı |
| Çevre danışmanlığı | Danışman, çevre etki analizi (UVP) | ~42–52k | Almanca güçlü |
| NGO / politika | Kampanya, savunuculuk, proje koordinasyon | ~35–45k | Değişken |
| Kamu (Umweltamt) | Uzman memur, ruhsat/denetim | ~40–50k (TVöD) | Almanca C1 |
| Araştırma / akademi | Doktora, enstitü araştırmacısı | ~TV-L E13 (~%65–100) | İngilizce dostu |

**Kalın gerçek:** En yüksek maaş ve en fazla açık pozisyon **yenilenebilir enerji ve teknik ESG** tarafında. NGO/politika tatmin edici ama rekabetçi ve düşük maaşlı olabilir.

## Uzmanlaşma neden şart?
Bu, kümenin en önemli mesajı: **geniş diploma tek başına yeterli değil, uzmanlaşman gerekiyor.** İşverenler dört ayrı dünyaya bakar:

- **Yenilenebilir enerji** → teknik beceri: GIS, enerji modelleme, proje finansmanı.
- **ESG/kurumsal** → raporlama standartları (CSRD, GRI), Excel/veri, iş dili.
- **Politika/danışmanlık** → mevzuat bilgisi, analiz, yazma.
- **Araştırma** → istatistik, saha/laboratuvar, yayın.

Master seçimi, staj ve ilk iş bu dallardan **birini** seçmekle ilgili. "Her şeyi biraz bilen" profil, "bir şeyi iyi bilen" adaya kaybeder. Yeşil kariyerlerin sektör kırılımı için [sürdürülebilirlik & yenilenebilir enerjide çalışmak yazımıza](/tr/blog/green-careers-working-in-sustainability-and-renewable-energy-in-germany) bak. Aynı "disiplinlerarası ama uzmanlaş" mantığı [doğa bilimleri diplomasında endüstri kariyerleri](/tr/blog/what-to-do-with-a-science-degree-in-germany-industry-careers) için de geçerli.

## Mezuniyet sonrası 18 ay iş-arama oturumu
Alman üniversitesinden mezun olan uluslararası öğrenci, **oturma iznini iş aramak için 18 aya kadar uzatabilir**. Bu süre kariyerinin en kritik penceresi: nitelik seviyene uygun herhangi bir işte çalışabilirsin ve doğru teklifi bulunca Blue Card / çalışma iznine geçersin.

Bu 18 ayı boşa harcama:
- Mezuniyetten **önce** staj ve Werkstudent tecrübesi topla.
- İlk aydan başvur; yaz aylarını bekleme.
- Almanca'yı bu dönemde B2/C1'e taşı.

İngilizce master ile geldiysen yol farkını [Almancasız İngilizce master yazımızda](/tr/blog/english-taught-environmental-and-sustainability-masters-in-germany) bulabilirsin. Vize/oturum stratejisini net görmek istersen [master vs iş arama vizesi karşılaştırmamız](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) iki anahtar yolu ayırıyor.

## Almanca + strateji
Araştırma ve uluslararası kuruluşlar İngilizce dostu; ama **kurumsal ESG, danışmanlık ve kamu için Almanca neredeyse şart**. Gerçekçi strateji:
1. **Almanca** en az B2, tercihen C1 — kamu ve danışmanlıkta pazarlık dışı.
2. **Blue Card**: 2026 genel maaş eşiği **~50.700€/yıl**; darboğaz/yeni-mezun mesleklerde **~45.934€/yıl** (her ikisi de yaklaşık, resmi kaynaktan doğrula).
3. Öğrenciyken **Sperrkonto** ~992€/ay ≈ **~11.904€/yıl** (2025/2026, doğrula).

Blue Card daha çok MINT/teknik rollerde (yenilenebilir enerji mühendisliği gibi) rahat karşılanır; saf politika/NGO rollerinde eşik daha zorlayıcı olabilir.

## Uluslararası öğrenci için gerçekçi yol
Somut bir plan:
1. **Master'da bir dal seç** (yenilenebilir / ESG / politika / araştırma).
2. **Werkstudent + staj** ile o dalda 6–12 ay tecrübe.
3. **Almanca B2/C1**'i mezuniyete kadar tamamla.
4. Mezuniyette **18 aylık iş-arama** penceresini erken kullan.
5. İlk teklifte **Blue Card eşiğini** kontrol et; teknik/enerji rolleri eşiği daha rahat aşar.

Okulun "prestiji" iş bulmada Türkiye'deki kadar belirleyici değil; program uyumu ve ağ daha önemli. Bu konuyu [Almanya'da üniversite prestiji ve sıralama yazımızda](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) açıkladık.

## Sonuç & dürüst tavsiye
Çevre/sürdürülebilirlik diploması Almanya'da **büyüyen bir sektöre** açılıyor — ama diploma seni otomatik olarak işe sokmuyor. Kazanan formül: **erken uzmanlaş + pratik tecrübe + Almanca + 18 aylık pencereyi akıllı kullan.** Yenilenebilir enerji ve teknik ESG en çok açık ve en iyi maaşı sunar; NGO/politika anlamlı ama rekabetçi ve düşük maaşlıdır. Diplomanı bir kimlik değil, üzerine uzmanlık inşa edeceğin bir platform olarak gör.

*Bu yazı 2026 başındaki bilgilere dayanır ve genel bilgilendirme amaçlıdır; maaşlar, Blue Card eşikleri (~50.700€ / ~45.934€), Sperrkonto tutarı ve vize kuralları değişebilir. Karar öncesi resmi kaynaklardan (ilgili üniversite, DAAD, Ausländerbehörde, Bundesagentur für Arbeit) doğrulayın.*
MD;
        $deBody = <<<'MD'
Du hast einen Abschluss in Umweltwissenschaften oder Nachhaltigkeit und die Frage ist einfach: **Was kannst du damit in Deutschland arbeiten?** Die Antwort ist hoffnungsvoll, aber ehrlich. Die grüne Branche wächst, doch weil dein Abschluss interdisziplinär ist, definiert er allein keinen festen Beruf. Dieser Beitrag zeigt, wohin dich der Abschluss führt und wie der realistische Weg auf dem Arbeitsmarkt aussieht.

## Wohin führt dich ein interdisziplinärer Abschluss?
Ein Umwelt- & Nachhaltigkeitsabschluss verbindet Naturwissenschaft + Politik + Management. Stärke: **er öffnet viele Türen** — Energie, Beratung, Corporate ESG, NGO, öffentlicher Dienst, Forschung. Schwäche: In keine dieser Türen trittst du als fertiger Spezialist ein. Arbeitgeber suchen keine "Umweltfreunde", sondern **Menschen, die ein konkretes Problem lösen**: das Windprojekt rechnen, den Klimabericht schreiben, den Stoffstrom steuern.

Der Abschluss ist also ein **Fundament**; was du darauf baust, entscheidet deine Karriere. Den Überblick über das Fach findest du in unserem [Leitfaden zum Studium der Umweltwissenschaften & Nachhaltigkeit](/de/blog/studying-environmental-sciences-and-sustainability-in-germany-as-a-foreigner-de).

## Karrierewege (Tabelle)
Die Tabelle fasst typische Wege, das ungefähre Einstiegsgehalt (ca. 2026; brutto/Jahr, **prüfen**) und die Sprachanforderung zusammen.

| Weg | Beispielrollen | Einstiegsgehalt (ca.) | Sprache |
|---|---|---|---|
| Erneuerbare Energie (technisch) | Projektentwickler, Wind/Solar-Engineering | ~48–58k | Deutsch + Englisch |
| Corporate ESG / Nachhaltigkeit | ESG-Referent, CSR-Manager, Reporting | ~45–55k | Deutsch von Vorteil |
| Umweltberatung | Consultant, UVP/Gutachten | ~42–52k | Deutsch stark |
| NGO / Politik | Kampagne, Advocacy, Projektkoordination | ~35–45k | Variabel |
| Öffentlicher Dienst (Umweltamt) | Sachbearbeiter, Genehmigung/Aufsicht | ~40–50k (TVöD) | Deutsch C1 |
| Forschung / Wissenschaft | Promotion, Institutsforscher | ~TV-L E13 (~65–100%) | Englisch-freundlich |

**Fettgedruckte Wahrheit:** Das höchste Gehalt und die meisten offenen Stellen liegen bei **erneuerbarer Energie und technischem ESG**. NGO/Politik ist erfüllend, aber wettbewerbsintensiv und oft niedrig bezahlt.

## Warum Spezialisierung Pflicht ist
Das ist die wichtigste Botschaft des Clusters: **Der breite Abschluss allein reicht nicht — du musst dich spezialisieren.** Arbeitgeber blicken auf vier getrennte Welten:

- **Erneuerbare Energie** → technische Skills: GIS, Energiemodellierung, Projektfinanzierung.
- **ESG/Corporate** → Reporting-Standards (CSRD, GRI), Excel/Daten, Business-Sprache.
- **Politik/Beratung** → Regulierungswissen, Analyse, Schreiben.
- **Forschung** → Statistik, Feld/Labor, Publikationen.

Masterwahl, Praktikum und erster Job bedeuten, **einen** dieser Zweige zu wählen. Das Profil "von allem ein bisschen" verliert gegen die Kandidatin "eine Sache richtig gut". Die Branchenaufteilung grüner Karrieren findest du in unserem [Beitrag zum Arbeiten in Nachhaltigkeit & erneuerbarer Energie](/de/blog/green-careers-working-in-sustainability-and-renewable-energy-in-germany-de). Dieselbe Logik "interdisziplinär, aber spezialisieren" gilt auch für [Industriekarrieren mit einem naturwissenschaftlichen Abschluss](/de/blog/what-to-do-with-a-science-degree-in-germany-industry-careers-de).

## Die 18-monatige Jobsuche nach dem Abschluss
Wer an einer deutschen Hochschule abschließt, kann die **Aufenthaltserlaubnis zur Arbeitssuche auf bis zu 18 Monate** verlängern. Dieses Fenster ist das kritischste deiner Karriere: Du darfst in dieser Zeit jede Beschäftigung annehmen und wechselst bei einem passenden Angebot auf die Blaue Karte / Arbeitserlaubnis.

Verschwende diese 18 Monate nicht:
- Sammle **vor** dem Abschluss Praktikums- und Werkstudentenerfahrung.
- Bewirb dich ab dem ersten Monat; warte nicht auf den Sommer.
- Bring dein Deutsch in dieser Phase auf B2/C1.

Bist du mit einem englischsprachigen Master gekommen, findest du den Unterschied in unserem [Beitrag zu englischsprachigen Mastern ohne Deutsch](/de/blog/english-taught-environmental-and-sustainability-masters-in-germany-de). Für die Visa-Strategie trennt unser [Vergleich Master vs. Job-Seeker-Visum](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de) die zwei Schlüsselwege.

## Deutsch + Strategie
Forschung und internationale Organisationen sind englischfreundlich; aber für **Corporate ESG, Beratung und öffentlichen Dienst ist Deutsch fast Pflicht**. Realistische Strategie:
1. **Deutsch** mindestens B2, besser C1 — im öffentlichen Dienst und in der Beratung nicht verhandelbar.
2. **Blaue Karte**: allgemeine Gehaltsschwelle 2026 **~50.700€/Jahr**; in Engpass-/Berufsanfängerberufen **~45.934€/Jahr** (beides ca., aus offizieller Quelle prüfen).
3. Als Studierende(r) **Sperrkonto** ~992€/Monat ≈ **~11.904€/Jahr** (2025/2026, prüfen).

Die Blaue Karte wird eher in MINT/technischen Rollen (wie Windenergie-Engineering) leicht erreicht; in reinen Politik-/NGO-Rollen kann die Schwelle anspruchsvoller sein.

## Der realistische Weg für internationale Studierende
Ein konkreter Plan:
1. **Wähle im Master einen Zweig** (erneuerbar / ESG / Politik / Forschung).
2. **Werkstudent + Praktikum**: 6–12 Monate Erfahrung in diesem Zweig.
3. **Deutsch B2/C1** bis zum Abschluss abschließen.
4. Beim Abschluss das **18-Monats-Fenster** früh nutzen.
5. Beim ersten Angebot die **Blue-Card-Schwelle** prüfen; technische/Energierollen überschreiten sie leichter.

Das "Prestige" der Hochschule ist bei der Jobsuche weniger entscheidend, als du denkst; Programmpassung und Netzwerk zählen mehr. Das erklären wir in unserem [Beitrag zu Prestige & Rankings deutscher Unis](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de).

## Fazit & ehrlicher Rat
Ein Umwelt-/Nachhaltigkeitsabschluss öffnet in Deutschland eine **wachsende Branche** — aber der Abschluss bringt dich nicht automatisch in den Job. Die Gewinnerformel: **früh spezialisieren + Praxiserfahrung + Deutsch + das 18-Monats-Fenster klug nutzen.** Erneuerbare Energie und technisches ESG bieten die meisten Stellen und die besten Gehälter; NGO/Politik ist sinnstiftend, aber wettbewerbsintensiv und niedrig bezahlt. Sieh deinen Abschluss nicht als Identität, sondern als Plattform, auf der du Spezialisierung aufbaust.

*Dieser Beitrag beruht auf dem Stand Anfang 2026 und dient der allgemeinen Information; Gehälter, Blue-Card-Schwellen (~50.700€ / ~45.934€), Sperrkonto-Betrag und Visaregeln können sich ändern. Prüfe vor einer Entscheidung offizielle Quellen (jeweilige Hochschule, DAAD, Ausländerbehörde, Bundesagentur für Arbeit).*
MD;
        $enBody = <<<'MD'
You hold a degree in environmental science or sustainability, and the question is simple: **what can you actually do with it in Germany?** The answer is both hopeful and honest. The green sector is growing, but because your degree is interdisciplinary, it doesn't define a single job on its own. This post explains where the degree takes you and what the realistic path on the job market looks like.

## Where does an interdisciplinary degree take you?
An environmental & sustainability degree blends natural science + policy + management. Its strength: **it opens many doors** — energy, consulting, corporate ESG, NGOs, public sector, research. Its weakness: you don't walk through any of them as a finished specialist. Employers aren't looking for "environmentalists" but for **someone who solves a specific problem**: sizing the wind project, writing the carbon report, managing the material stream.

So the degree is a **foundation**; what you build on it decides your career. For the big picture of the field, see our [guide to studying environmental science & sustainability](/en/blog/studying-environmental-sciences-and-sustainability-in-germany-as-a-foreigner-en).

## Career paths (table)
The table below summarizes typical paths, the approximate entry salary (approx. 2026; gross/year, **verify**), and the language requirement.

| Path | Example roles | Entry salary (approx.) | Language |
|---|---|---|---|
| Renewable energy (technical) | Project developer, wind/solar engineering support | ~48–58k | German + English |
| Corporate ESG / sustainability | ESG specialist, CSR manager, reporting | ~45–55k | German a plus |
| Environmental consulting | Consultant, environmental impact (EIA) | ~42–52k | Strong German |
| NGO / policy | Campaigning, advocacy, project coordination | ~35–45k | Variable |
| Public sector (Umweltamt) | Case officer, permits/inspection | ~40–50k (TVöD) | German C1 |
| Research / academia | PhD, institute researcher | ~TV-L E13 (~65–100%) | English-friendly |

**Bold truth:** The highest salaries and the most open positions are on the **renewable energy and technical ESG** side. NGO/policy is fulfilling but competitive and often low-paid.

## Why specialization is mandatory
This is the cluster's most important message: **the broad degree alone is not enough — you must specialize.** Employers look at four separate worlds:

- **Renewable energy** → technical skills: GIS, energy modeling, project finance.
- **ESG/corporate** → reporting standards (CSRD, GRI), Excel/data, business language.
- **Policy/consulting** → regulatory knowledge, analysis, writing.
- **Research** → statistics, field/lab, publications.

Choosing a master, an internship, and a first job means picking **one** of these branches. The "a bit of everything" profile loses to the candidate who knows "one thing well". For the sector breakdown of green careers, see our [post on working in sustainability & renewable energy](/en/blog/green-careers-working-in-sustainability-and-renewable-energy-in-germany-en). The same "interdisciplinary but specialize" logic also applies to [industry careers with a science degree](/en/blog/what-to-do-with-a-science-degree-in-germany-industry-careers-en).

## The 18-month job search after graduation
If you graduate from a German university, you can extend your **residence permit to look for work for up to 18 months**. This window is the most critical of your career: during it you may take any job matching your qualification level, and once you find the right offer you switch to the EU Blue Card / work permit.

Don't waste these 18 months:
- Gather internship and Werkstudent experience **before** graduating.
- Apply from the first month; don't wait for summer.
- Push your German to B2/C1 during this phase.

If you came with an English-taught master, find the difference in our [post on English-taught masters without German](/en/blog/english-taught-environmental-and-sustainability-masters-in-germany-en). For the visa strategy, our [Master vs. Job-Seeker visa comparison](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en) separates the two key routes.

## German + strategy
Research and international organizations are English-friendly; but for **corporate ESG, consulting, and the public sector, German is almost mandatory**. A realistic strategy:
1. **German** at least B2, ideally C1 — non-negotiable in the public sector and consulting.
2. **Blue Card**: 2026 general salary threshold **~€50,700/year**; in bottleneck/new-graduate professions **~€45,934/year** (both approximate, verify from an official source).
3. As a student, the **Sperrkonto** is ~€992/month ≈ **~€11,904/year** (2025/2026, verify).

The Blue Card is met more easily in STEM/technical roles (like wind-energy engineering); in pure policy/NGO roles the threshold can be more demanding.

## The realistic path for international students
A concrete plan:
1. **Choose a branch in your master** (renewable / ESG / policy / research).
2. **Werkstudent + internship**: 6–12 months of experience in that branch.
3. Complete **German B2/C1** by graduation.
4. At graduation, use the **18-month window** early.
5. On your first offer, check the **Blue Card threshold**; technical/energy roles clear it more easily.

The "prestige" of your university matters less in the job hunt than you might think; program fit and network count more. We explain this in our [post on prestige & rankings of German universities](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en).

## Conclusion & honest advice
An environmental/sustainability degree opens onto a **growing sector** in Germany — but the degree does not automatically put you in a job. The winning formula: **specialize early + practical experience + German + use the 18-month window wisely.** Renewable energy and technical ESG offer the most openings and the best pay; NGO/policy is meaningful but competitive and low-paid. See your degree not as an identity but as a platform on which you build specialization.

*This post reflects information as of early 2026 and is for general guidance only; salaries, Blue Card thresholds (~€50,700 / ~€45,934), the Sperrkonto amount, and visa rules can change. Before deciding, verify with official sources (the relevant university, DAAD, the Ausländerbehörde, the Bundesagentur für Arbeit).*
MD;

        $variants = [
            'tr' => ['slug'=>'what-to-do-with-an-environmental-sustainability-degree-in-germany-job-market',    'title'=>'Almanya\'da Çevre/Sürdürülebilirlik Diplomasıyla Ne Yapılır? İş Piyasası (2026)', 'excerpt'=>'Çevre & sürdürülebilirlik diploması Almanya\'da hangi kariyer yollarına açılır? Kariyer tablosu, neden uzmanlaşman şart, mezuniyet sonrası 18 aylık iş-arama penceresi, Almanca ve Blue Card (2026) gerçeği.', 'meta_title'=>'Çevre/Sürdürülebilirlik Diplomasıyla Ne Yapılır? Almanya (2026)', 'meta_description'=>'Almanya\'da çevre & sürdürülebilirlik diplomasıyla iş piyasası: kariyer yolları, uzmanlaşma, 18 aylık iş-arama, Almanca ve Blue Card 2026 rakamları.', 'body'=>$trBody],
            'de' => ['slug'=>'what-to-do-with-an-environmental-sustainability-degree-in-germany-job-market-de', 'title'=>'Was tun mit einem Umwelt-/Nachhaltigkeitsabschluss in Deutschland? Arbeitsmarkt (2026)', 'excerpt'=>'Welche Karrierewege öffnet ein Umwelt- & Nachhaltigkeitsabschluss in Deutschland? Karrieretabelle, warum Spezialisierung Pflicht ist, das 18-monatige Jobsuche-Fenster, Deutsch und Blue Card (2026).', 'meta_title'=>'Umwelt-/Nachhaltigkeitsabschluss: Arbeitsmarkt Deutschland (2026)', 'meta_description'=>'Arbeitsmarkt mit Umwelt- & Nachhaltigkeitsabschluss in Deutschland: Karrierewege, Spezialisierung, 18-Monats-Jobsuche, Deutsch und Blue-Card-Zahlen 2026.', 'body'=>$deBody],
            'en' => ['slug'=>'what-to-do-with-an-environmental-sustainability-degree-in-germany-job-market-en', 'title'=>'What To Do With an Environmental / Sustainability Degree in Germany? Job Market (2026)', 'excerpt'=>'Which career paths does an environmental & sustainability degree open in Germany? A career table, why you must specialize, the 18-month post-graduation job-search window, German, and the Blue Card (2026) reality.', 'meta_title'=>'Environmental / Sustainability Degree: Germany Job Market (2026)', 'meta_description'=>'Job market with an environmental & sustainability degree in Germany: career paths, specialization, the 18-month job search, German, and Blue Card 2026 figures.', 'body'=>$enBody],
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
            'what-to-do-with-an-environmental-sustainability-degree-in-germany-job-market',
            'what-to-do-with-an-environmental-sustainability-degree-in-germany-job-market-de',
            'what-to-do-with-an-environmental-sustainability-degree-in-germany-job-market-en',
        ])->delete();
    }
};

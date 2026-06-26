<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Working in IT/tech in Germany as a foreigner — Blue Card, routes & salary.
 * Verified: IT = #1 Engpassberuf; EU Blue Card 2025 thresholds (general ~€48,300 / shortage+Berufsanfänger ~€43,759.80);
 * Nov 2023 Skilled Immigration Act lets IT specialists without a degree qualify with ≥3 yrs experience; PR after 27/21 months;
 * Chancenkarte (June 2024) + 6-month job-seeker visa; salary bands 2025 (junior/mid/senior). All thresholds rise yearly.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'b7d1e2a0-3333-4c8a-9f30-aa01bb02cc03';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da yazılımcı ya da IT uzmanı olarak çalışmak istiyorsan, kötü haberi en başta vermeyelim: aslında elinde güçlü bir koz var. Almanya **bilişim uzmanı sıkıntısı** yaşıyor ve bu, yabancı bir IT'ci olarak senin lehine işliyor. Bu yazı, vize rotalarını, EU Blue Card eşiklerini ve gerçek maaşları dürüstçe anlatıyor.

## IT, Almanya'nın 1 numaralı darboğaz mesleği (Engpassberuf)

Almanya'da **bilişim/IT, açık ara en çok eleman aranan "darboğaz meslek" (Engpassberuf) listesinin başında.** Açık pozisyon sayısı on binlerle ifade ediliyor ve yerli arz talebi karşılayamıyor.

Bunun senin için anlamı net: yabancı bir yazılımcı olarak **işverenler ve göçmenlik sistemi sana kapıyı daha geniş açıyor.** Darboğaz mesleklerde maaş eşikleri daha düşük, başvuru süreçleri daha hızlı, hatta diplomasız rotalar bile var. Bu avantajı bilmek, doğru kararı vermenin yarısı.

## EU Blue Card (Blaue Karte EU): ana rota

Almanya'da bir iş teklifin varsa, yüksek nitelikli olarak çalışmanın ana yolu **EU Blue Card (Blaue Karte EU)**. Şartı basit: nitelikli bir işin + belirli bir **brüt maaş eşiğini** geçen bir teklif.

Burada kritik nokta şu: IT bir darboğaz mesleği olduğu için **daha düşük (indirimli) eşik** geçerli. Aynı şey son 3 yıl içinde mezun olmuş yeni mezunlar (Berufsanfänger) için de geçerli.

| Kategori (2025) | Yıllık brüt maaş eşiği (yaklaşık) |
|---|---|
| Genel eşik | ~€48.300 |
| Darboğaz meslek (IT dâhil) + yeni mezun (Berufsanfänger) | ~€43.759,80 |

> **2025 itibarıyla; eşik yıllık güncellenir, başvurudan önce doğrula.** Karşılaştırma için: 2024'te bu rakamlar €45.300 / €41.041,80 idi — yani her yıl yukarı çıkıyor. Sayıyı asla kalıcı kabul etme.

İyi haber: **giriş seviyesi (junior) bir IT maaşı bile genellikle darboğaz eşiğini rahatça geçiyor.** Yani teklifin varsa Blue Card çoğu zaman ulaşılabilir.

## Diploması olmayan IT uzmanları için büyük fırsat (Kasım 2023 reformu)

Bu, kendi kendine öğrenen (self-taught) ve bootcamp mezunu yazılımcılar için oyunu değiştiren madde. **Kasım 2023 Nitelikli Göç Yasası (Fachkräfteeinwanderungsgesetz) reformuyla**, **üniversite diploması OLMAYAN IT uzmanları da Blue Card alabiliyor.**

Şartlar:

- Son **7 yıl içinde en az 3 yıl** ilgili (IT alanında) **profesyonel iş deneyimi**, ve
- Darboğaz maaş eşiğini karşılayan **nitelikli bir iş teklifi**.

Resmi bir diploma şartı yok. Yani kodu işte/projelerde öğrenmiş, deneyimi olan ama üniversite bitirmemiş biri için bile bu kapı açık. Bootcamp + birkaç yıl gerçek iş deneyimi olan biri için bu, en somut yol.

## Blue Card'ın avantajları

Blue Card sadece çalışma izni değil; uzun vadeli ciddi avantajlar getiriyor:

- **Hızlı süresiz oturum (Niederlassungserlaubnis):** Blue Card ile **27 ay** sonra, Almanca **B1** seviyen varsa **21 ay** sonra süresiz oturuma geçebiliyorsun.
- **Kolay aile birleşimi:** Eşin Almanca şartı olmadan gelebiliyor ve doğrudan çalışma izni alıyor.
- **AB içi hareketlilik:** Belirli koşullarda başka AB ülkelerine geçiş daha kolay.

Bu üç madde, Blue Card'ı klasik çalışma izninden ayıran en güçlü taraf.

## Henüz işin yokken giriş rotaları

Önce işi sonra ülkeyi mi bekliyorsun? Henüz iş teklifin yoksa Almanya'ya girmenin yolları var:

- **Chancenkarte (Fırsat Kartı):** Haziran 2024'ten beri geçerli, **puan bazlı** bir kart. İş teklifi olmadan Almanya'ya gelip yerinde iş aramana izin veriyor (nitelik, deneyim, yaş, dil puanlanıyor).
- **6 aylık iş arama vizesi (Job-seeker visa):** Yurt dışından, nitelikli iş aramak için 6 aya kadar gelebiliyorsun.

İkisini ve master rotasıyla farkını ayrıntılı karşılaştıran yazı: [Almanya master mı, iş arama vizesi mi?](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career).

## İşte İngilizce mi, Almanca mı?

En çok merak edilen soru. Gerçek şu: **İngilizce-only işler Berlin'de, startup'larda ve büyük tech şirketlerinde yoğunlaşıyor** — SAP, Zalando, N26, Delivery Hero, Trade Republic gibi. Buralarda Almanca olmadan kariyer mümkün.

Ama Berlin dışına, **Mittelstand'a (orta ölçekli aile şirketleri) ve otomotiv sektörüne** çıktığında **Almanca devasa fark yaratıyor.** Günlük iletişim, toplantılar, terfi — hepsi Almanca yürüyor. İngilizceyle girer, Almancayla yükselirsin.

Dilin iş hayatındaki gerçek rolünü dürüstçe anlatan yazı: [İş için Almanca: dürüst gerçek](/tr/blog/german-language-reality-for-jobs-in-germany-the-honest-truth).

## Maaş gerçeği (2025, brüt)

Brüt rakamlardan **vergi ve sosyal kesinti olarak kabaca %35–42 gidiyor** — net hesabını buna göre yap. 2025 itibarıyla yazılım geliştirici maaş bandları yaklaşık:

| Seviye | Yıllık brüt (2025) |
|---|---|
| Junior | ~€45.000 – €55.000 |
| Mid-level | ~€60.000 – €75.000 |
| Senior | ~€80.000 – €100.000+ |

Münih ve Frankfurt genel olarak daha yüksek (ama yaşam maliyeti de öyle). Önemli not: **giriş seviyesi bir IT maaşı bile çoğu zaman Blue Card darboğaz eşiğini rahatça geçiyor** — yani vize tarafında IT'ciler şanslı.

> Rakamlar 2025 brüt tahminidir; şirkete, şehre ve role göre değişir, başvurudan önce güncel verilerle doğrula.

## Sonuç ve dürüst tavsiye

- IT **Almanya'nın 1 numaralı darboğaz mesleği** — yabancı bir yazılımcı olarak elinde güçlü bir koz var.
- Ana rota **EU Blue Card**; IT için **indirimli eşik** geçerli ve junior maaş bile genelde bunu geçiyor.
- **Diploman yoksa bile**, son 7 yılda 3 yıl deneyim + uygun teklifle Blue Card alabilirsin (Kasım 2023 reformu).
- İşin yoksa **Chancenkarte** veya **iş arama vizesiyle** gelip yerinde ara.
- İngilizceyle başla, ama **Almanca öğrenmek kariyerini katlar** (özellikle Berlin dışında).

Devamı için: [Almanya'da yabancı olarak bilgisayar mühendisliği/Informatik okumak](/tr/blog/studying-computer-science-informatik-in-germany-as-a-foreigner) ve [Almanya'da bilgisayar mühendisliği diplomasıyla ne yapılır? (iş piyasası & maaş)](/tr/blog/what-to-do-with-a-computer-science-degree-in-germany-job-market-salary). İş teklifiyle vize süreci için: [İş teklifiyle çalışma vizesi](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track). Öğrenciysen mezuniyet sonrası geçiş: [Öğrenci vizesinden çalışma iznine (Zweckwechsel)](/tr/blog/changing-student-visa-to-work-permit-germany-zweckwechsel).

*Bu yazı 2026 başı itibarıyla geçerli kurallar ve eşiklere göre hazırlanmıştır. Vize ve maaş eşikleri yıllık güncellenir; başvurudan önce mutlaka resmi kaynaklardan (Make it in Germany, ABH, BAMF) güncel rakamları doğrula.*
MD;

        $deBody = <<<'MD'
Wenn du als Softwareentwickler:in oder IT-Fachkraft in Deutschland arbeiten willst, hast du einen starken Trumpf in der Hand: Deutschland hat einen **massiven Fachkräftemangel in der IT** – und das spielt dir als Ausländer:in in die Karten. Dieser Artikel erklärt ehrlich die Visa-Wege, die Blue-Card-Schwellen und die echten Gehälter.

## IT ist Deutschlands Engpassberuf Nr. 1

In Deutschland steht **IT/Informatik ganz oben auf der Liste der Engpassberufe** – Berufe, in denen offene Stellen kaum besetzt werden können. Zehntausende offene Stellen, und der heimische Nachwuchs reicht bei Weitem nicht.

Für dich heißt das: Als ausländische IT-Fachkraft öffnet dir das System die Tür weiter. **In Engpassberufen gelten niedrigere Gehaltsschwellen, schnellere Verfahren – und es gibt sogar Wege ohne Studienabschluss.** Diesen Vorteil zu kennen, ist die halbe Miete.

## EU Blue Card (Blaue Karte EU): der Hauptweg

Wenn du ein Jobangebot in Deutschland hast, ist die **Blaue Karte EU** der Hauptweg für qualifizierte Arbeit. Die Bedingung: ein qualifizierter Job + ein Angebot über einer bestimmten **Brutto-Gehaltsschwelle**.

Entscheidend: Weil IT ein Engpassberuf ist, gilt die **niedrigere (reduzierte) Schwelle**. Dasselbe gilt für Berufsanfänger:innen, die in den letzten 3 Jahren ihren Abschluss gemacht haben.

| Kategorie (2025) | Jährliche Brutto-Gehaltsschwelle (ca.) |
|---|---|
| Allgemeine Schwelle | ~48.300 € |
| Engpassberuf (inkl. IT) + Berufsanfänger | ~43.759,80 € |

> **Stand 2025; die Schwelle wird jährlich angepasst, vor dem Antrag prüfen.** Zum Vergleich: 2024 lagen die Werte bei 45.300 € / 41.041,80 € – sie steigen also jedes Jahr. Betrachte keine Zahl als dauerhaft.

Gute Nachricht: **Schon ein Einstiegsgehalt (Junior) in der IT liegt meist locker über der Engpass-Schwelle.** Mit einem Angebot ist die Blue Card also oft erreichbar.

## Der große Vorteil für IT-Fachkräfte ohne Studium (Reform Nov. 2023)

Das ist der Game-Changer für Self-taught-Entwickler:innen und Bootcamp-Absolvent:innen. Mit der **Reform des Fachkräfteeinwanderungsgesetzes im November 2023** können **auch IT-Fachkräfte OHNE Hochschulabschluss eine Blue Card bekommen.**

Voraussetzungen:

- Mindestens **3 Jahre einschlägige Berufserfahrung in den letzten 7 Jahren** (im IT-Bereich), und
- ein **qualifiziertes Jobangebot**, das die Engpass-Schwelle erfüllt.

Kein formaler Abschluss nötig. Wer also im Job oder in Projekten programmieren gelernt hat und Erfahrung mitbringt, aber kein Studium hat, kann diesen Weg gehen. Für jemanden mit Bootcamp + ein paar Jahren echter Berufserfahrung ist das der konkreteste Weg.

## Vorteile der Blue Card

Die Blue Card ist mehr als nur eine Arbeitserlaubnis:

- **Schnelle Niederlassungserlaubnis:** Mit Blue Card nach **27 Monaten**, mit Deutsch auf **B1**-Niveau schon nach **21 Monaten** unbefristeter Aufenthalt.
- **Einfacher Familiennachzug:** Dein:e Partner:in kann ohne Deutschnachweis nachkommen und direkt arbeiten.
- **EU-Mobilität:** Unter bestimmten Bedingungen leichterer Wechsel in andere EU-Länder.

Diese drei Punkte machen die Blue Card stärker als eine klassische Arbeitserlaubnis.

## Einreisewege, wenn du noch keinen Job hast

Noch kein Jobangebot? Es gibt Wege, trotzdem nach Deutschland zu kommen:

- **Chancenkarte:** Seit Juni 2024, eine **punktebasierte** Karte. Du kannst ohne Jobangebot einreisen und vor Ort suchen (Qualifikation, Erfahrung, Alter, Sprache werden bepunktet).
- **6-monatiges Job-Seeker-Visum:** Aus dem Ausland kannst du bis zu 6 Monate zur qualifizierten Jobsuche einreisen.

Beide Wege und der Unterschied zum Master-Weg im Detail: [Deutschland: Master oder Job-Seeker-Visum?](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).

## Englisch oder Deutsch im Job?

Die häufigste Frage. Die Wahrheit: **Englisch-only-Jobs konzentrieren sich in Berlin, bei Startups und großen Tech-Firmen** – SAP, Zalando, N26, Delivery Hero, Trade Republic. Dort ist eine Karriere ohne Deutsch möglich.

Sobald du aber Berlin verlässt, in den **Mittelstand und die Automobilbranche**, macht **Deutsch einen riesigen Unterschied.** Alltag, Meetings, Beförderungen laufen auf Deutsch. Du kommst mit Englisch rein – und steigst mit Deutsch auf.

Die ehrliche Rolle der Sprache im Berufsleben: [Deutsch für den Job: die ehrliche Wahrheit](/de/blog/german-language-reality-for-jobs-in-germany-the-honest-truth-de).

## Die Gehaltsrealität (2025, brutto)

Vom Brutto gehen grob **35–42 % an Steuern und Sozialabgaben** – rechne dein Netto entsprechend. Ungefähre Gehaltsbänder für Softwareentwickler:innen, Stand 2025:

| Level | Jahresgehalt brutto (2025) |
|---|---|
| Junior | ~45.000 € – 55.000 € |
| Mid-level | ~60.000 € – 75.000 € |
| Senior | ~80.000 € – 100.000 €+ |

München und Frankfurt liegen meist höher (aber die Lebenshaltungskosten auch). Wichtig: **Schon ein Einstiegsgehalt in der IT liegt meist locker über der Blue-Card-Engpass-Schwelle** – beim Visum haben IT-Leute also Glück.

> Die Zahlen sind Brutto-Schätzwerte für 2025; sie variieren je nach Firma, Stadt und Rolle – vor dem Antrag mit aktuellen Daten prüfen.

## Fazit & ehrlicher Rat

- IT ist **Deutschlands Engpassberuf Nr. 1** – als ausländische:r Entwickler:in hast du einen starken Trumpf.
- Hauptweg ist die **Blaue Karte EU**; für IT gilt die **reduzierte Schwelle**, und schon Junior-Gehälter liegen meist darüber.
- **Auch ohne Abschluss** kannst du mit 3 Jahren Erfahrung in den letzten 7 Jahren + passendem Angebot eine Blue Card bekommen (Reform Nov. 2023).
- Ohne Job: per **Chancenkarte** oder **Job-Seeker-Visum** einreisen und vor Ort suchen.
- Starte mit Englisch, aber **Deutsch vervielfacht deine Karriere** (besonders außerhalb Berlins).

Mehr dazu: [Informatik in Deutschland als Ausländer:in studieren](/de/blog/studying-computer-science-informatik-in-germany-as-a-foreigner-de) und [Was mache ich mit einem Informatik-Abschluss in Deutschland? (Arbeitsmarkt & Gehalt)](/de/blog/what-to-do-with-a-computer-science-degree-in-germany-job-market-salary-de). Zum Visumsprozess mit Jobangebot: [Arbeitsvisum mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de). Als Student:in der Wechsel nach dem Abschluss: [Vom Studenten- zum Arbeitsvisum (Zweckwechsel)](/de/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-de).

*Dieser Artikel basiert auf den Anfang 2026 geltenden Regeln und Schwellen. Visa- und Gehaltsschwellen werden jährlich angepasst; prüfe vor dem Antrag unbedingt die aktuellen Zahlen aus offiziellen Quellen (Make it in Germany, ABH, BAMF).*
MD;

        $enBody = <<<'MD'
If you want to work as a software developer or IT specialist in Germany, you're holding a strong card: Germany has a **massive shortage of IT specialists** — and as a foreigner, that works in your favour. This article gives you the honest version of the visa routes, the Blue Card thresholds and the real salaries.

## IT is Germany's #1 shortage occupation (Engpassberuf)

In Germany, **IT/computer science sits at the very top of the shortage-occupation (Engpassberuf) list** — jobs that simply can't be filled fast enough. Tens of thousands of open positions, and the domestic supply falls far short.

What this means for you: as a foreign IT specialist, **the system opens the door wider.** Shortage occupations get **lower salary thresholds, faster procedures — and even routes without a university degree.** Knowing this leverage is half the battle.

## EU Blue Card (Blaue Karte EU): the main route

If you have a job offer in Germany, the **EU Blue Card** is the main route for qualified work. The condition is simple: a qualified job + an offer above a certain **gross salary threshold**.

The key point: because IT is a shortage occupation, the **lower (reduced) threshold** applies. The same goes for recent graduates (Berufsanfänger) who finished their degree within the last 3 years.

| Category (2025) | Annual gross salary threshold (approx.) |
|---|---|
| General threshold | ~€48,300 |
| Shortage occupation (incl. IT) + recent graduate | ~€43,759.80 |

> **As of 2025; the threshold is updated yearly, verify before you apply.** For comparison: in 2024 the figures were €45,300 / €41,041.80 — so they rise every year. Never treat a number as permanent.

Good news: **even an entry-level (junior) IT salary usually clears the shortage threshold comfortably.** So with an offer, the Blue Card is often within reach.

## The big one for self-taught/bootcamp devs (Nov 2023 reform)

This is the game-changer for self-taught developers and bootcamp graduates. With the **November 2023 Skilled Immigration Act (Fachkräfteeinwanderungsgesetz) reform**, **IT specialists WITHOUT a university degree can also get a Blue Card.**

Requirements:

- At least **3 years of relevant professional experience in the last 7 years** (in IT), and
- a **qualifying job offer** that meets the shortage threshold.

No formal degree required. So someone who learned to code on the job or in projects, has experience but never finished a degree, can still take this route. For someone with a bootcamp + a few years of real work experience, this is the most concrete path.

## Blue Card perks

The Blue Card is more than just a work permit:

- **Fast permanent residence (Niederlassungserlaubnis):** With a Blue Card after **27 months**, or after just **21 months with German at B1** level.
- **Easy family reunification:** Your partner can join without a German-language requirement and is allowed to work straight away.
- **EU mobility:** Under certain conditions, easier movement to other EU countries.

These three points are what make the Blue Card stronger than a standard work permit.

## Entry routes when you don't have a job yet

No job offer yet? There are still ways into Germany:

- **Chancenkarte (Opportunity Card):** In force since June 2024, a **points-based** card. You can enter without a job offer and look for work on the ground (qualifications, experience, age and language are scored).
- **6-month job-seeker visa:** From abroad, you can come for up to 6 months to look for qualified work.

Both routes and how they differ from the master's route, in detail: [Germany: master's or job-seeker visa?](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

## English vs German at work

The most-asked question. The truth: **English-only jobs concentrate in Berlin, at startups and big tech** — SAP, Zalando, N26, Delivery Hero, Trade Republic. There, a career without German is possible.

But the moment you leave Berlin — into the **Mittelstand (mid-sized family firms) and the automotive industry** — **German makes a massive difference.** Daily communication, meetings, promotions all run in German. You get in with English; you rise with German.

The honest role of language in working life: [German for jobs: the honest truth](/en/blog/german-language-reality-for-jobs-in-germany-the-honest-truth-en).

## The salary reality (2025, gross)

Roughly **35–42% of your gross goes to tax and social contributions** — calculate your net accordingly. Approximate salary bands for software developers, as of 2025:

| Level | Annual gross (2025) |
|---|---|
| Junior | ~€45,000 – €55,000 |
| Mid-level | ~€60,000 – €75,000 |
| Senior | ~€80,000 – €100,000+ |

Munich and Frankfurt tend to be higher (but so is the cost of living). Important: **even an entry-level IT salary usually clears the Blue Card shortage threshold comfortably** — so on the visa side, IT people are lucky.

> The numbers are gross estimates for 2025; they vary by company, city and role — verify with current data before applying.

## Bottom line & honest advice

- IT is **Germany's #1 shortage occupation** — as a foreign developer, you hold a strong card.
- The main route is the **EU Blue Card**; IT gets the **reduced threshold**, and even junior salaries usually clear it.
- **Even without a degree**, you can get a Blue Card with 3 years of experience in the last 7 years + a suitable offer (Nov 2023 reform).
- No job yet? Enter via the **Chancenkarte** or **job-seeker visa** and search on the ground.
- Start with English, but **learning German multiplies your career** (especially outside Berlin).

Read on: [Studying computer science / Informatik in Germany as a foreigner](/en/blog/studying-computer-science-informatik-in-germany-as-a-foreigner-en) and [What to do with a computer science degree in Germany (job market & salary)](/en/blog/what-to-do-with-a-computer-science-degree-in-germany-job-market-salary-en). For the visa process with a job offer: [Work visa with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en). If you're a student, the post-graduation switch: [From student visa to work permit (Zweckwechsel)](/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en).

*This article reflects the rules and thresholds in force as of early 2026. Visa and salary thresholds are updated yearly; before applying, always verify the current figures from official sources (Make it in Germany, ABH, BAMF).*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary',
                'title' => 'Almanya\'da yabancı olarak IT/teknoloji sektöründe çalışmak: Blue Card, rotalar ve maaş',
                'excerpt' => 'IT, Almanya\'nın 1 numaralı darboğaz mesleği. EU Blue Card eşikleri (2025), diplomasız IT uzmanları için rota, Chancenkarte ve gerçek yazılımcı maaşları — dürüst rehber.',
                'meta_title' => 'Almanya\'da IT\'ci olarak çalışmak: Blue Card & maaş',
                'meta_description' => 'Yabancı yazılımcılar için Almanya: EU Blue Card 2025 eşikleri, diplomasız rota (3 yıl deneyim), Chancenkarte ve gerçek maaşlar. Eşikler yıllık güncellenir.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-de',
                'title' => 'Als Ausländer:in in der IT/Tech-Branche in Deutschland arbeiten: Blaue Karte, Wege & Gehalt',
                'excerpt' => 'IT ist Deutschlands Engpassberuf Nr. 1. Blue-Card-Schwellen (2025), der Weg für IT-Fachkräfte ohne Abschluss, Chancenkarte und echte Entwicklergehälter — ehrlich erklärt.',
                'meta_title' => 'In der IT in Deutschland arbeiten: Blue Card & Gehalt',
                'meta_description' => 'Für ausländische Entwickler:innen: Blue-Card-Schwellen 2025, Weg ohne Abschluss (3 Jahre Erfahrung), Chancenkarte und echte Gehälter. Schwellen jährlich angepasst.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-en',
                'title' => 'Working in IT/tech in Germany as a foreigner: Blue Card, routes & salary',
                'excerpt' => 'IT is Germany\'s #1 shortage occupation. EU Blue Card thresholds (2025), the no-degree route for IT specialists, the Opportunity Card and real developer salaries — the honest guide.',
                'meta_title' => 'Working in IT in Germany: Blue Card & salary guide',
                'meta_description' => 'For foreign developers: EU Blue Card 2025 thresholds, the no-degree route (3 years experience), Opportunity Card and real salaries. Thresholds rise yearly — verify first.',
                'body' => $enBody,
            ],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale'           => $locale,
                'translation_group_id' => $groupId,
                'user_id'          => $userId,
                'category_id'      => $categoryId,
                'title'            => $v['title'],
                'excerpt'          => Str::limit($v['excerpt'], 250, '…'),
                'content_md'       => $v['body'],
                'content_html'     => $html,
                'meta_title'       => $v['meta_title'],
                'meta_description' => Str::limit($v['meta_description'], 158, '…'),
                'reading_minutes'  => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
                'is_published'     => true,
                'published_at'     => now(),
            ];

            $existing = Post::where('slug', $v['slug'])->first();
            if ($existing) {
                $existing->update($payload);
            } else {
                Post::create($payload + ['slug' => $v['slug']]);
            }
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary',
            'working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-de',
            'working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-en',
        ])->delete();
    }
};

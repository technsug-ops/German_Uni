<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da lisanslı mimar olmak — Architektenkammer yol haritası (2026).
 * Doğrulandı: "Architekt" korumalı unvan, eyalet Architektenkammer kaydı şart; akredite derece
 * (genelde 5 yıl / min 300 ECTS, EU direktifi ≥4 yıl) + ~2 yıl pratik; bachelor tek başına yetmez.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'd3a30000-3333-4a2c-9f50-dd01ee04aa03';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da mimarlık okumak bir şeydir; **"Architekt" unvanını taşıyıp kendi imzanla inşaat ruhsatı sunabilmek bambaşka bir şeydir.** Bu yazı tam da ikinci kısmı — lisans yolunu — anlatıyor. Çünkü bu, mimarlık kümemizin en yanlış anlaşılan ama en değerli konusu: mezun olmak seni "mimar" yapmaz, seni "mimarlık mezunu" yapar.

Tıptaki Approbation'ı düşün: diploma tek başına hasta tedavi etme yetkisi vermez. Mimarlıkta da benzer bir kapı var ve adı **Architektenkammer** (mimarlar odası). Bu yazıda kimin, nasıl ve hangi şartlarla bu kapıdan geçebildiğini dürüstçe anlatacağım.

## 1. "Architekt" korumalı bir unvandır — Kammer kaydı şart

Almanya'da **"Architekt" yasal olarak korumalı bir unvandır** (geschützte Berufsbezeichnung). Yani mimarlık okumuş olman, hatta bir büroda çalışıyor olman seni yasal olarak "Architekt" yapmaz. Bu unvanı ancak bir eyaletin **Architektenkammer'ine kayıtlıysan** kullanabilirsin.

Kayıt neden önemli? Çünkü **inşaat ruhsatı başvurularını (Bauvorlageberechtigung) imzalama yetkisi** genelde Kammer kaydına bağlıdır. Kayıtlı değilsen tasarım yapabilir, bir büroda çalışabilir, projeye katkı sunabilirsin — ama planları resmi olarak sunan, sorumluluğu üstlenen kişi **kayıtlı bir Architekt** olmak zorundadır.

Özetle: **Diploma → çalışabilirsin. Kammer kaydı → "Architekt"sin ve ruhsat sunabilirsin.** İkisi ayrı aşamalardır ve aradaki mesafe genelde yıllarla ölçülür.

## 2. Gerekler: akredite derece + pratik deneyim

Kammer'e kayıt tipik olarak iki temel şart ister *(2025/2026 itibarıyla, yaklaşık; kendi eyaletinin Kammer'inde doğrula)*:

| Şart | Tipik gereklilik | Not |
|---|---|---|
| **Akademik derece** | Akredite mimarlık derecesi, genelde **5 yıl / min. 300 ECTS** | EU direktifi asgari **4 yıl** der; Almanya pratikte 5 yılı (Bachelor+Master) bekler |
| **Pratik deneyim** | Mezuniyet sonrası genelde **~2 yıl** denetimli pratik (Berufspraxis) | Süre eyalete göre değişir; doğrula |
| **Kayıt** | Bir eyalet Architektenkammer'ine başvuru | Belgeler + bazen mülakat/dosya |

**Kalın gerçek:** Ne yalnızca diploma ne yalnızca deneyim tek başına yeter. **Akredite derece + pratik deneyim + kayıt** üçlüsü birlikte gerekir. Pratik yılları da genelde denetimli olmalı — yani deneyimli bir Architekt gözetiminde geçmeli.

Ayrıca birçok Kammer sürekli eğitim (Fortbildung) beklentisiyle üyeliği sürdürür. Yani kayıt bir kere alınıp bitirilen bir şey değil, sürekli bir mesleki statüdür.

## 3. Yurtdışı derecenin tanınırlığı: AB direktifi vs 3. ülke

Burada senin durumun kritik. **Nereden mezun olduğun** yolun uzunluğunu belirler:

- **AB/AEA (EU/EEA) derecesi:** Mesleki Nitelikler Direktifi kapsamında, 5 yıllık (veya 4+2) akredite mimarlık dereceleri AB genelinde **büyük ölçüde otomatik tanınır.** Bu en pürüzsüz yoldur.
- **Üçüncü ülke (ör. Türkiye) derecesi:** Otomatik tanınma **yoktur.** Derecen tek tek incelenir (Einzelfallprüfung). Eyalet Kammer'i içeriğini, süresini ve ECTS'ini Almanya standardıyla karşılaştırır. Eksik görülürse **ek ders, ek pratik veya köprü (Anpassung) istenebilir.**

**Kalın gerçek:** 3. ülke mezunuysan, "diplomam var" demek yetmez — **denklik/tanınma süreci** senin gerçek engelindir ve sonucu eyalete ve dosyana göre değişir. Erken başla ve ilgili eyalet Kammer'iyle doğrudan iletişime geç.

Bu tanınırlık mantığı diğer düzenlenmiş mesleklere de benzer; iş vizesi ve tanınma tarafını [Almanya iş vizesi: iş teklifiyle süreç ve zaman çizelgesi](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) yazısında da ele aldık.

## 4. Her eyaletin ayrı Kammer'i var

Almanya'da **tek bir ulusal mimarlar odası yoktur.** Her Bundesland'ın kendi Architektenkammer'i vardır — Bayern, Berlin, Nordrhein-Westfalen, Baden-Württemberg ve diğerleri ayrı ayrı.

Bu ne demek? **Kayıt şartları, istenen pratik süresi ve prosedür eyaletten eyalete değişebilir.** Genel çerçeve (akredite derece + pratik + kayıt) benzer olsa da detaylar farklıdır. Bir eyalette kayıtlıysan diğer eyaletlerde de büyük ölçüde tanınırsın, ama **başvuracağın eyaletin Kammer'inin kendi kurallarını okumak zorundasın.**

**Pratik öneri:** Hangi şehirde çalışmayı planlıyorsan, o eyaletin Kammer web sitesini kaynak al. "Bir arkadaşım Bayern'de şöyle yapmış" bilgisi seni Berlin'de yanıltabilir.

## 5. Bachelor tek başına yetmez — master şart

Bu, kümedeki en sık yapılan hata. **Almanya'da sadece 3 yıllık bir Bachelor mimarlık diplomasıyla Architektenkammer'e kayıt olamazsın.**

Neden? Çünkü Kammer'ler genelde **min. 5 yıl / 300 ECTS** akredite bir eğitim bekler. Tipik Alman yapısı **Bachelor (genelde 3-4 yıl) + Master (1-2 yıl)** kombinasyonudur ve lisans yolunun kapısını genelde **Master açar.**

**Kalın gerçek:** Kariyer planın "mimar olmak" ise, **Master neredeyse pazarlık konusu değildir.** Sadece Bachelor'la mezun olursan mimarlık bürosunda çalışabilirsin ama "Architekt" unvanını taşıyamaz, ruhsat sunamazsın. Bu, mimarlığı diğer birçok mühendislik dalından ayıran nokta — kıyas için [Almanya'da mühendis olarak çalışmak: Blue Card, maaş, unvan](/tr/blog/working-as-an-engineer-in-germany-blue-card-salary) yazısına bak; orada "Ingenieur" unvanının mantığını da göreceksin.

Master'a giden yol ve İngilizce seçenekler için: [Almancasız Almanya'da mimarlık: İngilizce master ve Urban Design](/tr/blog/english-taught-architecture-masters-in-germany-without-german). Mimarlık okumanın genel resmi ise burada: [Almanya'da mimarlık okumak: uluslararası öğrenci rehberi](/tr/blog/studying-architecture-in-germany-as-a-foreigner).

## 6. Süreç ve adımlar (hedge'li)

Sıra genelde şöyledir *(eyalete göre değişir, kesin süre için Kammer'i doğrula)*:

1. **Akredite Master'ı bitir** (Bachelor + Master, min. 5 yıl / 300 ECTS hedefiyle).
2. **Denetimli pratik topla** — mezuniyet sonrası genelde ~2 yıl, bir Architekt gözetiminde.
3. **3. ülke mezunuysan tanınma sürecini başlat** — dosyanı ilgili eyalet Kammer'ine sun.
4. **Kammer'e kayıt başvurusu yap** — belgeler, pratik kanıtı, bazen mülakat/dosya değerlendirmesi.
5. **Kayıt sonrası** unvanı kullan, ruhsat sun, sürekli eğitimle üyeliği sürdür.

Bu yol, mimar olarak çalışmanın gerçek maaş ve iş piyasası tarafıyla iç içedir; onu [Almanya'da mimar olarak çalışmak: maaş ve iş piyasası](/tr/blog/working-as-an-architect-in-germany-salary-job-market) yazısında ayrıca ele aldık.

## Sonuç & dürüst tavsiye

Dürüst olayım: Almanya'da **lisanslı mimar olmak uzun bir yoldur** ve tek bir diplomayla bitmez. Gerçekçi çerçeve şudur — **akredite Master + ~2 yıl denetimli pratik + eyalet Kammer kaydı.** Bunların hiçbiri opsiyonel değil.

Üçüncü ülke mezunuysan, en büyük belirsizlik **tanınma sürecidir** — bunu erken, doğrudan ilgili eyalet Kammer'iyle netleştir. Ve şu gerçeği unutma: **"Architekt" olmak istiyorsan Bachelor tek başına yetmez; Master şarttır.** Eğer amacın sadece bir büroda tasarım yapmaksa lisans şart değildir, ama unvan ve ruhsat yetkisi istiyorsan bu yolu yürümen gerekir.

*Bu yazı 2026 başı itibarıyla genel bir rehberdir; Architektenkammer şartları, pratik süreleri ve tanınma kuralları eyalete göre değişir ve zamanla güncellenir. Kesin ve güncel bilgi için başvuracağın eyaletin Architektenkammer'ini doğrula.*
MD;

        $deBody = <<<'MD'
Architektur in Deutschland zu studieren ist eine Sache; **den Titel "Architekt" zu führen und mit deiner eigenen Unterschrift eine Bauvorlage einzureichen, ist etwas ganz anderes.** Genau um diesen zweiten Teil — den Weg zur Zulassung — geht es hier. Denn das ist das am meisten missverstandene Thema unseres Architektur-Clusters: Ein Abschluss macht dich nicht zum "Architekten", sondern zum "Architektur-Absolventen".

Denk an die Approbation in der Medizin: Das Diplom allein erlaubt noch keine Behandlung von Patienten. In der Architektur gibt es eine ähnliche Tür, und sie heißt **Architektenkammer**. Ich erkläre dir ehrlich, wer wie und unter welchen Bedingungen durch diese Tür geht.

## 1. "Architekt" ist ein geschützter Titel — Kammereintrag ist Pflicht

In Deutschland ist **"Architekt" eine gesetzlich geschützte Berufsbezeichnung.** Dass du Architektur studiert hast oder in einem Büro arbeitest, macht dich rechtlich noch nicht zum "Architekten". Diesen Titel darfst du nur führen, wenn du **in die Architektenkammer eines Bundeslandes eingetragen bist.**

Warum ist der Eintrag so wichtig? Weil die **Bauvorlageberechtigung** — das Recht, Bauanträge einzureichen — in der Regel an den Kammereintrag gebunden ist. Ohne Eintrag darfst du entwerfen, in einem Büro arbeiten und am Projekt mitwirken — aber wer die Pläne offiziell einreicht und die Verantwortung übernimmt, muss ein **eingetragener Architekt** sein.

Kurz gesagt: **Diplom → du darfst arbeiten. Kammereintrag → du bist "Architekt" und darfst Bauvorlagen einreichen.** Das sind zwei getrennte Stufen, und der Abstand dazwischen misst sich meist in Jahren.

## 2. Voraussetzungen: akkreditierter Abschluss + Praxis

Der Kammereintrag verlangt typischerweise zwei Grundvoraussetzungen *(Stand 2025/2026, ungefähr; prüfe bei der Kammer deines Bundeslandes)*:

| Voraussetzung | Typische Anforderung | Hinweis |
|---|---|---|
| **Akademischer Abschluss** | Akkreditierter Architektur-Abschluss, meist **5 Jahre / min. 300 ECTS** | Die EU-Richtlinie nennt mindestens **4 Jahre**; Deutschland erwartet in der Praxis 5 Jahre (Bachelor+Master) |
| **Berufspraxis** | Nach dem Abschluss meist **~2 Jahre** betreute Praxis | Die Dauer variiert je Bundesland; prüfe das |
| **Eintrag** | Antrag bei einer Landes-Architektenkammer | Unterlagen + teils Gespräch/Prüfung der Mappe |

**Fette Wahrheit:** Weder das Diplom allein noch die Praxis allein reicht. Es braucht das Zusammenspiel aus **akkreditiertem Abschluss + Berufspraxis + Eintrag.** Die Praxisjahre müssen zudem meist betreut sein — also unter Aufsicht eines erfahrenen Architekten.

Viele Kammern verlangen außerdem Fortbildung, um die Mitgliedschaft zu halten. Der Eintrag ist also nicht einmalig, sondern ein dauerhafter beruflicher Status.

## 3. Anerkennung ausländischer Abschlüsse: EU-Richtlinie vs. Drittstaat

Hier ist deine Situation entscheidend. **Wo du deinen Abschluss gemacht hast** bestimmt die Länge des Weges:

- **EU/EWR-Abschluss:** Im Rahmen der Berufsqualifikationsrichtlinie werden akkreditierte fünfjährige (oder 4+2) Architektur-Abschlüsse EU-weit **weitgehend automatisch anerkannt.** Das ist der reibungsloseste Weg.
- **Drittstaat-Abschluss (z. B. Türkei):** Es gibt **keine automatische Anerkennung.** Dein Abschluss wird einzeln geprüft (Einzelfallprüfung). Die Landeskammer vergleicht Inhalt, Dauer und ECTS mit dem deutschen Standard. Bei Lücken können **zusätzliche Kurse, Praxis oder eine Anpassung verlangt werden.**

**Fette Wahrheit:** Als Drittstaat-Absolvent reicht "Ich habe ein Diplom" nicht — das **Anerkennungsverfahren** ist deine eigentliche Hürde, und das Ergebnis hängt vom Bundesland und deiner Akte ab. Fang früh an und wende dich direkt an die Kammer des jeweiligen Bundeslandes.

Diese Anerkennungslogik ähnelt der anderer reglementierter Berufe; Arbeitsvisum und Anerkennung behandeln wir auch in [Deutsches Arbeitsvisum: Prozess und Zeitplan mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).

## 4. Jedes Bundesland hat seine eigene Kammer

In Deutschland gibt es **keine einzige nationale Architektenkammer.** Jedes Bundesland hat seine eigene Architektenkammer — Bayern, Berlin, Nordrhein-Westfalen, Baden-Württemberg und die anderen jeweils getrennt.

Was heißt das? **Eintragsvoraussetzungen, geforderte Praxisdauer und Verfahren können sich von Land zu Land unterscheiden.** Der Grundrahmen (akkreditierter Abschluss + Praxis + Eintrag) ist ähnlich, aber die Details unterscheiden sich. Wenn du in einem Bundesland eingetragen bist, wirst du in den anderen weitgehend anerkannt, aber du musst **die eigenen Regeln der Kammer deines Ziel-Bundeslandes lesen.**

**Praktischer Tipp:** In welcher Stadt du arbeiten willst — nimm die Website der Kammer dieses Bundeslandes als Quelle. "Ein Freund hat es in Bayern so gemacht" kann dich in Berlin in die Irre führen.

## 5. Bachelor allein reicht nicht — Master ist Pflicht

Das ist der häufigste Fehler im Cluster. **Mit einem reinen dreijährigen Bachelor in Architektur kannst du dich in Deutschland nicht in die Architektenkammer eintragen lassen.**

Warum? Weil die Kammern meist **min. 5 Jahre / 300 ECTS** akkreditierte Ausbildung erwarten. Die typische deutsche Struktur ist die Kombination **Bachelor (meist 3-4 Jahre) + Master (1-2 Jahre)**, und die Tür zur Zulassung öffnet meist erst der **Master.**

**Fette Wahrheit:** Wenn dein Karriereplan "Architekt werden" heißt, ist der **Master praktisch nicht verhandelbar.** Nur mit Bachelor darfst du in einem Architekturbüro arbeiten, aber du darfst den Titel "Architekt" nicht führen und keine Bauvorlagen einreichen. Das unterscheidet die Architektur von vielen Ingenieurdisziplinen — zum Vergleich [Als Ingenieur in Deutschland arbeiten: Blue Card, Gehalt, Titel](/de/blog/working-as-an-engineer-in-germany-blue-card-salary-de); dort siehst du auch die Logik des Titels "Ingenieur".

Zum Weg in den Master und zu englischsprachigen Optionen: [Architektur ohne Deutsch: englischsprachige Master und Urban Design](/de/blog/english-taught-architecture-masters-in-germany-without-german-de). Das Gesamtbild des Architektur-Studiums findest du hier: [Architektur in Deutschland studieren: Leitfaden für internationale Studierende](/de/blog/studying-architecture-in-germany-as-a-foreigner-de).

## 6. Ablauf und Schritte (mit Vorbehalt)

Die Reihenfolge ist meist so *(variiert je Bundesland, für genaue Dauer die Kammer prüfen)*:

1. **Akkreditierten Master abschließen** (Bachelor + Master, Ziel min. 5 Jahre / 300 ECTS).
2. **Betreute Praxis sammeln** — nach dem Abschluss meist ~2 Jahre, unter Aufsicht eines Architekten.
3. **Als Drittstaat-Absolvent das Anerkennungsverfahren starten** — Akte bei der zuständigen Landeskammer einreichen.
4. **Antrag auf Kammereintrag stellen** — Unterlagen, Praxisnachweis, teils Gespräch/Mappen-Prüfung.
5. **Nach dem Eintrag** den Titel führen, Bauvorlagen einreichen, die Mitgliedschaft mit Fortbildung halten.

Dieser Weg ist eng mit Gehalt und Arbeitsmarkt verbunden; das behandeln wir gesondert in [Als Architekt in Deutschland arbeiten: Gehalt und Arbeitsmarkt](/de/blog/working-as-an-architect-in-germany-salary-job-market-de).

## Fazit & ehrlicher Rat

Ehrlich gesagt: In Deutschland ein **zugelassener Architekt zu werden ist ein langer Weg** und endet nicht mit einem einzigen Diplom. Der realistische Rahmen ist — **akkreditierter Master + ~2 Jahre betreute Praxis + Eintrag in die Landeskammer.** Nichts davon ist optional.

Als Drittstaat-Absolvent ist die größte Unsicherheit das **Anerkennungsverfahren** — kläre das früh und direkt mit der zuständigen Landeskammer. Und vergiss diese Wahrheit nicht: **Wenn du "Architekt" werden willst, reicht der Bachelor allein nicht; der Master ist Pflicht.** Wenn dein Ziel nur ist, in einem Büro zu entwerfen, brauchst du keine Zulassung — aber für Titel und Bauvorlageberechtigung musst du diesen Weg gehen.

*Dieser Beitrag ist ein allgemeiner Leitfaden mit Stand Anfang 2026; die Voraussetzungen der Architektenkammern, Praxiszeiten und Anerkennungsregeln variieren je Bundesland und ändern sich mit der Zeit. Für genaue und aktuelle Informationen prüfe die Architektenkammer des Bundeslandes, in dem du dich bewerben willst.*
MD;

        $enBody = <<<'MD'
Studying architecture in Germany is one thing; **carrying the title "Architekt" and submitting building permit applications under your own signature is something else entirely.** This article is about exactly that second part — the licensing path. Because it's the most misunderstood topic in our architecture cluster: graduating doesn't make you an "architect," it makes you an "architecture graduate."

Think of the Approbation in medicine: the degree alone doesn't grant the right to treat patients. Architecture has a similar door, and it's called the **Architektenkammer** (chamber of architects). Here I'll explain honestly who passes through that door, how, and under what conditions.

## 1. "Architekt" is a protected title — chamber registration is mandatory

In Germany, **"Architekt" is a legally protected professional title** (geschützte Berufsbezeichnung). Having studied architecture, or even working at a firm, does not legally make you an "Architekt." You may only use this title if you are **registered with the Architektenkammer of a federal state.**

Why does registration matter so much? Because the **Bauvorlageberechtigung** — the right to submit building permit applications — is generally tied to chamber registration. Without it, you may design, work at a firm, and contribute to projects — but the person who officially submits the plans and takes on the responsibility must be a **registered Architekt.**

In short: **Degree → you may work. Chamber registration → you are an "Architekt" and may submit building applications.** These are two separate stages, and the gap between them is usually measured in years.

## 2. Requirements: accredited degree + practical experience

Chamber registration typically requires two core things *(as of 2025/2026, approximate; verify with the chamber of your federal state)*:

| Requirement | Typical requirement | Note |
|---|---|---|
| **Academic degree** | Accredited architecture degree, usually **5 years / min. 300 ECTS** | The EU directive states a minimum of **4 years**; Germany in practice expects 5 years (Bachelor+Master) |
| **Practical experience** | After graduation, usually **~2 years** of supervised practice (Berufspraxis) | Duration varies by state; verify |
| **Registration** | Application to a state Architektenkammer | Documents + sometimes an interview/portfolio review |

**Bold fact:** Neither the degree alone nor the experience alone is enough. You need the combination of **accredited degree + practical experience + registration.** The practice years must usually also be supervised — that is, under the guidance of an experienced Architekt.

Many chambers also require continuing education (Fortbildung) to maintain membership. So registration isn't a one-time step — it's an ongoing professional status.

## 3. Recognition of foreign degrees: EU directive vs. third country

Here your situation is decisive. **Where you earned your degree** determines the length of the path:

- **EU/EEA degree:** Under the Professional Qualifications Directive, accredited five-year (or 4+2) architecture degrees are **largely automatically recognized** across the EU. This is the smoothest route.
- **Third-country degree (e.g. Turkey):** There is **no automatic recognition.** Your degree is assessed individually (Einzelfallprüfung). The state chamber compares its content, duration and ECTS with the German standard. If gaps are found, **additional courses, practice, or a compensation measure (Anpassung) may be required.**

**Bold fact:** As a third-country graduate, "I have a degree" is not enough — the **recognition process** is your real hurdle, and the outcome depends on the state and your file. Start early and contact the relevant state chamber directly.

This recognition logic resembles that of other regulated professions; we also cover work visas and recognition in [Germany work visa: process and timeline with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).

## 4. Every federal state has its own chamber

In Germany there is **no single national chamber of architects.** Each Bundesland has its own Architektenkammer — Bavaria, Berlin, North Rhine-Westphalia, Baden-Württemberg and the others, each separately.

What does this mean? **Registration requirements, the required practice period, and the procedure can differ from state to state.** The overall framework (accredited degree + practice + registration) is similar, but the details differ. If you're registered in one state, you'll be largely recognized in the others, but you must **read the specific rules of the chamber of your target state.**

**Practical tip:** Whatever city you plan to work in, use that state's chamber website as your source. "A friend did it this way in Bavaria" can mislead you in Berlin.

## 5. A bachelor alone is not enough — a master is mandatory

This is the most common mistake in the cluster. **With a pure three-year bachelor's degree in architecture, you cannot register with the Architektenkammer in Germany.**

Why? Because chambers usually expect **min. 5 years / 300 ECTS** of accredited education. The typical German structure is the combination **Bachelor (usually 3-4 years) + Master (1-2 years)**, and the door to licensing is usually opened only by the **master.**

**Bold fact:** If your career plan is "to become an architect," the **master is essentially non-negotiable.** With only a bachelor you may work at an architecture firm, but you may not carry the title "Architekt" or submit building applications. This is what sets architecture apart from many engineering disciplines — for comparison, see [Working as an engineer in Germany: Blue Card, salary, title](/en/blog/working-as-an-engineer-in-germany-blue-card-salary-en); there you'll also see the logic of the "Ingenieur" title.

For the path to the master and English-taught options: [Architecture without German: English-taught masters and Urban Design](/en/blog/english-taught-architecture-masters-in-germany-without-german-en). For the big picture of studying architecture: [Studying architecture in Germany: a guide for international students](/en/blog/studying-architecture-in-germany-as-a-foreigner-en).

## 6. Process and steps (hedged)

The sequence is usually as follows *(varies by state; verify exact durations with the chamber)*:

1. **Complete an accredited master** (Bachelor + Master, aiming for min. 5 years / 300 ECTS).
2. **Gather supervised practice** — after graduation, usually ~2 years under the guidance of an Architekt.
3. **If you're a third-country graduate, start the recognition process** — submit your file to the relevant state chamber.
4. **Apply for chamber registration** — documents, proof of practice, sometimes an interview/portfolio review.
5. **After registration**, use the title, submit building applications, and maintain membership through continuing education.

This path is closely tied to the real salary and job-market side of working as an architect; we cover that separately in [Working as an architect in Germany: salary and job market](/en/blog/working-as-an-architect-in-germany-salary-job-market-en).

## Conclusion & honest advice

Let me be honest: becoming a **licensed architect in Germany is a long road** and does not end with a single degree. The realistic framework is — **an accredited master + ~2 years of supervised practice + registration with the state chamber.** None of these is optional.

If you're a third-country graduate, the biggest uncertainty is the **recognition process** — clarify it early and directly with the relevant state chamber. And don't forget this truth: **if you want to be an "Architekt," a bachelor alone is not enough; a master is mandatory.** If your goal is only to design at a firm, a license isn't required — but if you want the title and permit authority, you have to walk this path.

*This article is a general guide as of early 2026; Architektenkammer requirements, practice periods and recognition rules vary by federal state and change over time. For accurate and current information, verify with the Architektenkammer of the state where you intend to apply.*
MD;

        $variants = [
            'tr' => ['slug'=>'becoming-a-licensed-architect-in-germany-architektenkammer',    'title'=>'Almanya\'da Lisanslı Mimar Olmak: Architektenkammer ve Yol Haritası (2026)', 'excerpt'=>'Almanya\'da "Architekt" korumalı bir unvandır ve eyalet Architektenkammer kaydı şarttır. Akredite derece (5 yıl/300 ECTS) + ~2 yıl pratik, yurtdışı derece tanınırlığı ve neden bachelor tek başına yetmediği — dürüst yol haritası.', 'meta_title'=>'Almanya\'da Lisanslı Mimar Olmak: Architektenkammer (2026)', 'meta_description'=>'Architektenkammer kaydı, akredite derece + pratik, yurtdışı derece tanınırlığı ve neden mimarlıkta master şart olduğunu anlatan dürüst 2026 rehberi.', 'body'=>$trBody],
            'de' => ['slug'=>'becoming-a-licensed-architect-in-germany-architektenkammer-de', 'title'=>'Zugelassener Architekt in Deutschland werden: Architektenkammer und Fahrplan (2026)', 'excerpt'=>'In Deutschland ist "Architekt" ein geschützter Titel, und der Eintrag in die Landes-Architektenkammer ist Pflicht. Akkreditierter Abschluss (5 Jahre/300 ECTS) + ~2 Jahre Praxis, Anerkennung ausländischer Abschlüsse und warum der Bachelor allein nicht reicht.', 'meta_title'=>'Zugelassener Architekt in Deutschland: Architektenkammer (2026)', 'meta_description'=>'Kammereintrag, akkreditierter Abschluss + Praxis, Anerkennung ausländischer Abschlüsse und warum in der Architektur der Master Pflicht ist — ehrlicher Leitfaden 2026.', 'body'=>$deBody],
            'en' => ['slug'=>'becoming-a-licensed-architect-in-germany-architektenkammer-en', 'title'=>'Becoming a Licensed Architect in Germany: Architektenkammer and Roadmap (2026)', 'excerpt'=>'In Germany "Architekt" is a protected title and registration with a state Architektenkammer is mandatory. Accredited degree (5 years/300 ECTS) + ~2 years of practice, recognition of foreign degrees, and why a bachelor alone is not enough — an honest roadmap.', 'meta_title'=>'Becoming a Licensed Architect in Germany: Architektenkammer (2026)', 'meta_description'=>'Chamber registration, accredited degree + practice, recognition of foreign degrees, and why a master is mandatory in architecture — an honest 2026 guide.', 'body'=>$enBody],
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
            'becoming-a-licensed-architect-in-germany-architektenkammer',
            'becoming-a-licensed-architect-in-germany-architektenkammer-de',
            'becoming-a-licensed-architect-in-germany-architektenkammer-en',
        ])->delete();
    }
};

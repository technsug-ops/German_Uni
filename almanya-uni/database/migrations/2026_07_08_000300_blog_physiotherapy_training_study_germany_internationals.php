<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da fizyoterapi eğitimi — Ausbildung ve Bachelor yolları (2026).
 * Doğrulandı: Physiotherapeut düzenlenmiş meslek, staatliche Erlaubnis gerekir; iki eğitim yolu
 * (3 yıl Ausbildung Berufsfachschule + yeni Bachelor/dual, Akademisierung süreci); bazı özel
 * okullarda Schulgeld; B2 Almanca + lise + okul yeri şartı. Sayılar yıl-hedge'li, resmi doğrula.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c3d30000-3333-4faf-9f00-cc0add10aa03';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da fizyoterapist olmak istiyorsun ama hangi eğitimi alacağını bilmiyor musun? İyi haber: **fizyoterapi Almanya'da akademik zorunluluğu olmayan, düzenlenmiş bir sağlık mesleğidir.** Yani üniversite diploması şart değil — ama iki farklı yol var ve hangisinin sana uygun olduğunu bilmen gerekiyor.

Bu yazı **sıfırdan başlayanlar** için, yani henüz fizyoterapist değilsen. **Zaten fizyoterapistsen** eğitimini baştan almana gerek yok; o zaman [yurtdışı diplomanı tanıtma (Anerkennung) rehberimize](/tr/blog/getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung) bakmalısın, çünkü senin yolun çok daha kısadır. Genel bir çerçeve için de [Almanya'da fizyoterapist olmak rehberi](/tr/blog/becoming-a-physiotherapist-in-germany-as-a-foreigner) iyi bir başlangıçtır.

## İki Yol: 3 Yıllık Ausbildung mı, Bachelor mı?

Almanya'da fizyoterapist olmanın iki resmi yolu vardır. Her ikisi de sonunda seni **staatlich anerkannter Physiotherapeut** (devletçe tanınan fizyoterapist) yapar, ama giriş şartları, süre ve maliyet farklıdır.

| Kriter | 3 Yıllık Ausbildung | Bachelor (Studium) |
| --- | --- | --- |
| Nerede | Berufsfachschule / Physiotherapieschule | Yüksekokul / üniversite (Hochschule) |
| Süre | **~3 yıl** | ~3–4 yıl (bazıları dual/ausbildungsintegrierend) |
| Diploma | Devlet sınavı + Berufsbezeichnung izni | Bachelor of Science + (çoğunlukla) meslek izni |
| Maliyet | Bazı özel okullarda **Schulgeld (ücret)**; devlet okulları çoğunlukla ücretsiz | Devlet üniversitesinde genelde sadece Semesterbeitrag |
| Giriş | Lise / orta öğretim diploması | Genelde Abitur/Fachabitur veya denkliği |
| Dil | **B2 Almanca (pratikte şart)** | **B2 Almanca (pratikte şart)** |

**Önemli gerçek:** İngilizce fizyoterapi programı Almanya'da pratikte **yok denecek kadar azdır.** Hastalarla, aileleriyle ve ekibinle sürekli konuşacağın için **B2 Almanca her iki yolda da fiili şarttır.**

## Klasik Yol: 3 Yıllık Ausbildung (ve Schulgeld Gerçeği)

En yaygın ve en erişilebilir yol, bir **Berufsfachschule** ya da **Physiotherapieschule**'de yapılan 3 yıllık okul temelli eğitimdir. Teorik dersler (anatomi, fizyoloji, hastalık bilgisi) ve pratik uygulamalar (Praktika) iç içe geçer; sonunda **devlet sınavına (staatliche Prüfung)** girersin ve başarılıysan meslek unvanını kullanma iznini alırsın.

Dürüst olmam gereken bir nokta var: hemşirelik Ausbildung'unun aksine, **fizyoterapi Ausbildung'u her zaman maaşlı değildir** ve bazı okullar — özellikle **özel okullar — Schulgeld (okul ücreti) alır.** Bu ücret okuldan okula ciddi biçimde değişir; bazı eyalet/kamu okullarında ise ücretsizdir. Bu yüzden **başvurmadan önce her okulun ücret durumunu tek tek doğrulaman şart.**

Ausbildung mantığını genel olarak merak ediyorsan, [Almanya'da Ausbildung nedir rehberimiz](/tr/blog/what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners) sistemin tümünü anlatıyor. Maaşlı bir alan arıyorsan, karşılaştırma için [hemşirelik Ausbildung yazımıza](/tr/blog/nursing-ausbildung-in-germany-for-internationals-paid-training) da göz atabilirsin — orada eğitim boyunca maaş alırsın.

## Yeni Yol: Bachelor ve Dual Programlar (Akademisierung)

Almanya son yıllarda fizyoterapiyi **akademikleştirme (Akademisierung)** sürecinde. Giderek daha fazla yüksekokul ve üniversite, **fizyoterapi Bachelor programları** açıyor. Bunların bir kısmı **"ausbildungsintegrierend"** yani hem okul eğitimini hem de Bachelor'ı birleştiren **dual** programlardır; bir kısmı ise klasik akademik programlardır.

Bachelor yolunun avantajı: uluslararası ortamda **daha kolay tanınan bir derece**, araştırma ve uzmanlaşmaya açık kapı. Dezavantajı: genelde **Abitur/Fachabitur** ya da denkliği gerekir ve program sayısı Ausbildung okullarına göre sınırlıdır. **Hangi programın mezuna doğrudan meslek izni verdiğini mutlaka doğrula** — çünkü bazı Bachelor'lar izni içerirken bazıları ek adım isteyebilir.

## Şartlar: B2 Almanca + Lise + Okul Yeri

Hangi yolu seçersen seç, üç şey neredeyse her zaman gerekir:

- **Dil:** Genelde **B2 Almanca** (bazı okullar başlangıçta B1 kabul edip B2'yi süreç içinde ister). Bu, üzerinde pazarlık yapılmayan gerçek şarttır.
- **Okul diploması:** Ausbildung için orta öğretim/lise diploması; Bachelor için genelde Abitur/Fachabitur veya denkliği. Yurtdışı diplomanın Almanya'da tanınması (Zeugnisanerkennung) gerekebilir.
- **Okul/program yeri (Platz):** Vize alabilmen için önce **bir okuldan kesin kabul** almış olman gerekir. Yer bulmak sürecin en zorlu adımıdır; erken ve çok sayıda başvur.

Dil planını ciddiye al: Almancayı yapılandırılmış biçimde ilerletmek, tüm sürecin belkemiğidir.

## Vize ve Başvuru

AB dışı bir ülkedensen (örneğin Türkiye), **eğitim/meslek eğitimi amaçlı bir oturum izni** için başvurursun. Genel akış şöyledir (adımları resmi kaynaktan doğrula):

1. Almancanı **en az B1–B2** seviyesine getir ve belgele.
2. Okullara/programlara başvur, **kesin kabul (Zusage)** al.
3. Geçimini kanıtla (**Sperrkonto/bloke hesap** veya eşdeğeri), sağlık sigortası ayarla.
4. Ülkendeki Alman konsolosluğunda **eğitim/Ausbildung vizesi** için randevu al ve başvur.
5. Almanya'da Aufenthaltstitel'e (oturum kartı) çevir.

İş teklifiyle gelen nitelikli işçi vizesi ve hızlandırılmış prosedür gibi seçenekler için [iş teklifiyle Almanya çalışma vizesi rehberimiz](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) faydalı olacaktır. **Vize adımları ve rakamları yıldan yıla değişir — her zaman ülkendeki Alman konsolosluğunun güncel sayfasından doğrula.**

## Eğitim Sonrası: Erlaubnis, Anerkennung ve Kariyer

Eğitimini Almanya'da tamamlar ve devlet sınavını geçersen, doğrudan **"Erlaubnis zum Führen der Berufsbezeichnung"** (meslek unvanını kullanma izni) alırsın — ayrıca bir Anerkennung sürecine gerek kalmaz. (Anerkennung süreci yalnızca **yurtdışında eğitim almış** olanlar içindir.)

İş piyasası açısından haber iyi: **yaşlanan nüfus nedeniyle fizyoterapiste talep çok yüksek** ve iş garantisi güçlüdür. Özel praxisler, hastaneler, **rehabilitasyon klinikleri**, spor kulüpleri ve zamanla kendi praxis'ini açma seçeneği masadadır. Maaş, dil şartı ve günlük çalışma koşulları gibi dürüst detaylar için [fizyoterapist olarak çalışmak: maaş, dil ve gerçek](/tr/blog/working-as-a-physiotherapist-in-germany-salary-language-and-reality) yazımızı mutlaka oku.

## Sonuç & Dürüst Tavsiye

Sıfırdan başlıyorsan, **3 yıllık Ausbildung en erişilebilir yoldur** — ama maaşlı olmayabileceğini ve bazı özel okulların Schulgeld aldığını unutma; okulu seçmeden önce **ücret ve dil şartını tek tek doğrula.** Uzun vadede akademik kapıları açık tutmak istiyorsan ve elinde Abitur denkliği varsa, **Bachelor/dual yol** daha güçlü bir yatırım olabilir. Her iki durumda da işin özü aynıdır: **önce Almanca (B2), sonra okul yeri.** Diploman zaten varsa, eğitimi baştan alma — tanınma yolu senin için çok daha hızlıdır.

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır; ücretler, vize adımları ve program şartları yaklaşık olup zamanla değişir. Kesin ve güncel bilgi için ilgili eyaletin tanınma/eğitim makamına, seçtiğin okula ve ülkendeki Alman konsolosluğuna danış.*
MD;
        $deBody = <<<'MD'
Du willst in Deutschland Physiotherapeut werden, weißt aber nicht, welche Ausbildung die richtige ist? Gute Nachricht: **Physiotherapie ist in Deutschland ein reglementierter Gesundheitsberuf ohne akademische Pflicht.** Ein Hochschulstudium ist also nicht zwingend — aber es gibt zwei Wege, und du solltest wissen, welcher zu dir passt.

Dieser Beitrag ist für **Einsteiger ohne Vorqualifikation**. **Bist du bereits Physiotherapeut?** Dann musst du nicht neu ausbilden; sieh dir stattdessen unseren [Leitfaden zur Anerkennung deines ausländischen Abschlusses](/de/blog/getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung-de) an, denn dein Weg ist deutlich kürzer. Für den Gesamtüberblick ist unser [Leitfaden „Physiotherapeut werden in Deutschland"](/de/blog/becoming-a-physiotherapist-in-germany-as-a-foreigner-de) ein guter Start.

## Zwei Wege: 3-jährige Ausbildung oder Bachelor?

Es gibt zwei offizielle Wege zum Physiotherapeuten. Beide machen dich am Ende zum **staatlich anerkannten Physiotherapeuten**, unterscheiden sich aber bei Zugang, Dauer und Kosten.

| Kriterium | 3-jährige Ausbildung | Bachelorstudium |
| --- | --- | --- |
| Wo | Berufsfachschule / Physiotherapieschule | Hochschule / Universität |
| Dauer | **~3 Jahre** | ~3–4 Jahre (teils dual/ausbildungsintegrierend) |
| Abschluss | Staatliche Prüfung + Berufserlaubnis | Bachelor of Science + (meist) Berufserlaubnis |
| Kosten | An manchen privaten Schulen **Schulgeld**; staatliche oft kostenfrei | An staatlichen Hochschulen meist nur Semesterbeitrag |
| Zugang | Mittlerer Schulabschluss | Meist Abitur/Fachabitur oder gleichwertig |
| Sprache | **B2 Deutsch (faktisch Pflicht)** | **B2 Deutsch (faktisch Pflicht)** |

**Wichtige Wahrheit:** Englischsprachige Physiotherapie-Programme gibt es in Deutschland praktisch **so gut wie nicht.** Weil du ständig mit Patienten, Angehörigen und dem Team sprichst, ist **B2 Deutsch auf beiden Wegen faktisch Voraussetzung.**

## Klassischer Weg: 3-jährige Ausbildung (und die Wahrheit über Schulgeld)

Der häufigste und zugänglichste Weg ist die dreijährige schulische Ausbildung an einer **Berufsfachschule** bzw. **Physiotherapieschule.** Theorie (Anatomie, Physiologie, Krankheitslehre) und Praxis (Praktika) greifen ineinander; am Ende legst du die **staatliche Prüfung** ab und erhältst bei Bestehen die Erlaubnis, die Berufsbezeichnung zu führen.

Ehrlich gesagt: Anders als bei der Pflegeausbildung ist die **Physiotherapie-Ausbildung nicht immer vergütet**, und manche Schulen — besonders **private — verlangen Schulgeld.** Dieses Schulgeld variiert stark von Schule zu Schule; an einigen staatlichen/öffentlichen Schulen ist die Ausbildung dagegen kostenfrei. Deshalb gilt: **Prüfe vor der Bewerbung bei jeder Schule einzeln die Kostenlage.**

Wenn dich das Ausbildungssystem generell interessiert, erklärt unser [Leitfaden „Was ist eine Ausbildung"](/de/blog/what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners-de) das ganze System. Suchst du eine vergütete Alternative, wirf zum Vergleich einen Blick auf unseren [Beitrag zur Pflegeausbildung](/de/blog/nursing-ausbildung-in-germany-for-internationals-paid-training-de) — dort wirst du während der Ausbildung bezahlt.

## Neuer Weg: Bachelor- und duale Programme (Akademisierung)

Deutschland befindet sich in einem Prozess der **Akademisierung** der Physiotherapie. Immer mehr Hochschulen und Universitäten bieten **Bachelorprogramme in Physiotherapie** an. Ein Teil davon ist **„ausbildungsintegrierend"**, also **dual** — schulische Ausbildung und Bachelor werden kombiniert; ein anderer Teil sind klassische akademische Studiengänge.

Vorteil des Bachelorwegs: ein international **leichter anerkannter Abschluss** und offene Türen zu Forschung und Spezialisierung. Nachteil: meist wird das **Abitur/Fachabitur** oder eine Gleichwertigkeit verlangt, und die Zahl der Plätze ist begrenzter als bei den Ausbildungsschulen. **Prüfe unbedingt, ob ein Programm den Absolventen direkt die Berufserlaubnis verleiht** — manche Bachelor schließen sie ein, andere erfordern einen Zusatzschritt.

## Voraussetzungen: B2 Deutsch + Schulabschluss + Platz

Welchen Weg du auch wählst, drei Dinge sind fast immer nötig:

- **Sprache:** in der Regel **B2 Deutsch** (manche Schulen akzeptieren zu Beginn B1 und verlangen B2 im Verlauf). Das ist die nicht verhandelbare Bedingung.
- **Schulabschluss:** für die Ausbildung ein mittlerer Schulabschluss; für den Bachelor meist Abitur/Fachabitur oder gleichwertig. Eine Zeugnisanerkennung deines ausländischen Abschlusses kann nötig sein.
- **Ausbildungs-/Studienplatz:** Für das Visum brauchst du zuerst eine **feste Zusage** einer Schule. Einen Platz zu finden ist der schwierigste Schritt — bewirb dich früh und an vielen Stellen.

Nimm die Sprachplanung ernst: strukturiertes Deutschlernen ist das Rückgrat des gesamten Prozesses.

## Visum und Bewerbung

Kommst du aus einem Nicht-EU-Land (z. B. der Türkei), beantragst du einen **Aufenthaltstitel zu Ausbildungszwecken.** Der übliche Ablauf (bitte offiziell prüfen):

1. Bring dein Deutsch auf mindestens **B1–B2** und belege es.
2. Bewirb dich an Schulen/Programmen und hol dir eine **feste Zusage**.
3. Weise deinen Lebensunterhalt nach (**Sperrkonto** o. Ä.), regle die Krankenversicherung.
4. Beantrage bei der deutschen Auslandsvertretung das **Ausbildungsvisum** und vereinbare einen Termin.
5. Wandle es in Deutschland in einen Aufenthaltstitel um.

Für Optionen wie das Fachkräftevisum mit Jobangebot und das beschleunigte Verfahren hilft dir unser [Leitfaden zum Arbeitsvisum mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de). **Visumsschritte und Beträge ändern sich jährlich — prüfe immer die aktuelle Seite der deutschen Auslandsvertretung in deinem Land.**

## Nach der Ausbildung: Erlaubnis, Anerkennung und Karriere

Wenn du deine Ausbildung in Deutschland abschließt und die staatliche Prüfung bestehst, erhältst du direkt die **„Erlaubnis zum Führen der Berufsbezeichnung"** — ein separates Anerkennungsverfahren ist dann nicht nötig. (Die Anerkennung betrifft nur, wer **im Ausland ausgebildet** wurde.)

Am Arbeitsmarkt sind die Aussichten gut: Wegen der **alternden Bevölkerung ist die Nachfrage nach Physiotherapeuten sehr hoch** und die Jobsicherheit stark. Praxen, Krankenhäuser, **Reha-Kliniken**, Sportvereine und später eine eigene Praxis stehen dir offen. Für die ehrlichen Details zu Gehalt, Sprache und Arbeitsalltag lies unbedingt unseren Beitrag [Als Physiotherapeut in Deutschland arbeiten: Gehalt, Sprache und Realität](/de/blog/working-as-a-physiotherapist-in-germany-salary-language-and-reality-de).

## Fazit & ehrlicher Rat

Startest du bei null, ist die **3-jährige Ausbildung der zugänglichste Weg** — aber denk daran, dass sie nicht immer vergütet ist und manche Privatschulen Schulgeld verlangen; **prüfe vor der Schulwahl Kosten und Sprachanforderung einzeln.** Willst du langfristig akademische Türen offenhalten und hast eine Abitur-Gleichwertigkeit, kann der **Bachelor-/duale Weg** die stärkere Investition sein. In beiden Fällen gilt dasselbe: **zuerst Deutsch (B2), dann der Platz.** Hast du bereits einen Abschluss, bilde nicht neu aus — der Anerkennungsweg ist für dich viel schneller.

*Dieser Beitrag dient der allgemeinen Information mit Stand Anfang 2026; Kosten, Visumsschritte und Programmvoraussetzungen sind ungefähr und ändern sich mit der Zeit. Für verbindliche, aktuelle Angaben wende dich an die zuständige Anerkennungs-/Bildungsbehörde des jeweiligen Bundeslandes, an deine Schule und an die deutsche Auslandsvertretung in deinem Land.*
MD;
        $enBody = <<<'MD'
Do you want to become a physiotherapist in Germany but aren't sure which training to take? Good news: **physiotherapy in Germany is a regulated health profession with no academic requirement.** A university degree isn't mandatory — but there are two paths, and you should know which one fits you.

This article is for **beginners with no prior qualification.** **Already a physiotherapist?** Then you don't need to retrain from scratch; instead, read our [guide to getting your foreign qualification recognized (Anerkennung)](/en/blog/getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung-en), because your route is much shorter. For the big picture, our [guide to becoming a physiotherapist in Germany](/en/blog/becoming-a-physiotherapist-in-germany-as-a-foreigner-en) is a good start.

## Two Paths: 3-Year Ausbildung or Bachelor's?

There are two official routes to becoming a physiotherapist. Both make you a **state-recognized physiotherapist (staatlich anerkannter Physiotherapeut)** in the end, but they differ in entry, duration and cost.

| Criterion | 3-Year Ausbildung | Bachelor's Degree |
| --- | --- | --- |
| Where | Berufsfachschule / physiotherapy school | University of applied sciences / university |
| Duration | **~3 years** | ~3–4 years (some dual/integrated) |
| Award | State exam + licence to use the title | Bachelor of Science + (usually) the licence |
| Cost | **Schulgeld (tuition)** at some private schools; state schools often free | Usually only a semester fee at public universities |
| Entry | Secondary school diploma | Usually Abitur/Fachabitur or equivalent |
| Language | **B2 German (in practice required)** | **B2 German (in practice required)** |

**Key truth:** English-taught physiotherapy programs in Germany are **almost non-existent.** Because you talk constantly with patients, families and your team, **B2 German is effectively required on both paths.**

## The Classic Path: 3-Year Ausbildung (and the Schulgeld Reality)

The most common and accessible route is the three-year school-based training at a **Berufsfachschule** or **physiotherapy school.** Theory (anatomy, physiology, pathology) and practice (Praktika) are interwoven; at the end you sit the **state exam (staatliche Prüfung)** and, on passing, receive the licence to use the professional title.

Here's an honest point: unlike nursing Ausbildung, **physiotherapy Ausbildung is not always paid**, and some schools — especially **private ones — charge Schulgeld (tuition).** This fee varies widely from school to school; at some state/public schools the training is free. So **verify each school's cost situation individually before applying.**

If you want to understand the Ausbildung system in general, our [guide to what an Ausbildung is](/en/blog/what-is-ausbildung-dual-vocational-training-in-germany-for-foreigners-en) explains the whole thing. If you're after a paid alternative, compare with our [nursing Ausbildung article](/en/blog/nursing-ausbildung-in-germany-for-internationals-paid-training-en) — there you're paid throughout the training.

## The New Path: Bachelor's and Dual Programs (Academization)

Germany is going through a process of **academization (Akademisierung)** of physiotherapy. More and more universities offer **bachelor's programs in physiotherapy.** Some are **"ausbildungsintegrierend"** — i.e. **dual**, combining the school-based training with the bachelor's; others are classic academic degrees.

The advantage of the bachelor's route: a degree that is **more easily recognized internationally**, plus open doors to research and specialization. The downside: it usually requires the **Abitur/Fachabitur** or an equivalent, and the number of places is more limited than at Ausbildung schools. **Always check whether a program grants graduates the licence directly** — some bachelor's include it, others require an extra step.

## Requirements: B2 German + School Diploma + a Place

Whichever path you pick, three things are almost always needed:

- **Language:** usually **B2 German** (some schools accept B1 at the start and require B2 later). This is the non-negotiable condition.
- **School diploma:** for the Ausbildung, a secondary/high-school diploma; for the bachelor's, usually Abitur/Fachabitur or equivalent. Recognition of your foreign diploma (Zeugnisanerkennung) may be required.
- **A place (Platz):** to get a visa you first need a **firm acceptance** from a school. Finding a place is the hardest step — apply early and to many.

Take language planning seriously: structured German learning is the backbone of the whole process.

## Visa and Application

If you're from a non-EU country (e.g. Turkey), you apply for a **residence permit for training purposes.** The usual flow (verify officially):

1. Get your German to at least **B1–B2** and document it.
2. Apply to schools/programs and secure a **firm acceptance (Zusage)**.
3. Prove your means of living (a **blocked account/Sperrkonto** or equivalent), arrange health insurance.
4. Book an appointment and apply for the **training visa** at the German mission in your country.
5. Convert it into a residence permit once in Germany.

For options such as the skilled-worker visa with a job offer and the fast-track procedure, our [guide to the German work visa with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en) will help. **Visa steps and amounts change year to year — always verify on the current page of the German mission in your country.**

## After Training: Licence, Anerkennung and Career

If you complete your training in Germany and pass the state exam, you receive the **"Erlaubnis zum Führen der Berufsbezeichnung"** (licence to use the professional title) directly — no separate Anerkennung process is needed. (Anerkennung applies only to those **trained abroad.**)

The job market looks good: because of the **ageing population, demand for physiotherapists is very high** and job security is strong. Private practices, hospitals, **rehabilitation clinics**, sports clubs and, in time, your own practice are all on the table. For honest details on salary, language and daily working conditions, be sure to read our article [Working as a physiotherapist in Germany: salary, language and reality](/en/blog/working-as-a-physiotherapist-in-germany-salary-language-and-reality-en).

## Conclusion & Honest Advice

If you're starting from scratch, the **3-year Ausbildung is the most accessible path** — but remember it may not be paid and some private schools charge Schulgeld; **verify cost and language requirements school by school before choosing.** If you want to keep academic doors open long-term and hold an Abitur equivalent, the **bachelor's/dual route** may be the stronger investment. Either way, the core is the same: **German first (B2), then the place.** If you already hold a qualification, don't retrain — the recognition route is far faster for you.

*This article is general information as of early 2026; costs, visa steps and program requirements are approximate and change over time. For binding, up-to-date information, consult the relevant state recognition/education authority, your chosen school, and the German mission in your country.*
MD;

        $variants = [
            'tr' => ['slug'=>'physiotherapy-training-and-study-in-germany-for-internationals',    'title'=>'Almanya\'da Fizyoterapi Eğitimi: Ausbildung ve Bachelor Yolları (2026)', 'excerpt'=>'Almanya\'da sıfırdan fizyoterapist olmanın iki yolu: 3 yıllık Ausbildung (bazı özel okullarda Schulgeld/ücret gerçeği) ve yeni Bachelor/dual. Şartlar (B2 + lise + okul yeri), vize, başvuru ve eğitim sonrası kariyer — dürüst rehber.', 'meta_title'=>'Almanya Fizyoterapi Eğitimi: Ausbildung & Bachelor (2026)', 'meta_description'=>'Almanya\'da fizyoterapi eğitimi: 3 yıllık Ausbildung vs Bachelor/dual, Schulgeld gerçeği, B2 + lise + okul yeri şartı, vize ve kariyer. Rakamlar hedge\'li — resmi doğrula.', 'body'=>$trBody],
            'de' => ['slug'=>'physiotherapy-training-and-study-in-germany-for-internationals-de', 'title'=>'Physiotherapie-Ausbildung in Deutschland: Ausbildung & Bachelor (2026)', 'excerpt'=>'Zwei Wege zum Physiotherapeuten in Deutschland: die 3-jährige Ausbildung (an manchen Privatschulen Schulgeld) und der neue Bachelor/duale Weg. Voraussetzungen (B2 + Schulabschluss + Platz), Visum, Bewerbung und Karriere — ein ehrlicher Leitfaden.', 'meta_title'=>'Physiotherapie in Deutschland lernen: Ausbildung & Bachelor (2026)', 'meta_description'=>'Physiotherapie-Ausbildung in Deutschland: 3-jährige Ausbildung vs. Bachelor/dual, Schulgeld-Realität, B2 + Schulabschluss + Platz, Visum und Karriere. Zahlen ungefähr — offiziell prüfen.', 'body'=>$deBody],
            'en' => ['slug'=>'physiotherapy-training-and-study-in-germany-for-internationals-en', 'title'=>'Physiotherapy Training and Study in Germany: Ausbildung & Bachelor (2026)', 'excerpt'=>'Two routes to becoming a physiotherapist in Germany from scratch: the 3-year Ausbildung (with the Schulgeld/tuition reality at some private schools) and the new bachelor/dual path. Requirements (B2 + school diploma + a place), visa, application and career — an honest guide.', 'meta_title'=>'Physiotherapy Training & Study in Germany: Ausbildung & Bachelor', 'meta_description'=>'Physiotherapy training in Germany: 3-year Ausbildung vs bachelor/dual, the Schulgeld reality, B2 + school diploma + a place, visa and career. Figures hedged — verify officially.', 'body'=>$enBody],
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
            'physiotherapy-training-and-study-in-germany-for-internationals',
            'physiotherapy-training-and-study-in-germany-for-internationals-de',
            'physiotherapy-training-and-study-in-germany-for-internationals-en',
        ])->delete();
    }
};

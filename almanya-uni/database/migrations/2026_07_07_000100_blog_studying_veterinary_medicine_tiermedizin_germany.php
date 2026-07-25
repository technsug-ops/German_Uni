<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Veterinerlik (Tiermedizin) okumak (2026). Doğrulandı:
 * Tiermedizin 11 dönem (~5,5 yıl), Staatsexamen → Approbation, tamamen Almanca, İngilizce
 * program yok; sadece 5 üni (FU Berlin/LMU/TiHo Hannover/Giessen/Leipzig), NC ~1,0.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'a1b10000-1111-4dcf-9fe0-aa08bb0ddd01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da veteriner hekim olmak isteyen uluslararası öğrenciler için **Tiermedizin**, tutkuyla okunan ama girişi son derece zor bir yoldur. Bu rehber programın yapısını, neden sadece beş üniversitede okutulduğunu, Numerus Clausus gerçeğini ve başvuru adımlarını dürüstçe anlatır — pembe tablo çizmeden.

## Tiermedizin nedir? Yapı, süre ve Staatsexamen

Almanya'da veterinerlik eğitimi **11 dönem, yani yaklaşık 5,5 yıl** sürer ve federal **Tierärztliche Approbationsverordnung (TAppV)** ile düzenlenir. Program klasik bir bachelor/master değildir: bir **devlet sınavıyla (Staatsexamen)** biter. Bu sınavı geçince **Approbation als Tierarzt** — yani mesleği icra ruhsatı — alırsın.

**En kritik gerçek şu: Tiermedizin tamamen Almancadır ve Almanya'da İngilizce dilinde veterinerlik programı YOKTUR.** Tıp ve diş hekimliğinde olduğu gibi, dersler, klinik uygulamalar ve sınavlar Almanca yürütülür. Dolayısıyla "önce İngilizce okurum, Almancayı sonra hallederim" planı burada işlemez.

Program yapı olarak iki büyük bölüme ayrılır: önce anatomi, fizyoloji, biyokimya gibi temel bilimlerin ağırlıkta olduğu **preklinik (vorklinischer Abschnitt)**, ardından iç hastalıkları, cerrahi, doğum, patoloji ve tür-spesifik dersleri kapsayan **klinik (klinischer Abschnitt)**. Eğitim boyunca zorunlu **stajlar (Praktika)** vardır: kliniklerde, muayenehanelerde, mezbaha/gıda denetiminde ve çiftliklerde pratik yaparsın. Staatsexamen tek bir sınav değil, farklı derslerde yayılan sınav bloklarından oluşur; bu yüzden tempoyu 5,5 yıl boyunca yüksek tutmak gerekir. Bu, yarım bırakması pahalı, uzun soluklu bir maratondur.

## Sadece 5 üniversite ve NC ~1,0 gerçeği

Almanya'da veterinerlik yalnızca **beş üniversitede** okutulur: **FU Berlin, LMU München, TiHo Hannover** (Stiftung Tierärztliche Hochschule Hannover — sadece veterinerliğe adanmış uzman kurum), **Giessen (JLU)** ve **Leipzig**. Bu kadar az fakülte + yüksek talep = aşırı rekabet.

Sonuç, çok yüksek bir **Numerus Clausus (NC)**: giriş not barajı çoğu dönem **~1,0-1,5** civarındadır. Yani neredeyse tam not gerektirir. Kontenjanlar merkezi olarak (büyük ölçüde **hochschulstart.de** üzerinden) dağıtılır ve yabancı adaylar için ayrı bir kota kanalı işler. **Yer bulmanın zor olduğunu baştan kabul etmek, bu rehberin en dürüst kısmıdır.**

Türk ve diğer AB-dışı öğrenciler genelde her fakültenin uluslararası öğrencilere ayırdığı **sınırlı kontenjandan (Ausländerquote)** yarışır; bu kontenjan genellikle toplam yerlerin küçük bir yüzdesidir. Pratikte tek bir üniversiteye değil, uygun olan tüm fakültelere aynı anda başvurmak akıllıca olur. Yine de garantisi yoktur: güçlü bir not ortalaması ve kusursuz bir dosya bile kesin kabul anlamına gelmez. Bu yüzden başvurunun yanında gerçekçi bir **B planı** (başka bir AB ülkesinde okuyup sonra Almanya'ya geçiş, ya da zaten veterinersen tanınma yolu) düşünmek şarttır.

## Başvuru yolu: uni-assist ve Studienkolleg (M-Kurs)

Türk lise diploman genelde doğrudan üniversiteye giriş için yeterli sayılmaz. Adımlar tipik olarak şöyle:

1. Diplomanın denkliğini kontrol et (**anabin/KMK**); gerekirse **[Studienkolleg](/tr/blog/studienkolleg-is-not-a-language-school-what-it-really-is)** yolundan geç.
2. Veterinerlik tıp/fen ağırlıklı olduğu için doğru Studienkolleg kursu genelde **M-Kurs** (tıp/biyoloji odaklı) olur; sonunda **Feststellungsprüfung**'u geçersin.
3. Başvuruları **uni-assist** üzerinden veya ilgili üniversitenin belirttiği kanaldan yap; belge, dil sertifikası ve not ortalamasını hazırla.

Bu, tıp okumaya çok benzeyen bir süreçtir; ayrıntılar için **[yabancı olarak Almanya'da tıp okumak](/tr/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas)** rehberi de faydalı bir kıyas sunar.

## C1 Almanca ve mesleki dil

Program tamamen Almanca olduğundan, dil yeterliliği pazarlık konusu değildir. Pratikte **en az C1 seviyesi** (DSH-2 veya TestDaF TDN 4) beklenir; kliniklerde hasta sahibiyle iletişim ve tıbbi terminoloji için **mesleki Almanca** da şarttır. Dil, başvuruyu yapabilmenin ön koşuludur — sonradan telafi edilecek bir ayrıntı değil.

Gerçekçi ol: sıfırdan B2/C1 seviyesine ulaşmak çoğu kişide yoğun çalışmayla **1,5-2 yıl** alır. Bu süreyi başvuru takvimine baştan yedirmen gerekir; çünkü hem Studienkolleg hem de üniversite başvurusu geçerli bir dil sertifikası ister. Anatomi ve fizyoloji gibi derslerin Almanca terminolojisi ağırdır; günlük Almancayı iyi konuşmak yetmez, akademik ve tıbbi dile de hâkim olmalısın. Dolayısıyla dil hazırlığını "yan iş" değil, projenin merkezine koy.

## Beş fakülte bir bakışta

| Üniversite | Şehir / Eyalet | Not |
|---|---|---|
| **FU Berlin** | Berlin | Fachbereich Veterinärmedizin, güçlü klinik ağı |
| **LMU München** | München / Bayern | Non-EU öğrenciye Bayern harcı uygulanabilir |
| **TiHo Hannover** | Hannover / Niedersachsen | Sadece veterinerliğe adanmış uzman kurum |
| **JLU Giessen** | Gießen / Hessen | Hessen'in tek veterinerlik fakültesi |
| **Universität Leipzig** | Leipzig / Sachsen | Doğu Almanya'nın köklü fakültesi |

*Kontenjan, NC ve başvuru kanalları döneme göre değişir; her zaman ilgili fakültenin güncel sayfasından doğrula (2025/2026 itibarıyla, yaklaşık).*

## Ücret ve yaşam masrafı

Çoğu kamu üniversitesinde öğrenim ücreti yoktur; sadece **dönemlik Semesterbeitrag (~150-350€)** ödenir. İstisna: **Baden-Württemberg**, AB-dışı öğrencilerden dönem başına **~1.500€** alır (ama BW'de veterinerlik fakültesi yok, yani bu daha çok genel kural). Asıl yük **yaşam masrafıdır**: aylık **~950-1.100€** (kira, sigorta, geçim) gerçekçi bir bütçedir ve vize için bloke hesap (Sperrkonto) şartı bununla bağlantılıdır. Rakamlar 2025/2026 itibarıyla yaklaşıktır; doğrula.

## Sonuç ve dürüst tavsiye

Tiermedizin, hayvan sağlığına tutkun biri için harika bir meslek. Ama uluslararası öğrenci gözünden en dürüst özet şu: **sadece 5 üniversite ve NC ~1,0 nedeniyle okuma yeri almak aşırı zordur** ve süreç tamamen Almanca ilerler.

Önemli bir ayrım: **eğer sen zaten kendi ülkende veteriner hekimsen, sıfırdan okumak yerine diploma tanınması (Approbation) yolu çok daha gerçekçidir.** O senaryodaysan **[yurtdışı veteriner olarak Almanya'da Approbation ve tanınma](/tr/blog/foreign-veterinarian-in-germany-approbation-and-recognition)** yazısı asıl yolunu anlatır. Meslek hayatını, maaşı ve klinik gerçekleri merak ediyorsan **[Almanya'da veteriner olarak çalışmak: maaş ve kariyer](/tr/blog/working-as-a-veterinarian-in-germany-salary-career-and-practice)**; "gerçekten değer mi?" sorusunu tartan dürüst değerlendirme için **[Almanya'da veterinerlik okumaya değer mi?](/tr/blog/is-studying-veterinary-medicine-in-germany-worth-it-honest-reality)** yazılarına bak.

Kısacası: liseden geliyorsan ve notların çok güçlüyse dene — ama B planını (başka AB ülkesi ya da tanınma yolu) baştan düşün. Zaten veterinersen, doğrudan tanınma sürecine odaklan.

*Bu içerik 2026 başı itibarıyla genel bilgilendirme amaçlıdır; kurallar, NC değerleri, ücretler ve başvuru kanalları değişebilir. Bağlayıcı bilgi için ilgili üniversitenin, hochschulstart.de'nin ve resmi makamların güncel sayfalarını doğrula.*
MD;

        $deBody = <<<'MD'
Für internationale Studieninteressierte, die in Deutschland Tierärztin oder Tierarzt werden wollen, ist das **Tiermedizin**-Studium ein Herzensweg — aber der Zugang ist extrem schwer. Dieser Leitfaden erklärt ehrlich den Aufbau, warum es nur fünf Fakultäten gibt, die Numerus-Clausus-Realität und deinen Bewerbungsweg.

## Was ist Tiermedizin? Aufbau, Dauer und Staatsexamen

Das Studium der Tiermedizin dauert **11 Semester, also rund 5,5 Jahre**, und ist bundesweit durch die **Tierärztliche Approbationsverordnung (TAppV)** geregelt. Es ist kein klassisches Bachelor-/Master-Studium: Es endet mit dem **Staatsexamen**. Bestehst du dieses, erhältst du die **Approbation als Tierarzt** — deine Berufszulassung.

**Die wichtigste Wahrheit: Tiermedizin ist vollständig auf Deutsch, und es gibt in Deutschland KEIN englischsprachiges Tiermedizin-Studium.** Wie in der Human- und Zahnmedizin laufen Vorlesungen, klinische Praktika und Prüfungen auf Deutsch. Der Plan „erst auf Englisch, Deutsch später" funktioniert hier nicht.

Das Studium gliedert sich in zwei große Abschnitte: zuerst den **vorklinischen Abschnitt** mit Grundlagenfächern wie Anatomie, Physiologie und Biochemie, danach den **klinischen Abschnitt** mit Innerer Medizin, Chirurgie, Geburtshilfe, Pathologie und tierartspezifischen Fächern. Über das Studium verteilt gibt es verpflichtende **Praktika** — in Kliniken, Praxen, in der Schlachthof- und Lebensmittelüberwachung sowie in landwirtschaftlichen Betrieben. Das Staatsexamen ist keine einzelne Prüfung, sondern verteilt sich über mehrere Fächer und Blöcke; das Tempo bleibt über 5,5 Jahre hoch. Es ist ein langer Marathon, dessen Abbruch teuer ist.

## Nur 5 Universitäten und die NC-~1,0-Realität

Tiermedizin wird in Deutschland nur an **fünf Standorten** angeboten: **FU Berlin, LMU München, TiHo Hannover** (Stiftung Tierärztliche Hochschule Hannover — eine reine Spezialhochschule für Tiermedizin), **Gießen (JLU)** und **Leipzig**. So wenige Fakultäten bei hoher Nachfrage bedeuten extremen Wettbewerb.

Die Folge ist ein sehr hoher **Numerus Clausus (NC)**: Der Grenzwert liegt in vielen Semestern bei **~1,0-1,5**, verlangt also fast eine Bestnote. Die Plätze werden zentral (überwiegend über **hochschulstart.de**) vergeben, für ausländische Bewerber läuft ein eigenes Quotenverfahren. **Zu akzeptieren, dass ein Studienplatz schwer zu bekommen ist, ist der ehrlichste Teil dieses Leitfadens.**

Türkische und andere Non-EU-Studierende konkurrieren meist um die **begrenzte Ausländerquote**, die jede Fakultät für internationale Bewerber reserviert — in der Regel nur ein kleiner Prozentsatz der Plätze. In der Praxis ist es klug, sich nicht nur an einer, sondern parallel an allen infrage kommenden Fakultäten zu bewerben. Eine Garantie gibt es trotzdem nicht: Selbst ein starker Notendurchschnitt und eine makellose Bewerbung bedeuten keine sichere Zusage. Deshalb ist neben der Bewerbung ein realistischer **Plan B** unverzichtbar (Studium in einem anderen EU-Land mit späterem Wechsel nach Deutschland oder, falls du bereits Tierarzt bist, der Anerkennungsweg).

## Bewerbungsweg: uni-assist und Studienkolleg (M-Kurs)

Ein türkisches Abiturzeugnis reicht oft nicht für den direkten Hochschulzugang. Die Schritte sind typischerweise:

1. Prüfe die Gleichwertigkeit deines Zeugnisses (**anabin/KMK**); bei Bedarf gehst du über das **[Studienkolleg](/de/blog/studienkolleg-is-not-a-language-school-what-it-really-is-de)**.
2. Da Tiermedizin medizinisch-naturwissenschaftlich geprägt ist, ist der passende Kurs meist der **M-Kurs** (medizinisch-biologisch); am Ende bestehst du die **Feststellungsprüfung**.
3. Bewirb dich über **uni-assist** bzw. den von der Hochschule genannten Weg; halte Zeugnisse, Sprachnachweis und Notendurchschnitt bereit.

Der Ablauf ähnelt stark dem Medizinstudium; als Vergleich hilft auch der Leitfaden **[als Ausländer Medizin in Deutschland studieren](/de/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-de)**.

## C1-Deutsch und Fachsprache

Da das Studium komplett auf Deutsch läuft, ist die Sprache nicht verhandelbar. In der Praxis wird **mindestens C1** (DSH-2 oder TestDaF TDN 4) erwartet; für die Kommunikation mit Tierhaltern und die medizinische Terminologie in der Klinik brauchst du zudem **fachsprachliches Deutsch**. Sprache ist die Voraussetzung, um dich überhaupt bewerben zu können — kein Detail, das man später nachholt.

## Die fünf Fakultäten im Überblick

| Universität | Stadt / Bundesland | Hinweis |
|---|---|---|
| **FU Berlin** | Berlin | Fachbereich Veterinärmedizin, starkes Kliniknetz |
| **LMU München** | München / Bayern | Für Non-EU ggf. bayerische Gebühren |
| **TiHo Hannover** | Hannover / Niedersachsen | Reine Spezialhochschule für Tiermedizin |
| **JLU Gießen** | Gießen / Hessen | Einzige tiermedizinische Fakultät Hessens |
| **Universität Leipzig** | Leipzig / Sachsen | Traditionsreiche Fakultät im Osten |

*Kapazität, NC und Bewerbungswege ändern sich je Semester; prüfe stets die aktuelle Seite der jeweiligen Fakultät (Stand 2025/2026, ungefähr).*

## Gebühren und Lebenshaltung

An den meisten staatlichen Universitäten gibt es keine Studiengebühren; du zahlst nur den **Semesterbeitrag (~150-350€)** pro Semester. Ausnahme: **Baden-Württemberg** verlangt von Non-EU-Studierenden **~1.500€** pro Semester (dort gibt es aber keine tiermedizinische Fakultät, es ist also eher die allgemeine Regel). Die eigentliche Last ist die **Lebenshaltung**: rund **950-1.100€ im Monat** (Miete, Versicherung, Alltag) sind realistisch, und daran hängt auch das Sperrkonto fürs Visum. Zahlen sind Stand 2025/2026 ungefähr; bitte prüfen.

## Fazit und ehrlicher Rat

Tiermedizin ist ein wunderbarer Beruf für alle, die für Tiergesundheit brennen. Aus Sicht internationaler Studierender lautet die ehrlichste Zusammenfassung: **Wegen nur 5 Fakultäten und einem NC von ~1,0 ist ein Studienplatz extrem schwer zu bekommen**, und alles läuft auf Deutsch.

Eine wichtige Unterscheidung: **Wenn du in deinem Heimatland bereits Tierarzt bist, ist die Anerkennung (Approbation) ein viel realistischerer Weg als ein Neustart im Studium.** In diesem Fall erklärt dir der Beitrag **[als ausländische Tierärztin/Tierarzt: Approbation und Anerkennung](/de/blog/foreign-veterinarian-in-germany-approbation-and-recognition-de)** den eigentlichen Weg. Für Gehalt und Praxisalltag lies **[als Tierarzt in Deutschland arbeiten: Gehalt und Karriere](/de/blog/working-as-a-veterinarian-in-germany-salary-career-and-practice-de)**; für die Frage „lohnt es sich wirklich?" die ehrliche Einschätzung in **[lohnt sich ein Tiermedizinstudium in Deutschland?](/de/blog/is-studying-veterinary-medicine-in-germany-worth-it-honest-reality-de)**.

Kurz gesagt: Kommst du frisch aus der Schule und hast sehr gute Noten, versuch es — aber plane den Plan B (anderes EU-Land oder Anerkennungsweg) von Anfang an mit. Bist du schon Tierarzt, konzentriere dich direkt auf die Anerkennung.

*Dieser Beitrag dient der allgemeinen Information (Stand Anfang 2026); Regeln, NC-Werte, Gebühren und Bewerbungswege können sich ändern. Verbindliche Auskünfte findest du auf den aktuellen Seiten der jeweiligen Universität, von hochschulstart.de und der zuständigen Behörden.*
MD;

        $enBody = <<<'MD'
For international students who want to become a veterinarian in Germany, **Tiermedizin** (veterinary medicine) is a passion path — but access is extremely hard. This guide honestly explains the structure, why only five faculties exist, the Numerus Clausus reality and your application route, without sugar-coating.

## What is Tiermedizin? Structure, duration and the Staatsexamen

Veterinary medicine in Germany lasts **11 semesters, about 5.5 years**, and is regulated nationally by the **Tierärztliche Approbationsverordnung (TAppV)**. It is not a classic bachelor's/master's programme: it ends with the **Staatsexamen**. Pass it, and you receive the **Approbation als Tierarzt** — your licence to practise.

**The most important truth: Tiermedizin is taught entirely in German, and there is NO English-language veterinary programme in Germany.** As in human and dental medicine, lectures, clinical rotations and exams are all in German. The plan "study in English first, learn German later" does not work here.

Structurally the degree splits into two large phases: first the **pre-clinical stage (vorklinischer Abschnitt)** with foundational subjects such as anatomy, physiology and biochemistry, then the **clinical stage (klinischer Abschnitt)** covering internal medicine, surgery, obstetrics, pathology and species-specific subjects. Throughout, there are mandatory **placements (Praktika)** — in clinics, practices, slaughterhouse/food inspection and on farms. The Staatsexamen is not a single test but a series of exam blocks spread across subjects, so you must keep the pace high across all 5.5 years. It is a long marathon that is costly to abandon halfway.

## Only 5 universities and the NC ~1.0 reality

Veterinary medicine is offered at only **five locations** in Germany: **FU Berlin, LMU München, TiHo Hannover** (Stiftung Tierärztliche Hochschule Hannover — a specialist institution devoted solely to veterinary medicine), **Giessen (JLU)** and **Leipzig**. So few faculties plus high demand means extreme competition.

The result is a very high **Numerus Clausus (NC)**: the entry threshold sits around **~1.0-1.5** in many semesters, meaning you need close to a top grade. Places are allocated centrally (largely via **hochschulstart.de**), with a separate quota channel for international applicants. **Accepting that a study place is hard to get is the most honest part of this guide.**

Turkish and other non-EU students usually compete for the **limited international quota (Ausländerquote)** that each faculty reserves for foreign applicants — typically only a small percentage of the places. In practice it is wise to apply not to a single university but to all eligible faculties in parallel. Even so, there is no guarantee: a strong grade average and a flawless application still do not mean a certain offer. That is why, alongside the application, a realistic **plan B** is essential (studying in another EU country and transferring to Germany later, or, if you are already a vet, the recognition route).

## Application route: uni-assist and the Studienkolleg (M-Kurs)

A Turkish high-school diploma is often not enough for direct university entry. The steps are typically:

1. Check the equivalence of your diploma (**anabin/KMK**); if needed, go through the **[Studienkolleg](/en/blog/studienkolleg-is-not-a-language-school-what-it-really-is-en)**.
2. Because veterinary medicine is medical/science-heavy, the right track is usually the **M-Kurs** (medical/biological); you finish by passing the **Feststellungsprüfung**.
3. Apply via **uni-assist** or the channel the university specifies; prepare transcripts, language certificate and grade average.

The process closely resembles studying medicine; as a comparison, the guide on **[studying medicine in Germany as a foreigner](/en/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-en)** is useful.

## C1 German and professional language

Since the programme is entirely in German, language is non-negotiable. In practice you need **at least C1** (DSH-2 or TestDaF TDN 4), plus **professional German** for talking to animal owners and handling clinical terminology. Language is the precondition for even being able to apply — not a detail to fix later.

## The five faculties at a glance

| University | City / State | Note |
|---|---|---|
| **FU Berlin** | Berlin | Fachbereich Veterinärmedizin, strong clinical network |
| **LMU München** | Munich / Bavaria | Bavarian fees may apply to non-EU students |
| **TiHo Hannover** | Hanover / Lower Saxony | Specialist institution devoted solely to veterinary medicine |
| **JLU Giessen** | Gießen / Hesse | The only veterinary faculty in Hesse |
| **Universität Leipzig** | Leipzig / Saxony | Long-established faculty in eastern Germany |

*Capacity, NC and application channels vary by semester; always verify on the relevant faculty's current page (as of 2025/2026, approximate).*

## Fees and living costs

Most public universities charge no tuition; you pay only the **Semesterbeitrag (~€150-350)** per semester. Exception: **Baden-Württemberg** charges non-EU students **~€1,500** per semester (but there is no veterinary faculty there, so this is more of a general rule). The real burden is **living costs**: about **€950-1,100 per month** (rent, insurance, daily life) is realistic, and the blocked account (Sperrkonto) for your visa is tied to this. Figures are approximate as of 2025/2026; please verify.

## Conclusion and honest advice

Tiermedizin is a wonderful profession for anyone passionate about animal health. From an international student's perspective, the most honest summary is: **because there are only 5 faculties and an NC of ~1.0, a study place is extremely hard to obtain**, and everything runs in German.

An important distinction: **if you are already a veterinarian in your home country, recognition (Approbation) is a far more realistic route than starting the degree from scratch.** In that case, the article on **[being a foreign veterinarian in Germany: Approbation and recognition](/en/blog/foreign-veterinarian-in-germany-approbation-and-recognition-en)** explains your real path. For salary and clinical reality, read **[working as a veterinarian in Germany: salary and career](/en/blog/working-as-a-veterinarian-in-germany-salary-career-and-practice-en)**; and for the "is it really worth it?" question, see the honest assessment in **[is studying veterinary medicine in Germany worth it?](/en/blog/is-studying-veterinary-medicine-in-germany-worth-it-honest-reality-en)**.

In short: if you are fresh out of school with very strong grades, try — but plan your plan B (another EU country or the recognition route) from the start. If you are already a vet, focus directly on recognition.

*This content is for general information as of early 2026; rules, NC values, fees and application channels may change. For binding information, verify the current pages of the relevant university, hochschulstart.de and the responsible authorities.*
MD;

        $variants = [
            'tr' => ['slug'=>'studying-veterinary-medicine-tiermedizin-in-germany-as-a-foreigner',    'title'=>'Almanya\'da Veterinerlik (Tiermedizin) Okumak: Uluslararası Öğrenci Rehberi (2026)', 'excerpt'=>'Almanya\'da veterinerlik (Tiermedizin) okumak: 5,5 yıl, Staatsexamen, İngilizce program yok, sadece 5 üni ve NC ~1,0 gerçeği. Başvuru, Studienkolleg M-Kurs, C1 Almanca ve ücretler — dürüst rehber.', 'meta_title'=>'Almanya\'da Veterinerlik (Tiermedizin) Okumak (2026)', 'meta_description'=>'Almanya\'da veterinerlik okumak: sadece 5 üni, NC ~1,0, tamamen Almanca, İngilizce program yok. Studienkolleg M-Kurs, C1 ve ücretler dahil dürüst rehber.', 'body'=>$trBody],
            'de' => ['slug'=>'studying-veterinary-medicine-tiermedizin-in-germany-as-a-foreigner-de', 'title'=>'Tiermedizin in Deutschland studieren: Leitfaden für internationale Studierende (2026)', 'excerpt'=>'Tiermedizin in Deutschland studieren: 5,5 Jahre, Staatsexamen, kein englischsprachiges Programm, nur 5 Fakultäten und NC ~1,0. Bewerbung, Studienkolleg M-Kurs, C1-Deutsch und Gebühren — ehrlich erklärt.', 'meta_title'=>'Tiermedizin in Deutschland studieren (2026)', 'meta_description'=>'Tiermedizin studieren: nur 5 Fakultäten, NC ~1,0, komplett auf Deutsch, kein englisches Programm. Studienkolleg M-Kurs, C1 und Gebühren — ehrlicher Leitfaden.', 'body'=>$deBody],
            'en' => ['slug'=>'studying-veterinary-medicine-tiermedizin-in-germany-as-a-foreigner-en', 'title'=>'Studying Veterinary Medicine (Tiermedizin) in Germany: A Guide for International Students (2026)', 'excerpt'=>'Studying veterinary medicine (Tiermedizin) in Germany: 5.5 years, Staatsexamen, no English-language programme, only 5 faculties and an NC of ~1.0. Application, Studienkolleg M-Kurs, C1 German and fees — an honest guide.', 'meta_title'=>'Studying Veterinary Medicine in Germany (2026)', 'meta_description'=>'Study veterinary medicine in Germany: only 5 faculties, NC ~1.0, fully in German, no English programme. Studienkolleg M-Kurs, C1 and fees — an honest guide.', 'body'=>$enBody],
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
            'studying-veterinary-medicine-tiermedizin-in-germany-as-a-foreigner',
            'studying-veterinary-medicine-tiermedizin-in-germany-as-a-foreigner-de',
            'studying-veterinary-medicine-tiermedizin-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};

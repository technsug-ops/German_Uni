<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Müzik & Sahne Sanatları Okumak (2026). Doğrulandı: Musikhochschule vs
 * tiyatro okulu ayrımı; kabulde NC yok → audition (Vorspiel/Aufnahmeprüfung) belirleyici; kamu
 * konservatuvarları ücretsiz ama aşırı rekabetçi; oyunculuk/müzik eğitimi için C1 Almanca sık şart.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'e2f10000-1111-4c7f-9f80-ee12ff18cc01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da müzik ya da sahne sanatları okumak, tıp veya mühendislik okumaktan **tamamen farklı bir mantıkla** işler. Lise not ortalaman (Abitur) neredeyse hiç önemli değildir; belirleyici olan **audition'dur (Vorspiel / Vorsingen; oyunculukta Vorsprechen)**. Almanya dünyada en çok orkestra ve opera evine sahip ülke — yani fırsat gerçekten var, ama rekabet de acımasız. Bu rehber, uluslararası bir öğrenci olarak alanları, kurumları, dil şartını ve başvuru sürecini dürüstçe anlatıyor.

## Hangi alanlar var?

"Müzik & sahne sanatları" tek bir şey değil; birbirinden çok farklı eğitim, kültür ve kariyer beklentisi olan dallar var:

- **Enstrüman / şan performansı:** Klasik icra (piyano, yaylı, üflemeli, şan), en klasik konservatuvar yolu.
- **Kompozisyon & Dirigieren (şeflik):** Az kadro, yüksek prestij, çok rekabetçi.
- **Musikpädagogik (müzik eğitimi/pedagoji):** Öğretmenlik ve Musikschule yolu — **en stabil seçeneklerden biri**.
- **Kilise müziği (Kirchenmusik), Jazz/Pop:** Kendi bölümleri ve auditionları olan ayrı dünyalar.
- **Darstellende Kunst (sahne sanatları):** **Schauspiel (oyunculuk)**, **Tanz (dans)**, Musical ve **Regie (yönetmenlik)**.

Hangi dalı seçeceğin, hem gireceğin kurumu hem de mezuniyet sonrası gerçekliğini belirler. Kariyer ve gelir tarafının dürüst dökümü için [Almanya'da müzisyen/sanatçı olarak çalışmak](/tr/blog/working-as-a-musician-or-performer-in-germany-careers-salary-and-reality) yazısına bak.

## Kurumlar: Musikhochschule mı, tiyatro okulu mu?

İki temel dünya var ve aralarındaki fark önemli:

| Özellik | Musikhochschule (konservatuvar) | Tiyatro / sahne okulu |
|---|---|---|
| Odak | Enstrüman, şan, kompozisyon, şeflik, müzik eğitimi | Oyunculuk (Schauspiel), dans, yönetmenlik |
| Örnekler | **Hanns Eisler Berlin, HfM Hannover, München, Köln, Freiburg, Lübeck, Detmold, Stuttgart, Leipzig (Mendelssohn)** | **HfS Ernst Busch Berlin** (oyunculukta tepe), **Folkwang Essen** |
| Kabul | **Vorspiel / Vorsingen + teori/Gehörbildung sınavı** | **Vorsprechen (sahne denemesi) + fiziksel/ses çalışması** |
| Ücret | Kamuda **ücretsiz** (~dönem katkısı) | Kamuda **ücretsiz**; özel okullar pahalı |
| Rekabet | **Çok yüksek** — kabul oranı düşük | **Aşırı yüksek** — Ernst Busch'ta yüzlerce aday, birkaç kişilik kontenjan |

**Kalın gerçek:** Kamu konservatuvarları ve devlet tiyatro okulları neredeyse ücretsizdir, ama bunun bedelini **rekabet** olarak ödersin. Ernst Busch gibi bir okulda kontenjan bir elin parmaklarını geçmezken başvuran sayısı yüzlerce olabilir. Prestijli isim peşinde koşmadan önce, hangi hocanın ve hangi ekolün sana uyduğunu düşün; Almanya'da prestijin nasıl çalıştığını [üniversite prestiji ve sıralamalar](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) yazısında ele alıyoruz.

## En kritik fark: NC yok → audition belirleyici

Almanya'da popüler bölümlerin çoğu kontenjanı **Numerus Clausus (NC)** yani not ortalaması ile sınırlar. **Müzik ve sahne sanatlarında durum tamamen farklı: pratikte NC yoktur.** Onun yerine her şeyi belirleyen şey **audition'dur:**

1. **Vorspiel / Vorsingen (enstrüman/şan denemesi)** ya da oyunculukta **Vorsprechen (sahne denemesi):** hazırladığın repertuarı jüri önünde sunarsın. **Başarının anahtarı budur.**
2. **Teori ve Gehörbildung (kulak eğitimi) sınavı:** çoğu konservatuvar müzik teorisi ve işitme testi de uygular.

Yani orta düzey okul notlarıyla bile **güçlü bir audition'la** tepe bir okula girebilirsin; ama zayıf bir audition'ı mükemmel notlar kurtarmaz. Standart çok yüksektir ve kabul oranları düşüktür — bu, tıp veya mühendislikten tamamen farklı bir kabul mantığıdır. Audition'a nasıl hazırlanacağın başlı başına bir konu; adım adım rehberimiz olan [Alman konservatuvarları için audition (Aufnahmeprüfung) nasıl hazırlanır](/tr/blog/how-to-prepare-for-a-music-audition-aufnahmepruefung-at-german-conservatories) yazısını mutlaka oku.

## Dil: B2-C1 Almanca şart mı?

Cevap alana göre değişir:

- **Saf enstrümantal performans** daha uluslararasıdır; müziğin dili evrensel olduğu için bazı programlar İngilizceye daha esnektir.
- Ama **oyunculuk (Schauspiel), Alman tiyatrosu ve müzik eğitimi (Musikpädagogik)** için **Almanca çoğu zaman şarttır** — genelde **C1**. Sahnede Almanca metin oynayacaksan bunun alternatifi yoktur.
- Konservatuvarlar genelde en az **B2-C1 Almanca** ister; başvuru ve derslerin dili çoğunlukla Almancadır.

Kısacası: enstrümantalcı biraz daha rahat, ama oyunculuk/pedagoji tarafındaysan Almancaya erkenden yüklen.

## Başvuru: doğrudan başvuru ve Vorspiel takvimi

Süreç kuruma göre değişir, ama tipik akış şöyledir:

- Çoğu Musikhochschule ve tiyatro okulu başvuruyu **doğrudan kendisi** alır; repertuar listesini, ön kayıt tarihini ve **Vorspiel/Vorsprechen** gününü okul belirler.
- **Başvuru tarihleri erkendir** (audition günü genelde dönem başından aylar önce). Takvimi erken kontrol et.
- **Aynı anda birden fazla okula başvurmak** neredeyse bir zorunluluktur — kontenjanlar dar, bir tek okula bel bağlamak riskli.

Bu kabul ve hazırlık mantığı, Almanya'da sanat & tasarım okumaya çok benzer (orada da NC yok, belirleyici olan **portfolyo/Mappe**). Komşu alanı görmek istersen [Almanya'da sanat & tasarım okumak](/tr/blog/studying-art-and-design-in-germany-as-a-foreigner) iyi bir karşılaştırma.

## Ücret & yaşam masrafı

- **Kamu konservatuvarları ve devlet tiyatro okulları neredeyse ücretsizdir:** genelde sadece **~150-350€ dönem katkısı (Semesterbeitrag)**. Baden-Württemberg'de AB dışı öğrenciler için **~1.500€/dönem** istisnası olabilir (2025/2026, yaklaşık — doğrula).
- **Bazı özel müzik/sanat okulları pahalıdır** (yılda birkaç bin euro).
- Vize için **Sperrkonto** (bloke hesap) genelde **~992€/ay = 11.904€/yıl** civarındadır (2025/2026, yaklaşık — doğrula). Blue Card genel eşiği ~50.700€'dur ama sanat alanı için çoğunlukla ilgisizdir.
- Yaşam masrafı şehre göre değişir; büyük kültür merkezleri (Berlin, München, Hamburg) daha pahalıdır.

## Sonuç & dürüst tavsiye

Almanya müzik ve sahne sanatları için dünyada eşsiz bir yer — en çok orkestra ve opera evi, neredeyse ücretsiz kamu okulları, güçlü ekoller. Ama kendine karşı dürüst ol:

- **Audition her şeydir.** Notların değil, jüri önündeki performansın belirler. Erken hazırlan; ön-audition ve özel ders düşün.
- **Rekabet acımasız.** Aynı anda birkaç okula başvurmayı planla; tek okula bel bağlama.
- **Dil ciddi bir engel olabilir:** enstrümantalcı için esnek, ama **oyunculuk ve pedagoji için C1 Almanca** gerçek bir şart.
- **Gelir, tenured (kadrolu) pozisyon dışında güvencesizdir.** Fırsat var ama garanti yok. **Müzik eğitimi/pedagoji genelde daha stabil bir yoldur.** Bu kararı vermeden önce [müzik/sahne sanatları okumaya değer mi?](/tr/blog/is-studying-music-or-performing-arts-in-germany-worth-it-honest-reality) yazısındaki dürüst değerlendirmeye bak.

*Bu yazıdaki sayılar, program şartları ve ücretler 2025/2026 itibarıyla yaklaşık değerlerdir ve değişebilir. Başvurmadan önce ilgili okulun ve resmi kaynakların güncel bilgilerini mutlaka doğrula.*
MD;
        $deBody = <<<'MD'
Musik oder darstellende Kunst in Deutschland zu studieren funktioniert nach einer **völlig anderen Logik** als Medizin oder Ingenieurwesen. Deine Abiturnote spielt fast keine Rolle; entscheidend ist das **Vorspiel (bzw. Vorsingen; im Schauspiel das Vorsprechen)**. Deutschland hat weltweit die meisten Orchester und Opernhäuser – die Chance ist also real, aber die Konkurrenz ist gnadenlos. Dieser Leitfaden erklärt dir als internationaler Studentin die Fachrichtungen, Institutionen, die Sprachfrage und den Bewerbungsweg – ehrlich.

## Welche Fachrichtungen gibt es?

„Musik & darstellende Kunst" ist kein einzelnes Fach, sondern eine Familie sehr unterschiedlicher Richtungen mit ganz verschiedenen Einkommens- und Karrierechancen:

- **Instrumental- / Gesangsperformance:** Klassische Interpretation (Klavier, Streicher, Bläser, Gesang) – der klassischste Konservatoriumsweg.
- **Komposition & Dirigieren:** Wenige Stellen, hohes Prestige, sehr kompetitiv.
- **Musikpädagogik:** Der Weg in den Lehrberuf und an die Musikschule – **eine der stabilsten Optionen**.
- **Kirchenmusik, Jazz/Pop:** Eigene Abteilungen mit eigenen Aufnahmeprüfungen.
- **Darstellende Kunst:** **Schauspiel**, **Tanz**, Musical und **Regie**.

Welche Richtung du wählst, bestimmt sowohl die Hochschule als auch deine Realität nach dem Abschluss. Eine ehrliche Übersicht zu Karriere und Einkommen findest du im Beitrag [als Musiker:in oder Performer:in in Deutschland arbeiten](/de/blog/working-as-a-musician-or-performer-in-germany-careers-salary-and-reality-de).

## Institutionen: Musikhochschule oder Schauspielschule?

Es gibt zwei Grundwelten, und der Unterschied ist wichtig:

| Merkmal | Musikhochschule | Schauspiel- / Bühnenschule |
|---|---|---|
| Fokus | Instrument, Gesang, Komposition, Dirigieren, Musikpädagogik | Schauspiel, Tanz, Regie |
| Beispiele | **Hanns Eisler Berlin, HfM Hannover, München, Köln, Freiburg, Lübeck, Detmold, Stuttgart, Leipzig (Mendelssohn)** | **HfS Ernst Busch Berlin** (Spitze im Schauspiel), **Folkwang Essen** |
| Zulassung | **Vorspiel / Vorsingen + Theorie/Gehörbildung** | **Vorsprechen + körperliche/stimmliche Arbeit** |
| Kosten | Öffentlich **gebührenfrei** (~Semesterbeitrag) | Öffentlich **gebührenfrei**; Privatschulen teuer |
| Konkurrenz | **Sehr hoch** – niedrige Aufnahmequote | **Extrem hoch** – an der Ernst Busch Hunderte Bewerbungen, wenige Plätze |

**Fette Wahrheit:** Öffentliche Musikhochschulen und staatliche Schauspielschulen sind nahezu gebührenfrei – den Preis zahlst du in **Konkurrenz**. An einer Schule wie der Ernst Busch stehen wenigen Plätzen oft Hunderte Bewerbungen gegenüber. Lauf nicht nur dem prestigeträchtigen Namen hinterher; überlege, welche Professorin und welche Schule wirklich zu dir passt. Wie Prestige in Deutschland funktioniert, behandeln wir im Beitrag [Uni-Prestige und Rankings in Deutschland](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de).

## Der wichtigste Unterschied: kein NC → das Vorspiel entscheidet

Die meisten beliebten Studiengänge in Deutschland begrenzen die Plätze über den **Numerus Clausus (NC)**, also die Note. **In Musik und darstellender Kunst ist das völlig anders: praktisch gibt es keinen NC.** Stattdessen entscheidet das **Vorspiel:**

1. **Vorspiel / Vorsingen** bzw. im Schauspiel das **Vorsprechen:** Du präsentierst dein einstudiertes Repertoire vor einer Jury. **Das ist der Schlüssel zum Erfolg.**
2. **Theorie- und Gehörbildungsprüfung:** Die meisten Hochschulen prüfen zusätzlich Musiktheorie und Gehör.

Auch mit mittelmäßigen Schulnoten kommst du also mit einem **starken Vorspiel** an eine Spitzenhochschule – aber ein schwaches Vorspiel rettet dir keine perfekte Note. Das Niveau ist sehr hoch und die Aufnahmequoten sind niedrig – eine völlig andere Zulassungslogik als in Medizin oder Ingenieurwesen. Wie du dich auf das Vorspiel vorbereitest, ist ein Thema für sich; lies unbedingt unseren Schritt-für-Schritt-Leitfaden [wie du dich auf ein Vorspiel (Aufnahmeprüfung) an deutschen Konservatorien vorbereitest](/de/blog/how-to-prepare-for-a-music-audition-aufnahmepruefung-at-german-conservatories-de).

## Sprache: B2-C1 Deutsch nötig?

Die Antwort hängt vom Fach ab:

- **Reine Instrumentalperformance** ist internationaler; da die Sprache der Musik universell ist, sind manche Programme flexibler mit Englisch.
- Aber für **Schauspiel, deutsches Theater und Musikpädagogik** ist **Deutsch meist ein Muss** – in der Regel **C1**. Wenn du auf der Bühne deutschen Text spielst, gibt es dazu keine Alternative.
- Konservatorien verlangen meist mindestens **B2-C1 Deutsch**; Bewerbung und Unterricht laufen überwiegend auf Deutsch.

Kurz: Instrumentalistinnen haben es etwas leichter, aber im Schauspiel/in der Pädagogik lerne früh Deutsch.

## Bewerbung: direkt und der Vorspiel-Zeitplan

Der Ablauf hängt von der Hochschule ab, aber der typische Weg ist:

- Die meisten Musikhochschulen und Schauspielschulen nehmen die Bewerbung **direkt selbst** entgegen; Repertoireliste, Anmeldefrist und **Vorspiel-/Vorsprechtermin** legt die Hochschule fest.
- **Die Fristen sind früh** (der Vorspieltermin liegt oft Monate vor Semesterbeginn). Prüfe den Zeitplan früh.
- **Sich an mehreren Schulen gleichzeitig zu bewerben** ist fast Pflicht – die Plätze sind knapp, auf eine einzige Schule zu setzen ist riskant.

Diese Zulassungs- und Vorbereitungslogik ähnelt stark dem Kunst- und Designstudium in Deutschland (auch dort kein NC, entscheidend ist die **Mappe**). Zum Vergleich ist [Kunst & Design in Deutschland studieren](/de/blog/studying-art-and-design-in-germany-as-a-foreigner-de) ein guter Nachbar.

## Kosten & Lebenshaltung

- **Öffentliche Musikhochschulen und staatliche Schauspielschulen sind nahezu gebührenfrei:** meist nur **~150-350€ Semesterbeitrag**. Ausnahme: in Baden-Württemberg ggf. **~1.500€/Semester für Nicht-EU-Studierende** (Stand 2025/2026, ungefähr – bitte prüfen).
- **Einige private Musik-/Kunstschulen sind teuer** (mehrere tausend Euro pro Jahr).
- Für das Visum liegt das **Sperrkonto** meist bei **~992€/Monat = 11.904€/Jahr** (Stand 2025/2026, ungefähr – bitte prüfen). Die allgemeine Blue-Card-Schwelle liegt bei ~50.700€, ist für den Kunstbereich aber meist irrelevant.
- Die Lebenshaltung variiert je Stadt; große Kulturzentren (Berlin, München, Hamburg) sind teurer.

## Fazit & ehrlicher Rat

Deutschland ist für Musik und darstellende Kunst weltweit einzigartig – die meisten Orchester und Opernhäuser, nahezu gebührenfreie öffentliche Hochschulen, starke Schulen. Aber sei ehrlich zu dir:

- **Das Vorspiel ist alles.** Nicht deine Noten, sondern deine Leistung vor der Jury entscheidet. Bereite dich früh vor; denk über Probe-Vorspiele und Einzelunterricht nach.
- **Die Konkurrenz ist gnadenlos.** Plane, dich gleichzeitig an mehreren Schulen zu bewerben.
- **Sprache kann eine echte Hürde sein:** flexibel für Instrumentalistinnen, aber **C1 Deutsch für Schauspiel und Pädagogik** eine echte Voraussetzung.
- **Das Einkommen ist außerhalb fester (tenured) Stellen unsicher.** Die Chance ist da, aber keine Garantie. **Musikpädagogik ist meist der stabilere Weg.** Bevor du dich entscheidest, lies die ehrliche Einschätzung in [lohnt sich ein Musik-/Bühnenstudium in Deutschland?](/de/blog/is-studying-music-or-performing-arts-in-germany-worth-it-honest-reality-de).

*Die Zahlen, Zulassungsbedingungen und Gebühren in diesem Beitrag sind ungefähre Werte für 2025/2026 und können sich ändern. Prüfe vor der Bewerbung unbedingt die aktuellen Angaben der jeweiligen Hochschule und der offiziellen Quellen.*
MD;
        $enBody = <<<'MD'
Studying music or the performing arts in Germany works on a **completely different logic** than medicine or engineering. Your school grades (Abitur) matter almost not at all; what decides everything is the **audition (Vorspiel / Vorsingen; in acting, the Vorsprechen)**. Germany has more orchestras and opera houses than any other country in the world — so the opportunity is real, but the competition is brutal. This guide walks you, as an international student, through the fields, the institutions, the language question and the application route — honestly.

## Which fields are there?

"Music & performing arts" is not one thing; it is a family of very different paths with very different income and career outlooks:

- **Instrumental / vocal performance:** Classical interpretation (piano, strings, winds, voice) — the most classic conservatory route.
- **Composition & Dirigieren (conducting):** Few posts, high prestige, very competitive.
- **Musikpädagogik (music education/pedagogy):** The route into teaching and the Musikschule — **one of the most stable options**.
- **Kirchenmusik (church music), Jazz/Pop:** Separate departments with their own auditions.
- **Darstellende Kunst (performing arts):** **Schauspiel (acting)**, **Tanz (dance)**, musical theatre and **Regie (directing)**.

The direction you pick shapes both the institution you attend and your reality after graduation. For an honest breakdown of careers and income, see [working as a musician or performer in Germany](/en/blog/working-as-a-musician-or-performer-in-germany-careers-salary-and-reality-en).

## Institutions: Musikhochschule or acting school?

There are two basic worlds, and the difference matters:

| Feature | Musikhochschule (conservatory) | Acting / stage school |
|---|---|---|
| Focus | Instrument, voice, composition, conducting, music education | Acting (Schauspiel), dance, directing |
| Examples | **Hanns Eisler Berlin, HfM Hannover, Munich, Cologne, Freiburg, Lübeck, Detmold, Stuttgart, Leipzig (Mendelssohn)** | **HfS Ernst Busch Berlin** (top for acting), **Folkwang Essen** |
| Admission | **Vorspiel / Vorsingen + theory/Gehörbildung** | **Vorsprechen (audition) + physical/vocal work** |
| Fees | Public schools **free** (~semester contribution) | Public schools **free**; private schools expensive |
| Competition | **Very high** — low acceptance rate | **Extremely high** — Ernst Busch sees hundreds of applicants for a handful of places |

**Bold fact:** Public conservatories and state acting schools are nearly free — you pay the price in **competition**. At a school like Ernst Busch, a handful of places can face hundreds of applications. Do not just chase the prestigious name; think about which professor and which school actually fit you. How prestige works in Germany is covered in [how university prestige and rankings work](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en).

## The most critical difference: no NC → the audition decides

Most popular degrees in Germany cap places via the **Numerus Clausus (NC)** — that is, your grade average. **In music and the performing arts it is completely different: in practice there is no NC.** What decides everything instead is the **audition:**

1. **Vorspiel / Vorsingen** (instrument/voice), or the **Vorsprechen** in acting: you present your prepared repertoire in front of a jury. **This is the key to success.**
2. **Theory and Gehörbildung (ear training) exam:** most conservatories also test music theory and aural skills.

So even with mediocre school grades you can get into a top school with a **strong audition** — but a weak audition is not saved by perfect grades. The standard is very high and acceptance rates are low — a completely different admissions logic from medicine or engineering. How you prepare for the audition is a topic of its own; be sure to read our step-by-step guide, [how to prepare for a music audition (Aufnahmeprüfung) at German conservatories](/en/blog/how-to-prepare-for-a-music-audition-aufnahmepruefung-at-german-conservatories-en).

## Language: is B2-C1 German required?

The answer depends on the field:

- **Pure instrumental performance** is more international; because the language of music is universal, some programs are more flexible with English.
- But for **acting (Schauspiel), German theatre and music education (Musikpädagogik)**, **German is usually a must** — typically **C1**. If you will perform German text on stage, there is no alternative to it.
- Conservatories usually ask for at least **B2-C1 German**; the application and the teaching are mostly in German.

In short: instrumentalists have it a bit easier, but if you are on the acting/pedagogy side, load up on German early.

## Application: direct, and the audition timeline

The process depends on the institution, but the typical route is:

- Most Musikhochschulen and acting schools take the application **directly themselves**; the school sets the repertoire list, the registration deadline and the **Vorspiel/Vorsprechen** date.
- **Deadlines are early** (the audition date is often months before the semester starts). Check the timeline early.
- **Applying to several schools at once** is almost mandatory — places are scarce, and betting on a single school is risky.

This admission and preparation logic closely resembles studying art & design in Germany (there is no NC there either, and the **portfolio/Mappe** decides). If you want to compare, [studying art & design in Germany](/en/blog/studying-art-and-design-in-germany-as-a-foreigner-en) is a good neighbor.

## Fees & cost of living

- **Public conservatories and state acting schools are nearly free:** usually only a **~€150-350 semester contribution (Semesterbeitrag)**. Exception: in Baden-Württemberg possibly **~€1,500 per semester for non-EU students** (as of 2025/2026, approximate — verify).
- **Some private music/art schools are expensive** (several thousand euros a year).
- For the visa, the **Sperrkonto** (blocked account) is usually around **~€992/month = €11,904/year** (as of 2025/2026, approximate — verify). The general Blue Card threshold is ~€50,700 but is mostly irrelevant for the arts.
- Living costs vary by city; big cultural hubs (Berlin, Munich, Hamburg) are pricier.

## Conclusion & honest advice

Germany is unique in the world for music and the performing arts — the most orchestras and opera houses, nearly free public schools, strong traditions. But be honest with yourself:

- **The audition is everything.** Not your grades, but your performance in front of the jury decides. Prepare early; consider mock auditions and private lessons.
- **The competition is brutal.** Plan to apply to several schools at once; do not bet on a single school.
- **Language can be a real barrier:** flexible for instrumentalists, but **C1 German for acting and pedagogy** is a genuine requirement.
- **Income is insecure outside tenured posts.** The opportunity is there, but there is no guarantee. **Music education/pedagogy is usually the more stable path.** Before you decide, read the honest assessment in [is studying music or performing arts in Germany worth it?](/en/blog/is-studying-music-or-performing-arts-in-germany-worth-it-honest-reality-en).

*The figures, admission requirements and fees in this article are approximate values for 2025/2026 and may change. Before applying, always verify the current information from the relevant school and official sources.*
MD;

        $variants = [
            'tr' => ['slug'=>'studying-music-and-performing-arts-in-germany-as-a-foreigner',    'title'=>'Almanya\'da Müzik & Sahne Sanatları Okumak: Rehber (2026)', 'excerpt'=>'Almanya\'da müzik & sahne sanatları okumak: alanlar, Musikhochschule vs tiyatro okulu, NC yok → audition (Vorspiel) belirleyici, dil şartı, ücret ve dürüst tavsiye (2026).', 'meta_title'=>'Almanya\'da Müzik & Sahne Sanatları Okumak (2026)', 'meta_description'=>'Almanya\'da müzik & sahne sanatları: NC yok, audition (Vorspiel/Aufnahmeprüfung) belirleyici. Musikhochschule vs tiyatro okulu, C1 Almanca, ücret ve dürüst tavsiye.', 'body'=>$trBody],
            'de' => ['slug'=>'studying-music-and-performing-arts-in-germany-as-a-foreigner-de', 'title'=>'Musik & darstellende Kunst in Deutschland studieren: Leitfaden (2026)',        'excerpt'=>'Musik & darstellende Kunst in Deutschland studieren: Fachrichtungen, Musikhochschule vs. Schauspielschule, kein NC → Vorspiel entscheidet, Sprache, Kosten und ehrlicher Rat (2026).',   'meta_title'=>'Musik & darstellende Kunst in Deutschland studieren (2026)',  'meta_description'=>'Musik & darstellende Kunst in Deutschland: kein NC, das Vorspiel entscheidet. Musikhochschule vs. Schauspielschule, C1 Deutsch, Kosten und ehrlicher Rat.',   'body'=>$deBody],
            'en' => ['slug'=>'studying-music-and-performing-arts-in-germany-as-a-foreigner-en', 'title'=>'Studying Music & Performing Arts in Germany: A Guide (2026)',        'excerpt'=>'Studying music & performing arts in Germany: fields, Musikhochschule vs acting school, no NC → the audition (Vorspiel) decides, language, fees and honest advice (2026).',   'meta_title'=>'Studying Music & Performing Arts in Germany (2026)',  'meta_description'=>'Music & performing arts in Germany: no NC, the audition (Vorspiel/Aufnahmeprüfung) decides. Musikhochschule vs acting school, C1 German, fees and honest advice.',   'body'=>$enBody],
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
            'studying-music-and-performing-arts-in-germany-as-a-foreigner',
            'studying-music-and-performing-arts-in-germany-as-a-foreigner-de',
            'studying-music-and-performing-arts-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};

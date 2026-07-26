<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Alman konservatuvarları için audition (Aufnahmeprüfung/Vorspiel) nasıl hazırlanır (2026).
 * Doğrulandı: kamu Musikhochschule kabulünde NC yok — audition (Vorspiel/Vorsingen, oyunculukta Vorsprechen)
 * belirleyici; teori + Gehörbildung sınavı standart; B2-C1 Almanca sık şart. Rakamlar 2026 yaklaşık, doğrula.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'e2f20000-2222-4c7f-9f80-ee12ff18cc02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Alman konservatuvarında (Musikhochschule) kabulün tek bir kelimesi vardır: **audition**. Not ortalaman, lise diploman, kaç dil bildiğin — hiçbiri belirleyici değil. Kürsünün önünde 10-20 dakika çaldığında ya da söylediğinde jüri kararını verir. Bu yazı, enstrüman, şan ve oyunculuk adayları için Almanya'daki **Aufnahmeprüfung** (giriş sınavı) sürecini adım adım, somut biçimde anlatıyor.

## Audition neden her şey? (NC yok)
Tıp veya hukukta kabul notla (Numerus Clausus) olur. Müzikte **NC yoktur**: yeteneğin doğrudan, canlı olarak ölçülür. Kamu Musikhochschule'leri **ücretsizdir** (sadece dönem katkısı), ama tam da bu yüzden **acımasız derecede rekabetçidir** — bazı sınıflarda kontenjan tek hanede, başvuran sayısı yüzlerce olabilir.

**Sonuç şu:** hazırlığının %90'ı çalgının/sesinin başında geçmeli. CV güzelleştirmek, motivasyon mektubu cilalamak ikincil. Jüri şunu görmek ister: teknik hâkimiyet, müzikalite (sadece nota basmak değil, ifade), sahne duruşu ve **eğitilebilirlik** — yani seni bir öğrenci olarak geliştirebilecekleri potansiyel. Genç ve teknik olarak kusursuz olmandan çok, **doğru hoca ile büyüyebilecek** bir aday olman önemlidir.

Bağlamın tamamı için: [Almanya'da müzik & sahne sanatları okumak](/tr/blog/studying-music-and-performing-arts-in-germany-as-a-foreigner).

## Repertuar seçimi: en kritik strateji kararı
Repertuar, auditionun kalbidir. Her okul kendi **şartlarını (Anforderungen)** web sitesinde yayınlar — bunları kelimesi kelimesine oku. Genel çerçeve:

| Alan | Tipik audition talebi |
|---|---|
| Klasik enstrüman | Farklı dönemlerden 3-4 eser (ör. Barok + Klasik/Romantik + Modern), sıklıkla **etüt** + bir konçerto bölümü |
| Şan (klasik) | 3-5 eser: farklı dillerde arya + Lied (Almanca Lied sık istenir), farklı dönem/karakter |
| Piyano | Bach (prelüd-füg), bir klasik sonat bölümü, romantik/virtüöz bir eser, etüt |
| Jazz/Pop | Standart repertuar, doğaçlama (improvisation), sıklıkla ritim grubuyla veya solo |
| Oyunculuk (Schauspiel) | 2-4 **monolog** (klasik + modern, kontrast), sıklıkla şarkı + hareket/doğaçlama |

**Kalın gerçek:** Zor eser seçip *yarım* çalmaktansa, biraz daha kolay eseri **kusursuz ve müzikal** sunmak her zaman kazandırır. Jüri, kontrol edemediğin bir virtüözite gösterisini hemen fark eder. Repertuarını **kontrastı** gösterecek şekilde kur: teknik, lirik, ritmik farklı yönlerini görsünler. Bir eseri "ezber güvenli bölge" olarak seç — heyecandan en çok o taşır.

Oyunculukta monologlarını **yaşına ve tipine** uygun seç; 50 yaşındaki bir kral rolünü 19 yaşında oynamak risklidir. Şan ve oyunculukta **Almanca** repertuar/metin neredeyse her zaman beklenir.

## Süreç: başvuru → Vorspiel/Vorsprechen → sonuç
Tipik akış:

1. **Online başvuru (Bewerbung):** Çoğu okul için son tarih kışın (Aralık-Şubat), sınavlar ilkbaharda (Mart-Haziran). Bazı okullarda güz dönemi için de tarih vardır. **Başvuru ücreti** genelde 30-80€.
2. **Ön eleme (bazı okullarda):** Video kaydı istenebilir; kayıt kalitesi önemli ama **abartılı montaj yapma** — jüri canlıyı görmek ister.
3. **Vorspiel / Vorsingen / Vorsprechen (canlı sınav):** Enstrümanda "Vorspiel", şanda "Vorsingen", oyunculukta "Vorsprechen". 1-3 turdan oluşabilir; ilk turda kesilip "teşekkürler" denmesi normaldir — jüri ilk 2-3 dakikada çok şey anlar.
4. **Teori + Gehörbildung sınavı** (aşağıda).
5. **Sonuç:** Kabul (Zulassung), yedek liste veya ret. Kabul edilirsen sık sık belirli bir **hoca sınıfına (Klasse)** girmiş olursun — bu, ileride kiminle çalışacağını belirler.

**Not:** Enstrümantal performans nispeten uluslararasıdır (İngilizce esnek olabilir), ama oyunculuk, müzik eğitimi (Musikpädagogik) ve kilise müziği için genelde **B2-C1 Almanca** şarttır. Dil belgesi çoğu zaman kayıt için ayrıca istenir.

## Teori & Gehörbildung (kulak eğitimi) sınavı
Çoğu aday buna hazırlıksız yakalanır. Audition sadece çalmak değildir; ayrı bir **müzik teorisi + Gehörbildung** (işitsel eğitim / ear training) sınavı vardır. Tipik içerik:

- **Gehörbildung:** aralık (interval) tanıma, akor tipleri, ritim ve melodi dikte (duyduğunu notaya alma), *vom Blatt singen* (deşifre/prima vista okuma-söyleme).
- **Teori:** temel armoni, kadanslar, tonalite, form analizi; bazen küçük bir yazılı ödev.
- Bazı okullar kısa bir **piyano yan-beceri** (ikincil çalgı) ister; enstrüman adaylarının temel piyano bilmesi avantajdır.

**Kalın gerçek:** Teori/Gehörbildung sınırda kalırsa asıl çalgı çok iyi olsa bile riske girersin. Bu bölümü **aylar önceden** düzenli çalış — solfej ve dikte kas hafızası ister, gece önce açılmaz.

## Hazırlık: özel ders, ön-audition, zaman planı
En etkili üç kaldıraç:

- **Hedef hoca ile özel ders / Probestunde:** İdeali, başvurmadan önce sınavına gireceğin okuldaki (veya benzer seviyedeki) bir hocayla birkaç ders almak. Hem seni tanırlar hem de o okulun beklentisini öğrenirsin. Almanya'ya gelip **deneme dersi** istemek yaygın ve makbuldür.
- **Mock audition (ön-audition):** Repertuarını en az 5-10 kez **başkalarının önünde** çal. Sahne heyecanı ancak tekrarla ehlileşir. Kayıt al, izle, düzelt.
- **Birden fazla okula başvur:** Bu, kümenin en önemli taktiğidir. Tek okula bel bağlama; **4-6 okula** başvurmak kabul şansını ciddi artırır. Sınav tarihleri farklı şehirlerde çakışabilir, bu yüzden takvimi erken kur.

Örnek 12 ay zaman planı:

| Zaman | Odak |
|---|---|
| 12-9 ay önce | Okul + hoca listesi, şart tabloları, repertuar taslağı |
| 9-6 ay önce | Repertuarı sabitle, hoca ile düzenli ders, teori/Gehörbildung başlat |
| 6-3 ay önce | Mock audition'lar, video kayıt, Almanca sınav (gerekiyorsa) |
| 3-1 ay önce | Cila, sahne provası, seyahat/konaklama planı |
| Son 2 hafta | Dinlen, aşırı çalışma yok, program bütünlüğü |

Portföy mantığı burada tanıdık gelebilir — Sanat & Tasarım'da da benzer bir "seçme sınavı" mantığı var: [Alman sanat okulları için portfolyo (Mappe) nasıl hazırlanır](/tr/blog/how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools).

## Yaygın hatalar
- **Çok zor repertuar** seçip kontrolü kaybetmek (en sık hata).
- **Teori/Gehörbildung'u ihmal etmek** — asıl çalgı iyi diye rahatlamak.
- **Tek okula** başvurup elenince yıl kaybetmek.
- Şartları yüzeysel okumak; istenen eser sayısı/dönem çeşitliliğini kaçırmak.
- **Dil belgesini** son ana bırakmak (oyunculuk/pedagoji için kritik).
- Sahne önünde ilk kez çalmak — heyecanı prova etmemek.
- Jüri turda keserse paniklemek; bu **normaldir**, ret anlamına gelmez.

## Sonuç & dürüst tavsiye
Alman konservatuvarında kabul, notla değil, **kürsünün önünde 15 dakikayla** belirlenir. Bu hem adil hem acımasızdır: paran, ülken, diploman değil, o an ne yaptığın konuşur. En iyi hazırlık üç ayakta durur — **doğru repertuarı kusursuz sun, teori/Gehörbildung'u aylar önceden çalış, birden fazla okula başvur**. Almanca'yı (oyunculuk/pedagoji için C1) erken hallet.

Gerçekçi ol: ilk denemede kabul edilmemek çok yaygındır ve **başarısızlık değildir** — birçok müzisyen bir yıl daha hazırlanıp ikinci turda girer. Auditiondan sonrasının ekonomik gerçeği için [Almanya'da müzisyen/sanatçı olarak çalışmak](/tr/blog/working-as-a-musician-or-performer-in-germany-careers-salary-and-reality) ve tüm kararın dürüst muhasebesi için [müzik/sahne sanatları okumaya değer mi](/tr/blog/is-studying-music-or-performing-arts-in-germany-worth-it-honest-reality) yazılarını oku. Okul seçerken prestij tuzağına düşme: [Almanya'da üniversite prestiji nasıl işler](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) — müzikte önemli olan sıralama değil, **doğru hocadır**.

*Bu yazıdaki bilgiler ve rakamlar 2026 başı içindir ve yaklaşıktır; audition şartları, tarihler, repertuar talepleri ve dil koşulları okuldan okula değişir ve güncellenir. Başvurmadan önce hedef Musikhochschule'nin resmi Aufnahmeprüfung sayfasını mutlaka doğrula.*
MD;

        $deBody = <<<'MD'
Bei der Zulassung an einer deutschen Musikhochschule zählt ein einziges Wort: die **Aufnahmeprüfung**. Dein Notendurchschnitt, dein Abitur, wie viele Sprachen du sprichst — nichts davon entscheidet. Die Jury urteilt, wenn du 10-20 Minuten vor ihr spielst oder singst. Dieser Artikel erklärt Instrumentalist:innen, Sänger:innen und Schauspielbewerber:innen den **Aufnahmeprüfungs**-Prozess in Deutschland Schritt für Schritt und konkret.

## Warum die Aufnahmeprüfung alles ist (kein NC)
In Medizin oder Jura läuft die Zulassung über die Note (Numerus Clausus). In der Musik gibt es **keinen NC**: dein Talent wird direkt und live gemessen. Staatliche Musikhochschulen sind **kostenfrei** (nur der Semesterbeitrag) — aber genau deshalb **gnadenlos umkämpft**: in manchen Klassen gibt es einstellige Plätze und Hunderte Bewerbungen.

**Das heißt:** 90 % deiner Vorbereitung müssen am Instrument oder an der Stimme stattfinden. Lebenslauf und Motivationsschreiben sind zweitrangig. Die Jury will sehen: technische Beherrschung, Musikalität (nicht nur richtige Töne, sondern Ausdruck), Bühnenpräsenz und **Ausbildungsfähigkeit** — also das Potenzial, dich als Studierende:n weiterzuentwickeln. Wichtiger als makellose Perfektion ist, dass du jemand bist, der mit der richtigen Lehrperson **wachsen** kann.

Den ganzen Kontext findest du hier: [Musik & darstellende Kunst in Deutschland studieren](/de/blog/studying-music-and-performing-arts-in-germany-as-a-foreigner-de).

## Repertoirewahl: die kritischste strategische Entscheidung
Das Repertoire ist das Herz der Prüfung. Jede Hochschule veröffentlicht ihre eigenen **Anforderungen** auf der Website — lies sie Wort für Wort. Der allgemeine Rahmen:

| Bereich | Typische Prüfungsanforderung |
|---|---|
| Klassisches Instrument | 3-4 Werke aus verschiedenen Epochen (z. B. Barock + Klassik/Romantik + Moderne), oft **Etüde** + ein Konzertsatz |
| Gesang (klassisch) | 3-5 Stücke: Arien in verschiedenen Sprachen + Lied (deutsches Lied oft verlangt), verschiedene Epochen/Charaktere |
| Klavier | Bach (Präludium-Fuge), ein klassischer Sonatensatz, ein romantisches/virtuoses Werk, Etüde |
| Jazz/Pop | Standardrepertoire, Improvisation, oft mit Rhythmusgruppe oder solo |
| Schauspiel | 2-4 **Monologe** (klassisch + modern, Kontrast), oft Lied + Bewegung/Improvisation |

**Fette Wahrheit:** Ein etwas leichteres Stück **makellos und musikalisch** vorzutragen gewinnt immer gegen ein schweres Werk, das du nur *halb* spielst. Die Jury bemerkt sofort eine Virtuosität, die du nicht kontrollierst. Baue dein Repertoire so, dass es **Kontrast** zeigt: technisch, lyrisch, rhythmisch. Wähle ein Stück als "auswendige Sicherheitszone" — die Nervosität trägt es am stärksten.

Im Schauspiel wähle Monologe passend zu **Alter und Typ**; einen 50-jährigen König mit 19 zu spielen ist riskant. In Gesang und Schauspiel wird **deutschsprachiges** Repertoire bzw. deutscher Text fast immer erwartet.

## Ablauf: Bewerbung → Vorspiel/Vorsprechen → Ergebnis
Typischer Ablauf:

1. **Online-Bewerbung:** An den meisten Hochschulen liegt die Frist im Winter (Dezember-Februar), die Prüfungen im Frühjahr (März-Juni). Manche Hochschulen haben auch einen Termin fürs Wintersemester. Die **Bewerbungsgebühr** beträgt meist 30-80 €.
2. **Vorauswahl (an manchen Hochschulen):** Ein Videomitschnitt kann verlangt werden; die Aufnahmequalität zählt, aber **übertreibe den Schnitt nicht** — die Jury will das Live-Spiel sehen.
3. **Vorspiel / Vorsingen / Vorsprechen (Live-Prüfung):** Am Instrument "Vorspiel", im Gesang "Vorsingen", im Schauspiel "Vorsprechen". Sie kann aus 1-3 Runden bestehen; dass in der ersten Runde abgebrochen und "danke" gesagt wird, ist normal — die Jury versteht in den ersten 2-3 Minuten viel.
4. **Theorie + Gehörbildung** (siehe unten).
5. **Ergebnis:** Zulassung, Warteliste oder Absage. Wirst du zugelassen, kommst du oft in eine bestimmte **Klasse** einer Lehrperson — das bestimmt, mit wem du künftig arbeitest.

**Hinweis:** Instrumentale Performance ist relativ international (Englisch kann flexibel sein), aber für Schauspiel, Musikpädagogik und Kirchenmusik ist meist **B2-C1 Deutsch** Pflicht. Ein Sprachnachweis wird für die Einschreibung oft separat verlangt.

## Theorie & Gehörbildung
Die meisten Bewerber:innen werden hier unvorbereitet erwischt. Die Aufnahmeprüfung ist nicht nur Spielen; es gibt eine eigene Prüfung in **Musiktheorie + Gehörbildung**. Typischer Inhalt:

- **Gehörbildung:** Intervalle erkennen, Akkordtypen, Rhythmus- und Melodiediktat (Gehörtes notieren), *vom Blatt singen*.
- **Theorie:** Grundharmonik, Kadenzen, Tonalität, Formanalyse; manchmal eine kleine schriftliche Aufgabe.
- Manche Hochschulen verlangen ein kurzes **Klavier-Nebenfach**; für Instrumentalist:innen sind Klaviergrundkenntnisse ein Vorteil.

**Fette Wahrheit:** Bleibt Theorie/Gehörbildung an der Grenze, riskierst du die Zulassung, selbst wenn dein Hauptinstrument sehr gut ist. Übe diesen Teil **Monate im Voraus** regelmäßig — Solfège und Diktat brauchen Muskelgedächtnis, das öffnet sich nicht am Vorabend.

## Vorbereitung: Einzelunterricht, Probevorspiel, Zeitplan
Die drei wirksamsten Hebel:

- **Einzelunterricht / Probestunde bei der Ziel-Lehrperson:** Ideal ist, vor der Bewerbung ein paar Stunden bei einer Lehrperson an deiner Zielhochschule (oder auf vergleichbarem Niveau) zu nehmen. So kennen sie dich, und du lernst die Erwartung dieser Hochschule. Nach Deutschland zu kommen und eine **Probestunde** zu erbitten ist üblich und gern gesehen.
- **Mock-Audition (Probevorspiel):** Spiel dein Repertoire mindestens 5-10 Mal **vor anderen**. Lampenfieber zähmt man nur durch Wiederholung. Nimm auf, schau zu, korrigiere.
- **Bewirb dich an mehreren Hochschulen:** Das ist die wichtigste Taktik. Verlass dich nicht auf eine Hochschule; eine Bewerbung an **4-6 Hochschulen** erhöht die Chance deutlich. Prüfungstermine in verschiedenen Städten können sich überschneiden, also plane den Kalender früh.

Beispiel-Zeitplan über 12 Monate:

| Zeitpunkt | Fokus |
|---|---|
| 12-9 Monate vorher | Hochschul- + Lehrpersonenliste, Anforderungstabellen, Repertoire-Entwurf |
| 9-6 Monate vorher | Repertoire festlegen, regelmäßiger Unterricht, Theorie/Gehörbildung starten |
| 6-3 Monate vorher | Mock-Auditions, Videomitschnitt, Deutschprüfung (falls nötig) |
| 3-1 Monate vorher | Feinschliff, Bühnenprobe, Reise/Unterkunft planen |
| Letzte 2 Wochen | Ausruhen, kein Übertraining, Programm-Integrität |

Die Portfolio-Logik kommt dir vielleicht bekannt vor — auch in Kunst & Design gibt es eine ähnliche "Eignungsprüfungs"-Logik: [Mappe für deutsche Kunst- & Designhochschulen vorbereiten](/de/blog/how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools-de).

## Häufige Fehler
- **Zu schweres Repertoire** wählen und die Kontrolle verlieren (häufigster Fehler).
- **Theorie/Gehörbildung vernachlässigen** — sich zurücklehnen, weil das Hauptinstrument gut ist.
- Sich nur an **einer Hochschule** bewerben und bei einer Absage ein Jahr verlieren.
- Anforderungen oberflächlich lesen; Werkzahl/Epochenvielfalt verpassen.
- Den **Sprachnachweis** bis zuletzt aufschieben (für Schauspiel/Pädagogik kritisch).
- Zum ersten Mal vor Publikum spielen — die Nervosität nicht proben.
- In Panik geraten, wenn die Jury in einer Runde abbricht; das ist **normal** und bedeutet keine Absage.

## Fazit & ehrlicher Rat
Die Zulassung an einer deutschen Musikhochschule entscheidet nicht die Note, sondern **15 Minuten vor der Jury**. Das ist fair und gnadenlos zugleich: nicht dein Geld, dein Land oder dein Zeugnis sprechen, sondern was du in diesem Moment tust. Die beste Vorbereitung steht auf drei Beinen — **das richtige Repertoire makellos vortragen, Theorie/Gehörbildung Monate vorher üben, dich an mehreren Hochschulen bewerben**. Kläre Deutsch (C1 für Schauspiel/Pädagogik) früh.

Sei realistisch: beim ersten Versuch nicht zugelassen zu werden ist sehr häufig und **kein Scheitern** — viele Musiker:innen bereiten sich ein weiteres Jahr vor und treten in der zweiten Runde an. Zur wirtschaftlichen Realität nach der Prüfung lies [als Musiker:in oder Performer:in in Deutschland arbeiten](/de/blog/working-as-a-musician-or-performer-in-germany-careers-salary-and-reality-de) und zur ehrlichen Gesamtabrechnung [lohnt sich ein Musik-/Bühnenkunststudium](/de/blog/is-studying-music-or-performing-arts-in-germany-worth-it-honest-reality-de). Fall bei der Wahl nicht in die Prestige-Falle: [Wie Uni-Prestige in Deutschland funktioniert](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de) — in der Musik zählt kein Ranking, sondern die **richtige Lehrperson**.

*Die Informationen und Zahlen in diesem Artikel gelten für Anfang 2026 und sind Näherungswerte; Prüfungsanforderungen, Termine, Repertoirevorgaben und Sprachbedingungen unterscheiden sich von Hochschule zu Hochschule und werden aktualisiert. Prüfe vor der Bewerbung unbedingt die offizielle Aufnahmeprüfungs-Seite deiner Zielhochschule.*
MD;

        $enBody = <<<'MD'
Admission to a German conservatoire (Musikhochschule) comes down to a single word: the **audition**. Your grade average, your school-leaving diploma, how many languages you speak — none of it decides. The jury makes its call when you play or sing for 10-20 minutes in front of it. This article walks instrumentalists, singers and acting applicants through Germany's **Aufnahmeprüfung** (entrance exam) process step by step and concretely.

## Why the audition is everything (no NC)
In medicine or law, admission runs on the grade (Numerus Clausus). In music there is **no NC**: your talent is measured directly and live. Public Musikhochschulen are **free** (only a semester fee) — but precisely for that reason they are **brutally competitive**: some classes have single-digit places and hundreds of applicants.

**This means:** 90% of your preparation has to happen at the instrument or the voice. CV polish and motivation letters are secondary. The jury wants to see technical command, musicality (not just correct notes, but expression), stage presence, and **teachability** — the potential to develop you as a student. More important than flawless perfection is being someone who can **grow** with the right teacher.

For the full context, see [studying music & performing arts in Germany](/en/blog/studying-music-and-performing-arts-in-germany-as-a-foreigner-en).

## Repertoire choice: the most critical strategic decision
Repertoire is the heart of the audition. Every school publishes its own **requirements (Anforderungen)** on its website — read them word for word. The general frame:

| Field | Typical audition requirement |
|---|---|
| Classical instrument | 3-4 works from different eras (e.g. Baroque + Classical/Romantic + Modern), often an **étude** + a concerto movement |
| Voice (classical) | 3-5 pieces: arias in different languages + Lied (German Lied often required), varied eras/characters |
| Piano | Bach (prelude-fugue), a classical sonata movement, a Romantic/virtuoso work, an étude |
| Jazz/Pop | Standard repertoire, improvisation, often with a rhythm section or solo |
| Acting (Schauspiel) | 2-4 **monologues** (classical + modern, contrast), often a song + movement/improvisation |

**Bold truth:** playing a slightly easier piece **flawlessly and musically** always beats a hard work you only play *halfway*. The jury instantly spots virtuosity you cannot control. Build your repertoire to show **contrast**: technical, lyrical, rhythmic. Pick one piece as your "memory safety zone" — nerves hit that one hardest.

In acting, choose monologues suited to your **age and type**; playing a 50-year-old king at 19 is risky. In voice and acting, **German-language** repertoire or text is expected almost every time.

## The process: application → Vorspiel/Vorsprechen → result
Typical flow:

1. **Online application (Bewerbung):** at most schools the deadline is in winter (December-February) and exams are in spring (March-June). Some schools also have a winter-semester date. The **application fee** is usually €30-80.
2. **Pre-selection (at some schools):** a video recording may be required; recording quality matters, but **don't over-edit** — the jury wants to see the live playing.
3. **Vorspiel / Vorsingen / Vorsprechen (live exam):** "Vorspiel" for instruments, "Vorsingen" for voice, "Vorsprechen" for acting. It can involve 1-3 rounds; being stopped in the first round with a "thank you" is normal — the jury understands a lot in the first 2-3 minutes.
4. **Theory + Gehörbildung** (see below).
5. **Result:** admission (Zulassung), waiting list, or rejection. If admitted, you often enter a specific teacher's **class (Klasse)** — this determines who you will work with.

**Note:** instrumental performance is relatively international (English can be flexible), but for acting, music education (Musikpädagogik) and church music, **B2-C1 German** is usually mandatory. A language certificate is often required separately for enrolment.

## Theory & Gehörbildung (ear training)
Most applicants get caught out here. The audition is not only playing; there is a separate exam in **music theory + Gehörbildung** (ear training). Typical content:

- **Gehörbildung:** interval recognition, chord types, rhythm and melody dictation (writing down what you hear), *sight-singing* (vom Blatt singen).
- **Theory:** basic harmony, cadences, tonality, formal analysis; sometimes a small written task.
- Some schools require a short **piano secondary skill**; for instrumentalists, basic piano is an advantage.

**Bold truth:** if theory/Gehörbildung is borderline, you risk admission even if your main instrument is excellent. Practise this part **months in advance** and regularly — solfège and dictation need muscle memory; they don't switch on the night before.

## Preparation: private lessons, mock auditions, timeline
The three most effective levers:

- **Private lessons / a trial lesson (Probestunde) with your target teacher:** ideally, take a few lessons before applying with a teacher at your target school (or one at a comparable level). They get to know you, and you learn that school's expectations. Coming to Germany to request a **trial lesson** is common and welcomed.
- **Mock audition:** play your repertoire at least 5-10 times **in front of others**. Stage nerves are only tamed through repetition. Record, watch, correct.
- **Apply to several schools:** this is the cluster's most important tactic. Don't bet on one school; applying to **4-6 schools** raises your chances significantly. Exam dates in different cities can clash, so set your calendar early.

Sample 12-month timeline:

| When | Focus |
|---|---|
| 12-9 months out | School + teacher list, requirement tables, repertoire draft |
| 9-6 months out | Lock repertoire, regular lessons, start theory/Gehörbildung |
| 6-3 months out | Mock auditions, video recording, German exam (if needed) |
| 3-1 months out | Polish, stage rehearsal, plan travel/accommodation |
| Final 2 weeks | Rest, no over-practising, programme integrity |

The portfolio logic may feel familiar — art & design has a similar "aptitude exam" logic: [how to prepare a portfolio (Mappe) for German art & design schools](/en/blog/how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools-en).

## Common mistakes
- Choosing **repertoire that's too hard** and losing control (the most common mistake).
- **Neglecting theory/Gehörbildung** — relaxing because the main instrument is good.
- Applying to **only one school** and losing a year on a rejection.
- Reading requirements superficially; missing the required number of works or era variety.
- Leaving the **language certificate** to the last minute (critical for acting/pedagogy).
- Playing in front of an audience for the first time at the audition — not rehearsing the nerves.
- Panicking when the jury stops you in a round; this is **normal** and does not mean rejection.

## Conclusion & honest advice
Admission to a German conservatoire is decided not by a grade but by **15 minutes in front of the jury**. That is both fair and merciless: not your money, country or diploma speak, but what you do in that moment. The best preparation stands on three legs — **present the right repertoire flawlessly, practise theory/Gehörbildung months in advance, and apply to several schools**. Sort out German (C1 for acting/pedagogy) early.

Be realistic: not getting in on the first attempt is very common and **is not failure** — many musicians prepare for another year and enter a second round. For the economic reality after the audition, read [working as a musician or performer in Germany](/en/blog/working-as-a-musician-or-performer-in-germany-careers-salary-and-reality-en), and for the honest overall reckoning, [is studying music or performing arts in Germany worth it](/en/blog/is-studying-music-or-performing-arts-in-germany-worth-it-honest-reality-en). When choosing, don't fall into the prestige trap: [how university prestige works in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en) — in music no ranking matters, only the **right teacher**.

*The information and figures in this article are for early 2026 and are approximate; audition requirements, dates, repertoire specifications and language conditions differ from school to school and are updated. Before applying, always verify your target Musikhochschule's official Aufnahmeprüfung page.*
MD;

        $variants = [
            'tr' => ['slug'=>'how-to-prepare-for-a-music-audition-aufnahmepruefung-at-german-conservatories',    'title'=>'Alman Konservatuvarları İçin Audition (Aufnahmeprüfung) Nasıl Hazırlanır? (2026)', 'excerpt'=>'Alman konservatuvarlarında NC yok — kabulü audition (Vorspiel/Vorsingen, oyunculukta Vorsprechen) belirler. Repertuar seçimi, süreç, teori/Gehörbildung sınavı, hazırlık stratejisi, yaygın hatalar ve 12 aylık zaman planı.', 'meta_title'=>'Alman Konservatuvarı Audition (Aufnahmeprüfung) Nasıl Hazırlanır 2026', 'meta_description'=>'Alman konservatuvarlarında audition her şeydir. Repertuar, Vorspiel/Vorsprechen süreci, teori/Gehörbildung, hazırlık stratejisi, hatalar ve zaman planı (2026).', 'body'=>$trBody],
            'de' => ['slug'=>'how-to-prepare-for-a-music-audition-aufnahmepruefung-at-german-conservatories-de', 'title'=>'Aufnahmeprüfung an deutschen Musikhochschulen: So bereitest du dich vor (2026)', 'excerpt'=>'An deutschen Musikhochschulen gibt es keinen NC — die Aufnahmeprüfung (Vorspiel/Vorsingen, im Schauspiel Vorsprechen) entscheidet. Repertoirewahl, Ablauf, Theorie/Gehörbildung, Vorbereitungsstrategie, häufige Fehler und ein 12-Monats-Zeitplan.', 'meta_title'=>'Aufnahmeprüfung an deutschen Musikhochschulen vorbereiten 2026', 'meta_description'=>'An deutschen Musikhochschulen ist die Aufnahmeprüfung alles. Repertoire, Vorspiel/Vorsprechen, Theorie/Gehörbildung, Strategie, Fehler und Zeitplan (2026).', 'body'=>$deBody],
            'en' => ['slug'=>'how-to-prepare-for-a-music-audition-aufnahmepruefung-at-german-conservatories-en', 'title'=>'How to Prepare for a Music Audition (Aufnahmeprüfung) at German Conservatories (2026)', 'excerpt'=>'German conservatoires have no NC — the audition (Vorspiel/Vorsingen, Vorsprechen for acting) decides admission. Repertoire choice, the process, the theory/Gehörbildung exam, a preparation strategy, common mistakes and a 12-month timeline.', 'meta_title'=>'How to Prepare for a German Conservatoire Audition (Aufnahmeprüfung) 2026', 'meta_description'=>'At German conservatoires the audition is everything. Repertoire, the Vorspiel/Vorsprechen process, theory/Gehörbildung, a strategy, mistakes and a timeline (2026).', 'body'=>$enBody],
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
            'how-to-prepare-for-a-music-audition-aufnahmepruefung-at-german-conservatories',
            'how-to-prepare-for-a-music-audition-aufnahmepruefung-at-german-conservatories-de',
            'how-to-prepare-for-a-music-audition-aufnahmepruefung-at-german-conservatories-en',
        ])->delete();
    }
};

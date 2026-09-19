<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN) — KÜLTÜR KÜMESİ 1/4: Almanya'da kültür şoku ve ilk altı ay.
 *
 * Kullanıcının verdiği küratörlü kaynaklar (bkz. memory curated-source-enrichment) taranarak
 * hangi acıların gerçekten tekrarlandığı çıkarıldı: study-abroad.org (TR+DE kültürel uyum),
 * deutschedu.com (öğrenci hayatı soru-cevap), almancakulubu.com (forum), life-in-germany.de ve
 * studying-in-germany.org (gelenek/görenek). Ortak tema: dakiklik, doğrudan iletişim, akademik
 * özerklik, yavaş kurulan arkadaşlık, kural yoğunluğu.
 * Rakamlar blog kaynaklarından DEĞİL, birincil kaynaklardan doğrulandı (Ruhezeit 22-06,
 * Rundfunkbeitrag 18,36 €/konut, Pfand 25/8-15 ct, çalışma hakkı 140 tam gün § 16b Abs. 3).
 * Küme: 1) kültür şoku 2) yazısız kurallar 3) arkadaş edinmek 4) kültürle Almanca.
 * Yazar: Halil Yaprakli. Kategori: german-life-culture (kategori boştu, küme onu dolduruyor).
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c41f8b52-90a7-4d63-9d21-6f0b1ae74c10';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'german-life-culture')->value('id')
            ?? DB::table('categories')->where('slug', 'yasam')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Vize çıktı, uçak indi, oda tutuldu. Sonra üçüncü hafta geliyor ve kimse sana bunu söylememiş oluyor: **asıl zor kısım evrak değilmiş.**

Almanya'da kültür şoku genelde dramatik bir olayla gelmez. Kimse sana kaba davranmaz; sadece dersten sonra kimse "kahve içelim mi" demez. Kimse seni dışlamaz; sadece herkesin takvimi üç hafta önceden doludur. Bu yazı, o ilk altı ayın gerçek eğrisini ve her aşamada işe yarayan somut hamleleri anlatıyor.

## Kültür şokunun dört evresi — ve Almanya'da nasıl göründüğü

| Evre | Ne zaman | Almanya'da tipik hissi |
|---|---|---|
| **Balayı** | 0–4 hafta | Her şey düzenli, trenler çalışıyor, şehir temiz. "Ben bunu başarırım." |
| **Kriz** | 1–4 ay | Bürokrasi bitmiyor, kimse yakınlaşmıyor, hava kararıyor. "Buraya ait değilim." |
| **Toparlanma** | 4–8 ay | Kurallar mantıklı gelmeye başlıyor, bir-iki tanıdık çıkıyor, dil kulağa oturuyor. |
| **Uyum** | 8+ ay | Farklar hâlâ var ama artık sürpriz değil; kendi ritmini kurdun. |

Kritik olan şu: **kriz evresi bir başarısızlık değil, takvimin normal parçası.** Çoğu öğrenci tam bu noktada "yanlış karar verdim" diye düşünür — oysa aynı eğriden geçen binlerce kişi var.

## En sık çarpılan beş fark

**1. Dakiklik bir nezaket değil, bir sözleşme.** Randevuya 5 dakika erken gitmek normaldir; 10 dakika geç kalmak açıklama gerektirir. Üniversitede küçük bir istisna var: ders saatleri **c.t.** (*cum tempore*) işaretliyse ders akademik çeyrekle, yani ilan edilen saatten 15 dakika sonra başlar; **s.t.** ise tam saatte başlar. Bu iki harfi ders programında kaçırmak ilk hafta klasiğidir.

**2. Doğrudan iletişim kişisel değildir.** "Bu bölüm çalışmıyor" cümlesi Almanca akademik ortamda bir eleştiri değil, bir bilgi aktarımıdır. Türk öğrencilerin çoğu ilk aylarda bunu sert bulur; birkaç ay sonra aynı netliği kendi işinde kullanmaya başlar. Tersi de geçerli: senden de net konuşman beklenir, "belki", "bakarız" cevapları burada bilgi taşımaz.

**3. Akademik özerklik = kimse seni takip etmez.** Yoklama çoğu derste yoktur, hatırlatma gelmez, ödev takibi sana aittir. Birçok derste **not, tek bir final sınavından** çıkar. Bu özgürlük ilk dönem çok hoş, ikinci dönem tehlikelidir: takvimini kendin kurmazsan kimse kurmaz. Sınav hakkını kaybetmenin nereye kadar gidebileceğini [Exmatrikulation yazımızda](/tr/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back) anlattık.

**4. Spontanlık yok, planlama var.** "Bu akşam müsait misin?" sorusu burada garip karşılanır. Buluşmalar önceden ayarlanır. Bu soğukluk değil, farklı bir zaman kültürü — ve öğrenilebilir: sen de iki hafta sonrasına teklif etmeye başladığında sistem birden sana da çalışır.

**5. Kural yoğunluğu.** Gece 22:00–06:00 arası sessizlik, pazar günleri kapalı mağazalar, çöp ayrıştırma, şişe iadesi. Hepsi ilk bakışta abartılı görünür, sonra çoğu mantıklı gelmeye başlar. Bu kuralların tam listesi ve cezaları için kümenin ikinci yazısına bak: [Almanya'nın yazısız kuralları](/tr/blog/unwritten-rules-of-daily-life-in-germany).

## Kriz evresini kısaltan şeyler

**İlk iki haftada bitirilecek işler bir listeye yazılır.** Anmeldung, banka hesabı, sigorta, üniversite kaydı. Bu liste bitmeden hiçbir şey normalleşmez çünkü hepsi birbirine bağlıdır — [Anmeldung rehberimiz](/tr/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt) sırayı veriyor.

**Bir tane tekrarlayan sosyal randevu kur.** Haftada bir aynı saatte olan herhangi bir şey: üniversite sporu (Hochschulsport), bir dil tandemi, bir dernek, koro, tırmanış salonu. Almanya'da arkadaşlık tek seferlik buluşmalardan değil, **tekrardan** doğar. Ayrıntısı kümenin üçüncü yazısında: [Almanya'da arkadaş edinmek](/tr/blog/making-friends-in-germany-as-an-international-student).

**Dili günlük hayata sok.** Kurs tek başına yetmiyor; asıl sıçrama dinlediğin şeyin değişmesiyle geliyor. Nasıl kuracağını dördüncü yazıda anlattık: [kültürle Almanca öğrenmek](/tr/blog/learning-german-through-culture-german-media-and-podcasts).

**Karanlığı hafife alma.** Kasım–Şubat arası gün ışığı kısadır ve bu, ruh hâlini gerçekten etkiler. Sabah dışarı çıkmak, D vitamini konusunu doktora sormak ve düzenli hareket, "dayan" demekten daha işe yarar.

## Yardım nereden alınır?

Almanya'da öğrenciye yönelik destek yapıları güçlüdür ama **kendin başvurman gerekir** — kimse seni bulmaz:

- **Studierendenwerk psikolojik danışma birimi:** çoğu şehirde ücretsiz, kısa süreli randevu, sıklıkla İngilizce.
- **International Office:** akademik ve idari tıkanmalarda ilk kapı; buddy/mentor programlarını da onlar yürütür.
- **AStA:** öğrenci temsilciliği; hukuki danışma ve sosyal etkinlikler.
- **Fachschaft:** kendi bölümünün öğrenci topluluğu — sınav taktikleri, eski sorular, ders seçimi burada konuşulur.

Aile yanına gelsin istiyorsan ziyaret vizesinin ayrı kuralları var; reddedilirse ne yapılacağını [ziyaret vizesi yazımızda](/tr/blog/family-visit-visa-refused-germany-what-to-do) anlattık.

## Sıkça Sorulanlar

### Kültür şoku ne kadar sürer?
Tek bir rakam yok, ama tipik eğri şöyle: ilk ay iyi, 2–4. aylar zor, 4–8. aylarda toparlanma. Dil seviyesi ve düzenli sosyal temas bu süreyi belirgin biçimde kısaltır.

### Almancam zayıf; İngilizceyle ilk yılı geçirebilir miyim?
Büyük şehirlerde ve İngilizce programlarda akademik olarak evet. Ama günlük hayat — Ausländerbehörde yazışması, doktor, kira sözleşmesi, komşuluk — Almanca işler. İngilizceyle "idare etmek" mümkündür, "yerleşmek" zordur.

### Almanlar soğuk mu?
Mesafeli başlangıç yaygındır, soğukluk değil. Ayrım şu: burada samimiyet başlangıçta değil, zamanla verilir. İlk ay kimseyle yakınlaşamamış olman bir sinyal değil, normal hız.

### Kendimi izole hissediyorum, bu depresyon mu?
Uyum süreci geçici çöküşler üretir; iki haftadan uzun süren uyku/iştah bozukluğu, derse gidememe ve umutsuzluk ise ayrı bir konudur. O noktada Studierendenwerk'in danışma birimi veya bir doktor (Hausarzt) doğru adres — sigortan bunu kapsar.

### Kendi kültürümden uzaklaşmam mı gerekiyor?
Hayır, ve bu genelde ters teper. En iyi uyum sağlayan öğrenciler kendi çevresini de kuran, bayramını kutlayan, yemeğini paylaşan öğrenciler oluyor. Uyum, kimlik değiştirmek değil; ikinci bir işletim sistemi öğrenmek.

## Sonuç ve dürüst tavsiye

İlk altı ay boyunca en yararlı cümle şu: **"Bu bana özel değil."** Trenin gecikmesi, randevunun üç hafta sonraya verilmesi, komşunun 22:00'de kapıyı çalması, kimsenin spontane buluşmaya gelmemesi — hepsi sistemin normal çalışması. Kişiselleştirmediğin anda enerjin, asıl önemli şeye (dil ve düzen) kalıyor.

Somut plan: ilk iki haftada idari listeyi bitir, üçüncü haftada haftalık tekrarlayan bir sosyal randevu kur, ilk ayın sonunda dil temasını günlük hâle getir. Bu üçünü yapan öğrenciler için kriz evresi genelde aylar değil, haftalar sürüyor.

*Bu yazıdaki kurallar ve tutarlar 2026 Eylül itibarıyla geçerlidir; sessizlik saatleri, mağaza açılış kuralları ve harçlar eyaletten eyalete değişir ve güncellenebilir. Kendi şehrinin resmî sayfasını esas al.*
MD;

        $deBody = <<<'MD'
Visum erteilt, Flug gelandet, Zimmer gefunden. Dann kommt die dritte Woche, und niemand hat dir gesagt: **der schwierige Teil waren nicht die Papiere.**

Der Kulturschock in Deutschland kommt selten als dramatisches Ereignis. Niemand ist unhöflich zu dir; es fragt nur nach dem Seminar niemand, ob du mitkommst. Niemand schließt dich aus; die Kalender sind nur drei Wochen im Voraus voll. Dieser Artikel beschreibt die tatsächliche Kurve der ersten sechs Monate — und was in jeder Phase wirklich hilft.

## Die vier Phasen — und wie sie sich in Deutschland anfühlen

| Phase | Wann | Typisches Gefühl in Deutschland |
|---|---|---|
| **Honeymoon** | 0–4 Wochen | Alles ist geordnet, die Stadt ist sauber. „Das schaffe ich." |
| **Krise** | 1–4 Monate | Die Bürokratie hört nicht auf, niemand kommt näher, es wird früh dunkel. „Ich gehöre hier nicht hin." |
| **Erholung** | 4–8 Monate | Die Regeln ergeben Sinn, erste Bekanntschaften entstehen, die Sprache sitzt besser. |
| **Anpassung** | ab 8 Monaten | Die Unterschiede bleiben, überraschen aber nicht mehr. |

Entscheidend ist: **Die Krisenphase ist kein Scheitern, sondern ein normaler Teil des Ablaufs.** Genau hier denken viele „Ich habe die falsche Entscheidung getroffen" — obwohl Tausende dieselbe Kurve durchlaufen.

## Die fünf Unterschiede, die am härtesten treffen

**1. Pünktlichkeit ist keine Höflichkeit, sondern eine Zusage.** Fünf Minuten früher zu erscheinen ist normal; zehn Minuten Verspätung verlangt eine Erklärung. An der Hochschule gibt es eine kleine Ausnahme: Steht **c.t.** (*cum tempore*) im Vorlesungsverzeichnis, beginnt die Veranstaltung mit dem akademischen Viertel, also 15 Minuten später; **s.t.** heißt pünktlich zur vollen Zeit. Diese zwei Buchstaben zu übersehen, ist ein Klassiker der ersten Woche.

**2. Direktheit ist nicht persönlich gemeint.** „Dieser Teil funktioniert nicht" ist im akademischen Kontext keine Kritik an dir, sondern eine Information. Viele internationale Studierende empfinden das anfangs als schroff und nutzen dieselbe Klarheit ein paar Monate später selbst. Umgekehrt gilt: Auch von dir wird Deutlichkeit erwartet — „vielleicht" oder „mal sehen" transportieren hier keine Information.

**3. Akademische Selbstständigkeit heißt: niemand kontrolliert dich.** Anwesenheitslisten gibt es meist nicht, Erinnerungen kommen nicht, die Fristen gehören dir. In vielen Modulen entscheidet **eine einzige Prüfung** über die Note. Diese Freiheit ist im ersten Semester angenehm und im zweiten gefährlich. Wohin ein verlorener Prüfungsanspruch führen kann, steht in unserem Artikel zur [Exmatrikulation](/de/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-de).

**4. Keine Spontaneität, sondern Planung.** „Hast du heute Abend Zeit?" wirkt hier ungewöhnlich. Treffen werden verabredet. Das ist keine Kälte, sondern eine andere Zeitkultur — und sie ist erlernbar: Sobald du selbst zwei Wochen im Voraus vorschlägst, funktioniert das System auch für dich.

**5. Die Regeldichte.** Nachtruhe von 22 bis 6 Uhr, sonntags geschlossene Geschäfte, Mülltrennung, Flaschenpfand. Das wirkt zunächst übertrieben und ergibt später meist Sinn. Die vollständige Liste steht im zweiten Teil dieser Reihe: [die ungeschriebenen Regeln](/de/blog/unwritten-rules-of-daily-life-in-germany-de).

## Was die Krisenphase verkürzt

**Die Behördenliste der ersten zwei Wochen abarbeiten.** Anmeldung, Bankkonto, Krankenversicherung, Immatrikulation. Vorher normalisiert sich nichts, weil alles voneinander abhängt — unsere [Anmeldung-Anleitung](/de/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt-de) gibt die Reihenfolge vor.

**Einen wiederkehrenden sozialen Termin schaffen.** Irgendetwas, das jede Woche zur selben Zeit stattfindet: Hochschulsport, ein Sprachtandem, ein Verein, ein Chor, die Kletterhalle. Freundschaften entstehen hier nicht aus einmaligen Treffen, sondern aus **Wiederholung**. Mehr dazu im dritten Teil: [Freunde finden in Deutschland](/de/blog/making-friends-in-germany-as-an-international-student-de).

**Die Sprache in den Alltag holen.** Ein Kurs allein reicht selten; der Sprung kommt, wenn sich ändert, was du täglich hörst. Wie das geht, steht im vierten Teil: [Deutsch über Kultur lernen](/de/blog/learning-german-through-culture-german-media-and-podcasts-de).

**Die Dunkelheit nicht unterschätzen.** Zwischen November und Februar ist es kurz hell, und das wirkt sich real auf die Stimmung aus. Morgens rausgehen, Vitamin D ärztlich abklären und Bewegung helfen mehr als Durchhalteparolen.

## Wo es Hilfe gibt

Die Unterstützungsstrukturen für Studierende sind stark — aber **du musst dich selbst melden**:

- **Psychologische Beratung des Studierendenwerks:** vielerorts kostenlos, kurzfristige Termine, oft auch auf Englisch.
- **International Office:** erste Adresse bei akademischen und administrativen Blockaden, betreut auch Buddy-Programme.
- **AStA:** Studierendenvertretung mit Rechtsberatung und Veranstaltungen.
- **Fachschaft:** die Studierendenvertretung deines Fachs — Prüfungstaktik, Altklausuren, Modulwahl.

Wenn deine Familie dich besuchen möchte, gelten eigene Regeln; was bei einer Ablehnung zu tun ist, steht in unserem [Besuchsvisum-Artikel](/de/blog/family-visit-visa-refused-germany-what-to-do-de).

## Häufige Fragen

### Wie lange dauert ein Kulturschock?
Eine feste Zahl gibt es nicht, aber die typische Kurve lautet: erster Monat gut, Monat zwei bis vier schwer, ab Monat vier Erholung. Sprachniveau und regelmäßiger sozialer Kontakt verkürzen die Phase deutlich.

### Mein Deutsch ist schwach — reicht Englisch im ersten Jahr?
In Großstädten und englischsprachigen Studiengängen akademisch ja. Der Alltag aber — Ausländerbehörde, Arztpraxis, Mietvertrag, Nachbarschaft — läuft auf Deutsch. Mit Englisch kommt man durch, ankommen ist schwerer.

### Sind Deutsche kalt?
Ein distanzierter Anfang ist verbreitet, Kälte ist es nicht. Nähe wird hier nicht am Anfang vergeben, sondern mit der Zeit. Dass im ersten Monat keine Freundschaft entsteht, ist kein Signal, sondern das normale Tempo.

### Ich fühle mich isoliert — ist das schon eine Depression?
Anpassung erzeugt vorübergehende Tiefs. Schlaf- und Appetitstörungen über mehrere Wochen, Antriebslosigkeit und Hoffnungslosigkeit sind etwas anderes. Dann sind die Beratungsstelle des Studierendenwerks oder eine Hausarztpraxis die richtige Adresse — deine Versicherung deckt das ab.

### Muss ich mich von meiner eigenen Kultur entfernen?
Nein, und meist wirkt das kontraproduktiv. Am besten kommen die Studierenden an, die auch ihr eigenes Umfeld pflegen. Anpassung heißt nicht Identitätswechsel, sondern ein zweites Betriebssystem zu lernen.

## Fazit und ehrlicher Rat

Der nützlichste Satz der ersten sechs Monate lautet: **„Das ist nicht persönlich gemeint."** Der verspätete Zug, der Termin in drei Wochen, die Nachbarin, die um 22 Uhr klingelt, die ausbleibende spontane Verabredung — das ist das System im Normalbetrieb. Sobald du es nicht mehr persönlich nimmst, bleibt Energie für das Wesentliche: Sprache und Struktur.

Konkret: In den ersten zwei Wochen die Behördenliste abschließen, in der dritten Woche einen wöchentlichen sozialen Termin etablieren, bis Ende des ersten Monats täglichen Sprachkontakt herstellen. Wer diese drei Dinge tut, erlebt die Krisenphase meist in Wochen statt in Monaten.

*Regeln und Beträge gelten mit Stand September 2026; Ruhezeiten, Ladenöffnung und Gebühren unterscheiden sich nach Bundesland und können sich ändern. Maßgeblich ist die offizielle Seite deiner Stadt.*
MD;

        $enBody = <<<'MD'
Visa granted, plane landed, room secured. Then week three arrives, and nobody warned you: **the hard part was never the paperwork.**

Culture shock in Germany rarely arrives as a dramatic event. Nobody is rude to you; it is just that after the seminar, nobody asks whether you want to get coffee. Nobody excludes you; the calendars are simply full three weeks ahead. This article maps the real curve of your first six months and what actually helps at each stage.

## The four phases — and how each feels in Germany

| Phase | When | What it typically feels like here |
|---|---|---|
| **Honeymoon** | 0–4 weeks | Everything is orderly, the trains run, the city is clean. "I can do this." |
| **Crisis** | 1–4 months | The bureaucracy never ends, nobody gets closer, it gets dark early. "I do not belong here." |
| **Recovery** | 4–8 months | The rules start making sense, a few acquaintances appear, the language settles. |
| **Adjustment** | 8+ months | The differences remain but stop being surprises; you have your own rhythm. |

The crucial point: **the crisis phase is not a failure, it is a scheduled part of the process.** This is exactly where students conclude they made the wrong decision — while thousands are moving along the same curve.

## The five differences that hit hardest

**1. Punctuality is not politeness, it is a commitment.** Arriving five minutes early is normal; being ten minutes late requires an explanation. University has one small exception: if a class is marked **c.t.** (*cum tempore*) it starts with the academic quarter, fifteen minutes after the listed time; **s.t.** means it starts on the hour. Missing those two letters is a first-week classic.

**2. Directness is not personal.** "This part does not work" is not criticism of you in a German academic setting; it is information. Most international students find it blunt at first and are using the same clarity themselves a few months later. It cuts both ways: you are expected to be explicit too. "Maybe" and "we will see" carry no information here.

**3. Academic independence means nobody chases you.** Attendance is usually not taken, reminders do not arrive, deadlines are yours to track. In many modules **a single exam** decides the grade. That freedom is pleasant in your first semester and dangerous in your second. Where a lost examination entitlement can lead is covered in our guide to [de-registration](/en/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-en).

**4. Planning replaces spontaneity.** "Are you free tonight?" reads as odd here. Meetings are arranged in advance. That is not coldness but a different time culture — and it is learnable: once you start proposing something two weeks out, the system works for you too.

**5. The density of rules.** Quiet hours from 22:00 to 06:00, shops closed on Sundays, waste separation, bottle deposits. It looks excessive at first and mostly makes sense later. The full list is in part two of this series: [the unwritten rules of daily life](/en/blog/unwritten-rules-of-daily-life-in-germany-en).

## What shortens the crisis phase

**Finish the administrative list in your first two weeks.** Address registration, bank account, health insurance, enrolment. Nothing feels normal until that list is done, because each item depends on the others — our [Anmeldung walkthrough](/en/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt-en) sets the order.

**Create one recurring social appointment.** Anything that happens weekly at the same time: university sports (Hochschulsport), a language tandem, a club, a choir, a climbing gym. Friendships here grow out of **repetition**, not one-off meetings. Part three goes into detail: [making friends in Germany](/en/blog/making-friends-in-germany-as-an-international-student-en).

**Put the language into your daily life.** A course alone rarely does it; the jump comes when what you listen to every day changes. Part four shows how: [learning German through culture](/en/blog/learning-german-through-culture-german-media-and-podcasts-en).

**Do not underestimate the darkness.** Between November and February daylight is short, and it genuinely affects your mood. Getting outside in the morning, asking a doctor about vitamin D and moving regularly help more than telling yourself to push through.

## Where to get help

Germany's student support structures are strong, but **you have to approach them** — nobody will find you:

- **The Studierendenwerk's psychological counselling service:** free in most cities, short-notice appointments, frequently available in English.
- **The International Office:** your first stop for academic and administrative blockages, and usually the home of buddy programmes.
- **AStA:** the student representation, offering legal advice and events.
- **Fachschaft:** your own department's student body — exam tactics, past papers, module choice.

If your family wants to visit, that runs on different rules; what to do after a refusal is in our [visit visa guide](/en/blog/family-visit-visa-refused-germany-what-to-do-en).

## Frequently asked questions

### How long does culture shock last?
There is no single number, but the typical curve is: a good first month, a hard month two to four, recovery from month four onwards. Language level and regular social contact shorten it noticeably.

### My German is weak — can I get through the first year in English?
Academically, yes, in large cities and English-taught programmes. But daily life — the immigration office, the doctor, the lease, your neighbours — runs in German. English lets you cope; it rarely lets you settle.

### Are Germans cold?
A distant start is common; coldness it is not. Closeness here is granted over time rather than at the beginning. Making no friends in your first month is not a signal, it is the normal pace.

### I feel isolated. Is this depression?
Adjustment produces temporary lows. Sleep and appetite disruption lasting weeks, being unable to attend classes and persistent hopelessness are something else. At that point the Studierendenwerk counselling service or a GP (Hausarzt) is the right address — your insurance covers it.

### Do I have to distance myself from my own culture?
No, and it usually backfires. The students who settle best are the ones who also maintain their own circle, celebrate their holidays and share their food. Adjusting is not changing identity; it is learning a second operating system.

## Conclusion and honest advice

The most useful sentence of your first six months is: **"This is not about me."** The delayed train, the appointment three weeks out, the neighbour at your door at 22:00, the friend who will not meet spontaneously — that is the system working normally. The moment you stop taking it personally, your energy goes where it matters: language and structure.

Concretely: finish the administrative list in the first two weeks, establish a weekly social appointment in week three, and make language contact daily by the end of month one. For students who do those three things, the crisis phase usually lasts weeks rather than months.

*The rules and amounts here are current as of September 2026; quiet hours, shop opening rules and fees vary by federal state and can change. Treat your own city's official page as authoritative.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'culture-shock-in-germany-first-six-months-for-students',
                'title' => 'Almanya\'da Kültür Şoku: İlk Altı Ayın Gerçek Eğrisi',
                'excerpt' => 'Vize ve evrak bittikten sonra başlayan asıl zorluk: kültür şokunun dört evresi, en sık çarpılan beş fark (dakiklik, doğrudan iletişim, akademik özerklik, planlı sosyallik, kural yoğunluğu) ve kriz evresini haftalara indiren somut hamleler.',
                'meta_title' => 'Almanya\'da Kültür Şoku: İlk Altı Ay ve Uyum Rehberi',
                'meta_description' => 'Almanya\'da kültür şoku: dört evre, en sık çarpılan beş fark, kriz evresini kısaltan adımlar ve nereden destek alınır (2026 öğrenci rehberi).',
                'body' => $trBody,
            ],
            'de' => [
                'slug' => 'culture-shock-in-germany-first-six-months-for-students-de',
                'title' => 'Kulturschock in Deutschland: die echte Kurve der ersten sechs Monate',
                'excerpt' => 'Was nach Visum und Behördengängen wirklich schwer wird: die vier Phasen des Kulturschocks, die fünf Unterschiede, die am härtesten treffen, und die konkreten Schritte, die die Krisenphase von Monaten auf Wochen verkürzen.',
                'meta_title' => 'Kulturschock in Deutschland: erste sechs Monate überstehen',
                'meta_description' => 'Kulturschock in Deutschland: vier Phasen, fünf harte Unterschiede, was die Krisenphase verkürzt und wo Studierende Hilfe bekommen (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug' => 'culture-shock-in-germany-first-six-months-for-students-en',
                'title' => 'Culture Shock in Germany: The Real Curve of Your First Six Months',
                'excerpt' => 'What actually gets hard once the visa and the paperwork are done: the four phases of culture shock, the five differences that hit hardest, and the concrete moves that turn the crisis phase from months into weeks.',
                'meta_title' => 'Culture Shock in Germany: Surviving Your First Six Months',
                'meta_description' => 'Culture shock in Germany: the four phases, five hard differences, what shortens the crisis phase and where students can get support (2026).',
                'body' => $enBody,
            ],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale' => $locale, 'translation_group_id' => $groupId, 'user_id' => $userId, 'category_id' => $categoryId,
                'title' => $v['title'], 'excerpt' => Str::limit($v['excerpt'], 250, '…'),
                'content_md' => $v['body'], 'content_html' => $html,
                'meta_title' => $v['meta_title'], 'meta_description' => Str::limit($v['meta_description'], 158, '…'),
                'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
                'is_published' => true, 'published_at' => now(),
            ];
            $existing = Post::where('slug', $v['slug'])->first();
            $existing ? $existing->update($payload) : Post::create($payload + ['slug' => $v['slug']]);
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'culture-shock-in-germany-first-six-months-for-students',
            'culture-shock-in-germany-first-six-months-for-students-de',
            'culture-shock-in-germany-first-six-months-for-students-en',
        ])->delete();
    }
};

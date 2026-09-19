<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN) — KÜLTÜR KÜMESİ 3/4: Almanya'da arkadaş edinmek.
 *
 * Kaynak tarama (kullanıcının verdiği küratörlü linkler) hepsinde tekrarlayan tek bulgu:
 * arkadaşlık yavaş kurulur ama derindir, spontane buluşma yoktur, yakınlık zamanla verilir.
 * Yazı bu gözlemi somut mekanizmalara bağlıyor: tekrar eden randevu (Verein/Hochschulsport/
 * Fachschaft/tandem), davet görgüsü, "uluslararası balon" tuzağı ve dil eşiği.
 * Rakam/istatistik iddiası bilinçli olarak YOK — kaynaklardaki yüzdeler (ör. "%85 dakikliğe
 * önem verir") doğrulanabilir birincil kaynağa dayanmıyordu, bu yüzden aktarılmadı.
 * Küme: 1) kültür şoku 2) yazısız kurallar 3) arkadaş edinmek 4) kültürle Almanca.
 * Yazar: Halil Yaprakli. Kategori: german-life-culture.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '9b3a7d16-4c28-4f5e-b0d7-1e8c6a0f39b2';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'german-life-culture')->value('id')
            ?? DB::table('categories')->where('slug', 'yasam')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Derse giriyorsun, yanındakiyle iki cümle konuşuyorsun, ders bitiyor, herkes dağılıyor. Ertesi hafta aynı kişi seni hatırlıyor ama yine iki cümle. Üçüncü ay geldiğinde soru netleşiyor: **"Ben burada nasıl arkadaş edineceğim?"**

Bu, Almanya'daki uluslararası öğrencilerin en çok konuştuğu konu — ve çoğu kişinin yanlış teşhis koyduğu konu. Sorun genelde senin sosyal becerin değil; arkadaşlığın burada **farklı bir mekanizmayla** kuruluyor olması.

## Önce kelimenin kendisi: Freund ≠ friend

Almancada **Freund** kelimesi Türkçedeki "arkadaş"tan dar, "dost"a yakın bir anlam taşır. Haftada bir görüştüğün, adını bildiğin, iyi geçindiğin kişi çoğu Alman için **Bekannter** (tanıdık) kategorisindedir. Bu bir soğukluk değil, bir sınıflandırma farkı.

Pratik sonucu şu: ilk aylarda "arkadaş edinemedim" diye ölçtüğün şey, aslında burada **normal ilerleme**. Tanıdık havuzun büyüyor; dostluk ondan sonra çıkıyor.

| Türkiye'de yaygın model | Almanya'da yaygın model |
|---|---|
| Hızlı yakınlaşma, geniş çevre | Yavaş yakınlaşma, dar çevre |
| Spontane buluşma ("çıkalım mı?") | Planlı buluşma (iki hafta sonrası) |
| Samimiyet önce verilir, sonra test edilir | Samimiyet zamanla kazanılır, sonra kalıcıdır |
| Ortak çevre üzerinden tanışma | Ortak **aktivite** üzerinden tanışma |

Son satır, bu yazının özü: Almanya'da insanlar sohbetle değil, **bir şeyi birlikte tekrar tekrar yaparak** yakınlaşıyor.

## Tek kural: tekrar

Tek seferlik etkinlikler burada arkadaşlık üretmiyor. Üreten şey, **haftada bir, aynı saatte, aynı insanlarla** olan herhangi bir şey. Seçenekler:

- **Hochschulsport:** üniversitenin spor programı. Dönem başında kayıtlar hızla doluyor, ücretler genelde sembolik. Voleybolden dansa, tırmanıştan yüzmeye onlarca kurs olur.
- **Verein (dernek/kulüp):** Almanya'nın sosyal hayatı büyük ölçüde derneklerde kurulur — spor kulübü, koro, satranç, fotoğrafçılık, itfaiye gönüllülüğü. Üyelik aidatı çoğu zaman düşüktür ve yabancılara kapalı değildir.
- **Fachschaft:** kendi bölümünün öğrenci topluluğu. Hem sınav/ders bilgisi hem sosyal etkinlik merkezi. Katkı vermeye başladığın an "yüz" olmaktan çıkıp "isim" olursun.
- **Sprachtandem:** sen Türkçe/İngilizce veriyorsun, o Almanca veriyor. Çoğu üniversitede resmî eşleştirme sistemi var; bu, hem dil hem sosyal temas üretir.
- **Buddy / mentor programları:** International Office üzerinden, ilk dönem için eşleştirme.
- **Ehrenamt (gönüllülük):** mülteci desteği, çevre, hayvan barınağı. Almanya'da gönüllülük saygı gören bir şeydir ve kapıları açar.
- **WG (paylaşımlı ev):** en güçlü sosyal ağ kaynaklarından biri. Ev arkadaşı seçimi bir sosyal karardır — [WG bulma rehberimiz](/tr/blog/finding-a-wg-in-germany-your-comprehensive-guide-to-dorm-wg) bu tarafa da bakıyor.

Hepsini aynı anda yapmana gerek yok. **Bir tanesini altı ay boyunca sürdürmek**, altı farklı etkinliğe birer kez gitmekten kıyaslanmayacak kadar etkili.

## Davet görgüsü: çağrıldığında ne yapılır?

Bir eve davet edildiysen, ilişki düşündüğünden daha ileri demektir. Birkaç yerel âdet:

- **Zamanında git.** 10 dakika geç kalmak nezaket değil, sorun.
- **Bir şey götür:** şarap, tatlı, kendi mutfağından bir yemek. Küçük ama beklenir.
- **Hesap paylaşımı nettir.** Restoranda herkes kendi hesabını öder ("getrennt, bitte"). Israrla başkasının hesabını ödemek burada cömertlik değil, tuhaflık sinyali olabilir.
- **Ayrılma saatini abartma.** Ev sahibi kapıyı kapatmaz ama gece yarısını geçen ziyaret, özellikle hafta içi, sessizlik saatleriyle çakışır ([yazısız kurallar](/tr/blog/unwritten-rules-of-daily-life-in-germany)).
- **Du/Sie:** öğrenciler arasında neredeyse her zaman *du*. Hoca, memur, komşu (yaşlıysa) için *Sie* ile başla; karşı taraf *du* teklif eder.

## "Uluslararası balon" tuzağı

Gelen her öğrencinin önünde iki yol var: kendi dilinden insanların çevresi ve yerel çevre. Dürüst cevap: **ikisine de ihtiyacın var.**

Kendi topluluğun ilk aylarda ayakta kalmanı sağlar — bilgiyle, yemekle, tanıdıklıkla. Ama tek çevre orası kalırsa iki şey olur: Almancan durur ve mezuniyet sonrası iş ağın kurulmaz. Almanya'da işlerin önemli bir kısmı ilan yoluyla değil, tanıdık üzerinden hareket eder.

Pratik denge: **haftada en az bir kez, Almanca konuşulan ve senin kendi ülkenden kimsenin olmadığı bir ortamda bulun.** Bu tek kural, çoğu öğrencinin ikinci yılını birinci yılından tamamen farklı kılıyor.

## Dil eşiği gerçek ama sandığından düşük

Derin sohbet için B2 gerekiyor, doğru. Ama arkadaşlığın başlaması için **A2–B1 yeterli**: birlikte spor yapmak, yemek pişirmek, oyun oynamak, yürüyüşe gitmek dilden çok tekrar ister.

Almancanı günlük hayata sokmanın yollarını kümenin son yazısında topladık: [kültürle Almanca öğrenmek](/tr/blog/learning-german-through-culture-german-media-and-podcasts). Sınav tarafı için [TestDaF, DSH, telc karşılaştırmamız](/tr/blog/testdaf-dsh-telc-which-exam-is-more-advantageous-for-germany-in) var.

## Yalnızlık ciddileşirse

Uyum sürecinin yalnızlığı ile klinik yalnızlık arasında fark var. İkincisinin işaretleri: haftalarca süren uyku/iştah bozukluğu, derse gidememe, kimseyle konuşmak istememe, umutsuzluk.

O noktada gidilecek yerler belli ve ücretsiz: **Studierendenwerk'in psikolojik danışma birimi** (çoğu şehirde İngilizce de sunuluyor), üniversitenin **International Office**'i, aile hekimi (Hausarzt). Sağlık sigortan bunu kapsar — [sigorta rehberimiz](/tr/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs) hangi hizmetin nasıl karşılandığını anlatıyor.

## Sıkça Sorulanlar

### Almanlar yabancılarla arkadaş olmak istemiyor mu?
İstemiyor değil; **hızlı** arkadaş olmuyor. Aynı yavaşlık kendi aralarında da geçerli. Fark, senin acele etmen ve onların etmemesi.

### Üniversitede kimse sohbet başlatmıyor, ben mi başlatmalıyım?
Evet — ve bu burada kaba değil, normal karşılanır. En işe yarar giriş, ortak bağlamdan gelenidir: ders notu, sınav tarihi, grup ödevi, laboratuvar saati.

### Erasmus/uluslararası etkinlikler işe yarıyor mu?
Sosyal ihtiyaç için evet, yerel ağ için sınırlı. Bu etkinliklerde çoğunlukla yine uluslararası öğrenciler olur. Dengeyi Verein ve Hochschulsport tarafında kur.

### Ev arkadaşlarımla arkadaş olmak zorunda mıyım?
Hayır ve beklenmiyor da. Almanya'da WG çoğu zaman bir yaşam düzeni ortaklığıdır. Yine de "ortak akşam yemeği" teklif etmek düşük maliyetli, yüksek getirili bir hamledir.

### Kendi ülkemden insanlarla takılmam kötü mü?
Hayır — ama tek çevren orası olursa maliyeti dil ve iş ağı tarafında ödersin. Kural basit: kendi topluluğun kalsın, üstüne haftada bir yerel ortam ekle.

### Ne kadar sürede gerçek bir arkadaş çevrem olur?
Tipik cevap: **bir ila iki dönem**, düzenli tekrarlayan bir aktivite sürdürüldüğünde. Bu süreyi kısaltan tek şey süreklilik; uzatan tek şey her dönem sıfırdan başlamak.

## Sonuç ve dürüst tavsiye

Almanya'da arkadaşlık bir "kişilik testi" değil, bir **devamlılık meselesi**. Bir aktivite seç, altı ay boyunca aynı saatte oraya git, ismini öğrenen üç kişi çıksın — geri kalanı kendiliğinde geliyor.

Ve şunu erken kabul et: ilk dönem büyük ihtimalle yalnız geçecek. Bu senin hakkında bir şey söylemiyor; sistemin hızı bu. İkinci dönem, birinci dönemde kurduğun tekrarların meyvesi oluyor.

Kümenin diğer yazıları: [kültür şoku ve ilk altı ay](/tr/blog/culture-shock-in-germany-first-six-months-for-students) · [yazısız kurallar](/tr/blog/unwritten-rules-of-daily-life-in-germany) · [kültürle Almanca öğrenmek](/tr/blog/learning-german-through-culture-german-media-and-podcasts)

*Bu yazıdaki gözlemler 2026 Eylül itibarıyla geçerli genel eğilimlerdir; üniversite ve şehir arasında farklılık gösterir. Kendi kampüsündeki International Office ve Fachschaft en güncel bilgi kaynağıdır.*
MD;

        $deBody = <<<'MD'
Du kommst ins Seminar, wechselst zwei Sätze mit der Person neben dir, das Seminar endet, alle gehen. Nächste Woche erkennt dich dieselbe Person wieder — und wieder bleibt es bei zwei Sätzen. Im dritten Monat wird die Frage konkret: **„Wie finde ich hier eigentlich Freunde?"**

Das ist das meistdiskutierte Thema unter internationalen Studierenden in Deutschland — und das am häufigsten falsch diagnostizierte. Das Problem sind meist nicht deine sozialen Fähigkeiten, sondern dass Freundschaft hier über **einen anderen Mechanismus** entsteht.

## Zuerst das Wort selbst: Freund ist nicht *friend*

„Freund" ist im Deutschen enger gefasst als in vielen anderen Sprachen. Wer sich einmal die Woche sieht, sich gut versteht und den Namen kennt, ist für viele zunächst **ein Bekannter**. Das ist keine Kälte, sondern eine andere Kategorisierung.

Die praktische Folge: Was du in den ersten Monaten als „ich finde keine Freunde" misst, ist hier **normaler Fortschritt**. Dein Bekanntenkreis wächst; Freundschaft entsteht daraus später.

| Häufiges Modell anderswo | Häufiges Modell in Deutschland |
|---|---|
| Schnelle Nähe, großer Kreis | Langsame Nähe, kleiner Kreis |
| Spontane Treffen („gehen wir raus?") | Geplante Treffen (in zwei Wochen) |
| Vertrauen zuerst geben, dann prüfen | Vertrauen wächst und bleibt dann |
| Kennenlernen über gemeinsame Kreise | Kennenlernen über gemeinsame **Aktivität** |

Die letzte Zeile ist der Kern: Menschen kommen sich hier weniger durch Gespräche näher als dadurch, dass sie **etwas regelmäßig gemeinsam tun**.

## Die eine Regel: Wiederholung

Einmalige Veranstaltungen erzeugen hier selten Freundschaften. Was funktioniert, ist irgendetwas, das **wöchentlich, zur selben Zeit, mit denselben Leuten** stattfindet:

- **Hochschulsport:** das Sportprogramm der Hochschule. Die Plätze sind zu Semesterbeginn schnell weg, die Gebühren meist symbolisch — von Volleyball über Tanz bis Klettern.
- **Verein:** das soziale Leben in Deutschland organisiert sich stark in Vereinen — Sport, Chor, Schach, Fotografie, Freiwillige Feuerwehr. Die Beiträge sind oft niedrig, und Zugang haben auch Internationale.
- **Fachschaft:** die Studierendenvertretung deines Fachs, zugleich Info- und Sozialzentrum. Sobald du mitarbeitest, wirst du vom Gesicht zum Namen.
- **Sprachtandem:** du gibst deine Sprache, jemand anderes gibt Deutsch. An den meisten Hochschulen gibt es offizielle Vermittlung — Sprache und Kontakte in einem.
- **Buddy- und Mentoringprogramme:** über das International Office, meist fürs erste Semester.
- **Ehrenamt:** Geflüchtetenhilfe, Umwelt, Tierheim. Ehrenamt genießt hier Ansehen und öffnet Türen.
- **WG:** eine der stärksten sozialen Quellen überhaupt. Die Auswahl der Mitbewohnenden ist eine soziale Entscheidung — unser [WG-Ratgeber](/de/blog/finding-a-wg-in-germany-your-comprehensive-guide-to-dorm-wg-de) geht darauf ein.

Du musst nicht alles gleichzeitig machen. **Eine Sache ein halbes Jahr durchzuhalten** wirkt ungleich stärker, als sechs Veranstaltungen je einmal zu besuchen.

## Eingeladen — und jetzt?

Wer nach Hause eingeladen wird, ist weiter, als er denkt. Ein paar lokale Gepflogenheiten:

- **Pünktlich sein.** Zehn Minuten Verspätung sind keine Höflichkeit, sondern ein Problem.
- **Etwas mitbringen:** Wein, Nachtisch oder etwas Selbstgekochtes. Klein, aber erwartet.
- **Getrennt zahlen ist die Norm.** Im Restaurant zahlt jede Person das Eigene („getrennt, bitte"). Darauf zu bestehen, für andere zu zahlen, wirkt hier eher befremdlich als großzügig.
- **Den Zeitpunkt des Aufbruchs im Blick behalten.** Besuche, die weit über Mitternacht gehen, kollidieren unter der Woche mit den Ruhezeiten (siehe [ungeschriebene Regeln](/de/blog/unwritten-rules-of-daily-life-in-germany-de)).
- **Du oder Sie:** unter Studierenden fast immer *du*. Bei Lehrenden, Behörden und älteren Nachbarn mit *Sie* beginnen; das *du* wird angeboten.

## Die Falle der internationalen Blase

Vor jeder neu angekommenen Person liegen zwei Wege: der Kreis der eigenen Sprache und das lokale Umfeld. Die ehrliche Antwort lautet: **du brauchst beides.**

Die eigene Community trägt dich durch die ersten Monate — mit Wissen, Essen, Vertrautheit. Bleibt sie aber dein einziges Umfeld, passiert zweierlei: dein Deutsch stagniert, und das berufliche Netz für die Zeit nach dem Abschluss entsteht nicht. Ein erheblicher Teil der Stellen läuft hier über Kontakte, nicht über Ausschreibungen.

Die praktische Balance: **mindestens einmal pro Woche in einem Umfeld sein, in dem Deutsch gesprochen wird und niemand aus deinem Herkunftsland dabei ist.** Diese eine Regel macht bei vielen das zweite Studienjahr völlig anders als das erste.

## Die Sprachschwelle ist real, aber niedriger als gedacht

Für tiefe Gespräche braucht es B2 — das stimmt. Damit Freundschaft *beginnt*, reicht aber oft **A2 bis B1**: gemeinsam Sport treiben, kochen, spielen oder wandern verlangt mehr Wiederholung als Vokabular.

Wie du Deutsch in den Alltag holst, steht im letzten Teil der Reihe: [Deutsch über Kultur lernen](/de/blog/learning-german-through-culture-german-media-and-podcasts-de). Zur Prüfungsseite haben wir einen [Vergleich von TestDaF, DSH und telc](/de/blog/testdaf-dsh-telc-which-exam-is-more-advantageous-for-germany-in-de).

## Wenn Einsamkeit ernst wird

Zwischen der Einsamkeit einer Anpassungsphase und klinischer Einsamkeit liegt ein Unterschied. Anzeichen für Letztere: wochenlange Schlaf- und Appetitstörungen, nicht mehr zu Veranstaltungen gehen, Rückzug, Hoffnungslosigkeit.

Die Anlaufstellen sind klar und kostenlos: die **psychologische Beratung des Studierendenwerks** (vielerorts auch auf Englisch), das **International Office**, die Hausarztpraxis. Deine Krankenversicherung deckt das ab — unser [Versicherungsratgeber](/de/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs-de) erklärt, was wie übernommen wird.

## Häufige Fragen

### Wollen Deutsche keine internationalen Freunde?
Doch — nur nicht **schnell**. Dieselbe Langsamkeit gilt untereinander. Der Unterschied ist, dass du es eilig hast und sie nicht.

### An der Uni beginnt niemand ein Gespräch. Muss ich anfangen?
Ja — und das gilt hier nicht als aufdringlich, sondern als normal. Am besten funktioniert der gemeinsame Kontext: Mitschrift, Prüfungstermin, Gruppenarbeit, Laborzeit.

### Bringen Erasmus- und Internationalen-Veranstaltungen etwas?
Für das soziale Grundbedürfnis ja, für das lokale Netz begrenzt. Dort triffst du überwiegend wieder internationale Studierende. Den Ausgleich schaffen Verein und Hochschulsport.

### Muss ich mit meinen Mitbewohnenden befreundet sein?
Nein, das wird auch nicht erwartet. Eine WG ist hier oft eine Organisationsgemeinschaft. Ein gemeinsames Abendessen vorzuschlagen, ist trotzdem günstig und wirkungsvoll.

### Ist es schlecht, viel Zeit mit Landsleuten zu verbringen?
Nein — aber wenn das dein einziges Umfeld bleibt, zahlst du den Preis bei Sprache und Berufsnetz. Die Regel ist einfach: eigene Community behalten, wöchentlich ein lokales Umfeld ergänzen.

### Wie lange dauert es bis zu einem echten Freundeskreis?
Typischerweise **ein bis zwei Semester**, wenn eine regelmäßige Aktivität durchgehalten wird. Verkürzen lässt sich das nur durch Kontinuität; verlängert wird es durch ständiges Neuanfangen.

## Fazit und ehrlicher Rat

Freundschaft in Deutschland ist kein Persönlichkeitstest, sondern eine Frage der **Beständigkeit**. Such dir eine Aktivität, geh ein halbes Jahr lang zur selben Zeit dorthin und sorge dafür, dass drei Menschen deinen Namen kennen — der Rest ergibt sich.

Und akzeptiere früh: Das erste Semester wird wahrscheinlich einsam. Das sagt nichts über dich aus; es ist die Geschwindigkeit des Systems. Das zweite Semester ist die Ernte dessen, was du im ersten wiederholt hast.

Die weiteren Teile der Reihe: [Kulturschock und die ersten sechs Monate](/de/blog/culture-shock-in-germany-first-six-months-for-students-de) · [ungeschriebene Regeln](/de/blog/unwritten-rules-of-daily-life-in-germany-de) · [Deutsch über Kultur lernen](/de/blog/learning-german-through-culture-german-media-and-podcasts-de)

*Die Beobachtungen geben allgemeine Tendenzen mit Stand September 2026 wieder und unterscheiden sich je nach Hochschule und Stadt. International Office und Fachschaft vor Ort sind die aktuellste Quelle.*
MD;

        $enBody = <<<'MD'
You walk into a seminar, exchange two sentences with the person beside you, the seminar ends, everyone leaves. Next week the same person recognises you — and it is two sentences again. By month three the question is unavoidable: **"How am I supposed to make friends here?"**

This is the single most discussed subject among international students in Germany, and the most frequently misdiagnosed. The problem is usually not your social skills; it is that friendship here forms through **a different mechanism**.

## Start with the word itself: Freund is not "friend"

German uses **Freund** more narrowly than English uses "friend". Someone you see weekly, get along with and can name is, for many Germans, **ein Bekannter** — an acquaintance. That is not coldness; it is a different way of sorting people.

The practical consequence: what you measure in your first months as "I have made no friends" is **normal progress** here. Your pool of acquaintances is growing; friendship emerges from it later.

| Common model elsewhere | Common model in Germany |
|---|---|
| Fast closeness, wide circle | Slow closeness, narrow circle |
| Spontaneous meetings ("shall we go out?") | Planned meetings (in two weeks) |
| Trust given first, tested later | Trust earned over time, then durable |
| Meeting through shared circles | Meeting through a shared **activity** |

That last line is the heart of it: people here grow close less through conversation than by **doing something together, repeatedly**.

## The one rule: repetition

One-off events rarely produce friendships here. What works is anything that happens **weekly, at the same time, with the same people**:

- **Hochschulsport:** the university's sports programme. Places fill fast at the start of term and fees are usually nominal — volleyball, dance, climbing, swimming and dozens more.
- **Verein (club or association):** German social life is organised heavily through clubs — sports, choirs, chess, photography, the volunteer fire brigade. Membership fees are often low, and internationals are welcome.
- **Fachschaft:** your department's student body, doubling as an information hub and a social centre. The moment you contribute, you stop being a face and become a name.
- **Language tandem:** you offer your language, someone offers German. Most universities run official matching, which delivers language practice and contacts at once.
- **Buddy and mentoring programmes:** run through the International Office, usually for your first semester.
- **Volunteering (Ehrenamt):** refugee support, environmental work, animal shelters. Volunteering is respected here and opens doors.
- **Shared flats (WG):** one of the strongest social sources there is. Choosing flatmates is a social decision — our [WG guide](/en/blog/finding-a-wg-in-germany-your-comprehensive-guide-to-dorm-wg-en) covers that side too.

You do not need all of them. **Sticking with one for six months** works incomparably better than attending six different events once each.

## Invitation etiquette: what to do when you are asked over

If you have been invited to someone's home, the relationship is further along than you think. A few local customs:

- **Be on time.** Ten minutes late is not politeness here, it is a problem.
- **Bring something:** wine, dessert, or something you cooked. Small, but expected.
- **Splitting the bill is the norm.** In restaurants everyone pays their own ("getrennt, bitte"). Insisting on paying for others reads as odd rather than generous.
- **Watch when you leave.** Visits running well past midnight collide with quiet hours on weeknights (see [the unwritten rules](/en/blog/unwritten-rules-of-daily-life-in-germany-en)).
- **Du or Sie:** among students it is almost always *du*. Start with *Sie* for lecturers, officials and older neighbours; the *du* will be offered to you.

## The international bubble trap

Every newly arrived student faces two paths: the circle of people who share your language, and the local environment. The honest answer is that **you need both.**

Your own community carries you through the first months with knowledge, food and familiarity. But if it stays your only circle, two things follow: your German stalls, and the professional network you will need after graduation never forms. A significant share of jobs here moves through contacts rather than advertisements.

The practical balance: **at least once a week, be in a setting where German is spoken and nobody from your own country is present.** That single rule is what makes many students' second year completely different from their first.

## The language threshold is real, but lower than you think

Deep conversation needs B2 — true. But for friendship to *begin*, **A2 to B1 is often enough**: playing sport, cooking, gaming or hiking together demands repetition more than vocabulary.

How to get German into your daily life is the final part of this series: [learning German through culture](/en/blog/learning-german-through-culture-german-media-and-podcasts-en). For the exam side, see our [comparison of TestDaF, DSH and telc](/en/blog/testdaf-dsh-telc-which-exam-is-more-advantageous-for-germany-in-en).

## When loneliness turns serious

There is a difference between the loneliness of an adjustment phase and clinical loneliness. Signs of the latter: sleep and appetite disruption lasting weeks, no longer attending classes, withdrawing entirely, persistent hopelessness.

The places to go are clear and free: the **Studierendenwerk's psychological counselling service** (available in English in many cities), the **International Office**, and a GP. Your health insurance covers this — our [insurance guide](/en/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs-en) explains what is covered and how.

## Frequently asked questions

### Do Germans not want international friends?
They do — just not **quickly**. The same slowness applies among themselves. The difference is that you are in a hurry and they are not.

### Nobody starts a conversation at university. Should I?
Yes — and it is read as normal here, not intrusive. The most effective opener comes from shared context: lecture notes, an exam date, a group assignment, a lab slot.

### Are Erasmus and international events worth it?
For basic social needs, yes; for a local network, only partly. You will mostly meet other international students there. Balance them with a Verein or Hochschulsport.

### Do I have to be friends with my flatmates?
No, and it is not expected. A German WG is often an organisational arrangement. Proposing a shared dinner is still a low-cost, high-return move.

### Is it bad to spend time mostly with people from my own country?
No — but if that becomes your only circle, you pay for it in language and professional network. The rule is simple: keep your own community and add one local setting per week.

### How long until I have a real circle of friends?
Typically **one to two semesters**, if you keep one recurring activity going. Continuity shortens it; starting over every semester stretches it.

## Conclusion and honest advice

Friendship in Germany is not a personality test; it is a question of **persistence**. Pick one activity, show up at the same time for six months, make sure three people learn your name — the rest follows.

And accept this early: your first semester will probably be lonely. That says nothing about you; it is the speed of the system. The second semester is the harvest of the repetition you put in during the first.

The rest of the series: [culture shock and your first six months](/en/blog/culture-shock-in-germany-first-six-months-for-students-en) · [the unwritten rules](/en/blog/unwritten-rules-of-daily-life-in-germany-en) · [learning German through culture](/en/blog/learning-german-through-culture-german-media-and-podcasts-en)

*These observations describe general tendencies as of September 2026 and vary by university and city. Your campus International Office and Fachschaft are the most current sources.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'making-friends-in-germany-as-an-international-student',
                'title' => 'Almanya\'da Arkadaş Edinmek: Neden Zor, Nasıl Kolaylaşır',
                'excerpt' => 'Freund ile "arkadaş" aynı şey değil. Almanya\'da yakınlık sohbetle değil tekrarla kuruluyor: Verein, Hochschulsport, Fachschaft, tandem ve WG mekanizması; davet görgüsü, uluslararası balon tuzağı ve yalnızlık ciddileşirse nereye gidilir.',
                'meta_title' => 'Almanya\'da Arkadaş Edinmek: Yavaş Ama İşleyen Yol',
                'meta_description' => 'Almanya\'da arkadaşlık neden yavaş kurulur: Freund/Bekannter farkı, Verein ve Hochschulsport, davet görgüsü, dil eşiği ve yalnızlıkla baş etme (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug' => 'making-friends-in-germany-as-an-international-student-de',
                'title' => 'Freunde finden in Deutschland: warum es schwer ist und was hilft',
                'excerpt' => 'Nähe entsteht hier nicht durch Gespräche, sondern durch Wiederholung: Verein, Hochschulsport, Fachschaft, Sprachtandem und WG. Dazu Einladungsgepflogenheiten, die Falle der internationalen Blase und was bei ernster Einsamkeit hilft.',
                'meta_title' => 'Freunde finden in Deutschland: der langsame, aber sichere Weg',
                'meta_description' => 'Warum Freundschaften in Deutschland langsam entstehen: Freund vs. Bekannter, Verein und Hochschulsport, Einladungsregeln, Sprachschwelle, Hilfe (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug' => 'making-friends-in-germany-as-an-international-student-en',
                'title' => 'Making Friends in Germany: Why It Is Hard and What Works',
                'excerpt' => 'Closeness here grows out of repetition rather than conversation: clubs, university sports, your department body, language tandems and shared flats. Plus invitation etiquette, the international-bubble trap and what to do when loneliness turns serious.',
                'meta_title' => 'Making Friends in Germany: The Slow Route That Works',
                'meta_description' => 'Why friendships form slowly in Germany: Freund vs Bekannter, clubs and university sports, invitation etiquette, the language threshold and support (2026).',
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
            'making-friends-in-germany-as-an-international-student',
            'making-friends-in-germany-as-an-international-student-de',
            'making-friends-in-germany-as-an-international-student-en',
        ])->delete();
    }
};

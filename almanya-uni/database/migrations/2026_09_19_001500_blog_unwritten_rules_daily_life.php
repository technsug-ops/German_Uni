<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN) — KÜLTÜR KÜMESİ 2/4: Almanya'da gündelik hayatın yazısız kuralları.
 *
 * Doğrulanmış veriler (Eylül 2026):
 *   - Ruhezeit: gece 22:00-06:00; pazar ve resmî tatiller gün boyu. Dayanak eyalet
 *     Landesimmissionsschutzgesetz'leri + ev Hausordnung'u → eyalet/bina farkı vurgulandı.
 *   - Pfand: Einweg 25 ct; Mehrweg genelde 8 ct (bira) / 15 ct (çoğu diğer şişe) — VerpackG.
 *   - Rundfunkbeitrag: 18,36 €/ay, KONUT başına (WG'de tek ücret, bölüşülür), 01.08.2021'den beri
 *     değişmedi; muafiyet esas olarak BAföG alanlara — uluslararası öğrencilerin çoğu BAföG almaz,
 *     bu yüzden "öğrenciyim, muafım" beklentisi yanlış. rundfunkbeitrag.de
 *   - Ladenschlussgesetz/eyalet düzenlemeleri: pazar kapalı, istisnalar (gar, havalimanı, fırın,
 *     verkaufsoffener Sonntag).
 * Küme: 1) kültür şoku 2) yazısız kurallar 3) arkadaş edinmek 4) kültürle Almanca.
 * Yazar: Halil Yaprakli. Kategori: german-life-culture.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '2d6ec4a1-58f3-41bc-8a0e-7b9d3c5f2e84';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'german-life-culture')->value('id')
            ?? DB::table('categories')->where('slug', 'yasam')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da hiç kimse sana kuralları anlatmaz. Çünkü onlar için bunlar kural değil, sadece **hayatın işleyişi**. Sen ise ilk aylarda bunları tek tek, genellikle utanarak öğrenirsin: komşunun kapıyı çalmasıyla, kasadaki bakışla, posta kutusuna düşen bir faturayla.

Bu yazı o listeyi önden veriyor. Hepsi küçük şeyler; ama toplamı, "burada kendimi beceriksiz hissediyorum" duygusunun büyük kısmını oluşturuyor.

## 1. Sessizlik saatleri (Ruhezeit)

En sık ihlal edilen ve en hızlı sorun çıkaran kural bu.

| Zaman | Kural |
|---|---|
| **22:00 – 06:00** (her gün) | Gece sessizliği: müzik, çamaşır makinesi, matkap, yüksek sesli konuşma yok |
| **Pazar ve resmî tatiller** | Gün boyu sessizlik: taşınma, delme, çim biçme, gürültülü tamirat yok |
| **13:00 – 15:00** (bazı binalar) | Öğle sessizliği — yasadan çok **ev yönetmeliğinden** (Hausordnung) gelir |

Dayanak eyaletlerin gürültü mevzuatı ve binanın Hausordnung'udur; ayrıntı eyaletten eyalete, hatta binadan binaya değişir. **Pratik kural:** taşınacaksan hafta içi gündüz taşın, parti verecekseniz komşulara bir gün önceden haber verin (kapıya küçük bir not klasik ve gerçekten işe yarar).

## 2. Pazar günü ülke kapanır

Marketler, mağazalar, kuaförler pazar günü **kapalıdır**. İstisnalar: tren garlarındaki ve havalimanlarındaki dükkânlar, fırınlar (genelde birkaç saat), benzin istasyonları, eczane nöbeti ve yılda birkaç kez ilan edilen *verkaufsoffener Sonntag*.

Yeni gelenin klasik hatası: cumartesi akşamı alışverişi ertelemek. Cumartesi akşamı kapanan market, pazartesi sabaha kadar kapalıdır.

Resmî tatiller de aynı kuralı izler — ve bunlar **eyalete göre değişir**. Bavyera'da tatil olan gün Berlin'de iş günü olabilir.

## 3. Çöp ayrıştırma ve Pfand

Çöp ayrıştırma burada çevrecilikten çok **temel bir düzen kuralı** olarak görülür. Tipik kategoriler:

- **Papier** (mavi): kâğıt, karton
- **Bio** (kahverengi): yemek ve bitki atıkları
- **Verpackung / Gelber Sack** (sarı): plastik, metal, kompozit ambalaj
- **Glas** (kumbara): renk ayrımıyla — beyaz, yeşil, kahverengi
- **Restmüll** (siyah): geri kalan her şey

Şişe iadesi (**Pfand**) ayrı bir sistemdir ve para senin cebindedir:

| Tür | Depozito |
|---|---|
| Tek kullanımlık kutu ve PET şişe (Einweg) | **25 cent** |
| Geri dönüşümlü bira şişesi (Mehrweg) | genelde **8 cent** |
| Diğer geri dönüşümlü şişeler | genelde **15 cent** |

Market girişindeki otomata verirsin, fiş çıkar, kasada düşülür. Bir öğrencinin yıllık Pfand'ı küçümsenmeyecek bir tutara ulaşır — ve doldurulmuş şişeleri sokakta çöp kutusunun **yanına** bırakmak, toplayanlar için bırakılmış kabul edilen yaygın bir nezaket davranışıdır.

## 4. Sürpriz fatura: Rundfunkbeitrag

Yeni gelen öğrencinin en çok şaşırdığı kalem budur. Kamu yayın katkı payı **aylık 18,36 €**'dur ve **kişi başına değil, konut başına** alınır.

- **WG'de yaşıyorsan** daire için toplam tek katkı payı ödenir; ev arkadaşları bölüşür. Yeni taşınan kişi genelde ayrıca kaydolmaz — biri zaten ödüyordur.
- **Tek başına yaşıyorsan** ödeme sana aittir.
- **Muafiyet** esas olarak BAföG alanlara tanınır. Uluslararası öğrencilerin büyük çoğunluğu BAföG almadığı için *"öğrenciyim, muafım"* varsayımı burada çalışmaz.
- Anmeldung yaptıktan birkaç hafta sonra adresine mektup gelir; görmezden gelmek borcu büyütür.

## 5. Para, ödeme ve bahşiş

Kart kullanımı hızla yaygınlaşsa da **nakit hâlâ hayatta**: küçük fırınlar, çoğu döner büfesi, bazı barlar ve haftalık pazarlar kart kabul etmeyebilir. Cebinde 20–30 € taşımak pratik bir alışkanlık.

- Kartla ödeyeceksen "Karte, bitte" demek yeterli; bazı yerlerde asgari tutar olur.
- Bahşiş: restoranda genelde **yuvarlama ya da %5–10**. Masaya bırakmak yerine ödeme anında toplam tutarı söylemek (ör. 18 € hesapta "20, bitte") yerel alışkanlıktır.
- Girokonto açmadan önce Anmeldung gerekir — sıralama bozulursa ilk ay zorlaşır.

## 6. Komşuluk ve ev kuralları

- **Hausordnung** (ev yönetmeliği) kira sözleşmesinin parçasıdır: çöp günleri, merdiven temizliği, bodrum kullanımı, bisiklet yeri.
- Bazı binalarda **Kehrwoche** vardır: merdiven/ortak alan temizliği haftalık sırayla kiracılara aittir. Atlarsan konuşulur.
- Kapı zili ve posta kutusuna **isim etiketi** koymak gerekir; yoksa kargon ve resmî mektupların gelmez. Resmî yazışmalar için bu kritik — Ausländerbehörde mektubunu kaçırmak gerçek bir risktir ([gecikme yazımıza](/tr/blog/auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage) bak).
- Havalandırma: günde birkaç kez pencereyi tam açıp kısa süre havalandırmak (**Stoßlüften**) beklenen davranıştır; küf oluşursa kiracı sorumlu tutulabilir.

## 7. Sokakta ve toplu taşımada

- **Kırmızı ışıkta karşıya geçilmez** — özellikle yanında çocuk varsa uyarı alırsın.
- **Bisiklet yolunda yürünmez.** Kaldırımdaki kırmızı/ayrı şerit bisikletlidir ve zili çalan haklıdır.
- Toplu taşımada bilet kontrolü sivil yapılır; kaçak binmenin cezası tipik olarak **60 €** düzeyindedir ve öğrenci kartın (Semesterticket) geçerlilik alanını bilmen gerekir.
- Trende sessizlik beklenir; telefonla yüksek sesle konuşmak hoş karşılanmaz.

## 8. Randevu kültürü

Doktor, kuaför, banka, üniversite sekreterliği, hatta bazı yerlerde çöp merkezine gitmek bile **Termin** ister. Habersiz gitmek çoğu kapıda işe yaramaz. İki pratik sonuç:

1. Randevuyu ihtiyacın doğduğu gün al, ihtiyaç aciliyete dönüştüğünde değil.
2. Gelemeyeceksen iptal et — iptal edilmeyen randevu bazı yerlerde ücretlendirilir ve not düşülür.

## Sıkça Sorulanlar

### Gece 22:00'den sonra duş alabilir miyim?
Evet. Yaygın efsanenin aksine duş almak yasak değildir; korunan şey "oda ses düzeyini aşan gürültü"dür. Ama çamaşır/bulaşık makinesini gece çalıştırmak çoğu binada tartışma yaratır.

### Komşum gürültüden şikâyet etti, ne yapmalıyım?
Önce doğrudan ve sakin konuş: çoğu olay burada biter. Yazılı uyarı (Abmahnung) gelirse ciddiye al; tekrarlayan ihlaller kira sözleşmesi için gerekçe oluşturabilir.

### Rundfunkbeitrag'ı ödemezsem ne olur?
Borç birikir ve icra takibine kadar gidebilir; "bilmiyordum" geçerli bir savunma değildir. WG'de yaşıyorsan ödemenin zaten yapıldığını ev arkadaşlarından teyit et ve payını ver.

### Çöpü yanlış kutuya atarsam ceza gelir mi?
Bireysel cezadan çok, bina düzeyinde uyarı ve ek maliyet üretir: yanlış doldurulmuş konteyner boşaltılmayabilir ve fatura tüm binaya yansıyabilir. Bu yüzden komşular bu konuda hassastır.

### Pazar günü alışveriş gerçekten imkânsız mı?
Şehir içinde evet, ama gar ve havalimanı marketleri açıktır; fiyatlar biraz yüksektir. Büyük şehirlerde bazı "Spätkauf/Kiosk" işletmeleri de açık olur.

### Bu kuralların hepsi her eyalette aynı mı?
Hayır. Sessizlik saatleri, tatiller ve mağaza açılışları eyalet mevzuatına bağlıdır; ev içi kurallar ise Hausordnung'a. Taşındığında iki şeyi oku: kira sözleşmesinin eki ve şehrin resmî sayfasındaki atık/gürültü bilgisi.

## Sonuç ve dürüst tavsiye

Bu kuralların ortak mantığı şu: **öngörülebilirlik.** Herkes aynı şeyi yaparsa kimse kimseyi rahatsız etmez. Onları keyfi kısıtlamalar olarak değil, paylaşılan bir protokol olarak okuduğunda öğrenmesi de kolaylaşıyor.

İlk hafta için pratik liste: Hausordnung'u oku, posta kutusuna isim etiketi koy, çöp kategorilerini öğren, Pfand otomatını bir kez dene, Rundfunkbeitrag mektubunu bekle ve cebinde nakit taşı. Bu altısı, "acemi" hissettiren anların çoğunu ortadan kaldırıyor.

Kümenin diğer yazıları: [kültür şoku ve ilk altı ay](/tr/blog/culture-shock-in-germany-first-six-months-for-students) · [Almanya'da arkadaş edinmek](/tr/blog/making-friends-in-germany-as-an-international-student) · [kültürle Almanca öğrenmek](/tr/blog/learning-german-through-culture-german-media-and-podcasts)

*Tutarlar ve kurallar 2026 Eylül itibarıyla geçerlidir; sessizlik saatleri, tatiller, mağaza açılışları ve katkı payı değişebilir ve eyalete göre farklılaşır. Kendi şehrinin ve binanın kurallarını esas al.*
MD;

        $deBody = <<<'MD'
In Deutschland erklärt dir niemand die Regeln. Für die meisten sind es keine Regeln, sondern schlicht **der Lauf der Dinge**. Du lernst sie in den ersten Monaten einzeln — meist etwas peinlich berührt: durch das Klingeln der Nachbarin, den Blick an der Kasse, einen Brief im Briefkasten.

Dieser Artikel liefert die Liste vorab. Es sind lauter Kleinigkeiten; zusammen machen sie aber einen großen Teil des Gefühls aus, sich hier ungeschickt zu bewegen.

## 1. Ruhezeiten

Die am häufigsten verletzte Regel — und die, die am schnellsten Ärger macht.

| Zeit | Regel |
|---|---|
| **22:00 – 06:00** (täglich) | Nachtruhe: keine Musik, keine Waschmaschine, keine Bohrmaschine, keine lauten Gespräche |
| **Sonn- und Feiertage** | Ganztägige Ruhe: kein Umzug, kein Bohren, kein Rasenmähen, keine lauten Reparaturen |
| **13:00 – 15:00** (manche Häuser) | Mittagsruhe — seltener Gesetz, meist **Hausordnung** |

Grundlage sind die Immissionsschutzgesetze der Länder und die Hausordnung; die Details unterscheiden sich je nach Bundesland und sogar Gebäude. **Praxisregel:** werktags tagsüber umziehen, und vor einer Feier die Nachbarn einen Tag vorher informieren — der Zettel im Treppenhaus ist ein Klassiker und wirkt tatsächlich.

## 2. Sonntags steht das Land still

Supermärkte, Geschäfte und Friseure haben sonntags **geschlossen**. Ausnahmen: Läden in Bahnhöfen und an Flughäfen, Bäckereien (meist für ein paar Stunden), Tankstellen, der Apothekennotdienst und die wenigen *verkaufsoffenen Sonntage* im Jahr.

Der klassische Anfängerfehler: den Einkauf auf Samstagabend zu verschieben. Was samstags schließt, bleibt bis Montagmorgen zu.

Für Feiertage gilt dasselbe — und die sind **Ländersache**. Was in Bayern frei ist, kann in Berlin ein Arbeitstag sein.

## 3. Mülltrennung und Pfand

Mülltrennung gilt hier weniger als Umweltgeste denn als **Grundregel der Ordnung**. Die üblichen Kategorien:

- **Papier** (blau): Papier und Kartonagen
- **Bio** (braun): Speise- und Pflanzenreste
- **Verpackung / Gelber Sack** (gelb): Kunststoff, Metall, Verbundverpackungen
- **Glas** (Container): nach Farben getrennt — weiß, grün, braun
- **Restmüll** (schwarz): alles Übrige

Das Pfandsystem ist davon getrennt, und das Geld gehört dir:

| Art | Pfand |
|---|---|
| Einwegdosen und PET-Flaschen | **25 Cent** |
| Mehrweg-Bierflaschen | meist **8 Cent** |
| Andere Mehrwegflaschen | meist **15 Cent** |

Du gibst sie am Automaten im Markt zurück, bekommst einen Bon und löst ihn an der Kasse ein. Über ein Jahr summiert sich das spürbar — und volle Flaschen **neben** einen Mülleimer zu stellen gilt als verbreitete Geste gegenüber Sammlerinnen und Sammlern.

## 4. Die überraschende Rechnung: Rundfunkbeitrag

Der Posten, der neu angekommene Studierende am meisten überrascht. Der Rundfunkbeitrag beträgt **18,36 € im Monat** und wird **pro Wohnung** erhoben, nicht pro Person.

- **In einer WG** fällt für die Wohnung insgesamt nur ein Beitrag an; die Mitbewohnenden teilen ihn. Wer neu einzieht, meldet sich meist nicht zusätzlich an — jemand zahlt bereits.
- **Wer allein wohnt**, zahlt selbst.
- Eine **Befreiung** gibt es im Kern für BAföG-Empfangende. Die meisten internationalen Studierenden beziehen kein BAföG, deshalb trägt die Annahme *„ich bin Studentin, also befreit"* hier nicht.
- Einige Wochen nach der Anmeldung kommt Post an deine Adresse; sie zu ignorieren lässt die Forderung wachsen.

## 5. Geld, Bezahlen, Trinkgeld

Kartenzahlung breitet sich aus, aber **Bargeld lebt**: kleine Bäckereien, viele Imbisse, manche Bars und Wochenmärkte nehmen keine Karte. 20–30 € dabeizuhaben ist praktisch.

- „Karte, bitte" genügt; mancherorts gibt es einen Mindestbetrag.
- Trinkgeld: im Restaurant üblicherweise **aufrunden oder 5–10 %**. Statt Geld auf dem Tisch zu lassen, nennt man beim Bezahlen den Gesamtbetrag (bei 18 € etwa „20, bitte").
- Vor dem Girokonto steht die Anmeldung — gerät die Reihenfolge durcheinander, wird der erste Monat mühsam.

## 6. Nachbarschaft und Hausregeln

- Die **Hausordnung** ist Teil des Mietvertrags: Mülltage, Treppenreinigung, Kellernutzung, Fahrradabstellplatz.
- In manchen Häusern gibt es die **Kehrwoche**: Treppenhaus und Gemeinschaftsflächen werden reihum von den Mietparteien gereinigt. Wer sie auslässt, wird darauf angesprochen.
- **Namensschild** an Klingel und Briefkasten ist Pflicht, sonst kommen Pakete und Behördenpost nicht an. Für amtliche Schreiben ist das kritisch — einen Brief der Ausländerbehörde zu verpassen, ist ein echtes Risiko (siehe unseren Artikel zu [Verzögerungen](/de/blog/auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage-de)).
- Lüften: mehrmals täglich kurz und vollständig öffnen (**Stoßlüften**) wird erwartet; bei Schimmel kann die Mietpartei haften.

## 7. Auf der Straße und im Nahverkehr

- **Bei Rot geht man nicht über die Straße** — besonders in Gegenwart von Kindern wirst du darauf hingewiesen.
- **Auf dem Radweg läuft man nicht.** Der abgesetzte Streifen gehört den Radfahrenden, und wer klingelt, hat recht.
- Kontrollen im Nahverkehr erfolgen in Zivil; Schwarzfahren kostet typischerweise **60 €**, und du solltest den Geltungsbereich deines Semestertickets kennen.
- Im Zug wird Ruhe erwartet; lautes Telefonieren kommt schlecht an.

## 8. Terminkultur

Arztpraxis, Friseur, Bank, Studierendensekretariat und mancherorts sogar der Wertstoffhof verlangen einen **Termin**. Unangekündigt zu erscheinen, funktioniert an den meisten Türen nicht. Zwei praktische Folgen:

1. Termin an dem Tag buchen, an dem der Bedarf entsteht — nicht erst, wenn er dringend wird.
2. Absagen, wenn du nicht kommen kannst: nicht abgesagte Termine werden mancherorts berechnet und vermerkt.

## Häufige Fragen

### Darf ich nach 22 Uhr duschen?
Ja. Entgegen dem verbreiteten Mythos ist Duschen nicht verboten; geschützt wird vor Lärm über Zimmerlautstärke. Wasch- und Spülmaschine nachts laufen zu lassen, sorgt dagegen in vielen Häusern für Ärger.

### Die Nachbarin hat sich über Lärm beschwert — was jetzt?
Zuerst direkt und ruhig sprechen; die meisten Fälle enden hier. Kommt eine schriftliche Abmahnung, nimm sie ernst: wiederholte Verstöße können mietrechtliche Folgen haben.

### Was passiert, wenn ich den Rundfunkbeitrag nicht zahle?
Die Forderung wächst und kann bis zur Vollstreckung gehen; „ich wusste es nicht" zählt nicht. In der WG prüfen, ob bereits gezahlt wird, und den Anteil übernehmen.

### Gibt es ein Bußgeld, wenn ich Müll falsch trenne?
Seltener individuell — eher Ärger auf Hausebene und Zusatzkosten: falsch befüllte Tonnen werden unter Umständen nicht geleert, und die Rechnung trifft alle. Deshalb reagieren Nachbarn empfindlich.

### Ist Einkaufen am Sonntag wirklich unmöglich?
In der Stadt weitgehend ja, aber Bahnhofs- und Flughafensupermärkte öffnen; die Preise liegen etwas höher. In Großstädten haben zudem Spätkäufe und Kioske auf.

### Gelten diese Regeln überall gleich?
Nein. Ruhezeiten, Feiertage und Ladenöffnung sind Ländersache, die Hausregeln stehen in der Hausordnung. Beim Einzug also zwei Dinge lesen: die Anlage zum Mietvertrag und die Abfall- und Lärminfos deiner Stadt.

## Fazit und ehrlicher Rat

Die gemeinsame Logik dieser Regeln heißt **Berechenbarkeit**. Wenn alle dasselbe tun, stört niemand niemanden. Als geteiltes Protokoll gelesen statt als Willkür, lernen sie sich deutlich leichter.

Praktische Liste für die erste Woche: Hausordnung lesen, Namensschild anbringen, Mülltrennung lernen, einmal den Pfandautomaten ausprobieren, Post zum Rundfunkbeitrag erwarten und Bargeld dabeihaben. Diese sechs Punkte entschärfen die meisten peinlichen Momente.

Die weiteren Teile der Reihe: [Kulturschock und die ersten sechs Monate](/de/blog/culture-shock-in-germany-first-six-months-for-students-de) · [Freunde finden in Deutschland](/de/blog/making-friends-in-germany-as-an-international-student-de) · [Deutsch über Kultur lernen](/de/blog/learning-german-through-culture-german-media-and-podcasts-de)

*Beträge und Regeln gelten mit Stand September 2026; Ruhezeiten, Feiertage, Ladenöffnung und der Beitrag können sich ändern und unterscheiden sich nach Bundesland. Maßgeblich sind die Regeln deiner Stadt und deines Hauses.*
MD;

        $enBody = <<<'MD'
Nobody in Germany explains the rules to you. For most people they are not rules at all, just **how things work**. You learn them one at a time over your first months, usually with a little embarrassment: a neighbour at the door, a look at the checkout, a letter in your postbox.

This article hands you the list in advance. Every item is small; together they account for much of that feeling of being clumsy here.

## 1. Quiet hours (Ruhezeit)

The most frequently broken rule, and the one that causes trouble fastest.

| Time | Rule |
|---|---|
| **22:00 – 06:00** (daily) | Night quiet: no music, washing machine, power tools or loud conversation |
| **Sundays and public holidays** | Quiet all day: no moving house, drilling, lawn mowing or noisy repairs |
| **13:00 – 15:00** (some buildings) | Midday quiet — rarely law, usually the building's **Hausordnung** |

The basis is each federal state's noise legislation plus your building's house rules, so details differ by state and even by building. **Practical rule:** move house on a weekday during the day, and tell your neighbours a day before a party — the note in the stairwell is a cliché because it works.

## 2. On Sundays the country closes

Supermarkets, shops and hairdressers are **closed** on Sundays. The exceptions: shops inside railway stations and airports, bakeries (usually for a few hours), petrol stations, the on-call pharmacy, and the handful of *verkaufsoffene Sonntage* each year.

The classic newcomer mistake is leaving the shopping until Saturday evening. What closes on Saturday stays closed until Monday morning.

Public holidays follow the same logic — and they are **set by each state**. A holiday in Bavaria can be a working day in Berlin.

## 3. Waste separation and deposits

Separating waste is treated less as an environmental gesture than as **a basic rule of order**. The usual categories:

- **Papier** (blue): paper and cardboard
- **Bio** (brown): food and plant waste
- **Verpackung / Gelber Sack** (yellow): plastics, metals, composite packaging
- **Glas** (bottle banks): separated by colour — clear, green, brown
- **Restmüll** (black): everything else

The deposit system (**Pfand**) is separate, and the money is yours:

| Type | Deposit |
|---|---|
| Single-use cans and PET bottles | **25 cents** |
| Reusable beer bottles | usually **8 cents** |
| Other reusable bottles | usually **15 cents** |

You return them at the machine inside the supermarket, get a voucher and redeem it at the till. Over a year it adds up noticeably — and leaving full bottles **beside** a public bin rather than in it is a widely understood courtesy towards collectors.

## 4. The surprise bill: the broadcasting fee

This is the item that startles newly arrived students most. The Rundfunkbeitrag is **€18.36 per month** and is charged **per dwelling, not per person**.

- **In a shared flat (WG)** only one fee is due for the flat; flatmates split it. Someone moving in usually does not register separately — somebody is already paying.
- **Living alone**, it is yours to pay.
- **Exemption** is essentially for people receiving BAföG. Most international students do not receive BAföG, so the assumption *"I am a student, so I am exempt"* does not hold here.
- A letter arrives at your address a few weeks after you register. Ignoring it only grows the debt.

## 5. Money, payment and tipping

Card payment is spreading, but **cash is very much alive**: small bakeries, many takeaways, some bars and weekly markets may not take cards. Carrying €20–30 is a practical habit.

- "Karte, bitte" is enough; some places have a minimum amount.
- Tipping: in restaurants, **round up or add 5–10%**. Rather than leaving money on the table, you state the total when paying (for an €18 bill: "20, bitte").
- A bank account comes after your address registration — get that order wrong and your first month gets harder.

## 6. Neighbours and house rules

- The **Hausordnung** is part of your lease: bin days, stairwell cleaning, cellar use, where bikes go.
- Some buildings run a **Kehrwoche**: tenants take weekly turns cleaning the stairwell and shared areas. Skipping yours will be mentioned.
- A **name tag** on your doorbell and postbox is required, otherwise parcels and official post do not reach you. That matters enormously for official mail — missing a letter from the immigration office is a real risk (see our piece on [delays](/en/blog/auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage-en)).
- Ventilation: opening windows fully for short bursts several times a day (**Stoßlüften**) is expected; if mould appears, tenants can be held liable.

## 7. On the street and on public transport

- **You do not cross on a red light** — especially with children present, you will be told.
- **You do not walk in the bike lane.** The separate strip belongs to cyclists, and the one ringing the bell is in the right.
- Ticket inspections are done in plain clothes; travelling without a valid ticket typically costs **€60**, and you should know the area your semester ticket covers.
- Trains are quiet spaces; loud phone calls are not appreciated.

## 8. Appointment culture

Doctors, hairdressers, banks, the student registry and in some places even the recycling centre require a **Termin**. Turning up unannounced does not work at most doors. Two practical consequences:

1. Book the appointment on the day the need appears, not when it becomes urgent.
2. Cancel if you cannot attend — missed appointments are charged and noted in some places.

## Frequently asked questions

### Can I shower after 22:00?
Yes. Contrary to the popular myth, showering is not banned; what is protected against is noise above normal room level. Running a washing machine or dishwasher at night, however, will cause friction in many buildings.

### My neighbour complained about noise. What now?
Talk to them directly and calmly first; most cases end there. If a written warning (Abmahnung) arrives, take it seriously — repeated breaches can have tenancy consequences.

### What happens if I do not pay the broadcasting fee?
The debt accumulates and can end up in enforcement; "I did not know" is not a defence. In a shared flat, confirm with your flatmates that it is already being paid and contribute your share.

### Will I be fined for sorting waste wrongly?
Individual fines are rare; what happens instead is building-level friction and extra cost, because wrongly filled bins may be left unemptied and the bill lands on everyone. That is why neighbours care.

### Is shopping on Sunday really impossible?
Within the city, largely yes — but station and airport supermarkets open, at slightly higher prices. Large cities also have late shops and kiosks.

### Are these rules the same everywhere?
No. Quiet hours, holidays and shop opening are state matters; house rules live in the Hausordnung. When you move in, read two things: the annex to your lease, and your city's official waste and noise information.

## Conclusion and honest advice

The shared logic behind all of this is **predictability**. If everyone does the same thing, nobody disturbs anyone. Read as a shared protocol rather than arbitrary restriction, the rules are far easier to learn.

A practical first-week list: read the house rules, put your name on the postbox, learn the waste categories, try a deposit machine once, expect the broadcasting fee letter, and carry some cash. Those six remove most of the moments that make you feel like a beginner.

The rest of the series: [culture shock and your first six months](/en/blog/culture-shock-in-germany-first-six-months-for-students-en) · [making friends in Germany](/en/blog/making-friends-in-germany-as-an-international-student-en) · [learning German through culture](/en/blog/learning-german-through-culture-german-media-and-podcasts-en)

*Amounts and rules are current as of September 2026; quiet hours, holidays, shop opening and the broadcasting fee can change and vary by state. Treat your own city's and building's rules as authoritative.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'unwritten-rules-of-daily-life-in-germany',
                'title' => 'Almanya\'nın Yazısız Kuralları: Kimsenin Anlatmadığı Gündelik Hayat',
                'excerpt' => 'Sessizlik saatleri, kapalı pazarlar, çöp kategorileri, Pfand, konut başına alınan Rundfunkbeitrag, nakit ve bahşiş, Hausordnung ve randevu kültürü — ilk aylarda tek tek öğrenilen kuralların önden verilmiş tam listesi.',
                'meta_title' => 'Almanya\'da Yazısız Kurallar: Ruhezeit, Pfand, Rundfunkbeitrag',
                'meta_description' => 'Almanya gündelik hayat kuralları: 22:00-06:00 sessizlik, pazar kapalı, çöp ayrıştırma, 25 cent Pfand, 18,36 € Rundfunkbeitrag ve randevu kültürü (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug' => 'unwritten-rules-of-daily-life-in-germany-de',
                'title' => 'Die ungeschriebenen Regeln des deutschen Alltags',
                'excerpt' => 'Ruhezeiten, geschlossene Sonntage, Mülltrennung, Pfand, der Rundfunkbeitrag pro Wohnung, Bargeld und Trinkgeld, Hausordnung und Terminkultur — die vollständige Liste der Regeln, die man sonst einzeln und mühsam lernt.',
                'meta_title' => 'Ungeschriebene Regeln in Deutschland: Ruhezeit, Pfand, Beitrag',
                'meta_description' => 'Alltagsregeln in Deutschland: Nachtruhe 22-6 Uhr, Sonntagsruhe, Mülltrennung, 25 Cent Pfand, 18,36 € Rundfunkbeitrag und Terminkultur (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug' => 'unwritten-rules-of-daily-life-in-germany-en',
                'title' => 'The Unwritten Rules of Daily Life in Germany',
                'excerpt' => 'Quiet hours, closed Sundays, waste categories, bottle deposits, a broadcasting fee charged per dwelling, cash and tipping, house rules and appointment culture — the complete list newcomers normally learn the hard way.',
                'meta_title' => 'Unwritten Rules in Germany: Quiet Hours, Pfand, Broadcasting Fee',
                'meta_description' => 'Daily life rules in Germany: 22:00-06:00 quiet hours, closed Sundays, waste sorting, 25-cent deposits, the €18.36 broadcasting fee and appointments (2026).',
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
            'unwritten-rules-of-daily-life-in-germany',
            'unwritten-rules-of-daily-life-in-germany-de',
            'unwritten-rules-of-daily-life-in-germany-en',
        ])->delete();
    }
};

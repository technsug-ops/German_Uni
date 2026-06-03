<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Kurucu yazısı revize — kullanıcı geri bildirimine göre:
 * - Daha akıcı + bağlantılı (kolon/tablo azalt)
 * - İlk soru/hook daha dikkat çekici
 * - Otoriter ama dürüst ton
 */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'kurucudan-mektup-almanyauni-neden-var';
        $posts = DB::table('posts')->where('translation_group_id', function ($q) use ($slug) {
            $q->select('translation_group_id')->from('posts')->where('slug', $slug)->limit(1);
        })->get();

        if ($posts->isEmpty()) {
            return;
        }

        foreach ($posts as $p) {
            $content = match ($p->locale) {
                'tr'    => self::founderTR(),
                'en'    => self::founderEN(),
                'de'    => self::founderDE(),
                default => null,
            };
            if (! $content) continue;

            $html = Str::markdown($content, ['html_input' => 'allow']);
            DB::table('posts')->where('id', $p->id)->update([
                'content_md'      => $content,
                'content_html'    => $html,
                'reading_minutes' => max(3, (int) round(str_word_count(strip_tags($html)) / 220)),
                'updated_at'      => now(),
            ]);
        }
    }

    public function down(): void {}

    private static function founderTR(): string
    {
        return <<<'MD'
# AlmanyaUni Neden Var: Kurucudan Açık Mektup

Bir Mayıs gecesi saat 23:40. Telegram'da "Almanya Üniversite Hayalim" diye bir kanal var, 14 bin kişilik. Bir mesaj geliyor:

> *"Arkadaşlar yarın saat 09:00'da uni-assist'e dosya yollayacağım, transkriptimi noter onaylı çevirttim ama VPD almamışım. Şimdi başvurum çöp mü oldu? Eylülde başlamak istiyorum, kafam çok karışık."*

Cevap yazan üç kişi. Üçü de farklı şey diyor. Biri "geç değil, dosyayı yolla, VPD'yi sonra ekle" diyor; biri "kabul etmezler" diyor; biri o kişiyi bir Telegram kanalına davet ediyor. Soru gerçek, panik gerçek, ama net cevap kimsenin elinde değil.

Bu sahne her gece var. Sadece bu mesaj değil — **vize randevusunun neden 7 ay sonraya verildiği**, **Sperrkonto'nun €992'sinin yetip yetmeyeceği**, **Anabin'de diplomanın hangi sınıfta olduğu**, **Krankenkasse'lerin fiyatı sabitken neden seçim yapıldığı**. Bu sorular her gün on binlerce Türk öğrencinin telefonunda dönüyor. Onları arayan kişi sayısı, doğru cevabı bulan kişi sayısından çok daha fazla. Ve bu kişi sayısının çoğu, kendi başvuru sürecini bir karanlığa girerek yaşıyor.

## Bu Sistemin Bu Kadar Karmaşık Olması Gerekmiyor

Adım Halil Yaprakli. Almanya'ya geldim, sürecini yaşadım, eve döndüm, geri geldim, kalmaya karar verdim. Her aşamasında "bunu keşke biri önceden söyleseydi" dediğim onlarca an oldu. Sperrkonto'da Coracle yerine Expatrio seçtim çünkü adres beyanı daha hızlıydı; bunu bilemediğim için ilk başvurumda iki ay kaybettim. uni-assist'e VPD almadan başvurdum, dosyam bekletildi; bunu bilseydim Mart yerine Ocak'ta başvururdum. Bu örnekler birikiyor — ve her biri **bilgi eşitsizliğinin** somut bir maliyeti.

Almanya'nın akademik sistemi karmaşık olabilir ama anlaşılmaz değildir. Bilgi resmi kanallarda — DAAD'da, Hochschulkompass'ta, KMK Anabin'de, Bundesagentur'un BERUFENET'inde — ücretsiz olarak duruyor. Ama bu bilgi Almanca, çoğu zaman bürokratik dilde, ve bir Türk öğrenci için "diploma denklikleri Anabin'de H+, H+-, H-, GO-1, GO-3 olarak sınıflandırılır" cümlesini görmek bir hayli ürkütücü. Yaptığımız şey bu cümleyi açıyor, Türkçeye çeviriyor, Türk öğrenci için ne anlama geldiğini örnekle gösteriyor.

İşte AlmanyaUni bu fikrin etrafında doğdu: **resmi bilgi + Türkçe açıklama + topluluk deneyimi**. Üçü yan yana geldiğinde bir öğrencinin Almanya'ya geçiş kararı bir karanlıktan bir plana dönüşüyor.

## Bugün Nerede Olduğumuz

Platformda şu an 600'ü aşkın aktif Alman üniversitesi var — bu sayı her hafta DAAD ve Hochschulkompass verisi ile karşılaştırılıyor, ekleme veya çıkarma yapılıyor. Bu üniversitelerin 18.000'i aşkın programını filtrelemek için arama motoru çalışıyor: İngilizce öğretim, NC-frei, lisans/master/PhD, alan, şehir, bütçe — istediğin kriterle daralt. Yanlış üniversiteye başvuru, doğru üniversiteyi keşfetmemekten daha pahalı.

Sayısal hesaplar bir başka mesele. Sperrkonto bulucu üç sağlayıcıyı (Expatrio, Coracle, Fintiba) açılış ücreti, adres beyanı süresi ve müşteri desteğine göre karşılaştırıyor. Yaşam maliyeti hesaplayıcısı şehir bazlı kira aralığını DAAD ve DZHW verisine bağlıyor. Vize maliyet hesaplayıcısı sadece konsolosluk ücretini değil, bütün başvuru maliyetlerini (Sperrkonto + sigorta + uni-assist + tercüme + APS + uçuş) tek tabloda gösteriyor — gerçekten bir vize "€75" değil, "€12.000–€13.000" arası.

Bunların yanında 130'u aşkın şehir profili, 200'ü aşkın SSS, ve her hafta yeni eklenen rehber yazılar. Hepsi ücretsiz, hepsi kayıt zorunluluğu olmadan. Premium versiyon mentor seansları gibi gelişmiş özellikler için 2026 sonunda gelecek, ama temel bilgi rehberliği her zaman açık kalacak. Çünkü temel bilgiye erişim bir öğrencinin lüks hakkı değil, hak hakkıdır.

## Kim Yazıyor Bu Yazıları

Yedi editör: Anna Schmidt Berlin'de yaşıyor, Alman akademik sisteminin içerisinden yazıyor — Studienkolleg, Hochschulzulassung gibi sistemin teknik kısımları onun alanı. Ayesha Khan Münih TUM'da uluslararası master öğrencisi, AB dışı bir öğrencinin Sperrkonto, Krankenkasse, Schufa ile imtihanını birinci elden yaşamış; bu deneyim onun yazılarında her satıra siniyor. Elif G. uni-assist başvurularındaki ret sebeplerinin haritasını çıkarmış; Gamze E. TestDaF ve DSH arasındaki fark öğrenciye nasıl açıklanır biliyor; Hakan Kutlu konsolosluk randevusu için saat 06:00 portal kontrolü stratejisini gerçek dünyada uygulamış; Caner Türkdoğru Werkstudent pozisyon başvurusundan başlayıp ilk Junior Developer maaşına kadar olan yolu yaşamış. Bu yedi kişi yazıyor. Ben yedincisiyim, kurucu olarak.

Editörlerin yazdıkları her şey **resmi kaynaklara** dayanıyor (DAAD, KMK, Anabin, BERUFENET, Auswärtiges Amt) ve **topluluk havuzu** ile karşılaştırılıyor — Telegram ve Forum'dan derlediğimiz 142 binin üzerinde mesajı analiz ettik. Bir editör bir konuyu yazarken o konunun toplulukta hangi soruları doğurduğunu görüyor, onların hepsine cevap vermeye çalışıyor.

## On İki Ay Sonra Nerede Olacağız

Önümüzdeki yıl üç şey yapacağız. **Application Tracker** — başvurunun sekiz adımını eligibility kontrolünden vize randevusuna kadar tek dashboard'dan takip etmek mümkün olacak; her adım için ne yapman gerektiği, hangi belgeyi şimdi hazırlaman gerektiği, hangi tarihten önce bitirmen gerektiği orada olacak. **Mentor ağı** — Almanya'da kariyer yapmış Türk profesyonellerle birebir görüşme yapabileceksin; bazıları ücretsiz, bazıları saat başı ücretli. **Forum** 2027'de açılacak — anonim soru-cevap, uzman moderasyonu, kategori arşivi. Bunlar lüks özellikler değil; bunlar başvuru sürecinin kritik anlarında yanlış cevaplara karşı sigortandır.

## Senin Yapabileceğin Şey

Üç şey öneriyorum. **Bir**: doğru kaynaklara güven. DAAD, KMK Anabin, Hochschulkompass, BERUFENET, Auswärtiges Amt. Bu beş site senin tüm temel sorularına resmi cevap verecek; ücretsiz, güncel, otorite. **İki**: AlmanyaUni'de bulduğun her yazıyı bir referans olarak değil bir başlangıç noktası olarak gör — kendi durumun her zaman bir nüans taşır, kendi durumunu kendi kontrolünden geçir. **Üç**: deneyimini paylaş. Forum yakında açılacak; orada yazacağın her gerçek anekdot bir başkasının kararına yön verecek. Bizim yazdıklarımız resmi; senin yazacakların gerçek. İkisi bir araya geldiğinde bir öğrenci sistemde kaybolmuyor.

## Son Söz

Almanya'ya geçiş kararını bir hayalden bir plana dönüştürmek için yalnız olman gerekmiyor. Bu platform tam olarak bu yüzden var. Her yeni eklenen SSS, her tamamlanan tool, her zenginleştirilen üniversite sayfası — hepsi senin kararının arkasındaki belirsizliği azaltmak için. Geri bildirimini bekliyorum; doğru kaynak gördüysen paylaş, hatalı bilgi gördüysen düzeltelim.

Türk öğrenci için Almanya hiç bu kadar erişilebilir olmamıştı. Hep birlikte daha da erişilebilir yapacağız.

**— Halil Yaprakli**
*Kurucu, AlmanyaUni*
*Mayıs 2026*
MD;
    }

    private static function founderEN(): string
    {
        return <<<'MD'
# Why AlmanyaUni Exists: An Open Letter from the Founder

A May night, 11:40 pm. There's a Telegram channel called "My Germany University Dream" — 14,000 members. A message arrives:

> *"Friends, tomorrow at 9 am I'm submitting my file to uni-assist. I had my transcript translated by a notarized translator, but I didn't get a VPD. Is my application trash now? I want to start in September, my head is spinning."*

Three people answer. The three say three different things. One says "submit it anyway, add the VPD later"; one says "they won't accept it"; one redirects them to yet another Telegram channel. The question is real, the panic is real, but no one has a clear answer.

This scene plays out every night. Not just this message — **why your visa appointment is seven months away**, **whether €992/month on the Sperrkonto will be enough**, **what class your diploma falls into on Anabin**, **why you need to choose a Krankenkasse when their prices are all the same by law**. These questions circulate every day across tens of thousands of Turkish students' phones. The number asking is far higher than the number finding a reliable answer. And most of them walk through their own application like walking through a dark room.

## This System Does Not Have to Be This Hard

My name is Halil Yaprakli. I came to Germany, lived the process, went home, came back, decided to stay. At every step there were dozens of moments where I thought *"someone should have told me this in advance."* I picked Expatrio over Coracle on Sperrkonto because the address registration was faster — I didn't know this and lost two months on my first attempt. I applied to uni-assist without a VPD; my file sat in limbo. If I had known, I would have applied in January instead of March. These examples accumulate, and every single one is the concrete cost of **information inequality**.

Germany's academic system can be complex, but it is not opaque. The information sits, free of charge, in official channels — DAAD, Hochschulkompass, KMK Anabin, the BERUFENET database of the Federal Employment Agency. But that information is in German, often in bureaucratic prose, and for a Turkish student the sentence *"diploma equivalence is classified on Anabin as H+, H+-, H-, GO-1, GO-3"* is, frankly, intimidating. What we do is unpack that sentence, translate it, and show with examples what it actually means for a Turkish applicant.

That's the idea AlmanyaUni was built around: **official information + Turkish-language explanation + community experience**. When those three sit side by side, the decision to come to Germany turns from darkness into a plan.

## Where We Are Today

There are over 600 active German universities on the platform — that count is reconciled weekly against DAAD and Hochschulkompass data, with additions and removals applied. A search engine narrows their 18,000+ programs by English-taught status, NC-frei, Bachelor/Master/PhD, field, city, budget. Applying to the wrong university is more expensive than failing to discover the right one.

The numerical tools are a different chapter. The Sperrkonto finder compares three providers (Expatrio, Coracle, Fintiba) on opening fee, address-registration speed, and support quality. The cost-of-living calculator ties city-level rent ranges to DAAD and DZHW data. The visa cost calculator does not just show the €75 consulate fee — it brings the full application cost (Sperrkonto + insurance + uni-assist + translation + APS + flight) into one table; the real number is not "€75" but somewhere between €12,000 and €13,000.

Beyond these, there are 130+ city profiles, 200+ FAQs, and new guides added every week. All free, no registration required. A Premium tier with mentor sessions arrives late 2026, but core guidance will always stay open. Access to basic information is not a luxury for a student — it is a right.

## Who Writes These Pieces

Seven editors. Anna Schmidt lives in Berlin and writes from inside the German academic system — Studienkolleg, Hochschulzulassung, the technical layers are her territory. Ayesha Khan is an international Master's student at TU Munich; she has lived the non-EU student's trial with Sperrkonto, Krankenkasse, Schufa first-hand, and that experience seeps into every line she writes. Elif G. has mapped the typical rejection patterns at uni-assist; Gamze E. knows how to explain the gap between TestDaF and DSH to a student in plain language; Hakan Kutlu has put the 6 am consulate-portal refresh strategy into actual practice; Caner Türkdoğru walked the road from a Werkstudent application to a first Junior Developer salary. These seven write. I am the seventh, as founder.

Everything the editors write is grounded in **official sources** (DAAD, KMK, Anabin, BERUFENET, Auswärtiges Amt) and cross-checked against a **community pool** — over 142,000 messages from Telegram and Forum that we have analysed. When an editor takes on a topic, they see which questions that topic actually raises in the community, and they aim to answer all of them.

## Where We Will Be in Twelve Months

Three things in the next year. **Application Tracker** — the eight steps of the application, from eligibility check to visa appointment, follow-able from a single dashboard; for each step you'll see what to do, which document to prepare now, by what date you should finish. **Mentor network** — one-on-one conversations with Turkish professionals who built careers in Germany; some free, some priced per hour. **Forum** opens in 2027 — anonymous Q&A, expert moderation, category archive. These are not luxury features; they are insurance against wrong answers at critical moments of the application process.

## What You Can Do

Three things. **One**: trust the right sources. DAAD, KMK Anabin, Hochschulkompass, BERUFENET, Auswärtiges Amt. These five sites give you authoritative, free, current answers to every fundamental question. **Two**: treat every AlmanyaUni piece you read as a starting point rather than a final reference — your own situation always carries nuance; verify against your own context. **Three**: share your experience. The forum opens soon; every honest anecdote you contribute there will guide someone else's decision. What we write is official; what you write is real. When the two come together, a student stops getting lost in the system.

## Final Words

You do not need to be alone on the path to Germany; that's exactly why this platform exists. Every clarified FAQ, every completed tool, every enriched university page exists to reduce the uncertainty behind your decision. Your feedback is welcome — if you find a great source, share it; if you spot a wrong piece, let us fix it.

Germany has never been this accessible for Turkish students. Together, we will make it even more so.

**— Halil Yaprakli**
*Founder, AlmanyaUni*
*May 2026*
MD;
    }

    private static function founderDE(): string
    {
        return <<<'MD'
# Warum AlmanyaUni existiert: Ein offener Brief des Gründers

Eine Mai-Nacht, 23:40 Uhr. Es gibt einen Telegram-Kanal namens "Mein Deutschland-Uni-Traum" mit 14.000 Mitgliedern. Eine Nachricht kommt rein:

> *"Leute, morgen um 9 Uhr reiche ich meine Unterlagen bei uni-assist ein. Ich habe mein Zeugnis von einem vereidigten Übersetzer übersetzen lassen, aber kein VPD eingeholt. Ist meine Bewerbung jetzt im Eimer? Ich will im September anfangen, mein Kopf raucht."*

Drei Leute antworten. Die drei sagen drei verschiedene Dinge. Einer sagt "trotzdem einreichen, VPD später nachreichen"; einer sagt "wird nicht akzeptiert"; einer leitet sie in einen weiteren Telegram-Kanal um. Die Frage ist echt, die Panik ist echt, aber niemand hat eine klare Antwort.

Diese Szene spielt sich jede Nacht ab. Nicht nur diese eine Nachricht — **warum dein Visumstermin sieben Monate in der Zukunft liegt**, **ob die 992 € im Sperrkonto wirklich reichen**, **in welche Klasse dein Abschluss in Anabin fällt**, **warum du dir eine Krankenkasse aussuchen sollst, obwohl die Beiträge gesetzlich gleich sind**. Diese Fragen kursieren täglich in den Telefonen von zehntausenden türkischer Studierender. Die Zahl der Fragenden übersteigt bei Weitem die Zahl derer, die eine zuverlässige Antwort finden. Und die meisten gehen durch ihre eigene Bewerbung wie durch einen dunklen Raum.

## Dieses System muss nicht so schwer sein

Mein Name ist Halil Yaprakli. Ich kam nach Deutschland, durchlief den Prozess, ging zurück, kam wieder, blieb. Bei jedem Schritt gab es Dutzende Momente, in denen ich dachte: *"Das hätte mir vorher jemand sagen sollen."* Ich wählte Expatrio statt Coracle für das Sperrkonto, weil die Adressmeldung schneller ging — das wusste ich nicht und verlor beim ersten Anlauf zwei Monate. Ich bewarb mich bei uni-assist ohne VPD; mein Dossier blieb im Limbo. Hätte ich es gewusst, hätte ich im Januar statt im März eingereicht. Solche Beispiele häufen sich, und jedes einzelne ist der konkrete Preis von **Informationsungleichheit**.

Das deutsche Hochschulsystem mag komplex sein, undurchsichtig ist es nicht. Die Informationen liegen kostenlos in offiziellen Kanälen — DAAD, Hochschulkompass, KMK Anabin, die BERUFENET-Datenbank der Bundesagentur für Arbeit. Aber sie sind auf Deutsch, oft in bürokratischer Sprache, und für eine türkische Studierende ist der Satz *"Die Hochschulzugangsberechtigung wird in Anabin als H+, H+-, H-, GO-1, GO-3 klassifiziert"* ehrlich gesagt einschüchternd. Was wir tun: diesen Satz aufdröseln, übersetzen, und mit Beispielen zeigen, was er für eine türkische Bewerberin tatsächlich bedeutet.

Genau auf dieser Idee wurde AlmanyaUni aufgebaut: **offizielle Information + türkischsprachige Erklärung + Community-Erfahrung**. Wenn diese drei nebeneinander stehen, wird aus der Entscheidung, nach Deutschland zu kommen, aus dem Dunkel ein Plan.

## Wo wir heute stehen

Über 600 aktive deutsche Hochschulen sind auf der Plattform — diese Zahl wird wöchentlich mit DAAD- und Hochschulkompass-Daten abgeglichen, neue kommen dazu, andere fallen heraus. Eine Suchmaschine filtert ihre über 18.000 Studiengänge nach Englisch-sprachig, NC-frei, Bachelor/Master/PhD, Fach, Stadt, Budget. Sich an der falschen Uni zu bewerben kostet mehr als die richtige nicht zu entdecken.

Die rechnerischen Tools sind ein eigenes Kapitel. Der Sperrkonto-Finder vergleicht drei Anbieter (Expatrio, Coracle, Fintiba) nach Eröffnungsgebühr, Anmeldegeschwindigkeit und Support-Qualität. Der Lebenshaltungs-Rechner verknüpft Mietspannen auf Stadt-Ebene mit DAAD- und DZHW-Daten. Der Visumskosten-Rechner zeigt nicht nur die Konsulatsgebühr von 75 € — er bringt die kompletten Bewerbungskosten (Sperrkonto + Versicherung + uni-assist + Übersetzung + APS + Flug) in eine Tabelle; die tatsächliche Zahl ist nicht "75 €", sondern liegt zwischen 12.000 und 13.000 €.

Daneben über 130 Städteprofile, über 200 FAQs, und neue Leitfäden in wöchentlichem Takt. Alles kostenlos, ohne Registrierungspflicht. Eine Premium-Variante mit Mentor-Sessions kommt Ende 2026, der Kernbereich bleibt aber immer offen. Zugang zu grundlegender Information ist kein Luxus für Studierende, sondern ein Recht.

## Wer schreibt diese Texte

Sieben Redakteure. Anna Schmidt lebt in Berlin und schreibt aus dem Inneren des deutschen Hochschulsystems — Studienkolleg, Hochschulzulassung, die technischen Schichten sind ihr Gebiet. Ayesha Khan ist internationale Masterstudentin an der TU München; sie hat die Tortur des Nicht-EU-Studierenden mit Sperrkonto, Krankenkasse, Schufa aus erster Hand erlebt, und diese Erfahrung sickert in jede Zeile, die sie schreibt. Elif G. hat die typischen Ablehnungsmuster bei uni-assist kartiert; Gamze E. weiß, wie man den Unterschied zwischen TestDaF und DSH in klarer Sprache erklärt; Hakan Kutlu hat die 6-Uhr-Strategie für Konsulatsportale in der Praxis angewandt; Caner Türkdoğru ist den Weg vom Werkstudent-Antrag bis zum ersten Junior-Developer-Gehalt gegangen. Diese sieben schreiben. Ich bin der siebte, als Gründer.

Alles, was die Redaktion schreibt, fußt auf **offiziellen Quellen** (DAAD, KMK, Anabin, BERUFENET, Auswärtiges Amt) und wird gegen einen **Community-Pool** geprüft — über 142.000 Nachrichten aus Telegram und Forum, die wir analysiert haben. Wenn eine Redakteurin ein Thema übernimmt, sieht sie, welche Fragen dieses Thema in der Community tatsächlich aufwirft, und versucht, sie alle zu beantworten.

## Wo wir in zwölf Monaten sein werden

Drei Dinge im kommenden Jahr. **Application Tracker** — die acht Schritte der Bewerbung, von der Zulassungsprüfung bis zum Visumstermin, in einem Dashboard nachverfolgbar; für jeden Schritt siehst du, was zu tun ist, welches Dokument du jetzt vorbereiten musst, bis wann du fertig sein solltest. **Mentor-Netzwerk** — Eins-zu-eins-Gespräche mit türkischen Fachkräften, die in Deutschland Karriere gemacht haben; manche kostenlos, manche stundenweise bezahlt. **Forum** öffnet 2027 — anonyme Q&A, Experten-Moderation, Kategorienarchiv. Das sind keine Luxus-Features; das sind Versicherungen gegen falsche Antworten in kritischen Momenten des Bewerbungsprozesses.

## Was du tun kannst

Drei Dinge. **Eins**: vertraue den richtigen Quellen. DAAD, KMK Anabin, Hochschulkompass, BERUFENET, Auswärtiges Amt. Diese fünf Seiten geben dir autoritative, kostenlose, aktuelle Antworten auf jede Grundfrage. **Zwei**: behandle jeden AlmanyaUni-Text, den du liest, eher als Startpunkt denn als endgültige Referenz — deine eigene Situation trägt immer Nuancen; gleiche sie mit deinem eigenen Kontext ab. **Drei**: teile deine Erfahrung. Das Forum öffnet bald; jede ehrliche Anekdote, die du dort beiträgst, lenkt die Entscheidung einer anderen Person. Was wir schreiben, ist offiziell; was du schreibst, ist real. Wenn beide zusammenkommen, verirrt sich keine Studierende mehr im System.

## Schlussworte

Du musst auf dem Weg nach Deutschland nicht allein sein; genau dafür existiert diese Plattform. Jede geklärte FAQ, jedes fertige Tool, jede angereicherte Hochschulseite existiert, um die Unsicherheit hinter deiner Entscheidung zu reduzieren. Über dein Feedback freuen wir uns — siehst du eine großartige Quelle, teile sie; siehst du eine falsche Stelle, lass uns sie korrigieren.

Deutschland war für türkische Studierende noch nie so zugänglich. Gemeinsam machen wir es noch zugänglicher.

**— Halil Yaprakli**
*Gründer, AlmanyaUni*
*Mai 2026*
MD;
    }
};

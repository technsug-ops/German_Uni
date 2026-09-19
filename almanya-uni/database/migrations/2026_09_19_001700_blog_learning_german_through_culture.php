<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN) — KÜLTÜR KÜMESİ 4/4: kültürle Almanca öğrenmek (medya, podcast, Mediathek).
 *
 * Kullanıcının kaynak listesindeki iki Alman kültür yayıncısı (dw.com/de/kultur ve
 * deutschlandfunkkultur.de) bu yazının çekirdeği. Not: her iki site de fetch'e kapalı olduğu için
 * yayın akışına dair SPESİFİK program iddiaları bilinçli olarak sınırlı tutuldu ve "program listesi
 * değişir, siteden doğrula" hedge'i eklendi; yalnızca uzun süredir istikrarlı olan formatlar anıldı.
 * Süre ifadesi site standardına uygun: Almanca A0 → C1 her zaman "12-24 ay" (bkz. memory
 * german-c1-duration-12-24-months).
 * Küme: 1) kültür şoku 2) yazısız kurallar 3) arkadaş edinmek 4) kültürle Almanca.
 * Yazar: Halil Yaprakli. Kategori: german-life-culture.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7e52c93d-1f64-4a8b-95c0-3d47b8e6a15f';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'german-life-culture')->value('id')
            ?? DB::table('categories')->where('slug', 'dil')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanca kursları çoğu öğrenciyi B1'e kadar taşır, sonra garip bir şey olur: **seviye durur.** Dilbilgisi biliyorsundur, sınavı geçmişsindir, ama kantinde iki Almanın hızlı sohbetini hâlâ yakalayamazsın.

Sebep basit: kurs sana **dili öğretir**, ama akıcılık dilin **içinde yaşamaktan** gelir. Almanya'nın en büyük avantajı da tam burada: kamu yayıncılığı sayesinde ücretsiz, yüksek kaliteli ve seviyelendirilmiş devasa bir içerik havuzu var. Bu yazı o havuzu nasıl kullanacağını anlatıyor.

## Neden medya, kurstan hızlı ilerletiyor?

Kurs haftada birkaç saat verir. Medya günde bir saat verir ve **doğal hız, doğal aksan, gerçek kelime sıklığı** taşır. Üç şeyi aynı anda çözer:

1. **Dinleme hızı:** sınav kayıtları yavaştır; gerçek konuşma değildir.
2. **Kültürel referans:** haberdeki tartışmayı bilmiyorsan sohbete giremezsin. Dil biliyor olman yetmez.
3. **Tekrar maliyeti sıfır:** aynı bölümü üç kez dinlemek ücretsizdir.

## Seviyene göre nereden başlamalı

| Seviyen | Ne kullan | Nasıl |
|---|---|---|
| **A1–A2** | Deutsche Welle'nin ücretsiz Almanca öğrenme bölümü: başlangıç için video kurs dizisi ve kısa günlük formatlar | Altyazıyı **Almanca** aç, günde 10–15 dakika |
| **A2–B1** | DW'nin yavaş okunan haber formatı (*langsam gesprochene Nachrichten*) ve sözlüklü haber formatları | Önce altyazısız dinle, sonra metinle tekrar et |
| **B1–B2** | ARD/ZDF Mediathek dizileri ve belgeselleri, altyazılı | 20–40 dakikalık bölümler; bilmediğin 5 kelimeyi not al, fazlasını alma |
| **B2–C1** | Deutschlandfunk Kultur ve benzeri kamu kültür kanallarının söyleşi/deneme formatları | Yürürken dinle; anlamadığın %20'yi dert etme |
| **C1+** | Tartışma programları, podcast'lerin uzun bölümleri, edebiyat | Konuyu takip et, dili değil |

> 💡 **Program isimleri ve yayın akışları zamanla değişir.** Yukarıdaki kategoriler istikrarlı; güncel içerik listesini yayıncının kendi sitesinden kontrol et.

## Deutsche Welle: öğrenci için en verimli başlangıç

DW, Almanya'nın uluslararası yayıncısı ve dil öğrenenler için ayrı, **tamamen ücretsiz** bir bölümü var. Neden işe yarıyor:

- İçerik **seviyelere göre etiketli** (A1'den C'ye), böylece kendi seviyenin biraz üstünü seçebilirsin.
- Çoğu formatın **yazılı metni (Transkript)** var — dinleyip sonra okumak, en hızlı ilerleten çalışma biçimlerinden biri.
- Haber formatları kısa: 5–10 dakikalık bölümler günlük alışkanlık kurmayı kolaylaştırır.
- Kültür bölümü ayrıca sinema, müzik, edebiyat ve toplum içeriği verir — kelime dağarcığını "sınav Almancası"ndan çıkarır.

## Deutschlandfunk Kultur: B2 sonrası sıçrama

Deutschlandfunk Kultur bir kamu **kültür radyosu**: söyleşiler, denemeler, kültür haberleri, uzun belgesel formatlar. Dili yavaşlatılmamıştır — bu yüzden B2 öncesinde sinir bozucu, B2 sonrasında son derece verimlidir.

Nasıl kullanılır:

- Podcast/arşiv üzerinden **konuyu sen seç**: ilgini çeken bir konu, anlamadığın kelimelere tahammülünü artırır.
- Günlük 20–30 dakika, **yürürken veya mutfakta**. Bu "ekstra zaman" değildir; zaten var olan zamanın doldurulmasıdır.
- Hedef **her kelimeyi anlamak değil**, argümanı takip etmek. C1'in tanımı budur.

## ARD/ZDF Mediathek ve dizi stratejisi

Alman kamu televizyonlarının çevrimiçi arşivleri ücretsizdir ve büyük bölümünde **Almanca altyazı** vardır. Öğrenci için en iyi kombinasyon: **Almanca ses + Almanca altyazı.** Türkçe altyazı, beynin kolay yolu seçmesine izin verir ve kazanç neredeyse sıfıra iner.

Kültürel bonus: pazar akşamları yayınlanan polisiye *Tatort*, Almanya'da bir televizyon programından fazlasıdır — ertesi gün konuşulur. Bir sohbete girmenin en kestirme yollarından biri onu izlemiş olmaktır.

## Haftalık gerçekçi plan

| Ne zaman | Ne | Süre |
|---|---|---|
| Sabah (yolda) | Yavaş okunan haber veya seviyene uygun kısa format | 10 dk |
| Öğle | Metinli bir haber formatını okuyup dinle | 15 dk |
| Akşam (2–3 gün) | Mediathek'ten dizi/belgesel, Almanca altyazı | 30 dk |
| Haftada 1 | Tandem veya Stammtisch — üretim tarafı | 60 dk |

Dinleme tek başına konuşmayı açmaz; **üretim** gerekir. Tandem ve dernek ortamının nasıl kurulacağını kümenin üçüncü yazısında anlattık: [Almanya'da arkadaş edinmek](/tr/blog/making-friends-in-germany-as-an-international-student).

## Ücretsiz iki kaynak daha

- **Şehir kütüphanesi (Stadtbibliothek):** öğrenci üyeliği çok ucuz veya ücretsizdir; sınav kitapları, sesli kitaplar ve dijital ödünç alma sistemleri (Onleihe gibi) bulunur.
- **Volkshochschule (VHS):** belediyelerin yetişkin eğitim merkezleri. Almanca kursları özel dil okullarına göre belirgin biçimde ucuzdur ve çoğu şehirde akşam grupları vardır.

## Sınav tarafı

Medya akıcılık verir, sertifika vermez. Üniversite ve oturum işlemleri için sınav gerekir; hangisinin senin için doğru olduğunu [TestDaF, DSH, telc karşılaştırmamızda](/tr/blog/testdaf-dsh-telc-which-exam-is-more-advantageous-for-germany-in) anlattık. Pratik denge: **günlük medya + sınavdan 2–3 ay önce format çalışması.**

## Sıkça Sorulanlar

### Sıfırdan C1'e ne kadar sürer?
Gerçekçi aralık **12–24 ay**: düzenli kurs, günlük temas ve üretim (konuşma/yazma) bir aradaysa alt sınıra, haftada birkaç saatle idare ediliyorsa üst sınıra yakın olur. "3 ayda C1" vaatlerine güvenme.

### Altyazıyı hangi dilde açmalıyım?
Almanca. Türkçe altyazı izlemeyi kolaylaştırır ama öğrenmeyi neredeyse durdurur. Almanca altyazı, duyduğunla yazılanı eşleştirir — asıl kazanç buradadır.

### Hiçbir şey anlamıyorum, seviyem düşük diye mi?
Genelde hayır — içerik seçimi yanlıştır. Seviyenin **biraz üstünü** seç: %60–70'ini anladığın içerik en hızlı ilerletir. Hiçbir şey anlamadığın içerik motivasyonu yakar.

### Günde kaç dakika yeterli?
Günde 20–30 dakika, haftada bir 3 saatten daha etkilidir. Belirleyici olan süre değil, **süreklilik**.

### Netflix/YouTube da olur mu?
Olur, ama iki şarta dikkat et: Almanca dublaj yerine **Almanca orijinal** yapımları seç (dublaj çeviri dili taşır) ve altyazıyı Almanca tut. Kamu yayıncılarının arşivleri bu açıdan daha güvenli.

### Haber mi izlemeliyim, dizi mi?
İkisi farklı kas: haber kelime ve güncel referans verir, dizi doğal konuşma ve günlük ifade verir. Sınava hazırlanıyorsan haber ağırlığı, günlük hayatta rahatlamak istiyorsan dizi ağırlığı.

## Sonuç ve dürüst tavsiye

Almanya'da dil öğrenmenin en büyük avantajı ücretsiz içeriğin bolluğu; en büyük tuzağı ise bu bolluğun içinde **hiçbir şeyi düzenli yapmamak**. İki kaynak seç — biri seviyene uygun kısa format, biri ilgini çeken uzun format — ve sekiz hafta boyunca aynı saatte kullan.

Sekiz haftanın sonunda fark ettiğin ilk şey şu olacak: kantindeki o hızlı sohbet, artık duvar değil, sadece hızlı bir sohbet.

Kümenin diğer yazıları: [kültür şoku ve ilk altı ay](/tr/blog/culture-shock-in-germany-first-six-months-for-students) · [yazısız kurallar](/tr/blog/unwritten-rules-of-daily-life-in-germany) · [arkadaş edinmek](/tr/blog/making-friends-in-germany-as-an-international-student)

*Yayıncıların program listeleri ve erişim koşulları değişebilir; bu yazıdaki bilgiler 2026 Eylül itibarıyla geçerlidir. Güncel içerik için ilgili yayıncının kendi sitesini kontrol et.*
MD;

        $deBody = <<<'MD'
Sprachkurse bringen die meisten Studierenden bis B1, dann passiert etwas Merkwürdiges: **das Niveau bleibt stehen.** Die Grammatik sitzt, die Prüfung ist bestanden — und trotzdem entgeht dir in der Mensa das schnelle Gespräch am Nebentisch.

Der Grund ist einfach: Ein Kurs vermittelt **die Sprache**, Flüssigkeit entsteht aber **im Sprachbad**. Genau hier liegt Deutschlands großer Vorteil: Dank des öffentlich-rechtlichen Angebots existiert ein riesiger, kostenloser und nach Niveau sortierter Fundus. Dieser Artikel zeigt, wie man ihn nutzt.

## Warum Medien schneller wirken als ein Kurs allein

Ein Kurs liefert ein paar Stunden pro Woche. Medien liefern eine Stunde pro Tag — in **natürlichem Tempo, mit echten Akzenten und realer Wortfrequenz**. Damit lösen sie drei Probleme gleichzeitig:

1. **Hörtempo:** Prüfungsaufnahmen sind langsam und entsprechen keiner echten Unterhaltung.
2. **Kulturelle Referenz:** Wer die Debatte der Woche nicht kennt, kann nicht mitreden — Sprachkenntnis allein genügt nicht.
3. **Wiederholung kostet nichts:** dieselbe Folge dreimal zu hören ist gratis.

## Wo du je nach Niveau einsteigst

| Niveau | Was | Wie |
|---|---|---|
| **A1–A2** | Der kostenlose Deutschlernbereich der Deutschen Welle: Videokurs-Reihe für den Einstieg und kurze Tagesformate | Untertitel auf **Deutsch**, 10–15 Minuten täglich |
| **A2–B1** | Die langsam gesprochenen Nachrichten der DW und Nachrichtenformate mit Vokabelteil | Erst ohne Text hören, dann mit Transkript wiederholen |
| **B1–B2** | Serien und Dokumentationen in der ARD/ZDF-Mediathek, mit Untertiteln | Folgen von 20–40 Minuten; höchstens fünf neue Wörter notieren |
| **B2–C1** | Gespräch- und Essayformate von Deutschlandfunk Kultur und ähnlichen Kulturwellen | Beim Gehen hören; die nicht verstandenen 20 % ignorieren |
| **C1+** | Diskussionssendungen, lange Podcastfolgen, Literatur | Dem Thema folgen, nicht der Sprache |

> 💡 **Sendungsnamen und Programmschemata ändern sich.** Die Kategorien oben sind stabil; das aktuelle Angebot prüfst du auf der Seite des jeweiligen Senders.

## Deutsche Welle: der effizienteste Einstieg

Die DW ist der Auslandsrundfunk Deutschlands und unterhält einen eigenen, **vollständig kostenlosen** Bereich für Deutschlernende. Warum das funktioniert:

- Die Inhalte sind **nach Niveaustufen markiert** (A1 bis C), du kannst also gezielt knapp über deinem Stand wählen.
- Zu vielen Formaten gibt es ein **Transkript** — hören und anschließend mitlesen gehört zu den wirksamsten Lernformen.
- Die Nachrichtenformate sind kurz: 5–10 Minuten lassen sich zur täglichen Gewohnheit machen.
- Der Kulturbereich liefert zusätzlich Film, Musik, Literatur und Gesellschaft — das holt den Wortschatz aus dem Prüfungsdeutsch heraus.

## Deutschlandfunk Kultur: der Sprung nach B2

Deutschlandfunk Kultur ist eine öffentlich-rechtliche **Kulturwelle**: Gespräche, Essays, Kulturnachrichten, lange Featureformate. Die Sprache ist nicht verlangsamt — vor B2 frustrierend, nach B2 außerordentlich ergiebig.

So nutzt du sie:

- Über Podcast und Archiv **wählst du das Thema selbst**; Interesse erhöht die Toleranz für unbekannte Wörter deutlich.
- Täglich 20–30 Minuten, **beim Gehen oder Kochen**. Das ist keine Extrazeit, sondern gefüllte Zeit.
- Ziel ist nicht, jedes Wort zu verstehen, sondern der Argumentation zu folgen. Genau das ist C1.

## ARD/ZDF-Mediathek und die Serienstrategie

Die Online-Archive der öffentlich-rechtlichen Sender sind kostenlos, und ein großer Teil verfügt über **deutsche Untertitel**. Die beste Kombination zum Lernen: **deutscher Ton plus deutsche Untertitel.** Untertitel in der Muttersprache lassen das Gehirn den bequemen Weg nehmen, und der Lerneffekt geht gegen null.

Kultureller Bonus: Der sonntägliche *Tatort* ist mehr als eine Fernsehsendung — er wird am Montag besprochen. Ihn gesehen zu haben, ist einer der kürzesten Wege in ein Gespräch.

## Ein realistischer Wochenplan

| Wann | Was | Dauer |
|---|---|---|
| Morgens (unterwegs) | Langsam gesprochene Nachrichten oder ein kurzes Format auf deinem Niveau | 10 Min. |
| Mittags | Ein Nachrichtenformat mit Transkript lesen und hören | 15 Min. |
| Abends (2–3 Tage) | Serie oder Doku aus der Mediathek, deutsche Untertitel | 30 Min. |
| 1× pro Woche | Tandem oder Stammtisch — die Produktionsseite | 60 Min. |

Hören allein öffnet das Sprechen nicht; es braucht **Produktion**. Wie du Tandem und Vereinsumfeld aufbaust, steht im dritten Teil der Reihe: [Freunde finden in Deutschland](/de/blog/making-friends-in-germany-as-an-international-student-de).

## Zwei weitere kostenlose Quellen

- **Stadtbibliothek:** Studierendenausweise sind sehr günstig oder kostenlos; es gibt Prüfungsliteratur, Hörbücher und digitale Ausleihe (etwa Onleihe).
- **Volkshochschule (VHS):** die kommunalen Weiterbildungszentren. Deutschkurse sind dort deutlich günstiger als in privaten Sprachschulen, meist mit Abendgruppen.

## Die Prüfungsseite

Medien bringen Flüssigkeit, aber kein Zertifikat. Für Hochschule und Aufenthalt brauchst du eine Prüfung; welche zu dir passt, steht in unserem [Vergleich von TestDaF, DSH und telc](/de/blog/testdaf-dsh-telc-which-exam-is-more-advantageous-for-germany-in-de). Die praktische Balance: **täglicher Medienkontakt plus zwei bis drei Monate Formattraining vor dem Prüfungstermin.**

## Häufige Fragen

### Wie lange dauert es von null bis C1?
Realistisch **12–24 Monate**: mit regelmäßigem Kurs, täglichem Kontakt und aktiver Produktion eher am unteren Ende, mit ein paar Stunden pro Woche eher am oberen. Versprechen wie „C1 in drei Monaten" sind unseriös.

### In welcher Sprache sollten die Untertitel laufen?
Auf Deutsch. Untertitel in der Muttersprache erleichtern das Schauen und stoppen das Lernen fast vollständig. Deutsche Untertitel verknüpfen Gehörtes mit Geschriebenem — darin liegt der Gewinn.

### Ich verstehe gar nichts — ist mein Niveau zu niedrig?
Meist nicht; meist ist die Auswahl falsch. Wähle knapp **über** deinem Niveau: Inhalte, von denen du 60–70 % verstehst, bringen am meisten. Was du gar nicht verstehst, verbrennt Motivation.

### Wie viele Minuten pro Tag reichen?
20–30 Minuten täglich wirken stärker als drei Stunden einmal pro Woche. Entscheidend ist nicht die Dauer, sondern die **Kontinuität**.

### Gehen Netflix und YouTube auch?
Ja, mit zwei Bedingungen: **deutsche Originalproduktionen** statt Synchronfassungen wählen (Synchronsprache bleibt Übersetzungssprache) und Untertitel auf Deutsch lassen. Die Archive der Öffentlich-Rechtlichen sind in dieser Hinsicht sicherer.

### Lieber Nachrichten oder Serien?
Zwei verschiedene Muskeln: Nachrichten liefern Wortschatz und aktuelle Bezüge, Serien natürliche Rede und Alltagswendungen. Vor einer Prüfung mehr Nachrichten, für den Alltag mehr Serien.

## Fazit und ehrlicher Rat

Der größte Vorteil beim Deutschlernen in Deutschland ist die Fülle kostenloser Inhalte; die größte Falle ist, in dieser Fülle **nichts regelmäßig** zu tun. Wähle zwei Quellen — ein kurzes Format auf deinem Niveau und ein langes zu einem Thema, das dich interessiert — und nutze sie acht Wochen lang zur selben Zeit.

Nach acht Wochen fällt dir zuerst dies auf: Das schnelle Gespräch in der Mensa ist keine Wand mehr, sondern einfach ein schnelles Gespräch.

Die weiteren Teile der Reihe: [Kulturschock und die ersten sechs Monate](/de/blog/culture-shock-in-germany-first-six-months-for-students-de) · [ungeschriebene Regeln](/de/blog/unwritten-rules-of-daily-life-in-germany-de) · [Freunde finden](/de/blog/making-friends-in-germany-as-an-international-student-de)

*Programmangebote und Zugangsbedingungen der Sender können sich ändern; die Angaben gelten mit Stand September 2026. Aktuelles findest du auf den Seiten der jeweiligen Sender.*
MD;

        $enBody = <<<'MD'
Language courses carry most students to B1, and then something odd happens: **the level stops moving.** You know the grammar, you passed the exam — and you still cannot follow two Germans talking quickly in the canteen.

The reason is simple: a course teaches you **the language**, while fluency comes from **living inside it**. That is exactly where Germany has an unusual advantage: thanks to public-service broadcasting, there is an enormous, free, level-graded pool of material. This article is about using it.

## Why media moves you faster than a course alone

A course gives you a few hours a week. Media gives you an hour a day, at **natural speed, with real accents and real word frequency**. It solves three problems at once:

1. **Listening speed:** exam recordings are slow and sound nothing like conversation.
2. **Cultural reference:** if you do not know the debate of the week, you cannot join the conversation. Knowing the language is not enough.
3. **Repetition is free:** listening to the same episode three times costs nothing.

## Where to start, by level

| Your level | What to use | How |
|---|---|---|
| **A1–A2** | Deutsche Welle's free German-learning section: the beginner video-course series and short daily formats | Subtitles in **German**, 10–15 minutes a day |
| **A2–B1** | DW's slowly spoken news (*langsam gesprochene Nachrichten*) and news formats with vocabulary | Listen without the text first, then repeat with the transcript |
| **B1–B2** | Series and documentaries in the ARD/ZDF Mediathek, with subtitles | Episodes of 20–40 minutes; note at most five new words |
| **B2–C1** | Interview and essay formats from Deutschlandfunk Kultur and similar culture channels | Listen while walking; ignore the 20% you miss |
| **C1+** | Discussion programmes, long podcast episodes, literature | Follow the argument, not the language |

> 💡 **Programme names and schedules change over time.** The categories above are stable; check the current line-up on the broadcaster's own site.

## Deutsche Welle: the most efficient starting point

DW is Germany's international broadcaster and runs a separate, **entirely free** section for German learners. Why it works:

- Content is **tagged by level** (A1 through C), so you can pick something just above where you are.
- Many formats come with a **transcript** — listening and then reading along is among the most effective study methods there is.
- The news formats are short: 5–10 minutes makes a daily habit realistic.
- The culture section adds film, music, literature and society — which pulls your vocabulary out of "exam German".

## Deutschlandfunk Kultur: the jump after B2

Deutschlandfunk Kultur is a public-service **culture station**: interviews, essays, cultural news and long feature formats. The language is not slowed down, which makes it frustrating before B2 and extremely productive after it.

How to use it:

- Through its podcasts and archive **you choose the topic**, and genuine interest raises your tolerance for unknown words considerably.
- Twenty to thirty minutes a day, **while walking or cooking**. This is not extra time; it is time you already spend.
- The goal is not to understand every word but to follow the argument. That is what C1 actually means.

## The ARD/ZDF Mediathek and a strategy for series

The online archives of Germany's public broadcasters are free, and much of the catalogue carries **German subtitles**. The best combination for learning is **German audio plus German subtitles.** Subtitles in your own language let your brain take the easy route, and the benefit drops to almost nothing.

A cultural bonus: the Sunday-evening crime series *Tatort* is more than a TV programme — it gets discussed on Monday. Having watched it is one of the shortest routes into a conversation.

## A realistic weekly plan

| When | What | Duration |
|---|---|---|
| Morning (commuting) | Slowly spoken news or a short format at your level | 10 min |
| Midday | Read and listen to one news format with its transcript | 15 min |
| Evening (2–3 days) | A series or documentary from the Mediathek, German subtitles | 30 min |
| Once a week | Tandem or Stammtisch — the production side | 60 min |

Listening alone does not unlock speaking; that requires **production**. How to build a tandem and a club environment is covered in part three: [making friends in Germany](/en/blog/making-friends-in-germany-as-an-international-student-en).

## Two more free resources

- **The city library (Stadtbibliothek):** student membership is very cheap or free, with exam books, audiobooks and digital lending systems such as Onleihe.
- **Volkshochschule (VHS):** municipal adult education centres. German courses there are markedly cheaper than at private language schools, usually with evening groups.

## The exam side

Media builds fluency, not certificates. University admission and residence procedures need an exam; which one suits you is covered in our [comparison of TestDaF, DSH and telc](/en/blog/testdaf-dsh-telc-which-exam-is-more-advantageous-for-germany-in-en). The practical balance: **daily media contact, plus two to three months of format-specific practice before the exam.**

## Frequently asked questions

### How long does it take to get from zero to C1?
Realistically **12–24 months**: closer to the lower end with a regular course, daily exposure and active production, closer to the upper end on a few hours a week. Treat "C1 in three months" claims as marketing.

### Which language should the subtitles be in?
German. Subtitles in your own language make watching easier and stop the learning almost completely. German subtitles link what you hear to what is written — that is where the gain is.

### I understand nothing. Is my level too low?
Usually not; usually the choice of material is wrong. Pick something just **above** your level: content you understand 60–70% of moves you fastest. Content you understand nothing of burns motivation.

### How many minutes a day is enough?
Twenty to thirty minutes daily beats three hours once a week. What matters is not duration but **continuity**.

### Do Netflix and YouTube count?
Yes, with two conditions: choose **German original productions** rather than dubbed versions (dubbing carries translated language), and keep subtitles in German. The public broadcasters' archives are safer on both counts.

### News or series?
They train different muscles: news gives you vocabulary and current references, series give you natural speech and everyday phrasing. Weight towards news before an exam, towards series for daily life.

## Conclusion and honest advice

The great advantage of learning German in Germany is the abundance of free material; the great trap is doing **nothing regularly** inside that abundance. Pick two sources — one short format at your level, one longer one on a subject you care about — and use them at the same time of day for eight weeks.

After eight weeks the first thing you notice is this: that fast conversation in the canteen is no longer a wall, just a fast conversation.

The rest of the series: [culture shock and your first six months](/en/blog/culture-shock-in-germany-first-six-months-for-students-en) · [the unwritten rules](/en/blog/unwritten-rules-of-daily-life-in-germany-en) · [making friends](/en/blog/making-friends-in-germany-as-an-international-student-en)

*Broadcasters' programme offerings and access conditions can change; the information here is current as of September 2026. Check the relevant broadcaster's own site for what is available now.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'learning-german-through-culture-german-media-and-podcasts',
                'title' => 'Kültürle Almanca Öğrenmek: Medya, Podcast ve Mediathek Rehberi',
                'excerpt' => 'Kurs seni B1\'e taşır, akıcılığı medya verir. Seviyene göre nereden başlayacağın (DW, Deutschlandfunk Kultur, ARD/ZDF Mediathek), altyazı stratejisi, haftalık gerçekçi plan ve sıfırdan C1\'e dürüst süre beklentisi.',
                'meta_title' => 'Kültürle Almanca Öğrenmek: DW, Podcast ve Mediathek Planı',
                'meta_description' => 'Almanca akıcılık için ücretsiz medya planı: seviyeye göre DW, Deutschlandfunk Kultur ve Mediathek, altyazı stratejisi, haftalık program (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug' => 'learning-german-through-culture-german-media-and-podcasts-de',
                'title' => 'Deutsch über Kultur lernen: Medien, Podcasts und Mediathek',
                'excerpt' => 'Ein Kurs bringt dich bis B1, die Flüssigkeit kommt aus den Medien. Wo du je nach Niveau einsteigst (DW, Deutschlandfunk Kultur, ARD/ZDF-Mediathek), die Untertitelstrategie, ein realistischer Wochenplan und eine ehrliche Zeitangabe bis C1.',
                'meta_title' => 'Deutsch über Kultur lernen: DW, Podcasts und Mediathek',
                'meta_description' => 'Kostenloser Medienplan für flüssiges Deutsch: DW, Deutschlandfunk Kultur und Mediathek nach Niveau, Untertitelstrategie, Wochenplan (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug' => 'learning-german-through-culture-german-media-and-podcasts-en',
                'title' => 'Learning German Through Culture: Media, Podcasts and the Mediathek',
                'excerpt' => 'A course takes you to B1; fluency comes from media. Where to start at each level (DW, Deutschlandfunk Kultur, the ARD/ZDF Mediathek), the subtitle strategy, a realistic weekly plan and an honest timeline from zero to C1.',
                'meta_title' => 'Learn German Through Culture: DW, Podcasts and the Mediathek',
                'meta_description' => 'A free media plan for fluent German: DW, Deutschlandfunk Kultur and the Mediathek by level, subtitle strategy and a weekly routine (2026).',
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
            'learning-german-through-culture-german-media-and-podcasts',
            'learning-german-through-culture-german-media-and-podcasts-de',
            'learning-german-through-culture-german-media-and-podcasts-en',
        ])->delete();
    }
};

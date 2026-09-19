<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Ausländerbehörde oturum uzatmasını geciktirirse — Fiktionsbescheinigung + Untätigkeitsklage.
 *
 * Grounding (resmî kaynaklar, Eylül 2026):
 *   - § 81 Abs. 4 AufenthG: süresi dolmadan uzatma başvurusu yapılırsa mevcut oturum karar verilene
 *     kadar geçerli sayılır (Fortgeltungsfiktion); ABH bunu belgeleyen Fiktionsbescheinigung verir.
 *     gesetze-im-internet.de/aufenthg_2004/__81.html
 *   - § 81 Abs. 4 belgesiyle yurt dışı seyahati + yeniden giriş ve çalışma MÜMKÜN; § 81 Abs. 3
 *     varyantıyla DEĞİL. Harç 13 € (reşit olmayan 6,50 €); Türk vatandaşları için ücretsiz.
 *     service.berlin.de/dienstleistung/326233/en/ (Berlin; muafiyet eyalet sayfasından doğrulanmalı)
 *   - Fiktionsbescheinigung tipik geçerlilik 3-6 ay, karar çıkmazsa uzatılır.
 *   - § 75 VwGO: yeterli sebep olmadan 3 aydan uzun karar verilmezse Untätigkeitsklage açılabilir.
 * Topluluk verisi (r/germany, reddit_kb): "How can the Ausländerbehörde come legally out of a
 * Untätigkeitsklage?" — davayı kazanmak mümkün ama yargı süreci de uzun; beklenti yönetimi şart.
 * Yazar: Halil Yaprakli. Kategori: visa-residence. Slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '0efa6829-2358-4b25-bffb-95ba9d012583';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'visa-residence')->value('id')
            ?? DB::table('categories')->where('slug', 'vize')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Oturum kartının süresi altı hafta sonra doluyor, Ausländerbehörde'nin randevu sistemi boş, e-postalarına cevap gelmiyor ve aklından tek bir cümle geçiyor: *"Kartım biterse yasa dışı duruma mı düşeceğim?"*

Kısa cevap: **hayır — başvurunu kart süresi dolmadan yaptıysan.** Alman hukuku tam da bu durum için bir emniyet supabı içeriyor. Ama bu supap otomatik çalışmıyor; senin zamanında ve **kanıtlanabilir** şekilde bir şey yapmış olmanı şart koşuyor. Bu yazı o mekanizmayı, elindeki belgenin ne işe yaradığını ve beklemenin gerçekten mantıksızlaştığı noktada devreye giren dava yolunu anlatıyor.

## Her şeyi belirleyen tek kural: başvuruyu süre dolmadan yap

Oturum hukukunun bu konudaki en kritik maddesi **§ 81 Abs. 4 AufenthG**. Özü şu: mevcut oturum iznin **sona ermeden önce** uzatma (veya başka bir oturum türüne geçiş) başvurusu yaparsan, **eski iznin, makam karar verene kadar geçerli sayılmaya devam eder.** Almanca adı *Fortgeltungsfiktion*, yani "devam ediyormuş gibi sayılma".

Bunun pratik anlamı büyük: kartın üzerindeki tarih geçse bile Almanya'da yasal olarak bulunuyorsun; çalışma iznin devam ediyor; kira sözleşmen, sigortan ve üniversite kaydın etkilenmiyor.

Bunun tersi de aynı sertlikte geçerli: **süre dolduktan bir gün sonra** yapılan başvuru bu korumadan yararlanmaz. O noktada makamın takdirine kalırsın ve süreç çok daha zor yönetilir.

Bu yüzden randevu bulamamak bir mazeret değil — **başvuru randevuyla değil, dilekçeyle yapılır.** Yapman gereken:

- Uzatma talebini **yazılı** olarak ilet: ABH'nin online formu, e-posta veya dilekçe.
- Postayla gönderiyorsan **Einschreiben (taahhütlü)** kullan; en azından *Einwurf-Einschreiben*.
- E-posta gönderiyorsan **okundu bilgisi** iste ve gönderilen klasörünü PDF olarak sakla.
- Dilekçede net yaz: adın, doğum tarihin, dosya numaran (varsa), mevcut iznin bitiş tarihi ve "Aufenthaltstitel verlängern" talebin.

Elindeki tek şey bir e-posta çıktısı bile olsa, **tarihli bir kanıtın** olması hem Fiktionsbescheinigung talebinin hem de olası bir davanın temelidir.

## Fiktionsbescheinigung nedir, ne işe yarar?

Fiktionsbescheinigung, "başvurun işleme alındı ve statün devam ediyor" anlamına gelen resmî belgedir. Ama **iki farklı türü vardır ve aralarındaki fark günlük hayatını doğrudan etkiler:**

| | § 81 Abs. 4 (Fortgeltungsfiktion) | § 81 Abs. 3 |
|---|---|---|
| Kimde olur | Geçerli oturum izni veya D vizesi varken zamanında başvuranlar | Vizesiz ama yasal olarak Almanya'da bulunanlar |
| Çalışma | Eski iznindeki çalışma hakkı **devam eder** | **Yeni işe başlamaya izin vermez** |
| Yurt dışına çıkış + dönüş | **Mümkündür** | **Mümkün değildir** |

Almanya'da okuyan ya da çalışan Türk vatandaşlarının büyük çoğunluğu **§ 81 Abs. 4** grubundadır — yani seyahat ve çalışma hakkı korunur. Yine de belgeni aldığında **üzerinde hangi fıkranın yazdığını kontrol et.** Yanlış fıkra basıldıysa (oluyor) hemen düzeltilmesini iste.

**Harç:** yetişkinler için 13 €, reşit olmayanlar için 6,50 € düzeyindedir. Türk vatandaşları için birçok makam bu belgeyi **harçsız** düzenler — bu, Türkiye–AET Ortaklık Hukuku'ndan doğan harç sınırlamalarının bir yansımasıdır. Kendi şehrinin Ausländerbehörde sayfasında "Gebühren" başlığını kontrol et; ücret istenirse muafiyeti nazikçe sor.

**Geçerlilik:** genellikle 3–6 ay verilir. Karar bu sürede çıkmazsa **uzatılır** — bu normaldir, başvurunun reddedildiği anlamına gelmez.

## Seyahat: bir kâğıtla uçağa binmek

§ 81 Abs. 4 belgesiyle Türkiye'ye gidip dönebilirsin. Pratikte sorun hukukta değil, **check-in bankosunda** çıkar: havayolu görevlisi bu belgeyi tanımayabilir. Yanında bulundur:

- Geçerli pasaport (şart — Fiktionsbescheinigung pasaport yerine geçmez),
- Süresi dolmuş olsa da **eski oturum kartın**,
- Fiktionsbescheinigung'un aslı,
- Mümkünse uzatma başvurusunun ve ABH yazışmasının çıktısı.

Ek bir güvence: belgenin üzerinde "Erwerbstätigkeit gestattet" gibi şerhlerin gerçekten basılı olduğundan emin ol. Sınırda tartışma çıkma ihtimalini ciddi biçimde azaltır.

## İşveren ve üniversite tarafı

Werkstudent sözleşmen veya yarı zamanlı işin varsa İK departmanı bazen "kartın süresi dolmuş" diye paniğe kapılır. İK'ya vermen gereken cevap kısa: **§ 81 Abs. 4 uyarınca önceki iznin tüm hakları karar verilene kadar devam eder** ve elindeki belge bunu ispatlar. İşveren isterse belgenin fotokopisini personel dosyasına koyar; ek bir izin gerekmez.

Üniversite tarafında da benzer: **Rückmeldung** (dönem yenileme) ve kayıt işlemleri oturum kartı süresine değil, kendi takvimine bağlıdır. Yine de kaydınla ilgili bir sorun çıkarsa süreç hızla [Exmatrikulation](/tr/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back) tarafına kayabilir; o riski ayrı bir yazıda ayrıntılı anlattık.

## Üç ay doldu: Untätigkeitsklage

Bekleme süresi makul olmaktan çıktığında elinde gerçek bir hukuki araç var: **Untätigkeitsklage** (eylemsizlik davası), dayanağı **§ 75 VwGO**. Kural basit: makam, **yeterli bir sebep olmaksızın üç aydan uzun süre** başvurun hakkında karar vermezse idare mahkemesinde dava açabilirsin.

Gerçekçi beklenti şu — ve topluluk deneyimleri de tam olarak bunu söylüyor: *dava açmak "dosyan eksiksizse haklısın" tarafında güçlüdür, ama mahkemenin kendisi de hızlı değildir.* Bazı şehirlerde dava dilekçesinin ABH'ye tebliğ edilmesi tek başına dosyayı hareketlendirir; bazı yerlerde aylarca beklersin.

Dava açmadan önce sırayla denenmesi mantıklı adımlar:

1. **Yazılı hatırlatma (Sachstandsanfrage):** dosya numarasıyla, kibar ve tarihli. Bir kopyasını sakla.
2. **Somut aciliyet göster:** iş sözleşmesi, staj başlangıcı, yurt dışı seyahati, sınav takvimi gibi belgelerle. Makamlar önceliklendirme yapabiliyor.
3. **Dienstaufsichtsbeschwerde:** kurum amirliğine şikâyet. Hukuki sonucu sınırlıdır ama bazen işe yarar.
4. **Avukat üzerinden tek sayfalık ihtar:** çoğu dosya davadan ucuza burada kapanır.
5. **Untätigkeitsklage.**

**Maliyet ve risk:** dava masrafı ve avukat ücreti işin değerine göre hesaplanır; oturum davalarında genelde birkaç yüz ile bin küsur euro arasında bir bütçeden söz edilir. Kazanılırsa masrafların karşı tarafa yüklenmesi mümkündür. Rakamlar dosyaya göre değişir, avukatla önden netleştir.

**"ABH bana kızar mı, sonra intikam alır mı?"** — Karar hukuki kriterlere bağlıdır ve dava açmak yasal bir haktır. Pratikte asıl risk, dosyanın hâlâ eksik olmasıdır: dava açmadan önce **istenen tüm belgeleri eksiksiz verdiğinden emin ol.** Makamın "yeterli sebep" savunmasının en sık dayanağı budur.

## Randevu bulamıyorsan pratik taktikler

- ABH sayfasındaki online randevu takvimini **sabah erken saatlerde** kontrol et; iptaller genelde o saatlerde düşer.
- Şehirlerin çoğunda öğrenciler için ayrı bir kanal vardır — üniversitenin **International Office**'i bazen doğrudan randevu ayarlayabilir.
- Talebini yine de yazılı gönder: randevu beklerken bile **başvuru tarihi** senin lehine işler.
- Adresin değiştiyse önce [Anmeldung](/tr/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt) işlemini tamamla; eksik adres kaydı dosyayı bekletir.

## Sıkça Sorulanlar

### Kartımın süresi doldu ama hâlâ cevap yok. Yasa dışı mı sayılıyorum?
Hayır. Başvurunu süre dolmadan yaptıysan § 81 Abs. 4 kapsamında statün devam eder. Kanıtın, başvurunun tarihli belgesidir. Elinde henüz Fiktionsbescheinigung yoksa bile durum değişmez; belgeyi talep et.

### Fiktionsbescheinigung ile Türkiye'ye gidebilir miyim?
§ 81 Abs. 4 varyantıyla evet: yurt dışına çıkış ve yeniden giriş mümkündür. § 81 Abs. 3 varyantında hayır. Belgenin üzerindeki fıkrayı mutlaka kontrol et; pasaportunu ve eski kartını da yanına al.

### Üç ay doldu, davayı kazanır mıyım?
Başvurun eksiksizse ve makamın gecikme için haklı bir gerekçesi yoksa konum güçlüdür. Ancak mahkeme süreci de zaman alır; dava genelde "hemen sonuç" değil, "dosyayı hareket ettiren baskı" olarak işler.

### İşverenim süresi dolmuş kartı kabul etmiyor.
İK'ya § 81 Abs. 4'ü ve Fiktionsbescheinigung'u göster: eski izindeki çalışma hakkı karar verilene kadar devam eder. Gerekirse ABH'den tek cümlelik yazılı teyit istemek çoğu tartışmayı bitirir.

### Oturum türümü değiştirmek istiyorum, aynı kural geçerli mi?
Evet — süresi dolmadan yapılan **başka bir oturum iznine geçiş** başvurusu da aynı korumadan yararlanır. Öğrencilikten çalışmaya geçiş özelinde ayrıntılar için [Zweckwechsel rehberimize](/tr/blog/changing-student-visa-to-work-permit-germany-zweckwechsel) bak.

### Sağlık sigortam etkilenir mi?
Statün devam ettiği için sigorta ilişkin de devam eder. Yine de sigorta şirketi güncel belge ister; Fiktionsbescheinigung'u ilet. Öğrenci tarifeleri ve sağlayıcı karşılaştırması için [sigorta rehberimiz](/tr/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs) yardımcı olur.

## Sonuç ve dürüst tavsiye

Bu konudaki tüm stres tek bir güne bağlı: **kartının bitiş tarihinden önceki güne.** O günden önce yazılı ve kanıtlanabilir bir başvuru yaptıysan, sonrasında yaşadığın şey can sıkıcı bir bekleyiştir — hukuki bir tehlike değil. O günü kaçırdıysan ilk işin randevu aramak değil, bir danışma noktasına (üniversitenin International Office'i, göçmen danışmanlığı veya avukat) ulaşmak olmalı.

Sırayı basit tut: **zamanında yazılı başvuru → Fiktionsbescheinigung → nazik hatırlatma → aciliyet belgesi → (3 ay sonra) hukuki baskı.** Bu sıra atlanmadan işlediğinde çoğu dosya mahkemeye gitmeden kapanır.

*Bu yazıdaki yasal maddeler ve harç tutarları 2026 Eylül itibarıyla geçerlidir; Ausländerbehörde uygulamaları şehirden şehre değişir ve harçlar güncellenebilir. Kendi durumun için şehrinin resmî sayfasını ve gerekiyorsa bir avukatı doğrulama kaynağı olarak kullan.*
MD;

        $deBody = <<<'MD'
Deine Aufenthaltskarte läuft in sechs Wochen ab, das Terminportal der Ausländerbehörde zeigt keine freien Termine, auf deine E-Mails kommt keine Antwort — und ein Gedanke lässt dich nicht los: *„Bin ich illegal, wenn die Karte abläuft?"*

Die kurze Antwort: **nein — vorausgesetzt, du hast deinen Antrag vor Ablauf gestellt.** Das deutsche Aufenthaltsrecht hat für genau diese Situation ein Sicherheitsventil. Es greift aber nicht automatisch: Es setzt voraus, dass du rechtzeitig und **nachweisbar** tätig geworden bist. Dieser Artikel erklärt den Mechanismus, wozu die Bescheinigung in deiner Hand taugt und ab wann der Klageweg realistisch wird.

## Die eine Regel, die alles entscheidet: Antrag vor Ablauf

Die wichtigste Norm ist **§ 81 Abs. 4 AufenthG**. Der Kern: Wenn du die Verlängerung — oder den Wechsel in einen anderen Aufenthaltstitel — **vor Ablauf** deines aktuellen Titels beantragst, **gilt der bisherige Titel bis zur Entscheidung der Behörde als fortbestehend.** Juristisch heißt das *Fortgeltungsfiktion*.

Praktisch bedeutet das: Auch wenn das Datum auf der Karte verstrichen ist, hältst du dich rechtmäßig in Deutschland auf, deine Erwerbserlaubnis läuft weiter, Mietvertrag, Versicherung und Immatrikulation bleiben unberührt.

Die Kehrseite ist genauso hart: Ein Antrag **einen Tag nach Ablauf** genießt diesen Schutz nicht mehr. Dann liegt vieles im Ermessen der Behörde, und das Verfahren wird deutlich unangenehmer.

Deshalb ist „ich bekomme keinen Termin" keine Entschuldigung — **der Antrag wird nicht durch einen Termin gestellt, sondern durch einen Schriftsatz.** Was du tun solltest:

- Stelle den Verlängerungsantrag **schriftlich**: Online-Formular der ABH, E-Mail oder Brief.
- Bei Postversand: **Einschreiben**, mindestens Einwurf-Einschreiben.
- Bei E-Mail: Lesebestätigung anfordern und den Sendeordner als PDF sichern.
- Schreibe klar hinein: Name, Geburtsdatum, Aktenzeichen (falls vorhanden), Ablaufdatum des aktuellen Titels und den Antrag auf Verlängerung.

Selbst ein einfacher E-Mail-Ausdruck genügt — entscheidend ist ein **datierter Nachweis**. Er ist die Grundlage sowohl für die Fiktionsbescheinigung als auch für eine spätere Klage.

## Was ist die Fiktionsbescheinigung?

Die Fiktionsbescheinigung ist das amtliche Dokument, das bestätigt: Dein Antrag liegt vor und dein Status besteht fort. Es gibt sie in **zwei Varianten, und der Unterschied betrifft deinen Alltag direkt:**

| | § 81 Abs. 4 (Fortgeltungsfiktion) | § 81 Abs. 3 |
|---|---|---|
| Wer bekommt sie | Wer mit gültigem Aufenthaltstitel oder D-Visum rechtzeitig beantragt hat | Wer sich ohne Visum rechtmäßig im Bundesgebiet aufhält |
| Erwerbstätigkeit | Das bisherige Arbeitsrecht **gilt weiter** | **Erlaubt keine Aufnahme einer Erwerbstätigkeit** |
| Auslandsreise + Wiedereinreise | **Möglich** | **Nicht möglich** |

Studierende und Beschäftigte fallen fast immer unter **§ 81 Abs. 4** — Reise- und Arbeitsrecht bleiben also erhalten. Prüfe trotzdem, **welcher Absatz auf deinem Dokument angekreuzt ist.** Falsche Eintragungen kommen vor; lass sie sofort korrigieren.

**Gebühr:** in der Regel 13 € für Erwachsene, 6,50 € für Minderjährige. Für türkische Staatsangehörige stellen viele Behörden die Bescheinigung **gebührenfrei** aus — eine Folge der Gebührenbeschränkungen aus dem Assoziationsrecht EWG–Türkei. Prüfe die Rubrik „Gebühren" auf der Seite deiner Ausländerbehörde.

**Gültigkeit:** meist 3–6 Monate. Ergeht in dieser Zeit keine Entscheidung, wird sie **verlängert** — das ist normal und bedeutet keine Ablehnung.

## Reisen: mit einem Blatt Papier ins Flugzeug

Mit der Variante nach § 81 Abs. 4 kannst du ausreisen und wieder einreisen. Das Problem liegt selten im Recht, sondern **am Check-in-Schalter**: Das Dokument ist vielen Airline-Mitarbeitenden unbekannt. Nimm mit:

- gültigen Reisepass (Pflicht — die Bescheinigung ersetzt keinen Pass),
- die **alte Aufenthaltskarte**, auch wenn abgelaufen,
- das Original der Fiktionsbescheinigung,
- möglichst den Ausdruck des Antrags und des Schriftverkehrs mit der ABH.

Zusätzliche Absicherung: Achte darauf, dass Vermerke wie „Erwerbstätigkeit gestattet" tatsächlich eingetragen sind. Das verhindert die meisten Diskussionen an der Grenze.

## Arbeitgeber und Hochschule

Wenn du als Werkstudent oder in Teilzeit arbeitest, gerät die Personalabteilung manchmal in Panik: „Die Karte ist abgelaufen." Deine Antwort ist kurz: **Nach § 81 Abs. 4 gelten alle Rechte des bisherigen Titels bis zur Entscheidung fort**, und die Bescheinigung belegt das. Eine Kopie für die Personalakte genügt; eine zusätzliche Erlaubnis ist nicht nötig.

An der Hochschule gilt Ähnliches: Rückmeldung und Immatrikulation hängen am Hochschulkalender, nicht an deiner Karte. Wenn es dort dennoch klemmt, kann die Sache schnell Richtung [Exmatrikulation](/de/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-de) kippen — dazu haben wir einen eigenen Artikel.

## Drei Monate vorbei: die Untätigkeitsklage

Wird das Warten unzumutbar, gibt es ein echtes Instrument: die **Untätigkeitsklage** nach **§ 75 VwGO**. Die Regel: Entscheidet die Behörde **ohne zureichenden Grund länger als drei Monate** nicht über deinen Antrag, kannst du beim Verwaltungsgericht klagen.

Realistische Erwartung — und die Erfahrungsberichte aus der Community sagen genau das: *Die Erfolgsaussichten sind gut, wenn dein Antrag vollständig ist, aber das Gerichtsverfahren selbst braucht ebenfalls Zeit.* In manchen Städten bringt schon die Zustellung der Klage Bewegung in die Akte, in anderen wartest du weitere Monate.

Sinnvolle Reihenfolge vor der Klage:

1. **Schriftliche Sachstandsanfrage** mit Aktenzeichen, sachlich und datiert. Kopie aufbewahren.
2. **Dringlichkeit belegen**: Arbeitsvertrag, Praktikumsbeginn, Auslandsreise, Prüfungstermine. Behörden priorisieren durchaus.
3. **Dienstaufsichtsbeschwerde**: rechtlich begrenzt wirksam, manchmal aber hilfreich.
4. **Anwaltliches Aufforderungsschreiben**: oft die Stufe, auf der sich die Sache günstiger erledigt als vor Gericht.
5. **Untätigkeitsklage.**

**Kosten und Risiko:** Gerichts- und Anwaltskosten richten sich nach dem Streitwert; in Aufenthaltssachen bewegt man sich häufig im Bereich einiger hundert bis über tausend Euro. Bei Erfolg können die Kosten der Behörde auferlegt werden. Kläre die Zahlen vorab anwaltlich.

**„Nimmt mir die Behörde das übel?"** — Die Entscheidung folgt rechtlichen Kriterien; eine Klage ist ein legitimes Recht. Das eigentliche Risiko ist eine **unvollständige Akte**: Genau darauf stützt sich die Verteidigung „zureichender Grund". Reiche alles Geforderte vorher ein.

## Praktische Taktiken, wenn es keinen Termin gibt

- Terminportal **früh morgens** prüfen; Stornierungen erscheinen meist dann.
- Viele Städte haben einen eigenen Kanal für Studierende — das **International Office** der Hochschule kann manchmal direkt vermitteln.
- Antrag trotzdem schriftlich stellen: Während du auf einen Termin wartest, arbeitet das **Antragsdatum** für dich.
- Bei Umzug zuerst die [Anmeldung](/de/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt-de) erledigen; eine fehlende Meldeadresse blockiert die Akte.

## Häufige Fragen

### Meine Karte ist abgelaufen, es kommt keine Antwort. Bin ich illegal hier?
Nein. Wenn der Antrag vor Ablauf gestellt wurde, besteht dein Status nach § 81 Abs. 4 fort. Nachweis ist der datierte Antrag. Auch ohne Fiktionsbescheinigung ändert sich daran nichts — fordere sie aber an.

### Kann ich mit der Fiktionsbescheinigung ins Ausland reisen?
Mit der Variante § 81 Abs. 4 ja: Ausreise und Wiedereinreise sind möglich. Mit § 81 Abs. 3 nicht. Prüfe den Absatz auf dem Dokument und nimm Pass und alte Karte mit.

### Drei Monate sind um — gewinne ich die Klage?
Wenn dein Antrag vollständig ist und die Behörde keinen zureichenden Grund hat, stehst du gut da. Das Verfahren dauert dennoch; die Klage wirkt meist als Druckmittel, nicht als Sofortlösung.

### Mein Arbeitgeber akzeptiert die abgelaufene Karte nicht.
Zeige der Personalabteilung § 81 Abs. 4 und die Bescheinigung: Das Arbeitsrecht des bisherigen Titels gilt bis zur Entscheidung fort. Eine kurze schriftliche Bestätigung der ABH beendet die meisten Diskussionen.

### Ich will den Aufenthaltszweck wechseln — gilt dieselbe Regel?
Ja, auch der rechtzeitige Antrag auf einen **anderen Aufenthaltstitel** genießt diesen Schutz. Details zum Wechsel vom Studium in die Beschäftigung findest du in unserem [Zweckwechsel-Artikel](/de/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-de).

### Was passiert mit meiner Krankenversicherung?
Da dein Status fortbesteht, bleibt auch das Versicherungsverhältnis bestehen. Reiche der Kasse die Bescheinigung ein. Einen Anbietervergleich findest du in unserem [Versicherungsratgeber](/de/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs-de).

## Fazit und ehrlicher Rat

Der gesamte Stress hängt an einem einzigen Tag: **dem Tag vor Ablauf deiner Karte.** Wer davor schriftlich und nachweisbar beantragt hat, erlebt danach eine lästige Warterei — keine rechtliche Gefahr. Wer diesen Tag verpasst hat, sollte nicht zuerst nach einem Termin suchen, sondern eine Beratungsstelle einschalten: International Office, Migrationsberatung oder Anwältin bzw. Anwalt.

Halte die Reihenfolge einfach: **rechtzeitiger schriftlicher Antrag → Fiktionsbescheinigung → sachliche Erinnerung → Dringlichkeitsnachweis → (nach drei Monaten) rechtlicher Druck.** Wer sie einhält, erledigt die meisten Fälle ohne Gericht.

*Die genannten Normen und Gebühren gelten mit Stand September 2026; die Praxis unterscheidet sich von Stadt zu Stadt und Gebühren können sich ändern. Prüfe die offizielle Seite deiner Ausländerbehörde und ziehe im Zweifel anwaltlichen Rat hinzu.*
MD;

        $enBody = <<<'MD'
Your residence card expires in six weeks, the Ausländerbehörde's booking system shows no appointments, your e-mails go unanswered, and one thought keeps circling: *"Will I be illegal once the card runs out?"*

The short answer: **no — provided you filed your application before the card expired.** German residence law has a safety valve built for exactly this situation. But it does not trigger automatically: it requires you to have acted in time and in a way you can **prove**. This article explains the mechanism, what the certificate in your hand actually does, and the point at which going to court becomes reasonable.

## The single rule that decides everything: apply before expiry

The key provision is **Section 81(4) of the Residence Act (AufenthG)**. In essence: if you apply for an extension — or for a switch to a different residence title — **before your current permit expires**, the old permit **is deemed to continue until the authority decides.** German law calls this *Fortgeltungsfiktion*, a "fiction of continued validity".

In practice that means: even after the date on the card has passed, your stay is lawful, your work authorisation continues, and your lease, insurance and university enrolment are unaffected.

The flip side is equally strict: an application filed **one day after expiry** does not enjoy this protection. From there you depend on the authority's discretion, and the process becomes far harder to manage.

So "I couldn't get an appointment" is not an excuse — **an application is made by a written request, not by an appointment.** What to do:

- Submit the extension request **in writing**: the authority's online form, e-mail, or a letter.
- If you post it, use **registered mail (Einschreiben)** — at minimum *Einwurf-Einschreiben*.
- If you e-mail, request a read receipt and save your sent folder as a PDF.
- State clearly: your name, date of birth, file number (if any), the expiry date of your current permit, and your request to extend it.

Even a printed e-mail will do — what matters is a **dated proof**. It underpins both your claim to a Fiktionsbescheinigung and any later court action.

## What the Fiktionsbescheinigung is and does

The Fiktionsbescheinigung is the official document confirming that your application is on file and your status continues. It comes in **two versions, and the difference affects your daily life directly:**

| | Section 81(4) (continued validity) | Section 81(3) |
|---|---|---|
| Who gets it | People who applied in time while holding a valid permit or D visa | People lawfully present in Germany without a visa |
| Employment | The work rights of your previous permit **continue** | **Does not allow you to take up employment** |
| Travel abroad + re-entry | **Permitted** | **Not permitted** |

Students and employees almost always fall under **Section 81(4)** — travel and work rights are preserved. Even so, check **which subsection is ticked on your document.** Mistakes happen; ask for an immediate correction.

**Fee:** typically €13 for adults and €6.50 for minors. For Turkish citizens many authorities issue it **free of charge** — a consequence of the fee limits arising from EEC–Turkey Association law. Check the "Gebühren" section on your local authority's page.

**Validity:** usually three to six months. If no decision is issued in that window, it is **extended** — that is routine and does not signal a rejection.

## Travelling on a sheet of paper

With the Section 81(4) version you may leave Germany and return. The problem is rarely the law; it is **the check-in desk**, where staff may not recognise the document. Carry:

- a valid passport (mandatory — the certificate does not replace it),
- your **old residence card**, even though it has expired,
- the original Fiktionsbescheinigung,
- ideally a printout of your application and correspondence with the authority.

One extra safeguard: make sure endorsements such as "Erwerbstätigkeit gestattet" (employment permitted) are actually printed on it. That heads off most border discussions.

## Your employer and your university

If you work as a Werkstudent or part-time, HR sometimes panics: "Your card has expired." Your answer is short: **under Section 81(4) all rights of the previous permit continue until a decision is made**, and the certificate proves it. A copy for the personnel file is enough; no additional permission is required.

The same applies at university: re-registration and enrolment follow the academic calendar, not your card. If something does go wrong there, matters can slide quickly towards [Exmatrikulation](/en/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-en) — we cover that in a separate guide.

## Three months gone: the Untätigkeitsklage

When waiting stops being reasonable, you have a genuine legal instrument: the **Untätigkeitsklage** (action for failure to act) under **Section 75 of the Code of Administrative Court Procedure (VwGO)**. The rule: if the authority fails to decide your application for **more than three months without sufficient reason**, you may sue before the administrative court.

A realistic expectation — and community experience says exactly this: *your position is strong if your file is complete, but the court process itself is not fast either.* In some cities merely serving the claim moves the file; in others you wait months more.

A sensible ladder before filing:

1. **A written status enquiry (Sachstandsanfrage)** quoting your file number, factual and dated. Keep a copy.
2. **Evidence of urgency**: employment contract, internship start date, travel plans, exam dates. Authorities do prioritise.
3. **Dienstaufsichtsbeschwerde** (complaint to the supervisory body): limited legal effect, occasionally useful.
4. **A one-page letter from a lawyer**: often where the matter settles more cheaply than in court.
5. **The Untätigkeitsklage itself.**

**Cost and risk:** court and lawyer fees depend on the value in dispute; residence cases commonly sit somewhere between a few hundred and just over a thousand euros. If you win, costs can be shifted to the authority. Get exact figures from a lawyer first.

**"Will the authority hold it against me?"** — Decisions follow legal criteria, and litigation is a lawful right. The real risk is an **incomplete file**: that is precisely what a "sufficient reason" defence rests on. Submit everything requested before you sue.

## Practical tactics when there are no appointments

- Check the booking portal **early in the morning**; cancellations usually surface then.
- Many cities run a separate channel for students — your university's **International Office** can sometimes arrange a slot directly.
- File in writing regardless: while you wait for an appointment, the **application date** works in your favour.
- If you have moved, complete your [Anmeldung](/en/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt-en) first; a missing registered address stalls the file.

## Frequently asked questions

### My card expired and there is still no answer. Am I here illegally?
No. If you applied before expiry, your status continues under Section 81(4). Your dated application is the proof. That holds even if you do not yet have the certificate — but do request it.

### Can I travel home with a Fiktionsbescheinigung?
With the Section 81(4) version, yes: departure and re-entry are permitted. With Section 81(3), no. Check the subsection on the document and carry your passport and old card.

### Three months have passed — will I win the case?
If your application is complete and the authority has no sufficient reason for the delay, your position is strong. The process still takes time; the claim usually works as pressure rather than as an instant fix.

### My employer refuses to accept an expired card.
Show HR Section 81(4) and the certificate: the work rights of the previous permit continue until a decision. A one-line written confirmation from the authority ends most disputes.

### I want to change the purpose of my stay — does the same rule apply?
Yes. A timely application for a **different residence title** enjoys the same protection. For the move from studies to employment, see our [Zweckwechsel guide](/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en).

### What happens to my health insurance?
Because your status continues, so does your insurance relationship. Send the certificate to your insurer. For a provider comparison, see our [health insurance guide](/en/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs-en).

## Conclusion and honest advice

All of this stress hangs on a single day: **the day before your card expires.** If you applied in writing and provably before then, what follows is an annoying wait — not a legal danger. If you missed that day, your first move is not hunting for an appointment but reaching an advice point: the International Office, a migration counselling service, or a lawyer.

Keep the order simple: **timely written application → Fiktionsbescheinigung → polite reminder → proof of urgency → (after three months) legal pressure.** Followed in that order, most files close without ever reaching a courtroom.

*The provisions and fees cited are current as of September 2026; practice varies from city to city and fees can change. Check your local authority's official page and consult a lawyer where your case is complex.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage',
                'title' => 'Ausländerbehörde Cevap Vermiyor: Fiktionsbescheinigung ve Untätigkeitsklage',
                'excerpt' => 'Oturum kartın bitiyor ama randevu yok. § 81 Abs. 4 AufenthG neden seni koruyor, Fiktionsbescheinigung ile seyahat ve çalışma hakkın ne oluyor, üç ay dolduğunda Untätigkeitsklage gerçekten mantıklı mı — sırasıyla ve abartısız.',
                'meta_title' => 'Ausländerbehörde Gecikirse: Fiktionsbescheinigung ve Dava Yolu',
                'meta_description' => 'Oturum uzatması gecikiyorsa: § 81 Abs. 4 koruması, Fiktionsbescheinigung ile seyahat ve çalışma, harç muafiyeti ve 3 ay sonrası Untätigkeitsklage (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug' => 'auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage-de',
                'title' => 'Wenn die Ausländerbehörde nicht antwortet: Fiktionsbescheinigung und Untätigkeitsklage',
                'excerpt' => 'Die Karte läuft ab, es gibt keinen Termin. Warum § 81 Abs. 4 AufenthG dich schützt, was die Fiktionsbescheinigung für Reisen und Arbeit bedeutet und wann eine Untätigkeitsklage nach drei Monaten wirklich sinnvoll ist.',
                'meta_title' => 'Ausländerbehörde zu langsam: Fiktionsbescheinigung und Klage',
                'meta_description' => 'Verzögerte Verlängerung: Schutz nach § 81 Abs. 4, Reisen und Arbeiten mit Fiktionsbescheinigung, Gebühren und Untätigkeitsklage nach drei Monaten (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug' => 'auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage-en',
                'title' => 'When the Ausländerbehörde Goes Silent: Fiktionsbescheinigung and Untätigkeitsklage',
                'excerpt' => 'Your card expires and there are no appointments. Why Section 81(4) of the Residence Act protects you, what the Fiktionsbescheinigung means for travel and work, and when an action for failure to act genuinely makes sense after three months.',
                'meta_title' => 'Ausländerbehörde Delays: Fiktionsbescheinigung and Legal Action',
                'meta_description' => 'Delayed permit extension: Section 81(4) protection, travel and work on a Fiktionsbescheinigung, fees, and the three-month Untätigkeitsklage route (2026).',
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
            'auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage',
            'auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage-de',
            'auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage-en',
        ])->delete();
    }
};

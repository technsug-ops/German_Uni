<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da banka hesabı açma — adım adım ve gerçek zorluklarıyla.
 *
 * Sitede boşluktu: Sperrkonto, Schufa ve Steuer-ID yazıları vardı ama GIROKONTO açma süreci
 * ve tıkandığı yerler hiçbir yazıda toplu ele alınmamıştı.
 *
 * Doğrulanmış hukuk/veri (Eylül 2026):
 *   - Basiskonto hakkı: § 38 ZKG — AB'de yasal olarak bulunan her tüketicinin hakkı; negatif Schufa,
 *     haciz, iflas veya sabit adres yokluğu RET SEBEBİ OLAMAZ. Haksız redde karşı BaFin nezdinde
 *     ücretsiz idari süreç; BaFin hesabın açılmasını idari işlemle emredebilir. (bafin.de)
 *     Uyarı: Basiskonto ücretsiz demek değil — Stiftung Warentest taramasında 272 Basiskonto'dan
 *     yalnızca 2'si online ücretsiz, şubede hiçbiri.
 *   - IBAN diskriminasyonu: SEPA Tüzüğü (AB) 260/2012 uyarınca ödeyen de alacaklı da hesabın
 *     hangi üye devlette olduğuna göre ayrım yapamaz. Şikayet: BaFin (bafin.de/beschwerde) ve
 *     Wettbewerbszentrale'nin SEPA-ayrımcılığı şikayet birimi.
 *   - Nakit payı (Bundesbank, Zahlungsverhalten 2025): işlemlerin %45'i nakit, %55'i nakitsiz.
 * Yazar: Halil Yaprakli. Kategori: finans. Küme dışı ama Anmeldung/Schufa/Steuer-ID/Sperrkonto
 * yazılarına bağlanıyor.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '3fbc27e8-6a91-4d52-8c7b-91f2d0e4a673';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'finans')->value('id')
            ?? DB::table('categories')->where('slug', 'yasam')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da ilk haftanın en sinir bozucu cümlesi şudur: **"Önce hesabınız olmalı."** Ev sahibi hesap ister, hesap için adres kaydı (Anmeldung) istenir, adres kaydı için ev gerekir. Klasik tavuk-yumurta.

Bu yazı hem doğru sırayı hem de sürecin gerçekten tıkandığı yerleri anlatıyor — ve tıkandığında hangi hakkın olduğunu, çünkü Almanya'da **hesap açmayı reddetmenin sınırları yasayla çizilmiş.**

## Önce karıştırılan üç hesabı ayıralım

| Hesap | Ne işe yarar | Ne değildir |
|---|---|---|
| **Sperrkonto** (bloke hesap) | Vize için para kanıtı; aylık sabit tutar serbest bırakılır | Günlük hesap değil — maaş yatmaz, kira çıkmaz |
| **Girokonto** (cari hesap) | Günlük hayat: kira, maaş, kart, otomatik ödeme | Vize için tek başına yeterli bir kanıt değil |
| **Basiskonto** (temel hesap) | Yasal hakka dayanan asgari cari hesap | Ücretsiz demek değil |

Öğrencilerin büyük kısmı [Sperrkonto](/tr/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and) ile gelir ve ilk aylarda bir **Girokonto** açar; ikisi ayrı hesaplardır ve bloke hesaptan serbest kalan tutar genelde bu cari hesaba aktarılır.

## Doğru sıra

1. **Anmeldung** (adres kaydı) — çoğu bankanın ilk istediği belge. Adım adım: [Anmeldung rehberimiz](/tr/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt).
2. **Girokonto başvurusu** — online veya şubede.
3. **Kimlik doğrulama** — VideoIdent veya PostIdent.
4. **Kart ve PIN postayla gelir** — genelde ayrı zarflarda, birkaç iş günü.
5. **Steuer-ID** birkaç hafta içinde adresine gelir; çalışmaya başlayacaksan şart. Ayrıntı: [Steuer-ID rehberimiz](/tr/blog/how-to-get-a-german-steuer-id-as-a-student-iban).
6. **Sperrkonto → Girokonto** aylık transfer talimatını kur.

Sırayı bilmek, ilk ayın yarısını kurtarır.

## Hangi banka tipi?

| | Şube bankası (Sparkasse, Volksbank, Deutsche Bank) | Çevrimiçi banka (N26, C24, Revolut, Vivid vb.) |
|---|---|---|
| Başvuru | Randevu + yüz yüze | Uygulamadan, dakikalar içinde |
| Dil | Çoğunlukla Almanca | Genelde İngilizce arayüz |
| Nakit yatırma | Şubede kolay | Sınırlı, çoğu zaman ücretli |
| Girocard (EC kart) | Standart | Bazılarında ek ücretli veya yok |
| Resmî işlerde kabul | Sorunsuz | Genelde sorunsuz, ama IBAN ülkesine dikkat |

**Pratik strateji:** çoğu öğrenci çevrimiçi bankayla başlıyor (hızlı, İngilizce), sonra Girocard ve nakit ihtiyacı için bir şube bankası ekliyor. İkisini birden tutmak yaygın ve mantıklı.

## Gereken belgeler

- **Pasaport** (kimlik kartı değil — uluslararası öğrenciler için pasaport standarttır)
- **Oturum izni veya vize** (bazı bankalar ister)
- **Anmeldebestätigung** (adres kaydı belgesi)
- **Immatrikulationsbescheinigung** (öğrenci belgesi — öğrenci hesabı ücretsizliği için)
- **Alman cep telefonu numarası** (doğrulama SMS'i için; ön ödemeli hat yeterli)
- **Steuer-ID** (hemen değil, sonradan verilebilir)

## Gerçek zorluklar — ve çözümleri

### 1. Tavuk-yumurta: adres yok, hesap yok

Ev bulmadan Anmeldung yapamıyorsun; bazı ev sahipleri hesap görmeden sözleşme yapmıyor. Çıkış yolları:

- Geçici konaklamada (yurt, Zwischenmiete, öğrenci evi) **Wohnungsgeberbestätigung** alabiliyorsan Anmeldung yapılır — ev sahibinin imzası şart.
- Bazı çevrimiçi bankalar hesabı açıp **adres doğrulamasını sonraya** bırakır; başvuruda geçici adresini kullan, taşınınca güncelle.
- Sperrkonto sağlayıcıları bazı paketlerde cari hesabı da veriyor — gelmeden önce bunu kontrol et.

### 2. VideoIdent'te takılmak

En sık şikâyet: video doğrulama reddediliyor. Sebepleri neredeyse hep aynı:

- Pasaport üzerindeki hologram parlaması, kötü ışık, düşük kamera çözünürlüğü
- **İsimdeki Türkçe karakterler** — başvuruya yazdığın ad ile pasaportun makine-okur alanındaki yazım (ör. "Gülşah" ↔ "GULSAH") uyuşmuyor. Başvuruyu **pasaporttaki latinize yazımla** doldur.
- Süresi dolmak üzere olan pasaport
- Sağlam olmayan internet bağlantısı

İki kez reddedildiyse **PostIdent**'e geç: formu yazdır, pasaportla bir postaneye git. Yavaş ama neredeyse hiç hata vermez.

### 3. Schufa'n yok

Almanya'ya yeni geldiysen kredi geçmişin **yok** — kötü değil, boş. Cari hesap açmak için genelde sorun olmaz; ama kredi kartı, taksitli alışveriş ve bazı telefon sözleşmeleri reddedilebilir. Hesap açılışının kendisi bir Schufa kaydı oluşturur ve zamanla geçmişin birikir. Ayrıntı: [Schufa rehberimiz](/tr/blog/schufa-guide-2026-why-is-credit-score-important-for-turkish-students).

### 4. Beklenmeyen ücretler

Öğrenci hesapları genelde ücretsizdir ama koşulludur: **yaş sınırı** (çoğu bankada 27–30) ve öğrenci belgesi. Şartın dışına düştüğün ay aylık ücret başlar ve fark edilmesi aylar alabilir. Yılda bir kez şu üçünü kontrol et: aylık hesap ücreti, kart ücreti, yurt dışı çekim ücreti.

### 5. Hesap açma talebin reddedilirse — burada güçlü bir hakkın var

Almanya'da **AB'de yasal olarak bulunan her tüketicinin temel hesap (Basiskonto) hakkı vardır** (§ 38 ZKG). Önemli olan kısım: **negatif Schufa, haciz, iflas ya da sabit adres yokluğu ret gerekçesi olamaz.** Banka reddederse:

1. Reddi **yazılı** olarak iste.
2. **BaFin**'e başvur — ücretsiz bir idari süreç işletilir ve BaFin, hesabın açılmasını idari işlemle emredebilir.
3. Tüketici derneği (Verbraucherzentrale) danışmanlığı bu süreçte yardımcı olur.

Uyarı: Basiskonto "ücretsiz hesap" demek değildir. Stiftung Warentest taramasında incelenen 272 temel hesaptan yalnızca ikisi çevrimiçi ücretsizdi; şubede ücretsiz olan yoktu. Hakkın var, ama ücreti karşılaştırman gerekir.

### 6. IBAN'ın kabul edilmiyor

Çevrimiçi bankaların bir kısmı Almanya dışı IBAN verir (LT, IE gibi). İşveren, ev sahibi, sigorta ya da bir kurum "yalnızca DE ile başlayan IBAN" derse, bu **yasa dışıdır**: SEPA Tüzüğü (AB) 260/2012 uyarınca ödeme yapan da alan da hesabın hangi üye devlette olduğuna göre ayrım yapamaz.

Ne yaparsın: kuralı nazikçe hatırlat (çoğu durumda mesele bilgisizlik ve teknik formdur), sonuç alamazsan **BaFin'in şikâyet formuna** başvur; ayrıca Wettbewerbszentrale'nin SEPA ayrımcılığı şikâyet birimi bu dosyaları topluyor. Hızlı çözüm isteyen öğrenciler için pratik yol: DE IBAN veren bir hesabı yedek olarak açmak.

### 7. Kart var ama geçmiyor

Almanya'da **Girocard (EC kart)** ile kredi kartı farklı şeylerdir; küçük işletmelerin bir kısmı yalnızca Girocard kabul eder, bazıları yalnızca nakit ister. Bundesbank'ın ödeme davranışı çalışmasına göre 2025'te işlemlerin **%45'i hâlâ nakitle** yapıldı. Yani sanal kart tek başına yetmez: yanında nakit ve mümkünse bir Girocard bulundur ([yazısız kurallar yazımız](/tr/blog/unwritten-rules-of-daily-life-in-germany) bu tarafı ayrıntılandırıyor).

## Sıkça Sorulanlar

### Almanya'ya gelmeden hesap açabilir miyim?
Sperrkonto'yu evet, genelde yurt dışından açarsın. Girokonto için çoğu banka Alman adresi ister; birkaç çevrimiçi banka istisna olabilir ama vize sürecinde Sperrkonto esas kanıttır.

### Sperrkonto günlük hesap yerine geçer mi?
Hayır. Bloke hesaptan yalnızca aylık belirlenen tutar serbest kalır; kira ödemesi, maaş girişi ve otomatik ödemeler için ayrı bir Girokonto gerekir.

### Sadece çevrimiçi banka yeter mi?
Çoğu durumda yeter. Yetmediği üç durum: düzenli nakit yatırman gerekiyorsa, karşı taraf yalnızca Girocard kabul ediyorsa, ya da bir kurum DE IBAN dayatıyorsa (yasal olmasa da pratikte karşına çıkar).

### Hesabım reddedildi, elimde ne var?
Reddi yazılı iste ve Basiskonto hakkını hatırlat (§ 38 ZKG). Banka direnirse BaFin'e başvur; süreç ücretsizdir ve BaFin hesabın açılmasını emredebilir.

### Öğrenci hesabı ne zaman ücretli olur?
Yaş sınırını aştığında veya öğrenci belgesini yenilemediğinde. Banka çoğu zaman bunu duyurmadan uygular — dönem başında öğrenci belgeni bankaya iletmeyi alışkanlık hâline getir.

### Steuer-ID gelmeden çalışabilir miyim?
İşveren maaş bordrosu için Steuer-ID ister; gecikirse ilk maaşta yüksek vergi kesintisi olabilir ve sonradan iade edilir. Numaranı takip et: [Steuer-ID rehberimiz](/tr/blog/how-to-get-a-german-steuer-id-as-a-student-iban).

## Sonuç ve dürüst tavsiye

Banka hesabı, Almanya'daki ilk ayın en çok vakit kaybettiren ama en çok **hakla korunan** işidir. İki cümle aklında kalsın:

1. **Sıra:** Anmeldung → Girokonto → kimlik doğrulama → kart → Steuer-ID → Sperrkonto transferi.
2. **Tıkanırsan:** ret yazılı olarak istenir, temel hesap hakkı hatırlatılır, gerekirse BaFin devreye girer. IBAN ayrımcılığı da yasaktır.

Pratik kurulum: bir çevrimiçi banka (hız ve İngilizce arayüz) + bir şube bankası (Girocard ve nakit). İkisi birlikte, ilk yılın bütün senaryolarını kapatıyor.

*Bu yazıdaki yasal dayanaklar ve tutarlar 2026 Eylül itibarıyla geçerlidir; banka ücretleri, öğrenci hesabı yaş sınırları ve belge listeleri bankadan bankaya değişir ve değişebilir. Başvurmadan önce bankanın kendi fiyat listesini (Preis- und Leistungsverzeichnis) kontrol et.*
MD;

        $deBody = <<<'MD'
Der nervigste Satz der ersten Woche in Deutschland lautet: **„Dafür brauchen Sie erst ein Konto."** Die Vermieterin will eine Kontoverbindung, für das Konto wird die Anmeldung verlangt, für die Anmeldung braucht man eine Wohnung. Ein klassisches Henne-Ei-Problem.

Dieser Artikel zeigt die richtige Reihenfolge und die Stellen, an denen es wirklich klemmt — und welche Rechte du hast, wenn es klemmt. Denn in Deutschland ist gesetzlich geregelt, **wie weit eine Bank eine Kontoeröffnung ablehnen darf.**

## Zuerst: drei Konten, die ständig verwechselt werden

| Konto | Wofür | Was es nicht ist |
|---|---|---|
| **Sperrkonto** | Finanzierungsnachweis fürs Visum; monatlich wird ein fester Betrag frei | Kein Alltagskonto — kein Gehalt, keine Miete |
| **Girokonto** | Alltag: Miete, Gehalt, Karte, Lastschriften | Für sich allein kein Visumsnachweis |
| **Basiskonto** | Gesetzlich garantiertes Mindest-Girokonto | Nicht automatisch kostenlos |

Die meisten Studierenden kommen mit einem [Sperrkonto](/de/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-de) an und eröffnen in den ersten Wochen ein **Girokonto**; der monatlich freigegebene Betrag wird üblicherweise dorthin überwiesen.

## Die richtige Reihenfolge

1. **Anmeldung** — das erste Dokument, das fast jede Bank sehen will. Schritt für Schritt: unsere [Anmeldung-Anleitung](/de/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt-de).
2. **Girokonto beantragen** — online oder in der Filiale.
3. **Identifikation** — VideoIdent oder PostIdent.
4. **Karte und PIN kommen per Post** — meist getrennt, innerhalb weniger Werktage.
5. **Steuer-ID** trifft nach einigen Wochen ein; für eine Beschäftigung ist sie nötig. Details: unser [Steuer-ID-Ratgeber](/de/blog/how-to-get-a-german-steuer-id-as-a-student-iban-de).
6. **Dauerauftrag Sperrkonto → Girokonto** einrichten.

Wer die Reihenfolge kennt, spart die halbe erste Woche.

## Welcher Banktyp?

| | Filialbank (Sparkasse, Volksbank, Deutsche Bank) | Onlinebank (N26, C24, Revolut, Vivid u. a.) |
|---|---|---|
| Antrag | Termin, persönlich | In Minuten über die App |
| Sprache | Überwiegend Deutsch | Meist englische Oberfläche |
| Bargeld einzahlen | In der Filiale einfach | Eingeschränkt, oft kostenpflichtig |
| Girocard | Standard | Teils Aufpreis oder gar nicht |
| Akzeptanz bei Behörden | Unproblematisch | Meist unproblematisch, aber auf das IBAN-Land achten |

**Praxisstrategie:** Viele starten mit einer Onlinebank (schnell, englischsprachig) und ergänzen später eine Filialbank für Girocard und Bargeld. Beides parallel zu führen, ist verbreitet und sinnvoll.

## Benötigte Unterlagen

- **Reisepass** (für internationale Studierende der Standard)
- **Aufenthaltstitel oder Visum** (manche Banken verlangen ihn)
- **Anmeldebestätigung**
- **Immatrikulationsbescheinigung** (für das kostenfreie Studierendenkonto)
- **Deutsche Mobilnummer** (für Verifizierungs-SMS; Prepaid genügt)
- **Steuer-ID** (kann nachgereicht werden)

## Die echten Hürden — und was hilft

### 1. Henne und Ei: keine Adresse, kein Konto

Ohne Wohnung keine Anmeldung, und manche Vermietende wollen vorab eine Kontoverbindung. Auswege:

- Auch bei Zwischenmiete oder im Wohnheim ist die Anmeldung möglich, sofern du eine **Wohnungsgeberbestätigung** bekommst — die Unterschrift der vermietenden Person ist Pflicht.
- Einige Onlinebanken eröffnen das Konto und prüfen die Adresse **später**; melde zunächst die Übergangsadresse und aktualisiere sie nach dem Umzug.
- Manche Sperrkonto-Anbieter liefern im Paket ein Girokonto mit — vor der Anreise prüfen.

### 2. Scheitern beim VideoIdent

Die häufigste Beschwerde: Die Videoprüfung wird abgelehnt. Die Gründe wiederholen sich:

- Spiegelnde Hologramme, schlechtes Licht, schwache Kamera
- **Sonderzeichen im Namen** — die Schreibweise im Antrag passt nicht zur maschinenlesbaren Zone des Passes (etwa „Gülşah" ↔ „GULSAH"). Fülle den Antrag in der **Pass-Schreibweise** aus.
- Ein Pass, der bald abläuft
- Instabile Internetverbindung

Nach zwei Fehlversuchen: auf **PostIdent** wechseln — Formular ausdrucken, mit Pass in eine Postfiliale. Langsamer, aber praktisch fehlerfrei.

### 3. Keine Schufa-Historie

Wer neu ankommt, hat **keine** Kredithistorie — nicht schlecht, sondern leer. Fürs Girokonto ist das meist unerheblich; Kreditkarte, Ratenkauf und manche Mobilfunkverträge können aber abgelehnt werden. Die Kontoeröffnung selbst erzeugt einen Schufa-Eintrag, und mit der Zeit entsteht Historie. Mehr dazu: unser [Schufa-Ratgeber](/de/blog/schufa-guide-2026-why-is-credit-score-important-for-turkish-students-de).

### 4. Unerwartete Gebühren

Studierendenkonten sind meist kostenlos, aber an Bedingungen geknüpft: **Altersgrenze** (häufig 27–30) und gültige Immatrikulationsbescheinigung. Sobald du herausfällst, beginnt die Kontoführungsgebühr — oft unbemerkt. Prüfe einmal jährlich drei Posten: Kontoführung, Kartengebühr, Abhebungen im Ausland.

### 5. Wenn die Eröffnung abgelehnt wird — hier steht dir ein Recht zu

In Deutschland hat **jede Verbraucherin und jeder Verbraucher mit rechtmäßigem Aufenthalt in der EU Anspruch auf ein Basiskonto** (§ 38 ZKG). Entscheidend: **negative Schufa, Pfändung, Privatinsolvenz oder fehlender fester Wohnsitz sind keine zulässigen Ablehnungsgründe.** Wenn eine Bank ablehnt:

1. Die Ablehnung **schriftlich** verlangen.
2. Die **BaFin** einschalten — das Verfahren ist kostenlos, und die BaFin kann die Eröffnung per Verwaltungsakt anordnen.
3. Die Verbraucherzentrale berät begleitend.

Wichtig: Basiskonto heißt nicht kostenlos. In einer Untersuchung der Stiftung Warentest waren von 272 Basiskonten nur zwei online gebührenfrei, in der Filiale keines. Das Recht besteht, die Preise musst du trotzdem vergleichen.

### 6. Deine IBAN wird nicht akzeptiert

Manche Onlinebanken vergeben IBANs außerhalb Deutschlands (etwa LT oder IE). Verlangt ein Arbeitgeber, eine Vermieterin, eine Versicherung oder eine Behörde „nur IBAN mit DE", ist das **unzulässig**: Nach der SEPA-Verordnung (EU) 260/2012 dürfen weder Zahler noch Zahlungsempfänger danach unterscheiden, in welchem Mitgliedstaat das Konto geführt wird.

Vorgehen: sachlich auf die Regel hinweisen (meist ist es Unkenntnis oder ein starres Formular); bleibt es dabei, hilft die **Beschwerde bei der BaFin**, und die Wettbewerbszentrale sammelt Fälle in ihrer Beschwerdestelle zur SEPA-Diskriminierung. Wer schnell weiterkommen will, eröffnet zusätzlich ein Konto mit DE-IBAN.

### 7. Karte da, wird aber nicht genommen

**Girocard** und Kreditkarte sind in Deutschland zweierlei; kleinere Betriebe akzeptieren teils nur Girocard, teils nur Bargeld. Laut Bundesbank-Studie zum Zahlungsverhalten wurden 2025 noch **45 % der Transaktionen bar** bezahlt. Eine virtuelle Karte allein reicht also nicht: Bargeld und möglichst eine Girocard gehören dazu (mehr dazu in unseren [ungeschriebenen Regeln](/de/blog/unwritten-rules-of-daily-life-in-germany-de)).

## Häufige Fragen

### Kann ich das Konto vor der Einreise eröffnen?
Das Sperrkonto ja, meist aus dem Ausland. Fürs Girokonto verlangen die meisten Banken eine deutsche Adresse; einzelne Onlinebanken sind Ausnahmen, fürs Visum zählt ohnehin das Sperrkonto.

### Ersetzt das Sperrkonto ein Alltagskonto?
Nein. Vom Sperrkonto wird nur der monatliche Betrag frei; für Miete, Gehalt und Lastschriften brauchst du ein eigenes Girokonto.

### Reicht eine reine Onlinebank?
Meistens ja. Drei Ausnahmen: regelmäßige Bareinzahlungen, Gegenüber akzeptiert nur Girocard, oder eine Stelle besteht auf einer DE-IBAN (auch wenn das rechtlich unzulässig ist).

### Mein Antrag wurde abgelehnt — was nun?
Ablehnung schriftlich anfordern und auf den Anspruch nach § 38 ZKG hinweisen. Bleibt die Bank dabei, die BaFin einschalten; das Verfahren ist kostenlos und kann die Eröffnung anordnen.

### Wann wird das Studierendenkonto kostenpflichtig?
Wenn du die Altersgrenze überschreitest oder die Immatrikulationsbescheinigung nicht erneuerst. Banken setzen das oft stillschweigend um — reiche die Bescheinigung jedes Semester nach.

### Kann ich ohne Steuer-ID arbeiten?
Der Arbeitgeber braucht sie für die Lohnabrechnung; fehlt sie, wird zunächst hoch besteuert und später erstattet. Verfolge die Nummer: [Steuer-ID-Ratgeber](/de/blog/how-to-get-a-german-steuer-id-as-a-student-iban-de).

## Fazit und ehrlicher Rat

Das Bankkonto kostet im ersten Monat am meisten Zeit — ist aber zugleich das am besten **rechtlich geschützte** Vorhaben. Zwei Sätze solltest du behalten:

1. **Die Reihenfolge:** Anmeldung → Girokonto → Identifikation → Karte → Steuer-ID → Dauerauftrag vom Sperrkonto.
2. **Wenn es klemmt:** Ablehnung schriftlich verlangen, auf das Basiskonto verweisen, notfalls die BaFin einschalten. Auch IBAN-Diskriminierung ist verboten.

Praktisches Setup: eine Onlinebank (Tempo, englische Oberfläche) plus eine Filialbank (Girocard, Bargeld). Zusammen decken sie alle Szenarien des ersten Jahres ab.

*Rechtsgrundlagen und Angaben gelten mit Stand September 2026; Gebühren, Altersgrenzen und Unterlagenlisten unterscheiden sich je nach Bank und können sich ändern. Prüfe vor dem Antrag das Preis- und Leistungsverzeichnis der Bank.*
MD;

        $enBody = <<<'MD'
The most maddening sentence of your first week in Germany is: **"You will need an account first."** The landlord wants bank details, the bank wants your address registration, and registering requires a flat. A textbook chicken-and-egg problem.

This article gives you the correct order and the points where the process genuinely jams — plus the rights you have when it does, because German law sets clear limits on **how far a bank may refuse to open an account.**

## First, three accounts people constantly confuse

| Account | What it is for | What it is not |
|---|---|---|
| **Sperrkonto** (blocked account) | Proof of funds for the visa; a fixed amount is released monthly | Not an everyday account — no salary, no rent |
| **Girokonto** (current account) | Daily life: rent, salary, card, direct debits | Not sufficient on its own as visa evidence |
| **Basiskonto** (basic account) | A minimum current account guaranteed by law | Not automatically free |

Most students arrive with a [Sperrkonto](/en/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-en) and open a **Girokonto** in their first weeks; the monthly released amount is normally transferred into it.

## The correct order

1. **Anmeldung** (address registration) — the first document nearly every bank asks for. Step by step: our [Anmeldung walkthrough](/en/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt-en).
2. **Apply for a Girokonto** — online or in a branch.
3. **Identity verification** — VideoIdent or PostIdent.
4. **Card and PIN arrive by post** — usually in separate envelopes, within a few working days.
5. **Your Steuer-ID** arrives within a few weeks and is required once you start working. Details: our [Steuer-ID guide](/en/blog/how-to-get-a-german-steuer-id-as-a-student-iban-en).
6. **Set up the standing transfer** from the blocked account to your current account.

Knowing the order saves you half of your first month.

## Which type of bank?

| | Branch bank (Sparkasse, Volksbank, Deutsche Bank) | Online bank (N26, C24, Revolut, Vivid and others) |
|---|---|---|
| Application | Appointment, in person | Minutes, through the app |
| Language | Mostly German | Usually an English interface |
| Depositing cash | Easy at a branch | Limited, often charged |
| Girocard (EC card) | Standard | Sometimes extra, sometimes unavailable |
| Acceptance by institutions | No issues | Usually fine, but watch the IBAN country |

**A practical strategy:** many students start with an online bank (fast, in English) and later add a branch account for the Girocard and cash. Running both is common and sensible.

## Documents you need

- **Passport** (the standard for international students)
- **Residence permit or visa** (some banks ask for it)
- **Anmeldebestätigung**, your registration confirmation
- **Enrolment certificate** (for fee-free student accounts)
- **A German mobile number** for verification texts; prepaid is fine
- **Steuer-ID** (can be supplied later)

## The real obstacles — and what to do

### 1. Chicken and egg: no address, no account

You cannot register without a flat, and some landlords want bank details before signing. Ways through:

- Temporary housing counts: if you can get a **Wohnungsgeberbestätigung** from a dorm or sublet, you can register — the landlord's signature is mandatory.
- Some online banks open the account and verify the address **later**; apply with your temporary address and update it after you move.
- A few blocked-account providers bundle a current account — check before you travel.

### 2. Getting stuck in VideoIdent

The most common complaint: video verification is rejected. The reasons repeat themselves:

- Holograms reflecting, poor lighting, a weak camera
- **Special characters in your name** — the spelling on the application does not match the machine-readable zone of your passport (for example "Gülşah" versus "GULSAH"). Fill in the application **exactly as your passport prints it**.
- A passport close to expiry
- An unstable connection

After two failures, switch to **PostIdent**: print the form and take it with your passport to a post office. Slower, but almost never fails.

### 3. You have no Schufa record

Newly arrived, your credit history is **absent** rather than bad. That rarely blocks a current account, but credit cards, instalment purchases and some mobile contracts can be refused. Opening the account itself creates a Schufa entry, and history builds over time. More in our [Schufa guide](/en/blog/schufa-guide-2026-why-is-credit-score-important-for-turkish-students-en).

### 4. Unexpected fees

Student accounts are usually free, but conditionally: an **age limit** (commonly 27–30) and a valid enrolment certificate. The month you fall outside those conditions, monthly fees begin — often unnoticed. Check three items once a year: account fee, card fee, and charges for withdrawals abroad.

### 5. If your application is refused — you have a strong right here

In Germany **every consumer lawfully resident in the EU is entitled to a basic account** (Section 38 of the Payment Accounts Act, ZKG). The crucial part: **a negative Schufa record, attachment of earnings, insolvency or having no fixed address are not permissible grounds for refusal.** If a bank says no:

1. Ask for the refusal **in writing**.
2. Take it to **BaFin**, the financial regulator — the procedure is free, and BaFin can order the bank to open the account.
3. Your local consumer advice centre (Verbraucherzentrale) can support the process.

One caveat: a basic account is not a free account. In a Stiftung Warentest survey of 272 basic accounts, only two were free online and none in branches. The right exists; you still have to compare prices.

### 6. Your IBAN is not accepted

Some online banks issue IBANs from outside Germany (LT or IE, for instance). If an employer, landlord, insurer or public body insists on "an IBAN starting with DE", that is **unlawful**: under the SEPA Regulation (EU) 260/2012, neither payer nor payee may discriminate based on which member state holds the account.

What to do: point to the rule politely — it is usually ignorance or a rigid form — and if that fails, **file a complaint with BaFin**; the Wettbewerbszentrale also runs a dedicated complaints office for SEPA discrimination. Students who need a fast fix simply open a second account with a DE IBAN.

### 7. You have a card, but it is not accepted

In Germany a **Girocard** and a credit card are different things; smaller businesses may take only the former, or only cash. According to the Bundesbank's payment behaviour study, **45% of transactions were still made in cash** in 2025. A virtual card alone will not carry you: keep cash and ideally a Girocard as well (our [unwritten rules guide](/en/blog/unwritten-rules-of-daily-life-in-germany-en) covers this side in detail).

## Frequently asked questions

### Can I open an account before arriving?
A blocked account, yes — usually from abroad. For a current account most banks want a German address; a few online banks are exceptions, and for the visa the blocked account is what counts anyway.

### Does a blocked account replace an everyday account?
No. Only the set monthly amount is released from it; rent, salary and direct debits need a separate current account.

### Is an online bank alone enough?
Usually yes. Three exceptions: you need to deposit cash regularly, the other side accepts only Girocard, or an organisation insists on a DE IBAN (even though that is not lawful).

### My application was refused — what now?
Request the refusal in writing and cite the basic-account entitlement under Section 38 ZKG. If the bank holds firm, involve BaFin; the procedure is free and can compel the bank to open the account.

### When does a student account start charging?
When you pass the age limit or fail to renew your enrolment certificate. Banks often apply this silently — send in the certificate every semester.

### Can I work before my Steuer-ID arrives?
Your employer needs it for payroll; without it you are taxed at a high rate at first and refunded later. Track the number: [Steuer-ID guide](/en/blog/how-to-get-a-german-steuer-id-as-a-student-iban-en).

## Conclusion and honest advice

The bank account eats more of your first month than anything else — and it is also the task with the **strongest legal backing**. Two sentences to keep:

1. **The order:** Anmeldung → current account → identity verification → card → Steuer-ID → standing transfer from the blocked account.
2. **When it jams:** get the refusal in writing, cite the basic-account entitlement, escalate to BaFin if necessary. IBAN discrimination is prohibited too.

A practical setup: one online bank (speed, English interface) plus one branch bank (Girocard, cash). Together they cover every scenario of your first year.

*Legal references and figures are current as of September 2026; fees, student account age limits and document lists vary between banks and can change. Check the bank's own price list (Preis- und Leistungsverzeichnis) before applying.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'opening-a-bank-account-in-germany-the-real-obstacles',
                'title' => 'Almanya\'da Banka Hesabı Açma Serüveni: Adımlar ve Gerçek Zorluklar',
                'excerpt' => 'Adres olmadan hesap, hesap olmadan ev: tavuk-yumurta döngüsünden çıkış yolu. Sperrkonto/Girokonto/Basiskonto farkı, doğru sıra, VideoIdent tuzakları, Schufa\'sızlık, gizli ücretler ve reddedilirsen § 38 ZKG ile BaFin yolu.',
                'meta_title' => 'Almanya\'da Banka Hesabı Açma: Adımlar, Zorluklar, Haklar',
                'meta_description' => 'Almanya\'da Girokonto açma: doğru sıra, gerekli belgeler, VideoIdent hataları, Schufa, gizli ücretler, Basiskonto hakkı ve IBAN ayrımcılığı (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug' => 'opening-a-bank-account-in-germany-the-real-obstacles-de',
                'title' => 'Bankkonto in Deutschland eröffnen: Ablauf und die echten Hürden',
                'excerpt' => 'Ohne Adresse kein Konto, ohne Konto keine Wohnung — so kommst du aus der Schleife. Unterschied zwischen Sperrkonto, Girokonto und Basiskonto, die richtige Reihenfolge, VideoIdent-Fallen, fehlende Schufa, versteckte Gebühren und der Anspruch nach § 38 ZKG.',
                'meta_title' => 'Bankkonto in Deutschland eröffnen: Ablauf, Hürden, Rechte',
                'meta_description' => 'Girokonto in Deutschland: Reihenfolge, Unterlagen, VideoIdent-Fehler, Schufa, Gebühren, Basiskonto-Anspruch und IBAN-Diskriminierung (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug' => 'opening-a-bank-account-in-germany-the-real-obstacles-en',
                'title' => 'Opening a Bank Account in Germany: The Steps and the Real Obstacles',
                'excerpt' => 'No address without an account, no account without an address — how to break the loop. The difference between blocked, current and basic accounts, the right order, VideoIdent traps, having no Schufa record, hidden fees, and your legal right to an account.',
                'meta_title' => 'Opening a Bank Account in Germany: Steps, Obstacles, Rights',
                'meta_description' => 'German current account: the right order, documents, VideoIdent failures, Schufa, fees, your basic-account entitlement and IBAN discrimination (2026).',
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
            'opening-a-bank-account-in-germany-the-real-obstacles',
            'opening-a-bank-account-in-germany-the-real-obstacles-de',
            'opening-a-bank-account-in-germany-the-real-obstacles-en',
        ])->delete();
    }
};

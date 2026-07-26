<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Sosyal Hizmet (Soziale Arbeit) okumak (2026).
 * Doğrulandı: Soziale Arbeit/Sozialpädagogik = düzenlenmiş meslek; çalışmak için
 * staatliche Anerkennung ("Staatlich anerkannte:r Sozialarbeiter:in") gerekir.
 * Bachelor genelde FH/HAW (uygulamalı) + sık Anerkennungsjahr/pratik dönem. Bachelor
 * Almanca (C1); İngilizce program NADİR. Yüksek talep/Fachkräftemangel (Jugendamt,
 * göç, yaşlı/engelli, okul sosyal hizmeti). Sperrkonto 2026 ~11.904€/yıl. Sayılar yıl-hedge'li.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c9d10000-1111-4a5f-9f60-cc10dd16aa01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Sosyal hizmet (Almancası **Soziale Arbeit**, yakın kardeşi **Sozialpädagogik**) Almanya'da çok özel bir alan: bir yandan **düzenlenmiş bir meslek**, diğer yandan yıllardır süren ciddi bir eleman açığı (**Fachkräftemangel**) var. Yani doğru dille geldiğinde neredeyse garantili, anlamlı ve kalıcı oturuma açılan bir yol. Ama bu yolun bir bedeli var ve o bedel neredeyse tamamen dil üzerine kurulu. Bu yazı alanı süslemeden anlatıyor: ne okunur, hangi yapıda, hangi dilde, nasıl başvurulur ve dürüst beklenti ne olmalı. En baştan net olalım: **Almanya'da sosyal hizmet, Almancası güçlü bir Türk öğrenci için mükemmel ve stabil bir rota; Almancası olmayan için ise gerçekten zor bir alandır.**

## 1. Alan nedir ve "staatliche Anerkennung" neden kritik?

Soziale Arbeit; insanlarla, ailelerle ve gruplarla çalışıp onların toplumsal hayata katılımını desteklemeyi hedefleyen uygulamalı bir alandır. Ama Almanya'da işin özü şu: bu **düzenlenmiş (regulated) bir meslektir.** "Sosyalarbeiter:in" olarak devletin ve kurumların tanıdığı bir çalışan olmak için sadece diploma yetmez; **staatliche Anerkennung** (devlet tanınması) gerekir. Bu tanınmayı aldığında unvanın resmî olarak **"Staatlich anerkannte:r Sozialarbeiter:in"** olur.

Bu neden önemli? Çünkü **Jugendamt** (gençlik dairesi), belediyeler, hastaneler ve büyük refah kuruluşları çoğu pozisyonda bu resmî tanınmayı şart koşar. Almanya'daki bir sosyal hizmet lisansı programını seçerken sorman gereken ilk soru, "bu program mezuniyette **staatliche Anerkennung** veriyor mu?" olmalıdır. İyi haber: FH/HAW'ların akredite Soziale Arbeit programlarının büyük çoğunluğu bunu içerir.

## 2. FH/HAW yapısı ve Anerkennungsjahr (pratik yıl)

Almanya'da sosyal hizmet ağırlıkla **klasik üniversitelerde (Uni) değil**, uygulamalı bilimler yüksekokullarında — yani **Fachhochschule (FH) / Hochschule für Angewandte Wissenschaften (HAW)** — okutulur. Bu bilinçli bir tercih: alan teoriden çok sahaya dönük olduğu için FH/HAW'ın uygulama odaklı yapısı buraya birebir uyar. Zaten Almanya'da FH ile Uni arasındaki fark bir "kalite" farkı değil, bir **yönelim** farkıdır; bunu merak edenler için [Almanya'da prestij ve sıralamalar nasıl işler yazısı](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) bu kafa karışıklığını gideriyor.

Programın en ayırt edici parçası **pratik dönem**. Birçok FH/HAW programı ya müfredata gömülü uzun bir zorunlu staj (Praxissemester) içerir ya da mezuniyet sonrası bir **Anerkennungsjahr** (tanınma yılı) şart koşar — kurumda denetimli, ücretli/kısmi ücretli, tam zamanlı çalışılan bir pratik yıl. Bazı programlar bu yılı **"dual"** biçimde diplomaya entegre eder, bazısında ayrı yürütülür. Sonuçta staatliche Anerkennung genelde **teori + denetimli pratik** birleşince verilir.

| Öğe | Ne demek |
|---|---|
| **Kurum tipi** | Genelde FH / HAW (uygulamalı), bazı Uni'lerde de var |
| **Derece** | Bachelor of Arts, Soziale Arbeit (genelde 6–7 dönem) |
| **Pratik** | Praxissemester ve/veya **Anerkennungsjahr** |
| **Çıktı** | Diploma **+ staatliche Anerkennung** (asıl değerli olan bu) |
| **Öğretim dili** | Neredeyse tamamen **Almanca (C1)** |

## 3. Dil gerçeği: Almanca C1 şart, İngilizce program nadir

Bu, tüm yazının en dürüst ve en belirleyici bölümü. **Sosyal hizmet Almanya'da dil-yoğun bir alandır ve lisans programları neredeyse tamamen Almancadır — genelde C1 istenir.** Bunun mühendislik gibi alanlardan farkı çok net: sosyal hizmetin işi doğrudan **insanla, danışanla** konuşmaktır. Bir Jugendamt görüşmesini, bir aile danışmanlığını, bir mülteci başvurusunu ya da bir okul çatışmasını İngilizceyle yürütemezsin. Üstüne bir de **Alman sosyal hukuku (SGB — Sozialgesetzbuch)** var; bu sistemi anlamadan sahada çalışmak mümkün değil ve bu mevzuat baştan aşağı Almancadır.

Bu yüzden **İngilizce sosyal hizmet lisansı Almanya'da gerçekten nadirdir.** Nadiren rastladığın İngilizce programlar da genelde uluslararası/sosyal politika odaklı master düzeyinde olur ve bunlar seni otomatik olarak sahada çalışacak "Sozialarbeiter:in" yapmaz — çünkü staatliche Anerkennung ve danışan işi Almanca ister. Dürüst özet: **bu alanı ciddiye alıyorsan, Almancayı bir "ek" değil, mesleğin kendisi olarak görmelisin.** Almancan yoksa, alan sana kapalı değil ama önce ciddi bir dil yatırımı şart.

## 4. Tanınan okullar (yaklaşık, 2026)

Almanya'da "en iyi sosyal hizmet üniversitesi" diye tek bir sıralama yoktur; önemli olan programın **akredite** olması ve **staatliche Anerkennung** vermesidir. Aşağıdaki tablo, saygın kabul edilen bazı FH/HAW örnekleridir (kesin sıralama değil, örnek):

| Okul | Not |
|---|---|
| **Alice Salomon Hochschule Berlin (ASH)** | Almanya'da sosyal hizmetin en köklü adreslerinden |
| **Katholische / Evangelische Hochschulen** (Freiburg, Berlin, Nürnberg vb.) | Kilise kökenli, alanda çok güçlü ağ (Caritas/Diakonie bağlantısı) |
| **Hochschule München / HAW Hamburg / TH Köln** | Büyük şehir HAW'ları, geniş program ve staj ağı |
| **Frankfurt UAS, Hochschule Düsseldorf, Hochschule Esslingen** | Yerleşik, akredite Soziale Arbeit programları |
| **Duale Hochschule (örn. BW)** | Pratiği en baştan diplomaya gömen "dual" seçenek |

**Seçerken bak:** (1) Program **staatliche Anerkennung** veriyor mu? (2) Pratik dönem/Anerkennungsjahr nasıl kurgulanmış? (3) Şehir ve staj kurumu ağı. Bir okulun "adı"ndan çok bu üç madde belirleyicidir.

## 5. Başvuru: uni-assist ve FH portalları

Uluslararası (AB-dışı) öğrenciler başvuruyu çoğunlukla **uni-assist** üzerinden yapar; bazı FH/HAW'ların kendi başvuru portalı da olabilir. Süreç kabaca şöyle işler: lise/lisans denklik kontrolü, **Almanca dil belgesi (genelde C1 — TestDaF/DSH/telc/Goethe)**, transkript, motivasyon mektubu ve bazı programlarda ön-staj (Vorpraktikum) veya kısa bir uygunluk sınavı/mülakat.

Sosyal hizmet programlarında **Numerus Clausus (NC)** yani kontenjan sınırı sık görülür; talep yüksek olduğu için kabul not ortalamasına göre yapılabilir. NC her yıl ve her okulda değişir, sabit bir eşik yoktur. Bir de dikkat: Türkiye lisesinden gelen birçok öğrenci doğrudan lisansa başvuramaz ve önce **Studienkolleg** ya da bir yıl üniversite okumuş olma şartıyla karşılaşabilir. **Başvuru tarihlerini, tam belge listesini ve NC durumunu her zaman okulun resmî sayfasından ve uni-assist'ten doğrula.**

## 6. Ücret, yaşam ve yüksek talep

Para tarafı Almanya'nın en büyük avantajlarından: **kamu FH/HAW'ları esasen ücretsizdir**, sadece dönem katkı payı ödersin (2025/2026 itibarıyla yaklaşık **150–350€/dönem**, çoğu zaman semester ticket dahil). Tek büyük istisna **Baden-Württemberg**: AB-dışı öğrencilerden dönem başına yaklaşık **1.500€** alınır.

Vize için **bloke hesap (Sperrkonto)** gerekir; 2026 itibarıyla gereken tutar yaklaşık **ayda 992€ = yılda 11.904€** (yaklaşık; güncelini mutlaka doğrula). Yaşam maliyeti şehre göre ciddi değişir — Münih/Frankfurt pahalı, küçük şehirler daha uygun.

Ama asıl büyük resim **talep**: sosyal hizmet Almanya'da yıllardır **darboğaz meslek** (Fachkräftemangel). Gençlik yardımı (**Jugendamt/Jugendhilfe**), göç ve mülteci çalışması, yaşlı ve engelli desteği, aile danışmanlığı, okul sosyal hizmeti, bağımlılık ve evsizlik alanlarında sürekli eleman aranıyor. İşverenler ise çok sağlam: **Caritas, Diakonie, AWO, Paritätischer** gibi refah kuruluşları, belediyeler/Jugendamt, okullar ve hastaneler. Maaş tarafı çoğunlukla **TVöD-SuE** (Sozial- und Erziehungsdienst) tarifesine bağlıdır — giriş yaklaşık **42–48k€/yıl brüt** civarı, stabil ve düzenli artışlı (yaklaşık; doğrula). Alanların ve maaşın ayrıntısı için [Almanya'da sosyal hizmet uzmanı olarak çalışmak yazısına](/tr/blog/working-as-a-social-worker-in-germany-fields-salary-and-reality) bak.

## 7. Sonuç ve dürüst tavsiye

Almanya'da sosyal hizmet okumak, doğru profildeki öğrenci için ülkedeki **en stabil ve anlamlı** rotalardan biridir: talep çok yüksek, iş güvencesi iyi, meslek düzenli, maaş makul ve yol kalıcı oturuma açılıyor. Ama bunun tek bir gerçek koşulu var ve onu tekrarlamaktan çekinmiyorum: **Almanca C1 pazarlık konusu değildir.** Bu alan baştan sona insanla ve Alman sosyal hukukuyla (SGB) yürüdüğü için, dil olmadan ne okuman ne de çalışman gerçekçi.

Birkaç dürüst not: (1) Almancan güçlüyse veya güçlendirmeye kararlıysan, bu alan senin için gerçekten mükemmel bir yatırım. (2) Program seçerken tek kriterin **staatliche Anerkennung + pratik dönem** olsun. (3) **Zaten Türkiye'de sosyal hizmet/sosyal pedagoji okuduysan**, sıfırdan okumak yerine diplomanı tanıtma yolu daha mantıklı olabilir — bunu [yurtdışı sosyal hizmet diplomanı Almanya'da tanıtmak (Anerkennung) yazısında](/tr/blog/getting-your-foreign-social-work-qualification-recognized-in-germany-anerkennung) ayrıntılı anlattım. (4) "Bu alan gerçekten bana değer mi?" diye tereddüt ediyorsan, artı-eksileri tarttığım [Almanya'da sosyal hizmet okumaya değer mi yazısı](/tr/blog/is-studying-social-work-in-germany-worth-it-honest-reality) tam sana göre. (5) Toplumla ilgilenip ama daha akademik/araştırma yönü ağır bir alan istiyorsan, komşu bir seçenek olarak [Almanya'da sosyoloji ve sosyal bilimler okumak yazısına](/tr/blog/studying-sociology-and-social-sciences-in-germany-as-a-foreigner) da göz at.

*Bu yazı 2026 yılı için genel bir rehberdir ve bireysel danışmanlığın yerini tutmaz. Program içerikleri, staatliche Anerkennung koşulları, Anerkennungsjahr kuralları, ücretler, NC eşikleri, dil şartları, Sperrkonto tutarı, TVöD maaşları ve başvuru tarihleri zamanla değişir; karar vermeden önce her rakamı ve şartı okulun resmî sayfasından, ilgili eyaletin tanınma makamından, DAAD'dan ve uni-assist'ten doğrula.*
MD;

        $deBody = <<<'MD'
Soziale Arbeit (nah verwandt mit der **Sozialpädagogik**) ist in Deutschland ein besonderes Feld: einerseits ein **reglementierter Beruf**, andererseits seit Jahren ein spürbarer **Fachkräftemangel**. Das heißt: Mit der richtigen Sprache ist es ein fast sicherer, sinnvoller Weg, der zu einem dauerhaften Aufenthalt führen kann. Aber dieser Weg hat einen Preis – und dieser Preis besteht fast vollständig aus Sprache. Dieser Beitrag zeigt dir das Feld ohne Beschönigung: was du studierst, in welcher Struktur, in welcher Sprache, wie du dich bewirbst und welche Erwartungen ehrlich sind. Sagen wir es gleich klar: **Soziale Arbeit in Deutschland ist für internationale Studierende mit starkem Deutsch ein exzellenter, stabiler Weg – ohne Deutsch ist es ein wirklich schwieriges Feld.**

## 1. Was das Feld ist und warum die staatliche Anerkennung entscheidend ist

Soziale Arbeit ist ein anwendungsorientiertes Feld: Du arbeitest mit Menschen, Familien und Gruppen und unterstützt ihre Teilhabe am gesellschaftlichen Leben. Der Kern in Deutschland aber ist: Es ist ein **reglementierter Beruf.** Um als anerkannte:r Sozialarbeiter:in bei Ämtern und Trägern zu arbeiten, reicht das Diplom allein nicht; du brauchst die **staatliche Anerkennung.** Mit ihr lautet dein Titel offiziell **"Staatlich anerkannte:r Sozialarbeiter:in".**

Warum ist das wichtig? Weil das **Jugendamt**, Kommunen, Krankenhäuser und große Wohlfahrtsverbände diese Anerkennung für die meisten Stellen voraussetzen. Bei der Wahl eines Bachelors in Sozialer Arbeit ist deine erste Frage deshalb: "Führt dieses Programm beim Abschluss zur **staatlichen Anerkennung**?" Die gute Nachricht: Die große Mehrheit der akkreditierten FH/HAW-Programme schließt das ein.

## 2. FH/HAW-Struktur und Anerkennungsjahr

In Deutschland wird Soziale Arbeit überwiegend **nicht an klassischen Universitäten**, sondern an **Fachhochschulen (FH) / Hochschulen für Angewandte Wissenschaften (HAW)** gelehrt. Das ist bewusst so: Weil das Feld stärker auf die Praxis als auf reine Theorie ausgerichtet ist, passt die anwendungsnahe Struktur der FH/HAW genau. Der Unterschied zwischen FH und Uni ist ohnehin kein "Qualitäts"-, sondern ein **Ausrichtungsunterschied**; wer das noch verwirrend findet, dem hilft der Beitrag [wie Prestige und Rankings in Deutschland funktionieren](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de).

Das prägendste Element des Programms ist die **Praxisphase.** Viele FH/HAW-Programme enthalten entweder ein langes verpflichtendes Praxissemester im Curriculum oder verlangen nach dem Abschluss ein **Anerkennungsjahr** – ein betreutes, (teil-)vergütetes, in Vollzeit absolviertes Praxisjahr bei einem Träger. Manche Programme integrieren dieses Jahr **"dual"** in das Diplom, andere führen es separat. Am Ende wird die staatliche Anerkennung meist erst durch **Theorie + betreute Praxis** erteilt.

| Element | Bedeutung |
|---|---|
| **Hochschultyp** | Meist FH / HAW (anwendungsnah), auch an einigen Unis |
| **Abschluss** | Bachelor of Arts, Soziale Arbeit (meist 6–7 Semester) |
| **Praxis** | Praxissemester und/oder **Anerkennungsjahr** |
| **Ergebnis** | Diplom **+ staatliche Anerkennung** (das eigentlich Wertvolle) |
| **Unterrichtssprache** | Fast vollständig **Deutsch (C1)** |

## 3. Die Sprachrealität: Deutsch C1 ist Pflicht, englische Programme sind selten

Das ist der ehrlichste und entscheidendste Abschnitt. **Soziale Arbeit ist in Deutschland ein sprachintensives Feld, und die Bachelorprogramme sind fast vollständig auf Deutsch – meist wird C1 verlangt.** Der Unterschied zu Fächern wie dem Ingenieurwesen ist klar: Die Arbeit der Sozialen Arbeit besteht direkt aus dem **Gespräch mit Menschen, mit Klient:innen.** Ein Gespräch beim Jugendamt, eine Familienberatung, einen Asylantrag oder einen Schulkonflikt kannst du nicht auf Englisch führen. Dazu kommt das **deutsche Sozialrecht (SGB – Sozialgesetzbuch);** ohne dieses System zu verstehen, ist die Arbeit im Feld unmöglich – und diese Rechtsgrundlage ist durchgängig auf Deutsch.

Deshalb sind **englischsprachige Bachelor in Sozialer Arbeit in Deutschland wirklich selten.** Die seltenen englischen Programme liegen meist auf Masterebene mit internationalem/sozialpolitischem Fokus und machen dich nicht automatisch zur praktizierenden "Sozialarbeiter:in" – denn staatliche Anerkennung und Klientenarbeit verlangen Deutsch. Ehrliches Fazit: **Wenn du dieses Feld ernst nimmst, musst du Deutsch nicht als "Zusatz", sondern als den Beruf selbst begreifen.** Ohne Deutsch ist das Feld nicht verschlossen, aber eine ernsthafte Sprachinvestition kommt zuerst.

## 4. Anerkannte Hochschulen (ungefähr, 2026)

In Deutschland gibt es keine einzelne Rangliste der "besten Hochschule für Soziale Arbeit"; entscheidend ist, dass das Programm **akkreditiert** ist und die **staatliche Anerkennung** ermöglicht. Die folgende Tabelle nennt einige angesehene FH/HAW-Beispiele (keine feste Rangliste):

| Hochschule | Hinweis |
|---|---|
| **Alice Salomon Hochschule Berlin (ASH)** | Eine der traditionsreichsten Adressen der Sozialen Arbeit |
| **Katholische / Evangelische Hochschulen** (Freiburg, Berlin, Nürnberg usw.) | Kirchlich getragen, starkes Netzwerk (Caritas/Diakonie) |
| **Hochschule München / HAW Hamburg / TH Köln** | Große Groß­stadt-HAWs, breites Programm- und Praxisnetz |
| **Frankfurt UAS, Hochschule Düsseldorf, Hochschule Esslingen** | Etablierte, akkreditierte Programme |
| **Duale Hochschule (z. B. BW)** | "Duale" Option, die die Praxis von Anfang an einbaut |

**Achte bei der Wahl auf:** (1) Führt das Programm zur **staatlichen Anerkennung**? (2) Wie ist die Praxisphase / das Anerkennungsjahr organisiert? (3) Stadt und Netzwerk an Praxisträgern. Diese drei Punkte zählen mehr als der "Name" einer Hochschule.

## 5. Bewerbung: uni-assist und FH-Portale

Internationale (Nicht-EU-)Studierende bewerben sich meist über **uni-assist**; manche FH/HAW haben ein eigenes Portal. Der Ablauf grob: Anerkennungsprüfung der Vorbildung, **Deutschnachweis (meist C1 – TestDaF/DSH/telc/Goethe)**, Transcript, Motivationsschreiben und bei manchen Programmen ein Vorpraktikum oder ein kurzer Eignungstest/ein Gespräch.

Bei Programmen der Sozialen Arbeit ist ein **Numerus Clausus (NC)** häufig; wegen der hohen Nachfrage kann die Zulassung nach dem Notendurchschnitt erfolgen. Der NC ändert sich jedes Jahr und je Hochschule; einen festen Schwellenwert gibt es nicht. Achtung außerdem: Viele Studierende mit türkischem Schulabschluss können sich nicht direkt bewerben und müssen ggf. zuerst ein **Studienkolleg** besuchen oder ein Jahr Studium nachweisen. **Prüfe Bewerbungsfristen, die genaue Unterlagenliste und den NC-Status immer auf der offiziellen Seite der Hochschule und bei uni-assist.**

## 6. Kosten, Leben und hohe Nachfrage

Die finanzielle Seite ist einer der größten Vorteile Deutschlands: **staatliche FH/HAW sind grundsätzlich kostenlos**, du zahlst nur den Semesterbeitrag (Stand 2025/2026 etwa **150–350 €/Semester**, oft inkl. Semesterticket). Die große Ausnahme ist **Baden-Württemberg**: Nicht-EU-Studierende zahlen rund **1.500 €/Semester**.

Für das Visum brauchst du ein **Sperrkonto**; 2026 liegt der Betrag bei etwa **992 €/Monat = 11.904 €/Jahr** (ungefähr; aktuellen Wert prüfen). Die Lebenshaltungskosten hängen stark von der Stadt ab – München/Frankfurt teuer, kleinere Städte günstiger.

Das große Bild aber ist die **Nachfrage:** Soziale Arbeit ist in Deutschland seit Jahren ein **Engpassberuf** (Fachkräftemangel). In der Jugendhilfe (**Jugendamt**), Migrations- und Flüchtlingsarbeit, Alten- und Behindertenhilfe, Familienberatung, Schulsozialarbeit, Suchthilfe und Wohnungslosenhilfe werden ständig Fachkräfte gesucht. Die Arbeitgeber sind solide: Wohlfahrtsverbände wie **Caritas, Diakonie, AWO, Paritätischer**, Kommunen/Jugendämter, Schulen und Krankenhäuser. Das Gehalt richtet sich meist nach dem Tarif **TVöD-SuE** (Sozial- und Erziehungsdienst) – Einstieg etwa **42–48k €/Jahr brutto**, stabil und mit regelmäßigen Steigerungen (ungefähr; prüfen). Details zu Feldern und Gehalt findest du im Beitrag [als Sozialarbeiter:in in Deutschland arbeiten](/de/blog/working-as-a-social-worker-in-germany-fields-salary-and-reality-de).

## 7. Fazit und ehrlicher Rat

Soziale Arbeit in Deutschland zu studieren ist für das richtige Profil einer der **stabilsten und sinnvollsten** Wege im Land: sehr hohe Nachfrage, gute Arbeitsplatzsicherheit, ein geregelter Beruf, ein solides Gehalt und ein Weg, der zum dauerhaften Aufenthalt führt. Aber es gibt eine reale Bedingung, und ich wiederhole sie gern: **Deutsch C1 ist nicht verhandelbar.** Weil dieses Feld von Anfang bis Ende mit Menschen und dem deutschen Sozialrecht (SGB) läuft, sind ohne Sprache weder Studium noch Arbeit realistisch.

Ein paar ehrliche Hinweise: (1) Wenn dein Deutsch stark ist oder du entschlossen bist, es stark zu machen, ist dieses Feld eine wirklich exzellente Investition. (2) Lass dein einziges Kriterium bei der Programmwahl **staatliche Anerkennung + Praxisphase** sein. (3) Wenn du **im Ausland bereits Soziale Arbeit/Sozialpädagogik studiert hast**, ist statt eines Neustarts womöglich die Anerkennung deines Abschlusses sinnvoller – das erkläre ich ausführlich im Beitrag [deinen ausländischen Abschluss in Sozialer Arbeit in Deutschland anerkennen lassen (Anerkennung)](/de/blog/getting-your-foreign-social-work-qualification-recognized-in-germany-anerkennung-de). (4) Wenn du zögerst, ob sich das Feld für dich lohnt, ist der Beitrag [lohnt es sich, Soziale Arbeit in Deutschland zu studieren](/de/blog/is-studying-social-work-in-germany-worth-it-honest-reality-de) genau richtig. (5) Interessierst du dich für Gesellschaft, willst aber ein akademischer/forschungsnäheres Fach, wirf als Nachbaroption einen Blick auf [Soziologie und Sozialwissenschaften in Deutschland studieren](/de/blog/studying-sociology-and-social-sciences-in-germany-as-a-foreigner-de).

*Dieser Beitrag ist ein allgemeiner Leitfaden für das Jahr 2026 und ersetzt keine individuelle Beratung. Programminhalte, Bedingungen der staatlichen Anerkennung, Regeln des Anerkennungsjahres, Gebühren, NC-Werte, Sprachanforderungen, Sperrkonto-Betrag, TVöD-Gehälter und Bewerbungsfristen ändern sich mit der Zeit; prüfe vor jeder Entscheidung jede Zahl und Anforderung auf der offiziellen Seite der Hochschule, bei der zuständigen Anerkennungsstelle des Landes, beim DAAD und bei uni-assist.*
MD;

        $enBody = <<<'MD'
Social work (in German **Soziale Arbeit**, with its close relative **Sozialpädagogik**) is a very particular field in Germany: on one side it is a **regulated profession**, on the other there has been a serious staff shortage (**Fachkräftemangel**) for years. In other words, arriving with the right language makes it an almost guaranteed, meaningful path that can open the way to permanent residence. But that path has a price, and the price is almost entirely about language. This post shows you the field without gloss: what you study, in what structure, in which language, how you apply, and what your honest expectations should be. Let's be clear from the start: **social work in Germany is an excellent, stable route for an international student with strong German — and a genuinely hard field for someone without it.**

## 1. What the field is, and why staatliche Anerkennung is critical

Social work is an applied field: you work with people, families and groups and support their participation in social life. But in Germany the core is this: it is a **regulated profession.** To work as a recognised social worker for public offices and providers, the degree alone is not enough; you need **staatliche Anerkennung** (state recognition). With it, your title officially becomes **"Staatlich anerkannte:r Sozialarbeiter:in".**

Why does this matter? Because the **Jugendamt** (youth welfare office), municipalities, hospitals and the large welfare organisations require this recognition for most positions. So when choosing a social-work bachelor in Germany, your first question should be: "Does this programme lead to **staatliche Anerkennung** on graduation?" The good news: the large majority of accredited FH/HAW programmes include it.

## 2. The FH/HAW structure and the Anerkennungsjahr (recognition year)

In Germany, social work is taught mostly **not at classic universities**, but at universities of applied sciences — **Fachhochschule (FH) / Hochschule für Angewandte Wissenschaften (HAW)**. This is deliberate: because the field is oriented toward practice rather than pure theory, the applied structure of the FH/HAW fits perfectly. In any case, the difference between an FH and a Uni is not one of "quality" but of **orientation**; if that still confuses you, the post on [how prestige and rankings work in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en) clears it up.

The most distinctive part of the programme is the **practice phase.** Many FH/HAW programmes either embed a long compulsory practical semester (Praxissemester) in the curriculum or require an **Anerkennungsjahr** (recognition year) after graduation — a supervised, (partly) paid, full-time practical year at a provider. Some programmes integrate this year into the degree in a **"dual"** form; others run it separately. In the end, staatliche Anerkennung is usually granted only when **theory + supervised practice** come together.

| Element | Meaning |
|---|---|
| **Institution type** | Usually FH / HAW (applied), also at some universities |
| **Degree** | Bachelor of Arts, Social Work (usually 6–7 semesters) |
| **Practice** | Praxissemester and/or **Anerkennungsjahr** |
| **Outcome** | Degree **+ staatliche Anerkennung** (the truly valuable part) |
| **Language of instruction** | Almost entirely **German (C1)** |

## 3. The language reality: German C1 is mandatory, English programmes are rare

This is the most honest and most decisive section. **Social work is a language-intensive field in Germany, and the bachelor programmes are almost entirely in German — C1 is usually required.** The contrast with fields like engineering is stark: the work of social work is directly about **talking with people, with clients.** You cannot run a Jugendamt interview, a family counselling session, an asylum application or a school conflict in English. On top of that sits **German social law (SGB — Sozialgesetzbuch);** working in the field without understanding this system is impossible — and that legislation is entirely in German.

That is why **English-taught social-work bachelors are genuinely rare in Germany.** The rare English programmes you do find are usually master's level with an international/social-policy focus, and they do not automatically make you a practising "Sozialarbeiter:in" — because staatliche Anerkennung and client work require German. Honest summary: **if you take this field seriously, you must see German not as an "add-on" but as the profession itself.** Without German the field is not closed to you, but a serious language investment comes first.

## 4. Recognised schools (approximate, 2026)

Germany has no single ranking of the "best social-work school"; what matters is that the programme is **accredited** and leads to **staatliche Anerkennung.** The table below lists some respected FH/HAW examples (not a fixed ranking):

| School | Note |
|---|---|
| **Alice Salomon Hochschule Berlin (ASH)** | One of the most established addresses for social work in Germany |
| **Catholic / Protestant universities** (Freiburg, Berlin, Nürnberg, etc.) | Church-based, strong networks (Caritas/Diakonie links) |
| **Hochschule München / HAW Hamburg / TH Köln** | Large city HAWs, broad programme and placement networks |
| **Frankfurt UAS, Hochschule Düsseldorf, Hochschule Esslingen** | Established, accredited social-work programmes |
| **Duale Hochschule (e.g. in BW)** | A "dual" option that builds practice in from the start |

**When choosing, check:** (1) Does the programme lead to **staatliche Anerkennung**? (2) How is the practice phase / Anerkennungsjahr organised? (3) The city and the network of placement providers. These three points matter more than a school's "name."

## 5. Applying: uni-assist and FH portals

International (non-EU) students mostly apply through **uni-assist**; some FH/HAW have their own portal. The process, roughly: recognition check of your prior education, a **German language certificate (usually C1 — TestDaF/DSH/telc/Goethe)**, transcript, a motivation letter, and in some programmes a pre-placement (Vorpraktikum) or a short aptitude test/interview.

A **Numerus Clausus (NC)** is common in social-work programmes; because demand is high, admission may be based on grade average. The NC changes every year and by school; there is no fixed threshold. Note too: many students with a Turkish school-leaving certificate cannot apply directly and may first face the requirement of a **Studienkolleg** or proof of a year of university study. **Always verify application deadlines, the exact document list and the NC status on the school's official page and with uni-assist.**

## 6. Fees, living costs and high demand

The financial side is one of Germany's biggest advantages: **public FH/HAW are essentially free**, you only pay the semester contribution (as of 2025/2026 roughly **€150–350/semester**, often including a semester ticket). The one big exception is **Baden-Württemberg**: non-EU students pay about **€1,500/semester**.

For the visa you need a **blocked account (Sperrkonto)**; as of 2026 the required amount is about **€992/month = €11,904/year** (approximate; verify the current figure). Living costs vary sharply by city — Munich/Frankfurt expensive, smaller towns cheaper.

But the big picture is **demand:** social work has been a **bottleneck profession** (Fachkräftemangel) in Germany for years. In youth welfare (**Jugendamt/Jugendhilfe**), migration and refugee work, elderly and disability support, family counselling, school social work, addiction and homelessness services, staff are constantly sought. The employers are solid: welfare organisations such as **Caritas, Diakonie, AWO, Paritätischer**, municipalities/Jugendämter, schools and hospitals. Pay mostly follows the **TVöD-SuE** (Sozial- und Erziehungsdienst) scale — entry around **€42–48k/year gross**, stable and with regular increases (approximate; verify). For details on fields and salary, see the post on [working as a social worker in Germany](/en/blog/working-as-a-social-worker-in-germany-fields-salary-and-reality-en).

## 7. Conclusion and honest advice

Studying social work in Germany is, for the right profile, one of the **most stable and meaningful** routes in the country: very high demand, good job security, a regulated profession, a solid salary, and a path that opens toward permanent residence. But there is one real condition, and I am happy to repeat it: **German C1 is not negotiable.** Because this field runs from start to finish on people and on German social law (SGB), without the language neither studying nor working is realistic.

A few honest notes: (1) If your German is strong or you are determined to make it strong, this field is a genuinely excellent investment. (2) Let your only criterion in choosing a programme be **staatliche Anerkennung + a practice phase.** (3) If you **already studied social work/social pedagogy abroad**, having your degree recognised may make more sense than starting from scratch — I explain this in detail in the post on [getting your foreign social-work qualification recognised in Germany (Anerkennung)](/en/blog/getting-your-foreign-social-work-qualification-recognized-in-germany-anerkennung-en). (4) If you are hesitating over whether the field is worth it for you, the post on [is studying social work in Germany worth it](/en/blog/is-studying-social-work-in-germany-worth-it-honest-reality-en) is exactly for you. (5) If you care about society but want a more academic/research-oriented field, look at the neighbouring option of [studying sociology and social sciences in Germany](/en/blog/studying-sociology-and-social-sciences-in-germany-as-a-foreigner-en).

*This post is a general guide for the year 2026 and does not replace individual advice. Programme content, the conditions for staatliche Anerkennung, Anerkennungsjahr rules, fees, NC thresholds, language requirements, the Sperrkonto amount, TVöD salaries and application deadlines change over time; before making any decision, verify every figure and requirement on the school's official page, with the relevant state recognition authority, with the DAAD, and with uni-assist.*
MD;

        $variants = [
            'tr' => ['slug'=>'studying-social-work-soziale-arbeit-in-germany-as-a-foreigner',    'title'=>'Almanya\'da Sosyal Hizmet (Soziale Arbeit) Okumak: Rehber (2026)', 'excerpt'=>'Almanya\'da Sosyal Hizmet (Soziale Arbeit) okumak: düzenlenmiş meslek + staatliche Anerkennung, FH/HAW yapısı ve Anerkennungsjahr, Almanca C1 şartı ve İngilizce programın nadir gerçeği, tanınan okullar, uni-assist başvurusu, ücret & yüksek talep (Fachkräftemangel) ve dürüst tavsiye (2026).', 'meta_title'=>'Almanya\'da Sosyal Hizmet (Soziale Arbeit) Okumak 2026', 'meta_description'=>'Almanya\'da Soziale Arbeit: staatliche Anerkennung, FH/HAW + Anerkennungsjahr, Almanca C1 şartı, tanınan okullar, uni-assist, ücret & yüksek talep, dürüst tavsiye (2026).', 'body'=>$trBody],
            'de' => ['slug'=>'studying-social-work-soziale-arbeit-in-germany-as-a-foreigner-de', 'title'=>'Soziale Arbeit in Deutschland studieren: Leitfaden (2026)', 'excerpt'=>'Soziale Arbeit in Deutschland studieren: reglementierter Beruf + staatliche Anerkennung, FH/HAW-Struktur und Anerkennungsjahr, Deutsch C1 als Pflicht und die Seltenheit englischer Programme, anerkannte Hochschulen, Bewerbung über uni-assist, Kosten & hohe Nachfrage (Fachkräftemangel) und ehrlicher Rat (2026).', 'meta_title'=>'Soziale Arbeit in Deutschland studieren 2026', 'meta_description'=>'Soziale Arbeit in Deutschland: staatliche Anerkennung, FH/HAW + Anerkennungsjahr, Deutsch C1, anerkannte Hochschulen, uni-assist, Kosten & hohe Nachfrage, ehrlicher Rat (2026).', 'body'=>$deBody],
            'en' => ['slug'=>'studying-social-work-soziale-arbeit-in-germany-as-a-foreigner-en', 'title'=>'Studying Social Work (Soziale Arbeit) in Germany (2026)', 'excerpt'=>'Studying social work (Soziale Arbeit) in Germany: a regulated profession + staatliche Anerkennung, the FH/HAW structure and Anerkennungsjahr, German C1 as mandatory and the rarity of English programmes, recognised schools, applying via uni-assist, fees & high demand (Fachkräftemangel), and honest advice (2026).', 'meta_title'=>'Studying Social Work (Soziale Arbeit) in Germany 2026', 'meta_description'=>'Social work in Germany: staatliche Anerkennung, FH/HAW + Anerkennungsjahr, German C1 requirement, recognised schools, uni-assist, fees & high demand, honest advice (2026).', 'body'=>$enBody],
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
            'studying-social-work-soziale-arbeit-in-germany-as-a-foreigner',
            'studying-social-work-soziale-arbeit-in-germany-as-a-foreigner-de',
            'studying-social-work-soziale-arbeit-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};

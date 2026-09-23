<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN) — Almanya'nın en ucuz 20 öğrenci şehri, GERÇEK aylık maliyetle.
 *
 * Veri ve doğrulama (Eylül 2026):
 *   - Kira: MLP Studentenwohnreport 2025, sıcak kira, 30 m² (DB'de student_rent_warm30).
 *     Yıllık değişim oranı da aynı kaynaktan (student_rent_index).
 *   - Semesterbeitrag: WS 2026/27, her üniversite için tek tek toplandı. 7 rakam
 *     üniversitenin kendi sayfasından (Chemnitz, Bochum, Magdeburg, Greifswald,
 *     Leipzig, Bielefeld, Ulm), 12'si üniversite sayfasına atıf yapan kaynaklardan,
 *     1'i (RWTH Aachen) TAHMİN — 2026/27 rakamı yayınlanmadığı için WS24/25'teki
 *     304,47 €'ya D-Ticket artışı eklendi. Yazıda dipnotla işaretli.
 *   - Deutschlandticket: Ocak 2026'dan beri 63 €/ay (2025'te 58 €).
 *     Deutschlandsemesterticket: WS 2026/27'den itibaren 226,80 €/dönem (37,80 €/ay).
 *   - Ulaşım kartı Baden-Württemberg (Mannheim, Tübingen, Ulm) ve Greifswald'da
 *     Semesterbeitrag'a DAHİL DEĞİL → o şehirlere aylık 63 € eklendi.
 *
 * Formül: aylık = kira + (Semesterbeitrag ÷ 6) + (kart dahil değilse 63 €)
 * Yazar: Halil Yaprakli. Kategori: yasam (yoksa para/kariyer).
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c7e15a93-4d82-4b60-9a17-3f5e28d1c604';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'yasam')->value('id')
            ?? DB::table('categories')->where('slug', 'para')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almanya'nın en ucuz öğrenci şehirleri" listeleri hep aynı şeyi yapar: kiraları sıralar, biter. Oysa kira, bir öğrencinin şehre bağlı sabit giderinin tamamı değil. Yanına **Semesterbeitrag** gelir — ve asıl mesele, o ücretin içinde **ulaşım kartı olup olmadığıdır**.

Bu ayrımı hesaba katınca sıralama değişiyor. Bazı şehirler dört basamak geriliyor.

Aşağıdaki tablo, 20 şehir için üç kalemi birleştiriyor: kira, Semesterbeitrag'ın aylık karşılığı, ve ulaşım kartı dahil değilse Deutschlandticket bedeli.

## Hesap nasıl yapıldı

**Aylık şehir maliyeti = kira + (Semesterbeitrag ÷ 6) + (kart dahil değilse 63 €)**

- **Kira:** MLP Studentenwohnreport 2025, 30 m² için sıcak kira (yan giderler dahil).
- **Semesterbeitrag:** 2026/27 kış dönemi, her üniversite için ayrı ayrı toplandı. Dönemlik ücret 6'ya bölündü.
- **Ulaşım:** Deutschlandsemesterticket'e geçen üniversitelerde ulaşım zaten Semesterbeitrag'ın içinde (dönemlik 226,80 €). Geçmeyenlerde öğrenci normal Deutschlandticket'i alıyor: **Ocak 2026'dan beri 63 €/ay**.

Yemek, sağlık sigortası, telefon gibi kalemler tabloda yok — çünkü bunlar şehre göre neredeyse hiç değişmiyor ve sıralamayı etkilemiyor. Onları yazının sonunda ayrıca ekliyoruz.

## En ucuz 20 şehir — gerçek aylık maliyet

| # | Şehir | Kira | Semester/ay | Ulaşım | **Toplam** | Kira sırası |
|---|---|---|---|---|---|---|
| 1 | Chemnitz | 296 € | 57 € | dahil | **353 €** | 1 |
| 2 | Magdeburg | 374 € | 55 € | dahil | **429 €** | 3 ▲ |
| 3 | Bochum | 368 € | 63 € | dahil | **431 €** | 2 ▼ |
| 4 | Greifswald | 402 € | 21 € | +63 € | **486 €** | 4 |
| 5 | Leipzig | 442 € | 55 € | dahil | **497 €** | 5 |
| 6 | Bielefeld | 443 € | 59 € | dahil | **502 €** | 6 |
| 7 | Jena | 466 € | 56 € | dahil | **522 €** | 8 ▲ |
| 8 | Kiel | 460 € | 67 € | dahil | **527 €** | 7 ▼ |
| 9 | Hannover | 477 € | 76 € | dahil | **553 €** | 9 |
| 10 | Rostock | 496 € | 59 € | dahil | **555 €** | 11 ▲ |
| 11 | Dresden | 499 € | 60 € | dahil | **559 €** | 12 ▲ |
| 12 | Saarbrücken | 496 € | 69 € | dahil | **565 €** | 10 ▼▼ |
| 13 | Trier | 510 € | 59 € | dahil | **569 €** | 16 ▲▲▲ |
| 14 | Bremen | 504 € | 74 € | dahil | **578 €** | 14 |
| 15 | Aachen\* | 521 € | 59 € | dahil | **580 €** | 17 ▲▲ |
| 16 | Göttingen | 508 € | 81 € | dahil | **589 €** | 15 ▼ |
| 17 | Mannheim | 502 € | 32 € | +63 € | **597 €** | 13 ▼▼▼▼ |
| 18 | Oldenburg | 526 € | 76 € | dahil | **602 €** | 18 |
| 19 | Tübingen | 526 € | 32 € | +63 € | **621 €** | 19 |
| 20 | Ulm | 530 € | 32 € | +63 € | **625 €** | 20 |

\* RWTH Aachen 2026/27 Semesterbeitrag'ını henüz yayınlamadı. Buradaki rakam, 2024/25 dönemindeki 304,47 €'ya Deutschlandsemesterticket zammı eklenerek tahmin edildi. Kesin tutar için üniversitenin kendi sayfasına bak.

Karşılaştırma için en pahalı üç şehrin sadece kirası: **Münih 837 €**, Frankfurt 734 €, Köln 688 €.

## Dört bulgu

### 1. Düşük Semesterbeitrag ucuzluk demek değil

Tabloda Semesterbeitrag'ı en düşük şehirler Ulm (32 €/ay), Tübingen (32 €) ve Mannheim (32 €). Kulağa harika geliyor — ta ki nedenini öğrenene kadar: **Baden-Württemberg'de ulaşım kartı Semesterbeitrag'a dahil değil.** Öğrenci Deutschlandticket'i cebinden alıyor, aylık 63 €.

Sonuç: Mannheim kira sıralamasında 13. sıradayken gerçek maliyette **17. sıraya düşüyor**. Listenin en sert düşüşü.

Bunun tersi de var: Göttingen'in Semesterbeitrag'ı Almanya'nın en yükseklerinden (ayda 81 €), ama içinde tüm Almanya'da geçerli ulaşım kartı var. Yani o 81 €'nun 38 €'su zaten ulaşım.

**Çıkarılacak ders:** Semesterbeitrag rakamını tek başına karşılaştırma. Önce sor: *ulaşım kartı içinde mi?*

### 2. Chemnitz ile ikinci sıra arasında uçurum var

Chemnitz 353 €. İkinci sıradaki Magdeburg 429 €. Aradaki fark **76 €** — yani listenin 2. ve 10. sırası arasındaki farktan daha büyük.

Münih'le karşılaştırınca tablo daha da netleşiyor: sadece kira farkı aylık 541 €. Üç yıllık bir lisansta bu **yaklaşık 19.500 €** eder. Aynı diploma, aynı ülke.

### 3. Bugün ucuz olan, mezun olurken ucuz olmayabilir

Kira rakamının yanında bir de artış hızı var. Bu listedeki şehirlerin son bir yıldaki kira artışları birbirinden çok farklı:

| Hızla pahalanıyor | Durağan |
|---|---|
| Rostock **+%9,1** | Ulm **−%3,0** |
| Saarbrücken **+%7,7** | Trier **+%0,1** |
| Kiel **+%7,1** | Chemnitz **+%1,2** |
| Oldenburg **+%6,9** | Aachen **+%1,6** |

Bu oranlar üç yıl sürdürürse Rostock'ta kira 496 €'dan ~643 €'ya çıkar; Chemnitz'de 296 €'dan ~307 €'ya. Yani bugün 7 sıra olan fark, mezuniyette çok daha büyük olur.

**Lisans mı master mı okuyacağın bu tabloyu okuma biçimini değiştirir.** Bir yıllık master için bugünkü rakam yeterli; dört yıllık bir yol için artış hızına bakmadan karar verme.

### 4. Greifswald paradoksu: kendi hayat tarzın sıralamayı değiştirir

Greifswald'ın Semesterbeitrag'ı Almanya'nın en düşüğü: dönemde 127 €, yani ayda 21 €. Ama ulaşım kartı dahil değil, o yüzden tabloda üstüne 63 € ekledik ve şehir 4. sırada kaldı.

Şu var ki Greifswald küçük ve düz bir sahil şehri — öğrencilerin çoğu bisiklet kullanıyor. Deutschlandticket'i hiç almayan biri için gerçek maliyet **423 €**, yani şehir **2. sıraya** çıkıyor.

Aynı hesap tersine de işler: Berlin gibi büyük bir şehirde ulaşımsız yaşamak mümkün değil.

**Bu tablo bir sıralama değil, bir başlangıç noktası.** Kendi satırını kendin hesapla: bisiklete biniyorsan ulaşım sütununu sil.

## Tabloda olmayanlar

Şehre göre değişmediği için tabloya koymadığımız kalemler:

- **Sağlık sigortası:** öğrenci tarifesi ülke genelinde aynı, aylık ~130–140 €
- **Yemek:** ~200–300 € (Mensa'da öğle yemeği 3–5 €)
- **Telefon/internet:** ~20–35 €
- **Kişisel harcama, kitap, eğlence:** ~100–150 €

Yani **gerçek toplam aylık gider**, tablodaki rakama kabaca **450–600 € eklenerek** bulunur. Chemnitz için ~800–950 €, Ulm için ~1.075–1.225 €.

Bu rakamlar bloke hesap açısından da anlamlı: vize için gösterilmesi gereken tutar Almanya genelinde sabit, ama parayı nerede harcayacağın bu tabloya bağlı. Kendi bütçeni çıkarmak için [yaşam maliyeti hesaplayıcımızı](/tr/tools/cost-of-living) kullanabilirsin.

## Bu listeyi nasıl kullanmalı

Ucuzluk tek başına bir kriter değil. Sıralamayı şu üç soruyla birlikte oku:

1. **Bölümün orada var mı?** En ucuz şehir, okumak istediğin bölümü vermiyorsa senin için en ucuz şehir değildir.
2. **Kaç yıl kalacaksın?** Kısa program → bugünkü kira. Uzun program → artış hızı.
3. **Ulaşımı nasıl çözeceksin?** Bisiklet şehri mi, yoksa her gün tramvay mı?

Şehirlerin üniversite ve bölüm listelerine [şehir sayfalarımızdan](/tr/cities) bakabilirsin. Öne çıkan birkaçı: [Chemnitz](/tr/cities/chemnitz-q2795), [Magdeburg](/tr/cities/magdeburg-q1733), [Bochum](/tr/cities/bochum-q2103), [Leipzig](/tr/cities/leipzig-q2079).

## Sonuç

Kira listeleri yanlış değil, eksik. Semesterbeitrag'ı ve ulaşımı hesaba katınca listenin ortası belirgin şekilde karışıyor — ve en ucuz üç şehir (Chemnitz, Magdeburg, Bochum) hepsi doğu ve Ruhr bölgesinde, hepsinin ulaşım kartı dahil.

Karar verirken tek bir sütuna bakma. Kirayı, dönem ücretini, ulaşımı ve artış hızını birlikte oku — dördü birlikte, tek başına hiçbirinin söylemediği şeyi söylüyor.

*Kira verileri MLP Studentenwohnreport 2025'ten, Semesterbeitrag tutarları üniversitelerin 2026/27 kış dönemi açıklamalarından alınmıştır. Semesterbeitrag her dönem değişebilir; başvurudan önce üniversitenin kendi sayfasından teyit et.*
MD;

        $deBody = <<<'MD'
Listen mit den „günstigsten Studentenstädten Deutschlands" machen fast immer dasselbe: Sie sortieren nach Miete – und hören dort auf. Doch die Miete ist nicht der einzige ortsabhängige Fixposten. Dazu kommt der **Semesterbeitrag** – und entscheidend ist, ob darin ein **Ticket für den Nahverkehr** enthalten ist.

Rechnet man das mit ein, verschiebt sich die Reihenfolge. Manche Städte rutschen vier Plätze nach unten.

## Wie gerechnet wurde

**Monatliche Stadtkosten = Miete + (Semesterbeitrag ÷ 6) + (63 € falls kein Ticket enthalten)**

- **Miete:** MLP Studentenwohnreport 2025, Warmmiete für 30 m².
- **Semesterbeitrag:** Wintersemester 2026/27, je Hochschule einzeln recherchiert, durch sechs geteilt.
- **Nahverkehr:** Wo das Deutschlandsemesterticket eingeführt wurde, steckt die Mobilität bereits im Semesterbeitrag (226,80 € pro Semester). Wo nicht, kaufen Studierende das reguläre Deutschlandticket: **seit Januar 2026 63 € im Monat**.

Essen, Krankenversicherung und Telefon stehen nicht in der Tabelle – sie unterscheiden sich kaum nach Ort und ändern die Reihenfolge nicht. Sie kommen am Ende des Artikels dazu.

## Die 20 günstigsten Städte – echte Monatskosten

| # | Stadt | Miete | Semester/Monat | Nahverkehr | **Gesamt** | Rang nach Miete |
|---|---|---|---|---|---|---|
| 1 | Chemnitz | 296 € | 57 € | enthalten | **353 €** | 1 |
| 2 | Magdeburg | 374 € | 55 € | enthalten | **429 €** | 3 ▲ |
| 3 | Bochum | 368 € | 63 € | enthalten | **431 €** | 2 ▼ |
| 4 | Greifswald | 402 € | 21 € | +63 € | **486 €** | 4 |
| 5 | Leipzig | 442 € | 55 € | enthalten | **497 €** | 5 |
| 6 | Bielefeld | 443 € | 59 € | enthalten | **502 €** | 6 |
| 7 | Jena | 466 € | 56 € | enthalten | **522 €** | 8 ▲ |
| 8 | Kiel | 460 € | 67 € | enthalten | **527 €** | 7 ▼ |
| 9 | Hannover | 477 € | 76 € | enthalten | **553 €** | 9 |
| 10 | Rostock | 496 € | 59 € | enthalten | **555 €** | 11 ▲ |
| 11 | Dresden | 499 € | 60 € | enthalten | **559 €** | 12 ▲ |
| 12 | Saarbrücken | 496 € | 69 € | enthalten | **565 €** | 10 ▼▼ |
| 13 | Trier | 510 € | 59 € | enthalten | **569 €** | 16 ▲▲▲ |
| 14 | Bremen | 504 € | 74 € | enthalten | **578 €** | 14 |
| 15 | Aachen\* | 521 € | 59 € | enthalten | **580 €** | 17 ▲▲ |
| 16 | Göttingen | 508 € | 81 € | enthalten | **589 €** | 15 ▼ |
| 17 | Mannheim | 502 € | 32 € | +63 € | **597 €** | 13 ▼▼▼▼ |
| 18 | Oldenburg | 526 € | 76 € | enthalten | **602 €** | 18 |
| 19 | Tübingen | 526 € | 32 € | +63 € | **621 €** | 19 |
| 20 | Ulm | 530 € | 32 € | +63 € | **625 €** | 20 |

\* Die RWTH Aachen hat den Semesterbeitrag für 2026/27 noch nicht veröffentlicht. Der Wert ist geschätzt: 304,47 € aus dem WS 2024/25 zuzüglich der Erhöhung des Deutschlandsemestertickets. Für den genauen Betrag bitte die Seite der Hochschule prüfen.

Zum Vergleich die reine Miete der teuersten Städte: **München 837 €**, Frankfurt 734 €, Köln 688 €.

## Vier Erkenntnisse

### 1. Ein niedriger Semesterbeitrag heißt nicht günstig

Die niedrigsten Semesterbeiträge in der Tabelle haben Ulm, Tübingen und Mannheim (je rund 32 € im Monat). Klingt gut – bis man den Grund kennt: **In Baden-Württemberg ist kein Nahverkehrsticket im Semesterbeitrag enthalten.** Studierende zahlen das Deutschlandticket selbst, 63 € monatlich.

Mannheim steht nach Miete auf Platz 13, nach echten Kosten nur noch auf **Platz 17** – der stärkste Absturz der Liste.

Umgekehrt: Göttingen hat einen der höchsten Semesterbeiträge Deutschlands (81 € im Monat), darin steckt aber ein bundesweit gültiges Ticket. Von diesen 81 € sind 38 € reine Mobilität.

**Die Lehre:** Semesterbeiträge nie isoliert vergleichen. Erst fragen: *Ist das Ticket drin?*

### 2. Zwischen Chemnitz und Platz zwei liegt eine Lücke

Chemnitz: 353 €. Magdeburg auf Platz zwei: 429 €. Die Differenz von **76 €** ist größer als der Abstand zwischen Platz 2 und Platz 10.

Im Vergleich mit München wird es deutlicher: allein bei der Miete 541 € Unterschied im Monat. Über einen dreijährigen Bachelor sind das rund **19.500 €** – bei gleichem Abschluss im gleichen Land.

### 3. Günstig heute heißt nicht günstig zum Abschluss

Neben der Miethöhe zählt die Dynamik. Die Veränderung im letzten Jahr fällt sehr unterschiedlich aus:

| Steigt schnell | Stabil |
|---|---|
| Rostock **+9,1 %** | Ulm **−3,0 %** |
| Saarbrücken **+7,7 %** | Trier **+0,1 %** |
| Kiel **+7,1 %** | Chemnitz **+1,2 %** |
| Oldenburg **+6,9 %** | Aachen **+1,6 %** |

Hält das drei Jahre an, steigt die Miete in Rostock von 496 € auf rund 643 €, in Chemnitz von 296 € auf etwa 307 €.

**Bachelor oder Master verändert die Lesart der Tabelle.** Für einen einjährigen Master genügt der heutige Wert; für einen vierjährigen Weg gehört die Dynamik in die Entscheidung.

### 4. Das Greifswald-Paradox: der eigene Alltag verschiebt die Rangliste

Greifswald hat den niedrigsten Semesterbeitrag Deutschlands: 127 € pro Semester, also 21 € im Monat. Ein Ticket ist nicht enthalten, deshalb stehen in der Tabelle 63 € obendrauf – Platz 4.

Nur: Greifswald ist eine kleine, flache Küstenstadt, in der fast alle Rad fahren. Wer kein Deutschlandticket kauft, zahlt real **423 €** – und die Stadt rückt auf **Platz 2**.

Umgekehrt gilt dasselbe: In einer Großstadt wie Berlin ohne Nahverkehr zu leben, funktioniert nicht.

**Die Tabelle ist kein Ranking, sondern ein Ausgangspunkt.** Wer Rad fährt, streicht die Spalte Nahverkehr.

## Was nicht in der Tabelle steht

Weil es sich kaum nach Ort unterscheidet:

- **Krankenversicherung:** bundesweit gleicher Studierendentarif, ca. 130–140 € im Monat
- **Essen:** ca. 200–300 € (Mittagessen in der Mensa 3–5 €)
- **Telefon/Internet:** ca. 20–35 €
- **Persönliches, Bücher, Freizeit:** ca. 100–150 €

Die **tatsächlichen Gesamtausgaben** liegen also rund **450–600 € über** dem Tabellenwert: für Chemnitz etwa 800–950 €, für Ulm etwa 1.075–1.225 €.

Für die eigene Rechnung hilft unser [Rechner für Lebenshaltungskosten](/de/tools/cost-of-living).

## Wie man diese Liste nutzt

Günstig allein ist kein Kriterium. Drei Fragen gehören dazu:

1. **Gibt es dort deinen Studiengang?** Die günstigste Stadt ohne dein Fach ist für dich nicht die günstigste.
2. **Wie lange bleibst du?** Kurzes Programm → heutige Miete. Langes Programm → Dynamik.
3. **Wie löst du Mobilität?** Fahrradstadt oder täglich Straßenbahn?

Hochschulen und Studiengänge je Stadt findest du auf unseren [Städteseiten](/de/cities), zum Beispiel [Chemnitz](/de/cities/chemnitz-q2795), [Magdeburg](/de/cities/magdeburg-q1733), [Bochum](/de/cities/bochum-q2103) und [Leipzig](/de/cities/leipzig-q2079).

## Fazit

Mietlisten sind nicht falsch, nur unvollständig. Mit Semesterbeitrag und Nahverkehr sortiert sich die Mitte des Feldes spürbar um – und die drei günstigsten Städte (Chemnitz, Magdeburg, Bochum) liegen alle im Osten beziehungsweise im Ruhrgebiet und haben das Ticket inklusive.

Nicht auf eine einzelne Spalte schauen. Miete, Semesterbeitrag, Mobilität und Dynamik zusammen lesen – zu viert sagen sie etwas, das keine davon allein sagt.

*Mietdaten aus dem MLP Studentenwohnreport 2025, Semesterbeiträge aus den Veröffentlichungen der Hochschulen für das Wintersemester 2026/27. Beiträge können sich jedes Semester ändern; vor der Bewerbung auf der Seite der Hochschule prüfen.*
MD;

        $enBody = <<<'MD'
Lists of "Germany's cheapest student cities" almost all do the same thing: they sort by rent and stop there. But rent is not the only fixed cost that depends on where you live. There is also the **Semesterbeitrag** – the compulsory semester fee – and what really matters is whether a **public transport pass is included in it**.

Once you account for that, the order changes. Some cities drop four places.

## How the numbers were built

**Monthly city cost = rent + (semester fee ÷ 6) + (€63 if no transport pass is included)**

- **Rent:** MLP Studentenwohnreport 2025, warm rent for 30 m² (utilities included).
- **Semester fee:** winter semester 2026/27, researched university by university, divided by six.
- **Transport:** where a university has moved to the Deutschlandsemesterticket, mobility is already inside the semester fee (€226.80 per semester). Where it has not, students buy the regular Deutschlandticket: **€63 per month since January 2026**.

Food, health insurance and phone costs are not in the table – they barely vary by city and do not change the order. They are added at the end of the article.

## The 20 cheapest cities – real monthly cost

| # | City | Rent | Fee/month | Transport | **Total** | Rank by rent |
|---|---|---|---|---|---|---|
| 1 | Chemnitz | €296 | €57 | included | **€353** | 1 |
| 2 | Magdeburg | €374 | €55 | included | **€429** | 3 ▲ |
| 3 | Bochum | €368 | €63 | included | **€431** | 2 ▼ |
| 4 | Greifswald | €402 | €21 | +€63 | **€486** | 4 |
| 5 | Leipzig | €442 | €55 | included | **€497** | 5 |
| 6 | Bielefeld | €443 | €59 | included | **€502** | 6 |
| 7 | Jena | €466 | €56 | included | **€522** | 8 ▲ |
| 8 | Kiel | €460 | €67 | included | **€527** | 7 ▼ |
| 9 | Hannover | €477 | €76 | included | **€553** | 9 |
| 10 | Rostock | €496 | €59 | included | **€555** | 11 ▲ |
| 11 | Dresden | €499 | €60 | included | **€559** | 12 ▲ |
| 12 | Saarbrücken | €496 | €69 | included | **€565** | 10 ▼▼ |
| 13 | Trier | €510 | €59 | included | **€569** | 16 ▲▲▲ |
| 14 | Bremen | €504 | €74 | included | **€578** | 14 |
| 15 | Aachen\* | €521 | €59 | included | **€580** | 17 ▲▲ |
| 16 | Göttingen | €508 | €81 | included | **€589** | 15 ▼ |
| 17 | Mannheim | €502 | €32 | +€63 | **€597** | 13 ▼▼▼▼ |
| 18 | Oldenburg | €526 | €76 | included | **€602** | 18 |
| 19 | Tübingen | €526 | €32 | +€63 | **€621** | 19 |
| 20 | Ulm | €530 | €32 | +€63 | **€625** | 20 |

\* RWTH Aachen has not yet published its 2026/27 semester fee. The figure here is an estimate: €304.47 from winter 2024/25 plus the increase in the Deutschlandsemesterticket. Check the university's own page for the exact amount.

For comparison, rent alone in the most expensive cities: **Munich €837**, Frankfurt €734, Cologne €688.

## Four findings

### 1. A low semester fee does not mean a cheap city

The lowest semester fees in the table belong to Ulm, Tübingen and Mannheim – about €32 a month each. That sounds excellent until you learn why: **in Baden-Württemberg no transport pass is included in the semester fee.** Students buy the Deutschlandticket themselves, at €63 a month.

Mannheim ranks 13th on rent but only **17th on real cost** – the steepest fall in the list.

The reverse also holds. Göttingen has one of the highest semester fees in Germany (€81 a month), but it contains a pass valid across all of Germany. Of those €81, €38 is transport.

**The lesson:** never compare semester fees in isolation. Ask first: *is the ticket inside?*

### 2. There is a gap between Chemnitz and second place

Chemnitz: €353. Magdeburg in second: €429. That **€76** gap is larger than the distance between second and tenth place.

Against Munich the picture is starker: €541 a month in rent alone. Over a three-year bachelor's that is roughly **€19,500** – for the same degree in the same country.

### 3. Cheap today is not cheap at graduation

Alongside the level of rent sits its direction. Over the past year these cities moved very differently:

| Rising fast | Stable |
|---|---|
| Rostock **+9.1%** | Ulm **−3.0%** |
| Saarbrücken **+7.7%** | Trier **+0.1%** |
| Kiel **+7.1%** | Chemnitz **+1.2%** |
| Oldenburg **+6.9%** | Aachen **+1.6%** |

If those rates hold for three years, rent in Rostock goes from €496 to about €643; in Chemnitz from €296 to about €307.

**Whether you are doing a bachelor's or a master's changes how you read this table.** For a one-year master's, today's figure is enough; for a four-year path, the trend belongs in the decision.

### 4. The Greifswald paradox: your own habits reorder the list

Greifswald has the lowest semester fee in Germany: €127 per semester, or €21 a month. No transport pass is included, so the table adds €63 – leaving the city in fourth.

But Greifswald is a small, flat coastal town where most students cycle. If you never buy a Deutschlandticket, your real cost is **€423** and the city moves to **second place**.

The same logic runs the other way: living without public transport in a city like Berlin is not realistic.

**This table is a starting point, not a ranking.** If you cycle, delete the transport column.

## What the table leaves out

Because it barely varies by city:

- **Health insurance:** the same student rate nationwide, roughly €130–140 a month
- **Food:** roughly €200–300 (a Mensa lunch costs €3–5)
- **Phone and internet:** roughly €20–35
- **Personal spending, books, leisure:** roughly €100–150

So your **actual total monthly spending** is about **€450–600 above** the table figure: around €800–950 in Chemnitz, around €1,075–1,225 in Ulm.

To build your own budget, use our [cost of living calculator](/en/tools/cost-of-living).

## How to use this list

Cheap on its own is not a criterion. Read the ranking alongside three questions:

1. **Does your programme exist there?** The cheapest city without your subject is not your cheapest city.
2. **How long will you stay?** Short programme → today's rent. Long programme → the trend.
3. **How will you handle transport?** A cycling town, or a tram every day?

You can find the universities and programmes in each city on our [city pages](/en/cities) – for example [Chemnitz](/en/cities/chemnitz-q2795), [Magdeburg](/en/cities/magdeburg-q1733), [Bochum](/en/cities/bochum-q2103) and [Leipzig](/en/cities/leipzig-q2079).

## Conclusion

Rent lists are not wrong, just incomplete. Add the semester fee and transport and the middle of the field visibly reshuffles – while the three cheapest cities (Chemnitz, Magdeburg, Bochum) all sit in the east or the Ruhr area, and all include the ticket.

Do not decide on a single column. Read rent, semester fee, transport and trend together – the four of them say something none of them says alone.

*Rent data from the MLP Studentenwohnreport 2025; semester fees from the universities' own winter 2026/27 announcements. Fees can change every semester, so confirm on the university's page before applying.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'cheapest-student-cities-germany-real-monthly-cost',
                'title' => 'Almanya\'nın En Ucuz 20 Öğrenci Şehri — Gerçek Aylık Maliyetle',
                'excerpt' => 'Kira listeleri eksik: Semesterbeitrag ve ulaşım kartı hesaba katılınca sıralama değişiyor, Mannheim dört sıra geriliyor. 20 şehir için kira + dönem ücreti + ulaşım birleşik tablo, kira artış hızlarıyla birlikte.',
                'meta_title' => 'Almanya\'nın En Ucuz 20 Öğrenci Şehri (2026 Gerçek Maliyet)',
                'meta_description' => 'Kira + Semesterbeitrag + ulaşım birlikte hesaplandı. Chemnitz 353 €, Ulm 625 €. Ulaşım kartı hangi şehirlerde dahil değil ve kira nerede hızla artıyor?',
                'body' => $trBody,
            ],
            'de' => [
                'slug' => 'cheapest-student-cities-germany-real-monthly-cost-de',
                'title' => 'Die 20 günstigsten Studentenstädte Deutschlands — mit echten Monatskosten',
                'excerpt' => 'Mietlisten sind unvollständig: Mit Semesterbeitrag und Nahverkehr verschiebt sich die Reihenfolge, Mannheim fällt vier Plätze. Tabelle für 20 Städte inklusive Mietdynamik.',
                'meta_title' => 'Die 20 günstigsten Studentenstädte Deutschlands (2026)',
                'meta_description' => 'Miete + Semesterbeitrag + Nahverkehr zusammen gerechnet. Chemnitz 353 €, Ulm 625 €. Wo ist das Ticket nicht enthalten und wo steigen die Mieten am schnellsten?',
                'body' => $deBody,
            ],
            'en' => [
                'slug' => 'cheapest-student-cities-germany-real-monthly-cost-en',
                'title' => 'The 20 Cheapest Student Cities in Germany — With Real Monthly Costs',
                'excerpt' => 'Rent lists are incomplete: add the semester fee and transport pass and the order changes, with Mannheim dropping four places. A combined table for 20 cities, plus how fast rents are rising.',
                'meta_title' => 'The 20 Cheapest Student Cities in Germany (2026 Real Cost)',
                'meta_description' => 'Rent + semester fee + transport calculated together. Chemnitz €353, Ulm €625. Where is the transport pass not included, and where are rents rising fastest?',
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
            'cheapest-student-cities-germany-real-monthly-cost',
            'cheapest-student-cities-germany-real-monthly-cost-de',
            'cheapest-student-cities-germany-real-monthly-cost-en',
        ])->delete();
    }
};

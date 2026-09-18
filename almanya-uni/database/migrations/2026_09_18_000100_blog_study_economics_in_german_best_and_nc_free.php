<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanca ekonomi (VWL) lisansı nerede okunur — prestij sırası + NC'siz üniversiteler.
 *
 * Grounding (kendi Hochschulkompass kataloğumuz, hk_catalog; uzaktan/Lehramt/Nebenfach ayıklandı):
 *   - Lisans düzeyinde 64 kurumda 109 Almanca VWL/Wirtschaftswissenschaften programı.
 *   - Bunların 70'i (43 kurum) zulassungsfrei; salt VWL'de 34 kurumda 43 program, 29'u NC'siz.
 *   - Kritik bulgu: aynı üniversitede "Wirtschaftswissenschaften" NC'li iken "Volkswirtschaftslehre"
 *     NC'siz olabiliyor (Bonn, LMU, Tübingen, Freiburg, Konstanz, Stuttgart doğrulandı).
 * Mevcut ekonomi kümesini (2026-07-02) tamamlar; o yazı "VWL nedir/nasıl okunur", bu yazı "nerede".
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. Slug-bazlı idempotent, prod ID'lerinden bağımsız.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'e7c30918-2b44-4a71-9d15-8c0f1a62d901';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almanya'da Almanca ekonomi nerede okunur?" sorusu aslında **iki ayrı sorudur** ve bunları karıştırmak yıllara mal olur: *(1) en iyisi nerede?* ve *(2) gerçekten nereye girebilirim?* Bu yazı ikisini ayrı ayrı yanıtlıyor. Önce prestij sırasıyla başlıyoruz, sonra NC'siz — yani not barajı olmadan kayıt olabileceğin — üniversiteleri veriyoruz.

Rakamlar, Almanya'nın resmi program kataloğu **Hochschulkompass** kayıtlarından çıkarıldı: uzaktan öğretim, öğretmenlik (Lehramt) ve yan dal varyantları ayıklandığında lisans düzeyinde **64 üniversitede 109 Almanca ekonomi programı** var; bunların **43 kurumdaki 70 tanesi NC'siz**.

## Önce doğru ismi öğren: VWL mi, Wirtschaftswissenschaften mi?

Almanca ekonomi lisansı iki isimle karşına çıkar ve bu isim farkı **başvurunun kaderini belirler**:

- **Volkswirtschaftslehre (VWL)** — bizim "iktisat" dediğimiz alan: makro, mikro, ekonometri, iktisat politikası.
- **Wirtschaftswissenschaften** — ekonomi + işletmeyi birleştiren geniş program; içinde BWL de vardır.

Kritik nokta şu: **aynı üniversitede bu ikisinin kabul koşulu farklı olabiliyor.** Bonn'da *Wirtschaftswissenschaften* NC'li (yerel seçme) iken *Volkswirtschaftslehre* **NC'siz**. LMU Münih'te, Tübingen'de, Freiburg'da, Konstanz'da ve Stuttgart'ta tablo aynı. Yani Almanya'nın en güçlü iktisat fakültelerinden birine, sadece programın doğru adını seçerek **not barajı olmadan** girebiliyorsun. Bunu bilmeyen aday, aynı binadaki NC'li programa başvurup eleniyor.

## En prestijli ekonomi fakülteleri (sırayla)

Önce bir uyarı: **genel dünya sıralaması bölüm gücünü ölçmez.** Bir üniversite tıp ve fizikle ilk 100'e girmiş olabilir; bu, iktisat bölümünün güçlü olduğu anlamına gelmez. Aşağıda ekonomi alanındaki araştırma itibarına göre sıraladım, üniversitenin genel dünya sırasını ayrı sütunda bıraktım.

| # | Üniversite | Şehir | Genel dünya sırası | Almanca lisans | NC durumu |
|---|---|---|---|---|---|
| 1 | **Bonn** | Bonn | ~67 | VWL + Wirtschaftswissenschaften | VWL **NC'siz**, Wiwi NC'li |
| 2 | **Mannheim** | Mannheim | ~280 | VWL | NC'li |
| 3 | **LMU München** | Münih | ~34 | VWL + Wirtschaftswissenschaften | VWL **NC'siz**, Wiwi NC'li |
| 4 | **Köln** | Köln | ~151 | VWL | NC'li |
| 5 | **Goethe Frankfurt** | Frankfurt | ~151 | Wirtschaftswissenschaften | NC'li |
| 6 | **Humboldt Berlin** | Berlin | ~87 | VWL (iki varyant) | biri NC'li, biri **NC'siz** |
| 7 | **Freie Universität Berlin** | Berlin | ~98 | VWL | NC'li |
| 8 | **Münster** | Münster | ~193 | VWL | NC'li |
| 9 | **Tübingen** | Tübingen | ~100 | VWL + Wirtschaftswissenschaft | VWL **NC'siz** |
| 10 | **Heidelberg / Hamburg / Konstanz** | — | ~80 / ~125 / ~201 | VWL veya Wiwi | Heidelberg & Hamburg NC'li, Konstanz'ın bir varyantı **NC'siz** |

*Dünya sıralaması değerleri QS/THE/ARWU içinden en iyi sırayı gösterir ve yıldan yıla değişir; NC durumu her dönem güncellenir. Başvurudan önce üniversitenin kendi sayfasından doğrula.*

**Bonn** Almanya'nın iktisat bakımından en güçlü adresi sayılır: teorik derinlik, Bonn Graduate School of Economics ve yoğun araştırma kültürü. **Mannheim** kantitatif iktisadın merkezi — ZEW araştırma enstitüsüyle iç içe, ekonometri tarafı ağır. **LMU Münih** hem geniş fakülte kadrosu hem ifo Enstitüsü'ne yakınlığıyla öne çıkar. **Frankfurt** ise sıralamadan bağımsız bir avantaj sunar: Avrupa Merkez Bankası ve finans sektörü aynı şehirde, staj ve iş bulma açısından eşsiz.

## Altın kesişim: tepe fakülte + NC yok

Aday için en değerli liste bu — hem güçlü hem de not barajı uygulamayan Almanca VWL lisansları:

| Üniversite | Şehir | Neden değerli |
|---|---|---|
| **Bonn** | Bonn | Ülkenin en güçlü iktisat fakültesi, VWL programı NC'siz |
| **LMU München** | Münih | En yüksek genel dünya sırası (~34), VWL NC'siz |
| **Tübingen** | Tübingen | Köklü araştırma üniversitesi, VWL NC'siz |
| **Freiburg** | Freiburg | Ordo-liberal iktisat geleneğinin merkezi, VWL NC'siz |
| **Göttingen** | Göttingen | Güçlü araştırma geçmişi, VWL NC'siz |
| **Kiel** | Kiel | Kiel Dünya Ekonomisi Enstitüsü (IfW) aynı şehirde, VWL NC'siz |

Bu altı isim, "iyi üniversite = yüksek NC" varsayımının Almanya'da neden çalışmadığını gösteriyor. Almanya'da NC bir **kalite göstergesi değil, talep göstergesidir**: kontenjandan fazla başvuru gelirse NC doğar, gelmezse doğmaz.

## En kolay girilenler: NC'siz Almanca ekonomi lisansı olan üniversiteler

Katalogda **43 kurumda 70 NC'siz program** var. Aşağıda uzaktan öğretim, özel ücretli okullar, Bundeswehr üniversitesi ve öğretmenlik varyantları çıkarıldıktan sonra kalan devlet üniversiteleri:

**Bavyera:** LMU München · Regensburg · Würzburg · Bayreuth · Augsburg · Eichstätt-Ingolstadt · TH Deggendorf
**Baden-Württemberg:** Tübingen · Freiburg · Stuttgart (VWL) · Konstanz · Ulm
**Kuzey Ren-Vestfalya:** Bonn · TU Dortmund · Duisburg-Essen · Siegen · Paderborn
**Hessen & Renanya-Pfalz:** TU Darmstadt · Gießen · Marburg · Kassel · Trier · RPTU Kaiserslautern-Landau
**Aşağı Saksonya & kuzey:** Göttingen · Kiel · Osnabrück · Oldenburg · Bremen
**Doğu eyaletleri:** Jena · Magdeburg · Chemnitz · Erfurt · Rostock · Greifswald · Schmalkalden
**Berlin:** Humboldt-Universität (VWL'nin bir varyantı)

Bütçesi sınırlı ve "kesin yerleşmek" isteyen bir aday için en rahat rota **Bavyera ve doğu eyaletleri**: hem NC yok, hem öğrenim ücreti yok, hem de yaşam maliyeti Münih dışında düşük (Regensburg, Würzburg, Bayreuth, Jena, Magdeburg, Greifswald, Rostock).

## "NC yok" ne demek değildir

Bu bölümü atlama — NC'siz program, otomatik kabul anlamına **gelmez**. Kapıda hâlâ dört şart var:

1. **Almanca C1 belgesi.** Bu programlar tamamen Almanca. Standart kabul: **DSH-2** veya **TestDaF 4x4**. Sıfırdan C1'e gerçekçi süre **12–24 aydır** — [Almanca yol haritası](/tr/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh) ve [TestDaF mi DSH mi](/tr/blog/testdaf-or-dsh-2026-german-language-exam-comparison) yazılarına bak.
2. **Diploma denkliği.** Türk lise diploman tek başına yetmeyebilir; anabin değerlendirmesi ve çoğu durumda [Studienkolleg](/tr/blog/studienkolleg-guide-2026-who-needs-it-which-course-which-school) gerekir. Detay: [Anabin H+, H+-, H- nedir](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma).
3. **uni-assist ve süre.** NC'siz program bile **başvuru tarihine** tabidir (kış dönemi için genelde 15 Temmuz, yaz dönemi için 15 Ocak). Uluslararası başvurular çoğunlukla uni-assist üzerinden gider — [adım adım rehber](/tr/blog/uni-assist-application-guide-a-z-your-step-by-step-path).
4. **Gerçek eleme ilk yılda.** Almanya'da kapı geniş, koridor dardır: *Mathematik I/II*, *Statistik* ve *Mikroökonomie* sınavları ilk yılda ciddi bir eleme yapar. Bonn ve Mannheim gibi kantitatif bölümlerde bu his daha da serttir.

## Para: Baden-Württemberg tuzağı

NC'siz listede Tübingen, Freiburg, Konstanz, Stuttgart ve Ulm çok cazip görünüyor — ama hepsi **Baden-Württemberg** eyaletinde. Bu eyalet AB dışından gelen öğrencilerden **dönem başına yaklaşık 1.500 €** öğrenim ücreti alıyor. Diğer eyaletlerin devlet üniversitelerinde öğrenim ücreti yok; yalnızca **dönemlik katkı payı (Semesterbeitrag) ~150–350 €** ödersin ve bu genelde ulaşım biletini içerir.

Yani aynı NC'siz VWL programı için Freiburg ile Regensburg arasındaki fark, lisans boyunca **yaklaşık 9.000 €**'ya kadar çıkabilir. *(2026 başı itibarıyla; tutarlar yıllık güncellenir, başvurudan önce doğrula.)*

## Nasıl seçmeli — kısa karar çerçevesi

- **Doktora/araştırma hedefin varsa:** Bonn, Mannheim, LMU, Köln. NC'yi göze al, gerekirse Bonn'un NC'siz VWL kapısını kullan.
- **"Kesin yerleşeyim" diyorsan:** Regensburg, Würzburg, Bayreuth, Jena, Magdeburg, Rostock, Greifswald — NC yok, ücret yok.
- **Hem tepe hem NC'siz istiyorsan:** Bonn, LMU, Tübingen, Freiburg, Göttingen, Kiel.
- **Finans kariyeri hedefliyorsan:** Frankfurt — okul kadar şehir de kariyer yapar.
- **Staj ve yarı zamanlı iş önceliğinse:** Köln, Münih, Frankfurt, Berlin gibi büyük iş piyasaları.

## Sonuç & dürüst tavsiye

Almanya'da Almanca ekonomi okumak, sanılanın aksine **not ortalaması savaşı değil, dil ve doğru program seçimi meselesidir.** İki cümlede özet: en güçlü iktisat fakültesi olarak **Bonn**'u hedefle ve şansın şu ki oranın VWL programı NC'siz; "kesin girmek" önceliğinse **Bavyera ve doğu eyaletlerindeki** NC'siz programlar seni not barajına takılmadan içeri alır. Asıl yatırımı **C1 Almancaya** ve ilk yılın matematiğine yap — eleme orada.

Devamı için kümedeki diğer yazılar: [Almanya'da VWL okumak — genel rehber](/tr/blog/studying-economics-vwl-in-germany-as-a-foreigner), [Almancasız İngilizce ekonomi master programları](/tr/blog/english-taught-economics-masters-in-germany-without-german), [ekonomist olarak çalışmak](/tr/blog/working-as-an-economist-in-germany-research-policy-finance) ve [VWL diplomasıyla iş piyasası](/tr/blog/what-to-do-with-an-economics-vwl-degree-in-germany-job-market).

*Bu yazı 2026 başı itibarıyla, Hochschulkompass program kayıtlarına dayanılarak hazırlanmıştır. NC durumu, harçlar, başvuru tarihleri ve dil koşulları her dönem değişebilir; başvurudan önce üniversitenin güncel sayfasından doğrula.*
MD;

        $deBody = <<<'MD'
Die Frage "Wo studiert man in Deutschland VWL auf Deutsch?" besteht in Wahrheit aus **zwei Fragen**, und wer sie vermischt, verliert Jahre: *(1) Wo ist es am besten?* und *(2) Wo komme ich realistisch rein?* Dieser Beitrag beantwortet beide getrennt. Zuerst die Reihenfolge nach Renommee, danach die Unis **ohne NC** — also ohne Notenhürde.

Die Zahlen stammen aus den Einträgen des offiziellen deutschen Studiengangkatalogs **Hochschulkompass**: Ohne Fernstudium, Lehramt und Nebenfachvarianten gibt es auf Bachelorniveau **109 deutschsprachige Wirtschaftsprogramme an 64 Hochschulen** — davon sind **70 an 43 Hochschulen zulassungsfrei**.

## Erst der richtige Name: VWL oder Wirtschaftswissenschaften?

Der deutschsprachige Wirtschaftsbachelor begegnet dir unter zwei Namen, und dieser Unterschied **entscheidet über deine Bewerbung**:

- **Volkswirtschaftslehre (VWL)** — die Gesamtwirtschaft: Makro, Mikro, Ökonometrie, Wirtschaftspolitik.
- **Wirtschaftswissenschaften** — das breite Programm aus VWL und BWL zusammen.

Entscheidend ist: **An derselben Universität können die Zulassungsbedingungen unterschiedlich sein.** In Bonn ist *Wirtschaftswissenschaften* örtlich zulassungsbeschränkt, *Volkswirtschaftslehre* dagegen **zulassungsfrei**. Dasselbe Bild an der LMU München, in Tübingen, Freiburg, Konstanz und Stuttgart. Du kommst also an eine der stärksten ökonomischen Fakultäten des Landes — **ohne Notenhürde**, nur weil du den richtigen Programmnamen wählst. Wer das nicht weiß, bewirbt sich auf den NC-Studiengang im selben Gebäude und scheitert.

## Die renommiertesten wirtschaftswissenschaftlichen Fakultäten

Zuerst eine Warnung: **Ein allgemeines Weltranking misst nicht die Stärke eines Fachbereichs.** Eine Uni kann dank Medizin und Physik in den Top 100 stehen, ohne eine starke VWL-Fakultät zu haben. Die folgende Reihenfolge orientiert sich am Forschungsruf in der Ökonomie; die allgemeine Weltplatzierung steht in einer eigenen Spalte.

| # | Universität | Stadt | Allg. Weltrang | Deutschsprachiger Bachelor | Zulassung |
|---|---|---|---|---|---|
| 1 | **Bonn** | Bonn | ~67 | VWL + Wirtschaftswissenschaften | VWL **zulassungsfrei**, Wiwi mit NC |
| 2 | **Mannheim** | Mannheim | ~280 | VWL | mit NC |
| 3 | **LMU München** | München | ~34 | VWL + Wirtschaftswissenschaften | VWL **zulassungsfrei**, Wiwi mit NC |
| 4 | **Köln** | Köln | ~151 | VWL | mit NC |
| 5 | **Goethe-Uni Frankfurt** | Frankfurt | ~151 | Wirtschaftswissenschaften | mit NC |
| 6 | **Humboldt-Universität** | Berlin | ~87 | VWL (zwei Varianten) | eine mit NC, eine **zulassungsfrei** |
| 7 | **Freie Universität Berlin** | Berlin | ~98 | VWL | mit NC |
| 8 | **Münster** | Münster | ~193 | VWL | mit NC |
| 9 | **Tübingen** | Tübingen | ~100 | VWL + Wirtschaftswissenschaft | VWL **zulassungsfrei** |
| 10 | **Heidelberg / Hamburg / Konstanz** | — | ~80 / ~125 / ~201 | VWL bzw. Wiwi | Heidelberg & Hamburg mit NC, eine Variante in Konstanz **zulassungsfrei** |

*Die Weltplatzierungen zeigen den besten Rang aus QS/THE/ARWU und ändern sich jährlich; der Zulassungsmodus wird jedes Semester neu festgelegt. Prüfe ihn vor der Bewerbung auf der Seite der Hochschule.*

**Bonn** gilt als stärkste ökonomische Adresse Deutschlands: theoretische Tiefe, die Bonn Graduate School of Economics, eine dichte Forschungskultur. **Mannheim** ist das Zentrum der quantitativen Ökonomik — eng verzahnt mit dem ZEW, mit schwerem Ökonometrie-Anteil. Die **LMU München** punktet mit breiter Fakultät und der Nähe zum ifo Institut. **Frankfurt** bietet einen Vorteil jenseits jedes Rankings: Europäische Zentralbank und Finanzsektor sitzen in derselben Stadt — unschlagbar für Praktika und Berufseinstieg.

## Die goldene Schnittmenge: Spitzenfakultät und kein NC

Das ist die wertvollste Liste für Bewerber:innen — stark **und** ohne Notenhürde:

| Universität | Stadt | Warum sie zählt |
|---|---|---|
| **Bonn** | Bonn | Stärkste VWL-Fakultät des Landes, Programm zulassungsfrei |
| **LMU München** | München | Höchste allgemeine Weltplatzierung (~34), VWL zulassungsfrei |
| **Tübingen** | Tübingen | Traditionsreiche Forschungsuniversität, VWL zulassungsfrei |
| **Freiburg** | Freiburg | Heimat der ordoliberalen Tradition, VWL zulassungsfrei |
| **Göttingen** | Göttingen | Starke Forschungsgeschichte, VWL zulassungsfrei |
| **Kiel** | Kiel | Institut für Weltwirtschaft am Ort, VWL zulassungsfrei |

Diese sechs Namen zeigen, warum die Gleichung "gute Uni = hoher NC" in Deutschland nicht aufgeht. Der NC ist **kein Qualitätsmaß, sondern ein Nachfragemaß**: Gibt es mehr Bewerbungen als Plätze, entsteht ein NC — sonst nicht.

## Am leichtesten zugänglich: Hochschulen mit zulassungsfreier VWL

Im Katalog stehen **70 zulassungsfreie Programme an 43 Hochschulen**. Nach Abzug von Fernstudium, privaten Anbietern, der Bundeswehr-Universität und Lehramtsvarianten bleiben diese staatlichen Universitäten:

**Bayern:** LMU München · Regensburg · Würzburg · Bayreuth · Augsburg · Eichstätt-Ingolstadt · TH Deggendorf
**Baden-Württemberg:** Tübingen · Freiburg · Stuttgart (VWL) · Konstanz · Ulm
**Nordrhein-Westfalen:** Bonn · TU Dortmund · Duisburg-Essen · Siegen · Paderborn
**Hessen & Rheinland-Pfalz:** TU Darmstadt · Gießen · Marburg · Kassel · Trier · RPTU Kaiserslautern-Landau
**Niedersachsen & Norden:** Göttingen · Kiel · Osnabrück · Oldenburg · Bremen
**Ostdeutschland:** Jena · Magdeburg · Chemnitz · Erfurt · Rostock · Greifswald · Schmalkalden
**Berlin:** Humboldt-Universität (eine VWL-Variante)

Wer knapp kalkuliert und sicher einen Platz will, fährt mit **Bayern und Ostdeutschland** am besten: kein NC, keine Studiengebühren und — außerhalb Münchens — niedrige Lebenshaltungskosten (Regensburg, Würzburg, Bayreuth, Jena, Magdeburg, Greifswald, Rostock).

## Was "kein NC" **nicht** bedeutet

Überspringe diesen Abschnitt nicht: Zulassungsfrei heißt **nicht** automatisch aufgenommen. Vier Hürden bleiben:

1. **Deutsch auf C1-Niveau.** Diese Studiengänge laufen vollständig auf Deutsch. Standard sind **DSH-2** oder **TestDaF 4x4**. Von null bis C1 dauert es realistisch **12–24 Monate** — siehe [Deutsch-Fahrplan](/de/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh-de) und [TestDaF oder DSH](/de/blog/testdaf-or-dsh-2026-german-language-exam-comparison-de).
2. **Anerkennung des Zeugnisses.** Ein ausländisches Schulzeugnis reicht oft nicht allein; es zählt die anabin-Bewertung, häufig ist ein [Studienkolleg](/de/blog/studienkolleg-guide-2026-who-needs-it-which-course-which-school-de) nötig. Details: [anabin H+, H+-, H-](/de/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-de).
3. **uni-assist und Fristen.** Auch zulassungsfreie Programme haben **Bewerbungsfristen** (meist 15. Juli fürs Wintersemester, 15. Januar fürs Sommersemester). Internationale Bewerbungen laufen meist über uni-assist — [Schritt für Schritt](/de/blog/uni-assist-application-guide-a-z-your-step-by-step-path-de).
4. **Die echte Auslese kommt im ersten Jahr.** Die Tür ist weit, der Flur ist eng: *Mathematik I/II*, *Statistik* und *Mikroökonomie* sieben im ersten Jahr kräftig aus — in quantitativen Fakultäten wie Bonn und Mannheim besonders spürbar.

## Geld: die Baden-Württemberg-Falle

In der NC-freien Liste sehen Tübingen, Freiburg, Konstanz, Stuttgart und Ulm sehr attraktiv aus — sie liegen aber alle in **Baden-Württemberg**. Dieses Bundesland verlangt von Studierenden aus Nicht-EU-Ländern rund **1.500 € Studiengebühr pro Semester**. An staatlichen Hochschulen der übrigen Länder fallen keine Studiengebühren an, nur der **Semesterbeitrag von etwa 150–350 €**, der meist ein Verkehrsticket enthält.

Für denselben zulassungsfreien VWL-Bachelor kann der Unterschied zwischen Freiburg und Regensburg über das Studium hinweg also bis zu **rund 9.000 €** ausmachen. *(Stand Anfang 2026; die Beträge werden jährlich angepasst — vor der Bewerbung prüfen.)*

## Wie du wählst — ein kurzer Entscheidungsrahmen

- **Ziel Promotion/Forschung:** Bonn, Mannheim, LMU, Köln — NC in Kauf nehmen oder über Bonns zulassungsfreie VWL einsteigen.
- **Ziel "sicher einen Platz":** Regensburg, Würzburg, Bayreuth, Jena, Magdeburg, Rostock, Greifswald — kein NC, keine Gebühren.
- **Spitze und zulassungsfrei:** Bonn, LMU, Tübingen, Freiburg, Göttingen, Kiel.
- **Ziel Finanzkarriere:** Frankfurt — hier macht die Stadt so viel Karriere wie die Uni.
- **Praktika und Nebenjob zuerst:** große Arbeitsmärkte wie Köln, München, Frankfurt, Berlin.

## Fazit & ehrlicher Rat

VWL auf Deutsch zu studieren ist in Deutschland **kein Notenwettlauf, sondern eine Frage der Sprache und der richtigen Programmwahl.** In zwei Sätzen: Ziele auf **Bonn** als stärkste ökonomische Fakultät — und das Glück ist, dass dort genau die VWL zulassungsfrei ist; steht Planungssicherheit an erster Stelle, holen dich die zulassungsfreien Programme in **Bayern und Ostdeutschland** ohne Notenhürde herein. Investiere die eigentliche Energie in **C1-Deutsch** und die Mathematik des ersten Jahres — dort wird ausgesiebt.

Weiterlesen: [VWL in Deutschland studieren — der Gesamtüberblick](/de/blog/studying-economics-vwl-in-germany-as-a-foreigner-de), [englischsprachige Master ohne Deutsch](/de/blog/english-taught-economics-masters-in-germany-without-german-de), [als Ökonom:in arbeiten](/de/blog/working-as-an-economist-in-germany-research-policy-finance-de) und [Arbeitsmarkt mit VWL-Abschluss](/de/blog/what-to-do-with-an-economics-vwl-degree-in-germany-job-market-de).

*Dieser Beitrag wurde Anfang 2026 auf Basis der Hochschulkompass-Einträge erstellt. Zulassungsmodus, Gebühren, Fristen und Sprachanforderungen können sich jedes Semester ändern; prüfe sie vor der Bewerbung auf der aktuellen Seite der Hochschule.*
MD;

        $enBody = <<<'MD'
"Where do you study economics in German in Germany?" is really **two questions**, and mixing them up costs years: *(1) where is it best?* and *(2) where can I realistically get in?* This guide answers them separately. We start with the prestige order, then give you the universities with **no NC** — no grade threshold at the door.

The numbers come from the entries of **Hochschulkompass**, Germany's official programme catalogue. Excluding distance learning, teacher-training and minor-subject variants, there are **109 German-taught economics bachelor programmes at 64 universities** — and **70 of them, at 43 institutions, are open admission (zulassungsfrei)**.

## First, learn the right name: VWL or Wirtschaftswissenschaften?

The German-taught economics bachelor appears under two names, and that difference **decides your application**:

- **Volkswirtschaftslehre (VWL)** — economics proper: macro, micro, econometrics, economic policy.
- **Wirtschaftswissenschaften** — the broad programme combining economics and business administration.

Here is the crucial part: **at the same university the admission rules for the two can differ.** At Bonn, *Wirtschaftswissenschaften* is NC-restricted while *Volkswirtschaftslehre* is **open admission**. The same pattern holds at LMU Munich, Tübingen, Freiburg, Konstanz and Stuttgart. In other words, you can enter one of the country's strongest economics faculties **without a grade threshold**, simply by choosing the right programme name. Applicants who do not know this apply to the NC programme in the same building — and get rejected.

## The most prestigious economics faculties, in order

A warning first: **a general world ranking does not measure departmental strength.** A university can sit in the global top 100 on the strength of medicine and physics without having a strong economics faculty. The order below follows research reputation in economics; the university's overall world position sits in its own column.

| # | University | City | Overall world rank | German-taught bachelor | Admission |
|---|---|---|---|---|---|
| 1 | **Bonn** | Bonn | ~67 | VWL + Wirtschaftswissenschaften | VWL **open**, Wiwi NC-restricted |
| 2 | **Mannheim** | Mannheim | ~280 | VWL | NC-restricted |
| 3 | **LMU Munich** | Munich | ~34 | VWL + Wirtschaftswissenschaften | VWL **open**, Wiwi NC-restricted |
| 4 | **Cologne** | Cologne | ~151 | VWL | NC-restricted |
| 5 | **Goethe Frankfurt** | Frankfurt | ~151 | Wirtschaftswissenschaften | NC-restricted |
| 6 | **Humboldt Berlin** | Berlin | ~87 | VWL (two variants) | one NC-restricted, one **open** |
| 7 | **Freie Universität Berlin** | Berlin | ~98 | VWL | NC-restricted |
| 8 | **Münster** | Münster | ~193 | VWL | NC-restricted |
| 9 | **Tübingen** | Tübingen | ~100 | VWL + Wirtschaftswissenschaft | VWL **open** |
| 10 | **Heidelberg / Hamburg / Konstanz** | — | ~80 / ~125 / ~201 | VWL or Wiwi | Heidelberg & Hamburg NC-restricted, one Konstanz variant **open** |

*World positions show the best rank across QS/THE/ARWU and change from year to year; the admission mode is set afresh each semester. Verify it on the university's own page before applying.*

**Bonn** is widely regarded as Germany's strongest address in economics: theoretical depth, the Bonn Graduate School of Economics, a dense research culture. **Mannheim** is the centre of quantitative economics — closely tied to the ZEW institute, heavy on econometrics. **LMU Munich** combines a broad faculty with proximity to the ifo Institute. **Frankfurt** offers an advantage no ranking captures: the European Central Bank and the financial sector are in the same city, which is unmatched for internships and first jobs.

## The golden overlap: top faculty and no NC

This is the most valuable list for an applicant — strong **and** without a grade threshold:

| University | City | Why it matters |
|---|---|---|
| **Bonn** | Bonn | The country's strongest economics faculty, and its VWL is open admission |
| **LMU Munich** | Munich | Highest overall world position (~34), VWL open admission |
| **Tübingen** | Tübingen | Long-standing research university, VWL open admission |
| **Freiburg** | Freiburg | Home of the ordoliberal tradition, VWL open admission |
| **Göttingen** | Göttingen | Deep research history, VWL open admission |
| **Kiel** | Kiel | Kiel Institute for the World Economy in the same city, VWL open admission |

These six names show why "good university = high NC" does not hold in Germany. The NC is **not a quality measure but a demand measure**: if applications exceed places, an NC appears; if not, there is none.

## Easiest to enter: universities with open-admission German-taught economics

The catalogue lists **70 open-admission programmes at 43 institutions**. After removing distance learning, private providers, the armed forces university and teacher-training variants, these public universities remain:

**Bavaria:** LMU Munich · Regensburg · Würzburg · Bayreuth · Augsburg · Eichstätt-Ingolstadt · TH Deggendorf
**Baden-Württemberg:** Tübingen · Freiburg · Stuttgart (VWL) · Konstanz · Ulm
**North Rhine-Westphalia:** Bonn · TU Dortmund · Duisburg-Essen · Siegen · Paderborn
**Hesse & Rhineland-Palatinate:** TU Darmstadt · Gießen · Marburg · Kassel · Trier · RPTU Kaiserslautern-Landau
**Lower Saxony & the north:** Göttingen · Kiel · Osnabrück · Oldenburg · Bremen
**Eastern states:** Jena · Magdeburg · Chemnitz · Erfurt · Rostock · Greifswald · Schmalkalden
**Berlin:** Humboldt-Universität (one VWL variant)

If your budget is tight and certainty matters most, **Bavaria and the eastern states** are the smoothest route: no NC, no tuition, and — outside Munich — low living costs (Regensburg, Würzburg, Bayreuth, Jena, Magdeburg, Greifswald, Rostock).

## What "no NC" does **not** mean

Do not skip this section: open admission does **not** mean automatic acceptance. Four requirements remain at the door:

1. **German at C1 level.** These programmes are taught entirely in German. The standard proof is **DSH-2** or **TestDaF 4x4**. Going from zero to C1 realistically takes **12–24 months** — see the [German roadmap](/en/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh-en) and [TestDaF or DSH](/en/blog/testdaf-or-dsh-2026-german-language-exam-comparison-en).
2. **Recognition of your diploma.** A foreign school-leaving certificate is often not enough on its own; the anabin assessment decides, and in many cases a [Studienkolleg](/en/blog/studienkolleg-guide-2026-who-needs-it-which-course-which-school-en) year is required. Details: [anabin H+, H+-, H-](/en/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-en).
3. **uni-assist and deadlines.** Even an open-admission programme has **application deadlines** (usually 15 July for the winter semester, 15 January for the summer semester). International applications mostly go through uni-assist — [step-by-step guide](/en/blog/uni-assist-application-guide-a-z-your-step-by-step-path-en).
4. **The real selection happens in year one.** The door is wide, the corridor is narrow: *Mathematik I/II*, *Statistik* and *Mikroökonomie* thin the cohort out in the first year — and that bites hardest in quantitative faculties like Bonn and Mannheim.

## Money: the Baden-Württemberg trap

On the open-admission list, Tübingen, Freiburg, Konstanz, Stuttgart and Ulm look very attractive — but they all sit in **Baden-Württemberg**, the state that charges non-EU students roughly **€1,500 in tuition per semester**. Public universities in the other states charge no tuition at all; you pay only the **semester contribution of about €150–350**, which usually includes a transport ticket.

So for the very same open-admission VWL bachelor, the gap between Freiburg and Regensburg can reach **around €9,000** over the degree. *(As of early 2026; the amounts are revised annually — verify before applying.)*

## How to choose — a short decision frame

- **Aiming at a PhD or research:** Bonn, Mannheim, LMU, Cologne. Accept the NC, or use Bonn's open-admission VWL as your way in.
- **Aiming at certainty of a place:** Regensburg, Würzburg, Bayreuth, Jena, Magdeburg, Rostock, Greifswald — no NC, no tuition.
- **Wanting top tier and open admission:** Bonn, LMU, Tübingen, Freiburg, Göttingen, Kiel.
- **Aiming at a finance career:** Frankfurt — there the city builds as much of the career as the university does.
- **Prioritising internships and part-time work:** large job markets such as Cologne, Munich, Frankfurt, Berlin.

## Conclusion & honest advice

Studying economics in German is **not a battle over grade averages but a question of language and of choosing the right programme.** In two sentences: aim for **Bonn** as the strongest economics faculty — and the lucky part is that its VWL programme carries no NC; if certainty comes first, the open-admission programmes in **Bavaria and the eastern states** will take you in without any grade threshold. Put your real effort into **C1 German** and the first-year mathematics — that is where the selection actually happens.

Read on in this series: [studying VWL in Germany — the full guide](/en/blog/studying-economics-vwl-in-germany-as-a-foreigner-en), [English-taught economics masters without German](/en/blog/english-taught-economics-masters-in-germany-without-german-en), [working as an economist](/en/blog/working-as-an-economist-in-germany-research-policy-finance-en) and [the job market with a VWL degree](/en/blog/what-to-do-with-an-economics-vwl-degree-in-germany-job-market-en).

*This post was prepared in early 2026 on the basis of Hochschulkompass programme records. Admission mode, fees, deadlines and language requirements can change every semester; verify them on the university's current page before applying.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'study-economics-in-german-in-germany-best-and-nc-free-universities',
                'title' => 'Almanya\'da Almanca Ekonomi (VWL) Nerede Okunur? En Prestijliden NC\'siz Üniversitelere',
                'excerpt' => 'Almanca ekonomi/VWL lisansı nerede okunur: prestij sırasıyla Bonn, Mannheim, LMU, Köln, Frankfurt; ardından NC\'siz (zulassungsfrei) üniversitelerin tam listesi. Aynı üniversitede VWL ile Wirtschaftswissenschaften arasındaki kritik kabul farkı, Baden-Württemberg harç tuzağı ve dil/denklik şartları.',
                'meta_title' => 'Almanya\'da Almanca Ekonomi Nerede Okunur? Prestij + NC\'siz Liste',
                'meta_description' => 'Almanca VWL lisansı: en prestijli fakülteler (Bonn, Mannheim, LMU, Köln) ve NC\'siz üniversitelerin listesi. Kabul farkı, harçlar ve dil şartı (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug' => 'study-economics-in-german-in-germany-best-and-nc-free-universities-de',
                'title' => 'Wo kann man in Deutschland VWL auf Deutsch studieren? Von den Top-Fakultäten bis zu NC-freien Unis',
                'excerpt' => 'Wo man VWL auf Deutsch studiert: zuerst die Reihenfolge nach Renommee — Bonn, Mannheim, LMU, Köln, Frankfurt — danach die vollständige Liste der zulassungsfreien Hochschulen. Dazu der entscheidende Unterschied zwischen VWL und Wirtschaftswissenschaften, die Baden-Württemberg-Gebührenfalle und die Sprach- und Anerkennungsvoraussetzungen.',
                'meta_title' => 'VWL auf Deutsch studieren: Top-Fakultäten und NC-freie Unis',
                'meta_description' => 'Deutschsprachiger VWL-Bachelor: die stärksten Fakultäten (Bonn, Mannheim, LMU, Köln) und alle zulassungsfreien Hochschulen. Zulassung, Gebühren, Sprache (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug' => 'study-economics-in-german-in-germany-best-and-nc-free-universities-en',
                'title' => 'Where to Study Economics in German in Germany: From the Top Faculties to NC-Free Universities',
                'excerpt' => 'Where to study a German-taught economics (VWL) bachelor: first the prestige order — Bonn, Mannheim, LMU, Cologne, Frankfurt — then the full list of open-admission universities. Plus the crucial admission difference between VWL and Wirtschaftswissenschaften, the Baden-Württemberg tuition trap, and the language and recognition requirements.',
                'meta_title' => 'Study Economics in German: Top Faculties and NC-Free Universities',
                'meta_description' => 'German-taught economics (VWL) bachelor: the strongest faculties (Bonn, Mannheim, LMU, Cologne) and every open-admission university. Admission, fees, language (2026).',
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
            'study-economics-in-german-in-germany-best-and-nc-free-universities',
            'study-economics-in-german-in-germany-best-and-nc-free-universities-de',
            'study-economics-in-german-in-germany-best-and-nc-free-universities-en',
        ])->delete();
    }
};

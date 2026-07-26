<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Sanat & Tasarım Okumak (2026). Doğrulandı: Kunsthochschule vs FH/HAW
 * ayrımı; kabulde NC yok → portfolyo (Mappe) + Eignungsprüfung belirleyici; çoğu program C1 Almanca.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'd4e10000-1111-4b0f-9f10-dd0bee11bb01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da sanat ve tasarım okumak, tıp ya da mühendislik okumaktan **tamamen farklı bir mantıkla** işler. Not ortalaman (Abitur/lise diploması) neredeyse hiç önemli değildir; belirleyici olan **portfolyondur (Mappe)** ve çoğu okulda gireceğin **yetenek sınavıdır (Eignungsprüfung / Aufnahmeprüfung)**. Bu rehber, uluslararası bir öğrenci olarak alanları, kurumları, dil şartını ve başvuru sürecini dürüstçe anlatıyor.

## Hangi alanlar var?

Almanya'da "sanat & tasarım" tek bir şey değil; birbirinden çok farklı gelir ve kariyer beklentisi olan dallar var:

- **Freie Kunst (güzel sanatlar):** Resim, heykel, yeni medya. Tutku yolu — sanatsal olarak zengin ama **gelir açısından güvencesiz**.
- **Grafik / Kommunikationsdesign (iletişim tasarımı):** Marka, tipografi, editoryal. İstihdam orta düzey.
- **Industrie- / Produktdesign (endüstriyel/ürün tasarımı):** Sanayiyle güçlü bağ; Almanya'da iyi bir yol.
- **Modedesign (moda tasarımı):** Yaratıcı ama piyasası değişken.
- **Mediendesign / UX-UI / Digital Design:** **Şu an en çok büyüyen ve en iyi ödeyen** alan.
- **İllüstrasyon** ve bunların kesişimleri.

Hangi dalı seçeceğin, hem okuyacağın kurumu hem de mezuniyet sonrası gerçekliğini belirler.

## Kurumlar: Kunsthochschule mı, FH/HAW mı?

İki temel kurum tipi var ve aralarındaki fark önemli:

| Özellik | Kunsthochschule / Kunstakademie | FH / HAW (Uygulamalı Bilimler) |
|---|---|---|
| Odak | Sanatsal, kavramsal, özerk | Uygulamalı, meslek/piyasa odaklı |
| Tipik alanlar | Freie Kunst, güzel sanatlar, tasarım | Grafik, ürün, UX, medya tasarımı |
| Örnek kurumlar | **Kunstakademie Düsseldorf, UdK Berlin, HfBK Hamburg, HfG Karlsruhe, Bauhaus Weimar, Städelschule Frankfurt** | **HfG Offenbach, Burg Giebichenstein Halle, Folkwang Essen, HAW Hamburg** |
| Atmosfer | Klas/atölye, profesöre bağlı | Modül/kredi, yapılandırılmış |
| Kabul | **Portfolyo + Eignungsprüfung** | **Portfolyo + Eignungsprüfung** |

**Kalın gerçek:** UdK Berlin ve Kunstakademie Düsseldorf gibi tepe akademilere giriş **son derece rekabetçidir** — kontenjanlar az, başvuru çok. FH/HAW'lar genelde daha yapılandırılmış ve piyasa yönelimlidir; bir işe girmeyi hedefliyorsan bu genellikle daha güvenli bir zemindir.

## En kritik fark: NC yok → portfolyo + Eignungsprüfung

Almanya'da çoğu popüler bölüm **Numerus Clausus (NC)** ile, yani not ortalamasıyla kontenjan sınırlar. **Sanat ve tasarımda durum farklıdır: pratikte NC yoktur.** Yerine geçen şey:

1. **Portfolyo (Mappe):** Genellikle 15-25 özgün çalışma. **Başarının anahtarı budur.** Sadece bitmiş işler değil; fikir, süreç, deneme ve kişisel bakış açısı görmek isterler.
2. **Eignungsprüfung / Aufnahmeprüfung (yetenek sınavı):** Ön elemeyi geçersen çoğu okul seni bir sınava/mülakata çağırır; yerinde çizim, ödev veya sözlü değerlendirme olabilir.

Yani lise notların vasat olsa bile **güçlü bir portfolyoyla** tepe bir okula girebilirsin — ama zayıf bir portfolyoyla mükemmel notlar seni kurtarmaz. Portfolyonu nasıl hazırlayacağın başlı başına bir konu; ayrıntılı adım adım rehberimiz için [portfolyo (Mappe) nasıl hazırlanır](/tr/blog/how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools) yazısına mutlaka bak.

## Dil: C1 Almanca mı, İngilizce mi?

**Programların büyük çoğunluğu Almancadır** ve genellikle **C1 seviyesi** (DSH-2 veya TestDaF) istenir. Atölye ve eleştiri (Kritik) kültürü yoğun sözelidir; Almanca burada gerçekten belirleyicidir.

Bununla birlikte, özellikle **tasarım, medya ve UX** tarafında bazı **İngilizce yüksek lisans** programları ve bazı özel okullar var. Almancan yoksa bu yol mümkün ama sınırlıdır — detaylar için [Almancasız İngilizce tasarım & medya master](/tr/blog/english-taught-design-and-media-masters-in-germany-without-german) yazısına göz at.

## Başvuru: doğrudan mı, uni-assist mi?

Süreç kuruma göre değişir:

- Birçok Kunsthochschule başvuruyu **doğrudan kendisi** alır; portfolyo teslimi ve Eignungsprüfung takvimini okulun kendisi belirler.
- Bazı FH/HAW ve üniversiteler ise **uni-assist** üzerinden ön belge kontrolü ister.
- **Başvuru tarihleri sanatta erken olabilir** (portfolyo teslim tarihleri bazen dönem başlamadan aylar önce). Takvimi erken kontrol et.

Kurum seçerken sadece isim/prestij peşinde koşma; ekol, profesör ve mezun profili senin işine daha çok uyabilir. Almanya'da prestijin nasıl işlediğini [üniversite prestiji ve sıralamaları](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) yazısında ele aldık.

## Ücret & yaşam maliyeti

- **Kamu sanat/tasarım okulları neredeyse ücretsizdir:** genelde **dönem başına ~150-350€** (Semesterbeitrag). İstisna: **Baden-Württemberg'de AB-dışı öğrenciler için dönem başına ~1.500€** öğrenim ücreti (2025/2026 itibarıyla, yaklaşık — doğrula).
- **Bazı özel tasarım okulları pahalıdır** (yıllık binlerce euro).
- Yaşam gideri şehre göre değişir; büyük yaratıcı merkezler (Berlin, Hamburg, Münih) daha pahalıdır.

Sanat/tasarım okumak ile mimarlık okumanın kabul ve kariyer mantığı bazı yönlerden benzer; karşılaştırmak istersen [Almanya'da mimarlık okumak](/tr/blog/studying-architecture-in-germany-as-a-foreigner) yazısı iyi bir komşu.

## Sonuç & dürüst tavsiye

Almanya sanat & tasarım için dünya çapında güçlü bir yer — kamu okulları neredeyse ücretsiz, ekol çeşitliliği yüksek. Ama dürüst olalım:

- **Portfolyo her şeydir.** Erken başla; bir Mappenkurs (portfolyo hazırlık kursu) düşün.
- **Tepe akademiler çok rekabetçi.** Aynı anda birkaç okula başvurmayı planla.
- **Alan seçimi geliri belirler:** **UX/UI, dijital ve ürün tasarımı istihdam edilebilir ve iyi ödüyor** (~45-60k+); grafik/iletişim tasarımı orta; **Freie Kunst tutku yoludur ve geliri düşüktür** — bunu bilerek gir. Maaş ve iş piyasası gerçeği için [Almanya'da tasarımcı olarak çalışmak](/tr/blog/working-as-a-designer-in-germany-careers-salary-and-reality) yazısına bak.
- Çoğu program için **C1 Almanca** gerçek bir şart; erkenden dil çalış.

*Bu yazıdaki sayılar, program şartları ve ücretler 2025/2026 itibarıyla yaklaşık değerlerdir ve değişebilir. Başvurmadan önce ilgili okulun ve resmi kaynakların güncel bilgilerini mutlaka doğrula.*
MD;
        $deBody = <<<'MD'
Kunst und Design in Deutschland zu studieren funktioniert nach einer **völlig anderen Logik** als Medizin oder Ingenieurwesen. Deine Abiturnote spielt fast keine Rolle; entscheidend sind deine **Mappe (Portfolio)** und die **Eignungsprüfung / Aufnahmeprüfung** an den meisten Hochschulen. Dieser Leitfaden erklärt dir als internationaler Studentin die Fachrichtungen, Institutionen, die Sprachfrage und den Bewerbungsweg – ehrlich.

## Welche Fachrichtungen gibt es?

„Kunst & Design" ist kein einzelnes Fach, sondern eine Familie sehr unterschiedlicher Richtungen mit ganz verschiedenen Einkommens- und Karrierechancen:

- **Freie Kunst:** Malerei, Bildhauerei, neue Medien. Ein Weg aus Leidenschaft – künstlerisch reich, aber **finanziell unsicher**.
- **Grafik- / Kommunikationsdesign:** Marke, Typografie, Editorial. Mittlere Beschäftigungslage.
- **Industrie- / Produktdesign:** Enge Verbindung zur Industrie; in Deutschland ein guter Weg.
- **Modedesign:** Kreativ, aber schwankender Markt.
- **Mediendesign / UX-UI / Digital Design:** Aktuell der **am stärksten wachsende und am besten bezahlte** Bereich.
- **Illustration** und deren Schnittstellen.

Welche Richtung du wählst, bestimmt sowohl die Hochschule als auch deine Realität nach dem Abschluss.

## Institutionen: Kunsthochschule oder FH/HAW?

Es gibt zwei Grundtypen, und der Unterschied ist wichtig:

| Merkmal | Kunsthochschule / Kunstakademie | FH / HAW (angewandte Wissenschaften) |
|---|---|---|
| Fokus | Künstlerisch, konzeptuell, autonom | Angewandt, berufs-/marktorientiert |
| Typische Fächer | Freie Kunst, bildende Kunst, Design | Grafik, Produkt, UX, Mediendesign |
| Beispiele | **Kunstakademie Düsseldorf, UdK Berlin, HfBK Hamburg, HfG Karlsruhe, Bauhaus Weimar, Städelschule Frankfurt** | **HfG Offenbach, Burg Giebichenstein Halle, Folkwang Essen, HAW Hamburg** |
| Atmosphäre | Klasse/Atelier, professorabhängig | Module/Credits, strukturiert |
| Zulassung | **Mappe + Eignungsprüfung** | **Mappe + Eignungsprüfung** |

**Fette Wahrheit:** Der Zugang zu Spitzenakademien wie der UdK Berlin oder der Kunstakademie Düsseldorf ist **äußerst kompetitiv** – wenige Plätze, viele Bewerbungen. FH/HAW sind meist strukturierter und marktnäher; wenn du auf einen Job zielst, ist das oft der sicherere Boden.

## Der wichtigste Unterschied: kein NC → Mappe + Eignungsprüfung

Die meisten beliebten Studiengänge in Deutschland begrenzen die Plätze über den **Numerus Clausus (NC)**, also die Note. **In Kunst und Design ist das anders: praktisch gibt es keinen NC.** Stattdessen zählt:

1. **Die Mappe (Portfolio):** meist 15-25 eigene Arbeiten. **Das ist der Schlüssel zum Erfolg.** Gesucht werden nicht nur fertige Werke, sondern Ideen, Prozess, Experimente und deine persönliche Perspektive.
2. **Eignungsprüfung / Aufnahmeprüfung:** Nach der Vorauswahl lädt dich die Hochschule zu einer Prüfung/einem Gespräch ein – Zeichnen vor Ort, Aufgaben oder ein mündliches Verfahren.

Auch mit mittelmäßigen Schulnoten kannst du also mit einer **starken Mappe** an eine Spitzenhochschule kommen – aber mit einer schwachen Mappe retten dich perfekte Noten nicht. Wie du deine Mappe aufbaust, ist ein Thema für sich; lies unbedingt unseren Schritt-für-Schritt-Leitfaden [Mappe für deutsche Kunst- und Designhochschulen vorbereiten](/de/blog/how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools-de).

## Sprache: C1 Deutsch oder Englisch?

**Die große Mehrheit der Programme ist auf Deutsch** und verlangt meist **Niveau C1** (DSH-2 oder TestDaF). Die Atelier- und Kritik-Kultur ist stark mündlich; Deutsch ist hier wirklich entscheidend.

Dennoch gibt es besonders im Bereich **Design, Medien und UX** einige **englischsprachige Masterprogramme** und einige Privathochschulen. Ohne Deutsch ist dieser Weg möglich, aber begrenzt – Details findest du im Beitrag [englischsprachige Design- und Medien-Master ohne Deutsch](/de/blog/english-taught-design-and-media-masters-in-germany-without-german-de).

## Bewerbung: direkt oder über uni-assist?

Der Ablauf hängt von der Hochschule ab:

- Viele Kunsthochschulen nehmen die Bewerbung **direkt selbst** entgegen; Mappenabgabe und Termin der Eignungsprüfung legt die Hochschule fest.
- Manche FH/HAW und Universitäten verlangen eine Vorprüfung der Unterlagen über **uni-assist**.
- **Bewerbungsfristen in der Kunst sind oft früh** (Mappenabgabe teils Monate vor Semesterbeginn). Prüfe den Zeitplan früh.

Lauf bei der Wahl nicht nur dem Namen/Prestige hinterher; Schule, Professorin und Absolventenprofil passen vielleicht besser zu deinem Ziel. Wie Prestige in Deutschland funktioniert, behandeln wir im Beitrag [Uni-Prestige und Rankings in Deutschland](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de).

## Kosten & Lebenshaltung

- **Öffentliche Kunst-/Designhochschulen sind nahezu gebührenfrei:** meist **~150-350€ pro Semester** (Semesterbeitrag). Ausnahme: **in Baden-Württemberg ~1.500€ pro Semester für Nicht-EU-Studierende** (Stand 2025/2026, ungefähr – bitte prüfen).
- **Einige Privathochschulen sind teuer** (mehrere tausend Euro pro Jahr).
- Die Lebenshaltung variiert je Stadt; große Kreativzentren (Berlin, Hamburg, München) sind teurer.

Die Zulassungs- und Karrierelogik von Kunst/Design ähnelt in mancher Hinsicht dem Architekturstudium; zum Vergleich ist [Architektur in Deutschland studieren](/de/blog/studying-architecture-in-germany-as-a-foreigner-de) ein guter Nachbar.

## Fazit & ehrlicher Rat

Deutschland ist für Kunst & Design weltweit stark – öffentliche Hochschulen fast gebührenfrei, große Vielfalt der Schulen. Aber sei ehrlich zu dir:

- **Die Mappe ist alles.** Fang früh an; denk über einen Mappenkurs nach.
- **Spitzenakademien sind sehr kompetitiv.** Plane, dich gleichzeitig an mehreren Schulen zu bewerben.
- **Die Fachwahl bestimmt das Einkommen:** **UX/UI, Digital- und Produktdesign sind gut vermittelbar und gut bezahlt** (~45-60k+); Grafik-/Kommunikationsdesign mittel; **Freie Kunst ist ein Weg aus Leidenschaft mit niedrigem Einkommen** – geh bewusst hinein. Zur Gehalts- und Arbeitsmarktrealität lies [als Designerin in Deutschland arbeiten](/de/blog/working-as-a-designer-in-germany-careers-salary-and-reality-de).
- Für die meisten Programme ist **C1 Deutsch** eine echte Voraussetzung; lerne früh die Sprache.

*Die Zahlen, Zulassungsbedingungen und Gebühren in diesem Beitrag sind ungefähre Werte für 2025/2026 und können sich ändern. Prüfe vor der Bewerbung unbedingt die aktuellen Angaben der jeweiligen Hochschule und der offiziellen Quellen.*
MD;
        $enBody = <<<'MD'
Studying art and design in Germany works on a **completely different logic** than medicine or engineering. Your school grades matter almost not at all; what decides everything is your **portfolio (Mappe)** and the **aptitude test (Eignungsprüfung / Aufnahmeprüfung)** at most schools. This guide walks you, as an international student, through the fields, the institutions, the language question and the application route — honestly.

## Which fields are there?

"Art & design" is not one thing; it is a family of very different paths with very different income and career outlooks:

- **Freie Kunst (fine art):** Painting, sculpture, new media. A passion path — artistically rich but **financially insecure**.
- **Grafik / Kommunikationsdesign (graphic/communication design):** Brand, typography, editorial. Mid-level employability.
- **Industrie- / Produktdesign (industrial/product design):** Strong ties to industry; a solid path in Germany.
- **Modedesign (fashion design):** Creative but a volatile market.
- **Mediendesign / UX-UI / Digital Design:** Currently the **fastest-growing and best-paid** area.
- **Illustration** and the crossovers between these.

The direction you pick shapes both the institution you attend and your reality after graduation.

## Institutions: Kunsthochschule or FH/HAW?

There are two basic types, and the difference matters:

| Feature | Kunsthochschule / Kunstakademie | FH / HAW (universities of applied sciences) |
|---|---|---|
| Focus | Artistic, conceptual, autonomous | Applied, profession/market-oriented |
| Typical fields | Freie Kunst, fine art, design | Graphic, product, UX, media design |
| Examples | **Kunstakademie Düsseldorf, UdK Berlin, HfBK Hamburg, HfG Karlsruhe, Bauhaus Weimar, Städelschule Frankfurt** | **HfG Offenbach, Burg Giebichenstein Halle, Folkwang Essen, HAW Hamburg** |
| Atmosphere | Class/atelier, professor-driven | Modules/credits, structured |
| Admission | **Portfolio + Eignungsprüfung** | **Portfolio + Eignungsprüfung** |

**Bold fact:** Getting into top academies like UdK Berlin or Kunstakademie Düsseldorf is **extremely competitive** — few places, many applications. FH/HAW schools are usually more structured and market-oriented; if you are aiming for a job, that is often the safer ground.

## The most critical difference: no NC → portfolio + Eignungsprüfung

Most popular degrees in Germany cap places via the **Numerus Clausus (NC)** — that is, your grade average. **In art and design it is different: in practice there is no NC.** What replaces it:

1. **The portfolio (Mappe):** usually 15-25 original works. **This is the key to success.** They want to see not only finished pieces but ideas, process, experiments and your personal point of view.
2. **Eignungsprüfung / Aufnahmeprüfung (aptitude test):** if you pass the pre-selection, most schools invite you to a test/interview — drawing on site, tasks, or an oral assessment.

So even with mediocre school grades you can get into a top school with a **strong portfolio** — but a weak portfolio is not saved by perfect grades. How you build your portfolio is a topic of its own; be sure to read our step-by-step guide, [how to prepare a portfolio (Mappe)](/en/blog/how-to-prepare-a-portfolio-mappe-for-german-art-and-design-schools-en).

## Language: C1 German or English?

**The vast majority of programs are in German** and usually require **level C1** (DSH-2 or TestDaF). The atelier and critique (Kritik) culture is highly verbal; German is genuinely decisive here.

That said, especially in **design, media and UX** there are some **English-taught master's** programs and some private schools. Without German this route is possible but limited — for details see [English-taught design & media master's without German](/en/blog/english-taught-design-and-media-masters-in-germany-without-german-en).

## Application: direct or via uni-assist?

The process depends on the institution:

- Many Kunsthochschulen take the application **directly themselves**; the school sets the portfolio deadline and the Eignungsprüfung date.
- Some FH/HAW and universities require a document pre-check through **uni-assist**.
- **Application deadlines in art are often early** (portfolio deadlines sometimes months before the semester starts). Check the timeline early.

When choosing, do not just chase the name/prestige; the school, the professor and the graduate profile may fit your goal better. How prestige works in Germany is covered in [how university prestige and rankings work](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en).

## Fees & cost of living

- **Public art/design schools are nearly free:** usually **~€150-350 per semester** (Semesterbeitrag). Exception: **in Baden-Württemberg ~€1,500 per semester for non-EU students** (as of 2025/2026, approximate — verify).
- **Some private design schools are expensive** (several thousand euros a year).
- Living costs vary by city; big creative hubs (Berlin, Hamburg, Munich) are pricier.

The admission and career logic of art/design resembles studying architecture in some ways; if you want to compare, [studying architecture in Germany](/en/blog/studying-architecture-in-germany-as-a-foreigner-en) is a good neighbor.

## Conclusion & honest advice

Germany is world-class for art & design — public schools nearly free, a wide variety of schools. But be honest with yourself:

- **The portfolio is everything.** Start early; consider a Mappenkurs (portfolio prep course).
- **Top academies are very competitive.** Plan to apply to several schools at once.
- **Field choice determines income:** **UX/UI, digital and product design are employable and well paid** (~€45-60k+); graphic/communication design is mid; **Freie Kunst is a passion path with low income** — go in with eyes open. For salary and job-market reality, see [working as a designer in Germany](/en/blog/working-as-a-designer-in-germany-careers-salary-and-reality-en).
- For most programs **C1 German** is a real requirement; study the language early.

*The figures, admission requirements and fees in this article are approximate values for 2025/2026 and may change. Before applying, always verify the current information from the relevant school and official sources.*
MD;

        $variants = [
            'tr' => ['slug'=>'studying-art-and-design-in-germany-as-a-foreigner',    'title'=>'Almanya\'da Sanat & Tasarım Okumak: Uluslararası Öğrenci Rehberi (2026)', 'excerpt'=>'Almanya\'da sanat & tasarım okumak: alanlar, Kunsthochschule vs FH/HAW, NC yok → portfolyo (Mappe) + Eignungsprüfung, dil şartı, ücret ve dürüst kariyer tavsiyesi (2026).', 'meta_title'=>'Almanya\'da Sanat & Tasarım Okumak (2026) | Rehber', 'meta_description'=>'Almanya\'da sanat & tasarım: NC yok, portfolyo (Mappe) + Eignungsprüfung belirleyici. Kunsthochschule vs FH/HAW, C1 Almanca, ücret ve dürüst tavsiye.', 'body'=>$trBody],
            'de' => ['slug'=>'studying-art-and-design-in-germany-as-a-foreigner-de', 'title'=>'Kunst & Design in Deutschland studieren: Leitfaden für internationale Studierende (2026)',        'excerpt'=>'Kunst & Design in Deutschland studieren: Fachrichtungen, Kunsthochschule vs. FH/HAW, kein NC → Mappe + Eignungsprüfung, Sprache, Kosten und ehrlicher Rat (2026).',   'meta_title'=>'Kunst & Design in Deutschland studieren (2026) | Guide',  'meta_description'=>'Kunst & Design in Deutschland: kein NC, Mappe + Eignungsprüfung entscheidend. Kunsthochschule vs. FH/HAW, C1 Deutsch, Kosten und ehrlicher Rat.',   'body'=>$deBody],
            'en' => ['slug'=>'studying-art-and-design-in-germany-as-a-foreigner-en', 'title'=>'Studying Art & Design in Germany: A Guide for International Students (2026)',        'excerpt'=>'Studying art & design in Germany: fields, Kunsthochschule vs FH/HAW, no NC → portfolio (Mappe) + Eignungsprüfung, language, fees and honest career advice (2026).',   'meta_title'=>'Studying Art & Design in Germany (2026) | Guide',  'meta_description'=>'Art & design in Germany: no NC, portfolio (Mappe) + Eignungsprüfung decide. Kunsthochschule vs FH/HAW, C1 German, fees and honest advice.',   'body'=>$enBody],
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
        Post::whereIn('slug', ['studying-art-and-design-in-germany-as-a-foreigner', 'studying-art-and-design-in-germany-as-a-foreigner-de', 'studying-art-and-design-in-germany-as-a-foreigner-en'])->delete();
    }
};

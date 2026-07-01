<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Public vs private business schools in Germany — Mannheim, WHU, Frankfurt School (2026).
 * Doğrulandı: Kamu işletme üniversiteleri (Mannheim/Köln/Goethe/LMU/Münster) neredeyse ücretsiz (~150-350€/dönem
 * katkı; BW non-EU ~1.500€/dönem), rekabetçi NC; özel okullar (WHU/Frankfurt School/ESMT/HHL) ~20-40k€, İngilizce,
 * güçlü sanayi bağı, MBA. Rakamlar 2025/2026 yaklaşık — yıla göre değişir, resmi kaynaktan doğrula.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'b2a20000-2222-4baa-9f30-aa01bb02ee02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da işletme okumaya karar verdin — ama önünde çoğu kişinin fark etmeden geçtiği **kritik bir çatal** var: **ücretsiz kamu üniversitesi mi, yoksa on binlerce euroluk özel işletme okulu mu?** Mannheim, WHU, Frankfurt School gibi adlar sürekli karşına çıkacak. Bu yazı, ikisini dürüstçe karşılaştırıyor ve "hangisi bana uygun" sorusuna somut bir pusula veriyor.

## Karar: ücretsiz kamu vs pahalı özel

Almanya'nın işletme eğitimindeki temel gerçek şu: **kaliteli eğitim için mutlaka para ödemen gerekmiyor.** Kamu üniversiteleri neredeyse ücretsizdir (sadece dönem katkı payı, *2025/2026 itibarıyla yaklaşık ~150–350€/dönem*; Baden-Württemberg'de AB-dışı öğrenciler için ~1.500€/dönem — doğrula). Özel işletme okulları ise *~20.000–40.000€* program ücreti ister.

O halde soru "hangisi daha iyi" değil: **"Fazladan ödediğin paranın karşılığında ne alıyorsun?"** Kamu tarafında bedava, güçlü akademik itibar ve geniş tanınırlık var; özel tarafında İngilizce program, küçük sınıf, yoğun network ve hızlı kariyer ivmesi var. İkisi de doğru öğrenci için doğru tercih olabilir.

## Tepe kamu işletme üniversiteleri (NC rekabetçi)

Kamu tarafında Almanya'nın işletme ağırlığı bellidir. Bunlar ücretsiz ama **rekabetçi** — özellikle Bachelor'da NC (numerus clausus / not barajı) yüksek olur.

| Üniversite | Öne çıkan | Dil (Bachelor) | Ücret (yaklaşık, 2025/26) |
|---|---|---|---|
| **Universität Mannheim** | Almanya'nın #1 işletme (BWL) adresi | Genelde Almanca | Dönem katkısı (~150€) |
| **Uni Köln** | Büyük, güçlü BWL fakültesi | Almanca | Dönem katkısı |
| **Goethe Uni Frankfurt** | Finans merkezinin kalbinde | Almanca | Dönem katkısı |
| **LMU München** | Köklü, prestijli | Almanca | Dönem katkısı |
| **Uni Münster** | Saygın BWL geleneği | Almanca | Dönem katkısı |

**Kalın gerçek:** Kamu Bachelor'ları genelde **Almanca (C1)** ister; İngilizce Bachelor kamuda nadirdir. Buna karşılık **İngilizce Master'lar** (Management, Finance, Marketing, Business Analytics) kamu ünilerinde de vardır ve **neredeyse ücretsizdir**. Almancasız gelmeyi düşünüyorsan bu ayrımı [İngilizce İşletme/Management master programları](/tr/blog/english-taught-business-management-masters-in-germany-without-german) rehberinde ayrıntısıyla oku.

## Özel işletme okulları: ücret, İngilizce, sanayi bağı

Özel okullar pahalıdır ama karşılığında farklı bir paket sunar: İngilizce eğitim, küçük sınıf, güçlü şirket/mezun ağı ve genelde hazır bir kariyer altyapısı.

| Okul | Öne çıkan | Dil | Ücret (yaklaşık, 2025/26) |
|---|---|---|---|
| **WHU – Otto Beisheim** | Almanya'nın en prestijli işletme okulu; güçlü network | İngilizce | Yüksek (~30–40k+€ program) |
| **Frankfurt School of Finance & Management** | Finans/bankacılıkta güçlü, Frankfurt merkezli | İngilizce | ~20–40k€ |
| **ESMT Berlin** | Uluslararası, MBA/Master, sanayi bağlantılı | İngilizce | ~20–40k€ |
| **HHL Leipzig** | Girişimcilik + management vurgusu | İngilizce | ~20–30k€ |

*Ücretler 2025/2026 itibarıyla yaklaşıktır ve programa göre değişir — resmi kaynaktan doğrula.* Bu okulların en güçlü yanı diploma değil, **kapı açan mezun ağı ve şirket ilişkileridir** (danışmanlık, finans, DAX şirketleri).

## Ne zaman özel gerçekten değer?

Özel okul her zaman "para israfı" değildir; bazı durumlarda mantıklıdır:

- **İngilizce okumak istiyorsan** (özellikle Bachelor'da) ve Almanca'yı hızlı öğrenemeyeceksen.
- **Network ve kariyer hızı** senin için para kadar değerliyse (danışmanlık/finans hedefi).
- **MBA yapıyorsan** — deneyimli profesyonel olarak network ve marka değeri belirleyici.
- **Küçük sınıf + yoğun mentorluk** öğrenme tarzına uyuyorsa.

Tersine, **Almanca öğrenebiliyorsan ve akademik derinlik/araştırma istiyorsan**, ücretsiz kamu üniversitesi çoğu durumda daha akıllıcadır. Genel bir denge için [kamu vs özel üniversite: dengeli karşılaştırma](/tr/blog/public-vs-private-universities-germany-balanced-comparison) yazısına da bak.

## NC ve başvuru gerçeği

Kamu tarafında en büyük engel **NC**'dir: Mannheim/Köln gibi tepe adreslerde işletme Bachelor'ı çok rekabetçidir ve not barajı yüksektir. Ama panik yok — birçok **Fachhochschule (FH/HAW)** işletme programı NC-siz ya da daha esnek kabul sunar ve iş piyasasında pratik olarak çok değerlidir.

Özel okullarda ise kabul "paranın" değil genelde **başvuru + mülakat + bazen GMAT/GRE** işidir; ödeme gücü tek başına giriş garantisi değildir. Her iki tarafta da başvuruyu erken planla.

## MBA seçeneği

MBA, lisans hemen sonrası için değil, **birkaç yıl iş deneyimi olan profesyoneller** içindir. Almanya'da güçlü MBA adresleri özel tarafta yoğunlaşır: **WHU, Mannheim Business School, ESMT Berlin, Frankfurt School**. Burada ödediğin ücretin karşılığı büyük ölçüde **network, marka ve kariyer sıçramasıdır** — kariyer hedefin buna değiyorsa mantıklıdır.

## Sonuç & dürüst tavsiye

- **Bütçe kısıtlıysa ve Almanca öğrenebiliyorsan:** kamu üniversitesi (Mannheim/Köln/Goethe/LMU/Münster) neredeyse ücretsiz ve fazlasıyla saygın — çoğu öğrenci için en akıllı yol.
- **İngilizce okumak, güçlü network ve hızlı kariyer istiyorsan (ve bütçen varsa):** WHU/Frankfurt School/ESMT gibi özel okullar gerçek değer sunabilir.
- **Diploma sonrası hedefin kritik:** danışmanlık/finans için Almanca ciddi avantajdır — bunu [Almanya'da işletme, danışmanlık ve finans'ta çalışmak](/tr/blog/working-in-business-consulting-finance-in-germany-blue-card-salary) ve [BWL diplomasıyla iş piyasası](/tr/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market) yazılarında dürüstçe ele alıyoruz. Master vs iş arama vizesi rotası için: [Almanya master mı, iş arama vizesi mi?](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career).

Etiketin değil hedefin belirlesin: "prestijli okul" değil, **sana uygun okul** kazandırır.

---
*2025/2026 itibarıyla hazırlanmıştır. Ücretler, NC barajları ve kabul koşulları üniversiteye ve döneme göre değişir — başvurudan önce resmi kaynaklardan doğrula.*
MD;

        $deBody = <<<'MD'
Du hast dich entschieden, in Deutschland BWL (Betriebswirtschaftslehre) zu studieren — aber vor dir liegt eine **entscheidende Weggabelung**, die viele übersehen: **kostenlose staatliche Universität oder private Business School für zehntausende Euro?** Namen wie Mannheim, WHU und Frankfurt School werden dir ständig begegnen. Dieser Artikel vergleicht beide ehrlich und gibt dir einen konkreten Kompass für die Frage „Was passt zu mir?".

## Die Entscheidung: kostenlos staatlich vs. teuer privat

Die Grundwahrheit der BWL-Ausbildung in Deutschland lautet: **Für gute Bildung musst du nicht zwingend bezahlen.** Staatliche Universitäten sind fast kostenlos (nur der Semesterbeitrag, *Stand 2025/2026 ungefähr ~150–350€/Semester*; in Baden-Württemberg für Nicht-EU-Studierende ~1.500€/Semester — bitte prüfen). Private Business Schools verlangen dagegen *~20.000–40.000€* Programmgebühr.

Die Frage ist also nicht „Was ist besser?", sondern: **„Was bekommst du für das Geld, das du zusätzlich zahlst?"** Auf der staatlichen Seite: kostenlos, starker akademischer Ruf, breite Anerkennung. Auf der privaten Seite: englischsprachige Programme, kleine Klassen, dichtes Netzwerk und schnellere Karrieredynamik. Beides kann für den richtigen Studierenden die richtige Wahl sein.

## Top staatliche BWL-Universitäten (NC kompetitiv)

Auf der staatlichen Seite ist Deutschlands BWL-Landschaft klar. Diese sind kostenlos, aber **kompetitiv** — besonders im Bachelor ist der NC (Numerus Clausus) hoch.

| Universität | Stärke | Sprache (Bachelor) | Kosten (ungefähr, 2025/26) |
|---|---|---|---|
| **Universität Mannheim** | Deutschlands BWL-Adresse Nr. 1 | Meist Deutsch | Semesterbeitrag (~150€) |
| **Uni Köln** | Große, starke BWL-Fakultät | Deutsch | Semesterbeitrag |
| **Goethe-Uni Frankfurt** | Im Herzen des Finanzzentrums | Deutsch | Semesterbeitrag |
| **LMU München** | Traditionsreich, renommiert | Deutsch | Semesterbeitrag |
| **Uni Münster** | Angesehene BWL-Tradition | Deutsch | Semesterbeitrag |

**Wichtige Wahrheit:** Staatliche Bachelor verlangen meist **Deutsch (C1)**; englischsprachige Bachelor sind staatlich selten. Dafür gibt es **englischsprachige Master** (Management, Finance, Marketing, Business Analytics) auch an staatlichen Unis, und die sind **nahezu kostenlos**. Wenn du ohne Deutsch kommen möchtest, lies die Details im Ratgeber [englischsprachige BWL-/Management-Master](/de/blog/english-taught-business-management-masters-in-germany-without-german-de).

## Private Business Schools: Kosten, Englisch, Industriekontakte

Private Schools sind teuer, bieten aber ein anderes Paket: englischsprachige Lehre, kleine Klassen, starke Unternehmens-/Alumni-Netzwerke und meist eine fertige Karriereinfrastruktur.

| School | Stärke | Sprache | Kosten (ungefähr, 2025/26) |
|---|---|---|---|
| **WHU – Otto Beisheim** | Deutschlands prestigeträchtigste Business School; starkes Netzwerk | Englisch | Hoch (~30–40k+€ Programm) |
| **Frankfurt School of Finance & Management** | Stark in Finance/Banking, Standort Frankfurt | Englisch | ~20–40k€ |
| **ESMT Berlin** | International, MBA/Master, industrienah | Englisch | ~20–40k€ |
| **HHL Leipzig** | Fokus auf Entrepreneurship + Management | Englisch | ~20–30k€ |

*Die Gebühren sind Stand 2025/2026 ungefähr und variieren je nach Programm — bitte aus offizieller Quelle prüfen.* Die größte Stärke dieser Schools ist nicht das Diplom, sondern das **türöffnende Alumni-Netzwerk und die Unternehmenskontakte** (Beratung, Finance, DAX-Konzerne).

## Wann lohnt sich privat wirklich?

Eine private School ist nicht immer „Geldverschwendung"; in bestimmten Fällen ist sie sinnvoll:

- **Wenn du auf Englisch studieren willst** (besonders im Bachelor) und Deutsch nicht schnell lernen kannst.
- **Wenn Netzwerk und Karrieretempo** dir so viel wert sind wie das Geld (Ziel Beratung/Finance).
- **Wenn du einen MBA machst** — als erfahrene:r Profi sind Netzwerk und Markenwert entscheidend.
- **Wenn kleine Klassen + intensives Mentoring** zu deinem Lernstil passen.

Umgekehrt gilt: **Wenn du Deutsch lernen kannst und akademische Tiefe/Forschung willst**, ist die kostenlose staatliche Universität meist die klügere Wahl. Wie stark der Faktor Sprache im Berufsleben ist, siehst du auch am Beispiel [Arbeiten in IT/Tech in Deutschland](/de/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-de).

## NC und die Bewerbungsrealität

Auf der staatlichen Seite ist die größte Hürde der **NC**: An Top-Adressen wie Mannheim/Köln ist der BWL-Bachelor sehr kompetitiv und der Notenschnitt hoch. Aber keine Panik — viele **Fachhochschulen (FH/HAW)** bieten BWL-Programme ohne NC oder mit flexibleren Zugangsvoraussetzungen an und sind auf dem Arbeitsmarkt praktisch sehr wertvoll.

An privaten Schools ist die Zulassung meist keine Frage des „Geldes", sondern von **Bewerbung + Interview + teils GMAT/GRE**; Zahlungsfähigkeit allein garantiert keinen Platz. Plane die Bewerbung auf beiden Seiten früh.

## Die MBA-Option

Der MBA ist nicht für direkt nach dem Bachelor, sondern für **Profis mit mehreren Jahren Berufserfahrung**. Starke MBA-Adressen in Deutschland konzentrieren sich auf der privaten Seite: **WHU, Mannheim Business School, ESMT Berlin, Frankfurt School**. Der Gegenwert deiner Gebühr ist hier vor allem **Netzwerk, Marke und Karrieresprung** — sinnvoll, wenn dein Karriereziel das rechtfertigt.

## Fazit & ehrlicher Rat

- **Wenn dein Budget begrenzt ist und du Deutsch lernen kannst:** Die staatliche Universität (Mannheim/Köln/Goethe/LMU/Münster) ist fast kostenlos und hoch angesehen — für die meisten der klügste Weg.
- **Wenn du auf Englisch studieren, ein starkes Netzwerk und schnelle Karriere willst (und Budget hast):** Private Schools wie WHU/Frankfurt School/ESMT können echten Wert bieten.
- **Dein Ziel nach dem Abschluss ist entscheidend:** Für Beratung/Finance ist Deutsch ein großer Vorteil — das behandeln wir ehrlich in [Arbeiten in Business, Beratung und Finance in Deutschland](/de/blog/working-in-business-consulting-finance-in-germany-blue-card-salary-de) und [Was tun mit einem BWL-Abschluss?](/de/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market-de). Zur Route Master vs. Job-Seeker-Visum: [Deutschland: Master oder Job-Seeker-Visum?](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).

Nicht das Etikett entscheidet, sondern dein Ziel: Nicht die „Prestige-School", sondern die **zu dir passende School** bringt dich weiter.

---
*Stand 2025/2026. Gebühren, NC-Grenzen und Zulassungsvoraussetzungen variieren je nach Universität und Semester — bitte vor der Bewerbung aus offiziellen Quellen prüfen.*
MD;

        $enBody = <<<'MD'
You've decided to study business in Germany — but ahead of you lies a **critical fork in the road** that many people miss: **a free public university or a private business school costing tens of thousands of euros?** Names like Mannheim, WHU and Frankfurt School will come up again and again. This article compares the two honestly and gives you a concrete compass for the question "Which one is right for me?"

## The decision: free public vs. expensive private

The fundamental truth of business education in Germany is this: **you don't necessarily have to pay for quality education.** Public universities are almost free (only the semester contribution, *as of 2025/2026 roughly ~€150–350/semester*; in Baden-Württemberg ~€1,500/semester for non-EU students — verify). Private business schools, by contrast, charge *~€20,000–40,000* in program fees.

So the question isn't "which is better?" but rather: **"What do you get for the extra money you pay?"** On the public side: free, strong academic reputation, broad recognition. On the private side: English-taught programs, small classes, a dense network and faster career momentum. Both can be the right choice for the right student.

## Top public business universities (competitive NC)

On the public side, Germany's business landscape is clear. These are free but **competitive** — especially at bachelor level, the NC (numerus clausus / grade cutoff) is high.

| University | Strength | Language (bachelor) | Cost (approx., 2025/26) |
|---|---|---|---|
| **University of Mannheim** | Germany's #1 business (BWL) address | Mostly German | Semester fee (~€150) |
| **University of Cologne (Köln)** | Large, strong business faculty | German | Semester fee |
| **Goethe Uni Frankfurt** | In the heart of the finance hub | German | Semester fee |
| **LMU Munich** | Long-established, prestigious | German | Semester fee |
| **University of Münster** | Respected business tradition | German | Semester fee |

**Bold fact:** Public bachelor's programs usually require **German (C1)**; English-taught bachelor's are rare at public universities. In return, **English-taught master's** (Management, Finance, Marketing, Business Analytics) exist at public universities too, and they are **nearly free**. If you're thinking of coming without German, read the details in our guide to [English-taught business/management master's programs](/en/blog/english-taught-business-management-masters-in-germany-without-german-en).

## Private business schools: fees, English, industry ties

Private schools are expensive, but they offer a different package: English-taught teaching, small classes, strong corporate/alumni networks and usually a ready-made career infrastructure.

| School | Strength | Language | Cost (approx., 2025/26) |
|---|---|---|---|
| **WHU – Otto Beisheim** | Germany's most prestigious business school; strong network | English | High (~€30–40k+ program) |
| **Frankfurt School of Finance & Management** | Strong in finance/banking, based in Frankfurt | English | ~€20–40k |
| **ESMT Berlin** | International, MBA/master, industry-connected | English | ~€20–40k |
| **HHL Leipzig** | Focus on entrepreneurship + management | English | ~€20–30k |

*Fees are approximate as of 2025/2026 and vary by program — verify from an official source.* The greatest strength of these schools isn't the degree itself but the **door-opening alumni network and corporate ties** (consulting, finance, DAX companies).

## When is private actually worth it?

A private school isn't always "a waste of money"; in certain cases it makes sense:

- **If you want to study in English** (especially at bachelor level) and can't learn German quickly.
- **If network and career speed** matter to you as much as the money (aiming for consulting/finance).
- **If you're doing an MBA** — as an experienced professional, network and brand value are decisive.
- **If small classes + intensive mentoring** suit your learning style.

Conversely: **if you can learn German and want academic depth/research**, the free public university is usually the smarter choice in most cases. For how much language weighs in working life, see the parallel case of [working in IT/tech in Germany](/en/blog/working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary-en).

## NC and the application reality

On the public side, the biggest hurdle is the **NC**: at top addresses like Mannheim/Cologne, the business bachelor is highly competitive and the grade cutoff is high. But don't panic — many **universities of applied sciences (FH/HAW)** offer business programs with no NC or more flexible admission, and they are practically very valuable on the job market.

At private schools, admission is usually not a matter of "money" but of **application + interview + sometimes GMAT/GRE**; ability to pay alone doesn't guarantee a spot. Plan your application early on both sides.

## The MBA option

The MBA isn't for right after your bachelor's, but for **professionals with several years of work experience**. Germany's strong MBA addresses are concentrated on the private side: **WHU, Mannheim Business School, ESMT Berlin, Frankfurt School**. Here the return on your fee is mainly **network, brand and a career jump** — worth it if your career goal justifies it.

## Conclusion & honest advice

- **If your budget is limited and you can learn German:** the public university (Mannheim/Cologne/Goethe/LMU/Münster) is almost free and highly respected — the smartest route for most students.
- **If you want to study in English, a strong network and a fast career (and you have the budget):** private schools like WHU/Frankfurt School/ESMT can offer real value.
- **Your goal after graduation is critical:** for consulting/finance, German is a major advantage — we cover this honestly in [working in business, consulting and finance in Germany](/en/blog/working-in-business-consulting-finance-in-germany-blue-card-salary-en) and [what to do with a business/BWL degree](/en/blog/what-to-do-with-a-business-bwl-degree-in-germany-job-market-en). For the master's vs. job-seeker visa route: [Germany: master's or job-seeker visa?](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

Let your goal decide, not the label: it's not the "prestige school" but the **school that fits you** that gets you ahead.

---
*Prepared as of 2025/2026. Fees, NC cutoffs and admission requirements vary by university and semester — verify from official sources before applying.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'public-vs-private-business-schools-in-germany-mannheim-whu-frankfurt',
                'title' => 'Almanya İşletme Okulları: Kamu mu Özel mi? Mannheim, WHU, Frankfurt School (2026)',
                'excerpt' => 'Almanya\'da işletme okumak: ücretsiz kamu üniversitesi (Mannheim/Köln/Goethe/LMU/Münster, rekabetçi NC) mi, pahalı özel işletme okulu (WHU/Frankfurt School/ESMT/HHL, İngilizce, güçlü network) mi? Ücret, NC, İngilizce, MBA ve "ne zaman özel değer" dürüst karşılaştırma + sonuç.',
                'meta_title' => 'Almanya İşletme Okulları: Kamu mu Özel mi? (2026)',
                'meta_description' => 'Almanya işletme eğitimi: ücretsiz kamu üni (Mannheim/Köln/Goethe) vs pahalı özel okul (WHU/Frankfurt School/ESMT). Ücret, NC, İngilizce, MBA — dürüst karşılaştırma.',
                'body' => $trBody,
            ],
            'de' => [
                'slug' => 'public-vs-private-business-schools-in-germany-mannheim-whu-frankfurt-de',
                'title' => 'BWL in Deutschland: Staatlich oder privat? Mannheim, WHU, Frankfurt School (2026)',
                'excerpt' => 'BWL in Deutschland studieren: kostenlose staatliche Uni (Mannheim/Köln/Goethe/LMU/Münster, kompetitiver NC) oder teure private Business School (WHU/Frankfurt School/ESMT/HHL, Englisch, starkes Netzwerk)? Kosten, NC, Englisch, MBA — ehrlicher Vergleich + Fazit.',
                'meta_title' => 'BWL Deutschland: Staatlich oder privat? (2026)',
                'meta_description' => 'BWL-Studium in Deutschland: kostenlose staatliche Uni (Mannheim/Köln/Goethe) vs. teure private School (WHU/Frankfurt School/ESMT). Kosten, NC, Englisch, MBA — ehrlicher Vergleich.',
                'body' => $deBody,
            ],
            'en' => [
                'slug' => 'public-vs-private-business-schools-in-germany-mannheim-whu-frankfurt-en',
                'title' => 'Business Schools in Germany: Public or Private? Mannheim, WHU, Frankfurt School (2026)',
                'excerpt' => 'Studying business in Germany: a free public university (Mannheim/Cologne/Goethe/LMU/Münster, competitive NC) or an expensive private business school (WHU/Frankfurt School/ESMT/HHL, English, strong network)? Fees, NC, English, MBA — an honest comparison + verdict.',
                'meta_title' => 'Business Schools in Germany: Public or Private? (2026)',
                'meta_description' => 'Studying business in Germany: free public uni (Mannheim/Cologne/Goethe) vs. expensive private school (WHU/Frankfurt School/ESMT). Fees, NC, English, MBA — an honest comparison.',
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
            'public-vs-private-business-schools-in-germany-mannheim-whu-frankfurt',
            'public-vs-private-business-schools-in-germany-mannheim-whu-frankfurt-de',
            'public-vs-private-business-schools-in-germany-mannheim-whu-frankfurt-en',
        ])->delete();
    }
};

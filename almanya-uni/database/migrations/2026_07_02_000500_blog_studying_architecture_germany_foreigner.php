<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Studying Architecture in Germany as a foreigner (2026). Doğrulandı:
 * Architektur = tasarım (Bauing DEĞİL); bachelor genelde Almanca C1, İngilizce master sınırlı;
 * portfolyo (Mappe)+NC+Eignungsprüfung yaygın; "Architekt" için Architektenkammer (5 yıl/300 ECTS + ~2 yıl pratik).
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'd1a10000-1111-4a2c-9f50-dd01ee04aa01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya, çağdaş mimarlığın kalbi: Bauhaus'un doğduğu ülke, güçlü bir tasarım kültürü ve ücretsiz (veya çok ucuz) devlet üniversiteleri. Ama uluslararası öğrenci olarak Almanya'da mimarlık okumak, sanıldığından farklı çalışır. Bu rehber, en çok karıştırılan noktaları — mimarlık vs inşaat mühendisliği, portfolyo (Mappe) gerçeği, dil ve lisans — dürüstçe açıklıyor.

## Mimarlık (Architektur) inşaat mühendisliği (Bauingenieurwesen) DEĞİLDİR

Bu, en pahalıya patlayan karışıklık. **Architektur = tasarım, mekan, biçim, kentsel yaşam.** Nasıl görüneceğine, nasıl yaşanacağına karar verirsin. **Bauingenieurwesen (inşaat mühendisliği) = strüktür, statik, malzeme, taşıyıcı sistem.** Binanın ayakta durmasını sağlayan hesap odur.

İkisi aynı şantiyede buluşur ama farklı programlar, farklı fakülteler, farklı kariyerlerdir. Başvurmadan önce hangisini istediğine karar ver:

- **Tasarım, eskiz, konsept, stüdyo kültürü** seni çekiyorsa → **Architektur**.
- **Matematik, mekanik, statik hesap, altyapı** seni çekiyorsa → **Bauingenieurwesen**.

Mühendislik tarafını merak ediyorsan [Almanya'da mühendislik okuma rehberimize](/tr/blog/studying-engineering-in-germany-as-a-foreigner) da bakabilirsin — ama bu yazı tamamen tasarım odaklı mimarlıkla ilgili.

## Portfolyo (Mappe) + NC + Eignungsprüfung gerçeği

Almanya'da mimarlık, sırf notunla giremeyeceğin nadir bölümlerden biridir. Çoğu program şunlardan **en az birini** ister:

- **Portfolyo (Mappe):** Kendi çizim, eskiz, tasarım ve el işi çalışmalarından oluşan bir dosya. Bu, başvurunun kalbidir. Fotoğraf, kolaj, maket, serbest el çizimi — yaratıcı düşünceni gösteren her şey.
- **Eignungsprüfung / Eignungsfeststellung:** Yetenek/uygunluk sınavı. Bazı okullarda çizim testi veya mülakat.
- **NC (Numerus Clausus):** Kontenjan kısıtı; popüler okullarda not barajı yüksek olabilir.

**Kalın gerçek: portfolyo hazırlığı aylar sürer.** Başvuru döneminden en az 6-12 ay önce çalışmaya başla. İyi bir Mappe, zayıf bir notu bile telafi edebilir; kötü bir Mappe, mükemmel notu bile götürür.

## Bachelor (Almanca) genelde şart — İngilizce master sınırlı ama var

Burada net ol:

- **Bachelor (B.Sc./B.A. Architektur) neredeyse her zaman Almanca**dır ve tipik olarak **C1 seviyesi** ister (TestDaF/DSH). İngilizce bachelor Almanya'da çok nadirdir.
- **İngilizce master** vardır ama sınırlıdır: M.Sc. Architecture, **Urban Design / Städtebau**, Integrated Design gibi programlar. Örnek olarak TU Berlin, TU München, Bauhaus-Universität Weimar ve kentsel odaklı HafenCity Universität Hamburg akla gelir.

Yani "Almancam yok ama Almanya'da mimarlık okurum" beklentisi genelde sadece **master** için ve **sınırlı sayıda** programda gerçekçidir. Almancasız yol için ayrı rehberimiz var: [Almancasız İngilizce mimarlık masterları](/tr/blog/english-taught-architecture-masters-in-germany-without-german).

## Tepe okullar (2026, yaklaşık)

Almanya'da mimarlık için öne çıkan kamu üniversiteleri:

| Üniversite | Şehir | Öne çıkan |
|---|---|---|
| TU München (TUM) | Münih | Güçlü tasarım + araştırma, uluslararası ağ |
| TU Berlin | Berlin | Kentsel odak, İngilizce master seçenekleri |
| RWTH Aachen | Aachen | Teknik + tasarım dengesi |
| TU Darmstadt | Darmstadt | Köklü mimarlık fakültesi |
| Universität Stuttgart | Stuttgart | Hesaplamalı tasarım, ILEK/strüktür geleneği |
| KIT | Karlsruhe | Araştırma yoğun |
| Bauhaus-Universität Weimar | Weimar | Bauhaus mirası, tasarım/kültür odağı |
| HafenCity Universität | Hamburg | Kentsel tasarım / Urban Design |

*2025/2026 itibarıyla, yaklaşık; program dilleri ve şartları her yıl değişebilir — mutlaka okulun sayfasından doğrula.*

## Başvuru: uni-assist mi doğrudan mı?

- Birçok üniversite uluslararası başvuruları **uni-assist** üzerinden toplar (belge/denklik ön kontrolü). Bazıları doğrudan kendi portalını kullanır.
- Tipik belgeler: lise/lisans diploması + transkript, dil sertifikası (Almanca C1 veya İngilizce master için IELTS/TOEFL), **portfolyo**, motivasyon mektubu, bazen ön eleme sınavı.
- **Studienkolleg** bazı adaylar için gerekebilir — ama bu bir dil kursu değildir, lise denkliği/hazırlık kurumudur. Detay için: [Studienkolleg bir dil okulu değildir](/tr/blog/studienkolleg-is-not-a-language-school-what-it-really-is).

Başvuru takvimi kritik: portfolyo + dil + uni-assist ön kontrolü birlikte aylar alır. Erken başla.

## Ücret & burs — ve lisans için 5-yıl kuralına giriş

- **Kamu üniversiteleri neredeyse ücretsiz:** dönem başına yaklaşık **~150-350€** Semesterbeitrag (idari katkı, ulaşım dahil olabilir). **İstisna: Baden-Württemberg**, AB-dışı öğrencilerden dönem başına yaklaşık **~1.500€** alır *(2025/2026 itibarıyla, yaklaşık; doğrula)*.
- **Burslar:** DAAD ve çeşitli vakıflar mimarlık öğrencilerine de açıktır; erken başvuru şart.

**Ve şimdi en önemli dürüst gerçek — lisans:** Almanya'da **"Architekt" korumalı bir unvandır.** Bu unvanı kullanmak ve inşaat ruhsatı başvurusu sunma yetkisi için bir eyaletin **Architektenkammer**'ine (mimarlar odası) kayıt gerekir. Tipik gereklilik: **akredite bir derece (genelde 5 yıl = Bachelor + Master, min ~300 ECTS / AB direktifi ≥4 yıl) + ~2 yıl mesleki pratik** *(2025/2026 itibarıyla, yaklaşık; eyalete göre değişir)*.

**Yani tek başına bachelor seni lisanslı mimar yapmaz.** Tıptaki Approbation gibi düşün: diploma başlangıçtır, unvan ayrı bir süreçtir. Bu yolun tamamı için: [Almanya'da lisanslı mimar olmak (Architektenkammer)](/tr/blog/becoming-a-licensed-architect-in-germany-architektenkammer).

## Sonuç & dürüst tavsiye

Almanya'da mimarlık okumak muhteşem bir fırsat: dünya çapında tasarım kültürü, ücretsiz kamu eğitimi, güçlü kariyer altyapısı. Ama üç gerçeği baştan kabul et:

1. **Bachelor için Almanca (C1) neredeyse şart.** İngilizce sadece sınırlı masterlarda gerçekçi.
2. **Portfolyo (Mappe) her şeydir.** Aylar önceden hazırla.
3. **"Architekt" olmak için 5 yıllık derece + Kammer kaydı gerekir** — bachelor tek başına yetmez. Ve dürüst ol: mimarlıkta giriş maaşı mühendisliğe göre genelde daha düşüktür. Bu bir tutku mesleği.

Vizyonun net, portfolyon güçlü ve Almancan hazırsa — Almanya sana dünyanın en iyi mimarlık eğitimlerinden birini sunar. Kariyer ve maaş tarafını da planla: [Almanya'da mimar olarak çalışmak](/tr/blog/working-as-an-architect-in-germany-salary-job-market) ve genel strateji için [Almanya'da master mı iş arama vizesi mi](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career).

*Not: Bu içerik 2026 başı itibarıyla genel bilgilendirme amaçlıdır; program dilleri, ücretler, NC ve lisans kuralları eyalete ve yıla göre değişir. Başvuru yapmadan önce ilgili üniversite ve Architektenkammer'in güncel bilgilerini doğrula.*
MD;
        $deBody = <<<'MD'
Deutschland ist ein Herzstück der modernen Architektur: das Geburtsland des Bauhauses, mit starker Entwurfskultur und kostenlosen (oder sehr günstigen) staatlichen Universitäten. Doch als internationale:r Student:in in Deutschland Architektur zu studieren funktioniert anders, als viele denken. Dieser Leitfaden erklärt ehrlich die am häufigsten verwechselten Punkte — Architektur vs. Bauingenieurwesen, die Mappe-Realität, Sprache und Berufszulassung.

## Architektur ist NICHT Bauingenieurwesen

Das ist die teuerste Verwechslung. **Architektur = Entwurf, Raum, Form, städtisches Leben.** Du entscheidest, wie ein Gebäude aussieht und wie darin gelebt wird. **Bauingenieurwesen = Struktur, Statik, Material, Tragwerk.** Das ist die Berechnung, die das Gebäude stehen lässt.

Beide treffen sich auf derselben Baustelle, sind aber unterschiedliche Studiengänge, Fakultäten und Karrieren. Entscheide vor der Bewerbung, was du willst:

- Wenn dich **Entwurf, Skizze, Konzept, Studiokultur** reizen → **Architektur**.
- Wenn dich **Mathematik, Mechanik, Statik, Infrastruktur** reizen → **Bauingenieurwesen**.

Interessiert dich die Ingenieurseite, wirf einen Blick in unseren [Leitfaden zum Ingenieurstudium in Deutschland](/de/blog/studying-engineering-in-germany-as-a-foreigner-de) — dieser Artikel dreht sich jedoch ganz um die entwurfsorientierte Architektur.

## Die Realität von Mappe + NC + Eignungsprüfung

Architektur ist einer der wenigen Studiengänge, in die du in Deutschland nicht allein über deine Note kommst. Die meisten Programme verlangen **mindestens eines** davon:

- **Mappe (Portfolio):** eine Sammlung deiner Zeichnungen, Skizzen, Entwürfe und gestalterischen Arbeiten. Das ist das Herz deiner Bewerbung. Fotografie, Collage, Modell, Freihandzeichnung — alles, was dein gestalterisches Denken zeigt.
- **Eignungsprüfung / Eignungsfeststellung:** ein Eignungstest. An manchen Hochschulen ein Zeichentest oder Gespräch.
- **NC (Numerus Clausus):** eine Zulassungsbeschränkung; an beliebten Hochschulen kann die Notengrenze hoch sein.

**Fettgedruckte Wahrheit: Die Vorbereitung der Mappe dauert Monate.** Beginne mindestens 6-12 Monate vor der Bewerbungsfrist. Eine starke Mappe kann sogar eine schwächere Note ausgleichen; eine schwache Mappe ruiniert auch eine perfekte Note.

## Bachelor meist auf Deutsch — englischsprachige Master begrenzt, aber vorhanden

Sei hier klar:

- Der **Bachelor (B.Sc./B.A. Architektur) ist fast immer auf Deutsch** und verlangt typischerweise **Niveau C1** (TestDaF/DSH). Englischsprachige Bachelor sind in Deutschland sehr selten.
- **Englischsprachige Master** gibt es, aber begrenzt: M.Sc. Architecture, **Urban Design / Städtebau**, Integrated Design. Beispiele sind TU Berlin, TU München, Bauhaus-Universität Weimar und die städtebaulich ausgerichtete HafenCity Universität Hamburg.

Die Erwartung "Ich kann kein Deutsch, studiere aber trotzdem Architektur in Deutschland" ist also meist nur für den **Master** und in **begrenzt vielen** Programmen realistisch. Für den Weg ohne Deutsch haben wir einen eigenen Leitfaden: [Englischsprachige Architektur-Master ohne Deutsch](/de/blog/english-taught-architecture-masters-in-germany-without-german-de).

## Top-Hochschulen (2026, ungefähr)

Herausragende staatliche Universitäten für Architektur in Deutschland:

| Universität | Stadt | Stärke |
|---|---|---|
| TU München (TUM) | München | Starker Entwurf + Forschung, internationales Netzwerk |
| TU Berlin | Berlin | Städtebaulicher Fokus, englischsprachige Master |
| RWTH Aachen | Aachen | Balance aus Technik + Entwurf |
| TU Darmstadt | Darmstadt | Traditionsreiche Architekturfakultät |
| Universität Stuttgart | Stuttgart | Computergestützter Entwurf, Tragwerkstradition |
| KIT | Karlsruhe | Forschungsintensiv |
| Bauhaus-Universität Weimar | Weimar | Bauhaus-Erbe, Gestaltungs-/Kulturfokus |
| HafenCity Universität | Hamburg | Urban Design / Städtebau |

*Stand ca. 2025/2026, ungefähr; Programmsprachen und Voraussetzungen können sich jährlich ändern — prüfe unbedingt die Seite der Hochschule.*

## Bewerbung: uni-assist oder direkt?

- Viele Universitäten bündeln internationale Bewerbungen über **uni-assist** (Vorprüfung der Unterlagen/Anerkennung). Andere nutzen ihr eigenes Portal direkt.
- Typische Unterlagen: Abitur-/Bachelorzeugnis + Transcript, Sprachnachweis (Deutsch C1 oder für Master IELTS/TOEFL), **Mappe**, Motivationsschreiben, teils eine Eignungsprüfung.
- Ein **Studienkolleg** kann für manche Bewerber:innen nötig sein — aber das ist kein Sprachkurs, sondern eine Einrichtung zur Studienvorbereitung/Anerkennung. Mehr dazu: [Ein Studienkolleg ist keine Sprachschule](/de/blog/studienkolleg-is-not-a-language-school-what-it-really-is-de).

Der Bewerbungskalender ist entscheidend: Mappe + Sprache + uni-assist-Vorprüfung dauern zusammen Monate. Fang früh an.

## Kosten & Stipendien — und der Einstieg in die 5-Jahres-Regel für die Zulassung

- **Staatliche Universitäten sind fast kostenlos:** ca. **~150-350€** Semesterbeitrag pro Semester (Verwaltungsbeitrag, ggf. inkl. Ticket). **Ausnahme: Baden-Württemberg** erhebt von Nicht-EU-Studierenden ca. **~1.500€** pro Semester *(Stand ca. 2025/2026, ungefähr; bitte prüfen)*.
- **Stipendien:** DAAD und diverse Stiftungen stehen auch Architekturstudierenden offen; frühe Bewerbung ist Pflicht.

**Und jetzt die wichtigste ehrliche Wahrheit — die Zulassung:** In Deutschland ist **"Architekt:in" eine geschützte Berufsbezeichnung.** Um diesen Titel zu führen und bauvorlageberechtigt zu sein, ist die Eintragung in die **Architektenkammer** eines Bundeslandes nötig. Typische Voraussetzung: **ein akkreditierter Abschluss (meist 5 Jahre = Bachelor + Master, min. ~300 ECTS / EU-Richtlinie ≥4 Jahre) + ca. 2 Jahre Berufspraxis** *(Stand ca. 2025/2026, ungefähr; je nach Bundesland verschieden)*.

**Ein Bachelor allein macht dich also nicht zum zugelassenen Architekten.** Denk an die Approbation in der Medizin: Das Diplom ist der Anfang, der Titel ist ein eigener Prozess. Den ganzen Weg findest du hier: [Zugelassene:r Architekt:in in Deutschland werden (Architektenkammer)](/de/blog/becoming-a-licensed-architect-in-germany-architektenkammer-de).

## Fazit & ehrlicher Rat

Architektur in Deutschland zu studieren ist eine großartige Chance: weltweite Entwurfskultur, kostenlose staatliche Bildung, starke Karriereinfrastruktur. Aber akzeptiere drei Wahrheiten von Anfang an:

1. **Für den Bachelor ist Deutsch (C1) fast Pflicht.** Englisch ist nur in begrenzten Mastern realistisch.
2. **Die Mappe ist alles.** Bereite sie Monate im Voraus vor.
3. **Um "Architekt:in" zu werden, brauchst du einen 5-jährigen Abschluss + Kammereintragung** — ein Bachelor allein reicht nicht. Und sei ehrlich: Das Einstiegsgehalt in der Architektur ist meist niedriger als im Ingenieurwesen. Es ist ein Berufungsberuf.

Wenn deine Vision klar, deine Mappe stark und dein Deutsch bereit ist — bietet dir Deutschland eine der besten Architekturausbildungen der Welt. Plane auch Karriere und Gehalt: [Als Architekt:in in Deutschland arbeiten](/de/blog/working-as-an-architect-in-germany-salary-job-market-de) und für die Gesamtstrategie [Master oder Job-Seeker-Visum in Deutschland](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de).

*Hinweis: Dieser Inhalt dient der allgemeinen Information mit Stand Anfang 2026; Programmsprachen, Gebühren, NC und Zulassungsregeln variieren je nach Bundesland und Jahr. Prüfe vor der Bewerbung die aktuellen Angaben der jeweiligen Universität und Architektenkammer.*
MD;
        $enBody = <<<'MD'
Germany is a heartland of modern architecture: the birthplace of the Bauhaus, with a strong design culture and free (or very cheap) public universities. But studying architecture in Germany as an international student works differently than many expect. This guide honestly explains the most commonly confused points — architecture vs. civil engineering, the portfolio (Mappe) reality, language, and professional licensing.

## Architecture (Architektur) is NOT civil engineering (Bauingenieurwesen)

This is the most expensive confusion. **Architecture = design, space, form, urban life.** You decide how a building looks and how people live in it. **Civil engineering (Bauingenieurwesen) = structure, statics, materials, load-bearing systems.** That is the calculation that keeps the building standing.

The two meet on the same construction site but are different degree programs, faculties, and careers. Decide before you apply which one you want:

- If **design, sketching, concept, studio culture** attract you → **Architektur**.
- If **mathematics, mechanics, statics, infrastructure** attract you → **Bauingenieurwesen**.

If the engineering side interests you, take a look at our [guide to studying engineering in Germany](/en/blog/studying-engineering-in-germany-as-a-foreigner-en) — but this article is entirely about design-focused architecture.

## The reality of portfolio (Mappe) + NC + Eignungsprüfung

Architecture is one of the few programs in Germany you cannot enter on grades alone. Most programs require **at least one** of these:

- **Portfolio (Mappe):** a collection of your drawings, sketches, designs, and creative work. This is the heart of your application. Photography, collage, models, freehand drawing — anything that shows your design thinking.
- **Eignungsprüfung / Eignungsfeststellung:** an aptitude/suitability test. At some schools, a drawing test or interview.
- **NC (Numerus Clausus):** an admission cap; at popular schools the grade threshold can be high.

**Bold truth: preparing the Mappe takes months.** Start at least 6-12 months before the application deadline. A strong Mappe can even offset a weaker grade; a weak Mappe wastes even a perfect grade.

## Bachelor mostly in German — English-taught masters limited but real

Be clear here:

- The **bachelor (B.Sc./B.A. Architektur) is almost always in German** and typically requires **C1 level** (TestDaF/DSH). English-taught bachelors are very rare in Germany.
- **English-taught masters** exist but are limited: M.Sc. Architecture, **Urban Design / Städtebau**, Integrated Design. Examples include TU Berlin, TU München, Bauhaus-Universität Weimar, and the urban-focused HafenCity Universität Hamburg.

So the expectation "I don't speak German but I'll study architecture in Germany" is usually realistic only for the **master** and in a **limited number** of programs. For the no-German path, we have a dedicated guide: [English-taught architecture masters without German](/en/blog/english-taught-architecture-masters-in-germany-without-german-en).

## Top schools (2026, approximate)

Standout public universities for architecture in Germany:

| University | City | Strength |
|---|---|---|
| TU München (TUM) | Munich | Strong design + research, international network |
| TU Berlin | Berlin | Urban focus, English-taught master options |
| RWTH Aachen | Aachen | Balance of technology + design |
| TU Darmstadt | Darmstadt | Long-established architecture faculty |
| Universität Stuttgart | Stuttgart | Computational design, structural tradition |
| KIT | Karlsruhe | Research-intensive |
| Bauhaus-Universität Weimar | Weimar | Bauhaus heritage, design/culture focus |
| HafenCity Universität | Hamburg | Urban Design / Städtebau |

*As of ca. 2025/2026, approximate; program languages and requirements can change yearly — always verify on the school's page.*

## Applying: uni-assist or direct?

- Many universities collect international applications via **uni-assist** (pre-check of documents/equivalency). Others use their own portal directly.
- Typical documents: high-school/bachelor diploma + transcript, language certificate (German C1, or IELTS/TOEFL for a master), **portfolio**, motivation letter, sometimes an aptitude test.
- A **Studienkolleg** may be needed for some applicants — but it is not a language course; it is a study-preparation/equivalency institution. More here: [A Studienkolleg is not a language school](/en/blog/studienkolleg-is-not-a-language-school-what-it-really-is-en).

The application calendar is critical: Mappe + language + uni-assist pre-check together take months. Start early.

## Costs & scholarships — and an intro to the 5-year rule for licensing

- **Public universities are almost free:** roughly **~€150-350** Semesterbeitrag per semester (administrative fee, may include a transit ticket). **Exception: Baden-Württemberg** charges non-EU students roughly **~€1,500** per semester *(as of ca. 2025/2026, approximate; verify)*.
- **Scholarships:** DAAD and various foundations are open to architecture students too; apply early.

**And now the most important honest truth — licensing:** In Germany, **"Architekt" is a protected title.** To use it and be authorized to submit building permits, you must register with the **Architektenkammer** (chamber of architects) of a federal state. Typical requirement: **an accredited degree (usually 5 years = Bachelor + Master, min ~300 ECTS / EU directive ≥4 years) + ~2 years of professional practice** *(as of ca. 2025/2026, approximate; varies by state)*.

**So a bachelor alone does not make you a licensed architect.** Think of it like the Approbation in medicine: the diploma is the start, the title is a separate process. For the full path: [Becoming a licensed architect in Germany (Architektenkammer)](/en/blog/becoming-a-licensed-architect-in-germany-architektenkammer-en).

## Conclusion & honest advice

Studying architecture in Germany is a great opportunity: world-class design culture, free public education, strong career infrastructure. But accept three truths from the start:

1. **For the bachelor, German (C1) is almost mandatory.** English is realistic only in limited masters.
2. **The Mappe is everything.** Prepare it months in advance.
3. **To become an "Architekt" you need a 5-year degree + chamber registration** — a bachelor alone is not enough. And be honest: entry salaries in architecture are generally lower than in engineering. It is a vocation of passion.

If your vision is clear, your Mappe is strong, and your German is ready — Germany offers you one of the best architecture educations in the world. Plan the career and salary side too: [Working as an architect in Germany](/en/blog/working-as-an-architect-in-germany-salary-job-market-en) and for overall strategy [Germany masters vs job-seeker visa](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en).

*Note: This content is general information as of early 2026; program languages, fees, NC, and licensing rules vary by federal state and year. Before applying, verify the current details of the relevant university and Architektenkammer.*
MD;

        $variants = [
            'tr' => ['slug'=>'studying-architecture-in-germany-as-a-foreigner',    'title'=>'Almanya\'da Mimarlık Okumak: Uluslararası Öğrenci Rehberi (2026)', 'excerpt'=>'Almanya\'da mimarlık okumak: Architektur vs inşaat mühendisliği farkı, portfolyo (Mappe) + NC gerçeği, Almanca bachelor vs İngilizce master, tepe okullar ve lisans için 5-yıl kuralı. Uluslararası öğrenci için dürüst rehber.', 'meta_title'=>'Almanya\'da Mimarlık Okumak: Rehber (2026)', 'meta_description'=>'Architektur vs inşaat müh., portfolyo (Mappe) + NC, Almanca bachelor vs İngilizce master, tepe okullar ve Architektenkammer lisansı — dürüst rehber.', 'body'=>$trBody],
            'de' => ['slug'=>'studying-architecture-in-germany-as-a-foreigner-de', 'title'=>'Architektur in Deutschland studieren: Leitfaden für Internationale (2026)', 'excerpt'=>'Architektur in Deutschland studieren: Architektur vs. Bauingenieurwesen, die Realität von Mappe + NC, Bachelor auf Deutsch vs. englischsprachiger Master, Top-Hochschulen und die 5-Jahres-Regel zur Zulassung. Ehrlicher Leitfaden.', 'meta_title'=>'Architektur in Deutschland studieren: Leitfaden (2026)', 'meta_description'=>'Architektur vs. Bauingenieurwesen, Mappe + NC, deutscher Bachelor vs. englischer Master, Top-Hochschulen und die Architektenkammer-Zulassung — ehrlich.', 'body'=>$deBody],
            'en' => ['slug'=>'studying-architecture-in-germany-as-a-foreigner-en', 'title'=>'Studying Architecture in Germany: An International Student Guide (2026)', 'excerpt'=>'Studying architecture in Germany: architecture vs. civil engineering, the portfolio (Mappe) + NC reality, German bachelor vs. English master, top schools, and the 5-year rule for licensing. An honest guide for international students.', 'meta_title'=>'Studying Architecture in Germany: A Guide (2026)', 'meta_description'=>'Architecture vs. civil engineering, portfolio (Mappe) + NC, German bachelor vs. English master, top schools, and Architektenkammer licensing — an honest guide.', 'body'=>$enBody],
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
        Post::whereIn('slug', ['studying-architecture-in-germany-as-a-foreigner', 'studying-architecture-in-germany-as-a-foreigner-de', 'studying-architecture-in-germany-as-a-foreigner-en'])->delete();
    }
};

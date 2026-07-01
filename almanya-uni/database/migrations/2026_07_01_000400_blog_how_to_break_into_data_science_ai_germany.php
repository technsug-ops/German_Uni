<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Data Science & AI'ye nasıl girilir — derece, bootcamp, geçiş (2026).
 * Doğrulandı: DS/AI ana yolu CS/DS/AI master; bootcamp'ler (Data Science Retreat, neuefische,
 * Le Wagon) + Kaggle/GitHub portfolyo tamamlayıcı; yeni mezun 18 ay iş-arama izni; sayılar hedge'li.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'd4a40000-4444-4daa-9f30-aa01bb02dd04';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da Data Science ve yapay zekâ (AI) alanına "girmek" istiyorsun ama nereden başlayacağını bilmiyorsun. LinkedIn'de "3 aylık bootcamp, garantili iş" reklamları, YouTube'da "6 haftada Data Scientist ol" videoları... Gerçek biraz daha soğuk: Almanya'da bu alana giriş **hâlâ büyük oranda dereceyle** olur, geri kalanı da ciddi emek ister. Bu yazı sana dürüst yolları — derece, bootcamp, portfolyo ve arka plan geçişi — anlatır.

## Ana yol: CS / Data Science / AI master (işverenlerin çoğu dereceyi ister)

Almanya'da Data Scientist veya ML Engineer olmanın **en sağlam yolu bir üniversite derecesidir** — genelde **master seviyesinde**. Bilgisayar bilimi (Informatik), matematik, istatistik veya mühendislik lisansı üzerine bir **Data Science / Machine Learning / Artificial Intelligence** master'ı, Almanya iş piyasasında en çok tanınan giriş biletidir.

Neden derece bu kadar önemli? Çünkü Almanya işverenleri **formel nitelik (Qualifikation) kültürüne** yatkındır ve HR filtreleri genelde diploma bekler. Ayrıca **Blue Card** ve çalışma vizesi süreci resmi bir yükseköğrenim diplomasına dayanır — dereceniz yoksa vize tarafında da işiniz zorlaşır.

- **En temiz yol:** CS/Math/İstatistik lisansı → İngilizce DS/AI master (TUM, Uni Tübingen, Saarland, Mannheim, TU Darmstadt gibi).
- Detaylar için: [Yabancı olarak Almanya'da Data Science & AI okumak](/tr/blog/studying-data-science-ai-in-germany-as-a-foreigner) ve [Almancasız İngilizce DS/AI master programları](/tr/blog/english-taught-data-science-ai-masters-in-germany).

**Kalın gerçek:** Almanya'da işverenlerin büyük çoğunluğu, iki eşit adaydan **dereceli olanı** tercih eder. Bootcamp tek başına genelde yeterli görülmez.

## Bootcamp gerçeği: kime uygun, kime değil

Bootcamp'ler tamamen değersiz değil — ama **kimseye "garantili iş" vermez**. Almanya'da tanınan başlıcalar:

| Bootcamp | Şehir / Format | Odak | Kime uygun |
|---|---|---|---|
| **Data Science Retreat** | Berlin | İleri DS/ML, mentor-yoğun | Zaten teknik/matematik altyapısı olan |
| **neuefische** | Hamburg/Berlin/uzaktan | DS & ML, Almanya iş ağı | Kariyer değiştiren, yerel ağ isteyen |
| **Le Wagon** | Berlin/Münih + global | Data Science & Analytics giriş | Programlamaya yeni başlayan |
| **Spiced Academy / WBS** | Berlin | DS + iş yerleştirme desteği | Yönlendirme + destek isteyen |

Bootcamp **mantıklı olduğu durum:** zaten **CS/mühendislik/istatistik/fizik** gibi sayısal bir lisansın var ve sadece pratik ML/araç becerisi, portfolyo ve yerel ağ eksikse. Bootcamp'i "hızlandırıcı" olarak kullanırsın.

Bootcamp **tek başına yeterli olmadığı durum:** sayısal bir arka planın hiç yoksa. 3 ayda lineer cebir + olasılık + ML teorisini gerçekten öğrenemezsin; işverenler bunu görür.

**Kalın gerçek:** Bootcamp bir **tamamlayıcıdır, dereceye tam ikame değildir.** *2025/2026 itibarıyla ~9.000–15.000€ ücret aralığındadır; doğrula.*

## Portfolyo kritik: Kaggle, GitHub, gerçek proje

Derecen ya da bootcamp'in ne olursa olsun, seni öne çıkaran şey **kanıtlanabilir iştir**. Almanya işverenleri CV'de "Python biliyorum" yazmasına değil, **ne yaptığına** bakar.

- **GitHub:** Temiz, dokümante edilmiş 2-3 gerçek proje. README, veri, notebook, sonuç. "Titanic tutorial" değil — kendi problemin.
- **Kaggle:** Yarışma veya dataset katkısı. Sıralama şart değil ama aktiflik gösterir.
- **Uçtan uca bir proje:** Veri toplama → temizleme → model → değerlendirme → (mümkünse) deploy (MLOps kokusu verir).
- **Blog/yazı:** Bir projeyi anlatan kısa bir yazı, iletişim becerini kanıtlar.

**Kalın gerçek:** Almanya'da **portfolyosu olan bootcamp mezunu**, portfolyosu olmayan master mezununu bazı rollerde geçebilir. Ama en güçlüsü: **derece + portfolyo** birlikte.

## Hangi arka plandan geçilir?

Data Science/AI'ye geçiş yaygındır ve Almanya'da **sayısal lisansların hepsi kapı açar** — ama hepsi eşit kolaylıkta değil:

| Arka plan | Geçiş kolaylığı | Ne eksik kalır (kapatılacak) |
|---|---|---|
| **Bilgisayar bilimi (Informatik)** | Çok kolay | İstatistik/ML teorisi derinliği |
| **Matematik / İstatistik** | Çok kolay | Programlama pratiği, mühendislik |
| **Fizik** | Kolay | Yazılım/veri mühendisliği pratiği |
| **Mühendislik (EE/Mekatronik)** | Orta-kolay | Saf ML teorisi, istatistik |
| **Ekonometri / İktisat** | Orta | Programlama, ML mühendisliği |
| **Sayısal olmayan alanlar** | Zor | Ciddi matematik köprüsü şart |

Geçiş için tipik köprü: eksik matematik/istatistik/programlama kredisini kapatan bir **master'a köprü (Vorkurs/ön koşul)** ya da güçlü bir öz-çalışma + portfolyo. İlgili karşılaştırma: [Bir CS derecesiyle Almanya'da ne yapılır](/tr/blog/what-to-do-with-a-computer-science-degree-in-germany-job-market-salary).

## Yeni mezun: 18 ay iş-arama izni → çalışma izni

Bir Alman üniversitesinden mezun olan uluslararası öğrenciler **18 aylık iş-arama oturma iznine** hak kazanır. Bu süre DS/AI gibi teknik alanlarda **altın değerinde**: kampüs, staj ve networkün taze, dil biraz oturmuş.

- Mezuniyet sonrası kalıp iş aradığın **18 ay** boyunca çalışabilir (kısıtsız) ve iş aradıkça geçinebilirsin.
- Alanında bir iş bulunca → **Blue Card** veya nitelikli çalışma vizesine geçersin.
- MINT (STEM) alanları darboğaz sayıldığı için Blue Card eşiği **daha düşüktür**. *2025 itibarıyla MINT/yeni mezun için ~43.760€, genel eşik ~48.300€ civarı; yıllık güncellenir, doğrula.*

Master vs iş-arama vizesi stratejisi için: [Almanya'da master vs iş-arama vizesi](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career). Ayrıca not: **Studienkolleg bir dil kursu değildir** — lisans için Almanya'ya gelecekler için: [Studienkolleg gerçekte nedir](/tr/blog/studienkolleg-is-not-a-language-school-what-it-really-is).

## Yaygın hatalar (sadece araç öğrenmek, matematiği atlamak, Almancayı ihmal)

Alana girmeye çalışanların en sık düştüğü tuzaklar:

1. **Sadece "araç" öğrenmek.** Pandas, scikit-learn, birkaç tutorial... ama **lineer cebir, olasılık, ML teorisi** yok. Almanya'da mülakatlar teori sorar; bu tuzak seni ilk turda eler.
2. **Matematiği atlamak.** "Kütüphaneler hallediyor" demek başlangıçta işe yarar; kıdemli rollerde duvara toslarsın.
3. **Almancayı ihmal etmek.** İş **İngilizce** olsa bile: staj, günlük hayat, ekip içi iletişim ve bazı işverenler için Almanca fark yaratır. En az **B1-B2** hedefle.
4. **Portfolyosuz başvurmak.** "Bootcamp bitirdim" tek başına zayıf sinyaldir.
5. **Yanlış rolü hedeflemek.** Data Scientist mi, ML Engineer mı, Data Engineer mı? Farkı bil, ona göre hazırlan: [Almanya'da Data Scientist / ML Engineer olarak çalışmak](/tr/blog/working-as-a-data-scientist-ml-engineer-in-germany-blue-card-salary).

## Sonuç & dürüst tavsiye

Almanya'da Data Science & AI'ye girmenin **kestirme yolu yok** ama net bir yolu var: **sayısal bir lisans → İngilizce DS/AI master → güçlü portfolyo → 18 aylık iş-arama izniyle iş → Blue Card.** Zaten sayısal bir arka planın varsa bootcamp + portfolyo ile hızlanabilirsin; yoksa önce **matematik köprüsünü** kur. En büyük hata teoriyi atlayıp sadece araç öğrenmektir — Almanya iş piyasası bunu affetmez. Dereceni, portfolyonu ve (evet) Almancanı birlikte inşa et.

*Bu yazı 2026 başı itibarıyla genel bir rehberdir; maaşlar, Blue Card eşikleri, bootcamp ücretleri, vize süreleri ve program şartları yıldan yıla değişir. Başvurudan önce üniversitenin, işverenin ve resmi göç makamlarının güncel bilgilerini mutlaka doğrula.*
MD;

        $deBody = <<<'MD'
Du willst in Deutschland in Data Science und KI (Künstliche Intelligenz) einsteigen, weißt aber nicht, wo du anfangen sollst. Auf LinkedIn siehst du Werbung wie „3-Monats-Bootcamp, Jobgarantie", auf YouTube „In 6 Wochen Data Scientist". Die Realität ist etwas nüchterner: Der Einstieg läuft in Deutschland **immer noch überwiegend über einen Abschluss**, der Rest kostet echte Arbeit. Dieser Artikel zeigt dir die ehrlichen Wege — Abschluss, Bootcamp, Portfolio und Quereinstieg.

## Der Hauptweg: Master in Informatik / Data Science / KI

Der solideste Weg zum Data Scientist oder ML Engineer in Deutschland ist ein **Universitätsabschluss** — meist auf **Masterniveau**. Ein **Data-Science-, Machine-Learning- oder KI-Master** auf einem Bachelor in Informatik, Mathematik, Statistik oder Ingenieurwesen ist das am besten anerkannte Eintrittsticket in den deutschen Arbeitsmarkt.

Warum ist der Abschluss so wichtig? Deutschland ist stark auf **formale Qualifikation** ausgerichtet, und HR-Filter erwarten meist ein Diplom. Zudem basieren **Blaue Karte** und Arbeitsvisum auf einem offiziellen Hochschulabschluss — ohne Abschluss wird es auch beim Visum schwieriger.

- **Sauberster Weg:** Bachelor in Informatik/Mathe/Statistik → englischsprachiger DS/KI-Master (TUM, Uni Tübingen, Saarland, Mannheim, TU Darmstadt).
- Mehr dazu: [Als Ausländer in Deutschland Data Science & KI studieren](/de/blog/studying-data-science-ai-in-germany-as-a-foreigner-de) und [Englischsprachige DS/KI-Master ohne Deutsch](/de/blog/english-taught-data-science-ai-masters-in-germany-de).

**Fette Wahrheit:** Die meisten Arbeitgeber wählen bei zwei gleichwertigen Bewerbern den **mit Abschluss**. Ein Bootcamp allein reicht meist nicht.

## Die Bootcamp-Wahrheit: für wen es passt

Bootcamps sind nicht wertlos — aber sie geben **niemandem eine „Jobgarantie"**. In Deutschland bekannte Anbieter:

| Bootcamp | Stadt / Format | Fokus | Für wen |
|---|---|---|---|
| **Data Science Retreat** | Berlin | Fortgeschrittenes DS/ML, mentorintensiv | Wer schon technische/mathematische Basis hat |
| **neuefische** | Hamburg/Berlin/remote | DS & ML, deutsches Job-Netzwerk | Quereinsteiger mit Wunsch nach lokalem Netzwerk |
| **Le Wagon** | Berlin/München + global | Einstieg Data Science & Analytics | Programmier-Anfänger |
| **Spiced Academy / WBS** | Berlin | DS + Vermittlungsunterstützung | Wer Struktur und Begleitung braucht |

Ein Bootcamp ist **sinnvoll**, wenn du bereits einen quantitativen Bachelor (**Informatik/Ingenieurwesen/Statistik/Physik**) hast und dir nur praktische ML-/Tool-Skills, ein Portfolio und ein lokales Netzwerk fehlen. Dann nutzt du es als „Beschleuniger".

Ein Bootcamp reicht **allein nicht**, wenn du gar keinen quantitativen Hintergrund hast. Lineare Algebra + Wahrscheinlichkeit + ML-Theorie lernst du nicht wirklich in 3 Monaten — und Arbeitgeber merken das.

**Fette Wahrheit:** Ein Bootcamp ist eine **Ergänzung, kein voller Ersatz für einen Abschluss.** *Stand 2025/2026 kostet es meist ~9.000–15.000€; bitte prüfen.*

## Portfolio ist entscheidend: Kaggle, GitHub, echtes Projekt

Egal welcher Abschluss oder welches Bootcamp — was dich hervorhebt, ist **nachweisbare Arbeit**. Deutsche Arbeitgeber schauen nicht auf „Ich kann Python", sondern darauf, **was du gebaut hast**.

- **GitHub:** 2-3 saubere, dokumentierte echte Projekte. README, Daten, Notebook, Ergebnis. Kein „Titanic-Tutorial" — dein eigenes Problem.
- **Kaggle:** Wettbewerb oder Dataset-Beitrag. Kein Platz-1 nötig, aber Aktivität zählt.
- **End-to-End-Projekt:** Datenerhebung → Bereinigung → Modell → Evaluation → (wenn möglich) Deployment (riecht nach MLOps).
- **Blog/Text:** Ein kurzer Text zu einem Projekt beweist Kommunikationsfähigkeit.

**Fette Wahrheit:** In Deutschland kann ein **Bootcamp-Absolvent mit Portfolio** einen **Master-Absolventen ohne Portfolio** in manchen Rollen schlagen. Am stärksten ist aber: **Abschluss + Portfolio** zusammen.

## Aus welchem Hintergrund gelingt der Quereinstieg?

Der Quereinstieg in DS/KI ist verbreitet — in Deutschland öffnen **alle quantitativen Bachelor** die Tür, aber nicht gleich leicht:

| Hintergrund | Einstieg | Was fehlt (nachzuholen) |
|---|---|---|
| **Informatik** | Sehr leicht | Tiefe in Statistik/ML-Theorie |
| **Mathematik / Statistik** | Sehr leicht | Programmierpraxis, Engineering |
| **Physik** | Leicht | Software-/Data-Engineering-Praxis |
| **Ingenieurwesen (E-Technik/Mechatronik)** | Mittel-leicht | Reine ML-Theorie, Statistik |
| **Ökonometrie / VWL** | Mittel | Programmierung, ML-Engineering |
| **Nicht-quantitative Fächer** | Schwer | Ernsthafte Mathe-Brücke nötig |

Typische Brücke: ein **Vorkurs/Master mit Auflagen**, der fehlende Mathe-/Statistik-/Programmier-Credits schließt, oder starkes Selbststudium + Portfolio. Passender Vergleich: [Was man mit einem Informatik-Abschluss in Deutschland macht](/de/blog/what-to-do-with-a-computer-science-degree-in-germany-job-market-salary-de).

## Absolvent: 18 Monate Jobsuche-Erlaubnis → Arbeitserlaubnis

Wer an einer deutschen Uni abschließt, erhält eine **18-monatige Aufenthaltserlaubnis zur Jobsuche**. In technischen Feldern wie DS/KI ist diese Zeit **Gold wert**: Campus, Praktika und Netzwerk sind frisch, die Sprache sitzt etwas.

- Nach dem Abschluss darfst du bleiben und **18 Monate** lang uneingeschränkt arbeiten, während du suchst.
- Findest du einen Job im Fach → Wechsel zur **Blauen Karte** oder qualifizierten Arbeitserlaubnis.
- MINT-Fächer gelten als Engpass, daher ist die Blaue-Karte-Schwelle **niedriger**. *Stand 2025 für MINT/Absolventen ~43.760€, allgemeine Schwelle ~48.300€; wird jährlich aktualisiert, bitte prüfen.*

Zur Strategie Master vs. Jobsuche-Visum: [Master vs. Jobsuche-Visum in Deutschland](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de). Und: **Das Studienkolleg ist kein Sprachkurs** — für alle, die für den Bachelor kommen: [Was das Studienkolleg wirklich ist](/de/blog/studienkolleg-is-not-a-language-school-what-it-really-is-de).

## Häufige Fehler (nur Tools lernen, Mathe überspringen, Deutsch vernachlässigen)

Die häufigsten Fallen beim Einstieg:

1. **Nur „Tools" lernen.** Pandas, scikit-learn, ein paar Tutorials... aber keine **lineare Algebra, Wahrscheinlichkeit, ML-Theorie**. Interviews in Deutschland fragen Theorie ab; das siebt dich in der ersten Runde aus.
2. **Mathe überspringen.** „Die Bibliotheken machen das" funktioniert am Anfang; in Senior-Rollen läufst du gegen die Wand.
3. **Deutsch vernachlässigen.** Selbst wenn der Job **auf Englisch** ist: Praktikum, Alltag, Teamkommunikation und manche Arbeitgeber machen mit Deutsch einen Unterschied. Ziel: mindestens **B1-B2**.
4. **Ohne Portfolio bewerben.** „Bootcamp abgeschlossen" allein ist ein schwaches Signal.
5. **Die falsche Rolle anvisieren.** Data Scientist, ML Engineer oder Data Engineer? Kenne den Unterschied: [Als Data Scientist / ML Engineer in Deutschland arbeiten](/de/blog/working-as-a-data-scientist-ml-engineer-in-germany-blue-card-salary-de).

## Fazit & ehrlicher Rat

Es gibt keine Abkürzung in Data Science & KI in Deutschland, aber einen klaren Weg: **quantitativer Bachelor → englischsprachiger DS/KI-Master → starkes Portfolio → Job über die 18-monatige Jobsuche-Erlaubnis → Blaue Karte.** Hast du schon einen quantitativen Hintergrund, beschleunigst du mit Bootcamp + Portfolio; sonst baue zuerst die **Mathe-Brücke**. Der größte Fehler ist, die Theorie zu überspringen und nur Tools zu lernen — das verzeiht der deutsche Arbeitsmarkt nicht. Baue Abschluss, Portfolio und (ja) Deutsch gemeinsam auf.

*Dieser Artikel ist ein allgemeiner Leitfaden mit Stand Anfang 2026; Gehälter, Blaue-Karte-Schwellen, Bootcamp-Preise, Visa-Fristen und Programmvoraussetzungen ändern sich jährlich. Prüfe vor der Bewerbung immer die aktuellen Angaben der Universität, des Arbeitgebers und der offiziellen Ausländerbehörden.*
MD;

        $enBody = <<<'MD'
You want to break into Data Science and AI (Artificial Intelligence) in Germany, but you don't know where to start. LinkedIn shows you "3-month bootcamp, job guaranteed" ads, YouTube promises "Become a Data Scientist in 6 weeks." The reality is colder: in Germany, the entry path is **still mostly through a degree**, and the rest takes real work. This article walks you through the honest routes — degree, bootcamp, portfolio, and career switching.

## The main road: a CS / Data Science / AI master's

The most solid way to become a Data Scientist or ML Engineer in Germany is a **university degree** — usually at **master's level**. A **Data Science / Machine Learning / Artificial Intelligence** master's on top of a bachelor in computer science (Informatik), mathematics, statistics, or engineering is the most recognised entry ticket into the German job market.

Why does the degree matter so much? Germany leans heavily on a **formal qualification (Qualifikation) culture**, and HR filters usually expect a diploma. On top of that, the **Blue Card** and work visa are built on an official higher-education degree — without one, the visa side gets harder too.

- **Cleanest route:** CS/Math/Stats bachelor → English-taught DS/AI master's (TUM, Uni Tübingen, Saarland, Mannheim, TU Darmstadt).
- More on this: [Studying Data Science & AI in Germany as a foreigner](/en/blog/studying-data-science-ai-in-germany-as-a-foreigner-en) and [English-taught DS/AI master's without German](/en/blog/english-taught-data-science-ai-masters-in-germany-en).

**Bold truth:** Most German employers, given two equal candidates, pick the **one with the degree**. A bootcamp alone is usually not enough.

## The bootcamp reality: who it fits

Bootcamps aren't worthless — but they give **nobody a "guaranteed job."** Well-known providers in Germany:

| Bootcamp | City / Format | Focus | Best for |
|---|---|---|---|
| **Data Science Retreat** | Berlin | Advanced DS/ML, mentor-heavy | People who already have a technical/math base |
| **neuefische** | Hamburg/Berlin/remote | DS & ML, German job network | Career switchers wanting a local network |
| **Le Wagon** | Berlin/Munich + global | Intro Data Science & Analytics | Programming beginners |
| **Spiced Academy / WBS** | Berlin | DS + placement support | Those who need structure and guidance |

A bootcamp **makes sense** when you already hold a quantitative bachelor (**CS/engineering/statistics/physics**) and only lack practical ML/tool skills, a portfolio, and a local network. Then you use it as an "accelerator."

A bootcamp is **not enough on its own** if you have no quantitative background at all. You can't truly learn linear algebra + probability + ML theory in 3 months, and employers notice.

**Bold truth:** A bootcamp is a **complement, not a full substitute for a degree.** *As of 2025/2026 they typically cost ~€9,000–15,000; verify.*

## Portfolio is critical: Kaggle, GitHub, a real project

Whatever your degree or bootcamp, what makes you stand out is **provable work**. German employers don't care that your CV says "I know Python" — they care about **what you built**.

- **GitHub:** 2-3 clean, documented real projects. README, data, notebook, result. Not a "Titanic tutorial" — your own problem.
- **Kaggle:** A competition or dataset contribution. No need for a top rank, but activity counts.
- **End-to-end project:** Data collection → cleaning → model → evaluation → (if possible) deployment (that smells like MLOps).
- **Blog/writeup:** A short piece explaining a project proves communication skills.

**Bold truth:** In Germany a **bootcamp graduate with a portfolio** can beat a **master's graduate without one** for some roles. But the strongest combination is: **degree + portfolio** together.

## Which background can you switch from?

Switching into DS/AI is common — in Germany **all quantitative bachelors** open the door, but not equally easily:

| Background | Ease of entry | What's missing (to fill) |
|---|---|---|
| **Computer Science (Informatik)** | Very easy | Depth in statistics/ML theory |
| **Mathematics / Statistics** | Very easy | Programming practice, engineering |
| **Physics** | Easy | Software/data-engineering practice |
| **Engineering (EE/Mechatronics)** | Medium-easy | Pure ML theory, statistics |
| **Econometrics / Economics** | Medium | Programming, ML engineering |
| **Non-quantitative fields** | Hard | A serious maths bridge is required |

A typical bridge: a **preparatory course / master's with conditions** that closes missing maths/stats/programming credits, or strong self-study + portfolio. Related comparison: [What to do with a computer science degree in Germany](/en/blog/what-to-do-with-a-computer-science-degree-in-germany-job-market-salary-en).

## Fresh graduate: 18-month job-search permit → work permit

International students who graduate from a German university get an **18-month job-search residence permit**. In technical fields like DS/AI, this time is **gold**: your campus, internships, and network are fresh, and your language is somewhat settled.

- After graduating you can stay and work **without restriction for 18 months** while you look for a job.
- Once you land a role in your field → switch to the **Blue Card** or a qualified work visa.
- MINT (STEM) fields count as shortage occupations, so the Blue Card threshold is **lower**. *As of 2025, ~€43,760 for MINT/new graduates vs a general threshold of ~€48,300; updated yearly, verify.*

For the master's vs job-seeker visa strategy: [Master's vs job-seeker visa in Germany](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en). And note: **the Studienkolleg is not a language school** — for anyone coming for a bachelor's: [What the Studienkolleg really is](/en/blog/studienkolleg-is-not-a-language-school-what-it-really-is-en).

## Common mistakes (learning only tools, skipping maths, neglecting German)

The most frequent traps when trying to break in:

1. **Learning only "tools."** Pandas, scikit-learn, a few tutorials... but no **linear algebra, probability, ML theory**. German interviews ask theory; this trap filters you out in round one.
2. **Skipping the maths.** "The libraries handle it" works early on; in senior roles you hit a wall.
3. **Neglecting German.** Even when the job is **in English**: internships, daily life, team communication, and some employers make German matter. Aim for at least **B1-B2**.
4. **Applying without a portfolio.** "I finished a bootcamp" alone is a weak signal.
5. **Targeting the wrong role.** Data Scientist, ML Engineer, or Data Engineer? Know the difference and prepare accordingly: [Working as a Data Scientist / ML Engineer in Germany](/en/blog/working-as-a-data-scientist-ml-engineer-in-germany-blue-card-salary-en).

## Conclusion & honest advice

There's no shortcut into Data Science & AI in Germany, but there is a clear path: **a quantitative bachelor → an English-taught DS/AI master's → a strong portfolio → a job via the 18-month job-search permit → the Blue Card.** If you already have a quantitative background, you can accelerate with a bootcamp + portfolio; if not, build the **maths bridge** first. The biggest mistake is skipping the theory and learning only tools — the German job market doesn't forgive that. Build your degree, your portfolio, and (yes) your German together.

*This article is a general guide as of early 2026; salaries, Blue Card thresholds, bootcamp fees, visa timelines, and programme requirements change year to year. Always verify the current details with the university, the employer, and official immigration authorities before applying.*
MD;

        $variants = [
            'tr' => ['slug'=>'how-to-break-into-data-science-ai-in-germany',    'title'=>'Almanya\'da Data Science & AI\'ye Nasıl Girilir? Derece, Bootcamp, Geçiş (2026)', 'excerpt'=>'Almanya\'da Data Science ve AI alanına giriş yolları: CS/DS/AI master ana yol mu, bootcamp\'ler (Data Science Retreat, neuefische, Le Wagon) kime uygun, portfolyo neden kritik ve hangi arka plandan geçilir — dürüst bir rehber (2026).', 'meta_title'=>'Almanya\'da Data Science & AI\'ye Nasıl Girilir? (2026)', 'meta_description'=>'Almanya\'da Data Science & AI\'ye giriş: master mı bootcamp mı, portfolyo, arka plan geçişi, 18 ay iş-arama izni ve yaygın hatalar (2026, doğrula).', 'body'=>$trBody],
            'de' => ['slug'=>'how-to-break-into-data-science-ai-in-germany-de', 'title'=>'Einstieg in Data Science & KI in Deutschland: Abschluss, Bootcamp, Quereinstieg (2026)', 'excerpt'=>'Wie du in Deutschland in Data Science und KI einsteigst: Ist der DS/KI-Master der Hauptweg, für wen passen Bootcamps (Data Science Retreat, neuefische, Le Wagon), warum ist ein Portfolio entscheidend und aus welchem Hintergrund gelingt der Quereinstieg (2026).', 'meta_title'=>'Einstieg in Data Science & KI in Deutschland (2026)', 'meta_description'=>'Data Science & KI in Deutschland: Master oder Bootcamp, Portfolio, Quereinstieg, 18 Monate Jobsuche-Erlaubnis und häufige Fehler (2026, bitte prüfen).', 'body'=>$deBody],
            'en' => ['slug'=>'how-to-break-into-data-science-ai-in-germany-en', 'title'=>'How to Break Into Data Science & AI in Germany: Degree, Bootcamp, Switching (2026)', 'excerpt'=>'How to break into Data Science and AI in Germany: is a DS/AI master\'s the main road, who bootcamps (Data Science Retreat, neuefische, Le Wagon) fit, why a portfolio is critical, and which background you can switch from — an honest guide (2026).', 'meta_title'=>'How to Break Into Data Science & AI in Germany (2026)', 'meta_description'=>'Data Science & AI in Germany: master\'s vs bootcamp, portfolio, career switching, the 18-month job-search permit, and common mistakes (2026, verify).', 'body'=>$enBody],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale'           => $locale,
                'translation_group_id' => $groupId,
                'user_id'          => $userId,
                'category_id'      => $categoryId,
                'title'            => $v['title'],
                'excerpt'          => Str::limit($v['excerpt'], 250, '…'),
                'content_md'       => $v['body'],
                'content_html'     => $html,
                'meta_title'       => $v['meta_title'],
                'meta_description' => Str::limit($v['meta_description'], 158, '…'),
                'reading_minutes'  => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
                'is_published'     => true,
                'published_at'     => now(),
            ];
            $existing = Post::where('slug', $v['slug'])->first();
            $existing ? $existing->update($payload) : Post::create($payload + ['slug' => $v['slug']]);
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'how-to-break-into-data-science-ai-in-germany',
            'how-to-break-into-data-science-ai-in-germany-de',
            'how-to-break-into-data-science-ai-in-germany-en',
        ])->delete();
    }
};

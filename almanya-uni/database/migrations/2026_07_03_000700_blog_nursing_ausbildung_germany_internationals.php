<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da hemşirelik Ausbildung — uluslararası öğrenciler için maaşlı eğitim (2026).
 * Doğrulandı: generalistische Pflegeausbildung 3 yıl, MAAŞLI (~1.100-1.400€/ay, yıl-hedge), teori+uygulama;
 * gerek B2 Almanca + lise diploması + Ausbildungsplatz; sonunda Pflegefachfrau/-mann. Sayılar hedge'li, resmi kaynağa yönlendirilir.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'f3a30000-3333-4c4e-9f70-ff01aa06cc03';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da hemşire olmanın iki ana yolu var: yurtdışında aldığın diplomayı **tanıtmak (Anerkennung)** ya da **sıfırdan meslek eğitimine (Ausbildung)** başlamak. Bu yazı ikincisine odaklanıyor: henüz hemşirelik diploman yoksa ya da kariyerini sıfırdan Almanya'da kurmak istiyorsan, **generalistische Pflegeausbildung** senin için doğru kapı olabilir. En güzel yanı: bu eğitim **maaşlı**.

## Ausbildung nedir: 3 yıl, maaşlı, teori + uygulama

Almanya'daki mesleki eğitim sistemi (Ausbildung), okulu bitirmenle işe başlaman arasındaki köprüdür. Hemşirelikte bu, 2020 Pflegeberufegesetz ile gelen **generalistische (genelci) Pflegeausbildung**tır. Eskiden ayrı olan hastane hemşireliği (Krankenpflege), yaşlı bakımı (Altenpflege) ve çocuk hemşireliği tek bir genelci meslekte birleşti.

Temel özellikler (2025/2026 itibarıyla, yaklaşık; doğrula):
- **Süre:** genelde **3 yıl** (tam zamanlı).
- **Yapı:** **teori** (Pflegeschule / meslek okulunda ders) + **uygulama** (hastane, bakım evi, poliklinik gibi kurumlarda rotasyon). İkisi iç içe gider.
- **Maaş:** eğitim boyunca **maaş alırsın** — çünkü bir işverenle (hastane/bakım kurumu) sözleşme imzalarsın. Bu, Ausbildung'u üniversite okumaktan ayıran en büyük avantajdır.
- **Sonuç:** başarıyla bitirince **Pflegefachfrau / Pflegefachmann** (nitelikli bakım uzmanı) unvanını alırsın — Almanya çapında geçerli, tam yetkili bir meslek.

## Kimler için: sıfırdan başlayanlar

Ausbildung yolu özellikle şuraya uygun:
- Henüz **hemşirelik diploması olmayanlar** (lise mezunu ya da başka alandan gelenler).
- Diploması olup da tanınma (Anerkennung) sürecinin bürokrasisiyle uğraşmak yerine **Almanya sistemine baştan girmek** isteyenler.
- Almanya'da **hem çalışıp hem öğrenerek** kalıcı bir kariyer kurmak isteyenler.

Diploman zaten varsa, tanınma yolu daha hızlı olabilir — o zaman [tanınma (Anerkennung) rehberimize](/tr/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung) bakmanı öneririm. İki yolun karşılaştırması için de [Almanya'da hemşire olmak genel rehberi](/tr/blog/becoming-a-nurse-in-germany-as-a-foreigner) iyi bir başlangıç.

## Şartlar: B2 Almanca + lise diploması + Ausbildungsplatz

Ausbildung'a kabul için genelde şu üç şey gerekir (kurum/eyalete göre değişir, doğrula):

| Şart | Ne demek | Not |
|---|---|---|
| **Almanca dil seviyesi** | Çoğu okul/işveren **B2** ister (bazıları B1'le başlatıp süreçte B2'ye çıkarır) | En kritik engel; bakım işi iletişim-yoğun |
| **Okul diploması** | En az **lise/orta öğretim** denkliği | Diplomanın denkliği ayrıca değerlendirilebilir |
| **Ausbildungsplatz** | Bir işverenle (hastane/bakım kurumu) **eğitim sözleşmesi** | Bunu bulman gerekir — burs değil, iş sözleşmesi |

**Dil neden bu kadar önemli:** hastalarla, ailelerle ve ekiple sürekli konuşursun; hataların bedeli ağır olabilir. Bu yüzden dil, teknik bilgiden önce gelir. Almancanı planlı ilerletmek için [sıfırdan C1'e Almanca yol haritası](/tr/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh) yazımız işine yarayacak.

## Maaş: eğitim boyunca ne kadar kazanırsın?

Ausbildung'un en somut cazibesi: **eğitimin sırasında maaş alırsın.** Aşağıdaki rakamlar **2025/2026 itibarıyla, yaklaşık; kesinlikle doğrula** — işveren, Tarif sözleşmesi (ör. TVöD-P) ve eyalete göre ciddi değişir:

| Ausbildung yılı | Yaklaşık brüt maaş/ay | Not |
|---|---|---|
| 1. yıl | ~1.100–1.200 € | Tarife bağlı; değişir |
| 2. yıl | ~1.200–1.300 € | Genelde yılda artar |
| 3. yıl | ~1.300–1.400 € | Bitişe yakın en yüksek |

Bu maaş bir üniversite bursu değil; **çalıştığın için aldığın ücrettir**. Eğitim bitince, tam Pflegefachkraft olarak maaşın belirgin biçimde yükselir (giriş seviyesi kabaca ~3.000–3.600 € brüt/ay aralığında konuşuluyor — yine yıl-hedge'li, doğrula). Detay için [hemşire olarak çalışmak: maaş, dil ve gerçek](/tr/blog/working-as-a-nurse-in-germany-salary-language-and-reality) yazımıza bak.

**Kalın gerçek:** Ausbildung maaşı geçinmene yeter ama lüks değildir; büyük şehirlerde kira zorlayabilir.

## Ausbildung vizesi ve başvuru

Türkiye'den geliyorsan sıralama kabaca şöyle işler (2025/2026 itibarıyla, yaklaşık — resmi kaynaktan doğrula):
1. **Almancanı yükselt** (hedef B2; en azından B1 ile başvuru şansı).
2. **Ausbildungsplatz bul** — bir hastane/bakım kurumuyla eğitim sözleşmesi imzala. Alman işverenler uluslararası aday için aktif alım yapıyor (Pflegenotstand — bakım açığı).
3. **Ausbildung vizesi başvurusu** — mesleki eğitim amaçlı ulusal vize. Almanya'da **hızlandırılmış nitelikli işçi prosedürü** de bazı durumlarda süreci kısaltabilir.
4. **Denklik/diploma belgeleri**, dil sertifikası, sözleşme ve maddi durum kanıtı ile başvuruyu tamamla.

**Kesin vize adımları ve güncel şartlar için** ilgili eyaletin makamına ve resmi **anerkennung-in-deutschland.de** / **make-it-in-germany.com** kaynaklarına danış. Burada garanti veremem; kurallar değişir.

Not: Almanya'da zaten öğrenciysen ve Ausbildung'a geçmek istiyorsan, [öğrencilikten Ausbildung'a geçiş rehberimiz](/tr/blog/switching-from-study-to-ausbildung-germany-residence-permit) tam sana göre.

## Sonrası: Pflegefachfrau/-mann ve kariyer

Ausbildung'u bitirince kapılar açılır:
- **Tam yetkili Pflegefachfrau/-mann** olursun — Almanya çapında aranan bir meslek.
- **İş garantisi denecek kadar yüksek talep** var; işsizlik neredeyse yok.
- **Kalıcı oturuma (Niederlassungserlaubnis)** giden net bir yol; birkaç yıl çalıştıktan sonra başvurabilirsin.
- **Uzmanlaşma:** yoğun bakım, ameliyathane, anestezi, palyatif bakım gibi alanlarda ileri eğitimler; ekip liderliği; hatta üniversitede Pflege okuyup akademik yola geçmek.

## Sonuç & dürüst tavsiye

Ausbildung, hemşirelik diploman yoksa Almanya'ya girmenin en sağlam yollarından biri: **maaşlı, yapılandırılmış ve kalıcı oturuma açılan** bir yol. Ama romantize etmeyeyim — iş **fiziksel ve duygusal olarak zor**, vardiyalı ve Ausbildung maaşı mütevazıdır. En büyük engel para değil, **dildir**: B2 olmadan ne kabul ne de sağlıklı bir iş hayatı olur.

Dürüst tavsiyem: önce **Almancaya yüklen**, paralelde **Ausbildungsplatz** aramaya başla, ve vize/denklik adımlarının hiçbirini kulaktan dolma bilgiyle yürütme — her adımı resmi kaynaktan teyit et. Sabırlı olursan, 3 yıl sonra elinde Almanya'nın en talep gören mesleklerinden biri olur.

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır; maaşlar, dil şartları, vize ve Ausbildung kuralları eyalete, işverene ve zamana göre değişir. Nihai kararında mutlaka resmi Anerkennungsstelle / anerkennung-in-deutschland.de ve ilgili konsolosluk/makam bilgisini doğrula.*
MD;

        $deBody = <<<'MD'
Es gibt zwei Hauptwege, um in Deutschland Pflegefachkraft zu werden: entweder lässt du deinen ausländischen Abschluss **anerkennen (Anerkennung)** oder du startest von Grund auf mit einer **Ausbildung**. Dieser Artikel dreht sich um den zweiten Weg: Wenn du noch keinen Pflegeabschluss hast oder deine Karriere komplett in Deutschland aufbauen willst, ist die **generalistische Pflegeausbildung** dein Weg. Das Beste daran: Diese Ausbildung wird **vergütet**.

## Was ist die Ausbildung: 3 Jahre, bezahlt, Theorie + Praxis

Die deutsche Ausbildung verbindet Schule und Beruf. In der Pflege heißt das seit dem Pflegeberufegesetz 2020: **generalistische Pflegeausbildung**. Die früher getrennten Bereiche Kranken-, Alten- und Kinderkrankenpflege wurden zu einem einzigen generalistischen Beruf zusammengeführt.

Die wichtigsten Merkmale (Stand 2025/2026, ungefähr; bitte prüfen):
- **Dauer:** in der Regel **3 Jahre** (Vollzeit).
- **Aufbau:** **Theorie** (Unterricht an der Pflegeschule) + **Praxis** (Einsätze in Krankenhaus, Pflegeheim, ambulanter Pflege). Beides greift ineinander.
- **Vergütung:** Du bekommst während der Ausbildung **ein Gehalt**, weil du einen Vertrag mit einem Träger (Klinik/Pflegeeinrichtung) schließt. Das ist der große Vorteil gegenüber einem Studium.
- **Abschluss:** Nach bestandener Prüfung trägst du den Titel **Pflegefachfrau / Pflegefachmann** — bundesweit gültig und voll anerkannt.

## Für wen: Anfänger ohne Vorabschluss

Der Ausbildungsweg passt besonders, wenn du:
- **noch keinen Pflegeabschluss** hast (Schulabschluss oder Quereinstieg),
- einen Abschluss hast, aber die Bürokratie der Anerkennung umgehen und **von Anfang an ins deutsche System** einsteigen willst,
- in Deutschland **arbeiten und gleichzeitig lernen** und eine dauerhafte Karriere aufbauen willst.

Wenn du bereits einen Abschluss hast, kann die Anerkennung schneller sein — dann lies unseren [Leitfaden zur Anerkennung](/de/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung-de). Für den Vergleich beider Wege ist unser [Überblick, wie du in Deutschland Pflegefachkraft wirst](/de/blog/becoming-a-nurse-in-germany-as-a-foreigner-de) ein guter Start.

## Voraussetzungen: B2-Deutsch + Schulabschluss + Ausbildungsplatz

Für die Zulassung brauchst du meist diese drei Dinge (je nach Träger/Bundesland, bitte prüfen):

| Voraussetzung | Was gemeint ist | Hinweis |
|---|---|---|
| **Deutschkenntnisse** | Die meisten Schulen/Arbeitgeber verlangen **B2** (manche starten mit B1) | Größte Hürde; Pflege ist kommunikationsintensiv |
| **Schulabschluss** | mindestens **mittlerer Schulabschluss / Abitur** (Gleichwertigkeit) | Dein Abschluss kann geprüft werden |
| **Ausbildungsplatz** | ein **Ausbildungsvertrag** mit einem Träger | Den musst du finden — kein Stipendium, ein Arbeitsvertrag |

**Warum die Sprache so wichtig ist:** Du sprichst ständig mit Patienten, Angehörigen und dem Team; Fehler können schwerwiegend sein. Deshalb kommt die Sprache vor dem Fachwissen. Um dein Deutsch planvoll aufzubauen, hilft dir unsere [Deutsch-Roadmap von Null bis C1](/de/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh-de).

## Gehalt: Was verdienst du während der Ausbildung?

Der größte konkrete Vorteil: Du **bekommst während der Ausbildung ein Gehalt.** Die folgenden Zahlen sind **Stand 2025/2026, ungefähr — unbedingt prüfen**; sie hängen stark vom Träger, vom Tarifvertrag (z. B. TVöD-P) und vom Bundesland ab:

| Ausbildungsjahr | Ungefähres Bruttogehalt/Monat | Hinweis |
|---|---|---|
| 1. Jahr | ~1.100–1.200 € | tarifabhängig; variiert |
| 2. Jahr | ~1.200–1.300 € | steigt meist jährlich |
| 3. Jahr | ~1.300–1.400 € | am Ende am höchsten |

Das ist kein Stipendium, sondern **Lohn für deine Arbeit**. Nach der Ausbildung steigt dein Gehalt als volle Pflegefachkraft deutlich (Einstieg wird grob mit ~3.000–3.600 € brutto/Monat genannt — ebenfalls mit Jahres-Hinweis, bitte prüfen). Details findest du in [Als Pflegekraft arbeiten: Gehalt, Sprache und Realität](/de/blog/working-as-a-nurse-in-germany-salary-language-and-reality-de).

**Ehrliche Tatsache:** Das Ausbildungsgehalt reicht zum Leben, ist aber kein Luxus; in Großstädten kann die Miete knapp werden.

## Ausbildungsvisum und Bewerbung

Von außerhalb der EU läuft es grob so (Stand 2025/2026, ungefähr — offiziell prüfen):
1. **Deutsch verbessern** (Ziel B2; mindestens B1 zur Bewerbung).
2. **Ausbildungsplatz finden** — Ausbildungsvertrag mit einer Klinik/Pflegeeinrichtung. Deutsche Arbeitgeber rekrutieren aktiv international (Pflegenotstand).
3. **Visum zur Berufsausbildung** beantragen — nationales Visum. Das **beschleunigte Fachkräfteverfahren** kann in manchen Fällen helfen.
4. Bewerbung mit **Zeugnissen**, Sprachnachweis, Vertrag und Nachweis der Lebenshaltungsmittel abschließen.

**Für die genauen Visumsschritte und aktuellen Bedingungen** wende dich an die zuständige Behörde deines Bundeslandes und an die offiziellen Quellen **anerkennung-in-deutschland.de** / **make-it-in-germany.com**. Garantien gibt es hier nicht; Regeln ändern sich.

Hinweis: Wenn du bereits in Deutschland studierst und in eine Ausbildung wechseln willst, ist unser [Leitfaden zum Wechsel vom Studium zur Ausbildung](/de/blog/switching-from-study-to-ausbildung-germany-residence-permit-de) genau richtig.

## Danach: Pflegefachfrau/-mann und Karriere

Nach der Ausbildung öffnen sich Türen:
- Du wirst **voll qualifizierte Pflegefachfrau/-mann** — ein bundesweit gefragter Beruf.
- Die **Nachfrage ist so hoch**, dass Arbeitslosigkeit kaum existiert.
- Ein klarer Weg zur **Niederlassungserlaubnis**; nach einigen Jahren Arbeit kannst du sie beantragen.
- **Spezialisierung:** Intensivpflege, OP, Anästhesie, Palliativ; Teamleitung; oder ein Pflegestudium für den akademischen Weg.

## Fazit & ehrlicher Rat

Die Ausbildung ist einer der solidesten Wege nach Deutschland, wenn du keinen Pflegeabschluss hast: **bezahlt, strukturiert und mit Weg zur Niederlassung**. Aber ich romantisiere nichts — die Arbeit ist **körperlich und emotional anstrengend**, im Schichtdienst, und das Ausbildungsgehalt ist bescheiden. Die größte Hürde ist nicht Geld, sondern die **Sprache**: Ohne B2 gibt es weder Zulassung noch ein gesundes Arbeitsleben.

Mein ehrlicher Rat: Konzentriere dich zuerst auf **Deutsch**, suche parallel einen **Ausbildungsplatz**, und wickle keinen Visums- oder Anerkennungsschritt nach Hörensagen ab — bestätige jeden Schritt aus offizieller Quelle. Mit Geduld hast du nach 3 Jahren einen der gefragtesten Berufe Deutschlands.

*Dieser Artikel dient der allgemeinen Information (Stand Anfang 2026); Gehälter, Sprachanforderungen, Visum und Ausbildungsregeln ändern sich je nach Bundesland, Arbeitgeber und Zeit. Bestätige deine endgültige Entscheidung immer bei der offiziellen Anerkennungsstelle / anerkennung-in-deutschland.de und der zuständigen Auslandsvertretung/Behörde.*
MD;

        $enBody = <<<'MD'
There are two main ways to become a nurse in Germany: get your foreign qualification **recognized (Anerkennung)** or start from scratch with an **Ausbildung**. This article is about the second path: if you don't have a nursing qualification yet, or you want to build your career in Germany from the ground up, the **generalist nursing Ausbildung** is your door. The best part: this training is **paid**.

## What the Ausbildung is: 3 years, paid, theory + practice

Germany's Ausbildung system bridges school and profession. In nursing, since the 2020 Nursing Professions Act (Pflegeberufegesetz), this is the **generalist nursing Ausbildung (generalistische Pflegeausbildung)**. The formerly separate fields of hospital nursing (Krankenpflege), elderly care (Altenpflege) and pediatric nursing were merged into one generalist profession.

Key features (as of 2025/2026, approximate; verify):
- **Duration:** usually **3 years** (full-time).
- **Structure:** **theory** (classes at a nursing school / Pflegeschule) + **practice** (rotations in hospitals, care homes, outpatient care). The two interlock.
- **Pay:** you receive **a salary** during the training, because you sign a contract with an employer (clinic/care provider). This is the big advantage over a university degree.
- **Result:** after passing the final exam you hold the title **Pflegefachfrau / Pflegefachmann** (qualified nursing specialist) — valid nationwide and fully recognized.

## Who it's for: beginners starting from scratch

The Ausbildung path fits especially if you:
- **don't yet have a nursing qualification** (school leaver or career changer),
- have a qualification but want to skip the recognition bureaucracy and **enter the German system from the start**,
- want to **work and study at the same time** and build a lasting career in Germany.

If you already have a qualification, recognition may be faster — then read our [guide to recognition (Anerkennung)](/en/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung-en). For a comparison of both paths, our [overview of becoming a nurse in Germany](/en/blog/becoming-a-nurse-in-germany-as-a-foreigner-en) is a good start.

## Requirements: B2 German + school diploma + Ausbildungsplatz

To be admitted you usually need these three things (varies by provider/state, verify):

| Requirement | What it means | Note |
|---|---|---|
| **German level** | Most schools/employers ask for **B2** (some start at B1) | Biggest hurdle; care work is communication-heavy |
| **School diploma** | at least **secondary school / high school** equivalency | Your diploma may be assessed separately |
| **Ausbildungsplatz** | a **training contract** with an employer (clinic/care provider) | You must find it — not a scholarship, a work contract |

**Why language matters so much:** you talk constantly with patients, families and the team; mistakes can be serious. That's why language comes before technical knowledge. To build your German methodically, our [German roadmap from zero to C1](/en/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh-en) will help.

## Salary: how much do you earn during training?

The most concrete appeal: you **get paid during the training.** The figures below are **as of 2025/2026, approximate — definitely verify**; they vary a lot by employer, collective agreement (e.g. TVöD-P) and state:

| Ausbildung year | Approx. gross salary/month | Note |
|---|---|---|
| Year 1 | ~€1,100–1,200 | tariff-dependent; varies |
| Year 2 | ~€1,200–1,300 | usually rises yearly |
| Year 3 | ~€1,300–1,400 | highest near the end |

This is not a scholarship; it's **wages for your work**. After the Ausbildung, your pay as a full Pflegefachkraft rises significantly (entry level is roughly cited at ~€3,000–3,600 gross/month — again year-hedged, verify). See [working as a nurse: salary, language and reality](/en/blog/working-as-a-nurse-in-germany-salary-language-and-reality-en) for details.

**Blunt fact:** the training salary is enough to live on but not luxury; in big cities rent can be tight.

## Ausbildung visa and application

Coming from outside the EU, the sequence roughly works like this (as of 2025/2026, approximate — verify officially):
1. **Improve your German** (target B2; at least B1 to apply).
2. **Find an Ausbildungsplatz** — sign a training contract with a clinic/care provider. German employers actively recruit internationally (Pflegenotstand — care shortage).
3. **Apply for the vocational training visa** — a national visa. The **fast-track skilled worker procedure** can help in some cases.
4. Complete the application with **certificates**, language proof, contract and evidence of financial means.

**For the exact visa steps and current conditions**, consult your state's authority and the official sources **anerkennung-in-deutschland.de** / **make-it-in-germany.com**. No guarantees here; rules change.

Note: if you already study in Germany and want to switch to an Ausbildung, our [guide to switching from study to Ausbildung](/en/blog/switching-from-study-to-ausbildung-germany-residence-permit-en) is exactly for you.

## After: Pflegefachfrau/-mann and career

After the Ausbildung, doors open:
- You become a **fully qualified Pflegefachfrau/-mann** — a profession in demand nationwide.
- Demand is **so high** that unemployment barely exists.
- A clear path to **permanent residence (Niederlassungserlaubnis)**; after a few years of work you can apply.
- **Specialization:** intensive care, OR, anesthesia, palliative care; team leadership; or a nursing degree for the academic route.

## Conclusion & honest advice

The Ausbildung is one of the most solid ways into Germany if you don't have a nursing qualification: **paid, structured and leading to permanent residence**. But I won't romanticize it — the work is **physically and emotionally demanding**, on shifts, and the training salary is modest. The biggest hurdle isn't money, it's **language**: without B2 there's neither admission nor a healthy working life.

My honest advice: focus on **German** first, start looking for an **Ausbildungsplatz** in parallel, and don't handle any visa or recognition step on hearsay — confirm every step from an official source. With patience, in 3 years you'll hold one of Germany's most sought-after professions.

*This article is general information as of early 2026; salaries, language requirements, visa and Ausbildung rules change by state, employer and over time. Always confirm your final decision with the official Anerkennungsstelle / anerkennung-in-deutschland.de and the relevant consulate/authority.*
MD;

        $variants = [
            'tr' => ['slug'=>'nursing-ausbildung-in-germany-for-internationals-paid-training',    'title'=>'Almanya\'da Hemşirelik Ausbildung: Uluslararası Öğrenciler İçin Maaşlı Eğitim (2026)', 'excerpt'=>'Almanya\'da hemşirelik diploman yoksa: 3 yıllık maaşlı generalistische Pflegeausbildung. Şartlar (B2 + lise + Ausbildungsplatz), maaş (~1.100-1.400€/ay, hedge), vize ve sonunda Pflegefachfrau/-mann olmak.', 'meta_title'=>'Almanya Hemşirelik Ausbildung: Maaşlı Eğitim (2026)', 'meta_description'=>'Almanya\'da sıfırdan hemşire ol: 3 yıllık maaşlı Pflegeausbildung. Şartlar, maaş (~1.100-1.400€/ay, yaklaşık), vize ve kariyer. Rakamlar hedge\'li — resmi kaynaktan doğrula.', 'body'=>$trBody],
            'de' => ['slug'=>'nursing-ausbildung-in-germany-for-internationals-paid-training-de', 'title'=>'Pflegeausbildung in Deutschland: Bezahlte Ausbildung für Internationale (2026)', 'excerpt'=>'Ohne Pflegeabschluss nach Deutschland: die 3-jährige, vergütete generalistische Pflegeausbildung. Voraussetzungen (B2 + Schulabschluss + Ausbildungsplatz), Gehalt (~1.100-1.400€/Monat, ungefähr), Visum und der Weg zur Pflegefachfrau/-mann.', 'meta_title'=>'Pflegeausbildung Deutschland: bezahlt, für Internationale (2026)', 'meta_description'=>'Werde in Deutschland von Grund auf Pflegekraft: 3 Jahre vergütete Pflegeausbildung. Voraussetzungen, Gehalt (~1.100-1.400€/Monat, ca.), Visum, Karriere. Zahlen ungefähr — offiziell prüfen.', 'body'=>$deBody],
            'en' => ['slug'=>'nursing-ausbildung-in-germany-for-internationals-paid-training-en', 'title'=>'Nursing Ausbildung in Germany: Paid Training for Internationals (2026)', 'excerpt'=>'No nursing qualification yet? Enter Germany via the 3-year, paid generalist nursing Ausbildung. Requirements (B2 + school diploma + Ausbildungsplatz), salary (~€1,100-1,400/month, approx.), visa, and becoming a Pflegefachfrau/-mann.', 'meta_title'=>'Nursing Ausbildung in Germany: Paid Training (2026)', 'meta_description'=>'Become a nurse in Germany from scratch: a 3-year paid nursing Ausbildung. Requirements, salary (~€1,100-1,400/month, approx.), visa and career. Figures hedged — verify officially.', 'body'=>$enBody],
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
            'nursing-ausbildung-in-germany-for-internationals-paid-training',
            'nursing-ausbildung-in-germany-for-internationals-paid-training-de',
            'nursing-ausbildung-in-germany-for-internationals-paid-training-en',
        ])->delete();
    }
};

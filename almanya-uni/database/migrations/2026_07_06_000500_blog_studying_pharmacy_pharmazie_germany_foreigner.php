<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Eczacılık (Pharmazie) okumak — uluslararası öğrenci rehberi (2026).
 * Doğrulandı: Pharmazie 4 yıl (8 dönem) + 1 yıl Praktisches Jahr → 3 Staatsexamen → Approbation als Apotheker,
 * tamamen Almanca, İngilizce program yok; NC rekabetçi ama tıp/diş kadar acımasız değil;
 * yol = uni-assist + sık sık Studienkolleg (M-Kurs) + C1 Almanca. Sayılar yıl-hedge'li (2025/2026, doğrula).
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'f9a10000-1111-4cae-9fd0-ff07aa0ccc01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almanya'da eczacılık (Pharmazie) okuyabilir miyim?" sorusunun dürüst yanıtı: **evet, mümkün — ve tıp ya da diş hekimliğine göre daha ulaşılabilir bir yol.** Ama koşullar net: program **tamamen Almancadır**, sonunda **Staatsexamen** ve **Approbation als Apotheker** (eczacı ruhsatı) gelir. Bu yazı, hayal kırıklığı yaşamamanız için gerçekleri sıcağı sıcağına anlatır.

## 1. Pharmazie nasıl bir programdır? (4 yıl + Praktisches Jahr, 3 Staatsexamen, İngilizce YOK)
Almanya'da eczacılık lisansı **4 yıl (8 dönem)** teorik/pratik eğitim + ardından **1 yıllık Praktisches Jahr (pratik yıl)** olmak üzere yaklaşık **5 yıl** sürer. Praktisches Jahr'ın en az yarısı bir **halk eczanesinde (öffentliche Apotheke)** yapılır; diğer yarısı hastane eczanesi, ilaç sanayii veya araştırma kurumunda geçebilir. Süreç boyunca **üç ayrı devlet sınavı (Staatsexamen)** verirsiniz ve en sonunda eyalet makamından **Approbation als Apotheker** alırsınız.

En kritik gerçek şu: eczacılık **düzenlenmiş bir sağlık mesleğidir** ve program **tamamen Almancadır** — devlet üniversitelerinde **İngilizce eczacılık lisansı YOKTUR.** Tıp ve diş hekimliğinde olduğu gibi burada da "İngilizce okuyup kurtulurum" seçeneği yoktur. (Yalnızca lisansüstü düzeyde, İngilizce **farmasötik bilimler / Drug Sciences** yüksek lisansları vardır; ama bunlar sizi eczacı yapmaz, ayrı bir akademik/sanayi yoludur.)

## 2. NC gerçeği: rekabetçi ama tıp/diş kadar acımasız değil
Eczacılık da çoğu yerde **Numerus Clausus (NC)** ile sınırlıdır. NC **rekabetçidir** — Alman öğrenciler için Abitur ortalaması genellikle **1,x** aralığında dolaşır. Ama işin iyi yanı şu: eczacılığın NC'si genellikle **tıp ve diş hekimliğinden belirgin biçimde daha ulaşılabilirdir.** Tıpta neredeyse 1,0 gerekirken, eczacılıkta daha esnek bir aralık ve daha fazla kontenjan söz konusudur.

> **Gerçeklik kontrolü:** Eczacılık kolay değildir, ama Almanya'nın "imkânsıza yakın" bölümü de değildir. Sağlık mesleklerine ilgi duyan ama tıp/diş NC'sini gerçekçi bulmayan uluslararası bir öğrenci için eczacılık **çok daha somut bir hedeftir.**

## 3. Başvuru yolu: uni-assist + Studienkolleg (M-Kurs)
AB-dışı (örneğin Türk) bir aday için tipik yol:
1. **uni-assist** üzerinden başvuru ve lise diplomanızın denklik ön değerlendirmesi.
2. Diplomanız doğrudan üniversite girişi (Hochschulzugangsberechtigung) saymıyorsa, önce **Studienkolleg (M-Kurs — tıp/biyoloji odaklı kur)** ve ardından **Feststellungsprüfung.** Studienkolleg bir dil kursu değildir; asıl amacı akademik ve konu bazlı hazırlıktır — ayrıntı için [Studienkolleg gerçekte nedir](/tr/blog/studienkolleg-is-not-a-language-school-what-it-really-is) yazısına bakın.
3. Bazı fakülteler seçim aşamasında **TMS (Test für Medizinische Studiengänge)** sonucunu dikkate alabilir; zorunlu olmasa da rekabette avantaj sağlayabilir — kendi hedef üniversitenizin kurallarını doğrulayın.

Başvuru takvimine dikkat: kış dönemi için son tarih genellikle **15 Temmuz**, yaz dönemi için **15 Ocak** civarındadır (üniversiteye göre değişir, mutlaka doğrulayın). Kontenjan mantığı tıpla benzerdir ama daha rahattır; karşılaştırma için [Almanya'da yabancı olarak tıp okumak](/tr/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas) yazısı işinize yarar.

## 4. Dil: C1 Almanca şart, mesleki Almanca gerçeği
Program tamamen Almanca olduğundan en az **C1 (DSH-2 / TestDaF)** dil belgesi gerekir; bazı fakülteler sağlık bölümleri için daha yüksek düzey isteyebilir. Ama kâğıt üstündeki minimum yetmez: kimya, farmakoloji ve galenik derslerinde **yoğun terminoloji**, sözlü sınavlar ve Praktisches Jahr'da eczanede **hastayla/müşteriyle Almanca iletişim** gerçek akıcılık ister.

> "Anlıyorum ama zor konuşuyorum" eczacılık için yeterli değildir. Başlamadan önce C1'inizi tazelemek ve mesleki Almancaya erkenden yatırım yapmak en akıllıca adımdır.

## 5. Tepe eczacılık fakülteleri
Eczacılık yalnızca köklü doğa bilimleri/tıp altyapısı olan üniversitelerde vardır. Sık anılan adresler arasında **LMU München, Heidelberg, Freiburg, Frankfurt, Bonn, Münster, Tübingen, Marburg, Berlin (FU) ve Kiel** sayılabilir. Kontenjanlar sınırlıdır ama tıp/diş kadar dar değildir. "Hangi üniversite daha iyi?" tartışması için [Almanya'da üniversite prestiji ve sıralamalar nasıl işler](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) yazısına göz atabilirsiniz — özetle: eczacılıkta üniversitenin adından çok, sizi eczacı yapan Approbation ve staj ağı önemlidir.

## 6. Ücret ve yaşam masrafı
Devlet üniversitelerinde eczacılık kural olarak **öğrenim ücreti almaz**; yalnızca dönemlik **Semesterbeitrag** ödenir. Baden-Württemberg eyaleti AB-dışı öğrencilerden dönemlik ücret alır. Aşağıdaki rakamlar **2025/2026 itibarıyla yaklaşıktır; başvurudan önce ilgili üniversiteden doğrulayın.**

| Kalem | Yaklaşık tutar (2025/2026, doğrula) |
|---|---|
| Devlet üniversitesi öğrenim ücreti | Yok (yalnızca Semesterbeitrag) |
| Semesterbeitrag | ~150–350 € / dönem |
| Baden-Württemberg (AB-dışı) | ~1.500 € / dönem |
| Yaşam masrafı (vize blokeli hesap) | ~11.900 € / yıl civarı, şehre göre değişir |
| Kitap, laboratuvar malzemesi | Ek bütçe (bölüme göre) |

Not: Vize için genellikle bir yıllık yaşam masrafını kanıtlayan **bloke hesap (Sperrkonto)** ve geçerli bir **sağlık sigortası** gerekir. Rakamları başvuru öncesi güncel resmi kaynaklardan doğrulayın.

## Sonuç & dürüst tavsiye
Dürüst olalım: **eczacılık, uluslararası bir öğrenci için Almanya'nın en gerçekçi sağlık meslekleri yollarından biridir.** NC rekabetçidir ama tıp/diş kadar acımasız değildir; program tamamen Almancadır ama karşılığında **çok yönlü ve sağlam bir kariyer** verir — halk eczanesi, hastane eczanesi ve özellikle uluslararası için erişilebilir olan **ilaç sanayii** (Bayer, Boehringer, Merck, Fresenius; regulatory affairs, Pharmakovigilanz). Bu kariyer tarafını [Almanya'da eczacı olarak çalışmak: eczane, ilaç sanayi ve maaş](/tr/blog/working-as-a-pharmacist-in-germany-pharmacy-industry-and-salary) yazısında ayrıntılandırıyoruz.

Önemli bir ayrım: **eğer zaten eczacıysanız**, sıfırdan okumak yerine çok daha gerçekçi olan yol Almanya'da diplomanızın tanınmasıdır (Approbation) — bunu [yurtdışı eczacı Almanya'da nasıl çalışır: Approbation ve tanınma](/tr/blog/foreign-pharmacist-in-germany-approbation-and-recognition) yazısında anlatıyoruz. "Tüm bu emeğe değer mi?" sorusunun dürüst muhasebesi için ise [Almanya'da eczacılık okumaya değer mi](/tr/blog/is-studying-pharmacy-in-germany-worth-it-honest-reality) yazısına bakın.

---
*2026 itibarıyla geçerli kurallara dayanır; NC, kontenjan, dil şartı ve ücretler eyalet ve üniversiteye göre değişir — başvurudan önce ilgili üniversitenin International Office'i ve uni-assist üzerinden doğrulayın.*
MD;

        $deBody = <<<'MD'
„Kann ich in Deutschland Pharmazie studieren?" Die ehrliche Antwort: **Ja, das ist möglich — und ein zugänglicherer Weg als Medizin oder Zahnmedizin.** Aber die Bedingungen sind klar: Das Studium ist **komplett auf Deutsch**, am Ende stehen das **Staatsexamen** und die **Approbation als Apotheker.** Dieser Beitrag nennt dir ehrlich die Fakten, damit du keine Enttäuschung erlebst.

## 1. Wie ist Pharmazie aufgebaut? (4 Jahre + Praktisches Jahr, 3 Staatsexamen, KEIN Englisch)
Das Pharmaziestudium in Deutschland dauert **4 Jahre (8 Semester)** Theorie und Praxis plus ein anschließendes **einjähriges Praktisches Jahr** — insgesamt also rund **5 Jahre.** Mindestens die Hälfte des Praktischen Jahres absolvierst du in einer **öffentlichen Apotheke**; die andere Hälfte kann in einer Krankenhausapotheke, in der Pharmaindustrie oder in der Forschung stattfinden. Auf dem Weg legst du **drei getrennte Staatsexamen** ab und erhältst am Ende von der Landesbehörde die **Approbation als Apotheker.**

Die wichtigste Wahrheit: Pharmazie ist ein **reglementierter Gesundheitsberuf** und das Studium ist **komplett auf Deutsch** — an staatlichen Universitäten gibt es **KEIN englischsprachiges Pharmaziestudium.** Wie bei Human- und Zahnmedizin existiert die Option „ich studiere einfach auf Englisch" nicht. (Nur auf Master-Ebene gibt es englischsprachige **Drug Sciences / Pharmaceutical Sciences**-Programme; die machen dich aber nicht zum Apotheker, sondern sind ein eigener akademischer/industrieller Weg.)

## 2. Die NC-Realität: kompetitiv, aber nicht so brutal wie Medizin/Zahnmedizin
Auch Pharmazie ist vielerorts durch einen **Numerus Clausus (NC)** beschränkt. Der NC ist **kompetitiv** — für deutsche Bewerber liegt der Abiturschnitt meist im Bereich **1,x.** Die gute Nachricht: Der NC in Pharmazie ist in der Regel **deutlich zugänglicher als in Medizin oder Zahnmedizin.** Während Medizin nahezu 1,0 verlangt, gibt es in Pharmazie einen entspannteren Bereich und mehr Plätze.

> **Realitätscheck:** Pharmazie ist nicht leicht, aber auch nicht Deutschlands „fast unmögliches" Fach. Für internationale Studierende, die einen Gesundheitsberuf wollen, den Medizin-/Zahnmedizin-NC aber unrealistisch finden, ist Pharmazie ein **viel konkreteres Ziel.**

## 3. Der Bewerbungsweg: uni-assist + Studienkolleg (M-Kurs)
Für Bewerber aus Nicht-EU-Ländern (z. B. Türkei) sieht der typische Weg so aus:
1. Bewerbung und Vorprüfung der Zeugnisanerkennung über **uni-assist**.
2. Wenn dein Schulabschluss keine direkte Hochschulzugangsberechtigung ergibt, zuerst das **Studienkolleg (M-Kurs — medizinisch/biologisch orientiert)** und danach die **Feststellungsprüfung.** Das Studienkolleg ist kein Sprachkurs, sondern eine fachliche Vorbereitung — Details unter [Was das Studienkolleg wirklich ist](/de/blog/studienkolleg-is-not-a-language-school-what-it-really-is-de).
3. Manche Fakultäten berücksichtigen im Auswahlverfahren ein **TMS-Ergebnis (Test für Medizinische Studiengänge)**; nicht überall Pflicht, aber ein Vorteil — prüfe die Regeln deiner Ziel-Uni.

Achte auf die Fristen: fürs Wintersemester meist um den **15. Juli**, fürs Sommersemester um den **15. Januar** (variiert je nach Uni — bitte prüfen). Die Quotenlogik ähnelt der Medizin, ist aber entspannter; zum Vergleich hilft [Als Ausländer Medizin in Deutschland studieren](/de/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-de).

## 4. Sprache: C1 Pflicht, und die Realität des Fachdeutsch
Da das Studium komplett auf Deutsch ist, brauchst du mindestens einen **C1-Nachweis (DSH-2 / TestDaF)**; manche Fakultäten verlangen für Gesundheitsfächer ein höheres Niveau. Aber das Papierminimum reicht nicht: In Chemie, Pharmakologie und Galenik brauchst du **intensive Fachterminologie**, in mündlichen Prüfungen und im Praktischen Jahr in der Apotheke die **Kommunikation mit Patienten/Kunden auf Deutsch** — also echte Flüssigkeit.

> „Ich verstehe, spreche aber schwer" genügt für Pharmazie nicht. Dein C1 vor dem Start aufzufrischen und früh in Fachdeutsch zu investieren ist der klügste Schritt.

## 5. Top-Fakultäten für Pharmazie
Pharmazie gibt es nur an Universitäten mit etablierter naturwissenschaftlich-medizinischer Basis. Häufig genannt werden **LMU München, Heidelberg, Freiburg, Frankfurt, Bonn, Münster, Tübingen, Marburg, Berlin (FU) und Kiel.** Die Plätze sind begrenzt, aber nicht so knapp wie in Medizin/Zahnmedizin. Zur Frage „welche Uni ist besser?" siehe [Wie Prestige und Rankings in Deutschland funktionieren](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de) — kurz gesagt: In Pharmazie zählt weniger der Name der Uni als die Approbation und das Praktikumsnetzwerk, das dich zum Apotheker macht.

## 6. Gebühren und Lebenshaltung
An staatlichen Universitäten ist Pharmazie in der Regel **studiengebührenfrei**; du zahlst nur den **Semesterbeitrag.** Baden-Württemberg erhebt von Nicht-EU-Studierenden eine Semestergebühr. Die folgenden Zahlen sind **Stand 2025/2026 ungefähr; vor der Bewerbung bei der jeweiligen Uni bestätigen.**

| Posten | Ungefährer Betrag (Stand 2025/2026, prüfen) |
|---|---|
| Studiengebühr staatliche Uni | Keine (nur Semesterbeitrag) |
| Semesterbeitrag | ~150–350 € / Semester |
| Baden-Württemberg (Nicht-EU) | ~1.500 € / Semester |
| Lebenshaltung (Sperrkonto fürs Visum) | ~11.900 € / Jahr, je nach Stadt |
| Bücher, Labormaterial | Zusätzliches Budget (je nach Fach) |

Hinweis: Fürs Visum brauchst du in der Regel ein **Sperrkonto**, das die Lebenshaltung für ein Jahr nachweist, sowie eine gültige **Krankenversicherung**. Prüfe die Zahlen vor der Bewerbung an aktuellen offiziellen Quellen.

## Fazit & ehrlicher Rat
Sei ehrlich: **Pharmazie ist für internationale Studierende einer der realistischsten Wege in einen Gesundheitsberuf in Deutschland.** Der NC ist kompetitiv, aber nicht so brutal wie in Medizin/Zahnmedizin; das Studium ist komplett auf Deutsch, gibt dir aber im Gegenzug eine **vielseitige und sichere Karriere** — öffentliche Apotheke, Krankenhausapotheke und vor allem die für Internationale zugängliche **Pharmaindustrie** (Bayer, Boehringer, Merck, Fresenius; Regulatory Affairs, Pharmakovigilanz). Diese Karriereseite vertiefen wir in [Als Apotheker in Deutschland arbeiten: Apotheke, Pharmaindustrie und Gehalt](/de/blog/working-as-a-pharmacist-in-germany-pharmacy-industry-and-salary-de).

Ein wichtiger Unterschied: **wenn du bereits Apotheker bist**, ist der weitaus realistischere Weg nicht das Studium von vorn, sondern die Anerkennung deines Abschlusses in Deutschland (Approbation) — das erklären wir in [Ausländischer Apotheker in Deutschland: Approbation und Anerkennung](/de/blog/foreign-pharmacist-in-germany-approbation-and-recognition-de). Zur ehrlichen Abrechnung „lohnt sich der ganze Aufwand?" siehe [Lohnt sich ein Pharmaziestudium in Deutschland](/de/blog/is-studying-pharmacy-in-germany-worth-it-honest-reality-de).

---
*Stand 2026; NC, Plätze, Sprachanforderung und Gebühren variieren je nach Bundesland und Universität — vor der Bewerbung beim International Office der Ziel-Uni und bei uni-assist bestätigen.*
MD;

        $enBody = <<<'MD'
"Can I study pharmacy (Pharmazie) in Germany?" The honest answer: **yes, it is possible — and a more accessible path than medicine or dentistry.** But the conditions are clear: the programme is **entirely in German**, and it ends with the **Staatsexamen** and the **Approbation als Apotheker** (pharmacist licence). To spare you disappointment, here are the honest facts.

## 1. How is Pharmazie structured? (4 years + Praktisches Jahr, 3 Staatsexamen, NO English)
The pharmacy degree in Germany takes **4 years (8 semesters)** of theory and practice plus a subsequent **one-year Praktisches Jahr** (practical year) — about **5 years** in total. At least half of the Praktisches Jahr is spent in a **public pharmacy (öffentliche Apotheke)**; the other half can be in a hospital pharmacy, the pharmaceutical industry or a research institution. Along the way you sit **three separate state exams (Staatsexamen)** and finally receive the **Approbation als Apotheker** from the state authority.

The most important truth: pharmacy is a **regulated health profession** and the programme is **entirely in German** — there is **NO English-taught pharmacy degree** at public universities. As with human and dental medicine, the "I'll just study in English" option does not exist. (Only at master's level are there English-taught **Drug Sciences / Pharmaceutical Sciences** programmes; but those do not make you a pharmacist — they are a separate academic/industry route.)

## 2. The NC reality: competitive, but not as brutal as medicine/dentistry
Pharmacy is also capped by a **Numerus Clausus (NC)** in most places. The NC is **competitive** — for German applicants the Abitur average usually hovers around **1.x.** The good news: the NC for pharmacy is generally **noticeably more accessible than for medicine or dentistry.** Where medicine demands nearly 1.0, pharmacy offers a more flexible range and more seats.

> **Reality check:** Pharmacy is not easy, but it is not Germany's "near-impossible" subject either. For an international student who wants a health profession but finds the medicine/dentistry NC unrealistic, pharmacy is a **far more concrete goal.**

## 3. The application route: uni-assist + Studienkolleg (M-Kurs)
For a non-EU applicant (e.g. from Turkey), the typical route is:
1. Apply and get a preliminary credential check via **uni-assist**.
2. If your school diploma does not grant direct university entry (Hochschulzugangsberechtigung), first the **Studienkolleg (M-Kurs — medicine/biology-oriented track)** and then the **Feststellungsprüfung.** The Studienkolleg is not a language course but subject-based preparation — details in [what the Studienkolleg really is](/en/blog/studienkolleg-is-not-a-language-school-what-it-really-is-en).
3. Some faculties may consider a **TMS (Test für Medizinische Studiengänge)** result in their selection; not mandatory everywhere, but an edge — check the rules of your target university.

Watch the deadlines: for the winter semester usually around **15 July**, for the summer semester around **15 January** (it varies by university — please verify). The quota logic mirrors medicine but is more relaxed; for comparison, see [studying medicine in Germany as a foreigner](/en/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-en).

## 4. Language: C1 required, and the reality of professional German
Because the programme is entirely in German, you need at least a **C1 certificate (DSH-2 / TestDaF)**; some faculties require a higher level for health subjects. But the paper minimum is not enough: chemistry, pharmacology and galenics courses demand **intensive terminology**, and oral exams plus the pharmacy placement in the Praktisches Jahr require **communicating with patients/customers in German** — that is, genuine fluency.

> "I understand but struggle to speak" is not enough for pharmacy. Refreshing your C1 before you start and investing early in professional German is the smartest move.

## 5. Top faculties for pharmacy
Pharmacy exists only at universities with an established natural-sciences/medical base. Frequently cited names include **LMU Munich, Heidelberg, Freiburg, Frankfurt, Bonn, Münster, Tübingen, Marburg, Berlin (FU) and Kiel.** Seats are limited but not as scarce as in medicine/dentistry. On the "which university is better?" question, see [how prestige and rankings work in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en) — in short, in pharmacy the university's name matters less than the Approbation and the placement network that make you a pharmacist.

## 6. Fees and living costs
At public universities pharmacy is generally **tuition-free**; you only pay the **Semesterbeitrag.** Baden-Württemberg charges non-EU students a semester fee. The figures below are **approximate as of 2025/2026; confirm with the relevant university before applying.**

| Item | Approx. amount (as of 2025/2026, verify) |
|---|---|
| Public university tuition | None (only Semesterbeitrag) |
| Semesterbeitrag | ~€150–350 / semester |
| Baden-Württemberg (non-EU) | ~€1,500 / semester |
| Living costs (blocked account for visa) | ~€11,900 / year, varies by city |
| Books, lab materials | Extra budget (depends on faculty) |

Note: for the visa you generally need a **blocked account (Sperrkonto)** proving one year of living costs plus valid **health insurance**. Verify the figures against current official sources before applying.

## Bottom line & honest advice
Be honest: **pharmacy is one of the most realistic routes into a health profession in Germany for an international student.** The NC is competitive but not as brutal as medicine/dentistry; the programme is entirely in German, but in return it gives you a **versatile and secure career** — public pharmacy, hospital pharmacy and, above all, the internationally accessible **pharmaceutical industry** (Bayer, Boehringer, Merck, Fresenius; regulatory affairs, pharmacovigilance). We go deeper into this career side in [working as a pharmacist in Germany: pharmacy, industry and salary](/en/blog/working-as-a-pharmacist-in-germany-pharmacy-industry-and-salary-en).

One important distinction: **if you are already a pharmacist**, the far more realistic route is not studying from scratch but getting your degree recognised in Germany (Approbation) — we cover this in [foreign pharmacist in Germany: Approbation and recognition](/en/blog/foreign-pharmacist-in-germany-approbation-and-recognition-en). For an honest reckoning of "is all this effort worth it?", see [is studying pharmacy in Germany worth it](/en/blog/is-studying-pharmacy-in-germany-worth-it-honest-reality-en).

---
*Based on rules in force as of 2026; NC, seats, language requirements and fees vary by state and university — confirm with the target university's International Office and uni-assist before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'studying-pharmacy-pharmazie-in-germany-as-a-foreigner',
                'title' => 'Almanya\'da Eczacılık (Pharmazie) Okumak: Uluslararası Öğrenci Rehberi (2026)',
                'excerpt' => 'Almanya\'da eczacılık okumak tıp/dişe göre daha ulaşılabilir: 4 yıl + Praktisches Jahr, 3 Staatsexamen, tamamen Almanca (İngilizce program YOK), NC rekabetçi ama daha esnek, uni-assist + Studienkolleg M-Kurs + C1 Almanca, ücret ve yaşam — dürüst rehber. Sanayi yolu uluslararası için güçlü.',
                'meta_title' => 'Almanya\'da Eczacılık (Pharmazie) Okumak — Rehber (2026)',
                'meta_description' => 'Almanya\'da eczacılık: 4 yıl + Praktisches Jahr, 3 Staatsexamen, tamamen Almanca, İngilizce yok, NC rekabetçi ama tıp/dişten ulaşılabilir, uni-assist + Studienkolleg, C1 — dürüst 2026 rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'studying-pharmacy-pharmazie-in-germany-as-a-foreigner-de',
                'title' => 'Pharmazie in Deutschland studieren: Leitfaden für internationale Studierende (2026)',
                'excerpt' => 'Pharmazie in Deutschland ist zugänglicher als Medizin/Zahnmedizin: 4 Jahre + Praktisches Jahr, 3 Staatsexamen, komplett auf Deutsch (KEIN Englisch), NC kompetitiv aber flexibler, uni-assist + Studienkolleg M-Kurs + C1, Gebühren und Lebenshaltung — ehrlicher Leitfaden. Der Industrieweg ist für Internationale stark.',
                'meta_title' => 'Pharmazie in Deutschland studieren — Leitfaden (2026)',
                'meta_description' => 'Pharmazie in Deutschland: 4 Jahre + Praktisches Jahr, 3 Staatsexamen, komplett auf Deutsch, kein Englisch, NC kompetitiv aber zugänglicher als Medizin, uni-assist + Studienkolleg, C1 — ehrlicher Leitfaden 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'studying-pharmacy-pharmazie-in-germany-as-a-foreigner-en',
                'title' => 'Studying Pharmacy (Pharmazie) in Germany: A Guide for International Students (2026)',
                'excerpt' => 'Studying pharmacy in Germany is more accessible than medicine/dentistry: 4 years + Praktisches Jahr, 3 Staatsexamen, entirely in German (NO English programme), NC competitive but more flexible, uni-assist + Studienkolleg M-Kurs + C1, fees and living — an honest guide. The industry path is strong for internationals.',
                'meta_title' => 'Studying Pharmacy (Pharmazie) in Germany — Guide (2026)',
                'meta_description' => 'Pharmacy in Germany: 4 years + Praktisches Jahr, 3 Staatsexamen, entirely in German, no English, NC competitive but more accessible than medicine, uni-assist + Studienkolleg, C1 — an honest 2026 guide.',
                'body' => $enBody,
            ],
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
            'studying-pharmacy-pharmazie-in-germany-as-a-foreigner',
            'studying-pharmacy-pharmazie-in-germany-as-a-foreigner-de',
            'studying-pharmacy-pharmazie-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};

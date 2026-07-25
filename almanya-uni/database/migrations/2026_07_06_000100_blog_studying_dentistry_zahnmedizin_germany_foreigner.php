<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Diş Hekimliği (Zahnmedizin) okumak — uluslararası öğrenci rehberi (2026).
 * Doğrulandı: Zahnmedizin 5–5,5 yıl, tamamen Almanca, İngilizce program yok; NC ~1,0 aşırı rekabetçi;
 * yol = uni-assist + sık sık Studienkolleg (M-Kurs) + TMS + C1 Almanca. Sayılar yıl-hedge'li (2025/2026, doğrula).
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'e8f10000-1111-4b9d-9fc0-ee06ff0bbb01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almanya'da diş hekimliği okuyabilir miyim?" sorusunun kısa yanıtı: **teoride evet, pratikte Almanya'nın en zor yollarından biri.** Zahnmedizin (diş hekimliği) düzenlenmiş bir sağlık mesleğidir; sonunda **Staatsexamen** ve **Approbation als Zahnarzt** (diş hekimi lisansı) gelir. Bu yazı hayal kırıklığını önlemek için gerçekleri dürüstçe anlatır.

## 1. Zahnmedizin nasıl bir programdır? (5–5,5 yıl, Staatsexamen, İngilizce YOK)
Almanya'da diş hekimliği **yaklaşık 5–5,5 yıl (10–11 dönem)** sürer ve devlet sınavı **Staatsexamen** ile biter; ardından eyalet makamından **Approbation** alınır. En kritik gerçek şu: program **tamamen Almancadır** ve **İngilizce diş hekimliği programı YOKTUR.** Tıpta olduğu gibi Zahnmedizin de düzenlenmiş bir meslek olduğu için "İngilizce okuyup kurtulurum" seçeneği devlet üniversitelerinde bulunmaz.

Yapı kabaca üç aşamalıdır: **preklinik** (anatomi, fizyoloji, biyokimya + erken sınavlar — doğa bilimleri ön sınavı ve **Physikum** benzeri kademeler), **klinik dönem** (hastalık bilgisi, cerrahi, protez, ortodonti) ve baştan sona çok yoğun **pratik/laboratuvar kursları (phantom kurs, gerçek hasta tedavisi)**. Diş hekimliği yönetmeliği (Approbationsordnung/ZApprO) çerçevesinde ilerler. Tıptan farklı olarak diş hekimliğinde el becerisi ve laboratuvar çalışması çok daha erken ve yoğun başlar; bu yüzden haftalık ders + uygulama yükü Almanya'nın en ağır programlarından biridir.

## 2. NC gerçeği: ~1,0 ve aşırı rekabet
Diş hekimliği **Numerus Clausus (NC)** ile sınırlıdır ve NC **çok yüksektir** — Alman öğrenciler için Abitur ortalaması genellikle **~1,0–1,5** aralığındadır (1,0 en iyi nottur). Kontenjan azdır, başvuran çoktur; bu yüzden yer bulmak **aşırı zordur.** Bu bir "iyi çalışırsam olur" tablosu değil, neredeyse mükemmele yakın not gerektiren bir tablodur.

> **Gerçeklik kontrolü:** Diş hekimliği tıpla birlikte Almanya'nın en rekabetçi iki bölümündendir. Uluslararası öğrenci kontenjanı ayrı ve dardır. "Almanya'da devlet üniversitesinde ücretsiz diş hekimliği" cazip görünür ama yer almak istisnai derecede zordur.

## 3. Başvuru yolu: uni-assist + Studienkolleg (M-Kurs) + TMS
AB-dışı (örneğin Türk) bir aday için tipik yol:
1. **uni-assist** üzerinden başvuru ve diploma denkliği ön değerlendirmesi.
2. Lise diplomanız doğrudan üniversite girişi (direkte Hochschulzugangsberechtigung) saymıyorsa, önce **Studienkolleg (M-Kurs — tıp/biyoloji odaklı kur)** ve ardından **Feststellungsprüfung.** Studienkolleg bir dil kursu değildir; asıl amacı akademik ve konu bazlı hazırlıktır — ayrıntı için [Studienkolleg gerçekte nedir](/tr/blog/studienkolleg-is-not-a-language-school-what-it-really-is) yazısına bakın.
3. **TMS (Test für Medizinische Studiengänge)** — zorunlu olmasa da iyi bir TMS sonucu birçok fakültede seçim puanını ciddi biçimde yükseltir ve rekabette avantaj sağlar.

Başvuru takvimine dikkat: kış dönemi için son tarih genellikle **15 Temmuz**, yaz dönemi için **15 Ocak** civarındadır (üniversiteye göre değişir, mutlaka doğrulayın). Yerler kısmen not ortalamasına, kısmen üniversitenin kendi seçim yöntemine (Auswahlverfahren der Hochschulen) göre dağıtılır; bu yöntemde TMS, mesleki ön deneyim veya mülakat puana katkı yapabilir. Süreç ve kontenjan mantığı büyük ölçüde tıpla aynıdır; karşılaştırma için [Almanya'da yabancı olarak tıp okumak](/tr/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas) yazısı da faydalıdır.

## 4. Dil: C1 Almanca şart, tıbbi Almanca gerçeği
Devlet üniversitelerinde diş hekimliği tamamen Almanca olduğu için en az **C1 (DSH-2 / TestDaF)** dil belgesi gerekir; bazı fakülteler diş/tıp için **DSH-3** isteyebilir. Ama kâğıt üstündeki minimum yetmez: klinik dönemde **hastayla Almanca iletişim**, sözlü sınavlar ve tıbbi terminoloji gerçek akıcılık ister.

> "Anlıyorum ama zor konuşuyorum" diş hekimliği için yeterli değildir. Başlamadan önce paslanmış bir C1'i tazelemek en akıllıca yatırımdır.

## 5. Tepe diş fakülteleri ve kontenjan azlığı
Diş hekimliği yalnızca köklü tıp fakültesi olan üniversitelerde vardır (ör. Charité Berlin, LMU München, Heidelberg, Freiburg, Frankfurt, Göttingen ve benzerleri). Sayı sınırlıdır ve her birindeki **yıllık kontenjan çok azdır** — bir bölüme yüzlerce değil, çoğu zaman onlarca öğrenci alınır. Uluslararası kontenjan ise bunun küçük bir dilimidir. Prestij ve "hangi üniversite" tartışması için [Almanya'da üniversite prestiji ve sıralamalar nasıl işler](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) yazısına göz atabilirsiniz.

## 6. Ücret ve yaşam masrafı
Devlet üniversitelerinde diş hekimliği kural olarak **öğrenim ücreti almaz**; yalnızca dönemlik **Semesterbeitrag** ödenir. Baden-Württemberg eyaleti AB-dışı öğrencilerden dönemlik ücret alır. Özel diş fakültesi nadirdir ve pahalıdır. Aşağıdaki rakamlar **2025/2026 itibarıyla yaklaşıktır; başvurudan önce ilgili üniversiteden doğrulayın.**

| Kalem | Yaklaşık tutar (2025/2026, doğrula) |
|---|---|
| Devlet üniversitesi öğrenim ücreti | Yok (yalnızca Semesterbeitrag) |
| Semesterbeitrag | ~150–350 € / dönem |
| Baden-Württemberg (AB-dışı) | ~1.500 € / dönem |
| Yaşam masrafı (vize blokeli hesap) | ~11.900 € / yıl civarı, şehre göre değişir |
| Özel diş fakültesi | Nadir ve pahalı (fakülteye göre) |

Not: Vize için genellikle bir yıllık yaşam masrafını kanıtlayan **bloke hesap (Sperrkonto)** ve geçerli bir **sağlık sigortası** gerekir; ayrıca diş hekimliğinde enstrüman/malzeme ve kitap gibi ek maliyetler de bütçeye eklenir. Rakamları başvuru öncesi güncel resmi kaynaklardan doğrulayın.

## Sonuç & dürüst tavsiye
Dürüst olalım: **uluslararası bir öğrenci için Almanya'da diş hekimliği okuma yeri almak aşırı zordur** — NC ~1,0, kontenjan az, program tamamen Almanca ve uzun. İmkânsız değil ama gerçekçi bir plan gerektirir: erken başlayın, dili C1'in çok üstüne taşıyın, TMS'ye hazırlanın ve Studienkolleg ihtimalini baştan hesaba katın.

Önemli bir ayrım: **eğer zaten diş hekimiyseniz**, sıfırdan okumak yerine çok daha gerçekçi olan yol Almanya'da diplomanızın tanınmasıdır (Approbation) — bunu ayrıntısıyla [yurtdışı diş hekimi Almanya'da nasıl çalışır: Approbation ve tanınma](/tr/blog/foreign-dentist-in-germany-approbation-and-recognition) yazısında anlatıyoruz. Lisansı aldıktan sonraki kariyer, maaş ve muayenehane tarafı için [Almanya'da diş hekimi olarak çalışmak](/tr/blog/working-as-a-dentist-in-germany-salary-career-and-own-practice) yazısına; "tüm bu emeğe değer mi?" sorusu için [Almanya'da diş hekimliği okumaya değer mi](/tr/blog/is-studying-dentistry-in-germany-worth-it-honest-reality) yazısına bakın.

---
*2026 itibarıyla geçerli kurallara dayanır; NC, kontenjan, dil şartı ve ücretler eyalet ve üniversiteye göre değişir — başvurudan önce ilgili üniversitenin International Office'i ve uni-assist üzerinden doğrulayın.*
MD;

        $deBody = <<<'MD'
„Kann ich in Deutschland Zahnmedizin studieren?" Die kurze Antwort: **theoretisch ja, in der Praxis einer der härtesten Wege in Deutschland.** Zahnmedizin ist ein reglementierter Gesundheitsberuf; am Ende stehen das **Staatsexamen** und die **Approbation als Zahnarzt**. Dieser Beitrag nennt dir ehrlich die Fakten, damit du keine Enttäuschung erlebst.

## 1. Wie ist Zahnmedizin aufgebaut? (5–5,5 Jahre, Staatsexamen, KEIN Englisch)
Zahnmedizin dauert in Deutschland **etwa 5–5,5 Jahre (10–11 Semester)** und endet mit dem **Staatsexamen**; danach erhältst du von der Landesbehörde die **Approbation**. Die wichtigste Wahrheit: Das Studium ist **komplett auf Deutsch** und es gibt **KEIN englischsprachiges Zahnmedizin-Programm.** Wie bei der Humanmedizin ist Zahnmedizin ein reglementierter Beruf — die Option „ich studiere einfach auf Englisch" existiert an staatlichen Universitäten nicht.

Der Aufbau ist grob dreiteilig: **Vorklinik** (Anatomie, Physiologie, Biochemie + frühe Prüfungen — naturwissenschaftliche Vorprüfung und ein Physikum-ähnlicher Abschnitt), **klinischer Teil** (Krankheitslehre, Chirurgie, Prothetik, Kieferorthopädie) und durchgehend sehr intensive **praktische Kurse (Phantomkurs, echte Patientenbehandlung)**. Alles läuft im Rahmen der zahnärztlichen Approbationsordnung (ZApprO). Anders als in der Medizin beginnen Handwerk und Laborarbeit in der Zahnmedizin früher und intensiver; die wöchentliche Kurs- und Übungslast gehört damit zu den höchsten in Deutschland.

## 2. Die NC-Realität: ~1,0 und extreme Konkurrenz
Zahnmedizin ist durch einen **Numerus Clausus (NC)** beschränkt, und der NC ist **sehr hoch** — für deutsche Bewerber liegt der Abiturschnitt meist bei **~1,0–1,5** (1,0 ist die Bestnote). Es gibt wenige Plätze und viele Bewerber; einen Platz zu bekommen ist **extrem schwer.** Das ist kein „mit Fleiß schaffe ich das"-Szenario, sondern eines, das nahezu perfekte Noten verlangt.

> **Realitätscheck:** Zahnmedizin gehört zusammen mit Medizin zu den zwei umkämpftesten Fächern Deutschlands. Die Quote für internationale Studierende ist getrennt und eng. „Kostenlos Zahnmedizin an einer staatlichen Uni" klingt attraktiv, aber einen Platz zu bekommen ist außergewöhnlich schwer.

## 3. Der Bewerbungsweg: uni-assist + Studienkolleg (M-Kurs) + TMS
Für Bewerber aus Nicht-EU-Ländern (z. B. Türkei) sieht der typische Weg so aus:
1. Bewerbung und Vorprüfung der Zeugnisanerkennung über **uni-assist**.
2. Wenn dein Schulabschluss keine direkte Hochschulzugangsberechtigung ergibt, zuerst das **Studienkolleg (M-Kurs — medizinisch/biologisch orientiert)** und danach die **Feststellungsprüfung.** Das Studienkolleg ist kein Sprachkurs, sondern eine fachliche Vorbereitung — Details unter [Was das Studienkolleg wirklich ist](/de/blog/studienkolleg-is-not-a-language-school-what-it-really-is-de).
3. **TMS (Test für Medizinische Studiengänge)** — nicht überall Pflicht, aber ein gutes TMS-Ergebnis verbessert an vielen Fakultäten deine Auswahlpunktzahl deutlich und ist ein echter Wettbewerbsvorteil.

Achte auf die Bewerbungsfristen: fürs Wintersemester meist um den **15. Juli**, fürs Sommersemester um den **15. Januar** (variiert je nach Uni — bitte prüfen). Die Plätze werden teils nach Notenschnitt, teils über das Auswahlverfahren der Hochschulen vergeben; dort können TMS, einschlägige Vorerfahrung oder ein Auswahlgespräch Punkte bringen. Verfahren und Quotenlogik ähneln stark der Medizin; zum Vergleich hilft auch [Als Ausländer Medizin in Deutschland studieren](/de/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-de).

## 4. Sprache: C1 Pflicht, und die Realität des Fachdeutsch
Da Zahnmedizin an staatlichen Universitäten komplett auf Deutsch ist, brauchst du mindestens einen **C1-Nachweis (DSH-2 / TestDaF)**; manche Fakultäten verlangen für Zahn-/Humanmedizin **DSH-3.** Aber das Papierminimum reicht nicht: In der Klinik brauchst du **Deutsch am Patienten**, in mündlichen Prüfungen und in der Fachterminologie echte Flüssigkeit.

> „Ich verstehe, spreche aber schwer" genügt für Zahnmedizin nicht. Ein eingerostetes C1 vor dem Start aufzufrischen ist die klügste Investition.

## 5. Top-Fakultäten und knappe Plätze
Zahnmedizin gibt es nur an Universitäten mit etablierter medizinischer Fakultät (z. B. Charité Berlin, LMU München, Heidelberg, Freiburg, Frankfurt, Göttingen u. a.). Die Zahl ist begrenzt und die **jährlichen Plätze pro Standort sind sehr wenige** — oft nur Dutzende statt Hunderte. Die internationale Quote ist davon nur ein kleiner Anteil. Zur Frage „welche Uni" siehe [Wie Prestige und Rankings in Deutschland funktionieren](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de).

## 6. Gebühren und Lebenshaltung
An staatlichen Universitäten ist Zahnmedizin in der Regel **studiengebührenfrei**; du zahlst nur den **Semesterbeitrag.** Baden-Württemberg erhebt von Nicht-EU-Studierenden eine Semestergebühr. Private zahnmedizinische Fakultäten sind selten und teuer. Die folgenden Zahlen sind **Stand 2025/2026 ungefähr; vor der Bewerbung bei der jeweiligen Uni bestätigen.**

| Posten | Ungefährer Betrag (Stand 2025/2026, prüfen) |
|---|---|
| Studiengebühr staatliche Uni | Keine (nur Semesterbeitrag) |
| Semesterbeitrag | ~150–350 € / Semester |
| Baden-Württemberg (Nicht-EU) | ~1.500 € / Semester |
| Lebenshaltung (Sperrkonto fürs Visum) | ~11.900 € / Jahr, je nach Stadt |
| Private zahnmedizinische Fakultät | Selten und teuer (je nach Fakultät) |

Hinweis: Fürs Visum brauchst du in der Regel ein **Sperrkonto**, das die Lebenshaltung für ein Jahr nachweist, sowie eine gültige **Krankenversicherung**; in der Zahnmedizin kommen zusätzlich Kosten für Instrumente/Material und Bücher hinzu. Prüfe die Zahlen vor der Bewerbung an aktuellen offiziellen Quellen.

## Fazit & ehrlicher Rat
Sei ehrlich zu dir: **für internationale Studierende ist es extrem schwer, einen Studienplatz für Zahnmedizin in Deutschland zu bekommen** — NC ~1,0, wenige Plätze, komplett auf Deutsch und langes Studium. Nicht unmöglich, aber es braucht einen realistischen Plan: früh anfangen, die Sprache weit über C1 bringen, den TMS vorbereiten und das Studienkolleg von Anfang an einplanen.

Ein wichtiger Unterschied: **wenn du bereits Zahnarzt bist**, ist der weitaus realistischere Weg nicht das Studium von vorn, sondern die Anerkennung deines Abschlusses in Deutschland (Approbation) — das erklären wir ausführlich in [Ausländischer Zahnarzt in Deutschland: Approbation und Anerkennung](/de/blog/foreign-dentist-in-germany-approbation-and-recognition-de). Zu Karriere, Gehalt und eigener Praxis nach der Approbation siehe [Als Zahnarzt in Deutschland arbeiten](/de/blog/working-as-a-dentist-in-germany-salary-career-and-own-practice-de); zur Frage „lohnt sich der ganze Aufwand?" siehe [Lohnt sich ein Zahnmedizinstudium in Deutschland](/de/blog/is-studying-dentistry-in-germany-worth-it-honest-reality-de).

---
*Stand 2026; NC, Plätze, Sprachanforderung und Gebühren variieren je nach Bundesland und Universität — vor der Bewerbung beim International Office der Ziel-Uni und bei uni-assist bestätigen.*
MD;

        $enBody = <<<'MD'
"Can I study dentistry in Germany?" The short answer: **theoretically yes, in practice one of the hardest paths in Germany.** Dentistry (Zahnmedizin) is a regulated health profession; it ends with the **Staatsexamen** and the **Approbation als Zahnarzt** (dental licence). To spare you disappointment, here are the honest facts.

## 1. How is Zahnmedizin structured? (5–5.5 years, Staatsexamen, NO English)
Dentistry in Germany takes **about 5–5.5 years (10–11 semesters)** and ends with the **Staatsexamen**; you then receive the **Approbation** from the state authority. The most important truth: the programme is **entirely in German** and there is **NO English-taught dentistry programme.** Like human medicine, dentistry is a regulated profession — the "I'll just study in English" option does not exist at public universities.

The structure is roughly three-part: a **preclinical** part (anatomy, physiology, biochemistry plus early exams — a natural-sciences preliminary and a Physikum-style stage), the **clinical** phase (pathology, surgery, prosthetics, orthodontics), and throughout, very intensive **hands-on courses (phantom course, real patient treatment)**. Everything runs under the dental licensing regulation (ZApprO). Unlike medicine, manual and lab work in dentistry starts earlier and more intensively, making the weekly course-and-practice load one of the heaviest in Germany.

## 2. The NC reality: ~1.0 and extreme competition
Dentistry is capped by a **Numerus Clausus (NC)**, and the NC is **very high** — for German applicants the Abitur average is usually around **~1.0–1.5** (1.0 being the top grade). There are few seats and many applicants, so getting a place is **extremely hard.** This is not a "work hard and you'll make it" scenario; it demands near-perfect grades.

> **Reality check:** Dentistry, together with medicine, is one of Germany's two most competitive subjects. The quota for international students is separate and narrow. "Free dentistry at a public university" sounds attractive, but securing a seat is exceptionally difficult.

## 3. The application route: uni-assist + Studienkolleg (M-Kurs) + TMS
For a non-EU applicant (e.g. from Turkey), the typical route is:
1. Apply and get a preliminary credential check via **uni-assist**.
2. If your school diploma does not grant direct university entry, first the **Studienkolleg (M-Kurs — medicine/biology-oriented track)** and then the **Feststellungsprüfung.** The Studienkolleg is not a language course but subject-based preparation — details in [what the Studienkolleg really is](/en/blog/studienkolleg-is-not-a-language-school-what-it-really-is-en).
3. **TMS (Test für Medizinische Studiengänge)** — not mandatory everywhere, but a strong TMS result significantly boosts your selection score at many faculties and gives a real competitive edge.

Watch the application deadlines: for the winter semester usually around **15 July**, for the summer semester around **15 January** (it varies by university — please verify). Seats are allocated partly by grade average and partly through each university's own selection procedure (Auswahlverfahren der Hochschulen), where the TMS, relevant prior experience or an interview can add points. The procedure and quota logic closely mirror medicine; for comparison, see [studying medicine in Germany as a foreigner](/en/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-en).

## 4. Language: C1 required, and the reality of medical German
Because dentistry at public universities is entirely in German, you need at least a **C1 certificate (DSH-2 / TestDaF)**; some faculties require **DSH-3** for dentistry/medicine. But the paper minimum is not enough: the clinical phase requires **communicating with patients in German**, and oral exams plus medical terminology demand genuine fluency.

> "I understand but struggle to speak" is not enough for dentistry. Refreshing a rusty C1 before you start is the smartest investment.

## 5. Top faculties and scarce seats
Dentistry exists only at universities with an established medical faculty (e.g. Charité Berlin, LMU Munich, Heidelberg, Freiburg, Frankfurt, Göttingen and similar). The number is limited and the **annual seats per location are very few** — often dozens rather than hundreds. The international quota is only a small slice of that. On the "which university" question, see [how prestige and rankings work in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en).

## 6. Fees and living costs
At public universities dentistry is generally **tuition-free**; you only pay the **Semesterbeitrag.** Baden-Württemberg charges non-EU students a semester fee. Private dental faculties are rare and expensive. The figures below are **approximate as of 2025/2026; confirm with the relevant university before applying.**

| Item | Approx. amount (as of 2025/2026, verify) |
|---|---|
| Public university tuition | None (only Semesterbeitrag) |
| Semesterbeitrag | ~€150–350 / semester |
| Baden-Württemberg (non-EU) | ~€1,500 / semester |
| Living costs (blocked account for visa) | ~€11,900 / year, varies by city |
| Private dental faculty | Rare and expensive (depends on faculty) |

Note: for the visa you generally need a **blocked account (Sperrkonto)** proving one year of living costs plus valid **health insurance**; in dentistry, budget also for instruments/materials and books. Verify the figures against current official sources before applying.

## Bottom line & honest advice
Be honest with yourself: **for an international student, getting a dentistry seat in Germany is extremely hard** — NC ~1.0, few seats, entirely in German, and a long programme. Not impossible, but it needs a realistic plan: start early, push your language well beyond C1, prepare for the TMS, and factor in the Studienkolleg from the outset.

One important distinction: **if you are already a dentist**, the far more realistic route is not studying from scratch but getting your degree recognised in Germany (Approbation) — we cover this in detail in [foreign dentist in Germany: Approbation and recognition](/en/blog/foreign-dentist-in-germany-approbation-and-recognition-en). For career, salary and your own practice after the Approbation, see [working as a dentist in Germany](/en/blog/working-as-a-dentist-in-germany-salary-career-and-own-practice-en); for the "is all this effort worth it?" question, see [is studying dentistry in Germany worth it](/en/blog/is-studying-dentistry-in-germany-worth-it-honest-reality-en).

---
*Based on rules in force as of 2026; NC, seats, language requirements and fees vary by state and university — confirm with the target university's International Office and uni-assist before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'studying-dentistry-zahnmedizin-in-germany-as-a-foreigner',
                'title' => 'Almanya\'da Diş Hekimliği (Zahnmedizin) Okumak: Uluslararası Öğrenci Rehberi (2026)',
                'excerpt' => 'Almanya\'da diş hekimliği okumak mümkün ama en zor yollardan biri: 5–5,5 yıl tamamen Almanca (İngilizce program YOK), NC ~1,0 aşırı rekabet, uni-assist + Studienkolleg M-Kurs + TMS, C1 Almanca şartı, dar kontenjan, ücret ve yaşam — dürüst rehber. Zaten diş hekimiysen tanınma yolu daha gerçekçi.',
                'meta_title' => 'Almanya\'da Diş Hekimliği (Zahnmedizin) Okumak — Rehber (2026)',
                'meta_description' => 'Almanya\'da diş hekimliği: 5–5,5 yıl tamamen Almanca, İngilizce yok, NC ~1,0, uni-assist + Studienkolleg + TMS, C1 şartı, ücret — yabancılar için dürüst 2026 rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'studying-dentistry-zahnmedizin-in-germany-as-a-foreigner-de',
                'title' => 'Zahnmedizin in Deutschland studieren: Leitfaden für internationale Studierende (2026)',
                'excerpt' => 'Zahnmedizin in Deutschland ist möglich, aber einer der härtesten Wege: 5–5,5 Jahre komplett auf Deutsch (KEIN Englisch), NC ~1,0, uni-assist + Studienkolleg M-Kurs + TMS, C1-Pflicht, knappe Plätze, Gebühren und Lebenshaltung — ehrlicher Leitfaden. Bist du schon Zahnarzt, ist die Anerkennung realistischer.',
                'meta_title' => 'Zahnmedizin in Deutschland studieren — Leitfaden (2026)',
                'meta_description' => 'Zahnmedizin in Deutschland: 5–5,5 Jahre auf Deutsch, kein Englisch, NC ~1,0, uni-assist + Studienkolleg + TMS, C1-Pflicht, Gebühren — ehrlicher Leitfaden für Ausländer 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'studying-dentistry-zahnmedizin-in-germany-as-a-foreigner-en',
                'title' => 'Studying Dentistry (Zahnmedizin) in Germany: A Guide for International Students (2026)',
                'excerpt' => 'Studying dentistry in Germany is possible but one of the hardest paths: 5–5.5 years entirely in German (NO English programme), NC ~1.0 extreme competition, uni-assist + Studienkolleg M-Kurs + TMS, C1 required, scarce seats, fees and living — an honest guide. If you are already a dentist, recognition is more realistic.',
                'meta_title' => 'Studying Dentistry (Zahnmedizin) in Germany — Guide (2026)',
                'meta_description' => 'Dentistry in Germany: 5–5.5 years in German, no English, NC ~1.0, uni-assist + Studienkolleg + TMS, C1 required, fees — an honest 2026 guide for foreigners.',
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
            'studying-dentistry-zahnmedizin-in-germany-as-a-foreigner',
            'studying-dentistry-zahnmedizin-in-germany-as-a-foreigner-de',
            'studying-dentistry-zahnmedizin-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};

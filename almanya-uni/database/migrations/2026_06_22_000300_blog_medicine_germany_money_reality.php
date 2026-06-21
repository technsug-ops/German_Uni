<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da AB-dışı tıp öğrencisi için PARA gerçeği (2026).
 *
 * Doğrulandı (Auswärtiges Amt / Sperrkonto, BAföG-Höchstsatz, Mindestlohn 2026,
 * AB-dışı çalışma izni 140/280 gün, Werkstudent, Ausbildung-ücretleri, 2026):
 *  - "Bedava" yarı-doğru: kamu üni TUITION harçsız (Semesterbeitrag ~200-350€/dönem),
 *    ama yaşam pahalı + masrafları kapatan burs neredeyse yok.
 *  - Sperrkonto 2026: AB-dışı 11.904€/yıl (992€/ay), vize öncesi; ~992€/ay serbest.
 *    Yıllık güncellenir (BAföG-Höchstsatz'a bağlı). AB/AEA/İsviçre gerekmez.
 *  - Çalışma: AB-dışı 140 tam / 280 yarım gün/yıl (~20h/hafta dönem içi).
 *    Asgari ücret 2026 ~13,90€/saat → kabaca ~1.000€/ay tavan; tıp yoğunluğu yüzden gerçekçi değil.
 *  - BAföG: taze AB-dışı öğrenciye yok (oturum/statü/yıl şartı). Study visa'da: hayır.
 *  - Werkstudent ~1.000-1.200€/ay ama iyi Almanca + tıpla birleştirmesi zor.
 *  - Bazı hastaneler 1. Staatsexamen sonrası küçük aylık ödenek (sonra çalışma taahhüdü).
 *  - Akıllı-para alternatifi: hemşirelik/sağlık Ausbildung ilk aydan ÜCRETLİ (~1.100-1.300€/ay brüt).
 *
 * Yazar: Halil Yaprakli. Kategori: finans → almanyada-egitim → ilk. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e72';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'finans')->value('id')
            ?? DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almanya'da tıp bedava!" — internette en çok dolaşan ve en yanıltıcı cümlelerden biri. Doğrusu şu: kamu üniversitesinde **öğrenim harcı yok**, ama bu "okuması ucuz" demek değil. Yabancı (AB-dışı) bir öğrenci için tıp okumak finansal olarak **en zorlu yollardan biri.** "Bedava eğitim" miti ile "yarı zamanlı çalışıp finanse ederim" miti — ikisini de tek tek çürütelim.

## "Bedava" sözünün yarısı doğru
Evet, kamu üniversitesinde tıbın **tuition'ı harçsız**; ödediğin tek şey dönemlik **Semesterbeitrag (~€200–350)**. Ama yaşam masrafları (kira, sigorta, yemek) yüksektir ve bunları karşılayan **burs neredeyse yoktur.** Yani "bedava eğitim" ≠ "ucuza yaşam".

## Sperrkonto: vizeden önce göstermen gereken para
AB-dışı öğrenci, vize için bir **bloke hesap (Sperrkonto)** açıp 2026 itibarıyla **€11.904/yıl = €992/ay** yatırmak zorunda. Para size aylık ~€992 olarak serbest bırakılır ve rakam her yıl **BAföG-Höchstsatz'a** bağlı güncellenir. AB/AEA/İsviçre öğrencileri bunu yapmak zorunda değildir. (Detay: [Sperrkonto nedir](/tr/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and).)

## "Çalışarak okurum" — tıpta gerçekçi değil
AB-dışı öğrenci yılda **140 tam / 280 yarım gün** (dönem içi ~haftada 20 saat) çalışabilir. Asgari ücret 2026'da **~€13,90/saat** → en iyi ihtimalle kabaca **~€1.000/ay**. Ama tıp **zaman canavarı**: ağır sınavlar, zorunlu tam zamanlı Famulatur/staj, anatomi-klinik yükü. Çoğu tıp öğrencisi anlamlı bir işi sürdüremez. **Buna bel bağlama.** [Werkstudent](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market) işleri daha iyi öder (~€1.000–1.200/ay) ama iyi Almanca ister ve tıbın temposuyla birleştirmesi çok zordur.

## BAföG: taze AB-dışı öğrenciye yok
Almanların devlet öğrenci desteği **BAföG**, study visa'yla gelen taze AB-dışı öğrenciye **açık değildir** — kalıcı oturum, belirli statü ya da Almanya'da yıllar gerektirir. Yani plana BAföG'ü koyma.

## Para kaynakları: gerçekçi tablo

| Kaynak | Gerçekçi aylık | Uyarı |
|---|---|---|
| Sperrkonto | ~€992 (sabit) | Senin paran; "gelir" değil, kendi birikimin |
| Yarı zamanlı iş | ~€1.000 (tavan) | Tıpta vakit yok; süreklilik zor |
| Werkstudent | ~€1.000–1.200 | İyi Almanca şart; tıpla birleştirmesi zor |
| BAföG | €0 | Taze AB-dışı öğrenciye yok |
| Hemşirelik Ausbildung | ~€1.100–1.300 (brüt) | İlk aydan ÜCRETLİ — ama tıp değil, farklı yol |

## Akıllı-para alternatifi: ücretli Ausbildung
Para açısından daha güvenli bir sağlık yolu: **hemşirelik/sağlık Ausbildung ilk aydan ücretlidir** — eğitim süresince **brüt ~€1.100–1.300/ay** alırsın. Bu tıbın kısa yolu değildir, **ayrı bir meslek yoludur**; ama sağlam bir gelirle Almanya'ya yerleşip ileride üniversiteye/tıbba yönelmek isteyenler için mantıklı bir başlangıç olabilir. (Geçiş: [üniversiteden Ausbildung'a geçiş](/tr/blog/switching-from-study-to-ausbildung-germany-residence-permit).) Ayrıca bazı hastaneler **1. Staatsexamen sonrası** küçük aylık bir ödenek verip karşılığında sonra orada çalışma taahhüdü ister.

## Dürüst sonuç
Almanya'da tıp "bedava" değil, sadece **harçsız.** AB-dışı bir öğrenci için gerçek tablo: vize için ~€12 bin Sperrkonto, neredeyse burs yok, çalışarak finanse etme miti, BAföG yok. Bütçeni **Sperrkonto + aile desteği** üzerine kur; işi olası bir bonus say, ana plan yapma. Sağlam gelirli bir alternatif arıyorsan **ücretli Ausbildung** masada. (Tıbın kendine özgü zorlukları: [yabancı olarak tıp okumak](/tr/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas).)

---
*Rakamlar 2026 itibarıyladır (Sperrkonto €992/ay, asgari ücret ~€13,90/saat); Sperrkonto tutarı ve çalışma kuralları her yıl güncellenir — başvurudan önce Auswärtiges Amt / üniversite International Office'ten teyit et.*
MD;

        $deBody = <<<'MD'
„Medizin in Deutschland ist kostenlos!" — einer der meistverbreiteten und irreführendsten Sätze im Netz. Wahr ist: An staatlichen Unis gibt es **keine Studiengebühren**, aber das heißt nicht „billig zu studieren". Für internationale (Nicht-EU-)Studierende ist Medizin finanziell **einer der härtesten Wege.** Den „Kostenlos"-Mythos und den „Ich finanziere es mit einem Nebenjob"-Mythos räumen wir hier auf.

## „Kostenlos" ist halb wahr
Ja, an staatlichen Unis ist die **Tuition gebührenfrei**; du zahlst nur den **Semesterbeitrag (~€200–350)** pro Semester. Aber die Lebenshaltungskosten (Miete, Versicherung, Essen) sind hoch, und **es gibt fast keine Stipendien**, die sie decken. „Kostenloses Studium" ≠ „günstig zu leben".

## Sperrkonto: das Geld, das du vor dem Visum nachweisen musst
Nicht-EU-Studierende müssen für das Visum ein **Sperrkonto** eröffnen und 2026 **€11.904/Jahr = €992/Monat** nachweisen. Das Geld wird dir monatlich mit ~€992 freigegeben, und der Betrag wird jährlich an den **BAföG-Höchstsatz** angepasst. EU/EWR/Schweizer Studierende brauchen das nicht. (Details: [Was ist das Sperrkonto](/de/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-de).)

## „Ich arbeite mich durch" — in der Medizin unrealistisch
Nicht-EU-Studierende dürfen **140 ganze / 280 halbe Tage pro Jahr** (im Semester ~20 Std./Woche) arbeiten. Mindestlohn 2026 **~€13,90/Std.** → bestenfalls grob **~€1.000/Monat**. Aber Medizin ist ein **Zeitfresser**: harte Prüfungen, verpflichtende Vollzeit-Famulaturen, die klinische Last. Die meisten Medizinstudierenden können keinen ernsthaften Job halten. **Verlass dich nicht darauf.** Werkstudentenjobs zahlen besser (~€1.000–1.200/Monat), verlangen aber gutes Deutsch und lassen sich mit dem Medizin-Pensum kaum vereinbaren.

## BAföG: nichts für frische Nicht-EU-Studierende
Die staatliche Studienförderung **BAföG** steht frischen Nicht-EU-Studierenden mit Studienvisum **nicht offen** — sie setzt eine Niederlassungserlaubnis, einen bestimmten Status oder Jahre in Deutschland voraus. Plane also nicht mit BAföG.

## Finanzierungsquellen: der realistische Überblick

| Quelle | Realistisch pro Monat | Vorbehalt |
|---|---|---|
| Sperrkonto | ~€992 (fix) | Dein eigenes Geld; kein „Einkommen" |
| Nebenjob | ~€1.000 (Obergrenze) | Keine Zeit in der Medizin; schwer dauerhaft |
| Werkstudent | ~€1.000–1.200 | Gutes Deutsch nötig; schwer kombinierbar |
| BAföG | €0 | Nicht für frische Nicht-EU-Studierende |
| Pflege-Ausbildung | ~€1.100–1.300 (brutto) | Ab dem 1. Monat BEZAHLT — aber kein Medizinstudium |

## Die clevere Geld-Alternative: bezahlte Ausbildung
Ein finanziell sicherer Weg im Gesundheitsbereich: Die **Pflege-/Gesundheits-Ausbildung wird ab dem ersten Monat bezahlt** — während der Ausbildung **brutto ~€1.100–1.300/Monat**. Das ist keine Abkürzung ins Medizinstudium, sondern ein **eigener Berufsweg**; aber für alle, die mit solidem Einkommen in Deutschland Fuß fassen und später Richtung Studium gehen wollen, ein sinnvoller Start. (Wechsel: [vom Studium zur Ausbildung](/de/blog/switching-from-study-to-ausbildung-germany-residence-permit-de).) Manche Kliniken zahlen zudem **nach dem 1. Staatsexamen** ein kleines monatliches Stipendium gegen eine spätere Arbeitsverpflichtung.

## Ehrliches Fazit
Medizin in Deutschland ist nicht „kostenlos", nur **gebührenfrei.** Für Nicht-EU-Studierende sieht es real so aus: ~€12.000 Sperrkonto fürs Visum, fast keine Stipendien, der Finanzierungs-durch-Job-Mythos, kein BAföG. Baue dein Budget auf **Sperrkonto + Familienunterstützung**; einen Job betrachte als möglichen Bonus, nicht als Plan. Suchst du eine Alternative mit solidem Einkommen, steht die **bezahlte Ausbildung** zur Wahl. (Die eigenen Hürden der Medizin: [Medizin als Ausländer studieren](/de/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-de).)

---
*Zahlen Stand 2026 (Sperrkonto €992/Monat, Mindestlohn ~€13,90/Std.); Sperrkonto-Betrag und Arbeitsregeln werden jährlich aktualisiert — vor der Bewerbung beim Auswärtigen Amt / dem International Office bestätigen.*
MD;

        $enBody = <<<'MD'
"Medicine in Germany is free!" — one of the most repeated and most misleading lines online. The truth: at public universities there is **no tuition**, but that does not mean "cheap to study". For international (non-EU) students, medicine is financially **one of the toughest paths.** Let's bust both the "free education" myth and the "I'll fund it with a part-time job" myth.

## "Free" is half true
Yes, at public universities **tuition is free**; you only pay the **Semesterbeitrag (~€200–350)** per semester. But living costs (rent, insurance, food) are high, and there are **almost no scholarships** that cover them. "Free study" ≠ "cheap to live".

## Sperrkonto: the money you must show before the visa
Non-EU students must open a **blocked account (Sperrkonto)** and, as of 2026, prove **€11,904/year = €992/month** for the visa. The money is released to you at ~€992/month, and the figure is updated yearly in line with the **BAföG maximum rate**. EU/EEA/Swiss students do not need it. (Details: [What is the Sperrkonto](/en/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-en).)

## "I'll work my way through" — unrealistic for medicine
Non-EU students may work **140 full / 280 half days per year** (~20 hrs/week during the semester). Minimum wage in 2026 is **~€13.90/hour** → at best roughly **~€1,000/month**. But medicine is a **time monster**: heavy exams, mandatory full-time Famulatur/internships, the clinical workload. Most med students simply cannot hold a meaningful job. **Don't rely on it.** Werkstudent jobs pay better (~€1,000–1,200/month) but require good German and are hard to combine with medicine's workload.

## BAföG: not for fresh non-EU students
Germany's state student aid, **BAföG**, is **not open** to fresh non-EU students on a study visa — it requires permanent residence, a specific status, or years in Germany. So don't build BAföG into your plan.

## Funding sources: the realistic picture

| Source | Realistic monthly | Caveat |
|---|---|---|
| Sperrkonto | ~€992 (fixed) | Your own money; not "income" |
| Part-time job | ~€1,000 (ceiling) | No time in medicine; hard to sustain |
| Werkstudent | ~€1,000–1,200 | Needs good German; hard to combine |
| BAföG | €0 | Not for fresh non-EU students |
| Nursing Ausbildung | ~€1,100–1,300 (gross) | PAID from month one — but not medicine |

## The smarter-money alternative: a paid Ausbildung
A financially safer healthcare route: a **nursing/healthcare Ausbildung is paid from the first month** — roughly **€1,100–1,300/month gross** while you train. This is not a shortcut into medicine but a **separate career path**; still, for anyone who wants to settle in Germany on a solid income and move toward studying later, it can be a sensible start. (Switching: [from study to Ausbildung](/en/blog/switching-from-study-to-ausbildung-germany-residence-permit-en).) Some hospitals also pay a small monthly stipend **after the 1st Staatsexamen** in exchange for committing to work there later.

## Honest bottom line
Medicine in Germany is not "free", only **tuition-free.** For non-EU students the real picture is: ~€12,000 Sperrkonto for the visa, almost no scholarships, the fund-it-by-working myth, and no BAföG. Build your budget on **Sperrkonto + family support**; treat a job as a possible bonus, not the plan. If you want an alternative with solid income, a **paid Ausbildung** is on the table. (Medicine's own hurdles: [studying medicine as a foreigner](/en/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-en).)

---
*Figures as of 2026 (Sperrkonto €992/month, minimum wage ~€13.90/hour); the Sperrkonto amount and work rules are updated yearly — confirm with the Auswärtiges Amt / the International Office before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'studying-medicine-germany-non-eu-money-reality',
                'title' => 'Almanya\'da Tıp Okumanın Para Gerçeği (AB-dışı, 2026): Sperrkonto, İş ve "Bedava" Miti',
                'excerpt' => 'Almanya\'da tıp "bedava" değil, sadece harçsız. AB-dışı öğrenci için para gerçeği: vize için €992/ay Sperrkonto, neredeyse burs yok, "çalışarak finanse ederim" miti (tıpta vakit yok), BAföG yok ve ücretli hemşirelik Ausbildung alternatifi — gerçekçi tablo ve dürüst tavsiye.',
                'meta_title' => 'Almanya\'da Tıp Okumanın Para Gerçeği — Sperrkonto & "Bedava" Miti (2026)',
                'meta_description' => 'AB-dışı tıp öğrencisi için para gerçeği: €992/ay Sperrkonto, çalışma tavanı ~€1.000, BAföG yok, burs yok ve ücretli Ausbildung alternatifi — dürüst 2026 rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'studying-medicine-germany-non-eu-money-reality-de',
                'title' => 'Die Geld-Realität des Medizinstudiums in Deutschland (Nicht-EU, 2026): Sperrkonto, Jobs & der „Kostenlos"-Mythos',
                'excerpt' => 'Medizin in Deutschland ist nicht „kostenlos", nur gebührenfrei. Die Geld-Realität für Nicht-EU-Studierende: €992/Monat Sperrkonto fürs Visum, fast keine Stipendien, der „Ich finanziere es mit einem Job"-Mythos (keine Zeit in der Medizin), kein BAföG und die bezahlte Pflege-Ausbildung als Alternative.',
                'meta_title' => 'Geld-Realität Medizinstudium Deutschland — Sperrkonto & „Kostenlos"-Mythos (2026)',
                'meta_description' => 'Geld-Realität für Nicht-EU-Medizinstudierende: €992/Monat Sperrkonto, Job-Obergrenze ~€1.000, kein BAföG, kaum Stipendien und die bezahlte Ausbildung als Alternative — ehrlicher Leitfaden 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'studying-medicine-germany-non-eu-money-reality-en',
                'title' => 'The Money Reality of Studying Medicine in Germany (Non-EU, 2026): Sperrkonto, Jobs & the \'Free\' Myth',
                'excerpt' => 'Medicine in Germany is not "free", only tuition-free. The money reality for non-EU students: €992/month Sperrkonto for the visa, almost no scholarships, the "I\'ll fund it with a job" myth (no time in medicine), no BAföG, and the paid nursing Ausbildung as an alternative — a realistic picture and honest advice.',
                'meta_title' => 'Money Reality of Medicine in Germany — Sperrkonto & the \'Free\' Myth (2026)',
                'meta_description' => 'Money reality for non-EU medicine students: €992/month Sperrkonto, job ceiling ~€1,000, no BAföG, almost no scholarships, and a paid Ausbildung alternative — an honest 2026 guide.',
                'body' => $enBody,
            ],
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
            if ($existing) {
                $existing->update($payload);
            } else {
                Post::create($payload + ['slug' => $v['slug']]);
            }
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'studying-medicine-germany-non-eu-money-reality',
            'studying-medicine-germany-non-eu-money-reality-de',
            'studying-medicine-germany-non-eu-money-reality-en',
        ])->delete();
    }
};

<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Studienkolleg bir dil okulu DEĞİLDİR — gerçekte ne işe yarar (2026).
 *
 * Doğrulandı (KMK/anabin, Studienkolleg kuralları, Feststellungsprüfung, 2026):
 *  - Studienkolleg = 1 yıllık (2 dönem) HAZIRLIK/DENKLİK yılı; Abitur'a (Hochschulzugangsberechtigung)
 *    köprü kurar. Feststellungsprüfung (FSP) ile biter — bir DİL KURSU DEĞİL.
 *  - Almanca'yı orada SIFIRDAN öğrenmezsin: çoğu kamu Studienkollegs B2 (bazı özel B1) ister;
 *    Aufnahmeprüfung B1+/B2 ölçer. Almanca'yı önceden normal bir Sprachschule'de öğrenirsin.
 *  - Kimin ihtiyacı var: SADECE diploman Abitur'a "yakın ama eşit değil" ise. Eşitse → doğrudan
 *    başvur (Studienkolleg yok). Çok düşükse → Studienkolleg seçenek değil. Durumu anabin'de kontrol et.
 *  - Kurlar: M (tıp/diş/biyo/eczacılık/vet), T (teknik/müh/mat/fen), W (ekonomi/sosyal),
 *    G (insan bilimleri/Almanca), S (diller).
 *  - Ders B2/C1 düzeyinde Almanca verilir → zayıf Almanca = kalırsın; "Almanca öğrenmek için git" tersine.
 *  - Tıp için: top notla (1,x) FSP'den sonra bile NC çok rekabetçi; ekstra puan FSJ/deneyimden.
 *
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 * İç-link: anabin + yabancı olarak tıp (her ikisi de tr/de/en mevcut) → her dilde locale-doğru.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e73';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'basvuru')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Yabancı öğrencilerin Almanya hakkındaki **1 numaralı yanlış inancı** şudur: "Almancam zayıf, o yüzden önce Studienkolleg'e gidip orada Almanca öğrenirim." Bu kafa karışıklığı süreci en baştan yanlış kurar. Net olalım: **Studienkolleg bir dil okulu değildir.** Almancayı orada sıfırdan öğrenmezsin — tam tersine, oraya girebilmek için zaten Almanca bilmen gerekir.

## Studienkolleg gerçekte nedir?
Studienkolleg **1 yıllık (2 dönem) bir hazırlık/denklik kursudur.** İşi seni Alman **Abitur'a (üniversite giriş yeterliliği / Hochschulzugangsberechtigung)** köprülemektir — yani akademik seviyeni bir Alman lise mezununa **eşit** hale getirir. Sonunda bir dil sertifikası değil, **Feststellungsprüfung (FSP)** denen bir **denklik/seviye tespit sınavı** vardır. Yani bir Almanca kursu değil, bir **akademik denklik yılıdır.**

## Almancayı zaten biliyor olmalısın
İşin püf noktası: Studienkolleg'e **Almanca bilerek gelirsin.** Çoğu **kamu Studienkollegs B2** ister (bazı **özel** olanlar **B1** kabul eder) ve giriş sınavı (**Aufnahmeprüfung**) **B1+/B2** seviyeni ölçer. Almancayı öğreneceğin yer normal bir **dil okuludur (Sprachschule)** — Studienkolleg değil. Üstelik dersler **B2/C1 düzeyinde Almanca** işlenir; zayıf Almancayla derslerden kalırsın. İşte tam da bu yüzden "Almanca öğrenmek için Studienkolleg'e git" mantığı baştan terstir.

## Kimin ihtiyacı var, kim gidemez?
Studienkolleg **herkes için değildir.** Belirleyici tek şey lise diplomanın Alman Abitur'a göre durumudur:

- Diploman Abitur'a **"yakın ama eşit değil"** ise → Studienkolleg **senin için**.
- Diploman **zaten eşdeğer** ise → Studienkolleg'i atlar, **doğrudan üniversiteye başvurursun.**
- Diploman Abitur'un **çok altındaysa** → Studienkolleg bir **seçenek değildir.**

Durumunu **anabin** veritabanında (KMK) kontrol et — "isteyince gidilen" bir yer değildir. (Detay: [Anabin nedir](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma).)

## Kurlar (Kurse) — hedef alanına göre
Studienkolleg, okumak istediğin bölüme göre branşlara ayrılır:

| Kur | Hedef alan |
| --- | --- |
| **M-Kurs** | Tıp, diş hekimliği, biyoloji, eczacılık, veterinerlik |
| **T-Kurs** | Teknik / mühendislik, matematik, fen bilimleri |
| **W-Kurs** | Ekonomi, sosyal bilimler |
| **G-Kurs** | İnsan bilimleri, Almanca (Germanistik) |
| **S-Kurs** | Diller |

## Mit vs Gerçek
| Mit | Gerçek |
| --- | --- |
| "Almanca öğrenmek için gidilen bir kurs." | Hayır — Abitur denkliği yılı; **girmek için bile B1/B2** lazım. |
| "Almancamı geliştirmek için herkes kaydolabilir." | Sadece diploman **yakın-ama-eşit-değil** ise; Almancayı önce **Sprachschule'de** öğren. |
| "Sonunda dil sertifikası alırsın." | Sonunda **Feststellungsprüfung (FSP)** verirsin — akademik sınav. |

## Özet ve sonraki adım
Studienkolleg bir Almanca kursu değil, **Abitur'a köprü kuran akademik denklik yılıdır** ve FSP ile biter. Almancayı önce bir Sprachschule'de B2'ye getir, durumunu anabin'de kontrol et, sonra doğru kura (M/T/W/G/S) başvur. **Tıp** hedefliyorsan dikkat: FSP'yi en üst notla (1,x) bitirsen bile NC çok rekabetçidir; ekstra puan **FSJ/deneyimden** gelir.

İlgili: [Anabin & denklik](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma) · [Yabancı olarak tıp okumak](/tr/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas).

---
*2026 itibarıyla yürürlükteki kurallar temel alınmıştır; diploma denkliği, kabul şartları ve kur düzenlemeleri eyalete/Studienkolleg'e göre değişir — anabin'i kontrol et ve hedef Studienkolleg'den teyit al.*
MD;

        $deBody = <<<'MD'
Der **größte Irrtum** internationaler Studierender über Deutschland lautet: „Mein Deutsch ist schwach, also gehe ich erst ins Studienkolleg und lerne dort Deutsch." Dieser Denkfehler verdirbt den ganzen Plan von Anfang an. Klartext: **Das Studienkolleg ist keine Sprachschule.** Du lernst dort kein Deutsch von null — im Gegenteil, du musst bereits Deutsch können, um überhaupt hineinzukommen.

## Was das Studienkolleg wirklich ist
Das Studienkolleg ist ein **einjähriger Vorbereitungskurs (2 Semester).** Seine Aufgabe ist es, dich zur deutschen **Abitur-Ebene (Hochschulzugangsberechtigung)** zu bringen — also dein akademisches Niveau dem eines deutschen Abiturienten **gleichzustellen.** Am Ende steht kein Sprachzertifikat, sondern die **Feststellungsprüfung (FSP)** — eine Niveau-/Eignungsprüfung. Es ist also kein Deutschkurs, sondern ein **akademisches Angleichungsjahr.**

## Du musst bereits Deutsch können
Der Kern: Du kommst **mit Deutsch** ins Studienkolleg. Die meisten **staatlichen Studienkollegs verlangen B2** (manche **privaten** akzeptieren **B1**), und die **Aufnahmeprüfung** testet **B1+/B2.** Deutsch lernst du vorher an einer normalen **Sprachschule** — nicht im Studienkolleg. Zudem läuft der Unterricht **auf Deutsch (B2/C1-Niveau)**; mit schwachem Deutsch fällst du durch. Genau deshalb ist „ins Studienkolleg gehen, um Deutsch zu lernen" von Grund auf falsch herum.

## Wer braucht es, wer darf nicht?
Das Studienkolleg ist **nicht für jeden.** Entscheidend ist allein der Stand deines Schulabschlusses im Vergleich zum Abitur:

- Dein Abschluss ist **„nah, aber nicht gleichwertig"** → das Studienkolleg ist **für dich**.
- Dein Abschluss ist **bereits gleichwertig** → du überspringst das Studienkolleg und **bewirbst dich direkt.**
- Dein Abschluss liegt **deutlich darunter** → das Studienkolleg ist **keine Option.**

Prüfe deinen Status in der **anabin**-Datenbank (KMK) — man „wählt" das nicht einfach. (Mehr: [Was ist Anabin](/de/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-de).)

## Die Kurse — nach Zielfach
Das Studienkolleg ist nach dem angestrebten Studienfach in Kurse gegliedert:

| Kurs | Zielfach |
| --- | --- |
| **M-Kurs** | Medizin, Zahnmedizin, Biologie, Pharmazie, Veterinär |
| **T-Kurs** | Technik / Ingenieurwesen, Mathematik, Naturwissenschaften |
| **W-Kurs** | Wirtschaft, Sozialwissenschaften |
| **G-Kurs** | Geisteswissenschaften, Germanistik |
| **S-Kurs** | Sprachen |

## Mythos vs. Realität
| Mythos | Realität |
| --- | --- |
| „Ein Kurs, um Deutsch zu lernen." | Nein — ein Abitur-Angleichungsjahr; **schon zum Einstieg B1/B2** nötig. |
| „Jeder kann sich einschreiben, um Deutsch zu verbessern." | Nur wenn dein Abschluss **nah-aber-nicht-gleichwertig** ist; Deutsch zuerst an der **Sprachschule.** |
| „Am Ende gibt es ein Sprachzertifikat." | Am Ende steht die **Feststellungsprüfung (FSP)** — eine akademische Prüfung. |

## Fazit & nächster Schritt
Das Studienkolleg ist kein Deutschkurs, sondern ein **akademisches Angleichungsjahr zur Abitur-Ebene**, das mit der FSP endet. Bring dein Deutsch zuerst an einer Sprachschule auf B2, prüfe deinen Status in anabin und bewirb dich dann für den richtigen Kurs (M/T/W/G/S). Zielst du auf **Medizin**: Achtung — selbst mit Bestnote (1,x) in der FSP bleibt der NC hart umkämpft; Extrapunkte kommen aus **FSJ/Erfahrung.**

Verwandt: [Anabin & Anerkennung](/de/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-de) · [Als Ausländer Medizin studieren](/de/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-de).

---
*Stand 2026; Anerkennung, Zugangsvoraussetzungen und Kursregelungen variieren je nach Bundesland/Studienkolleg — prüfe anabin und bestätige beim Ziel-Studienkolleg.*
MD;

        $enBody = <<<'MD'
The **number-one misconception** international students have about Germany is this: "My German is weak, so I'll go to a Studienkolleg first and learn German there." That confusion sets the whole plan up wrong from the start. Let's be clear: **a Studienkolleg is not a language school.** You don't learn German there from scratch — on the contrary, you must already arrive with German just to get in.

## What a Studienkolleg really is
A Studienkolleg is a **one-year (2-semester) foundation course.** Its job is to bridge you up to the German **Abitur (university entrance qualification / Hochschulzugangsberechtigung)** — that is, to raise your academic level to be **equivalent** to a German high-school graduate's. It ends not with a language certificate but with the **Feststellungsprüfung (FSP)** — an assessment exam. So it is not a German course; it is an **academic equivalence year.**

## You must already know German
Here's the key: you arrive at the Studienkolleg **already speaking German.** Most **public Studienkollegs require B2** (some **private** ones accept **B1**), and the entrance exam (**Aufnahmeprüfung**) tests **B1+/B2.** You learn German beforehand at a normal **language school (Sprachschule)** — not at the Studienkolleg. On top of that, teaching is **in German at B2/C1 level**; with weak German you fail. That is exactly why "go to Studienkolleg to learn German" is backwards.

## Who needs it, who can't attend?
A Studienkolleg is **not for everyone.** The only thing that decides it is how your school-leaving certificate compares to the German Abitur:

- Your diploma is **"close but not equal"** → the Studienkolleg is **for you**.
- Your diploma is **already equivalent** → you skip the Studienkolleg and **apply directly.**
- Your diploma is **far below** → the Studienkolleg is **not an option.**

Check your status in the **anabin** database (KMK) — you don't simply "choose" to attend. (More: [What is Anabin](/en/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-en).)

## The Kurse — by target field
A Studienkolleg is split into courses according to the subject you want to study:

| Course | Target field |
| --- | --- |
| **M-Kurs** | Medicine, dentistry, biology, pharmacy, veterinary |
| **T-Kurs** | Technical / engineering, maths, sciences |
| **W-Kurs** | Economics, social sciences |
| **G-Kurs** | Humanities, German studies |
| **S-Kurs** | Languages |

## Myth vs Reality
| Myth | Reality |
| --- | --- |
| "It's a course to learn German." | No — it's an Abitur-equivalence year; you need **B1/B2 just to enter.** |
| "Anyone can enrol to improve their German." | Only if your diploma is **close-but-not-equal**; learn German first at a **Sprachschule.** |
| "You get a language certificate at the end." | At the end you sit the **Feststellungsprüfung (FSP)** — an academic exam. |

## Bottom line & next step
A Studienkolleg is not a German course; it is an **academic equivalence year that bridges you to the Abitur level** and ends with the FSP. Get your German to B2 at a Sprachschule first, check your status on anabin, then apply to the right course (M/T/W/G/S). If you're aiming for **medicine**, take note: even with a top FSP grade (1.x), admission stays fiercely competitive (NC); extra points come from **FSJ/experience.**

Related: [Anabin & recognition](/en/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-en) · [Study medicine as a foreigner](/en/blog/study-medicine-in-germany-as-a-foreigner-nc-language-testas-en).

---
*Based on rules in force as of 2026; recognition, admission requirements and course arrangements vary by state/Studienkolleg — check anabin and confirm with your target Studienkolleg.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'studienkolleg-is-not-a-language-school-what-it-really-is',
                'title' => 'Studienkolleg Bir Dil Okulu Değildir: Gerçekte Ne İşe Yarar? (2026)',
                'excerpt' => 'En yaygın yanlış inanç: "Almanca öğrenmek için Studienkolleg\'e gidilir." Hayır — Studienkolleg bir dil okulu değil, Abitur\'a köprü kuran 1 yıllık akademik denklik yılıdır ve Feststellungsprüfung ile biter. Girmek için bile B1/B2 lazım, kurlar (M/T/W/G/S) ve kim gidebilir — net rehber.',
                'meta_title' => 'Studienkolleg Bir Dil Okulu Değildir — Gerçekte Ne İşe Yarar (2026)',
                'meta_description' => 'Studienkolleg dil okulu değildir: Abitur\'a köprü kuran 1 yıllık denklik yılı, FSP, girmek için B1/B2, M/T/W/G/S kurları ve anabin denkliği — yaygın miti çürüten 2026 rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'studienkolleg-is-not-a-language-school-what-it-really-is-de',
                'title' => 'Das Studienkolleg ist keine Sprachschule: Was es wirklich ist (2026)',
                'excerpt' => 'Der größte Irrtum: „Man geht ins Studienkolleg, um Deutsch zu lernen." Nein — das Studienkolleg ist keine Sprachschule, sondern ein einjähriges akademisches Angleichungsjahr zur Abitur-Ebene, das mit der Feststellungsprüfung endet. Schon zum Einstieg B1/B2, die Kurse (M/T/W/G/S) und wer überhaupt darf — klarer Leitfaden.',
                'meta_title' => 'Das Studienkolleg ist keine Sprachschule — was es wirklich ist (2026)',
                'meta_description' => 'Das Studienkolleg ist keine Sprachschule: einjähriges Angleichungsjahr zur Abitur-Ebene, FSP, B1/B2 zum Einstieg, M/T/W/G/S-Kurse und anabin-Anerkennung — der Mythos-Check 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'studienkolleg-is-not-a-language-school-what-it-really-is-en',
                'title' => 'Studienkolleg Is Not a Language School: What It Really Is (2026)',
                'excerpt' => 'The number-one myth: "You go to a Studienkolleg to learn German." No — a Studienkolleg is not a language school but a one-year academic equivalence year that bridges you to the Abitur level and ends with the Feststellungsprüfung. You need B1/B2 just to enter, the Kurse (M/T/W/G/S) and who can attend — a clear guide.',
                'meta_title' => 'Studienkolleg Is Not a Language School — What It Really Is (2026)',
                'meta_description' => 'A Studienkolleg is not a language school: a one-year Abitur-equivalence year, the FSP, B1/B2 to enter, the M/T/W/G/S Kurse and anabin recognition — the 2026 myth-buster guide.',
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
            'studienkolleg-is-not-a-language-school-what-it-really-is',
            'studienkolleg-is-not-a-language-school-what-it-really-is-de',
            'studienkolleg-is-not-a-language-school-what-it-really-is-en',
        ])->delete();
    }
};

<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da yabancı olarak tıp okumak (2026).
 *
 * Doğrulandı (make-it-in-germany, hochschulstart.de, Charité/Goethe/Bonn dil şartları,
 * mygermanuniversity, 2026):
 *  - Humanmedizin bundesweit NC → hochschulstart.de. AB/AEA + Bildungsinländer hochschulstart
 *    (notlar + sıklıkla TMS); AB-DIŞI ayrı uluslararası kontenjandan (~%5-8) uni-assist + TestAS.
 *  - Dil: min C1/DSH-2; tıpta bazı fakülteler DSH-3 (ör. Goethe-Uni Frankfurt). Kanıt başvuruda.
 *  - İngilizce tıp kamu üniversitesinde ~yok; sadece özel/şube (UMCH Hamburg, EUC Frankfurt), pahalı.
 *  - Zweitstudium (~%3): yabancı ilk diplomanın tetikleyip tetiklemediği ÜNİYE GÖRE değişir → teyit.
 *  - Maliyet: kamu harçsız (AB); Semesterbeitrag ~200-350€; AB-dışı BW 1.500€/dönem.
 *  - Yapı: 6y3a — Vorklinik(2)→Physikum→Klinik(3)→2.StEx→PJ(1, mütevazı ödenek)→3.StEx→Approbation.
 *
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 * İç-link: testas-guide + anabin (her ikisi de tr/de/en mevcut) → her dilde locale-doğru.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e70';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almanya'da tıp okuyabilir miyim?" — yabancı öğrencilerin en çok sorduğu, en çok da yanlış bilinen sorularından biri. Kısa cevap: **mümkün, ama Almanya'daki en zor yollardan biri.** Hayal kırıklığı yaşamamak için süreci ve gerçekleri net koyalım.

## 1. Tıp = ülke çapında NC + hochschulstart
Tıp (Humanmedizin) Almanya'da **bundesweit zulassungsbeschränkt** — kontenjan tüm ülkede merkezî dağıtılır, başvuru **hochschulstart.de** üzerinden yapılır. Almanlar için NC çok serttir (~1,0–1,2 Abitur ortalaması).

## 2. AB'li misin, AB-dışı mı? (en kritik ayrım)
- **AB/AEA vatandaşı** (ör. İtalya, ve Bildungsinländer): Almanlar gibi **hochschulstart.de**'den başvurursun; notların + (çoğu zaman) **TMS** belirleyici.
- **AB-dışı** (ör. Türkiye): Ayrı bir **uluslararası kontenjandan** (genelde tüm kontenjanın **~%5–8'i**) **uni-assist** üzerinden başvurursun. Notların + **TestAS** + bazen mülakat değerlendirilir. (TestAS şart mı? Bkz: [TestAS rehberi](/tr/blog/testas-guide-2026-do-you-need-it-to-study-in-germany).)

## 3. Dil: en az C1/DSH-2, tıpta sıklıkla DSH-3
Kamu üniversitelerinde tıp **tamamen Almanca**. Asgari **C1 / DSH-2** (TestDaF 4+/Goethe C1). Ama tıp/diş için **bazı fakülteler DSH-3 ister** (ör. Goethe-Uni Frankfurt). Tıpta dil kanıtı genelde **başvuru anında** istenir.

> **Gerçek uyarı:** "Anlıyorum ama konuşamıyorum" yetmez. Anatomi sözlü sınavları, klinik dönem ve PJ'de **hastalarla Almanca iletişim** hayatidir. C1 kâğıt üstünde minimum; gerçekte akıcı olman gerekir. Pas tutmuş bir C1'in varsa, başlamadan tazele.

## 4. "İngilizce tıp okurum" — neredeyse mit
Kamu üniversitelerinde **İngilizce tıp programı yok denecek kadar az.** İngilizce sadece birkaç **özel/şube kampüste** var (ör. UMCH Hamburg, European University Cyprus Frankfurt) ve bunlar **pahalı/ücretli.** Yani "Almanya'da bedava İngilizce tıp" gerçekçi değil.

## 5. Zaten bir diploman varsa: Zweitstudium tuzağı
Bir lisansı zaten bitirdiysen, **Zweitstudium (ikinci öğrenim) kontenjanına** düşebilirsin — bu çok dar (~%3) ve ayrı puanlanır. **Ama:** yabancı (Almanya dışı) bir ilk diplomanın bu statüyü tetikleyip tetiklemediği **üniversiteye göre değişir** — kimi öğrenci yurt dışı diplomasıyla "ilk öğrenim" sayılmıştır. **Mutlaka hedef üniversitenin International Office'ine / hochschulstart'a sor.** (Diploma denkliği: [Anabin nedir](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma).)

## 6. Maliyet
Kamu üniversitelerinde tıp **harçsız** (AB'li için kesin); sadece dönemlik **Semesterbeitrag ~€200–350**. (AB-dışı, Baden-Württemberg'de €1.500/dönem.) İngilizce özel programlar ise pahalıdır.

## 7. Program yapısı (6 yıl 3 ay)
1. **Vorklinik** (2 yıl) → 1. Staatsexamen (Physikum)
2. **Klinik** (3 yıl) → 2. Staatsexamen
3. **PJ – Praktisches Jahr** (1 yıl, hastanede uygulamalı; **mütevazı bir aylık ödenek** ödenir) → 3. Staatsexamen → **Approbation** (hekimlik lisansı)

## Özet ve dürüst tavsiye
Almanya'da tıp **gerçek ama zor bir hedef**: sert NC, Almanca-only (DSH-2/3), AB-dışı için dar kontenjan, klinikte dil belirleyici. Yapılabilir — ama "denemekten korkma" ile "hazırlıksız girme" arasındaki fark dildir. Pas tutmuş C1'in varsa, **başlamadan birkaç ay yoğun Almanca + TestAS/TMS hazırlığı** en akıllıca adım. Alternatif/yedek olarak biyomedikal/tıp mühendisliği yüksek lisansı ya da sonradan **Facharzt (uzmanlık) için Almanya'ya gelme** yolu da masada.

İlgili: [TestAS rehberi](/tr/blog/testas-guide-2026-do-you-need-it-to-study-in-germany) · [Anabin & denklik](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma).

---
*2026 itibarıyla yürürlükteki kurallar temel alınmıştır; kontenjan, dil ve denklik şartları üniversiteye göre değişir — başvurudan önce hochschulstart / üniversite International Office'ten teyit et.*
MD;

        $deBody = <<<'MD'
„Kann ich in Deutschland Medizin studieren?" — eine der häufigsten und meist missverstandenen Fragen internationaler Studierender. Kurz: **möglich, aber einer der schwersten Wege in Deutschland.** Damit es keine Enttäuschung gibt, hier die Fakten.

## 1. Medizin = bundesweiter NC + Hochschulstart
Humanmedizin ist in Deutschland **bundesweit zulassungsbeschränkt** — die Plätze werden zentral vergeben, die Bewerbung läuft über **hochschulstart.de**. Für Deutsche ist der NC sehr hart (~1,0–1,2 Abitur).

## 2. EU oder Nicht-EU? (die entscheidende Unterscheidung)
- **EU/EWR-Bürger** (z. B. Italien, sowie Bildungsinländer): Bewerbung wie Deutsche über **hochschulstart.de**; Noten + (oft) der **TMS** sind entscheidend.
- **Nicht-EU** (z. B. Türkei): Bewerbung über eine separate **internationale Quote** (meist **~5–8 %** der Plätze) via **uni-assist**. Hier zählen Noten + der **TestAS** + ggf. ein Auswahlgespräch. (Brauchst du TestAS? Siehe: [TestAS-Leitfaden](/de/blog/testas-guide-2026-do-you-need-it-to-study-in-germany-de).)

## 3. Sprache: mindestens C1/DSH-2, in der Medizin oft DSH-3
An staatlichen Unis ist Medizin **komplett auf Deutsch**. Minimum **C1 / DSH-2** (TestDaF 4+/Goethe C1). Für Medizin/Zahnmedizin verlangen aber **manche Fakultäten DSH-3** (z. B. Goethe-Uni Frankfurt). Der Sprachnachweis wird in der Medizin meist **schon zur Bewerbung** verlangt.

> **Realitäts-Check:** „Ich verstehe, spreche aber kaum" reicht nicht. Mündliche Anatomie-Prüfungen, der klinische Abschnitt und das PJ erfordern **Patientenkommunikation auf Deutsch**. C1 ist das Papier-Minimum; faktisch musst du fließend sein. Ein eingerostetes C1 vor dem Start auffrischen.

## 4. „Medizin auf Englisch" — fast ein Mythos
An staatlichen Unis gibt es **so gut wie keine englischsprachigen Medizinprogramme.** Englisch nur an wenigen **privaten/Zweigcampus** (z. B. UMCH Hamburg, European University Cyprus Frankfurt) — und die sind **teuer/kostenpflichtig.** „Kostenlos auf Englisch Medizin in Deutschland" ist unrealistisch.

## 5. Schon ein Abschluss? Die Zweitstudium-Falle
Hast du bereits einen Abschluss, kannst du in die **Zweitstudium-Quote** fallen — sehr klein (~3 %) und separat bewertet. **Aber:** ob ein **ausländischer** Erstabschluss diesen Status auslöst, ist **uniabhängig** — manche zählen ein Auslandsstudium als „Erststudium". **Unbedingt beim International Office / bei Hochschulstart nachfragen.** (Anerkennung: [Was ist Anabin](/de/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-de).)

## 6. Kosten
An staatlichen Unis ist Medizin **gebührenfrei** (für EU sicher); nur **Semesterbeitrag ~200–350 €**. (Nicht-EU in Baden-Württemberg: 1.500 €/Semester.) Private englische Programme sind teuer.

## 7. Studienaufbau (6 Jahre 3 Monate)
1. **Vorklinik** (2 Jahre) → 1. Staatsexamen (Physikum)
2. **Klinik** (3 Jahre) → 2. Staatsexamen
3. **PJ – Praktisches Jahr** (1 Jahr im Krankenhaus; **eine moderate monatliche Aufwandsentschädigung** wird gezahlt) → 3. Staatsexamen → **Approbation**

## Fazit & ehrlicher Rat
Medizin in Deutschland ist **real, aber hart**: strenger NC, nur Deutsch (DSH-2/3), enge Quote für Nicht-EU, Sprache entscheidet in der Klinik. Machbar — aber der Unterschied zwischen „trau dich" und „unvorbereitet starten" ist die Sprache. Bei eingerostetem C1: **vor dem Start ein paar Monate intensiv Deutsch + TestAS/TMS-Vorbereitung.** Als Plan B: ein Master in Biomedizin-/Medizintechnik oder später der Weg zum **Facharzt** in Deutschland.

Verwandt: [TestAS-Leitfaden](/de/blog/testas-guide-2026-do-you-need-it-to-study-in-germany-de) · [Anabin & Anerkennung](/de/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-de).

---
*Stand 2026; Quote, Sprache und Anerkennung variieren je nach Uni — vor der Bewerbung bei Hochschulstart / dem International Office bestätigen.*
MD;

        $enBody = <<<'MD'
"Can I study medicine in Germany?" — one of the most common and most misunderstood questions international students ask. Short answer: **possible, but one of the hardest paths in Germany.** To avoid disappointment, here are the facts.

## 1. Medicine = nationwide NC + Hochschulstart
Human medicine in Germany is **nationally restricted (bundesweit zulassungsbeschränkt)** — seats are allocated centrally and you apply via **hochschulstart.de**. For Germans the NC is brutal (~1.0–1.2 Abitur average).

## 2. EU or non-EU? (the decisive split)
- **EU/EEA citizens** (e.g. Italy, plus Bildungsinländer): apply like Germans via **hochschulstart.de**; grades + (often) the **TMS** decide.
- **Non-EU** (e.g. Turkey): apply through a separate **international quota** (usually **~5–8%** of seats) via **uni-assist**. Grades + the **TestAS** + sometimes an interview count. (Do you need TestAS? See: [TestAS guide](/en/blog/testas-guide-2026-do-you-need-it-to-study-in-germany-en).)

## 3. Language: at least C1/DSH-2, often DSH-3 for medicine
At public universities, medicine is **entirely in German**. Minimum **C1 / DSH-2** (TestDaF 4+/Goethe C1). But for medicine/dentistry **some faculties require DSH-3** (e.g. Goethe University Frankfurt). For medicine, language proof is usually required **at application**.

> **Reality check:** "I understand but barely speak" isn't enough. Oral anatomy exams, the clinical phase and the PJ require **communicating with patients in German**. C1 is the paper minimum; in practice you need to be fluent. Refresh a rusty C1 before you start.

## 4. "I'll study medicine in English" — almost a myth
Public universities have **virtually no English-taught medicine.** English exists only at a few **private/branch campuses** (e.g. UMCH Hamburg, European University Cyprus Frankfurt) — and those are **expensive/fee-paying.** "Free medicine in English in Germany" isn't realistic.

## 5. Already hold a degree? The Zweitstudium trap
If you've already completed a degree, you may fall into the **Zweitstudium (second-degree) quota** — very small (~3%) and scored separately. **But:** whether a **foreign** first degree triggers this status **varies by university** — some count a degree obtained abroad as "first studies". **Always ask the target university's International Office / Hochschulstart.** (Recognition: [What is Anabin](/en/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-en).)

## 6. Costs
At public universities medicine is **tuition-free** (certain for EU); only a **semester contribution of ~€200–350**. (Non-EU in Baden-Württemberg: €1,500/semester.) Private English programmes are expensive.

## 7. Programme structure (6 years 3 months)
1. **Preclinical** (2 years) → 1st State Exam (Physikum)
2. **Clinical** (3 years) → 2nd State Exam
3. **PJ – Practical Year** (1 year in hospital; **a modest monthly allowance** is paid) → 3rd State Exam → **Approbation** (licence to practise)

## Bottom line & honest advice
Medicine in Germany is **real but hard**: strict NC, German-only (DSH-2/3), a narrow quota for non-EU, and language decides in the clinic. Doable — but the gap between "go for it" and "starting unprepared" is the language. With a rusty C1: **a few months of intensive German + TestAS/TMS prep before you start** is the smart move. As a plan B: a master's in biomedical/medical engineering, or later the route to becoming a **Facharzt (specialist)** in Germany.

Related: [TestAS guide](/en/blog/testas-guide-2026-do-you-need-it-to-study-in-germany-en) · [Anabin & recognition](/en/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-en).

---
*Based on rules in force as of 2026; quota, language and recognition vary by university — confirm with Hochschulstart / the International Office before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'study-medicine-in-germany-as-a-foreigner-nc-language-testas',
                'title' => 'Almanya\'da Yabancı Olarak Tıp Okumak (2026): NC, Dil, TestAS/TMS ve Gerçekler',
                'excerpt' => 'Almanya\'da yabancı olarak tıp okumak mümkün ama en zor yollardan biri: bundesweit NC + hochschulstart, AB vs AB-dışı kontenjan (uni-assist + TestAS), C1/DSH-2 (tıpta sık sık DSH-3), İngilizce tıp neden mit, Zweitstudium tuzağı, maliyet ve 6 yıl 3 aylık yapı — dürüst rehber.',
                'meta_title' => 'Almanya\'da Yabancı Olarak Tıp Okumak — NC, Dil, TestAS (2026)',
                'meta_description' => 'Almanya\'da tıp: NC + hochschulstart, AB/AB-dışı kontenjan, C1/DSH-2 (sık sık DSH-3), İngilizce tıp miti, Zweitstudium ve maliyet — yabancılar için dürüst 2026 rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'study-medicine-in-germany-as-a-foreigner-nc-language-testas-de',
                'title' => 'Als Ausländer Medizin in Deutschland studieren (2026): NC, Sprache, TestAS/TMS & Realität',
                'excerpt' => 'Medizin als Ausländer in Deutschland ist möglich, aber einer der schwersten Wege: bundesweiter NC + Hochschulstart, EU vs Nicht-EU-Quote (uni-assist + TestAS), C1/DSH-2 (oft DSH-3), warum „Medizin auf Englisch" ein Mythos ist, die Zweitstudium-Falle, Kosten und der Aufbau (6 J 3 Mon) — ehrlicher Leitfaden.',
                'meta_title' => 'Medizin in Deutschland als Ausländer — NC, Sprache, TestAS (2026)',
                'meta_description' => 'Medizin in Deutschland: NC + Hochschulstart, EU/Nicht-EU-Quote, C1/DSH-2 (oft DSH-3), Englisch-Mythos, Zweitstudium & Kosten — ehrlicher Leitfaden für Ausländer 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'study-medicine-in-germany-as-a-foreigner-nc-language-testas-en',
                'title' => 'Studying Medicine in Germany as a Foreigner (2026): NC, Language, TestAS/TMS & the Reality',
                'excerpt' => 'Studying medicine in Germany as a foreigner is possible but one of the hardest paths: nationwide NC + Hochschulstart, EU vs non-EU quota (uni-assist + TestAS), C1/DSH-2 (often DSH-3), why English-taught medicine is a myth, the Zweitstudium trap, costs and the 6-year-3-month structure — an honest guide.',
                'meta_title' => 'Study Medicine in Germany as a Foreigner — NC, Language, TestAS (2026)',
                'meta_description' => 'Medicine in Germany: NC + Hochschulstart, EU/non-EU quota, C1/DSH-2 (often DSH-3), the English-taught myth, Zweitstudium & costs — an honest 2026 guide for foreigners.',
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
            'study-medicine-in-germany-as-a-foreigner-nc-language-testas',
            'study-medicine-in-germany-as-a-foreigner-nc-language-testas-de',
            'study-medicine-in-germany-as-a-foreigner-nc-language-testas-en',
        ])->delete();
    }
};

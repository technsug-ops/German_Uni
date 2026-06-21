<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da psikoloji diplomasıyla ne yapılır — terapist olmadan kariyer (2026).
 *
 * Doğrulandı:
 *  - Psikologların ÇOĞU psikoterapist DEĞİL; terapi tek (çok regüle) yol, çoğu klinik-dışı çalışır.
 *  - Klinik yol (terapi) = Master + Approbation + ~5y Weiterbildung (psikoterapist yazısında; link).
 *  - Klinik-dışı: Wirtschaftspsychologie / Arbeits- & Organisationspsychologie, HR/Personal,
 *    pazar araştırması & tüketici/UX araştırması, koçluk & danışmanlık (regülasyonsuz), people analytics, reklam/medya.
 *  - Lisans tek başına sınırlı: psikoloji fiilen Master gerektiren alan; Master planla.
 *  - Wirtschaftspsychologie: genel psikolojiden çok daha az NC-kısıtlı, iş odaklı → popüler alternatif.
 *  - İngilizce roller: büyük uluslararası şirketlerde (özellikle Berlin) bazı HR/pazar araştırması/UX/org-psikoloji
 *    rolleri İngilizce; klinikteki kadar sert dil engeli yok. Expat koçluk/danışmanlık da seçenek (regülasyonsuz, korumalı klinik unvan yok).
 *  - AB-dışı çalışma gerçeği: vize için nitelikli iş gerek; rekabet + vize → çoğu rolde akıcı Almanca büyük avantaj.
 *
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 * İç-link: studying-psychology + becoming-a-psychotherapist + changing-student-visa-to-work-permit
 *   (üçü de tr/de/en mevcut) → her dilde locale-doğru.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e79';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'finans')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Çoğu öğrencinin kafasında psikoloji = "terapist olmak" eşittir. Oysa Almanya'da **psikologların büyük çoğunluğu psikoterapist değildir.** Terapi, ağır regüle edilmiş tek bir yoldur; psikoloji diploması aslında **çok yönlü** bir diplomadır ve klinik-dışı kariyerlerin kapısını açar. Bu yazıda terapist olmadan psikoloji ile neler yapabileceğini ve İngilizce çalışma ihtimalini netleştiriyoruz.

## Klinik yol (terapi) — tek cümlede
Terapist olmak istiyorsan yol bellidir: **Master + Approbation + ~5 yıl Weiterbildung.** Uzun, pahalı ve çok regüle. Bunu ayrı ele aldık: [Almanya'da psikoterapist olmak](/tr/blog/becoming-a-psychotherapist-in-germany-2020-reform-approbation). Burada odağımız **terapi dışındaki** kariyerler.

## Klinik-dışı kariyerler (asıl geniş alan)
- **Wirtschaftspsychologie / Arbeits- & Organisationspsychologie** (iş ve örgüt psikolojisi): şirketlerde insan-davranışı, motivasyon, ekip ve süreç.
- **HR / Personal:** işe alım, yetenek yönetimi, organizasyon gelişimi.
- **Pazar araştırması & tüketici / UX araştırması:** kullanıcı davranışı, anket/deney tasarımı, ürün ve reklam içgörüsü.
- **Koçluk & danışmanlık:** regülasyonsuz alan — ama **korumalı klinik unvanları kullanamazsın.**
- Ek olarak **people analytics**, reklam/medya psikolojisi gibi nişler.

## Büyük ihtimalle Master gerekecek
Almanya'da psikolojide **lisans tek başına oldukça sınırlı.** İyi rollerin çoğu fiilen **Master gerektirir.** Yani plan yaparken Bachelor'ı bir durak, **Master'ı asıl hedef** olarak gör — özellikle klinik tarafına geçme ihtimalini açık tutmak istiyorsan.

## İngilizce çalışma açısı (Almancan kusursuz değilse)
İyi haber: büyük **uluslararası şirketlerde** (özellikle **Berlin**) bazı **HR / pazar araştırması / UX / örgüt psikolojisi** rolleri **İngilizce** yürütülür. Burada kusursuz olmayan Almanca, klinik işteki kadar sert bir engel değildir. Expat'lere yönelik **koçluk / danışmanlık** da bir seçenek (regülasyonsuz; yalnız korumalı klinik unvan kullanamazsın). Yine de **AB-dışı** isen vize için nitelikli bir işe ihtiyacın var; rekabet + vize şartları yüzünden **akıcı Almanca çoğu rolde büyük avantajdır.**

## Daha kolay giriş: Wirtschaftspsychologie
Terapi yerine **kurumsal** kariyer istiyorsan, **Wirtschaftspsychologie (iş psikolojisi)** mantıklı bir tercih: genel psikolojiye göre **çok daha az NC-kısıtlı** ve **iş odaklı.** Bu yüzden terapist olmak istemeyen ama insan-davranışı + iş dünyasını birleştirmek isteyenler için popüler bir alternatiftir.

| Kariyer | Almanca şart mı? | Ek nitelik | Not |
|---|---|---|---|
| Terapi | Evet (C1+) | Approbation + Weiterbildung | En regüle yol |
| HR / Personal | Genelde Almanca | Master | Intl. firmalarda bazen İngilizce |
| Pazar araştırması / UX | İngilizce mümkün | Master | Berlin'de intl. roller |
| Koçluk / danışmanlık | Değişir | — | Regülasyonsuz, korumalı unvan yok |

## Özet ve sonraki adım
Psikoloji ≠ "terapist olmak". Diploma çok yönlü; **klinik-dışı** kariyerler (iş psikolojisi, HR, pazar/UX araştırması, koçluk) geniş bir alan açar ve bir kısmı **İngilizce** yapılabilir. Çoğu için **Master şart**, terapi için ise ayrı ve uzun bir lisanslama yolu var. İşe geçişi düşünüyorsan: [yabancı olarak psikoloji okumak](/tr/blog/studying-psychology-in-germany-international-students-nc-language) ve [öğrenci→çalışma vizesi geçişi](/tr/blog/changing-student-visa-to-work-permit-germany-zweckwechsel).

---
*2026 itibarıyla geçerli genel duruma dayanır; unvan koruması, vize ve işveren dil şartları role ve şirkete göre değişir — başvurudan önce teyit et.*
MD;

        $deBody = <<<'MD'
Bei vielen Studierenden steht Psychologie = „Therapeut werden" gleich. Dabei sind in Deutschland **die meisten Psychologen keine Psychotherapeuten.** Therapie ist nur ein einziger, stark regulierter Weg; der Psychologie-Abschluss ist eigentlich **vielseitig** und öffnet zahlreiche nicht-klinische Karrieren. Hier klären wir, was du ohne Therapie mit Psychologie machen kannst — und wo Englisch reicht.

## Der klinische Weg (Therapie) — in einem Satz
Willst du Therapeut werden, ist der Weg klar: **Master + Approbation + ~5 Jahre Weiterbildung.** Lang, teuer, hochreguliert. Das behandeln wir separat: [Psychotherapeut in Deutschland werden](/de/blog/becoming-a-psychotherapist-in-germany-2020-reform-approbation-de). Hier geht es um Karrieren **jenseits der Therapie.**

## Nicht-klinische Karrieren (das eigentlich große Feld)
- **Wirtschaftspsychologie / Arbeits- & Organisationspsychologie:** menschliches Verhalten, Motivation, Teams und Prozesse in Unternehmen.
- **HR / Personal:** Recruiting, Talentmanagement, Organisationsentwicklung.
- **Marktforschung & Konsumenten- / UX-Forschung:** Nutzerverhalten, Studien-Design, Produkt- und Werbeinsights.
- **Coaching & Beratung:** unreguliertes Feld — aber **geschützte klinische Titel darfst du nicht führen.**
- Dazu Nischen wie **People Analytics**, Werbe-/Medienpsychologie.

## Du brauchst sehr wahrscheinlich einen Master
In Deutschland ist der **Bachelor allein ziemlich begrenzt.** Die meisten guten Rollen verlangen faktisch einen **Master.** Plane den Bachelor als Zwischenschritt und den **Master als eigentliches Ziel** — gerade wenn du dir den klinischen Weg offenhalten willst.

## Englisch-Perspektive (wenn dein Deutsch nicht perfekt ist)
Gute Nachricht: In großen **internationalen Unternehmen** (besonders in **Berlin**) laufen manche **HR- / Marktforschungs- / UX- / organisationspsychologische** Rollen **auf Englisch**. Nicht-perfektes Deutsch ist hier kein so harter Blocker wie in der Klinik. Auch **Coaching / Beratung** für Expats ist möglich (unreguliert; nur ohne geschützte klinische Titel). Trotzdem: Als **Nicht-EU-Bürger** brauchst du für das Visum einen qualifizierenden Job; wegen Wettbewerb + Visumsanforderungen ist **fließendes Deutsch in den meisten Rollen ein großer Vorteil.**

## Leichterer Zugang: Wirtschaftspsychologie
Willst du statt Therapie eine **unternehmensnahe** Karriere, ist **Wirtschaftspsychologie** eine sinnvolle Wahl: deutlich **weniger NC-beschränkt** als die allgemeine Psychologie und **berufsorientiert.** Daher eine beliebte Alternative für alle, die Menschenverhalten mit der Geschäftswelt verbinden wollen, statt zu therapieren.

| Karriere | Deutsch nötig? | Zusatzqualifikation | Hinweis |
|---|---|---|---|
| Therapie | Ja (C1+) | Approbation + Weiterbildung | Am stärksten reguliert |
| HR / Personal | Meist Deutsch | Master | In intl. Firmen teils Englisch |
| Marktforschung / UX | Englisch möglich | Master | Intl. Rollen in Berlin |
| Coaching / Beratung | Variiert | — | Unreguliert, kein geschützter Titel |

## Fazit & nächster Schritt
Psychologie ≠ „Therapeut werden". Der Abschluss ist vielseitig; **nicht-klinische** Karrieren (Wirtschaftspsychologie, HR, Markt-/UX-Forschung, Coaching) eröffnen ein breites Feld, und ein Teil davon geht **auf Englisch.** Für die meisten ist ein **Master Pflicht**, für Therapie ein separater, langer Lizenzweg. Wenn du an den Berufseinstieg denkst: [Psychologie als Ausländer studieren](/de/blog/studying-psychology-in-germany-international-students-nc-language-de) und [Wechsel von Studenten- zu Arbeitsvisum](/de/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-de).

---
*Stand 2026, allgemeiner Überblick; Titelschutz, Visum und Sprachanforderungen der Arbeitgeber variieren je nach Rolle und Firma — vor der Bewerbung bestätigen.*
MD;

        $enBody = <<<'MD'
For many students, psychology equals "becoming a therapist." But in Germany, **most psychologists are not psychotherapists.** Therapy is just one heavily regulated path; a psychology degree is actually **versatile** and opens many non-clinical careers. This article clarifies what you can do with psychology beyond therapy — and where working in English is realistic.

## The clinical path (therapy) — in one line
If you want to become a therapist, the route is clear: **Master + Approbation + ~5 years of Weiterbildung.** Long, expensive, highly regulated. We cover it separately: [becoming a psychotherapist in Germany](/en/blog/becoming-a-psychotherapist-in-germany-2020-reform-approbation-en). Here we focus on careers **beyond therapy.**

## Non-clinical careers (the genuinely big field)
- **Wirtschaftspsychologie / work & organizational psychology:** human behaviour, motivation, teams and processes inside companies.
- **HR / Personal:** recruiting, talent management, organizational development.
- **Market research & consumer / UX research:** user behaviour, study design, product and advertising insight.
- **Coaching & counselling:** an unregulated field — but **you cannot use protected clinical titles.**
- Plus niches like **people analytics** and advertising/media psychology.

## You'll most likely need a Master
In Germany the **Bachelor alone is fairly limited.** Most decent roles effectively require a **Master.** So plan the Bachelor as a stepping stone and the **Master as the real target** — especially if you want to keep the clinical path open.

## The English-language angle (if your German isn't perfect)
Good news: in large **international companies** (especially in **Berlin**), some **HR / market-research / UX / org-psychology** roles are done **in English**. Non-perfect German is far less of a hard blocker here than in clinical work. **Coaching / counselling** for expats is also an option (unregulated; just no protected clinical titles). Still, as a **non-EU** national you need a qualifying job for your visa; with competitive fields plus visa requirements, **fluent German is a big advantage in most roles.**

## Easier admission: Wirtschaftspsychologie
If you want a **corporate** career rather than therapy, **Wirtschaftspsychologie (business psychology)** is a sensible choice: far **less NC-restricted** than general psychology and more **job-oriented.** That makes it a popular alternative for those who want to combine human behaviour with the business world instead of doing therapy.

| Career | German needed? | Extra qualification | Notes |
|---|---|---|---|
| Therapy | Yes (C1+) | Approbation + Weiterbildung | Most regulated path |
| HR / Personal | Usually German | Master | Sometimes English in intl. firms |
| Market research / UX | English possible | Master | Intl. roles in Berlin |
| Coaching / counselling | Varies | — | Unregulated, no protected title |

## Bottom line & next step
Psychology ≠ "becoming a therapist." The degree is versatile; **non-clinical** careers (business psychology, HR, market/UX research, coaching) open a wide field, and some of it can be done **in English.** For most roles a **Master is required**, while therapy is a separate, long licensing route. If you're thinking about getting into work: [studying psychology as an international student](/en/blog/studying-psychology-in-germany-international-students-nc-language-en) and [changing your student visa to a work permit](/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en).

---
*Based on the general situation as of 2026; title protection, visa rules and employer language requirements vary by role and company — confirm before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'what-can-you-do-with-a-psychology-degree-in-germany',
                'title' => 'Almanya\'da Psikoloji Diplomasıyla Ne Yapılır? Terapist Olmadan Kariyer (2026)',
                'excerpt' => 'Almanya\'da psikologların çoğu terapist değil: terapi tek (regüle) yol. Klinik-dışı kariyerler — Wirtschaftspsychologie/iş psikolojisi, HR, pazar & UX araştırması, koçluk — Master gerekliliği, İngilizce çalışma açısı ve daha kolay giriş için Wirtschaftspsychologie alternatifi.',
                'meta_title' => 'Almanya\'da Psikoloji Diplomasıyla Ne Yapılır — Terapi Dışı Kariyer (2026)',
                'meta_description' => 'Almanya\'da psikoloji: terapi dışı kariyerler (iş psikolojisi, HR, pazar/UX araştırması, koçluk), neden Master şart, İngilizce roller ve daha az NC\'li Wirtschaftspsychologie alternatifi — 2026.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'what-can-you-do-with-a-psychology-degree-in-germany-de',
                'title' => 'Was kann man mit einem Psychologie-Abschluss in Deutschland machen? Karriere ohne Therapie (2026)',
                'excerpt' => 'Die meisten Psychologen in Deutschland sind keine Therapeuten: Therapie ist nur ein regulierter Weg. Nicht-klinische Karrieren — Wirtschaftspsychologie, HR, Markt- & UX-Forschung, Coaching — die Master-Notwendigkeit, die Englisch-Perspektive und Wirtschaftspsychologie als leichterer Zugang.',
                'meta_title' => 'Was kann man mit Psychologie in Deutschland machen — Karriere ohne Therapie (2026)',
                'meta_description' => 'Psychologie in Deutschland: nicht-klinische Karrieren (Wirtschaftspsychologie, HR, Markt-/UX-Forschung, Coaching), warum ein Master nötig ist, Englisch-Rollen und Wirtschaftspsychologie als Alternative — 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'what-can-you-do-with-a-psychology-degree-in-germany-en',
                'title' => 'What Can You Do With a Psychology Degree in Germany? Careers Beyond Therapy (2026)',
                'excerpt' => 'Most psychologists in Germany are not therapists: therapy is just one regulated path. Non-clinical careers — business psychology, HR, market & UX research, coaching — why you\'ll need a Master, the English-language angle, and Wirtschaftspsychologie as the easier-admission alternative.',
                'meta_title' => 'What Can You Do With a Psychology Degree in Germany — Beyond Therapy (2026)',
                'meta_description' => 'Psychology in Germany: non-clinical careers (business psychology, HR, market/UX research, coaching), why a Master is needed, English-language roles and Wirtschaftspsychologie as a less-NC alternative — 2026.',
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
            'what-can-you-do-with-a-psychology-degree-in-germany',
            'what-can-you-do-with-a-psychology-degree-in-germany-de',
            'what-can-you-do-with-a-psychology-degree-in-germany-en',
        ])->delete();
    }
};

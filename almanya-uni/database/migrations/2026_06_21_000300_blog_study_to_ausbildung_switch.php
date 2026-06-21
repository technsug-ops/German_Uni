<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Üniversiteden / şartlı kabul (Studienkolleg) sürecinden Ausbildung'a geçiş.
 *
 * Hukuki temel (gesetze-im-internet.de §16a/§16b):
 *  - 2023–2024 reformlarından sonra §16b'den §16a'ya (Berufsausbildung) geçiş, şartlar
 *    tutarsa mezun olmadan da mümkün; gerekli: Ausbildung sözleşmesi + Bundesagentur für
 *    Arbeit (BA) onayı + nitelikli (genelde >=2 yıllık) Ausbildung mesleği.
 *  - Ausbildung sonrası §18a (mesleki Fachkraft) çalışma iznine yol açık.
 *
 * Yazar: Halil Yaprakli. Kategori: visa-residence. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e62';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'visa-residence')->value('id')
            ?? DB::table('categories')->where('slug', 'vize')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'ya üniversite için geldin ama "belki de bana uygun yol **Ausbildung (meslek eğitimi)**" diye düşünmeye başladıysan, iyi haber: **artık öğrenciyken — hatta dil kursu / Studienkolleg gibi öğrenime hazırlık (şartlı kabul) sürecindeyken bile — meslek eğitimine geçip oturum izni türünü değiştirebilirsin.**

## Eskiden neydi, şimdi ne?
Eskiden öğrenci oturum izninden (§16b) başka amaca geçiş büyük ölçüde kısıtlıydı. **2023–2024 reformlarından sonra** bu kısıtlama neredeyse tamamen kalktı. Bugün **§16a (meslek eğitimi oturum izni)** için şartları taşıyorsan, üniversiteyi bitirmiş olman gerekmez.

## Ausbildung'a geçiş için ne gerekir?
1. **Bir Ausbildung yeri (sözleşmesi):** Bir işletmeyle imzalanmış dual meslek eğitimi sözleşmesi.
2. **Federal İş Ajansı (Bundesagentur für Arbeit) onayı:** Geçiş, nitelikli (genelde en az 2 yıllık) bir Ausbildung mesleğine olmalı ve BA onayı gerekir.
3. **Geçim ve genel şartlar:** Geçimini kanıtlama, geçerli pasaport vb. standart şartlar.

## Kimler için mantıklı?
- Almancası iyi (genelde **B1–B2**) ve **uygulamalı öğrenmeyi** sevenler.
- Üniversite teorisinde zorlanan, ama **maaşlı + işe alınma garantisine yakın** bir yol isteyenler.
- "Üniversiteyi bitiremezsem statüm ne olur?" kaygısı taşıyanlar için sağlam bir B planı.

## Avantajları
- **Maaşlı eğitim:** Ausbildung süresince ücret alırsın.
- **İşe alınma:** Sektör açığı varsa eğitim sonrası kalıcılık ihtimali yüksek.
- **Net oturum yolu:** Ausbildung bitince **§18a (mesleki nitelikli iş gücü)** çalışma iznine geçiş açık.

## Dikkat edilecekler
- Geçiş otomatik değil; **sözleşme + BA onayı + yabancılar dairesi** üçlüsü gerekir.
- "Geçici" / niteliksiz işlere yönelik izinler hâlâ istisna; ama gerçek bir **mesleki Ausbildung** kapsam içindedir.
- Şartlı kabul / Studienkolleg sürecindeysen de bu yol açıktır — ama her ilin yabancılar dairesi uygulaması farklı olabilir; önce teyit al.

## Özet
Üniversite herkese göre değil ve artık bu bir çıkmaz değil. Almanca seviyen ve bir Ausbildung sözleşmen varsa, öğrenci / şartlı kabul sürecinden **meslek eğitimine geçip oturum izni türünü değiştirebilirsin** — maaşlı, pratik ve işe en yakın yollardan birine.

---
*2026 itibarıyla yürürlükteki düzenlemeler temel alınmıştır. Uygulama il il değişebilir — başvurudan önce yabancılar dairesinden teyit et.*
MD;

        $deBody = <<<'MD'
Du bist zum Studium nach Deutschland gekommen, denkst aber, dass eine **Ausbildung** vielleicht besser zu dir passt? Gute Nachricht: **Du kannst jetzt schon während des Studiums — sogar während der Studienvorbereitung (Sprachkurs/Studienkolleg, bedingte Zulassung) — in eine Berufsausbildung wechseln und deinen Aufenthaltstitel ändern.**

## Früher vs. heute
Früher war der Wechsel von der studienbezogenen Aufenthaltserlaubnis (§16b) zu einem anderen Zweck stark eingeschränkt. **Nach den Reformen 2023–2024** ist diese Einschränkung fast vollständig entfallen. Wenn du die Voraussetzungen für **§16a (Aufenthaltserlaubnis zur Berufsausbildung)** erfüllst, musst du dein Studium nicht abgeschlossen haben.

## Was wird für den Wechsel zur Ausbildung benötigt?
1. **Ein Ausbildungsplatz (Vertrag):** ein unterschriebener dualer Ausbildungsvertrag mit einem Betrieb.
2. **Zustimmung der Bundesagentur für Arbeit:** Der Wechsel muss in eine qualifizierte (i. d. R. mindestens 2-jährige) Ausbildung erfolgen und von der BA genehmigt werden.
3. **Lebensunterhalt & allgemeine Voraussetzungen:** Sicherung des Lebensunterhalts, gültiger Pass usw.

## Für wen ist das sinnvoll?
- Wer gut Deutsch spricht (meist **B1–B2**) und **praktisches Lernen** mag.
- Wer mit der Theorie an der Uni hadert, aber einen **bezahlten Weg mit hoher Übernahmechance** sucht.
- Als solider Plan B gegen die Sorge „Was passiert mit meinem Status, wenn ich das Studium nicht schaffe?"

## Vorteile
- **Bezahlte Ausbildung:** Du erhältst während der Ausbildung eine Vergütung.
- **Übernahme:** Bei Fachkräftemangel sind die Chancen auf Übernahme hoch.
- **Klarer Aufenthaltsweg:** Nach der Ausbildung ist der Wechsel in eine Arbeitserlaubnis als **Fachkraft mit Berufsausbildung (§18a)** offen.

## Worauf achten?
- Der Wechsel ist nicht automatisch; nötig sind **Vertrag + BA-Zustimmung + Ausländerbehörde**.
- Erlaubnisse für „vorübergehende" / unqualifizierte Beschäftigung bleiben die Ausnahme — eine echte **Berufsausbildung** fällt aber darunter.
- Auch in der Studienvorbereitung / im Studienkolleg ist dieser Weg offen — die Praxis der Ausländerbehörden ist jedoch regional unterschiedlich; vorher bestätigen.

## Fazit
Die Uni ist nicht für jeden — und das ist heute keine Sackgasse mehr. Mit ausreichenden Deutschkenntnissen und einem Ausbildungsvertrag kannst du aus dem Studium / der bedingten Zulassung **in eine Berufsausbildung wechseln und deinen Aufenthaltstitel ändern** — hin zu einem bezahlten, praxisnahen Weg mit hoher Jobchance.

---
*Stand 2026. Die Praxis kann regional variieren — vor der Antragstellung bei der Ausländerbehörde bestätigen.*
MD;

        $enBody = <<<'MD'
You came to Germany to study, but you're starting to think an **Ausbildung (vocational training)** might suit you better? Good news: **you can now switch into vocational training while still a student — even during study preparation (language course/Studienkolleg, i.e. conditional admission) — and change your residence permit.**

## Old vs. new
Previously, switching from a study residence permit (§16b) to a different purpose was heavily restricted. **After the 2023–2024 reforms** that restriction was almost entirely removed. If you meet the requirements for **§16a (residence permit for vocational training)**, you don't need to have finished your studies.

## What's needed to switch to an Ausbildung?
1. **A training place (contract):** a signed dual vocational-training contract with a company.
2. **Approval from the Federal Employment Agency (Bundesagentur für Arbeit):** the switch must be into a qualified (usually at least 2-year) training occupation and approved by the BA.
3. **Livelihood & general conditions:** proof of livelihood, a valid passport, etc.

## Who is it for?
- Those with good German (usually **B1–B2**) who enjoy **hands-on learning.**
- Those struggling with university theory but wanting a **paid path with a high chance of being hired.**
- A solid plan B against the worry "what happens to my status if I can't finish my degree?"

## Advantages
- **Paid training:** you earn a wage throughout the Ausbildung.
- **Hiring:** where there's a labour shortage, the chance of being kept on after training is high.
- **A clear residence path:** after the Ausbildung, you can switch to a work permit as a **skilled worker with vocational training (§18a).**

## Things to watch
- The switch isn't automatic; you need a **contract + BA approval + the immigration office.**
- Permits for "temporary" / unskilled employment remain the exception — but a genuine **vocational Ausbildung** is covered.
- This path is also open during study preparation / Studienkolleg — but immigration-office practice varies by region, so confirm first.

## Bottom line
University isn't for everyone — and that's no longer a dead end. With enough German and a training contract, you can switch from study / conditional admission **into vocational training and change your residence permit** — onto a paid, practical path with a strong shot at a job.

---
*Based on rules in force as of 2026. Practice can vary by region — confirm with the immigration office before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'switching-from-study-to-ausbildung-germany-residence-permit',
                'title' => 'Üniversiteden Ausbildung\'a (Meslek Eğitimine) Geçiş: Artık Mümkün (2026)',
                'excerpt' => 'Almanya\'da öğrenciyken — hatta dil/Studienkolleg (şartlı kabul) sürecindeyken bile — Ausbildung\'a geçip oturum izni türünü değiştirmek artık mümkün. §16a şartları: Ausbildung sözleşmesi + Bundesagentur für Arbeit onayı; sonrasında §18a çalışma izni yolu, avantajlar ve dikkat edilecekler.',
                'meta_title' => 'Üniversiteden Ausbildung\'a Geçiş — Almanya 2026',
                'meta_description' => 'Öğrenci / şartlı kabul sürecinden meslek eğitimine (§16a) geçiş: sözleşme + BA onayı, maaşlı eğitim, §18a yolu ve pratik uyarılar (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'switching-from-study-to-ausbildung-germany-residence-permit-de',
                'title' => 'Vom Studium zur Ausbildung wechseln: Jetzt möglich (2026)',
                'excerpt' => 'In Deutschland kannst du während des Studiums — sogar in der Studienvorbereitung (Studienkolleg/bedingte Zulassung) — in eine Berufsausbildung wechseln und den Aufenthaltstitel ändern. Voraussetzungen für §16a: Ausbildungsvertrag + Zustimmung der Bundesagentur für Arbeit; danach Weg zu §18a, Vorteile und Praxis.',
                'meta_title' => 'Vom Studium zur Ausbildung wechseln — Deutschland 2026',
                'meta_description' => 'Wechsel vom Studium / der bedingten Zulassung in die Berufsausbildung (§16a): Vertrag + BA-Zustimmung, bezahlte Ausbildung, §18a-Weg und Praxis (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'switching-from-study-to-ausbildung-germany-residence-permit-en',
                'title' => 'Switching from Study to Ausbildung (Vocational Training) in Germany: Now Possible (2026)',
                'excerpt' => 'In Germany you can switch into vocational training while studying — even during study preparation (Studienkolleg/conditional admission) — and change your residence permit. §16a requirements: a training contract + Federal Employment Agency approval; then the §18a route, plus advantages and practical notes.',
                'meta_title' => 'Switch from Study to Ausbildung — Germany 2026',
                'meta_description' => 'Switching from study / conditional admission into vocational training (§16a): contract + BA approval, paid training, the §18a route and practical notes (2026).',
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
            'switching-from-study-to-ausbildung-germany-residence-permit',
            'switching-from-study-to-ausbildung-germany-residence-permit-de',
            'switching-from-study-to-ausbildung-germany-residence-permit-en',
        ])->delete();
    }
};

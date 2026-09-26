<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Exmatrikulation rehberi (TR/EN/DE) — doğrulanmamış / fazla genel ifadelerin düzeltilmesi.
 *
 * Değişenler: yalnız onaylanan gövde cümleleri (+ TR excerpt). Slug, başlık, canonical, translation_group_id,
 * published_at ve hreflang kümesi DEĞİŞMEZ.
 *   - "üçüncü denemede = endgültig nicht bestanden" varsayımı → deneme hakları Prüfungsordnung'a göre tükenince.
 *   - "birçok PO istisnai 4. deneme öngörür" → bazı PO'lar ek hak / Härtefall tanıyabilir.
 *   - "§ 16b izni kayıt bitince düşer" → otomatik değil; ABH duruma göre kısaltabilir / yeniden değerlendirebilir
 *     (§ 7(2) S. 2 AufenthG takdir; BMI Anwendungshinweise 06/2024: program değişikliği ayrıca değerlendirilir).
 *   - "üniversiteler kayıt silmeyi ABH'ye bildirir" → kaynakla doğrulanmadı, kaldırıldı.
 *   - § 20 Abs. 3 → § 20 Abs. 1 Nr. 1; SSS başlığındaki "üç kez kaldım" varsayımı kaldırıldı.
 *   - Yeni not sistemi / sınav tekrarı rehberine karşılıklı link.
 *
 * ATOMİK + FAIL-FAST: önce üç dilin TÜM beklenen eski blokları kontrol edilir. Biri bile yoksa (ve yenisi de
 * yoksa) RuntimeException — hiçbir dile yazılmaz. Hepsi doğrulanırsa TR+EN+DE TEK transaction'da güncellenir.
 * Hepsi zaten uygulanmışsa no-op; kısmen uygulanmış tutarsız durumda yine exception (yarım uygulama yok).
 */
return new class extends Migration
{
    public function up(): void
    {
        $spec = [
            'tr' => [
                'slug' => 'exmatrikulation-germany-causes-residence-permit-and-coming-back',
                'excerpt' => ['üçüncü sınav hakkı bitince ne olur', 'sınav hakları tükenince (endgültig nicht bestanden) ne olur'],
                'pairs' => [
                    ['| Orta — hukuken sorunsuz ama oturumun dayanağı kalkar |',
                     '| Orta — hukuken sorunsuz ama oturumun amacı sona erer |'],
                    ['Bir dersi üçüncü kez geçemediğinde ortaya çıkan sonuç Almanca\'da *endgültig nicht bestanden* olarak geçer.',
                     '*Endgültig nicht bestanden*, geçerli Prüfungsordnung\'unun tanıdığı deneme haklarını tükettiğinde ortaya çıkar. Deneme hakkı sayısı programdan programa değişir.'],
                    ['— sınav hakkı sayısı, tekrar sınavı imkânı ve istisnalar üniversiteden üniversiteye değişir.',
                     '— sınav hakkı sayısı, tekrar sınavı imkânı ve istisnalar üniversiteden üniversiteye değişir. Not sistemi, deneme hakları ve tekrar süreleri için [not sistemi ve sınav tekrar hakkı rehberimize](/tr/blog/german-university-grading-system-and-exam-retakes) bak.'],
                    ['- **Härtefallantrag / ek deneme:** birçok Prüfungsordnung istisnai bir dördüncü deneme öngörür.',
                     '- **Härtefallantrag / ek deneme:** bazı Prüfungsordnung\'lar ek deneme veya hardship imkânı tanır — kendi yönetmeliğini kontrol et.'],
                    ['**§ 16b AufenthG** ile aldığın oturum izni öğrenim amacına bağlıdır — kaydın silinince iznin dayanağı ortadan kalkar. Üniversiteler kayıt silmeyi **Ausländerbehörde\'ye bildirir**, yani "belki fark etmez" diye beklemek işe yaramaz.',
                     '**§ 16b AufenthG** ile aldığın oturum izni öğrenim amacına bağlıdır. Öğrenci statüsünün sona ermesi, oturum iznini aynı anda otomatik olarak geçersiz kılmaz; Ausländerbehörde duruma göre izni kısaltabilir veya yeniden değerlendirebilir. Bu yüzden "belki fark etmez" diye beklemek işe yaramaz.'],
                    ['| § 16b kapsamında öğrenim devam eder | ABH onayı gerekir; amaç değişikliği makul sürede tamamlanabilmeli |',
                     '| § 16b kapsamında öğrenim devam eder | Program değişikliği ayrıca değerlendirilir; yeni izin başvurusu gerekir ve öğrenim makul sürede tamamlanabilmeli |'],
                    ['§ 20 Abs. 3 — 18 aya kadar iş arama',
                     '§ 20 Abs. 1 Nr. 1 — 18 aya kadar iş arama'],
                    ['Ama § 16b iznin dayanağı kalktığı için **harekete geçmezsen** çıkış yükümlülüğü doğabilir.',
                     'Ama § 16b izninin amacı sona erebileceği için ABH izni kısaltabilir ve **harekete geçmezsen** çıkış yükümlülüğü doğabilir.'],
                    ['### Bir dersten üç kez kaldım. Aynı bölümü başka üniversitede okuyabilir miyim?',
                     '### Bir sınavdan kesin olarak kaldım (endgültig nicht bestanden). Aynı bölümü başka üniversitede okuyabilir miyim?'],
                    ['Öğrenim amacı sona erdiği için izninin dayanağı düşer.',
                     'Öğrenim amacı sona erer; ABH iznini kısaltabilir veya yeniden değerlendirebilir.'],
                ],
            ],
            'en' => [
                'slug' => 'exmatrikulation-germany-causes-residence-permit-and-coming-back-en',
                'excerpt' => null,
                'pairs' => [
                    ['Failing a course on the third attempt produces the result *endgültig nicht bestanden*.',
                     '*Endgültig nicht bestanden* occurs when you exhaust the attempts allowed by your applicable Prüfungsordnung. The number of attempts varies.'],
                    ['— the number of attempts, repeat options and exceptions differ between universities.',
                     '— the number of attempts, repeat options and exceptions differ between universities. How grades, attempts and repeat deadlines work in practice is explained in our [guide to German university grades and exam retakes](/en/blog/german-university-grading-system-and-exam-retakes-en).'],
                    ['- **Hardship application / extra attempt:** many examination regulations provide for an exceptional fourth attempt.',
                     '- **Hardship application / extra attempt:** some examination regulations provide additional attempts or hardship options — check yours.'],
                    ['A residence permit under **Section 16b AufenthG** is tied to the purpose of study — once your enrolment ends, the basis for the permit falls away. Universities **report de-registrations to the Ausländerbehörde**, so waiting and hoping nobody notices does not work.',
                     'A residence permit under **Section 16b AufenthG** is tied to the purpose of study. Loss of student status does not automatically cancel the residence permit at the same moment. The Ausländerbehörde may shorten or reassess the permit, depending on the circumstances — so waiting and hoping does not work.'],
                    ['| Study continues under Section 16b | The authority must consent; the purpose must remain achievable within a reasonable time |',
                     '| Study continues under Section 16b | A change of programme is assessed separately; a new permit has to be applied for, and the purpose must remain achievable within a reasonable time |'],
                    ['Section 20(3) — up to 18 months to find work',
                     'Section 20(1) no. 1 — up to 18 months to find work'],
                    ['But because the basis for your Section 16b permit falls away, an obligation to leave can arise **if you do nothing.**',
                     'But because the purpose of your Section 16b permit can end, the authority may shorten the permit, and an obligation to leave can arise **if you do nothing.**'],
                    ['### I failed a course three times. Can I study the same subject elsewhere?',
                     '### I finally failed an exam. Can I study the same subject elsewhere?'],
                    ['The purpose of your stay ends, so the basis for the permit falls away.',
                     'The purpose of your stay ends, and the authority may shorten or reassess your permit.'],
                ],
            ],
            'de' => [
                'slug' => 'exmatrikulation-germany-causes-residence-permit-and-coming-back-de',
                'excerpt' => null,
                'pairs' => [
                    ['Wer eine Prüfung im dritten Versuch nicht besteht, erhält das Ergebnis *endgültig nicht bestanden*.',
                     '*Endgültig nicht bestanden* tritt ein, wenn du die Versuche ausgeschöpft hast, die deine geltende Prüfungsordnung erlaubt. Die Zahl der Versuche ist unterschiedlich.'],
                    ['— Versuchszahl, Wiederholungsmöglichkeiten und Ausnahmen unterscheiden sich von Hochschule zu Hochschule.',
                     '— Versuchszahl, Wiederholungsmöglichkeiten und Ausnahmen unterscheiden sich von Hochschule zu Hochschule. Wie Noten, Versuche und Wiederholungsfristen konkret geregelt sind, erklärt unser [Ratgeber zu Notensystem und Prüfungswiederholung](/de/blog/german-university-grading-system-and-exam-retakes-de).'],
                    ['- **Härtefallantrag / zusätzlicher Versuch:** Viele Prüfungsordnungen sehen einen ausnahmsweisen vierten Versuch vor.',
                     '- **Härtefallantrag / zusätzlicher Versuch:** Manche Prüfungsordnungen sehen zusätzliche Versuche oder Härtefallregelungen vor — prüfe deine.'],
                    ['Die Aufenthaltserlaubnis nach **§ 16b AufenthG** ist an den Studienzweck gebunden — fällt die Immatrikulation weg, entfällt die Grundlage. Hochschulen **melden Exmatrikulationen der Ausländerbehörde**; abzuwarten und zu hoffen, dass es niemand bemerkt, funktioniert nicht.',
                     'Die Aufenthaltserlaubnis nach **§ 16b AufenthG** ist an den Studienzweck gebunden. Der Verlust des Studierendenstatus beendet den Aufenthaltstitel nicht automatisch im selben Moment; die Ausländerbehörde kann ihn je nach Umständen nachträglich verkürzen oder neu bewerten. Abzuwarten und zu hoffen, funktioniert deshalb nicht.'],
                    ['| Studium nach § 16b läuft weiter | Zustimmung der ABH nötig; der Zweck muss in angemessener Zeit erreichbar bleiben |',
                     '| Studium nach § 16b läuft weiter | Ein Studiengangwechsel wird gesondert geprüft; der Titel ist neu zu beantragen, und der Zweck muss in angemessener Zeit erreichbar bleiben |'],
                    ['§ 20 Abs. 3 — bis zu 18 Monate Arbeitsplatzsuche',
                     '§ 20 Abs. 1 Nr. 1 — bis zu 18 Monate Arbeitsplatzsuche'],
                    ['Weil aber die Grundlage deines § 16b-Titels entfällt, kann eine Ausreisepflicht entstehen, **wenn du nicht handelst.**',
                     'Weil der Zweck deines § 16b-Titels enden kann, darf die ABH den Titel verkürzen, und eine Ausreisepflicht kann entstehen, **wenn du nicht handelst.**'],
                    ['### Ich bin dreimal durchgefallen. Kann ich dasselbe Fach anderswo studieren?',
                     '### Ich habe eine Prüfung endgültig nicht bestanden. Kann ich dasselbe Fach anderswo studieren?'],
                    ['Der Studienzweck endet, damit entfällt die Grundlage des Titels.',
                     'Der Studienzweck endet; die ABH kann den Titel verkürzen oder neu bewerten.'],
                ],
            ],
        ];

        // ---- PRE-FLIGHT (yazma yok) ----
        $posts = [];
        $pending = 0;
        $done = 0;
        $problems = [];
        foreach ($spec as $locale => $s) {
            $post = Post::where('slug', $s['slug'])->where('locale', $locale)->first();
            if (! $post) {
                $problems[] = "{$locale}: kayıt bulunamadı ({$s['slug']})";
                continue;
            }
            $posts[$locale] = $post;
            $md = (string) $post->content_md;
            foreach ($s['pairs'] as $i => [$old, $new]) {
                if (str_contains($md, $new)) {
                    $done++;
                } elseif (substr_count($md, $old) === 1) {
                    $pending++;
                } else {
                    $problems[] = "{$locale}: beklenen blok #{$i} bulunamadı veya tekil değil";
                }
            }
            if ($s['excerpt']) {
                [$old, $new] = $s['excerpt'];
                $ex = (string) $post->excerpt;
                if (str_contains($ex, $new)) {
                    $done++;
                } elseif (str_contains($ex, $old)) {
                    $pending++;
                } else {
                    $problems[] = "{$locale}: beklenen excerpt parçası bulunamadı";
                }
            }
        }

        if ($problems) {
            throw new RuntimeException('Exmatrikulation düzeltmesi: ön kontrol başarısız, hiçbir dile yazılmadı. '.implode('; ', $problems));
        }
        if ($pending === 0) {
            return; // zaten uygulanmış — no-op (updated_at'a dokunulmaz)
        }
        if ($done > 0) {
            throw new RuntimeException("Exmatrikulation düzeltmesi: kısmen uygulanmış tutarsız durum ({$done} uygulanmış, {$pending} bekleyen), hiçbir dile yazılmadı.");
        }

        // ---- ATOMİK UYGULAMA ----
        DB::transaction(function () use ($spec, $posts) {
            foreach ($spec as $locale => $s) {
                $post = $posts[$locale];
                $md = (string) $post->content_md;
                foreach ($s['pairs'] as [$old, $new]) {
                    $md = str_replace($old, $new, $md);
                }
                $post->content_md = $md; // Post::booted() content_html'i yeniden üretir
                if ($s['excerpt']) {
                    $post->excerpt = str_replace($s['excerpt'][0], $s['excerpt'][1], (string) $post->excerpt);
                }
                $post->save();
            }
        });
    }

    public function down(): void
    {
        // Bilinçli olarak boş: eski metin doğrulanmamış iddialar içeriyordu.
    }
};

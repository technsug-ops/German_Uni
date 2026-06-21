<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): İş teklifin var → Almanya çalışma vizesi süreci ve gerçek süre (2026).
 *
 * Doğrulandı (Make-it-in-Germany, Auswärtiges Amt, §81a AufenthG, 2026):
 *  - §81a beschleunigtes Fachkräfteverfahren: işveren başlatır (Ausländerbehörde),
 *    ücret 411€; BA onayı 1 hafta (yoksa onaylı sayılır), Vorabzustimmung 3 ay geçerli;
 *    konsolosluk randevusu 3 hafta içinde + karar ~3 hafta → vize kısmı ~4-6 hafta.
 *  - Standart yol: ~6-12 hafta (≈3 ay). Eski "1 yıl" = randevu kuyruğu.
 *  - Hızlanma nedenleri: Konsularportal dijital başvuru (Oca 2025) + remonstrasyon
 *    kaldırılması (Tem 2025). Türkiye'de diğer kategoriler hâlâ iDATA.
 *
 * Yazar: Halil Yaprakli. Kategori: visa-residence. FK-safe + slug-bazlı idempotent.
 * İç-link: çok-dilli (job-seeker/zweckwechsel/anabin) her dilde; consulate/izmir/garantör
 * TR-only → sadece TR'de.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e6f';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'visa-residence')->value('id')
            ?? DB::table('categories')->where('slug', 'vize')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'dan bir iş teklifi aldın ve aklındaki tek soru şu: "Süreç ne kadar sürer?" İyi haber: **eskiden (iDATA randevu kuyruğu yüzünden) yaklaşık bir yıl süren bu süreç, 2024–2025 değişiklikleriyle ciddi şekilde hızlandı.** Kafa karışıklığının sebebi de bu: kimi "3-6 hafta" diyor, kimi "3 ay" — **ikisi de doğru, çünkü iki farklı yol var.**

## Neden hızlandı?
- **Hızlandırılmış Uzman Prosedürü (§81a):** İşveren başlatır, kurumlara süre sınırı koyar.
- **Dijital konsolosluk başvurusu (Konsularportal):** 1 Ocak 2025'ten beri nitelikli iş/öğrenim/aile vizeleri dijital alınabiliyor.
- **Remonstrasyonun (itiraz) kaldırılması:** 1 Temmuz 2025'ten itibaren → vize bölümlerinde personel açıldı, kuyruk kısaldı.

Eski "1 yıl" aslında **randevu beklemesiydi** — bu darboğaz büyük ölçüde çözüldü.

## İki yol, iki süre
| | Hızlandırılmış (§81a) | Standart |
|---|---|---|
| Kim başlatır | **İşveren** (Almanya'da yabancılar dairesinde) | Sen (konsoloslukta) |
| Konsolosluk randevusu | **3 hafta içinde** verilmek zorunda | müsaitliğe bağlı |
| Vize kararı | randevudan sonra **~3 hafta** | değişken |
| Pratik vize süresi | **~4-6 hafta** | **~6-12 hafta (≈3 ay)** |
| Ücret | €411 (genelde işveren öder) | standart vize harcı |

Yani **"3-6 hafta" hızlandırılmış yola, "3 ay" standart yola** denk geliyor.

## Adım adım süreç (iş teklifin hazır)
1. ✅ **İmzalı iş sözleşmesi / teklif** — sende var, en kritik adım tamam.
2. 🔑 **İşverenden §81a'yı başlatmasını iste.** Asıl hız farkı burada. İşveren, senin vekâletinle işyerinin bulunduğu ildeki **yabancılar dairesine (Ausländerbehörde)** başvurur.
3. **Diploma denkliği (Anerkennung)** gerekiyorsa süreçte yapılır (regüle meslekler için şart; IT'de tecrübe yolu olabilir). Bkz: [Anabin ve diploma denkliği](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma).
4. **Federal İş Ajansı (BA) onayı** — bir haftada yanıt gelmezse onaylanmış sayılır.
5. Yabancılar dairesi **ön onay (Vorabzustimmung)** verir — 3 ay geçerli.
6. **Konsularportal'dan dijital başvuru** + İstanbul/İzmir/Ankara'da randevu (diğer kategoriler hâlâ iDATA üzerinden). Bkz: [konsolosluk süreci](/tr/blog/germany-student-visa-consulate-process-before-you-go) · [İzmir Auslandsportal](/tr/blog/izmir-consulate-auslandsportal-digital-diplo-visa-process).
7. **Ulusal D vizesi** başvurusu: belgeler + biyometri → karar.
8. Almanya'ya giriş → ilk haftalarda yerel yabancılar dairesinde **oturum iznine (Aufenthaltstitel)** çevir.

## Pratik ipuçları
- **En büyük kaldıraç işverende:** §81a'yı başlatması, süreci aylardan haftalara indirebilir. İK/hukuk departmanına "beschleunigtes Fachkräfteverfahren" diye sor.
- **Belgeleri eksiksiz hazırla:** randevuya eksik belgeyle gitmek en sık gecikme sebebi.
- **Maaş & nitelik uyumu:** iş, diplomanın seviyesine uygun olmalı; Mavi Kart eşiğini karşılıyorsa süreç daha da net. (İlgili: [öğrenci → çalışma vizesi geçişi](/tr/blog/changing-student-visa-to-work-permit-germany-zweckwechsel).)
- Zaten Almanya'daysan (ör. iş arama vizesiyle), konsolosluğa hiç gitmeden içeride geçiş yapabilirsin. Bkz: [İş Arama Vizesi rehberi](/tr/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates).

## Özet
İş teklifin varsa en hızlı rota **§81a hızlandırılmış prosedür** — işveren başlatırsa vize kısmı **~4-6 hafta**. Standart yolda **~3 ay** beklemek normal. Eski "1 yıl" korkusu artık geçerli değil; randevu darboğazı çözüldü.

---
*2026 itibarıyla yürürlükteki düzenlemeler temel alınmıştır. Süreler temsilciliğe, ile ve dosyanın eksiksizliğine göre değişir — başvurudan önce ilgili Alman temsilciliğinden / yabancılar dairesinden teyit et.*
MD;

        $deBody = <<<'MD'
Du hast ein Jobangebot aus Deutschland und fragst dich nur eines: „Wie lange dauert das Verfahren?" Gute Nachricht: **Was früher (wegen der iDATA-Terminschlange) rund ein Jahr dauerte, hat sich mit den Änderungen 2024–2025 deutlich beschleunigt.** Daher die Verwirrung: manche sagen „3–6 Wochen", andere „3 Monate" — **beides stimmt, weil es zwei Wege gibt.**

## Warum schneller?
- **Beschleunigtes Fachkräfteverfahren (§81a):** vom Arbeitgeber gestartet, mit festen Fristen für die Behörden.
- **Digitaler Visumantrag (Konsularportal):** seit 1. Januar 2025 können Visa für qualifizierte Arbeit/Studium/Familie digital beantragt werden.
- **Abschaffung der Remonstration:** seit 1. Juli 2025 → mehr Personalkapazität in den Visastellen, kürzere Wartezeiten.

Das alte „1 Jahr" war im Kern die **Terminwartezeit** — dieser Engpass ist weitgehend gelöst.

## Zwei Wege, zwei Zeitspannen
| | Beschleunigt (§81a) | Standard |
|---|---|---|
| Wer startet | **Arbeitgeber** (bei der Ausländerbehörde) | du (beim Konsulat) |
| Visumtermin | **innerhalb von 3 Wochen** zu vergeben | je nach Verfügbarkeit |
| Visumentscheidung | **~3 Wochen** nach dem Termin | variabel |
| Praktische Visumdauer | **~4–6 Wochen** | **~6–12 Wochen (≈3 Monate)** |
| Gebühr | 411 € (meist vom Arbeitgeber) | reguläre Visumgebühr |

Also: **„3–6 Wochen" = beschleunigter Weg, „3 Monate" = Standardweg.**

## Schritt für Schritt (Jobangebot liegt vor)
1. ✅ **Unterschriebener Arbeitsvertrag / Angebot** — hast du, der wichtigste Schritt ist erledigt.
2. 🔑 **Bitte deinen Arbeitgeber, das §81a-Verfahren zu starten.** Hier liegt der eigentliche Zeitvorteil. Der Arbeitgeber beantragt es in deiner Vollmacht bei der **Ausländerbehörde** am Betriebsort.
3. **Anerkennung deines Abschlusses**, falls nötig (Pflicht bei reglementierten Berufen; in der IT ggf. über Berufserfahrung). Siehe: [Anabin & Anerkennung](/de/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-de).
4. **Zustimmung der Bundesagentur für Arbeit (BA)** — ohne Antwort binnen einer Woche gilt sie als erteilt.
5. Die Ausländerbehörde erteilt die **Vorabzustimmung** — 3 Monate gültig.
6. **Digitaler Antrag über das Konsularportal** + Termin (andere Kategorien weiter über iDATA).
7. **Nationales Visum (D)**: Unterlagen + Biometrie → Entscheidung.
8. Einreise → in den ersten Wochen bei der örtlichen Ausländerbehörde in einen **Aufenthaltstitel** umwandeln.

## Praktische Tipps
- **Der größte Hebel liegt beim Arbeitgeber:** Startet er §81a, schrumpft das Verfahren von Monaten auf Wochen. Frag HR/Recht nach dem „beschleunigten Fachkräfteverfahren".
- **Vollständige Unterlagen:** mit fehlenden Dokumenten zum Termin zu erscheinen ist der häufigste Verzögerungsgrund.
- **Gehalt & Qualifikation:** die Stelle muss zum Niveau deines Abschlusses passen; erfüllt sie die Blaue-Karte-Schwelle, ist es noch klarer. (Verwandt: [Vom Studium zur Arbeitserlaubnis](/de/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-de).)
- Bist du schon in Deutschland (z. B. mit Jobsuchvisum), kannst du im Inland wechseln. Siehe: [Jobsuchvisum-Leitfaden](/de/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-de).

## Fazit
Mit Jobangebot ist der schnellste Weg das **beschleunigte Fachkräfteverfahren (§81a)** — startet der Arbeitgeber, dauert der Visumteil **~4–6 Wochen**. Auf dem Standardweg sind **~3 Monate** normal. Die alte „1-Jahr"-Angst gilt nicht mehr.

---
*Stand 2026. Die Dauer variiert je nach Auslandsvertretung, Region und Vollständigkeit der Unterlagen — vor der Antragstellung bei der zuständigen deutschen Vertretung / Ausländerbehörde bestätigen.*
MD;

        $enBody = <<<'MD'
You've got a job offer from Germany and just one question: "How long does the process take?" Good news: **what used to take about a year (because of the iDATA appointment queue) has sped up significantly with the 2024–2025 changes.** That's why people disagree: some say "3–6 weeks", others "3 months" — **both are right, because there are two paths.**

## Why faster?
- **Accelerated Skilled Worker Procedure (§81a):** started by the employer, with fixed deadlines for the authorities.
- **Digital consular application (Konsularportal):** since 1 January 2025, visas for skilled work/study/family can be applied for digitally.
- **Abolition of the remonstration (appeal):** since 1 July 2025 → freed staff capacity at visa sections, shorter waits.

The old "1 year" was essentially the **appointment wait** — that bottleneck is largely solved.

## Two paths, two timelines
| | Accelerated (§81a) | Standard |
|---|---|---|
| Who starts it | **Employer** (at the immigration office) | you (at the consulate) |
| Visa appointment | must be given **within 3 weeks** | depends on availability |
| Visa decision | **~3 weeks** after the appointment | variable |
| Practical visa time | **~4–6 weeks** | **~6–12 weeks (≈3 months)** |
| Fee | €411 (usually paid by the employer) | standard visa fee |

So **"3–6 weeks" = the accelerated path, "3 months" = the standard path.**

## Step by step (job offer in hand)
1. ✅ **Signed contract / offer** — you have it; the most critical step is done.
2. 🔑 **Ask your employer to start the §81a procedure.** This is where the real time saving is. The employer applies on your behalf at the **immigration office (Ausländerbehörde)** where the company is located.
3. **Degree recognition (Anerkennung)** if needed (required for regulated professions; in IT possibly via experience). See: [Anabin & recognition](/en/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-en).
4. **Federal Employment Agency (BA) approval** — if there's no reply within a week, it counts as granted.
5. The immigration office issues the **pre-approval (Vorabzustimmung)** — valid 3 months.
6. **Digital application via the Konsularportal** + appointment (other categories still via iDATA).
7. **National (D) visa**: documents + biometrics → decision.
8. Enter Germany → within the first weeks, convert to a **residence permit (Aufenthaltstitel)** at the local immigration office.

## Practical tips
- **The biggest lever is your employer:** if they start §81a, the process shrinks from months to weeks. Ask HR/legal about the "beschleunigtes Fachkräfteverfahren" (accelerated skilled-worker procedure).
- **Complete documents:** showing up to the appointment with missing papers is the most common cause of delay.
- **Salary & qualification match:** the job must match your degree level; meeting the Blue Card threshold makes it even cleaner. (Related: [from study to work permit](/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en).)
- If you're already in Germany (e.g. on a job-seeker visa), you can switch domestically. See: [Job Seeker Visa guide](/en/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-en).

## Bottom line
With a job offer, the fastest route is the **accelerated skilled worker procedure (§81a)** — if the employer starts it, the visa part takes **~4–6 weeks**. On the standard route, **~3 months** is normal. The old "1 year" fear no longer applies.

---
*Based on rules in force as of 2026. Timelines vary by mission, region and document completeness — confirm with the relevant German mission / immigration office before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'germany-work-visa-with-job-offer-process-timeline-fast-track',
                'title' => 'İş Teklifin Var → Almanya Çalışma Vizesi: Adımlar ve Gerçek Süre (2026)',
                'excerpt' => 'İş teklifin varsa Almanya çalışma vizesi ne kadar sürer? Eski "1 yıl" (iDATA kuyruğu) 2024–2025 değişiklikleriyle düştü. Hızlandırılmış Uzman Prosedürü (§81a) ile vize kısmı ~4-6 hafta, standart yolda ~3 ay. Adım adım süreç, "3-6 hafta vs 3 ay" açıklaması, işverenin rolü ve pratik ipuçları.',
                'meta_title' => 'Almanya Çalışma Vizesi Süresi & Adımları — İş Teklifiyle (2026)',
                'meta_description' => 'İş teklifiyle Almanya çalışma vizesi süreci: §81a hızlandırılmış prosedür (~4-6 hafta) vs standart (~3 ay), adım adım ve neden hızlandı (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'germany-work-visa-with-job-offer-process-timeline-fast-track-de',
                'title' => 'Du hast ein Jobangebot → Arbeitsvisum für Deutschland: Schritte & echte Dauer (2026)',
                'excerpt' => 'Wie lange dauert das Arbeitsvisum mit Jobangebot? Das alte „1 Jahr" (iDATA-Terminschlange) ist mit den Änderungen 2024–2025 gefallen. Mit dem beschleunigten Fachkräfteverfahren (§81a) dauert der Visumteil ~4–6 Wochen, auf dem Standardweg ~3 Monate. Schritte, „3–6 Wochen vs 3 Monate", Arbeitgeberrolle und Praxistipps.',
                'meta_title' => 'Arbeitsvisum Deutschland: Dauer & Schritte mit Jobangebot (2026)',
                'meta_description' => 'Arbeitsvisum mit Jobangebot: beschleunigtes Fachkräfteverfahren §81a (~4–6 Wochen) vs Standard (~3 Monate), Schritt für Schritt und warum schneller (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'germany-work-visa-with-job-offer-process-timeline-fast-track-en',
                'title' => 'You Have a Job Offer → Germany Work Visa: Steps & Real Timeline (2026)',
                'excerpt' => 'How long does a German work visa take with a job offer? The old "1 year" (iDATA queue) fell with the 2024–2025 changes. With the accelerated skilled worker procedure (§81a) the visa part takes ~4–6 weeks, the standard route ~3 months. Step-by-step process, "3–6 weeks vs 3 months" explained, the employer\'s role and practical tips.',
                'meta_title' => 'Germany Work Visa Timeline & Steps with a Job Offer (2026)',
                'meta_description' => 'Work visa with a job offer: accelerated skilled worker procedure §81a (~4–6 weeks) vs standard (~3 months), step by step and why it got faster (2026).',
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
            'germany-work-visa-with-job-offer-process-timeline-fast-track',
            'germany-work-visa-with-job-offer-process-timeline-fast-track-de',
            'germany-work-visa-with-job-offer-process-timeline-fast-track-en',
        ])->delete();
    }
};

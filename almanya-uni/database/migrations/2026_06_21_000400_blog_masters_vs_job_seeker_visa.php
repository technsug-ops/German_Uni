<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Master ↔ İş Arama Vizesi köprüsü + Chancenkarte.
 *
 * Hukuki temel doğrulandı (gesetze-im-internet.de §20/§20a + BAMF/Make-it-in-Germany):
 *  - §20: Almanya üni mezunu (Master dahil) → 18 aya kadar iş arama izni; 18 ay ÖTESİNE
 *    uzatılamaz. Mezunlar bu süreçte UNEINGESCHRÄNKT (sınırsız, her iş, tam/yarı zamanlı)
 *    çalışabilir — yurtdışından gelen aday yalnızca deneme/parça işle sınırlıdır.
 *  - §20a Chancenkarte: puan sistemi, 1 yıl arama (20 saat/hafta + 2 hafta deneme);
 *    iş sözleşmesi + BA onayıyla Folge-Chancenkarte olarak 2 yıla kadar uzatılabilir.
 *
 * Yazar: Halil Yaprakli. Kategori: visa-residence. FK-safe + slug-bazlı idempotent.
 * İç-linkler locale-doğru: job-seeker & sperrkonto tüm dillerde mevcut; werkstudent
 * yalnızca TR → DE/EN'de link verilmedi.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e63';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'visa-residence')->value('id')
            ?? DB::table('categories')->where('slug', 'vize')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya, nitelikli iş gücü açığını kapatmak için kapılarını ardına kadar açtı. 2024'te tam olarak yürürlüğe giren yeni nitelikli göçmenlik yasaları (Fırsat Kartı / Chancenkarte dahil), eğitim ve doğrudan iş arama kanallarını eskisinden çok daha entegre hale getirdi.

Aslında aynı hedefe iki farklı kapıdan giren iki aday tipi var:

- **Master öğrencileri:** *geleceğin* nitelikli çalışan adayları.
- **İş arayan vizesi sahipleri:** *bugünün* hazır profesyonelleri.

Bu iki grubu birleştiren en güçlü köprü: **mezuniyet sonrası 18 aylık iş arama izni.** İşte tüm tablo.

## Bölüm 1: Master öğrencileri için "gizli" iş arama vizesi
Almanya'da bir üniversiteden mezun olan herkes (Bachelor, **Master**, doktora), §20 AufenthG uyarınca iş aramak için **18 aya kadar oturum izni** (Aufenthaltserlaubnis zur Arbeitsplatzsuche) alabilir.

**En büyük avantaj — sınırsız çalışma:** Yurtdışından iş arama vizesiyle gelen birinin aksine, **Almanya mezunu bu 18 ay boyunca herhangi bir işte, tam veya yarı zamanlı, sınırsız çalışabilir** (kaynak: BAMF / Make it in Germany). Yani Master yapmak, aslında en güvenli ve en esnek "iş arama vizesi" demek: hem geçimini sağlarsın hem de piyasaya hızlı girersin.

Dikkat:

- İzin **18 ayın ötesine uzatılamaz** (tek seferlik). Ama uzatmana gerek de yok: alanına uygun nitelikli bir iş bulduğunda doğrudan **§18b çalışma iznine / AB Mavi Kartına** geçersin.
- Geçim kanıtı gerekir (bloke hesap mantığı). Detay: [Sperrkonto nedir](/tr/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and).
- Alman diploman zaten "Alman diploması" olduğu için **denklik (anabin) sorunu yaşamazsın** — büyük bir hız avantajı.

İş arama vizesinin tüm detayları için: [Almanya İş Arama Vizesi 2026 — mezunlara tam rehber](/tr/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates).

## Bölüm 2: Doğrudan iş arayanlar için Master diplomasının gücü
Henüz Almanya'da okumamış, **yurtdışından** doğrudan iş aramak isteyenlerin yolu artık **Fırsat Kartı (Chancenkarte, §20a):**

- Puan sistemiyle (nitelik, dil — Almanca A1+ *veya* İngilizce B2, yaş, tecrübe, Almanya bağı) verilen **1 yıllık** arama izni.
- Arama sırasında **haftada 20 saat** çalışma + **2 haftalık deneme** çalışması serbest.
- İş sözleşmesi + İş Ajansı (BA) onayıyla **2 yıla kadar uzatılabilir** (Folge-Chancenkarte).

Burada **yüksek lisans diploması** seni öne çıkarır: puanlamada nitelik üst seviyeden sayılır; üstelik diploman **Almanya'dansa** Alman İK uzmanlarının gözünde prestij + anabin denklik kolaylığı sağlar. Yani aynı diploma hem "iç" hem "dış" yolda kozun olur.

## Bölüm 3: İki kitlenin ortak paydası — kazandıran stratejiler
İster Master yoluyla ister doğrudan gel, Almanya'da iş bulmanın değişmeyen kuralları:

1. **Dil:** Master'ın İngilizce bile olsa, iş piyasasında **Almanca B1–B2 hayati.** Çoğu işveren günlük operasyon için Almanca bekler.
2. **Networking:** **LinkedIn + Xing** profili ve kariyer fuarları (üniversite Career Center etkinlikleri dahil) iş bulmanın en hızlı yolu.
3. **Werkstudent avantajı:** Master öğrencisi okurken alanında [Werkstudent](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market) olarak çalışıp mezuniyette 1–2 yıl tecrübeyle çıkar; doğrudan iş arayan ise piyasaya hazır girer ama yerel referans/tecrübe açığını kapatmalıdır.

## Karşılaştırma: Master yolu vs Doğrudan iş arayan (Chancenkarte)
| Kriter | Master + 18 ay iş arama (§20) | Chancenkarte (§20a) |
|---|---|---|
| Kime | Almanya üni mezunları | Yurtdışındaki nitelikli / puan tutan adaylar |
| Süre | 18 ay (uzatılamaz) | 1 yıl (+2 yıla kadar uzatma) |
| Arama sırası çalışma | **Sınırsız** (her iş, tam/yarı) | Haftada 20 saat + 2 hafta deneme |
| Denklik (anabin) | Gerekmez (Alman diploması) | Genelde gerekir |
| Maliyet / süre | Eğitim süresi + harç (çoğu devlet üni harçsız) | Daha kısa giriş, ama iş bulma riski yüksek |
| Risk | Düşük (zaten ülkedesin, ağın var) | Orta (uzaktan iş bulmak zor) |

## Sonuç ve sıradaki adım
Master öğrencileri *geleceğin* nitelikli çalışanı, iş arayan vizesi sahipleri *bugünün* profesyoneli — ama ikisini de aynı 18 aylık köprü ve aynı nitelikli göç yasası taşıyor. Hangi yol sana uygun? Almanya'da okuma fırsatın varsa Master + 18 ay en güvenli rota; hazır profesyonelsen Chancenkarte hızlı giriş sağlar.

👉 Daha derine inmek için: [İş Arama Vizesi tam rehberi](/tr/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates) · [Öğrenci vizesinden çalışma vizesine geçiş](/tr/blog/changing-student-visa-to-work-permit-germany-zweckwechsel). Sorularını yorumlarda bırak — hangi yolun sana uygun olduğunu birlikte değerlendirelim.

---
*2026 itibarıyla yürürlükteki düzenlemeler temel alınmıştır. Süre, eşik ve uygulama il il değişebilir — başvurudan önce yabancılar dairesinden / resmî kaynaktan teyit et.*
MD;

        $deBody = <<<'MD'
Deutschland hat seine Türen für Fachkräfte weit geöffnet. Die 2024 vollständig in Kraft getretenen Gesetze zur Fachkräfteeinwanderung (inklusive Chancenkarte) haben den Bildungs- und den direkten Jobsuch-Weg enger verzahnt als je zuvor.

Es gibt zwei Profile, die über zwei verschiedene Türen zum selben Ziel gelangen:

- **Master-Studierende:** die Fachkräfte von *morgen*.
- **Jobsuchvisum-Inhaber:** die einsatzbereiten Profis von *heute*.

Die stärkste Brücke zwischen beiden: die **18-monatige Aufenthaltserlaubnis zur Arbeitsplatzsuche nach dem Abschluss.**

## Teil 1: Das „versteckte" Jobsuchvisum für Master-Studierende
Wer an einer deutschen Hochschule einen Abschluss macht (Bachelor, **Master**, Promotion), erhält nach § 20 AufenthG eine **Aufenthaltserlaubnis zur Arbeitsplatzsuche für bis zu 18 Monate.**

**Der größte Vorteil — uneingeschränktes Arbeiten:** Anders als jemand, der mit einem Jobsuchvisum aus dem Ausland kommt, dürfen **Absolventen in diesen 18 Monaten uneingeschränkt arbeiten — jede Tätigkeit, Voll- oder Teilzeit** (Quelle: BAMF / Make it in Germany). Ein Masterstudium ist damit faktisch das sicherste und flexibelste „Jobsuchvisum".

Zu beachten:

- Die Erlaubnis ist **nicht über 18 Monate hinaus verlängerbar** (einmalig). Du brauchst die Verlängerung aber nicht: Sobald du eine passende qualifizierte Stelle findest, wechselst du direkt in eine **Arbeitserlaubnis nach § 18b / Blaue Karte EU.**
- Der Lebensunterhalt muss gesichert sein (Sperrkonto-Logik). Mehr: [Was ist ein Sperrkonto](/de/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-de).
- Dein deutscher Abschluss ist bereits ein „deutscher Abschluss" → **kein Anerkennungsproblem (anabin)** — ein großer Geschwindigkeitsvorteil.

Alle Details zum Jobsuchvisum: [Jobsuchvisum 2026 — kompletter Leitfaden für Absolventen](/de/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-de).

## Teil 2: Die Kraft des Master-Abschlusses für direkte Jobsuchende
Wer noch nicht in Deutschland studiert hat und **aus dem Ausland** direkt eine Stelle sucht, nutzt heute die **Chancenkarte (§ 20a):**

- Über ein Punktesystem (Qualifikation, Sprache — Deutsch A1+ *oder* Englisch B2, Alter, Erfahrung, Deutschland-Bezug) vergebene **einjährige** Suchkarte.
- Während der Suche **20 Stunden/Woche** + eine **zweiwöchige Probebeschäftigung** erlaubt.
- Mit Arbeitsvertrag + Zustimmung der Bundesagentur für Arbeit **bis zu zwei Jahre verlängerbar** (Folge-Chancenkarte).

Ein **Masterabschluss** hebt dich hier hervor: höhere Punktzahl und — wenn der Abschluss **aus Deutschland** stammt — Prestige bei deutschen HR-Verantwortlichen plus einfache anabin-Anerkennung.

## Teil 3: Die gemeinsame Basis beider Gruppen — Erfolgsstrategien
1. **Sprache:** Selbst bei einem englischsprachigen Master ist **Deutsch B1–B2** auf dem Arbeitsmarkt entscheidend.
2. **Networking:** Ein **LinkedIn- + Xing**-Profil, Karrieremessen und Career-Center-Events sind der schnellste Weg.
3. **Werkstudent-Vorteil:** Master-Studierende sammeln schon während des Studiums Branchenerfahrung als Werkstudent; direkte Jobsuchende treten einsatzbereit ein, müssen aber lokale Referenzen aufbauen.

## Vergleich: Master-Weg vs. direkte Jobsuche (Chancenkarte)
| Kriterium | Master + 18 Monate Jobsuche (§ 20) | Chancenkarte (§ 20a) |
|---|---|---|
| Für wen | Absolventen deutscher Hochschulen | Qualifizierte / punktende Personen im Ausland |
| Dauer | 18 Monate (nicht verlängerbar) | 1 Jahr (+ bis zu 2 Jahre Verlängerung) |
| Arbeiten während der Suche | **Uneingeschränkt** (jede Stelle, Voll-/Teilzeit) | 20 Std./Woche + 2 Wochen Probe |
| Anerkennung (anabin) | Nicht nötig (deutscher Abschluss) | Meist nötig |
| Kosten / Zeit | Studiendauer + Gebühren (staatliche Unis meist gebührenfrei) | Schnellerer Einstieg, höheres Jobsuch-Risiko |
| Risiko | Niedrig (bereits im Land, Netzwerk) | Mittel (Jobsuche aus der Ferne) |

## Fazit & nächster Schritt
Master-Studierende sind die Fachkräfte von morgen, Jobsuchvisum-Inhaber die Profis von heute — beide trägt dieselbe 18-Monats-Brücke und dasselbe Fachkräfteeinwanderungsrecht. Hast du die Chance, in Deutschland zu studieren, ist Master + 18 Monate die sicherste Route; bist du bereits Profi, bietet die Chancenkarte den schnellen Einstieg.

👉 Tiefer einsteigen: [Jobsuchvisum — kompletter Leitfaden](/de/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-de) · [Vom Studium zur Arbeitserlaubnis wechseln](/de/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-de). Stell deine Fragen in den Kommentaren.

---
*Stand 2026. Dauer, Schwellen und Praxis können regional variieren — vor der Antragstellung bei der Ausländerbehörde / offiziellen Stelle bestätigen.*
MD;

        $enBody = <<<'MD'
Germany has opened its doors wide to skilled workers. The skilled-immigration laws that came fully into force in 2024 (including the Chancenkarte / Opportunity Card) have woven the education route and the direct job-search route together more tightly than ever.

Two profiles enter the same goal through two different doors:

- **Master's students:** the skilled workers of *tomorrow*.
- **Job-seeker-visa holders:** the ready-to-go professionals of *today*.

The strongest bridge between them: the **18-month post-graduation job-search residence permit.** Here's the full picture.

## Part 1: the "hidden" job-seeker visa for Master's students
Anyone who graduates from a German university (Bachelor's, **Master's**, doctorate) can obtain — under § 20 AufenthG — a **residence permit to look for work for up to 18 months** (Aufenthaltserlaubnis zur Arbeitsplatzsuche).

**The biggest advantage — unrestricted work:** unlike someone arriving from abroad on a job-seeker visa, **graduates may work without restriction during these 18 months — any job, full- or part-time** (source: BAMF / Make it in Germany). That effectively makes doing a Master's the safest and most flexible "job-seeker visa" there is.

Note:

- The permit **cannot be extended beyond 18 months** (one-off). But you won't need to: once you find a matching qualified job, you switch directly to a **§18b work permit / EU Blue Card.**
- You must prove your livelihood (the blocked-account logic). More: [What is a Sperrkonto](/en/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-en).
- Your German degree is already a "German degree" → **no recognition (anabin) hurdle** — a big speed advantage.

For the full details of the job-seeker visa: [Germany Job Seeker Visa 2026 — complete guide for graduates](/en/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-en).

## Part 2: the power of a Master's degree for direct job-seekers
Those who haven't studied in Germany yet and want to look for a job **from abroad** now use the **Chancenkarte (Opportunity Card, §20a):**

- A **one-year** search card granted via a points system (qualification, language — German A1+ *or* English B2, age, experience, ties to Germany).
- During the search, **20 hours/week** + a **two-week trial** employment are allowed.
- With a job contract + Federal Employment Agency approval it can be **extended up to two more years** (Folge-Chancenkarte).

A **Master's degree** stands out here: it scores at the higher qualification tier, and — if the degree is **from Germany** — it carries prestige with German HR plus easy anabin recognition. The same diploma becomes your trump card on both the "inside" and "outside" routes.

## Part 3: common ground for both groups — winning strategies
1. **Language:** even with an English-taught Master's, **German B1–B2 is vital** on the job market. Most employers expect German for day-to-day operations.
2. **Networking:** a **LinkedIn + Xing** profile, career fairs and Career-Center events are the fastest route.
3. **The Werkstudent advantage:** Master's students gain industry experience while studying (as a Werkstudent); direct job-seekers arrive ready but must build local references.

## Comparison: the Master's route vs. the direct job-seeker (Chancenkarte)
| Criterion | Master's + 18-month search (§20) | Chancenkarte (§20a) |
|---|---|---|
| For whom | Graduates of German universities | Qualified / points-qualifying people abroad |
| Duration | 18 months (not extendable) | 1 year (+ up to 2 years' extension) |
| Work during search | **Unrestricted** (any job, full/part-time) | 20 hours/week + 2-week trial |
| Recognition (anabin) | Not needed (German degree) | Usually needed |
| Cost / time | Study duration + fees (most public unis fee-free) | Faster entry, but higher job-search risk |
| Risk | Low (already in country, network built) | Medium (job-hunting from afar) |

## Conclusion & next step
Master's students are the skilled workers of tomorrow; job-seeker-visa holders are the professionals of today — but both are carried by the same 18-month bridge and the same skilled-immigration law. If you have the chance to study in Germany, a Master's + 18 months is the safest route; if you're already a professional, the Chancenkarte offers the fast entry.

👉 Go deeper: [Job Seeker Visa full guide](/en/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-en) · [From student visa to work permit](/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en). Drop your questions in the comments.

---
*Based on rules in force as of 2026. Duration, thresholds and practice can vary by region — confirm with the immigration office / official source before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'germany-masters-vs-job-seeker-visa-two-keys-career',
                'title' => 'Almanya\'da Kariyerin İki Anahtarı: Master mı, İş Arayan Vizesi mi? (2026)',
                'excerpt' => 'Almanya\'da Master ile iş arayan vizesi aynı köprüde buluşuyor: mezuniyet sonrası 18 aylık iş arama izni (§20) ve sınırsız çalışma hakkı, Chancenkarte (§20a) puan sistemi ve uzatma, Master diplomasının gücü, ortak stratejiler (Almanca B1-B2, LinkedIn/Xing, Werkstudent) ve karşılaştırma tablosu.',
                'meta_title' => 'Master mı İş Arayan Vizesi mi? Almanya Kariyer Rehberi 2026',
                'meta_description' => 'Almanya master sonrası çalışma izni vs Fırsat Kartı (Chancenkarte): 18 aylık iş arama vizesi, sınırsız çalışma, puan sistemi ve kariyer stratejileri (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'germany-masters-vs-job-seeker-visa-two-keys-career-de',
                'title' => 'Zwei Schlüssel für deine Karriere in Deutschland: Master oder Jobsuchvisum? (2026)',
                'excerpt' => 'Master-Studium und Jobsuchvisum treffen sich auf derselben Brücke: die 18-monatige Aufenthaltserlaubnis zur Arbeitsplatzsuche (§20) mit uneingeschränktem Arbeiten, die Chancenkarte (§20a) mit Punktesystem und Verlängerung, die Kraft des Masterabschlusses, gemeinsame Strategien (Deutsch B1-B2, LinkedIn/Xing, Werkstudent) und eine Vergleichstabelle.',
                'meta_title' => 'Master oder Jobsuchvisum? Karriere in Deutschland 2026',
                'meta_description' => 'Arbeiten nach dem Master vs. Chancenkarte: 18-Monats-Jobsuche, uneingeschränktes Arbeiten, Punktesystem und Karrierestrategien in Deutschland (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'germany-masters-vs-job-seeker-visa-two-keys-career-en',
                'title' => 'Two Keys to Your Career in Germany: a Master\'s or the Job Seeker Visa? (2026)',
                'excerpt' => 'A Master\'s degree and the job seeker visa meet on the same bridge: the 18-month post-study job-search permit (§20) with unrestricted work, the Chancenkarte (§20a) points system and extension, the power of a Master\'s diploma, shared strategies (German B1-B2, LinkedIn/Xing, Werkstudent) and a comparison table.',
                'meta_title' => 'Master\'s or Job Seeker Visa? Germany Career Guide 2026',
                'meta_description' => 'Working after a Master\'s vs the Chancenkarte (Opportunity Card): the 18-month job-search permit, unrestricted work, points system and career strategies (2026).',
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
            'germany-masters-vs-job-seeker-visa-two-keys-career',
            'germany-masters-vs-job-seeker-visa-two-keys-career-de',
            'germany-masters-vs-job-seeker-visa-two-keys-career-en',
        ])->delete();
    }
};

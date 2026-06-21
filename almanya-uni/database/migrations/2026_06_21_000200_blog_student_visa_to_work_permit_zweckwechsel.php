<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Öğrenci vizesinden çalışma vizesine geçiş (Zweckwechsel).
 *
 * Hukuki temel doğrulandı (gesetze-im-internet.de §16b/§18a/§18b/§18g):
 *  - §16b(4): 16.08.2023 + 01.03.2024 reformlarıyla Zweckwechselverbot büyük ölçüde
 *    kalktı; sadece §19c(1) geçici istihdam geçişi yasak. Fachkraft/§16a/Mavi Kart
 *    şartlar tutarsa mezun olmadan da mümkün.
 *  - §18b: Alman VEYA denkliği tanınan yabancı üniversite diploması + nitelikli iş.
 *  - Mavi Kart §18g 2026 eşik: genel 50.700 €, indirimli (Engpass + yeni mezun) 45.934,20 €.
 *
 * Yazar: Halil Yaprakli. Kategori: visa-residence. FK-safe + slug-bazlı idempotent.
 * Üç locale tek translation_group_id altında (sabit UUID — re-run'da gruplanma korunur).
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e61';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'visa-residence')->value('id')
            ?? DB::table('categories')->where('slug', 'vize')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'ya üniversite okumak için gelen birçok öğrenci şunu merak eder: "Diplomam elime geçmeden, öğrenci oturum iznimi çalışma vizesine çevirebilir miyim?" Cevap eskiden büyük ölçüde "hayır"dı; ama **2023–2024 reformuyla kurallar köklü biçimde değişti.** İşte güncel, doğru tablo.

## Önce temel kural: "Fachkraft" (nitelikli iş gücü) ne demek?
Almanya çalışma göçünün belkemiği **nitelikli iş gücü (Fachkraft)** ilkesidir. İki ana yol var:

- **§18b — Akademik nitelikli iş gücü:** Alman bir üniversite diploman *veya* **denkliği tanınan / Alman diplomasıyla denk sayılan yabancı bir üniversite diploman** + alanına uygun nitelikli bir iş teklifin olmalı.
- **§18a — Mesleki nitelikli iş gücü:** Tanınan bir meslek eğitimi diploman + uygun iş.
- **AB Mavi Kart (§18g):** Üniversite diploması + iş + maaş eşiği (2026: genel **50.700 €**, darboğaz meslekler ve son 3 yılda mezun olanlar için indirimli **45.934,20 €**).

Yani çalışma vizesine geçmenin kalbi tek soruda düğümlenir: **Elinde, seni "nitelikli" yapan tamamlanmış bir diploma var mı?**

## Eski kural vs yeni kural (önemli düzeltme)
Eskiden öğrenci oturum izniyle (§16b) okurken başka amaçlı izne **geçiş büyük ölçüde yasaktı** ("Zweckwechselverbot").

**16 Ağustos 2023 ve 1 Mart 2024 reformlarıyla bu yasak neredeyse tamamen kalktı.** Bugün §16b(4) yalnızca tek bir geçişi yasaklıyor: **§19c(1) kapsamındaki *geçici* istihdam izinleri** (au-pair, gönüllü hizmet vb.). Bunun dışında — nitelikli iş gücü (§18a/§18b), Mavi Kart, meslek eğitimi (§16a) — **şartları taşıyorsan, mezun olmadan da geçiş hukuken mümkün hale geldi.**

Yani artık engel "yasak" değil; engel **niteliğe sahip olup olmamak.**

## Senaryo 1: Lisans (Bachelor) için gelen, başka diploması olmayan öğrenci
Henüz tamamlanmış bir diploman yok → seni Fachkraft yapacak bir nitelik de yok. Dolayısıyla pratikte **çalışma vizesine geçemezsin** — yasak olduğu için değil, *henüz nitelikli sayılmadığın için.* Doğru yol: **lisansını bitir**, sonra mezun olarak doğrudan §18b'ye (veya 18 aylık iş arama iznine, §20) geç.

## Senaryo 2: Türkiye'de zaten üniversite mezunu olup Almanya'ya okumaya gelen kişi (istisna)
İşte kritik istisna. **Türkiye'den tanınan/denk sayılan bir üniversite diploman varsa**, bu eski diploman seni *şimdiden* "akademik nitelikli iş gücü" yapar. Alanına uygun nitelikli bir iş teklifi bulursan, Almanya'daki öğrenciliğini bitirmeden **§18b çalışma iznine / Mavi Karta geçebilirsin.** (Diplomanın denkliği için anabin / Zeugnisbewertung kontrolü şart.)

## Senaryo 3: Yüksek lisans (Master) için gelenler
Master için geldiysen **zaten bir lisans diploman var** demektir. O lisans (tanınıyorsa) seni Fachkraft yapmaya yeter. Bu yüzden uygun bir iş teklifiyle, Master'ı bitirmeden bile öğrenci oturum iznini **çalışma vizesine çevirebilirsin** — yine maaş eşiği ve diploma–iş uyumu şartıyla.

## Pratikte dikkat
- Hukuk liberalleşti ama **yabancılar dairesi (Ausländerbehörde) uygulamaları il il değişir;** mezuniyet sonrası geçiş her zaman en temiz yoldur.
- İş teklifi **diplomanın seviyesine ve alanına uygun** olmalı; asgari maaş eşiklerini karşılamalı.
- Diploma denkliği (anabin) sürecini erken başlat.

---
*2026 itibarıyla yürürlükteki düzenlemeler temel alınmıştır. Eşik tutarları ve uygulama değişebilir — başvurudan önce yabancılar dairesinden / resmî kaynaktan teyit et.*
MD;

        $deBody = <<<'MD'
Viele, die zum Studium nach Deutschland kommen, fragen sich: „Kann ich meine Aufenthaltserlaubnis zum Studium in eine Arbeitserlaubnis umwandeln, bevor ich meinen Abschluss habe?" Früher lautete die Antwort meist „nein" — doch mit der Reform 2023–2024 hat sich die Rechtslage grundlegend geändert. Hier das aktuelle, korrekte Bild.

## Grundprinzip: Was heißt „Fachkraft"?
Das Rückgrat der deutschen Erwerbsmigration ist das Fachkräfteprinzip. Zwei Hauptwege:

- **§18b — Fachkraft mit akademischer Ausbildung:** ein deutscher Hochschulabschluss *oder* ein **anerkannter bzw. einem deutschen Abschluss vergleichbarer ausländischer Hochschulabschluss** + ein passendes qualifiziertes Stellenangebot.
- **§18a — Fachkraft mit Berufsausbildung:** eine anerkannte Berufsausbildung + passende Stelle.
- **Blaue Karte EU (§18g):** Hochschulabschluss + Job + Gehaltsschwelle (2026: allgemein **50.700 €**, für Engpassberufe und Berufseinsteiger innerhalb von 3 Jahren nach dem Abschluss ermäßigt **45.934,20 €**).

Im Kern geht es also um eine Frage: **Hast du einen abgeschlossenen Abschluss, der dich zur Fachkraft macht?**

## Alte vs. neue Regel (wichtige Korrektur)
Früher war während des Studiums (§16b) ein Zweckwechsel weitgehend untersagt („Zweckwechselverbot"). **Mit den Reformen vom 16.08.2023 und 01.03.2024 ist dieses Verbot fast vollständig entfallen.** Heute verbietet §16b Abs. 4 nur noch einen einzigen Wechsel: zu Aufenthaltserlaubnissen für **vorübergehende Beschäftigung nach §19c Abs. 1** (z. B. Au-pair, Freiwilligendienst). Alles andere — Fachkraft (§18a/§18b), Blaue Karte, Berufsausbildung (§16a) — ist bei Erfüllung der Voraussetzungen **auch ohne Studienabschluss möglich.**

Das Hindernis ist also nicht mehr ein „Verbot", sondern die **Qualifikation selbst.**

## Szenario 1: Bachelor-Studierende ohne weiteren Abschluss
Du hast noch keinen abgeschlossenen Abschluss → keine Qualifikation als Fachkraft. Daher kannst du praktisch **nicht** in eine Arbeitserlaubnis wechseln — nicht wegen eines Verbots, sondern weil du noch nicht als Fachkraft giltst. Der richtige Weg: **Studium abschließen** und dann als Absolvent direkt §18b (oder die 18-monatige Arbeitsplatzsuche, §20).

## Szenario 2: Wer in der Türkei bereits einen Hochschulabschluss hat (Ausnahme)
Hier die entscheidende Ausnahme: Hast du **bereits einen anerkannten ausländischen Hochschulabschluss** (z. B. aus der Türkei), macht dich dieser *schon jetzt* zur akademischen Fachkraft. Mit einem passenden qualifizierten Stellenangebot kannst du **noch vor Ende deines Studiums** in eine Arbeitserlaubnis nach §18b / Blaue Karte wechseln. (Anerkennung über anabin / Zeugnisbewertung erforderlich.)

## Szenario 3: Master-Studierende
Für ein Masterstudium hast du **bereits einen Bachelor**. Dieser (sofern anerkannt) genügt, um als Fachkraft zu gelten. Mit einem passenden Jobangebot kannst du daher selbst vor dem Masterabschluss deine studienbezogene Aufenthaltserlaubnis **in eine Arbeitserlaubnis umwandeln** — wieder unter Beachtung von Gehaltsschwelle und Abschluss-Job-Passung.

## In der Praxis
- Die Rechtslage wurde liberalisiert, doch **die Praxis der Ausländerbehörden ist regional unterschiedlich;** der Wechsel nach dem Abschluss ist immer der sauberste Weg.
- Das Stellenangebot muss **Niveau und Fachrichtung deines Abschlusses** entsprechen und die Gehaltsschwellen erfüllen.
- Starte die Anerkennung (anabin) frühzeitig.

---
*Stand 2026. Schwellenwerte und Verwaltungspraxis können sich ändern — vor der Antragstellung bei der Ausländerbehörde / offiziellen Stelle bestätigen.*
MD;

        $enBody = <<<'MD'
Many students who come to Germany to study ask: "Can I convert my student residence permit into a work permit before I graduate?" The old answer was mostly "no" — but the 2023–2024 reform changed the rules fundamentally. Here is the accurate, up-to-date picture.

## First principle: what is a "Fachkraft" (skilled worker)?
The backbone of German labour migration is the skilled-worker principle. Two main routes:

- **§18b — skilled worker with academic training:** a German university degree *or* a **recognised foreign degree comparable to a German one** + a matching qualified job offer.
- **§18a — skilled worker with vocational training:** a recognised vocational qualification + a suitable job.
- **EU Blue Card (§18g):** university degree + job + salary threshold (2026: general **€50,700**; reduced **€45,934.20** for shortage occupations and recent graduates within 3 years of graduating).

So it all comes down to one question: **do you hold a completed qualification that makes you a skilled worker?**

## Old rule vs. new rule (important correction)
Previously, while on a study residence permit (§16b), switching purpose was largely prohibited (the "Zweckwechselverbot"). **With the reforms of 16 August 2023 and 1 March 2024 this ban was almost entirely removed.** Today §16b(4) bans only one switch: to permits for **temporary employment under §19c(1)** (e.g. au-pair, voluntary service). Everything else — skilled worker (§18a/§18b), Blue Card, vocational training (§16a) — is **now legally possible even before graduating, provided you meet the requirements.**

So the obstacle is no longer a "ban" — it's **whether you hold the qualification.**

## Scenario 1: a Bachelor's student with no other degree
You don't yet hold a completed qualification → nothing makes you a skilled worker. So in practice you **cannot** switch to a work permit — not because it's banned, but because you don't yet qualify. The right path: **finish your degree**, then switch directly to §18b as a graduate (or to the 18-month job-search permit, §20).

## Scenario 2: someone who already holds a Turkish university degree (the exception)
Here's the key exception. If you **already hold a recognised foreign university degree** (e.g. from Turkey), that earlier degree *already* makes you an academic skilled worker. With a matching qualified job offer you can switch to a §18b work permit / Blue Card **even before finishing your studies in Germany.** (Recognition via the anabin database / Zeugnisbewertung is required.)

## Scenario 3: Master's students
If you came for a Master's, you **already hold a Bachelor's degree.** That degree (if recognised) is enough to count as a skilled worker. So with a suitable job offer you can convert your study residence permit **into a work permit even before finishing the Master's** — again subject to salary thresholds and a degree–job match.

## In practice
- The law has been liberalised, but **immigration-office (Ausländerbehörde) practice varies by region;** switching after graduation is always the cleanest route.
- The job offer must match the **level and field of your degree** and meet the salary thresholds.
- Start the degree-recognition (anabin) process early.

---
*Based on rules in force as of 2026. Thresholds and administrative practice can change — confirm with the immigration office / official source before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'changing-student-visa-to-work-permit-germany-zweckwechsel',
                'title' => 'Öğrenci Vizesinden Çalışma Vizesine Geçiş: Kim Yapabilir, Kim Yapamaz? (2026)',
                'excerpt' => 'Almanya\'da öğrenci oturum iznini çalışma vizesine çevirmek: 2023–2024 reformuyla Zweckwechselverbot kalktı (sadece §19c(1) geçici iş hariç). Bachelor öğrencisi neden geçemez, Türkiye diploması olan ve Master öğrencisi neden geçebilir — Fachkraft (§18a/§18b), Mavi Kart eşikleri ve pratik uyarılar.',
                'meta_title' => 'Öğrenci Vizesini Çalışma Vizesine Çevirme — Almanya 2026',
                'meta_description' => 'Öğrenci oturum iznini çalışma vizesine geçirme: yeni Zweckwechsel kuralı, eski diploma istisnası, Master mezunu yolu ve §18b/Mavi Kart şartları (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'changing-student-visa-to-work-permit-germany-zweckwechsel-de',
                'title' => 'Vom Studentenvisum zur Arbeitserlaubnis in Deutschland: Wer kann wechseln? (2026)',
                'excerpt' => 'Aufenthaltserlaubnis zum Studium in eine Arbeitserlaubnis umwandeln: Mit der Reform 2023–2024 ist das Zweckwechselverbot fast entfallen (nur §19c(1) bleibt). Warum Bachelor-Studierende (noch) nicht wechseln können, während Personen mit ausländischem Abschluss und Master-Studierende es können — Fachkraft (§18a/§18b), Blaue-Karte-Schwellen und Praxis.',
                'meta_title' => 'Studium zu Arbeitserlaubnis wechseln — Deutschland 2026',
                'meta_description' => 'Zweckwechsel vom Studium zur Arbeit: neue Regel, Ausnahme bei vorhandenem Abschluss, Master-Weg und §18b/Blaue-Karte-Voraussetzungen (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'changing-student-visa-to-work-permit-germany-zweckwechsel-en',
                'title' => 'From Student Visa to Work Permit in Germany: Who Can Switch? (2026)',
                'excerpt' => 'Converting a study residence permit into a work permit: the 2023–2024 reform removed almost all of the Zweckwechselverbot (only §19c(1) temporary work remains). Why Bachelor\'s students can\'t (yet) switch, while those with a foreign degree and Master\'s students can — skilled worker (§18a/§18b), Blue Card thresholds and practical notes.',
                'meta_title' => 'Switch Student Visa to Work Permit — Germany 2026',
                'meta_description' => 'Changing purpose from study to work: the new rule, the existing-degree exception, the Master\'s route and §18b/Blue Card requirements (2026).',
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
            'changing-student-visa-to-work-permit-germany-zweckwechsel',
            'changing-student-visa-to-work-permit-germany-zweckwechsel-de',
            'changing-student-visa-to-work-permit-germany-zweckwechsel-en',
        ])->delete();
    }
};

<?php

use App\Models\Faq;
use App\Models\FaqTopic;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Dil kursu vizesi (§ 16f AufenthG) — 5 SSS × 3 dil (TR/EN/DE).
 *
 * Kaynak: topluluktan gelen "dil kursu vizesiyle Münih/Augsburg'da kurs arıyorum"
 * sorusu. Sitede § 16f hakkında hiçbir içerik yoktu.
 *
 * Doğrulanmış veriler (Eylül 2026):
 *   - § 16f Abs. 1: studienvorbereitend OLMAYAN dil kursu için oturum izni.
 *   - § 16f Abs. 3 c. 4 (kanun metninden birebir): "Die Aufenthaltserlaubnis nach
 *     Absatz 1 zur Teilnahme an einem Sprachkurs berechtigt nur zur Ausübung einer
 *     Beschäftigung von bis zu 20 Stunden je Woche." → ÇALIŞMA İZNİ VAR, hafta 20 saat.
 *     (Schüleraustausch ve Schulbesuch için çalışma YASAK — aynı fıkra c. 5.)
 *   - Amaç değişikliği yasağı (Abs. 3 c. 1–2) yalnızca Schulbesuch (Abs. 2) ve
 *     Schüleraustausch sonrası için geçerli; SPRACHKURS için kanunda böyle bir
 *     genel yasak YOK. Uygulama yine de Ausländerbehörde'ye bağlı.
 *   - Auswärtiges Amt: yoğun kurs, her gün ders, haftada en az 18 ders saati;
 *     akşam/hafta sonu kursları kabul edilmez. Süre: 3 aydan uzun, en fazla 1 yıl.
 *   - Türkiye bilgi notu 44F: haftada en az 18 ders saati; geçim için BAföG azami
 *     tutarının %10 fazlası; 365 günü kapsayan Incoming sağlık sigortası; vize
 *     ücreti 75 € (çocuk 40 €). Bloke hesap 2026: 1.091 €/ay = 13.092 €/yıl
 *     (yükseköğrenime hazırlık kursunda 992 €/ay = 11.904 €/yıl).
 *
 * Idempotent: slug varsa atlar. faq_topic_id topic SLUG'ından çözülür.
 */
return new class extends Migration
{
    public function up(): void
    {
        $topicIds = FaqTopic::pluck('id', 'slug')->all();
        $existing = DB::table('faqs')->pluck('slug')->flip();

        $inserted = 0;
        $skipped  = 0;

        Faq::unguard();

        foreach ($this->faqs() as $item) {
            $topicId = $topicIds[$item['topic_slug']] ?? null;

            if (! $topicId) {
                $skipped += count($item['translations']);

                continue;
            }

            $groupId = (string) Str::uuid();

            foreach ($item['translations'] as $locale => $t) {
                $slug = $locale === 'tr' ? $item['slug'] : $item['slug'] . '-' . $locale;

                if (isset($existing[$slug])) {
                    $skipped++;

                    continue;
                }

                $faq = new Faq();
                $faq->forceFill([
                    'faq_topic_id'         => $topicId,
                    'translation_group_id' => $groupId,
                    'locale'               => $locale,
                    'question'             => $t['question'],
                    'slug'                 => $slug,
                    'answer_md'            => trim($t['answer_md']),
                    'intent'               => 'community',
                    'category'             => 'visa',
                    'has_answer'           => true,
                    'is_published'         => true,
                    'sort_order'           => $item['sort_order'],
                ]);
                $faq->save();

                $existing[$slug] = true;
                $inserted++;
            }
        }

        Faq::reguard();

        echo "language-course visa FAQs: +{$inserted} eklendi, {$skipped} atlandı\n";
    }

    public function down(): void
    {
        DB::table('faqs')->whereIn('slug', $this->allSlugs())->delete();
    }

    /** @return array<string> */
    private function allSlugs(): array
    {
        $slugs = [];
        foreach ($this->faqs() as $item) {
            foreach (array_keys($item['translations']) as $locale) {
                $slugs[] = $locale === 'tr' ? $item['slug'] : $item['slug'] . '-' . $locale;
            }
        }

        return $slugs;
    }

    private function faqs(): array
    {
        $srcLaw  = 'https://www.gesetze-im-internet.de/aufenthg_2004/__16f.html';
        $srcAA   = 'https://jakarta.diplo.de/id-de/service/visa-und-einreise/2582220-2582220';
        $src44F  = 'https://tuerkei.diplo.de/tr-tr/service/05-visaeinreise/2769658-2769658';
        $src36F  = 'https://tuerkei.diplo.de/tr-de/service/05-visaeinreise/2628310-2628310';

        return [
            [
                'slug'       => 'german-language-course-visa-requirements-16f',
                'topic_slug' => 'vize',
                'sort_order' => 20,
                'translations' => [
                    'tr' => [
                        'question'  => 'Almanya dil kursu vizesi (§ 16f) şartları neler?',
                        'answer_md' => <<<MD
Dil kursu vizesi, **§ 16f AufenthG** uyarınca yükseköğrenime hazırlık amacı taşımayan Almanca kursları için veriliyor. En kritik şart kursun kendisiyle ilgili:

- **Yoğun kurs olmalı** — her gün ders, **haftada en az 18 ders saati**
- **Akşam ve hafta sonu kursları kabul edilmiyor**
- Süre: 3 aydan uzun, **en fazla 1 yıl**

Bu şart çoğu başvurunun takıldığı yer. Online platformlar (Babbel, Lingoda gibi), kendi kendine çalışma programları ve Volkshochschule'nin akşam kursları bu tanıma uymuyor — vize için geçerli değiller.

Türkiye'den başvuruda istenen belgeler (Bilgi Notu 44F):

- Başvuru formu, biyometrik fotoğraf, pasaport
- **Motivasyon yazısı** ve özgeçmiş
- **Dil kursuna kayıt belgesi**
- Eğitim ve meslek belgeleri, varsa dil sertifikası
- Geçim güvencesi: BAföG azami tutarının **%10 fazlası** (bloke hesap ya da Verpflichtungserklärung)
- **365 günü kapsayan** özel sağlık sigortası (Incoming Sigortası)
- Vize ücreti 75 € (çocuklarda 40 €)

İşlem süresi Türkiye'de yaklaşık 8 hafta. Kursu seçerken haftalık ders saatini okulun kendisinden yazılı olarak teyit et — 18 saatin altındaysa başvuru baştan reddedilir.

Kaynaklar: [§ 16f AufenthG]({$srcLaw}) · [Auswärtiges Amt — dil kursu vizesi]({$srcAA}) · [Türkiye Bilgi Notu 44F]({$src44F})

*Bu içerik genel bilgilendirme amaçlıdır, hukuki danışmanlık değildir.*
MD,
                    ],
                    'en' => [
                        'question'  => 'What are the requirements for a German language course visa (§ 16f)?',
                        'answer_md' => <<<MD
The language course visa is granted under **§ 16f of the German Residence Act** for German courses that do **not** serve to prepare for university study. The decisive condition concerns the course itself:

- It must be an **intensive course** — daily instruction, **at least 18 teaching hours per week**
- **Evening and weekend courses are not accepted**
- Duration: more than three months, **up to a maximum of one year**

This is where most applications fail. Online platforms (such as Babbel or Lingoda), self-study programmes and evening classes at a Volkshochschule do not meet the definition and will not support a visa.

Documents required when applying from Turkey (information sheet 44F):

- Application form, biometric photo, passport
- **Motivation letter** and CV
- **Proof of enrolment** in the language course
- Educational and professional certificates, plus any language certificate
- Proof of funds: the BAföG maximum rate **plus 10%** (blocked account or a formal declaration of support)
- Private health insurance covering the **full 365 days** (so-called incoming insurance)
- Visa fee of €75 (€40 for children)

Processing in Turkey takes roughly eight weeks. Ask the school to confirm the weekly teaching hours in writing before you book — below 18 hours the application fails at the first hurdle.

Sources: [§ 16f Residence Act]({$srcLaw}) · [Federal Foreign Office — language course visa]({$srcAA}) · [Turkey information sheet 44F]({$src44F})

*This is general information, not legal advice.*
MD,
                    ],
                    'de' => [
                        'question'  => 'Welche Voraussetzungen gelten für das Sprachkursvisum (§ 16f AufenthG)?',
                        'answer_md' => <<<MD
Das Sprachkursvisum wird nach **§ 16f AufenthG** für Deutschkurse erteilt, die **nicht** der Studienvorbereitung dienen. Entscheidend ist der Kurs selbst:

- Es muss ein **Intensivkurs** sein — täglicher Unterricht, **mindestens 18 Unterrichtsstunden pro Woche**
- **Abend- und Wochenendkurse werden nicht anerkannt**
- Dauer: über drei Monate, **höchstens ein Jahr**

Daran scheitern die meisten Anträge. Online-Plattformen (etwa Babbel oder Lingoda), Selbstlernprogramme und Abendkurse der Volkshochschule erfüllen die Definition nicht.

Erforderliche Unterlagen bei Antragstellung in der Türkei (Merkblatt 44F):

- Antragsformular, biometrisches Foto, Reisepass
- **Motivationsschreiben** und Lebenslauf
- **Anmeldebestätigung** des Sprachkurses
- Bildungs- und Berufsnachweise, ggf. Sprachzertifikat
- Lebensunterhalt: BAföG-Höchstsatz **zuzüglich 10 %** (Sperrkonto oder Verpflichtungserklärung)
- Private Krankenversicherung für die **gesamten 365 Tage** (Incoming-Versicherung)
- Visumgebühr 75 € (Kinder 40 €)

Die Bearbeitung dauert in der Türkei etwa acht Wochen. Lassen Sie sich die wöchentliche Stundenzahl vor der Buchung schriftlich von der Schule bestätigen.

Quellen: [§ 16f AufenthG]({$srcLaw}) · [Auswärtiges Amt — Sprachkursvisum]({$srcAA}) · [Merkblatt 44F Türkei]({$src44F})

*Allgemeine Information, keine Rechtsberatung.*
MD,
                    ],
                ],
            ],

            [
                'slug'       => 'can-i-work-on-a-german-language-course-visa',
                'topic_slug' => 'is',
                'sort_order' => 21,
                'translations' => [
                    'tr' => [
                        'question'  => 'Dil kursu vizesiyle Almanya\'da çalışabilir miyim?',
                        'answer_md' => <<<MD
**Evet — haftada 20 saate kadar.** Bu, internette sık sık yanlış yazılan bir konu, oysa kanun metninde açıkça yer alıyor.

§ 16f Abs. 3 AufenthG, kelimesi kelimesine:

> *"Die Aufenthaltserlaubnis nach Absatz 1 zur Teilnahme an einem Sprachkurs berechtigt nur zur Ausübung einer Beschäftigung von bis zu 20 Stunden je Woche."*

Yani dil kursu için verilen oturum izni, **haftada 20 saati aşmamak kaydıyla çalışmaya yetki veriyor**. "Nur" kelimesi yasak değil, üst sınır anlamında.

**Aynı maddedeki istisnalara dikkat:** öğrenci değişimi (Schüleraustausch) ve okul eğitimi (§ 16f Abs. 2) için verilen oturum izinleri çalışmaya **hiç yetki vermiyor**. Üç farklı durum aynı maddede düzenlendiği için karıştırılıyor.

Pratikte iki şeyi unutma:

1. Ders yükün zaten haftada en az 18 saat — üstüne 20 saat çalışmak yoğun bir program demek, ve vize kursa devam şartına bağlı.
2. Vize başvurusunda geçimini **çalışarak** karşılayacağını söyleyemezsin; bloke hesap ya da Verpflichtungserklärung yine de şart. Çalışma, geçim güvencesinin yerine geçmiyor.

Kaynak: [§ 16f AufenthG — kanun metni]({$srcLaw})

*Bu içerik genel bilgilendirme amaçlıdır, hukuki danışmanlık değildir.*
MD,
                    ],
                    'en' => [
                        'question'  => 'Can I work in Germany on a language course visa?',
                        'answer_md' => <<<MD
**Yes — up to 20 hours a week.** This is frequently reported incorrectly online, yet it is stated plainly in the statute.

§ 16f (3) of the Residence Act, word for word:

> *"Die Aufenthaltserlaubnis nach Absatz 1 zur Teilnahme an einem Sprachkurs berechtigt nur zur Ausübung einer Beschäftigung von bis zu 20 Stunden je Woche."*

("A residence permit under subsection 1 for participation in a language course entitles the holder to employment of up to 20 hours per week.") The word *nur* sets a ceiling; it is not a prohibition.

**Note the contrast inside the same provision:** permits issued for a pupil exchange or for school attendance (§ 16f (2)) carry **no** right to work at all. Because all three cases sit in one section, they are often confused.

Two practical points:

1. Your course already runs at a minimum of 18 hours a week. Adding 20 hours of work is demanding, and your permit depends on attending the course.
2. At the visa stage you cannot rely on future earnings to prove your living costs — a blocked account or a formal declaration of support is still required. Work does not replace that proof.

Source: [§ 16f Residence Act — full text]({$srcLaw})

*This is general information, not legal advice.*
MD,
                    ],
                    'de' => [
                        'question'  => 'Darf ich mit einem Sprachkursvisum in Deutschland arbeiten?',
                        'answer_md' => <<<MD
**Ja — bis zu 20 Stunden pro Woche.** Das wird online häufig falsch dargestellt, steht aber ausdrücklich im Gesetz.

§ 16f Abs. 3 AufenthG, im Wortlaut:

> *„Die Aufenthaltserlaubnis nach Absatz 1 zur Teilnahme an einem Sprachkurs berechtigt nur zur Ausübung einer Beschäftigung von bis zu 20 Stunden je Woche."*

Das Wort „nur" setzt eine Obergrenze, es ist kein Verbot.

**Der Kontrast in derselben Vorschrift:** Aufenthaltserlaubnisse für einen Schüleraustausch und für den Schulbesuch (§ 16f Abs. 2) berechtigen **gar nicht** zur Erwerbstätigkeit. Weil alle drei Fälle in einer Norm stehen, werden sie oft verwechselt.

Zwei praktische Hinweise:

1. Der Kurs umfasst bereits mindestens 18 Wochenstunden. 20 Stunden Arbeit zusätzlich sind anspruchsvoll, und die Aufenthaltserlaubnis hängt an der Kursteilnahme.
2. Im Visumverfahren kann der Lebensunterhalt nicht mit künftigem Arbeitseinkommen nachgewiesen werden — Sperrkonto oder Verpflichtungserklärung bleiben erforderlich.

Quelle: [§ 16f AufenthG — Gesetzestext]({$srcLaw})

*Allgemeine Information, keine Rechtsberatung.*
MD,
                    ],
                ],
            ],

            [
                'slug'       => 'which-german-language-course-visa-44f-36f-43f',
                'topic_slug' => 'vize',
                'sort_order' => 22,
                'translations' => [
                    'tr' => [
                        'question'  => 'Hangi dil kursu vizesine başvurmalıyım? (44F, 36F, 43F farkı)',
                        'answer_md' => <<<MD
Almanya'nın Türkiye temsilcilikleri dil kursu için **üç ayrı** bilgi notu yayımlıyor. Yanlışını seçmek, başvurunun reddine ya da Almanya'da amacını değiştirememeye yol açıyor.

| Bilgi notu | Ne için | Yasal dayanak |
|---|---|---|
| **44F** | Genel dil kursu — üniversite ya da mesleki eğitim hedefi **yok** | § 16f AufenthG |
| **36F** | **Yükseköğrenime hazırlık** dil kursu (üniversite hedefi var) | § 16b AufenthG |
| **43F** | **Mesleki eğitime (Ausbildung) hazırlık** dil kursu | — |

**Nasıl seçilir:** Hedefin Almanya'da üniversite okumaksa **44F değil 36F** senin yolun. 44F "studienvorbereitend olmayan" kurslar için; yani en baştan üniversite hedefini belirtmeden gelirsen, sonradan öğrenci oturumuna geçiş zorlaşır.

**Bloke hesap tutarları da farklı** (2026):

- Genel dil kursu (44F): **1.091 €/ay** = 13.092 €/yıl
- Yükseköğrenime hazırlık (36F): **992 €/ay** = 11.904 €/yıl

Aradaki fark tesadüf değil: 44F'de geçim güvencesi BAföG azami tutarının **%10 fazlası** olarak hesaplanıyor.

Üniversiteye gitmeyi düşünüyorsan, kurs kaydını yapmadan önce hangi bilgi notuna göre başvuracağına karar ver. Bu sıralama sonradan düzeltilmesi en zor hatalardan biri.

Kaynaklar: [Bilgi Notu 44F — Dil Kursu]({$src44F}) · [Bilgi Notu 36F — Yükseköğrenime Hazırlık Dil Kursu]({$src36F})

*Bu içerik genel bilgilendirme amaçlıdır, hukuki danışmanlık değildir.*
MD,
                    ],
                    'en' => [
                        'question'  => 'Which German language course visa should I apply for? (44F, 36F, 43F)',
                        'answer_md' => <<<MD
The German missions in Turkey publish **three separate** information sheets for language courses. Choosing the wrong one leads to refusal, or to being unable to switch purpose once you are in Germany.

| Sheet | Purpose | Legal basis |
|---|---|---|
| **44F** | General language course — **no** university or vocational goal | § 16f Residence Act |
| **36F** | **Study-preparatory** language course (university is the goal) | § 16b Residence Act |
| **43F** | Language course preparing for **vocational training** (Ausbildung) | — |

**How to choose:** if your goal is to study at a German university, your route is **36F, not 44F**. Sheet 44F covers courses that are expressly *not* study-preparatory, so arriving without declaring a study goal makes a later switch to a student permit harder.

**The blocked account amounts differ too** (2026):

- General language course (44F): **€1,091/month** = €13,092/year
- Study-preparatory (36F): **€992/month** = €11,904/year

The gap is not arbitrary: for 44F the required funds are calculated as the BAföG maximum rate **plus 10%**.

If university is on your horizon, decide which sheet applies before you enrol in a course. This is one of the hardest sequencing mistakes to undo later.

Sources: [Information sheet 44F — language course]({$src44F}) · [Information sheet 36F — study-preparatory language course]({$src36F})

*This is general information, not legal advice.*
MD,
                    ],
                    'de' => [
                        'question'  => 'Welches Sprachkursvisum ist das richtige? (44F, 36F, 43F)',
                        'answer_md' => <<<MD
Die deutschen Auslandsvertretungen in der Türkei veröffentlichen **drei getrennte** Merkblätter für Sprachkurse. Das falsche zu wählen führt zur Ablehnung oder dazu, dass ein späterer Zweckwechsel in Deutschland scheitert.

| Merkblatt | Wofür | Rechtsgrundlage |
|---|---|---|
| **44F** | Allgemeiner Sprachkurs — **kein** Studien- oder Ausbildungsziel | § 16f AufenthG |
| **36F** | **Studienvorbereitender** Sprachkurs (Studium ist das Ziel) | § 16b AufenthG |
| **43F** | **Ausbildungsvorbereitender** Sprachkurs | — |

**Auswahl:** Wer in Deutschland studieren will, braucht **36F, nicht 44F**. Merkblatt 44F gilt ausdrücklich für Kurse, die *nicht* der Studienvorbereitung dienen.

**Auch die Sperrkonto-Beträge unterscheiden sich** (2026):

- Allgemeiner Sprachkurs (44F): **1.091 €/Monat** = 13.092 €/Jahr
- Studienvorbereitend (36F): **992 €/Monat** = 11.904 €/Jahr

Der Unterschied ist systematisch: Bei 44F wird der Lebensunterhalt als BAföG-Höchstsatz **zuzüglich 10 %** berechnet.

Wer ein Studium plant, sollte die Zuordnung vor der Kursbuchung klären — dieser Fehler lässt sich später nur schwer korrigieren.

Quellen: [Merkblatt 44F — Sprachkurs]({$src44F}) · [Merkblatt 36F — Studienvorbereitender Sprachkurs]({$src36F})

*Allgemeine Information, keine Rechtsberatung.*
MD,
                    ],
                ],
            ],

            [
                'slug'       => 'switching-language-course-visa-to-student-visa-germany',
                'topic_slug' => 'vize',
                'sort_order' => 23,
                'translations' => [
                    'tr' => [
                        'question'  => 'Dil kursu vizesi öğrenci oturumuna çevrilebilir mi?',
                        'answer_md' => <<<MD
Kanunda **dil kursu için genel bir amaç değiştirme yasağı yok** — ama bu "kolayca çevrilir" anlamına gelmiyor. İkisini ayırmak önemli.

**Kanun ne diyor:** § 16f Abs. 3 AufenthG'deki amaç değiştirme kısıtı iki duruma bağlanmış:

- **Okul eğitimi** (Abs. 2) sırasında: başka amaçlı oturum izni kural olarak yalnızca yasal hak varsa verilir
- **Öğrenci değişimi** sonrasında: aynı kısıt

Dil kursu (Abs. 1) bu iki cümlenin kapsamında **sayılmamış**. Yani Schulbesuch ve Schüleraustausch için açık olan yasak, dil kursu için kanunda yazmıyor.

**Pratikte ne oluyor:** Karar Ausländerbehörde'nin. Öğrenci oturumuna geçmek için § 16b şartlarını sağlaman gerekir — üniversite kabulü, geçim güvencesi, sigorta. Bazı Ausländerbehörde'ler geçişe izin verir, bazıları vizenin veriliş amacına dayanarak yurt dışından yeniden başvuru ister. Şehirden şehire değişiyor.

**Doğru yol baştan seçmek:** Hedefin üniversiteyse zaten **yükseköğrenime hazırlık dil kursu vizesiyle** (Bilgi Notu 36F, § 16b) gelmelisin. O vize, dil kursunu ve sonrasındaki öğrenciliği tek bir amaç zinciri olarak kapsıyor; sonradan çevirmeye çalışmak gereksiz risk.

Zaten 44F ile geldiysen: üniversite kabulünü aldıktan sonra bulunduğun şehrin Ausländerbehörde'sine geçişin mümkün olup olmadığını **yazılı olarak** sor, planını ona göre yap.

Kaynak: [§ 16f AufenthG — kanun metni]({$srcLaw})

*Bu içerik genel bilgilendirme amaçlıdır, hukuki danışmanlık değildir.*
MD,
                    ],
                    'en' => [
                        'question'  => 'Can a language course visa be converted into a student residence permit?',
                        'answer_md' => <<<MD
The statute contains **no general ban on changing purpose after a language course** — but that does not mean the switch is easy. The two things are worth separating.

**What the law says:** the restriction in § 16f (3) of the Residence Act is tied to two situations:

- During a stay for **school attendance** (subsection 2): a permit for a different purpose is as a rule granted only where there is a statutory entitlement
- **After a pupil exchange**: the same restriction

A language course (subsection 1) is **not** covered by either sentence. The explicit bar that exists for school attendance and pupil exchange is simply not written for language courses.

**What happens in practice:** the decision rests with the local immigration office. To move to a student permit you must meet the § 16b requirements — university admission, proof of funds, insurance. Some offices allow the switch; others point to the purpose the visa was issued for and require a fresh application from abroad. Practice varies from city to city.

**The clean route is to choose correctly from the start:** if university is your goal, come on the **study-preparatory language course visa** (information sheet 36F, § 16b). That permit covers the language course and the subsequent studies as one chain of purpose, so there is nothing to convert.

If you are already in Germany on a 44F visa, ask your local immigration office **in writing** whether a switch is possible once you hold an admission letter, and plan around their answer.

Source: [§ 16f Residence Act — full text]({$srcLaw})

*This is general information, not legal advice.*
MD,
                    ],
                    'de' => [
                        'question'  => 'Kann ein Sprachkursvisum in eine Aufenthaltserlaubnis zum Studium umgewandelt werden?',
                        'answer_md' => <<<MD
Das Gesetz enthält **kein generelles Zweckwechselverbot für Sprachkurse** — was jedoch nicht bedeutet, dass der Wechsel einfach ist.

**Was im Gesetz steht:** Die Beschränkung in § 16f Abs. 3 AufenthG knüpft an zwei Fälle an:

- Während eines Aufenthalts zum **Schulbesuch** (Abs. 2): Aufenthaltserlaubnis zu anderem Zweck in der Regel nur bei gesetzlichem Anspruch
- **Im Anschluss an einen Schüleraustausch**: dieselbe Beschränkung

Der Sprachkurs (Abs. 1) wird von beiden Sätzen **nicht erfasst**.

**In der Praxis:** Zuständig ist die Ausländerbehörde. Für den Wechsel müssen die Voraussetzungen des § 16b erfüllt sein — Zulassung, gesicherter Lebensunterhalt, Versicherung. Manche Behörden lassen den Wechsel zu, andere verweisen auf den Zweck, zu dem das Visum erteilt wurde, und verlangen eine erneute Antragstellung vom Ausland aus. Die Praxis ist uneinheitlich.

**Der saubere Weg ist die richtige Wahl von Anfang an:** Wer studieren möchte, reist mit dem **studienvorbereitenden Sprachkursvisum** ein (Merkblatt 36F, § 16b). Dieses deckt Sprachkurs und anschließendes Studium als eine Zweckkette ab.

Wer bereits mit einem 44F-Visum in Deutschland ist, sollte die zuständige Ausländerbehörde **schriftlich** fragen, ob ein Wechsel nach Vorliegen der Zulassung möglich ist.

Quelle: [§ 16f AufenthG — Gesetzestext]({$srcLaw})

*Allgemeine Information, keine Rechtsberatung.*
MD,
                    ],
                ],
            ],

            [
                'slug'       => 'blocked-account-amount-german-language-course-visa',
                'topic_slug' => 'para',
                'sort_order' => 24,
                'translations' => [
                    'tr' => [
                        'question'  => 'Dil kursu vizesi için bloke hesapta ne kadar para olmalı?',
                        'answer_md' => <<<MD
2026 için Türkiye'den yapılan başvurularda geçerli tutarlar:

| Kurs türü | Aylık | Yıllık |
|---|---|---|
| **Genel dil kursu** (Bilgi Notu 44F, § 16f) | **1.091 €** | **13.092 €** |
| **Yükseköğrenime hazırlık kursu** (36F, § 16b) | **992 €** | **11.904 €** |

**Neden farklı:** Genel dil kursunda temsilcilik, geçim güvencesini BAföG azami tutarının **%10 fazlası** olarak hesaplıyor. Yükseköğrenime hazırlık kursunda standart öğrenci tutarı uygulanıyor.

**İki seçenek var:**

1. **Bloke hesap (Sperrkonto)** — Alman bankasında açılır, aylık sabit tutar çekebilirsin
2. **Verpflichtungserklärung** — Almanya'da bir kişinin taahhüdü; belgede "Bonität nachgewiesen" ibaresi ve kalış amacı olarak "Sprachkurs" yazılı olmalı

Vizen 1 yıla kadar veriliyorsa tutar tam yıl üzerinden istenir; daha kısa kurslarda orantılı hesaplanır.

**Sık yapılan hata:** dil kursu için öğrenci tutarını (992 €) yatırmak. Aradaki 99 €/ay fark bir yılda 1.188 € ediyor ve eksik bloke hesap başvurunun reddine yeter. Başvurmadan önce ilgili temsilciliğin güncel bilgi notundan rakamı teyit et — tutarlar BAföG oranlarıyla birlikte her yıl değişebiliyor.

Kaynak: [Bilgi Notu 44F — Dil Kursu]({$src44F})

*Bu içerik genel bilgilendirme amaçlıdır, hukuki danışmanlık değildir.*
MD,
                    ],
                    'en' => [
                        'question'  => 'How much money must be in the blocked account for a language course visa?',
                        'answer_md' => <<<MD
The amounts applying to applications from Turkey in 2026:

| Course type | Monthly | Annual |
|---|---|---|
| **General language course** (sheet 44F, § 16f) | **€1,091** | **€13,092** |
| **Study-preparatory course** (36F, § 16b) | **€992** | **€11,904** |

**Why they differ:** for a general language course the mission calculates the required funds as the BAföG maximum rate **plus 10%**. Study-preparatory courses use the standard student rate.

**Two ways to prove funds:**

1. **Blocked account (Sperrkonto)** — opened at a German bank; you may withdraw a fixed amount each month
2. **Verpflichtungserklärung** — a formal declaration by a sponsor in Germany; it must state "Bonität nachgewiesen" and name "Sprachkurs" as the purpose of stay

If the visa is issued for up to a year, the full annual sum is required; for shorter courses it is calculated pro rata.

**A common mistake** is depositing the student rate (€992) for a language course. The €99 monthly gap is €1,188 over a year, and an underfunded account is enough for a refusal. Confirm the figure on your mission's current information sheet before applying — the amounts track the BAföG rates and change from year to year.

Source: [Information sheet 44F — language course]({$src44F})

*This is general information, not legal advice.*
MD,
                    ],
                    'de' => [
                        'question'  => 'Wie viel Geld muss für das Sprachkursvisum auf dem Sperrkonto liegen?',
                        'answer_md' => <<<MD
Für Anträge aus der Türkei gelten 2026 folgende Beträge:

| Kursart | Monatlich | Jährlich |
|---|---|---|
| **Allgemeiner Sprachkurs** (Merkblatt 44F, § 16f) | **1.091 €** | **13.092 €** |
| **Studienvorbereitender Kurs** (36F, § 16b) | **992 €** | **11.904 €** |

**Grund für den Unterschied:** Beim allgemeinen Sprachkurs berechnet die Vertretung den Lebensunterhalt als BAföG-Höchstsatz **zuzüglich 10 %**. Für studienvorbereitende Kurse gilt der Standardsatz für Studierende.

**Zwei Nachweiswege:**

1. **Sperrkonto** — bei einer deutschen Bank eröffnet, monatlich fester Auszahlungsbetrag
2. **Verpflichtungserklärung** — mit dem Vermerk „Bonität nachgewiesen" und „Sprachkurs" als Aufenthaltszweck

Wird das Visum für bis zu einem Jahr erteilt, ist der volle Jahresbetrag nachzuweisen; bei kürzeren Kursen anteilig.

**Häufiger Fehler:** der Studierendensatz (992 €) für einen Sprachkurs. Die Differenz von 99 € monatlich ergibt 1.188 € im Jahr — ein zu gering ausgestattetes Sperrkonto genügt für eine Ablehnung. Vor Antragstellung den aktuellen Betrag im Merkblatt der Vertretung prüfen.

Quelle: [Merkblatt 44F — Sprachkurs]({$src44F})

*Allgemeine Information, keine Rechtsberatung.*
MD,
                    ],
                ],
            ],
        ];
    }
};

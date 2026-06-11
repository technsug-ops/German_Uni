<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Brief → blog: "Öğrenci vizesi görüşmesi" tr/en/de (3 yazı, tek group).
 * content_html boş → blog:render-html doldurur. Kategori 8, idempotent.
 */
return new class extends Migration
{
    private string $group = 'a1b2c3d4-0003-4000-8000-000000000003';

    private array $slugs = [
        'ogrenci-vizesi-gorusmesi-sorular-hazirlik',
        'student-visa-interview-questions-preparation',
        'visumsgespraech-studierende-fragen-vorbereitung',
    ];

    public function up(): void
    {
        $now = now();
        $catId = DB::table('categories')->where('id', 8)->exists() ? 8 : null;

        $tr = <<<'MD'
# Öğrenci Vizesi Görüşmesi: Sık Sorulan Sorular ve Hazırlık

> **30 saniyelik özet:** Alman öğrenci vizesi görüşmesi kısa (5–15 dk) ve genelde nazik geçer; amaç gerçek bir öğrenci olduğunu ve maddi olarak kendine yetebileceğini görmek. En kritik üçlü: **kabul mektubu, Sperrkonto/finansal kanıt ve ikna edici bir "neden Almanya, neden bu program" cevabı.** Belgelerin eksiksizse görüşme bir formalitedir.

---

## Görüşme neyi ölçer?

Konsolosluk üç şeyi teyit etmek ister:

1. **Gerçek öğrenci misin?** — kabul, plan ve programla uyumlu geçmiş.
2. **Kendini finanse edebiliyor musun?** — güncel Sperrkonto tutarı, burs veya taahhütname.
3. **Niyetin net mi?** — neden bu program, mezuniyet sonrası planın.

## Sık sorulan sorular

- Neden Almanya'da okumak istiyorsun?
- Neden bu üniversite ve bu program?
- Bu programın içeriği ne, hangi dersler var?
- Almanca/İngilizce seviyen ne? (program diline göre)
- Eğitimini nasıl finanse edeceksin?
- Mezun olunca ne yapmayı planlıyorsun?
- Almanya'da tanıdığın var mı, daha önce gittin mi?

Sorular "tuzak" değildir; tutarlı, dürüst ve programınla uyumlu cevaplar beklenir.

## Olmazsa olmaz belgeler

- Geçerli pasaport + biyometrik fotoğraf
- Üniversite **kabul mektubu** (veya koşullu kabul / başvuru kanıtı)
- **Finansal kanıt:** güncel Sperrkonto açılış belgesi, burs veya Verpflichtungserklärung
- Sağlık sigortası kanıtı
- Diploma + transkript (gerekirse VPD)
- Dil sertifikası (program diline göre)
- Vize başvuru formu + ücret

> Belge listesi konsolosluğa göre küçük farklılıklar gösterir — randevu öncesi resmi siteden teyit et.

## Yaygın ret sebepleri ve çözümleri

- **Yetersiz finansal kanıt:** Sperrkonto tutarı güncel eşiği karşılamalı; eksik tutar en sık redlerdendir. Güncel rakamı doğrula.
- **Tutarsız niyet:** "Neden bu program?" sorusuna geçmişinle uyumlu, somut cevap ver. Genel-geçer cevaplar şüphe yaratır.
- **Eksik/eski belge:** Kabul, sigorta ve dil sertifikan güncel ve eksiksiz olsun.
- **Dil–program uyumsuzluğu:** Almanca programa İngilizce sertifikayla gelme; program diline uygun kanıt sun.

## Hazırlık ipuçları

- Cevaplarını **ezberleme, anla** — doğal ve tutarlı konuş.
- Programın müfredatından 2–3 ders adını bil.
- Mezuniyet sonrası planını tek net cümleyle anlat.
- Belgeleri sıralı bir dosyada götür; istenen belgeyi saniyede çıkar.

> İlgili: uni-assist/VPD ve Sperrkonto rehberlerimiz, görüşmeye gelene kadarki tüm adımları kapatır.
MD;

        $en = <<<'MD'
# Student Visa Interview: Common Questions and How to Prepare

> **30-second summary:** The German student visa interview is short (5–15 min) and usually polite; the goal is to confirm you are a genuine student who can support themselves financially. The critical trio: **your admission letter, Sperrkonto/proof of funds, and a convincing "why Germany, why this programme" answer.** If your documents are complete, the interview is a formality.

---

## What does the interview assess?

The consulate wants to confirm three things:

1. **Are you a genuine student?** — admission, a plan, and a background consistent with the programme.
2. **Can you finance yourself?** — the current Sperrkonto amount, a scholarship, or a sponsorship declaration.
3. **Is your intent clear?** — why this programme, and your plan after graduation.

## Common questions

- Why do you want to study in Germany?
- Why this university and this programme?
- What does the programme cover — which courses?
- What is your German/English level? (depending on the programme language)
- How will you finance your studies?
- What do you plan to do after graduating?
- Do you know anyone in Germany, have you been before?

The questions aren't "traps"; consistent, honest answers that match your programme are expected.

## Must-have documents

- Valid passport + biometric photo
- University **admission letter** (or conditional admission / proof of application)
- **Proof of funds:** current Sperrkonto opening confirmation, a scholarship, or a Verpflichtungserklärung
- Proof of health insurance
- Diploma + transcript (VPD if required)
- Language certificate (per the programme language)
- Visa application form + fee

> The document list varies slightly by consulate — confirm on the official site before your appointment.

## Common rejection reasons and fixes

- **Insufficient proof of funds:** The Sperrkonto amount must meet the current threshold; a shortfall is among the most common rejections. Verify the current figure.
- **Inconsistent intent:** Answer "why this programme?" with something concrete and consistent with your background. Generic answers raise doubt.
- **Missing/outdated documents:** Keep your admission, insurance and language certificate current and complete.
- **Language–programme mismatch:** Don't apply to a German-taught programme with only an English certificate; provide proof matching the programme language.

## Preparation tips

- Don't **memorise** your answers — understand them, so you speak naturally and consistently.
- Know 2–3 course names from the curriculum.
- State your post-graduation plan in one clear sentence.
- Bring documents in an ordered folder; produce any requested item in seconds.

> Related: our uni-assist/VPD and Sperrkonto guides cover every step leading up to the interview.
MD;

        $de = <<<'MD'
# Visumsgespräch für Studierende: Häufige Fragen und Vorbereitung

> **Zusammenfassung in 30 Sekunden:** Das deutsche Studentenvisum-Gespräch ist kurz (5–15 Min.) und meist freundlich; Ziel ist zu bestätigen, dass du ein echter Studierender bist und dich finanziell selbst tragen kannst. Das entscheidende Trio: **Zulassungsbescheid, Sperrkonto/Finanzierungsnachweis und eine überzeugende „Warum Deutschland, warum dieser Studiengang"-Antwort.** Sind deine Unterlagen vollständig, ist das Gespräch eine Formsache.

---

## Was prüft das Gespräch?

Das Konsulat will drei Dinge bestätigen:

1. **Bist du ein echter Studierender?** — Zulassung, ein Plan und ein zum Studiengang passender Hintergrund.
2. **Kannst du dich finanzieren?** — der aktuelle Sperrkonto-Betrag, ein Stipendium oder eine Verpflichtungserklärung.
3. **Ist deine Absicht klar?** — warum dieser Studiengang und dein Plan nach dem Abschluss.

## Häufige Fragen

- Warum möchtest du in Deutschland studieren?
- Warum diese Hochschule und dieser Studiengang?
- Was umfasst der Studiengang — welche Kurse?
- Wie ist dein Deutsch-/Englischniveau? (je nach Studiensprache)
- Wie finanzierst du dein Studium?
- Was planst du nach dem Abschluss?
- Kennst du jemanden in Deutschland, warst du schon dort?

Die Fragen sind keine „Fallen"; erwartet werden konsistente, ehrliche und zum Studiengang passende Antworten.

## Unverzichtbare Dokumente

- Gültiger Reisepass + biometrisches Foto
- **Zulassungsbescheid** der Hochschule (oder bedingte Zulassung / Bewerbungsnachweis)
- **Finanzierungsnachweis:** aktuelle Sperrkonto-Eröffnungsbestätigung, ein Stipendium oder eine Verpflichtungserklärung
- Nachweis der Krankenversicherung
- Zeugnis + Notenübersicht (VPD falls erforderlich)
- Sprachzertifikat (je nach Studiensprache)
- Visumantrag + Gebühr

> Die Dokumentenliste variiert leicht je nach Konsulat — prüfe sie vor dem Termin auf der offiziellen Seite.

## Häufige Ablehnungsgründe und Lösungen

- **Unzureichender Finanzierungsnachweis:** Der Sperrkonto-Betrag muss die aktuelle Schwelle erreichen; ein Fehlbetrag gehört zu den häufigsten Ablehnungen. Prüfe die aktuelle Zahl.
- **Inkonsistente Absicht:** Beantworte „Warum dieser Studiengang?" konkret und passend zu deinem Hintergrund. Allgemeine Antworten wecken Zweifel.
- **Fehlende/veraltete Dokumente:** Halte Zulassung, Versicherung und Sprachzertifikat aktuell und vollständig.
- **Sprache–Studiengang-Missverhältnis:** Bewirb dich nicht mit nur einem Englischzertifikat auf einen deutschsprachigen Studiengang; liefere einen zur Studiensprache passenden Nachweis.

## Vorbereitungstipps

- **Lerne deine Antworten nicht auswendig** — verstehe sie, damit du natürlich und konsistent sprichst.
- Kenne 2–3 Kursnamen aus dem Curriculum.
- Formuliere deinen Plan nach dem Abschluss in einem klaren Satz.
- Bring die Dokumente in einer geordneten Mappe mit; gib jedes verlangte Dokument in Sekunden heraus.

> Verwandt: Unsere uni-assist/VPD- und Sperrkonto-Leitfäden decken jeden Schritt bis zum Gespräch ab.
MD;

        $rows = [
            ['locale' => 'tr', 'slug' => $this->slugs[0],
                'title' => 'Öğrenci Vizesi Görüşmesi: Sık Sorulan Sorular ve Hazırlık',
                'excerpt' => 'Alman öğrenci vizesi görüşmesinde ne sorulur, hangi belgeler şart, en sık ret sebepleri ve görüşmeye nasıl hazırlanılır — adım adım rehber.',
                'content_md' => $tr, 'reading_minutes' => 5],
            ['locale' => 'en', 'slug' => $this->slugs[1],
                'title' => 'Student Visa Interview: Common Questions and How to Prepare',
                'excerpt' => 'What you are asked in the German student visa interview, which documents are essential, the most common rejection reasons, and how to prepare — a step-by-step guide.',
                'content_md' => $en, 'reading_minutes' => 5],
            ['locale' => 'de', 'slug' => $this->slugs[2],
                'title' => 'Visumsgespräch für Studierende: Häufige Fragen und Vorbereitung',
                'excerpt' => 'Was im deutschen Studentenvisum-Gespräch gefragt wird, welche Dokumente unverzichtbar sind, die häufigsten Ablehnungsgründe und wie du dich vorbereitest.',
                'content_md' => $de, 'reading_minutes' => 5],
        ];

        foreach ($rows as $r) {
            if (DB::table('posts')->where('slug', $r['slug'])->where('locale', $r['locale'])->exists()) {
                continue;
            }
            DB::table('posts')->insert([
                'locale' => $r['locale'],
                'translation_group_id' => $this->group,
                'type' => 'blog',
                'category_id' => $catId,
                'title' => $r['title'],
                'slug' => $r['slug'],
                'excerpt' => $r['excerpt'],
                'content_md' => $r['content_md'],
                'content_html' => null,
                'reading_minutes' => $r['reading_minutes'],
                'meta_title' => $r['title'],
                'meta_description' => $r['excerpt'],
                'is_published' => true,
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('posts')->where('translation_group_id', $this->group)->delete();
    }
};

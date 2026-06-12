<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 4. parti şablon (premium içerik): Arbeitszeugnis talebi, Widerspruch (resmi
 * karara itiraz — BAföG/vize/kabul), Krankmeldung, Urlaubssemester başvurusu.
 * Idempotent (slug). Widerspruch örnek metindir — hukuki danışmanlık değil.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('document_templates')) {
            return;
        }
        $now = now();
        foreach ($this->templates() as $t) {
            $t['placeholders'] = json_encode($t['placeholders'], JSON_UNESCAPED_UNICODE);
            DB::table('document_templates')->updateOrInsert(
                ['slug' => $t['slug']],
                array_merge($t, ['updated_at' => $now, 'created_at' => $now])
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('document_templates')) {
            DB::table('document_templates')->whereIn('slug', [
                'arbeitszeugnis-anfrage', 'widerspruch-bescheid', 'krankmeldung', 'urlaubssemester-antrag',
            ])->delete();
        }
    }

    private function templates(): array
    {
        $zeugnis = <<<'TXT'
Betreff: Bitte um ein qualifiziertes Arbeitszeugnis


Sehr geehrte/r [ANSPRECHPARTNER],

zum [ENDDATUM] endet meine Tätigkeit als [POSITION] bei [UNTERNEHMEN]. Für meine
weitere berufliche Laufbahn bitte ich Sie um ein qualifiziertes Arbeitszeugnis.

Über eine wohlwollende Beurteilung meiner Tätigkeiten und Leistungen würde ich
mich sehr freuen. Falls es hilfreich ist, stelle ich Ihnen gerne eine Übersicht
meiner Aufgaben und Projekte zur Verfügung:
[AUFGABEN_UEBERSICHT]

Bitte senden Sie mir das Zeugnis bis zum [WUNSCHDATUM] zu. Für Rückfragen stehe
ich gerne zur Verfügung.

Vielen Dank im Voraus.

Mit freundlichen Grüßen
[VORNAME] [NACHNAME]
[EMAIL]  ·  [TELEFON]
TXT;

        $widerspruch = <<<'TXT'
[VORNAME] [NACHNAME]
[ADRESSE], [PLZ] [STADT]

[BEHOERDE_NAME]
[BEHOERDE_ADRESSE]

[ORT], [DATUM]


Betreff: Widerspruch gegen den Bescheid vom [BESCHEID_DATUM], Az. [AKTENZEICHEN]


Sehr geehrte Damen und Herren,

gegen den oben genannten Bescheid vom [BESCHEID_DATUM], mir zugegangen am
[ZUGANG_DATUM], lege ich hiermit fristgerecht

                              W i d e r s p r u c h

ein.

Begründung:
[BEGRUENDUNG]

Ich bitte Sie, den Bescheid zu überprüfen und [FORDERUNG]. Ergänzende Unterlagen
reiche ich gerne nach.

Über eine zeitnahe Rückmeldung würde ich mich freuen.

Mit freundlichen Grüßen


_______________________
[VORNAME] [NACHNAME]
TXT;

        $krank = <<<'TXT'
Betreff: Krankmeldung – [VORNAME] [NACHNAME]


Sehr geehrte/r [ANSPRECHPARTNER],

leider bin ich erkrankt und kann heute, den [DATUM], nicht zur [ARBEIT_ODER_UNI]
kommen. Voraussichtlich werde ich [DAUER] ausfallen.

[ATTEST_HINWEIS]

Sollten Sie weitere Informationen benötigen, melde ich mich selbstverständlich.

Vielen Dank für Ihr Verständnis und freundliche Grüße
[VORNAME] [NACHNAME]
TXT;

        $urlaub = <<<'TXT'
Betreff: Antrag auf ein Urlaubssemester für das [SEMESTER]


Sehr geehrte Damen und Herren,

hiermit beantrage ich für das [SEMESTER] ein Urlaubssemester in meinem
Studiengang [STUDIENGANG].

Matrikelnummer: [MATRIKELNUMMER]
Grund: [GRUND]

[NACHWEIS_HINWEIS]

Bitte teilen Sie mir mit, ob weitere Unterlagen erforderlich sind und welche
Auswirkungen das Urlaubssemester auf meine Einschreibung hat.

Vielen Dank im Voraus.

Mit freundlichen Grüßen
[VORNAME] [NACHNAME]
[MATRIKELNUMMER]
TXT;

        return [
            [
                'slug' => 'arbeitszeugnis-anfrage',
                'category' => 'career',
                'doc_type' => 'email',
                'title_tr' => 'Arbeitszeugnis (Çalışma Belgesi) Talep E-postası',
                'title_en' => 'Request for a Job Reference (Arbeitszeugnis)',
                'title_de' => 'Bitte um Arbeitszeugnis – E-Mail-Vorlage',
                'description_tr' => 'İşten ayrılırken işverenden "qualifiziertes Arbeitszeugnis" (detaylı referans) istemek için kibar e-posta. Almanya\'da yasal hakkın.',
                'description_en' => 'A polite email to ask your employer for a "qualifiziertes Arbeitszeugnis" (detailed job reference) when leaving — your legal right in Germany.',
                'description_de' => 'Höfliche E-Mail mit der Bitte um ein qualifiziertes Arbeitszeugnis beim Ausscheiden.',
                'body_de' => $zeugnis,
                'body_en' => null,
                'guide_tr' => "Almanya'da çalışan, işten ayrılırken **Arbeitszeugnis hakkına** sahiptir.\n\n- **\"Qualifiziertes\"** (nitelikli/detaylı) iste — sadece \"einfaches\" (basit) değil; iş arama için detaylı olan değerlidir.\n- **Görev listesi sun** (`[AUFGABEN_UEBERSICHT]`): işveren bunu kullanır, lehine olur.\n- Zeugnis dili **kodludur** (\"stets zur vollsten Zufriedenheit\" = çok iyi); aldığında kontrol et.\n- Net bir **teslim tarihi** belirt.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "In Germany, an employee has a **legal right to an Arbeitszeugnis** when leaving.\n\n- Ask for a **\"qualifiziertes\"** (detailed) one, not just \"einfaches\" (basic) — the detailed version helps your job search.\n- **Offer a task list** (`[AUFGABEN_UEBERSICHT]`): the employer will use it, in your favour.\n- Zeugnis language is **coded** (\"stets zur vollsten Zufriedenheit\" = excellent); check it when received.\n- State a clear **deadline**.\n- Fill every `[BRACKET]`.",
                'guide_de' => "In Deutschland besteht ein **Anspruch auf ein Arbeitszeugnis**.\n\n- **„Qualifiziertes\"** Zeugnis verlangen, nicht nur „einfaches\".\n- **Aufgabenliste anbieten** — hilft bei der Erstellung.\n- Zeugnissprache ist **kodiert** — beim Erhalt prüfen.\n- Klare **Frist** nennen.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'POSITION', 'label_tr' => 'Pozisyonun', 'label_en' => 'Your position', 'label_de' => 'Position'],
                    ['key' => 'UNTERNEHMEN', 'label_tr' => 'Şirket', 'label_en' => 'Company', 'label_de' => 'Unternehmen'],
                    ['key' => 'ENDDATUM', 'label_tr' => 'İş bitiş tarihin', 'label_en' => 'End date', 'label_de' => 'Enddatum'],
                    ['key' => 'AUFGABEN_UEBERSICHT', 'label_tr' => 'Görev/proje özetin', 'label_en' => 'Summary of your tasks', 'label_de' => 'Aufgabenübersicht'],
                    ['key' => 'WUNSCHDATUM', 'label_tr' => 'İstediğin teslim tarihi', 'label_en' => 'Requested deadline', 'label_de' => 'Wunschdatum'],
                ],
                'is_premium' => true, 'sort_order' => 62, 'is_active' => true,
            ],
            [
                'slug' => 'widerspruch-bescheid',
                'category' => 'application',
                'doc_type' => 'letter',
                'title_tr' => 'Resmi Karara İtiraz (Widerspruch)',
                'title_en' => 'Objection to an Official Decision (Widerspruch)',
                'title_de' => 'Widerspruch gegen einen Bescheid – Vorlage',
                'description_tr' => 'BAföG, vize, kabul veya bir resmi Bescheid\'a karşı yasal süre içinde itiraz (Widerspruch) mektubu. (Örnek metin, hukuki danışmanlık değil.)',
                'description_en' => 'A formal objection (Widerspruch) against a BAföG, visa, admission or other official decision within the legal deadline. (Sample text, not legal advice.)',
                'description_de' => 'Formeller Widerspruch gegen einen Bescheid (BAföG, Visum, Zulassung) innerhalb der Frist. (Mustertext, keine Rechtsberatung.)',
                'body_de' => $widerspruch,
                'body_en' => null,
                'guide_tr' => "⚠️ Örnek metindir, **hukuki danışmanlık değil** — önemli durumda uzmana/danışma merkezine git (AStA, Studierendenwerk ücretsiz yardım eder).\n\n- **Süre kritik:** Widerspruchsfrist genelde kararın eline geçişinden itibaren **1 aydır** (bkz. Rechtsbehelfsbelehrung). Geçirme!\n- **Aktenzeichen** (dosya no) ve **Bescheid tarihi** mutlaka yaz.\n- **Begründung** (gerekçe) somut ve belgeli olsun (`[BEGRUENDUNG]`).\n- Yazılı + **ıslak imzalı** olmalı; mümkünse Einschreiben.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "⚠️ Sample text, **not legal advice** — for important cases seek expert/counselling help (AStA, Studierendenwerk offer free support).\n\n- **The deadline is critical:** the Widerspruchsfrist is usually **1 month** from receipt (see the Rechtsbehelfsbelehrung). Don't miss it!\n- Always state the **Aktenzeichen** (file number) and the **decision date**.\n- The **reasoning** must be concrete and documented (`[BEGRUENDUNG]`).\n- Must be written and **hand-signed**; ideally registered post.\n- Fill every `[BRACKET]`.",
                'guide_de' => "⚠️ Mustertext, **keine Rechtsberatung** — im Zweifel Beratung suchen (AStA, Studierendenwerk).\n\n- **Frist beachten:** meist **1 Monat** ab Zugang (Rechtsbehelfsbelehrung).\n- **Aktenzeichen** und **Bescheiddatum** angeben.\n- **Begründung** konkret und belegt.\n- Schriftlich und **unterschrieben**, möglichst per Einschreiben.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'BEHOERDE_NAME', 'label_tr' => 'Kurum (BAföG-Amt, Ausländerbehörde…)', 'label_en' => 'Authority', 'label_de' => 'Behörde'],
                    ['key' => 'BESCHEID_DATUM', 'label_tr' => 'Kararın tarihi', 'label_en' => 'Date of the decision', 'label_de' => 'Bescheiddatum'],
                    ['key' => 'AKTENZEICHEN', 'label_tr' => 'Dosya numarası (Az.)', 'label_en' => 'File number', 'label_de' => 'Aktenzeichen'],
                    ['key' => 'ZUGANG_DATUM', 'label_tr' => 'Kararın eline geçtiği tarih', 'label_en' => 'Date you received it', 'label_de' => 'Zugangsdatum'],
                    ['key' => 'BEGRUENDUNG', 'label_tr' => 'İtiraz gerekçen', 'label_en' => 'Your reasoning', 'label_de' => 'Begründung'],
                    ['key' => 'FORDERUNG', 'label_tr' => 'Talebin (örn. yeniden değerlendirme)', 'label_en' => 'Your request', 'label_de' => 'Forderung'],
                ],
                'is_premium' => true, 'sort_order' => 35, 'is_active' => true,
            ],
            [
                'slug' => 'krankmeldung',
                'category' => 'career',
                'doc_type' => 'email',
                'title_tr' => 'Hastalık Bildirimi (Krankmeldung)',
                'title_en' => 'Sick Notification (Krankmeldung)',
                'title_de' => 'Krankmeldung – E-Mail-Vorlage',
                'description_tr' => 'İşe veya üniversiteye hastalık nedeniyle gelemeyeceğini bildiren kısa, doğru tonlu Almanca e-posta.',
                'description_en' => 'A short, correctly-toned German email to notify work or university that you are off sick.',
                'description_de' => 'Kurze Krankmeldung per E-Mail an Arbeit oder Hochschule.',
                'body_de' => $krank,
                'body_en' => null,
                'guide_tr' => "Almanya'da hastalık bildirimi **hızlı ve doğru** olmalı.\n\n- **İş başlamadan önce** haber ver (mümkünse sabah erken).\n- **Attest/AU** (rapor): işverende genelde **3. günden** itibaren gerekir, ama işveren **ilk günden** isteyebilir — `[ATTEST_HINWEIS]` ile belirt.\n- Üni **sınavına** giremiyorsan özel \"ärztliches Attest\" (çoğu zaman aynı gün) şarttır.\n- Teşhis yazmana gerek yok; sadece \"erkrankt\" yeterli.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "A sick notification in Germany should be **fast and correct**.\n\n- Notify **before work starts** (early morning if possible).\n- The **Attest/AU** (doctor's note): usually required from **day 3** at work, but an employer may ask from **day 1** — note this in `[ATTEST_HINWEIS]`.\n- For a **university exam**, a specific \"ärztliches Attest\" (often same-day) is mandatory.\n- You don't need to state a diagnosis; \"erkrankt\" is enough.\n- Fill every `[BRACKET]`.",
                'guide_de' => "Eine Krankmeldung sollte **schnell und korrekt** sein.\n\n- **Vor Arbeitsbeginn** Bescheid geben.\n- **AU/Attest** meist ab **Tag 3**, ggf. ab **Tag 1**.\n- Für **Prüfungen** spezielles ärztliches Attest nötig.\n- Keine Diagnose nötig; „erkrankt\" genügt.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'ARBEIT_ODER_UNI', 'label_tr' => 'İş yeri / üniversite', 'label_en' => 'Work / university', 'label_de' => 'Arbeit / Uni'],
                    ['key' => 'DATUM', 'label_tr' => 'Bugünün tarihi', 'label_en' => 'Today\'s date', 'label_de' => 'Datum'],
                    ['key' => 'DAUER', 'label_tr' => 'Tahmini süre (örn. 2-3 gün)', 'label_en' => 'Expected duration', 'label_de' => 'Dauer'],
                    ['key' => 'ATTEST_HINWEIS', 'label_tr' => 'Rapor notu (örn. "AU\'yu yarın iletirim")', 'label_en' => 'Note about the sick note', 'label_de' => 'Attest-Hinweis'],
                ],
                'is_premium' => false, 'sort_order' => 68, 'is_active' => true,
            ],
            [
                'slug' => 'urlaubssemester-antrag',
                'category' => 'application',
                'doc_type' => 'email',
                'title_tr' => 'İzin Dönemi Başvurusu (Urlaubssemester)',
                'title_en' => 'Leave of Absence Request (Urlaubssemester)',
                'title_de' => 'Antrag auf Urlaubssemester – Vorlage',
                'description_tr' => 'Hastalık, yurt dışı staj, dil kursu vb. nedenle bir dönem izin (Urlaubssemester) almak için üniversiteye başvuru.',
                'description_en' => 'A request to your university for a leave semester (Urlaubssemester) due to illness, internship abroad, language course, etc.',
                'description_de' => 'Antrag an die Hochschule auf ein Urlaubssemester (Krankheit, Auslandspraktikum, Sprachkurs …).',
                'body_de' => $urlaub,
                'body_en' => null,
                'guide_tr' => "Urlaubssemester kayıt aktif kalır ama o dönem ders/sınav yükümlülüğün düşer.\n\n- **Geçerli sebep** gerekir (`[GRUND]`): hastalık, yurt dışı staj/değişim, dil kursu, hamilelik/çocuk bakımı.\n- **Başvuru süresi** üniversiteye göre değişir — genelde kayıt döneminde. Geç kalma.\n- **ÖNEMLİ:** Urlaubssemester **BAföG'ü ve oturum iznini (Aufenthaltstitel) etkileyebilir** — Ausländerbehörde'ye danış.\n- Gerekli **kanıtı** ekle (`[NACHWEIS_HINWEIS]`).\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "A leave semester keeps you enrolled but suspends your course/exam obligations.\n\n- A **valid reason** is required (`[GRUND]`): illness, internship/exchange abroad, language course, pregnancy/childcare.\n- The **application window** varies by university — usually during enrolment. Don't be late.\n- **IMPORTANT:** a Urlaubssemester **can affect BAföG and your residence permit** — check with the Ausländerbehörde.\n- Attach the required **proof** (`[NACHWEIS_HINWEIS]`).\n- Fill every `[BRACKET]`.",
                'guide_de' => "Ein Urlaubssemester hält die Einschreibung, setzt aber Studienpflichten aus.\n\n- **Triftiger Grund** nötig (Krankheit, Auslandspraktikum, Sprachkurs …).\n- **Antragsfrist** beachten (meist zur Rückmeldung).\n- **Wichtig:** kann **BAföG und Aufenthaltstitel** beeinflussen — Ausländerbehörde fragen.\n- **Nachweis** beifügen.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'SEMESTER', 'label_tr' => 'Hangi dönem (örn. SS 2027)', 'label_en' => 'Which semester', 'label_de' => 'Semester'],
                    ['key' => 'STUDIENGANG', 'label_tr' => 'Bölümün', 'label_en' => 'Study programme', 'label_de' => 'Studiengang'],
                    ['key' => 'MATRIKELNUMMER', 'label_tr' => 'Öğrenci numaran', 'label_en' => 'Matriculation number', 'label_de' => 'Matrikelnummer'],
                    ['key' => 'GRUND', 'label_tr' => 'İzin sebebin', 'label_en' => 'Reason for leave', 'label_de' => 'Grund'],
                    ['key' => 'NACHWEIS_HINWEIS', 'label_tr' => 'Eklediğin kanıt (örn. staj sözleşmesi)', 'label_en' => 'Proof you attach', 'label_de' => 'Nachweis-Hinweis'],
                ],
                'is_premium' => true, 'sort_order' => 38, 'is_active' => true,
            ],
        ];
    }
};

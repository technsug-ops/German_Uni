<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 2. parti şablon (premium içerik) — kategori çeşitliliği: konut, kariyer, finans.
 * Wohnungsbewerbung, iş başvurusu Anschreiben, Sperrkonto freigabe, Exmatrikulation.
 * Idempotent (slug). Gerçek Almanca belge formatları.
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
                'wohnungsbewerbung', 'anschreiben-job', 'sperrkonto-freigabe', 'exmatrikulation',
            ])->delete();
        }
    }

    private function templates(): array
    {
        $wohnung = <<<'TXT'
[VORNAME] [NACHNAME]
[ADRESSE], [PLZ] [STADT]
[EMAIL]  ·  [TELEFON]

[VERMIETER_NAME]
[VERMIETER_ADRESSE]

[ORT], [DATUM]


Betreff: Bewerbung um die Wohnung in der [WOHNUNG_ADRESSE]


Sehr geehrte/r [VERMIETER_NAME],

mit großem Interesse habe ich Ihr Wohnungsangebot in der [WOHNUNG_ADRESSE]
gesehen und möchte mich hiermit um die Wohnung bewerben.

Ich bin [ALTER] Jahre alt und studiere [STUDIENGANG] an der [HOCHSCHULE]. Mein
Aufenthalt ist finanziell abgesichert durch [FINANZIERUNG], sodass die Miete von
[MIETE] zuverlässig gezahlt werden kann.

Als Mieter bin ich ruhig, ordentlich und Nichtraucher. Gerne lege ich Ihnen
folgende Unterlagen vor: Immatrikulationsbescheinigung, Finanzierungsnachweis
und SCHUFA-Auskunft.

Über eine Einladung zur Besichtigung würde ich mich sehr freuen.

Mit freundlichen Grüßen
[VORNAME] [NACHNAME]
TXT;

        $job = <<<'TXT'
[VORNAME] [NACHNAME]
[ADRESSE], [PLZ] [STADT]
[EMAIL]  ·  [TELEFON]

[UNTERNEHMEN]
[ANSPRECHPARTNER]
[UNTERNEHMEN_ADRESSE]

[ORT], [DATUM]


Betreff: Bewerbung als [POSITION] – [STELLEN_REFERENZ]


Sehr geehrte/r [ANSPRECHPARTNER],

mit großem Interesse habe ich Ihre Stellenausschreibung als [POSITION] gelesen.
Als [AKTUELLE_ROLLE] mit Erfahrung in [KERNKOMPETENZ] bin ich überzeugt, gut in
Ihr Team zu passen.

Während [ERFAHRUNG_KONTEXT] habe ich [KONKRETE_LEISTUNG]. Besonders motiviert
mich an [UNTERNEHMEN] [WARUM_FIRMA].

Über die Möglichkeit eines persönlichen Gesprächs würde ich mich sehr freuen.
Mein frühestmöglicher Eintrittstermin ist [EINTRITTSTERMIN].

Mit freundlichen Grüßen
[VORNAME] [NACHNAME]
TXT;

        $sperr = <<<'TXT'
Betreff: Antrag auf Freigabe meines Sperrkontos – [KONTONUMMER]


Sehr geehrte Damen und Herren,

ich bin Inhaber/in des Sperrkontos mit der Nummer [KONTONUMMER]. Mein Aufenthalt
in Deutschland [STATUS], daher beantrage ich die [ANLIEGEN] des Kontos.

Als Nachweis füge ich [NACHWEIS] bei.

Bitte teilen Sie mir mit, ob weitere Unterlagen erforderlich sind. Für Rückfragen
stehe ich gerne zur Verfügung.

Mit freundlichen Grüßen
[VORNAME] [NACHNAME]
[EMAIL]  ·  [TELEFON]
TXT;

        $exmat = <<<'TXT'
Betreff: Antrag auf Exmatrikulation zum [DATUM_ZUM]


Sehr geehrte Damen und Herren,

hiermit beantrage ich meine Exmatrikulation aus dem Studiengang [STUDIENGANG]
zum [DATUM_ZUM].

Matrikelnummer: [MATRIKELNUMMER]
Grund: [GRUND]

Bitte senden Sie mir die Exmatrikulationsbescheinigung an die oben genannte
E-Mail-Adresse. Eine etwaige Rückerstattung des Semesterbeitrags überweisen Sie
bitte auf folgendes Konto: [IBAN].

Vielen Dank im Voraus.

Mit freundlichen Grüßen
[VORNAME] [NACHNAME]
[EMAIL]
TXT;

        return [
            [
                'slug' => 'wohnungsbewerbung',
                'category' => 'housing',
                'doc_type' => 'letter',
                'title_tr' => 'Ev Başvuru Mektubu (Wohnungsbewerbung)',
                'title_en' => 'Apartment Application Letter (Wohnungsbewerbung)',
                'title_de' => 'Wohnungsbewerbung-Vorlage',
                'description_tr' => 'Ev sahibine güven veren resmi başvuru mektubu — öğrenci, finansman güvencesi, sessiz kiracı. Rekabetçi Alman konut piyasasında fark yaratır.',
                'description_en' => 'A formal application letter that reassures landlords — student status, secured financing, quiet tenant. Stands out in Germany\'s competitive housing market.',
                'description_de' => 'Formelle Wohnungsbewerbung, die Vermieter überzeugt — Studierendenstatus, gesicherte Finanzierung, ruhiger Mieter.',
                'body_de' => $wohnung,
                'body_en' => null,
                'guide_tr' => "Almanya'da ev bulmak rekabetçidir; iyi bir başvuru mektubu **öne çıkmanı** sağlar.\n\n- **Finansmanı vurgula** (`[FINANZIERUNG]`): Sperrkonto / burs / aile → ev sahibi kira garantisi arar.\n- **Sessiz, düzenli, sigara içmeyen** kiracı olduğunu belirt — ev sahiplerinin tam istediği.\n- Hazır belge listesi (Immatrikulation, finansman kanıtı, SCHUFA) güven verir.\n- Kısa ve kibar tut; bir paragraf kendini tanıt, bir paragraf güvence.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "Finding a flat in Germany is competitive; a good application letter helps you **stand out**.\n\n- **Highlight your financing** (`[FINANZIERUNG]`): Sperrkonto / scholarship / family — landlords want rent security.\n- State that you are a **quiet, tidy, non-smoking** tenant.\n- Listing ready documents (enrolment, proof of funds, SCHUFA) builds trust.\n- Keep it short and polite.\n- Fill every `[BRACKET]`.",
                'guide_de' => "Wohnungssuche ist kompetitiv; eine gute Bewerbung hebt Sie hervor.\n\n- **Finanzierung betonen** (`[FINANZIERUNG]`): Sperrkonto / Stipendium / Familie.\n- **Ruhig, ordentlich, Nichtraucher** erwähnen.\n- Unterlagenliste (Immatrikulation, Finanzierungsnachweis, SCHUFA) schafft Vertrauen.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'WOHNUNG_ADRESSE', 'label_tr' => 'İlandaki dairenin adresi', 'label_en' => 'Address of the advertised flat', 'label_de' => 'Adresse der Wohnung'],
                    ['key' => 'VERMIETER_NAME', 'label_tr' => 'Ev sahibinin adı', 'label_en' => 'Landlord\'s name', 'label_de' => 'Name des Vermieters'],
                    ['key' => 'FINANZIERUNG', 'label_tr' => 'Finansman (Sperrkonto/burs/aile)', 'label_en' => 'Financing (blocked account/scholarship/family)', 'label_de' => 'Finanzierung'],
                    ['key' => 'MIETE', 'label_tr' => 'Aylık kira (örn. 450 €)', 'label_en' => 'Monthly rent', 'label_de' => 'Miete'],
                    ['key' => 'STUDIENGANG', 'label_tr' => 'Bölümün', 'label_en' => 'Study programme', 'label_de' => 'Studiengang'],
                ],
                'is_premium' => true, 'sort_order' => 50, 'is_active' => true,
            ],
            [
                'slug' => 'anschreiben-job',
                'category' => 'career',
                'doc_type' => 'letter',
                'title_tr' => 'İş Başvuru Mektubu (Anschreiben)',
                'title_en' => 'Job Application Cover Letter (Anschreiben)',
                'title_de' => 'Bewerbungsanschreiben-Vorlage (Job)',
                'description_tr' => 'Werkstudent/tam zamanlı iş için Alman tarzı Anschreiben — pozisyona uygunluğunu somut başarıyla anlatan kapak mektubu.',
                'description_en' => 'A German-style cover letter (Anschreiben) for a Werkstudent or full-time role — showing your fit with concrete achievements.',
                'description_de' => 'Bewerbungsanschreiben für Werkstudenten- oder Vollzeitstelle — mit konkreten Leistungen.',
                'body_de' => $job,
                'body_en' => null,
                'guide_tr' => "Alman **Anschreiben** bir sayfadır ve İngilizce cover letter'dan daha resmidir.\n\n- **İlk cümlede** hangi pozisyona, nereden gördüğünü belirt (`[STELLEN_REFERENZ]`).\n- **Somut başarı** ver (`[KONKRETE_LEISTUNG]`) — \"takım oyuncusuyum\" değil, rakam/sonuç.\n- **Neden bu firma** (`[WARUM_FIRMA]`) → firmayı araştırdığını göster.\n- Mümkünse **isimle hitap et** (`[ANSPRECHPARTNER]`); yoksa \"Sehr geehrte Damen und Herren\".\n- Eintrittstermin (başlama tarihi) ekle.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "A German **Anschreiben** is one page and more formal than an English cover letter.\n\n- In the **first sentence**, name the position and where you saw it.\n- Give a **concrete achievement** — not \"I'm a team player\" but numbers/results.\n- Show **why this company** → prove you researched it.\n- Address a **named person** if possible; otherwise \"Sehr geehrte Damen und Herren\".\n- Add your earliest start date (Eintrittstermin).\n- Fill every `[BRACKET]`.",
                'guide_de' => "Ein **Anschreiben** ist eine Seite und formell.\n\n- **Erster Satz:** Position + Fundort nennen.\n- **Konkrete Leistung** statt Floskeln.\n- **Warum diese Firma** zeigen.\n- Möglichst **namentlich** anreden.\n- Eintrittstermin angeben.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'POSITION', 'label_tr' => 'Başvurduğun pozisyon', 'label_en' => 'Position', 'label_de' => 'Position'],
                    ['key' => 'UNTERNEHMEN', 'label_tr' => 'Şirket adı', 'label_en' => 'Company', 'label_de' => 'Unternehmen'],
                    ['key' => 'KERNKOMPETENZ', 'label_tr' => 'Ana yetkinliğin', 'label_en' => 'Your core skill', 'label_de' => 'Kernkompetenz'],
                    ['key' => 'KONKRETE_LEISTUNG', 'label_tr' => 'Somut başarı (rakam/sonuç)', 'label_en' => 'Concrete achievement', 'label_de' => 'Konkrete Leistung'],
                    ['key' => 'WARUM_FIRMA', 'label_tr' => 'Bu şirketi neden istediğin', 'label_en' => 'Why this company', 'label_de' => 'Warum diese Firma'],
                    ['key' => 'EINTRITTSTERMIN', 'label_tr' => 'Başlayabileceğin tarih', 'label_en' => 'Earliest start date', 'label_de' => 'Eintrittstermin'],
                ],
                'is_premium' => true, 'sort_order' => 60, 'is_active' => true,
            ],
            [
                'slug' => 'sperrkonto-freigabe',
                'category' => 'finance',
                'doc_type' => 'email',
                'title_tr' => 'Sperrkonto Freigabe/Çözme E-postası',
                'title_en' => 'Blocked Account Release Request Email',
                'title_de' => 'Sperrkonto-Freigabe – E-Mail-Vorlage',
                'description_tr' => 'Sperrkonto sağlayıcısına aylık ödeme, freigabe veya hesap kapatma talebi için doğru tonlu kısa Almanca e-posta.',
                'description_en' => 'A correctly-toned German email to ask your blocked-account provider for the monthly payout, release, or account closure.',
                'description_de' => 'Kurze E-Mail an den Sperrkonto-Anbieter für Auszahlung, Freigabe oder Auflösung.',
                'body_de' => $sperr,
                'body_en' => null,
                'guide_tr' => "Sperrkonto işlemleri **doğru terim + kanıt** ister.\n\n- Konu satırına **hesap numaranı** koy (`[KONTONUMMER]`).\n- Anliegen'i net seç (`[ANLIEGEN]`): *monatliche Auszahlung* (aylık ödeme) / *Freigabe* (serbest bırakma) / *Auflösung* (kapatma).\n- Uygun **kanıt** ekle (`[NACHWEIS]`): Immatrikulation, Aufenthaltstitel veya Exmatrikulation.\n- Kısa ve resmi tut.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "Blocked-account requests need the **right term + proof**.\n\n- Put your **account number** in the subject (`[KONTONUMMER]`).\n- Choose the request clearly (`[ANLIEGEN]`): *monatliche Auszahlung* (monthly payout) / *Freigabe* (release) / *Auflösung* (closure).\n- Attach the matching **proof** (`[NACHWEIS]`): enrolment, residence permit, or de-registration.\n- Keep it short and formal.\n- Fill every `[BRACKET]`.",
                'guide_de' => "Sperrkonto-Anliegen brauchen den **richtigen Begriff + Nachweis**.\n\n- **Kontonummer** in den Betreff.\n- Anliegen klar wählen: Auszahlung / Freigabe / Auflösung.\n- Passenden **Nachweis** beifügen.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'KONTONUMMER', 'label_tr' => 'Sperrkonto numaran', 'label_en' => 'Blocked account number', 'label_de' => 'Kontonummer'],
                    ['key' => 'STATUS', 'label_tr' => 'Durumun (örn. başladı / bitti)', 'label_en' => 'Your status (e.g. started / finished)', 'label_de' => 'Status'],
                    ['key' => 'ANLIEGEN', 'label_tr' => 'Talep (Auszahlung/Freigabe/Auflösung)', 'label_en' => 'Request (payout/release/closure)', 'label_de' => 'Anliegen'],
                    ['key' => 'NACHWEIS', 'label_tr' => 'Eklediğin kanıt belgesi', 'label_en' => 'Proof document attached', 'label_de' => 'Nachweis'],
                ],
                'is_premium' => false, 'sort_order' => 70, 'is_active' => true,
            ],
            [
                'slug' => 'exmatrikulation',
                'category' => 'application',
                'doc_type' => 'email',
                'title_tr' => 'Exmatrikulation (Kayıt Sildirme) Başvurusu',
                'title_en' => 'De-registration (Exmatrikulation) Request',
                'title_de' => 'Exmatrikulation – Antrag-Vorlage',
                'description_tr' => 'Mezuniyet/okul değişikliği sonrası üniversiteden kaydını sildirme (Exmatrikulation) ve Semesterbeitrag iadesi için resmi başvuru.',
                'description_en' => 'A formal request to de-register (Exmatrikulation) from your university after graduation or transfer — including semester fee refund.',
                'description_de' => 'Formeller Antrag auf Exmatrikulation — inkl. Rückerstattung des Semesterbeitrags.',
                'body_de' => $exmat,
                'body_en' => null,
                'guide_tr' => "Exmatrikulation, üniversiteyle resmi ilişkini bitirir — burs/vize/Sperrkonto işlemleri için gerekebilir.\n\n- **Matrikelnummer** şart (`[MATRIKELNUMMER]`).\n- **Tarih** net olsun (`[DATUM_ZUM]`) — genelde dönem sonu.\n- **Grund** (sebep) belirt: Studienabschluss / Studienortwechsel / persönliche Gründe.\n- **Semesterbeitrag iadesi** için IBAN ekle (`[IBAN]`).\n- Studierendensekretariat'a gönder.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "Exmatrikulation formally ends your enrolment — often needed for scholarship/visa/blocked-account steps.\n\n- **Matriculation number** is required.\n- State a clear **date** (usually end of semester).\n- Give a **reason**: graduation / transfer / personal.\n- Add your **IBAN** for the semester-fee refund.\n- Send it to the Studierendensekretariat.\n- Fill every `[BRACKET]`.",
                'guide_de' => "Die Exmatrikulation beendet die Einschreibung formal.\n\n- **Matrikelnummer** angeben.\n- Klares **Datum** (meist Semesterende).\n- **Grund** nennen.\n- **IBAN** für die Rückerstattung angeben.\n- An das Studierendensekretariat senden.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'STUDIENGANG', 'label_tr' => 'Bölümün', 'label_en' => 'Study programme', 'label_de' => 'Studiengang'],
                    ['key' => 'MATRIKELNUMMER', 'label_tr' => 'Öğrenci numaran', 'label_en' => 'Matriculation number', 'label_de' => 'Matrikelnummer'],
                    ['key' => 'DATUM_ZUM', 'label_tr' => 'Sildirme tarihi (örn. 31.03.2027)', 'label_en' => 'De-registration date', 'label_de' => 'Datum zum'],
                    ['key' => 'GRUND', 'label_tr' => 'Sebep (mezuniyet/değişiklik…)', 'label_en' => 'Reason', 'label_de' => 'Grund'],
                    ['key' => 'IBAN', 'label_tr' => 'İade için IBAN', 'label_en' => 'IBAN for refund', 'label_de' => 'IBAN'],
                ],
                'is_premium' => false, 'sort_order' => 80, 'is_active' => true,
            ],
        ];
    }
};

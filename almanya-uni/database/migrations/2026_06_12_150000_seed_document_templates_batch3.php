<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 3. parti şablon (premium içerik): Praktikum-Anschreiben, Erasmus & Stipendium
 * Motivationsschreiben, Wohnungs-Kündigung, Untermietvertrag. Idempotent (slug).
 * Untermietvertrag örnek sözleşmedir — hukuki danışmanlık değil (rehberde belirtildi).
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
                'praktikum-anschreiben', 'erasmus-motivation', 'stipendium-motivation',
                'kuendigung-wohnung', 'untermietvertrag',
            ])->delete();
        }
    }

    private function templates(): array
    {
        $praktikum = <<<'TXT'
[VORNAME] [NACHNAME]
[ADRESSE], [PLZ] [STADT]
[EMAIL]  ·  [TELEFON]

[UNTERNEHMEN]
[ANSPRECHPARTNER]
[UNTERNEHMEN_ADRESSE]

[ORT], [DATUM]


Betreff: Bewerbung um ein Praktikum im Bereich [BEREICH] ab [ZEITRAUM]


Sehr geehrte/r [ANSPRECHPARTNER],

derzeit studiere ich [STUDIENGANG] an der [HOCHSCHULE] und suche zum [ZEITRAUM]
ein [DAUER]-Praktikum im Bereich [BEREICH]. Ihr Unternehmen interessiert mich
besonders, weil [WARUM_FIRMA].

Durch mein Studium und [BISHERIGE_ERFAHRUNG] bringe ich Kenntnisse in
[KENNTNISSE] mit. Besonders [STAERKE] möchte ich in der Praxis vertiefen.

Über die Möglichkeit, Ihr Team zu unterstützen und praktische Erfahrung zu
sammeln, würde ich mich sehr freuen.

Mit freundlichen Grüßen
[VORNAME] [NACHNAME]
TXT;

        $erasmus = <<<'TXT'
[VORNAME] [NACHNAME]
[STUDIENGANG], [HEIMAT_HOCHSCHULE]
[EMAIL]

[ORT], [DATUM]


Betreff: Motivationsschreiben für das Erasmus+ Programm an der [GAST_HOCHSCHULE]


Sehr geehrte Damen und Herren,

mit diesem Schreiben bewerbe ich mich um einen Erasmus+ Studienplatz an der
[GAST_HOCHSCHULE] für das [SEMESTER]. [EINLEITUNG_WARUM_ERASMUS]

An der [GAST_HOCHSCHULE] reizt mich besonders [WARUM_GASTUNI] – etwa die Kurse
[KURSE]. Diese ergänzen mein Studium [STUDIENGANG] ideal.

Persönlich erhoffe ich mir interkulturelle Erfahrung, sprachliche
Weiterentwicklung und ein internationales Netzwerk. Sprachlich bin ich durch
[SPRACHKENNTNISSE] gut vorbereitet.

Ich bin überzeugt, dass dieser Austausch mich fachlich und persönlich
voranbringt, und freue mich auf Ihre Rückmeldung.

Mit freundlichen Grüßen
[VORNAME] [NACHNAME]
TXT;

        $stipendium = <<<'TXT'
[VORNAME] [NACHNAME]
[ADRESSE], [PLZ] [STADT]
[EMAIL]

[STIFTUNG_NAME]
[ORT]

[ORT], [DATUM]


Betreff: Motivationsschreiben für das Stipendium [STIPENDIUM_NAME]


Sehr geehrte Damen und Herren,

mit großem Interesse bewerbe ich mich um das Stipendium [STIPENDIUM_NAME]. Ich
studiere [STUDIENGANG] an der [HOCHSCHULE] und [AKTUELLE_LEISTUNG].

Mein akademischer Weg ist geprägt von [WERDEGANG]. Besonders [ENGAGEMENT] zeigt,
dass ich die Ziele Ihrer Stiftung teile.

Das Stipendium würde mir ermöglichen, [WAS_ERMOEGLICHT_ES], und mich auf
[ZUKUNFTSZIEL] zu konzentrieren. Im Gegenzug möchte ich mich [WIE_ENGAGIEREN].

Über die Gelegenheit, Sie persönlich zu überzeugen, würde ich mich sehr freuen.

Mit freundlichen Grüßen
[VORNAME] [NACHNAME]
TXT;

        $kuendigung = <<<'TXT'
[VORNAME] [NACHNAME]
[ADRESSE], [PLZ] [STADT]

[VERMIETER_NAME]
[VERMIETER_ADRESSE]

[ORT], [DATUM]


Betreff: Kündigung des Mietvertrags für die Wohnung [WOHNUNG_ADRESSE]


Sehr geehrte/r [VERMIETER_NAME],

hiermit kündige ich den Mietvertrag für die oben genannte Wohnung ordentlich und
fristgerecht zum [KUENDIGUNGSDATUM].

Ich bitte Sie, mir den Erhalt dieser Kündigung schriftlich zu bestätigen. Für die
Wohnungsübergabe schlage ich den [UEBERGABE_DATUM] vor; bitte teilen Sie mir
einen passenden Termin mit.

Die Kaution überweisen Sie nach ordnungsgemäßer Übergabe bitte auf folgendes
Konto: [IBAN].

Mit freundlichen Grüßen


_______________________
[VORNAME] [NACHNAME]
TXT;

        $unter = <<<'TXT'
UNTERMIETVERTRAG


zwischen

[HAUPTMIETER_NAME]  (Hauptmieter/in)
[HAUPTMIETER_ADRESSE]

und

[UNTERMIETER_NAME]  (Untermieter/in)
[UNTERMIETER_ADRESSE]


§ 1  Mietobjekt
Vermietet wird [ZIMMER_BESCHREIBUNG] in der Wohnung [WOHNUNG_ADRESSE] zur
Mitbenutzung von Küche, Bad und Flur.

§ 2  Mietzeit
Das Untermietverhältnis beginnt am [BEGINN]. Es ist [BEFRISTUNG].

§ 3  Miete
Die monatliche Miete beträgt [MIETE] EUR (inkl. Nebenkosten) und ist bis zum
3. Werktag eines Monats auf folgendes Konto zu zahlen: [IBAN].

§ 4  Kaution
Der/die Untermieter/in zahlt eine Kaution in Höhe von [KAUTION] EUR.

§ 5  Sonstiges
Der/die Hauptmieter/in bestätigt, dass die Untervermietung vom Hauptvermieter
genehmigt ist. Nebenabreden bedürfen der Schriftform.


[ORT], [DATUM]


_______________________          _______________________
[HAUPTMIETER_NAME]               [UNTERMIETER_NAME]
(Hauptmieter/in)                 (Untermieter/in)
TXT;

        return [
            [
                'slug' => 'praktikum-anschreiben',
                'category' => 'career',
                'doc_type' => 'letter',
                'title_tr' => 'Staj Başvuru Mektubu (Praktikum)',
                'title_en' => 'Internship Application Letter (Praktikum)',
                'title_de' => 'Praktikumsbewerbung – Anschreiben',
                'description_tr' => 'Almanya\'da staj (Pflicht- veya freiwilliges Praktikum) için Anschreiben — öğrenci profilini ve motivasyonunu öne çıkaran kapak mektubu.',
                'description_en' => 'A cover letter for an internship (Pflicht- or voluntary Praktikum) in Germany — highlighting your student profile and motivation.',
                'description_de' => 'Anschreiben für ein Pflicht- oder freiwilliges Praktikum — mit Studierendenprofil und Motivation.',
                'body_de' => $praktikum,
                'body_en' => null,
                'guide_tr' => "Staj başvurusu kısa ve **motivasyon odaklı** olmalı.\n\n- **Zorunlu staj** (Pflichtpraktikum) ise belirt — firmalar bunu sever (sigorta/ücret avantajı).\n- Tarih ve **süreyi** net ver (`[ZEITRAUM]`, `[DAUER]`).\n- Henüz çok deneyimin yoksa **öğrenmeye isteğini** ve ders/proje bilgini vurgula.\n- `[WARUM_FIRMA]` ile firmayı araştırdığını göster.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "An internship application should be short and **motivation-driven**.\n\n- If it's a **mandatory internship** (Pflichtpraktikum), say so — companies prefer it.\n- State the **dates and duration** clearly.\n- With little experience, emphasise your **willingness to learn** and relevant coursework.\n- Use `[WARUM_FIRMA]` to show you researched the company.\n- Fill every `[BRACKET]`.",
                'guide_de' => "Eine Praktikumsbewerbung ist kurz und **motivationsorientiert**.\n\n- **Pflichtpraktikum** erwähnen, falls zutreffend.\n- **Zeitraum und Dauer** klar angeben.\n- Lernbereitschaft und relevante Studieninhalte betonen.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'BEREICH', 'label_tr' => 'Staj alanı (örn. Marketing)', 'label_en' => 'Internship field', 'label_de' => 'Bereich'],
                    ['key' => 'ZEITRAUM', 'label_tr' => 'Başlangıç zamanı', 'label_en' => 'Start period', 'label_de' => 'Zeitraum'],
                    ['key' => 'DAUER', 'label_tr' => 'Süre (örn. 3 aylık)', 'label_en' => 'Duration', 'label_de' => 'Dauer'],
                    ['key' => 'WARUM_FIRMA', 'label_tr' => 'Bu firmayı neden istediğin', 'label_en' => 'Why this company', 'label_de' => 'Warum diese Firma'],
                    ['key' => 'KENNTNISSE', 'label_tr' => 'İlgili bilgi/becerin', 'label_en' => 'Relevant skills', 'label_de' => 'Kenntnisse'],
                ],
                'is_premium' => true, 'sort_order' => 65, 'is_active' => true,
            ],
            [
                'slug' => 'erasmus-motivation',
                'category' => 'application',
                'doc_type' => 'letter',
                'title_tr' => 'Erasmus+ Motivasyon Mektubu',
                'title_en' => 'Erasmus+ Motivation Letter',
                'title_de' => 'Erasmus+ Motivationsschreiben',
                'description_tr' => 'Erasmus+ değişim başvurusu için motivasyon mektubu — neden bu üniversite, akademik uyum ve kişisel hedefler.',
                'description_en' => 'A motivation letter for an Erasmus+ exchange — why this host university, academic fit and personal goals.',
                'description_de' => 'Motivationsschreiben für einen Erasmus+ Austausch — Gasthochschule, fachliche Passung, persönliche Ziele.',
                'body_de' => $erasmus,
                'body_en' => null,
                'guide_tr' => "Erasmus mektubu **akademik + kişisel** dengeyi kurmalı.\n\n- **Gast üniversiteyi** somut anlat (`[WARUM_GASTUNI]`): spesifik dersler (`[KURSE]`), bölümün gücü.\n- Kendi müfredatına **nasıl uyduğunu** göster — komisyon akademik mantık arar.\n- Dil hazırlığını belirt (`[SPRACHKENNTNISSE]`).\n- Klişe \"kültür öğrenmek istiyorum\"u somutla.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "An Erasmus letter balances **academic + personal**.\n\n- Describe the **host university** concretely (`[WARUM_GASTUNI]`): specific courses, departmental strengths.\n- Show how it **fits your curriculum** — the committee wants academic logic.\n- Mention your language readiness (`[SPRACHKENNTNISSE]`).\n- Make the cliché \"I want to experience culture\" concrete.\n- Fill every `[BRACKET]`.",
                'guide_de' => "Ein Erasmus-Schreiben verbindet **Fachliches + Persönliches**.\n\n- **Gasthochschule** konkret beschreiben (Kurse, Stärken).\n- **Passung zum Curriculum** zeigen.\n- Sprachliche Vorbereitung nennen.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'GAST_HOCHSCHULE', 'label_tr' => 'Gideceğin üniversite', 'label_en' => 'Host university', 'label_de' => 'Gasthochschule'],
                    ['key' => 'SEMESTER', 'label_tr' => 'Hangi dönem', 'label_en' => 'Which semester', 'label_de' => 'Semester'],
                    ['key' => 'WARUM_GASTUNI', 'label_tr' => 'Bu üniversiteyi seçme nedenin', 'label_en' => 'Why this host university', 'label_de' => 'Warum diese Gastuni'],
                    ['key' => 'KURSE', 'label_tr' => 'İlgini çeken dersler', 'label_en' => 'Courses of interest', 'label_de' => 'Kurse'],
                    ['key' => 'SPRACHKENNTNISSE', 'label_tr' => 'Dil seviyen', 'label_en' => 'Language level', 'label_de' => 'Sprachkenntnisse'],
                ],
                'is_premium' => true, 'sort_order' => 25, 'is_active' => true,
            ],
            [
                'slug' => 'stipendium-motivation',
                'category' => 'application',
                'doc_type' => 'letter',
                'title_tr' => 'Burs (Stipendium) Motivasyon Mektubu',
                'title_en' => 'Scholarship (Stipendium) Motivation Letter',
                'title_de' => 'Stipendium-Motivationsschreiben',
                'description_tr' => 'DAAD / vakıf bursu başvurusu için motivasyon mektubu — akademik başarı, sosyal sorumluluk ve vakfın değerleriyle uyum.',
                'description_en' => 'A motivation letter for a DAAD / foundation scholarship — academic merit, social engagement and fit with the foundation\'s values.',
                'description_de' => 'Motivationsschreiben für ein DAAD-/Stiftungsstipendium — Leistung, Engagement, Passung zur Stiftung.',
                'body_de' => $stipendium,
                'body_en' => null,
                'guide_tr' => "Burs mektubunda **vakfın değerleriyle uyum** kritiktir.\n\n- Vakfı araştır: siyasi/dini/akademik odağı neyse ona **dürüstçe** bağlan (`[ENGAGEMENT]`).\n- Sadece not değil; **sosyal sorumluluk / gönüllülük / liderlik** Almanya burslarında çok önemli.\n- Bursun sana **ne sağlayacağını** ve karşılığında nasıl katkı vereceğini somutla.\n- Abartma; samimi ve spesifik kazandırır.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "For a scholarship letter, **fit with the foundation's values** is key.\n\n- Research the foundation and connect **honestly** to its focus (`[ENGAGEMENT]`).\n- Not just grades — **social engagement / volunteering / leadership** matters a lot in Germany.\n- Make concrete what the scholarship **enables** and how you'll give back.\n- Be sincere and specific, not grand.\n- Fill every `[BRACKET]`.",
                'guide_de' => "Beim Stipendium ist die **Passung zur Stiftung** entscheidend.\n\n- Stiftung recherchieren und **ehrlich** anknüpfen.\n- Nicht nur Noten — **Engagement / Ehrenamt / Führung** zählt.\n- Konkret machen, was das Stipendium ermöglicht.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'STIPENDIUM_NAME', 'label_tr' => 'Burs adı (örn. DAAD …)', 'label_en' => 'Scholarship name', 'label_de' => 'Stipendiumsname'],
                    ['key' => 'STIFTUNG_NAME', 'label_tr' => 'Vakıf/kurum adı', 'label_en' => 'Foundation name', 'label_de' => 'Stiftungsname'],
                    ['key' => 'ENGAGEMENT', 'label_tr' => 'Sosyal sorumluluk/gönüllülük örneğin', 'label_en' => 'Your social engagement', 'label_de' => 'Engagement'],
                    ['key' => 'WAS_ERMOEGLICHT_ES', 'label_tr' => 'Bursun sana sağlayacağı', 'label_en' => 'What the scholarship enables', 'label_de' => 'Was es ermöglicht'],
                    ['key' => 'ZUKUNFTSZIEL', 'label_tr' => 'Gelecek hedefin', 'label_en' => 'Future goal', 'label_de' => 'Zukunftsziel'],
                ],
                'is_premium' => true, 'sort_order' => 28, 'is_active' => true,
            ],
            [
                'slug' => 'kuendigung-wohnung',
                'category' => 'housing',
                'doc_type' => 'letter',
                'title_tr' => 'Ev Fesih Mektubu (Kündigung)',
                'title_en' => 'Apartment Lease Termination (Kündigung)',
                'title_de' => 'Wohnungskündigung – Vorlage',
                'description_tr' => 'Kira sözleşmesini yasal süreye uygun feshetme mektubu — teslim ve kaution iadesi talebiyle. (Genelde 3 ay Kündigungsfrist.)',
                'description_en' => 'A letter to terminate your lease within the legal notice period — including handover and deposit refund. (Usually a 3-month notice.)',
                'description_de' => 'Ordentliche Wohnungskündigung mit Übergabe und Kautionsrückzahlung. (Meist 3 Monate Frist.)',
                'body_de' => $kuendigung,
                'body_en' => null,
                'guide_tr' => "Kira feshi Almanya'da **biçim ve süreye** sıkı bağlıdır.\n\n- **Kündigungsfrist** çoğu sözleşmede **3 aydır** — `[KUENDIGUNGSDATUM]`'yi ona göre hesapla.\n- Mektup **ıslak imzalı** olmalı; en güvenlisi **Einschreiben** (taahhütlü posta) ile göndermek. E-posta çoğu zaman geçersiz.\n- **Kaution iadesi** için IBAN ekle (`[IBAN]`).\n- Teslim (Übergabe) tarihi öner.\n- Tüm `[KÖŞELİ]` alanları doldur, çıktıyı imzala.",
                'guide_en' => "Lease termination in Germany is strict on **form and notice**.\n\n- The notice period (Kündigungsfrist) is usually **3 months** — set `[KUENDIGUNGSDATUM]` accordingly.\n- The letter must be **hand-signed**; safest is to send it by **Einschreiben** (registered post). Email is often invalid.\n- Add your IBAN for the **deposit refund**.\n- Propose a handover date.\n- Fill every `[BRACKET]` and sign the printout.",
                'guide_de' => "Die Kündigung ist streng bei **Form und Frist**.\n\n- Die Kündigungsfrist beträgt meist **3 Monate**.\n- Brief **handschriftlich unterschreiben**; am sichersten per **Einschreiben**.\n- IBAN für die **Kautionsrückzahlung** angeben.\n- Übergabetermin vorschlagen.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'WOHNUNG_ADRESSE', 'label_tr' => 'Daire adresi', 'label_en' => 'Apartment address', 'label_de' => 'Wohnungsadresse'],
                    ['key' => 'VERMIETER_NAME', 'label_tr' => 'Ev sahibinin adı', 'label_en' => 'Landlord\'s name', 'label_de' => 'Name des Vermieters'],
                    ['key' => 'KUENDIGUNGSDATUM', 'label_tr' => 'Fesih tarihi (3 ay sonrası)', 'label_en' => 'Termination date', 'label_de' => 'Kündigungsdatum'],
                    ['key' => 'UEBERGABE_DATUM', 'label_tr' => 'Önerdiğin teslim tarihi', 'label_en' => 'Proposed handover date', 'label_de' => 'Übergabedatum'],
                    ['key' => 'IBAN', 'label_tr' => 'Kaution iadesi için IBAN', 'label_en' => 'IBAN for deposit refund', 'label_de' => 'IBAN'],
                ],
                'is_premium' => true, 'sort_order' => 55, 'is_active' => true,
            ],
            [
                'slug' => 'untermietvertrag',
                'category' => 'housing',
                'doc_type' => 'contract',
                'title_tr' => 'Alt Kira Sözleşmesi (Untermietvertrag)',
                'title_en' => 'Sublease Agreement (Untermietvertrag)',
                'title_de' => 'Untermietvertrag – Mustervorlage',
                'description_tr' => 'Oda/daire alt kiralaması için örnek Almanca sözleşme — taraflar, kira, kaution, süre. (Örnek metindir, hukuki danışmanlık değildir.)',
                'description_en' => 'A sample German sublease agreement for a room/flat — parties, rent, deposit, term. (Sample text, not legal advice.)',
                'description_de' => 'Muster-Untermietvertrag für Zimmer/Wohnung — Parteien, Miete, Kaution, Laufzeit. (Mustertext, keine Rechtsberatung.)',
                'body_de' => $unter,
                'body_en' => null,
                'guide_tr' => "⚠️ Bu bir **örnek** sözleşmedir, hukuki danışmanlık değildir; önemli durumlarda uzmana danış.\n\n- **En kritik:** Hauptvermieter'in (asıl ev sahibi) **alt kiraya iznini** al — izinsiz Untermiete fesih sebebi olabilir.\n- **Befristung** (`[BEFRISTUNG]`): süreli mi (befristet bis …) süresiz mi (unbefristet) net yaz.\n- Kira **Nebenkosten dahil mi** belirt; ödeme için IBAN ver.\n- **İki nüsha** yazdır, her iki taraf da imzalasın.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "⚠️ This is a **sample** agreement, not legal advice; consult a professional for important cases.\n\n- **Most important:** get the main landlord's **permission to sublet** — an unauthorised Untermiete can be grounds for termination.\n- **Term** (`[BEFRISTUNG]`): state clearly whether fixed-term (befristet bis …) or open-ended.\n- Specify whether rent **includes utilities**; give an IBAN.\n- Print **two copies**, both parties sign.\n- Fill every `[BRACKET]`.",
                'guide_de' => "⚠️ Mustertext, **keine Rechtsberatung** — im Zweifel Fachrat einholen.\n\n- **Wichtig:** Erlaubnis des Hauptvermieters zur Untervermietung einholen.\n- **Befristung** klar angeben.\n- Miete inkl. Nebenkosten? IBAN angeben.\n- **Zwei Exemplare** drucken, beide unterschreiben.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'HAUPTMIETER_NAME', 'label_tr' => 'Asıl kiracı (sen) adı', 'label_en' => 'Main tenant name', 'label_de' => 'Name Hauptmieter'],
                    ['key' => 'UNTERMIETER_NAME', 'label_tr' => 'Alt kiracı adı', 'label_en' => 'Subtenant name', 'label_de' => 'Name Untermieter'],
                    ['key' => 'ZIMMER_BESCHREIBUNG', 'label_tr' => 'Oda tanımı (örn. 18 m² möbleli oda)', 'label_en' => 'Room description', 'label_de' => 'Zimmerbeschreibung'],
                    ['key' => 'BEFRISTUNG', 'label_tr' => 'Süre (befristet bis … / unbefristet)', 'label_en' => 'Term (fixed/open-ended)', 'label_de' => 'Befristung'],
                    ['key' => 'MIETE', 'label_tr' => 'Aylık kira', 'label_en' => 'Monthly rent', 'label_de' => 'Miete'],
                    ['key' => 'KAUTION', 'label_tr' => 'Kaution tutarı', 'label_en' => 'Deposit amount', 'label_de' => 'Kaution'],
                ],
                'is_premium' => true, 'sort_order' => 90, 'is_active' => true,
            ],
        ];
    }
};

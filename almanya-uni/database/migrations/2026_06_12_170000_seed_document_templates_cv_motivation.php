<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * CV + Motivationsschreiben ÇEŞİTLERİ (kullanıcı isteği). 2 yeni CV (İngilizce/
 * uluslararası CV, akademik CV) + 3 yeni motivasyon (Bachelor, PhD/Promotion,
 * Ausbildung). Idempotent (slug). Mevcut lebenslauf + motivationsschreiben'i tamamlar.
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
                'lebenslauf-englisch', 'lebenslauf-akademisch',
                'motivationsschreiben-bachelor', 'motivationsschreiben-phd', 'motivationsschreiben-ausbildung',
            ])->delete();
        }
    }

    private function templates(): array
    {
        $cvEn = <<<'TXT'
CURRICULUM VITAE

[FULL_NAME]
[ADDRESS], [POSTCODE] [CITY], Germany
Phone: [PHONE]  ·  Email: [EMAIL]
Date of birth: [DOB]  ·  Nationality: [NATIONALITY]
LinkedIn: [LINKEDIN]

────────────────────────────────────────

EDUCATION

[FROM]–[TO]   [DEGREE] in [FIELD]
              [UNIVERSITY], [CITY_COUNTRY]
              Focus: [FOCUS]  ·  Grade: [GRADE]

[FROM]–[TO]   [HIGH_SCHOOL], [CITY_COUNTRY]

────────────────────────────────────────

EXPERIENCE

[FROM]–[TO]   [JOB_TITLE], [COMPANY], [CITY]
              · [ACHIEVEMENT_1]
              · [ACHIEVEMENT_2]

────────────────────────────────────────

SKILLS

Languages:   Turkish (native), English ([ENGLISH_LEVEL]), German ([GERMAN_LEVEL])
Technical:   [TECHNICAL_SKILLS]

────────────────────────────────────────

[OPTIONAL_SECTIONS]
TXT;

        $cvAcad = <<<'TXT'
ACADEMIC CV  ·  WISSENSCHAFTLICHER LEBENSLAUF

[FULL_NAME]
[ADDRESS]  ·  [EMAIL]  ·  [PHONE]

────────────────────────────────────────

RESEARCH INTERESTS
[RESEARCH_INTERESTS]

EDUCATION
[FROM]–[TO]   [DEGREE], [UNIVERSITY]
              Thesis: "[THESIS_TITLE]"  (Supervisor: [SUPERVISOR])  ·  Grade: [GRADE]

RESEARCH EXPERIENCE
[FROM]–[TO]   [ROLE], [INSTITUTE]
              [RESEARCH_DESCRIPTION]

PUBLICATIONS
[PUBLICATIONS]

CONFERENCES & TALKS
[CONFERENCES]

TEACHING EXPERIENCE
[TEACHING]

SCHOLARSHIPS & AWARDS
[AWARDS]

TECHNICAL & LANGUAGE SKILLS
[SKILLS]

REFERENCES
[REFERENCES]
TXT;

        $motBachelor = <<<'TXT'
[VORNAME] [NACHNAME]
[ADRESSE], [PLZ] [STADT]
[EMAIL]  ·  [TELEFON]

[HOCHSCHULE]
Zulassungsstelle – [STUDIENGANG]
[ORT]

[ORT], [DATUM]


Betreff: Motivationsschreiben für den Bachelorstudiengang [STUDIENGANG]


Sehr geehrte Damen und Herren,

mit großem Interesse bewerbe ich mich um einen Studienplatz im Bachelorstudiengang
[STUDIENGANG] an der [HOCHSCHULE]. [EINLEITUNG_WARUM_DIESES_FACH]

In der Schule haben mich besonders [SCHULFAECHER] begeistert. [KONKRETE_ERFAHRUNG]
hat meinen Wunsch bestärkt, dieses Fach vertieft zu studieren.

Für ein Studium in Deutschland und speziell an der [HOCHSCHULE] entscheide ich
mich, weil [WARUM_DEUTSCHLAND_UNI]. Auf das Studium bereite ich mich sprachlich
durch [SPRACHKENNTNISSE] vor.

Nach dem Bachelor möchte ich [ZUKUNFTSZIEL]. Ich bin motiviert, fleißig und
bereit, mich den Herausforderungen eines Auslandsstudiums zu stellen.

Über eine positive Rückmeldung würde ich mich sehr freuen.

Mit freundlichen Grüßen
[VORNAME] [NACHNAME]
TXT;

        $motPhd = <<<'TXT'
[VORNAME] [NACHNAME]
[ADRESSE], [PLZ] [STADT]
[EMAIL]  ·  [TELEFON]

[PROFESSOR_TITEL] [PROFESSOR_NAME]
[INSTITUT], [HOCHSCHULE]
[ORT]

[ORT], [DATUM]


Betreff: Bewerbung um eine Promotionsstelle im Bereich [FORSCHUNGSBEREICH]


Sehr geehrte/r [PROFESSOR_TITEL] [PROFESSOR_NAME],

mit großem Interesse verfolge ich die Forschung Ihrer Arbeitsgruppe zu [THEMA].
Insbesondere [KONKRETE_ARBEIT] hat mein Interesse geweckt, da es eng an meine
bisherige Arbeit anknüpft.

In meiner Masterarbeit "[MASTERARBEIT_TITEL]" habe ich [METHODEN_ERGEBNISSE].
Dabei konnte ich Erfahrung in [KOMPETENZEN] sammeln.

Für eine Promotion in Ihrer Gruppe interessiert mich besonders [FORSCHUNGSIDEE].
Ich bin überzeugt, mit meinem Hintergrund in [HINTERGRUND] einen Beitrag zu Ihren
Projekten leisten zu können.

Über die Möglichkeit eines Gesprächs zu möglichen Promotionsthemen würde ich mich
sehr freuen. Meine Unterlagen (Lebenslauf, Zeugnisse, Schriftproben) füge ich bei.

Mit freundlichen Grüßen
[VORNAME] [NACHNAME]
TXT;

        $motAusbildung = <<<'TXT'
[VORNAME] [NACHNAME]
[ADRESSE], [PLZ] [STADT]
[EMAIL]  ·  [TELEFON]

[UNTERNEHMEN]
[ANSPRECHPARTNER]
[UNTERNEHMEN_ADRESSE]

[ORT], [DATUM]


Betreff: Bewerbung um einen Ausbildungsplatz als [AUSBILDUNGSBERUF] ab [BEGINN]


Sehr geehrte/r [ANSPRECHPARTNER],

mit großem Interesse habe ich Ihre Ausschreibung für eine Ausbildung als
[AUSBILDUNGSBERUF] gelesen und bewerbe mich hiermit um einen Ausbildungsplatz
ab [BEGINN].

Für diesen Beruf interessiere ich mich, weil [WARUM_BERUF]. Durch [ERFAHRUNG_PRAKTIKUM]
konnte ich bereits einen Einblick gewinnen und meine Eignung feststellen.

Ihr Unternehmen spricht mich besonders an, weil [WARUM_FIRMA]. Ich bringe
[STAERKEN] mit und bin motiviert, mich engagiert in Ihr Team einzubringen.

Über eine Einladung zu einem Gespräch oder einem Probetag würde ich mich sehr
freuen.

Mit freundlichen Grüßen
[VORNAME] [NACHNAME]
TXT;

        return [
            [
                'slug' => 'lebenslauf-englisch',
                'category' => 'application',
                'doc_type' => 'cv',
                'title_tr' => 'İngilizce / Uluslararası CV',
                'title_en' => 'English / International CV',
                'title_de' => 'Englischer Lebenslauf (CV)',
                'description_tr' => 'İngilizce eğitim veren programlar ve uluslararası iş başvuruları için İngilizce CV — fotoğrafsız, eylem fiilli, tek sayfa.',
                'description_en' => 'An English CV for English-taught programmes and international job applications — no photo, action verbs, one page.',
                'description_de' => 'Englischer CV für englischsprachige Programme und internationale Bewerbungen.',
                'body_de' => $cvEn,
                'body_en' => $cvEn,
                'guide_tr' => "İngilizce/uluslararası CV, Alman Lebenslauf'tan **farklıdır**.\n\n- **Fotoğraf YOK, doğum tarihi opsiyonel, imza yok** (Anglo-Sakson standardı).\n- **Eylem fiilleriyle** başarı yaz: \"led\", \"built\", \"increased … by 20%\".\n- **Ters kronolojik**, öğrenci için **tek sayfa**.\n- İngilizce programa başvuruyorsan bunu kullan; Almanca işe Lebenslauf'u.\n- `[OPTIONAL_SECTIONS]`: Projects, Certifications, Volunteering ekleyebilirsin.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "An English/international CV is **different** from the German Lebenslauf.\n\n- **No photo, date of birth optional, no signature** (Anglo-Saxon standard).\n- Write achievements with **action verbs**: \"led\", \"built\", \"increased … by 20%\".\n- **Reverse-chronological**, **one page** for students.\n- Use this for English-taught programmes; use the Lebenslauf for German jobs.\n- `[OPTIONAL_SECTIONS]`: add Projects, Certifications, Volunteering.\n- Fill every `[BRACKET]`.",
                'guide_de' => "Ein englischer CV unterscheidet sich vom Lebenslauf.\n\n- **Kein Foto, kein Geburtsdatum nötig, keine Unterschrift.**\n- Erfolge mit **Aktionsverben**.\n- **Umgekehrt chronologisch**, eine Seite.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'FULL_NAME', 'label_tr' => 'Ad soyad', 'label_en' => 'Full name', 'label_de' => 'Voller Name'],
                    ['key' => 'DEGREE', 'label_tr' => 'Derece (BSc/MSc…)', 'label_en' => 'Degree', 'label_de' => 'Abschluss'],
                    ['key' => 'FIELD', 'label_tr' => 'Alan/bölüm', 'label_en' => 'Field of study', 'label_de' => 'Fachrichtung'],
                    ['key' => 'ACHIEVEMENT_1', 'label_tr' => 'Başarı 1 (eylem fiili + sonuç)', 'label_en' => 'Achievement 1', 'label_de' => 'Leistung 1'],
                    ['key' => 'ENGLISH_LEVEL', 'label_tr' => 'İngilizce seviyen (örn. C1/IELTS 7)', 'label_en' => 'English level', 'label_de' => 'Englisch-Niveau'],
                    ['key' => 'TECHNICAL_SKILLS', 'label_tr' => 'Teknik beceriler', 'label_en' => 'Technical skills', 'label_de' => 'Technische Kenntnisse'],
                ],
                'is_premium' => true, 'sort_order' => 12, 'is_active' => true,
            ],
            [
                'slug' => 'lebenslauf-akademisch',
                'category' => 'application',
                'doc_type' => 'cv',
                'title_tr' => 'Akademik CV (PhD / Araştırma)',
                'title_en' => 'Academic CV (PhD / Research)',
                'title_de' => 'Akademischer Lebenslauf (Promotion)',
                'description_tr' => 'Doktora/araştırma başvuruları için akademik CV — yayınlar, konferanslar, ders deneyimi, burslar, referanslar. (2-4 sayfa olabilir.)',
                'description_en' => 'An academic CV for PhD/research applications — publications, conferences, teaching, awards, references. (May be 2–4 pages.)',
                'description_de' => 'Akademischer Lebenslauf für Promotion/Forschung — Publikationen, Konferenzen, Lehre, Auszeichnungen.',
                'body_de' => $cvAcad,
                'body_en' => $cvAcad,
                'guide_tr' => "Akademik CV, normal CV'den **uzun ve detaylıdır** (2-4 sayfa olabilir).\n\n- **Tek sayfa kuralı geçerli değil** — yayın, konferans, ders deneyimini tam listele.\n- **Yayınlar** standart bir atıf formatında (örn. APA) olsun.\n- **Tez başlığı + danışman** mutlaka.\n- **Referanslar** (genelde 2-3 akademisyen) ekle — adı, kurumu, e-postası.\n- Boş bölüm varsa (henüz yayın yoksa) o başlığı silebilirsin.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "An academic CV is **longer and more detailed** than a normal CV (may be 2–4 pages).\n\n- The **one-page rule does not apply** — fully list publications, conferences, teaching.\n- Format **publications** in a standard citation style (e.g. APA).\n- Always include your **thesis title + supervisor**.\n- Add **references** (usually 2–3 academics): name, institution, email.\n- Delete any empty section (e.g. if you have no publications yet).\n- Fill every `[BRACKET]`.",
                'guide_de' => "Ein akademischer Lebenslauf ist **länger und detaillierter** (2–4 Seiten).\n\n- Publikationen, Konferenzen, Lehre vollständig auflisten.\n- **Thementitel + Betreuer** angeben.\n- **Referenzen** (2–3 Personen) hinzufügen.\n- Leere Abschnitte entfernen.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'RESEARCH_INTERESTS', 'label_tr' => 'Araştırma ilgi alanların', 'label_en' => 'Research interests', 'label_de' => 'Forschungsinteressen'],
                    ['key' => 'THESIS_TITLE', 'label_tr' => 'Tez başlığın', 'label_en' => 'Thesis title', 'label_de' => 'Titel der Abschlussarbeit'],
                    ['key' => 'SUPERVISOR', 'label_tr' => 'Danışmanın', 'label_en' => 'Supervisor', 'label_de' => 'Betreuer'],
                    ['key' => 'PUBLICATIONS', 'label_tr' => 'Yayınların (atıf formatında)', 'label_en' => 'Publications', 'label_de' => 'Publikationen'],
                    ['key' => 'CONFERENCES', 'label_tr' => 'Konferans/sunumların', 'label_en' => 'Conferences & talks', 'label_de' => 'Konferenzen'],
                    ['key' => 'REFERENCES', 'label_tr' => 'Referanslar (ad, kurum, e-posta)', 'label_en' => 'References', 'label_de' => 'Referenzen'],
                ],
                'is_premium' => true, 'sort_order' => 14, 'is_active' => true,
            ],
            [
                'slug' => 'motivationsschreiben-bachelor',
                'category' => 'application',
                'doc_type' => 'letter',
                'title_tr' => 'Motivasyon Mektubu — Bachelor',
                'title_en' => 'Motivation Letter — Bachelor',
                'title_de' => 'Motivationsschreiben — Bachelor',
                'description_tr' => 'Lisans (Bachelor) başvurusu için motivasyon mektubu — lise ilgisinden bölüm seçimine, neden Almanya ve bu üniversite.',
                'description_en' => 'A motivation letter for a Bachelor\'s application — from school interests to your choice of field, why Germany and this university.',
                'description_de' => 'Motivationsschreiben für die Bachelor-Bewerbung — Schulinteressen, Fachwahl, warum Deutschland.',
                'body_de' => $motBachelor,
                'body_en' => null,
                'guide_tr' => "Bachelor motivasyonu, master'dan farklı: henüz iş/proje yok, **potansiyel ve ilgi** anlatılır.\n\n- **Lise derslerinden** ilgine köprü kur (`[SCHULFAECHER]`).\n- Somut bir deneyim/olay ver (`[KONKRETE_ERFAHRUNG]`) — yarışma, proje, gönüllülük.\n- **Neden Almanya + bu üniversite** (`[WARUM_DEUTSCHLAND_UNI]`).\n- Dil hazırlığını belirt; uluslararası öğrenci olarak motivasyonunu göster.\n- Abartısız, samimi ve hevesli ol.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "A Bachelor motivation differs from a Master's: with no jobs/projects yet, you show **potential and interest**.\n\n- Bridge from your **school subjects** to your interest (`[SCHULFAECHER]`).\n- Give a concrete experience (`[KONKRETE_ERFAHRUNG]`) — a competition, project, volunteering.\n- Explain **why Germany + this university** (`[WARUM_DEUTSCHLAND_UNI]`).\n- Mention your language preparation.\n- Be sincere and enthusiastic, not boastful.\n- Fill every `[BRACKET]`.",
                'guide_de' => "Eine Bachelor-Motivation zeigt **Potenzial und Interesse** statt Berufserfahrung.\n\n- Von **Schulfächern** zum Interesse überleiten.\n- Konkrete Erfahrung nennen.\n- **Warum Deutschland + diese Uni** erklären.\n- Sprachvorbereitung erwähnen.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'STUDIENGANG', 'label_tr' => 'Başvurduğun bölüm', 'label_en' => 'Study programme', 'label_de' => 'Studiengang'],
                    ['key' => 'HOCHSCHULE', 'label_tr' => 'Üniversite', 'label_en' => 'University', 'label_de' => 'Hochschule'],
                    ['key' => 'SCHULFAECHER', 'label_tr' => 'Sevdiğin lise dersleri', 'label_en' => 'Favourite school subjects', 'label_de' => 'Schulfächer'],
                    ['key' => 'KONKRETE_ERFAHRUNG', 'label_tr' => 'İlgini pekiştiren somut deneyim', 'label_en' => 'A concrete experience', 'label_de' => 'Konkrete Erfahrung'],
                    ['key' => 'WARUM_DEUTSCHLAND_UNI', 'label_tr' => 'Neden Almanya + bu üniversite', 'label_en' => 'Why Germany + this university', 'label_de' => 'Warum Deutschland + Uni'],
                    ['key' => 'ZUKUNFTSZIEL', 'label_tr' => 'Gelecek hedefin', 'label_en' => 'Future goal', 'label_de' => 'Zukunftsziel'],
                ],
                'is_premium' => true, 'sort_order' => 21, 'is_active' => true,
            ],
            [
                'slug' => 'motivationsschreiben-phd',
                'category' => 'application',
                'doc_type' => 'letter',
                'title_tr' => 'Motivasyon / Başvuru — Doktora (Promotion)',
                'title_en' => 'Motivation Letter — PhD (Promotion)',
                'title_de' => 'Motivationsschreiben — Promotion',
                'description_tr' => 'Bir profesöre doktora/araştırma pozisyonu başvurusu — onun araştırmasıyla bağ, master tezin, getireceğin katkı.',
                'description_en' => 'An application to a professor for a PhD/research position — connection to their research, your Master\'s thesis, your contribution.',
                'description_de' => 'Bewerbung bei einer Professorin/einem Professor um eine Promotionsstelle.',
                'body_de' => $motPhd,
                'body_en' => null,
                'guide_tr' => "PhD başvurusu **kişiye özeldir** — profesörü ve grubunu gerçekten araştır.\n\n- **Onun spesifik çalışmasına** atıf yap (`[KONKRETE_ARBEIT]`) — genel \"araştırmanız ilginç\" zayıf.\n- **Master tezinden** somut yöntem/sonuç ver (`[METHODEN_ERGEBNISSE]`).\n- **Kendi araştırma fikrini** sun (`[FORSCHUNGSIDEE]`) — sadece \"öğrenmek istiyorum\" değil, katkı.\n- Kısa tut (1 sayfa); CV + transkript + yazı örneği iliştir.\n- İsimle ve doğru unvanla hitap et (Prof. Dr.).\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "A PhD application is **personal** — genuinely research the professor and their group.\n\n- Reference their **specific work** (`[KONKRETE_ARBEIT]`) — a generic \"your research is interesting\" is weak.\n- Give concrete methods/results from your **Master's thesis** (`[METHODEN_ERGEBNISSE]`).\n- Propose your **own research idea** (`[FORSCHUNGSIDEE]`) — contribution, not just \"I want to learn\".\n- Keep it short (1 page); attach CV + transcript + writing sample.\n- Address them by name and correct title (Prof. Dr.).\n- Fill every `[BRACKET]`.",
                'guide_de' => "Eine Promotionsbewerbung ist **persönlich** — Gruppe genau recherchieren.\n\n- Auf **konkrete Arbeit** Bezug nehmen.\n- Methoden/Ergebnisse der **Masterarbeit** nennen.\n- **Eigene Forschungsidee** vorschlagen.\n- Kurz halten; Unterlagen beifügen.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'PROFESSOR_NAME', 'label_tr' => 'Profesörün adı', 'label_en' => 'Professor\'s name', 'label_de' => 'Name Professor'],
                    ['key' => 'THEMA', 'label_tr' => 'Grubun araştırma teması', 'label_en' => 'The group\'s research theme', 'label_de' => 'Thema'],
                    ['key' => 'KONKRETE_ARBEIT', 'label_tr' => 'Onun spesifik bir çalışması/makalesi', 'label_en' => 'A specific paper/work of theirs', 'label_de' => 'Konkrete Arbeit'],
                    ['key' => 'MASTERARBEIT_TITEL', 'label_tr' => 'Master tezinin başlığı', 'label_en' => 'Your Master\'s thesis title', 'label_de' => 'Masterarbeit-Titel'],
                    ['key' => 'FORSCHUNGSIDEE', 'label_tr' => 'Önerdiğin araştırma fikri', 'label_en' => 'Your proposed research idea', 'label_de' => 'Forschungsidee'],
                    ['key' => 'KOMPETENZEN', 'label_tr' => 'Edindiğin yetkinlikler', 'label_en' => 'Skills you gained', 'label_de' => 'Kompetenzen'],
                ],
                'is_premium' => true, 'sort_order' => 23, 'is_active' => true,
            ],
            [
                'slug' => 'motivationsschreiben-ausbildung',
                'category' => 'career',
                'doc_type' => 'letter',
                'title_tr' => 'Motivasyon / Başvuru — Ausbildung',
                'title_en' => 'Motivation Letter — Ausbildung (Vocational)',
                'title_de' => 'Motivationsschreiben — Ausbildung',
                'description_tr' => 'İkili mesleki eğitim (Ausbildung) başvurusu için Anschreiben — neden bu meslek, neden bu firma, güçlü yanların.',
                'description_en' => 'A cover letter for a dual vocational training (Ausbildung) — why this profession, why this company, your strengths.',
                'description_de' => 'Anschreiben für eine Ausbildung — warum dieser Beruf, warum diese Firma, deine Stärken.',
                'body_de' => $motAusbildung,
                'body_en' => null,
                'guide_tr' => "Ausbildung başvurusu **iş başvurusu gibidir** ama motivasyon ve uygunluk ön planda.\n\n- **Neden bu meslek** (`[WARUM_BERUF]`) — staj/deneyim ile destekle (`[ERFAHRUNG_PRAKTIKUM]`).\n- **Neden bu firma** (`[WARUM_FIRMA]`) → firmayı araştır.\n- **Başlama tarihi** (Ausbildung genelde Ağustos/Eylül başlar) belirt.\n- Almanca seviyen önemli — net konuşma/yazma yeteneğini göster (mektubun kendisi kanıt).\n- Probetag (deneme günü) teklifini değerlendir.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "An Ausbildung application is **like a job application** but motivation and fit come first.\n\n- **Why this profession** (`[WARUM_BERUF]`) — back it up with an internship/experience (`[ERFAHRUNG_PRAKTIKUM]`).\n- **Why this company** (`[WARUM_FIRMA]`) → research it.\n- State your **start date** (Ausbildung usually starts Aug/Sept).\n- Your German level matters — the letter itself is proof of clear writing.\n- Consider offering a trial day (Probetag).\n- Fill every `[BRACKET]`.",
                'guide_de' => "Eine Ausbildungsbewerbung ist **wie eine Jobbewerbung**, Motivation zählt.\n\n- **Warum dieser Beruf** — mit Praktikum belegen.\n- **Warum diese Firma** zeigen.\n- **Beginn** angeben (meist Aug/Sept).\n- Deutschniveau wichtig.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'AUSBILDUNGSBERUF', 'label_tr' => 'Meslek (örn. Fachinformatiker)', 'label_en' => 'Vocation', 'label_de' => 'Ausbildungsberuf'],
                    ['key' => 'BEGINN', 'label_tr' => 'Başlama tarihi', 'label_en' => 'Start date', 'label_de' => 'Beginn'],
                    ['key' => 'WARUM_BERUF', 'label_tr' => 'Bu mesleği neden istiyorsun', 'label_en' => 'Why this profession', 'label_de' => 'Warum dieser Beruf'],
                    ['key' => 'ERFAHRUNG_PRAKTIKUM', 'label_tr' => 'İlgili staj/deneyimin', 'label_en' => 'Relevant internship/experience', 'label_de' => 'Erfahrung/Praktikum'],
                    ['key' => 'WARUM_FIRMA', 'label_tr' => 'Bu firmayı neden seçtin', 'label_en' => 'Why this company', 'label_de' => 'Warum diese Firma'],
                    ['key' => 'STAERKEN', 'label_tr' => 'Güçlü yanların', 'label_en' => 'Your strengths', 'label_de' => 'Stärken'],
                ],
                'is_premium' => true, 'sort_order' => 64, 'is_active' => true,
            ],
        ];
    }
};

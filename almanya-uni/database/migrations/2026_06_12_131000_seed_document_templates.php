<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * İlk 4 profesyonel başvuru şablonu (premium içerik). Gerçek Almanca belge
 * formatları — Lebenslauf, Motivationsschreiben, Empfehlungsschreiben, başvuru
 * durumu e-postası. Idempotent (slug). Admin'den düzenlenebilir.
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
            $slug = $t['slug'];
            DB::table('document_templates')->updateOrInsert(
                ['slug' => $slug],
                array_merge($t, ['updated_at' => $now, 'created_at' => $now])
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('document_templates')) {
            DB::table('document_templates')->whereIn('slug', [
                'lebenslauf', 'motivationsschreiben', 'empfehlungsschreiben', 'bewerbungsstatus-anfrage',
            ])->delete();
        }
    }

    private function templates(): array
    {
        $lebenslauf = <<<'TXT'
LEBENSLAUF

[VORNAME] [NACHNAME]
[ADRESSE], [PLZ] [STADT]
Tel.: [TELEFON]  ·  E-Mail: [EMAIL]
Geburtsdatum: [GEBURTSDATUM]  ·  Geburtsort: [GEBURTSORT]
Staatsangehörigkeit: [STAATSANGEHOERIGKEIT]

────────────────────────────────────────

STUDIUM / AUSBILDUNG

[VON]–[BIS]   [STUDIENGANG], [HOCHSCHULE], [ORT/LAND]
              Schwerpunkt: [SCHWERPUNKT]
              Abschluss: [ABSCHLUSS] (Note: [NOTE])

[VON]–[BIS]   [SCHULE], [ORT/LAND]
              Abschluss: [SCHULABSCHLUSS]

────────────────────────────────────────

PRAKTISCHE ERFAHRUNG

[VON]–[BIS]   [POSITION], [UNTERNEHMEN], [ORT]
              [TAETIGKEIT_KURZ]

────────────────────────────────────────

SPRACHKENNTNISSE

Türkisch:   Muttersprache
Deutsch:    [DEUTSCH_NIVEAU]  (z. B. B2 / TestDaF 4×4)
Englisch:   [ENGLISCH_NIVEAU]

────────────────────────────────────────

EDV-KENNTNISSE

[EDV_KENNTNISSE]

────────────────────────────────────────

[ORT], [DATUM]


[VORNAME] [NACHNAME]
TXT;

        $motivationDe = <<<'TXT'
[VORNAME] [NACHNAME]
[ADRESSE], [PLZ] [STADT]
[EMAIL]  ·  [TELEFON]

[HOCHSCHULE]
[STUDIENGANG] – Zulassungsstelle
[ORT]

[ORT], [DATUM]


Betreff: Motivationsschreiben für den Masterstudiengang [STUDIENGANG]


Sehr geehrte Damen und Herren,

mit großem Interesse bewerbe ich mich um einen Studienplatz im Masterprogramm
[STUDIENGANG] an der [HOCHSCHULE]. [EINLEITUNG_WARUM_DIESES_FACH]

Während meines Bachelorstudiums in [BACHELOR_FACH] an der [BACHELOR_HOCHSCHULE]
habe ich [RELEVANTE_KENNTNISSE]. Besonders [SCHWERPUNKT_INTERESSE] hat mein
Interesse für eine Vertiefung in diesem Bereich geweckt.

Ihr Studiengang überzeugt mich besonders durch [WARUM_DIESE_UNI]. Die Verbindung
von [PROGRAMM_STAERKE] entspricht genau meinen fachlichen Zielen.

Nach meinem Abschluss möchte ich [BERUFSZIEL]. Das Masterprogramm an der
[HOCHSCHULE] ist für diesen Weg der ideale nächste Schritt.

Über die Möglichkeit, mich persönlich vorzustellen, würde ich mich sehr freuen.

Mit freundlichen Grüßen
[VORNAME] [NACHNAME]
TXT;

        $motivationEn = <<<'TXT'
[VORNAME] [NACHNAME]
[ADRESSE], [PLZ] [STADT]
[EMAIL]  ·  [TELEFON]

[HOCHSCHULE]
Admissions Office – [STUDIENGANG]
[ORT]

[ORT], [DATUM]


Subject: Letter of Motivation – Master's programme [STUDIENGANG]


Dear Admissions Committee,

I am writing to apply for the Master's programme in [STUDIENGANG] at
[HOCHSCHULE]. [EINLEITUNG_WARUM_DIESES_FACH]

During my Bachelor's studies in [BACHELOR_FACH] at [BACHELOR_HOCHSCHULE], I
[RELEVANTE_KENNTNISSE]. In particular, [SCHWERPUNKT_INTERESSE] sparked my
interest in specialising in this field.

Your programme stands out to me because of [WARUM_DIESE_UNI]. The combination of
[PROGRAMM_STAERKE] aligns precisely with my academic goals.

After graduating, I intend to [BERUFSZIEL]. The Master's programme at
[HOCHSCHULE] is the ideal next step toward this goal.

I would welcome the opportunity to introduce myself in person.

Yours sincerely,
[VORNAME] [NACHNAME]
TXT;

        $empfehlung = <<<'TXT'
[HOCHSCHULE_GUTACHTER]
[GUTACHTER_NAME], [GUTACHTER_TITEL]
[GUTACHTER_EMAIL]

[ORT], [DATUM]


Empfehlungsschreiben für [VORNAME] [NACHNAME]


Sehr geehrte Damen und Herren,

als [GUTACHTER_TITEL] an der [HOCHSCHULE_GUTACHTER] kenne ich Frau/Herrn
[NACHNAME] seit [ZEITRAUM] aus [KONTEXT]. Ich empfehle sie/ihn nachdrücklich
für [ZIEL].

Frau/Herr [NACHNAME] gehörte mit [NOTE_RANG] zu den besten Studierenden meines
Kurses [KURSNAME]. Besonders hervorzuheben sind [STAERKEN].

[KONKRETES_BEISPIEL]

Aufgrund der fachlichen Qualifikation und der persönlichen Eigenschaften bin ich
überzeugt, dass Frau/Herr [NACHNAME] das Studium an Ihrer Hochschule mit großem
Erfolg absolvieren wird.

Für Rückfragen stehe ich Ihnen gerne zur Verfügung.

Mit freundlichen Grüßen
[GUTACHTER_NAME]
[GUTACHTER_TITEL]
TXT;

        $statusEmail = <<<'TXT'
Betreff: Nachfrage zum Bewerbungsstatus – [STUDIENGANG] ([BEWERBUNGSNUMMER])


Sehr geehrte Damen und Herren,

am [BEWERBUNGSDATUM] habe ich mich für den Studiengang [STUDIENGANG] zum
[SEMESTER] beworben (Bewerbungsnummer: [BEWERBUNGSNUMMER]).

Da die im Portal angegebene Bearbeitungszeit inzwischen verstrichen ist, möchte
ich höflich nach dem aktuellen Stand meiner Bewerbung fragen. Sollten noch
Unterlagen fehlen, lassen Sie es mich bitte wissen – ich reiche sie umgehend nach.

Vielen Dank im Voraus für Ihre Rückmeldung.

Mit freundlichen Grüßen
[VORNAME] [NACHNAME]
[EMAIL]  ·  [TELEFON]
TXT;

        return [
            [
                'slug' => 'lebenslauf',
                'category' => 'application',
                'doc_type' => 'cv',
                'title_tr' => 'Lebenslauf (Almanca CV) Şablonu',
                'title_en' => 'Lebenslauf (German CV) Template',
                'title_de' => 'Lebenslauf-Vorlage (tabellarisch)',
                'description_tr' => 'Alman üniversite ve iş başvurularının beklediği tablo biçimli (tabellarisch) CV. Ters kronolojik, 1–2 sayfa.',
                'description_en' => 'The tabular (tabellarisch) CV German universities and employers expect — reverse-chronological, 1–2 pages.',
                'description_de' => 'Tabellarischer Lebenslauf nach deutschem Standard — umgekehrt chronologisch, 1–2 Seiten.',
                'body_de' => $lebenslauf,
                'body_en' => null,
                'guide_tr' => "Almanya'da CV **tabellarischer Lebenslauf** denen tablo biçimindedir; düz paragraf değil.\n\n- **Ters kronolojik:** En yeni eğitim/deneyim en üstte.\n- **1–2 sayfa**, sade, tek yazı tipi. Süslü tasarım yok.\n- **Fotoğraf** artık zorunlu değil (AGG sonrası) ama Türk öğrenciler için hâlâ yaygın — vesikalık ekleyebilirsin.\n- **Tarih + imza** en alta (Almanlar bunu bekler).\n- Diller bölümünde seviyeyi **CEFR** ile yaz (B2, C1) ve sınavı belirt (TestDaF 4×4, DSH-2).\n- Tüm `[KÖŞELİ]` alanları kendi bilginle değiştir, köşeli parantezleri sil.",
                'guide_en' => "In Germany the CV is a **tabellarischer Lebenslauf** — a table-style layout, not prose.\n\n- **Reverse-chronological:** most recent education/experience first.\n- **1–2 pages**, clean, one font. No fancy design.\n- A **photo** is no longer required but still common — you may add a passport-style photo.\n- Add **place, date and your signature** at the bottom (Germans expect this).\n- State language levels with **CEFR** (B2, C1) and name the exam (TestDaF, DSH-2).\n- Replace every `[BRACKET]` field and delete the brackets.",
                'guide_de' => "Der Lebenslauf ist in Deutschland **tabellarisch** — keine Fließtext-Form.\n\n- **Umgekehrt chronologisch:** das Aktuellste zuerst.\n- **1–2 Seiten**, schlicht, eine Schriftart.\n- **Ort, Datum und Unterschrift** unten nicht vergessen.\n- Sprachniveaus mit **GER** angeben (B2, C1) und Prüfung nennen.\n- Alle `[KLAMMER]`-Felder ersetzen und Klammern entfernen.",
                'placeholders' => [
                    ['key' => 'VORNAME', 'label_tr' => 'Adın', 'label_en' => 'First name', 'label_de' => 'Vorname'],
                    ['key' => 'NACHNAME', 'label_tr' => 'Soyadın', 'label_en' => 'Last name', 'label_de' => 'Nachname'],
                    ['key' => 'GEBURTSDATUM', 'label_tr' => 'Doğum tarihi (GG.AA.YYYY)', 'label_en' => 'Date of birth', 'label_de' => 'Geburtsdatum'],
                    ['key' => 'STUDIENGANG', 'label_tr' => 'Okuduğun/okuyacağın bölüm', 'label_en' => 'Study programme', 'label_de' => 'Studiengang'],
                    ['key' => 'HOCHSCHULE', 'label_tr' => 'Üniversite adı', 'label_en' => 'University', 'label_de' => 'Hochschule'],
                    ['key' => 'NOTE', 'label_tr' => 'Not ortalaması', 'label_en' => 'Grade / GPA', 'label_de' => 'Note'],
                    ['key' => 'DEUTSCH_NIVEAU', 'label_tr' => 'Almanca seviyen (örn. B2)', 'label_en' => 'German level', 'label_de' => 'Deutsch-Niveau'],
                ],
                'is_premium' => true, 'sort_order' => 10, 'is_active' => true,
            ],
            [
                'slug' => 'motivationsschreiben',
                'category' => 'application',
                'doc_type' => 'letter',
                'title_tr' => 'Motivationsschreiben (Niyet Mektubu) Şablonu',
                'title_en' => 'Motivation Letter (Motivationsschreiben) Template',
                'title_de' => 'Motivationsschreiben-Vorlage (Master)',
                'description_tr' => 'Master başvurusu için niyet mektubu — neden bu bölüm, neden bu üniversite, kariyer hedefin. Almanca + İngilizce sürüm.',
                'description_en' => 'Motivation letter for a Master\'s application — why this field, why this university, your career goal. German + English version.',
                'description_de' => 'Motivationsschreiben für die Master-Bewerbung — Warum dieses Fach, warum diese Uni, Ihr Berufsziel.',
                'body_de' => $motivationDe,
                'body_en' => $motivationEn,
                'guide_tr' => "Motivationsschreiben başvurunun **kalbidir** — komisyon seni burada tanır.\n\n- **Bir sayfa**, 4–5 paragraf. Özgeçmişi tekrarlama; *neden*leri anlat.\n- **Somut ol:** \"yazılıma ilgim var\" yerine \"X projesinde Y yaptım\".\n- **Üniversiteyi araştır:** o bölümün spesifik dersi/laboratuvarı/profesörünü adıyla an (`[WARUM_DIESE_UNI]`).\n- **İngilizce programa** başvuruyorsan İngilizce sürümü kullan (yukarıda body değişir).\n- Klişe cümlelerden kaçın; kendi sesinle yaz.\n- Tüm `[KÖŞELİ]` alanları doldur, parantezleri sil.",
                'guide_en' => "The motivation letter is the **heart** of your application.\n\n- **One page**, 4–5 paragraphs. Don't repeat your CV — explain your *reasons*.\n- **Be concrete:** instead of \"I like software\", write \"in project X I did Y\".\n- **Research the university:** name a specific course, lab or professor (`[WARUM_DIESE_UNI]`).\n- For **English-taught programmes**, use the English version above.\n- Avoid clichés; write in your own voice.\n- Fill every `[BRACKET]` and remove the brackets.",
                'guide_de' => "Das Motivationsschreiben ist das **Herzstück** der Bewerbung.\n\n- **Eine Seite**, 4–5 Absätze. Kein Lebenslauf in Prosa — erklären Sie Ihre *Gründe*.\n- **Konkret sein:** statt „Ich interessiere mich für Software\" lieber „im Projekt X habe ich Y gemacht\".\n- **Uni recherchieren:** konkretes Modul, Labor oder Professor nennen.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'STUDIENGANG', 'label_tr' => 'Başvurduğun master programı', 'label_en' => 'Master programme', 'label_de' => 'Studiengang'],
                    ['key' => 'HOCHSCHULE', 'label_tr' => 'Hedef üniversite', 'label_en' => 'Target university', 'label_de' => 'Hochschule'],
                    ['key' => 'EINLEITUNG_WARUM_DIESES_FACH', 'label_tr' => 'Bu alanı neden seçtiğin (1–2 cümle)', 'label_en' => 'Why this field (1–2 sentences)', 'label_de' => 'Warum dieses Fach'],
                    ['key' => 'RELEVANTE_KENNTNISSE', 'label_tr' => 'Bachelor\'da kazandığın ilgili bilgi/proje', 'label_en' => 'Relevant skills/projects from your Bachelor', 'label_de' => 'Relevante Kenntnisse'],
                    ['key' => 'WARUM_DIESE_UNI', 'label_tr' => 'Bu üniversiteyi özel kılan şey', 'label_en' => 'What makes this university special', 'label_de' => 'Warum diese Uni'],
                    ['key' => 'BERUFSZIEL', 'label_tr' => 'Mezuniyet sonrası kariyer hedefin', 'label_en' => 'Career goal after graduation', 'label_de' => 'Berufsziel'],
                ],
                'is_premium' => true, 'sort_order' => 20, 'is_active' => true,
            ],
            [
                'slug' => 'empfehlungsschreiben',
                'category' => 'application',
                'doc_type' => 'letter',
                'title_tr' => 'Empfehlungsschreiben (Tavsiye Mektubu) Şablonu',
                'title_en' => 'Letter of Recommendation Template',
                'title_de' => 'Empfehlungsschreiben-Vorlage',
                'description_tr' => 'Hocandan/işverenden alacağın tavsiye mektubu — başvuru ve burslar için. Hocaya örnek olarak verebileceğin Almanca taslak.',
                'description_en' => 'A recommendation letter from your professor or employer — for applications and scholarships. A German draft to hand your referee.',
                'description_de' => 'Empfehlungsschreiben von Professor/in oder Arbeitgeber — für Bewerbung und Stipendien.',
                'body_de' => $empfehlung,
                'body_en' => null,
                'guide_tr' => "Çoğu hoca **\"taslağı sen yaz, ben düzeltip imzalayayım\"** der. Bu şablon tam da onun için.\n\n- Mektup **hocanın antetli kağıdında / e-postasından** çıkmalı (`[HOCHSCHULE_GUTACHTER]`).\n- **Somut örnek şart:** sıralama/not, spesifik proje veya ders performansı (`[KONKRETES_BEISPIEL]`).\n- Hocanın **seni ne kadar, hangi bağlamda tanıdığını** netleştir (`[ZEITRAUM]`, `[KONTEXT]`).\n- Abartma; inandırıcı ve spesifik olan güçlüdür.\n- Hazırlayıp hocaya **kibarca** sun; düzeltme payı bırak.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "Most professors say **\"draft it yourself, I'll edit and sign\"** — this template is exactly for that.\n\n- The letter should come from the **referee's letterhead / email** (`[HOCHSCHULE_GUTACHTER]`).\n- **A concrete example is essential:** ranking/grade, a specific project or course performance (`[KONKRETES_BEISPIEL]`).\n- Clarify **how long and in what context** the referee knows you.\n- Don't exaggerate; specific and credible beats grand claims.\n- Fill every `[BRACKET]`.",
                'guide_de' => "Viele Professoren sagen **„Entwerfen Sie es selbst, ich unterschreibe\"** — dafür ist diese Vorlage.\n\n- Der Brief sollte vom **Briefkopf/E-Mail der gutachtenden Person** stammen.\n- **Konkretes Beispiel** ist entscheidend: Rang/Note, ein bestimmtes Projekt.\n- Klären Sie **Zeitraum und Kontext** des Kennenlernens.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'GUTACHTER_NAME', 'label_tr' => 'Mektubu yazan hocanın adı', 'label_en' => 'Referee\'s name', 'label_de' => 'Name des Gutachters'],
                    ['key' => 'GUTACHTER_TITEL', 'label_tr' => 'Hocanın unvanı (Prof. Dr. …)', 'label_en' => 'Referee\'s title', 'label_de' => 'Titel'],
                    ['key' => 'ZEITRAUM', 'label_tr' => 'Seni ne zamandır tanıdığı', 'label_en' => 'How long they\'ve known you', 'label_de' => 'Zeitraum'],
                    ['key' => 'KONTEXT', 'label_tr' => 'Hangi bağlamda (ders/proje)', 'label_en' => 'In what context (course/project)', 'label_de' => 'Kontext'],
                    ['key' => 'NOTE_RANG', 'label_tr' => 'Notun/sıralaman', 'label_en' => 'Your grade/rank', 'label_de' => 'Note/Rang'],
                    ['key' => 'KONKRETES_BEISPIEL', 'label_tr' => 'Somut başarı örneği (1–2 cümle)', 'label_en' => 'Concrete achievement (1–2 sentences)', 'label_de' => 'Konkretes Beispiel'],
                ],
                'is_premium' => true, 'sort_order' => 30, 'is_active' => true,
            ],
            [
                'slug' => 'bewerbungsstatus-anfrage',
                'category' => 'application',
                'doc_type' => 'email',
                'title_tr' => 'Başvuru Durumu Sorma E-postası',
                'title_en' => 'Application Status Inquiry Email',
                'title_de' => 'Nachfrage zum Bewerbungsstatus (E-Mail)',
                'description_tr' => 'Başvurun uzun süredir cevapsızsa üniversiteye kibar ve doğru tonla durum soran kısa Almanca e-posta.',
                'description_en' => 'A short, correctly-toned German email to politely ask the university about your pending application.',
                'description_de' => 'Kurze, höfliche E-Mail an die Hochschule zum Stand der Bewerbung.',
                'body_de' => $statusEmail,
                'body_en' => null,
                'guide_tr' => "Almanya'da kurumlarla yazışmada **ton ve doğru format** çok önemlidir.\n\n- **Portaldaki süre dolmadan** yazma — sabırsız görünme.\n- Konu satırına mutlaka **başvuru numarası** koy (`[BEWERBUNGSNUMMER]`).\n- Kısa ve net tut; \"eksik belge varsa hemen tamamlarım\" cümlesi iyi izlenim bırakır.\n- **Sehr geehrte Damen und Herren** / **Mit freundlichen Grüßen** standart resmi kalıptır.\n- Tüm `[KÖŞELİ]` alanları doldur.",
                'guide_en' => "In Germany, **tone and correct format** matter when contacting institutions.\n\n- Don't write **before the stated processing time** has passed.\n- Always put your **application number** in the subject line.\n- Keep it short; offering to send missing documents immediately makes a good impression.\n- **Sehr geehrte Damen und Herren** / **Mit freundlichen Grüßen** is the standard formal frame.\n- Fill every `[BRACKET]`.",
                'guide_de' => "Ton und Format sind beim Kontakt mit Hochschulen wichtig.\n\n- Nicht **vor Ablauf der Bearbeitungszeit** schreiben.\n- **Bewerbungsnummer** in die Betreffzeile.\n- Kurz halten; Bereitschaft zur Nachreichung fehlender Unterlagen wirkt gut.\n- Alle `[KLAMMER]`-Felder ausfüllen.",
                'placeholders' => [
                    ['key' => 'STUDIENGANG', 'label_tr' => 'Başvurduğun bölüm', 'label_en' => 'Programme', 'label_de' => 'Studiengang'],
                    ['key' => 'BEWERBUNGSNUMMER', 'label_tr' => 'Başvuru numaran', 'label_en' => 'Application number', 'label_de' => 'Bewerbungsnummer'],
                    ['key' => 'BEWERBUNGSDATUM', 'label_tr' => 'Başvuru tarihin', 'label_en' => 'Application date', 'label_de' => 'Bewerbungsdatum'],
                    ['key' => 'SEMESTER', 'label_tr' => 'Hangi dönem (örn. WS 2026/27)', 'label_en' => 'Which semester', 'label_de' => 'Semester'],
                ],
                'is_premium' => false, 'sort_order' => 40, 'is_active' => true,
            ],
        ];
    }
};

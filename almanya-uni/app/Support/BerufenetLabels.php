<?php

namespace App\Support;

/**
 * BERUFENET (Bundesagentur für Arbeit) "Infofeld" başlıklarının TR/EN sözlüğü.
 *
 * info_fields key'leri BERUFENET'in Almanca `ueberschrift` değerleridir (açık bir küme).
 * Burada bilinen ~96 base başlık TR + EN'ye çevrilir. Eyalet-bazlı varyantlar
 * ("Ausbildungsvergütung (Bayern)" gibi) base + Bundesland haritasıyla bileşik çözülür.
 *
 * Haritada olmayan key → get() null döner → view'de gizlenir (Almanca sızması yasak,
 * [[feedback-source-language-localization]]). Yeni başlık çıkarsa `professions:label-coverage`
 * ile yakalanır.
 */
class BerufenetLabels
{
    /** 16 Bundesland — Almanca ad => ['tr'=>…, 'en'=>…]. */
    public const BUNDESLAENDER = [
        'Baden-Württemberg'      => ['tr' => 'Baden-Württemberg',      'en' => 'Baden-Württemberg'],
        'Bayern'                 => ['tr' => 'Bavyera',                'en' => 'Bavaria'],
        'Berlin'                 => ['tr' => 'Berlin',                 'en' => 'Berlin'],
        'Brandenburg'            => ['tr' => 'Brandenburg',            'en' => 'Brandenburg'],
        'Bremen'                 => ['tr' => 'Bremen',                 'en' => 'Bremen'],
        'Hamburg'                => ['tr' => 'Hamburg',                'en' => 'Hamburg'],
        'Hessen'                 => ['tr' => 'Hessen',                 'en' => 'Hesse'],
        'Mecklenburg-Vorpommern' => ['tr' => 'Mecklenburg-Vorpommern', 'en' => 'Mecklenburg-Vorpommern'],
        'Niedersachsen'          => ['tr' => 'Aşağı Saksonya',         'en' => 'Lower Saxony'],
        'Nordrhein-Westfalen'    => ['tr' => 'Kuzey Ren-Vestfalya',    'en' => 'North Rhine-Westphalia'],
        'Rheinland-Pfalz'        => ['tr' => 'Rheinland-Pfalz',        'en' => 'Rhineland-Palatinate'],
        'Saarland'               => ['tr' => 'Saarland',               'en' => 'Saarland'],
        'Sachsen'                => ['tr' => 'Saksonya',               'en' => 'Saxony'],
        'Sachsen-Anhalt'         => ['tr' => 'Saksonya-Anhalt',        'en' => 'Saxony-Anhalt'],
        'Schleswig-Holstein'     => ['tr' => 'Schleswig-Holstein',     'en' => 'Schleswig-Holstein'],
        'Thüringen'              => ['tr' => 'Türingen',               'en' => 'Thuringia'],
    ];

    /** Base başlık => ['tr'=>…, 'en'=>…]. (96 başlık) */
    public const LABELS = [
        'Abschluss-/Berufsbezeichnungen'                  => ['tr' => 'Diploma / Meslek Unvanları',         'en' => 'Qualification / Job Titles'],
        'Aktuelles'                                       => ['tr' => 'Güncel',                              'en' => 'Latest'],
        'Alternativen nach Studienabbruch'                => ['tr' => 'Öğrenimi Bırakınca Alternatifler',   'en' => 'Alternatives After Dropping Out'],
        'Anerkennung von ausländischen Qualifikationen'   => ['tr' => 'Yabancı Diplomaların Tanınması',     'en' => 'Recognition of Foreign Qualifications'],
        'Anrechnungs- und Fortsetzungsmöglichkeiten'      => ['tr' => 'Sayım ve Devam Olanakları',          'en' => 'Credit Transfer & Continuation Options'],
        'Arbeits- und Sozialverhalten'                    => ['tr' => 'Çalışma ve Sosyal Davranış',         'en' => 'Work & Social Conduct'],
        'Arbeitsbedingungen im Einzelnen'                 => ['tr' => 'Çalışma Koşulları (Detay)',          'en' => 'Working Conditions (Detail)'],
        'Arbeitsbereiche/Branchen'                        => ['tr' => 'Çalışma Alanları / Sektörler',       'en' => 'Work Areas / Sectors'],
        'Arbeitsgegenstände/Arbeitsmittel'                => ['tr' => 'Çalışma Nesneleri / Araç-Gereç',     'en' => 'Work Objects / Tools'],
        'Arbeitsorte'                                     => ['tr' => 'Çalışma Yerleri',                    'en' => 'Workplaces'],
        'Arbeitssituation'                                => ['tr' => 'Çalışma Durumu',                     'en' => 'Work Situation'],
        'Aufgaben und Tätigkeiten (Beschreibung)'         => ['tr' => 'Görevler ve İşler (Açıklama)',       'en' => 'Tasks & Activities (Description)'],
        'Aufgaben und Tätigkeiten im Einzelnen'           => ['tr' => 'Görevler ve İşler (Detay)',          'en' => 'Tasks & Activities (Detail)'],
        'Aufgaben und Tätigkeiten kompakt'                => ['tr' => 'Görevler ve İşler (Özet)',           'en' => 'Tasks & Activities (Summary)'],
        'Ausbildung im Ausland'                           => ['tr' => 'Yurt Dışında Ausbildung',            'en' => 'Vocational Training Abroad'],
        'Ausbildungsalternativen'                         => ['tr' => 'Ausbildung Alternatifleri',          'en' => 'Training Alternatives'],
        'Ausbildungsaufbau'                               => ['tr' => 'Ausbildung Yapısı',                  'en' => 'Training Structure'],
        'Ausbildungsdauer'                                => ['tr' => 'Ausbildung Süresi',                  'en' => 'Training Duration'],
        'Ausbildungsinhalte'                              => ['tr' => 'Ausbildung İçerikleri',              'en' => 'Training Content'],
        'Ausbildungskosten'                               => ['tr' => 'Ausbildung Masrafları',              'en' => 'Training Costs'],
        'Ausbildungssituation'                            => ['tr' => 'Ausbildung Durumu',                  'en' => 'Training Situation'],
        'Ausbildungsvergütung'                            => ['tr' => 'Ausbildung Ücreti',                  'en' => 'Training Pay'],
        'Auswahlverfahren'                                => ['tr' => 'Seçim Süreci',                       'en' => 'Selection Procedure'],
        'BERUF AKTUELL'                                   => ['tr' => 'Meslek Güncel',                      'en' => 'Occupation Update'],
        'Berufliche Einsatzmöglichkeiten'                 => ['tr' => 'Mesleki İstihdam Olanakları',        'en' => 'Career Opportunities'],
        'Berufsrelevante gesundheitliche Einschränkungen' => ['tr' => 'Mesleğe Engel Sağlık Kısıtları',     'en' => 'Health Restrictions Relevant to the Job'],
        'Branchen im Einzelnen'                           => ['tr' => 'Sektörler (Detay)',                  'en' => 'Sectors (Detail)'],
        'Charakteristische körperliche Anforderungen'     => ['tr' => 'Tipik Fiziksel Gereksinimler',       'en' => 'Typical Physical Requirements'],
        'Das Studium im Überblick'                        => ['tr' => 'Öğrenime Genel Bakış',               'en' => 'The Degree Programme at a Glance'],
        'Die Ausbildung im Überblick'                     => ['tr' => 'Ausbildunga Genel Bakış',            'en' => 'The Training at a Glance'],
        'Die Tätigkeit im Überblick'                      => ['tr' => 'Mesleğe Genel Bakış',               'en' => 'The Occupation at a Glance'],
        'Die Weiterbildung im Überblick'                  => ['tr' => 'İleri Eğitime Genel Bakış',          'en' => 'The Further Training at a Glance'],
        'Digitalisierung'                                 => ['tr' => 'Dijitalleşme',                       'en' => 'Digitalisation'],
        'Duales Studium'                                  => ['tr' => 'İkili (Dual) Öğrenim',               'en' => 'Dual Study Programme'],
        'Entwicklung der Ausbildung'                      => ['tr' => 'Ausbildungun Gelişimi',              'en' => 'Development of the Training'],
        'Entwicklung der Weiterbildung'                   => ['tr' => 'İleri Eğitimin Gelişimi',            'en' => 'Development of the Further Training'],
        'Entwicklung des Studienfachs'                    => ['tr' => 'Bölümün Gelişimi',                   'en' => 'Development of the Field of Study'],
        'Existenzgründung'                                => ['tr' => 'Girişimcilik / Kendi İşini Kurma',   'en' => 'Self-Employment'],
        'Fähigkeiten, Kenntnisse und Fertigkeiten'        => ['tr' => 'Yetenek, Bilgi ve Beceriler',        'en' => 'Abilities, Knowledge & Skills'],
        'Interessen'                                      => ['tr' => 'İlgi Alanları',                      'en' => 'Interests'],
        'Job- und Besetzungsalternativen'                 => ['tr' => 'İş ve İstihdam Alternatifleri',      'en' => 'Job & Placement Alternatives'],
        'Kombinationsmöglichkeiten'                       => ['tr' => 'Birleştirme Olanakları',             'en' => 'Combination Options'],
        'Kompetenzen'                                     => ['tr' => 'Yetkinlikler',                       'en' => 'Competencies'],
        'Lernorte'                                        => ['tr' => 'Öğrenim Yerleri',                    'en' => 'Places of Learning'],
        'Medien'                                          => ['tr' => 'Medya / Kaynaklar',                  'en' => 'Media / Resources'],
        'Mögliche Tätigkeitsfelder'                       => ['tr' => 'Olası Çalışma Alanları',             'en' => 'Possible Fields of Activity'],
        'Mögliche weiterführende Studienfächer'           => ['tr' => 'Olası İleri Bölümler',               'en' => 'Possible Further Study Subjects'],
        'Perspektiven nach der Ausbildung'                => ['tr' => 'Ausbildung Sonrası Perspektifler',   'en' => 'Prospects After Training'],
        'Perspektiven nach der Weiterbildung'             => ['tr' => 'İleri Eğitim Sonrası Perspektifler', 'en' => 'Prospects After Further Training'],
        'Rechtliche Regelungen für das Studium'           => ['tr' => 'Öğrenim İçin Yasal Düzenlemeler',    'en' => 'Legal Regulations for the Degree'],
        'Rechtliche Regelungen für die Ausbildung'        => ['tr' => 'Ausbildung İçin Yasal Düzenlemeler', 'en' => 'Legal Regulations for the Training'],
        'Rechtliche Regelungen für die Tätigkeit'         => ['tr' => 'Meslek İçin Yasal Düzenlemeler',     'en' => 'Legal Regulations for the Occupation'],
        'Rechtliche Regelungen für die Weiterbildung'     => ['tr' => 'İleri Eğitim İçin Yasal Düzenlemeler', 'en' => 'Legal Regulations for the Further Training'],
        'Schulische Vorbildung in der Praxis'             => ['tr' => 'Pratikte Okul Ön Eğitimi',           'en' => 'Prior Schooling in Practice'],
        'Sonstige Zugangsbedingungen'                     => ['tr' => 'Diğer Giriş Koşulları',              'en' => 'Other Access Conditions'],
        'Spezialisierung während der Ausbildung'          => ['tr' => 'Ausbildung Sırasında Uzmanlaşma',    'en' => 'Specialisation During Training'],
        'Spezialisierung während der Weiterbildung'       => ['tr' => 'İleri Eğitim Sırasında Uzmanlaşma',  'en' => 'Specialisation During Further Training'],
        'Spezialisierung während des Studiums'            => ['tr' => 'Öğrenim Sırasında Uzmanlaşma',       'en' => 'Specialisation During the Degree'],
        'Steckbrief'                                      => ['tr' => 'Künye',                              'en' => 'Profile'],
        'Stellen- und Bewerberbörsen'                     => ['tr' => 'İş ve Başvuru Borsaları',            'en' => 'Job & Applicant Boards'],
        'Studienalternativen'                             => ['tr' => 'Öğrenim Alternatifleri',             'en' => 'Study Alternatives'],
        'Studiendauer'                                    => ['tr' => 'Öğrenim Süresi',                     'en' => 'Programme Duration'],
        'Studiengangsbezeichnungen'                       => ['tr' => 'Bölüm Adları',                       'en' => 'Degree Programme Titles'],
        'Studieninhalte'                                  => ['tr' => 'Ders İçerikleri',                    'en' => 'Study Content'],
        'Studienkosten'                                   => ['tr' => 'Öğrenim Masrafları',                 'en' => 'Study Costs'],
        'Studiensituation'                                => ['tr' => 'Öğrenim Durumu',                     'en' => 'Study Situation'],
        'Studium im Ausland'                              => ['tr' => 'Yurt Dışında Öğrenim',               'en' => 'Studying Abroad'],
        'Trends'                                          => ['tr' => 'Trendler',                           'en' => 'Trends'],
        'Tätigkeitsbezeichnungen'                         => ['tr' => 'Meslek Unvanları',                   'en' => 'Activity Titles'],
        'Unmittelbare Job- und Besetzungsalternativen'    => ['tr' => 'Doğrudan İş ve İstihdam Alternatifleri', 'en' => 'Immediate Job & Placement Alternatives'],
        'Verbände und Organisationen'                     => ['tr' => 'Dernekler ve Kuruluşlar',            'en' => 'Associations & Organisations'],
        'Verdienst/Einkommen'                             => ['tr' => 'Maaş / Gelir',                       'en' => 'Earnings / Income'],
        'Vergütung'                                       => ['tr' => 'Ücret',                              'en' => 'Pay'],
        'Vergütung während des Studiums'                  => ['tr' => 'Öğrenim Sırasında Ücret',            'en' => 'Pay During the Degree'],
        'Verkürzungen/Verlängerungen'                     => ['tr' => 'Kısaltma / Uzatmalar',               'en' => 'Shortenings / Extensions'],
        'Weiterbildung (berufliche Anpassung)'            => ['tr' => 'İleri Eğitim (Mesleki Uyum)',        'en' => 'Further Training (Professional Adaptation)'],
        'Weiterbildung (beruflicher Aufstieg)'            => ['tr' => 'İleri Eğitim (Kariyer İlerleme)',    'en' => 'Further Training (Career Advancement)'],
        'Weiterbildung im Ausland'                        => ['tr' => 'Yurt Dışında İleri Eğitim',          'en' => 'Further Training Abroad'],
        'Weiterbildungsalternativen'                      => ['tr' => 'İleri Eğitim Alternatifleri',        'en' => 'Further Training Alternatives'],
        'Weiterbildungsaufbau'                            => ['tr' => 'İleri Eğitim Yapısı',                'en' => 'Further Training Structure'],
        'Weiterbildungsdauer'                             => ['tr' => 'İleri Eğitim Süresi',                'en' => 'Further Training Duration'],
        'Weiterbildungsinhalte'                           => ['tr' => 'İleri Eğitim İçerikleri',            'en' => 'Further Training Content'],
        'Weiterbildungskosten'                            => ['tr' => 'İleri Eğitim Masrafları',            'en' => 'Further Training Costs'],
        'Weiterbildungssituation'                         => ['tr' => 'İleri Eğitim Durumu',               'en' => 'Further Training Situation'],
        'Weiterbildungsvergütung'                         => ['tr' => 'İleri Eğitim Ücreti',                'en' => 'Further Training Pay'],
        'Weitere Besetzungsalternativen (Arbeitgebersicht)' => ['tr' => 'Diğer İstihdam Alternatifleri (İşveren)', 'en' => 'Other Placement Alternatives (Employer View)'],
        'Weitere Jobalternativen (Bewerbersicht)'         => ['tr' => 'Diğer İş Alternatifleri (Aday)',     'en' => 'Other Job Alternatives (Applicant View)'],
        'Wichtige Schulfächer'                            => ['tr' => 'Önemli Okul Dersleri',               'en' => 'Important School Subjects'],
        'Wichtige Vorkenntnisse'                          => ['tr' => 'Önemli Ön Bilgiler',                 'en' => 'Important Prior Knowledge'],
        'Zugang zur Tätigkeit'                            => ['tr' => 'Mesleğe Giriş',                      'en' => 'Access to the Occupation'],
        'Zugangsberufe/Zugangstätigkeiten'                => ['tr' => 'Giriş Meslekleri / Faaliyetleri',    'en' => 'Entry Occupations / Activities'],
        'Zugangsstudienfächer'                            => ['tr' => 'Giriş Bölümleri',                    'en' => 'Entry Study Subjects'],
        'Zugangsvoraussetzungen für das Studium'          => ['tr' => 'Öğrenime Giriş Koşulları',           'en' => 'Admission Requirements for the Degree'],
        'Zugangsvoraussetzungen für die Ausbildung'       => ['tr' => 'Ausbildunga Giriş Koşulları',        'en' => 'Entry Requirements for the Training'],
        'Zugangsvoraussetzungen für die Weiterbildung'    => ['tr' => 'İleri Eğitime Giriş Koşulları',      'en' => 'Entry Requirements for the Further Training'],
        'Zusatzqualifikationen'                           => ['tr' => 'Ek Nitelikler',                      'en' => 'Additional Qualifications'],
    ];

    /**
     * Almanca BERUFENET başlığının verilen dildeki etiketini döndürür.
     * Eyalet-bazlı varyant ("X (Bayern)") base + Bundesland ile bileşik çözülür.
     * Bilinmeyen başlık → null (view'de gizlenir).
     */
    public static function get(string $germanKey, string $locale): ?string
    {
        if ($locale === 'de') {
            return $germanKey;
        }

        if (isset(self::LABELS[$germanKey][$locale])) {
            return self::LABELS[$germanKey][$locale];
        }

        // "Base (Bundesland)" varyantı
        foreach (self::BUNDESLAENDER as $land => $landTrans) {
            $suffix = ' (' . $land . ')';
            if (str_ends_with($germanKey, $suffix)) {
                $base = mb_substr($germanKey, 0, -mb_strlen($suffix));
                $baseLabel = self::LABELS[$base][$locale] ?? null;
                if ($baseLabel === null) {
                    return null;
                }
                return $baseLabel . ' (' . ($landTrans[$locale] ?? $land) . ')';
            }
        }

        return null;
    }

    /**
     * Bir info_fields key listesinden TR/EN haritasında karşılığı OLMAYANLARı döndürür.
     * `professions:label-coverage` teşhisi + i18n disiplini için.
     */
    public static function unmapped(array $germanKeys): array
    {
        $missing = [];
        foreach ($germanKeys as $k) {
            if (self::get($k, 'tr') === null) {
                $missing[] = $k;
            }
        }
        return array_values(array_unique($missing));
    }
}

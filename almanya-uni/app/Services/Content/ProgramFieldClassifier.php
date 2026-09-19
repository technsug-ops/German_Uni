<?php

namespace App\Services\Content;

/**
 * Program adından ALAN (fields_of_study) tahmini.
 *
 * NEDEN VAR: iki içe aktarma kaynağı da alan bilgisi vermiyor —
 *   · Hochschulkompass kataloğunda konu grubu alanı hiç yok, yalnızca program adı var.
 *   · DAAD API'si konu metni veriyor ama bizim 10 alanımıza eşlenmemiş; importer
 *     field_of_study_id'yi boş bırakıyordu → 2.532 program alan sıralamalarında ve alan
 *     sayfalarında GÖRÜNMÜYORDU.
 *
 * KURALLAR TAHMİNLE DEĞİL, SİTENİN KENDİ DAĞILIMI ÖLÇÜLEREK yazıldı; ölçüm açıkça hatalı
 * olduğunda semantik tercih edildi ve not düşüldü:
 *   Wirtschaftsingenieur → hukuk-ekonomi (61 mevcut kayıt) · Medizintechnik → tıp-sağlık
 *   Data Science → bilişim (64) · Psikoloji → sosyal-bilimler (244) · Mechatronik → mühendislik
 *   Agrar/Ernährung/Forst mevcut veride hukuk-ekonomi'ye düşmüş (…management adları yüzünden)
 *   — doğru yeri tarım-ormancılık.
 *
 * SIRA ÖNEMLİ: ilk eşleşen kazanır. Spesifik alanlar genel alanlardan önce gelir
 * ("Lehramt Mathematik" matematik-doğa'ya gider, sosyal-bilimler'e değil).
 */
class ProgramFieldClassifier
{
    /** @var array<string, array{include: string[], exclude: string[]}> */
    public const RULES = [
        'tip-saglik' => [
            'include' => [
                'medizin', 'medicine', 'zahnmedizin', 'dentistry', 'pharmaz', 'pharmacy',
                'pflege', 'nursing', 'hebamme', 'midwifery', 'physiotherap', 'ergotherap',
                'logopäd', 'gesundheit', 'public health', 'rettungs', 'therapie', 'therapy',
                'humanmedizin', 'arzneimittel', 'klinische', 'clinical', 'epidemiolog',
            ],
            'exclude' => [
                'informatik', 'informatics', 'management', 'ökonomie', 'economics', 'wirtschaft',
                'tiermedizin', 'veterinär', 'veterinary', 'ingenieur', 'engineering', 'psycholog',
            ],
        ],
        'veteriner-spor' => [
            'include' => ['veterinär', 'veterinary', 'tiermedizin', 'tierärztlich', 'tierwissenschaft', 'sport', 'bewegung', 'exercise science'],
            'exclude' => ['sportmanagement', 'sportökonomie', 'wirtschaft', 'informatik', 'ingenieur', 'transport'],
        ],
        'tarim-ormancilik' => [
            'include' => [
                'agrar', 'agricultur', 'landwirtschaft', 'forst', 'forestry', 'ernährung',
                'nutrition', 'gartenbau', 'horticultur', 'weinbau', 'lebensmittel', 'food science',
                'holztechnik', 'fischerei', 'aquacultur',
            ],
            'exclude' => ['management', 'wirtschaft', 'business', 'informatik', 'ökonomie'],
        ],
        'dil-kultur' => [
            'include' => [
                'sprach', 'linguistic', 'germanistik', 'anglistik', 'romanistik', 'slavistik',
                'literatur', 'literature', 'philologie', 'translation', 'übersetz', 'dolmetsch',
                'interpreting', 'orientalistik', 'sinologie', 'japanologie', 'kulturwissenschaft',
            ],
            'exclude' => ['management', 'wirtschaft', 'business', 'informatik', 'lehramt'],
        ],
        'matematik-doga' => [
            'include' => [
                'mathematik', 'mathematics', 'physik', 'physics', 'chemie', 'chemistry',
                'biolog', 'biochemie', 'geowissenschaft', 'geoscience', 'geologie', 'astronom',
                'statistik', 'statistics', 'meteorolog', 'mineralog', 'nanoscience',
                'naturwissenschaft', 'molekular', 'molecular',
            ],
            'exclude' => [
                'ingenieur', 'engineering', 'verfahrenstechnik', 'informatik', 'informatics',
                'medizin', 'technik', 'wirtschaftsmathematik', 'management',
            ],
        ],
        'sanat-tasarim' => [
            'include' => [
                'kunst', 'art ', 'fine arts', 'design', 'musik', 'music', 'architekt',
                'architecture', 'gestaltung', 'theater', 'tanz', 'dance', 'film', 'fotografie',
                'photograph', 'modedesign', 'schauspiel', 'bildende', 'komposition',
            ],
            'exclude' => ['informatik', 'management', 'ingenieur', 'kunststoff', 'wirtschaft'],
        ],
        'bilisim' => [
            'include' => [
                'informatik', 'informatics', 'computer', 'computing', 'software', 'data science',
                'künstliche intelligenz', 'artificial intelligence', 'machine learning',
                'cyber', 'it-sicherheit', 'it sicherheit', 'informationssystem', 'information systems',
                'computational', 'digital media', 'games',
            ],
            'exclude' => ['informationstechnik', 'biomedizinische'],
        ],
        'muhendislik' => [
            'include' => [
                'ingenieur', 'engineering', 'maschinenbau', 'mechanical', 'elektrotechnik',
                'electrical', 'bauingenieur', 'civil engineering', 'verfahrenstechnik',
                'mechatronik', 'mechatronics', 'fahrzeugtechnik', 'automotive', 'luftfahrt',
                'raumfahrt', 'aerospace', 'energietechnik', 'werkstoff', 'materials science',
                'produktionstechnik', 'automatisierung', 'robotik', 'robotics',
                'nachrichtentechnik', 'umwelttechnik', 'schiffbau', 'kybernetik', 'metalltechnik',
            ],
            'exclude' => [
                'wirtschaftsingenieur', 'informatik', 'medizintechnik', 'medizinische technik',
                'biomedizin', 'financial engineering', 'business engineering',
            ],
        ],
        'hukuk-ekonomi' => [
            'include' => [
                'wirtschaft', 'ökonom', 'economics', 'betriebswirt', 'volkswirt', 'management',
                'finance', 'finanz', 'rechnungswesen', 'controlling', 'marketing', 'steuer',
                'recht', 'jura', 'law', 'logistik', 'logistics', 'supply chain', 'immobilien',
                'versicherung', 'banking', 'accounting', 'business', 'wirtschaftsingenieur',
            ],
            'exclude' => ['wirtschaftsinformatik', 'informatik', 'psycholog'],
        ],
        // EN GENEL alan → en sonda: Lehramt ve "…wissenschaft" kalıntıları buraya düşer.
        'sosyal-bilimler' => [
            'include' => [
                'sozial', 'social', 'psycholog', 'soziologie', 'sociology', 'politik', 'political',
                'pädagog', 'education', 'erziehung', 'lehramt', 'geschichte', 'history',
                'philosophie', 'philosophy', 'theolog', 'ethnolog', 'anthropolog',
                'kommunikationswissenschaft', 'medienwissenschaft', 'europastudien',
                'bildungswissenschaft', 'international relations', 'development studies',
            ],
            'exclude' => ['informatik', 'management', 'wirtschaft', 'ingenieur', 'design'],
        ],
    ];

    /**
     * Verilen metin(ler)den alan slug'ı tahmin eder; emin olunamazsa null döner
     * (yanlış alana atamak, atamamaktan kötüdür — sıralamalar buna bakıyor).
     *
     * @param  string[]  $texts  program adı, İngilizce adı, ham konu metni…
     */
    public function classify(array $texts): ?string
    {
        $haystack = mb_strtolower(implode(' · ', array_filter($texts)), 'UTF-8');
        if ($haystack === '') {
            return null;
        }

        foreach (self::RULES as $slug => $rule) {
            $hit = false;
            foreach ($rule['include'] as $k) {
                if (str_contains($haystack, $k)) {
                    $hit = true;
                    break;
                }
            }
            if (! $hit) {
                continue;
            }
            foreach ($rule['exclude'] as $k) {
                if (str_contains($haystack, $k)) {
                    $hit = false;
                    break;
                }
            }
            if ($hit) {
                return $slug;
            }
        }

        return null;
    }
}

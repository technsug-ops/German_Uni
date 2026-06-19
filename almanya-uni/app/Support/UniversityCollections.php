<?php

namespace App\Support;

/**
 * Curated, editorial university collections → category landing pages.
 *
 * Each entry: slug => [icon, accent, title, subtitle, intro, uni_slugs[]].
 * `title`/`subtitle`/`intro` are English strings used directly as __() keys
 * (TR/DE live in lang/tr.json + lang/de.json, per the project i18n convention).
 *
 * `uni_slugs` are canonical University.slug values, resolved & verified against
 * the DB (picked the main institution by student_count, not sub-institutes).
 * Known thin-data canonicals kept on purpose because the correctly-named record
 * is the right link target: Humboldt + TU Berlin (duplicate sub-institute rows
 * hold the bulk data — flagged for a later merge), GISMA (0 active programs).
 */
class UniversityCollections
{
    public static function all(): array
    {
        return [
            'top-public-universities' => [
                'icon'     => '🏛️',
                'accent'   => 'primary',
                'title'    => 'Most Preferred Public Universities in Germany',
                'subtitle' => 'Germany\'s most sought-after state universities — tuition-free, world-ranked, and the top choice of international students.',
                'intro'    => 'These public (staatlich) universities consistently rank among Germany\'s most applied-to institutions. All are tuition-free (only a small semester contribution) and recognised worldwide. Tap any university to see its programmes, deadlines and city.',
                'uni_slugs' => [
                    'ludwig-maximilians-universitat-munchen-q55044',
                    'technische-universitat-munchen-partner-019ddbba',
                    'universitat-heidelberg-partner-019ddbba',
                    'humboldt-universitat-zu-berlin',
                    'albert-ludwigs-universitat-freiburg-im-breisgau-partner-019ddbba',
                    'universitat-zu-koln-q54096',
                    'eberhard-karls-universitat-tubingen-q153978',
                    'rwth-aachen-university-partner-019de9ee',
                    'johannes-gutenberg-universitat-mainz-q161982',
                    'rheinische-friedrich-wilhelms-universitat-bonn-q152171',
                ],
            ],

            'english-taught-universities' => [
                'icon'     => '🌍',
                'accent'   => 'accent',
                'title'    => 'Universities Teaching in English in Germany',
                'subtitle' => 'Leading German universities offering English-taught programmes — you can start your studies without German.',
                'intro'    => 'These universities run degree programmes taught fully or partly in English, so you can apply and begin without a German certificate. You will still need German for daily life and many Bachelor\'s programmes, but these are the strongest options for English-medium study. Tap any university for its English-taught programmes.',
                'uni_slugs' => [
                    'technische-universitat-munchen-partner-019ddbba',
                    'ludwig-maximilians-universitat-munchen-q55044',
                    'universitat-heidelberg-partner-019ddbba',
                    'technische-universitat-berlin',
                    'rwth-aachen-university-partner-019de9ee',
                    'albert-ludwigs-universitat-freiburg-im-breisgau-partner-019ddbba',
                    'eberhard-karls-universitat-tubingen-q153978',
                    'karlsruher-institut-fur-technologie-q309988',
                    'gisma-university-of-applied-sciences-hs518',
                    'constructor-university-bremen-partner-019ddbba',
                ],
            ],

            'conditional-admission-universities' => [
                'icon'     => '📝',
                'accent'   => 'amber',
                'title'    => 'Universities Offering Conditional Admission in Germany',
                'subtitle' => 'Universities verified from official sources as issuing conditional admission (bedingte Zulassung) to international applicants who still need to complete a requirement.',
                'intro'    => 'Conditional admission (bedingte Zulassung) lets a university admit you before you have fully met a requirement — typically a pending German certificate — and enrol you in a preparatory language course meanwhile. The universities below are verified from official sources as offering this. Some others (Universität Hamburg, TU Braunschweig, Universität Paderborn, RWTH Aachen) offer it only for certain programs, while some frequently-cited universities (e.g. Justus-Liebig Gießen) officially state they do NOT offer conditional enrolment. Always confirm the exact conditions with the university before applying.',
                // Resmî kaynaktan DOĞRULANMIŞ (Haziran 2026). Tümü fetch edilir; 2 kolonda gösterilir.
                'uni_slugs' => [
                    'universitat-bremen-q500692',
                    'universitat-duisburg-essen-q696757',
                    'philipps-universitat-marburg-q155354',
                    'technische-universitat-clausthal-q447354',
                    'technische-universitat-dortmund-q685557',
                    'universitat-hamburg-q156725',
                    'technische-universitat-braunschweig-q734324',
                    'universitat-paderborn-q679134',
                    'rwth-aachen-university-partner-019de9ee',
                ],
                // 2 kolon: kesin veren + programa bağlı. items: slug => [status, note] (view'da __()).
                'groups' => [
                    [
                        'title'  => 'Definitely offers conditional admission',
                        'icon'   => '✅',
                        'accent' => 'emerald',
                        'items'  => [
                            'universitat-bremen-q500692'               => ['status' => 'Confirmed', 'note' => 'Well known for conditional admission — issues it to almost every qualified applicant.'],
                            'universitat-duisburg-essen-q696757'       => ['status' => 'Confirmed', 'note' => 'Official page allows applying without sufficient German and obtaining a Sprachkurs-Zulassung.'],
                            'philipps-universitat-marburg-q155354'     => ['status' => 'Confirmed', 'note' => 'Explicitly defines "conditional admission" and offers enrolment as a language student for two semesters.'],
                            'technische-universitat-clausthal-q447354' => ['status' => 'Confirmed', 'note' => 'Official "Conditional Admission" program — you enrol in the language course and receive conditional admission at the same time.'],
                            'technische-universitat-dortmund-q685557'  => ['status' => 'Confirmed', 'note' => 'Issues a Sprachkurs-Zulassung if your C1 is missing — you first enrol as a language-course student.'],
                        ],
                    ],
                    [
                        'title'  => 'Program-dependent / limited',
                        'icon'   => '⚠️',
                        'accent' => 'amber',
                        'items'  => [
                            'universitat-hamburg-q156725'                  => ['status' => 'Depends on program', 'note' => 'Varies by department.'],
                            'technische-universitat-braunschweig-q734324'  => ['status' => 'Limited', 'note' => 'Possible in some programs.'],
                            'universitat-paderborn-q679134'                => ['status' => 'Depends on program', 'note' => 'No general guarantee.'],
                            'rwth-aachen-university-partner-019de9ee'      => ['status' => 'Limited', 'note' => 'Possible in certain cases, especially tied to a language course.'],
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function find(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }
}

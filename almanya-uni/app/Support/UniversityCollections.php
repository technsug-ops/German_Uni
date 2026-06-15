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
                'subtitle' => 'Universities known for issuing conditional admission (bedingte Zulassung) to international applicants who still need to complete a requirement.',
                'intro'    => 'Conditional admission (bedingte Zulassung) lets you receive an offer before you have fully met every requirement — for example a pending language certificate. These universities, many of them technical universities popular with international students, are frequently cited for this route. Always confirm the exact conditions with the university before you apply.',
                'uni_slugs' => [
                    'universitat-kassel-q833822',
                    'technische-universitat-darmstadt-q310695',
                    'universitat-stuttgart-q122453',
                    'technische-universitat-clausthal-q447354',
                    'ruhr-universitat-bochum-q309948',
                    'bergische-universitat-wuppertal-q447953',
                    'technische-universitat-braunschweig-q734324',
                    'rheinland-pfalzische-technische-universitat-kaiserslautern-landau-q111020102',
                    'technische-universitat-dortmund-q685557',
                    'technische-universitat-chemnitz-q159630',
                ],
            ],
        ];
    }

    public static function find(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }
}

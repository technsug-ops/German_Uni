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
                'subtitle' => 'Ten English-taught programme examples at eight German universities, checked on official pages (October 2026), with the language certificate each one asks for.',
                'intro'    => 'Many German universities offer some programmes in English. That does not mean all of their programmes are taught in English. This page shows examples checked on the universities\' official pages: the degree, the language of instruction, the English proof for the application and any extra German requirement. The examples are not a complete list of each university\'s English-taught programmes.',
                // 2026-10-10: eski liste (LMU, Tübingen, KIT, GISMA, Constructor dahil) kaynaksızdı ("Almanca olmadan başlayabilirsin"
                // genellemesi). Artık yalnız resmî program sayfasında doğrulanmış örneği olan kurumlar; sıra = 'examples' sırası.
                'uni_slugs' => [
                    'technische-universitat-munchen-partner-019ddbba',
                    'technische-universitat-berlin',
                    'universitat-heidelberg-partner-019ddbba',
                    'albert-ludwigs-universitat-freiburg-im-breisgau-partner-019ddbba',
                    'rwth-aachen-university-partner-019de9ee',
                    'technische-universitat-darmstadt-q310695',
                    'technische-universitat-braunschweig-q734324',
                    'universitat-bremen-q500692',
                ],
                // Program örnekleri (view: universities/_program_examples). Program adları resmî adlarıdır (çevrilmez); diğer
                // metinler İngilizce __() anahtarı. 'teaching': english = tamamen/ağırlıkla İngilizce, bilingual = Almanca + İngilizce.
                // Her bilgi resmî program/dil sayfasından, kontrol 2026-10-10. Sınav puanı yalnız resmî sayfada yazıyorsa verilir.
                'examples' => [
                    'technische-universitat-munchen-partner-019ddbba' => [
                        [
                            'program'  => 'Aerospace',
                            'degree'   => 'B.Sc.',
                            'level'    => 'bachelor',
                            'teaching' => 'english',
                            'teaching_note' => 'Taught in English; some electives are offered in German.',
                            'english'  => 'Sufficient English, shown through school qualifications in the aptitude assessment or a certificate of at least B2.',
                            'german'   => 'A German certificate of at least A2, due by the end of the application deadline.',
                            'url'      => 'https://www.tum.de/en/studies/degree-programs/detail/aerospace-bachelor-of-science-bsc',
                            'checked'  => '2026-10-10',
                        ],
                        [
                            'program'  => 'Data Engineering and Analytics',
                            'degree'   => 'M.Sc.',
                            'level'    => 'master',
                            'teaching' => 'english',
                            'teaching_note' => 'Mostly taught in English; some courses may be taught in German.',
                            'english'  => 'Proof of English by the application deadline. TUM\'s certificate page lists, among others, IELTS Academic 6.5 overall and TOEFL iBT 88 (test date score). A previous degree taught at least 50% in English can also count.',
                            'german'   => 'No German requirement stated.',
                            'url'      => 'https://www.tum.de/en/studies/degree-programs/detail/data-engineering-and-analytics-master-of-science-msc',
                            'language_url' => 'https://www.tum.de/en/studies/application/application-info-portal/admission-requirements/language-certificates',
                            'checked'  => '2026-10-10',
                        ],
                    ],
                    'technische-universitat-berlin' => [
                        [
                            'program'  => 'Computer Science (Informatik)',
                            'degree'   => 'M.Sc.',
                            'level'    => 'master',
                            'teaching' => 'english',
                            'teaching_note' => 'Taught in English.',
                            'english'  => 'English at B2. The faculty lists, among others, IELTS Academic 6.5 and TOEFL iBT 87 (test date score). A Bachelor\'s taught in English counts only if it was completed in an English-speaking country.',
                            'german'   => 'No German requirement stated.',
                            'url'      => 'https://www.tu.berlin/en/studying/study-programs/all-programs-offered/study-course/computer-science-informatik-m-sc',
                            'language_url' => 'https://www.tu.berlin/en/go319348/',
                            'checked'  => '2026-10-10',
                        ],
                        [
                            'program'  => 'Civil Systems Engineering',
                            'degree'   => 'M.Sc.',
                            'level'    => 'master',
                            'teaching' => 'english',
                            'teaching_note' => 'Taught in English; some modules are offered in German.',
                            'english'  => 'English at C1 or an equivalent level.',
                            'german'   => 'Not required for admission; German helps because some modules are taught in German.',
                            'url'      => 'https://www.tu.berlin/en/studying/study-programs/all-programs-offered/study-course/civil-systems-engineering-m-sc',
                            'checked'  => '2026-10-10',
                        ],
                    ],
                    'universitat-heidelberg-partner-019ddbba' => [
                        [
                            'program'  => 'Scientific Computing',
                            'degree'   => 'M.Sc.',
                            'level'    => 'master',
                            'teaching' => 'english',
                            'teaching_note' => 'Taught in English, partly in German; it can be completed entirely in English.',
                            'english'  => 'English at B2. If you studied in a system with English as the main language of instruction, a short personal statement can replace the certificate.',
                            'german'   => 'No German requirement stated.',
                            'url'      => 'https://www.uni-heidelberg.de/en/study/all-subjects/scientific-computing/scientific-computing-master',
                            'language_url' => 'https://mastersc.iwr.uni-heidelberg.de/application-admission/faq-application',
                            'checked'  => '2026-10-10',
                        ],
                    ],
                    'albert-ludwigs-universitat-freiburg-im-breisgau-partner-019ddbba' => [
                        [
                            'program'  => 'Liberal Arts and Sciences',
                            'degree'   => 'B.A. / B.Sc.',
                            'level'    => 'bachelor',
                            'teaching' => 'english',
                            'teaching_note' => 'Taught in English (four years, 240 ECTS credits).',
                            'english'  => 'English at B2 or higher; the university publishes the list of accepted certificates.',
                            'german'   => 'The current programme pages state no German requirement for admission.',
                            'url'      => 'https://uni-freiburg.de/en/studies/degree-programmes/degree-programme/391/',
                            'language_url' => 'https://uni-freiburg.de/en/studies/applying/language-certificates/',
                            'checked'  => '2026-10-10',
                        ],
                    ],
                    'rwth-aachen-university-partner-019de9ee' => [
                        [
                            'program'  => 'Data Science',
                            'degree'   => 'M.Sc.',
                            'level'    => 'master',
                            'teaching' => 'english',
                            'teaching_note' => 'Taught in English.',
                            'english'  => 'An English certificate showing fluent spoken and written English; an IELTS result must be from the Academic test. Medium of instruction certificates are generally not accepted.',
                            'german'   => 'No German requirement stated.',
                            'url'      => 'https://sc.informatik.rwth-aachen.de/en/studium/master/master-data-science/application-for-admission/',
                            'checked'  => '2026-10-10',
                        ],
                    ],
                    'technische-universitat-darmstadt-q310695' => [
                        [
                            'program'  => 'Computer Science',
                            'degree'   => 'M.Sc.',
                            'level'    => 'master',
                            'teaching' => 'english',
                            'teaching_note' => 'English- and German-taught modules; it can be completed entirely in English.',
                            'english'  => 'English at C1, for example UNIcert III, IELTS 7.0 or TOEFL iBT 95.',
                            'german'   => 'No German requirement stated.',
                            'url'      => 'https://www.informatik.tu-darmstadt.de/studium_fb20/im_studium/studiengaenge_liste/computer_science_msc.en.jsp',
                            'checked'  => '2026-10-10',
                        ],
                    ],
                    'technische-universitat-braunschweig-q734324' => [
                        [
                            'program'  => 'Sustainable Engineering of Products and Processes',
                            'degree'   => 'B.Sc.',
                            'level'    => 'bachelor',
                            'teaching' => 'bilingual',
                            'teaching_note' => 'Bilingual programme (German and English).',
                            'english'  => 'For example IELTS 6.5, TOEFL iBT 85 or Cambridge B2 First 175; two years of school education in English also count.',
                            'german'   => 'German A2 at enrolment is enough to start; DSH-1 or TestDaF 4x3 must be reached by the end of the third semester.',
                            'url'      => 'https://www.tu-braunschweig.de/en/fmb/students/bachelors-degree-programmes/sustainable-engineering-of-products-and-processes/application',
                            'checked'  => '2026-10-10',
                        ],
                    ],
                    'universitat-bremen-q500692' => [
                        [
                            'program'  => 'Space Engineering',
                            'degree'   => 'M.Sc.',
                            'level'    => 'master',
                            'teaching' => 'english',
                            'teaching_note' => 'Taught in English.',
                            'english'  => 'English at C1. A school-leaving certificate or your most recent degree obtained in English also counts.',
                            'german'   => 'No German requirement stated.',
                            'url'      => 'https://www.uni-bremen.de/en/faculty-04-production-engineering-mechanical-engineering-and-process-engineering/studies-teaching/study-programs/msc-space-engineering',
                            'checked'  => '2026-10-10',
                        ],
                    ],
                ],
            ],

            'conditional-admission-universities' => [
                'icon'     => '📝',
                'accent'   => 'amber',
                'title'    => 'Universities Offering Conditional Admission in Germany',
                'subtitle' => 'Conditional admission, German-course admission or certificate at enrolment? 9 German universities compared using their official pages (checked October 2026).',
                'intro'    => 'Conditional admission, admission to a German course, preparatory student status and handing in the language certificate at enrolment are four different routes. Only the first one gives you a conditional place in a degree programme. Of the 9 universities on this page, two (Philipps-Universität Marburg and TU Clausthal) offer conditional admission to German-taught programmes, two (Duisburg-Essen and TU Dortmund) admit you to a German course and expect a new application for the degree, and five are often listed but follow a different route.',
                // 2026-10-10: resmî sayfalardan yeniden doğrulandı (her kurumun kaynak URL'si 'paths' içinde). Eski 'groups'
                // ("Definitely offers / Confirmed / Limited") resmî kaynaklarla çelişiyordu; Bremen, RWTH, Paderborn, Hamburg yanlış sınıftaydı.
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
                // Karar sayfası verisi (view: universities/_admission_paths). Her metin İngilizce __() anahtarı; TR/DE lang/*.json'da.
                // Kaynak: kurumların resmî sayfaları, kontrol 2026-10-10. 'listed' = ItemList'e (şartlı kabul listesi) girer.
                'paths' => [
                    'explainer' => [
                        ['term' => 'Conditional admission to a degree programme', 'text' => 'The university admits you to the programme on condition that you meet a missing requirement, usually the German certificate, within a set time.'],
                        ['term' => 'Admission to a German course', 'text' => 'You are admitted only to a language course or as a language-course student. Once you have the certificate, you apply again for the degree programme.'],
                        ['term' => 'Preparatory student status', 'text' => 'You are enrolled for a preparatory phase, but no place in a degree programme is reserved for you.'],
                        ['term' => 'Certificate due at enrolment', 'text' => 'You may apply before you have the certificate, but you must hand it in by the enrolment deadline. There is no language-course route.'],
                    ],
                    'sections' => [
                        [
                            'key'    => 'conditional',
                            'listed' => true,
                            'title'  => 'Conditional admission to a German-taught degree programme',
                            'lead'   => 'These two universities state on their official pages that they admit applicants without sufficient German to the degree programme on condition that they pass a recognised German exam.',
                            'items'  => [
                                'philipps-universitat-marburg-q155354' => [
                                    'path'        => 'Conditional admission (bedingte Zulassung)',
                                    'summary'     => 'Applicants without sufficient German who apply for an admission-free programme through uni-assist receive conditional admission and are enrolled as language students for two semesters. The university says this reserves the study place while you are enrolled as a language student.',
                                    'scope'       => 'Admission-free programmes applied for through uni-assist. Not for NC programmes. For Master\'s programmes the official page is not explicit.',
                                    'certificate' => 'The DSH level your programme requires (or an accepted equivalent), uploaded in Marvin by 30 September (winter semester) or 31 March (summer semester).',
                                    'place'       => 'Yes, while you are enrolled as a language student in the same programme.',
                                    'reapply'     => 'No for the same programme. Changing programme needs a new uni-assist application.',
                                    'portal'      => 'uni-assist',
                                    'source'      => 'https://www.uni-marburg.de/de/studium/im-studium/formalitaeten/sprachnachweise/deutschkenntnisse',
                                    'checked'     => '2026-10-10',
                                ],
                                'technische-universitat-clausthal-q447354' => [
                                    'path'        => 'Conditional admission (bedingte Zulassung)',
                                    'summary'     => 'Applicants from abroad for a German-taught programme receive conditional admission to the programme together with a registration confirmation for a fee-based intensive German course at a partner language school.',
                                    'scope'       => 'German-taught Bachelor\'s and Master\'s programmes. Not for applicants who apply from within Germany.',
                                    'certificate' => 'DSH-2, DSH-3 or TestDaF (TDN 4) for full admission. The page does not state a deadline.',
                                    'place'       => 'The official page does not say.',
                                    'reapply'     => 'The official page does not say.',
                                    'portal'      => 'HISinOne (TU Clausthal\'s own portal)',
                                    'source'      => 'https://www.izc.tu-clausthal.de/en/ways-to-clausthal/degree-seeking-students/conditional-admission',
                                    'checked'     => '2026-10-10',
                                ],
                            ],
                        ],
                        [
                            'key'    => 'language-course',
                            'listed' => true,
                            'title'  => 'Admission to a German course, then a new application',
                            'lead'   => 'Here you can apply without sufficient German, but you are admitted to a German course, not to the degree programme. You apply again once you have the certificate.',
                            'items'  => [
                                'universitat-duisburg-essen-q696757' => [
                                    'path'        => 'Admission to a German course',
                                    'summary'     => 'You can apply for an admission-free Bachelor\'s or Master\'s programme without sufficient German and receive a notice of admission to a German course. You study at a private language school and apply again for the degree once you have passed the DSH.',
                                    'scope'       => 'Admission-free Bachelor\'s and Master\'s programmes. Not for NC programmes.',
                                    'certificate' => 'DSH-2 level (DSH-3 for some programmes) before the degree programme starts.',
                                    'place'       => 'No.',
                                    'reapply'     => 'Yes, within the regular deadline.',
                                    'portal'      => 'Bachelor\'s: uni-assist. Master\'s: uni-assist or the UDE portal, depending on the programme.',
                                    'source'      => 'https://www.uni-due.de/international/international-admissions.php',
                                    'checked'     => '2026-10-10',
                                ],
                                'technische-universitat-dortmund-q685557' => [
                                    'path'        => 'Admission to a German course',
                                    'summary'     => 'Up to level B1 you receive a letter of access to a language course. With B2 and an admission-free programme you may hand in the C1 certificate at enrolment. Language-course students cannot attend lectures or take exams.',
                                    'scope'       => 'Applicants without C1. For NC programmes, C1 must be submitted by the application deadline.',
                                    'certificate' => 'Admission-free programmes: at enrolment. NC programmes: by the application deadline.',
                                    'place'       => 'No.',
                                    'reapply'     => 'Yes, within the regular deadline.',
                                    'portal'      => 'uni-assist',
                                    'source'      => 'https://international.tu-dortmund.de/studienbewerber/-innen/bewerbung/deutschkurs/',
                                    'checked'     => '2026-10-10',
                                ],
                            ],
                        ],
                        [
                            'key'    => 'confused',
                            'listed' => false,
                            'title'  => 'Often listed, but a different route',
                            'lead'   => 'These universities are often named in lists of conditional admission. Their official pages describe a different route, or none at all.',
                            'items'  => [
                                'universitat-bremen-q500692' => [
                                    'path'        => 'Preparatory student status (Vorbereitungsstudium)',
                                    'summary'     => 'Bremen states that conditional admission without sufficient German is not possible. Bachelor\'s applicants without C1 can enrol for a preparatory phase of up to four semesters while attending an external intensive German course.',
                                    'scope'       => 'Bachelor\'s and law applicants with a direct university entrance qualification but no C1. Master\'s applicants apply directly to the university.',
                                    'certificate' => 'C1 for the degree application. For a Bachelor\'s application it can be handed in up to 15 September (winter semester) or 15 March (summer semester).',
                                    'place'       => 'No.',
                                    'reapply'     => 'Yes, with the C1 certificate through the MOIN portal.',
                                    'portal'      => 'uni-assist (VPD), then the MOIN portal',
                                    'source'      => 'https://www.uni-bremen.de/studium/orientieren-bewerben/studienplatzbewerbung/bewerbungen-aus-dem-ausland/vorbereitungsstudium',
                                    'checked'     => '2026-10-10',
                                ],
                                'technische-universitat-braunschweig-q734324' => [
                                    'path'        => 'University German courses, no admission',
                                    'summary'     => 'TU Braunschweig runs fee-based preparatory German courses. The application confirmation for these courses is explicitly not an admission and not a conditional admission. One bilingual Bachelor\'s programme (Sustainable Engineering of Products and Processes) admits students with A2.',
                                    'scope'       => 'Course participants are not enrolled students. Exception: the SEPP Bachelor\'s programme.',
                                    'certificate' => 'Admission-free programmes: a B2 course is enough when you apply, the certificate is needed at enrolment. Other programmes: by the application deadline.',
                                    'place'       => 'No.',
                                    'reapply'     => 'Yes, a regular application.',
                                    'portal'      => 'TU Braunschweig\'s own portal',
                                    'source'      => 'https://www.tu-braunschweig.de/learning-german/preparatory-courses',
                                    'checked'     => '2026-10-10',
                                ],
                                'universitat-paderborn-q679134' => [
                                    'path'        => 'German course discontinued',
                                    'summary'     => 'Paderborn has discontinued its DSH course and no longer accepts course applications, except from refugees already living in the region. German-taught programmes require DSH-2, TestDaF 4 or an equivalent certificate.',
                                    'scope'       => 'All German-taught Bachelor\'s and Master\'s programmes.',
                                    'certificate' => 'Before you start the degree programme.',
                                    'place'       => 'Not applicable.',
                                    'reapply'     => 'Not applicable.',
                                    'portal'      => 'The university applicant portal or uni-assist, depending on the programme',
                                    'source'      => 'https://www.uni-paderborn.de/en/zfs/dsh-courses',
                                    'checked'     => '2026-10-10',
                                ],
                                'universitat-hamburg-q156725' => [
                                    'path'        => 'Certificate due at enrolment',
                                    'summary'     => 'Universität Hamburg offers no preparatory German courses. The German certificate has to be submitted with the enrolment application within the enrolment period and cannot be handed in later.',
                                    'scope'       => 'Bachelor\'s, Staatsexamen and German-taught Master\'s programmes.',
                                    'certificate' => 'With the enrolment application, within the enrolment period.',
                                    'place'       => 'Not applicable.',
                                    'reapply'     => 'Not applicable.',
                                    'portal'      => 'STiNE (Universität Hamburg\'s own portal)',
                                    'source'      => 'https://www.uni-hamburg.de/campuscenter/bewerbung/international/studium-mit-abschluss/sprachkenntnisse/deutschkenntnisse.html',
                                    'checked'     => '2026-10-10',
                                ],
                                'rwth-aachen-university-partner-019de9ee' => [
                                    'path'        => 'Certificate due at enrolment',
                                    'summary'     => 'RWTH Aachen currently offers no preparatory German courses. You can apply before you have the certificate, but the German certificate must be submitted by the end of the enrolment period.',
                                    'scope'       => 'German-taught programmes, both admission-free and NC.',
                                    'certificate' => 'By the end of the enrolment period (international Bachelor\'s applicants: C1).',
                                    'place'       => 'Not applicable.',
                                    'reapply'     => 'Not applicable.',
                                    'portal'      => 'RWTHonline',
                                    'source'      => 'https://www.rwth-aachen.de/cms/root/studium/vor-dem-studium/zugangsvoraussetzungen/~zwyn/sprachkenntnisse/',
                                    'checked'     => '2026-10-10',
                                ],
                            ],
                        ],
                    ],
                    'guides' => [
                        ['title' => 'Conditional admission guide for Bachelor\'s and Master\'s', 'slugs' => [
                            'tr' => 'germany-conditional-admission-bedingte-zulassung-guide',
                            'en' => 'conditional-admission-germany-bachelor-master-2026-guide',
                            'de' => 'bedingte-zulassung-deutschland-bachelor-master-2026-leitfaden',
                        ]],
                        ['title' => 'Conditional admission visa: which information sheet and which language level', 'slugs' => [
                            'tr' => 'conditional-admission-visa-germany-which-merkblatt-and-language-level',
                            'en' => 'conditional-admission-visa-germany-which-merkblatt-and-language-level-en',
                            'de' => 'conditional-admission-visa-germany-which-merkblatt-and-language-level-de',
                        ]],
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

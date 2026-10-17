<?php

/*
 * SEO Recovery P1-1 — beş ana arama niyeti için tek kaynak (docs/SEO_RECOVERY_P1_1_INTENT_URL_MATRIX.md).
 * Ana sayfa "Başvuru Rehberleri" bloğu, alt sayfalardaki "Ana rehber" kutusu, FAQ eşleştirmesi ve testler buradan okur.
 *
 * primary: dil başına ana URL. ['route' => ad, 'params' => [...]] ya da ['post' => slug]. Yazı ancak o dilde
 *          yayımlanmışsa bağlanır (App\Support\SeoIntents); olmayan çeviri için URL üretilmez.
 * members: aynı niyetteki alt/rakip sayfalar → "Ana rehber" kutusu. Yazı slug'ları (her dilin kendi slug'ı),
 *          route adları ve koleksiyon slug'ları. Ana URL'nin kendisi burada olmaz.
 * Metinler İngilizce __() anahtarlarıdır (lang/tr.json, lang/de.json).
 */

return [
    'intents' => [
        'university_application' => [
            'label'       => 'University application',
            'description' => 'Start here: studying in Germany and your first steps.',
            'icon'        => 'academic-cap',
            'primary'     => [
                'tr' => ['route' => 'study.germany'],
                'en' => ['route' => 'study.germany'],
                'de' => ['route' => 'study.germany'],
            ],
            'members' => [
                'posts' => [
                    'germany-bachelor-application-2026-complete-flow-guide-for-turkish-high-school',
                    'germany-bachelor-application-2026-complete-flow-guide-for-turkish-high-school-en',
                    'germany-bachelor-application-2026-complete-flow-guide-for-turkish-high-school-de',
                    'uni-assist-application-guide-a-z-reach-your-germany-university-dream',
                    'uni-assist-application-guide-a-z-reach-your-germany-university-dream-en',
                    'uni-assist-application-guide-a-z-reach-your-germany-university-dream-de',
                    'studienkolleg-your-bridge-to-university-in-germany-who-needs-it-how',
                    'studienkolleg-your-bridge-to-university-in-germany-who-needs-it-how-en',
                    'studienkolleg-your-bridge-to-university-in-germany-who-needs-it-how-de',
                ],
            ],
        ],

        'masters_requirements' => [
            'label'       => 'Master\'s requirements',
            'description' => 'Admission requirements, documents and the application path for a Master\'s.',
            'icon'        => 'book-open',
            'primary'     => [
                'tr' => ['post' => 'doing-a-masters-in-germany-2026-a-z-guide'],
                'en' => ['post' => 'doing-a-masters-in-germany-2026-a-z-guide-en'],
                'de' => ['post' => 'doing-a-masters-in-germany-2026-a-z-guide-de'],
            ],
            'members' => [
                'posts' => [
                    'your-guide-to-masters-degrees-in-germany-top-universities-and-how',
                    'your-guide-to-masters-degrees-in-germany-top-universities-and-how-en',
                    'your-guide-to-masters-degrees-in-germany-top-universities-and-how-de',
                    'english-masters-in-germany-without-german-knowledge-finding-programs-and-application',
                    'english-masters-in-germany-without-german-knowledge-finding-programs-and-application-en',
                    'english-masters-in-germany-without-german-knowledge-finding-programs-and-application-de',
                    'english-master-admission-chances-germany-gpa-fh-vs-uni-strategy',
                    'english-masters-germany-gpa-fh-uni-application',
                    'englischer-master-deutschland-gpa-fh-uni-bewerbung',
                ],
            ],
        ],

        'university_costs' => [
            'label'       => 'University costs',
            'description' => 'Tuition fees, semester contribution and the real cost of studying.',
            'icon'        => 'banknotes',
            'primary'     => [
                'tr' => ['post' => 'is-university-free-in-germany-2026-real-costs'],
                'en' => ['post' => 'is-university-free-in-germany-2026-real-costs-en'],
                'de' => ['post' => 'is-university-free-in-germany-2026-real-costs-de'],
            ],
            'members' => [
                'posts' => [
                    'is-tuition-free-university-in-germany-a-reality-your-application-guide',
                    'is-tuition-free-university-in-germany-a-reality-your-application-guide-en',
                    'is-tuition-free-university-in-germany-a-reality-your-application-guide-de',
                ],
                'routes' => ['discover.tuition-free'],
            ],
        ],

        'conditional_admission' => [
            'label'       => 'Conditional admission',
            'description' => 'What conditional admission (bedingte Zulassung) is and how to get it.',
            'icon'        => 'check-circle',
            'primary'     => [
                'tr' => ['post' => 'germany-conditional-admission-bedingte-zulassung-guide'],
                'en' => ['post' => 'conditional-admission-germany-bachelor-master-2026-guide'],
                'de' => ['post' => 'bedingte-zulassung-deutschland-bachelor-master-2026-leitfaden'],
            ],
            'members' => [
                'posts' => [
                    'conditional-admission-visa-germany-which-merkblatt-and-language-level',
                    'conditional-admission-visa-germany-which-merkblatt-and-language-level-en',
                    'conditional-admission-visa-germany-which-merkblatt-and-language-level-de',
                ],
                // "Şartlı kabul veren üniversiteler" alt niyeti olarak korunur; rehbere bağlanır.
                'collections' => ['conditional-admission-universities'],
            ],
        ],

        'application_deadlines' => [
            'label'       => 'Application deadlines',
            'description' => 'Upcoming application deadlines for winter and summer semester.',
            'icon'        => 'calendar',
            'primary'     => [
                'tr' => ['route' => 'tools.deadlines'],
                'en' => ['route' => 'tools.deadlines'],
                'de' => ['route' => 'tools.deadlines'],
            ],
            'members' => [],
        ],
    ],
];

<?php

namespace App\Services;

use App\Models\FieldOfStudy;
use App\Models\State;
use App\Models\University;
use Illuminate\Database\Eloquent\Builder;

class RankingService
{
    private const DEFAULT_LIMIT = 50;

    public function all(): array
    {
        // Sıralama listesi günde bir değişir + title/description locale-aware (__())
        // → per-locale 6 saat cache. Eyalet + alan sorgularını (her istekte) tekrarlamaz.
        return cache()->remember('rankings.all.' . app()->getLocale(), now()->addHours(6), function () {
            return $this->buildAll();
        });
    }

    private function buildAll(): array
    {
        $rankings = $this->globalRankings();

        // Eyalet bazlı
        foreach (State::orderBy('name_de')->get(['id', 'slug', 'name_tr', 'name_de']) as $state) {
            $rankings[] = [
                'slug' => $state->slug . '-universities',
                'title' => __('Universities in :state', ['state' => $state->name]),
                'description' => __('Complete list of German universities located in :state state, sorted by student count.', ['state' => $state->name]),
                'category' => 'eyalet',
                'count_label' => 'öğrenci',
            ];
        }

        // Alan bazlı (yalnızca program sayısı >= 50 olan alanlar — yeterli veri)
        $popularFields = FieldOfStudy::active()
            ->withCount(['programs' => fn ($q) => $q->where('is_active', 1)])
            ->having('programs_count', '>=', 50)
            ->orderBy('sort_order')
            ->get(['id', 'slug', 'name_tr', 'name_en', 'name_de']);

        foreach ($popularFields as $field) {
            $label = $field->name; // locale-aware accessor
            $rankings[] = [
                'slug' => 'best-' . $field->slug . '-universities',
                'title' => __('Best :field Universities', ['field' => $label]),
                'description' => __('German universities offering the most :field programs and student capacity.', ['field' => $label]),
                'category' => 'alan',
                'count_label' => 'program',
            ];
        }

        return $rankings;
    }

    public function resolve(string $slug): ?array
    {
        // Global rankings
        foreach ($this->globalRankings() as $cfg) {
            if ($cfg['slug'] === $slug) {
                return $cfg + ['builder' => $this->buildGlobal($cfg)];
            }
        }

        // Alan bazlı — pattern: best-{field-slug}-universities
        if (str_starts_with($slug, 'best-') && str_ends_with($slug, '-universities')) {
            $fieldSlug = substr($slug, strlen('best-'), -strlen('-universities'));
            $field = FieldOfStudy::where('slug', $fieldSlug)->first();
            if ($field) {
                $label = $field->name; // locale-aware
                return [
                    'slug' => $slug,
                    'title' => __('Best :field Universities', ['field' => $label]),
                    'description' => __('German universities offering the most :field programs and student capacity. Ranked by program count, student capacity, and other metrics.', ['field' => $label]),
                    'category' => 'alan',
                    'count_label' => 'program',
                    'field_name' => $label,
                    'builder' => $this->buildForField($field->id),
                ];
            }
        }

        // Eyalet bazlı (yeni EN suffix)
        if (str_ends_with($slug, '-universities')) {
            $stateSlug = substr($slug, 0, -strlen('-universities'));
            $state = State::where('slug', $stateSlug)->first();
            if ($state) {
                return [
                    'slug' => $slug,
                    'title' => __('Universities in :state', ['state' => $state->name]),
                    'description' => __('Complete list of German universities located in :state state.', ['state' => $state->name]),
                    'category' => 'eyalet',
                    'count_label' => 'öğrenci',
                    'state_name' => $state->name,
                    'builder' => $this->buildForState($state->id),
                ];
            }
        }

        return null;
    }

    public function fetchTop(array $config, int $limit = self::DEFAULT_LIMIT): array
    {
        /** @var Builder $builder */
        $builder = $config['builder'];
        $countLabel = $config['count_label'] ?? 'öğrenci';
        $fieldId = $config['field_id'] ?? null;

        $rows = $builder->limit($limit)->get();

        // Alan bazlı için program sayısını ek olarak çek
        $programCounts = [];
        if ($countLabel === 'program' && $rows->isNotEmpty() && isset($config['field_name'])) {
            $uniIds = $rows->pluck('id')->all();
            $field = \App\Models\FieldOfStudy::where('name_tr', $config['field_name'])
                ->orWhere('name_de', $config['field_name'])
                ->first();
            if ($field) {
                $programCounts = \App\Models\Program::query()
                    ->where('field_of_study_id', $field->id)
                    ->where('is_active', 1)
                    ->whereIn('university_id', $uniIds)
                    ->selectRaw('university_id, COUNT(*) as cnt')
                    ->groupBy('university_id')
                    ->pluck('cnt', 'university_id')
                    ->toArray();
            }
        }

        return $rows
            ->map(fn ($u) => [
                'rank' => 0,
                'slug' => $u->slug,
                'name_de' => $u->name_de,
                'logo_url' => $u->logo_url,
                'city_name' => $u->city?->name,
                'state_name' => $u->city?->state?->name,
                'type' => $u->type,
                'founded_year' => $u->founded_year,
                'student_count' => $u->student_count,
                'qs_world_rank' => $u->qs_world_rank,
                'the_world_rank' => $u->the_world_rank,
                'arwu_world_rank' => $u->arwu_world_rank,
                'community_mention_score' => $u->community_mention_score,
                'program_count' => $programCounts[$u->id] ?? null,
                'qs_overall_score'         => $u->qs_overall_score,
                'qs_academic_reputation'   => $u->qs_academic_reputation,
                'qs_employer_reputation'   => $u->qs_employer_reputation,
                'qs_citations_per_faculty' => $u->qs_citations_per_faculty,
                'qs_faculty_student_ratio' => $u->qs_faculty_student_ratio,
                'qs_international_faculty' => $u->qs_international_faculty,
                'qs_international_students'=> $u->qs_international_students,
                'qs_international_research'=> $u->qs_international_research,
                'qs_employment_outcomes'   => $u->qs_employment_outcomes,
                'qs_sustainability'        => $u->qs_sustainability,
            ])
            ->values()
            ->map(function ($row, $index) {
                $row['rank'] = $index + 1;
                return $row;
            })
            ->toArray();
    }

    /**
     * Legacy TR → EN slug map (301 redirect uyumluluğu için).
     */
    public const SLUG_REDIRECTS = [
        'en-buyuk-universiteler' => 'largest-universities-germany',
        'en-eski-universiteler' => 'oldest-universities-germany',
        'en-yeni-universiteler' => 'newest-universities-germany',
        'turk-ogrenci-favorisi-universiteler' => 'international-student-favorite-universities',
        'toplulukta-en-cok-konusulan-universiteler' => 'most-discussed-universities',
        'qs-world-ranking-almanya' => 'qs-world-ranking-germany',
        'the-world-ranking-almanya' => 'the-world-ranking-germany',
        'devlet-universiteleri' => 'public-universities-germany',
        'ozel-universiteleri' => 'private-universities-germany',
        'uygulamali-bilimler-universiteleri' => 'applied-sciences-universities-germany',
        'sanat-universiteleri' => 'arts-universities-germany',
    ];

    private function globalRankings(): array
    {
        return [
            [
                'slug' => 'largest-universities-germany',
                'title' => __('Germany\'s Largest Universities'),
                'description' => __('Current ranking of universities in Germany with the most students. Includes public and private institutions.'),
                'category' => 'genel',
                'count_label' => 'öğrenci',
                'sort' => 'student_count_desc',
            ],
            [
                'slug' => 'oldest-universities-germany',
                'title' => __('Germany\'s Oldest Universities'),
                'description' => __('The oldest universities in Germany — ranked by founding year. Centuries of academic tradition.'),
                'category' => 'genel',
                'count_label' => 'kuruluş',
                'sort' => 'founded_year_asc',
            ],
            [
                'slug' => 'newest-universities-germany',
                'title' => __('Germany\'s Newest Universities'),
                'description' => __('Modern German universities founded in recent years.'),
                'category' => 'genel',
                'count_label' => 'kuruluş',
                'sort' => 'founded_year_desc',
            ],

            [
                'slug' => 'international-student-favorite-universities',
                'title' => __('International Student Favorites'),
                'description' => __('Universities suited to international students: uni-assist members, English-taught programs, large student base. Scored on application ease, language flexibility, and institution size.'),
                'category' => 'oncelik',
                'count_label' => 'öğrenci',
                'sort' => 'turkish_friendly',
            ],

            [
                'slug' => 'most-discussed-universities',
                'title' => __('Most Discussed Universities in the Community'),
                'description' => __('Universities most mentioned in the AlmanyaUni Forum (120K messages) + Telegram (142K messages) pools. The real interest map of the international student community.'),
                'category' => 'oncelik',
                'count_label' => 'topluluk_skoru',
                'sort' => 'community_mentions',
            ],

            [
                'slug' => 'qs-world-ranking-germany',
                'title' => __('QS World Ranking — Germany'),
                'description' => __('German universities in the QS World University Rankings 2026. Global academic reputation + research + international recognition metrics.'),
                'category' => 'dunya',
                'count_label' => 'qs_rank',
                'sort' => 'qs_world_rank',
            ],

            [
                'slug' => 'the-world-ranking-germany',
                'title' => __('Times Higher Education (THE) Ranking — Germany'),
                'description' => __('German universities in the Times Higher Education World University Rankings. Composite score of teaching quality, research, and industry income.'),
                'category' => 'dunya',
                'count_label' => 'the_rank',
                'sort' => 'the_world_rank',
            ],

            [
                'slug' => 'arwu-shanghai-ranking-germany',
                'title' => __('ARWU (Shanghai) Ranking — Germany'),
                'description' => __('German universities in the Academic Ranking of World Universities (ARWU/Shanghai). Research-focused: Nobel laureates, highly-cited researchers, Nature/Science publications.'),
                'category' => 'dunya',
                'count_label' => 'arwu_rank',
                'sort' => 'arwu_world_rank',
            ],

            [
                'slug' => 'public-universities-germany',
                'title' => __('Public Universities in Germany'),
                'description' => __('Comprehensive list of public universities in Germany — tuition-free or very low fees.'),
                'category' => 'tur',
                'count_label' => 'öğrenci',
                'type' => 'public',
            ],
            [
                'slug' => 'private-universities-germany',
                'title' => __('Private Universities in Germany'),
                'description' => __('Comprehensive list of private universities in Germany.'),
                'category' => 'tur',
                'count_label' => 'öğrenci',
                'type' => 'private',
            ],
            [
                'slug' => 'applied-sciences-universities-germany',
                'title' => __('Universities of Applied Sciences (Fachhochschule)'),
                'description' => __('List of German universities of applied sciences (FH/HAW) offering practice-oriented education.'),
                'category' => 'tur',
                'count_label' => 'öğrenci',
                'type' => 'applied_sciences',
            ],
            [
                'slug' => 'arts-universities-germany',
                'title' => __('Art & Music Universities in Germany'),
                'description' => __('German universities offering education in fine arts, music, and design.'),
                'category' => 'tur',
                'count_label' => 'öğrenci',
                'type' => 'art',
            ],
        ];
    }

    private function baseQuery(): Builder
    {
        return University::query()
            ->where('is_active', true)
            ->with('city.state');
    }

    private function buildGlobal(array $cfg): Builder
    {
        $q = $this->baseQuery();

        if (!empty($cfg['type'])) {
            $q->where('type', $cfg['type']);
            $q->whereNotNull('student_count')->orderByDesc('student_count');
            return $q;
        }

        return match ($cfg['sort'] ?? '') {
            'student_count_desc' => $q->whereNotNull('student_count')->orderByDesc('student_count'),
            'founded_year_asc' => $q->whereNotNull('founded_year')->orderBy('founded_year'),
            'founded_year_desc' => $q->whereNotNull('founded_year')->orderByDesc('founded_year'),

            // Türk öğrenci favorisi: weighted skor
            // uni-assist üyesi (+3) + İngilizce program varlığı (+2) + öğrenci sayısı (büyüklük)
            'turkish_friendly' => $q
                ->select('universities.*')
                ->selectRaw('(
                    (CASE WHEN universities.is_uni_assist_member THEN 30 ELSE 0 END)
                    + (CASE WHEN EXISTS (
                        SELECT 1 FROM programs
                        WHERE programs.university_id = universities.id
                        AND programs.is_active = 1
                        AND programs.language IN ("en","both")
                    ) THEN 20 ELSE 0 END)
                    + (COALESCE(universities.student_count, 0) / 1000)
                ) as turkish_score')
                ->orderByDesc('turkish_score')
                ->whereNotNull('student_count'),

            'community_mentions' => $q
                ->where('community_mention_score', '>', 0)
                ->orderByDesc('community_mention_score'),

            'qs_world_rank' => $q
                ->whereNotNull('qs_world_rank')
                ->orderBy('qs_world_rank'),

            'the_world_rank' => $q
                ->whereNotNull('the_world_rank')
                ->orderBy('the_world_rank'),

            'arwu_world_rank' => $q
                ->whereNotNull('arwu_world_rank')
                ->orderBy('arwu_world_rank'),

            default => $q->orderBy('name_de'),
        };
    }

    private function buildForState(int $stateId): Builder
    {
        return $this->baseQuery()
            ->whereHas('city', fn ($q) => $q->where('state_id', $stateId))
            ->orderByRaw('student_count IS NULL, student_count DESC')
            ->orderBy('name_de');
    }

    private function buildForField(int $fieldId): Builder
    {
        return $this->baseQuery()
            ->whereHas('programs', fn ($q) => $q->where('field_of_study_id', $fieldId)->where('is_active', 1))
            ->withCount(['programs as field_programs_count' => fn ($q) => $q->where('field_of_study_id', $fieldId)->where('is_active', 1)])
            ->orderByDesc('field_programs_count')
            ->orderByDesc('student_count');
    }

    /**
     * Methodology metadata per ranking type. AIO + E-E-A-T + trust signal.
     * Tüm string'ler __() ile sarılı — yeni dil eklendiğinde lang/*.json'a key ekle.
     * Returns null = ranking için açıklayıcı methodology yok (basit liste, header zaten yeterli).
     */
    public static function methodologyFor(string $countLabel): ?array
    {
        return match ($countLabel) {
            'qs_rank' => [
                'title' => __('QS World University Rankings Methodology'),
                'intro' => __('QS World University Rankings calculates each university\'s overall score from 9 weighted indicators:'),
                'indicators' => University::QS_INDICATORS, // [key => ['weight','label','tooltip']]
                'source_label' => __('Source:'),
                'source_url' => 'https://www.topuniversities.com/qs-world-university-rankings/methodology',
                'source_text' => 'topuniversities.com/qs-world-university-rankings/methodology',
            ],

            'the_rank' => [
                'title' => __('Times Higher Education (THE) Ranking Methodology'),
                'intro' => __('THE World University Rankings uses 18 calibrated performance indicators grouped into 5 areas. Each university\'s overall score is a weighted sum:'),
                'indicators' => [
                    'teaching'       => ['weight' => 30, 'label' => __('Teaching (learning environment)'), 'tooltip' => __('Reputation survey + staff-to-student ratio + doctorates awarded + institutional income')],
                    'research_env'   => ['weight' => 29, 'label' => __('Research Environment'),           'tooltip' => __('Reputation, research income, productivity (papers per academic)')],
                    'research_qual'  => ['weight' => 30, 'label' => __('Research Quality'),               'tooltip' => __('Citation impact, research strength, research excellence, research influence')],
                    'international'  => ['weight' => 7.5,'label' => __('International Outlook'),          'tooltip' => __('Share of international students + staff + co-authored papers')],
                    'industry'       => ['weight' => 3.5,'label' => __('Industry'),                      'tooltip' => __('Income from industry + patents citing university research')],
                ],
                'source_label' => __('Source:'),
                'source_url' => 'https://www.timeshighereducation.com/world-university-rankings/world-university-rankings-2024-methodology',
                'source_text' => 'timeshighereducation.com/methodology',
            ],

            'arwu_rank' => [
                'title' => __('ARWU (Shanghai Ranking) Methodology'),
                'intro' => __('Academic Ranking of World Universities focuses on research output, alumni achievements and faculty awards. 6 indicators weighted as follows:'),
                'indicators' => [
                    'alumni'         => ['weight' => 10, 'label' => __('Alumni Award'),               'tooltip' => __('Alumni winning Nobel Prizes and Fields Medals')],
                    'award'          => ['weight' => 20, 'label' => __('Award'),                      'tooltip' => __('Staff winning Nobel Prizes and Fields Medals')],
                    'hici'           => ['weight' => 20, 'label' => __('Highly Cited Researchers'),    'tooltip' => __('Number of researchers in 21 broad subject categories')],
                    'n_s'            => ['weight' => 20, 'label' => __('Nature & Science'),            'tooltip' => __('Papers published in Nature or Science journals')],
                    'pub'            => ['weight' => 20, 'label' => __('Publications'),                'tooltip' => __('Papers indexed in Science Citation Index Expanded + Social Science Citation Index')],
                    'pcp'            => ['weight' => 10, 'label' => __('Per Capita Performance'),      'tooltip' => __('Per capita academic performance — weighted score divided by full-time academic staff')],
                ],
                'source_label' => __('Source:'),
                'source_url' => 'https://www.shanghairanking.com/methodology/arwu/2024',
                'source_text' => 'shanghairanking.com/methodology',
            ],

            'topluluk_skoru' => [
                'title' => __('AlmanyaUni Community Mention Score Methodology'),
                'intro' => __('Our proprietary community signal — measures how actively an institution is discussed in our verified pools (Forum 120K messages + Telegram 142K messages + comment threads). Reflects real interest of the international student community in Turkey + abroad.'),
                'indicators' => [
                    'mention_freq'   => ['weight' => 60, 'label' => __('Mention frequency'),       'tooltip' => __('How often the university name appears in community messages (deduplicated per user per week)')],
                    'sentiment'      => ['weight' => 25, 'label' => __('Sentiment polarity'),       'tooltip' => __('Positive vs negative discussion ratio — calculated from message context')],
                    'recency'        => ['weight' => 15, 'label' => __('Recency boost'),            'tooltip' => __('Mentions in last 90 days weighted higher (community heat)')],
                ],
                'source_label' => __('Methodology:'),
                'source_url' => '/about-data',
                'source_text' => __('See data methodology page'),
            ],

            'öğrenci' => [
                'title' => __('Methodology — Largest Universities'),
                'intro' => __('Universities ranked by total enrolled student count (Bachelor + Master + PhD). Data source:'),
                'indicators' => [],
                'source_label' => __('Source:'),
                'source_url' => 'https://www.hochschulkompass.de',
                'source_text' => 'Hochschulkompass + Wikipedia (verified)',
                'note' => __('Numbers updated annually from each university\'s official statistics report.'),
            ],

            'kuruluş' => [
                'title' => __('Methodology — Founding Year'),
                'intro' => __('Universities ranked by founding year (oldest first or newest first depending on list). Includes institutions established as universities and those promoted from earlier academies.'),
                'indicators' => [],
                'source_label' => __('Source:'),
                'source_url' => 'https://www.hochschulkompass.de',
                'source_text' => 'Hochschulkompass + university self-reported history',
            ],

            'program' => [
                'title' => __('Methodology — Field Programme Count'),
                'intro' => __('Universities ranked by number of active programmes in the selected field (Bachelor, Master, PhD combined). Higher count = broader specialisation in the field.'),
                'indicators' => [],
                'source_label' => __('Source:'),
                'source_url' => 'https://www.hochschulkompass.de',
                'source_text' => 'Hochschulkompass + DAAD programme database',
                'note' => __('Programme counts updated quarterly via automated sync.'),
            ],

            default => null,
        };
    }
}

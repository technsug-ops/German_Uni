<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\JobPosting;
use App\Models\University;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SampleJobPostingsSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotent: don't double-seed
        if (JobPosting::count() >= 10) {
            $this->command?->info('Job postings already seeded, skipping.');
            return;
        }

        $byCity = fn (string $slug) => City::where('slug', 'like', $slug . '%')->orWhere('name_de', $slug)->first();
        $byUniName = fn (string $name) => University::where('name_de', 'like', '%' . $name . '%')->orWhere('short_name', 'like', '%' . $name . '%')->first();
        $byFieldSlug = fn (string $slug) => FieldOfStudy::where('slug', $slug)->first();

        $jobs = [
            [
                'title'            => 'PhD Position in Machine Learning for Computer Vision',
                'position_type'    => 'phd',
                'employment_type'  => 'fixed_term',
                'language'         => 'en',
                'salary_band'      => 'TV-L E13 / 65%',
                'salary_min_eur'   => 27000,
                'salary_max_eur'   => 32000,
                'university_name'  => 'Technische Universität München',
                'city_name'        => 'München',
                'field_slug'       => 'bilisim',
                'is_remote'        => false,
                'is_featured'      => true,
                'excerpt'          => 'Join the Computer Vision Group for a fully-funded PhD on self-supervised representation learning. 3+1 year contract, supervisor team of 4 PIs.',
                'description'      => "We are recruiting a doctoral researcher to work on **self-supervised learning** for computer vision tasks. The position is funded for 3 years, with possible extension to 4.\n\nYou will:\n\n- Develop new pre-training objectives for video understanding\n- Publish at CVPR / NeurIPS / ICCV\n- Co-supervise Bachelor and Master theses\n- Collaborate with industry partners (BMW, Siemens)",
                'requirements'     => "- Master's degree in Computer Science, EE, or Mathematics\n- Strong PyTorch / JAX skills\n- 1+ first-author paper at a peer-reviewed venue (preferred)\n- English fluency (no German required)\n- Self-driven research attitude",
                'application_url'  => 'https://example.org/tum-phd-cv',
                'source_name'      => 'THE Jobs',
                'deadline_in_days' => 28,
            ],
            [
                'title'            => 'Postdoctoral Researcher in Sustainable Materials',
                'position_type'    => 'postdoc',
                'employment_type'  => 'fixed_term',
                'language'         => 'en',
                'salary_band'      => 'TV-L E14 / 100%',
                'salary_min_eur'   => 54000,
                'salary_max_eur'   => 68000,
                'university_name'  => 'RWTH Aachen',
                'city_name'        => 'Aachen',
                'field_slug'       => 'muhendislik',
                'is_remote'        => false,
                'is_featured'      => true,
                'excerpt'          => '2-year postdoc on bio-based polymers in the DFG Excellence Cluster. €54-68k/yr, conference budget €4k.',
                'description'      => "The Excellence Cluster on Sustainable Materials seeks a postdoctoral researcher.\n\n**Project:** Mechanistic studies of enzymatic polymerisation for high-performance bioplastics.\n\nResources: own lab bench, dedicated PhD student, full instrument access (NMR / SAXS / TEM).",
                'requirements'     => "- PhD in Polymer Chemistry, Chemical Engineering, or related\n- Hands-on enzyme expression / characterisation\n- Strong publication record (h-index > 5)\n- German B1 helpful but not required",
                'application_url'  => 'https://example.org/rwth-postdoc',
                'source_name'      => 'Academic Positions',
                'deadline_in_days' => 21,
            ],
            [
                'title'            => 'Wissenschaftlicher Mitarbeiter / Lecturer in Deutsch als Fremdsprache',
                'position_type'    => 'lecturer',
                'employment_type'  => 'fixed_term',
                'language'         => 'de',
                'salary_band'      => 'TV-L E13 / 75%',
                'salary_min_eur'   => 36000,
                'salary_max_eur'   => 41000,
                'university_name'  => 'Humboldt-Universität',
                'city_name'        => 'Berlin',
                'field_slug'       => 'dil-kultur',
                'is_remote'        => false,
                'excerpt'          => '4 SWS Lehrdeputat in Deutsch als Fremdsprache. Schwerpunkt B2-C1 Niveau, internationale Studierende.',
                'description'      => "Sie unterrichten Deutsch als Fremdsprache auf B2-C1 Niveau und entwickeln Lehrmaterialien für unsere internationalen Studierenden.\n\n4 SWS Lehrdeputat plus 50% Forschungsanteil zu Spracherwerb.",
                'requirements'     => "- Magister / M.A. in DaF, Germanistik oder Linguistik\n- 2+ Jahre Unterrichtserfahrung\n- Erfahrung mit GeR-konformen Curricula\n- Bereitschaft zur Promotion erwünscht",
                'application_url'  => 'https://example.org/hu-daf',
                'source_name'      => 'academics.de',
                'deadline_in_days' => 35,
            ],
            [
                'title'            => 'W2 Professorship in Quantum Computing',
                'position_type'    => 'professor',
                'employment_type'  => 'permanent',
                'language'         => 'both',
                'salary_band'      => 'W2',
                'salary_min_eur'   => 85000,
                'salary_max_eur'   => 110000,
                'university_name'  => 'Universität Hamburg',
                'city_name'        => 'Hamburg',
                'field_slug'       => 'matematik-doga',
                'is_remote'        => false,
                'is_featured'      => true,
                'excerpt'          => 'Tenure-track W2 chair in quantum information. Lab budget €450k start-up + 2 PhD positions.',
                'description'      => "The Faculty of Physics invites applications for a **W2 Professorship in Quantum Computing**.\n\nThe successful candidate will build an internationally competitive research group at the intersection of quantum hardware and algorithm design.",
                'requirements'     => "- Habilitation or equivalent international track record\n- Strong publication record (Nature / Science / PRX-level)\n- Teaching at all levels (BSc, MSc, PhD)\n- Commitment to diversity and outreach",
                'application_url'  => 'https://example.org/uhh-w2-quantum',
                'source_name'      => 'THE Jobs',
                'deadline_in_days' => 60,
            ],
            [
                'title'            => 'Research Associate — Robotics and Autonomous Systems',
                'position_type'    => 'researcher',
                'employment_type'  => 'fixed_term',
                'language'         => 'en',
                'salary_band'      => 'TV-L E13 / 100%',
                'salary_min_eur'   => 50000,
                'salary_max_eur'   => 62000,
                'university_name'  => 'Karlsruhe Institute of Technology',
                'city_name'        => 'Karlsruhe',
                'field_slug'       => 'muhendislik',
                'is_remote'        => false,
                'excerpt'          => '3-year research position on multi-robot coordination. Industry collaboration with Bosch and KUKA.',
                'description'      => "Join the Intelligent Process Automation Lab to work on **multi-robot coordination** under partial observability.\n\nThe position includes substantial industry collaboration (Bosch, KUKA) and travel budget for IROS / ICRA / RSS.",
                'requirements'     => "- PhD in Robotics, Control or related (or near completion)\n- C++ / Python / ROS2 fluency\n- Experience with real robot hardware\n- English C1, German A2+ welcome",
                'application_url'  => 'https://example.org/kit-robotics',
                'source_name'      => 'EURAXESS',
                'deadline_in_days' => 14,
            ],
            [
                'title'            => 'Postdoc in Climate Modelling — Earth System Dynamics',
                'position_type'    => 'postdoc',
                'employment_type'  => 'fixed_term',
                'language'         => 'en',
                'salary_band'      => 'TV-L E14 / 100%',
                'salary_min_eur'   => 56000,
                'salary_max_eur'   => 70000,
                'university_name'  => 'Universität Hamburg',
                'city_name'        => 'Hamburg',
                'field_slug'       => 'matematik-doga',
                'is_remote'        => true,
                'excerpt'          => '2-year remote-friendly postdoc with the Cluster of Excellence CLICCS. High-performance computing access (DKRZ).',
                'description'      => "We seek an early-career researcher to investigate **tipping points in the Earth climate system** using state-of-the-art ESMs.\n\nThe position is part of the CLICCS Cluster of Excellence. Hybrid working — minimum 2 days/week on site.",
                'requirements'     => "- PhD in atmospheric science, oceanography or climate physics\n- Strong programming (Python, Fortran)\n- Experience with ICON / CESM / MPI-ESM\n- First-author publications in journals like JCLI / GRL",
                'application_url'  => 'https://example.org/uhh-climate',
                'source_name'      => 'EURAXESS',
                'deadline_in_days' => 42,
            ],
            [
                'title'            => 'PhD Position — Sustainable Architecture and Urban Design',
                'position_type'    => 'phd',
                'employment_type'  => 'fixed_term',
                'language'         => 'both',
                'salary_band'      => 'TV-L E13 / 65%',
                'salary_min_eur'   => 28000,
                'salary_max_eur'   => 33000,
                'university_name'  => 'TU Berlin',
                'city_name'        => 'Berlin',
                'field_slug'       => 'sanat-tasarim',
                'is_remote'        => false,
                'excerpt'          => '3-year PhD on circular construction materials and adaptive reuse in dense European cities.',
                'description'      => "The Chair of Sustainable Building Design invites applications for a PhD position on **circular construction materials**.\n\nYou will combine field studies in 3 EU cities with life-cycle assessment modelling.",
                'requirements'     => "- Master's in Architecture, Civil Engineering or Urban Planning\n- LCA / BIM software experience\n- English C1 + German B1 (project partly in DE)\n- Strong portfolio of design work",
                'application_url'  => 'https://example.org/tub-arch',
                'source_name'      => 'academics.de',
                'deadline_in_days' => 30,
            ],
            [
                'title'            => 'Junior Professor (W1 Tenure-Track) — Computational Linguistics',
                'position_type'    => 'professor',
                'employment_type'  => 'fixed_term',
                'language'         => 'en',
                'salary_band'      => 'W1 with tenure-track to W2',
                'salary_min_eur'   => 68000,
                'salary_max_eur'   => 75000,
                'university_name'  => 'Universität Stuttgart',
                'city_name'        => 'Stuttgart',
                'field_slug'       => 'dil-kultur',
                'is_remote'        => false,
                'excerpt'          => '6-year tenure-track W1 → W2 in NLP / Computational Linguistics. €120k start-up, 1 PhD slot.',
                'description'      => "The Institute for Natural Language Processing invites applications for a **Junior Professorship with tenure track**.\n\nResearch focus: low-resource NLP, multilingual models, LLM evaluation methodology.",
                'requirements'     => "- PhD in NLP, CL or CS with strong linguistic background\n- Publications at ACL / EMNLP / NAACL\n- Teaching experience\n- Plan for an externally-funded research group",
                'application_url'  => 'https://example.org/stuttgart-w1',
                'source_name'      => 'THE Jobs',
                'deadline_in_days' => 50,
            ],
            [
                'title'            => 'Wissenschaftliche Hilfskraft — Public Health Data Analysis',
                'position_type'    => 'researcher',
                'employment_type'  => 'part_time',
                'language'         => 'both',
                'salary_band'      => 'WHK €13.25/h',
                'salary_min_eur'   => null,
                'salary_max_eur'   => null,
                'university_name'  => 'Charité Berlin',
                'city_name'        => 'Berlin',
                'field_slug'       => 'tip-saglik',
                'is_remote'        => true,
                'excerpt'          => '20h/week student-researcher role analysing COVID epidemiology data. Open to current Master students.',
                'description'      => "The Institute of Public Health hires a student researcher (Wissenschaftliche Hilfskraft) for **20 hours/week** working on epidemiological data analysis.\n\nGood entry into a future Master thesis / PhD project.",
                'requirements'     => "- Currently enrolled Master student\n- R or Stata proficiency\n- German B2 sufficient (no clinical contact)\n- Available for at least 6 months",
                'application_url'  => 'https://example.org/charite-whk',
                'source_name'      => 'Charité Karriereportal',
                'deadline_in_days' => 10,
            ],
            [
                'title'            => 'Senior Researcher — Industrial AI (Industry Collaboration)',
                'position_type'    => 'industry',
                'employment_type'  => 'permanent',
                'language'         => 'en',
                'salary_band'      => 'TV-L E14 / 100%',
                'salary_min_eur'   => 58000,
                'salary_max_eur'   => 72000,
                'university_name'  => 'Fraunhofer IPK',
                'city_name'        => 'Berlin',
                'field_slug'       => 'bilisim',
                'is_remote'        => true,
                'is_featured'      => true,
                'excerpt'          => 'Permanent senior researcher at Fraunhofer IPK. Translate ML research into industrial production lines.',
                'description'      => "Lead research projects on **machine learning for production engineering** in close collaboration with German Mittelstand companies.\n\nThe position is permanent and explicitly bridges academic publication and industrial deployment.",
                'requirements'     => "- PhD in CS / ME / EE\n- 3+ years industry or industrial-research experience\n- Track record of bringing research to production\n- Comfortable with bilingual (DE/EN) project teams",
                'application_url'  => 'https://example.org/fraunhofer-ipk',
                'source_name'      => 'Fraunhofer Karriere',
                'deadline_in_days' => 45,
            ],
        ];

        $created = 0;
        foreach ($jobs as $j) {
            $uni   = $byUniName($j['university_name']);
            $city  = $byCity($j['city_name']);
            $field = $byFieldSlug($j['field_slug']);

            JobPosting::create([
                'title'             => $j['title'],
                'position_type'     => $j['position_type'],
                'employment_type'   => $j['employment_type'],
                'language'          => $j['language'],
                'salary_band'       => $j['salary_band'] ?? null,
                'salary_min_eur'    => $j['salary_min_eur'] ?? null,
                'salary_max_eur'    => $j['salary_max_eur'] ?? null,
                'university_id'     => $uni?->id,
                'city_id'           => $city?->id,
                'field_of_study_id' => $field?->id,
                'is_remote'         => $j['is_remote'] ?? false,
                'is_featured'       => $j['is_featured'] ?? false,
                'is_active'         => true,
                'excerpt'           => $j['excerpt'],
                'description'       => $j['description'],
                'requirements'      => $j['requirements'],
                'application_url'   => $j['application_url'],
                'source_name'       => $j['source_name'] ?? null,
                'posted_at'         => Carbon::now()->subDays(rand(1, 10))->toDateString(),
                'deadline_at'       => Carbon::now()->addDays($j['deadline_in_days'])->toDateString(),
            ]);
            $created++;
        }

        $this->command?->info("Seeded {$created} sample job postings.");
    }
}

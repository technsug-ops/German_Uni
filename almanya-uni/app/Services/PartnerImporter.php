<?php

namespace App\Services;

use App\Models\FieldOfStudy;
use App\Models\Program;
use App\Models\University;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PartnerImporter
{
    /**
     * Partner'ın 20 study field adından bizim 10 FieldOfStudy slug'una map.
     */
    private const FIELD_MAP = [
        'Social Sciences, Humanities and Linguistics' => 'sosyal-bilimler',
        'Business Management and Economics'           => 'hukuk-ekonomi',
        'Engineering'                                 => 'muhendislik',
        'Engineering Sciences'                        => 'muhendislik',
        'Natural Sciences and Mathematics'            => 'matematik-doga',
        'Mathematics, Natural Sciences'               => 'matematik-doga',
        'Arts, Design and Architecture'               => 'sanat-tasarim',
        'Art, Music, Design'                          => 'sanat-tasarim',
        'Computer Science and IT'                     => 'bilisim',
        'Medicine and Health'                         => 'tip-saglik',
        'Medicine, Health Sciences'                   => 'tip-saglik',
        'Law'                                         => 'hukuk-ekonomi',
        'Economic Sciences, Law'                      => 'hukuk-ekonomi',
        'Agricultural and Forestry Sciences'          => 'tarim-ormancilik',
        'Agricultural and Forest Sciences'            => 'tarim-ormancilik',
        'Hospitality, Tourism and Sports'             => 'veteriner-spor',
        'Humanities and Social Sciences'              => 'sosyal-bilimler',
        'Language and Cultural Studies'               => 'dil-kultur',
        'Public Administration'                       => 'hukuk-ekonomi',
        'Teaching Degrees'                            => 'dil-kultur',
    ];

    /** @var array<string, int> cache: name (lower-normalized) → university_id */
    private array $uniNameCache = [];
    /** @var array<string, int> cache: partner_id → university_id */
    private array $uniByPartnerId = [];
    /** @var array<string, int> field slug → id */
    private array $fieldCache = [];

    public function importUniversities(string $path): array
    {
        $data = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        $stats = ['linked' => 0, 'unlinked' => 0, 'created' => 0];

        foreach ($data as $row) {
            // Önce isim ile mevcut DB üni'sine eşleştir
            $existing = $this->findUniversityByName($row['name']);

            if ($existing) {
                $existing->update([
                    'partner_id'           => $row['id'],
                    'is_uni_assist_member' => $row['is_uni_assist_member'] ?? null,
                    'uni_assist_id'        => $row['uni_assist_id'] ?? null,
                ]);
                $this->uniByPartnerId[$row['id']] = $existing->id;
                $stats['linked']++;
            } else {
                $stats['unlinked']++;
                // Bilinmeyen üni'leri partner_id'le saklamak için yeni kayıt
                $u = University::create([
                    'partner_id'           => $row['id'],
                    'name_de'              => $row['name'],
                    'name_tr'              => $row['name'], // TR çevirisi sonradan
                    'slug'                 => Str::slug($row['name']) . '-partner-' . substr($row['id'], 0, 8),
                    'is_uni_assist_member' => $row['is_uni_assist_member'] ?? null,
                    'uni_assist_id'        => $row['uni_assist_id'] ?? null,
                    'data_source'          => 'partner',
                    'is_active'            => true,
                ]);
                $this->uniByPartnerId[$row['id']] = $u->id;
                $stats['created']++;
            }
        }

        return $stats;
    }

    public function importPrograms(string $path, callable $tick = null, int $limit = 0): array
    {
        $data = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        if ($limit > 0) {
            $data = array_slice($data, 0, $limit);
        }

        // Eğer importUniversities çağrılmadıysa partner_id eşleşmesini DB'den çek
        if (empty($this->uniByPartnerId)) {
            $this->uniByPartnerId = University::whereNotNull('partner_id')
                ->pluck('id', 'partner_id')
                ->toArray();
        }

        // Field of study cache
        $this->fieldCache = FieldOfStudy::pluck('id', 'slug')->toArray();

        $stats = ['imported' => 0, 'updated' => 0, 'skipped_no_uni' => 0, 'errors' => 0];

        foreach ($data as $row) {
            try {
                $uniId = $this->uniByPartnerId[$row['university_id']] ?? null;

                // Üni eşleşmediyse partner adı üzerinden son bir deneme
                if (! $uniId) {
                    $match = $this->findUniversityByName($row['university_name']);
                    if ($match) {
                        $uniId = $match->id;
                        $this->uniByPartnerId[$row['university_id']] = $uniId;
                    }
                }

                if (! $uniId) {
                    $stats['skipped_no_uni']++;
                    continue;
                }

                $this->upsertProgram($row, $uniId, $stats);
            } catch (\Throwable $e) {
                $stats['errors']++;
                logger()->warning("PartnerImporter: program {$row['id']} failed: " . $e->getMessage());
            }

            if ($tick) {
                $tick();
            }
        }

        return $stats;
    }

    /**
     * Public wrapper — partner:sync komutundan API row'ları için çağrılır.
     * Aynı upsert mantığını snapshot ZIP ile API verisi paylaşır.
     */
    public function upsertProgramFromApi(array $row, int $uniId, array &$stats): void
    {
        $this->upsertProgram($row, $uniId, $stats);
    }

    private function upsertProgram(array $row, int $uniId, array &$stats): void
    {
        $fieldId = $this->mapStudyField($row['study_fields'] ?? []);

        $slug = $this->buildProgramSlug($row['course_name'], $row['degree_type'] ?? null, $row['id']);

        $attrs = [
            'university_id'                 => $uniId,
            'field_of_study_id'             => $fieldId,
            'partner_university_name'       => $row['university_name'] ?? null,
            'name_de'                       => $row['course_name'],
            'slug'                          => $slug,
            'degree'                        => $row['degree_type'] ?? 'unknown',
            'degree_specification'          => $row['degree_specification'] ?? null,
            'language'                      => $this->normalizeLanguage($row['language'] ?? null),
            'duration_semesters'            => $row['duration_semesters'] ?? null,
            'location'                      => $row['location'] ?? null,
            'tuition_fee_eur'               => $row['tuition_eur_per_semester'] ?? null,
            'application_fee_eur'           => $row['application_fee_eur'] ?? null,
            'cost_per_semester_eur'         => $row['cost_per_semester_eur'] ?? null,
            'application_deadline_summer'   => $row['application_deadline_summer'] ?? null,
            'application_deadline_winter'   => $row['application_deadline_winter'] ?? null,
            'admission_mode'                => $row['admission_type'] ?? null,
            'nc_value'                      => $row['nc_value'] ?? null,
            'subjects'                      => $row['subjects'] ?? null,
            'study_fields_raw'              => $row['study_fields'] ?? null,
            'description_tr'                => $row['description_tr'] ?? null,
            'description_en'                => $row['description_en'] ?? null,
            'qualification_requirements_tr' => $row['qualification_requirements_tr'] ?? null,
            'language_requirements_tr'      => $row['language_requirements_tr'] ?? null,
            'required_documents_tr'         => $row['required_documents_tr'] ?? null,
            'source'                        => 'partner',
            'last_synced_at'                => now(),
            'is_active'                     => true,
        ];

        $existing = Program::where('partner_id', $row['id'])->first();

        if ($existing) {
            $existing->update($attrs);
            $stats['updated']++;
        } else {
            Program::create(array_merge(['partner_id' => $row['id']], $attrs));
            $stats['imported']++;
        }
    }

    /**
     * İsim ile mevcut üniversite bul: tam → slug → fuzzy.
     */
    public function findUniversityByName(string $name): ?University
    {
        $key = mb_strtolower(trim($name));

        if (isset($this->uniNameCache[$key])) {
            return University::find($this->uniNameCache[$key]);
        }

        // 1) Tam isim
        $hit = University::where('name_de', $name)
            ->orWhere('name_en', $name)
            ->orWhere('short_name', $name)
            ->first();

        // 2) İsim parçaları ile fuzzy LIKE
        if (! $hit) {
            $hit = University::where('name_de', 'like', "%$name%")->first();
        }

        // 3) Slug normalize
        if (! $hit) {
            $slug = Str::slug($name);
            // Slug'lar genelde "-q12345" gibi Wikidata ID ile bitiyor; o yüzden prefix arıyoruz
            $hit = University::where('slug', 'like', $slug . '%')->first();
        }

        if ($hit) {
            $this->uniNameCache[$key] = $hit->id;
        }
        return $hit;
    }

    private function mapStudyField(array $studyFields): ?int
    {
        foreach ($studyFields as $sf) {
            $slug = self::FIELD_MAP[$sf] ?? null;
            if ($slug && isset($this->fieldCache[$slug])) {
                return $this->fieldCache[$slug];
            }
        }
        return null;
    }

    private function normalizeLanguage(?string $lang): ?string
    {
        if (! $lang) return null;
        return match (strtolower($lang)) {
            'german', 'de'   => 'de',
            'english', 'en'  => 'en',
            'both'           => 'both',
            default          => $lang,
        };
    }

    private function buildProgramSlug(string $name, ?string $degree, string $partnerId): string
    {
        $base = Str::slug($name);
        if ($degree) {
            $base .= '-' . Str::slug($degree);
        }
        // UUIDv7 prefix sürekli aynı (timestamp tabanlı), son 8 karakter (random)
        // daha güvenli — slug çakışmasını minimumlar.
        $suffix = substr(str_replace('-', '', $partnerId), -8);
        return Str::limit($base, 180, '') . '-' . $suffix;
    }
}

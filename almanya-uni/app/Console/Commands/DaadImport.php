<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Models\University;
use App\Services\DaadApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DaadImport extends Command
{
    protected $signature = 'daad:import
        {--degree= : Tek bir degree id (1=BA, 2=MA, 3=PhD, ... default: hepsi)}
        {--dry-run : DB\'ye yazma}
        {--limit= : Test için max kayıt}
        {--create-shells : Üniversite DB\'de yoksa shell olarak ekle}';

    protected $description = 'DAAD International Programmes (~2.776 İngilizce program) import eder.';

    private int $statsCreated = 0;
    private int $statsUpdated = 0;
    private int $statsSkippedNoUni = 0;
    private int $statsShellCreated = 0;
    private int $statsSeen = 0;

    public function handle(DaadApiClient $api): int
    {
        $dryRun = $this->option('dry-run');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $createShells = $this->option('create-shells');

        $degrees = $this->option('degree')
            ? [(int) $this->option('degree')]
            : array_keys(DaadApiClient::DEGREES);

        $this->info($dryRun ? '🔍 DRY-RUN' : '▶ IMPORT');
        $this->line('Degrees: ' . implode(',', $degrees));
        $this->line('Shell create: ' . ($createShells ? 'YES' : 'NO (üni yoksa skip)'));
        $this->newLine();

        // Scout sync kapat — sadece Searchable kullanan model'ler için
        if (method_exists(University::class, 'disableSearchSyncing')) {
            University::disableSearchSyncing();
        }

        try {
            foreach ($degrees as $degree) {
                $degreeName = DaadApiClient::DEGREES[$degree] ?? "degree-$degree";
                $this->line("─── Degree $degree ($degreeName) ───");

                $stop = false;
                $stats = $api->paginate($degree, function (array $c) use ($dryRun, $createShells, $limit, &$stop) {
                    $this->statsSeen++;
                    if ($limit && $this->statsSeen >= $limit) {
                        $stop = true;
                        return;
                    }
                    if ($stop) return;
                    $this->processCourse($c, $dryRun, $createShells);
                });

                $this->line("  toplam={$stats['total']} fetched={$stats['fetched']}");
                if ($stop) break;
            }
        } finally {
            if (method_exists(University::class, 'enableSearchSyncing')) {
                University::enableSearchSyncing();
            }
        }

        $this->newLine();
        $this->info('═══ ÖZET ═══');
        $this->line("  Görülen kayıt:    {$this->statsSeen}");
        $this->line("  Yeni program:     {$this->statsCreated}");
        $this->line("  Güncellenen:      {$this->statsUpdated}");
        $this->line("  Skipped (üni yok): {$this->statsSkippedNoUni}");
        $this->line("  Shell üni eklendi: {$this->statsShellCreated}");
        $this->line("  DB toplam program: " . Program::count());

        return self::SUCCESS;
    }

    private function processCourse(array $c, bool $dryRun, bool $createShells): void
    {
        $academy = trim((string) ($c['academy'] ?? ''));
        if (!$academy) {
            $this->statsSkippedNoUni++;
            return;
        }

        $university = $this->matchUniversity($academy);
        if (!$university) {
            if ($createShells && !$dryRun) {
                $university = $this->createShellUniversity($academy, $c);
                $this->statsShellCreated++;
            } else {
                $this->statsSkippedNoUni++;
                return;
            }
        }

        $degreeMap = [1=>'bachelor',2=>'master',3=>'phd',4=>'other',5=>'other',6=>'studienkolleg',7=>'sprachkurs',8=>'other',10=>'other'];
        $degreeId = (int) ($c['courseType'] ?? 0);
        $degree = $degreeMap[$degreeId] ?? 'other';

        $language = $this->inferLanguage($c['languages'] ?? []);
        $duration = $this->parseDuration($c['programmeDuration'] ?? null);
        $tuition = $this->parseMoney($c['tuitionFees'] ?? null);
        $deadlineSummary = $this->cleanHtml($c['applicationDeadline'] ?? null);
        $descriptionEn = $c['courseName'] ?? null;
        $slug = Str::slug(($c['courseName'] ?? 'daad') . '-' . $degree . '-daad' . ($c['id'] ?? ''));

        $imageUrl = !empty($c['image']) ? 'https://www2.daad.de' . $c['image'] : null;
        $langLvlDe = $this->joinLevels($c['languageLevelGerman'] ?? null);
        $langLvlEn = $this->joinLevels($c['languageLevelEnglish'] ?? null);
        $isOnline = !empty($c['isElearning']) || !empty($c['isCompleteOnlinePossible']);
        $financialSupport = $this->joinList($c['financialSupport'] ?? null);
        $supportInfo = $this->joinList($c['supportInternationalStudents'] ?? null);

        $payload = [
            'university_id' => $university->id,
            'name_en' => $c['courseName'] ?? null,
            'name_de' => $c['courseNameShort'] ?? $c['courseName'] ?? null,
            'slug' => substr($slug, 0, 191),
            'degree' => $degree,
            'language' => $language,
            'duration_semesters' => $duration,
            'start_semester' => $c['beginning'] ?? null,
            'tuition_fee_eur' => $tuition,
            'admission_summary' => $deadlineSummary ? substr($deadlineSummary, 0, 5000) : null,
            'description_en' => $descriptionEn,
            'image_url' => $imageUrl,
            'language_level_de' => $langLvlDe,
            'language_level_en' => $langLvlEn,
            'is_online' => $isOnline,
            'study_form' => $isOnline ? 'online' : null,
            'financial_support' => $financialSupport,
            'support_info' => $supportInfo,
            'source_url' => isset($c['link']) ? 'https://www2.daad.de' . $c['link'] : null,
            'source' => 'daad',
            'source_id' => (string) ($c['id'] ?? ''),
            'study_fields_raw' => array_values(array_filter([
                $c['subject'] ?? null,
            ])),
            'is_active' => true,
            'last_synced_at' => now(),
        ];

        if ($dryRun) {
            if ($this->statsSeen <= 3) {
                $this->line('   [dry] ' . substr($c['courseName'] ?? '?', 0, 50) . ' @ ' . $university->name_de);
            }
            return;
        }

        $existing = Program::where('source', 'daad')->where('source_id', (string) $c['id'])->first();
        if ($existing) {
            $existing->update($payload);
            $this->statsUpdated++;
        } else {
            Program::create($payload);
            $this->statsCreated++;
        }
    }

    private function matchUniversity(string $academy): ?University
    {
        $normalized = $this->normalizeUniName($academy);

        $candidates = University::query()
            ->where(function ($q) use ($academy, $normalized) {
                $q->where('name_de', $academy)
                    ->orWhere('name_en', $academy)
                    ->orWhere('short_name', $academy)
                    ->orWhereRaw('LOWER(name_de) = ?', [mb_strtolower($academy)])
                    ->orWhereRaw('LOWER(name_en) = ?', [mb_strtolower($academy)]);
            })
            ->limit(5)
            ->get();

        if ($candidates->count() === 1) {
            return $candidates->first();
        }
        if ($candidates->isEmpty()) {
            return $this->fuzzyMatch($normalized);
        }
        return $candidates->first();
    }

    private function fuzzyMatch(string $normalized): ?University
    {
        $best = null;
        $bestScore = 0;

        foreach (University::query()->select('id', 'name_de', 'name_en', 'short_name')->cursor() as $u) {
            $candidates = array_filter([$u->name_de, $u->name_en, $u->short_name]);
            foreach ($candidates as $cand) {
                $candNorm = $this->normalizeUniName($cand);
                if ($candNorm === '') continue;
                similar_text($normalized, $candNorm, $pct);
                if ($pct > $bestScore) {
                    $bestScore = $pct;
                    $best = $u;
                }
            }
        }

        return $bestScore >= 88 ? $best : null;
    }

    private function normalizeUniName(string $s): string
    {
        $s = mb_strtolower($s);
        $s = preg_replace('/\b(university|universität|hochschule|fachhochschule|technische|of|der|für|und|the)\b/u', '', $s);
        $s = preg_replace('/[^a-z0-9äöüß ]/u', ' ', $s);
        return trim(preg_replace('/\s+/', ' ', $s));
    }

    private function createShellUniversity(string $academy, array $c): University
    {
        $slug = Str::slug($academy);
        $original = $slug;
        $i = 1;
        while (University::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }
        return University::create([
            'name_de' => $academy,
            'name_en' => $academy,
            'name_tr' => $academy,
            'slug' => $slug,
            'type' => 'public',
            'data_source' => 'daad',
            'is_active' => true,
            'is_official' => true,
        ]);
    }

    private function inferLanguage(array $langs): string
    {
        $set = array_map('strtolower', $langs);
        $hasEn = in_array('english', $set, true);
        $hasDe = in_array('german', $set, true) || in_array('deutsch', $set, true);
        return match (true) {
            $hasEn && $hasDe => 'both',
            $hasEn => 'en',
            $hasDe => 'de',
            default => 'en',
        };
    }

    private function parseDuration(?string $raw): ?int
    {
        if (!$raw) return null;
        if (preg_match('/(\d+)\s*semester/i', $raw, $m)) {
            return (int) $m[1];
        }
        if (preg_match('/(\d+)\s*year/i', $raw, $m)) {
            return (int) $m[1] * 2;
        }
        return null;
    }

    private function parseMoney(?string $raw): ?float
    {
        if (!$raw) return null;
        $clean = preg_replace('/[^0-9.,]/', '', $raw);
        $clean = str_replace(',', '', $clean);
        return is_numeric($clean) ? (float) $clean : null;
    }

    private function cleanHtml(?string $html): ?string
    {
        if (!$html) return null;
        return trim(preg_replace('/\s+/u', ' ', strip_tags($html)));
    }

    private function joinLevels($raw): ?string
    {
        if (!is_array($raw) || empty($raw)) return null;
        $clean = array_values(array_filter(array_map('trim', $raw)));
        return $clean ? implode(', ', $clean) : null;
    }

    private function joinList($raw): ?string
    {
        if (!is_array($raw) || empty($raw)) return null;
        $items = [];
        foreach ($raw as $item) {
            if (is_string($item) && trim($item) !== '') {
                $items[] = trim($item);
            }
        }
        return $items ? implode("\n", $items) : null;
    }
}

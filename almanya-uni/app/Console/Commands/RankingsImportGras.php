<?php

namespace App\Console\Commands;

use App\Models\FieldOfStudy;
use App\Models\University;
use App\Models\UniversitySubjectRank;
use Illuminate\Console\Command;

/**
 * ShanghaiRanking GRAS konu sıralamalarını university_subject_ranks tablosuna aktarır.
 *
 * NEDEN: Alan sıralamalarımızın kalite bileşeni üniversitenin GENEL dünya sırası. Bu,
 * mühendislik kurumlarını olduğundan zayıf gösteriyor — TU Darmstadt genel sırada #246,
 * ama GRAS Mechanical Engineering'de 76-100 bandında. Konu bazlı veri bu farkı kapatır.
 *
 * VERİ NEREDEN: `resources/data/gras-2025-de.json`, GRAS'ın herkese açık Nuxt statik
 * payload'larından yerelde toplandı (yalnızca Almanya satırları, 8 mühendislik konusu).
 * Prod PHP-only paylaşımlı barındırma olduğu ve payload'lar JS olduğu için çalışma
 * zamanında çekilmiyor; veri dosya olarak taşınıyor. Her satırda kaynak URL saklanıyor
 * ki sitede atıf verilebilsin.
 *
 * KURUM EŞLEŞTİRME: `resources/data/gras-mapping.json` içinde ELLE kuruldu. Bulanık
 * eşleştirme denendi ve tehlikeli çıktı ("Technical University of Munich" için ilk
 * adaylar güzel sanatlar akademileriydi), bu yüzden otomatik eşleştirme YOK — eşlemede
 * olmayan kurum sessizce atlanmaz, raporlanır.
 */
class RankingsImportGras extends Command
{
    protected $signature = 'rankings:import-gras
        {--execute : Uygula (varsayılan dry-run)}';

    protected $description = 'GRAS konu sıralamalarını (Almanya) university_subject_ranks tablosuna aktarır.';

    public function handle(): int
    {
        $dry = !$this->option('execute');
        $this->info($dry ? '🔍 DRY-RUN' : '▶ EXECUTE');

        $rows = $this->readJson(resource_path('data/gras-2025-de.json'));
        $map = $this->readJson(resource_path('data/gras-mapping.json'));
        if ($rows === null || $map === null) {
            return self::FAILURE;
        }

        // slug → id çözümlemeleri tek seferde
        $uniIds = University::whereIn('slug', array_values($map['universities']))
            ->pluck('id', 'slug')->all();
        $fieldIds = FieldOfStudy::whereIn('slug', array_column($map['subjects'], 'field'))
            ->pluck('id', 'slug')->all();

        $prepared = [];
        $skipped = [];

        foreach ($rows as $r) {
            $slug = $map['universities'][$r['univ_name_en']] ?? null;
            $subject = $map['subjects'][$r['subject_code']] ?? null;

            if (!$slug || !$subject) {
                $skipped[] = "eşlemede yok: {$r['univ_name_en']} / {$r['subject_code']}";
                continue;
            }
            if (!isset($uniIds[$slug])) {
                $skipped[] = "üniversite kaydı yok: {$slug} ({$r['univ_name_en']})";
                continue;
            }
            if (!isset($fieldIds[$subject['field']])) {
                $skipped[] = "alan kaydı yok: {$subject['field']}";
                continue;
            }

            [$low, $high] = $this->parseRank($r['ranking']);
            if ($low === null) {
                $skipped[] = "sıra okunamadı: {$r['ranking']} ({$r['univ_name_en']})";
                continue;
            }

            $prepared[] = [
                'university_id' => $uniIds[$slug],
                'field_of_study_id' => $fieldIds[$subject['field']],
                'source' => UniversitySubjectRank::SOURCE_GRAS,
                'source_subject' => $subject['name'],
                'scope' => null,
                'rank_low' => $low,
                'rank_high' => $high,
                'tier' => null,
                'year' => (int) $r['year'],
                'source_url' => $r['source_url'],
            ];
        }

        $this->line('Okunan satır:      ' . count($rows));
        $this->line('Aktarılacak satır: ' . count($prepared));
        $this->line('Atlanan satır:     ' . count($skipped));
        foreach (array_slice($skipped, 0, 20) as $s) {
            $this->warn('  · ' . $s);
        }

        if ($prepared) {
            $this->newLine();
            $this->line('Örnek (ilk 5):');
            foreach (array_slice($prepared, 0, 5) as $p) {
                $this->line(sprintf(
                    '  uni#%-4d alan#%-3d %-38s %d-%d',
                    $p['university_id'], $p['field_of_study_id'],
                    mb_substr($p['source_subject'], 0, 36), $p['rank_low'], $p['rank_high']
                ));
            }
        }

        if ($dry) {
            $this->newLine();
            $this->warn('DRY-RUN — değişiklik YOK. --execute ile uygula.');

            return self::SUCCESS;
        }

        $now = now();
        foreach ($prepared as $p) {
            // Doğal anahtar migration'daki unique ile aynı → yeniden çalıştırmak güvenli.
            UniversitySubjectRank::updateOrCreate(
                [
                    'university_id' => $p['university_id'],
                    'field_of_study_id' => $p['field_of_study_id'],
                    'source' => $p['source'],
                    'source_subject' => $p['source_subject'],
                    'year' => $p['year'],
                ],
                $p + ['updated_at' => $now]
            );
        }

        $this->newLine();
        $this->info('═══ SONUÇ ═══');
        $this->line('Tablodaki toplam satır: ' . UniversitySubjectRank::count());
        $this->line('GRAS satırı:            ' . UniversitySubjectRank::where('source', 'gras')->count());

        return self::SUCCESS;
    }

    /** "51-75" → [51, 75] · "1" → [1, 1] · okunamayan → [null, null] */
    private function parseRank(string $raw): array
    {
        $raw = trim($raw);

        if (preg_match('/^(\d+)\s*-\s*(\d+)$/', $raw, $m)) {
            return [(int) $m[1], (int) $m[2]];
        }
        if (preg_match('/^(\d+)$/', $raw, $m)) {
            return [(int) $m[1], (int) $m[1]];
        }

        return [null, null];
    }

    private function readJson(string $path): ?array
    {
        if (!is_file($path)) {
            $this->error('Dosya yok: ' . $path);

            return null;
        }
        $data = json_decode((string) file_get_contents($path), true);
        if (!is_array($data)) {
            $this->error('JSON okunamadı: ' . $path);

            return null;
        }

        return $data;
    }
}

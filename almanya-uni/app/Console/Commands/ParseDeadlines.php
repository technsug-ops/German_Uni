<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Services\DeadlineParser;
use Illuminate\Console\Command;

class ParseDeadlines extends Command
{
    protected $signature = 'programs:parse-deadlines
        {--source= : Sadece bu source\'taki programlar (örn: daad)}
        {--dry-run : Sadece raporla}
        {--overwrite : Mevcut deadline\'ları üzerine yaz}
        {--sample=10 : Dry-run\'da örnek göster}';

    protected $description = 'admission_summary freeform metninden application_deadline_summer/winter çıkarır.';

    public function handle(DeadlineParser $parser): int
    {
        $query = Program::query()->whereNotNull('admission_summary')->where('is_active', true);
        if ($source = $this->option('source')) {
            $query->where('source', $source);
        }
        if (!$this->option('overwrite')) {
            $query->where(function ($q) {
                $q->whereNull('application_deadline_winter')->orWhereNull('application_deadline_summer');
            });
        }

        $total = (clone $query)->count();
        $this->info(sprintf(
            '%s — %d program incelenecek (source=%s, overwrite=%s)',
            $this->option('dry-run') ? '🔍 DRY-RUN' : '▶ EXECUTE',
            $total,
            $source ?? 'all',
            $this->option('overwrite') ? 'YES' : 'NO',
        ));

        $stats = ['high' => 0, 'medium' => 0, 'low' => 0, 'updated_winter' => 0, 'updated_summer' => 0];
        $samples = [];
        $sampleLimit = (int) $this->option('sample');

        foreach ($query->cursor() as $p) {
            $r = $parser->parse($p->admission_summary);
            $stats[$r['confidence']]++;

            if ($r['winter'] || $r['summer']) {
                if (count($samples) < $sampleLimit) {
                    $samples[] = [
                        'id' => $p->id,
                        'preview' => substr($p->admission_summary, 0, 100) . '...',
                        'winter' => $r['winter'] ?? '-',
                        'summer' => $r['summer'] ?? '-',
                        'confidence' => $r['confidence'],
                    ];
                }

                if (!$this->option('dry-run')) {
                    $updates = [];
                    if ($r['winter']) {
                        $updates['application_deadline_winter'] = $r['winter'];
                        $stats['updated_winter']++;
                    }
                    if ($r['summer']) {
                        $updates['application_deadline_summer'] = $r['summer'];
                        $stats['updated_summer']++;
                    }
                    if ($updates) {
                        Program::where('id', $p->id)->update($updates);
                    }
                }
            }
        }

        $this->newLine();
        $this->info('═══ ÖZET ═══');
        $this->line(sprintf('  high confidence (her ikisi):     %d', $stats['high']));
        $this->line(sprintf('  medium confidence (tek tarih):   %d', $stats['medium']));
        $this->line(sprintf('  low confidence (tarih yok):      %d', $stats['low']));
        $this->newLine();
        $this->line(sprintf('  Winter doldurulan: %d', $stats['updated_winter']));
        $this->line(sprintf('  Summer doldurulan: %d', $stats['updated_summer']));

        if ($samples) {
            $this->newLine();
            $this->info('═══ ÖRNEKLER ═══');
            $this->table(
                ['ID', 'Conf', 'Winter', 'Summer', 'Preview'],
                array_map(fn ($s) => [$s['id'], $s['confidence'], $s['winter'], $s['summer'], $s['preview']], $samples)
            );
        }

        return self::SUCCESS;
    }
}

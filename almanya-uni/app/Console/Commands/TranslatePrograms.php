<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Services\GeminiTranslator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('translate:programs
    {--limit=0 : Maks kaç program çevrilsin (0 = sınırsız)}
    {--dry-run : API çağrısı yap ama DB\'ye yazma (maliyet test\'i için)}
    {--delay=200 : İstek aralığı (ms) — Free tier 1500 req/gün limit\'ini aşmamak için 200ms+ önerilir}
    {--from-id=0 : Belirli ID\'den başla (resume için)}
    {--yes : Maliyet onayını atla (background için)}
')]
#[Description('description_en dolu ama description_tr boş olan programları Gemini API ile EN→TR çevirir.')]
class TranslatePrograms extends Command
{
    public function handle(GeminiTranslator $translator): int
    {
        if (! $translator->isConfigured()) {
            $this->error('GEMINI_API_KEY .env\'de tanımlı değil.');
            $this->line('  Key al: https://aistudio.google.com/apikey');
            $this->line('  .env\'e ekle: GEMINI_API_KEY=AIza...');
            $this->line('  Sonra: php artisan config:clear');
            return self::FAILURE;
        }

        $limitOpt = (int) $this->option('limit');
        $dryRun   = (bool) $this->option('dry-run');
        $delay    = max(0, (int) $this->option('delay'));
        $fromId   = (int) $this->option('from-id');

        $query = Program::query()
            ->whereNotNull('description_en')
            ->whereNull('description_tr')
            ->where('is_active', true)
            ->orderBy('id');

        if ($fromId > 0) {
            $query->where('id', '>=', $fromId);
        }

        $totalCandidates = (clone $query)->count();
        $total = $limitOpt > 0 ? min($limitOpt, $totalCandidates) : $totalCandidates;

        if ($total === 0) {
            $this->info('Çevrilecek program yok.');
            return self::SUCCESS;
        }

        $charSum = (clone $query)
            ->when($limitOpt > 0, fn ($q) => $q->limit($limitOpt))
            ->selectRaw('SUM(CHAR_LENGTH(description_en)) as t')
            ->value('t') ?? 0;

        // Gemini 2.5 Flash fiyatları: $0.30/M in + $2.50/M out
        // Token tahmini: 1 token ≈ 4 char (EN için)
        $estInputTokens  = (int) ceil($charSum / 4);
        $estOutputTokens = (int) ceil($estInputTokens * 1.1);
        $estCostUsd = ($estInputTokens / 1_000_000 * 0.30) + ($estOutputTokens / 1_000_000 * 2.50);

        $this->info(sprintf(
            '%d program çevrilecek (~%s karakter, ~%s+%s tokens, ~$%.2f).',
            $total,
            number_format($charSum, 0, ',', '.'),
            number_format($estInputTokens, 0, ',', '.'),
            number_format($estOutputTokens, 0, ',', '.'),
            $estCostUsd
        ));
        $this->info('Mod: ' . ($dryRun ? 'DRY-RUN' : 'LIVE') . ' · gecikme: ' . $delay . 'ms · model: ' . config('services.gemini.model'));

        if (! $dryRun && $estCostUsd > 3.0 && ! $this->option('yes')) {
            if (! $this->confirm("Tahmini maliyet ~\${$estCostUsd} — devam edeyim mi?", false)) {
                return self::FAILURE;
            }
        }

        if ($limitOpt > 0) {
            $query->limit($limitOpt);
        }

        $programs = $query->select(['id', 'description_en'])->get();

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:6s% — %message%');
        $bar->setMessage('başlıyor...');
        $bar->start();

        $stats = [
            'translated'    => 0,
            'empty'         => 0,
            'errors'        => 0,
            'input_tokens'  => 0,
            'output_tokens' => 0,
        ];

        $run = function () use ($programs, $translator, $dryRun, $delay, $bar, &$stats) {
            foreach ($programs as $program) {
                $bar->setMessage('#' . $program->id);

                try {
                    $result = $translator->translate($program->description_en);

                    if ($result && ! empty($result['translation'])) {
                        if (! $dryRun) {
                            Program::where('id', $program->id)
                                ->update(['description_tr' => $result['translation']]);
                        }
                        $stats['translated']++;
                        $stats['input_tokens']  += $result['input_tokens'];
                        $stats['output_tokens'] += $result['output_tokens'];
                    } else {
                        $stats['empty']++;
                    }
                } catch (\Throwable $e) {
                    $stats['errors']++;
                    $this->newLine();
                    $this->warn("#{$program->id} fail: " . mb_substr($e->getMessage(), 0, 180));
                }

                $bar->advance();

                if ($delay > 0) {
                    usleep($delay * 1000);
                }
            }
        };

        if (method_exists(Program::class, 'withoutSyncingToSearch')) {
            Program::withoutSyncingToSearch($run);
        } else {
            $run();
        }

        $bar->finish();
        $this->newLine(2);

        $costUsd = ($stats['input_tokens'] / 1_000_000 * 0.30) + ($stats['output_tokens'] / 1_000_000 * 2.50);

        $this->table(
            ['Translated', 'Empty', 'Errors', 'Input tokens', 'Output tokens', 'Cost (USD)'],
            [[
                $stats['translated'],
                $stats['empty'],
                $stats['errors'],
                number_format($stats['input_tokens'], 0, ',', '.'),
                number_format($stats['output_tokens'], 0, ',', '.'),
                '$' . number_format($costUsd, 4, '.', ''),
            ]]
        );

        if ($dryRun) {
            $this->warn('DRY-RUN — DB değişmedi.');
        }

        return self::SUCCESS;
    }
}

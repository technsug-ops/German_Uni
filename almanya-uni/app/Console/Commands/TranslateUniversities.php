<?php

namespace App\Console\Commands;

use App\Models\University;
use App\Services\GeminiTranslator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('translate:universities
    {--limit=0 : Maks kaç üni çevrilsin (0 = sınırsız)}
    {--dry-run : API çağrısı yap ama DB\'ye yazma}
    {--delay=300 : İstek aralığı (ms)}
    {--from-id=0 : Belirli ID\'den başla (resume)}
    {--source=auto : auto (de varsa de, yoksa en) | de | en}
')]
#[Description('description_de veya description_en dolu, description_tr boş olan üniversiteleri Gemini ile TR\'ye çevirir.')]
class TranslateUniversities extends Command
{
    public function handle(GeminiTranslator $translator): int
    {
        if (! $translator->isConfigured()) {
            $this->error('GEMINI_API_KEY .env\'de yok.');
            $this->line('  Al: https://aistudio.google.com/apikey');
            $this->line('  Ekle: GEMINI_API_KEY=AIza...');
            $this->line('  Sonra: php artisan config:clear');
            return self::FAILURE;
        }

        $limitOpt = (int) $this->option('limit');
        $dryRun   = (bool) $this->option('dry-run');
        $delay    = max(0, (int) $this->option('delay'));
        $fromId   = (int) $this->option('from-id');
        $source   = $this->option('source');

        $query = University::query()
            ->whereNull('description_tr')
            ->where('is_active', true)
            ->where(function ($q) use ($source) {
                if ($source === 'de') {
                    $q->whereNotNull('description_de');
                } elseif ($source === 'en') {
                    $q->whereNotNull('description_en');
                } else {
                    $q->whereNotNull('description_de')->orWhereNotNull('description_en');
                }
            })
            ->orderBy('id');

        if ($fromId > 0) {
            $query->where('id', '>=', $fromId);
        }

        $totalCandidates = (clone $query)->count();
        $total = $limitOpt > 0 ? min($limitOpt, $totalCandidates) : $totalCandidates;

        if ($total === 0) {
            $this->info('Çevrilecek üni yok.');
            return self::SUCCESS;
        }

        // Kaba char tahmini (de varsa onu, yoksa en)
        $charSum = (clone $query)
            ->when($limitOpt > 0, fn ($q) => $q->limit($limitOpt))
            ->selectRaw('SUM(GREATEST(CHAR_LENGTH(COALESCE(description_de, "")), CHAR_LENGTH(COALESCE(description_en, "")))) AS t')
            ->value('t') ?? 0;

        $estInputTokens  = (int) ceil($charSum / 4);
        $estOutputTokens = (int) ceil($estInputTokens * 1.2);
        $estCostUsd = ($estInputTokens / 1_000_000 * 0.30) + ($estOutputTokens / 1_000_000 * 2.50);

        $this->info(sprintf(
            '%d üniversite çevrilecek (~%s karakter, ~%s+%s tokens, ~$%.3f). Kaynak: %s',
            $total,
            number_format($charSum, 0, ',', '.'),
            number_format($estInputTokens, 0, ',', '.'),
            number_format($estOutputTokens, 0, ',', '.'),
            $estCostUsd,
            $source
        ));
        $this->info('Mod: ' . ($dryRun ? 'DRY-RUN' : 'LIVE') . ' · gecikme: ' . $delay . 'ms · model: ' . config('services.gemini.model'));

        if (! $dryRun && $estCostUsd > 1.0) {
            if (! $this->confirm("Devam edeyim mi?", false)) {
                return self::FAILURE;
            }
        }

        if ($limitOpt > 0) {
            $query->limit($limitOpt);
        }

        $unis = $query->select(['id', 'name_de', 'description_de', 'description_en'])->get();

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

        University::withoutSyncingToSearch(function () use ($unis, $translator, $dryRun, $delay, $source, $bar, &$stats) {
            foreach ($unis as $uni) {
                $bar->setMessage('#' . $uni->id . ' ' . mb_substr($uni->name_de, 0, 30));

                // Kaynak metni seç
                $text = match ($source) {
                    'de'    => $uni->description_de,
                    'en'    => $uni->description_en,
                    default => $uni->description_de ?: $uni->description_en,
                };

                if (! $text) {
                    $stats['empty']++;
                    $bar->advance();
                    continue;
                }

                try {
                    $result = $translator->translate($text, GeminiTranslator::SYSTEM_PROMPT_UNIVERSITY);

                    if ($result && ! empty($result['translation'])) {
                        if (! $dryRun) {
                            University::where('id', $uni->id)
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
                    $this->warn("#{$uni->id} fail: " . mb_substr($e->getMessage(), 0, 180));
                }

                $bar->advance();

                if ($delay > 0) {
                    usleep($delay * 1000);
                }
            }
        });

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

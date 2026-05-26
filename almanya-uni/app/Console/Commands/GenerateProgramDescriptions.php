<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Services\GeminiTranslator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('programs:generate-descriptions
    {--limit=0 : Maks kaç program (0 = sınırsız)}
    {--dry-run : API çağrısı yap ama DB\'ye yazma}
    {--delay=200 : İstek aralığı (ms)}
    {--from-id=0 : Belirli ID\'den başla (resume)}
    {--yes : Maliyet onayını atla (background için)}
')]
#[Description('Hiç açıklaması olmayan (description_tr + description_en boş) programlar için metadata\'dan Gemini ile TR açıklama ÜRETİR (çeviri değil).')]
class GenerateProgramDescriptions extends Command
{
    // Üretim promptu: SADECE verilen olgusal alanlardan yaz, uydurma yapma.
    private const SYSTEM_PROMPT = <<<'TXT'
You write short, factual Turkish overview descriptions for German university study programs, for AlmanyaUni — a guide for Turkish students who want to study in Germany. You are NOT translating; you are composing a brief overview from the structured facts given.

Rules:
- Output ONLY the Turkish description. No preamble, no headings, no quotation marks.
- Length: 2 short paragraphs (about 60-110 words total).
- Use ONLY the facts provided in the input. NEVER invent specific numbers, fees, deadlines, rankings, accreditations, or admission requirements that are not given.
- Describe what the field/program is generally about (the subject area) and who it suits, in natural fluent Turkish for a 17-22 year old Turkish student.
- Keep German terms in German: Bachelor, Master, Hochschule, Fachhochschule, Universität, Numerus Clausus (NC), Studienkolleg. Keep the university and city names original.
- Do not promise outcomes ("garantili iş" vb.) and avoid marketing fluff.
- If the program language is given, mention it naturally (ör. "Almanca yürütülür" / "İngilizce yürütülür").
TXT;

    public function handle(GeminiTranslator $gemini): int
    {
        if (! $gemini->isConfigured()) {
            $this->error('GEMINI_API_KEY .env\'de yok.');
            return self::FAILURE;
        }

        $limitOpt = (int) $this->option('limit');
        $dryRun   = (bool) $this->option('dry-run');
        $delay    = max(0, (int) $this->option('delay'));
        $fromId   = (int) $this->option('from-id');

        $query = Program::query()
            ->whereNull('description_tr')
            ->whereNull('description_en')
            ->where('is_active', true)
            ->with(['field:id,name_tr', 'university:id,name_de,city_id', 'university.city:id,name_de'])
            ->orderBy('id');

        if ($fromId > 0) {
            $query->where('id', '>=', $fromId);
        }

        $totalCandidates = (clone $query)->count();
        $total = $limitOpt > 0 ? min($limitOpt, $totalCandidates) : $totalCandidates;

        if ($total === 0) {
            $this->info('Üretilecek program yok.');
            return self::SUCCESS;
        }

        // Üretimde output ağırlıklı: ~110 kelime ≈ 220 token çıktı, input ~150 token
        $estInputTokens  = $total * 200;
        $estOutputTokens = $total * 240;
        $estCostUsd = ($estInputTokens / 1_000_000 * 0.30) + ($estOutputTokens / 1_000_000 * 2.50);

        $this->info(sprintf(
            '%d program için açıklama üretilecek (~%s+%s tokens, ~$%.2f). Model: %s',
            $total,
            number_format($estInputTokens, 0, ',', '.'),
            number_format($estOutputTokens, 0, ',', '.'),
            $estCostUsd,
            config('services.gemini.model')
        ));
        $this->info('Mod: ' . ($dryRun ? 'DRY-RUN' : 'LIVE') . ' · gecikme: ' . $delay . 'ms');

        if (! $dryRun && $estCostUsd > 3.0 && ! $this->option('yes')) {
            if (! $this->confirm("Tahmini ~\${$estCostUsd} — devam edeyim mi?", false)) {
                return self::FAILURE;
            }
        }

        if ($limitOpt > 0) {
            $query->limit($limitOpt);
        }

        $programs = $query->get();

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:6s% — %message%');
        $bar->start();

        $stats = ['generated' => 0, 'skipped' => 0, 'errors' => 0, 'input_tokens' => 0, 'output_tokens' => 0];

        $run = function () use ($programs, $gemini, $dryRun, $delay, $bar, &$stats) {
            foreach ($programs as $p) {
                $bar->setMessage('#' . $p->id);

                $facts = $this->buildFacts($p);
                if ($facts === null) {
                    $stats['skipped']++;
                    $bar->advance();
                    continue;
                }

                try {
                    $result = $gemini->translate($facts, self::SYSTEM_PROMPT);

                    if ($result && ! empty($result['translation'])) {
                        if (! $dryRun) {
                            Program::where('id', $p->id)
                                ->update(['description_tr' => $result['translation']]);
                        }
                        $stats['generated']++;
                        $stats['input_tokens']  += $result['input_tokens'];
                        $stats['output_tokens'] += $result['output_tokens'];
                    } else {
                        $stats['skipped']++;
                    }
                } catch (\Throwable $e) {
                    $stats['errors']++;
                    $this->newLine();
                    $this->warn("#{$p->id} fail: " . mb_substr($e->getMessage(), 0, 160));
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
            ['Generated', 'Skipped', 'Errors', 'Input tokens', 'Output tokens', 'Cost (USD)'],
            [[
                $stats['generated'],
                $stats['skipped'],
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

    /**
     * Programın yapılandırılmış olgularını promptluk metne çevirir.
     * İsim/alan hiç yoksa null döner (üretecek bir şey yok).
     */
    private function buildFacts(Program $p): ?string
    {
        $name = $p->name_de ?: $p->name_en;
        $field = $p->field?->name_tr;

        if (! $name && ! $field) {
            return null;
        }

        $lines = [];
        $lines[] = 'Program: ' . ($name ?: '(belirtilmemiş)');
        if ($field) {
            $lines[] = 'Alan: ' . $field;
        }
        if ($p->degree) {
            $lines[] = 'Derece: ' . trim($p->degree . ' ' . ($p->degree_specification ?? ''));
        }
        if ($p->university?->name_de) {
            $lines[] = 'Üniversite: ' . $p->university->name_de;
        }
        if ($p->university?->city?->name_de) {
            $lines[] = 'Şehir: ' . $p->university->city->name_de;
        }
        if ($p->language) {
            $lines[] = 'Eğitim dili: ' . $p->language;
        }
        if ($p->duration_semesters) {
            $lines[] = 'Süre: ' . $p->duration_semesters . ' yarıyıl';
        }
        if ($p->study_form) {
            $lines[] = 'Öğrenim şekli: ' . $p->study_form;
        }

        return implode("\n", $lines);
    }
}

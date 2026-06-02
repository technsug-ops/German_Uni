<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Models\University;
use App\Services\Content\ContentVoice;
use App\Services\GeminiTranslator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * TR kaynak prose açıklamalarını eksik dillere (EN/DE) NATIVE çevirir.
 *
 * Sızıntı yasağı (i18n konsolidasyon #3) sonrası: description_tr var ama
 * description_en/_de boş olan kayıtlar EN/DE sayfada GİZLENİYOR. Bu komut o
 * boşluğu doldurur → içerik gizlenmek yerine hedef dilde görünür.
 *
 * Register/ton tek kaynaktan: ContentVoice (DE "du", EN "you", anti-AI-slop).
 * Altyapı: GeminiTranslator (retry + safety + token/maliyet sayımı).
 */
#[Signature('i18n:fill-prose
    {--type=all : program | university | all}
    {--limit=0 : Maks kaç kayıt (0 = sınırsız)}
    {--dry-run : API çağrısı yap ama DB\'ye yazma}
    {--delay=300 : İstek aralığı (ms)}
    {--from-id=0 : Belirli ID\'den başla (resume)}
')]
#[Description('description_tr dolu ama EN/DE boş olan program/üni açıklamalarını native (ContentVoice) olarak çevirir — strict fallback ile gizlenen içeriği görünür kılar.')]
class I18nFillProse extends Command
{
    public function handle(GeminiTranslator $translator): int
    {
        if (! $translator->isConfigured()) {
            $this->error('GEMINI_API_KEY .env\'de yok. (config:clear sonrası tekrar dene)');
            return self::FAILURE;
        }

        $type    = $this->option('type');
        $limit   = (int) $this->option('limit');
        $dryRun  = (bool) $this->option('dry-run');
        $delay   = max(0, (int) $this->option('delay'));
        $fromId  = (int) $this->option('from-id');

        // İş listesi: her biri [model, id, source_tr, target_lang, column]
        $jobs = [];

        if (in_array($type, ['program', 'all'], true)) {
            // Program: sadece description_en kolonu var (de yok → DE sayfa en'e düşer).
            $q = Program::query()
                ->whereNotNull('description_tr')->where('description_tr', '!=', '')
                ->where(fn ($x) => $x->whereNull('description_en')->orWhere('description_en', ''))
                ->when($fromId > 0, fn ($x) => $x->where('id', '>=', $fromId))
                ->orderBy('id')
                ->select(['id', 'description_tr']);
            foreach ($q->cursor() as $p) {
                $jobs[] = ['model' => Program::class, 'id' => $p->id, 'src' => $p->description_tr, 'lang' => 'en', 'col' => 'description_en'];
            }
        }

        if (in_array($type, ['university', 'all'], true)) {
            $q = University::query()
                ->whereNotNull('description_tr')->where('description_tr', '!=', '')
                ->where(function ($x) {
                    $x->whereNull('description_en')->orWhere('description_en', '')
                      ->orWhereNull('description_de')->orWhere('description_de', '');
                })
                ->when($fromId > 0, fn ($x) => $x->where('id', '>=', $fromId))
                ->orderBy('id')
                ->select(['id', 'description_tr', 'description_en', 'description_de']);
            foreach ($q->cursor() as $u) {
                if (empty($u->description_en)) {
                    $jobs[] = ['model' => University::class, 'id' => $u->id, 'src' => $u->description_tr, 'lang' => 'en', 'col' => 'description_en'];
                }
                if (empty($u->description_de)) {
                    $jobs[] = ['model' => University::class, 'id' => $u->id, 'src' => $u->description_tr, 'lang' => 'de', 'col' => 'description_de'];
                }
            }
        }

        if ($limit > 0) {
            $jobs = array_slice($jobs, 0, $limit);
        }

        $total = count($jobs);
        if ($total === 0) {
            $this->info('Doldurulacak boşluk yok — tüm prose senkron. ✅');
            return self::SUCCESS;
        }

        // Maliyet tahmini
        $charSum = array_sum(array_map(fn ($j) => mb_strlen($j['src']), $jobs));
        $estIn   = (int) ceil($charSum / 4) + ($total * 300);   // +prompt overhead
        $estOut  = (int) ceil($estIn * 1.2);
        $estCost = ($estIn / 1_000_000 * 0.30) + ($estOut / 1_000_000 * 2.50);

        $byLang = [];
        foreach ($jobs as $j) { $byLang[$j['lang']] = ($byLang[$j['lang']] ?? 0) + 1; }
        $langStr = implode(', ', array_map(fn ($l, $n) => "$l:$n", array_keys($byLang), $byLang));

        $this->info(sprintf(
            '%d çeviri (%s) · ~%s karakter · ~$%.3f · model: %s',
            $total, $langStr, number_format($charSum, 0, ',', '.'), $estCost, config('services.gemini.model')
        ));
        $this->line('Mod: ' . ($dryRun ? 'DRY-RUN (DB değişmez)' : 'LIVE') . ' · gecikme: ' . $delay . 'ms');

        if (! $dryRun && $estCost > 0.50 && ! $this->confirm('Devam edeyim mi?', true)) {
            return self::FAILURE;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:6s% — %message%');
        $bar->start();

        $stats = ['done' => 0, 'empty' => 0, 'errors' => 0, 'in' => 0, 'out' => 0];

        foreach ($jobs as $j) {
            $bar->setMessage(class_basename($j['model']) . " #{$j['id']} → {$j['lang']}");
            try {
                $result = $translator->translate($j['src'], $this->systemPrompt($j['lang']));
                if ($result && ! empty($result['translation'])) {
                    if (! $dryRun) {
                        $this->writeBack($j['model'], $j['id'], $j['col'], $result['translation']);
                    }
                    $stats['done']++;
                    $stats['in']  += $result['input_tokens'];
                    $stats['out'] += $result['output_tokens'];
                } else {
                    $stats['empty']++;
                }
            } catch (\Throwable $e) {
                $stats['errors']++;
                $this->newLine();
                $this->warn(class_basename($j['model']) . " #{$j['id']} → {$j['lang']} fail: " . mb_substr($e->getMessage(), 0, 160));
            }
            $bar->advance();
            if ($delay > 0) {
                usleep($delay * 1000);
            }
        }

        $bar->finish();
        $this->newLine(2);

        $cost = ($stats['in'] / 1_000_000 * 0.30) + ($stats['out'] / 1_000_000 * 2.50);
        $this->table(
            ['Done', 'Empty', 'Errors', 'Input tok', 'Output tok', 'Cost (USD)'],
            [[$stats['done'], $stats['empty'], $stats['errors'],
              number_format($stats['in'], 0, ',', '.'), number_format($stats['out'], 0, ',', '.'),
              '$' . number_format($cost, 4, '.', '')]]
        );

        if ($dryRun) {
            $this->warn('DRY-RUN — DB değişmedi.');
        } else {
            $this->info('Bitti. `php artisan i18n:health` ile doğrula, prod\'a `i18n:export-content` + import gerekebilir.');
        }

        return self::SUCCESS;
    }

    /** TR → hedef dil, native register (ContentVoice) + prose koruma kuralları. */
    private function systemPrompt(string $target): string
    {
        $targetName = ContentVoice::languageName($target);
        $voice = ContentVoice::for($target);

        return <<<TXT
You translate AND LOCALIZE short German-university / study-program descriptions from Turkish into {$targetName} for AlmanyaUni, a guide for international students applying to German universities. This is NOT a literal translation — render it natively per the VOICE rules.

TARGET-LANGUAGE VOICE & REGISTER (follow strictly):
{$voice}

RULES:
- Output ONLY the {$targetName} text. No preamble, no explanations, no quotation marks, no markdown code fences.
- Keep German proper nouns and terms unchanged: university and city names, plus Hochschule, Fachhochschule, Universität, Bachelor, Master, Studienkolleg, Numerus Clausus (NC), Abitur, ECTS, BAföG, Sperrkonto, Uni-Assist, TestDaF, DSH, Bundesland names. Gloss a German term once in parentheses on first mention if helpful.
- Keep degree forms like "Bachelor of Science (B.Sc.)" / "Master of Science (M.Sc.)" intact.
- Preserve paragraph breaks and any list/bullet markers exactly.
- Do NOT add information, numbers, deadlines or claims not present in the source.
- If the source is empty, return empty.
TXT;
    }

    private function writeBack(string $model, int $id, string $col, string $value): void
    {
        if ($model === University::class) {
            // Scout: query-builder update Scout'u tetiklemez; yine de güvenli sarmal.
            University::withoutSyncingToSearch(fn () => University::where('id', $id)->update([$col => $value]));
        } else {
            Program::where('id', $id)->update([$col => $value]);
        }
    }
}

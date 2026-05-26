<?php

namespace App\Console\Commands;

use App\Models\University;
use App\Services\GeminiTranslator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('universities:generate-descriptions
    {--limit=0 : Maks kaç üni (0 = sınırsız)}
    {--dry-run : API çağrısı yap ama DB\'ye yazma}
    {--delay=300 : İstek aralığı (ms)}
    {--from-id=0 : Belirli ID\'den başla (resume)}
    {--yes : Maliyet onayını atla}
')]
#[Description('Hiç açıklaması olmayan (description_tr + description_de + description_en boş) üniversiteler için metadata\'dan Gemini ile TR açıklama ÜRETİR (çeviri değil).')]
class GenerateUniversityDescriptions extends Command
{
    private const SYSTEM_PROMPT = <<<'TXT'
You write short, factual Turkish overview descriptions for German higher education institutions, for AlmanyaUni — a guide for Turkish students who want to study in Germany. You are NOT translating; you compose a brief overview from the structured facts given.

Rules:
- Output ONLY the Turkish description. No preamble, no headings, no quotation marks.
- Length: 2 short paragraphs (about 60-110 words total).
- Use ONLY the facts provided. NEVER invent founding years, student numbers, rankings, accreditations, tuition, or program lists that are not given.
- Describe the institution type and general focus in natural fluent Turkish for a 17-22 year old Turkish student.
- Keep German/English terms and the institution & city names original. Use German for institution type words where natural (Hochschule, Fachhochschule, Universität).
- Avoid marketing fluff and outcome promises.
- If it is a private institution (type=private), you may note it is a "özel (private) yükseköğretim kurumu".
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

        $query = University::query()
            ->whereNull('description_tr')
            ->whereNull('description_de')
            ->whereNull('description_en')
            ->where('is_active', true)
            ->with(['city:id,name_de'])
            ->orderBy('id');

        if ($fromId > 0) {
            $query->where('id', '>=', $fromId);
        }

        $total = (clone $query)->count();
        $total = $limitOpt > 0 ? min($limitOpt, $total) : $total;

        if ($total === 0) {
            $this->info('Üretilecek üni yok.');
            return self::SUCCESS;
        }

        $estCostUsd = $total * 0.0005;
        $this->info(sprintf('%d üni için açıklama üretilecek (~$%.3f). Model: %s · Mod: %s',
            $total, $estCostUsd, config('services.gemini.model'), $dryRun ? 'DRY-RUN' : 'LIVE'));

        if ($limitOpt > 0) {
            $query->limit($limitOpt);
        }

        $unis = $query->get();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $stats = ['generated' => 0, 'skipped' => 0, 'errors' => 0];

        University::withoutSyncingToSearch(function () use ($unis, $gemini, $dryRun, $delay, $bar, &$stats) {
            foreach ($unis as $u) {
                $facts = $this->buildFacts($u);

                try {
                    $result = $gemini->translate($facts, self::SYSTEM_PROMPT);
                    if ($result && ! empty($result['translation'])) {
                        if (! $dryRun) {
                            University::where('id', $u->id)->update(['description_tr' => $result['translation']]);
                        }
                        $stats['generated']++;
                    } else {
                        $stats['skipped']++;
                    }
                } catch (\Throwable $e) {
                    $stats['errors']++;
                    $this->newLine();
                    $this->warn("#{$u->id} fail: " . mb_substr($e->getMessage(), 0, 160));
                }

                $bar->advance();
                if ($delay > 0) {
                    usleep($delay * 1000);
                }
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->table(['Generated', 'Skipped', 'Errors'], [[$stats['generated'], $stats['skipped'], $stats['errors']]]);

        if ($dryRun) {
            $this->warn('DRY-RUN — DB değişmedi.');
        }

        return self::SUCCESS;
    }

    private function buildFacts(University $u): string
    {
        $typeLabel = match ($u->type) {
            'private'          => 'özel üniversite/yüksekokul (private)',
            'applied_sciences' => 'Fachhochschule (uygulamalı bilimler yüksekokulu)',
            'art'              => 'sanat/müzik yüksekokulu',
            'religion'         => 'dini/teolojik yüksekokul',
            'public'           => 'devlet üniversitesi',
            default            => 'yükseköğretim kurumu',
        };

        $lines = [];
        $lines[] = 'Kurum adı: ' . ($u->name_de ?: $u->name_en);
        $lines[] = 'Tür: ' . $typeLabel;
        if ($u->city?->name_de) {
            $lines[] = 'Şehir: ' . $u->city->name_de;
        }
        if ($u->founded_year) {
            $lines[] = 'Kuruluş yılı: ' . $u->founded_year;
        }
        if ($u->student_count) {
            $lines[] = 'Öğrenci sayısı: ' . $u->student_count;
        }

        return implode("\n", $lines);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Scholarship;
use App\Services\Content\ContentVoice;
use App\Services\GeminiTranslator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * DAAD burslarını TR'ye lokalize eder (kaynak-dili lokalizasyon kuralı).
 * DAAD sadece de/en veriyor → /tr'de İngilizce sızıyordu. Bu komut:
 *   - name → name_tr (orijinal korunur, accessor parantezde gösterir; akronim
 *     ise model orijinali döndürür → parantez gizlenir)
 *   - programmname → programmname_tr
 *   - introduction (en/de) → introduction_json['tr'] (native, ContentVoice)
 * İsim ÇEVRİLİR ama orijinal ASLA silinmez. İdempotent: name_tr doluysa atlar.
 */
#[Signature('scholarships:localize
    {--limit=0 : Maks kaç burs (0 = sınırsız)}
    {--dry-run : API çağır ama DB yazma}
    {--delay=250 : İstek aralığı (ms)}
    {--force : name_tr dolu olanları da yeniden çevir}
')]
#[Description('DAAD burslarının ad + açıklamasını native TR\'ye çevirir (orijinal korunur, /tr parantezde yerel ad + TR içerik).')]
class ScholarshipsLocalize extends Command
{
    public function handle(GeminiTranslator $translator): int
    {
        if (! $translator->isConfigured()) {
            $this->error('GEMINI_API_KEY .env\'de yok.');
            return self::FAILURE;
        }

        $limit  = (int) $this->option('limit');
        $dry    = (bool) $this->option('dry-run');
        $delay  = max(0, (int) $this->option('delay'));
        $force  = (bool) $this->option('force');

        $q = Scholarship::query()->whereNull('removed_at')
            ->when(! $force, fn ($x) => $x->where(fn ($w) => $w->whereNull('name_tr')->orWhere('name_tr', '')))
            ->orderBy('id');
        if ($limit > 0) $q->limit($limit);
        $rows = $q->get();

        $total = $rows->count();
        if ($total === 0) {
            $this->info('Lokalize edilecek burs yok — hepsi TR. ✅');
            return self::SUCCESS;
        }

        $this->info("{$total} burs TR'ye lokalize edilecek · model: " . config('services.gemini.model') . ($dry ? ' · DRY-RUN' : ''));

        $namePrompt = $this->namePrompt();
        $introPrompt = $this->introPrompt();

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->start();

        $stats = ['done' => 0, 'skip' => 0, 'err' => 0, 'in' => 0, 'out' => 0];

        Scholarship::withoutSyncingToSearch(function () use ($rows, $translator, $dry, $delay, $namePrompt, $introPrompt, $bar, &$stats) {
            foreach ($rows as $s) {
                $bar->setMessage('#' . $s->id . ' ' . mb_substr($s->name_en ?: $s->name_de ?: '', 0, 30));
                try {
                    $update = [];
                    $introJson = is_array($s->introduction_json) ? $s->introduction_json : [];

                    // 1) Ad (orijinal korunur, TR karşılık üretilir)
                    $srcName = $s->name_en ?: $s->name_de;
                    if ($srcName) {
                        $r = $translator->translate($srcName, $namePrompt);
                        if ($r && ! empty($r['translation'])) {
                            $update['name_tr'] = $r['translation'];
                            $stats['in'] += $r['input_tokens']; $stats['out'] += $r['output_tokens'];
                        }
                    }
                    // 2) Programmname (varsa)
                    $srcPg = $s->programmname_en ?: $s->programmname_de;
                    if ($srcPg) {
                        $r = $translator->translate($srcPg, $namePrompt);
                        if ($r && ! empty($r['translation'])) {
                            $update['programmname_tr'] = $r['translation'];
                            $stats['in'] += $r['input_tokens']; $stats['out'] += $r['output_tokens'];
                        }
                    }
                    // 3) Introduction prose → native TR
                    $srcIntro = $introJson['en'] ?? $introJson['de'] ?? null;
                    if (! empty($srcIntro)) {
                        $r = $translator->translate($srcIntro, $introPrompt);
                        if ($r && ! empty($r['translation'])) {
                            $introJson['tr'] = $r['translation'];
                            $update['introduction_json'] = $introJson;
                            $stats['in'] += $r['input_tokens']; $stats['out'] += $r['output_tokens'];
                        }
                    }

                    if ($update) {
                        if (! $dry) Scholarship::where('id', $s->id)->update($update);
                        $stats['done']++;
                    } else {
                        $stats['skip']++;
                    }
                } catch (\Throwable $e) {
                    $stats['err']++;
                    $this->newLine();
                    $this->warn("#{$s->id} fail: " . mb_substr($e->getMessage(), 0, 140));
                }
                $bar->advance();
                if ($delay > 0) usleep($delay * 1000);
            }
        });

        $bar->finish();
        $this->newLine(2);
        $cost = ($stats['in'] / 1_000_000 * 0.30) + ($stats['out'] / 1_000_000 * 2.50);
        $this->table(['Done', 'Skip', 'Err', 'In tok', 'Out tok', 'Cost USD'],
            [[$stats['done'], $stats['skip'], $stats['err'],
              number_format($stats['in'], 0, ',', '.'), number_format($stats['out'], 0, ',', '.'),
              '$' . number_format($cost, 4, '.', '')]]);
        if ($dry) $this->warn('DRY-RUN — DB değişmedi.');

        return self::SUCCESS;
    }

    /** Burs/program ADI çevirisi — orijinali değil TR karşılığını üretir; akronim/özel ad korunur. */
    private function namePrompt(): string
    {
        return <<<'TXT'
You translate a German/English scholarship or funding-programme NAME into Turkish for AlmanyaUni (study-in-Germany guide for Turkish students).

Rules:
- Output ONLY the Turkish name. No quotes, no explanation.
- Keep acronyms, programme codes and proper nouns UNCHANGED (e.g. DAAD, ALECOSTA, BECAL, ERASMUS, SPK, names of foundations/institutions). Translate ONLY the descriptive/common-noun parts ("Grant Programme" → "Burs Programı", "Government Scholarships Programme" → "Devlet Bursları Programı", "Bilateral Exchange of Academics" → "Akademisyenler İçin İkili Değişim").
- If the name is PURELY an acronym or proper noun with nothing translatable, return it UNCHANGED.
- Natural, concise Turkish. Keep German institution names in German.
TXT;
    }

    /** Açıklama (introduction) → native TR (ContentVoice register + anti-AI-slop). */
    private function introPrompt(): string
    {
        $voice = ContentVoice::for('tr');
        return <<<TXT
You translate AND localize a scholarship description from English/German into Turkish for AlmanyaUni. NOT a literal translation — native, fluent Turkish per the voice rules.

VOICE:
{$voice}

RULES:
- Output ONLY the Turkish text. No preamble, no quotes.
- Keep German/English proper nouns and institution names (DAAD, SPK, CONARE...) and acronyms unchanged; gloss once if helpful.
- Preserve meaning and any concrete facts (durations, degrees). Add nothing not in the source.
- If source is empty, return empty.
TXT;
    }
}

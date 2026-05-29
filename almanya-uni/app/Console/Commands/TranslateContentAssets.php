<?php

namespace App\Console\Commands;

use App\Models\ContentAsset;
use App\Services\Content\ContentTranslator;
use Illuminate\Console\Command;

class TranslateContentAssets extends Command
{
    protected $signature = 'content:translate-assets
        {--asset= : Translate a single asset by id}
        {--brief= : Translate every asset of this brief}
        {--lang=all : Target language: all, or one of tr,en,de,fr,es,it,pl,ru,ar,fa}
        {--source-lang=tr : Filter source language}
        {--force : Re-translate existing translations}
        {--sleep=2 : Seconds to wait between API calls (Gemini rate-limit)}
        {--dry-run : Show what would happen without calling Gemini}';

    protected $description = 'Translate ContentAssets into 10 supported languages via Gemini.';

    public function handle(): int
    {
        $translator = new ContentTranslator();

        $q = ContentAsset::query()
            ->whereNull('source_asset_id') // source assets only (not translations of translations)
            ->where('language', $this->option('source-lang'));

        if ($id = $this->option('asset')) {
            $q->where('id', (int) $id);
        } elseif ($briefId = $this->option('brief')) {
            $q->where('content_brief_id', (int) $briefId);
        } else {
            $this->error('Provide --asset=N or --brief=N');
            return self::INVALID;
        }

        $assets = $q->get();
        $total = $assets->count();

        if ($total === 0) {
            $this->info('No matching source assets.');
            return self::SUCCESS;
        }

        $langOption = $this->option('lang');
        $targets = $langOption === 'all'
            ? array_diff(ContentTranslator::SUPPORTED_LANGUAGES, [$this->option('source-lang')])
            : [$langOption];

        $this->info("🌍 Will translate {$total} asset(s) → " . count($targets) . ' language(s) (' . implode(', ', $targets) . ')');

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN — no API calls will be made.');
            return self::SUCCESS;
        }

        $sleep = (int) $this->option('sleep');
        $force = (bool) $this->option('force');

        $successCount = 0;
        $skipCount = 0;
        $failCount = 0;

        foreach ($assets as $asset) {
            $this->line("→ Asset #{$asset->id} ({$asset->asset_type}, src={$asset->language})");

            foreach ($targets as $target) {
                try {
                    $existing = ContentAsset::where('source_asset_id', $asset->id)
                        ->where('language', $target)
                        ->where('asset_type', $asset->asset_type)
                        ->first();

                    if ($existing && ! $force) {
                        $this->line("   ⏭️  {$target} already exists (#{$existing->id}) — skip");
                        $skipCount++;
                        continue;
                    }

                    $new = $translator->translate($asset, $target, $force);
                    $this->info("   ✅ {$target} → asset #{$new->id} (" . strlen($new->body_md) . ' chars)');
                    $successCount++;

                    if ($sleep > 0) sleep($sleep);
                } catch (\Throwable $e) {
                    $this->error("   ❌ {$target}: " . substr($e->getMessage(), 0, 200));
                    $failCount++;
                }
            }
        }

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("✅ Created: {$successCount}");
        $this->info("⏭️  Skipped: {$skipCount}");
        if ($failCount > 0) $this->error("❌ Failed: {$failCount}");

        return self::SUCCESS;
    }
}

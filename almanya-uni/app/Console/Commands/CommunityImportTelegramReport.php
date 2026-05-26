<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SimpleXMLElement;

/**
 * Telegram analiz raporu (xlsx 2026-05-18) import.
 * 6 sheet: Özet, Aylık, Konular, Gruplar, Heatmap, Top500 Soru.
 *
 * Cache: storage/app/community/telegram_report.json
 *
 * NOT: PHP zip extension olmadığı için xlsx'i önce manuel unzip'lenmeli:
 *   unzip telegram_rapor_2026-05-18.xlsx -d /tmp/tg_rapor
 *   --extracted=/tmp/tg_rapor parametresi ile bu komuta ver
 */
class CommunityImportTelegramReport extends Command
{
    protected $signature = 'community:import-telegram-report
        {--extracted= : Unzip\'lenmiş xlsx klasörü}
        {--name=general : Cache dosya adı (general, visa_denklik vs.)}';

    protected $description = 'Telegram rapor xlsx → cache JSON. Birden fazla rapor desteklenir: --name=visa_denklik';

    public function handle(): int
    {
        $base = $this->option('extracted') ?: '/tmp/tg_rapor';
        if (!is_dir($base . '/xl/worksheets')) {
            $this->error("Unzip'lenmiş klasör bulunamadı: $base");
            $this->line('Önce: unzip "telegram_rapor_X.xlsx" -d /tmp/tg_rapor_X');
            return self::FAILURE;
        }
        $name = preg_replace('/[^a-z0-9_]/', '', strtolower($this->option('name')));

        $sheets = [
            'ozet' => 1, 'aylik' => 2, 'konular' => 3,
            'gruplar' => 4, 'heatmap' => 5, 'top500_soru' => 6,
        ];

        $data = [
            'generated_at' => now()->toIso8601String(),
            'name' => $name,
            'extracted_from' => $base,
        ];

        foreach ($sheets as $key => $idx) {
            $path = "$base/xl/worksheets/sheet$idx.xml";
            if (!is_file($path)) {
                $this->warn("Sheet $idx (eksik): $path");
                continue;
            }
            $rows = $this->parseSheet($path);
            $data[$key] = $rows;
            $this->line('  ' . str_pad($key, 18) . ': ' . count($rows) . ' satır');
        }

        $cachePath = storage_path("app/community/telegram_report_{$name}.json");
        if (!is_dir(dirname($cachePath))) {
            mkdir(dirname($cachePath), 0755, true);
        }
        file_put_contents($cachePath, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->info('✅ ' . $cachePath . ' (' . round(filesize($cachePath) / 1024, 1) . ' KB)');
        return self::SUCCESS;
    }

    /**
     * SpreadsheetML sheet XML'ini parse et — inline string ("t=str").
     * Header (1. satır) → key olur, sonraki satırlar → assoc array.
     */
    private function parseSheet(string $path): array
    {
        $xml = file_get_contents($path);
        // Namespace'i kaldır (XPath kolaylığı için)
        $xml = preg_replace('/xmlns[^=]*="[^"]*"/', '', $xml);

        $doc = new SimpleXMLElement($xml);
        $rows = $doc->sheetData->row ?? null;
        if (!$rows) return [];

        $header = null;
        $result = [];
        foreach ($rows as $row) {
            $cells = [];
            foreach ($row->c as $c) {
                $ref = (string) ($c['r'] ?? '');
                $col = preg_replace('/\d+/', '', $ref); // A1 → A
                $val = (string) ($c->v ?? '');
                // Inline string (<is><t>...)
                if (isset($c->is->t)) {
                    $val = (string) $c->is->t;
                }
                $cells[$col] = $val;
            }

            if ($header === null) {
                $header = $cells;
                continue;
            }

            $assoc = [];
            foreach ($header as $col => $name) {
                $assoc[$name] = $cells[$col] ?? '';
            }
            $result[] = $assoc;
        }

        return $result;
    }
}

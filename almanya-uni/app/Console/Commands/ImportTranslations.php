<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * `i18n:export-content` ile üretilen gzip veri dosyalarını prod DB'ye uygular.
 * Bir migration'dan (veya /admin/ops üzerinden) çağrılır — KAS'ta CLI yok.
 * Idempotent. Bloklar: varsayılan sadece-boşken doldurur (--force ile üzerine
 * yazar). FAQ: bozuk/eksik prod satırını local sağlam sürümle DEĞİŞTİRİR.
 */
class ImportTranslations extends Command
{
    protected $signature = 'i18n:import-content {--force : Dolu blokların üzerine de yaz}';
    protected $description = 'Export edilmiş çevirileri (content_blocks + FAQ) prod DB\'ye uygula';

    /** dosya => [tablo] */
    private const BLOCK_FILES = [
        'city_blocks'       => 'cities',
        'university_blocks' => 'universities',
        'field_blocks'      => 'fields_of_study',
        'state_blocks'      => 'states',
    ];

    public function handle(): int
    {
        $dir = database_path('migrations/data');
        $force = (bool) $this->option('force');

        foreach (self::BLOCK_FILES as $file => $table) {
            $this->importBlocks("$dir/$file.json.gz", $table, $force);
        }
        $this->importFaqs("$dir/faq_translations.json.gz");

        return self::SUCCESS;
    }

    private function importBlocks(string $path, string $table, bool $force): void
    {
        $data = $this->readGz($path);
        if ($data === null) return;
        if (! Schema::hasColumn($table, 'content_blocks_en')) { $this->warn("$table: content_blocks_en kolonu yok, atlandı"); return; }

        $applied = 0;
        foreach ($data as $slug => $loc) {
            try {
                $row = DB::table($table)->where('slug', $slug)->first();
                if (! $row) continue;
                $update = [];
                foreach (['en', 'de'] as $l) {
                    if (empty($loc[$l])) continue;
                    $col = "content_blocks_$l";
                    if ($force || empty($row->$col)) {
                        $update[$col] = json_encode($loc[$l], JSON_UNESCAPED_UNICODE);
                    }
                }
                if ($update) { DB::table($table)->where('id', $row->id)->update($update); $applied++; }
            } catch (\Throwable $e) {
                \Log::warning("import blocks skip {$table}/{$slug}: " . $e->getMessage());
            }
        }
        $this->info("✅ {$table}: {$applied} satır güncellendi");
    }

    private function importFaqs(string $path): void
    {
        $data = $this->readGz($path);
        if ($data === null) return;

        $applied = 0;
        foreach ($data as $key => $rec) {
            try {
                [$group, $locale] = explode(':', $key, 2);
                $q = DB::table('faqs')->where('translation_group_id', $group)->where('locale', $locale);
                if (! $q->exists()) continue;
                $q->update([
                    'question'       => $rec['question'],
                    'answer_md'      => $rec['answer_md'],
                    'answer_html'    => $rec['answer_html'],
                    'answer_minutes' => $rec['answer_minutes'] ?? 1,
                    'has_answer'     => true,
                ]);
                $applied++;
            } catch (\Throwable $e) {
                \Log::warning("import faq skip {$key}: " . $e->getMessage());
            }
        }
        $this->info("✅ faqs: {$applied} satır güncellendi");
    }

    private function readGz(string $path): ?array
    {
        if (! is_file($path)) return null;
        $json = gzdecode(file_get_contents($path));
        $data = json_decode($json, true);
        return is_array($data) ? $data : null;
    }
}

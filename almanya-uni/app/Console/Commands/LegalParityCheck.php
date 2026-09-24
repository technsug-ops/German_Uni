<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Legacy `legal_pages.bodies` JSON'ı ile `legal_page_translations` satırlarının
 * birebir aynı olduğunu doğrular.
 *
 * İki aşamalı rollout'un (A: taşı, B: legacy kolonu düşür) A adımında, B'ye
 * geçmeden önce koşulması gereken kontrol budur. Prod'da da çalışır.
 */
class LegalParityCheck extends Command
{
    protected $signature = 'legal:parity';

    protected $description = 'legal_pages JSON gövdeleri ile legal_page_translations kayıtlarını karşılaştırır';

    public function handle(): int
    {
        if (! Schema::hasTable('legal_page_translations')) {
            $this->error('legal_page_translations tablosu yok — önce migration koşturun.');

            return self::FAILURE;
        }

        $rows = [];
        $fail = 0;

        foreach (DB::table('legal_pages')->orderBy('id')->get() as $page) {
            $bodies = json_decode($page->bodies ?? '', true);
            $bodies = is_array($bodies) ? $bodies : [];

            $translations = DB::table('legal_page_translations')
                ->where('legal_page_id', $page->id)
                ->pluck('body', 'locale')
                ->all();

            // JSON'da gövdesi olan diller + çeviri satırı olan diller birleşimi;
            // tek tarafta kalan bir dil de FAIL sayılmalı.
            $locales = array_unique(array_merge(
                array_keys(array_filter($bodies, fn ($b) => is_string($b) && trim($b) !== '')),
                array_keys($translations),
            ));
            sort($locales);

            foreach ($locales as $locale) {
                $legacy = $bodies[$locale] ?? null;
                $new = $translations[$locale] ?? null;

                $ok = $legacy !== null && $new !== null && $legacy === $new;
                $fail += $ok ? 0 : 1;

                $rows[] = [
                    $page->key,
                    $locale,
                    $legacy === null ? '—' : substr(hash('sha256', $legacy), 0, 12),
                    $new === null ? '—' : substr(hash('sha256', $new), 0, 12),
                    $ok ? 'PASS' : 'FAIL',
                ];
            }
        }

        $this->table(['key', 'locale', 'legacy JSON hash', 'translation hash', 'parity'], $rows);

        if ($fail > 0) {
            $this->error("{$fail} kayıtta parity SAĞLANAMADI — legacy kolon düşürülmemeli.");

            return self::FAILURE;
        }

        $this->info('Parity %100: ' . count($rows) . ' çeviri kaydının tamamı legacy JSON ile birebir aynı.');

        return self::SUCCESS;
    }
}

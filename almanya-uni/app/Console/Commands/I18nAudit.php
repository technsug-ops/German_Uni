<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class I18nAudit extends Command
{
    protected $signature = 'i18n:audit
        {--show-missing : List the missing keys per file}
        {--leaky : Also flag TR/DE values that still contain common English words}
        {--max=15 : Maximum sample size to print per category}';

    protected $description = 'Audit __() usage across views/controllers/support against lang/{tr,de,en}.json. Used as a discipline gate to keep TR/DE pages from leaking English.';

    public function handle(): int
    {
        $base = base_path();
        $dirs = [
            $base . '/resources/views',
            $base . '/app/Support',
            $base . '/app/Http/Controllers',
        ];

        $strings = [];
        foreach ($dirs as $dir) {
            if (! is_dir($dir)) continue;
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
                if (! $file->isFile()) continue;
                $ext = $file->getExtension();
                if (! in_array($ext, ['php', 'blade.php'])) continue;
                $content = file_get_contents($file->getPathname());
                if (strlen($content) > 2_000_000) continue;
                preg_match_all("/__\(\s*'((?:[^'\\\\]|\\\\.)*)'/", $content, $m1);
                preg_match_all('/__\(\s*"((?:[^"\\\\]|\\\\.)*)"/', $content, $m2);
                foreach (array_merge($m1[1], $m2[1]) as $raw) {
                    $strings[stripcslashes($raw)] = true;
                }
            }
        }
        $strings = array_keys($strings);

        $tr = json_decode(file_get_contents($base . '/lang/tr.json') ?: '{}', true) ?: [];
        $de = json_decode(file_get_contents($base . '/lang/de.json') ?: '{}', true) ?: [];
        $en = json_decode(file_get_contents($base . '/lang/en.json') ?: '{}', true) ?: [];

        $missingTr = $missingDe = $missingEn = [];
        foreach ($strings as $s) {
            if (! isset($tr[$s])) $missingTr[] = $s;
            if (! isset($de[$s])) $missingDe[] = $s;
            if (! isset($en[$s])) $missingEn[] = $s;
        }

        $this->newLine();
        $this->info('=== i18n Audit ===');
        $this->line('Total __() unique strings: ' . count($strings));
        $this->line('Missing in lang/tr.json: ' . count($missingTr));
        $this->line('Missing in lang/de.json: ' . count($missingDe));
        $this->line('Missing in lang/en.json (self-keys): ' . count($missingEn));

        $max = max(1, (int) $this->option('max'));
        if ($this->option('show-missing') && $missingTr) {
            $this->newLine();
            $this->warn('TR missing (first ' . $max . '):');
            foreach (array_slice($missingTr, 0, $max) as $s) {
                $this->line('  · ' . mb_substr($s, 0, 110));
            }
        }
        if ($this->option('show-missing') && $missingDe) {
            $this->newLine();
            $this->warn('DE missing (first ' . $max . '):');
            foreach (array_slice($missingDe, 0, $max) as $s) {
                $this->line('  · ' . mb_substr($s, 0, 110));
            }
        }

        if ($this->option('leaky')) {
            $enLeakWords = [
                'travel', 'accommodation', 'research allowance', 'family allowance', 'health insurance',
                'monthly stipend', 'stipend', 'tuition fee', 'tuition', 'course fee', 'scholarship database',
                'fellowship', 'allowance', 'Renewable Energy', 'Public Health', 'Tropical Agriculture',
                'application deadline', 'application fee', 'Semester Ticket', 'free of charge',
                'language course', 'free language course',
            ];
            $trMarkers = ['için', 'ile', 'değil', 'sağlık', 'aylık', 'üniversite', 'öğrenci', 'kurs', 'burs'];
            $deMarkers = ['für', 'mit', 'sind', 'nicht', 'oder', 'Studierende', 'Hochschule', 'Krankenversicherung'];
            $leakyTr = $leakyDe = [];
            foreach ($tr as $k => $v) {
                if (! is_string($v) || $v === $k) continue;
                $isTr = false;
                foreach ($trMarkers as $m) if (mb_stripos($v, $m) !== false) { $isTr = true; break; }
                if (! $isTr) continue;
                foreach ($enLeakWords as $en) {
                    if (preg_match('/(^|[\s>+,.;:()\[\]{}\/-])' . preg_quote($en, '/') . '($|[\s<+,.;:()\[\]{}\/-])/i', $v)) {
                        $leakyTr[$k] = $v;
                        break;
                    }
                }
            }
            foreach ($de as $k => $v) {
                if (! is_string($v) || $v === $k) continue;
                $isDe = false;
                foreach ($deMarkers as $m) if (mb_stripos($v, $m) !== false) { $isDe = true; break; }
                if (! $isDe) continue;
                foreach ($enLeakWords as $en) {
                    if (preg_match('/(^|[\s>+,.;:()\[\]{}\/-])' . preg_quote($en, '/') . '($|[\s<+,.;:()\[\]{}\/-])/i', $v)) {
                        $leakyDe[$k] = $v;
                        break;
                    }
                }
            }
            $this->newLine();
            $this->line('TR leaks (English words inside Turkish translation): ' . count($leakyTr));
            $this->line('DE leaks: ' . count($leakyDe));
            foreach (array_slice($leakyTr, 0, $max, true) as $k => $v) {
                $this->line('  · TR ' . mb_substr($k, 0, 60) . ' → ' . mb_substr($v, 0, 80));
            }
        }

        $this->newLine();
        return (count($missingTr) > 0 || count($missingDe) > 0) ? self::FAILURE : self::SUCCESS;
    }
}

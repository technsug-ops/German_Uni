<?php

namespace App\Console\Commands;

use App\Models\University;
use Illuminate\Console\Command;

class ReviewHrkCreates extends Command
{
    protected $signature = 'hrk:review-creates
        {--file=data/hrk-hochschulen.tsv}
        {--out=data/hrk-review.csv}
        {--threshold=70 : Benzerlik eşiği (similar_text %)}';

    protected $description = 'HRK\'da yeni eklenecek 57 üni için DB\'de muhtemel duplicate\'ları çıkar (manuel review)';

    public function handle(): int
    {
        $path = base_path($this->option('file'));
        $out = base_path($this->option('out'));
        $threshold = (int) $this->option('threshold');

        $hrkRows = $this->readTsv($path);

        $dbUnis = University::query()
            ->select(['id', 'wikidata_id', 'hs_nummer', 'name_de', 'name_en', 'short_name', 'data_source', 'website_url'])
            ->get();

        [$dbByDomain, $dbByNormName] = $this->buildIndex($dbUnis);

        $unmatched = [];
        foreach ($hrkRows as $row) {
            $hsNr = (int) ($row['Hs-Nr.'] ?? 0);
            if ($hsNr <= 0) continue;
            if ($this->isAlreadyImported($hsNr)) continue;

            if (! $this->hasMatch($row, $dbByDomain, $dbByNormName)) {
                $unmatched[] = $row;
            }
        }

        $fh = fopen($out, 'w');
        fputcsv($fh, [
            'hs_nr', 'hrk_name', 'hrk_short', 'hrk_city', 'hrk_typ', 'hrk_traeger', 'hrk_url',
            'suggestion_1_id', 'suggestion_1_name', 'suggestion_1_source', 'suggestion_1_pct',
            'suggestion_2_id', 'suggestion_2_name', 'suggestion_2_source', 'suggestion_2_pct',
            'suggestion_3_id', 'suggestion_3_name', 'suggestion_3_source', 'suggestion_3_pct',
            'decision (link_to_id | create_new | skip)',
        ]);

        $this->info("HRK'da kalan: " . count($unmatched));

        foreach ($unmatched as $row) {
            $hsNr = $row['Hs-Nr.'] ?? '';
            $hrkName = $row['Hochschulname'] ?? '';
            $hrkNorm = $this->normalize($hrkName);
            $hrkCity = $row['Ort (Hausanschrift)'] ?? '';

            $suggestions = [];
            foreach ($dbUnis as $u) {
                $best = 0;
                foreach ([$u->name_de, $u->name_en, $u->short_name] as $cand) {
                    if (! $cand) continue;
                    similar_text($hrkNorm, $this->normalize($cand), $pct);
                    if ($pct > $best) $best = $pct;
                }
                if ($best >= $threshold) {
                    $suggestions[] = ['u' => $u, 'pct' => round($best, 1)];
                }
            }
            usort($suggestions, fn ($a, $b) => $b['pct'] <=> $a['pct']);
            $suggestions = array_slice($suggestions, 0, 3);

            $rowOut = [
                $hsNr,
                $hrkName,
                $row['Hochschulkurzname'] ?? '',
                $hrkCity,
                $row['Hochschultyp'] ?? '',
                $row['Trägerschaft'] ?? '',
                $row['Home Page'] ?? '',
            ];

            for ($i = 0; $i < 3; $i++) {
                if (isset($suggestions[$i])) {
                    $s = $suggestions[$i];
                    $rowOut[] = $s['u']->id;
                    $rowOut[] = $s['u']->name_de;
                    $rowOut[] = $s['u']->data_source;
                    $rowOut[] = $s['pct'];
                } else {
                    $rowOut[] = '';
                    $rowOut[] = '';
                    $rowOut[] = '';
                    $rowOut[] = '';
                }
            }
            $rowOut[] = '';
            fputcsv($fh, $rowOut);
        }

        fclose($fh);
        $this->info("✅ Review CSV yazıldı: {$this->option('out')}");
        $this->newLine();
        $this->line("Bir sonraki adım:");
        $this->line("  1) CSV'yi Excel'de aç");
        $this->line("  2) Her satırın `decision` kolonuna yaz:");
        $this->line("     - `link_to_id:123` → DB'deki #123 üniyi HRK alanlarıyla zenginleştir");
        $this->line("     - `create_new`     → Yeni üni olarak ekle");
        $this->line("     - `skip`           → Atla");
        $this->line("  3) `php artisan hrk:apply-review`");

        return self::SUCCESS;
    }

    private function isAlreadyImported(int $hsNr): bool
    {
        return University::where('hs_nummer', $hsNr)->exists();
    }

    private function hasMatch(array $row, array $dbByDomain, array $dbByNormName): bool
    {
        $url = $row['Home Page'] ?? null;
        if ($url) {
            $d = $this->domain($url);
            if ($d && isset($dbByDomain[$d])) return true;
        }
        foreach ([$row['Hochschulname'] ?? null, $row['Adressname der Hochschule'] ?? null] as $name) {
            if (! $name) continue;
            $k = $this->normalize($name);
            if ($k !== '' && isset($dbByNormName[$k])) return true;
            if (strlen($k) >= 10) {
                foreach ($dbByNormName as $dbKey => $u) {
                    if (strlen($dbKey) < 10) continue;
                    if (str_contains($dbKey, $k) || str_contains($k, $dbKey)) return true;
                    similar_text($k, $dbKey, $pct);
                    if ($pct >= 92) return true;
                }
            }
        }
        return false;
    }

    private function buildIndex($dbUnis): array
    {
        $byDomain = [];
        $byName = [];
        foreach ($dbUnis as $u) {
            if ($u->website_url) {
                $d = $this->domain($u->website_url);
                if ($d) $byDomain[$d] ??= $u;
            }
            foreach ([$u->name_de, $u->name_en, $u->short_name] as $c) {
                if (! $c) continue;
                $k = $this->normalize($c);
                if ($k !== '') $byName[$k] ??= $u;
            }
        }
        return [$byDomain, $byName];
    }

    private function readTsv(string $path): array
    {
        $content = file_get_contents($path);
        $lines = preg_split('/\r\n|\n/', $content);
        $headers = null;
        $rows = [];
        foreach ($lines as $line) {
            if ($line === '') continue;
            $cols = explode("\t", $line);
            if ($headers === null) { $headers = $cols; continue; }
            if (count($cols) < count($headers) - 3) continue;
            $row = [];
            foreach ($headers as $i => $h) $row[$h] = $cols[$i] ?? null;
            $rows[] = $row;
        }
        return $rows;
    }

    private function domain(?string $url): ?string
    {
        if (! $url) return null;
        $url = trim($url);
        if (! preg_match('~^https?://~i', $url)) $url = 'http://' . $url;
        $host = parse_url($url, PHP_URL_HOST);
        if (! $host) return null;
        return preg_replace('/^www\./', '', strtolower($host));
    }

    private function normalize(string $name): string
    {
        $name = mb_strtolower($name, 'UTF-8');
        $name = strtr($name, ['ä'=>'ae','ö'=>'oe','ü'=>'ue','ß'=>'ss','é'=>'e','è'=>'e','ê'=>'e','á'=>'a','à'=>'a','â'=>'a']);
        $name = preg_replace('/\(.*?\)/', ' ', $name);
        $name = preg_replace('/\b(im breisgau|am main|an der donau|im rheinland|in baden|in westfalen|zu berlin)\b/u', ' ', $name);
        $name = preg_replace('/\b(e\.?\s?v\.?|gmbh|ggmbh|gemeinnuetzige.*)\b/u', ' ', $name);
        $name = preg_replace('/[^a-z0-9]+/', '', $name);
        return trim($name);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\University;
use Illuminate\Console\Command;

class AnalyzeHrk extends Command
{
    protected $signature = 'hrk:analyze
        {--file=data/hrk-hochschulen.tsv : TSV dosya yolu (project root\'a göre)}
        {--show-unmatched : Eşleşmeyenleri listele}
        {--export-mapping= : Eşleme tablosunu CSV\'ye yaz (yol)}';

    protected $description = 'HRK TSV ile DB üni listesi arasında eşleme analizi (read-only)';

    public function handle(): int
    {
        $path = base_path($this->option('file'));

        if (! is_file($path)) {
            $this->error("Dosya bulunamadı: {$path}");
            return self::FAILURE;
        }

        $hrkRows = $this->readTsv($path);
        $this->info("HRK dosyası: " . count($hrkRows) . " üni okundu");

        $dbUnis = University::query()
            ->select(['id', 'wikidata_id', 'name_de', 'name_en', 'slug', 'short_name', 'type', 'student_count', 'founded_year', 'website_url'])
            ->get();
        $this->info("DB: " . $dbUnis->count() . " üni");
        $this->newLine();

        $dbByDomain = [];
        $dbByNormName = [];
        foreach ($dbUnis as $u) {
            if ($u->website_url) {
                $domain = $this->domain($u->website_url);
                if ($domain) {
                    $dbByDomain[$domain] ??= $u;
                }
            }
            foreach ([$u->name_de, $u->name_en, $u->short_name] as $candidate) {
                if (! $candidate) continue;
                $key = $this->normalize($candidate);
                if ($key !== '') {
                    $dbByNormName[$key] ??= $u;
                }
            }
        }

        $matchedPairs = [];
        $unmatchedHrk = [];
        $matchSource = ['domain' => 0, 'exact_name' => 0, 'fuzzy' => 0];

        foreach ($hrkRows as $row) {
            $hit = null;
            $how = null;

            $url = $row['Home Page'] ?? null;
            if ($url) {
                $domain = $this->domain($url);
                if ($domain && isset($dbByDomain[$domain])) {
                    $hit = $dbByDomain[$domain];
                    $how = 'domain';
                }
            }

            if (! $hit) {
                $candidates = array_filter([
                    $row['Hochschulname'] ?? null,
                    $row['Adressname der Hochschule'] ?? null,
                ]);
                foreach ($candidates as $name) {
                    $key = $this->normalize($name);
                    if ($key !== '' && isset($dbByNormName[$key])) {
                        $hit = $dbByNormName[$key];
                        $how = 'exact_name';
                        break;
                    }
                }

                if (! $hit) {
                    foreach ($candidates as $name) {
                        $key = $this->normalize($name);
                        if (strlen($key) < 10) continue;
                        foreach ($dbByNormName as $dbKey => $dbU) {
                            if (strlen($dbKey) < 10) continue;
                            if (str_contains($dbKey, $key) || str_contains($key, $dbKey)) {
                                $hit = $dbU;
                                $how = 'fuzzy';
                                break 2;
                            }
                            similar_text($key, $dbKey, $pct);
                            if ($pct >= 92) {
                                $hit = $dbU;
                                $how = 'fuzzy';
                                break 2;
                            }
                        }
                    }
                }
            }

            if ($hit) {
                $matchSource[$how]++;
                $matchedPairs[] = [
                    'hs_nr' => $row['Hs-Nr.'] ?? '',
                    'hrk_short' => $row['Hochschulkurzname'] ?? '',
                    'hrk_name' => $row['Hochschulname'] ?? '',
                    'db_id' => $hit->id,
                    'db_name' => $hit->name_de,
                    'wikidata_id' => $hit->wikidata_id ?? '',
                    'match_via' => $how,
                ];
            } else {
                $unmatchedHrk[] = $row;
            }
        }

        $matchedDbIds = collect($matchedPairs)->pluck('db_id')->unique();
        $dbOnly = $dbUnis->whereNotIn('id', $matchedDbIds);
        $matched = count($matchedPairs);

        $this->info("📊 Eşleme Raporu");
        $this->info("├── HRK ∩ DB (eşleşen):     {$matched}");
        $this->line("│     ├── via domain:       {$matchSource['domain']}");
        $this->line("│     ├── via exact name:   {$matchSource['exact_name']}");
        $this->line("│     └── via fuzzy:        {$matchSource['fuzzy']}");
        $this->info("├── HRK \\ DB (sadece HRK):  " . count($unmatchedHrk));
        $this->info("└── DB \\ HRK (sadece DB):   " . $dbOnly->count());
        $this->newLine();

        $typeDist = [];
        foreach ($hrkRows as $row) {
            $t = $row['Hochschultyp'] ?? 'unknown';
            $typeDist[$t] = ($typeDist[$t] ?? 0) + 1;
        }
        $this->info("📚 HRK'daki Hochschultyp dağılımı:");
        foreach ($typeDist as $t => $c) {
            $this->line("    " . str_pad($t, 35) . " {$c}");
        }
        $this->newLine();

        $traegerDist = [];
        foreach ($hrkRows as $row) {
            $t = $row['Trägerschaft'] ?? 'unknown';
            $traegerDist[$t] = ($traegerDist[$t] ?? 0) + 1;
        }
        $this->info("🏛️  HRK'daki Trägerschaft dağılımı:");
        foreach ($traegerDist as $t => $c) {
            $this->line("    " . str_pad($t, 35) . " {$c}");
        }
        $this->newLine();

        if ($exportPath = $this->option('export-mapping')) {
            $abs = base_path($exportPath);
            $fh = fopen($abs, 'w');
            fputcsv($fh, ['hs_nr', 'hrk_short', 'hrk_name', 'db_id', 'wikidata_id', 'db_name', 'match_via']);
            foreach ($matchedPairs as $p) {
                fputcsv($fh, $p);
            }
            fclose($fh);
            $this->info("✅ Eşleme tablosu yazıldı: {$exportPath} ({$matched} satır)");
        }

        if ($this->option('show-unmatched')) {
            $this->warn("🆕 HRK'da var, DB'de yok (" . count($unmatchedHrk) . "):");
            foreach (array_slice($unmatchedHrk, 0, 50) as $row) {
                $this->line(sprintf(
                    "  [%s] %s — %s, %s",
                    $row['Hs-Nr.'] ?? '?',
                    $row['Hochschulname'] ?? '?',
                    $row['Bundesland'] ?? '?',
                    $row['Hochschultyp'] ?? '?'
                ));
            }
            if (count($unmatchedHrk) > 50) {
                $this->line("  ... ve " . (count($unmatchedHrk) - 50) . " tane daha");
            }
        }

        return self::SUCCESS;
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
            if ($headers === null) {
                $headers = $cols;
                continue;
            }

            if (count($cols) < count($headers) - 3) {
                continue;
            }

            $row = [];
            foreach ($headers as $i => $h) {
                $row[$h] = $cols[$i] ?? null;
            }
            $rows[] = $row;
        }

        return $rows;
    }

    private function domain(?string $url): ?string
    {
        if (! $url) return null;
        $url = trim($url);
        if (! preg_match('~^https?://~i', $url)) {
            $url = 'http://' . $url;
        }
        $host = parse_url($url, PHP_URL_HOST);
        if (! $host) return null;
        $host = strtolower($host);
        $host = preg_replace('/^www\./', '', $host);
        return $host;
    }

    private function normalize(string $name): string
    {
        $name = mb_strtolower($name, 'UTF-8');
        $name = strtr($name, [
            'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
            'é' => 'e', 'è' => 'e', 'ê' => 'e',
            'á' => 'a', 'à' => 'a', 'â' => 'a',
        ]);
        $name = preg_replace('/\(.*?\)/', ' ', $name);
        $name = preg_replace('/\b(im breisgau|am main|an der donau|im rheinland|in baden|in westfalen|zu berlin)\b/u', ' ', $name);
        $name = preg_replace('/\b(e\.?\s?v\.?|gmbh|ggmbh|gemeinnuetzige.*)\b/u', ' ', $name);
        $name = preg_replace('/[^a-z0-9]+/', '', $name);
        return trim($name);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportHrk extends Command
{
    protected $signature = 'hrk:import
        {--file=data/hrk-hochschulen.tsv : TSV dosya yolu (project root\'a göre)}
        {--dry-run : DB\'ye yazmadan rapor ver}
        {--no-create : Yeni üni ekleme, sadece mevcutları güncelle}
        {--no-update : Mevcutları güncelleme, sadece yeni ekle}';

    protected $description = 'HRK resmi üni listesini DB\'ye import et (eşleşeni update, yenisini create)';

    public function handle(): int
    {
        $path = base_path($this->option('file'));
        if (! is_file($path)) {
            $this->error("Dosya yok: {$path}");
            return self::FAILURE;
        }

        $dry = (bool) $this->option('dry-run');
        $noCreate = (bool) $this->option('no-create');
        $noUpdate = (bool) $this->option('no-update');

        if ($dry) $this->warn('⚠️  DRY-RUN: DB değişmeyecek');
        $this->newLine();

        $hrkRows = $this->readTsv($path);
        $this->info("HRK satır: " . count($hrkRows));

        $dbUnis = University::query()
            ->select(['id', 'wikidata_id', 'hs_nummer', 'name_de', 'name_en', 'slug', 'short_name', 'website_url', 'data_source'])
            ->get();
        $this->info("DB üni: {$dbUnis->count()}");
        $this->newLine();

        [$dbByDomain, $dbByNormName] = $this->buildIndex($dbUnis);
        $cities = City::query()->get()->keyBy(fn ($c) => mb_strtolower($c->name_de ?: $c->name_tr));

        $stats = ['updated' => 0, 'created' => 0, 'skipped' => 0, 'matched_via' => ['domain' => 0, 'exact' => 0, 'fuzzy' => 0]];
        $createdSamples = [];
        $updatedSamples = [];

        foreach ($hrkRows as $row) {
            $hsNr = (int) ($row['Hs-Nr.'] ?? 0);
            if ($hsNr <= 0) continue;

            [$match, $how] = $this->findMatch($row, $dbByDomain, $dbByNormName);

            $hrkData = $this->buildUniData($row, $cities);

            if ($match) {
                if ($noUpdate) { $stats['skipped']++; continue; }
                $stats['matched_via'][$how]++;

                $applied = $this->applyUpdate($match, $hrkData, $dry);
                $stats['updated']++;
                if (count($updatedSamples) < 5) {
                    $updatedSamples[] = "  [{$how}] HS#{$hsNr} {$match->name_de} ← " . implode(', ', $applied);
                }
            } else {
                if ($noCreate) { $stats['skipped']++; continue; }

                $created = $this->createNew($hrkData, $dry);
                $stats['created']++;
                if (count($createdSamples) < 10) {
                    $createdSamples[] = "  + HS#{$hsNr} {$hrkData['name_de']}";
                }
            }
        }

        $this->info("📊 Sonuç");
        $this->info("├── Güncellenen: {$stats['updated']}");
        $this->line("│     ├── domain: {$stats['matched_via']['domain']}");
        $this->line("│     ├── exact:  {$stats['matched_via']['exact']}");
        $this->line("│     └── fuzzy:  {$stats['matched_via']['fuzzy']}");
        $this->info("├── Yeni eklenen: {$stats['created']}");
        $this->info("└── Atlanan: {$stats['skipped']}");
        $this->newLine();

        if ($updatedSamples) {
            $this->line("Güncelleme örnekleri:");
            foreach ($updatedSamples as $s) $this->line($s);
            $this->newLine();
        }
        if ($createdSamples) {
            $this->line("Yeni üni örnekleri:");
            foreach ($createdSamples as $s) $this->line($s);
            $this->newLine();
        }

        if ($dry) {
            $this->warn("⚠️  DRY-RUN tamamlandı. Gerçek import için --dry-run flag'ini kaldır.");
        } else {
            $this->info("✅ Import tamamlandı.");
        }

        return self::SUCCESS;
    }

    private function findMatch(array $row, array $dbByDomain, array $dbByNormName): array
    {
        $url = $row['Home Page'] ?? null;
        if ($url) {
            $domain = $this->domain($url);
            if ($domain && isset($dbByDomain[$domain])) {
                return [$dbByDomain[$domain], 'domain'];
            }
        }

        $candidates = array_filter([
            $row['Hochschulname'] ?? null,
            $row['Adressname der Hochschule'] ?? null,
        ]);

        foreach ($candidates as $name) {
            $key = $this->normalize($name);
            if ($key !== '' && isset($dbByNormName[$key])) {
                return [$dbByNormName[$key], 'exact'];
            }
        }

        foreach ($candidates as $name) {
            $key = $this->normalize($name);
            if (strlen($key) < 10) continue;
            foreach ($dbByNormName as $dbKey => $dbU) {
                if (strlen($dbKey) < 10) continue;
                if (str_contains($dbKey, $key) || str_contains($key, $dbKey)) {
                    return [$dbU, 'fuzzy'];
                }
                similar_text($key, $dbKey, $pct);
                if ($pct >= 92) {
                    return [$dbU, 'fuzzy'];
                }
            }
        }

        return [null, null];
    }

    private function buildUniData(array $row, $cities): array
    {
        $hsNr = (int) ($row['Hs-Nr.'] ?? 0);
        $nameDe = trim($row['Hochschulname'] ?? '');
        $shortName = trim($row['Hochschulkurzname'] ?? '');
        $traegerschaft = trim($row['Trägerschaft'] ?? '');
        $hochschultyp = trim($row['Hochschultyp'] ?? '');

        $cityName = trim($row['Ort (Hausanschrift)'] ?? '');
        $cityId = null;
        if ($cityName !== '') {
            $key = mb_strtolower($cityName);
            $cityModel = $cities->get($key);
            if ($cityModel) $cityId = $cityModel->id;
        }

        $studentCount = (int) ($row['Anzahl Studierende'] ?? 0);
        $foundedYear = (int) ($row['Gründungsjahr'] ?? 0);

        return [
            'hs_nummer'         => $hsNr,
            'name_de'           => $nameDe,
            'short_name'        => $shortName !== '' ? $shortName : null,
            'hochschultyp'      => $hochschultyp !== '' ? $hochschultyp : null,
            'traegerschaft'     => $traegerschaft !== '' ? $traegerschaft : null,
            'promotion_recht'   => trim($row['Promotionsrecht'] ?? '') ?: null,
            'habilitation_recht'=> trim($row['Habilitationsrecht'] ?? '') ?: null,
            'hrk_member'        => isset($row['Mitglied HRK']) ? ($row['Mitglied HRK'] === '1') : null,
            'type'              => $this->mapType($hochschultyp, $traegerschaft),
            'student_count'     => $studentCount > 0 ? $studentCount : null,
            'founded_year'      => $foundedYear > 1000 && $foundedYear < 2100 ? $foundedYear : null,
            'website_url'       => $this->normalizeUrl($row['Home Page'] ?? null),
            'phone'             => $this->joinPhone($row['Telefonvorwahl'] ?? null, $row['Telefon'] ?? null),
            'street'            => trim($row['Straße'] ?? '') ?: null,
            'postal_code'       => trim($row['Postleitzahl (Hausanschrift)'] ?? '') ?: null,
            'city_id'           => $cityId,
            'city_name_hrk'     => $cityName,
        ];
    }

    private function applyUpdate(University $uni, array $hrkData, bool $dry): array
    {
        $applied = [];

        $alwaysFromHrk = ['hs_nummer', 'hochschultyp', 'traegerschaft', 'promotion_recht', 'habilitation_recht', 'hrk_member'];
        foreach ($alwaysFromHrk as $field) {
            if (! is_null($hrkData[$field]) && $uni->{$field} !== $hrkData[$field]) {
                $uni->{$field} = $hrkData[$field];
                $applied[] = $field;
            }
        }

        $fillIfEmpty = ['short_name', 'website_url', 'phone', 'street', 'postal_code', 'student_count', 'founded_year', 'city_id', 'name_de'];
        foreach ($fillIfEmpty as $field) {
            if (! is_null($hrkData[$field]) && empty($uni->{$field})) {
                $uni->{$field} = $hrkData[$field];
                $applied[] = $field;
            }
        }

        $uni->data_source = $uni->data_source === 'wikidata' ? 'wikidata+hrk' : 'hrk';
        $uni->last_synced_at = now();

        if (! $dry && $uni->isDirty()) {
            $uni->saveQuietly();
        }

        return $applied;
    }

    private function createNew(array $hrkData, bool $dry): ?University
    {
        $slug = $this->slugify($hrkData['name_de']) . '-hs' . $hrkData['hs_nummer'];

        $attrs = [
            'name_de'           => $hrkData['name_de'],
            'name_tr'           => $hrkData['name_de'],
            'slug'              => $slug,
            'hs_nummer'         => $hrkData['hs_nummer'],
            'short_name'        => $hrkData['short_name'],
            'hochschultyp'      => $hrkData['hochschultyp'],
            'traegerschaft'     => $hrkData['traegerschaft'],
            'promotion_recht'   => $hrkData['promotion_recht'],
            'habilitation_recht'=> $hrkData['habilitation_recht'],
            'hrk_member'        => $hrkData['hrk_member'],
            'type'              => $hrkData['type'],
            'student_count'     => $hrkData['student_count'],
            'founded_year'      => $hrkData['founded_year'],
            'website_url'       => $hrkData['website_url'],
            'phone'             => $hrkData['phone'],
            'street'            => $hrkData['street'],
            'postal_code'       => $hrkData['postal_code'],
            'city_id'           => $hrkData['city_id'],
            'data_source'       => 'hrk',
            'last_synced_at'    => now(),
            'is_active'         => true,
        ];

        if ($dry) return null;
        return University::withoutSyncingToSearch(fn () => University::create($attrs));
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
            foreach ([$u->name_de, $u->name_en, $u->short_name] as $cand) {
                if (! $cand) continue;
                $key = $this->normalize($cand);
                if ($key !== '') $byName[$key] ??= $u;
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
        $host = strtolower($host);
        return preg_replace('/^www\./', '', $host);
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

    private function normalizeUrl(?string $url): ?string
    {
        if (! $url) return null;
        $url = trim($url);
        if ($url === '') return null;
        if (! preg_match('~^https?://~i', $url)) $url = 'http://' . $url;
        return $url;
    }

    private function joinPhone(?string $vorwahl, ?string $rest): ?string
    {
        $v = trim((string) $vorwahl);
        $r = trim((string) $rest);
        if ($v === '' && $r === '') return null;
        if ($v && $r) return "{$v} {$r}";
        return $v ?: $r;
    }

    private function mapType(string $hochschultyp, string $traegerschaft): string
    {
        if (str_contains($hochschultyp, 'Künstlerische')) return 'art';
        if (str_contains($hochschultyp, 'Fachhochschul') || str_contains($hochschultyp, 'HAW')) return 'applied_sciences';
        if (str_contains($traegerschaft, 'kirchlich')) return 'religion';
        if (str_contains($traegerschaft, 'privat')) return 'private';
        return 'public';
    }

    private function slugify(string $name): string
    {
        return Str::slug(Str::limit($name, 80, ''));
    }
}

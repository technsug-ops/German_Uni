<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ApplyHrkReview extends Command
{
    protected $signature = 'hrk:apply-review
        {--decisions=data/hrk-decisions.csv : Karar dosyası (hs_nr,decision,note)}
        {--hrk=data/hrk-hochschulen.tsv : HRK TSV dosyası}
        {--dry-run : DB\'ye yazmadan rapor}';

    protected $description = 'HRK review kararlarını uygula (skip/link_to_id/create_new)';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        if ($dry) $this->warn('⚠️  DRY-RUN');

        $decisions = $this->readDecisions(base_path($this->option('decisions')));
        $hrkRows = $this->readTsv(base_path($this->option('hrk')));
        $hrkByNr = [];
        foreach ($hrkRows as $row) {
            $nr = (int) ($row['Hs-Nr.'] ?? 0);
            if ($nr > 0) $hrkByNr[$nr] = $row;
        }

        $cities = City::all()->keyBy(fn ($c) => mb_strtolower($c->name_de ?: $c->name_tr));

        $stats = ['skipped' => 0, 'linked' => 0, 'created' => 0, 'errors' => 0];
        $samples = ['linked' => [], 'created' => []];

        foreach ($decisions as $hsNr => $info) {
            $decision = $info['decision'];
            $row = $hrkByNr[$hsNr] ?? null;

            if (! $row) {
                $this->warn("HS#{$hsNr} HRK TSV'de yok, atlandı");
                $stats['errors']++;
                continue;
            }

            try {
                if ($decision === 'skip') {
                    $stats['skipped']++;
                } elseif (str_starts_with($decision, 'link_to_id:')) {
                    $targetId = (int) substr($decision, strlen('link_to_id:'));
                    $target = University::find($targetId);
                    if (! $target) {
                        $this->error("HS#{$hsNr} link target ID#{$targetId} bulunamadı");
                        $stats['errors']++;
                        continue;
                    }
                    $hrkData = $this->buildUniData($row, $cities);
                    $this->applyUpdate($target, $hrkData, $dry);
                    $stats['linked']++;
                    if (count($samples['linked']) < 10) {
                        $samples['linked'][] = "  HS#{$hsNr} {$row['Hochschulname']} → #{$targetId} {$target->name_de}";
                    }
                } elseif ($decision === 'create_new') {
                    $hrkData = $this->buildUniData($row, $cities);
                    $this->createNew($hrkData, $dry);
                    $stats['created']++;
                    if (count($samples['created']) < 10) {
                        $samples['created'][] = "  + HS#{$hsNr} {$row['Hochschulname']}";
                    }
                } else {
                    $this->warn("HS#{$hsNr} bilinmeyen decision: {$decision}");
                    $stats['errors']++;
                }
            } catch (\Throwable $e) {
                $this->error("HS#{$hsNr} hata: " . $e->getMessage());
                $stats['errors']++;
            }
        }

        $this->newLine();
        $this->info("📊 Sonuç");
        $this->info("├── Atlanan (skip):       {$stats['skipped']}");
        $this->info("├── Bağlanan (link):      {$stats['linked']}");
        $this->info("├── Yeni oluşturulan:     {$stats['created']}");
        $this->info("└── Hata:                 {$stats['errors']}");

        if ($samples['linked']) {
            $this->newLine();
            $this->line("Bağlanan örnekler:");
            foreach ($samples['linked'] as $s) $this->line($s);
        }
        if ($samples['created']) {
            $this->newLine();
            $this->line("Yeni eklenen örnekler:");
            foreach ($samples['created'] as $s) $this->line($s);
        }

        if ($dry) {
            $this->newLine();
            $this->warn("DRY-RUN tamamlandı.");
        } else {
            $this->info("✅ Apply tamamlandı.");
        }

        return self::SUCCESS;
    }

    private function readDecisions(string $path): array
    {
        $fh = fopen($path, 'r');
        if (! $fh) return [];
        $headers = fgetcsv($fh);
        $out = [];
        while (($cols = fgetcsv($fh)) !== false) {
            $row = array_combine($headers, $cols);
            $nr = (int) ($row['hs_nr'] ?? 0);
            if ($nr > 0 && ! empty($row['decision'])) {
                $out[$nr] = ['decision' => trim($row['decision']), 'note' => $row['note'] ?? ''];
            }
        }
        fclose($fh);
        return $out;
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
        ];
    }

    private function applyUpdate(University $uni, array $hrkData, bool $dry): void
    {
        $alwaysFromHrk = ['hs_nummer', 'hochschultyp', 'traegerschaft', 'promotion_recht', 'habilitation_recht', 'hrk_member'];
        foreach ($alwaysFromHrk as $field) {
            if (! is_null($hrkData[$field])) $uni->{$field} = $hrkData[$field];
        }
        $fillIfEmpty = ['short_name', 'website_url', 'phone', 'street', 'postal_code', 'student_count', 'founded_year', 'city_id', 'name_de'];
        foreach ($fillIfEmpty as $field) {
            if (! is_null($hrkData[$field]) && empty($uni->{$field})) $uni->{$field} = $hrkData[$field];
        }

        $uni->data_source = $uni->data_source === 'wikidata' ? 'wikidata+hrk' : ($uni->data_source === 'partner' ? 'partner+hrk' : 'hrk');
        $uni->last_synced_at = now();

        if (! $dry && $uni->isDirty()) {
            $uni->saveQuietly();
        }
    }

    private function createNew(array $hrkData, bool $dry): ?University
    {
        $slug = Str::slug(Str::limit($hrkData['name_de'], 80, '')) . '-hs' . $hrkData['hs_nummer'];

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
}

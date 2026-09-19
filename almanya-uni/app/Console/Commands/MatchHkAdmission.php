<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Models\University;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('programs:match-hk-admission
    {--in=storage/app/hk-zulassungsfrei.json : Hochschulkompass JSON yolu}
    {--out=storage/app/admission-hk.csv : Çıktı CSV (program_slug,admission_mode)}
    {--mode=zulassungsfrei : İşaretlenecek admission_mode değeri}
    {--fuzzy=90 : Program adı fuzzy eşik (similar_text %)}
    {--report= : Eşleşmeyenleri buraya yaz (debug)}')]
#[Description('Hochschulkompass program listesini DB programlarına üni+ad ile eşleştirip import CSV üretir.')]
class MatchHkAdmission extends Command
{
    public function handle(): int
    {
        $in = base_path($this->option('in'));
        if (! is_file($in)) { $this->error("JSON yok: {$in}"); return self::FAILURE; }

        $rows = json_decode(file_get_contents($in), true);
        if (! is_array($rows)) { $this->error('JSON parse edilemedi'); return self::FAILURE; }
        $this->info('HK satır: ' . count($rows));

        // 1) DB üni index'i: normUni => uni_id  + token/şehir index
        $uniById = [];
        $uniIndex = [];   // normName => id
        $uniTokens = [];  // uni_id => [token set, city]
        foreach (University::query()->with('city')->get(['id', 'name_de', 'name_en', 'short_name', 'city_id']) as $u) {
            $uniById[$u->id] = $u;
            $cityTok = $this->cityToken(optional($u->city)->name_de ?: optional($u->city)->name_tr ?: '');
            $tokAll = [];
            foreach ([$u->name_de, $u->name_en, $u->short_name] as $cand) {
                if (! $cand) continue;
                $k = $this->normUni($cand);
                if ($k !== '') $uniIndex[$k] ??= $u->id;
                foreach ($this->tokens($cand) as $t) $tokAll[$t] = true;
            }
            $uniTokens[$u->id] = ['tok' => array_keys($tokAll), 'city' => $cityTok];
        }
        $this->uniTokens = $uniTokens;

        // 2) DB program index'i: uni_id => [ [normName, degree, slug], ... ]
        $progByUni = [];
        Program::query()->select(['id', 'university_id', 'name_de', 'name_en', 'name_tr', 'slug', 'degree'])
            ->chunk(2000, function ($chunk) use (&$progByUni) {
                foreach ($chunk as $p) {
                    if (! $p->university_id) continue;
                    $names = array_unique(array_filter([$p->name_de, $p->name_en, $p->name_tr]));
                    foreach ($names as $n) {
                        $progByUni[$p->university_id][] = [
                            'norm' => $this->normProg($n),
                            'degree' => $p->degree,
                            'slug' => $p->slug,
                        ];
                    }
                }
            });

        $stats = ['matched' => 0, 'uni_miss' => 0, 'prog_miss' => 0, 'via_exact' => 0, 'via_fuzzy' => 0];
        $matchedSlugs = [];
        $unmatched = [];
        $fuzzy = (int) $this->option('fuzzy');

        $bar = $this->output->createProgressBar(count($rows));
        foreach ($rows as $r) {
            $bar->advance();
            $uniId = $this->resolveUni($r['hochschule'] ?? '', $uniIndex, $r['ort'] ?? '');
            if (! $uniId) { $stats['uni_miss']++; $unmatched[] = "UNI_MISS\t{$r['hochschule']}\t{$r['fach']}"; continue; }

            $progs = $progByUni[$uniId] ?? [];
            if (! $progs) { $stats['prog_miss']++; $unmatched[] = "NO_PROGS\t{$r['hochschule']}\t{$r['fach']}"; continue; }

            $target = $this->normProg($r['fach'] ?? '');
            $wantDeg = $this->mapDegree($r['abschluss'] ?? '', $r['typ'] ?? '');

            // exact normName + (uyumlu degree öncelik)
            $hit = null; $how = null;
            foreach ($progs as $p) {
                if ($p['norm'] === $target) {
                    if ($wantDeg && $p['degree'] === $wantDeg) { $hit = $p; $how = 'exact'; break; }
                    if (! $hit) { $hit = $p; $how = 'exact'; }
                }
            }
            // fuzzy
            if (! $hit && strlen($target) >= 6) {
                $best = 0;
                foreach ($progs as $p) {
                    if (strlen($p['norm']) < 6) continue;
                    similar_text($target, $p['norm'], $pct);
                    if ($pct > $best && $pct >= $fuzzy) {
                        if ($wantDeg && $p['degree'] && $p['degree'] !== $wantDeg) continue;
                        $best = $pct; $hit = $p; $how = 'fuzzy';
                    }
                }
            }

            if ($hit) {
                $matchedSlugs[$hit['slug']] = true;
                $stats['matched']++;
                $stats['via_' . $how]++;
            } else {
                $stats['prog_miss']++;
                $unmatched[] = "PROG_MISS\t{$r['hochschule']}\t{$r['fach']}\t{$wantDeg}";
            }
        }
        $bar->finish();
        $this->newLine(2);

        // CSV yaz
        $out = base_path($this->option('out'));
        $fh = fopen($out, 'w');
        fputcsv($fh, ['program_slug', 'admission_mode']);
        foreach (array_keys($matchedSlugs) as $slug) {
            fputcsv($fh, [$slug, $this->option('mode')]);
        }
        fclose($fh);

        $this->info('📊 Eşleştirme sonucu');
        $this->line("├── Eşleşen program (uniq slug): " . count($matchedSlugs));
        $this->line("│     ├── exact: {$stats['via_exact']}");
        $this->line("│     └── fuzzy: {$stats['via_fuzzy']}");
        $this->line("├── Üni bulunamadı:  {$stats['uni_miss']}");
        $this->line("├── Program eşleşmedi: {$stats['prog_miss']}");
        $this->line("└── CSV: {$out} (" . count($matchedSlugs) . " satır)");

        if ($report = $this->option('report')) {
            file_put_contents(base_path($report), implode("\n", array_slice($unmatched, 0, 3000)));
            $this->line("Eşleşmeyen örnekleri: {$report}");
        }

        return self::SUCCESS;
    }

    private array $uniTokens = [];

    private function resolveUni(string $name, array $uniIndex, string $ort = ''): ?int
    {
        $k = $this->normUni($name);
        if ($k === '') return null;
        if (isset($uniIndex[$k])) return $uniIndex[$k];

        // contains (uzun normalize edilmiş ad)
        if (strlen($k) >= 8) {
            foreach ($uniIndex as $dk => $id) {
                if (strlen($dk) >= 8 && (str_contains($dk, $k) || str_contains($k, $dk))) return $id;
            }
        }

        // token-örtüşme (Jaccard) + şehir teyidi
        $hkTok = $this->tokens($name);
        if (! $hkTok) return null;
        $hkCity = $this->cityToken($ort);
        $bestId = null; $bestScore = 0.0;
        foreach ($this->uniTokens as $id => $info) {
            $dbTok = $info['tok'];
            if (! $dbTok) continue;
            $inter = count(array_intersect($hkTok, $dbTok));
            if ($inter === 0) continue;
            $union = count(array_unique(array_merge($hkTok, $dbTok)));
            $jac = $inter / max(1, $union);
            // şehir eşleşmesi güçlü sinyal
            $cityBonus = ($hkCity && $info['city'] && $hkCity === $info['city']) ? 0.35 : 0.0;
            $score = $jac + $cityBonus;
            // en az 1 ayırt edici (uzun) token ortak olmalı
            $hasLong = false;
            foreach (array_intersect($hkTok, $dbTok) as $t) if (strlen($t) >= 5) { $hasLong = true; break; }
            if (! $hasLong && $cityBonus === 0.0) continue;
            if ($score > $bestScore) { $bestScore = $score; $bestId = $id; }
        }
        return $bestScore >= 0.45 ? $bestId : null;
    }

    /** Üni adından ayırt edici token kümesi (jenerik kelimeler atılır) */
    private function tokens(string $s): array
    {
        $s = mb_strtolower($s, 'UTF-8');
        $s = strtr($s, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss', '-' => ' ', '.' => ' ', ',' => ' ', ':' => ' ']);
        $stop = ['university', 'universitaet', 'hochschule', 'fachhochschule', 'technische', 'of', 'applied', 'sciences',
            'fuer', 'fur', 'und', 'der', 'die', 'das', 'uas', 'haw', 'fh', 'tu', 'fu', 'uni', 'and', 'the', 'in', 'am', 'zu', 'des'];
        $out = [];
        foreach (preg_split('/\s+/', $s) as $w) {
            $w = preg_replace('/[^a-z0-9]/', '', $w);
            if (strlen($w) < 3 || in_array($w, $stop, true)) continue;
            $out[$w] = true;
        }
        return array_keys($out);
    }

    private function cityToken(string $s): string
    {
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = strtr($s, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        $s = preg_replace('/\s*\(.*?\)\s*/', '', $s);
        $s = preg_replace('/[^a-z]/', '', $s);
        return $s;
    }

    private function mapDegree(string $abschluss, string $typ): ?string
    {
        $a = mb_strtolower($abschluss . ' ' . $typ);
        if (str_contains($a, 'bachelor') || str_contains($a, 'b.sc') || str_contains($a, 'b.a') || str_contains($a, 'b.eng') || str_contains($a, 'grundständig')) return 'bachelor';
        if (str_contains($a, 'master') || str_contains($a, 'm.sc') || str_contains($a, 'm.a') || str_contains($a, 'm.eng') || str_contains($a, 'weiterführend')) return 'master';
        if (str_contains($a, 'staatsexamen') || str_contains($a, 'staatsprüfung')) return null;
        return null;
    }

    private function normUni(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $s = strtr($s, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        // açıklayıcı ekleri kes: " - ..." ve ", ..." (örn "Hochschule Aalen - Technik, ...")
        $s = preg_replace('/\s[-–:].*$/u', ' ', $s);
        $s = preg_replace('/,.*$/u', ' ', $s);
        $s = preg_replace('/\(.*?\)/', ' ', $s);
        $s = preg_replace('/\b(university of applied sciences|university|universitaet|hochschule|fachhochschule|technische|fh|uni)\b/u', ' ', $s);
        $s = preg_replace('/\b(im breisgau|am main|an der donau|zu berlin|in westfalen|of applied sciences|applied sciences)\b/u', ' ', $s);
        $s = preg_replace('/[^a-z0-9]+/', '', $s);
        return $s;
    }

    private function normProg(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $s = strtr($s, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss', '–' => '-', '—' => '-']);
        // derece etiketleri
        $s = preg_replace('/\b(b\.?\s?sc|m\.?\s?sc|b\.?\s?a|m\.?\s?a|b\.?\s?eng|m\.?\s?eng|bachelor|master|of science|of arts|of engineering|llm|ll\.m|mba)\b\.?/u', ' ', $s);
        $s = preg_replace('/\(.*?\)/', ' ', $s);
        $s = preg_replace('/[^a-z0-9]+/', '', $s);
        return $s;
    }
}

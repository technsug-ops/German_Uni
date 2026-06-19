<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * hk_catalog (Hochschulkompass scrape, tüm Zulassungsmodus'lar) → DB programları
 * eşleştirir; SADECE admission_mode'u BOŞ olan programlar için slug => mode haritası
 * üretir (mevcut veriyi EZMEZ). Çıktı resources/data/hk-admission-extra.json.
 *
 * Eşleştirme: üni adı normalize + token/şehir teyidi, program adı exact + fuzzy(≥%90).
 * MatchHkAdmission ile aynı mantık. Yeni program YARATMAZ — sadece işaretleme verisi.
 * Migration bu JSON'u fill-empty-only uygular (prod'da hk_catalog gerektirmez).
 */
class BackfillHkAdmission extends Command
{
    protected $signature = 'programs:backfill-hk-admission {--fuzzy=90} {--out=resources/data/hk-admission-extra.json}';

    protected $description = 'hk_catalog kabul modlarını boş programlara eşleştirip slug=>mode JSON üretir (ezmeden).';

    public function handle(): int
    {
        if (! DB::getSchemaBuilder()->hasTable('hk_catalog')) {
            $this->error('hk_catalog yok — önce programs:load-hk-backup çalıştır.');
            return self::FAILURE;
        }
        $fuzzy = (int) $this->option('fuzzy');

        // 1) Üni index'i
        $uniIndex = [];
        $uniTokens = [];
        foreach (DB::table('universities as u')->leftJoin('cities as c', 'c.id', '=', 'u.city_id')
            ->where('u.is_active', 1)->get(['u.id', 'u.name_de', 'u.name_en', 'u.short_name', 'c.name_de as city']) as $u) {
            $tokAll = [];
            foreach ([$u->name_de, $u->name_en, $u->short_name] as $cand) {
                if (! $cand) continue;
                $k = $this->normUni($cand);
                if ($k !== '') $uniIndex[$k] ??= $u->id;
                foreach ($this->tokens($cand) as $t) $tokAll[$t] = true;
            }
            $uniTokens[$u->id] = ['tok' => array_keys($tokAll), 'city' => $this->cityTok($u->city ?? '')];
        }

        // 2) Program index'i (sadece admission_mode BOŞ olanlar — dolduracaklarımız)
        $progByUni = [];
        DB::table('programs')->select('university_id', 'name_de', 'name_en', 'name_tr', 'slug', 'degree')
            ->where('is_active', 1)
            ->where(fn ($q) => $q->whereNull('admission_mode')->orWhere('admission_mode', ''))
            ->orderBy('id')->chunk(2000, function ($chunk) use (&$progByUni) {
                foreach ($chunk as $p) {
                    if (! $p->university_id) continue;
                    foreach (array_unique(array_filter([$p->name_de, $p->name_en, $p->name_tr])) as $n) {
                        $progByUni[$p->university_id][] = ['norm' => $this->normProg($n), 'degree' => $p->degree, 'slug' => $p->slug];
                    }
                }
            });

        // 3) hk_catalog satırlarını eşleştir
        $map = [];
        $stats = ['rows' => 0, 'matched' => 0, 'exact' => 0, 'fuzzy' => 0];
        foreach (DB::table('hk_catalog')->get(['hochschule', 'fach', 'ort', 'abschluss', 'typ', 'zulassung', 'mode']) as $r) {
            $stats['rows']++;
            $hkMode = $r->zulassung ?: $r->mode;
            if (! in_array($hkMode, ['zulassungsfrei', 'oertlich', 'auswahl', 'bundesweit'], true)) continue;

            $uniId = $this->resolveUni($r->hochschule ?? '', $r->ort ?? '', $uniIndex, $uniTokens);
            if (! $uniId || empty($progByUni[$uniId])) continue;

            $target = $this->normProg($r->fach ?? '');
            $wantDeg = $this->mapDeg($r->abschluss ?? '', $r->typ ?? '');
            $hit = null; $how = null;
            foreach ($progByUni[$uniId] as $p) {
                if ($p['norm'] === $target) {
                    if ($wantDeg && $p['degree'] === $wantDeg) { $hit = $p; $how = 'exact'; break; }
                    if (! $hit) { $hit = $p; $how = 'exact'; }
                }
            }
            if (! $hit && strlen($target) >= 6) {
                $best = 0;
                foreach ($progByUni[$uniId] as $p) {
                    if (strlen($p['norm']) < 6) continue;
                    similar_text($target, $p['norm'], $pct);
                    if ($pct > $best && $pct >= $fuzzy) {
                        if ($wantDeg && $p['degree'] && $p['degree'] !== $wantDeg) continue;
                        $best = $pct; $hit = $p; $how = 'fuzzy';
                    }
                }
            }
            if (! $hit) continue;

            // Aynı slug birden çok HK satırına düşerse: ilk eşleşme kalır (deterministik).
            if (! isset($map[$hit['slug']])) { $map[$hit['slug']] = $hkMode; $stats['matched']++; $stats[$how]++; }
        }

        $out = base_path($this->option('out'));
        @mkdir(dirname($out), 0775, true);
        file_put_contents($out, json_encode($map, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");

        $byMode = array_count_values($map);
        arsort($byMode);
        $this->info('📊 Backfill haritası üretildi (sadece BOŞ programlar):');
        $this->line('   toplam: ' . count($map) . " (exact {$stats['exact']}, fuzzy {$stats['fuzzy']})");
        foreach ($byMode as $m => $c) $this->line("   - {$m}: {$c}");
        $this->line('   → ' . $out);

        return self::SUCCESS;
    }

    private function resolveUni(string $name, string $ort, array $uniIndex, array $uniTokens): ?int
    {
        $k = $this->normUni($name);
        if ($k === '') return null;
        if (isset($uniIndex[$k])) return $uniIndex[$k];
        if (strlen($k) >= 8) {
            foreach ($uniIndex as $dk => $id) {
                if (strlen($dk) >= 8 && (str_contains($dk, $k) || str_contains($k, $dk))) return $id;
            }
        }
        $hkTok = $this->tokens($name);
        if (! $hkTok) return null;
        $hkCity = $this->cityTok($ort);
        $bestId = null; $bestScore = 0.0;
        foreach ($uniTokens as $id => $info) {
            $dbTok = $info['tok'];
            if (! $dbTok) continue;
            $inter = count(array_intersect($hkTok, $dbTok));
            if ($inter === 0) continue;
            $jac = $inter / max(1, count(array_unique(array_merge($hkTok, $dbTok))));
            $cityBonus = ($hkCity && $info['city'] && $hkCity === $info['city']) ? 0.35 : 0.0;
            $hasLong = false;
            foreach (array_intersect($hkTok, $dbTok) as $t) if (strlen($t) >= 5) { $hasLong = true; break; }
            if (! $hasLong && $cityBonus === 0.0) continue;
            $score = $jac + $cityBonus;
            if ($score > $bestScore) { $bestScore = $score; $bestId = $id; }
        }
        return $bestScore >= 0.45 ? $bestId : null;
    }

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

    private function cityTok(string $s): string
    {
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = strtr($s, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        $s = preg_replace('/\s*\(.*?\)\s*/', '', $s);
        return preg_replace('/[^a-z]/', '', $s);
    }

    private function mapDeg(string $abschluss, string $typ): ?string
    {
        $a = mb_strtolower($abschluss . ' ' . $typ);
        if (str_contains($a, 'bachelor') || str_contains($a, 'b.sc') || str_contains($a, 'b.a') || str_contains($a, 'b.eng') || str_contains($a, 'grundständig')) return 'bachelor';
        if (str_contains($a, 'master') || str_contains($a, 'm.sc') || str_contains($a, 'm.a') || str_contains($a, 'm.eng') || str_contains($a, 'weiterführend')) return 'master';
        return null;
    }

    private function normUni(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $s = strtr($s, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        $s = preg_replace('/\s[-–:].*$/u', ' ', $s);
        $s = preg_replace('/,.*$/u', ' ', $s);
        $s = preg_replace('/\(.*?\)/', ' ', $s);
        $s = preg_replace('/\b(university of applied sciences|university|universitaet|hochschule|fachhochschule|technische|fh|uni)\b/u', ' ', $s);
        $s = preg_replace('/\b(im breisgau|am main|an der donau|zu berlin|in westfalen|of applied sciences|applied sciences)\b/u', ' ', $s);
        return preg_replace('/[^a-z0-9]+/', '', $s);
    }

    private function normProg(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $s = strtr($s, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss', '–' => '-', '—' => '-']);
        $s = preg_replace('/\b(b\.?\s?sc|m\.?\s?sc|b\.?\s?a|m\.?\s?a|b\.?\s?eng|m\.?\s?eng|bachelor|master|of science|of arts|of engineering|llm|ll\.m|mba)\b\.?/u', ' ', $s);
        $s = preg_replace('/\(.*?\)/', ' ', $s);
        return preg_replace('/[^a-z0-9]+/', '', $s);
    }
}

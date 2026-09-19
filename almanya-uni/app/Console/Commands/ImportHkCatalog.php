<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Models\University;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('programs:import-hk-catalog
    {--glob=storage/app/hk-*.json : Okunacak HK mod dosyaları (zulassungsfrei/oertlich/bundesweit/auswahl)}
    {--dry-run : DB değişmez, sadece rapor}
    {--update-only : Sadece mevcut programların admission_mode\'unu güncelle, yeni program EKLEME}
    {--create-unis : DB\'de olmayan üniversiteleri de oluştur (tam katalog için)}
    {--fuzzy=92 : Mevcut program dedup fuzzy eşik (%)}
    {--report=storage/app/hk-import-report.txt : Üni bulunamayan satırlar buraya}
    {--audit-uni= : Sadece HK üni → DB üni eşleşmelerini bu dosyaya yazıp çık}')]
#[Description('Hochschulkompass tam kataloğunu DB ile birleştir: eşleşeni güncelle, eksiği yeni program olarak ekle.')]
class ImportHkCatalog extends Command
{
    private array $uniTokens = [];
    private array $keyIndex = [];

    /** Az-kayıplı normalize varyantları (tam + dash-öncesi + virgül-öncesi) */
    private function uniKeys(string $s): array
    {
        $base = mb_strtolower($s, 'UTF-8');
        $base = strtr($base, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        $base = preg_replace('/\(.*?\)/u', ' ', $base);                    // parantez
        // legal/boilerplate
        $base = preg_replace('/,?\s*(staatlich anerkannte?.*$|gemeinnuetzige.*$|g?gmbh.*$|in traegerschaft.*$|hochschule der.*stiftung.*$)/u', ' ', $base);
        $base = trim(preg_replace('/\s+/', ' ', $base));
        $variants = [$base];
        if (($p = mb_strpos($base, ' - ')) !== false) $variants[] = mb_substr($base, 0, $p);
        if (($p = mb_strpos($base, ' – ')) !== false) $variants[] = mb_substr($base, 0, $p);
        if (($c = mb_strpos($base, ',')) !== false) $variants[] = mb_substr($base, 0, $c);
        $out = [];
        foreach ($variants as $v) {
            $k = preg_replace('/[^a-z0-9]+/', '', $v);
            if (strlen($k) >= 5) $out[$k] = true;
        }
        return array_keys($out);
    }

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $updateOnly = (bool) $this->option('update-only');
        $fuzzy = (int) $this->option('fuzzy');

        // 1) HK satırlarını yükle (tüm modlar)
        $files = glob(base_path($this->option('glob')));
        if (! $files) { $this->error('HK dosyası yok: ' . $this->option('glob')); return self::FAILURE; }
        $rows = [];
        foreach ($files as $f) {
            $data = json_decode(file_get_contents($f), true) ?: [];
            $this->line('  ' . basename($f) . ': ' . count($data));
            foreach ($data as $r) $rows[] = $r;
        }
        $this->info('Toplam HK satır: ' . count($rows));
        if ($dry) $this->warn('⚠️  DRY-RUN: DB değişmeyecek');

        // 2) Üni index
        $uniIndex = [];
        foreach (University::query()->with('city')->get(['id', 'name_de', 'name_en', 'short_name', 'city_id']) as $u) {
            $tokAll = [];
            foreach ([$u->name_de, $u->name_en, $u->short_name] as $cand) {
                if (! $cand) continue;
                $k = $this->normUni($cand);
                if ($k !== '') $uniIndex[$k] ??= $u->id;
                foreach ($this->tokens($cand) as $t) $tokAll[$t] = true;
            }
            $this->uniTokens[$u->id] = [
                'tok' => array_keys($tokAll),
                'city' => $this->cityToken(optional($u->city)->name_de ?: optional($u->city)->name_tr ?: ''),
                'type' => $this->uniType(($u->name_de ?: '') . ' ' . ($u->name_en ?: '')),
            ];
            foreach ([$u->name_de, $u->name_en, $u->short_name] as $cand) {
                if (! $cand) continue;
                foreach ($this->uniKeys($cand) as $key) {
                    if ($key !== '') $this->keyIndex[$key][$u->id] = $u->id;
                }
            }
        }

        // (opsiyonel) üni eşleşme denetimi — çözülmeyenler için aynı-şehir adaylarını öner
        if ($auditPath = $this->option('audit-uni')) {
            $allUnis = University::query()->with('city')->get(['id', 'name_de', 'city_id']);
            $uniById = $allUnis->pluck('name_de', 'id');
            $distinct = [];
            foreach ($rows as $r) { $distinct[$r['hochschule'] ?? ''] = $r['ort'] ?? ''; }
            $lines = [];
            foreach ($distinct as $hk => $ort) {
                if ($hk === '') continue;
                $id = $this->resolveUni($hk, $uniIndex, $ort);
                if ($id) {
                    $lines[] = "✓\t{$hk}\t→\t" . ($uniById[$id] ?? $id) . "\t(id:{$id})";
                } else {
                    // aynı şehirdeki adayları öner
                    $cityTok = $this->cityToken($ort);
                    $cands = [];
                    foreach ($allUnis as $u) {
                        if ($this->cityToken(optional($u->city)->name_de ?: '') === $cityTok && $cityTok !== '') {
                            $cands[] = $u->name_de . " (id:{$u->id})";
                        }
                    }
                    $lines[] = "✗\t{$hk}\t[{$ort}]\tADAYLAR: " . (implode(' | ', array_slice($cands, 0, 6)) ?: 'yok');
                }
            }
            sort($lines);
            file_put_contents(base_path($auditPath), implode("\n", $lines));
            $this->info(count($lines) . ' farklı HK üni → ' . base_path($auditPath));
            return self::SUCCESS;
        }

        // 2b) Eksik üniversiteleri oluştur (tam katalog)
        if ($this->option('create-unis')) {
            $created = $this->createMissingUnis($rows, $uniIndex, $dry);
            $this->info(($dry ? '[dry] ' : '') . "Oluşturulan/oluşturulacak üni: {$created}");
        }

        // 3) Mevcut program index: uni_id => [normName => true]
        $progByUni = [];
        Program::query()->select(['university_id', 'name_de', 'name_en', 'name_tr', 'degree'])
            ->chunk(2000, function ($chunk) use (&$progByUni) {
                foreach ($chunk as $p) {
                    if (! $p->university_id) continue;
                    foreach (array_filter([$p->name_de, $p->name_en, $p->name_tr]) as $n) {
                        $progByUni[$p->university_id][$this->normProg($n)] = true;
                    }
                }
            });

        $stats = ['update' => 0, 'create' => 0, 'uni_miss' => 0, 'dup_skip' => 0];
        $uniMissNames = [];
        $newProgs = [];
        $updateSlugs = []; // [uni_id|normName] zaten işlendi mi (mod çakışması önlemek)
        $createdKeys = [];

        $bar = $this->output->createProgressBar(count($rows));
        foreach ($rows as $r) {
            $bar->advance();
            $fach = trim($r['fach'] ?? '');
            if ($fach === '') continue;
            $uniId = $this->resolveUni($r['hochschule'] ?? '', $uniIndex, $r['ort'] ?? '');
            if (! $uniId) {
                $stats['uni_miss']++;
                $uniMissNames[$r['hochschule'] ?? '?'] = ($uniMissNames[$r['hochschule'] ?? '?'] ?? 0) + 1;
                continue;
            }
            $norm = $this->normProg($fach);
            $mode = $r['admission_mode'] ?? 'zulassungsfrei';

            // mevcut mu? (exact normName veya fuzzy)
            $exists = isset($progByUni[$uniId][$norm]);
            if (! $exists && strlen($norm) >= 8) {
                foreach (array_keys($progByUni[$uniId] ?? []) as $dn) {
                    if (strlen($dn) < 8) continue;
                    similar_text($norm, $dn, $pct);
                    if ($pct >= $fuzzy) { $exists = true; break; }
                }
            }

            if ($exists) {
                $key = $uniId . '|' . $norm;
                if (isset($updateSlugs[$key])) { continue; }
                $updateSlugs[$key] = $mode;
                $stats['update']++;
            } else {
                if ($updateOnly) continue;
                $key = $uniId . '|' . $norm;
                if (isset($createdKeys[$key])) { $stats['dup_skip']++; continue; }
                $createdKeys[$key] = true;
                $newProgs[] = ['uni' => $uniId, 'fach' => $fach, 'norm' => $norm, 'r' => $r, 'mode' => $mode];
                $stats['create']++;
            }
        }
        $bar->finish();
        $this->newLine(2);

        // 4) Uygula
        if (! $dry) {
            $this->applyUpdates($updateSlugs);   // mevcutların admission_mode'unu güncelle
            $this->applyCreates($newProgs);      // eksik programları oluştur
        }

        $this->info('📊 Import özeti');
        $this->line("├── Güncellenecek mevcut program: {$stats['update']}");
        $this->line("├── Yeni eklenecek program:        {$stats['create']}");
        $this->line("├── Üni bulunamadı (atlandı):      {$stats['uni_miss']}");
        $this->line("└── Dup atlanan:                   {$stats['dup_skip']}");

        if ($report = $this->option('report')) {
            arsort($uniMissNames);
            $lines = [];
            foreach ($uniMissNames as $n => $c) $lines[] = "{$c}\t{$n}";
            file_put_contents(base_path($report), implode("\n", $lines));
            $this->line("Üni-miss raporu: {$report} (" . count($uniMissNames) . " farklı üni)");
        }

        return self::SUCCESS;
    }

    private function applyUpdates(array $updateSlugs): void
    {
        $bar = $this->output->createProgressBar(count($updateSlugs));
        foreach ($updateSlugs as $key => $mode) {
            [$uniId, $norm] = explode('|', $key, 2);
            // bu üninin programlarını çek, normProg eşleşeni güncelle
            Program::where('university_id', $uniId)->get(['id', 'name_de', 'name_en', 'name_tr', 'admission_mode'])
                ->each(function ($p) use ($norm, $mode) {
                    foreach (array_filter([$p->name_de, $p->name_en, $p->name_tr]) as $n) {
                        if ($this->normProg($n) === $norm) {
                            if ($p->admission_mode !== $mode) {
                                $p->admission_mode = $mode;
                                $p->saveQuietly();
                            }
                            return false;
                        }
                    }
                });
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    private function applyCreates(array $newProgs): void
    {
        $bar = $this->output->createProgressBar(count($newProgs));
        foreach ($newProgs as $np) {
            $r = $np['r'];
            $degree = $this->mapDegree($r['abschluss'] ?? '', $r['typ'] ?? '');
            $base = Str::slug(Str::limit($np['fach'], 120, '') . '-' . ($degree ?: 'prog') . '-hk');
            $slug = substr($base, 0, 180);
            $i = 0;
            while (Program::where('slug', $slug)->exists()) {
                $slug = substr($base, 0, 170) . '-' . substr(md5($np['uni'] . $np['fach'] . $i), 0, 6);
                $i++;
            }
            Program::create([
                'university_id' => $np['uni'],
                'name_de' => $np['fach'],
                'name_en' => null,
                'name_tr' => null,
                'slug' => $slug,
                'degree' => $degree ?: 'other',
                'admission_mode' => $np['mode'],
                'study_form' => $this->mapForm($r['form'] ?? ''),
                'location' => $r['ort'] ?? null,
                'source' => 'hochschulkompass',
                'source_id' => substr(md5(($r['hochschule'] ?? '') . '|' . $np['fach'] . '|' . ($r['abschluss'] ?? '')), 0, 32),
                'is_active' => true,
                'last_synced_at' => now(),
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
    }

    /**
     * Yüksek-isabet üni çözümü.
     * Kural: (1) exact normName; (2) tip-uyumu ZORUNLU (Uni≠FH≠özel);
     * (3) en az 1 ayırt edici (uzun, jenerik-olmayan) token ortak; şehir yalnız eşitlik bozucu.
     * Belirsizse null döner (yanlış eşleşmektense atla).
     */
    /** DB'de eşleşmeyen distinct HK ünilerini oluştur, resolver cache'lerine ekle */
    private function createMissingUnis(array $rows, array &$uniIndex, bool $dry): int
    {
        // şehir index
        $cityIndex = [];
        foreach (\App\Models\City::query()->get(['id', 'name_de', 'name_tr']) as $c) {
            foreach ([$c->name_de, $c->name_tr] as $n) {
                if ($n) $cityIndex[$this->cityToken($n)] = $c->id;
            }
        }
        // distinct çözülemeyen üniler
        $missing = [];
        foreach ($rows as $r) {
            $hk = trim($r['hochschule'] ?? '');
            if ($hk === '') continue;
            if ($this->resolveUni($hk, $uniIndex, $r['ort'] ?? '')) continue;
            $missing[$hk] ??= $r['ort'] ?? '';
        }
        $count = 0;
        foreach ($missing as $hk => $ort) {
            $count++;
            if ($dry) continue;
            // şehir çöz
            $firstCity = trim(explode(',', $ort)[0] ?? '');
            $cityId = $cityIndex[$this->cityToken($firstCity)] ?? null;
            $type = $this->uniDbType($hk);
            $base = \Illuminate\Support\Str::slug(\Illuminate\Support\Str::limit($hk, 90, '')) . '-hk';
            $slug = substr($base, 0, 185);
            $i = 0;
            while (University::where('slug', $slug)->exists()) { $slug = substr($base, 0, 176) . '-' . substr(md5($hk . $i++), 0, 6); }
            $u = University::create([
                'name_de' => $hk, 'name_tr' => $hk, 'name_en' => null,
                'slug' => $slug, 'type' => $type, 'city_id' => $cityId,
                'data_source' => 'hochschulkompass', 'is_active' => true, 'last_synced_at' => now(),
            ]);
            // cache'lere ekle ki programları bu üniye bağlansın
            $this->uniTokens[$u->id] = [
                'tok' => $this->tokens($hk), 'type' => $this->uniType($hk),
                'city' => $cityId ? $this->cityToken($firstCity) : '',
            ];
            foreach ($this->uniKeys($hk) as $k) $this->keyIndex[$k][$u->id] = $u->id;
            $this->createdUniCache[$this->aliasKey($hk)] = $u->id;
        }
        return $count;
    }

    /** HK adından DB type enum: public|applied_sciences|art|private|religion */
    private function uniDbType(string $name): string
    {
        $t = mb_strtolower($name, 'UTF-8');
        if (str_contains($t, 'theologische') || str_contains($t, 'kirchenmusik') || str_contains($t, 'kirchlich')) return 'religion';
        $art = $this->uniType($name) === 'art';
        if ($art) return 'art';
        if (preg_match('/\b(gmbh|privat|staatlich anerkannte?)\b/u', $t)) return 'private';
        if ($this->uniType($name) === 'uni') return 'public';
        return 'applied_sciences';
    }

    private array $createdUniCache = [];

    private function resolveUni(string $name, array $uniIndex, string $ort = ''): ?int
    {
        // bu koşuda oluşturulmuş üni
        if ($cid = $this->createdUniCache[$this->aliasKey($name)] ?? null) return $cid;

        // kürate harita (en yüksek öncelik)
        if ($aid = $this->aliasUniId($name)) return $aid;

        // alias (kısaltma adlı üniler)
        $aliasNorm = $this->aliasNorm($name);
        if ($aliasNorm && isset($uniIndex[$aliasNorm])) return $uniIndex[$aliasNorm];

        // NOT: lossy exact-normName index'i bilerek kullanmıyoruz (Uni/FH Potsdam,
        // virgülle kesilen şehir gibi collision'lar). Tip-uyumlu token skorlayıcı karar verir.
        $hkType = $this->uniType($name);

        // az-kayıplı exact match (tip-uyumlu, tek aday ise): birebir adı olanları kurtarır
        foreach ($this->uniKeys($name) as $hkKey) {
            if ($hkKey === '' || ! isset($this->keyIndex[$hkKey])) continue;
            $cands = array_values(array_filter($this->keyIndex[$hkKey], fn ($id) => $this->uniTokens[$id]['type'] === $hkType));
            if (count($cands) === 1) return $cands[0];
        }

        $hkTok = $this->tokens($name);
        if (! $hkTok) return null;
        // ayırt edici (uzun) HK token'ları — şehir/tip dışı kimlik
        $hkLong = array_values(array_filter($hkTok, fn ($t) => strlen($t) >= 5));
        if (! $hkLong) return null;
        $hkCity = $this->cityToken($ort);

        $bestId = null; $bestScore = 0.0; $second = 0.0;
        foreach ($this->uniTokens as $id => $info) {
            if (! $info['tok']) continue;
            if ($info['type'] !== $hkType) continue;            // tip uyumu zorunlu
            $interLong = array_intersect($hkLong, $info['tok']);
            if (! $interLong) continue;                          // ayırt edici token ZORUNLU
            $inter = count(array_intersect($hkTok, $info['tok']));
            $union = count(array_unique(array_merge($hkTok, $info['tok'])));
            $jac = $inter / max(1, $union);
            $cityOk = ($hkCity && $info['city'] && $hkCity === $info['city']);
            // skor: ayırt edici örtüşme oranı + jaccard + (şehir eşitlik bozucu)
            $longRatio = count($interLong) / max(1, count($hkLong));
            $score = $longRatio * 0.7 + $jac * 0.3 + ($cityOk ? 0.05 : 0);
            if ($score > $bestScore) { $second = $bestScore; $bestScore = $score; $bestId = $id; }
            elseif ($score > $second) { $second = $score; }
        }
        // güçlü ve tek aday: kabul. Yakın iki aday varsa (belirsiz) atla.
        if ($bestScore >= 0.6 && ($bestScore - $second) >= 0.15) return $bestId;
        return null;
    }

    /** Kurum tipi: uni | fh | art | other (özel/kilise FH'ler de fh sayılır) */
    private function uniType(string $s): string
    {
        $t = mb_strtolower($s, 'UTF-8');
        if (preg_match('/\b(akademie|kunsthochschule|musikhochschule|hochschule für (musik|bildende|kunst|gestaltung|film))\b/u', $t)) return 'art';
        if (preg_match('/(^|\b)(universit[aä]t|university|technische universit|tu |^tu$|rwth)\b/u', $t) && ! str_contains($t, 'fachhochschule')) {
            // "Universität ..." gerçek üni; ama "... University of Applied Sciences" FH'dir
            if (str_contains($t, 'applied sciences') || str_contains($t, 'angewandte wissenschaft')) return 'fh';
            return 'uni';
        }
        if (str_contains($t, 'rwth')) return 'uni';
        return 'fh'; // Hochschule / Fachhochschule / FH / private Hochschule
    }

    private ?array $aliasMap = null;

    /** data/hk-uni-aliases.json: alnum-normalize edilmiş HK adı => DB uni id */
    private function aliasUniId(string $name): ?int
    {
        if ($this->aliasMap === null) {
            $this->aliasMap = [];
            $path = base_path('data/hk-uni-aliases.json');
            if (is_file($path)) {
                foreach (json_decode(file_get_contents($path), true) ?: [] as $hk => $id) {
                    if (! is_int($id)) continue;
                    $this->aliasMap[$this->aliasKey($hk)] = $id;
                }
            }
        }
        return $this->aliasMap[$this->aliasKey($name)] ?? null;
    }

    private function aliasKey(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $s = strtr($s, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        return preg_replace('/[^a-z0-9]+/', '', $s);
    }

    /** RWTH gibi kısaltma adlılar için sabit köprü */
    private function aliasNorm(string $name): ?string
    {
        $t = mb_strtolower($name, 'UTF-8');
        if (str_contains($t, 'rheinisch-westfälische technische hochschule aachen') || str_contains($t, 'rwth aachen')) {
            return $this->normUni('rwthaachen');
        }
        return null;
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

    private function cityToken(string $s): string
    {
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = strtr($s, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        $s = preg_replace('/\s*\(.*?\)\s*/', '', $s);
        return preg_replace('/[^a-z]/', '', $s);
    }

    private function mapDegree(string $abschluss, string $typ): ?string
    {
        $a = mb_strtolower($abschluss . ' ' . $typ);
        if (str_contains($a, 'bachelor') || str_contains($a, 'b.sc') || str_contains($a, 'b.a') || str_contains($a, 'b.eng') || str_contains($a, 'grundständig')) return 'bachelor';
        if (str_contains($a, 'master') || str_contains($a, 'm.sc') || str_contains($a, 'm.a') || str_contains($a, 'm.eng') || str_contains($a, 'weiterführend')) return 'master';
        if (str_contains($a, 'staatsexamen') || str_contains($a, 'staatsprüfung')) return 'other';
        return null;
    }

    private function mapForm(string $form): ?string
    {
        $f = mb_strtolower($form);
        if (str_contains($f, 'fernstudium')) return 'online';
        if (str_contains($f, 'berufsbegleitend')) return 'part_time';
        if (str_contains($f, 'teilzeit')) return 'part_time';
        if (str_contains($f, 'vollzeit')) return 'full_time';
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

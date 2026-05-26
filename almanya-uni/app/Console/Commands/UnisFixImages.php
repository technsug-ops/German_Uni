<?php

namespace App\Console\Commands;

use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UnisFixImages extends Command
{
    protected $signature = 'unis:fix-images
        {--dry-run : Sadece öneri tablosu, kaydetme}
        {--id= : Tek bir üni id\'si}
        {--all : Tüm aktif üniler (sadece şüpheli olanlar değil)}
        {--low-confidence : Düşük güvenli (Commons fallback) önerileri de uygula}
        {--limit=0 : İlk N üni (0 = hepsi)}';

    protected $description = 'Şüpheli üni kapak görsellerini Wikidata P18 (ana bina) ile değiştir, fallback Commons araması';

    private const UA = 'AlmanyaUni/1.0 (https://www.almanyauni.com; hello@almanyauni.com)';

    /** image_url'in foto değil logo/arma/yanlış olduğunu gösteren ipuçları */
    private const BAD_HINTS = [
        'logo', 'Logo', 'Wappen', 'Siegel', 'seal', 'Gedenktafel', 'Skulptur', 'Statue',
        'Anzeige', 'Briefmarke', 'stamp', 'Münze', 'coin', 'Medaille', 'Plakette',
        'Portrait', 'Porträt', 'Ansichtskarten', 'Zeno', 'Bundesarchiv',
        '_178', '_179', '_180', '_181', '_182', '_183', '_184', '_185', '_186', '_187', '_188', '_189', '_190',
    ];

    /** Sonuç dosya adında istenmeyen kelimeler (logo/arma/harita vb.) */
    private const REJECT = [
        'logo', 'wappen', 'siegel', 'seal', 'gedenktafel', 'skulptur', 'statue', 'portrait',
        'porträt', 'briefmarke', 'stamp', 'münze', 'coin', 'medaille', 'plakette', 'karte',
        'map', 'plan', 'grundriss', 'organigramm', 'diagramm', 'ansichtskarten', 'zeno',
    ];

    /** Bina fotoğrafına işaret eden kelimeler (Commons fallback puanı) */
    private const BUILDING_HINTS = [
        'hauptgebäude', 'hauptgebaeude', 'gebäude', 'gebaeude', 'campus', 'building',
        'hochschule', 'universität', 'universitat', 'university', 'fakultät', 'institut', 'bibliothek',
    ];

    /** İsim token'ından çıkarılacak jenerik kelimeler */
    private const STOP = [
        'universität', 'universitat', 'university', 'hochschule', 'für', 'fur', 'und', 'der', 'die',
        'das', 'von', 'zu', 'am', 'of', 'the', 'and', 'applied', 'sciences', 'staatlich', 'anerkannte',
        'technische', 'technical', 'private', 'gmbh', 'fachhochschule', 'akademie', 'institut', 'institute',
        'school', 'college', 'management', 'business',
    ];

    public function handle(): int
    {
        $query = University::query()->where('is_active', 1);

        if ($id = $this->option('id')) {
            $query->where('id', (int) $id);
        } elseif (! $this->option('all')) {
            $query->whereNotNull('image_url')->where('image_url', '!=', '')
                ->where(function ($q) {
                    foreach (self::BAD_HINTS as $h) {
                        $q->orWhere('image_url', 'like', '%' . $h . '%');
                    }
                });
        }

        $query->orderByDesc('student_count');
        if ($this->option('limit') > 0) {
            $query->limit((int) $this->option('limit'));
        }

        $unis = $query->get(['id', 'name_de', 'short_name', 'wikidata_id', 'student_count', 'image_url']);
        $total = $unis->count();
        if ($total === 0) {
            $this->info('İşlenecek üni yok.');
            return self::SUCCESS;
        }

        $dry = $this->option('dry-run');
        $applyLow = $this->option('low-confidence');
        $this->info(($dry ? '[DRY-RUN] ' : '') . "{$total} üni işleniyor...");
        $this->newLine();

        $set = 0;
        $skip = 0;
        $rows = [];

        foreach ($unis as $u) {
            [$url, $conf] = $this->resolveImage($u);
            $oldFn = $u->image_url ? urldecode(basename(parse_url($u->image_url, PHP_URL_PATH))) : '—';

            if (! $url) {
                $skip++;
                $rows[] = [$u->id, mb_substr($u->name_de, 0, 32), mb_substr($oldFn, 0, 26), '⚠️ YOK', ''];
                continue;
            }

            $newFn = urldecode(basename(parse_url($url, PHP_URL_PATH)));
            $badge = $conf === 'P18' ? '✅ P18' : '🔶 ara';
            $willApply = ! $dry && ($conf === 'P18' || $applyLow);
            $rows[] = [$u->id, mb_substr($u->name_de, 0, 32), mb_substr($oldFn, 0, 26), mb_substr($newFn, 0, 30), $badge . ($willApply ? '' : ($dry ? '' : ' (atlandı)'))];

            if ($willApply) {
                University::withoutSyncingToSearch(function () use ($u, $url) {
                    University::whereKey($u->id)->update(['image_url' => $url]);
                });
                $set++;
            }
        }

        $this->table(['id', 'Üni', 'Eski', $dry ? 'ÖNERİ' : 'Yeni', 'Kaynak'], $rows);
        $this->newLine();
        if ($dry) {
            $this->info("[DRY-RUN] {$total} öneri. ✅ P18 = güvenilir, 🔶 ara = Commons (kontrol gerek). Uygula: --dry-run kaldır (sadece P18'i uygular; Commons için --low-confidence ekle).");
        } else {
            $this->info("✅ {$set} güncellendi, " . ($total - $set) . " atlandı/bulunamadı.");
        }

        return self::SUCCESS;
    }

    /** @return array{0: ?string, 1: ?string} [url, confidence(P18|commons|null)] */
    private function resolveImage(University $u): array
    {
        // 1) Wikidata P18 — entity'ye bağlı otoriter görsel
        if ($u->wikidata_id) {
            $file = $this->wikidataP18($u->wikidata_id);
            if ($file) {
                $low = mb_strtolower($file);
                $bad = false;
                foreach (self::REJECT as $kw) {
                    if (str_contains($low, $kw)) {
                        $bad = true;
                        break;
                    }
                }
                if (! $bad) {
                    $thumb = 'https://commons.wikimedia.org/wiki/Special:FilePath/'
                        . rawurlencode($file) . '?width=960';
                    return [$thumb, 'P18'];
                }
            }
        }

        // 2) Commons araması — güven kapısı: isim token'ı dosya adında olmalı
        $commons = $this->commonsBuilding($u->name_de);
        return $commons ? [$commons, 'commons'] : [null, null];
    }

    private function wikidataP18(string $qid): ?string
    {
        try {
            $r = Http::timeout(15)->withHeaders(['User-Agent' => self::UA])
                ->get('https://www.wikidata.org/w/api.php', [
                    'action' => 'wbgetclaims', 'format' => 'json',
                    'entity' => $qid, 'property' => 'P18',
                ]);
            $claims = $r->json('claims.P18');
            if (! $claims) {
                return null;
            }
            return $claims[0]['mainsnak']['datavalue']['value'] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function commonsBuilding(string $name): ?string
    {
        $clean = trim(preg_replace('/\s*[-–]\s*(University of Applied Sciences|University).*/i', '', $name));
        $tokens = $this->distinctiveTokens($clean);

        foreach ([$clean . ' Hauptgebäude', $clean . ' Campus', $clean] as $term) {
            $hit = $this->searchCommons($term, $tokens);
            if ($hit) {
                return $hit;
            }
        }
        return null;
    }

    /** İsimden jenerik olmayan ayırt edici kelimeler (>=4 harf). */
    private function distinctiveTokens(string $name): array
    {
        $words = preg_split('/[\s,\-–\/]+/u', mb_strtolower($name));
        $out = [];
        foreach ($words as $w) {
            $w = trim($w);
            if (mb_strlen($w) >= 4 && ! in_array($w, self::STOP, true)) {
                $out[] = $w;
            }
        }
        return $out;
    }

    private function searchCommons(string $term, array $tokens): ?string
    {
        try {
            $resp = Http::timeout(15)->withHeaders(['User-Agent' => self::UA])
                ->get('https://commons.wikimedia.org/w/api.php', [
                    'action' => 'query', 'format' => 'json', 'generator' => 'search',
                    'gsrsearch' => $term, 'gsrnamespace' => 6, 'gsrlimit' => 20,
                    'prop' => 'imageinfo', 'iiprop' => 'url|size', 'iiurlwidth' => 960,
                ]);
            $pages = $resp->json('query.pages');
            if (! $pages) {
                return null;
            }

            $scored = [];
            foreach ($pages as $p) {
                $title = $p['title'] ?? '';
                $low = mb_strtolower($title);
                if (! preg_match('/\.(jpg|jpeg)$/i', $title)) {
                    continue;
                }
                $w = $p['imageinfo'][0]['width'] ?? 0;
                $h = $p['imageinfo'][0]['height'] ?? 0;
                if ($w <= $h || $w < 800) {
                    continue;
                }
                foreach (self::REJECT as $kw) {
                    if (str_contains($low, $kw)) {
                        continue 2;
                    }
                }
                // Güven kapısı: ayırt edici token dosya adında olmalı
                $tokenMatch = false;
                foreach ($tokens as $t) {
                    if (str_contains($low, $t)) {
                        $tokenMatch = true;
                        break;
                    }
                }
                if (! $tokenMatch) {
                    continue;
                }
                $score = $w / max($h, 1);
                foreach (self::BUILDING_HINTS as $kw) {
                    if (str_contains($low, $kw)) {
                        $score += 2;
                    }
                }
                $thumb = $p['imageinfo'][0]['thumburl'] ?? null;
                if ($thumb) {
                    $scored[] = ['score' => $score, 'url' => $thumb];
                }
            }
            if (empty($scored)) {
                return null;
            }
            usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);
            return $scored[0]['url'];
        } catch (\Throwable $e) {
            return null;
        }
    }
}

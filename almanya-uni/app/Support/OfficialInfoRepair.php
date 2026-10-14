<?php

namespace App\Support;

use App\Models\Faq;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Resmî kaynağa dayalı hedefli içerik onarımı (OSV-3).
 *
 * Paket JSON'u (database/data/osv/*.json) kayıtları id ile değil `slug + locale` ile bulur (prod/yerel id'leri farklı).
 * Her düzenleme yalnız ilgili metin parçasını değiştirir:
 *  - replace       : `find` alanda TAM BİR KEZ geçmeli → `replace` ile değişir.
 *  - insert_after  : `find` (çapa) tam bir kez geçmeli → çapanın hemen arkasına `text` eklenir.
 *  - prepend       : `text` alanın başına eklenir. insert_after/prepend'de `marker` metinde zaten varsa → already.
 *  - set           : alanın tamamı değişir; yalnız mevcut metnin sha256'sı `expect_sha256` ise (beklenmeyen içerik ezilmez).
 * Durumlar: apply | already (zaten uygulanmış → no-op) | mismatch (beklenen metin yok → atlanır, ezilmez)
 *           | ambiguous (find birden çok kez) | missing (kayıt yok). Yalnız `apply` yazılır; ikinci çalıştırma no-op'tur.
 *
 * `html` bölümü kayıtlı HTML'deki (ör. sözlük tooltip'i) sabit bir metni olduğu gibi değiştirir; model kaydedilmez,
 * yalnız o alt dize değişir.
 *
 * Post/Faq model `saving` kancası content_html / answer_html'i Markdown'dan yeniden üretir. Bu modellerde webhook,
 * kuyruk veya bildirim tetikleyen gözlemci yoktur.
 */
class OfficialInfoRepair
{
    private const MODELS = ['posts' => Post::class, 'faqs' => Faq::class];

    private const FIELDS = [
        'posts' => ['title', 'meta_title', 'meta_description', 'excerpt', 'content_md'],
        'faqs' => ['question', 'answer_md'],
    ];

    private const HTML_FIELDS = ['posts' => ['content_html'], 'faqs' => ['answer_html']];

    public static function load(string $path): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("OSV paketi bulunamadı: $path");
        }
        $pkg = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        foreach (['package', 'checked_at', 'edits'] as $k) {
            if (! array_key_exists($k, $pkg)) {
                throw new RuntimeException("OSV paketi eksik alan: $k");
            }
        }
        $keys = [];
        foreach ($pkg['edits'] as $e) {
            foreach (['key', 'table', 'slug', 'locale', 'field', 'op'] as $k) {
                if (empty($e[$k])) {
                    throw new RuntimeException('OSV düzenlemesi eksik alan: ' . $k . ' (' . ($e['key'] ?? '?') . ')');
                }
            }
            if (! in_array($e['field'], self::FIELDS[$e['table']] ?? [], true)) {
                throw new RuntimeException("İzin verilmeyen alan: {$e['table']}.{$e['field']} ({$e['key']})");
            }
            if (in_array($e['op'], ['insert_after', 'prepend'], true) && (empty($e['marker']) || ! str_contains($e['text'] ?? '', $e['marker']))) {
                throw new RuntimeException("insert/prepend işaretçisi metinde yok: {$e['key']}");
            }
            if (! in_array($e['op'], ['replace', 'insert_after', 'prepend', 'set'], true)) {
                throw new RuntimeException("Bilinmeyen işlem: {$e['op']} ({$e['key']})");
            }
            if (isset($keys[$e['key']])) {
                throw new RuntimeException("Yinelenen anahtar: {$e['key']}");
            }
            $keys[$e['key']] = true;
        }
        foreach ($pkg['html'] ?? [] as $h) {
            if (! in_array($h['field'] ?? '', self::HTML_FIELDS[$h['table'] ?? ''] ?? [], true) || empty($h['find']) || ! isset($h['replace'])) {
                throw new RuntimeException('Geçersiz html düzenlemesi: ' . ($h['key'] ?? '?'));
            }
        }

        return $pkg;
    }

    /** Salt okunur önizleme: hiçbir şey yazmaz. */
    public static function plan(array $pkg): array
    {
        $items = [];
        $records = [];   // "table|slug|locale" => ['id','group','fields'=>[field=>new value]]
        $working = [];   // aynı alandaki ardışık düzenlemeler için çalışma kopyası

        foreach ($pkg['edits'] as $e) {
            $rk = "{$e['table']}|{$e['slug']}|{$e['locale']}";
            if (! array_key_exists($rk, $records)) {
                $row = DB::table($e['table'])->where('slug', $e['slug'])->where('locale', $e['locale'])->first();
                $records[$rk] = $row ? ['id' => $row->id, 'group' => $row->translation_group_id ?? null, 'row' => (array) $row, 'fields' => []] : null;
            }
            $rec = $records[$rk];
            $item = ['key' => $e['key'], 'topic' => $e['topic'] ?? '', 'table' => $e['table'], 'slug' => $e['slug'],
                'locale' => $e['locale'], 'field' => $e['field'], 'op' => $e['op'], 'id' => $rec['id'] ?? null, 'group' => $rec['group'] ?? null];
            if ($rec === null) {
                $items[] = $item + ['status' => 'missing'];
                continue;
            }
            $wk = "$rk|{$e['field']}";
            $current = $working[$wk] ?? (string) ($rec['row'][$e['field']] ?? '');
            [$status, $new] = self::evaluate($e, $current);
            if ($status === 'apply') {
                $working[$wk] = $new;
                $records[$rk]['fields'][$e['field']] = $new;
            }
            $items[] = $item + ['status' => $status];
        }

        $html = [];
        foreach ($pkg['html'] ?? [] as $h) {
            // LIKE harmanlaması büyük/küçük harf duyarsız olabilir → kesin eşleşme PHP'de süzülür.
            $ids = DB::table($h['table'])->where($h['field'], 'like', '%' . self::like($h['find']) . '%')->get(['id', $h['field']])
                ->filter(fn ($r) => str_contains((string) $r->{$h['field']}, $h['find']))->pluck('id')->values()->all();
            $done = DB::table($h['table'])->where($h['field'], 'like', '%' . self::like($h['replace']) . '%')->count();
            $html[] = ['key' => $h['key'], 'table' => $h['table'], 'field' => $h['field'], 'ids' => $ids,
                'status' => $ids ? 'apply' : ($done ? 'already' : 'none'), 'find' => $h['find'], 'replace' => $h['replace']];
        }

        $counts = array_count_values(array_column($items, 'status'));

        return ['package' => $pkg['package'], 'items' => $items, 'html' => $html, 'counts' => $counts,
            'writes' => array_filter(array_map(fn ($r) => $r && $r['fields'] ? ['id' => $r['id'], 'fields' => $r['fields']] : null, $records))];
    }

    /** @return array{0:string,1:?string} */
    public static function evaluate(array $e, string $current): array
    {
        switch ($e['op']) {
            case 'replace':
                $n = substr_count($current, $e['find']);
                if ($n === 1) {
                    return ['apply', str_replace($e['find'], $e['replace'], $current)];
                }
                if ($n > 1) {
                    return ['ambiguous', null];
                }

                return [str_contains($current, $e['replace']) ? 'already' : 'mismatch', null];
            case 'insert_after':
                if (str_contains($current, $e['marker'])) {
                    return ['already', null];
                }
                $n = substr_count($current, $e['find']);
                if ($n === 1) {
                    return ['apply', str_replace($e['find'], $e['find'] . $e['text'], $current)];
                }

                return [$n > 1 ? 'ambiguous' : 'mismatch', null];
            case 'prepend':
                if (str_contains($current, $e['marker'])) {
                    return ['already', null];
                }

                return trim($current) === '' ? ['mismatch', null] : ['apply', $e['text'] . $current];
            case 'set':
                if (hash('sha256', $current) === hash('sha256', $e['value'])) {
                    return ['already', null];
                }

                return hash('sha256', $current) === $e['expect_sha256'] ? ['apply', $e['value']] : ['mismatch', null];
        }

        return ['mismatch', null];
    }

    /** Planı tek transaction içinde uygular; yalnız `apply` durumundaki alanlar yazılır. */
    public static function apply(array $pkg): array
    {
        return DB::transaction(function () use ($pkg) {
            $plan = self::plan($pkg);
            foreach ($plan['writes'] as $rk => $w) {
                [$table] = explode('|', $rk);
                $model = (self::MODELS[$table])::query()->findOrFail($w['id']);
                foreach ($w['fields'] as $f => $v) {
                    $model->{$f} = $v;
                }
                $model->save();
            }
            foreach ($plan['html'] as $h) {
                foreach ($h['ids'] as $id) {
                    $v = (string) DB::table($h['table'])->where('id', $id)->value($h['field']);
                    DB::table($h['table'])->where('id', $id)->update([$h['field'] => str_replace($h['find'], $h['replace'], $v)]);
                }
            }

            return $plan;
        });
    }

    public static function describe(array $plan): string
    {
        $l = ["Paket: {$plan['package']}", 'Durum sayıları: ' . json_encode($plan['counts'])];
        foreach ($plan['items'] as $it) {
            $l[] = sprintf('  %-9s %-28s %s #%s %s/%s .%s (%s)', $it['status'], $it['key'], $it['table'], $it['id'] ?? '-', $it['locale'], $it['slug'], $it['field'], $it['op']);
        }
        foreach ($plan['html'] as $h) {
            $l[] = sprintf('  %-9s %-28s %s.%s kayıt: %s', $h['status'], $h['key'], $h['table'], $h['field'], $h['ids'] ? implode(',', $h['ids']) : '-');
        }

        return implode("\n", $l) . "\n";
    }

    private static function like(string $s): string
    {
        return addcslashes($s, '%_\\');
    }
}

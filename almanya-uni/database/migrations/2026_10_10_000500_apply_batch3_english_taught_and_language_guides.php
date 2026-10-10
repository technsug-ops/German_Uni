<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SEO Growth Batch 3 — İngilizce eğitim ve dil rehberleri (veri: database/data/seo-batch3/posts.json + *.md).
 *
 * 1) İki ana rehber grubu (TR/EN/DE) baştan yazılır: İngilizce yüksek lisans rehberi (çeviri grubu d45db9b2) ve
 *    üniversite için Almanca seviyesi + dil belgeleri rehberi (çeviri grubu 62c85877). Başlık, meta, excerpt, görsel
 *    altyazısı ve içerik birlikte değişir. Olgular resmî kaynaklardan, kontrol 2026-10-10 (kaynaklar yazıların sonunda).
 * 2) İki destekleyici rehber grubunda (genel yüksek lisans, genel lisans) yalnız İngilizce eğitim cümleleri, bağlantı
 *    satırı ve yüksek lisans rehberindeki "14 eyalet" ücret cümleleri cümle düzeyinde düzeltilir.
 *
 * Production'daki bazı slug'lar lokal kopyadan farklı (dil rehberi ve lisans rehberi prod'da yeniden adlandırıldı);
 * her yazı için slug adayları verilir ve tam olarak biri bulunmalıdır.
 *
 * Güvenlik: önce tüm hedefler doğrulanır. Yazı yoksa/birden fazla aday varsa, beklenen eski başlık yoksa, düzeltilecek
 * eski cümle de yeni cümle de yoksa, sonuçta kaldırılan genelleme kalırsa ya da kolon sınırı aşılırsa RuntimeException
 * → hiçbir şey yazılmaz. Zaten uygulanmış yazı atlanır (rerun no-op, updated_at dahil). Hedeflerin hiçbirinin olmaması
 * yalnız testing ortamında (APP_ENV=testing; CI testleri dahil) "iş yok" sayılır; production'da hatadır.
 * Kayıtlar Eloquent ile kaydedilir; Post::saving content_html'i content_md'den yeniden üretir.
 */
return new class extends Migration
{
    private const DIR = 'database/data/seo-batch3';

    private const MAX_LENGTH = ['title' => 255, 'meta_title' => 255, 'excerpt' => 280, 'meta_description' => 300, 'featured_image_caption' => 255];

    public function up(): void
    {
        if (! Schema::hasTable('posts') || ! is_file(base_path(self::DIR . '/posts.json'))) {
            return;
        }
        $data = json_decode(file_get_contents(base_path(self::DIR . '/posts.json')), true, 512, JSON_THROW_ON_ERROR);

        $targets = array_merge($data['replace'], $data['fixes']);
        $any = DB::table('posts')->where(function ($q) use ($targets) {
            foreach ($targets as $t) {
                $q->orWhere(fn ($w) => $w->where('locale', $t['locale'])->whereIn('slug', $t['slugs']));
            }
        })->exists();
        if (app()->environment('testing') && ! $any) {
            return;
        }

        $plan = [];
        $problems = [];

        foreach ($data['replace'] as $r) {
            $post = $this->find($r, $problems);
            if (! $post) {
                continue;
            }
            $label = "{$r['locale']}/{$post->slug}";
            $new = [
                'title' => $r['title'],
                'meta_title' => $r['meta_title'],
                'meta_description' => $r['meta_description'],
                'excerpt' => $r['excerpt'],
                'content_md' => rtrim(file_get_contents(base_path(self::DIR . '/' . $r['file']))) . "\n",
            ];
            if ($r['featured_image_caption'] !== null) {
                $new['featured_image_caption'] = $r['featured_image_caption'];
            }

            $applied = collect($new)->every(fn ($v, $k) => (string) $post->{$k} === $v);
            if ($applied) {
                continue; // zaten uygulanmış → no-op
            }
            if (! in_array((string) $post->title, $r['old_titles'], true)) {
                $problems[] = "$label: beklenmeyen başlık (kısmen uygulanmış ya da farklı içerik)";
                continue;
            }
            $this->checkFields($label, $new, $data['leftover'], $problems);
            $post->fill($new);
            $plan[] = $post;
        }

        foreach ($data['fixes'] as $f) {
            $post = $this->find($f, $problems);
            if (! $post) {
                continue;
            }
            $label = "{$f['locale']}/{$post->slug}";
            $md = (string) $post->content_md;
            foreach ($f['rules'] as $n => [$olds, $newText]) {
                $fixed = $this->fix($md, $olds, $newText);
                if ($fixed === null) {
                    $problems[] = "$label düzeltme " . ($n + 1) . ': eski ya da yeni cümle bulunamadı';
                    continue;
                }
                $md = $fixed;
            }
            if (preg_match($data['leftover'], $md, $m)) {
                $problems[] = "$label: '{$m[0]}' kaldı";
            }
            if ($md !== (string) $post->content_md) {
                $post->content_md = $md;
                $plan[] = $post;
            }
        }

        if ($problems) {
            throw new RuntimeException('Batch 3 rehberleri güncellenmedi, hiçbir şey yazılmadı: ' . implode('; ', $problems));
        }

        DB::transaction(function () use ($plan) {
            foreach ($plan as $post) {
                $post->save();
            }
        });
    }

    /** Yazıyı locale + slug adaylarından bulur; tam olarak bir aday bulunmalı. */
    private function find(array $target, array &$problems): ?Post
    {
        $found = Post::where('locale', $target['locale'])->whereIn('slug', $target['slugs'])->get();
        if ($found->count() !== 1) {
            $problems[] = "{$target['locale']}/{$target['slugs'][0]}: " . ($found->isEmpty() ? 'bulunamadı' : 'birden fazla aday');

            return null;
        }

        return $found->first();
    }

    private function checkFields(string $label, array $fields, string $leftover, array &$problems): void
    {
        foreach ($fields as $field => $value) {
            if (mb_strlen($value) > (self::MAX_LENGTH[$field] ?? PHP_INT_MAX)) {
                $problems[] = "$label $field kolon sınırını aşıyor";
            }
            if (preg_match($leftover, $value, $m)) {
                $problems[] = "$label $field: '{$m[0]}' yeni metinde";
            }
        }
    }

    /**
     * Yeni metin zaten varsa dokunmaz; yoksa eski varyantlardan tam bir kez geçeni değiştirir; ikisi de yoksa null.
     * Eşleşmede markdown kalın işaretleri (*) göz ardı edilir, boşluklar esnektir ve satır başındaki liste işareti
     * "-", "*" ya da "+" olabilir (canlı metinden doğrulanan cümlelerin prod markdown'ı farklı işaret kullanabilir).
     */
    private function fix(string $text, array $olds, string $new): ?string
    {
        if (str_contains($text, $new)) {
            return $text;
        }
        foreach ($olds as $old) {
            $old = preg_replace('/\n- /u', "\n\x00 ", $old);
            $chars = mb_str_split(preg_replace('/[ \t]+/u', ' ', $old));
            $pattern = '/\**' . implode('\**', array_map(fn ($ch) => match ($ch) {
                ' ', "\n" => '\s+',
                "\x00" => '[-*+]',
                default => preg_quote($ch, '/'),
            }, $chars)) . '\**/u';
            if (preg_match_all($pattern, $text) === 1) {
                return preg_replace_callback($pattern, fn () => $new, $text);
            }
        }

        return null;
    }

    public function down(): void
    {
        // İçerik verisi.
    }
};

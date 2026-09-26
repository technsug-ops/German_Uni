<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Tez rehberi (TR/EN/DE): düz metin kalan [n] kaynak işaretlerini mevcut link formatına çevirir.
 *
 * Sebep: 2026_09_26_000400'deki dönüştürme, bir işaretin hemen arkasından gelen işareti ("[4][5]" ya da
 * "[8][9][13]" zincirleri) atlamıştı. Bu işaretler görünür ama tıklanamaz durumdaydı (kırık anchor yok).
 * Düzeltilen: TR 8, EN 1, DE 19 işaret. Yalnız gövdedeki (Kaynaklar/Sources/Quellen başlığından önceki)
 * düz işaretler "[\[n\]](#content-…)" biçimine çevrilir; başka metin, başlık, slug, published_at değişmez.
 *
 * ATOMİK + FAIL-FAST: önce üç dilde beklenen düz işaret dizisi birebir doğrulanır. Tamamı düzeltilmişse no-op;
 * beklenenden farklı herhangi bir durumda RuntimeException ve hiçbir dile yazılmaz. Uygulama tek transaction.
 */
return new class extends Migration
{
    private const PLAIN = '/(?<!\\\\)\[(\d{1,2})\](?!\()/';

    private const SPEC = [
        'tr' => ['slug' => 'bachelor-and-master-thesis-process-in-germany', 'heading' => '## Kaynaklar', 'anchor' => 'content-kaynaklar',
            'expected' => ['5', '20', '8', '9', '13', '13', '13', '14'], 'linked_after' => 68],
        'en' => ['slug' => 'bachelor-and-master-thesis-process-in-germany-en', 'heading' => '## Sources', 'anchor' => 'content-sources',
            'expected' => ['5'], 'linked_after' => 41],
        'de' => ['slug' => 'bachelor-and-master-thesis-process-in-germany-de', 'heading' => '## Quellen', 'anchor' => 'content-quellen',
            'expected' => ['11', '13', '15', '11', '12', '15', '20', '8', '9', '13', '13', '9', '11', '15', '14', '15', '13', '12', '14'], 'linked_after' => 70],
    ];

    public function up(): void
    {
        $posts = [];
        $fixed = 0;
        $pending = 0;
        $problems = [];

        foreach (self::SPEC as $locale => $s) {
            $post = Post::where('slug', $s['slug'])->where('locale', $locale)->first();
            if (! $post) {
                $problems[] = "{$locale}: kayıt bulunamadı";
                continue;
            }
            [$body, $sources] = $this->split((string) $post->content_md, $s['heading']);
            if ($sources === null) {
                $problems[] = "{$locale}: kaynak başlığı bulunamadı";
                continue;
            }
            preg_match_all(self::PLAIN, $body, $m);
            $plain = $m[1];
            $linked = preg_match_all('/\[\\\\\[\d{1,2}\\\\\]\]\(#'.preg_quote($s['anchor'], '/').'\)/', $body);
            $listed = preg_match_all('/^\d{1,2}\./m', $sources);

            if ($plain === [] && $linked === $s['linked_after']) {
                $fixed++;
            } elseif ($plain === $s['expected'] && $linked === $s['linked_after'] - count($s['expected'])) {
                if (max(array_map('intval', $plain)) > $listed) {
                    $problems[] = "{$locale}: kaynak listesinde olmayan numara";
                    continue;
                }
                $pending++;
                $posts[$locale] = [$post, $body, $sources];
            } else {
                $problems[] = "{$locale}: beklenmeyen durum (düz: ".count($plain).", linkli: {$linked})";
            }
        }

        if ($problems) {
            throw new RuntimeException('Tez kaynak işaretleri: ön kontrol başarısız, hiçbir dile yazılmadı. '.implode('; ', $problems));
        }
        if ($pending === 0) {
            return; // zaten düzeltilmiş — no-op
        }
        if ($fixed > 0) {
            throw new RuntimeException("Tez kaynak işaretleri: kısmen düzeltilmiş tutarsız durum ({$fixed} dil düzeltilmiş, {$pending} bekleyen), hiçbir dile yazılmadı.");
        }

        DB::transaction(function () use ($posts) {
            foreach ($posts as $locale => [$post, $body, $sources]) {
                $anchor = self::SPEC[$locale]['anchor'];
                $body = preg_replace_callback(self::PLAIN, fn ($m) => '[\\['.$m[1].'\\]](#'.$anchor.')', $body);
                $post->content_md = $body.$sources; // Post::booted() content_html'i yeniden üretir
                $post->save();
            }
        });
    }

    /** Gövdeyi kaynak başlığından önce/sonra ikiye ayırır; başlık ve sonrası aynen korunur. */
    private function split(string $md, string $heading): array
    {
        if (! preg_match('/^'.preg_quote($heading, '/').'[ \t]*\r?$/m', $md, $m, PREG_OFFSET_CAPTURE)) {
            return [$md, null];
        }
        $pos = $m[0][1];

        return [substr($md, 0, $pos), substr($md, $pos)];
    }

    public function down(): void
    {
        // Bilinçli olarak boş: yalnız link biçimi eklendi.
    }
};

<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

/**
 * 2026_09_18_000100 blogunun iç linklerini prod slug'larına düzeltir.
 *
 * NEDEN: link hedefleri LOKAL veritabanına karşı doğrulanmıştı; prod'da aynı üç yazı
 * BAŞKA slug'larla yayında (lokal kopya bayat). Sonuç: üç locale × 3 link = 9 kırık link
 * (canlıda 404). Doğru referans her zaman canlı site — bkz. memory `english-slugs-and-links`.
 *
 * Yalnızca bu yazının üç satırına dokunur; str_replace tabanlı ve idempotent (eski slug
 * kalmadıysa hiçbir şey değişmez). Locale son ekleri (-de/-en) temel slug'ı takip ettiği
 * için temel slug'ı değiştirmek üç dili birden düzeltir.
 */
return new class extends Migration
{
    /** eski (lokal) slug => yeni (prod, canlıda 200 doğrulandı) slug */
    private const SLUG_FIXES = [
        'testdaf-or-dsh-2026-german-language-exam-comparison' => 'testdaf-or-dsh-your-guide-and-tips-to-choosing-the-right',
        'what-are-anabin-h-h-h-how-is-your-turkish-diploma'   => 'what-is-anabin-h-h-h-how-is-a-turkish-diploma',
        'uni-assist-application-guide-a-z-your-step-by-step-path' => 'uni-assist-application-guide-a-z-reach-your-germany-university-dream',
    ];

    private const SLUGS = [
        'study-economics-in-german-in-germany-best-and-nc-free-universities',
        'study-economics-in-german-in-germany-best-and-nc-free-universities-de',
        'study-economics-in-german-in-germany-best-and-nc-free-universities-en',
    ];

    public function up(): void
    {
        $this->rewrite(self::SLUG_FIXES);
    }

    public function down(): void
    {
        $this->rewrite(array_flip(self::SLUG_FIXES));
    }

    private function rewrite(array $map): void
    {
        foreach (Post::whereIn('slug', self::SLUGS)->get() as $post) {
            $md = (string) $post->content_md;
            $new = str_replace(array_keys($map), array_values($map), $md);

            if ($new === $md) {
                continue;
            }

            $post->content_md = $new;
            $post->content_html = Str::markdown($new, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $post->save();
        }
    }
};

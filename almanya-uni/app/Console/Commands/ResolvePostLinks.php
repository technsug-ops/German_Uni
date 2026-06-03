<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\Content\BlogPublisher;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Mevcut yazılardaki AI iç linklerini ÇÖZÜMLER (BlogPublisher::resolveInternalLinks):
 * gerçek yayınlanmış yazıya bağlar veya hedefi yoksa düz metne indirir → 404 biter.
 * content_md + content_html güncellenir. Idempotent: değişmeyeni atlar.
 */
class ResolvePostLinks extends Command
{
    protected $signature = 'content:resolve-post-links {--dry-run} {--limit=0 : 0=hepsi}';
    protected $description = 'Yazılardaki iç linkleri gerçek yazılara çözer / hedefsizleri düz metne indirir';

    public function handle(BlogPublisher $publisher): int
    {
        $dry = (bool) $this->option('dry-run');
        $limit = (int) $this->option('limit');

        $q = Post::query()->whereNotNull('content_md')->where('content_md', '!=', '');
        if ($limit > 0) {
            $q->limit($limit);
        }
        $posts = $q->get();

        $changed = 0; $skipped = 0;
        foreach ($posts as $post) {
            $newMd = $publisher->resolveInternalLinks((string) $post->content_md, $post->locale ?: 'tr');
            if ($newMd === $post->content_md) {
                $skipped++;
                continue;
            }
            $this->line(($dry ? '🔍 ' : '✅ ') . "#{$post->id} [{$post->locale}] " . mb_substr((string) $post->title, 0, 55));
            if ($dry) {
                $changed++;
                continue;
            }
            $html = Str::markdown($newMd, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $post->update(['content_md' => $newMd, 'content_html' => $html]);
            $changed++;
        }

        $this->newLine();
        $this->info("İç link çözümleme: {$changed} yazı güncellendi, {$skipped} değişmedi" . ($dry ? ' (DRY-RUN)' : ''));
        return self::SUCCESS;
    }
}

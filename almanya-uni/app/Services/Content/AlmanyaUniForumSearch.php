<?php

namespace App\Services\Content;

use Illuminate\Support\Facades\DB;

/**
 * AlmanyaUni kendi phpBB forumunda (almanyauni_forum DB) topic araması.
 * Entity adına göre topic_title eşleştirir, view-count'a göre sıralar.
 */
class AlmanyaUniForumSearch
{
    private const DB = 'almanyauni_forum';
    private const FORUM_PATH = '/forum/viewtopic.php';

    /**
     * @return array<int, array{title:string, url:string, views:int, replies:int, last_post:?string}>
     */
    public function search(string $entityName, int $limit = 5): array
    {
        $keywords = $this->extractKeywords($entityName);
        if (empty($keywords)) return [];

        try {
            $query = DB::table(self::DB . '.phpbb_topics')
                ->where('topic_visibility', 1)
                ->where(function ($w) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $w->orWhere('topic_title', 'like', "%$kw%");
                    }
                })
                ->orderByDesc('topic_views')
                ->limit($limit)
                ->select(['topic_id', 'topic_title', 'topic_views', 'topic_posts_approved', 'topic_last_post_time']);

            return $query->get()->map(fn ($t) => [
                'title' => $t->topic_title,
                'url' => self::FORUM_PATH . '?t=' . $t->topic_id,
                'views' => (int) $t->topic_views,
                'replies' => max(0, (int) $t->topic_posts_approved - 1),
                'last_post' => $t->topic_last_post_time ? date('Y-m-d', $t->topic_last_post_time) : null,
            ])->toArray();
        } catch (\Throwable $e) {
            \Log::warning('AlmanyaUni forum search hatası: ' . $e->getMessage());
            return [];
        }
    }

    private function extractKeywords(string $text): array
    {
        $stop = ['hochschule', 'universität', 'university', 'üniversite', 'üniversitesi',
            'der', 'die', 'das', 'in', 'am', 'an', 'auf',
            'technische', 'angewandte', 'wissenschaften'];

        $words = preg_split('/[\s,\.\-]+/u', mb_strtolower($text));
        $words = array_filter($words, fn ($w) => mb_strlen($w) >= 3 && !in_array($w, $stop, true));
        return array_values(array_unique($words));
    }

    public function isAvailable(): bool
    {
        try {
            DB::table(self::DB . '.phpbb_topics')->limit(1)->exists();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}

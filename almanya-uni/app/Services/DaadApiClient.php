<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DaadApiClient
{
    public const BASE = 'https://www2.daad.de/deutschland/studienangebote/international-programmes/api/solr/en/search.json';

    public const DEGREES = [
        1 => 'bachelor',
        2 => 'master',
        3 => 'phd',
        4 => 'staatsexamen',
        5 => 'graduate_school',
        6 => 'preparatory',
        7 => 'language_course',
        8 => 'short_course',
        10 => 'other',
    ];

    /**
     * Belirli degree için tüm sayfaları gez. Her sayfa için callback'i çağırır.
     *
     * @param  callable(array $course): void  $onCourse
     * @return array{total: int, fetched: int}
     */
    public function paginate(int $degree, callable $onCourse, int $pageSize = 50, int $sleepMs = 1000): array
    {
        $offset = 0;
        $fetched = 0;
        $total = null;

        while (true) {
            $url = self::BASE . '?' . http_build_query([
                'limit' => $pageSize,
                'offset' => $offset,
                'degree[]' => $degree,
                'sort' => 4,
            ]);

            $resp = Http::withHeaders(['User-Agent' => 'AlmanyaUniBot/1.0 (+https://almanyauni.de/bot)'])
                ->timeout(20)
                ->retry(2, 1000)
                ->get($url);

            if (!$resp->successful()) {
                throw new \RuntimeException('DAAD HTTP ' . $resp->status() . ' @ offset=' . $offset);
            }

            $data = $resp->json();
            $total ??= (int) ($data['numResults'] ?? 0);
            $courses = $data['courses'] ?? [];

            if (empty($courses)) break;

            foreach ($courses as $c) {
                $onCourse($c);
                $fetched++;
            }

            $offset += $pageSize;
            if ($offset >= $total) break;
            usleep($sleepMs * 1000);
        }

        return ['total' => $total ?? 0, 'fetched' => $fetched];
    }
}

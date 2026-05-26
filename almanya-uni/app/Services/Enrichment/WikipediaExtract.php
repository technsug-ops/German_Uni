<?php

namespace App\Services\Enrichment;

use Illuminate\Support\Facades\Http;

/**
 * Wikipedia REST API'den özet (extract) + ana görsel + koordinat çek.
 * Generic — şehir, üni, herhangi bir Wikipedia sayfası için.
 */
class WikipediaExtract
{
    private const USER_AGENT = 'AlmanyaUniBot/1.0 (https://almanyauni.de; technsug@gmail.com)';

    /**
     * @return array{title, extract, thumbnail_url, source_url, lang}|null
     */
    public function fetchByTitle(string $title, string $lang = 'de'): ?array
    {
        $title = trim($title);
        if (!$title) return null;

        // REST Summary API — extract + thumbnail
        $url = "https://$lang.wikipedia.org/api/rest_v1/page/summary/" . rawurlencode($title);

        try {
            $resp = Http::timeout(30)
                ->withHeaders(['User-Agent' => self::USER_AGENT, 'Accept' => 'application/json'])
                ->get($url);

            if (!$resp->ok()) return null;
            $data = $resp->json();

            if (!empty($data['type']) && $data['type'] === 'disambiguation') {
                return null;
            }

            return [
                'title' => $data['title'] ?? $title,
                'extract' => $data['extract'] ?? '',
                'thumbnail_url' => $data['thumbnail']['source'] ?? ($data['originalimage']['source'] ?? null),
                'image_width' => $data['originalimage']['width'] ?? null,
                'source_url' => $data['content_urls']['desktop']['page'] ?? null,
                'lang' => $lang,
                'coordinates' => isset($data['coordinates'])
                    ? ['lat' => $data['coordinates']['lat'], 'lon' => $data['coordinates']['lon']]
                    : null,
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * URL'den title çıkar (örn. https://de.wikipedia.org/wiki/Berlin → "Berlin").
     */
    public function fetchByUrl(string $url, ?string $lang = null): ?array
    {
        if (preg_match('#https?://([a-z]+)\.wikipedia\.org/wiki/(.+)$#u', $url, $m)) {
            $detectedLang = $m[1];
            $title = urldecode($m[2]);
            return $this->fetchByTitle($title, $lang ?: $detectedLang);
        }
        return null;
    }

    /**
     * Çoklu dil dene: de → en → tr.
     */
    public function fetchMultiLang(string $title, array $langs = ['de', 'en', 'tr']): array
    {
        $results = [];
        foreach ($langs as $lang) {
            $r = $this->fetchByTitle($title, $lang);
            if ($r) {
                $results[$lang] = $r;
            }
        }
        return $results;
    }

    /**
     * Wikipedia makalesindeki görselleri çek (MediaWiki action API + generator=images).
     * Tek istek ile tüm görseller + URL + size + MIME döner.
     *
     * @return array<int, array{url:string, title:string, width:int, height:int, mime:string}>
     */
    public function fetchImages(string $title, string $lang = 'de', int $max = 30, int $thumbWidth = 800): array
    {
        $title = trim($title);
        if (!$title) return [];

        $url = "https://$lang.wikipedia.org/w/api.php";

        try {
            $resp = Http::timeout(30)
                ->withHeaders(['User-Agent' => self::USER_AGENT, 'Accept' => 'application/json'])
                ->get($url, [
                    'action' => 'query',
                    'format' => 'json',
                    'prop' => 'imageinfo',
                    'generator' => 'images',
                    'titles' => $title,
                    'iiprop' => 'url|size|mime',
                    'iiurlwidth' => $thumbWidth,
                    'gimlimit' => $max,
                    'redirects' => 1,
                ]);

            if (!$resp->ok()) return [];
            $data = $resp->json();
            $pages = $data['query']['pages'] ?? [];

            $images = [];
            foreach ($pages as $page) {
                $info = $page['imageinfo'][0] ?? null;
                if (!$info) continue;
                $images[] = [
                    'url' => $info['thumburl'] ?? $info['url'] ?? '',
                    'original_url' => $info['url'] ?? '',
                    'title' => $page['title'] ?? '',
                    'width' => (int) ($info['thumbwidth'] ?? $info['width'] ?? 0),
                    'height' => (int) ($info['thumbheight'] ?? $info['height'] ?? 0),
                    'mime' => $info['mime'] ?? '',
                    'source_url' => $info['descriptionurl'] ?? '',
                ];
            }
            return $images;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Galeri için "okunaklı" görseller seç:
     *  - SVG, ikon, logo, disambig, audio dosyaları at
     *  - En az 300px genişlik
     *  - Maks N adet, en geniş olanlar tercih edilir
     *
     * @param  array<int, array> $images  fetchImages çıktısı
     * @return array<int, array{url:string, alt:string, source_url:string}>
     */
    public function curateGallery(array $images, int $limit = 10): array
    {
        $blacklistPatterns = [
            '/icon/i', '/logo/i', '/wiki(media|pedia)/i', '/disambig/i',
            '/^File:OOjs/i', '/Commons-logo/i', '/Wiktionary/i',
            '/seal/i', '/coat[\s_]?of[\s_]?arms/i',
            '/\.svg$/i', '/\.ogg$/i', '/\.webm$/i',
            '/Wappen/i', '/Flag_of_/i',
        ];

        $filtered = array_values(array_filter($images, function (array $img) use ($blacklistPatterns) {
            if (empty($img['url'])) return false;
            if (!str_starts_with($img['mime'] ?? '', 'image/')) return false;
            if (($img['width'] ?? 0) < 300) return false;
            $title = $img['title'] ?? '';
            foreach ($blacklistPatterns as $p) {
                if (preg_match($p, $title)) return false;
            }
            return true;
        }));

        usort($filtered, fn ($a, $b) => ($b['width'] ?? 0) <=> ($a['width'] ?? 0));

        return array_map(function ($img) {
            $alt = preg_replace('/^(File|Datei|Fichier|Archivo|Image):/i', '', $img['title']);
            $alt = preg_replace('/\.(jpe?g|png|gif|webp|tiff?)$/i', '', $alt);
            $alt = preg_replace('/[_]+/', ' ', $alt);
            $alt = trim($alt);
            return [
                'url' => $img['url'],
                'alt' => $alt,
                'source_url' => $img['source_url'] ?? '',
            ];
        }, array_slice($filtered, 0, $limit));
    }
}

<?php

namespace App\Services;

use App\Models\University;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WikipediaService
{
    private const USER_AGENT = 'AlmanyaUniBot/1.0 (https://almanyauni.de; technsug@gmail.com)';
    private const COMMONS_FILEPATH = 'https://commons.wikimedia.org/wiki/Special:FilePath/';

    /**
     * Tek üni için Wikipedia infobox'tan veri çekip eksik alanları doldurur.
     * Return: ['changes' => array<string, [old,new]>, 'status' => string]
     */
    public function enrich(University $u, bool $dryRun = false): array
    {
        if (! $u->wikipedia_url_de) {
            return ['status' => 'skip_no_url', 'changes' => []];
        }

        $title = $this->urlToTitle($u->wikipedia_url_de);
        if (! $title) {
            return ['status' => 'skip_bad_url', 'changes' => []];
        }

        $wikitext = $this->fetchWikitext($title);
        if (! $wikitext) {
            return ['status' => 'fetch_failed', 'changes' => []];
        }

        $infobox = $this->extractInfobox($wikitext);
        if (! $infobox) {
            return ['status' => 'no_infobox', 'changes' => []];
        }

        $changes = [];

        // Student count
        if (empty($u->student_count) && isset($infobox['Studentenzahl'])) {
            $n = $this->parseStudentCount($infobox['Studentenzahl']);
            if ($n) {
                $changes['student_count'] = [$u->student_count, $n];
                $u->student_count = $n;
            }
        }

        // Founded year
        if (empty($u->founded_year) && isset($infobox['Gründungsdatum'])) {
            $year = $this->parseFoundedYear($infobox['Gründungsdatum']);
            if ($year) {
                $changes['founded_year'] = [$u->founded_year, $year];
                $u->founded_year = $year;
            }
        }

        // Logo
        if (empty($u->logo_url) && isset($infobox['Logo'])) {
            $logoUrl = $this->buildLogoUrl($infobox['Logo']);
            if ($logoUrl) {
                $changes['logo_url'] = [$u->logo_url, $logoUrl];
                $u->logo_url = $logoUrl;
            }
        }

        if ($changes && ! $dryRun) {
            $u->save();
        }

        return [
            'status'  => $changes ? 'updated' : 'no_changes',
            'changes' => $changes,
        ];
    }

    /**
     * Wikipedia URL'inden sayfa başlığını çıkar.
     * https://de.wikipedia.org/wiki/Ludwig-Maximilians-Universit%C3%A4t_M%C3%BCnchen
     *   → Ludwig-Maximilians-Universität_München
     */
    private function urlToTitle(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (! $path) {
            return null;
        }
        $title = basename($path);
        if ($title === '' || $title === 'wiki') {
            return null;
        }
        return rawurldecode($title);
    }

    /**
     * MediaWiki API'den wikitext çek.
     */
    private function fetchWikitext(string $title): ?string
    {
        try {
            $resp = Http::timeout(20)
                ->withHeaders(['User-Agent' => self::USER_AGENT])
                ->get('https://de.wikipedia.org/w/api.php', [
                    'action' => 'parse',
                    'page'   => $title,
                    'format' => 'json',
                    'prop'   => 'wikitext',
                    'redirects' => 1,
                ]);

            if (! $resp->ok()) {
                return null;
            }
            $data = $resp->json();
            return $data['parse']['wikitext']['*'] ?? null;
        } catch (\Throwable $e) {
            Log::warning("Wikipedia fetch failed for $title: " . $e->getMessage());
            return null;
        }
    }

    /**
     * "Infobox Hochschule" bloğunu çıkar ve key=value şeklinde parse et.
     */
    private function extractInfobox(string $wikitext): ?array
    {
        // Match Infobox Hochschule veya Infobox_Hochschule (varyantlar)
        if (! preg_match('/\{\{Infobox[\s_]+Hochschule(.+?)\n\}\}/s', $wikitext, $m)) {
            return null;
        }

        $body = $m[1];
        $fields = [];

        // Her satır: | Key = Value (multi-line dahil)
        // Önce satır başı pipe ile böl, ilki boş olur
        $parts = preg_split('/^\s*\|\s*/m', $body);

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            $eq = strpos($part, '=');
            if ($eq === false) {
                continue;
            }

            $key   = trim(substr($part, 0, $eq));
            $value = trim(substr($part, $eq + 1));

            if ($key !== '' && $value !== '') {
                $fields[$key] = $value;
            }
        }

        return $fields ?: null;
    }

    /**
     * "54.616 <small>(WS 24/25)</small><ref ...>" → 54616
     */
    private function parseStudentCount(string $raw): ?int
    {
        // Remove HTML/ref/comments
        $clean = preg_replace('/<ref[^>]*>.*?<\/ref>/s', '', $raw);
        $clean = preg_replace('/<ref[^\/]*\/>/', '', $clean);
        $clean = strip_tags($clean);

        // First number with optional thousand separators
        // German: 54.616 (dot as thousands)
        if (preg_match('/(\d{1,3}(?:\.\d{3})+|\d{4,7})/', $clean, $m)) {
            $n = (int) str_replace('.', '', $m[1]);
            // Sanity check: 50 ile 500000 arası
            if ($n >= 50 && $n <= 500000) {
                return $n;
            }
        }
        return null;
    }

    /**
     * "1472 in Ingolstadt,<br />seit 1826 ..." → 1472
     * "8. Juni 1810" → 1810
     */
    private function parseFoundedYear(string $raw): ?int
    {
        $clean = preg_replace('/<ref[^>]*>.*?<\/ref>/s', '', $raw);
        $clean = strip_tags($clean);

        // İlk 4 haneli yıl (1000-2099 arası)
        if (preg_match('/\b(1[0-9]{3}|20[0-2][0-9])\b/', $clean, $m)) {
            return (int) $m[1];
        }
        return null;
    }

    /**
     * "LMU Muenchen Logo.svg" → Commons FilePath URL
     * Dosya adındaki underscore'ları normalize et.
     */
    private function buildLogoUrl(string $filename): ?string
    {
        $clean = trim($filename);
        // Strip eventual "Datei:" / "File:" prefix
        $clean = preg_replace('/^(Datei|File|Bild):\s*/i', '', $clean);
        // Strip alignment / size modifiers (after a pipe)
        if (str_contains($clean, '|')) {
            $clean = explode('|', $clean, 2)[0];
            $clean = trim($clean);
        }
        if ($clean === '') {
            return null;
        }
        // URL encode (Commons accepts spaces or %20 or _)
        return self::COMMONS_FILEPATH . rawurlencode(str_replace(' ', '_', $clean)) . '?width=400';
    }
}

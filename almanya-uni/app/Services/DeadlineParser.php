<?php

namespace App\Services;

use Carbon\Carbon;

class DeadlineParser
{
    private const MONTHS_EN = [
        'january' => 1, 'jan' => 1, 'february' => 2, 'feb' => 2, 'march' => 3, 'mar' => 3,
        'april' => 4, 'apr' => 4, 'may' => 5, 'june' => 6, 'jun' => 6, 'july' => 7, 'jul' => 7,
        'august' => 8, 'aug' => 8, 'september' => 9, 'sep' => 9, 'sept' => 9, 'october' => 10, 'oct' => 10,
        'november' => 11, 'nov' => 11, 'december' => 12, 'dec' => 12,
    ];

    /**
     * @return array{summer: ?string, winter: ?string, confidence: string}
     */
    public function parse(?string $text): array
    {
        if (!$text) {
            return ['summer' => null, 'winter' => null, 'confidence' => 'empty'];
        }

        $normalized = $this->normalize($text);
        $lower = mb_strtolower($normalized);

        $winterDate = $this->extractDateForSemester($lower, 'winter');
        $summerDate = $this->extractDateForSemester($lower, 'summer');

        $confidence = match (true) {
            $winterDate && $summerDate => 'high',
            $winterDate || $summerDate => 'medium',
            default => 'low',
        };

        return [
            'summer' => $summerDate,
            'winter' => $winterDate,
            'confidence' => $confidence,
        ];
    }

    private function normalize(string $s): string
    {
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $s = preg_replace('/\s+/u', ' ', $s);
        $s = str_replace(['–', '—'], '-', $s);
        return trim($s);
    }

    /**
     * Cümleyi semester kelimesine göre kesip o pencerede en uygun tarihi yakala.
     */
    private function extractDateForSemester(string $text, string $semester): ?string
    {
        if (!str_contains($text, $semester . ' semester') && !str_contains($text, $semester . '-semester')) {
            return null;
        }

        $pattern = '/(?P<phrase>(?:[^.\n]{0,200}?))\b' . $semester . '[\s\-]semester/u';
        if (!preg_match_all($pattern, $text, $matches)) {
            return null;
        }

        $bestDate = null;
        foreach ($matches['phrase'] as $phrase) {
            $window = mb_substr($text, max(0, mb_strpos($text, $phrase) - 80), mb_strlen($phrase) + 80);
            $date = $this->extractDate($window);
            if ($date) {
                // En geç tarihi al (en muhafazakar)
                if (!$bestDate || strtotime($date) > strtotime($bestDate)) {
                    $bestDate = $date;
                }
            }
        }

        return $bestDate;
    }

    private function extractDate(string $text): ?string
    {
        $monthsAlt = implode('|', array_keys(self::MONTHS_EN));

        $patterns = [
            // "15 august" / "15 aug"
            "/\b(?P<day>\d{1,2})\s+(?P<month>$monthsAlt)\b/iu",
            // "august 15"
            "/\b(?P<month>$monthsAlt)\s+(?P<day>\d{1,2})\b/iu",
            // "15.08." / "15.08.2026"
            "/\b(?P<day>\d{1,2})\.(?P<monthnum>\d{1,2})\.(?P<year>\d{4})?/u",
        ];

        $candidates = [];
        foreach ($patterns as $p) {
            if (preg_match_all($p, $text, $m, PREG_SET_ORDER)) {
                foreach ($m as $hit) {
                    $day = (int) $hit['day'];
                    $month = isset($hit['monthnum']) ? (int) $hit['monthnum'] : (self::MONTHS_EN[mb_strtolower($hit['month'])] ?? null);
                    if (!$month || $day < 1 || $day > 31 || $month < 1 || $month > 12) continue;

                    $year = isset($hit['year']) && $hit['year'] ? (int) $hit['year'] : $this->guessUpcomingYear($month, $day);

                    try {
                        $candidates[] = Carbon::createFromDate($year, $month, $day)->toDateString();
                    } catch (\Throwable $e) {
                        // skip invalid date
                    }
                }
            }
        }

        if (empty($candidates)) return null;

        // En geç tarihi (en muhafazakar/güvenli) kullan
        sort($candidates);
        return end($candidates);
    }

    private function guessUpcomingYear(int $month, int $day): int
    {
        $today = Carbon::today();
        $candidate = Carbon::createFromDate($today->year, $month, $day);
        if ($candidate->isPast()) {
            return $today->year + 1;
        }
        return $today->year;
    }
}

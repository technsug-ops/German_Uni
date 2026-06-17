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
        // Almanca ay adları (admission_summary bazen Almanca)
        'januar' => 1, 'februar' => 2, 'märz' => 3, 'maerz' => 3, 'mai' => 5, 'juni' => 6,
        'juli' => 7, 'oktober' => 10, 'dezember' => 12,
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

    // Dönem indikatörleri — "semester"a ek olarak "intake", Almanca terimler, intake-ayı.
    // Wintersemester ~Ekim başlar → october/winter; Sommersemester ~Nisan → april/summer.
    private const SEM_INDICATORS = [
        'winter' => ['winter semester', 'winter-semester', 'wintersemester', 'winter intake', 'winter term', 'october intake', 'autumn intake', 'fall intake'],
        'summer' => ['summer semester', 'summer-semester', 'sommersemester', 'summer intake', 'summer term', 'april intake', 'spring intake'],
    ];

    /**
     * Metinde dönem indikatörünü bulup ona EN YAKIN tarihi yakala. "En yakın" kritik:
     * "1 February (April intake) or 15 August (October intake)" gibi iki tarihli
     * cümlede her dönem doğru tarihi alsın (latest değil — yanlış eşleşmeyi önler).
     */
    private function extractDateForSemester(string $text, string $semester): ?string
    {
        $indicators = self::SEM_INDICATORS[$semester] ?? [];
        // Cümlelere böl — ama "15. Juli" gibi tarihteki noktada BÖLME (rakam+nokta korunur).
        $sentences = preg_split('/(?<=[.!?\n])(?<!\d\.)\s+/u', $text) ?: [$text];

        $best = null;
        $bestDist = PHP_INT_MAX;
        foreach ($sentences as $sent) {
            $low = mb_strtolower($sent);
            foreach ($indicators as $ind) {
                $ipos = mb_strpos($low, $ind);
                if ($ipos === false) continue;
                // İndikatörü içeren CÜMLEDE, ona EN YAKIN tarihi al (komşu cümleye taşma yok).
                foreach ($this->extractDatesWithPos($sent) as [$date, $dpos]) {
                    $dist = abs($dpos - $ipos);
                    if ($dist < $bestDist) {
                        $bestDist = $dist;
                        $best = $date;
                    }
                }
            }
        }
        return $best;
    }

    /**
     * Penceredeki tüm tarihleri konumlarıyla döner: [[Y-m-d, pos], ...].
     */
    private function extractDatesWithPos(string $text): array
    {
        $monthsAlt = implode('|', array_keys(self::MONTHS_EN));
        $patterns = [
            "/\b(?P<day>\d{1,2})\s*\.?\s+(?P<month>$monthsAlt)\b/iu", // "15 July", "15. Juli"
            "/\b(?P<month>$monthsAlt)\s+(?P<day>\d{1,2})\b/iu",        // "July 15"
            "/\b(?P<day>\d{1,2})\.(?P<monthnum>\d{1,2})\.(?P<year>\d{4})?/u", // "15.07.2026"
        ];

        $out = [];
        foreach ($patterns as $p) {
            if (preg_match_all($p, $text, $ms, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
                foreach ($ms as $hit) {
                    $day = (int) $hit['day'][0];
                    $month = isset($hit['monthnum']) ? (int) $hit['monthnum'][0]
                        : (self::MONTHS_EN[mb_strtolower($hit['month'][0])] ?? null);
                    if (!$month || $day < 1 || $day > 31 || $month < 1 || $month > 12) continue;
                    $year = isset($hit['year']) && $hit['year'][0] ? (int) $hit['year'][0] : $this->guessUpcomingYear($month, $day);
                    try {
                        $out[] = [\Carbon\Carbon::createFromDate($year, $month, $day)->toDateString(), (int) $hit[0][1]];
                    } catch (\Throwable $e) {
                    }
                }
            }
        }
        return $out;
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

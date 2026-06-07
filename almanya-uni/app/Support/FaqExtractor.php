<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Auto-extracts a FAQ (question/answer pairs) from post content HTML by treating
 * any <h2>/<h3> heading that ends with "?" as a question and the prose that
 * follows it (up to the next heading) as the answer.
 *
 * Output feeds Seo::genericFaqPage() → schema.org/FAQPage JSON-LD. This makes
 * how-to / Q&A blog posts eligible for AI Overviews (Google AIO + Bing Copilot)
 * and gives LLM crawlers clean machine-readable Q&A — no manual tagging needed.
 *
 * Locale-safe: questions/answers are taken verbatim from the (already localized)
 * article body, so no Turkish leaks into EN/DE pages.
 */
class FaqExtractor
{
    /**
     * @return array<int,array{q:string,a:string}>
     */
    public static function fromHtml(?string $html): array
    {
        if (! $html) {
            return [];
        }

        // Locate every h2/h3 with byte offsets so we can slice the answer body.
        if (! preg_match_all('/<h([23])[^>]*>(.*?)<\/h\1>/is', $html, $headings, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            return [];
        }

        $faqs = [];
        $total = count($headings);

        for ($i = 0; $i < $total; $i++) {
            $question = self::toText($headings[$i][2][0]);

            // Question headings only: must end with a question mark (ASCII or full-width).
            if ($question === '' || ! preg_match('/[?？]$/u', $question)) {
                continue;
            }

            // Answer = everything between this heading and the next h2/h3 (or EOF).
            $answerStart = $headings[$i][0][1] + strlen($headings[$i][0][0]);
            $answerEnd = ($i + 1 < $total) ? $headings[$i + 1][0][1] : strlen($html);
            $answer = self::toText(substr($html, $answerStart, $answerEnd - $answerStart));

            // Skip empty/near-empty answers (a question heading with no prose under it).
            if (mb_strlen($answer) < 20) {
                continue;
            }

            $faqs[] = [
                'q' => $question,
                'a' => Str::limit($answer, 700),
            ];
        }

        return $faqs;
    }

    /** Strip tags, decode entities, collapse whitespace → plain text. */
    private static function toText(string $html): string
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim((string) $text);
    }
}

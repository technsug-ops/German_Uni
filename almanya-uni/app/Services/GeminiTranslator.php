<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class GeminiTranslator
{
    private const API_BASE = 'https://generativelanguage.googleapis.com/v1beta/models/';
    private const DEFAULT_MODEL = 'gemini-2.5-flash-lite';

    public const SYSTEM_PROMPT_PROGRAM = <<<'TXT'
You translate German university program descriptions from English into Turkish for AlmanyaUni, a guide for Turkish students looking to study in Germany.

Rules:
- Output ONLY the Turkish translation. No preamble, no explanations, no quotation marks around the result.
- Keep these German terms in German (do not translate them): Bachelor, Master, PhD, Doktora, Studienkolleg, Numerus Clausus (NC), Abitur, Hochschule, Fachhochschule, Universität, Berufsakademie, Lehramt, Staatsexamen, ECTS, BAföG, Sperrkonto, Anmeldung, Aufenthaltstitel, Uni-Assist, TestDaF, DSH, Goethe-Zertifikat. City and university names stay original.
- Use natural, fluent Turkish that a 17-22 year old Turkish university candidate would understand. Avoid awkward calque translations.
- If the source mentions degrees like "Master of Science (M.Sc.)", keep that form.
- Preserve paragraph breaks, lists, and bullet markers (*, -, 1., 2.) exactly.
- If source contains HTML tags, keep them. If source contains markdown, keep it.
- Do not add information not present in the source.
TXT;

    public const SYSTEM_PROMPT_UNIVERSITY = <<<'TXT'
You translate German university descriptions into Turkish for AlmanyaUni, a guide for Turkish students looking to study in Germany.

Rules:
- Output ONLY the Turkish translation. No preamble, no explanations, no quotation marks around the result.
- Source language is German (or sometimes English). Detect automatically.
- Keep these terms unchanged: Hochschule, Fachhochschule, Universität, Bundesland, Bundesländer, ECTS, NC, Abitur, Studienkolleg, Staatsexamen, Uni-Assist, BAföG, plus the university's own name (e.g. "Ludwig-Maximilians-Universität München" stays as-is or with Turkish article like "Ludwig-Maximilians-Universität München").
- City names: keep German form (München, Köln, Nürnberg — NOT Munich/Cologne/Nuremberg in the Turkish output unless the Turkish form is widely used like "Berlin"). Eyalet adlarını Almanca bırak: Baden-Württemberg, Bayern, Nordrhein-Westfalen.
- Use natural, fluent Turkish for a 17-22 year old Turkish student audience.
- Preserve paragraph breaks, lists, formatting.
- Do not add information not present in the source.
- If source is empty or null, return empty.
TXT;

    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.key');
        $this->model  = (string) config('services.gemini.model', self::DEFAULT_MODEL);
    }

    public function isConfigured(): bool
    {
        return $this->apiKey !== '';
    }

    /**
     * Tek metin çevirisi. $systemPrompt omitted ise PROGRAM prompt kullanılır.
     * @return array{translation:string, input_tokens:int, output_tokens:int}|null
     */
    public function translate(?string $text, ?string $systemPrompt = null): ?array
    {
        if ($text === null || trim($text) === '') {
            return null;
        }

        $url = self::API_BASE . $this->model . ':generateContent';

        $resp = $this->client()->post($url, [
            'systemInstruction' => [
                'parts' => [['text' => $systemPrompt ?? self::SYSTEM_PROMPT_PROGRAM]],
            ],
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $text]]],
            ],
            'generationConfig' => [
                'temperature'     => 0.2,
                'maxOutputTokens' => 8192,
                'topP'            => 0.95,
            ],
            // Akademik metinlerde "harm" filtresi gereksiz yere bloklayabiliyor — gevşet
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT',       'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH',      'threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT','threshold' => 'BLOCK_ONLY_HIGH'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT','threshold' => 'BLOCK_ONLY_HIGH'],
            ],
        ]);

        if (! $resp->ok()) {
            $resp->throw();
        }

        $data = $resp->json();

        // Response: candidates[0].content.parts[0].text
        $translation = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (! $translation) {
            return null;
        }

        $usage = $data['usageMetadata'] ?? [];

        return [
            'translation'   => trim($translation),
            'input_tokens'  => (int) ($usage['promptTokenCount']     ?? 0),
            'output_tokens' => (int) ($usage['candidatesTokenCount'] ?? 0),
        ];
    }

    private function client(): PendingRequest
    {
        return Http::asJson()
            ->timeout(120)
            ->withHeaders([
                'x-goog-api-key' => $this->apiKey,
            ])
            ->retry(3, 2000, function ($exception) {
                if (! $exception instanceof \Illuminate\Http\Client\RequestException) {
                    return false;
                }
                $status = $exception->response?->status();
                // 429 (rate limit), 503 (overloaded), 5xx için retry
                return $status === 429 || ($status >= 500 && $status < 600);
            }, throw: false);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Models\University;
use App\Services\GeminiTranslator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

#[Signature('programs:ai-detect-admission
    {--uni= : Belirli üni slug\'ı (boşsa tüm aktif üni\'ler)}
    {--limit=0 : Maks kaç üni işlensin}
    {--batch=15 : Tek Gemini çağrısında kaç program}
    {--apply : Sonucu DB\'ye yaz (varsayılan: sadece CSV)}
    {--output=storage/app/admission-ai-draft.csv : Çıktı CSV yolu}
    {--delay=1500 : İstek aralığı (ms)}
    {--overwrite : Mevcut admission_mode dolu olanları da güncelle}
')]
#[Description('Gemini AI ile her programın muhtemel NC durumunu tahmin eder.')]
class AiDetectAdmission extends Command
{
    private const SYSTEM_PROMPT = <<<'TXT'
You are an expert on German university admission (Zulassungsmodus). Calibrated on real data from German university websites (Universität Bonn 2025, TU Berlin, etc.).

CRITICAL CORRECTION FROM REAL DATA:
- **Most Master programs are zulassungsfrei**, NOT auswahl. Only true Eignungsprüfung programs (art, music, sports, some specific MAs) are auswahl. Standard subject-based MAs are zulassungsfrei.
- Most Bachelor programs in humanities, theology, philology, languages, niche sciences = zulassungsfrei.
- Lehramt (teacher training) Bachelor is usually zulassungsfrei.

Rules:

1. **bundesweit** — ONLY for: Human Medicine (Medizin / Humanmedizin), Dentistry (Zahnmedizin), Pharmacy (Pharmazie), Veterinary Medicine (Tiermedizin / Veterinärmedizin). Hochschulstart-controlled.

2. **zulassungsfrei** — DEFAULT for:
   - **Most Master of Arts / Master of Science** (unless clearly art/music/sport)
   - Bachelor: Physics, Mathematics, Chemistry, Biology, Geosciences, Astrophysics
   - Bachelor: Most Engineering at Technische Universitäten (Mech, Elec, Civil, Aerospace, Materials, Industrial)
   - Bachelor: All niche humanities (Sinology, Egyptology, Theology, Philology, Slavic, Indology, Tibetology, Bengali, Arabisch, Altamerikanistik, Asienwissenschaften, Archäologie)
   - Bachelor: Lehramt (most subjects), Agrarwissenschaft, Forstwissenschaft, Ernährungswissenschaft
   - Computer Science at TUs (Berlin, München, Dresden, etc. — but NOT LMU/Heidelberg, those are NC)

3. **oertlich** (local NC) — Likely for:
   - Bachelor: Psychology (almost always), BWL/Business Administration, Economics/VWL, Law/Jura (some)
   - Bachelor: Communication Sciences, Media Studies, Sport Science (often), Architecture
   - Bachelor: Computer Science at major non-TU universities (LMU, Heidelberg, Köln)
   - Some applied Masters at FH: Data Science, AI, International Business
   - Health-related (excluding the 4 bundesweit): Nursing Science, Hebammenwissenschaft

4. **auswahl** (Eignungsprüfung/portfolio/audition) — ONLY for:
   - All Kunstakademie / Musikhochschule programs (any degree)
   - Architecture Master (often portfolio)
   - Design, Film, Theatre, Music programs at any uni
   - Sports / Physical Education (Eignung body test)
   - Some teacher training art/music/sport subjects

5. **Output format**: ONLY a JSON array, no preamble, no markdown fences. Each item: {"slug": "<exact-original-slug>", "mode": "<zulassungsfrei|oertlich|bundesweit|auswahl>", "confidence": "high|medium|low", "reason": "<≤10 words>"}

6. Confidence:
   - "high" — Medicine/Dentistry/Pharm/Vet = bundesweit, clear art/music = auswahl, niche humanities Bachelor = zulassungsfrei
   - "medium" — Standard MA without clear signal, Bachelor in clear category
   - "low" — Ambiguous: Master with "applied", interdisciplinary, joint degrees, unusual program names

7. If you cannot classify, set mode to null. Don't guess wildly.

8. **Single-Subject Master** (Master of Arts Single-Subject) at large unis like Bonn = usually zulassungsfrei.

9. **Lehramt** programs = usually zulassungsfrei (especially at Bonn, FAU, Tübingen). Some art/music/sport Lehramt = auswahl.
TXT;

    public function handle(GeminiTranslator $translator): int
    {
        if (! $translator->isConfigured()) {
            $this->error('GEMINI_API_KEY .env\'de yok.');
            return self::FAILURE;
        }

        $uniSlug = $this->option('uni');
        $limit   = (int) $this->option('limit');
        $batch   = max(1, min(30, (int) $this->option('batch')));
        $apply   = (bool) $this->option('apply');
        $output  = $this->option('output');
        $delay   = max(0, (int) $this->option('delay'));
        $overwrite = (bool) $this->option('overwrite');

        $uniQuery = University::withCount('programs')
            ->where('is_active', true)
            ->orderByDesc('programs_count');

        if ($uniSlug) $uniQuery->where('slug', $uniSlug);
        if ($limit > 0) $uniQuery->limit($limit);

        $unis = $uniQuery->get();
        if ($unis->isEmpty()) {
            $this->error('Hiç üniversite bulunamadı.');
            return self::FAILURE;
        }

        $this->info(count($unis) . ' üniversite işlenecek. Mod: ' . ($apply ? 'APPLY (DB\'ye yazar)' : 'CSV only'));

        $csvHandle = fopen($output, 'w');
        if (! $csvHandle) {
            $this->error("CSV yazılamıyor: $output");
            return self::FAILURE;
        }
        fputcsv($csvHandle, ['program_slug', 'admission_mode', 'admission_summary', 'university', 'program_name', 'confidence']);

        $totalStats = ['predicted' => 0, 'null' => 0, 'errors' => 0, 'applied' => 0, 'input_tokens' => 0, 'output_tokens' => 0];

        foreach ($unis as $uni) {
            $programs = Program::where('university_id', $uni->id)
                ->where('is_active', true)
                ->when(! $overwrite, fn ($q) => $q->whereNull('admission_mode'))
                ->get(['id', 'slug', 'name_de', 'degree']);

            if ($programs->isEmpty()) {
                $this->line(sprintf('  %-55s — atlandı', mb_substr($uni->name_de, 0, 55)));
                continue;
            }

            $this->info(sprintf('▸ %s (%d program)', $uni->name_de, $programs->count()));

            $bar = $this->output->createProgressBar($programs->count());
            $bar->start();

            foreach ($programs->chunk($batch) as $chunk) {
                try {
                    $result = $this->predictBatch($uni, $chunk);

                    $totalStats['input_tokens']  += $result['input_tokens'];
                    $totalStats['output_tokens'] += $result['output_tokens'];

                    foreach ($result['predictions'] as $pred) {
                        $programObj = $chunk->firstWhere('slug', $pred['slug'] ?? '');
                        $name = $programObj?->name_de ?? '?';

                        if (! empty($pred['mode']) && in_array($pred['mode'], ['zulassungsfrei', 'oertlich', 'bundesweit', 'auswahl'], true)) {
                            fputcsv($csvHandle, [
                                $pred['slug'],
                                $pred['mode'],
                                $pred['reason'] ?? '',
                                $uni->name_de,
                                $name,
                                $pred['confidence'] ?? 'medium',
                            ]);
                            $totalStats['predicted']++;

                            if ($apply) {
                                Program::where('slug', $pred['slug'])->update([
                                    'admission_mode'    => $pred['mode'],
                                    'admission_summary' => $pred['reason'] ?? null,
                                ]);
                                $totalStats['applied']++;
                            }
                        } else {
                            $totalStats['null']++;
                        }
                    }
                } catch (\Throwable $e) {
                    $totalStats['errors']++;
                    $this->newLine();
                    $this->warn('  Batch failed: ' . mb_substr($e->getMessage(), 0, 150));
                }

                $bar->advance($chunk->count());
                if ($delay > 0) usleep($delay * 1000);
            }

            $bar->finish();
            $this->newLine();
        }

        fclose($csvHandle);

        $cost = ($totalStats['input_tokens'] / 1_000_000 * 0.10) + ($totalStats['output_tokens'] / 1_000_000 * 0.40);
        $this->newLine();
        $this->table(['Predicted', 'Null', 'Errors', 'Applied', 'Tokens in/out', 'Cost USD'], [[
            $totalStats['predicted'],
            $totalStats['null'],
            $totalStats['errors'],
            $totalStats['applied'],
            number_format($totalStats['input_tokens']) . ' / ' . number_format($totalStats['output_tokens']),
            '$' . number_format($cost, 4, '.', ''),
        ]]);

        $this->info("CSV: $output");
        if (! $apply) {
            $this->warn("DB'ye yazılmadı. Yazmak için: --apply (veya CSV'yi inceleyip programs:import-admission ile yükle)");
        }

        return self::SUCCESS;
    }

    private function predictBatch(University $uni, $programs): array
    {
        $items = $programs->map(fn ($p) => [
            'slug'   => $p->slug,
            'name'   => $p->name_de,
            'degree' => $p->degree,
        ])->values()->all();

        $userMessage = "University: {$uni->name_de}\n\nPrograms to classify (return JSON array only):\n" . json_encode($items, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $resp = Http::asJson()
            ->timeout(120)
            ->withHeaders(['x-goog-api-key' => config('services.gemini.key')])
            ->post('https://generativelanguage.googleapis.com/v1beta/models/' . config('services.gemini.model', 'gemini-2.5-flash-lite') . ':generateContent', [
                'systemInstruction' => ['parts' => [['text' => self::SYSTEM_PROMPT]]],
                'contents'          => [['role' => 'user', 'parts' => [['text' => $userMessage]]]],
                'generationConfig'  => [
                    'temperature'      => 0.2,
                    'maxOutputTokens'  => 4096,
                    'responseMimeType' => 'application/json',
                ],
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ],
            ]);

        if (! $resp->ok()) {
            throw new \RuntimeException('Gemini ' . $resp->status() . ': ' . mb_substr($resp->body(), 0, 200));
        }

        $data = $resp->json();
        $raw  = $data['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
        $usage = $data['usageMetadata'] ?? [];

        $predictions = json_decode($raw, true);
        if (! is_array($predictions)) {
            if (preg_match('/```(?:json)?\s*(\[.+\])\s*```/s', $raw, $m)) {
                $predictions = json_decode($m[1], true);
            }
            if (! is_array($predictions)) $predictions = [];
        }

        return [
            'predictions'   => $predictions,
            'input_tokens'  => (int) ($usage['promptTokenCount'] ?? 0),
            'output_tokens' => (int) ($usage['candidatesTokenCount'] ?? 0),
        ];
    }
}

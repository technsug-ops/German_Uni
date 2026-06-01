<?php
/**
 * Task 1 — EN native pass: lang/en.json'da Türkçe sızdıran değerleri (bazı blade'ler
 * Türkçe string'i __() anahtarı yapmış, en.json identity=TR) native İngilizce'ye çevirir.
 * de.json/tr.json zaten doğru — sadece en.json değeri düzeltilir.
 * Format korunur (indent=4, \/ escape). ContentVoice EN register.
 *
 * Çalıştır (şehir çevirisi bitince): php _fix_en_leaks.php  [--dry]
 */
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\Content\ContentVoice;
use Illuminate\Support\Facades\Http;

$dry = in_array('--dry', $argv);
$apiKey = config('services.gemini.key');
if (! $apiKey) { fwrite(STDERR, "GEMINI key yok\n"); exit(1); }

$path = __DIR__ . '/lang/en.json';
$en = json_decode(file_get_contents($path), true);
$voice = ContentVoice::for('en');

// TR sızdıran değerler (Türkçe'ye özgü karakter)
$leaky = [];
foreach ($en as $k => $v) {
    if (is_string($v) && preg_match('/[ışğİ]/u', $v)) $leaky[$k] = $v;
}
echo count($leaky) . " TR-sızdıran en.json girdisi bulundu" . ($dry ? " (DRY)\n" : "\n");

$translateOne = function (string $tr) use ($voice, $apiKey): ?string {
    $prompt = <<<TXT
Translate this UI/content string from Turkish to NATIVE English for AlmanyaUni (study-in-Germany guide).

VOICE:
{$voice}

RULES:
- Output ONLY the translated string, nothing else (no quotes, no explanation).
- Preserve markdown (**bold**, links), placeholders (:count, :name, :uni), HTML tags, and German proper nouns (Sperrkonto, Studienkolleg, Anabin, BAföG, TestDaF) verbatim.
- Keep numbers/values identical.

TURKISH:
{$tr}
TXT;
    for ($a = 0; $a < 3; $a++) {
        try {
            $r = Http::asJson()->timeout(120)->withHeaders(['x-goog-api-key' => $apiKey])
                ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 4096],
                ]);
            if (! $r->ok()) { sleep(4); continue; }
            $t = trim((string) $r->json('candidates.0.content.parts.0.text'));
            $t = trim($t, "\"'`");
            if ($t !== '' && ! preg_match('/[ışğİ]/u', $t)) return $t;
        } catch (\Throwable $e) { sleep(4); }
    }
    return null;
};

$done = 0; $fail = 0; $i = 0;
foreach ($leaky as $k => $tr) {
    $i++;
    echo "[$i/" . count($leaky) . "] " . mb_substr($k, 0, 55) . " ... ";
    if ($dry) { echo "(dry)\n"; continue; }
    $eng = $translateOne($tr);
    if ($eng) { $en[$k] = $eng; $done++; echo "✓ " . mb_substr($eng, 0, 45) . "\n"; }
    else { $fail++; echo "✗\n"; }
    usleep(1500000);
}

if (! $dry && $done) {
    // JSON_UNESCAPED_SLASHES VERME -> slash'ler \/ escape'li kalır (lang format).
    // JSON_UNESCAPED_UNICODE -> ü/ç literal. 4-boşluk = PRETTY_PRINT.
    $s = json_encode($en, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if ($s === false) { fwrite(STDERR, "json_encode FAIL — yazılmadı\n"); exit(1); }
    file_put_contents($path, $s . "\n");
    echo "\nen.json güncellendi: +$done çeviri, $fail fail\n";
}

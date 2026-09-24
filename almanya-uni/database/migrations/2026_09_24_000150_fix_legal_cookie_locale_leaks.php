<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Çerez Politikası EN/DE gövdelerindeki Türkçe sızıntılarını temizler.
 *
 * Tek bir `bodies` JSON'ında üç dilin birlikte tutulması, çeviri sırasında bazı
 * cümlelerin Türkçe kalmasına yol açmış. Canlıda (2026-09-24) EN ve DE Çerez
 * Politikası'nda kalan parçalar:
 *
 *   EN: "Session, CSRF protection, dil tercihi. Onay gerektirmez (GDPR 6(1)(f))."
 *       tablo: "Session yönetimi"
 *   DE: "Session, CSRF-Schutz, dil tercihi. Onay gerektirmez (GDPR 6(1)(f))."
 *       tablo: "Session yönetimi"
 *
 * Analitik ve pazarlama satırlarındaki Türkçe kalıntılar 000100 (izleme beyanı)
 * migration'ında zaten değiştiriliyor; burada geri kalanlar ele alınıyor.
 * TR gövdesine ve gizlilik sayfasına DOKUNULMAZ (privacy EN/DE temiz).
 *
 * Slug/URL/redirect işine girilmez — o ayrı bir görev.
 *
 * Bu migration hâlâ legacy `bodies` JSON'ı üzerinde çalışır; çeviri tablosuna
 * taşıma bundan SONRAKİ migration'da (000300) yapılır, böylece taşınan veri
 * baştan düzeltilmiş olur.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('legal_pages')) {
            Log::warning('legal locale leak fix: legal_pages tablosu yok, atlandı');

            return;
        }

        $row = DB::table('legal_pages')->where('key', 'cookies')->first();

        if (! $row) {
            Log::warning("legal locale leak fix: 'cookies' sayfası bulunamadı");
            echo "legal locale leak fix: 'cookies' kaydı yok, atlandı\n";

            return;
        }

        $bodies = json_decode($row->bodies ?? '', true);

        if (! is_array($bodies) || $bodies === []) {
            Log::warning("legal locale leak fix: 'cookies' bodies çözümlenemedi");

            return;
        }

        $report = [];
        $changed = false;

        foreach ($this->replacements() as $locale => $pairs) {
            $body = $bodies[$locale] ?? null;

            if (! is_string($body) || trim($body) === '') {
                $report[] = "cookies/{$locale}: gövde yok, atlandı";

                continue;
            }

            $hits = [];
            $misses = [];

            foreach ($pairs as $label => [$search, $replace]) {
                if (! str_contains($body, $search)) {
                    // Zaten düzeltilmiş olabilir (idempotency) ya da metin
                    // elle değişmiş olabilir — her iki hâlde de dokunma.
                    $misses[] = $label;

                    continue;
                }

                $body = str_replace($search, $replace, $body);
                $hits[] = $label;
            }

            if ($misses !== []) {
                Log::warning("legal locale leak fix: cookies/{$locale} eşleşmeyen parça(lar): " . implode(' | ', $misses));
            }

            if ($hits === []) {
                $report[] = "cookies/{$locale}: eşleşme yok (muhtemelen zaten temiz)";

                continue;
            }

            $bodies[$locale] = $body;
            $changed = true;
            $report[] = "cookies/{$locale}: " . implode(', ', $hits)
                . ($misses !== [] ? ' — EKSİK: ' . implode(', ', $misses) : '');
        }

        if ($changed) {
            DB::table('legal_pages')->where('key', 'cookies')->update([
                'bodies'     => json_encode($bodies, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        }

        echo "legal locale leak fix:\n  " . implode("\n  ", $report) . "\n";
    }

    public function down(): void
    {
        // Bilinçli olarak boş: geri alınacak şey "İngilizce sayfaya Türkçe cümle
        // geri koymak" olurdu.
    }

    /**
     * Birebir string değişimi — bu parçalar tek satırda, tek biçimde geçiyor,
     * regex'e gerek yok ve yanlış eşleşme riski de böylece sıfır.
     *
     * @return array<string, array<string, array{0: string, 1: string}>>
     */
    private function replacements(): array
    {
        return [
            'en' => [
                'zorunlu çerez satırı' => [
                    '<li><strong>Essential cookies:</strong> Session, CSRF protection, dil tercihi. Onay gerektirmez (GDPR 6(1)(f)).</li>',
                    '<li><strong>Essential cookies:</strong> Session, CSRF protection, language preference. No consent required (Art. 6(1)(f) GDPR).</li>',
                ],
                'tablo: oturum yönetimi' => [
                    '<td>Session yönetimi</td>',
                    '<td>Session management</td>',
                ],
            ],
            'de' => [
                'zorunlu çerez satırı' => [
                    '<li><strong>Notwendige Cookies:</strong> Session, CSRF-Schutz, dil tercihi. Onay gerektirmez (GDPR 6(1)(f)).</li>',
                    '<li><strong>Notwendige Cookies:</strong> Session, CSRF-Schutz, Spracheinstellung. Keine Einwilligung erforderlich (Art. 6(1)(f) DSGVO).</li>',
                ],
                'tablo: oturum yönetimi' => [
                    '<td>Session yönetimi</td>',
                    '<td>Sitzungsverwaltung</td>',
                ],
            ],
        ];
    }
};

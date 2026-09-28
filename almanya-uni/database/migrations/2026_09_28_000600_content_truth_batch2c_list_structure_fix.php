<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Content Truth Sprint — Batch 2C liste yapısı düzeltmesi (student-work-permit TR, student-matters TR).
 *
 * Batch 2C (000500) "20 Saat Kuralı: Werkstudent …" maddesini doğru metinle değiştirdi, ama bu madde girintili bir
 * alt listedeydi ("    *   …", 4 boşluk). Değiştirme sırasında yalnız 0–3 boşluk girintiyi tanıyan önek regex'i liste
 * işaretini düşürdü; satır "   **20 Saat Kuralı:** …" olarak kaldı ve render'da bir önceki "Vergi Yükümlülüğü"
 * maddesinin devamı gibi göründü.
 *
 * Bu migration YALNIZ eksik liste önekini geri koyar: önek, hemen önceki kardeş madde satırından ("Vergi
 * Yükümlülüğü") birebir alınır (girinti + liste stili aynen korunur). Metin, link ve diğer satırlar değişmez.
 * Ön kontrol her iki yazıda: bozuk satır tam 1 kez + önceki satır "Vergi Yükümlülüğü" liste maddesi. Tamamı
 * düzeltilmişse no-op; başka her durum → RuntimeException, hiçbir yazıya yazılmaz. Tek transaction.
 */
return new class extends Migration
{
    private const SLUGS = [
        'student-work-permit-in-germany-2026-20-hour-rule-and-types',
        'student-matters-in-germany-the-20-hour-rule-tax-and-health',
    ];

    private const BODY = '**20 Saat Kuralı:** Werkstudent olarak ders döneminde haftada en fazla 20 saat çalışmanız, öğrenci sosyal sigorta avantajını korumanın koşuludur; bu bir oturum izni kuralı değildir. Tatil dönemlerinde bu sınır uygulanmaz ve tam zamanlı çalışabilirsiniz; bu haftalar yine 140 iş günlük hesaba sayılır.';

    private const SIBLING = '**Vergi Yükümlülüğü:**';

    public function up(): void
    {
        $problems = [];
        $writes = [];
        $applied = 0;
        foreach (self::SLUGS as $slug) {
            $rows = DB::table('posts')->where('slug', $slug)->where('locale', 'tr')->get(['id', 'content_md']);
            if ($rows->count() !== 1) {
                $problems[] = "{$slug}: kayıt sayısı {$rows->count()}";
                continue;
            }
            $lines = explode("\n", (string) $rows->first()->content_md);
            $broken = [];
            $fixed = 0;
            foreach ($lines as $i => $l) {
                $core = rtrim($l, "\r");
                if (preg_match('/^[ \t]*$/', substr($core, 0, strlen($core) - strlen(self::BODY))) && str_ends_with($core, self::BODY) && strlen($core) > strlen(self::BODY)) {
                    $broken[] = $i;                                   // yalnız boşluk öneki: liste işareti düşmüş
                } elseif (preg_match('/^\s*[*+-]\s+' . preg_quote(self::BODY, '/') . '$/u', $core)) {
                    $fixed++;                                         // zaten liste maddesi
                }
            }
            if (count($broken) === 0 && $fixed === 1) {
                $applied++;
                continue;
            }
            if (count($broken) !== 1 || $fixed !== 0) {
                $problems[] = "{$slug}: beklenen bozuk durum yok (bozuk ".count($broken).", düzgün {$fixed})";
                continue;
            }
            $i = $broken[0];
            $prev = $i - 1;
            while ($prev >= 0 && trim($lines[$prev]) === '') {
                $prev--;
            }
            if ($prev < 0 || ! preg_match('/^(\s*[*+-]\s+)' . preg_quote(self::SIBLING, '/') . '/u', $lines[$prev], $m)) {
                $problems[] = "{$slug}: önceki satır 'Vergi Yükümlülüğü' liste maddesi değil";
                continue;
            }
            $cr = str_ends_with($lines[$i], "\r") ? "\r" : '';
            $lines[$i] = $m[1].self::BODY.$cr;                       // önek kardeş maddeden birebir
            $writes[] = [$rows->first()->id, implode("\n", $lines)];
        }
        if ($writes && $applied) {
            $problems[] = "kısmen uygulanmış durum ({$applied} düzgün, ".count($writes).' bozuk)';
        }
        if ($problems) {
            if (app()->runningUnitTests()) {
                return; // Test DB'si sıfırdan kurulur; bu yazılar orada yok. Hiçbir şey yazma.
            }
            throw new RuntimeException('Content Truth Batch 2C liste yapısı: ön kontrol başarısız, hiçbir yazıya yazılmadı. '.implode(' | ', $problems));
        }
        if (! $writes) {
            return; // zaten düzeltilmiş — no-op
        }
        DB::transaction(function () use ($writes) {
            foreach ($writes as [$id, $md]) {
                $post = Post::findOrFail($id);
                $post->content_md = $md;   // booted(): content_html yeniden render
                $post->save();
            }
        });
    }

    public function down(): void
    {
        // Bilinçli olarak boş: forward-only içerik düzeltmesi.
    }
};

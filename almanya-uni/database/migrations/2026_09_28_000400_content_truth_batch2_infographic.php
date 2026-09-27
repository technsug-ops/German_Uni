<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Content Truth Sprint — Batch 2 (D — infografik): student-visa-interview (TR/EN/DE) infografiğindeki eski
 * Sperrkonto tutarları.
 *
 * Yazının gövdesi değil, altında render edilen infografik (content_assets, asset_type=infographic_data) hâlâ
 * 2024 öncesi değerleri gösteriyor: 11.208 €/yıl ve 934 €/ay. 01.09.2024'ten beri: 992 €/ay = 11.904 €/yıl.
 *
 * Kimlik (Batch 1C ile aynı): brief numarası sabit KODLANMAZ — slug+locale → post → content_brief_id (üç dilde
 * aynı olmalı) → o dilin render edilen infographic_data varlığı (status ready/published; tam 1 adet olmalı).
 * Değişiklik: infografik JSON'undaki metin değerlerinde birebir alt dize değişimi (başlık/hero/kart alanları).
 * Durum her alt dize için ORİJİNAL veriye göre belirlenir: eski ≥1 ve yeni 0 → bekleyen; eski 0 ve yeni ≥1 →
 * uygulanmış; başka her durum ya da kısmi durum → RuntimeException, hiçbir dile yazılmaz. Tek transaction.
 * İkinci çalıştırma no-op. Post alanları ve diğer content_assets'e dokunulmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        $spec = json_decode(<<<'JSON'
{
 "slugs": {
  "tr": "student-visa-interview-common-questions-and-how-to-prepare",
  "en": "student-visa-interview-common-questions-and-how-to-prepare-en",
  "de": "student-visa-interview-common-questions-and-how-to-prepare-de"
 },
 "subs": {
  "tr": [
   {"old": "2024 itibarıyla yıllık 11.208€", "new": "01.09.2024'ten beri yıllık 11.904 €"}
  ],
  "en": [
   {"old": "€11,208", "new": "€11,904"},
   {"old": "Balance for 1 year (2024)", "new": "Balance for 1 year (since 1 September 2024)"},
   {"old": "Current Amount (2024)", "new": "Current Amount (since 1 September 2024)"},
   {"old": "(e.g., €934)", "new": "(e.g., €992)"}
  ],
  "de": [
   {"old": "aktuell ca. 11.208 Euro für ein Jahr ist erforderlich (Stand 2024)", "new": "aktuell 11.904 Euro für ein Jahr ist erforderlich (seit 01.09.2024)"},
   {"old": "Sperrkonto (ca. 11.208€/Jahr)", "new": "Sperrkonto (11.904 €/Jahr)"}
  ]
 }
}
JSON, true, 512, JSON_THROW_ON_ERROR);

        $problems = [];
        $posts = [];
        foreach ($spec['slugs'] as $locale => $slug) {
            $post = DB::table('posts')->where('slug', $slug)->where('locale', $locale)->first(['id', 'content_brief_id', 'translation_group_id']);
            if (! $post) {
                $problems[] = "{$locale}: yazı yok";
                continue;
            }
            $posts[$locale] = $post;
        }
        $briefs = array_values(array_unique(array_map(fn ($p) => (string) $p->content_brief_id, $posts)));
        $groups = array_values(array_unique(array_map(fn ($p) => (string) $p->translation_group_id, $posts)));
        if (count($posts) === 3 && (count($briefs) !== 1 || $briefs[0] === '' || count($groups) !== 1 || $groups[0] === '')) {
            $problems[] = 'yazılar aynı brief/çeviri grubuna bağlı değil';
        }

        $pending = 0;
        $applied = 0;
        $writes = [];
        if (! $problems) {
            foreach ($spec['subs'] as $locale => $subs) {
                $assets = DB::table('content_assets')
                    ->where('content_brief_id', (int) $briefs[0])
                    ->where('asset_type', 'infographic_data')
                    ->where('language', $locale)
                    ->whereIn('status', ['ready', 'published'])
                    ->get(['id', 'body_md']);
                if ($assets->count() !== 1) {
                    $problems[] = "{$locale}: render edilen infographic_data sayısı {$assets->count()} (beklenen 1)";
                    continue;
                }
                $asset = $assets->first();
                $raw = trim((string) $asset->body_md);
                $fenced = (bool) preg_match('/^```/', $raw);
                $data = json_decode(preg_replace('/^```(?:json)?\s*|\s*```$/', '', $raw), true);
                if (! is_array($data)) {
                    $problems[] = "{$locale}: infografik JSON okunamadı";
                    continue;
                }
                $orig = $data;
                $changed = false;
                foreach ($subs as $s) {
                    $o = $this->countSub($orig, $s['old']);
                    $n = $this->countSub($orig, $s['new']);
                    if ($o >= 1 && $n === 0) {
                        $pending++;
                        $data = $this->replaceSub($data, $s['old'], $s['new']);
                        $changed = true;
                    } elseif ($o === 0 && $n >= 1) {
                        $applied++;
                    } else {
                        $problems[] = "{$locale}: alt dize eşleşmedi (eski {$o}, yeni {$n}) «{$s['old']}»";
                    }
                }
                if ($changed) {
                    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                    $writes[$asset->id] = $fenced ? "```json\n{$json}\n```" : $json;
                }
            }
        }
        if ($pending > 0 && $applied > 0) {
            $problems[] = "kısmen uygulanmış tutarsız durum ({$applied} uygulanmış, {$pending} bekleyen)";
        }
        if ($problems) {
            if (app()->runningUnitTests()) {
                return; // Test DB'si sıfırdan kurulur; brief/infografik varlıkları orada yok. Hiçbir şey yazma.
            }
            throw new RuntimeException('Content Truth Batch 2 (D — infografik): ön kontrol başarısız, hiçbir varlığa yazılmadı. '.implode(' | ', $problems));
        }
        if (! $writes) {
            return; // zaten uygulanmış — no-op
        }

        DB::transaction(function () use ($writes) {
            foreach ($writes as $id => $body) {
                DB::table('content_assets')->where('id', $id)->update(['body_md' => $body, 'updated_at' => now()]);
            }
        });
    }

    private function countSub($v, string $needle): int
    {
        if (is_array($v)) {
            return array_sum(array_map(fn ($x) => $this->countSub($x, $needle), $v));
        }

        return is_string($v) ? substr_count($v, $needle) : 0;
    }

    private function replaceSub($v, string $old, string $new)
    {
        if (is_array($v)) {
            return array_map(fn ($x) => $this->replaceSub($x, $old, $new), $v);
        }

        return is_string($v) ? str_replace($old, $new, $v) : $v;
    }

    public function down(): void
    {
        // Bilinçli olarak boş: forward-only içerik düzeltmesi.
    }
};

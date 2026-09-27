<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Content Truth Sprint — Batch 1C: what-is-bafog (TR/EN/DE) infografiğindeki eski BAföG tutarları.
 *
 * Batch 1'de makale gövdesi 992 €'ya çekildi; gövdenin altında render edilen infografik (content_assets,
 * asset_type=infographic_data) hâlâ WS 2022/23 değerlerini gösteriyordu: 934 € azami + EN/DE bileşenler
 * 452 / 360 / 122 (=934). WS 2024/25'ten beri (§ 13, § 13a BAföG): 475 + 380 + 102 (KV) + 35 (PV) = 992 €.
 * Kullanıcı kararı (27.09.2026): toplam + bileşen kartları birlikte güncellenir; başka kart/alan değişmez.
 *
 * Kimlik: brief numarası sabit KODLANMAZ — slug+locale → post → content_brief_id (üç dilde aynı olmalı) →
 * o dilin render edilen infographic_data varlığı (status ready/published; tam 1 adet olmalı).
 * Ön kontrol: her kart production'da görünen BİREBİR value+label ile tam 1 kez bulunmalı; tamamı yeni
 * değerdeyse no-op; eksik varlık, birden fazla varlık, beklenmeyen değer veya kısmi durum → RuntimeException,
 * hiçbir dile yazılmaz. Yazma tek transaction. Post alanları ve diğer content_assets'e dokunulmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        $spec = json_decode(<<<'JSON'
{
 "slugs": {
  "tr": "what-is-bafog-eligibility-and-application-for-international-students",
  "en": "what-is-bafog-eligibility-and-application-for-international-students-en",
  "de": "what-is-bafog-eligibility-and-application-for-international-students-de"
 },
 "edits": {
  "tr": [
   {
    "where": "hero",
    "old": {
     "value": "€934",
     "label": "Aylık Maksimum BAföG Desteği"
    },
    "new": {
     "value": "€992",
     "label": "Aylık Maksimum BAföG Desteği (2024/25 kış döneminden beri)"
    }
   },
   {
    "where": "item",
    "old": {
     "value": "€934",
     "label": "Maksimum Aylık Destek"
    },
    "new": {
     "value": "€992",
     "label": "Maksimum Aylık Destek (WS 2024/25'ten beri)"
    }
   }
  ],
  "en": [
   {
    "where": "hero",
    "old": {
     "value": "€934/month",
     "label": "Maximum monthly BAföG support for students living outside their parents' home (as of Winter Semester 2022/23)."
    },
    "new": {
     "value": "€992/month",
     "label": "Maximum monthly BAföG support for students living outside their parents' home (since Winter Semester 2024/25)."
    }
   },
   {
    "where": "item",
    "old": {
     "value": "€452/month",
     "label": "Basic Allowance"
    },
    "new": {
     "value": "€475/month",
     "label": "Basic Allowance"
    }
   },
   {
    "where": "item",
    "old": {
     "value": "Up to €360/month",
     "label": "Housing (not with parents)"
    },
    "new": {
     "value": "Up to €380/month",
     "label": "Housing (not with parents)"
    }
   },
   {
    "where": "item",
    "old": {
     "value": "Up to €122/month",
     "label": "Health Insurance"
    },
    "new": {
     "value": "Up to €137/month",
     "label": "Health Insurance"
    }
   },
   {
    "where": "item",
    "old": {
     "value": "€934/month",
     "label": "Total Maximum"
    },
    "new": {
     "value": "€992/month",
     "label": "Total Maximum (since WS 2024/25)"
    }
   }
  ],
  "de": [
   {
    "where": "hero",
    "old": {
     "value": "934 €",
     "label": "Maximaler monatlicher Förderhöchstsatz (seit WS 2022/23)"
    },
    "new": {
     "value": "992 €",
     "label": "Maximaler monatlicher Förderhöchstsatz (seit dem Wintersemester 2024/25)"
    }
   },
   {
    "where": "item",
    "old": {
     "value": "934 €/Monat",
     "label": "Förderhöchstsatz"
    },
    "new": {
     "value": "992 €/Monat",
     "label": "Förderhöchstsatz (seit WS 2024/25)"
    }
   },
   {
    "where": "item",
    "old": {
     "value": "360 €/Monat",
     "label": "Wohnpauschale"
    },
    "new": {
     "value": "380 €/Monat",
     "label": "Wohnpauschale"
    }
   },
   {
    "where": "item",
    "old": {
     "value": "122 €/Monat",
     "label": "Kranken-/Pflegevers."
    },
    "new": {
     "value": "137 €/Monat",
     "label": "Kranken-/Pflegevers."
    }
   }
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
            foreach ($spec['edits'] as $locale => $edits) {
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
                $changed = false;
                foreach ($edits as $e) {
                    $old = $this->find($data, $e['where'], $e['old']);
                    $new = $this->find($data, $e['where'], $e['new']);
                    if (count($old) === 1 && count($new) === 0) {
                        $pending++;
                        $this->set($data, $old[0], $e['new']);
                        $changed = true;
                    } elseif (count($old) === 0 && count($new) === 1) {
                        $applied++;
                    } else {
                        $problems[] = "{$locale}: kart eşleşmedi (eski: ".count($old).', yeni: '.count($new).") «{$e['old']['label']} = {$e['old']['value']}»";
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
            throw new RuntimeException('Content Truth Batch 1C: ön kontrol başarısız, hiçbir varlığa yazılmadı. '.implode(' | ', $problems));
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

    /** hero_stat ya da sections[*].items[*] içinde value+label BİREBİR eşleşen konumlar. */
    private function find(array $data, string $where, array $vl): array
    {
        if ($where === 'hero') {
            $h = $data['hero_stat'] ?? null;

            return (is_array($h) && ($h['value'] ?? null) === $vl['value'] && ($h['label'] ?? null) === $vl['label']) ? [['hero']] : [];
        }
        $hits = [];
        foreach ($data['sections'] ?? [] as $si => $s) {
            foreach ($s['items'] ?? [] as $ii => $it) {
                if (($it['value'] ?? null) === $vl['value'] && ($it['label'] ?? null) === $vl['label']) {
                    $hits[] = ['item', $si, $ii];
                }
            }
        }

        return $hits;
    }

    private function set(array &$data, array $at, array $vl): void
    {
        if ($at[0] === 'hero') {
            $data['hero_stat']['value'] = $vl['value'];
            $data['hero_stat']['label'] = $vl['label'];

            return;
        }
        $data['sections'][$at[1]]['items'][$at[2]]['value'] = $vl['value'];
        $data['sections'][$at[1]]['items'][$at[2]]['label'] = $vl['label'];
    }

    public function down(): void
    {
        // Bilinçli olarak boş: forward-only içerik düzeltmesi.
    }
};

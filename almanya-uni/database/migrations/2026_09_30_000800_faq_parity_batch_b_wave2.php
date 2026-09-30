<?php

use App\Models\Faq;
use App\Models\FaqQualityAtlas;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * FAQ Parity Project — Batch B, Wave 2 (FAQ Atlas audit 2026-09-28): 17 Batch-B kümesi (#7–#23) + 2 ek P0 kümesi
 * (almanyadaki-tu9-universiteleri…, studienkollege-gitmeden-veya…; atlas'ta OUTSIDE_BATCH, sınıfları değişmez).
 * Chatbot smoke testinde (30.09.2026) yanlış bilgi üreten kaynaklar: "H+ = tam denklik / direkt kabul", H kodlarının
 * lise diplomasına uygulanması, "Türk lise mezunları genellikle Studienkolleg'e gider", "YKS 200-250 / 450+",
 * "bazı eyaletlerde sadece YKS yeter", ZAB 150-200 €, VPD her yerde / tek VPD / posta, uydurma not örnekleri, TestAS/DAAD muafiyeti.
 * Doğru (F1–F5, 29–30.09.2026; anabin Türkiye kuralları + kurum statüsü, DAAD kabul veritabanı, ZAB, uni-assist, KMK):
 * okul diploması YKS puanı/yerleşme/Türkiye'deki öğrenimle değerlendirilir (genel lise + SAY/SÖZ/EA/DİL > 180 +
 * fakültede ≥4 yıllık programa yerleşme → alana bağlı doğrudan giriş; 170 yalnız 2020; yüksekokul → yalnız FH; meslek
 * lisesi → alana yönelik Studienkolleg; önlisans + TYT > 150 → doğrudan; MYO 1 yıl → Studienkolleg yalnız FH;
 * açıköğretim 2 yıl → doğrudan; lisans → tüm alanlar; yerleşmesiz → resmî genel kural yok); H kodları kurum statüsü;
 * ZAB Zeugnisbewertung 208 € / 104 €, kabul hakkı değil; VPD üniversiteye özel, 1 yıl, kabul değil, başvuru dijital;
 * 75 € / 30 €; modifiye Bavyera formülü (Nmax/Nmin anabin'den, tek ondalık, yuvarlanmaz).
 *
 * Kapsam: "batch" kümeleri atlas'ta (audit 2026-09-28) proposed_batch = B, "extra" kümeleri atlas'ta var olmalı (batch
 * sınıfları değişmez; atlas'a yazılmaz). EN/DE kardeşi olmayan 4 kümeye İngilizce slug'lı EN/DE kaydı oluşturulur.
 * Motor Wave 1 ile aynı (2026_09_30_000300): eski durum = canlı H1 + görünen gövdenin ilk 100 karakteri (boşsa < 80
 * karakter); yeni kayıt TR kardeşinin grubunda; slug çakışması / farklı küme / beklenmeyen durum / kısmi durum →
 * RuntimeException, yazım yok; tek transaction + yazım sonrası doğrulama; ikinci çalıştırma no-op.
 */
return new class extends Migration
{
    public const SPEC = 'database/migrations/data/faq_parity_batch_b_wave2_2026_09_30.json';

    public const SPEC_SHA256 = 'fc151bbde17b233fcf33d30609594504c642aa4b87f47d0aaa09eadc9e48bb02';

    public function up(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $raw = file_get_contents(base_path(self::SPEC));
        if (hash('sha256', str_replace("\r\n", "\n", (string) $raw)) !== self::SPEC_SHA256) {
            throw new RuntimeException('FAQ Parity Batch B Wave 2: spec dosyasının SHA-256 değeri beklenenle uyuşmuyor, hiçbir şey yazılmadı.');
        }

        $result = $this->run(json_decode($raw, true, 512, JSON_THROW_ON_ERROR));
        Log::info('FAQ Parity Batch B Wave 2: '.$result);
    }

    /** Ön kontrol + yazım. Dönüş: "applied: …" ya da "noop". Sorun varsa RuntimeException (yazım yok). */
    public function run(array $spec): string
    {
        $problems = [];
        $plan = [];
        $states = ['pending' => 0, 'applied' => 0];

        // 1) Kapsam: "batch" kümeleri atlas'ta bu denetimin Batch B'sinde; "extra" kümeleri atlas'ta (herhangi bir sınıfta) olmalı
        $specSlugs = array_column($spec['clusters'], 'tr_slug');
        $batchSlugs = array_column(array_filter($spec['clusters'], fn ($c) => ($c['scope'] ?? 'batch') === 'batch'), 'tr_slug');
        $extraSlugs = array_column(array_filter($spec['clusters'], fn ($c) => ($c['scope'] ?? 'batch') === 'extra'), 'tr_slug');
        if (! Schema::hasTable('faq_quality_atlas')) {
            $problems[] = 'faq_quality_atlas tablosu yok';
        } else {
            $inBatch = FaqQualityAtlas::where('audit_label', $spec['audit_label'])
                ->where('proposed_batch', $spec['proposed_batch'])
                ->whereIn('tr_slug', $batchSlugs)
                ->pluck('tr_slug')->all();
            $inAtlas = FaqQualityAtlas::where('audit_label', $spec['audit_label'])
                ->whereIn('tr_slug', $extraSlugs)
                ->pluck('tr_slug')->all();
            $outside = array_merge(array_diff($batchSlugs, $inBatch), array_diff($extraSlugs, $inAtlas));
            if (! $specSlugs || $outside || count($specSlugs) !== count(array_unique($specSlugs))
                || count($batchSlugs) + count($extraSlugs) !== count($specSlugs)) {
                $problems[] = "kapsam: spec kümeleri atlas'ta beklenen sınıfta değil ya da tekrarlı ("
                    .implode(',', array_slice($outside, 0, 5)).')';
            }
        }

        $newSlugs = [];
        foreach ($spec['clusters'] as $c) {
            $label = $c['tr_slug'];
            $recs = collect($c['records'])->keyBy('locale');
            $tr = Faq::where('locale', 'tr')->where('slug', $label)->first();
            if (! $tr) {
                $problems[] = "{$label}: TR kaydı yok";
                continue;
            }
            $group = $tr->translation_group_id;
            $p = ['tr' => $tr, 'group' => $group, 'set_group' => null, 'ops' => []];

            foreach (['tr', 'en', 'de'] as $loc) {
                $r = $recs[$loc] ?? null;
                if (! $r) {
                    $problems[] = "{$label}: spec'te {$loc} kaydı yok";
                    continue;
                }
                $tag = "{$label} [{$loc}:{$r['mode']}]";

                if ($r['mode'] === 'create') {
                    if (in_array($r['slug'], $newSlugs, true)) {
                        $problems[] = "{$tag}: slug spec içinde tekrar ediyor";
                        continue;
                    }
                    $newSlugs[] = $r['slug'];
                    $row = Faq::where('slug', $r['slug'])->where('locale', $loc)->first();
                    if ($row) {
                        if ($group && $row->translation_group_id === $group && $this->isNew($row, $r['new'])) {
                            $states['applied']++;
                        } else {
                            $problems[] = "{$tag}: slug başka bir SSS'ye ait (id {$row->id})";
                        }
                        continue;
                    }
                    if (Faq::where('slug', $r['slug'])->exists()) {
                        $problems[] = "{$tag}: slug başka bir dilde kullanılıyor";
                        continue;
                    }
                    if ($group && Faq::where('translation_group_id', $group)->where('locale', $loc)->exists()) {
                        $problems[] = "{$tag}: kümede zaten bir {$loc} kardeşi var (beklenmeyen durum)";
                        continue;
                    }
                    if (! $group) {
                        if (Faq::where('translation_group_id', $c['group_if_missing'])->exists()) {
                            $problems[] = "{$tag}: yedek grup kimliği başka kayıtlarda kullanılıyor";
                            continue;
                        }
                        $p['set_group'] = $c['group_if_missing'];
                    }
                    $states['pending']++;
                    $p['ops'][] = ['create', $loc, $r];
                    continue;
                }

                $row = $loc === 'tr' ? $tr : Faq::where('slug', $r['slug'])->where('locale', $loc)->first();
                if (! $row) {
                    $problems[] = "{$tag}: kayıt yok";
                    continue;
                }
                if ($loc !== 'tr' && (! $group || $row->translation_group_id !== $group)) {
                    $problems[] = "{$tag}: kayıt TR ile aynı kümede değil (grup {$row->translation_group_id})";
                    continue;
                }

                if ($r['mode'] === 'keep') {
                    if (! $this->isOld($row, $r['old'])) {
                        $problems[] = "{$tag}: TR eski durumu (soru/gövde) canlı taramayla uyuşmuyor";
                    }
                    continue;
                }

                if ($r['mode'] === 'remove') {
                    $md = (string) $row->answer_md;
                    $hits = array_sum(array_map(fn ($s) => substr_count($md, $s), $r['remove']));
                    // Çapa: soru aynı + silinmeyecek komşu cümle tam bir kez (gövdenin başı silinen cümleyi içerebilir).
                    $anchored = $this->ws((string) $row->question) === $r['old']['question'] && substr_count($md, $r['anchor']) === 1;
                    if ($anchored && $this->isOld($row, $r['old']) && collect($r['remove'])->every(fn ($s) => substr_count($md, $s) === 1)) {
                        $states['pending']++;
                        $p['ops'][] = ['remove', $loc, $r, $row];
                    } elseif ($anchored && $hits === 0) {
                        $states['applied']++;
                    } else {
                        $problems[] = "{$tag}: silinecek cümle beklenen durumda değil (eşleşme {$hits})";
                    }
                    continue;
                }

                // rewrite (tr) / update (en, de)
                if ($this->isNew($row, $r['new'])) {
                    $states['applied']++;
                } elseif ($this->isOld($row, $r['old'])) {
                    $states['pending']++;
                    $p['ops'][] = ['update', $loc, $r, $row];
                } else {
                    $problems[] = "{$tag}: beklenmeyen eski durum (id {$row->id})";
                }
            }
            $plan[] = $p;
        }

        if (! $problems && $states['pending'] > 0 && $states['applied'] > 0) {
            $problems[] = "kısmen uygulanmış durum ({$states['applied']} uygulanmış, {$states['pending']} bekleyen)";
        }
        if ($problems) {
            throw new RuntimeException('FAQ Parity Batch B Wave 2: ön kontrol başarısız, hiçbir şey yazılmadı. '.implode(' | ', array_slice($problems, 0, 40)));
        }
        if ($states['pending'] === 0) {
            return 'noop';
        }

        $counts = ['tr' => 0, 'update' => 0, 'create' => 0];
        DB::transaction(function () use ($plan, &$counts) {
            foreach ($plan as $p) {
                $tr = $p['tr'];
                $group = $p['group'];
                if ($p['set_group']) {
                    // Yalnız küme kimliği: TR içeriği değişmez → updated_at'e dokunulmaz.
                    $group = $p['set_group'];
                    DB::table('faqs')->where('id', $tr->id)->whereNull('translation_group_id')->update(['translation_group_id' => $group]);
                }
                foreach ($p['ops'] as $op) {
                    [$kind, $loc, $r] = $op;
                    if ($kind === 'create') {
                        $m = new Faq;
                        $m->locale = $loc;
                        $m->translation_group_id = $group;
                        $m->faq_topic_id = $tr->faq_topic_id;
                        $m->intent = $tr->intent;
                        $m->category = $tr->category;
                        $m->sort_order = $tr->sort_order;
                        $m->is_featured = $tr->is_featured;
                        $m->is_published = true;
                        $m->slug = $r['slug'];
                        $m->question = $r['new']['question'];
                        $m->answer_md = $r['new']['answer_md'];   // booted(): answer_html + has_answer + answer_minutes
                        $m->save();
                        $counts['create']++;
                    } elseif ($kind === 'remove') {
                        $m = Faq::findOrFail($op[3]->id);
                        $md = (string) $m->answer_md;
                        foreach ($r['remove'] as $s) {
                            $md = preg_replace('/[ \t]*'.preg_quote($s, '/').'/u', '', $md, 1);
                        }
                        $m->answer_md = $md;
                        $m->save();
                        $counts['tr']++;
                    } else {
                        $m = Faq::findOrFail($op[3]->id);
                        $m->question = $r['new']['question'];
                        $m->answer_md = $r['new']['answer_md'];
                        $m->save();
                        $counts[$loc === 'tr' ? 'tr' : 'update']++;
                    }
                }

                // Transaction içi doğrulama: küme artık tamamen yeni durumda mı?
                $siblings = Faq::where('translation_group_id', $group)->get()->groupBy('locale');
                foreach ($p['ops'] as [$kind, $loc, $r]) {
                    $row = $siblings[$loc] ?? collect();
                    $ok = $row->count() === 1 && $row->first()->has_answer && $row->first()->answer_html
                        && ($kind === 'remove'
                            ? ! str_contains((string) $row->first()->answer_md, 'birkaç hafta')
                            : $this->isNew($row->first(), $r['new']) && $row->first()->slug === $r['slug']);
                    if (! $ok) {
                        throw new RuntimeException("FAQ Parity Batch B Wave 2: yazım sonrası doğrulama başarısız ({$tr->slug} / {$loc}), geri alındı.");
                    }
                }
            }
        });

        return "applied: TR {$counts['tr']}, EN/DE güncellenen {$counts['update']}, EN/DE oluşturulan {$counts['create']}";
    }

    private function isNew(Faq $row, array $new): bool
    {
        return $row->question === $new['question'] && $row->answer_md === $new['answer_md'];
    }

    private function isOld(Faq $row, array $old): bool
    {
        if ($this->ws((string) $row->question) !== $old['question']) {
            return false;
        }
        $visible = $this->visible((string) $row->answer_html);

        return $old['visible'] === 'empty'
            ? mb_strlen($visible) < 80
            : str_contains($visible, $old['head']);
    }

    /** Sayfada görünen cevap metni (canlı taramadaki çıkarımla aynı: etiket → boşluk, entity çöz, boşluk sadeleştir). */
    private function visible(string $html): string
    {
        return $this->ws(html_entity_decode(preg_replace('/<[^>]+>/u', ' ', $html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function ws(string $s): string
    {
        return trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $s));
    }

    public function down(): void
    {
        // Bilinçli olarak boş: forward-only içerik düzeltmesi.
    }
};

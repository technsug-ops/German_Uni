<?php

namespace App\Support;

use App\Models\Faq;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * EN FAQ içerik paketi (database/data/faq-en/*.json): mevcut EN kayıtlarının onarımı + TR-only gruplara EN kardeş.
 *
 * plan()  — hiçbir şey yazmaz; yapılacak her işlemi ve her sorunu listeler (önizleme, idempotans kanıtı).
 * apply() — plan'ı tek transaction'da uygular. Sorun varsa hiçbir şey yazılmaz.
 *
 * Kurallar:
 * - Onarım: EN kaydı id ile bulunur; soru BEKLENEN eski metinle birebir eşleşmiyorsa (ve yeni metinle de değilse)
 *   sessizce üzerine yazılmaz → sorun. Kayıt id'si ve çeviri grubu korunur.
 * - Oluşturma: TR kaydı id + soru ile doğrulanır; grup ve konu TR kaydından alınır (sabit UUID yok). Grupta EN kayıt
 *   (yayında ya da taslak) varsa yeni kayıt açılmaz: aynı içerikse "uygulanmış", farklıysa sorun.
 * - Slug: etkilenen HER grup (onarılan + yeni EN'li) için Faz 2 kuralı: İngilizce sorudan taban, TR = taban,
 *   EN = taban-en, DE = taban-de, ≤ 80 karakter, çakışmada -2…; başka grubun geçerli slug'ı asla alınmaz.
 *   Doğru olan slug yeniden üretilmez (hedefe eşit → dokunulmaz).
 * - Yönlendirme: taşınan her eski slug için dile özgü slug_redirects satırı; değişen gruba işaret eden ESKİ satırlar
 *   son slug'a çevrilir (zincir yok). from_slug başka bir kaydın geçerli slug'ıysa yönlendirme yazılmaz (ele geçirme yok).
 * - kb_chunks.url taşınan kayıtlar için yeniden yazılır (içerik değişen/yeni kayıtlar kb:embed ile ayrıca işlenir).
 * - Test ortamı / boş DB: paketteki hiçbir kayıt yoksa plan boş döner (RefreshDatabase'de migration sessiz geçer).
 */
final class FaqEnglishContentBatch
{
    public static function load(string $path): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("EN FAQ paketi bulunamadı: $path");
        }
        $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        foreach (['repairs', 'creations'] as $k) {
            if (! isset($data[$k]) || ! is_array($data[$k])) {
                throw new RuntimeException("EN FAQ paketi '$k' listesi eksik: $path");
            }
        }

        return $data;
    }

    /**
     * @return array{repairs: array, creations: array, slugs: array, redirects: array, rewrites: array, kb: array, skipped: array, problems: array, empty: bool}
     */
    public static function plan(array $data): array
    {
        $out = ['repairs' => [], 'patches' => [], 'creations' => [], 'slugs' => [], 'redirects' => [], 'rewrites' => [], 'kb' => [], 'skipped' => [], 'problems' => [], 'empty' => false];
        foreach (['faqs', 'faq_topics', 'slug_redirects'] as $t) {
            if (! Schema::hasTable($t)) {
                $out['empty'] = true;

                return $out;
            }
        }
        // Paket bu veritabanına ait mi? (kayıt id'leri ortamlar arasında farklı → slug ya da yeni soru ile tanınır; TR id'ler sabit)
        $refSlugs = array_values(array_filter(array_merge(array_column($data['repairs'], 'en_slug'), array_column($data['repairs'], 'slug'), array_column($data['patches'] ?? [], 'slug'))));
        $refQuestions = array_column($data['repairs'], 'question');
        $refIds = array_column($data['creations'], 'tr_id');
        $known = DB::table('faqs')->where(function ($q) use ($refSlugs, $refQuestions, $refIds) {
            $q->whereIn('id', $refIds ?: [0])->orWhereIn('slug', $refSlugs ?: [''])->orWhere(fn ($w) => $w->where('locale', 'en')->whereIn('question', $refQuestions ?: ['']));
        })->exists();
        if (! $known) {
            $out['empty'] = true; // boş/test DB: paket bu veritabanına ait değil

            return $out;
        }
        $topics = DB::table('faq_topics')->pluck('slug', 'id')->all();
        $all = DB::table('faqs')->orderBy('id')->get(['id', 'locale', 'slug', 'question', 'answer_md', 'translation_group_id', 'faq_topic_id', 'is_published']);
        $byId = $all->keyBy('id');
        $taken = []; // locale → slug → id
        foreach ($all as $r) {
            $taken[$r->locale][$r->slug] = $r->id;
        }
        $groups = [];   // gid → ['en_question' => …, 'members' => [locale => row|planned]]
        $affected = []; // gid → true

        foreach ($data['repairs'] as $i => $rep) {
            // locale (varsayılan en); slug anahtarı 'slug' ya da eski ad 'en_slug'
            $loc = $rep['locale'] ?? 'en';
            $rep['slug'] = $rep['slug'] ?? $rep['en_slug'] ?? null;
            foreach (['slug', 'expect_question', 'question', 'answer_md'] as $k) {
                if (empty($rep[$k])) {
                    $out['problems'][] = "repairs[$i]: '$k' eksik";
                    continue 2;
                }
            }
            if (! in_array($loc, ['tr', 'en', 'de'], true)) {
                $out['problems'][] = "repairs[$i]: geçersiz locale '$loc'";
                continue;
            }
            // Mevcut slug ile (henüz uygulanmamış) ya da yeni soru ile (uygulanmış, slug değişmiş) bulunur
            $row = $all->first(fn ($r) => $r->locale === $loc && $r->slug === $rep['slug'])
                ?? $all->first(fn ($r) => $r->locale === $loc && self::same($r->question, $rep['question']));
            if (! $row) {
                // Bu ortamda kayıt yok (prod/yerel farkı): bu madde atlanır, diğerleri etkilenmez
                $out['skipped'][] = "onarım '{$rep['slug']}' ($loc): kayıt yok (slug ve yeni soru ile bulunamadı) — atlandı";
                continue;
            }
            if (! $row->translation_group_id) {
                $out['problems'][] = "onarım {$loc}#{$row->id}: çeviri grubu yok";
                continue;
            }
            if ($loc !== 'tr' && (self::looksTurkish($rep['question']) || self::looksTurkish($rep['answer_md']))) {
                $out['problems'][] = "onarım {$loc}#{$row->id}: yeni metin Türkçe görünüyor";
                continue;
            }
            $affected[$row->translation_group_id] = true;
            if ($loc === 'en') {
                $groups[$row->translation_group_id]['en_question'] = $rep['question'];
            }
            if (self::same($row->question, $rep['question']) && (string) $row->answer_md === $rep['answer_md']) {
                $out['skipped'][] = "onarım {$loc}#{$row->id}: zaten uygulanmış";
                continue;
            }
            if (! self::same($row->question, $rep['expect_question'])) {
                // Eski içerik beklenenle uyuşmuyor: ÜZERİNE YAZILMAZ, raporlanır; diğer maddeler devam eder
                $out['skipped'][] = "onarım {$loc}#{$row->id}: mevcut soru beklenen eski metinle eşleşmiyor — üzerine yazılmadı: '" . mb_substr($row->question, 0, 70) . "'";
                if ($loc === 'en') {
                    unset($affected[$row->translation_group_id], $groups[$row->translation_group_id]);
                }
                continue;
            }
            $out['repairs'][] = ['id' => $row->id, 'locale' => $loc, 'group' => $row->translation_group_id, 'old_question' => $row->question, 'question' => $rep['question'], 'answer_md' => $rep['answer_md']];
        }

        // Yama: cevap içinde tam bir kez geçen alt dizgi değişimi (soru ve slug değişmez; bağlantılar korunur)
        foreach ($data['patches'] ?? [] as $i => $pt) {
            $loc = $pt['locale'] ?? 'en';
            foreach (['slug', 'expect_question', 'replace'] as $k) {
                if (empty($pt[$k])) {
                    $out['problems'][] = "patches[$i]: '$k' eksik";
                    continue 2;
                }
            }
            $row = $all->first(fn ($r) => $r->locale === $loc && $r->slug === $pt['slug']);
            if (! $row) {
                $out['skipped'][] = "yama '{$pt['slug']}' ($loc): kayıt yok — atlandı";
                continue;
            }
            if (! self::same($row->question, $pt['expect_question'])) {
                $out['skipped'][] = "yama {$loc}#{$row->id}: soru beklenenle eşleşmiyor — atlandı";
                continue;
            }
            $md = (string) $row->answer_md;
            if (collect($pt['replace'])->every(fn ($new, $old) => str_contains($md, $new) && ! str_contains($md, $old))) {
                $out['skipped'][] = "yama {$loc}#{$row->id}: zaten uygulanmış";
                continue;
            }
            $ok = true;
            foreach ($pt['replace'] as $old => $new) {
                if (substr_count($md, $old) !== 1) {
                    $out['skipped'][] = "yama {$loc}#{$row->id}: '" . mb_substr($old, 0, 50) . "…' tam bir kez bulunamadı (" . substr_count($md, $old) . ') — üzerine yazılmadı';
                    $ok = false;
                    break;
                }
            }
            if (! $ok) {
                continue;
            }
            $out['patches'][] = ['id' => $row->id, 'locale' => $loc, 'answer_md' => str_replace(array_keys($pt['replace']), array_values($pt['replace']), $md),
                'summary' => implode(' | ', array_map(fn ($o) => "'" . mb_substr($o, 0, 40) . "…'", array_keys($pt['replace'])))];
        }

        foreach ($data['creations'] as $i => $cre) {
            foreach (['tr_id', 'expect_question', 'topic', 'question', 'answer_md'] as $k) {
                if (empty($cre[$k])) {
                    $out['problems'][] = "creations[$i]: '$k' eksik";
                    continue 2;
                }
            }
            $tr = $byId->get((int) $cre['tr_id']);
            if (! $tr) {
                $out['problems'][] = "oluşturma tr#{$cre['tr_id']}: TR kaydı yok";
                continue;
            }
            if ($tr->locale !== 'tr' || ! self::same($tr->question, $cre['expect_question'])) {
                $out['skipped'][] = "oluşturma tr#{$tr->id}: TR kaydı beklenen soruyla eşleşmiyor ('{$tr->locale}': '" . mb_substr($tr->question, 0, 70) . "') — atlandı";
                continue;
            }
            $assignGroup = null;
            if (! $tr->translation_group_id) {
                // TR-only kayıt henüz bir çeviri grubunda değil (canlıda böyle): yeni grup kimliği atanır, EN kardeş ona bağlanır.
                $assignGroup = (string) \Illuminate\Support\Str::uuid();
                $tr->translation_group_id = $assignGroup; // bellek içi: slug/yönlendirme planı grubu görsün
            }
            if (($topics[$tr->faq_topic_id] ?? null) !== $cre['topic']) {
                $out['problems'][] = "oluşturma tr#{$tr->id}: konu '" . ($topics[$tr->faq_topic_id] ?? '?') . "', paket '{$cre['topic']}' diyor";
                continue;
            }
            if (self::looksTurkish($cre['question']) || self::looksTurkish($cre['answer_md'])) {
                $out['problems'][] = "oluşturma tr#{$tr->id}: yeni EN metin Türkçe görünüyor";
                continue;
            }
            $gid = $tr->translation_group_id;
            $existingEn = $all->first(fn ($r) => $r->translation_group_id === $gid && $r->locale === 'en');
            $affected[$gid] = true;
            $groups[$gid]['en_question'] = $cre['question'];
            if ($existingEn) {
                if (self::same($existingEn->question, $cre['question']) && (string) $existingEn->answer_md === $cre['answer_md']) {
                    $out['skipped'][] = "oluşturma tr#{$tr->id}: EN kardeş en#{$existingEn->id} zaten var (aynı içerik)";
                } else {
                    // Taslak/eşdeğer kayıt varsa yeni kayıt açılmaz; elle karar için raporlanır
                    $out['skipped'][] = "oluşturma tr#{$tr->id}: grupta EN kayıt en#{$existingEn->id} zaten var (yayın=" . (int) $existingEn->is_published . ", içerik farklı) — yeni kayıt açılmadı";
                    unset($affected[$gid], $groups[$gid]);
                }
                continue;
            }
            $dupe = $all->first(fn ($r) => $r->locale === 'en' && self::same($r->question, $cre['question']));
            if ($dupe) {
                $out['skipped'][] = "oluşturma tr#{$tr->id}: aynı İngilizce soru başka grupta var (en#{$dupe->id}) — yeni kayıt açılmadı";
                unset($affected[$gid], $groups[$gid]);
                continue;
            }
            $out['creations'][] = ['tr_id' => $tr->id, 'group' => $gid, 'assign_group' => $assignGroup !== null, 'topic_id' => $tr->faq_topic_id, 'topic' => $cre['topic'], 'question' => $cre['question'], 'answer_md' => $cre['answer_md']];
        }
        if ($out['problems']) {
            return $out;
        }

        // Slug normalizasyonu: etkilenen her grup
        $planned = []; // gid → locale → ['id'=>?, 'slug'=>current|null, 'topic_id'=>…, 'new'=>bool]
        foreach (array_keys($affected) as $gid) {
            foreach ($all->where('translation_group_id', $gid) as $r) {
                if (isset($planned[$gid][$r->locale])) {
                    $out['problems'][] = "grup $gid: '{$r->locale}' dilinde birden fazla kayıt";
                    continue 2;
                }
                $planned[$gid][$r->locale] = ['id' => $r->id, 'slug' => $r->slug, 'topic_id' => $r->faq_topic_id, 'new' => false];
            }
        }
        foreach ($out['creations'] as $c) {
            $planned[$c['group']]['en'] = ['id' => null, 'slug' => null, 'topic_id' => $c['topic_id'], 'new' => true, 'tr_id' => $c['tr_id']];
        }
        $target = fn (string $b, string $loc) => $loc === 'tr' ? $b : "$b-$loc";
        $moves = []; // old slug (per locale) → new
        $ownIds = fn (array $members) => array_filter(array_column($members, 'id'));
        foreach ($planned as $gid => $members) {
            // Taban: paketteki yeni EN soru; yoksa veritabanındaki EN soru (TR/DE onarımı slug'ı değiştirmez); EN yoksa dokunulmaz
            $enQuestion = $groups[$gid]['en_question'] ?? ($all->first(fn ($r) => $r->translation_group_id === $gid && $r->locale === 'en')?->question);
            if ($enQuestion === null) {
                continue;
            }
            $base = SlugRedirects::fromText((string) $enQuestion);
            if ($base === '') {
                $out['problems'][] = "grup $gid: İngilizce sorudan slug üretilemedi";
                continue;
            }
            $own = $ownIds($members);
            $candidate = $base;
            $free = false;
            for ($n = 2; $n < 100; $n++) {
                $free = true;
                foreach ($members as $loc => $m) {
                    $owner = $taken[$loc][$target($candidate, $loc)] ?? null;
                    if ($owner !== null && ! in_array($owner, $own, true)) {
                        $free = false;
                        break;
                    }
                }
                if ($free) {
                    break;
                }
                $candidate = "$base-$n";
            }
            if (! $free) {
                $out['problems'][] = "grup $gid: '$base' için boş slug bulunamadı";
                continue;
            }
            foreach ($members as $loc => $m) {
                $new = $target($candidate, $loc);
                if ($m['new']) {
                    $taken['en'][$new] = 'new';
                    foreach ($out['creations'] as &$c) {
                        if ($c['group'] === $gid) {
                            $c['slug'] = $new;
                        }
                    }
                    unset($c);
                    continue;
                }
                if ($m['slug'] === $new) {
                    continue; // doğru slug yeniden üretilmez
                }
                unset($taken[$loc][$m['slug']]);
                $taken[$loc][$new] = $m['id'];
                $moves[$loc][$m['slug']] = $new;
                $out['slugs'][] = ['id' => $m['id'], 'locale' => $loc, 'old' => $m['slug'], 'new' => $new, 'topic_id' => $m['topic_id']];
            }
        }
        if ($out['problems']) {
            return $out;
        }

        // Yönlendirmeler: eski → son (dile özgü); eski satırların hedefi güncellenir (zincir yok); ele geçirme yok
        $existing = DB::table('slug_redirects')->where('entity', 'faq')->get(['id', 'from_slug', 'to_slug', 'locale']);
        foreach ($out['slugs'] as $mv) {
            $loc = $mv['locale'];
            $hijack = $taken[$loc][$mv['old']] ?? null; // taşındıktan sonra eski slug'ı bu dilde başka biri taşıyor mu?
            if ($hijack !== null) {
                $out['skipped'][] = "yönlendirme {$mv['old']} ($loc): slug başka kaydın geçerli adresi (#{$hijack}) — yazılmadı";
                continue;
            }
            $has = $existing->first(fn ($r) => $r->from_slug === $mv['old'] && $r->locale === $loc);
            if ($has) {
                if ($has->to_slug !== $mv['new']) {
                    $out['rewrites'][] = ['id' => $has->id, 'from' => $mv['old'], 'to' => $mv['new'], 'locale' => $loc];
                }
            } else {
                $out['redirects'][] = ['from' => $mv['old'], 'to' => $mv['new'], 'locale' => $loc];
            }
        }
        $allMoves = [];
        foreach ($moves as $loc => $m) {
            $allMoves += $m; // slug'lar dil ekiyle zaten ayrışık (tr taban, en -en, de -de)
        }
        foreach ($existing as $r) {
            if (isset($allMoves[$r->to_slug]) && ! collect($out['rewrites'])->contains('id', $r->id)) {
                $out['rewrites'][] = ['id' => $r->id, 'from' => $r->from_slug, 'to' => $allMoves[$r->to_slug], 'locale' => $r->locale];
            }
        }
        if (Schema::hasTable('kb_chunks')) {
            foreach ($out['slugs'] as $mv) {
                if (isset($topics[$mv['topic_id']])) {
                    $out['kb'][] = ['id' => $mv['id'], 'locale' => $mv['locale'], 'url' => "/{$mv['locale']}/faq/{$topics[$mv['topic_id']]}/{$mv['new']}"];
                }
            }
        }

        return $out;
    }

    /** Planı uygular; yazılan işlem sayılarını döner. Sorun listesi doluysa RuntimeException (hiçbir şey yazılmaz). */
    public static function apply(array $plan): array
    {
        if ($plan['problems']) {
            throw new RuntimeException('EN FAQ paketi uygulanmadı, hiçbir şey yazılmadı: ' . implode('; ', array_slice($plan['problems'], 0, 12)) . (count($plan['problems']) > 12 ? ' …(' . count($plan['problems']) . ')' : ''));
        }
        $counts = ['repairs' => 0, 'creations' => 0, 'slugs' => 0, 'redirects' => 0, 'rewrites' => 0, 'kb' => 0];
        $counts['patches'] = 0;
        if ($plan['empty'] || (! $plan['repairs'] && ! $plan['patches'] && ! $plan['creations'] && ! $plan['slugs'] && ! $plan['redirects'] && ! $plan['rewrites'])) {
            return $counts;
        }
        DB::transaction(function () use ($plan, &$counts) {
            $now = now();
            foreach ($plan['repairs'] as $r) {
                $faq = Faq::findOrFail($r['id']);
                $faq->question = $r['question'];
                $faq->answer_md = $r['answer_md'];
                self::save($faq, ($r['locale'] ?? 'en') . "#{$r['id']}");
                $counts['repairs']++;
            }
            foreach ($plan['patches'] as $p) {
                $faq = Faq::findOrFail($p['id']);
                $faq->answer_md = $p['answer_md'];
                self::save($faq, "yama {$p['locale']}#{$p['id']}");
                $counts['patches']++;
            }
            $newIds = [];
            foreach ($plan['creations'] as $c) {
                $tr = Faq::findOrFail($c['tr_id']);
                if (! empty($c['assign_group'])) {
                    if ($tr->translation_group_id && $tr->translation_group_id !== $c['group']) {
                        throw new RuntimeException("EN FAQ paketi: tr#{$tr->id} plan ile yazma arasında gruba bağlanmış; yeniden önizleme gerekir");
                    }
                    DB::table('faqs')->where('id', $tr->id)->update(['translation_group_id' => $c['group']]);
                }
                $faq = new Faq([
                    'faq_topic_id' => $c['topic_id'], 'question' => $c['question'], 'slug' => '__slugmove-new-' . $c['tr_id'], 'answer_md' => $c['answer_md'],
                    'intent' => $tr->intent ?: 'community', 'category' => $tr->category, 'is_featured' => false, 'sort_order' => (int) $tr->sort_order, 'is_published' => true,
                ]);
                $faq->locale = 'en';
                $faq->translation_group_id = $c['group'];
                self::save($faq, "tr#{$c['tr_id']} → en");
                $newIds[$c['tr_id']] = [$faq->id, $c['slug']];
                $counts['creations']++;
            }
            foreach ($plan['slugs'] as $mv) {
                DB::table('faqs')->where('id', $mv['id'])->update(['slug' => '__slugmove-' . $mv['id']]);
            }
            foreach ($plan['slugs'] as $mv) {
                DB::table('faqs')->where('id', $mv['id'])->update(['slug' => $mv['new'], 'updated_at' => $now]);
                $counts['slugs']++;
            }
            foreach ($newIds as [$id, $slug]) {
                DB::table('faqs')->where('id', $id)->update(['slug' => $slug]);
            }
            foreach ($plan['redirects'] as $rd) {
                DB::table('slug_redirects')->insert(['entity' => 'faq', 'from_slug' => $rd['from'], 'to_slug' => $rd['to'], 'locale' => $rd['locale'], 'created_at' => $now, 'updated_at' => $now]);
                $counts['redirects']++;
            }
            foreach ($plan['rewrites'] as $rw) {
                DB::table('slug_redirects')->where('id', $rw['id'])->update(['to_slug' => $rw['to'], 'updated_at' => $now]);
                $counts['rewrites']++;
            }
            foreach ($plan['kb'] as $kb) {
                $counts['kb'] += DB::table('kb_chunks')->where('source_type', 'faq')->where('source_id', $kb['id'])->where('locale', $kb['locale'])->update(['url' => $kb['url']]);
            }
        });

        return $counts;
    }

    /** İnsan okur özet (önizleme sayfası ve migrate çıktısı). */
    public static function describe(array $plan): string
    {
        if ($plan['empty']) {
            return "Paket bu veritabanına ait kayıt içermiyor (boş/test DB) — yapılacak işlem yok.\n";
        }
        $l = [];
        $l[] = sprintf('ONARIM: %d | YAMA: %d | OLUŞTURMA: %d | SLUG TAŞIMA: %d | YÖNLENDİRME (yeni): %d | YÖNLENDİRME (hedef güncelleme): %d | KB URL: %d | ATLANAN: %d | SORUN: %d',
            count($plan['repairs']), count($plan['patches'] ?? []), count($plan['creations']), count($plan['slugs']), count($plan['redirects']), count($plan['rewrites']), count($plan['kb']), count($plan['skipped']), count($plan['problems']));
        foreach ($plan['problems'] as $p) {
            $l[] = "  !! $p";
        }
        foreach ($plan['repairs'] as $r) {
            $l[] = '  onar ' . ($r['locale'] ?? 'en') . "#{$r['id']}: '" . mb_substr($r['old_question'], 0, 50) . "' → '{$r['question']}'";
        }
        foreach ($plan['patches'] ?? [] as $p) {
            $l[] = "  yama {$p['locale']}#{$p['id']}: {$p['summary']}";
        }
        foreach ($plan['creations'] as $c) {
            $l[] = "  oluştur tr#{$c['tr_id']} → en [{$c['topic']}] '{$c['question']}' slug=" . ($c['slug'] ?? '?') . (! empty($c['assign_group']) ? ' (TR kaydına yeni çeviri grubu atanır)' : '');
        }
        foreach ($plan['slugs'] as $m) {
            $l[] = "  slug #{$m['id']} ({$m['locale']}): {$m['old']} → {$m['new']}";
        }
        foreach ($plan['redirects'] as $r) {
            $l[] = "  301 [{$r['locale']}] {$r['from']} → {$r['to']}";
        }
        foreach ($plan['rewrites'] as $r) {
            $l[] = "  301 güncelle #{$r['id']} [" . ($r['locale'] ?? 'null') . "] {$r['from']} → {$r['to']}";
        }
        foreach ($plan['skipped'] as $s) {
            $l[] = "  atla: $s";
        }

        return implode("\n", $l) . "\n";
    }

    private static function save(Faq $faq, string $label): void
    {
        try {
            $faq->save();
        } catch (\Throwable $e) {
            throw new RuntimeException("EN FAQ paketi uygulanmadı: $label kaydedilemedi: " . $e->getMessage(), 0, $e);
        }
    }

    /** Metin eşitliği: boşluk/satır sonu ve tipografik kesme işareti farkları yok sayılır (içerik farkı değil). */
    public static function same(?string $a, ?string $b): bool
    {
        $n = fn (?string $s) => trim(preg_replace('/\s+/u', ' ', str_replace(['’', '‘', '`'], "'", (string) $s)));

        return $n($a) === $n($b);
    }

    private static function looksTurkish(string $text): bool
    {
        return Faq::looksTurkish($text);
    }
}

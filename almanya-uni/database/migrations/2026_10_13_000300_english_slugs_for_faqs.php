<?php

use App\Support\SlugRedirects;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * İngilizce URL kuralı — Faz 2: FAQ slug'ları. Her çeviri grubunun İngilizce sorusundan tek bir İngilizce taban slug
 * üretilir; TR = taban, EN = taban-en, DE = taban-de (blog konvansiyonu). Eski slug'lar slug_redirects (entity 'faq')
 * ile kalıcı 301 olur; kb_chunks URL'leri yeniden yazılır (yeniden embed gerekmez).
 *
 * - EN kardeşi olmayan gruplar ve gruba bağlı olmayan kayıtlar DEĞİŞTİRİLMEZ (slug uydurulmaz; raporlanır).
 * - Çakışma: aynı taban slug başka bir gruba düşerse -2, -3 … eklenir; sıra deterministiktir (grup içi en küçük id).
 * - Uzunluk: taban ≤ 80 karakter, kelime sınırında kesilir.
 * - Rerun no-op: hedef slug'ı zaten taşıyan kayıt atlanır; yönlendirme kaydı yalnız yoksa yazılır.
 * - Tek transaction; ön doğrulamada bir sorun varsa hiçbir şey yazılmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['faqs', 'faq_topics', 'slug_redirects'] as $t) {
            if (! Schema::hasTable($t)) {
                return;
            }
        }
        $rows = DB::table('faqs')->orderBy('id')->get(['id', 'locale', 'slug', 'question', 'translation_group_id', 'faq_topic_id']);
        if ($rows->isEmpty()) {
            return;
        }
        $topicSlugs = DB::table('faq_topics')->pluck('slug', 'id')->all();

        // Dil bazında kullanılan slug kümesi (çakışma kontrolü; planlanan yeni slug'lar da eklenir)
        $taken = [];
        foreach ($rows as $r) {
            $taken[$r->locale][$r->slug] = $r->id;
        }

        $plan = [];      // [id, locale, old, new, topic_id]
        $problems = [];
        $groups = $rows->whereNotNull('translation_group_id')->where('translation_group_id', '!=', '')->groupBy('translation_group_id')
            ->sortBy(fn ($items) => $items->min('id'));
        foreach ($groups as $gid => $items) {
            $en = $items->firstWhere('locale', 'en');
            if (! $en) {
                continue; // İngilizce soru yok → slug uydurulmaz
            }
            $base = SlugRedirects::fromText((string) $en->question);
            if ($base === '') {
                $problems[] = "grup $gid: İngilizce sorudan slug üretilemedi (faq {$en->id})";
                continue;
            }
            $byLocale = [];
            foreach ($items as $it) {
                if (isset($byLocale[$it->locale])) {
                    $problems[] = "grup $gid: '{$it->locale}' dilinde birden fazla kayıt";
                    continue 2;
                }
                $byLocale[$it->locale] = $it;
            }
            $target = fn (string $b, string $loc) => $loc === 'tr' ? $b : "$b-$loc";

            // Zaten uygulanmış mı? (tüm dillerin slug'ı hedefle aynı)
            $applied = collect($byLocale)->every(fn ($it, $loc) => $it->slug === $target($base, $loc));
            if ($applied) {
                continue;
            }
            // Çakışma: hedef slug'lar bu grubun dışında birine aitse sayı eki
            $candidate = $base;
            for ($n = 2; $n < 100; $n++) {
                $free = true;
                foreach ($byLocale as $loc => $it) {
                    $owner = $taken[$loc][$target($candidate, $loc)] ?? null;
                    if ($owner !== null && $owner !== $it->id) {
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
                $problems[] = "grup $gid: '$base' için boş slug bulunamadı";
                continue;
            }
            foreach ($byLocale as $loc => $it) {
                $new = $target($candidate, $loc);
                unset($taken[$loc][$it->slug]);
                $taken[$loc][$new] = $it->id;
                if ($it->slug !== $new) {
                    $plan[] = [$it->id, $loc, $it->slug, $new, $it->faq_topic_id];
                }
            }
        }
        if ($problems) {
            throw new RuntimeException('İngilizce FAQ slug\'ları (Faz 2) uygulanmadı, hiçbir şey yazılmadı: ' . implode('; ', array_slice($problems, 0, 10)) . (count($problems) > 10 ? ' …(' . count($problems) . ')' : ''));
        }
        if (! $plan) {
            return;
        }

        DB::transaction(function () use ($plan, $topicSlugs) {
            $now = now();
            // Unique(locale, slug) çakışmalarını önlemek için önce geçici slug'lara, sonra hedefe (zincir/değiş-tokuş güvenli)
            foreach ($plan as $i => [$id]) {
                DB::table('faqs')->where('id', $id)->update(['slug' => "__slugmove-$id"]);
            }
            foreach ($plan as [$id, $loc, $old, $new, $topicId]) {
                DB::table('faqs')->where('id', $id)->update(['slug' => $new, 'updated_at' => $now]);
                if (! DB::table('slug_redirects')->where('entity', 'faq')->where('from_slug', $old)->exists()) {
                    DB::table('slug_redirects')->insert(['entity' => 'faq', 'from_slug' => $old, 'to_slug' => $new, 'created_at' => $now, 'updated_at' => $now]);
                }
                if (Schema::hasTable('kb_chunks') && isset($topicSlugs[$topicId])) {
                    DB::table('kb_chunks')->where('source_type', 'faq')->where('source_id', $id)->where('locale', $loc)
                        ->update(['url' => "/$loc/faq/{$topicSlugs[$topicId]}/$new"]);
                }
            }
        });
    }

    public function down(): void
    {
        // Geri alma yok — URL değişikliği kalıcı, yönlendirmeler korunur.
    }
};

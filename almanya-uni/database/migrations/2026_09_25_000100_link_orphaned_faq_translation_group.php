<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Kopuk SSS çeviri grubunu birleştirir: "Türk lise diplomasının Almanya'da geçerliliği var mı?"
 *
 * KÖK NEDEN (canlı hreflang denetimi, 2026-09-25)
 * TR kaydının translation_group_id'si dolu, 2026-05-24'te üretilen EN ve DE çevirilerinin
 * NULL. Kardeşler grup üzerinden bulunduğu için üç sayfa (üçü de 200) üç ayrı hreflang
 * kümesi gibi davranıyor; her biri yalnızca kendini gösteriyor.
 *
 * YAPILAN: yalnızca translation_group_id eşitlenir. Slug, URL, içerik, yayın durumu ve
 * updated_at değişmez; redirect eklenmez.
 *
 * GÜVENLİK
 *   - Kayıtlar locale + slug + ortak konu ile bulunur (ID ortamdan ortama değişebilir).
 *   - Hedef grup: TR'nin grubu; yoksa EN'in, yoksa DE'nin; hiçbiri yoksa yeni UUID.
 *   - Hedef grupta bu üçü dışında aynı dillerden bir kayıt varsa → exception (bir kümede
 *     aynı dilden iki kayıt olamaz). Hiçbir şey yazılmaz.
 *   - Üçünden hiçbiri yoksa (boş kurulum) atlanır; bir kısmı varsa → exception.
 *   - Idempotent: zaten aynı gruptaysa dokunulmaz.
 */
return new class extends Migration
{
    private const SLUGS = [
        'tr' => 'turk-lise-diplomasinin-almanyada-gecerliligi-var-mi',
        'en' => 'turk-lise-diplomasinin-almanyada-gecerliligi-var-mi-en',
        'de' => 'turk-lise-diplomasinin-almanyada-gecerliligi-var-mi-de',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('faqs')) {
            return;
        }

        $rows = [];
        foreach (self::SLUGS as $locale => $slug) {
            $found = DB::table('faqs')->where('locale', $locale)->where('slug', $slug)->get();
            if ($found->count() > 1) {
                throw new RuntimeException("faq group fix: {$locale}/{$slug} için birden fazla kayıt var");
            }
            if ($found->count() === 1) {
                $rows[$locale] = $found->first();
            }
        }

        if ($rows === []) {
            Log::info('faq group fix: kayıtlar yok (boş kurulum), atlandı');

            return;
        }

        if (count($rows) !== 3) {
            throw new RuntimeException('faq group fix: beklenen 3 dilden yalnızca ' . implode(',', array_keys($rows)) . ' bulundu, HİÇBİR ŞEY YAZILMADI');
        }

        if (count(array_unique(array_map(fn ($r) => $r->faq_topic_id, $rows))) !== 1) {
            throw new RuntimeException('faq group fix: üç kayıt farklı konularda, kardeş oldukları doğrulanamadı');
        }

        $target = $rows['tr']->translation_group_id
            ?: $rows['en']->translation_group_id
            ?: $rows['de']->translation_group_id
            ?: (string) Str::uuid();

        $ids = array_map(fn ($r) => $r->id, $rows);

        // Hedef grupta başka bir kayıt aynı dilden varsa küme bozulur.
        $clash = DB::table('faqs')
            ->where('translation_group_id', $target)
            ->whereNotIn('id', $ids)
            ->whereIn('locale', array_keys(self::SLUGS))
            ->get(['id', 'locale', 'slug']);
        if ($clash->isNotEmpty()) {
            throw new RuntimeException("faq group fix: {$target} grubunda başka kayıtlar var: "
                . $clash->map(fn ($c) => "#{$c->id} {$c->locale}/{$c->slug}")->implode(', '));
        }

        // EN/DE başka bir dolu gruba bağlıysa ve o grupta başka üyeler varsa sessizce koparma.
        foreach ($rows as $locale => $r) {
            if ($r->translation_group_id && $r->translation_group_id !== $target) {
                $others = DB::table('faqs')->where('translation_group_id', $r->translation_group_id)->where('id', '!=', $r->id)->count();
                if ($others > 0) {
                    throw new RuntimeException("faq group fix: {$locale} kaydı #{$r->id} başka bir kümeye ({$r->translation_group_id}) bağlı");
                }
            }
        }

        $changed = [];
        foreach ($rows as $locale => $r) {
            if ($r->translation_group_id !== $target) {
                DB::table('faqs')->where('id', $r->id)->update(['translation_group_id' => $target]);
                $changed[] = "{$locale}#{$r->id} " . var_export($r->translation_group_id, true) . " → {$target}";
            }
        }

        echo 'faq group fix: ' . ($changed === [] ? "zaten tek küme ({$target})" : implode('; ', $changed)) . "\n";
    }

    public function down(): void
    {
        // Bilinçli olarak boş: üç kaydı yeniden koparmak hreflang kümesini bozmak olur.
    }
};

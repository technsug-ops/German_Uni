<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * blog_redirects'i dil bazında tamamlar — farklı dile giden yönlendirmeyi kaldırmanın veri tarafı.
 *
 * Eskiden BlogController satırı yalnız from_slug ile arıyordu: /en/blog/<tr-eski-slug> → TR
 * sayfası (dil değiştiren 301). Artık arama from_slug + İSTEĞİN DİLİ ile yapılıyor. Bu
 * migration, eskiden "başka dil üzerinden" çalışan her adres için AYNI DİLDE doğru hedefi ekler.
 *
 * KURAL (tahmin yok): satır (F, X → T) için T'nin X dilindeki yayında kaydı bulunur; o kaydın
 * çeviri grubunda Y dilinde yayında, AYNI TÜRDE bir kardeş varsa (F, Y → kardeş) eklenir.
 * Eklenmez: Y için satır zaten varsa · Y dilinde F slug'lı yayında bir yazı varsa (o sayfa
 * açılır) · grup/kardeş yoksa (o dilde 404 kalır — başka dile gönderilmez).
 *
 * Zincir: hedefi yayında olmayıp kendisi de yönlendirilen satırlar son hedefe bağlanır
 * (yalnız aynı dilde ve son hedef yayındaysa).
 */
return new class extends Migration
{
    /** Değişiklik öncesi tablonun birebir kopyası (index'ler dahil). */
    public const BACKUP = 'blog_redirects_backup_20260925';

    public function up(): void
    {
        if (! Schema::hasTable('blog_redirects') || ! Schema::hasTable('posts')) {
            return;
        }

        // KURTARMA YEDEĞİ: şemaya dokunmadan önce tablo index'leriyle birlikte kopyalanır
        // (CREATE TABLE … LIKE korur). Yeniden koşuda üzerine yazılmaz → her zaman ÖNCEKİ hâl.
        // Rapor: /admin/ops/redirect-check. Geri dönüş gerekirse kaynak budur.
        if (! Schema::hasTable(self::BACKUP)) {
            DB::statement('CREATE TABLE `' . self::BACKUP . '` LIKE `blog_redirects`');
            DB::statement('INSERT INTO `' . self::BACKUP . '` SELECT * FROM `blog_redirects`');
        }

        // Şema: from_slug TEK BAŞINA unique'ti → bir slug için yalnız BİR dilde satır tutulabiliyordu;
        // /en/… ve /de/… istekleri o tek satırın diline (çoğu zaman TR) gidiyordu. Benzersizlik
        // (from_slug, locale) çiftine taşınır; bileşik indeks from_slug aramasını da karşılar.
        $indexes = collect(DB::select('SHOW INDEX FROM blog_redirects'))->pluck('Key_name')->unique()->all();
        if (in_array('blog_redirects_from_slug_unique', $indexes, true)) {
            Schema::table('blog_redirects', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->dropUnique('blog_redirects_from_slug_unique');
            });
        }
        if (! in_array('blog_redirects_from_slug_locale_unique', $indexes, true)) {
            Schema::table('blog_redirects', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->unique(['from_slug', 'locale'], 'blog_redirects_from_slug_locale_unique');
            });
        }

        $locales = \App\Support\Hreflang::activeLocales();
        $live = fn (string $slug, string $locale) => DB::table('posts')
            ->where('slug', $slug)->where('locale', $locale)->where('is_published', true)
            ->whereNotNull('published_at')->where('published_at', '<=', now())->first();

        $collapsed = 0;
        $added = 0;

        DB::transaction(function () use ($locales, $live, &$collapsed, &$added) {
            // 1) Zincirleri kısalt (aynı dil).
            foreach (DB::table('blog_redirects')->get() as $r) {
                if ($live($r->to_slug, $r->locale)) {
                    continue;
                }
                $to = $r->to_slug;
                for ($i = 0; $i < 5; $i++) {
                    $next = DB::table('blog_redirects')->where('from_slug', $to)->where('locale', $r->locale)->value('to_slug');
                    if (! $next || $next === $r->from_slug) {
                        break;
                    }
                    $to = $next;
                    if ($live($to, $r->locale)) {
                        DB::table('blog_redirects')->where('id', $r->id)->update(['to_slug' => $to]);
                        $collapsed++;
                        break;
                    }
                }
            }

            // 2) Eksik dilleri gerçek kardeşten tamamla.
            foreach (DB::table('blog_redirects')->get() as $r) {
                $target = $live($r->to_slug, $r->locale);
                if (! $target || ! $target->translation_group_id) {
                    continue;
                }
                $isNews = $target->type === 'news';
                foreach ($locales as $y) {
                    if ($y === $r->locale) {
                        continue;
                    }
                    if (DB::table('blog_redirects')->where('from_slug', $r->from_slug)->where('locale', $y)->exists()) {
                        continue;
                    }
                    if ($live($r->from_slug, $y)) {
                        continue;
                    }
                    $sibling = DB::table('posts')
                        ->where('translation_group_id', $target->translation_group_id)
                        ->where('locale', $y)->where('is_published', true)
                        ->whereNotNull('published_at')->where('published_at', '<=', now())
                        ->when($isNews, fn ($q) => $q->where('type', 'news'),
                            fn ($q) => $q->where(fn ($w) => $w->where('type', 'blog')->orWhereNull('type')))
                        ->first();
                    if (! $sibling) {
                        continue;
                    }
                    DB::table('blog_redirects')->insert([
                        'from_slug' => $r->from_slug, 'to_slug' => $sibling->slug, 'locale' => $y,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                    $added++;
                }
            }
        });

        echo "blog_redirects locale-complete: {$added} aynı-dil satırı eklendi, {$collapsed} zincir kısaltıldı\n";
    }

    public function down(): void
    {
        // Bilinçli olarak boş: eklenen satırlar yalnız aynı dilde doğru hedefe gider.
    }
};

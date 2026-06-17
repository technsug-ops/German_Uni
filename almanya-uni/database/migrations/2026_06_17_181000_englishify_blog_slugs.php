<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Tüm blog slug'larını İngilizce'ye geçir (kullanıcı kuralı: URL'lerde Türkçe olmasın).
 *
 * Şema: İngilizce taban = EN kardeşin başlığından; tr=taban, en=taban-en, de=taban-de.
 * (slug global unique olduğu için locale eki şart.) EN kardeşi olmayan eski standalone
 * Türkçe postlara elle İngilizce taban verilir. Zaten İngilizce slug'lular atlanır.
 * Her değişen slug için blog_redirects'e eski→yeni 301 kaydı yazılır. Idempotent.
 */
return new class extends Migration
{
    /** EN kardeşi olmayan eski Türkçe standalone postlara elle İngilizce taban. */
    private array $manualBase = [
        6  => 'sperrkonto-2025-blocked-account-germany-visa-guide',
        14 => 'student-affairs-germany-20-hour-rule-tax-health-insurance',
    ];

    public function up(): void
    {
        $groups = Post::whereNotNull('translation_group_id')
            ->get(['id', 'locale', 'slug', 'translation_group_id', 'title'])
            ->groupBy('translation_group_id');

        foreach ($groups as $members) {
            $en = $members->firstWhere('locale', 'en');

            if ($en && trim((string) $en->title) !== '') {
                $base = $this->englishBase($en->title);
            } else {
                // EN kardeş yok: elle harita varsa onu kullan, yoksa atla
                // (ör. zaten İngilizce slug'lı yeni bloglar germany-… → dokunma).
                $base = null;
                foreach ($members as $m) {
                    if (isset($this->manualBase[$m->id])) {
                        $base = $this->manualBase[$m->id];
                        break;
                    }
                }
                if (! $base) {
                    continue;
                }
            }

            foreach ($members as $m) {
                $suffix = $m->locale === 'tr' ? '' : ('-' . $m->locale);
                $new = $base . $suffix;

                // Çakışma koruması (farklı bir post aynı slug'ı tutuyorsa numara ekle)
                $i = 2;
                while (Post::where('slug', $new)->where('id', '!=', $m->id)->exists()) {
                    $new = $base . $suffix . '-' . $i;
                    $i++;
                }

                if ($new === $m->slug) {
                    continue; // zaten doğru
                }

                DB::table('blog_redirects')->updateOrInsert(
                    ['from_slug' => $m->slug],
                    ['to_slug' => $new, 'locale' => $m->locale, 'updated_at' => now(), 'created_at' => now()]
                );
                Post::whereKey($m->id)->update(['slug' => $new]);
            }
        }
    }

    /** Başlıktan temiz, ~11 kelimelik İngilizce slug tabanı. */
    private function englishBase(string $title): string
    {
        $segments = explode('-', Str::slug($title));
        return implode('-', array_slice(array_filter($segments), 0, 11));
    }

    public function down(): void
    {
        // Geri alınmaz — İngilizce-slug kuralı kalıcı (redirect'ler eski URL'leri korur).
    }
};

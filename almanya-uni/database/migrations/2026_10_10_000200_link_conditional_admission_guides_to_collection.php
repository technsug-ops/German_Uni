<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Şartlı kabul rehberi (TR/EN/DE) ve şartlı kabul vizesi yazısı (TR/EN/DE) → şartlı kabul koleksiyonuna bağlamsal link.
 *
 * Akış: rehber → /{dil}/universities/collections/conditional-admission-universities → üniversite profili.
 * Rehberlerde bu 9 kurum hakkında yanlış iddia yok (2026-10-10 canlı tarama; yalnız kaynakça cümlesi), içerik değişmez;
 * yalnız tek paragraf eklenir: rehberde ilk "Bachelor" başlığından önce, vize yazısında son H2'den (sonuç) önce.
 * content_md ve content_html birlikte güncellenir; çapa iki alanda da bulunmazsa ya da link zaten varsa yazı atlanır.
 */
return new class extends Migration
{
    private const COLLECTION = '/universities/collections/conditional-admission-universities';

    private const POSTS = [
        // [locale, slug, yerleşim]
        ['tr', 'germany-conditional-admission-bedingte-zulassung-guide', 'before_bachelor'],
        ['en', 'conditional-admission-germany-bachelor-master-2026-guide', 'before_bachelor'],
        ['de', 'bedingte-zulassung-deutschland-bachelor-master-2026-leitfaden', 'before_bachelor'],
        ['tr', 'conditional-admission-visa-germany-which-merkblatt-and-language-level', 'before_last'],
        ['en', 'conditional-admission-visa-germany-which-merkblatt-and-language-level-en', 'before_last'],
        ['de', 'conditional-admission-visa-germany-which-merkblatt-and-language-level-de', 'before_last'],
    ];

    private const TEXT = [
        'before_bachelor' => [
            'tr' => ['Hangi üniversitelerin bu yolu hangi kapsamda sunduğunu resmî sayfalarına göre ', "Almanya'da şartlı kabul veren üniversiteler", ' sayfasında karşılaştırdık: bazıları bölüme şartlı kabul veriyor, bazıları yalnız dil kursuna kabul ediyor, bazılarında dil belgesi kayıtta isteniyor.'],
            'en' => ['We compared which universities offer this route, and in what scope, using their official pages: ', 'universities offering conditional admission in Germany', '. Some give conditional admission to the degree, some only admit you to a German course, and some simply expect the certificate at enrolment.'],
            'de' => ['Welche Hochschulen diesen Weg in welchem Umfang anbieten, haben wir anhand ihrer offiziellen Seiten verglichen: ', 'bedingte Zulassung an deutschen Hochschulen', '. Manche vergeben eine bedingte Studienzulassung, manche lassen nur zum Deutschkurs zu, und bei manchen ist der Nachweis einfach bei der Einschreibung fällig.'],
        ],
        'before_last' => [
            'tr' => ['Vize başvurusundan önce kabul belgenin türünü netleştir: bölüme şartlı kabul ile dil kursuna kabul farklı belgelerdir. Üniversitelerin hangi yolu sunduğunu ', "Almanya'da şartlı kabul veren üniversiteler", ' sayfasında resmî kaynaklarıyla karşılaştırdık.'],
            'en' => ['Before you apply for the visa, check which kind of letter you have: conditional admission to a degree and admission to a German course are different documents. We compared which route each university offers in ', 'universities offering conditional admission in Germany', ', based on their official pages.'],
            'de' => ['Kläre vor dem Visumantrag, welche Art von Bescheid du hast: eine bedingte Studienzulassung und eine Zulassung zum Deutschkurs sind verschiedene Dokumente. Welchen Weg die einzelnen Hochschulen anbieten, zeigt unser Vergleich ', 'bedingte Zulassung an deutschen Hochschulen', ', anhand ihrer offiziellen Seiten.'],
        ],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }

        foreach (self::POSTS as [$locale, $slug, $placement]) {
            $post = DB::table('posts')->where('slug', $slug)->where('locale', $locale)->first(['id', 'content_md', 'content_html']);
            if (! $post) {
                Log::warning("conditional-admission-guide-link: $locale/$slug bulunamadı");
                continue;
            }
            $href = '/' . $locale . self::COLLECTION;
            if (str_contains((string) $post->content_md, $href) || str_contains((string) $post->content_html, $href)) {
                continue; // zaten bağlı
            }

            [$before, $label, $after] = self::TEXT[$placement][$locale];
            $md = $this->insertMd((string) $post->content_md, $placement, "{$before}[{$label}]({$href}){$after}");
            $html = $this->insertHtml((string) $post->content_html, $placement,
                '<p>' . e($before) . '<a href="' . e($href) . '">' . e($label) . '</a>' . e($after) . '</p>');

            if ($md === null || $html === null) {
                Log::warning("conditional-admission-guide-link: $locale/$slug çapa bulunamadı, atlandı");
                continue;
            }

            DB::transaction(fn () => DB::table('posts')->where('id', $post->id)->update([
                'content_md' => $md,
                'content_html' => $html,
                'updated_at' => now(),
            ]));
        }
    }

    /** H2 satırlarından çapayı seçer; ilk H2'nin önüne asla eklemez (giriş bütünlüğü). */
    private function insertMd(string $md, string $placement, string $paragraph): ?string
    {
        $lines = preg_split('/\R/u', $md);
        $h2 = array_keys(array_filter($lines, fn ($l) => preg_match('/^##\s(?!#)/u', $l)));
        $target = $this->pick($h2, fn ($i) => (bool) preg_match('/bachelor/iu', $lines[$i]), $placement);
        if ($target === null) {
            return null;
        }
        array_splice($lines, $target, 0, [$paragraph, '']);

        return implode("\n", $lines);
    }

    private function insertHtml(string $html, string $placement, string $paragraph): ?string
    {
        if (! preg_match_all('/<h2\b[^>]*>.*?<\/h2>/su', $html, $m, PREG_OFFSET_CAPTURE)) {
            return null;
        }
        $heads = $m[0];
        $target = $this->pick(array_keys($heads), fn ($i) => (bool) preg_match('/bachelor/iu', strip_tags($heads[$i][0])), $placement);
        if ($target === null) {
            return null;
        }
        $offset = $heads[$target][1];

        return substr($html, 0, $offset) . $paragraph . "\n" . substr($html, $offset);
    }

    /** before_bachelor: "Bachelor" geçen ilk H2 (ilk H2 değilse); before_last: son H2 (en az 3 H2 varsa). */
    private function pick(array $indexes, callable $isBachelor, string $placement): ?int
    {
        if ($placement === 'before_last') {
            return count($indexes) >= 3 ? end($indexes) : null;
        }
        foreach ($indexes as $pos => $i) {
            if ($pos > 0 && $isBachelor($i)) {
                return $i;
            }
        }

        return null;
    }

    public function down(): void
    {
        // İçerik verisi.
    }
};

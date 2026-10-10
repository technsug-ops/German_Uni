<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SEO Growth Batch 2 — destekleyici rehberler: yüksek lisans rehberi ve öğrenim ücreti rehberi (TR/EN/DE) → TU Berlin,
 * TUM, TU Darmstadt, Heidelberg ve Freiburg profilleri.
 *
 * 1) Her rehbere bir bağlantı paragrafı eklenir (yüksek lisans: "6." ile başlayan H2'nin önü; ücret: "2." ile başlayan
 *    H2'nin önü; canlıdaki EN ücret rehberinde tek H2 olduğundan orada sona).
 * 2) Ücret rehberinde yeni paragrafla çelişen eski genellemeler ("14 eyalette ücretsiz" vb.) yalnız ilgili cümle
 *    düzeyinde düzeltilir: Baden-Württemberg dışında genel eyalet ücreti yok, ama TUM 2024/25 kış döneminden beri
 *    AB/AEA dışı yeni öğrencilerden kendi ücretini alıyor (kaynak tum.de, kontrol 2026-10-10). Yalnız bugünkü tutarlar
 *    verilir; TUM'un 2027/28 artışı burada yer almaz (profilde gelecek tarihiyle duruyor). Öğrenim ücreti
 *    Semesterbeitrag'dan ayrı tutulur.
 *
 * Kayıtlar Eloquent ile kaydedilir; Post::saving content_html'i content_md'den yeniden üretir.
 *
 * Güvenlik: önce altı yazı doğrulanır. Yazı yoksa, çapa yoksa, düzeltilecek eski cümle de yeni cümle de yoksa ya da
 * düzeltme sonrası "14 eyalet" genellemesi kalırsa RuntimeException → hiçbir şey yazılmaz. Rerun no-op. Altı yazının
 * hiçbirinin olmaması yalnız testing ortamında (APP_ENV=testing; CI testleri dahil) "iş yok" sayılır; production'da hatadır.
 */
return new class extends Migration
{
    private const POSTS = [
        // [locale, slug, grup, çapa regex (H2 metni), çapa yoksa sona ekle?]
        ['tr', 'doing-a-masters-in-germany-2026-a-z-guide', 'master', '/^\s*6\./u', false],
        ['en', 'doing-a-masters-in-germany-2026-a-z-guide-en', 'master', '/^\s*6\./u', false],
        ['de', 'doing-a-masters-in-germany-2026-a-z-guide-de', 'master', '/^\s*6\./u', false],
        ['tr', 'is-university-free-in-germany-2026-real-costs', 'fees', '/^\s*2\./u', false],
        ['en', 'is-university-free-in-germany-2026-real-costs-en', 'fees', '/^\s*2\./u', true],
        ['de', 'is-university-free-in-germany-2026-real-costs-de', 'fees', '/^\s*2\./u', false],
    ];

    private const SLUGS = [
        'tub' => 'technische-universitat-berlin',
        'tum' => 'technische-universitat-munchen-partner-019ddbba',
        'tud' => 'technische-universitat-darmstadt-q310695',
        'hd' => 'universitat-heidelberg-partner-019ddbba',
        'fr' => 'albert-ludwigs-universitat-freiburg-im-breisgau-partner-019ddbba',
    ];

    /** Paragraflar; {tub} vb. yer tutucular profil adresine çevrilir. Paragraf işareti olarak TUM profil adresi kullanılır. */
    private const TEXT = [
        'master' => [
            'tr' => 'Beş büyük üniversitenin yüksek lisans başvuru yolunu resmî sayfalarına göre profillerinde özetledik: [TU Berlin]({tub}) (uni-assist VPD ve tuPORT), [TUM]({tum}) (TUMonline ve yetenek değerlendirmesi), [TU Darmstadt]({tud}) (TUCaN), [Heidelberg Üniversitesi]({hd}) (heiCO) ve [Freiburg Üniversitesi]({fr}) (üniversitenin kendi portalı).',
            'en' => 'We summarised the Master\'s application route of five major universities, based on their official pages, in their profiles: [TU Berlin]({tub}) (uni-assist VPD and tuPORT), [TUM]({tum}) (TUMonline and aptitude assessment), [TU Darmstadt]({tud}) (TUCaN), [Heidelberg University]({hd}) (heiCO) and [University of Freiburg]({fr}) (the university\'s own portal).',
            'de' => 'Den Bewerbungsweg für den Master an fünf großen Universitäten haben wir anhand ihrer offiziellen Seiten in den Profilen zusammengefasst: [TU Berlin]({tub}) (uni-assist-VPD und tuPORT), [TUM]({tum}) (TUMonline und Eignungsverfahren), [TU Darmstadt]({tud}) (TUCaN), [Universität Heidelberg]({hd}) (heiCO) und [Universität Freiburg]({fr}) (eigenes Portal).',
        ],
        'fees' => [
            'tr' => 'Öğrenim ücreti yalnız eyalete değil üniversiteye de bağlı olabilir: [TUM]({tum}), 2024/25 kış döneminden itibaren AB/AEA dışından gelen yeni öğrencilerden dönem başına genellikle lisansta 2.000 veya 3.000 €, yüksek lisansta 4.000 veya 6.000 € alıyor; Baden-Württemberg\'deki [Heidelberg]({hd}) ve [Freiburg]({fr}) AB/AEA dışından gelen uluslararası öğrencilerden dönem başına 1.500 € alıyor; [TU Berlin]({tub}) ise sürekli eğitim (weiterbildende) yüksek lisansları dışında öğrenim ücreti almıyor. Dönem katkı payı (Semesterbeitrag) öğrenim ücretinden ayrıdır ve her üniversitede ödenir. Tutarları ve muafiyetleri resmî kaynaklarıyla bu profillerde topladık.',
            'en' => 'Tuition can depend on the university as well as the state: since winter semester 2024/25, [TUM]({tum}) has charged new students from outside the EU/EEA usually €2,000 or €3,000 per semester for Bachelor\'s and €4,000 or €6,000 for Master\'s programmes; [Heidelberg]({hd}) and [Freiburg]({fr}) in Baden-Württemberg charge international students from outside the EU/EEA €1,500 per semester; [TU Berlin]({tub}) charges no tuition except for continuing-education (weiterbildende) Master\'s programmes. The semester contribution (Semesterbeitrag) is separate from tuition and is paid everywhere. The amounts and exemptions, with official sources, are in these profiles.',
            'de' => 'Studiengebühren hängen nicht nur vom Bundesland, sondern auch von der Hochschule ab: Die [TUM]({tum}) verlangt seit dem Wintersemester 2024/25 von neuen Studierenden aus Nicht-EU/EWR-Staaten pro Semester meist 2.000 oder 3.000 € im Bachelor und 4.000 oder 6.000 € im Master; [Heidelberg]({hd}) und [Freiburg]({fr}) in Baden-Württemberg verlangen von internationalen Studierenden aus Nicht-EU/EWR-Staaten 1.500 € pro Semester; die [TU Berlin]({tub}) erhebt außer für weiterbildende Master keine Studiengebühren. Der Semesterbeitrag ist keine Studiengebühr und fällt überall zusätzlich an. Beträge und Befreiungen mit offiziellen Quellen stehen in diesen Profilen.',
        ],
    ];

    /**
     * Ücret rehberinde çelişen eski genellemelerin cümle düzeyinde düzeltmesi: alan => [[[eski varyantlar], yeni], ...].
     * Eski varyantlar düz metindir; eşleşmede markdown kalın işaretleri (*) göz ardı edilir. Birden çok varyant lokal
     * kopya ile canlıdaki metin farkından gelir (DE, canlı sayfa 2026-10-10 kontrol edildi).
     */
    private const FIXES = [
        'tr' => [
            'content_md' => [
                [["Bu karar bugün 14 eyalette geçerli. Yani: Bavyera, Berlin, Brandenburg, Bremen, Hamburg, Hessen, Mecklenburg-Vorpommern, Aşağı Saksonya, Kuzey Ren-Vestfalya, Rheinland-Pfalz, Saarland, Saksonya, Sachsen-Anhalt, Schleswig-Holstein, Thüringen — öğrenim ücreti yok."],
                    "Bugün Baden-Württemberg dışındaki eyaletlerde — Bavyera, Berlin, Brandenburg, Bremen, Hamburg, Hessen, Mecklenburg-Vorpommern, Aşağı Saksonya, Kuzey Ren-Vestfalya, Rheinland-Pfalz, Saarland, Saksonya, Sachsen-Anhalt, Schleswig-Holstein, Thüringen — **genel bir eyalet öğrenim ücreti yok.** Bu her üniversitenin ücretsiz olduğu anlamına gelmez: örneğin Bavyera'daki TUM, 2024/25 kış döneminden itibaren AB/AEA dışından gelen yeni öğrencilerden öğrenim ücreti alıyor."],
                [["Master için de aynı kural geçerli: devlet üniversitesinde ücretsiz, Baden-Württemberg'de 1.500 €/sömestre AB dışı."],
                    "Master için de genel kural aynı: Baden-Württemberg dışındaki devlet üniversitelerinin çoğunda **öğrenim ücreti yok**, Baden-Württemberg'de AB dışı öğrenciler için 1.500 €/sömestre. İstisnalar var: örneğin TUM, 2024/25 kış döneminden itibaren AB/AEA dışından gelen yeni yüksek lisans öğrencilerinden dönem başına genellikle 4.000 veya 6.000 € alıyor; sürekli eğitim (weiterbildende) yüksek lisansları da her eyalette ücretli olabilir."],
                [["Hayır, 14 eyalette devlet üniversitelerinde Türk öğrenciler dahil tüm öğrenciler öğrenim ücretinden muaftır."],
                    "**Hayır**, Baden-Württemberg dışındaki eyaletlerde devlet üniversitelerinin çoğu Türk öğrenciler dahil öğrencilerden öğrenim ücreti almaz; ancak örneğin TUM, 2024/25 kış döneminden itibaren AB/AEA dışından gelen yeni öğrencilerden ücret alıyor."],
                [["Devlet üniversitelerinde tıp okumak (Humanmedizin) ücretsiz — sadece Baden-Württemberg'de AB dışı için 1.500 €/sömestre. Bavyera, Berlin, NRW, Hessen tıp fakülteleri tam ücretsiz."],
                    "Devlet üniversitelerinde tıp (Humanmedizin) için çoğu eyalette **genel bir öğrenim ücreti yok** — Baden-Württemberg'de AB dışı öğrenciler 1.500 €/sömestre öder. Üniversiteler kendi ücret kurallarına sahip olabildiği için (örneğin Bavyera'da TUM) tutarı her zaman üniversitenin resmî sayfasından kontrol et."],
                [["2014'ten beri ücretsizlik politikası 14 eyalette istikrarlı. Baden-Württemberg dışında bir eyaletin \"ücret koyacağı\" yönünde 2026 itibarıyla net bir karar yok."],
                    "Baden-Württemberg dışında 2026 itibarıyla genel bir eyalet öğrenim ücreti yok; eyaletler üniversitelere kendi ücretini alma imkânı tanıyabiliyor — örneğin Bavyera'da TUM, 2024/25 kış döneminden itibaren AB/AEA dışından gelen yeni öğrencilerden öğrenim ücreti alıyor."],
            ],
            'excerpt' => [
                [["14 eyalette devlet üniversiteleri ücretsiz, Baden-Württemberg AB dışı öğrencilerden 1.500 €/sömestre alıyor."],
                    "Çoğu eyalette öğrenim ücreti yok; Baden-Württemberg AB dışından 1.500 €/sömestre, TUM kendi ücretini alıyor."],
            ],
            'meta_description' => [
                [["Almanya'da Türk öğrenciler için gerçek maliyet: 14 eyalet ücretsiz, Baden-Württemberg 1.500 €/sömestre."],
                    "Almanya'da gerçek maliyet: çoğu eyalette öğrenim ücreti yok; Baden-Württemberg ve TUM gibi istisnalar var."],
            ],
        ],
        'en' => [
            'excerpt' => [
                [["In 14 Bundesländer, public Universitäten are free, while Baden-Württemberg charges non-EU students 1,500 €/semester."],
                    "Most states charge no tuition; Baden-Württemberg charges non-EU students €1,500/semester and TUM sets its own fees."],
            ],
            'meta_description' => [
                [["The real cost for international students in Germany: 14 Bundesländer are free, Baden-Württemberg charges 1,500 €/semester."],
                    "Real cost of studying in Germany: most states charge no tuition; Baden-Württemberg and TUM are exceptions."],
            ],
        ],
        'de' => [
            'content_md' => [
                [["Diese Regelung gilt heute in 14 Bundesländern. Das heißt: Bayern, Berlin, Brandenburg, Bremen, Hamburg, Hessen, Mecklenburg-Vorpommern, Niedersachsen, Nordrhein-Westfalen, Rheinland-Pfalz, Saarland, Sachsen, Sachsen-Anhalt, Schleswig-Holstein, Thüringen – keine Studiengebühren.",
                    "Diese Entscheidung gilt heute in 14 Bundesländern. Das heißt: Bayern, Berlin, Brandenburg, Bremen, Hamburg, Hessen, Mecklenburg-Vorpommern, Niedersachsen, Nordrhein-Westfalen, Rheinland-Pfalz, Saarland, Sachsen, Sachsen-Anhalt, Schleswig-Holstein, Thüringen – keine Studiengebühren."],
                    "Heute gibt es außerhalb Baden-Württembergs – in Bayern, Berlin, Brandenburg, Bremen, Hamburg, Hessen, Mecklenburg-Vorpommern, Niedersachsen, Nordrhein-Westfalen, Rheinland-Pfalz, Saarland, Sachsen, Sachsen-Anhalt, Schleswig-Holstein und Thüringen – **keine allgemeinen landesweiten Studiengebühren.** Das heißt nicht, dass jede Hochschule gebührenfrei ist: Die TUM in Bayern verlangt seit dem Wintersemester 2024/25 von neuen Studierenden aus Nicht-EU/EWR-Staaten Studiengebühren."],
                [["Auch für den Master gilt die gleiche Regel: kostenlos an staatlichen Universitäten, in Baden-Württemberg 1.500 €/Semester für Nicht-EU-Studierende.",
                    "Auch für Masterstudiengänge gilt die gleiche Regel: kostenlos an staatlichen Universitäten, in Baden-Württemberg 1.500 €/Semester für Nicht-EU-Studierende."],
                    "Für den Master gilt im Grundsatz dieselbe Regel: An den meisten staatlichen Universitäten außerhalb Baden-Württembergs gibt es **keine Studiengebühren**, in Baden-Württemberg zahlen Nicht-EU-Studierende 1.500 €/Semester. Es gibt Ausnahmen: Die TUM verlangt seit dem Wintersemester 2024/25 von neuen Masterstudierenden aus Nicht-EU/EWR-Staaten pro Semester meist 4.000 oder 6.000 €; weiterbildende Master können überall gebührenpflichtig sein."],
                [["Nein, in 14 Bundesländern sind alle Studierenden, einschließlich türkischer Studierender, von Studiengebühren an staatlichen Universitäten befreit."],
                    "**Nein**, außerhalb Baden-Württembergs erheben die meisten staatlichen Universitäten auch von türkischen Studierenden keine Studiengebühren; die TUM verlangt jedoch seit dem Wintersemester 2024/25 von neuen Studierenden aus Nicht-EU/EWR-Staaten Gebühren."],
                [["Ein Medizinstudium (Humanmedizin) an staatlichen Universitäten ist kostenlos – nur in Baden-Württemberg fallen für Nicht-EU-Studierende 1.500 €/Semester an. Die medizinischen Fakultäten in Bayern, Berlin, NRW und Hessen sind komplett kostenlos.",
                    "Ein Medizinstudium (Humanmedizin) an staatlichen Universitäten ist kostenlos – nur in Baden-Württemberg fallen für Nicht-EU-Studierende 1.500 €/Semester an. Die medizinischen Fakultäten in Bayern, Berlin, NRW und Hessen sind vollständig kostenlos."],
                    "Für ein Medizinstudium (Humanmedizin) an staatlichen Universitäten gibt es in den meisten Bundesländern **keine allgemeinen Studiengebühren** – in Baden-Württemberg zahlen Nicht-EU-Studierende 1.500 €/Semester. Da Hochschulen eigene Gebührenregeln haben können (z. B. die TUM in Bayern), prüfe den Betrag immer auf der offiziellen Seite der Universität."],
                [["Seit 2014 ist die Politik der Gebührenfreiheit in 14 Bundesländern stabil. Außerhalb Baden-Württembergs gibt es bis 2026 keine klare Entscheidung, dass ein Bundesland „Gebühren einführen“ wird.",
                    "Seit 2014 ist die Gebührenfreiheit in 14 Bundesländern stabil. Ab 2026 gibt es keine konkrete Entscheidung, dass ein weiteres Bundesland Gebühren einführen wird."],
                    "Außerhalb Baden-Württembergs gibt es 2026 keine allgemeinen landesweiten Studiengebühren; Länder können Hochschulen aber eigene Gebühren erlauben – so verlangt die TUM in Bayern seit dem Wintersemester 2024/25 von neuen Studierenden aus Nicht-EU/EWR-Staaten Studiengebühren."],
            ],
            'excerpt' => [
                [["In 14 Bundesländern sind staatliche Universitäten kostenlos, Baden-Württemberg erhebt von Nicht-EU-Studierenden 1.500 €/Semester."],
                    "Meist keine Studiengebühren; Baden-Württemberg erhebt von Nicht-EU-Studierenden 1.500 €/Semester, die TUM eigene Gebühren."],
            ],
            'meta_description' => [
                [["In 14 Bundesländern sind staatliche Universitäten kostenlos, Baden-Württemberg erhebt von Nicht-EU-Studierenden 1.500 €/Semester."],
                    "Meist keine allgemeinen Studiengebühren an staatlichen Unis; Ausnahmen u. a. Baden-Württemberg und TUM."],
            ],
        ],
    ];

    /** Kolon sınırları (posts.excerpt 280, posts.meta_description 300). */
    private const MAX_LENGTH = ['excerpt' => 280, 'meta_description' => 300];

    /** Düzeltmeden sonra ücret rehberinde kalmaması gereken genelleme. */
    private const LEFTOVER = '/\b14\s+(eyalet|Bundesl)/u';

    public function up(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }
        // Altı yazının hiçbiri yoksa: yalnız testing ortamında (yerel ve GitHub CI testleri, phpunit.xml
        // APP_ENV=testing) "iş yok"; production'da aşağıda hata sayılır.
        $exists = DB::table('posts')->where(function ($q) {
            foreach (self::POSTS as [$locale, $slug]) {
                $q->orWhere(fn ($w) => $w->where('slug', $slug)->where('locale', $locale));
            }
        })->exists();
        if (! $exists && app()->environment('testing')) {
            return;
        }

        $plan = [];
        $problems = [];
        foreach (self::POSTS as [$locale, $slug, $group, $anchor, $append]) {
            $post = Post::where('slug', $slug)->where('locale', $locale)->first();
            if (! $post) {
                $problems[] = "$locale/$slug bulunamadı";
                continue;
            }
            $fields = ['content_md' => (string) $post->content_md, 'excerpt' => (string) $post->excerpt, 'meta_description' => (string) $post->meta_description];

            $marker = "/$locale/universities/" . self::SLUGS['tum'];
            if (! str_contains($fields['content_md'], $marker)) {
                $md = $this->insertMd($fields['content_md'], $anchor, self::fill(self::TEXT[$group][$locale], $locale), $append);
                if ($md === null) {
                    $problems[] = "$locale/$slug çapa bulunamadı";
                    continue;
                }
                $fields['content_md'] = $md;
            }

            if ($group === 'fees') {
                foreach (self::FIXES[$locale] as $field => $rules) {
                    foreach ($rules as $n => [$olds, $new]) {
                        $fixed = $this->fix($fields[$field], $olds, $new);
                        if ($fixed === null) {
                            $problems[] = "$locale/$slug $field düzeltme " . ($n + 1) . ': eski ya da yeni cümle bulunamadı';
                            continue;
                        }
                        $fields[$field] = $fixed;
                    }
                }
                foreach ($fields as $field => $value) {
                    if (preg_match(self::LEFTOVER, $value, $m)) {
                        $problems[] = "$locale/$slug $field: '{$m[0]}' genellemesi kaldı";
                    }
                    if (mb_strlen($value) > (self::MAX_LENGTH[$field] ?? PHP_INT_MAX)) {
                        $problems[] = "$locale/$slug $field kolon sınırını aşıyor";
                    }
                }
            }

            foreach ($fields as $field => $value) {
                if ($value !== (string) $post->{$field}) {
                    $post->{$field} = $value;
                }
            }
            if ($post->isDirty()) {
                $plan[] = $post;
            }
        }

        if ($problems) {
            throw new RuntimeException('Rehberler güncellenmedi, hiçbir şey yazılmadı: ' . implode('; ', $problems));
        }

        DB::transaction(function () use ($plan) {
            foreach ($plan as $post) {
                $post->save();
            }
        });
    }

    private static function fill(string $text, string $locale): string
    {
        return preg_replace_callback('/\{(\w+)\}/', fn ($m) => "/$locale/universities/" . self::SLUGS[$m[1]], $text);
    }

    /** Yeni metin zaten varsa dokunmaz; yoksa eski varyantlardan tam bir kez geçeni değiştirir; ikisi de yoksa null. */
    private function fix(string $text, array $olds, string $new): ?string
    {
        if (str_contains($text, $new)) {
            return $text;
        }
        foreach ($olds as $old) {
            $chars = mb_str_split(preg_replace('/\s+/u', ' ', $old));
            $pattern = '/\**' . implode('\**', array_map(fn ($ch) => $ch === ' ' ? '\s+' : preg_quote($ch, '/'), $chars)) . '\**/u';
            if (preg_match_all($pattern, $text) === 1) {
                return preg_replace_callback($pattern, fn () => $new, $text);
            }
        }

        return null;
    }

    private function insertMd(string $md, string $anchor, string $paragraph, bool $append): ?string
    {
        $lines = preg_split('/\R/u', $md);
        foreach ($lines as $i => $line) {
            if (preg_match('/^##\s(?!#)(.*)$/u', $line, $m) && preg_match($anchor, $m[1])) {
                array_splice($lines, $i, 0, [$paragraph, '']);

                return implode("\n", $lines);
            }
        }

        return $append ? rtrim($md) . "\n\n" . $paragraph . "\n" : null;
    }

    public function down(): void
    {
        // İçerik verisi.
    }
};

<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * SEO Growth Batch 4 — öğrenci şehirleri (Berlin, München, Aachen, Hamburg), yaşam maliyeti rehberi ve iki destekleyici
 * rehberde kısa bağlantı satırı (veri: database/data/city-content/batch4-student-cities-2026-10-11.json; kaynaklar ve
 * kontrol tarihi dosyada, her rakam dönem ve kaynağıyla).
 *
 * Şehir sayfaları (cities.content_blocks{,_en,_de}): intro → kaynaklı giriş + seo_title/seo_description + editorial_lock;
 * quick_facts → doğrulanmış satırlar; eski kaynaksız "Üniversiteler / Konaklama / Ulaşım" bölümleri çıkar, yerine
 * "Nerede okuyabilirim / Nerede kalabilirim / Aylık bütçe" bölümleri, konaklama ve Semesterbeitrag tabloları, kaynaklı
 * cost_of_living kartları, ilk geliş masrafları ve (varsa) istatistik raporu bağlam kutusu gelir; faq/cta yenilenir;
 * external_links'e resmî kaynaklar eklenir; universities_in_city.top_unis gerçek kurum adlarıyla değişir. Genel bakış,
 * gezilecek yerler, öğrenci kültürü, video, galeri, forum ve schema blokları korunur. Yalnız bu dört şehrin paylaşılan
 * kira alanları (avg_rent_*, stw_*), dört Studierendenwerk sağlayıcı kaydı ve mevcut üç yurt kaydı kaynaklı değerlerle
 * güncellenir; toplu fiyat yazımı yok.
 *
 * Ana rehber: TR yazısı (slug korunur) başlık/meta/excerpt/içerik olarak yeniden yazılır; EN/DE kardeşleri aynı çeviri
 * grubunda, aynı yazar ve kategoriyle oluşturulur (yayın tarihi = bu migration'ın çalıştığı an). Destekleyici iki rehber
 * grubunda (en ucuz 20 şehir, WG rehberi) yalnız bir bağlantı satırı eklenir.
 *
 * Güvenlik: önce bütün hedefler doğrulanır (şehir ve dil kolonları, beklenen blok türleri, kaldırılacak başlıklar,
 * sağlayıcı kayıtları, rehberin eski başlığı ve çeviri grubu, destekleyici başlıkların tam bir kez bulunması). Beklenmeyen
 * durum → RuntimeException, hiçbir şey yazılmaz. Zaten uygulanmış hedefler atlanır; tümü uygulanmışsa no-op (updated_at
 * dahil). Hedeflerin hiçbirinin olmaması yalnız testing ortamında "iş yok" sayılır; production'da hatadır. Yazma tek
 * transaction'da. cities:enrich, content:translate-blocks ve cities:cost-fill editorial_lock taşıyan şehirlere yazmaz.
 */
return new class extends Migration
{
    private const FILE = 'database/data/city-content/batch4-student-cities-2026-10-11.json';

    private const COLUMNS = ['tr' => 'content_blocks', 'en' => 'content_blocks_en', 'de' => 'content_blocks_de'];

    private const REQUIRED_TYPES = ['intro', 'quick_facts', 'cost_of_living', 'faq', 'cta'];

    private const MAX_LENGTH = ['title' => 255, 'slug' => 255, 'meta_title' => 255, 'excerpt' => 280, 'meta_description' => 300];

    public function up(): void
    {
        if (! Schema::hasTable('cities') || ! Schema::hasTable('posts') || ! is_file(base_path(self::FILE))) {
            return;
        }
        $data = json_decode(file_get_contents(base_path(self::FILE)), true, 512, JSON_THROW_ON_ERROR);
        $checked = $data['checked'];
        $head = $data['headings'];

        $anyCity = DB::table('cities')->whereIn('slug', array_keys($data['cities']))->exists();
        $anyGuide = DB::table('posts')->whereIn('slug', array_column($data['guide']['posts'], 'slug'))->exists();
        // Test ortamında hedef şehir yoksa iş yok (CI'daki taze DB'de eski migration'dan gelen TR rehber tek başına hedef değildir).
        if (app()->environment('testing') && ! $anyCity) {
            return;
        }

        $problems = [];
        $plan = ['cities' => [], 'providers' => [], 'dorms' => [], 'posts' => [], 'new_posts' => []];

        /* ------------------------------------------------------------------ şehirler */
        foreach ($data['cities'] as $slug => $spec) {
            $row = DB::table('cities')->where('slug', $slug)->first();
            if (! $row) {
                $problems[] = "$slug: şehir kaydı yok";
                continue;
            }
            $decoded = [];
            $locked = [];
            foreach (self::COLUMNS as $loc => $col) {
                $blocks = json_decode((string) $row->{$col}, true);
                $decoded[$loc] = is_array($blocks) ? $blocks : [];
                $locked[$loc] = collect($decoded[$loc])->contains(fn ($b) => is_array($b) && ! empty($b['editorial_lock']));
            }
            if (count(array_filter($locked)) === 3) {
                continue; // zaten uygulanmış → no-op (alan güncellemeleri de aynı transaction'da yapılmıştı)
            }
            if (array_filter($locked)) {
                $problems[] = "$slug: kısmen uygulanmış (" . implode(',', array_keys(array_filter($locked))) . ')';
                continue;
            }
            foreach (self::COLUMNS as $loc => $col) {
                $types = array_map(fn ($b) => $b['type'] ?? null, $decoded[$loc]);
                foreach (self::REQUIRED_TYPES as $type) {
                    if (! in_array($type, $types, true)) {
                        $problems[] = "$slug $col: '$type' bloğu yok";
                    }
                }
                $headings = collect($decoded[$loc])->filter(fn ($b) => ($b['type'] ?? null) === 'section')->pluck('h')->all();
                foreach ($spec['drop_headings'][$loc] as $h) {
                    if (! in_array($h, $headings, true)) {
                        $problems[] = "$slug $col: kaldırılacak '$h' bölümü bulunamadı";
                    }
                }
            }

            $provider = DB::table('housing_providers')->where('slug', $spec['provider']['slug'])->first();
            if (! $provider) {
                $problems[] = "$slug: sağlayıcı '{$spec['provider']['slug']}' yok";
            }
            $dorm = DB::table('student_dorms')->where('city_id', $row->id)->orderBy('id')->first();
            if ($spec['dorm'] && ! $dorm && ! app()->environment('testing')) {
                $problems[] = "$slug: student_dorms kaydı yok";
            }

            $plan['cities'][$slug] = [$row->id, $decoded, $spec];
            if ($provider) {
                $plan['providers'][] = [$provider->id, $spec['provider']];
            }
            if ($spec['dorm'] && $dorm) {
                $plan['dorms'][] = [$dorm->id, $spec['dorm']];
            }
        }

        /* ------------------------------------------------------------------ ana rehber */
        $g = $data['guide'];
        $trSpec = $g['posts']['tr'];
        $tr = Post::where('locale', 'tr')->where('slug', $trSpec['slug'])->first();
        if (! $tr) {
            if (! app()->environment('testing') || $anyGuide) {
                $problems[] = "rehber tr/{$trSpec['slug']}: bulunamadı";
            }
        } else {
            // Çeviri grubu ortamdan okunur: TR rehberi oluşturan migration grubu Str::uuid() ile üretmişti,
            // yani her ortamda (local/CI/prod) farklı. Sabit bir UUID beklemek prod'da doğrulamayı düşürür.
            $group = (string) ($tr->translation_group_id ?: Str::uuid());
            $fields = $this->postFields($trSpec);
            $this->checkLengths("rehber tr", $fields, $problems);
            $applied = collect($fields)->every(fn ($v, $k) => (string) $tr->{$k} === $v);
            if (! $applied) {
                if (! in_array((string) $tr->title, $trSpec['old_titles'], true)) {
                    $problems[] = "rehber tr/{$trSpec['slug']}: beklenmeyen başlık (kısmen uygulanmış ya da farklı içerik)";
                } else {
                    $tr->fill($fields);
                    $tr->translation_group_id = $group;
                    $plan['posts'][] = $tr;
                }
            } elseif (! $tr->translation_group_id) {
                $tr->translation_group_id = $group;
                $plan['posts'][] = $tr;
            }
            foreach (['en', 'de'] as $loc) {
                $spec = $g['posts'][$loc];
                $fields = $this->postFields($spec);
                $this->checkLengths("rehber $loc", $fields, $problems);
                $sibling = Post::where('locale', $loc)->where('slug', $spec['slug'])->first();
                if ($sibling) {
                    if ($sibling->translation_group_id !== $group) {
                        $problems[] = "rehber $loc/{$spec['slug']}: slug başka bir çeviri grubuna ait";
                        continue;
                    }
                    if (collect($fields)->every(fn ($v, $k) => (string) $sibling->{$k} === $v)) {
                        continue; // aynı içerik → no-op
                    }
                    // Prod'daki mevcut EN/DE çeviriler (eski TR metnin çevirisi) yalnız bilinen eski başlıkla yerinde güncellenir;
                    // slug ve ilk yayın tarihi korunur, elle değiştirilmiş içerik ezilmez.
                    if (! in_array((string) $sibling->title, $spec['old_titles'], true)) {
                        $problems[] = "rehber $loc/{$spec['slug']}: mevcut kayıt beklenen içerikten farklı; elle değiştirilmiş içerik ezilmez";
                        continue;
                    }
                    $sibling->fill($fields);
                    $sibling->is_published = true;
                    if ($sibling->published_at) {
                        $plan['posts'][] = $sibling;
                    } else {
                        $plan['new_posts'][] = $sibling; // taslaktı → ilk gerçek yayın anı
                    }
                    continue;
                }
                $other = Post::where('locale', $loc)->where('translation_group_id', $group)->first();
                if ($other) {
                    $problems[] = "rehber $loc: grupta farklı slug'lı kayıt var ({$other->slug})";
                    continue;
                }
                $new = new Post($fields);
                $new->slug = $spec['slug'];
                $new->locale = $loc;
                $new->type = 'blog';
                $new->translation_group_id = $group;
                $new->user_id = $tr->user_id;
                $new->category_id = $tr->category_id;
                $new->is_published = true;
                $plan['new_posts'][] = $new;
            }
        }

        /* ------------------------------------------------------------------ destekleyici rehberler */
        foreach ($data['supporting'] as $s) {
            $found = Post::where('locale', $s['locale'])->whereIn('slug', $s['slugs'])->get();
            if ($found->count() !== 1) {
                if ($found->isEmpty() && app()->environment('testing')) {
                    continue; // CI fixture'ında yoksa iş yok
                }
                $problems[] = "destek {$s['locale']}/{$s['slugs'][0]}: " . ($found->isEmpty() ? 'bulunamadı' : 'birden fazla aday');
                continue;
            }
            $post = $found->first();
            $md = (string) $post->content_md;
            if (str_contains($md, $s['line'])) {
                continue; // zaten eklenmiş
            }
            if (substr_count($md, "\n" . $s['before_heading'] . "\n") !== 1) {
                $problems[] = "destek {$s['locale']}/{$post->slug}: '{$s['before_heading']}' başlığı tam bir kez bulunamadı";
                continue;
            }
            $post->content_md = str_replace("\n" . $s['before_heading'] . "\n", "\n" . $s['line'] . "\n\n" . $s['before_heading'] . "\n", $md);
            $plan['posts'][] = $post;
        }

        if ($problems) {
            throw new RuntimeException('Batch 4 uygulanmadı, hiçbir şey yazılmadı: ' . implode('; ', $problems));
        }

        DB::transaction(function () use ($plan, $head, $checked) {
            $now = now();
            foreach ($plan['cities'] as [$id, $decoded, $spec]) {
                $update = ['updated_at' => $now];
                foreach (self::COLUMNS as $loc => $col) {
                    $update[$col] = json_encode($this->transform($decoded[$loc], $spec, $loc, $head[$loc], $checked), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                foreach ($spec['fields'] as $k => $v) {
                    $update[$k] = $v;
                }
                DB::table('cities')->where('id', $id)->update($update);
            }
            foreach ($plan['providers'] as [$id, $p]) {
                DB::table('housing_providers')->where('id', $id)->update([
                    'price_min' => $p['price_min'], 'price_max' => $p['price_max'], 'total_capacity' => $p['total_capacity'],
                    'waiting_period' => $p['waiting_period'], 'updated_at' => $now,
                ]);
            }
            foreach ($plan['dorms'] as [$id, $d]) {
                DB::table('student_dorms')->where('id', $id)->update([
                    'rent_min_eur' => $d['rent_min_eur'], 'rent_max_eur' => $d['rent_max_eur'], 'waitlist_avg' => $d['waitlist_avg'],
                    'notes_tr' => $d['notes_tr'], 'notes_en' => $d['notes_en'], 'notes_de' => $d['notes_de'], 'updated_at' => $now,
                ]);
            }
            foreach ($plan['posts'] as $post) {
                $post->save();
            }
            foreach ($plan['new_posts'] as $post) {
                $post->published_at = $now; // ilk gerçek web yayını
                $post->save();
            }
        });
    }

    private function postFields(array $spec): array
    {
        return [
            'title' => $spec['title'],
            'meta_title' => $spec['meta_title'],
            'meta_description' => $spec['meta_description'],
            'excerpt' => $spec['excerpt'],
            'content_md' => $spec['md'],
        ];
    }

    private function checkLengths(string $label, array $fields, array &$problems): void
    {
        foreach (self::MAX_LENGTH as $field => $max) {
            if (isset($fields[$field]) && mb_strlen($fields[$field]) > $max) {
                $problems[] = "$label: $field kolon sınırını aşıyor";
            }
        }
    }

    /** Bir dilin bloklarını dönüştürür: eski kaynaksız bölümler çıkar, kaynaklı bloklar quick_facts'ten sonra gelir. */
    private function transform(array $blocks, array $spec, string $loc, array $head, string $checked): array
    {
        $t = $spec['locales'][$loc];
        $drop = $spec['drop_headings'][$loc];
        $out = [];
        foreach ($blocks as $b) {
            $type = $b['type'] ?? null;
            if ($type === 'section' && in_array($b['h'] ?? '', $drop, true)) {
                continue;
            }
            if ($type === 'cost_of_living') {
                continue; // kaynaklı sürümü quick_facts'ten sonra yeniden eklenir
            }
            switch ($type) {
                case 'intro':
                    $out[] = array_merge($b, [
                        'body_md' => $t['intro'], 'seo_title' => $t['seo_title'], 'seo_description' => $t['seo_description'],
                        'editorial_lock' => true, 'verified_at' => $checked, 'sources' => $spec['sources'],
                    ]);
                    break;
                case 'quick_facts':
                    $b['h'] = $head['facts'];
                    $b['items'] = array_map(fn ($f) => ['label' => $f[0], 'value' => $f[1]], $t['facts']);
                    $out[] = $b;
                    array_push($out, ...$this->sourcedBlocks($t, $head, $checked));
                    break;
                case 'faq':
                    $b['h'] = $b['h'] ?? $head['faq'];
                    $b['items'] = array_map(fn ($qa) => ['q' => $qa[0], 'a' => $qa[1]], $t['faq']);
                    $out[] = $b;
                    break;
                case 'cta':
                    $b['h'] = null;
                    $b['body_md'] = $t['cta'];
                    $out[] = $b;
                    break;
                case 'external_links':
                    $urls = array_column($b['items'] ?? [], 'url');
                    foreach ($t['links'] as $link) {
                        if (! in_array($link['url'], $urls, true)) {
                            $b['items'][] = ['url' => $link['url'], 'type' => 'official', 'label' => $link['label']];
                        }
                    }
                    $out[] = $b;
                    break;
                case 'universities_in_city':
                    $b['top_unis'] = $t['top_unis'];
                    $out[] = $b;
                    break;
                case 'schema_jsonld':
                    if (isset($b['data']) && is_array($b['data'])) {
                        $b['data']['description'] = $this->plain($t['intro']);
                    }
                    $out[] = $b;
                    break;
                default:
                    $out[] = $b;
            }
        }

        return $out;
    }

    private function sourcedBlocks(array $t, array $head, string $checked): array
    {
        $section = fn (string $h, string $md) => ['type' => 'section', 'h' => $h, 'body_md' => $md, 'verified_at' => $checked];
        $table = fn (array $tb) => ['type' => 'table', 'h' => $tb['h'], 'headers' => $tb['headers'], 'rows' => $tb['rows'], 'caption' => $tb['caption'], 'verified_at' => $checked];
        [$study, $live, $budget] = $t['sections'];
        $blocks = [
            $section($study[0], $study[1]),
            $section($live[0], $live[1]),
            $table($t['housing_table']),
            $section($budget[0], $budget[1]),
            ['type' => 'cost_of_living', 'h' => $t['cost']['h'], 'currency' => '€', 'items' => $t['cost']['items'], 'total' => $t['cost']['total'], 'note' => $t['cost']['note'], 'verified_at' => $checked],
            $table($t['fees_table']),
            $section($head['first'], $t['first_costs']),
        ];
        if (! empty($t['stats'])) {
            $blocks[] = $section($t['stats']['h'], $t['stats']['body']);
        }

        return $blocks;
    }

    private function plain(string $md): string
    {
        $s = preg_replace('/\[([^\]]+)\]\([^)]+\)/u', '$1', $md);

        return trim(preg_replace('/\s+/u', ' ', str_replace(['**', '*'], '', $s)));
    }

    public function down(): void
    {
        // Geri alma yok — editoryal içerik yeni bir data-migration ile değiştirilir.
    }
};

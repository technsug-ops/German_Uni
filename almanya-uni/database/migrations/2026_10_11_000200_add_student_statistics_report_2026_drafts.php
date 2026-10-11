<?php

use App\Models\Post;
use App\Support\StudentStatisticsReport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ApplyToGerman Almanya Öğrenci İstatistikleri Raporu 2026 — TR/EN/DE web yayını TASLAK olarak eklenir
 * (veri: database/data/stats-report-2026). Yazar: Şule Demirel (users.slug = sule-demirel, 000700 ile eklendi).
 *
 * Üç kayıt tek çeviri grubunda (StudentStatisticsReport::GROUP), is_published = false, published_at = NULL.
 * Yayın ayrı adımdır: 2026_10_11_000300_publish_student_statistics_report_2026 (yayın sahibi onayı 2026-10-11);
 * gerçek web yayın tarihi o anda atanır (Eylül 2026 raporun hazırlanma tarihidir, yayın tarihi değildir).
 *
 * Güvenlik: yazar yoksa, slug başka bir çeviri grubuna aitse, grup kaydı beklenenden farklıysa ya da kolon sınırı
 * aşılırsa RuntimeException → hiçbir şey yazılmaz. Aynı içerik zaten varsa no-op (updated_at dahil).
 * Kayıtlar Eloquent ile kaydedilir; Post::saving content_html'i content_md'den üretir (dipnot + tablo dahil).
 */
return new class extends Migration
{
    private const MAX_LENGTH = ['title' => 255, 'slug' => 255, 'meta_title' => 255, 'excerpt' => 280, 'meta_description' => 300];

    public function up(): void
    {
        if (! Schema::hasTable('posts') || ! is_file(base_path(StudentStatisticsReport::DIR . '/posts.json'))) {
            return;
        }
        $def = StudentStatisticsReport::definition();
        $group = $def['translation_group_id'];
        $problems = [];

        $author = DB::table('users')->where('slug', $def['author_slug'])->where('is_author', true)->first(['id', 'name']);
        if (! $author) {
            throw new RuntimeException("Rapor taslağı eklenmedi: yazar '{$def['author_slug']}' bulunamadı (önce yazar migration'ı çalışmalı).");
        }
        $categoryId = DB::table('categories')->where('slug', $def['category_slug'])->value('id');

        $plan = [];
        foreach ($def['posts'] as $locale => $p) {
            $label = "{$locale}/{$p['slug']}";
            $fields = [
                'title' => $p['title'],
                'slug' => $p['slug'],
                'meta_title' => $p['meta_title'],
                'meta_description' => $p['meta_description'],
                'excerpt' => $p['excerpt'],
                'content_md' => rtrim(file_get_contents(base_path(StudentStatisticsReport::DIR . '/' . $p['file']))) . "\n",
            ];
            foreach (self::MAX_LENGTH as $field => $max) {
                if (mb_strlen($fields[$field]) > $max) {
                    $problems[] = "$label: $field kolon sınırını aşıyor";
                }
            }

            $bySlug = Post::where('locale', $locale)->where('slug', $p['slug'])->first();
            if ($bySlug && $bySlug->translation_group_id !== $group) {
                $problems[] = "$label: slug başka bir çeviri grubuna ait (#{$bySlug->id})";
                continue;
            }
            $inGroup = Post::where('locale', $locale)->where('translation_group_id', $group)->get();
            if ($inGroup->count() > 1) {
                $problems[] = "$label: grupta bu dilde birden fazla kayıt var";
                continue;
            }
            $existing = $inGroup->first();
            if ($existing) {
                $same = collect($fields)->every(fn ($v, $k) => (string) $existing->{$k} === $v);
                if ($same && (int) $existing->user_id === (int) $author->id) {
                    continue; // zaten eklenmiş → no-op
                }
                $problems[] = "$label: grup kaydı (#{$existing->id}) beklenen taslaktan farklı; elle güncellenmiş içerik ezilmez";
                continue;
            }

            $post = new Post($fields);
            $post->locale = $locale;
            $post->type = 'blog';
            $post->translation_group_id = $group;
            $post->user_id = $author->id;
            $post->category_id = $categoryId;
            $post->is_published = false;
            $post->published_at = null;
            $plan[] = $post;
        }

        if ($problems) {
            throw new RuntimeException('Rapor taslağı eklenmedi, hiçbir şey yazılmadı: ' . implode('; ', $problems));
        }

        DB::transaction(function () use ($plan) {
            foreach ($plan as $post) {
                $post->save();
            }
        });
    }

    public function down(): void
    {
        // Geri alma yok — taslaklar panelden yönetilir; yayındaki bir rapor migration geri alınarak silinmez.
    }
};

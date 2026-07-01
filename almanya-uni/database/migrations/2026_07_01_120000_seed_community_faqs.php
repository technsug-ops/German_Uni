<?php

use App\Models\Faq;
use App\Models\FaqTopic;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Topluluk (r/germany) havuzundan üretilip AGENT'larla gözden geçirilen SSS'leri
 * prod'a taşır (TR + EN + DE, translation_group_id ile bağlı). Lokalde üretilir,
 * gözden geçirilir; seeder prod'da çalışmaz → data-migration ile taşınır (idempotent).
 *
 * Veri: database/migrations/data/community_faqs.json.gz
 *   [{group_id, locale, topic_slug, question, slug, answer_md, sort_order, is_published}, ...]
 *
 * Idempotent: slug zaten varsa atlar. faq_topic_id topic SLUG'ından çözülür (prod ID
 * farkı olsa da doğru bağlanır). answer_html/minutes Faq saving-hook'u ile render edilir.
 */
return new class extends Migration
{
    public function up(): void
    {
        $path = database_path('migrations/data/community_faqs.json.gz');
        if (! is_file($path)) {
            // Veri dosyası yoksa sessizce geç (migration deploy'da güvenli kalsın).
            return;
        }
        $rows = json_decode(gzdecode(file_get_contents($path)), true) ?: [];
        if (empty($rows)) return;

        $topicIds = FaqTopic::pluck('id', 'slug')->all();
        $existing = DB::table('faqs')->pluck('slug')->flip();

        Faq::unguard();
        $inserted = 0; $skipped = 0;
        foreach ($rows as $r) {
            $slug = $r['slug'] ?? null;
            $topicId = $topicIds[$r['topic_slug'] ?? ''] ?? null;
            if (! $slug || ! $topicId) { $skipped++; continue; }
            if (isset($existing[$slug])) { $skipped++; continue; }

            $f = new Faq();
            $f->forceFill([
                'faq_topic_id'         => $topicId,
                'translation_group_id' => $r['group_id'],
                'locale'               => $r['locale'],
                'question'             => $r['question'],
                'slug'                 => $slug,
                'answer_md'            => $r['answer_md'],   // hook answer_html + minutes üretir
                'intent'               => 'community',
                'has_answer'           => true,
                'is_published'         => (bool) ($r['is_published'] ?? true),
                'sort_order'           => (int) ($r['sort_order'] ?? 0),
            ]);
            $f->save();
            $existing[$slug] = true;
            $inserted++;
        }
        Faq::reguard();

        echo "community_faqs: +{$inserted} eklendi, {$skipped} atlandı\n";
    }

    public function down(): void
    {
        // Geri alma: bu migration'ın eklediği community SSS'leri silmez (içerik korunur).
        // Gerekirse elle: Faq::where('intent','community')->...
    }
};

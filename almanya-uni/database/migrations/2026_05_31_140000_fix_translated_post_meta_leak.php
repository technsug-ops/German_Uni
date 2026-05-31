<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fixes Turkish meta_title / meta_description leaking onto translated blog
 * posts. The EN/DE versions of post #77 (uni-assist) and post #27 (student
 * job search) had their title/excerpt/body translated, but the SEO meta
 * fields stayed Turkish (or NULL → title fallback). This sets correct,
 * locale-appropriate meta.
 *
 * Matched by slug + locale (stable across environments). Idempotent and
 * non-destructive: a field is only overwritten when it's empty OR still
 * contains Turkish-unique characters (ı ş ğ İ) — so an already-correct
 * EN/DE value, or a later human edit, is never clobbered.
 */
return new class extends Migration
{
    /** @var array<string, array{meta_title?: string, meta_description?: string}> keyed by slug */
    private array $fixes = [
        'uniassist-rejection-vpd-solutions' => [
            'meta_title'       => 'uni-assist Application: VPD, Rejection Reasons & Solutions',
            'meta_description' => 'How to apply to German universities via uni-assist: what the VPD is, who issues it, and the most common rejection reasons — with fixes from real cases.',
        ],
        'uniassist-ablehnung-vpd-loesungen' => [
            'meta_title'       => 'uni-assist Bewerbung: VPD, Ablehnungsgründe & Lösungen',
            'meta_description' => 'Bewerbung an deutschen Unis über uni-assist: Was die VPD ist, wer sie ausstellt und die häufigsten Ablehnungsgründe — mit Lösungen aus echten Fällen.',
        ],
        'almanya-ogrenci-is-arama-ipuclari-sikca-sorulanlar-de' => [
            'meta_title'       => 'Jobsuche für Studierende in Deutschland: Tipps, FAQ & Links',
        ],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('posts')) return;

        foreach ($this->fixes as $slug => $vals) {
            $post = DB::table('posts')->where('slug', $slug)->first();
            if (! $post) continue; // post not on this environment — skip

            $update = [];
            foreach ($vals as $field => $value) {
                $current = (string) ($post->{$field} ?? '');
                // Only set when empty OR still Turkish (contains ı/ş/ğ/İ).
                if ($current === '' || preg_match('/[ışğİ]/u', $current)) {
                    $update[$field] = $value;
                }
            }

            if ($update) {
                $update['updated_at'] = now();
                DB::table('posts')->where('id', $post->id)->update($update);
            }
        }
    }

    public function down(): void
    {
        // No-op: we cannot restore the leaked Turkish meta, and we wouldn't want to.
    }
};

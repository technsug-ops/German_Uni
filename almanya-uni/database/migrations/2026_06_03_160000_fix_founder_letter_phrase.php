<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kurucu mektubunda Türkçe ifade düzeltmesi:
 *   "bunu kim önceden söyleseydi keşke" → "bunu keşke biri önceden söyleseydi"
 * Prod'daki yayınlanmış post içeriğini (content_md + content_html) hedefli günceller.
 * Idempotent: eşleşme yoksa hiçbir şey yapmaz.
 */
return new class extends Migration
{
    private const OLD = 'bunu kim önceden söyleseydi keşke';
    private const NEW = 'bunu keşke biri önceden söyleseydi';

    public function up(): void
    {
        if (! Schema::hasTable('posts')) return;

        $posts = DB::table('posts')
            ->where('content_md', 'like', '%' . self::OLD . '%')
            ->orWhere('content_html', 'like', '%' . self::OLD . '%')
            ->get(['id', 'content_md', 'content_html']);

        foreach ($posts as $p) {
            DB::table('posts')->where('id', $p->id)->update([
                'content_md'   => str_replace(self::OLD, self::NEW, (string) $p->content_md),
                'content_html' => str_replace(self::OLD, self::NEW, (string) $p->content_html),
            ]);
        }
    }

    public function down(): void
    {
        // Geri alma gereksiz (dil düzeltmesi).
    }
};

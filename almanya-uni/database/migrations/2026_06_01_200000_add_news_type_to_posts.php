<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Post tablosunu haber (news) için genişletir. Yayınlanan haber = type='news'
 * Post (mevcut SEO/i18n/JSON-LD/translation_group altyapısını kullanır).
 * Blog yazıları type='blog' (varsayılan) olarak kalır → birbirine karışmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'type')) {
                $table->string('type', 16)->default('blog')->index()->after('translation_group_id');
            }
            if (! Schema::hasColumn('posts', 'source_url')) {
                $table->string('source_url', 500)->nullable()->after('video_url');
            }
            if (! Schema::hasColumn('posts', 'source_name')) {
                $table->string('source_name', 120)->nullable()->after('source_url');
            }
            if (! Schema::hasColumn('posts', 'event_date')) {
                $table->date('event_date')->nullable()->after('source_name');
            }
            // Admin önceliği: 0 = öncelik yok (en yeni en önde). >0 → öne sabitlenir.
            if (! Schema::hasColumn('posts', 'news_priority')) {
                $table->smallInteger('news_priority')->default(0)->index()->after('event_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            foreach (['type', 'source_url', 'source_name', 'event_date'] as $c) {
                if (Schema::hasColumn('posts', $c)) $table->dropColumn($c);
            }
        });
    }
};

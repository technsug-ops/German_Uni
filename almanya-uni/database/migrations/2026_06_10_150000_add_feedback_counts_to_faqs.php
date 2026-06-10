<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FAQ cevaplarına "işine yaradı mı?" oylama sayaçları (blog post pattern'i).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            if (! Schema::hasColumn('faqs', 'helpful_count')) {
                $table->unsignedInteger('helpful_count')->default(0)->after('view_count');
            }
            if (! Schema::hasColumn('faqs', 'unhelpful_count')) {
                $table->unsignedInteger('unhelpful_count')->default(0)->after('helpful_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            foreach (['helpful_count', 'unhelpful_count'] as $col) {
                if (Schema::hasColumn('faqs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

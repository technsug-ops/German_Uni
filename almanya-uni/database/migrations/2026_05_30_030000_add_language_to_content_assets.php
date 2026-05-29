<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_assets')) {
            return;
        }

        Schema::table('content_assets', function (Blueprint $table) {
            if (! Schema::hasColumn('content_assets', 'language')) {
                $table->string('language', 5)->default('tr')->after('asset_type')->index();
            }
            if (! Schema::hasColumn('content_assets', 'source_asset_id')) {
                $table->foreignId('source_asset_id')->nullable()->after('language')
                    ->constrained('content_assets')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('content_assets')) {
            return;
        }
        Schema::table('content_assets', function (Blueprint $table) {
            if (Schema::hasColumn('content_assets', 'source_asset_id')) {
                $table->dropConstrainedForeignId('source_asset_id');
            }
            if (Schema::hasColumn('content_assets', 'language')) {
                $table->dropColumn('language');
            }
        });
    }
};

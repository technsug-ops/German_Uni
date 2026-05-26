<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Topluluk katkıları — öğrenci deneyimi / ipucu / düzeltme (UGC)
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20)->default('experience'); // experience | tip | correction
            $table->string('title');
            $table->text('content');
            $table->string('target_type', 20)->nullable();      // city | university | program | general
            $table->unsignedBigInteger('target_id')->nullable(); // ilgili entity id (opsiyonel)
            $table->string('target_label')->nullable();          // "Berlin", "TU München" (gösterim)
            $table->string('status', 12)->default('pending');    // pending | approved | rejected
            $table->unsignedInteger('upvote_count')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'target_type']);
            $table->index('user_id');
        });

        // Topluluk Katkıcısı rozeti — onaylı katkısı olan kullanıcı
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_contributor')) {
                $table->boolean('is_contributor')->default(false)->after('is_author');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contributions');
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_contributor')) {
                $table->dropColumn('is_contributor');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Post yorumları — UGC + E-E-A-T sinyali.
 *
 * Akış: Anonim/login yorum → status=pending → admin approval → status=approved → public görünür.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('post_comments')->cascadeOnDelete();

            // Anonim yorum desteği: user_id NULL ise bunlar dolu
            $table->string('author_name', 80)->nullable();
            $table->string('author_email', 150)->nullable();

            $table->text('body');
            $table->string('status', 16)->default('pending')->index(); // pending | approved | spam | rejected
            $table->boolean('is_pinned')->default(false);
            $table->unsignedInteger('helpful_count')->default(0);

            // Anti-spam / audit
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['post_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_comments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Event reviews — post-event UGC + rating (1-5 stars).
 * Pattern: PostComment ile aynı yapı + rating kolonu.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->string('attendee_name', 80)->nullable();
            $table->string('attendee_email', 150)->nullable();
            $table->text('body')->nullable();
            $table->string('status', 16)->default('pending')->index(); // pending|approved|spam|rejected
            $table->boolean('is_pinned')->default(false);
            $table->unsignedInteger('helpful_count')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['event_id', 'status', 'created_at']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->decimal('avg_rating', 3, 2)->nullable()->after('registered_count');
            $table->unsignedInteger('reviews_count')->default(0)->after('avg_rating');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['avg_rating', 'reviews_count']);
        });
        Schema::dropIfExists('event_reviews');
    }
};

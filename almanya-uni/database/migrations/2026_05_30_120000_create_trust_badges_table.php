<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('trust_badges')) return;

        Schema::create('trust_badges', function (Blueprint $t) {
            $t->id();
            $t->string('platform', 60)->unique()->comment('trustpilot | google_reviews | capterra | g2 | facebook | youtube | featured_in_x');
            $t->string('display_name', 100)->comment('Human-readable label, e.g. "Trustpilot"');
            $t->string('logo_url', 500)->nullable()->comment('SVG/PNG hosted URL — or local path');
            $t->string('profile_url', 500)->nullable()->comment('Public profile/review page link');

            // Optional metric: when we have reviews/ratings to display
            $t->decimal('rating', 3, 1)->nullable()->comment('e.g. 4.7 (out of 5)');
            $t->unsignedInteger('review_count')->nullable();
            $t->string('badge_html')->nullable()->comment('Optional inline embed snippet from the platform');

            // Layout slot
            $t->enum('slot', ['footer', 'hero', 'about'])->default('footer');
            $t->unsignedSmallInteger('sort_order')->default(10);

            $t->boolean('is_active')->default(false)->comment('Hide until we actually register at the platform');
            $t->timestamps();

            $t->index(['is_active', 'slot', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trust_badges');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('popups')) return;

        Schema::create('popups', function (Blueprint $t) {
            $t->id();
            $t->string('key', 60)->unique()->comment('Stable cookie key — change to force re-show after edits');

            // Theme & layout
            $t->enum('theme', ['gradient', 'minimal', 'banner_top', 'banner_bottom', 'side_card', 'fullscreen'])
              ->default('gradient');
            $t->enum('position', ['center', 'top', 'bottom', 'bottom_right', 'bottom_left'])
              ->default('center');

            // Content (locale-aware)
            $t->string('title_tr')->nullable();
            $t->string('title_en')->nullable();
            $t->string('title_de')->nullable();
            $t->text('body_tr')->nullable();
            $t->text('body_en')->nullable();
            $t->text('body_de')->nullable();

            // Visual
            $t->string('image_url')->nullable();
            $t->string('emoji', 20)->nullable()->comment('Single emoji decoration');
            $t->string('accent_color', 20)->nullable()->comment('Hex color override (e.g. #F97316)');

            // CTA
            $t->string('cta_label_tr')->nullable();
            $t->string('cta_label_en')->nullable();
            $t->string('cta_label_de')->nullable();
            $t->string('cta_url', 500)->nullable();
            $t->boolean('cta_external')->default(false)->comment('opens in new tab');

            // Optional secondary action (dismiss-with-text instead of just ×)
            $t->string('secondary_label_tr')->nullable();
            $t->string('secondary_label_en')->nullable();
            $t->string('secondary_label_de')->nullable();

            // Targeting
            $t->json('target_pages')->nullable()->comment('Array of route names or URL patterns; null = all');
            $t->json('exclude_pages')->nullable();
            $t->json('locales')->nullable()->comment('Show only on these locales; null = all');

            // Trigger
            $t->enum('trigger', ['page_load', 'scroll_50', 'time_5s', 'time_15s', 'exit_intent'])
              ->default('time_5s');
            $t->unsignedInteger('delay_ms')->default(5000)->comment('Override for time-based triggers');

            // Dismiss behavior
            $t->unsignedInteger('dismiss_days')->default(7)->comment('Days until popup can re-appear after dismiss');
            $t->boolean('show_dismiss_button')->default(true);

            // Scheduling
            $t->boolean('is_active')->default(false);
            $t->timestamp('starts_at')->nullable();
            $t->timestamp('ends_at')->nullable();
            $t->unsignedSmallInteger('priority')->default(5)->comment('Lower = shown first when multiple match');

            // Metrics
            $t->unsignedInteger('view_count')->default(0);
            $t->unsignedInteger('click_count')->default(0);
            $t->unsignedInteger('dismiss_count')->default(0);

            $t->timestamps();

            $t->index(['is_active', 'priority']);
            $t->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popups');
    }
};

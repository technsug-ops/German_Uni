<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('email_templates')) {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('name');
                $table->string('category')->default('general'); // partnership | affiliate | general
                $table->string('locale')->default('de');        // de | en | tr
                $table->string('subject');
                $table->text('body');
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('email_messages')) {
            Schema::create('email_messages', function (Blueprint $table) {
                $table->id();
                $table->string('direction')->default('outbound'); // outbound | inbound
                // Soft reference to housing_providers; intentionally NO FK constraint.
                $table->unsignedBigInteger('provider_id')->nullable()->index();
                $table->string('to_email');
                $table->string('to_name')->nullable();
                $table->string('from_email');
                $table->string('subject');
                $table->text('body');
                $table->string('template_key')->nullable();
                $table->string('status')->default('queued'); // queued | sent | failed
                $table->text('error')->nullable();
                $table->string('message_id')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
                $table->index('direction');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('email_messages');
        Schema::dropIfExists('email_templates');
    }
};

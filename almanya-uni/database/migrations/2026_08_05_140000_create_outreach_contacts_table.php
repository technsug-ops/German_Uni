<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('outreach_contacts')) {
            Schema::create('outreach_contacts', function (Blueprint $table) {
                $table->id();
                $table->string('organization');            // firma / kurum adı
                $table->string('contact_name')->nullable(); // muhatap kişi
                $table->string('email')->nullable()->index();
                $table->string('phone')->nullable();
                $table->string('website')->nullable();
                $table->string('category')->default('other'); // OutreachContact::CATEGORIES
                $table->string('status')->default('new');     // OutreachContact::STATUSES
                $table->string('priority')->default('normal'); // high | normal | low
                $table->text('notes')->nullable();
                $table->timestamp('last_contacted_at')->nullable();
                $table->date('next_followup_at')->nullable()->index();
                $table->timestamps();
                $table->index(['status', 'category']);
            });
        }

        // Mailleri kontağa bağla. email_messages.provider_id gibi FK'sız yumuşak referans.
        if (Schema::hasTable('email_messages') && ! Schema::hasColumn('email_messages', 'contact_id')) {
            Schema::table('email_messages', function (Blueprint $table) {
                $table->unsignedBigInteger('contact_id')->nullable()->index()->after('provider_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('email_messages') && Schema::hasColumn('email_messages', 'contact_id')) {
            Schema::table('email_messages', function (Blueprint $table) {
                $table->dropColumn('contact_id');
            });
        }

        Schema::dropIfExists('outreach_contacts');
    }
};

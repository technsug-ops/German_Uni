<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('premium_interests')) return;

        Schema::table('premium_interests', function (Blueprint $t) {
            if (! Schema::hasColumn('premium_interests', 'wants_beta')) {
                $t->boolean('wants_beta')->default(false)->after('note')
                    ->comment('User opted into early-access beta tester program');
            }
            if (! Schema::hasColumn('premium_interests', 'beta_invited_at')) {
                $t->timestamp('beta_invited_at')->nullable()->after('wants_beta')
                    ->comment('Admin clicked "invite to beta" — recipient got the welcome mail');
            }
            if (! Schema::hasColumn('premium_interests', 'confirmation_sent_at')) {
                $t->timestamp('confirmation_sent_at')->nullable()->after('beta_invited_at')
                    ->comment('Tracks whether the "thanks for interest" mail went out');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('premium_interests')) return;
        Schema::table('premium_interests', function (Blueprint $t) {
            foreach (['wants_beta', 'beta_invited_at', 'confirmation_sent_at'] as $col) {
                if (Schema::hasColumn('premium_interests', $col)) $t->dropColumn($col);
            }
        });
    }
};

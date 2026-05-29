<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Email engagement + deliverability tracking columns.
 *
 *   bounce_count       — incremented on each hard/soft bounce webhook
 *   bounced_at         — when first bounce was recorded (set on 1st)
 *   complaint_at       — recipient marked us as spam (Brevo "spam" event)
 *   last_sent_at       — timestamp of last outbound email
 *   last_open_at       — when subscriber last opened an email
 *   last_click_at      — when subscriber last clicked a link
 *   open_count         — total opens lifetime
 *   click_count        — total clicks lifetime
 *   webhook_meta       — JSON: last raw webhook event for debugging
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subscribers')) return;

        Schema::table('subscribers', function (Blueprint $t) {
            if (! Schema::hasColumn('subscribers', 'bounce_count'))  $t->unsignedSmallInteger('bounce_count')->default(0)->after('user_agent');
            if (! Schema::hasColumn('subscribers', 'bounced_at'))    $t->timestamp('bounced_at')->nullable()->after('bounce_count');
            if (! Schema::hasColumn('subscribers', 'complaint_at'))  $t->timestamp('complaint_at')->nullable()->after('bounced_at');
            if (! Schema::hasColumn('subscribers', 'last_sent_at'))  $t->timestamp('last_sent_at')->nullable()->after('complaint_at');
            if (! Schema::hasColumn('subscribers', 'last_open_at'))  $t->timestamp('last_open_at')->nullable()->after('last_sent_at');
            if (! Schema::hasColumn('subscribers', 'last_click_at')) $t->timestamp('last_click_at')->nullable()->after('last_open_at');
            if (! Schema::hasColumn('subscribers', 'open_count'))    $t->unsignedInteger('open_count')->default(0)->after('last_click_at');
            if (! Schema::hasColumn('subscribers', 'click_count'))   $t->unsignedInteger('click_count')->default(0)->after('open_count');
            if (! Schema::hasColumn('subscribers', 'webhook_meta'))  $t->json('webhook_meta')->nullable()->after('click_count');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('subscribers')) return;
        Schema::table('subscribers', function (Blueprint $t) {
            foreach (['bounce_count','bounced_at','complaint_at','last_sent_at','last_open_at','last_click_at','open_count','click_count','webhook_meta'] as $col) {
                if (Schema::hasColumn('subscribers', $col)) $t->dropColumn($col);
            }
        });
    }
};

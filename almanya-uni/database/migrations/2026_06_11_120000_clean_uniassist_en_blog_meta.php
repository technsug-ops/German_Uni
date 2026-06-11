<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Blog #77 (uni-assist VPD) EN çevirisinin title + excerpt'i makine-çevirisi
 * artığı parantezli gloss'larla hantaldı ("uni-assist (pre-check service for
 * university applications) Application — VPD (Preliminary Review Document)…").
 * Gövde içeriği (content_md/html) zaten temiz; sadece meta alanları sadeleştirilir.
 * DE çevirisinin başlığı zaten temiz — dokunulmaz. Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('posts')
            ->where('slug', 'uniassist-rejection-vpd-solutions')
            ->where('locale', 'en')
            ->update([
                'title'   => 'uni-assist Application — VPD, Common Rejection Reasons & Solutions',
                'excerpt' => '80% of Turkish high-school/university graduates apply to Germany through uni-assist. What a VPD (Vorprüfungsdokumentation) is, who issues it, and why it gets rejected — a step-by-step guide with real community mistakes.',
            ]);
    }

    public function down(): void
    {
        // İçerik temizliği — geri alınmaz (no-op).
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dil Kursları + Yeminli Tercüme Büroları (Housing Providers deseni) + Lead toplama.
 * Herkese açık + lead-gen + affiliate-ready (görsel + esnek bilgi, admin'den yönetilir).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('language_courses')) {
            Schema::create('language_courses', function (Blueprint $t) {
                $t->id();
                $t->string('slug')->unique();
                $t->string('name');
                $t->string('type', 20)->default('private'); // university | private | online
                $t->string('website')->nullable();
                $t->string('affiliate_url')->nullable();     // affiliate yönlendirme (varsa website yerine)
                $t->string('email')->nullable();
                $t->string('phone')->nullable();
                $t->string('logo_url')->nullable();
                $t->string('image_path')->nullable();         // affiliate banner görseli (upload)
                $t->text('description_tr')->nullable();
                $t->text('description_en')->nullable();
                $t->text('description_de')->nullable();
                $t->json('cities')->nullable();
                $t->json('levels')->nullable();               // A1..C2
                $t->json('features')->nullable();
                $t->integer('price_min')->nullable();
                $t->integer('price_max')->nullable();
                $t->string('price_note')->nullable();
                $t->unsignedInteger('click_count')->default(0);
                $t->boolean('is_featured')->default(false);
                $t->boolean('is_active')->default(true)->index();
                $t->integer('sort_order')->default(0);
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('translation_offices')) {
            Schema::create('translation_offices', function (Blueprint $t) {
                $t->id();
                $t->string('slug')->unique();
                $t->string('name');
                $t->string('type', 20)->default('agency'); // sworn_individual | agency | online
                $t->string('website')->nullable();
                $t->string('affiliate_url')->nullable();
                $t->string('email')->nullable();
                $t->string('phone')->nullable();
                $t->string('logo_url')->nullable();
                $t->string('image_path')->nullable();
                $t->text('description_tr')->nullable();
                $t->text('description_en')->nullable();
                $t->text('description_de')->nullable();
                $t->json('cities')->nullable();
                $t->json('languages')->nullable();           // "TR-DE", "DE-TR"...
                $t->json('features')->nullable();            // hizmetler: apostil, noter, ekspres...
                $t->boolean('is_sworn')->default(true);      // yeminli mi
                $t->unsignedInteger('click_count')->default(0);
                $t->boolean('is_featured')->default(false);
                $t->boolean('is_active')->default(true)->index();
                $t->integer('sort_order')->default(0);
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('leads')) {
            Schema::create('leads', function (Blueprint $t) {
                $t->id();
                $t->string('source_type', 40)->index();      // language_course | translation_office
                $t->unsignedBigInteger('source_id')->nullable()->index();
                $t->string('source_name')->nullable();
                $t->string('name')->nullable();
                $t->string('email')->nullable();
                $t->string('phone')->nullable();
                $t->text('message')->nullable();
                $t->string('locale', 5)->nullable();
                $t->string('status', 16)->default('new')->index(); // new | contacted | converted | archived
                $t->json('meta')->nullable();
                $t->timestamps();
            });
        }

        // Mega menüye ekle (Keşfet grubu) — idempotent
        if (Schema::hasTable('menu_pages')) {
            $now = now();
            $menu = [
                ['key' => 'language-courses.index',    'label' => 'Dil Kursları',    'icon' => '🗣️', 'description' => 'Üniversite + özel + online', 'group' => 'kesfet', 'sort_order' => 95],
                ['key' => 'translation-offices.index', 'label' => 'Yeminli Tercüme', 'icon' => '📜', 'description' => 'Diploma & belge çevirisi',   'group' => 'kesfet', 'sort_order' => 96],
            ];
            foreach ($menu as $row) {
                DB::table('menu_pages')->updateOrInsert(
                    ['key' => $row['key']],
                    $row + ['link_type' => 'route', 'is_enabled' => true, 'protect_route' => true, 'updated_at' => $now, 'created_at' => $now]
                );
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
        Schema::dropIfExists('translation_offices');
        Schema::dropIfExists('language_courses');
    }
};

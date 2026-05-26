<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Yeni event_categories tablosu (7 strategic category)
        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();
            $table->string('name_tr', 100);
            $table->string('name_en', 100)->nullable();
            $table->string('icon', 8)->nullable();
            $table->string('color', 16)->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Events tablosuna ek alanlar
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('type')->constrained('event_categories')->nullOnDelete();
            $table->string('sponsor', 200)->nullable()->after('host');
            $table->string('sponsor_logo_url', 500)->nullable()->after('sponsor');
            $table->text('reward')->nullable()->after('sponsor_logo_url'); // ödül açıklaması (sertifika, mentorship, prize pool, vb.)
            $table->string('target_audience', 100)->nullable()->after('reward'); // bachelor/master/phd/alumni/all
            $table->string('difficulty', 20)->nullable()->after('target_audience'); // beginner/intermediate/advanced
            $table->unsignedSmallInteger('duration_minutes')->nullable()->after('difficulty');
            $table->json('tags')->nullable()->after('duration_minutes'); // AI, Python, Career, Startup, vb.
        });

        // 7 strategic category seed
        $categories = [
            ['slug' => 'networking', 'name_tr' => 'Networking & Kariyer', 'icon' => '🤝', 'color' => '#0F766E', 'description' => 'Profesyonellerle tanışma, staj/iş fırsatları, mentorship', 'sort_order' => 1],
            ['slug' => 'skill', 'name_tr' => 'Beceri Geliştirme', 'icon' => '🛠️', 'color' => '#7C3AED', 'description' => 'Workshop, bootcamp, hackathon — pratik beceri kazandıran', 'sort_order' => 2],
            ['slug' => 'peer-learning', 'name_tr' => 'Topluluk & Tanışma', 'icon' => '🌍', 'color' => '#EA580C', 'description' => 'Öğrenci buluşmaları, kültür etkinlikleri, dil değişimi', 'sort_order' => 3],
            ['slug' => 'personal-growth', 'name_tr' => 'Kişisel Gelişim', 'icon' => '🧠', 'color' => '#DB2777', 'description' => 'Entrepreneurship, productivity, life coaching, finans okuryazarlığı', 'sort_order' => 4],
            ['slug' => 'adventure', 'name_tr' => 'Macera & Sosyal', 'icon' => '🏔️', 'color' => '#0891B2', 'description' => 'Doğa, spor, gezi, eğlence — strese ara ver', 'sort_order' => 5],
            ['slug' => 'industry-immersion', 'name_tr' => 'Sektör Keşfi', 'icon' => '🏭', 'color' => '#CA8A04', 'description' => 'Fabrika gezisi, ofis ziyareti, sektör paneli (Siemens, Bosch, Deutsche Bank...)', 'sort_order' => 6],
            ['slug' => 'special-format', 'name_tr' => 'Özel Format', 'icon' => '🎤', 'color' => '#9333EA', 'description' => 'TED-Talk, festival, film festivali, pitch yarışması — exclusive', 'sort_order' => 7],
        ];

        foreach ($categories as $cat) {
            DB::table('event_categories')->insert(array_merge($cat, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Mevcut events için category map (mevcut 9 type → en uygun kategoriye)
        $typeToCategory = [
            'webinar'      => 'skill',          // çoğu öğretici
            'workshop'     => 'skill',
            'info_session' => 'personal-growth',
            'qa_live'      => 'personal-growth',
            'meetup'       => 'peer-learning',
            'open_day'     => 'industry-immersion',
            'panel'        => 'networking',
            'deadline'     => 'personal-growth',
            'conference'   => 'special-format',
        ];

        foreach ($typeToCategory as $type => $catSlug) {
            $catId = DB::table('event_categories')->where('slug', $catSlug)->value('id');
            DB::table('events')->where('type', $type)->update(['category_id' => $catId]);
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'sponsor', 'sponsor_logo_url', 'reward', 'target_audience', 'difficulty', 'duration_minutes', 'tags']);
        });
        Schema::dropIfExists('event_categories');
    }
};

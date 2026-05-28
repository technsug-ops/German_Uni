<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * MenuPage description'ları İngilizce source'a standartlaştır.
 *
 * Site i18n mimarisi: DB'de İngilizce kaynak metin, lang/{locale}.json'da çeviri.
 * Bu migration mevcut DB description'larını (TR veya EN) → kanonik İngilizce'ye çevirir.
 *
 * Lang JSON dosyalarında karşılıkları zaten eklenmiş olmalı (bu migration ile birlikte commit'lenir).
 */
return new class extends Migration
{
    /**
     * key → canonical English description map.
     */
    private const DESCRIPTIONS = [
        'universities.index'        => 'All German universities',
        'programs.index'            => 'Bachelor + Master',
        'cities.index'              => 'Student cities',
        'states.index'              => '16 federal states',
        'fields.index'              => 'Academic fields',
        'professions.index'         => 'Profession descriptions',
        'map.index'                 => 'Interactive map',
        'rankings.index'            => 'Size & prestige',
        'compare.index'             => 'Compare 2-4 universities',
        'journey.show'              => '8 steps to Germany',
        'tools.recommendation'      => 'Match in 5 questions',
        'tools.career-compass'      => 'Skill + profession',
        'tools.eligibility-checker' => 'Anabin + university rules',
        'tools.cost-of-living'      => 'City-based expense',
        'tools.budget-planner'      => 'Income-expense balance',
        'tools.visa-cost'           => 'All cost items',
        'tools.blocked-account'     => 'Compare blocked accounts',
        'tools.deadlines'           => 'Deadline + calendar',
        'tools.grade-converter'     => 'TR → German 1-5',
        'housing.index'             => 'Find accommodation',
        'tools.index'               => 'All tools',
        'scholarships.index'        => '166 scholarship programs',
        'scholarships.daad'         => 'Government scholarships',
        'events.index'              => 'Webinar · workshop · panel',
        'mentors.index'             => '1-on-1 with alumni',
        'study.germany'             => 'Why Germany, city-industry map',
        'blog.index'                => 'Guide articles',
        'faqs.index'                => '269 answered questions',
        'about'                     => 'About us',
        'team'                      => 'Founder · editor · contributor',
        'forum'                     => 'Community forum',
        'pricing'                   => 'Free + Premium tiers',
    ];

    public function up(): void
    {
        foreach (self::DESCRIPTIONS as $key => $description) {
            DB::table('menu_pages')
                ->where('key', $key)
                ->update(['description' => $description]);
        }

        // Cache invalidate (key sabit, model olmadan elle)
        \Illuminate\Support\Facades\Cache::forget('menu_pages.all_v1');
        \Illuminate\Support\Facades\Cache::forget('menu_pages.statusmap_v1');
    }

    public function down(): void
    {
        // Restore'a gerek yok — lang JSON'lar zaten doğru çeviriyi yapacak
    }
};

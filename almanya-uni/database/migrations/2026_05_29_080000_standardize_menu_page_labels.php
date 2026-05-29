<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * MenuPage label'larını English source'a standardize eder.
 * Daha önceki migration descriptions'ları yapmıştı ama labels TR kalmıştı —
 * sonuç: EN/DE sayfasında bile menü label'ları TR görünüyordu.
 *
 * Lang JSON dosyalarına TR + DE çevirileri seeder olarak bu migration ile birlikte
 * commit'lenir (lang dosyaları repo'da güncellenir).
 */
return new class extends Migration
{
    private const LABELS = [
        'universities.index'        => 'Universities',
        'programs.index'            => 'Programs',
        'cities.index'              => 'Cities',
        'states.index'              => 'Federal States',
        'fields.index'              => 'Fields of Study',
        'professions.index'         => 'Professions',
        'map.index'                 => 'Map',
        'rankings.index'            => 'Rankings',
        'compare.index'             => 'Compare',
        'journey.show'              => 'My Application Journey',
        'tools.recommendation'      => 'University Match Quiz',
        'tools.career-compass'      => 'Career Compass',
        'tools.eligibility-checker' => 'Eligibility Check',
        'tools.cost-of-living'      => 'Cost of Living',
        'tools.budget-planner'      => 'Budget Planner',
        'tools.visa-cost'           => 'Visa Cost',
        'tools.blocked-account'     => 'Blocked Account Finder',
        'tools.deadlines'           => 'Application Calendar',
        'tools.grade-converter'     => 'Grade Converter',
        'housing.index'             => 'Housing Guide',
        'tools.index'               => 'All Tools',
        'scholarships.index'        => 'All Scholarships',
        'scholarships.daad'         => 'DAAD Scholarships',
        'events.index'              => 'Events',
        'mentors.index'             => 'Mentors',
        'study.germany'             => 'Studying in Germany',
        'blog.index'                => 'Blog',
        'faqs.index'                => 'FAQ',
        'about'                     => 'About Us',
        'team'                      => 'Team & Editors',
        'forum'                     => 'Forum',
        'pricing'                   => 'Premium',
    ];

    public function up(): void
    {
        foreach (self::LABELS as $key => $label) {
            DB::table('menu_pages')->where('key', $key)->update(['label' => $label]);
        }

        // Cache invalidate
        \Illuminate\Support\Facades\Cache::forget('menu_pages.all_v1');
        \Illuminate\Support\Facades\Cache::forget('menu_pages.statusmap_v1');
    }

    public function down(): void {}
};

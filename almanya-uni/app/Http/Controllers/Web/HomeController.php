<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Faq;
use App\Models\FaqTopic;
use App\Models\FieldOfStudy;
use App\Models\Post;
use App\Models\Profession;
use App\Models\Program;
use App\Models\Scholarship;
use App\Models\State;
use App\Models\University;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured_universities = University::where('student_count', '>', 0)
            ->where('is_active', 1)
            ->orderBy('student_count', 'desc')
            ->limit(8)
            ->get()
            ->map(fn ($u) => [
                'slug' => $u->slug,
                'name_de' => $u->name_de,
                'short_name' => $u->short_name,
                'logo_url' => $u->logo_url,
                'image_url' => $u->image_url,
                'city_name' => $u->city?->name,
                'student_count' => $u->student_count,
                'type' => $u->type,
                'founded_year' => $u->founded_year,
                'has_content' => !empty($u->content_blocks),
            ])
            ->toArray();

        $cities = City::where('is_active', 1)
            ->has('universities')
            ->withCount(['universities' => fn ($q) => $q->where('is_active', 1)])
            ->whereNotIn('slug', [
                'harburg-q1635', 'nordrhein-westfalen-q1198', 'bayern-q980',
                'nord-q1997469', 'schleswig-holstein-q1194', 'rheinland-pfalz-q1200',
            ])
            ->orderByDesc('universities_count')
            ->limit(8)
            ->get()
            ->map(fn ($c) => [
                'slug' => $c->slug,
                'name' => $c->name,
                'image_url' => $c->image_url,
                'state_name' => $c->state?->name,
                'universities_count' => $c->universities_count,
                'has_content' => !empty($c->content_blocks),
            ])
            ->toArray();

        $states = State::withCount('cities')
            ->orderByDesc('cities_count')
            ->limit(8)
            ->get()
            ->map(fn ($s) => [
                'slug' => $s->slug,
                'name' => $s->name,
                'cities_count' => $s->cities_count,
            ])
            ->toArray();

        $latest_posts = Post::published()
            ->with('category')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get(['id', 'slug', 'title', 'excerpt', 'reading_minutes', 'published_at', 'category_id'])
            ->map(fn ($p) => [
                'slug' => $p->slug,
                'title' => $p->title,
                'excerpt' => $p->excerpt,
                'reading_minutes' => $p->reading_minutes,
                'published_at' => $p->published_at,
                'category_name' => $p->category?->name,
                'category_slug' => $p->category?->slug,
                'category_color' => $p->category?->color,
            ])
            ->toArray();

        $featured_faqs = Faq::published()
            ->answered()
            ->where('is_featured', true)
            ->with('topic')
            ->limit(4)
            ->get(['id', 'slug', 'question', 'faq_topic_id', 'answer_minutes']);

        if ($featured_faqs->isEmpty()) {
            $featured_faqs = Faq::published()
                ->answered()
                ->with('topic')
                ->orderByDesc('view_count')
                ->orderBy('sort_order')
                ->limit(4)
                ->get(['id', 'slug', 'question', 'faq_topic_id', 'answer_minutes']);
        }

        // Sayımlar günde bir değişir → 6 saat cache (locale-bağımsız).
        $faq_stats = cache()->remember('home.faq_stats_v2', now()->addHours(6), fn () => [
            'total'    => Faq::published()->where('has_answer', true)->count(),
            'topics'   => FaqTopic::active()->count(),
        ]);

        // 4 öne çıkan burs (DAAD önce, sonra programmname dolu olanlar)
        $featured_scholarships = Scholarship::whereNull('removed_at')
            ->orderByDesc('is_daad')
            ->orderBy('name_en')
            ->limit(4)
            ->get(['id', 'slug', 'name_en', 'name_de', 'programmname_en', 'is_daad']);

        // Top 6 alan — program sayısına göre
        $top_fields = FieldOfStudy::active()
            ->withCount(['programs' => fn ($q) => $q->where('is_active', 1)])
            ->orderByDesc('programs_count')
            ->limit(6)
            ->get(['id', 'slug', 'name_tr', 'icon', 'color','name_en','name_de']);

        // Crawl-konsantrasyon (SEO): ana sayfa (en yüksek otorite) → seçili
        // YÜKSEK-DEĞER program/meslek DETAY sayfalarına link. Böylece Google'ın
        // crawl bütçesi önce değerli sayfalara akar (discovered-not-indexed azalır).
        // Deterministik + 6 saat cache → stabil link grafiği (SEO için önemli).
        // DÜZ ARRAY cache'le (Eloquent Collection DEĞİL): file cache serialize/unserialize
        // Eloquent Collection'ı bozabiliyor ("incomplete object" → 500). Düz array güvenli.
        // Locale-spesifik key — name accessor cache anında locale'e göre çözülüyor.
        $featured_programs = cache()->remember('home.featured_programs_v3:' . app()->getLocale(), now()->addHours(6), fn () =>
            Program::query()
                ->where('programs.is_active', true)
                ->whereIn('programs.language', ['en', 'both'])
                ->whereNotNull('programs.description_tr')->where('programs.description_tr', '!=', '')
                ->join('universities', 'universities.id', '=', 'programs.university_id')
                ->where('universities.is_active', 1)
                ->orderByDesc('universities.student_count')
                ->orderBy('programs.id')
                ->limit(8)
                ->select('programs.id', 'programs.slug', 'programs.name_de', 'programs.name_en', 'programs.degree', 'programs.language', 'programs.university_id')
                ->get()
                ->load('university:id,slug,name_de')
                ->map(fn ($p) => [
                    'slug'     => $p->slug,
                    'name'     => $p->name,
                    'degree'   => $p->degree,
                    'language' => $p->language,
                    'uni_name' => $p->university?->name_de,
                ])->all()
        );

        $featured_professions = cache()->remember('home.featured_professions_v3:' . app()->getLocale(), now()->addHours(6), fn () =>
            Profession::where('is_active', true)
                ->where('type', 'studienberuf')
                ->whereNotNull('description_tr')->where('description_tr', '!=', '')
                ->whereNotNull('field_of_study_id')
                ->with('field:id,slug,name_tr,name_en,name_de,icon')
                ->orderBy('field_of_study_id')->orderBy('id')
                ->limit(8)
                ->get(['id', 'slug', 'name_tr', 'name_de', 'name_en', 'field_of_study_id', 'type'])
                ->map(fn ($p) => [
                    'slug'       => $p->slug,
                    'name'       => $p->name,
                    'field_name' => $p->field?->name,
                ])->all()
        );

        // Premium başvuru şablonları — ana sayfa hunisi. DÜZ ARRAY (Collection değil!),
        // locale'li key (title accessor cache anında çözülür).
        $featured_templates = cache()->remember('home.featured_templates_v1:' . app()->getLocale(), now()->addHours(6), fn () =>
            \App\Models\DocumentTemplate::active()
                ->orderBy('sort_order')->orderBy('id')->limit(6)
                ->get()
                ->map(fn ($t) => ['slug' => $t->slug, 'title' => $t->title, 'icon' => $t->icon])->all()
        );

        // 9 aggregate count — günde bir değişir → 6 saat cache (locale-bağımsız).
        $totals = cache()->remember('home.totals_v2', now()->addHours(6), fn () => [
            'universities' => University::where('is_active', true)->count(),
            'universities_on_map' => University::where('is_active', true)->whereNotNull('latitude')->count(),
            'cities'       => City::where('is_active', true)->count(),
            'states'       => State::count(),
            'posts'        => Post::published()->count(),
            'programs'     => Program::where('is_active', true)->count(),
            'programs_en'  => Program::where('is_active', true)->whereIn('language', ['en', 'both'])->count(),
            'professions'  => Profession::where('is_active', true)->count(),
            'scholarships' => Scholarship::whereNull('removed_at')->count(),
        ]);

        return view('home', [
            'featured_universities' => $featured_universities,
            'featured_programs'     => $featured_programs,
            'featured_professions'  => $featured_professions,
            'featured_templates'    => $featured_templates,
            'featured_scholarships' => $featured_scholarships,
            'top_fields'            => $top_fields,
            'cities'                => $cities,
            'states'                => $states,
            'latest_posts'          => $latest_posts,
            'featured_faqs'         => $featured_faqs,
            'faq_stats'             => $faq_stats,
            'totals'                => $totals,
        ]);
    }
}

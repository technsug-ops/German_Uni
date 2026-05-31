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
            ->with('category:id,name,name_tr,name_en,name_de,slug,color')
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
            ->with('topic:id,name,name_tr,name_en,name_de,slug,color,icon')
            ->limit(4)
            ->get(['id', 'slug', 'question', 'faq_topic_id', 'answer_minutes']);

        if ($featured_faqs->isEmpty()) {
            $featured_faqs = Faq::published()
                ->answered()
                ->with('topic:id,name,name_tr,name_en,name_de,slug,color,icon')
                ->orderByDesc('view_count')
                ->orderBy('sort_order')
                ->limit(4)
                ->get(['id', 'slug', 'question', 'faq_topic_id', 'answer_minutes']);
        }

        $faq_stats = [
            'total'    => Faq::published()->where('has_answer', true)->count(),
            'topics'   => FaqTopic::active()->count(),
        ];

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

        $totals = [
            'universities' => University::where('is_active', true)->count(),
            'universities_on_map' => University::where('is_active', true)->whereNotNull('latitude')->count(),
            'cities'       => City::where('is_active', true)->count(),
            'states'       => State::count(),
            'posts'        => Post::published()->count(),
            'programs'     => Program::where('is_active', true)->count(),
            'programs_en'  => Program::where('is_active', true)->whereIn('language', ['en', 'both'])->count(),
            'professions'  => Profession::where('is_active', true)->count(),
            'scholarships' => Scholarship::whereNull('removed_at')->count(),
        ];

        return view('home', [
            'featured_universities' => $featured_universities,
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

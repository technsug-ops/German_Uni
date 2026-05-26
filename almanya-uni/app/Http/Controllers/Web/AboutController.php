<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Faq;
use App\Models\Post;
use App\Models\Profession;
use App\Models\Program;
use App\Models\Scholarship;
use App\Models\State;
use App\Models\University;
use App\Models\User;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * Ekip / Yazarlar & Katkı Sağlayanlar — statülere göre gruplu profiller.
     */
    public function team(): View
    {
        // Yazar/editör kullanıcıları (Yapra Test ve admin-only hariç) + blog sayısı
        $people = User::where(fn ($q) => $q->where('is_author', true)->orWhere('role_label', '!=', null))
            ->whereNotIn('id', [1]) // test kullanıcısı
            ->withCount(['posts' => fn ($q) => $q->where('is_published', true)])
            ->with(['posts' => fn ($q) => $q->where('is_published', true)
                ->whereNotNull('published_at')
                ->orderByDesc('published_at')
                ->take(20)
                ->select('id', 'user_id', 'title', 'slug', 'published_at', 'reading_minutes')])
            ->orderByDesc('posts_count')
            ->get(['id', 'name', 'role_label', 'bio', 'avatar_url', 'is_admin']);

        // Statüye göre grupla
        $founders = $people->filter(fn ($u) => str_contains(mb_strtolower($u->role_label ?? ''), 'kurucu'));
        $editors  = $people->filter(fn ($u) => str_contains(mb_strtolower($u->role_label ?? ''), 'editör')
            && ! str_contains(mb_strtolower($u->role_label ?? ''), 'kurucu'));
        $others   = $people->reject(fn ($u) => $founders->contains('id', $u->id) || $editors->contains('id', $u->id));

        // Mentorlar (varsa)
        $mentors = \App\Models\Mentor::where('is_active', true)
            ->orderByDesc('is_featured')
            ->take(12)
            ->get(['name', 'slug', 'headline', 'avatar_url', 'current_company']);

        // Topluluk katkıcıları (onaylı katkısı olan kullanıcılar)
        $contributors = User::where('is_contributor', true)
            ->whereNotIn('id', $people->pluck('id')->all()) // editör/kurucu zaten yukarıda
            ->withCount(['contributions as approved_contributions_count' => fn ($q) => $q->where('status', 'approved')])
            ->orderByDesc('approved_contributions_count')
            ->take(24)
            ->get(['id', 'name', 'avatar_url']);

        $totalPosts = Post::where('is_published', 1)->where('locale', app()->getLocale())->count();
        $totalContributions = \App\Models\Contribution::approved()->count();

        return view('about.team', compact('founders', 'editors', 'others', 'mentors', 'contributors', 'totalPosts', 'totalContributions'));
    }


    public function index(): View
    {
        $stats = [
            'universities' => University::where('is_active', true)->count(),
            'programs'     => Program::where('is_active', true)->count(),
            'professions'  => Profession::where('is_active', true)->count(),
            'cities'       => City::whereHas('universities', fn ($q) => $q->where('is_active', 1))->count(),
            'states'       => State::count(),
            'scholarships' => Scholarship::whereNull('removed_at')->count(),
            'posts'        => Post::where('is_published', 1)->where('locale', app()->getLocale())->whereNotNull('published_at')->count(),
            'faqs'         => Faq::published()->where('has_answer', true)->count(),
        ];

        // Son 3 blog post
        $recentPosts = Post::where('is_published', 1)->where('locale', app()->getLocale())
            ->whereNotNull('published_at')
            ->with('category:id,name,slug,color')
            ->orderByDesc('published_at')
            ->take(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'reading_minutes', 'published_at', 'category_id']);

        // Team — manuel kurgu, sonradan veritabanına alınabilir
        $team = [
            [
                'name' => 'Yapra',
                'role' => __('Founder &amp; Developer'),
                'bio'  => __('Built the platform to help international students navigate higher education in Germany. Manages product strategy and engineering.'),
                'avatar' => 'Y',
                'color' => 'accent',
                'social' => [
                    ['icon' => '✉️', 'label' => 'technsug@gmail.com', 'url' => 'mailto:technsug@gmail.com'],
                ],
            ],
            [
                'name' => __('You?'),
                'role' => __('Volunteer / Contributor'),
                'bio'  => __(':brand grows with community support. Join us with translation, content writing, developer contributions, or sharing your student experience.', ['brand' => brand('name')]),
                'avatar' => '+',
                'color' => 'primary',
                'social' => [
                    ['icon' => '✉️', 'label' => __('Write to us'), 'url' => 'mailto:technsug@gmail.com?subject=Contribution'],
                ],
            ],
        ];

        return view('about.index', compact('stats', 'team', 'recentPosts'));
    }
}

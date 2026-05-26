<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MentorController extends Controller
{
    public function index(Request $request): View
    {
        $topic = $request->query('topic');
        $language = $request->query('lang');
        $freeOnly = $request->boolean('free');

        $query = Mentor::active();

        if ($topic) {
            $query->whereJsonContains('topics', $topic);
        }
        if ($language) {
            $query->whereJsonContains('languages', $language);
        }
        if ($freeOnly) {
            $query->where('rate_eur', 0);
        }

        $mentors = $query
            ->orderByDesc('is_featured')
            ->orderByDesc('rating_avg')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(24)
            ->withQueryString();

        // Tüm topic'leri topla (filter chip için)
        $allTopics = Mentor::active()
            ->whereNotNull('topics')
            ->pluck('topics')
            ->flatten()
            ->unique()
            ->filter()
            ->sort()
            ->values()
            ->take(20)
            ->all();

        return view('mentors.index', [
            'mentors'   => $mentors,
            'allTopics' => $allTopics,
            'filters'   => compact('topic', 'language', 'freeOnly'),
        ]);
    }

    public function show(string $slug): View
    {
        $mentor = Mentor::active()->where('slug', $slug)->firstOrFail();

        $related = Mentor::active()
            ->where('id', '!=', $mentor->id)
            ->when($mentor->topics, function ($q) use ($mentor) {
                $q->where(function ($w) use ($mentor) {
                    foreach ($mentor->topics ?? [] as $t) {
                        $w->orWhereJsonContains('topics', $t);
                    }
                });
            })
            ->orderByDesc('rating_avg')
            ->take(3)
            ->get();

        return view('mentors.show', compact('mentor', 'related'));
    }
}

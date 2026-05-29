<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqTopic;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        $topics = FaqTopic::active()
            ->withCount(['faqs' => fn ($q) => $q->where('is_published', true)])
            ->withCount(['faqs as answered_count' => fn ($q) => $q->where('is_published', true)->where('has_answer', true)])
            ->orderBy('sort_order')
            ->get();

        $totalQuestions = $topics->sum('faqs_count');
        $totalAnswered = $topics->sum('answered_count');

        $featured = Faq::published()
            ->answered()
            ->where('is_featured', true)
            ->with('topic:id,name,slug,color')
            ->limit(6)
            ->get(['id', 'slug', 'question', 'faq_topic_id', 'answer_minutes']);

        // En popüler 5 soru (view_count'a göre)
        $popular = Faq::published()
            ->answered()
            ->where('view_count', '>', 0)
            ->with('topic:id,name,slug,color')
            ->orderByDesc('view_count')
            ->limit(5)
            ->get(['id', 'slug', 'question', 'faq_topic_id', 'answer_minutes', 'view_count']);

        // Son güncellenen 5 soru
        $recent = Faq::published()
            ->answered()
            ->with('topic:id,name,slug,color')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get(['id', 'slug', 'question', 'faq_topic_id', 'answer_minutes', 'updated_at']);

        // Arama sonuçları
        $searchResults = null;
        if ($query !== '') {
            $like = '%' . $query . '%';
            $searchResults = Faq::published()
                ->where(function ($w) use ($like) {
                    $w->where('question', 'like', $like)
                      ->orWhere('answer_html', 'like', $like);
                })
                ->with('topic:id,name,slug,color,icon')
                ->orderByDesc('has_answer')
                ->orderByDesc('view_count')
                ->limit(20)
                ->get(['id', 'slug', 'question', 'faq_topic_id', 'has_answer', 'answer_minutes']);
        }

        return view('faqs.index', [
            'topics' => $topics,
            'total_questions' => $totalQuestions,
            'total_answered' => $totalAnswered,
            'featured' => $featured,
            'popular' => $popular,
            'recent' => $recent,
            'q' => $query,
            'searchResults' => $searchResults,
        ]);
    }

    public function topic(string $slug): View
    {
        $topic = FaqTopic::active()->where('slug', $slug)->firstOrFail();

        // Locale-aware: only show FAQs in the current site language
        $faqs = $topic->faqs()
            ->where('is_published', true)
            ->where('locale', app()->getLocale())
            ->orderByDesc('has_answer')
            ->orderBy('sort_order')
            ->get(['id', 'slug', 'question', 'intent', 'has_answer', 'answer_minutes', 'answer_html', 'faq_topic_id']);

        return view('faqs.topic', [
            'topic' => $topic,
            'faqs' => $faqs,
        ]);
    }

    public function show(string $topicSlug, string $slug)
    {
        $topic = FaqTopic::active()->where('slug', $topicSlug)->firstOrFail();

        $faq = Faq::published()
            ->where('faq_topic_id', $topic->id)
            ->where('slug', $slug)
            ->first();

        // Slug exists but in a different locale (translation group) — redirect to the
        // current-locale sibling instead of 404.
        if (! $faq) {
            $otherLocale = Faq::where('is_published', true)
                ->where('faq_topic_id', $topic->id)
                ->where('slug', $slug)
                ->first();
            if ($otherLocale && $otherLocale->translation_group_id) {
                $sibling = Faq::published()
                    ->where('translation_group_id', $otherLocale->translation_group_id)
                    ->first();
                if ($sibling) {
                    return redirect()->route('faqs.show', [$topic->slug, $sibling->slug], 301);
                }
            }
            throw new NotFoundHttpException();
        }

        Faq::where('id', $faq->id)->increment('view_count');

        $related = Faq::published()
            ->where('faq_topic_id', $topic->id)
            ->where('id', '!=', $faq->id)
            ->orderByDesc('has_answer')
            ->limit(5)
            ->get(['id', 'slug', 'question', 'answer_minutes', 'has_answer', 'faq_topic_id']);

        return view('faqs.show', [
            'topic' => $topic,
            'faq' => $faq,
            'related' => $related,
        ]);
    }
}

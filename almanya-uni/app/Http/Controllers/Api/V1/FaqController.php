<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use App\Models\FaqTopic;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:200',
            'topic_id' => 'nullable|integer|exists:faq_topics,id',
            'topic_slug' => 'nullable|string|max:120',
            'featured' => 'nullable|boolean',
            'intent' => 'nullable|string|max:40',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Faq::query()
            ->where('is_published', true)
            ->where('has_answer', true)
            ->with('topic:id,slug,name,name_tr,name_en,name_de,icon');

        if (!empty($validated['q'])) {
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $validated['q']) . '%';
            $query->where(function ($w) use ($term) {
                $w->where('question', 'like', $term)
                    ->orWhere('answer_md', 'like', $term);
            });
        }

        if (!empty($validated['topic_id'])) {
            $query->where('faq_topic_id', $validated['topic_id']);
        }
        if (!empty($validated['topic_slug'])) {
            $query->whereHas('topic', fn ($q) => $q->where('slug', $validated['topic_slug']));
        }
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }
        if (!empty($validated['intent'])) {
            $query->where('intent', $validated['intent']);
        }

        $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id');

        $perPage = (int) ($validated['per_page'] ?? 25);
        $paginator = $query->paginate($perPage)->withQueryString();

        return FaqResource::collection($paginator);
    }

    public function show(string $slugOrId)
    {
        $query = Faq::query()
            ->where('is_published', true)
            ->with('topic');

        $faq = is_numeric($slugOrId)
            ? $query->findOrFail((int) $slugOrId)
            : $query->where('slug', $slugOrId)->firstOrFail();

        return new FaqResource($faq);
    }

    public function topics()
    {
        $topics = FaqTopic::query()
            ->where('is_active', true)
            ->withCount(['faqs' => fn ($q) => $q->where('is_published', true)->where('has_answer', true)])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $topics->map(fn ($t) => [
                'id' => $t->id,
                'slug' => $t->slug,
                'name' => $t->name,
                'icon' => $t->icon,
                'color' => $t->color,
                'description' => $t->description,
                'faqs_count' => $t->faqs_count,
            ]),
        ]);
    }
}

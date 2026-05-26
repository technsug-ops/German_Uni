<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $type     = $request->query('type');
        $category = $request->query('category');

        $upcomingQ = Event::active()->upcoming();
        $pastQ     = Event::active()->past();
        $liveQ     = Event::active()->live();

        if ($type && array_key_exists($type, Event::TYPES)) {
            $upcomingQ->where('type', $type);
            $pastQ->where('type', $type);
            $liveQ->where('type', $type);
        } elseif ($category) {
            $typesInCategory = array_keys(array_filter(
                Event::TYPES,
                fn ($meta) => ($meta['category'] ?? null) === $category
            ));
            if (! empty($typesInCategory)) {
                $upcomingQ->whereIn('type', $typesInCategory);
                $pastQ->whereIn('type', $typesInCategory);
                $liveQ->whereIn('type', $typesInCategory);
            }
        }

        $live     = $liveQ->orderBy('starts_at')->get();
        $upcoming = $upcomingQ->orderBy('starts_at')->take(12)->get();
        $past     = $pastQ->orderByDesc('starts_at')->take(8)->get();

        $categories = EventCategory::orderBy('sort_order')->get();

        return view('events.index', compact('live', 'upcoming', 'past', 'type', 'category', 'categories'));
    }

    public function show(string $slug): View
    {
        $event = Event::active()->where('slug', $slug)->firstOrFail();

        $related = Event::active()
            ->where('id', '!=', $event->id)
            ->where('type', $event->type)
            ->upcoming()
            ->orderBy('starts_at')
            ->take(3)
            ->get();

        return view('events.show', compact('event', 'related'));
    }
}

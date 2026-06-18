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

        // Sadece KENDİ etkinliklerimiz — dış konserler /events/concerts'te.
        $upcomingQ = Event::active()->own()->upcoming();
        $pastQ     = Event::active()->own()->past();
        $liveQ     = Event::active()->own()->live();

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

        // Etkinlik bildirimi aboneliği için: yaklaşan KENDİ etkinliği olan şehirler.
        $alertCityIds = Event::active()->own()->upcoming()->whereNotNull('city_id')->distinct()->pluck('city_id');
        $alertCities = \App\Models\City::whereIn('id', $alertCityIds)
            ->orderBy('name_de')
            ->get(['id', 'name_tr', 'name_en', 'name_de', 'slug']);

        // Dış konser sayfasına çapraz-link için sayaç.
        $concertCount = Event::active()->external()->upcoming()->count();

        return view('events.index', compact('live', 'upcoming', 'past', 'type', 'category', 'categories', 'alertCities', 'concertCount'));
    }

    /**
     * Dış konser & kültür etkinlikleri (Ticketmaster) — kendi etkinliklerimizden ayrı sayfa.
     * Şehir + tip filtresi, tarihe göre sıralı, sayfalı.
     */
    public function concerts(Request $request): View
    {
        $type     = $request->query('type');
        $citySlug = $request->query('city');
        $when     = $request->query('when'); // today | weekend | week | month

        // Sadece kültür tipleri geçerli (güvenlik)
        $cultureTypes = array_keys(array_filter(
            Event::TYPES,
            fn ($meta) => ($meta['category'] ?? null) === 'culture'
        ));

        $activeCity = $citySlug
            ? \App\Models\City::where('slug', $citySlug)->first(['id', 'name_tr', 'name_en', 'name_de', 'slug'])
            : null;

        // Şehir + tip filtresi — highlight ve ana liste ortak kullanır.
        $applyBase = function ($query) use ($type, $cultureTypes, $activeCity) {
            if ($type && in_array($type, $cultureTypes, true)) {
                $query->where('type', $type);
            }
            if ($activeCity) {
                $query->where('city_id', $activeCity->id);
            }

            return $query;
        };

        // 🔥 Öne çıkanlar — en yakın 6 (gün filtresinden bağımsız, şehir+tip uygulanır)
        $highlights = $applyBase(Event::active()->external()->upcoming())
            ->orderBy('starts_at')->take(6)->get();

        // Ana liste — şehir + tip + gün filtresi, sayfalı
        $q = $applyBase(Event::active()->external()->upcoming());
        if ($when) {
            $now = now();
            match ($when) {
                'today'   => $q->whereBetween('starts_at', [$now, $now->copy()->endOfDay()]),
                'weekend' => $q->whereBetween('starts_at', [
                    $now->isWeekend() ? $now->copy()->startOfDay() : $now->copy()->next(\Carbon\Carbon::SATURDAY)->startOfDay(),
                    ($now->isWeekend() ? $now->copy() : $now->copy()->next(\Carbon\Carbon::SATURDAY))->endOfWeek(\Carbon\Carbon::SUNDAY),
                ]),
                'week'    => $q->whereBetween('starts_at', [$now, $now->copy()->addDays(7)]),
                'month'   => $q->whereBetween('starts_at', [$now, $now->copy()->addMonth()]),
                default   => null,
            };
        }
        $events = $q->orderBy('starts_at')->paginate(24)->withQueryString();

        // Şehir kartları: yaklaşan dış etkinlik sayısı + şehir görseli (eventim tarzı).
        $cityCounts = Event::active()->external()->upcoming()
            ->whereNotNull('city_id')
            ->selectRaw('city_id, COUNT(*) AS c')
            ->groupBy('city_id')
            ->pluck('c', 'city_id');

        // Filtre için en çok etkinliği olan ilk 5 şehir (fotoğraflı kart)
        $cities = \App\Models\City::whereIn('id', $cityCounts->keys())
            ->get(['id', 'name_tr', 'name_en', 'name_de', 'slug', 'image_url'])
            ->sortByDesc(fn ($c) => $cityCounts[$c->id] ?? 0)
            ->take(5)
            ->values();

        // "Bana haber ver" dropdown'u için: yaklaşan dış etkinliği olan TÜM şehirler
        $alertCities = \App\Models\City::whereIn('id', $cityCounts->keys())
            ->orderBy('name_de')
            ->get(['id', 'name_tr', 'name_en', 'name_de', 'slug']);

        // Tip filtresi seçenekleri (sadece mevcut olan kültür tipleri).
        $usedTypes = Event::active()->external()->upcoming()->distinct()->pluck('type')->all();
        $typeOptions = array_filter(
            Event::TYPES,
            fn ($meta, $key) => in_array($key, $cultureTypes, true) && in_array($key, $usedTypes, true),
            ARRAY_FILTER_USE_BOTH
        );

        return view('events.concerts', compact('events', 'highlights', 'cities', 'cityCounts', 'alertCities', 'activeCity', 'type', 'typeOptions', 'when'));
    }

    public function show(string $slug): View
    {
        $event = Event::active()->where('slug', $slug)
            ->with(['hostUser:id,name,slug,avatar_url,role_label', 'goingRsvps', 'approvedReviews'])
            ->firstOrFail();

        $related = Event::active()
            ->where('id', '!=', $event->id)
            ->where('type', $event->type)
            ->upcoming()
            ->orderBy('starts_at')
            ->take(3)
            ->get();

        // Mevcut kullanıcının RSVP'si (UI'da göstermek için)
        $myRsvp = null;
        if ($user = request()->user()) {
            $myRsvp = \App\Models\EventRsvp::where('event_id', $event->id)->where('user_id', $user->id)->first();
        }

        return view('events.show', compact('event', 'related', 'myRsvp'));
    }

    /**
     * RSVP submit — anonim veya login.
     */
    public function rsvp(Request $request, string $slug): \Illuminate\Http\RedirectResponse
    {
        $event = Event::active()->where('slug', $slug)->firstOrFail();

        if ($event->starts_at->isPast()) {
            return back()->with('rsvp_status', __('This event has already taken place.'));
        }

        $data = $request->validate([
            'status'         => 'required|in:going,maybe,cancelled',
            'attendee_name'  => 'nullable|string|max:80',
            'attendee_email' => 'nullable|email|max:150',
            'note'           => 'nullable|string|max:500',
            'website'        => 'nullable|string|max:200', // honeypot
        ]);

        if (! empty($data['website'])) {
            return back()->with('rsvp_status', __('Thanks!'));
        }

        $user = $request->user();
        if (! $user) {
            $request->validate([
                'attendee_name'  => 'required|string|min:2|max:80',
                'attendee_email' => 'required|email|max:150',
            ]);
        }

        $rsvp = \App\Models\EventRsvp::updateOrCreate(
            $user
                ? ['event_id' => $event->id, 'user_id' => $user->id]
                : ['event_id' => $event->id, 'attendee_email' => $data['attendee_email']],
            [
                'attendee_name'  => $user ? null : ($data['attendee_name'] ?? null),
                'attendee_email' => $user ? null : ($data['attendee_email'] ?? null),
                'status'         => $data['status'],
                'note'           => $data['note'] ?? null,
                'ip_address'     => $request->ip(),
                'user_agent'     => substr((string) $request->userAgent(), 0, 255),
            ]
        );

        // Counts'u güncelle (Event.registered_count + maybe_count)
        $going = \App\Models\EventRsvp::where('event_id', $event->id)->where('status', 'going')->count();
        $maybe = \App\Models\EventRsvp::where('event_id', $event->id)->where('status', 'maybe')->count();
        Event::where('id', $event->id)->update(['registered_count' => $going, 'maybe_count' => $maybe]);

        $msg = match ($data['status']) {
            'going'     => __('See you there! Your spot is reserved.'),
            'maybe'     => __('Saved as Maybe — you can update your answer any time.'),
            'cancelled' => __('Your RSVP is cancelled. We hope to see you next time.'),
        };

        return back()->with('rsvp_status', $msg)->withFragment('rsvp');
    }

    /**
     * Etkinlik için review/rating bırak (sadece geçmiş etkinlikler).
     */
    public function review(Request $request, string $slug): \Illuminate\Http\RedirectResponse
    {
        $event = Event::active()->where('slug', $slug)->firstOrFail();

        if ($event->starts_at->isFuture()) {
            return back()->with('review_status', __('Reviews open after the event ends.'));
        }

        $data = $request->validate([
            'rating'         => 'required|integer|min:1|max:5',
            'body'           => 'nullable|string|max:1500',
            'attendee_name'  => 'nullable|string|max:80',
            'attendee_email' => 'nullable|email|max:150',
            'website'        => 'nullable|string|max:200',
        ]);

        if (! empty($data['website'])) {
            return back()->with('review_status', __('Thanks!'));
        }

        $user = $request->user();
        if (! $user) {
            $request->validate([
                'attendee_name'  => 'required|string|min:2|max:80',
                'attendee_email' => 'required|email|max:150',
            ]);
        }

        \App\Models\EventReview::create([
            'event_id'       => $event->id,
            'user_id'        => $user?->id,
            'rating'         => $data['rating'],
            'attendee_name'  => $user ? null : ($data['attendee_name'] ?? null),
            'attendee_email' => $user ? null : ($data['attendee_email'] ?? null),
            'body'           => $data['body'] ?? null,
            'status'         => 'pending',
            'ip_address'     => $request->ip(),
            'user_agent'     => substr((string) $request->userAgent(), 0, 255),
        ]);

        return back()
            ->with('review_status', __('Thanks for your review — it\'s pending moderation.'))
            ->withFragment('reviews');
    }
}

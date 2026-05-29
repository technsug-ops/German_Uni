<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\MentorBookingConfirmation;
use App\Models\Mentor;
use App\Models\MentorSession;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
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

    /**
     * Book an in-app mentor session — auto-generates a Jitsi meeting room
     * and emails both parties. Auth required.
     */
    public function book(Request $request, string $slug): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login')->with('error', __('Login to book a mentor session.'));
        }

        $mentor = Mentor::active()->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'scheduled_at'       => 'required|date|after:+30 minutes',
            'duration_minutes'   => 'sometimes|integer|in:15,30,45,60',
            'topic'              => 'nullable|string|max:200',
            'notes'              => 'nullable|string|max:2000',
            'preferred_language' => 'sometimes|in:tr,en,de',
        ]);

        // Prevent double-booking the same time slot for this mentor
        $conflict = MentorSession::where('mentor_id', $mentor->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereBetween('scheduled_at', [
                \Carbon\Carbon::parse($data['scheduled_at'])->subMinutes(15),
                \Carbon\Carbon::parse($data['scheduled_at'])->addMinutes(($data['duration_minutes'] ?? 30) + 15),
            ])->exists();

        if ($conflict) {
            return back()->with('error', __('This time slot is no longer available. Please pick another.'));
        }

        $session = MentorSession::create([
            'mentor_id'          => $mentor->id,
            'user_id'            => $user->id,
            'scheduled_at'       => $data['scheduled_at'],
            'duration_minutes'   => $data['duration_minutes'] ?? ($mentor->session_duration ?? 30),
            'topic'              => $data['topic']  ?? null,
            'notes'              => $data['notes']  ?? null,
            'preferred_language' => $data['preferred_language'] ?? app()->getLocale(),
            'external_provider'  => 'in_app',
            'status'             => 'pending',
        ]);

        // Email both parties — fail silently to not break booking flow
        try {
            Mail::to($user->email)->queue(new MentorBookingConfirmation($session, 'user'));
            if ($mentor->contact_email) {
                Mail::to($mentor->contact_email)->queue(new MentorBookingConfirmation($session, 'mentor'));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Mentor booking email failed', [
                'session' => $session->id, 'err' => $e->getMessage(),
            ]);
        }

        return redirect()->route('mentors.show', $mentor->slug)
            ->with('success', __('Session booked! Jitsi link sent to your email.'));
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Models\UniversityReview;
use App\Models\UniversityReviewVote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UniversityReviewController extends Controller
{
    /** Yorum gönder (form POST — universities/show.blade.php). */
    public function store(Request $request, string $uniSlug): RedirectResponse
    {
        $uni = University::where('slug', $uniSlug)->firstOrFail();

        $data = $request->validate([
            'rating'         => 'required|integer|min:1|max:5',
            'title'          => 'required|string|max:200',
            'body'           => 'required|string|min:30|max:2500',
            'author_name'    => 'required|string|max:100',
            'author_email'   => 'required|email|max:150',
            'author_program' => 'nullable|string|max:150',
            'author_status'  => 'nullable|in:current_student,alumni,admitted,applicant',
            'study_year'     => 'nullable|integer|min:1990|max:' . (now()->year + 1),
            'consent'        => 'accepted', // KVKK + content policy onayı
            'captcha_answer' => ['required', new \App\Rules\MathCaptchaRule()],
        ]);

        // Email başına 1 review per uni — dupe önleme (DB unique zaten engelliyor ama UX iyi mesaj)
        $existing = UniversityReview::where('university_id', $uni->id)
            ->where('author_email', strtolower($data['author_email']))
            ->first();
        if ($existing) {
            return back()
                ->withErrors(['author_email' => __('You have already submitted a review for this university.')])
                ->withInput();
        }

        $review = UniversityReview::create([
            'university_id'     => $uni->id,
            'user_id'           => auth()->id(),
            'author_name'       => $data['author_name'],
            'author_email'      => strtolower($data['author_email']),
            'author_program'    => $data['author_program'] ?? null,
            'author_status'     => $data['author_status'] ?? null,
            'study_year'        => $data['study_year'] ?? null,
            'rating'            => $data['rating'],
            'title'             => $data['title'],
            'body'              => $data['body'],
            'locale'            => app()->getLocale(),
            'status'            => 'pending',
            'verification_token'=> Str::random(48),
            'ip_address'        => $request->ip(),
            'user_agent'        => substr((string) $request->userAgent(), 0, 500),
        ]);

        // Mail doğrulama gönder — verification link tıklanmazsa moderation kuyruğa girmez
        try {
            $verifyUrl = route('reviews.verify', ['token' => $review->verification_token]);
            Mail::raw(
                __('Hello, please confirm your review for :uni by clicking: :url', [
                    'uni' => $uni->display_name ?? $uni->name_de,
                    'url' => $verifyUrl,
                ]),
                fn ($m) => $m->to($review->author_email)->subject(__('Verify your review on :brand', ['brand' => brand('name')]))
            );
        } catch (\Throwable $e) {
            // Mail gitmediyse review yine var, manuel moderate edilir
            logger()->warning('Review verification email failed', ['error' => $e->getMessage(), 'review_id' => $review->id]);
        }

        return back()->with('status', __('Thanks! Please check your email to verify your review. After verification it will be reviewed and published within 48 hours.'));
    }

    /** Email tıklamasıyla doğrula. */
    public function verify(string $token): RedirectResponse
    {
        $review = UniversityReview::where('verification_token', $token)->first();
        if (! $review) {
            return redirect()->route('home')->with('status', __('Verification link invalid or already used.'));
        }
        $review->update([
            'is_verified'        => true,
            'verified_at'        => now(),
            'verification_token' => null,
        ]);

        return redirect()->route('universities.show', $review->university->slug)
            ->with('status', __('Email verified. Your review is now in moderation queue.'));
    }

    /** Helpful / unhelpful / report — JS fetch çağırır. */
    public function vote(Request $request, UniversityReview $review): JsonResponse
    {
        $request->validate(['vote' => 'required|in:helpful,unhelpful,report']);

        if ($review->status !== 'approved') {
            return response()->json(['error' => 'review_not_visible'], 403);
        }

        $userId = auth()->id();
        $sessionToken = $userId ? null : ($request->session()->get('rv_token') ?: ($request->session()->put('rv_token', Str::random(48)) ?? $request->session()->get('rv_token')));

        $existing = UniversityReviewVote::where('review_id', $review->id)
            ->where('vote', $request->vote)
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->where('session_token', $sessionToken))
            ->first();

        if ($existing) {
            return response()->json(['status' => 'already_voted', 'count' => $review->{$request->vote . '_count'}]);
        }

        UniversityReviewVote::create([
            'review_id'     => $review->id,
            'user_id'       => $userId,
            'session_token' => $sessionToken,
            'vote'          => $request->vote,
        ]);

        $col = $request->vote . '_count';
        $review->increment($col);

        return response()->json([
            'status' => 'ok',
            'count'  => $review->{$col},
        ]);
    }
}

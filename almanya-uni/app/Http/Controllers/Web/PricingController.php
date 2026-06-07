<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\PremiumInterestConfirmation;
use App\Models\PremiumInterest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function index(Request $request): View
    {
        // Real-time counters — cached 5 min to stay snappy.
        $counts = Cache::remember('pricing.counters.v2', 300, function () {
            return [
                'total'        => PremiumInterest::count(),
                'beta_signups' => PremiumInterest::where('wants_beta', true)->count(),
                'premium_tier' => PremiumInterest::where('tier_interest', 'premium')->count(),
                'pro_tier'     => PremiumInterest::where('tier_interest', 'pro')->count(),
            ];
        });

        return view('pricing.index', [
            'isPremium'   => $request->user()?->isPremium() ?? false,
            'currentTier' => $request->user()?->premiumTier(),
            'counts'      => $counts,
        ]);
    }

    public function express(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email'          => 'required|email|max:150',
            'name'           => 'nullable|string|max:100',
            'tier_interest'  => 'required|in:premium,pro,undecided',
            'note'           => 'nullable|string|max:1000',
            'wants_beta'     => 'sometimes|boolean',
            'captcha_answer' => ['required', new \App\Rules\MathCaptchaRule()],
        ]);

        unset($data['captcha_answer']);
        $data['wants_beta'] = (bool) ($data['wants_beta'] ?? false);

        // Idempotent: same email re-submits → update existing record
        $interest = PremiumInterest::updateOrCreate(
            ['email' => strtolower(trim($data['email']))],
            array_merge($data, [
                'source_page' => $request->headers->get('referer'),
                'locale'      => app()->getLocale(),
            ])
        );

        // Confirmation mail — fail silently so submission UX isn't broken
        try {
            Mail::to($interest->email)->send(new PremiumInterestConfirmation($interest));
            $interest->update(['confirmation_sent_at' => now()]);
        } catch (\Throwable $e) {
            Log::warning('Premium interest confirmation mail failed', [
                'email' => $interest->email,
                'err'   => $e->getMessage(),
            ]);
        }

        // Invalidate counter cache so the next visitor sees the increment
        Cache::forget('pricing.counters.v2');

        $msg = $interest->wants_beta
            ? __('Thanks! You\'re on the beta tester list — we\'ll invite you within 2 weeks.')
            : __('Thanks! We\'ll email you when premium launches with early-bird discount.');

        return back()->with('status', $msg)->with('interest_submitted', true);
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PremiumInterest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function index(Request $request): View
    {
        $isPremium = $request->user()?->isPremium() ?? false;
        return view('pricing.index', [
            'isPremium' => $isPremium,
            'currentTier' => $request->user()?->premiumTier(),
        ]);
    }

    public function express(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email'         => 'required|email|max:150',
            'name'          => 'nullable|string|max:100',
            'tier_interest' => 'required|in:premium,pro,undecided',
            'note'          => 'nullable|string|max:1000',
        ]);

        PremiumInterest::create($data + [
            'source_page' => $request->headers->get('referer'),
            'locale'      => app()->getLocale(),
        ]);

        return back()->with('status', __('Thanks! We\'ll email you when premium launches with early-bird discount.'));
    }
}

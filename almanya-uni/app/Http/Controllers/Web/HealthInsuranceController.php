<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\HealthInsuranceProvider;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HealthInsuranceController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter');

        $providers = HealthInsuranceProvider::published()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $filtered = match ($filter) {
            'cheapest' => $providers->sortBy(fn ($p) => $p->yearly_estimate_eur ?? PHP_INT_MAX)->values(),
            'public'   => $providers->where('type', 'public')->values(),
            'private'  => $providers->where('type', 'private')->values(),
            'expat'    => $providers->where('type', 'expat')->values(),
            'enrollment' => $providers->where('accepted_for_enrollment', true)->values(),
            'dental'   => $providers->where('covers_dental', true)->values(),
            'english'  => $providers->where('english_support', true)->values(),
            default    => $providers,
        };

        $totals = [
            'total'   => $providers->count(),
            'public'  => $providers->where('type', 'public')->count(),
            'private' => $providers->where('type', 'private')->count(),
            'expat'   => $providers->where('type', 'expat')->count(),
        ];

        return view('tools.health-insurance.index', [
            'providers'     => $filtered,
            'all_providers' => $providers,
            'filter'        => $filter,
            'totals'        => $totals,
        ]);
    }

    public function show(string $slug): View
    {
        $provider = HealthInsuranceProvider::published()->where('slug', $slug)->firstOrFail();

        $others = HealthInsuranceProvider::published()
            ->where('id', '!=', $provider->id)
            ->orderBy('sort_order')
            ->get();

        return view('tools.health-insurance.show', [
            'provider' => $provider,
            'others'   => $others,
        ]);
    }
}

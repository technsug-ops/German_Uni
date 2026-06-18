<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FieldOfStudy;
use App\Models\Program;
use App\Models\University;
use Illuminate\Contracts\View\View;

/**
 * Yüksek-niyetli keşif hub'ları (programmatic SEO). Yeni thin sayfa üretmez —
 * mevcut programs.index filtrelerine derin-link verir; hub'ın kendisi indexlenir.
 */
class ProgramDiscoveryController extends Controller
{
    /** /english-taught — Almanya'da İngilizce verilen bölümler. */
    public function englishTaught(): View
    {
        $langs = ['en', 'both'];

        $fields = FieldOfStudy::active()
            ->withCount(['programs as cnt' => fn ($q) => $q->where('is_active', true)->whereIn('language', $langs)])
            ->get()
            ->filter(fn ($f) => $f->cnt > 0)
            ->sortByDesc('cnt')
            ->values();

        $total = Program::where('is_active', true)->whereIn('language', $langs)->count();

        $topUnis = University::where('is_active', true)
            ->withCount(['programs as cnt' => fn ($q) => $q->where('is_active', true)->whereIn('language', $langs)])
            ->having('cnt', '>', 0)
            ->orderByDesc('cnt')
            ->take(12)
            ->get();

        return view('discover.english', compact('fields', 'total', 'topUnis'));
    }
}

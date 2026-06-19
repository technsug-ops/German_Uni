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

    /** /tuition-free — ücretsiz (devlet üniversitesi) bölümler. */
    public function tuitionFree(): View
    {
        // Ücretsiz = harç 0 VEYA (harç bilinmiyor + üni özel değil). Devlet üni'ler
        // Almanya'da öğrenim ücreti almaz (yalnız dönem katkısı). Özel üni hariç.
        $free = fn ($q) => $q->where('is_active', true)->where(function ($w) {
            $w->where('tuition_fee_eur', 0)->orWhere(function ($n) {
                $n->whereNull('tuition_fee_eur')->whereHas('university', fn ($u) => $u->where('type', '!=', 'private'));
            });
        });

        $fields = FieldOfStudy::active()
            ->withCount(['programs as cnt' => $free])
            ->get()
            ->filter(fn ($f) => $f->cnt > 0)
            ->sortByDesc('cnt')
            ->values();

        $total = $free(Program::query())->count();

        $topUnis = University::where('is_active', true)
            ->where('type', '!=', 'private')
            ->withCount(['programs as cnt' => fn ($q) => $q->where('is_active', true)
                ->where(fn ($w) => $w->where('tuition_fee_eur', 0)->orWhereNull('tuition_fee_eur'))])
            ->having('cnt', '>', 0)
            ->orderByDesc('cnt')
            ->take(12)
            ->get();

        return view('discover.tuition-free', compact('fields', 'total', 'topUnis'));
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FieldOfStudy;
use App\Models\Post;
use App\Models\Program;
use App\Models\University;
use Illuminate\Contracts\View\View;

/**
 * Yüksek-niyetli keşif hub'ları (programmatic SEO). Yeni thin sayfa üretmez —
 * mevcut programs.index filtrelerine derin-link verir; hub'ın kendisi indexlenir.
 */
class ProgramDiscoveryController extends Controller
{
    /**
     * /english-taught — İngilizce lisans ve yüksek lisans programlarını bulma sayfası (program seçimi; kurum seçimi
     * universities/collections/english-taught-universities sayfasında).
     *
     * Sayılar katalog dil etiketinden gelir (resmî doğrulama değildir) ve programs.index filtreleriyle aynı kümeyi
     * sayar: "tamamen İngilizce" = language 'en', "iki dilli" = language 'both' — ikisi birbirine katılmaz. Dil ya da
     * kimlik doğrulaması resmî kaynakla çelişen kayıtlar sayılmaz; liste filtresi de aynı kuralı uygular.
     */
    public function englishTaught(): View
    {
        $counts = Program::where('is_active', true)->withoutLanguageConflict()
            ->whereIn('language', ['en', 'both'])
            ->whereIn('degree', ['bachelor', 'master'])
            ->selectRaw('degree, language, count(*) as c')
            ->groupBy('degree', 'language')
            ->get()
            ->mapWithKeys(fn ($r) => ["{$r->degree}.{$r->language}" => (int) $r->c]);

        $fields = FieldOfStudy::active()
            ->withCount(['programs as cnt' => fn ($q) => $q->where('is_active', true)->withoutLanguageConflict()->where('language', 'en')])
            ->get()
            ->filter(fn ($f) => $f->cnt > 0)
            ->sortByDesc('cnt')
            ->values();

        // Başvuru (İngilizce master) ve dil belgesi kararı için aynı dildeki rehberler (çeviri grubu kardeşleri).
        $locale = app()->getLocale();
        $guides = collect(self::ENGLISH_GUIDES)
            ->map(fn ($slugs) => Post::where('is_published', true)->where('locale', $locale)->whereIn('slug', (array) ($slugs[$locale] ?? []))->first())
            ->filter();

        return view('discover.english', compact('counts', 'fields', 'guides'));
    }

    private const ENGLISH_GUIDES = [
        'master' => [
            'tr' => 'english-masters-in-germany-without-german-knowledge-finding-programs-and-application',
            'en' => 'english-masters-in-germany-without-german-knowledge-finding-programs-and-application-en',
            'de' => 'english-masters-in-germany-without-german-knowledge-finding-programs-and-application-de',
        ],
        'language' => [
            'tr' => ['goethe-telc-testdaf-dsh-difference-german-language-exam-comparison-for-turkish', 'goethe-telc-testdaf-dsh-differences-german-language-exam-comparison-for-turkish'],
            'en' => ['goethe-telc-testdaf-dsh-difference-german-language-exam-comparison-for-turkish-en', 'goethe-telc-testdaf-dsh-differences-german-language-exam-comparison-for-turkish-en'],
            'de' => ['goethe-telc-testdaf-dsh-difference-german-language-exam-comparison-for-turkish-de', 'goethe-telc-testdaf-dsh-differences-german-language-exam-comparison-for-turkish-de'],
        ],
    ];

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

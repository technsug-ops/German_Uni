<?php

use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\CityController;
use App\Http\Controllers\Web\CompareController;
use App\Http\Controllers\Web\FaqController;
use App\Http\Controllers\Web\FieldController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\HousingController;
use App\Http\Controllers\Web\LegalController;
use App\Http\Controllers\Web\MapController;
use App\Http\Controllers\Web\NewsletterController;
use App\Http\Controllers\Web\ProfessionController;
use App\Http\Controllers\Web\ProgramController;
use App\Http\Controllers\Web\RankingController;
use App\Http\Controllers\Web\ScholarshipController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\SitemapController;
use App\Http\Controllers\Web\StateController;
use App\Http\Controllers\Web\ToolsController;
use App\Http\Controllers\Web\UniversityWebController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Çoklu dil yapılandırması (i18n)
|--------------------------------------------------------------------------
| TR (default) prefix YOK: /programs
| EN: /en/programs · DE: /de/programs · AR: /ar/programs (aktif olunca)
|
| Route name'leri locale prefix ile: "programs.index" (TR), "en.programs.index" (EN), ...
| `lroute('programs.index')` helper'ı current locale'a göre doğru name'i çağırır.
*/

$routes = function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::get('/search/suggest', \App\Http\Controllers\Web\SearchSuggestController::class)
        ->middleware('throttle:60,1')
        ->name('search.suggest');
    Route::get('/about', [AboutController::class, 'index'])->name('about');
    Route::get('/ekip', [AboutController::class, 'team'])->name('team');
    Route::redirect('/team', '/ekip', 301);
    Route::get('/almanyada-egitim', [\App\Http\Controllers\Web\AboutGermanyController::class, 'index'])->name('study.germany');
    Route::redirect('/about-germany', '/almanyada-egitim', 301);
    Route::redirect('/neden-almanya', '/almanyada-egitim', 301);
    Route::redirect('/yazarlar', '/ekip', 301);
    Route::post('/feedback', [\App\Http\Controllers\Web\FeedbackController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('feedback.store');

    // Newsletter — double opt-in akış
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
        ->middleware('throttle:10,1')
        ->name('newsletter.subscribe');
    Route::get('/newsletter/confirm/{token}', [NewsletterController::class, 'confirm'])
        ->name('newsletter.confirm');
    Route::match(['get', 'post'], '/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])
        ->name('newsletter.unsubscribe');

    // Legal pages — KVKK + GDPR + TMG (DB-driven, admin-editable via Filament)
    Route::get('/gizlilik',     [LegalController::class, 'privacy'])->name('legal.privacy');
    Route::get('/kosullar',     [LegalController::class, 'terms'])->name('legal.terms');
    Route::get('/cerez-politikasi', [LegalController::class, 'cookies'])->name('legal.cookies');
    Route::get('/impressum',    [LegalController::class, 'impressum'])->name('legal.impressum');
    Route::get('/yasal-uyari',  [LegalController::class, 'disclaimer'])->name('legal.disclaimer');

    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

    Route::get('/faq', [FaqController::class, 'index'])->name('faqs.index');
    Route::get('/faq/{topic}', [FaqController::class, 'topic'])->name('faqs.topic');
    Route::get('/faq/{topic}/{slug}', [FaqController::class, 'show'])->name('faqs.show');

    // Almanya eğitim sistemi sözlüğü (semantic SEO — APS, Anabin, Sperrkonto, DAAD, vb.)
    Route::get('/sozluk', [\App\Http\Controllers\Web\GlossaryController::class, 'index'])->name('glossary.index');
    Route::get('/sozluk/{slug}', [\App\Http\Controllers\Web\GlossaryController::class, 'show'])->name('glossary.show');

    Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
    Route::get('/compare/result', [CompareController::class, 'show'])->name('compare.show');

    Route::get('/rankings', [RankingController::class, 'index'])->name('rankings.index');
    Route::get('/rankings/{slug}', [RankingController::class, 'show'])->name('rankings.show');

    Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');

    // Programmatic SEO landing pages — locale-aware, SEO-optimized filtered lists.
    // Spesifik route'lar /programs/{slug}'tan ÖNCE tanımlanmalı (route order matters).
    Route::get('/programs/city/{city}/field/{field}', [\App\Http\Controllers\Web\LandingController::class, 'cityField'])->name('programs.city-field');
    Route::get('/programs/city/{city}/language/{lang}', [\App\Http\Controllers\Web\LandingController::class, 'cityLanguage'])->name('programs.city-language');
    Route::get('/programs/field/{field}/degree/{degree}', [\App\Http\Controllers\Web\LandingController::class, 'fieldDegree'])->name('programs.field-degree');

    Route::get('/programs/{slug}', [ProgramController::class, 'show'])->name('programs.show');

    Route::get('/fields', [FieldController::class, 'index'])->name('fields.index');
    Route::get('/fields/{slug}', [FieldController::class, 'show'])->name('fields.show');

    Route::get('/states', [StateController::class, 'index'])->name('states.index');
    Route::get('/states/{slug}', [StateController::class, 'show'])->name('states.show');

    // Burslar — DB tabanlı DAAD scholarship database (166 burs) + statik rehberler.
    // Specific path'ler /scholarships/{slug} CATCH-ALL'tan ÖNCE tanımlanmalı.
    Route::get('/scholarships', [ScholarshipController::class, 'index'])->name('scholarships.index');
    Route::get('/scholarships/guide', [ScholarshipController::class, 'staticIndex'])->name('scholarships.guide');
    Route::get('/scholarships/daad', [ScholarshipController::class, 'daad'])->name('scholarships.daad');
    Route::get('/scholarships/{slug}', [ScholarshipController::class, 'show'])
        ->where('slug', '[a-z0-9\-]+')
        ->name('scholarships.show');

    Route::get('/professions', [ProfessionController::class, 'index'])->name('professions.index');
    Route::get('/professions/{slug}', [ProfessionController::class, 'show'])->name('professions.show');

    Route::get('/map', [MapController::class, 'index'])->name('map.index');

    Route::prefix('tools')->name('tools.')->group(function () {
        Route::get('/', [ToolsController::class, 'index'])->name('index');
        Route::get('/cost-of-living', [ToolsController::class, 'costOfLiving'])->name('cost-of-living');
        Route::get('/grade-converter', [ToolsController::class, 'gradeConverter'])->name('grade-converter');
        Route::get('/visa-cost', [ToolsController::class, 'visaCost'])->name('visa-cost');
        Route::get('/deadlines', [ToolsController::class, 'deadlines'])->name('deadlines');
        Route::get('/deadlines/ics', [ToolsController::class, 'deadlinesIcs'])->name('deadlines.ics');
        Route::get('/budget-planner', [ToolsController::class, 'budgetPlanner'])->name('budget-planner');
        Route::match(['get', 'post'], '/recommendation', [ToolsController::class, 'recommendation'])->name('recommendation');
        Route::match(['get', 'post'], '/career-compass', [ToolsController::class, 'careerCompass'])->name('career-compass');
        Route::match(['get', 'post'], '/eligibility-checker', [ToolsController::class, 'eligibilityChecker'])->name('eligibility-checker');
        Route::get('/studienkolleg', [ToolsController::class, 'studienkolleg'])->name('studienkolleg');
        Route::get('/sperrkonto', [\App\Http\Controllers\Web\BlockedAccountController::class, 'index'])->name('blocked-account');
        Route::get('/sperrkonto/country/{country}', [\App\Http\Controllers\Web\BlockedAccountController::class, 'country'])->name('blocked-account.country');
        Route::get('/sperrkonto/{slug}', [\App\Http\Controllers\Web\BlockedAccountController::class, 'show'])->name('blocked-account.show');
    });
    Route::redirect('/araclar/kariyer-pusulasi', '/tools/career-compass', 301);
    Route::redirect('/tools/bloke-hesap', '/tools/sperrkonto', 301);
    Route::redirect('/araclar/sperrkonto', '/tools/sperrkonto', 301);

    // University Reviews — UGC + helpful votes (i18n)
    Route::post('/universities/{uniSlug}/reviews', [\App\Http\Controllers\Web\UniversityReviewController::class, 'store'])
        ->middleware('throttle:5,60')
        ->name('universities.reviews.store');
    Route::post('/reviews/{review}/vote', [\App\Http\Controllers\Web\UniversityReviewController::class, 'vote'])
        ->middleware('throttle:30,1')
        ->name('reviews.vote');
    Route::get('/reviews/verify/{token}', [\App\Http\Controllers\Web\UniversityReviewController::class, 'verify'])
        ->name('reviews.verify');

    Route::get('/universities', [UniversityWebController::class, 'index'])->name('universities.index');
    // NC Frei programmatic SEO — uni 'nc-free' özel rota (uni slug'tan ÖNCE tanımlanmalı)
    Route::get('/universities/{slug}/nc-free', [\App\Http\Controllers\Web\AdmissionFreeController::class, 'byUniversity'])
        ->name('admission-free.by-university');
    Route::get('/universities/{slugOrId}', [UniversityWebController::class, 'show'])->name('universities.show');

    // NC Frei programmatic SEO — alan grubu
    Route::get('/subjects/{slug}/nc-free', [\App\Http\Controllers\Web\AdmissionFreeController::class, 'bySubject'])
        ->name('admission-free.by-subject');

    // Housing — okuma herkese açık
    Route::get('/housing', [HousingController::class, 'index'])->name('housing.index');
    Route::get('/housing/providers', [HousingController::class, 'providers'])->name('housing.providers');
    Route::get('/housing/providers/{slug}', [HousingController::class, 'providerShow'])->name('housing.provider-show');
    Route::redirect('/housing/saglayicilar', '/housing/providers', 301);
    Route::get('/housing/templates/{slug}', [HousingController::class, 'template'])->name('housing.template');
    Route::get('/housing/tips', [HousingController::class, 'tips'])->name('housing.tips');

    // Events / Etkinlikler
    Route::get('/events', [\App\Http\Controllers\Web\EventController::class, 'index'])->name('events.index');
    Route::get('/events/{slug}', [\App\Http\Controllers\Web\EventController::class, 'show'])->name('events.show');
    Route::redirect('/etkinlikler', '/events', 301);

    // Mentors / Mentorlar
    Route::get('/mentors', [\App\Http\Controllers\Web\MentorController::class, 'index'])->name('mentors.index');
    Route::get('/mentors/{slug}', [\App\Http\Controllers\Web\MentorController::class, 'show'])->name('mentors.show');
    Route::redirect('/mentorlar', '/mentors', 301);

    // City listesi + detay (locale grubu içinde → /tr/cities, /en/cities ...)
    Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
    Route::get('/cities/{slug}', [CityController::class, 'show'])->name('cities.show');

    Route::middleware('auth')->group(function () {
        Route::get('/housing/tips/new', [HousingController::class, 'createTip'])->name('housing.tip-create');
        Route::post('/housing/tips', [HousingController::class, 'storeTip'])->name('housing.tip-store');

        // Topluluk katkısı (deneyim/ipucu paylaşımı)
        Route::get('/deneyim-paylas', [\App\Http\Controllers\Web\ContributionController::class, 'create'])->name('contribute');
        Route::post('/deneyim-paylas', [\App\Http\Controllers\Web\ContributionController::class, 'store'])->middleware('throttle:5,1')->name('contribute.store');
    });

    // Application Tracker — locale grubu içinde (mega menü doğru locale alabilsin)
    Route::get('/journey',                       [\App\Http\Controllers\Web\ApplicationTrackerController::class, 'show'])->name('journey.show');
    Route::post('/journey/step/{step}/toggle',   [\App\Http\Controllers\Web\ApplicationTrackerController::class, 'toggle'])->name('journey.step.toggle');
    Route::patch('/journey/update',              [\App\Http\Controllers\Web\ApplicationTrackerController::class, 'update'])->name('journey.update');

    // Premium pricing
    Route::get('/pricing',  [\App\Http\Controllers\Web\PricingController::class, 'index'])->name('pricing');
    Route::post('/pricing/interest', [\App\Http\Controllers\Web\PricingController::class, 'express'])->middleware('throttle:5,1')->name('pricing.interest');
};

// ─────────── Sitemap & API (locale'siz, tek versiyon) ───────────

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-content.xml', [SitemapController::class, 'content'])->name('sitemap.content');
Route::get('/sitemap-landings.xml', [SitemapController::class, 'landings'])->name('sitemap.landings');
Route::get('/sitemap-glossary.xml', [SitemapController::class, 'glossary'])->name('sitemap.glossary');

// Token-gated image cache trigger (KAS has no SSH/cron — fire via curl after deploy)
//   curl "https://applytogerman.com/_system/cache-hot-images?token=XXX&limit=20"
Route::get('/_system/cache-hot-images', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = env('SYSTEM_TOKEN');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }
    // Long-running; raise PHP limits
    @set_time_limit(600);
    @ini_set('memory_limit', '512M');

    $exit = \Illuminate\Support\Facades\Artisan::call('images:cache-hot', array_filter([
        '--limit' => $request->query('limit'),
        '--width' => $request->query('width'),
        '--logo-width' => $request->query('logo-width'),
        '--quality' => $request->query('quality'),
        '--force' => $request->boolean('force'),
    ], fn ($v) => $v !== null && $v !== false));

    return response()->json([
        'exit' => $exit,
        'output' => \Illuminate\Support\Facades\Artisan::output(),
    ]);
})->middleware('throttle:30,1'); // 30 req / minute (token already protects against abuse)

// Token-gated migration runner — KAS has no CLI access, run via curl after deploy
Route::get('/_system/migrate', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = env('SYSTEM_TOKEN');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }
    @set_time_limit(300);

    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $migrate = \Illuminate\Support\Facades\Artisan::output();

    $seedClass = $request->query('seed');
    $seed = null;
    if ($seedClass && preg_match('/^[A-Za-z0-9_]+Seeder$/', $seedClass)) {
        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\' . $seedClass,
            '--force' => true,
        ]);
        $seed = \Illuminate\Support\Facades\Artisan::output();
    }

    return response()->json(['migrate' => $migrate, 'seed' => $seed]);
})->middleware('throttle:5,1');

// Token-gated stats reset — wipe demo/test traffic numbers to start fresh
//   curl "https://applytogerman.com/_system/reset-stats?token=XXX&dry-run=1"  (preview)
//   curl "https://applytogerman.com/_system/reset-stats?token=XXX"            (apply)
Route::get('/_system/reset-stats', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = env('SYSTEM_TOKEN');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }

    $args = [];
    if ($request->boolean('dry-run')) $args['--dry-run'] = true;
    if ($request->boolean('keep-engagements')) $args['--keep-engagements'] = true;

    \Illuminate\Support\Facades\Artisan::call('stats:reset', $args);
    return response()->json([
        'output' => \Illuminate\Support\Facades\Artisan::output(),
    ]);
})->middleware('throttle:5,1');

// Dual-brand robots.txt — absolute Sitemap URL + brand-aware
Route::get('/robots.txt', function (\Illuminate\Http\Request $request) {
    $host = strtolower(preg_replace('/^www\./', '', $request->getHost()));
    $domains = config('brand.domains', []);
    $brandKey = $domains[$host] ?? config('brand.fallback', 'almanyauni');
    $brands = config('brand.brands', []);
    $domain = $brands[$brandKey]['domain'] ?? $host;
    $base = $request->getScheme() . '://' . $domain;

    $lines = [
        'User-agent: *',
        'Allow: /',
        '',
        '# Filtre/state sayfaları — indexe kapalı (duplicate önleme)',
        'Disallow: /search',
        'Disallow: /arama',
        'Disallow: /*/search',
        'Disallow: /*/arama',
        'Disallow: /compare/result',
        'Disallow: /karsilastir/sonuc',
        'Disallow: /*/compare/result',
        'Disallow: /api/',
        '',
        '# Admin paneli',
        'Disallow: /admin/',
        'Disallow: /livewire/',
        '',
        '# Auth sayfaları',
        'Disallow: /login',
        'Disallow: /register',
        'Disallow: /password/',
        '',
        '# Sitemap & RSS — absolute URL (current brand)',
        'Sitemap: ' . $base . '/sitemap.xml',
        'Sitemap: ' . $base . '/rss.xml',
        '',
        '# AI bot\'lar açık (ChatGPT, Claude, Gemini)',
        'User-agent: GPTBot',
        'Allow: /',
        '',
        'User-agent: ClaudeBot',
        'Allow: /',
        '',
        'User-agent: Google-Extended',
        'Allow: /',
    ];

    return response(implode("\n", $lines) . "\n", 200)
        ->header('Content-Type', 'text/plain; charset=utf-8');
});

// llms.txt — AI/LLM crawler için içerik haritası (https://llmstxt.org/ standardı)
// Brand-aware: her marka için ayrı içerik
Route::get('/llms.txt', function (\Illuminate\Http\Request $request) {
    $host = strtolower(preg_replace('/^www\./', '', $request->getHost()));
    $domains = config('brand.domains', []);
    $brandKey = $domains[$host] ?? config('brand.fallback', 'almanyauni');
    $brands = config('brand.brands', []);
    $brand = $brands[$brandKey] ?? [];
    $name = $brand['name'] ?? 'AlmanyaUni';
    $domain = $brand['domain'] ?? $host;
    $base = $request->getScheme() . '://' . $domain;

    $totals = cache()->remember('llms_txt_totals_v1', now()->addHours(24), fn () => [
        'unis' => \App\Models\University::where('is_active', 1)->count(),
        'programs' => \App\Models\Program::where('is_active', 1)->count(),
        'programs_en' => \App\Models\Program::where('is_active', 1)->whereIn('language', ['en', 'both'])->count(),
        'cities' => \App\Models\City::where('is_active', 1)->count(),
        'fields' => \App\Models\FieldOfStudy::where('is_active', 1)->count(),
        'faqs' => \App\Models\Faq::where('has_answer', 1)->count(),
        'scholarships' => \App\Models\Scholarship::whereNull('removed_at')->count(),
    ]);

    $content = <<<MD
# {$name}

> {$name} is a comprehensive guide for international students applying to German universities. We provide data on {$totals['unis']} active universities, {$totals['programs']} study programs ({$totals['programs_en']} taught in English), {$totals['cities']} student cities, scholarships, visa procedures, and cost-of-living for studying in Germany.

## About

- [About Us]({$base}/about): Mission, team, and editorial standards
- [How to Apply]({$base}/blog): Guides for applying to German universities
- [FAQ]({$base}/faqs): {$totals['faqs']} answered questions from real applicants

## Universities

- [All Universities]({$base}/universities): Browse {$totals['unis']} active German universities
- [Compare]({$base}/compare): Side-by-side comparison of 2-4 universities
- [Rankings]({$base}/rankings): 36 rankings (QS, THE, by field, community favorites)

## Programs & Study Options

- [All Programs]({$base}/programs): {$totals['programs']} programs (Bachelor, Master, PhD)
- [English-Taught Programs]({$base}/programs?language=en): {$totals['programs_en']} programs in English
- [Fields of Study]({$base}/fields): {$totals['fields']} academic fields

## Cities & Living

- [Student Cities]({$base}/cities): {$totals['cities']} cities with cost-of-living data
- [Cost of Living]({$base}/tools/cost-of-living): Monthly expense calculator (DAAD official data)
- [Housing]({$base}/housing): Student accommodation providers and tips

## Tools & Calculators

- [Application Calendar]({$base}/tools/deadlines): Upcoming deadlines for 7,000+ programs
- [Visa Cost Calculator]({$base}/tools/visa-cost): Total cost of the German student visa
- [Blocked Account Finder]({$base}/tools/sperrkonto): Compare Sperrkonto providers
- [Grade Converter]({$base}/tools/grade-converter): Convert to German 1-5 grade system
- [Eligibility Checker]({$base}/tools/eligibility-checker): Diploma recognition (Anabin-based)
- [Studienkolleg Finder]({$base}/tools/studienkolleg): Foundation year programs
- [University Match Quiz]({$base}/tools/recommendation): Find universities matching your profile

## Scholarships

- [Scholarships]({$base}/scholarships): {$totals['scholarships']} active scholarships
- [DAAD Guide]({$base}/scholarships/daad): German Academic Exchange Service grants

## Glossary

- [Germany Education Glossary]({$base}/sozluk): Key terms (APS, uni-assist, Sperrkonto, Studienkolleg, Blue Card, DAAD, Anabin, ECTS)

## Reference

- [Sitemap]({$base}/sitemap.xml): Full site index (split: content, landings, glossary)
- [Robots]({$base}/robots.txt): Crawl rules

## Editorial Notes

- Languages: Turkish (primary), English, German
- Update frequency: Daily for programs, weekly for universities, on event for FAQs
- Sources: DAAD official data, Wikidata, university partner API, official Bundesländer education data
- Authority: 10+ years education consulting experience for students applying to Germany
MD;

    return response($content, 200)->header('Content-Type', 'text/markdown; charset=utf-8');
});

// Blog yardımcı oldu mu? oyu (Alpine widget'tan POST)
Route::post('/api/blog-feedback', [\App\Http\Controllers\Web\BlogController::class, 'feedback'])
    ->middleware('throttle:30,1')
    ->name('blog.feedback');

Route::post('/api/blog-engagement', [\App\Http\Controllers\Web\BlogController::class, 'engagement'])
    ->middleware('throttle:120,1')
    ->name('blog.engagement');

// Dinamik og:image — /og/{type}/{slug}.png (cache: storage/app/public/og/)
Route::get('/og/{type}/{slug}', [\App\Http\Controllers\Web\OgImageController::class, 'show'])
    ->where('type', 'university|program|city|field|profession|post|scholarship')
    ->where('slug', '[a-z0-9\-]+\.png')
    ->name('og.image');
Route::get('/rss.xml', [\App\Http\Controllers\Web\FeedController::class, 'rss'])->name('feed.rss');
Route::get('/feed', fn () => redirect('/rss.xml', 301));
Route::get('/api/map/universities', [MapController::class, 'universitiesJson'])->name('map.universities.json');

// ─────────── Çok-dilli routing ───────────
// Default dil (config('locale.default')) prefix'siz (root); diğer diller /tr, /de, /fr prefix'li.
// Tek route name seti ("programs.index"); locale prefix'i SetLocale middleware'inde
// URL::defaults(['locale' => ...]) ile otomatik enjekte edilir → mevcut route() çağrıları
// olduğu gibi locale-aware çalışır (485 çağrıyı elle değiştirmeye gerek yok).
// Tüm diller prefix'li: /en (default), /tr, /de, /fr. Tek route name seti;
// locale prefix'i SetLocale'de URL::defaults(['locale'=>...]) ile otomatik enjekte edilir.
$__allLocales = implode('|', array_keys(config('locale.locales', ['en' => []])));

Route::prefix('{locale}')
    ->where(['locale' => $__allLocales ?: 'en'])
    ->middleware('set.locale')
    ->group($routes);

// Bare root (applytogerman.com) → tercih edilen dile (default hazırsa o, değilse ilk aktif) 302
Route::get('/', function () {
    $default = config('locale.default', 'en');
    $cfg = config("locale.locales.$default", []);
    $target = (! empty($cfg['active']) && empty($cfg['coming_soon']))
        ? $default
        : collect(config('locale.locales', []))
            ->filter(fn ($c) => ! empty($c['active']) && empty($c['coming_soon']))
            ->keys()->first();
    return redirect('/' . ($target ?? 'tr'), 302);
});

// Locale prefix'siz eski/dış linkler (/universities) → ilk aktif dile yönlendir (loop-safe)
Route::fallback(function (\Illuminate\Http\Request $request) {
    $path  = trim($request->path(), '/');
    $first = explode('/', $path)[0] ?? '';
    if (in_array($first, array_keys(config('locale.locales', [])), true)) {
        // Locale prefix'li ama route bulunamadı → 404. Locale'i set et ki 404 sayfası doğru dilde render olsun
        \Illuminate\Support\Facades\App::setLocale($first);
        abort(404);
    }
    $active = collect(config('locale.locales', []))
        ->filter(fn ($c) => ! empty($c['active']) && empty($c['coming_soon']))
        ->keys()->first() ?? 'tr';
    $qs = $request->getQueryString();
    return redirect('/' . $active . ($path ? '/' . $path : '') . ($qs ? '?' . $qs : ''), 302);
});

// ─────────── Auth-protected, locale-bağımsız ───────────

Route::middleware('auth')->group(function () {
    // Dashboard — auth sonrası landing (Auth controller'ları buraya yönlendiriyor)
    Route::get('/dashboard', function () {
        return redirect()->route('profile.edit');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/favorites/toggle', [\App\Http\Controllers\Web\FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::patch('/profile/favorites/{id}/note', [\App\Http\Controllers\Web\FavoriteController::class, 'updateNote'])->name('favorites.note');
});

// Journey routes locale grubunun içinden çağrılır (routes/web.php $routes group'unda eklendi).
// Burada sadece backward-compat /journey → /tr/journey redirect (locale prefix'siz eski linkler için).
Route::redirect('/journey', '/tr/journey', 302);

// ─────────── Locale switch (session güncellemesi) ───────────

Route::get('/locale/{locale}', function ($locale) {
    $supported = array_keys(array_filter(config('locale.locales'), fn ($c) => ! empty($c['active'])));
    if (! in_array($locale, $supported, true)) abort(404);

    session(['locale' => $locale]);
    cookie()->queue(cookie()->forever('locale', $locale));

    return back()->withCookie(cookie()->forever('locale', $locale));
})->name('locale.switch');

// ─────────── Breeze auth ───────────

require __DIR__ . '/auth.php';

// ─────────── Eski TR slug → yeni evrensel slug (301) ───────────

Route::redirect('/arama', '/search', 301);
Route::redirect('/biz-kimiz', '/about', 301);
Route::redirect('/blog/kategori/{slug}', '/blog/category/{slug}', 301);
Route::redirect('/sss', '/faq', 301);
Route::redirect('/sss/{topic}', '/faq/{topic}', 301);
Route::redirect('/sss/{topic}/{slug}', '/faq/{topic}/{slug}', 301);
Route::redirect('/karsilastir', '/compare', 301);
Route::redirect('/karsilastir/sonuc', '/compare/result', 301);
Route::redirect('/siralama', '/rankings', 301);
Route::redirect('/siralama/{slug}', '/rankings/{slug}', 301);
Route::redirect('/programlar', '/programs', 301);
Route::redirect('/programlar/{slug}', '/programs/{slug}', 301);
Route::redirect('/meslekler', '/professions', 301);
Route::redirect('/meslekler/{slug}', '/professions/{slug}', 301);
Route::redirect('/harita', '/map', 301);
Route::redirect('/araclar', '/tools', 301);
Route::redirect('/araclar/yasam-maliyeti', '/tools/cost-of-living', 301);
Route::redirect('/araclar/not-donusturucu', '/tools/grade-converter', 301);
Route::redirect('/araclar/uni-onerisi', '/tools/recommendation', 301);

// Public API dokümantasyonu (Scalar UI). YAML spec'i public/api/openapi.yaml.
Route::view('/api/docs', 'api.docs')->name('api.docs');
Route::redirect('/developers', '/api/docs', 301);
Route::redirect('/api', '/api/docs', 301);

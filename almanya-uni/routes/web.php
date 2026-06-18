<?php

use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\CityController;
use App\Http\Controllers\Web\CompareController;
use App\Http\Controllers\Web\FaqController;
use App\Http\Controllers\Web\FieldController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\HousingController;
use App\Http\Controllers\Web\JobController;
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

    // Linkable-asset: alıntılanabilir Almanya eğitim istatistikleri (backlink mıknatısı)
    Route::get('/germany-study-statistics', [\App\Http\Controllers\Web\StatisticsController::class, 'index'])->name('stats');

    // Profesyonel başvuru belgesi şablonları (premium içerik — Lebenslauf, Motivationsschreiben…)
    Route::get('/templates', [\App\Http\Controllers\Web\DocumentTemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/{slug}', [\App\Http\Controllers\Web\DocumentTemplateController::class, 'show'])->name('templates.show');

    Route::get('/search', [SearchController::class, 'index'])
        ->middleware('throttle:30,1')
        ->name('search.index');
    Route::get('/search/suggest', \App\Http\Controllers\Web\SearchSuggestController::class)
        ->middleware('throttle:60,1')
        ->name('search.suggest');
    Route::get('/about', [AboutController::class, 'index'])->name('about');
    Route::get('/link-to-us', [AboutController::class, 'linkToUs'])->name('link-to-us');
    Route::get('/team', [AboutController::class, 'team'])->name('team');
    Route::get('/advisory-board', [AboutController::class, 'advisoryBoard'])->name('advisory-board');
    Route::redirect('/danisma-kurulu', '/advisory-board', 301);
    Route::redirect('/ekip', '/team', 301);
    Route::get('/author/{slug}', [AboutController::class, 'author'])->name('author.show');
    Route::redirect('/yazar/{slug}', '/author/{slug}', 301);
    Route::get('/study-in-germany', [\App\Http\Controllers\Web\AboutGermanyController::class, 'index'])->name('study.germany');
    Route::redirect('/almanyada-egitim', '/study-in-germany', 301);
    Route::redirect('/about-germany', '/study-in-germany', 301);
    Route::redirect('/neden-almanya', '/study-in-germany', 301);
    Route::redirect('/yazarlar', '/team', 301);
    Route::post('/feedback', [\App\Http\Controllers\Web\FeedbackController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('feedback.store');

    // Popup tracking — view/click/dismiss counter endpoints
    Route::post('/popups/{popup}/track/{event}', function (\App\Models\Popup $popup, string $event) {
        if (! in_array($event, ['view', 'click', 'dismiss'], true)) abort(400);
        $popup->increment($event . '_count');
        return response()->json(['ok' => true]);
    })->where('event', 'view|click|dismiss')->middleware('throttle:60,1')->name('popups.track');

    // Newsletter — double opt-in akış
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
        ->middleware('throttle:10,1')
        ->name('newsletter.subscribe');
    Route::get('/newsletter/confirm/{token}', [NewsletterController::class, 'confirm'])
        ->name('newsletter.confirm');
    Route::match(['get', 'post'], '/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])
        ->name('newsletter.unsubscribe');

    // Legal pages — KVKK + GDPR + TMG (DB-driven, admin-editable via Filament)
    Route::get('/privacy',       [LegalController::class, 'privacy'])->name('legal.privacy');
    Route::get('/terms',         [LegalController::class, 'terms'])->name('legal.terms');
    Route::get('/cookie-policy', [LegalController::class, 'cookies'])->name('legal.cookies');
    Route::get('/impressum',     [LegalController::class, 'impressum'])->name('legal.impressum'); // Almanca yasal terim — korunur
    Route::get('/disclaimer',    [LegalController::class, 'disclaimer'])->name('legal.disclaimer');
    Route::redirect('/gizlilik', '/privacy', 301);
    Route::redirect('/kosullar', '/terms', 301);
    Route::redirect('/cerez-politikasi', '/cookie-policy', 301);
    Route::redirect('/yasal-uyari', '/disclaimer', 301);

    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
    Route::post('/blog/{slug}/comment', [BlogController::class, 'storeComment'])
        ->middleware('throttle:5,10')
        ->name('blog.comment.store');

    // Haber akışı — "Almanya'dan" (type='news' Post'lar)
    Route::get('/news', [\App\Http\Controllers\Web\NewsController::class, 'index'])->name('news.index');
    Route::get('/news/category/{slug}', [\App\Http\Controllers\Web\NewsController::class, 'category'])->name('news.category');
    Route::get('/news/{slug}', [\App\Http\Controllers\Web\NewsController::class, 'show'])->name('news.show');
    // Eski Türkçe path'ler → İngilizce canonical (SEO/var olan linkler için 301)
    Route::redirect('/haberler/kategori/{slug}', '/news/category/{slug}', 301);
    Route::redirect('/haberler/{slug}', '/news/{slug}', 301);
    Route::redirect('/haberler', '/news', 301);

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
    Route::get('/rankings/{slug}/methodology', [RankingController::class, 'methodology'])->name('rankings.methodology');

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
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{slug}', [JobController::class, 'show'])->name('jobs.show');

    Route::get('/scholarships', [ScholarshipController::class, 'index'])->name('scholarships.index');
    Route::get('/scholarships/guide', [ScholarshipController::class, 'staticIndex'])->name('scholarships.guide');
    Route::get('/scholarships/daad', [ScholarshipController::class, 'daad'])->name('scholarships.daad');
    Route::get('/scholarships/{slug}', [ScholarshipController::class, 'show'])
        ->where('slug', '[a-z0-9\-]+')
        ->name('scholarships.show');

    Route::get('/professions', [ProfessionController::class, 'index'])->name('professions.index');
    Route::get('/professions/{slug}', [ProfessionController::class, 'show'])->name('professions.show');

    Route::get('/map', [MapController::class, 'index'])->name('map.index');

    Route::get('/student-rent-map', function () {
        $cities = \App\Models\City::query()
            ->whereNotNull('student_rent_warm30')
            ->whereNotNull('latitude')->whereNotNull('longitude')
            ->orderByDesc('student_rent_warm30')
            ->get(['name_de', 'name_tr', 'name_en', 'slug', 'latitude', 'longitude',
                'student_rent_warm30', 'student_rent_kalt30', 'student_rent_wg_warm', 'student_rent_wg_kalt',
                'student_rent_index', 'student_rent_index_3yr', 'student_rent_source', 'student_rent_year']);
        return view('map.rents', ['cities' => $cities]);
    })->name('map.rents');

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
        // Almanya öğrenci vizesi randevu rehberi (iData, Türkiye) — TR çekirdek spearhead
        Route::get('/visa-appointment', [ToolsController::class, 'visaAppointment'])->name('visa-appointment');

        // Almanca dil sertifikaları karşılaştırma rehberi (TestDaF/DSH/telc/Goethe) — TR çekirdek #2
        Route::get('/language-certificates', [ToolsController::class, 'languageCertificates'])->name('language-certificates');

        Route::get('/sperrkonto', [\App\Http\Controllers\Web\BlockedAccountController::class, 'index'])->name('blocked-account');
        Route::get('/sperrkonto/country/{country}', [\App\Http\Controllers\Web\BlockedAccountController::class, 'country'])->name('blocked-account.country');
        Route::get('/sperrkonto/{slug}', [\App\Http\Controllers\Web\BlockedAccountController::class, 'show'])->name('blocked-account.show');

        // Sağlık Sigortası Karşılaştırma (GKV / PKV / Expat) — fintech bundle boşluğu
        Route::get('/health-insurance', [\App\Http\Controllers\Web\HealthInsuranceController::class, 'index'])->name('health-insurance');
        Route::get('/health-insurance/{slug}', [\App\Http\Controllers\Web\HealthInsuranceController::class, 'show'])->name('health-insurance.show');

        // Professional Recognition (Mesleki Denklik) — Euroversity insight
        Route::match(['get', 'post'], '/professional-recognition', [ToolsController::class, 'professionalRecognition'])
            ->name('professional-recognition');

        // Pathway Finder — deutschland.de style quiz: 5 questions → Studienkolleg/Bachelor/Master/PhD/Ausbildung/Sprachkurs
        Route::match(['get', 'post'], '/pathway-finder', [ToolsController::class, 'pathwayFinder'])
            ->name('pathway-finder');

        // Inspire Me — MyGuide style random discovery (uni / city / programme / scholarship / profession / field)
        Route::get('/inspire-me', [ToolsController::class, 'inspireMe'])->name('inspire-me');
    });
    Route::redirect('/araclar/kariyer-pusulasi', '/tools/career-compass', 301);
    Route::redirect('/tools/bloke-hesap', '/tools/sperrkonto', 301);
    Route::redirect('/araclar/sperrkonto', '/tools/sperrkonto', 301);
    Route::redirect('/tools/vize-randevu', '/tools/visa-appointment', 301);
    Route::redirect('/araclar/vize-randevu', '/tools/visa-appointment', 301);
    Route::redirect('/tools/dil-sertifikalari', '/tools/language-certificates', 301);
    Route::redirect('/tools/saglik-sigortasi', '/tools/health-insurance', 301);
    Route::redirect('/araclar/saglik-sigortasi', '/tools/health-insurance', 301);

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
    // Küratörlü kategori sayfaları (İngilizce eğitim, en çok tercih edilen devlet, şartlı kabul).
    // Uni slug catch-all'dan ÖNCE — "collections" literal ilk segment, çakışma yok.
    Route::get('/universities/collections/{slug}', [UniversityWebController::class, 'collection'])
        ->name('universities.collection');
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

    // Partner dizinleri (Dil Kursları + Yeminli Tercüme) — herkese açık + lead-gen
    Route::get('/language-courses', [\App\Http\Controllers\Web\PartnerController::class, 'languageCourses'])->name('language-courses.index');
    Route::get('/language-courses/{slug}', [\App\Http\Controllers\Web\PartnerController::class, 'languageCourse'])->name('language-courses.show');
    Route::get('/translation-offices', [\App\Http\Controllers\Web\PartnerController::class, 'translationOffices'])->name('translation-offices.index');
    Route::get('/translation-offices/{slug}', [\App\Http\Controllers\Web\PartnerController::class, 'translationOffice'])->name('translation-offices.show');
    Route::post('/partner-lead', [\App\Http\Controllers\Web\PartnerController::class, 'storeLead'])->name('partner-lead.store');
    Route::get('/go/{kind}/{id}', [\App\Http\Controllers\Web\PartnerController::class, 'click'])->name('partner.click')->whereNumber('id');

    // Events / Etkinlikler
    Route::get('/events', [\App\Http\Controllers\Web\EventController::class, 'index'])->name('events.index');
    // Şehir bazlı etkinlik bildirimi aboneliği (uni slug catch-all'dan ÖNCE — "alerts" literal).
    Route::post('/events/alerts/subscribe', [\App\Http\Controllers\Web\EventAlertController::class, 'subscribe'])
        ->middleware('throttle:8,10')
        ->name('events.alerts.subscribe');
    Route::get('/events/alerts/confirm/{token}', [\App\Http\Controllers\Web\EventAlertController::class, 'confirm'])
        ->name('events.alerts.confirm');
    Route::match(['get', 'post'], '/events/alerts/unsubscribe/{token}', [\App\Http\Controllers\Web\EventAlertController::class, 'unsubscribe'])
        ->name('events.alerts.unsubscribe');
    // Web push aboneliği (tarayıcı) — şehir bazlı etkinlik bildirimi
    Route::post('/events/alerts/push/subscribe', [\App\Http\Controllers\Web\PushSubscriptionController::class, 'subscribe'])
        ->middleware('throttle:15,1')
        ->name('events.alerts.push.subscribe');
    Route::post('/events/alerts/push/unsubscribe', [\App\Http\Controllers\Web\PushSubscriptionController::class, 'unsubscribe'])
        ->middleware('throttle:15,1')
        ->name('events.alerts.push.unsubscribe');
    Route::get('/events/{slug}', [\App\Http\Controllers\Web\EventController::class, 'show'])->name('events.show');
    Route::post('/events/{slug}/rsvp', [\App\Http\Controllers\Web\EventController::class, 'rsvp'])
        ->middleware('throttle:10,10')
        ->name('events.rsvp');
    Route::post('/events/{slug}/review', [\App\Http\Controllers\Web\EventController::class, 'review'])
        ->middleware('throttle:5,10')
        ->name('events.review');
    Route::redirect('/etkinlikler', '/events', 301);

    // Mentors / Mentorlar
    Route::get('/mentors', [\App\Http\Controllers\Web\MentorController::class, 'index'])->name('mentors.index');
    Route::get('/mentors/{slug}', [\App\Http\Controllers\Web\MentorController::class, 'show'])->name('mentors.show');
    Route::post('/mentors/{slug}/book', [\App\Http\Controllers\Web\MentorController::class, 'book'])
        ->middleware(['auth', 'throttle:5,60'])
        ->name('mentors.book');
    Route::redirect('/mentorlar', '/mentors', 301);

    // City listesi + detay (locale grubu içinde → /tr/cities, /en/cities ...)
    Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
    Route::get('/cities/{slug}', [CityController::class, 'show'])->name('cities.show');

    Route::middleware('auth')->group(function () {
        Route::get('/housing/tips/new', [HousingController::class, 'createTip'])->name('housing.tip-create');
        Route::post('/housing/tips', [HousingController::class, 'storeTip'])->name('housing.tip-store');

        // Topluluk katkısı (deneyim/ipucu paylaşımı)
        Route::get('/contribute', [\App\Http\Controllers\Web\ContributionController::class, 'create'])->name('contribute');
        Route::post('/contribute', [\App\Http\Controllers\Web\ContributionController::class, 'store'])->middleware('throttle:5,1')->name('contribute.store');
    });
    Route::redirect('/deneyim-paylas', '/contribute', 301);

    // İletişim / gönüllü formu — PUBLIC (auth grubu DIŞINDA; ziyaretçi de yazabilir)
    Route::get('/contact', [\App\Http\Controllers\Web\ContactController::class, 'create'])->name('contact');
    Route::post('/contact', [\App\Http\Controllers\Web\ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
    Route::redirect('/iletisim', '/contact', 301);

    // Application Tracker — locale grubu içinde (mega menü doğru locale alabilsin)
    Route::get('/journey',                       [\App\Http\Controllers\Web\ApplicationTrackerController::class, 'show'])->name('journey.show');
    Route::post('/journey/step/{step}/toggle',   [\App\Http\Controllers\Web\ApplicationTrackerController::class, 'toggle'])->name('journey.step.toggle');
    Route::patch('/journey/step/{step}',         [\App\Http\Controllers\Web\ApplicationTrackerController::class, 'updateStep'])->name('journey.step.update');
    Route::patch('/journey/update',              [\App\Http\Controllers\Web\ApplicationTrackerController::class, 'update'])->name('journey.update');

    // Premium pricing
    Route::get('/pricing',  [\App\Http\Controllers\Web\PricingController::class, 'index'])->name('pricing');
    Route::post('/pricing/interest', [\App\Http\Controllers\Web\PricingController::class, 'express'])->middleware('throttle:5,1')->name('pricing.interest');
};

// ─────────── Sitemap & API (locale'siz, tek versiyon) ───────────

// Brand-aware PWA manifest — name/short_name/lang/icons change per domain & locale.
// İKİ yola da kayıtlı: /site.webmanifest (head bunu kullanır) + /manifest.json.
// Not: prod'da eski statik public/manifest.json route'u gölgeleyebildiği için head
// /site.webmanifest'e bakar (statik dosya yok → route garantili çalışır).
$pwaManifest = function (\Illuminate\Http\Request $request) {
    $host = strtolower(preg_replace('/^www\./', '', $request->getHost()));
    $domains = config('brand.domains', []);
    $brandKey = $domains[$host] ?? config('brand.fallback', 'almanyauni');
    $brands = config('brand.brands', []);
    $b = $brands[$brandKey] ?? [];

    $name = $b['name'] ?? 'AlmanyaUni';
    $shortName = $name;
    $description = $b['tagline'] ?? __('University guide for Germany');
    $themeColor = $b['theme_color'] ?? '#1A1A1A';
    // PWA app ikonu KARE olmalı — geniş wordmark lockup değil, onyx kare ikon (favicon.svg).
    $appIcon = '/img/favicon.svg';

    return response()->json([
        'name' => $name,
        'short_name' => $shortName,
        'description' => is_array($description) ? ($description[app()->getLocale()] ?? reset($description)) : $description,
        'start_url' => '/',
        'scope' => '/',
        'display' => 'standalone',
        'orientation' => 'portrait',
        'background_color' => '#ffffff',
        'theme_color' => $themeColor,
        'lang' => app()->getLocale() . '-' . strtoupper(app()->getLocale()),
        'dir' => 'ltr',
        'categories' => ['education', 'lifestyle', 'reference'],
        'icons' => [
            ['src' => $appIcon, 'sizes' => 'any', 'type' => 'image/svg+xml', 'purpose' => 'any'],
            ['src' => '/img/icons/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ['src' => '/img/icons/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
        ],
        'shortcuts' => [
            ['name' => __('Universities'), 'url' => '/' . app()->getLocale() . '/universities'],
            ['name' => __('Cities'), 'url' => '/' . app()->getLocale() . '/cities'],
            ['name' => __('Tools'), 'url' => '/' . app()->getLocale() . '/tools'],
            ['name' => __('Scholarships'), 'url' => '/' . app()->getLocale() . '/scholarships'],
        ],
    ], 200, ['Content-Type' => 'application/manifest+json; charset=utf-8']);
};
Route::get('/site.webmanifest', $pwaManifest)->name('pwa.manifest');
Route::get('/manifest.json', $pwaManifest);

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-content.xml', [SitemapController::class, 'content'])->name('sitemap.content');
Route::get('/sitemap-landings.xml', [SitemapController::class, 'landings'])->name('sitemap.landings');
Route::get('/sitemap-glossary.xml', [SitemapController::class, 'glossary'])->name('sitemap.glossary');

// Token-gated image cache trigger (KAS has no SSH/cron — fire via curl after deploy)
//   curl "https://applytogerman.com/_system/cache-hot-images?token=XXX&limit=20"
Route::get('/_system/cache-hot-images', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }
    // Long-running; raise PHP limits (image-fetch throttling can push past 10 min)
    @set_time_limit(1500);
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

// Token-gated storytelling generation — KAS has no SSH/cron, fire via curl after deploy
//   curl "https://applytogerman.com/_system/storytelling?token=XXX"
Route::get('/_system/storytelling', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }
    @set_time_limit(600);
    @ini_set('memory_limit', '512M');

    $exit = \Illuminate\Support\Facades\Artisan::call('storytelling:infographics', array_filter([
        '--force' => $request->boolean('force'),
    ], fn ($v) => $v !== false));

    return response()->json([
        'exit' => $exit,
        'output' => \Illuminate\Support\Facades\Artisan::output(),
    ]);
})->middleware('throttle:10,1');

// Token-gated migration runner — KAS has no CLI access, run via curl after deploy
Route::get('/_system/migrate', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }
    @set_time_limit(300);

    $errors = [];
    $migrate = null;
    $seed = null;

    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrate = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        $errors['migrate'] = $e->getMessage();
        $migrate = \Illuminate\Support\Facades\Artisan::output() ?: 'no output';
    }

    $seedClass = $request->query('seed');
    if ($seedClass && preg_match('/^[A-Za-z0-9_]+Seeder$/', $seedClass)) {
        try {
            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\' . $seedClass,
                '--force' => true,
            ]);
            $seed = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $e) {
            $errors['seed'] = $e->getMessage();
            $seed = \Illuminate\Support\Facades\Artisan::output() ?: 'no output';
        }
    }

    // Cache temizliği — yeni migration/seed sonrası eski view + config snapshot'ları geçersiz
    $cache = [];
    foreach (['view:clear', 'cache:clear', 'config:clear', 'route:clear'] as $cmd) {
        try {
            \Illuminate\Support\Facades\Artisan::call($cmd);
            $cache[$cmd] = trim(\Illuminate\Support\Facades\Artisan::output());
        } catch (\Throwable $e) {
            $cache[$cmd] = 'ERROR: ' . $e->getMessage();
        }
    }

    // PHP OPCache reset — without this, edited Model/Controller files keep
    // running their previous bytecode in long-lived PHP-FPM workers and
    // changes won't appear until natural revalidation (5+ minutes on KAS).
    $opcache = [];
    if (function_exists('opcache_get_status')) {
        $opcache['enabled_before'] = (bool) (opcache_get_status(false)['opcache_enabled'] ?? false);
        $opcache['reset'] = function_exists('opcache_reset') ? (bool) opcache_reset() : false;
    } else {
        $opcache['enabled_before'] = false;
        $opcache['reset'] = false;
    }

    return response()->json([
        'migrate' => $migrate,
        'seed'    => $seed,
        'cache'   => $cache,
        'opcache' => $opcache,
        'errors'  => $errors,
    ]);
})->middleware('throttle:5,1');

// Token-gated Ticketmaster etkinlik importu — günlük cron (04:30) var, bu elle/ilk-doldurma.
// KAS'ta CLI yok → curl ile tetiklenir. Tek şehir hızlıdır (HTTP timeout riski yok).
//   Tek şehir: curl "https://applytogerman.com/_system/import-events?token=XXX&city=Berlin"
//   Tüm şehirler: ...&all=1  (yavaş — timeout olursa cron tamamlar)
Route::get('/_system/import-events', function (\Illuminate\Http\Request $request) {
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $request->query('token'))) {
        abort(403, 'Invalid token');
    }
    @set_time_limit(280);

    $params = [];
    if ($request->boolean('all')) {
        // tüm varsayılan şehirler
    } else {
        $params['--city'] = [$request->query('city', 'Berlin')];
    }
    if ($size = $request->query('size')) {
        $params['--size'] = (int) $size;
    }

    try {
        $exit = \Illuminate\Support\Facades\Artisan::call('events:import-ticketmaster', $params);

        return response()->json([
            'exit'   => $exit,
            'output' => \Illuminate\Support\Facades\Artisan::output(),
        ]);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->middleware('throttle:5,1');

// Token-gated içerik denetimi — prod veri hatalarını raporlar (read-only). KAS'ta CLI yok.
//   curl "https://applytogerman.com/_system/content-audit?token=XXX&samples=5&only=deadlines"
Route::get('/_system/content-audit', function (\Illuminate\Http\Request $request) {
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $request->query('token'))) {
        abort(403, 'Invalid token');
    }
    @set_time_limit(280);
    $params = ['--samples' => (int) $request->query('samples', 5)];
    if ($only = $request->query('only')) {
        $params['--only'] = $only;
    }
    \Illuminate\Support\Facades\Artisan::call('content:audit', $params);
    return response(\Illuminate\Support\Facades\Artisan::output(), 200, ['Content-Type' => 'text/plain; charset=utf-8']);
})->middleware('throttle:10,1');

// Token-gated güvenli veri düzeltmeleri — DRY-RUN varsayılan; apply=1 ile yazar.
// (Kullanıcı onaylı; mevcut _system/migrate ile aynı kalıp. Prod'da CLI yok → curl ile.)
//   Rapor: curl "https://applytogerman.com/_system/fix-content?token=XXX&job=deadlines"
//   Uygula: ...&job=deadlines&apply=1   (job=deadlines|data)
Route::get('/_system/fix-content', function (\Illuminate\Http\Request $request) {
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $request->query('token'))) {
        abort(403, 'Invalid token');
    }
    @set_time_limit(280);
    $command = match ($request->query('job')) {
        'deadlines'    => 'programs:fix-deadlines',
        'data'         => 'programs:fix-data',
        'dedupe'       => 'programs:dedupe',
        'cities'       => 'universities:create-missing-cities', // Wikidata P131 — tek seferlik backfill
        'daad-details' => 'daad:enrich-details',                // DAAD detay gereklilik — batch (limit ile)
        default        => null,
    };
    if (! $command) {
        return response()->json(['error' => 'job must be deadlines|data|dedupe|cities|daad-details'], 400);
    }
    $params = $request->boolean('apply') ? ['--apply' => true] : [];
    // daad-details yavaş (HTTP fetch) — endpoint timeout'una sığması için batch limitli çağrılır.
    if ($request->query('job') === 'daad-details') {
        $params['--limit'] = (int) ($request->query('limit') ?: 150);
        $params['--sleep'] = 150;
    }
    try {
        $exit = \Illuminate\Support\Facades\Artisan::call($command, $params);
        return response("exit={$exit}\n\n" . \Illuminate\Support\Facades\Artisan::output(), 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->middleware('throttle:5,1');


// Token-gated log kuyruğu — prod hatalarını teşhis için son N satır (CLI yok, curl ile).
//   curl "https://applytogerman.com/_system/log-tail?token=XXX&lines=80&grep=Aachen"
Route::get('/_system/log-tail', function (\Illuminate\Http\Request $request) {
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $request->query('token'))) {
        abort(403, 'Invalid token');
    }
    $file = storage_path('logs/laravel.log');
    if (! is_file($file)) {
        return response()->json(['error' => 'no log file']);
    }
    $lines = max(10, min(400, (int) $request->query('lines', 120)));
    $content = @file($file, FILE_IGNORE_NEW_LINES) ?: [];
    $tail = array_slice($content, -1500); // son ~1500 satırda ara
    if ($g = $request->query('grep')) {
        $tail = array_values(array_filter($tail, fn ($l) => stripos($l, $g) !== false));
    }
    $tail = array_slice($tail, -$lines);

    return response(implode("\n", $tail), 200, ['Content-Type' => 'text/plain; charset=utf-8']);
})->middleware('throttle:10,1');

// Token-gated etkinlik bildirim digest'i elle tetik (haftalık cron Perşembe var; bu anlık test).
//   Kuru: curl "https://applytogerman.com/_system/notify-subscribers?token=XXX&dry=1"
//   Gerçek: ...&token=XXX   (email + web push gönderir)
//   Tek abone: ...&email=foo@bar.com
Route::get('/_system/notify-subscribers', function (\Illuminate\Http\Request $request) {
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $request->query('token'))) {
        abort(403, 'Invalid token');
    }
    @set_time_limit(280);

    $params = [];
    if ($request->boolean('dry')) {
        $params['--dry'] = true;
    }
    if ($email = $request->query('email')) {
        $params['--email'] = $email;
    }

    try {
        $exit = \Illuminate\Support\Facades\Artisan::call('events:notify-subscribers', $params);

        return response()->json([
            'exit'   => $exit,
            'output' => \Illuminate\Support\Facades\Artisan::output(),
        ]);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->middleware('throttle:5,1');

// Token-gated blog iç-link/resim düzeltme — KAS'ta CLI yok, curl ile çalıştırılır.
// Varsayılan dry-run (sadece tabela + rapor); &apply=1 ile uygular.
//   Dry-run:  curl "https://applytogerman.com/_system/fix-blog-links?token=XXX"
//   Uygula:   curl "https://applytogerman.com/_system/fix-blog-links?token=XXX&apply=1"
Route::get('/_system/fix-blog-links', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }
    @set_time_limit(300);

    $args = $request->boolean('apply') ? ['--apply' => true] : [];
    try {
        \Illuminate\Support\Facades\Artisan::call('content:fix-blog-links', $args);
        $out = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage(), 'output' => \Illuminate\Support\Facades\Artisan::output()], 500);
    }

    // Uygulandıysa view/opcache temizle (içerik güncel görünsün)
    if ($request->boolean('apply')) {
        foreach (['view:clear', 'cache:clear'] as $cmd) {
            try { \Illuminate\Support\Facades\Artisan::call($cmd); } catch (\Throwable $e) {}
        }
        if (function_exists('opcache_reset')) { @opcache_reset(); }
    }

    return response()->json([
        'mode' => $request->boolean('apply') ? 'applied' : 'dry-run',
        'output' => $out,
        'catalog_file' => 'storage/app/blog-link-catalog.json',
    ]);
})->middleware('throttle:5,1');

// Token-gated content_html backfill — çeviri/legacy importtan boş content_html
// kalan yayında yazıları content_md'den render eder (mutator ile aynı pipeline).
// KAS'ta CLI yok → curl. Varsayılan dry-run; &apply=1 ile uygular + cache temizler.
//   Dry-run:  curl "https://applytogerman.com/_system/render-blog-html?token=XXX"
//   Uygula:   curl "https://applytogerman.com/_system/render-blog-html?token=XXX&apply=1"
Route::get('/_system/render-blog-html', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }
    @set_time_limit(300);

    $args = array_filter([
        '--apply' => $request->boolean('apply') ?: null,
        '--id'    => $request->integer('id') ?: null,
        '--force' => $request->boolean('force') ?: null, // dolu content_html'leri de re-render (i18n temizliği)
    ]);
    try {
        \Illuminate\Support\Facades\Artisan::call('blog:render-html', $args);
        $out = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage(), 'output' => \Illuminate\Support\Facades\Artisan::output()], 500);
    }

    if ($request->boolean('apply')) {
        foreach (['view:clear', 'cache:clear'] as $cmd) {
            try { \Illuminate\Support\Facades\Artisan::call($cmd); } catch (\Throwable $e) {}
        }
        if (function_exists('opcache_reset')) { @opcache_reset(); }
    }

    return response()->json([
        'mode' => $request->boolean('apply') ? 'applied' : 'dry-run',
        'output' => $out,
    ]);
})->middleware('throttle:5,1');

// Token-gated meslek (BERUFENET) çeviri backfill — KAS Cronjob bunu curl ile
// oturumsuz çağırır (admin /admin/ops/... ise oturum ister). Zaman bütçesiyle
// her çağrı ~max_seconds kadar çalışıp temiz çıkar; "KALAN: X" / "TAMAMLANDI" basar.
//   KAS Cronjob (2 dk'da bir): curl "https://applytogerman.com/_system/professions-translate?token=XXX"
Route::get('/_system/professions-translate', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }
    // PHP'ye gateway'den fazla pay ver: gateway 504 dönse bile FPM çalışmaya
    // devam edip o ana kadarki meslekleri kaydetmeyi sürdürür (her meslek tek tek yazılır).
    @set_time_limit(180);
    try {
        \Illuminate\Support\Facades\Artisan::call('professions:translate-info-fields', array_filter([
            '--limit'       => (int) $request->integer('limit', 0),
            // Cron için yüksek varsayılan (~5× iş/çağrı). Erken gateway timeout zararsız:
            // tamamlanan meslekler kaydedildi, sonraki cron tick'i whereNull ile devam eder.
            '--max-seconds' => (int) $request->integer('max_seconds', 110),
            '--sleep'       => (int) $request->integer('sleep', 0),
            '--lang'        => $request->query('lang'), // tr | en | boş(ikisi)
            '--shards'      => (int) $request->integer('shards', 1),
            '--shard'       => (int) $request->integer('shard', 0),
            '--missing'     => $request->boolean('missing'),
        ], fn ($v) => $v !== false && $v !== null));
        $out = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        $out = 'EXCEPTION: ' . $e->getMessage();
    }
    return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
})->middleware('throttle:30,1');

// Token-gated CITY enrichment (Wikipedia hero/gallery görseli + Gemini AI içerik).
// Boş şehirleri doldurur (yeni oluşturulan Duisburg/Krefeld... + 74 boş şehir) VEYA
// --force ile yanlış görselli şehirleri yeniden çeker (Sankt Augustin/Potsdam portreleri).
// Her şehir ATOMİK yazılır → gateway 504 zararsız (tamamlananlar kaydedilir, tekrar-çağrı
// --only-without ile kaldığı yerden devam eder). Cron'a da konabilir.
//   Batch:  curl ".../_system/enrich-cities?token=XXX&limit=3"            (sıradaki 3 boş şehir)
//   Tek:    curl ".../_system/enrich-cities?token=XXX&slug=duisburg"
//   Düzelt: curl ".../_system/enrich-cities?token=XXX&slug=sankt-augustin&force=1"
Route::get('/_system/enrich-cities', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }
    @set_time_limit(600);
    try {
        $params = array_filter([
            '--limit'        => (int) $request->integer('limit', 3),
            '--only-without' => $request->boolean('only-without', ! $request->filled('slug') && ! $request->boolean('force')),
            '--min-unis'     => (int) $request->integer('min-unis', 1),
            '--slug'         => $request->query('slug'),
            '--force'        => $request->boolean('force'),
            '--sleep'        => (int) $request->integer('sleep', 1),
        ], fn ($v) => $v !== false && $v !== null && $v !== '');
        // Küratörlü kaynaklar (?source[]=url) — slug ile birlikte grounding için.
        if ($srcs = array_filter((array) $request->query('source'))) {
            $params['--source'] = array_values($srcs);
        }
        \Illuminate\Support\Facades\Artisan::call('cities:enrich', $params);
        $out = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        $out = 'EXCEPTION: ' . $e->getMessage();
    }
    return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
})->middleware('throttle:30,1');

// Token-gated content_blocks TR→EN/DE çeviri (KAS cron/CLI yok → elle + Auto Refresh Plus).
// content_blocks DB verisidir (git'le gelmez) → prod'da bu route ile üretilir.
// Komut idempotent + hedefi eksik satırları seçer → küçük limit'le tekrar tekrar çağır,
// "OK: 0, Fail: 0" / "0 kayıt" diyene dek (gateway timeout zararsız: her satır tek tek save'lenir).
//   Şehir EN+DE: ".../_system/translate-blocks?token=XXX&entity=city&limit=2"
//   Tek locale:  ".../_system/translate-blocks?token=XXX&entity=city&locales=en&limit=3"
//   Diğer:       entity=university|field|state
Route::get('/_system/translate-blocks', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }
    @set_time_limit(600);
    try {
        \Illuminate\Support\Facades\Artisan::call('content:translate-blocks', array_filter([
            '--entity'  => $request->query('entity', 'city'),
            '--locales' => $request->query('locales', 'en,de'),
            '--ids'     => $request->query('ids'),
            '--limit'   => (int) $request->integer('limit', 2),
            '--force'   => $request->boolean('force'),
            '--sleep'   => (int) $request->integer('sleep', 1),
        ], fn ($v) => $v !== false && $v !== null && $v !== ''));
        $out = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        $out = 'EXCEPTION: ' . $e->getMessage();
    }
    return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
})->middleware('throttle:30,1');

// Token-gated DAAD burs TR lokalizasyonu (ad + programmname + introduction → native TR).
// /tr'de İngilizce ad/açıklama sızıntısını giderir (kaynak-dili lokalizasyon kuralı).
// İdempotent (name_tr doluysa atlar) → küçük limit'le tekrar çağır, "burs yok" diyene dek.
//   ".../_system/scholarships-localize?token=XXX&limit=10"
Route::get('/_system/scholarships-localize', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }
    @set_time_limit(600);
    try {
        \Illuminate\Support\Facades\Artisan::call('scholarships:localize', array_filter([
            '--limit' => (int) $request->integer('limit', 10),
            '--force' => $request->boolean('force'),
            '--delay' => (int) $request->integer('delay', 250),
        ], fn ($v) => $v !== false && $v !== null && $v !== ''));
        $out = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        $out = 'EXCEPTION: ' . $e->getMessage();
    }
    return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
})->middleware('throttle:30,1');

// Token-gated HAFTALIK DIGEST tetikleyici — KAS'ta schedule:run cron yok.
// Dry-run (önizleme):  curl ".../_system/newsletter-digest?token=XXX"
// Gerçekten gönder:    curl ".../_system/newsletter-digest?token=XXX&send=1"
// Tek adrese test:     curl ".../_system/newsletter-digest?token=XXX&send=1&only=ben@x.com"
Route::get('/_system/newsletter-digest', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }
    @set_time_limit(600);
    try {
        $params = array_filter([
            '--days'     => (int) $request->integer('days', 7),
            '--send'     => $request->boolean('send'),
            '--limit'    => (int) $request->integer('limit', 12),
            '--locale'   => (string) $request->query('locale', ''),
            '--force'    => $request->boolean('force'),
            '--throttle' => (int) $request->integer('throttle', 100),
        ], fn ($v) => $v !== false && $v !== null && $v !== '');
        if ($only = $request->query('only')) {
            $params['--only'] = [$only];
        }
        \Illuminate\Support\Facades\Artisan::call('newsletter:digest', $params);
        $out = \Illuminate\Support\Facades\Artisan::output();
    } catch (\Throwable $e) {
        $out = 'EXCEPTION: ' . $e->getMessage();
    }
    return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
})->middleware('throttle:30,1');

// Token-gated mailer testi — Resend/SMTP gerçekten çalışıyor mu? Abone şartı YOK.
//   curl ".../_system/test-mail?token=XXX&to=ben@gmail.com"
Route::get('/_system/test-mail', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }
    $to = (string) $request->query('to');
    if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return response('?to=gecerli@email gerekli', 400)->header('Content-Type', 'text/plain; charset=utf-8');
    }
    $mailer = config('mail.default');
    try {
        \Illuminate\Support\Facades\Mail::raw(
            "ApplyToGerman test e-postasi — mailer calisiyor.\nMailer: {$mailer}\nZaman: " . now()->toDateTimeString(),
            fn ($m) => $m->to($to)->subject('ApplyToGerman — mailer test')
        );
        $out = "OK · '{$to}' adresine test gonderildi (mailer: {$mailer}). Gelen kutusu + SPAM klasorunu kontrol et.";
    } catch (\Throwable $e) {
        $out = "HATA (mailer: {$mailer}): " . $e->getMessage();
    }
    return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
})->middleware('throttle:10,1');

// Token-gated single-image cache override — for cases where a uni has no
// usable Wikipedia image and we want to point to a custom CDN URL instead.
//   curl ".../_system/cache-custom?token=XXX&type=uni&slug=fom-...&url=https://...&width=600"
Route::get('/_system/cache-custom', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }
    $type = $request->query('type', 'uni');         // uni | city | uni-logo
    $slug = (string) $request->query('slug', '');
    $url = (string) $request->query('url', '');
    $width = (int) $request->query('width', $type === 'uni-logo' ? 120 : 600);

    if (! $slug || ! preg_match('/^[a-z0-9-]+$/', $slug)) {
        return response()->json(['error' => 'Invalid slug'], 400);
    }
    if (! preg_match('#^https?://#i', $url)) {
        return response()->json(['error' => 'Invalid url'], 400);
    }
    if (! in_array($type, ['uni', 'city', 'uni-logo'], true)) {
        return response()->json(['error' => 'Invalid type'], 400);
    }

    $subdir = match ($type) {
        'uni' => 'unis',
        'city' => 'cities',
        'uni-logo' => 'uni-logos',
    };
    $cacheRoot = public_path("img/cache/$subdir");
    if (! is_dir($cacheRoot)) @mkdir($cacheRoot, 0775, true);
    $destPath = "$cacheRoot/$slug.webp";

    if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
        return response()->json(['error' => 'GD/WebP missing on server'], 500);
    }

    try {
        $response = \Illuminate\Support\Facades\Http::timeout(30)
            ->withHeaders(['User-Agent' => 'AlmanyaUni/1.0 (image cache; tech@applytogerman.com)'])
            ->retry(1, 500)
            ->get($url);
        if (! $response->successful()) {
            return response()->json(['error' => 'Source HTTP ' . $response->status()], 422);
        }
        $bytes = $response->body();
        if (strlen($bytes) < 100 || strlen($bytes) > 12 * 1024 * 1024) {
            return response()->json(['error' => 'Source too small or too large: ' . strlen($bytes) . 'b'], 422);
        }
        $src = @imagecreatefromstring($bytes);
        if (! $src) return response()->json(['error' => 'GD cannot decode source'], 422);

        $srcW = imagesx($src);
        $srcH = imagesy($src);
        $newW = min($width, $srcW);
        $newH = (int) round($srcH * ($newW / $srcW));
        $dst = imagecreatetruecolor($newW, $newH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $tr = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $newW, $newH, $tr);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);
        $ok = imagewebp($dst, $destPath, 82);
        imagedestroy($src);
        imagedestroy($dst);

        if (! $ok) return response()->json(['error' => 'WebP encode failed'], 500);
        return response()->json([
            'ok' => true,
            'path' => "/img/cache/$subdir/$slug.webp",
            'size' => filesize($destPath),
            'dimensions' => "{$newW}x{$newH}",
            'source_size' => strlen($bytes),
        ]);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->middleware('throttle:20,1');

// Token-gated stats reset — wipe demo/test traffic numbers to start fresh
//   curl "https://applytogerman.com/_system/reset-stats?token=XXX&dry-run=1"  (preview)
//   curl "https://applytogerman.com/_system/reset-stats?token=XXX"            (apply)
Route::get('/_system/reset-stats', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = config('services.system_token');
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

// One-shot log tailer — SYSTEM_TOKEN protected. For diagnosing prod 500s when
// we don't have shell access (KAS shared hosting). Returns last N lines of the
// default channel log. Default 200, max 2000. Sensitive — token required.
//   curl "https://applytogerman.com/_system/log-tail?token=XXX&lines=150"
Route::get('/_system/log-tail', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $expected = config('services.system_token');
    if (! $expected || ! hash_equals((string) $expected, (string) $token)) {
        abort(403, 'Invalid token');
    }

    $lines = (int) min(2000, max(10, $request->query('lines', 200)));
    // LOG_STACK=daily → dosya laravel-YYYY-MM-DD.log. En YENİ log dosyasını seç
    // (laravel.log tek-dosya modundan kalma eski olabilir → güncel hatayı kaçırırız).
    $candidates = glob(storage_path('logs/laravel*.log')) ?: [];
    usort($candidates, fn ($a, $b) => filemtime($b) <=> filemtime($a));
    $logPath = $candidates[0] ?? storage_path('logs/laravel.log');
    if (! is_file($logPath)) {
        return response()->json(['error' => 'log not found', 'path' => $logPath], 404);
    }

    // Stream-tail: read from end. For typical 200-line tails this is cheap.
    $fp = fopen($logPath, 'rb');
    $bufSize = 8192;
    $size = filesize($logPath);
    $pos = $size;
    $chunk = '';
    while ($pos > 0 && substr_count($chunk, "\n") <= $lines) {
        $read = min($bufSize, $pos);
        $pos -= $read;
        fseek($fp, $pos);
        $chunk = fread($fp, $read) . $chunk;
    }
    fclose($fp);
    $tail = implode("\n", array_slice(explode("\n", $chunk), -$lines));
    return response($tail, 200)->header('Content-Type', 'text/plain; charset=utf-8');
})->middleware('throttle:30,1');

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

// llms-full.txt — llms.txt'in GENİŞ sürümü: tüm rehberler (kategoriye göre) + üni/şehir/alan
// listeleri. LLM'lerin sitenin tam içerik haritasını tek dosyada ingest etmesi için.
Route::get('/llms-full.txt', function (\Illuminate\Http\Request $request) {
    $host = strtolower(preg_replace('/^www\./', '', $request->getHost()));
    $domains = config('brand.domains', []);
    $brandKey = $domains[$host] ?? config('brand.fallback', 'almanyauni');
    $brand = config('brand.brands', [])[$brandKey] ?? [];
    $name = $brand['name'] ?? 'AlmanyaUni';
    $domain = $brand['domain'] ?? $host;
    $base = $request->getScheme() . '://' . $domain;

    $content = cache()->remember("llms_full_txt_v2_{$brandKey}", now()->addHours(12), function () use ($name, $base) {
        $out = "# {$name} — Full Content Map (llms-full.txt)\n\n";
        $out .= "> Expanded version of /llms.txt: a full index of guides/articles, universities, student cities and fields of study for international students applying to Germany. See {$base}/llms.txt for the concise version.\n\n";

        // Rehberler & makaleler (TR birincil), kategoriye göre
        $out .= "## Guides & Articles\n\n";
        $posts = \App\Models\Post::where('is_published', 1)->where('locale', 'tr')
            ->with('category')->orderByDesc('published_at')
            ->get(['id', 'slug', 'title', 'excerpt', 'category_id', 'locale']);
        $grouped = $posts->groupBy(function ($p) {
            $n = $p->category?->name;
            if (is_array($n)) { $n = $n['tr'] ?? ($n['en'] ?? reset($n)); }
            return $n ?: 'Genel';
        });
        foreach ($grouped as $cat => $items) {
            $out .= "### {$cat}\n";
            foreach ($items as $p) {
                $ex = \Illuminate\Support\Str::limit(trim(strip_tags((string) $p->excerpt)), 150);
                $out .= "- [{$p->title}]({$base}/{$p->locale}/blog/{$p->slug})" . ($ex ? ": {$ex}" : '') . "\n";
            }
            $out .= "\n";
        }

        // En büyük üniversiteler
        $out .= "## Top Universities\n\n";
        foreach (\App\Models\University::where('is_active', 1)->orderByDesc('student_count')->limit(50)->get(['slug', 'name_de']) as $u) {
            $out .= "- [{$u->name_de}]({$base}/en/universities/{$u->slug})\n";
        }
        $out .= "\n";

        // Öğrenci şehirleri
        $out .= "## Student Cities\n\n";
        foreach (\App\Models\City::where('is_active', 1)->orderBy('name_de')->get(['slug', 'name_de']) as $c) {
            $out .= "- [{$c->name_de}]({$base}/en/cities/{$c->slug})\n";
        }
        $out .= "\n";

        // Alanlar
        $out .= "## Fields of Study\n\n";
        foreach (\App\Models\FieldOfStudy::where('is_active', 1)->orderBy('name_en')->get(['slug', 'name_en', 'name_tr']) as $f) {
            $nm = $f->name_en ?: $f->name_tr;
            $out .= "- [{$nm}]({$base}/en/fields/{$f->slug})\n";
        }
        $out .= "\n";

        $out .= "## Reference\n\n";
        $out .= "- [Concise llms.txt]({$base}/llms.txt): short version with tools, scholarships, glossary\n";
        $out .= "- [Sitemap]({$base}/sitemap.xml): full machine-readable index\n\n";
        $out .= "## Editorial Notes\n\n";
        $out .= "- Languages: Turkish (primary), English, German\n";
        $out .= "- Sources: DAAD official data, Wikidata, university partner API, official Bundesländer education data\n";
        $out .= "- Authority: 10+ years education consulting experience for students applying to Germany\n";
        return $out;
    });

    return response($content, 200)->header('Content-Type', 'text/markdown; charset=utf-8');
});

// Blog yardımcı oldu mu? oyu (Alpine widget'tan POST)
Route::post('/api/blog-feedback', [\App\Http\Controllers\Web\BlogController::class, 'feedback'])
    ->middleware('throttle:30,1')
    ->name('blog.feedback');

// FAQ cevabı işine yaradı mı? oyu
Route::post('/api/faq-feedback', [\App\Http\Controllers\Web\FaqController::class, 'feedback'])
    ->middleware('throttle:30,1')
    ->name('faqs.feedback');

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

// Affiliate dış-link redirect + tıklama takibi (locale-agnostik). /go/sperrkonto/{slug}?ctx=index
Route::get('/go/{type}/{slug}', [\App\Http\Controllers\Web\AffiliateController::class, 'go'])
    ->where('type', 'sperrkonto|insurance|housing')
    ->where('slug', '[a-z0-9\-]+')
    ->middleware('throttle:60,1')
    ->name('affiliate.go');
Route::get('/api/map/universities', [MapController::class, 'universitiesJson'])
    ->middleware('throttle:30,1')
    ->name('map.universities.json');

// Gömülebilir araç widget'ları (backlink mıknatısı) — locale'siz canonical URL,
// ?lang=tr|en|de ile dil seçilir. frame-ancestors * controller'da set edilir.
Route::get('/embed/cost-of-living', [\App\Http\Controllers\Web\EmbedController::class, 'costOfLiving'])
    ->middleware('throttle:60,1')
    ->name('embed.cost-of-living');
Route::get('/embed/stats', [\App\Http\Controllers\Web\EmbedController::class, 'stats'])
    ->middleware('throttle:60,1')
    ->name('embed.stats');

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

/**
 * Host-aware default locale resolver — shared by both the bare-root redirect
 * and the locale-less fallback below. Mirrors the logic in
 * App\Http\Middleware\SetLocale::resolveDefaultFromDomain so /, /about, and
 * /tr all agree on what "default" means for the current brand.
 *
 *   almanyauni.com/    → /tr  (TR-first brand)
 *   applytogerman.com/ → /en  (intl brand)
 */
$__brandDefaultLocale = function (\Illuminate\Http\Request $request): string {
    $host     = strtolower(preg_replace('/^www\./', '', $request->getHost()));
    // NB: $host contains dots ("almanyauni.com"). config('brand.domains.almanyauni.com')
    // would treat each dot as nesting and miss the key — fetch the array first then index.
    $domains  = config('brand.domains', []);
    $brandKey = $domains[$host] ?? null;
    $brands   = config('brand.brands', []);
    $brand    = $brands[$brandKey] ?? [];
    $default  = $brand['default_locale'] ?? config('locale.default', 'en');

    $cfg = config("locale.locales.$default", []);
    if (! empty($cfg['active']) && empty($cfg['coming_soon'])) {
        return $default;
    }

    return collect(config('locale.locales', []))
        ->filter(fn ($c) => ! empty($c['active']) && empty($c['coming_soon']))
        ->keys()->first() ?? 'tr';
};

// Bare root → brand's default locale (almanyauni.com → /tr, applytogerman.com → /en)
Route::get('/', function (\Illuminate\Http\Request $request) use ($__brandDefaultLocale) {
    return redirect('/' . $__brandDefaultLocale($request), 302);
});

// Locale-prefix'siz eski/dış linkler (/universities) → brand'in default diline yönlendir (loop-safe)
Route::fallback(function (\Illuminate\Http\Request $request) use ($__brandDefaultLocale) {
    $path  = trim($request->path(), '/');
    $first = explode('/', $path)[0] ?? '';
    if (in_array($first, array_keys(config('locale.locales', [])), true)) {
        // Locale prefix'li ama route bulunamadı → 404. Locale'i set et ki 404 sayfası doğru dilde render olsun.
        \Illuminate\Support\Facades\App::setLocale($first);
        abort(404);
    }
    $target = $__brandDefaultLocale($request);
    $qs = $request->getQueryString();
    return redirect('/' . $target . ($path ? '/' . $path : '') . ($qs ? '?' . $qs : ''), 302);
});

// ─────────── Auth-protected, locale-bağımsız ───────────

Route::middleware('auth')->group(function () {
    // GEÇİCİ — admin migrate teşhis/fix (SSH yok; tarayıcıdan migrate çalıştırır + çıktı gösterir).
    // İş bitince KALDIR. Sadece is_admin.
    Route::get('/admin/ops/migrate', function () {
        abort_unless(auth()->user()?->is_admin, 403);
        @set_time_limit(300);
        $out = "=== migrate:status ===\n";
        \Illuminate\Support\Facades\Artisan::call('migrate:status');
        $out .= \Illuminate\Support\Facades\Artisan::output();
        if (request()->boolean('run')) {
            $out .= "\n=== migrate --force ===\n";
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true, '--no-interaction' => true]);
                $out .= \Illuminate\Support\Facades\Artisan::output();
            } catch (\Throwable $e) {
                $out .= "EXCEPTION: " . $e->getMessage() . "\n";
            }
            $out .= "\n=== migrate:status (sonra) ===\n";
            \Illuminate\Support\Facades\Artisan::call('migrate:status');
            $out .= \Illuminate\Support\Facades\Artisan::output();
        } else {
            $out .= "\n[ Uygulamak için: bu URL'ye ?run=1 ekle ]\n";
        }
        return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
    });

    // GEÇİCİ — çeviri import (content_blocks_en/de + FAQ EN/DE). Local'de
    // `i18n:export-content` ile üretilen gzip veri dosyalarını prod DB'ye uygular.
    // Re-runnable (idempotent). ?force=1 dolu blokların üzerine de yazar.
    Route::get('/admin/ops/import-content', function () {
        abort_unless(auth()->user()?->is_admin, 403);
        @set_time_limit(600);
        try {
            \Illuminate\Support\Facades\Artisan::call('i18n:import-content', array_filter([
                '--force' => request()->boolean('force'),
            ]));
            $out = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $e) {
            $out = 'EXCEPTION: ' . $e->getMessage();
        }
        return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
    });

    // GEÇİCİ — eksik prose (program/üni açıklaması) TR→EN/DE doldur. Strict fallback
    // ile EN/DE'de gizlenen içeriği native (ContentVoice) çeviriyle görünür kılar.
    // Re-runnable: sadece boş alanları doldurur. ?type=program|university|all &limit=N
    Route::get('/admin/ops/fill-prose', function () {
        abort_unless(auth()->user()?->is_admin, 403);
        @set_time_limit(600);
        try {
            \Illuminate\Support\Facades\Artisan::call('i18n:fill-prose', array_filter([
                '--type'  => request()->string('type', 'all')->value(),
                '--limit' => (int) request()->integer('limit'),
                '--delay' => (int) request()->integer('delay', 250),
            ]));
            $out = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $e) {
            $out = 'EXCEPTION: ' . $e->getMessage();
        }
        return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
    });

    // GEÇİCİ — DAAD burslarını TR'ye lokalize et (ad + açıklama). DAAD sadece
    // de/en veriyor → /tr'de İngilizce sızıyor. Re-runnable: name_tr boşları çevirir.
    Route::get('/admin/ops/scholarships-localize', function () {
        abort_unless(auth()->user()?->is_admin, 403);
        @set_time_limit(600);
        try {
            \Illuminate\Support\Facades\Artisan::call('scholarships:localize', array_filter([
                '--limit' => (int) request()->integer('limit'),
                '--force' => request()->boolean('force'),
                '--delay' => (int) request()->integer('delay', 250),
            ]));
            $out = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $e) {
            $out = 'EXCEPTION: ' . $e->getMessage();
        }
        return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
    });

    // GEÇİCİ — BERUFENET meslek info_fields'ını TR+EN'ye çevir (tüm alanlar, key-bazlı).
    // İlk backfill:  ?reset=1 (bir kez, eski şema tr/en'i sıfırlar) → sonra ?limit=200 (tekrar tekrar)
    // Çeyreklik resync (import diff'ten sonra): ?missing=1&limit=0
    Route::get('/admin/ops/professions-translate', function () {
        abort_unless(auth()->user()?->is_admin, 403);
        @set_time_limit(600);
        $out = '';
        try {
            if (request()->boolean('reset')) {
                $n = \App\Models\Profession::whereNotNull('info_fields_tr')
                    ->update(['info_fields_tr' => null, 'info_fields_en' => null]);
                $out .= "RESET: {$n} meslek info_fields_tr/en sıfırlandı.\n\n";
            }
            \Illuminate\Support\Facades\Artisan::call('professions:translate-info-fields', array_filter([
                '--limit'       => (int) request()->integer('limit', 0),
                '--max-seconds' => (int) request()->integer('max_seconds', 45), // gateway timeout'tan önce temiz çık
                '--sleep'       => (int) request()->integer('sleep', 0),
                '--lang'        => request()->query('lang'),                    // tr | en | boş(ikisi)
                '--shards'      => (int) request()->integer('shards', 1),       // paralel sekme
                '--shard'       => (int) request()->integer('shard', 0),
                '--force'       => request()->boolean('force'),
                '--missing'     => request()->boolean('missing'),
            ], fn ($v) => $v !== false && $v !== null));
            $out .= \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $e) {
            $out .= 'EXCEPTION: ' . $e->getMessage();
        }
        return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
    });

    // Otomatik haber çekme (Mod 1) — KAS'ta SSH yok; tarayıcıdan veya KAS
    // Cronjob bu URL'yi çağırarak RSS kaynaklardan aday çeker. Idempotent (dedupe).
    Route::get('/admin/ops/news-fetch', function () {
        abort_unless(auth()->user()?->is_admin, 403);
        @set_time_limit(300);
        try {
            \Illuminate\Support\Facades\Artisan::call('news:fetch', array_filter([
                '--dry-run' => request()->boolean('dry'),
            ]));
            $out = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $e) {
            $out = 'EXCEPTION: ' . $e->getMessage();
        }
        $out .= "\n[ Adaylar: /admin/news-candidates → İçeriği Çek → AI Taslak → Paylaş ]\n";
        return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
    });

    // "Almanya'da Yaşam & Kültür" taslak brief'lerini seed et (status=draft, asset üretmez).
    // The Local "german-habits" gibi içeriklerden KONU ilhamı; içerik özgün üretilecek.
    // Idempotent (slug). Backlog: /admin/content-briefs.
    Route::get('/admin/ops/seed-culture-briefs', function () {
        abort_unless(auth()->user()?->is_admin, 403);
        @set_time_limit(120);
        try {
            \Illuminate\Support\Facades\Artisan::call('content:seed-culture-briefs', array_filter([
                '--skip-existing' => request()->boolean('skip_existing'),
            ]));
            $out = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $e) {
            $out = 'EXCEPTION: ' . $e->getMessage();
        }
        $out .= "\n[ Taslaklar: /admin/content-briefs → fikri geliştir → Hazır → asset üret ]\n";
        return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
    });

    // Görseli olmayan yayınlanmış haberlere AI illüstrasyon (tek seferlik backfill).
    Route::get('/admin/ops/news-backfill-images', function () {
        abort_unless(auth()->user()?->is_admin, 403);
        @set_time_limit(600);
        try {
            \Illuminate\Support\Facades\Artisan::call('news:backfill-images', array_filter([
                '--dry-run' => request()->boolean('dry'),
                '--force'   => request()->boolean('force'), // metinli/eski görselleri yenile
                '--limit'   => (int) request()->integer('limit'),
            ]));
            $out = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $e) {
            $out = 'EXCEPTION: ' . $e->getMessage();
        }
        return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
    });

    // Mevcut haber slug'larını İngilizce tabana çevir (?dry=1 önizleme). URL değişir.
    Route::get('/admin/ops/news-reslug-english', function () {
        abort_unless(auth()->user()?->is_admin, 403);
        @set_time_limit(120);
        try {
            \Illuminate\Support\Facades\Artisan::call('news:reslug-english', array_filter([
                '--dry-run' => request()->boolean('dry'),
            ]));
            $out = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $e) {
            $out = 'EXCEPTION: ' . $e->getMessage();
        }
        return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
    });

    // Eksik çevirileri TAMAMLA — blog (TR→EN/DE) + FAQ. Gemini ile prod DB'ye yazar.
    // Idempotent: tamamlananı atlar. Timeout olursa (uzun Gemini) tekrar çağır.
    Route::get('/admin/ops/translate-missing', function () {
        abort_unless(auth()->user()?->is_admin, 403);
        @set_time_limit(600);
        @ini_set('max_execution_time', '600');
        $out = '';
        try {
            \Illuminate\Support\Facades\Artisan::call('content:translate-posts', [
                '--all-untranslated' => true, '--sleep' => 1,
            ]);
            $out .= "=== blog ===\n" . \Illuminate\Support\Facades\Artisan::output();
            \Illuminate\Support\Facades\Artisan::call('faq:translate', [
                '--locale' => 'en,de', '--only-broken' => true, '--sleep' => 1,
            ]);
            $out .= "\n=== faq ===\n" . \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $e) {
            $out .= "\nEXCEPTION (tekrar çağır, kaldığı yerden devam eder): " . $e->getMessage();
        }
        return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
    });

    // Analytics SIFIRLA — dummy/demo page_views verisini sil (gerçek trafikle başla).
    // KAS SSH yok → tarayıcıdan ?run=1 ile tetikle. Sadece is_admin.
    Route::get('/admin/ops/analytics-reset', function () {
        abort_unless(auth()->user()?->is_admin, 403);
        @set_time_limit(120);
        $before = \Illuminate\Support\Facades\DB::table('page_views')->count();
        if (! request()->boolean('run')) {
            return response("page_views satır: {$before}\n\n[ Silmek için: bu URL'ye ?run=1 ekle ]\n", 200)
                ->header('Content-Type', 'text/plain; charset=utf-8');
        }
        try {
            \Illuminate\Support\Facades\Artisan::call('analytics:reset', ['--force' => true]);
            $out = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $e) {
            $out = 'EXCEPTION: ' . $e->getMessage();
        }
        return response("Önce: {$before} satır\n{$out}", 200)->header('Content-Type', 'text/plain; charset=utf-8');
    });

    // OG görsel cache'ini temizle — eski (Türkçe gliflerin kutu çıktığı) PNG'ler
    // silinir, sonraki istekte yeni fontla yeniden üretilir. ?run=1 ile siler.
    Route::get('/admin/ops/clear-og-cache', function () {
        abort_unless(auth()->user()?->is_admin, 403);
        $dir = storage_path('app/public/og');
        $files = is_dir($dir) ? \Illuminate\Support\Facades\File::allFiles($dir) : [];
        $count = count($files);
        if (! request()->boolean('run')) {
            return response("OG cache: {$count} PNG\n\n[ Silmek için: bu URL'ye ?run=1 ekle ]\n", 200)
                ->header('Content-Type', 'text/plain; charset=utf-8');
        }
        foreach ($files as $f) {
            @unlink($f->getPathname());
        }
        return response("Silindi: {$count} OG PNG. Yeni fontla yeniden üretilecekler.\n", 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    });

    // Yazılardaki AI iç linklerini çöz: gerçek yazıya bağla / hedefsizi düz metne indir.
    // ?dry=1 önizleme, ?run=1 uygula, ?limit=N. Idempotent.
    Route::get('/admin/ops/resolve-post-links', function () {
        abort_unless(auth()->user()?->is_admin, 403);
        @set_time_limit(300);
        if (! request()->boolean('run') && ! request()->boolean('dry')) {
            return response("Önizleme: ?dry=1 · Uygula: ?run=1 · (&limit=N)\n", 200)
                ->header('Content-Type', 'text/plain; charset=utf-8');
        }
        try {
            \Illuminate\Support\Facades\Artisan::call('content:resolve-post-links', array_filter([
                '--dry-run' => request()->boolean('dry'),
                '--limit'   => (int) request()->integer('limit'),
            ]));
            $out = \Illuminate\Support\Facades\Artisan::output();
        } catch (\Throwable $e) {
            $out = 'EXCEPTION: ' . $e->getMessage();
        }
        return response($out, 200)->header('Content-Type', 'text/plain; charset=utf-8');
    });

    // Dashboard/Profil/Favoriler — prefix'siz (URL /profile) AMA kullanıcı-yüzlü.
    // set.locale ŞART: yoksa route() global default 'en' kullanır → tüm iç linkler
    // (üni gez, şehir gez, öneri testi) kullanıcı TR'deyken İngilizce sayfaya sızar.
    // set.locale cookie/session locale'i okuyup URL::defaults'u kullanıcının diline çeker.
    Route::middleware('set.locale')->group(function () {
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

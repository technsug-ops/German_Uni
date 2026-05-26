<?php

namespace App\Providers;

use App\Models\Feedback;
use App\Models\Program;
use App\Models\University;
use App\Observers\FeedbackObserver;
use App\Observers\WebhookEventObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        University::observe(WebhookEventObserver::class);
        Program::observe(WebhookEventObserver::class);
        Feedback::observe(FeedbackObserver::class);

        // i18n: Tüm route'lar {locale} prefix'li olduğundan route() her bağlamda bir locale ister.
        // Global default → SetLocale olmayan bağlamlarda (sitemap, rss, og, api, email, console)
        // route() patlamaz. İstek bazında SetLocale bunu gerçek locale ile override eder.
        // Değer: default dil hazırsa o, değilse ilk aktif dil (şu an /tr).
        $d = config('locale.default', 'en');
        $dcfg = config("locale.locales.$d", []);
        $urlLocale = (! empty($dcfg['active']) && empty($dcfg['coming_soon']))
            ? $d
            : (collect(config('locale.locales', []))
                ->filter(fn ($c) => ! empty($c['active']) && empty($c['coming_soon']))
                ->keys()->first() ?? $d);
        \Illuminate\Support\Facades\URL::defaults(['locale' => $urlLocale]);
    }
}

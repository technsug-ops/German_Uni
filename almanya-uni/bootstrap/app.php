<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // SetLocale GLOBAL DEĞİL — sadece {locale} route grubuna uygulanır (routes/web.php).
        // Global olursa sitemap/rss/api gibi locale-bağımsız route'larda da çalışıp /tr'ye yönlendirir.
        $middleware->web(append: [
            \App\Http\Middleware\ThrottleScrapers::class,
            \App\Http\Middleware\TrackPageView::class,
            \App\Http\Middleware\EnsurePageEnabled::class,
        ]);

        // KVKK consent + analytics session cookie'leri şifreleme dışı (JS okuyabilsin)
        $middleware->encryptCookies(except: [
            'almanyauni_consent',
            'almanyauni_uid',
        ]);

        // sendBeacon CSRF token gönderemez — engagement (scroll/süre) verisi hassas değil
        $middleware->validateCsrfTokens(except: [
            'api/blog-engagement',
        ]);

        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        $middleware->alias([
            'set.locale' => \App\Http\Middleware\SetLocale::class,
            'api.throttle' => \App\Http\Middleware\ApiThrottleAndLog::class,
            'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

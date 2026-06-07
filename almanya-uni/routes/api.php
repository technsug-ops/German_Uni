<?php

use App\Http\Controllers\Api\PartnerWebhookController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\BlockedAccountController;
use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\CityController;
use App\Http\Controllers\Api\V1\FaqController;
use App\Http\Controllers\Api\V1\HousingProviderController;
use App\Http\Controllers\Api\V1\FieldOfStudyController;
use App\Http\Controllers\Api\V1\ProfessionController;
use App\Http\Controllers\Api\V1\ProgramController;
use App\Http\Controllers\Api\V1\ScholarshipController;
use App\Http\Controllers\Api\V1\StateController;
use App\Http\Controllers\Api\V1\UniversityController;
use App\Http\Controllers\Api\V1\WebhookSubscriptionController;
use Illuminate\Support\Facades\Route;

// Partner kuruluş webhook receiver — HMAC-SHA256 signature ile korumalı.
Route::post('partner/webhook', [PartnerWebhookController::class, 'handle'])
    ->middleware('throttle:120,1')
    ->name('partner.webhook');

// Brevo transactional event webhook — bounce/complaint/click/open
// Auth: X-Brevo-Webhook-Token header (config services.brevo.webhook_token)
Route::post('webhooks/brevo', [\App\Http\Controllers\Webhooks\BrevoWebhookController::class, 'handle'])
    ->middleware('throttle:200,1')
    ->name('webhooks.brevo');

// Resend transactional event webhook — bounce/complaint/click/open
// Auth: Svix HMAC signature (svix-id, svix-timestamp, svix-signature headers)
Route::post('webhooks/resend', [\App\Http\Controllers\Webhooks\ResendWebhookController::class, 'handle'])
    ->middleware('throttle:300,1')
    ->name('webhooks.resend');

// FlatReklam Özel Site SEO API (v1) — sağlayıcı sözleşmesi.
// Auth: flatreklam.auth (Bearer = setting('flatreklam_api_token')).
// Hem /api/flatreklam/v1/* hem /api/flatreklam/* base URL'i çalışır (panelin
// /v1 ekleyip eklemediği belirsiz — ikisini de kabul ediyoruz).
$flatreklamRoutes = function () {
    $c = \App\Http\Controllers\Api\FlatReklam\SeoController::class;
    Route::get('ping', [$c, 'ping']);
    Route::get('seo-resources', [$c, 'index']);
    Route::get('seo-resources/{id}', [$c, 'show']);
    Route::patch('seo-resources/{id}', [$c, 'update']);
};
Route::middleware(['flatreklam.auth', 'throttle:60,1'])->group(function () use ($flatreklamRoutes) {
    Route::prefix('flatreklam/v1')->group($flatreklamRoutes);
    Route::prefix('flatreklam')->group($flatreklamRoutes);
});

Route::prefix('v1')->middleware('api.throttle')->group(function () {
    Route::prefix('auth')->group(function () {
        // Self-registration KAPALI — açık API verilmiyor. API erişimi yalnızca
        // yönetimce verilen token'larla (apiclient:create) olur. login mevcut
        // hesaplar için açık kalır.
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('logout-all', [AuthController::class, 'logoutAll']);
        });
    });

    // ──────────────────────────────────────────────────────────────────
    // Veri (okuma) API'si — ARTIK İZNE TABİ. Açık erişim kapatıldı:
    // geçerli bir Sanctum token'ı (ApiClient `apiclient:create` ile verilir,
    // veya kullanıcı login token'ı) ZORUNLU. ApiClient plan ability'leri
    // enforce edilir; kullanıcı token'ları '*' aldığı için tümüne erişir.
    //   free       → read:universities, read:programs, read:reference
    //   partner    → + webhooks:manage
    //   enterprise → * (her şey)
    // ──────────────────────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::middleware('ability:read:universities,*')->group(function () {
            Route::get('universities', [UniversityController::class, 'index']);
            Route::get('universities/top', [UniversityController::class, 'top']);
            Route::get('universities/compare', [UniversityController::class, 'compare']);
            Route::get('universities/{slugOrId}/content', [UniversityController::class, 'content']);
            Route::get('universities/{slugOrId}', [UniversityController::class, 'show']);
        });

        Route::middleware('ability:read:programs,*')->group(function () {
            Route::get('programs', [ProgramController::class, 'index']);
            Route::get('programs/stats', [ProgramController::class, 'stats']);
            Route::get('programs/{slugOrId}', [ProgramController::class, 'show']);
        });

        // Referans veriler (eyalet/şehir/alan/meslek/SSS/burs/yurt/bloke hesap/blog)
        Route::middleware('ability:read:reference,*')->group(function () {
            Route::get('states', [StateController::class, 'index']);
            Route::get('states/{slugOrId}', [StateController::class, 'show']);

            Route::get('cities', [CityController::class, 'index']);
            Route::get('cities/{slugOrId}/content', [CityController::class, 'content']);
            Route::get('cities/{slugOrId}', [CityController::class, 'show']);

            Route::get('fields-of-study', [FieldOfStudyController::class, 'index']);
            Route::get('fields-of-study/{slugOrId}', [FieldOfStudyController::class, 'show']);

            Route::get('professions', [ProfessionController::class, 'index']);
            Route::get('professions/{slugOrId}', [ProfessionController::class, 'show']);

            Route::get('faqs', [FaqController::class, 'index']);
            Route::get('faqs/topics', [FaqController::class, 'topics']);
            Route::get('faqs/{slugOrId}', [FaqController::class, 'show']);

            Route::get('scholarships', [ScholarshipController::class, 'index']);
            Route::get('scholarships/{slugOrId}', [ScholarshipController::class, 'show']);

            Route::get('housing-providers', [HousingProviderController::class, 'index']);
            Route::get('housing-providers/{slugOrId}', [HousingProviderController::class, 'show']);

            Route::get('blocked-accounts', [BlockedAccountController::class, 'index']);
            Route::get('blocked-accounts/{slugOrId}', [BlockedAccountController::class, 'show']);

            Route::get('blog', [BlogController::class, 'index']);
            Route::get('blog/{slugOrId}', [BlogController::class, 'show']);
        });
    });

    // 3. parti'lerin event abonelikleri — ApiClient bearer + `webhooks:manage` ability.
    // free plan'da abone olunamaz (403), partner/enterprise olabilir.
    Route::middleware(['auth:sanctum', 'ability:webhooks:manage,*'])
        ->prefix('webhooks/subscriptions')->group(function () {
            Route::get('/', [WebhookSubscriptionController::class, 'index']);
            Route::post('/', [WebhookSubscriptionController::class, 'store']);
            Route::get('{id}', [WebhookSubscriptionController::class, 'show']);
            Route::patch('{id}', [WebhookSubscriptionController::class, 'update']);
            Route::delete('{id}', [WebhookSubscriptionController::class, 'destroy']);
            Route::post('{id}/test', [WebhookSubscriptionController::class, 'test']);
        });
});

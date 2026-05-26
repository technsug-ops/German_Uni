<?php

use App\Http\Controllers\Api\PartnerWebhookController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\CityController;
use App\Http\Controllers\Api\V1\FaqController;
use App\Http\Controllers\Api\V1\FieldOfStudyController;
use App\Http\Controllers\Api\V1\ProfessionController;
use App\Http\Controllers\Api\V1\ProgramController;
use App\Http\Controllers\Api\V1\StateController;
use App\Http\Controllers\Api\V1\UniversityController;
use App\Http\Controllers\Api\V1\WebhookSubscriptionController;
use Illuminate\Support\Facades\Route;

// Partner kuruluş webhook receiver — HMAC-SHA256 signature ile korumalı.
Route::post('partner/webhook', [PartnerWebhookController::class, 'handle'])
    ->middleware('throttle:120,1')
    ->name('partner.webhook');

Route::prefix('v1')->middleware('api.throttle')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,1');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('logout-all', [AuthController::class, 'logoutAll']);
        });
    });

    Route::get('universities', [UniversityController::class, 'index']);
    Route::get('universities/top', [UniversityController::class, 'top']);
    Route::get('universities/compare', [UniversityController::class, 'compare']);
    Route::get('universities/{slugOrId}/content', [UniversityController::class, 'content']);
    Route::get('universities/{slugOrId}', [UniversityController::class, 'show']);

    Route::get('states', [StateController::class, 'index']);
    Route::get('states/{slugOrId}', [StateController::class, 'show']);

    Route::get('cities', [CityController::class, 'index']);
    Route::get('cities/{slugOrId}/content', [CityController::class, 'content']);
    Route::get('cities/{slugOrId}', [CityController::class, 'show']);

    Route::get('programs', [ProgramController::class, 'index']);
    Route::get('programs/stats', [ProgramController::class, 'stats']);
    Route::get('programs/{slugOrId}', [ProgramController::class, 'show']);

    Route::get('fields-of-study', [FieldOfStudyController::class, 'index']);
    Route::get('fields-of-study/{slugOrId}', [FieldOfStudyController::class, 'show']);

    Route::get('professions', [ProfessionController::class, 'index']);
    Route::get('professions/{slugOrId}', [ProfessionController::class, 'show']);

    Route::get('faqs', [FaqController::class, 'index']);
    Route::get('faqs/topics', [FaqController::class, 'topics']);
    Route::get('faqs/{slugOrId}', [FaqController::class, 'show']);

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

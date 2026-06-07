<?php

namespace Tests\Feature;

use App\Models\ApiClient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Veri API'si yetkilendirme matrisi — açık erişim kapatıldıktan sonra (2026-06-07):
 *   token yok        → 401
 *   ApiClient free   → read uçları 200, webhooks:manage 403
 *   ApiClient partner→ webhooks:manage erişebilir
 *   User ('*')       → her şeye erişir
 */
class ApiAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function apiClient(string $plan): ApiClient
    {
        return ApiClient::create([
            'name' => ucfirst($plan) . ' Client',
            'slug' => $plan . '-' . uniqid(),
            'contact_email' => $plan . '@example.com',
            'plan' => $plan,
            'rate_limit_per_minute' => ApiClient::PLAN_LIMITS[$plan],
            'is_active' => true,
        ]);
    }

    private function tokenFor(ApiClient $client): string
    {
        return $client->createToken('test', $client->defaultAbilities())->plainTextToken;
    }

    // ─────────────── NEGATİF: token yok ───────────────

    public function test_token_olmadan_veri_ucu_401(): void
    {
        $this->getJson('/api/v1/universities')->assertStatus(401);
        $this->getJson('/api/v1/cities')->assertStatus(401);
        $this->getJson('/api/v1/programs')->assertStatus(401);
    }

    public function test_gecersiz_token_401(): void
    {
        $this->withToken('gecersiz-token-123')
            ->getJson('/api/v1/universities')->assertStatus(401);
    }

    // ─────────────── POZİTİF: ApiClient free okuma ───────────────

    public function test_free_client_referans_verisini_okur(): void
    {
        $token = $this->tokenFor($this->apiClient('free'));

        // read:reference ability → cities erişebilir (5xx/401/403 olmamalı).
        $this->withToken($token)->getJson('/api/v1/cities')->assertStatus(200);
        $this->withToken($token)->getJson('/api/v1/universities')->assertStatus(200);
        $this->withToken($token)->getJson('/api/v1/programs')->assertStatus(200);
    }

    // ─────────────── NEGATİF: free webhook yönetemez ───────────────

    public function test_free_client_webhook_yonetemez_403(): void
    {
        $token = $this->tokenFor($this->apiClient('free'));

        // free plan'da webhooks:manage ability YOK → 403.
        $this->withToken($token)
            ->getJson('/api/v1/webhooks/subscriptions')->assertStatus(403);
    }

    // ─────────────── POZİTİF: partner webhook erişir ───────────────

    public function test_partner_client_webhook_erisir(): void
    {
        $token = $this->tokenFor($this->apiClient('partner'));

        // partner'da webhooks:manage VAR → 403 olmamalı (liste 200 döner).
        $this->withToken($token)
            ->getJson('/api/v1/webhooks/subscriptions')->assertStatus(200);
    }

    // ─────────────── POZİTİF: User '*' her şeye erişir ───────────────

    public function test_user_token_tum_veri_uclarina_erisir(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('app', ['*'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/universities')->assertStatus(200);
        $this->withToken($token)->getJson('/api/v1/scholarships')->assertStatus(200);
        $this->withToken($token)->getJson('/api/v1/blog')->assertStatus(200);
    }
}

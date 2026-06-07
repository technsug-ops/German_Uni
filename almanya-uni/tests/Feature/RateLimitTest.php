<?php

namespace Tests\Feature;

use App\Models\ApiClient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Brute-force / abuse savunması:
 *  - Login: 5 başarısız denemeden sonra DOĞRU şifreyle bile kilitli (per-email throttle).
 *  - Veri API'si: dakikalık limit aşılınca 429 + Retry-After header.
 */
class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────── Login throttle ───────────────

    public function test_bes_basarisiz_giristen_sonra_dogru_sifre_bile_kilitli(): void
    {
        $user = User::factory()->create(['password' => Hash::make('dogru-sifre')]);

        // 5 yanlış deneme → throttle sayacı dolar.
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'yanlis-sifre',
                ...$this->captcha(),
            ]);
        }

        // 6. deneme DOĞRU şifreyle → yine de kilitli (rate limit, kimlik değil).
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'dogru-sifre',
            ...$this->captcha(),
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_farkli_email_kilitten_etkilenmez(): void
    {
        $locked = User::factory()->create(['password' => Hash::make('x')]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => $locked->email,
                'password' => 'wrong',
                ...$this->captcha(),
            ]);
        }

        // Throttle key email|ip → başka kullanıcı geçerli şifresiyle girebilmeli.
        $other = User::factory()->create(['password' => Hash::make('gizli-123')]);
        $this->post('/login', [
            'email' => $other->email,
            'password' => 'gizli-123',
            ...$this->captcha(),
        ]);

        $this->assertAuthenticatedAs($other);
    }

    // ─────────────── API throttle (429) ───────────────

    public function test_api_dakikalik_limit_asilinca_429(): void
    {
        $client = ApiClient::create([
            'name' => 'Düşük Limitli Client',
            'slug' => 'low-' . uniqid(),
            'contact_email' => 'low@example.com',
            'plan' => 'free',
            'rate_limit_per_minute' => 2, // efektif limit 2
            'is_active' => true,
        ]);
        $token = $client->createToken('t', $client->defaultAbilities())->plainTextToken;

        // İlk 2 istek limit içinde.
        $this->withToken($token)->getJson('/api/v1/cities')->assertStatus(200);
        $this->withToken($token)->getJson('/api/v1/cities')->assertStatus(200);

        // 3. istek limiti aşar → 429 + retry header.
        $this->withToken($token)->getJson('/api/v1/cities')
            ->assertStatus(429)
            ->assertHeader('X-RateLimit-Remaining', '0')
            ->assertJsonStructure(['message', 'limit_per_minute', 'retry_after_seconds']);
    }
}

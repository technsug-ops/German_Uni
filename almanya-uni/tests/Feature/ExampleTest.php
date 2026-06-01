<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Kök URL aktif dile yönlendirir (/tr); takip edip ana sayfa açılıyor mu
        $response = $this->followingRedirects()->get('/');

        $response->assertOk();
    }
}

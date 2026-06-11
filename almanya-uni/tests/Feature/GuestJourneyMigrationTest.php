<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestJourneyMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_journey_migrates_to_user_on_login(): void
    {
        $user = User::factory()->create();

        // Misafir session ilerlemesi
        session([
            'journey.steps'         => ['eligibility', 'university_match'],
            'journey.target_degree' => 'master',
            'journey.target_intake' => 'WS2026',
        ]);

        event(new Login('web', $user, false));

        $tracker = $user->fresh()->applicationTracker;
        $this->assertNotNull($tracker);
        $this->assertEqualsCanonicalizing(['eligibility', 'university_match'], $tracker->steps_completed);
        $this->assertSame('master', $tracker->target_degree);
        $this->assertSame('WS2026', $tracker->target_intake);

        // Session temizlendi
        $this->assertEmpty(session('journey.steps', []));
    }

    public function test_login_merges_without_overwriting_db_progress(): void
    {
        $user = User::factory()->create();
        $user->applicationTracker()->create([
            'steps_completed' => ['visa'],
            'target_degree'   => 'bachelor',
            'started_at'      => now(),
        ]);

        session([
            'journey.steps'         => ['eligibility'],
            'journey.target_degree' => 'master', // DB'de bachelor var → ezilmemeli
        ]);

        event(new Login('web', $user, false));

        $tracker = $user->fresh()->applicationTracker;
        $this->assertEqualsCanonicalizing(['visa', 'eligibility'], $tracker->steps_completed);
        $this->assertSame('bachelor', $tracker->target_degree);
    }

    public function test_login_without_guest_progress_creates_no_tracker(): void
    {
        $user = User::factory()->create();

        event(new Login('web', $user, false));

        $this->assertNull($user->fresh()->applicationTracker);
    }

    public function test_invalid_step_keys_are_ignored(): void
    {
        $user = User::factory()->create();
        session(['journey.steps' => ['eligibility', 'not_a_real_step']]);

        event(new Login('web', $user, false));

        $tracker = $user->fresh()->applicationTracker;
        $this->assertSame(['eligibility'], $tracker->steps_completed);
    }
}

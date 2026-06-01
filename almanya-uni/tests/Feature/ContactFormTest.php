<?php

namespace Tests\Feature;

use App\Models\Feedback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * İletişim formu — bu oturumda POST'ta "Undefined array key name" 500'ü vermişti.
 * Bu testler deploy öncesi koşsaydı onu yakalardı.
 */
class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_contact_form_renders(): void
    {
        $this->get(route('contact', ['type' => 'partnership', 'subject' => 'Translate']))
            ->assertStatus(200)
            ->assertSee('Translate', false); // preset subject input'ta
    }

    public function test_contact_submission_creates_feedback(): void
    {
        $this->post(route('contact.store'), [
            'type' => 'partnership',
            'email' => 'visitor@example.com',
            'subject' => 'Translate',
            'message' => 'I would like to help translate content into Spanish.',
        ])->assertRedirect();

        $this->assertDatabaseHas(Feedback::class, [
            'email' => 'visitor@example.com',
            'type' => 'partnership',
            'subject' => 'Translate',
        ]);
    }

    public function test_contact_submission_without_optional_name(): void
    {
        // İsim göndermeden — "Undefined array key name" regresyonu için
        $this->post(route('contact.store'), [
            'type' => 'general',
            'email' => 'noname@example.com',
            'message' => 'A general question without providing a name field.',
        ])->assertRedirect();

        $this->assertSame(1, Feedback::where('email', 'noname@example.com')->count());
    }

    public function test_contact_requires_email_and_message(): void
    {
        $this->post(route('contact.store'), ['type' => 'general'])
            ->assertSessionHasErrors(['email', 'message']);
    }
}

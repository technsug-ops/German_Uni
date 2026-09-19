<?php

namespace Tests\Feature;

use App\Models\EmailMessage;
use App\Models\OutreachContact;
use App\Services\Mail\Outbox;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Firma kontak defterinin takip otomatiği.
 *
 * NEDEN BU TEST VAR: defterin tek işi "kiminle nerede kaldık"ı doğru göstermek.
 * Mail gidince durumun ilerlemesi ve yazışmanın kontağa bağlanması elle
 * güncellemeye bırakılırsa defter ilk haftadan yanlış bilgi gösterir.
 */
class OutreachContactTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_sending_links_the_message_and_advances_the_contact(): void
    {
        Mail::fake();

        $contact = OutreachContact::create([
            'organization' => 'CHE Test gGmbH',
            'email' => 'info@che.example',
            'category' => 'data_provider',
            'status' => 'new',
        ]);

        $msg = Outbox::send(
            'partnerships',
            $contact->email,
            $contact->organization,
            'Anfrage',
            'Sehr geehrte Damen und Herren,',
            ['contact_id' => $contact->id],
        );

        $this->assertSame('sent', $msg->status);
        $this->assertSame($contact->id, $msg->contact_id);

        $contact->refresh();
        $this->assertSame('contacted', $contact->status, 'Mail gidince durum "mail atıldı"ya geçmeli.');
        $this->assertNotNull($contact->last_contacted_at);
        $this->assertSame(1, $contact->messages()->count());
    }

    public function test_reply_marks_contact_replied_but_does_not_undo_later_stages(): void
    {
        $contact = OutreachContact::create([
            'organization' => 'CHE Test gGmbH',
            'email' => 'info@che.example',
            'status' => 'contacted',
        ]);

        $contact->markReplied();
        $this->assertSame('replied', $contact->fresh()->status);

        // Anlaşma sağlanmışsa yeni bir yanıt durumu geri almamalı.
        $contact->update(['status' => 'partner']);
        $contact->markReplied();
        $this->assertSame('partner', $contact->fresh()->status);
    }

    public function test_incoming_address_matches_contact_case_insensitively(): void
    {
        $contact = OutreachContact::create([
            'organization' => 'CHE Test gGmbH',
            'email' => 'info@che.example',
        ]);

        $this->assertSame($contact->id, OutreachContact::findByEmail('INFO@che.example ')?->id);
        $this->assertNull(OutreachContact::findByEmail('baska@adres.example'));
        $this->assertNull(OutreachContact::findByEmail(null));
    }

    public function test_followup_due_ignores_closed_contacts(): void
    {
        $open = OutreachContact::create([
            'organization' => 'Açık',
            'status' => 'contacted',
            'next_followup_at' => now()->subDay(),
        ]);
        $closed = OutreachContact::create([
            'organization' => 'Kapalı',
            'status' => 'declined',
            'next_followup_at' => now()->subDay(),
        ]);
        $future = OutreachContact::create([
            'organization' => 'İleri tarih',
            'status' => 'contacted',
            'next_followup_at' => now()->addWeek(),
        ]);

        $this->assertTrue($open->isFollowupDue());
        $this->assertFalse($closed->isFollowupDue());
        $this->assertFalse($future->isFollowupDue());
    }

    public function test_che_data_request_template_and_contact_are_seeded(): void
    {
        $contact = OutreachContact::where('email', 'info@che.de')->first();
        $this->assertNotNull($contact, 'CHE kontağı data-migration ile gelmeli.');
        $this->assertSame('data_provider', $contact->category);

        $template = \App\Models\EmailTemplate::where('key', 'data-request-che-de')->first();
        $this->assertNotNull($template, 'CHE veri talebi şablonu data-migration ile gelmeli.');
        $this->assertStringContainsString('HRK-Nummer', $template->body);
        $this->assertStringContainsString('{{sender_name}}', $template->body);
    }

    public function test_message_without_contact_still_sends(): void
    {
        Mail::fake();

        $msg = Outbox::send('partnerships', 'kimse@ornek.example', null, 'Konu', 'Gövde');

        $this->assertSame('sent', $msg->status);
        $this->assertNull($msg->contact_id);
        $this->assertSame(1, EmailMessage::where('direction', 'outbound')->count());
    }
}

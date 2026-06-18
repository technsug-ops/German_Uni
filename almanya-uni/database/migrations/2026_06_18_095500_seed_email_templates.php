<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = Carbon::now();

        $bodyA = <<<'TXT'
Sehr geehrte Damen und Herren,

mein Name ist {{sender_name}}, ich bin Gründer von ApplyToGerman.com.

ApplyToGerman ist eine Online-Plattform, die internationale – insbesondere türkischsprachige – Studierende Schritt für Schritt durch ihren Weg an eine deutsche Hochschule begleitet: von der Studienplatzbewerbung über Visum und Krankenversicherung bis hin zur Wohnungssuche in {{city}}.

Unsere Nutzerinnen und Nutzer haben in der Regel bereits eine Zulassung erhalten und suchen kurzfristig eine Unterkunft – genau die Zielgruppe, die für die Wohnheime von {{provider_name}} relevant ist. Auf unseren Stadt- und Wohnseiten stellen wir Studierendenwerke bereits mit Kontakt- und Konditionsangaben vor und verlinken auf deren offizielle Seiten.

Wir möchten Ihnen eine Kooperation vorschlagen:
• einen offiziellen Partner-Eintrag mit stets aktuellen, korrekten Angaben;
• die direkte Weiterleitung bereits zugelassener Studierender auf Ihr Bewerbungsportal;
• auf Wunsch gemeinsame Inhalte (z. B. „So bewerbe ich mich um einen Wohnheimplatz in {{city}}").

Für uns steht im Vordergrund, unseren Nutzerinnen und Nutzern verlässliche Informationen zu bieten – und Ihnen motivierte internationale Studierende zuzuführen.

Gerne stelle ich ApplyToGerman in einem kurzen Telefonat näher vor. Wann würde es Ihnen passen?

Mit freundlichen Grüßen
{{sender_name}}
ApplyToGerman.com
TXT;

        // Template B: same as A, but add the affiliate sentence before the "Für uns steht..." paragraph.
        $affiliateSentence = 'Selbstverständlich sind wir auch offen für eine partnerschaftliche Vergütung pro vermittelter Buchung (Affiliate-/Referral-Modell).';
        $bodyB = str_replace(
            "Für uns steht im Vordergrund,",
            $affiliateSentence . "\n\nFür uns steht im Vordergrund,",
            $bodyA
        );

        $bodyC = <<<'TXT'
Hello {{provider_name}} team,

I'm {{sender_name}} from ApplyToGerman.com, a platform guiding international students through their move to Germany — admission, visa, insurance and housing.

We already feature student housing providers across German cities and send pre-qualified, already-admitted students to their booking pages. We'd like to join your affiliate/referral program so we can track the bookings we drive and grow the partnership.

Could you point me to the right contact or signup link for your affiliate program?

Thank you — looking forward to working together.

Best regards,
{{sender_name}}
ApplyToGerman.com
TXT;

        $templates = [
            [
                'key' => 'partnership-stw-de',
                'name' => 'Partnerlik — Studierendenwerk (DE)',
                'category' => 'partnership',
                'locale' => 'de',
                'sort_order' => 1,
                'subject' => 'Kooperationsanfrage – Studierendenwohnheime für internationale Studierende',
                'body' => $bodyA,
            ],
            [
                'key' => 'partnership-private-de',
                'name' => 'Partnerlik — Özel Zincir (DE)',
                'category' => 'partnership',
                'locale' => 'de',
                'sort_order' => 2,
                'subject' => 'Partnerschaft & Affiliate – studentisches Wohnen auf ApplyToGerman.com',
                'body' => $bodyB,
            ],
            [
                'key' => 'affiliate-application-en',
                'name' => 'Affiliate Başvuru (EN)',
                'category' => 'affiliate',
                'locale' => 'en',
                'sort_order' => 3,
                'subject' => 'Affiliate partnership inquiry – ApplyToGerman.com (student housing)',
                'body' => $bodyC,
            ],
        ];

        foreach ($templates as $t) {
            DB::table('email_templates')->updateOrInsert(
                ['key' => $t['key']],
                [
                    'name' => $t['name'],
                    'category' => $t['category'],
                    'locale' => $t['locale'],
                    'subject' => $t['subject'],
                    'body' => $t['body'],
                    'is_active' => true,
                    'sort_order' => $t['sort_order'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('email_templates')->whereIn('key', [
            'partnership-stw-de',
            'partnership-private-de',
            'affiliate-application-en',
        ])->delete();
    }
};

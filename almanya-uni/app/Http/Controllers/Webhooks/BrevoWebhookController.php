<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Brevo (formerly Sendinblue) transactional email event webhook.
 *
 * Brevo POSTs JSON to /webhooks/brevo on every email event:
 *   - delivered / hard_bounce / soft_bounce / spam / unsubscribed
 *   - opened / clicked / blocked / invalid_email
 *
 * Doc: https://developers.brevo.com/docs/transactional-webhooks
 *
 * Security: header X-Brevo-Webhook-Token must match SERVICES_BREVO_WEBHOOK_TOKEN env.
 * (Brevo doesn't sign payloads natively — we add a custom static-token guard.)
 */
class BrevoWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        // Auth — static token shared secret
        $expected = (string) config('services.brevo.webhook_token');
        if ($expected === '' || ! hash_equals($expected, (string) $request->header('X-Brevo-Webhook-Token'))) {
            Log::warning('Brevo webhook unauthorized', ['ip' => $request->ip()]);
            return response()->json(['error' => 'unauthorized'], 401);
        }

        // Brevo can send a single object OR a batch array
        $events = $request->json('event') ? [$request->all()] : $request->all();
        if (! is_array($events)) {
            return response()->json(['error' => 'bad payload'], 400);
        }

        $handled = 0;
        foreach ($events as $event) {
            try {
                $this->processEvent($event);
                $handled++;
            } catch (\Throwable $e) {
                Log::warning('Brevo webhook event processing failed', [
                    'err' => $e->getMessage(),
                    'event' => $event['event'] ?? null,
                ]);
            }
        }

        return response()->json(['ok' => true, 'handled' => $handled]);
    }

    private function processEvent(array $event): void
    {
        $type  = $event['event'] ?? null;
        $email = strtolower((string) ($event['email'] ?? ''));
        if (! $type || ! $email) return;

        $sub = Subscriber::where('email', $email)->first();
        if (! $sub) return; // not our subscriber

        // Always stash the most recent webhook event for debugging
        $sub->webhook_meta = [
            'last_event' => $type,
            'last_at'    => now()->toIso8601String(),
            'reason'     => $event['reason']  ?? null,
            'subject'    => $event['subject'] ?? null,
        ];

        switch ($type) {
            case 'hard_bounce':
            case 'invalid_email':
            case 'blocked':
                $sub->bounce_count = ($sub->bounce_count ?? 0) + 1;
                $sub->bounced_at = $sub->bounced_at ?: now();
                $sub->save();
                Log::info('Brevo hard bounce — subscriber blocked', ['email' => $email, 'type' => $type]);
                break;

            case 'soft_bounce':
                $sub->bounce_count = ($sub->bounce_count ?? 0) + 1;
                // Mark as bounced after 5 consecutive soft bounces
                if ($sub->bounce_count >= 5) {
                    $sub->bounced_at = $sub->bounced_at ?: now();
                }
                $sub->save();
                break;

            case 'spam':
                // Strict policy: spam complaint == immediate unsubscribe
                $sub->complaint_at = now();
                $sub->unsubscribed_at = $sub->unsubscribed_at ?: now();
                $sub->unsubscribe_reason = 'spam_complaint';
                $sub->save();
                Log::info('Brevo spam complaint — subscriber unsubscribed', ['email' => $email]);
                break;

            case 'unsubscribed':
                if (! $sub->unsubscribed_at) {
                    $sub->unsubscribed_at = now();
                    $sub->unsubscribe_reason = 'brevo_list_unsubscribe';
                    $sub->save();
                }
                break;

            case 'opened':
            case 'uniqueOpened':
                $sub->open_count = ($sub->open_count ?? 0) + 1;
                $sub->last_open_at = now();
                $sub->save();
                break;

            case 'click':
                $sub->click_count = ($sub->click_count ?? 0) + 1;
                $sub->last_click_at = now();
                $sub->save();
                break;

            case 'delivered':
            case 'request':
                // Already known — just refresh webhook_meta which we set above
                $sub->save();
                break;

            default:
                // unknown event type — log + persist meta
                $sub->save();
                Log::info('Brevo webhook unknown event', ['type' => $type, 'email' => $email]);
        }
    }
}

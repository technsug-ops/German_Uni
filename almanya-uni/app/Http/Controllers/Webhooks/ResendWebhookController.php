<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Resend (resend.com) transactional email webhook.
 *
 * Resend POSTs JSON to /api/webhooks/resend on every email event.
 * Events: email.sent, email.delivered, email.delivery_delayed,
 *         email.complained, email.bounced, email.opened, email.clicked
 *
 * Doc: https://resend.com/docs/dashboard/webhooks/introduction
 *
 * Security: cryptographic signature verification via Svix headers:
 *   svix-id, svix-timestamp, svix-signature
 * Secret comes from Resend dashboard when you create the webhook.
 * Stored in env as RESEND_WEBHOOK_SECRET (config services.resend.webhook_secret).
 */
class ResendWebhookController extends Controller
{
    private const TIMESTAMP_TOLERANCE_SECONDS = 300; // 5 min replay-attack window

    public function handle(Request $request): JsonResponse
    {
        $secret = (string) config('services.resend.webhook_secret');
        if ($secret === '') {
            Log::warning('Resend webhook called but RESEND_WEBHOOK_SECRET is not configured');
            return response()->json(['error' => 'not_configured'], 503);
        }

        // 1. Verify Svix signature (Resend uses Svix as its webhook signing layer)
        if (! $this->verifySvixSignature($request, $secret)) {
            Log::warning('Resend webhook signature invalid', ['ip' => $request->ip()]);
            return response()->json(['error' => 'invalid_signature'], 401);
        }

        // 2. Parse payload
        $payload = $request->all();
        $type    = $payload['type'] ?? null;
        $data    = $payload['data'] ?? [];

        if (! $type) {
            return response()->json(['error' => 'missing_type'], 400);
        }

        // 3. Extract recipient — Resend "to" is an array
        $email = strtolower((string) ($data['to'][0] ?? $data['email'] ?? ''));
        if (! $email) {
            // Sent-without-recipient event (rare) — accept but no-op
            return response()->json(['ok' => true, 'handled' => 0]);
        }

        $sub = Subscriber::where('email', $email)->first();
        if (! $sub) {
            // Not our subscriber — Resend may also deliver to non-newsletter recipients
            return response()->json(['ok' => true, 'handled' => 0]);
        }

        // 4. Persist webhook meta for debugging on every event
        $sub->webhook_meta = [
            'last_event'   => $type,
            'last_at'      => now()->toIso8601String(),
            'message_id'   => $data['email_id'] ?? null,
            'reason'       => $data['reason'] ?? ($data['bounce']['message'] ?? null),
        ];

        // 5. Dispatch by event type
        switch ($type) {
            case 'email.bounced':
                $bounceType = strtolower((string) ($data['bounce']['type'] ?? 'hard'));
                $sub->bounce_count = ($sub->bounce_count ?? 0) + 1;
                if ($bounceType === 'hard' || $sub->bounce_count >= 5) {
                    $sub->bounced_at = $sub->bounced_at ?: now();
                }
                $sub->save();
                Log::info('Resend bounce', ['email' => $email, 'type' => $bounceType]);
                break;

            case 'email.complained':
                // Recipient marked us as spam — immediate unsubscribe + flag
                $sub->complaint_at = now();
                $sub->unsubscribed_at = $sub->unsubscribed_at ?: now();
                $sub->unsubscribe_reason = 'spam_complaint';
                $sub->save();
                Log::info('Resend spam complaint — unsubscribed', ['email' => $email]);
                break;

            case 'email.opened':
                $sub->open_count = ($sub->open_count ?? 0) + 1;
                $sub->last_open_at = now();
                $sub->save();
                break;

            case 'email.clicked':
                $sub->click_count = ($sub->click_count ?? 0) + 1;
                $sub->last_click_at = now();
                $sub->save();
                break;

            case 'email.sent':
            case 'email.delivered':
            case 'email.delivery_delayed':
                // Already tracked via last_sent_at on dispatch — just refresh meta
                $sub->save();
                break;

            default:
                $sub->save();
                Log::info('Resend webhook unknown event', ['type' => $type, 'email' => $email]);
        }

        return response()->json(['ok' => true, 'handled' => 1, 'event' => $type]);
    }

    /**
     * Verify Resend's Svix-format webhook signature.
     *
     * Svix signs the string "{svix-id}.{svix-timestamp}.{raw-body}" with HMAC-SHA256
     * using the secret (which is base64-encoded with "whsec_" prefix).
     * The svix-signature header contains one or more "v1,base64sig" entries,
     * separated by space.
     */
    private function verifySvixSignature(Request $request, string $secret): bool
    {
        $svixId  = $request->header('svix-id');
        $svixTs  = $request->header('svix-timestamp');
        $svixSig = $request->header('svix-signature');

        if (! $svixId || ! $svixTs || ! $svixSig) return false;

        // Reject replays older than 5 min
        if (abs(time() - (int) $svixTs) > self::TIMESTAMP_TOLERANCE_SECONDS) return false;

        // Strip "whsec_" prefix and base64-decode the secret
        $secretRaw = $secret;
        if (str_starts_with($secretRaw, 'whsec_')) {
            $secretRaw = substr($secretRaw, 6);
        }
        $secretBytes = base64_decode($secretRaw, true);
        if ($secretBytes === false) return false;

        // Compute expected HMAC
        $payload = $svixId . '.' . $svixTs . '.' . $request->getContent();
        $expected = base64_encode(hash_hmac('sha256', $payload, $secretBytes, true));

        // Header may contain multiple sig entries: "v1,sig1 v1,sig2"
        foreach (explode(' ', $svixSig) as $sigEntry) {
            $parts = explode(',', $sigEntry, 2);
            if (count($parts) !== 2) continue;
            if (hash_equals($expected, $parts[1])) {
                return true;
            }
        }
        return false;
    }
}

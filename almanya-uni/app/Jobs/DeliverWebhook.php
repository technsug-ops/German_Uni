<?php

namespace App\Jobs;

use App\Models\WebhookDelivery;
use App\Models\WebhookSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class DeliverWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 4;

    public function backoff(): array
    {
        return [10, 60, 300, 1800];
    }

    public function __construct(
        public int $subscriptionId,
        public string $event,
        public array $payload,
    ) {}

    public function handle(): void
    {
        $sub = WebhookSubscription::find($this->subscriptionId);
        if (!$sub || !$sub->is_active) {
            return;
        }

        $body = json_encode([
            'event' => $this->event,
            'occurred_at' => now()->toIso8601String(),
            'data' => $this->payload,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $signature = hash_hmac('sha256', $body, $sub->secret);
        $started = microtime(true);

        $delivery = WebhookDelivery::create([
            'webhook_subscription_id' => $sub->id,
            'event' => $this->event,
            'payload' => $this->payload,
            'attempts' => $this->attempts(),
            'created_at' => now(),
        ]);

        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'X-AlmanyaUni-Event' => $this->event,
                    'X-AlmanyaUni-Signature' => $signature,
                    'Content-Type' => 'application/json',
                ])
                ->withBody($body, 'application/json')
                ->post($sub->url);

            $duration = (int) ((microtime(true) - $started) * 1000);

            $delivery->update([
                'status_code' => $response->status(),
                'response_body' => substr((string) $response->body(), 0, 1000),
                'duration_ms' => $duration,
                'succeeded' => $response->successful(),
                'delivered_at' => now(),
            ]);

            if ($response->successful()) {
                $sub->update([
                    'last_success_at' => now(),
                    'failure_count' => 0,
                    'last_failure_reason' => null,
                ]);
                return;
            }

            $reason = 'HTTP ' . $response->status();
            $this->markFailed($sub, $reason);

            throw new \RuntimeException($reason);
        } catch (\Throwable $e) {
            $this->markFailed($sub, substr($e->getMessage(), 0, 250));
            $delivery->update([
                'response_body' => substr($e->getMessage(), 0, 1000),
                'duration_ms' => (int) ((microtime(true) - $started) * 1000),
                'succeeded' => false,
            ]);
            throw $e;
        }
    }

    private function markFailed(WebhookSubscription $sub, string $reason): void
    {
        $sub->forceFill([
            'last_failure_at' => now(),
            'last_failure_reason' => $reason,
            'failure_count' => $sub->failure_count + 1,
        ])->save();

        if ($sub->failure_count >= 20) {
            $sub->update(['is_active' => false]);
        }
    }
}

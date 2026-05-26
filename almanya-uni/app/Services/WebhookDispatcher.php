<?php

namespace App\Services;

use App\Jobs\DeliverWebhook;
use App\Models\WebhookSubscription;

class WebhookDispatcher
{
    public function dispatch(string $event, array $payload): int
    {
        $subscriptions = WebhookSubscription::query()
            ->where('is_active', true)
            ->get()
            ->filter(fn ($s) => $s->subscribesTo($event));

        foreach ($subscriptions as $sub) {
            DeliverWebhook::dispatch($sub->id, $event, $payload);
        }

        return $subscriptions->count();
    }
}

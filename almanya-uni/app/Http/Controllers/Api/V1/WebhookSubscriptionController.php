<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\DeliverWebhook;
use App\Models\ApiClient;
use App\Models\WebhookSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebhookSubscriptionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $client = $this->client($request);

        return response()->json([
            'data' => $client->webhookSubscriptions()->get()->map(fn ($s) => $this->toArray($s)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'required|url|max:255',
            'events' => 'required|array|min:1',
            'events.*' => 'in:' . implode(',', array_merge(WebhookSubscription::AVAILABLE_EVENTS, ['*'])),
        ]);

        $client = $this->client($request);
        $secret = Str::random(48);

        $sub = $client->webhookSubscriptions()->create([
            'url' => $validated['url'],
            'events' => $validated['events'],
            'secret' => $secret,
            'is_active' => true,
        ]);

        return response()->json([
            'data' => $this->toArray($sub),
            'secret' => $secret,
            'message' => 'Secret yalnızca burada gösterilir. HMAC-SHA256 imza için sakla.',
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $client = $this->client($request);
        $sub = $client->webhookSubscriptions()->findOrFail($id);

        return response()->json(['data' => $this->toArray($sub)]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'sometimes|url|max:255',
            'events' => 'sometimes|array|min:1',
            'events.*' => 'in:' . implode(',', array_merge(WebhookSubscription::AVAILABLE_EVENTS, ['*'])),
            'is_active' => 'sometimes|boolean',
        ]);

        $client = $this->client($request);
        $sub = $client->webhookSubscriptions()->findOrFail($id);
        $sub->update($validated);

        return response()->json(['data' => $this->toArray($sub)]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $client = $this->client($request);
        $sub = $client->webhookSubscriptions()->findOrFail($id);
        $sub->delete();

        return response()->json(null, 204);
    }

    public function test(Request $request, int $id): JsonResponse
    {
        $client = $this->client($request);
        $sub = $client->webhookSubscriptions()->findOrFail($id);

        DeliverWebhook::dispatch($sub->id, 'ping', [
            'message' => 'Test webhook from AlmanyaUni',
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json(['message' => 'Test event kuyruğa alındı.']);
    }

    private function client(Request $request): ApiClient
    {
        $tokenable = $request->user();
        abort_unless($tokenable instanceof ApiClient, 401, 'Bu uç nokta yalnızca API client token ile çağrılabilir.');
        return $tokenable;
    }

    private function toArray(WebhookSubscription $s): array
    {
        return [
            'id' => $s->id,
            'url' => $s->url,
            'events' => $s->events,
            'is_active' => $s->is_active,
            'failure_count' => $s->failure_count,
            'last_success_at' => $s->last_success_at?->toIso8601String(),
            'last_failure_at' => $s->last_failure_at?->toIso8601String(),
            'last_failure_reason' => $s->last_failure_reason,
            'created_at' => $s->created_at->toIso8601String(),
        ];
    }
}

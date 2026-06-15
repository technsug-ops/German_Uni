<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Tarayıcı web-push aboneliği (şehir bazlı etkinlik bildirimi).
 * Frontend PushManager.subscribe() çıktısını + city_id'yi POST eder.
 */
class PushSubscriptionController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint'    => ['required', 'string', 'max:1000'],
            'keys.p256dh' => ['required', 'string', 'max:255'],
            'keys.auth'   => ['required', 'string', 'max:255'],
            'city_id'     => ['required', 'integer', 'exists:cities,id'],
        ]);

        $sub = PushSubscription::updateOrCreate(
            [
                'endpoint_hash' => PushSubscription::hashEndpoint($data['endpoint']),
                'city_id'       => $data['city_id'],
            ],
            [
                'user_id'  => $request->user()?->id,
                'endpoint' => $data['endpoint'],
                'p256dh'   => $data['keys']['p256dh'],
                'auth'     => $data['keys']['auth'],
                'locale'   => app()->getLocale(),
            ]
        );

        return response()->json(['ok' => true, 'id' => $sub->id]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string', 'max:1000'],
            'city_id'  => ['nullable', 'integer'],
        ]);

        $q = PushSubscription::where('endpoint_hash', PushSubscription::hashEndpoint($data['endpoint']));
        if (! empty($data['city_id'])) {
            $q->where('city_id', $data['city_id']);
        }
        $deleted = $q->delete();

        return response()->json(['ok' => true, 'deleted' => $deleted]);
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\EventAlertConfirmation;
use App\Models\City;
use App\Models\EventCitySubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Şehir bazlı etkinlik bildirimi aboneliği (double opt-in).
 * Akış newsletter ile aynı: subscribe → doğrulama maili → confirm → haftalık digest.
 * Senkron Mail::send (sunucuda queue worker yok).
 */
class EventAlertController extends Controller
{
    public function subscribe(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'email'        => ['required', 'email:rfc', 'max:191'],
            'city_id'      => ['required', 'integer', 'exists:cities,id'],
            'source'       => ['nullable', 'string', 'max:50'],
            'website'      => ['nullable', 'max:0'],          // 🍯 honeypot
            'gdpr_consent' => ['accepted'],
        ], [
            'gdpr_consent.accepted' => __('You must accept the privacy terms.'),
            'website.max'           => __('Spam detected.'),
        ]);

        try {
            $email = strtolower(trim($data['email']));
            $city  = City::findOrFail($data['city_id']);

            $existing = EventCitySubscription::where('email', $email)->where('city_id', $city->id)->first();

            if ($existing && $existing->is_confirmed) {
                return $this->respond($request, true, __('You are already subscribed to :city events.', ['city' => $city->name]), 'already');
            }

            $sub = EventCitySubscription::updateOrCreate(
                ['email' => $email, 'city_id' => $city->id],
                [
                    'user_id'         => $request->user()?->id,
                    'locale'          => app()->getLocale(),
                    'source'          => $data['source'] ?? 'web',
                    'confirmed_at'    => null,
                    'unsubscribed_at' => null,
                    'ip_address'      => $request->ip(),
                ]
            );
            $sub->regenerateTokens();
            $sub->save();

            try {
                Mail::to($sub->email)->send(new EventAlertConfirmation($sub));
            } catch (\Throwable $e) {
                Log::error('Event alert mail failed', ['email' => $sub->email, 'err' => $e->getMessage()]);

                return $this->respond($request, false, __('Could not send the email. Please try again shortly.'), 'mail_failed');
            }

            return $this->respond($request, true, __('Almost done! Check your inbox to confirm your subscription (also the spam folder).'), 'pending');
        } catch (\Throwable $e) {
            Log::error('Event alert subscribe failed', ['err' => $e->getMessage()]);

            return $this->respond($request, false, __('Something went wrong. Please try again shortly.'), 'error');
        }
    }

    public function confirm(string $token): View
    {
        $sub = EventCitySubscription::with('city')->where('confirm_token', $token)->first();

        if (! $sub) {
            return view('newsletter.result', [
                'success' => false,
                'title'   => __('Invalid confirmation link'),
                'message' => __('This link may have already been used, expired, or is broken. Try subscribing again.'),
            ]);
        }

        if ($sub->unsubscribed_at) {
            $sub->unsubscribed_at = null;
        }
        if (! $sub->confirmed_at) {
            $sub->confirmed_at = now();
        }
        $sub->save();

        return view('newsletter.result', [
            'success' => true,
            'title'   => __('Subscription confirmed! 🎉'),
            'message' => __('You will now get event alerts for :city at :email.', ['city' => $sub->city?->name, 'email' => $sub->email]),
        ]);
    }

    public function unsubscribe(Request $request, string $token): View
    {
        $sub = EventCitySubscription::with('city')->where('unsubscribe_token', $token)->first();

        if (! $sub) {
            return view('newsletter.result', [
                'success' => false,
                'title'   => __('Invalid link'),
                'message' => __('This unsubscribe link was not found or is broken.'),
            ]);
        }

        if (! $sub->unsubscribed_at) {
            $sub->unsubscribed_at = now();
            $sub->save();
        }

        return view('newsletter.result', [
            'success' => true,
            'title'   => __('Unsubscribed'),
            'message' => __('We will no longer send :city event alerts to :email.', ['city' => $sub->city?->name, 'email' => $sub->email]),
        ]);
    }

    private function respond(Request $request, bool $success, string $message, string $status, int $code = 200): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => $success, 'status' => $status, 'message' => $message], $code);
        }

        return back()->with($success ? 'event_alert_success' : 'event_alert_error', $message);
    }
}

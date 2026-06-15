<?php

namespace App\Console\Commands;

use App\Mail\EventCityDigestMail;
use App\Models\Event;
use App\Models\EventCitySubscription;
use App\Models\PushSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Minishlink\WebPush\Subscription as WebPushSubscription;
use Minishlink\WebPush\WebPush;

/**
 * Şehir abonelerine haftalık etkinlik digest'i gönderir.
 * İlk gönderimde yaklaşan etkinlikler (30 gün); sonraki gönderimlerde yalnızca
 * son bildirimden BERİ eklenen etkinlikler → tekrar yok. Boş içerikte mail gönderilmez.
 * Senkron Mail::send (sunucuda queue worker yok).
 */
class NotifyEventSubscribers extends Command
{
    protected $signature = 'events:notify-subscribers
        {--email= : Sadece bu e-posta (test)}
        {--dry : Mail gönderme, sadece say}
        {--limit=500 : Max abone batch (rate-limit koruması)}';

    protected $description = 'Şehir abonelerine yeni eklenen yaklaşan etkinliklerin haftalık digest\'ini gönderir';

    private const WINDOW_DAYS = 30;
    private const MAX_EVENTS = 15;

    public function handle(): int
    {
        $query = EventCitySubscription::active()->with('city');

        if ($email = $this->option('email')) {
            $query->where('email', strtolower(trim($email)));
        }

        $subs = $query->limit((int) $this->option('limit'))->get();
        $this->info("Hedef abone: {$subs->count()}");

        $sent = 0;
        $skipped = 0;
        $originalLocale = app()->getLocale();

        foreach ($subs as $sub) {
            if (! $sub->city) {
                $skipped++;
                continue;
            }

            $events = $this->eventsFor($sub->city_id, $sub->last_notified_at);

            if ($events->isEmpty()) {
                $skipped++;
                continue;
            }

            if ($this->option('dry')) {
                $this->line(sprintf('  [dry][mail] %s → %s: %d etkinlik', $sub->email, $sub->city->name, $events->count()));
                $sent++;
                continue;
            }

            // Aboneye kendi dilinde gönder (email blade __() bu locale'i kullanır).
            app()->setLocale($sub->locale ?: 'tr');

            try {
                Mail::to($sub->email)->send(new EventCityDigestMail($sub, $sub->city, $events));
                $sub->update(['last_notified_at' => now()]);
                $sent++;
            } catch (\Throwable $e) {
                Log::error('Event digest mail failed', ['email' => $sub->email, 'err' => $e->getMessage()]);
                $skipped++;
            }
        }

        $this->info("E-posta bitti: {$sent} gönderildi, {$skipped} atlandı.");

        // Web push kanalı
        [$pSent, $pSkipped] = $this->sendPushDigests($originalLocale);

        app()->setLocale($originalLocale);

        $this->info("Push bitti: {$pSent} gönderildi, {$pSkipped} atlandı.");

        return self::SUCCESS;
    }

    /**
     * Tarayıcı push abonelerine digest. @return array{0:int,1:int} [sent, skipped]
     */
    private function sendPushDigests(string $fallbackLocale): array
    {
        $public  = config('services.webpush.public_key');
        $private = config('services.webpush.private_key');
        if (! $public || ! $private) {
            $this->warn('VAPID anahtarları yok — push atlandı.');

            return [0, 0];
        }

        $query = PushSubscription::with('city');
        if ($email = $this->option('email')) {
            // test modunda push'u atla (email'e özel)
            return [0, 0];
        }
        $subs = $query->limit((int) $this->option('limit'))->get();

        $sent = 0;
        $skipped = 0;

        $webPush = new WebPush(['VAPID' => [
            'subject'    => config('services.webpush.subject'),
            'publicKey'  => $public,
            'privateKey' => $private,
        ]]);

        foreach ($subs as $psub) {
            if (! $psub->city) {
                $skipped++;
                continue;
            }
            $events = $this->eventsFor($psub->city_id, $psub->last_notified_at);
            if ($events->isEmpty()) {
                $skipped++;
                continue;
            }

            if ($this->option('dry')) {
                $this->line(sprintf('  [dry][push] #%d → %s: %d etkinlik', $psub->id, $psub->city->name, $events->count()));
                $sent++;
                continue;
            }

            app()->setLocale($psub->locale ?: $fallbackLocale);
            $payload = json_encode([
                'title' => __(':count new events in :city this week 🎵', ['count' => $events->count(), 'city' => $psub->city->name]),
                'body'  => $events->take(3)->map(fn ($e) => $e->title)->implode(' · '),
                'url'   => '/' . app()->getLocale() . '/events?category=culture',
                'icon'  => '/img/icons/icon-192.png',
                'tag'   => 'event-alert-' . $psub->city_id,
            ], JSON_UNESCAPED_UNICODE);

            try {
                $report = $webPush->sendOneNotification(
                    WebPushSubscription::create([
                        'endpoint'  => $psub->endpoint,
                        'publicKey' => $psub->p256dh,
                        'authToken' => $psub->auth,
                    ]),
                    $payload
                );

                if ($report->isSuccess()) {
                    $psub->update(['last_notified_at' => now()]);
                    $sent++;
                } elseif ($report->isSubscriptionExpired()) {
                    // 404/410 → endpoint ölü, temizle
                    $psub->delete();
                    $skipped++;
                } else {
                    $skipped++;
                }
            } catch (\Throwable $e) {
                Log::error('Event push failed', ['sub' => $psub->id, 'err' => $e->getMessage()]);
                $skipped++;
            }
        }

        return [$sent, $skipped];
    }

    /**
     * İlk digest → yaklaşan etkinlikler; sonraki → son bildirimden beri eklenenler.
     */
    private function eventsFor(int $cityId, ?Carbon $lastNotifiedAt)
    {
        $q = Event::active()
            ->where('city_id', $cityId)
            ->where('starts_at', '>=', now())
            ->where('starts_at', '<=', now()->addDays(self::WINDOW_DAYS));

        if ($lastNotifiedAt) {
            // Yalnızca son bildirimden beri eklenen yeni etkinlikler (tekrar yok).
            $q->where('created_at', '>', $lastNotifiedAt);
        }

        return $q->orderBy('starts_at')->take(self::MAX_EVENTS)->get();
    }
}

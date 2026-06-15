<?php

namespace App\Console\Commands;

use App\Mail\EventCityDigestMail;
use App\Models\Event;
use App\Models\EventCitySubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

            $events = $this->eventsFor($sub);

            if ($events->isEmpty()) {
                $skipped++;
                continue;
            }

            if ($this->option('dry')) {
                $this->line(sprintf('  [dry] %s → %s: %d etkinlik', $sub->email, $sub->city->name, $events->count()));
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

        app()->setLocale($originalLocale);

        $this->info("Bitti: {$sent} gönderildi, {$skipped} atlandı.");

        return self::SUCCESS;
    }

    /**
     * İlk digest → yaklaşan etkinlikler; sonraki → son bildirimden beri eklenenler.
     */
    private function eventsFor(EventCitySubscription $sub)
    {
        $q = Event::active()
            ->where('city_id', $sub->city_id)
            ->where('starts_at', '>=', now())
            ->where('starts_at', '<=', now()->addDays(self::WINDOW_DAYS));

        if ($sub->last_notified_at) {
            // Yalnızca son bildirimden beri eklenen yeni etkinlikler (tekrar yok).
            $q->where('created_at', '>', $sub->last_notified_at);
        }

        return $q->orderBy('starts_at')->take(self::MAX_EVENTS)->get();
    }
}

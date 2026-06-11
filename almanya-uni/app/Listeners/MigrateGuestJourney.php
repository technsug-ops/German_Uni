<?php

namespace App\Listeners;

use App\Models\ApplicationTracker;
use App\Models\User;
use Illuminate\Auth\Events\Login;

/**
 * Misafir, Başvuru Yolculuğu'nda (Application Tracker) session bazlı ilerleme
 * kaydeder; giriş/kayıt olunca bu ilerleme şimdiye kadar KAYBOLUYORDU. Bu
 * listener, Login anında session'daki journey state'ini kullanıcının kalıcı
 * DB tracker'ına BİRLEŞTİRİR (union — DB'deki ilerlemeyi ezmez), sonra session'ı
 * temizler. Anonim → üye köprüsü (engagement).
 */
class MigrateGuestJourney
{
    private const DEGREES = ['bachelor', 'master', 'phd'];

    public function handle(Login $event): void
    {
        $session = session();

        $steps     = (array) $session->get('journey.steps', []);
        $intake    = $session->get('journey.target_intake');
        $degree    = $session->get('journey.target_degree');
        $startedAt = $session->get('journey.started_at');

        // Misafir oturumunda hiç journey verisi yoksa dokunma
        if (empty($steps) && empty($intake) && empty($degree)) {
            return;
        }

        $user = $event->user;
        if (! $user instanceof User) {
            return;
        }

        $tracker = $user->applicationTracker ?? new ApplicationTracker([
            'user_id'         => $user->id,
            'started_at'      => $startedAt ?: now(),
            'steps_completed' => [],
        ]);

        // Adımları BİRLEŞTİR (geçerli step key'leriyle sınırla, union, sırayı koru)
        $validKeys = collect(ApplicationTracker::STEPS)->pluck('key')->all();
        $merged = array_values(array_unique(array_merge(
            (array) $tracker->steps_completed,
            array_values(array_intersect($steps, $validKeys))
        )));
        $tracker->steps_completed = $merged;

        // Hedefleri SADECE DB'de boşsa doldur — kullanıcının kendi kaydını ezme
        if (empty($tracker->target_intake) && ! empty($intake)) {
            $tracker->target_intake = (string) $intake;
        }
        if (empty($tracker->target_degree) && in_array($degree, self::DEGREES, true)) {
            $tracker->target_degree = $degree;
        }
        if (empty($tracker->started_at)) {
            $tracker->started_at = $startedAt ?: now();
        }
        $tracker->last_activity_at = now();
        $tracker->save();

        // Artık DB'de — session journey state'ini temizle
        $session->forget(['journey.steps', 'journey.target_intake', 'journey.target_degree', 'journey.started_at']);
    }
}

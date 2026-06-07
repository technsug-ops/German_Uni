<?php

namespace App\Console\Commands;

use App\Mail\JourneyReminder;
use App\Models\ApplicationTracker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class JourneyRemindInactive extends Command
{
    protected $signature = 'journey:remind-inactive
        {--days=14 : Trigger when last_activity_at is older than this many days}
        {--send : Actually send emails (otherwise dry-run preview)}
        {--throttle=200 : Milliseconds between sends to respect SMTP rate limits}';

    protected $description = 'Email users who started but stalled their Germany journey 14+ days ago.';

    public function handle(): int
    {
        $days     = (int) $this->option('days');
        $send     = (bool) $this->option('send');
        $throttle = (int) $this->option('throttle');

        $cutoff = now()->subDays($days);

        // Reachable trackers:
        //   - user opted into reminders
        //   - last_activity_at older than N days (or never)
        //   - not 100% complete
        $trackers = ApplicationTracker::query()
            ->where('email_reminders', true)
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('last_activity_at')->orWhere('last_activity_at', '<', $cutoff);
            })
            ->with('user')
            ->get()
            ->filter(function ($t) {
                $total = count(ApplicationTracker::STEPS);
                return $t->user && $t->completedCount() < $total;
            });

        $this->info("Found {$trackers->count()} stalled journey trackers (last_activity > {$days} days)");

        if (! $send) {
            $this->warn('DRY RUN — no emails will be sent. Use --send to dispatch.');
            foreach ($trackers->take(10) as $t) {
                $this->line(sprintf('  · %s — %d/%d done — last active %s',
                    $t->user->email,
                    $t->completedCount(),
                    count(ApplicationTracker::STEPS),
                    $t->last_activity_at?->diffForHumans() ?? 'never',
                ));
            }
            if ($trackers->count() > 10) $this->line('  · ...');
            return self::SUCCESS;
        }

        $sent = 0; $failed = 0;
        $bar = $this->output->createProgressBar($trackers->count());
        $bar->start();

        foreach ($trackers as $tracker) {
            try {
                Mail::to($tracker->user->email)->send(new JourneyReminder($tracker->user, $tracker));
                $sent++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("  {$tracker->user->email}: " . substr($e->getMessage(), 0, 100));
                $failed++;
            }
            if ($throttle > 0) usleep($throttle * 1000);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Queued: {$sent}");
        if ($failed > 0) $this->error("❌ Failed: {$failed}");
        return self::SUCCESS;
    }
}

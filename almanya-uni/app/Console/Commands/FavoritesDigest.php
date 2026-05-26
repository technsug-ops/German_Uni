<?php

namespace App\Console\Commands;

use App\Mail\FavoritesDigestMail;
use App\Models\Program;
use App\Models\University;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class FavoritesDigest extends Command
{
    protected $signature = 'favorites:digest
                            {--user= : Sadece bu user_id (test)}
                            {--dry : Mail gönderme, sadece say}
                            {--limit=200 : Max user batch (rate-limit koruması)}';

    protected $description = 'Favorilerine bağlı haftalık özet: yeni program, yaklaşan deadline, ilgili blog (free tier + premium üye)';

    public function handle(): int
    {
        $query = User::query()
            ->whereNotNull('email_verified_at')
            ->whereHas('favorites');

        if ($uid = $this->option('user')) {
            $query->where('id', $uid);
        }

        $users = $query->limit((int) $this->option('limit'))->get();
        $this->info("Hedef user: {$users->count()}");

        $sent = 0;
        $skipped = 0;
        foreach ($users as $user) {
            $payload = $this->buildPayload($user);

            if (empty($payload['favorites'])) {
                $skipped++;
                continue;
            }

            // Boş içerik = spam, gönderme
            $hasUpdates = ! empty($payload['new_programs'])
                || ! empty($payload['upcoming_deadlines'])
                || ! empty($payload['related_blogs']);

            if (! $hasUpdates && $payload['favorites_count'] < 3) {
                $skipped++;
                continue;
            }

            if (! $this->option('dry')) {
                try {
                    Mail::to($user->email)->send(new FavoritesDigestMail($user, $payload));
                    $sent++;
                } catch (\Throwable $e) {
                    $this->warn("FAIL {$user->email}: " . $e->getMessage());
                }
            } else {
                $sent++;
                $this->line("DRY {$user->email} | fav={$payload['favorites_count']} | new={$payload['new_count']} | dl=" . count($payload['upcoming_deadlines']));
            }
        }

        $this->info("Gönderildi: $sent | Atlandı: $skipped");
        return self::SUCCESS;
    }

    private function buildPayload(User $user): array
    {
        $favorites = $user->favorites()->with('favoriteable')->get();
        $uniIds = $favorites->where('favoriteable_type', University::class)->pluck('favoriteable_id');
        $programIds = $favorites->where('favoriteable_type', Program::class)->pluck('favoriteable_id');

        // Son 14 günde favori üniversitede eklenen yeni programlar
        $newPrograms = [];
        if ($uniIds->isNotEmpty()) {
            $newPrograms = Program::whereIn('university_id', $uniIds)
                ->where('created_at', '>=', now()->subDays(14))
                ->with('university:id,slug,name_de,name_en')
                ->limit(5)
                ->get(['id', 'slug', 'name_de', 'name_en', 'degree', 'university_id'])
                ->toArray();
        }

        // Favori program deadline'ı önümüzdeki 30 günde (winter veya summer)
        $upcomingDeadlines = [];
        if ($programIds->isNotEmpty()) {
            $upcomingDeadlines = Program::whereIn('id', $programIds)
                ->where(function ($q) {
                    $q->whereBetween('application_deadline_winter', [now(), now()->addDays(30)])
                      ->orWhereBetween('application_deadline_summer', [now(), now()->addDays(30)]);
                })
                ->limit(5)
                ->get(['id', 'slug', 'name_de', 'name_en', 'application_deadline_winter', 'application_deadline_summer'])
                ->map(function ($p) {
                    $w = $p->application_deadline_winter;
                    $s = $p->application_deadline_summer;
                    $next = collect([$w, $s])
                        ->filter(fn ($d) => $d && $d >= now())
                        ->sort()
                        ->first();
                    return [
                        'id' => $p->id,
                        'slug' => $p->slug,
                        'name_de' => $p->name_de,
                        'name_en' => $p->name_en,
                        'deadline' => $next,
                        'semester' => $next === $w ? 'winter' : 'summer',
                    ];
                })
                ->sortBy('deadline')
                ->values()
                ->toArray();
        }

        // Favori üni veya program varsa son 7 günde yayınlanan ilgili blog post
        $relatedBlogs = \App\Models\Post::whereNotNull('published_at')
            ->where('published_at', '>=', now()->subDays(7))
            ->orderByDesc('published_at')
            ->limit(3)
            ->get(['id', 'slug', 'title', 'excerpt', 'published_at'])
            ->toArray();

        return [
            'favorites' => $favorites->toArray(),
            'favorites_count' => $favorites->count(),
            'new_programs' => $newPrograms,
            'new_count' => count($newPrograms),
            'upcoming_deadlines' => $upcomingDeadlines,
            'related_blogs' => $relatedBlogs,
        ];
    }
}

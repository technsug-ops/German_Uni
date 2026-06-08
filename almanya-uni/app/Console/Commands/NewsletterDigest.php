<?php

namespace App\Console\Commands;

use App\Mail\WeeklyDigest;
use App\Models\City;
use App\Models\Post;
use App\Models\Program;
use App\Models\Scholarship;
use App\Models\Subscriber;
use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NewsletterDigest extends Command
{
    protected $signature = 'newsletter:digest
        {--days=7 : Son N gün penceresi (blog/haber/enrichment)}
        {--send : Gerçekten gönder (yoksa dry-run)}
        {--limit=12 : Maksimum kart sayısı}
        {--only=* : Sadece bu email\'lere gönder}
        {--force : Bu hafta zaten gönderilenleri de gönder (test/yeniden)}
        {--throttle=100 : Aboneler arası bekleme (ms) — SMTP rate limit için}';

    protected $description = 'Haftalık Almanya Rehberi — yeni blog + haber + öne çıkan burs + yaklaşan deadline + keşif, abonelere';

    public function handle(): int
    {
        // Digest tek seferde üretilip tüm abonelere gider; birincil kitle TR →
        // ad/açıklama/route'lar TR. (EN/DE abone segmentasyonu sonraki faz.)
        app()->setLocale('tr');

        $days = (int) $this->option('days');
        $limit = (int) $this->option('limit');
        $since = now()->subDays($days);

        $items = collect();

        // ── 📝 Yeni blog yazıları (son N gün) ──
        Post::published()->where('type', '!=', 'news')->where('locale', 'tr')
            ->where('published_at', '>=', $since)
            ->orderByDesc('published_at')->take(4)
            ->get(['slug', 'title', 'excerpt', 'featured_image', 'published_at'])
            ->each(fn ($p) => $items->push([
                'title' => $p->title,
                'category' => '📝 Blog',
                'category_color' => '#2563eb',
                'url' => route('blog.show', $p->slug),
                'image' => $p->featured_image,
                'description' => (string) $p->excerpt,
                'sort' => 1,
            ]));

        // ── 📰 Almanya'dan haberler (son N gün) ──
        Post::published()->where('type', 'news')->where('locale', 'tr')
            ->where('published_at', '>=', $since)
            ->orderByDesc('published_at')->take(3)
            ->get(['slug', 'title', 'excerpt', 'featured_image', 'published_at'])
            ->each(fn ($p) => $items->push([
                'title' => $p->title,
                'category' => '📰 Haber',
                'category_color' => '#dc2626',
                'url' => route('news.show', $p->slug),
                'image' => $p->featured_image,
                'description' => (string) $p->excerpt,
                'sort' => 2,
            ]));

        // ── 🎓 Öne çıkan burs (her hafta döner — weekOfYear offset) ──
        $scholarTotal = Scholarship::whereNull('removed_at')->where('is_daad', 1)->count();
        if ($scholarTotal > 0) {
            $offset = (now()->weekOfYear * 2) % $scholarTotal;
            Scholarship::whereNull('removed_at')->where('is_daad', 1)
                ->orderBy('id')->skip($offset)->take(2)->get()
                ->each(fn ($s) => $items->push([
                    'title' => $s->name,
                    'category' => '🎓 Burs',
                    'category_color' => '#d97706',
                    'url' => route('scholarships.show', $s->slug),
                    'image' => null,
                    'description' => (string) ($s->introductionText('tr') ?: $s->programmname_tr ?: $s->programmname_en ?: __('DAAD scholarship for international students.')),
                    'sort' => 3,
                ]));
        }

        // ── 🔎 Haftanın keşfi — son N günde enrich edilen şehir + üniversite ──
        City::whereNotNull('content_blocks')->where('last_enriched_at', '>=', $since)
            ->orderByDesc('last_enriched_at')->take(3)
            ->get(['slug', 'name_de', 'image_url', 'content_blocks'])
            ->each(fn ($c) => $items->push([
                'title' => $c->name_de . ' — Şehir Rehberi',
                'category' => '🏙️ Şehir',
                'category_color' => '#0891b2',
                'url' => route('cities.show', $c->slug),
                'image' => $c->image_url,
                'description' => \App\Support\Seo::descriptionFromBlocks($c->content_blocks, "{$c->name_de} şehri rehberi."),
                'sort' => 4,
            ]));

        University::where('is_active', 1)->whereNotNull('content_blocks')
            ->where('last_enriched_at', '>=', $since)
            ->orderByDesc('last_enriched_at')->take(3)
            ->get(['slug', 'name_de', 'image_url', 'content_blocks'])
            ->each(fn ($u) => $items->push([
                'title' => $u->name_de,
                'category' => '🎓 Üniversite',
                'category_color' => '#1e40af',
                'url' => route('universities.show', $u->slug),
                'image' => $u->image_url,
                'description' => \App\Support\Seo::descriptionFromBlocks($u->content_blocks, "{$u->name_de} hakkında rehber."),
                'sort' => 4,
            ]));

        // Bölüm sırasına göre (blog→haber→burs→keşif), sonra kırp
        $items = $items->sortBy('sort')->take($limit)->values()->toArray();

        // ── ⏰ Yaklaşan başvuru deadline'ları (önümüzdeki 21 gün) ──
        $today = now()->toDateString();
        $until = now()->addDays(21)->toDateString();
        $deadlines = Program::where('is_active', 1)
            ->with('university:id,name_de,slug')
            ->where(function ($q) use ($today, $until) {
                $q->whereBetween('application_deadline_winter', [$today, $until])
                    ->orWhereBetween('application_deadline_summer', [$today, $until]);
            })
            ->orderByRaw('LEAST(COALESCE(application_deadline_winter, "9999-12-31"), COALESCE(application_deadline_summer, "9999-12-31"))')
            ->take(5)
            ->get(['id', 'slug', 'name_de', 'university_id', 'application_deadline_winter', 'application_deadline_summer'])
            ->map(function ($p) use ($today) {
                $cands = array_filter([
                    (string) $p->application_deadline_winter,
                    (string) $p->application_deadline_summer,
                ], fn ($d) => $d !== '' && substr($d, 0, 10) >= $today);
                return [
                    'program' => $p->name_de,
                    'university' => $p->university?->name_de,
                    'date' => $cands ? substr(min($cands), 0, 10) : null,
                    'url' => route('programs.show', $p->slug),
                ];
            })
            ->filter(fn ($d) => $d['date'] !== null)
            ->values()->toArray();

        // İçerik yoksa (hiç kart + hiç deadline) gönderme
        if (empty($items) && empty($deadlines)) {
            $this->warn("Son {$days} gün için içerik yok — digest gönderilmedi.");
            return self::SUCCESS;
        }

        $stats = [
            'total'      => count($items),
            'blog'       => collect($items)->where('category', '📝 Blog')->count(),
            'news'       => collect($items)->where('category', '📰 Haber')->count(),
            'scholarships' => collect($items)->where('category', '🎓 Burs')->count(),
            'deadlines'  => count($deadlines),
        ];

        $this->info("📦 {$stats['total']} kart + {$stats['deadlines']} deadline (blog {$stats['blog']}, haber {$stats['news']}, burs {$stats['scholarships']})");

        // Aboneler — reachable: confirmed + not unsubscribed + not bounced/complained
        $query = Subscriber::reachable();
        // Bu hafta zaten gönderilenleri atla → endpoint timeout/retry'da çift gönderim YOK
        // (idempotent). --force ile bypass (test).
        if (! $this->option('force')) {
            $query->where(fn ($q) => $q->whereNull('last_sent_at')->orWhere('last_sent_at', '<', now()->subDays(6)));
        }
        if ($onlys = array_filter((array) $this->option('only'))) {
            $query->whereIn('email', $onlys);
        }
        $subs = $query->get();
        $this->info("👥 {$subs->count()} reachable abone (hard-bounce + complaint hariç)");

        if (! $this->option('send')) {
            $this->warn('⚠️ Dry-run — gerçek e-mail YOK. --send ile gönder.');
            $this->newLine();
            foreach (array_slice($items, 0, 8) as $i) {
                $this->line("  {$i['category']}  " . \Illuminate\Support\Str::limit($i['title'], 60));
            }
            foreach ($deadlines as $d) {
                $this->line("  ⏰ {$d['date']}  " . \Illuminate\Support\Str::limit($d['program'], 50));
            }
            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;
        $bar = $this->output->createProgressBar($subs->count());
        $bar->start();
        $throttleMs = max(0, (int) $this->option('throttle'));

        foreach ($subs as $sub) {
            try {
                Mail::to($sub->email)->send(new WeeklyDigest($sub, $items, $stats, $deadlines));
                $sub->update(['last_sent_at' => now()]);
                $sent++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("  {$sub->email}: " . substr($e->getMessage(), 0, 100));
                $failed++;
            }
            if ($throttleMs > 0) {
                usleep($throttleMs * 1000);
            }
            $bar->advance();
        }
        $bar->finish();
        $this->newLine(2);
        $this->info("✅ {$sent} gönderildi, ❌ {$failed} başarısız");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}

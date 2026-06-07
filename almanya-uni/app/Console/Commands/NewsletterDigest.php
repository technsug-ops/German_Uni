<?php

namespace App\Console\Commands;

use App\Mail\WeeklyDigest;
use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\State;
use App\Models\Subscriber;
use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NewsletterDigest extends Command
{
    protected $signature = 'newsletter:digest
        {--days=7 : Son N günde enrich edilenler}
        {--send : Gerçekten gönder (yoksa dry-run)}
        {--limit=15 : Maksimum içerik sayısı}
        {--only=* : Sadece bu email\'lere gönder}
        {--throttle=100 : Aboneler arası bekleme (ms) — SMTP rate limit için}';

    protected $description = 'Haftalık digest e-postası — son N günde enrich edilen içerikler abonelere';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $limit = (int) $this->option('limit');
        $since = now()->subDays($days);

        // Son N günde enrich edilenleri topla
        $items = collect();

        City::whereNotNull('content_blocks')
            ->where('last_enriched_at', '>=', $since)
            ->orderByDesc('last_enriched_at')
            ->take(10)
            ->get(['slug', 'name_de', 'image_url', 'content_blocks', 'last_enriched_at'])
            ->each(fn ($c) => $items->push([
                'title' => $c->name_de . ' — Şehir Rehberi',
                'category' => '🏙️ Şehir',
                'category_color' => '#0891b2',
                'url' => route('cities.show', $c->slug),
                'image' => $c->image_url,
                'description' => \App\Support\Seo::descriptionFromBlocks($c->content_blocks, "{$c->name_de} şehri rehberi."),
                'pub_date' => $c->last_enriched_at,
            ]));

        University::where('is_active', 1)->whereNotNull('content_blocks')
            ->where('last_enriched_at', '>=', $since)
            ->orderByDesc('last_enriched_at')
            ->take(10)
            ->get(['slug', 'name_de', 'image_url', 'content_blocks', 'last_enriched_at'])
            ->each(fn ($u) => $items->push([
                'title' => $u->name_de,
                'category' => '🎓 Üniversite',
                'category_color' => '#1e40af',
                'url' => route('universities.show', $u->slug),
                'image' => $u->image_url,
                'description' => \App\Support\Seo::descriptionFromBlocks($u->content_blocks, "{$u->name_de} hakkında rehber."),
                'pub_date' => $u->last_enriched_at,
            ]));

        FieldOfStudy::active()->whereNotNull('content_blocks')
            ->where('last_enriched_at', '>=', $since)
            ->orderByDesc('last_enriched_at')
            ->get(['slug', 'name_tr', 'image_url', 'content_blocks', 'last_enriched_at'])
            ->each(fn ($f) => $items->push([
                'title' => $f->name_tr . ' — Eğitim Alanı',
                'category' => '📚 Alan',
                'category_color' => '#7c3aed',
                'url' => route('fields.show', $f->slug),
                'image' => $f->image_url,
                'description' => \App\Support\Seo::descriptionFromBlocks($f->content_blocks, "Almanya'da {$f->name_tr} alanı."),
                'pub_date' => $f->last_enriched_at,
            ]));

        State::whereNotNull('content_blocks')
            ->where('last_enriched_at', '>=', $since)
            ->orderByDesc('last_enriched_at')
            ->get(['slug', 'name_de', 'image_url', 'content_blocks', 'last_enriched_at'])
            ->each(fn ($s) => $items->push([
                'title' => $s->name_de . ' — Eyalet',
                'category' => '🗺️ Eyalet',
                'category_color' => '#059669',
                'url' => route('states.show', $s->slug),
                'image' => $s->image_url,
                'description' => \App\Support\Seo::descriptionFromBlocks($s->content_blocks, "{$s->name_de} eyalet rehberi."),
                'pub_date' => $s->last_enriched_at,
            ]));

        $items = $items->sortByDesc('pub_date')->take($limit)->values()->toArray();

        if (empty($items)) {
            $this->warn("Son {$days} gün için içerik yok. Önce enrich çalıştır.");
            return self::SUCCESS;
        }

        $this->info("📦 {$days} günde " . count($items) . ' içerik bulundu');

        $stats = [
            'cities' => collect($items)->where('category', '🏙️ Şehir')->count(),
            'universities' => collect($items)->where('category', '🎓 Üniversite')->count(),
            'fields' => collect($items)->where('category', '📚 Alan')->count(),
            'states' => collect($items)->where('category', '🗺️ Eyalet')->count(),
        ];

        // Aboneler — Reachable scope: confirmed + not unsubscribed + not bounced/complained
        $query = Subscriber::reachable();
        if ($onlys = (array) $this->option('only')) {
            $query->whereIn('email', $onlys);
        }
        $subs = $query->get();
        $this->info("👥 {$subs->count()} reachable abone hedefleniyor (hard-bounce + complaint hariç)");

        if (!$this->option('send')) {
            $this->warn('⚠️ Dry-run modu — gerçek e-mail göndermiyor. --send ile gönder.');
            $this->newLine();
            $this->info('İçerik önizleme:');
            foreach (array_slice($items, 0, 5) as $i) {
                $this->line("  {$i['category']}  {$i['title']}");
            }
            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;
        $bar = $this->output->createProgressBar($subs->count());
        $bar->start();

        // Rate limiter: SMTP providers throttle hızlı blast'ları. Brevo free 300/day,
        // pay-as-you-go ~10/sec sustained. 100ms aralık = 10/sec; konservatif.
        $throttleMs = (int) ($this->option('throttle') ?? 100);

        foreach ($subs as $sub) {
            try {
                // queue() ile yolla: queue worker drain eder, sync block YOK
                Mail::to($sub->email)->send(new WeeklyDigest($sub, $items, $stats));
                $sub->update(['last_sent_at' => now()]);
                $sent++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("  {$sub->email}: " . substr($e->getMessage(), 0, 100));
                $failed++;
            }
            if ($throttleMs > 0) usleep($throttleMs * 1000);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine(2);
        $this->info("✅ {$sent} gönderildi, ❌ {$failed} başarısız");
        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}

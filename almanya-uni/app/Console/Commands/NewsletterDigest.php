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
use Illuminate\Support\Str;

class NewsletterDigest extends Command
{
    protected $signature = 'newsletter:digest
        {--days=7 : Son N gün penceresi (blog/haber/enrichment)}
        {--send : Gerçekten gönder (yoksa dry-run)}
        {--locale= : Sadece bu dil (tr/en/de); boşsa tüm aktif diller}
        {--limit=12 : Maksimum kart sayısı}
        {--only=* : Sadece bu email\'lere gönder}
        {--force : Bu hafta zaten gönderilenleri de gönder (test/yeniden)}
        {--throttle=100 : Aboneler arası bekleme (ms) — SMTP rate limit için}';

    protected $description = 'Haftalık Almanya Rehberi — dil-başına (tr/en/de) ayrı bülten: blog + haber + burs + deadline + keşif';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $limit = (int) $this->option('limit');
        $since = now()->subDays($days);
        $send = (bool) $this->option('send');
        $force = (bool) $this->option('force');
        $throttleMs = max(0, (int) $this->option('throttle'));
        $onlys = array_filter((array) $this->option('only'));

        // Aktif diller (config/locale) — her biri AYRI bülten + AYRI abone segmenti
        $active = collect(config('locale.locales', []))
            ->filter(fn ($l) => ($l['active'] ?? false))->keys()->all();
        $locales = $this->option('locale')
            ? array_values(array_intersect([$this->option('locale')], $active))
            : $active;

        if (empty($locales)) {
            $this->error('Geçerli aktif dil yok.');
            return self::FAILURE;
        }

        // Deadline sorgusu AĞIR (14K program, indekssiz LEAST sıralama) — dilden
        // bağımsız olduğundan TEK kez çek, döngüde sadece locale URL'i üretilir.
        $deadlineBase = $this->fetchDeadlineBase();

        $grandSent = 0;
        $grandFailed = 0;

        foreach ($locales as $loc) {
            app()->setLocale($loc); // içerik + ad bu dilde
            // ⚠️ route() doğru /{loc}/ prefix'i üretsin: setLocale() TEK BAŞINA URL::defaults'u
            // güncellemez (oto-aware sadece web request middleware'inde). Bu olmadan TR/DE
            // bülten linkleri /en/{tr-slug} olur → EN sayfası o slug'ı bulamaz → 404. (2026-06-09)
            \Illuminate\Support\Facades\URL::defaults(['locale' => $loc]);
            [$items, $deadlines, $stats] = $this->buildContent($loc, $since, $limit, $deadlineBase);

            // İçerik yoksa o dili atla (boş mail gönderme)
            if (empty($items) && empty($deadlines)) {
                $this->warn("[{$loc}] içerik yok — atlandı.");
                continue;
            }

            // O dilin aboneleri
            $query = Subscriber::reachable()->where('language', $loc);
            if (! $force) {
                $query->where(fn ($q) => $q->whereNull('last_sent_at')->orWhere('last_sent_at', '<', now()->subDays(6)));
            }
            if ($onlys) {
                $query->whereIn('email', $onlys);
            }
            $subs = $query->get();

            $this->info("[{$loc}] {$stats['total']} kart + {$stats['deadlines']} deadline (blog {$stats['blog']}, haber {$stats['news']}, burs {$stats['scholarships']}) → {$subs->count()} abone");

            if (! $send) {
                foreach (array_slice($items, 0, 5) as $i) {
                    $this->line("   {$i['category']}  " . Str::limit($i['title'], 55));
                }
                continue;
            }

            if ($subs->isEmpty()) {
                $this->line("   [{$loc}] gönderilecek abone yok.");
                continue;
            }

            $bar = $this->output->createProgressBar($subs->count());
            $bar->start();
            foreach ($subs as $sub) {
                try {
                    Mail::to($sub->email)->send(new WeeklyDigest($sub, $items, $stats, $deadlines));
                    $sub->update(['last_sent_at' => now()]);
                    $grandSent++;
                } catch (\Throwable $e) {
                    $this->newLine();
                    $this->error("   {$sub->email}: " . substr($e->getMessage(), 0, 90));
                    $grandFailed++;
                }
                if ($throttleMs > 0) {
                    usleep($throttleMs * 1000);
                }
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
        }

        if ($send) {
            $this->newLine();
            $this->info("✅ Toplam {$grandSent} gönderildi, ❌ {$grandFailed} başarısız (" . count($locales) . ' dil)');
        } else {
            $this->newLine();
            $this->warn('⚠️ Dry-run — gerçek e-mail YOK. --send ile gönder.');
        }

        return $grandFailed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Bir dil için içerik kartları + deadline'lar + istatistik üret.
     * setLocale dışarıda yapıldı → ad/açıklama/route bu dilde.
     *
     * @return array{0: array, 1: array, 2: array}
     */
    /**
     * Yaklaşan deadline'lı programları TEK kez çek (dil-bağımsız ham veri).
     * @return array<int, array{program:string, university:?string, date:string, slug:string}>
     */
    private function fetchDeadlineBase(): array
    {
        $today = now()->toDateString();
        $until = now()->addDays(21)->toDateString();

        return Program::where('is_active', 1)
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
                    'slug' => $p->slug,
                ];
            })
            ->filter(fn ($d) => $d['date'] !== null)
            ->values()->all();
    }

    private function buildContent(string $loc, \Carbon\Carbon $since, int $limit, array $deadlineBase = []): array
    {
        $items = collect();

        // 📝 Yeni blog yazıları (o dilde)
        Post::published()->where('type', '!=', 'news')->where('locale', $loc)
            ->where('published_at', '>=', $since)
            ->orderByDesc('published_at')->take(4)
            ->get(['slug', 'title', 'excerpt', 'featured_image', 'published_at'])
            ->each(fn ($p) => $items->push([
                'type' => 'blog', 'title' => $p->title,
                'category' => '📝 ' . __('Blog'), 'category_color' => '#2563eb',
                'url' => route('blog.show', $p->slug), 'image' => $p->featured_image,
                'description' => (string) $p->excerpt, 'sort' => 1,
            ]));

        // 📰 Almanya'dan haberler (o dilde)
        Post::published()->where('type', 'news')->where('locale', $loc)
            ->where('published_at', '>=', $since)
            ->orderByDesc('published_at')->take(3)
            ->get(['slug', 'title', 'excerpt', 'featured_image', 'published_at'])
            ->each(fn ($p) => $items->push([
                'type' => 'news', 'title' => $p->title,
                'category' => '📰 ' . __('News'), 'category_color' => '#dc2626',
                'url' => route('news.show', $p->slug), 'image' => $p->featured_image,
                'description' => (string) $p->excerpt, 'sort' => 2,
            ]));

        // 🎓 Öne çıkan burs (her hafta döner)
        $scholarTotal = Scholarship::whereNull('removed_at')->where('is_daad', 1)->count();
        if ($scholarTotal > 0) {
            $offset = (now()->weekOfYear * 2) % $scholarTotal;
            Scholarship::whereNull('removed_at')->where('is_daad', 1)
                ->orderBy('id')->skip($offset)->take(2)->get()
                ->each(fn ($s) => $items->push([
                    'type' => 'scholarship', 'title' => $s->name,
                    'category' => '🎓 ' . __('Scholarship'), 'category_color' => '#d97706',
                    'url' => route('scholarships.show', $s->slug), 'image' => null,
                    'description' => (string) ($s->introductionText($loc) ?: $s->{'programmname_' . $loc} ?: $s->programmname_en ?: __('DAAD scholarship for international students.')),
                    'sort' => 3,
                ]));
        }

        // 🔎 Haftanın keşfi — son N günde enrich edilen şehir + üniversite
        City::whereNotNull('content_blocks')->where('last_enriched_at', '>=', $since)
            ->orderByDesc('last_enriched_at')->take(3)
            ->get(['slug', 'name_de', 'image_url', 'content_blocks'])
            ->each(fn ($c) => $items->push([
                'type' => 'city', 'title' => $c->name_de . ' — ' . __('City Guide'),
                'category' => '🏙️ ' . __('City'), 'category_color' => '#0891b2',
                'url' => route('cities.show', $c->slug), 'image' => $c->image_url,
                'description' => \App\Support\Seo::descriptionFromBlocks($c->content_blocks, $c->name_de),
                'sort' => 4,
            ]));

        University::where('is_active', 1)->whereNotNull('content_blocks')
            ->where('last_enriched_at', '>=', $since)
            ->orderByDesc('last_enriched_at')->take(3)
            ->get(['slug', 'name_de', 'image_url', 'content_blocks'])
            ->each(fn ($u) => $items->push([
                'type' => 'university', 'title' => $u->name_de,
                'category' => '🎓 ' . __('University'), 'category_color' => '#1e40af',
                'url' => route('universities.show', $u->slug), 'image' => $u->image_url,
                'description' => \App\Support\Seo::descriptionFromBlocks($u->content_blocks, $u->name_de),
                'sort' => 4,
            ]));

        $items = $items->sortBy('sort')->take($limit)->values()->toArray();

        // ⏰ Yaklaşan deadline'lar — TEK-sefer çekilen base'den locale URL üret (ucuz, DB yok)
        $deadlines = array_map(fn ($d) => [
            'program' => $d['program'],
            'university' => $d['university'],
            'date' => $d['date'],
            'url' => route('programs.show', $d['slug']),
        ], $deadlineBase);

        $stats = [
            'total'        => count($items),
            'blog'         => collect($items)->where('type', 'blog')->count(),
            'news'         => collect($items)->where('type', 'news')->count(),
            'scholarships' => collect($items)->where('type', 'scholarship')->count(),
            'deadlines'    => count($deadlines),
        ];

        return [$items, $deadlines, $stats];
    }
}

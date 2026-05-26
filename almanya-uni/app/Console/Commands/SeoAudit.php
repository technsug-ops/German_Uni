<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Faq;
use App\Models\Post;
use App\Models\Program;
use App\Models\University;
use App\Services\Seo\SeoAuditorService;
use Illuminate\Console\Command;

class SeoAudit extends Command
{
    protected $signature = 'seo:audit
        {--template= : Sadece bu template (home, university_detail, ...)}
        {--all : Tüm template\'leri audit et}
        {--with-ai : AI ile section önerisi üret (Gemini kullanır, token harcar)}
        {--base-url= : Site base URL (default APP_URL)}';

    protected $description = 'Sayfa şablonlarını forum + telegram trending keyword\'lerle karşılaştırır.';

    public function handle(SeoAuditorService $svc): int
    {
        $baseUrl = rtrim($this->option('base-url') ?: config('app.url'), '/');
        $withAi = (bool) $this->option('with-ai');

        $targets = $this->resolveTargets($baseUrl);
        if ($this->option('template')) {
            $key = $this->option('template');
            if (!isset($targets[$key])) {
                $this->error("Template bulunamadı: $key");
                return self::FAILURE;
            }
            $targets = [$key => $targets[$key]];
        } elseif (!$this->option('all')) {
            $this->error('--template=X veya --all gerek.');
            return self::FAILURE;
        }

        $this->info(count($targets) . ' template audit edilecek (AI öneri: ' . ($withAi ? 'AÇIK' : 'KAPALI') . ')');
        $this->newLine();

        foreach ($targets as $template => $url) {
            $this->line('━━━ ' . str_pad($template, 22) . ' ' . substr($url, 0, 60) . ' ━━━');
            try {
                $audit = $svc->audit($template, $url, $withAi);
                $this->line(sprintf(
                    '  Found: %d kw · Missing: %d · Opportunity: %d/100 · İçerik: %d char · H2: %d',
                    count($audit->keywords_found ?? []),
                    count($audit->keywords_missing ?? []),
                    $audit->opportunity_score,
                    $audit->content_length,
                    $audit->h2_count,
                ));
                if ($audit->ai_suggestions) {
                    $this->line('  AI öneri: ' . mb_substr($audit->ai_suggestions, 0, 120) . '...');
                }
            } catch (\Throwable $e) {
                $this->error('  ✗ ' . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info('✅ /admin/seo-audits adresinde detayları gör.');
        return self::SUCCESS;
    }

    /**
     * Audit edilecek template'ler — her biri için sample URL.
     */
    private function resolveTargets(string $baseUrl): array
    {
        // Sample sayfaları DB'den dinamik al
        $sampleUni = University::where('is_active', 1)->whereNotNull('slug')->orderByDesc('student_count')->first();
        $sampleCity = City::has('universities')->orderByDesc('id')->first();
        $sampleProgram = Program::where('is_active', 1)->whereNotNull('slug')->whereNotNull('description_en')->first();
        $sampleFaq = Faq::published()->answered()->first();
        $samplePost = Post::published()->first();

        $t = [
            'home' => $baseUrl . '/',
            'university_index' => $baseUrl . '/universities',
            'program_index' => $baseUrl . '/programs',
            'faq_index' => $baseUrl . '/faq',
            'blog_index' => $baseUrl . '/blog',
            'compare' => $baseUrl . '/compare',
            'rankings' => $baseUrl . '/rankings',
            'map' => $baseUrl . '/map',
            'tool_cost' => $baseUrl . '/tools/cost-of-living',
            'tool_grade' => $baseUrl . '/tools/grade-converter',
            'tool_recommendation' => $baseUrl . '/tools/recommendation',
            'about' => $baseUrl . '/about',
            'housing' => $baseUrl . '/housing',
        ];

        if ($sampleUni)     $t['university_detail'] = $baseUrl . '/universities/' . $sampleUni->slug;
        if ($sampleCity)    $t['city_detail']       = $baseUrl . '/cities/' . $sampleCity->slug;
        if ($sampleProgram) $t['program_detail']    = $baseUrl . '/programs/' . $sampleProgram->slug;
        if ($sampleFaq && $sampleFaq->topic) {
            $t['faq_detail'] = $baseUrl . '/faq/' . $sampleFaq->topic->slug . '/' . $sampleFaq->slug;
        }
        if ($samplePost)    $t['blog_detail']       = $baseUrl . '/blog/' . $samplePost->slug;

        return $t;
    }
}

<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SystemHealthWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Sistem Sağlığı';

    protected static ?int $sort = 30;

    protected function getStats(): array
    {
        $data = Cache::remember('dashboard:system_health_v2', now()->addMinutes(10), function () {
            // stdClass değil — array dön ki cache serialize/unserialize'da __PHP_Incomplete_Class olmasın
            $programs = (array) (DB::selectOne("
                SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN language IN ('en', 'both') THEN 1 ELSE 0 END) AS en_total
                FROM programs
                WHERE is_active = 1
            ") ?: (object) ['total' => 0, 'en_total' => 0]);

            $faqs = (int) DB::table('faqs')
                ->where('is_published', 1)
                ->where('has_answer', 1)
                ->count();

            $posts = (int) DB::table('posts')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->count();

            $apiClients = 0;
            $briefs = 0;
            try {
                $apiClients = (int) DB::table('api_clients')->count();
            } catch (\Throwable) {}
            try {
                $briefs = (int) DB::table('content_briefs')->count();
            } catch (\Throwable) {}

            // Topluluk pool — JSON dosya boyutlarından tahmin (parse etmiyoruz, ağır)
            $tgPath = storage_path('app/community/telegram_by_topic.json');
            $forumPath = storage_path('app/community/forum_insights.json');
            $tgReports = count(glob(storage_path('app/community/telegram_report_*.json')) ?: []);
            $tgPool = is_file($tgPath) ? round(filesize($tgPath) / 1024) : 0;
            $forumKb = is_file($forumPath) ? round(filesize($forumPath) / 1024) : 0;

            return compact('programs', 'faqs', 'posts', 'apiClients', 'briefs', 'tgPool', 'forumKb', 'tgReports');
        });

        $progTotal = (int) ($data['programs']['total'] ?? 0);
        $progEn = (int) ($data['programs']['en_total'] ?? 0);

        return [
            Stat::make('Aktif program', number_format($progTotal))
                ->description($progEn . ' İngilizce')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success'),

            Stat::make('SSS + Blog', "{$data['faqs']} SSS · {$data['posts']} blog")
                ->description('Yayında')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('API + Brief', "{$data['apiClients']} client · {$data['briefs']} brief")
                ->description('Public API + Content Factory')
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning'),

            Stat::make('Topluluk pool', "{$data['tgPool']} KB · {$data['forumKb']} KB")
                ->description("{$data['tgReports']} TG raporu")
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),
        ];
    }
}

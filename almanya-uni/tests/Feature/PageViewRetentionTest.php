<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackPageView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * page_views saklama süresi: gizlilik politikası "erişim kayıtları 90 günde anonimleştirilir" diyor.
 * TrackPageView::pruneExpired() süresi dolan satırları siler, süresi dolmayanlara dokunmaz.
 */
class PageViewRetentionTest extends TestCase
{
    use RefreshDatabase;

    private function pageView(\DateTimeInterface $at, string $path = '/en'): void
    {
        DB::table('page_views')->insert([
            'session_id' => md5($path . $at->format('c')), 'path' => $path, 'is_bot' => 0, 'created_at' => $at,
        ]);
    }

    public function test_prune_deletes_only_rows_past_retention(): void
    {
        $this->pageView(now()->subDays(TrackPageView::RETENTION_DAYS + 1), '/old-1');
        $this->pageView(now()->subDays(TrackPageView::RETENTION_DAYS + 200), '/old-2');
        $this->pageView(now()->subDays(TrackPageView::RETENTION_DAYS - 1), '/recent');
        $this->pageView(now(), '/today');

        $this->assertSame(2, TrackPageView::pruneExpired());
        $this->assertEqualsCanonicalizing(['/recent', '/today'], DB::table('page_views')->pluck('path')->all());
        $this->assertSame(0, TrackPageView::pruneExpired());
    }
}

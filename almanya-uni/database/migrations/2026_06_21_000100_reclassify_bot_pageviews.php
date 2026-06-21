<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Admin analitik şişkinliğini geriye dönük düzeltir: page_views tablosunda
 * SAYFA OLMAYAN istekleri (asset/feed: .webmanifest, .ics, .json, .xml, statik
 * dosyalar) ve script/SEO-bot user-agent'larını is_bot=1 olarak işaretler.
 *
 * VisitorStats yalnızca is_bot=0 sayar → panel rakamları anında gerçeğe yaklaşır.
 * Veri SİLİNMEZ (sadece sınıflandırma). Idempotent.
 *
 * (İleriye dönük kayıt zaten TrackPageView middleware'inde düzeltildi:
 *  sadece text/html yanıtlar + genişletilmiş bot tespiti.)
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('page_views')) {
            return;
        }

        // 1) Asset/feed yolları — "sayfa" değil (en büyük şişme kaynağı: /site.webmanifest, *.ics)
        $exts = [
            'webmanifest', 'ics', 'json', 'xml', 'txt', 'rss', 'atom', 'csv',
            'css', 'js', 'map', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp',
            'avif', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'pdf', 'zip', 'gz',
            'mp3', 'mp4', 'webm',
        ];
        DB::table('page_views')
            ->where('is_bot', 0)
            ->where(function ($q) use ($exts) {
                foreach ($exts as $e) {
                    $q->orWhere('path', 'like', '%.' . $e);
                }
                $q->orWhere('path', 'like', '%/ics');           // /tools/deadlines/ics gibi
                $q->orWhere('path', 'like', '%.webmanifest%');
            })
            ->update(['is_bot' => 1]);

        // 2) Script / SEO-bot / monitoring user-agent'ları (eski filtre kaçırmıştı)
        $botUa = [
            'python-requests', 'python-httpx', 'headlesschrome', 'phantomjs',
            'curl/', 'wget', 'go-http', 'okhttp', 'axios', 'node-fetch', 'scrapy',
            'libwww', 'apache-httpclient', 'dataforseo', 'semrush', 'ahrefs',
            'mj12', 'dotbot', 'petalbot', 'bytespider', 'censys', 'zgrab',
            'expanse', 'gptbot', 'uptimerobot', 'statuscake', 'site24x7',
            'powershell', 'seoaudit', 'almanyauni-',
        ];
        DB::table('page_views')
            ->where('is_bot', 0)
            ->where(function ($q) use ($botUa) {
                foreach ($botUa as $b) {
                    $q->orWhere('user_agent', 'like', '%' . $b . '%');
                }
            })
            ->update(['is_bot' => 1]);

        // 3) UA yok / şüpheli kısa → bot
        DB::table('page_views')
            ->where('is_bot', 0)
            ->where(function ($q) {
                $q->whereNull('user_agent')
                  ->orWhere('user_agent', '')
                  ->orWhereRaw('LENGTH(user_agent) < 15');
            })
            ->update(['is_bot' => 1]);
    }

    public function down(): void
    {
        // Bilinçli no-op: yanlış-pozitif "insan" sayımına geri dönmek istemiyoruz.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Otomatik haber çekimi (news:fetch) RSS/Atom kaynaklarını config/news_sources.php'den
 * DB'ye taşır → admin panelden (Haber Kaynakları) düzenlenebilir: ekle/sil/aç-kapat,
 * keyword + kategori. config dosyası artık yalnızca tablo boşsa fallback/seed kaynağı.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('url', 600);
            $table->string('default_category', 80)->nullable(); // categories.kind='news' slug
            $table->json('keywords')->nullable();               // boşsa filtre yok (hepsi)
            $table->unsignedTinyInteger('max_per_source')->nullable(); // boşsa global varsayılan
            $table->boolean('enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamp('last_fetched_at')->nullable();
            $table->string('last_result', 160)->nullable();     // teşhis: "5 yeni" / "okunamadı"
            $table->timestamps();

            $table->index('enabled');
        });

        // Mevcut config kaynaklarını seed et (kayıp olmasın). Idempotent: name benzersiz değil
        // ama bu tek seferlik create migration'ında tablo boş, çakışma yok.
        $now = now();
        $seed = [
            ['Google News · Vize & Göç', 'https://news.google.com/rss/search?q=Germany%20student%20visa%20OR%20Chancenkarte%20OR%20%22skilled%20immigration%22&hl=en-US&gl=US&ceid=US:en', 'visa-residence', ['Germany', 'German', 'Deutschland', 'Chancenkarte', 'visa', 'immigration'], true, 10],
            ['Google News · Üniversite & Burs', 'https://news.google.com/rss/search?q=%22study%20in%20Germany%22%20(university%20OR%20DAAD%20OR%20scholarship%20OR%20students)&hl=en-US&gl=US&ceid=US:en', 'universities', ['Germany', 'German', 'Deutschland', 'DAAD', 'university', 'scholarship'], true, 20],
            ['ICEF Monitor', 'https://monitor.icef.com/feed/', 'universities', ['Germany', 'German', 'Deutschland', 'DAAD', 'Berlin', 'Munich', 'Chancenkarte'], true, 30],
            ['DAAD', 'https://www.daad.de/en/rss/', 'universities', [], false, 40],
            ['Make-it-in-Germany', 'https://www.make-it-in-germany.com/en/rss', 'visa-residence', [], false, 50],
            ['Mediendienst Integration', 'https://mediendienst-integration.de/feed.html', 'integration', [], false, 60],
        ];

        $rows = [];
        foreach ($seed as [$name, $url, $cat, $keywords, $enabled, $sort]) {
            $rows[] = [
                'name'             => $name,
                'url'              => $url,
                'default_category' => $cat,
                'keywords'         => json_encode($keywords),
                'max_per_source'   => null,
                'enabled'          => $enabled,
                'sort_order'       => $sort,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }
        DB::table('news_sources')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('news_sources');
    }
};

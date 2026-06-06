<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Tool sayfalarından linklenen ama is_published=0 (taslak) kalmış REHBER yazılarını yayınla.
 * QA raporu (Merve, 05.06.2026): "werkstudent rehberi" (bütçe planlayıcı) ve
 * "Sperrkonto Rehberi" (vize maliyeti) butonları 404 veriyordu — yazılar tam içerikli
 * (20K+ karakter, published_at dolu) ama yayın bayrağı set edilmemiş.
 *
 * NOT: Eloquent save() KULLANILMAZ — Scout indexleme prod'da FAIL veriyor (memory kuralı).
 * DB::table ile düz update. Idempotent: yalnızca published_at dolu + içerikli taslakları açar.
 */
return new class extends Migration
{
    public function up(): void
    {
        $slugs = [
            'almanyada-ogrenci-isleri-20-saat-kurali-vergi-ve-saglik-sigortasi-rehberi',
            'sperrkonto-2025-tam-rehber-almanya-vizesi-icin-bloke-hesap',
        ];

        DB::table('posts')
            ->whereIn('slug', $slugs)
            ->where('is_published', 0)
            ->whereNotNull('published_at')
            ->whereNotNull('content_md')
            ->update(['is_published' => 1, 'updated_at' => now()]);

        // Potsdam yanlış görsel (DEVAM EDİYOR — kök neden nihayet bulundu).
        // getImageUrlAttribute accessor'ı, DB image_url null OLSA BİLE diskteki
        // img/cache/unis/{slug}.webp varsa onu döndürüyor (53 üni için görsel YALNIZCA
        // cache'te yaşadığından bu davranış kasıtlı). Potsdam'ın cache DOSYASI yanlış
        // (Q153012 P18 = kişi portresi). Bu yüzden 2026-06-05'in image_url=null'ı işe
        // yaramıyordu. Çözüm: (a) DB image_url null, (b) komutta SKIP_QIDS denylist
        // (geri-doldurmayı önler), (c) yanlış cache dosyasını sil → accessor cache
        // bulamaz, null döner, CoverImage fallback (gradient/şehir) render olur.
        DB::table('universities')
            ->where('wikidata_id', 'Q153012')
            ->update(['image_url' => null, 'updated_at' => now()]);

        $badCache = public_path('img/cache/unis/universitat-potsdam-q153012.webp');
        if (is_file($badCache)) {
            @unlink($badCache);
        }
    }

    public function down(): void
    {
        // Geri alma kasıtlı no-op: yayınlanan rehberi tekrar taslağa çekmek istemiyoruz.
    }
};

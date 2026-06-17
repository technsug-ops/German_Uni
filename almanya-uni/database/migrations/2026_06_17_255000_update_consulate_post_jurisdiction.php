<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

/**
 * Konsolosluk iletişim post'una iDATA yetki-alanı (hangi il hangi konsolosluğa bağlı)
 * dağılımını ekler — "Hangi konsolosluğa başvurmalıyım?" bölümünü genişletir. Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $post = Post::where('slug', 'germany-consulates-turkey-contact-ankara-istanbul-izmir')->first();
        if (! $post) {
            return;
        }
        $md = (string) $post->content_md;
        if (str_contains($md, 'iDATA yetki alanı dağılımı')) {
            return; // zaten eklenmiş
        }

        $old = 'Genel kural: **ikamet ettiğin ile göre** yetkili temsilcilik belirlenir. Yaşadığın ilin hangi konsolosluğa bağlı olduğunu iDATA randevu sistemi veya resmi site üzerinden kontrol et. İkametini değiştirip başka konsolosluğa geçmek bazen bekleme/farklı kurallar getirebilir — randevu almadan önce teyit et.';

        $new = <<<'NEW'
Yetkili temsilcilik **ikamet ettiğin ile göre** belirlenir. iDATA yetki alanı dağılımı şöyle:

**🔴 İstanbul Başkonsolosluğu** — iDATA ofisleri: İstanbul Avrupa, İstanbul Asya, Bursa
İstanbul, Kocaeli, Sakarya, Düzce, Bolu, Yalova, Bursa, Bilecik, Eskişehir, Çanakkale, Balıkesir, Tekirdağ, Edirne, Kırklareli.

**🔵 İzmir Başkonsolosluğu** — iDATA ofisleri: İzmir, Antalya
İzmir, Manisa, Aydın, Denizli, Muğla, Uşak, Kütahya, Afyonkarahisar, Isparta, Burdur, Antalya.

**🟢 Ankara Büyükelçiliği** — iDATA ofisleri: Ankara, Gaziantep, Trabzon
Yukarıdaki illerin dışında kalan **diğer tüm iller** (İç Anadolu, Karadeniz, Doğu/Güneydoğu Anadolu ve Akdeniz'in büyük kısmı — ör. Ankara, Konya, Adana, Mersin, Kayseri, Trabzon, Gaziantep, Diyarbakır, Erzurum…).

İkametini değiştirip başka konsolosluğa geçmek bazen bekleme/farklı kurallar getirebilir. **Güncel yetki alanı** için randevu almadan önce **[iDATA](https://www.idata.com.tr)** ve resmi siteden teyit et.
NEW;

        $md = str_contains($md, $old) ? str_replace($old, $new, $md) : ($md . "\n\n## Hangi konsolosluğa başvurmalıyım?\n\n" . $new);

        $post->content_md = $md;
        $post->content_html = Str::markdown($md, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $post->save();
    }

    public function down(): void
    {
        // İçerik genişletme — geri alınmaz.
    }
};

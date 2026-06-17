<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * TR blog (vize): İzmir Konsolosluğu Auslandsportal (digital.diplo.de) "mail" süreci —
 * güncel/kesinleşmemiş topluluk bilgisi, dikkat notlarıyla. FK-safe, İngilizce slug.
 */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'izmir-consulate-auslandsportal-digital-diplo-visa-process';

        $body = <<<'MD'
İzmir Almanya Konsolosluğu'na vize başvurusu yapan bazı kişiler son dönemde, süreci hızlandırmaya yönelik bir **"mail" süreci** ile karşılaşıyor. Aşağıda bu durumun şu anki bilinen hâlini ve **kritik uyarıları** topluluk bilgisine dayanarak özetledik.

> 🚨 **Önce bunu oku:** Bu bilgi **topluluk/aktarım kaynaklıdır ve KESİNLEŞMEMİŞTİR.** Uygulama değişebilir veya kişiye göre farklılaşabilir. Harekete geçmeden önce **resmi kaynaktan** ([İzmir Başkonsolosluğu — tuerkei.diplo.de](https://tuerkei.diplo.de/tr-tr/vertretungen/generalkonsulat-izmir)) ve aldığın resmi maildeki yönergeden teyit et.

## Şu an bilinen durum

İzmir'de ortaya çıkan bu "mail" sürecinin, İstanbul'daki gibi **iDATA yoğunluğunu azaltıp süreci hızlandırmak** amacıyla başlatıldığı düşünülüyor. Aktarılan bilgiye göre: **mail alan kişiler**, mevcut başvurularını iptal edip **[digital.diplo.de](https://digital.diplo.de/) (Auslandsportal)** üzerinden yeniden başvurursa **öncelik tanınabiliyor.** (Kesinliği net değil — sadece aktarılan bilgi bu yönde.)

## ⚠️ Üç kritik uyarı

1. **Belgelerin eksiksiz olmalı.** Auslandsportal üzerinden başvuru yapabilmek için **tüm belgelerin hazır** olması gerekir; eksikse sistemden başvuru yapamazsın.
2. **Mail ALMAYAN kimse geçiş yapmasın.** Mail almadıysan **iDATA başvurunu iptal edip portala geçme** — bu süreç şu an **yalnızca mail alan kişiler** için geçerli.
3. **Asılları yanında getir.** Randevuya gelirken, Auslandsportal'a yüklediğin ve ibrazı istenen **tüm belgelerin asıllarını** yanında bulundur.

## Adım adım (mail aldıysan)

1. **[digital.diplo.de](https://digital.diplo.de/)** (Auslandsportal) üzerinden başvur; tüm belgeleri eksiksiz yükle.
2. Yetkili **konsolosluk ön inceleme** yapar.
3. Mevcut **iDATA randevunu iptal et.**
4. Ön inceleme tamamlanınca, **Auslandsportal'daki iDATA randevu bağlantısı** ile yeni randevu oluştur.
5. **Biyometrik veri** alımı için sana randevu verilir; o gün **belge asıllarını** götür.

Sürecin belirli bir sıraya göre mi yoksa rastgele mi ilerlediği net değil; amaç büyük olasılıkla iDATA'daki yoğunluğu azaltmak.

## İlgili rehberler

- [Almanya konsoloslukları iletişim bilgileri](/tr/blog/germany-consulates-turkey-contact-ankara-istanbul-izmir)
- [Konsolosluğa gitmeden önce: vize süreci nasıl işler](/tr/blog/germany-student-visa-consulate-process-before-you-go)

---

**Not:** Gelişmeler değişebilir. Resmi ve güncel bilgi için her zaman **tuerkei.diplo.de** ve aldığın resmi mail esastır. Bu yazı bilgilendirme amaçlıdır.
MD;

        $excerpt = 'İzmir Almanya Konsolosluğu Auslandsportal (digital.diplo.de) "mail" süreci: mail alanların iDATA\'yı iptal edip portaldan öncelikli başvurusu, üç kritik uyarı ve adım adım akış (güncel, kesinleşmemiş topluluk bilgisi).';

        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);

        $userId = DB::table('users')->where('id', 6)->exists() ? 6 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 6)->exists() ? 6 : DB::table('categories')->where('slug', 'vize')->value('id');

        $payload = [
            'locale'           => 'tr',
            'user_id'          => $userId,
            'category_id'      => $categoryId,
            'title'            => 'İzmir Konsolosluğu: Auslandsportal (digital.diplo.de) Mail Süreci — Güncel Not',
            'excerpt'          => Str::limit($excerpt, 250, '…'),
            'content_md'       => $body,
            'content_html'     => $html,
            'meta_title'       => 'İzmir Konsolosluğu Auslandsportal (digital.diplo.de) Vize Süreci',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes'  => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published'     => true,
            'published_at'     => now(),
        ];

        $existing = Post::where('slug', $slug)->first();
        if ($existing) {
            $existing->update($payload);
        } else {
            Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]);
        }
    }

    public function down(): void
    {
        Post::where('slug', 'izmir-consulate-auslandsportal-digital-diplo-visa-process')->delete();
    }
};

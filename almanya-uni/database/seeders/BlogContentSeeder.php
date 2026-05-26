<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BlogContentSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::firstOrCreate(
            ['email' => 'editor@almanyauni.com'],
            [
                'name' => 'AlmanyaUni Editörü',
                'password' => Hash::make(Str::random(32)),
            ]
        );

        $categories = [
            [
                'name' => 'Almanya\'da Eğitim',
                'slug' => 'almanyada-egitim',
                'description' => 'Almanya\'daki üniversite eğitimi, akademik takvim ve sistemler hakkında temel rehberler.',
                'color' => '#1E40AF',
                'sort_order' => 1,
            ],
            [
                'name' => 'Başvuru Süreçleri',
                'slug' => 'basvuru-surecleri',
                'description' => 'Lisans ve yüksek lisans başvuru süreçleri, gereken belgeler, tarihler ve ipuçları.',
                'color' => '#F97316',
                'sort_order' => 2,
            ],
            [
                'name' => 'Dil Sınavları',
                'slug' => 'dil-sinavlari',
                'description' => 'TestDaF, DSH, IELTS, TOEFL ve diğer Almanca/İngilizce dil sınavları.',
                'color' => '#0EA5E9',
                'sort_order' => 3,
            ],
            [
                'name' => 'Öğrenci Hayatı',
                'slug' => 'ogrenci-hayati',
                'description' => 'Yaşam maliyeti, barınma, çalışma izni ve Almanya\'da öğrenci olmanın detayları.',
                'color' => '#10B981',
                'sort_order' => 4,
            ],
        ];

        $catMap = [];
        foreach ($categories as $cat) {
            $catMap[$cat['slug']] = Category::firstOrCreate(
                ['slug' => $cat['slug']],
                $cat
            )->id;
        }

        $posts = [
            [
                'title' => 'Almanya\'da Üniversite Eğitimi: 2026 Başlangıç Rehberi',
                'slug' => 'almanyada-universite-egitimi-2026-rehberi',
                'excerpt' => 'Almanya\'da lisans veya yüksek lisans yapmak isteyen Türk öğrenciler için temel bilgiler: sistem, ücretler, başvuru takvimi ve doğru üniversite seçimi.',
                'category' => 'almanyada-egitim',
                'meta_description' => 'Almanya\'da üniversite eğitimi nasıl alınır? 2026 yılı için güncel başvuru süreci, ücretler, dil şartları ve üniversite seçimi rehberi.',
                'content' => $this->postContent1(),
            ],
            [
                'title' => 'TestDaF mi DSH mi? 2026 Almanca Dil Sınavı Karşılaştırması',
                'slug' => 'testdaf-mi-dsh-mi-karsilastirmasi',
                'excerpt' => 'Alman üniversiteleri için en yaygın iki Almanca dil sınavı olan TestDaF ve DSH arasındaki farklar, hangi sınavın size daha uygun olduğu ve hazırlık ipuçları.',
                'category' => 'dil-sinavlari',
                'meta_description' => 'TestDaF ile DSH arasındaki farklar nelerdir? Hangisi daha kolay, hangisi daha çok kabul ediliyor? Detaylı karşılaştırma ve hazırlık rehberi.',
                'content' => $this->postContent2(),
            ],
        ];

        foreach ($posts as $p) {
            Post::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'user_id' => $author->id,
                    'category_id' => $catMap[$p['category']],
                    'title' => $p['title'],
                    'excerpt' => $p['excerpt'],
                    'content_md' => $p['content'],
                    'meta_description' => $p['meta_description'],
                    'published_at' => now()->subDays(rand(1, 14)),
                    'is_published' => true,
                ]
            );
        }
    }

    private function postContent1(): string
    {
        return <<<'MD'
Almanya, Türk öğrenciler için en popüler yurt dışı eğitim destinasyonlarından biri. Ücretsiz devlet üniversiteleri, güçlü ekonomi ve yaşanabilir şehirler bu ilginin başlıca nedenleri. Bu rehberde başvuru sürecinden günlük hayata kadar bilmen gereken her şeyi özetliyoruz.

## Almanya'da Üniversite Sistemi

Alman yükseköğretim sistemi üç ana üniversite türüne ayrılır:

- **Universität (Klassische Universität):** Akademik ve araştırma odaklı. Tıp, hukuk, mühendislik ve temel bilimler genelde burada okutulur.
- **Fachhochschule (FH / HAW):** Uygulamalı bilimler üniversitesi. Daha pratik, sektör odaklı, staj zorunluluğu olan programlar.
- **Kunst- und Musikhochschule:** Sanat, müzik ve tasarım alanlarında uzmanlaşmış kurumlar.

> İlk karar: Akademik kariyer mi yoksa sektörde hızlı istihdam mı istiyorsun? Bu seçim üniversite türünü belirler.

## Eğitim Ücretleri

Çoğu **devlet üniversitesi öğrenim ücretsizdir.** Ödediğin tek şey yarıyıl başına 150-350 € tutarındaki *Semesterbeitrag* (yarıyıl katkı payı) — bu rakama genelde toplu taşıma kartı da dahildir.

Özel üniversitelerde durum farklı:

| Üniversite Türü | Yıllık Ücret |
| --- | --- |
| Devlet üniversitesi | ~600 € (sadece katkı) |
| Özel üniversite | 5,000-20,000 € |
| Baden-Württemberg eyaleti (AB dışı) | 3,000 € |

## Başvuru Takvimi

Almanya'da yıl iki yarıyıla bölünür:

1. **Kış yarıyılı (Wintersemester):** Ekim - Mart. Başvurular Mayıs-Temmuz arasında.
2. **Yaz yarıyılı (Sommersemester):** Nisan - Eylül. Başvurular Aralık-Ocak arasında.

Çoğu lisans programı yalnızca kış yarıyılında öğrenci alır, dolayısıyla **Temmuz 15 deadline'ı kritik bir tarihtir.**

## Dil Şartı

Almanca eğitim veren bir programa başvuracaksan C1 seviyesinde Almanca belgesi gerekli. İngilizce programlarda ise IELTS 6.5 veya TOEFL 90+ standart hale geldi.

Detaylar için [TestDaF ve DSH karşılaştırması yazımıza](/blog/testdaf-mi-dsh-mi-karsilastirmasi) bakabilirsin.

## Sıradaki Adım

Üniversite seçimi konusunda ilk adımı atmak için [583 üniversiteyi karşılaştırabilirsin](/universities). Ayrıca eyaletine göre üniversiteleri görmek istersen [sıralama sayfalarına](/siralama) göz at.
MD;
    }

    private function postContent2(): string
    {
        return <<<'MD'
Alman bir üniversitede Almanca eğitim almak istiyorsan iki sınavla karşılaşırsın: **TestDaF** ve **DSH**. Aralarındaki farkları bilmek, doğru sınavı seçmek için kritik.

## TestDaF Nedir?

TestDaF (Test Deutsch als Fremdsprache), uluslararası kabul gören standart bir Almanca yeterlilik sınavıdır. Yılda **altı kez** düzenlenir ve Türkiye'de Goethe-Institut'ların düzenlediği oturumlarda girilebilir.

**Yapı:**
- Okuma anlama (60 dk)
- Dinleme anlama (40 dk)
- Yazılı anlatım (60 dk)
- Sözlü anlatım (35 dk, dijital kayıt)

Her bölümden TDN 3, TDN 4 veya TDN 5 alırsın. **TDN 4** çoğu üniversite için yeterlidir, bazı programlar (tıp gibi) TDN 5 ister.

## DSH Nedir?

DSH (Deutsche Sprachprüfung für den Hochschulzugang), her üniversitenin **kendi içinde** düzenlediği bir Almanca giriş sınavıdır. Yani başvurduğun her uni için farklı format ve farklı yer.

**Genel yapı:**
- Yazılı bölüm (3-4 saat): okuma, dinleme, gramer, yazma
- Sözlü bölüm (15-20 dk, sınavdan ~1 hafta sonra)

DSH-1, DSH-2 ve DSH-3 seviyeleri vardır. **DSH-2 standart kabuldür.**

## Doğrudan Karşılaştırma

| Kriter | TestDaF | DSH |
| --- | --- | --- |
| Düzenleyici | Resmi merkezi sınav | Her üniversite kendisi |
| Sıklık | Yılda 6 kez | Üniversiteye göre değişir |
| Geçerlilik | Süresiz | Sadece o üniversite için |
| Türkiye'de girebilir miyim? | ✅ Evet | ❌ Hayır |
| Çoklu başvuruda kullanım | ✅ Bir kez al, her yerde kullan | ❌ Her uni için tekrar |
| Hazırlık materyali | Bol, internette ücretsiz | Sınırlı, uniye göre |

## Hangi Sınavı Seçmeliyim?

**TestDaF tercih et:** Eğer Türkiye'den başvuru yapıyorsan, birden fazla üniversiteye başvuracaksan veya henüz Almanya'da değilsen.

**DSH tercih et:** Eğer hâlâ Almanya'da değilsen, dil kursuna gidiyor ve hedef üniversiteni belirlediysen — DSH zaten o uninin Studienkolleg'inde verilir.

## Pratik İpucu

> Çoğu Türk öğrenci için en mantıklı yol: önce Türkiye'de TestDaF al, kabul mektubunu alıp Almanya'ya git. Eğer ilk başvuruda TestDaF yeterli gelmediyse, Studienkolleg'de DSH'a hazırlan.

Almanya'daki üniversite sistemi hakkında daha fazlasını [başlangıç rehberinden](/blog/almanyada-universite-egitimi-2026-rehberi) okuyabilirsin.
MD;
    }
}

<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (geliş-sonrası serisi): Mezuniyet sonrası iş piyasası gerçeği. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'germany-job-market-reality-after-graduation-for-international-students';
        $body = <<<'MD'
"Almanya'da diploma = iş" değil. Mezuniyet sonrası iş piyasası, gelmeden önce duyduğundan daha zorlu olabilir. Bu yazı, beklentini gerçekçi tutman ve **doğru hazırlanman** için durumu dürüstçe anlatıyor.

## Gerçek: diploma tek başına yetmez
Prestijli bir TU'dan mezun olmak bile seni otomatik işe sokmaz. Son dönemde **Almanlar bile** iş bulmakta zorlanıyor. Firmalar, özellikle mühendislikte, seni **hızla üretken** görmek ister — bu yüzden iki büyük engel öne çıkar: **dil** ve **yerel deneyim eksikliği**.

## Firmalar gerçekte neye bakar?
- **Notlara değil**, pozisyonun istediği **araç/beceri** deneyimine — projeler, tez, açık kaynak ve özellikle [alan-içi Werkstudent/Praktikum](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market).
- **Almanca** (çoğu pozisyonda B2/C1 — [iş için Almanca gerçeği](/tr/blog/german-language-reality-for-jobs-in-germany-the-honest-truth)).
- Yurt dışı iş deneyimi, talep gören özel bir uzmanlığın yoksa genelde fazla değer görmez.

## Ne yapmalı? (okurken başlar)
- **Okurken deneyim biriktir:** Werkstudent, staj, tez projesini gerçek bir problemle ilişkilendir.
- **Almancanı B2→C1'e taşı**; İngilizce master yapsan bile.
- **Network kur:** kariyer fuarları, LinkedIn, şirket etkinlikleri, profesör/araştırma bağlantıları.
- **Mezuniyet sonrası yollar:** İş arama vizesi (Almanya'da iş aramak için süre), nitelikli istihdam için [Blue Card](/tr/blog/germany-blue-card-2026-the-comprehensive-guide-for-graduates); IT için [iş arama rehberi](/tr/blog/germany-it-job-search-2026-a-comprehensive-guide-for-turkish-graduates).
- **Beklentiyi yönet:** ilk iş hayalindeki olmayabilir; deneyim için kapı açan pozisyonu küçümseme.

## Sonuç
Almanya iş piyasası, doğru hazırlanan için hâlâ güçlü fırsatlar sunuyor — ama **diploma + Almanca + yerel deneyim** üçlüsünü kuranlar için. Bunu **okurken** inşa etmeye başla; mezun olunca sıfırdan başlamak çok daha zor. Tüm seri: [geliş-sonrası gerçek hayat rehberi](/tr/blog/germany-life-after-arrival-advice-to-past-self).

---
*Uluslararası öğrenci/mezun deneyimlerinden ve topluluğumuzdan derlenmiştir.*
MD;
        $excerpt = 'Almanya\'da mezuniyet sonrası iş piyasası gerçeği: diploma (prestijli TU bile) tek başına iş garantisi değil, Almanlar bile zorlanıyor; firmalar not değil deneyim/araç + Almanca ister. Okurken nasıl hazırlanmalı.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 7)->exists() ? 7 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 9)->exists() ? 9 : DB::table('categories')->where('slug', 'yasam')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Mezuniyet Sonrası Almanya İş Piyasası Gerçeği (Diploma ≠ İş)',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya\'da Mezuniyet Sonrası İş Bulmak: Gerçekçi Rehber',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'germany-job-market-reality-after-graduation-for-international-students')->delete(); }
};

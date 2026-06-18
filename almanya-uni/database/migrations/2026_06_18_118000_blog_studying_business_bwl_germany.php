<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (eğitim): Almanya'da İşletme (BWL) okumak — uluslararası öğrenci rehberi. Dengeli + dürüst. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'studying-business-administration-bwl-in-germany-international-student-guide';
        $body = <<<'MD'
"Almanya'da İşletme (BWL) okumak uluslararası öğrenci için iyi bir tercih mi?" Lise sonrası en çok sorulan sorulardan biri. Kısa cevap: **iyi bir tercih olabilir — ama İşletme'nin diğer alanlardan farklı, kritik bir özelliği var.** Dürüst ve dengeli bir rehber.

## İşletme Almanya'da iyi bir seçim mi? (nüanslı evet)
- **Artısı:** Güçlü ekonomi, çok sayıda program (Uni + FH), staj/Werkstudent imkânı, devlet üniversitelerinde **harçsız** eğitim.
- **Ama (en önemli nokta):** İşletme/business işleri, mühendislik veya CS'ye kıyasla **çok daha fazla Almanca'ya bağlıdır.** Alman iş dünyası BWL rollerinde günlük işi **Almanca** yürütür.
- **Sonuç:** Almanya'da **uzun vadede kariyer** hedefliyorsan, **sadece İngilizce** bir BWL Bachelor riskli olabilir — mezuniyette tam-zamanlı iş ararken Almanca eksikliği büyük engel olur. Bu yüzden: ya **Almanca** oku, ya da İngilizce okuyup **B2-C1 Almanca'yı paralel** ciddiyetle yükselt. (Detay: [Almanca bilmeden okumak/yaşamak gerçeği](/tr/blog/studying-in-germany-without-german-living-and-student-job-reality).)

## Başvuru sürecinde karşılaşacağın zorluklar
1. **Diploma denkliği:** Lise diploman Almanya'da doğrudan üniversiteye yetiyor mu? Bunu **Anabin** ile kontrol et; yetmiyorsa **Studienkolleg** gerekebilir. (Bkz. [Anabin H+/H-/H+- nedir](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma).)
2. **Bloke hesap (Sperrkonto):** Vize için zorunlu; 2025'te yıllık ~**11.904 €**. (Tam rehber: [Sperrkonto](/tr/blog/sperrkonto-2025-complete-guide-blocked-account-for-germany-visa).)
3. **Dil belgesi:** İngilizce program için IELTS/TOEFL; Almanca program için TestDaF/DSH.
4. **Rekabet:** BWL **çok popüler** — özellikle İngilizce programlarda dünyanın her yerinden adayla yarışırsın. Birçok BWL programı **NC'lidir** (kontenjan sınırı).
5. **uni-assist:** Çoğu başvuru buradan geçer; belge/çeviri/onay süreci zaman alır.

## Burs ve finansal destek
- **Dürüst gerçek:** Lisans için burs **nadirdir**, üzerine plan kurma.
- **BAföG** genelde lisans başında AB-dışı öğrenciye açık değildir; **DAAD** çoğunlukla Master/PhD odaklıdır.
- Yine de bak: vakıf/Begabtenförderung ve alternatifler — [Almanya'da burs & BAföG alternatifleri](/tr/blog/bafog-alternatives-scholarships-in-germany-for-turkish-students).
- **Finansman gerçeği:** Çoğu öğrenci aile desteği veya ülkesinde kredi + **Werkstudent** geliriyle döner. Gerçek maliyet: [öğrenci bütçe gerçeği](/tr/blog/real-cost-of-being-a-student-in-germany-budget-truth).

## Senin durumundaki birine tavsiyeler
- **Almanca'ya en baştan yatırım yap** — BWL'de bu pazarlık konusu değil. Hedef: en az B2.
- **FH'yi ciddi düşün:** Uygulamalı BWL'de FH'ler genelde **daha esnek kabul** ve güçlü staj sunar ([prestij değil, Uni vs FH](/tr/blog/prestige-myth-german-universities-uni-vs-fh-practical-path)).
- **Geniş başvur:** Tek-iki yere değil, çok sayıda Uni/FH'ye; NC'li ve açık-kabul programları karıştır ([program araması](/tr/programs)).
- **Werkstudent/staj planla:** İş tecrübesi BWL'de kariyerin anahtarı ([Werkstudent](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market)).
- **YouTube/online gelir:** Güzel bir ek, ama vize/finansman planını buna dayandırma; Sperrkonto/aile desteği esas olmalı.
- **Güvenilir kaynak kullan:** Sosyal medya/danışman vaatlerine değil, resmî kaynaklara ve gerçek deneyimlere güven.

## Sonuç
Almanya'da İşletme okumak uluslararası öğrenci için **mantıklı** olabilir — yeter ki **Almanca'yı kariyerinin merkezine** koy, **FH dahil geniş başvur**, finansmanı (Sperrkonto + aile/kredi + Werkstudent) gerçekçi planla ve burslara **bel bağlama**. İngilizce başlayabilirsin ama BWL'de uzun vadeli başarı **Almanca** ile gelir. İlgili: [Devlet vs Özel vs FH](/tr/blog/public-vs-private-universities-germany-balanced-comparison) · [Bachelor mı Master mı](/tr/blog/bachelor-vs-master-in-germany-difficulty-language-work-for-internationals) · [şehir mi üniversite mi](/tr/blog/city-vs-university-which-matters-more-in-germany).

---
*Genel rehberdir. Tutarlar, kabul ve dil koşulları programa/yıla göre değişir — resmî kaynaklardan teyit et.*
MD;
        $excerpt = 'Almanya\'da İşletme (BWL) okumak uluslararası öğrenci için iyi mi? Nüanslı evet: BWL diğer alanlardan çok daha fazla Almanca\'ya bağlı — uzun vadeli kariyer için sadece İngilizce Bachelor riskli, B2-C1 şart. Başvuru zorlukları (Anabin denkliği, Sperrkonto ~11.904€, NC, uni-assist), burslar nadir, FH\'yi düşün, geniş başvur, finansmanı gerçekçi planla.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 4)->exists() ? 4 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 1)->exists() ? 1 : DB::table('categories')->where('slug', 'almanyada-egitim')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'da İşletme (BWL) Okumak: Uluslararası Öğrenci Rehberi',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya\'da İşletme (BWL) Okumak: Dil, Başvuru, Burs Rehberi',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'studying-business-administration-bwl-in-germany-international-student-guide')->delete(); }
};

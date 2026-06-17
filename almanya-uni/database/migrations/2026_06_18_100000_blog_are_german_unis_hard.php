<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (geliş-sonrası serisi): Alman üniversiteleri gerçekten zor mu? Eleme süreci gerçeği. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'are-german-universities-hard-for-international-students-the-weeding-out-truth';
        $body = <<<'MD'
"Alman üniversitelerinde ilk yıl yarısını eler", "mühendislikte herkes kalır", "UK/lise bilgin yetmez" — bu korku hikâyelerini çok duyarsın. Peki gerçek ne? Kısa cevap: **eleme (weeding out) gerçektir ama yanlış anlaşılır.** Konu konunun imkânsız zorluğu değil; **sistemin seni yetişkin gibi tek başına bırakması.** Bu yazı, uluslararası öğrenci deneyimlerine dayanarak durumu dürüstçe + nasıl başa çıkacağınla anlatıyor.

## Asıl "eleme": rehbersiz, kendin-yönet sistem
En büyük fark zorluk değil, **özgürlük ve sonuçları.** Alman üniversiteleri (özellikle klasik Uni'ler) seni **yetişkin** kabul eder:
- Kendi **ders programını** sen kurarsın; hangi modül hangi dönem, ön koşullar, kontenjanlar…
- **Sınav kaydını** sen yaparsın, ne zaman/nasıl olduğunu sen araştırırsın.
- Sınava ne çalışacağını **çoğunlukla sana söylemezler** — neyin önemli olduğunu kendin çıkarman beklenir.
- Ödev/sunum/ makale formatlarını kendin öğrenirsin.

UK gibi "her şey planlı, hoca adım adım yönlendirir" sistemden gelenler için asıl zorluk budur — konular değil, **kendi kendini organize etmek.**

## Orientierungsprüfung (OP) ve ilk-yıl filtre dersleri
- Bazı bölümlerde ilk **2-3 dönemde** geçmen gereken **Orientierungsprüfung (OP)** vardır; geçemezsen o bölümü **Almanya genelinde** okuyamazsın.
- Mühendislikte Mathe / Technische Mechanik gibi dersler bilinçli **filtre**dir; bazı kohortlarda **~%50** ilk yılı geçemez — ve bu **sadece yabancılar değil, Almanlar da** kapsar.
- Önemli gerçek: TU Braunschweig'de bir eğitmenin aktardığı gibi, güçlü **çalışma disiplini** olan uluslararası öğrenciler bu sınavları geçerken, Almanlar ilk denemede sık sık kalabiliyor. Yani mesele **milliyet değil, disiplin.**

## Dil
Bachelor genelde **Almanca**. İngilizce Master'lar var ama **idare/yönetmelik çoğu zaman Almanca** yürür (resmi kural kitabı bile İngilizce olmayabilir). Bkz. [iş & hayatta Almanca gerçeği](/tr/blog/german-language-reality-for-jobs-in-germany-the-honest-truth).

## Uni mi, Hochschule (FH) mi? — rehberlik farkı
Tam bağımsızlıktan hoşlanmıyorsan: **Hochschule / Technische Hochschule / Fachhochschule (HAW)** genelde **daha okul-benzeri, daha çok rehberlik** sunar; ders planı ve destek daha yapılandırılmıştır. Klasik Uni daha araştırma-odaklı ve bağımsızdır. (RWTH gibi bazıları isminde "Hochschule" geçse de üniversitedir.) Hangisinin sana uyduğunu, [şehir mi üniversite mi](/tr/blog/city-vs-university-which-matters-more-in-germany) kararıyla birlikte düşün.

## Nasıl başa çıkılır? (Kalanların değil, geçenlerin yaptığı)
Geçemeyenler genelde "ben zaten biliyorum" deyip **oryantasyonlara/yardıma gitmeyenler**. Sen tersini yap:
- İlk dönem **tüm oryantasyon** etkinliklerine git (uni nasıl işliyor, sınav kaydı, modüller).
- **Tutorien** (alıştırma saatleri — bazıları zorunlu) ve profesör **Sprechstunde** (ofis saatleri) kullan.
- **Kime soracağını** öğren — bilgi dağınıktır, doğru kişiye ulaşmak yarı çözümdür.
- Sınav öncesi **hazırlık/Brückenkurs** kurslarını atlama.
- Disiplinli bir çalışma rutini kur ([yalnızlık & rutin](/tr/blog/loneliness-and-mental-health-as-an-international-student-in-germany)).

## Master genelde daha rahat
Çok öğrenci master'ı bachelor'dan **daha kolay** buluyor: derslere git, slaytları/notları düzgün çalış, sınava gir — kesişen, acımasız rekabet yok. Bachelor'da entegrasyon da bazen daha kolaydır çünkü **herkes yenidir.**

## Bilgini ölçmek için ücretsiz kaynak?
"Yeterli miyim" diye bakmak istersen: üniversitelerin dönem öncesi **Vorkurs/Brückenkurs Mathematik** materyalleri, MIT OpenCourseWare, Khan Academy ve bölümlerin yayınladığı örnek sınavlar iyi bir ön-kontrol sağlar.

## Sonuç
Alman üniversiteleri **imkânsız değil**, ama seni **kendi kendine öğrenen** biri olmaya zorlar. Disiplinin varsa, oryantasyon/tutoring/ofis saatlerini kullanırsan ve gerekiyorsa daha rehberli bir **Hochschule** seçersen, eleme süreci fazlasıyla aşılabilir. Düz A öğrencisiysen ve çalışmaya gönüllüysen — yapılabilir. Devamı: [geliş-sonrası gerçek hayat rehberi](/tr/blog/germany-life-after-arrival-advice-to-past-self).

---
*Uluslararası öğrenci ve mezun deneyimlerinden derlenmiştir. Her üniversite/bölüm farklıdır.*
MD;
        $excerpt = 'Alman üniversiteleri uluslararası öğrenciler için gerçekten zor mu? Asıl "eleme" konuların zorluğu değil, kendin-yönet sistem + Orientierungsprüfung + ilk-yıl filtre dersleri. Almanlar da kalıyor; mesele disiplin. Uni vs Hochschule farkı ve nasıl başa çıkılır.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 6)->exists() ? 6 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 1)->exists() ? 1 : DB::table('categories')->where('slug', 'almanyada-egitim')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Alman Üniversiteleri Uluslararası Öğrenciler İçin Gerçekten Zor mu? (Eleme Süreci Gerçeği)',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Alman Üniversiteleri Zor mu? Eleme Süreci & Nasıl Başa Çıkılır',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'are-german-universities-hard-for-international-students-the-weeding-out-truth')->delete(); }
};

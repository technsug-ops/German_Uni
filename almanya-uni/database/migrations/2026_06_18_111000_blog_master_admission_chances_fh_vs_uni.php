<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** TR blog (başvuru): İngilizce Master kabul şansı — GPA, FH vs Uni, akıllı başvuru stratejisi. FK-safe. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'english-master-admission-chances-germany-gpa-fh-vs-uni-strategy';
        $body = <<<'MD'
"2.4-2.5 not ortalamasıyla Almanya'da İngilizce CS (veya başka) Master'a gerçek şansım ne?" — şekerleme istemeden, çıplak gerçeği konuşalım. İyi haber: Almanya'da master kabulü, sandığından **daha tahmin edilebilir**; çünkü "izlenim/sunum" değil, **net kurallar ve sayılar** üzerine kuruludur.

## Çirkin gerçek 1: Kabul "holistic" değil, kural-bazlı
ABD'nin aksine Almanya'da master kabulü **programa özel sert kurallarla** işler:
- Her programın kendi **hard requirements**'ı vardır (not eşiği, konu-özel modüller, dil belgesi). Karşılamıyorsan **otomatik elenirsin** — ne kadar "ilginç bir profilin" olursa olsun.
- **Extracurricular / staj / proje**, başvuru formunda bir alan yoksa genelde **sayılmaz.** Motivasyon mektubu bazı programlarda değerlidir, bazılarında hiç okunmaz.
- Yani strateji: **hangi programın tam olarak neyi istediğini oku**, eksiksiz karşıla, başvur. Emin değilsen programın **International Office**'una sor.

## Çirkin gerçek 2: Not ortalaması (GPA) eşikleri
- Çoğu **Universität**, otomatik filtrede **~2.5 veya daha iyi** ister; 2.5 seni birçok yerde **eşikten geçirir** ama garanti etmez.
- **Tepe üniversiteler** (TUM, RWTH, HPI/Potsdam gibi) çok daha yüksek rekabet → 2.4-2.5 ile genelde **reach** (zorlu). Buralara "şansımı deneyeyim" diye başvuru ücreti yakma; gerçekçi ol.
- **Anabin H+** denklik ve **konu-özel uygunluk** (çekirdek CS modülleri: OS, mimari, DB, ağlar, SE) çoğu programda kapıyı açar — bu yöndeysen avantajlısın. (Bkz. [Anabin H+/H-/H+- nedir](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma).)

## Çirkin gerçek 3: FH (Hochschule) = akıllı güvenli bahis
**Fachhochschule / HAW (Universities of Applied Sciences)**, uygulamalı CS'de genelde **daha yüksek kabul oranı** ve daha esnek not eşikleri sunar — orta GPA'li uluslararası öğrenci için **güvenli liman**. Diploma "Uni" değil "FH" yazsa da iş piyasasında uygulamalı beceri çok değerlidir ([Werkstudent gerçeği](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market)). Uni-FH farkı ve rehberlik için: [Alman üniversiteleri zor mu](/tr/blog/are-german-universities-hard-for-international-students-the-weeding-out-truth).

## Akıllı başvuru stratejisi: Reach / Target / Safe
1. **Geniş başvur** — tek-iki programa değil, **10-15+** programa. Almanya'da bazı programlar kontenjanını bile dolduramıyor.
2. **Üç kategoriye böl:**
   - **Reach** (zorlu): TUM, RWTH, HPI…
   - **Target** (gerçekçi): not eşiğini rahat geçtiğin orta Uni'ler.
   - **Safe** (güvenli): iyi İngilizce CS veren **FH'ler**.
3. **Geçen dönemin rakamlarına bak:** program "aday > kontenjan" durumundaysa bir **seçim mekanizması** (NC/puanlama) vardır → şansını hesapla. Sürekli boş kalan programlarda sadece şartları karşılamak çoğu zaman yeter.
4. Üniversiteleri/programları karşılaştır: [üniversiteler](/tr/universities) ve [program araması](/tr/programs?language=en) (İngilizce programlar).

## Gerçek örnek
2.9 GPA'li bir GUC mezunu, TU Chemnitz Embedded Systems'a kabul aldı (yüksek kabul oranlı, güvenli plan). 2.4-2.5 senin için daha iyi → **doğru karışımla birden fazla kabul** alman mümkün.

## Sonuç
2.4-2.5 ile Almanya'da İngilizce Master **kesinlikle mümkün** — ama "izlenim"e değil, **kurallara ve sayılara** oyna: hard requirements'ı eksiksiz karşıla, **FH'leri güvenli bahis** olarak kullan, **çok ve katmanlı (reach/target/safe) başvur**, tepe üniversitelere gerçekçi yaklaş. İlgili: [İngilizce Master tam rehber](/tr/blog/english-masters-in-germany-without-german-your-complete-guide) · [uni-assist A-Z](/tr/blog/uni-assist-application-guide-a-z-your-step-by-step-path) · [şartlı kabul](/tr/blog/germany-conditional-admission-bedingte-zulassung-guide).

---
*Uluslararası öğrenci deneyimlerinden derlenmiştir. Eşikler ve kontenjanlar programa/döneme göre değişir — resmi programdan teyit et.*
MD;
        $excerpt = '2.4-2.5 GPA ile Almanya\'da İngilizce Master\'a gerçek kabul şansın: Almanya\'da kabul holistic değil sert-kural bazlı, GPA eşikleri (~2.5), Anabin H+, FH vs Uni stratejisi ve reach/target/safe ile 10-15+ geniş başvuru. Çirkin gerçek + akıllı plan.';
        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $userId = DB::table('users')->where('id', 4)->exists() ? 4 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 8)->exists() ? 8 : DB::table('categories')->where('slug', 'basvuru')->value('id');
        $payload = ['locale' => 'tr', 'user_id' => $userId, 'category_id' => $categoryId,
            'title' => 'Almanya\'da İngilizce Master Kabul Şansın: Not Ortalaması (GPA), FH vs Uni ve Akıllı Başvuru',
            'excerpt' => Str::limit($excerpt, 250, '…'), 'content_md' => $body, 'content_html' => $html,
            'meta_title' => 'Almanya İngilizce Master Kabul Şansı: GPA Eşiği, FH vs Uni Stratejisi',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published' => true, 'published_at' => now()];
        $existing = Post::where('slug', $slug)->first();
        if ($existing) { $existing->update($payload); } else { Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]); }
    }
    public function down(): void { Post::where('slug', 'english-master-admission-chances-germany-gpa-fh-vs-uni-strategy')->delete(); }
};

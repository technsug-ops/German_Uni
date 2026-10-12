<?php

namespace Tests\Feature;

use App\Support\LegacyPaths;
use App\Support\SlugRedirects;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Eski URL → son URL tek adımda: locale'siz eski takma adlar, önceki turların slug'ları ve dil bazlı FAQ
 * yönlendirmeleri zincir kurmaz; bilinmeyen yol gerçek 404; sorgu dizesi korunur.
 */
class LegacyRedirectChainTest extends TestCase
{
    use RefreshDatabase;

    private function seedRedirects(): void
    {
        $topic = DB::table('faq_topics')->insertGetId(['name' => 'Visa', 'slug' => 'visa', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('fields_of_study')->insert(['name_tr' => 'Bilişim', 'name_en' => 'Computer Science', 'name_de' => 'Informatik', 'slug' => 'computer-science', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('categories')->insert(['name' => 'Application', 'name_en' => 'Application', 'slug' => 'application', 'kind' => 'blog', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        foreach ([['tr', 'how-to-get-a-visa', 'Vize nasıl alınır?'], ['en', 'how-to-get-a-visa-en', 'How to get a visa?'], ['de', 'how-to-get-a-visa-de', 'Wie bekomme ich ein Visum?']] as [$loc, $slug, $q]) {
            DB::table('faqs')->insert(['locale' => $loc, 'slug' => $slug, 'question' => $q, 'answer_md' => 'Cevap ' . $loc, 'answer_html' => '<p>Cevap ' . $loc . '</p>', 'has_answer' => 1, 'is_published' => 1,
                'translation_group_id' => '0b4b4b4b-0000-4000-8000-0000000000aa', 'faq_topic_id' => $topic, 'created_at' => now(), 'updated_at' => now()]);
        }
        $now = now();
        DB::table('slug_redirects')->insert([
            ['entity' => 'faq_topic', 'locale' => null, 'from_slug' => 'vize', 'to_slug' => 'visa', 'created_at' => $now, 'updated_at' => $now],
            ['entity' => 'field', 'locale' => null, 'from_slug' => 'bilisim', 'to_slug' => 'computer-science', 'created_at' => $now, 'updated_at' => $now],
            ['entity' => 'category', 'locale' => null, 'from_slug' => 'basvuru', 'to_slug' => 'application', 'created_at' => $now, 'updated_at' => $now],
            ['entity' => 'faq', 'locale' => 'tr', 'from_slug' => 'vize-nasil-alinir', 'to_slug' => 'how-to-get-a-visa', 'created_at' => $now, 'updated_at' => $now],
            ['entity' => 'faq', 'locale' => 'en', 'from_slug' => 'vize-nasil-alinir-en', 'to_slug' => 'how-to-get-a-visa-en', 'created_at' => $now, 'updated_at' => $now],
            ['entity' => 'faq', 'locale' => 'de', 'from_slug' => 'vize-nasil-alinir-de', 'to_slug' => 'how-to-get-a-visa-de', 'created_at' => $now, 'updated_at' => $now],
            // Prod'da görülen durum: eski EN slug'ı = yeni TR slug'ı → dil bilinmeden zincir TR'yi EN'e taşırdı
            ['entity' => 'faq', 'locale' => 'tr', 'from_slug' => 'eski-tr', 'to_slug' => 'how-to-get-a-visa', 'created_at' => $now, 'updated_at' => $now],
            ['entity' => 'faq', 'locale' => 'en', 'from_slug' => 'how-to-get-a-visa', 'to_slug' => 'how-to-get-a-visa-en', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function test_locale_aware_resolution_does_not_cross_languages(): void
    {
        $this->seedRedirects();
        $this->assertSame('how-to-get-a-visa', SlugRedirects::to('faq', 'eski-tr', 'tr'), 'TR zinciri EN slug\'ına atlamaz');
        $this->assertSame('how-to-get-a-visa-en', SlugRedirects::to('faq', 'how-to-get-a-visa', 'en'));
        $this->assertNull(SlugRedirects::to('faq', 'how-to-get-a-visa', 'tr'), 'TR için bu slug zaten güncel');
        $this->assertSame('visa', SlugRedirects::to('faq_topic', 'vize'));
    }

    public function test_legacy_paths_resolve_aliases_and_slugs_together(): void
    {
        $this->seedRedirects();
        $this->assertSame('faq/visa/how-to-get-a-visa', LegacyPaths::resolve('sss/vize/vize-nasil-alinir', 'tr'));
        $this->assertSame('faq/visa/how-to-get-a-visa-en', LegacyPaths::resolve('sss/vize/vize-nasil-alinir-en', 'en'));
        $this->assertSame('faq/visa', LegacyPaths::resolve('sss/vize', 'tr'));
        $this->assertSame('faq', LegacyPaths::resolve('sss', 'tr'));
        $this->assertSame('blog/category/application', LegacyPaths::resolve('blog/kategori/basvuru', 'tr'));
        $this->assertSame('subjects/computer-science/nc-free', LegacyPaths::resolve('subjects/bilisim/nc-free', 'en'));
        $this->assertSame('programs/field/computer-science/degree/master', LegacyPaths::resolve('programs/field/bilisim/degree/master', 'en'));
        $this->assertSame('tools/cost-of-living', LegacyPaths::resolve('araclar/yasam-maliyeti', 'tr'));
        $this->assertSame('author/halil', LegacyPaths::resolve('yazar/halil', 'tr'));
        $this->assertNull(LegacyPaths::resolve('faq/visa/how-to-get-a-visa', 'tr'), 'güncel yol değişmez');
        $this->assertNull(LegacyPaths::resolve('completely/unknown', 'tr'));
    }

    public function test_old_locale_less_urls_reach_the_final_url_in_one_hop(): void
    {
        $this->seedRedirects();
        $r = $this->get('/sss/vize/vize-nasil-alinir-en?utm_source=x');
        $r->assertStatus(301);
        $this->assertStringEndsWith('/faq/visa/how-to-get-a-visa-en?utm_source=x', $r->headers->get('Location'));
        $this->assertStringContainsString('/en/faq/', $r->headers->get('Location'), 'slug eki -en → dil ipucu en');
        $this->get('/sss/vize/vize-nasil-alinir')->assertStatus(301)->assertRedirect(url('/tr/faq/visa/how-to-get-a-visa'));
        $this->get('/blog/kategori/basvuru')->assertStatus(301)->assertRedirectContains('/blog/category/application');
        $this->get('/araclar/yasam-maliyeti')->assertStatus(301)->assertRedirectContains('/tools/cost-of-living');
        // locale'li eski takma ad da tek adımda
        $this->get('/tr/sss/vize/vize-nasil-alinir')->assertStatus(301)->assertRedirect(url('/tr/faq/visa/how-to-get-a-visa'));
        $this->get('/en/faq/vize/vize-nasil-alinir-en')->assertStatus(301)->assertRedirect(url('/en/faq/visa/how-to-get-a-visa-en'));
        // locale'siz ama güncel yol: yalnız dil normalizasyonu (302), slug değişmez
        $this->get('/faq/visa/how-to-get-a-visa-en')->assertStatus(302)->assertRedirectContains('/faq/visa/how-to-get-a-visa-en');
        // eski TR slug'ı EN adresinde: dilin kardeşine tek adımda (eski FaqController davranışı korunur)
        $this->get('/en/faq/visa/vize-nasil-alinir')->assertStatus(301)->assertRedirect(url('/en/faq/visa/how-to-get-a-visa-en'));
        $this->get('/en/faq/vize/vize-nasil-alinir')->assertStatus(301)->assertRedirect(url('/en/faq/visa/how-to-get-a-visa-en'));
        // bilinmeyen: gerçek 404
        $this->get('/en/faq/visa/no-such-question')->assertNotFound();
        $this->get('/en/sss/olmayan-konu/olmayan-soru')->assertNotFound();
        $this->get('/en/fields/olmayan-alan')->assertNotFound();
        // yeni hedefler 200
        $this->get('/en/faq/visa/how-to-get-a-visa-en')->assertOk()->assertSee('Cevap en');
        $this->get('/tr/faq/visa/how-to-get-a-visa')->assertOk()->assertSee('Cevap tr');
    }
}

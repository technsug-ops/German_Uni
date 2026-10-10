<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

/**
 * Yazar Şule Demirel: tek profil, LinkedIn tam adresi bozulmadan profilde ve BlogPosting şemasında (author.url,
 * sameAs, publisher). Kısa LinkedIn kullanıcı adı saklanan eski yazarlar aynı çalışmaya devam eder.
 */
class AuthorSuleDemirelTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_10_10_000700_add_author_sule_demirel.php';

    private const LINKEDIN = 'https://www.linkedin.com/in/%C5%9Fule-demirel-415a66116/';

    public function test_migration_adds_one_author_profile_and_rerun_is_a_noop(): void
    {
        $this->assertSame(1, User::where('slug', 'sule-demirel')->count(), 'RefreshDatabase migration\'ı çalıştırdı');
        $before = DB::table('users')->where('slug', 'sule-demirel')->first();

        (require base_path(self::MIGRATION))->up();

        $this->assertEquals($before, DB::table('users')->where('slug', 'sule-demirel')->first());
        $u = User::where('slug', 'sule-demirel')->first();
        $this->assertSame('Şule Demirel', $u->name);
        $this->assertTrue((bool) $u->is_author);
        $this->assertSame(['linkedin' => self::LINKEDIN], $u->social_links);
        // Ham kolonlar: User modeli bio/role_label'ı aktif dile göre çevirir (CI'da varsayılan dil EN).
        $row = DB::table('users')->where('slug', 'sule-demirel')->first();
        $this->assertStringStartsWith('Şule Demirel, ApplyToGerman\'da Türkiye Öğrenci Direktörü.', $row->bio, 'onaylı biyografi');
        $this->assertStringContainsString('DreamToMove', $row->bio_en);
        $this->assertStringContainsString('DreamToMove', $row->bio_de);
        $this->assertStringContainsString('DreamToMove', $this->get('/tr/author/sule-demirel')->getContent());
        $this->assertNull($u->years_experience);
    }

    public function test_slug_taken_by_someone_else_fails(): void
    {
        DB::table('users')->where('slug', 'sule-demirel')->update(['name' => 'Başka Kişi']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('başka bir kişiye ait');
        (require base_path(self::MIGRATION))->up();
    }

    public function test_author_page_links_linkedin_as_given(): void
    {
        $html = $this->get('/tr/author/sule-demirel')->assertOk()->getContent();

        $this->assertStringContainsString('Şule Demirel', $html);
        $this->assertStringContainsString('href="' . self::LINKEDIN . '"', $html);
        $this->assertStringNotContainsString('linkedin.com/in/https', $html);
        $this->assertMatchesRegularExpression('/<span>LinkedIn<\/span>/', $html, 'buton okunur etiketle');
        $visible = strip_tags(preg_replace('/<script\b.*?<\/script>/s', '', $html));
        $this->assertStringNotContainsString('%C5%9Fule', $visible, 'kodlanmış adres görünür metinde yok (şemada olması doğru)');
    }

    public function test_director_title_moves_her_above_contributors(): void
    {
        $row = DB::table('users')->where('slug', 'sule-demirel')->first();
        $this->assertSame('Türkiye Öğrenci Direktörü', $row->role_label);
        $this->assertSame('Director, Students from Türkiye', $row->role_label_en);
        $this->assertSame('Direktorin für Studierende aus der Türkei', $row->role_label_de);

        $html = $this->get('/tr/team')->assertOk()->getContent();
        $name = mb_strpos($html, 'Şule Demirel');
        // Bölüm başlığı (sayfa <title>'ı değil); başka katkı sağlayan yoksa bölüm hiç basılmaz.
        $contributors = mb_strpos($html, 'Katkı Sağlayanlar</h2>');
        $this->assertNotFalse($name);
        $this->assertStringContainsString('Türkiye Öğrenci Direktörü', $html);
        $this->assertTrue($contributors === false || $name < $contributors, 'katkı sağlayanların üstünde, editör/direktör grubunda');
    }

    public function test_team_page_card_links_to_author_profile(): void
    {
        $html = $this->get('/tr/team')->assertOk()->getContent();

        $this->assertStringContainsString('href="' . route('author.show', 'sule-demirel') . '"', $html);
    }

    private function postBy(User $u, string $slug): void
    {
        DB::table('posts')->insert(['locale' => 'tr', 'user_id' => $u->id, 'title' => 'Test yazısı ' . $slug, 'slug' => $slug,
            'content_md' => 'Metin.', 'content_html' => '<p>Metin.</p>', 'excerpt' => 'Özet.', 'meta_description' => 'Açıklama.',
            'is_published' => 1, 'published_at' => now()->subDay(), 'created_at' => now(), 'updated_at' => now()]);
    }

    private function blogPosting(string $slug): array
    {
        $html = $this->get("/tr/blog/{$slug}")->assertOk()->getContent();
        $this->assertStringNotContainsString('linkedin.com/in/https', $html);
        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $m);

        return collect($m[1])->map(fn ($j) => json_decode($j, true))->firstWhere('@type', 'BlogPosting');
    }

    public function test_blog_posting_schema_matches_visible_author(): void
    {
        $this->postBy(User::where('slug', 'sule-demirel')->first(), 'b-sule-test');

        $schema = $this->blogPosting('b-sule-test');

        $this->assertSame('Person', $schema['author']['@type']);
        $this->assertSame('Şule Demirel', $schema['author']['name']);
        $this->assertSame(route('author.show', 'sule-demirel'), $schema['author']['url']);
        $this->assertSame([self::LINKEDIN], $schema['author']['sameAs']);
        $this->assertSame(brand('name'), $schema['publisher']['name']);
    }

    public function test_linkedin_handle_still_becomes_profile_url(): void
    {
        $id = DB::table('users')->insertGetId(['name' => 'Handle Yazar', 'slug' => 'handle-yazar', 'email' => Str::random(8) . '@example.invalid',
            'password' => 'x', 'is_author' => true, 'social_links' => json_encode(['linkedin' => 'handle-yazar']), 'created_at' => now(), 'updated_at' => now()]);
        $this->postBy(User::find($id), 'b-handle-test');

        $this->assertSame(['https://linkedin.com/in/handle-yazar'], $this->blogPosting('b-handle-test')['author']['sameAs']);
    }
}

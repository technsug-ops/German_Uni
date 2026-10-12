<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\MathCaptcha;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * MathCaptcha: cevap istemciye gitmez; referans oturuma bağlı, süreli ve tek kullanımlık.
 */
class MathCaptchaTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_reference_does_not_carry_the_answer(): void
    {
        $c = MathCaptcha::generate();
        $answer = (string) ($c['a'] + $c['b']);
        // Referans rastgele alfasayısal bir dizidir; cevabı, süreyi ya da imza yapısını taşımaz.
        // (Rastgele dizinin base64 çözümü tesadüfen rakam içerebileceği için çözülmüş hali test edilmez.)
        $this->assertSame(0, preg_match('/[^A-Za-z0-9]/', $c['key']));
        $this->assertSame(40, strlen($c['key']));
        $this->assertSame($c['a'] + $c['b'], session()->get(MathCaptcha::SESSION_KEY)[$c['key']]['answer'], 'cevap yalnız oturumda');
    }

    public function test_correct_answer_passes_once_and_is_not_replayable(): void
    {
        $c = MathCaptcha::generate();
        $this->assertTrue(MathCaptcha::validate($c['key'], $c['a'] + $c['b']));
        $this->assertFalse(MathCaptcha::validate($c['key'], $c['a'] + $c['b']), 'tek kullanımlık: aynı referans ikinci kez geçmez');
    }

    public function test_wrong_missing_and_tampered_references_fail(): void
    {
        $c = MathCaptcha::generate();
        $this->assertFalse(MathCaptcha::validate($c['key'], $c['a'] + $c['b'] + 1), 'yanlış cevap');
        $this->assertFalse(MathCaptcha::validate(null, $c['a'] + $c['b']), 'referans yok');
        $this->assertFalse(MathCaptcha::validate('', $c['a'] + $c['b']));
        $this->assertFalse(MathCaptcha::validate(substr($c['key'], 0, 39) . 'x', $c['a'] + $c['b']), 'değiştirilmiş referans');
        $this->assertFalse(MathCaptcha::validate(str_repeat('a', 40), $c['a'] + $c['b']), 'uydurma referans');
        $this->assertTrue(MathCaptcha::validate($c['key'], $c['a'] + $c['b']), 'yanlış denemeden sonra doğru cevap hâlâ geçer (TTL içinde)');
    }

    public function test_expired_reference_fails(): void
    {
        $c = MathCaptcha::generate();
        $pending = session()->get(MathCaptcha::SESSION_KEY);
        $pending[$c['key']]['expires'] = time() - 1;
        session()->put(MathCaptcha::SESSION_KEY, $pending);
        $this->assertFalse(MathCaptcha::validate($c['key'], $c['a'] + $c['b']));
    }

    public function test_reference_from_another_session_is_rejected(): void
    {
        $c = MathCaptcha::generate();
        session()->flush(); // başka oturum: bu referansı bilmez
        $this->assertFalse(MathCaptcha::validate($c['key'], $c['a'] + $c['b']));
    }

    public function test_multiple_pending_captchas_coexist_and_old_ones_are_pruned(): void
    {
        $first = MathCaptcha::generate();
        $second = MathCaptcha::generate();
        $this->assertTrue(MathCaptcha::validate($second['key'], $second['a'] + $second['b']));
        $this->assertTrue(MathCaptcha::validate($first['key'], $first['a'] + $first['b']), 'aynı sayfadaki ikinci captcha ilkini bozmaz');
        for ($i = 0; $i < MathCaptcha::MAX_PENDING + 5; $i++) {
            MathCaptcha::generate();
        }
        $this->assertLessThanOrEqual(MathCaptcha::MAX_PENDING, count(session()->get(MathCaptcha::SESSION_KEY)));
    }

    public function test_login_still_works_with_valid_captcha_and_blocks_without_it(): void
    {
        $user = User::factory()->create(['password' => Hash::make('secret-password-123')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'secret-password-123', 'captcha_key' => 'bogus', 'captcha_answer' => 7])
            ->assertSessionHasErrors('captcha_answer');
        $this->assertGuest();

        $this->post('/login', ['email' => $user->email, 'password' => 'secret-password-123', ...$this->captcha()])
            ->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_form_renders_question_without_answer_in_markup(): void
    {
        $html = $this->get('/login')->assertOk()->getContent();
        $this->assertStringContainsString('name="captcha_key"', $html);
        preg_match('/name="captcha_key" value="([^"]+)"/', $html, $m);
        $this->assertSame(40, strlen($m[1] ?? ''));
        preg_match('/(\d) \+ (\d)/', $html, $q);
        $this->assertNotEmpty($q, 'soru görünür');
        $answer = (int) $q[1] + (int) $q[2];
        $this->assertStringNotContainsString('value="' . $answer . '"', $html);
    }
}

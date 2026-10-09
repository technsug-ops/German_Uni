<?php

namespace Tests\Feature;

use App\Mail\GeminiAccessAlert;
use App\Services\Rag\ChatService;
use App\Services\Rag\GeminiEmbedder;
use App\Services\Rag\ProgramRetriever;
use App\Services\Rag\Retriever;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Mockery;
use RuntimeException;
use Tests\TestCase;

/**
 * 2026-10-09: Gemini prepaid kredisi bitti → 403 "project denied access"; ChatService hatayı yutup genel mesaj döndüğü
 * için kimse fark etmedi. Erişim reddinde (401/403/429) admin'e e-posta gider, 6 saatte en fazla bir kez.
 */
class ChatServiceAccessAlertTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Cache::forget('gemini_access_alert_sent');
        config(['mail.admin_email' => 'admin@example.test', 'services.gemini.key' => 'test-key']);
    }

    private function service(?\Throwable $embedError = null): ChatService
    {
        $emb = Mockery::mock(GeminiEmbedder::class);
        $embedError
            ? $emb->shouldReceive('embedOne')->andThrow($embedError)
            : $emb->shouldReceive('embedOne')->andReturn(array_fill(0, 8, 0.1));
        $adv = Mockery::mock(Retriever::class);
        $adv->shouldReceive('retrieve')->andReturn(['top' => 0.8, 'results' => [
            ['title' => 'Sperrkonto', 'url' => '/tr/faq/sperrkonto', 'score' => 0.8, 'content' => 'Sperrkonto bilgisi.'],
        ]]);
        $prog = Mockery::mock(ProgramRetriever::class);
        $prog->shouldReceive('retrieve')->andReturn(['top' => 0.0, 'results' => []]);

        return new ChatService($adv, $prog, $emb);
    }

    public function test_embed_403_sends_one_alert_per_window(): void
    {
        $svc = $this->service(new RuntimeException('Embed HTTP 403: {"error":{"message":"Your project has been denied access."}}'));

        $svc->ask('Sperrkonto ne kadar?');
        $svc->ask('Sperrkonto ne kadar?');

        Mail::assertSent(GeminiAccessAlert::class, 1);
        Mail::assertSent(GeminiAccessAlert::class, fn ($m) => $m->hasTo('admin@example.test') && str_contains($m->detail, '403'));
    }

    public function test_chat_429_sends_alert(): void
    {
        Http::fake(['*' => Http::response(['error' => ['status' => 'RESOURCE_EXHAUSTED']], 429)]);

        $out = $this->service()->ask('Sperrkonto ne kadar?');

        $this->assertStringContainsString('Teknik bir sorun', $out['answer']);
        Mail::assertSent(GeminiAccessAlert::class, 1);
    }

    public function test_transient_errors_do_not_alert(): void
    {
        $this->service(new RuntimeException('Embed HTTP 503: overloaded'))->ask('Sperrkonto ne kadar?');

        Http::fake(['*' => Http::response('oops', 500)]);
        $this->service()->ask('Sperrkonto ne kadar?');

        Mail::assertNothingSent();
    }
}

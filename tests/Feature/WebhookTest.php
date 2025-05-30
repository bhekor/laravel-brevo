<?php

namespace Bhekor\LaravelBrevo\Tests\Feature;

use Bhekor\LaravelBrevo\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;

/**
 * @group feature
 */
class WebhookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['brevo.webhook.secret' => 'test-secret']);

        Route::brevoWebhooks('/test-webhook');
    }

    /** @test */
    public function it_processes_valid_webhook_request()
    {
        Event::fake();

        $payload = [
            'event' => 'delivered',
            'email' => 'test@example.com',
            'date' => now()->toISOString()
        ];

        $signature = hash_hmac('sha256', json_encode($payload), 'test-secret');

        $response = $this->postJson('/test-webhook', $payload, [
            'X-Brevo-Signature' => $signature
        ]);

        $response->assertStatus(200);
        Event::assertDispatched('Bhekor\LaravelBrevo\Events\EmailDelivered');
    }

    /** @test */
    public function it_rejects_invalid_webhook_signature()
    {
        $response = $this->postJson('/test-webhook', [
            'event' => 'delivered'
        ], [
            'X-Brevo-Signature' => 'invalid-signature'
        ]);

        $response->assertStatus(403);
    }
}
<?php

namespace Bhekor\LaravelBrevo\Tests\Unit;

use Bhekor\LaravelBrevo\Tests\TestCase;
use Bhekor\LaravelBrevo\Webhooks\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Mockery;

/**
 * @covers \Bhekor\LaravelBrevo\Webhooks\WebhookController
 */
class WebhookControllerTest extends TestCase
{
    private WebhookController $controller;
    private $mockMapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockMapper = Mockery::mock(\Bhekor\LaravelBrevo\Webhooks\EventMapper::class);
        $this->controller = new WebhookController($this->mockMapper);

        config(['brevo.webhook.secret' => 'test-secret']);
    }

    /** @test */
    public function it_processes_valid_webhook()
    {
        $request = Request::create('/webhook', 'POST', [], [], [], [
            'HTTP_X_Brevo_Signature' => hash_hmac('sha256', json_encode([
                'event' => 'delivered',
                'email' => 'test@example.com'
            ]), 'test-secret')
        ], json_encode([
            'event' => 'delivered',
            'email' => 'test@example.com'
        ]));

        $this->mockMapper->shouldReceive('map')
            ->with('delivered')
            ->andReturn('Bhekor\LaravelBrevo\Events\EmailDelivered');

        Event::fake();

        $response = $this->controller->__invoke($request);

        $this->assertEquals(200, $response->getStatusCode());
        Event::assertDispatched('Bhekor\LaravelBrevo\Events\EmailDelivered');
    }

    /** @test */
    public function it_rejects_invalid_signature()
    {
        $request = Request::create('/webhook', 'POST', [], [], [], [
            'HTTP_X_Brevo_Signature' => 'invalid-signature'
        ], json_encode([
            'event' => 'delivered'
        ]));

        $this->expectException(\Bhekor\LaravelBrevo\Exceptions\BrevoValidationException::class);
        $this->expectExceptionMessage('Invalid webhook signature');

        $this->controller->__invoke($request);
    }
}
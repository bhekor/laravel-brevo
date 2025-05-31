<?php

namespace Bhekor\LaravelBrevo\Tests\Unit;

use Bhekor\LaravelBrevo\Services\BrevoClient;
use Bhekor\LaravelBrevo\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

/**
 * @covers \Bhekor\LaravelBrevo\Services\BrevoClient
 */
class BrevoClientTest extends TestCase
{
    private BrevoClient $client;
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);

        $this->client = new BrevoClient('test-api-key', [
            'handler' => $handlerStack,
        ]);
    }

    /** @test */
    public function it_sends_email_successfully()
    {
        $this->mockHandler->append(new Response(201, [], json_encode([
            'messageId' => 'test-message-id'
        ])));

        $response = $this->client->sendEmail([
            'to' => [['email' => 'test@example.com']],
            'subject' => 'Test Subject'
        ]);

        $this->assertEquals('test-message-id', $response['messageId']);
    }

    /** @test */
    public function it_handles_api_errors()
    {
        $this->mockHandler->append(new Response(400, [], json_encode([
            'message' => 'Invalid request'
        ])));

        $this->expectException(\Bhekor\LaravelBrevo\Exceptions\BrevoApiException::class);
        $this->expectExceptionMessage('Invalid request');

        $this->client->sendEmail(['invalid' => 'payload']);
    }

    /** @test */
    public function it_gets_email_event_report()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'events' => ['delivered' => 10]
        ])));

        $response = $this->client->getEmailEventReport([
            'limit' => 10
        ]);

        $this->assertEquals(['delivered' => 10], $response['events']);
    }
}
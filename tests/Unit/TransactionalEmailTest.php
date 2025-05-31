<?php

namespace Bhekor\LaravelBrevo\Tests\Unit;

use Bhekor\LaravelBrevo\Services\TransactionalEmail;
use Bhekor\LaravelBrevo\Tests\TestCase;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * @covers \Bhekor\LaravelBrevo\Services\TransactionalEmail
 */
class TransactionalEmailTest extends TestCase
{
    private TransactionalEmail $service;
    private $mockClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockClient = $this->createMock(\Bhekor\LaravelBrevo\Contracts\BrevoClientInterface::class);
        $this->service = new TransactionalEmail($this->mockClient);
    }

    /** @test */
    public function it_builds_correct_payload_for_simple_email()
    {
        $email = (new Email())
            ->from(new Address('from@example.com', 'From Name'))
            ->to(new Address('to@example.com', 'To Name'))
            ->subject('Test Subject')
            ->text('Test Body');

        $this->mockClient->expects($this->once())
            ->method('sendEmail')
            ->with([
                'sender' => [
                    'email' => 'from@example.com',
                    'name' => 'From Name'
                ],
                'to' => [[
                    'email' => 'to@example.com',
                    'name' => 'To Name'
                ]],
                'subject' => 'Test Subject',
                'htmlContent' => null,
                'textContent' => 'Test Body',
            ]);

        $this->service->send($email);
    }

    /** @test */
    public function it_handles_attachments_correctly()
    {
        $email = (new Email())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Test Subject')
            ->text('Test Body')
            ->attach('Test content', 'test.txt');

        $this->mockClient->expects($this->once())
            ->method('sendEmail')
            ->with($this->callback(function ($payload) {
                return isset($payload['attachment']) &&
                       count($payload['attachment']) === 1 &&
                       $payload['attachment'][0]['name'] === 'test.txt';
            }));

        $this->service->send($email);
    }
}
<?php

namespace Bhekor\LaravelBrevo\Tests\Unit;

use Bhekor\LaravelBrevo\Mail\BrevoTransport;
use Bhekor\LaravelBrevo\Tests\TestCase;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * @covers \Bhekor\LaravelBrevo\Mail\BrevoTransport
 */
class MailTransportTest extends TestCase
{
    private BrevoTransport $transport;
    private $mockClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockClient = $this->createMock(\Bhekor\LaravelBrevo\Contracts\BrevoClientInterface::class);
        $this->transport = new BrevoTransport($this->mockClient, []);
    }

    /** @test */
    public function it_sends_email_through_transport()
    {
        $email = (new Email())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Test Subject')
            ->text('Test Body');

        $this->mockClient->expects($this->once())
            ->method('sendEmail')
            ->willReturn(['messageId' => 'test123']);

        $sentMessage = $this->transport->send($email);

        $this->assertInstanceOf(SentMessage::class, $sentMessage);
    }

    /** @test */
    public function it_adds_message_id_header()
    {
        $email = (new Email())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Test Subject')
            ->text('Test Body');

        $this->mockClient->method('sendEmail')
            ->willReturn(['messageId' => 'test123']);

        $sentMessage = $this->transport->send($email);
        $headers = $sentMessage->getOriginalMessage()->getHeaders();

        $this->assertEquals('test123', $headers->get('X-Brevo-Message-ID')->getBody());
    }
}
<?php

namespace Bhekor\LaravelBrevo\Tests\Feature;

use Bhekor\LaravelBrevo\Tests\TestCase;
use Illuminate\Support\Facades\Mail;

/**
 * @group feature
 */
class MailTransportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'mail.default' => 'brevo',
            'brevo.api_key' => 'test-key',
            'brevo.default_from.email' => 'from@example.com',
            'brevo.default_from.name' => 'Test Sender',
        ]);
    }

    /** @test */
    public function it_sends_email_through_brevo_transport()
    {
        Mail::fake();

        Mail::to('test@example.com')->send(new TestMail());

        Mail::assertSent(TestMail::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    }

    /** @test */
    public function it_sends_markdown_emails()
    {
        Mail::fake();

        Mail::to('test@example.com')->send(new TestMarkdownMail());

        Mail::assertSent(TestMarkdownMail::class, function ($mail) {
            return $mail->hasTo('test@example.com') &&
                   $mail->build()->view === 'emails.test-markdown';
        });
    }
}

class TestMail extends \Illuminate\Mail\Mailable
{
    public function build()
    {
        return $this->subject('Test Email')
                    ->text('This is a test email');
    }
}

class TestMarkdownMail extends \Illuminate\Mail\Mailable
{
    public function build()
    {
        return $this->markdown('emails.test-markdown')
                    ->subject('Test Markdown Email');
    }
}
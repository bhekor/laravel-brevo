<?php

namespace Bhekor\LaravelBrevo\Mail;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Bhekor\LaravelBrevo\Contracts\BrevoClientInterface;
use Bhekor\LaravelBrevo\Exceptions\BrevoApiException;

/**
 * Brevo Mail Transport Implementation
 */
class BrevoTransport extends AbstractTransport
{
    private BrevoClientInterface $client;
    private array $config;

    /**
     * Create a new Brevo transport instance
     *
     * @param BrevoClientInterface $client
     * @param array $config
     */
    public function __construct(BrevoClientInterface $client, array $config)
    {
        parent::__construct();
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $payload = $this->buildPayload($email);

        try {
            $response = $this->client->sendEmail($payload);
            $message->getOriginalMessage()->getHeaders()->addTextHeader(
                'X-Brevo-Message-ID',
                $response['messageId']
            );
        } catch (BrevoApiException $e) {
            throw new \Symfony\Component\Mailer\Exception\TransportException(
                sprintf('Brevo API error: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Build API payload from Email object
     *
     * @param \Symfony\Component\Mime\Email $email
     * @return array
     */
    private function buildPayload(\Symfony\Component\Mime\Email $email): array
    {
        $payload = [
            'sender' => $this->getSender($email),
            'to' => $this->mapRecipients($email->getTo()),
            'subject' => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody(),
            'textContent' => $email->getTextBody(),
        ];

        $this->addCcRecipients($email, $payload);
        $this->addBccRecipients($email, $payload);
        $this->addReplyTo($email, $payload);
        $this->addAttachments($email, $payload);

        return $payload;
    }

    /**
     * Get sender information with fallback to config
     *
     * @param \Symfony\Component\Mime\Email $email
     * @return array
     */
    private function getSender(\Symfony\Component\Mime\Email $email): array
    {
        $from = $email->getFrom();
        $defaultFrom = $this->config['default_from'] ?? [
            'email' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
            'name' => env('MAIL_FROM_NAME', 'Example'),
        ];

        return $from ? $this->mapAddress($from[0]) : $defaultFrom;
    }

    /**
     * Map recipients to Brevo format
     *
     * @param array $recipients
     * @return array
     */
    private function mapRecipients(array $recipients): array
    {
        return array_map([$this, 'mapAddress'], $recipients);
    }

    /**
     * Map single address to Brevo format with required name field
     *
     * @param \Symfony\Component\Mime\Address $address
     * @return array
     */
    private function mapAddress(\Symfony\Component\Mime\Address $address): array
    {
        return [
            'email' => $address->getAddress(),
            'name' => $address->getName() ?: $address->getAddress(), // Fallback to email if name not provided
        ];
    }

    /**
     * Add CC recipients to payload if present
     *
     * @param \Symfony\Component\Mime\Email $email
     * @param array &$payload
     */
    private function addCcRecipients(\Symfony\Component\Mime\Email $email, array &$payload): void
    {
        if (!empty($email->getCc())) {
            $payload['cc'] = $this->mapRecipients($email->getCc());
        }
    }

    /**
     * Add BCC recipients to payload if present
     *
     * @param \Symfony\Component\Mime\Email $email
     * @param array &$payload
     */
    private function addBccRecipients(\Symfony\Component\Mime\Email $email, array &$payload): void
    {
        if (!empty($email->getBcc())) {
            $payload['bcc'] = $this->mapRecipients($email->getBcc());
        }
    }

    /**
     * Add reply-to address if present
     *
     * @param \Symfony\Component\Mime\Email $email
     * @param array &$payload
     */
    private function addReplyTo(\Symfony\Component\Mime\Email $email, array &$payload): void
    {
        if (!empty($email->getReplyTo())) {
            $payload['replyTo'] = $this->mapAddress($email->getReplyTo()[0]);
        }
    }

    /**
     * Add attachments if present
     *
     * @param \Symfony\Component\Mime\Email $email
     * @param array &$payload
     */
    private function addAttachments(\Symfony\Component\Mime\Email $email, array &$payload): void
    {
        if (!empty($email->getAttachments())) {
            $payload['attachment'] = array_map(function ($attachment) {
                return [
                    'name' => $attachment->getFilename(),
                    'content' => base64_encode($attachment->getBody()),
                ];
            }, iterator_to_array($email->getAttachments()));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return 'brevo';
    }
}
<?php

namespace Bhekor\LaravelBrevo\Mail;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Bhekor\LaravelBrevo\Contracts\BrevoClientInterface;
use Bhekor\LaravelBrevo\Exceptions\BrevoApiException;

/**
 * Brevo mail transport for Laravel.
 * 
 * @package Bhekor\LaravelBrevo\Mail
 */
class BrevoTransport extends AbstractTransport
{
    private BrevoClientInterface $client;
    private array $config;

    /**
     * Create new Brevo transport instance.
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
     * Build API payload from Email object.
     * 
     * @param \Symfony\Component\Mime\Email $email
     * @return array
     */
    private function buildPayload(\Symfony\Component\Mime\Email $email): array
    {
        $payload = [
            'sender' => $this->mapAddress($email->getFrom()[0]),
            'to' => array_map([$this, 'mapAddress'], $email->getTo()),
            'subject' => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody(),
            'textContent' => $email->getTextBody(),
        ];

        if (!empty($email->getCc())) {
            $payload['cc'] = array_map([$this, 'mapAddress'], $email->getCc());
        }

        if (!empty($email->getBcc())) {
            $payload['bcc'] = array_map([$this, 'mapAddress'], $email->getBcc());
        }

        if (!empty($email->getReplyTo())) {
            $payload['replyTo'] = $this->mapAddress($email->getReplyTo()[0]);
        }

        if (!empty($email->getAttachments())) {
            $payload['attachment'] = $this->processAttachments($email->getAttachments());
        }

        return $payload;
    }

    /**
     * Map Symfony Address to Brevo format.
     * 
     * @param \Symfony\Component\Mime\Address $address
     * @return array
     */
    private function mapAddress(\Symfony\Component\Mime\Address $address): array
    {
        return [
            'email' => $address->getAddress(),
            'name' => $address->getName(),
        ];
    }

    /**
     * Process email attachments.
     * 
     * @param array $attachments
     * @return array
     */
    private function processAttachments(array $attachments): array
    {
        $processed = [];
        
        foreach ($attachments as $attachment) {
            $processed[] = [
                'name' => $attachment->getFilename(),
                'content' => base64_encode($attachment->getBody()),
            ];
        }

        return $processed;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return 'brevo';
    }
}
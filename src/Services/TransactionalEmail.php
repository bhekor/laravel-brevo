<?php

namespace Bhekor\LaravelBrevo\Services;

use Bhekor\LaravelBrevo\Contracts\BrevoClientInterface;
use Bhekor\LaravelBrevo\Exceptions\BrevoApiException;
use Symfony\Component\Mime\Email;

/**
 * Transactional email service for Brevo.
 * 
 * @package Bhekor\LaravelBrevo\Services
 */
class TransactionalEmail
{
    private BrevoClientInterface $client;

    /**
     * Create new transactional email service instance.
     * 
     * @param BrevoClientInterface $client
     */
    public function __construct(BrevoClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Send transactional email.
     * 
     * @param Email $email
     * @return array
     * @throws BrevoApiException
     */
    public function send(Email $email): array
    {
        $payload = $this->buildPayload($email);
        return $this->client->sendEmail($payload);
    }

    /**
     * Build API payload from Email object.
     * 
     * @param Email $email
     * @return array
     */
    private function buildPayload(Email $email): array
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
}
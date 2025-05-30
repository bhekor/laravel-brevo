<?php

namespace Bhekor\LaravelBrevo\Contracts;

/**
 * Interface for mail transport implementations.
 * 
 * @package Bhekor\LaravelBrevo\Contracts
 */
interface MailTransportInterface
{
    /**
     * Send an email through the transport.
     * 
     * @param \Symfony\Component\Mime\Email $email
     * @return array API response
     * @throws \Bhekor\LaravelBrevo\Exceptions\BrevoApiException
     */
    public function send(\Symfony\Component\Mime\Email $email): array;
}
<?php

namespace Bhekor\LaravelBrevo\Contracts;

use Symfony\Component\Mime\Email;

/**
 * Mail Transport Interface
 * 
 * Defines the contract for mail transport implementations.
 */
interface MailTransportInterface
{
    /**
     * Send email
     *
     * @param Email $email
     * @return array
     * @throws \Bhekor\LaravelBrevo\Exceptions\BrevoApiException
     */
    public function send(Email $email): array;
}
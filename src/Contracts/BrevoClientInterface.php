<?php

namespace Bhekor\LaravelBrevo\Contracts;

/**
 * Brevo Client Interface
 * 
 * Defines the contract for the Brevo API client.
 */
interface BrevoClientInterface
{
    /**
     * Send transactional email
     *
     * @param array $payload
     * @return array
     * @throws \Bhekor\LaravelBrevo\Exceptions\BrevoApiException
     */
    public function sendEmail(array $payload): array;

    /**
     * Get email event reports
     *
     * @param array $query
     * @return array
     * @throws \Bhekor\LaravelBrevo\Exceptions\BrevoApiException
     */
    public function getEmailEventReport(array $query = []): array;

    /**
     * Make API request
     *
     * @param string $method
     * @param string $uri
     * @param array $data
     * @return array
     * @throws \Bhekor\LaravelBrevo\Exceptions\BrevoApiException
     */
    public function request(string $method, string $uri, array $data = []): array;
}
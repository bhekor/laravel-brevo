<?php

namespace Bhekor\LaravelBrevo\Contracts;

/**
 * Interface for Brevo API client.
 * 
 * @package Bhekor\LaravelBrevo\Contracts
 */
interface BrevoClientInterface
{
    /**
     * Send transactional email through Brevo API.
     * 
     * @param array $payload Email data
     * @return array API response
     * @throws \Bhekor\LaravelBrevo\Exceptions\BrevoApiException
     */
    public function sendEmail(array $payload): array;

    /**
     * Get email event reports.
     * 
     * @param array $query Query parameters
     * @return array API response
     * @throws \Bhekor\LaravelBrevo\Exceptions\BrevoApiException
     */
    public function getEmailEventReport(array $query = []): array;

    /**
     * Make API request to Brevo.
     * 
     * @param string $method HTTP method
     * @param string $uri API endpoint
     * @param array $data Request data
     * @return array API response
     * @throws \Bhekor\LaravelBrevo\Exceptions\BrevoApiException
     */
    public function request(string $method, string $uri, array $data = []): array;
}
<?php

namespace Bhekor\LaravelBrevo\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Bhekor\LaravelBrevo\Contracts\BrevoClientInterface;
use Bhekor\LaravelBrevo\Exceptions\BrevoApiException;

/**
 * Brevo API Client Implementation
 * 
 * Handles all communication with the Brevo API including:
 * - Sending transactional emails
 * - Retrieving email event reports
 * - Managing API requests and responses
 */
class BrevoClient implements BrevoClientInterface
{
    /**
     * Guzzle HTTP client instance
     * @var Client
     */
    private Client $client;

    /**
     * Brevo API key
     * @var string
     */
    private string $apiKey;

    /**
     * Base URI for API requests
     * @var string
     */
    private string $baseUri;

    /**
     * Request timeout in seconds
     * @var int
     */
    private int $timeout;

    /**
     * Create a new Brevo API client instance
     *
     * @param string $apiKey Brevo API key
     * @param string $host Base API host (must include /v3)
     * @param int $timeout Request timeout in seconds
     * @throws \InvalidArgumentException If invalid host is provided
     */
    public function __construct(
        string $apiKey, 
        string $host = 'https://api.brevo.com/v3',
        int $timeout = 15
    ) {
        if (!str_ends_with($host, '/v3')) {
            throw new \InvalidArgumentException(
                'Brevo API host must end with /v3. Provided: ' . $host
            );
        }

        $this->apiKey = $apiKey;
        $this->baseUri = rtrim($host, '/') . '/';
        $this->timeout = $timeout;

        $this->initializeClient();
    }

    /**
     * Initialize the HTTP client with proper configuration
     */
    private function initializeClient(): void
    {
        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $this->apiKey,
                'content-type' => 'application/json',
            ],
            'timeout' => $this->timeout,
            'http_errors' => false, // We handle errors manually
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function sendEmail(array $payload): array
    {
        return $this->request('POST', 'smtp/email', $payload);
    }

    /**
     * {@inheritDoc}
     */
    public function getEmailEventReport(array $query = []): array
    {
        return $this->request('GET', 'smtp/statistics/events', $query);
    }

    /**
     * {@inheritDoc}
     */
    public function request(string $method, string $uri, array $data = []): array
    {
        try {
            $options = $this->prepareRequestOptions($method, $data);
            $response = $this->client->request($method, ltrim($uri, '/'), $options);
            
            return $this->processResponse($response);
        } catch (GuzzleException $e) {
            throw new BrevoApiException(
                'HTTP Request Failed: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Prepare request options based on HTTP method
     *
     * @param string $method HTTP method
     * @param array $data Request data
     * @return array Prepared options
     */
    private function prepareRequestOptions(string $method, array $data): array
    {
        return $method === 'GET' 
            ? ['query' => $data] 
            : ['json' => $data];
    }

    /**
     * Process API response
     *
     * @param ResponseInterface $response
     * @return array Decoded response data
     * @throws BrevoApiException
     */
    private function processResponse(ResponseInterface $response): array
    {
        $contents = $response->getBody()->getContents();
        $data = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BrevoApiException(
                'Invalid JSON response: ' . $contents,
                $response->getStatusCode()
            );
        }

        if ($response->getStatusCode() >= 400) {
            $this->handleErrorResponse($response, $data);
        }

        return $data;
    }

    /**
     * Handle error responses from API
     *
     * @param ResponseInterface $response
     * @param array $data
     * @throws BrevoApiException
     */
    private function handleErrorResponse(ResponseInterface $response, array $data): void
    {
        $message = $data['message'] ?? $data['error'] ?? 'Brevo API error';
        $code = $response->getStatusCode();
        $errorCode = $data['code'] ?? null;

        throw new BrevoApiException($message, $code, null, $errorCode);
    }
}
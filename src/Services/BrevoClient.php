<?php

namespace Bhekor\LaravelBrevo\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Bhekor\LaravelBrevo\Contracts\BrevoClientInterface;
use Bhekor\LaravelBrevo\Exceptions\BrevoApiException;

/**
 * Brevo API client implementation.
 * 
 * @package Bhekor\LaravelBrevo\Services
 */
class BrevoClient implements BrevoClientInterface
{
    private Client $client;
    private string $apiKey;

    /**
     * Create a new Brevo client instance.
     * 
     * @param string $apiKey Brevo API key
     * @param array $options Guzzle client options
     */
    public function __construct(string $apiKey, array $options = [])
    {
        $this->apiKey = $apiKey;

        $this->client = new Client(array_merge([
            'base_uri' => 'https://api.brevo.com/v3/',
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $this->apiKey,
                'content-type' => 'application/json',
            ],
            'timeout' => $options['timeout'] ?? 15,
        ], $options));
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
            $options = [];

            if ($method === 'GET') {
                $options['query'] = $data;
            } else {
                $options['json'] = $data;
            }

            $response = $this->client->request($method, $uri, $options);

            return $this->handleResponse($response);
        } catch (GuzzleException $e) {
            throw new BrevoApiException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Handle API response.
     * 
     * @param ResponseInterface $response
     * @return array
     * @throws BrevoApiException
     */
    private function handleResponse(ResponseInterface $response): array
    {
        $contents = $response->getBody()->getContents();
        $data = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BrevoApiException('Invalid JSON response: '.$contents);
        }

        if ($response->getStatusCode() >= 400) {
            throw new BrevoApiException(
                $data['message'] ?? 'Brevo API error',
                $response->getStatusCode()
            );
        }

        return $data;
    }
}
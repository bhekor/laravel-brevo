<?php

namespace Bhekor\LaravelBrevo\Exceptions;

/**
 * Custom exception for Brevo API errors
 * 
 * Contains additional API-specific error information
 */
class BrevoApiException extends \RuntimeException
{
    /**
     * Brevo API error code
     * @var string|null
     */
    private ?string $apiCode;

    /**
     * Original response data
     * @var array|null
     */
    private ?array $responseData;

    /**
     * Create new exception instance
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param \Throwable|null $previous Previous exception
     * @param string|null $apiCode Brevo API error code
     * @param array|null $responseData Full response data
     */
    public function __construct(
        string $message = "Brevo API error occurred",
        int $code = 0,
        ?\Throwable $previous = null,
        ?string $apiCode = null,
        ?array $responseData = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->apiCode = $apiCode;
        $this->responseData = $responseData;
    }

    /**
     * Get API error code
     *
     * @return string|null
     */
    public function getApiCode(): ?string
    {
        return $this->apiCode;
    }

    /**
     * Get full response data
     *
     * @return array|null
     */
    public function getResponseData(): ?array
    {
        return $this->responseData;
    }

    /**
     * Create from Guzzle exception
     *
     * @param \GuzzleHttp\Exception\GuzzleException $e
     * @return static
     */
    public static function fromGuzzleException(GuzzleException $e): self
    {
        return new self(
            'HTTP Request Failed: ' . $e->getMessage(),
            $e->getCode(),
            $e
        );
    }
}
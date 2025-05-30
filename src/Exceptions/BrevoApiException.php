<?php

namespace Bhekor\LaravelBrevo\Exceptions;

/**
 * Exception for Brevo API errors.
 * 
 * @package Bhekor\LaravelBrevo\Exceptions
 */
class BrevoApiException extends \RuntimeException
{
    /**
     * Create new exception instance.
     * 
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = "Brevo API error occurred",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
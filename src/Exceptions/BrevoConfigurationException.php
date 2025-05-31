<?php

namespace Bhekor\LaravelBrevo\Exceptions;

/**
 * Exception for configuration errors.
 * 
 * @package Bhekor\LaravelBrevo\Exceptions
 */
class BrevoConfigurationException extends \RuntimeException
{
    /**
     * Create new exception instance.
     * 
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = "Brevo configuration error",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
<?php

namespace Bhekor\LaravelBrevo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Bhekor\LaravelBrevo\Services\TransactionalEmail email()
 * @method static array sendEmail(array $payload)
 * @method static array getEmailEventReport(array $query = [])
 * 
 * @see \Bhekor\LaravelBrevo\Services\BrevoClient
 */
class Brevo extends Facade
{
    /**
     * Get the registered name of the component.
     * 
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Bhekor\LaravelBrevo\Contracts\BrevoClientInterface::class;
    }
}
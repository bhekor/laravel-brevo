<?php

/**
 * Brevo Package Configuration
 * 
 * This file contains the default configuration for the Laravel-Brevo package.
 * Values can be overridden by publishing the config file or via environment variables.
 */

return [
    /**
     * Brevo API Key
     * 
     * @var string
     */
    'api_key' => env('BREVO_API_KEY'),

    /**
     * Brevo API Host
     * Must include /v3 suffix (e.g., https://api.brevo.com/v3)
     * 
     * @var string
     */
    'host' => env('BREVO_API_HOST', 'https://api.brevo.com/v3'),

    /**
     * Default From Address
     * 
     * @var array
     */
    'default_from' => [
        'email' => env('BREVO_FROM_EMAIL', 'hello@example.com'),
        'name' => env('BREVO_FROM_NAME', 'Example'),
    ],

    /**
     * Mail Transport Configuration
     * 
     * @var array
     */
    'mail' => [
        'transport' => 'brevo',
        'api_key' => env('BREVO_API_KEY'),
        'host' => env('BREVO_API_HOST', 'https://api.brevo.com/v3'),
        'timeout' => env('BREVO_TIMEOUT', 15),
    ],

    /**
     * Webhook Configuration
     * 
     * @var array
     */
    'webhook' => [
        'secret' => env('BREVO_WEBHOOK_SECRET'),
        'route' => '/brevo/webhook',
        'middleware' => ['api'],
    ],
];
<?php

namespace Bhekor\LaravelBrevo\Webhooks;

/**
 * Maps Brevo webhook events to Laravel events.
 * 
 * @package Bhekor\LaravelBrevo\Webhooks
 */
class EventMapper
{
    private const EVENT_MAP = [
        'delivered' => 'Bhekor\LaravelBrevo\Events\EmailDelivered',
        'opened' => 'Bhekor\LaravelBrevo\Events\EmailOpened',
        'click' => 'Bhekor\LaravelBrevo\Events\EmailClicked',
        'bounce' => 'Bhekor\LaravelBrevo\Events\EmailBounced',
        'spam' => 'Bhekor\LaravelBrevo\Events\EmailMarkedAsSpam',
    ];

    /**
     * Map Brevo event to Laravel event class.
     * 
     * @param string $brevoEvent
     * @return string|null
     */
    public function map(string $brevoEvent): ?string
    {
        return self::EVENT_MAP[$brevoEvent] ?? null;
    }
}
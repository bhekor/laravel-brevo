<?php

namespace Bhekor\LaravelBrevo\Webhooks;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Bhekor\LaravelBrevo\Webhooks\EventMapper;
use Bhekor\LaravelBrevo\Exceptions\BrevoValidationException;

/**
 * Handles incoming Brevo webhooks.
 * 
 * @package Bhekor\LaravelBrevo\Webhooks
 */
class WebhookController
{
    private EventMapper $eventMapper;

    /**
     * Create new webhook controller instance.
     * 
     * @param EventMapper $eventMapper
     */
    public function __construct(EventMapper $eventMapper)
    {
        $this->eventMapper = $eventMapper;
    }

    /**
     * Handle incoming webhook.
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws BrevoValidationException
     */
    public function __invoke(Request $request)
    {
        $this->verifySignature($request);

        $eventData = $request->json()->all();
        $eventClass = $this->eventMapper->map($eventData['event']);

        if ($eventClass) {
            Event::dispatch(new $eventClass($eventData));
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Verify webhook signature.
     * 
     * @param Request $request
     * @throws BrevoValidationException
     */
    private function verifySignature(Request $request): void
    {
        $signature = $request->header('X-Brevo-Signature');
        $secret = config('brevo.webhook.secret');

        if (empty($secret)) {
            throw new BrevoValidationException('Webhook secret not configured');
        }

        $computedSignature = hash_hmac('sha256', $request->getContent(), $secret);

        if (!hash_equals($computedSignature, $signature)) {
            throw new BrevoValidationException('Invalid webhook signature');
        }
    }
}
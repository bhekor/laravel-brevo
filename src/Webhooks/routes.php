<?php

use Bhekor\LaravelBrevo\Webhooks\WebhookController;
use Illuminate\Support\Facades\Route;

/**
 * Brevo Webhook Routes
 * 
 * Defines the routes for handling Brevo webhooks.
 */
Route::post(config('brevo.webhook.route'), WebhookController::class)
    ->middleware(config('brevo.webhook.middleware', ['api']));
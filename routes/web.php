<?php

use Bhekor\LaravelBrevo\Webhooks\WebhookController;
use Illuminate\Support\Facades\Route;

/**
 * Register Brevo webhook routes
 */
Route::post(config('brevo.webhook.route'), WebhookController::class)
    ->middleware(config('brevo.webhook.middleware', ['api']));
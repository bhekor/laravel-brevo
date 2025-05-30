<?php

namespace Bhekor\LaravelBrevo;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Bhekor\LaravelBrevo\Contracts\BrevoClientInterface;
use Bhekor\LaravelBrevo\Mail\BrevoTransport;
use Bhekor\LaravelBrevo\Services\BrevoClient;

/**
 * Service provider for Laravel-Brevo package integration.
 * 
 * @package Bhekor\LaravelBrevo
 */
class BrevoServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/brevo.php', 'brevo');

        $this->app->singleton(BrevoClientInterface::class, function ($app) {
            $config = $app['config']->get('brevo');

            if (empty($config['api_key'])) {
                throw new \RuntimeException('Brevo API key not configured');
            }

            return new BrevoClient(
                $config['api_key'],
                $config['options'] ?? []
            );
        });

        $this->app->afterResolving(MailManager::class, function (MailManager $manager) {
            $manager->extend('brevo', function () {
                $config = $this->app['config']->get('brevo');
                return new BrevoTransport(
                    $this->app->make(BrevoClientInterface::class),
                    $config
                );
            });
        });

        if ($this->app['config']->get('brevo.default_mailer')) {
            $this->app['config']->set('mail.default', 'brevo');
        }
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/brevo.php' => config_path('brevo.php'),
            ], 'brevo-config');

            $this->publishes([
                __DIR__.'/../resources/js' => public_path('vendor/laravel-brevo'),
            ], 'brevo-assets');
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}
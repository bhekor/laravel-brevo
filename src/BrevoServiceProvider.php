<?php

namespace Bhekor\LaravelBrevo;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Bhekor\LaravelBrevo\Contracts\BrevoClientInterface;
use Bhekor\LaravelBrevo\Mail\BrevoTransport;
use Bhekor\LaravelBrevo\Services\BrevoClient;

/**
 * Laravel Service Provider for Brevo Package
 * 
 * Handles package registration and bootstrapping
 */
class BrevoServiceProvider extends ServiceProvider
{
    /**
     * Register package services
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/brevo.php', 'brevo');
        $this->registerClient();
        $this->registerMailTransport();
    }

    /**
     * Bootstrap package services
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configurePublishing();
        $this->configureRoutes();
        $this->configureMail();
    }

    /**
     * Register Brevo API client
     */
    protected function registerClient(): void
    {
        $this->app->singleton(BrevoClientInterface::class, function ($app) {
            $config = $app['config']->get('brevo');

            $this->validateConfig($config);

            return new BrevoClient(
                $config['api_key'],
                $this->normalizeHost($config['host'] ?? 'https://api.brevo.com/v3'),
                $config['mail']['timeout'] ?? 15
            );
        });
    }

    /**
     * Register mail transport
     */
    protected function registerMailTransport(): void
    {
        $this->app->afterResolving(MailManager::class, function (MailManager $manager) {
            $manager->extend('brevo', function () {
                return new BrevoTransport(
                    $this->app->make(BrevoClientInterface::class),
                    $this->app['config']->get('brevo.mail', [])
                );
            });
        });
    }

    /**
     * Configure package resources to publish
     */
    protected function configurePublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/brevo.php' => config_path('brevo.php'),
            ], 'brevo-config');

            $this->publishes([
                __DIR__.'/../resources/js' => public_path('vendor/laravel-brevo'),
            ], 'brevo-assets');
        }
    }

    /**
     * Configure package routes
     */
    protected function configureRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    /**
     * Configure mail settings
     */
    protected function configureMail(): void
    {
        $this->app['config']->set('mail.mailers.brevo', array_merge(
            ['transport' => 'brevo'],
            $this->app['config']->get('brevo.mail', [])
        ));
    }

    /**
     * Validate required configuration
     *
     * @param array $config
     * @throws \RuntimeException
     */
    private function validateConfig(array $config): void
    {
        if (empty($config['api_key'])) {
            throw new \RuntimeException(
                'Brevo API key not configured. Please set BREVO_API_KEY in your .env file.'
            );
        }
    }

    /**
     * Normalize API host URL
     *
     * @param string $host
     * @return string
     */
    private function normalizeHost(string $host): string
    {
        $host = rtrim($host, '/');
        return str_ends_with($host, '/v3') ? $host : $host . '/v3';
    }
}
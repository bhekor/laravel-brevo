<?php

namespace Bhekor\LaravelBrevo\Tests;

use Bhekor\LaravelBrevo\BrevoServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            BrevoServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('mail.default', 'brevo');
        $app['config']->set('brevo.api_key', 'test-key');
    }
}
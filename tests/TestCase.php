<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Tests;

use Gollumeo\Aegis\AegisServiceProvider;
use Gollumeo\Aegis\Support\ConfigKeys;
use Gollumeo\Aegis\Tests\Fakes\Controllers\DummyController;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public const string VALID_PREFIX = 'Prefix-test-key-1234';

    /**
     * @return class-string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            AegisServiceProvider::class,
        ];
    }

    /**
     * Configure package-specific environment settings for tests.
     *
     * Sets Aegis configuration values (using ConfigKeys enum values) required by tests:
     * - require header enforcement
     * - header name
     * - HTTP methods to protect
     * - idempotency key length range, charset and prefix
     *
     * The configuration is applied to the test application container via the global config helper.
     */
    protected function defineEnvironment($app): void
    {
        config([ConfigKeys::RequireHeader->value => true]);
        config([ConfigKeys::HeaderName->value => 'Idempotency-Key']);
        config([ConfigKeys::Methods->value => ['POST', 'PUT', 'PATCH', 'DELETE']]);
        config([ConfigKeys::KeyMin->value => 16]);
        config([ConfigKeys::KeyMax->value => 120]);
        config([ConfigKeys::KeyCharset->value => 'A-Za-z0-9_-']);
        config([ConfigKeys::KeyPrefix->value => 'Prefix']);
    }

    /**
     * Register test routes for the application.
     *
     * Defines a POST /payments endpoint handled by DummyController::pay and protected by the 'aegis' middleware.
     */
    protected function defineRoutes(mixed $router): void
    {
        $router->post('/payments', [DummyController::class, 'pay'])->middleware('aegis');
    }
}

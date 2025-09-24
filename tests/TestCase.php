<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Tests;

use Gollumeo\Aegis\AegisServiceProvider;
use Gollumeo\Aegis\Support\ConfigKeys;
use Gollumeo\Aegis\Tests\Fakes\Controllers\DummyController;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @return class-string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            AegisServiceProvider::class,
        ];
    }

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

    protected function defineRoutes(mixed $router): void
    {
        $router->post('/payments', [DummyController::class, 'pay'])->middleware('aegis');
    }
}

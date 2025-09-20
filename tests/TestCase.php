<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Tests;

use Gollumeo\Aegis\AegisServiceProvider;
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
        config(['aegis.require_header' => true]);
        config(['aegis.header_name' => 'Idempotency-Key']);
        config(['aegis.methods' => ['POST', 'PUT', 'PATCH', 'DELETE']]);
        config(['aegis.key.min' => 16]);
        config(['aegis.key.max' => 120]);
        config(['aegis.key.charset' => 'A-Za-z0-9_-']);
    }

    protected function defineRoutes(mixed $router): void
    {
        $router->post('/payments', [DummyController::class, 'pay'])->middleware('aegis');
    }
}

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
    }

    protected function defineRoutes(mixed $router): void
    {
        $router->post('/payments', [DummyController::class, 'pay'])->middleware('aegis');
    }
}

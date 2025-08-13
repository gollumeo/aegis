<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Tests;

use Gollumeo\Aegis\AegisServiceProvider;
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
        //
    }
}

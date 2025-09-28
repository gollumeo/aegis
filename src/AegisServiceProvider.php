<?php

declare(strict_types=1);

namespace Gollumeo\Aegis;

use Gollumeo\Aegis\Application\Contracts\Insurance;
use Gollumeo\Aegis\Domain\Policies\Composites\ComposeInsurances;
use Gollumeo\Aegis\Infrastructure\Http\Middleware\Idempotency;
use Gollumeo\Aegis\Support\AegisConfig;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

final class AegisServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        app('router')->aliasMiddleware('aegis', Idempotency::class);
        $this->publishes(
            paths: [__DIR__.'/../config/aegis.php' => config_path('aegis.php')],
            groups: 'aegis-config'
        );
    }

    public function register(): void
    {
        $this->app->bind(Insurance::class, function (Application $app): Insurance {
            $policies = [];
            foreach (AegisConfig::policies() as $class) {
                /** @var Insurance $policy */
                $policy = $app->make($class);
                $policies[] = $policy;
            }

            return new ComposeInsurances($policies);
        });
    }
}

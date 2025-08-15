<?php

declare(strict_types=1);

namespace Gollumeo\Aegis;

use Gollumeo\Aegis\Infrastructure\Http\Middleware\Idempotency;
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
}

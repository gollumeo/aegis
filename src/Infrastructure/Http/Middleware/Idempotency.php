<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Infrastructure\Http\Middleware;

use Closure;
use Gollumeo\Aegis\Application\Contracts\Insurance;
use Gollumeo\Aegis\Support\AegisConfig;
use Illuminate\Http\Request;
use Response;
use Throwable;

final readonly class Idempotency
{
    // TODO
    public function __construct(
        private Insurance $composedInsurance,
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        try {
            $this->composedInsurance->assert($request);
        } catch (Throwable $exception) {
            return Response::json([
                'error' => $exception->getMessage(),
                'message' => 'This endpoint requires'.AegisConfig::headerName().'.',
                'how_to_fix' => 'Generate a stable key and resend the same request with it.',
            ], 428, [
                'X-Idempotency-Required' => true,
                'X-Idempotency-Header' => AegisConfig::headerName(),
                'X-Idempotency-Methods' => implode(', ', AegisConfig::methods()),
            ]);
        }

        return $next($request);
    }
}

<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class Idempotency
{
    public function handle(Request $request, Closure $next): mixed
    {
        // TODO: refacto

        /** @var array<string> $methods */
        $methods = config('aegis.methods');
        /** @var bool $require */
        $require = config('aegis.require_header');
        /** @var string $header */
        $header = config('aegis.header_name');

        if ($require && in_array($request->method(), $methods, true)
            && ! $request->hasHeader($header)) {
            return response()->json([
                'error' => 'idempotency_header_required',
                'message' => "This endpoint requires '{$header}'.",
                'how_to_fix' => 'Generate a stable key and resend the same request with it.',
            ], 428, [
                'X-Idempotency-Required' => true,
                'X-Idempotency-Header' => $header,
                'X-Idempotency-Methods' => implode(', ', $methods),
            ]);
        }

        return $next($request);
    }
}

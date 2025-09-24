<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Domain\Policies;

use Gollumeo\Aegis\Application\Contracts\Insurance;
use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyCharset;
use Gollumeo\Aegis\Support\AegisConfig;
use Illuminate\Http\Request;

final class EnsureIdempotencyCharset implements Insurance
{
    /**
     * Validates the configured idempotency header value contains only allowed characters.
     *
     * Retrieves the allowed character set and header name from AegisConfig, reads that header
     * from the given request, and throws InvalidIdempotencyCharset if the value contains any
     * characters outside the configured charset.
     *
     * @throws InvalidIdempotencyCharset If the header value contains disallowed characters.
     */
    public function assert(Request $request): void
    {
        $charset = AegisConfig::keyCharset();
        $idempotencyHeaderName = AegisConfig::headerName();
        $key = $request->header($idempotencyHeaderName);

        if (! preg_match('/^['.$charset.']+$/', $key)) {
            throw new InvalidIdempotencyCharset();
        }
    }
}

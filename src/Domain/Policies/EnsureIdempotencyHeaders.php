<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Domain\Policies;

use Gollumeo\Aegis\Application\Contracts\Insurance;
use Gollumeo\Aegis\Domain\Exceptions\MissingIdempotencyHeader;
use Illuminate\Http\Request;

final class EnsureIdempotencyHeaders implements Insurance
{
    /**
     * Ensures that the incoming request contains the required idempotency header
     * and that the header value is not empty. Throws an exception if these conditions
     * are not met.
     *
     * @param  Request  $request  The HTTP request object to validate.
     *
     * @throws MissingIdempotencyHeader Thrown if the idempotency header is missing or its value is empty.
     */
    public function assert(Request $request): void
    {
        /** @var string $idempotencyHeaderName */
        $idempotencyHeaderName = config('aegis.header_name');

        if (! $request->headers->has($idempotencyHeaderName)) {
            throw new MissingIdempotencyHeader();
        }

        /** @var string $headers */
        $headers = $request->headers->get($idempotencyHeaderName);

        if (mb_trim($headers) === '') {
            throw new MissingIdempotencyHeader();
        }
    }
}

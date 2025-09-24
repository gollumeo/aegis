<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Domain\Policies;

use Gollumeo\Aegis\Application\Contracts\Insurance;
use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyKeyLength;
use Gollumeo\Aegis\Support\AegisConfig;
use Illuminate\Http\Request;

final class EnsureIdempotencyKeyLength implements Insurance
{
    /**
     * Ensure the request contains an idempotency key whose length falls within configured bounds.
     *
     * Retrieves the header name and allowed minimum/maximum lengths from AegisConfig, reads the
     * header value (defaults to an empty string if missing), and validates its length using
     * multibyte-safe string length. If the length is less than the configured minimum or greater
     * than the configured maximum, an InvalidIdempotencyKeyLength exception is thrown.
     *
     * @param Request $request The incoming HTTP request to validate.
     *
     * @throws InvalidIdempotencyKeyLength When the idempotency key length is outside the allowed range.
     */
    public function assert(Request $request): void
    {
        $minKeyLength = AegisConfig::keyMin();
        $maxKeyLength = AegisConfig::keyMax();
        $headerName = AegisConfig::headerName();
        $headers = $request->headers->get($headerName) ?? '';

        if (mb_strlen($headers) < $minKeyLength || mb_strlen($headers) > $maxKeyLength) {
            throw new InvalidIdempotencyKeyLength();
        }
    }
}

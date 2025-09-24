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
     * Validates the idempotency key length in the request headers based on the predefined minimum and maximum charset configurations.
     *
     * @param  Request  $request  The incoming HTTP request containing the headers to be validated.
     *
     * @throws InvalidIdempotencyKeyLength If the idempotency key length is outside the allowed range.
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

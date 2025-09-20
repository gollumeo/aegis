<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Domain\Policies;

use Gollumeo\Aegis\Application\Contracts\Insurance;
use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyKeyLength;
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
        /** @var int $minCharset */
        $minCharset = config('aegis.key.min');
        /** @var int $maxCharset */
        $maxCharset = config('aegis.key.max');
        /** @var string $idempotencyHeaderName */
        $idempotencyHeaderName = config('aegis.header_name');
        /** @var string $headers */
        $headers = $request->headers->get($idempotencyHeaderName);

        if (mb_strlen($headers) < $minCharset || mb_strlen($headers) > $maxCharset) {
            throw new InvalidIdempotencyKeyLength();
        }
    }
}

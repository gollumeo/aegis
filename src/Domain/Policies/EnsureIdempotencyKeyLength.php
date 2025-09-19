<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Domain\Policies;

use Gollumeo\Aegis\Application\Contracts\Insurance;
use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyKeyLength;
use Illuminate\Http\Request;

final class EnsureIdempotencyKeyLength implements Insurance
{
    /**
     * @throws InvalidIdempotencyKeyLength
     */
    public function assert(Request $request): void
    {
        /** @var int $minCharset */
        $minCharset = config('aegis.key.min');
        /** @var int $maxCharset */
        $maxCharset = config('aegis.key.max');

        /** @var string $idempotencyHeaderName */
        $idempotencyHeaderName = config('aegis.header_name');

        $headers = $request->headers->get($idempotencyHeaderName);

        if (mb_strlen($headers) < $minCharset || mb_strlen($headers) > $maxCharset) {
            throw new InvalidIdempotencyKeyLength();
        }
    }
}

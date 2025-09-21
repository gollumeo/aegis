<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Domain\Policies;

use Gollumeo\Aegis\Application\Contracts\Insurance;
use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyCharset;
use Illuminate\Http\Request;

final class EnsureIdempotencyCharset implements Insurance
{
    /**
     * {@inheritDoc}
     *
     * @throws InvalidIdempotencyCharset
     */
    public function assert(Request $request): void
    {
        /** @var string $charset */
        $charset = config('aegis.key.charset');
        /** @var string $idempotencyHeaderName */
        $idempotencyHeaderName = config('aegis.header_name');
        /** @var string $key */
        $key = $request->header($idempotencyHeaderName);

        if (! preg_match('/^['.$charset.']+$/', $key)) {
            throw new InvalidIdempotencyCharset();
        }

    }
}

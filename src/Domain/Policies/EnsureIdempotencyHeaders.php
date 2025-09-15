<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Domain\Policies;

use Gollumeo\Aegis\Application\Contracts\InsuranceContract;
use Gollumeo\Aegis\Domain\Exceptions\MissingIdempotencyHeader;
use Illuminate\Http\Request;

final class EnsureIdempotencyHeaders implements InsuranceContract
{
    /**
     * @throws MissingIdempotencyHeader
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

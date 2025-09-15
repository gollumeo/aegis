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
        /** @var string $idempotencyHeader */
        $idempotencyHeader = config('aegis.header_name');

        if (! $request->headers->has($idempotencyHeader)) {
            throw new MissingIdempotencyHeader();
        }
    }
}

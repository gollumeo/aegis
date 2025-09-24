<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Domain\Policies;

use Gollumeo\Aegis\Application\Contracts\Insurance;
use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyKeyPrefix;
use Gollumeo\Aegis\Support\AegisConfig;
use Illuminate\Http\Request;

use function mb_strlen;

final class EnsureIdempotencyKeyPrefix implements Insurance
{
    /**
     * Ensures the request's Idempotency-Key header starts with the configured prefix when enforcement is enabled.
     *
     * If the configured prefix is empty or null, no validation is performed. If the header is missing or does not start
     * with the required prefix, throws InvalidIdempotencyKeyPrefix.
     *
     * @throws InvalidIdempotencyKeyPrefix
     */
    public function assert(Request $request): void
    {
        $keyPrefix = AegisConfig::keyPrefix();

        if (! $keyPrefix) {
            return; // feature disabled
        }

        $headerName = AegisConfig::headerName();
        $key = $request->header($headerName) ?? '';

        if ($key === '') {
            throw new InvalidIdempotencyKeyPrefix($keyPrefix);
        }

        $prefixLength = mb_strlen($keyPrefix);
        if (strncmp($key, $keyPrefix, $prefixLength) !== 0) {
            throw new InvalidIdempotencyKeyPrefix($keyPrefix);
        }
    }
}

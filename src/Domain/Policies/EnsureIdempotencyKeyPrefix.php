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
     * Ensures that the Idempotency-Key header starts with the configured prefix when required.
     * Single-key config: aegis.key.required_prefix holds the prefix string. When null or empty, enforcement is disabled.
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

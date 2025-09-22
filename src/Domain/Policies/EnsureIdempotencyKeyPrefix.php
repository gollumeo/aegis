<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Domain\Policies;

use Gollumeo\Aegis\Application\Contracts\Insurance;
use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyKeyPrefix;
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
        /** @var null|string $requiredPrefix */
        $requiredPrefix = config('aegis.key.required_prefix');

        if ($requiredPrefix === null || $requiredPrefix === '') {
            return; // feature disabled
        }

        /** @var string $idempotencyHeaderName */
        $idempotencyHeaderName = config('aegis.header_name');
        /** @var string $key */
        $key = $request->header($idempotencyHeaderName);

        if ($key === '') {
            throw new InvalidIdempotencyKeyPrefix($requiredPrefix);
        }

        $prefixLength = mb_strlen($requiredPrefix);
        if (strncmp($key, $requiredPrefix, $prefixLength) !== 0) {
            throw new InvalidIdempotencyKeyPrefix($requiredPrefix);
        }
    }
}

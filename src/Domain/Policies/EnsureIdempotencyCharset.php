<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Domain\Policies;

use Gollumeo\Aegis\Application\Contracts\Insurance;
use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyCharset;
use Gollumeo\Aegis\Support\AegisConfig;
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
        $charset = AegisConfig::keyCharset();
        $idempotencyHeaderName = AegisConfig::headerName();
        $key = $request->header($idempotencyHeaderName);

        if (! preg_match('/^['.$charset.']+$/', $key)) {
            throw new InvalidIdempotencyCharset();
        }
    }
}

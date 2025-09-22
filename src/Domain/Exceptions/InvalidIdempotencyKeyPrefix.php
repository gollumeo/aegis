<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Domain\Exceptions;

use Exception;

final class InvalidIdempotencyKeyPrefix extends Exception
{
    public function __construct(string $prefix)
    {
        parent::__construct("Invalid key prefix '{$prefix}'.");
    }
}

<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Domain\Exceptions;

use Exception;

final class InvalidIdempotencyKeyLength extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid Idempotency Key length.');
    }
}

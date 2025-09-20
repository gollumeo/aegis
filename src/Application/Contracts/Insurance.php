<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Application\Contracts;

use Illuminate\Http\Request;

interface Insurance
{
    /**
     * Asserts a condition based on the provided request.
     *
     * @param  Request  $request  The request object containing data to evaluate.
     */
    public function assert(Request $request): void;
}

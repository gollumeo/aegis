<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Application\Contracts;

use Illuminate\Http\Request;

interface Insurance
{
    public function assert(Request $request): void;
}

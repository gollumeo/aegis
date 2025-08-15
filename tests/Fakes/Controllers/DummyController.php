<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Tests\Fakes\Controllers;

use Illuminate\Http\JsonResponse;

final class DummyController
{
    private int $counter;

    public function pay(): JsonResponse
    {
        $this->counter++;

        return response()->json(['ok' => true], 201, ['Content-Type', 'application/json'], JSON_PRETTY_PRINT);
    }
}

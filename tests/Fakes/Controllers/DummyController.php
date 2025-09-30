<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Tests\Fakes\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

final class DummyController
{
    private static int $hits = 0;

    public static function totalHits(): int
    {
        return self::$hits;
    }

    public static function reset(): void
    {
        self::$hits = 0;
    }

    public function pay(): JsonResponse
    {
        self::$hits++;

        return Response::json(['ok' => true], 201, ['Content-Type' => 'application/json'], JSON_PRETTY_PRINT);
    }
}

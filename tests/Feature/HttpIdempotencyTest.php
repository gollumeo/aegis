<?php

declare(strict_types=1);

use Gollumeo\Aegis\Support\AegisConfig;
use Gollumeo\Aegis\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

describe('Feature: Http Idempotency', function (): void {
    it('musts throw a 428 if header is missing', function (): void {
        /** @var TestCase $this */
        $response = $this->post('/payments');
        expect($response->status())->toBe(Response::HTTP_PRECONDITION_REQUIRED);
    });

    it('musts send a 201 if the request passes', function (): void {
        /** @var TestCase $this */
        $response = $this->post('/payments', [], [
            AegisConfig::headerName() => $this::VALID_KEY,
        ]);

        expect($response->status())->toBe(Response::HTTP_CREATED);
    });
});

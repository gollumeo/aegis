<?php

declare(strict_types=1);

use Gollumeo\Aegis\Tests\TestCase;

describe('Feature: Http Idempotency', function (): void {
    it('musts throw a 428 if header is missing', function (): void {
        /** @var TestCase $this */
        $response = $this->post('/payments');
        expect($response->status())->toBe(428);
    });
});

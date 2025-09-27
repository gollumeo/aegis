<?php

declare(strict_types=1);

use Gollumeo\Aegis\Domain\Exceptions\MissingIdempotencyHeader;
use Gollumeo\Aegis\Domain\Policies\Composites\ComposeInsurances;
use Gollumeo\Aegis\Domain\Policies\EnsureIdempotencyHeaders;
use Gollumeo\Aegis\Support\AegisConfig;

describe('Unit: Compose Insurances', function (): void {
    it('fails if any policy fails', function (): void {
        $composition = new ComposeInsurances([
            new EnsureIdempotencyHeaders(),
        ]);

        $request = Request::create('/payments', 'POST');

        expect(
            /**
             * @throws MissingIdempotencyHeader
             */
            fn () => $composition->assert($request)
        )->toThrow(MissingIdempotencyHeader::class);
    });

    it('succeeds if all policies pass', function (): void {
        $composition = new ComposeInsurances([
            new EnsureIdempotencyHeaders(),
        ]);
        $request = Request::create('/payments', 'POST');
        $request->headers->set(AegisConfig::headerName(), '45851234567891012');

        expect(
            fn () => $composition->assert($request)
        )->not->toThrow(MissingIdempotencyHeader::class);
    });
});

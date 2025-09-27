<?php

declare(strict_types=1);

use Gollumeo\Aegis\Domain\Exceptions\MissingIdempotencyHeader;
use Gollumeo\Aegis\Domain\Policies\Composites\ComposeInsurances;
use Gollumeo\Aegis\Domain\Policies\EnsureIdempotencyHeaders;

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
});

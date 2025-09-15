<?php

declare(strict_types=1);

use Gollumeo\Aegis\Domain\Exceptions\MissingIdempotencyHeader;
use Gollumeo\Aegis\Domain\Policies\EnsureIdempotencyHeaders;
use Illuminate\Http\Request;

describe('Unit: Idempotency Policy', function (): void {
    it('fails if the Idempotency-Key header is missing', function (): void {
        $insurance = new EnsureIdempotencyHeaders();

        $request = Request::create('/payments', 'POST');

        expect(
            /**
             * @throws MissingIdempotencyHeader
             */
            fn () => $insurance->assert($request))->toThrow(MissingIdempotencyHeader::class);
    });

    it('fails if the Idempotency-Key header is invalid', function (): void {
        $insurance = new EnsureIdempotencyHeaders();
        $request = Request::create('/payments', 'POST');

        /** @var string $aegisHeaderName */
        $aegisHeaderName = config('aegis.header_name');

        $request->headers->set($aegisHeaderName, '');
        expect(
            /**
             * @throws MissingIdempotencyHeader
             */ fn () => $insurance->assert($request)
        )->toThrow(MissingIdempotencyHeader::class);
    });
});

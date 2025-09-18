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
             */
            fn () => $insurance->assert($request)
        )->toThrow(MissingIdempotencyHeader::class);

        $request->headers->set($aegisHeaderName, '123');

        expect(
            /**
             * @throws MissingIdempotencyHeader
             */
            fn () => $insurance->assert($request)
        )->not->toThrow(MissingIdempotencyHeader::class);
    });

    it('fails if the Idempotency-Key header length is too short', function (): void {
        $insurance = new EnsureIdempotencyKeyLength();

        $request = Request::create('/payments', 'POST');

        $aegisHeaderName = config('aegis.header_name');

        $request->headers->set($aegisHeaderName, '123');

        expect(
            fn () => $insurance->assert($request)
        )->toThrow(InvalidIdempotencyKeyLength::class);
    });
});

<?php

declare(strict_types=1);

use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyCharset;
use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyKeyLength;
use Gollumeo\Aegis\Domain\Exceptions\MissingIdempotencyHeader;
use Gollumeo\Aegis\Domain\Policies\EnsureIdempotencyCharset;
use Gollumeo\Aegis\Domain\Policies\EnsureIdempotencyHeaders;
use Gollumeo\Aegis\Domain\Policies\EnsureIdempotencyKeyLength;
use Illuminate\Http\Request;
use Random\RandomException;

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

    });

    it('succeeds if the Idempotency-Key header is correct', function (): void {
        $insurance = new EnsureIdempotencyHeaders();
        $request = Request::create('/payments', 'POST');
        /** @var string $aegisHeaderName */
        $aegisHeaderName = config('aegis.header_name');
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
        /** @var string $aegisHeaderName */
        $aegisHeaderName = config('aegis.header_name');
        $request->headers->set($aegisHeaderName, '123');

        expect(
            /**
             * @throws InvalidIdempotencyKeyLength
             */
            fn () => $insurance->assert($request)
        )->toThrow(InvalidIdempotencyKeyLength::class);
    });

    it('fails if the Idempotency-Key length is too long',
        /**
         * @throws RandomException|InvalidIdempotencyKeyLength
         */
        function (): void {
            $insurance = new EnsureIdempotencyKeyLength();
            $request = Request::create('/payments', 'POST');
            /** @var string $aegisHeaderName */
            $aegisHeaderName = config('aegis.header_name');

            /** @var int<1, max> $maxKeyLength */
            $maxKeyLength = config('aegis.key.max');
            $randomLongKey = bin2hex(random_bytes(($maxKeyLength))); // `bin2hex` doubles the string anyway (1 byte === 2 hex chars)

            $request->headers->set($aegisHeaderName, $randomLongKey);

            expect(
                fn () => $insurance->assert($request)
            )->toThrow(InvalidIdempotencyKeyLength::class);
        });

    it('succeeds if the Idempotency-Key length is correct', function (): void {
        $insurance = new EnsureIdempotencyKeyLength();
        $request = Request::create('/payments', 'POST');
        /** @var string $aegisHeaderName */
        $aegisHeaderName = config('aegis.header_name');
        $request->headers->set($aegisHeaderName, '123456-789-azerzerzd');

        expect(
            /**
             * @throws InvalidIdempotencyKeyLength
             */
            fn () => $insurance->assert($request)
        )->not->toThrow(InvalidIdempotencyKeyLength::class);
    });

    it('fails if the Idempotency-Key charset is invalid', function (): void {
        $insurance = new EnsureIdempotencyCharset();
        $request = Request::create('/payments', 'POST');
        /** @var string $aegisHeaderName */
        $aegisHeaderName = config('aegis.header_name');
        $request->headers->set($aegisHeaderName, '!!!');

        expect(
            /**
             * @throws InvalidIdempotencyCharset
             */
            fn () => $insurance->assert($request)
        )->toThrow(InvalidIdempotencyCharset::class);
    });
});

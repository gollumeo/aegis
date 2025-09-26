<?php

declare(strict_types=1);

use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyCharset;
use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyKeyLength;
use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyKeyPrefix;
use Gollumeo\Aegis\Domain\Exceptions\MissingIdempotencyHeader;
use Gollumeo\Aegis\Domain\Policies\EnsureIdempotencyCharset;
use Gollumeo\Aegis\Domain\Policies\EnsureIdempotencyHeaders;
use Gollumeo\Aegis\Domain\Policies\EnsureIdempotencyKeyLength;
use Gollumeo\Aegis\Domain\Policies\EnsureIdempotencyKeyPrefix;
use Gollumeo\Aegis\Support\AegisConfig;
use Gollumeo\Aegis\Support\ConfigKeys;
use Illuminate\Http\Request;

describe('Unit: Idempotency Policy', function (): void {
    it('fails if the Idempotency-Key header is missing', function (): void {
        $insurance = new EnsureIdempotencyHeaders();
        $request = Request::create('/payments', 'POST');

        expect(
            /**
             * @throws MissingIdempotencyHeader
             */
            fn () => $insurance->assert($request)
        )->toThrow(MissingIdempotencyHeader::class);
    });

    it('fails if the Idempotency-Key header is invalid', function (): void {
        $insurance = new EnsureIdempotencyHeaders();
        $request = Request::create('/payments', 'POST');
        $aegisHeaderName = AegisConfig::headerName();
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
        $aegisHeaderName = AegisConfig::headerName();
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
        $aegisHeaderName = AegisConfig::headerName();
        $request->headers->set($aegisHeaderName, '123');

        expect(
            /**
             * @throws InvalidIdempotencyKeyLength
             */
            fn () => $insurance->assert($request)
        )->toThrow(InvalidIdempotencyKeyLength::class);
    });

    it('fails if the Idempotency-Key length is too long', function (): void {
        $insurance = new EnsureIdempotencyKeyLength();
        $aegisHeaderName = AegisConfig::headerName();
        $maxKeyLength = AegisConfig::keyMax();
        $randomLongKey = str_repeat('a', $maxKeyLength + 1);
        $request = Request::create('/payments', 'POST');
        $request->headers->set($aegisHeaderName, $randomLongKey);

        expect(
            /**
             * @throws InvalidIdempotencyKeyLength
             */
            fn () => $insurance->assert($request)
        )->toThrow(InvalidIdempotencyKeyLength::class);
    });

    it('succeeds if the Idempotency-Key length is correct', function (): void {
        $insurance = new EnsureIdempotencyKeyLength();
        $aegisHeaderName = AegisConfig::headerName();
        $request = Request::create('/payments', 'POST');
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
        $aegisHeaderName = AegisConfig::headerName();
        $request = Request::create('/payments', 'POST');
        $request->headers->set($aegisHeaderName, '!!!');

        expect(
            /**
             * @throws InvalidIdempotencyCharset
             */
            fn () => $insurance->assert($request)
        )->toThrow(InvalidIdempotencyCharset::class);

        $request->headers->set($aegisHeaderName, 'foo_é-bar');
        expect(
            /**
             * @throws InvalidIdempotencyCharset
             */ fn () => $insurance->assert($request)
        )->toThrow(InvalidIdempotencyCharset::class);
    });

    it('succeeds if the Idempotency-Key charset is correct', function (): void {
        $insurance = new EnsureIdempotencyCharset();
        $aegisHeaderName = AegisConfig::headerName();
        $request = Request::create('/payments', 'POST');
        $request->headers->set($aegisHeaderName, '123456-azerzerzd');

        expect(
            /**
             * @throws InvalidIdempotencyCharset
             */
            fn () => $insurance->assert($request)
        )->not->toThrow(InvalidIdempotencyCharset::class);
    });

    it('fails if Idempotency-Key prefix is invalid when it is required', function (): void {
        $insurance = new EnsureIdempotencyKeyPrefix();
        $aegisHeaderName = AegisConfig::headerName();
        $request = Request::create('/payments', 'POST');
        $request->headers->set($aegisHeaderName, 'Other-abc');

        expect(
            /**
             * @throws InvalidIdempotencyKeyPrefix
             */
            fn () => $insurance->assert($request)
        )->toThrow(InvalidIdempotencyKeyPrefix::class);

        $request->headers->set($aegisHeaderName, '');

        expect(
            /**
             * @throws InvalidIdempotencyKeyPrefix
             */
            fn () => $insurance->assert($request)
        )->toThrow(InvalidIdempotencyKeyPrefix::class);
    });
    it('succeeds if Idempotency-Key prefix is valid when it is required', function (): void {
        $insurance = new EnsureIdempotencyKeyPrefix();
        $aegisHeaderName = AegisConfig::headerName();
        $request = Request::create('/payments', 'POST');
        // TestCase config sets required_prefix to 'Prefix'
        $request->headers->set($aegisHeaderName, 'Prefix-abc');

        expect(
            /**
             * @throws InvalidIdempotencyKeyPrefix
             */
            fn () => $insurance->assert($request)
        )->not->toThrow(InvalidIdempotencyKeyPrefix::class);
    });

    it('does nothing when prefix requirement is disabled', function (): void {
        config([ConfigKeys::KeyPrefix->value => null]);
        $insurance = new EnsureIdempotencyKeyPrefix();
        $request = Request::create('/payments', 'POST');
        $aegisHeaderName = AegisConfig::headerName();
        // Using a key not starting with the previous prefix to ensure it would fail if enabled
        $request->headers->set($aegisHeaderName, 'Other-abc');

        expect(
            /**
             * @throws InvalidIdempotencyKeyPrefix
             */
            fn () => $insurance->assert($request)
        )->not->toThrow(InvalidIdempotencyKeyPrefix::class);
    });
});

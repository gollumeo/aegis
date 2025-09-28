<?php

declare(strict_types=1);

use Gollumeo\Aegis\Application\Contracts\Insurance;
use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyCharset;
use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyKeyLength;
use Gollumeo\Aegis\Domain\Exceptions\InvalidIdempotencyKeyPrefix;
use Gollumeo\Aegis\Domain\Exceptions\MissingIdempotencyHeader;
use Gollumeo\Aegis\Domain\Policies\Composites\ComposeInsurances;
use Gollumeo\Aegis\Domain\Policies\EnsureIdempotencyCharset;
use Gollumeo\Aegis\Domain\Policies\EnsureIdempotencyHeaders;
use Gollumeo\Aegis\Domain\Policies\EnsureIdempotencyKeyLength;
use Gollumeo\Aegis\Domain\Policies\EnsureIdempotencyKeyPrefix;
use Gollumeo\Aegis\Support\AegisConfig;
use Gollumeo\Aegis\Tests\TestCase;

describe('Unit: Compose Insurances', function (): void {
    dataset('insurances', [
        [new EnsureIdempotencyHeaders(), '', MissingIdempotencyHeader::class],
        [new EnsureIdempotencyKeyLength(), '1234', InvalidIdempotencyKeyLength::class],
        [new EnsureIdempotencyCharset(), 'foo!bar', InvalidIdempotencyCharset::class],
        [new EnsureIdempotencyKeyPrefix(), 'Other', InvalidIdempotencyKeyPrefix::class],
    ]);

    dataset('invalid-keys', [
        '',
        '1234',
        '123456789!!!!!!!!!!!!!!',
        'Other-Prefix-Good-Length-Charset',
    ]);

    it('fails if any policy fails', function (Insurance $insurance, string $header, string $exception): void {
        $composition = new ComposeInsurances([
            $insurance,
        ]);

        $request = Request::create('/payments', 'POST');
        $request->headers->set(AegisConfig::headerName(), $header);

        expect(
            fn () => $composition->assert($request)
        )->toThrow($exception);
    })->with('insurances');

    it('succeeds if all policies pass', function (): void {
        $composition = new ComposeInsurances([
            new EnsureIdempotencyHeaders(),
        ]);
        $request = Request::create('/payments', 'POST');
        /** @var TestCase $this */
        $request->headers->set(AegisConfig::headerName(), $this::VALID_PREFIX);

        expect(
            fn () => $composition->assert($request)
        )->not->toThrow(MissingIdempotencyHeader::class);
    });

    it('fails and stops on first failure', function (string $invalidKey): void {
        $composition = new ComposeInsurances([
            new EnsureIdempotencyHeaders(),
            new EnsureIdempotencyKeyLength(),
            new EnsureIdempotencyKeyPrefix(),
            new EnsureIdempotencyCharset(),
        ]);
        $request = Request::create('/payments', 'POST');
        $request->headers->set(AegisConfig::headerName(), $invalidKey);

        expect(
            fn () => $composition->assert($request)
        )->toThrow(Exception::class);
    })->with('invalid-keys');
});

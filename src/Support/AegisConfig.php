<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Support;

final readonly class AegisConfig
{
    public static function requireHeader(): bool
    {
        return (bool) config(ConfigKeys::RequireHeader->value);
    }

    public static function headerName(): string
    {
        /** @var string $headerName */
        $headerName = config(ConfigKeys::HeaderName->value);

        return $headerName;
    }

    /**
     * @return string[]
     */
    public static function methods(): array
    {
        /** @var string[] $methods */
        $methods = config(ConfigKeys::Methods->value);

        return $methods;
    }

    public static function ttlSeconds(): int
    {
        /** @var int $ttlSeconds */
        $ttlSeconds = config(ConfigKeys::TtlSeconds->value);

        return $ttlSeconds;
    }

    public static function keyMin(): int
    {
        /** @var int $keyMin */
        $keyMin = config(ConfigKeys::KeyMin->value);

        return $keyMin;
    }

    public static function keyMax(): int
    {
        /** @var int $keyMax */
        $keyMax = config(ConfigKeys::KeyMax->value);

        return $keyMax;
    }

    public static function keyCharset(): string
    {
        /** @var string $charset */
        $charset = config(ConfigKeys::KeyCharset->value);

        return $charset;
    }

    public static function keyPrefix(): ?string
    {
        /** @var string|null $prefix */
        $prefix = config(ConfigKeys::KeyPrefix->value);

        return $prefix;
    }
}

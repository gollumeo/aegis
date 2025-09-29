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

    /**
     * Get the configured time-to-live in seconds.
     *
     * Reads the value at the configuration key represented by ConfigKeys::TtlSeconds
     * and returns it cast to an int.
     *
     * @return int The TTL in seconds.
     */
    public static function ttlSeconds(): int
    {
        /** @var int $ttlSeconds */
        $ttlSeconds = config(ConfigKeys::TtlSeconds->value);

        return $ttlSeconds;
    }

    /**
     * Return the replay headers whitelist from configuration.
     *
     * Reads the ConfigKeys::ReplayWhitelist configuration value and returns it as
     * an array of header names (strings) that should be allowed for replay checks.
     *
     * @return string[] Array of header names from the `replay_whitelist` config entry.
     */
    public static function replayHeadersWhitelist(): array
    {
        /** @var string[] $replayWhiteList */
        $replayWhiteList = config(ConfigKeys::ReplayWhitelist->value);

        return $replayWhiteList;
    }

    /**
     * Get the configured minimum key length.
     *
     * Reads the value at ConfigKeys::KeyMin and returns it as an int.
     *
     * @return int The minimum allowed key length from configuration.
     */
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

    /**
     * @return string[]
     */
    public static function policies(): array
    {
        /** @var string[] $policies */
        $policies = config(ConfigKeys::Policies->value);

        return $policies;
    }
}

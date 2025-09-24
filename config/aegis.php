<?php

declare(strict_types=1);

use Gollumeo\Aegis\Support\ConfigKeys;

return [
    ConfigKeys::RequireHeader->value => true,
    ConfigKeys::HeaderName->value => 'Idempotency-Key',
    ConfigKeys::Methods->value => ['POST', 'PUT', 'DELETE', 'PATCH'],
    ConfigKeys::TtlSeconds->value => 60,
    ConfigKeys::ReplayWhitelist->value => ['Content-Type', 'Cache-Control', 'ETag', 'Location', 'Content-Location', 'Vary'],
    'key' => [
        ConfigKeys::KeyMin->value => 16,
        ConfigKeys::KeyMax->value => 120,
        ConfigKeys::KeyCharset->value => 'A-Za-z0-9_-',
        ConfigKeys::KeyPrefix->value => null,
    ],
];

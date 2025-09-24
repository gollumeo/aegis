<?php

declare(strict_types=1);

use Gollumeo\Aegis\Support\ConfigKeys;

return [
// config/aegis.php

return [
    // … other config entries …

    // previously:
    // ConfigKeys::RequireHeader->value    => true,
    // ConfigKeys::HeaderName->value       => 'Idempotency-Key',
    // ConfigKeys::Methods->value          => ['POST','PUT','DELETE','PATCH'],
    // ConfigKeys::TtlSeconds->value       => 60,
    // ConfigKeys::ReplayWhitelist->value  => [/* … */],

    // now:
    'require_header'             => true,
    'header_name'                => 'Idempotency-Key',
    'methods'                    => ['POST','PUT','DELETE','PATCH'],
    'ttl_seconds'                => 60,
    'replay_headers_whitelist'   => ['Content-Type','Cache-Control','ETag','Location','Content-Location','Vary'],

    // … other config entries …

    // previously:
    // ConfigKeys::KeyMin->value     => 16,
    // ConfigKeys::KeyMax->value     => 120,
    // ConfigKeys::KeyCharset->value => 'A-Za-z0-9_-',
    // ConfigKeys::KeyPrefix->value  => null,

    // now:
    'key' => [
        'min'              => 16,
        'max'              => 120,
        'charset'          => 'A-Za-z0-9_-',
        'required_prefix'  => null,
    ],
];
    'key' => [
        ConfigKeys::KeyMin->value => 16,
        ConfigKeys::KeyMax->value => 120,
        ConfigKeys::KeyCharset->value => 'A-Za-z0-9_-',
        ConfigKeys::KeyPrefix->value => null,
    ],
];

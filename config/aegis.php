<?php

declare(strict_types=1);

return [
    'require_header' => true,
    'header_name' => 'Idempotency-Key',
    'methods' => ['POST', 'PUT', 'DELETE', 'PATCH'],
    'ttl_seconds' => 60,
    'replay_headers_whitelist' => ['Content-Type', 'Cache-Control', 'ETag', 'Location', 'Content-Location', 'Vary'],
    'key' => [
        'min' => 16,
        'max' => 120,
        'charset' => 'A-Za-z0-9_-',
        'required_prefix' => null,
        'required_prefix_value' => 'Idempotency',
    ],
];

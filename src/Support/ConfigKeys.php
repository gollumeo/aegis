<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Support;

enum ConfigKeys: string
{
    case RequireHeader = 'aegis.require_header';
    case HeaderName = 'aegis.header_name';
    case Methods = 'aegis.methods';
    case TtlSeconds = 'aegis.ttl_seconds';
    case ReplayWhitelist = 'aegis.replay_headers_whitelist';

    case KeyMin = 'aegis.key.min';
    case KeyMax = 'aegis.key.max';
    case KeyCharset = 'aegis.key.charset';
    case KeyPrefix = 'aegis.key.required_prefix';

    case Policies = 'aegis.policies';
}

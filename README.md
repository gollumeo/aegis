# Aegis — HTTP Idempotency for Laravel

Aegis is an opinionated, configuration‑driven HTTP Idempotency middleware for Laravel. It enforces the presence and validity of an Idempotency‑Key header on write‑type requests and lays the groundwork for safe 2xx response replay and Location‑header aware flows.

Current status: early preview (v0.1). Header enforcement and domain validation policies are available; full replay storage and prefix enforcement are on the roadmap.


## Why idempotency?
Idempotency allows clients to safely retry non‑GET requests without accidentally performing the action multiple times. Clients attach a stable Idempotency‑Key per unique operation; the server enforces constraints and can replay prior successful responses.


## Features
- Enforced header presence for configurable HTTP methods with clear 428 (Precondition Required) responses.
- Domain policies you can compose:
  - EnsureIdempotencyHeaders (presence, non‑empty)
  - EnsureIdempotencyKeyLength (min/max)
  - EnsureIdempotencyCharset (allowed characters)
- Configuration via config/aegis.php (publishable).
- Auto‑discovered service provider and a route middleware alias aegis.
- Tested with Pest + Orchestra Testbench; static analysis with PHPStan; code style with Laravel Pint.

Planned for upcoming releases:
- Response replay (2xx replay) with TTL and response header whitelist.
- Optional Idempotency‑Key required prefix enforcement.


## Requirements
- PHP: ^8.4 with ext‑json
- Laravel components: illuminate/support ^12.0, illuminate/http ^12.0


## Installation
1) Require the package

```bash
composer require gollumeo/aegis
```

2) (Optional) Publish the config file

```bash
php artisan vendor:publish --tag=aegis-config
```

The service provider is auto‑discovered and also publishes a config/aegis.php when you run the publish command.


## Quick start
Apply the middleware alias aegis to your write routes.

```php
use Illuminate\Support\Facades\Route;

Route::post('/payments', [PaymentController::class, 'store'])
    ->middleware('aegis');
```

Client requests must include an Idempotency‑Key header (name is configurable). Example curl:

```bash
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Idempotency-Key: 9c2e8c24-8c1a-4b8f-a7d0-1ccf9c13b2a3" \
  -d '{"amount": 1200, "currency": "USD"}' \
  https://api.example.test/payments
```

When require_header is true and a guarded method is called without the header, Aegis returns 428 with a helpful payload and hint headers:

- Status: 428 Precondition Required
- Body (JSON):

```json
{
  "error": "idempotency_header_required",
  "message": "This endpoint requires 'Idempotency-Key'.",
  "how_to_fix": "Generate a stable key and resend the same request with it."
}
```

- Response headers:
  - X-Idempotency-Required: true
  - X-Idempotency-Header: Idempotency-Key
  - X-Idempotency-Methods: POST, PUT, DELETE, PATCH

Note: As of v0.1 the middleware focuses on header enforcement. The domain policies listed below are available and can be composed; broader replay functionality is under active development.


## Configuration
Publish or inspect config/aegis.php. Defaults shown here:

```php
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
        'required_prefix' => null,              // reserved: feature under development
        'required_prefix_value' => 'Idempotency'// reserved: feature under development
    ],
];
```

Notes:
- key.charset is a PCRE character class fragment. The key must fully match it: ^[charset]+$.
- ttl_seconds and replay_headers_whitelist are used by the forthcoming replay store.
- key.required_prefix and key.required_prefix_value are reserved for an upcoming policy; do not rely on them yet.


## Domain policies and exceptions
Aegis favors small, focused “policy” classes that assert a specific concern. You can use these directly in your own pipeline, or rely on the middleware once composition is finalized.

- EnsureIdempotencyHeaders → throws MissingIdempotencyHeader
- EnsureIdempotencyKeyLength → throws InvalidIdempotencyKeyLength
- EnsureIdempotencyCharset → throws InvalidIdempotencyCharset
- EnsureIdempotencyKeyPrefix → planned (will throw InvalidIdempotencyKeyPrefix)

Each policy implements the contract:

```php
interface Insurance {
    public function assert(Illuminate\Http\Request $request): void;
}
```


## Testing
- Runner: Pest v3
- Framework: Orchestra Testbench v10

Run the entire suite (note: a known red test exists for key prefix, which is not implemented yet):

```bash
composer test
# or
php vendor\bin\pest --parallel
```

Recommended during development while the red test exists:

```bash
# Run a specific file
php vendor\bin\pest tests\Unit\EnsureIdempotencyKeyLengthTest.php

# Or run with name filter
php vendor\bin\pest --filter="charset is correct"
```

Static analysis and style:

```bash
# Code style (Laravel Pint)
php vendor\bin\pint --parallel

# Static analysis (PHPStan + Larastan)
php vendor\bin\phpstan analyze .

# CI helper
composer ci
```

Windows note: use backslashes in vendor paths as shown above.


## Middleware alias and service provider
- Alias: aegis → Gollumeo\Aegis\Infrastructure\Http\Middleware\Idempotency
- Provider: Gollumeo\Aegis\AegisServiceProvider (auto‑discovered)
- Config publish tag: aegis-config


## Roadmap
- Idempotency replay store with TTL.
- Location‑safe replay for 2xx responses.
- Configurable key prefix enforcement.
- More granular configuration and telemetry hooks.


## Versioning and stability
- Current package version: 0.1
- Minimum supported PHP: 8.4
- Targeted Laravel components: v12

Breaking changes may occur before 1.0. Pin your dependency accordingly.


## Contributing
- Fork and create a feature branch.
- Please run: composer ci (tests, Pint, PHPStan) before opening a PR.
- Keep changes small and focused; prefer adding/adjusting domain policies and wiring via the middleware.


## License
MIT License. See the LICENSE file if present; otherwise the license is declared in composer.json.
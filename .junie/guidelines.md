Aegis (Laravel HTTP Idempotency middleware) — Project Guidelines

Audience: experienced PHP/Laravel package developers working on this repository.

1) Build and Configuration
- Runtime: PHP 8.4 with ext-json. The package targets Laravel 12 components (illuminate/*) and is tested using Orchestra Testbench (no full Laravel app required).
- Autoloading: PSR-4
  - Gollumeo\Aegis\ => src/
  - Tests namespace => tests/
- Service Provider: Gollumeo\Aegis\AegisServiceProvider is auto-discovered (composer extra.laravel.providers) and is also registered explicitly in tests via Testbench.
- Config: config/aegis.php controls idempotency behavior
  - require_header: bool (default true)
  - header_name: string (default Idempotency-Key)
  - methods: array of HTTP verbs guarded by the middleware
  - ttl_seconds: replay TTL
  - replay_headers_whitelist: headers allowed to be replayed
  - key.min, key.max: idempotency key length constraints
  - key.charset: allowed characters (PCRE char class fragment)
  - key.required_prefix / key.required_prefix_value: optional prefix enforcement knobs (feature under development; see Testing notes)
- Middleware: src/Infrastructure/Http/Middleware/Idempotency wires the domain “policies” that validate the request before reaching controllers.
- Static analysis and style:
  - Code style: Laravel Pint (pint.json). Run: php vendor\bin\pint --parallel
  - Static analysis: PHPStan + Larastan (phpstan.neon). Run: php vendor\bin\phpstan analyze .
- CI helper script: composer ci runs tests, Pint, then PHPStan.

2) Testing
- Test runner: Pest v3 (composer test => php vendor\bin\pest --parallel)
- Test framework: Orchestra Testbench v10 boots a lightweight Laravel app. See tests/TestCase.php for environment configuration and routing.
- Where to add tests: place Pest tests under tests/Unit or tests/Feature. Import Laravel Request as needed (Illuminate\Http\Request). Use the provided TestCase when writing feature tests that need routing/middleware.
- Known red test (as of 2025‑09‑22): tests/Unit/IdempotencyPolicyTest.php includes a spec referring to EnsureIdempotencyKeyPrefix and InvalidIdempotencyKeyPrefix which are not yet implemented. Running the full suite will fail on that case.
  - Until prefix enforcement is implemented, prefer targeted runs (per‑file or with a filter) when developing new code, or temporarily skip that spec locally.

Running tests
- Run the entire suite (will currently fail due to the prefix spec):
  - composer test
  - or php vendor\bin\pest --parallel
- Run a specific directory or file (recommended while the red test exists):
  - php vendor\bin\pest tests\Unit\EnsureIdempotencyKeyLengthTest.php
  - php vendor\bin\pest tests\Unit\SomeFile.php
- Run with a name filter:
  - php vendor\bin\pest --filter="charset is correct"

Example: adding and running a simple test (verified)
- We validated the flow with a throwaway test to avoid the known red case.
- Example test content (Pest):
  - File: tests/Unit/SanityTest.php
    ---------------------------------
    <?php
    declare(strict_types=1);
    it('sanity: math and environment are OK', function (): void {
        expect(1 + 1)->toBe(2);
    });
    ---------------------------------
- Run it directly:
  - php vendor\bin\pest tests\Unit\SanityTest.php
- Expected output (observed):
  - PASS  Tests\Unit\SanityTest
  - Tests: 1 passed (1 assertions)
- Clean up: remove the throwaway file once done (keep repository clean). Note: for this documentation task we created and successfully ran such a test locally, then deleted it.

Guidelines for adding new tests in this project
- Unit tests for domain policies
  - Use Illuminate\Http\Request::create() to build requests and set the configured idempotency header (config('aegis.header_name')).
  - Validate domain exceptions: MissingIdempotencyHeader, InvalidIdempotencyKeyLength, InvalidIdempotencyCharset, etc., using Pest’s toThrow expectations.
- Feature tests for middleware
  - Extend the shared TestCase in tests/TestCase.php. Routes are defined there for /payments with the aegis middleware applied. This ensures the middleware and configuration are booted via Testbench.
- Performance: prefer shorter keys and fast paths in tests; the suite is configured for parallel execution.

3) Additional development notes
- Policies live in src/Domain/Policies and each one asserts a specific concern (headers present, key length, charset, etc.). Favor small, focused policy classes and pure exception types in src/Domain/Exceptions.
- Configuration‑driven behavior: pull constraints from config('aegis.*') so tests can override these values via Testbench in tests/TestCase.php (see defineEnvironment()).
- Middleware flow: the Idempotency middleware composes the policies and is registered in the service provider. When extending behavior (e.g., required key prefix), add a new Policy class and wire it into the middleware; provide targeted unit tests and update tests/TestCase.php config overrides accordingly.
- Static analysis: Keep PHPStan level as configured in phpstan.neon; prefer typed properties, strict_types=1, and precise generics/psalm‑like annotations where needed.
- Coding style: Run Pint before committing. Avoid manual formatting changes that fight Pint.

Windows notes
- Paths shown here use backslashes. All example commands were executed with PowerShell.
- Prefix php vendor\bin\tool for binaries rather than relying on vendor\bin shims.

Release hygiene
- Update composer.json version as appropriate.
- Ensure CI script (composer ci) passes locally (tests, Pint, PHPStan) before tagging.

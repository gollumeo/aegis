<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Domain\Policies\Composites;

use Gollumeo\Aegis\Application\Contracts\Insurance;
use Gollumeo\Aegis\Domain\Exceptions\MissingIdempotencyHeader;
use Illuminate\Http\Request;

final class ComposeInsurances implements Insurance
{
    /**
     * @param  Insurance[]  $insurances
     */
    public function __construct(private array $insurances) {}

    /**
     * {@inheritDoc}
     *
     * @throws MissingIdempotencyHeader
     */
    public function assert(Request $request): void
    {
        foreach ($this->insurances as $insurance) {
            dump($insurance);
        }
        throw new MissingIdempotencyHeader();
    }
}

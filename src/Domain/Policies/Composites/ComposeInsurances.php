<?php

declare(strict_types=1);

namespace Gollumeo\Aegis\Domain\Policies\Composites;

use Gollumeo\Aegis\Application\Contracts\Insurance;
use Illuminate\Http\Request;

final class ComposeInsurances implements Insurance
{
    /**
     * @param  Insurance[]  $insurances
     */
    public function __construct(private readonly array $insurances) {}

    /**
     * {@inheritDoc}
     */
    public function assert(Request $request): void
    {
        foreach ($this->insurances as $insurance) {
            $insurance->assert($request);
        }
    }
}

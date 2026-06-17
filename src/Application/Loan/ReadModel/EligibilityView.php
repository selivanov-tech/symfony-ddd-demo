<?php

declare(strict_types=1);

namespace App\Application\Loan\ReadModel;

final class EligibilityView
{
    public function __construct(
        public readonly bool $eligible,
        public readonly ?string $reason = null,
    ) {
    }
}

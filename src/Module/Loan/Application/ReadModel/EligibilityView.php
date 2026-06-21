<?php

declare(strict_types=1);

namespace App\Module\Loan\Application\ReadModel;

final class EligibilityView
{
    public function __construct(
        public readonly bool $eligible,
        public readonly ?string $reason = null,
    ) {
    }
}

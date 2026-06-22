<?php

declare(strict_types=1);

namespace App\Module\Loan\Application\ReadModel;

use OpenApi\Attributes as OA;

final class EligibilityView
{
    public function __construct(
        #[OA\Property(example: true)]
        public readonly bool $eligible,
        #[OA\Property(example: 'Credit score too low', nullable: true)]
        public readonly ?string $reason = null,
    ) {
    }
}

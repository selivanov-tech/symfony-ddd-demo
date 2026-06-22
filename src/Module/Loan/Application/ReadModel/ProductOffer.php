<?php

declare(strict_types=1);

namespace App\Module\Loan\Application\ReadModel;

use App\Module\Loan\Domain\ValueObject\ProductTerms;
use App\Shared\Domain\Identity\UuidInterface;

final class ProductOffer
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly float $amount,
        public readonly ProductTerms $terms,
    ) {
    }
}

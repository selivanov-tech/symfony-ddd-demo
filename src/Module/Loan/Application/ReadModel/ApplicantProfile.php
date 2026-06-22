<?php

declare(strict_types=1);

namespace App\Module\Loan\Application\ReadModel;

use App\Module\Loan\Domain\ValueObject\CreditProfile;
use App\Shared\Domain\Identity\UuidInterface;

final class ApplicantProfile
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly CreditProfile $credit,
    ) {
    }
}

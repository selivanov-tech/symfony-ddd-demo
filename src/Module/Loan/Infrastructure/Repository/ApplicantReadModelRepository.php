<?php

declare(strict_types=1);

namespace App\Module\Loan\Infrastructure\Repository;

use App\Module\Customer\Domain\Repository\CustomerRepositoryInterface;
use App\Module\Loan\Application\ReadModel\ApplicantProfile;
use App\Module\Loan\Application\Repository\ApplicantReadModelRepositoryInterface;
use App\Module\Loan\Domain\ValueObject\CreditProfile;

final class ApplicantReadModelRepository implements ApplicantReadModelRepositoryInterface
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
    ) {
    }

    public function findById(string $id): ?ApplicantProfile
    {
        $customer = $this->customers->findById($id);
        if ($customer === null) {
            return null;
        }

        return new ApplicantProfile(
            $customer->getId(),
            $customer->getPresentedName(),
            (string) $customer->getEmail(),
            (string) $customer->getPhone(),
            new CreditProfile(
                $customer->getFicoScore()->value,
                $customer->getMonthlyIncome(),
                $customer->getAge(),
                $customer->getAddress()->getState(),
            ),
        );
    }
}

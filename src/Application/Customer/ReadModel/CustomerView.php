<?php

declare(strict_types=1);

namespace App\Application\Customer\ReadModel;

use App\Domain\Customer\Entity\Customer;

final class CustomerView
{
    /**
     * @param array<string, mixed> $address
     */
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $fullName,
        public readonly string $birthday,
        public readonly int $ficoScore,
        public readonly array $address,
        public readonly int $monthlyIncome,
    ) {
    }

    public static function fromCustomer(Customer $customer): self
    {
        return new self(
            id: $customer->getId()->toString(),
            email: (string) $customer->getEmail(),
            phone: (string) $customer->getPhone(),
            firstName: $customer->getFirstName(),
            lastName: $customer->getLastName(),
            fullName: $customer->getPresentedName(),
            birthday: $customer->getBirthday()->format('Y-m-d'),
            ficoScore: $customer->getFicoScore()->value,
            address: $customer->getAddress()->toArray(),
            monthlyIncome: $customer->getMonthlyIncome(),
        );
    }
}

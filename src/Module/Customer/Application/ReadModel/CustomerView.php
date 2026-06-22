<?php

declare(strict_types=1);

namespace App\Module\Customer\Application\ReadModel;

use App\Module\Customer\Domain\Entity\Customer;
use OpenApi\Attributes as OA;

final class CustomerView
{
    /**
     * @param array<string, mixed> $address
     */
    public function __construct(
        #[OA\Property(example: '0193b3e9-7b1d-7c44-8a90-3f2e1a9c7d22')]
        public readonly string $id,
        #[OA\Property(example: 'jane.doe@example.com')]
        public readonly string $email,
        #[OA\Property(example: '5550000001')]
        public readonly string $phone,
        #[OA\Property(example: 'Jane')]
        public readonly string $firstName,
        #[OA\Property(example: 'Doe')]
        public readonly string $lastName,
        #[OA\Property(example: 'Jane Doe')]
        public readonly string $fullName,
        #[OA\Property(example: '1990-01-01')]
        public readonly string $birthday,
        #[OA\Property(example: 720)]
        public readonly int $ficoScore,
        #[OA\Property(example: ['street' => '1 Market St', 'city' => 'San Francisco', 'state' => 'CA', 'zip' => '94105'])]
        public readonly array $address,
        #[OA\Property(example: 6000)]
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

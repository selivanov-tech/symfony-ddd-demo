<?php

declare(strict_types=1);

namespace App\Application\Customer\Command\UpdateCustomer;

use App\Shared\Application\Bus\Command\CommandInterface;

final class UpdateCustomerCommand implements CommandInterface
{
    /**
     * @param array<string, mixed>|null $address
     */
    public function __construct(
        public readonly string $id,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $birthday,
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly ?string $ssn,
        public readonly ?int $ficoScore,
        public readonly ?array $address,
        public readonly ?int $monthlyIncome,
    ) {
    }
}

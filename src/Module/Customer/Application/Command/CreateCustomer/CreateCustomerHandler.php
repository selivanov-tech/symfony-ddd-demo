<?php

declare(strict_types=1);

namespace App\Module\Customer\Application\Command\CreateCustomer;

use App\Module\Customer\Domain\Entity\Customer;
use App\Module\Customer\Domain\Repository\CustomerRepositoryInterface;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateCustomerHandler
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
        private readonly UuidFactoryInterface $uuidFactory,
    ) {
    }

    public function __invoke(CreateCustomerCommand $command): string
    {
        $customer = Customer::create(
            $this->uuidFactory->uuid7(),
            $command->email,
            $command->phone,
            $command->ssn,
            $command->firstName,
            $command->lastName,
            new DateTimeImmutable($command->birthday),
            $command->ficoScore,
            $command->address,
            $command->monthlyIncome,
        );

        $this->customers->save($customer);

        return $customer->getId()->toString();
    }
}

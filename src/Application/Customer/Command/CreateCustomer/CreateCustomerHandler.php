<?php

declare(strict_types=1);

namespace App\Application\Customer\Command\CreateCustomer;

use App\Domain\Customer\Entity\Customer;
use App\Domain\Customer\Repository\CustomerRepositoryInterface;
use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateCustomerHandler
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
    ) {
    }

    public function __invoke(CreateCustomerCommand $command): string
    {
        $customer = (new Customer())
            ->setEmail($command->email)
            ->setPhone($command->phone)
            ->setSsn($command->ssn)
            ->setFirstName($command->firstName)
            ->setLastName($command->lastName)
            ->setBirthday(new DateTimeImmutable($command->birthday))
            ->setFicoScore($command->ficoScore)
            ->setAddress($command->address)
            ->setMonthlyIncome($command->monthlyIncome);

        $this->customers->save($customer);

        return $customer->getId();
    }
}

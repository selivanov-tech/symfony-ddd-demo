<?php

declare(strict_types=1);

namespace App\Application\Customer\Command\UpdateCustomer;

use App\Application\Exception\Customer\CustomerNotFoundException;
use App\Domain\Customer\Repository\CustomerRepositoryInterface;
use DateTimeImmutable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class UpdateCustomerHandler
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
    ) {
    }

    public function __invoke(UpdateCustomerCommand $command): void
    {
        $customer = $this->customers->findById($command->id) ?? throw new CustomerNotFoundException();

        if ($command->email !== null) {
            $customer->setEmail($command->email);
        }
        if ($command->phone !== null) {
            $customer->setPhone($command->phone);
        }
        if ($command->birthday !== null) {
            $customer->setBirthday(new DateTimeImmutable($command->birthday));
        }
        if ($command->firstName !== null) {
            $customer->setFirstName($command->firstName);
        }
        if ($command->lastName !== null) {
            $customer->setLastName($command->lastName);
        }
        if ($command->ssn !== null) {
            $customer->setSsn($command->ssn);
        }
        if ($command->ficoScore !== null) {
            $customer->setFicoScore($command->ficoScore);
        }
        if ($command->address !== null) {
            $customer->setAddress($command->address);
        }
        if ($command->monthlyIncome !== null) {
            $customer->setMonthlyIncome($command->monthlyIncome);
        }

        $this->customers->save($customer);
    }
}

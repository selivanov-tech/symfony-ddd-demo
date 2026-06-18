<?php

declare(strict_types=1);

namespace App\Module\Customer\Application\Command\UpdateCustomer;

use App\Module\Customer\Application\Exception\CustomerNotFoundException;
use App\Module\Customer\Domain\Repository\CustomerRepositoryInterface;
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

        if ($command->email !== null || $command->phone !== null || $command->address !== null) {
            $customer->changeContactDetails(
                $command->email ?? (string) $customer->getEmail(),
                $command->phone ?? (string) $customer->getPhone(),
                $command->address ?? $customer->getAddress()->toArray(),
            );
        }
        if ($command->firstName !== null || $command->lastName !== null) {
            $customer->rename(
                $command->firstName ?? $customer->getFirstName(),
                $command->lastName ?? $customer->getLastName(),
            );
        }
        if ($command->birthday !== null) {
            $customer->correctBirthday(new DateTimeImmutable($command->birthday));
        }
        if ($command->ssn !== null) {
            $customer->correctSsn($command->ssn);
        }
        if ($command->ficoScore !== null) {
            $customer->recordFicoScore($command->ficoScore);
        }
        if ($command->monthlyIncome !== null) {
            $customer->recordMonthlyIncome($command->monthlyIncome);
        }

        $this->customers->save($customer);
    }
}

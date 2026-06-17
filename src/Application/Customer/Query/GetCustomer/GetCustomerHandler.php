<?php

declare(strict_types=1);

namespace App\Application\Customer\Query\GetCustomer;

use App\Application\Customer\ReadModel\CustomerView;
use App\Application\Exception\Customer\CustomerNotFoundException;
use App\Domain\Customer\Repository\CustomerRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetCustomerHandler
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
    ) {
    }

    public function __invoke(GetCustomerQuery $query): CustomerView
    {
        $customer = $this->customers->findById($query->id) ?? throw new CustomerNotFoundException();

        return CustomerView::fromCustomer($customer);
    }
}

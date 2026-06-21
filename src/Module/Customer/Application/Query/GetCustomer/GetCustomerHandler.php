<?php

declare(strict_types=1);

namespace App\Module\Customer\Application\Query\GetCustomer;

use App\Module\Customer\Application\Exception\CustomerNotFoundException;
use App\Module\Customer\Application\ReadModel\CustomerView;
use App\Module\Customer\Domain\Repository\CustomerRepositoryInterface;
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

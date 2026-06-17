<?php

declare(strict_types=1);

namespace App\Application\Customer\Query\GetCustomer;

use App\Shared\Application\Bus\Query\QueryInterface;

final class GetCustomerQuery implements QueryInterface
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}
